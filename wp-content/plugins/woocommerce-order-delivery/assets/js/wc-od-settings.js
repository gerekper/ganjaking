/* global wc_od_settings_l10n */
jQuery(function( $ ) {

	'use strict';

	if ( typeof wc_od_settings_l10n === 'undefined' ) {
		return false;
	}

	var WC_OD_Settings = {
		init: function() {
			var $timepickers   = $( '.timepicker' ),
			    $eventCalendar = $( '.wc-od-calendar-field' );

			// Init timepickers
			if ( $timepickers.length ) {
				$timepickers.timepicker( { timeFormat: 'H:i', maxTime: '23:59' } );
			}

			this.deliveryCheckoutOptionsToggle();
			this.statusToggle();
			this.shippingMethodsFieldsToggle();

			if ( $eventCalendar.length && typeof $.fn.WC_OD_Calendar === 'function' ) {
				// Init calendar.
				if ( 'delivery' === wc_od_settings_l10n.eventsType ) {
					$eventCalendar.WC_OD_Delivery_Calendar( wc_od_settings_l10n );
				} else {
					$eventCalendar.WC_OD_Calendar( wc_od_settings_l10n );
				}
			}
		},
		deliveryCheckoutOptionsToggle: function() {
			var $field = $( 'input[name="wc_od_checkout_delivery_option"]' ),
				$toggleFields = $(
					'.wc_od_time_frames,' +
					'.wc_od_delivery_days,' +
					'#wc_od_max_delivery_days,' +
					'[name="wc_od_delivery_fields_option"],' +
					'#wc_od_subscriptions_limit_to_billing_interval'
				).closest( 'tr' );

			if ( $field.length ) {
				if ( 'text' === $field.filter( ':checked' ).val() ) {
					$toggleFields.hide();
				}

				$field.on( 'change', function() {
					$toggleFields.toggle();
				});
			}
		},
		statusToggle: function() {
			$( '.wc-od-input-toggle input[type="checkbox"]' ).on( 'change', function() {
				var $toggle = $( this ).siblings( 'span' );

				if ( $( this ).prop( 'checked' ) ) {
					$toggle.attr( 'class', $toggle.attr( 'class' ).replace( 'disabled', 'enabled' ) );
				} else {
					$toggle.attr( 'class', $toggle.attr( 'class' ).replace( 'enabled', 'disabled' ) );
				}
			});
		},

		/**
		 * Backward compatibility.
		 *
		 * This code has been moved to table-fields.js.
		 *
		 * @deprecated 1.7.0
		 */
		madeSortable: function() {
			$( 'table.wc-od-field-table.sortable tbody' ).sortable({
				items: 'tr:not(.unsortable)',
				cursor: 'move',
				axis: 'y',
				handle: 'td.sort',
				scrollSensitivity: 40,
				helper: function( event, ui ) {
					ui.children().each( function() {
						$( this ).width( $( this ).width() );
					});
					ui.css( 'left', '0' );
					return ui;
				},
				start: function( event, ui ) {
					ui.item.css( 'background-color', '#f6f6f6' );
				},
				stop: function( event, ui ) {
					ui.item.removeAttr( 'style' );
				}
			});
		},
		shippingMethodsFieldsToggle: function() {
			$( 'select#shipping_methods_option' ).on( 'change', function() {
				if ( 'specific' === $( this ).val() ) {
					$( this ).closest('tr').next( 'tr' ).hide();
					$( this ).closest('tr').next().next( 'tr' ).show();
				} else if ( 'all_except' === $( this ).val() ) {
					$( this ).closest('tr').next( 'tr' ).show();
					$( this ).closest('tr').next().next( 'tr' ).hide();
				} else {
					$( this ).closest('tr').next( 'tr' ).hide();
					$( this ).closest('tr').next().next( 'tr' ).hide();
				}
			}).trigger( 'change' );
		}
	};

	WC_OD_Settings.init();
});