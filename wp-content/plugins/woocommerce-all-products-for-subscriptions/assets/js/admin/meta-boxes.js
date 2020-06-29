/* global wcsatt_admin_params */
jQuery( function($) {

	var $wcsatt_data_tab        = $( '#wcsatt_data' ),
		$wcsatt_options_wrapper = $wcsatt_data_tab.find( '.general_scheme_options' ),
		$wcsatt_options_one_off = $wcsatt_options_wrapper.find( 'input#_wcsatt_allow_one_off' ),
		$wcsatt_options_default = $wcsatt_options_wrapper.find( 'select#_wcsatt_default_status' ),
		$wcsatt_options_layout  = $wcsatt_options_wrapper.find( '.wcsatt_image_select__container' ),
		$wcsatt_options_prompt  = $wcsatt_options_wrapper.find( 'textarea#_wcsatt_subscription_prompt' ),
		$wcsatt_schemes_wrapper = $wcsatt_data_tab.find( '.subscription_schemes' ),
		$wcsatt_schemes         = $wcsatt_schemes_wrapper.find( '.subscription_scheme' ),
		wcsatt_schemes_count    = $wcsatt_schemes.length,
		wcsatt_block_params     = {
		message:    null,
		overlayCSS: {
			background: wcsatt_admin_params.post_id !== '' ? '#fff' : '#f1f1f1',
			opacity:    0.6
		}
	};

	/* ------------------------------------*/
	/* Subscription Schemes
	/* ------------------------------------*/

	function days_in_month( month ) {
		// Intentionally choose a non-leap year because we want february to have only 28 days.
		return new Date( Date.UTC( 2001, month, 0 ) ).getUTCDate();
	}

	$.fn.wcsatt_init_help_tips = function() {

		$( this ).find( '.help_tip, .tips, .woocommerce-help-tip' ).tipTip( {
			'attribute': 'data-tip',
			'fadeIn':    50,
			'fadeOut':   50,
			'delay':     200
		} );
	};

	$.fn.wcsatt_init_type_dependent_inputs = function() {

		var product_type         = $( 'select#product-type' ).val(),
		    override_option_text = 'variable' === product_type ? wcsatt_admin_params.i18n_override_option_variable : wcsatt_admin_params.i18n_override_option,
		    inherit_option_text  = 'variable' === product_type ? wcsatt_admin_params.i18n_inherit_option_variable : wcsatt_admin_params.i18n_inherit_option,
		    discount_description = 'variable' === product_type ? wcsatt_admin_params.i18n_discount_description_variable : wcsatt_admin_params.i18n_discount_description;

		$( this ).find( '.subscription_pricing_method_input [value="inherit"]' ).text( inherit_option_text );
		$( this ).find( '.subscription_pricing_method_input [value="override"]' ).text( override_option_text );
		$( this ).find( '.subscription_price_discount .woocommerce-help-tip' ).attr( 'data-tip', discount_description ).wcsatt_init_help_tips();
	};

	$.fn.wcsatt_refresh_days_in_month = function() {

		var $this                = $( this ),
		    $dayOfMonthInput     = $this.find( '.satt_subscription_payment_sync_date_day' ),
		    $syncAnnualContainer = $this.find( '.subscription_sync_annual' ),
		    monthInputVal        = parseInt( $this.find( '.wc_input_subscription_payment_sync_date_month' ).val(), 10 );

		if ( monthInputVal ) {

			var days_max = days_in_month( monthInputVal );

			if ( $dayOfMonthInput.val() > days_max ) {
				$dayOfMonthInput.val( days_max );
			}

			$dayOfMonthInput.attr( {
				step: '1',
				min: '1',
				max: days_max
			} );

			$syncAnnualContainer.addClass( 'subscription_sync_annual--synced' );

		} else {

			$syncAnnualContainer.removeClass( 'subscription_sync_annual--synced' );
		}
	};

	$.fn.wcsatt_refresh_scheme_lengths = function() {

		var $this             = $( this ),
		    $lengthElement    = $this.find( '.wc_input_subscription_length' ),
		    $periodSelector   = $this.find( '.wc_input_subscription_period' ),
		    $intervalSelector = $this.find( '.wc_input_subscription_period_interval' ),
		    selectedLength    = $lengthElement.val(),
		    billingInterval   = parseInt( $intervalSelector.val(), 10 ),
		    hasSelectedLength = false;

		$lengthElement.empty();

		$.each( wcsatt_admin_params.subscription_lengths[ $periodSelector.val() ], function( length, description ) {
			if ( parseInt( length, 10 ) == 0 || 0 == ( parseInt( length, 10 ) % billingInterval ) ) {
				$lengthElement.append( $( '<option></option>' ).attr( 'value', length ).text( description ) );
			}
		} );

		$lengthElement.children( 'option' ).each( function() {
			if ( this.value == selectedLength ) {
				hasSelectedLength = true;
				return false;
			}
		} );

		if ( hasSelectedLength ) {
			$lengthElement.val( selectedLength );
		} else {
			$lengthElement.val( 0 );
		}
	};

	// Cart level settings.
	if ( wcsatt_admin_params.post_id === '' ) {

		$wcsatt_data_tab.on( 'click', 'h3', function() {

			var p = $( this ).closest( '.wc-metabox' );
			var c = p.find( '.wc-metabox-content' );

			if ( p.hasClass( 'closed' ) ) {
				c.show();
			} else {
				c.hide();
			}

			p.toggleClass( 'closed' );

		} );

		$wcsatt_data_tab.find( '.wc-metabox' ).each( function() {

			var p = $( this );
			var c = p.find( '.wc-metabox-content' );

			if ( p.hasClass( 'closed' ) ) {
				c.hide();
			}
		} );

	}

	$.fn.wcsatt_refresh_sync_options = function() {

		var $this                   = $( this ),
		    $periodSelector         = $this.find( '.wc_input_subscription_period' ),
		    $syncOptions            = $this.find( '.subscription_sync' ),
		    $syncAnnualContainer    = $syncOptions.find( '.subscription_sync_annual' ),
		    $syncWeekMonthContainer = $syncOptions.find( '.subscription_sync_week_month' ),
		    $syncWeekMonthSelect    = $syncWeekMonthContainer.find( 'select' ),
		    $syncMonthSelect        = $syncAnnualContainer.find( 'select' ),
		    billingPeriod           = $periodSelector.val();

		if ( 'day' === billingPeriod ) {

			$syncOptions.hide();
			$syncWeekMonthSelect.val( 0 );
			$syncMonthSelect.val( 0 ).change();

		} else {

			$syncOptions.show();

			if ( 'year' === billingPeriod ) {

				$syncWeekMonthContainer.hide();
				$syncWeekMonthSelect.val( 0 );
				$syncAnnualContainer.show();

			} else {

				$syncAnnualContainer.hide();
				$syncMonthSelect.val( 0 ).change();
				$syncWeekMonthSelect.empty();

				$.each( WCSubscriptions.syncOptions[ billingPeriod ], function( key, description ) {
					if ( ! key ) {
						description = wcsatt_admin_params.i18n_do_no_sync;
					}
					$syncWeekMonthSelect.append( $('<option></option>' ).attr( 'value', key ).text( description ) );
				} );

				$syncWeekMonthContainer.show();
			}
		}
	};

	function update_general_options() {

		if ( $wcsatt_schemes.length > 0 ) {

			$wcsatt_options_one_off.prop( 'disabled', false );
			$wcsatt_options_default.prop( 'disabled', false );
			$wcsatt_options_prompt.prop( 'disabled', false );

		} else {

			$wcsatt_options_one_off.prop( 'checked', true ).change();
			$wcsatt_options_one_off.prop( 'disabled', true );
			$wcsatt_options_default.prop( 'disabled', true );
			$wcsatt_options_prompt.prop( 'disabled', true );
		}

		toggle_layout_select();
	}

	// Updates '#wcsatt_data' classes.
	function update_panel_classes() {

		if ( $wcsatt_schemes.length > 0 ) {
			$wcsatt_data_tab.removeClass( 'planless' );
		} else {
			$wcsatt_data_tab.addClass( 'planless' );
		}

		if ( $wcsatt_schemes.length > 1 ) {
			$wcsatt_data_tab.addClass( 'more_than_one_plan' );
		} else {
			$wcsatt_data_tab.removeClass( 'more_than_one_plan' );
		}

		if ( $wcsatt_options_one_off.is( ':checked' ) ) {
			$wcsatt_data_tab.addClass( 'onetime_enabled' );
		} else {
			$wcsatt_data_tab.removeClass( 'onetime_enabled' );
		}
	}

	// Populate type-specific inputs.
	function initialize_type_dependent_scheme_inputs() {

		if ( $wcsatt_schemes.length > 0 ) {
			$wcsatt_schemes.wcsatt_init_type_dependent_inputs();
		}
	}

	// Toggle one-time shipping. Shows the one time shipping option only if the product contains subscription schemes.
	function toggle_one_time_shipping() {

		var product_type  = $( 'select#product-type' ).val(),
			schemes_count = $wcsatt_schemes.length;

		if ( 'subscription' !== product_type && 'variable-subscription' !== product_type ) {
			if ( schemes_count > 0 ) {
				$( '.subscription_one_time_shipping' ).show();
			} else {
				$( '.subscription_one_time_shipping' ).hide();
			}
		}
	}

	// Conditionaly change layout icons based on current subscription schemes configuration.
	function initialize_layout_select() {

		var $layouts = $wcsatt_options_layout.find( 'li' );

		$layouts.on( 'click', function() {

			if ( $wcsatt_options_layout.hasClass( 'disabled' ) ) {
				return false;
			}

			var $layout = $( this ),
				$input  = $layout.find( 'input' );

			$input.prop( 'checked', true );

			// Toggle classes.
			$layouts.removeClass( 'selected' );
			$layout.addClass( 'selected' );
		} );

		$wcsatt_options_one_off.on( 'change', function() {
			toggle_layout_select();
		} );
	}

	initialize_layout_select();

	// Toggle disabled status of layout select.
	function toggle_layout_select() {

		$wcsatt_options_layout.removeClass( 'disabled' );

		if ( 1 === $wcsatt_schemes.length && ! $wcsatt_options_one_off.is( ':checked' ) ) {
			$wcsatt_options_layout.addClass( 'disabled' );
		} else if ( 0 === $wcsatt_schemes.length && $wcsatt_options_one_off.is( ':checked' ) ) {
			$wcsatt_options_layout.addClass( 'disabled' );
		}
	}

	function init_nux() {

	if ( 'yes' === wcsatt_admin_params.is_onboarding ) {
			setTimeout( function() {
				$( '.satt_options a' ).trigger( 'click' );
			}, 500 );
		}
	}

	init_nux();

	// Trigger one-time shipping option toggle when switching product type.
	$( 'select#product-type' )

		.change( function() {

			initialize_type_dependent_scheme_inputs();
			update_panel_classes();
			update_general_options();
			toggle_one_time_shipping();

		} ).change();

	// Added/removed schemes?
	$wcsatt_data_tab

		.on( 'woocommerce_subscription_schemes_changed', function() {

			$wcsatt_schemes = $wcsatt_schemes_wrapper.find( '.subscription_scheme' );

			update_scheme_indexes();
			update_panel_classes();
			update_general_options();
			toggle_one_time_shipping();

		} )

		// Remove onboarding elements when adding component.
		.one( 'woocommerce_subscription_scheme_added', function() {
			$wcsatt_data_tab.removeClass( 'onboarding' );
		} );

	$wcsatt_schemes_wrapper

		// Toggle suitable price override method fields.
		.on( 'change', 'select.subscription_pricing_method_input', function() {

			var $this           = $( this ),
			    $product_data   = $this.closest( '.subscription_scheme_product_data' ),
			    override_method = $this.val();

			$product_data.find( '.subscription_pricing_method' ).hide();
			$product_data.find( '.subscription_pricing_method_' + override_method ).show();

		} )

		// Update subscription ranges when subscription period or interval is changed.
		.on( 'change', '.wc_input_subscription_period', function() {

			var $parent = $( this ).closest( '.subscription_scheme' );

			$parent.wcsatt_refresh_scheme_lengths();
			$parent.wcsatt_refresh_sync_options();
		} )

		// Update days in month when month changes.
		.on( 'change', '.wc_input_subscription_payment_sync_date_month', function() {

			var $parent = $( this ).closest( '.subscription_scheme' );

			$parent.wcsatt_refresh_days_in_month();
		} );

	$wcsatt_data_tab

		// Remove.
		.on( 'click', 'span.scheme-remove', function() {

			var $parent = $( this ).closest( '.subscription_scheme' );

			$parent.find( '*' ).off();
			$parent.remove();

			$wcsatt_data_tab.trigger( 'woocommerce_subscription_schemes_changed' );

			return false;
		} )

		// Remove all.
		.on( 'click', '.remove_all', function() {

			if ( $wcsatt_data_tab.hasClass( 'no_plans' ) ) {
				return false;
			}

			$wcsatt_schemes.each( function( index, el ) {

				var $scheme = $( el );

				$scheme.find( '*' ).off();
				$scheme.remove();

			} );

			$wcsatt_data_tab.trigger( 'woocommerce_subscription_schemes_changed' );

			return false;
		} )

		// Add.
		.on( 'click', 'button.add_subscription_scheme', function () {

			$wcsatt_data_tab.block( wcsatt_block_params );

			wcsatt_schemes_count++;

			var data = {
				action:  'wcsatt_add_subscription_scheme',
				post_id:  wcsatt_admin_params.post_id,
				index:    wcsatt_schemes_count,
				security: wcsatt_admin_params.add_subscription_scheme_nonce
			};

			$.post( wcsatt_admin_params.wc_ajax_url, data, function ( response ) {

				// Append markup.
				$wcsatt_schemes_wrapper.append( response.markup );

				var $added_scheme = $wcsatt_schemes_wrapper.find( '.subscription_scheme' ).last();

				// Run scripts against added markup.
				$added_scheme.wcsatt_init_type_dependent_inputs();
				$added_scheme.wcsatt_init_help_tips();

				// Trigger 'change' event to show/hide price override method options.
				$added_scheme.find( 'select.subscription_pricing_method_input' ).change();

				$wcsatt_data_tab.unblock();

				$wcsatt_data_tab.trigger( 'woocommerce_subscription_scheme_added', response );

				$wcsatt_data_tab.trigger( 'woocommerce_subscription_schemes_changed' );

			}, 'json' );

			return false;
		} );

	// Hide "default to" option when "force subscription" is checked.
	$wcsatt_options_one_off

		.on( 'change', function() {

			if ( $( this ).is( ':checked' ) ) {
				$wcsatt_data_tab.find( '.wcsatt_default_status' ).show();
			} else {
				$wcsatt_data_tab.find( '.wcsatt_default_status' ).hide();
			}

			update_panel_classes();

		} ).change();


	// NYP compatibility.
	$( '#_nyp' )

		.on( 'change', function() {

			if ( $( this ).is( ':checked' ) ) {
				$wcsatt_schemes_wrapper.addClass( 'is_nyp' );
			} else {
				$wcsatt_schemes_wrapper.removeClass( 'is_nyp' );
			}

		} ).change();

	// Init metaboxes.
	init_subscription_schemes_metaboxes();

	function update_scheme_indexes() {

		$wcsatt_schemes.each( function( index, el ) {
			$( '.position', el ).val( parseInt( $( el ).index( '.subscription_schemes .subscription_scheme' ), 10 ) );
			$( el ).attr( 'rel', index );
		} );
	}

	function init_subscription_schemes_metaboxes() {

		$wcsatt_schemes_wrapper.find( 'select.subscription_pricing_method_input' ).change();
		$wcsatt_schemes_wrapper.find( 'select.wc_input_subscription_payment_sync_date_month[value!="0"]' ).change();

		// Initial order.
		var subscription_schemes = $wcsatt_schemes.get();

		subscription_schemes.sort( function( a, b ) {

		var compA = parseInt( $(a).attr( 'rel' ), 10 ),
			compB = parseInt( $(b).attr( 'rel' ), 10 );

		   return ( compA < compB ) ? -1 : ( compA > compB ) ? 1 : 0;
		} );

		$( subscription_schemes ).each( function( idx, itm ) {
			$wcsatt_schemes_wrapper.append( itm );
		} );

		// Component ordering.
		$wcsatt_schemes_wrapper.sortable( {
			items:                '.subscription_scheme',
			cursor:               'move',
			axis:                 'y',
			handle:               'span.scheme-handle',
			scrollSensitivity:    40,
			forcePlaceholderSize: true,
			helper:               'clone',
			opacity:              0.65,
			placeholder:          'wc-metabox-sortable-placeholder',
			start:function( event,ui ){
				ui.item.css( 'background-color','#f6f6f6' );
			},
			stop:function( event,ui ){
				ui.item.removeAttr( 'style' );
				update_scheme_indexes();
			}
		} );
	}

} );
