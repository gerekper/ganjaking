/* globals bk, wcbk_admin, google, ajaxurl */
jQuery( function ( $ ) {
	"use strict";

	var block_params         = bk.blockParamsNoLoader,
		block_params_disable = bk.blockParamsDisable,
		yith_wcbk_fake_form  = {
			reset_fields : function ( fields ) {

				fields.each( function () {
					var field = $( this );

					if ( field.is( 'input' ) ) {
						if ( field.attr( 'type' ) === 'checkbox' ) {
							field.removeAttr( 'checked' );
						} else if ( field.attr( 'type' ) === 'text' || field.attr( 'type' ) === 'number' ) {
							field.val( '' );
						}
					} else if ( field.is( 'textarea' ) ) {
						field.val( '' );
					}
				} );
			},
			get_form_data: function ( fields ) {
				var data = !!fields.length ? {} : false;

				fields.each( function () {
					if ( data !== false ) {
						var field    = $( this ),
							name     = field.data( 'name' ) || false,
							required = field.data( 'required' ) || 'no';

						if ( name ) {
							if ( field.is( 'input[type=checkbox]' ) ) {
								if ( field.is( ':checked' ) ) {
									data[ name ] = field.val();
								}
							} else {
								data[ name ] = field.val();
							}
						}

						if ( required === 'yes' && field.val() === '' ) {
							field.focus();
							data = false;
						}
					}
				} );

				return data;
			}
		};

	var dom        = {
			productData        : $( '#woocommerce-product-data' ),
			productType        : $( 'select#product-type' ),
			bookingDurationUnit: $( 'select#_yith_booking_duration_unit' ),
			minuteSelect       : $( '#_yith_booking_duration_minute_select' ),
			bookingDuration    : $( '#_yith_booking_duration' ),
			bookingDurationType: $( 'select#_yith_booking_duration_type' ),
			peopleEnabled      : $( '#_yith_booking_has_persons' ),
			peopleTypesEnabled : $( '#_yith_booking_enable_person_types' ),
			canBeCancelled     : $( '#_yith_booking_can_be_cancelled' )
		},
		isBooking  = function () {
			return wcbk_admin.prod_type === dom.productType.val();
		},
		getBooking = function () {
			var _booking = false;
			if ( isBooking() ) {
				_booking = {
					duration          : parseInt( dom.bookingDuration.val(), 10 ),
					durationUnit      : dom.bookingDurationUnit.val(),
					durationType      : dom.bookingDurationType.val(),
					canBeCancelled    : 'yes' === dom.canBeCancelled.val(),
					peopleEnabled     : 'yes' === dom.peopleEnabled.val(),
					peopleTypesEnabled: 'yes' === dom.peopleTypesEnabled.val()
				};

				_booking.hasTime          = ['hour', 'minute'].includes( _booking.durationUnit );
				_booking.isCustomerOneDay = 'customer' === _booking.durationType && 1 === _booking.duration && 'day' === _booking.durationUnit;
				_booking.isFixedAndTime   = 'fixed' === _booking.durationType && _booking.hasTime;
			}

			return _booking;
		};

	if ( 'yes' === wcbk_admin.isCreatingNewBookingProduct ) {
		dom.productType.val( wcbk_admin.prod_type ).trigger( 'change' );
	}

	/** ------------------------------------------------------------------------
	 *  Show/Hide Fields
	 * ------------------------------------------------------------------------- */

	var bkEnabledFields = {
		enablePrefix       : '.bk_enable_if_',
		conditions         : {
			customer_chooses_blocks: 'customer_chooses_blocks',
			can_be_cancelled       : 'can_be_cancelled',
			customer_one_day       : 'customer_one_day',
			booking_has_persons    : 'booking_has_persons',
			day                    : 'day',
			time                   : 'time',
			fixed_and_time         : 'fixed_and_time',
			unit_is_month          : 'unit_is_month',
			unit_is_day            : 'unit_is_day',
			unit_is_hour           : 'unit_is_hour',
			unit_is_minute         : 'unit_is_minute',
			people_and_people_types: 'people_and_people_types'
		},
		initialProductType : 'simple',
		init               : function () {
			var self = bkEnabledFields;

			self.initialProductType = dom.productType.val();

			dom.minuteSelect.hide().on( 'change', function () {
				dom.bookingDuration.val( $( this ).val() ).trigger( 'change' );
			} );

			// show TAX if product is Booking
			$( document ).find( '._tax_status_field' ).closest( 'div' ).addClass( 'show_if_' + wcbk_admin.prod_type );

			// on select booking product set Virtual as checked
			dom.productType.on( 'change', function () {
				if ( bkEnabledFields.initialProductType === wcbk_admin.prod_type ) {
					return;
				}
				if ( wcbk_admin.prod_type === $( this ).val() ) {
					$( '#_virtual' ).attr( 'checked', 'checked' ).trigger( 'change' );
				}
			} ).trigger( 'change' ); // trigger change to product type select to call WC show_and_hide_panels()

			// Booking has times or is day
			dom.bookingDurationUnit.on( 'change', function () {
				var value    = $( this ).val(),
					has_time = value === 'hour' || value === 'minute',
					is_day   = value === 'day';

				// Minute Select
				if ( value === 'minute' ) {
					dom.bookingDuration.hide();
					dom.minuteSelect.show().trigger( 'change' );
				} else {
					dom.minuteSelect.hide();
					dom.bookingDuration.show();
				}

				$( document ).trigger( 'yith_wcbk_booking_product_duration_unit_changed', [{
					value   : value,
					has_time: has_time,
					is_day  : is_day
				}] );
			} ).trigger( 'change' );

		},
		handleEnabledFields: function ( theBooking ) {
			var self = bkEnabledFields;

			if ( theBooking ) {
				self.handle( self.conditions.customer_chooses_blocks, 'customer' === theBooking.durationType );
				self.handle( self.conditions.can_be_cancelled, theBooking.canBeCancelled );
				self.handle( self.conditions.customer_one_day, theBooking.isCustomerOneDay );
				self.handle( self.conditions.fixed_and_time, theBooking.isFixedAndTime );
				self.handle( self.conditions.booking_has_persons, theBooking.peopleEnabled );
				self.handle( self.conditions.booking_has_persons, theBooking.peopleEnabled && theBooking.peopleTypesEnabled );

				self.handle( self.conditions.time, theBooking.hasTime );
				self.handle( self.conditions.day, 'day' === theBooking.durationUnit );
				self.handle( self.conditions.unit_is_month, 'month' === theBooking.durationUnit );
				self.handle( self.conditions.unit_is_day, 'day' === theBooking.durationUnit );
				self.handle( self.conditions.unit_is_hour, 'hour' === theBooking.durationUnit );
				self.handle( self.conditions.unit_is_minute, 'minute' === theBooking.durationUnit );
			}
		},
		handle             : function ( target, condition ) {
			var targetDisable = bkEnabledFields.enablePrefix + target;

			if ( condition ) {
				$( targetDisable ).unblock();
			} else {
				$( targetDisable ).block( block_params_disable );
			}
		}
	};

	bkEnabledFields.init();

	/**
	 * bkDataHandler
	 */

	var bkDataHandler = {
		init      : function () {
			dom.productType.on( 'change', bkDataHandler.update );
			dom.bookingDurationUnit.on( 'change', bkDataHandler.update );
			dom.bookingDurationType.on( 'change', bkDataHandler.update );
			dom.canBeCancelled.on( 'change', bkDataHandler.update );
			dom.peopleEnabled.on( 'change', bkDataHandler.update );
			dom.peopleTypesEnabled.on( 'change', bkDataHandler.update );
			$( document ).on( 'change', '#_yith_booking_duration_type, #_yith_booking_duration, #_yith_booking_duration_unit', bkDataHandler.update );
			bkDataHandler.update();
		},
		getClasses: function ( theBooking ) {
			var classes = [];

			if ( theBooking ) {
				classes.push( 'bk_is_booking' );

				classes.push( 'bk_duration_unit--' + theBooking.durationUnit );
				classes.push( 'bk_duration_type--' + theBooking.durationType );

				theBooking.hasTime && classes.push( 'bk_has_time' );
				theBooking.canBeCancelled && classes.push( 'bk_can_be_cancelled' );
				theBooking.isCustomerOneDay && classes.push( 'bk_is_customer_one_day' );
				theBooking.isFixedAndTime && classes.push( 'bk_is_fixed_and_time' );
				theBooking.peopleEnabled && classes.push( 'bk_has_people' );
				theBooking.peopleEnabled && theBooking.peopleTypesEnabled && classes.push( 'bk_has_people_and_people_types' );
			}

			return classes;
		},
		update    : function () {
			var theBooking = getBooking(),
				classes    = bkDataHandler.getClasses( theBooking );

			dom.productData.removeClass(
				function ( _idx, _classes ) {
					return _classes.split( /\s+/ ).filter( function ( singleClass ) {
						return singleClass.indexOf( 'bk_' ) === 0 && !classes.includes( singleClass );
					} ).join( ' ' );
				}
			);

			dom.productData.addClass( classes );
			bkEnabledFields.handleEnabledFields( theBooking );
		}
	};
	bkDataHandler.init();


	/** ------------------------------------------------------------------------
	 *  Costs Table
	 * ------------------------------------------------------------------------- */
	var costs_table            = $( '#yith-wcbk-booking-costs-table' ),
		costs_default_row      = costs_table.find( 'tr.yith-wcbk-costs-default-row' ).first(),
		costs_add_range        = $( '#yith-wcbk-costs-add-range' ),
		costs_fields_container = $( '#' + costs_table.data( 'fields-container-id' ) ),
		costs_input            = costs_fields_container.find( '.yith-wcbk-admin-input-range' ),
		costs_number           = costs_fields_container.find( '.yith-wcbk-number-range' ),
		costs_monthselect      = costs_fields_container.find( '.yith-wcbk-month-range-select' ),
		costs_weekselect       = costs_fields_container.find( '.yith-wcbk-week-range-select' ),
		costs_dayselect        = costs_fields_container.find( '.yith-wcbk-day-range-select' );

	costs_table
		.on( 'change', 'select.yith-wcbk-costs-range-type-select', function ( event ) {
			var select     = $( event.target ),
				row        = select.closest( 'tr' ),
				from       = row.find( 'td.yith-wcbk-costs-from' ),
				to         = row.find( 'td.yith-wcbk-costs-to' ),
				range_type = select.val(),
				from_input, to_input;

			switch ( range_type ) {
				case 'custom':
					from_input = costs_input.clone().addClass( 'yith-wcbk-admin-date-picker' ).yith_wcbk_datepicker();
					to_input   = costs_input.clone().addClass( 'yith-wcbk-admin-date-picker' ).yith_wcbk_datepicker();
					break;
				case 'month':
					from_input = costs_monthselect.clone();
					to_input   = costs_monthselect.clone();
					break;
				case 'week':
					from_input = costs_weekselect.clone();
					to_input   = costs_weekselect.clone();
					break;
				case 'day':
					from_input = costs_dayselect.clone();
					to_input   = costs_dayselect.clone();
					break;
				case 'time':
					break;
				default:
					from_input = costs_number.clone();
					to_input   = costs_number.clone();
					break;
			}

			if ( 'time' === range_type ) {
				from_input.find( 'input' ).attr( 'name', '_yith_booking_costs_range[from][]' );
				to_input.find( 'input' ).attr( 'name', '_yith_booking_costs_range[to][]' );
			} else {
				from_input.attr( 'name', '_yith_booking_costs_range[from][]' );
				to_input.attr( 'name', '_yith_booking_costs_range[to][]' );
			}

			from.html( from_input );
			to.html( to_input );

		} )
		/* ----  D e l e t e   R o w  ---- */
		.on( 'click', '.yith-wcbk-delete', function ( event ) {
			var delete_btn = $( event.target ),
				target_row = delete_btn.closest( 'tr' );

			target_row.remove();
		} )

		/* ----  S o r t a b l e  ---- */
		.find( 'tbody' ).sortable( {
									   items               : 'tr',
									   cursor              : 'move',
									   handle              : '.yith-wcbk-anchor',
									   axis                : 'y',
									   scrollSensitivity   : 40,
									   forcePlaceholderSize: true,
									   opacity             : 0.65,
									   helper              : function ( e, tr ) {
										   var originals = tr.children(),
											   helper    = tr.clone();
										   helper.children().each( function ( index ) {
											   // Set helper cell sizes to match the original sizes
											   $( this ).width( originals.eq( index ).width() );
										   } );
										   return helper;
									   }
								   } );

	costs_add_range.on( 'click', function () {
		var added_row = costs_default_row.clone().removeClass( 'yith-wcbk-costs-default-row' );
		costs_table.append( added_row );
		added_row.find( 'select.yith-wcbk-costs-range-type-select' ).trigger( 'change' );
	} );

	/** ------------------------------------------------------------------------
	 *  Dynamic Duration
	 * ------------------------------------------------------------------------- */
	var yith_wcbk_product_metabox_dynamic_durations = function () {
		var _duration_unit = dom.bookingDurationUnit.val(),
			_duration      = dom.bookingDuration.val(),
			_duration_label, _duration_label_qty, _duration_unit_label;

		if ( _duration < 2 ) {
			_duration_label     = bk.i18n_durations[ _duration_unit ].singular.replace( '%s', _duration );
			_duration_label_qty = bk.i18n_durations[ _duration_unit ].singular_qty.replace( '%s', _duration );
		} else {
			_duration_label     = bk.i18n_durations[ _duration_unit ].plural.replace( '%s', _duration );
			_duration_label_qty = bk.i18n_durations[ _duration_unit ].plural_qty.replace( '%s', _duration );
		}

		_duration_unit_label = bk.i18n_durations[ _duration_unit ].plural_unit;

		$( '.yith-wcbk-product-metabox-dynamic-duration' ).html( _duration_label );
		$( '.yith-wcbk-product-metabox-dynamic-duration-qty' ).html( _duration_label_qty );
		$( '.yith-wcbk-product-metabox-dynamic-duration-unit' ).html( _duration_unit_label );
	};
	$( document ).on( 'change', '#_yith_booking_duration, #_yith_booking_duration_unit', yith_wcbk_product_metabox_dynamic_durations );
	$( document ).on( 'yith_wcbk_product_metabox_dynamic_durations', yith_wcbk_product_metabox_dynamic_durations );
	yith_wcbk_product_metabox_dynamic_durations();

	/**
	 * Expand/Collapse People Types
	 */
	var yith_wcbk_product_people_types = {
		expandCollapseButton: $( '.yith-wcbk-people-types__expand-collapse' ),
		list                : $( '#yith-wcbk-people-types__list' ),
		init                : function () {
			this.expandCollapseButton.on( 'click', this.expandCollapseAll );
		},
		expandCollapseAll   : function ( event ) {
			var button = $( event.target ).closest( '.yith-wcbk-people-types__expand-collapse' ),
				list   = yith_wcbk_product_people_types.list;

			if ( button.is( '.yith-wcbk-people-types__expand-collapse--collapse' ) ) {
				button.removeClass( 'yith-wcbk-people-types__expand-collapse--collapse' );
				list.find( '.yith-wcbk-settings-section-box:not(.yith-wcbk-settings-section-box--closed) .yith-wcbk-settings-section-box__toggle' ).click();
			} else {
				button.addClass( 'yith-wcbk-people-types__expand-collapse--collapse' );
				list.find( '.yith-wcbk-settings-section-box.yith-wcbk-settings-section-box--closed .yith-wcbk-settings-section-box__toggle' ).click();
			}
		}
	};

	yith_wcbk_product_people_types.init();

	$( '.yith-wcbk-product-sub-tab' ).last().addClass( 'yith-wcbk-product-sub-tab--last' );

} );