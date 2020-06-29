jQuery( function( $ ) {

	// Update preview on change Single
	$( '#woocommerce_advanced_product_labels' ).on( 'change keyup', 'input, select', function() {

		var data = {
			type: 	$( '[name=_wapl_label_type]' ).val(),
			text: 	$( '[name=_wapl_label_text]' ).val(),
			align: 	$( '[name=_wapl_label_align]' ).val(),
			style: 	$( '[name=_wapl_label_style]' ).val(),
		};
		if ( 'custom' != data.style ) {
			$( '.label-wrap .product-label' ).css({ 'background-color': '', 'color': '' });
		}

		$( '.label-wrap' ).removeClass().addClass( 'wapl-label-id-' + $( '#post_ID' ).val() + ' label-wrap wapl-' + data.type + ' label-' + data.style + ' wapl-align' + data.align );
		$( '.label-wrap .product-label' ).removeClass().addClass( 'woocommerce-advanced-product-label product-label label-' + data.style );
		$( '.product-label' ).html( $( '<span/>' ).addClass( 'wapl-label-text' ).html( data.text ) );

	});


	// Update preview Global
	jQuery( '#wapl_settings' ).on( 'change keyup', 'input, select', function() {

		var data = {
			type: 	jQuery( '#wapl_global_label_type' ).val(),
			text: 	jQuery( '#wapl_global_label_text' ).val(),
			align: 	jQuery( '#wapl_global_label_align' ).val(),
			style: 	jQuery( '#wapl_global_label_style' ).val(),
		};

		if ( 'custom' != data.style ) {
			jQuery( '.label-wrap .product-label' ).css({ 'background-color': '', 'color': '' });
		}

		$( '.label-wrap' ).removeClass().addClass( 'wapl-label-id-' + $( '#post_ID' ).val() + ' label-wrap wapl-' + data.type + ' label-' + data.style + ' wapl-align' + data.align );
		$( '.label-wrap .product-label' ).removeClass().addClass( 'woocommerce-advanced-product-label product-label label-' + data.style );
		$( '.product-label' ).html( $( '<span/>' ).addClass( 'wapl-label-text' ).html( data.text ) );

	});


	// Background color picker
	$( '#wapl-custom-background.color-picker' ).wpColorPicker({
// 		palettes: ['#D9534F', '#3498db', '#39A539', '#ffe312', '#ffA608', '#999', '#444', '#fff'],
		color: '#D9534F',
		palettes: false,
		change: function( event, ui ) {
			$( '.label-wrap .product-label' ).css({ 'background-color': ui.color.toString() });
			jQuery( '.label-wrap' ).prev( 'style' ).html( '.label-wrap .product-label:after { border-color: ' + ui.color.toString() + '; }' );
		},
	});
	$( '#wapl-custom-text.color-picker' ).wpColorPicker({
// 		palettes: ['#D9534F', '#3498db', '#39A539', '#ffe312', '#ffA608', '#999', '#444', '#fff'],
		color: '#fff',
		palettes: false,
		change: function( event, ui ) {
			$( '.label-wrap .product-label' ).css({ 'color': ui.color.toString() });
		},
	});

	// Display/hide the color pickers
	$( '#wapl_settings' ).on( 'change', '#wapl_global_label_style', function() {

		if ( 'custom' == $( this ).val() ) {
			// Set default values when colors are not set
			if ( 'rgba(0, 0, 0, 0)' == $( '.label-wrap .product-label' ).css( 'background-color' ) ) {
				$( '.label-wrap .product-label' ).css({
					'background-color': $( '#wapl_settings #wapl-custom-background.color-picker' ).val(),
					'color': $( '#wapl_settings #wapl-custom-text.color-picker' ).val()
				});
			}

			$( '#wapl_settings .custom-colors' ).slideDown( 'fast' );
		} else {
			$( '#wapl_settings .custom-colors' ).slideUp( 'fast' );
		}

	});

	// Display/hide the color pickers single product label
	$( 'body' ).on( 'change', '#_wapl_label_style', function() {

		if ( 'custom' == $( this ).val() ) {
			// Set default values when colors are not set

			if ( 'rgba(0, 0, 0, 0)' == $( '.label-wrap .product-label' ).css( 'background-color' ) ) {
				$( '.label-wrap .product-label' ).css({
					'background-color': $( '#wapl-custom-background.color-picker' ).val(),
					'color': $( '#wapl-custom-text.color-picker' ).val()
				});
			}

			$( '.wapl-custom-colors.custom-colors' ).slideDown( 'fast' );
		} else {
			$( '.wapl-custom-colors.custom-colors' ).slideUp( 'fast' );
		}

	});

});