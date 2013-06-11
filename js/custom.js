$(function() {
	$('.aj-nav').click(function(e) {
		e.preventDefault();
		$(this).parent().siblings().find('ul').slideUp();
		$(this).next().slideToggle();
	});

	// Bootstrap Table Class
	$('table').addClass('table');
});