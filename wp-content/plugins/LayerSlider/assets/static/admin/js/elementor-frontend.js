class WidgetLayerSliderHandler extends elementorModules.frontend.handlers.Base {

	onInit() {
		var $id = window.top.jQuery('.ls-id input');
		if ($id.length && !$id.val()) {
			window.top.LS_Widget.chooseSlider();
		}
	}

	onDestroy() {
		this.$element.find( '.ls-container' ).layerSlider( 'destroy' );
	}
}

jQuery( window ).on( 'elementor/frontend/init', () => {
	elementorFrontend.hooks.addAction( 'frontend/element_ready/layerslider.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( WidgetLayerSliderHandler, {
			$element,
		} );
	} );
} );
