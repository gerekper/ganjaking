/*Service Box*/
(function ($) {
	"use strict";
/*Animated services box*/
	var WidgetAnimatedServicesBoxHandler = function($scope, $) {
		var container = $scope.find('.pt_plus_asb_wrapper'),
			loop_item=container.find(".service-item-loop" );
		//Fancy box
		if(container.hasClass('fancy-box-style-1')){				
					var fb_height = container.find('.fancybox-inner-content').outerHeight();			
					container.find('.fancybox-image-background').css('min-height',fb_height).css('height',fb_height);
					container.find('.fancybox-inner-content').css('position','absolute').css('top',0).css('height','max-content');
		}
		//Article box
		if(container.hasClass('article-box-style-1')){
			$('.article-box-style-1 .article-box-inner-content').on('mouseenter',function() {
				$(this).find(".asb-desc").slideDown(300)
				$(this).find(".pt-plus-button-wrapper").slideDown(300)	
			});
			$('.article-box-style-1 .article-box-inner-content').on('mouseleave',function() {
				$(this).find(".asb-desc").slideUp(300)
				$(this).find(".pt-plus-button-wrapper").slideUp(300)
			});
		}
		//Image Accordion
		if(container.hasClass('image-accordion')){
			loop_item.on("mouseenter",function() {
				var flexgrow = $(this).data('flexgrow');
				$(this).closest('.image-accordion').find('.active_accrodian').css('flex-grow','1').removeClass('active_accrodian');
				$(this).addClass( "active_accrodian" ).css('flex-grow',flexgrow);
			}).on("mouseleave", function() {
				if($(this).closest('.image-accordion').data("accordion-hover")=='yes'){
					$(this).css('flex-grow','1').removeClass('active_accrodian');
				}
			});
		}
		//SlideBox
		if(container.hasClass('sliding-boxes')){
			var w=$(window).innerWidth();
			$(window).on('resize',function(){
				var w=$(window).innerWidth();
				if(w>=1024){
					var total_item = loop_item.length;		
					var new_total_item = total_item + 1; 
					var margin = (total_item - 1) * 15;
					var divWidth = container.find(".asb_wrap_list").width();			
					
					divWidth = divWidth -  margin;
					new_total_item = (divWidth / new_total_item) - 10 ;			
					loop_item.attr('data-width',new_total_item).css('width',new_total_item );
					loop_item.find("img").css('width',new_total_item );
					loop_item.find(".tp-sb-image").css('width',new_total_item );
					loop_item.find(".asb-content").css('width',new_total_item ).css('left',new_total_item );
					container.find(".service-item-loop.active-slide" ).css('width',new_total_item*2 );
				}else{
					loop_item.find("img").css('width','' );
					loop_item.find(".tp-sb-image").css('width','' );
					loop_item.find(".asb-content").css('width','' ).css('left','' );
					container.find(".service-item-loop.active-slide" ).css('width','');
				}
				loop_item.on("mouseenter", function() {
					var width = $(this).attr('data-width') - 10;
					if(w>=1024){
						$(this ).closest('.sliding-boxes').find(".service-item-loop").removeClass("active-slide").css('width',width );
						$(this).addClass("active-slide").css('width',width * 2 );
						$('.asb-content',this).css('left',width);
					}else{
						$(this ).closest('.sliding-boxes').find(".service-item-loop").removeClass("active-slide");
						$(this).addClass("active-slide");
					}
				});
			});
			if(w>=1024){
				var total_item = loop_item.length;		
				var new_total_item = total_item + 1; 
				var margin = (total_item - 1) * 15;
				var divWidth = container.find(".asb_wrap_list").width();			
				
				divWidth = divWidth -  margin;
				new_total_item = (divWidth / new_total_item) - 10 ;			
				loop_item.attr('data-width',new_total_item).css('width',new_total_item );
				loop_item.find("img").css('width',new_total_item );
				loop_item.find(".tp-sb-image").css('width',new_total_item );
				loop_item.find(".asb-content").css('width',new_total_item ).css('left',new_total_item );
				container.find(".service-item-loop.active-slide" ).css('width',new_total_item*2 );
			}
			loop_item.on("mouseenter", function() {
				var width = $(this).attr('data-width') - 10;
				if(w>=1024){
					$(this ).closest('.sliding-boxes').find(".service-item-loop").removeClass("active-slide").css('width',width );
					$(this).addClass("active-slide").css('width',width * 2 );
					$('.asb-content',this).css('left',width);
				}else{
					$(this ).closest('.sliding-boxes').find(".service-item-loop").removeClass("active-slide");
					$(this).addClass("active-slide");
				}
			});
		}
		//Hover Sections
		if(container.hasClass('hover-section')){
			var i = 0;
			loop_item.each(function(){
				var hover_sec_boc = $(this).data('hsboc');
				if(i==0){
					var total_hover_section = $(this).find('.hover-section-content-wrapper').data('image');					
					$(this).closest('section.elementor-element,.elementor-element.e-container,.elementor-element.e-con').css('background','url('+ total_hover_section +') center/cover').css('transition', 'background 0.5s linear').css('box-shadow',  hover_sec_boc + ' 0px 0px 0px 2000px inset');
				}
				i++;
			});
			loop_item.on("mouseenter", function() {
				var image = $(this).find('.hover-section-content-wrapper').data('image');
				$(this).closest('.asb_wrap_list').find(".service-item-loop").removeClass("active-hover");
				$(this).addClass("active-hover");
				$(this).closest('section.elementor-element,.elementor-element.e-container,.elementor-element.e-con').css('background','url('+ image +') center/cover');				
			});
		}
		//Services Element
		if (container.hasClass('services-element-style-1')) {
			$(window).on('load resize',function(){
				container.find(".se-wrapper").each(function(){
					var sec_height = $(this).height();
					var top_pa=$(this).css('padding-top');
					top_pa=parseInt(top_pa, 10);
					sec_height = sec_height + 40 +top_pa;				
					$(this).find(".se-listing-section").css('padding-top', sec_height);				
				});
			});
			container.find(".se-wrapper").each(function(){
				var sec_height = $(this).height();
				var top_pa=$(this).css('padding-top');
				top_pa=parseInt(top_pa, 10);
				sec_height = sec_height + 40 +top_pa;			
				$(this).find(".se-listing-section").css('padding-top', sec_height);				
			});
		}
		//portfolio style 1
		if (container.hasClass("portfolio-style-1")) {
			var i = 0;
			loop_item.on("mouseenter", function() {
				var imageurl = $(this).data('url');
				$(this).closest('.portfolio-style-1').find(".service-item-loop").removeClass("active-port");
				$(this).addClass("active-port");
				$(this).closest('.asb_wrap_list').find('.portfolio-hover-image').css('background','url('+ imageurl +')');				
			});			
		}
		if (container.hasClass("portfolio-style-2")) {
			loop_item.on("mouseenter", function() {
				var imageurl = $(this).data('url');
				$(this).closest('.asb_wrap_list').find(".service-item-loop").removeClass("active-port");
				$(this).addClass("active-port");
				$(this).closest('.asb_wrap_list').find('.portfolio-wrapper').css('background','url('+ imageurl +')');
			});
		}
	};
	/*Animated services box*/
	$(window).on("load resize",function(){
		var width_in=$(window).innerWidth();
		if(width_in <= 1024){
			if ($(".pt_plus_asb_wrapper.portfolio .asb-title-link").length) {
				$(".pt_plus_asb_wrapper.portfolio .asb-title-link").one('click', function () { 
					 event.stopPropagation();							 
					$(this).removeAttr("href");
					if($(this).find(".mobile-click-port").length==0){
						var portfolio_click = $(this).closest('.service-item-loop.active-port').data('clickurl');
						var portfolio_clicktext = $(this).closest('.service-item-loop.active-port').data('clicktext');
						$(this).append('<div class="mobile-click-port"><a class="pf_a_click" href="' + portfolio_click + '">' + portfolio_clicktext + '</a></div>');
					}
				});
			}
		}
		if(width_in <= 767){		
			if ($(".pt_plus_asb_wrapper.hover-section,.pt_plus_asb_wrapper.sliding-boxes,.pt_plus_asb_wrapper.image-accordion").length) {		
				$(".pt_plus_asb_wrapper.hover-section .asb-title-link,.pt_plus_asb_wrapper.sliding-boxes .asb-title-link,.pt_plus_asb_wrapper.image-accordion .asb-title-link").on('click', function () {		
					 event.stopPropagation();		
					 $(this).removeAttr("href");		
				});
			}
		}
	});
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-animated-service-boxes.default', WidgetAnimatedServicesBoxHandler);
	});
})(jQuery);