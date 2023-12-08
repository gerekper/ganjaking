(function ($) {
	'use strict';	
	var WidgetMobileMenu = function($scope, $) {	
		
		if($('.tp-mm-wrapper.swiper-container,.tp-mm-l-wrapper.swiper-container,.tp-mm-r-wrapper.swiper-container').length > 0){
			new Swiper(".tp-mm-wrapper.swiper-container,.tp-mm-l-wrapper.swiper-container,.tp-mm-r-wrapper.swiper-container",{
				slidesPerView: "auto",
				mousewheelControl: !0,
				freeMode: !0
			});
		}
		if($('.tp-mobile-menu.tpet-on').length > 0){
			$(".tp-mm-et-link").on( "click", function(e) {			
			$(this).closest(".tp-mobile-menu").find('.header-extra-toggle-content').addClass("open");
			$(this).closest(".tp-mobile-menu").find('.extra-toggle-content-overlay').addClass('open');
		});
		$('.extra-toggle-close-menu').on("click", function(e) {
			e.preventDefault();
			$(this).closest(".tp-mobile-menu").find('.header-extra-toggle-content').removeClass("open");
			$(this).closest(".tp-mobile-menu").find('.extra-toggle-content-overlay').removeClass('open');
		});
		$('.extra-toggle-content-overlay').on( "click", function(e) {
			e.preventDefault();
			$(this).closest(".tp-mobile-menu").find('.header-extra-toggle-content').removeClass("open");
			$(this).removeClass('open');
		});
		}	
		if($('.tp-mobile-menu .extra-toggle-close-menu.mm-ci-auto').length > 0){
			$(".tp-mm-ca .tp-mm-et-link").on( "click", function(e) {
			
				$(this).closest(".tp-loop-inner").find('.tp-mm-et-link').addClass("tp-mm-ca");
				$(this).closest(".tp-loop-inner").find('.extra-toggle-close-menu-auto').addClass('tp-mm-ca');
			});
			$(".extra-toggle-close-menu-auto").on("click",function(){				
				$(this).closest(".tp-mobile-menu").find('.header-extra-toggle-content').removeClass("open");
				$(this).closest(".tp-mobile-menu").find('.extra-toggle-content-overlay').removeClass('open');
				$(this).closest(".tp-mobile-menu").find('.tp-loop-inner .tp-mm-et-link').removeClass("tp-mm-ca");
				$(this).closest(".tp-mobile-menu").find('.tp-loop-inner .extra-toggle-close-menu-auto').removeClass('tp-mm-ca');
				
			});
			$(".extra-toggle-content-overlay").on("click",function(){				
				$(this).closest(".tp-loop-inner").find( ".extra-toggle-close-menu-auto.tp-mm-ca").trigger( "click" );
			});
		}
		
		var container = $scope.find('.tp-mobile-menu');
		var container_scroll_view = $scope.find('.tp-mobile-menu .tp-mm-ul');
		if(container.length > 0 && container_scroll_view){
			$(window).on('scroll', function() {
				var scroll = $(this).scrollTop();
				container.each(function () {
					var scroll_view_value = $(this).data("scroll-view");
					var uid=$(this).data("uid"),
						$scroll_top = $("."+uid );
					if (scroll > scroll_view_value) {
						$scroll_top.addClass('show');
					}else {
						$scroll_top.removeClass('show');
					}
					
				});
			});	
		}
	container.find(" a").each(function(){
		var pathname = window.location.href;            
			if ($(this).attr("href") == window.location.href.replace(/\/$/, '')){
				$(this).closest(".tp-mm-li").addClass("active");               
			}else if(pathname && $(this).attr("href") && $(this).attr("href")==pathname && $(this).attr("href").indexOf(pathname) > -1){                
				$(this).closest(".tp-mm-li").addClass('active');               
			}
	   });
	};
		
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-mobile-menu.default', WidgetMobileMenu);
	});

})(jQuery);