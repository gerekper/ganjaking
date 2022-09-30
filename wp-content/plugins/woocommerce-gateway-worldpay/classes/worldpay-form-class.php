<?php

    /**
     * WC_Gateway_WorldPay_Form class.
     *
     * @extends WC_Payment_Gateway
     */
	class WC_Gateway_Worldpay_Form extends WC_Payment_Gateway {

		/**
	 	 * Get all the options and constants
	 	 *
		 * [__construct description]
		 */
		public function __construct() {

			$this->id							= 'worldpay';
			$this->method_title 				= __('WorldPay Form', 'woocommerce_worlday');
			$this->icon 						= apply_filters( 'wc_worldpay_icon', '' );
			$this->has_fields 					= false;

			// Default values
			$this->default_enabled				= 'no';
			$this->default_title 				= __('Pay with WorldPay', 'woocommerce_worlday');
			$this->default_description  		= __('Credit Card via WorldPay', 'woocommerce_worlday');
			$this->default_order_button_text  	= __('Pay securely with WorldPay', 'woocommerce_worlday');
  			$this->default_status				= 'testing';
  			$this->default_wplogo				= 'no';
  			$this->default_vmelogo				= 'no';
  			$this->default_cardtypes			= '';
			$this->default_instId				= '';
			$this->default_callbackPW			= '';
			$this->default_orderDesc			= __('Order {ordernum} with ', 'woocommerce_worlday') .  get_bloginfo('name');
			$this->default_accid				= '';
			$this->default_authMode				= 'A';
			$this->default_fixContact			= 'yes';
			$this->default_hideContact			= 'yes';
			$this->default_hideCurrency			= 'yes';
			$this->default_lang					= 'yes';
			$this->default_noLanguageMenu		= 'yes';
			$this->default_remoteid				= '';
			$this->default_remotepw				= '';
			$this->default_worldpaymd5			= $this->generate_md5();
			$this->default_signaturefields		= 'instId:amount:currency:cartId';
			$this->default_debug 				= false;
			$this->default_dynamiccallback 		= false;
			$this->default_submission			= 'form';
			$this->default_method 				= 'alltransactions';
			$this->default_addgautm 			= 'no';
			$this->default_withDelivery 		= 'no';

			// Load the settings.
			$this->init_settings();

			// Backwards compatibilty
			// Use old default fields if site is already using MD5
			if( isset( $this->settings['worldpaymd5'] ) && $this->settings['worldpaymd5'] != '' ) {
				$this->default_signaturefields = 'instId:amount:currency:cartId:name:email:address1:postcode';
			}

			// Load the form fields
			$this->init_form_fields();

			// Get setting values
			$this->enabled						= isset( $this->settings['enabled'] ) && $this->settings['enabled'] == 'yes' ? 'yes' : $this->default_enabled;
			$this->title 						= isset( $this->settings['title'] ) ? $this->settings['title'] : $this->default_title;
			$this->description  				= isset( $this->settings['description'] ) ? $this->settings['description'] : $this->default_description;
			$this->order_button_text  			= isset( $this->settings['order_button_text'] ) ? $this->settings['order_button_text'] : $this->default_order_button_text;
  			$this->status						= isset( $this->settings['status'] ) ? $this->settings['status'] : $this->default_status;
  			$this->wplogo						= isset( $this->settings['wplogo'] ) && $this->settings['wplogo'] == 'yes' ? 'yes' : $this->default_wplogo;
  			$this->vmelogo						= isset( $this->settings['vmelogo'] ) && $this->settings['vmelogo'] == 'yes' ? 'yes' : $this->default_vmelogo;
  			$this->cardtypes					= isset( $this->settings['cardtypes'] ) ? $this->settings['cardtypes'] : $this->default_cardtypes;
			$this->instId						= isset( $this->settings['instId'] ) ? $this->settings['instId'] : $this->default_instId;
			$this->callbackPW					= isset( $this->settings['callbackPW'] ) ? $this->settings['callbackPW'] : $this->default_callbackPW;
			$this->orderDesc					= isset( $this->settings['orderDesc'] ) ? $this->settings['orderDesc'] : $this->default_orderDesc;
			$this->accid						= isset( $this->settings['accid'] ) ? $this->settings['accid'] : $this->default_accid;
			$this->authMode						= isset( $this->settings['authMode'] ) ? $this->settings['authMode'] : $this->default_authMode;
			$this->fixContact					= isset( $this->settings['fixContact'] ) && $this->settings['fixContact'] == 'no' ? 'no' : $this->default_fixContact;
			$this->hideContact					= isset( $this->settings['hideContact'] ) && $this->settings['hideContact'] == 'no' ? 'no' : $this->default_hideContact;
			$this->hideCurrency					= isset( $this->settings['hideCurrency'] ) && $this->settings['hideCurrency'] == 'no' ? 'no' : $this->default_hideCurrency;
			$this->lang							= isset( $this->settings['lang'] ) && $this->settings['lang'] == 'no' ? 'no' : $this->default_lang;
			$this->noLanguageMenu				= isset( $this->settings['noLanguageMenu'] ) && $this->settings['noLanguageMenu'] == 'no' ? 'no' : $this->default_noLanguageMenu;
			$this->remoteid						= isset( $this->settings['remoteid'] ) ? $this->settings['remoteid'] : $this->default_remoteid;
			$this->remotepw						= isset( $this->settings['remotepw'] ) ? $this->settings['remotepw'] : $this->default_remotepw;
			$this->worldpaymd5					= isset( $this->settings['worldpaymd5'] ) ? $this->settings['worldpaymd5'] : $this->default_worldpaymd5;
			$this->signaturefields				= isset( $this->settings['signaturefields'] ) ? $this->settings['signaturefields'] : $this->default_signaturefields;
			$this->dynamiccallback				= isset( $this->settings['dynamiccallback'] ) && $this->settings['dynamiccallback'] == 'yes' ? true : $this->default_dynamiccallback;
			$this->submission					= isset( $this->settings['submission'] ) ? $this->settings['submission'] : $this->default_submission;
			$this->method						= isset( $this->settings['method'] ) ? $this->settings['method'] : $this->default_method;
			$this->addgautm						= isset( $this->settings['addgautm'] ) ? $this->settings['addgautm'] : $this->default_addgautm;

			// Logs transactions
			$this->debug						= isset( $this->settings['debug'] ) && $this->settings['debug'] == 'yes' ? true : $this->default_debug;

			// emails someone in the event of a problem with a cancellation or refund or pre-auth
			$this->worldpaydebug				= 'yes';
			$this->worldpaydebugemail			= isset( $this->settings['worldpaydebugemail'] ) ? $this->settings['worldpaydebugemail'] : get_bloginfo('admin_email');

			$this->clean_array					= array( '<', '>', '&', "'", '"' );

			// Deactivate on checkout if $this->method != alltransactions
			if( is_checkout() && $this->method != 'alltransactions' ) {
				$this->enabled = 'no';
			}

			// Hooks
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'woocommerce_api_' . strtolower( get_class( $this ) ), array( $this, 'check_worldpay_response' ), 0 );

			// Old way, uses wpcallback.php
			add_action( 'valid-worldpay-request', array( $this, 'successful_request' ) );

			// New way, no extra files!
			add_action( 'valid-wpform-request', array( $this, 'successful_wpform_request' ) );

			// The receipt page is only needed for form submissions, 
			if ( $this->submission == 'form' ) {
				add_action( 'woocommerce_receipt_worldpay', array( $this, 'receipt_page' ) );
			}

			// Redirect to thankyou page
			add_action( 'woocommerce_payment_complete', array( $this, 'redirect' ) );

			// When a subscriber or store manager changes a subscription's status in the store, change the status with WorldPay
			add_action( 'woocommerce_subscription_status_cancelled', array( $this, 'cancel_subscription_with_worldpay' ) );
			add_action( 'woocommerce_subscription_cancelled_worldpay', array( $this, 'cancel_subscription_with_worldpay') );

			// Remove subs support if $this->dynamiccallback is TRUE or remote ID is not set
			if( $this->dynamiccallback || $this->remoteid == '' ) {

				$this->supports = array(
					'products'
				);

			} else {

				$this->supports = array(
					'products',
					'subscriptions',
					'gateway_scheduled_payments',
					'subscription_cancellation',
					'refunds',
					'subscription_amount_changes'
				);	

			}

			// Logs
			if ( $this->debug ) {
				$this->log = new WC_Logger();
			}

			// Add test card info to the description if in test mode
			if ( $this->status == 'testing' ) {
				$this->description .= ' ' . __( '<br />TEST MODE ENABLED.<br />In test mode, you can use Visa card number 4111111111111111 with any CVC and a valid expiration date.', 'woocommerce_worlday' );
				$this->description  = trim( $this->description );
			}

		} // END __construct

		protected static function liveurl() {
			return 'https://secure.worldpay.com/wcc/purchase';
		}

		protected static function testurl() {
			return 'https://secure-test.worldpay.com/wcc/purchase';
		}

		protected static function status() {
			$settings = get_option( 'woocommerce_worldpay_settings' );
			return $settings['status'];
		}

		protected static function debug() {
			$settings = get_option( 'woocommerce_worldpay_settings' );
			return $settings['debug'];
		}

		/**
    	 * Initialise Gateway Settings Form Fields
    	 *
    	 * [init_form_fields description]
    	 * @return [type]
    	 */
    	function init_form_fields() {
    		include( $this->get_plugin_path() . 'includes/worldpay-form-admin.php' );
    	} // END init_form_fields

		/**
		 * Add selected card icons to payment method label, defaults to Visa/MC/Amex/Discover
		 *
		 * [get_icon description]
		 * @return [type]
		 */
		public function get_icon() {
			global $woocommerce;

			$icon = '';

			if ( $this->icon ) {
		
				if ( get_option('woocommerce_force_ssl_checkout')=='no' ) :
					// use icon provided by filter
					$icon = '<img src="' . esc_url( $this->icon ) . '" alt="' . esc_attr( $this->title ) . '" />';			
				else :
					// use icon provided by filter
					$icon = '<img src="' . esc_url( WC_HTTPS::force_https_url( $this->icon ) ) . '" alt="' . esc_attr( $this->title ) . '" />';		
				endif;

			} elseif ( ! empty( $this->cardtypes ) ) {

				if ( get_option('woocommerce_force_ssl_checkout')=='no' ) {

					// display icons for the selected card types
					foreach ( $this->cardtypes as $card_type ) {

						$icon .= '<img src="' . 
									esc_url( $this->get_plugin_url() . '/images/card-' . 
									strtolower( str_replace(' ','-',$card_type) ) . '.png' ) . '" alt="' . 
									esc_attr( strtolower( $card_type ) ) . '" />';
					}

				} else {

					// display icons for the selected card types
					foreach ( $this->cardtypes as $card_type ) {

						$icon .= '<img src="' . 
									esc_url( WC_HTTPS::force_https_url( $this->get_plugin_url() ) . '/images/card-' . 
									strtolower( str_replace(' ','-',$card_type) ) . '.png' ) . '" alt="' . 
									esc_attr( strtolower( $card_type ) ) . '" />';
					}

				}

			}

			/**
			 * Add Payments V.me logo
			 */
			if ( $this->vmelogo == 'yes' ) {

				if ( get_option('woocommerce_force_ssl_checkout')=='no' ) {
					// use icon provided by filter
					$icon = '<img src="' . esc_url( $this->get_plugin_url() . '/images/vme.png' ) . '" alt="v.me with WorldPay" />' . $icon;			
				} else {
					// use icon provided by filter
					$icon = '<img src="' . esc_url( WC_HTTPS::force_https_url( $this->get_plugin_url() . '/images/vme.png' ) ) . '" alt="v.me with WorldPay" />' . $icon;		
				}

			}

			/**
			 * Add Payments Powered By WorldPay logo
			 */
			if ( $this->wplogo == 'yes' ) {
				if ( get_option('woocommerce_force_ssl_checkout')=='no' ) {
					// use icon provided by filter
					$icon = '<img src="' . esc_url( $this->get_plugin_url() . '/images/poweredByWorldPay.png' ) . '" alt="Payments Powered By WorldPay" />' . $icon;			
				} else {
					// use icon provided by filter
					$icon = '<img src="' . esc_url( WC_HTTPS::force_https_url( $this->get_plugin_url() . '/images/poweredByWorldPay.png' ) ) . '" alt="Payments Powered By WorldPay" />' . $icon;		
				}
			}

			return apply_filters( 'woocommerce_gateway_icon', $icon, $this->id );
		}

		/**
 		 * Admin Panel Options
		 * - Options for bits like 'title' and availability on a country-by-country basis
		 *
		 * [admin_options description]
		 * @return [type]
		 */
		public function admin_options() {
			include( $this->get_plugin_path() . 'includes/worldpay-form-admin-notice.php' );
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


		/**
		 * Generate the form button
		 * Only used for FORM submission method
		 *
		 * [generate_worldpay_form description]
		 * @param  [type] $order_id
		 * @return [type]
		 */
		public function generate_worldpay_form( $order_id ) {
			global $woocommerce;
			 
			$order = new WC_Order( $order_id );

			include_once( 'class-wc-gateway-worldpay-request.php' );
			$worldpay_args = WC_Gateway_WorldPay_Request::get_worldpay_args( $order );			

			if ( self::status() == 'testing' ) {
				$worldpayform_adr 			= self::testurl();
				$worldpay_args['testMode'] 	= '100';
			} else {
				$worldpayform_adr = self::liveurl();
				$testMode		  = '';
			}

			/**
			 * Build WorldPay Form
			 */
			$worldpayform = '';
			foreach ( $worldpay_args as $key => $value ) {

				$worldpayform .= '<input type="hidden" name="' .$key. '" 	value="' .$value. '">' . "\r\n";

			}

			wc_enqueue_js('
				jQuery(function(){
					jQuery("body").block({

						message: "<img src=\"'.WC()->plugin_url().'/assets/images/select2-spinner.gif\" alt=\"Redirecting...\" />'.__('Thank you for your order. We are now redirecting you to WorldPay to make payment.', 'woocommerce_worlday').'",
						overlayCSS:
						{
							background: "#fff",
							opacity: 0.6
						},
						css:
						{
				        	padding:        20,
					        textAlign:      "center",
					        color:          "#555",
					        border:         "3px solid #aaa",
					        backgroundColor:"#fff",
					        cursor:         "wait",
					        lineHeight:		"32px"
				    	}

					});
					jQuery("#submit_worldpay_payment_form").click();
				});
			');

			/**
			 * This is the form.
			 */
			return  '<form action="'.$worldpayform_adr.'" method="post" id="worldpay_payment_form">
					' . $worldpayform . '
					<input type="submit" class="button-alt" id="submit_worldpay_payment_form" value="'.__('Pay via WorldPay', 'woocommerce_worlday').'" /> <a class="button cancel" href="'.$order->get_cancel_order_url().'">'.__('Cancel order &amp; restore cart', 'woocommerce_worlday').'</a>
					</form>';

		} // END generate_form


		/**
		 * Process the payment and return the result
		 *
		 * [process_payment description]
		 * @param  [type] $order_id
		 * @return [type]
		 */
		function process_payment( $order_id ) {

			$order	= wc_get_order( $order_id );

			if ( $this->submission == 'form' ) {
				// FORM submission method
           		return array(
            	   	'result'    => 'success',
           			'redirect'	=> $order->get_checkout_payment_url( true )
           		);

           	} else {
           		// URL submission method
           		include_once( 'class-wc-gateway-worldpay-request.php' );
           		$worldpay_request = new WC_Gateway_WorldPay_Request( $this );

           		return array(
					'result'   => 'success',
					'redirect' => $worldpay_request->get_request_url( $order, self::status() )
				);

           	}

		} // END process_payment

		/**
		 * receipt_page
		 *
		 * [receipt_page description]
		 * @param  [type] $order
		 * @return [type]
		 */
		function receipt_page( $order ) {

			echo '<p>'.__('Thank you for your order, please click the button below to pay with WorldPay.', 'woocommerce_worlday').'</p>';
			echo $this->generate_worldpay_form( $order );

		} // END receipt_page

		/**
		 * Check for WorldPay Response
 		 *
 		 * [check_worldpay_response description]
 		 * @return [type]
 		 */
		public function check_worldpay_response() {
			global $woocommerce;

			ob_clean();
			header( "HTTP/1.0 200 OK");
			ob_end_flush();

			// The new way!
			if ( $this->check_worldpay_request_is_valid( $_REQUEST ) ) {

				do_action( "valid-wpform-request", $_REQUEST );

			} 
			// The old way, this is coming in from wpcallback.php
			elseif ( isset( $_GET["order"] ) && $_GET["callback"] == 'y' ) {

				$worldpay_order 		= absint( intval( $_GET["order"] ) );

				$worldpaycrypt_b64		= get_post_meta( $worldpay_order, '_worldpay_crypt', TRUE );
				$worldpaycrypt_b64 		= base64_decode( $worldpaycrypt_b64 );
				$worldpaycrypt_b64 		= $this->worldpaysimpleXor( $worldpaycrypt_b64, $this->callbackPW );
				$worldpay_return_values = $this->getTokens( $worldpaycrypt_b64 );

				if ( isset($worldpay_return_values['transId']) ) :
        			do_action( "valid-worldpay-request", $worldpay_return_values );
				endif;

   			} else {

   				if ( isset($_REQUEST["MC_FailureURL"]) ) {
					$url = $_REQUEST["MC_FailureURL"];

					if( $this->addgautm	&& $this->addgautm == 'yes' ) {
						$url = add_query_arg( array(
				                        'utm_nooverride' => 1
				                    ), $url );
					}
			
					echo "<meta http-equiv='Refresh' content='1; Url=\"$url\"'>";
					exit;
   				} else {
   					wp_die( "WorldPay Return Failure", "WorldPay", array( 'response' => 200 ) );
   				}

			}

			$url = $this->get_return_url( $order );
        	
        	if( $this->addgautm && $this->addgautm == 'yes' ) {
				$url = add_query_arg( array(
		                        'utm_nooverride' => 1
		                    ), $url );
			}

			wp_redirect( $url );
			exit;

		} // END check_worldpay_response

		/**
		 * Successful Payment!
		 * Old method, uses wpcallback.php
 		 *
 		 * [successful_request description]
 		 * @param  [type] $worldpay_return_values
 		 * @return [type]
 		 */
		function successful_request( $worldpay_return_values ) {
			
			$order 	 = new WC_Order( (int) $worldpay_return_values['order'] );

			$this->update_order_notes( $order, $worldpay_return_values );

			/**
			 * Check MC_transactionNumber
			 * if this is 1 then this is either the first transaction for a subscription
			 * or the only transction for a none subscription order
			 */
			if ( $worldpay_return_values['MC_transactionNumber'] == '1' ) {
			
				// Normal transaction at the front end
	        	$order->payment_complete( $worldpay_return_values['transId'] );
	        	// Clear the cart, just in case
	        	WC()->cart->empty_cart();

	        	$url = $this->get_return_url( $order, $worldpay_return_values );

	        	if( $this->addgautm	&& $this->addgautm == 'yes'	) {
					$url = add_query_arg( array(
			                        'utm_nooverride' => 1
			                    ), $url );
				}

				wp_redirect( $url );
				exit;
				
			}

		} // END successful_request

		/**
		 * Successful Payment!
		 * New method
 		 *
 		 * [successful_wpform_request description]
 		 * @param  [type] $worldpay_return_values
 		 * @return [type]
 		 */
		function successful_wpform_request( $worldpay_return_values ) {
			
			$order 	 = new WC_Order( (int) $worldpay_return_values['MC_order'] );

			$this->update_order_notes( $order, $worldpay_return_values );

			/**
			 * Check MC_transactionNumber
			 * if this is 1 then this is either the first transaction for a subscription
			 * or the only transction for a none subscription order
			 */
			if ( $worldpay_return_values['MC_transactionNumber'] == '1' ) {

				// Normal transaction at the front end
	        	$order->payment_complete( $worldpay_return_values['transId'] );
	        	// Clear the cart, just in case
	        	WC()->cart->empty_cart();
	        	exit;
				
			}

		} // END successful_wpform_request

		/**
		 * Update the order notes with all the transaction informations
 		 */
		function update_order_notes( $order, $worldpay_return_values ) {	
			global $woocommerce;

        	$order_id   = $order->get_id();

			/**
			 * Make sure the order notes contain the FuturePayID
			 * and add it as post_meta so we can find it easily when WorldPay sends 
			 * updates about payments / cancellations etc
			 */
			$orderNotes  = ''; 
			if ( function_exists( 'wcs_order_contains_subscription' ) && wcs_order_contains_subscription( $order ) ) {

				$orderNotes .=	'<br /><!-- FUTURE PAY-->';
				$orderNotes .=	'<br />FuturePayID : ' 	. $worldpay_return_values['futurePayId'];
				$orderNotes .=	'<br /><!-- FUTURE PAY-->';
				
				update_post_meta( $order_id, '_futurepayid', $worldpay_return_values['futurePayId'] );

			}

			$orderNotes .=	'<br />transId : ' 			. $worldpay_return_values['transId'];
			$orderNotes .=	'<br />transStatus : ' 		. $worldpay_return_values['transStatus'];
			$orderNotes .=	'<br />transTime : '		. $worldpay_return_values['transTime'];
			$orderNotes .=	'<br />authAmount : ' 		. $worldpay_return_values['authAmount'];
			$orderNotes .=	'<br />authCurrency : ' 	. $worldpay_return_values['authCurrency'];
			$orderNotes .=	'<br />rawAuthMessage : ' 	. $worldpay_return_values['rawAuthMessage'];
			$orderNotes .=	'<br />rawAuthCode : ' 		. $worldpay_return_values['rawAuthCode'];
			$orderNotes .=	'<br />cardType : ' 		. $worldpay_return_values['cardType'];
			$orderNotes .=	'<br />countryMatch : ' 	. $worldpay_return_values['countryMatch'];
			$orderNotes .=	'<br />AVS : ' 				. $worldpay_return_values['AVS'];
			
			$order->add_order_note( __('WorldPay payment completed.' . $orderNotes, 'woocommerce_worlday') );

			// Set Transaction ID
			$order->set_transaction_id( $worldpay_return_values['transId'] );

		} // END update_order_notes

		/**
		 * Validate WorldPay Response
 		 *
 		 * [check_worldpay_response description]
 		 * @return [type]
 		 */
		public function check_worldpay_request_is_valid( $worldpay_response ) {
			global $woocommerce, $wpdb;

			if ( $this->debug == true ) {
   				$this->log->add( $this->id, __('WorldPay Response', 'woocommerce_worlday') . '');
   				$this->log->add( $this->id, '====================================' );
   				$this->log->add( $this->id, print_r( str_replace( array("\r\n", "\r", "\n", "<br />"), " ", $worldpay_response ), TRUE ) );
   				$this->log->add( $this->id, '====================================' );
   			}

   			// Add the full return to the order meta, just in case
   			if( isset( $worldpay_response["MC_order"] ) ) {
				update_post_meta( $worldpay_response["MC_order"], '_worldpay_response', $worldpay_response );
			}

			$order 				  = '';
			$transId 			  = '';
			$transStatus 		  = '';
			$transTime 			  = '';
			$authAmount 		  = '';
			$authCurrency 		  = '';
			$rawAuthMessage 	  = '';
			$rawAuthCode 		  = '';
			$callbackPW 		  = '';
			$cardType 			  = '';
			$countryMatch 		  = '';
			$AVS 				  = '';			
			$url 				  = '';
			$failurl 			  = '';
			$MC_transactionNumber = '';
			$futurePayId		  = '';
			$futurePayStatusChange= '';

			if ( (isset($worldpay_response["transId"]) && $worldpay_response["transStatus"]=='Y') || (isset($worldpay_response["futurePayId"]) && $worldpay_response["transStatus"]=='Y') ) :

				$settings_callbackPW  = $this->callbackPW;

				$order 				  = isset( $worldpay_response["MC_order"] ) ? wc_clean( $worldpay_response["MC_order"] ) : '';
				$transId 			  = isset( $worldpay_response["transId"] ) ? wc_clean( $worldpay_response["transId"] ) : '';
				$transStatus 		  = isset( $worldpay_response["transStatus"] ) ? wc_clean( $worldpay_response["transStatus"] ) : '';
				$transTime 			  = isset( $worldpay_response["transTime"] ) ? wc_clean( $worldpay_response["transTime"] ) : ''; // (UnixTime)
				$authAmount 		  = isset( $worldpay_response["authAmount"] ) ? wc_clean( $worldpay_response["authAmount"] ) : '';
				$authCurrency 		  = isset( $worldpay_response["authCurrency"] ) ? wc_clean( $worldpay_response["authCurrency"] ) : '';
				$rawAuthMessage 	  = isset( $worldpay_response["rawAuthMessage"] ) ? wc_clean( $worldpay_response["rawAuthMessage"] ) : '';
				$rawAuthCode 		  = isset( $worldpay_response["rawAuthCode"] ) ? wc_clean( $worldpay_response["rawAuthCode"] ) : '';
				$callbackPW 		  = isset( $worldpay_response["callbackPW"] ) ? wc_clean( $worldpay_response["callbackPW"] ) : '';
				$cardType 			  = isset( $worldpay_response["cardType"] ) ? wc_clean( $worldpay_response["cardType"] ) : '';
				$countryMatch 		  = isset( $worldpay_response["countryMatch"] ) ? wc_clean( $worldpay_response["countryMatch"] ) : '';
				$AVS 				  = isset( $worldpay_response["AVS"] ) ? wc_clean( $worldpay_response["AVS"] ) : '';		
				$url 				  = isset( $worldpay_response["MC_SuccessURL"] ) ? wc_clean( $worldpay_response["MC_SuccessURL"] ) : '';
				$failurl 			  = isset( $worldpay_response["MC_FailureURL"] ) ? wc_clean( $worldpay_response["MC_FailureURL"] ) : '';
				$MC_transactionNumber = isset( $worldpay_response["MC_transactionNumber"] ) ? wc_clean( $worldpay_response["MC_transactionNumber"] ) : '';

				if ( isset($worldpay_response["futurePayId"]) ) {
					$futurePayId		  = wc_clean( $worldpay_response["futurePayId"] );
				}

				if ( isset($worldpay_response["futurePayStatusChange"]) ) {
					$futurePayStatusChange= wc_clean( $worldpay_response["futurePayStatusChange"] );
				}

				/**
				 * Process Subscription orders here
				 *
				 * MC_transactionNumber is only set for normal orders
				 */
				if ( !$MC_transactionNumber || $MC_transactionNumber == '' ) :
					// Get the order id based on the futurepayid
					$orderid = $wpdb->get_row("SELECT post_id FROM $wpdb->postmeta 
												WHERE meta_key = '_futurepayid' 
												AND meta_value = '".$futurePayId."'
												ORDER BY post_id ASC 
												LIMIT 1"
											 );
					
					// Make sure there is a row from the DB
					if( NULL !== $orderid ) {
											 
						$order 	 = new WC_Order( (int) $orderid->post_id );

						$order_id   = $order->get_id();

						// Some kind of subscription update
						if ( (class_exists( 'WC_Subscriptions' ) && $futurePayStatusChange == 'Merchant Cancelled') || (class_exists( 'WC_Subscriptions' ) && $futurePayStatusChange == 'Customer Cancelled') ) {
							$order->add_order_note( __('WorldPay Subscription Notice : ' . $futurePayStatusChange, 'woocommerce_worlday') );
							// Cancel the subscription
							WC_Subscriptions_Manager::cancel_subscriptions_for_order( $order_id );
						}
						
						if ( $rawAuthCode == 'D' && class_exists( 'WC_Subscriptions' ) ) {
							// Record failed payment
							WC_Subscriptions_Manager::process_subscription_payment_failure_on_order( $order_id );
							// Cancel the subscription
							WC_Subscriptions_Manager::cancel_subscriptions_for_order( $order_id );				
						}
						
						if ( $rawAuthCode == 'A' && function_exists( 'wcs_order_contains_subscription' ) && wcs_order_contains_subscription( $order ) ) {

							$subscription 			= wcs_get_subscriptions_for_order( $order_id );
							$subscription_id 		= key( $subscription );

							// Get Subscription object for $subscription_id
							$subscription 			= wcs_get_subscription( $subscription_id );

							// Create renewal order
							$renewal_order 			= wcs_create_renewal_order( $subscription );

							$renewal_order->payment_complete( $worldpay_response['transId'] );
							// Clear the cart, just in case
		        			WC()->cart->empty_cart();
							$renewal_order->add_order_note( __( 'WorldPay subscription payment completed.', 'woocommerce_worlday' ) );

							// Set WorldPay as the payment method (we can't use $renewal_order->set_payment_method() here as it requires an object we don't have)
							update_post_meta( $renewal_order->get_id(), '_payment_method', $this->id );
							update_post_meta( $renewal_order->get_id(), '_payment_method_title', $this->method_title );

						}

					 	/**
			 			 * Make sure the order notes contain the FuturePayID
						 * and add it as post_meta so we can find it easily when Worldpay sends 
						 * updates about payments / cancellations etc
						 */
						$orderNotes = '';
						if ( function_exists( 'wcs_order_contains_subscription' ) && wcs_order_contains_subscription( $order ) ) {
							$orderNotes .=	'<br /><!-- FUTURE PAY-->';
							$orderNotes .=	'<br />FuturePayID : ' 	. $futurePayId;
							$orderNotes .=	'<br /><!-- FUTURE PAY-->';

							update_post_meta( $order_id, '_futurepayid', $futurePayId );
						}

						$orderNotes .=	'<br />transId : ' 			. $transId;
						$orderNotes .=	'<br />transStatus : ' 		. $transStatus;
						$orderNotes .=	'<br />transTime : '		. $transTime;
						$orderNotes .=	'<br />authAmount : ' 		. $authAmount;
						$orderNotes .=	'<br />authCurrency : ' 	. $authCurrency;
						$orderNotes .=	'<br />rawAuthMessage : ' 	. $rawAuthMessage;
						$orderNotes .=	'<br />rawAuthCode : ' 		. $rawAuthCode;
						$orderNotes .=	'<br />cardType : ' 		. $cardType;
						$orderNotes .=	'<br />countryMatch : ' 	. $countryMatch;
						$orderNotes .=	'<br />AVS : ' 				. $AVS;
						
						$renewal_order->add_order_note( __('WorldPay payment completed.' . $orderNotes, 'woocommerce_worlday') ); 

					}

					return false;
					
				else:
					
					/**
					 * This is an ordinary payment, carry on
					 */
	        		return true;
					
				endif;
				
			else :

				// Transaction cancelled / failed
				return false;
				
			endif;

		}

		/**
		 * Redirect successful orders to the thank you page
		 * 
		 * @param  [type] $order [description]
		 * @return [type]        [description]
		 */
		function redirect ( $order ) {
			global $woocommerce;
			$order 	 = new WC_Order( (int) $order );

        	$payment_method = $order->get_payment_method();

			if( $payment_method === $this->id ) {
				$url = $this->get_return_url( $order );

				if( $this->addgautm	&& $this->addgautm == 'yes'	) {
					$url = add_query_arg( array(
			                        'utm_nooverride' => 1
			                    ), $url );
				}

				echo "<meta http-equiv='Refresh' content='1; Url=\"$url\"'>";
			}
			
		}

		/**
		 * [base64Decode description]
		 * @param  [type] $scrambled [description]
		 * @return [type]            [description]
		 */
		function base64Decode($scrambled) {
			// Initialise output variable
			$output = "";
	
			// Fix plus to space conversion issue
			$scrambled = str_replace(" ", "+", $scrambled);

			// Do decoding
			$output = base64_decode($scrambled);

			// Return the result
			return $output;
		} // END base64Decode

		/**
		 * A Simple Xor encryption algorithm
		 *
		 * [worldpaysimpleXor description]
		 * @param  [type] $text [description]
		 * @param  [type] $key  [description]
		 * @return [type]       [description]
		 */
		function worldpaysimpleXor($text, $key) {
		// Initialise key array
			$key_ascii_array = array();
		
			// Initialise output variable
			$output = "";
		
			// Convert $key into array of ASCII values
			for($i = 0; $i < strlen($key); $i++){
				$key_ascii_array[$i] = ord(substr($key, $i, 1));
			}
	
			// Step through string a character at a time
			for($i = 0; $i < strlen($text); $i++) {
				// Get ASCII code from string, get ASCII code from key (loop through with MOD), XOR the
				// two, get the character from the result
				$output .= chr(ord(substr($text, $i, 1)) ^ ($key_ascii_array[$i % strlen($key)]));
			}
	
			// Return the result
			return $output;
		} // END simpleXor	

		/**
		 * A convenience function that extracts the values from the query string.
		 * Works even if one of the values is a URL containing the & or = signs.
		 */
		function getTokens( $query_string = NULL ) {

			$output = array();

			if ( $query_string ) {

				$msgType = ( isset( $output['msgType'] ) && $output['msgType'] != '' ) ? $output['msgType'] : '';

				parse_str( $query_string , $output );

				$output['subscriptionurl'] = $output['subscriptionurl'] . '&msgType=' . $msgType . '&wc-api=' . $output['wc-api'];
				unset( $output['msgType'] );
				unset( $output['wc-api'] );

			}

			// Return the output array
			return $output;

		} // END getTokens

		/**
		 * Subscription Cancelation
		 * 
		 * When a store manager or user cancels a subscription in the store, also cancel the subscription with WorldPay. 
		 */
		function cancel_subscription_with_worldpay( $subscription ) {

			if ( $subscription && $subscription->get_payment_method() == $this->id ) {

				$parent_order      	= $subscription->get_parent();
	            $parent_order_id   	= $parent_order->get_id();

				$futurepayid 		= get_post_meta( $parent_order_id, '_futurepayid', TRUE );

				$response 			= $this->change_subscription_status( $futurepayid, 'Cancel' );

				if ( isset( $response['ACK'] ) && $response['ACK'] == 'Success' ) {
					$order 	 = new WC_Order( (int) $subscription->parent_id );
					$order->add_order_note( __( 'Subscription cancelled', 'woocommerce_worlday' ) );
				}

			}

		}

		/**
		 * Cancel Subscription via iAdmin
		 */
		function change_subscription_status( $futurepayid, $new_status ) {

			if ( self::status() == 'testing' ) {
				$curlurl = 'https://secure-test.worldpay.com/wcc/iadmin';
			} else {
				$curlurl = 'https://secure.worldpay.com/wcc/iadmin';
			}

			switch( $new_status ) {
				case 'Cancel' :
					$new_status_string = __( 'cancelled', 'woocommerce_worlday' );

					// New API Request for cancellations
					$api_request 				= array();
					$api_request['instId'] 		= urlencode( $this->remoteid );
					$api_request['authPW'] 		= urlencode( $this->remotepw );
					$api_request['futurePayId'] = $futurepayid;
					$api_request['op-cancelFP'] = '';

					break;
			}

			// Debugging
			if ( $this->debug == true ) {
   				$this->log->add( $this->id, __('WorldPay Cancel Subscription Request', 'woocommerce_worlday') . '');
   				$this->log->add( $this->id, '====================================' );
   				$this->log->add( $this->id, print_r( str_replace( '<br />',"\n", $api_request ), TRUE ) );
   				$this->log->add( $this->id, '====================================' );
   			}

   			try {

				$headers = array(
				    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
				    'Cache-Control: no-cache',
				    'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
				    'User-Agent: ' . $_SERVER['HTTP_USER_AGENT'],
				);

				$cancel_request = array(
										'method' 		=> 'POST',
										'timeout' 		=> 45,
										'redirection' 	=> 5,
										'httpversion' 	=> '1.0',
										'blocking' 		=> true,
										'headers' 		=> $headers,
										'body' 			=> $api_request,
										'cookies' 		=> array()
	    							);

				$result = wp_remote_post( $curlurl, $cancel_request );

				if( is_wp_error( $result ) ) {
					$error = $result->get_error_message();
					throw new Exception( __( 'There was a problem cancelling the subscription with the FuturePay ID ' . $futurepayid . ' <pre>' . print_r( $error, TRUE ) . '</pre>', 'woocommerce_worlday' ) );
				} else {
					if ( !$this->startsWith( $result['body'], 'Y' ) ) {
						throw new Exception( __( 'There was a problem cancelling the subscription with the FuturePay ID ' . $futurepayid . ' <pre>' . print_r( $result['body'], TRUE ) . '</pre>', 'woocommerce_worlday' ) );
					}
				}

   			} catch( Exception $e ) {

   				$this->log->add( $this->id, __('WorldPay Cancel Subscription Request', 'woocommerce_worlday') . '');
   				$this->log->add( $this->id, '====================================' );
   				$this->log->add( $this->id, print_r( str_replace( '<br />',"\n", $e->getMessage() ), TRUE ) );
   				$this->log->add( $this->id, '====================================' );

			}

		}

		/**
		 * [refund description]
		 * @param  Varien_Object $payment [description]
		 * @param  [type]        $amount  [description]
		 * @return [type]                 [description]
		 */
    	function process_refund( $order_id, $amount = NULL, $reason = '' ) {

   			try {

	    		$order = new WC_Order( $order_id );

	    		$api_request = array();

				if ( self::status() == 'testing' ) :
					$curlurl = 'https://secure-test.worldpay.com/wcc/itransaction';
					$api_request['testMode'] = '100';
				else :
					$curlurl = 'https://secure.worldpay.com/wcc/itransaction';
					$api_request['testMode'] = '0';
				endif;

				// New API Request for cancellations
				$api_request['instId'] 				= $this->remoteid;
				$api_request['authPW'] 				= $this->remotepw;
				$api_request['cartId']   			= 'Refund';
				$api_request['transId'] 			= $this->get_transaction_id( $order );
				$api_request['amount']   			= $amount;
				$api_request['currency'] 			= $order->get_currency();
				$api_request['op'] 					= 'refund-partial';

				$headers = array(
				    'Accept' 		=> 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
				    'Cache-Control' => 'no-cache',
				    'Content-Type' 	=> 'application/x-www-form-urlencoded; charset=utf-8'
				);

				$args = array(
									'method' 		=> 'POST',
									'timeout' 		=> 45,
									'redirection' 	=> 5,
									'httpversion' 	=> '1.0',
									'blocking' 		=> true,
									'headers' 		=> array(),
									'body' 			=> $api_request,
									'cookies' 		=> array()

    							);
				// Debugging
				if ( $this->debug == true ) {
	   				$this->log->add( $this->id, __('WorldPay Refund Request', 'woocommerce_worlday') . '');
	   				$this->log->add( $this->id, '====================================' );
	   				$this->log->add( $this->id, print_r( str_replace( '<br />',"\n", $api_request ), TRUE ) );
	   				$this->log->add( $this->id, print_r( $args, TRUE ) );
	   				$this->log->add( $this->id, '====================================' );
	   			}

				$result = wp_remote_post( $curlurl, $args );

				if( is_wp_error( $result ) ) {

					$error = $result->get_error_message();

					$order->add_order_note( __( 'There was a problem with the API when processing this refund : ' . print_r( $error, TRUE ), 'woocommerce_worlday' ) );

					throw new Exception( __( 'There was a problem with the API when processing this refund : ' . print_r( $error, TRUE ), 'woocommerce_worlday' ) );

				} elseif ( $this->startsWith( $result['body'], 'A' ) ) {

					$transactionid = explode(",", $result['body'] );

					$order 	     	= new WC_Order( (int) $order_id );
					$order_currency = $order->get_currency();

					$order->add_order_note( sprintf( __( 'Order Refunded<br />Refund Amount - %1$s<br />Refund Reason - %2$s<br />Transaction Id - %3$s', 'woocommerce_worlday' ), wc_price( $amount, array( 'currency' => $order_currency ) ), $reason, $transactionid[1] ) );

					return true;

				} else {

					throw new Exception( __( 'There was a problem processing this refund : ' . print_r( $result['body'], TRUE ), 'woocommerce_worlday' ) );

				}

   			} catch( Exception $e ) {
   					
   				if ( $this->debug == true ) {

	   				$this->log->add( $this->id, __('WorldPay Refund Failed', 'woocommerce_worlday') . '');
	   				$this->log->add( $this->id, '====================================' );
	   				$this->log->add( $this->id, print_r( str_replace( '<br />',"\n", $e->getMessage() ), TRUE ) );
	   				$this->log->add( $this->id, '====================================' );

   				}

   				return new WP_Error( 'error', __('Refund failed ', 'woocommerce_worlday') . $e->getMessage() );

			}

    	}

		function get_transaction_id( $order ) {

			$order_id 		= $order->get_id();
			$transaction 	= get_post_meta( $order_id, '_worldpay_response', TRUE );

			if( isset( $transaction['transId'] ) ) {
				return $transaction['transId'];
			}

			return $order->get_transaction_id();

		}
		
		function startsWith($haystack, $needle) {
    		return $needle === "" || strpos($haystack, $needle) === 0;
		}

		/**
		 * Returns the plugin's url without a trailing slash
		 *
		 * [get_plugin_url description]
		 * @return [type]
		 */
		public static function get_plugin_url() {
			return str_replace('/classes','',untrailingslashit( plugins_url( '/', __FILE__ ) ) );
		}

		/**
		 * Returns the plugin's path
		 *
		 * [get_plugin_url description]
		 * @return [type]
		 */
		public function get_plugin_path() {
			return str_replace('classes','',( plugin_dir_path( __FILE__ ) ) );
		}

		/**
		 * Create a unique MD5 example for sites to use
		 * @return $md5
		 */
		private function generate_md5() {

			// Create MD5
			$md5 = MD5( NONCE_SALT . AUTH_SALT . time() );

			// Replace possible problematic characters
			$md5 = preg_replace( '/[^a-zA-Z0-9\']/', '$', $md5 );
			$md5 = str_replace( 'a', 'A', $md5 );

			// Make sure it's not too long
			$md5 = substr( $md5, 0, 25 );

			$md5 = $md5 . 'X$';

			return $md5;

		}

	} // END CLASS