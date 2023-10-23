/**
 * Product page scripts
 *
 * @package YITH\FAQPluginForWordPress\Assets\JS
 */

jQuery(
	function ( $ ) {

		$( '#yfwp_show_faq_tab' ).on(
			'change',
			function () {
				if ( $( this ).is( ':checked' ) ) {
					$( '#yfwp_tab_label' ).prop( 'disabled', false );
					$( '#yfwp_shortcode' ).prop( 'disabled', false );
					$( '.wrap-field-dep' ).show( 500 );
				} else {
					$( '#yfwp_tab_label' ).prop( 'disabled', true );
					$( '#yfwp_shortcode' ).prop( 'disabled', true );
					$( '.wrap-field-dep' ).hide();
				}
			}
		).trigger( 'change' );

		$( '#publish' ).on(
			'click',
			function ( e ) {

				var tab_enabled   = $( '#yfwp_show_faq_tab' ).is( ':checked' ),
					tab_label     = $( '#yfwp_tab_label' ),
					shortcode     = $( '#yfwp_shortcode' ),
					error_message = '',
					has_error     = false;

				$( '.option-field' ).removeClass( 'has-error' ).find( 'small' ).remove();

				if ( tab_enabled && tab_label.val() === '' ) {
					has_error     = tab_label;
					error_message = yfwp_wc.errors.missing_field;
				} else if ( tab_enabled && shortcode.val() === '' ) {
					has_error     = shortcode;
					error_message = yfwp_wc.errors.missing_preset;
				}

				if ( has_error !== false ) {
					has_error.parent().parent().addClass( 'has-error' ).append( '<small class="field-error ">' + error_message + '</small>' );
					$( '.yith-faq_options a' ).trigger( 'click' );
					$( 'html, body' ).animate( { scrollTop: has_error.offset().top - 100 }, 500 );
					e.preventDefault();
				}

			}
		);

	}
);
