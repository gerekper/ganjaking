<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WC_AF_Score_Helper' ) ) {
	class WC_AF_Score_Helper {
		/**
		 * @param $score_points
		 *
		 * @static
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return array
		*/
		public static function get_score_meta( $score_points ) {

			// No score? return defaults
			if ( '' == $score_points ) {
				return array( 'color' => '#adadad', 'label' => __( 'No fraud checking has been done on this order yet.', 'woocommerce-anti-fraud' ) );
			}

			$meta = array(
				'label' => __( 'Low Risk', 'woocommerce-anti-fraud' ),
				'color' => '#7AD03A'
			);
			$high_threshold = get_option('wc_settings_anti_fraud_higher_risk_threshold');

			$low_threshold = get_option('wc_settings_anti_fraud_low_risk_threshold');
			$score_points = self::invert_score($score_points);
			if ( $score_points <= $high_threshold && $score_points >= $low_threshold ) {	
				$meta['label'] = __( 'Medium Risk', 'woocommerce-anti-fraud' );
				$meta['color'] = '#FFAE00';
			} elseif ( $score_points > $high_threshold ) {
				
				$meta['label'] = __( 'High Risk', 'woocommerce-anti-fraud' );
				$meta['color'] = '#D54E21';
			}

			return $meta;
		}

		/**
		 * Invert a score
		 *
		 * @param        $score
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return int
		 */
		public static function invert_score( $score ) {
			$score = ( $score < 0 ) ? 0 : $score;

			return 100 - $score;
		}

		/**
		 * Schedule a fraud check
		 *
		 * @param $order_id
		 *
		 * @since  1.0.0
		 * @access public
		 */
		public function schedule_fraud_check( $order_id ) {

			// Try to get the Anti Fraud score
			$score = get_post_meta( $order_id, 'wc_af_score', true );

			// Check if the order is already checked
			if ( '' != $score ) {
				return;
			}

			// Get the order
			$order = wc_get_order( $order_id );

			update_post_meta( $order_id, '_wc_af_waiting', true );

			// Schedule the anti fraud check
			wp_schedule_single_event( time(), 'wc-af-check', array( 'order_id' => $order_id ) );
		}

		/**
		 * Returns a flag indicating if a fraud check has been queued but not yet completed
		 *
		 * @param $order_id
		 *
		 * @since  1.0.2
		 * @access public
		 */
		public static function is_fraud_check_queued( $order_id ) {

			$waiting = get_post_meta( $order_id, '_wc_af_waiting', true );
			return ( ! empty( $waiting ) );

		}

		/**
		 * Returns a flag indicating if a fraud check has been completed
		 *
		 * @param $order_id
		 *
		 * @since  1.0.2
		 * @access public
		 */
		public static function is_fraud_check_complete( $order_id ) {

			$score = get_post_meta( $order_id, 'wc_af_score', true );
			return ( ! empty( $score ) );

		}
		
		/**
		 * Do the actual fraud check
		 *
		 * @param $order_id
		 *
		 * @since 1.0.0
		 * @since 1.0.13 Revert points score to make clear comparison with email score.
		 *
		 */
		public function do_check( $order_id ) {
			
			// Create a Score object
			$score = new WC_AF_Score( $order_id );

			// Calculate score
			$score->calculate();

			// The score points
			$score_points = $score->get_score();
			// Save score to order
			update_post_meta( $order_id, 'wc_af_score', $score_points );

			// Rules in JSON
			$json_rules = array();
			if ( count( $score->get_failed_rules() ) > 0 ) {
				foreach ( $score->get_failed_rules() as $failed_rule ) {
					$json_rules[] = $failed_rule->to_json();
				}
			}

			// Save the failed rules as JSON
			update_post_meta( $order_id, 'wc_af_failed_rules', $json_rules );

			// Clear the pending flag from meta
			delete_post_meta( $order_id, '_wc_af_waiting' );

			// Get the order
			$order = wc_get_order( $order_id );

			$blacklist_available = false;

			$is_enable_blacklist = get_option('wc_settings_anti_fraudenable_automatic_email_blacklist');
			
			if('yes' == $is_enable_blacklist){

				$email_blacklist = get_option('wc_settings_anti_fraudblacklist_emails');
				if('' != $email_blacklist ){
					// String to array
					$blacklist = explode( ",", $email_blacklist );
					// Check if is valid array
					if ( is_array( $blacklist ) && count( $blacklist ) > 0 ) {

						// Trim items to be sure
						foreach ( $blacklist as $k => $v ) {
							$blacklist[$k] = trim( $v );
						}

						// Set $blacklist_available true
						$blacklist_available = true;
					}
				}
			}
			
			
			$whitelist_available = false;

			$is_enable_whitelist = get_option('wc_settings_anti_fraud_whitelist');
			
			if('' != $is_enable_whitelist){

				$email_whitelist = get_option('wc_settings_anti_fraud_whitelist');
				if('' != $email_whitelist ){
					// String to array
					
					$whitelist = explode( "\n", $email_whitelist );
					// Check if is valid array
					if ( is_array( $whitelist ) && count( $whitelist ) > 0 ) {

						// Trim items to be sure
						foreach ( $whitelist as $k => $v ) {
							$whitelist[$k] = trim( $v );
						}

						// Set $blacklist_available true
						$whitelist_available = true;
					}
				}
			}

			// Default new status
			$new_status = null;

			//check payment method
			/*$payment_method = get_post_meta( $order_id, '_payment_method', true );
			
			if('paypal' == $payment_method || 'ppec_paypal' == $payment_method && null == get_post_meta($order_id,'wc_af_paypal_email_status')) {
				
				$paypal_verification = $this->paypal_email_verification($order,10);
			}*/

			// If a payment for this order has already completed, use the payment requested
			// status as the default
			$payment_requested_status = get_post_meta( $order_id, '_wc_af_post_payment_status', true );
			if ( ! empty( $payment_requested_status ) ) {
				$new_status = $payment_requested_status;
			}
			$ip_address = $order->get_customer_ip_address();
			$is_whitelisted = true;
			$orderemail = version_compare( WC_VERSION, '3.0', '<' ) ? $order->billing_email : $order->get_billing_email();
			// Check if there is a valid white list and if consumer email is found in white list
			if ( $whitelist_available && in_array( ( $orderemail ), $whitelist ) ) {
				// This order is white lsited
				$is_whitelisted = true;
				$cancel_score = get_option( 'wc_settings_anti_fraud_cancel_score' );
				$hold_score   = get_option( 'wc_settings_anti_fraud_hold_score' );

				// 0 in settings means to disable
				$cancel_score = 0 >= intval( $cancel_score ) ? 0 : self::invert_score( $cancel_score );
				$hold_score   = 0 >= intval( $hold_score ) ? 0 : self::invert_score( $hold_score );

				update_post_meta( $order_id, 'wc_af_score', 100 );
				update_post_meta( $order_id, 'wc_af_failed_rules', '' );
				
				if ( $score_points <= $cancel_score && 0 !== $cancel_score ) {
					$new_status = 'processing'; 
					
				} elseif ( $score_points <= $hold_score && 0 !== $hold_score ) {
					$new_status = 'processing';
					
				}
				
			} else {

				$cancel_score = get_option( 'wc_settings_anti_fraud_cancel_score' );
				$hold_score   = get_option( 'wc_settings_anti_fraud_hold_score' );

				// 0 in settings means to disable
				$cancel_score = 0 >= intval( $cancel_score ) ? 0 : self::invert_score( $cancel_score );
				$hold_score   = 0 >= intval( $hold_score ) ? 0 : self::invert_score( $hold_score );

				if(get_option( 'wc_af_enable_whitelist_payment_method' ) == 'yes') {

					if(get_option('wc_settings_anti_fraud_whitelist_payment_method') && null != get_option('wc_settings_anti_fraud_whitelist_payment_method')) {

						$whitelist_payment_method = get_option('wc_settings_anti_fraud_whitelist_payment_method');
						$payment_method = get_post_meta( $order_id, '_payment_method', true );
							
						$whitelist_payment_method = explode( ",", $whitelist_payment_method );

						if( !in_array( $payment_method, $whitelist_payment_method ) ) {
							// Check for automated action rules
							if ( $score_points <= $cancel_score && 0 !== $cancel_score ) {
								
								$new_status = 'cancelled';
								$existing_blacklist_ip = get_option('wc_settings_anti_fraudblacklist_ipaddress',false);
								if($existing_blacklist_ip != '') {
								$auto_blacklist_ip = explode( ",", $existing_blacklist_ip );
								
									if(!in_array( $ip_address, $auto_blacklist_ip )){
										$existing_blacklist_ip .= ','.$ip_address;
										update_option('wc_settings_anti_fraudblacklist_ipaddress',$existing_blacklist_ip);
									}
								}else {
									update_option('wc_settings_anti_fraudblacklist_ipaddress',$ip_address);
								}
								$is_whitelisted = false;
							} elseif ( $score_points <= $hold_score && 0 !== $hold_score ) {

								$new_status = 'on-hold';
							}
						}
					}
				} else {
					
					if ( $score_points <= $cancel_score && 0 !== $cancel_score ) {
								
						$new_status = 'cancelled';
						$existing_blacklist_ip = get_option('wc_settings_anti_fraudblacklist_ipaddress',false);
						if($existing_blacklist_ip != '') {
						$auto_blacklist_ip = explode( ",", $existing_blacklist_ip );
						
							if(!in_array( $ip_address, $auto_blacklist_ip )){
								$existing_blacklist_ip .= ','.$ip_address;
								update_option('wc_settings_anti_fraudblacklist_ipaddress',$existing_blacklist_ip);
							}
						}else {
							update_option('wc_settings_anti_fraudblacklist_ipaddress',$ip_address);
						}
						$is_whitelisted = false;
					} elseif ( $score_points <= $hold_score && 0 !== $hold_score ) {

						$new_status = 'on-hold';
					}
				}
				
				//Auto blacklist email with high risk
				$enable_auto_blacklist = get_option('wc_settings_anti_fraudenable_automatic_blacklist');
				
				$new_score_points = self::invert_score( $score_points );
				
				if('yes' == $enable_auto_blacklist && $new_score_points > get_option('wc_settings_anti_fraud_higher_risk_threshold')){
					$existing_blacklist_emails = get_option('wc_settings_anti_fraudblacklist_emails',false);
					$auto_blacklist_emails = explode( ",", $existing_blacklist_emails );
					
					if(!in_array( $orderemail, $auto_blacklist_emails )){
						$existing_blacklist_emails .= ','.$orderemail;
						
						update_option('wc_settings_anti_fraudblacklist_emails',$existing_blacklist_emails);
					}
				}
			}

			/**
			 * Filter: 'wc_anti_fraud_new_order_status' - Allow altering new order status
			 *
			 * @api array $new_status The new status
			 * @api int      $order_id Order ID
			 * @api WC_Order $order    the Order
			 */
			$new_status = apply_filters( 'wc_anti_fraud_new_order_status', $new_status, $order_id, $order );

			// Possibly update the order status
			if ( $new_status ) {
				update_post_meta( $order_id, '_wc_af_recommended_status', $new_status );

				/**
				 * Filter: 'wc_anti_fraud_skip_order_statuses' - Skip status change for these statuses
				 *
				 * @api array $statuses List of statuses to skip changes for
				 */
				$skip_status_change = apply_filters( 'wc_anti_fraud_skip_order_statuses', array( 'cancelled' ) );

				if ( ! in_array( $order->get_status(), $skip_status_change ) ) {
					$order->update_status( $new_status, __( 'Fraud check done.', 'woocommerce-anti-fraud' ) );
				} else {
					$order->add_order_note( __( 'Fraud check done.', 'woocommerce-anti-fraud' ) );
				}
			} else {
				delete_post_meta( $order_id, '_wc_af_recommended_status' );
			}

			$email_score  = get_option( 'wc_settings_anti_fraud_email_score' );
			$score_points = self::invert_score( $score_points );
			$email_nofication_status = get_option('wc_af_email_notification');
			
			if( ( $email_nofication_status == 'yes' ) && ( $score_points > $email_score ) ) {

				// This is unfortunately needed in the current WooCommerce email setup
				if ( version_compare( WC_VERSION, '2.2.11', '<=' ) ) {
					include_once( WC()->plugin_path() . '/includes/abstracts/abstract-wc-email.php' );
				} else if(version_compare( WC_VERSION, '2.2.11', '>=' ) && (version_compare( WC_VERSION, '4.0.1', '<=' ))) {
					include_once( WC()->plugin_path() . '/includes/emails/class-wc-email.php' );
					include_once( WC()->plugin_path() . '/includes/libraries/class-emogrifier.php' );
				}else {
					include_once( WC()->plugin_path() . '/includes/emails/class-wc-email.php' );
				}

				// Setup admin email
				$email = new WC_AF_Admin_Email( $order, $score_points );

				// Send admin email
				$data = $email->send_notification();
			}
			
			// Check if we need to send an admin email notification
			if ( false == $is_whitelisted ) {
				
				// This is unfortunately needed in the current WooCommerce email setup
				if ( version_compare( WC_VERSION, '2.2.11', '<=' ) ) {
					include_once( WC()->plugin_path() . '/includes/abstracts/abstract-wc-email.php' );
				} else if(version_compare( WC_VERSION, '2.2.11', '>=' ) && (version_compare( WC_VERSION, '4.0.1', '<=' ))) {
					include_once( WC()->plugin_path() . '/includes/emails/class-wc-email.php' );
					include_once( WC()->plugin_path() . '/includes/libraries/class-emogrifier.php' );
				}else {
					include_once( WC()->plugin_path() . '/includes/emails/class-wc-email.php' );
				}

				// Setup admin email
				$email = new WC_AF_Admin_Email( $order, $score_points );

				// Send admin email
				$email->send_notification();

				$order->update_status( 'cancelled', __( 'Fraud check done.', 'woocommerce-anti-fraud' ) );
			}
			
		}

		/**
		 * Function to get the ip address utilizing WC core
		 * geolocation when available.
		 *
		 * @since 1.0.7
		 * @version 1.0.7
		 */
		public static function get_ip_address() {
			if ( class_exists( 'WC_Geolocation' ) ) {
				return WC_Geolocation::get_ip_address();
			}

			return $_SERVER['REMOTE_ADDR'];
		}
		/**
		  *Function to send email if payment is done via papal		
		*/
		function paypal_email_verification($order,$score_points ) {
			
			// This is unfortunately needed in the current WooCommerce email setup
			if ( version_compare( WC_VERSION, '2.2.11', '<=' ) ) {
				include_once( WC()->plugin_path() . '/includes/abstracts/abstract-wc-email.php' );
			} else if(version_compare( WC_VERSION, '2.2.11', '>=' ) && (version_compare( WC_VERSION, '4.0.1', '<=' ))) {
				include_once( WC()->plugin_path() . '/includes/emails/class-wc-email.php' );
				include_once( WC()->plugin_path() . '/includes/libraries/class-emogrifier.php' );
			}else {
				include_once( WC()->plugin_path() . '/includes/emails/class-wc-email.php' );
			}

			$order_details = new WC_Order( $order );
			$order_id = $order_details->get_id();

			$payment_method = get_post_meta( $order_id, '_payment_method', true );

			if( $payment_method == 'ppec_paypal' ) {

				$orderemail = get_post_meta( $order_id, '_paypal_express_payer_email', true );
			
			} else {

				$orderemail = get_post_meta( $order_id, '_paypal_payer_email', true );

			}

			if(get_option('wc_settings_anti_fraud_paypal_verified_address') && null != get_option('wc_settings_anti_fraud_paypal_verified_address')){

				$paypal_verified_emails = get_option('wc_settings_anti_fraud_paypal_verified_address');

				$verified_paypal = explode( ",", $paypal_verified_emails );
				
				if( !in_array( $orderemail,$verified_paypal ) ) {
					// Setup admin email
					$email = new WC_AF_Paypal_Email( $order, $score_points );

					// Send admin email
					$email_status = $email->send_notification();
					
					if($email_status){
						update_post_meta($order,'wc_af_paypal_email_status',true);
					}
				}	
			}
			else{
				// Setup admin email
				$email = new WC_AF_Paypal_Email( $order, $score_points );

				// Send admin email
				$email_status = $email->send_notification();
				
				if($email_status){
					update_post_meta($order,'wc_af_paypal_email_status',true);
				}
			}
		}
	}
}