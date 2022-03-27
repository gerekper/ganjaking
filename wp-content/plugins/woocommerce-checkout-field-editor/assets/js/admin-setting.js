jQuery( document ).ready( function( $ ) {
	$( 'table#wc_checkout_fields tbody' ).sortable({
		items:'tr',
		cursor:'move',
		axis:'y',
		handle: 'td',
		scrollSensitivity:40,
		helper:function( e,ui ) {
			ui.children().each( function() {
				$( this ).width( $( this ).width() );
			});
			ui.css( 'left', '0' );
			return ui;
		},
		start:function( event, ui ) {
			ui.item.css( 'background-color', '#f6f6f6' );
		},
		stop:function( event, ui ) {
			ui.item.removeAttr( 'style' );
			field_row_indexes();
		}
	});

	function field_row_indexes() {
		$( '#wc_checkout_fields tbody tr' ).each(function( index, el ) {
			$( 'input.field_order', el ).val( parseInt( $( el ).index( '#wc_checkout_fields tbody tr' ) ), 10 );
		});
	};

	/**
	 * Because on WC init, Select2 has already initialized
	 * our new row making it a Select2 element. So we should
	 * remove them until a row has been cloned. This prevents
	 * duplicated Select2 elements to show.
	 */
	$( 'tr.new_row' ).find( '.wc-enhanced-select' ).each( function() {
		if ( typeof $( this ).select2 === 'function' ) {
			$(this).select2('destroy');
		}
	});

	$( 'a.new_row' ).click( function() {
		var size = $( '#wc_checkout_fields tbody tr' ).length;

		size ++;

		var new_row = $( 'tr.new_row' ).clone();

		html = $( new_row ).html();

		html = html.replace( /\[0\]/g, "[" +  size + "]" );

		$( new_row ).html( html );

		$( new_row ).removeClass( 'new_row' ).appendTo( '#checkout_fields' ).show();

		$( 'table#wc_checkout_fields tr:not(.new_row) .enhanced' ).each( function( index, el ) {
			$( this ).removeClass( 'enhanced' );
		});

		$( document.body ).trigger( 'wc-enhanced-select-init' );

		field_row_indexes();

		return false;
	});

	$( 'a.enable_row' ).click( function() {

		var selected_rows = $( '#wc_checkout_fields tbody' ).find( '.check-column input:checked' );

		$( selected_rows ).each( function() {
			var tr = $( this ).closest( 'tr' );
			tr.removeClass( 'disabled' );
			tr.find( '.field_enabled' ).val( '1' );
		});

		return false;
	});

	$( 'a.disable_row' ).click( function() {

		var selected_rows = $( '#wc_checkout_fields tbody' ).find( '.check-column input:checked' );

		$( selected_rows ).each( function() {
			var tr = $( this ).closest( 'tr' );
			tr.addClass( 'disabled' );
			tr.find( '.field_enabled' ).val( '0' );
		});

		return false;
	});

	$( 'table#wc_checkout_fields' ).on( 'change', 'td.enabled input', function() {
		if ( $( this ).is( ':checked' ) ) {
			$( this ).closest( 'tr' ).removeClass( 'disabled' );
		} else {
			$( this ).closest( 'tr' ).addClass( 'disabled' );
		}
	});

	$( 'table#wc_checkout_fields' ).on( 'change', 'select.field_type', function() {

		var val = $( this ).val();

		$( this ).closest( 'tr' ).find( '.field-options input.placeholder, .field-options input.options, .field-options .na, .field-validation .options, .field-validation .na' ).hide();

		if ( val === 'select' || val === 'multiselect' || val === 'radio' ) {
			$( this ).closest( 'tr' ).find( '.field-options .options, .field-validation .options' ).show();
		} else if ( val === 'heading' ) {
			$( this ).closest( 'tr' ).find( '.field-options .na, .field-validation .na' ).show();
		} else if ( val === 'checkbox' ) {
			$( this ).closest( 'tr' ).find( '.field-validation .options' ).show();
			$( this ).closest( 'tr' ).find( '.field-options .na' ).show();
		} else {
			$( this ).closest( 'tr' ).find('.field-options .placeholder, .field-validation .options' ).show();
		}

	});

	$( '#wc_checkout_fields' ).find( '.field-options input.placeholder, .field-options input.options' ).hide();
	$( '#wc_checkout_fields td.enabled input' ).change();
	$( '#wc_checkout_fields select.field_type' ).change();

	// order comments
	$( 'table#wc_checkout_fields' ).find( 'tr[data-field-name="order_comments"] .field-options .placeholder' ).show();
	$( 'table#wc_checkout_fields' ).find( 'tr[data-field-name="order_comments"] .field-options .na' ).hide();
});
