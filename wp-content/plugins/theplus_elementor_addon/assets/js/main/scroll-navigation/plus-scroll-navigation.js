( function( $ ) {
	"use strict";
	var WidgetScrollNavHandler = function($scope, $) {
		var scroll_nav = $scope.find('.theplus-scroll-navigation'),
            offset = scroll_nav.data("scroll-top-offset");

		if(scroll_nav.length > 0 ){
			if(scroll_nav.data("pagescroll") =='yes'){
				scroll_nav.find(".theplus-scroll-navigation__item:eq(0)").addClass("highlight");
				$(".theplus-scroll-navigation__item").on('click',function(e){
					e.preventDefault();
					if(!$(this).hasClass("highlight")){
						var id=$(this).attr("href");
						var itemId = id.substring(1, id.length);
						if($(this).closest(".theplus-scroll-navigation").data("pagescroll-type")=='fullpage'){
							fullpage_api.moveTo(itemId);
						}else if($(this).closest(".theplus-scroll-navigation").data("pagescroll-type")=='pagepiling'){
							if($('.tp_page_pilling').length>0){
								var container=$('.tp_page_pilling');
								var opt = container.data("page-piling-opt");
								var inw = $(window).innerWidth();
								if((inw <= 1024 && inw >= 768) && (opt['pp_tablet_off']=='no' || opt['pp_tablet_off']==undefined )){
									$.fn.pagepiling.moveTo(itemId);
								}else if((inw <= 767 ) && (opt['pp_mobile_off']=='no' || opt['pp_tablet_off']==undefined )){
									$.fn.pagepiling.moveTo(itemId);
								}else if(inw >= 1025){
									$.fn.pagepiling.moveTo(itemId);
								}else{
									$(".theplus-scroll-navigation__item").mPageScroll2id({
										highlightSelector:".theplus-scroll-navigation__item",
										highlightClass:"highlight",
										forceSingleHighlight:true,
									});

									$(".theplus-scroll-navigation__item").on('click',function(e){
										e.preventDefault();
										var to=$(this).parent().parent("section").next().attr("id");
										$.mPageScroll2id("scrollTo",to);
									});
								}
							}
						}else if($(this).closest(".theplus-scroll-navigation").data("pagescroll-type")=='multiscroll'){
							$.fn.multiscroll.moveTo(itemId);
						}
						$(this).parent().find(".highlight").removeClass("highlight");
						$(this).addClass("highlight");
					}
				});
			}else{
				$(".theplus-scroll-navigation__item").mPageScroll2id({
					highlightSelector:".theplus-scroll-navigation__item",
					highlightClass:"highlight",
					forceSingleHighlight:true,
                    offset:offset,
				});

				$(".theplus-scroll-navigation__item").on('click',function(e){
					e.preventDefault();
					var to=$(this).parent().parent("section").next().attr("id");
					$.mPageScroll2id("scrollTo",to);

                    
				});
			}
		}
		var container = $scope.find('.theplus-scroll-navigation.scroll-view');
		var container_scroll_view = $scope.find('.theplus-scroll-navigation__inner');
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
	};
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-scroll-navigation.default', WidgetScrollNavHandler);
	});
})(jQuery);