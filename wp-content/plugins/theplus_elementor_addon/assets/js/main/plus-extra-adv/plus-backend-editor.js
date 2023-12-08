( function( $ ) {
	'use strict';
	var WidgetThePlusHandlerBackEnd = function ($scope, $) {
		var wid_sec=$scope.parents('section.elementor-element,.elementor-element.e-container,.elementor-element.e-con');
		
		/*--- on load animation ----*/
		if(wid_sec.find(".animate-general").length){
				"use strict";
				$scope.find('.animate-general').each(function() {
					var c, p=$(this);
					if(!p.hasClass("animation-done")){
						if(p.find('.animated-columns').length){
							var b = $('.animated-columns',this);				
							var delay_time=p.data("animate-delay");
							
							c = p.find('.animated-columns');
							c.each(function() {
								if(!$(this).hasClass("animation-done")){
									$(this).css("opacity", "0");
								}
							});
							
							}else{			
							var b=$(this);
							var delay_time=b.data("animate-delay");
							
							if(b.data("animate-item")){
								c = b.find(b.data("animate-item"));
								c.each(function() {
									if(!$(this).hasClass("animation-done")){
										$(this).css("opacity", "0");
									}
								});
								}else{
								b.css("opacity", "0");
							}
						}
					}
				});
				
				var d = function() {
					$scope.find('.animate-general').each(function() {
						var c, d, p=$(this), e = "85%";
						var id=$(this).data("id");
						if(p.data("animate-columns")=="stagger"){
							var b = $('.animated-columns',this);
							var animation_stagger=p.data("animate-stagger");
							var delay_time=p.data("animate-delay");
							var out_delay_time=p.data("animate-out-delay");
							var duration_time=p.data("animate-duration");
							var out_duration_time=p.data("animate-out-duration");
							var d = p.data("animate-type");
							var o = p.data("animate-out-type");												
							var animate_offset = p.data("animate-offset");
							
							p.css("opacity","1");
							c = p.find('.animated-columns');
							p.waypoint(function(direction) {
								if( direction === 'down'){
									if(!c.hasClass("animation-done")){
										c.addClass("animation-done").removeClass("animation-out-done").velocity(d,{ delay: delay_time,duration: duration_time,display:'auto',stagger: animation_stagger});
									}
								}else if (direction === 'up' && o!='' && o!=undefined && !c.hasClass("animation-out-done")) {
									c.addClass("animation-out-done").removeClass("animation-done").velocity(o,{ delay: out_delay_time,duration: out_duration_time,display:'auto',stagger: animation_stagger});
								}
							}, { offset: animate_offset } );
							if(c){
								$('head').append("<style type='text/css'>."+id+" .animated-columns.animation-done{opacity:1;}</style>")
							}
							
							}else if(p.data("animate-columns")=="columns"){
							
							var b = $('.animated-columns',this);
							var delay_time=p.data("animate-delay");
							var out_delay_time=p.data("animate-out-delay");
							var d = p.data("animate-type");
							var o = p.data("animate-out-type");	
							var animate_offset = p.data("animate-offset");
							var duration_time=p.data("animate-duration");
							var out_duration_time=p.data("animate-out-duration");
							p.css("opacity","1");
							c = p.find('.animated-columns');
							c.each(function() {
								var bc=$(this);
								bc.waypoint(function(direction) {
									if( direction === 'down'){
										if(!bc.hasClass("animation-done")){
											bc.addClass("animation-done").removeClass("animation-out-done").velocity(d,{ delay: delay_time,duration: duration_time,drag:true,display:'auto'});
										}
									}else if (direction === 'up' && o!='' && o!=undefined && !bc.hasClass("animation-out-done")) {
										bc.addClass("animation-out-done").removeClass("animation-done").velocity(o,{ delay: out_delay_time,duration: out_duration_time,display:'auto'});
									}
								}, { offset: animate_offset } );
							});
							if(c){
								$('head').append("<style type='text/css'>."+id+" .animated-columns.animation-done{opacity:1;}</style>")
							}
							}else{
							var b = $(this);
							var delay_time=b.data("animate-delay");
							var out_delay_time=b.data("animate-out-delay");
							var duration_time=b.data("animate-duration");
							var out_duration_time=p.data("animate-out-duration");
							d = b.data("animate-type"),
							o = b.data("animate-out-type"),
							animate_offset = b.data("animate-offset"),
							b.waypoint(function(direction ) {
								if( direction === 'down'){
									if(!b.hasClass("animation-done")){
										b.addClass("animation-done").removeClass("animation-out-done").velocity(d, {delay: delay_time,duration: duration_time,display:'auto'});
									}
								}else if (direction === 'up' && o!='' && o!=undefined && !b.hasClass("animation-out-done")) {
									if(!b.hasClass("animation-out-done")){
										b.addClass("animation-out-done").removeClass("animation-done").velocity(o,{ delay: out_delay_time,duration: out_duration_time,display:'auto' });
									}
								}
							}, { offset: animate_offset } );
						}
					})
				},
				e = function() {
					$(".call-on-waypoint").each(function() {
						var c = $(this);
						c.waypoint(function() {
							c.trigger("on-waypoin")
							}, {
							triggerOnce: !0,
							offset: "bottom-in-view"
						})
					})
				};
				e(); d();
		}
		/*--- on load animation ----*/
		/*magic scroll */
		if(wid_sec.find(".magic-scroll").length){
				pt_plus_animateParalax();
		}
		/*magic scroll */
		/*mousemove parallax*/
		
		if(wid_sec.find(".pt-plus-move-parallax").length > 0){
			plus_mousemove_parallax();
		}
		/*mousemove parallax*/
		/*cascading Slide Show Image*/
		if(wid_sec.find(".cascading-block").length > 0){
			cascading_overflow();
		}
		if(wid_sec.find('.slide_show_image').length>0){
			cascading_slide_show_image();
		}
		/*cascading Slide Show Image*/
		/*Text Heading Animation*/
		if(wid_sec.find('.pt-plus-cd-headline').length>0){
			plus_heading_animation();
		}
		/*Text Heading Animation*/
		/* bg creative parallax */
		if(wid_sec.find('.creative-simple-img-parallax').length>0){
				plus_bgimage_scrollparallax();
		}
		/* bg creative parallax */
		/* animated svg */
		if(wid_sec.find('.pt_plus_row_bg_animated_svg').length>0){
			$(document).ready(function() {
				setTimeout(function(){
					$('.pt_plus_row_bg_animated_svg').pt_plus_animated_svg();
					$('body').find('.pt_plus_row_bg_animated_svg').attr('style', 'stroke:black');
				}, 100);
			});
		}
		if(wid_sec.find('.pt_plus_animated_svg').length>0 || wid_sec.find('.ts-hover-draw-svg').length>0){
			$(document).ready(function() {
				setTimeout(function(){
					$('.pt_plus_animated_svg,.ts-hover-draw-svg').pt_plus_animated_svg();
				}, 100);
			});
		}
		/* animated svg */
		//Metro layout
		if(wid_sec.find('.list-isotope-metro').length>0){
				var container=wid_sec.find('.list-isotope-metro');
				var uid=container.data("id");
				var columns=container.attr('data-metro-columns');
				var metro_style=container.attr('data-metro-style');
				theplus_backend_packery_portfolio(uid,columns,metro_style);
		}
		//Slick Carousel Layout
		if(wid_sec.find('.list-carousel-slick').length>0){
			var carousel_elem = $scope.find('.list-carousel-slick').eq(0);
			if (carousel_elem.length > 0) {
				if(!carousel_elem.hasClass("done-carousel")){
					theplus_carousel_list();
				}
			}
		}
		if(wid_sec.find('.theplus-contact-form').length){
			plus_cf7_form();
		}
		/*-video post---*/
		if(wid_sec.find('iframe').length>0 && typeof initFluidVids !== 'undefined' && $.isFunction(initFluidVids)){
			initFluidVids();
		}
		/*-video post ----*/
		/*tilt parallax*/
		if(wid_sec.find(".js-tilt").length){			
				$('.js-tilt').tilt();
		}
		/*tilt parallax*/
		/*Reveal animation*/
		if(wid_sec.find(".pt-plus-reveal").length){
			plus_reveal_animation();
		}
		/*Reveal animation*/
		
		if (wid_sec.find('img.tp-lazyload').length) {
			tp_lazy_load()
		}
		if(wid_sec.find(".lazy-background").length){
			var lazyBackgrounds = [].slice.call(document.querySelectorAll(".lazy-background"));

			if (lazyBackgrounds && "IntersectionObserver" in window && "IntersectionObserverEntry" in window && "intersectionRatio" in window.IntersectionObserverEntry.prototype) {
				let lazyBackgroundObserver = new IntersectionObserver(function(entries, observer) {
					entries.forEach(function(entry) {
						if (entry.isIntersecting) {
							entry.target.classList.remove("lazy-background");
							lazyBackgroundObserver.unobserve(entry.target);
						}
					});
				});

				lazyBackgrounds.forEach(function(lazyBackground) {
					lazyBackgroundObserver.observe(lazyBackground);
				});
			}
		}
		
		if(wid_sec.find(".columns-vimeo-bg").length){
			$('.columns-vimeo-bg iframe').each(function() {
				var $self = $(this)
					id = $self.attr('id');
				if (window.addEventListener) {
					window.addEventListener('message', onMessageReceived, false);
				} else {
					window.attachEvent('onmessage', onMessageReceived, false);
				}
		
				function onMessageReceived(e) {
					if(e.origin==='https://player.vimeo.com'){
						var data = JSON.parse(e.data);
						switch (data.event) {
							case 'ready':
								$self[0].contentWindow.postMessage('{"method":"play", "value":1}','https://player.vimeo.com');
								if($self.data('muted') && $self.data('muted') == '1') {
									$self[0].contentWindow.postMessage('{"method":"setVolume", "value":0}','https://player.vimeo.com');
								}
								var videoHolder = document.getElementById('wrapper-'+id);
								if(videoHolder && videoHolder.id){
									videoHolder.classList.remove('tp-loading');
								}
								break;
						}
					}
				}
			});
		}
	};
	$(window).on('elementor/frontend/init', function () {
		if (elementorFrontend.isEditMode()) {
			elementorFrontend.hooks.addAction('frontend/element_ready/global', WidgetThePlusHandlerBackEnd);
		}
	});
})(jQuery);