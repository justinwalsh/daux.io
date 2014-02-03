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

// Handle AJAX requests
if(isset($_POST["markdown"]) && $options["file_editor"] === true) {
    handle_editor_post($_POST, $page);
    die;
}

// If a timezone has been set in the config file, override the default PHP timezone for this application.
if(isset($options['timezone']))
{
    date_default_timezone_set($options['timezone']);
}


// Redirect to docs, if there is no homepage
if ($homepage && $homepage_url !== '/') {
    header('Location: '.$homepage_url);
}

?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <title><?php echo $options['title']; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="<?php echo $options['tagline'];?>" />
    <meta name="author" content="<?php echo $options['title']; ?>">
    <?php if ($options['colors']) { ?>
    <link rel="icon" href="<?php echo $base_url ?>/img/favicon.png" type="image/x-icon">
    <?php } else { ?>
    <link rel="icon" href="<?php echo $base_url ?>/img/favicon-<?php echo $options['theme'];?>.png" type="image/x-icon">
    <?php } ?>
    <!-- Mobile -->
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Font -->
    <link href='http://fonts.googleapis.com/css?family=Roboto+Slab:400,700,300,100' rel='stylesheet' type='text/css'>

    <!-- LESS -->
    <?php if ($options['colors']) { ?>
        <style type="text/less">
            <?php foreach($options['colors'] as $k => $v) { ?>
            @<?php echo $k;?>: <?php echo $v;?>;
            <?php } ?>
            @import "<?php echo $base_url ?>/less/import/daux-base.less";
        </style>
        <script src="<?php echo $base_url ?>/js/less.min.js"></script>
    <?php } else { ?>
        <link rel="stylesheet" href="<?php echo $base_url ?>/css/daux-<?php echo $options['theme'];?>.css">
    <?php } ?>

    <!-- hightlight.js -->
    <script src="<?php echo $base_url ?>/js/highlight.min.js"></script>
    <script>hljs.initHighlightingOnLoad();</script>

    <!-- Navigation -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.0/jquery.min.js"></script>

    <?php if($options["file_editor"]) { ?>
    <!-- Front end file editor -->
    <script src="<?php echo $base_url ?>/js/editor.js"></script>
    <?php } ?>
    <script>
    if (typeof jQuery == 'undefined') {
        document.write(unescape("%3Cscript src='<?php echo $base_url ?>/js/jquery-1.10.2.min.js' type='text/javascript'%3E%3C/script%3E"));
    }
    </script>
    <script src="<?php echo $base_url ?>/js/bootstrap.min.js"></script>
    <script src="<?php echo $base_url ?>/js/custom.js"></script>
    <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>
    <?php if ($homepage) { ?>
        <!-- Hompage -->
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <a class="brand pull-left" href="<?php echo $base_url ?><?php echo $homepage_url;?>"><?php echo $options['title']; ?></a>
                    <p class="navbar-text pull-right">
                        Generated by <a href="http://daux.io">Daux.io</a>
                    </p>
                </div>
            </div>
        </div>

        <div class="homepage-hero well container-fluid">
            <div class="container">
                <div class="row">
                    <div class="text-center span12">
                        <?php if ($options['tagline']) { ?>
                            <h2><?php echo $options['tagline'];?></h2>
                        <?php } ?>
                    </div>
                </div>
                <div class="row">
                    <div class="span10 offset1">
                        <?php if ($options['image']) { ?>
                            <img class="homepage-image" src="<?php echo $base_url ?>/<?php echo $options['image'];?>" alt="<?php echo $options['title'];?>">
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="hero-buttons container-fluid">
            <div class="container">
                <div class="row">
                    <div class="text-center span12">
                        <?php if ($options['repo']) { ?>
                            <a href="https://github.com/<?php echo $options['repo']; ?>" class="btn btn-secondary btn-hero">
                                View On GitHub
                            </a>
                        <?php } ?>
                        <?php if (count($options['languages']) > 0) { ?>
                            <?php foreach ($options['languages'] as $language_key => $language_name) { ?>
                            <a href="<?php echo $base_url . "/" . $language_key . "/"; ?>" class="btn btn-primary btn-hero">
                                <?php echo $language_name; ?>
                            </a>
                            <?php } ?>
                        <?php } else { ?>
                        <a href="<?php echo $docs_url;?>" class="btn btn-primary btn-hero">
                            View Documentation
                        </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="homepage-content container-fluid">
            <div class="container">
                <div class="row">
                    <div class="span10 offset1">
                        <?php echo $page['html'];?>
                    </div>
                </div>
            </div>
        </div>

        <div class="homepage-footer well container-fluid">
            <div class="container">
                <div class="row">
                    <div class="span5 offset1">
                        <?php if (!empty($options['links'])) { ?>
                            <ul class="footer-nav">
                                <?php foreach($options['links'] as $name => $url) { ?>
                                    <li><a href="<?php echo $url;?>" target="_blank"><?php echo $name;?></a></li>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                    </div>
                    <div class="span5">
                        <div class="pull-right">
                            <?php if (!empty($options['twitter'])) { ?>
                                <?php foreach($options['twitter'] as $handle) { ?>
                                    <div class="twitter">
                                        <iframe allowtransparency="true" frameborder="0" scrolling="no" style="width:162px; height:20px;" src="https://platform.twitter.com/widgets/follow_button.html?screen_name=<?php echo $handle;?>&amp;show_count=false"></iframe>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <!-- Docs -->
        <?php if ($options['repo']) { ?>
            <a href="https://github.com/<?php echo $options['repo']; ?>" target="_blank" id="github-ribbon"><img src="https://s3.amazonaws.com/github/ribbons/forkme_right_darkblue_121621.png" alt="Fork me on GitHub"></a>
        <?php } ?>
        <div class="container-fluid fluid-height wrapper">
            <div class="navbar navbar-fixed-top">
                <div class="navbar-inner">
                    <a class="brand pull-left" href="<?php echo $base_url ?><?php echo $homepage_url;?>"><?php echo $options['title']; ?></a>
                    <p class="navbar-text pull-right">
                        Generated by <a href="http://daux.io">Daux.io</a>
                    </p>
                </div>
            </div>

            <div class="row-fluid columns content">
                <div class="left-column article-tree span3">
                    <!-- For Mobile -->
                    <div class="responsive-collapse">
                        <button type="button" class="btn btn-sidebar" id="menu-spinner-button">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>
                    <div id="sub-nav-collapse" class="sub-nav-collapse">
                        <!-- Navigation -->
                        <?php echo build_nav($tree, $url_params); ?>

                        <?php if (!empty($options['links']) || !empty($options['twitter'])) { ?>
                            <div class="well well-sidebar">
                                <!-- Links -->
                                <?php foreach($options['links'] as $name => $url) { ?>
                                    <a href="<?php echo $url;?>" target="_blank"><?php echo $name;?></a><br>
                                <?php } ?>
                                <!-- Twitter -->
                                <?php foreach($options['twitter'] as $handle) { ?>
                                    <div class="twitter">
                                                <hr/>
                                        <iframe allowtransparency="true" frameborder="0" scrolling="no" style="width:162px; height:20px;" src="https://platform.twitter.com/widgets/follow_button.html?screen_name=<?php echo $handle;?>&amp;show_count=false"></iframe>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="right-column <?php echo ($options['float']?'float-view':''); ?> content-area span9">
                    <div class="content-page">
                        <article>
                            <?php if($options['date_modified'] && isset($page['modified'])) { ?>
                                <div class="page-header sub-header clearfix">
                                    <h1><?php echo $page['title'];?>
                                        <?php if($options["file_editor"]) { ?>
                                            <a href="javascript:;" id="editThis" class="btn">Edit this page</a>
                                        <?php } ?>
                                    </h1>
                                        <span style="float: left; font-size: 10px; color: gray;">
                                            <?php echo date("l, F j, Y", $page['modified']);?>
                                        </span>
                                        <span style="float: right; font-size: 10px; color: gray;">
                                            <?php echo date ("g:i A", $page['modified']);?>
                                        </span>
                                </div>
                            <?php } else { ?>
                                <div class="page-header">
                                    <h1><?php echo $page['title'];?>
                                        <?php if($options["file_editor"]) { ?>
                                            <a href="javascript:;" id="editThis" class="btn">Edit this page</a>
                                        <?php } ?>
                                    </h1>
                                </div>
                            <?php } ?>
                            <?php echo $page['html'];?>
                            <?php if($options["file_editor"]) { ?>
                                <div class="editor <?php if(!$options['date_modified']) { ?>paddingTop<?php } ?>">
                                    <h3>You are editing <?php echo $page['path']; ?>&nbsp;<a href="javascript:;" class="closeEditor btn btn-warning">Close</a></h3>
                                    <div class="navbar navbar-inverse navbar-default navbar-fixed-bottom" role="navigation">
                                        <div class="navbar-inner">
                                            <a href="javascript:;" class="save_editor btn btn-primary navbar-btn pull-right">Save file</a>
                                        </div>
                                    </div>
                                    <textarea id="markdown_editor"><?php echo $page['markdown'];?></textarea>
                                    <div class="clearfix"></div>
                                </div>
                            <?php } ?>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

<?php if ($options['google_analytics']) { ?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', '<?php echo $options['google_analytics'];?>', '<?php echo $_SERVER['HTTP_HOST'];?>');
  ga('send', 'pageview');

</script>
<?php } ?>
<?php if ($options['piwik_analytics']) { ?>
<script type="text/javascript">
  var _paq = _paq || [];
  _paq.push(["trackPageView"]);
  _paq.push(["enableLinkTracking"]);

  (function() {
    var u=(("https:" == document.location.protocol) ? "https" : "http") + "://<?php echo $options['piwik_analytics'];?>/";
    _paq.push(["setTrackerUrl", u+"piwik.php"]);
    _paq.push(["setSiteId", <?php echo $options['piwik_analytics_id'];?>]);
    var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";
    g.defer=true; g.async=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);
  })();
</script>
<?php } ?>
</body>
</html>
