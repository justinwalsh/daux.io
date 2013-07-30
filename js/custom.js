$(function() {
	$('.aj-nav').click(function(e) {
		e.preventDefault();
		$(this).parent().siblings().find('ul').slideUp();
		$(this).next().slideToggle();
	});

	// Bootstrap Table Class
	$('table').addClass('table');

	var mobileNavWorkaround = function() {
		if($(window).width()< 768) {
			$('#sub-nav-collapse').css('height', '0px');
		} else {
			$('#sub-nav-collapse').css('height', 'auto');
		}
	}

	$(window).resize(function() {
		mobileNavWorkaround();
	});
	mobileNavWorkaround();
});