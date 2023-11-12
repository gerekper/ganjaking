<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'WC_AF_Score_Helper' ) ) {
	class WC_AF_Score_Helper {

		/**
		 * Get score meta
		 *
		 * @param $score_points
		 *
		 * @static
		 *
		 * @since  1.0.0
		 *
		 * @return array
		 */
		public static function get_score_meta( $score_points, $order_id = '' ) {
			// Af_Logger::debug('Score Points '.$score_points);
			// No score? return defaults
			if ( '' == $score_points ) {
				return array(
					'color' => '#adadad',
					'label' => __( 'No fraud checking has been done on this order yet.', 'woocommerce-anti-fraud' ),
				);
			}

			$meta = array(
				'label' => __( 'Low Risk', 'woocommerce-anti-fraud' ),
				'color' => '#7AD03A',
			);
			$high_threshold = get_option( 'wc_settings_anti_fraud_higher_risk_threshold' );

			$low_threshold = get_option( 'wc_settings_anti_fraud_low_risk_threshold' );
			$score_points = self::invert_score( $score_points );
			$whitelist_action = opmc_hpos_get_post_meta( $order_id, 'whitelist_action', true );
			if ( $score_points <= $high_threshold && $score_points >= $low_threshold ) {

				$meta['label'] = __( 'Medium Risk', 'woocommerce-anti-fraud' );
				$meta['color'] = ( 'user_email_whitelisted' == $whitelist_action ) ? 'grey' : '#FFAE00';
			} elseif ( $score_points > $high_threshold ) {

				$meta['label'] = __( 'High Risk', 'woocommerce-anti-fraud' );
				$meta['color'] = ( 'user_email_whitelisted' == $whitelist_action ) ? 'grey' : '#D54E21';
			}

			return $meta;
		}

		/**
		 * Invert a score
		 *
		 * @param        $score
		 *
		 * @since  1.0.0
		 *
		 * @return int
		 */
		public static function invert_score( $score ) {

			if ( $score < 0 ) {

				$score = 0;

			} else {

				$score = $score;
			}

			return 100 - $score;
		}

		/**
		 * Schedule a fraud check
		 *
		 * @param $order_id
		 *
		 * @since  1.0.0
		 */
		public function schedule_fraud_check( $order_id, $checknow = false ) {
			// Try to get the Anti Fraud score
			$score = opmc_hpos_get_post_meta( $order_id, 'wc_af_score', true );

			// Check if the order is already checked
			if ( '' != $score ) {
				return;
			}

			// Get the order
			$order = wc_get_order( $order_id );
			opmc_hpos_update_post_meta( $order_id, '_wc_af_waiting', true );

			if ( $checknow ) {

				$this->do_check( $order_id );
			} else {
				// Schedule the anti fraud check
				wp_schedule_single_event( time(), 'wc-af-check', array( 'order_id' => $order_id ) );
			}
		}

		/**
		 * Cancel scheduled fruad check
		 *
		 * @param $order_id
		 */
		public function cancel_schedule_fraud_check( $order_id ) {
			// Try to get the Anti Fraud score
			$score = opmc_hpos_get_post_meta( $order_id, 'wc_af_score', true );

			if ( $this->is_fraud_check_queued( $order_id ) ) {
				wp_clear_scheduled_hook( 'wc-af-check', array( 'order_id' => $order_id ) );

				// Get the order
				$order = wc_get_order( $order_id );
				opmc_hpos_update_post_meta( $order_id, '_wc_af_waiting', false );
			}

		}

		/**
		 * Returns a flag indicating if a fraud check has been queued but not yet completed
		 *
		 * @param $order_id
		 *
		 * @since  1.0.2
		 */
		public static function is_fraud_check_queued( $order_id ) {

			$waiting = opmc_hpos_get_post_meta( $order_id, '_wc_af_waiting', true );
			return ( ! empty( $waiting ) );

		}

		/**
		 * Returns a flag indicating if a fraud check has been completed
		 *
		 * @param $order_id
		 *
		 * @since  1.0.2
		 */
		public static function is_fraud_check_complete( $order_id ) {

			$score = opmc_hpos_get_post_meta( $order_id, 'wc_af_score', true );
			return ( ! empty( $score ) );

		}

		public function whitelistedEmail( $email ) {
			$email_whitelist = get_option( 'wc_settings_anti_fraud_whitelist' );

			$whitelist = explode( "\n", $email_whitelist );
			$whitlisted_email = false;

			if ( in_array( $email, $whitelist ) ) {
				Af_Logger::debug( 'whitelist email found' . $email );
				$whitlisted_email = true;
			}
			Af_Logger::debug( 'whitlisted_email ' . $whitlisted_email );

			return $whitlisted_email;
		}

		/**
		 * Do the actual fraud check
		 *
		 * @param $order_id
		 *
		 * @since 1.0.0
		 * @since 1.0.13 Revert points score to make clear comparison with email score.
		 */
		public function do_check( $order_id ) {

			$order = new WC_Order( $order_id );
			$order_email = $order->get_billing_email();

			$wildcard = get_option( 'wildcard_whitelist_email' );

			if ( $this->whitelistedEmail( $order_email ) || isset( $wildcard ) && 'true' == $wildcard ) {
				Af_Logger::debug( 'Fraud Check exited.' );
				opmc_hpos_update_post_meta( $order_id, 'wc_af_score', 100 );
				opmc_hpos_update_post_meta( $order_id, 'whitelist_action', 'user_email_whitelisted' );
				$order->add_order_note( __( 'Order fraud checks skipped due to whitelisted email.', 'woocommerce-anti-fraud' ) );
				return;
			}

			$whitelisted_ips = get_option('is_whitelisted_ips');

			if (isset($whitelisted_ips) && 'true' == $whitelisted_ips) {
				Af_Logger::debug('Fraud Check exited.');
				update_post_meta($order_id, 'wc_af_score', 100);
				update_post_meta($order_id, 'whitelist_action', 'user_email_whitelisted');
				$order->add_order_note(__('Order fraud checks skipped due to whitelisted customer IPs.', 'woocommerce-anti-fraud'));
				return;
			}

			$is_whitelisted_roles = get_option( 'is_whitelisted_roles' );
			$payment_methods_whitelist = get_option( 'white_payment_methods' );
			$not_whitelisted_email = get_option( 'not_whitelisted_email' );

			if ( isset( $is_whitelisted_roles ) && 'true' == $is_whitelisted_roles ) {

				Af_Logger::debug( 'Fraud Check exited.' );
				opmc_hpos_update_post_meta( $order_id, 'wc_af_score', 100 );
				opmc_hpos_update_post_meta( $order_id, 'whitelist_action', 'user_email_whitelisted' );
				$order->add_order_note( __( 'Order fraud checks skipped due to whitelisted user role.', 'woocommerce-anti-fraud' ) );
				return;

			}

			if ( isset( $payment_methods_whitelist ) && 'true' == $payment_methods_whitelist ) {

				Af_Logger::debug( 'Fraud Check exited.' );
				opmc_hpos_update_post_meta( $order_id, 'wc_af_score', 100 );
				opmc_hpos_update_post_meta( $order_id, 'whitelist_action', 'user_email_whitelisted' );
				$order->add_order_note( __( 'Order fraud checks skipped due to whitelisted payment method.', 'woocommerce-anti-fraud' ) );
				return;
			}

			if ( isset( $not_whitelisted_email ) && true == $not_whitelisted_email ) {

				Af_Logger::debug( 'Fraud Check exited.' );
				opmc_hpos_update_post_meta( $order_id, 'wc_af_score', 100 );
				opmc_hpos_update_post_meta( $order_id, 'whitelist_action', 'user_email_whitelisted' );
				$order->add_order_note( __( 'Order fraud checks skipped due to whitelisted email.', 'woocommerce-anti-fraud' ) );
				return;
			}

			$usr_verify_frm_ts = get_option( 'user_verified_from_ts' );
			if ( isset( $usr_verify_frm_ts ) && 'yes' == $usr_verify_frm_ts ) {

				Af_Logger::debug( 'Fraud Check exited.' );
				opmc_hpos_update_post_meta( $order_id, 'wc_af_score', 100 );
				opmc_hpos_update_post_meta( $order_id, 'whitelist_action', 'user_email_whitelisted' );
				$order->add_order_note( __( 'Order fraud checks skipped due to user verified from Trust Swiftly.', 'woocommerce-anti-fraud' ) );
				return;
			}

			// Create a Score object
			$score = new WC_AF_Score( $order_id );

			// Calculate score
			$score->calculate();

			// The score points
			$score_points = $score->get_score();
			// Save score to order
			opmc_hpos_update_post_meta( $order_id, 'wc_af_score', $score_points );

			// Rules in JSON
			$json_rules = array();
			if ( count( $score->get_failed_rules() ) > 0 ) {
				foreach ( $score->get_failed_rules() as $failed_rule ) {
					$json_rules[] = $failed_rule->to_json();
				}
			}

			// Save the failed rules as JSON
			opmc_hpos_update_post_meta( $order_id, 'wc_af_failed_rules', $json_rules );

			// Clear the pending flag from meta
			opmc_hpos_delete_post_meta( $order_id, '_wc_af_waiting', '' );

			// Get the order
			$order = wc_get_order( $order_id );
			$current_user_id = $order->get_user_id();
			$order_date = $order->get_date_created();
			$order_date = gmdate( 'Y-m-d', strtotime( $order_date ) );
			$currentDate = gmdate( 'Y-m-d' );
			$userDetails = $order->get_user();
			$userRole = ( '' != $userDetails ) ? $userDetails->roles[0] : '';
			$blacklist_available = false;

			$args = array(
				'post_type' => 'shop_order',
				'post_status' => 'any',
				'posts_per_page' => 1,
				'meta_query' => array(
					array(
						'key' => '_customer_user',
						'value' => $current_user_id,
					),
				),
				'orderby' => 'ID',
				'order' => 'DESC',
			);

			$loop = new WP_Query( $args );
			$orderids = array();
			while ( $loop->have_posts() ) {
				$loop->the_post();
				$orderids[] = get_the_ID();
			}
			wp_reset_postdata();
			/*
			$_card_decline_times = 0;
			$cardDeclinedMsgs = array('authentication failed', 'declined');
			if ( !empty($orderids) ) {
				global $wpdb;
				$table_perfixed = $wpdb->prefix . 'comments';
				$orderids = implode(',',$orderids);
				$sql = "SELECT * FROM $table_perfixed WHERE  `comment_post_ID` IN ($orderids) AND  `comment_type` LIKE  'order_note'";
				$results = $wpdb->get_results($sql);
				$ic = 1;
				foreach ( $results as $note ) {
					if ( (strpos($note->comment_content, 'declined') !== false) || (strpos($note->comment_content, 'authentication failed') !== false)) {
						Af_Logger::debug('Note '.$ic .' '. $note->comment_content);
						$_card_decline_times = opmc_hpos_get_post_meta( $order_id, '_card_decline_times', true );
						if( isset( $_card_decline_times ) && !empty( $_card_decline_times ) ) {
							$_card_decline_times = $_card_decline_times + 1;
							opmc_hpos_update_post_meta( $order_id, '_card_decline_times', $_card_decline_times );
						} else {
							opmc_hpos_update_post_meta( $order_id, '_card_decline_times', 1 );
						}
						break;
					} $ic++;
				}
			}
			$_card_decline_times = opmc_hpos_get_post_meta( $order_id, '_card_decline_times', true );
			Af_Logger::debug('Card declined '.$_card_decline_times.' time');*/
			$is_enable_blacklist = get_option( 'wc_settings_anti_fraudenable_automatic_email_blacklist' );
			$is_enable_ip_blacklist = get_option( 'wc_settings_anti_fraudenable_automatic_ip_blacklist' );

			if ( 'yes' == $is_enable_blacklist ) {

				$email_blacklist = get_option( 'wc_settings_anti_fraudblacklist_emails' );
				if ( '' != $email_blacklist ) {
					// String to array
					$blacklist = explode( ',', $email_blacklist );
					// Check if is valid array
					if ( is_array( $blacklist ) && count( $blacklist ) > 0 ) {

						// Trim items to be sure
						foreach ( $blacklist as $k => $v ) {
							$blacklist[ $k ] = trim( $v );
						}

						// Set $blacklist_available true
						$blacklist_available = true;
					}
				}
			}

			$whitelist_available = false;

			$is_enable_whitelist = get_option( 'wc_settings_anti_fraud_whitelist' );

			if ( '' != $is_enable_whitelist ) {

				$email_whitelist = get_option( 'wc_settings_anti_fraud_whitelist' );
				if ( '' != $email_whitelist ) {
					// String to array

					$whitelist = explode( "\n", $email_whitelist );
					// Check if is valid array
					if ( is_array( $whitelist ) && count( $whitelist ) > 0 ) {

						// Trim items to be sure
						foreach ( $whitelist as $k => $v ) {
							$whitelist[ $k ] = trim( $v );
						}

						// Set $blacklist_available true
						$whitelist_available = true;
					}
				}
			}

			// Default new status
			$new_status = null;

			// check payment method
			/*
			$payment_method = opmc_hpos_get_post_meta( $order_id, '_payment_method', true );

			if('paypal' == $payment_method || 'ppec_paypal' == $payment_method && null == opmc_hpos_get_post_meta($order_id,'wc_af_paypal_email_status')) {

				$paypal_verification = $this->paypal_email_verification($order,10);
			}*/

			// If a payment for this order has already completed, use the payment requested
			// status as the default
			$payment_requested_status = opmc_hpos_get_post_meta( $order_id, '_wc_af_post_payment_status', true );
			if ( ! empty( $payment_requested_status ) ) {
				$new_status = $payment_requested_status;
			}
			$ip_address = $order->get_customer_ip_address();
			$is_whitelisted = true;

			$wc_af_whitelist_user_roles = get_option( 'wc_af_whitelist_user_roles' );
			if ( empty( $wc_af_whitelist_user_roles ) ) {
				$wc_af_whitelist_user_roles = array();
			}
			$is_enable_whitelist_user_roles = get_option( 'wc_af_enable_whitelist_user_roles' );

			$orderemail = version_compare( WC_VERSION, '3.0', '<' ) ? $order->billing_email : $order->get_billing_email();
			// Check if there is a valid white list and if consumer email is found in white list
			if ( $whitelist_available && in_array( ( $orderemail ), $whitelist ) ) {
				Af_Logger::debug( 'Consumer email is found in white list ' );
				$is_whitelisted = true;
				$cancel_score = get_option( 'wc_settings_anti_fraud_cancel_score' );
				$hold_score   = get_option( 'wc_settings_anti_fraud_hold_score' );

				// 0 in settings means to disable
				$cancel_score = 0 >= intval( $cancel_score ) ? 0 : self::invert_score( $cancel_score );
				$hold_score   = 0 >= intval( $hold_score ) ? 0 : self::invert_score( $hold_score );

				// opmc_hpos_update_post_meta( $order_id, 'wc_af_score', 100 );
				// opmc_hpos_update_post_meta( $order_id, 'wc_af_failed_rules', '' );

				if ( $score_points <= $cancel_score && 0 !== $cancel_score ) {
					$new_status = 'processing';

				} elseif ( $score_points <= $hold_score && 0 !== $hold_score ) {
					$new_status = 'processing';

				}
				$wc_af_failed_rules = opmc_hpos_get_post_meta( $order_id, 'wc_af_failed_rules', true );
				$wc_af_failed_rules[] = '{"id":"whitelist","label":"User Email is Whitelisted."}';
				opmc_hpos_update_post_meta( $order_id, 'wc_af_failed_rules', $wc_af_failed_rules );
				opmc_hpos_update_post_meta( $order_id, 'whitelist_action', 'user_email_whitelisted' );

				$order->add_order_note( __( 'User Email is Whitelisted.', 'woocommerce-anti-fraud' ) );
			} else if ( ( in_array( $userRole, $wc_af_whitelist_user_roles ) ) && ( 'yes' == $is_enable_whitelist_user_roles ) ) {

				Af_Logger::debug( $userRole . ' is available in user role white list ' );
				$is_whitelisted = true;
				$cancel_score = get_option( 'wc_settings_anti_fraud_cancel_score' );
				$hold_score   = get_option( 'wc_settings_anti_fraud_hold_score' );

				// 0 in settings means to disable
				$cancel_score = 0 >= intval( $cancel_score ) ? 0 : self::invert_score( $cancel_score );
				$hold_score   = 0 >= intval( $hold_score ) ? 0 : self::invert_score( $hold_score );

				opmc_hpos_update_post_meta( $order_id, 'wc_af_score', 100 );
				opmc_hpos_update_post_meta( $order_id, 'wc_af_failed_rules', '' );

				$order->add_order_note( __( $userRole . ' User Role is Whitelisted.', 'woocommerce-anti-fraud' ) );

				if ( $score_points <= $cancel_score && 0 !== $cancel_score ) {
					$new_status = 'processing';

				} elseif ( $score_points <= $hold_score && 0 !== $hold_score ) {
					$new_status = 'processing';

				}
			} else {

				Af_Logger::debug( 'Checking for payment method whitelist ' );

				$cancel_score = get_option( 'wc_settings_anti_fraud_cancel_score' );
				$hold_score   = get_option( 'wc_settings_anti_fraud_hold_score' );

				// 0 in settings means to disable
				$cancel_score = 0 >= intval( $cancel_score ) ? 0 : self::invert_score( $cancel_score );
				$hold_score   = 0 >= intval( $hold_score ) ? 0 : self::invert_score( $hold_score );

				if ( get_option( 'wc_af_enable_whitelist_payment_method' ) == 'yes' ) {

					if ( get_option( 'wc_settings_anti_fraud_whitelist_payment_method' ) && null != get_option( 'wc_settings_anti_fraud_whitelist_payment_method' ) ) {

						$whitelist_payment_method = get_option( 'wc_settings_anti_fraud_whitelist_payment_method' );
						$payment_method = opmc_hpos_get_post_meta( $order_id, '_payment_method', true );
						if ( ! in_array( $payment_method, $whitelist_payment_method ) ) {
							// Check for automated action rules
							if ( $score_points <= $cancel_score && 0 !== $cancel_score ) {

								$new_status = 'cancelled';
								Af_Logger::debug( 'Status changed to ' . $new_status );
								$is_whitelisted = false;
							} elseif ( $score_points <= $hold_score && 0 !== $hold_score ) {

								$new_status = 'on-hold';
							}
						} else {
							if ( 'cod' == $payment_method || 'bacs' == $payment_method || 'cheque' == $payment_method ) {

								$is_whitelisted = true;
								$new_status = 'on-hold';
								Af_Logger::debug( 'Payment method ' . $payment_method . ' available in whitelist. Status changed to ' . $new_status . ' because payment method is ' . $payment_method );
							} else {

								$is_whitelisted = true;
								// don't change status, use the default status assigned by payment plugin
								$new_status = '';
								$order->add_order_note( __( 'Fraud check done.', 'woocommerce-anti-fraud' ) );
								$order->add_order_note( __( 'Payment method ' . $payment_method . ' available in whitelist. Status will be used from payment method plugin', 'woocommerce-anti-fraud' ) );
								Af_Logger::debug( 'Payment method ' . $payment_method . ' available in whitelist. Status will be used from payment method plugin' );
							}
						}
					}
				} else {

					if ( $score_points <= $cancel_score && 0 !== $cancel_score ) {

						$new_status = 'cancelled';
						$is_whitelisted = false;
					} elseif ( $score_points <= $hold_score && 0 !== $hold_score ) {

						$new_status = 'on-hold';
					}
					Af_Logger::debug( 'Status changed to ' . $new_status );
				}

				// Auto blacklist email with high risk
				$enable_auto_blacklist = get_option( 'wc_settings_anti_fraudenable_automatic_blacklist' );

				$new_score_points = self::invert_score( $score_points );

				if ( 'yes' == $enable_auto_blacklist && $new_score_points > get_option( 'wc_settings_anti_fraud_higher_risk_threshold' ) ) {
					$existing_blacklist_emails = get_option( 'wc_settings_anti_fraudblacklist_emails', false );
					$auto_blacklist_emails = explode( ',', $existing_blacklist_emails );

					if ( ! in_array( $orderemail, $auto_blacklist_emails ) ) {
						$existing_blacklist_emails .= ',' . $orderemail;

						$wildcard = get_option( 'wildcard_whitelist_email' );

						if ( isset( $wildcard ) && 'true' != $wildcard ) {
							update_option( 'wc_settings_anti_fraudblacklist_emails', $existing_blacklist_emails );
						}
					}
				}
				// Blacklist ip if enabled
				if ( 'yes' == $is_enable_ip_blacklist && $new_score_points > get_option( 'wc_settings_anti_fraud_higher_risk_threshold' ) ) {
					Af_Logger::debug( 'IP Blacklist ' . $ip_address );
					$existing_blacklist_ip = get_option( 'wc_settings_anti_fraudblacklist_ipaddress', false );
					if ( '' != $existing_blacklist_ip ) {
						$auto_blacklist_ip = explode( ',', $existing_blacklist_ip );

						if ( ! in_array( $ip_address, $auto_blacklist_ip ) ) {
							$existing_blacklist_ip .= ',' . $ip_address;
							update_option( 'wc_settings_anti_fraudblacklist_ipaddress', $existing_blacklist_ip );
						}
					} else {
						update_option( 'wc_settings_anti_fraudblacklist_ipaddress', $ip_address );
					}
				}

				$max_order_per_ip = new WC_AF_Rule_Velocities();
				$is_max = $max_order_per_ip->is_risk( $order );
				if ( 'yes' === get_option( 'wc_af_attempt_count_check' ) && true === $is_max && false === $is_whitelisted ) {
					$new_status = 'cancelled';
					$order->add_order_note( __( 'Max order limit reched from same IP.', 'woocommerce-anti-fraud' ) );
					Af_Logger::debug( 'Max Order Reched  and Order: ' . $new_status );
				}
			}

			$order_limit_enabled = get_option( 'wc_af_limit_order_count', 'no' );
			$is_update_status_active = get_option( 'wc_af_fraud_update_state' );

			Af_Logger::debug( 'Order Limit enabled :' . print_r( $order_limit_enabled, true ) );

			if ( 'yes' === $order_limit_enabled ) {

				$orders_allowed_limit = get_option( 'wc_af_allowed_order_limit' );
				$limit_time_start = get_option( 'wc_af_limit_time_start' );
				$limit_time_end = get_option( 'wc_af_limit_time_end' );
				// $is_update_status_active = get_option('wc_af_fraud_update_state');

				// $start_time = new DateTime( $limit_time_start, new DateTimeZone(wp_timezone_string()) );
				// $end_time = new DateTime( $limit_time_end, new DateTimeZone(wp_timezone_string()) );
				// $now = new DateTime( 'NOW', new DateTimeZone(wp_timezone_string()) );

				$time_zone = new \DateTimeZone( wp_timezone_string() );

				$start_time = new \DateTime( $limit_time_start, $time_zone );
				$end_time = new \DateTime( $limit_time_end, $time_zone );
				$now = new \DateTime( 'NOW', $time_zone );

				if ( ( $now >= $start_time ) && ( $now <= $end_time ) ) {

					$orders_between = wc_get_orders(
						array(
							'limit'               => -1,
							'type'                => wc_get_order_types( 'order-count' ),
							'date_created'          => $start_time->getTimestamp() . '...' . $end_time->getTimestamp(),
						)
					);

					if ( $orders_allowed_limit <= count( $orders_between ) ) {
						
						$new_status = 'cancelled';
						$order->add_order_note( __( 'Max Order Limit between time reached.', 'woocommerce-anti-fraud' ) );
						Af_Logger::debug( 'Max Order Limit between time reached: ' . $new_status );
					}
				}
			}

			/**
			 * Filter: 'wc_anti_fraud_new_order_status' - Allow altering new order status
			 *
			 * @api array $new_status The new status
			 * @api int      $order_id Order ID
			 * @api WC_Order $order    the Order
			 *
			 * @since 1.0.0
			 */
			$new_status = apply_filters( 'wc_anti_fraud_new_order_status', $new_status, $order_id, $order );
			Af_Logger::debug( 'wc_anti_fraud_new_order_status  ' . $new_status );

			// Possibly update the order status
			if ( $new_status ) {
				opmc_hpos_update_post_meta( $order_id, '_wc_af_recommended_status', $new_status );

				/**
				 * Filter: 'wc_anti_fraud_skip_order_statuses' - Skip status change for these statuses
				 *
				 * @api array $statuses List of statuses to skip changes for
				 *
				 * @since 1.0.0
				 */
				$skip_status_change = apply_filters( 'wc_anti_fraud_skip_order_statuses', array( 'cancelled' ) );

				Af_Logger::debug( 'is_update_status_active : ' . $is_update_status_active );
				Af_Logger::debug( 'payment_requested_status : ' . print_r( $payment_requested_status, true ) );

				if ( ! in_array( $order->get_status(), $skip_status_change ) && 'yes' == $is_update_status_active ) {
					if ( ! empty( $payment_requested_status ) ) {
						$order->update_status( $payment_requested_status, __( 'Fraud check done.', 'woocommerce-anti-fraud' ) );
					} else {
						$order->update_status( $new_status, __( 'Fraud check done.', 'woocommerce-anti-fraud' ) );
					}
				} else {
					$order->add_order_note( __( 'Fraud check done.', 'woocommerce-anti-fraud' ) );
				}
			} else {
				opmc_hpos_delete_post_meta( $order_id, '_wc_af_recommended_status', '' );
			}

			$email_score  = get_option( 'wc_settings_anti_fraud_email_score' );
			$score_points = self::invert_score( $score_points );
			$email_nofication_status = get_option( 'wc_af_email_notification' );

			if ( ( 'yes' == $email_nofication_status ) && ( $score_points > $email_score ) ) {

				// This is unfortunately needed in the current WooCommerce email setup
				if ( version_compare( WC_VERSION, '2.2.11', '<=' ) ) {
					include_once( WC()->plugin_path() . '/includes/abstracts/abstract-wc-email.php' );
				} else {
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
				} else {
					include_once( WC()->plugin_path() . '/includes/emails/class-wc-email.php' );
				}

				// Setup admin email
				$email = new WC_AF_Admin_Email( $order, $score_points );

				// Send admin email
				$email->send_notification();

				$is_update_status_active = get_option( 'wc_af_fraud_update_state' );
				if ( 'yes' == $is_update_status_active ) {
					if ( ! empty( $payment_requested_status ) ) {
						$order->update_status( $payment_requested_status, __( 'Fraud check done.', 'woocommerce-anti-fraud' ) );
					} else {
						$order->update_status( $new_status, __( 'Fraud check done.', 'woocommerce-anti-fraud' ) );
					}
				} else {
					$order->add_order_note( __( 'Fraud check done.', 'woocommerce-anti-fraud' ) );
					// $order->update_status( 'cancelled', __( 'Fraud check done.', 'woocommerce-anti-fraud' ) );
				}
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

			return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( $_SERVER['REMOTE_ADDR'] ) : '';
		}
		/**
		 * Function to send email if payment is done via papal
		 */
		public function paypal_email_verification( $order, $score_points ) {
			Af_Logger::debug( 'paypal_email_verification' );
			// This is unfortunately needed in the current WooCommerce email setup
			if ( version_compare( WC_VERSION, '2.2.11', '<=' ) ) {
				include_once( WC()->plugin_path() . '/includes/abstracts/abstract-wc-email.php' );
			} else {
				include_once( WC()->plugin_path() . '/includes/emails/class-wc-email.php' );
			}

			$order_details = new WC_Order( $order );
			$order_id = $order_details->get_id();

			$payment_method = opmc_hpos_get_post_meta( $order_id, '_payment_method', true );

			if ( 'ppec_paypal' == $payment_method ) {

				$orderemail = opmc_hpos_get_post_meta( $order_id, '_paypal_express_payer_email', true );

			} else {

				$orderemail = opmc_hpos_get_post_meta( $order_id, '_paypal_payer_email', true );

			}

			if ( get_option( 'wc_settings_anti_fraud_paypal_verified_address' ) && null != get_option( 'wc_settings_anti_fraud_paypal_verified_address' ) ) {

				$paypal_verified_emails = get_option( 'wc_settings_anti_fraud_paypal_verified_address' );

				$verified_paypal = explode( ',', $paypal_verified_emails );

				if ( ! in_array( $orderemail, $verified_paypal ) ) {
					// Setup admin email
					$email = new WC_AF_Paypal_Email( $order, $score_points );

					// Send admin email
					$email_status = $email->send_notification();

					if ( $email_status ) {
						opmc_hpos_update_post_meta( $order, 'wc_af_paypal_email_status', true );
						Af_Logger::debug( 'paypal_email_verification notification send to  ' . $orderemail );
					}
				}
			} else {
				// Setup admin email
				$email = new WC_AF_Paypal_Email( $order, $score_points );

				// Send admin email
				$email_status = $email->send_notification();

				if ( $email_status ) {
					opmc_hpos_update_post_meta( $order, 'wc_af_paypal_email_status', true );
					Af_Logger::debug( 'paypal_email_verification notification send to  ' . $orderemail );
				}
			}
		}
	}
}
