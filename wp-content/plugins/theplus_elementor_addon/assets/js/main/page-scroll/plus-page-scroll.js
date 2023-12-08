/*Page Scroll*/(function(a) {
	'use strict';
	var b = function(a, b) {
        return this.init(a, b)
    };
    var dev = [];
	b.defaults = {
		licenseKey: '845A4AB1-B87A4168-BA7C13F1-54120148',
		navigationTooltips: !0,
		navigation: true, 
		navigationPosition: 'right',
		showActiveTooltip: true,
		slidesNavigation: true,
		controlArrows: true,
		easingcss3: 'cubic-bezier(.32,.18,.22,1)',
		scrollingSpeed: 850,
		keyboardScrolling: false,
		responsiveWidth: 900,
		normalScrollElements: '.tp-fp-section-scrollable > .fp-tableCell > .elementor',
    },	
	b.prototype = {
        init: function(a, b) {
			return a.data("tponePage") ? this : (this.el = a,
           this.setOptions(b).build(),
            this)
		},
		setOptions: function(c) {
            return this.el.data("__tsonePage", this),
            this.options = a.extend(!0, {}, b.defaults, c),
            this
			
        },
		build: function() {
			var b = a(this.el),c = this.options;
			
			var uid = b.data('id');
			b.fullpage(a.extend(!0, {}, this.options, {
				onLeave: function(b, c) {
					var d = a(this);
					
					//paginate
					a('.fullpage-nav-paginate .slide-nav').removeClass("active animated");
					a('.fullpage-nav-paginate .slide-nav[data-slide='+parseInt(c.index)+']').addClass("active animated");
					
					//scroll nav
					var id=b.item;
					var ids=a(id).closest(".tp-page-scroll-wrapper").data("scroll-nav-id");
					if(ids!='' && ids!=undefined){
						a('#'+ids).find('.highlight').removeClass("highlight");
						a('#'+ids).find('a:eq(' + parseInt(c.index) + ')').addClass("highlight");
					}

				},
				afterLoad: function(origin, destination, direction){
					var scrolloverflow = b.data("scrolloverflow");
					if(scrolloverflow=='yes' && scrolloverflow != 'undefined'){
						if( direction == 'down' ){						
							dev.push(destination.item.parentElement.style.transform);
						}
						if( direction == 'up' ){
							let getsection = document.querySelectorAll('.section.fp-section');
								getsection.forEach(function(self, index, get) {
									if( self.classList.contains('active') ){                                                                
										if(index == 0){       
											destination.item.parentElement.style.transform = 'translate3d(0px, 0px, 0px)';
                                            destination.item.querySelector(".fp-tableCell > .elementor").scrollTo({ top: 0, behavior: 'smooth' });
										}else{
											destination.item.parentElement.style.transform = dev[index -1];
                                            destination.item.querySelector(".fp-tableCell > .elementor").scrollTo({ top: 1, behavior: 'smooth' });
                                        }
									}
								});
						} 
					}
                    
                    //Animation
					var tp_anim_cls = a("#"+uid).find('.fp-section:not(.active)');
					tp_anim_cls.find('.animate-general').removeClass('animation-done');
					tp_anim_cls.find('.animate-general').css('opacity','0');
					tp_anim_cls.find('.pt_plus_animated_image.bg-img-animated').removeClass('creative-animated');
					
					var tp_anim = a("#"+uid).find('.fp-section.active');
					a(tp_anim).find('.animate-general:not(.animation-done)').each(function() {
						var d;
						var b = a(this);
						var delay_time=b.data("animate-delay");
						d = b.data("animate-type");
						if(b.hasClass("animation-done")){
							b.hasClass("animation-done");
						}else{
							b.addClass("animation-done").velocity(d, {delay: delay_time,display:'auto'});
						}
					});
					
					/*load draw svg*/
					if(a(tp_anim).find(".pt_plus_animated_svg").length > 0){
						a('.pt_plus_animated_svg',tp_anim).pt_plus_animated_svg();
					}
					
					if(a(tp_anim).find('.pt_plus_animated_image.bg-img-animated').length > 0){
						a(tp_anim).find('.pt_plus_animated_image.bg-img-animated').each(function() {
							var b=a(this);
							if(b.hasClass("creative-animated")){
								b.hasClass("creative-animated");
								}else{
								b.addClass("creative-animated");
							}							
						});
					}
					if(a("#"+uid).find(".fp-section .elementor-widget[data-settings]").length > 0){
						tp_anim_cls.find(".elementor-widget.animated").each(function() {
							var t=a(this), b = a(this).data("settings");
							if(b!=undefined && b._animation){
								t.addClass("elementor-invisible").removeClass(b._animation +" animated");
							}
						});
						tp_anim_cls.find(".elementor-column.animated").each(function() {
							var t=a(this), b = a(this).data("settings");
							if(b!=undefined && b.animation){
								t.addClass("elementor-invisible").removeClass(b.animation +" animated");
							}
						});
						tp_anim.find(".elementor-widget:not(.animated)").each(function() {
							var t=a(this), b = a(this).data("settings");
							if(b!=undefined && b._animation && b._animation_delay){
								setTimeout(function(){
									t.removeClass("elementor-invisible").addClass(b._animation + ' animated');	
								}, b._animation_delay);								
							}else if(b!=undefined){
								t.removeClass("elementor-invisible").addClass(b._animation + ' animated');
							}
						});
						tp_anim.find(".elementor-column:not(.animated)").each(function() {
							var t=a(this), b = a(this).data("settings");
							if(b!=undefined && b.animation && b.animation_delay){
								setTimeout(function(){
									t.removeClass("elementor-invisible").addClass(b.animation + ' animated');	
								}, b.animation_delay);								
							}else if(b!=undefined){
								t.removeClass("elementor-invisible").addClass(b.animation + ' animated');
							}
						});
					}
				}
			})),
		  
			//Next Sections
			a('.fp-nav-btn.fp-nav-next').on('click', function(b) {
				b.preventDefault();
				a.fn.fullpage.moveSectionDown();
			});
			//Prev Sections
			a('.fp-nav-btn.fp-nav-prev').on('click', function(b) {
				b.preventDefault();
				a.fn.fullpage.moveSectionUp();
			});
			
			var	uid=b.data("id"),
			show_paginate= b.data("show_paginate"),
			paginate_style= b.data("paginate_style"),
			paginate_position= b.data("paginate_position");
			
			//paginate
			if(show_paginate=='on'){			
				var slide_length=a('.tp-page-scroll-wrapper.tp_full_page .section.fp-section').length;
				var content_length='';
				content_length+='<div class="fullpage-nav-paginate '+paginate_position+'">';
				for(var i=0;i<slide_length;i++){
					if(i==0){
						 content_length+='<span class="slide-nav '+paginate_style+' active animated" data-slide="'+parseInt(i)+'">'+(i < 9 ? '0'+parseInt(i+1) : ''+parseInt(i+1))+'</span>';
					}else{
					content_length+='<span class="slide-nav '+paginate_style+' " data-slide="'+i+'">'+(i < 9 ? '0'+parseInt(i+1) : ''+parseInt(i+1))+'</span>';
					}
				}
				if(i < 10){
					content_length+='<span class="total-page-nav">0'+(i)+'</span>';
				}else{
					content_length+='<span class="total-page-nav">'+(i)+'</span>';
				}
				
				content_length+='</div>';
				var main_div=a('#'+uid).parent();
				a(main_div).append(content_length);					
			}
			
			//footer
			var	show_footer= b.data("show_footer");
			if(show_footer=='yes' && show_footer != 'undefined'){
				a( ".tp-page-scroll-wrapper .section.fp-section" ).last().addClass( "fp-auto-height" );
			}

                var scrolloverflowscroll = b.data("scrolloverflowscroll");
                if(scrolloverflowscroll=='yes' && scrolloverflowscroll != 'undefined'){
                    var scnheight = a(window).height();
                    var scnwidth = a(window).width();
                    
                    const section = a(".section.fp-section");
                    
                    section.each(function (data,index){					
                        var height = a(this)[0].querySelector('.fp-tableCell .elementor-section,.fp-tableCell .elementor-inner').clientHeight;
                        var heigh1t = a(this)[0].querySelector('.fp-tableCell');
                        if(scnheight < height){												
                            a(this).addClass( "tp-fp-section-scrollable" );
                            a(this)[0].querySelector(".fp-tableCell .elementor").style.width=scnwidth+"px";
                            a(this)[0].querySelector(".fp-tableCell .elementor").style.height=scnheight+'px';
                            var scroller = a(this)[0].querySelector(".fp-tableCell .elementor");
                            scroller.addEventListener("scroll", (event) =>{
                                var scrollTop = Math.ceil(scroller.scrollTop);
                                if((scrollTop + scroller.clientHeight) >= height){
                                    if(a(this)[0].nextElementSibling){
                                        fullpage_api.moveTo(a(this)[0].nextElementSibling.dataset.anchor);
                                    }									
                                }else if(scrollTop <= 0){
                                    if(a(this)[0].previousElementSibling){
                                        fullpage_api.moveTo(a(this)[0].previousElementSibling.dataset.anchor);   
                                    }									
                                }
                            });
                        }
                    });
                }
		}
	}
	
	a.fn.PlusFullPage = function(c) {
        return this.map(function() {
            var d = a(this);            
            var e, f = d.data('full-page-opt');
            return f && (e = a.extend(!0, {}, c, f)),
            new b(d,e)
        })
    }
}
).apply(this, [jQuery]);

(function ($) {
	var WidgetPageScrollHandler = function($scope, $) {		
		var container=$scope.find('.tp-page-scroll-wrapper');
		var uid = container.data('id');	
	
		if($scope.find('.tp-page-scroll-wrapper.tp_full_page').length>0){
			$("#fp-nav").remove();
			if ( $( 'html' ).hasClass( 'fp-enabled' ) ) {
				$.fn.fullpage.destroy('all');
			}
			$('.tp-page-scroll-wrapper.tp_full_page').PlusFullPage();
			
		}
			
		/*page piling start*/
		if($scope.find('.tp-page-scroll-wrapper.tp_page_pilling').length>0){
			$("#pp-nav").remove();
			var container=$scope.find(".tp_page_pilling");
			var opt = container.data("page-piling-opt");
			var obj = {};
			if(opt['navigation']['display']==true){
				obj['navigation'] = {};
				obj.navigation.position = opt['navigation']['position'];
				obj.navigation.tooltips = opt['navigation']['tooltips'];
			}else{
				obj.navigation=false;
			}
			var inw = $(window).innerWidth();
			var on_off = 'enable';
			if((inw <= 1024 && inw >= 768) && opt['pp_tablet_off']=='yes'){
				on_off = 'disable';					
				$('.fp-nxt-prev').addClass('tab-hidden');
				
			}
			if((inw <= 767 ) && opt['pp_mobile_off']=='yes'){
				on_off = 'disable';					
				$('.fp-nxt-prev').addClass('mob-hidden');					
			}
			
			if(on_off=='enable'){
				$('#pagepiling').pagepiling({
					 menu: null,
					direction: opt['direction'],
					verticalCentered: true,
					sectionsColor: [],
					anchors: opt["anchors"],
					scrollingSpeed: opt["scrollingSpeed"],
					easing: 'swing',
					loopBottom: opt["loopBottom"],
					loopTop: opt["loopTop"],
					css3: true,
					navigation: obj.navigation,
					normalScrollElements: null,
					normalScrollElementTouchThreshold: 5,
					touchSensitivity: 5,
					keyboardScrolling: opt["keyboardScrolling"],
					sectionSelector: '.section',
					animateAnchor: false,

					onLeave: function(index, nextIndex, direction){					
						if(direction=='down'){						
							$('.fullpage-nav-paginate .slide-nav').removeClass("active animated");
							$('.fullpage-nav-paginate .slide-nav[data-slide='+parseInt(nextIndex-1)+']').addClass("active animated");
						}else{
							$('.fullpage-nav-paginate .slide-nav').removeClass("active animated");
							$('.fullpage-nav-paginate .slide-nav[data-slide='+parseInt(nextIndex-1)+']').addClass("active animated");
						}
						
						//scroll nav
						var curr_div=container.find('.pp-section:eq(' + parseInt(index-1) + ')');
						var ids=$(curr_div).closest(".tp-page-scroll-wrapper").data("scroll-nav-id");
						if(ids!='' && ids!=undefined){
							$('#'+ids).find('.highlight').removeClass("highlight");
							$('#'+ids).find('a:eq(' + parseInt(nextIndex-1) + ')').addClass("highlight");
						}
						
						//Animation widgets
						var tp_anim_cls = container.find('.pp-section:not(.active)');
						if(container.find(".pp-section .elementor-widget[data-settings]").length > 0){
							$(tp_anim_cls).find(".elementor-widget[data-settings].animated").each(function() {
								var t=$(this), b = $(this).data("settings");
								if(b._animation){
									t.addClass("elementor-invisible").removeClass(b._animation +" animated");
								}
							});
						}
						
						//Animation columns
						var tp_anim_cls = container.find('.pp-section:not(.active)');
						if(container.find(".pp-section .elementor-column[data-settings]").length > 0){
							$(tp_anim_cls).find(".elementor-column[data-settings].animated").each(function() {
								var t=$(this), b = $(this).data("settings");
								if(b.animation){
									t.addClass("elementor-invisible").removeClass(b.animation +" animated");
								}
							});
						}
					},
					afterLoad: function(anchorLink, index){
						//Animation
						var tp_anim_cls = container.find('.pp-section:not(.active)');
						tp_anim_cls.find('.animate-general').removeClass('animation-done');
						tp_anim_cls.find('.animate-general').css('opacity','0');
						
						var tp_anim = container.find('.pp-section.active');
						$(tp_anim).find('.animate-general:not(.animation-done)').each(function() {
								var d;
								var b = $(this);
								var delay_time=b.data("animate-delay");
								d = b.data("animate-type");
								if(b.hasClass("animation-done")){
									b.hasClass("animation-done");
								}else{
									b.addClass("animation-done").velocity(d, {delay: delay_time,display:'auto'});
								}
						});
						
						/*load draw svg*/
						if(container.find(".pt_plus_animated_svg").length > 0){
							$('.pt_plus_animated_svg',tp_anim).pt_plus_animated_svg();
						}
						
						if(container.find(".pp-section .elementor-widget[data-settings]").length > 0){
							
							$(tp_anim).find(".elementor-widget[data-settings]:not(.animated)").each(function() {
								var t=$(this), b = $(this).data("settings");
								if(b._animation && b._animation_delay){
									setTimeout(function(){
										t.removeClass("elementor-invisible").addClass(b._animation + ' animated');	
									}, b._animation_delay);								
								}else{
									t.removeClass("elementor-invisible").addClass(b._animation + ' animated');
								}
							});
						}
						if(container.find(".pp-section .elementor-column[data-settings]").length > 0){
							
							$(tp_anim).find(".elementor-column[data-settings]:not(.animated)").each(function() {
								var t=$(this), b = $(this).data("settings");
								if(b.animation && b.animation_delay){
									setTimeout(function(){
										t.removeClass("elementor-invisible").addClass(b.animation + ' animated');	
									}, b.animation_delay);								
								}else{
									t.removeClass("elementor-invisible").addClass(b.animation + ' animated');
								}
							});
						}
					}
				});
				
				//Next Section
				$('.fp-nav-btn.fp-nav-next').on('click', function(b) {
					b.preventDefault();
					$.fn.pagepiling.moveSectionDown();
				});
				//Prev Section
				$('.fp-nav-btn.fp-nav-prev').on('click', function(b) {
					b.preventDefault();
					$.fn.pagepiling.moveSectionUp();
				});
			}
			
			var sf = $scope.find('.tp-page-scroll-wrapper.tp_page_pilling');
			var nav_position = sf.data("postion"),
			show_paginate= sf.data("show_paginate"),
			paginate_style= sf.data("paginate_style"),
			paginate_position= sf.data("paginate_position");
		
			$('body').find('#fp-nav').addClass(nav_position);
			
			//Paginate
			if(show_paginate=='on' && on_off=='enable'){
				var slide_length=$('.tp-page-scroll-wrapper.tp_page_pilling .section.pp_section').length;
				var content_length='';
				content_length+='<div class="fullpage-nav-paginate '+paginate_position+'">';
				for(var i=0;i<slide_length;i++){
					if(i==0){
						 content_length+='<span class="slide-nav '+paginate_style+' active animated" data-slide="'+parseInt(i)+'">'+(i < 9 ? '0'+parseInt(i+1) : ''+parseInt(i+1))+'</span>';
					}else{
					content_length+='<span class="slide-nav '+paginate_style+' " data-slide="'+i+'">'+(i < 9 ? '0'+parseInt(i+1) : ''+parseInt(i+1))+'</span>';
					}
				}
				if(i < 10){
					content_length+='<span class="total-page-nav">0'+(i)+'</span>';
				}else{
					content_length+='<span class="total-page-nav">'+(i)+'</span>';
				}
				content_length+='</div>';
				var main_div=$('#'+uid).parent();
				$(main_div).append(content_length);											
			}
		}
		/*page piling end*/
		
		/*multi scroll start*/
		if($scope.find('.tp-page-scroll-wrapper.tp_multi_scroll').length>0){
			$("#pp-nav").remove();
			var container=$scope.find(".tp_multi_scroll");
			var opt = container.data("multi-scroll-opt");
			var multi_scroll_elem = $scope.find(".theplus-multiscroll-wrap"),
			multi_scroll_opt = multi_scroll_elem.data("settings"),
			multi_id = multi_scroll_opt["multi_id"];

			$("#theplus-scroll-nav-menu-" + multi_id).removeClass("theplus-scroll-responsive");
			  
			function multiScrollLoadFunc(){
				$("#theplus-multiscroll-" + multi_id).multiscroll({
					verticalCentered: true,
					scrollingSpeed: multi_scroll_opt["scrollingSpeed"],
					easing: 'easeInQuart',
					menu: "#theplus-scroll-nav-menu-" + multi_id,
					sectionsColor: [],				
					navigation: multi_scroll_opt["dots"],
					navigationPosition: multi_scroll_opt["dotsPosition"]+' '+multi_scroll_opt["dotsVertical"],
					navigationColor: "#000",
					navigationTooltips: multi_scroll_opt["dotsTooltips"],				
					loopBottom: multi_scroll_opt["loopBottom"],
					loopTop: multi_scroll_opt["loopTop"],
					css3: true,
					paddingTop: 0,
					paddingBottom: 0,
					normalScrollElements: null,				
					keyboardScrolling: multi_scroll_opt["keyboardScrolling"],
					touchSensitivity: 5,
					responsiveWidth: 0,
					responsiveHeight: 0,
					responsiveExpand: false,
					anchors: opt["anchors"],
					sectionSelector: ".theplus-multiscroll-temp-" + multi_id,
					leftSelector: ".theplus-multiscroll-left-" + multi_id,
					rightSelector: ".theplus-multiscroll-right-" + multi_id,
					leftSide: multi_scroll_opt['leftSide'],
					rightSide: multi_scroll_opt['rightSide'],

					onLeave: function(index, nextIndex, direction){
						if(direction=='down'){						
							$('.fullpage-nav-paginate .slide-nav').removeClass("active animated");
							$('.fullpage-nav-paginate .slide-nav[data-slide='+parseInt(nextIndex-1)+']').addClass("active animated");
						}else{
							$('.fullpage-nav-paginate .slide-nav').removeClass("active animated");
							$('.fullpage-nav-paginate .slide-nav[data-slide='+parseInt(nextIndex-1)+']').addClass("active animated");
						}

						//scroll nav
						var curr_div=container.find('.theplus-multiscroll-inner');
						var ids=$(curr_div).closest(".tp-page-scroll-wrapper").data("scroll-nav-id");
						if(ids!='' && ids!=undefined){
							$('#'+ids).find('.highlight').removeClass("highlight");
							$('#'+ids).find('a:eq(' + parseInt(nextIndex-1) + ')').addClass("highlight");
						}
						//alert(nextIndex);
						
					},
					afterLoad: function(anchorLink, index){
						//Animation
						var tp_anim_cls = container.find('.theplus-multiscroll-temp:not(.active)');
						tp_anim_cls.find('.animate-general').removeClass('animation-done');
						tp_anim_cls.find('.animate-general').css('opacity','0');
						
						var tp_anim = container.find('.theplus-multiscroll-temp.active');
						$(tp_anim).find('.animate-general:not(.animation-done)').each(function() {
							var d;
							var b = $(this);
							var delay_time=b.data("animate-delay");
							d = b.data("animate-type");
							if(b.hasClass("animation-done")){
								b.hasClass("animation-done");
							}else{
								b.addClass("animation-done").velocity(d, {delay: delay_time,display:'auto'});
							}
						});						
						
						/*load draw svg*/
						if(container.find(".pt_plus_animated_svg").length > 0){
							$('.pt_plus_animated_svg',tp_anim).pt_plus_animated_svg();
						}
						
						if(container.find(".theplus-multiscroll-temp .elementor-widget[data-settings]").length > 0){
							$(tp_anim_cls).find(".elementor-widget[data-settings].animated").each(function() {
								var t=$(this), b = $(this).data("settings");
								if(b._animation){
									t.addClass("elementor-invisible").removeClass(b._animation +" animated");
								}
							});
							$(tp_anim_cls).find(".elementor-column[data-settings].animated").each(function() {
								var t=$(this), b = $(this).data("settings");
								if(b.animation){
									t.addClass("elementor-invisible").removeClass(b.animation +" animated");
								}
							});
							$(tp_anim).find(".elementor-widget[data-settings]:not(.animated)").each(function() {
								var t=$(this), b = $(this).data("settings");
								if(b._animation && b._animation_delay){
									setTimeout(function(){
										t.removeClass("elementor-invisible").addClass(b._animation + ' animated');	
									}, b._animation_delay);								
								}else{
									t.removeClass("elementor-invisible").addClass(b._animation + ' animated');
								}
							});
							$(tp_anim).find(".elementor-column[data-settings]:not(.animated)").each(function() {
								var t=$(this), b = $(this).data("settings");
								if(b.animation && b.animation_delay){
									setTimeout(function(){
										t.removeClass("elementor-invisible").addClass(b.animation + ' animated');	
									}, b.animation_delay);
								}else{
									t.removeClass("elementor-invisible").addClass(b.animation + ' animated');
								}
							});
						}
					}

				});
			}
			
			function multiScrollNormal() {
				var tempate_left = $(multi_scroll_elem).find(".theplus-multiscroll-left-temp");
				$(multi_scroll_elem).find(".theplus-multiscroll-right-temp").each(function(e) {					
					$(this).insertAfter(tempate_left[e]);
				});
				$(multi_scroll_elem).parents(".elementor-top-section,.elementor-element.e-container,.elementor-element.e-con").removeClass("elementor-section-height-full");			  
			}
			
			var responsive_Device = $("body").data("elementor-device-mode");
			
			if(multi_scroll_opt['disable_tablet'] === 'yes' && multi_scroll_opt['disable_mobile'] ==='yes' && "desktop" === responsive_Device){
				multiScrollLoadFunc();
			}else if((multi_scroll_opt['disable_tablet'] === 'yes' && multi_scroll_opt['disable_mobile'] === 'no') && ("mobile" === responsive_Device || "desktop" === responsive_Device)){
				multiScrollLoadFunc();
			}else if((multi_scroll_opt['disable_mobile'] === 'yes' && multi_scroll_opt['disable_tablet'] === 'no') && ("tablet" === responsive_Device || "desktop" === responsive_Device) ){
				multiScrollLoadFunc();
			}else if(multi_scroll_opt['disable_tablet'] === 'no' && multi_scroll_opt['disable_mobile'] === 'no' ){
				multiScrollLoadFunc();
			}else{
				multiScrollNormal();
			}
			
			$('.fp-nav-btn.fp-nav-next').on('click', function(b) {					
				b.preventDefault();					
				$.fn.multiscroll.moveSectionDown();					
			});
			$('.fp-nav-btn.fp-nav-prev').on('click', function(b) {					
				b.preventDefault();
				$.fn.multiscroll.moveSectionUp();
			});
			
			var msw = $scope.find('.tp-page-scroll-wrapper.tp_multi_scroll');
			var show_paginate= msw.data("show_paginate"),
			paginate_style= msw.data("paginate_style"),
			paginate_position= msw.data("paginate_position");
		
			$('body').find('#fp-nav').addClass(nav_position);
			
			
			//paginate
			if(show_paginate=='on'){			
				var slide_length=$('.tp-page-scroll-wrapper.tp_multi_scroll .theplus-multiscroll-left-temp.ms-section').length;						
				var content_length='';
				content_length+='<div class="fullpage-nav-paginate '+paginate_position+'">';
				for(var i=0;i<slide_length;i++){
					if(i==0){
						 content_length+='<span class="slide-nav '+paginate_style+' active animated" data-slide="'+parseInt(i)+'">'+(i < 9 ? '0'+parseInt(i+1) : ''+parseInt(i+1))+'</span>';
					}else{
					content_length+='<span class="slide-nav '+paginate_style+' " data-slide="'+i+'">'+(i < 9 ? '0'+parseInt(i+1) : ''+parseInt(i+1))+'</span>';
					}
				}
				if(i < 10){
					content_length+='<span class="total-page-nav">0'+(i)+'</span>';
				}else{
					content_length+='<span class="total-page-nav">'+(i)+'</span>';
				}
				content_length+='</div>';
				var main_div=$('#'+uid).parent();
				$(main_div).append(content_length);					
			}
			
		}
		/*multi scroll end*/
		
		/*Hscroll bg start*/	
		
		var parent_row= container.closest('section.elementor-element,.elementor-element.e-container,.elementor-element.e-con');
		var pageWrapper = $('.plus-scroll-sections-bg');
		if(pageWrapper.length > 0){
			var scroll_section_bg=$scope.closest('.elementor').find("> .plus-scroll-sections-bg");
			scroll_section_bg.remove();
			if($scope.find('.tp_hscroll_root .plus-scroll-sections-bg').length>0){
				var scroll_sec_bg= $scope.find('.plus-scroll-sections-bg');
				var position=scroll_sec_bg.data("position");
				container.closest('.elementor').prepend(scroll_sec_bg);
				container.closest('.elementor').css("position",position);				
			}
		}
		
		/*Hscroll bg start*/
	};

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-page-scroll.default', WidgetPageScrollHandler);
	});
})(jQuery);