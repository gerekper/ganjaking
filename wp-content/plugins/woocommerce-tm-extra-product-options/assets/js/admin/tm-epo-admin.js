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

		$( '.woocommerce_tm_epo' )
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
		$( '.woocommerce_tm_epos .woocommerce_tm_epo' )
			.toArray()
			.forEach( function( el ) {
				$( '.tm_epo_menu_order', el ).val( parseInt( $( el ).index( '.woocommerce_tm_epos .woocommerce_tm_epo' ), 10 ) );
			} );
	}

	// Price fields
	function epoShowPriceField( obj, what ) {
		var val = obj.val();
		var div = obj.closest( '.woocommerce_tm_epo' );
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
			jo = div.find( '.tmcp_pricing' );
			jo.append(
				'<input type="text" size="5" name="tmcp_regular_price[' +
					loop +
					'][' +
					attribute +
					'][' +
					variation +
					']" value="" class="wc_input_price tmcp-price-input tmcp-price-input-variation-' +
					variation +
					'" data-price-input-attribute="' +
					attribute +
					'" />'
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
			$( '#tm_extra_product_options' ).block( {
				message: null
			} );
			data = {
				action: 'woocommerce_tm_load_epos',
				post_id: TMEPOADMINJS.post_id,
				security: TMEPOADMINJS.load_tm_epo_nonce
			};
			$.post( TMEPOADMINJS.ajax_url, data, function( response ) {
				$( '.tm_mode_local' ).html( response );
				$( '#tm_extra_product_options' ).unblock();
				$( '#tm_extra_product_options' ).trigger( 'woocommerce_tm_epos_loaded' );
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

		$( '#tm_meta_cpf_mode' ).val( mode );
		$( '.tm_mode_selector' ).addClass( 'tm_hidden' );
		$( '.tm_mode_builder,.tm_mode_local,.tm_mode_settings' ).hide();
		$( '.tm_mode_' + mode ).show();
		$( '.tm_builder_select,.tm_local_select,.tm_settings_select' ).removeClass( 'button-primary' );
		$( '.tm_' + mode + '_select' ).addClass( 'button-primary' );
	}

	$( document ).ready( function() {
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
			items: '.woocommerce_tm_epo',
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

		$( '#tm_extra_product_options' ).on( 'change', 'select.tmcp-variation', function() {
			epoShowPriceField( $( this ), 'variation' );
		} );
		$( '#tm_extra_product_options' ).on( 'change', 'select.tmcp_att', function() {
			epoShowPriceField( $( this ), 'attribute' );
		} );

		$( '#tm_extra_product_options' ).on( 'woocommerce_tm_epo_added', function() {
			$( '.woocommerce_tm_epos' ).sortable( epoSortableOptions );
		} );
		$( '#tm_extra_product_options' ).on( 'woocommerce_tm_epos_loaded', function() {
			$( '.woocommerce_tm_epos' ).sortable( epoSortableOptions );
		} );

		$( '.woocommerce_tm_epos' ).sortable( epoSortableOptions );

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
		$( '.tm_epo_class a' ).on( 'click', function() {
			epoCheck();
		} );

		// Add extra option
		$( '#tm_extra_product_options' ).on( 'click', 'button.tm_add_epo', function() {
			var attributeType = $( 'select.tmcp_attr_list' ).val();
			var thisRow = $( ".woocommerce_tm_epos .woocommerce_tm_epo[data-epo-attr='" + attributeType + "']" );
			var loop = $( '.woocommerce_tm_epo' ).length;
			var data = {
				action: 'woocommerce_tm_add_epo',
				post_id: TMEPOADMINJS.post_id,
				att_id: attributeType,
				loop: loop,
				security: TMEPOADMINJS.add_tm_epo_nonce
			};

			if ( thisRow.length > 0 ) {
				thisRow.find( '.woocommerce_tmcp_attributes' ).show();
				return;
			}
			$( '.tm_mode_local' ).block( {
				message: null
			} );

			$.post( TMEPOADMINJS.ajax_url, data, function( response ) {
				if ( response === 'max' ) {
					window.alert( TMEPOADMINJS.i18n_max_tmcp );
					$( '.tm_mode_local' ).unblock();
				} else if ( response ) {
					$( '.woocommerce_tm_epos' ).append( response );

					$( '#tm_extra_product_options' ).trigger( 'woocommerce_tm_epo_added' );
					epoUpdateBoxes();
				}
			} ).always( function() {
				$( '.tm_mode_local' ).unblock();
			} );
			return false;
		} );

		// Remove extra option
		$( '#tm_extra_product_options' ).on( 'click', '.remove_tm_epo', function( e ) {
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

		$( '#tm_extra_product_options' ).on( 'change', '.tm-type', function() {
			var element = $( this );
			var choices = element.closest( '.data' ).find( '.tmcp_choices' );

			if ( element.val() === 'checkbox' ) {
				choices.removeClass( 'tm-hidden' );
			} else {
				choices.addClass( 'tm-hidden' );
			}
		} );

		$( '#tm_extra_product_options' ).on( 'click', '.tm_select_mode', function( e ) {
			var mode = 'local';

			e.preventDefault();

			if ( $( this ).is( '.tm_builder_select' ) ) {
				mode = 'builder';
			}
			if ( $( this ).is( '.tm_settings_select' ) ) {
				mode = 'settings';
			}
			setMode( mode );
		} );

		if ( ! $( '#tm_meta_cpf_mode' ).val() ) {
			$( '#tm_meta_cpf_mode' ).val( 'builder' );
		}
		setMode( $( '#tm_meta_cpf_mode' ).val() );

		if ( $.tmEPOAdmin ) {
			$.tmEPOAdmin.create_normal_dropdown( $( '.tm_mode_settings' ) );
		}
		if ( $.fn.tmcheckboxes ) {
			$( '.tm_mode_settings' ).tmcheckboxes();
		}
	} );
}( window, document, window.jQuery ) );
