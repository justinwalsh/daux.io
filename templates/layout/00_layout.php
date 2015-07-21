<!DOCTYPE html>
<!--[if lt IE 7]>       <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>          <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>          <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!-->  <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <title><?php echo $page['title']; ?></title>
    <meta name="description" content="<?php echo $params['tagline'];?>" />
    <meta name="author" content="<?php echo $params['author']; ?>">
    <meta charset="UTF-8">
    <link rel="icon" href="<?php echo $params['theme']['favicon']; ?>" type="image/x-icon">
    <!-- Mobile -->
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Font -->
    <?php foreach ($params['theme']['fonts'] as $font) echo "<link href='$font' rel='stylesheet' type='text/css'>"; ?>

    <!-- CSS -->
    <?php foreach ($params['theme']['css'] as $css) echo "<link href='$css' rel='stylesheet' type='text/css'>"; ?>

    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>
    <?= $this->section('content'); ?>

    <?php if ($params['google_analytics']) {
        $this->insert('theme::partials/google_analytics', ['analytics' => $params['google_analytics'], 'host' => array_key_exists('host', $params)? $params['host'] : '']);
    } ?>
    <?php if ($params['piwik_analytics']) {
        $this->insert('theme::partials/piwik_analytics', ['url' => $params['piwik_analytics'], 'id' => $params['piwik_analytics_id']]);
    } ?>


    <!-- jQuery -->
    <?php
    if ($params['theme']['require-jquery']) echo '<script src="' . $base_url . 'resources/js/jquery-1.11.0.min.js' . '"></script>';
    if ($params['theme']['bootstrap-js']) echo '<script src="' . $base_url . 'resources/js/bootstrap.min.js' . '"></script>';
    ?>

    <!-- hightlight.js -->
    <script src="<?php echo $base_url; ?>resources/js/highlight.min.js"></script>
    <script>hljs.initHighlightingOnLoad();</script>

    <!-- JS -->
    <?php foreach ($params['theme']['js'] as $js) echo '<script src="' . $js . '"></script>'; ?>

    <!-- Front end file editor -->
    <?php if ($params['file_editor']) echo '<script src="'. $base_url. 'resources/js/editor.js"></script>'; ?>
    <script src="<?php echo $base_url; ?>resources/js/custom.js"></script>
</body>
</html>
