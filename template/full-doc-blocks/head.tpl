<!DOCTYPE html>
<html>
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
		<link rel="stylesheet" href="<?php echo $base_url ?>/css/daux-<?php echo $options['theme'];?>.min.css">
	<?php } ?>
	<link rel="stylesheet" href="<?php echo $base_url ?>/css/full-doc.css">

	<!-- hightlight.js -->
	<script src="<?php echo $base_url ?>/js/highlight.min.js"></script>
	<script>hljs.initHighlightingOnLoad();</script>

	<!-- Navigation -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script>
	if (typeof jQuery == 'undefined') {
		document.write(unescape("%3Cscript src='<?php echo $base_url ?>/js/jquery-1.11.0.min.js' type='text/javascript'%3E%3C/script%3E"));
	}
	</script>
	<script src="<?php echo $base_url ?>/js/bootstrap.min.js"></script>
	<script src="<?php echo $base_url ?>/js/custom.js"></script>
	<!--[if lt IE 9]>
	<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body>