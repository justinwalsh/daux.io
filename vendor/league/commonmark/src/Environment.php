<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark;

use League\CommonMark\Block\Parser\BlockParserInterface;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\Extension\CommonMarkCoreExtension;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Extension\MiscExtension;
use League\CommonMark\Inline\Parser\InlineParserInterface;
use League\CommonMark\Inline\Processor\InlineProcessorInterface;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;
use League\CommonMark\Util\Configuration;

class Environment
{
    /**
     * @var ExtensionInterface[]
     */
    protected $extensions = array();

    /**
     * @var MiscExtension
     */
    protected $miscExtension;

    /**
     * @var bool
     */
    protected $extensionsInitialized = false;

    /**
     * @var BlockParserInterface[]
     */
    protected $blockParsers = array();

    /**
     * @var BlockRendererInterface[]
     */
    protected $blockRenderersByClass = array();

    /**
     * @var InlineParserInterface[]
     */
    protected $inlineParsers = array();

    /**
     * @var array
     */
    protected $inlineParsersByCharacter = array();

    /**
     * @var InlineProcessorInterface[]
     */
    protected $inlineProcessors = array();

    /**
     * @var InlineRendererInterface[]
     */
    protected $inlineRenderersByClass = array();

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var string
     */
    protected $inlineParserCharacterRegex;

    public function __construct(array $config = array())
    {
        $this->miscExtension = new MiscExtension();
        $this->config = new Configuration($config);
    }

    /**
     * @param array $config
     */
    public function mergeConfig(array $config = array())
    {
        $this->assertUninitialized('Failed to modify configuration - extensions have already been initialized');

        $this->config->mergeConfig($config);
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config = array())
    {
        $this->assertUninitialized('Failed to modify configuration - extensions have already been initialized');

        $this->config->setConfig($config);
    }

    /**
     * @param string|null $key
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function getConfig($key = null, $default = null)
    {
        return $this->config->getConfig($key, $default);
    }

    /**
     * @param BlockParserInterface $parser
     *
     * @return $this
     */
    public function addBlockParser(BlockParserInterface $parser)
    {
        $this->assertUninitialized('Failed to add block parser - extensions have already been initialized');

        $this->miscExtension->addBlockParser($parser);

        return $this;
    }

    /**
     * @param string $blockClass
     * @param BlockRendererInterface $blockRenderer
     *
     * @return $this
     */
    public function addBlockRenderer($blockClass, BlockRendererInterface $blockRenderer)
    {
        $this->assertUninitialized('Failed to add block renderer - extensions have already been initialized');

        $this->miscExtension->addBlockRenderer($blockClass, $blockRenderer);

        return $this;
    }

    /**
     * @param InlineParserInterface $parser
     *
     * @return $this
     */
    public function addInlineParser(InlineParserInterface $parser)
    {
        $this->assertUninitialized('Failed to add inline parser - extensions have already been initialized');

        $this->miscExtension->addInlineParser($parser);

        return $this;
    }

    /**
     * @param InlineProcessorInterface $processor
     *
     * @return $this
     */
    public function addInlineProcessor(InlineProcessorInterface $processor)
    {
        $this->assertUninitialized('Failed to add inline processor - extensions have already been initialized');

        $this->miscExtension->addInlineProcessor($processor);

        return $this;
    }

    /**
     * @param string $inlineClass
     * @param InlineRendererInterface $renderer
     *
     * @return $this
     */
    public function addInlineRenderer($inlineClass, InlineRendererInterface $renderer)
    {
        $this->assertUninitialized('Failed to add inline renderer - extensions have already been initialized');

        $this->miscExtension->addInlineRenderer($inlineClass, $renderer);

        return $this;
    }

    /**
     * @return BlockParserInterface[]
     */
    public function getBlockParsers()
    {
        $this->initializeExtensions();

        return $this->blockParsers;
    }

    /**
     * @param string $blockClass
     *
     * @return BlockRendererInterface|null
     */
    public function getBlockRendererForClass($blockClass)
    {
        $this->initializeExtensions();

        if (!isset($this->blockRenderersByClass[$blockClass])) {
            return null;
        }

        return $this->blockRenderersByClass[$blockClass];
    }

    /**
     * @param string $name
     *
     * @return InlineParserInterface
     */
    public function getInlineParser($name)
    {
        $this->initializeExtensions();

        return $this->inlineParsers[$name];
    }

    /**
     * @return InlineParserInterface[]
     */
    public function getInlineParsers()
    {
        $this->initializeExtensions();

        return $this->inlineParsers;
    }

    /**
     * @param string $character
     *
     * @return InlineParserInterface[]|null
     */
    public function getInlineParsersForCharacter($character)
    {
        $this->initializeExtensions();

        if (!isset($this->inlineParsersByCharacter[$character])) {
            return null;
        }

        return $this->inlineParsersByCharacter[$character];
    }

    /**
     * @return InlineProcessorInterface[]
     */
    public function getInlineProcessors()
    {
        $this->initializeExtensions();

        return $this->inlineProcessors;
    }

    /**
     * @param string $inlineClass
     *
     * @return InlineRendererInterface|null
     */
    public function getInlineRendererForClass($inlineClass)
    {
        $this->initializeExtensions();

        if (!isset($this->inlineRenderersByClass[$inlineClass])) {
            return null;
        }

        return $this->inlineRenderersByClass[$inlineClass];
    }

    public function createInlineParserEngine()
    {
        $this->initializeExtensions();

        return new InlineParserEngine($this);
    }

    /**
     * Get all registered extensions
     *
     * @return ExtensionInterface[]
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Add a single extension
     *
     * @param ExtensionInterface $extension
     *
     * @return $this
     */
    public function addExtension(ExtensionInterface $extension)
    {
        $this->assertUninitialized('Failed to add extension - extensions have already been initialized');

        $this->extensions[$extension->getName()] = $extension;

        return $this;
    }

    protected function initializeExtensions()
    {
        // Only initialize them once
        if ($this->extensionsInitialized) {
            return;
        }

        $this->extensionsInitialized = true;

        // Initialize all the registered extensions
        foreach ($this->extensions as $extension) {
            $this->initializeExtension($extension);
        }

        // Also initialize those one-off classes
        $this->initializeExtension($this->miscExtension);

        // Lastly, let's build a regex which matches all inline characters
        // This will enable a huge performance boost with inline parsing
        $this->buildInlineParserCharacterRegex();
    }

    /**
     * @param ExtensionInterface $extension
     */
    protected function initializeExtension(ExtensionInterface $extension)
    {
        $this->initalizeBlockParsers($extension->getBlockParsers());
        $this->initializeBlockRenderers($extension->getBlockRenderers());
        $this->initializeInlineParsers($extension->getInlineParsers());
        $this->initializeInlineProcessors($extension->getInlineProcessors());
        $this->initializeInlineRenderers($extension->getInlineRenderers());
    }

    /**
     * @param BlockParserInterface[] $blockParsers
     */
    private function initalizeBlockParsers($blockParsers)
    {
        foreach ($blockParsers as $blockParser) {
            if ($blockParser instanceof EnvironmentAwareInterface) {
                $blockParser->setEnvironment($this);
            }

            $this->blockParsers[$blockParser->getName()] = $blockParser;
        }
    }

    /**
     * @param BlockRendererInterface[] $blockRenderers
     */
    private function initializeBlockRenderers($blockRenderers)
    {
        foreach ($blockRenderers as $class => $blockRenderer) {
            $this->blockRenderersByClass[$class] = $blockRenderer;
        }
    }

    /**
     * @param InlineParserInterface[] $inlineParsers
     */
    private function initializeInlineParsers($inlineParsers)
    {
        foreach ($inlineParsers as $inlineParser) {
            if ($inlineParser instanceof EnvironmentAwareInterface) {
                $inlineParser->setEnvironment($this);
            }

            $this->inlineParsers[$inlineParser->getName()] = $inlineParser;

            foreach ($inlineParser->getCharacters() as $character) {
                $this->inlineParsersByCharacter[$character][] = $inlineParser;
            }
        }
    }

    /**
     * @param InlineProcessorInterface[] $inlineProcessors
     */
    private function initializeInlineProcessors($inlineProcessors)
    {
        foreach ($inlineProcessors as $inlineProcessor) {
            $this->inlineProcessors[] = $inlineProcessor;
        }
    }

    /**
     * @param InlineRendererInterface[] $inlineRenderers
     */
    private function initializeInlineRenderers($inlineRenderers)
    {
        foreach ($inlineRenderers as $class => $inlineRenderer) {
            $this->inlineRenderersByClass[$class] = $inlineRenderer;
        }
    }

    /**
     * @return Environment
     */
    public static function createCommonMarkEnvironment()
    {
        $environment = new static();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->mergeConfig(array(
            'renderer' => array(
                'block_separator' => "\n",
                'inner_separator' => "\n",
                'soft_break' => "\n",
            )
        ));

        return $environment;
    }

    /**
     * Regex which matches any character that an inline parser might be interested in
     *
     * @return string
     */
    public function getInlineParserCharacterRegex()
    {
        return $this->inlineParserCharacterRegex;
    }

    private function buildInlineParserCharacterRegex()
    {
        $chars = array_keys($this->inlineParsersByCharacter);

        $this->inlineParserCharacterRegex = '/^[^' . preg_quote(implode('', $chars)) . ']+/';
    }

    /**
     * @param string $message
     *
     * @throws \RuntimeException
     */
    private function assertUninitialized($message = 'The environment cannot be modified after initialization')
    {
        if ($this->extensionsInitialized) {
            throw new \RuntimeException($message);
        }
    }
}
