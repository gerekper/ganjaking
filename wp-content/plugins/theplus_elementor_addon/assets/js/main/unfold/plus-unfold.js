/*unfold*/
(function ($) {
	"use strict";
	var WidgetUnfoldHandler = function($scope, $) {
		var container = $scope.find('.tp-unfold-wrapper'),
		data_id = container.data("id"),
		data_icon_position = container.data("icon-position"),
		data_content_a_source = container.data("content_a_source"),
		data_co_custom1 = container.data("co_custom1"),
		data_co_c_min_height1 = container.data("co_c_min_height1"),
		data_co_c_opacity_color1 = container.data("co_c_opacity_color1"),
		data_con_pos = container.data("con_pos"),
		data_readmore = container.data("readmore"),
		data_readless = container.data("readless"),
		data_readmore_icon = container.data("readmore-icon"),
		data_readless_icon = container.data("readless-icon"),
		data_content_max_height = container.data("content-max-height"),
		data_content_max_height_m = container.data("content-max-height-m"),
		data_content_max_height_t = container.data("content-max-height-t"),
		data_transition_duration = container.data("transition-duration"),
		toggle1 = container.find(".tp-unfold-toggle"),
		data_scrollTop = container.data('scroll-top-unfold');
		
		if(container.length){
			if(data_content_a_source === 'innersectionbased'){
				if(data_con_pos==='default'){
					var get_height_of_div = container.closest(".elementor-widget-tp-unfold").next('.elementor-inner-section').find(".elementor-container").outerHeight();
					if(data_co_custom1==='yes'){
						var secid = container.closest(".elementor-widget-tp-unfold").next('.elementor-inner-section').data("id");						
						container.closest(".elementor-widget-tp-unfold").next('.elementor-inner-section').append("<style>.elementor-element-"+secid+":not(.tpsecunfold):after{content:'';position:absolute;top:0;bottom:0;left:0;right:0;background:linear-gradient(rgba(255,255,255,0),"+data_co_c_opacity_color1+");z-index:11;top: auto;min-height: "+data_co_c_min_height1+"px;}</style>");
					}else{
						var secid = container.closest(".elementor-widget-tp-unfold").next('.elementor-inner-section').data("id");
						container.closest(".elementor-widget-tp-unfold").next('.elementor-inner-section').append("<style>.elementor-element-"+secid+":not(.tpsecunfold):after{content:'';position:absolute;top:0;bottom:0;left:0;right:0;background:linear-gradient(rgba(255,255,255,0),#00000012);z-index:11}</style>");
					}					
					container.closest(".elementor-widget-tp-unfold").next('.elementor-inner-section').css("height",data_content_max_height).css("overflow","hidden");
				}else if(data_con_pos==='after_button'){
					var get_height_of_div = container.closest(".elementor-widget-tp-unfold").prev('.elementor-inner-section').find(".elementor-container").outerHeight();
					if(data_co_custom1==='yes'){
						var secid = container.closest(".elementor-widget-tp-unfold").prev('.elementor-inner-section').data("id");						
						container.closest(".elementor-widget-tp-unfold").prev('.elementor-inner-section').append("<style>.elementor-element-"+secid+":not(.tpsecunfold):after{content:'';position:absolute;top:0;bottom:0;left:0;right:0;background:linear-gradient(rgba(255,255,255,0),"+data_co_c_opacity_color1+");z-index:11;top: auto;min-height: "+data_co_c_min_height1+"px;}</style>");
					}else{
						var secid = container.closest(".elementor-widget-tp-unfold").prev('.elementor-inner-section').data("id");
						container.closest(".elementor-widget-tp-unfold").prev('.elementor-inner-section').append("<style>.elementor-element-"+secid+":not(.tpsecunfold):after{content:'';position:absolute;top:0;bottom:0;left:0;right:0;background:linear-gradient(rgba(255,255,255,0),#00000012);z-index:11}</style>");
					}
					container.closest(".elementor-widget-tp-unfold").prev('.elementor-inner-section').css("height",data_content_max_height).css("overflow","hidden");
				}
			}else if(data_content_a_source === 'containerbased'){
				if(data_con_pos==='default'){
					var get_height_of_div = container.closest(".elementor-element.e-container,.elementor-element.e-con").next(".elementor-element.e-container,.elementor-element.e-con").outerHeight();                    
					if(data_co_custom1==='yes'){
						var secid = container.closest(".elementor-element.e-container,.elementor-element.e-con").next(".elementor-element.e-container,.elementor-element.e-con").data("id");						
						container.closest(".elementor-element.e-container,.elementor-element.e-con").next(".elementor-element.e-container,.elementor-element.e-con").append("<style>.elementor-element-"+secid+":not(.tpsecunfold):after{content:'';position:absolute;top:0;bottom:0;left:0;right:0;background:linear-gradient(rgba(255,255,255,0),"+data_co_c_opacity_color1+");z-index:11;top: auto;min-height: "+data_co_c_min_height1+"px;}</style>");
					}else{
						var secid = container.closest(".elementor-element.e-container,.elementor-element.e-con").next(".elementor-element.e-container,.elementor-element.e-con").data("id");
						container.closest(".elementor-element.e-container,.elementor-element.e-con").next(".elementor-element.e-container,.elementor-element.e-con").append("<style>.elementor-element-"+secid+":not(.tpsecunfold):after{content:'';position:absolute;top:0;bottom:0;left:0;right:0;background:linear-gradient(rgba(255,255,255,0),#00000012);z-index:11}</style>");
					}					
					container.closest(".elementor-element.e-container,.elementor-element.e-con").next(".elementor-element.e-container,.elementor-element.e-con").css("height",data_content_max_height).css("overflow","hidden");
				}else if(data_con_pos==='after_button'){
					var get_height_of_div = container.closest(".elementor-element.e-container,.elementor-element.e-con").prev(".elementor-element.e-container,.elementor-element.e-con").outerHeight();
					if(data_co_custom1==='yes'){
						var secid = container.closest(".elementor-element.e-container,.elementor-element.e-con").prev(".elementor-element.e-container,.elementor-element.e-con").data("id");						
						container.closest(".elementor-element.e-container,.elementor-element.e-con").prev(".elementor-element.e-container,.elementor-element.e-con").append("<style>.elementor-element-"+secid+":not(.tpsecunfold):after{content:'';position:absolute;top:0;bottom:0;left:0;right:0;background:linear-gradient(rgba(255,255,255,0),"+data_co_c_opacity_color1+");z-index:11;top: auto;min-height: "+data_co_c_min_height1+"px;}</style>");
					}else{
						var secid = container.closest(".elementor-element.e-container,.elementor-element.e-con").prev(".elementor-element.e-container,.elementor-element.e-con").data("id");
						container.closest(".elementor-element.e-container,.elementor-element.e-con").prev(".elementor-element.e-container,.elementor-element.e-con").append("<style>.elementor-element-"+secid+":not(.tpsecunfold):after{content:'';position:absolute;top:0;bottom:0;left:0;right:0;background:linear-gradient(rgba(255,255,255,0),#00000012);z-index:11}</style>");
					}
					container.closest(".elementor-element.e-container,.elementor-element.e-con").prev(".elementor-element.e-container,.elementor-element.e-con").css("height",data_content_max_height).css("overflow","hidden");
				}
			}else{
				var get_height_of_div = container.find(".tp-unfold-description .tp-unfold-description-inner").outerHeight();
			}			

			if(get_height_of_div <= data_content_max_height ){
				container.find(".tp-unfold-last-toggle").css("display", "none");
				if(data_content_a_source === 'innersectionbased'){
					if(data_con_pos==='default'){
						container.closest(".elementor-widget-tp-unfold").next('.elementor-inner-section').after().css("min-height",0);
					}else if(data_con_pos==='after_button'){							
						container.closest(".elementor-widget-tp-unfold").prev('.elementor-inner-section').after().css("min-height",0);
					}
				}else if(data_content_a_source === 'containerbased'){
					if(data_con_pos==='default'){          
						container.closest(".elementor-element.e-container,.elementor-element.e-con").next(".elementor-element.e-container,.elementor-element.e-con").after().css("min-height",0);
					}else if(data_con_pos==='after_button'){
						container.closest(".elementor-element.e-container,.elementor-element.e-con").prev(".elementor-element.e-container,.elementor-element.e-con").after().css("min-height",0);
					}
				}else{
					container.find(".tp-unfold-description").append("<style>.tp-unfold-wrapper."+ data_id + " .tp-unfold-description:after{:0 !important;}</style>");
				}
				
			}else{
				container.find(".tp-unfold-last-toggle").css("display", "flex");
			}
		
			toggle1.on('click',function(){
				var unfold_toggle = $(this).closest('.tp-unfold-wrapper').find(".tp-unfold-toggle");
				var unfold_desc='';
				if(data_content_a_source === 'innersectionbased'){
					if(data_con_pos==='default'){
						unfold_desc = container.closest(".elementor-widget-tp-unfold").next('.elementor-inner-section');
					}else if(data_con_pos==='after_button'){							
						unfold_desc = container.closest(".elementor-widget-tp-unfold").prev('.elementor-inner-section');
					}
				}else if(data_content_a_source === 'containerbased'){
                    if(data_con_pos==='default'){
						unfold_desc = container.closest(".elementor-element.e-container,.elementor-element.e-con").next(".elementor-element.e-container,.elementor-element.e-con");
					}else if(data_con_pos==='after_button'){							
						unfold_desc = container.closest(".elementor-element.e-container,.elementor-element.e-con").prev(".elementor-element.e-container,.elementor-element.e-con");
					}
                }else{
					unfold_desc = $(this).closest('.tp-unfold-wrapper').find(".tp-unfold-description");
				}
			
				$(this).closest('.tp-unfold-wrapper').toggleClass("fullview");
				container.closest(".elementor-widget-tp-unfold").prev('.elementor-inner-section').toggleClass("tpsecunfold");
				container.closest(".elementor-widget-tp-unfold").next('.elementor-inner-section').toggleClass("tpsecunfold");
				/**container compatibility */
				container.closest(".elementor-widget-tp-unfold").closest('.elementor-element.e-container,.elementor-element.e-con').prev('.elementor-element.e-container,.elementor-element.e-con').toggleClass("tpsecunfold");
				container.closest(".elementor-widget-tp-unfold").closest('.elementor-element.e-container,.elementor-element.e-con').next('.elementor-element.e-container,.elementor-element.e-con').toggleClass("tpsecunfold");

				if($(this).closest('.tp-unfold-wrapper').hasClass("fullview") || container.closest(".elementor-widget-tp-unfold").prev('.elementor-inner-section').hasClass("tpsecunfold") || container.closest(".elementor-widget-tp-unfold").next('.elementor-inner-section').hasClass("tpsecunfold")){
					if(data_content_a_source === 'innersectionbased'){
						if(data_con_pos==='default'){
							var outerhight = parseInt(container.closest(".elementor-widget-tp-unfold").next('.elementor-inner-section').find(".elementor-container").outerHeight());
						}else if(data_con_pos==='after_button'){
							var outerhight = parseInt(container.closest(".elementor-widget-tp-unfold").prev('.elementor-inner-section').find(".elementor-container").outerHeight());
						}
					}else if(data_content_a_source === 'containerbased'){                        
							var outerhight = get_height_of_div;
                    }else{
						var outerhight = parseInt($(this).closest('.tp-unfold-wrapper').find(".tp-unfold-description-inner").outerHeight());
					}
					
					
					if(data_icon_position=='tp_ic_pos_before' && data_icon_position !=undefined){
						unfold_toggle.html(data_readless_icon + data_readless);
					}else if(data_icon_position=='tp_ic_pos_after' && data_icon_position !=undefined){
						unfold_toggle.html(data_readless + data_readless_icon);
					}
					
					if(data_content_a_source === 'innersectionbased' || data_content_a_source === 'containerbased'){
						if(data_con_pos==='default'){
							unfold_desc.animate({height:outerhight},data_transition_duration);

							let isAccordion = container.closest(".elementor-widget-tp-unfold").next('.elementor-inner-section.tpsecunfold').find(".theplus-accordion-wrapper");
							if ( isAccordion.length > 0 ) {
								setTimeout(() => {
									$(unfold_desc).css("height", "auto");
								}, 1000);
							}
						}else if(data_con_pos==='after_button'){
							unfold_desc.animate({height:outerhight},data_transition_duration);
						}
					}else{
						unfold_desc.animate({height:outerhight},data_transition_duration);
					}
					
				}else{
					
					if(data_icon_position=='tp_ic_pos_before' && data_icon_position !=undefined){						
						unfold_toggle.html(data_readmore_icon + data_readmore);
					}else if(data_icon_position=='tp_ic_pos_after' && data_icon_position !=undefined){
						unfold_toggle.html(data_readmore + data_readmore_icon);
					}
					
					var inw = $(window).innerWidth();
					if(inw >= 1025){
						unfold_desc.animate({height:data_content_max_height},data_transition_duration);
					}else if(inw <= 1024 && inw >= 768){
						unfold_desc.animate({height:data_content_max_height_t},data_transition_duration);						
					}else if(inw <= 767 ){
						unfold_desc.animate({height:data_content_max_height_m},data_transition_duration);				
					}
					if(data_scrollTop == 'yes'){
						TopscrollTo();
					}
				}
				if(container.closest(".post-inner-loop").length){	
					setTimeout(function(){
						container.closest(".post-inner-loop").isotope('layout');						
					}, data_transition_duration - 20);
				}
				
			});

			var TopscrollTo = function () {
				setTimeout(() => {
					jQuery('html,body').animate({
						scrollTop: (jQuery('.tp-unfold-title').offset().top - 50) 
					}, 300);
				}, 100);
			}
		}		
	};	
	
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-unfold.default', WidgetUnfoldHandler);
	});
})(jQuery);