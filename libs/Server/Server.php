<?php namespace Todaymade\Daux\Server;

use Todaymade\Daux\Daux;
use Todaymade\Daux\Exception;
use Todaymade\Daux\MarkdownPage;
use Todaymade\Daux\SimplePage;
use Todaymade\Daux\Tree\Directory;

class Server
{

    private $daux;
    private $params;

    public static function serve()
    {
        $daux = new Daux(Daux::LIVE_MODE);

        try {
            $daux->initialize();
            $server = new static($daux);

            $page = $server->handle($_REQUEST);
        } catch (NotFoundException $e) {
            $page = new ErrorPage("An error occured", $e->getMessage(), $daux->getParams());
        }

        $page->display();
    }

    public function __construct(Daux $daux)
    {
        $this->daux = $daux;
    }

    public function handle($query = [])
    {
        $this->params = $this->daux->getParams();

        $request = $this->getRequest();
        $request = urldecode($request);
        $request_type = isset($query['method']) ? $query['method'] : '';
        if ($request == 'first_page') {
            $request = $this->daux->tree->first_page->uri;
        }
        switch ($request_type) {
            case 'DauxEdit':
                if (!$this->daux->options['file_editor']) {
                    throw new Exception('Editing is currently disabled in config');
                }

                $content = isset($query['markdown']) ? $query['markdown'] : '';
                return $this->saveFile($request, $content);

            default:
                return $this->getPage($request);
        }
    }

    private function saveFile($request, $content)
    {
        $file = $this->getFile($request);

        if ($file === false) {
            throw new NotFoundException('The Page you requested is yet to be made. Try again later.');
        }

        if (!$file->write($content)) {
            throw new Exception('The file you wish to write to is not writable.');
        }

        return new SimplePage('Success', 'Successfully Edited');
    }

    private function getFile($request)
    {
        $tree = $this->daux->tree;
        $request = explode('/', $request);
        foreach ($request as $node) {
            // If the element we're in currently is not a
            // directory, we failed to find the requested file
            if (!$tree instanceof Directory) {
                return false;
            }

            // if the node exists in the current request tree,
            // change the $tree variable to reference the new
            // node and proceed to the next url part
            if (isset($tree->value[$node])) {
                $tree = $tree->value[$node];
                continue;
            }

            // At this stage, we're in a directory, but no
            // sub-item matches, so the current node must
            // be an index page or we failed
            if ($node !== 'index' && $node !== 'index.html') {
                return false;
            }

            return $tree->getIndexPage();
        }

        // If the entry we found is not a directory, we're done
        if (!$tree instanceof Directory) {
            return $tree;
        }

        if ($tree->getIndexPage()) {
            return $tree->getIndexPage();
        }

        return false;
    }

    private function getPage($request)
    {
        $params = $this->params;

        $file = $this->getFile($request);
        if ($file === false) {
            throw new NotFoundException('The Page you requested is yet to be made. Try again later.');
        }
        $params['request'] = $request;
        $params['file_uri'] = $file->value;
        if ($request !== 'index') {
            $params['entry_page'] = $file->first_page;
        }
        return MarkdownPage::fromFile($file, $params);
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
