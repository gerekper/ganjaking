/* global jQuery, bk, yith_wcbk_dates, yith_wcbk_datepicker_params */
jQuery( function ( $ ) {
	"use strict";

	$.fn.yith_wcbk_datepicker = function () {

		var datepickerFormat  = 'yy-mm-dd',
			block_params      = {
				message        : null,
				overlayCSS     : {
					background: '#fff',
					opacity   : 0.7
				},
				ignoreIfBlocked: true
			},
			tempDate          = new Date(),
			today             = new Date( tempDate.getFullYear(), tempDate.getMonth(), tempDate.getDate() ),
			getDatepicker     = function ( _datepicker ) {
				var _type = _datepicker.data( 'type' ),
					_from = $( _datepicker.data( 'related-from' ) );

				if ( _type === 'to' && _from.length > 0 ) {
					_datepicker = _from;
				}

				return _datepicker;
			},
			getDatepickerData = function ( _datepicker ) {
				_datepicker = getDatepicker( _datepicker );

				return {
					all_day          : _datepicker.data( 'all-day' ),
					allow_same_date  : _datepicker.data( 'allow-same-date' ) || 'yes',
					ajax_load_months : _datepicker.data( 'ajax-load-months' ),
					month_to_load    : _datepicker.data( 'month-to-load' ),
					year_to_load     : _datepicker.data( 'year-to-load' ),
					min_duration     : _datepicker.data( 'min-duration' ),
					notAvailableDates: _datepicker.data( 'not-available-dates' ) || [],
					product_id       : _datepicker.data( 'product-id' ),
					is_static        : _datepicker.data( 'static' ) || 'no',
					loadedMonths     : _datepicker.data( 'loaded-months' ) || [],
					monthsToLoad     : _datepicker.data( 'months-to-load' ) || 1
				};
			};

		return $( this ).each(
			function () {
				var $current_datepicker          = $( this ),
					$relatedRangePickerWrap      = $current_datepicker.parent( '.yith-wcbk-date-range-picker' ),
					type                         = $current_datepicker.data( 'type' ),
					minDate                      = $current_datepicker.data( 'min-date' ),
					maxDate                      = $current_datepicker.data( 'max-date' ),
					numberOfMonths               = $current_datepicker.data( 'number-of-months' ) || 1,
					to                           = $( $current_datepicker.data( 'related-to' ) ),
					on_select_open               = $( $current_datepicker.data( 'on-select-open' ) ),
					from                         = $( $current_datepicker.data( 'related-from' ) ),
					$formatted                   = $current_datepicker.next( '.yith-wcbk-date-picker--formatted' ),
					$updateOnChange              = $( $current_datepicker.data( 'update-on-change' ) ),
					firstNonAvailableDate        = false,
					disableAfterNonAvailableDate = 'yes' === $current_datepicker.data( 'disable-after-non-available-date' ) && 'to' === type,
					hasMonthsToLoad              = function ( year, month, $_datepicker ) {
						$current_datepicker = $_datepicker;
						var datepickerData  = getDatepickerData( $_datepicker );

						if ( 'yes' === datepickerData.ajax_load_months ) {
							var selected_month_date = year + '-' + month + '-01',
								loaded_month_date   = datepickerData.year_to_load + '-' + datepickerData.month_to_load + '-01',
								interval            = yith_wcbk_dates.date_diff( selected_month_date, loaded_month_date, 'months' );

							if ( interval === 0 ) {
								return true;
							}
						}
						return false;
					},
					monthNeedsToBeLoaded         = function ( year, month, $_datepicker ) {
						$current_datepicker = $_datepicker;
						var datepickerData  = getDatepickerData( $_datepicker ),
							currentMonthKey = year + '-' + ( month > 9 ? month : ( '0' + parseInt( month, 10 ) ) );

						return !datepickerData.loadedMonths.includes( currentMonthKey );
					},
					updateMinToDate              = function ( _fromDate, delay ) {
						delay              = delay || 0;
						var datepickerData = getDatepickerData( $current_datepicker );
						if ( to.length > 0 ) {
							var _durationToAdd, _minDate;

							_durationToAdd = bk.settings.check_min_max_duration_in_calendar === 'yes' ? datepickerData.min_duration : 1;
							if ( datepickerData.allow_same_date === 'yes' ) {
								_durationToAdd -= 1;
							}

							if ( _durationToAdd > 0 ) {
								_minDate = yith_wcbk_dates.add_days_to_date( _fromDate, _durationToAdd );
							} else {
								_minDate = _fromDate;
							}

							if ( to.val() && yith_wcbk_dates.date_diff( to.val(), _minDate ) < 0 ) {
								to.datepicker( "setDate", null );
							}

							if ( delay ) {
								setTimeout( function () {
									to.datepicker( "option", "minDate", _minDate );
								}, delay );
							} else {
								to.datepicker( "option", "minDate", _minDate );
							}
						}
					};

				$current_datepicker.datepicker(
					{
						dateFormat     : datepickerFormat,
						minDate        : minDate,
						maxDate        : maxDate,
						showAnim       : false,
						showButtonPanel: true,
						closeText      : yith_wcbk_datepicker_params.i18n_clear,
						altField       : $formatted,
						altFormat      : bk.settings.datepickerFormat,
						numberOfMonths : numberOfMonths,
						popup          : {
							position: "bottom left",
							origin  : "top left"
						},
						beforeShow     : function ( input, instance ) {
							firstNonAvailableDate = false;
							$current_datepicker.addClass( 'yith-wcbk-datepicker--opened' );
							if ( $relatedRangePickerWrap.length ) {
								$relatedRangePickerWrap.addClass( 'yith-wcbk-date-range-picker--opened' );
								if ( type ) {
									$relatedRangePickerWrap.addClass( 'yith-wcbk-date-range-picker--opened-' + type );
								}
							}
							$( '#ui-datepicker-div' ).addClass( 'yith-wcbk-datepicker' ).addClass( 'notranslate' );

							var datepickerData = getDatepickerData( $current_datepicker );

							if ( 'yes' === datepickerData.is_static ) {
								$( '#ui-datepicker-div' ).addClass( 'yith-wcbk-datepicker--static' ).appendTo( $current_datepicker.parent().parent() );
							}

							instance.yith_booking_date_selected = false;

							setTimeout( function () {
								$( '.ui-datepicker-close' ).on( 'click', function () {
									$current_datepicker.datepicker( "setDate", null ).trigger( 'change' );
								} );
							}, 10 );
						},
						beforeShowDay  : function ( date ) {
							var allowed        = true,
								allowed_days   = $current_datepicker.data( 'allowed-days' ) || [],
								datepickerData = getDatepickerData( $current_datepicker ),
								classes        = [],
								fromDate       = 'to' === type && from.length ? from.datepicker( 'getDate' ) : false;

							if ( date.getFullYear() > 1900 && date.getDate() === 15 ) {
								var mainDatepicker = getDatepicker( $current_datepicker );

								if ( monthNeedsToBeLoaded( date.getFullYear(), date.getMonth() + 1, $current_datepicker ) ) {
									if ( hasMonthsToLoad( date.getFullYear(), date.getMonth() + 1, $current_datepicker ) ) {
										$current_datepicker.trigger( 'yith_wcbk_datepicker_update_non_available_dates', [mainDatepicker, datepickerData] );
									} else {
										$current_datepicker.trigger( 'yith_wcbk_datepicker_load_non_available_dates_in_month', [mainDatepicker, datepickerData, date.getFullYear(), date.getMonth() + 1] );
									}
								}
							}

							if ( allowed && disableAfterNonAvailableDate && firstNonAvailableDate && date.getTime() >= firstNonAvailableDate.getTime() ) {
								allowed = false;
							}
							if ( allowed && allowed_days.length > 0 ) {
								var current_day = date.getDay();
								if ( current_day === 0 ) {
									current_day = 7;
								}
								allowed = allowed_days.indexOf( current_day ) !== -1;
							}

							if ( allowed && datepickerData.notAvailableDates.length > 0 ) {
								var _min_duration = bk.settings.check_min_max_duration_in_calendar === 'yes' ? datepickerData.min_duration : 1,
									_date, formatted_date;

								if ( 'yes' === datepickerData.all_day ) {
									_min_duration -= 1;
								}

								_date          = 'to' === type ? yith_wcbk_dates.add_days_to_date( date, -_min_duration ) : date;
								formatted_date = yith_wcbk_dates.formatDate( _date );

								allowed = datepickerData.notAvailableDates.indexOf( formatted_date ) === -1;
							}

							if ( !allowed && date.getTime() > today.getTime() ) {
								classes.push( 'bk-non-available-date' );
								if ( 'to' !== type || ( !!fromDate && date.getTime() > ( fromDate.getTime() + ( yith_wcbk_dates.DAY_IN_MILLISECONDS * _min_duration ) ) ) ) {
									if ( !firstNonAvailableDate ) {
										firstNonAvailableDate = new Date( date );
									}
								}
							}

							if ( 'from' === type && to.length && yith_wcbk_dates.formatDate( date ) === to.val() ) {
								classes.push( 'bk-to-date' );
							}

							if ( 'to' === type && from.length && yith_wcbk_dates.formatDate( date ) === from.val() ) {
								classes.push( 'bk-from-date' );
							}

							return [allowed, classes.join( ' ' )];
						},
						onClose        : function ( selectedDate, instance ) {
							$( '#ui-datepicker-div' ).removeClass( 'yith-wcbk-datepicker' ).removeClass( 'notranslate' );

							var datepickerData = getDatepickerData( $current_datepicker );

							if ( 'yes' === datepickerData.is_static ) {
								$( '#ui-datepicker-div' ).removeClass( 'yith-wcbk-datepicker--static' ).appendTo( $( 'body' ) );
							}

							if ( instance.yith_booking_date_selected && on_select_open.length > 0 && !on_select_open.val() ) {
								on_select_open.trigger( 'focus' );
								setTimeout( function () {
									on_select_open.trigger( 'focus' );
								}, 50 );
							}

							$current_datepicker.removeClass( 'yith-wcbk-datepicker--opened' );
							if ( $relatedRangePickerWrap.length ) {
								$relatedRangePickerWrap.removeClass( 'yith-wcbk-date-range-picker--opened' );
								if ( type ) {
									$relatedRangePickerWrap.removeClass( 'yith-wcbk-date-range-picker--opened-' + type );
								}
							}
						},
						onSelect       : function ( selectedDate, instance ) {
							if ( selectedDate ) {
								updateMinToDate( selectedDate );

								instance.yith_booking_date_selected = true;
							}
							$( this ).trigger( 'change' );
						}
					} );

				// open datepicker when clicking on the "formatted" input
				$formatted.on( 'focus', function () {
					$current_datepicker.trigger( 'focus' );
				} );

				// open the datepicker when clicking on datepicker icon
				var $datepickerIcon    = $current_datepicker.parent().find( '.yith-wcbk-booking-date-icon' ),
					datepickerIsOpened = false;

				$datepickerIcon
					.on( 'mousedown', function () {
						datepickerIsOpened = !!$current_datepicker.datepicker( 'widget' ).is( ':visible' );
					} )
					.on( 'click', function () {
						if ( !datepickerIsOpened ) {
							$current_datepicker.trigger( 'focus' );
						}
					} );

				if ( $current_datepicker.is( '.yith-wcbk-date-picker--inline' ) ) {
					$current_datepicker.val( $current_datepicker.data( 'value' ) );
				}

				if ( $current_datepicker.val() ) {
					// Set time to prevent issues with timezones.
					$current_datepicker.datepicker( 'setDate', $current_datepicker.val() + ' 00:00:00' );
					updateMinToDate( $current_datepicker.val(), 100 );
				}

				if ( $updateOnChange.length ) {
					$current_datepicker.on( 'change', function () {
						$updateOnChange.val( $( this ).val() );
					} );

					$updateOnChange.val( $current_datepicker.val() );
				}
			}
		);
	};
} );