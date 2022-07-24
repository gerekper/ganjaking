<?php

    /**
     * WC_Gateway_Worldwide_Payment_Gateway class.
     *
     * @extends WC_Payment_Gateway
     */
	class WC_Gateway_Worldwide_Payment_Gateway extends WC_Payment_Gateway {

		/**
	 	 * Get all the options and constants
	 	 *
		 * [__construct description]
		 */
		public function __construct() {

			$this->id							= 'woowpwpg';
			$this->method_title 				= __('WorldPay Worldwide Payment Gateway', 'woocommerce_worlday');
			$this->icon 						= apply_filters( 'wc_worldpay_wpg_icon', '' );
			$this->has_fields 					= true;

			// Default values
			$this->default_enabled				= 'no';
			$this->default_title 				= __('Pay with Worldpay', 'woocommerce_worlday');
			$this->default_description  		= __('Credit Card via Worldpay', 'woocommerce_worlday');
			$this->default_order_button_text  	= __('Pay securely with Worldpay', 'woocommerce_worlday');
  			$this->default_status				= 'testing';
  			$this->default_debug 				= 'no';
  			$this->default_submission 			= 'direct';
  			$this->default_payment_methods 		= '';
  			$this->default_applepay				= 'no';
  			$this->default_googlepay			= 'no';

			// Load the settings.
			$this->init_settings();

			// Load the form fields
			$this->init_form_fields();

			// Get setting values
			$this->enabled						= isset( $this->settings['enabled'] ) && $this->settings['enabled'] == 'yes' ? 'yes' : $this->default_enabled;
			$this->title 						= isset( $this->settings['title'] ) ? $this->settings['title'] : $this->default_title;
			$this->description  				= isset( $this->settings['description'] ) ? $this->settings['description'] : $this->default_description;
			$this->order_button_text  			= isset( $this->settings['order_button_text'] ) ? $this->settings['order_button_text'] : $this->default_order_button_text;
  			$this->status						= isset( $this->settings['status'] ) ? $this->settings['status'] : $this->default_status;

			// Logs transactions, always on for testing.
			$this->debug						= isset( $this->settings['debug'] ) && $this->settings['debug'] == 'yes' ? true : $this->default_debug;

			// Hooks
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'woocommerce_api_' . strtolower( get_class( $this ) ), array( $this, 'check_worldpay_response' ), 0 );

			$this->supports = array(
				'products'
			);

			// Logs
			if ( $this->debug == 'yes' ) {
				$this->log = new WC_Logger();
			}

			// Add test card info to the description if in test mode
			if ( $this->status == 'testing' ) {
				$this->description .= ' ' . __( '<br />TEST MODE ENABLED.<br />In test mode, you can use Visa card number 4111 1111 1111 1111 with any CVC and a valid expiration date.', 'woocommerce_worlday' );
				$this->description  = trim( $this->description );
			}

		} // END __construct

		/**
    	 * Initialise Gateway Form Fields
    	 *
    	 * [init_form_fields description]
    	 * @return [type]
    	 */
    	function init_form_fields() {
    		// include( WORLDPAYPLUGINPATH . 'includes/worldpay-wpg-admin.php' );
    	}

		/**
 		 * Admin Panel Options
		 * - Options for bits like 'title' and availability on a country-by-country basis
		 *
		 * [admin_options description]
		 * @return [type]
		 */
		public function admin_options() {
			include( WORLDPAYPLUGINPATH . 'includes/worldpay-wpg-admin.php' );
			?>
			<table class="form-table">
			<?php
				// Generate the HTML for the settings form.
				$this->generate_settings_html();
			?>
			</table><!--/.form-table-->
			<?php

		} // END admin_options


		/**
 		 * There are no payment fields for WorldPay, but we want to show the description if set.
 		 *
		 * [payment_fields description]
		 * @return [type]
		 */
		function payment_fields() {

			if ( $this->description ) {
				echo wpautop( wptexturize($this->description) );
			}

		} // END payment_fields

	} // END CLASS