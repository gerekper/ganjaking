/*Carousel Anything*/( function( $ ) {
	"use strict";
	var WidgetCarouselAnythingHandler = function ($scope, $) {
		$(document).ready(function() {
		var $target = $('.theplus-carousel-anything-wrapper', $scope);
		
			if($target.length){
				var uid=$target.data("id");
				
				$('.'+uid+' > .post-inner-loop').on('beforeChange', function(e, slick, currentSlide, nextSlide) {
					if(currentSlide!=nextSlide){
						var $animatingElements = $('.grid-item.slick-slide:not(.slick-active)').find('.animate-general');
						if($animatingElements.length){
							$animatingElements.each(function() {
								var p = $(this);
								p.removeClass("animation-done");
								p.css("opacity","0");
							});
						}
					}
					if($('.'+uid).data("connection")!='' && $('.'+uid).data("connection")!=undefined){
						var connection= $('.'+uid).data("connection");
						
						if(!$("#"+connection).find('.plus-accordion-header[data-tab="'+parseInt(nextSlide+1)+'"]').hasClass("active")){
							$("#"+connection).find('.plus-accordion-header[data-tab="'+parseInt(nextSlide+1)+'"]').trigger("click");
							$("#"+connection).find('.plus-accordion-header[data-tab="'+parseInt(nextSlide+1)+'"]').trigger("mouseenter");
						}
						if(!$("#"+connection).find('li .plus-tab-header[data-tab="'+parseInt(nextSlide+1)+'"]').hasClass("active")){
							$("#"+connection).find('li .plus-tab-header[data-tab="'+parseInt(nextSlide+1)+'"]').trigger("click");
							$("#"+connection).find('li .plus-tab-header[data-tab="'+parseInt(nextSlide+1)+'"]').trigger("mouseenter");
						}
						
						if(!$("#"+connection).find('.tp-carodots-item[data-tab="'+parseInt(nextSlide)+'"]').hasClass("active")){
							$("#"+connection).find('.tp-carodots-item[data-tab="'+parseInt(nextSlide)+'"]').trigger("click");
						}
						
						if( $("#"+connection).find('.carousel-pagination')){
							var ctab = nextSlide + 1;
							$("#"+connection).find(".carousel-pagination ul.pagination-list li.pagination-list-in.active").html('0'+ctab);
						}
						
						if(!$("#"+connection).find('.tp-process-steps-wrapper[data-index="'+parseInt(nextSlide)+'"]').hasClass("active")){
							var con__event = $("#"+connection).data("eventtype");
							if(con__event=='con_pro_click'){							
								$("#"+connection).find('.tp-process-steps-wrapper[data-index="'+parseInt(nextSlide)+'"]').trigger("click");
							}else if(con__event=='con_pro_hover'){
								$("#"+connection).find('.tp-process-steps-wrapper[data-index="'+parseInt(nextSlide)+'"]').trigger("hover");
							}
							$("#"+connection).find('.tp-process-steps-wrapper').removeClass("active");
							$("#"+connection).find('.tp-process-steps-wrapper[data-index="'+parseInt(nextSlide)+'"]').addClass("active");
						}
						if(!$("#"+connection).find('.info-box-inner[data-slick-index="'+parseInt(nextSlide)+'"]').hasClass("tp-info-active")){
							var con__event = $("#"+connection).data("eventtype");					
							if(con__event=='con_pro_click'){
								$("#"+connection).find('.info-box-inner[data-slick-index="'+parseInt(nextSlide)+'"]').trigger("click");
							}else if(con__event=='con_pro_hover'){
								$("#"+connection).find('.info-box-inner[data-slick-index="'+parseInt(nextSlide)+'"]').trigger("hover");
							}
							
							if ( $.isFunction($.fn.plus_infobox_connection) ) {
								plus_infobox_connection(parseInt(nextSlide),connection);
							}						
							
							$("#"+connection).find('.info-box-inner').removeClass("tp-info-active");
							$("#"+connection).find('.info-box-inner[data-slick-index="'+parseInt(nextSlide)+'"]').addClass("tp-info-active");
						}
					}
				});
				
				$('.'+uid+' > .post-inner-loop').on('afterChange', function(e, slick, currentSlide, nextSlide) {
					var $animatingElements = $('.grid-item.slick-slide.slick-active').find('.animate-general');	
					if($animatingElements.length){
						doAnimations($animatingElements);
					}
				});
				function doAnimations(elements) {
					elements.each(function() {
						var p = $(this);
						var delay_time=p.data("animate-delay");
						var duration_time=p.data("animate-duration");
						var d = p.data("animate-type");
						if(!p.hasClass("animation-done")){
							p.addClass("animation-done").velocity(d,{ delay: delay_time,duration: duration_time,display:'auto'});
						}
					});
				}
			}
		});
	};
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-carousel-anything.default', WidgetCarouselAnythingHandler);
	});
})(jQuery);