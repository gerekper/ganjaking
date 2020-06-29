jQuery(
	function ( $ ) {

		var field_id;

		$( document ).ready(
			function () {
				"use strict";

				var id_field = '<input type="hidden" name="ywctm-product-id" value="' + ywctm.product_id + '" />';

				switch ( ywctm.form_type ) {

					case 'contact-form-7':
						$( '.ywctm-inquiry-form-wrapper div.wpcf7 > form,  #tab-inquiry_form div.wpcf7 > form' ).append( id_field );
						break;

					case 'ninja-forms':
						$( '.ywctm-inquiry-form-wrapper .ywctm-toggle-content, #tab-inquiry_form' ).append( id_field );
						break;

					case 'formidable-forms':
						field_id = $( '#field_ywctm-product-id' ).attr( 'name' ).replace( 'item_meta[', '' ).replace( ']', '' );
						$( '.ywctm-inquiry-form-wrapper .frm_fields_container, #tab-inquiry_form .frm_fields_container' ).append( id_field ).append( '<input type="hidden" name="ywctm-ff-field-id" value="' + field_id + '" />' );
						break;

					case 'gravity-forms':
						$( '.ywctm-inquiry-form-wrapper .gform_wrapper > form > .gform_footer,  #tab-inquiry_form .gform_wrapper > form > .gform_footer' ).append( id_field );
						break;

				}

				set_variation_inquiry();

				$( '.ywctm-inquiry-form-wrapper.has-toggle .ywctm-toggle-button' ).click(
					function () {
						$( this ).parent().find( '.ywctm-toggle-content' ).slideToggle();
					}
				);

			}
		);

		$( document ).on(
			'woocommerce_variation_has_changed',
			set_variation_inquiry
		);

		$( document ).on(
			'nfFormReady',
			function () {

				field_id = $( '.nf-form-content :input[value="ywctm-product-id"]' ).attr( 'id' );
				$( '.ywctm-inquiry-form-wrapper .ywctm-toggle-content' ).append( '<input type="hidden" name="ywctm-nf-field-id" value="' + field_id + '" />' );
				set_variation_inquiry();

			}
		);

		function set_variation_inquiry() {

			if ( 'none' === ywctm.form_type ) {
				return
			}

			var variation_id = parseInt( $( '.single_variation_wrap .variation_id, .single_variation_wrap input[name="variation_id"], .woocommerce-variation-add-to-cart input[name="variation_id"]' ).val() );

			if ( ! isNaN( variation_id ) && variation_id !== 0 ) {
				$( 'input[name="ywctm-product-id"]' ).val( variation_id );
				if ( 'ninja-forms' === ywctm.form_type ) {
					$( '#' + field_id ).val( variation_id );
				}

			} else {
				$( 'input[name="ywctm-product-id"]' ).val( ywctm.product_id );
				if ( 'ninja-forms' === ywctm.form_type ) {
					$( '#' + field_id ).val( ywctm.product_id );
				}
			}

		}

	}
);
