/*----cascading image loop slide -----*/
(function($) {
    'use strict';
	$(document).ready(function() {
		cascading_slide_show_image();
		cascading_overflow();
	});
	$(window).on("load resize",function() {
		cascading_overflow();
	});
})(jQuery);
function cascading_slide_show_image(){
	'use strict';
	var $=jQuery;
	$(".slide_show_image").length && $(".slide_show_image").each(function() {
		var t = $(this),uid1=t.data("uid");
		var uid=$('.'+uid1);
		$('.'+uid1+'.slide_show_image .cascading-image:last').addClass('active');
		var  i = t.find(".cascading-image"),opt=t.data("play");
		$('.'+uid1+" .cascading-image").each(function() {
			var o = $(this);
			if(opt=='onclick'){
				o.on('click',function() {
					if (!i.last().is(o))
					return o.addClass("out").animate({
						opacity: 0.7,
						}, 200, function() {
						o.detach(),
						o.insertAfter(i.last()).animate({
							opacity: 1,
							}, 500, function() {
							o.removeClass("out")
						}),
						i = t.find(".cascading-image"),
						i.removeClass('active'),
						o.addClass("active");
					}),
					!1
				});
				}else{
				var time=$('.'+uid1).data('interval_time');
				setInterval(function () {
					var current = $('.'+uid1+'.slide_show_image .cascading-image.active').removeClass('active'),
					o = current.next().length ? current.next() : current.siblings().filter(':first');
					o.addClass('active');
					if (!i.last().is(o))
					return o.addClass("out").animate({
						opacity: 0.7,
						}, 200, function() {
						o.detach(),
						o.insertAfter(i.last()).animate({
							opacity: 1,
							}, 500, function() {
							o.removeClass("out")
						}),
						i = t.find(".cascading-image"),
						i.removeClass('active'),
						o.addClass("active");
					}),
					!1
				}, time);
				o.on('click',function() {
					if (!i.last().is(o))
					return o.addClass("out").animate({
						opacity: 0.7,
						}, 200, function() {
						o.detach(),
						o.insertAfter(i.last()).animate({
							opacity: 1,
							}, 500, function() {
							o.removeClass("out")
						}),
						i = t.find(".cascading-image"),
						i.removeClass('active'),
						o.addClass("active");
					}),
					!1
				});
			}
		})
	});
}
function cascading_overflow(){
	'use strict';
	var $=jQuery;
	$(".cascading-block").length && $(".cascading-block").each(function() {
		var width = window.innerWidth;
		var cadcading_overflow=$(this);
		var cadcading_overflow_desktop=$(this).data('overflow-desktop');
		var cadcading_overflow_tablet=$(this).data('overflow-tablet');
		var cadcading_overflow_mobile=$(this).data('overflow-mobile');
		if(cadcading_overflow_desktop=='yes'  && width > 991){
			cadcading_overflow.closest("section.elementor-element").css("overflow","hidden");	
		}else if(cadcading_overflow_tablet=='yes' && (width <= 991 && width > 600)){
			cadcading_overflow.closest("section.elementor-element").css("overflow","hidden");	
		}else if(cadcading_overflow_mobile=='yes' && width <= 600){
			cadcading_overflow.closest("section.elementor-element").css("overflow","hidden");	
		}else{
			cadcading_overflow.closest("section.elementor-element").css("overflow","visible");
		}
	});
}