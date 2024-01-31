<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Generates requests to send to WorldPay
 */
class WC_Gateway_WorldPay_Request extends WC_Gateway_Worldpay_Form {

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

	/**
	 * Constructor
	 * @param WC_Gateway_WorldPay $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct();

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
        return array("'" => "", 'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o','ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
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

		if( isset( $settings['dynamiccallback'] ) && $settings['dynamiccallback'] === 'yes' ) {
			$callbackurl   	= site_url( 'wp-content/plugins/woocommerce-gateway-worldpay/wpcallback.php' );
		} else {
			$callbackurl   	= str_replace( 'https:', 'http:', add_query_arg( 'wc-api', 'WC_Gateway_Worldpay_Form', home_url( '/' ) ) );
			// Add utm_nooverride if required
			$callbackurl   	= self::get_callback_url( $callbackurl );
		}

		// Setup the url for orders that are cancelled at WorldPay
		$failureurl 		= str_replace( '&amp;', '&', $order->get_cancel_order_url() );
		// $failureurl 		= str_replace( 'https:', 'http:', $failureurl );

		// Add utm_nooverride if required
		$failureurl   	= self::get_callback_url( $failureurl );

		$worldpay_args['instId'] 	= isset( $settings['instId'] ) ? $settings['instId'] : '';
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

		if( isset( $settings['withDelivery'] ) && $settings['withDelivery'] == 'yes' && $order->needs_shipping_address() ) {
			$worldpay_args['withDelivery'] 	= '1';
			$worldpay_args['delvName'] 		= strtr( $shipping_first_name. ' ' .$shipping_last_name, self::unwanted_array() );
			$worldpay_args['delvAddress1'] 	= strtr( $shipping_address_1, self::unwanted_array() );
			$worldpay_args['delvAddress2'] 	= strtr( $shipping_address_2, self::unwanted_array() );
			$worldpay_args['delvAddress3'] 	= '';
			$worldpay_args['delvTown'] 		= strtr( $shipping_city, self::unwanted_array() );
			$worldpay_args['delvRegion'] 	= strtr( $shipping_state, self::unwanted_array() );
			$worldpay_args['delvPostcode'] 	= strtr( $shipping_postcode, self::unwanted_array() );
			$worldpay_args['delvCountry'] 	= strtr( $shipping_country, self::unwanted_array() );
		}

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
		$worldpay_args['MC_SuccessURL'] 		= $callbackurl;
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
		if ( isset( $settings['worldpaymd5'] ) && $settings['worldpaymd5'] != '' ) {

			$worldpay_args['signatureFields'] = apply_filters( 'woocommerce_worldpay_signature_fields', self::get_signaturefields() );
			$worldpay_args['signature'] = md5( self::build_signature( $worldpay_args, $settings['worldpaymd5'] ) );

		}

		// Make sure we remove smart quotes
		$worldpay_args = apply_filters( 'woocommerce_worldpay_args', $worldpay_args, $order );

		if ( isset( $settings['debug'] ) && $settings['debug'] == 'yes' ) {
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
		$signature[] 	 = $worldpaymd5;

		foreach( $fields AS $field ) {
			$signature[] = $worldpay_args[$field];
		}
		
		return implode( ':', $signature );

	}

	protected static function get_worldpay_order_num( $order ) {

		$order_id   = $order->get_id();

		$output_order_num = $order->get_order_number();

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
	 * Args for Subscriptions 2.0
	 * @param  [type] $order [description]
	 * @return [type]        [description]
	 */
	protected static function get_worldpay_subscriptions_args( $order ) {

		$order_id   = $order->get_id();

		$subscriptions 			= wcs_get_subscriptions_for_order( $order_id );
		$subscription_id 		= key( $subscriptions );

		$subscription 	 		= wcs_get_subscription( (int) $subscription_id );

		/*
		$_billing_period 		= strtolower( get_post_meta( $subscription_id, '_billing_period', TRUE ) );
		$_trial_period 			= strtolower( get_post_meta( $subscription_id, '_trial_period', TRUE ) );
		$_schedule_trial_end	= strtolower( get_post_meta( $subscription_id, '_schedule_trial_end', TRUE ) );
		$_schedule_next_payment = strtolower( get_post_meta( $subscription_id, '_schedule_next_payment', TRUE ) );
		$_schedule_end 			= strtolower( get_post_meta( $subscription_id, '_schedule_end', TRUE ) );
		*/

		$_billing_period 		= strtolower( $subscription->get_billing_period() );
		$_trial_period 			= strtolower( $subscription->get_trial_period() );
		$_schedule_trial_end 	= date( "Y-m-d", strtolower( $subscription->get_time( 'trial_end' ) ) );
		$_schedule_next_payment = date( "Y-m-d", strtolower( $subscription->get_time( 'next_payment' ) ) );
		$_schedule_end 			= date( "Y-m-d", strtolower( $subscription->get_time( 'end' ) ) );

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
		$intervalMult = $subscription->get_billing_interval();

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
						
				if ( $subscription->get_total() == $order->order_total ) {
					$worldpay_args['amount'] = '0.00';
					unset( $worldpay_args['startDelayMult'] );
					unset( $worldpay_args['startDelayUnit'] );
					$worldpay_args['startDate'] = date( "Y-m-d", strtotime(date( "Y-m-d" ) . ' + 1 day') );
				} else {
					$worldpay_args['amount'] = $order->order_total - $subscription->get_total();
				}

				// Increase the number of payments by 1 since we now have a 1 day free trial
				$worldpay_args['noOfPayments'] = $noOfPayments + 1;

			}

			if ( $noOfPayments === 1 ) {

			} else {
				$worldpay_args['intervalMult'] = $intervalMult;
				$worldpay_args['intervalUnit'] = $subscription_period;	
			}

			$worldpay_args['normalAmount'] = $subscription->get_total();

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
	 * Set a default city if city field is empty
	 */
	protected static function city( $city, $order ) {
		
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

	/**
	 * [get_signaturefields description]
	 * @return [type] [description]
	 */
	protected static function get_signaturefields() {
		$settings = get_option( 'woocommerce_worldpay_settings' );

		if( isset( $settings['signaturefields'] ) && $settings['signaturefields'] != '' ) {
			return $settings['signaturefields'];
		}

		return 'instId:amount:currency:cartId:name:email:address1:postcode';

	}

	/**
	 * [get_callback_url description]
	 * @param  [type] $url [description]
	 * @return [type]      [description]
	 */
	protected static function get_callback_url( $url ) {

		$settings 		= get_option( 'woocommerce_worldpay_settings' );

		if( isset( $settings['addgautm'] ) && $settings['addgautm'] == 'yes' ) {

			// We don't know if the URL already has the parameter so we should remove it just in case
			$url = remove_query_arg( 'utm_nooverride', $url );

			// Now add the utm_nooverride query arg to the URL
			$url = add_query_arg( 'utm_nooverride', '1', $url );

		}

		return $url;

	}

} // WC_Gateway_WorldPay_Request
