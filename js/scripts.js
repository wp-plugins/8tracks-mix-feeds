jQuery(document).ready(function($){
	
	$(".mix-feed-item-link h2").hide();
	
	$(".mix-feed-item-link").hover(
		function () {
			$(this).find("h2").fadeIn();
			$(this).siblings().css({
				'-webkit-opacity': 0.4,
				'-moz-opacity': 0.4,
				'opacity': 0.4
			});
		},
		
		function () {
			$(this).find("h2").fadeOut();
			$(this).siblings().css({
				'-webkit-opacity': 1,
				'-moz-opacity': 1,
				'opacity': 1
			});
		}
	);
	
	
	$(".mix-feed-item-link h2").hover(
		function () {
			$(this).find("h2").fadeIn();
			$(this).siblings().css({
				'-webkit-opacity': 0.4,
				'-moz-opacity': 0.4,
				'opacity': 0.4
			});
		},
		
		function () {
			$(this).find("h2").fadeOut();
			$(this).siblings().css({
				'-webkit-opacity': 1,
				'-moz-opacity': 1,
				'opacity': 1
			});
		}
	);

});