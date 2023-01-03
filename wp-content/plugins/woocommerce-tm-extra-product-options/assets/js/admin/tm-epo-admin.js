( function( window, document, $ ) {
	'use strict';

	var TMEPOADMINJS = window.TMEPOADMINJS;
	var checkForChanges = 0;
	var epoSortableOptions;

	if ( ! TMEPOADMINJS ) {
		return;
	}

	// Update option boxes
	function epoUpdateBoxes() {
		var productType = $( '#product-type' ).val();

		$( '.tc-normal-epo' )
			.toArray()
			.forEach( function( element ) {
				var div = $( element );
				var currentAttribute = div.find( '.tmcp_attribute' ).val();
				var attribute = div.find( 'select.tmcp_att' ).val();
				var variation = div.find( 'select.tmcp-variation' ).val();
				var showField = 'input.tmcp-price-input-variation-' + variation + "[data-price-input-attribute='" + attribute + "'],select.tmcp-price-input-variation-" + variation + "[data-price-input-attribute='" + attribute + "']";

				if ( ! ( productType === 'variable' || productType === 'variable-subscription' ) ) {
					div.find( '.show_if_variable' ).hide();
				} else {
					div.find( '.show_if_variable' ).show();
				}
				div.find( 'select.tmcp_att' ).hide();
				div.find( "select.tmcp_att[data-tm-attr='" + currentAttribute + "']" ).show();
				div.find( 'input.tmcp-price-input' ).hide();
				div.find( 'select.tmcp-price-input-type' ).hide();
				div.find( showField ).show();
			} );
	}

	// Set Ordering
	function epoRowIndex() {
		$( '.tc-normal-epos .tc-normal-epo' )
			.toArray()
			.forEach( function( el ) {
				$( '.tm_epo_menu_order', el ).val( parseInt( $( el ).index( '.tc-normal-epos .tc-normal-epo' ), 10 ) );
			} );
	}

	// Price fields
	function epoShowPriceField( obj, what ) {
		var val = obj.val();
		var div = obj.closest( '.tc-normal-epo' );
		var loop = div.find( 'input.tmcp_loop' ).val();
		var attribute = div.find( 'select.tmcp_att' ).val();
		var variation = div.find( 'select.tmcp-variation' ).val();
		var showField = '';
		var jo;

		div.find( 'input.tmcp-price-input' ).hide();
		div.find( 'select.tmcp-price-input-type' ).hide();

		switch ( what ) {
			case 'variation':
				variation = val;
				showField = 'input.tmcp-price-input-variation-' + val + "[data-price-input-attribute='" + attribute + "'],select.tmcp-price-input-variation-" + val + "[data-price-input-attribute='" + attribute + "']";
				break;
			case 'attribute':
				attribute = val;
				showField = 'input.tmcp-price-input-variation-' + variation + "[data-price-input-attribute='" + val + "'],select.tmcp-price-input-variation-" + variation + "[data-price-input-attribute='" + val + "']";
				break;
		}
		if ( div.find( showField ).length <= 0 ) {
			jo = div.find( '.tmcp-pricing' );
			jo.append(
				'<input type="text" name="tmcp_regular_price[' +
					loop +
					'][' +
					attribute +
					'][' +
					variation +
					']" value="" class="wc_input_price tmcp-price-input tmcp-price-input-variation-' +
					variation +
					'" data-price-input-attribute="' +
					attribute +
					'">'
			);
			jo.append(
				'<select class="tmcp-price-input-type tmcp-price-input tmcp-price-input-variation-' +
					variation +
					'" data-price-input-attribute="' +
					attribute +
					'" name="tmcp_regular_price_type[' +
					loop +
					'][' +
					attribute +
					'][' +
					variation +
					']"><option value="">' +
					TMEPOADMINJS.i18n_fixed_type +
					'</option><option value="percent">' +
					TMEPOADMINJS.i18n_percent_type +
					'</option></select>'
			);
		}

		div.find( showField ).show();
	}

	// Check for changes that affect the options
	function epoCheck() {
		var data;

		if ( checkForChanges === 1 ) {
			$( '#tc-admin-extra-product-options' ).block( {
				message: null
			} );
			data = {
				action: 'woocommerce_tm_load_epos',
				post_id: TMEPOADMINJS.post_id,
				security: TMEPOADMINJS.load_tm_epo_nonce
			};
			$.post( TMEPOADMINJS.ajax_url, data, function( response ) {
				$( '.tm-mode-local' ).html( response );
				$( '#tc-admin-extra-product-options' ).unblock();
				$( '#tc-admin-extra-product-options' ).trigger( 'tc_normal_epos_loaded' );
				checkForChanges = 0;
				epoUpdateBoxes();
			} );
		}
	}

	// Mode Selector
	function setMode( mode ) {
		if ( ! mode ) {
			return;
		}

		$( '#tm-meta-cpf-mode' ).val( mode );
		$( '.tm-mode-selector' ).addClass( 'tc-active' );
		$( '.tm-mode-builder,.tm-mode-local,.tm-mode-settings' ).hide();
		$( '.tm-mode-' + mode ).show();
		$( '.tc-builder-select,.tc-local-select,.tc-settings-select' ).removeClass( 'button-primary' );
		$( '.tc-' + mode + '-select' ).addClass( 'button-primary' );
	}

	// Price display mode
	function priceDisplayMode( mode ) {
		var checked = mode.filter( ':checked' );
		checked.closest( '.price-display-mode-wrap' ).removeClass( function( index, className ) {
			return ( className.match( /(^|\s)mode-\S+/g ) || [] ).join( ' ' );
		} ).addClass( 'mode-' + checked.val() );
	}

	// document ready
	$( function() {
		var bulkActionSelectorTop = $( '#bulk-action-selector-top' );
		var found = false;

		if ( bulkActionSelectorTop.length > 0 ) {
			bulkActionSelectorTop.children( 'option' ).each( function( i, o ) {
				if ( $( o ).val() === 'tcline' ) {
					found = true;
					$( o ).replaceWith( $( '<optgroup class="tc-bulk-opt" label="' + $( o ).text() + '">' ) );
				} else if ( found && $( o ).val() !== 'tcclear' && $( o ).val() !== 'tcproductclear' && $( o ).val() !== 'tcclearexclude' && $( o ).val() !== 'tcclearexcludeadd' ) {
					$( o ).appendTo( $( '.tc-bulk-opt' ) );
				}
				if ( $( o ).val() === 'tcline2' ) {
					$( o ).remove();
				}
			} );
		}

		epoSortableOptions = {
			items: '.tc-normal-epo',
			cursor: 'move',
			axis: 'y',
			handle: 'h3 .move',
			scrollSensitivity: 40,
			forcePlaceholderSize: true,
			helper: 'clone',
			opacity: 0.65,
			placeholder: 'wc-metabox-sortable-placeholder',
			start: function( event, ui ) {
				if ( event ) {
					ui.item.css( 'background-color', '#f6f6f6' );
				}
			},
			stop: function( event, ui ) {
				if ( event ) {
					ui.item.removeAttr( 'style' );
				}
				epoRowIndex();
			}
		};

		epoUpdateBoxes();

		$( '#tc-admin-extra-product-options' ).on( 'change', 'select.tmcp-variation', function() {
			epoShowPriceField( $( this ), 'variation' );
		} );
		$( '#tc-admin-extra-product-options' ).on( 'change', 'select.tmcp_att', function() {
			epoShowPriceField( $( this ), 'attribute' );
		} );

		$( '#tc-admin-extra-product-options' ).on( 'tc_normal_epo_added', function() {
			$( '.tc-normal-epos' ).sortable( epoSortableOptions );
		} );
		$( '#tc-admin-extra-product-options' ).on( 'tc_normal_epos_loaded', function() {
			$( '.tc-normal-epos' ).sortable( epoSortableOptions );
		} );

		$( '.tc-normal-epos' ).sortable( epoSortableOptions );

		$( '#variable_product_options' ).on( 'click', 'button.remove_variation', function() {
			checkForChanges = 1;
		} );
		$( '#variable_product_options' ).on( 'woocommerce_variations_added', function() {
			checkForChanges = 1;
		} );
		$( '.product_attributes' ).on( 'click', 'button.add_new_attribute', function() {
			checkForChanges = 1;
		} );
		$( '.save_attributes' ).on( 'click', function() {
			checkForChanges = 1;
		} );
		$( '.tc-epo-woocommerce-tab a' ).on( 'click', function() {
			epoCheck();
		} );

		// Add extra option
		$( '#tc-admin-extra-product-options' ).on( 'click', 'button.tm_add_epo', function() {
			var attributeType = $( 'select.tmcp-attr-list' ).val();
			var thisRow = $( ".tc-normal-epos .tc-normal-epo[data-epo-attr='" + attributeType + "']" );
			var loop = $( '.tc-normal-epo' ).length;
			var data = {
				action: 'woocommerce_tm_add_epo',
				post_id: TMEPOADMINJS.post_id,
				att_id: attributeType,
				loop: loop,
				security: TMEPOADMINJS.add_tm_epo_nonce
			};

			if ( thisRow.length > 0 ) {
				thisRow.find( '.woocommerce-tmcp-attributes' ).show();
				return;
			}
			$( '.tm-mode-local' ).block( {
				message: null
			} );

			$.post( TMEPOADMINJS.ajax_url, data, function( response ) {
				if ( response === 'max' ) {
					window.alert( TMEPOADMINJS.i18n_max_tmcp );
					$( '.tm-mode-local' ).unblock();
				} else if ( response ) {
					$( '.tc-normal-epos' ).append( response );

					$( '#tc-admin-extra-product-options' ).trigger( 'tc_normal_epo_added' );
					epoUpdateBoxes();
				}
			} ).always( function() {
				$( '.tm-mode-local' ).unblock();
			} );
			return false;
		} );

		// Remove extra option
		$( '#tc-admin-extra-product-options' ).on( 'click', '.remove_tm_epo', function( e ) {
			var answer = window.confirm( TMEPOADMINJS.i18n_remove_tmcp );
			var element;
			var variation;
			var data;

			e.preventDefault();

			if ( answer ) {
				element = $( this ).parent().parent();
				variation = $( this ).attr( 'rel' );
				if ( variation > 0 ) {
					element.block( {
						message: null
					} );
					data = {
						action: 'woocommerce_tm_remove_epo',
						tmcpid: variation,
						security: TMEPOADMINJS.delete_tm_epo_nonce
					};
					$.post( TMEPOADMINJS.ajax_url, data, function() {
						element.fadeOut( '300', function() {
							element.remove();
						} );
					} );
				} else {
					element.fadeOut( '300', function() {
						element.remove();
					} );
				}
			}
			return false;
		} );

		$( '#tc-admin-extra-product-options' ).on( 'change', '.tm-type', function() {
			var element = $( this );
			var choices = element.closest( '.data' ).find( '.tmcp_choices' );

			if ( element.val() === 'checkbox' ) {
				choices.removeClass( 'tm-hidden' );
			} else {
				choices.addClass( 'tm-hidden' );
			}
		} );

		$( '#tc-admin-extra-product-options' ).on( 'click', '.tc-select-mode', function( e ) {
			var mode = 'local';

			e.preventDefault();

			if ( $( this ).is( '.tc-builder-select' ) ) {
				mode = 'builder';
			}
			if ( $( this ).is( '.tc-settings-select' ) ) {
				mode = 'settings';
			}
			setMode( mode );
		} );

		// Price display mode
		$( '#tc-admin-extra-product-options' ).on( 'change', '.price-display-mode', function() {
			priceDisplayMode( $( this ) );
		} );
		priceDisplayMode( $( '.price-display-mode' ) );

		if ( ! $( '#tm-meta-cpf-mode' ).val() ) {
			$( '#tm-meta-cpf-mode' ).val( 'builder' );
		}
		setMode( $( '#tm-meta-cpf-mode' ).val() );

		if ( $.tmEPOAdmin ) {
			$.tmEPOAdmin.create_normal_dropdown( $( '.tm-mode-settings' ) );
		}
		if ( $.fn.tmcheckboxes ) {
			$( '.tm-mode-settings' ).tmcheckboxes();
		}
	} );
}( window, document, window.jQuery ) );
