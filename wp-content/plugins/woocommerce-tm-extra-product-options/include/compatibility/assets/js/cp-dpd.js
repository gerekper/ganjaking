( function( window, document, $ ) {
	'use strict';

	var TMEPOJS;

	function tc_round( value, precision, mode ) {
		return $.epoAPI.math.round( value, precision, mode );
	}

	function multFloats( a, b ) {
		var atens = Math.pow( 10, String( a ).length - String( a ).indexOf( '.' ) - 1 );
		var btens = Math.pow( 10, String( b ).length - String( b ).indexOf( '.' ) - 1 );

		return ( a * atens * ( b * btens ) ) / ( atens * btens );
	}

	function getChosenAttributes( form ) {
		var attributeFields = form.find( '.variations select' );
		var data = {};
		var count = 0;
		var chosen = 0;

		attributeFields.each( function() {
			var field = $( this );
			var attribute_name = field.data( 'attribute_name' ) || field.attr( 'name' );
			var value = field.val() || '';

			if ( value.length > 0 ) {
				chosen = chosen + 1;
			}

			count = count + 1;
			data[ attribute_name ] = value;
		} );

		return {
			count: count,
			chosenCount: chosen,
			data: data
		};
	}

	function getDiscountObj( totalsHolder, rules, current_variation, cv, qty, force ) {
		var discount = [ false, false ];

		$( rules[ current_variation ] ).each( function( id, rule ) {
			var min = parseFloat( rule.min );
			var max = parseFloat( rule.max );
			var type = rule.type;
			var value = parseFloat( rule.value );
			var found = true;

			if ( id !== undefined && rule.conditions ) {
				$( rule.conditions ).each( function( cid, condition ) {
					var att_ids;
					var chosen_atts;
					var c;
					var product_attributes;

					if ( cid !== undefined && condition.type === 'product__attributes' ) {
						att_ids = totalsHolder.data( 'tm-epo-dpd-attributes-to-id' );
						chosen_atts = getChosenAttributes( totalsHolder.data( 'tm_for_cart' ) );
						c = [];
						product_attributes = condition.product_attributes;

						Object.keys( chosen_atts.data ).forEach( function( item ) {
							if ( item && att_ids[ item ] ) {
								c[ c.length ] = att_ids[ item ][ chosen_atts.data[ item ] ].toString();
							}
						} );

						product_attributes = product_attributes.map( function( item ) {
							return item.toString();
						} );
						c = c.map( function( item ) {
							return item.toString();
						} );

						if ( condition.method_option === 'at_least_one' ) {
							found = product_attributes.some( function( item ) {
								return $.inArray( item.toString(), c ) !== -1;
							} );
						} else if ( condition.method_option === 'all' ) {
							found = product_attributes.every( function( item ) {
								return $.inArray( item.toString(), c ) !== -1;
							} );
						} else if ( condition.method_option === 'only' ) {
							found =
								product_attributes.every( function( item ) {
									return $.inArray( item.toString(), c ) !== -1;
								} ) &&
								c.every( function( item ) {
									return $.inArray( item.toString(), product_attributes ) !== -1;
								} );
						} else if ( condition.method_option === 'none' ) {
							found = ! product_attributes.some( function( item ) {
								return $.inArray( item.toString(), c ) !== -1;
							} );
						}
					}

					if ( condition.type === 'product__variation' ) {
						if ( condition.method_option === 'in_list' ) {
							if ( $.inArray( cv.toString(), condition.product_variations ) !== -1 ) {
								found = true;
							} else {
								found = false;
							}
						} else if ( $.inArray( cv.toString(), condition.product_variations ) === -1 ) {
							found = true;
						} else {
							found = false;
						}
					}
				} );
			}

			if ( ! found ) {
				return true;
			}

			if ( force || ( ! Number.isFinite( max ) && min <= qty ) || ( Number.isFinite( max ) && min <= qty && qty <= max ) ) {
				if ( min === 1 && totalsHolder.data( 'priceIsWithDiscount' ) ) {
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

	/**
	 * Calculate the product price
	 *
	 * @since  1.0
	 * @return String
	 */
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

		if ( ! ( ! rules || ! mainCart || $.isEmptyObject( rules ) || $.isEmptyObject( mainCart ) ) ) {
			price = parseFloat( price ) || 0;
			qty_element = totalsHolder.data( 'qty_element' );
			qty = parseFloat( qty_element.val() );
			variation_id_selector = totalsHolder.data( 'variationIdElement' );
			current_variation = parseFloat( variation_id_selector.val() );
			cv = current_variation;

			if ( variation_id_selector.length > 0 && ( ! current_variation || current_variation === 0 ) ) {
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
			if ( ( rules[ current_variation ] && current_variation !== 0 ) || rules[ 0 ] ) {
				if ( ! rules[ current_variation ] ) {
					current_variation = 0;
				}

				discount = getDiscountObj( totalsHolder, rules, current_variation, cv, qty );
				if ( ! discount[ 2 ] ) {
					value = discount[ 0 ];
					type = discount[ 1 ];
					_dc = parseInt( TMEPOJS.currency_format_num_decimals, 10 );

					switch ( type ) {
						case 'percentage':
						case 'discount__percentage':

							price = price - ( Math.ceil( ( multFloats( price, ( value / 100 ) ) * Math.pow( 10, _dc ) ) - 0.5 ) * Math.pow( 10, -_dc ) );
							price = tc_round( price, _dc );
							if ( price < 0 ) {
								price = 0;
							}
							break;

						case 'price':
						case 'discount__amount':
							price = price - value;
							if ( price < 0 ) {
								price = 0;
							}
							break;

						case 'fixed':
						case 'fixed__price':
							price = value;
							if ( price < 0 ) {
								price = 0;
							}
							break;
					}
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
		var pt = epo_object.main_product.find( '.rp_wcdpd_pricing_table' ).find( 'td > .amount' );
		var vpt = epo_object.main_product.find( '.rp_wcdpd_pricing_table_variation_container' );
		var enable_pricing_table = totals.attr( 'data-tm-epo-dpd-enable-pricing-table' );
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
						case 'discount__percentage':
							price = price / ( 1 - ( value / 100 ) );
							price = ( Math.ceil( price * Math.pow( 10, _dc ) ) - 0.5 ) * Math.pow( 10, -_dc );
							price = tc_round( price, _dc );
							if ( price < 0 ) {
								price = 0;
							}
							break;

						case 'price':
						case 'discount__amount':
							price = price + ( value * object.qty );
							price = Math.ceil( ( price * Math.pow( 10, _dc ) ) - 0.5 ) * Math.pow( 10, -_dc );
							price = tc_round( price, _dc );
							if ( price < 0 ) {
								price = 0;
							}
							break;

						case 'fixed':
						case 'fixed__price':
							// not supported
							break;
					}

					switch ( ruletype ) {
						case 'percentage':
						case 'discount__percentage':
							if ( apply_dpd ) {
								price = price * ( 1 - ( rulevalue / 100 ) );
							}
							new_product_price = new_product_price * ( 1 - ( rulevalue / 100 ) );
							break;

						case 'price':
						case 'discount__amount':
							if ( apply_dpd ) {
								price = price - rulevalue;
							}
							new_product_price = new_product_price - rulevalue;
							break;

						case 'fixed':
						case 'fixed__price':
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
			price = getDiscountObj( totals, rules, current_variation, cv, qty );
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
			discount = getDiscountObj( totalsHolder, rules, current_variation, cv, qty, force );
			value = discount[ 0 ];
			type = discount[ 1 ];
			if ( price === undefined ) {
				price = 0;
			}
			switch ( type ) {
				case 'percentage':
				case 'discount__percentage':
					price = price * ( 1 - ( value / 100 ) );
					break;
				case 'price':
				case 'discount__amount':
					price = price - value;
					break;
				case 'fixed':
				case 'fixed__price':
					price = value;
					break;
			}
		}

		return price;
	}

	function alterProductPrice( product_price, element, cart, epoTotalsContainer ) {
		var mode = epoTotalsContainer.attr( 'data-tm-epo-dpd-original-price-base' );
		var undiscountedProductPrice = epoTotalsContainer.attr( 'data-price' );

		if ( mode === 'undiscounted' && undiscountedProductPrice !== undefined ) {
			return undiscountedProductPrice;
		}

		return product_price;
	}

	function getUndiscountedPrice( price, type, value, _dc ) {
		switch ( type ) {
			case 'percentage':
			case 'discount__percentage':
				price = parseFloat( ( price / ( 1 - ( value / 100 ) ) ) * Math.pow( 10, _dc ) ) * Math.pow( 10, -_dc ) * 1;
				price = tc_round( price, _dc );
				if ( price < 0 ) {
					price = 0;
				}
				break;

			case 'price':
			case 'discount__amount':
				price = price + value;
				price = parseFloat( price * Math.pow( 10, _dc ) ) * Math.pow( 10, -_dc );
				price = tc_round( price, _dc );
				if ( price < 0 ) {
					price = 0;
				}
				break;
		}
		return price;
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

	$( document ).ready( function() {
		TMEPOJS = window.TMEPOJS || null;

		if ( ! TMEPOJS ) {
			return;
		}

		$.epoAPI.addFilter( 'tc_calculate_product_price', calculateProductPrice, 10, 2 );
		$.epoAPI.addFilter( 'tc_apply_dpd', tc_apply_dpd, 10, 4 );
		$.epoAPI.addFilter( 'tc_alter_product_price', alterProductPrice, 10, 4 );

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
				dpd_prefix = totalsHolder.data( 'tm-epo-dpd-prefix' );
				dpd_suffix = totalsHolder.data( 'tm-epo-dpd-suffix' );

				if ( apply_dpd === 1 ) {
					dpd_discount = tm_get_dpd( totalsHolder, epo_object, apply_dpd );
					dpd_string = '';
					if ( dpd_discount[ 0 ] && dpd_discount[ 1 ] && ( dpd_prefix || dpd_suffix ) ) {
						dpd_discount_type = dpd_discount[ 1 ];
						dpd_discount_string = '';
						switch ( dpd_discount_type ) {
							case 'percentage':
							case 'discount__percentage':
								dpd_discount_string = dpd_discount[ 0 ] + '%';
								break;
							case 'price':
							case 'discount__amount':
								dpd_discount_string = tm_set_price( dpd_discount[ 0 ] * qty, totalsHolder, false, false );
								break;
							case 'fixed':
							case 'fixed__price':
								dpd_discount_string = tm_set_price( ( parseFloat( totalsHolder.data( 'price' ) ) - dpd_discount[ 0 ] ) * qty, totalsHolder, false, false );
								break;
						}
						dpd_string = dpd_prefix + ' ' + dpd_discount_string + ' ' + dpd_suffix;
					}
					if ( dpd_string ) {
						dpd_css_selector = totalsHolder.data( 'tm-epo-dpd-label-css-selector' );
						dpd_string_placement = totalsHolder.data( 'tm-epo-dpd-string-placement' );
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

		// Enable original final total display
		$( window ).on( 'tc-epo-after-update', function( e, o ) {
			var totalsHolder;
			var tc_totals_ob;
			var do_oft;
			var rules;
			var $cart;
			var variation_id_selector;
			var qty_element;
			var qty;
			var current_variation;
			var cv;
			var discount;
			var value;
			var type;
			var _dc;
			var original_price;
			var price;
			var dpdEnabled;
			var undiscountedProductPrice;
			var originalOptionsTotal;
			var latePrices;

			if ( o && o.data && o.epo && o.totals_holder ) {
				totalsHolder = o.totals_holder;
				tc_totals_ob = o.epo;

				do_oft = totalsHolder.data( 'tm-epo-dpd-original-final-total' );

				if ( do_oft !== 'yes' ) {
					return;
				}

				rules = totalsHolder.data( 'product-price-rules' );
				$cart = totalsHolder.data( 'tm_for_cart' );
				variation_id_selector = 'input[name^="variation_id"]';
				if ( $cart.find( 'input.variation_id' ).length > 0 ) {
					variation_id_selector = 'input.variation_id';
				}
				qty_element = totalsHolder.data( 'qty_element' );
				qty = parseFloat( qty_element.val() );
				current_variation = $cart.find( variation_id_selector ).val();
				cv = current_variation;

				if ( ! current_variation ) {
					current_variation = 0;
				}
				if ( ! Number.isFinite( qty ) ) {
					if ( totalsHolder.attr( 'data-is-sold-individually' ) || qty_element.length === 0 ) {
						qty = 1;
					}
				}
				dpdEnabled = totalsHolder.data( 'tm-epo-dpd-enable' ) === 'yes';
				discount = getDiscountObj( totalsHolder, rules, current_variation, cv, qty );
				value = discount[ 0 ];
				type = discount[ 1 ];
				_dc = parseInt( TMEPOJS.currency_format_num_decimals, 10 );
				if ( dpdEnabled ) {
					original_price = tc_totals_ob.product_total_price_without_options + tc_totals_ob.options_original_total_price;
				} else {
					original_price = tc_totals_ob.product_total_price_without_options;
				}
				price = original_price;
				original_price = tc_round( original_price, _dc );

				if ( dpdEnabled ) {
					undiscountedProductPrice = getUndiscountedPrice( tc_totals_ob.product_total_price_without_options, type, value, _dc );
					originalOptionsTotal = getUndiscountedPrice( tc_totals_ob.options_original_total_price - tc_totals_ob.late_total_original_price, type, value, _dc );
					latePrices = o.data.add_late_fields_prices( o.data.epo_object, undiscountedProductPrice, originalOptionsTotal, originalOptionsTotal, o.data.bundle_id, totalsHolder, 0 );
					price = undiscountedProductPrice + originalOptionsTotal + latePrices[ 0 ];
				} else {
					// need to suport when Enable discounts on extra options = disable
					if ( o.totals_holder.attr( 'data-tm-epo-dpd-price-override' ) !== '1' ) {
						price = getUndiscountedPrice( price, type, value, _dc );
					}

					price = price + tc_totals_ob.options_original_total_price;
				}

				price = tc_round( price, _dc );
				if ( original_price === price ) {
					return;
				}

				price = price + tc_totals_ob.cart_fee_options_total_price;

				$( '.tm-final-totals' )
					.last()
					.find( '.price.amount.final' )
					.after( '<div class="price amount original"><del>' + o.data.tm_set_price( price ) + '</del></div>' );
			}
		} );
	} );
}( window, document, window.jQuery ) );
