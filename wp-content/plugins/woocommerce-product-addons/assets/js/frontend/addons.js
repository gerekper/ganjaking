/* eslint camelcase: [2, {properties: "never"}] */
/* global woocommerce_addons_params, jQuery, accounting */
( function( $, window ) {

	// This script is not yet ready to be publicly stored in the window.
	WC_PAO = window.WC_PAO || {};

	WC_PAO.initialized_forms = [];

	WC_PAO.Helper = {

		/**
		 * Escapes HTML.
		 *
		 * @param html
		 * @returns {*}
		 */
		escapeHtml: function( html ) {
			return document
				.createElement( 'div' )
				.appendChild(document.createTextNode( html ) ).parentNode
				.innerHTML;
		},

		/**
		 * Determines if a subscription is selected in a grouped product.
		 *
		 * @returns {boolean}
		 */
		isGroupedSubsSelected: function () {
			var group = $( '.product-type-grouped' ),
				subs  = false;

			if ( group.length ) {
				group.find( '.group_table tr.product' ).each( function () {
					if ( 0 < $(this).find( '.input-text.qty' ).val() ) {
						if (
							$(this).find( '.entry-summary .subscription-details' )
								.length
						) {
							subs = true;
							return false;
						}
					}
				});
			}

			return subs;
		},

		/**
		 * Determines if a product is a mixed or grouped product type.
		 *
		 * @returns {boolean}
		 */
		isGroupedMixedProductType: function() {
			var group  = $( '.product-type-grouped' ),
				subs   = 0,
				simple = 0;

			if ( group.length ) {
				group.find( '.group_table tr.product' ).each( function () {
					if ( 0 < $(this).find( '.input-text.qty' ).val() ) {
						// For now only checking between simple and subs.
						if (
							$(this).find( '.entry-summary .subscription-details' )
								.length
						) {
							subs++;
						} else {
							simple++;
						}
					}
				});

				if ( 0 < subs && 0 < simple ) {
					return true;
				}
			}

			return false;
		},

		/**
		 * Delays the execution of the callback function by ms.
		 *
		 * @param callback
		 * @param ms
		 */
		delay: function( callback, ms ) {
			var timer = 0;

			clearTimeout( timer );
			timer = setTimeout( callback, ms );
		},
	};

	WC_PAO.Form = ( function () {

		/**
		 * Addons Form Controller.
		 *
		 * @param object $form
		 */
		function Form( $form ) {
			// Make sure is called as a constructor.
			if ( ! ( this instanceof Form ) ) {
				return new Form( $form );
			}

			if ( ! $form.length ) {
				return false;
			}

			// Holds the jQuery instance.
			this.$el     = $form;
			this.$addons = this.$el.find( '.wc-pao-addon-field' );

			if ( ! this.$addons.length ) {
				this.$addons = false;
				return false;
			}

			this.is_rtl                    = 'rtl' === document.documentElement.dir;
			this.validation                = new Validation( this );
			this.totals                    = new Totals( this );
			this.show_incomplete_subtotals = this.totals.showIncompleteSubtotals();

			this.setupEvents();

			this.validation.validate();
			this.updateTotals();

			$( '.wc-pao-addon-image-swatch' ).tipTip({ delay: 200 } );

			WC_PAO.initialized_forms.push( this );
		}

		/**
		 * Sets up event listeners.
		 */
		Form.prototype.setupEvents = function() {

			var self = this;

			// Validate addons on form submit.
			self.$el.find( 'button[type="submit"]' ).on( 'click', function () {

				if ( self.validation.validate( true ) ) {
					return true;
				}

				// Scroll viewport to the first invalid configured addon, if it not currently in viewport.
				var $messages = self.$el.find( '.wc-pao-validation-notice' );

				if( $messages.length > 0 ) {
					var $first_invalid_addon = self.$el.find( $messages[0].closest( '.wc-pao-addon-container' ) );

					if ( $first_invalid_addon.length > 0 && ! self.is_in_viewport( $first_invalid_addon ) ) {
						$first_invalid_addon[0].scrollIntoView();
					}
				}

				return false;
			});

			/**
			 * Addons value changed.
			 */
			self.$el.on(
				'blur change',
				'.wc-pao-addon input, .wc-pao-addon textarea, .wc-pao-addon select, .wc-pao-addon-custom-text',
				function () {

					self.validation.validateAddon( $(this), true );
					self.updateTotals();
				}
			);

			self.$el.on(
				'keyup',
				'.wc-pao-addon input, .wc-pao-addon textarea, .wc-pao-addon-custom-text',
				function () {
					var $addon = $(this);

					WC_PAO.Helper.delay( function() {
						self.validation.validateAddon( $addon, true );
						self.updateTotals();
					}, 300 );
				}
			);

			// Product quantity changed.
			self.$el.on(
				'change',
				'input.qty',
				function () {
					self.updateTotals();
				}
			);

			// Special handling for image swatches.
			// When a user clicks on an image swatch, the selection is transferred to a hidden select element.
			var touchTime;

			self.$el.on(
				'touchstart',
				'.wc-pao-addon-image-swatch',
				function (e) {
					touchTime = new Date();
				}
			);

			self.$el.on(
				'click touchend',
				'.wc-pao-addon-image-swatch',
				function (e) {
					e.preventDefault();

					if ( 'touchend' === e.type && touchTime ) {
						var diff = new Date() - touchTime;

						if ( diff > 100 ) {
							// This is a scroll event and not a tap, so skip.
							return;
						}
					}

					var selected_value = $(this).data( 'value' ),
						$parent        = $(this).parents( '.wc-pao-addon-wrap' ),
						label          = $.parseHTML( $(this).data( 'price' ) ),
						$selected      = $parent.find( '.wc-pao-addon-image-swatch-selected-swatch' );

					// Clear selected swatch.
					$selected.html( '' );

					// Clear all selected.
					$parent
						.find( '.wc-pao-addon-image-swatch' )
						.removeClass( 'selected' );

					// Select this swatch.
					$(this).addClass( 'selected' );

					// Set the value in hidden select field.
					$parent
						.find( '.wc-pao-addon-image-swatch-select' )
						.val( selected_value );

					// Display selected label below swatches.
					$selected.html( label );

					self.validation.validateAddon( $parent.find( 'select.wc-pao-addon-field' ), true );
					self.updateTotals();
				}
			);

			/**
			 * Variable Products.
			 */

			// Reset addon totals when the variation selection is cleared. The form is not valid until a variation is selected.
			self.$el.on( 'click', '.reset_variations', function () {
				self.totals.reset();
			});

			// When the variation form initially loads.
			self.$el.on( 'wc_variation_form', function () {
				self.validation.validate();
				self.updateTotals();
			});

			// When a new variation is selected, validate the form and update the addons totals.
			self.$el.on( 'found_variation', function ( event, variation ) {
				self.totals.updateVariation( variation );
				self.validation.validate();
				self.updateTotals();
			});

			// When a variation selection is cleared by selecting "Choose an option...", reset totals as the form becomes invalid.
			self.$el.on( 'hide_variation', function ( event ) {
				self.updateTotals();
			});

			self.$el.on( 'woocommerce-product-addons-update', function () {
				self.validation.validate();
				self.updateTotals();
			});

			/**
			 * Integrations.
			 */

			// Compatibility with Smart Coupons self declared gift amount purchase.
			//
			// CAUTION: This code is unstable.
			$( '#credit_called' ).on( 'keyup', function () {
				self.validation.validate();
				self.updateTotals();
			});
		}

		/**
		 * Updates addons totals if the form is valid or resets them otherwise.
		 */
		Form.prototype.updateTotals = function() {

			this.totals.calculate()

			if ( this.show_incomplete_subtotals || this.isValid() ) {
				this.totals.render();
			} else {
				this.totals.reset()
			}
		}

		/**
		 * Determines if the form is valid.
		 * @returns boolean
		 */
		Form.prototype.isValid = function() {

			var valid               = true,
				$add_to_cart_button = this.$el.find( 'button.single_add_to_cart_button' );

			if ( $add_to_cart_button.is( '.disabled' ) ) {
				valid = false;
				return valid;
			}

			$.each( this.validation.getValidationState(), function() {

				if ( ! this.validity ) {
					valid = false;
					return false;
				}
			});

			return valid;
		}

		/**
		 * Element-in-viewport check with partial element detection & direction support.
		 * Credit: Sam Sehnert - https://github.com/customd/jquery-visible
		 */
		Form.prototype.is_in_viewport = function( element, partial, hidden, direction ) {

			var $w = $( window );

			if ( element.length < 1 ) {
				return;
			}

			var $t         = element.length > 1 ? element.eq(0) : element,
				t          = $t.get(0),
				vpWidth    = $w.width(),
				vpHeight   = $w.height(),
				clientSize = hidden === true ? t.offsetWidth * t.offsetHeight : true;

			direction = (direction) ? direction : 'vertical';

			if ( typeof t.getBoundingClientRect === 'function' ) {

				// Use this native browser method, if available.
				var rec      = t.getBoundingClientRect(),
					tViz     = rec.top    >= 0 && rec.top    <  vpHeight,
					bViz     = rec.bottom >  0 && rec.bottom <= vpHeight,
					mViz     = rec.top    <  0 && rec.bottom >  vpHeight,
					lViz     = rec.left   >= 0 && rec.left   <  vpWidth,
					rViz     = rec.right  >  0 && rec.right  <= vpWidth,
					vVisible = partial ? tViz || bViz || mViz : tViz && bViz,
					hVisible = partial ? lViz || rViz : lViz && rViz;

				if ( direction === 'both' ) {
					return clientSize && vVisible && hVisible;
				} else if ( direction === 'vertical' ) {
					return clientSize && vVisible;
				} else if ( direction === 'horizontal' ) {
					return clientSize && hVisible;
				}

			} else {

				var viewTop       = $w.scrollTop(),
					viewBottom    = viewTop + vpHeight,
					viewLeft      = $w.scrollLeft(),
					viewRight     = viewLeft + vpWidth,
					offset        = $t.offset(),
					_top          = offset.top,
					_bottom       = _top + $t.height(),
					_left         = offset.left,
					_right        = _left + $t.width(),
					compareTop    = partial === true ? _bottom : _top,
					compareBottom = partial === true ? _top : _bottom,
					compareLeft   = partial === true ? _right : _left,
					compareRight  = partial === true ? _left : _right;

				if ( direction === 'both' ) {
					return !!clientSize && ( ( compareBottom <= viewBottom ) && ( compareTop >= viewTop ) ) && ( ( compareRight <= viewRight ) && ( compareLeft >= viewLeft ) );
				} else if ( direction === 'vertical' ) {
					return !!clientSize && ( ( compareBottom <= viewBottom ) && ( compareTop >= viewTop ) );
				} else if ( direction === 'horizontal' ) {
					return !!clientSize && ( ( compareRight <= viewRight ) && ( compareLeft >= viewLeft ) );
				}
			}
		};

		/**
		 * Addons Totals Controller.
		 *
		 * @param object Form
		 */
		function Totals( Form ) {
			// Make sure is called as a constructor.
			if ( ! ( this instanceof Totals ) ) {
				return new Totals( Form );
			}

			if ( $.isEmptyObject( Form ) ) {
				return false;
			}

			// Holds the jQuery instance.
			this.$form   = Form.$el;
			this.$addons = Form.$addons;

			// Parameters.
			this.$variation_input = this.$form.hasClass( 'variations_form' )
				? this.$form.find(
					'input[name="variation_id"], input.variation_id'
				)
				: false,
			this.is_variable      = this.$variation_input && this.$variation_input.length > 0,
			this.$totals          = this.$form.find( '#product-addons-total' ),
			this.product_id       = this.is_variable ? this.$variation_input.val() : this.$totals.data( 'product-id' );

			if ( ! this.product_id ) {
				return false;
			}

			// The product base price. For Variable Products, this is the minimum variation price.
			this.base_price             = this.$totals.data( 'price' ),
			this.raw_price              = this.$totals.data( 'raw-price' ),
			this.product_type           = this.$totals.data( 'type' ),
			this.qty                    = parseFloat( this.$form.find( 'input.qty' ).val() ),
			this.addons_price_data      = [];
			this.$subscription_plans    = this.$form.find( '.wcsatt-options-product' ),
			this.has_subscription_plans = this.$subscription_plans.length > 0;
			this.is_rtl                 = Form.is_rtl;
			this.total                  = 0;
			this.total_raw              = 0;
			this.show_subtotal_panel    = true;
		}

		/**
		 * Determines if addons subtotals should be visible even if validation fails.
		 *
		 * @returns boolean
		 */
		Totals.prototype.showIncompleteSubtotals = function() {
			return this.$totals.data( 'show-incomplete-sub-total' ) === 1;
		}

		/**
		 * Update addon totals when a new variation is selected.
		 *
		 * @param variation
		 */
		Totals.prototype.updateVariation = function( variation ) {

			// Handle multiple variation dropdowns in a single form -- for example, a Bundle with many Variable bundled items.
			this.$variation_input = this.$form.hasClass( 'variations_form' )
				? this.$form.find(
					'input[name="variation_id"], input.variation_id'
				)
				: false;
			this.product_id       = variation.variation_id;

			this.$totals.data( 'product-id', this.product_id );

			if ( typeof variation.display_price !== 'undefined' ) {
				this.base_price = variation.display_price;
			} else if (
				$( variation.price_html ).find( '.amount' ).last().length
			) {

				this.base_price = $( variation.price_html )
					.find( '.amount' )
					.last()
					.text();

				this.base_price = this.base_price.replace(
					woocommerce_addons_params.currency_format_symbol,
					''
				);

				this.base_price = this.base_price.replace(
					woocommerce_addons_params.currency_format_thousand_sep,
					''
				);

				this.base_price = this.base_price.replace(
					woocommerce_addons_params.currency_format_decimal_sep,
					'.'
				);

				this.base_price = this.base_price.replace( /[^0-9\.]/g, '' );
				this.base_price = parseFloat( this.base_price );

			}

			this.$totals.data( 'price', this.base_price );
		};

		/**
		 * Calculates addon totals based on configured addons.
		 */
		Totals.prototype.calculate = function() {

			var self = this;

			self.qty               = parseFloat( self.$form.find( 'input.qty' ).val() );
			self.addons_price_data = [];
			self.total             = 0;
			self.total_raw         = 0;
			self.base_price        = self.$totals.data( 'price' );
			self.raw_price         = self.$totals.data( 'raw-price' );
			self.product_id        = self.is_variable ? self.$variation_input.val() : self.$totals.data( 'product-id' );

			/**
			 * Compatibility with Smart Coupons self declared gift amount purchase.
			 *
			 * CAUTION: This code is unstable.
			 * A dedicated Smart Coupons event should be used to change the base price based on the gift card amount.
			 */
			if (
				'' === self.base_price &&
				'undefined' !== typeof custom_gift_card_amount &&
				custom_gift_card_amount.length &&
				0 < custom_gift_card_amount.val()
			) {
				self.base_price = custom_gift_card_amount.val();
			}

			/**
			 * Compatibility with Bookings.
			 *
			 * CAUTION: This code is unstable.
			 * A dedicated Bookings event should be used to change the base price based on the bookings cost.
			 */
			if (
				woocommerce_addons_params.is_bookings &&
				$( '.wc-bookings-booking-cost' ).length
			) {
				self.base_price = parseFloat(
					$( '.wc-bookings-booking-cost' ).attr(
						'data-raw-price'
					)
				);
			}

			/**
			 * Calculates totals of selected addons.
			 *
			 */
			this.$addons.each( function () {

				if ( ! $( this ).val() ) {
					return;
				}

				var $addon                 = $( this ),
					parent_container       = $addon.parents( '.wc-pao-addon' ),
					name                   = parent_container.find( '.wc-pao-addon-name' )
						.length
						? parent_container
							.find( '.wc-pao-addon-name' )
							.data( 'addon-name' )
						: '',
					value_label            = '',
					addon_cost             = 0,
					addon_cost_raw         = 0,
					price_type             = $addon.data( 'price-type' ),
					is_custom_price        = false,
					addon_data             = {},
					has_per_person_pricing = parent_container.find(
						'.wc-pao-addon-name'
					).length
						? parent_container
							.find( '.wc-pao-addon-name' )
							.data( 'has-per-person-pricing' )
						: false,
					has_per_block_pricing  = parent_container.find(
						'.wc-pao-addon-name'
					).length
						? parent_container
							.find( '.wc-pao-addon-name' )
							.data( 'has-per-block-pricing' )
						: false;

				if ( $addon.is( '.wc-pao-addon-custom-price' ) ) {

					is_custom_price = true;
					addon_cost      = $addon.val();
					addon_cost_raw  = $addon.val();
					price_type      = 'quantity_based';

				} else if (
					$addon.is( '.wc-pao-addon-input-multiplier' )
				) {
					// Avoid converting empty strings to 0.
					if ( '' !== $addon.val() ) {
						$addon.val( Math.ceil( $addon.val() ) );

						addon_cost     = $addon.data( 'price' ) * $addon.val();
						addon_cost_raw = $addon.data('raw-price') * $addon.val();
					}
				} else if (
					$addon.is(
						'.wc-pao-addon-checkbox, .wc-pao-addon-radio'
					)
				) {

					if ( ! $addon.is( ':checked' ) ) {
						return;
					}

					value_label    = $addon.data( 'label' );
					addon_cost     = $addon.data( 'price' );
					addon_cost_raw = $addon.data('raw-price');
				} else if (
					$addon.is(
						'.wc-pao-addon-image-swatch-select, .wc-pao-addon-select'
					)
				) {

					if (
						! $addon.find( 'option:selected' ) ||
						'' === $addon.find( 'option:selected' ).val()
					) {
						return;
					}

					price_type     = $addon.find( 'option:selected' ).data( 'price-type' );
					value_label    = $addon.find( 'option:selected' ).data( 'label' );
					addon_cost     = $addon.find( 'option:selected' ).data( 'price' );
					addon_cost_raw = $addon.find( 'option:selected' ).data( 'raw-price' );

				} else {

					if ( ! $addon.val() ) {
						return;
					}

					addon_cost     = $addon.data('price');
					addon_cost_raw = $addon.data('raw-price');
				}

				if ( ! addon_cost ) {
					addon_cost = 0;
				}
				if ( ! addon_cost_raw ) {
					addon_cost_raw = 0;
				}

				/**
				 * Compatibility with Bookings.
				 *
				 * CAUTION: This code is unstable.
				 * A dedicated Bookings/Accomodation Bookings event should be used to change the base price based on the bookings duration, persons and cost.
				 */
				if (
					( 'booking' === self.product_type ||
						'accommodation-booking' === self.product_type ) &&
					woocommerce_addons_params.is_bookings
				) {
					self.qty = 0;

					// Duration field.
					var block_qty = 0;
					if (
						'undefined' !==
						typeof $( '#wc_bookings_field_duration' ) &&
						0 < $( '#wc_bookings_field_duration' ).val()
					) {
						block_qty = $(
							'#wc_bookings_field_duration'
						).val();
					}

					// Duration fields with start and end time.
					if (
						'undefined' !==
						typeof $( '#wc-bookings-form-end-time' ) &&
						0 < $( '#wc-bookings-form-end-time' ).val()
					) {
						block_qty = $(
							'#wc-bookings-form-end-time'
						).val();
					}

					// Persons field(s).
					var single_persons_input = $( '#wc_bookings_field_persons' ),
						person_qty           = 0;

					if ( 1 === single_persons_input.length ) {
						// Persons field when person types is disabled.
						person_qty =
							parseInt( person_qty, 10 ) +
							parseInt( single_persons_input.val(), 10 );
					} else {
						// Persons fields for multiple person types.
						$( '.wc-bookings-booking-form' )
							.find( 'input' )
							.each( function () {
								// There could be more than one persons field.
								var field = this.id.match(
									/wc_bookings_field_persons_(\d+)/
								);

								if (
									null !== field &&
									'undefined' !== typeof field &&
									$( '#' + field[0] ).length
								) {
									person_qty =
										parseInt( person_qty, 10 ) +
										parseInt(
											$( '#' + field[0]).val(),
											10
										);
								}
							});
					}

					if (
						0 === self.qty &&
						$( '.wc-bookings-booking-cost' ).length
					) {
						self.qty = 1;
					}

					// Apply person/block quantities.
					if ( has_per_person_pricing && person_qty ) {
						self.qty *= person_qty;
					}
					if ( has_per_block_pricing && block_qty ) {
						self.qty *= block_qty;
					}
				}

				// Format addon totals based on their type.
				switch ( price_type ) {
					case 'flat_fee':
						addon_data.cost     = parseFloat( addon_cost );
						addon_data.cost_raw = parseFloat( addon_cost_raw );
						break;
					case 'quantity_based':
						addon_data.cost_pu     = parseFloat( addon_cost );
						addon_data.cost_raw_pu = parseFloat( addon_cost_raw );
						addon_data.cost        = addon_data.cost_pu * self.qty;
						addon_data.cost_raw    = addon_data.cost_raw_pu * self.qty;
						break;
					case 'percentage_based':
						addon_data.cost_pct     = parseFloat( addon_cost ) / 100;
						addon_data.cost_raw_pct = parseFloat( addon_cost_raw ) / 100;
						addon_data.cost         =
							parseFloat( self.base_price ) *
							addon_data.cost_pct *
							self.qty;
						addon_data.cost_raw     =
							parseFloat( self.raw_price ) *
							addon_data.cost_raw_pct *
							self.qty;
						break;
				}

				self.total     += addon_data.cost || 0;
				self.total_raw += addon_data.cost_raw || 0;

				/**
				 * Formats addon names to include user input.
				 * The formatted addon name will be displayed in the addons subtotal table.
				 */
				if ( 'undefined' !== typeof value_label ) {
					if (
						'number' === typeof value_label ||
						value_label.length
					) {
						addon_data.name =
							name +
							( value_label ? ' - ' + value_label : '' );
					} else {
						var user_input     = $addon.val(),
							trimCharacters = parseInt(
								woocommerce_addons_params.trim_user_input_characters,
								10
							);

						// Check if type is file upload.
						if ( $addon.is( '.wc-pao-addon-file-upload' ) ) {
							user_input = user_input.replace(
								/^.*[\\\/]/,
								''
							);
						}

						if ( trimCharacters < user_input.length ) {
							user_input =
								user_input.slice( 0, trimCharacters ) +
								'...';
						}

						addon_data.name =
							name +
							' - ' +
							WC_PAO.Helper.escapeHtml( user_input );
					}

					addon_data.is_custom_price = is_custom_price;
					addon_data.price_type      = price_type;

					self.addons_price_data.push( addon_data );
				}
			});

			// Save prices for 3rd party access.
			self.$totals.data( 'price_data', self.addons_price_data );
			self.$form.trigger( 'updated_addons' );
		};

		/**
		 * Renders addon totals.
		 */
		Totals.prototype.render = function() {

			var self = this;

			// Early exit if another plugin has determined that Product Addon totals should remain hidden.
			if ( ! self.$totals.data( 'show-sub-total' ) ) {
				self.$totals.empty();
				self.$form.trigger( 'updated_addons' );
				return;
			}

			if ( self.qty ) {

				var product_total_price,
					formatted_sub_total,
					$subscription_details,
					subscription_details_html,
					html,
					formatted_addon_total       = self.formatMoney( self.total ),
					has_custom_price_with_taxes = false;

				if ( 'undefined' !== typeof self.base_price && self.product_id ) {
					// If it is a bookable product.
					if ( $( '.wc-bookings-booking-form' ).length ) {
						product_total_price = ! isNaN( self.base_price ) ? parseFloat( self.base_price ) : 0;
					} else {
						product_total_price = parseFloat( self.base_price * self.qty );
					}

					formatted_sub_total = self.formatMoney( product_total_price + self.total );
				}

				/**
				 * Compatibility with Subscribe All The Things/All Products for WooCommerce Subscriptions.
				 *
				 * CAUTION: This code is unstable.
				 * An All Products for WooCommerce Subscriptions specific event should be used to get
				 * subscription details when a new subscription plan is selected.
				 */
				if ( self.has_subscription_plans ) {
					var satt = self.$form.data( 'satt_script' );

					if ( satt && satt.schemes_model.get_active_scheme_key() ) {
						var $selected_plan = self.$subscription_plans.find( 'input:checked' );

						if ( $selected_plan.val() ) {
							$subscription_details = $selected_plan.parent().find( '.subscription-details' );
						}
					}
				} else if ( self.$form.parent().find( '.subscription-details' ).length ) {
					// Add-Ons added at bundle level only affect the up-front price.
					if ( ! self.$form.hasClass( 'bundle_data' ) ) {
						$subscription_details = self.$form.parent().find( '.subscription-details' );

						/*
						 * Check if product is a variable
						 * because the subscription_details HTML element will
						 * be located in different area.
						 */
						if ( self.$variation_input && self.$variation_input.length > 0 ) {
							$subscription_details = self.$form.parent().find( '.woocommerce-variation .subscription-details' );
						}
					}
				}

				if ( $subscription_details && $subscription_details.length > 0 ) {
					// Space is needed here in some cases.
					subscription_details_html =
						' ' +
						$subscription_details
							.clone()
							.wrap( '<p>' )
							.parent()
							.html();
				}

				/**
				 * Compatibility with Grouped and subscription products.
				 *
				 * CAUTION: This code is unstable.
				 * This code needs to be moved to a grouped/subscription-specific function.
				 */
				if ( 'grouped' === self.product_type ) {
					if ( subscription_details_html && ! WC_PAO.Helper.isGroupedMixedProductType() && WC_PAO.Helper.isGroupedSubsSelected() ) {
						formatted_addon_total += subscription_details_html;

						if ( formatted_sub_total ) {
							formatted_sub_total += subscription_details_html;
						}
					}
				} else if ( subscription_details_html ) {
					if ( formatted_sub_total ) {
						formatted_sub_total += subscription_details_html;
					}
				}

				/**
				 * Render addon subtotals in a table-like format above the Add to Cart button.
				 * As the first line item, display the main product followed by each total price (base price * quantity).
				 * Then, display one line item for each selected addon followed by each price (given that one exists).
				 */
				if ( formatted_sub_total ) {
					var product_name       = self.$form.find( '.wc-pao-addon-container' ).data( 'product-name' ),
						product_price      = self.formatMoney( product_total_price ),
						product_tax_status = self.$form.find( '.wc-pao-addon-container' ).data( 'product-tax-status' );

					/**
					 * Bookings compatibility code.
					 *
					 * CAUTION: This code is unstable.
					 * This code does not change addon totals for booking products if the form is right to left.
					 */
					if ( $( '.wc-bookings-booking-form' ).length ) {
						html =
							'<div class="product-addon-totals"><ul><li><div class="wc-pao-col1"><strong>' +
							product_name +
							'</strong></div><div class="wc-pao-col2"><strong><span class="amount">' +
							product_price +
							'</span></strong></div></li>';
					} else {
						// Display the base product as the first line item in the addons subtotals table.
						var quantity_string = self.is_rtl
							? woocommerce_addons_params.quantity_symbol + self.qty
							: self.qty + woocommerce_addons_params.quantity_symbol;

						html =
							'<div class="product-addon-totals"><ul><li><div class="wc-pao-col1"><strong><span>' +
							quantity_string +
							'</span> ' +
							product_name +
							'</strong></div><div class="wc-pao-col2"><strong><span class="amount">' +
							product_price +
							'</span></strong></div></li>';
					}

					if ( self.addons_price_data.length ) {
						$.each( self.addons_price_data, function ( i, addon ) {
							var cost, formatted_value;
							if ( 'quantity_based' === addon.price_type ) {
								cost            = addon.cost;
								formatted_value =
									0 === cost
									? '-'
									: self.formatMoney( cost );

								html =
									html +
									'<li class="wc-pao-row-quantity-based"><div class="wc-pao-col1">' +
									addon.name +
									'</div><div class="wc-pao-col2"><span class="amount">' +
									formatted_value +
									'</span></div></li>';
							} else {

								cost = addon.cost;

								formatted_value =
									0 === cost
										? '-'
										: '<span class="amount">' +
										self.formatMoney( cost ) +
										'</span>';

								html =
									html +
									'<li><div class="wc-pao-col1"><strong>' +
									addon.name +
									'</strong></div><div class="wc-pao-col2">' +
									formatted_value +
									'</div></li>';
							}

							if ( woocommerce_addons_params.tax_enabled && addon.is_custom_price ) {
								has_custom_price_with_taxes = true;
							}
						});
					}

					// To show our "price display suffix" we have to do some magic since the string can contain variables (excl/incl tax values)
					// so we have to take our sub total and find out what the tax value is, which we can do via an ajax call
					// if its a simple string, or no string at all, we can output the string without an extra call
					var price_display_suffix = '',
						sub_total_string     =
							typeof self.$totals.data( 'i18n_sub_total' ) === 'undefined'
								? woocommerce_addons_params.i18n_sub_total
								: self.$totals.data( 'i18n_sub_total' );

					// No suffix is present, so we can just output the total.
					if (
						! has_custom_price_with_taxes &&
						( ! woocommerce_addons_params.price_display_suffix ||
						! woocommerce_addons_params.tax_enabled )
					) {
						html =
							html +
							'<li class="wc-pao-subtotal-line"><p class="price">' +
							sub_total_string +
							' <span class="amount">' +
							formatted_sub_total +
							'</span></p></li></ul></div>';

						if ( self.show_subtotal_panel ) {
							self.$totals.html( html );
						} else {
							self.$totals.html( '' );
						}

						self.$form.trigger( 'updated_addons' );
						return;
					}

					// A suffix is present, but no special labels are used - meaning we don't need to figure out any other special values - just display the plain text value
					if (
						'taxable' === product_tax_status &&
						! has_custom_price_with_taxes &&
						false === woocommerce_addons_params.price_display_suffix.indexOf( '{price_including_tax}' ) > -1 &&
						false === woocommerce_addons_params.price_display_suffix.indexOf( '{price_excluding_tax}' ) > -1
					) {
						html =
							html +
							'<li class="wc-pao-subtotal-line"><strong>' +
							sub_total_string +
							' <span class="amount">' +
							formatted_sub_total +
							'</span> ' +
							woocommerce_addons_params.price_display_suffix +
							'</strong></li></ul></div>';

						if ( self.show_subtotal_panel ) {
							self.$totals.html( html );
						} else {
							self.$totals.html( '' );
						}

						self.$form.trigger( 'updated_addons' );
						return;
					}

					// Based on the totals/info and settings we have, we need to use the get_price_*_tax functions
					// to get accurate totals. We can get these values with a special Ajax function
					$.ajax({
						type: 'POST',
						url: woocommerce_addons_params.ajax_url,
						data: {
							action: 'wc_product_addons_calculate_tax',
							product_id: self.product_id,
							add_on_total: self.total,
							add_on_total_raw: self.total_raw,
							qty: self.qty,
						},
						success: function ( result ) {
							if ( result.result == 'SUCCESS' ) {
								price_display_suffix =
									'<small class="woocommerce-price-suffix">' +
									woocommerce_addons_params.price_display_suffix +
									'</small>';

								var formatted_price_including_tax = self.formatMoney( result.price_including_tax ),
									formatted_price_excluding_tax = self.formatMoney( result.price_excluding_tax );

								price_display_suffix =
									price_display_suffix.replace(
										'{price_including_tax}',
										'<span class="amount">' +
										formatted_price_including_tax +
										'</span>'
									);
								price_display_suffix =
									price_display_suffix.replace(
										'{price_excluding_tax}',
										'<span class="amount">' +
										formatted_price_excluding_tax +
										'</span>'
									);

								var subtotal = woocommerce_addons_params.display_include_tax
									? formatted_price_including_tax
									: formatted_price_excluding_tax;

								html =
									html +
									'<li class="wc-pao-subtotal-line"><p class="price">' +
									sub_total_string +
									' <span class="amount">' +
									subtotal +
									'</span> ' +
									price_display_suffix +
									' </p></li></ul></div>';

								if ( self.show_subtotal_panel ) {
									self.$totals.html( html );
								} else {
									self.$totals.html( '' );
								}

								self.$form.trigger( 'updated_addons' );
							} else {
								html =
									html +
									'<li class="wc-pao-subtotal-line"><p class="price">' +
									sub_total_string +
									' <span class="amount">' +
									formatted_sub_total +
									'</span></p></li></ul></div>';
								if ( self.show_subtotal_panel ) {
									self.$totals.html( html );
								} else {
									self.$totals.html( '' );
								}
								self.$form.trigger( 'updated_addons' );
							}
						},
						error: function () {
							html =
								html +
								'<li class="wc-pao-subtotal-line"><p class="price">' +
								sub_total_string +
								' <span class="amount">' +
								formatted_sub_total +
								'</span></p></li></ul></div>';

							if ( self.show_subtotal_panel ) {
								self.$totals.html( html );
							} else {
								self.$totals.html( '' );
							}
							self.$form.trigger( 'updated_addons' );
						},
					});
				} else {
					self.$totals.empty();
					self.$form.trigger( 'updated_addons' );
				}
			} else {
				self.$totals.empty();
				self.$form.trigger( 'updated_addons' );
			}
		};

		/**
		 * Resets and hides addon totals.
		 */
		Totals.prototype.reset = function() {
			this.$totals.empty();
			this.$totals.html( '' );
			this.$form.trigger( 'updated_addons' );
		}

		/**
		 * Formats addon prices.
		 *
		 * @param amount
		 * @returns {[]|*}
		 */
		Totals.prototype.formatMoney = function ( amount ) {
			let formatNumDecimal = woocommerce_addons_params.currency_format_num_decimals;

			// Remove trailing zeros.
			if ( woocommerce_addons_params.trim_trailing_zeros ) {
				const amountIsInteger = parseFloat( amount ) % 1 === 0;

				// Remove zeros.
				// if float, 4.6500 => 4.65
				// if integer, 4.0000 => 4
				amount = parseFloat( amount );

				// Set precision value (mandatory to be passed).
				if ( amountIsInteger ) {
					// Set 0 decimal precision for integers.
					formatNumDecimal = 0;
				} else {
					// Count decimal from amount (zeros skipped already) and set as precision.
					// 4.655 => 3 digits after decimal point.
					formatNumDecimal = amount.toString().split( '.' )[ 1 ].length;
				}
			}

			return accounting.formatMoney(amount, {
				symbol: woocommerce_addons_params.currency_format_symbol,
				decimal: woocommerce_addons_params.currency_format_decimal_sep,
				thousand:
				woocommerce_addons_params.currency_format_thousand_sep,
				precision: formatNumDecimal,
				format: woocommerce_addons_params.currency_format,
			});
		};

		/**
		 * Addons Validation Controller.
		 *
		 * @param object Form
		 */
		function Validation( Form ) {

			// Make sure is called as a constructor.
			if ( ! ( this instanceof Validation ) ) {
				return new Validation( Form );
			}

			if ( ! Form.$addons.length ) {
				return false;
			}

			// Holds the jQuery instance.
			this.$form   = Form.$el;
			this.$addons = Form.$addons;

			// An object that holds the validation state and message of each addon.
			this.validationState = this.getInitialState();

		}

		/**
		 * Gets the initial validation state. All addons are valid in this state.
		 */
		Validation.prototype.getInitialState = function() {

			var schema = {};

			$.each( this.$addons, function() {
				schema[ $(this).attr( 'id' ) ] = { validity: true, message: '' };
			} );

			return schema;
		}

		/**
		 * Gets the current validation state.
		 */
		Validation.prototype.getValidationState = function() {
			return this.validationState;
		}

		/**
		 * Validates a single addon and conditionally prints a validation message.
		 *
		 * @param jQuery object $addon
		 * @return bool
		 */
		Validation.prototype.validateAddon = function( $addon, printMessages = false ) {

			var	validation_rules = $addon.data( 'restrictions' ),
				id               = $addon.attr( 'id' ),
				validity         = true;

			if ( ! $.isEmptyObject( validation_rules ) ) {

				if ( 'required' in validation_rules ) {
					if ( 'yes' === validation_rules.required ) {
						validity = this.validateRequired( $addon );
					}
				}

				if ( validity && 'content' in validation_rules ) {
					if ( 'only_letters' === validation_rules.content ) {
						validity = this.validateLetters( $addon );
					} else if ( 'only_numbers' === validation_rules.content ) {
						validity = this.validateNumbers( $addon );
					} else if ( 'only_letters_numbers' === validation_rules.content ) {
						validity = this.validateLettersNumbers( $addon );
					} else if ( 'email' === validation_rules.content ) {
						validity = this.validateEmail( $addon );
					}
				}

				if ( validity && 'min' in validation_rules ) {
					validity = this.validateMin( $addon, validation_rules.min );
				}

				if ( validity && 'max' in validation_rules ) {
					validity = this.validateMax( $addon, validation_rules.max );
				}
			}

			if ( printMessages ) {
				this.printMessage( $addon );
			}

			return this.validationState[id].validity;
		};

		/**
		 * Validates all addons and conditionally prints validation messages.
		 *
		 * @return bool
		 */
		Validation.prototype.validate = function( printMessages = false ) {

			var validity = true,
				self     = this;

			$.each( self.$addons, function() {
				if ( ! self.validateAddon( $(this), printMessages ) ) {
					validity = false;
				}
			});

			return validity;
		};

		/**
		 * Outputs validation message for specific addon.
		 * @param jQuery object $addon
		 */
		Validation.prototype.printMessage = function( $addon ) {

			var id                 = $addon.attr( 'id' ),
				element            = this.$form.find( '#' + id ),
				formattedElementID = id + '-validation-notice',
				message            = this.validationState[id].message;

			// For radio buttons, display a single notice after all radio buttons.
			if ( element.is( ':radio' ) || element.is( ':checkbox' )  ) {

				var $container_element = element.closest( '.wc-pao-addon-container .wc-pao-addon-wrap' );

				$container_element.find( '.wc-pao-validation-notice' ).remove();

				if ( ! this.validationState[id].validity ) {
					$container_element.append( '<small id="' + formattedElementID + '" class="wc-pao-validation-notice">' + message + '</small>' );
				}

			// For the rest addon types, display a notice under each addon.
			} else {
				element.closest( '.wc-pao-addon-container' ).find( '.wc-pao-validation-notice' ).remove();
				if ( ! this.validationState[id].validity ) {
					element.after( '<small id="' + formattedElementID + '" class="wc-pao-validation-notice">' + message.replace( /</g, "&lt;" ).replace( />/g, "&gt;" ) + '</small>' );
				}
			}
		};

		/**
		 * Validates if required addons are configured.
		 * @param jQuery object $element
		 * @return boolean
		 */
		Validation.prototype.validateRequired = function( $element ) {

			var validity = true,
				message  = '',
				reason   = '',
				id       = $element.attr( 'id');

			if ( $element.is( ':checkbox' ) || $element.is( ':radio' ) ) {

				var $container_element = $element.closest( '.wc-pao-addon-container' ),
					$options           = $container_element.find( '.wc-pao-addon-field' ),
					self               = this;

				validity = false;

				$.each( $options, function() {
					if ( $( this ).is( ':checked' ) ) {
						validity = true;
						return;
					}
				} );

				if ( ! validity ) {
					message = woocommerce_addons_params.i18n_validation_required_select;
				} else {

					// For groups of options, like radio buttons/checkboxes, if at least 1 option is selected, then consider all options as valid.
					$.each( $options, function() {
						var option_id = $(this).attr( 'id');
						self.validationState[ option_id ] = { validity: validity, message: message, reason: reason };
					} );

					return;
				}

			} else if ( $element.hasClass( 'wc-pao-addon-image-swatch-select' ) ) {
				var $container_element = $element.closest( '.wc-pao-addon-container' );

				validity = false;

				$.each( $container_element.find( '.wc-pao-addon-image-swatch' ), function() {

					if ( $( this ).hasClass( 'selected' ) ) {
						validity = true;
						return;
					}
				} );

				if ( ! validity ) {
					message = woocommerce_addons_params.i18n_validation_required_select;
				}
			} else {

				if ( ! $element.val() ) {
					validity = false;

					if ( 'file' === $element.attr( 'type' ) ) {
						message = woocommerce_addons_params.i18n_validation_required_file;
					} else if ( 'number' === $element.attr( 'type' ) ) {
						message = woocommerce_addons_params.i18n_validation_required_number;
					} else if ( $element.is( 'input' ) || $element.is( 'textarea' ) ) {
						message = woocommerce_addons_params.i18n_validation_required_input;
					} else if ( $element.is( 'select' ) ) {
						message = woocommerce_addons_params.i18n_validation_required_select;
					}
				}
			}

			if ( ! validity ) {
				reason = 'required';
			}

			this.validationState[id] = { validity: validity, message: message, reason: reason };

			return this.validationState[id].validity;
		};

		/**
		 * Validates if input contains only letters.
		 * @param jQuery object $element
		 * @return boolean
		 */
		Validation.prototype.validateLetters = function( $element ) {

			var validity = ! ( /[`!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~\d]/g.test( $element.val() ) ),
				message  = '',
				reason   = '',
				id       = $element.attr( 'id' );

			if ( ! $element.val() ){
				validity = true;
			}

			if ( ! validity ) {
				message = woocommerce_addons_params.i18n_validation_letters_only;
				reason  = 'letters';
			}

			this.validationState[id] = { validity: validity, message: message, reason: reason };

			return this.validationState[id].validity;

		};

		/**
		 * Validates if input contains only numbers.
		 * @param jQuery object $element
		 * @return boolean
		 */
		Validation.prototype.validateNumbers = function( $element ) {

			var validity = /^[0-9]*$/g.test( $element.val() ),
				message  = '',
				reason   = '',
				id       = $element.attr( 'id');

			if ( ! $element.val() ){
				validity = true;
			}

			if ( ! validity ) {
				message = woocommerce_addons_params.i18n_validation_numbers_only;
				reason  = 'numbers';
			}

			this.validationState[id] = { validity: validity, message: message, reason: reason };

			return this.validationState[id].validity;
		};

		/**
		 * Validates if input contains only letters and numbers.
		 * @param jQuery object $element
		 * @return boolean
		 */
		Validation.prototype.validateLettersNumbers = function( $element ) {

			var validity = ! ( /[`!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/g.test( $element.val() ) ),
				message  = '',
				reason   = '',
				id       = $element.attr( 'id');

			if ( ! $element.val() ){
				validity = true;
			}

			if ( ! validity ) {
				message = woocommerce_addons_params.i18n_validation_letters_and_numbers_only;
				reason  = 'letters_numbers';
			}

			this.validationState[id] = { validity: validity, message: message, reason: reason };

			return this.validationState[id].validity;

		};

		/**
		 * Validates if input contains a valid email address.
		 * @param jQuery object $element
		 * @return boolean
		 */
		Validation.prototype.validateEmail = function( $element ) {

			var validity = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/.test( $element.val() ),
				message  = '',
				reason   = '',
				id       = $element.attr( 'id');

			if ( ! $element.val() ){
				validity = true;
			}

			if ( ! validity ) {
				message = woocommerce_addons_params.i18n_validation_email_only;
				reason  = 'email';
			}

			this.validationState[id] = { validity: validity, message: message, reason: reason };

			return this.validationState[id].validity;

		};

		/**
		 * Validates if the min length and min number restrictions are violated.
		 * @param jQuery object $element
		 * @param int           min
		 * @return boolean
		 */
		Validation.prototype.validateMin = function( $element, min ) {

			var validity = true,
				message  = '',
				reason   = '',
				id       = $element.attr( 'id');

			if ( ! $element.val() ){
				validity = true;
			} else if ( 'number' === $element.attr( 'type' ) ) {
				var value = $element.val();

				if ( value.includes( '.' ) || value.includes( ',' ) ) {
					value = parseFloat( value );
				} else {
					value = parseInt( value );
				}

				if ( value < min ) {
					validity = false;
					message  = woocommerce_addons_params.i18n_validation_min_number.replace( '%c', min );
				}

			} else if ( 'text' === $element.attr( 'type' ) || $element.is( 'textarea' ) ) {

				if ( $element.val().length < min ) {
					validity = false;
					message  = woocommerce_addons_params.i18n_validation_min_characters.replace( '%c', min );
				}
			}

			if ( ! validity ) {
				reason = 'min';
			}

			this.validationState[id] = { validity: validity, message: message, reason: reason };

			return this.validationState[id].validity;
		};

		/**
		 * Validates if the max length and max number restrictions are violated.
		 * @param jQuery object $element
		 * @param int           max
		 * @return boolean
		 */
		Validation.prototype.validateMax = function( $element, max ) {

			var validity = true,
				message  = '',
				reason   = reason,
				id       = $element.attr( 'id');

			if ( ! $element.val() ){
				validity = true;
			} else if ( 'number' === $element.attr( 'type' ) ) {
				var value = $element.val();

				if ( value.includes( '.' ) || value.includes( ',' ) ) {
					value = parseFloat( value );
				} else {
					value = parseInt( value );
				}

				if ( value > max ) {
					validity = false;
					message  = woocommerce_addons_params.i18n_validation_max_number.replace( '%c', max );
				}

			} else if ( 'text' === $element.attr( 'type' ) || $element.is( 'textarea' ) ) {
				if ( $element.val().length > max ) {
					validity = false;
					message  = woocommerce_addons_params.i18n_validation_max_characters.replace( '%c', max );
				}
			}

			if ( ! validity ) {
				reason = 'max';
			}

			this.validationState[id] = { validity: validity, message: message, reason: reason };

			return this.validationState[id].validity;
		};

		return Form;
	} )();

	$(function () {
		// Quick view.
		$('body').on('quick-view-displayed', function () {
			$(this)
				.find('.cart:not(.cart_group)')
				.each(function () {
					var $form = new WC_PAO.Form( $(this) );
				});
		});

		// Initialize addon totals.
		$('body')
			.find('.cart:not(.cart_group)')
			.each(function () {
				var $form = new WC_PAO.Form( $(this) );
			});
	});

})( jQuery, window );
