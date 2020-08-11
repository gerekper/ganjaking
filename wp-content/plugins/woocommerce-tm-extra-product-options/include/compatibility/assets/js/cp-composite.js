( function( window, document, $ ) {
	'use strict';

	var TMEPOJS;
	var tcAPI;

	function get_composite_item_id( item ) {
		return item.attr( 'data-item-id' ) || item.attr( 'data-item_id' );
	}

	function get_composite_price_data( container_id, main_product ) {
		return main_product.find( '.bto_form_' + container_id + ',#composite_form_' + container_id + ',#composite_data_' + container_id ).data( 'price_data' );
	}

	function get_review_selector( item_id ) {
		return ' .review .price_' + item_id + ', .summary_element_' + item_id + ' .summary_element_price';
	}

	function get_composite_container_id( bto ) {
		var container_id = bto.attr( 'data-container-id' );
		var $composite_form;

		if ( ! container_id ) {
			$composite_form = $( bto ).closest( '.composite_form' );
			container_id = $composite_form.find( '.composite_data' ).data( 'container_id' );
		}
		return container_id;
	}

	function is_per_product_pricing( price_data, item_id ) {
		var p;
		if ( price_data.per_product_pricing !== undefined ) {
			p = price_data.per_product_pricing;
		} else if ( price_data.is_priced_individually !== undefined ) {
			if ( item_id ) {
				p = price_data.is_priced_individually[ item_id ] === 'yes';
			} else {
				p = Object.keys( price_data.is_priced_individually ).some( function( x ) {
					if ( Object.prototype.hasOwnProperty.call( price_data.is_priced_individually, x ) ) {
						p = price_data.is_priced_individually[ x ];
						return p === 'yes';
					}
					return null;
				} );
			}
		}
		if ( p === true ) {
			return true;
		}
		return false;
	}

	function check_bto( id, epoObject ) {
		var this_epo_totals_container = epoObject.this_epo_totals_container;
		var main_product = epoObject.main_product;
		var main_cart = epoObject.main_cart;
		var show = true;
		var item;
		var item_id;
		var form_data;
		var product_input;
		var quantity_input;
		var variation_input;
		var product_type;

		main_product
			.find( '.bto_form_' + id + ',#composite_form_' + id + ',#composite_data_' + id )
			.parent()
			.find( tcAPI.compositeSelector )
			.each( function() {
				item = $( this );
				item_id = get_composite_item_id( item );
				form_data = main_product.find( '.bto_form_' + id + ' .bundle_wrap .bundle_button .form_data_' + item_id + ',#composite_form_' + id + ' .bundle_wrap .bundle_button .form_data_' + item_id + ',#composite_data_' + id + ' .composite_wrap .composite_button .form_data_' + item_id );
				product_input = form_data.find( 'input.product_input' ).val();
				quantity_input = form_data.find( 'input.quantity_input' ).val();
				variation_input = form_data.find( 'input.variation_input' ).val();
				product_type = item.find( '.bto_item_data,.component_data' ).data( 'product_type' );

				if ( product_type === undefined || product_type === '' || product_input === '' ) {
					show = false;
				} else if ( product_type !== 'none' && quantity_input === '' ) {
					show = false;
				} else if ( product_type === 'variable' && variation_input === undefined ) {
					show = false;
				}
			} );

		if ( show ) {
			this_epo_totals_container.data( 'btois', 'show' );
		} else {
			this_epo_totals_container.data( 'btois', 'none' );
		}
		main_cart.trigger( {
			type: 'tm-epo-update',
			norules: 1
		} );
	}

	function bto_support( epoObject ) {
		var this_epo_totals_container = epoObject.this_epo_totals_container;
		var main_product = epoObject.main_product;
		var main_cart = epoObject.main_cart;
		var manualInitEPO = epoObject.manualInitEPO;
		var composite_add_to_cart_button;
		var collection;

		if ( main_product.data( 'tm-composite-setup' ) ) {
			return;
		}

		this_epo_totals_container.addClass( 'cpf-bto-totals' );
		main_product.data( 'tm-composite-setup', 1 );

		main_cart.on( 'tm-epo-after-update', function( event, eventData ) {
			var container_id = get_composite_container_id( eventData.container );
			if ( container_id ) {
				main_product.find( '.bto_form_' + container_id + ',#composite_form_' + container_id + ',#composite_data_' + container_id ).trigger( 'cpf_bto_review' );
			}
		} );

		// support for listen to after post success event for purchasable prodcuts (2.4)
		$( tcAPI.compositeSelector ).find( '.cart' ).append( '<input type="hidden" class="tm-post-support addon">' );
		main_product.find( '.tm-post-support.addon' ).on( 'change', function() {
			$( this ).closest( tcAPI.compositeSelector ).trigger( 'wc-composite-item-updated.cpf' );
		} );

		$( tcAPI.compositeSelector )
			.off( 'found_variation.cpf' )
			.on( 'found_variation.cpf', function( event, variation ) {
				var item = $( this );
				var container_id = get_composite_container_id( item );
				var price_data = get_composite_price_data( container_id, main_product );
				var product_price;
				var item_id = get_composite_item_id( item );
				var reviewObject = main_product.find( '.bto_form,#composite_form_' + container_id + ',#composite_data_' + container_id ).find( get_review_selector( item_id ) );
				var btoPriceObject = item.find( '.cpf-bto-price' );

				if ( ! price_data ) {
					return;
				}
				reviewObject.removeData( 'cpf_review_price' );
				reviewObject.find( '.amount' ).empty();

				if ( is_per_product_pricing( price_data, item_id ) === true ) {
					product_price = parseFloat( variation.price );
				}
				item.find( '.cart' ).data( 'per_product_pricing', is_per_product_pricing( price_data, item_id ) );
				btoPriceObject.data( 'per_product_pricing', is_per_product_pricing( price_data, item_id ) );
				btoPriceObject.val( product_price );
				main_cart.data( 'per_product_pricing', true );

				item.find( '.cart' ).trigger( {
					type: 'tm-epo-update',
					norules: 1
				} );
				setTimeout( function() {
					main_cart.trigger( {
						type: 'tm-epo-update',
						norules: 1
					} );
				}, 100 );

				this_epo_totals_container.data( 'btois', 'none' );
			} )
			.off( 'wc-composite-component-loaded.cpf' )
			.on( 'wc-composite-component-loaded.cpf', function() {
				$( this ).trigger( 'wc-composite-item-updated.cpf' );
			} )
			.off( 'wc-composite-item-updated.cpf' )
			.on( 'wc-composite-item-updated.cpf', function() {
				var item = $( this );
				var item_tm_extra_product_options = item.find( '.tm-extra-product-options' );
				var container_id = get_composite_container_id( item );
				var price_data = get_composite_price_data( container_id, main_product );
				var product_price;
				var item_id = get_composite_item_id( item );
				var reviewObject = main_product.find( '.bto_form,#composite_form_' + container_id + ',#composite_data_' + container_id ).find( get_review_selector( item_id ) );
				var btoPriceObject = item.find( '.cpf-bto-price' );
				var itemCart = item.find( '.cart' );
				var epoObjectCopy = $.extend( true, {}, epoObject );

				reviewObject.removeData( 'cpf_review_price' );
				reviewObject.find( '.amount' ).empty();

				if ( ! price_data ) {
					return;
				}

				if ( is_per_product_pricing( price_data, item_id ) === true ) {
					product_price = item.find( '.bto_item_data,.component_data' ).data( 'price' );
					if ( product_price === undefined ) {
						product_price = $( '#composite_data_' + container_id ).data( 'price_data' );
						if ( product_price && product_price.prices && item_id in product_price.prices ) {
							product_price = product_price.prices[ item_id ];
						}
					}
					product_price = parseFloat( product_price );
				}
				item.find( '.cart' ).data( 'per_product_pricing', is_per_product_pricing( price_data, item_id ) );
				btoPriceObject.data( 'per_product_pricing', is_per_product_pricing( price_data, item_id ) );
				btoPriceObject.val( product_price );
				main_cart.data( 'per_product_pricing', true );

				manualInitEPO( epoObjectCopy, item, itemCart, item_tm_extra_product_options, main_product );
			} )

			.off( 'change.cpfbto', '.bto_item_options select,.component_options_select' )
			.on( 'change.cpfbto', '.bto_item_options select,.component_options_select', function() {
				var item = $( this );
				var container_id = get_composite_container_id( item );
				var item_id = get_composite_item_id( item );
				var reviewObject = main_product.find( '.bto_form,#composite_form_' + container_id + ',#composite_data_' + container_id ).find( get_review_selector( item_id ) );

				reviewObject.removeData( 'cpf_review_price' );
				reviewObject.find( '.amount' ).empty();
				if ( item.val() === '' ) {
					this_epo_totals_container.data( 'passed', false );
					this_epo_totals_container.data( 'btois', 'none' );
				} else {
					main_cart.trigger( {
						type: 'tm-epo-update',
						norules: 1
					} );
				}
			} )
			.off( 'woocommerce_variation_select_change.cpf' )
			.on( 'woocommerce_variation_select_change.cpf', function() {
				var item = $( this );
				var container_id = get_composite_container_id( item );
				var item_id = get_composite_item_id( item );
				var reviewObject = main_product.find( '.bto_form,#composite_form_' + container_id + ',#composite_data_' + container_id ).find( get_review_selector( item_id ) );

				reviewObject.removeData( 'cpf_review_price' );
				reviewObject.find( '.amount' ).empty();
				if ( item.find( '.variations .attribute-options select' ).val() === '' ) {
					this_epo_totals_container.data( 'passed', false );
					this_epo_totals_container.data( 'btois', 'none' );
				}
			} );

		main_product
			.find( '.bundle_wrap' )
			.off( 'show_bundle.cpf,wc-composite-show-add-to-cart.cpf' )
			.on( 'show_bundle.cpf,wc-composite-show-add-to-cart.cpf', function() {
				check_bto( $( this ).closest( '.cart' ).attr( 'data-container-id' ), epoObject );
			} );

		main_product
			.find( '.composite_data .composite_wrap' )
			.off( 'wc-composite-show-add-to-cart.cpf' )
			.on( 'wc-composite-show-add-to-cart.cpf', function() {
				var id = $( this ).closest( '.composite_form' ).find( '.composite_data' ).data( 'container_id' );

				check_bto( id, epoObject );
				main_product.find( '#composite_data_' + id ).trigger( 'cpf_bto_review' );
			} );

		main_product
			.find( '.bto_form,.composite_form' )
			.off( 'woocommerce-product-addons-update.cpf cpf_bto_review' )
			.on( 'woocommerce-product-addons-update.cpf cpf_bto_review', function() {
				var bto_form = $( this );
				var item;
				var item_id;
				var html;
				var widget;
				var value;
				var options;
				var composite_totals_holder;

				bto_form
					.parent()
					.find( tcAPI.compositeSelector )
					.each( function() {
						item = $( this );
						item_id = get_composite_item_id( item );
						html = bto_form.find( get_review_selector( item_id ) );
						widget = $( '.widget_composite_summary_elements' ).find( '.summary_element.summary_element_' + item_id );
						options = item.find( '.cpf-bto-optionsprice' ).val();
						composite_totals_holder = bto_form.find( '.tc-epo-totals.tm-cart-' + item_id );

						if ( ! html.length ) {
							return;
						}
						if ( html.data( 'cpf_review_price' ) ) {
							value = $.epoAPI.math.unformat( html.data( 'cpf_review_price' ), tcAPI.localDecimalSeparator );
						} else if ( html.find( '.amount' ).length ) {
							value = $.epoAPI.math.unformat( html.find( '.amount' ).html(), TMEPOJS.currency_format_decimal_sep );
							html.data( 'cpf_review_price', value );
						}

						if ( options && composite_totals_holder.data( 'tc_totals_ob' ) ) {
							html.find( '.amount' ).html( composite_totals_holder.data( 'tc_totals_ob' ).formatted_final_total );
							widget.find( '.amount' ).html( composite_totals_holder.data( 'tc_totals_ob' ).formatted_final_total );
						}
					} );
			} );

		$( tcAPI.compositeSelector ).trigger( 'wc-composite-component-loaded.cpf' );

		/* The next code is required in order to accomodate conditioanl logic
		 * otherwise hidden items are posted.
		 *
		 * The relative composite function that affects this is this.Composite_Add_To_Cart_Button_View
		 */
		composite_add_to_cart_button = $( '.composite_add_to_cart_button' );
		collection = composite_add_to_cart_button;
		collection.each( function() {
			var currentEl = $( this ) ? $( this ) : $( document );
			var events = $._data( $( this )[ 0 ], 'events' );
			var isItself = $( this )[ 0 ] === composite_add_to_cart_button[ 0 ];
			if ( ! events ) {
				return;
			}
			$.each( events, function( i, event ) {
				if ( ! event || i !== 'click' ) {
					return;
				}
				$.each( event, function( j, h ) {
					var found = false;

					if ( h ) {
						if ( h.selector && h.selector.length > 0 ) {
							currentEl.find( h.selector ).each( function() {
								if ( $( this )[ 0 ] === composite_add_to_cart_button[ 0 ] ) {
									found = true;
								}
							} );
						} else if ( ! h.selector && isItself ) {
							found = true;
						}
					}

					if ( found ) {
						// event: i
						// selector: h.selector
						// handler: h.handler
						if ( h.handler.toString().indexOf( "$( this ).prop( 'disabled', false );" ) !== -1 || h.handler.toString().indexOf( '(this).prop("disabled",!1)' ) !== -1 || h.handler.toString().indexOf( "(this).prop('disabled',!1)" ) !== -1 ) {
							composite_add_to_cart_button.off( i, h.handler );
						}
					}
				} );
			} );
		} );
	}

	function composite_support( epoObject ) {
		var main_product = epoObject.main_product;

		$( '.composite_data' ).on( 'wc-composite-initializing', function( event, composite ) {
			composite.actions.add_action(
				'component_summary_content_updated',
				function( component ) {
					var bto_form = main_product.find( '.bto_form,.composite_form' );
					var html = main_product.find( '#composite_summary_' + bto_form.data( 'product_id' ) ).find( '.summary_element.summary_element_' + component.step_id );
					var widget = $( '.widget_composite_summary_elements' ).find( '.summary_element.summary_element_' + component.step_id );
					var composite_totals_holder = bto_form.find( '.tc-epo-totals.tm-cart-' + component.step_id );

					if ( composite_totals_holder.data( 'tc_totals_ob' ) ) {
						html.find( '.amount' ).html( composite_totals_holder.data( 'tc_totals_ob' ).formatted_final_total );
						widget.find( '.amount' ).html( composite_totals_holder.data( 'tc_totals_ob' ).formatted_final_total );
					}
				},
				100,
				this
			);
		} );
	}

	function finalTotalsBoxVisibility( showTotal, dataObject ) {
		if ( dataObject.totalsHolder.attr( 'data-type' ) === 'bto' || dataObject.totalsHolder.attr( 'data-type' ) === 'composite' ) {
			if ( dataObject.this_epo_totals_container.data( 'btois' ) === 'show' ) {
				showTotal = true;
			}
		}

		if ( ! dataObject.alternativeCart && dataObject.main_product.find( '.cpf-bto-price' ).length > 0 ) {
			showTotal = true;
		}

		return showTotal;
	}

	function calculateFinalProductPrice( object, dataObject ) {
		var alternativeCart = dataObject.alternativeCart;
		var product_price = dataObject.product_price;
		var product_total_price = dataObject.product_total_price;
		var v_product_price = dataObject.v_product_price;
		var line;
		var cartQty = dataObject.cartQty;
		var main_product = dataObject.main_product;
		var product_price_bto = main_product.data( 'product_price_bto' );
		var cpf_bto_price_all = main_product.find( '.cpf-bto-price' );

		if ( ! alternativeCart && cpf_bto_price_all.length > 0 ) {
			// Fix for products that are sold individually
			if ( product_price_bto ) {
				product_price = v_product_price;
				product_total_price = parseFloat( product_price * cartQty );
				product_price_bto.forEach( function( item ) {
					line = 0;
					if ( item[ 3 ] ) {
						line = parseFloat( item[ 0 ] ) + parseFloat( item[ 1 ] );
						product_price = product_price + line;
						product_total_price = product_total_price + parseFloat( line );
					} else {
						line = ( parseFloat( item[ 0 ] ) * parseFloat( item[ 2 ] ) ) + parseFloat( item[ 1 ] );
						product_price = product_price + line;
						product_total_price = product_total_price + parseFloat( line * cartQty );
					}
				} );

				object = {
					productPrice: product_price,
					productTotalPrice: product_total_price
				};
			}
		}

		return object;
	}

	function calculateCurrentProductPrice( product_price, dataObject ) {
		var alternativeCart = dataObject.alternativeCart;
		var cart = dataObject.cart;
		var main_product = dataObject.main_product;
		var cpf_bto_price = cart.find( '.cpf-bto-price' );
		var cpf_bto_price_all = main_product.find( '.cpf-bto-price' );
		var field;
		var fieldValue;
		var qty;
		var isi;
		var optionsprice;
		var product_price_bto = [];

		if ( alternativeCart && cpf_bto_price.length > 0 ) {
			product_price = parseFloat( cpf_bto_price.val() );
		} else if ( ! alternativeCart && cpf_bto_price_all.length > 0 ) {
			cpf_bto_price_all.each( function() {
				field = $( this );
				fieldValue = field.val();
				if ( Number.isFinite( parseFloat( fieldValue ) ) ) {
					qty = field.closest( '.cart' ).find( tcAPI.qtySelector );
					if ( qty.length > 0 ) {
						qty = parseFloat( qty.val() );
					} else {
						qty = 1;
					}
					isi = field.parent().find( '.cpf-bto-totals' ).attr( 'data-is-sold-individually' );
					optionsprice = field.parent().find( '.cpf-bto-optionsprice' ).val();
					if ( Number.isFinite( parseFloat( optionsprice ) ) ) {
						optionsprice = parseFloat( optionsprice );
					} else {
						optionsprice = 0;
					}
					product_price = parseFloat( product_price ) + parseFloat( fieldValue * qty );
					product_price_bto.push( [ fieldValue, optionsprice, qty, isi ] );
				}
			} );

			main_product.data( 'product_price_bto', product_price_bto );

			main_product.find( '.cpf-bto-optionsprice' ).each( function() {
				fieldValue = $( this ).val();
				if ( Number.isFinite( parseFloat( fieldValue ) ) ) {
					product_price = parseFloat( product_price ) + parseFloat( fieldValue );
				}
			} );
		}

		return product_price;
	}

	function calculatePerProductPricing( per_product_pricing, dataObject ) {
		var alternativeCart = dataObject.alternativeCart;
		var cart = dataObject.cart;
		var cpf_bto_price = cart.find( '.cpf-bto-price' );

		if ( alternativeCart && cpf_bto_price.length > 0 ) {
			per_product_pricing = cpf_bto_price.data( 'per_product_pricing' );
		}

		return per_product_pricing;
	}

	function getCurrentProductPrice( price, currentCart, totalsHolder ) {
		var cpf_bto_price;

		if ( ! totalsHolder.length ) {
			cpf_bto_price = currentCart.find( '.cpf-bto-price' );
			if ( cpf_bto_price.length > 0 ) {
				if ( Number.isFinite( parseFloat( cpf_bto_price.val() ) ) ) {
					price = parseFloat( cpf_bto_price.val() );
				}
			}
		}

		return price;
	}

	function getNativePricesBlockSelector( selector ) {
		selector = selector + ',.bundle_price .single_variation .price,.bto_item_wrap .single_variation .price,.component_wrap .single_variation .price,.composite_wrap .single_variation .price';
		return selector;
	}

	function getItemId( id, container ) {
		return get_composite_item_id( container ) || id;
	}

	function getTotalsContainer( container, element, main_product, bundleid ) {
		if ( element.closest( tcAPI.compositeSelector ).length > 0 ) {
			container = main_product.find( '.tm-epo-totals.tm-cart-' + bundleid );
		}

		return container;
	}

	function getEpoContainer( container, element, main_product, bundleid ) {
		if ( element.closest( tcAPI.compositeSelector ).length > 0 ) {
			container = main_product.find( '.tm-extra-product-options.tm-cart-' + bundleid );
		}

		return container;
	}

	function getBundleId( bundleid, cart ) {
		if ( ! bundleid ) {
			bundleid = cart.closest( '.component_content' ).attr( 'data-product_id' );
			if ( ! bundleid ) {
				bundleid = 0;
			}
		}

		return bundleid;
	}

	function alterProductPrice( product_price, element ) {
		var cpf_bto_price;

		if ( element.closest( tcAPI.compositeSelector ).length > 0 ) {
			cpf_bto_price = element.closest( '.component_wrap' ).find( '.cpf-bto-price' );
			if ( cpf_bto_price.length > 0 ) {
				if ( cpf_bto_price.data( 'per_product_pricing' ) ) {
					product_price = cpf_bto_price.val();
				} else {
					product_price = false;
				}
				cpf_bto_price.val( product_price );
			}
		}

		return product_price;
	}

	function getPerProductPricing( per_product_pricing, element ) {
		var item = element.closest( tcAPI.compositeSelector );
		var priceData;
		var item_id = get_composite_item_id( item );

		if ( item.length > 0 ) {
			priceData = get_composite_price_data( get_composite_container_id( item ), element );
			if ( priceData ) {
				per_product_pricing = is_per_product_pricing( priceData, item_id );
			}
		}

		return per_product_pricing;
	}

	function tmVariationCheckMatchVariationsForm( variationsForm, epoId, productId, $element ) {
		var component_data = $element.closest( '.component_data' );

		if ( component_data.find( '.variations' ).length ) {
			variationsForm = $element.closest( component_data );
		}

		return variationsForm;
	}

	$( window ).on( 'epo-after-init-in-timeout', function( event, epoObject ) {
		if ( event && epoObject && epoObject.epo ) {
			bto_support( epoObject.epo );
		}
	} );

	$( window ).on( 'epo-after-init', function( event, epoObject ) {
		if ( event && epoObject && epoObject.epo ) {
			composite_support( epoObject.epo );
		}
	} );

	$( window ).on( 'tcEpoBeforeOptionPriceCalculation', function( event, dataObject ) {
		if ( event && dataObject && dataObject.epo ) {
			if ( dataObject.alternativeCart ) {
				if ( ( dataObject.this_product_type === 'variable' || dataObject.this_product_type === 'variable-subscription' ) && ! dataObject.totalsHolder.data( 'moved_inside' ) ) {
					dataObject.totalsHolder.data( 'moved_inside', 1 );
				}
			}

			// Move total box of main cart if is composite
			if ( dataObject.epo.main_epo_inside_form && TMEPOJS.tm_epo_totals_box_placement === 'woocommerce_before_add_to_cart_button' ) {
				if ( ( dataObject.this_product_type === 'bto' || dataObject.this_product_type === 'composite' ) && ! dataObject.totalsHolder.data( 'moved_inside' ) ) {
					dataObject.cart.find( '.bundle_price,.composite_price' ).after( dataObject.totalsHolder );
					dataObject.totalsHolder.data( 'moved_inside', 1 );
				}
			}
		}
	} );

	$( window ).on( 'tcEpoMaybeChangePriceHtml', function( event, dataObject ) {
		if ( event && dataObject && dataObject.epo ) {
			if ( dataObject.bundleid && dataObject.alternativeCart && dataObject.cart.find( '.cpf-bto-price' ).length > 0 && dataObject.totalsObject.total_plus_fee > 0 ) {
				$( '#component_' + dataObject.bundleid )
					.find( tcAPI.compositeComponentPriceSelector )
					.html( $.epoAPI.util.decodeHTML( $.epoAPI.template.html( tcAPI.templateEngine.tc_formatted_price, { price: dataObject.nativePrice } ) ) )
					.show();
			} else if ( ! dataObject.alternativeCart && dataObject.main_product.find( '.cpf-bto-price' ).length > 0 && dataObject.totalsObject.total_plus_fee > 0 ) {
				dataObject.cart
					.find( tcAPI.compositePriceSelector )
					.html( $.epoAPI.util.decodeHTML( $.epoAPI.template.html( tcAPI.templateEngine.tc_formatted_price, { price: dataObject.nativePrice } ) ) )
					.show();
			}
		}
	} );

	$( window ).on( 'tcEpoAfterCalculateTotals', function( event, dataObject ) {
		if ( event && dataObject && dataObject.epo ) {
			if ( dataObject.alternativeCart ) {
				if ( dataObject.per_product_pricing ) {
					dataObject.cart.find( '.cpf-bto-optionsprice' ).val( parseFloat( dataObject.totalsObject.raw_options_total_price ) );
				}
			}
		}
	} );

	$( document ).ready( function() {
		TMEPOJS = window.TMEPOJS || null;
		tcAPI = $.tcAPI();

		if ( ! TMEPOJS || ! tcAPI ) {
			return;
		}

		tcAPI.compositePriceSelector = '.composite_data .composite_wrap .price .amount';
		tcAPI.compositeComponentPriceSelector = '.component_wrap > .price';

		$.epoAPI.addFilter( 'tcFinalTotalsBoxVisibility', finalTotalsBoxVisibility, 10, 2 );
		$.epoAPI.addFilter( 'tcCalculateFinalProductPrice', calculateFinalProductPrice, 10, 2 );
		$.epoAPI.addFilter( 'tcCalculateCurrentProductPrice', calculateCurrentProductPrice, 10, 2 );
		$.epoAPI.addFilter( 'tcCalculatePerProductPricing', calculatePerProductPricing, 10, 2 );

		$.epoAPI.addFilter( 'tcGetCurrentProductPrice', getCurrentProductPrice, 10, 3 );
		$.epoAPI.addFilter( 'tcGetNativePricesBlockSelector', getNativePricesBlockSelector, 10, 1 );
		$.epoAPI.addFilter( 'tc_get_item_id', getItemId, 10, 2 );
		$.epoAPI.addFilter( 'tc_get_totals_container', getTotalsContainer, 10, 4 );
		$.epoAPI.addFilter( 'tc_get_epo_container', getEpoContainer, 10, 4 );
		$.epoAPI.addFilter( 'tc_get_bundleid', getBundleId, 10, 2 );
		$.epoAPI.addFilter( 'tc_alter_product_price', alterProductPrice, 10, 2 );
		$.epoAPI.addFilter( 'tc_get_per_product_pricing', getPerProductPricing, 10, 2 );

		$.epoAPI.addFilter( 'tm_variation_check_match_variationsForm', tmVariationCheckMatchVariationsForm, 10, 4 );
	} );
}( window, document, window.jQuery ) );
