<?php

/**
 * Generates documentation structure.
 * Usage:
 *  php structure_generate.php outlineFile outputDir
 * 
 * outlineFile : A file containing the properly formtted outline, see outline.sample.txt
 * outputDir : Optional, output file directory for the generated structure/
 * 
 * @todo :
 *  - Allow users specify config.json values.
 *  - Load config.json values if they exist in target directory
 */
use Todaymade\Daux\StructureGenerator;

require_once 'libs/daux_structure_generator.php';

$file = $argv[1];
$outDir = isset($argv[2]) && strlen(trim($argv[2])) ? $argv[2] : getcwd() . DIRECTORY_SEPARATOR . 'docs';

//@TODO accept options from command line
$structureGenerator = new StructureGenerator([
    'indentation' => '  ', //two spaces, per level
    'docLandingPage' => true, //generate landing page for documentation
    'sectionLandingPage' => true, //generate landing page per section
    'weightStep' => 2, //weight differences in order of items in same section
    'createConfig' => true, //create config.json
        ]);


//@TODO accept config from CLI or load from yaml/json.
$structureGenerator->setConfig(array(
    'title' => 'Sample Generated'
        ), true);

$rootNode = $structureGenerator->generateTree($file);

//print structure 
$rootNode->printNode();

//generate outline
$structureGenerator->createFsStructure($rootNode, $outDir);

//generate globals
$globalConfig = array(
    'docs_directory' => realpath($outDir),
    'valid_markdown_extensions' => array("md", "markdown")
);

$globalsFile = 'globals_' . basename($outDir) . '.json';

file_put_contents($globalsFile, json_encode($globalConfig, JSON_PRETTY_PRINT));

echo "Structure has been successfully generated", PHP_EOL;
echo "To generate static files, use: ", PHP_EOL;
echo "\tphp generate.php {$globalsFile} destinationFolder", PHP_EOL;
