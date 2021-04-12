jQuery( function( $ ) {

	var $product_type_select       = $( 'select#product-type' ),
		$components_panel          = $( '#bto_product_data' ),
		$composite_price_calc      = $components_panel.find( '#_bto_shop_price_calc' ),
		$components_toggle_toolbar = $components_panel.find( '.bulk_toggle_wrapper' ),
		$components_container      = $( '.config_group', $components_panel ),
		$component_metaboxes       = $( '.bto_groups', $components_container ),
		$components                = $( '.bto_group', $component_metaboxes ),
		component_add_count        = $components.length,
		component_objects          = {},

		$scenarios_panel           = $( '#bto_scenario_data' ),
		$scenarios_toggle_toolbar  = $scenarios_panel.find( '.bulk_toggle_wrapper' ),
		$scenarios_container       = $( '.scenarios_config_group', $scenarios_panel ),
		$scenario_metaboxes        = $( '.bto_scenarios', $scenarios_panel ),
		$scenarios                 = $( '.bto_scenario', $scenario_metaboxes ),
		scenario_add_count         = $scenarios.length,
		scenario_objects           = {},

		$states_panel           = $( '#bto_state_data' ),
		$states_toggle_toolbar  = $states_panel.find( '.bulk_toggle_wrapper' ),
		$states_container       = $( '.states_config_group', $states_panel ),
		$state_metaboxes        = $( '.bto_states', $states_panel ),
		$states                 = $( '.bto_state', $state_metaboxes ),
		state_add_count         = $states.length,
		state_objects           = {},

		component_data_dirty       = false,
		component_image_frame_data = {
			image_frame: false,
			$button:     false
		},

		block_params = {
			message:    null,
			overlayCSS: {
				background: '#fff',
				opacity:    0.6
			}
		},

		tip_tip_params = {
			'attribute': 'data-tip',
			'fadeIn':    50,
			'fadeOut':   50,
			'delay':     200
		},

		layout_classes = [],

		is_scenario_loading = false,
		is_state_loading    = false,

		has_states          = $states_panel.length,

		configuration_save_xhr = false;

	// Prepare layout classes.
	$.each( wc_composite_admin_params.layouts, function( index, layout ) {
		layout_classes.push( 'layout-' + layout );
	} );

	// Composite type move stock msg up.
	$( '.composite_stock_msg' ).appendTo( '._manage_stock_field .description' );

	// Hide the default "Sold Individually" field.
	$( '#_sold_individually' ).closest( '.form-field' ).addClass( 'hide_if_composite' );

	// Hide the "Grouping" field.
	$( '#linked_product_data .grouping.show_if_simple, #linked_product_data .form-field.show_if_grouped' ).addClass( 'hide_if_composite' );

	// Simple type options are valid for bundles.
	$( '.show_if_simple:not(.hide_if_composite)' ).addClass( 'show_if_composite' );

	if ( typeof woocommerce_admin_meta_boxes === 'undefined' ) {
		woocommerce_admin_meta_boxes = woocommerce_writepanel_params;
	}

	// Composite type specific options.
	$( 'body' ).on( 'woocommerce-product-type-change', function( event, select_val ) {

		if ( 'composite' === select_val ) {

			$( '.show_if_external' ).hide();
			$( '.show_if_composite' ).show();

			$( 'input#_manage_stock' ).trigger( 'change' );
		}

	} );

	// On submit, post two inputs to determine if 'max_input_vars' kicks in: One at the start of the form (control) and one at the end (test).
	$( 'form#post' ).on( 'submit', function() {

		if ( 'composite' === $product_type_select.val() ) {

			var $form        = $( this ),
			    $control_var = $( '<input type="hidden" name="cp_post_control_var" value="1"/>' ),
			    $test_var    = $( '<input type="hidden" name="cp_post_test_var" value="1"/>' );

			$form.prepend( $control_var );
			$form.append( $test_var );
		}
	} );

	// Trigger product type change.
	$product_type_select.trigger( 'change' );

	// Downloadable support.
	$( 'input#_downloadable' ).on( 'change', function() {
		$product_type_select.trigger( 'change' );
	} );

	// Save initial 'Catalog Price' value.
	$composite_price_calc.data( 'val', $composite_price_calc.val() );

	/*
	 * Handle events in Components panel.
	 */
	$components_panel

		// Ignore other click events.
		.off( 'click' )

		// Layout selection.
		.on( 'click', '.bto_layout_label', function() {

			var $option   = $( this ),
				$selected = $option.closest( '.bto_layouts' ).find( '.selected' );

			$selected.removeClass( 'selected' );
			$option.addClass( 'selected' );

			$components_container.removeClass( layout_classes.join( ' ' ) );
			$components_container.addClass( 'layout-' + $option.find( 'input' ).val() );
		} )

		// Trigger event when 'Catalog Price' option changes.
		.on( 'change', '#_bto_shop_price_calc', function() {

			var new_calc            = $( this ).val(),
				unset_default_found = false;

			// Any components with an empty default?
			if ( 'defaults' === new_calc ) {

				$.each( component_objects, function( index, component_object ) {

					if ( ! component_object.is_optional() && ! component_object.get_default_option() ) {

						unset_default_found = true;

						// Alert user.
						window.alert( wc_composite_admin_params.i18n_defaults_unset );
						// Put back the old value.
						$composite_price_calc.val( $composite_price_calc.data( 'val' ) );
						return false;
					}
				} );
			}

			if ( ! unset_default_found ) {
				$composite_price_calc.data( 'val', new_calc );
			}
		} )

		// Remove onboarding elements when adding component.
		.one( 'wc-cp-component-added', function() {
			$components_container.removeClass( 'options_group--boarding' );
		} )

		// Update component DOM elements, menu order and toolbar state.
		.on( 'wc-cp-components-changed', function() {

			$component_metaboxes = $( '.bto_groups', $components_container );
			$components          = $( '.bto_group', $component_metaboxes );

			$components.each( function( index, el ) {
				$( '.group_position', el ).val( index );
				$( el ).attr( 'rel', index );
			} );

			// Component data must be saved before viewing the Scenarios panel, or adding new scenarios.
			set_component_data_dirty_state( true );

			update_components_toolbar_state();
		} );

	/*
	 * Handle events in Scenarios panel.
	 */

	// Update scenario DOM elements, menu order and toolbar state.
	$scenarios_panel

		.off( 'click' )

		// Remove onboarding elements when adding scenario.
		.one( 'wc-cp-scenario-added', function() {
			$scenarios_container.removeClass( 'options_group--boarding' );
		} )

		// Update toolbar state.
		.on( 'wc-cp-scenarios-changed', function() {

			$scenario_metaboxes = $( '.bto_scenarios', $scenarios_panel );
			$scenarios          = $( '.bto_scenario', $scenario_metaboxes );

			$scenarios.each( function( index, el ) {
				$( '.scenario_position', el ).val( index );
				$( el ).attr( 'rel', index );
			} );

			update_scenarios_toolbar_state();
		} );

	/*
	 * Handle events in States panel.
	 */

	// Update scenario DOM elements, menu order and toolbar state.

	if ( has_states ) {

		$states_panel

			.off( 'click' )

			// Remove onboarding elements when adding state.
			.one( 'wc-cp-state-added', function() {
				$states_container.removeClass( 'options_group--boarding' );
			} )

			// Update toolbar state.
			.on( 'wc-cp-states-changed', function() {

				$state_metaboxes = $( '.bto_states', $states_panel );
				$states          = $( '.bto_state', $state_metaboxes );

				$states.each( function( index, el ) {
					$( '.state_position', el ).val( index );
					$( el ).attr( 'rel', index );
				} );

				update_states_toolbar_state();
			} );
	}

	/*------------------------------------------*/
	/*  Components                              */
	/*------------------------------------------*/

	function Component( $el ) {

		var self = this;

		this.$el                          = $el;
		this.$content                     = $el.find( '.bto_group_data' );
		this.$metabox_title               = $el.find( 'h3 .group_name' );
		this.$section_links               = this.$content.find( '.subsubsub a' );
		this.$sections                    = this.$content.find( '.tab_group' );
		this.$discount                    = this.$content.find( '.group_discount' );
		this.$filters                     = this.$content.find( '.group_filters' );
		this.$display_prices              = this.$content.find( '.component_display_prices' );
		this.$pagination_style            = this.$content.find( '.component_pagination_style' );

		this.$query_type_containers       = this.$content.find( '.component_query_type_selector' );
		this.$query_type_selector         = this.$content.find( 'select.component_query_type' );

		this.$options_style_container     = this.$content.find( '.component_options_style' );
		this.$options_style_selector      = this.$content.find( 'select.options_style_selector' );

		this.$categories_selector         = this.$content.find( 'select.categories_selector' );
		this.$products_selector           = this.$content.find( 'select.products_selector' );

		this.$default_selectors_container = this.$content.find( '.default_selector_container' );
		this.$default_selectors           = this.$content.find( '.default_selector_wrapper' );

		this.$default_selector_categories = this.$content.find( 'select.default_selector_categories' );
		this.$default_selector_products   = this.$content.find( 'select.default_selector_products' );

		this.$optional_checkbox           = this.$content.find( 'input.component_optional' );

		this.$title_input                 = this.$content.find( 'input.group_title' );
		this.$priced_individually_input   = this.$content.find( '.group_priced_individually input' );

		this.$sort_filter_container       = this.$content.find( '.options_group_component--sort-filter' );
		this.$show_filters_input          = this.$content.find( '.group_show_filters input' );

		this.initialized_content          = false;

		this.component_toggled = function() {

			var	initialize = self.maybe_initialize_content(),
				delay      = initialize ? 50 : 10;

			var $el         = this.$el,
				$el_content = this.$content;

			setTimeout( function() {
				$el.toggleClass( 'closed' ).toggleClass( 'open' );
				$el_content.stop().slideToggle();
			}, delay );
		};

		this.maybe_initialize_content = function() {

			var initialize_content = false;

			if ( ! self.initialized_content ) {
				initialize_content = true;
				self.initialize_content();
			}

			return initialize_content;
		};

		this.section_changed = function( $section_link ) {

			self.$section_links.removeClass( 'current' );
			$section_link.addClass( 'current' );

			self.$sections.addClass( 'tab_group_hidden' );
			self.$content.find( '.tab_group_' + $section_link.data( 'tab' ) ).removeClass( 'tab_group_hidden' );
		};

		this.title_changed = function() {

			self.$metabox_title.text( self.$title_input.val() );

			// Component data must be saved before viewing the Scenarios panel, or adding new scenarios.
			set_component_data_dirty_state( true );
		};

		this.query_type_changed = function() {

			self.$query_type_containers.hide();
			self.$default_selectors.hide();

			var query_type = self.$query_type_selector.val();

			self.$content.find( '.component_query_type_' + query_type ).show();

			if ( this.initialized_content ) {

				self.maybe_hide_static_component_content();

				if ( 'category_ids' === query_type ) {
					self.reinitialize_default_option_category_select();
					self.maybe_update_default_option_category_ids( true );
				} else {
					self.initialize_default_option_product_select();
					self.default_option_changed();
				}

				// Component data must be saved before viewing the Scenarios panel, or adding new scenarios.
				set_component_data_dirty_state( true );
			}
		};

		this.products_changed = function() {

			self.maybe_hide_static_component_content();
			self.initialize_default_option_product_select();
			self.default_option_changed();

			// Component data must be saved before viewing the Scenarios panel, or adding new scenarios.
			set_component_data_dirty_state( true );
		};

		this.categories_changed = function() {

			var category_ids = self.get_category_ids();

			// Copy active category IDs on 'include' data attribute. Will be used when searching for a default.
			self.$default_selector_categories.data( 'include', category_ids.join() );

			// If needed, fetch the category IDs of the defult option and check if they are valid.
			self.maybe_update_default_option_category_ids( true );

			// Component data must be saved before viewing the Scenarios panel, or adding new scenarios.
			set_component_data_dirty_state( true );
		};

		this.optional_changed = function() {

			self.maybe_hide_static_component_content();
			self.default_option_changed();

			// Component data must be saved before viewing the Scenarios panel, or adding new scenarios.
			set_component_data_dirty_state( true );
		};

		this.options_style_changed = function() {

			var supports = self.$options_style_selector.find( 'option[value="' + self.$options_style_selector.val() + '"]' ).data( 'supports' );

			if ( 'yes' === supports.pagination ) {
				self.$pagination_style.show();
			} else {
				self.$pagination_style.hide();
			}
		};

		this.priced_individually_input_changed = function() {

			if ( self.$priced_individually_input.is( ':checked' ) ) {
				self.$discount.show();
				self.$display_prices.show();
			} else {
				self.$discount.hide();
				self.$display_prices.hide();
			}
		};

		this.show_filters_input_changed = function() {

			if ( self.$show_filters_input.is( ':checked' ) ) {
				self.$filters.show();
			} else {
				self.$filters.hide();
			}
		};

		this.maybe_hide_static_component_content = function() {

			var query_type = self.$query_type_selector.val();

			if ( 'category_ids' === query_type ) {

				self.$options_style_container.show();
				self.$sort_filter_container.show();

			} else {

				var products_count = self.get_product_ids().length;

				if ( products_count > 1 || products_count === 1 && self.is_optional() ) {
					self.$options_style_container.show();
					self.$sort_filter_container.show();
				} else {
					self.$options_style_container.hide();
					self.$sort_filter_container.hide();
				}
			}
		};

		this.default_option_changed = function() {

			var query_type            = self.$query_type_selector.val(),
				default_option        = self.get_default_option(),
				default_option_html   = self.get_default_option_html(),
				new_default_option    = 'category_ids' === query_type ? self.$default_selector_categories.val() : self.$default_selector_products.val(),
				update_default_option = true,
				error                 = false;

			if ( ! new_default_option && ! self.is_optional() ) {

				if ( 'category_ids' === query_type ) {

					if ( 'defaults' === $composite_price_calc.val() && self.get_category_ids().length > 0 ) {
						error = 'set_defaults';
					}

				} else {

					var products_count = self.get_product_ids().length;

					if ( products_count > 0 ) {

						if ( 'defaults' === $composite_price_calc.val() ) {
							error = 'set_defaults';
						} else if ( products_count === 1 ) {
							error = 'set_defaults_static';
						}
					}
				}
			}

			if ( error ) {

				if ( ! self.has_error( self.$default_selectors_container ) ) {

					setTimeout( function() {
						self.add_error_tip( self.$default_selectors_container, error );
					}, 5 );

					if ( ! default_option ) {
						self.add_error( self.$default_selectors_container, error );
					}
				}

				if ( 'product_ids' === query_type ) {
					// Product selector: Set value.
					self.$default_selector_products.val( default_option ).triggerHandler( 'change' );
				} else {
					// Category selector: Put option back in.
					if ( default_option_html ) {
						self.$default_selector_categories.append( default_option_html ).val( default_option ).triggerHandler( 'change' );
					}
				}

				update_default_option = false;
			}

			if ( update_default_option ) {
				self.clear_error( self.$default_selectors_container );
				self.set_default_option( new_default_option );
			}
		};

		this.add_error_tip = function( $target, error ) {

			var offset = $target.position();

			if ( $target.parent().find( '.wc_error_tip' ).length === 0 ) {
				$target.after( '<div class="wc_error_tip">' + wc_composite_admin_params[ 'i18n_' + error ] + '</div>' );
				$target.parent().find( '.wc_error_tip' )
					.css( 'left', offset.left + $target.width() - ( $target.width() / 2 ) - ( $( '.wc_error_tip' ).width() / 2 ) )
					.css( 'top', offset.top + $target.height() )
					.fadeIn( '100' );
			}
		};

		this.has_error = function( $target ) {
			return $target.hasClass( 'selection_error' );
		};

		this.add_error = function( $target, error ) {
			$target.find( '.wc-cp-error-tip' ).attr( 'data-tip', wc_composite_admin_params[ 'i18n_' + error ] );
			$target.find( '.wc-cp-error-tip' ).tipTip( tip_tip_params );
			$target.addClass( 'selection_error' );
		};

		this.clear_error = function( $target ) {
			$target.removeClass( 'selection_error' );
		};

		this.initialize = function() {

			self.query_type_changed();
			self.options_style_changed();
			self.priced_individually_input_changed();
			self.show_filters_input_changed();
		};

		this.initialize_default_option_category_select = function() {

			if ( 'undefined' === typeof self.get_default_option_html() ) {
				self.save_default_option_html();
			}
		};

		this.reinitialize_default_option_category_select = function() {

			var default_option      = self.get_default_option(),
				default_option_html = self.get_default_option_html();

			if ( default_option_html ) {
				self.$default_selector_categories.append( default_option_html );
			}

			self.$default_selector_categories.val( default_option ).triggerHandler( 'change' );
		};

		this.initialize_default_option_product_select = function() {

			var $options       = self.$products_selector.find( ':selected' ).clone().prop( 'selected', false ).removeAttr( 'selected' ).removeAttr( 'data-select2-id' ),
				has_default    = self.get_default_option(),
				default_exists = has_default && $options.filter( '[value="' + self.get_default_option() + '"]' ).length > 0;

			self.$default_selector_products.find( 'option' ).remove();
			self.$default_selector_products.append( $options );
			// Add an empty option.
			self.$default_selector_products.prepend( '<option/>' );

			if ( ! default_exists ) {
				var apply_default = ( ! self.is_optional() && $options.length > 0 && ( 'defaults' === $composite_price_calc.val() || 1 === $options.length ) ) ? $options.first().val() : 0;
				self.set_default_option( apply_default );
			}

			self.$default_selector_products.val( self.get_default_option() ).triggerHandler( 'change' );
		};

		this.initialize_content = function() {

			var query_type = self.$query_type_selector.val();

			if ( 'category_ids' === query_type ) {
				self.initialize_default_option_category_select();
			} else {
				self.initialize_default_option_product_select();
			}

			self.initialize_select2s();

			// Help-tips init.
			self.$content.find( '.woocommerce-help-tip' ).tipTip( tip_tip_params );

			self.initialized_content = true;
		};

		this.initialize_select2s = function() {

			self.$content.sw_select2( {

				sortable: {
					stop: function( $el ) {

						if ( $el.hasClass( 'products_selector' ) ) {

							var $component_el   = $el.closest( '.bto_group' ),
								component_el_id = $component_el.data( 'component_metabox_id' ),
								component       = component_objects[ component_el_id ];

							component.products_changed();
						}

					}
				}
			} );

		};

		this.maybe_update_default_option_category_ids = function( check_on_complete ) {

			check_on_complete = check_on_complete || false;

			// Fetch categories if needed.
			if ( null === self.get_default_option_category_ids() ) {

				if ( self.update_default_option_category_ids_xhr ) {
					self.update_default_option_category_ids_xhr.abort();
				}

				var data = {
					action:     'woocommerce_get_product_categories',
					product_id: self.get_default_option(),
					security:   wc_composite_admin_params.get_product_categories_nonce
				};

				self.update_default_option_category_ids_xhr = $.post( woocommerce_admin_meta_boxes.ajax_url, data, function ( response ) {

					self.set_default_option_category_ids( 'success' === response.result ? response.category_ids : [] );

					if ( check_on_complete ) {
						self.check_default_option_in_category_ids();
					}
				} );

			} else if ( check_on_complete ) {
				self.check_default_option_in_category_ids();
			}
		};

		this.check_default_option_in_category_ids = function() {

			if ( ( self.get_default_option() && ! self.is_default_option_in_category_ids() ) || self.get_category_ids().length === 0 ) {
				self.set_default_option( 0 );
				self.$default_selector_categories.val( 0 ).trigger( 'change' );
			}
		};

		this.is_default_option_in_category_ids = function() {

			// Check if the current default is within the included categories.
			var selection_data  = self.get_selector_data( self.$categories_selector ),
				default_cat_ids = self.get_default_option_category_ids(),
				option_cat_ids  = [];

			$.each( selection_data, function( index, data ) {
				option_cat_ids.push( parseInt( data.id ) );
			} );

			var is_valid = false;

			$.each( default_cat_ids, function( default_cat_index, default_cat_id ) {
				if ( self.array_contains( parseInt( default_cat_id ), option_cat_ids ) ) {
					is_valid = true;
					return false;
				}
			} );

			return is_valid;
		};

		this.get_default_option_category_ids = function() {
			var data = this.$default_selectors_container.data( 'selected_data' );
			return data.default_option_category_ids;
		};

		this.set_default_option_category_ids = function( value ) {
			var data = this.$default_selectors_container.data( 'selected_data' );
			data.default_option_category_ids = value;
		};

		this.get_default_option_html = function() {
			var data = this.$default_selectors_container.data( 'selected_data' );
			return data.default_option_html;
		};

		this.save_default_option_html = function() {

			var data = this.$default_selectors_container.data( 'selected_data' );

			if ( ! self.get_default_option() ) {
				data.default_option_html = false;
			} else {
				// Clone option from the active selector.
				var query_type           = self.$query_type_selector.val(),
					$default_option_html = 'category_ids' === query_type ? self.$default_selector_categories.find( ':selected' ).clone() : self.$default_selector_products.find( ':selected' ).clone();

				data.default_option_html = $default_option_html;
			}
		};

		this.get_default_option = function() {
			var data = this.$default_selectors_container.data( 'selected_data' );
			return data.default_option_id || 0;
		};

		this.set_default_option = function( value ) {

			var data = this.$default_selectors_container.data( 'selected_data' );

			data.default_option_id = value || 0;

			self.save_default_option_html();

			// Clear the categories storage. Will be updated only if needed, that is if the chosen categories are changed.
			self.set_default_option_category_ids( data.default_option_id ? null : false );
			self.maybe_update_default_option_category_ids();
		};

		this.get_selector_data = function( $selector ) {
			return $selector.selectSW( 'data' );
		};

		this.get_category_ids = function() {

			var selection_data = self.get_selector_data( self.$categories_selector ),
				option_cat_ids = [];

			$.each( selection_data, function( index, data ) {
				option_cat_ids.push( parseInt( data.id ) );
			} );

			return option_cat_ids;
		};

		this.get_product_ids = function() {

			var selection_data = self.get_selector_data( self.$products_selector ),
				product_ids    = [];

			$.each( selection_data, function( index, data ) {
				product_ids.push( parseInt( data.id ) );
			} );

			return product_ids;
		};

		this.array_contains = function( el, arr ) {
			return $.inArray( el, arr ) !== -1;
		};

		this.is_optional = function() {
			return self.$optional_checkbox.is( ':checked' );
		};

		this.initialize();
	}

	function set_component_data_dirty_state( is_dirty ) {
		is_dirty = is_dirty ? true : false;
		component_data_dirty = is_dirty;
	}

	function update_components_toolbar_state() {

		if ( $components.length > 0 ) {
			$components_container.removeClass( 'no-components' );
			$components_toggle_toolbar.removeClass( 'disabled' );
		} else {
			$components_container.addClass( 'no-components' );
			$components_toggle_toolbar.addClass( 'disabled' );
		}
	}

	function init_component_event_handlers() {

		/*
		 * Component Handlers.
		 */

		$components_container

			// Expand all: select2 init.
			.on( 'click', '.expand_all', function() {

				if ( $( this ).hasClass( 'disabled' ) ) {
					return false;
				}

				$.each( component_objects, function( index, component_object ) {
					component_object.initialize_content();
					setTimeout( function() {
						component_object.$el.addClass( 'open' ).removeClass( 'closed' );
						component_object.$content.show();
					}, 50 );

				} );

				return false;
			} )

			// Close all.
			.on( 'click', '.close_all', function() {

				if ( $( this ).hasClass( 'disabled' ) ) {
					return false;
				}

				$.each( component_objects, function( index, component_object ) {
					setTimeout( function() {
						component_object.$el.addClass( 'closed' ).removeClass( 'open' );
						component_object.$content.hide();
					}, 10 );

				} );

				return false;
			} )

			// select2 init.
			.on( 'click', '.bto_group_handle', function() {

				var $el       = $( this ).closest( '.bto_group' ),
					el_id     = $el.data( 'component_metabox_id' ),
					component = component_objects[ el_id ];

				if ( typeof( component ) !== 'undefined' ) {
					component.component_toggled();
				}
			} )

			.on( 'click', '.bto_group_handle .component-id-item', function() {
				return false;
			} )

			// Subsubsub navigation.
			.on( 'click', '.subsubsub a', function( e ) {

				var $section_link   = $( this ),
					$el             = $( this ).closest( '.bto_group' ),
					el_id           = $el.data( 'component_metabox_id' ),
					component       = component_objects[ el_id ];

				component.section_changed( $section_link );

				e.preventDefault();
			} )

			// Component Remove.
			.on( 'click', 'a.remove_row', function( e ) {

				var $el   = $( this ).closest( '.bto_group' ),
					el_id = $el.data( 'component_metabox_id' );

				$el.find( '*' ).off();
				$el.remove();

				delete component_objects[ el_id ];

				$components_panel.triggerHandler( 'wc-cp-components-changed' );

				e.preventDefault();
			} )

			// Add Component.
			.on( 'click', 'button.add_bto_group', function() {

				$components_panel.block( block_params );

				component_add_count++;

				var data = {
					action:   'woocommerce_add_composite_component',
					post_id:  woocommerce_admin_meta_boxes.post_id,
					id:       component_add_count,
					security: wc_composite_admin_params.add_component_nonce
				};

				setTimeout( function() {

					$.post( woocommerce_admin_meta_boxes.ajax_url, data, function ( response ) {

						$component_metaboxes.append( response );

						var $added    = $( '.bto_group', $component_metaboxes ).last(),
							added_obj = new Component( $added ),
							added_id  = 'component_' + component_add_count;

						$added.data( 'component_metabox_id', added_id );
						component_objects[ added_id ] = added_obj;

						$components_panel.triggerHandler( 'wc-cp-components-changed' );

						// select2 init.
						added_obj.initialize_content();

						$components_panel.triggerHandler( 'wc-cp-component-added', [ added_obj ] );
						$components_panel.unblock();

					} );

				}, 250 );

				return false;

			} )

			// Component Keyup.
			.on( 'keyup', 'input.group_title', function() {

				var $el             = $( this ).closest( '.bto_group' ),
					el_id           = $el.data( 'component_metabox_id' ),
					component       = component_objects[ el_id ];

				component.title_changed();
			} )

			// Query type.
			.on( 'change', 'select.component_query_type', function() {

				var $el             = $( this ).closest( '.bto_group' ),
					el_id           = $el.data( 'component_metabox_id' ),
					component       = component_objects[ el_id ];

				component.query_type_changed();
			} )

			// Categories.
			.on( 'change', 'select.categories_selector', function() {

				var $el             = $( this ).closest( '.bto_group' ),
					el_id           = $el.data( 'component_metabox_id' ),
					component       = component_objects[ el_id ];

				component.categories_changed();
			} )

			// Products.
			.on( 'change', 'select.products_selector', function() {

				var $el             = $( this ).closest( '.bto_group' ),
					el_id           = $el.data( 'component_metabox_id' ),
					component       = component_objects[ el_id ];

				component.products_changed();
			} )

			// Default category option.
			.on( 'change', 'select.default_selector_categories', function() {

				var $el             = $( this ).closest( '.bto_group' ),
					el_id           = $el.data( 'component_metabox_id' ),
					component       = component_objects[ el_id ];

				component.default_option_changed();
			} )

			// Default product option.
			.on( 'change', 'select.default_selector_products', function() {

				var $el             = $( this ).closest( '.bto_group' ),
					el_id           = $el.data( 'component_metabox_id' ),
					component       = component_objects[ el_id ];

				component.default_option_changed();
			} )

			// Optional.
			.on( 'change', 'input.component_optional', function() {

				var $el             = $( this ).closest( '.bto_group' ),
					el_id           = $el.data( 'component_metabox_id' ),
					component       = component_objects[ el_id ];

				component.optional_changed();
			} )

			// Options style.
			.on( 'change', 'select.options_style_selector', function() {

				var $el             = $( this ).closest( '.bto_group' ),
					el_id           = $el.data( 'component_metabox_id' ),
					component       = component_objects[ el_id ];

				component.options_style_changed();
			} )

			// Priced individually.
			.on( 'change', '.group_priced_individually input', function() {

				var $el             = $( this ).closest( '.bto_group' ),
					el_id           = $el.data( 'component_metabox_id' ),
					component       = component_objects[ el_id ];

				component.priced_individually_input_changed();
			} )

			// Filters.
			.on( 'change', '.group_show_filters input', function() {

				var $el             = $( this ).closest( '.bto_group' ),
					el_id           = $el.data( 'component_metabox_id' ),
					component       = component_objects[ el_id ];

				component.show_filters_input_changed();
			} )

			// Set Image.
			.on( 'click', '.upload_component_image_button', function( e ) {

				component_image_frame_data.$button = $( this );

				e.preventDefault();

				// If the media frame already exists, reopen it.
				if ( component_image_frame_data.image_frame ) {

					component_image_frame_data.image_frame.open();

				} else {

					// Create the media frame.
					component_image_frame_data.image_frame = wp.media( {

						// Set the title of the modal.
						title: wc_composite_admin_params.i18n_choose_component_image,
						button: {
							text: wc_composite_admin_params.i18n_set_component_image
						},
						states: [
							new wp.media.controller.Library( {
								title: wc_composite_admin_params.i18n_choose_component_image,
								filterable: 'all'
							} )
						]
					} );

					// When an image is selected, run a callback.
					component_image_frame_data.image_frame.on( 'select', function () {

						var attachment = component_image_frame_data.image_frame.state().get( 'selection' ).first().toJSON(),
							url        = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

						component_image_frame_data.$button.addClass( 'has_image' );
						component_image_frame_data.$button.closest( '.component_image' ).find( '.remove_component_image_button' ).addClass( 'has_image' );
						component_image_frame_data.$button.find( 'input' ).val( attachment.id ).trigger( 'change' );
						component_image_frame_data.$button.find( 'img' ).eq( 0 ).attr( 'src', url );
					} );

					// Finally, open the modal.
					component_image_frame_data.image_frame.open();
				}

			} )

			// Remove Image.
			.on( 'click', '.remove_component_image_button', function( e ) {

				var $button         = $( this ),
					$option_wrapper = $button.closest( '.component_image' ),
					$upload_button  = $option_wrapper.find( '.upload_component_image_button' );

				e.preventDefault();

				$upload_button.removeClass( 'has_image' );
				$button.removeClass( 'has_image' );
				$option_wrapper.find( 'input' ).val( '' ).trigger( 'change' );
				$upload_button.find( 'img' ).eq( 0 ).attr( 'src', wc_composite_admin_params.wc_placeholder_img_src );

			} );

	}

	function init_component_metaboxes() {

		// Component sorting.
		$component_metaboxes.sortable( {
			items: '.bto_group',
			cursor: 'move',
			axis: 'y',
			handle: '.sort-item',
			scrollSensitivity: 40,
			forcePlaceholderSize: true,
			helper: 'clone',
			opacity: 0.65,
			placeholder: 'wc-metabox-sortable-placeholder',
			start: function( event, ui ) {
				ui.item.css( 'background-color','#f6f6f6' );
			},
			stop: function( event, ui ) {
				ui.item.removeAttr( 'style' );
				$components_panel.triggerHandler( 'wc-cp-components-changed' );
			}
		} );

		update_components_toolbar_state();
	}

	function init_component_objects() {

		component_objects = {};

		// Create objects.
		$components.each( function( index ) {

			var $el   = $( this ),
				el_id = 'component_' + index;

			$el.data( 'component_metabox_id', el_id );
			component_objects[ el_id ] = new Component( $el );
		} );

		// Initialize metaboxes.
		init_component_metaboxes();
	}

	function init_components() {

		// Attach event handlers.
		init_component_event_handlers();

		// Create objects.
		init_component_objects();
	}

	init_components();


	/*--------------------------------------------------*/
	/*  Conditions Stack                                */
	/*--------------------------------------------------*/

	function Conditions_Stack( $data_container, $components_container ) {
		var os = this;
		// Validate input.
		if ( ! $data_container.length || ! $components_container.length ) {
			return;
		}

		// This is a Class, it needs to be instantiated.
		if ( ! ( this instanceof Conditions_Stack ) ) {
			return new Conditions_Stack( $data_container, $components_container );
		}

		// Template names.
		this.templates = {
			row              : 'wc_cp_condition_row',
			condition        : 'wc_cp_%%type%%_condition_content',
			add              : 'wc_cp_condition_add_content',
			component_option : 'wc_cp_condition_component_option'
		};

		// Template caches.
		this.os_row_template              = false;
		this.os_condition_templates       = {};
		this.os_add_template              = false;
		this.os_component_option_template = false;

		// DOM caches.
		this.$data_container        = $data_container.length > 1 ? $data_container.first() : $data_container;
		this.$components_container  = $components_container.length > 1 ? $components_container.first() : $components_container;
		this.$add_container         = this.$data_container.find( '.os_add' );
		this.$add_os_select         = this.$data_container.find( '.os_add select.os_type' );
		this.$list                  = this.$data_container.find( '.os_list' );
		this.$boarding              = this.$data_container.find( '.os_boarding' );

		// Runtime.
		this.modifiers                 = this.$list.data( 'os_modifiers' ) ? this.$list.data( 'os_modifiers' ) : {};
		this.options_count             = parseInt( this.$list.find( '.os_row' ).length, 10 );
		this.allow_unique_additions    = 'yes' === this.$data_container.data( 'unique_additions' ) ? 'yes' : 'no';
		this.components_select_options = false;

		// Check the DOM.
		if ( ! this.$add_container.length || ! this.$add_os_select.length || ! this.$list.length ) {
			return;
		}

		// Init list.
		this.$list.cp_scenario_conditions_scripts();

		// Events.
		this.$data_container

			// Clear tiptip.
			.on( 'mousedown', '.os_remove .trash', function () {
				$( this ).triggerHandler( 'mouseleave' );
			} )

			// Option Remove.
			.on( 'click', '.os_remove .trash', function ( e ) {
				e.preventDefault();
				$( this ).closest( '.os_row' ).remove();
				os.$data_container.trigger( 'os_removed', [ os ] );
				os.options_count--;
				os.maybe_add_boarding();
				return true;
			} )

			// Modifier change.
			.on( 'change', '.os_modifier select', function () {

				var $modifier      = $( this ),
					modifier       = $modifier.val(),
					$parent        = $modifier.closest( '.os_row_inner' ),
					$value_options = $parent.find( '.os_value div[data-modifiers]' );

				if ( $value_options.length ) {
					// This value depends on modifier select.

					$value_options.hide();
					$value_options.each( function() {

						var $value_option = $( this ),
							modifiers     = $value_option.data( 'modifiers' ).split( ',' );

						if ( modifiers && -1 !== modifiers.indexOf( modifier ) ) {
							$value_option.show();
						}
					} );
				}
			} )

			// Option Add.
			.on( 'change', '.os_add select.os_type', function () {

				var $selector    = $( this ),
					component_id = $selector.val();

				if ( 'add' === component_id ) {
					return false;
				}

				// Check for existing component.
				if ( os.allow_unique_additions ) {

					var $existing_components = os.$list.find( 'select.os_type' ),
						$existing_component;

					$existing_components.each( function() {
						var $selected = $( this ).find( ':selected' );
						if ( $selected && $selected.length && component_id === $selected.val() ) {
							$existing_component = $( this );
							// Break loop.
							return false;
						}
					} );

					if ( $existing_component && $existing_component.length ) {
						$existing_component.trigger( 'focus' );
						$selector.find( 'option[value="add"]' ).prop( 'selected', 'selected' );
						return false;
					}
				}

				var $component_query_data = os.$components_container.find( '#component_query_type_' + component_id );
				if ( ! $component_query_data || $component_query_data.length < 1 ) {
					return false;
				}

				// Template Variables.
				var os_index          = parseInt( os.$data_container.attr( 'data-os_count' ), 10 ) + 1,
					os_post_name      = os.$data_container.attr( 'data-os_post_name' ),
					type              = 'yes' === $component_query_data.data( 'use_ajax' ) ? 'ajax' : 'flat',
					component_options = $component_query_data.data( 'component_options' ) ? $component_query_data.data( 'component_options' ) : [];

				// Templates.
				var	os_row_template              = os.get_os_row_template(),
					os_condition_template        = os.get_os_condition_template( type ),
					os_component_option_template = os.get_os_component_option_template(),
					os_component_options         = os.get_component_select_options();

				if ( ! os_row_template || ! os_condition_template ) {
					return false;
				}

				var	$os_component_options_html;
				if ( component_options ) {
					for ( var co_index = 0; co_index < component_options.length; co_index++ ) {
						$os_component_options_html += os_component_option_template( {
							option_value : component_options[ co_index ].option_value,
							option_label : component_options[ co_index ].option_label,
						} );
					}
				}

				var $new_os_row_content = os_condition_template( {
					os_post_name              : os_post_name,
					os_component_id           : component_id,
					os_component_options_html : $os_component_options_html
				} );

				var $new_os_row = os_row_template( {
					os_index   : os_index,
					os_components: os_component_options,
					os_content : $new_os_row_content
				} );

				os.$data_container.attr( 'data-os_count', os_index );

				$new_os_row = os.prepare_new_row( $new_os_row );
				os.$list.append( $new_os_row );

				var $added = os.$list.find( '.os_row' ).last();

				// We have to make the appropriate condition_id selected in the condition_type select.
				$added.find( '.os_type option[value="' + component_id + '"]' ).prop( 'selected', 'selected' );

				os.$data_container.trigger( 'os_added', [ os ] );
				$added.cp_scenario_conditions_scripts();
				os.options_count++;
				os.maybe_add_boarding();

				// Change add_filter select back to placeholder.
				$selector.find( 'option[value="add"]' ).prop( 'selected', 'selected' );

				return false;
			} );

		// List events.
		this.$list

			// Option Change.
			.on( 'change', 'select.os_type', function () {

				var $selector    = $( this ),
					$option      = $selector.closest( '.os_row' ),
					component_id = $selector.val();

				var $component_query_data = os.$components_container.find( '#component_query_type_' + component_id );
				if ( ! $component_query_data || $component_query_data.length < 1 ) {
					return false;
				}

				var os_post_name      = os.$data_container.attr( 'data-os_post_name' ),
					type              = 'yes' === $component_query_data.data( 'use_ajax' ) ? 'ajax' : 'flat',
					component_options = $component_query_data.data( 'component_options' ) ? $component_query_data.data( 'component_options' ) : [];

				// Templates.
				var	os_condition_template        = os.get_os_condition_template( type ),
					os_component_option_template = os.get_os_component_option_template();

				if ( ! os_condition_template || ! os_component_option_template ) {
					return false;
				}

				var	$os_component_options_html;
				if ( component_options ) {
					for ( var co_index = 0; co_index < component_options.length; co_index++ ) {
						$os_component_options_html += os_component_option_template( {
							option_value : component_options[ co_index ].option_value,
							option_label : component_options[ co_index ].option_label,
						} );
					}
				}

				var $new_os_row_content = os_condition_template( {
					os_post_name              : os_post_name,
					os_component_id           : component_id,
					os_component_options_html : $os_component_options_html
				} );

				$new_os_row_content = os.prepare_new_row( $new_os_row_content );
				$option.find( '.os_content' ).html( $new_os_row_content );

				os.$data_container.trigger( 'os_changed', [ os ] );
				$option.cp_scenario_conditions_scripts();

				return false;
			} );

		// Component events.
		this.$components_container

			.on( 'wc-cp-components-changed', function() {
				// Clear components cache.
				os.components_select_options = false;
			} );

	}

	Conditions_Stack.prototype = ( function() {

		var reset_options = function() {
			this.$list.html('');
			this.$data_container.attr( 'data-filters_count', 0 );
			this.options_count = 0;
			this.maybe_add_boarding();
		};

		var maybe_add_boarding = function() {

			if ( this.options_count == 0 ) {
				this.$data_container.addClass( 'os_empty' );
				this.$list.addClass( 'hidden' );
				this.$boarding.addClass( 'active' );
				this.$add_container.addClass( 'os_add--boarding' );
			} else {
				this.$data_container.removeClass( 'os_empty' );
				this.$list.removeClass( 'hidden' );
				this.$boarding.removeClass( 'active' );
				this.$add_container.removeClass( 'os_add--boarding' );
			}
		};

		var prepare_new_row = function( row_html ) {

			// Parse to jQuery.
			var $row = $( row_html );

			// Alter 'modifiers' labels.
			if ( ! $.isEmptyObject( this.modifiers ) ) {

				var $modifier_select = $row.find( '.os_modifier select' ),
					$new_modifiers   = '';

				for ( var i in this.modifiers ) {
					$new_modifiers += '<option value=' + i + '>' + this.modifiers[i] + '</option>';
				}

				$modifier_select.html( $new_modifiers );
			}

			return $row;
		};

		var get_os_component_option_template = function() {

			var template = false;
			if ( typeof( this.os_component_option_template ) === 'function' ) {
				template = this.os_component_option_template;
			} else {
				template                          = wp.template( this.templates.component_option );
				this.os_component_option_template = template;
			}

			return template;
		};

		var get_os_row_template = function() {

			var template = false;
			if ( typeof( this.os_row_template ) === 'function' ) {
				template = this.os_row_template;
			} else {
				template             = wp.template( this.templates.row );
				this.os_row_template = template;
			}

			return template;
		};

		var get_os_condition_template = function( type ) {

			var template = false, name;
			if ( typeof( this.os_condition_templates[ type ] ) === 'function' ) {
				template = this.os_condition_templates[ type ];
			} else {
				name = this.templates.condition;
				name = name.replace( '%%type%%', type );
				template                            = wp.template( name );
				this.os_condition_templates[ type ] = template;
			}

			return template;
		};

		var get_os_add_template = function() {

			var template = false;
			if ( typeof( this.os_add_template ) === 'function' ) {
				template = this.os_add_template;
			} else {

				template             = wp.template( this.templates.add );
				this.os_add_template = template;
			}

			return template;
		};

		var get_component_select_options = function() {

			var options = '';

			if ( this.components_select_options !== false ) {
				options = this.components_select_options;
			} else {

				var $options = this.$add_container.find( 'select.os_type' ).children();

				$options.each( function() {
					var $this = $( this );

					// Skip add placeholder.
					if ( 'add' === $this.val() ) {
						return;
					}

					options += '<option value="' + $this.val() + '">' + $this.text()+'</option>';
				} );
				this.components_select_options = options;
			}

			return options;
		};

		return {
			reset_options: reset_options,
			get_os_row_template: get_os_row_template,
			get_os_condition_template: get_os_condition_template,
			get_os_add_template: get_os_add_template,
			get_os_component_option_template: get_os_component_option_template,
			maybe_add_boarding: maybe_add_boarding,
			prepare_new_row: prepare_new_row,
			get_component_select_options: get_component_select_options
		};

	}() );

	// Extend for row init.
	$.fn.cp_scenario_conditions_scripts = function() {

		var $this = $( this );

		$this.sw_select2();

		$this.find( '.woocommerce-help-tip, .help_tip, .tips' ).tipTip( tip_tip_params );
	};

	/*--------------------------------------------------*/
	/*  Scenarios                                       */
	/*--------------------------------------------------*/

	// Init conditions stack on scenarios.
	$scenarios_panel.on( 'wc-cp-scenario-initialized', function( e, scenario ) {

		var $conditions_container = scenario.$content.find( '.wc-cp-scenario-conditions-container' );
		if ( $conditions_container && $conditions_container.length ) {
			scenario.conditions_stack = new Conditions_Stack( $conditions_container, $components_panel );
		}

		// Scenario "Hide Options" action.
		var $action_options_conditions_container = scenario.$content.find( '.action_options .os_container' );
		if ( $action_options_conditions_container && $action_options_conditions_container.length ) {
			scenario.hidden_options_stack = new Conditions_Stack( $action_options_conditions_container, $components_panel );
		}
	} );

	function Scenario( $el ) {

		var self = this;

		this.$el                           = $el;
		this.$content                      = $el.find( '.bto_scenario_data' );
		this.$metabox_title                = $el.find( 'h3 .scenario_name .scenario_name_inner' );
		this.$non_effective_conditions_tip = $el.find( '.scenario-help-tip--non-effective-conditions' );

		this.$title_input                  = this.$content.find( 'input.bto_scenario_data_title' );
		this.render_content                = this.$content.data( 'scenario_content' );

		this.$cc_action_input              = this.$content.find( 'input.scenario_action_conditional_components_input' );
		this.$co_action_input              = this.$content.find( 'input.scenario_action_conditional_options_input' );
		this.$cc_component_ids             = this.$content.find( 'select.conditional_components_ids' );

		this.conditions_stack              = false;
		this.hidden_options_stack          = false;

		this.initialized_content           = false;

		this.scenario_toggled = function() {

			var $el         = this.$el,
				$el_content = this.$content;

			if ( is_scenario_loading ) {
				return;
			}

			var	initialize = self.maybe_initialize_content(),
				delay      = initialize && async_load_scenarios() ? 200 : ( initialize ? 50 : 10 );

			setTimeout( function() {

				$el.toggleClass( 'closed' ).toggleClass( 'open' );

				$el_content.stop().slideToggle( { always: function() {
					if ( initialize && async_load_scenarios() ) {
						self.$el.removeClass( 'loading' );
						is_scenario_loading = false;
					}
				} } );

			}, delay );
		};

		this.maybe_initialize_content = function() {

			var initialize_content = false,
				delay              = async_load_scenarios() ? 100 : 10;

			if ( ! self.initialized_content ) {

				initialize_content = true;

				if ( async_load_scenarios() ) {
					self.$el.addClass( 'loading' );
					is_scenario_loading = true;
				}

				setTimeout( function() {
					self.initialize_content();
				}, delay );
			}

			return initialize_content;
		};

		this.title_changed = function() {

			self.$metabox_title.text( self.$title_input.val() );
		};

		this.initialize_content = function() {

			if ( '' !== self.render_content ) {

				self.$content.html( self.render_content );

				self.$title_input      = self.$content.find( 'input.bto_scenario_data_title' );
				self.$cc_action_input  = self.$content.find( 'input.scenario_action_conditional_components_input' );
				self.$co_action_input  = self.$content.find( 'input.scenario_action_conditional_options_input' );
				self.$cc_component_ids = self.$content.find( 'select.conditional_components_ids' );
			}

			self.initialize_select2s();

			// Help-tips init.
			self.$content.find( '.tips, .woocommerce-help-tip' ).tipTip( tip_tip_params );

			self.initialized_content = true;
			$scenarios_panel.triggerHandler( 'wc-cp-scenario-initialized', self );
		};

		this.initialize_select2s = function() {

			self.$content.sw_select2( {

				sortable: {

					stop: function( $el ) {

						if ( $el.hasClass( 'products_selector' ) ) {

							var $component_el   = $el.closest( '.bto_group' ),
								component_el_id = $component_el.data( 'component_metabox_id' ),
								component       = component_objects[ component_el_id ];

							component.products_changed();
						}

					}
				}
			} );

		};

		this.update_non_effective_conditions_tip = function() {

			var components                     = {},
				$components                    = self.conditions_stack.$add_os_select.find( 'option' ),
				$conditions                    = self.conditions_stack.$list.find( '.os_row' ),
				$hidden_options                = self.hidden_options_stack.$list.find( '.os_row' ),
				hidden_components              = self.$cc_component_ids.val(),
				last_condition_component_index = 0,
				first_action_component_index   = 10000;

			if ( ! $components.length ) {
				self.$non_effective_conditions_tip.fadeOut( 200 );
			}

			if ( ! $conditions.length ) {
				self.$non_effective_conditions_tip.fadeOut( 200 );
			}

			$components.each( function( index, option ) {

				var value = $( option ).val();

				if ( 'add' === value ) {
					return true;
				}

				components[ value ] = index;
			} );

			$conditions.each( function( index, condition ) {

				var component_id = $( condition ).find( 'select.os_type' ).val(),
				    modifier     = $( condition ).find( '.os_modifier select' ).val();

				if ( 'in-any' === modifier ) {
					return true;
				}

				if ( components[ component_id ] > last_condition_component_index ) {
					last_condition_component_index = components[ component_id ];
				}

			} );

			if ( self.$cc_action_input.is( ':checked' ) ) {

				$.each( hidden_components, function( index, hidden_component_id ) {

					if ( components[ hidden_component_id ] < first_action_component_index ) {
						first_action_component_index = components[ hidden_component_id ];
					}
				} );
			}

			if ( self.$co_action_input.is( ':checked' ) ) {

				$hidden_options.each( function( index, hidden_option ) {

					var component_id = $( hidden_option ).find( 'select.os_type' ).val();

					if ( components[ component_id ] < first_action_component_index ) {
						first_action_component_index = components[ component_id ];
					}

				} );
			}

			if ( last_condition_component_index >= first_action_component_index ) {
				self.$non_effective_conditions_tip.fadeIn( 200 );
			} else {
				self.$non_effective_conditions_tip.fadeOut( 200 );
			}
		};

		this.initialize = function() {
			self.$non_effective_conditions_tip.tipTip( tip_tip_params );
		};

		this.initialize();
	}

	function async_load_scenarios() {
		return $components.length > 20;
	}

	function update_scenarios_toolbar_state() {

		if ( $scenarios.length > 0 ) {
			$scenarios_toggle_toolbar.removeClass( 'disabled' );
		} else {
			$scenarios_toggle_toolbar.addClass( 'disabled' );
		}
	}

	function init_scenario_event_handlers() {

		$scenarios_container

			// Expand all: select2 init.
			.on( 'click', '.expand_all', function() {

				if ( $( this ).hasClass( 'disabled' ) ) {
					return false;
				}

				$.each( scenario_objects, function( index, scenario_object ) {
					scenario_object.initialize_content();
					setTimeout( function() {
						scenario_object.$el.addClass( 'open' ).removeClass( 'closed' );
						scenario_object.$content.show();
					}, 50 );

				} );

				return false;
			} )

			// Close all.
			.on( 'click', '.close_all', function() {

				if ( $( this ).hasClass( 'disabled' ) ) {
					return false;
				}

				$.each( scenario_objects, function( index, scenario_object ) {
					setTimeout( function() {
						scenario_object.$el.addClass( 'closed' ).removeClass( 'open' );
						scenario_object.$content.hide();
					}, 10 );

				} );

				return false;
			} )

			// select2 init.
			.on( 'click', '.bto_scenario_handle', function() {

				var $el      = $( this ).closest( '.bto_scenario' ),
					el_id    = $el.data( 'scenario_metabox_id' ),
					scenario = scenario_objects[ el_id ];

				if ( typeof( scenario ) !== 'undefined' ) {
					scenario.scenario_toggled();
				}
			} )

			// Scenario Remove.
			.on( 'click', 'a.remove_row', function( e ) {

				var $el   = $( this ).closest( '.bto_scenario' ),
					el_id = $el.data( 'scenario_metabox_id' );

				$el.find( '*' ).off();
				$el.remove();

				delete scenario_objects[ el_id ];

				$scenarios_panel.triggerHandler( 'wc-cp-scenarios-changed' );

				e.preventDefault();
			} )

			// Scenario Keyup.
			.on( 'keyup', 'input.bto_scenario_data_title', function() {

				var $el      = $( this ).closest( '.bto_scenario' ),
					el_id    = $el.data( 'scenario_metabox_id' ),
					scenario = scenario_objects[ el_id ];

				scenario.title_changed();
			} )

			// Scenario action config toggle.
			.on( 'change', '.toggle_scenario_action_config input', function() {

				var $input        = $( this ),
					checked       = $input.is( ':checked' ),
					$parent       = $input.closest( '.scenario_action_config_group' ),
					$config_group = $parent.find( '.action_config' );

				if ( $config_group && $config_group.length ) {
					if ( checked ) {
						$config_group.show();
					} else {
						$config_group.hide();
					}
				}
			} )

			// Toggle scenario.
			.on( 'click', '.woocommerce-input-toggle', function() {

				var $toggler      = $( this ),
					$parent       = $toggler.closest( '.wc-metabox' ),
					$active_input = $parent.find( '.bto_scenario_handle .handle > input.enabled' );

				toggleActiveSwitch( $toggler, $active_input );
				return false; // Don't propagate.
			} )

			// Add scenario.
			.on( 'click', 'button.add_bto_scenario', function () {

				$scenarios_panel.block( block_params );

				scenario_add_count++;

				var data = {
					action: 	'woocommerce_add_composite_scenario',
					post_id: 	woocommerce_admin_meta_boxes.post_id,
					id: 		scenario_add_count,
					security: 	wc_composite_admin_params.add_scenario_nonce
				};

				setTimeout( function() {

					$.post( woocommerce_admin_meta_boxes.ajax_url, data, function ( response ) {

						$scenario_metaboxes.append( response );

						var $added    = $( '.bto_scenario', $scenario_metaboxes ).last(),
							added_obj = new Scenario( $added ),
							added_id  = 'scenario_' + scenario_add_count;

						$added.data( 'scenario_metabox_id', added_id );
						scenario_objects[ added_id ] = added_obj;

						$scenarios_panel.triggerHandler( 'wc-cp-scenarios-changed' );

						// select2 init.
						added_obj.initialize_content();

						$scenarios_panel.triggerHandler( 'wc-cp-scenario-added', [ added_obj ] );
						$scenarios_panel.unblock();

					} );

				}, 250 );

				return false;
			} )

			.on( 'change', 'input.scenario_action_conditional_options_input, input.scenario_action_conditional_components_input, select.conditional_components_ids, .wc-cp-scenario-conditions-container .os_modifier select', function() {

				var $el      = $( this ).closest( '.bto_scenario' ),
					el_id    = $el.data( 'scenario_metabox_id' ),
					scenario = scenario_objects[ el_id ];

				scenario.update_non_effective_conditions_tip();
			} )

			.on( 'os_removed os_changed os_added', function( e, stack ) {

				var $el      = stack.$data_container.closest( '.bto_scenario' ),
					el_id    = $el.data( 'scenario_metabox_id' ),
					scenario = scenario_objects[ el_id ];

				scenario.update_non_effective_conditions_tip();
			} );
	}

	function init_scenario_metaboxes() {

		// Scenario ordering.
		$scenario_metaboxes.sortable( {
			items: '.bto_scenario',
			cursor: 'move',
			axis: 'y',
			handle: '.sort-item',
			scrollSensitivity: 40,
			forcePlaceholderSize: true,
			helper: 'clone',
			opacity: 0.65,
			placeholder: 'wc-metabox-sortable-placeholder',
			start: function( event, ui ) {
				ui.item.css( 'background-color','#f6f6f6' );
			},
			stop: function( event, ui ) {
				ui.item.removeAttr( 'style' );
				$scenarios_panel.triggerHandler( 'wc-cp-scenarios-changed' );
			}
		} );

		update_scenarios_toolbar_state();
	}

	function init_scenario_objects() {

		scenario_objects = {};

		// Create objects.
		$scenarios.each( function( index ) {

			var $el   = $( this ),
				el_id = 'scenario_' + index;

			$el.data( 'scenario_metabox_id', el_id );
			scenario_objects[ el_id ] = new Scenario( $el );
		} );

		// Initialize metaboxes.
		init_scenario_metaboxes();
	}

	function init_scenarios() {

		// Attach event handlers.
		init_scenario_event_handlers();

		// Create objects.
		init_scenario_objects();
	}

	init_scenarios();

	/*
	 * Reload Scenarios when the view is dirty.
	 */

	// Replace anchor with new one.
	var $scenarios_tab   = $( '.cp_scenarios_tab' ),
	    $scenarios_tab_a = $scenarios_tab.find( 'a' );

	$scenarios_tab.prepend( '<a class="cp_scenarios_tab_inner" href="#bto_scenario_data"></a>' );

	var $scenarios_tab_a_new = $scenarios_tab.find( 'a.cp_scenarios_tab_inner' );

	$scenarios_tab_a_new.html( $scenarios_tab_a.html() );

	// Handle click events.
	$scenarios_tab_a_new.on( 'click', function() {

		if ( component_data_dirty && ! configuration_save_xhr ) {

			var reload_view = window.confirm( wc_composite_admin_params.i18n_scenarios_panel_blocked );

			if ( reload_view ) {

				$scenarios_tab_a.trigger( 'click' );

				setTimeout( function() {
					save_composite();
				}, 150 );
			}

		} else {
			$scenarios_tab_a.trigger( 'click' );
		}

		return false;
	} );


	/*--------------------------------------------------*/
	/*  States                                          */
	/*--------------------------------------------------*/

	if ( has_states ) {

		// Init conditions stack on scenarios.
		$states_panel.on( 'wc-cp-state-initialized', function( e, state ) {
			var $conditions_container = state.$content.find( '.wc-cp-state-conditions-container' );
			if ( $conditions_container.length ) {
				new Conditions_Stack( $conditions_container, $components_panel );
			}
		} );
	}

	function State( $el ) {

		var self = this;

		this.$el                 = $el;
		this.$content            = $el.find( '.bto_state_data' );
		this.$metabox_title      = $el.find( 'h3 .state_name .state_name_inner' );
		this.$title_input        = this.$content.find( 'input.bto_state_data_title' );
		this.render_content      = this.$content.data( 'state_content' );

		this.initialized_content = false;

		this.state_toggled = function() {

			var $el         = this.$el,
				$el_content = this.$content;

			if ( is_state_loading ) {
				return;
			}

			var	initialize = self.maybe_initialize_content(),
				delay      = initialize && async_load_states() ? 200 : ( initialize ? 50 : 10 );

			setTimeout( function() {

				$el.toggleClass( 'closed' ).toggleClass( 'open' );

				$el_content.stop().slideToggle( { always: function() {
					if ( initialize && async_load_states() ) {
						self.$el.removeClass( 'loading' );
						is_state_loading = false;
					}
				} } );

			}, delay );
		};

		this.maybe_initialize_content = function() {

			var initialize_content = false,
				delay              = async_load_states() ? 100 : 10;

			if ( ! self.initialized_content ) {

				initialize_content = true;

				if ( async_load_states() ) {
					self.$el.addClass( 'loading' );
					is_state_loading = true;
				}

				setTimeout( function() {
					self.initialize_content();
				}, delay );
			}

			return initialize_content;
		};

		this.title_changed = function() {

			self.$metabox_title.text( self.$title_input.val() );
		};

		this.initialize_content = function() {

			if ( '' !== self.render_content ) {

				self.$content.html( self.render_content );
				self.$title_input = self.$content.find( 'input.bto_state_data_title' );
			}

			self.initialize_select2s();

			// Help-tips init.
			self.$content.find( '.tips, .woocommerce-help-tip' ).tipTip( tip_tip_params );

			self.initialized_content = true;
			$states_panel.triggerHandler( 'wc-cp-state-initialized', self );
		};

		this.initialize_select2s = function() {

			self.$content.sw_select2( {

				sortable: {

					stop: function( $el ) {

						if ( $el.hasClass( 'products_selector' ) ) {

							var $component_el   = $el.closest( '.bto_group' ),
								component_el_id = $component_el.data( 'component_metabox_id' ),
								component       = component_objects[ component_el_id ];

							component.products_changed();
						}

					}
				}
			} );

		};

		this.initialize = function() {
			// Emptiness.
		};

		this.initialize();
	}

	function async_load_states() {
		return $components.length > 20;
	}

	function update_states_toolbar_state() {

		if ( $states.length > 0 ) {
			$states_toggle_toolbar.removeClass( 'disabled' );
		} else {
			$states_toggle_toolbar.addClass( 'disabled' );
		}
	}

	function init_state_event_handlers() {

		$states_container

			// Expand all: select2 init.
			.on( 'click', '.expand_all', function() {

				if ( $( this ).hasClass( 'disabled' ) ) {
					return false;
				}

				$.each( state_objects, function( index, state_object ) {
					state_object.initialize_content();
					setTimeout( function() {
						state_object.$el.addClass( 'open' ).removeClass( 'closed' );
						state_object.$content.show();
					}, 50 );

				} );

				return false;
			} )

			// Close all.
			.on( 'click', '.close_all', function() {

				if ( $( this ).hasClass( 'disabled' ) ) {
					return false;
				}

				$.each( state_objects, function( index, state_object ) {
					setTimeout( function() {
						state_object.$el.addClass( 'closed' ).removeClass( 'open' );
						state_object.$content.hide();
					}, 10 );

				} );

				return false;
			} )

			// select2 init.
			.on( 'click', '.bto_state_handle', function() {

				var $el   = $( this ).closest( '.bto_state' ),
					el_id = $el.data( 'state_metabox_id' ),
					state = state_objects[ el_id ];

				if ( typeof( state ) !== 'undefined' ) {
					state.state_toggled();
				}
			} )

			// Scenario Remove.
			.on( 'click', 'a.remove_row', function( e ) {

				var $el   = $( this ).closest( '.bto_state' ),
					el_id = $el.data( 'state_metabox_id' );

				$el.find( '*' ).off();
				$el.remove();

				delete state_objects[ el_id ];

				$states_panel.triggerHandler( 'wc-cp-states-changed' );

				e.preventDefault();
			} )

			// Scenario Keyup.
			.on( 'keyup', 'input.bto_state_data_title', function() {

				var $el   = $( this ).closest( '.bto_state' ),
					el_id = $el.data( 'state_metabox_id' ),
					state = state_objects[ el_id ];

				state.title_changed();
			} )

			// Toggle state.
			.on( 'click', '.woocommerce-input-toggle', function() {

				var $toggler      = $( this ),
					$parent       = $toggler.closest( '.wc-metabox' ),
					$active_input = $parent.find( '.bto_state_handle .handle > input.enabled' );

				toggleActiveSwitch( $toggler, $active_input );
				return false; // Don't propagate.
			} )

			// Add state.
			.on( 'click', 'button.add_bto_state', function () {

				$states_panel.block( block_params );

				state_add_count++;

				var data = {
					action: 	'woocommerce_add_composite_state',
					post_id: 	woocommerce_admin_meta_boxes.post_id,
					id: 		state_add_count,
					security: 	wc_composite_admin_params.add_state_nonce
				};

				setTimeout( function() {

					$.post( woocommerce_admin_meta_boxes.ajax_url, data, function ( response ) {

						$state_metaboxes.append( response );

						var $added    = $( '.bto_state', $state_metaboxes ).last(),
							added_obj = new State( $added ),
							added_id  = 'state_' + state_add_count;

						$added.data( 'state_metabox_id', added_id );
						state_objects[ added_id ] = added_obj;

						$states_panel.triggerHandler( 'wc-cp-states-changed' );

						// select2 init.
						added_obj.initialize_content();

						$states_panel.triggerHandler( 'wc-cp-state-added', [ added_obj ] );
						$states_panel.unblock();

					} );

				}, 250 );

				return false;
			} );
	}

	function init_state_metaboxes() {

		// State ordering.
		$state_metaboxes.sortable( {
			items: '.bto_state',
			cursor: 'move',
			axis: 'y',
			handle: '.sort-item',
			scrollSensitivity: 40,
			forcePlaceholderSize: true,
			helper: 'clone',
			opacity: 0.65,
			placeholder: 'wc-metabox-sortable-placeholder',
			start: function( event, ui ) {
				ui.item.css( 'background-color','#f6f6f6' );
			},
			stop: function( event, ui ) {
				ui.item.removeAttr( 'style' );
				$states_panel.triggerHandler( 'wc-cp-states-changed' );
			}
		} );

		update_states_toolbar_state();
	}

	function init_state_objects() {

		state_objects = {};

		// Create objects.
		$states.each( function( index ) {

			var $el   = $( this ),
				el_id = 'state_' + index;

			$el.data( 'state_metabox_id', el_id );
			state_objects[ el_id ] = new State( $el );
		} );

		// Initialize metaboxes.
		init_state_metaboxes();
	}

	function init_states() {

		// Attach event handlers.
		init_state_event_handlers();

		// Create objects.
		init_state_objects();
	}

	if ( has_states ) {

		init_states();

		/*
		 * Reload States when the view is dirty.
		 */

		// Replace anchor with new one.
		var $states_tab   = $( '.cp_states_tab' ),
		    $states_tab_a = $states_tab.find( 'a' );

		$states_tab.prepend( '<a class="cp_states_tab_inner" href="#bto_scenario_data"></a>' );

		var $states_tab_a_new = $states_tab.find( 'a.cp_states_tab_inner' );

		$states_tab_a_new.html( $states_tab_a.html() );

		// Handle click events.
		$states_tab_a_new.on( 'click', function() {

			if ( component_data_dirty && ! configuration_save_xhr ) {

				var reload_view = window.confirm( wc_composite_admin_params.i18n_states_panel_blocked );

				if ( reload_view ) {

					$states_tab_a.trigger( 'click' );

					setTimeout( function() {
						save_composite();
					}, 150 );
				}

			} else {
				$states_tab_a.trigger( 'click' );
			}

			return false;
		} );
	}

	/**
	 * Toggle enabled class and input value.
	 */
	function toggleActiveSwitch( $toggler, $input ) {

		if ( 'yes' === $input.val() ) {
			// Disable.
			$input.val( 'no' );
			$toggler.removeClass( 'woocommerce-input-toggle--enabled' ).addClass( 'woocommerce-input-toggle--disabled' );
		} else {
			// Enable.
			$input.val( 'yes' );
			$toggler.removeClass( 'woocommerce-input-toggle--disabled' ).addClass( 'woocommerce-input-toggle--enabled' );
		}
	}

	/*
	 * Save data and update configuration options via ajax.
	 */
	function save_composite() {

		$components_panel.block( block_params );
		$scenarios_panel.block( block_params );

		if ( has_states ) {
			$states_panel.block( block_params );
		}

		$components.find( '*' ).off();

		var data = {
			post_id:  woocommerce_admin_meta_boxes.post_id,
			data:     $( '#bto_product_data, #bto_scenario_data, #bto_state_data' ).find( 'input, select, textarea' ).serialize(),
			action:   'woocommerce_bto_composite_save',
			security: wc_composite_admin_params.save_composite_nonce
		};

		setTimeout( function() {

			configuration_save_xhr = $.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {

				if ( 'success' !== response.result ) {
					window.alert( wc_composite_admin_params.i18n_save_error );
				}

				var response_notices = response.notices,
					reponse_html     = response.html;

				var open_components               = [],
				    $components_group             = $( '#bto_config_group_inner', $components_panel ),
				    $components_group_in_response = $( reponse_html ).find( '#bto_config_group_inner' ),
				    $components_in_response       = $components_group_in_response.find( '.bto_group' ),

				    open_scenarios                = [],
				    $scenarios_group              = $( '#bto_scenarios_inner', $scenarios_panel ),
				    $scenarios_group_in_response  = $( reponse_html ).find( '#bto_scenarios_inner' ),
				    $scenarios_in_response        = $scenarios_group_in_response.find( '.bto_scenario' );

				var open_states               = [],
				    $states_group             = $( '#bto_states_inner', $states_panel ),
				    $states_group_in_response = $( reponse_html ).find( '#bto_states_inner' ),
				    $states_in_response       = $states_group_in_response.find( '.bto_state' );

				// Remember open/close state of Components.
				if ( $components.length === $components_in_response.length ) {

					// Make a list of open Components.
					$components.each( function() {

						var $el = $( this );

						if ( $el.hasClass( 'open' ) ) {
							var rel = $el.attr( 'rel' );
							open_components.push( rel );
						}
					} );
				}

				// Apply open/close state to Components in response.
				$components_in_response.each( function() {

					var $el = $( this ),
						rel = $el.attr( 'rel' );

					if ( $.inArray( rel, open_components ) !== -1 ) {
						$el.addClass( 'open' ).removeClass( 'closed' );
						$el.find( '.wc-metabox-content' ).show();
					} else {
						$el.find( '.wc-metabox-content' ).hide();
					}
				} );

				// Remember open/close state of Scenarios.
				if ( $scenarios.length === $scenarios_in_response.length ) {

					// Make a list of open Scenarios.
					$scenarios_group.find( '.bto_scenario' ).each( function() {

						var $el = $( this );

						if ( $el.hasClass( 'open' ) ) {
							var rel = $el.attr( 'rel' );
							open_scenarios.push( rel );
						}
					} );
				}

				// Apply open/close state to Scenarios in response.
				$scenarios_in_response.each( function() {

					var $el = $( this ),
						rel = $el.attr( 'rel' );

					if ( $.inArray( rel, open_scenarios ) !== -1 ) {
						$el.addClass( 'open' ).removeClass( 'closed' );
						$el.find( '.wc-metabox-content' ).show();
					} else {
						$el.find( '.wc-metabox-content' ).hide();
					}
				} );

				$components.find( '*' ).off();
				$scenarios.find( '*' ).off();

				$components_group.html( $components_group_in_response.html() );
				$scenarios_group.html( $scenarios_group_in_response.html() );

				$components_toggle_toolbar = $components_group.find( '.bulk_toggle_wrapper' );
				$scenarios_toggle_toolbar  = $scenarios_group.find( '.bulk_toggle_wrapper' );

				// Trigger change event.
				$components_panel.triggerHandler( 'wc-cp-components-changed' );

				// Create objects.
				init_component_objects();

				// Trigger change event.
				$scenarios_panel.triggerHandler( 'wc-cp-scenarios-changed' );

				// Create objects.
				init_scenario_objects();

				// select2 init - open components.
				$components.each( function() {

					var $el = $( this );

					if ( $el.hasClass( 'open' ) ) {

						var el_id     = $el.data( 'component_metabox_id' ),
							component = component_objects[ el_id ];

						component.initialize_content();
					}
				} );

				// select2 init - open scenarios.
				$scenarios.each( function() {

					var $el = $( this );

					if ( $el.hasClass( 'open' ) ) {

						var el_id    = $el.data( 'scenario_metabox_id' ),
							scenario = scenario_objects[ el_id ];

						scenario.initialize_content();
					}
				} );

				if ( has_states ) {

					// Remember open/close state of States.
					if ( $states.length === $states_in_response.length ) {

						// Make a list of open States.
						$states_group.find( '.bto_state' ).each( function() {

							var $el = $( this );

							if ( $el.hasClass( 'open' ) ) {
								var rel = $el.attr( 'rel' );
								open_states.push( rel );
							}
						} );
					}

					// Apply open/close state to Scenarios in response.
					$states_in_response.each( function() {

						var $el = $( this ),
							rel = $el.attr( 'rel' );

						if ( $.inArray( rel, open_states ) !== -1 ) {
							$el.addClass( 'open' ).removeClass( 'closed' );
							$el.find( '.wc-metabox-content' ).show();
						} else {
							$el.find( '.wc-metabox-content' ).hide();
						}
					} );

					$states.find( '*' ).off();

					$states_group.html( $states_group_in_response.html() );

					$states_toggle_toolbar  = $states_group.find( '.bulk_toggle_wrapper' );

					// Trigger change event.
					$states_panel.triggerHandler( 'wc-cp-states-changed' );

					// Create objects.
					init_state_objects();

					// select2 init - open states.
					$states.each( function() {

						var $el = $( this );

						if ( $el.hasClass( 'open' ) ) {

							var el_id    = $el.data( 'state_metabox_id' ),
								state = state_objects[ el_id ];

							state.initialize_content();
						}
					} );
				}

				if ( response_notices.length > 0 ) {
					$.each( response_notices, function( index, part ) {
						window.alert( part );
					} );
				}

				$components_panel.unblock();
				$scenarios_panel.unblock();

				if ( has_states ) {
					$states_panel.unblock();
				}

				configuration_save_xhr = false;

				set_component_data_dirty_state( false );

			}, 'json' );

		}, 250 );

	}

	function init_nux() {

		if ( 'yes' === wc_composite_admin_params.is_first_composite ) {
			$product_type_select.val( 'composite' ).trigger( 'change' ).trigger( 'focus' );
			setTimeout( function() {
				$( '.composite_product_options a' ).trigger( 'click' );
			}, 500 );
		}
	}

	init_nux();

	function init_composite_shipping() {

		var $shipping_data_container  = $components_panel.parent().find( '#shipping_product_data' ),
			$virtual_checkbox         = $( 'input#_virtual' ),
			$composite_type_container = $shipping_data_container.find( '.options_group.composite_type' ),
			$composite_type_options   = $composite_type_container.find( '.bto_type_options li' ),
			virtual_state             = $( 'input#_virtual:checked' ).length ? true : false;

		// Move Bundle type options group first.
		$composite_type_container.detach().prependTo( $shipping_data_container );

		// Move "Assembled Weight" field next to the Weight field.
		$shipping_data_container.find( '.form-field._weight_field' ).after( $composite_type_container.find( '.form-field.composite_aggregate_weight_field' ) );

		// Save virtual state.
		$virtual_checkbox.on( 'change', function() {
			if ( 'composite' !== $product_type_select.val() && 'bundle' !== $product_type_select.val() ) {
				virtual_state = $( this ).prop( 'checked' ) ? true : false;
			}
		} );

		$( 'body' ).on( 'woocommerce-product-type-change', function( event, select_val ) {

			if ( 'composite' !== select_val ) {
				// Restore virtual state.
				if ( 'simple' === select_val ) {
					$virtual_checkbox.prop( 'checked', virtual_state ).trigger( 'change' );
				}
			}

		} );

		// Toggle container shipping class.
		// Container classes are removed conditionaly using inline JS. @see WC_CP_Meta_Box_Product_Data::js_handle_container_classes()
		$composite_type_options.on( 'click', function() {

			var $option = $( this ),
				$input  = $option.find( 'input' ),
				value   = $input.prop( 'checked', 'checked' ).val();

			// Highlight selected.
			$composite_type_options.removeClass( 'selected' );
			$option.addClass( 'selected' );

			if ( 'assembled' === value ) {
				$shipping_data_container.removeClass( 'composite_unassembled' );
				$components_panel.removeClass( 'composite_unassembled' );
			} else if ( 'unassembled' === value ) {
				$shipping_data_container.addClass( 'composite_unassembled' );
				$components_panel.addClass( 'composite_unassembled' );
			}

		} );
	}

	init_composite_shipping();

} );
