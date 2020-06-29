jQuery(function() {
	jQuery(document).on('woocommerce_variations_loaded', function() {
		if (jQuery( 'select.variation_actions' ).hasClass('woocommerce-msrp-ajax-attached')) {
			return;
		}
		jQuery( 'select.variation_actions' ).on( 'msrp_set_prices_ajax_data', function( e, data ) {
			value = window.prompt( woocommerce_admin_meta_boxes_variations.i18n_enter_a_value );
			if ( value != '' && value != null ) {
				data.value = value;
			}
			return data;
		});
		jQuery( 'select.variation_actions' ).addClass('woocommerce-msrp-ajax-attached');
	});
});
