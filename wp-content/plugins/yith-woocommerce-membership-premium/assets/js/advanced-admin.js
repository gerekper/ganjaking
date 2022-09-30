jQuery( function ( $ ) {
	var advancedAdmin = {
		dom                   : {
			wrap             : $( '#yith-wcmbs-advanced-admin' ),
			content          : $( '#yith-wcmbs-advanced-admin__content' ),
			enabledToggle    : $( '#yith-wcmbs-advanced-admin__enabled-toggle' ),
			fields           : $( '#yith-wcmbs-advanced-admin__content input, #yith-wcmbs-advanced-admin__content select' ),
			hasEndDate       : $( '#has_end_date' ),
			endDate          : $( '#end_date' ),
			hasCredits       : $( '#has_credits' ),
			credits          : $( '#credits' ),
			creditsUpdate    : $( '#credits_update' ),
			nextCreditsUpdate: $( '#next_credits_update' ),
			discountEnabled  : $( '#discount_enabled' ),
			discount         : $( '#discount' )
		},
		selectors             : {
			setUnlimitedButton: '.yith-wcmbs-advanced-set-unlimited',
			section           : '.yith-wcmbs-form-field'
		},
		init                  : function () {
			$( document ).on( 'click', this.selectors.setUnlimitedButton, this.setUnlimitedHandler );

			this.dom.hasEndDate.on( 'change', this.hasEndDateHandler );
			this.hasEndDateHandler();
			this.dom.hasCredits.on( 'change', this.hasCreditsHandler );
			this.hasCreditsHandler();

			this.dom.discountEnabled.on( 'change', this.discountEnabledHandler );
			this.discountEnabledHandler();

			this.dom.enabledToggle.on( 'change', this.checkEnabledDisabled );
			this.checkEnabledDisabled();
		},
		setUnlimitedHandler   : function ( event ) {
			var _target  = $( event.target ),
				_section = _target.closest( advancedAdmin.selectors.section ),
				_field   = _section.find( 'input' ).first();

			if ( _field && !_field.prop( 'disabled' ) ) {
				_field.val( 'unlimited' );
			}
		},
		checkEnabledDisabled  : function () {
			if ( 'yes' === advancedAdmin.dom.enabledToggle.val() ) {
				advancedAdmin.dom.fields.attr( 'disabled', false );

				$( '.yith-wcmbs-advanced-admin--show-if-enabled' ).slideDown();

			} else {
				advancedAdmin.dom.fields.attr( 'disabled', true );

				$( '.yith-wcmbs-advanced-admin--show-if-enabled' ).slideUp();
			}
		},
		hasEndDateHandler     : function () {
			var _endDateContainer = advancedAdmin.dom.endDate.closest( advancedAdmin.selectors.section );
			if ( 'yes' === advancedAdmin.dom.hasEndDate.val() ) {
				var _originalValue = advancedAdmin.dom.endDate.data( 'original-value' );
				if ( 'unlimited' === _originalValue ) {
					var today      = new Date();
					_originalValue = today.getFullYear() + '-' + ( today.getMonth() + 1 ) + '-' + today.getDate();
				}

				advancedAdmin.dom.endDate.val( _originalValue );
				_endDateContainer.show();
			} else {
				advancedAdmin.dom.endDate.data( 'original-value', advancedAdmin.dom.endDate.val() );
				advancedAdmin.dom.endDate.val( 'unlimited' );
				_endDateContainer.hide();
			}
		},
		hasCreditsHandler     : function () {
			var _creditsContainer           = advancedAdmin.dom.credits.closest( advancedAdmin.selectors.section ),
				_creditsUpdateContainer     = advancedAdmin.dom.creditsUpdate.closest( advancedAdmin.selectors.section ),
				_nextCreditsUpdateContainer = advancedAdmin.dom.nextCreditsUpdate.closest( advancedAdmin.selectors.section );

			if ( 'yes' === advancedAdmin.dom.hasCredits.val() ) {
				var _originalValue = advancedAdmin.dom.credits.data( 'original-value' );
				if ( _originalValue < 0 ) {
					_originalValue = 1;
				}

				advancedAdmin.dom.credits.val( _originalValue );

				_creditsContainer.show();
				_creditsUpdateContainer.show();
				_nextCreditsUpdateContainer.show();
			} else {

				advancedAdmin.dom.credits.val( -1 );
				_creditsContainer.hide();
				_creditsUpdateContainer.hide();
				_nextCreditsUpdateContainer.hide();
			}
		},
		discountEnabledHandler: function () {
			var _discountContainer = advancedAdmin.dom.discount.closest( advancedAdmin.selectors.section );

			if ( 'yes' === advancedAdmin.dom.discountEnabled.val() ) {
				_discountContainer.show();
			} else {
				_discountContainer.hide();
			}
		}
	};

	advancedAdmin.init();
} );
