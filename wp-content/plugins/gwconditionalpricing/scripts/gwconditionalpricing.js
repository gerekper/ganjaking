/**
* GW Conditional Pricing Front-end JS
*/

(function($){

	function gpcpDebounce(func, wait, immediate){
		var timeout, args, context, timestamp, result;
		if (null == wait) wait = 100;

		function later() {
			var last = Date.now() - timestamp;

			if (last < wait && last >= 0) {
				timeout = setTimeout(later, wait - last);
			} else {
				timeout = null;
				if (!immediate) {
					result = func.apply(context, args);
					context = args = null;
				}
			}
		}

		var debounced = function(){
			context = this;
			args = arguments;
			timestamp = Date.now();
			var callNow = immediate && !timeout;
			if (!timeout) timeout = setTimeout(later, wait);
			if (callNow) {
				result = func.apply(context, args);
				context = args = null;
			}

			return result;
		}

		debounced.clear = function() {
			if (timeout) {
				clearTimeout(timeout);
				timeout = null;
			}
		}

		debounced.flush = function() {
			if (timeout) {
				result = func.apply(context, args);
				context = args = null;

				clearTimeout(timeout);
				timeout = null;
			}
		}

		return debounced;
	}


	window.GWConditionalPricing = function( formId, pricingLogic, basePrices ) {

		var self = this;

		self._formId       = formId;
		self._pricingLogic = pricingLogic;
		self._basePrices   = basePrices;

		this.pricingIteration = 0;

		this.init = function() {

			var gwcp         = this,
			formElem         = GWConditionalPricing.getFormElement( this._formId ),
			onChangeSelector = [ 'select', 'input:not( .ginput_total + input ):not( .ginput_total )', 'textarea' ].join( ', ' ),
			onClickSelector  = [ 'input[type="checkbox"]', 'input[type="radio"]', 'input[type="number"]' ].join( ', ' ),
			onKeyUpSelector  = [ 'input[type="text"]', 'input[type="hidden"]', 'input[type="number"]', 'textarea' ].join( ', ' );

			formElem
			.on( 'click', onClickSelector, self.debouncedUpdatePricing)
			.on( 'change', onChangeSelector, self.debouncedUpdatePricing)
			.on( 'keyup', onKeyUpSelector, self.debouncedUpdatePricing);

			gform.addFilter( 'gform_is_value_match', 'gwcpIsCustomQtyFieldMatch' );

			// Trigger price update after conditional logic is processed
			$( document ).on( 'gform_post_conditional_logic', function( e, formId ) {
				if ( parseInt( formId ) === self._formId) {
					self.debouncedUpdatePricing();
				}
			});

			// add to form for use with customizations
			formElem.data( 'gwcp', gwcp );

			gwcp.updatePricing();

		};

		GWConditionalPricing.getFormElement = function( formId ) {

			var formElem = $( gform.applyFilters( 'gpcp_form_element', '#gform_wrapper_' + formId ) );

			if ( formElem.length <= 0 ) {
				formElem = $( '#gform_' + formId );
			}

			return formElem;
		};

		self.debouncedUpdatePricing = gpcpDebounce( function() {
			self.updatePricing();
		}, 50);

		this.updatePricing = function( triggerFieldId ) {

			this.ruleCache[formId] = {};

			this.pricingIteration++;

			// if no product ID is passed, loop through all pricing logic
			var updateAllProducts = ! triggerFieldId;

			for ( var productId in this._pricingLogic ) {

				if ( ! this._pricingLogic.hasOwnProperty( productId ) ) {
					continue;
				}

				var pricingLevels = this._pricingLogic[ productId ];

				// This can happen if they delete a product from the form without removing the conditional pricing rules.
				if ( ! pricingLevels || pricingLevels[0] === null ) {
					continue;
				}

				var isProduct = parseInt( productId ) == triggerFieldId,
				isDependency  = this.isDependency( triggerFieldId, pricingLevels );

				if ( ! updateAllProducts && ! isProduct && ! isDependency ) {
					continue;
				}

				var matchFound = false;

				for ( var i = 0; i < pricingLevels.length; i++ ) {

					var pricingLevel = pricingLevels[i],
					isMatch          = this.isMatch( this._formId, pricingLevel.conditionalLogic );

					if ( ! isMatch ) {
						continue;
					}

					matchFound = true;
					GWConditionalPricing.setPrice( productId, pricingLevel.price, this._formId );

					// we only want the first match per product, otherwise, subsequent matches will overwrite the price
					break;
				}

				// if no matching pricing level was found, set back to basePrice
				if ( ! matchFound ) {
					GWConditionalPricing.setPrice( productId, false, this._formId );
				}

			}

			/**
			 * Trigger calculated product recalculation.
			 */
			if ( window.gf_global.gfcalc ) {
				var calcObject = window.gf_global.gfcalc[ formId ];

				if ( calcObject ) {
					calcObject.runCalcs( formId, calcObject.formulaFields );
				}
			}

			/**
			 * Do something after product prices have been updated.
			 *
			 * @since 1.2.13
			 *
			 * @param bool|int triggerFieldId The ID of the field that has triggered the pricing update
			 * @param object GWConditionalPricing Current GWConditionalPricing object.
			 *
			 * @see https://gist.github.com/spivurno/aaf3d6a684b418edeeed
			 */
			gform.doAction( 'gpcp_after_update_pricing', triggerFieldId, this );

			gformCalculateTotalPrice( this._formId );

		};

		this.ruleCache = {};

		/**
		 * Based on Gravity Form's gf_get_field_action but includes caching for performance and changes return value to
		 * boolean.
		 *
		 * @param formId
		 * @param conditionalLogic
		 * @return {boolean}
		 */
		this.isMatch = function( formId, conditionalLogic ) {
			if ( ! conditionalLogic) {
				return true;
			}

			if ( ! (formId in this.ruleCache)) {
				this.ruleCache[formId] = {};
			}

			var matches = 0;
			for ( var i = 0; i < conditionalLogic["rules"].length; i ++ ) {
				var rule = $.extend({}, conditionalLogic["rules"][i]);

				/* Format the rule value to the proper number depending on currency. */
				var fieldNumberFormat = gf_get_field_number_format( rule.fieldId, formId, 'value' );

				if( fieldNumberFormat ) {
					rule.value = gf_format_number( rule.value, fieldNumberFormat );
				}

				var ruleStringified = JSON.stringify( rule );
				var inputSelector = '#input_{0}_{1}'.format( formId, rule['fieldId'] );
				var isHiddenCacheKey = 'is_hidden:' + inputSelector;
				var $field = $( inputSelector );

				if ( !( isHiddenCacheKey in this.ruleCache[formId] ) ) {
					this.ruleCache[formId][isHiddenCacheKey] = gformIsHidden( $field ) && $field.prop( 'type' ) !== 'hidden';
				}

				// Check if the field is hidden and skip processing altogether if it is
				if ( this.ruleCache[formId][isHiddenCacheKey] ) {
					continue;
				}

				if ( ! (ruleStringified in this.ruleCache[formId])) {
					this.ruleCache[formId][ruleStringified] = gf_is_match( formId, rule );
				}

				if (this.ruleCache[formId][ruleStringified]) {
					matches++;
				}

			}

			var action;
			if ( (conditionalLogic["logicType"] == "all" && matches == conditionalLogic["rules"].length) || (conditionalLogic["logicType"] == "any" && matches > 0) ) {
				action = conditionalLogic["actionType"];
			} else {
				action = conditionalLogic["actionType"] == "show" ? "hide" : "show";
			}

			return action == "show";
		};

		GWConditionalPricing.setPrice = function( productId, price, formId ) {

			var gwcp       = GWConditionalPricing.getFormElement( formId ).data( 'gwcp' ),
			currency       = new Currency( gf_global['gf_currency_config'] ),
			fieldId        = parseInt( productId ),
			input          = GWConditionalPricing.getProductInput( productId, formId ),
			isMultiProduct = input.length > 1,
			isReset        = price === false,
			origPrice      = price;

			if ( ! input ) {
				return false;
			}

			var changedInputs = null;

			input.each( function( index ) {

				var input  = $( this ),
				prevValue  = input.val(),
				newValue   = '',
				isSelected = false,
				inputId    = isMultiProduct ? productId + '.' + ( index + 1 ) : productId;

				if ( isReset ) {
					origPrice = currency.toNumber( gwcp.getBasePrice( inputId ) );
				}

				/**
				 * Filter the price about to be set for the given product.
				 *
				 * This filter runs both when the Conditional Pricing is setting the product to a matched pricing level and
				 * when resetting the original price when no pricing level matches.
				 *
				 * @param string|float price The price to be set.
				 * @param object       meta {
				 *     An array of useful meta information about the current price.
				 *
				 *     @type int|string productId The ID of the product for which the price is being set.
				 *     @type bool       isReset   Indicates if the price is being reset to its original value because not pricing rule is matched.
				 *     @type {jQuery}   $input    A jQuery object for the input on which the price is being set.
				 *     @type string     inputId   The ID of the specific input being modified. Only applies to multi-product fields.
				 *     @type object     gwcp      The current GWConditionalPricing object.
				 *     @type {Currency} currency  The current GF Currency object.
				 *     @type int        formId    The current form ID.
				 * }
				 */
				// pass origPrice to prevent multiple manipulations of the "price" in filter functions (js passes by reference)
				price = gform.applyFilters( 'gpcp_price', origPrice, {
					productId: productId,
					isReset:   isReset,
					input:     input,
					inputId:   inputId,
					gwcp:      gwcp,
					currency:  currency,
					formId:    formId
				} );

				// if 'false' is returned, do not set price
				if ( price === false ) {
					return;
				}

				// to be safe, let's always get the price as a number
				// sometimes GF sets it as a currency string or times as a number
				price = currency.toNumber( price );

				if ( input.is( 'option' ) || input.is( ':radio' ) ) {

					// skip any 'choice' that is "empty"
					if ( input.val() == '|' ) {
						return;
					}

					var productName = input.val().split( '|' )[0];

					newValue   = productName + '|' + price;
					isSelected = input.is( ':selected, :checked' );

					input.val( newValue );

				} else {

					newValue   = price;
					isSelected = true;

					input.val( currency.toMoney( price, true ) );

					// single product fields need to be visually updated outside of the hidden input
					var suffix = '_' + gwcp._formId + '_' + fieldId;

					$( 'span#input' + suffix ).text( currency.toMoney( price, true ) );

				}

				// convert previous value to number so we can compare two numbers rather than two currencies
				// UNLESS this is a choice-based value (i.e. "value|price", "First Choice|5").
				if ( prevValue.indexOf( '|' ) == -1 ) {
					prevValue = currency.toNumber( prevValue );
				}

				if ( newValue != prevValue && isSelected ) {
					//input.change();
					if ( changedInputs == null ) {
						changedInputs = input;
					} else {
						changedInputs.add( input );
					}
				}

			} );

			$( changedInputs ).change();

		};

		/**
		* Returns the lowest level HTML element that represents the product. In the case of a multi-product parent,
		* an array of lowest level HTML elements are returned for all "child" products.
		*
		*   select  => option
		*   radio   => input
		*   single  => input
		*
		*/
		GWConditionalPricing.getProductInput = function( productId, formId ) {

			var fieldId = parseInt( productId ),
			suffix      = '_' + formId + '_' + fieldId,
			productId   = productId.toString();

			// check for single product first
			var input = $( '#ginput_base_price' + suffix );

			// if no single product, check if this is a multi-product field (select, radio)
			if ( input.length <= 0 ) {

				var isMultiProductParent = fieldId == productId; // (ie 5 == 5, rather than 5.1 != 5 )
				var inputId              = productId.split( '.' )[1] - 1;

				input = $( '#input' + suffix );

				// If the input is a <ul> (<2.5) or <div> (2.5+), the product is a radio button.
				if ( input.is( 'ul' ) || input.is( 'div' ) ) {
					if ( isMultiProductParent ) {
						input = input.find( 'input[type="radio"]' );
					} else {

						// at some point, GF added the form ID to the choice ID for radio buttons (maybe it was always there... but I don't think so)
						input = input.find( '#choice_' + formId + '_' + fieldId + '_' + inputId );

						// if form ID version is not found, check for choice without form ID
						if ( input.length <= 0 ) {
							input = $( '#input' + suffix + ' #choice_' + fieldId + '_' + inputId );
						}
					}
				}
				// otherwise, assume the product is a select
				else {

					// get all select options (except the placeholder input)
					input = input.find( 'option:not( .gf_placeholder )' );

					if ( ! isMultiProductParent ) {
						input = input.eq( inputId );
					}

				}

			}

			return input.length <= 0 ? false : input;
		};

		this.getFieldIdFromHtmlId = function( id ) {

			if ( ! id ) {
				return false;
			}

			var idBits = id.split( '_' ),
			fieldId    = false;

			// ginput_quantity_1637_25
			if ( idBits[1] == 'quantity' ) {
				//fieldId = 'quantity_' + idBits[idBits.length - 1];
				fieldId = idBits[idBits.length - 1];
			} else if ( idBits[0] == 'choice' || idBits.length == 4 ) {
				fieldId = idBits[ idBits.length - 2 ];
			} else {
				fieldId = idBits[ idBits.length - 1 ];
			}

			return fieldId;
		};

		this.getBasePrice = function( productId ) {
			return this._basePrices[productId];
		};

		/**
		 * Deprecated since 1.2.17. Gravity Forms provides a function for this now: gformGetProductQuantity.
		 */
		GWConditionalPricing.getProductQuantity = function( fieldId, formId ) {

			var quantity,
			quantityInput = $( '#ginput_quantity_' + formId + '_' + fieldId );

			if ( quantityInput.length > 0 ) {

				quantity = ! gformIsNumber( quantityInput.val() ) ? 0 : quantityInput.val();

			} else {

				quantityElement = $( '.gfield_quantity_' + formId + '_' + fieldId );

				quantity = 1;
				if ( quantityElement.find( "input" ).length > 0 ) {
					quantity = quantityElement.find( "input" ).val();
				} else if (quantityElement.find( "select" ).length > 0) {
					quantity = quantityElement.find( "select" ).val();
				}

				if ( ! gformIsNumber( quantity ) ) {
					quantity = 0;
				}

			}

			quantity = parseFloat( quantity );

			// setting global variable if quantity is more than 0 (a product was selected). Will be used when calculating total
			if ( quantity > 0 ) {
				_anyProductSelected = true;
			}

			return quantity;
		};

		this.isDependency = function( fieldId, pricingLevels ) {

			for ( var i = 0; i < pricingLevels.length; i++ ) {
				for ( var j = 0; j < pricingLevels[ i ].conditionalLogic.rules.length; j++ ) {

					var ruleFieldId = pricingLevels[ i ].conditionalLogic.rules[ j ].fieldId;

					// Note: this will register all inputs changes of a multi-input field as dependencies, even if the ruleFieldId is input specific.
					// We might want to make this more specific in the future.
					if ( parseInt( ruleFieldId ) == parseInt( fieldId ) ) {
						return true;
					} else if ( ruleFieldId.indexOf( 'quantity' ) != -1 && ruleFieldId.split( '_' )[1] == fieldId ) {
						return true;
					}

				}
			}

			return false;
		};

		GWConditionalPricing.hasPlaceholder = function( productId, formId ) {

		};

		return this.init();
	}

})( jQuery );

function gwcpIsCustomQtyFieldMatch( isMatch, formId, rule ) {

	// check for actual field IDs cheaply
	if ( ! isNaN( parseInt( rule.fieldId ) ) ) {
		return isMatch;
	}

	// check of our quantity_X tag
	var regex = /(quantity)_([0-9]+)/;
	var match = regex.exec( rule.fieldId );
	if ( ! match || match[1] != 'quantity' ) {
		return isMatch;
	}

	var quantity = gformGetProductQuantity( formId, match[2] );

	return gf_matches_operation( quantity + '', rule.value, rule.operator );
}
