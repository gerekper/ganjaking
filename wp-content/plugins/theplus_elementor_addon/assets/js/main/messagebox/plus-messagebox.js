/*Message Box*/
(function ($) {
	"use strict";
	var WidgetMessageBoxHandler = function($scope, $) {
		var container = $scope.find('.tp-messagebox'),
		speed = container.data('speed');
		if(container.length){
			container.find(".msg-dismiss-content").on('click',function(){				
				$(this).closest(container).slideUp(speed);
			});
		}
	};
$(window).on('elementor/frontend/init', function () {
	elementorFrontend.hooks.addAction('frontend/element_ready/tp-messagebox.default', WidgetMessageBoxHandler);
});
})(jQuery);