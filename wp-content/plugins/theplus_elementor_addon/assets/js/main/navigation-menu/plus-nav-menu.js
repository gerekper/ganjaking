/*Navigation Menu*/(function ($) {
	'use strict';
	$(document).ready(function () {
		if($(".plus-navigation-wrap .plus-navigation-inner.menu-click").length>=1){
			theplus_ele_menu_clicking();
		}
		
		$(".plus-navigation-wrap .menu-item a").each(function(){
			var title = $(this).attr("title");
			$(this).attr("tmp_title", title);
			$(this).attr("title","");
		});
		
		if($(".mobile-plus-toggle-menu").length > 0){
			$(".mobile-plus-toggle-menu").on('click',function() {
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
		$(".plus-mobile-menu:not(.swiper-wrapper) .navbar-nav li.menu-item-has-children > a,.plus-mobile-menu:not(.swiper-wrapper) .navbar-nav li.dropdown-submenu > a").on("click", function(a) {
            a.preventDefault(),
            a.stopPropagation();
            var b = $(this)
              , c = b.parent()
              , d = b.parent().parent()
              , e = c.find("> ul.dropdown-menu");
            c.hasClass("open-menu") ? (e.slideUp(400),
            c.removeClass("open-menu")) : (d.css("height", "auto"),
            d.find("li.dropdown.open-menu ul.dropdown-menu").slideUp(400),
            d.find("li.dropdown-submenu.open-menu ul.dropdown-menu").slideUp(400),
            d.find("li.dropdown,li.dropdown-submenu.open-menu").removeClass("open-menu"),
            e.slideDown(400),
            c.addClass("open-menu"));
			
			if(e.find('.list-isotope').length){
				setTimeout(function(){
					e.find('.list-isotope .post-inner-loop').isotope('layout');
				}, 420);
			}
			if(e.find('.list-isotope-metro .post-inner-loop').length > 0){
				theplus_setup_packery_portfolio('all');		
			}	
        });
		$(".plus-navigation-wrap .plus-dropdown-default").each(function(){
			var mwidth= $(this).data("dropdown-width");
			var id= $(this).attr("id");
			if(mwidth!='' && mwidth!=undefined){
				$('head').append('<style>#'+id+'.plus-dropdown-default.plus-fw > ul.dropdown-menu{max-width:'+mwidth+' !important;min-width:'+mwidth+' !important;right:auto;}#'+id+'.plus-dropdown-default:not(.plus-fw) > ul.dropdown-menu{max-width:'+mwidth+' !important;min-width:'+mwidth+' !important;width: 100%;}</style>');
			}
		});
		if($(".plus-navigation-menu.menu-vertical-side.toggle-type-click").length > 0){
			$(".menu-vertical-side.toggle-type-click .plus-vertical-side-toggle").on("click",function(a){
				a.preventDefault(),
				a.stopPropagation();
				$(this).closest(".toggle-type-click").toggleClass("tp-click");
			});
		}
		if($(".plus-navigation-menu.menu-vertical-side.toggle-type-hover").length > 0){
			$(".menu-vertical-side.toggle-type-hover").on('mouseenter',function() {
				$(this).addClass("tp-hover");
			}).on('mouseleave', function() {
				$(this).removeClass("tp-hover");
			});
		}
	});
    
	var inner_width = window.innerWidth;
		if(inner_width <= 991 && $('.plus-mobile-menu-content').length){
			$(document).mouseup(function (e) {
				var container = $(".mobile-plus-toggle-menu");
				var mouse_click = $(e.target).find(".plus-navigation-inner").data("mobile-menu-click");
				if(mouse_click=='yes'){					
					if (!container.is(e.target) && container.has(e.target).length === 0){
						var $menu = $('li.dropdown');
						if (!$menu.is(e.target) && $menu.has(e.target).length === 0){
							$menu.find('ul.dropdown-menu').slideUp(400);
							$menu.find('li.dropdown-submenu.open-menu ul.dropdown-menu').slideUp(400);
							$menu.removeClass('open-menu');
							$(e.target).find(".plus-mobile-menu-content").slideUp(400);
							$(e.target).find(".plus-mobile-menu-content").removeClass('in');
							$(e.target).find(".plus-navigation-inner").find(".mobile-plus-toggle-menu").addClass("plus-collapsed");
                            if($(e.target).find(".plus-mobile-menu-content").hasClass('plus-menu-off-canvas')){
                                $(e.target).find(".plus-mobile-menu-content").removeClass('is-active');
                            }
						}
					}
				}
			});
		}
	
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
		if($('.plus-mobile-menu-content:not(.nav-cust-width)').length){
			$(".plus-mobile-menu-content:not(.plus-menu-off-canvas)").each(function(){
				var offeset=$(this).closest(".plus-navigation-wrap");
				var window_width = $(window).width();
				var menu_content=$(this);
				var offset_left = 0 - offeset.offset().left;
				
					if($('body').hasClass("rtl")){
						menu_content.css({
								right: offset_left,
								"box-sizing": "border-box",
								width: window_width
						});
					}else{
						menu_content.css({
								left: offset_left,
								"box-sizing": "border-box",
								width: window_width
						});
					}
					
			});
		}
		theplus_megamenu_fullwidth_container();
	});
} )(jQuery);
function theplus_navmenu_hover(){
	"use strict";
	var $= jQuery;	
	$(".plus-navigation-wrap .menu-hover .navbar-nav .dropdown").on('mouseenter',function() {
		var $container =$(this).closest(".plus-navigation-inner");
		var transition_style=$container.data("menu_transition");
		
		if(transition_style=='' || transition_style=='style-1'){
			$(this).find("> .dropdown-menu").stop().slideDown();
		}else if(transition_style=='style-2'){
			$(this).find("> .dropdown-menu").stop(true, true).delay(100).fadeIn(600);
		}else if(transition_style=='style-3' || transition_style=='style-4'){
			$(this).find("> .dropdown-menu").addClass("open-menu");			
		}
		
		if($('.list-isotope-metro .post-inner-loop',this).length > 0){
			theplus_setup_packery_portfolio('all');		
		}
		
	}).on('mouseleave', function() {
		var $container =$(this).closest(".plus-navigation-inner");
		var transition_style=$container.data("menu_transition");
		
		if(transition_style=='' || transition_style=='style-1'){
			$(this).find("> .dropdown-menu").stop().slideUp();
		}else if(transition_style=='style-2'){
			$(this).find("> .dropdown-menu").stop(true, true).delay(100).fadeOut(400);
		}else if(transition_style=='style-3' || transition_style=='style-4'){
			$(this).find("> .dropdown-menu").removeClass("open-menu");			
		}
		
	});
	$(".plus-navigation-wrap .menu-hover .navbar-nav .dropdown-submenu").on('mouseenter',function() {
		var $container =$(this).closest(".plus-navigation-inner");
		var transition_style=$container.data("menu_transition");
		
		if($('.list-isotope-metro .post-inner-loop',this).length > 0){
			theplus_setup_packery_portfolio('all');	
		}
		
		if(transition_style=='' || transition_style=='style-1'){
			$(this).find("> .dropdown-menu").stop().slideDown();
		}else if(transition_style=='style-2'){
		$(this).find("> .dropdown-menu").stop(true, true).delay(100).fadeIn(600);
		}else if(transition_style=='style-3' || transition_style=='style-4'){
			$(this).find("> .dropdown-menu").addClass("open-menu");
		}
		
	}).on('mouseleave', function() {
		var $container =$(this).closest(".plus-navigation-inner");
		var transition_style=$container.data("menu_transition");
		
		if(transition_style=='' || transition_style=='style-1'){
			$(this).find("> .dropdown-menu").stop().slideUp();
		}else if(transition_style=='style-2'){
			$(this).find("> .dropdown-menu").stop(true, true).delay(100).fadeOut(400);
		}else if(transition_style=='style-3' || transition_style=='style-4'){
			$(this).find("> .dropdown-menu").removeClass("open-menu");
		}
		
	});	
}
function theplus_megamenu_fullwidth_container(){
	"use strict";
	var $=jQuery;
	if($('.plus-navigation-menu .plus-dropdown-container').length > 0 || $('.plus-navigation-menu .plus-dropdown-full-width').length > 0){
	
		var left_offset=0;
	
		if( $('.plus-navigation-menu .plus-dropdown-container').length > 0 ) {
			$('.plus-navigation-menu .plus-dropdown-container').each(function(){
				var cthis =$(this);
				//Horizontal Menu
				var vertical_menu = cthis.closest('.menu-vertical-side');
				if(vertical_menu.length>0){
					var full_width=cthis.closest(".elementor-container").width();
					var menu_width = vertical_menu.find(".navbar-nav").width();
					var con_width = full_width - menu_width - 20;
					var container_megamenu=$(">.dropdown-menu",cthis);
					container_megamenu.css({
							"box-sizing": "border-box",
							width: con_width
					});
				}
				if(!vertical_menu.length){
					var cont_width=cthis.closest(".elementor-container").width();
					var window_width = window.innerWidth;
					window_width=window_width-cont_width;
					var left_offset=window_width/2;
					
					var offeset=cthis.closest(".plus-navigation-wrap");
					
					
					var container_megamenu=$(">.dropdown-menu",cthis);
					if($('body').hasClass("rtl")){
					var offset_right = 0 - offeset.offset().left+(left_offset);
						container_megamenu.css({
							right: offset_right,
							"box-sizing": "border-box",
							width: cont_width
						});
					}else{
					var offset_left = 0 - offeset.offset().left+(left_offset);
						container_megamenu.css({
							left: offset_left,
							"box-sizing": "border-box",
							width: cont_width
						});
					}
				}
			});
		}
		if( $('.plus-navigation-menu .plus-dropdown-full-width').length > 0 ) {
			$('.plus-navigation-menu .plus-dropdown-full-width').each(function(){
				var cthis =$(this);
				var vertical_menu = cthis.closest('.menu-vertical-side');
				if(vertical_menu.length>0){
					var full_width=cthis.closest(".elementor-container").width();
					var menu_width = vertical_menu.find(".navbar-nav").width();
					var con_width = full_width - menu_width - 20;
					var container_megamenu=$(">.dropdown-menu",cthis);
					container_megamenu.css({
							"box-sizing": "border-box",
							width: con_width
					});
				}
				if(!vertical_menu.length){
					var full_width=cthis.closest(".elementor-container").width();
					var window_width = $(window).width();
					//window_width=window_width-full_width;
					var offeset=cthis.closest(".plus-navigation-wrap");
					if(offeset.length > 0){
						var offset_left = 0 - offeset.offset().left-(left_offset);
					}else{
						var offset_left = 0 - 0+(left_offset);
					}
					if($('body').hasClass("rtl")){
						var offset_left = 0 - (window_width - (offeset.offset().left + offeset.width()));
					}
					var container_megamenu=$(">.dropdown-menu",cthis);
					
					if($('body').hasClass("rtl")){
						container_megamenu.css({
								right: offset_left,
								"box-sizing": "border-box",
								width: window_width
						});
					}else{
						container_megamenu.css({
								left: offset_left,
								"box-sizing": "border-box",
								width: window_width
						});
					}
				}
			});
		}
		
	}
}
function theplus_ele_menu_clicking(){
	"use strict";	
	var $=jQuery;
		$('.plus-navigation-wrap .menu-click .plus-navigation-menu .navbar-nav li.menu-item-has-children > a,.plus-navigation-wrap .menu-click .plus-navigation-menu .plus-mobile-menu.plus-menu-off-canvas .navbar-nav li.menu-item-has-children > a').on('click', function (event) {
			event.preventDefault(); 
			event.stopPropagation();
			if($(this).closest(".plus-navigation-inner.menu-click")){
				var navSideBut = $(this), 
				navSideItem = navSideBut.parent(),
				navSideUl = navSideBut.parent().parent(),
				navSideItemSub = navSideItem.find('> ul.dropdown-menu');

				if (navSideItem.hasClass('open-menu')) {
					navSideItemSub.slideUp(400);					
					navSideItem.removeClass('open-menu');
				} else {
					navSideUl.css("height","auto");
					navSideUl.find('li.dropdown.open-menu ul.dropdown-menu').slideUp(400);
					navSideUl.find('li.dropdown-submenu.open-menu ul.dropdown-menu').slideUp(400);
					navSideUl.find('li.dropdown,li.dropdown-submenu.open-menu').removeClass('open-menu');
					navSideItemSub.slideDown(400);					
					navSideItem.addClass('open-menu');
                    navSideItemSub.addClass('open-menu');
					if(navSideItem.find('.list-isotope-metro .post-inner-loop').length > 0){
						theplus_setup_packery_portfolio('all');		
					}	
				}
			}	
			
		});
		$(document).mouseup(function (e) {
			var $menu = $('li.dropdown');
			if (!$menu.is(e.target) && $menu.has(e.target).length === 0){
				$menu.find('ul.dropdown-menu').slideUp(400);
				$menu.find('li.dropdown-submenu.open-menu ul.dropdown-menu').slideUp(400);
				$menu.removeClass('open-menu');			
		   }
		});
}
(function ($) {
	'use strict';	
	var WidgetHeaderNavigation = function($scope, $) {

		var $plus_navigation = $scope.find('.plus-navigation-wrap'),
			getlink = $plus_navigation[0].querySelectorAll('.plus-navigation-menu.menu-horizontal .menu-item a');  

			if( getlink.length > 0 ){
				getlink.forEach(function(self){
					if(window.location.href == self.href){
						self.parentElement.classList.add('active');
					}
				})
			}

		if($plus_navigation.find(".hamburger-off-canvas").length > 0){
			$(document).on('click','.hamburger-off-canvas ',function(){ 
                $(this).addClass("open-menu");
                $('body').addClass('mobile-menu-open');
                var b = $(this),
                c = b.parent().parent();
                c.find('.plus-mobile-menu.plus-menu-off-canvas').addClass('is-active');
            })
            $(document).on('click','.mobile-menu-open','.plus-mobile-menu.plus-menu-off-canvas .close-menu',function(){
                var p = $(this),
                c = p.find('.plus-mobile-menu.plus-menu-off-canvas'),
                d = c.parent();
                if(c.hasClass('is-active')) {
                    c.removeClass('is-active in');
                    p.removeClass('mobile-menu-open');
                    d.find(".hamburger-off-canvas").removeClass('open-menu').addClass('plus-collapsed');
                }
            });
		}
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
		theplus_megamenu_fullwidth_container();
		//add class megamenu
		$plus_navigation.find(".nav>li .dropdown-menu").each(function(){
			var $this= $(this);
			var $megamenu_content=$this.find(".plus-megamenu-content");
			var $full_width_content=$this.closest(".plus-dropdown-full-width");
			var $container_content=$this.closest(".plus-dropdown-container");
			if($megamenu_content.length > 0 || $full_width_content.length || $container_content.length){
				var $closest_class=$this.closest(".dropdown");
				$closest_class.addClass("plus-fw");
			}
		});
		if($('.plus-mobile-menu-content.swiper-container').length > 0){
			new Swiper(".plus-mobile-menu-content.swiper-container",{
				slidesPerView: "auto",
				mousewheelControl: !0,
				freeMode: !0
			});
		}
		
		//navigation Sticky Opt
		if($plus_navigation.length > 0){
			var inner=$plus_navigation.find('.plus-navigation-inner'),
				nav_sticky=inner.data('nav-sticky'),
				nav_sticky_osup=inner.data('nav-sticky-osup'),
				nav_sticky_id=inner.data('wid');
			
			
			if (nav_sticky!=undefined && nav_sticky=='yes' && nav_sticky_id!='') {
				var sec_id = $plus_navigation.closest(".elementor-element.elementor-section:not(.elementor-inner-section),.elementor-element.e-container,.elementor-element.e-con").addClass("plus-nav-sticky-sec").data("id");
				if(inner_width > 801 && $plus_navigation.closest(".elementor-element.elementor-section:not(.elementor-inner-section):not(.elementor-hidden-desktop),.elementor-element.e-container,.elementor-element.e-con").length){				
					$plus_navigation.append('<div class="plus-nav-sticky ' + nav_sticky_id + "-" + sec_id + '"></div>');
				}
				if(inner_width < 800 && inner_width > 768 && $plus_navigation.closest(".elementor-element.elementor-section:not(.elementor-inner-section):not(.elementor-hidden-tablet),.elementor-element.e-container,.elementor-element.e-con").length){
					$plus_navigation.append('<div class="plus-nav-sticky ' + nav_sticky_id + "-" + sec_id + '"></div>');
				}
				
				if(inner_width < 767 && $plus_navigation.closest(".elementor-element.elementor-section:not(.elementor-inner-section):not(.elementor-hidden-phone),.elementor-element.e-container,.elementor-element.e-con").length){
					$plus_navigation.append('<div class="plus-nav-sticky ' + nav_sticky_id + "-" + sec_id + '"></div>');
				}				
                
                var sec_class = ".elementor-element-" + sec_id;
                $(".plus-nav-sticky." + nav_sticky_id + "-" + sec_id).insertBefore(sec_class), $(window).on("load scroll", function() {
                    var n = $(sec_class).outerHeight(),
                        o = $(".plus-nav-sticky." + nav_sticky_id + "-" + sec_id).length ? $(".plus-nav-sticky." + nav_sticky_id + "-" + sec_id).offset().top : 0,
                        i = $(".plus-nav-sticky." + nav_sticky_id + "-" + sec_id);
                    $(window).scrollTop() > o ? (i.css("min-height", n), $(sec_class).addClass("plus-fixed-sticky")) : (i.css("min-height", 0), $(sec_class).removeClass("plus-fixed-sticky"))
					/*On Mouse Scroll Up Sticky*/
					if(nav_sticky_osup!=undefined && nav_sticky_osup=='yes'){
						(function() {
							var header = new Headroom(document.querySelector(sec_class), {       
								tolerance: 5,
								classes: {
								  initial: "animated",
								  pinned: "slideDown",
								  unpinned: "slideUp"
								}
							});
							header.init();
							
						}());
					}
					/*On Mouse Scroll Up Sticky*/
                })
            }
		}
		
		/* AccessibilityMenu Code */
		$scope[0].addEventListener("keyup", function(e){
			e.preventDefault();
			e.stopPropagation();			
            MenuAccessibility(e,this);
		});

		let Tempblock = [];
        function MenuAccessibility (event, $this) {
            let GetMenu = $this.querySelectorAll('.dropdown-toggle');
				//event.which == 13 &&
                if( event.target.nextElementSibling){
                    event.target.nextElementSibling.style.display = "block";
					Tempblock.push(event.target.nextElementSibling);
                }
				
                if( GetMenu.length && event.target.nextElementSibling ){
					if(event.which == 27){
						if(Tempblock.length > 0){
							Tempblock.forEach(function(self) {
								self.style.display = "none";
							});
						}
					}

                    if(event.which == 9){
						if(event.target.classList.contains("dropdown-toggle")){
							GetMenu.forEach(function(self) {
								if(event.target !== self){
									self.nextElementSibling.style.display = "none";
								}
							});
						}

						if(event.target.parentElement.classList.contains("dropdown-submenu")){
							let gg = event.target.parentElement.closest('ul.dropdown-menu').querySelectorAll('li');
								gg.forEach(function(self) {
									if(self != event.target.parentElement && self.querySelectorAll('ul.dropdown-menu').length > 0){
										self.querySelector('ul.dropdown-menu').style.display = "none";
									}
								});
						}
                    }
                }
        }		

	};
	
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-navigation-menu.default', WidgetHeaderNavigation);
	});

})(jQuery);