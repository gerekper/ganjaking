/*Dynamic Listing*/( function( $ ) {
	"use strict";
	var WidgetDynamicCategoryHandler = function ($scope, $) {
		var container = $scope.find('.dynamic-cat-list.dynamic-cat-style_3.tp-dc-st3-bgimg');
		if(container.length){
			container.find('.grid-item').on('mouseenter',function() {
				var bgimage = $(this).find(".pt-dynamic-wrapper.style_3").data('bgimage');
				$(this).css("background","url("+ bgimage +") center/cover");
			});
			container.find('.grid-item').on('mouseleave',function() {
				$(this).css("background","");
			});
		}		
	};
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-dynamic-categories.default', WidgetDynamicCategoryHandler);
	});
})(jQuery);