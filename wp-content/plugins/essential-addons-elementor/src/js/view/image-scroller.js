
var ImageScroller = function($scope, $) {
	$(".eael-image-scroller-hover", $scope).hover(
		function() {
			if ($(this).hasClass("eael-image-scroller-vertical")) {
				var $container_height = parseInt($(this).css("height"));
				var $image_height = $("img", $(this)).height();
				var $translate = $container_height - $image_height;

				if ($translate > 0) {
					return;
				}

				$("img", $(this)).css({
					transform: "translateY(" + $translate + "px)"
				});
			} else if ($(this).hasClass("eael-image-scroller-horizontal")) {
				var $container_width = parseInt($(this).width());
				var $image_width = $("img", $(this)).width();
				var $translate = $container_width - $image_width;

				if ($translate > 0) {
					return;
				}

				$("img", $(this)).css({
					transform: "translateX(" + $translate + "px)"
				});
			}
		},
		function() {
			$("img", $(this)).css({
				transform: "translate(0)"
			});
		}
	);
};

jQuery(window).on("elementor/frontend/init", function() {
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/eael-image-scroller.default",
		ImageScroller
	);
});
