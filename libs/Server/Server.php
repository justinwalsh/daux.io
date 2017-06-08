<?php namespace Todaymade\Daux\Server;

use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Todaymade\Daux\Daux;
use Todaymade\Daux\DauxHelper;
use Todaymade\Daux\Exception;
use Todaymade\Daux\Format\Base\ComputedRawPage;
use Todaymade\Daux\Format\Base\LiveGenerator;
use Todaymade\Daux\Format\Base\Page;
use Todaymade\Daux\Format\HTML\RawPage;

class Server
{
    private $daux;
    private $params;
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
            $page = new ErrorPage('An error occured', $e->getMessage(), $daux->getParams());
        }

        $server->createResponse($page)->prepare($server->request)->send();
    }

    public function __construct(Daux $daux)
    {
        $this->daux = $daux;

        $this->request = Request::createFromGlobals();
        $this->base_url = $this->request->getHttpHost() . $this->request->getBaseUrl() . "/";
    }

    /**
     * Create a temporary file with the file suffix, for mime type detection.
     *
     * @param string $postfix
     * @return string
     */
    function getTemporaryFile($postfix) {
        $sysFileName = tempnam(sys_get_temp_dir(), 'daux');
        if ($sysFileName === false) {
            throw new \RuntimeException("Could not create temporary file");
        }

        $newFileName = $sysFileName . $postfix;
        if ($sysFileName == $newFileName) {
            return $sysFileName;
        }

        if (DIRECTORY_SEPARATOR == '\\' ? rename($sysFileName, $newFileName) : link($sysFileName, $newFileName)) {
            return $newFileName;
        }

        throw new \RuntimeException("Could not create temporary file");
    }

    /**
     * @param Page $page
     * @return Response
     */
    public function createResponse(Page $page) {
        if ($page instanceof RawPage) {
            return new BinaryFileResponse($page->getFile());
        }

        if ($page instanceof ComputedRawPage) {
            $file = $this->getTemporaryFile($page->getFilename());
            file_put_contents($file, $page->getContent());
            return new BinaryFileResponse($file);
        }

        return new Response($page->getContent(), $page instanceof ErrorPage ? 404 : 200);
    }

    /**
     * @return \Todaymade\Daux\Config
     */
    public function getParams()
    {
        $params = $this->daux->getParams();

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

        $request = substr($this->request->getRequestUri(), 1);

        if (substr($request, 0, 7) == 'themes/') {
            return $this->serveTheme(substr($request, 6));
        }

        if ($request == '') {
            $request = $this->daux->tree->getIndexPage()->getUri();
        }

        return $this->getPage($request);
    }

    /**
     * Handle a request on custom themes
     *
     * @param string $request
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
}
