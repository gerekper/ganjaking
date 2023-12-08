(function($) {
	"use strict";
	var WidgetStyleListHandler = function ($scope, $) {
		var $target = $('.plus-stylist-list-wrapper', $scope);
		var $hover_inverse = $('.plus-stylist-list-wrapper.hover-inverse-effect', $scope);
		var $hover_inverse_global = $('.plus-stylist-list-wrapper.hover-inverse-effect-global', $scope);

		if($target.length){

			window.addEventListener('resize', function(event) {
				respLayout()
			});

			respLayout();

			var $read_more =$target.find(".read-more-options");
			if($read_more.length){				
				var default_load =$target.find(".read-more-options").data("default-load");
				var $ul_listing =$target.find(".plus-icon-list-items");
				$ul_listing.each(function(){
				   $(this).find("li:gt("+default_load+")").hide();
				});
				$read_more.on("click", function(e){
					e.preventDefault();
					var $less_text=$(this).data("less-text");
					var $more_text=$(this).data("more-text");
					if($(this).hasClass("more")){
					   $(this).parent(".plus-stylist-list-wrapper").find(".plus-icon-list-items li").show();
					   $(this).text($less_text).addClass("less").removeClass("more");
					}else if($(this).hasClass("less")){
					   $(this).parent(".plus-stylist-list-wrapper").find(".plus-icon-list-items li:gt("+default_load+")").hide();
					   $(this).text($more_text).addClass("more").removeClass("less");
					}
				});
			}
		}
		if($(".plus-bg-hover-effect",$scope).length){
			$('.plus-icon-list-items >li',$target).on('mouseenter', function(e) {
				e.preventDefault();
				if (!$(this).hasClass('active')) {
					var index_el = $(this).index();

					$(this).addClass('active').siblings().removeClass('active');
					$(this).parents(".elementor-widget-tp-style-list").find('.plus-bg-hover-effect .hover-item-content').removeClass('active').eq(index_el).addClass('active');
				} else {
					return false
				}
			});
		}
		if($hover_inverse.length > 0){
			$('.plus-icon-list-items > li',$hover_inverse).on({
				mouseenter: function () {
					$(this).closest(".plus-icon-list-items").addClass("on-hover");
				},
				mouseleave: function () {
					$(this).closest(".plus-icon-list-items").removeClass("on-hover");
				}
			});
		}
		if($target.hasClass("hover-inverse-effect-global")){
			$('.plus-icon-list-items > li',$hover_inverse_global).on({
				mouseenter: function () {
					$('body').addClass("hover-stylist-global");
					var hover_class = $(this).closest(".plus-stylist-list-wrapper").data("hover-inverse");
					$(".hover-inverse-effect-global."+hover_class+" .plus-icon-list-items").addClass("on-hover");
				},
				mouseleave: function () {
					$('body').removeClass("hover-stylist-global");
					var hover_class = $(this).closest(".plus-stylist-list-wrapper").data("hover-inverse");
					$(".hover-inverse-effect-global."+hover_class+" .plus-icon-list-items").removeClass("on-hover");
				}
			});
		}

		function respLayout(){
			let layout = ($target[0] && $target[0].dataset && $target[0].dataset.layout) ? JSON.parse($target[0].dataset.layout) : [],
				desktop_layout = (layout && layout['desktop']) ? layout['desktop'] : "",
				tablet_layout = (layout && layout['desktop']) ? layout['tablet'] : "",
				mobile_layout = (layout && layout['desktop']) ? layout['mobile'] : "",
				body = document.querySelector('body'),
				device = body.dataset.elementorDeviceMode;
	
			classCheck();
			if(device == 'desktop' && desktop_layout == 'tp_sl_l_horizontal'){
				classCheck();
				$target[0].classList.add('tp-sl-l-horizontal');
			} else if(device == 'tablet' && tablet_layout == 'tp_sl_l_horizontal'){
				classCheck();
				$target[0].classList.add('tp-sl-l-horizontal');
			} else if(device == 'mobile' && mobile_layout == 'tp_sl_l_horizontal'){
				classCheck();
				$target[0].classList.add('tp-sl-l-horizontal');
			}
		}

		function classCheck(){
			if($target[0].classList.contains('tp-sl-l-horizontal')){
				$target[0].classList.remove('tp-sl-l-horizontal');
			}
		}

	};
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-style-list.default', WidgetStyleListHandler);
	});

})(jQuery);