( function( $ ) {

	/**
	 * GetDiscount is not called using the form ID which means it's not available to the gform_coupons_discount_amount
	 * filter.
	 *
	 * This is a workaround to crawl up the stack trace when GetDiscount is called to get the form ID so we can use it
	 * when filtering gform_coupons_discount_amount to exclude shipping and discounts from the coupon amount.
	 *
	 * Ideally, Gravity Forms passes the form ID to GetDiscount and subsequently the gform_coupons_discount_amount JS
	 * filter.
	 */
	var GetDiscountOriginal = window.GetDiscount;
	var getDiscountCurrentFormId;

	window.GetDiscount = function () {
		var caller = arguments.callee.caller;

		while (caller.name !== 'PopulateDiscountInfo') {
			if (typeof caller.caller !== 'function') {
				break;
			}

			caller = caller.caller;
		}

		if (caller.name === 'PopulateDiscountInfo') {
			getDiscountCurrentFormId = caller.arguments[1];
		}

		var result = GetDiscountOriginal.apply(this, arguments);

		getDiscountCurrentFormId = undefined;

		return result;
	}

	/*
	 * Memoization based on fast-memoize: https://github.com/caiogondim/fast-memoize.js/blob/master/src/index.js
	 */
	function memoize (fn, options) {
		var cache = options && options.cache
			? options.cache
			: cacheDefault

		var serializer = options && options.serializer
			? options.serializer
			: serializerDefault

		var strategy = options && options.strategy
			? options.strategy
			: strategyDefault

		return strategy(fn, {
			cache: cache,
			serializer: serializer
		})
	}

	//
	// Strategy
	//

	function isPrimitive (value) {
		return value == null || typeof value === 'number' || typeof value === 'boolean' // || typeof value === "string" 'unsafe' primitive for our needs
	}

	function monadic (fn, cache, serializer, arg) {
		var cacheKey = isPrimitive(arg) ? arg : serializer(arg)

		var computedValue = cache.get(cacheKey)
		if (typeof computedValue === 'undefined') {
			computedValue = fn.call(this, arg)
			cache.set(cacheKey, computedValue)
		}

		return computedValue
	}

	function variadic (fn, cache, serializer) {
		var args = Array.prototype.slice.call(arguments, 3)
		var cacheKey = serializer(args)

		var computedValue = cache.get(cacheKey)
		if (typeof computedValue === 'undefined') {
			computedValue = fn.apply(this, args)
			cache.set(cacheKey, computedValue)
		}

		return computedValue
	}

	function assemble (fn, context, strategy, cache, serialize) {
		return strategy.bind(
			context,
			fn,
			cache,
			serialize
		)
	}

	function strategyDefault (fn, options) {
		var strategy = fn.length === 1 ? monadic : variadic

		return assemble(
			fn,
			this,
			strategy,
			options.cache.create(),
			options.serializer
		)
	}

	function strategyVariadic (fn, options) {
		var strategy = variadic

		return assemble(
			fn,
			this,
			strategy,
			options.cache.create(),
			options.serializer
		)
	}

	function strategyMonadic (fn, options) {
		var strategy = monadic

		return assemble(
			fn,
			this,
			strategy,
			options.cache.create(),
			options.serializer
		)
	}

	//
	// Serializer
	//

	function serializerDefault () {
		return JSON.stringify(arguments)
	}

	//
	// Memoize Cache
	//

	function ObjectWithoutPrototypeCache () {
		this.cache = Object.create(null)
	}

	ObjectWithoutPrototypeCache.prototype.has = function (key) {
		return (key in this.cache)
	}

	ObjectWithoutPrototypeCache.prototype.get = function (key) {
		return this.cache[key]
	}

	ObjectWithoutPrototypeCache.prototype.set = function (key, value) {
		this.cache[key] = value
	}

	var cacheDefault = {
		create: function create () {
			return new ObjectWithoutPrototypeCache()
		}
	}
	/**
	 * End fast-memoize functions/methods
	 */

	/**
	 * Replace {coupons}, {discounts}, and {subtotal} merge tags in calculation formulas.
	 */
	gform.addFilter(
		'gform_calculation_formula',
		function( formula, formulaField, formId, calcObj ) {

			if ( ! hasOurMergeTags( formula ) ) {
				return formula;
			}

			var $field          = $( '#field_{0}_{1}'.format( formId, formulaField.field_id ) ),
			isCalculatedProduct = $field.hasClass( 'gfield_price' );

			// Don't bother trying to calculate Calculated Product fields until GF has registered them.
			if ( isCalculatedProduct && ( ! window[ '_gformPriceFields' ] || ! window[ '_gformPriceFields' ].length ) ) {
				formula = formula.replace( /{coupons}/gi, 0 );
				formula = formula.replace( /{discounts}/gi, 0 );
				formula = formula.replace( /{subtotal}/gi, 0 );
				return formula;
			}

			var couponCode = gformIsHidden( $( '#gf_coupon_code_' + formId ) ) ? '' : $( '#gf_coupon_codes_' + formId ).val(),
			hasCoupon      = couponCode != '' || $( '#gf_coupons_' + formId ).val() != '',
			total          = $( '#gf_total_no_discount_' + formId ).val(),
			couponsTotal   = 0,
			discountsTotal = getDiscountsTotal( formId, getSubtotal( formId ) ) * -1;

			// Catch cases where there are multiple forms on a page and current form does not have a Coupon field.
			if ( typeof total !== 'undefined' && hasCoupon && window['PopulateDiscountInfo'] ) {
				couponsTotal = PopulateDiscountInfo( total, formId );
			}

			formula = formula.replace( /{coupons}/gi,   parseFloat( couponsTotal ) );
			formula = formula.replace( /{discounts}/gi, parseFloat( discountsTotal + couponsTotal ) );
			formula = formula.replace( /{subtotal}/gi,  parseFloat( getSubtotal( formId, isCalculatedProduct ? [ formulaField.field_id ] : null ) ) );

			return formula;
		}
	);

	/**
	 * Exclude Shipping & Discounts from Coupons by default.
	 *
	 * @todo Gravity Forms added `formId` as a 6th parameter so we can get rid of our `getDiscountCurrentFormId` hack
	 *       whenever we're ready. See: https://github.com/gravityforms/gravityformscoupons/pull/29
	 */
	gform.addFilter(
		'gform_coupons_discount_amount',
		function( discount, couponType, couponAmount, price, totalDiscount ) {
			/**
			 * This variable is set above in our override of GetDiscount. The Form ID isn't passed to this filter
			 * by default and this is our workaround to get it.
			 */
			if (!getDiscountCurrentFormId) {
				return discount;
			}

			var formId    = getDiscountCurrentFormId,
			shippingTotal = gformGetShippingPrice( formId ),
			total         = price + totalDiscount - shippingTotal,
			//discountTotal = getDiscountsTotal( formId, total ) * -1, // make it positive
			price = Math.max( 0, price - shippingTotal /*- discountTotal*/ );

			if ( couponType == 'percentage' ) {
				discount = price * Number( ( couponAmount / 100 ) );
			} else if ( couponType == 'flat' ) {
				discount = Number( couponAmount );
				if ( discount > price ) {
					discount = price;
				}
			}

			return discount;
		}
	);

	/**
	 * Process Subtotal Fields
	 */
	gform.addFilter(
		'gform_product_total',
		function( total, formId ) {

			var $subtotalFields = $( '#gform_wrapper_' + formId + ' .ginput_subtotal_input' );

			$subtotalFields.each(
				function() {

					var origValue = $( this ).val();

					if ( gformIsHidden( $( this ) ) ) {
						if ( origValue !== 0 && origValue !== '0' ) {
							$( this ).val( 0 ).change();
						}
						return true;
					}

					var subtotal = getFieldTotal(
						{
							intent:          'Calculating subtotal for Subtotal field {0}'.format( $( this ). attr( 'id' ) ),
							total:           total,
							formId:          formId,
							amount:          null,
							amountType:      null,
							productsType:    $( this ).data( 'productstype' ),
							products:        $( this ).data( 'products' ),
							includeShipping: false
						}
					);

					if ( String( origValue ) != String( subtotal ) ) {
						$( this ).val( subtotal ).change();
					}

				}
			);

			return total;
		},
		48
	);

	/**
	 * Process Discount Fields
	 */
	gform.addFilter(
		'gform_product_total',
		function( total, formId ) {

			var totalDiscount = getDiscountsTotal( formId, total );

			total += totalDiscount;

			return Math.max( 0, total );
		},
		49
	);

	/**
	 * PayPal Checkout flow requires that the discount amount be specifically sent otherwise it will error during
	 * validation.
	 */
	if (window.GPECF.ppcpRequiresDiscountFilter === 'true') {
		gform.addFilter(
			'gform_ppcp_discount_total',
			function( discountTotal, formId ) {
				return discountTotal + (getDiscountsTotal( formId, getSubtotal( formId ) ) * -1);
			}
		);
	}

	/**
	 * Process Tax Fields
	 *
	 * Tax fields are not included in the Total by default. Any time the total changes,
	 * reapply our Tax fields to the total.
	 */
	gform.addFilter(
		'gform_product_total',
		function( total, formId ) {

			var $taxFields = $( '#gform_wrapper_' + formId + ' .ginput_tax_input' ),
			totalTax       = 0;

			$taxFields.each(
				function() {

					var origValue = $( this ).val();

					if ( gformIsHidden( $( this ) ) ) {
						if ( origValue !== 0 && origValue !== '0' ) {
							$( this ).val( 0 ).change();
						}
						return true;
					}

					var tax;

					// Don't calculate tax when the total is 0 and we are excluding products. Avoids issues with negative numbers...
					// In the future, we might consider handling this inside getFieldTotal().
					if ( total == 0 && $( this ).data( 'productstype' ) === 'exclude' ) {
						tax = 0;
					} else {
						tax = getFieldTotal(
							{
								intent:           'Calculating tax for Tax field {0}'.format( $( this ). attr( 'id' ) ),
								total:            total,
								formId:           formId,
								amount:           $( this ).data( 'amount' ),
								amountType:       $( this ).data( 'amounttype' ),
								products:         $( this ).data( 'products' ),
								productsType:     $( this ).data( 'productstype' ),
								includeDiscounts: true
							}
						);
					}

					if ( String( origValue ) !== String( tax ) ) {
						$( this ).val( tax ).change();
					}

					// Round here (and not just at the end) so we can guarantee that the tax amount displayed to the user is the
					// same tax amount used to calculate the total.
					totalTax += round( tax, 2 );

				}
			);

			total += totalTax;

			return round( total, 2 );
		},
		51 /* coupons applied at 50 */
	);

	/**
	 * Re-process calculations anytime a coupon code is added/removed.
	 */
	gform.addFilter(
		'gform_product_total',
		function( total, formId ) {

			var $coupons = $( '#gf_coupon_codes_{0}'.format( formId ) );

			if ( $coupons.data( 'gpecfChanged' ) ) {
				$coupons.data( 'gpecfChanged', false );
				$( document ).trigger( 'gform_post_conditional_logic' );
			}

			return total;
		}
	);

	/**
	 * Use this in the future to better optimize how calculations are reprocessed.
	 */
	// gform.addAction( 'gform_post_calculation_events', function() {
	//
	// }, 10, 4 );

	/**
	 * Bind our custom listeners after the form is rendered.
	 */
	$( document ).on(
		'gform_post_render',
		function( event, formId ) {

			reprioritizeWCGFProductAddon();

			// Queue calculations to be reprocessed when a coupon is added/removed.
			$( '#gf_coupon_codes_{0}'.format( formId ) ).change(
				function() {
					$( this ).data( 'gpecfChanged', true );
				}
			);

			// re-process calculations any time a price changes
			$( document ).on(
				'gform_price_change',
				function( event, formAndProductId, $elem ) {
					runCalculations( formAndProductId.formId );
				}
			);

			// GPCP does not tigger 'gform_price_change' event when it updates the price. Use it's own action instead.
			// Slightly concerned that this may caused performances issues with conditional logic; TBD.
			gform.addAction(
				'gpcp_after_update_pricing',
				function( triggerFieldId, gpcp ) {
					runCalculations( gpcp._formId );
				}
			);

			var inputChangeTimeout;

			// Calculated Number fields with only GPECF merge tags (e.g. {subtotal}) will not get recalculated when a
			// Calculated Product field's value changes. Listen for these changes and force calculations to run again.
			gform.addAction(
				'gform_input_change',
				function( elem, formId, fieldId ) {
					// GF ~2.4.15 and greater pass the elem that triggered the event rather than the window.
					if ( window === elem ) {
						return;
					}

					var $elem     = $( elem );
					var isProduct = $elem.attr( 'id' ) && $elem.attr( 'id' ).indexOf( 'ginput_base_price' ) === 0,
					isCalculation = $elem.parents( '.gfield' ).hasClass( 'gfield_calculation' );

					/**
					 * Prevent recursion.
					 */
					if ($( elem ).data('gpecfIsCalculating')) {
						return;
					}

					if ( isProduct && isCalculation ) {
						$( elem ).data('gpecfIsCalculating', true);

						/**
						 * gpecfIsCalculating along with the setTimeout is here to prevent an N+1-esq issue where every
						 * calculated product results in runCalculations firing
						 */
						if (inputChangeTimeout) {
							clearTimeout(inputChangeTimeout);
							inputChangeTimeout = undefined;
						}

						inputChangeTimeout = setTimeout(function() {
							runCalculations( formId );

							$( elem ).data('gpecfIsCalculating', false);
						}, 5);
					}
				}
			);

			// re-process calculations once GF has registered the pricing fields
			var priceFieldsRegisteredInterval = setInterval(
				function() {
					if ( window[ '_gformPriceFields' ] && window._gformPriceFields[ formId ] ) {
						//$( document ).trigger( 'gform_post_conditional_logic' );
						runCalculations( formId );
						clearInterval( priceFieldsRegisteredInterval );
					}
				}
			);

			// Some browsers (like FireFox) save the values that were previously entered but do not trigger a change event.
			// Let's force a change event on form render so displayed prices always match input value.
			$( '#gform_wrapper_' + formId ).find( '.ginput_subtotal_input, .ginput_discount_input, .ginput_tax_input' ).change();

		}
	);

	function getFieldTotal( args ) {

		args = parseArgs(
			args,
			{
				intent:           '',
				total:            0,
				formId:           null,
				amount:           null,
				amountType:       null,
				products:         null,
				productsType:     null,
				includeShipping:  true,
				includeDiscounts: false,
				calculateByProduct: false
			}
		);

		var fieldTotal     = 0,
			productsAmount = $.isArray( args.products ) && args.products.length > 0 ? getProductsTotal( args.formId, args.products, args.includeDiscounts ) : 0;

		if ( ! args.amount ) {
			args.amount = 0;
		}

		if ( ! args.includeShipping ) {
			args.total -= getShippingTotal( args.formId );
		}

		switch ( args.productsType ) {
			case 'include':
				args.total = productsAmount;
				break;
			case 'exclude':
				args.total -= productsAmount;
				break;
		}

		switch ( args.amountType ) {
			// used by subtotal field
			case null:
				fieldTotal = args.total;
				break;
			case 'percent':
				fieldTotal = args.total * ( args.amount / 100 );
				break;
			default:
				var amount = args.amount;
				/**
				 * Calculate the amount by the percentage of the total.
				 */
				if ( args.calculateByProduct ) {
					var orderTotal      = getSubtotal( args.formId ),
						totalPercentage = ( args.total * 100 ) / orderTotal;
					amount             *= ( totalPercentage / 100 );
				}
				fieldTotal = amount;
		}

		fieldTotal = Math.max( 0, parseFloat( fieldTotal ) );

		return fieldTotal;
	}

	var getProductsTotal = function(formId, productIds, includeDiscounts) {
		if ( ! window['_gformPriceFields'] || ! window['_gformPriceFields'][ formId ] ) {
			return 0;
		}
		return getProductsTotalMemoized(parseInt(formId), productIds, includeDiscounts, jQuery('form#gform_' + formId).serializeArray())
	}

	var getProductsTotalMemoized = memoize(function( formId, productIds, includeDiscounts, fieldValues ) {

		if ( typeof productIds == 'undefined' && window[ '_gformPriceFields' ] ) {
			productIds = _gformPriceFields[ formId ];
		} else if ( ! $.isArray( productIds ) ) {
			productIds = [ productIds ];
		}

		if ( ! productIds ) {
			return 0;
		}

		var total = 0;

		for ( var i = 0; i < productIds.length; i++ ) {
			var productTotal = gformCalculateProductPrice( formId, productIds[i] );
			total           += productTotal;
			if ( includeDiscounts ) {
				var discountsTotal = Math.abs( getDiscountsTotal( formId, productTotal, productIds[i] ) ),
					// This *might* not work with flat coupons...
					couponsTotal = jQuery( '#gf_coupons_' + formId ).val() ? getCouponsTotal( formId, productTotal ) : 0;
				total           -= ( discountsTotal + couponsTotal );
			}
		}

		return total;
	});

	function getSubtotal( formId, excludeProductIds ) {

		var rawSubtotal          = getProductsTotal( formId ),
			excludeProductsTotal = typeof excludeProductIds != 'undefined' && excludeProductIds ? getProductsTotal( formId, excludeProductIds ) : 0;

		return rawSubtotal - excludeProductsTotal;
	}

	function getShippingTotal( formId ) {
		return gformGetShippingPrice( formId );
	}

	/**
	 * Get the discount for all products or for a specific product.
	 *
	 * @param formId
	 * @param total
	 * @param productId
	 * @returns {number}
	 */
	function getDiscountsTotal( formId, total, productId ) {

		var $discountFields = $( '#gform_wrapper_' + formId + ' .ginput_discount_input' ),
			totalDiscount   = 0;

		$discountFields.each(
			function() {

				var origValue = $( this ).val(),
				productIds    = $( this ).data( 'products' ),
				productsType  = $( this ).data( 'productstype' );

				if ( gformIsHidden( $( this ) ) ) {
					if ( origValue !== 0 && origValue !== '0' ) {
						$( this ).val( 0 ).change();
					}
					return true;
				}

				// If productId is passed, only calculate total discount for specified product and only when that product
				// has a total greater than 0 (discounts should never deduct value below zero).
				if ( productId && productIds ) {
					if ( productsType == 'include' && ( $.inArray( productId, productIds ) === -1 || parseInt( total ) === 0 ) ) {
						return true;
					} else if ( productsType == 'exclude' && ( $.inArray( productId, productIds ) !== -1 || parseInt( total ) === 0 ) ) {
						return true;
					}
				}

				var calculateByProduct = productId && productId > 0,
				discount               = getFieldTotal(
					{
						intent:          'Calculating discount for Discount field {0}'.format( $( this ). attr( 'id' ) ),
						total:           total,
						formId:          formId,
						amount:          $( this ).data( 'amount' ),
						amountType:      $( this ).data( 'amounttype' ),
						products:        productIds,
						productsType:    productsType,
						includeShipping: false,
						calculateByProduct: calculateByProduct
					}
				);

				// Product-specific discounts should never be greater than the product total. Only necessary for flat discounts.
				if ( productsType === 'include' && productIds.length > 0 ) {
					discount = Math.min( getProductsTotal( formId, productIds, false ), discount );
				}

				discount *= -1;

				/**
				 * Modify the calculated discount for a Discount field.
				 *
				 * @since 1.0.23
				 *
				 * @param float  discount The total for the current Discount field.
				 * @param object $field   The jQuery object for the current Discount field.
				 */
				discount = gform.applyFilters( 'gpecf_discount_total', discount, $( this ) );

				if ( ! calculateByProduct && String( origValue ) !== String( discount ) ) {
					$( this ).val( discount ).change();
				}

				totalDiscount += discount;

			}
		);

		return totalDiscount;
	}

	function getCouponsTotal( formId, total ) {

		var coupons       = window[ 'gf_coupons' + formId ],
			code,
			coupon,
			couponDiscount,
			totalDiscount = 0;

		for ( code in coupons ) {
			if ( coupons.hasOwnProperty( code ) ) {
				coupon         = coupons[ code ];
				couponDiscount = GetDiscount( coupon['type'], coupon['amount'], total, totalDiscount );
				totalDiscount += couponDiscount;
			}
		}

		return totalDiscount;
	}

	var runCalculations = function( formId ) {

		var _GFCalc = rgars( window, 'gf_global/gfcalc/{0}'.format( formId ) );
		if ( ! _GFCalc ) {
			return;
		}

		var formulaFields = [];

		for ( var i = 0; i < _GFCalc.formulaFields.length; i++ ) {
			var field = _GFCalc.formulaFields[i];
			if ( hasOurMergeTags( field.formula ) ) {
				formulaFields.push( field );
			}
		}

		if ( formulaFields.length ) {
			_GFCalc.runCalcs( formId, formulaFields );
		}

	};

	function hasOurMergeTags( formula ) {

		var hasCouponsMergeTag   = formula.search( '{coupons}' ) != -1,
			hasDiscountsMergeTag = formula.search( '{discounts}' ) != -1,
			hasSubtotalMergeTag  = formula.search( '{subtotal}' ) != -1;

		return hasCouponsMergeTag || hasDiscountsMergeTag || hasSubtotalMergeTag;
	}

	function parseArgs( args, defaults ) {

		for ( key in defaults ) {
			if ( defaults.hasOwnProperty( key ) && typeof args[ key ] == 'undefined' ) {
				args[ key ] = defaults[ key ];
			}
		}

		return args;
	}

	/**
	 * @link http://www.jacklmoore.com/notes/rounding-in-javascript/
	 * @param value
	 * @param decimals
	 * @returns {number}
	 */
	function round( value, decimals ) {
		return Number( Math.round( value + 'e' + decimals ) + 'e-' + decimals );
	}

	function reprioritizeWCGFProductAddon() {
		if ( window.gform ) {
			var filters = window.gform.hooks.filter.gform_product_total;
			for ( var filter in filters ) {
				if ( filters.hasOwnProperty( filter ) ) {
					if ( filters[ filter ].callable.toString().indexOf( 'update_dynamic_price_ajax' ) !== -1 ) {
						// Set priority after everything else we do.
						filters[ filter ].priority = 52;
					}
				}
			}
		}
	}

} )( jQuery );
