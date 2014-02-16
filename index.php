<?php
/*

Daux.io
==================

Description
-----------

This is a tool for auto-generating documentation based on markdown files
located in the /docs folder of the project. To see all of the available
options and to read more about how to use the generator, visit:

http://daux.io


Author
------
Justin Walsh (Todaymade): justin@todaymade.com, @justin_walsh
Garrett Moon (Todaymade): garrett@todaymade.com, @garrett_moon


Feedback & Suggestions
----

To give us feedback or to suggest an idea, please create an request on the the
GitHub issue tracker:

https://github.com/justinwalsh/daux.io/issues

Bugs
----

To file bug reports please create an issue using the github issue tracker:

https://github.com/justinwalsh/daux.io/issues


Copyright and License
---------------------
Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are
met:

*   Redistributions of source code must retain the above copyright notice,
    this list of conditions and the following disclaimer.

*   Redistributions in binary form must reproduce the above copyright
    notice, this list of conditions and the following disclaimer in the
    documentation and/or other materials provided with the distribution.

This software is provided by the copyright holders and contributors "as
is" and any express or implied warranties, including, but not limited
to, the implied warranties of merchantability and fitness for a
particular purpose are disclaimed. In no event shall the copyright owner
or contributors be liable for any direct, indirect, incidental, special,
exemplary, or consequential damages (including, but not limited to,
procurement of substitute goods or services; loss of use, data, or
profits; or business interruption) however caused and on any theory of
liability, whether in contract, strict liability, or tort (including
negligence or otherwise) arising in any way out of the use of this
software, even if advised of the possibility of such damage.

*/

require_once dirname( __FILE__ ) . '/libs/functions.php';
$options = get_options();

$command_line=FALSE;
if(isset($argv)){
    define("CLI",TRUE);
    echo 'Daux.io documentation generator'."\n";
    
    if(!isset($argv[1]))
        $argv[1]= 'help';

    switch ($argv[1]) {
        //Generate static web documentation
        case 'generate':
            clean_copy_assets(dirname(__FILE__).'/static');

            //Generate index.html
            $markdown_structure = array();
            $base_url = '.';
            $index = generate_page($options, array(''), $base_url, TRUE, $markdown_structure);
            file_put_contents('./static/index.html', $index);
            echo '.';

            foreach ($markdown_structure as $element) {
                echo '.';
                $flat_tree_tmp = array();
                if( preg_match('/\.\/(.*)\/(.*)$/', $element['url'], $final_folder) ){
                    @mkdir('./static/'.$final_folder[1]);

                    $url_params =  preg_split('/\//',$final_folder[1] );
                    $folder_count = count($url_params);
                    array_push( $url_params , $final_folder[2] );

                    $base_url = relative_base($folder_count);
                    $file = generate_page($options, $url_params, $base_url, TRUE, $flat_tree_tmp);
                    file_put_contents('./static/'.$final_folder[1].'/'.$final_folder[2].'.html', $file);
                }else{
                    $strFile = str_replace('./', '', $element['url']);
                    $base_url = '.';
                    $file = generate_page($options, array($strFile), $base_url, TRUE, $flat_tree_tmp);
                    file_put_contents('./static/'.$strFile.'.html', $file);
                }               
            }
            echo "finished\n";
            echo "The documentation is generated in static folder\n";
            break;
        //Generate one-page documentation
        case 'full-doc':
            clean_copy_assets(dirname(__FILE__).'/static');

            $options['template'] ='full-doc';
            $markdown_structure = array();
            //Generate index.html
            $markdown_structure = array();
            $base_url = '.';
            $index = generate_page($options, array(''), $base_url, TRUE, $markdown_structure);
            file_put_contents('./static/full-doc.html', load_tpl_block('full-doc-blocks/head', $options, $base_url).$index);
            echo '.';
            array_pop($markdown_structure);

            foreach ($markdown_structure as $element) {
                echo '.';
                $flat_tree_tmp = array();
                if( preg_match('/\.\/(.*)\/(.*)$/', $element['url'], $final_folder) ){
                    $url_params = preg_split('/\//',$final_folder[1] );
                    $folder_count = count($url_params);
                    array_push( $url_params , $final_folder[2] );

                    $file = generate_page($options, $url_params, $base_url, TRUE, $flat_tree_tmp);
                    file_put_contents('./static/full-doc.html', $file, FILE_APPEND);
                }else{
                    $strFile = str_replace('./', '', $element['url']);
                    $file = generate_page($options, array($strFile), $base_url, TRUE, $flat_tree_tmp);
                    file_put_contents('./static/full-doc.html', $file, FILE_APPEND);
                }               
            }
            file_put_contents('./static/full-doc.html', file_get_contents('template/full-doc-blocks/foot.tpl'), FILE_APPEND);
            echo "finished\n";
            echo "The documentation is generated in static folder\n";
            break;
        default:
            echo "\n"; 
            echo 'Usage:'."\n";
            echo ' php index.php generate'."\n";
            echo ' php index.php full-doc'."\n";
            echo "\n";
            echo 'generate. Generate static web'."\n";
            echo 'fulldoc. Generate one-file documentation static html'."\n";
            echo "\n";
            break;
    }    
    exit();
}
define("CLI", FALSE);

$url_params = url_params();
generate_page($options, $url_params, get_base_url());

