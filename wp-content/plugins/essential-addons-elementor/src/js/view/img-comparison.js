
var ImageComparisonHandler = function ($scope, $) {
	var $img_comp = $(".eael-img-comp-container", $scope);
	var $options = {
		default_offset_pct: $img_comp.data("offset") || 0.7,
		orientation: $img_comp.data("orientation") || "horizontal",
		before_label: $img_comp.data("before_label") || "Before",
		after_label: $img_comp.data("after_label") || "After",
		no_overlay: $img_comp.data("overlay") == "yes" ? false : true,
		move_slider_on_hover: $img_comp.data("onhover") == "yes" ? true : false,
		move_with_handle_only: true,
		click_to_move: $img_comp.data("onclick") == "yes" ? true : false
	};

    var $tabContainer = $('.eael-advance-tabs'),
		nav = $tabContainer.find('.eael-tabs-nav li'),
		tabContent = $tabContainer.find('.eael-tabs-content > div');

	nav.on('click', function() {
		var currentContent = tabContent.eq($(this).index()),
			$imagCompExist = $(currentContent).find('.eael-img-comp-container');
		if($imagCompExist.length) {
			$img_comp.imagesLoaded().done(function () {
				$img_comp.find('div').remove();
				$img_comp.find('img').removeClass('twentytwenty-before twentytwenty-after').removeAttr('style');
				$img_comp.closest('.elementor-widget-container').html($img_comp);
				$img_comp.eatwentytwenty($options);
			});
		}
	});
	

	$img_comp.imagesLoaded().done(function () {
		$img_comp.find('div').remove();
		$img_comp.find('img').removeClass('twentytwenty-before twentytwenty-after').removeAttr('style');
		$img_comp.closest('.elementor-widget-container').html($img_comp);
		$img_comp.eatwentytwenty($options);
	});
};

jQuery(window).on("elementor/frontend/init", function () {
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/eael-image-comparison.default",
		ImageComparisonHandler
	);
});
