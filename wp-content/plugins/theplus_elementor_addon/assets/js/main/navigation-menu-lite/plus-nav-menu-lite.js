(function ($) {
	'use strict';
	$(document).ready(function () {
		if($(".plus-navigation-wrap .plus-navigation-inner.menu-click").length>=1){
			theplus_ele_menu_clicking();
		}
		if($(".mobile-plus-toggle-menu").length > 0){
			$(".mobile-plus-toggle-menu").click(function() {
				var target = $(this).data("target");
				$(this).toggleClass("plus-collapsed");
				if ($(target +'.collapse:not(".in")').length) {
				  
				  $(target +'.collapse:not(".in")').slideDown(400);
				  $(target +'.collapse:not(".in")').addClass('in');
				} else {
				  $(target + '.collapse.in').slideUp(400);
				  $(target +'.collapse.in').removeClass('in');
				}
			});
		}
		$(".plus-mobile-menu .navbar-nav li.menu-item-has-children > a").on("click", function(a) {
            a.preventDefault(),
            a.stopPropagation();
            var b = $(this)
              , c = b.parent()
              , d = b.parent().parent()
              , e = c.find("> ul.dropdown-menu");
            c.hasClass("open") ? (e.slideUp(400),
            c.removeClass("open")) : (d.css("height", "auto"),
            d.find("li.dropdown.open ul.dropdown-menu").slideUp(400),
            d.find("li.dropdown-submenu.open ul.dropdown-menu").slideUp(400),
            d.find("li.dropdown,li.dropdown-submenu.open").removeClass("open"),
            e.slideDown(400),
            c.addClass("open"))
        })
	});
	
	/*--Normal menu and Normal Bottom menu hover effect--*/
	var id;
	$(window).on("load resize",function(e){
		e.preventDefault();
		var inner_width = window.innerWidth;
		if(inner_width > 991){
			if($(".plus-navigation-wrap .plus-navigation-inner").hasClass("menu-hover")){
				theplus_navmenu_hover();
			}
		}
		//Mobile Menu Full Width
		if($('.plus-mobile-menu-content').length){
			var offeset=$(".plus-mobile-menu-content").closest(".plus-navigation-wrap");
			var window_width = $(window).width();
			var menu_content=$(".plus-mobile-menu-content");
			var offset_left = 0 - offeset.offset().left;
			menu_content.css({
					left: offset_left,
					"box-sizing": "border-box",
					width: window_width
			});
		}
	});
} )(jQuery);
function theplus_navmenu_hover(){
	var $= jQuery;	
	$(".plus-navigation-wrap .menu-hover .navbar-nav .dropdown").on("mouseenter", function() {
		var $container =$(this).closest(".plus-navigation-inner");
		var transition_style=$container.data("menu_transition");
		if(transition_style=='' || transition_style=='style-1'){
			$(this).find("> .dropdown-menu").stop().slideDown();
		}else if(transition_style=='style-2'){
			$(this).find("> .dropdown-menu").stop(true, true).delay(100).fadeIn(600);
		}
	}).on("mouseleave", function() {
		var $container =$(this).closest(".plus-navigation-inner");
		var transition_style=$container.data("menu_transition");
		if(transition_style=='' || transition_style=='style-1'){
			$(this).find("> .dropdown-menu").stop().slideUp();
		}else if(transition_style=='style-2'){
			$(this).find("> .dropdown-menu").stop(true, true).delay(100).fadeOut(400);
		}
	});
	$(".plus-navigation-wrap .menu-hover .navbar-nav .dropdown-submenu").on("mouseenter", function() {
		var $container =$(this).closest(".plus-navigation-inner");
		var transition_style=$container.data("menu_transition");
		if(transition_style=='' || transition_style=='style-1'){
			$(this).find("> .dropdown-menu").stop().slideDown();
		}else if(transition_style=='style-2'){
			$(this).find("> .dropdown-menu").stop(true, true).delay(100).fadeIn(600);
		}
	}).on("mouseleave", function() {
		var $container =$(this).closest(".plus-navigation-inner");
		var transition_style=$container.data("menu_transition");
		if(transition_style=='' || transition_style=='style-1'){
			$(this).find("> .dropdown-menu").stop().slideUp();
		}else if(transition_style=='style-2'){
			$(this).find("> .dropdown-menu").stop(true, true).delay(100).fadeOut(400);
		}
	});	
}
function theplus_ele_menu_clicking(){
	"use strict";	
	var $=jQuery;
		$('.plus-navigation-wrap .menu-click .plus-navigation-menu .navbar-nav li.menu-item-has-children > a').on('click', function (event) {
			event.preventDefault(); 
			event.stopPropagation();
			if($(this).closest(".plus-navigation-inner.menu-click")){
				var navSideBut = $(this), 
				navSideItem = navSideBut.parent(),
				navSideUl = navSideBut.parent().parent(),
				navSideItemSub = navSideItem.find('> ul.dropdown-menu');

				if (navSideItem.hasClass('open')) {
					navSideItemSub.slideUp(400);					
					navSideItem.removeClass('open');
				} else {
				navSideUl.css("height","auto");
				navSideUl.find('li.dropdown.open ul.dropdown-menu').slideUp(400);
				navSideUl.find('li.dropdown-submenu.open ul.dropdown-menu').slideUp(400);
				navSideUl.find('li.dropdown,li.dropdown-submenu.open').removeClass('open');
					navSideItemSub.slideDown(400);					
					navSideItem.addClass('open');
				}
			}
		});
		$(document).mouseup(function (e) {
			var $menu = $('li.dropdown');
			if (!$menu.is(e.target) && $menu.has(e.target).length === 0){
				$menu.find('ul.dropdown-menu').slideUp(400);
				$menu.find('li.dropdown-submenu.open ul.dropdown-menu').slideUp(400);
				$menu.removeClass('open');			
		   }
		});
}
(function ($) {
	'use strict';
	
	var WidgetHeaderNavigation = function($scope, $) {

		var $plus_navigation = $scope.find('.plus-navigation-wrap');
		
		if($plus_navigation.find(".hover-inverse-effect").length >0){
			$(".plus-navigation-menu .nav > li > a").on({
			  mouseenter: function() {
				$( this ).closest(".hover-inverse-effect").addClass("is-hover-inverse");
				$( this ).addClass( "is-hover" );
			  }, mouseleave: function() {
				$( this ).closest(".hover-inverse-effect").removeClass("is-hover-inverse");
				$( this ).removeClass( "is-hover" );
			  }
			});
		}
		if($plus_navigation.find(".submenu-hover-inverse-effect").length >0){
			$(".plus-navigation-menu .nav li.dropdown .dropdown-menu > li > a").on({
			  mouseenter: function() {
				$( this ).closest(".submenu-hover-inverse-effect").addClass("is-submenu-hover-inverse");
				$( this ).addClass( "is-hover" );
			  }, mouseleave: function() {
				$( this ).closest(".submenu-hover-inverse-effect").removeClass("is-submenu-hover-inverse");
				$( this ).removeClass( "is-hover" );
			  }
			});
		}
		
		var inner_width = window.innerWidth;
		if(inner_width > 991){
			if($plus_navigation.find(".plus-navigation-inner").hasClass("menu-hover")){
				theplus_navmenu_hover();
			}
		}
		
	};
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-navigation-menu-lite.default', WidgetHeaderNavigation);
	});
})(jQuery);