/* global bk, yith_booking_form_params, yith_wcbk_dates, yith_booking */

jQuery( function ( $ ) {
	"use strict";

	var absInt             = function ( value ) {
			return Math.abs( parseInt( value, 10 ) ) || 0;
		},
		sprintf            = function () {
			var string = arguments[ 0 ],
				args   = Array.prototype.slice.call( arguments ).slice( 1 );

			for ( var idx in args ) {
				var arg = args[ idx ];
				string  = string.replace( '%s', arg );
			}

			return string;
		},
		firstValidField    = function () {
			var fields = Array.prototype.slice.call( arguments );

			fields = fields.filter( function ( field ) {
				return field.length;
			} );

			return fields.shift();
		},
		blockParams        = bk.blockParams,
		blockParamsEmpty   = bk.blockParamsEmpty,
		_n                 = function ( singular, plural, number ) {
			number = !isNaN( number ) ? number : 0;
			return number < 2 ? singular : plural;
		},
		formErrorHandling  = {
			onFormUpdate : 'on-form-update' === yith_booking_form_params.form_error_handling,
			onButtonClick: 'on-button-click' === yith_booking_form_params.form_error_handling
		},
		formatDuration     = function ( duration, unit ) {
			var formattedDuration = '';

			if ( unit in bk.i18n_durations ) {
				formattedDuration = sprintf( _n( bk.i18n_durations[ unit ].singular, bk.i18n_durations[ unit ].plural, duration ), duration );
			}

			return formattedDuration;
		},
		_arrayHasObjectKey = function ( array, keys ) {
			if ( typeof keys === 'string' ) {
				keys = [keys];
			}

			var _found = array.find( function ( obj ) {
				return keys.indexOf( obj.key ) > -1;
			} );

			return typeof _found !== 'undefined';
		};

	$.fn.yith_booking_form = function () {

		var self = {
			form    : $( this ).closest( '.yith-wcbk-booking-form' ),
			dom     : {},
			ajaxCall: null
		};

		/** ---------------------------------------------
		 * Data
		 * ----------------------------------------------
		 */
		self.productID = self.form.data( 'product-id' );
		self.bookingData = self.form.data( 'booking_data' );


		/** ---------------------------------------------
		 * Dom elements
		 * ----------------------------------------------
		 */
		self.dom = {
			duration         : self.form.find( '.yith-wcbk-booking-duration' ),
			realDuration     : self.form.find( '.yith-wcbk-booking-real-duration' ),
			persons          : self.form.find( '.yith-wcbk-booking-persons' ),
			personTypes      : self.form.find( '.yith-wcbk-booking-person-types' ),
			optionalServices : self.form.find( 'input[type=checkbox].yith-wcbk-booking-service' ),
			serviceQuantities: self.form.find( 'input.yith-wcbk-booking-service-quantity' ),
			startDate        : self.form.find( '.yith-wcbk-booking-start-date' ),
			from             : self.form.find( '.yith-wcbk-booking-hidden-from' ),
			endDate          : self.form.find( '.yith-wcbk-booking-end-date' ),
			message          : self.form.find( '.yith-wcbk-booking-form-message' ),
			totals           : self.form.find( '.yith-wcbk-booking-form-totals' ),
			totalsSection    : self.form.find( '.yith-wcbk-form-section-totals' ),
			additionalData   : self.form.find( '.yith-wcbk-booking-form-additional-data' ),
			time             : self.form.find( '.yith-wcbk-booking-start-date-time' ),
			addToCart        : self.form.closest( 'form' ).find( 'button[type=submit]' ),
			addToQuote       : $( '.add-request-quote-button' ).first(),
			prices           : self.form.closest( yith_booking_form_params.dom.product_container ).find( yith_booking_form_params.dom.price )
		};

		self.dom.price    = 'yes' !== yith_booking_form_params.price_first_only ? self.dom.prices : self.dom.prices.first();
		self.dom.timeWrap = self.dom.time.closest( '.yith-wcbk-form-section' );

		/* ---------------------------------------------
		 * Getters
		 * ----------------------------------------------
		 */
		self.getDuration = function () {
			return self.bookingData.duration;
		};

		self.getDurationUnit = function () {
			return self.bookingData.duration_unit;
		};

		self.getMinimumDuration = function () {
			return self.bookingData.minimum_duration;
		};

		self.getMaximumDuration = function () {
			return self.bookingData.maximum_duration;
		};

		self.getMinimumPeople = function () {
			return self.bookingData.minimum_number_of_people;
		};

		self.getMaximumPeople = function () {
			return self.bookingData.maximum_number_of_people;
		};

		self.getFormattedDuration = function ( duration ) {
			return formatDuration( duration, self.getDurationUnit() );
		};

		/* ---------------------------------------------
		 * Conditionals
		 * ----------------------------------------------
		 */
		self.hasDuration = function () {
			return !!self.dom.duration.length;
		};

		self.isDurationUnit = function ( unit ) {
			return typeof unit === 'string' ? unit === self.getDurationUnit() : unit.includes( self.getDurationUnit() );
		};

		self.isFullDay = function () {
			return 'yes' === self.bookingData.full_day;
		};

		self.hasTime = function () {
			return self.isDurationUnit( ['hour', 'minute'] );
		};

		self.hasPeople = function () {
			return !!self.dom.persons.length || self.hasPersonTypes();
		};

		self.hasPersonTypes = function () {
			return !!self.dom.personTypes.length;
		};

		self.hasOptionalServices = function () {
			return !!self.dom.optionalServices.length;
		};

		/* ---------------------------------------------
		 * Setters
		 * ----------------------------------------------
		 */
		self.updateTotalsHtml = function ( html ) {
			!!html ? self.dom.totalsSection.show() : self.dom.totalsSection.hide();
			self.dom.totals.html( html );
		};

		self.updateMessageHtml = function ( message ) {
			!!message ? self.dom.message.show() : self.dom.message.hide();
			self.dom.message.html( message );
		};

		/* ---------------------------------------------
		 * Functions
		 * ----------------------------------------------
		 */

		var onLoad                         = function () {
				// If loading time-slots, the form will be updated on AJAX complete.
				var triggerFormUpdate = !maybeLoadTimeSlots( { updateFormOnCompleteOnlyIfValid: true } );

				handleTimeVisibility();

				if ( triggerFormUpdate ) {
					var data = validateForm();
					if ( data.validation ) {
						self.form.trigger( 'yith_wcbk_booking_form_update' );
					}
				}
			},
			hasFieldErrorsShown            = function () {
				return self.form.find( '.yith-wcbk-booking-form-error' ).length;
			},
			removeFieldErrors              = function () {
				self.form.find( '.yith-wcbk-booking-form-error' ).remove();
				self.form.find( '.yith-wcbk-form-section__content--with-error' ).removeClass( 'yith-wcbk-form-section__content--with-error' );
			},
			showFieldErrors                = function ( errors ) {
				removeFieldErrors();
				for ( var idx in errors ) {
					var error = errors[ idx ];
					if ( error.message && error.field ) {
						var theError = $( '<div class="yith-wcbk-booking-form-error">' + error.message + '</div>' );
						error.field.closest( '.yith-wcbk-form-section__content' ).addClass( 'yith-wcbk-form-section__content--with-error' ).append( theError );
					}
				}
			},
			validateForm                   = function () {
				var duration    = absInt( self.dom.duration.val() ),
					persons     = absInt( self.dom.persons.val() ),
					date        = self.dom.startDate.val(),
					from        = date,
					endDate     = self.dom.endDate.val(),
					peopleCount = persons,
					time        = self.dom.time.val(),
					validation  = true,
					formData    = {},
					errors      = [];

				if ( !!self.dom.from.length ) {
					if ( time ) {
						self.dom.from.val( date + ' ' + time );
					} else {
						self.dom.from.val( date );
					}
					from = self.dom.from.val();
				}

				if ( self.hasPersonTypes() ) {
					peopleCount = 0;
					self.dom.personTypes.each( function () {
						peopleCount += ( $( this ).val() < 1 ) ? 0 : parseInt( $( this ).val(), 10 );
					} );
				}

				if ( self.hasDuration() && !duration ) {
					errors.push(
						{
							key    : 'empty-duration',
							field  : self.dom.duration,
							message: yith_booking_form_params.i18n_empty_duration
						}
					);
					validation = false;
				}

				if ( from && endDate && self.isDurationUnit( 'day' ) ) {
					duration = yith_wcbk_dates.date_diff( endDate, from, 'days' );

					if ( self.isFullDay() ) {
						duration += 1;
					}
				}

				if ( !date || ( self.dom.endDate.length > 0 && !endDate ) ) {
					if ( !self.hasTime() ) {
						errors.push(
							{
								key    : 'empty-date',
								field  : firstValidField( self.dom.endDate, self.dom.startDate ),
								message: yith_booking_form_params.i18n_empty_date
							}
						);
					} else {
						errors.push(
							{
								key    : 'empty-date-for-time',
								field  : self.dom.startDate,
								message: yith_booking_form_params.i18n_empty_date_for_time
							}
						);
					}
					validation = false;
				}

				if ( self.hasTime() && date && !time ) {
					errors.push(
						{
							key    : 'empty-time',
							field  : self.dom.time,
							message: yith_booking_form_params.i18n_empty_time
						}
					);
					validation = false;
				}

				if ( self.hasPeople() ) {
					if ( peopleCount < self.getMinimumPeople() ) {
						errors.push(
							{
								key    : 'minimum-people',
								field  : self.hasPersonTypes() ? self.dom.personTypes.last() : self.dom.persons,
								message: sprintf( yith_booking_form_params.i18n_min_persons, self.getMinimumPeople() )
							}
						);

						validation = false;
					}

					if ( self.getMaximumPeople() > 0 && peopleCount > self.getMaximumPeople() ) {
						errors.push(
							{
								key    : 'maximum-people',
								field  : self.hasPersonTypes() ? self.dom.personTypes.last() : self.dom.persons,
								message: sprintf( yith_booking_form_params.i18n_max_persons, self.getMaximumPeople() )
							}
						);

						validation = false;
					}
				}

				if ( self.getMinimumDuration() > 0 ) {
					if ( duration < self.getMinimumDuration() ) {

						if ( !_arrayHasObjectKey( errors, ['empty-duration', 'empty-date'] ) ) {
							errors.push(
								{
									key    : 'minimum-duration',
									field  : firstValidField( self.dom.duration, self.dom.endDate, self.dom.startDate ),
									message: sprintf( yith_booking_form_params.i18n_min_duration, self.getFormattedDuration( self.getMinimumDuration() ) )
								}
							);
						}

						validation = false;
					}
				}

				if ( self.getMaximumDuration() ) {
					if ( duration > self.getMaximumDuration() ) {
						if ( !_arrayHasObjectKey( errors, ['empty-duration', 'empty-date'] ) ) {
							errors.push(
								{
									key    : 'maximum-duration',
									field  : firstValidField( self.dom.duration, self.dom.endDate, self.dom.startDate ),
									message: sprintf( yith_booking_form_params.i18n_max_duration, self.getFormattedDuration( self.getMaximumDuration() ) )
								}
							);
						}

						validation = false;
					}
				}

				var personTypes      = [],
					optionalServices = [];
				if ( self.hasPersonTypes() ) {
					self.dom.personTypes.each( function () {
						personTypes.push( {
											  id    : $( this ).data( 'person-type-id' ),
											  number: $( this ).val()
										  } );
					} );
				}

				if ( self.hasOptionalServices() ) {
					self.dom.optionalServices.each( function () {
						if ( $( this ).is( ':checked' ) ) {
							optionalServices.push( $( this ).data( 'service-id' ) );
						}
					} );
				}

				formData = {
					product_id      : self.productID,
					duration        : duration,
					from            : from,
					from_date       : date,
					time            : time,
					to              : endDate,
					persons         : persons,
					person_types    : personTypes,
					booking_services: optionalServices
				};

				if ( self.dom.serviceQuantities.length ) {
					self.dom.serviceQuantities.each( function () {
						var _name  = $( this ).attr( 'name' ),
							_value = $( this ).val();

						if ( _name.length ) {
							formData[ _name ] = _value;
						}
					} );
				}

				if ( self.dom.additionalData.length ) {
					self.dom.additionalData.each( function () {
						var _theField  = $( this ),
							_name      = _theField.attr( 'name' ),
							_value     = _theField.val(),
							_shouldAdd = _theField.is( 'input[type=checkbox]' ) ? _theField.is( ':checked' ) : true,
							_isArray   = _name.substring( _name.length - 2, _name.length ) === '[]';

						_shouldAdd = _shouldAdd && _name.length;

						if ( _shouldAdd ) {
							if ( _isArray ) {
								if ( _name in formData ) {
									formData[ _name ].push( _value );
								} else {
									formData[ _name ] = Array.isArray( _value ) ? _value : [_value];
								}
							} else {
								formData[ _name ] = _value;
							}
						}
					} );
				}

				return {
					errors    : errors,
					validation: validation,
					formData  : formData
				};
			},
			addToCartSetEnabled            = function ( enabled ) {
				if ( enabled ) {
					self.dom.addToCart.removeClass( 'yith-wcbk-not-allowed' );
					self.dom.addToQuote.removeClass( 'yith-wcbk-not-allowed' );
				} else {
					self.dom.addToCart.addClass( 'yith-wcbk-not-allowed' );
					self.dom.addToQuote.addClass( 'yith-wcbk-not-allowed' );
				}

				if ( formErrorHandling.onFormUpdate ) {
					if ( enabled ) {
						self.dom.addToCart.attr( 'disabled', false );
						self.dom.addToQuote.removeClass( 'disabled' );
					} else {
						self.dom.addToCart.attr( 'disabled', true );
						self.dom.addToQuote.addClass( 'disabled' );
					}
				} else {
					self.dom.addToCart.attr( 'disabled', false );
					self.dom.addToQuote.removeClass( 'disabled' );
				}

				self.form.trigger( 'yith_wcbk_booking_form_add_to_cart_enabled_status_updated', [enabled] );
			},
			formElementsBlock              = function ( block ) {
				if ( block ) {
					self.dom.totals.html() && self.dom.totals.block( blockParams );
					self.dom.message.html() && self.dom.message.block( blockParams );
					self.dom.price.block( blockParams );
					self.dom.addToCart.block( blockParamsEmpty );
					self.dom.addToQuote.block( blockParamsEmpty );
				} else {
					self.dom.message.unblock();
					self.dom.price.unblock();
					self.dom.addToCart.unblock();
					self.dom.addToQuote.unblock();
				}
			},
			handleAddToCartClick           = function ( e ) {
				if ( $( this ).is( '.yith-wcbk-not-allowed' ) ) {
					e.preventDefault();
				}

				if ( formErrorHandling.onButtonClick ) {
					removeFieldErrors();
					var data = validateForm();

					if ( !data.validation ) {
						showFieldErrors( data.errors );
					}
				}
			},
			handleRealDurationChange       = function () {
				var newDuration = Math.floor( self.dom.realDuration.val() / self.getDuration() );
				self.dom.duration.val( newDuration ).trigger( 'change' );
			},
			maybeLoadTimeSlots             = function ( options ) {
				var defaults = {
					updateFormOnCompleteOnlyIfValid: false
				};

				options = typeof options !== 'undefined' ? options : {};
				options = $.extend( {}, defaults, options );

				var loaded   = false,
					data     = validateForm(),
					duration = data.formData.duration || 0,
					date     = data.formData.from_date || false,
					time     = data.formData.time || false,
					postData = data.formData;

				if ( self.hasTime() && date && duration ) {

					postData.request = 'get_booking_available_times';

					if ( self.ajaxCall ) {
						self.ajaxCall.abort();
					}

					loaded = true;

					self.ajaxCall = yith_booking.ajax( postData, { block: self.dom.time.parent() } );

					self.ajaxCall
						.done(
							function ( response ) {
								var data = response.data;
								try {
									if ( data.error ) {
										self.dom.message.html( '<p class="error">' + data.error + '</p>' );
									} else {
										if ( data.time_data_html ) {
											self.dom.time.html( data.time_data_html );
										}
										if ( time ) {
											var $option_selected = self.dom.time.find( 'option[value="' + time + '"]' );
											if ( $option_selected ) {
												$option_selected.attr( 'selected', 'selected' );
											}
										}
										if ( data.message ) {
											self.dom.message.html( '<p>' + data.message + '</p>' );
										}

										self.dom.time.trigger( 'yith-wcbk-select-list:update' );
										self.form.trigger( 'yith_wcbk_form_update_time', data );
									}
								} catch ( err ) {
									console.log( err );
								}

							}
						)
						.always(
							function () {
								var data = validateForm();
								if ( !options.updateFormOnCompleteOnlyIfValid || data.validation ) {
									self.form.trigger( 'yith_wcbk_booking_form_update' );
								}
							}
						);
				}

				return loaded;
			},
			shouldErrorsBeShowOnUpdateForm = function ( args ) {
				if ( formErrorHandling.onFormUpdate ) {
					var target = typeof args.target !== 'undefined' ? args.target : false;
					if ( target && target.is( '.yith-wcbk-booking-start-date' ) && self.dom.endDate.length && !self.dom.endDate.val() ) {
						return false;
					}
					return true;
				}
				// When the error-handling is "on-button-click" we don't need to show errors on form update, since they'll be shown when clicking on the "book" button.
				return false;
			},
			onUpdateForm                   = function ( event, args ) {
				var hadErrors = hasFieldErrorsShown(),
					data      = validateForm();

				args = typeof args !== 'undefined' ? args : {};

				removeFieldErrors();

				if ( !data.validation ) {
					self.updateMessageHtml( '' );
					self.updateTotalsHtml( '' );
					if ( hadErrors || shouldErrorsBeShowOnUpdateForm( args ) ) {
						showFieldErrors( data.errors );
					}
					addToCartSetEnabled( false );
				} else {
					formElementsBlock( true );

					data.formData.request = 'get_booking_data';

					if ( self.ajaxCall ) {
						self.ajaxCall.abort();
					}

					self.ajaxCall = yith_booking.ajax(
						data.formData
					);

					self.ajaxCall
						.done(
							function ( response ) {
								var data = response.data;
								try {
									if ( data.error ) {
										self.updateMessageHtml( '<p class="error">' + response.error + '</p>' );
									} else {
										if ( data.message ) {
											self.updateMessageHtml( data.message );
										} else {
											self.updateMessageHtml( '' );
										}

										if ( data.totals_html ) {
											self.updateTotalsHtml( data.totals_html );
										} else {
											self.updateTotalsHtml( '' );
										}

										if ( data.price && data.price.length > 0 ) {
											self.dom.price.html( data.price );
										}

										addToCartSetEnabled( !!data.is_available );

										self.form.trigger( 'yith_wcbk_form_update_response', data );
									}
								} catch ( err ) {
									console.log( err.message );
								}

							}
						)
						.always(
							function ( jqXHR, textStatus ) {
								if ( textStatus !== 'abort' ) {
									formElementsBlock( false );
								}
							}
						);
				}
			},
			handleTimeVisibility           = function () {
				if ( self.hasTime() ) {
					if ( self.dom.startDate.val() ) {
						self.dom.timeWrap.show();
					} else {
						self.dom.timeWrap.hide();
					}
				}
			},
			updateNotAvailableDates        = function ( e, mainDatePicker, datePickerData ) {
				var datePicker = $( this ),
					data       = validateForm(),
					formData   = data.formData;

				$( '#ui-datepicker-div' ).block( blockParams );

				formData.month   = datePickerData.month_to_load;
				formData.year    = datePickerData.year_to_load;
				formData.request = 'get_product_non_available_dates';

				yith_booking.ajax( formData )
					.done(
						function ( response ) {
							var data = response.data;
							try {
								if ( data.error ) {
									console.log( data.error );
								} else {
									mainDatePicker.data( 'month-to-load', data.month_to_load );
									mainDatePicker.data( 'year-to-load', data.year_to_load );
									mainDatePicker.data( 'not-available-dates', datePickerData.notAvailableDates.concat( data.not_available_dates ) );
									mainDatePicker.data( 'loaded-months', datePickerData.loadedMonths.concat( data.loaded_months ) );

									$( '#ui-datepicker-div' ).unblock();
									datePicker.datepicker( 'refresh' );
								}

							} catch ( err ) {
								console.log( err.message );
							}
						}
					);
			},
			loadNonAvailableDatesInMonth   = function ( e, mainDatePicker, datePickerData, year, month ) {
				var datePicker = $( this ),
					formData   = validateForm().formData;

				formData.year           = year;
				formData.month          = month;
				formData.months_to_load = 1;
				formData.request        = 'get_product_non_available_dates';

				$( '#ui-datepicker-div' ).block( blockParams );

				yith_booking.ajax( formData )
					.done(
						function ( response ) {
							var data = response.data;
							try {
								if ( data.error ) {
									console.log( data.error );
								} else {
									mainDatePicker.data( 'not-available-dates', datePickerData.notAvailableDates.concat( data.not_available_dates ) );
									mainDatePicker.data( 'loaded-months', datePickerData.loadedMonths.concat( data.loaded_months ) );

									$( '#ui-datepicker-div' ).unblock();
									datePicker.datepicker( 'refresh' );
								}

							} catch ( err ) {
								console.log( err.message );
							}
						}
					);
			},
			loadNonAvailableDates          = function ( datePicker, onLoad ) {
				var data           = validateForm(),
					formData       = data.formData,
					datePickerData = {
						notAvailableDates: datePicker.data( 'not-available-dates' ) || []
					};

				formData.request = 'get_product_non_available_dates';

				yith_booking.ajax( formData )
					.done(
						function ( response ) {
							var data = response.data;
							try {
								if ( data.error ) {
									console.log( data.error );
								} else {
									datePicker.data( 'month-to-load', data.month_to_load );
									datePicker.data( 'year-to-load', data.year_to_load );
									datePicker.data( 'not-available-dates', datePickerData.notAvailableDates.concat( data.not_available_dates ) );
									datePicker.data( 'loaded-months', datePickerData.loadedMonths.concat( data.loaded_months ) );

									datePicker.datepicker( 'refresh' );
								}

							} catch ( err ) {
								console.log( err.message );
							}

							onLoad();
						}
					);
			};

		self.updateTotalsHtml( '' );

		addToCartSetEnabled( false );
		self.dom.addToCart.on( 'click', handleAddToCartClick );
		self.dom.addToQuote.on( 'click', handleAddToCartClick );
		self.dom.realDuration.on( 'change', handleRealDurationChange );

		self.form.on( 'yith_wcbk_booking_form_update', onUpdateForm );
		self.form.on( 'yith_wcbk_booking_form_loaded', onLoad );
		self.form.on( 'yith_wcbk_booking_form_update_time_slots', maybeLoadTimeSlots );
		self.form.on( 'yith_wcbk_datepicker_update_non_available_dates', '.yith-wcbk-date-picker', updateNotAvailableDates );
		self.form.on( 'yith_wcbk_datepicker_load_non_available_dates_in_month', '.yith-wcbk-date-picker', loadNonAvailableDatesInMonth );

		self.form.on( 'change', 'input, select, .yith-wcbk-date-picker--inline', function ( event ) {
			var $target           = $( event.target ),
				triggerFormUpdate = true;

			if ( $target.is( '.yith-wcbk-booking-real-duration' ) ) {
				// The form will be updated on changing the 'duration' field. Return to avoid triggering the update twice.
				return;
			}

			handleTimeVisibility();

			if ( $target.is( '.yith-wcbk-booking-start-date' ) || $target.is( '.yith-wcbk-booking-duration' ) ) {
				// If loading time-slots, the form will be updated on AJAX complete.
				triggerFormUpdate = !maybeLoadTimeSlots();
			}

			if ( triggerFormUpdate ) {
				self.form.trigger( 'yith_wcbk_booking_form_update', [{ target: $target, event: event }] );
			}
		} );

		if ( !yith_booking_form_params.is_admin && 'yes' === yith_booking_form_params.ajax_update_non_available_dates_on_load && self.dom.startDate.is( '.yith-wcbk-date-picker' ) ) {
			self.dom.startDate.parent().block( blockParams );
			loadNonAvailableDates( self.dom.startDate, function () {
				self.dom.startDate.parent().unblock();
				self.form.trigger( 'yith_wcbk_booking_form_loaded' );
			} );
		} else {
			self.form.trigger( 'yith_wcbk_booking_form_loaded' );
		}

	};

	$( document ).on( 'yith-wcbk-init-booking-form', function () {
		var datePickers         = $( '.yith-wcbk-date-picker' ),
			bookingForms        = $( '.yith-wcbk-booking-form' ),
			peopleSelectors     = $( '.yith-wcbk-people-selector' ),
			monthPickerWrappers = $( '.yith-wcbk-month-picker-wrapper' );

		datePickers.yith_wcbk_datepicker();

		monthPickerWrappers.yith_wcbk_monthpicker();

		peopleSelectors.each( function () {
			$( this ).yith_wcbk_people_selector();
		} );

		bookingForms.each( function () {
			$( this ).yith_booking_form();
		} );

		$( document ).trigger( 'yith-wcbk-booking-form-initialized' );
	} ).trigger( 'yith-wcbk-init-booking-form' );


	/**
	 * Support for YITH WooCommerce Quick View
	 */
	$( document ).on( 'qv_loader_stop', function () {
		$( document ).trigger( 'yith-wcbk-init-booking-form' );
		$( document ).trigger( 'yith-wcbk-init-fields:selector' );
		$( document ).trigger( 'yith-wcbk-init-fields:help-tip' );
		$( document ).trigger( 'yith-wcbk-init-fields:select-list' );
	} );

} );