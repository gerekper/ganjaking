;( function( $ ) {

	// Ensure wcsatt_single_product_params exists to continue.
	if ( typeof wcsatt_single_product_params === 'undefined' ) {
		return false;
	}

	// Subscription Schemes model.
	var Schemes_Model = function( opts ) {

		var Model = Backbone.Model.extend( {

			get_schemes_count: function() {

				var schemes       = this.get( 'schemes' ),
					schemes_count = 0;

				for ( var scheme_key in schemes ) {
					if ( ! schemes.hasOwnProperty( scheme_key ) ) {
						continue;
					}
					schemes_count++;
				}

				return schemes_count;
			},

			get_active_scheme_key: function() {
				return this.get( 'active_scheme_key' );
			},

			get_last_active_scheme_key: function() {
				return this.previous( 'active_scheme_key' );
			},

			set_active_scheme: function( key_to_set ) {
				this.set( { active_scheme_key: key_to_set !== '0' ? key_to_set : false } );
			},

			set_schemes: function( schemes_to_set ) {
				this.set( { schemes: schemes_to_set } );
			},

			is_active_scheme_prorated: function() {
				return this.get_scheme_prop( this.get_active_scheme_key(), 'is_prorated' );
			},

			was_last_active_scheme_prorated: function() {
				return this.get_scheme_prop( this.get_last_active_scheme_key(), 'is_prorated' );
			},

			get_scheme_prop: function( key, prop ) {

				var schemes = this.get( 'schemes' );

				if ( typeof schemes[ key ] === 'undefined' || typeof schemes[ key ][ prop ] === 'undefined' ) {
					return null;
				}

				return schemes[ key ][ prop ];
			},

			initialize: function() {

				var params = {
					schemes:           {},
					active_scheme_key: ''
				};

				this.set( params );
			}

		} );

		var obj = new Model( opts );
		return obj;
	};

	// Subscription Schemes view.
	var Schemes_View = function( opts ) {

		var View = Backbone.View.extend( {

			product: false,

			$el_content: false,
			$el_options: false,
			$el_dropdown: false,
			$el_prompt: false,
			$el_option_items: false,
			$el_option_inputs: false,
			$el_button: false,

			add_to_cart_button_text: false,
			sign_up_button_text: false,

			prompt_type: '',

			variation: false,
			variation_options_render_location: 'price_html',

			events: {
				'change select.wcsatt-options-product-dropdown': 'dropdown_scheme_changed',
				'change .wcsatt-options-prompt-action-input': 'action_link_clicked',
				'change .wcsatt-options-product-wrapper input': 'active_scheme_changed',
				'show_variation': 'variation_found',
				'reset_data': 'reset_schemes'
			},

			// "Subscribe" prompt action event handler.
			action_link_clicked: function( e ) {

				var model         = this.model,
					is_one_time   = 'one-time' === this.get_prompt_val(),
					state_changed = false;

				if ( ! model.get_active_scheme_key() ) {

					if ( is_one_time ) {
						return false;
					}

					// Is a subscription plan selected?
					var chosen_scheme_input = this.$el_option_inputs.filter( ':checked' );

					// If not, choose the first one.
					if ( '0' === chosen_scheme_input.val() ) {
						this.$el_option_inputs.filter( '[value!="0"]' ).first().prop( 'checked', true ).change();
					// Otherwise just update the model.
					} else {
						model.set_active_scheme( chosen_scheme_input.val() );
					}

					state_changed = this.maybe_toggle_options();

				} else {

					if ( ! is_one_time ) {
						return false;
					}

					model.set_active_scheme( false );

					state_changed = this.maybe_toggle_options();
				}

				if ( ! state_changed ) {
					return false;
				}
			},

			dropdown_scheme_changed: function( e ) {

				var scheme_key = this.$el_dropdown.val();

				this.$el_option_inputs.filter( '[value="' + scheme_key + '"]' ).prop( 'checked', true ).change();
			},

			active_scheme_changed: function( e ) {
				this.model.set_active_scheme( e.currentTarget.value );
			},

			variation_found: function( event, variation ) {

				if ( ! this.$el.hasClass( 'variations_form' ) ) {
					return;
				}

				this.variation = variation;

				if ( 'before_add_to_cart_button' === this.variation_options_render_location ) {
					this.$el_content.replaceWith( variation.satt_options_html );
				}

				this.initialize( { $el_content: this.$el.find( '.wcsatt-options-wrapper' ) } );

				if ( 'before_add_to_cart_button' === this.variation_options_render_location && this.$el_option_inputs.length > 1 ) {
					this.$el_content.slideDown( 200 );
				}
			},

			variation_selected: function() {
				return false !== this.variation;
			},

			reset_schemes: function() {

				if ( ! this.$el.hasClass( 'variations_form' ) ) {
					return;
				}

				this.variation = false;

				if ( 'before_add_to_cart_button' === this.variation_options_render_location ) {
					this.$el_content.slideUp( 200 );
				}

				this.model.set_schemes( {} );
				this.model.set_active_scheme( false );
			},

			has_schemes: function() {
				return this.$el_content.length > 0;
			},

			has_prompt: function( prompt_type ) {
				return this.$el_prompt.length > 0 && ( ! prompt_type || prompt_type === this.get_prompt_type() );
			},

			has_dropdown: function() {
				return this.$el_dropdown && this.$el_dropdown.length > 0;
			},

			get_prompt_type: function() {
				return this.$el_prompt.data( 'prompt_type' );
			},

			get_prompt_val: function() {

				var is_one_time = false;

				if ( this.has_prompt( 'radio' ) || this.has_prompt( 'checkbox' ) ) {

					if ( this.has_prompt( 'checkbox' ) && false === this.$el_prompt.find( '.wcsatt-options-prompt-action-input' ).is( ':checked' ) ) {
						is_one_time = true;
					} else if ( this.has_prompt( 'radio' ) && this.$el_prompt.find( '.wcsatt-options-prompt-action-input[value="no"]' ).is( ':checked' ) ) {
						is_one_time = true;
					}

					return is_one_time ? 'one-time' : 'subscribe';
				}

				return null;
			},

			find_schemes: function() {

				var schemes = {};

				if ( this.has_schemes() ) {

					this.$el_option_items  = this.$el_options.find( 'li' );
					this.$el_option_inputs = this.$el_options.find( 'input' );

					this.$el_option_inputs.filter( '[value!="0"]' ).each( function() {
						var scheme_data = $( this ).data( 'custom_data' );
						schemes[ scheme_data.subscription_scheme.key ] = scheme_data.subscription_scheme;
					} );
				}

				return schemes;
			},

			// Toggles the scheme options container when applicable.
			maybe_toggle_options: function( now ) {

				now = typeof now === 'undefined' ? false : now;

				var view     = this,
					duration = now ? 0 : 200;

				if ( view.$el_content.data( 'animating' ) === true ) {
					return false;
				}

				if ( view.$el_content.hasClass( 'closed' ) ) {

					if ( view.model.get_schemes_count() < 2 ) {
						return true;
					}

					view.$el_content.removeClass( 'closed' ).addClass( 'open' );

					if ( now ) {

						view.$el_options.show();

					} else {

						view.$el_content.data( 'animating', true );

						setTimeout( function() {
							view.$el_options.slideDown( { duration: duration, queue: false, always: function() {
								view.$el_content.data( 'animating', false );
							} } );
						}, 10 );

					}

				} else {

					if ( now ) {

						view.$el_options.hide();

					} else {

						view.$el_content.data( 'animating', true );

						setTimeout( function() {
							view.$el_options.slideUp( { duration: duration, queue: false, always: function() {
								view.$el_content.data( 'animating', false );
							} } );
						}, 10 );
					}

					view.$el_content.removeClass( 'open' ).addClass( 'closed' );
				}

				return true;
			},

			// True if the schemes wrapper is visible.
			schemes_visible: function() {
				return this.$el_content.hasClass( 'open' );
			},

			// Switches form button text to "Sign up now" when choosing a subscription plan.
			update_button_text: function() {

				if ( this.model.get_active_scheme_key() ) {
					if ( ! this.model.get_last_active_scheme_key() && this.sign_up_button_text ) {
						this.$el_button.text( this.sign_up_button_text );
					}
				} else {
					if ( this.model.get_last_active_scheme_key() ) {
						this.$el_button.text( this.add_to_cart_button_text );
					}
				}
			},

			// Initializes everything.
			initialize: function( options ) {

				this.variation_options_render_location = options.$el_content.hasClass( 'wcsatt-options-wrapper--variation' ) ? 'before_add_to_cart_button' : 'price_html';

				this.$el_content  = options.$el_content;
				this.$el_options  = options.$el_content.find( '.wcsatt-options-product-wrapper' );
				this.$el_prompt   = options.$el_content.find( '.wcsatt-options-product-prompt' );
				this.$el_dropdown = options.$el_content.find( '.wcsatt-options-product-dropdown' );

				this.model.set_schemes( this.find_schemes() );

				// Setup on first run only.

				if ( options.product ) {
					this.product                 = options.product;
					this.$el_button              = options.product.$form.find( '.single_add_to_cart_button' ).last();
					this.add_to_cart_button_text = this.$el_button.text();
					this.listenTo( this.model, 'change:active_scheme_key', this.update_button_text );
				}

				if ( this.has_schemes() ) {

					// Button text updates.

					this.sign_up_button_text = this.$el_content.data( 'sign_up_text' );

					// Maintain the selected scheme between variation changes.

					var $active_scheme_option = this.$el_option_inputs.filter( '[value="' + this.model.get_active_scheme_key() + '"]' );

					if ( $active_scheme_option.length > 0 ) {
						$active_scheme_option.prop( 'checked', true );
					} else {
						this.$el_option_inputs.filter( ':checked' ).change();
					}

					// Ensure container is hidden if there's nothing to choose.

					if ( this.$el_option_inputs.length <= 1 ) {
						this.$el_content.hide();
					}

					// Initialize prompt.

					if ( this.has_dropdown() ) {

						// Initialize open/closed state.
						if ( ! this.schemes_visible() ) {
							this.$el_options.hide();
						} else {
							this.$el_options.show();
						}

						// Initialize prompt + dropdown selection state.
						if ( this.model.get_active_scheme_key() ) {

							if ( ! this.schemes_visible() ) {
								this.maybe_toggle_options( true );
							}

							if ( this.has_prompt( 'checkbox' ) ) {
								this.$el_prompt.find( '.wcsatt-options-prompt-action-input' ).prop( 'checked', true );
							} else if ( this.has_prompt( 'radio' ) ) {
								this.$el_prompt.find( '.wcsatt-options-prompt-action-input[value="yes"]' ).prop( 'checked', true );
							}

							this.$el_dropdown.val( this.model.get_active_scheme_key() );

						} else if ( ! this.model.get_active_scheme_key() ) {

							if ( this.schemes_visible() ) {
								this.maybe_toggle_options( true );
							}

							if ( this.has_prompt( 'checkbox' ) ) {
								this.$el_prompt.find( '.wcsatt-options-prompt-action-input' ).prop( 'checked', false );
							} else if ( this.has_prompt( 'radio' ) ) {
								this.$el_prompt.find( '.wcsatt-options-prompt-action-input[value="no"]' ).prop( 'checked', true );
							}
						}
					}

					/*
					 * Fix Chrome back button bug: See https://github.com/somewherewarm/woocommerce-all-products-for-subscriptions/issues/179
					 */
					if ( this.has_dropdown() ) {

						var view = this;

						setTimeout( function() {

							if ( view.has_prompt( 'checkbox' ) ) {
								view.$el_prompt.find( '.wcsatt-options-prompt-action-input' ).change();
							} else {
								view.$el_prompt.find( '.wcsatt-options-prompt-action-input' ).filter( ':checked' ).change();
							}

						}, 10 );
					}

				} else if ( ! this.variation_selected() ) {
					this.model.set_active_scheme( false );
				} else {
					this.model.set_active_scheme( null );
				}
			}

		} );

		var obj = new View( opts );
		return obj;
	};

	// Add-to-subscription model.
	var Matching_Subscriptions_Model = function( opts ) {

		var Model = Backbone.Model.extend( {

			product: false,
			xhr: false,

			cached_responses: {},

			set_scheme_key: function( scheme_key_to_set ) {
				this.set( { scheme_key: scheme_key_to_set } );
			},

			get_scheme_key: function() {
				return this.get( 'scheme_key' );
			},

			get_matching_subscriptions_html: function() {

				var model             = this,
					active_scheme_key = this.product.schemes_model.get_active_scheme_key();

				if ( this.xhr ) {
					this.xhr.abort();
				}

				active_scheme_key = false === active_scheme_key ? '0' : active_scheme_key;

				if ( typeof this.cached_responses[ active_scheme_key ] !== 'undefined' ) {

					model.set( { html: this.cached_responses[ active_scheme_key ] } );
					model.trigger( 'matching_subscriptions_loaded' );

				} else {

					var data = {
						action:              'wcsatt_load_subscriptions_matching_product',
						product_id:          this.product.get_product_id(),
						subscription_scheme: active_scheme_key
					};

					// Get matching subscriptions list via ajax.
					this.xhr = $.post( wcsatt_single_product_params.wc_ajax_url.toString().replace( '%%endpoint%%', data.action ), data, function( response ) {

						if ( 'success' === response.result ) {
							model.set( { html: response.html } );
							model.cached_responses[ data.subscription_scheme ] = response.html;
						} else {
							model.set( { html: false } );
							model.attributes.scheme_key = false;
						}

						model.trigger( 'matching_subscriptions_loaded' );

					} );
				}
			},

			// Active scheme changed.
			active_scheme_changed: function() {

				if ( this.xhr ) {
					this.xhr.abort();
				}
			},

			initialize: function( options ) {

				this.product = options.product;

				var params = {
					scheme_key: '',
					html:       false
				};

				this.set( params );

				this.listenTo( this.product.schemes_model, 'change:active_scheme_key', this.active_scheme_changed );
				this.on( 'change:scheme_key', this.get_matching_subscriptions_html );
			}

		} );

		var obj = new Model( opts );
		return obj;
	};

	// Add-to-subscription view.
	var Matching_Subscriptions_View = function( opts ) {

		var View = Backbone.View.extend( {

			$el_content: false,

			product: false,

			block_params: {
				message:    null,
				fadeIn:     0,
				fadeOut:    0,
				overlayCSS: {
					background: 'rgba( 255, 255, 255, 0 )',
					opacity:    1,
				}
			},

			events: {
				'click .wcsatt-add-to-subscription-action-input': 'action_link_clicked'
			},

			// 'Add to subscription' link 'click' event handler.
			action_link_clicked: function() {

				var model         = this.model,
					view          = this,
					state_changed = false;

				if ( ! this.matching_subscriptions_visible() ) {

					if ( this.model.get_scheme_key() === this.product.schemes_model.get_active_scheme_key() ) {
						state_changed = this.toggle();
					} else {
						state_changed = true;
						this.$el.block( this.block_params );
						setTimeout( function() {
							model.set_scheme_key( view.product.schemes_model.get_active_scheme_key() );
						}, 200 );
					}

				} else {
					state_changed = this.toggle();
				}

				if ( ! state_changed ) {
					return false;
				}
			},

			// Active scheme changed.
			active_scheme_changed: function() {

				var view         = this,
					update_model = true;

				if ( false === this.product.schemes_model.get_active_scheme_key() || this.product.schemes_model.is_active_scheme_prorated() ) {
					update_model = false;
				}

				if ( update_model ) {

					if ( view.$el.hasClass( 'open' ) && view.model.get_scheme_key() !== view.product.schemes_model.get_active_scheme_key() ) {

						view.$el.block( view.block_params );

						if ( false === view.product.schemes_model.get_last_active_scheme_key() || this.product.schemes_model.was_last_active_scheme_prorated() ) {
							view.toggle( true );
						}

						setTimeout( function() {
							view.model.set_scheme_key( view.product.schemes_model.get_active_scheme_key() );
						}, 250 );
					}

					setTimeout( function() {
						view.$el.slideDown( 200 ).removeClass( 'inactive' ).addClass( 'active' );
					}, 50 );

				} else {
					this.$el.slideUp( 200 ).removeClass( 'active' ).addClass( 'inactive' );
				}
			},

			// Handles add-to-subscription button clicks.
			add_to_subscription_button_clicked: function( event ) {

				var $add_to_cart_button = event.data.view.product.$form.find( '.single_add_to_cart_button' ).last();

				// Trigger JS notice.
				if ( $add_to_cart_button.hasClass( 'disabled' ) ) {
					$add_to_cart_button.click();
					return false;
				}
			},

			// Toggles the matching subscriptions content wrapper.
			toggle: function( now ) {

				now = typeof now === 'undefined' ? false : now;

				var view     = this,
					duration = now ? 0 : 200;

				if ( view.$el.data( 'animating' ) === true ) {
					return false;
				}

				if ( view.$el.hasClass( 'closed' ) ) {

					setTimeout( function() {
						view.$el_content.slideDown( { duration: duration, queue: false, always: function() {
							view.$el.data( 'animating', false );
						} } );
					}, 10 );

					view.$el.removeClass( 'closed' ).addClass( 'open' );
					view.$el.data( 'animating', true );

				} else {

					setTimeout( function() {
						view.$el_content.slideUp( { duration: duration, queue: false, always: function() {
							view.$el.data( 'animating', false );
						} } );
					}, 10 );

					view.$el.removeClass( 'open' ).addClass( 'closed' );
					view.$el.data( 'animating', true );
				}

				return true;
			},

			// True if the matching subscriptions select wrapper is visible.
			matching_subscriptions_visible: function() {
				return this.$el_content.is( ':visible' );
			},

			// New subscriptions list loaded?
			matching_subscriptions_loaded: function() {
				this.render();
			},

			// Render the subscriptions selector.
			render: function() {

				var html = this.model.get( 'html' );

				this.$el.unblock();

				if ( false === html ) {

					window.alert( wcsatt_single_product_params.i18n_subs_load_error );

					if ( this.matching_subscriptions_visible() ) {
						this.toggle();
					}

					this.$el.find( 'input.wcsatt-add-to-subscription-action-input' ).prop( 'checked', false );

				} else {

					this.$el_content.html( html );

					if ( ! this.matching_subscriptions_visible() ) {
						this.toggle();
					}
				}
			},

			initialize: function( options ) {

				this.product     = options.product;
				this.$el_content = options.$el_content;

				this.listenTo( this.model, 'matching_subscriptions_loaded', this.matching_subscriptions_loaded );
				this.listenTo( this.product.schemes_model, 'change:active_scheme_key', this.active_scheme_changed );

				this.$el_content.on( 'click', '.wcsatt-add-to-subscription-button', { view: this }, this.add_to_subscription_button_clicked );
			}

		} );

		var obj = new View( opts );
		return obj;
	};

	// SATT Product object.
	var SATT_Product = function( $product_form ) {

		this.$form = $product_form;

		this.schemes_model = false;
		this.schemes_view  = false;

		this.matching_subscriptions_model = false;
		this.matching_subscriptions_view  = false;

		this.initialize = function() {

			this.schemes_model                = new Schemes_Model( { product: this } );
			this.matching_subscriptions_model = new Matching_Subscriptions_Model( { product: this } );

			this.schemes_view                = new Schemes_View( { product: this, model: this.schemes_model, el: $product_form, $el_content: $product_form.find( '.wcsatt-options-wrapper' ) } );
			this.matching_subscriptions_view = new Matching_Subscriptions_View( { product: this, model: this.matching_subscriptions_model, el: $product_form.find( '.wcsatt-add-to-subscription-wrapper' ), $el_content: $product_form.find( '.wcsatt-add-to-subscription-options' ) } );

			// Simple switching fix for https://github.com/woocommerce/woocommerce/commit/3340d5c7cc78d0a254dfed4c2c7f6f0d5645c8ba#diff-cb560f318dd3126e27d8499b80e71027
			if ( window.location.href.indexOf( 'switch-subscription' ) != -1 && window.location.href.indexOf( 'item' ) != -1 ) {
				$product_form.prop( 'action', '' );
			}

			// Ensure the one-time option is submitted correctly when the prompt is visible.
			$product_form.on( 'submit', { view: this.schemes_view }, this.form_submitted );

			// PAO integration.
			this.pao = new PAO_Integration( this );
		};

		this.get_product_id = function() {
			return $product_form.find( '.wcsatt-add-to-subscription-wrapper' ).data( 'product_id' );
		};

		this.form_submitted = function( e ) {

			var view = e.data.view;

			if ( 'one-time' === view.get_prompt_val() ) {
				view.$el_option_inputs.filter( '[value="0"]' ).prop( 'checked', true );
			}
		};

		/**
		 * Rounds price values according to WC settings.
		 */
		this.round_number = function( number, precision ) {

			var factor            = Math.pow( 10, precision ),
				tempNumber        = number * factor,
				roundedTempNumber = Math.round( tempNumber );

			return roundedTempNumber / factor;
		};

	};

	// Product Add-Ons integration.
	var PAO_Integration = function( product ) {

		var self = this;

		this.$addons_totals        = false;
		this.$discount_addons_data = product.$form.find( '.wcsatt-pao-data' );

		this.initialize = function() {

			if ( ! product.$form.hasClass( 'bundle_form' ) && ! product.$form.hasClass( 'composite_form' ) ) {

				var $addons_totals = product.$form.find( '#product-addons-total' );

				if ( $addons_totals.length > 0 && 1 == $addons_totals.data( 'show-sub-total' ) ) {

					var discount_addons = 'yes' === this.$discount_addons_data.data( 'discount_addons' );

					this.$addons_totals = $addons_totals;

					// Move subscription options after Add-Ons totals.
					if ( discount_addons ) {
						product.schemes_view.$el_content.insertAfter( $addons_totals );
						$addons_totals.addClass( 'subscription-details-hidden' );
					// Move subscription options before Add-Ons.
					} else {
						product.schemes_view.$el_content.insertBefore( product.$form.find( '.wc-pao-addon-container' ).first() );
					}

					if ( ! discount_addons ) {
						// Listen to scheme changes.
						product.schemes_model.on( 'change:active_scheme_key', this.active_scheme_changed );
						// Listen to variation changes.
						product.$form.on( 'show_variation', this.variation_changed );
					}

					if ( product.schemes_model.get_active_scheme_key() ) {
						this.active_scheme_changed();
					}
				}
			}
		};

		this.active_scheme_changed = function() {

			var active_scheme_key     = false === product.schemes_model.get_active_scheme_key() ? '0' : product.schemes_model.get_active_scheme_key(),
				$active_scheme_option = product.schemes_view.$el_options.find( 'input[value="' + active_scheme_key + '"]' ),
				active_scheme_data    = $active_scheme_option.data( 'custom_data' ),
				display_price         = active_scheme_data ? active_scheme_data.display_price : false,
				raw_price             = active_scheme_data ? active_scheme_data.raw_price : false;

			if ( display_price !== false && raw_price !== false ) {
				self.$addons_totals.data( 'price', display_price );
				self.$addons_totals.data( 'raw-price', raw_price );
				product.$form.trigger( 'woocommerce-product-addons-update' );
			}

		};

		this.variation_changed = function() {
			self.active_scheme_changed();
		};

		this.initialize();
	};

	// Product Bundles integration.
	var PB_Integration = function( bundle ) {

		var self = this,
			satt = bundle.$bundle_form.data( 'satt_script' );

		// Moves SATT options after the price.
		this.initialize_ui = function() {

			if ( satt.schemes_view.$el_content.length > 0 ) {
				if ( bundle.$addons_totals !== false ) {
					bundle.$addons_totals.after( satt.schemes_view.$el_content );
				} else {
					bundle.$bundle_price.after( satt.schemes_view.$el_content );
				}
			}
		};

		// Scans for SATT schemes attached on the Bundle.
		this.initialize_schemes = function() {

			bundle.satt_schemes = [];

			// Store scheme data for options that override the default prices.
			var $scheme_options = satt.schemes_view.$el_option_items.filter( '.subscription-option' );

			$scheme_options.each( function() {

				var $scheme_option = $( this ),
					scheme_data    = $scheme_option.find( 'input' ).data( 'custom_data' );

				bundle.satt_schemes.push( {
					$el:  $scheme_option,
					data: scheme_data
				} );

			} );
		};

		// Init.
		this.integrate = function() {

			if ( satt.schemes_view.has_schemes() ) {

				self.initialize_ui();
				self.initialize_schemes();

				if ( bundle.satt_schemes.length > 0 ) {
					bundle.$bundle_data.on( 'woocommerce-product-bundle-updated-totals', self.update_subscription_totals );
					bundle.$bundle_data.on( 'woocommerce-product-bundle-validation-status-changed', self.maybe_hide_subscription_options );
				}
			}
		};

		this.has_single_forced_susbcription = function() {
			return bundle.satt_schemes.length === 1 && satt.schemes_view.$el_option_items.filter( '.one-time-option' ).length === 0;
		};

		// Hide subscription options?
		this.maybe_hide_subscription_options = function() {

			if ( bundle.passes_validation() ) {
				if ( ! self.has_single_forced_susbcription() ) {
					satt.schemes_view.$el_content.slideDown( 200 );
				}
			} else {
				satt.schemes_view.$el_content.slideUp( 200 );
			}
		};

		// Update totals displayed in SATT options.
		this.update_subscription_totals = function( event, bundle ) {

			if ( ! bundle.passes_validation() ) {
				return;
			}

			var bundle_price_html       = bundle.get_price_html(),
				bundle_price_inner_html = $( bundle_price_html ).html();

			// If only a single option is present, then bundle prices are already overridden on the server side.
			// In this case, simply grab the subscription details from the option and append them to the bundle price string.
			if ( self.has_single_forced_susbcription() ) {

				bundle.$bundle_price.find( '.price' ).html( bundle.satt_schemes[0].data.option_details_html.replace( '%p', bundle_price_inner_html ) );

			/*
			 * If multiple options are present, then:
			 * - Calculate the subscription price for each option that overrides default prices and update its html string.
			 * - Update the base price plan displayed in the prompt.
			 */
			} else {

				$.each( bundle.satt_schemes, function( index, scheme ) {

					// Do we need to update any prices?
					if ( scheme.data.option_has_price || satt.schemes_view.has_prompt( 'radio' ) || satt.schemes_view.has_prompt( 'checkbox' ) ) {

						var scheme_price_data       = $.extend( true, {}, bundle.price_data ),
							scheme_price_html       = bundle_price_html,
							scheme_price_inner_html = bundle_price_inner_html;

						// Does the current scheme modify prices in any way? If yes, calculate new totals.
						if ( scheme.data.subscription_scheme.has_price_filter ) {

							if ( scheme.data.subscription_scheme.pricing_mode === 'inherit' && scheme.data.subscription_scheme.discount > 0 ) {

								$.each( bundle.bundled_items, function( index, bundled_item ) {

									var bundled_item_id = bundled_item.bundled_item_id;

									if ( scheme.data.discount_from_regular ) {
										scheme_price_data.prices[ bundled_item_id ] = scheme_price_data.regular_prices[ bundled_item_id ] * ( 1 - scheme.data.subscription_scheme.discount / 100 );
									} else {
										scheme_price_data.prices[ bundled_item_id ] = scheme_price_data.prices[ bundled_item_id ] * ( 1 - scheme.data.subscription_scheme.discount / 100 );
									}

									scheme_price_data.addons_prices[ bundled_item_id ] = scheme_price_data.addons_prices[ bundled_item_id ] * ( 1 - scheme.data.subscription_scheme.discount / 100 );

								} );

								scheme_price_data.base_price = scheme_price_data.base_price * ( 1 - scheme.data.subscription_scheme.discount / 100 );

								var addons_raw_price = bundle.get_addons_raw_price();

								scheme_price_data.addons_regular_price = bundle.has_addons() && 'yes' === satt.pao.$discount_addons_data.data( 'discount_addons' ) ? addons_raw_price : 0;
								scheme_price_data.addons_price         = bundle.has_addons() && 'yes' === satt.pao.$discount_addons_data.data( 'discount_addons' ) ? addons_raw_price * ( 1 - scheme.data.subscription_scheme.discount / 100 ) : 0;

							} else if ( scheme.data.subscription_scheme.pricing_mode === 'override' ) {

								scheme_price_data.base_regular_price = Number( scheme.data.subscription_scheme.regular_price );
								scheme_price_data.base_price         = Number( scheme.data.subscription_scheme.price );
							}

							scheme_price_data = bundle.calculate_subtotals( false, scheme_price_data );
							scheme_price_data = bundle.calculate_totals( scheme_price_data );

							scheme_price_html       = bundle.get_price_html( scheme_price_data );
							scheme_price_inner_html = $( scheme_price_html ).html();
						}

						var $option_price       = scheme.$el.find( '.subscription-price' ),
							option_scheme_price = scheme.data.option_details_html.replace( '%p', scheme_price_inner_html );

						// Update prompt.
						if ( scheme.data.subscription_scheme.is_base && ( satt.schemes_view.has_prompt( 'radio' ) || satt.schemes_view.has_prompt( 'checkbox' ) ) ) {

							var $prompt_input = satt.schemes_view.$el_prompt.find( '.wcsatt-options-prompt-action-input[value="yes"]' ),
								$prompt       = $prompt_input.closest( '.wcsatt-options-prompt-label' ).find( '.wcsatt-options-prompt-action' );

							// If the prompt doesn't contain anything to update, move on.
							if ( $prompt.find( '.subscription-price' ).length > 0 ) {
								$prompt.html( scheme.data.prompt_details_html.replace( '%p', scheme_price_inner_html ) ).find( 'span.total' ).remove();
							}
						}

						// Update plan.
						if ( scheme.data.option_has_price ) {

							$option_price.html( option_scheme_price ).find( 'span.total' ).remove();

							if ( satt.schemes_view.has_dropdown() ) {

								var dropdown_price = wc_pb_price_format( scheme_price_data.totals.price, true ),
									discount       = '';

								dropdown_price = scheme.data.dropdown_format.replace( '%p', dropdown_price );

								if ( scheme.data.subscription_scheme.has_price_filter ) {
									if ( scheme.data.subscription_scheme.pricing_mode === 'inherit' && scheme.data.subscription_scheme.discount > 0 ) {

										discount       = satt.round_number( scheme.data.subscription_scheme.discount, scheme.data.dropdown_discount_decimals );
										dropdown_price = scheme.data.dropdown_discounted_format.replace( '%d', discount ).replace( '%p', dropdown_price );

									} else if ( scheme_price_data.totals.regular_price > scheme_price_data.totals.price ) {

										var dropdown_regular_price = wc_pb_price_format( scheme_price_data.totals.regular_price, true );

										dropdown_price = scheme.data.dropdown_sale_format.replace( '%r', dropdown_regular_price ).replace( '%p', dropdown_price );
									}
								}

								satt.schemes_view.$el_dropdown.find( 'option[value=' + scheme.data.subscription_scheme.key + ']' ).text( dropdown_price );
							}
						}

						$option_price.trigger( 'wcsatt-updated-bundle-price', [ scheme_price_html, scheme, bundle, self ] );
					}

				} );
			}

		};

		// Lights on.
		if ( satt ) {
			this.integrate();
		}
	};

	var CP_Integration = function( composite ) {

		var self = this,
			satt = composite.$composite_form.data( 'satt_script' );

		// Moves SATT options after the price.
		this.initialize_ui = function() {

			if ( satt.schemes_view.$el_content.length > 0 ) {
				if ( composite.composite_price_view.$addons_totals !== false ) {
					composite.composite_price_view.$addons_totals.after( satt.schemes_view.$el_content );
				} else {
					composite.$composite_price.after( satt.schemes_view.$el_content );
				}
			}
		};

		// Scans for SATT schemes attached on the Composite.
		this.initialize_schemes = function() {

			composite.satt_schemes = [];

			// Store scheme data for options that override the default prices.
			var $scheme_options = satt.schemes_view.$el_option_items.filter( '.subscription-option' );

			$scheme_options.each( function() {

				var $scheme_option = $( this ),
					scheme_data    = $scheme_option.find( 'input' ).data( 'custom_data' );

				composite.satt_schemes.push( {
					$el:  $scheme_option,
					data: scheme_data,
				} );

			} );
		};

		// Init.
		this.integrate = function() {

			if ( satt.schemes_view.has_schemes() ) {

				self.initialize_schemes();

				composite.actions.add_action( 'initialize_composite', function() {

					self.initialize_ui();

					if ( composite.satt_schemes.length > 0 ) {

						if ( self.has_single_forced_susbcription() ) {
							composite.filters.add_filter( 'composite_price_html', self.filter_price_html, 10, self );
						} else {
							composite.actions.add_action( 'composite_totals_changed', self.update_subscription_totals, 101, self );
							composite.actions.add_action( 'composite_validation_status_changed', self.maybe_hide_subscription_options, 101, self );
							composite.actions.add_action( 'composite_initialized', self.maybe_hide_subscription_options, 101, self );
						}
					}

				}, 51, this );
			}
		};

		this.has_single_forced_susbcription = function() {
			return composite.satt_schemes.length === 1 && satt.schemes_view.$el_option_items.filter( '.one-time-option' ).length === 0;
		};

		// Hide subscription options?
		this.maybe_hide_subscription_options = function() {

			if ( 'pass' === composite.api.get_composite_validation_status() ) {
				if ( ! self.has_single_forced_susbcription() ) {
					satt.schemes_view.$el_content.slideDown( 200 );
				}
			} else {
				satt.schemes_view.$el_content.slideUp( 200 );
			}
		};

		// If only a single option is present, then composite prices are already overridden on the server side.
		// In this case, simply grab the subscription details from the option and append them to the composite price string.
		this.filter_price_html = function( price, view, price_data ) {

			var $price         = $( price ),
				price_inner    = $price.html(),
				scheme_details = composite.satt_schemes[0].data.option_details_html.replace( '%p', price_inner );

			price = '<p class="price">' + scheme_details + '</p>';

			return price;
		};

		// Update totals displayed in SATT options.
		this.update_subscription_totals = function() {

			if ( 'pass' !== composite.api.get_composite_validation_status() ) {
				return;
			}

			var composite_price_html       = composite.composite_price_view.get_price_html(),
				composite_totals           = composite.data_model.calculate_totals(),
				composite_price_inner_html = $( composite_price_html ).html();

			$.each( composite.satt_schemes, function( index, scheme ) {

				// Do we need to update any prices?
				if ( scheme.data.option_has_price || satt.schemes_view.has_prompt( 'radio' ) || satt.schemes_view.has_prompt( 'checkbox' ) ) {

					var scheme_price_data       = $.extend( true, {}, composite.data_model.price_data ),
						scheme_price_html       = composite_price_html,
						scheme_price_inner_html = composite_price_inner_html,
						scheme_price_totals     = composite_totals;

					// Does the current scheme modify prices in any way? If yes, calculate new totals.
					if ( scheme.data.subscription_scheme.has_price_filter ) {

						self.maybe_add_bundle_totals_filters( scheme );

						if ( scheme.data.subscription_scheme.pricing_mode === 'inherit' && scheme.data.subscription_scheme.discount > 0 ) {

							$.each( composite.get_components(), function( index, component ) {

								var component_id = component.component_id;

								if ( scheme.data.discount_from_regular ) {
									scheme_price_data.prices[ component_id ] = scheme_price_data.regular_prices[ component_id ] * ( 1 - scheme.data.subscription_scheme.discount / 100 );
								} else {
									scheme_price_data.prices[ component_id ] = scheme_price_data.prices[ component_id ] * ( 1 - scheme.data.subscription_scheme.discount / 100 );
								}

								scheme_price_data.addons_prices[ component_id ] = scheme_price_data.addons_prices[ component_id ] * ( 1 - scheme.data.subscription_scheme.discount / 100 );

							} );

							scheme_price_data.base_price = scheme_price_data.base_price * ( 1 - scheme.data.subscription_scheme.discount / 100 );

							var addons_raw_price = composite.composite_price_view.get_addons_raw_price();

							scheme_price_data.addons_regular_price = composite.composite_price_view.has_addons() && 'yes' === satt.pao.$discount_addons_data.data( 'discount_addons' ) ? addons_raw_price : 0;
							scheme_price_data.addons_price         = composite.composite_price_view.has_addons() && 'yes' === satt.pao.$discount_addons_data.data( 'discount_addons' ) ? addons_raw_price * ( 1 - scheme.data.subscription_scheme.discount / 100 ) : 0;

						} else if ( scheme.data.subscription_scheme.pricing_mode === 'override' ) {

							scheme_price_data.base_regular_price = Number( scheme.data.subscription_scheme.regular_price );
							scheme_price_data.base_price         = Number( scheme.data.subscription_scheme.price );
						}

						scheme_price_data = composite.data_model.calculate_subtotals( false, scheme_price_data );

						self.maybe_remove_bundle_totals_filters( scheme );

						scheme_price_totals = composite.data_model.calculate_totals( scheme_price_data );

						scheme_price_data.totals = scheme_price_totals;
						scheme_price_html        = composite.composite_price_view.get_price_html( scheme_price_data );
						scheme_price_inner_html  = $( scheme_price_html ).html();
					}

					var $option_price       = scheme.$el.find( '.subscription-price' ),
						option_scheme_price = scheme.data.option_details_html.replace( '%p', scheme_price_inner_html );

					// Update prompt.
					if ( scheme.data.subscription_scheme.is_base && ( satt.schemes_view.has_prompt( 'radio' ) || satt.schemes_view.has_prompt( 'checkbox' ) ) ) {

						var $prompt_input = satt.schemes_view.$el_prompt.find( '.wcsatt-options-prompt-action-input[value="yes"]' ),
							$prompt       = $prompt_input.closest( '.wcsatt-options-prompt-label' ).find( '.wcsatt-options-prompt-action' );

						// If the prompt doesn't contain anything to update, move on.
						if ( $prompt.find( '.subscription-price' ).length > 0 ) {
							$prompt.html( scheme.data.prompt_details_html.replace( '%p', scheme_price_inner_html ) ).find( 'span.total' ).remove();
						}
					}

					// Update plan.
					if ( scheme.data.option_has_price ) {

						$option_price.html( option_scheme_price ).find( 'span.total' ).remove();

						if ( satt.schemes_view.has_dropdown() ) {

							var dropdown_price = wc_cp_price_format( scheme_price_totals.price, true ),
								discount       = '';

							dropdown_price = scheme.data.dropdown_format.replace( '%p', dropdown_price );

							if ( scheme.data.subscription_scheme.has_price_filter ) {
								if ( scheme.data.subscription_scheme.pricing_mode === 'inherit' && scheme.data.subscription_scheme.discount > 0 ) {

									discount       = satt.round_number( scheme.data.subscription_scheme.discount, scheme.data.dropdown_discount_decimals );
									dropdown_price = scheme.data.dropdown_discounted_format.replace( '%d', discount ).replace( '%p', dropdown_price );

								} else if ( scheme_price_data.totals.regular_price > scheme_price_data.totals.price ) {

									var dropdown_regular_price = wc_cp_price_format( scheme_price_data.totals.regular_price, true );

									dropdown_price = scheme.data.dropdown_sale_format.replace( '%r', dropdown_regular_price ).replace( '%p', dropdown_price );
								}
							}

							satt.schemes_view.$el_dropdown.find( 'option[value=' + scheme.data.subscription_scheme.key + ']' ).text( dropdown_price );
						}
					}

					$option_price.trigger( 'wcsatt-updated-composite-price', [ scheme_price_html, scheme, composite, self ] );
				}

			} );
		};

		this.maybe_add_bundle_totals_filters = function( scheme ) {

			if ( scheme.data.subscription_scheme.pricing_mode === 'inherit' && scheme.data.subscription_scheme.discount > 0 ) {

				$.each( composite.get_components(), function( index, component ) {

					var product_type = component.get_selected_product_type();

					if ( 'bundle' === product_type ) {

						var bundle = component.get_bundle_script();

						if ( bundle ) {
							bundle.satt_scheme = scheme;
							bundle.filters.add_filter( 'bundle_totals', self.filter_bundle_totals, 10, self );
						}
					}

				} );
			}
		};

		this.maybe_remove_bundle_totals_filters = function( scheme ) {

			if ( scheme.data.subscription_scheme.pricing_mode === 'inherit' && scheme.data.subscription_scheme.discount > 0 ) {

				$.each( composite.get_components(), function( index, component ) {

					var product_type = component.get_selected_product_type();

					if ( 'bundle' === product_type ) {

						var bundle = component.get_bundle_script();

						if ( bundle ) {
							bundle.satt_scheme = false;
							bundle.filters.remove_filter( 'bundle_totals', self.filter_bundle_totals );
						}
					}

				} );
			}
		};

		this.filter_bundle_totals = function( totals, bundle_price_data, bundle, qty ) {

			if ( ! bundle.satt_scheme ) {
				return totals;
			}

			var scheme_data = bundle.satt_scheme.data,
				discount    = scheme_data.subscription_scheme.discount,
				price_data  = $.extend( true, {}, bundle_price_data );

			qty = typeof( qty ) === 'undefined' ? bundle.composite_data.component.get_selected_quantity() : qty;

			$.each( bundle.bundled_items, function( index, bundled_item ) {
				if ( scheme_data.discount_from_regular ) {
					price_data.prices[ bundled_item.bundled_item_id ] = price_data.regular_prices[ bundled_item.bundled_item_id ] * ( 1 - discount / 100 );
				} else {
					price_data.prices[ bundled_item.bundled_item_id ] = price_data.prices[ bundled_item.bundled_item_id ] * ( 1 - discount / 100 );
				}
			} );

			if ( price_data.base_price ) {
				if ( scheme_data.discount_from_regular ) {
					price_data.base_price = Number( price_data.base_regular_price ) * ( 1 - discount / 100 );
				} else {
					price_data.base_price = Number( price_data.base_price ) * ( 1 - discount / 100 );
				}
			}

			// Prevent infinite loop.
			bundle.satt_scheme = false;

			price_data = bundle.calculate_subtotals( false, price_data, qty );
			price_data = bundle.calculate_totals( price_data );

			totals = price_data.totals;


			return totals;
		};

		// Lights on.
		this.integrate();
	};

	/*
	 * Initialization.
	 */

	function initialize() {
		$( '.product form.cart' ).each( function() {
			maybe_initialize_form( $( this ) );
		} );
	}

	function maybe_initialize_form( $product_form ) {

		var satt_script;

		if ( ! $product_form.data( 'satt_script' ) ) {

			satt_script = new SATT_Product( $product_form );
			satt_script.initialize();

			$product_form.data( 'satt_script', satt_script );
		}
	}

	initialize();

	// Hook into Bundles.
	$( '.bundle_form .bundle_data' ).each( function() {
		$( this ).on( 'woocommerce-product-bundle-initializing', function( event, bundle ) {
			if ( ! bundle.is_composited() ) {
				new PB_Integration( bundle );
			}
		} );
	} );

	// Hook into Composites.
	$( '.composite_form .composite_data' ).each( function() {
		$( this ).on( 'wc-composite-initializing', function( event, composite ) {
			new CP_Integration( composite );
		} );
	} );

	// Allow third-party code to initialize SATT.
	$( document.body ).on( 'wcsatt-initialize', function() {
		initialize();
	} );

	// Allow third-party code to initialize SATT on custom containers.
	$.fn.wcsatt_initialize = function() {
		$( this ).find( '.product form.cart' ).each( function() {
			maybe_initialize_form( $( this ) );
		} );
	};

} ) ( jQuery );
