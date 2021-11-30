/**
 * Settings.
 *
 * @package WC_Account_Funds/Assets/JS/Admin
 * @since   2.6.0
 */

( function( $ ) {

	'use strict';

	var Settings = {

		init: function () {
			this.$topUpCheckbox = $( 'input#account_funds_enable_topup' );
			this.$discountCheckbox = $( 'input#account_funds_give_discount' );

			this.toggleTopUpFields( this.$topUpCheckbox.prop( 'checked' ) );
			this.toggleDiscountsFields( this.$discountCheckbox.prop( 'checked' ) );
			this.bindEvents();
		},

		bindEvents: function() {
			var that = this;

			this.$topUpCheckbox.on( 'change', function() {
				that.toggleTopUpFields( $( this ).prop( 'checked' ) );
			});

			this.$discountCheckbox.on( 'change', function() {
				that.toggleDiscountsFields( $( this ).prop( 'checked' ) );
			});
		},

		toggleTopUpFields: function( visible ) {
			$( 'input#account_funds_min_topup, input#account_funds_max_topup' ).closest( 'tr' ).toggle( visible );
		},

		toggleDiscountsFields: function( visible ) {
			$( 'select#account_funds_discount_type, input#account_funds_discount_amount' ).closest( 'tr' ).toggle( visible );
		}
	};

	$( function() {
		Settings.init();
	});
} )( jQuery );
