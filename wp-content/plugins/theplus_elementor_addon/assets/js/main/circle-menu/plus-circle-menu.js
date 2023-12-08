/*circle menu*/(function($) {
	"use strict";
	var WidgetCircleMenuHandler = function($scope, $) {
		var container = $scope.find('.plus-circle-menu-wrapper');
		var container_scroll_view = $scope.find('.plus-circle-menu-wrapper.scroll-view');
		var container_straight = $scope.find('.plus-circle-menu-wrapper.layout-straight');
		var container_circle = $scope.find('.plus-circle-menu-wrapper.layout-circle');
		
		
		if(container_straight.length>0){			
			$(".plus-circle-menu-wrapper.layout-straight .plus-circle-main-menu-list a.main_menu_icon").unbind().click(function(e){
				e.preventDefault();
				var uid=$(this).closest(".plus-circle-menu-wrapper").data("uid");
				if($('.'+uid+ ' .plus-circle-menu').hasClass("circleMenu-closed")){
					$(this).closest(".plus-circle-menu").removeClass("circleMenu-closed");					
					$(this).closest(".plus-circle-menu").addClass("circleMenu-open");					
					$(this).closest(".plus-circle-menu-wrapper").find('.show-bg-overlay').addClass("activebg");					
				}else if($('.'+uid+ ' .plus-circle-menu').hasClass("circleMenu-open")){
					$(this).closest(".plus-circle-menu").removeClass("circleMenu-open");
					$(this).closest(".plus-circle-menu").addClass("circleMenu-closed");
					$(this).closest(".plus-circle-menu-wrapper").find('.show-bg-overlay').removeClass("activebg");										
				}
			});
		}
		
		
		
		if(container_circle.length>0){
			$(".plus-circle-menu-wrapper.layout-circle .plus-circle-main-menu-list a.main_menu_icon").unbind( "click",function(e){
				e.preventDefault();
				var uid=$(this).closest(".plus-circle-menu-wrapper").data("uid");
				if($('.'+uid+ ' .plus-circle-menu').hasClass("circleMenu-closed")){
					$(this).closest(".plus-circle-menu-wrapper").find('.show-bg-overlay').addClass("activebg");					
				}else if($('.'+uid+ ' .plus-circle-menu').hasClass("circleMenu-open")){
					$(this).closest(".plus-circle-menu-wrapper").find('.show-bg-overlay').removeClass("activebg");					
				}				
			});
		}		
		if(container.length>0 && container_scroll_view){
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
	};
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-circle-menu.default', WidgetCircleMenuHandler);
	});	
	$(document).ready(function(){
		$(".show-bg-overlay").on('click',function(){
			$(this).closest(".plus-circle-menu-wrapper").find(".main_menu_icon").trigger( "click" );
		});
	});
})(jQuery);