<?php namespace Todaymade\Daux\Server;

use Todaymade\Daux\Daux;
use Todaymade\Daux\Exception;
use Todaymade\Daux\MarkdownPage;
use Todaymade\Daux\SimplePage;

class Server {

    private $daux;
    private $params;

    public static function serve() {
        $daux = new Daux(Daux::LIVE_MODE);

        try
        {
            $daux->initialize();
            $server = new static($daux);

            $page = $server->handle($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], $_REQUEST);
        }
        catch( NotFoundException $e )
        {
            $page = new ErrorPage("An error occured", $e->getMessage(), $daux->get_live_page_params());
        }

        $page->display();
    }

    public function __construct(Daux $daux) {
        $this->daux = $daux;
    }

    public function handle($url, $query = []) {
        $this->params = $this->daux->get_live_page_params();

        $request = Helper::get_request();
        $request = urldecode($request);
        $request_type = isset($query['method']) ? $query['method'] : '';
        if($request == 'first_page') {
            $request = $this->daux->tree->first_page->uri;
        }
        switch ($request_type) {
            case 'DauxEdit':
                if (!$this->daux->options['file_editor']) {
                    throw new Exception('Editing is currently disabled in config');
                }

                $content = isset($query['markdown']) ? $query['markdown'] : '';
                return $this->save_file($request, $content);

            default:
                return $this->get_page($request);
        }
    }

    private function save_file($request, $content) {
        $file = $this->get_file_from_request($request);

        if ($file === false) throw new NotFoundException('The Page you requested is yet to be made. Try again later.');

        if (!$file->write($content)) throw new Exception('The file you wish to write to is not writable.');

        return new SimplePage('Success', 'Successfully Edited');
    }

    private function get_file_from_request($request) {
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

            return $tree->index_page;
        }

        // If the entry we found is not a directory, we're done
        if (!$tree instanceof Directory) {
            return $tree;
        }

        if ($tree->index_page){
            return $tree->index_page;
        }

        return ($get_first_file) ? $tree->first_page : false;
    }

    private function get_page($request) {
        $params = $this->params;

        $file = $this->get_file_from_request($request);
        if ($file === false) throw new NotFoundException('The Page you requested is yet to be made. Try again later.');
        $params['request'] = $request;
        $params['file_uri'] = $file->value;
        if ($request !== 'index') $params['entry_page'] = $file->first_page;
        return MarkdownPage::fromFile($file, $params);
    }
}
