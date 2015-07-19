<?php namespace Todaymade\Daux\Server;

use Todaymade\Daux\Daux;
use Todaymade\Daux\DauxHelper;
use Todaymade\Daux\Exception;
use Todaymade\Daux\Format\HTML\Generator;
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

        try {
            $daux->initialize();
            $server = new static($daux);

            $page = $server->handle($_REQUEST);
        } catch (NotFoundException $e) {
            http_response_code(404);
            $page = new ErrorPage("An error occured", $e->getMessage(), $daux->getParams());
        }

        if ($page instanceof RawPage) {
            header('Content-type: ' . MimeType::get($page->getFile()));

            // Transfer file in 1024 byte chunks to save memory usage.
            if ($fd = fopen($page->getFile(), 'rb')) {
                while (!feof($fd)) {
                    print fread($fd, 1024);
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
        $this->base_url = $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
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

        $params['index_key'] = 'index';
        $params['host'] = $this->host;
        $params['base_page'] = $params['base_url'] = '//' . $this->base_url;
        if (!$this->daux->options['clean_urls']) {
            $params['base_page'] .= 'index.php/';
        }

        if ($params['image'] !== '') {
            $params['image'] = str_replace('<base_url>', $params['base_url'], $params['image']);
        }

        $params['theme'] = DauxHelper::getTheme($params, $this->base_url);

        return $params;
    }

    /**
     * Handle an incoming request
     *
     * @param array $query
     * @return \Todaymade\Daux\Tree\Entry
     * @throws Exception
     * @throws NotFoundException
     */
    public function handle($query = [])
    {
        $this->params = $this->getParams();

        $request = $this->getRequest();
        $request = urldecode($request);
        if ($request == 'first_page') {
            $request = $this->daux->tree->getFirstPage()->getUri();
        }

        return $this->getPage($request);
    }

    /**
     * @param string $request
     * @return \Todaymade\Daux\Tree\Entry
     * @throws NotFoundException
     */
    private function getPage($request)
    {
        $file = DauxHelper::getFile($this->daux->tree, $request);
        if ($file === false) {
            throw new NotFoundException('The Page you requested is yet to be made. Try again later.');
        }

        // TODO :: make it possible to replace the generator in live code
        $generator = new Generator($this->daux);
        return $generator->generateOne($file, $this->params);
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
                $_GET = array();
            }
            $uri = parse_url($uri, PHP_URL_PATH);
        } else {
            return false;
        }
        $uri = str_replace(array('//', '../'), '/', trim($uri, '/'));
        if ($uri == "") {
            $uri = "first_page";
        }
        return $uri;
    }
}
