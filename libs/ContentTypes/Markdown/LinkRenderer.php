<?php namespace Todaymade\Daux\ContentTypes\Markdown;

use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Element\Link;
use Todaymade\Daux\Config;
use Todaymade\Daux\DauxHelper;
use Todaymade\Daux\Exception;
use Todaymade\Daux\Tree\Entry;

class LinkRenderer extends \League\CommonMark\Inline\Renderer\LinkRenderer
{
    /**
     * @var Config
     */
    protected $daux;

    public function __construct($daux)
    {
        $this->daux = $daux;
    }

    /**
     * @param string $url
     * @return Entry
     * @throws Exception
     */
    protected function resolveInternalFile($url)
    {
        $triedAbsolute = false;

        // Legacy absolute paths could start with
        // "!" In this case we will try to find
        // the file starting at the root
        if ($url[0] == '!' || $url[0] == '/') {
            $url = ltrim($url, '!/');

            if ($file = DauxHelper::getFile($this->daux['tree'], $url)) {
                return $file;
            }

            $triedAbsolute = true;
        }

        // Seems it's not an absolute path or not found,
        // so we'll continue with the current folder
        if ($file = DauxHelper::getFile($this->daux->getCurrentPage()->getParent(), $url)) {
            return $file;
        }

        // If we didn't already try it, we'll
        // do a pass starting at the root
        if (!$triedAbsolute && $file = DauxHelper::getFile($this->daux['tree'], $url)) {
            return $file;
        }

        throw new Exception("Could not locate file '$url'");
    }

    /**
     * @param AbstractInline|Link $inline
     * @param ElementRendererInterface $htmlRenderer
     * @return HtmlElement
     * @throws Exception
     */
    public function render(AbstractInline $inline, ElementRendererInterface $htmlRenderer)
    {
        // This can't be in the method type as
        // the method is an abstract and should
        // have the same interface
        if (!$inline instanceof Link) {
            throw new \RuntimeException(
                'Wrong type passed to ' . __CLASS__ . '::' . __METHOD__ .
                " the expected type was 'League\\CommonMark\\Inline\\Element\\Link' but '" .
                get_class($inline) . "' was provided"
            );
        }

        $element = parent::render($inline, $htmlRenderer);

        $url = $inline->getUrl();

        // Absolute urls, empty urls and anchors
        // should not go through the url resolver
        if (empty($url) || $url[0] == '#' || preg_match('|^(?:[a-z]+:)?//|', $url)) {
            return $element;
        }

        // if there's a hash component in the url, ensure we
        // don't put that part through the resolver.
        $urlAndHash = explode("#", $url);
        $url = $urlAndHash[0];

        $file = $this->resolveInternalFile($url);

        $url = DauxHelper::getRelativePath($this->daux->getCurrentPage()->getUrl(), $file->getUrl());

        if(isset($urlAndHash[1])) {
            $url .= "#" . $urlAndHash[1];
        }

        $element->setAttribute('href', $url);

        return $element;
    }
}
