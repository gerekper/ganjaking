<?php

if ( ! class_exists( 'RewardPointsOrder' ) ) {

	class RewardPointsOrder {

		/**
		 * Order ID.
		 *
		 * @var int
		 */
		protected $order_id;

		/**
		 * Order Object.
		 *
		 * @var obj
		 */
		protected $order;

		/**
		 * Previous Order Points.
		 *
		 * @var bool
		 */
		protected $apply_previous_order_points;

		public function __construct( $order_id = 0, $apply_previous_order_points = 'no' ) {
			$this->order_id                    = $order_id;
			$this->order                       = wc_get_order( $order_id );
			$this->apply_previous_order_points = $apply_previous_order_points;
		}

		public function check_point_restriction( $getpaymentgatewayused, $pointsredeemed, $event_slug, $orderuserid, $nomineeid, $referrer_id, $productid, $variationid, $reasonindetail ) {
			return self::check_point_restriction_of_user( $getpaymentgatewayused, $pointsredeemed, $event_slug, $orderuserid, $nomineeid, $referrer_id, $productid, $variationid, $reasonindetail );
		}

		/**
		 * Check point restriction for user.
		 *
		 * @param string $method  Method to Update.
		 */
		private function check_point_restriction_of_user( $getpaymentgatewayused, $pointsredeemed, $event_slug, $orderuserid, $nomineeid, $referrer_id, $productid, $variationid, $reasonindetail ) {
			$restrictuserpoints = get_option( 'rs_max_earning_points_for_user' );
			if ( ! empty( $restrictuserpoints ) ) {
				$PointsData   = new RS_Points_Data( $orderuserid );
				$getoldpoints = $PointsData->total_available_points();
				if ( $getoldpoints <= $restrictuserpoints ) {
					$totalpointss = $getoldpoints + $getpaymentgatewayused;
					if ( $totalpointss <= $restrictuserpoints ) {
						$valuestoinsert = array(
							'pointstoinsert'    => $getpaymentgatewayused,
							'event_slug'        => $event_slug,
							'user_id'           => $orderuserid,
							'referred_id'       => $referrer_id,
							'product_id'        => $productid,
							'variation_id'      => $variationid,
							'reasonindetail'    => $reasonindetail,
							'nominee_id'        => $nomineeid,
							'totalearnedpoints' => $getpaymentgatewayused,
						);
						$this->total_points_management( $valuestoinsert );
						if ( '' != $nomineeid ) {
							$table_args = array(
								'user_id'       => $nomineeid,
								'checkpoints'   => 'PPRPFNP',
								'nomineeid'     => $orderuserid,
								'nomineepoints' => $getpaymentgatewayused,
							);
							RSPointExpiry::record_the_points( $table_args );
						}
						if ( '' != $referrer_id && ( 'PPRRPG' != $event_slug || 'PPRRPGCT' != $event_slug ) ) {
							$previouslog = get_option( 'rs_referral_log' );
							RS_Referral_Log::update_referral_log( $orderuserid, $referrer_id, $getpaymentgatewayused, array_filter( (array) $previouslog ) );
						}
						if ( 'RRRP' == $event_slug ) {
							$previouslog = get_option( 'rs_referral_log' );
							RS_Referral_Log::update_referral_log( $orderuserid, $referrer_id, $getpaymentgatewayused, array_filter( (array) $previouslog ) );
							update_user_meta( $referrer_id, '_rs_i_referred_by', $orderuserid );
						}
						if ( 'RPCPAR' == $event_slug || 'RPFPAC' == $event_slug ) {
							update_user_meta( $orderuserid, 'usercommentpage' . $productid, '1' );
						}

						if ( 'RPCPR' == $event_slug || 'RPFPOC' == $event_slug ) {
							update_user_meta( $orderuserid, 'usercommentpost' . $productid, '1' );
						}

						if ( 'RPPR' == $event_slug ) {
							update_user_meta( $orderuserid, 'userreviewed' . $productid, '1' );
						}

						if ( 'RPGV' == $event_slug ) {
							$Msg = str_replace( '[giftvoucherpoints]', $getpaymentgatewayused, get_option( 'rs_voucher_redeem_success_message' ) );
							echo do_shortcode( $Msg );
						}
					} else {
						$insertpoints = $restrictuserpoints - $getoldpoints;
						$this->points_management( $insertpoints, $pointsredeemed, 'MREPFU', $orderuserid );
					}
				} else {
					$this->points_management( 0, $pointsredeemed, 'MREPFU', $orderuserid );
				}
			} else {
				$valuestoinsert = array(
					'pointstoinsert'    => $getpaymentgatewayused,
					'event_slug'        => $event_slug,
					'user_id'           => $orderuserid,
					'referred_id'       => $referrer_id,
					'product_id'        => $productid,
					'variation_id'      => $variationid,
					'nominee_id'        => $nomineeid,
					'totalearnedpoints' => $getpaymentgatewayused,
				);
				$this->total_points_management( $valuestoinsert );
				if ( '' != $nomineeid ) {
					$table_args = array(
						'user_id'       => $nomineeid,
						'checkpoints'   => 'PPRPFNP',
						'nomineeid'     => $orderuserid,
						'nomineepoints' => $getpaymentgatewayused,
					);
					RSPointExpiry::record_the_points( $table_args );
				}
				if ( '' != $referrer_id && ( 'PPRRPG' != $event_slug || 'PPRRPGCT' != $event_slug ) ) {
					$previouslog = get_option( 'rs_referral_log' );
					RS_Referral_Log::update_referral_log( $orderuserid, $referrer_id, $getpaymentgatewayused, array_filter( (array) $previouslog ) );
				}
				if ( 'RRRP' == $event_slug ) {
					$previouslog = get_option( 'rs_referral_log' );
					RS_Referral_Log::update_referral_log( $orderuserid, $referrer_id, $getpaymentgatewayused, array_filter( (array) $previouslog ) );
					update_user_meta( $referrer_id, '_rs_i_referred_by', $orderuserid );
				}
				if ( 'RPCPAR' == $event_slug ) {
					update_user_meta( $orderuserid, 'usercommentpage' . $productid, '1' );
				}

				if ( 'RPCPR' == $event_slug ) {
					update_user_meta( $orderuserid, 'usercommentpost' . $productid, '1' );
				}

				if ( 'RPPR' == $event_slug ) {
					update_user_meta( $orderuserid, 'userreviewed' . $productid, '1' );
				}

				if ( 'RPGV' == $event_slug ) {
					$Msg = str_replace( '[giftvoucherpoints]', $getpaymentgatewayused, get_option( 'rs_voucher_redeem_success_message' ) );
					echo do_shortcode( $Msg );
				}
			}
		}

		/**
		 * Get total earned points.
		 */
		private function get_total_earned_points() {
			global $wpdb;
			$gettotalearnedpoints = $wpdb->get_results( $wpdb->prepare( "SELECT SUM((earnedpoints)) as availablepoints FROM {$wpdb->prefix}rspointexpiry WHERE orderid = %d", $this->order_id ), ARRAY_A );
			$totalearnedpoints    = ( null != $gettotalearnedpoints[0]['availablepoints'] ) ? $gettotalearnedpoints[0]['availablepoints'] : 0;
			return $totalearnedpoints;
		}

		/**
		 * Update Earning Points for User.
		 *
		 * @param string $method  Method to Update.
		 */
		public function update_earning_points_for_user( $method = '' ) {
			if ( ! $this->is_user_banned() ) {
				return;
			}

			if ( ! $this->check_restriction() ) {
				return;
			}

			if ( ! $this->award_earning_point_only_once() ) {
				return;
			}

			if ( ! $this->award_points_only_once_for_referral_system() ) {
				return;
			}

			$order_id = $this->order_id;
			$order    = wc_get_order( $order_id );
			if ( 'yes' === get_option( 'rs_product_purchase_activated' ) && 'yes' === $order->get_meta( 'rs_prevent_point_for_first_purchase' ) ) {
				return;
			}

			// Restrict Earn points for Selected Payment Gateway.
			$payment_method                     = $order->get_payment_method();
			$default_value_for_restrict_payment = ( 'yes' === get_option( 'rs_disable_point_if_reward_points_gateway', 'no' ) ) ? array( 'reward_gateway' ) : array();
			$selected_payment_method            = (array) get_option( 'rs_select_payment_gateway_for_restrict_reward', $default_value_for_restrict_payment );
			if ( 'yes' === get_option( 'rs_product_purchase_activated' ) && ! empty( $selected_payment_method ) && in_array( $payment_method, $selected_payment_method ) ) {
				return;
			}

			global $wpdb;
			$PointsTable             = $wpdb->prefix . 'rspointexpiry';
			$enabledisablemaxpoints  = get_option( 'rs_enable_disable_max_earning_points_for_user' );
			$orderobj                = srp_order_obj( $this->order );
			$orderuserid             = $orderobj['order_userid'];
			$product_purchase_points = array();
			/**
			 * Hook:rs_perform_action_for_order.
			 *
			 * @since 1.0
			 */
			do_action( 'rs_perform_action_for_order', $order_id );
			if ( 'yes' === get_option( 'rs_reward_action_activated' ) && 'yes' !== $order->get_meta( 'srp_gateway_points_awarded' ) ) {
				$payment_method = $orderobj['payment_method'];
				$GatewayPoints  = points_for_payment_gateways( $order_id, $orderuserid, $payment_method );
				if ( $GatewayPoints > 0 ) {
					if ( 'yes' === $enabledisablemaxpoints ) {
						$this->check_point_restriction( $GatewayPoints, 0, 'RPG', $orderuserid, '', '', '', '', '' );
					} else {
						$valuestoinsert = array(
							'pointstoinsert'    => $GatewayPoints,
							'event_slug'        => 'RPG',
							'user_id'           => $orderuserid,
							'totalearnedpoints' => $GatewayPoints,
						);
						$this->total_points_management( $valuestoinsert );
					}
					$order->update_meta_data( 'srp_gateway_points_awarded', 'yes' );
					/**
					 * Hook:fp_reward_point_for_using_gateways.
					 *
					 * @since 1.0
					 */
					do_action( 'fp_reward_point_for_using_gateways' );
				}
			}
			/* Reward Points For Using Payment Gateway Method - End */

			/* Reward Points For Purchasing the Product when modify point based on coupon - Start */
			$reward_points_based_on = get_option( 'rs_award_points_for_cart_or_product_total' );
			$points_refer           = array();
			$modified_point_list    = $order->get_meta( 'points_for_current_order' );
			if ( ! srp_check_is_array( $modified_point_list ) && class_exists( 'RSPointExpiry' ) ) {
				RSPointExpiry::award_point_for_manual_order( $order, false );
				$modified_point_list = $order->get_meta( 'points_for_current_order' );
			}

			if ( 'yes' === get_option( 'rs_enable_disable_reward_point_based_coupon_amount' ) &&
				'no' === $this->apply_previous_order_points &&
				srp_check_is_array( $order->get_coupon_codes() ) &&
				'yes' !== get_option( 'rs_enable_redeem_for_order' )
			) {
				if ( srp_check_is_array( $modified_point_list ) ) {
					$NomineeIdsinMyaccount    = ( 'yes' === get_option( 'rs_nominee_activated' ) ) ? get_user_meta( $orderuserid, 'rs_selected_nominee', true ) : '';
					$EnableNomineeinMyaccount = ( 'yes' === get_option( 'rs_nominee_activated' ) ) ? get_user_meta( $orderuserid, 'rs_enable_nominee', true ) : 'no';
					$NomineeIdsinCheckout     = ( 'yes' === get_option( 'rs_nominee_activated' ) ) ? $order->get_meta( 'rs_selected_nominee_in_checkout' ) : '';
					$order_total              = $order->get_total();
					$minimum_cart_total       = get_option( 'rs_minimum_cart_total_for_earning' );
					$maximum_cart_total       = get_option( 'rs_maximum_cart_total_for_earning' );
					$reasonindetail           = 'yes' === $this->apply_previous_order_points ? $method : '';

					foreach ( $modified_point_list as $product_id => $points ) {
						$productid                 = $product_id;
						$variationid               = $product_id;
						$productlevelrewardpointss = $points;
						if ( '' !== $minimum_cart_total && 0 !== $minimum_cart_total ) {
							if ( $order_total < $minimum_cart_total ) {
								$productlevelrewardpointss = 0;
							}
						}
						if ( '' !== $maximum_cart_total && 0 !== $maximum_cart_total ) {
							if ( $order_total > $maximum_cart_total ) {
								$productlevelrewardpointss = 0;
							}
						}
						if ( ! empty( $productlevelrewardpointss ) ) {
							include 'frontend/rs_insert_points_for_product_purchase.php';
						}
					}
				}
			} else {
				foreach ( $order->get_items() as $item ) {

					$Productid     = $item['product_id'];
					$Variationid   = $item['variation_id'];
					$itemquantity  = $item['qty'];
					$ProductObj    = srp_product_object( $Productid );
					$payment_price = 0;
					$AwardPoints   = true;
					if ( class_exists( 'SUMOPaymentPlans' ) ) {
						$Id            = empty( $item['variation_id'] ) ? $item['product_id'] : $item['variation_id'];
						$payment_price = ( is_payment_product( $order_id, $Id ) && is_final_payment( $order_id ) ) ? get_payment_product_price( $order_id ) : 0;

						if ( $payment_price ) {
							$AwardPoints = ( is_payment_product( $order_id, $Id ) && is_final_payment( $order_id ) ) ? true : false;
						}
					}

					if ( ! $AwardPoints ) {
						continue;
					}

					if ( 'yes' === get_option( 'rs_product_purchase_activated' ) ) {

						if ( ! block_points_for_renewal_order_sumo_subscriptions( $order_id, get_option( 'rs_award_point_for_renewal_order' ) ) ) {
							continue;
						}

						if ( ! rs_block_points_for_renewal_order_wc_subscriptions( $order_id, get_option( 'rs_award_product_purchase_point_wc_renewal_order' ) ) ) {
							continue;
						}

						$reasonindetail = 'yes' === $this->apply_previous_order_points ? $method : '';
						if ( 'yes' === get_option( 'rs_enable_product_category_level_for_product_purchase' ) ) {
							$product_purchase_points[] = $this->rs_insert_the_selected_level_in_reward_points( $enabledisablemaxpoints, 0, $Productid, $Variationid, $itemquantity, $orderuserid, $equearnamt   = '', $equredeemamt = '', $order_id, $item, $reasonindetail, $payment_price );
						} elseif ( '1' === $reward_points_based_on ) {
								$product_purchase_points[] = $this->rs_insert_the_selected_level_in_reward_points( $enabledisablemaxpoints, 0, $Productid, $Variationid, $itemquantity, $orderuserid, $equearnamt   = '', $equredeemamt = '', $order_id, $item, $reasonindetail, $payment_price );
						}

						/**
						 * Hook:fp_reward_point_for_product_purchase.
						 *
						 * @since 1.0
						 */
						do_action( 'fp_reward_point_for_product_purchase' );
					}

					$args = array(
						'product_id'    => $Productid,
						'variation_id'  => $Variationid,
						'quantity'      => $itemquantity,
						'product'       => $ProductObj,
						'item'          => $item,
						'payment_price' => $payment_price,
					);

					// Referral system product total type.
					$points_refer[ $Productid ] = $this->get_reward_points_based_on_product_total_in_referral_system( $args );
				}
			}
			/* Reward Points For Purchasing the Product when modify point based on coupon - End */

			/* Reward Points For Purchasing the Product - Start */
			if ( 'yes' === get_option( 'rs_referral_activated' ) ) {

				if ( ! block_points_for_renewal_order_sumo_subscriptions( $this->order_id, get_option( 'rs_award_referral_point_for_renewal_order' ) ) ) {
					return;
				}

				if ( ! rs_block_points_for_renewal_order_wc_subscriptions( $this->order_id, get_option( 'rs_award_referral_point_wc_renewal_order' ) ) ) {
					return;
				}

				if ( srp_check_is_array( $points_refer ) ) {
					$order->update_meta_data( 'rsgetreferalpoints', $points_refer );
				}

				// Referral system cart total type.
				$this->referral_system_based_on_cart_total();
			}

			if ( 'yes' === get_option( 'rs_product_purchase_activated' ) ) {

				if ( ! block_points_for_renewal_order_sumo_subscriptions( $order_id, get_option( 'rs_award_point_for_renewal_order' ) ) ) {
					return;
				}

				if ( ! rs_block_points_for_renewal_order_wc_subscriptions( $order_id, get_option( 'rs_award_product_purchase_point_wc_renewal_order' ) ) ) {
					return;
				}

				if ( 'yes' === get_option( 'rs_enable_first_purchase_reward_points' ) ) {
					$this->rs_insert_points_based_on_first_purchase( $enabledisablemaxpoints, $orderuserid );
				}

				// Award Points Based on Cart Total.
				if ( '2' === $reward_points_based_on ) {
					$product_purchase_points[] = $this->rs_insert_points_based_on_cart_total( $enabledisablemaxpoints, 0, $Productid, $Variationid, $itemquantity, $orderuserid, '', '', $order_id, $item, $reasonindetail, $payment_price );
				}

				// Award Points Based on Range Based.
				if ( '3' === $reward_points_based_on ) {
					$product_purchase_points[] = $this->rs_insert_points_based_on_range_based_total( $enabledisablemaxpoints, 0, $Productid, $Variationid, $itemquantity, $orderuserid, '', '', $order_id, $item, $reasonindetail, $payment_price );
				}
			}

			$overall_product_purchase_points = array_sum( $product_purchase_points );
			if ( $overall_product_purchase_points && 'yes' === $order->get_meta( 'reward_points_awarded' ) ) {
				/**
				 * Hook:fp_product_purchase_points_awarded_in_order.
				 *
				 * @since 1.0
				 */
				do_action( 'fp_product_purchase_points_awarded_in_order', $order_id, $overall_product_purchase_points );
			}

			$this->send_sms_and_email_in_product_purchase();
		}

		public function rs_insert_points_based_on_first_purchase( $enabledisablemaxpoints, $orderuserid ) {
			if ( ! $orderuserid ) {
				return;
			}

			$OrderCount = get_posts(
				array(
					'numberposts' => -1,
					'meta_key'    => '_customer_user',
					'meta_value'  => $orderuserid,
					'post_type'   => 'shop_order',
					'post_status' => array( 'wc-pending', 'wc-processing', 'wc-on-hold', 'wc-completed' ),
				)
			);

			if ( count( $OrderCount ) > 1 ) {
				return;
			}

			$PointsForFristPurchase = rs_get_first_purchase_point( $this->order );
			if ( empty( $PointsForFristPurchase ) ) {
				return;
			}

			if ( $this->check_redeeming_in_order() ) {
				return;
			}

			if ( 'yes' == $enabledisablemaxpoints ) {
				$this->check_point_restriction( $PointsForFristPurchase, 0, 'PFFP', $orderuserid, '', '', '', '', '' );
			} else {
				$valuestoinsert = array(
					'pointstoinsert'    => $PointsForFristPurchase,
					'event_slug'        => 'PFFP',
					'user_id'           => $orderuserid,
					'totalearnedpoints' => $PointsForFristPurchase,
				);
				$this->total_points_management( $valuestoinsert );
			}
		}

		public function rs_insert_points_based_on_range_based_total( $enabledisablemaxpoints, $pointsredeemed, $productid, $variationid, $itemquantity, $orderuserid, $equearnamt, $equredeemamt, $order_id, $item, $reasonindetail, $payment_price ) {

			if ( ! $orderuserid ) {
				return;
			}

			if ( 'yes' === get_option( 'rs_enable_product_category_level_for_product_purchase' ) ) {
				return;
			}

			if ( ! rs_restrict_product_purchase_point_when_free_shipping_is_enabled( $order_id ) ) {
				return;
			}

			if ( $this->check_redeeming_in_order() ) {
				return;
			}

			$order                     = wc_get_order( $order_id );
			$event_slug                = 'PPRPBCT';
			$NomineeIdsinMyaccount     = ( 'yes' === ( get_option( 'rs_nominee_activated' ) ) ) ? get_user_meta( $orderuserid, 'rs_selected_nominee', true ) : '';
			$EnableNomineeinMyaccount  = ( 'yes' === ( get_option( 'rs_nominee_activated' ) ) ) ? get_user_meta( $orderuserid, 'rs_enable_nominee', true ) : 'no';
			$NomineeIdsinCheckout      = ( 'yes' === ( get_option( 'rs_nominee_activated' ) ) ) ? $order->get_meta( 'rs_selected_nominee_in_checkout' ) : '';
			$productlevelrewardpointss = $order->get_meta( 'rs_points_for_current_order_based_on_range' );
			if ( 0 !== $productlevelrewardpointss ) {
				include 'frontend/rs_insert_points_for_product_purchase.php';
			}

			return $productlevelrewardpointss;
		}

		public function rs_insert_points_based_on_cart_total( $enabledisablemaxpoints, $pointsredeemed, $productid, $variationid, $itemquantity, $orderuserid, $equearnamt, $equredeemamt, $order_id, $item, $reasonindetail, $payment_price ) {

			if ( ! $orderuserid ) {
				return;
			}

			if ( 'yes' === get_option( 'rs_enable_product_category_level_for_product_purchase' ) ) {
				return;
			}

			if ( '2' === get_option( 'rs_enable_cart_total_reward_points' ) ) {
				return;
			}

			if ( ! rs_restrict_product_purchase_point_when_free_shipping_is_enabled( $order_id ) ) {
				return;
			}

			if ( $this->check_redeeming_in_order() ) {
				return;
			}

			$event_slug                = 'PPRPBCT';
			$NomineeIdsinMyaccount     = ( 'yes' === get_option( 'rs_nominee_activated' ) ) ? get_user_meta( $orderuserid, 'rs_selected_nominee', true ) : '';
			$EnableNomineeinMyaccount  = ( 'yes' === get_option( 'rs_nominee_activated' ) ) ? get_user_meta( $orderuserid, 'rs_enable_nominee', true ) : 'no';
			$NomineeIdsinCheckout      = ( 'yes' === get_option( 'rs_nominee_activated' ) ) ? $this->order->get_meta( 'rs_selected_nominee_in_checkout' ) : '';
			$shipping_cost             = $this->order->shipping_total + $this->order->shipping_tax;
			$productlevelrewardpointss = get_reward_points_based_on_cart_total( $this->order->get_total(), $shipping_cost, $this->order->get_user_id() );

			if ( 0 !== $productlevelrewardpointss ) {
				include 'frontend/rs_insert_points_for_product_purchase.php';
			}

			return $productlevelrewardpointss;
		}

		public function rs_insert_the_selected_level_in_reward_points( $enabledisablemaxpoints, $pointsredeemed, $productid, $variationid, $itemquantity, $orderuserid, $equearnamt, $equredeemamt, $order_id, $item, $reasonindetail, $payment_price ) {
			if ( 'yes' === block_points_for_salepriced_product( $productid, $variationid ) ) {
				return;
			}

			if ( ! rs_restrict_product_purchase_point_when_free_shipping_is_enabled( $order_id ) ) {
				return;
			}

			$min_quantity = rs_get_minimum_quantity_based_on_product_total( $productid, $variationid );
			if ( $min_quantity && $itemquantity < $min_quantity ) {
				return;
			}

			if ( $this->check_redeeming_in_order() ) {
				return;
			}

			$order                    = $this->order;
			$NomineeIdsinMyaccount    = ( 'yes' === get_option( 'rs_nominee_activated' ) ) ? get_user_meta( $orderuserid, 'rs_selected_nominee', true ) : '';
			$EnableNomineeinMyaccount = ( 'yes' === get_option( 'rs_nominee_activated' ) ) ? get_user_meta( $orderuserid, 'rs_enable_nominee', true ) : 'no';
			$NomineeIdsinCheckout     = ( 'yes' === get_option( 'rs_nominee_activated' ) ) ? $order->get_meta( 'rs_selected_nominee_in_checkout' ) : '';
			$order_total              = $order->get_total();
			$minimum_cart_total       = get_option( 'rs_minimum_cart_total_for_earning' );
			$maximum_cart_total       = get_option( 'rs_maximum_cart_total_for_earning' );

			$args                      = array(
				'productid'     => $productid,
				'variationid'   => $variationid,
				'item'          => $item,
				'payment_price' => $payment_price,
				'order'         => $this->order,
			);
			$productlevelrewardpointss = check_level_of_enable_reward_point( $args );
			if ( '' !== $minimum_cart_total && 0 !== $minimum_cart_total ) {
				if ( $order_total < $minimum_cart_total ) {
					$productlevelrewardpointss = 0;
				}
			}

			if ( '' !== $maximum_cart_total && 0 !== $maximum_cart_total ) {
				if ( $order_total > $maximum_cart_total ) {
					$productlevelrewardpointss = 0;
				}
			}
			if ( ! empty( $productlevelrewardpointss ) ) {
				include 'frontend/rs_insert_points_for_product_purchase.php';
			}

			return $productlevelrewardpointss;
		}

		/**
		 * Get reward points based on product total in referral system.
		 *
		 * @param array $args Arguments.
		 */
		public function get_reward_points_based_on_product_total_in_referral_system( $args ) {

			if ( 'yes' !== get_option( 'rs_referral_activated' ) ) {
				return 0;
			}

			if ( '2' === get_option( 'rs_award_points_for_cart_or_product_total_for_refferal_system', 1 ) ) {
				return 0;
			}

			if ( ! block_points_for_renewal_order_sumo_subscriptions( $this->order_id, get_option( 'rs_award_referral_point_for_renewal_order' ) ) ) {
				return 0;
			}

			if ( ! rs_block_points_for_renewal_order_wc_subscriptions( $this->order_id, get_option( 'rs_award_referral_point_wc_renewal_order' ) ) ) {
					return 0;
			}

			// Return if referral restriction is matched.
			if ( ! rs_validate_referral_system_restrictions( $this->order ) ) {
				return 0;
			}

			if ( ! rs_restrict_referral_system_purchase_point_for_free_shipping( $this->order_id ) ) {
				return 0;
			}

			extract( wp_parse_args( $args ) );

						// Block Points for Sale Priced Product in Referral System.
			if ( rs_block_points_for_salepriced_product_in_referral_system( $product_id, $variation_id ) ) {
				return 0;
			}

			$referrer_name              = $this->order->get_meta( '_referrer_name' );
			$referrer_email             = $this->order->get_meta( '_referrer_email' );
			$billing_email              = ( WC_VERSION <= (float) ( '3.0' ) ) ? $this->order->billing_email : $this->order->get_billing_email();
			$refer_id_from_payment_plan = get_referrer_id_from_payment_plan( $this->order_id );
			$referrer_id                = empty( $refer_id_from_payment_plan ) ? $referrer_name : $refer_id_from_payment_plan;

			$data = array(
				'referrer_id'     => $referrer_id,
				'product_id'      => $product_id,
				'variation_id'    => $variation_id,
				'item'            => $item,
				'getting_referer' => 'yes',
				'payment_price'   => $payment_price,
			);

			$referrer_points = 0;
			if ( ! empty( $referrer_id ) && ( $referrer_email !== $billing_email ) ) {
				$referrer_points         = $this->insert_product_total_points_in_referral_system( $data );
				$data['getting_referer'] = 'no';
				$this->insert_product_total_points_in_referral_system( $data );
			} else {
				$referrer_id = check_if_referrer_has_manual_link( $this->order->get_user_id() );
				if ( $referrer_id ) {
					$data['getting_referer'] = 'no';
					$this->insert_product_total_points_in_referral_system( $data );
				}
			}

			return $referrer_points;
		}

		/**
		 * Insert product total points in referral system.
		 *
		 * @param WP_Post $data Data.
		 */
		public function insert_product_total_points_in_referral_system( $data ) {
			extract( wp_parse_args( $data ) );

			if ( ! rs_validate_referrer_id_from_restrictions( $referrer_id, $this->order ) ) {
				return 0;
			}

			$args = array(
				'productid'        => $product_id,
				'variationid'      => $variation_id,
				'item'             => $item,
				'referred_user'    => $referrer_id,
				'getting_referrer' => $getting_referer,
				'payment_price'    => $payment_price,
				'order'            => $this->order,
			);

			$pointstoinsert = check_level_of_enable_reward_point( $args );

			$event_slug = ( 'no' === $getting_referer ) ? 'PPRRP' : 'PPRRPG';

			if ( 'yes' === get_option( 'rs_referral_points_after_discounts' ) && 'PPRRP' === $event_slug ) {
				$points_after_discounts = $this->order->get_meta( 'rs_referrer_points_after_discounts' );
				$item_product_id        = 'variable' === wc_get_product( $product_id )->get_type() ? $item['variation_id'] : $item['product_id'];
				$pointstoinsert         = isset( $points_after_discounts[ $item_product_id ] ) ? $points_after_discounts[ $item_product_id ] : 0;
			}

			if ( empty( $pointstoinsert ) ) {
				return 0;
			}

			if ( 'yes' === get_option( 'rs_enable_disable_max_earning_points_for_user' ) ) {
				if ( 'PPRRP' === $event_slug ) {
					$this->check_point_restriction( $pointstoinsert, 0, $event_slug, $referrer_id, $nomineeid      = '', $this->order->get_user_id(), $productid, $variationid, $reasonindetail = '' );
				} elseif ( 'PPRRPG' === $event_slug ) {
					$this->check_point_restriction( $pointstoinsert, 0, $event_slug, $this->order->get_user_id(), $nomineeid      = '', $referrer_id, $productid, $variationid, $reasonindetail = '' );
				}
			} else {
				$insert_data = array(
					'pointstoinsert'    => $pointstoinsert,
					'event_slug'        => $event_slug,
					'product_id'        => $product_id,
					'variation_id'      => $variation_id,
					'totalearnedpoints' => $pointstoinsert,
				);

				if ( 'PPRRP' === $event_slug ) {

					$insert_data['user_id']     = $referrer_id;
					$insert_data['referred_id'] = $this->order->get_user_id();
				} elseif ( 'PPRRPG' === $event_slug ) {

					$insert_data['user_id']     = $this->order->get_user_id();
					$insert_data['referred_id'] = $referrer_id;
				}

				$this->total_points_management( $insert_data );

				$referral_info = $this->order->get_user_id();
				if ( 0 === $this->order->get_user_id() && 'yes' === get_option( 'rs_referrer_earn_point_purchase_by_guest_users' ) ) {
					$referral_info = ( WC_VERSION <= (float) ( '3.0' ) ) ? $this->order->billing_email : $this->order->get_billing_email();
				}

				if ( 'PPRRPG' !== $event_slug ) {
					$previouslog = get_option( 'rs_referral_log' );
					RS_Referral_Log::update_referral_log( $referrer_id, $referral_info, $pointstoinsert, array_filter( (array) $previouslog ) );
				}
			}

			if ( 'PPRRP' === $event_slug ) {
				/**
				 * Hook:fp_product_purchase_points_for_referrer.
				 *
				 * @since 1.0
				 */
				do_action( 'fp_product_purchase_points_for_referrer', $referrer_id, $this->order->get_user_id(), $pointstoinsert );
			} else {
				/**
				 * Hook:fp_product_purchase_points_for_getting_referred.
				 *
				 * @since 1.0
				 */
				do_action( 'fp_product_purchase_points_for_getting_referred', $this->order->get_user_id(), $referrer_id, $pointstoinsert );
			}

			$this->order->update_meta_data( 'referralsystem_earning_once', '1' );
			$this->order->save();

			if ( 'no' === $getting_referer ) {
				return 0;
			}

			return $pointstoinsert;
		}

		/**
		 * Referral system based on cart total.
		 *
		 * @return void
		 */
		public function referral_system_based_on_cart_total() {
			if ( 'no' !== get_option( 'rs_enable_product_category_level_for_referral_product_purchase' ) || '1' === get_option( 'rs_award_points_for_cart_or_product_total_for_refferal_system', 1 ) ) {
				return;
			}

			$order = $this->order;
			if ( ! is_object( $order ) ) {
				return;
			}

			// Return if referral restriction is matched.
			if ( ! rs_validate_referral_system_restrictions( $this->order ) ) {
				return 0;
			}

			if ( ! rs_restrict_referral_system_purchase_point_for_free_shipping( $this->order_id ) ) {
					return 0;
			}

			$referrername              = $this->order->get_meta( '_referrer_name' );
			$referid_from_payment_plan = get_referrer_id_from_payment_plan( $this->order_id );
			$referrer_id               = empty( $referid_from_payment_plan ) ? $referrername : $referid_from_payment_plan;

			if ( ! rs_validate_referrer_id_from_restrictions( $referrer_id, $this->order ) ) {
				return 0;
			}

			if ( ! empty( $referrer_id ) ) {
				$this->insert_cart_total_points_in_referral_system( $referrer_id, 'yes' );
				$this->insert_cart_total_points_in_referral_system( $referrer_id );
			} else {
				$referrer_id = check_if_referrer_has_manual_link( $this->order->get_user_id() );
				if ( $referrer_id ) {
					$this->insert_cart_total_points_in_referral_system( $referrer_id );
				}
			}
		}

		/**
		 * Insert cart total points in referral system.
		 *
		 * @return void
		 */
		public function insert_cart_total_points_in_referral_system( $referrer_id, $getting_referer = 'no' ) {

			$referrer = is_object( get_user_by( 'login', $referrer_id ) ) ? get_user_by( 'login', $referrer_id ) : get_user_by( 'ID', $referrer_id );
			if ( ! is_object( $referrer ) ) {
				return;
			}

			$referrer_points = $this->order->get_meta( 'rs_referrer_points_based_on_cart_total' );
			$referred_points = $this->order->get_meta( 'rs_referred_points_based_on_cart_total' );
			if ( empty( $referrer_points ) && empty( $referred_points ) ) {
				return;
			}

			$event_slug = ( 'no' === $getting_referer ) ? 'PPRRPCT' : 'PPRRPGCT';
			if ( 'yes' === get_option( 'rs_enable_disable_max_earning_points_for_user' ) ) {

				if ( 'PPRRPCT' === $event_slug && $referrer_points ) {
					$this->check_point_restriction( $referrer_points, 0, $event_slug, $referrer_id, $nomineeid      = '', $this->order->get_user_id(), $productid      = '', $variationid    = '', $reasonindetail = '' );
				} elseif ( 'PPRRPGCT' === $event_slug && $referred_points ) {
					$this->check_point_restriction( $referred_points, 0, $event_slug, $this->order->get_user_id(), $nomineeid      = '', $referrer_id, $productid      = '', $variationid    = '', $reasonindetail = '' );
				}
			} else {

				$valuestoinsert = array();
				if ( 'PPRRPCT' === $event_slug && $referrer_points ) {

					$valuestoinsert = array(
						'pointstoinsert'    => $referrer_points,
						'event_slug'        => $event_slug,
						'user_id'           => $referrer_id,
						'referred_id'       => $this->order->get_user_id(),
						'totalearnedpoints' => $referrer_points,
					);
				} elseif ( 'PPRRPGCT' === $event_slug && $referred_points ) {

					$valuestoinsert = array(
						'pointstoinsert'    => $referred_points,
						'event_slug'        => $event_slug,
						'user_id'           => $this->order->get_user_id(),
						'referred_id'       => $referrer_id,
						'totalearnedpoints' => $referred_points,
					);
				}

				if ( 0 === $this->order->get_user_id() && 'yes' === get_option( 'rs_referrer_earn_point_purchase_by_guest_users' ) ) {
					$referral_info = ( WC_VERSION <= (float) ( '3.0' ) ) ? $this->order->billing_email : $this->order->get_billing_email();
				} else {
					$referral_info = $this->order->get_user_id();
				}

				$this->total_points_management( $valuestoinsert );
				if ( 'PPRRPGCT' !== $event_slug && $referrer_points ) {
					$previouslog = get_option( 'rs_referral_log' );
					RS_Referral_Log::update_referral_log( $referrer_id, $referral_info, $referrer_points, array_filter( (array) $previouslog ) );
				}
			}

			if ( 'PPRRPCT' === $event_slug ) {
				/**
				 * Hook:fp_product_purchase_points_for_referrer_based_on_cart_total.
				 *
				 * @since 1.0
				 */
				do_action( 'fp_product_purchase_points_for_referrer_based_on_cart_total', $referrer_id, $this->order->get_user_id(), $referrer_points );
			} else {
				/**
				 * Hook:fp_product_purchase_points_for_getting_referred_based_on_cart_total.
				 *
				 * @since 1.0
				 */
				do_action( 'fp_product_purchase_points_for_getting_referred_based_on_cart_total', $this->order->get_user_id(), $referrer_id, $referred_points );
			}

			$this->order->update_meta_data( 'referralsystem_earning_once', '1' );
			$this->order->save();
		}

		public function insert_points_for_product( $enabledisablemaxpoints, $order_id, $orderuserid, $nomineeid, $productlevelrewardpointss, $productid, $variationid, $reasonindetail ) {
			$event_slug = 'PPRPFN';
			if ( 'yes' == $enabledisablemaxpoints ) {
				$this->check_point_restriction( $productlevelrewardpointss, $pointsredeemed = 0, $event_slug, $orderuserid, $nomineeid, '', $productid, $variationid, $reasonindetail );
			} else {
				$valuestoinsert = array(
					'pointstoinsert'    => $productlevelrewardpointss,
					'event_slug'        => $event_slug,
					'user_id'           => $orderuserid,
					'product_id'        => $productid,
					'variation_id'      => $variationid,
					'reasonindetail'    => $reasonindetail,
					'nominee_id'        => $nomineeid,
					'nominee_points'    => $productlevelrewardpointss,
					'totalearnedpoints' => $productlevelrewardpointss,
				);
				$this->total_points_management( $valuestoinsert );
				$table_args = array(
					'user_id'       => $nomineeid,
					'checkpoints'   => 'PPRPFNP',
					'nomineeid'     => $orderuserid,
					'nomineepoints' => $productlevelrewardpointss,
				);
				RSPointExpiry::record_the_points( $table_args );
			}
		}

		public function send_sms_and_email_in_product_purchase() {

			if ( ! is_object( $this->order ) ) {
				return;
			}

			if ( 'no' !== $this->apply_previous_order_points || ! $this->order->get_meta( 'reward_points_awarded' ) ) {
				return;
			}

			$user_id    = $this->order->get_user_id();
			$PointsData = new RS_Points_Data( $user_id );
			$Points     = $PointsData->total_available_points();
			if ( ! empty( $Points ) ) {
				if ( 'yes' === get_option( 'rs_sms_activated' ) && 'yes' === get_option( 'rs_enable_send_sms_to_user' ) ) {
					if ( 'yes' === get_option( 'rs_send_sms_earning_points' ) ) {
						$PhoneNumber = ! empty( get_user_meta( $user_id, 'rs_phone_number_value_from_signup', true ) ) ? get_user_meta( $user_id, 'rs_phone_number_value_from_signup', true ) : get_user_meta( $user_id, 'rs_phone_number_value_from_account_details', true );
						$PhoneNumber = ! empty( $PhoneNumber ) ? $PhoneNumber : get_user_meta( $user_id, 'billing_phone', true );
						if ( '1' === get_option( 'rs_sms_sending_api_option' ) ) {
							RSFunctionForSms::send_sms_twilio_api( $this->order_id, 'earning', '', $PhoneNumber );
						} elseif ( '2' === get_option( 'rs_sms_sending_api_option' ) ) {
							RSFunctionForSms::send_sms_nexmo_api( $this->order_id, 'earning', '', $PhoneNumber );
						}
					}
				}
			}

			if ( 'yes' === get_option( 'rs_email_activated' ) ) {
				send_mail_for_product_purchase( $user_id, $this->order_id, 'earning' );
			}
		}

		/**
		 * Check Restriction.
		 */
		public function check_restriction() {
			if ( 'yes' !== get_option( 'rs_reward_action_activated' ) ) {
				return true;
			}

			$order         = $this->order;
			$order_user_id = srp_order_obj( $order );
			$order_user_id = $order_user_id['order_userid'];
			if ( ! $order_user_id ) {
				return true;
			}

			$order_count = get_posts(
				array(
					'numberposts' => -1,
					'meta_key'    => '_customer_user',
					'meta_value'  => $order_user_id,
					'post_type'   => wc_get_order_types(),
					'post_status' => implode( "','", array_keys( wc_get_order_statuses() ) ),
				)
			);

			if ( 1 === count( $order_count ) && 'yes' === get_option( '_rs_enable_signup' ) ) {
				if ( 'yes' === get_option( 'rs_reward_signup_after_first_purchase' ) && 'yes' == get_option( 'rs_signup_points_with_purchase_points' ) ) {
					$order->update_meta_data( 'rs_prevent_point_for_first_purchase', 'yes' );
					$order->save();
					return false;
				}
			}
			return true;
		}

		/**
		 * Check points already awarded for user.
		 */
		public function award_earning_point_only_once() {
			$earningpointonce = $this->order->get_meta( 'earning_point_once' );
			$earningpointonce = ( '1' !== $earningpointonce ) ? true : false;
			return $earningpointonce;
		}

		/**
		 * Check points already awarded for referral user.
		 */
		public function award_points_only_once_for_referral_system() {
			$referrer_value = $this->order->get_meta( 'referralsystem_earning_once' );
			$referrer_value = ( '1' === $referrer_value ) ? false : true;
			return $referrer_value;
		}

		/**
		 * Check if user is banned from earning and redeeming.
		 */
		public function is_user_banned() {
			$orderobj     = srp_order_obj( $this->order );
			$banning_type = check_banning_type( $orderobj['order_userid'] );
			$ban          = ( 'earningonly' !== $banning_type && 'both' !== $banning_type ) ? true : false;
			return $ban;
		}

		/**
		 * Check if redeeming is allowed in this order.
		 */
		public function check_redeeming_in_order() {
			$user_id             = srp_order_obj( $this->order );
			$user_id             = $user_id['order_userid'];
			$rewardpointscoupons = $this->order->get_items( array( 'coupon' ) );
			$getuserdatabyid     = get_user_by( 'id', $user_id );
			$getusernickname     = isset( $getuserdatabyid->user_login ) ? $getuserdatabyid->user_login : '';
			$maincouponchecker   = 'sumo_' . strtolower( $getusernickname );
			$auto_redeem_name    = 'auto_redeem_' . strtolower( $getusernickname );
			$sumo_coupon_name    = array( $maincouponchecker, $auto_redeem_name );

			if ( 'yes' == get_option( 'rs_disable_point_if_coupon' ) ) {
				if ( ! empty( $rewardpointscoupons ) ) {
					foreach ( $rewardpointscoupons as $array ) {
						$applied_coupons[] = $array['code'];
					}
					$diff_array = array_diff( $applied_coupons, $sumo_coupon_name );
					if ( is_array( $diff_array ) && ! empty( $diff_array ) ) {
						return true;
					}
				}
			}
			if ( 'yes' == get_option( 'rs_enable_redeem_for_order' ) ) {
				if ( ! empty( $rewardpointscoupons ) ) {
					foreach ( $rewardpointscoupons as $array ) {
						$applied_coupons[] = $array['code'];
					}
					if ( in_array( $maincouponchecker, $applied_coupons ) || in_array( $auto_redeem_name, $applied_coupons ) ) {
						return true;
					}
				}
			}
			if ( 'yes' == get_option( 'rs_coupon_compatability_activated' ) && 'yes' == get_option( '_rs_not_allow_earn_points_if_sumo_coupon' ) ) {
				foreach ( $rewardpointscoupons as $couponcode => $value ) {
					$coupon_id_array   = new WC_Coupon( $value['name'] );
					$coupon_id         = srp_coupon_obj( $coupon_id_array );
					$coupon_id         = $coupon_id['coupon_id'];
					$sumo_coupon_check = get_post_meta( $coupon_id, 'sumo_coupon_check', true );
					if ( 'yes' == $sumo_coupon_check ) {
						return true;
					}
				}
			}

			return check_if_discount_applied();
		}

		public function total_points_management( $args, $earning_conversion = true ) {
			$default_args   = array(
				'pointstoinsert'    => 0,
				'pointsredeemed'    => 0,
				'referred_id'       => 0,
				'product_id'        => 0,
				'variation_id'      => 0,
				'reasonindetail'    => '',
				'nominee_id'        => '',
				'nominee_points'    => 0,
				'totalearnedpoints' => 0,
				'totalredeempoints' => 0,
			);
			$valuestoinsert = wp_parse_args( $args, $default_args );
			$user_id        = $valuestoinsert['user_id'];
			if ( empty( $user_id ) ) {
				return;
			}

			if ( ! allow_reward_points_for_user( $user_id ) ) {
				return;
			}

			if ( check_if_discount_applied() ) {
				return;
			}

			global $wpdb;
			$table_name   = $wpdb->prefix . 'rspointexpiry';
			$check_points = isset( $args['event_slug'] ) ? $args['event_slug'] : '';
			$date         = expiry_date_for_points( $check_points );
			if ( isset( $valuestoinsert['manualaddpoints'] ) ) {
				$date = ! empty( $valuestoinsert['expireddate'] ) ? $valuestoinsert['expireddate'] : 999999999999;
			}

			$user_name = ! empty( $valuestoinsert['referred_id'] ) ? get_user_by( 'id', $valuestoinsert['referred_id'] )->user_login : '';

			$pointstoinsert = $valuestoinsert['pointstoinsert'];
			if ( 'MAP' !== $valuestoinsert['event_slug'] && $earning_conversion ) {
				$pointstoinsert = RSMemberFunction::earn_points_percentage( $user_id, (float) $valuestoinsert['pointstoinsert'] );
			}

			if ( 'PFFP' === $valuestoinsert['event_slug'] ) {
				$this->order->update_meta_data( 'rs_first_purchase_points', $pointstoinsert );
				$this->order->save();
			}

			// Update Referrer points after discounts meta.
			if ( 'yes' === get_option( 'rs_referral_points_after_discounts' ) && 'PPRRP' == $valuestoinsert['event_slug'] ) {
				$ProductId = ! empty( $valuestoinsert['variation_id'] ) ? $valuestoinsert['variation_id'] : $valuestoinsert['product_id'];
				$this->order->update_meta_data( 'rs_referrer_points_after_discounts', array( $ProductId => $pointstoinsert ) );
				$this->order->save();
			}

			$table_args = array(
				'user_id'           => $user_id,
				'pointstoinsert'    => $pointstoinsert,
				'usedpoints'        => $valuestoinsert['pointsredeemed'],
				'date'              => $date,
				'checkpoints'       => $valuestoinsert['event_slug'],
				'orderid'           => $this->order_id,
				'totalearnedpoints' => $valuestoinsert['totalearnedpoints'],
				'totalredeempoints' => $valuestoinsert['totalredeempoints'],
				'reason'            => $valuestoinsert['reasonindetail'],
				'productid'         => $valuestoinsert['product_id'],
				'variationid'       => $valuestoinsert['variation_id'],
				'refuserid'         => $valuestoinsert['referred_id'],
				'nomineeid'         => $valuestoinsert['nominee_id'],
				'nomineepoints'     => $valuestoinsert['nominee_points'],
			);
			/**
			 * Hook:rs_points_data_before_insertion.
			 *
			 * @since 1.0
			 */
			$table_args = apply_filters( 'rs_points_data_before_insertion', $table_args );

			RSPointExpiry::insert_earning_points( $table_args );
			RSPointExpiry::record_the_points( $table_args );
			$to = get_user_by( 'id', $user_id )->user_email;
			rs_send_mail_for_actions( $to, $valuestoinsert['event_slug'], $pointstoinsert, $user_name, $this->order_id );

						// Create order automatically as free product on reaching the earning member level.
			if ( 'yes' === get_option( 'rs_enable_earned_level_based_reward_points' ) && '2' === get_option( 'rs_free_product_add_by_user_or_admin' ) ) {
				rs_create_free_product_order_automatically( $user_id );
			}
		}

		/**
		 * Points Management.
		 */
		public function points_management( $earned_points, $redeemed_points, $event_slug, $user_id ) {
			$table_args = array(
				'user_id'           => $user_id,
				'pointstoinsert'    => $earned_points,
				'usedpoints'        => $redeemed_points,
				'checkpoints'       => $event_slug,
				'orderid'           => $this->order_id,
				'totalearnedpoints' => $this->get_total_earned_points(),
				'totalredeempoints' => 0,
			);
			RSPointExpiry::insert_earning_points( $table_args );
			RSPointExpiry::record_the_points( $table_args );
			$this->rs_send_mail_for_reaching_maximum_threshold( $user_id );
		}

		/**
		 * Send Mail after reaching maximum threshold.
		 *
		 * @param int $user_id User ID.
		 */
		public function rs_send_mail_for_reaching_maximum_threshold( $user_id ) {
			if ( 'yes' === get_option( 'rs_mail_for_reaching_maximum_threshold' ) ) {
				$PointsData     = new RS_Points_Data( $user_id );
				$totalpoints    = $PointsData->total_available_points();
				$user_data      = get_user_by( 'ID', $user_id );
				$receiver_name  = is_object( $user_data ) ? $user_data->user_login : '';
				$receiver_mail  = is_object( $user_data ) ? $user_data->user_email : '';
				$Email_subject  = get_option( 'rs_mail_subject_for_reaching_maximum_threshold' );
				$message        = get_option( 'rs_mail_message_for_reaching_maximum_threshold' );
				$Email_message  = str_replace( '[maximum_threshold]', get_option( 'rs_max_earning_points_for_user' ), str_replace( '[availablepoints]', $totalpoints, $message ) );
				$Email_message  = do_shortcode( $Email_message );
				$admin_email_id = get_option( 'admin_email' );
				$admin_name     = get_bloginfo( 'name', 'display' );
				if ( '' != $admin_email_id && '' != $admin_name && '' != $receiver_name && '' != $totalpoints && '' != $receiver_mail ) {
					add_filter( 'woocommerce_email_footer_text', 'srp_footer_link' );
					ob_start();
					wc_get_template( 'emails/email-header.php', array( 'email_heading' => $Email_subject ) );
					echo do_shortcode( $Email_message );
					wc_get_template( 'emails/email-footer.php' );
					$woo_temp_msg                 = ob_get_clean();
					$message_headers              = "MIME-Version: 1.0\r\n";
					$message_headers             .= "From: \"{$admin_name}\" <{$admin_email_id}>\n" . 'Content-Type: text/html; charset="' . get_option( 'blog_charset' ) . "\"\n";
					$message_headers             .= 'Reply-To: ' . $receiver_name . ' <' . $receiver_mail . ">\r\n";
					FPRewardSystem::$rs_from_name = $admin_name;
					add_filter( 'woocommerce_email_from_name', 'rs_alter_from_name_of_woocommerce', 10, 2 );
					if ( WC_VERSION <= (float) ( '2.2.0' ) ) {
						wp_mail( $receiver_mail, $Email_subject, $Email_message, $message_headers );
					} else {
						$mailer = WC()->mailer();
						$mailer->send( $receiver_mail, $Email_subject, $woo_temp_msg, $message_headers );
					}
					remove_filter( 'woocommerce_email_from_name', 'rs_alter_from_name_of_woocommerce', 10, 1 );
				}
			}
		}
	}

}
