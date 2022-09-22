/* global wc_bundles_admin_params */
/* global woocommerce_admin_meta_boxes */

jQuery( function( $ ) {

	function Bundled_Item( $el ) {

		var self = this;

		this.$element                        = $el;
		this.$content                        = $el.find( 'div.item-data' );
		this.$discount                       = this.$content.find( '.discount' );
		this.$visibility                     = this.$content.find( '.item_visibility' );
		this.$price_visibility               = this.$content.find( '.price_visibility' );
		this.$allowed_variations             = this.$content.find( 'div.allowed_variations' );
		this.$default_variation_attributes   = this.$content.find( 'div.default_variation_attributes' );
		this.$custom_title                   = this.$content.find( 'div.custom_title' );
		this.$custom_description             = this.$content.find( 'div.custom_description' );
		this.$override_title                 = this.$content.find( '.override_title' );
		this.$override_description           = this.$content.find( '.override_description' );
		this.$hide_thumbnail                 = this.$content.find( '.hide_thumbnail' );

		this.$section_links                  = this.$content.find( '.subsubsub a' );
		this.$sections                       = this.$content.find( '.options_group' );

		this.$priced_individually_input      = this.$content.find( '.priced_individually input' );
		this.$override_variations_input      = this.$content.find( '.override_variations input' );
		this.$override_defaults_input        = this.$content.find( '.override_default_variation_attributes input' );
		this.$override_title_input           = this.$override_title.find( 'input' );
		this.$override_description_input     = this.$override_description.find( 'input' );

		this.$price_visibility_product_input = this.$price_visibility.find( 'input.price_visibility_product' );
		this.$price_visibility_cart_input    = this.$price_visibility.find( 'input.price_visibility_cart' );
		this.$price_visibility_order_input   = this.$price_visibility.find( 'input.price_visibility_order' );

		this.$visibility_product_input       = this.$visibility.find( 'input.visibility_product' );
		this.$visibility_cart_input          = this.$visibility.find( 'input.visibility_cart' );
		this.$visibility_order_input         = this.$visibility.find( 'input.visibility_order' );

		this.$min_qty                        = this.$content.find( '.quantity_min' );
		this.$min_qty_input                  = this.$min_qty.find( 'input.item_quantity' );
		this.$max_qty                        = this.$content.find( '.quantity_max' );
		this.$max_qty_input                  = this.$max_qty.find( 'input.item_quantity' );
		this.$default_qty                    = this.$content.find( '.quantity_default' );
		this.$default_qty_input              = this.$default_qty.find( 'input.item_quantity' );

		this.$optional_checkbox              = this.$content.find( 'div.optional' );
		this.$optional_checkbox_input        = this.$optional_checkbox.find( 'input.checkbox' );

		this.max_qty_prev                    = this.$max_qty_input.val();
		this.default_qty_prev                = this.$default_qty_input.val();

		this.add_error_tip = function( $target, error ) {

			var offset = $target.position();

			if ( $target.parent().find( '.wc_error_tip' ).length === 0 ) {
				$target.after( '<div class="wc_error_tip">' + error + '</div>' );
				$target.parent().find( '.wc_error_tip' )
					.css( 'left', offset.left + $target.width() - ( $target.width() / 2 ) - ( $( '.wc_error_tip' ).width() / 2 ) )
					.css( 'top', offset.top + $target.height() + 4 )
					.fadeIn( '100' );
			} else {
				$target.parent().find( '.wc_error_tip' ).html( error );
			}
		};

		this.remove_error_tip = function( $target ) {

			$target.parent().find( '.wc_error_tip' ).fadeOut( '100', function() { $( this ).remove(); } );
		};

		this.maybe_hide_optional_checkbox = function( target ) {

			if ( 'min' === target ) {
				var min = self.$min_qty_input.val();
				if ( min > 0 ) {
					self.$optional_checkbox.show();
					self.$optional_checkbox_input.prop( 'disabled', false );
				} else if ( self.$optional_checkbox.data( 'is_optional_qty_zero' ) && 'no' === self.$optional_checkbox.data( 'is_optional_qty_zero' ) ) {
					self.$optional_checkbox.hide();
					self.$optional_checkbox_input.prop( 'disabled', true );
				}
			}
		};

		this.validate_quantity = function( target, context ) {

			var $input = self.$min_qty_input;

			if ( 'max' === target ) {
				$input = self.$max_qty_input;
			} else if ( 'default' === target ) {
				$input = self.$default_qty_input;
			}

			var qty    = $input.val(),
			    min    = parseFloat( $input.attr( 'min' ) ),
			    max    = parseFloat( $input.attr( 'max' ) ),
			    step   = parseFloat( $input.attr( 'step' ) ),
			    result = {
			    	qty:   qty,
			    	error: ''
			    };

			if ( min >= 0 && ( qty < min || isNaN( qty ) ) ) {

				// The max field doesn't have a max value. Also when validating the max field there's no need to validate the empty string.
				if ( 'max' !== target || qty !== '' ) {

					if ( 'max' === context ) {
						result.qty = self.max_qty_prev;
					} else if ( 'default' === context ) {
						result.qty = self.default_qty_prev;
					} else {
						result.qty = min;
					}

					result.error = wc_bundles_admin_params.i18n_qty_low_error.replace( '%s', min );
				}

				return result;
			}

			if ( max > 0 && qty > max ) {

				if ( 'default' === context ) {
					result.qty = self.default_qty_prev;
				} else {
					result.qty = max;
				}

				result.error = wc_bundles_admin_params.i18n_qty_high_error.replace( '%s', max );

				return result;
			}

			if ( step > 0 && qty > 0 ) {

				if ( qty % step ) {
					result.qty   = step * Math.ceil( qty / step );
					result.error = wc_bundles_admin_params.i18n_qty_step_error.replace( '%s', step );
				}

				return result;
			}

			return result;
		};

		this.priced_individually_input_changed = function() {
			if ( self.$priced_individually_input.is( ':checked' ) ) {
				self.$discount.show();
				self.$price_visibility.show();
			} else {
				self.$discount.hide();
				self.$price_visibility.hide();
			}
		};

		this.override_variations_input_changed = function() {
			if ( self.$override_variations_input.is( ':checked' ) ) {
				self.$allowed_variations.show();
			} else {
				self.$allowed_variations.hide();
			}
		};

		this.override_defaults_input_changed = function() {
			if ( self.$override_defaults_input.is( ':checked' ) ) {
				self.$default_variation_attributes.show();
			} else {
				self.$default_variation_attributes.hide();
			}
		};

		this.override_title_input_changed = function() {
			if ( self.$override_title_input.is( ':checked' ) ) {
				self.$custom_title.show();
			} else {
				self.$custom_title.hide();
			}
		};

		this.override_description_input_changed = function() {
			if ( self.$override_description_input.is( ':checked' ) ) {
				self.$custom_description.show();
			} else {
				self.$custom_description.hide();
			}
		};

		this.visibility_product_input_changed = function() {
			if ( self.$visibility_product_input.is( ':checked' ) ) {

				self.$override_title.show();
				self.$override_description.show();
				self.$hide_thumbnail.show();

				self.override_title_input_changed();
				self.override_description_input_changed();

			} else {

				self.$override_title.hide();
				self.$override_description.hide();
				self.$hide_thumbnail.hide();

				self.$custom_description.hide();
				self.$custom_title.hide();
			}
		};

		this.toggled_visibility = function( visibility_class ) {

			if ( self[ '$visibility_' + visibility_class + '_input' ].is( ':checked' ) ) {
				self[ '$price_visibility_' + visibility_class + '_input' ].css( 'opacity', 1 );
			} else {
				self[ '$price_visibility_' + visibility_class + '_input' ].css( 'opacity', 0.5 );
			}

		};

		this.section_changed = function( $section_link ) {

			self.$section_links.removeClass( 'current' );
			$section_link.addClass( 'current' );

			self.$sections.addClass( 'options_group_hidden' );
			self.$content.find( '.options_group_' + $section_link.data( 'tab' ) ).removeClass( 'options_group_hidden' );
		};

		this.initialize = function() {

			self.priced_individually_input_changed();
			self.override_variations_input_changed();
			self.override_defaults_input_changed();
			self.override_title_input_changed();
			self.override_description_input_changed();
			self.visibility_product_input_changed();

			self.toggled_visibility( 'product' );
			self.toggled_visibility( 'cart' );
			self.toggled_visibility( 'order' );

			self.$element.sw_select2();
		};

		this.initialize();
	}

	var $edit_in_cart                 = $( 'p._wc_pb_edit_in_cart_field' ),
		$product_type_select          = $( 'select#product-type' ),
		$group_mode_select            = $( 'select#_wc_pb_group_mode' ),
		$bundled_products_panel       = $( '#bundled_product_data' ),
		$bundled_products_wrapper     = $bundled_products_panel.find( '.wc-metaboxes-wrapper' ),
		$bundled_products_toolbar     = $bundled_products_panel.find( '.toolbar' ),
		$bundled_products_container   = $( '.wc-bundled-items' ),
		$bundled_products             = $( '.wc-bundled-item', $bundled_products_container ),
		$bundled_product_search       = $( '#bundled_product', $bundled_products_panel ),
		bundled_product_objects       = {},
		bundled_products_add_count    = $bundled_products.length,
		block_params                  = {
			message: 	null,
			overlayCSS: {
				background: '#fff',
				opacity: 	0.6
			}
		};

	var $shipping_data_container  = $bundled_products_panel.parent().find( '#shipping_product_data' ),
		$virtual_checkbox         = $( 'input#_virtual' ),
		virtual_checkbox_init_val = $virtual_checkbox.prop( 'checked' ),
		is_virtual_checkbox_dirty = false,
		$virtual_bundle_checkbox  = $( 'input#_virtual_bundle' ),
		$bundle_type_container    = $shipping_data_container.find( '.options_group.bundle_type' ),
		$bundle_type_options      = $bundle_type_container.find( '.bundle_type_options li' );

	$.fn.wc_bundles_select2 = function() {
		$( document.body ).trigger( 'wc-enhanced-select-init' );
	};

	// Bundle type move stock msg up.
	$( '.bundle_stock_msg' ).appendTo( '._manage_stock_field .description' );

	// Hide the default "Sold Individually" field.
	$( '#_sold_individually' ).closest( '.form-field' ).addClass( 'hide_if_bundle' );

	// Hide the "Grouping" field.
	$( '#linked_product_data .grouping.show_if_simple, #linked_product_data .form-field.show_if_grouped' ).addClass( 'hide_if_bundle' );

	// Simple type options are valid for bundles.
	$( '.show_if_simple:not(.hide_if_bundle)' ).addClass( 'show_if_bundle' );

	init_event_handlers();

	init_bundled_products();

	init_bundle_shipping();

	init_nux();

	init_expanding_button();

	// Trigger product type change.
	$product_type_select.trigger( 'change' );

	// Trigger group mode change.
	$group_mode_select.trigger( 'change' );

	function init_event_handlers() {

		// Bundle type specific options.
		$( 'body' ).on( 'woocommerce-product-type-change', function( event, select_val ) {

			if ( 'bundle' === select_val ) {

				$( '.show_if_external' ).hide();
				$( '.show_if_bundle' ).show();

				$( 'input#_manage_stock' ).trigger( 'change' );

				$( '#_nyp' ).trigger( 'change' );

				if ( is_virtual_checkbox_dirty ) {
					$virtual_bundle_checkbox.prop( 'checked', $virtual_checkbox.prop( 'checked' ) );
				}

				// Force virtual container to always show the shipping tab.
				if ( ! $virtual_bundle_checkbox.prop( 'checked' ) ) {
					$virtual_checkbox.prop( 'checked', false ).change();
				}

				if ( 'unassembled' === $bundle_type_options.find( 'input.bundle_type_option:checked' ).first().val() ) {
					$shipping_data_container.addClass( 'bundle_unassembled' );
					$bundled_products_panel.addClass( 'bundle_unassembled' );
				}
			}

		} );

		// On submit, post two inputs to determine if 'max_input_vars' kicks in: One at the start of the form (control) and one at the end (test).
		$( 'form#post' ).on( 'submit', function() {

			if ( 'bundle' === $product_type_select.val() ) {

				var $form        = $( this ),
				    $control_var = $( '<input type="hidden" name="pb_post_control_var" value="1"/>' ),
				    $test_var    = $( '<input type="hidden" name="pb_post_test_var" value="1"/>' );

				$form.prepend( $control_var );
				$form.append( $test_var );
			}
		} );

		// Show/hide 'Edit in cart' option.
		$group_mode_select.on( 'change', function() {
			if ( $.inArray( $group_mode_select.val(), wc_bundles_admin_params.group_modes_with_parent ) === -1 ) {
				$edit_in_cart.hide();
			} else {
				$edit_in_cart.show();
			}
		} );

		// Downloadable support.
		$( 'input#_downloadable' ).on( 'change', function() {
			$product_type_select.trigger( 'change' );
		} );


		// Add Product.
		$bundled_product_search

			.on( 'change', function() {

				var bundled_product_ids = $bundled_product_search.val(),
					bundled_product_id  = bundled_product_ids && bundled_product_ids.length > 0 ? bundled_product_ids.shift() : false;

				if ( ! bundled_product_id ) {
					return false;
				}

				$bundled_product_search.val( [] ).trigger( 'change' );

				$bundled_products_panel.block( block_params );

				bundled_products_add_count++;

				var data = {
					action: 	'woocommerce_add_bundled_product',
					post_id: 	woocommerce_admin_meta_boxes.post_id,
					id: 		bundled_products_add_count,
					product_id: bundled_product_id,
					security: 	wc_bundles_admin_params.add_bundled_product_nonce
				};

				setTimeout( function() {

					$.post( woocommerce_admin_meta_boxes.ajax_url, data, function ( response ) {

						if ( '' !== response.markup ) {

							$bundled_products_container.append( response.markup );

							var $added   = $( '.wc-bundled-item', $bundled_products_container ).last(),
								added_id = 'bundled_item_' + bundled_products_add_count;

							$added.data( 'bundled_item_id', added_id );
							bundled_product_objects[ added_id ] = new Bundled_Item( $added );

							$bundled_products_panel.triggerHandler( 'wc-bundled-products-changed' );

							$added.find( '.woocommerce-help-tip' ).tipTip( {
								'attribute' : 'data-tip',
								'fadeIn' : 50,
								'fadeOut' : 50,
								'delay' : 200
							} );

							$added.wc_bundles_select2();

							$bundled_products_panel.trigger( 'wc-bundles-added-bundled-product' );

						} else if ( response.message !== '' ) {
							window.alert( response.message );
						}

						$bundled_product_search.selectSW( 'open' );
						$bundled_product_search.selectSW( 'close' );

						$bundled_products_panel.unblock();

					} );

				}, 250 );

				return false;

			} );

		$bundled_products_wrapper

			// Expand all.
			.on( 'click', '.expand_all', function() {

				if ( $( this ).hasClass( 'disabled' ) ) {
					return false;
				}

				$.each( bundled_product_objects, function( index, bundled_product_object ) {
					bundled_product_object.$element.addClass( 'open' ).removeClass( 'closed' );
				} );

				return false;
			} )

			// Close all.
			.on( 'click', '.close_all', function() {

				if ( $( this ).hasClass( 'disabled' ) ) {
					return false;
				}

				$.each( bundled_product_objects, function( index, bundled_product_object ) {
					bundled_product_object.$element.addClass( 'closed' ).removeClass( 'open' );
				} );

				return false;
			} );

		$bundled_products_panel

			// Update menu order and toolbar states.
			.on( 'wc-bundled-products-changed', function() {

				$bundled_products = $( '.wc-bundled-item', $bundled_products_container );

				$bundled_products.each( function( index, el ) {
					$( '.item_menu_order', el ).val( index );
				} );

				update_toolbar_state();

			} )

			// Remove onboarding elements when adding bundled product.
			.one( 'wc-bundles-added-bundled-product', function() {
				$bundled_products_wrapper.removeClass( 'wc-bundle-metaboxes-wrapper--boarding' );
			} );

		$bundled_products_container

			// Validate quantities.
			.on( 'input change', 'input.item_quantity', function( e ) {

				var $input          = $( this ),
				    $el             = $input.closest( '.wc-bundled-item' ),
				    el_id           = $el.data( 'bundled_item_id' ),
				    bundled_product = bundled_product_objects[ el_id ];

				var changed = 'min';

				if ( $input.hasClass( 'item_quantity_max' ) ) {
					changed = 'max';
				} else if ( $input.hasClass( 'item_quantity_default' ) ) {
					changed = 'default';
				}

				var check = bundled_product.validate_quantity( changed, changed );

				// Is there an error?
				if ( check.error ) {

					// Show an error while typing, or replace the typed value with the corrected one on blur/change.
					if ( 'input' === event.type ) {

						// Add error.
						setTimeout( function() {
							bundled_product.add_error_tip( $input, check.error );
						}, 5 );

					} else {

						$input.val( check.qty ).change();
					}

				// Valid value?
				} else {

					// Clear existing errors.
					bundled_product.remove_error_tip( $input );

					// Update max/default inputs.
					if ( 'change' === event.type ) {

						// Check and update min/max attribute values.
						var min = bundled_product.$min_qty_input.val(),
						    max = bundled_product.$max_qty_input.val(),
						    def = bundled_product.$default_qty_input.val();

						bundled_product.$max_qty_input.attr( 'min', min );

						// Changes to the default input should never affect the max, even if invalid.
						if ( 'default' !== changed ) {

							check = bundled_product.validate_quantity( 'max', changed );

							if ( check.error ) {
								bundled_product.$max_qty_input.val( check.qty );
								max = check.qty;
							}

							bundled_product.max_qty_prev = max;
						}

						bundled_product.$default_qty_input.attr( 'min', min );
						bundled_product.$default_qty_input.attr( 'max', max );

						check = bundled_product.validate_quantity( 'default', changed );

						if ( check.error ) {
							bundled_product.$default_qty_input.val( check.qty );
							def = check.qty;
						}

						bundled_product.default_qty_prev = def;

						bundled_product.maybe_hide_optional_checkbox( changed );
					}
				}
			} )

			// Click to Edit.
			.on( 'click', 'a.edit-product', function( e ) {
				e.stopPropagation();
			} )

			// Remove Item.
			.on( 'click', 'a.remove_row', function( e ) {

				var $el   = $( this ).closest( '.wc-bundled-item' ),
					el_id = $el.data( 'bundled_item_id' );

				$el.find( '*' ).off();
				$el.remove();

				delete bundled_product_objects[ el_id ];

				$bundled_products_panel.triggerHandler( 'wc-bundled-products-changed' );

				e.preventDefault();

			} )

			// Priced individually.
			.on( 'change', '.priced_individually input', function() {

				var $el             = $( this ).closest( '.wc-bundled-item' ),
					el_id           = $el.data( 'bundled_item_id' ),
					bundled_product = bundled_product_objects[ el_id ];

				bundled_product.priced_individually_input_changed();
			} )

			// Variation filtering options.
			.on( 'change', '.override_variations input', function() {

				var $el             = $( this ).closest( '.wc-bundled-item' ),
					el_id           = $el.data( 'bundled_item_id' ),
					bundled_product = bundled_product_objects[ el_id ];

				bundled_product.override_variations_input_changed();
			} )

			// Selection defaults options.
			.on( 'change', '.override_default_variation_attributes input', function() {

				var $el             = $( this ).closest( '.wc-bundled-item' ),
					el_id           = $el.data( 'bundled_item_id' ),
					bundled_product = bundled_product_objects[ el_id ];

				bundled_product.override_defaults_input_changed();
			} )

			// Custom title options.
			.on( 'change', '.override_title input', function() {

				var $el             = $( this ).closest( '.wc-bundled-item' ),
					el_id           = $el.data( 'bundled_item_id' ),
					bundled_product = bundled_product_objects[ el_id ];

				bundled_product.override_title_input_changed();
			} )

			// Custom description options.
			.on( 'change', '.override_description input', function() {

				var $el             = $( this ).closest( '.wc-bundled-item' ),
					el_id           = $el.data( 'bundled_item_id' ),
					bundled_product = bundled_product_objects[ el_id ];

				bundled_product.override_description_input_changed();
			} )

			// Visibility.
			.on( 'change', 'input.visibility_product', function() {

				var $el             = $( this ).closest( '.wc-bundled-item' ),
					el_id           = $el.data( 'bundled_item_id' ),
					bundled_product = bundled_product_objects[ el_id ];

				bundled_product.visibility_product_input_changed();
				bundled_product.toggled_visibility( 'product' );
			} )

			.on( 'change', 'input.visibility_cart', function() {

				var $el             = $( this ).closest( '.wc-bundled-item' ),
					el_id           = $el.data( 'bundled_item_id' ),
					bundled_product = bundled_product_objects[ el_id ];

				bundled_product.toggled_visibility( 'cart' );
			} )

			.on( 'change', 'input.visibility_order', function() {

				var $el             = $( this ).closest( '.wc-bundled-item' ),
					el_id           = $el.data( 'bundled_item_id' ),
					bundled_product = bundled_product_objects[ el_id ];

				bundled_product.toggled_visibility( 'order' );
			} )

			// Sections.
			.on( 'click', '.subsubsub a', function( event ) {

				var $section_link   = $( this ),
					$el             = $( this ).closest( '.wc-bundled-item' ),
					el_id           = $el.data( 'bundled_item_id' ),
					bundled_product = bundled_product_objects[ el_id ];

				bundled_product.section_changed( $section_link );

				event.preventDefault();

			} );

	}

	function init_bundled_products() {

		// Create objects.
		$bundled_products.each( function( index ) {

			var $el   = $( this ),
				el_id = 'bundled_item_' + index;

			$el.data( 'bundled_item_id', el_id );
			bundled_product_objects[ el_id ] = new Bundled_Item( $el );
		} );

		// Item ordering.
		$bundled_products_container.sortable( {
			items: '.wc-bundled-item',
			cursor: 'move',
			axis: 'y',
			handle: '.sort-item',
			scrollSensitivity: 40,
			forcePlaceholderSize: true,
			helper: 'clone',
			opacity: 0.65,
			placeholder: 'wc-metabox-sortable-placeholder',
			start:function( event, ui ){
				ui.item.css( 'background-color','#f6f6f6' );
			},
			stop:function( event, ui ){
				ui.item.removeAttr( 'style' );
				$bundled_products_panel.triggerHandler( 'wc-bundled-products-changed' );
			}
		} );

		// Expand/collapse toolbar state.
		update_toolbar_state();
	}

	function init_nux() {

		if ( 'yes' === wc_bundles_admin_params.is_first_bundle ) {
			$product_type_select.val( 'bundle' ).trigger( 'change' ).trigger( 'focus' );
			setTimeout( function() {
				$( '.bundled_products_tab a' ).trigger( 'click' );
			}, 500 );
		}
	}

	function update_toolbar_state() {

		if ( $bundled_products.length > 0 ) {
			$bundled_products_wrapper.removeClass( 'no-items' );
			$bundled_products_toolbar.removeClass( 'disabled' );
		} else {
			$bundled_products_wrapper.addClass( 'no-items' );
			$bundled_products_toolbar.addClass( 'disabled' );
		}
	}

	function init_expanding_button() {

		var focus_timer,
		    $button_container = $bundled_products_panel.find( '.add_bundled_product' ),
		    $button           = $button_container.find( '.sw-expanding-button' ),
		    $body             = $( document.body );

		if ( ! $button.length ) {
			$button_container.sw_select2();
			return;
		}

		$button.sw_select2();

		$button.on( 'click', function( e ) {

			e.stopPropagation();

			clearTimeout( focus_timer );

			var $this  = $( this ),
				$input = $this.find( '.select2-search__field' );

			$this.addClass( 'sw-expanding-button--open' );

			focus_timer = setTimeout( function() {
				$input.trigger( 'focus' );
			}, 700 );

			$bundled_product_search.one( 'change', function() {
				$this.removeClass( 'sw-expanding-button--open' );
			} );

		} );

		$body.on( 'click', '.select2-container', function( e ) {
			e.stopPropagation();
		} );

		$body.on( 'click', function() {
			$button.removeClass( 'sw-expanding-button--open' );
		} );
	}

	function init_bundle_shipping() {

		// Move Bundle type options group first.
		$bundle_type_container.detach().prependTo( $shipping_data_container );

		// Move "Assembled Weight" to the Weight field.
		$shipping_data_container.find( '.form-field._weight_field' ).after( $bundle_type_container.find( '.form-field.bundle_aggregate_weight_field' ) );

		// Keep virtual checkbox in sync with ours.
		$virtual_bundle_checkbox.on( 'change', function() {

			var is_checked = $virtual_bundle_checkbox.prop( 'checked' );

			if ( $virtual_checkbox.prop( 'checked' ) !== is_checked ) {
				$virtual_checkbox.prop( 'checked', is_checked ).trigger( 'change' );
			}

			if ( is_checked ) {
				$bundled_products_panel.addClass( 'bundle_virtual' );
			} else {
				$bundled_products_panel.removeClass( 'bundle_virtual' );
			}
		} );

		// Determine when the virtual checkbox has become dirty.
		$virtual_checkbox.on( 'change', function() {
			if ( $virtual_checkbox.prop( 'checked' ) !== virtual_checkbox_init_val ) {
				is_virtual_checkbox_dirty = true;
			}
		} );

		// Toggle container shipping class.
		// Container classes are removed conditionaly using inline JS. @see WC_PB_Meta_Box_Product_Data::js_handle_container_classes()
		$bundle_type_options.on( 'click', function() {

			var $option = $( this ),
				$input  = $option.find( 'input' ),
				value   = $input.prop( 'checked', 'checked' ).val();

			// Highlight selected.
			$bundle_type_options.removeClass( 'selected' );
			$option.addClass( 'selected' );

			if ( 'assembled' === value ) {
				$shipping_data_container.removeClass( 'bundle_unassembled' );
				$bundled_products_panel.removeClass( 'bundle_unassembled' );
			} else if ( 'unassembled' === value ) {
				$shipping_data_container.addClass( 'bundle_unassembled' );
				$bundled_products_panel.addClass( 'bundle_unassembled' );
			}

		} );
	}

} );
