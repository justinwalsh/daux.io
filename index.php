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
$command_line=FALSE;
if(isset($argv)){
    require_once dirname( __FILE__ ) . '/libs/static.php';
    define("CLI",TRUE);
    echo 'Daux.io documentation generator'."\n";

    if(!isset($argv[1]))
        $argv[1]= 'help';

    switch ($argv[1]) {
        //Generate static web documentation
        case 'generate':
            generate_static((isset($argv[3])) ? $argv[3] : '');
            echo "Finished\n";
            echo "The documentation is generated in static folder\n";
            break;
        default:
            echo "\n";
            echo 'Usage:'."\n";
            echo ' php index.php generate'."\n";
            echo 'Generate static web'."\n";
            echo "\n";
            break;
    }
    exit();
}
require_once(dirname( __FILE__)."/libs/live.php");
$base_path = str_replace("/index.php", "", $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
define("CLI", FALSE);
build_tree();
$remove = array($base_path . '/');
if (!$options['clean_urls']) $remove[] = 'index.php?';
$request = str_replace($remove, "", $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
$request = rawurldecode($request);
if (isset($_POST['markdown']) && $options['file_editor'])
    file_put_contents(clean_url_to_file($request), $_POST['markdown']);
echo generate_live($request);
