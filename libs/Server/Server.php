<?php namespace Todaymade\Daux\Server;

use Symfony\Component\Console\Output\NullOutput;
use Todaymade\Daux\Daux;
use Todaymade\Daux\DauxHelper;
use Todaymade\Daux\Exception;
use Todaymade\Daux\Format\Base\LiveGenerator;
use Todaymade\Daux\Format\HTML\RawPage;

class Server
{
    private $daux;
    private $params;
    private $host;
    private $base_url;

    /**
     * Serve the documentation
     *
     * @throws Exception
     */
    public static function serve()
    {
        $daux = new Daux(Daux::LIVE_MODE);
        $daux->initializeConfiguration();

        $class = $daux->getProcessorClass();
        if (!empty($class)) {
            $daux->setProcessor(new $class($daux, new NullOutput(), 0));
        }

        // Set this critical configuration
        // for the tree generation
        $daux->getParams()['index_key'] = 'index';

        // Improve the tree with a processor
        $daux->generateTree();

        $server = new static($daux);

        try {
            $page = $server->handle();
        } catch (NotFoundException $e) {
            http_response_code(404);
            $page = new ErrorPage('An error occured', $e->getMessage(), $daux->getParams());
        }

        if ($page instanceof RawPage) {
            header('Content-type: ' . MimeType::get($page->getFile()));

            // Transfer file in 1024 byte chunks to save memory usage.
            if ($fd = fopen($page->getFile(), 'rb')) {
                while (!feof($fd)) {
                    echo fread($fd, 1024);
                }
                fclose($fd);
            }

            return;
        }

        header('Content-type: text/html; charset=utf-8');
        echo $page->getContent();
    }

    public function __construct(Daux $daux)
    {
        $this->daux = $daux;

        $this->host = $_SERVER['HTTP_HOST'];

        // The path has a special treatment on windows, revert the slashes
        $dir = dirname($_SERVER['PHP_SELF']);
        $this->base_url = $_SERVER['HTTP_HOST'] . (DIRECTORY_SEPARATOR == '\\' ? str_replace('\\', '/', $dir) : $dir);

        $t = strrpos($this->base_url, '/index.php');
        if ($t != false) {
            $this->base_url = substr($this->base_url, 0, $t);
        }
        if (substr($this->base_url, -1) !== '/') {
            $this->base_url .= '/';
        }
    }

    /**
     * @return \Todaymade\Daux\Config
     */
    public function getParams()
    {
        $params = $this->daux->getParams();

        $params['host'] = $this->host;

        DauxHelper::rebaseConfiguration($params, '//' . $this->base_url);
        $params['base_page'] = '//' . $this->base_url;
        if (!$this->daux->options['live']['clean_urls']) {
            $params['base_page'] .= 'index.php/';
        }

        // Text search would be too slow on live server
        $params['html']['search'] = false;

        return $params;
    }

    /**
     * Handle an incoming request
     *
     * @return \Todaymade\Daux\Format\Base\Page
     * @throws Exception
     * @throws NotFoundException
     */
    public function handle()
    {
        $this->params = $this->getParams();

        $request = $this->getRequest();
        $request = urldecode($request);

        if (substr($request, 0, 7) == 'themes/') {
            return $this->serveTheme(substr($request, 6));
        }

        if ($request == 'index_page') {
            $request = $this->daux->tree->getIndexPage()->getUri();
        }

        return $this->getPage($request);
    }

    /**
     * Handle a request on custom themes
     *
     * @return \Todaymade\Daux\Format\Base\Page
     * @throws NotFoundException
     */
    public function serveTheme($request)
    {
        $file = $this->getParams()->getThemesPath() . $request;

        if (file_exists($file)) {
            return new RawPage($file);
        }

        throw new NotFoundException;
    }

    /**
     * @param string $request
     * @return \Todaymade\Daux\Format\Base\Page
     * @throws NotFoundException
     */
    private function getPage($request)
    {
        $file = DauxHelper::getFile($this->daux->tree, $request);
        if ($file === false) {
            throw new NotFoundException('The Page you requested is yet to be made. Try again later.');
        }

        $this->daux->tree->setActiveNode($file);

        $generator = $this->daux->getGenerator();

        if (!$generator instanceof LiveGenerator) {
            throw new \RuntimeException(
                "The generator '" . get_class($generator) . "' does not implement the interface " .
                "'Todaymade\\Daux\\Format\\Base\\LiveGenerator' and thus doesn't support live rendering."
            );
        }

        return $this->daux->getGenerator()->generateOne($file, $this->params);
    }

    public function getRequest()
    {
        if (isset($_SERVER['PATH_INFO'])) {
            $uri = $_SERVER['PATH_INFO'];
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            $uri = $_SERVER['REQUEST_URI'];
            if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0) {
                $uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
            } elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0) {
                $uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
            }
            if (strncmp($uri, '?/', 2) === 0) {
                $uri = substr($uri, 2);
            }
            $parts = preg_split('#\?#i', $uri, 2);
            $uri = $parts[0];
            if (isset($parts[1])) {
                $_SERVER['QUERY_STRING'] = $parts[1];
                parse_str($_SERVER['QUERY_STRING'], $_GET);
            } else {
                $_SERVER['QUERY_STRING'] = '';
                $_GET = [];
            }
            $uri = parse_url($uri, PHP_URL_PATH);
        } else {
            return false;
        }
        $uri = str_replace(['//', '../'], '/', trim($uri, '/'));
        if ($uri == '') {
            $uri = 'index_page';
        }

        return $uri;
    }
}
