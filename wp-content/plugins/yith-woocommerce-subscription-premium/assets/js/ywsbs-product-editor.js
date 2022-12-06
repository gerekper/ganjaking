/**
 * ywsbs-product-editor.js
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Subscription
 * @version 1.0.0
 */
jQuery( function ( $ ) {

	var checkSubscriptionEndPeriod = function () {

		$( document ).on( 'change', '.ywsbs_price_time_option, .variable_ywsbs_subscription', function () {
			var timeOption = $( this ),
				$main      = timeOption.closest( '.ywsbs-general-section' ),
				selected = $("option:selected", timeOption ),
				timeOptionVal = timeOption.val(),
				sync_info = $main.find( '.ywsbs-synchronize-info' );

			$main.find( '.synch_section' ).addClass( 'hide' );

			$main.find( '.max-length-time-opt' ).text( selected.data('text') );

			if ( sync_info.length > 0 ) {
				if ( 'days' === timeOptionVal ) {
					sync_info.fadeOut().addClass( 'hide' );
				} else {
					if ( sync_info.hasClass( 'hide' ) ) {
						sync_info.removeClass( 'hide' ).fadeIn();
					}
					$main.find( '.synch_section[data-synch="' + timeOptionVal + '"]' ).removeClass( 'hide' );
				}
			}

		} );

		$( '#_ywsbs_price_time_option' ).change();
	};

	//Open or close the subscription panel for single products.
	var isSubscription = function () {
		var $sbs = $( '#_ywsbs_subscription' );
		if ( $sbs.is( ':checked' ) ) {
			$( '.ywsbs-general-section' ).slideDown( 'slow' );
		} else {
			$( '.ywsbs-general-section' ).slideUp( 'slow' );
		}
	};

	//Open or close the subscription panel for variable products.
	var isSubscriptionVariable = function () {
		var $sbs = $( '.checkbox_ywsbs_subscription' );

		$.each( $sbs, function () {
			var $t           = $( this ),
				ywsbsSection = $t.closest( '.data' ).find( '.ywsbs-general-section' );
			if ( $t.is( ':checked' ) ) {
				ywsbsSection.slideDown( 'slow' );
			} else {
				ywsbsSection.slideUp( 'slow' );
			}
		} );
	};

	//Open or close the subscription panel for variable products.
	var deliveryScheduleVariable = function () {
		var $delivery = $( '.delivery_period' );

		$.each( $delivery, function () {
			var $delivery_period = $( this ),
				$wrapper         = $delivery_period.closest( '.ywsbs_subscription_variation_products' );
			$syncWrapper         = $wrapper.find( '.ywsbs_delivery_synch_wrapper' );
			if ( $delivery_period.val() === 'days' ) {
				$syncWrapper.slideUp();
			} else {
				$syncWrapper.slideDown();
			}

			$wrapper.find( '.show-for.visible' ).slideUp( 'slow', function () {
			} ).removeClass( 'visible' );

			$wrapper.find( '.show-for-' + $delivery_period.val() ).slideDown( 'slow' ).css( {display: 'flex'} ).addClass( 'visible' );
		} );
	};

	//check the option dependances.
	var load_deps = function () {
		var depFields = $( document ).find( '.ywsbs-general-section .form-field[data-deps-on*="_ywsbs"]' );

		$.each( depFields, function () {
			var $t        = $( this ),
				type      = $t.data( 'type' ),
				fieldVal  = $t.data( 'deps-val' ),
				$fieldDep = false;

			if ( type === 'radio' ) {
				$fieldDep = $( document ).find( 'input[name="' + $t.data( 'deps-on' ) + '"]:checked' );
			} else {
				$fieldDep = $( document ).find( 'input[name="' + $t.data( 'deps-on' ) + '"]' );
			}

			if ( (type === 'checkbox' && $fieldDep.is( ':checked' )) || $fieldDep.val() == fieldVal ) {
				$t.slideDown( 'slow' );
			} else {
				$t.slideUp( 'slow' );
			}
		} );


		$( document ).on( 'change', 'input', function () {
			var $t    = $( this ),
				name  = $t.attr( 'name' ),
				field = $( document ).find( '.ywsbs-general-section .form-field[data-deps-on="' + name + '"]' );

			if ( $t.val() == field.data( 'deps-val' ) ) {
				field.slideDown( 'slow' );
			} else {
				field.slideUp( 'slow' );
			}

		} );

		$( document ).on( 'change', '.ywsbs_override_pause_settings input', function () {
			var $t           = $( this ),
				active       = $t.val(),
				depOpt       = $t.closest( '.ywsbs-general-section' ).find( '._ywsbs_enable_pause_field' ),
				doubleDepOpt = $t.closest( '.ywsbs-general-section' ).find( '.ywsbs_pause_options' );

			if ( active === 'yes' ) {
				depOpt.slideDown( 'slow' );
				depOpt.find( 'input:checked' ).change();
			} else {
				depOpt.slideUp( 'slow' );
				doubleDepOpt.slideUp( 'slow' );
			}
		} );

		//single product
		$( document ).on( 'change', '#_ywsbs_delivery_synch_delivery_period', function () {
			if ( $( '#_ywsbs_override_delivery_schedule' ).is( ':checked' ) ) {
				var $t = $( this );
				if ( $t.val() === 'days' ) {
					$( '.ywsbs_delivery_synch_wrapper' ).slideUp();
				} else {
					$( '.ywsbs_delivery_synch_wrapper' ).slideDown();
				}
			}
		} );

		$( '.ywsbs_override_pause_settings input' ).change();
		$( '#_ywsbs_delivery_sync_delivery_schedules' ).change();
		$( '#_ywsbs_override_delivery_schedule' ).change();
		$( '#_ywsbs_delivery_synch_delivery_period' ).change();
	};

	isSubscription();
	$( document ).on( 'click', '#_ywsbs_subscription', isSubscription );
	checkSubscriptionEndPeriod();

	load_deps();
	$( '#woocommerce-product-data' ).on( 'woocommerce_variations_loaded', function () {
		isSubscriptionVariable();
		deliveryScheduleVariable();

		$( document ).on( 'click', '.checkbox_ywsbs_subscription', isSubscriptionVariable );
		$( document ).on( 'change', '.delivery_period', deliveryScheduleVariable );
		load_deps();
	} );


	function countSubscriptionVariation() {
		var subscriptionVariations = $( document ).find( '.woocommerce_variable_attributes input.checkbox.checkbox_ywsbs_subscription:checked' ).length;

		var switchPriority = $( document ).find( '.switchable_priority' );

		$.each( switchPriority, function () {
			var $t           = $( this );
			var currentValue = parseInt( $t.val() );

			var $html = '';
			var i;
			for ( i = 0; i < subscriptionVariations; i++ ) {
				var counter        = parseInt( i );
				var optionSelected = (currentValue === counter) ? '" selected="selected"' : '"';
				$html += '<option value="' + counter + optionSelected + '>' + (counter + 1) + '</option>';
			}

			$t.html( $html );

		} );
	}

	function delivery_changes() {
		var $delivery_period = $( document ).find( '.single-delivery-period' );
		$( document ).find( '.show-for.visible' ).slideUp( 'slow', function () {
		} ).removeClass( 'visible' );
		$( document ).find( '.show-for-' + $delivery_period.val() ).slideDown( 'slow' ).css( {display: 'flex'} ).addClass( 'visible' );
	}

	$( document ).on( 'change', '.single-delivery-period', delivery_changes );
	$( document ).find( '.single-delivery-period' ).length > 0 && $( document ).find( '.single-delivery-period' ).change();

	$( document ).on( 'change', '.woocommerce_variable_attributes input.checkbox.checkbox_ywsbs_subscription', countSubscriptionVariation );
	$( '#woocommerce-product-data' ).on( 'woocommerce_variations_loaded', countSubscriptionVariation );


} );
