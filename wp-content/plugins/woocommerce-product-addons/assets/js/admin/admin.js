jQuery( function( $ ) {
	'use strict';

	var wc_pao_admin = {
		getAddonOptions: function() {
			var data = {
				security:  wc_pao_params.nonce.get_addon_options,
				action: 'wc_pao_get_addon_options',
			};

			return $.ajax( {
				type:    'POST',
				data:    data,
				url:     wc_pao_params.ajax_url
			} );
		},

		getAddonField: function( field_type, addon ) {
			var data = {
				security:  wc_pao_params.nonce.get_addon_field,
				action: 'wc_pao_get_addon_field',
				field_type: field_type,
				addon: addon
			};

			return $.ajax( {
				type:    'POST',
				data:    data,
				url:     wc_pao_params.ajax_url
			} );
		},

		getBlockParams: function() {
			return {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			};
		},

		refresh: function() {
			var addons              = $( '.wc-pao-addon' ).length,
				$addons_tyoe_select = $( '#product_addons_data select.addon_field_type' );

			if ( 0 < addons ) {
				$( '.wc-pao-toolbar' ).addClass( 'wc-pao-has-addons' );
				$( '.wc-pao-toolbar .wc-pao-import-addons' ).show();
				$( '.wc-pao-toolbar .wc-pao-export-addons' ).show();
				$( '.wc-pao-addons' ).addClass( 'wc-pao-has-addons' );
				$( '#product_addons_data' ).removeClass( 'onboarding' );
				$( '.addon_fields_container' ).removeClass( 'pao_boarding__addons' );
				$( '.pao_boarding__addons__message' ).hide();
			} else {
				$( '.wc-pao-toolbar' ).removeClass( 'wc-pao-has-addons' );
				$( '.wc-pao-addons' ).removeClass( 'wc-pao-has-addons' );
				$( '.wc-pao-toolbar .wc-pao-import-addons' ).hide();
				$( '.wc-pao-toolbar .wc-pao-export-addons' ).hide();
				$( '#product_addons_data' ).addClass( 'onboarding' );
				$( '.addon_fields_container' ).addClass( 'pao_boarding__addons' );
				$( '.pao_boarding__addons__message' ).show();
			}

			$addons_tyoe_select.find( 'option[value="add"]' ).prop( 'selected', 'selected' );
			$addons_tyoe_select.blur();

			$( document.body ).trigger( 'init_tooltips' );
		},

		expandAllFields: function() {
			var $element = $( '#product_addons_data .wc-pao-addon' );
			setTimeout( function() {
				$element.removeClass( 'closed' ).addClass( 'open' );
				$element.find( '.wc-pao-addon-content' ).show();
			}, 50 );
		},

		closeAllFields: function() {
			var $element = $( '#product_addons_data .wc-pao-addon' );
			setTimeout( function() {
				$element.removeClass( 'open' ).addClass( 'closed' );
				$element.find( '.wc-pao-addon-content' ).hide();
			}, 50 );
		},

		addonRowIndexes: function() {
			$( '.wc-pao-addons .wc-pao-addon' ).each( function( index, el ) {
				$( '.wc-pao-addon-position', el ).val( parseInt( $( el ).index( '.wc-pao-addons .wc-pao-addon' ) ) );
			} );
		},

		runFieldSortable: function() {
			$( '.wc-pao-addons' ).sortable( {
				items: '.wc-pao-addon',
				cursor: 'move',
				axis: 'y',
				handle: '.wc-pao-addon-sort-handle',
				scrollSensitivity: 40,
				helper: function( e, ui ) {
					return ui;
				},
				start: function( event, ui ) {
					ui.item.css( 'border-style', 'dashed' ).css( 'border-color', 'orange' );
				},
				stop: function( event, ui ) {
					ui.item.removeAttr( 'style' );
					wc_pao_admin.addonRowIndexes();
				}
			} );
		},

		runOptionSortable: function() {
			$( '.wc-pao-addon-content-options-container' ).sortable( {
				items: '.wc-pao-addon-option-row',
				cursor: 'move',
				axis: 'y',
				handle: '.wc-pao-addon-sort-handle',
				scrollSensitivity: 40,
				helper: function( e, ui ) {
					return ui;
				},
				start: function( event, ui ) {
					ui.item.css( 'border-style', 'dashed' ).css( 'border-width', '1px' ).css( 'border-color', 'orange' );
				},
				stop: function( event, ui ) {
					ui.item.removeAttr( 'style' );
					wc_pao_admin.addonRowIndexes();
				}
			} );
		},

		validateSettings: function( fields = 'all' ) {
			$( '.wc-pao-error-message' ).remove();
			$( '.updated' ).remove();

			var shouldReturn      = true,
				removeErrorBorder = true,
				requiredError     = false,
				maxLengthError    = false;

			// Loop through all addons to validate them.
			$( '.wc-pao-addons' ).find( '.wc-pao-addon' ).each( function( i ) {

				if ( 'all' === fields || 'addon_title' === fields ) {
					var titleLength = $( this ).find( '.wc-pao-addon-content-name' ).val().length;

					if ( 0 === titleLength ) {
						$( this ).addClass( 'wc-pao-error' ).find( '.wc-pao-addon-content-name' ).addClass( 'wc-pao-error' );

						shouldReturn      = false;
						removeErrorBorder = false;
						requiredError     = true;
					} else if ( titleLength > 255 ) {
						$( this ).addClass( 'wc-pao-error' ).find( '.wc-pao-addon-content-name' ).addClass( 'wc-pao-error' );

						shouldReturn      = false;
						removeErrorBorder = false;
						maxLengthError    = true;
					} else {
						$( this ).removeClass( 'wc-pao-error' ).find( '.wc-pao-addon-content-name' ).removeClass( 'wc-pao-error' );
						$(this).find( '.wc-pao-addon-header .woocommerce-help-tip.addons-max-length-title' ).hide();
					}
				}

				if ( 'all' === fields || 'addon_option_title' === fields ) {
					var type = $( this ).find( '.product_addon_type' ).val();

					$( this ).find( '.wc-pao-addon-option-row' ).each( function() {
						if ( ( 'multiple_choice' === type || 'checkbox' === type ) && 0 === $( this ).find( '.wc-pao-addon-content-label input' ).val().length ) {

							$( this ).find( '.wc-pao-addon-content-label input' ).addClass( 'wc-pao-error' );
							$( this ).parents( '.wc-pao-addon' ).eq( 0 ).addClass( 'wc-pao-error' );

							shouldReturn      = false;
							removeErrorBorder = false;
							requiredError     = true;
						} else {
							$( this ).find( '.wc-pao-addon-content-label input' ).removeClass( 'wc-pao-error' );
						}
					} );
				}

				if ( removeErrorBorder ) {
					$( this ).removeClass( 'wc-pao-error' );
				}
			} );

			if ( false === shouldReturn ) {
				var errorMessage;

				if ( requiredError ) {
					errorMessage = $( '<div class="notice notice-error wc-pao-error-message"><p>' + wc_pao_params.i18n.required_fields + '</p></div>' );
				} else if ( maxLengthError ) {
					errorMessage = $( '<div class="notice notice-error wc-pao-error-message"><p>' + wc_pao_params.i18n.max_title_length_exceeded + '</p></div>' );
				}

				$( '.wc-pao-addons' ).before( errorMessage );

				$( 'html, body' ).animate( {
					scrollTop: ( $( '.wc-pao-error-message' ).offset().top - 200 )
				}, 600 );
			}

			return shouldReturn;
		},

		render: function( selectedValue, $addon ) {
			var restrictionName;

			// Default show title format select.
			$addon.find( '.wc-pao-addon-main-settings-1 .wc-pao-col1' ).removeClass( 'full' );
			$addon.find( '.wc-pao-addon-main-settings-1 .wc-pao-col2' ).removeClass( 'hide' ).addClass( 'show' );

			// Default hide images column.
			$addon.find( '.wc-pao-addon-content-image' ).removeClass( 'show' ).addClass( 'hide' );
			$addon.find( '.wc-pao-addon-content-label' ).addClass( 'full' );

			// Default restriction adjustment type.
			$addon.find( '.wc-pao-addon-restrictions-select' ).removeClass( 'show' ).addClass( 'hide' );

			// Default hide display type select column.
			$addon.find( '.wc-pao-addon-main-settings-2 .wc-pao-col1-1' ).removeClass( 'show' ).addClass( 'hide' );
			$addon.find( '.wc-pao-addon-main-settings-2 .wc-pao-col1-2' ).removeClass( 'show' ).addClass( 'hide' );
			$addon.find( '.wc-pao-addon-main-settings-2 .wc-pao-col1' ).addClass( 'full' );

			// Default options rows to be hidden.
			$addon.find( '.wc-pao-addon-content-option-rows' ).removeClass( 'show' ).addClass( 'hide' );
			$addon.find( '.wc-pao-addon-content-non-option-rows' ).removeClass( 'hide' ).addClass( 'show' );

			// Show required field checkbox.
			$addon.find( '.wc-pao-addon-required-setting' ).removeClass( 'hide' ).addClass( 'show' );

			switch ( selectedValue ) {
				case 'multiple_choice':
					$addon.find( '.wc-pao-addon-main-settings-2 .wc-pao-col1-1' ).removeClass( 'hide' ).addClass( 'show' );
					$addon.find( '.wc-pao-addon-main-settings-2 .wc-pao-col1' ).removeClass( 'full' );

					$addon.find( '.wc-pao-addon-content-option-rows' ).removeClass( 'hide' ).addClass( 'show' );
					$addon.find( '.wc-pao-addon-content-non-option-rows' ).removeClass( 'show' ).addClass( 'hide' );

					if ( 'images' === $addon.find( '.wc-pao-addon-display-select' ).val() ) {
						$addon.find( '.wc-pao-addon-content-image' ).removeClass( 'hide' ).addClass( 'show' );
						$addon.find( '.wc-pao-addon-content-label' ).removeClass( 'full' );
						$addon.find( '.wc-pao-addon-content-option-header' ).text( wc_pao_params.i18n.options_header_with_image );
					}
					break;
				case 'checkbox':
					$addon.find( '.wc-pao-addon-main-settings-2 .wc-pao-col1-1' ).removeClass( 'show' ).addClass( 'hide' );
					$addon.find( '.wc-pao-addon-main-settings-2 .wc-pao-col1' ).addClass( 'full' );
					$addon.find( '.wc-pao-addon-content-option-rows' ).removeClass( 'hide' ).addClass( 'show' );
					$addon.find( '.wc-pao-addon-content-non-option-rows' ).removeClass( 'show' ).addClass( 'hide' );
					$addon.find( '.wc-pao-addon-content-image' ).removeClass( 'show' ).addClass( 'hide' );
					$addon.find( '.wc-pao-addon-content-label' ).addClass( 'full' );
					$addon.find( '.wc-pao-addon-adjust-price-container' ).removeClass( 'show' ).addClass( 'hide' );
					$addon.find( '.wc-pao-addon-restrictions-container' ).removeClass( 'show' ).addClass( 'hide' );
					break;
				case 'custom_price':
					restrictionName = wc_pao_params.i18n.limit_price_range;
					$addon.find( '.wc-pao-addon-adjust-price-container' ).removeClass( 'show' ).addClass( 'hide' );
					$addon.find( '.wc-pao-addon-min-max' ).removeClass( 'hide' ).addClass( 'show' );
					$addon.find( '.wc-pao-addon-main-settings-2 .wc-pao-col1-1' ).removeClass( 'show' ).addClass( 'hide' );
					$addon.find( '.wc-pao-addon-main-settings-2 .wc-pao-col1' ).addClass( 'full' );
					$addon.find( '.wc-pao-addon-content-option-rows' ).removeClass( 'show' ).addClass( 'hide' );
					$addon.find( '.wc-pao-addon-content-non-option-rows' ).removeClass( 'hide' ).addClass( 'show' );
					$addon.find( '.wc-pao-addon-restrictions-select' ).removeClass( 'show' ).addClass( 'hide' );
					$addon.find( '.wc-pao-addon-adjust-price-container' ).removeClass( 'show' ).addClass( 'hide' );
					$addon.find( '.wc-pao-addon-restrictions-container' ).removeClass( 'hide' ).addClass( 'show' );
					break;
				case 'input_multiplier':
					$addon.find( '.wc-pao-addon-adjust-price-container' ).removeClass( 'hide' ).addClass( 'show' );
					$addon.find( '.wc-pao-addon-restrictions-container' ).removeClass( 'hide' ).addClass( 'show' );
					restrictionName = wc_pao_params.i18n.limit_quantity_range;
					break;
				case 'custom_text':
					restrictionName = wc_pao_params.i18n.limit_character_length;

					$addon.find( '.wc-pao-addon-main-settings-2 .wc-pao-col1-2' ).removeClass( 'hide' ).addClass( 'show' );
					$addon.find( '.wc-pao-addon-restrictions-select' ).removeClass( 'hide' ).addClass( 'show' );
					$addon.find( '.wc-pao-addon-content-option-rows' ).removeClass( 'show' ).addClass( 'hide' );
					$addon.find( '.wc-pao-addon-content-non-option-rows' ).removeClass( 'hide' ).addClass( 'show' );
					$addon.find( '.wc-pao-addon-adjust-price-container' ).removeClass( 'hide' ).addClass( 'show' );
					$addon.find( '.wc-pao-addon-restrictions-container' ).removeClass( 'hide' ).addClass( 'show' );
					$addon.find( '.wc-pao-addon-min-max' ).removeClass( 'hide' ).addClass( 'show' );

					if ( 'email' === $addon.find( '.wc-pao-addon-restrictions-select' ).val() ) {
						$addon.find( '.wc-pao-addon-min-max' ).removeClass( 'show' ).addClass( 'hide' );
					}
					break;
				case 'custom_textarea':
					restrictionName = wc_pao_params.i18n.limit_character_length;
					$addon.find( '.wc-pao-addon-min-max' ).removeClass( 'hide' ).addClass( 'show' );
					$addon.find( '.wc-pao-addon-adjust-price-container' ).removeClass( 'hide' ).addClass( 'show' );
					$addon.find( '.wc-pao-addon-restrictions-container' ).removeClass( 'hide' ).addClass( 'show' );
					break;
				case 'file_upload':
					$addon.find( '.wc-pao-addon-adjust-price-container' ).removeClass( 'hide' ).addClass( 'show' );
					$addon.find( '.wc-pao-addon-restrictions-container' ).removeClass( 'show' ).addClass( 'hide' );
					break;
				case 'heading':
					$addon.find( '.wc-pao-addon-required-setting' ).removeClass( 'show' ).addClass( 'hide' );
					$addon.find( '.wc-pao-addon-adjust-price-container' ).removeClass( 'show' ).addClass( 'hide' );
					$addon.find( '.wc-pao-addon-restrictions-container' ).removeClass( 'show' ).addClass( 'hide' );
					$addon.find( '.wc-pao-addon-main-settings-1 .wc-pao-col1' ).addClass( 'full' );
					$addon.find( '.wc-pao-addon-main-settings-1 .wc-pao-col2' ).removeClass( 'show' ).addClass( 'hide' );
					break;
				default:
					$addon.find( '.wc-pao-addon-main-settings-2 .wc-pao-col1-1' ).removeClass( 'show' ).addClass( 'hide' );
					$addon.find( '.wc-pao-addon-main-settings-2 .wc-pao-col1-2' ).removeClass( 'show' ).addClass( 'hide' );
					$addon.find( '.wc-pao-addon-main-settings-2 .wc-pao-col1' ).addClass( 'full' );

					$addon.find( '.wc-pao-addon-content-option-rows' ).removeClass( 'show' ).addClass( 'hide' );
					$addon.find( '.wc-pao-addon-content-non-option-rows' ).removeClass( 'hide' ).addClass( 'show' );
					restrictionName = wc_pao_params.i18n.restrictions;
					$addon.find( '.wc-pao-addon-restrictions-container' ).removeClass( 'show' ).addClass( 'hide' );
					break;
			}

			$addon.find( '.wc-pao-addon-restriction-name' ).html( restrictionName );

			wc_pao_admin.update_remove_addon_option_buttons( $addon );
		},

		update_remove_addon_option_buttons: function ( $addon ) {

			// Count the number of options.  If one (or less), disable the remove option buttons
			var removeAddOnOptionButtons = $addon.find( '.wc-pao-remove-option' );

			if ( 2 > removeAddOnOptionButtons.length ) {
				removeAddOnOptionButtons.attr( 'disabled', 'disabled' );
			} else {
				removeAddOnOptionButtons.removeAttr( 'disabled' );
			}
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

		init: function() {
			var $products_addons_data = $( '#product_addons_data' ),
				$control_var          = $( '<input>' ),
				$test_var             = $( '<input>' );

			$control_var.attr( 'type', 'hidden' );
			$control_var.attr( 'name', 'pao_post_control_var' );
			$control_var.val( '1' );

			$test_var.attr( 'type', 'hidden' );
			$test_var.attr( 'name', 'pao_post_test_var' );
			$test_var.val( '1' );

			$products_addons_data.prepend( $control_var );
			$products_addons_data.append( $test_var );

			$( '.post-type-product' ).on( 'click', '#publishing-action input[name="save"]', function() {
				return wc_pao_admin.validateSettings( 'all' );
			} );

			$( '.product_page_addons' ).on( 'click', 'input[type="submit"]', function() {
				return wc_pao_admin.validateSettings( 'all' );
			} );

			$products_addons_data
				.on( 'keyup', '.wc-pao-addon-content-name', function() {
					var self = $( this );

					wc_pao_admin.delay( function() {
						var title = self.val();
						self.closest( '.wc-pao-addon' ).find( '.wc-pao-addon-name' ).text( title );
					}, 300 );
				} )
				.on( 'blur', '.wc-pao-addon-content-name', function() {
					wc_pao_admin.validateSettings( 'addon_title' );
				} )
				.on( 'keyup', '.wc-pao-addon-content-label input', function() {
					$( this ).removeClass( 'wc-pao-error' );
					$( '.wc-pao-error-message' ).remove();
				} )
				.on( 'change', 'select.addon_field_type', function() {
					var loop               = $( '.wc-pao-addons .wc-pao-addon' ).length,
						$field_type_select = $( this ),
						field_type         = $field_type_select.val(),
						block_params       = wc_pao_admin.getBlockParams();

					$products_addons_data.block( block_params );

					setTimeout( function() {
						$.when( wc_pao_admin.getAddonField( field_type ) ).then( function( html ) {
							var html = html.html;

							html = html.replace( /{loop}/g, loop );

							// Replace class closed with open so it is expanded when added.
							html = html.replace( /closed/g, 'open' );

							$( '.wc-pao-addons' ).append( html );

							var $addon = $( '.wc-pao-addons .wc-pao-addon' ).last(),
								selectedValue = $field_type_select.val(),
								selectedName  = $field_type_select.find( 'option[value="' + selectedValue + '"]' ).text();

							$addon.find( '.product_addon_type' ).val( selectedValue );
							$addon.find( '.wc-pao-addon-header .wc-pao-addon-type' ).html( selectedName );

							wc_pao_admin.render( selectedValue, $addon );

							wc_pao_admin.refresh();
							wc_pao_admin.runOptionSortable();

							// Show/hide special classes which may be in field html.
							var product_type = $( 'select#product-type' ).val();
							$( '.hide_if_' + product_type ).hide();
							$( '.show_if_' + product_type ).show();

							$products_addons_data.unblock();
						} );
					}, 250 );

					return false;
				} )
				.on( 'change', '.wc-pao-addon-display-select', function() {
					var selectedValue = $( this ).val(),
						parent        = $( this ).parents( '.wc-pao-addon' );

					if ( 'images' === selectedValue ) {
						parent.find( '.wc-pao-addon-content-image' ).removeClass( 'hide' ).addClass( 'show' );
						parent.find( '.wc-pao-addon-content-label' ).removeClass( 'full' );
						parent.find( '.wc-pao-addon-content-option-header' ).text( wc_pao_params.i18n.options_header_with_image );
					} else {
						parent.find( '.wc-pao-addon-content-image' ).removeClass( 'show' ).addClass( 'hide' );
						parent.find( '.wc-pao-addon-content-label' ).addClass( 'full' );
						parent.find( '.wc-pao-addon-content-option-header' ).text( wc_pao_params.i18n.options_header_default );
					}
				} )
				.on( 'click', 'button.wc-pao-add-option', function() {

					var $addon = $( this ).closest( '.wc-pao-addon' ),
						loop   = $addon.index( '.wc-pao-addon' ),
						parent = $( this ).parents( '.wc-pao-addon-content-option-rows' ).find( '.wc-pao-addon-content-options-container' );

					$.when( wc_pao_admin.getAddonOptions() ).then( function( html ) {
						var html = html.html;

						html = html.replace( /{loop}/g, loop );

						$( html ).appendTo( parent );

						if ( 'images' === $addon.find( '.wc-pao-addon-display-select' ).val() ) {
							$addon.find( '.wc-pao-addon-content-image' ).removeClass( 'hide' ).addClass( 'show' );
							$addon.find( '.wc-pao-addon-content-label' ).removeClass( 'full' );
							$addon.find( '.wc-pao-addon-content-option-header' ).text( wc_pao_params.i18n.options_header_with_image );
						}

						wc_pao_admin.update_remove_addon_option_buttons( $addon );
					} );

					return false;
				} )
				.on( 'click', 'a.wc-pao-remove-addon', function( e ) {

					e.preventDefault();

					$(this).closest( '.wc-pao-addon' ).off();
					$( '.wc-pao-error-message' ).remove();

					var answer = confirm( wc_pao_params.i18n.confirm_remove_addon );

					if ( answer ) {
						var addon = $( this ).closest( '.wc-pao-addon' );
						$( addon ).find( 'input' ).val( '' );
						$( addon ).remove();
					}

					$( '.wc-pao-addons .wc-pao-addon' ).each( function( index, el ) {
						var this_index = index;

						$( this ).find( '.product_addon_position' ).val( this_index );
						$( this ).find( 'select, input, textarea' ).prop( 'name', function( i, val ) {
							var field_name = val.replace( /\[[0-9]+\]/g, '[' + this_index + ']' );

							return field_name;
						} );
					} );

					wc_pao_admin.refresh();

					return false;
				} )
				.on( 'click', '.wc-pao-remove-option', function() {

					var disabled = $(this).attr( 'disabled' );

					if ( typeof disabled !== 'undefined' && disabled !== false ) {
						return false;
					}

					var answer = confirm( wc_pao_params.i18n.confirm_remove_option );

					if ( answer ) {
						var $addon = $( this ).parents( '.wc-pao-addon' ),
							removeAddOnOptionButtons;

						$( this ).parents( '.wc-pao-addon-option-row' ).remove();

						// Count the number of options.  If one (or less), disable the remove option buttons
						removeAddOnOptionButtons = $addon.find( '.wc-pao-remove-option' );

						if ( 2 > removeAddOnOptionButtons.length ) {
							removeAddOnOptionButtons.attr( 'disabled', 'disabled' );
						} else {
							removeAddOnOptionButtons.removeAttr( 'disabled' );
						}
					}

					return false;

				} )
				.on( 'click', '.wc-pao-expand-all', function( e ) {
					e.preventDefault();
					wc_pao_admin.expandAllFields();
				} )
				.on( 'click', '.wc-pao-close-all', function( e ) {
					e.preventDefault();
					wc_pao_admin.closeAllFields();
				} )
				.on( 'click', '.wc-pao-addon-description-enable', function() {
					if ( $( this ).is( ':checked' ) ) {
						$( this ).parents( '.wc-pao-addons-secondary-settings' ).find( '.wc-pao-addon-description' ).removeClass( 'hide' ).addClass( 'show' );
					} else {
						$( this ).parents( '.wc-pao-addons-secondary-settings' ).find( '.wc-pao-addon-description' ).removeClass( 'show' ).addClass( 'hide' );
					}
				} )
				.on( 'change', '.wc-pao-addon-option-price-type', function() {
					var selectedValue = $( this ).val();

					switch ( selectedValue ) {
						case 'flat_fee':
						case 'quantity_based':
							$( this ).parents( '.wc-pao-addon-content-price-type' ).removeClass( 'full' ).next( '.wc-pao-addon-content-price' ).eq(0).removeClass( 'hide' ).addClass( 'show' );
							break;
					}
				} )
				.on( 'click', '.wc-pao-addon-add-image', function() {
					var parent = $( this ).parent(),
						mediaFrame;

					// create the media frame
					mediaFrame = wp.media.frames.mediaFrame = wp.media( {

						title: wc_pao_params.i18n.add_image_swatch,

						button: {
							text: wc_pao_params.i18n.add_image
						},

						// only images
						library: {
							type: 'image'
						},

						multiple: false
					} );

					// After a file has been selected.
					mediaFrame.on( 'select', function() {
						var selection = mediaFrame.state().get( 'selection' );

						selection.map( function( attachment ) {

							attachment = attachment.toJSON();

							if ( attachment.id ) {
								var url = attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

								parent.find( '.wc-pao-addon-option-image-id' ).val( attachment.id );
								parent.find( '.wc-pao-addon-image-swatch img' ).prop( 'src', url );
								parent.find( '.wc-pao-addon-image-swatch' ).removeClass( 'hide' ).addClass( 'show' );
								parent.find( '.wc-pao-addon-add-image' ).removeClass( 'show' ).addClass( 'hide' );
								parent.find( '.dashicons-plus' ).removeClass( 'show' ).addClass( 'hide' );
							}
						} );
					} );

					// Open the modal frame.
					mediaFrame.open();
				} )
				.on( 'click', '.wc-pao-addon-image-swatch', function( e ) {
					e.preventDefault();

					var parent = $( this ).parent();

					parent.find( '.wc-pao-addon-option-image-id' ).val( '' );
					parent.find( '.wc-pao-addon-image-swatch img' ).prop( 'src', '' );
					parent.find( '.wc-pao-addon-image-swatch' ).removeClass( 'show' ).addClass( 'hide' );
					parent.find( '.wc-pao-addon-add-image' ).removeClass( 'hide' ).addClass( 'show' );
					parent.find( '.dashicons-plus' ).removeClass( 'hide' ).addClass( 'show' );
				} )
				.on( 'click', '.wc-pao-addon-restrictions', function() {
					if ( $( this ).is( ':checked' ) ) {
						$( this ).parents( '.wc-pao-addon-restrictions-container' ).find( '.wc-pao-addon-restrictions-settings' ).removeClass( 'hide' ).addClass( 'show' );
					} else {
						$( this ).parents( '.wc-pao-addon-restrictions-container' ).find( '.wc-pao-addon-restrictions-settings' ).removeClass( 'show' ).addClass( 'hide' );
					}
				} )
				.on( 'change', '.wc-pao-addon-restrictions-select', function() {
					var selectedValue = $( this ).val(),
						parent        = $( this ).parents( '.wc-pao-addon-restrictions-settings' );

					if ( 'email' === selectedValue ) {
						parent.find( '.wc-pao-addon-min-max' ).removeClass( 'show' ).addClass( 'hide' );
					} else {
						parent.find( '.wc-pao-addon-min-max' ).removeClass( 'hide' ).addClass( 'show' );
					}
				} )
				.on( 'click', '.wc-pao-addon-id', function() {
					return false;
				} )
				.on( 'click', '.wc-pao-addon-adjust-price', function() {
					if ( $( this ).is( ':checked' ) ) {
						$( this ).parents( '.wc-pao-addon-adjust-price-container' ).find( '.wc-pao-addon-adjust-price-settings' ).removeClass( 'hide' ).addClass( 'show' );
					} else {
						$( this ).parents( '.wc-pao-addon-adjust-price-container' ).find( '.wc-pao-addon-adjust-price-settings' ).removeClass( 'show' ).addClass( 'hide' );
					}
				} )

			$products_addons_data.find( '.wc-pao-addon' ).each( function() {
				var type = $(this).find( '.product_addon_type' ).val();

				wc_pao_admin.render( type, $(this) );
			} );

			wc_pao_admin.refresh();

			// Import / Export
			$products_addons_data.on( 'click', '.wc-pao-export-addons', function() {
				var addons_export_string = $products_addons_data.find( '.product_addons_export_string' ).val(),
					$temp = $("<input>");

				$("body").append($temp);

				$temp.val( addons_export_string ).select();
				document.execCommand("copy");

				$temp.remove();

				alert( wc_pao_params.i18n.addons_exported )

				return false;
			} );

			$products_addons_data.on( 'click', '.wc-pao-import-addons', function( e ) {

				e.preventDefault();

				$(this).blur();

				let imported_addons = window.prompt( wc_pao_params.i18n.import_addons_prompt );

				if ( ! imported_addons ) {
					return false;
				}

				var post_id, is_global;

				// Product-level add-ons.
				if ( 'undefined' != typeof woocommerce_admin_meta_boxes && woocommerce_admin_meta_boxes.post_id ) {
					post_id   = woocommerce_admin_meta_boxes.post_id;
					is_global = false;
				// Global-level add-ons.
				} else {
					var url_params = new URLSearchParams( window.location.search );
					post_id   = url_params.get( 'edit' );
					is_global = true;
				}

				var data = {
					security: wc_pao_params.nonce.import_addons,
					action: 'wc_pao_import_addons',
					import_product_addon: imported_addons,
					post_id: post_id,
					is_global: is_global
				};

				$.post( wc_pao_params.ajax_url, data, function( response ) {

					if ( response.success && true === response.success ) {
						var product_type = $( 'select#product-type' ).val(),
							block_params = wc_pao_admin.getBlockParams();

						$products_addons_data.block( block_params );

						alert( response.data.message );

						setTimeout( function() {

							$.each( response.data.addons, function( key, element ) {

								var addon = element,
									type  = addon.type;

								$.when( wc_pao_admin.getAddonField( type, element ) ).then( function( html ) {
									var html = html.html,
										loop = $( '.wc-pao-addons .wc-pao-addon' ).length;

									html = html.replace( /{loop}/g, loop );

									$( '.wc-pao-addons' ).append( html );

									var $addon       = $( '.wc-pao-addons .wc-pao-addon' ).last(),
										selectedName = $( 'select.addon_field_type' ).find( 'option[value="' + type + '"]' ).text();

									$addon.find( '.product_addon_type' ).val( type );
									$addon.find( '.wc-pao-addon-header .wc-pao-addon-type' ).html( selectedName );

									wc_pao_admin.render( type, $addon );

									wc_pao_admin.refresh();
									wc_pao_admin.runOptionSortable();

									// Show/hide special classes which may be in field html.
									$( '.hide_if_' + product_type ).hide();
									$( '.show_if_' + product_type ).show();
								} );
							} );
							$products_addons_data.unblock( block_params );

							setTimeout( function() {
								var $target_element = $( '.wc-pao-addon-header:last' );
								$( 'html, body' ).animate( {
									scrollTop: ( $target_element.offset().top - 200 )
								}, 1000 );
							}, 1000 );
						}, 1000 );
					} else {
						if ( response.data.message.length ) {
							alert( response.data.message );
						}
					}
				} );
			} );

			wc_pao_admin.runFieldSortable();
			wc_pao_admin.runOptionSortable();

			// Global add-ons handles for opening/closing add-ons configuration.
			// For product-level add-ons this is handled directly by core via the meta-boxes.js script.
			$( '.global-addons-form .wc-pao-addon' ).each( function() {

				if ( $(this).hasClass( 'closed' ) ) {
					$(this).find( '.wc-pao-addon-content' ).hide();
				}
			} );

			$( '.global-addons-form .wc-pao-addon-header' ).on( 'click', function() {
				var $addon_container = $(this).closest( '.wc-pao-addon' ),
					$addon_content   = $addon_container.find( '.wc-pao-addon-content' );

				setTimeout( function() {
					$addon_container.toggleClass( 'closed' ).toggleClass( 'open' );
					$addon_content.stop().slideToggle();
				}, 50 );
			} );

			// Global add-ons: Bulk Actions.
			$( '#addons-table #doaction' ).on( 'click', function( e ) {

				var value = $( '#bulk-action-selector-top' ).val();

				if ( value === 'delete' && ! window.confirm( wc_pao_params.i18n.delete_addons_warning ) ) {
					e.preventDefault();
					return false;
				}
			} );

			$( '#addons-table #doaction2' ).on( 'click', function( e ) {

				var value = $( '#bulk-action-selector-bottom' ).val();

				if ( value === 'delete' && ! window.confirm( wc_pao_params.i18n.delete_addons_warning ) ) {
					e.preventDefault();
					return false;
				}
			} );

			// Show / hide expand/close
			var total_add_ons = $( '.wc-pao-addons .wc-pao-addon' ).length;
			if ( total_add_ons > 1 ) {
				$( '.wc-pao-toolbar' ).show();
			}

			// Sync "Multiply cost by person count" field for Accommodation + Bookings.
			$( '[name*="addon_wc_booking_person_qty_multiplier"]' ).on(
				'change',
				function () {
					const addonCheckboxName  = $( this ).attr( 'name' );
					const addonCheckboxValue = $( this ).is( ':checked' );
					$( '[name*="' + addonCheckboxName + '"]' ).prop(
						'checked',
						addonCheckboxValue
					);
				}
			);
		}
	};

	wc_pao_admin.init();
} );
