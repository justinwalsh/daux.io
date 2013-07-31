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