jQuery(
	function ( $ ) {

		$( 'input[name^=ywctm_inquiry_form_where_show]' ).change(
			function () {
				if ( $( this ).is( ':checked' ) && 'tab' === $( this ).val() ) {
					$( 'input[name^=ywctm_inquiry_form_style][value=classic]' ).prop( 'checked', true ).click();
				}
			}
		).change();

		$( 'input[name^=ywctm_inquiry_form_style]' ).change(
			function () {
				if ( $( this ).is( ':checked' ) && 'toggle' === $( this ).val() ) {
					$( 'input[name^=ywctm_inquiry_form_tab_title]' ).parent().parent().parent().hide();
				} else {
					$( 'input[name^=ywctm_inquiry_form_tab_title]' ).parent().parent().parent().show( 500 );
				}
			}
		).change();

		$( '.ywctm-exclusions .yith-save-button' ).click(
			function ( e ) {

				var item_type = $( '#item_type' ).val(),
					element   = $( '#' + item_type + '_ids' );

				if ( element.find( 'option' ).length === 0 ) {

					element
						.parent()
						.find( 'small' )
						.remove();

					element
						.parent()
						.append( '<small style="color:#ff0000">' + ywctm.error_messages[ item_type ] + '</small>' );

					$( 'html, body' ).animate(
						{
							scrollTop: element.parent().offset().top - 30
						},
						500
					);

					e.preventDefault();
					return false;
				}
			}
		);

		$( '.column-inquiry_form .on_off' ).change(
			function () {

				var data = {
					action   : 'ywctm_enable_inquiry_form',
					item_id  : $( this ).parent().data( 'item-id' ),
					enabled  : $( this ).val(),
					section  : $( this ).parent().data( 'section' ),
					vendor_id: ywctm.vendor_id
				};

				$.post( ywctm.ajax_url, data );

			}
		);

		$( '.column-exclude .on_off' ).change(
			function () {

				var data = {
					action : 'ywctm_exclude_vendor',
					item_id: $( this ).attr( 'id' ).replace( 'exclude_vendor', '' ),
					enabled: $( this ).val(),
				};

				$.post( ywctm.ajax_url, data );

			}
		);

		var product_ids_row  = $( 'tr.ajax-products.product_ids' ),
			category_ids_row = $( 'tr.ajax-terms.category_ids' ),
			tag_ids_row      = $( 'tr.ajax-terms.tag_ids' );

		$( '#item_type' ).change(
			function () {

				var type = $( this ).val();

				switch ( type ) {
					case 'category':
						product_ids_row.hide();
						category_ids_row.show( 500 );
						tag_ids_row.hide();
						break;

					case 'tag':
						product_ids_row.hide();
						category_ids_row.hide();
						tag_ids_row.show( 500 );
						break;

					default:
						product_ids_row.show( 500 );
						category_ids_row.hide();
						tag_ids_row.hide();

				}

			}
		).change();

		$( '#ywctm_enable_atc_custom_options' ).change(
			function () {

				if ( 'no' === $( this ).val() ) {
					$( 'tr.ywctm_atc_status' ).hide();
					$( 'tr.ywctm_custom_button' ).hide();
					$( 'tr.ywctm_custom_button_loop' ).hide();
				} else {
					$( 'tr.ywctm_atc_status' ).show( 500 );
				}
				$( '#ywctm_atc_status' ).change();

			}
		).change();

		$( '#ywctm_atc_status' ).change(
			function () {

				var main_option = $( '#ywctm_enable_atc_custom_options' ).val();

				if ( 'show' === $( this ).val() || 'no' === main_option ) {
					$( 'tr.ywctm_custom_button' ).hide();
					$( 'tr.ywctm_custom_button_loop' ).hide();
				} else {
					$( 'tr.ywctm_custom_button' ).show( 500 );
					$( 'tr.ywctm_custom_button_loop' ).show( 500 );
				}
				$( '#ywctm_custom_button' ).change();
				$( '#ywctm_custom_button_loop' ).change();

			}
		).change();

		$( '#ywctm_enable_price_custom_options' ).change(
			function () {

				if ( 'no' === $( this ).val() ) {
					$( 'tr.ywctm_price_status' ).hide();
					$( 'tr.ywctm_custom_price_text' ).hide();
				} else {
					$( 'tr.ywctm_price_status' ).show( 500 );
				}
				$( '#ywctm_price_status' ).change();

			}
		).change();

		$( '#ywctm_price_status' ).change(
			function () {

				var main_option = $( '#ywctm_enable_price_custom_options' ).val();

				if ( 'show' === $( this ).val() || 'no' === main_option ) {
					$( 'tr.ywctm_custom_price_text' ).hide();
				} else {
					$( 'tr.ywctm_custom_price_text' ).show( 500 );
				}

				$( '#ywctm_custom_price_text' ).change();

			}
		).change();

		$( '#ywctm_has_exclusion' ).change(
			function () {
				if ( 'no' === $( this ).val() ) {
					$( '#ywctm_enable_atc_custom_options' )
						.prop( 'checked', false )
						.val( 'no' )
						.removeClass( 'onoffchecked' )
						.change();
					$( '#ywctm_enable_price_custom_options' )
						.prop( 'checked', false )
						.val( 'no' )
						.removeClass( 'onoffchecked' )
						.change();
					$( 'tr.ywctm_enable_inquiry_form' ).hide();
					$( 'tr.ywctm_enable_atc_custom_options' ).hide();
					$( 'tr.ywctm_enable_price_custom_options' ).hide();

				} else {
					$( 'tr.ywctm_enable_inquiry_form' ).show( 500 );
					$( 'tr.ywctm_enable_atc_custom_options' ).show( 500 );
					$( 'tr.ywctm_enable_price_custom_options' ).show( 500 );
				}
			}
		).change();

		$( '#ywctm_custom_button' ).change( function () {
			var atc_status   = $( '#ywctm_atc_status' ).val(),
				atc_override = $( '#ywctm_enable_atc_custom_options' ).val();
			if ( -1 === $.inArray( parseInt( $( this ).val() ), ywctm.buttons_custom_url ) || 'show' === atc_status || 'no' === atc_override ) {
				$( 'tr.ywctm_custom_button_url' ).hide()
			} else {
				$( 'tr.ywctm_custom_button_url' ).show( 500 );
			}
		} ).change();

		$( '#ywctm_custom_button_loop' ).change( function () {
			var atc_status   = $( '#ywctm_atc_status' ).val(),
				atc_override = $( '#ywctm_enable_atc_custom_options' ).val();
			if ( -1 === $.inArray( parseInt( $( this ).val() ), ywctm.buttons_custom_url ) || 'show' === atc_status || 'no' === atc_override ) {
				$( 'tr.ywctm_custom_button_loop_url' ).hide()
			} else {
				$( 'tr.ywctm_custom_button_loop_url' ).show( 500 );
			}
		} ).change();

		$( '#ywctm_custom_price_text' ).change( function () {
			var price_status   = $( '#ywctm_price_status' ).val(),
				price_override = $( '#ywctm_enable_price_custom_options' ).val();
			if ( -1 === $.inArray( parseInt( $( this ).val() ), ywctm.buttons_custom_url ) || 'show' === price_status || 'no' === price_override ) {
				$( 'tr.ywctm_custom_price_text_url' ).hide()
			} else {
				$( 'tr.ywctm_custom_price_text_url' ).show( 500 );
			}
		} ).change();

	}
);
