<?
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once('libs/functions.php');
$options = get_options();
$tree = get_tree("docs");
?>
<!DOCTYPE html>
<html>
<head>
	<!-- Bootstrap -->
	<link rel="stylesheet" href="/libs/themes/<?=$options['theme'];?>.css">
	<style type="text/css">
		html, body {
		    margin: 0;
		    padding: 0;
		    height: 100%;
		}

		.container-fluid {
		    margin: 0 auto;
		    height: 100%;
		    padding: 0;

		    -moz-box-sizing: border-box;
		    -webkit-box-sizing: border-box;
		    box-sizing: border-box;
		}

		.columns {
		    height: 100%;
		}

		.content-area, .article-tree {
		    overflow:auto;
		    height: 100%;
		}

		li ul {
			display: none;
		}

		li.open > ul {
			display: block;
		}

		a.folder {
			font-weight: bold;
		}

		pre {
			padding: 0;
		}

		table {
			width: 100%;
		}

		.footer {
			position: fixed;
			bottom:0;
			left: 0;
			padding: 15px;
		}
	</style>

	<!-- hightlight.js -->
	<link rel="stylesheet" href="http://yandex.st/highlightjs/7.3/styles/<?=$options['hightlight'];?>.min.css">
	<script src="http://yandex.st/highlightjs/7.3/highlight.min.js"></script>
	<script>hljs.initHighlightingOnLoad();</script>

	<!-- Navigation -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.0/jquery.min.js"></script>
	<script type='text/javascript'>
		$(function() {
			$('.aj-nav').click(function(e) {
				e.preventDefault();
				$(this).parent().siblings().find('ul').slideUp();
				$(this).next().slideToggle();
				// $(this).parent().siblings().removeClass('open');
				// $(this).parent().addClass('open');
			});
		});
	</script>
</head>
<body>
	<div class="container-fluid wrapper">
		<div class="navbar navbar-static-top">
			<div class="navbar-inner">
				<a class="brand" href="/"><?=$options['title']; ?></a>
			</div>
		</div>

		<div class="row-fluid columns content">
			<div class="span2 article-tree">
				<? echo build_nav($tree); ?>
				<div class="footer">Generate by <a href="https://github.com/justinwalsh/tm-docs">TM-Docs</a></div>
			</div>
			<div class="span10 content-area">
				<? echo load_page($tree); ?>
			</div>
		</div>
	</div>
</body>
</html>