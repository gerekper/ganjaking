(function($) {	
    var WidgetPostFeatureImageHandler = function($scope, $) {
			var fi_bg_elem = $scope.find('.tp-post-image.tp-feature-image-as-bg').eq(0);
			if ($scope.find('.tp-post-image.tp-feature-image-as-bg').length > 0) {
				var $tp_fi_bg_type = $scope.find('.tp-post-image.tp-feature-image-as-bg').data('tp-fi-bg-type');
				if($tp_fi_bg_type !='' && $tp_fi_bg_type !=undefined){
					if($tp_fi_bg_type == 'tp-fibg-section'){
						fi_bg_elem.closest('section.elementor-element.elementor-top-section').prepend(fi_bg_elem);
					}else if($tp_fi_bg_type == 'tp-fibg-inner-section'){
						fi_bg_elem.closest('section.elementor-element.elementor-inner-section').prepend(fi_bg_elem);
					}else if($tp_fi_bg_type == 'tp-fibg-column'){
						fi_bg_elem.closest('.elementor-column').prepend(fi_bg_elem);
					}else if($tp_fi_bg_type == 'tp-fibg-container'){
						fi_bg_elem.closest('.e-container,.e-con').prepend(fi_bg_elem);
					}
				}
			}
    };
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/tp-post-featured-image.default', WidgetPostFeatureImageHandler);
    });
})(jQuery);