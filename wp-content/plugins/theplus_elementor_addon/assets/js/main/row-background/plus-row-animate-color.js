/*--- animated background color ---*/
;(function($, window, document, undefined) {
	"use strict";
	$.fn.animatedBG = function(options){
		var defaults = {
				colorSet: ['#ef008c', '#00be59', '#654b9e', '#ff5432', '#00d8e6'],
				speed: 3000
			},
			settings = $.extend({}, defaults, options);

		return this.each(function(){
			var $this = $(this);

			$this.each(function(){
				var $el = $(this),
					colors = settings.colorSet;
				
				function shiftColor() {
					var color = colors.shift();
					colors.push(color);
					return color;
				}

				// initial color
				var initColor = shiftColor();
				$el.css('backgroundColor', initColor);
				setInterval(function(){
					var color = shiftColor();
					$el.animate({backgroundColor: color}, 3000);
				}, settings.speed);
			});
		});
	};
	$(function(){
		$(document).ready(function() {
		$(".row-animated-bg").each(function() {
			var data_id= $(this).data('id');
			var data_time=$(this).data('bg-time');
			var colors =$(this).data('bg');
			$('.'+data_id).animatedBG({
				colorSet: colors,
				speed: data_time
			});
		});
		});
	});
}(jQuery, window, document));
/*--- animated background color ---*/