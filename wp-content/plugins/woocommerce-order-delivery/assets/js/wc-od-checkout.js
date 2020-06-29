/* global wc_od_checkout_l10n */
jQuery( function( $ ) {

	'use strict';

	if ( typeof wc_od_checkout_l10n === 'undefined' ) {
		return false;
	}

	var WC_OD = function( options ) {
		this.options = options;
		this.$body   = $( 'body' );

		this.init();
	};

	WC_OD.prototype = {

		init: function() {
			// Bind events.
			this._bindEvents();
		},

		_bindEvents: function() {
			var that = this;

			// Update the calendar when the checkout form changes.
			this.$body.on( 'updated_checkout', function() {
				that.updateDeliveryDateCalendar();
			});

			// Update the checkout form when the time frame field changes.
			this.$body.on( 'change', '#delivery_time_frame', function() {
				that.$body.trigger( 'update_checkout' );
			});
		},

		updateDeliveryDateCalendar: function() {
			var that = this;

			// Refresh the options.
			this.options = $.extend( {}, this.options, window.wc_od_checkout_l10n );

			// Create the calendar.
			$( '#delivery_date' ).wc_od_datepicker( this.options ).on( 'changeDate', function() {
				that.$body.trigger( 'update_checkout' );
			});
		}
	};

	new WC_OD( wc_od_checkout_l10n );
});