/*header Extras*/
( function( $ ) {
	"use strict";
	//Music Bar
	$(document).ready(function(){
		var musicbar=$(".header-plus-music-toggle");
		
		musicbar.on("click", function(e) {
			e.preventDefault();
			var $this=$(this);
			
			var bgmusic=$this.attr("data-bgmusic"),
				bgmusic_load=$this.attr("data-bgmusic_load"),
				bgmusic_volume=$this.attr("data-bgmusic_volume");
			//if(bgmusic && "on" != bgmusic_load){
				var music= new buzz.sound(bgmusic,{
					preload: !1,
					loop: true,
					volume: bgmusic_volume,
				});
			//}
			
			if("on" == bgmusic_load){
				music.fadeTo(0, 1e3, function() {
					buzz.all().pause();
				});
				$this.removeClass("on");
					$this.attr("data-bgmusic_load", "off");
					bgmusic_load='off';
			}else{
				$(".header-plus-music-toggle.on").each(function(){
					$(this).removeClass("on");
					$(this).attr("data-bgmusic_load", "off");
				});
				music.fadeTo(50, 1e3, function() {
					buzz.all().pause();
					music.play();
				});
				$this.addClass("on");
					$this.attr("data-bgmusic_load", "on");
					bgmusic_load='on';
			}
		});
	});
	
	
	jQuery( document ).on( 'elementor/popup/show', (event, id, instance) => {		
		
		var musicbar=jQuery('#elementor-popup-modal-'+id).find(".header-plus-music-toggle");
		
		if(musicbar.length){
			var bgmusic=musicbar.attr("data-bgmusic"),
				bgmusic_load=musicbar.attr("data-bgmusic_load"),
				bgmusic_volume=musicbar.attr("data-bgmusic_volume");
			if(bgmusic){
				var music= new buzz.sound(bgmusic,{
					preload: !1,
					loop: true,
					volume: bgmusic_volume,
				});
			}
			musicbar.on("click", function(e) {
				e.preventDefault();
				var $this=$(this);
				//playpausemusic($this,music,bgmusic_load);
				if("on" == bgmusic_load){
					music.fadeTo(0, 1e3, function() {
						music.pause();
					});
					$this.removeClass("on");
						$this.attr("data-bgmusic_load", "off");
						bgmusic_load='off';
				}else{
					jQuery(".header-plus-music-toggle.on").each(function(){
						jQuery(this).removeClass("on");
						jQuery(this).attr("data-bgmusic_load", "off");
					});
					music.fadeTo(50, 1e3, function() {
						buzz.all().pause();
						music.play();
					});
					$this.addClass("on");
					$this.attr("data-bgmusic_load", "on");
					bgmusic_load='on';
				}
			});
		}
	} );
		jQuery( document ).on( 'elementor/popup/hide', (event, id, instance) => {
			var musicbar=jQuery('#elementor-popup-modal-'+id).find(".header-plus-music-toggle.on");
			if(musicbar.length){
				var bgmusic=musicbar.attr("data-bgmusic"),
				bgmusic_load=musicbar.attr("data-bgmusic_load"),
				bgmusic_volume=musicbar.attr("data-bgmusic_volume");
			    
			   if(bgmusic_load=='on'){
					if(bgmusic){
						var music= new buzz.sound(bgmusic,{
							preload: !1,
							loop: true,
							volume: bgmusic_volume,
						});
					}
					var $this =musicbar;
					music.fadeTo(50, 1e3, function() {
						buzz.all().pause();
					});
					$this.removeClass("on");
					$this.attr("data-bgmusic_load", "off");			
				}
			}
		} );
	
	function playpausemusic($this='', music='', action=''){
		
	}
	
	var WidgetHeaderExtras = function($scope, $) {
		var $container = $scope.find('.header-extra-icons');
		$(".header-extra-toggle-click:not(.open)").on( "click", function(e) {
			e.preventDefault();
            if (!$(this).hasClass('open')) {                
                $(this).closest(".extra-toggle-icon").find('.header-extra-toggle-click.style-2').addClass("open");
                $(this).closest(".extra-toggle-icon").find('.header-extra-toggle-click.style-3').addClass("open");
                $(this).closest(".extra-toggle-icon").find('.header-extra-toggle-click.style-4').addClass("open");
                $(this).closest(".extra-toggle-icon").find('.header-extra-toggle-content').addClass("open");
                $(this).closest(".extra-toggle-icon").find('.extra-toggle-content-overlay').addClass('open');
            }else{
                $(this).closest(".extra-toggle-icon").find('.header-extra-toggle-click.style-2').removeClass("open");
                $(this).closest(".extra-toggle-icon").find('.header-extra-toggle-click.style-3').removeClass("open");
                $(this).closest(".extra-toggle-icon").find('.header-extra-toggle-click.style-4').removeClass("open");
                $(this).closest(".extra-toggle-icon").find('.header-extra-toggle-content').removeClass("open");
                $(this).closest(".extra-toggle-icon").find('.extra-toggle-content-overlay').removeClass('open');
            }
		});
		$('.extra-toggle-close-menu,.header-extra-toggle-click.open .tp-menu-st3').on("click", function(e) {
			e.preventDefault();
			$(this).closest(".extra-toggle-icon").find('.header-extra-toggle-click.style-2').removeClass("open");
			$(this).closest(".extra-toggle-icon").find('.header-extra-toggle-click.style-3').removeClass("open");
			$(this).closest(".extra-toggle-icon").find('.header-extra-toggle-click.style-4').removeClass("open");
			$(this).closest(".extra-toggle-icon").find('.header-extra-toggle-content').removeClass("open");
			$(this).closest(".extra-toggle-icon").find('.extra-toggle-content-overlay').removeClass('open');
		});
		$('.extra-toggle-content-overlay').on( "click", function(e) {
			e.preventDefault();
			$(this).closest(".extra-toggle-icon").find('.header-extra-toggle-click.style-2').removeClass("open");
			$(this).closest(".extra-toggle-icon").find('.header-extra-toggle-click.style-3').removeClass("open");
			$(this).closest(".extra-toggle-icon").find('.header-extra-toggle-click.style-4').removeClass("open");
			$(this).closest(".extra-toggle-icon").find('.header-extra-toggle-content').removeClass("open");
			$(this).removeClass('open');
		});
		
		if($(".header-extra-icons .search-icon",$scope).length){
			var search_container =$(".header-extra-icons .search-icon",$scope);
			var search_icon =search_container.find(".plus-post-search-icon");
			var search_icon_close =search_container.find(".plus-search-close");
			
			search_icon.on('click',function(event){
				var $this=$(this),anim='';
				var form_content=$this.closest(".search-icon").find(".plus-search-form");
				var form_content_style=form_content.data("style");
				var animDuration = 500;
				if((form_content_style=='style-1' || form_content_style=='style-3') && !form_content.hasClass("open")){
					form_content.addClass("open");
					form_content.css({
						opacity: 0,
						display: "block"
					}),
					form_content.css("transform","perspective(200px) translateZ(30px)"),
					form_content.animate({
						transform: "none",
						opacity: 1
					}, {
						ease: "easeOutQuart",
						duration:animDuration,
						complete: function() {
							form_content.css("transform", "none")
						}
					});
				}
				if(form_content_style=='style-2' || form_content_style=='style-4'){
					form_content.toggleClass("open");
				}
			});
			search_icon_close.on('click',function(){
				var $this=$(this),anim='';
				var form_content=$this.closest(".plus-search-form");
				var form_content_style=form_content.data("style");
				var animDuration = 300;
				if((form_content_style=='style-1' || form_content_style=='style-3') && form_content.hasClass("open")){
					form_content.removeClass("open");
					form_content.css("transform","perspective(200px) translateZ(30px)"),
					form_content.animate({
						transform: "perspective(200px) translateZ(30px)",
						opacity: 0
					}, {
						duration :animDuration,
						ease: "easeInQuad",
						complete: function() {
							form_content.css("display", "none")
						}
					});
				}
				if(form_content_style=='style-2' || form_content_style=='style-4'){
					form_content.toggleClass("open");
				}
			});
			/*outside search div close search*/
			$(document).mouseup(function(e) {
				  var container = $(".plus-search-form.plus-search-form-content.style-3");
				  if(!container.is(e.target) && container.has(e.target).length === 0) {					
					container.removeClass('open').css('opacity','0').css('display','none').css('transform','perspective(200px) translateZ(30px)');					
				  }
				  
				  var container_4 = $(".header-extra-icons .plus-search-form.plus-search-form-content.style-4");
				  if(!container_4.is(e.target) && container_4.has(e.target).length === 0) {					
					container_4.removeClass('open');					
				  }
				  
				  var container_2 = $(".header-extra-icons .plus-search-form.plus-search-form-content.style-2");
				  if(!container_2.is(e.target) && container_2.has(e.target).length === 0) {					
					container_2.removeClass('open');					
				  }
			});			
			/*outside search div close search*/
		}
		if($(".header-extra-icons .mini-cart-icon",$scope).length){
		var timeout;
			if($(".header-extra-icons .mini-cart-icon.style-1",$scope).length){
				$('.header-extra-icons .mini-cart-icon.style-1 .content-icon-list').on("mouseover",function(){
					$('.tpmc-header-extra-toggle-content-ext',this).addClass('open');
					$('.widget_shopping_cart',this).addClass('open');
					clearTimeout(timeout);				
				});
				$('body').on('mouseleave','.header-extra-icons .mini-cart-icon.style-1 .content-icon-list',function(){
					var $that = $(this);
					setTimeout(function(){
						if(!$that.is(':hover')){							
							$that.find('.widget_shopping_cart').removeClass('open');
							$that.find('.tpmc-header-extra-toggle-content-ext').removeClass('open');
						}
					},50);
				});
			}
			if($(".header-extra-icons .mini-cart-icon.style-2",$scope).length){
				$(".header-extra-icons .mini-cart-icon.style-2 .content-icon-list .plus-cart-icon").on( "click", function(e) {
					e.preventDefault();
					$(this).addClass("open");					
					$(this).closest(".mini-cart-icon.style-2").find('.tpmc-header-extra-toggle-content').addClass("open");
					$(this).closest(".mini-cart-icon.style-2").find('.tpmc-extra-toggle-content-overlay').addClass('open');
					$(this).closest(".mini-cart-icon.style-2").find('.widget.woocommerce.widget_shopping_cart').addClass('open');
				});
				$('.mini-cart-icon.style-2 .tpmc-extra-toggle-close-menu').on("click", function(e) {
					e.preventDefault();
					$(this).removeClass("open");
					$(this).closest(".mini-cart-icon.style-2").find('.widget.woocommerce.widget_shopping_cart').removeClass('open');
					$(this).closest(".mini-cart-icon.style-2").find('.tpmc-header-extra-toggle-content').removeClass("open");					
					$(this).closest(".mini-cart-icon.style-2").find('.content-icon-list').removeClass("open");
					$(this).closest(".mini-cart-icon.style-2").find('.tpmc-extra-toggle-content-overlay').removeClass('open');
					
				});
				$('.mini-cart-icon.style-2 .tpmc-extra-toggle-content-overlay').on( "click", function(e) {
					e.preventDefault();
					$(this).closest(".mini-cart-icon.style-2").find('.tpmc-extra-toggle-close-menu').trigger('click');
					$(this).removeClass('open');
					
				});
			}
		}
	};
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-header-extras.default', WidgetHeaderExtras);
	});
})(jQuery);