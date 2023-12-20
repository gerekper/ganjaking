/*slick carousel*/( function( $ ) {
	"use strict";
	var WidgetThePlusHandler = function ($scope, $) {
		var wid_sec=$scope.parents('section.elementor-element,.e-container,.e-con');
		if(wid_sec.find('.list-carousel-slick').length>0){
			var carousel_elem = $scope.find('.list-carousel-slick').eq(0);
				if (carousel_elem.length > 0) {
					if(!carousel_elem.hasClass("done-carousel")){
						theplus_carousel_list();
					}
				}
		}
		};
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/global', WidgetThePlusHandler);
	});
})(jQuery);
function theplus_carousel_list(data_widget=''){	
	var $=jQuery;
	$('.list-carousel-slick').each(function() {
			var $self=$(this);
            //flexbox mobile
            var responsive_Device = $("body").data("elementor-device-mode");
            if("mobile" === responsive_Device){
                $self.closest('.e-container,.e-con').css('flex-wrap','unset');
            }
			var $uid=$self.data("id");
			var slider_direction=$self.data("slider_direction");
			var slide_speed=$self.data("slide_speed");
			var default_active_slide=$self.data("default_active_slide");
			var slider_desktop_column=$self.data("slider_desktop_column");
			var steps_slide=$self.data("steps_slide");
			var slide_fade_inout=$self.data("slide_fade_inout");
			var slider_padding=$self.data("slider_padding");
			
			var slider_draggable=$self.data("slider_draggable");
			var multi_drag=$self.data("multi_drag");
			var slider_infinite=$self.data("slider_infinite");
			var slider_pause_hover=$self.data("slider_pause_hover");
			var slider_adaptive_height=$self.data("slider_adaptive_height");
			var slider_animation=$self.data("slider_animation");
			
			var slider_autoplay=false;
			if(!elementorFrontend.isEditMode()){
				var slider_autoplay=$self.data("slider_autoplay");                
			}

			var autoplay_speed=$self.data("autoplay_speed");
			var slider_rows=$self.data("slider_rows");
			
			var slider_center_mode=$self.data("slider_center_mode");
			var center_padding=$self.data("center_padding");
			var scale_center_slide=$self.data("scale_center_slide");
			var scale_normal_slide=$self.data("scale_normal_slide");
			var opacity_normal_slide=$self.data("opacity_normal_slide");
			
			var slider_dots=$self.data("slider_dots");
			var slider_dots_style=$self.data("slider_dots_style");
			
			var slider_arrows=$self.data("slider_arrows");
			var slider_arrows_style=$self.data("slider_arrows_style");
			var arrows_position=$self.data("arrows_position");
			
			var slider_responsive_tablet=$self.data("slider_responsive_tablet");
			var slider_tablet_column=$self.data("slider_tablet_column");
			var tablet_steps_slide=$self.data("tablet_steps_slide");
			var tablet_center_mode=$self.data("tablet_center_mode");
			var tablet_center_padding=$self.data("tablet_center_padding");
			var tablet_slider_draggable=$self.data("tablet_slider_draggable");
			var tablet_slider_infinite=$self.data("tablet_slider_infinite");
			var tablet_slider_autoplay=$self.data("tablet_slider_autoplay");
			var tablet_autoplay_speed=$self.data("tablet_autoplay_speed");
			var tablet_slider_dots=$self.data("tablet_slider_dots");
			var tablet_slider_arrows=$self.data("tablet_slider_arrows");
			var tablet_slider_rows=$self.data("tablet_slider_rows");
			var tablet_center_mode=$self.data("tablet_center_mode");
			var tablet_center_padding=$self.data("tablet_center_padding");
			
			
			var slider_responsive_mobile=$self.data("slider_responsive_mobile");
			var mobile_slider_draggable=$self.data("mobile_slider_draggable");
			var mobile_slider_infinite=$self.data("mobile_slider_infinite");
			var mobile_slider_autoplay=$self.data("mobile_slider_autoplay");
			var mobile_autoplay_speed=$self.data("mobile_autoplay_speed");
			
			
			var slider_mobile_column=$self.data("slider_mobile_column");
			var mobile_steps_slide=$self.data("mobile_steps_slide");
			var mobile_center_mode=$self.data("mobile_center_mode");
			var mobile_center_padding=$self.data("mobile_center_padding");
			var mobile_slider_dots=$self.data("mobile_slider_dots");
			var mobile_slider_arrows=$self.data("mobile_slider_arrows");
			var mobile_slider_rows=$self.data("mobile_slider_rows");
			var mobile_center_mode=$self.data("mobile_center_mode");
			var mobile_center_padding=$self.data("mobile_center_padding");
			
			var testimonial_style=$self.data("testimonial-style");
			var slide_mouse_scroll=$self.data("slide_mouse_scroll");
			
			let data = $self[0].dataset;
			parsedData =  data && data.result ? JSON.parse(data.result) : '';

			var getDIrection = parsedData.carousel_direction,
				rtlVal = false;
			if( 'rtl' === getDIrection ){
				rtlVal = true;
			}

			if(steps_slide=='1'){
				steps_slide=='1';
			}else{
				steps_slide=slider_desktop_column;
			}
			if(tablet_steps_slide=='1'){
				tablet_steps_slide=='1';
			}else{
				tablet_steps_slide=slider_tablet_column;
			}
			if(slider_responsive_tablet!='yes'){
				tablet_slider_draggable=slider_draggable;
				tablet_slider_infinite=slider_infinite;
				tablet_slider_autoplay=slider_autoplay;
				tablet_autoplay_speed=autoplay_speed;
				tablet_slider_dots=slider_dots;
				tablet_slider_arrows=slider_arrows;
				tablet_slider_rows=slider_rows;
				tablet_center_mode=slider_center_mode;
				tablet_center_padding=center_padding;
			}
			if(slider_responsive_mobile!='yes'){
				mobile_slider_draggable=slider_draggable;
				mobile_slider_infinite=slider_infinite;
				mobile_slider_autoplay=slider_autoplay;
				mobile_autoplay_speed=autoplay_speed;
				mobile_slider_dots=slider_dots;
				mobile_slider_arrows=slider_arrows;
				mobile_slider_rows=slider_rows;
				mobile_center_mode=slider_center_mode;
				mobile_center_padding=center_padding;
			}
			
            if( slider_responsive_mobile == 'yes' ){
                var getdevice = jQuery('body').data('elementor-device-mode');
                if( getdevice == 'mobile'){
                    slider_rows = mobile_slider_rows;
                }
            }

			if(mobile_steps_slide=='1'){
				mobile_steps_slide=='1';
			}else{
				mobile_steps_slide=slider_mobile_column;
			}
			
			if(slider_arrows_style=='style-1'){
				var prev_arrow='<button role="tab" type="button" class="slick-nav slick-prev '+slider_arrows_style+'">Previous</button>';
				var next_arrow='<button role="tab" type="button" class="slick-nav slick-next '+slider_arrows_style+'">Next</button>';
			}
			
			if(slider_arrows_style=='style-2'){
				var prev_arrow='<button role="tab" type="button" class="slick-nav slick-prev '+slider_arrows_style+'"><span class="icon-wrap"></span>Previous</button>';
				var next_arrow='<button role="tab" type="button" class="slick-nav slick-next '+slider_arrows_style+'"><span class="icon-wrap"></span>Next</button>';
			}
			if(slider_arrows_style=='style-3' || slider_arrows_style=='style-4' ){
				var prev_arrow='<button role="tab" type="button" class="slick-nav slick-prev '+slider_arrows_style+' '+arrows_position+'">Previous</button>';
				var next_arrow='<button role="tab" type="button" class="slick-nav slick-next '+slider_arrows_style+' '+arrows_position+'">Next</button>';
			}
			if(slider_arrows_style=='style-5'){
				var prev_arrow='<button role="tab" type="button" class="slick-nav slick-prev '+slider_arrows_style+'"><span class="icon-wrap"></span>Previous</button>';
				var next_arrow='<button role="tab" type="button" class="slick-nav slick-next '+slider_arrows_style+'"><span class="icon-wrap"></span>Next</button>';
			}
			if(slider_arrows_style=='style-6'){
				var prev_arrow='<button role="tab" type="button" class="slick-nav slick-prev '+slider_arrows_style+'"><span class="icon-wrap"><i class="fas fa-long-arrow-alt-left" aria-hidden="true"></i></span>Previous</button>';
				var next_arrow='<button role="tab" type="button" class="slick-nav slick-next '+slider_arrows_style+'"><span class="icon-wrap"><i class="fas fa-long-arrow-alt-right" aria-hidden="true"></i></span>Next</button>';
			}
			
			if(default_active_slide==undefined){
				default_active_slide=0;
			}
			

			var args = {dots: slider_dots,
					vertical: slider_direction,	
					fade:slide_fade_inout,
					arrows: slider_arrows,
					infinite: slider_infinite,										
					speed: slide_speed,
					initialSlide: default_active_slide,
					adaptiveHeight: slider_adaptive_height,
					autoplay: slider_autoplay,
					cssEase: slider_animation,
					autoplaySpeed: autoplay_speed,
					pauseOnHover: slider_pause_hover,
					centerMode: slider_center_mode,
					centerPadding: center_padding+'px',
					prevArrow: prev_arrow,
					nextArrow: next_arrow,
					slidesToShow: slider_desktop_column,
					slidesToScroll: steps_slide,
					draggable:slider_draggable,
					swipeToSlide:multi_drag,
					dotsClass:slider_dots_style,
					rows : slider_rows,
					rtl: rtlVal,
			}
            
			if(!$(this).hasClass("done-carousel") && !$self.hasClass('theplus-insta-grid')){
				$('> .post-inner-loop',this).slick(args);
				// setTimeout(function(){
					$(".slick-dots.style-2 li").each(function(){
						if($(this).find("svg").length==0){
							$(this).append('<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 16 16" preserveAspectRatio="none"><circle cx="8" cy="8" r="6.215"></circle></svg>');
						}
					});
				// }, 1000);
				$(this).addClass("done-carousel");
				if(slide_mouse_scroll==true && slide_mouse_scroll!=undefined){
					
					$('.'+$uid+' > .post-inner-loop').mousewheel(function(e) {
						e.preventDefault();
						if (e.deltaY < 0) {
							$('.'+$uid+' > .post-inner-loop').slick("slickNext");
							} else {
							$('.'+$uid+' > .post-inner-loop').slick("slickPrev");
						}
					});
				}
			}else if(!$(this).hasClass("done-carousel") && $self.hasClass('theplus-insta-grid') && data_widget!='' && data_widget=='instagram'){
				if($('.'+$uid+' > .post-inner-loop').find('.theplus-insta-feed').length > 0){
					$('> .post-inner-loop',this).slick(args);
					$(this).addClass("done-carousel");				
					setTimeout(function(){
						$(".slick-dots.style-2 li").each(function(){
							if($(this).find("svg").length==0){
								$(this).append('<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 16 16" preserveAspectRatio="none"><circle cx="8" cy="8" r="6.215"></circle></svg>');
							}
						});
					}, 1000);
				}
			}
		
			var carousel_bg_conn = $(this).data("carousel-bg-conn");
			if(carousel_bg_conn !='' && carousel_bg_conn!= undefined){
				if($("#"+carousel_bg_conn).length > 0){
					$('> .post-inner-loop',this).on('beforeChange', function(event, slick, currentSlide, nextSlide){
						var active_bg = $("#"+carousel_bg_conn+' .bg-carousel-slide');
						active_bg.removeClass('bg-active-slide');
						if(active_bg.length > nextSlide){
							active_bg.eq(nextSlide).addClass('bg-active-slide');
						}else{
							var bg_total = active_bg.length;
							var c = nextSlide % bg_total;
							active_bg.eq(c).addClass('bg-active-slide');
						}
					});
				}
			}
			
			var connection = $(this).data("connection");
			if(connection !='' && connection!= undefined && $("#"+connection).length > 0){
				$('> .post-inner-loop',this).on('beforeChange', function(event, slick, currentSlide, nextSlide){
					
					if($('.theplus-carousel-remote .tp-carousel-dots').length){
						if($('.theplus-carousel-remote').data("connection") === connection){
							$(".tp-carousel-dots").find(".tp-carodots-item").removeClass('active default-active').addClass('inactive');
							var inc = nextSlide + 1;
							$(".tp-carousel-dots").find(".tp-carodots-item:nth-child("+inc+")").addClass('active');
						}						
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
				});
			}
			
			
			if((slider_center_mode===true && slider_center_mode !=undefined) || (mobile_center_mode===true && mobile_center_mode !=undefined) || (tablet_center_mode===true && tablet_center_mode !=undefined)){
				$('> .post-inner-loop',this).on('beforeChange', function (event, slick, currentSlide, nextSlide) {
					var 
						direction,
						slideCountZeroBased = slick.slideCount - 1;

					if (nextSlide == currentSlide) {
						direction = "same";

					} else if (Math.abs(nextSlide - currentSlide) == 1) {
						direction = (nextSlide - currentSlide > 0) ? "right" : "left";

					} else {
						direction = (nextSlide - currentSlide > 0) ? "left" : "right";
					}
					
					if (direction == 'right') {
						$('.slick-cloned[data-slick-index="' + (nextSlide + slideCountZeroBased + 1) + '"]', this).addClass('scc-animate');
					}

					if (direction == 'left') {
						$('.slick-cloned[data-slick-index="' + (nextSlide - slideCountZeroBased - 1) + '"]', this).addClass('scc-animate');
					}
				});

				$('> .post-inner-loop',this).on('afterChange', function (event, slick, currentSlide, nextSlide) {
					$('.scc-animate', this).removeClass('scc-animate');
					$('.scc-animate', this).removeClass('scc-animate');
				});
			}
	});
}

function accordion_tabs_connection(tab_index,connection){	
	var $=jQuery;
	if(connection!='' && $("."+connection).length==1){
		var current=$('.'+connection+' > .post-inner-loop').slick('slickCurrentSlide');
		if(current!=(tab_index-1)){
			$('.'+connection+' > .post-inner-loop').slick('slickGoTo', tab_index-1);
		}
	}
}