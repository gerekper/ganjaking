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

		getAddonField: function() {
			var data = {
				security:  wc_pao_params.nonce.get_addon_field,
				action: 'wc_pao_get_addon_field',
			};

			return $.ajax( {
				type:    'POST',
				data:    data,
				url:     wc_pao_params.ajax_url
			} );
		},

		refresh: function() {
			var addons = $( '.wc-pao-addon' ).length;

			if ( 0 < addons ) {
				$( '.wc-pao-toolbar' ).addClass( 'wc-pao-has-addons' );
				$( '.wc-pao-addons' ).addClass( 'wc-pao-has-addons' );
			} else {
				$( '.wc-pao-toolbar' ).removeClass( 'wc-pao-has-addons' );
				$( '.wc-pao-addons' ).removeClass( 'wc-pao-has-addons' );
			}

			$( document.body ).trigger( 'init_tooltips' );
		},

		expandAllFields: function() {
			$( '#product_addons_data .wc-pao-addon' ).removeClass( 'closed' ).addClass( 'open' );
		},

		closeAllFields: function() {
			$( '#product_addons_data .wc-pao-addon' ).removeClass( 'open' ).addClass( 'closed' );
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
				handle: '.wc-pao-addon-header',
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

		validateSettings: function( context ) {
			$( '.wc-pao-error-message' ).remove();
			$( '.updated' ).remove();

			var shouldReturn     = true,
				removeErrorBorder = true;

			// Loop through all addons to validate them.
			$( '.wc-pao-addons' ).find( '.wc-pao-addon' ).each( function( i ) {
				if ( 0 === $( this ).find( '.wc-pao-addon-content-name' ).val().length ) {
					$( this ).addClass( 'wc-pao-error' ).find( '.wc-pao-addon-content-name' ).addClass( 'wc-pao-error' );

					shouldReturn     = false;
					removeErrorBorder = false;
				} else {
					$( this ).find( '.wc-pao-addon-content-name' ).removeClass( 'wc-pao-error' );
				}

				var type = $( this ).find( '.wc-pao-addon-type-select' ).val();

				$( this ).find( '.wc-pao-addon-option-row' ).each( function() {
					if ( ( 'multiple_choice' === type || 'checkbox' === type ) && 0 === $( this ).find( '.wc-pao-addon-content-label input' ).val().length ) {

						$( this ).find( '.wc-pao-addon-content-label input' ).addClass( 'wc-pao-error' );
						$( this ).parents( '.wc-pao-addon' ).eq( 0 ).addClass( 'wc-pao-error' );

						shouldReturn     = false;
						removeErrorBorder = false;
					} else {
						$( this ).find( '.wc-pao-addon-content-label input' ).removeClass( 'wc-pao-error' );
					}
				} );

				if ( removeErrorBorder ) {
					$( this ).removeClass( 'wc-pao-error' );
				}
			} );

			if ( false === shouldReturn ) {
				var errorMessage = $( '<div class="notice notice-error wc-pao-error-message"><p>' + wc_pao_params.i18n.required_fields + '</p></div>' );

				if ( 'product' === context ) {
					$( '.wc-pao-addons' ).before( errorMessage );
				} else if ( 'global' === context ) {
					$( '.global-addons-form' ).before( errorMessage );
				}

				$( 'html, body' ).animate( {
					scrollTop: ( $( '.wc-pao-error-message' ).offset().top - 200 )
				}, 600 );
			}

			return shouldReturn;
		},

		init: function() {
			$( '.post-type-product' ).on( 'click', '#publishing-action input[name="save"]', function() {
				return wc_pao_admin.validateSettings( 'product' );
			} );

			$( '.product_page_addons' ).on( 'click', 'input[type="submit"]', function() {
				return wc_pao_admin.validateSettings( 'global' );
			} );

			$( '#product_addons_data' )
				.on( 'change', '.wc-pao-addon-content-name', function() {
					if ( $( this ).val() ) {
						$( this ).closest( '.wc-pao-addon' ).find( '.wc-pao-addon-name' ).text( $( this ).val() );
					} else {
						$( this ).closest( '.wc-pao-addon' ).find( '.wc-pao-addon-name' ).text( '' );
					}
				} )
				.on( 'keyup', '.wc-pao-addon-content-name, .wc-pao-addon-content-label input', function() {
					$( this ).removeClass( 'wc-pao-error' );
					$( '.wc-pao-error-message' ).remove();
				} )
				.on( 'change', 'select.wc-pao-addon-type-select', function() {
					var selectedValue = $( this ).val(),
						parent        = $( this ).parents( '.wc-pao-addon' ),
						selectedName  = $( this ).context.selectedOptions[0].innerHTML,
						restrictionName;

					// Update selected type label.
					parent.find( '.wc-pao-addon-header .wc-pao-addon-type' ).html( selectedName );

					// Default show title format select.
					parent.find( '.wc-pao-addon-main-settings-2 .wc-pao-col1' ).removeClass( 'full' );
					parent.find( '.wc-pao-addon-main-settings-2 .wc-pao-col2' ).removeClass( 'hide' ).addClass( 'show' );

					// Default hide images column.
					parent.find( '.wc-pao-addon-content-image' ).removeClass( 'show' ).addClass( 'hide' );
					parent.find( '.wc-pao-addon-content-label' ).addClass( 'full' );

					// Default restriction adjustment type.
					parent.find( '.wc-pao-addon-restrictions-select' ).removeClass( 'show' ).addClass( 'hide' );

					// Default hide display type select column.
					parent.find( '.wc-pao-addon-main-settings-1 .wc-pao-col2-1' ).removeClass( 'show' ).addClass( 'hide' );
					parent.find( '.wc-pao-addon-main-settings-1 .wc-pao-col2-2' ).removeClass( 'show' ).addClass( 'hide' );
					parent.find( '.wc-pao-addon-main-settings-1 .wc-pao-col1' ).addClass( 'full' );

					// Default options rows to be hidden.
					parent.find( '.wc-pao-addon-content-option-rows' ).removeClass( 'show' ).addClass( 'hide' );
					parent.find( '.wc-pao-addon-content-non-option-rows' ).removeClass( 'hide' ).addClass( 'show' );

					// Show required field checkbox.
					parent.find( '.wc-pao-addon-required-setting' ).removeClass( 'hide' ).addClass( 'show' );

					switch ( selectedValue ) {
						case 'multiple_choice':
							parent.find( '.wc-pao-addon-main-settings-1 .wc-pao-col2-1' ).removeClass( 'hide' ).addClass( 'show' );
							parent.find( '.wc-pao-addon-main-settings-1 .wc-pao-col1' ).removeClass( 'full' );

							parent.find( '.wc-pao-addon-content-option-rows' ).removeClass( 'hide' ).addClass( 'show' );
							parent.find( '.wc-pao-addon-content-non-option-rows' ).removeClass( 'show' ).addClass( 'hide' );

							if ( 'images' === parent.find( '.wc-pao-addon-display-select' ).val() ) {
								parent.find( '.wc-pao-addon-content-image' ).removeClass( 'hide' ).addClass( 'show' );
								parent.find( '.wc-pao-addon-content-label' ).removeClass( 'full' );
							}
							break;
						case 'checkbox':
							parent.find( '.wc-pao-addon-main-settings-1 .wc-pao-col2-1' ).removeClass( 'show' ).addClass( 'hide' );
							parent.find( '.wc-pao-addon-main-settings-1 .wc-pao-col1' ).addClass( 'full' );
							parent.find( '.wc-pao-addon-content-option-rows' ).removeClass( 'hide' ).addClass( 'show' );
							parent.find( '.wc-pao-addon-content-non-option-rows' ).removeClass( 'show' ).addClass( 'hide' );
							parent.find( '.wc-pao-addon-content-image' ).removeClass( 'show' ).addClass( 'hide' );
							parent.find( '.wc-pao-addon-content-label' ).addClass( 'full' );
							parent.find( '.wc-pao-addon-adjust-price-container' ).removeClass( 'show' ).addClass( 'hide' );
							parent.find( '.wc-pao-addon-restrictions-container' ).removeClass( 'show' ).addClass( 'hide' );
							break;
						case 'custom_price':
							restrictionName = wc_pao_params.i18n.limit_price_range;
							parent.find( '.wc-pao-addon-adjust-price-container' ).removeClass( 'show' ).addClass( 'hide' );
							parent.find( '.wc-pao-addon-min-max' ).removeClass( 'hide' ).addClass( 'show' );
							parent.find( '.wc-pao-addon-main-settings-1 .wc-pao-col2-1' ).removeClass( 'show' ).addClass( 'hide' );
							parent.find( '.wc-pao-addon-main-settings-1 .wc-pao-col1' ).addClass( 'full' );
							parent.find( '.wc-pao-addon-content-option-rows' ).removeClass( 'show' ).addClass( 'hide' );
							parent.find( '.wc-pao-addon-content-non-option-rows' ).removeClass( 'hide' ).addClass( 'show' );
							parent.find( '.wc-pao-addon-restrictions-select' ).removeClass( 'show' ).addClass( 'hide' );
							parent.find( '.wc-pao-addon-adjust-price-container' ).removeClass( 'show' ).addClass( 'hide' );
							parent.find( '.wc-pao-addon-restrictions-container' ).removeClass( 'hide' ).addClass( 'show' );
							break;
						case 'input_multiplier':
							parent.find( '.wc-pao-addon-adjust-price-container' ).removeClass( 'hide' ).addClass( 'show' );
							parent.find( '.wc-pao-addon-restrictions-container' ).removeClass( 'hide' ).addClass( 'show' );
							restrictionName = wc_pao_params.i18n.limit_quantity_range;
							break;
						case 'custom_text':
							restrictionName = wc_pao_params.i18n.limit_character_length;

							parent.find( '.wc-pao-addon-main-settings-1 .wc-pao-col2-2' ).removeClass( 'hide' ).addClass( 'show' );
							parent.find( '.wc-pao-addon-restrictions-select' ).removeClass( 'hide' ).addClass( 'show' );
							parent.find( '.wc-pao-addon-content-option-rows' ).removeClass( 'show' ).addClass( 'hide' );
							parent.find( '.wc-pao-addon-content-non-option-rows' ).removeClass( 'hide' ).addClass( 'show' );
							parent.find( '.wc-pao-addon-adjust-price-container' ).removeClass( 'hide' ).addClass( 'show' );
							parent.find( '.wc-pao-addon-restrictions-container' ).removeClass( 'hide' ).addClass( 'show' );
							parent.find( '.wc-pao-addon-min-max' ).removeClass( 'hide' ).addClass( 'show' );

							if ( 'email' === parent.find( '.wc-pao-addon-restrictions-select' ).val() ) {
								parent.find( '.wc-pao-addon-min-max' ).removeClass( 'show' ).addClass( 'hide' );
							}
							break;
						case 'custom_textarea':
							restrictionName = wc_pao_params.i18n.limit_character_length;
							parent.find( '.wc-pao-addon-min-max' ).removeClass( 'hide' ).addClass( 'show' );
							parent.find( '.wc-pao-addon-adjust-price-container' ).removeClass( 'hide' ).addClass( 'show' );
							parent.find( '.wc-pao-addon-restrictions-container' ).removeClass( 'hide' ).addClass( 'show' );
							break;
						case 'file_upload':
							parent.find( '.wc-pao-addon-adjust-price-container' ).removeClass( 'hide' ).addClass( 'show' );
							parent.find( '.wc-pao-addon-restrictions-container' ).removeClass( 'show' ).addClass( 'hide' );
							break;
						case 'heading':
							parent.find( '.wc-pao-addon-required-setting' ).removeClass( 'show' ).addClass( 'hide' );
							parent.find( '.wc-pao-addon-adjust-price-container' ).removeClass( 'show' ).addClass( 'hide' );
							parent.find( '.wc-pao-addon-restrictions-container' ).removeClass( 'show' ).addClass( 'hide' );
							parent.find( '.wc-pao-addon-main-settings-2 .wc-pao-col1' ).addClass( 'full' );
							parent.find( '.wc-pao-addon-main-settings-2 .wc-pao-col2' ).removeClass( 'show' ).addClass( 'hide' );
							break;
						default:
							parent.find( '.wc-pao-addon-main-settings-1 .wc-pao-col2-1' ).removeClass( 'show' ).addClass( 'hide' );
							parent.find( '.wc-pao-addon-main-settings-1 .wc-pao-col2-2' ).removeClass( 'show' ).addClass( 'hide' );
							parent.find( '.wc-pao-addon-main-settings-1 .wc-pao-col1' ).addClass( 'full' );

							parent.find( '.wc-pao-addon-content-option-rows' ).removeClass( 'show' ).addClass( 'hide' );
							parent.find( '.wc-pao-addon-content-non-option-rows' ).removeClass( 'hide' ).addClass( 'show' );
							restrictionName = wc_pao_params.i18n.restrictions;
							parent.find( '.wc-pao-addon-restrictions-container' ).removeClass( 'show' ).addClass( 'hide' );
							break;
					}

					parent.find( '.wc-pao-addon-restriction-name' ).html( restrictionName );

					// Count the number of options.  If one (or less), disable the remove option buttons
					var removeAddOnOptionButtons = $( this ).closest( '.wc-pao-addon' ).find( '.wc-pao-remove-option' );
					if ( 2 > removeAddOnOptionButtons.length ) {
						removeAddOnOptionButtons.attr( 'disabled', 'disabled' );
					} else {
						removeAddOnOptionButtons.removeAttr( 'disabled' );
					}
				} )
				.on( 'change', '.wc-pao-addon-display-select', function() {
					var selectedValue = $( this ).val(),
						parent        = $( this ).parents( '.wc-pao-addon' );

					if ( 'images' === selectedValue ) {
						parent.find( '.wc-pao-addon-content-image' ).removeClass( 'hide' ).addClass( 'show' );
						parent.find( '.wc-pao-addon-content-label' ).removeClass( 'full' );
					} else {
						parent.find( '.wc-pao-addon-content-image' ).removeClass( 'show' ).addClass( 'hide' );
						parent.find( '.wc-pao-addon-content-label' ).addClass( 'full' );
					}
				} )
				.on( 'click', 'button.wc-pao-add-option', function() {

					var loop   = $( this ).closest( '.wc-pao-addon' ).index( '.wc-pao-addon' ),
						parent = $( this ).parents( '.wc-pao-addon-content-option-rows' ).find( '.wc-pao-addon-content-options-container' );

					$.when( wc_pao_admin.getAddonOptions() ).then( function( html ) {
						var html = html.html;

						html = html.replace( /{loop}/g, loop );

						var selectedType = $( this ).parents( '.wc-pao-addon' ).find( '.wc-pao-addon-display-select' ).val();

						if ( 'images' === selectedType ) {
							html = html.replace( /wc-pao-addon-content-image hide/g, 'wc-pao-addon-content-image show' );
							html = html.replace( /wc-pao-addon-content-label full/g, 'wc-pao-addon-content-label' );
						}

						$( html ).appendTo( parent );

						$( 'select.wc-pao-addon-type-select' ).change();
					} );

					return false;
				} )
				.on( 'click', '.wc-pao-add-field', function() {
					var loop = $( '.wc-pao-addons .wc-pao-addon' ).length;

					$.when( wc_pao_admin.getAddonField() ).then( function( html ) {
						var html = html.html;

						html = html.replace( /{loop}/g, loop );

						// Replace class closed with open so it is expanded when added.
						html = html.replace( /closed/g, 'open' );

						$( '.wc-pao-addons' ).append( html );

						$( 'select.wc-pao-addon-type-select' ).change();

						wc_pao_admin.refresh();
						wc_pao_admin.runOptionSortable();

						// Show/hide special classes which may be in field html.
						var product_type    = $( 'select#product-type' ).val();
						$( '.hide_if_' + product_type ).hide();
						$( '.show_if_' + product_type ).show();
					} );

					return false;
				} )
				.on( 'click', '.wc-pao-remove-addon', function() {
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
					var answer = confirm( wc_pao_params.i18n.confirm_remove_option );

					if ( answer ) {
						var typeSelect = $( this ).parents( '.wc-pao-addon-content' ).find( 'select.wc-pao-addon-type-select' );

						$( this ).parents( '.wc-pao-addon-option-row' ).remove();

						typeSelect.change();
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
				.on( 'click', '.wc-pao-addon-header', function( e ) {
					e.preventDefault();
					var element = $( this ).parents( '.wc-pao-addon' );

					if ( element.hasClass( 'open' ) ) {
						element.removeClass( 'open' ).addClass( 'closed' );
					} else {
						element.removeClass( 'closed' ).addClass( 'open' );
					}
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
				.on( 'click', '.wc-pao-addon-adjust-price', function() {
					if ( $( this ).is( ':checked' ) ) {
						$( this ).parents( '.wc-pao-addon-adjust-price-container' ).find( '.wc-pao-addon-adjust-price-settings' ).removeClass( 'hide' ).addClass( 'show' );
					} else {
						$( this ).parents( '.wc-pao-addon-adjust-price-container' ).find( '.wc-pao-addon-adjust-price-settings' ).removeClass( 'show' ).addClass( 'hide' );
					}
				} )
				.find( 'select.wc-pao-addon-type-select' ).change();

			// Import / Export
			$( '#product_addons_data' ).on( 'click', '.wc-pao-export-addons', function() {

				$( '#product_addons_data textarea.wc-pao-import-field' ).hide();
				$( '#product_addons_data textarea.wc-pao-export-field' ).slideToggle( '300', function() {
					$( this ).select();
				} );

				return false;
			} );

			$( '#product_addons_data' ).on( 'click', '.wc-pao-import-addons', function() {

				$( '#product_addons_data textarea.wc-pao-export-field' ).hide();
				$( '#product_addons_data textarea.wc-pao-import-field' ).slideToggle( '300', function() {
					$( this ).val('');
				} );

				return false;
			} );

			wc_pao_admin.runFieldSortable();
			wc_pao_admin.runOptionSortable();

			// Show / hide expand/close
			var total_add_ons = $( '.wc-pao-addons .wc-pao-addon' ).length;
			if ( total_add_ons > 1 ) {
				$( '.wc-pao-toolbar' ).show();
			}
		}
	};

	wc_pao_admin.init();
} );
