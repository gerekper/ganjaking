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
			this.feeFieldsToggle();
			this.localPickupToggle();

			if ( $eventCalendar.length && typeof $.fn.WC_OD_Calendar === 'function' ) {
				// Init calendar.
				if ( 'delivery' === wc_od_settings_l10n.eventsType ) {
					$eventCalendar.WC_OD_Delivery_Calendar( wc_od_settings_l10n );
				} else {
					$eventCalendar.WC_OD_Calendar( wc_od_settings_l10n );
				}
			}
		},

		isCheckoutOption: function( option ) {
			return ( option === $( 'input[name="wc_od_checkout_delivery_option"]' ).filter( ':checked' ).val() );
		},

		deliveryCheckoutOptionsToggle: function() {
			var that   = this,
			    $field = $( 'input[name="wc_od_checkout_delivery_option"]' ),
			    $toggleFields = $(
					'.wc_od_time_frames,' +
					'#wc_od_checkout_text,' +
					'.wc_od_delivery_days,' +
					'#wc_od_max_delivery_days,' +
					'[name="wc_od_delivery_fields_option"],' +
					'#wc_od_pickup_text'
				).closest( 'tr' );

			if ( $field.length ) {
				if ( 'text' === $field.filter( ':checked' ).val() ) {
					$toggleFields.hide();
					this.settingsSectionToggle( 'subscription_options', false );
				}

				$field.on( 'change', function() {
					var display = ( 'text' !== $field.filter( ':checked' ).val() );

					$toggleFields.toggle( display );
					that.settingsSectionToggle( 'subscription_options', display );
					that.localPickupToggle();
				});
			}
		},

		settingsSectionToggle: function( sectionId, display ) {
			var description = $( '#' + sectionId + '-description' );

			if ( ! description.length ) {
				return;
			}

			description.toggle( display );
			description.prev( 'h2' ).toggle( display );
			description.next( '.form-table' ).toggle( display );
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
				var $tr = $( this ).closest( 'tr' );

				if ( 'specific' === $( this ).val() ) {
					$tr.next( 'tr' ).hide();
					$tr.next().next( 'tr' ).show();
				} else if ( 'all_except' === $( this ).val() ) {
					$tr.next( 'tr' ).show();
					$tr.next().next( 'tr' ).hide();
				} else {
					$tr.next( 'tr' ).hide();
					$tr.next().next( 'tr' ).hide();
				}
			}).trigger( 'change' );
		},

		feeFieldsToggle: function() {
			$( '#fee_tax_status' ).on( 'change', function() {
				$( '#fee_tax_class' ).closest( 'tr' ).toggle( 'none' !== $( this ).val() )
			}).trigger( 'change' );

			$( '#fee_amount' ).on( 'keyup', function() {
				var amount = $( this ).val().replace( /[,.]/g, '' );

				$( '[name^="fee_"]' ).not( '#fee_amount' ).closest( 'tr' ).toggle( 0 < amount );

				if ( 0 < amount ) {
					$( '#fee_tax_status' ).trigger( 'change' );
				}
			}).trigger( 'keyup' );
		},

		localPickupToggle: function() {
			var $field = $( 'input#wc_od_enable_local_pickup' ),
				$toggleField = $( 'textarea#wc_od_pickup_text' ).closest( 'tr' ),
				isCalendarOption = this.isCheckoutOption( 'calendar' );

			if( ! $field.prop( 'checked' ) || ! isCalendarOption ) {
				$toggleField.hide();
			}

			$field.on( 'change', function() {
				$toggleField.toggle( $field.prop( 'checked' ) && isCalendarOption );
			});
		},
	};

	WC_OD_Settings.init();
});
