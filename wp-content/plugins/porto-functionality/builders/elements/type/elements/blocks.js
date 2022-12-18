import PortoImageChoose from '../../../../shortcodes/assets/blocks/controls/image-choose';
import PortoTypographyControl from '../../../../shortcodes/assets/blocks/controls/typography';
import PortoAjaxSelect2Control from '../../../../shortcodes/assets/blocks/controls/ajaxselect2';

window.portoImageControl = PortoImageChoose;
window.portoTypographyControl = PortoTypographyControl;
window.portoAjaxSelect2Control = PortoAjaxSelect2Control;

import './featured-image';
import './content';
import './woo-price';
import './woo-rating';
import './woo-stock';
import './woo-desc';
import './woo-buttons';
import './meta';

window.porto_content_type = jQuery( '#content_type' ).val();
window.porto_content_type_value = '';
if ( window.porto_content_type ) {
	window.porto_content_type_value = jQuery( '#content_type_' + porto_content_type ).data( 'value' );
}
window.porto_tb_ids = [];

jQuery(document).ready(function($) {
	if ( ! $( '#content_type' ).length ) {
		return;
	}
	porto_content_type = $( '#content_type' ).val(), porto_content_type_value = '';
	if ( porto_content_type ) {
		porto_content_type_value = $( '#content_type_' + porto_content_type ).val() ? $( '#content_type_' + porto_content_type ).val() : $( '#content_type_' + porto_content_type ).data( 'value' );
	}

	$( document.body ).on( 'porto_tb_content_type_updated', function() {
		$.ajax( {
			url: porto_block_vars.ajax_url,
			data: {
				action: 'porto_dynamic_tags_acf_fields',
				nonce: porto_block_vars.nonce,
				content_type: porto_content_type,
				content_type_value: porto_content_type_value
			},
			type: 'post',
			success: function ( res ) {
				if ( res.success ) {
					porto_block_vars.acf = res.data;
					$( document.body ).on( 'porto_tb_acf_fields_updated' );
				}
			}
		} );
	} );

	$( document.body ).trigger( 'porto_tb_content_type_updated', [ porto_content_type, porto_content_type_value ] );
	$( '#content_type' ).on( 'change', function() {
		if ( porto_content_type !== $( this ).val() ) {
			porto_content_type = $( this ).val();
			porto_content_type_value = $( '#content_type_' + porto_content_type ).val();
			$( document.body ).trigger( 'porto_tb_content_type_updated', [ porto_content_type, porto_content_type_value ] );
		}
	} );

	$( '#content_type option' ).each( function() {
		var option_val = $( this ).val();
		if ( ! option_val ) {
			return;
		}
		$( '#content_type_' + option_val ).on( 'change', function( e ) {
			if ( porto_content_type_value !== $( this ).val() ) {
				porto_content_type_value = $( this ).val();
				$( document.body ).trigger( 'porto_tb_content_type_updated', [ porto_content_type, porto_content_type_value ] );
			}
		} );
	} );
});