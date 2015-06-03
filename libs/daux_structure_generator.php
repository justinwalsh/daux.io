<?php

namespace Todaymade\Daux;

use RuntimeException;

class StructureGenerator {

    /**
     *
     * @var array options for generating 
     */
    private $options = [
        //indentations
        'indentation' => '  ',
        'sectionLandingPage' => true,
        'docLandingPage' => true,
        'dirMode' => 0755,
        'createConfig' => true,
        'weightStep' => 2,
        'markdownExtension' => 'md',
    ];
    private $defaultConfig = array(
        'title' => 'DAUX.IO',
        'tagline' => 'The Easiest Way To Document Your Project',
        'author' => '--Author-Name--',
        'image' => 'img/app.png',
        'theme' => 'daux-blue',
        'template' => 'default',
        'clean_urls' => true,
        'toggle_code' => true,
        'breadcrumbs' => true,
        'breadcrumb_separator' => 'Chevrons',
        'date_modified' => true,
        'float' => true,
        'file_editor' => false,
        'repo' => 'justinwalsh/daux.io',
        'ignore' =>
        array(
            'files' =>
            array(
                0 => 'Work_In_Progress.md',
            ),
            'folders' =>
            array(
                0 => '99_Not_Ready',
            ),
        ),
        'twitter' =>
        array(
            0 => 'justin_walsh',
            1 => 'todaymade',
        ),
        'google_analytics' => '',
        'links' =>
        array(
            'Download' => 'https://github.com/justinwalsh/daux.io/archive/master.zip',
            'GitHub Repo' => 'https://github.com/justinwalsh/daux.io',
            'Help/Support/Bugs' => 'https://github.com/justinwalsh/daux.io/issues',
            'Made by Todaymade' => 'http://todaymade.com',
        ),
    );
    private $config = array();

    public function __construct(array $opts = array()) {
        $this->options = array_merge($this->options, $opts);
        $this->config = $this->defaultConfig;
    }

    public function setConfig(array $config, $merge = true) {
        if ($merge) {
            $this->config = array_merge($this->config, $config);
        } else {
            $this->config = $config;
        }
        return $this;
    }

    public function resetConfig() {
        $this->config = $this->defaultConfig;
        return $this;
    }

    public function getOpt($key, $default = null) {
        return array_key_exists($key, $this->options) ? $this->options[$key] : $default;
    }

    public function setOpt($key, $newVal) {
        $this->options[$key] = $newVal;
        return $this;
    }

    /**
     * 
     * Generates a tree from a well formatted outline file.
     * @param string $outlineFile
     * @return Node the root node of the tree created,
     * @throws StructureGeneratorException
     */
    public function generateTree($outlineFile) {
        Node::setWeightStep($this->getOpt('weightStep', 1));
        if (!is_file($outlineFile) || !is_readable($outlineFile)) {
            throw new StructureGeneratorException("File: [{$outlineFile}] was not found");
        }

        $fr = fopen($outlineFile, 'r');
        $rootNode = new Node('Root');
        $currentLevel = 0;

        $nodeStack = array();
        $levelParent = $rootNode;
        $currentNode = null;

        while (($line = fgets($fr)) !== false) {
            $l = rtrim($line);
            if (!strlen($l)) {
                //skip blanks
                continue;
            }

            $level = $this->getLevel($l);
            if ($level !== $currentLevel) {
                //change in preferred way
                if ($level > $currentLevel) {
                    //we don't expect skips in level
                    if ($level > $currentLevel + 1) {
                        throw new StructureGeneratorException("A level is missing, last level was {$currentLevel}, new level is {$level} for [{$l}] after [{$currentNode}]");
                    }

                    //push parent at this level
                    array_push($nodeStack, $levelParent);
                    //set new parent to the parent at this level
                    $levelParent = $currentNode;
                    $currentLevel = $level;
                } else {
                    //keep popping until a suitable level is reached.
                    while ($currentLevel > $level) {
                        $levelParent = array_pop($nodeStack);
                        --$currentLevel;
                    }
                }
                //change level
            }//else parent unchanged.


            $currentNode = new Node($l, $levelParent);
            if ($levelParent) {
                $levelParent->addNode($currentNode);
            }
        }

        return $rootNode;
    }

    private function writeIfNotExist($path, $content) {
        if (!file_exists($path)) {
            return file_put_contents($path, $content);
        }
        return 0;
    }

    public function createFsStructure(Node $node, $rootDirectory) {
        //only check once
        //then ensure that the root directory can be written to, if not, ensure it is writable
        //this is not a node
        $dirMode = $this->getOpt('dirMode', 0755);

        if ($node->isRoot()) {
            if (!is_dir($rootDirectory) && !mkdir($rootDirectory, $dirMode)) {
                throw new StructureGeneratorException("Root directory is not writable and could not be created.");
            }

            $rootDirectory = realpath($rootDirectory);

            if ($this->getOpt('docLandingPage', true)) {
                $index = $rootDirectory . DIRECTORY_SEPARATOR . '_index.md';
                $this->writeIfNotExist($index, "##{$node->getLabel()}##\n");
            }

            if ($this->getOpt('createConfig', false)) {
                $this->writeIfNotExist($rootDirectory . DIRECTORY_SEPARATOR . 'config.json', json_encode($this->config, JSON_PRETTY_PRINT));
            }
        }

        $path = $rootDirectory . $node->getFsName(false);

        if ($node->isLeaf()) {
            //create directory
            $dir = dirname($path);
            if (!is_dir($dir) && !mkdir($dir, $dirMode, true)) {
                throw new StructureGeneratorException('Could not create directory: [' . $dir . ']');
            }

            $path .= "." . $this->getOpt('markdownExtension', 'md');
            $this->writeIfNotExist($path, "##{$node->getLabel()}##\n");
        } else {
            if (!is_dir($path) && !mkdir($path, $dirMode, true)) {
                throw new RuntimeException("Target directory: {$path} could not be created.");
            }

            if (!$node->isRoot() && $this->getOpt('sectionLandingPage', false)) {
                $idxContent = "##{$node->getLabel()}##\nContent:\n";
                foreach ($node->getChildren() as $child){
                    $idxContent .= "- [{$child->getLabel()}]({$child->getUrl()})\n";
                }
                
                $this->writeIfNotExist($path . DIRECTORY_SEPARATOR . 'index.md', $idxContent);
            }

            foreach ($node->getChildren() as $childNode) {
                $this->createFsStructure($childNode, $rootDirectory);
            }
            
            
        }
    }

    private function getLevel($str) {
        $level = 0;
        $indentation = $this->getOpt('indentation');
        $iLength = strlen($indentation);

        if ($iLength < 1) {
            throw new StructureGeneratorException("Indentation cannot have a length less than 1");
        }

        while (substr($str, 0, $iLength) === $indentation) {
            ++$level;
            $str = substr($str, $iLength);
        }

        return $level;
    }

}

class Node {

    /**
     *
     * @var Node
     */
    protected $parent;

    /**
     *
     * @var int Weight Step
     */
    protected static $weightStep = 1;

    /**
     *
     * @var Node[]
     */
    protected $children;
    protected $label;
    protected $order;

    public function __construct($label, $parent = null, $order = -1) {
        $this->parent = $parent;
        $this->label = trim($label);
        $this->order = $order;
    }

    public function getOrder() {
        return $this->order;
    }

    public function setOrder($order) {
        $this->order = $order;
        return $this;
    }

    /**
     * 
     * @param Node $node
     * @return \Node
     */
    public function addNode(Node $node) {
        $this->children[] = $node->setOrder(count($this->children));
        return $node;
    }

    public function getParent() {
        return $this->parent;
    }

    public function getChildren() {
        return $this->children;
    }

    public function isRoot() {
        return $this->parent === null;
    }

    public function printNode($indent = 0) {
        echo sprintf("%s%s", str_repeat("\t", $indent), $this->getFsName(true)), PHP_EOL;
        if (count($this->children) > 0) {
            foreach ($this->children as $aNode) {
                $aNode->printNode($indent + 1);
            }
        }
    }

    public function isLeaf() {
        return empty($this->children) && $this->parent !== null;
    }

    public function getFsName($baseOnly = true) {
        if ($this->isRoot()) {
            return '';
        }

        $baseName = sprintf("%03d_%s", $this->order * self::$weightStep, $this->_name());
        return ($baseOnly || !$this->parent) ? $baseName : ($this->parent->getFsName($baseOnly) . DIRECTORY_SEPARATOR . $baseName);
    }

    private function _name() {
        $name = preg_replace('/[\s\\/\\\\:\~]/', '_', $this->label);
        if (preg_match('/^\d/', $name)) {
            $name = '_' . $name;
        }
        
        return $name;
    }

    public function getUrl() {
        if ($this->isRoot()) {
            return '';
        }
        
        return ltrim($this->parent->getUrl() . '/' . $this->_name(), '/');
    }

    public static function setWeightStep($step = 1) {
        self::$weightStep = $step;
    }

    public function getLabel() {
        return $this->label;
    }

    public function __toString() {
        return $this->isRoot() ? '[ROOT]' : $this->getFsName();
    }

}

class StructureGeneratorException extends RuntimeException {
    
}
