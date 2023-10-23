/**
 * Front JS
 */

jQuery( document ).ready(
	function ( $ ) {

		/**
		 * Init the colorpicker input
		 */
		initColorpicker = function() {
			  // Customizable args for wpColorPicker function.
			  var colorPicker_opt = {
					color: false, // If Iris is attached to an input element, it will first try to pick up its value attribute. Otherwise, you can supply a color of any type that Color.js supports. (Hex, rgb, and hsl are good bets.).
					mode: 'hsl', // Iris can sport a variety of looks. It supports hsl and ‘hsv’ modes depending on your needs.
					controls: {
						horiz: 's', // horizontal defaults to saturation.
						vert: 'l', // vertical defaults to lightness.
						strip: 'h' // right strip defaults to hue.
					},
					hide: true, // Hide colorpickers by default.
					target: false, // a DOM element / jQuery selector that the element will be appended within. Only used when called on an input.
					width: 180, // the width of the collection of UI elements.
					palettes: false, // show a palette of basic colors beneath the square.
					change: function(event, ui) {
						let pickerContainer    = $( this ).closest( '.wp-picker-container' );
						let pickerInputWrap    = pickerContainer.find( '.wp-picker-input-wrap' );
						let placeholderElement = pickerContainer.find( '.wp-picker-custom-placeholder' );
						let clearElement       = pickerContainer.find( '.wp-picker-default-custom' );
						let colorPickerShow    = pickerContainer.find( '.wp-color-picker' ).data( 'addon-colorpicker-show' );
						let defaultColor       = pickerContainer.find( '.wp-color-picker' ).data( 'default-color' );

						// Placeholder option to hide or not the necessary elements.
						if ( 'placeholder' === colorPickerShow ) {
							if ( '' !== ui.color.toString() || 'undefined' !== ui.color.toString() ) {
								pickerInputWrap.find( '.wp-color-picker' ).show();
								placeholderElement.hide();
								clearElement.show();
								placeholderElement.css( 'line-height', '3.0' );
							}
						}

						clearElement.removeClass( 'default_color' );
						if ( defaultColor !== ui.color.toString() ) {
							clearElement.addClass( 'default_color' );
						}

						$( document ).trigger( 'wapo-colorpicker-change' );

					},
					clear: function(event, ui) {

						let pickerContainer    = $( this ).closest( '.wp-picker-container' );
						let pickerInputWrap    = pickerContainer.find( '.wp-picker-input-wrap' );
						let placeholderElement = pickerContainer.find( '.wp-picker-custom-placeholder' );
						let clearElement       = pickerContainer.find( '.wp-picker-default-custom' );
						let colorPickerShow    = pickerContainer.find( '.wp-color-picker' ).data( 'addon-colorpicker-show' );

						// Placeholder option to hide or not the necessary elements.
						if ( 'placeholder' === colorPickerShow ) {
							pickerInputWrap.find( '.wp-color-picker' ).hide();
							placeholderElement.show();
							clearElement.hide();
							placeholderElement.css( 'line-height', '0' );
						}

						clearElement.removeClass( 'testing123' );

						$( document ).trigger( 'wapo-colorpicker-clear' );

					}
			};

			function inicializeAddonColorpickers() {

				// Initialize each colorpicker with wpColorPicker function.
				$( '.yith-wapo-block .yith-wapo-addon-type-colorpicker .wp-color-picker' ).each(
					function() {
						$( this ).wpColorPicker( colorPicker_opt );

						let pickerContainer = $( this ).closest( '.wp-picker-container' );
						let pickerText      = pickerContainer.find( 'button .wp-color-result-text' );
						let clearButton     = pickerContainer.find( '.wp-picker-default' );
						let pickerInputWrap = pickerContainer.find( '.wp-picker-input-wrap' );
						let colorPickerShow = $( this ).data( 'addon-colorpicker-show' );
						let placeholder     = $( this ).data( 'addon-placeholder' );

						// Hide always the picker text
						pickerText.html( '' );

						// Create an custom element to show the custom Clear button.
						let wrap_main1 = $( this ).parents( '.wp-picker-container' ),
						wrap1          = wrap_main1.find( '.wp-picker-input-wrap' );

						if ( ! wrap_main1.hasClass( 'yith-wapo-colorpicker-initialized' ) ) {
							wrap_main1.addClass( 'yith-wapo-colorpicker-initialized' );
						}

						if ( ! wrap1.find( '.wp-picker-default-custom' ).length ) {
							var button = $( '<span/>' ).attr(
								{
									class: 'wp-picker-default-custom'
								}
							);
							wrap1.find( '.wp-picker-default, .wp-picker-clear' ).wrap( button );
						}

						// If it's placeholder option, create a custom element to show the placeholder label.
						if ( 'placeholder' === colorPickerShow ) {
							pickerInputWrap.find( '.wp-color-picker' ).hide();
							if ( ! pickerInputWrap.find( '.wp-picker-custom-placeholder' ).length ) {
								var placeholder_el = $( '<span/>' ).attr(
									{
										class: 'wp-picker-custom-placeholder',
									}
								);
								placeholder_el.html( placeholder );
								pickerInputWrap.find( '.screen-reader-text' ).before( placeholder_el );
							}
							let clearElement       = pickerContainer.find( '.wp-picker-default-custom' );
							let placeholderElement = pickerContainer.find( '.wp-picker-custom-placeholder' );

							clearElement.hide();
							placeholderElement.css( 'line-height', '0' );
						}

						clearButton.trigger( 'click' );

					}
				);
			}

			$( document ).on( 'yith-wapo-after-reload-addons', inicializeAddonColorpickers );
			$( document ).on( 'yith-wapo-after-reload-addons', initDatePickers );

			checkColorPickerOnInput = function() {
				$( document ).on(
					'click',
					function (e) {
						if ( ! $( e.target ).is( '.yith-wapo-colorpicker-container .iris-picker, .yith-wapo-colorpicker-container .iris-picker-inner' ) ) {
							let initializedColorPickers = $( '.yith-wapo-colorpicker-container .yith-wapo-colorpicker-initialized .wp-color-picker' );
							if ( initializedColorPickers.length > 0 ) {
								initializedColorPickers.iris( 'hide' );
							}
							return;
						}
					}
				);
				$( '.yith-wapo-colorpicker-container .yith-wapo-colorpicker-initialized .wp-color-picker' ).click(
					function ( event ) {
						$( this ).iris( 'show' );
						return;
					}
				);
			};

			inicializeAddonColorpickers();
			checkColorPickerOnInput();

		};

		/**
		 * Init the datepicker input
		 */
		initDatePickers = function() {

			// Initialize each colorpicker with wpColorPicker function.
			$( '.yith-wapo-block .yith_wapo_date.datepicker' ).each(
				function() {
					let datepicker_input = $( this );
					initDatePicker( datepicker_input );
				}
			);


		};

		initTimePicker = function( datepicker_input ) {
			let params             = datepicker_input.data( 'params' ),
				timeData           = params.time_data ? params.time_data : '',
				showTimeSelector    = params.show_time_selector ? params.show_time_selector : '';

			if ( typeof timeData === 'object' && timeData !== null ) {
				timeData = Object.values(timeData);
			}
			if ( showTimeSelector ) {
				setTimeout( function() {
					if ( ! $('#wapo-datepicker-time').length ) {
						var timeDataHTML = '',
							tempTimeEl = datepicker_input.closest( '.date-container' ).find( '.temp-time' ).text();
						$( timeData ).each(function( index, value ) {
							if ( value !== tempTimeEl ) {
								timeDataHTML += '<option>' + value + '</option>'
							} else {
								timeDataHTML += '<option selected>' + value + '</option>'
							}
						} );
						var timeHTML = '<div id="wapo-datepicker-time"><label>' + yith_wapo.i18n.datepickerSetTime + '</label><select id="wapo-datepicker-time-select">' +
							timeDataHTML
							+ '</select></div>'
							+ '<div id="wapo-datepicker-save"><button>' + yith_wapo.i18n.datepickerSaveButton + '</button></div>';

						$( timeHTML ).appendTo('#ui-datepicker-div');
					}
				}, 10 );
			}
		}

		initDatePicker = function( datepicker_input ) {

			var params = datepicker_input.data( 'params' ),
				minimumDate         = '',
				maximumDate         = '',
				startYear           = params.start_year ? params.start_year : '',
				endYear             = params.end_year ? params.end_year : '',
				defaultDateSelected = params.default_date ? params.default_date : '',
				dateFormat          = params.date_format ? params.date_format : '',
				additional_opts 	= params.additional_opts ? params.additional_opts : '';

			if ( startYear ) {
				minimumDate = new Date( params.start_year, '00', '01' );
			}
			if ( endYear ) {
				maximumDate = new Date( params.end_year, '11', '31' );
			}

			// datepicker options: https://api.jqueryui.com/datepicker/

			var datePicker_opts = {
				minDate: minimumDate,
				maxDate: maximumDate,
				defaultDate: defaultDateSelected,
				dateFormat: dateFormat,
				beforeShowDay: function( date ) {
					let params         = datepicker_input.data( 'params' ),
					selectableDaysOpt  = params.selectable_days_opt ? params.selectable_days_opt : '',
					selectableDays     = params.selectable_days ? params.selectable_days : '',
					selectedItems      = params.selected_items ? params.selected_items : '',
					enabled            = params.enable_disable_date_rules ? params.enable_disable_date_rules : '',
					returnValue    	   = true;

					enabled            = ( 'enable' === enabled ) ? 1 : 0;

					if ( enabled ) {
						returnValue = false;
					}

					// Selectable days (MIN/MAX)
					if ( 'days' === selectableDaysOpt || 'date' === selectableDaysOpt ) {
						let currentDate = date.getDate() + '-' + ( date.getMonth() + 1 ) + '-' + date.getFullYear();
						if ( -1 === $.inArray( currentDate, selectableDays ) ) {
							returnValue = false;
							return false;
						}
					//Disable days before current day.
					} else if ( 'before' === selectableDaysOpt ) {
						let currentDate = date.getTime();
						let todayDate   = new Date();
						todayDate.setHours( 0, 0, 0, 0 ); // Set date to midnight 
						todayDate       = todayDate.getTime();
						if ( currentDate < todayDate ){
							returnValue = false;
							return false;
						}
					}

					// Selected Items (Specific days)
					if ( selectedItems.length > 0 ) {
						selectedItems = JSON.parse( selectedItems );
						$.each( selectedItems, function( i, items ) {
								if ( 'days' === i ) {
									let currentDate = new Date( date );
									$.each( items, function ( i, item ) {
										let [ item_y, item_m, item_d ] = item.split('-');  
										let selectedDay = new Date( parseInt(item_y), parseInt(item_m) - 1, parseInt(item_d) );
										if (currentDate.toDateString() === selectedDay.toDateString()) {
											returnValue = !! enabled;
											return false;
										}
									});
								} else if ( 'daysweek' === i ) {
									let dayWeek = date.getDay();
									$.each( items, function ( i, item ) {
										$.each( item, function (e, day) {
											if (dayWeek == day) {
												returnValue = !! enabled;
												return false;
											}
										});
									});
								} else if ( 'months' === i ) {
									let dateMonth = date.getMonth();
									$.each( items, function( i, item ) {
										$.each( item, function( e, month ) {
											if ( dateMonth == month -1 ) {
												returnValue = !! enabled;
												return false;
											}
										} );
									} );
								} else if ( 'years' === i ) {
									let dateYear = date.getFullYear();
									$.each( items, function( i, item ) {
										$.each( item, function( e, year ) {
											if ( dateYear == year ) {
												returnValue = !! enabled;
												return false;
											}
										} );
									} );
								}
							});
					}


					if ( returnValue ) {
						return [true];
					}
					return [false];
				},
				beforeShow: function ( datepicker ) {
					initTimePicker( datepicker_input );
				},
				onSelect: function( dateText, obj ) {
					if ( $( obj.dpDiv ).find( '#wapo-datepicker-time-select' ).length > 0 ) {
						let timeSelected = $( obj.dpDiv ).find( '#wapo-datepicker-time-select' ).val(),
							tempTimeEl = $( this ).closest( '.date-container' ).find( '.temp-time' );
						$( this ).val( dateText + ' ' + timeSelected );
						tempTimeEl.text( timeSelected );
					}
				},
				onChangeMonthYear: function( year, month, inst ) {
					initTimePicker( datepicker_input );
				},
				onUpdateDatepicker: function( ins ) {
					jQuery( '#ui-datepicker-div' ).attr( 'wapo-option-id', ins.id );
				},
				onClose: function( date, ins ) {
					$( this ).trigger( 'yith_wapo_date_field_updated', date );
				},
			}

			// additional parameters added with the yith_wapo_datepicker_options filter
			datePicker_opts = Object.assign( datePicker_opts, additional_opts );

			datepicker_input.datepicker(
				datePicker_opts
			);

		}

		initColorpicker();
		initDatePickers();
	}
);

jQuery(
	function ($) {
		var firstVariationLoading = false;

		// Conditional logic.
		/**
		 * Conditional Logic
		 */
		yith_wapo_conditional_logic_check = function( lastFinalConditions = {} ) {
			var finalConditions = {};

			jQuery( 'form.cart .yith-wapo-addon.conditional_logic' ).each(
				function() {

					var AddonConditionSection = false,
						AddonVariationSection = false;

					var logicDisplay = jQuery(this).data('conditional_logic_display'); // show / hide

					// Applied to conditions.
					var logicDisplayIf = jQuery(this).data('conditional_logic_display_if'); // all / any

					var ruleAddon = String(jQuery(this).data('conditional_rule_addon')),
						ruleAddonIs = String(jQuery(this).data('conditional_rule_addon_is')),
						ruleVariation = String(jQuery(this).data('conditional_rule_variations'));

					ruleAddon = (typeof ruleAddon !== 'undefined' && ruleAddon !== "0" && ruleAddon !== '') ? ruleAddon.split('|') : false; // addon number
					ruleAddonIs = (typeof ruleAddonIs !== 'undefined' && ruleAddonIs !== '') ? ruleAddonIs.split('|') : false; // selected / not-selected / empty / not-empty
					ruleVariation = (typeof ruleVariation !== 'undefined' && ruleVariation !== '') ? ruleVariation.split('|') : false; // variations number

					if (!ruleVariation && (!ruleAddon || !ruleAddonIs)) {  // Show addon if no variations conditions or addons conditions.

						AddonConditionSection = true;
						AddonVariationSection = true;
						logicDisplay = 'show';

					} else {

						// ConditionLogic.
						if (ruleAddon && ruleAddonIs) {

							switch (logicDisplayIf) {

								case 'all':
									AddonConditionSection = conditionalLogicAllRules(ruleAddon, ruleAddonIs);
									break;

								case 'any':
									AddonConditionSection = conditionalLogicAnyRules(ruleAddon, ruleAddonIs);
									break;

							}

						} else {
							AddonConditionSection = true;
						}

						if (AddonConditionSection && ruleVariation) { // Prevent check variations if addons condition fails.
							var variationProduct = jQuery('.variation_id').val();
							if (-1 !== jQuery.inArray(String(variationProduct), ruleVariation)) {
								AddonVariationSection = true;
							}

						} else {
							AddonVariationSection = true;
						}

					}

					switch (logicDisplay) {

						case 'show' :

							if (AddonVariationSection && AddonConditionSection) { // Both conditions true --> apply logic Display
								finalConditions[jQuery(this).attr('id')] = 'not-hidden';
							} else {
								finalConditions[jQuery(this).attr('id')] = 'hidden';
							}
							break;

						case 'hide' :
							if (AddonVariationSection && AddonConditionSection) {  // Both conditions true --> apply logic Display
								finalConditions[jQuery(this).attr('id')] = 'hidden';
							} else {
								finalConditions[jQuery(this).attr('id')] = 'not-hidden';
							}
					}
				}
			);

			jQuery.each(
				finalConditions,
				function( id, mode ) {
					let element = jQuery( '#' + id );

					if ( 'not-hidden' === mode ) {

						// Todo: We avoid out of stock to change disabled value.
						element.fadeIn().removeClass( 'hidden' ).find( '.yith-wapo-option:not(.out-of-stock) .yith-wapo-option-value' ).attr( 'disabled', false );
						let selectedOption = element.find( '.yith-wapo-option.selected' );
						yith_wapo_replace_image( selectedOption );


						// Re-enable select add-ons if it was hidden
						if ( element.hasClass( 'yith-wapo-addon-type-select' ) ){
							element.find( '.yith-wapo-option-value' ).attr( 'disabled', false );
						}

						// Check the min_max after disable value.
						yith_wapo_check_min_max( element );
					} else {
						element.hide().addClass( 'hidden' ).find( '.yith-wapo-option-value' ).attr( 'disabled', true );
					}

				}
			);

			if ( JSON.stringify( finalConditions ) !== JSON.stringify( lastFinalConditions ) ) {
				yith_wapo_conditional_logic_check( finalConditions );
			}

		}

		/**
		 * Conditional Rule AND
		 *
		 * @param ruleAddon
		 * @param ruleAddonIs
		 * @returns {boolean}
		 */
		function conditionalLogicAllRules( ruleAddon, ruleAddonIs ) {
			var conditional = true;

			for ( var x = 0; x < ruleAddon.length; x++ ) {

				if ( ruleAddon[x] == 0 || ! ruleAddon[x] ) {
					continue;
				}

				var ruleAddonSplit = ruleAddon[x].split( '-' );
				var AddonSelected  = false;
				var AddonNotEmpty  = false;

				// variation check
				if ( typeof ruleAddonSplit[1] != 'undefined' ) {

					AddonSelected = ( // Selector or checkbox
						jQuery( '#yith-wapo-' + ruleAddonSplit[0] + '-' + ruleAddonSplit[1] ).is( ':checked' )
						|| jQuery( 'select#yith-wapo-' + ruleAddonSplit[0] ).val() == ruleAddonSplit[1]
					) && ! jQuery( '#yith-wapo-addon-' + ruleAddonSplit[0] ).hasClass( 'hidden' );

					var typeText     = jQuery( 'input#yith-wapo-' + ruleAddonSplit[0] + '-' + ruleAddonSplit[1] ).val();			// text
					var typeTextarea = jQuery( 'textarea#yith-wapo-' + ruleAddonSplit[0] + '-' + ruleAddonSplit[1] ).val();		// textarea
					AddonNotEmpty    = (
						( typeof typeText != 'undefined' && typeText !== '' )
						|| ( typeof typeTextarea != 'undefined' && typeTextarea !== '' )
					) && ! jQuery( '#yith-wapo-addon-' + ruleAddonSplit[0] ).hasClass( 'hidden' );

					// addon check
				} else {
					AddonSelected = (
						jQuery( '#yith-wapo-addon-' + ruleAddon[x] + ' input:checkbox:checked' ).length > 0
						|| jQuery( '#yith-wapo-addon-' + ruleAddon[x] + ' input:radio:checked' ).length > 0
						|| jQuery( '#yith-wapo-addon-' + ruleAddon[x] + ' option:selected' ).length > 0
						&& 'default' != jQuery( '#yith-wapo-addon-' + ruleAddon[x] + ' option:selected' ).val() // Check if not default value of Select Add-on
					);
					AddonSelected = AddonSelected && ! jQuery( '#yith-wapo-addon-' + ruleAddon[x] ).hasClass( 'hidden' );

					var typeText = 'undefined';
					jQuery( '#yith-wapo-addon-' + ruleAddonSplit[0] + ' input, #yith-wapo-addon-' + ruleAddonSplit[0] + ' textarea' ).each(
						function( index ){
							if ( jQuery( this ).val() !== '' ) {
								typeText = true;
								return;
							}
						}
					);
					AddonNotEmpty = (
						( typeText != 'undefined' && typeText !== '')
						// || (typeof typeTextarea != 'undefined' && typeTextarea !== '')
					) && ! jQuery( '#yith-wapo-addon-' + ruleAddonSplit[0] ).hasClass( 'hidden' );
				}

				switch ( ruleAddonIs[x]  ) {
					case 'selected' :
						if ( ! AddonSelected ) {
							conditional = false;
						}
						break;
					case 'not-selected':
						if ( AddonSelected ) {
							conditional = false;
						}
						break;

					case 'empty' :
						if ( AddonNotEmpty ) {
							conditional = false;
						}
						break;

					case 'not-empty' :
						if ( ! AddonNotEmpty ) {
							conditional = false;
						}

						break;
				}

				if ( ! conditional ) {
					break;
				}

			}

			return conditional;

		}

		/**
		 * Conditional Rule OR
		 *
		 * @param ruleAddon
		 * @param ruleAddonIs
		 * @returns {boolean}
		 */
		function conditionalLogicAnyRules( ruleAddon, ruleAddonIs ) {

			var conditional = false;

			for ( var x = 0; x < ruleAddon.length; x++ ) {

				if ( ruleAddon[x] == 0 || ! ruleAddon[x] ) {
					continue;
				}
				var ruleAddonSplit = ruleAddon[x].split( '-' );

				// variation check
				if (typeof ruleAddonSplit[1] != 'undefined') {

					AddonSelected = ( // Selector or checkbox
						jQuery( '#yith-wapo-' + ruleAddonSplit[0] + '-' + ruleAddonSplit[1] ).is( ':checked' )
						|| jQuery( 'select#yith-wapo-' + ruleAddonSplit[0] ).val() == ruleAddonSplit[1]
					) && ! jQuery( '#yith-wapo-addon-' + ruleAddonSplit[0] ).hasClass( 'hidden' );

					var typeText     = jQuery( 'input#yith-wapo-' + ruleAddonSplit[0] + '-' + ruleAddonSplit[1] ).val();			// text
					var typeTextarea = jQuery( 'textarea#yith-wapo-' + ruleAddonSplit[0] + '-' + ruleAddonSplit[1] ).val();		// textarea
					AddonNotEmpty    = (
						(typeof typeText != 'undefined' && typeText !== '')
						|| (typeof typeTextarea != 'undefined' && typeTextarea !== '')
					) && ! jQuery( '#yith-wapo-addon-' + ruleAddonSplit[0] ).hasClass( 'hidden' );

					// addon check
				} else {
					AddonSelected = (
						jQuery( '#yith-wapo-addon-' + ruleAddon[x] + ' input:checkbox:checked' ).length > 0
						|| jQuery( '#yith-wapo-addon-' + ruleAddon[x] + ' input:radio:checked' ).length > 0
						|| jQuery( '#yith-wapo-addon-' + ruleAddon[x] + ' option:selected' ).length > 0
						&& 'default' != jQuery( '#yith-wapo-addon-' + ruleAddon[x] + ' option:selected' ).val() // Check if not default value of Select Add-on
					);
					AddonSelected = AddonSelected && ! jQuery( '#yith-wapo-addon-' + ruleAddon[x] ).hasClass( 'hidden' );

					var typeText = 'undefined';
					jQuery( '#yith-wapo-addon-' + ruleAddonSplit[0] + ' input, #yith-wapo-addon-' + ruleAddonSplit[0] + ' textarea' ).each(
						function( index ){
							if ( jQuery( this ).val() !== '' ) {
								typeText = true;
								return;
							}
						}
					);
					AddonNotEmpty = (
						( typeText != 'undefined' && typeText !== '')
						// || (typeof typeTextarea != 'undefined' && typeTextarea !== '')
					) && ! jQuery( '#yith-wapo-addon-' + ruleAddonSplit[0] ).hasClass( 'hidden' );
				}

				switch ( ruleAddonIs[x] ) {
					case 'selected' :
						if ( AddonSelected ) {
							conditional = true;
						}
						break;
					case 'not-selected':
						if ( ! AddonSelected ) {
							conditional = true;
						}
						break;

					case 'empty' :
						if ( ! AddonNotEmpty ) {
							conditional = true;
						}
						break;

					case 'not-empty' :
						if ( AddonNotEmpty ) {
							conditional = true;
						}

						break;
				}
				if ( conditional ) {
					break;
				}
			}

			return conditional;
		}

    calculateAddonsPrice = function() {
			var firstFreeOptions = 0,
			  currentAddonID     = 0,
			  totalPrice         = 0,
			  quantity           = $( yith_wapo.productQuantitySelector ).val(); // Quantity of the Add to cart form.

			if ( ! quantity > 0) {
				quantity = 1;
			}

			$( 'form.cart .yith-wapo-addon:not(.hidden):visible input, form.cart .yith-wapo-addon:not(.hidden):visible select, form.cart .yith-wapo-addon:not(.hidden):visible textarea' ).each(
				function () {

					let option          = $( this ),
					defaultProductPrice = parseFloat( $( '#yith-wapo-container' ).attr( 'data-product-price' ) ),
					optionID            = option.data( 'addon-id' );

					if ( optionID ) {
						  let optionType = option.attr( 'type' ),
						  priceMethod    = option.data( 'price-method' ),
						  price          = 0,
						  priceType      = '',
						  addon          = option.parents( '.yith-wapo-addon' ),
						  addonType      = addon.data( 'addon-type' ),
						  addonQty       = 1;

						if ( 'number' === optionType && 0 == option.val() ) {
							return totalPrice;
						}

            if ( option.is( 'textarea' ) ) {
              optionType = 'textarea';
            }

						if (option.is( ':checked' ) || option.find( ':selected' ).is( 'option' )
						|| (option.is( 'input:not([type=checkbox])' ) && option.is( 'input:not([type=radio])' ) && option.val() != '')
						|| (option.is( 'textarea' ) && option.val() != '')
						  ) {

							if ( option.is( 'select' ) ) {
								  option = option.find( ':selected' );
							}

							if ('number' === optionType) {
								yith_wapo_check_multiplied_price( option );
							}

							if ('text' === optionType || 'textarea' === optionType) {
								yith_wapo_check_multiplied_length( option );
							}

							if ( currentAddonID != optionID ) {
								currentAddonID   = option.data( 'addon-id' );
								firstFreeOptions = option.data( 'first-free-options' );
							}

							if ( option.data( 'first-free-enabled' ) == 'yes' && firstFreeOptions > 0) {
								firstFreeOptions--;
							} else {
								if ( typeof option.data( 'price-type' ) != 'undefined' && '' !== option.data( 'price-type' ) ) {
									priceType = option.data( 'price-type' ); // Percentage or fixed.
								}

								let dataPriceSale = option.data( 'price-sale' ),
								dataPrice         = option.data( 'price' );

								if (typeof dataPriceSale != 'undefined' && '' !== dataPriceSale && dataPriceSale >= 0 && 'multiplied' !== priceType ) {
									price = parseFloat( dataPriceSale );
								} else if (typeof dataPrice != 'undefined' && '' !== dataPrice ) {
									price = parseFloat( dataPrice );
								}

								if ( 'percentage' === priceType && 'discount' !== priceMethod ) {
									price = ( price * defaultProductPrice ) / 100;
								}

								if ( 'product' === addonType ) {
									if ( ! option.hasClass( '.yith-wapo-option' ) ) {
										option   = option.parents( '.yith-wapo-option' );
										addonQty = option.find( '.wapo-product-qty' );
										if ( addonQty ) {
											addonQty = addonQty.val();
											if ( addonQty > 1 ) {
												price = price * addonQty;
											}
										}
									}
								}

								// Multiply price by quantity. Not multiplied for Sell individually add-ons ( it will be 1 on cart ).
								if ( quantity > 1 && ! addon.hasClass( 'sell_individually' ) ) {
									price = price * quantity;
								}

								totalPrice += price;
							}
						}
					}
				}
			);

			return totalPrice;
		};

		setTotalBoxPrices = function( defaultProductPrice, totalPrice, replacePrice = true ) {
			var totalCurrency  = yith_wapo.woocommerce_currency_symbol,
			  totalCurrencyPos = yith_wapo.woocommerce_currency_pos,
			  totalThousandSep = yith_wapo.total_thousand_sep,
			  totalDecimalSep  = yith_wapo.decimal_sep,
			  totalPriceNumDec = yith_wapo.num_decimal,
			  quantity         = $( yith_wapo.productQuantitySelector ).val();

			if ( ! quantity > 0) {
				quantity = 1;
			}

			var totalProductPrice = defaultProductPrice * quantity,
			totalOptionsPrice     = parseFloat( totalPrice ),
			totalOrderPrice       = parseFloat( totalPrice + totalProductPrice ),
			totalOrderPriceHtml   = totalOrderPrice;

			// Price without formatting.
			var total_ProductPrice = totalProductPrice,
			total_OptionsPrice     = totalOptionsPrice;

			// Price formatting
			totalProductPrice = totalProductPrice.toFixed( totalPriceNumDec ).replace( '.', totalDecimalSep ).replace( /(\d)(?=(\d{3})+(?!\d))/g, '$1' + totalThousandSep );
			totalOptionsPrice = totalOptionsPrice.toFixed( totalPriceNumDec ).replace( '.', totalDecimalSep ).replace( /(\d)(?=(\d{3})+(?!\d))/g, '$1' + totalThousandSep );
			totalOrderPrice   = totalOrderPrice.toFixed( totalPriceNumDec ).replace( '.', totalDecimalSep ).replace( /(\d)(?=(\d{3})+(?!\d))/g, '$1' + totalThousandSep );

			if (totalCurrencyPos == 'right') {
				totalProductPrice   = totalProductPrice + totalCurrency;
				totalOptionsPrice   = totalOptionsPrice + totalCurrency;
				totalOrderPriceHtml = totalOrderPrice + totalCurrency;
			} else if (totalCurrencyPos == 'right_space') {
				totalProductPrice   = totalProductPrice + ' ' + totalCurrency;
				totalOptionsPrice   = totalOptionsPrice + ' ' + totalCurrency;
				totalOrderPriceHtml = totalOrderPrice + ' ' + totalCurrency;
			} else if (totalCurrencyPos == 'left_space') {
				  totalProductPrice   = totalCurrency + ' ' + totalProductPrice;
				  totalOptionsPrice   = totalCurrency + ' ' + totalOptionsPrice;
				  totalOrderPriceHtml = totalCurrency + ' ' + totalOrderPrice;
			} else {
				totalProductPrice   = totalCurrency + totalProductPrice;
				totalOptionsPrice   = totalCurrency + totalOptionsPrice;
				totalOrderPriceHtml = totalCurrency + totalOrderPrice;
			}

			if ( yith_wapo.priceSuffix ) {
				calculateProductPrice();
			} else {
				$( '#wapo-total-product-price' ).html( totalProductPrice );
			}

      replaceProductPrice( replacePrice, totalOrderPrice, totalOrderPriceHtml );

			$( '#wapo-total-options-price' ).html( totalOptionsPrice );
			$( '#wapo-total-order-price' ).html( totalOrderPriceHtml );

			$( '#wapo-total-price-table' ).css( 'opacity', '1' );

			$( document ).trigger( 'yith_wapo_product_price_updated', [total_ProductPrice + total_OptionsPrice] );
		},

    replaceProductPrice = function ( replacePrice, totalOrderPrice, totalOrderPriceHtml ) {
	  if ( 'yes' !== yith_wapo.replace_price_in_product_without_addons && ( ! $( '#yith-wapo-container' ).length || ! $( '#yith-wapo-container' ).find('.yith-wapo-block').length ) ){
		return;
	  }
      if ( replacePrice && 'yes' === yith_wapo.replace_product_price && ! isNaN( parseFloat( totalOrderPrice ) ) && $( yith_wapo.replace_product_price_class ).length > 0 ) {
        let priceSuffix   = yith_wapo.priceSuffix,
          priceSuffixHtml = '';

        if ( priceSuffix ) {
          priceSuffixHtml = ' <small class="woocommerce-price-suffix">' + priceSuffix + '</small>';
        }
        $( yith_wapo.replace_product_price_class ).html( '<span class="woocommerce-Price-amount amount"><bdi>' + totalOrderPriceHtml + '</bdi></span>' + priceSuffixHtml );
        let productPrice    = $( yith_wapo.replace_product_price_class + ' bdi' ).text();
		if ( wcPriceToFloat( productPrice ) === 0 ) {
          $( yith_wapo.replace_product_price_class ).find( 'bdi' ).remove();
        }
      }
    },

		calculateProductPrice = function () {
			  var data_price_suffix = {
					'action'	: 'update_totals_with_suffix',
					'product_id'	: parseInt( $( '#yith-wapo-container' ).attr( 'data-product-id' ) ),
					'security'  : yith_wapo.addons_nonce,
			};
			jQuery.ajax(
				{
					url : yith_wapo.ajaxurl,
					type : 'post',
					data : data_price_suffix,
					success : function( response ) {
						if ( response ) {
							let totalProductPrice = response['price_html'];
							$( '#wapo-total-product-price' ).html( totalProductPrice );

						}
					}
				}
			);
		},

		calculateTotalAddonsPrice = function (replacePrice = true) {

		  //Check logical conditions before calculate prices.
		  yith_wapo_conditional_logic_check();

		  if ( 'yes' === yith_wapo.hide_button_required ) {
				yith_wapo_check_required_fields( 'hide' );
			}

			$( '#wapo-total-price-table' ).css( 'opacity', '0.5' );
			var totalPrice          = 0;
			var defaultProductPrice = parseFloat( $( '#yith-wapo-container' ).attr( 'data-product-price' ) );
			var totalPriceBoxOption = yith_wapo.total_price_box_option;

			let selectedGifCardAmountButton = $( 'button.ywgc-amount-buttons.selected_button' );

			if ( selectedGifCardAmountButton.length > 0 ) {
				  defaultProductPrice = selectedGifCardAmountButton.data( 'price' );
			}

			totalPrice = calculateAddonsPrice();

			// Plugin option "Total price box".
			if ( 'hide_options' === totalPriceBoxOption ) {
				if ( 0 !== totalPrice ) {
					$( '#wapo-total-price-table .hide_options tr.wapo-total-options' ).fadeIn();
				} else {
					$( '#wapo-total-price-table .hide_options tr.wapo-total-options' ).hide();
				}
			}

			setTotalBoxPrices( defaultProductPrice, totalPrice, replacePrice );

		};

		productQuantityChange = function () {
			let inputNumber   = $( this ),
			  inputVal        = inputNumber.val(),
			  productId       = inputNumber.closest( '.yith-wapo-option' ).data( 'product-id' ),
			  addToCartLink   = inputNumber.closest( '.option-add-to-cart' ).find( '.add_to_cart_button' ),
			  productQuantity = 1,
			  hrefCreated     = '';

			if ( addToCartLink.length && productId ) {
				if ( inputVal > 1 ) {
					 productQuantity = inputVal;
				}

				hrefCreated = '?add-to-cart=' + productId + '&quantity=' + productQuantity;

				addToCartLink.attr( 'href', hrefCreated );
			}

		};

		wcPriceToFloat = function (wc_price){
			let price = wc_price.replace( /(?![\.\,])\D/g, '' )
			  .replace( yith_wapo.total_thousand_sep, '' )
			  .replace( yith_wapo.decimal_sep, '.' );

			return parseFloat( price );
		},
      getDefaultProductPrice = function () {
			if ( yith_wapo.enableGetDefaultVariationPrice ) {
				let product_id = $( '.variations_form.cart' ).data( 'product_id' );
				let data = {
					'action' : 'get_default_variation_price',
					'product_id' : parseInt( product_id ),
					'security'  : yith_wapo.addons_nonce,
				};
				jQuery.ajax(
					{
						url : yith_wapo.ajaxurl,
						type : 'post',
						data : data,
						success : function( response ) {
						  if ( response ) {
							let defaultProductPrice = response['price_html'];
							let container = jQuery( '#yith-wapo-container' );
							container.attr( 'data-product-price', response['current_price'] );
							container.attr( 'data-product-id', product_id );

							if ( 'yes' === yith_wapo.replace_product_price && container.find('.yith-wapo-block').length ) {
								$( yith_wapo.replace_product_price_class ).html( defaultProductPrice );
							}

						  }
						},
						complete: function (){
						}
					}
				);
			}
      },

      /**
       * Check the default options selected on load page to replace the image.
       */
      checkDefaultOptionsOnLoad = function() {
        let optionsSelected =  $( '.yith-wapo-addon:not(.conditional_logic) .yith-wapo-option.selected' );
        $( optionsSelected ).each(
          function() {
            let option = $( this );
            yith_wapo_replace_image( option );
          }
        );
      },

      resetAddons = function ( event, params ) {

        if ( 'yith_wccl' === params ) {
          return;
        }

        if ( ! firstVariationLoading ) {
          firstVariationLoading = true;
          return;
        }

        getDefaultProductPrice();
        //let container = jQuery( '#yith-wapo-container' );
        //container.attr( 'data-product-price', 0 );

        $( document ).trigger( 'yith-wapo-reset-addons' );

      },
      foundVariation = function( event, variation ) {
        updateContainerProductPrice( variation );
        $( document ).trigger( 'yith-wapo-reload-addons' );
      },

      reloadAddons = function ( event, productPrice = '' ) {
        var addons = $( 'form.cart' ).serializeArray(),
        container = $( 'form.cart:not(.ywcp) #yith-wapo-container' ),
        data   = {
          	'action'	: 'live_print_blocks',
          	'addons'	: addons,
			'currency'	: yith_wapo.woocommerce_currency,
			'current_language' : yith_wapo.currentLanguage,

		};

        if ( productPrice != '' ) {
          data.price = productPrice;
        }

        $( '#yith-wapo-container' ).css( 'opacity', '0.5' );

        $.ajax(
          {
            url : yith_wapo.ajaxurl,
            type : 'post',
            data : data,
            success : function( response ) {
              container.html( response );
              container.css( 'opacity', '1' );

              $( 'form.cart' ).trigger( 'yith-wapo-after-reload-addons' );

              calculateTotalAddonsPrice();

            },
          }
        );
      },

      removeUploadedFile = function( ev ) {
        let element = ev.target,
          uploadedFileContainer = $( element ).closest( '.yith-wapo-uploaded-file-element' ),
          maxMultiple = $( element ).closest( '.yith-wapo-option' ).data( 'max-multiple' ),
          lengthUploads = $( element ).closest( '.yith-wapo-ajax-uploader' ).find( '.yith-wapo-uploaded-file-element' ).length,
          uploaderContainer = $( element ).closest( '.yith-wapo-ajax-uploader' ).find( '.yith-wapo-ajax-uploader-container' ),
          parentInputHidden = $( element ).closest('.yith-wapo-option' ).find( 'input[type="hidden"].upload-parent' );

        uploadedFileContainer.remove();

        if ( 'undefined' === typeof maxMultiple || lengthUploads-1 < maxMultiple ) { // If max is not defined or length is less than max, show the Upload button.
			uploaderContainer.fadeIn();
        }
        if ( lengthUploads-1 <= 0 ) {
          parentInputHidden.val('');
          calculateTotalAddonsPrice();
        }

      },

      /**
       * Check min and max values for the sum of add-ons type Number.
       */
      checkNumbersTotalValues = function () {

			var numberAddons = $( '#yith-wapo-container .yith-wapo-addon-type-number:not(.hidden).numbers-check' ),
        isError = false;

      numberAddons.each( function( index ) {

        let numberAddon = $( this ),
        numberMin = numberAddon.data( 'numbers-min' ),
        numberMax = numberAddon.data( 'numbers-max' ),
        totalCount = 0,
        errorCheck = false,
        errorMessage = '',
        optionsElement = numberAddon.find( '.options' );

        // Reset
        if ( optionsElement.hasClass( 'error-message' ) ) {
          optionsElement.removeClass( 'error-message' );
        }
        numberAddon.find( '.yith-wapo-numbers-error-message' ).remove();

        numberAddon.find('input[type="number"]').each(function(){
          let number = $(this).val();
          if ( 'undefined' === number || '' === number ){
            return true; // continue
          }
          totalCount += parseFloat( number );
        });

        if ( 'undefined' !== typeof numberMin && totalCount < numberMin ) {
          errorCheck = true;
          errorMessage = yith_wapo.messages.minErrorMessage + ' ' + numberMin;
        };

        if ( 'undefined' !== typeof numberMax && totalCount > numberMax ) {
          errorCheck = true;
          errorMessage = yith_wapo.messages.maxErrorMessage + ' ' + numberMax;
        };

        if ( errorCheck ) {
          optionsElement.addClass( 'error-message' );
          numberAddon.append( $( '<small class="yith-wapo-numbers-error-message">' + errorMessage + '</small>' ) );
          isError = true;
		  jQuery( 'html, body' ).animate( { scrollTop: numberAddon.offset().top - 50 }, 500 );
		}

      });

	  $( document ).trigger( 'yith_wapo_check_number_total_values' );

      if ( isError ) {
		  return false;
      }

      return true;

		};


	  checkMaxSelected = function ( element ) {

		  var option = element.closest( '.yith-wapo-option' ),
			  addon = element.closest( '.yith-wapo-addon' ),
			  maxVal = addon.data( 'max' ),
			  optionsSelected = addon.find( '.yith-wapo-option.selected' ).length;

		  if ( '' === maxVal || 0 === maxVal ) {
			  return true;
		  }

		  if ( option.hasClass( 'selected' ) ) {
			  optionsSelected--;
		  } else {
			  optionsSelected++;
		  }

		  if ( optionsSelected > maxVal ) {
			  return false;
		  }

		  return true;

	  },

	  labelsOnChange = function() {

		  /**
		   * Check max value available.
		   * @type {boolean}
		   */
		  var maxSelected = checkMaxSelected( $(this) );

		  if ( ! maxSelected ) {
			  $(this).prop( 'checked', false );
			  return false;
		  }

		  var optionWrapper = $( this ).parent();

			// Proteo check
			if ( ! optionWrapper.hasClass( 'yith-wapo-option' ) ) {
				optionWrapper = optionWrapper.closest( '.yith-wapo-option' );
			}

			if ( $( this ).is( ':checked' ) ) {

				// Single selection
				if ( optionWrapper.hasClass( 'selection-single' ) ) {
					// Disable all
					optionWrapper.parent().find('input').prop('checked',false);
					optionWrapper.parent().find( '.selected' ).removeClass( 'selected' );
				}
				optionWrapper.addClass( 'selected' );
				// Check checkbox of selected one.
				optionWrapper.find( 'input' ).prop('checked', true);

				// Replace image
			 yith_wapo_replace_image( optionWrapper );

			} else {
				optionWrapper.removeClass( 'selected' );
				yith_wapo_replace_image( optionWrapper, true );
			}
		}

	  // Calculate Add-ons price triggers
		$( document ).on(
			'ywgc-amount-changed',
			function( e, button_amount ) {
				let price     = button_amount.data( 'price' );
				let container = jQuery( '#yith-wapo-container' );

				container.attr( 'data-product-price', price );
				calculateTotalAddonsPrice();
			}
		);

		/**
		 * Since 4.0.0 - Allow external plugins the possibility to recalculate totals, after having changed the price.
		 *
		 * Price param is necessary.
		 */
		$( document ).on(
			'yith-wapo-product-price-updated',
			function ( e, price ) {
				if ( typeof price !== 'undefined' ) {
					$( '#yith-wapo-container' ).attr( 'data-product-price', price );
				}
				calculateTotalAddonsPrice();
			}

		);

		$( document ).on( 'change', '.gift-cards-list .ywgc-manual-amount-container input.ywgc-manual-amount', function( e ) {
			let t     = $( this ),
				price = t.val();

			let container = jQuery( '#yith-wapo-container' );

			container.attr( 'data-product-price', price );
			calculateTotalAddonsPrice();
		} );

		// Dynamic compatibility.
		$( document ).on(
			'ywdpd_price_html_updated',
			function( e, html_price ) {
				let price = jQuery( html_price ).children( '.amount bdi' ).text();
				price     = wcPriceToFloat( price );

				if ( ! isNaN( price ) ) {
					let container = jQuery( '#yith-wapo-container' );

					container.attr( 'data-product-price', price );
					calculateTotalAddonsPrice();
				}
			}
		);

		$( document ).on(
			'yith_wcpb_ajax_update_price_request',
			function( e, response ) {
				let price = jQuery( response.price_html ).children( '.amount bdi' ).text();
				price     = wcPriceToFloat( price );

				if ( ! isNaN( price ) ) {
					let container = jQuery( '#yith-wapo-container' );

					container.attr( 'data-product-price', price );
					calculateTotalAddonsPrice();
				}
			}
		);

		$( document ).on(
			'change',
			'form.cart div.yith-wapo-addon, form.cart .quantity input[type=number]',
			function () {
				calculateTotalAddonsPrice();
			}
		);
		$( document ).on(
			'keyup',
			'form.cart .yith-wapo-addon-type-number input[type="number"], form.cart .yith-wapo-addon-type-text input[type="text"], form.cart .yith-wapo-addon-type-textarea textarea',
			function () {
				calculateTotalAddonsPrice();
			}
		);
		$( document ).on(
			'click',
			'form.cart .yith-wapo-addon-type-colorpicker .yith-wapo-colorpicker-initialized input.wp-color-picker',
			function () {
				calculateTotalAddonsPrice();
			}
		);
		$( document ).on(
			'wapo-colorpicker-change',
			function() {
				calculateTotalAddonsPrice();
			}
		);

    calculateTotalAddonsPrice();
    checkDefaultOptionsOnLoad();

    /** Product quantity change */
    $( document ).on( 'change keyup', '.yith-wapo-option .wapo-product-qty', productQuantityChange );
    $( document ).on( 'reset_data', resetAddons );
    $( document ).on( 'found_variation', foundVariation );

    /** ajax reload addons **/
    $( document ).on( 'yith-wapo-reload-addons', reloadAddons );

    /** Uploads */
    $( document ).on( 'click', '.yith-wapo-uploaded-file .remove', removeUploadedFile );

	/* Labels onChange */
	$( document ).on( 'change', '.yith-wapo-addon-type-label input', labelsOnChange );


	}
);

// addon type (checkbox)

jQuery( document ).on(
	'change',
	'.yith-wapo-addon-type-checkbox input',
	function() {
		let checkboxInput   = jQuery( this );
		let checkboxButton  = checkboxInput.parents( '.checkboxbutton' );
		let checkboxOption  = checkboxInput.parents( '.yith-wapo-option' );
		let checkboxOptions = checkboxOption.parent();

		let isChecked = checkboxInput.attr( 'checked' );

		if ( 'checked' !== isChecked ) {

			// Single selection
			if ( checkboxOption.hasClass( 'selection-single' ) ) {
				// Disable all.
				checkboxOptions.find( 'input' ).attr( 'checked', false );
				checkboxOptions.find( 'input' ).prop( 'checked', false );
				checkboxOptions.find( '.selected, .checked' ).removeClass( 'selected checked' );
			}

			// Enable only the current option.
			checkboxInput.attr( 'checked', true );
			checkboxInput.prop( 'checked', true );
			checkboxOption.addClass( 'selected' );
			checkboxButton.addClass( 'checked' );

			// Replace image
			yith_wapo_replace_image( checkboxOption );

		} else {
			checkboxInput.attr( 'checked', false );
			checkboxInput.prop( 'checked', false );
			checkboxOption.removeClass( 'selected' );
			checkboxButton.removeClass( 'checked' );

			yith_wapo_replace_image( checkboxOption, true );
		}
	}
);

// addon type (color)
jQuery( document ).on(
  'click',
  '.yith-wapo-addon-type-color .yith-wapo-option div.label',
  function() {
    jQuery( this ).closest( '.yith-wapo-option' ).find( '.yith-proteo-standard-checkbox' ).click();
  }
);

jQuery( document ).on(
	'change',
	'.yith-wapo-addon-type-color input',
	function() {
		var optionWrapper = jQuery( this ).parent();
		// Proteo check
		if ( ! optionWrapper.hasClass( 'yith-wapo-option' ) ) {
			optionWrapper = optionWrapper.parent(); }
		if ( jQuery( this ).is( ':checked' ) ) {
			optionWrapper.addClass( 'selected' );

			// Single selection
			if ( optionWrapper.hasClass( 'selection-single' ) ) {
				// Disable all
				optionWrapper.parent().find( 'input' ).prop( 'checked', false );
				optionWrapper.parent().find( '.selected' ).removeClass( 'selected' );
				// Enable only the current option
				optionWrapper.find( 'input' ).prop( 'checked', true );
				optionWrapper.addClass( 'selected' );
			}

			// Replace image
			yith_wapo_replace_image( optionWrapper );

		} else {
			optionWrapper.removeClass( 'selected' );
			yith_wapo_replace_image( optionWrapper, true );
		}
	}
);

// addon type (label)

jQuery( document ).on(
  'click',
  '.yith-wapo-addon-type-label .yith-wapo-option div.label',
  function( ev ) {
    ev.preventDefault();
    jQuery( this ).closest( '.yith-wapo-option' ).find( '.yith-proteo-standard-checkbox' ).click();
  }
);

// addon type (product)
jQuery( document ).on( 'click change', '.yith-wapo-addon-type-product .quantity input',
	function (e) {
		e.stopPropagation();
	}
);

jQuery( document ).on(
  'click',
  '.yith-wapo-addon-type-product .yith-wapo-option .product-container',
  function() {
    jQuery( this ).closest( '.yith-wapo-option' ).find( '.yith-proteo-standard-checkbox' ).click();
  }
);

jQuery( document ).on(
	'change',
	'.yith-wapo-addon-type-product .yith-wapo-option input.yith-proteo-standard-checkbox',
	function() {

		var optionWrapper = jQuery( this ).parent();// Proteo check
		// Proteo check
		if ( ! optionWrapper.hasClass( 'yith-wapo-option' ) ) {
			optionWrapper = optionWrapper.parent(); }
		if ( jQuery( this ).is( ':checked' ) ) {
			optionWrapper.addClass( 'selected' );

			// Single selection
			if ( optionWrapper.hasClass( 'selection-single' ) ) {
				// Disable all
				optionWrapper.parent().find( 'input' ).prop( 'checked', false );
				optionWrapper.parent().find( '.selected' ).removeClass( 'selected' );
				// Enable only the current option
				optionWrapper.find( 'input' ).prop( 'checked', true );
				optionWrapper.addClass( 'selected' );
			}

			// Replace image
			yith_wapo_replace_image( optionWrapper );

		} else {
			optionWrapper.removeClass( 'selected' );
			yith_wapo_replace_image( optionWrapper, true );
		}
	}
);

// addon type (radio)

jQuery( document ).on(
	'click',
	'.yith-wapo-addon-type-radio input',
	function() {
		var optionWrapper = jQuery( this ).closest( '.yith-wapo-option' );
		// Proteo check
		if ( ! optionWrapper.hasClass( 'yith-wapo-option' ) ) {
			optionWrapper = optionWrapper.closest( '.yith-wapo-option' );
    	}
		if ( jQuery( this ).is( ':checked' ) ) {
			optionWrapper.addClass( 'selected' );

			// Remove selected siblings
			optionWrapper.siblings().removeClass( 'selected' );

			// Replace image
			yith_wapo_replace_image( optionWrapper );

		} else {
			optionWrapper.removeClass( 'selected' ); }
	}
);

// addon type (select)

jQuery( 'body' ).on(
	'change',
	'.yith-wapo-addon-type-select select',
	function() {
		let optionWrapper    = jQuery( this ).parent();
		let selectedOption   = jQuery( this ).find( 'option:selected' );
		let optionImageBlock = optionWrapper.find( 'div.option-image' );
		// Proteo check
		if ( ! optionWrapper.hasClass( 'yith-wapo-option' ) ) {
			optionWrapper = optionWrapper.parent();
		}

		// Description & Image.
		var optionImage       = selectedOption.data( 'image' );
		var optionDescription = selectedOption.data( 'description' );
		var option_desc       = optionWrapper.find( 'p.option-description' );

		if ( typeof optionImage !== 'undefined' && optionImage ) {
			optionImage = '<img src="' + optionImage + '" style="max-width: 100%">';
			optionImageBlock.html( optionImage );
		}

		if ( 'default' === selectedOption.val() || '' === optionImage ) {
			optionImageBlock.hide();
		} else {
			optionImageBlock.fadeIn();
		}

		if ( 'undefined' === typeof optionDescription ) {
			option_desc.empty();
		} else {
			option_desc.html( optionDescription );
		}

		// Replace image
		if ( selectedOption.data( 'replace-image' ) ){
			yith_wapo_replace_image( selectedOption );
		} else {
			yith_wapo_replace_image( selectedOption, true );
		}

	}
);
jQuery( '.yith-wapo-addon-type-select select' ).trigger( 'change' );



// toggle feature

jQuery( document ).on(
	'click',
	'.yith-wapo-addon.wapo-toggle .addon-header',
	function( e ) {
		e.preventDefault();
		let addon_title = jQuery( this ).find( '.wapo-addon-title' );
		let addon_el    = addon_title.closest( '.yith-wapo-addon' );

		if ( addon_el.hasClass( 'toggle-open' ) ) {
			addon_el.removeClass( 'toggle-open' ).addClass( 'toggle-closed' );
		} else {
			addon_el.removeClass( 'toggle-closed' ).addClass( 'toggle-open' );
		}
		if ( addon_title.hasClass( 'toggle-open' ) ) {
			addon_title.removeClass( 'toggle-open' ).addClass( 'toggle-closed' );
		} else {
			addon_title.removeClass( 'toggle-closed' ).addClass( 'toggle-open' );
		}

		addon_el.find( '.options-container' ).toggle( 'fast' );

		jQuery( document ).trigger( 'yith_proteo_inizialize_html_elements' );
	}
);






// function: replace image

function yith_wapo_replace_image( optionWrapper, reset = false ) {

	var defaultPath     = yith_wapo.replace_image_path;
	var zoomMagnifier   = '.yith_magnifier_zoom_magnifier, .zoomWindowContainer .zoomWindow';
	var replaceImageURL = optionWrapper.data( 'replace-image' );

	if ( null === replaceImageURL || ! reset && jQuery( defaultPath ).attr( 'src' ) === replaceImageURL ) {
		return;
	}

	if ( typeof optionWrapper.data( 'replace-image' ) !== 'undefined' && optionWrapper.data( 'replace-image' ) != '' ) {

		// save original image for the reset
		if ( typeof( jQuery( defaultPath ).attr( 'wapo-original-img' ) ) == 'undefined' ) {
			jQuery( defaultPath ).attr( 'wapo-original-img', jQuery( defaultPath ).attr( 'src' ) );
			if ( jQuery( zoomMagnifier ).length ) {
				jQuery( zoomMagnifier ).attr( 'wapo-original-img', jQuery( zoomMagnifier ).css( 'background-image' ).slice( 4, -1 ).replace( /"/g, "" ) );
			}
		}
		jQuery( defaultPath ).attr( 'src', replaceImageURL );
		jQuery( defaultPath ).attr( 'srcset', replaceImageURL );
		jQuery( defaultPath ).attr( 'data-src', replaceImageURL );
		jQuery( zoomMagnifier ).css( 'background-image', 'url(' + replaceImageURL + ')' );
		jQuery( '#yith_wapo_product_img' ).val( replaceImageURL );
		jQuery( defaultPath ).attr( 'data-large_image', replaceImageURL );

		// Reset gallery position when add-on image change
		if ( jQuery( '.woocommerce-product-gallery .woocommerce-product-gallery__image' ).length > 0 ) {
			jQuery( '.woocommerce-product-gallery' ).trigger( 'woocommerce_gallery_reset_slide_position' );
		}
		jQuery( '.woocommerce-product-gallery' ).trigger( 'woocommerce_gallery_init_zoom' );
		jQuery( document ).trigger( 'yith-wapo-after-replace-image' );
	}

	if ( reset && typeof( jQuery( defaultPath ).attr( 'wapo-original-img' ) ) != 'undefined' ) {
		let checkReset = true;
		jQuery( ".yith-wapo-option" ).each(
			function( index, element ) {
				let option = jQuery( element );
				// Check if one option is still selected and has a image to replace, then do not change to default image.
				if ( option.data( 'replace-image' ) && option.hasClass( 'selected' ) ) {
					  checkReset = false;
				}
			}
		);
		if ( checkReset ) {
			var originalImage = jQuery( defaultPath ).attr( 'wapo-original-img' );
			var originalZoom  = jQuery( zoomMagnifier ).attr( 'wapo-original-img' );

			jQuery( defaultPath ).attr( 'src', originalImage );
			jQuery( defaultPath ).attr( 'srcset', originalImage );
			jQuery( defaultPath ).attr( 'data-src', originalImage );
			jQuery( defaultPath ).attr( 'data-large_image', originalImage );
			jQuery( zoomMagnifier ).css( 'background-image', 'url(' + originalZoom + ')' );

			// Reset gallery position when add-on image change
			if ( jQuery( '.woocommerce-product-gallery .woocommerce-product-gallery__image' ).length > 0 ) {
				jQuery( '.woocommerce-product-gallery' ).trigger( 'woocommerce_gallery_reset_slide_position' );
			}
			jQuery( '.woocommerce-product-gallery' ).trigger( 'woocommerce_gallery_init_zoom' );
			jQuery( document ).trigger( 'yith-wapo-after-replace-image' );
		}
	}
}

/** Check 'required' feature */
function yith_wapo_check_required_fields( action ) {

	var isRequired    = false;
	var hideButton    = false;
	var buttonClasses = yith_wapo.dom.single_add_to_cart_button;
	jQuery( 'form.cart .yith-wapo-addon:not(.hidden):visible input, form.cart .yith-wapo-addon:not(.hidden):visible select, form.cart .yith-wapo-addon:not(.hidden):visible textarea' ).each(
		function() {
			let element            = jQuery( this );
			let parent             = element.closest( '.yith-wapo-option' );
			let addon             = element.closest( '.yith-wapo-addon' );
			let toggle_addon       = element.closest( 'div.yith-wapo-addon.wapo-toggle' );
			let toggle_addon_title = toggle_addon.find( 'h3.wapo-addon-title.toggle-closed' );
			let addonTitle         = addon.find( '.wapo-addon-title' );

		  if ( 'file' === element.attr( 'type' ) || element.hasClass( 'wapo-product-qty' ) ) {
			return;
		  }
			if (
			element.attr( 'required' ) && ( 'checkbox' === element.attr( 'type' )
      || 'radio' === element.attr( 'type' ) ) && ! element.closest( '.yith-wapo-option' ).hasClass( 'selected' )
			|| element.attr( 'required' ) && ( element.val() == '' || element.val() == 'Required' )
			) {
				if ( action === 'highlight' ) {
				  	// Add required message.
				  	showRequiredMessage( element );
					addonTitle.addClass( 'wapo-error' );


					// Open toggle.
					if ( toggle_addon_title ) {
						toggle_addon_title.click();
					}
				}

				  hideButton = true;
				  isRequired = true;
			} else {
        // Restart default required status.
        restartRequiredElement( element );
      }
		}
	);
	if ( action == 'hide' ) {

		jQuery( buttonClasses ).fadeIn();
		if ( hideButton ) {
				jQuery( buttonClasses ).hide();
		}
	}
	return ! isRequired;
}

/** Print the required element with the message and colors */
showRequiredMessage = function( element ) {
  let option = element.closest( '.yith-wapo-option' );
  if ( option.find( '.required-error' ).length < 1 ) {
    option.append(
      '<div class="required-error">' +
        '<small class="required-message">' + yith_wapo.messages.requiredMessage + '</small>' +
      '</div>'
    );

    option.addClass( 'required-color' );
  }

}
  /** Restart the required element (removing it) and remove Color classes */
restartRequiredElement = function( element ) {
  let option = element.closest( '.yith-wapo-option' );
  element.closest( '.yith-wapo-option' ).find( '.required-error' ).remove();
  option.removeClass( 'required-color' );
}

function updateContainerProductPrice( variation ) {

  // Do not allow updating the price if product bundle form exists.
	if ( jQuery( '.cart.yith-wcpb-bundle-form' ).length || variation.variation_id !== parseInt( jQuery('.variation_id').val() ) ) {
     return;
  }

  let container         = jQuery( '#yith-wapo-container' ),
   	new_product_price = 0;
	if ( typeof( variation.display_price ) !== 'undefined' ) {
		new_product_price = variation.display_price;
		// Check if variation price and price_html are different, use the last one
		if ( 'yes' === yith_wapo.use_price_html_on_variations && typeof( variation.price_html ) !== 'undefined' ) {
			let new_product_price_html = jQuery( variation.price_html ).find( '> .amount bdi' ).text();
			new_product_price_html     = wcPriceToFloat( new_product_price_html );
			if ( ! isNaN( new_product_price_html ) && new_product_price !== new_product_price_html ) {
				new_product_price = new_product_price_html;
			}
		}
	}
	container.attr( 'data-product-price', new_product_price );
	container.attr( 'data-product-id', variation.variation_id );

}

// WooCommerce Measurement Price Calculator (compatibility)
jQuery( 'form.cart' ).on(
	'change',
	'#price_calculator.wc-measurement-price-calculator-price-table',
	function() {
		var price = jQuery( '#price_calculator.wc-measurement-price-calculator-price-table .product_price .amount' ).text();
		price     = wcPriceToFloat( price );

		if ( ! isNaN( price ) ) {
			let container = jQuery( '#yith-wapo-container' );

			container.attr( 'data-product-price', price );
			jQuery( document ).trigger( 'yith-wapo-reload-addons', [ price ] );
		}
	}
);

/*
 *	ajax upload file
 */

// preventing page from redirecting
jQuery( 'html' ).on(
	'dragover',
	function(e) {
		e.preventDefault();
		e.stopPropagation();
	}
);
jQuery( 'html' ).on( 'drop', function(e) {
  e.preventDefault();
  e.stopPropagation();
} );

// drag enter
jQuery( document ).on(
	'dragenter',
  '.yith-wapo-ajax-uploader',
	function (e) {
		e.stopPropagation();
		e.preventDefault();
		jQuery( this ).css( 'opacity', '0.5' );
	}
);

// drag over
jQuery( document ).on(
  'dragover',
  '.yith-wapo-ajax-uploader',
	function (e) {
		e.stopPropagation();
		e.preventDefault();
	}
);

// drag leave
jQuery( document ).on(
  'dragleave',
  '.yith-wapo-ajax-uploader',
	function (e) {
		e.stopPropagation();
		e.preventDefault();
    if ( jQuery(e.target).hasClass( 'yith-wapo-ajax-uploader' ) ) {
      jQuery( this ).css( 'opacity', '1' );
    }
	}
);

// Drop uploads
jQuery( '.yith-wapo-ajax-uploader' ).on(
	'drop',
	function (e) {
		e.stopPropagation();
		e.preventDefault();

		jQuery( this ).css( 'opacity', '1' );

		var input = jQuery( this ).closest( '.yith-wapo-option' ).find( 'input.file' ),
		  uploaderElement = jQuery( this ),
		  fileList  = e.originalEvent.dataTransfer.files,
		  correctFiles = checkBeforeUploadFiles( uploaderElement, fileList ); //Boolean


		if ( correctFiles ) {
		  uploadFiles( fileList, uploaderElement );
		}
	}
);

// upload on click
jQuery( document ).on(
  'change',
  '.yith-wapo-addon-type-file input.file',
  function( e ) {
    jQuery( this ).closest( '.yith-wapo-ajax-uploader' ).css( 'opacity', '1' );

    var input = jQuery( this ),
      uploaderElement = input.closest( '.yith-wapo-option' ).find( '.yith-wapo-ajax-uploader' ),
      fileList  = input[0].files,
      correctFiles = checkBeforeUploadFiles( uploaderElement, fileList ); //Boolean

    if ( correctFiles ) {
      uploadFiles( fileList, uploaderElement );
    }

  }
);
checkBeforeUploadFiles = function( uploaderElement, fileList ) {

  let countAlreadyUploaded = jQuery( uploaderElement ).find( '.yith-wapo-uploaded-file-element' ).length,
    totalUploads = countAlreadyUploaded + fileList.length,
    maxUploadsAllowed = jQuery( uploaderElement ).closest( '.yith-wapo-option' ).data( 'max-multiple' ),
    allowMultiple = jQuery( uploaderElement ).closest( '.yith-wapo-option' ).hasClass( 'allow-multiple' );

	if ( ! allowMultiple && totalUploads > 1 ) {
		alert( yith_wapo.messages.maxFilesAllowed + '1' );
		return false;
	}

	if ( 'undefined' !== typeof maxUploadsAllowed && totalUploads > maxUploadsAllowed ) {
		alert( yith_wapo.messages.maxFilesAllowed + maxUploadsAllowed );
		return false;
	}

  for ( var item in fileList ) {
    if ( jQuery.isNumeric( item ) ) {

      let file = fileList[item],
        message = '',
        isError = false;

      if ( ! yith_wapo.upload_allowed_file_types.includes( file.name.split( '.' ).pop().toLowerCase() ) ) {
        //message = 'Error - not supported extension!';
		  message = yith_wapo.messages.noSupportedExtension;
        isError = true;
      }

      if ( parseFloat( file.size ) >= parseFloat( yith_wapo.upload_max_file_size * 1024 * 1024 ) ) {
        //message = 'Error - file size for ' + file.name + ' - max ' + yith_wapo.upload_max_file_size + ' MB allowed!';
		message =  yith_wapo_sprintf( yith_wapo.messages.maxFileSize, file.name, yith_wapo.upload_max_file_size );
        isError = true;
      }

      if ( isError ) {
        alert( message );
        return false;
      }

    }
  }

  return true;

};

function yith_wapo_sprintf(format, ...values) {
	return format.replace(/%([sd])/g, function(match, tipo) {
		if (tipo === 's') {
			return values.shift();
		} else if (tipo === 'd') {
			const valor = values.shift();
			return Number.isInteger(valor) ? valor.toString() : '';
		}
		return match;
	});
}

function uploadFiles( fileList, uploaderElement ) {

  for ( var count = 0; count < fileList.length; count++ ) {
    let uploadedFileContainer = uploaderElement.find( '.yith-wapo-uploaded-file' ),
      currentIndex = uploaderElement.find( '.yith-wapo-uploaded-file-element' ).last().data( 'index' )+1;
    if ( isNaN(currentIndex) || 'undefined' == typeof currentIndex ){
      currentIndex = 0;
    }
    appendNewUploadedFile( count, fileList, uploadedFileContainer, currentIndex );

    if( count == fileList.length-1 ) {
      uploadedFileContainer.show();

      uploadSingleFile( fileList, 0, uploaderElement );
    }
  }

}

appendNewUploadedFile = function ( count, fileList, uploadedFileContainer, currentIndex ) {
  let exactSize = calculate_exact_file_size( fileList[count] ),
    fileName = fileList[count].name,
    optionId = jQuery( uploadedFileContainer ).closest( '.yith-wapo-option' ).data( 'option-id' );

  var newElement =
    '<div class="yith-wapo-uploaded-file-element uploaded-file-'+currentIndex+'" data-index="' + currentIndex + '">' +
      '<div class="yith-wapo-uploaded-file-info">' +
        '<span class="info">' +
          '<label class="file-name"><span>' + fileName + '</span></label>' +
          '<span class="file-size">' + exactSize + '</span>' +
        '</span>' +
        '<i class="remove yith-plugin-fw__action-button__icon yith-icon yith-icon-trash" style="display:none"></i>' +
      '</div>' +
      '<div class="yith-wapo-loader-container" id="progressbar'+currentIndex+'">' +
        '<div class="yith-wapo-loader-label"></div>' +
        '<div class="yith-wapo-loader" role="progressbar"></div>' +
      '</div>' +
      '<input type="hidden" id="yith-wapo-' + optionId + '" class="option yith-wapo-option-value" name="yith_wapo[][' + optionId + '][]" >' +
    '</div>';

  uploadedFileContainer.append( newElement );
}

uploadSingleFile = function( fileList, fileCount, uploaderElement, isFinalUpload = false, currentIndex = 0 ) {

  if ( 0 === parseInt(currentIndex) && jQuery( uploaderElement ).find( '.yith-wapo-uploaded-file-element.completed' ).length ) {
    currentIndex = jQuery( uploaderElement ).find( '.yith-wapo-uploaded-file-element.completed' ).last().data( 'index' )+1;
  }

  var fileLength = fileList.length-1,
    currentFile = fileList[fileCount],
    data  = new FormData(),
    option = jQuery( uploaderElement ).closest( '.yith-wapo-option' ),
    maxMultiple = option.data( 'max-multiple' ),
    uploadedFileContainer = jQuery( uploaderElement ).find( '.yith-wapo-uploaded-file' ),
    uploaderContainer = jQuery( uploaderElement ).find( '.yith-wapo-ajax-uploader-container' ),
    uploadedFileElement = jQuery( uploaderElement ).find( '.yith-wapo-uploaded-file-element[data-index="' + currentIndex + '"]' ),
    removeIcon = jQuery( uploaderElement ).find( '.yith-wapo-uploaded-file-info .remove' ),
    progressLabel = jQuery( uploadedFileElement ).find( '.yith-wapo-loader-container .yith-wapo-loader-label' ),
    progressbar = jQuery( uploadedFileElement ).find( '.yith-wapo-loader-container .yith-wapo-loader' );

  data.append( 'action', 'yith_wapo_upload_file' );
  data.append( 'currentFile', currentFile );

  jQuery.ajax(
    {
      url			: yith_wapo.ajaxurl,
      type		: 'POST',
      contentType	: false,
      processData	: false,
      async: true,
      dataType: 'json',
      data		: data,
      xhr: function() {
        var xhr = jQuery.ajaxSettings.xhr();
        if(xhr.upload){
          xhr.upload.addEventListener('progress', function(event){
            var currentPercent = 0;
            if (event.lengthComputable) {
              currentPercent = Math.ceil(event.loaded / event.total * 100);
            }
            progressbar.progressbar({
              value: currentPercent,
            });

          }, false);
          xhr.addEventListener("progress", function(e){
            if ( fileList.length == 1 || isFinalUpload ) {
              if ( option.hasClass( 'allow-multiple' ) ) {
                let countUploadedFiles = jQuery( uploaderElement ).find( '.yith-wapo-uploaded-file-element' ).length;
                if ( 'undefined' === typeof maxMultiple || 'undefined' !== typeof maxMultiple && countUploadedFiles < maxMultiple ) {
                  uploaderContainer.fadeIn();
                }
              }

              removeIcon.fadeIn();
              let parentInputHidden = jQuery( uploaderElement ).closest( '.yith-wapo-option' ).find( 'input[type="hidden"].upload-parent' );
              parentInputHidden.val( 1 );

              //Calculate prices after all the uploads.
              calculateTotalAddonsPrice();

            }
          }, false);
          return xhr;
        }
        return xhr;
      },
      beforeSend: function () {
        uploaderContainer.hide();
        uploadedFileContainer.show();

        progressbar.progressbar({
          change: function() {
            progressLabel.text( progressbar.progressbar( 'value' ) + "%" + ' ' + yith_wapo.i18n.uploadPercentageDoneString );
          },
          complete: function ( e ) {
            jQuery( e.target ).closest( '.yith-wapo-loader-container' ).fadeOut();
          }
        });
        progressbar.show();


      },
      success: function (res, status) {
        if ( status == 'success' ) {


          let hiddenElement = uploadedFileElement.find( 'input[type="hidden"]' ),
            infoElement     = uploadedFileElement.find( '.yith-wapo-uploaded-file-info span.info' ),
            resType         = res.type;


          hiddenElement.val( res.url ); // Add the value to the hidden input for $_POST['yith_wapo']

          if( 'image/jpeg' === resType || 'image/jpg' === resType || 'image/png' === resType || 'image/gif' === resType ){
            infoElement.append( '<img src="' + res.url + '" class="yith-wapo-img-uploaded" alt="Image uploaded from YITH Product Add-ons uploader">' ).fadeIn(); // Show the image preview.
          }

          if ( fileCount < fileLength ) {
            if ( fileCount == fileLength-1 ) {
              isFinalUpload = true;
            }
            uploadSingleFile(fileList, fileCount + 1, uploaderElement, isFinalUpload, currentIndex+1 );
          }
        }
      },
      complete: function() {
        jQuery( uploadedFileElement ).addClass( 'completed' );
        console.log( 'Single file upload completed!' );
      },
      error: function (res) {
        console.log('File upload failed!');
      },

    });

}


// click
jQuery( document ).on(
	'click',
	'.yith-wapo-ajax-uploader .button, .yith-wapo-ajax-uploader .link',
	function() {
		jQuery( this ).closest( '.yith-wapo-option').find( 'input.file' ).click();
	}
);

function calculate_exact_file_size( file ) {
	let exactSize  = 0;
	let file_size  = file.size;
	let file_types = ['Bytes', 'KB', 'MB', 'GB'],
	i              = 0;
	while ( file_size > 900 ) {
		file_size /= 1024;
		i++;
	}
	exactSize = ( Math.round( file_size * 100 ) / 100 ) + ' ' + file_types[i];

	return exactSize;
}

jQuery( 'form.cart' ).on(
	'click',
	'span.checkboxbutton',
	function() {
		if ( jQuery( this ).find( 'input' ).is( ':checked' ) ) {
			jQuery( this ).addClass( 'checked' );
		} else {
			jQuery( this ).removeClass( 'checked' );
		}
	}
);

jQuery( 'form.cart' ).on(
	'click',
	'span.radiobutton',
	function() {
		if ( jQuery( this ).find( 'input' ).is( ':checked' ) ) {
			jQuery( this ).closest('.yith-wapo-addon.yith-wapo-addon-type-radio').find( 'span.radiobutton.checked' ).removeClass( 'checked' );
			jQuery( this ).addClass( 'checked' );
		}
	}
);


// min max rules

jQuery( document ).on(
	'change',
	'.yith-wapo-addon-type-checkbox, .yith-wapo-addon-type-color, .yith-wapo-addon-type-label, .yith-wapo-addon-type-product',
	function() {
		yith_wapo_check_min_max( jQuery( this ) );
	}
);

// Check required fields before adding to cart( Required select and min/max values ).
jQuery( document ).on(
	'click',
	'form.cart button',
	function() {

		let numbersCheck = checkNumbersTotalValues(),
    	minMaxResult = yith_wapo_check_required_min_max();

		if ( ! numbersCheck ) { // if it's not true, do not allow to add to cart.
			return false;
		}

		if ( minMaxResult ) {
			jQuery( 'form.cart .yith-wapo-addon.conditional_logic.hidden' ).remove();
		} else {
			if ( ! yith_wapo.disable_scroll_on_required_mix_max ) {
				  jQuery( 'html, body' ).animate( { scrollTop: jQuery( '#yith-wapo-container' ).offset().top - 20 }, 500 );
			}
		}

		return minMaxResult;
	}
);

jQuery( document ).on(
	'click',
	'.add-request-quote-button',
	function(e) {
		e.preventDefault();

		if ( typeof yith_wapo_general === 'undefined' ){
			yith_wapo_general = { do_submit: true };
		}
		if ( ! yith_wapo_check_required_min_max() ) {
			yith_wapo_general.do_submit = false;
		} else {
			yith_wapo_general.do_submit = true;
		}
	}
);

function yith_wapo_check_required_min_max() {

  // Check force user selection for add-on type Select.
	if ( ! checkRequiredSelect() ) {
		return false;
	}
	if ( ! checkTextInputLimit() ){
		return false;
	}

  // Required feature.
	if ( ! yith_wapo_check_required_fields( 'highlight' ) ) {
		return false;
	}

	var requiredOptions = 0;
	var checkMinMax     = '';
	jQuery( 'form.cart .yith-wapo-addon:not(.hidden)' ).each(
		function() {
			checkMinMax = yith_wapo_check_min_max( jQuery( this ), true );
			if ( checkMinMax > 0 ) {
				  requiredOptions += checkMinMax;
			}
		}
	);
  if ( requiredOptions > 0 ) {
		return false;
	}
	return true;
}

/**
 * Check min/max for each option
 * @param addon
 * @param submit
 * @returns {number}
 */
function yith_wapo_check_min_max( addon, submit = false ) {

	var addonType       = addon.data( 'addon-type' );
	var minValue        = addon.data( 'min' );
	var maxValue        = addon.data( 'max' );
	var exaValue        = addon.data( 'exa' );
	var errorMessage    = addon.find( '.min-error-message' ),
	addonTitle          = addon.find( '.wapo-addon-title' ),
	numberOfChecked = 0;

	let toggle_addon_title = addon.find( 'h3.wapo-addon-title.toggle-closed' );
	addonTitle.removeClass( 'wapo-error' );

	if ( 'select' === addonType || ( '' === minValue && '' === exaValue && '' === maxValue ) ) {
		return;
	}

  // Number / Text / TextArea
	if ( 'number' === addonType || 'text' === addonType || 'textarea' === addonType ) {
		jQuery( addon ).find( '.yith-wapo-option-value' ).each(
			function( index ) {
				let numberValue = jQuery( this ).val();
				if ( numberValue.length ) {
					numberOfChecked++; // Summing number of filled.
				}
			}
		);

    if ( maxValue && numberOfChecked > maxValue ) {
      let optionsElement = jQuery( addon ).find( '.options-container' );
      if ( ! optionsElement.find( '.max-selected-error' ).length ) {
        optionsElement.append( '<p class="max-selected-error">' + yith_wapo.i18n.maxOptionsSelectedMessage + '</p>' );
		addonTitle.addClass( 'wapo-error' );
	  }
      return 1;
    }

  	} else {
    	// Checkbox / Radio
		numberOfChecked = addon.find( 'input:checkbox:checked, input:radio:checked' ).length; // Sum of number of checked.
	}

	// Exactly Values
	if ( exaValue > 0 ) {

		let optionsToSelect = 0;

		if ( exaValue == numberOfChecked ) {
			addon.removeClass( 'required-min' ).find( '.min-error' ).hide();
			addon.find( 'input:checkbox' ).not( ':checked' );
		} else {
			// If click on add to cart button.
			if ( submit ) {
				optionsToSelect = exaValue - numberOfChecked;
				addon.addClass( 'required-min' );
				addon.find( '.min-error' ).show();
				addonTitle.addClass( 'wapo-error' );

				let errorMessageText = yith_wapo.i18n.selectOptions.replace( '%d', exaValue )
				if ( 1 === exaValue ) {
					errorMessageText = yith_wapo.i18n.selectAnOption;
				}

				errorMessage.text( errorMessageText );

				if ( toggle_addon_title ) {
					toggle_addon_title.click();
				}
			}
			addon.find( '.yith-wapo-option:not(.out-of-stock) input:checkbox' ).not( ':checked' ).attr( 'disabled', false );
		}

		return optionsToSelect;

	} else {
		
		// At least values.
		if ( minValue > 0 ) {
			let optionsToSelect = minValue - numberOfChecked;
			if ( minValue <= numberOfChecked ) {
				addon.removeClass( 'required-min' ).find( '.min-error' ).hide();
			} else {
				// If click on add to cart button.
				if ( submit ) {
					let minMessage = yith_wapo.i18n.selectAnOption;
					if ( minValue > 1 ) {
						minMessage = yith_wapo.i18n.selectAtLeast.replace( '%d', minValue )
					}

					addon.addClass( 'required-min' );
					addon.find( '.min-error' ).show();
					addonTitle.addClass( 'wapo-error' );
					errorMessage.text( minMessage );

					if ( toggle_addon_title ) {
						toggle_addon_title.click();
					}
				}
				return optionsToSelect;
			}
		}

		// Max values.
		if ( ! maxValue || maxValue >= numberOfChecked ) {
			addon.removeClass( 'required-min' ).find( '.max-selected-error' ).hide();
		} else {
			// If click on add to cart button.
			if ( submit ) {
				addon.addClass( 'required-min' );
				let optionsElement = jQuery( addon ).find( '.options-container' );
				if ( ! optionsElement.find( '.max-selected-error' ).length ) {
					optionsElement.append( '<small class="max-selected-error">' + yith_wapo.i18n.maxOptionsSelectedMessage + '</small>' );
					addonTitle.addClass( 'wapo-error' );
				}
			}
			return 1;
		}

	}
}

/** Check force user select an option for add-on type Selector */
function checkRequiredSelect() {

	let value = true;

	jQuery( '.yith-wapo-addon.yith-wapo-addon-type-select select' ).each(
		function () {
			let currentSelect = jQuery( this );
			if ( currentSelect.is( ':required' ) ) {
				let addon       = currentSelect.parents( '.yith-wapo-addon' );
				let errorMessage    = addon.find( '.min-error-message' ),
				addonTitle          = addon.find( '.wapo-addon-title' );
				let selectValue = currentSelect.val();
				errorMessage.text( '' );
				addonTitle.removeClass( 'wapo-error' );
				addon.removeClass( 'required-min' );

				if ( 'default' === selectValue && ! addon.hasClass( 'hidden' ) ) {
					value = false;
					if ( ! value ) {
						 let error_el           = addon.find( '.min-error' );
						 let toggle_addon       = currentSelect.parents( 'div.yith-wapo-addon.wapo-toggle' );
						 let toggle_addon_title = toggle_addon.find( '.wapo-addon-title.toggle-closed' );
						 addon.addClass( 'required-min' );

						if ( toggle_addon_title ) {
							toggle_addon_title.click();
						}
						addonTitle.addClass( 'wapo-error' );
						errorMessage.text( yith_wapo.i18n.selectAnOption.replace( '%d', 1 ) );
						error_el.show();
					}
				}
			}
		}
	);

	return value;
}

function checkTextInputLimit(){
	let valid = true;
	jQuery( 'form.cart .yith-wapo-addon.yith-wapo-addon-type-text:not(.hidden) input' ).each( ( index, input ) => {
		let currentInput = jQuery( input ),
		currentValue = currentInput.val(),
		minLength = currentInput.attr( 'minlength' ),
		maxLength = currentInput.attr( 'maxlength' );
		if ( ( minLength !== '' && currentValue.length < minLength ) || ( maxLength !== '' && currentValue.length > maxLength ) ){
			currentInput.addClass( 'length-error' );
			currentInput.siblings('.length-error-message').show();
			valid = false;
		} else {
			currentInput.siblings('.length-error-message').hide();
			currentInput.removeClass( 'length-error' );
		}
	} );
	
	return valid;
}

// multiplied by value price

jQuery( document ).on(
	'change keyup',
	'.yith-wapo-addon-type-number input',
	function() {
		var inputElementValue = jQuery( this ).val(),
		optionWrapper = jQuery( this ).closest( '.yith-wapo-option' ),
		resetImage = false;

		if ( '' == inputElementValue ) {
			resetImage = true;
		}

		yith_wapo_replace_image( optionWrapper, resetImage );
		yith_wapo_check_multiplied_price( jQuery( this ) );
	}
);

function yith_wapo_check_multiplied_price( addon ) {
	let price        = addon.data( 'price' );
	let sale_price   = addon.data( 'price-sale' );
	let defaultPrice = addon.data( 'default-price' );
	let priceType    = addon.data( 'price-type' );
	let priceMethod  = addon.data( 'price-method' );
	let default_attr = 'price';
	let final_price  = 0;
	let addon_value  = addon.val();

	if ( ! defaultPrice > 0 ) {
		if ( sale_price > 0 && ( 'number' !== addon.attr( 'type' ) && 'multiplied' === priceType ) ) {
			price        = sale_price;
			default_attr = 'price-sale';
		}
		defaultPrice = price;
		addon.data( 'default-price', defaultPrice );
	}
	if ( priceMethod == 'value_x_product' ) {
		var productPrice = parseFloat( jQuery( '#yith-wapo-container' ).attr( 'data-product-price' ) );
		final_price      = addon_value * productPrice;
	} else if ( priceType == 'multiplied' ) {
		final_price = addon_value * defaultPrice;
	}

	if ( final_price > 0 || priceMethod == 'decrease' ) {
		addon.data( default_attr, final_price );
	}
}

// Multiply add-on price by length

function yith_wapo_check_multiplied_length( addon ) {

	let price        = addon.data( 'price' );
	let defaultPrice = addon.data( 'default-price' );
	let priceType    = addon.data( 'price-type' );

	if ( ! defaultPrice > 0 ) {
		defaultPrice = price;
		addon.data( 'default-price', defaultPrice );
	}
	if ( 'characters' === priceType ) {
		let remove_spaces = addon.data( 'remove-spaces' );
		let addonLength   = addon.val().length;
		if ( remove_spaces ) {
			addonLength = addon.val().replace( /\s+/g, '' ).length;
		}
		addon.data( 'price', addonLength * defaultPrice );
	}
}


// product qty

jQuery( '.wapo-product-qty' ).keyup(
	function() {
		var productID  = jQuery( this ).data( 'product-id' );
		var productQTY = jQuery( this ).val();
		var productURL = '?add-to-cart=' + productID + '&quantity=' + productQTY;
		jQuery( this ).parent().find( 'a' ).attr( 'href', productURL );
	}
);

// calendar time save

jQuery( document ).on(
	'click',
	'#wapo-datepicker-save button',
	function( ev ) {
		ev.preventDefault();

		var elementToClick = jQuery( '#ui-datepicker-div .ui-state-active' );

		if ( 0 == elementToClick.length ) {
			elementToClick = jQuery( '#ui-datepicker-div .ui-datepicker-today' );
		}

		elementToClick.click();
		jQuery( '.hasDatepicker' ).datepicker( 'hide' );
	}
);
jQuery( document ).on(
	'change',
	'#wapo-datepicker-time-select',
	function( ev ) {
		ev.preventDefault();
		var option_id = jQuery( '#ui-datepicker-div' ).attr( 'wapo-option-id' );
		var tempTime = jQuery( '#' + option_id ).closest( '.date-container' ).find( '.temp-time' );

		tempTime.text( jQuery( this ).val() );
	}
);

// Composite compatibility.
jQuery( document ).on( 'yith_wcp_price_updated', function ( event, total ){
	let global_qty = parseFloat( jQuery('form.cart.ywcp > div.quantity input.qty').val() );
	let price      = global_qty ? total / global_qty : total;
	let addonsContainer = jQuery( '#yith-wapo-container' );

	addonsContainer.attr( 'data-product-price', price );
	calculateTotalAddonsPrice();
} );