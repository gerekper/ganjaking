/**
 * Product Meta Boxes.
 *
 * @package WC_Instagram/Assets/JS/Admin
 * @since   3.3.0
 */

/* global wc_instagram_admin_meta_boxes_product_params */
( function( $ ) {

	'use strict';

	if ( typeof wc_instagram_admin_meta_boxes_product_params === 'undefined' ) {
		return false;
	}

	var select_width = ( window.innerWidth >= 1280 ? '50%' : '80%' );

	function load_category_select( category_id ) {
		$.post( wc_instagram_admin_meta_boxes_product_params.ajax_url, {
			action: 'wc_instagram_refresh_google_product_category_metabox_field',
			_wpnonce: wc_instagram_admin_meta_boxes_product_params.nonce,
			post_id: wc_instagram_admin_meta_boxes_product_params.post_id,
			category_id: category_id
		})
		.done( function( result ) {
			if ( ! result.success ) {
				return;
			}

			var $html = $( result.data.output );

			$( '#wc-instagram-google-product-categories-block' ).replaceWith( $html );
			initGoogleProductCategorySelect();
		});
	}

	function initGoogleProductCategorySelect() {
		$( '#wc-instagram-google-product-categories-block select:not(.select2-hidden-accessible)' ).selectWoo({ width: select_width });
	}

	$( function() {
		initGoogleProductCategorySelect();

		$( '#instagram_data' ).on( 'change', '#wc-instagram-google-product-categories-block select', function() {
			load_category_select( $( this ).val() );

			$( '#google_product_category' ).val( $( this ).val() );
		});
	});
})( jQuery );