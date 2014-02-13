$(function() {
	$('.aj-nav').click(function(e) {
		e.preventDefault();
		$(this).parent().siblings().find('ul').slideUp();
		$(this).next().slideToggle();
	});

	// Bootstrap Table Class
	$('table').addClass('table');

	// Responsive menu spinner
	$('#menu-spinner-button').click(function() {
		$('#sub-nav-collapse').slideToggle();
	});

	// Catch browser resize
	$(window).resize(function() {
		// Remove transition inline style on large screens
		if ($(window).width() >= 768)
			$('#sub-nav-collapse').removeAttr('style');
	});
});

//Fix GitHub Ribbon overlapping Scrollbar
if ($('article')[0].scrollHeight > $('.right-column').height()) $('#github-ribbon')[0].style.right = '16px'

//Toggle Code Block Visibility
function toggleCodeBlocks() {
    var t = localStorage.getItem("toggleCodeStats")
    t = (t + 1) % 3;
    localStorage.setItem("toggleCodeStats", t);
    var a = $('.content-page article');
    var b = a.children();
    var c = b.filter('pre');
    var d = $('.right-column');
    if (d.hasClass('float-view')) {
        d.removeClass('float-view');
        $('#toggleCodeBlockBtn')[0].innerHTML = "Hide Code Blocks";
    } else {
        if (a.hasClass('hideCode')) {
            d.addClass('float-view');
            a.removeClass('hideCode');
            b.removeClass('hideCode2');
            c.removeClass('hideCode3');
            $('#toggleCodeBlockBtn')[0].innerHTML = "Show Code Blocks Inline";
        } else {
            a.addClass('hideCode');
            b.addClass('hideCode2');
            c.addClass('hideCode3');
            $('#toggleCodeBlockBtn')[0].innerHTML = "Show Code Blocks";
        }
    }
}

if (localStorage.getItem("toggleCodeStats") >= 0) {
    var t = localStorage.getItem("toggleCodeStats");
    if (t == 1) {
        toggleCodeBlocks();
        localStorage.setItem("toggleCodeStats", 1);
    }
    if (t == 2) {
        toggleCodeBlocks();
        toggleCodeBlocks();
        localStorage.setItem("toggleCodeStats", 2);
    }
} else {
    localStorage.setItem("toggleCodeStats", 0);
}