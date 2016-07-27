<?php namespace Todaymade\Daux\Format\Confluence\ContentTypes\Markdown;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;

class FencedCodeRenderer implements BlockRendererInterface
{
    protected $supported_languages = [
        'actionscript3',
        'bash',
        'csharp',
        'coldfusion',
        'cpp',
        'css',
        'delphi',
        'diff',
        'erlang',
        'groovy',
        'html/xml',
        'java',
        'javafx',
        'javascript',
        'none',
        'perl',
        'php',
        'powershell',
        'python',
        'ruby',
        'scala',
        'sql',
        'vb',
    ];
    protected $known_conversions = ['html' => 'html/xml', 'xml' => 'html/xml', 'js' => 'javascript'];

    /**
     * @param AbstractBlock $block
     * @param HtmlRendererInterface $htmlRenderer
     * @param bool $inTightList
     *
     * @return HtmlElement|string
     */
    public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, $inTightList = false)
    {
        if (!($block instanceof FencedCode)) {
            throw new \InvalidArgumentException('Incompatible block type: ' . get_class($block));
        }

        $content = [];

        if ($language = $this->getLanguage($block->getInfoWords(), $htmlRenderer)) {
            $content[] = new HtmlElement('ac:parameter', ['ac:name' => 'language'], $language);
        }

        $content[] = new HtmlElement('ac:plain-text-body', [], '<![CDATA[' . $block->getStringContent() . ']]>');

        return new HtmlElement(
            'ac:structured-macro',
            ['ac:name' => 'code'],
            $content
        );
    }

    public function getLanguage($infoWords, ElementRendererInterface $htmlRenderer)
    {
        if (count($infoWords) === 0 || strlen($infoWords[0]) === 0) {
            return false;
        }

        $language = $htmlRenderer->escape($infoWords[0], true);

        if (array_key_exists($language, $this->known_conversions)) {
            $language = $this->known_conversions[$language];
        }

        if (in_array($language, $this->supported_languages)) {
            return $language;
        }

        return false;
    }
}
