<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Generates requests to send to WorldPay
 */
class WC_Gateway_WorldPay_Request {

	/**
	 * Pointer to gateway making the request
	 * @var WC_Gateway_Worldpay_Form
	 */
	protected $gateway;

	/**
	 * Endpoint for requests from WorldPay
	 * @var string
	 */
	protected $notify_url;

	// Developer Credentials
	protected $dev_installtion_id = '1156961';

	/**
	 * Constructor
	 * @param WC_Gateway_WorldPay $gateway
	 */
	public function __construct( $gateway ) {
		$this->gateway     = $gateway;
		$this->notify_url  = WC()->api_request_url( 'WC_Gateway_Worldpay_Form' );
	}

	/**
	 * Clean up the array
	 */
	protected static function clean_array() {
		return array( '<', '>', '&', "'", '"' );
	}

	/**
     * Replace unwanted characters
     */
    protected static function unwanted_array() {
        return array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o','ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
    }

	/**
	 * Get the WorldPay request URL for an order
	 * @param  WC_Order  $order
	 * @param  boolean $sandbox
	 * @return string
	 */
	public function get_request_url( $order, $testmode = 'testing' ) {

		$worldpay_args = http_build_query( $this->get_worldpay_args( $order ), '', '&' );

		if ( $testmode == 'testing' ) {
			return 'https://secure-test.worldpay.com/wcc/purchase?testMode=100&' . $worldpay_args;
		} else {
			return 'https://secure.worldpay.com/wcc/purchase?' . $worldpay_args;
		}

	}

	public static function get_instid ( $instid, $developer='no' ) {
		if( $developer === 'yes' ) {
			return '1156961';
		} else {
			return $instid;
		}
	}

	public static function get_payment_response_url ( $dynamiccallback, $developer='no' ) {
		if( $developer === 'yes' ) {
			return 'yes';
		} else {
			return $dynamiccallback;
		}

	}

	public static function get_md5_secret ( $worldpaymd5, $developer='no' ) {
		if( $developer === 'yes' ) {
			return 'b73571A64bc1b395A959b4f66X$';
		} else {
			return $worldpaymd5;
		}

	}

	protected static function get_payment_response_password ( $instid, $developer='no' ) {

	}


	/**
	 * Get WorldPay Args for passing to WorldPay
	 *
	 * @param WC_Order $order
	 * @return array
	 */
	public static function get_worldpay_args( $order ) {

		if( !is_object( $order ) ) {
			$order = new WC_Order( $order );
		}

        $order_id   = $order->get_id();

		$order_total 			= $order->get_total();
		$total_shipping 		= $order->get_shipping_total();
		$order_currency 		= $order->get_currency();
		$order_key  			= $order->get_order_key();

		$billing_first_name 	= self::convert_smart_quotes( $order->get_billing_first_name() );
		$billing_last_name 		= self::convert_smart_quotes( $order->get_billing_last_name() );
		$billing_address_1 		= self::convert_smart_quotes( $order->get_billing_address_1() );
		$billing_address_2 		= $order->get_billing_address_2();
		$billing_city 			= self::city( $order->get_billing_city(), $order );
		$billing_state 			= $order->get_billing_state();
		$billing_postcode 		= $order->get_billing_postcode();
		$billing_country 		= $order->get_billing_country();
		$billing_email 			= $order->get_billing_email();
		$billing_phone 			= $order->get_billing_phone();

		$shipping_first_name 	= self::convert_smart_quotes( $order->get_shipping_first_name() );
		$shipping_last_name 	= self::convert_smart_quotes( $order->get_shipping_last_name() );
		$shipping_address_1 	= self::convert_smart_quotes( $order->get_shipping_address_1() );
		$shipping_address_2 	= $order->get_shipping_address_2();
		$shipping_city 			= self::city( $order->get_shipping_city(), $order );
		$shipping_state 		= $order->get_shipping_state();
		$shipping_postcode 		= $order->get_shipping_postcode();
		$shipping_country 		= $order->get_shipping_country();

		$settings = get_option( 'woocommerce_worldpay_settings' );

		$accid = $settings['accid'];
		$lang  = $settings['lang'];

		$output_order_num = self::get_worldpay_order_num( $order );

		if( self::get_payment_response_url( $settings['dynamiccallback'] ) === 'yes' ) {
			$callbackurl   	= site_url( 'wp-content/plugins/woocommerce-gateway-worldpay/wpcallback.php' );
			$successurl   	= site_url( 'wp-content/plugins/woocommerce-gateway-worldpay/wpcallback.php' );
		} else {
			$callbackurl   	= str_replace( 'https:', 'http:', add_query_arg( 'wc-api', 'WC_Gateway_Worldpay_Form', home_url( '/' ) ) );
			$successurl    	= str_replace( 'https:', 'http:', add_query_arg( 'wc-api', 'WC_Gateway_Worldpay_Form', home_url( '/' ) ) );
		}

		// Setup the url for orders that are cancelled at WorldPay
		$failureurl 		= str_replace( '&amp;', '&', $order->get_cancel_order_url() );
		$failureurl 		= str_replace( 'https:', 'http:', $failureurl );

		$worldpay_args['instId'] 	= self::get_instid( $settings['instId'] );
		$worldpay_args['cartId'] 	= str_replace( self::clean_array(), '',  $order_key . '-' . $output_order_num . '-' . time() );
		$worldpay_args['amount']	= self::get_worldpay_order_amount( $order );
		$worldpay_args['currency'] 	= $order_currency;
		$worldpay_args['desc'] 		= str_replace( '{ordernum}', $output_order_num, str_replace( self::clean_array(), '',  $settings['orderDesc'] ) );
		$worldpay_args['name'] 		= strtr( $billing_first_name. ' ' .$billing_last_name, self::unwanted_array() );
		$worldpay_args['address1'] 	= strtr( $billing_address_1, self::unwanted_array() );
		$worldpay_args['address2'] 	= strtr( $billing_address_2, self::unwanted_array() );
		$worldpay_args['address3'] 	= '';
		$worldpay_args['town'] 		= strtr( $billing_city, self::unwanted_array() );
		$worldpay_args['region'] 	= strtr( $billing_state, self::unwanted_array() );
		$worldpay_args['postcode'] 	= strtr( $billing_postcode, self::unwanted_array() );
		$worldpay_args['country'] 	= strtr( $billing_country, self::unwanted_array() );
		$worldpay_args['tel'] 		= $billing_phone;
		$worldpay_args['email'] 	= strtr( $billing_email, self::unwanted_array() );

		if ( $settings['fixContact'] == 'yes' ) {
			$worldpay_args['fixContact'] = '';
		}

		if ( $settings['hideContact'] == 'yes' ) {
			$worldpay_args['hideContact'] = '';
		}

		if ( $accid != '' || isset( $accid ) ) {
			$worldpay_args['accId1'] = $accid;
		}

		if ( $settings['authMode'] == 'A' || $settings['authMode'] == 'E' ) {
			$worldpay_args['authMode'] = $settings['authMode'];
		}

		if ( $settings['hideCurrency'] == 'yes' ) {
			$worldpay_args['hideCurrency'] = '';
		}

		if ( $lang != '' || isset( $lang ) ) {
			$worldpay_args['lang'] = $lang;
		}

		if ( $settings['noLanguageMenu'] == 'yes' ) {
			$worldpay_args['noLanguageMenu'] = '';
		}

		$worldpay_args['MC_callback'] 			= $callbackurl;
		$worldpay_args['MC_callback-ppe'] 		= $callbackurl;
		$worldpay_args['MC_SuccessURL'] 		= $successurl;
		$worldpay_args['MC_FailureURL'] 		= $failureurl;
		$worldpay_args['MC_order'] 				= $order_id;
		$worldpay_args['MC_transactionNumber'] 	= '1';

		$subscription_args = array();

		if ( function_exists( 'wcs_order_contains_subscription' ) ) {
			if ( wcs_order_contains_subscription( $order ) ) {
				// Subscription 2.0
				$subscription_args = self::get_worldpay_subscriptions_args( $order );
			}
		} elseif( class_exists( 'WC_Subscriptions' ) && WC_Subscriptions_Order::order_contains_subscription( $order_id ) ) {
			$subscription_args = self::get_worldpay_subscription_args( $order );
		}

		if( sizeof( $subscription_args ) !== 0 ) {
			$worldpay_args = array_merge( $worldpay_args, $subscription_args );
		}

		/**
		 * Add MD5 args
		 *
		 * instId:amount:currency:cartId:name:email:address1:postcode
		 * 
		 * Modify Signature Fields used to verify transactions
		 * Standard list : instId:amount:currency:cartId:name:email:address1:postcode
		 * Any changes MUST use : as the separator
		 * Make sure you update the field in your Worldpay Installation settings.
		 * add_filter( 'woocommerce_worldpay_signature_fields', 'custom_woocommerce_worldpay_signature_fields' );
		 * function custom_woocommerce_worldpay_signature_fields( $fields ) {
		 * 	return 'instId:amount:currency:cartId:email:address1:postcode';
		 * }
		 */
		if ( self::get_md5_secret( $settings['worldpaymd5'] ) != '' ) {

			$worldpay_args['signatureFields'] = apply_filters( 'woocommerce_worldpay_signature_fields', 'instId:amount:currency:cartId:name:email:address1:postcode' );

			$build_signature = self::get_md5_secret( $settings['worldpaymd5'] ).':'.$worldpay_args['instId'].':'.$worldpay_args['amount'].':'.$worldpay_args['currency'].':'.$worldpay_args['cartId'].':'.$worldpay_args['name'].':'.$worldpay_args['email'].':'.$worldpay_args['address1'].':'.$worldpay_args['postcode'];

			$worldpay_args['signature'] = md5( self::build_signature( $worldpay_args, $settings['worldpaymd5'] ) );

		}

		// Make sure we remove smart quotes
		$worldpay_args = apply_filters( 'woocommerce_worldpay_args', $worldpay_args, $order );

		if ( $settings['debug'] == 'yes' ) {
			$log = new WC_Logger();

			$log->add( 'worldpay', '====================================' );
			$log->add( 'worldpay', __('WorldPay Args', 'woocommerce_worlday') . '');
			$log->add( 'worldpay', '====================================' );
			$log->add( 'worldpay', print_r( $worldpay_args, TRUE ) );
			$log->add( 'worldpay', '====================================' );
			$log->add( 'worldpay', '' );
		}

		return $worldpay_args;

	}

	protected static function build_signature( $worldpay_args, $worldpaymd5 ) {

		$signatureFields = $worldpay_args['signatureFields'];
		$fields 		 = explode( ':', $signatureFields );

		$signature 		 = array();
		$signature[] 	 = self::get_md5_secret( $worldpaymd5 );

		foreach( $fields AS $field ) {
			$signature[] = $worldpay_args[$field];
		}
		
		return implode( ':', $signature );

	}

	protected static function get_worldpay_order_num( $order ) {

		$order_id   = $order->get_id();

		$output_order_num = $order->get_order_number();

		// Look for the Sequential Order Numbers Pro / Sequential Order Numbers order number and use it if it's there
		if( !is_admin() ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		// Sequential Order Numbers
		if ( get_post_meta( $order_id,'_order_number',TRUE ) && class_exists( 'WC_Seq_Order_Number' ) ) :
			$output_order_num = get_post_meta( $order_id,'_order_number',TRUE );
		endif;

		// Sequential Order Numbers Pro
		if ( get_post_meta( $order_id,'_order_number_formatted',TRUE ) && class_exists( 'WC_Seq_Order_Number_Pro' ) ) :
			$output_order_num = get_post_meta( $order_id,'_order_number_formatted',TRUE );
		endif;

		return $output_order_num;

	}

	protected static function get_worldpay_order_amount( $order ) {

		$order_id   	= $order->get_id();
        $order_total 	= $order->get_total();

		/**
		 * Modify the order amount for subscriptions
		 *
		 * If there is a subscription we get the amount from WC_Subscriptions_Order::get_total_initial_payment( $order )
		 * otherwise it's just the order total
		 *
		 * WC_Subscriptions_Order::get_total_initial_payment( $order ) works out if there is a payment due today, 
		 * if not this value will be 0 and no money will be taken today
		 */
		if ( function_exists( 'wcs_order_contains_subscription' ) ) {
			return $order_total;
		} else {
			return apply_filters( 'get_worldpay_order_amount', $order_total, $order_id );
		}

	} // get_worldpay_order_amount

	/**
	 * Args for Subscriptions 1
	 * @param  [type] $order [description]
	 * @return [type]        [description]
	 */
	protected static function get_worldpay_subscription_args( $order ) {

		$order_id   = $order->get_id();
				
		switch ( strtolower(WC_Subscriptions_Order::get_subscription_period( $order )) ) {
				
			case 'day' :
				$subscription_period = '1';
				break;
					
			case 'week' :
				$subscription_period = '2';
				break;
					
			case 'month' :
				$subscription_period = '3';
				break;
				
			case 'year' :
				$subscription_period = '4';
				break;
				
		}
				
		switch ( strtolower(WC_Subscriptions_Order::get_subscription_trial_period( $order )) ) {
				
			case 'day' :
				$trial_period = '1';
				break;
					
			case 'week' :
				$trial_period = '2';
				break;
					
			case 'month' :
				$trial_period = '3';
				break;
				
			case 'year' :
				$trial_period = '4';
				break;
				
		}


		/**
		 * If subscription is for one period (1 month, 1 day, 1 year etc) and there is no trial period then we don't need to set up Future Pay
		 */
		if( WC_Subscriptions_Order::get_subscription_trial_length( $order ) < '1' && WC_Subscriptions_Order::get_subscription_length( $order ) == '1' ) {

		} else {

			$worldpay_args['futurePayType'] = 'regular';
			/**
			 * If the subscription period is less than 2 weeks the option must be 0 which means no modifications
			 */
			if ( $subscription_period == '1' || ( $subscription_period == '2' && WC_Subscriptions_Order::get_subscription_interval( $order ) <= '2' ) ) {
				$worldpay_args['option'] = 0;
			} else {
				$worldpay_args['option'] = 1;
			}
				
			/**
			 * Set start date if there is a trial period or use subscription period settings
			 * 
			 * Use strtotime because subscriptions passes an INT for the length and a word for period
			 * doing it any other way means a messy calculation
			 */
			if ( WC_Subscriptions_Order::get_subscription_trial_length( $order ) >= 1 || ( class_exists( 'WC_Subscriptions_Synchroniser' ) && WC_Subscriptions_Synchroniser::order_contains_synced_subscription( $order_id ) ) ) {

				if ( class_exists( 'WC_Subscriptions_Synchroniser' ) && WC_Subscriptions_Synchroniser::order_contains_synced_subscription( $order_id ) ) {
					// Get product
					$subscriptions_in_order      = WC_Subscriptions_Order::get_recurring_items( $order );
					$subscription_item           = array_pop( $subscriptions_in_order );
					$product_id                  = WC_Subscriptions_Order::get_items_product_id( $subscription_item );
					// Get first payment date
					$start_date = date( "Y-m-d", WC_Subscriptions_Synchroniser::get_first_payment_date( '', $order, $product_id, 'timestamp' ) );
				} else {
					$start_date = date("Y-m-d",strtotime("+" . WC_Subscriptions_Order::get_subscription_trial_length( $order ) . " " . WC_Subscriptions_Order::get_subscription_trial_period( $order )));
				}

				$worldpay_args['startDate'] = $start_date;

			} else {

				$worldpay_args['startDelayMult'] = WC_Subscriptions_Order::get_subscription_interval( $order );
				$worldpay_args['startDelayUnit'] = $subscription_period;							

			}
				
			/**
			 * Set subscription length
			 *
			 * WorldPay does not count the intial payment in the noOfPayments setting
			 *
			 * Includes work around for 2 payment subscriptions with no free trial.
			 */
			if( (WC_Subscriptions_Order::get_subscription_trial_length( $order ) == '0' || WC_Subscriptions_Order::get_subscription_trial_length( $order ) == '') && WC_Subscriptions_Order::get_subscription_length( $order ) == 2 ) {

				/**
				 * Two payment subscriptions with no free trial
				 * 
				 * Set the start date to be tomorrow, no payment will be taken initially
				 *
				 * Why do it this way?
				 * WorldPay takes 1 payment now so the number of payments in a subscription needs to be reduced by 1 BUT, 
				 * for a 2 payment subscription that means 1 payment now and 1 payment in the future - WorldPay does not allow only 1 payment in the future, the minimum is 2
				 * This work around means that the initial payment is take tomorrow at 3:00 AM, essentially forcing a free trial of 1 day.
				 *
				 * Further problems arise if the initial payment is not the same as the recurring payments.
				 */
						
				if ( WC_Subscriptions_Order::get_recurring_total( $order ) == WC_Subscriptions_Order::get_total_initial_payment( $order ) ) {
					$worldpay_args['amount'] = '0.00';
					unset( $worldpay_args['startDelayMult'] );
					unset( $worldpay_args['startDelayUnit'] );
					$worldpay_args['startDate'] = date( "Y-m-d", strtotime(date( "Y-m-d" ) . ' + 1 day') );
				} else {
					$worldpay_args['amount'] = WC_Subscriptions_Order::get_total_initial_payment( $order ) - WC_Subscriptions_Order::get_recurring_total( $order );
				}

				$worldpay_args['noOfPayments'] = WC_Subscriptions_Order::get_subscription_length( $order );

			} elseif( (WC_Subscriptions_Order::get_subscription_trial_length( $order ) == '0' || WC_Subscriptions_Order::get_subscription_trial_length( $order ) == '') && WC_Subscriptions_Order::get_subscription_length( $order ) > 2 ) {
				// More that two payments, no free trial
				$subs_length = WC_Subscriptions_Order::get_subscription_length( $order ) - 1;
				$worldpay_args['noOfPayments'] = $subs_length;

			} else {
				$worldpay_args['noOfPayments'] = WC_Subscriptions_Order::get_subscription_length( $order );
			}

			if ( WC_Subscriptions_Order::get_subscription_length( $order ) == '1') {

			} else {
				$worldpay_args['intervalMult'] = WC_Subscriptions_Order::get_subscription_interval( $order );
				$worldpay_args['intervalUnit'] = $subscription_period;	
			}

			$worldpay_args['normalAmount'] = WC_Subscriptions_Order::get_recurring_total( $order );

		} // if( WC_Subscriptions_Order::get_subscription_trial_length( $order ) == '0' && WC_Subscriptions_Order::get_subscription_length( $order ) == '1' )

		return $worldpay_args;

	} // get_worldpay_subscription_args

	/**
	 * Args for Subscriptions 2.0
	 * @param  [type] $order [description]
	 * @return [type]        [description]
	 */
	protected static function get_worldpay_subscriptions_args( $order ) {

		$order_id   = $order->get_id();

		$subscription 			= wcs_get_subscriptions_for_order( $order_id );
		$subscription_id 		= key( $subscription );

		$_billing_period 		= strtolower( get_post_meta( $subscription_id, '_billing_period', TRUE ) );
		$_trial_period 			= strtolower( get_post_meta( $subscription_id, '_trial_period', TRUE ) );
		$_schedule_trial_end	= strtolower( get_post_meta( $subscription_id, '_schedule_trial_end', TRUE ) );
		$_schedule_next_payment = strtolower( get_post_meta( $subscription_id, '_schedule_next_payment', TRUE ) );
		$_schedule_end 			= strtolower( get_post_meta( $subscription_id, '_schedule_end', TRUE ) );

		switch ( $_billing_period ) {
				
			case 'day' :
				$subscription_period = '1';
				break;
					
			case 'week' :
				$subscription_period = '2';
				break;
					
			case 'month' :
				$subscription_period = '3';
				break;
				
			case 'year' :
				$subscription_period = '4';
				break;
				
		}
				
		switch ( $_trial_period ) {
				
			case 'day' :
				$trial_period = '1';
				break;
					
			case 'week' :
				$trial_period = '2';
				break;
					
			case 'month' :
				$trial_period = '3';
				break;
				
			case 'year' :
				$trial_period = '4';
				break;

			default :
				$trial_period = $subscription_period;
				
		}

		/**
		 * Billing period "mult"
		 * eg every 2 weeks, every 3 months
		 */
		$intervalMult = get_post_meta( $subscription_id,'_billing_interval', true );

		// Number of payments
		$noOfPayments = self::get_subscription_number_of_payments( $_schedule_end, $_schedule_next_payment, $_billing_period, $intervalMult );

		/**
		 * If subscription is for one period (1 month, 1 day, 1 year etc) and there is no trial period then we don't need to set up Future Pay
		 */
		// if( $_schedule_trial_end == '0' && $noOfPayments == '1' ) {
		if( $_schedule_trial_end == '0' && $_schedule_next_payment == '0' ) {
			return;
		} else {

			// Build the Subscription $worldpay_args
			$worldpay_args['futurePayType'] = 'regular';

			/**
			 * If the subscription period is less than 2 weeks the option must be 0 which means no modifications
			 */
			if ( $subscription_period == '1' || ( $subscription_period == '2' && $noOfPayments <= '2' ) ) {
				$worldpay_args['option'] = 0;
			} else {
				$worldpay_args['option'] = 1;
			}
				
			/**
			 * Set start date if there is a trial period or use subscription period settings
			 * 
			 * Use strtotime because subscriptions passes an INT for the length and a word for period
			 * doing it any other way means a messy calculation
			 */
			if ( $_schedule_trial_end != '0' || ( class_exists( 'WC_Subscriptions_Synchroniser' ) && WC_Subscriptions_Synchroniser::subscription_contains_synced_product( $subscription_id ) ) ) {

				$start_date = strtotime( $_schedule_next_payment );
				$worldpay_args['startDate'] = date( "Y-m-d", $start_date );

			} else {

				$worldpay_args['startDelayMult'] = $intervalMult;
				$worldpay_args['startDelayUnit'] = $subscription_period;							

			}

			/**
			 * Set the number of payments
			 */
			$worldpay_args['noOfPayments'] = $noOfPayments;

			/**
			 * Set subscription length
			 *
			 * WorldPay does not count the intial payment in the noOfPayments setting
			 *
			 * Includes work around for 2 payment subscriptions with no free trial.
			 */
			if( $_schedule_trial_end == '0' && $noOfPayments == 1 ) {

				/**
				 * Two payment subscriptions with no free trial
				 * 
				 * Set the start date to be tomorrow, no payment will be taken initially
				 *
				 * Why do it this way?
				 * WorldPay takes 1 payment now so the number of payments in a subscription needs to be reduced by 1 BUT, 
				 * for a 2 payment subscription that means 1 payment now and 1 payment in the future - WorldPay does not allow only 1 payment in the future, the minimum is 2
				 * This work around means that the initial payment is take tomorrow at 3:00 AM, essentially forcing a free trial of 1 day.
				 *
				 * Further problems arise if the initial payment is not the same as the recurring payments.
				 */
						
				if ( get_post_meta( $subscription_id, '_order_total', TRUE ) == $order->order_total ) {
					$worldpay_args['amount'] = '0.00';
					unset( $worldpay_args['startDelayMult'] );
					unset( $worldpay_args['startDelayUnit'] );
					$worldpay_args['startDate'] = date( "Y-m-d", strtotime(date( "Y-m-d" ) . ' + 1 day') );
				} else {
					$worldpay_args['amount'] = $order->order_total - get_post_meta( $subscription_id, '_order_total', TRUE );
				}

				// Increase the number of payments by 1 since we now have a 1 day free trial
				$worldpay_args['noOfPayments'] = $noOfPayments + 1;

			}

			if ( $noOfPayments === 1 ) {

			} else {
				$worldpay_args['intervalMult'] = $intervalMult;
				$worldpay_args['intervalUnit'] = $subscription_period;	
			}

			$worldpay_args['normalAmount'] = get_post_meta( $subscription_id, '_order_total', TRUE );

/*
			futurePayType
			startDate
			startDelayUnit
			startDelayMult
		
			intervalUnit
			intervalMult
			initialAmount
			normalAmount
			option
*/

			$worldpay_args['intervalMult'] = $intervalMult;
			$worldpay_args['intervalUnit'] = $subscription_period;
			// $worldpay_args['noOfPayments'] = $noOfPayments;

			$debugger = array(
				'_billing_period' 		 => $_billing_period,
				'_trial_period' 		 => $_trial_period,
				'_schedule_trial_end' 	 => $_schedule_trial_end,
				'_schedule_next_payment' => $_schedule_next_payment,
				'_schedule_end' 		 => $_schedule_end,
				'noOfPayments' 			 => $worldpay_args['noOfPayments'],
			);
			
		} // if( WC_Subscriptions_Order::get_subscription_trial_length( $order ) == '0' && WC_Subscriptions_Order::get_subscription_length( $order ) == '1' )

		return $worldpay_args;

	} // get_worldpay_subscriptions_args

	/**
	 * return number of payments in subscription
	 * 
	 * $_schedule_end : subscription end date
	 * $_schedule_next_payment : next payment date for subscription
	 * $_billing_period : day, week, moth, year
	 * $intervalMult : every month, every 2 weeks etc.
	 */
	protected static function get_subscription_number_of_payments( $_schedule_end, $_schedule_next_payment, $_billing_period, $intervalMult ) {
		
		if( $_schedule_end == '0' ) {
			return 0;
		}

		$_schedule_next_payment = strtotime( $_schedule_next_payment );
     	$_schedule_end 			= strtotime( $_schedule_end );

		if( $_billing_period == 'day' ) {
			$datediff 	= $_schedule_end - $_schedule_next_payment;
			$return 	= floor( $datediff/(60*60*24*$intervalMult) );

     		return $return;
     	}

     	if( $_billing_period == 'week' ) {
			$datediff 	= $_schedule_end - $_schedule_next_payment;
     		return floor( $datediff/(60*60*24*7*$intervalMult) );
     	}

     	if( $_billing_period == 'month' ) {
			$numberOfMonths = abs( ( date('Y', $_schedule_end) - date('Y', $_schedule_next_payment) )*12 + ( date('m', $_schedule_end) - date('m', $_schedule_next_payment) ) );
     		return $numberOfMonths / $intervalMult;
     	}

     	if( $_billing_period == 'year' ) {

			$_schedule_next_payment = date( "Y", $_schedule_next_payment );
     		$_schedule_end 			= date( "Y", $_schedule_end );
     		$datediff 				= $_schedule_end - $_schedule_next_payment;
			return $datediff / $intervalMult;

     	}

	}

	/**
	 * [get_subscription_product_meta description]
	 * Pass the subscription ID and get the subscription product so that
	 * product post meta can be retrieved eg _subscription_period_interval or _subscription_trial_length
	 * 
	 * @param  [type] $subscription_id [description]
	 * @return [type]                  [description]
	 */
	protected static function get_subscription_product_meta( $subscription_id, $meta ) {

		$subscription = wcs_get_subscription( $subscription_id );
		foreach( $subscription->get_items() as $item ) {
   			return get_post_meta( $item['product_id'],$meta, TRUE );
		}

	}
	
	/**
	 * Set a default city if city field is empty
	 */
	protected static function city( $city, $order ) {
		// Check WC version - changes for WC 3.0.0
		$pre_wc_30 = version_compare( WC_VERSION, '3.0', '<' );
		
		if ( '' != $city ) {
			return $city;
		} else {
			return $order->get_billing_country();
		}
	}

	/**
	 * Hacky way to convert smart quotes that Worldpay just does not like.
	 * Useful for apostrophes in names and addresses, eg O'Connor
	 * Just using str_replace without htmlentities results in '€™
	 * @param  [type] $string [description]
	 * @return [type]         [description]
	 */
	protected static function convert_smart_quotes( $string ) {

		$settings = get_option( 'woocommerce_worldpay_settings' );

		if( isset( $settings['smart_quotes'] ) && $settings['smart_quotes'] == 'no' ) {
			return $string;
		}

	    $string = htmlentities( $string, ENT_QUOTES, "UTF-8" );
	    $string = str_replace( '&rsquo;', "'", $string );

	    return $string;

	}

} // WC_Gateway_WorldPay_Request
