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

require_once('libs/functions.php');

$options = get_options();
$tree = get_tree($options['docs_path'], $base_url);

// If a language is set in the config, rewrite urls based on the language
if (! isset($language) || $language === null) {
    $homepage_url = homepage_url($tree);
    $docs_url = docs_url($tree);
} else {
    $homepage_url = "/";
}

$docs_url = docs_url($tree);
$url_params = url_params();

if (count($options['languages']) > 0 && count($url_params) > 0 && strlen($url_params[0]) > 0) {
    $language = array_shift($url_params);
    $base_path = $options['docs_path'] . $language;
} else {
    $language = null;
    $base_path = $options['docs_path'];
}

$tree = get_tree($base_path, $base_url, '', true, $language);



$page = load_page($tree, $url_params);

// If a timezone has been set in the config file, override the default PHP timezone for this application.
if(isset($options['timezone']))
{
    date_default_timezone_set($options['timezone']);
}


// Redirect to docs, if there is no homepage
if ($homepage && $homepage_url !== '/') {
    header('Location: '.$homepage_url);
}

include('template/'.$options['template'].'.tpl');