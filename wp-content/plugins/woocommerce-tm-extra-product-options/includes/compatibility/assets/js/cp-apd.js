( function( window, document, $ ) {
	'use strict';

	var TMEPOJS;
	var tcAPI;

	function tc_round( value, precision, mode ) {
		return $.epoAPI.math.round( value, precision, mode );
	}

	function multFloats( a, b ) {
		var atens = Math.pow( 10, String( a ).length - String( a ).indexOf( '.' ) - 1 );
		var btens = Math.pow( 10, String( b ).length - String( b ).indexOf( '.' ) - 1 );

		return ( a * atens * ( b * btens ) ) / ( atens * btens );
	}

	function getDiscountObj( totalsHolder, rules, current_variation, cv, qty, force ) {
		var discount = [ false, false ];

		$( rules[ current_variation ] ).each( function( id, rule ) {
			var min = parseFloat( rule.min );
			var max = parseFloat( rule.max );
			var type = rule.type;
			var value = parseFloat( rule.value );

			if ( force || ( ! Number.isFinite( max ) && min <= qty ) || ( Number.isFinite( max ) && min <= qty && qty <= max ) ) {
				if ( ( totalsHolder.attr( 'data-tm-epo-apd-change-display-prices' ) === 'change_simple' || totalsHolder.attr( 'data-tm-epo-apd-change-display-prices' ) === 'change_all' ) && min === 1 && totalsHolder.data( 'priceIsWithDiscount' ) ) {
					discount = [ value, type, true ];
				} else {
					discount = [ value, type ];
				}

				/**
				 * we disable the next line to take into account the "All
				 * applicable rules" functionality note: find a better way to
				 * do this as it produces more loops
				 * return false;
				 */
			}
		} );

		return discount;
	}

	// Calculate the product price
	function calculateProductPrice( price, totalsHolder ) {
		var rules = totalsHolder.data( 'product-price-rules' );
		var mainCart = totalsHolder.data( 'tm_for_cart' );
		var qty_element;
		var qty;
		var variation_id_selector;
		var current_variation;
		var cv;
		var discount;
		var value;
		var type;
		var _dc;
		var mainQty;
		var discounted_price = false;

		if ( ! ( ! rules || ! mainCart || $.isEmptyObject( rules ) || $.isEmptyObject( mainCart ) ) ) {
			price = parseFloat( price ) || 0;
			qty_element = totalsHolder.data( 'qty_element' );
			qty = parseFloat( qty_element.val() );
			variation_id_selector = totalsHolder.data( 'variationIdElement' );
			current_variation = parseFloat( variation_id_selector.val() );
			cv = current_variation;

			if ( ! totalsHolder.is( '.tm-cart-inline' ) && variation_id_selector.length > 0 && ( ! current_variation || current_variation === 0 ) ) {
				return false;
			}
			if ( ! current_variation ) {
				current_variation = 0;
			}
			if ( ! rules[ current_variation ] ) {
				current_variation = 0;
			}
			if ( ! Number.isFinite( qty ) ) {
				if ( totalsHolder.attr( 'data-is-sold-individually' ) || qty_element.length === 0 ) {
					qty = 1;
				}
			}

			if ( TMEPOJS.tm_epo_global_product_element_quantity_sync === 'yes' && mainCart.is( tcAPI.associatedEpoCart ) ) {
				mainQty = parseFloat( $( '.tc-epo-totals[data-epo-id=' + mainCart.closest( tcAPI.epoSelector ).data( 'epoId' ) + ']' ).data( 'qty_element' ).val() );
				if ( ! Number.isFinite( mainQty ) ) {
					mainQty = 1;
				}
				qty = qty * mainQty;
			}

			if ( ( rules[ current_variation ] && current_variation !== 0 ) || rules[ 0 ] ) {
				if ( ! rules[ current_variation ] ) {
					current_variation = 0;
				}
				_dc = parseInt( TMEPOJS.currency_format_num_decimals, 10 );
				rules[ current_variation ].forEach( function( _rule ) {
					var _rules = [];
					_rules[ current_variation ] = _rule;

					discount = getDiscountObj( totalsHolder, _rules, current_variation, cv, qty, false );
					type = discount[ 1 ];
					if ( ! discount[ 2 ] || ( type === 'fixed_amount' ) ) {
						value = discount[ 0 ];
						if ( discounted_price === false ) {
							if ( current_variation ) {
								discounted_price = totalsHolder.data( 'variations' );
								discounted_price = parseFloat( discounted_price[ current_variation ] ) || 0;
							} else {
								discounted_price = price = parseFloat( totalsHolder.attr( 'data-price' ) ) || 0;
							}
						}

						switch ( type ) {
							case 'percentage':
								discounted_price = discounted_price - ( Math.ceil( ( multFloats( discounted_price, ( value / 100 ) ) * Math.pow( 10, _dc ) ) - 0.5 ) * Math.pow( 10, -_dc ) );
								discounted_price = tc_round( discounted_price, _dc );
								if ( discounted_price < 0 ) {
									discounted_price = 0;
								}
								break;

							case 'fixed_value':
								discounted_price = discounted_price - value;
								if ( discounted_price < 0 ) {
									discounted_price = 0;
								}
								break;

							case 'fixed_amount':
								discounted_price = value;
								if ( discounted_price < 0 ) {
									discounted_price = 0;
								}
								break;
						}
					}
				} );
				if ( discounted_price !== false ) {
					price = discounted_price;
				}
			}
		}

		totalsHolder.closest( '.tc-totals-form' ).find( '.cpf-product-price' ).val( price );

		return price;
	}

	function maybe_alter_pricing_table( o ) {
		var totals = o.totals_holder;
		var object = o.epo;
		var epo_object = o.data.epo_object;
		var pt = epo_object.main_product.find( '.wdp_pricing_table' ).find( 'td > .amount' );
		var vpt = epo_object.main_product.find( '.rp_wcdpd_pricing_table_variation_container' );
		var enable_pricing_table = totals.attr( 'data-tm-epo-apd-enable-pricing-table' );
		var local_decimal_separator;
		var local_thousand_separator;
		var rules;
		var $cart;
		var apply_dpd;
		var product_price;
		var ot;
		var variation_id_selector;
		var current_variation;
		var cv;

		if ( vpt.length > 0 ) {
			pt = vpt.find( '.rp_wcdpd_pricing_table_variation:visible' ).find( '.rp_wcdpd_pricing_table' ).find( 'td > .amount' );
		}
		if ( enable_pricing_table !== 'yes' || ! pt.length ) {
			return;
		}

		local_decimal_separator = TMEPOJS.tm_epo_global_displayed_decimal_separator === '' ? TMEPOJS.currency_format_decimal_sep : $.epoAPI.locale.getSystemDecimalSeparator();
		local_thousand_separator = TMEPOJS.tm_epo_global_displayed_decimal_separator === '' ? TMEPOJS.currency_format_thousand_sep : $.epoAPI.locale.getSystemDecimalSeparator() === ',' ? '.' : ',';

		rules = totals.data( 'product-price-rules' );
		$cart = totals.data( 'tm_for_cart' );
		apply_dpd = totals.data( 'fields-price-rules' );
		product_price = parseFloat( totals.data( 'price' ) );
		ot = $.epoAPI.math.unformat( object.options_price_per_unit, TMEPOJS.currency_format_decimal_sep );

		product_price = $.epoAPI.math.unformat( o.data.tm_set_price( product_price, totals, false ), TMEPOJS.currency_format_decimal_sep );

		if ( rules && $cart ) {
			variation_id_selector = totals.data( 'variationIdElement' );
			current_variation = parseFloat( variation_id_selector.val() );
			cv = current_variation;

			if ( variation_id_selector.length > 0 && ( ! current_variation || current_variation === 0 ) ) {
				current_variation = 0;
			}

			if ( ( rules[ current_variation ] && current_variation !== 0 ) || rules[ 0 ] ) {
				if ( ! rules[ current_variation ] ) {
					current_variation = 0;
				}

				$( rules[ current_variation ] ).each( function( id, rule ) {
					var discount = getDiscountObj( totals, rules, current_variation, cv, object.qty );
					var dprice = ot;
					var value = discount[ 0 ];
					var type = discount[ 1 ];
					var _dc = parseInt( TMEPOJS.currency_format_num_decimals, 10 );
					var price = dprice;
					var ruletype = rule.type;
					var rulevalue = parseFloat( rule.value );
					var new_product_price = product_price;
					var table_price;

					switch ( type ) {
						case 'percentage':
							price = price / ( 1 - ( value / 100 ) );
							price = ( Math.ceil( price * Math.pow( 10, _dc ) ) - 0.5 ) * Math.pow( 10, -_dc );
							price = tc_round( price, _dc );
							if ( price < 0 ) {
								price = 0;
							}
							break;

						case 'fixed_value':
							price = price + ( value * object.qty );
							price = Math.ceil( ( price * Math.pow( 10, _dc ) ) - 0.5 ) * Math.pow( 10, -_dc );
							price = tc_round( price, _dc );
							if ( price < 0 ) {
								price = 0;
							}
							break;

						case 'fixed_amount':
							// not supported
							break;
					}

					switch ( ruletype ) {
						case 'percentage':
							if ( apply_dpd ) {
								price = price * ( 1 - ( rulevalue / 100 ) );
							}
							new_product_price = new_product_price * ( 1 - ( rulevalue / 100 ) );
							break;

						case 'fixed_value':
							if ( apply_dpd ) {
								price = price - rulevalue;
							}
							new_product_price = new_product_price - rulevalue;
							break;

						case 'fixed_amount':
							if ( apply_dpd ) {
								price = rulevalue;
							}
							new_product_price = rulevalue;
							break;
					}

					table_price = $.epoAPI.math.format( new_product_price + price, {
						symbol: TMEPOJS.currency_format_symbol,
						decimal: local_decimal_separator,
						thousand: local_thousand_separator,
						precision: TMEPOJS.currency_format_num_decimals,
						format: TMEPOJS.currency_format
					} );
					$( pt[ id ] ).html( table_price );
				} );
			}
		}
	}

	function tm_get_dpd( totals, epo_object, apply ) {
		var price;
		var rules;
		var $cart;
		var variation_id_selector;
		var current_variation;
		var cv;
		var qty_element;
		var qty;
		var current_discount_type;
		var _dc = parseInt( TMEPOJS.currency_format_num_decimals, 10 );

		if ( apply !== 1 ) {
			return false;
		}

		price = [ false, false ];
		rules = totals.data( 'product-price-rules' );
		$cart = totals.data( 'tm_for_cart' );

		if ( ! rules || ! $cart ) {
			return false;
		}
		variation_id_selector = totals.data( 'variationIdElement' );
		current_variation = parseFloat( variation_id_selector.val() );
		cv = current_variation;
		qty_element = totals.data( 'qty_element' );
		qty = parseFloat( qty_element.val() );

		if ( variation_id_selector.length > 0 && ( ! current_variation || current_variation === 0 ) ) {
			current_variation = 0;
		}

		if ( ! current_variation ) {
			current_variation = 0;
		}
		if ( ! Number.isFinite( qty ) ) {
			if ( totals.attr( 'data-is-sold-individually' ) || qty_element.length === 0 ) {
				qty = 1;
			}
		}
		if ( ( rules[ current_variation ] && current_variation !== 0 ) || rules[ 0 ] ) {
			if ( ! rules[ current_variation ] ) {
				current_variation = 0;
			}
			current_discount_type = '';
			$( rules[ current_variation ] ).each( function( id, _rule ) {
				var _rules = [];
				var type;
				var value;
				var discountObj;
				_rules[ current_variation ] = _rule;
				discountObj = getDiscountObj( totals, _rules, current_variation, cv, qty );
				type = discountObj[ 1 ];
				if ( current_discount_type === '' ) {
					current_discount_type = type;
				} else if ( current_discount_type !== type ) {
					return false;
				}
				if ( ! discountObj[ 2 ] ) {
					value = discountObj[ 0 ];
				}

				switch ( type ) {
					case 'percentage':
						if ( price[ 0 ] ) {
							value = 100 * ( 1 - ( ( ( 100 - $.epoAPI.math.toFloat( price[ 0 ] ) ) / 100 ) * ( ( 100 - $.epoAPI.math.toFloat( value ) ) / 100 ) ) );
						}
						break;
					case 'fixed_value':
						if ( price[ 0 ] ) {
							value = $.epoAPI.math.toFloat( price[ 0 ] ) + $.epoAPI.math.toFloat( value );
						}
						break;
					case 'fixed_amount':
						break;
				}
				value = tc_round( value, _dc );

				price = [ value, type ];
			} );
		}

		return price;
	}

	function tc_apply_dpd( price, totalsHolder, apply, force ) {
		var rules;
		var $cart;
		var variation_id_selector;
		var current_variation;
		var cv;
		var qty_element;
		var qty;
		var discount;
		var value;
		var type;
		var _dc;
		var discounted_price;

		if ( typeof price === 'object' ) {
			price = price[ 0 ];
			if ( ! Number.isFinite( parseFloat( price ) ) ) {
				price = 0;
			}
		}
		if ( apply !== 1 ) {
			return price;
		}

		rules = totalsHolder.data( 'product-price-rules' );
		$cart = totalsHolder.data( 'tm_for_cart' );

		if ( ! rules || ! $cart ) {
			return price;
		}
		variation_id_selector = totalsHolder.data( 'variationIdElement' );
		current_variation = parseFloat( variation_id_selector.val() );
		cv = current_variation;
		qty_element = totalsHolder.data( 'qty_element' );
		qty = parseFloat( qty_element.val() );

		if ( variation_id_selector.length > 0 && ( ! current_variation || current_variation === 0 ) ) {
			current_variation = 0;
		}

		if ( ! current_variation ) {
			current_variation = 0;
		}
		if ( ! Number.isFinite( qty ) ) {
			if ( totalsHolder.attr( 'data-is-sold-individually' ) || qty_element.length === 0 ) {
				qty = 1;
			}
		}
		if ( ( rules[ current_variation ] && current_variation !== 0 ) || rules[ 0 ] ) {
			if ( ! rules[ current_variation ] ) {
				current_variation = 0;
			}

			if ( price === undefined ) {
				price = 0;
			}

			discounted_price = false;
			_dc = parseInt( TMEPOJS.currency_format_num_decimals, 10 );
			$( rules[ current_variation ] ).each( function( id, _rule ) {
				var _rules = [];
				_rules[ current_variation ] = _rule;
				discount = getDiscountObj( totalsHolder, _rules, current_variation, cv, qty, force );
				value = discount[ 0 ];
				type = discount[ 1 ];

				if ( discounted_price === false ) {
					discounted_price = price;
				}

				switch ( type ) {
					case 'percentage':
						discounted_price = discounted_price - ( Math.ceil( ( multFloats( discounted_price, ( value / 100 ) ) * Math.pow( 10, _dc ) ) - 0.5 ) * Math.pow( 10, -_dc ) );
						discounted_price = tc_round( discounted_price, _dc );
						if ( discounted_price < 0 ) {
							discounted_price = 0;
						}
						break;

					case 'fixed_value':
						discounted_price = discounted_price - value;
						if ( discounted_price < 0 ) {
							discounted_price = 0;
						}
						break;

					case 'fixed_amount':
						discounted_price = value;
						if ( discounted_price < 0 ) {
							discounted_price = 0;
						}
						break;
				}
			} );

			if ( discounted_price !== false ) {
				price = discounted_price;
			}
		}

		return price;
	}

	function alterProductPrice( product_price, element, cart, epoTotalsContainer ) {
		var mode = epoTotalsContainer.attr( 'data-tm-epo-apd-original-price-base' );
		var type;
		var undiscountedProductPrice;
		var variation_id_selector;
		var current_variation;

		if ( mode === 'undiscounted' ) {
			type = epoTotalsContainer.attr( 'data-type' );
			if ( type === 'variable' || type === 'variable-subscription' ) {
				variation_id_selector = epoTotalsContainer.data( 'variationIdElement' );
				current_variation = parseFloat( variation_id_selector.val() );
				if ( variation_id_selector.length > 0 && ( ! current_variation || current_variation === 0 ) ) {
					current_variation = 0;
				}
				if ( ! current_variation ) {
					current_variation = 0;
				}
				undiscountedProductPrice = epoTotalsContainer.data( 'variations' )[ current_variation ];
			} else {
				undiscountedProductPrice = epoTotalsContainer.attr( 'data-price' );
			}
			if ( undiscountedProductPrice !== undefined ) {
				return undiscountedProductPrice;
			}
		}

		return product_price;
	}

	function useUndiscountedPrice( use, element, cart, epoTotalsContainer ) {
		var mode = epoTotalsContainer.attr( 'data-tm-epo-apd-original-price-base' );
		var undiscountedProductPrice = epoTotalsContainer.attr( 'data-price' );

		if ( mode === 'undiscounted' && undiscountedProductPrice !== undefined ) {
			return true;
		}

		return use;
	}

	function tc_adjust_product_total_price( product_total_price, product_total_price_without_options, total_plus_fee, extraFee, total, cart_fee_options_total, totalsHolder ) {
		var rules = totalsHolder.data( 'product-price-rules' );
		var mainCart = totalsHolder.data( 'tm_for_cart' );
		var qty_element;
		var qty;
		var variation_id_selector;
		var current_variation;
		var cv;
		var discount;
		var type;
		var mainQty;
		var dpdEnabled = totalsHolder.data( 'tm-epo-apd-enable' ) === 'yes';

		if ( dpdEnabled && ( ! ( ! rules || ! mainCart || $.isEmptyObject( rules ) || $.isEmptyObject( mainCart ) ) ) ) {
			qty_element = totalsHolder.data( 'qty_element' );
			qty = parseFloat( qty_element.val() );
			variation_id_selector = totalsHolder.data( 'variationIdElement' );
			current_variation = parseFloat( variation_id_selector.val() );
			cv = current_variation;

			if ( variation_id_selector.length > 0 && ( ! current_variation || current_variation === 0 ) ) {
				return product_total_price;
			}
			if ( ! current_variation ) {
				current_variation = 0;
			}
			if ( ! rules[ current_variation ] ) {
				current_variation = 0;
			}
			if ( ! Number.isFinite( qty ) ) {
				if ( totalsHolder.attr( 'data-is-sold-individually' ) || qty_element.length === 0 ) {
					qty = 1;
				}
			}

			if ( TMEPOJS.tm_epo_global_product_element_quantity_sync === 'yes' && mainCart.is( tcAPI.associatedEpoCart ) ) {
				mainQty = parseFloat( $( '.tc-epo-totals[data-epo-id=' + mainCart.closest( tcAPI.epoSelector ).data( 'epoId' ) + ']' ).data( 'qty_element' ).val() );
				if ( ! Number.isFinite( mainQty ) ) {
					mainQty = 1;
				}
				qty = qty * mainQty;
			}

			if ( ( rules[ current_variation ] && current_variation !== 0 ) || rules[ 0 ] ) {
				if ( ! rules[ current_variation ] ) {
					current_variation = 0;
				}

				discount = getDiscountObj( totalsHolder, rules, current_variation, cv, qty );
				type = discount[ 1 ];
				if ( type === 'fixed_amount' ) {
					return product_total_price_without_options;
				}
			}
		}

		return product_total_price;
	}

	$( window ).on( 'epoEventHandlers', function( event, dataObject ) {
		var epoObject = dataObject.epoObject;
		var currentCart = dataObject.currentCart;
		var totalsHolder = dataObject.totalsHolder;
		var variation_id_selector = dataObject.variation_id_selector;
		var qtyElement = dataObject.qtyElement;

		// DPD update displayed values when rules change
		currentCart.off( 'tm-epo-check-dpd' ).on( 'tm-epo-check-dpd', function() {
			var rules = totalsHolder.data( 'product-price-rules' );
			var qty;
			var qty_prev;
			var current_variation;
			var min;
			var max;

			if ( rules && currentCart ) {
				qty = parseFloat( qtyElement.val() );
				qty_prev = parseFloat( qtyElement.data( 'tm-prev-value' ) );
				current_variation = currentCart.find( variation_id_selector ).val();

				if ( ! current_variation ) {
					current_variation = 0;
				}
				if ( ! Number.isFinite( qty ) ) {
					if ( totalsHolder.attr( 'data-is-sold-individually' ) || qtyElement.length === 0 ) {
						qty = 1;
					}
				}

				if ( ( rules[ current_variation ] && $.epoAPI.math.toFloat( current_variation ) !== 0 ) || rules[ 0 ] ) {
					if ( ! rules[ current_variation ] ) {
						current_variation = 0;
					}
					$( rules[ $.epoAPI.math.toFloat( current_variation ) ] ).each( function( id, rule ) {
						min = parseFloat( rule.min );
						max = parseFloat( rule.max );

						if ( ( ! Number.isFinite( max ) && min <= qty ) || ( Number.isFinite( max ) && min <= qty && qty <= max ) ) {
							if ( ! ( min <= qty_prev && qty_prev <= max ) ) {
								$( window ).trigger( 'epoCalculateRules', {
									epoObject: epoObject,
									currentCart: currentCart
								} );
							}
						} else if ( min > qty ) {
							$( window ).trigger( 'epoCalculateRules', {
								epoObject: epoObject,
								currentCart: currentCart
							} );
						}
					} );
				}
			}
		} );
	} );

	// document ready
	$( function() {
		TMEPOJS = window.TMEPOJS || null;
		tcAPI = $.tcAPI ? $.tcAPI() : null;

		if ( ! TMEPOJS || ! tcAPI ) {
			return;
		}

		$.epoAPI.addFilter( 'tc_calculate_product_price', calculateProductPrice, 10, 2 );
		$.epoAPI.addFilter( 'tc_apply_dpd', tc_apply_dpd, 10, 4 );
		$.epoAPI.addFilter( 'tc_alter_product_price', alterProductPrice, 10, 4 );
		$.epoAPI.addFilter( 'tc_use_undiscounted_price', useUndiscountedPrice, 10, 4 );
		$.epoAPI.addFilter( 'tc_adjust_product_total_price', tc_adjust_product_total_price, 10, 7 );

		$.fn.getDiscountObj = getDiscountObj;

		// Enable alteration of pricing table
		$( window ).on( 'tc-epo-after-update', function( e, o ) {
			if ( o && o.data && o.epo && o.totals_holder ) {
				maybe_alter_pricing_table( o );
			}
		} );

		// Prefix label and Suffix label
		$( window ).on( 'tc-totals-container', function( e, o ) {
			var totalsHolder;
			var epo_object;
			var tm_set_price;
			var qty;
			var apply_dpd;
			var dpd_prefix;
			var dpd_suffix;
			var dpd_discount;
			var dpd_string;
			var dpd_discount_type;
			var dpd_discount_string;
			var dpd_css_selector;
			var dpd_string_placement;

			if ( o && o.data && o.totals_holder ) {
				totalsHolder = o.totals_holder;
				epo_object = o.data.epo_object;
				tm_set_price = o.data.tm_set_price;
				qty = o.data.qty;

				// set to totalsHolder.data("fields-price-rules") if you want the current setting
				apply_dpd = 1;
				dpd_prefix = totalsHolder.data( 'tm-epo-apd-prefix' );
				dpd_suffix = totalsHolder.data( 'tm-epo-apd-suffix' );

				if ( apply_dpd === 1 ) {
					dpd_discount = tm_get_dpd( totalsHolder, epo_object, apply_dpd );
					dpd_string = '';
					if ( dpd_discount[ 0 ] && dpd_discount[ 1 ] && ( dpd_prefix || dpd_suffix ) ) {
						dpd_discount_type = dpd_discount[ 1 ];
						dpd_discount_string = '';
						switch ( dpd_discount_type ) {
							case 'percentage':
								dpd_discount_string = dpd_discount[ 0 ] + '%';
								break;
							case 'fixed_value':
								dpd_discount_string = tm_set_price( dpd_discount[ 0 ] * qty, totalsHolder, false, false );
								break;
							case 'fixed_amount':
								dpd_discount_string = tm_set_price( ( parseFloat( totalsHolder.data( 'price' ) ) - dpd_discount[ 0 ] ) * qty, totalsHolder, false, false );
								break;
						}
						dpd_string = dpd_prefix + ' ' + dpd_discount_string + ' ' + dpd_suffix;
					}
					if ( dpd_string ) {
						dpd_css_selector = totalsHolder.data( 'tm-epo-apd-label-css-selector' );
						dpd_string_placement = totalsHolder.data( 'tm-epo-apd-string-placement' );
						if ( dpd_css_selector === '' ) {
							dpd_css_selector = '.tm-final-totals .amount.final';
						}
						if ( dpd_string_placement === 'after' ) {
							totalsHolder.find( dpd_css_selector ).after( '<span class="tm-dpd-label">' + dpd_string + '</span>' );
						} else {
							totalsHolder.find( dpd_css_selector ).before( '<span class="tm-dpd-label">' + dpd_string + '</span>' );
						}
					}
				}
			}
		} );
	} );
}( window, document, window.jQuery ) );
