<?php
/*
 * Frontend Assests
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'RSFrontendAssets' ) ) {

	class RSFrontendAssets {

		public static function init() {

			if ( '1' == get_option( 'rs_message_before_after_cart_table' ) ) {
				if ( '1' == get_option( 'rs_reward_point_troubleshoot_before_cart' ) ) {
					add_action( 'woocommerce_before_cart', array( __CLASS__, 'message_for_guest' ), 999 );
				} else {
					add_action( 'woocommerce_before_cart_table', array( __CLASS__, 'message_for_guest' ), 999 );
				}
			} else {
				add_action( 'woocommerce_after_cart_table', array( __CLASS__, 'message_for_guest' ), 999 );
			}

			add_action( 'woocommerce_before_checkout_form', array( __CLASS__, 'message_for_guest' ), 999 );
			add_action( 'woocommerce_before_checkout_form', array( __CLASS__, 'force_guest_signup_in_checkout' ), 10, 1 );
			add_action( 'woocommerce_checkout_process', array( __CLASS__, 'create_account_for_guest' ) );
			add_filter( 'woocommerce_checkout_fields', array( __CLASS__, 'allow_user_to_involve_reward_program_in_checkbox' ) );

			/* Points that can be Earned Message based on select Type in Cart page */
			if ( '1' == get_option( 'rs_select_type_for_cart', '1' ) ) {
				add_action( 'woocommerce_cart_totals_before_order_total', array( __CLASS__, 'total_points_in_cart' ) );
			} else {
				add_action( 'woocommerce_cart_totals_after_order_total', array( __CLASS__, 'total_points_in_cart' ) );
			}

			/* Points that can be Earned Message based on select Type in Checkout page */
			if ( '2' === get_option( 'rs_select_type_for_checkout', '2' ) ) {
				add_action( 'woocommerce_review_order_after_order_total', array( __CLASS__, 'total_points_in_checkout' ) );
			} else {
				add_action( 'woocommerce_review_order_before_order_total', array( __CLASS__, 'total_points_in_checkout' ) );
			}

			add_action( 'woocommerce_order_details_after_order_table_items', array( __CLASS__, 'total_points_in_order_detail' ) ); // For WooCommerce  > V3.3

			if ( 3.0 === WC_VERSION ) {
				add_action( 'woocommerce_order_items_table', array( __CLASS__, 'total_points_in_order_detail' ) ); // For WooCommerce V3.0
			}

			if ( '1' == get_option( 'rs_message_before_after_cart_table' ) ) {
				if ( '1' == get_option( 'rs_reward_point_troubleshoot_before_cart' ) ) {
					add_action( 'woocommerce_before_cart', array( __CLASS__, 'display_notices_in_cart_and_checkout' ), 999 );
				} else {
					add_action( 'woocommerce_before_cart_table', array( __CLASS__, 'display_notices_in_cart_and_checkout' ), 999 );
				}
			} else {
				add_action( 'woocommerce_after_cart_table', array( __CLASS__, 'display_notices_in_cart_and_checkout' ), 999 );
			}
			add_action( 'woocommerce_before_checkout_form', array( __CLASS__, 'display_notices_in_cart_and_checkout' ), 999 );
		}

		public static function message_for_guest() {
			if ( is_user_logged_in() ) {
				return;
			}

			$ShowMsg = is_cart() ? get_option( 'rs_show_hide_message_for_guest' ) : get_option( 'rs_show_hide_message_for_guest_checkout_page' );
			if ( 2 == $ShowMsg ) {
				return;
			}

			$MsgToDisplay = is_cart() ? get_option( 'rs_message_for_guest_in_cart' ) : get_option( 'rs_message_for_guest_in_checkout' );
			$Divclass     = is_cart() ? 'rs_cart_message' : 'rs_checkout_message';

			$cart_contents = WC()->cart->cart_contents;
			if ( ! srp_check_is_array( $cart_contents ) ) {
				return;
			}

			$is_reward_point_awarded = false;
			if ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '1' != get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
				if ( '2' == get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
					$cart_total_points = get_reward_points_based_on_cart_total( WC()->cart->total, false );
					if ( $cart_total_points ) {
						$is_reward_point_awarded = true;
					}
				}

				if ( '3' == get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
					$range_total_points = RSProductPurchaseFrontend::get_reward_point_for_range_based_type();
					if ( $range_total_points ) {
						$is_reward_point_awarded = true;
					}
				}
			} else {
				foreach ( $cart_contents as $cart ) {

					$variation_id = isset( $cart['variation_id'] ) ? $cart['variation_id'] : '';
					$product_id   = isset( $cart['product_id'] ) ? $cart['product_id'] : '';

					$args = array(
						'productid'   => $product_id,
						'variationid' => $variation_id,
						'item'        => $cart,
					);

					$reward_point = check_level_of_enable_reward_point( $args );
					if ( $reward_point ) {
						$is_reward_point_awarded = true;
						continue;
					}
				}
			}

			if ( ! $is_reward_point_awarded ) {
				return;
			}
			?>
			<div class="woocommerce-info <?php echo esc_attr( $Divclass ); ?>"><?php echo do_shortcode( $MsgToDisplay ); ?></div>
			<?php
		}

		public static function force_guest_signup_in_checkout( $checkout ) {
			if ( is_user_logged_in() ) {
				return;
			}

			if ( ! is_checkout() ) {
				return;
			}

			if ( 'no' == get_option( 'rs_enable_acc_creation_for_guest_checkout_page' ) ) {
				return;
			}

			if ( ! isset( $checkout->enable_signup ) ) {
				return;
			}

			if ( ! isset( $checkout->enable_guest_checkout ) ) {
				return;
			}

			$PointsInfo = ( 'yes' == get_option( 'rs_enable_disable_reward_point_based_coupon_amount' ) ) ? self::modified_points_for_products() : self::original_points_for_product();
			if ( ! srp_check_is_array( $PointsInfo ) ) {
				return;
			}

			$checkout->enable_signup         = true;
			$checkout->enable_guest_checkout = false;
		}

		/* To Create account for Guest */

		public static function create_account_for_guest() {
			if ( is_user_logged_in() ) {
				return;
			}

			if ( ! is_checkout() ) {
				return;
			}

			if ( 'no' == get_option( 'rs_enable_acc_creation_for_guest_checkout_page' ) ) {
				return;
			}

			if ( ! self::check_if_product_has_reward_points() ) {
				return;
			}

			$_REQUEST['createaccount'] = 1;
		}

		public static function check_if_product_has_reward_points() {
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				if ( ! isset( $cart_item['product_id'] ) ) {
					continue;
				}

				$args   = array(
					'productid'   => $cart_item['product_id'],
					'variationid' => isset( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : 0,
					'item'        => $cart_item,
					'checklevel'  => 'yes',
				);
				$Points = check_level_of_enable_reward_point( $args );
				if ( ! empty( $Points ) ) {
					return true;
				}
			}
			return false;
		}

		/* Display checkbox to involve guest in reward program in Checkout */

		public static function allow_user_to_involve_reward_program_in_checkbox( $fields ) {
			if ( 'no' == get_option( 'rs_enable_reward_program' ) ) {
				return $fields;
			}

			$fields['account']['account_password']   = array(
				'type'        => 'password',
				'label'       => __( 'Create account password', 'woocommerce' ),
				'required'    => true,
				'priority'    => 90,
				'placeholder' => esc_attr__( 'Password', 'woocommerce' ),
			);
			$fields['account']['enable_reward_prgm'] = array(
				'type'              => 'checkbox',
				'label'             => get_option( 'rs_msg_in_acc_page_when_unchecked' ),
				'required'          => false,
				'priority'          => 100,
				'custom_attributes' => array( 'data-enable-reward-program' => get_option( 'rs_enable_reward_program' ) ),
			);
			return $fields;
		}

		/* Display Points in Cart before Order Total */

		public static function total_points_in_cart() {
			if ( ! is_user_logged_in() ) {
				return;
			}

			if ( 'yes' != get_option( 'rs_product_purchase_activated' ) && 'yes' != get_option( 'rs_buyingpoints_activated' ) ) {
				return;
			}

			if ( '2' == get_option( 'rs_show_hide_total_points_cart_field' ) ) {
				return;
			}

			if ( ! rs_restrict_product_purchase_point_when_free_shipping_is_enabled() ) {
				return;
			}

			$PaymentPlanPoints = 0;
			if ( 'yes' == get_option( 'rs_product_purchase_activated' ) ) {
				if ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '2' == get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
					$CartTotalPoints = get_reward_points_based_on_cart_total( WC()->cart->total, false, get_current_user_id() );
					$CartTotalPoints = RSMemberFunction::earn_points_percentage( get_current_user_id(), (float) $CartTotalPoints );
					/**
					 * Hook:srp_buying_points_in_cart.
					 *
					 * @since 1.0
					 */
					$Points = apply_filters( 'srp_buying_points_in_cart', 0 ) + $CartTotalPoints;
				} elseif ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '3' == get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
					/**
					 * Hook:srp_buying_points_in_cart.
					 *
					 * @since 1.0
					 */
					$Points = apply_filters( 'srp_buying_points_in_cart', 0 ) + RSProductPurchaseFrontend::get_reward_point_for_range_based_type();
				} else {
					$TotalPoints = WC()->session->get( 'rewardpoints' );
					if ( 0 == $TotalPoints ) {
						return;
					}

					global $totalrewardpoints_payment_plan;
					$PointsInfo        = ( 'yes' == get_option( 'rs_enable_disable_reward_point_based_coupon_amount' ) ) ? self::modified_points_for_products() : self::original_points_for_product();
					$ProductPlanPoints = srp_check_is_array( $totalrewardpoints_payment_plan ) ? round_off_type( array_sum( $totalrewardpoints_payment_plan ) ) : 0;
					/**
					 * Hook:srp_buying_points_for_payment_plan_in_cart.
					 *
					 * @since 1.0
					 */
					$PaymentPlanPoints     = $ProductPlanPoints + apply_filters( 'srp_buying_points_for_payment_plan_in_cart', 0 );
					$ProductPurchasePoints = array();
					if ( srp_check_is_array( $PointsInfo ) ) {
						foreach ( $PointsInfo as $ProductId => $Points ) {
							$Points                  = RSMemberFunction::earn_points_percentage( get_current_user_id(), (float) $Points );
							$ProductPurchasePoints[] = floatval( $Points );
						}
					}
					/**
					 * Hook:srp_buying_points_in_cart.
					 *
					 * @since 1.0
					 */
					$Points = ( array_sum( $ProductPurchasePoints ) + apply_filters( 'srp_buying_points_in_cart', 0 ) ) - $PaymentPlanPoints;
				}
				if ( 'yes' == get_option( 'rs_enable_first_purchase_reward_points' ) ) {
					$OrderCount          = get_posts(
						array(
							'numberposts' => -1,
							'meta_key'    => '_customer_user',
							'meta_value'  => get_current_user_id(),
							'post_type'   => wc_get_order_types(),
							'post_status' => array( 'wc-pending', 'wc-processing', 'wc-on-hold', 'wc-completed' ),
						)
					);
					$FirstPurchasePoints = RSMemberFunction::earn_points_percentage( get_current_user_id(), (float) rs_get_first_purchase_point() );
					$Points              = ( 0 == count( $OrderCount ) ) ? ( $Points + $FirstPurchasePoints ) : $Points;
				}
			} elseif ( 'yes' == get_option( 'rs_buyingpoints_activated' ) ) {
				/**
				 * Hook:srp_buying_points_in_cart.
				 *
				 * @since 1.0
				 */
				$Points = apply_filters( 'srp_buying_points_in_cart', 0 );
				/**
				 * Hook:srp_buying_points_for_payment_plan_in_cart.
				 *
				 * @since 1.0
				 */
				$PaymentPlanPoints = apply_filters( 'srp_buying_points_for_payment_plan_in_cart', 0 );
			}

			if ( 'yes' == get_option( 'rs_referral_activated' ) ) {
				$Points = $Points + self::get_referred_points_in_cart_and_checkout();
			}

			if ( empty( $Points ) ) {
				return;
			}

			$ConvertedValue = redeem_point_conversion( $Points, get_current_user_id(), 'price' );
			$CurrencyValue  = srp_formatted_price( round_off_type_for_currency( $ConvertedValue ) );
			$BoolVal        = 0 === WC()->cart->discount_cart ? true : ( 'no' == get_option( 'rs_enable_redeem_for_order' ) && 'no' == get_option( 'rs_disable_point_if_coupon' ) );
			if ( ! $BoolVal ) {
				return;
			}
			?>
			<tr class="srp-points-cart-wrapper">
				<th class="srp-points-caption-cart"><?php echo esc_html( get_option( 'rs_total_earned_point_caption' ) ); ?></th>
				<td class="srp-points-value-cart" data-title="<?php echo esc_html( get_option( 'rs_total_earned_point_caption' ) ); ?>"><?php echo do_shortcode( custom_message_in_thankyou_page( $Points, $CurrencyValue, 'rs_show_hide_equivalent_price_for_points_cart', 'rs_show_hide_custom_msg_for_points_cart', 'rs_custom_message_for_points_cart', $PaymentPlanPoints ) ); ?> </td>
			</tr>
			<?php
		}

		/* Get referred points in cart and checkout */

		public static function get_referred_points_in_cart_and_checkout() {

			if ( isset( $_COOKIE['rsreferredusername'] ) ) {
				$cookie_name = wc_clean( wp_unslash( $_COOKIE['rsreferredusername'] ) );
				$referrer    = ( 1 == get_option( 'rs_generate_referral_link_based_on_user' ) ) ? get_user_by( 'login', $cookie_name ) : get_user_by( 'id', $cookie_name );
				if ( ! is_object( $referrer ) ) {
					return;
				}

				$referrer_id = $referrer->ID;
			} else {
				$referrer_id = check_if_referrer_has_manual_link( get_current_user_id() );
			}

			if ( ! $referrer_id ) {
				return;
			}

			if ( ! srp_check_is_array( WC()->cart->cart_contents ) ) {
				return;
			}

			if ( ! rs_restrict_referral_system_purchase_point_for_free_shipping() ) {
				return;
			}

			$referred_points = 0;
			if ( '1' == get_option( 'rs_award_points_for_cart_or_product_total_for_refferal_system', 1 ) ) {
				foreach ( WC()->cart->cart_contents as $value ) {
					$args            = array(
						'productid'        => isset( $value['product_id'] ) ? $value['product_id'] : 0,
						'variationid'      => isset( $value['variation_id'] ) ? $value['variation_id'] : 0,
						'item'             => $value,
						'getting_referrer' => 'yes',
						'referred_user'    => get_current_user_id(),
					);
					$referred_points = check_level_of_enable_reward_point( $args );
				}
			} else {
				$referred_points = rs_get_reward_points_based_on_cart_total_for_referred();
			}

			return $referred_points;
		}

		/**
		 * Display Points in Checkout before Order Total
		 * */
		public static function total_points_in_checkout() {
			if ( ! is_user_logged_in() ) {
				return;
			}

			if ( 'yes' !== get_option( 'rs_product_purchase_activated' ) && 'yes' !== get_option( 'rs_buyingpoints_activated' ) ) {
				return;
			}

			if ( '2' === get_option( 'rs_show_hide_total_points_checkout_field' ) ) {
				return;
			}

			if ( ! rs_restrict_product_purchase_point_when_free_shipping_is_enabled() ) {
				return;
			}

			if ( 'yes' === get_option( 'rs_product_purchase_activated' ) ) {
				if ( 'no' === get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '2' == get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
					$Points = get_reward_points_based_on_cart_total( WC()->cart->total, false, get_current_user_id() );
					/**
					 * Hook:srp_buying_points_in_cart.
					 *
					 * @since 1.0
					 */
					$Points = RSMemberFunction::earn_points_percentage( get_current_user_id(), (float) $Points ) + apply_filters( 'srp_buying_points_in_cart', 0 );
				} elseif ( 'no' === get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '3' == get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
					$Points = RSProductPurchaseFrontend::get_reward_point_for_range_based_type();
				} else {
					$Points = WC()->session->get( 'rewardpoints' );
					if ( ! $Points ) {
						return;
					}

					global $totalrewardpoints_payment_plan;
					$PointsInfo        = ( 'yes' == get_option( 'rs_enable_disable_reward_point_based_coupon_amount' ) ) ? self::modified_points_for_products() : self::original_points_for_product();
					$ProductPlanPoints = srp_check_is_array( $totalrewardpoints_payment_plan ) ? round_off_type( array_sum( $totalrewardpoints_payment_plan ) ) : 0;
					/**
					 * Hook:srp_buying_points_for_payment_plan_in_cart.
					 *
					 * @since 1.0
					 */
					$PaymentPlanPoints     = $ProductPlanPoints + apply_filters( 'srp_buying_points_for_payment_plan_in_cart', 0 );
					$ProductPurchasePoints = array();
					if ( srp_check_is_array( $PointsInfo ) ) {
						foreach ( $PointsInfo as $ProductId => $Points ) {
							$Points                  = RSMemberFunction::earn_points_percentage( get_current_user_id(), (float) $Points );
							$ProductPurchasePoints[] = floatval( $Points );
						}
					}
					/**
					 * Hook:srp_buying_points_in_cart.
					 *
					 * @since 1.0
					 */
					$Points = ( array_sum( $ProductPurchasePoints ) + apply_filters( 'srp_buying_points_in_cart', 0 ) ) - $PaymentPlanPoints;
				}
				if ( 'yes' === get_option( 'rs_enable_first_purchase_reward_points' ) ) {
					$OrderCount          = get_posts(
						array(
							'numberposts' => -1,
							'meta_key'    => '_customer_user',
							'meta_value'  => get_current_user_id(),
							'post_type'   => wc_get_order_types(),
							'post_status' => array( 'wc-pending', 'wc-processing', 'wc-on-hold', 'wc-completed' ),
						)
					);
					$FirstPurchasePoints = RSMemberFunction::earn_points_percentage( get_current_user_id(), (float) rs_get_first_purchase_point() );
					$Points              = ( 0 === count( $OrderCount ) ) ? ( $Points + $FirstPurchasePoints ) : $Points;
				}
			} elseif ( 'yes' === get_option( 'rs_buyingpoints_activated' ) ) {
				/**
				 * Hook:srp_buying_points_in_cart.
				 *
				 * @since 1.0
				 */
				$Points = apply_filters( 'srp_buying_points_in_cart', 0 );
			}

			if ( 'yes' === get_option( 'rs_referral_activated' ) ) {
				$Points = $Points + self::get_referred_points_in_cart_and_checkout();
			}

			if ( empty( $Points ) ) {
				return;
			}

			$ConvertedValue = redeem_point_conversion( $Points, get_current_user_id(), 'price' );
			$CurrencyValue  = srp_formatted_price( round_off_type_for_currency( $ConvertedValue ) );
			$BoolVal        = ( 0 === WC()->cart->discount_cart ) ? true : ( 'no' === get_option( 'rs_enable_redeem_for_order' ) && 'no' === get_option( 'rs_disable_point_if_coupon' ) );
			if ( ! $BoolVal ) {
				return;
			}
			?>
			<tr class="srp-points-checkout-wrapper">
				<th class="srp-points-caption-checkout"><?php echo do_shortcode( get_option( 'rs_total_earned_point_caption_checkout' ) ); ?></th>
				<td class="srp-points-value-checkout">
					<?php
					echo do_shortcode( custom_message_in_thankyou_page( $Points, $CurrencyValue, 'rs_show_hide_equivalent_price_for_points', 'rs_show_hide_custom_msg_for_points_checkout', 'rs_custom_message_for_points_checkout', 0 ) );
					?>
				</td>
			</tr>
			<?php
		}

		/**
		 * Display Points in Order Detail.
		 *
		 * @param WP_Post $order Order object.
		 * */
		public static function total_points_in_order_detail( $order ) {
			if ( ! is_user_logged_in() ) {
				return;
			}

			if ( 'yes' !== get_option( 'rs_product_purchase_activated' ) && 'yes' !== get_option( 'rs_buyingpoints_activated' ) ) {
				return;
			}

			if ( '2' === get_option( 'rs_show_hide_total_points_order_field' ) ) {
				return;
			}

			$order_obj          = srp_order_obj( $order );
			$default_value      = ( 'yes' === get_option( 'rs_disable_point_if_reward_points_gateway', 'no' ) ) ? array( 'reward_gateway' ) : array();
			$restricted_gateway = get_option( 'rs_select_payment_gateway_for_restrict_reward', $default_value );

			if ( in_array( $order_obj['payment_method'], $restricted_gateway ) ) {
				return;
			}

			$redeemed_order = ( 'yes' === get_option( 'rs_redeeming_activated' ) ) ? $order->get_meta( 'rs_check_enable_option_for_redeeming' ) : 'no';
			if ( ! empty( $redeemed_order ) && 'no' !== $redeemed_order ) {
				return;
			}

			$obj = new RewardPointsOrder( $order_obj['order_id'], 'no' );
			if ( $obj->check_redeeming_in_order() ) {
				return;
			}

			$payment_plan_points = get_payment_product_price( $order_obj['order_id'], true );
			$points              = self::get_reward_points_for_order( $order_obj['order_id'] );

			if ( 'yes' === get_option( 'rs_enable_first_purchase_reward_points' ) ) {
				$order_count           = get_posts(
					array(
						'numberposts' => -1,
						'meta_key'    => '_customer_user',
						'meta_value'  => get_current_user_id(),
						'post_type'   => wc_get_order_types(),
						'post_status' => array( 'wc-pending', 'wc-processing', 'wc-on-hold', 'wc-completed' ),
					)
				);
				$first_purchase_points = RSMemberFunction::earn_points_percentage( get_current_user_id(), (float) rs_get_first_purchase_point( $order ) );
				$points                = ( '1' === count( $order_count ) ) ? ( $points + $first_purchase_points ) : $points;
			}

			if ( ! rs_restrict_product_purchase_point_when_free_shipping_is_enabled( $order_obj['order_id'] ) ) {
				return;
			}

			if ( empty( $points ) ) {
				return;
			}

			$converted_value = redeem_point_conversion( $points, $order_obj['order_userid'], 'price' );
			$currency_value  = srp_formatted_price( round_off_type_for_currency( $converted_value ) );
			?>
			<tfoot>
				<tr class="srp-points-thankyou-wrapper">
					<th class="srp-points-caption-thankyou"><?php echo do_shortcode( do_shortcode( get_option( 'rs_total_earned_point_caption_thank_you' ) ) ); ?></th>
					<td class="srp-points-value-thankyou">
						<?php
						echo do_shortcode( custom_message_in_thankyou_page( $points, $currency_value, 'rs_show_hide_equivalent_price_for_points_thankyou', 'rs_show_hide_custom_msg_for_points_thankyou', 'rs_custom_message_for_points_thankyou', $payment_plan_points ) );
						?>
					</td>
				</tr>
			</tfoot>
			<?php
		}

		/**
		 * Get reward points for order.
		 *
		 * @param int $order_id Order ID.
		 * */
		public static function get_reward_points_for_order( $order_id ) {

			$reward_points = 0;
			$order_object  = wc_get_order( $order_id );
			$buying_points = (float) srp_check_is_array( $order_object->get_meta( 'buy_points_for_current_order' ) ) ? array_sum( $order_object->get_meta( 'buy_points_for_current_order' ) ) : 0;
			$buying_points = RSMemberFunction::earn_points_percentage( get_current_user_id(), (float) $buying_points );

			if ( '1' === get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
				$product_purchase_points = (float) srp_check_is_array( $order_object->get_meta( 'points_for_current_order' ) ) ? array_sum( $order_object->get_meta( 'points_for_current_order' ) ) : 0;
				$product_purchase_points = RSMemberFunction::earn_points_percentage( get_current_user_id(), (float) $product_purchase_points );
				$reward_points           = $product_purchase_points + $buying_points;
			} elseif ( '2' === get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
				$reward_points = (float) $order_object->get_meta( 'points_for_current_order_based_on_cart_total' ) + $buying_points;
			} else {
				$reward_points = (float) $order_object->get_meta( 'rs_points_for_current_order_based_on_range' ) + $buying_points;
			}

			return $reward_points;
		}

		/**
		 * Display notices in cart and checkout.
		 * */
		public static function display_notices_in_cart_and_checkout() {

			self::complete_message_for_purchase();

			self::display_maximum_threshold_error_msg();

			self::display_minimum_quantity_restriction_error_msg();
		}

		/**
		 * Display complete message for product purchase in cart and checkout.
		 * */
		public static function complete_message_for_purchase() {
			if ( ! is_user_logged_in() ) {
				return;
			}

			if ( 'yes' !== get_option( 'rs_product_purchase_activated' ) && 'yes' !== get_option( 'rs_buyingpoints_activated' ) ) {
				return;
			}

			$show_msg = is_cart() ? get_option( 'rs_show_hide_message_for_total_points' ) : get_option( 'rs_show_hide_message_for_total_points_checkout_page' );
			if ( '2' === $show_msg ) {
				return;
			}

			if ( check_if_coupon_applied() ) {
				return;
			}

			if ( check_if_discount_applied() ) {
				return;
			}

			if ( 'yes' === get_option( 'rs_product_purchase_activated' ) ) {
				global $totalrewardpoints_payment_plan;
				$PaymentPlanPoints = srp_check_is_array( $totalrewardpoints_payment_plan ) ? round_off_type( array_sum( $totalrewardpoints_payment_plan ) ) : 0;
				/**
				 * Hook:srp_buying_points_for_payment_plan_in_cart.
				 *
				 * @since 1.0
				 */
				$points = $PaymentPlanPoints + apply_filters( 'srp_buying_points_for_payment_plan_in_cart', 0 );
				$points = ! rs_restrict_product_purchase_point_when_free_shipping_is_enabled() ? '' : $points;
			} elseif ( 'yes' === get_option( 'rs_buyingpoints_activated' ) ) {
				/**
				 * Hook:srp_buying_points_for_payment_plan_in_cart.
				 *
				 * @since 1.0
				 */
				$points = apply_filters( 'srp_buying_points_for_payment_plan_in_cart', 0 );
			}

			if ( empty( $points ) && class_exists( 'SUMOPaymentPlans' ) ) {
				$ShowMsgForPaymentPlan = is_cart() ? get_option( 'rs_show_hide_message_for_total_payment_plan_points' ) : get_option( 'rs_show_hide_message_for_total_points_checkout_page' );
				if ( '1' == $ShowMsgForPaymentPlan ) {
					$total_point_msg = is_cart() ? get_option( 'rs_message_payment_plan_total_price_in_cart' ) : get_option( 'rs_message_payment_plan_total_price_in_checkout' );
					$div_class1      = is_cart() ? 'sumo_reward_points_payment_plan_complete_message' : 'rs_complete_payment_plan_message_checkout';
					$div_class2      = is_cart() ? 'rs_cart_message' : 'rs_checkout_message';
					?>
					<div class="woocommerce-info <?php echo esc_attr( $div_class1 ); ?> <?php echo esc_attr( $div_class2 ); ?>">
						<?php echo do_shortcode( $total_point_msg ); ?>
					</div>
					<?php
				}
			} else {
				$points = total_points_for_current_purchase( WC()->cart->total, get_current_user_id() );
				$points = ! rs_restrict_product_purchase_point_when_free_shipping_is_enabled() ? 0 : $points;
				if ( empty( $points ) ) {
					return;
				}

				$total_point_msg = is_cart() ? get_option( 'rs_message_total_price_in_cart' ) : get_option( 'rs_message_total_price_in_checkout' );
				$div_class1      = is_cart() ? 'sumo_reward_points_complete_message' : 'rs_complete_message_checkout';
				$div_class2      = is_cart() ? 'rs_cart_message' : 'rs_checkout_message';
				if ( '1' === get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
					?>
					<div class="woocommerce-info <?php echo esc_attr( $div_class1 ); ?> <?php echo esc_attr( $div_class2 ); ?>">
						<?php echo do_shortcode( $total_point_msg ); ?>
					</div>
					<?php
				}
			}
		}

		/**
		 * Modified Points for Products.
		 * */
		public static function modified_points_for_products() {
			$points          = array();
			$original_points = self::original_points_for_product();
			if ( ! srp_check_is_array( $original_points ) ) {
				return $points;
			}

			foreach ( $original_points as $product_id => $point ) {
				$modified_points = self::coupon_points_conversion( $product_id, $point );
				if ( ! empty( $modified_points ) ) {
					$points[ $product_id ] = $modified_points;
				}
			}

			return $points;
		}

		/**
		 * Original Points for Products
		 * */
		public static function original_points_for_product() {
			$user_id = get_current_user_id();
			if ( 'earningonly' === check_banning_type( $user_id ) || 'both' === check_banning_type( $user_id ) ) {
				return array();
			}

			global $totalrewardpoints;
			$points        = array();
			$cart_contents = WC()->cart->cart_contents;
			if ( srp_check_is_array( $cart_contents ) ) {
				foreach ( $cart_contents as $value ) {
					if ( 'yes' === block_points_for_salepriced_product( $value['product_id'], $value['variation_id'] ) ) {
						continue;
					}

					$args          = array(
						'productid'   => $value['product_id'],
						'variationid' => $value['variation_id'],
						'item'        => $value,
					);
					$cart_quantity = isset( $value['quantity'] ) ? $value['quantity'] : 0;
					$product_id    = isset( $value['product_id'] ) ? $value['product_id'] : 0;
					$variation_id  = isset( $value['variation_id'] ) ? $value['variation_id'] : 0;
					$quantity      = rs_get_minimum_quantity_based_on_product_total( $product_id, $variation_id );

					if ( $quantity && $cart_quantity < $quantity ) {
						continue;
					}

					$Points               = check_level_of_enable_reward_point( $args );
					$user_role_percentage = RSMemberFunction::earn_points_percentage( $user_id, (float) $Points );
					if ( empty( $user_role_percentage ) ) {
						continue;
					}

					$totalrewardpoints = $Points;
					$ProductId         = ! empty( $value['variation_id'] ) ? $value['variation_id'] : $value['product_id'];

					if ( ! empty( $totalrewardpoints ) ) {
						if ( isset( $points[ $ProductId ] ) ) {
							$points[ $ProductId ] = $Points + $points[ $ProductId ];
						} else {
							$points[ $ProductId ] = $Points;
						}
					}
				}
			}
			return $points;
		}

		public static function coupon_points_conversion( $ProductId, $Points, $extra_args = array() ) {

			if ( empty( $Points ) ) {
				return $Points;
			}

			$DiscountedTotal = WC()->cart->coupon_discount_amounts;
			if ( ! srp_check_is_array( $DiscountedTotal ) ) {
				return $Points;
			}

			$DiscountedTotal = array_sum( array_values( $DiscountedTotal ) );
			$CouponAmounts   = self::get_product_price_for_individual_product( $ProductId, $Points, $DiscountedTotal );
			if ( ! srp_check_is_array( $CouponAmounts ) ) {
				return $Points;
			}

			$ConversionRate  = array();
			$ConvertedPoints = 0;

			$product_price = self::get_product_price_in_cart( $extra_args );

			foreach ( WC()->cart->applied_coupons as $CouponCode ) {
				$CouponObj    = new WC_Coupon( $CouponCode );
				$CouponObj    = srp_coupon_obj( $CouponObj );
				$ProductList  = $CouponObj['product_ids'];
				$CouponAmount = $CouponAmounts[ $CouponCode ][ $ProductId ];
				$LineTotal    = self::get_product_price_for_included_products( $ProductList );

				if ( empty( $ProductList ) && $product_price ) {
					$ConvertedPoints = $DiscountedTotal / $product_price;
				} elseif ( $LineTotal ) {
					$ConvertedPoints = $CouponAmount / $LineTotal;
				}

				$ConvertedAmount = $ConvertedPoints * $Points;
				if ( $Points > $ConvertedAmount ) {
					$ConversionRate[] = $Points - $ConvertedAmount;
				}
			}

			return end( $ConversionRate );
		}

		/**
		 * Get Product Price for individual products.
		 *
		 * @param array $product_id Product ID.
		 * @param float $points Points.
		 * @param float $discount_total Discount Total.
		 */
		public static function get_product_price_for_individual_product( $product_id, $points, $discount_total ) {
			$coupon_amount = array();
			foreach ( WC()->cart->applied_coupons as $coupon_code ) {
				$coupon_obj   = new WC_Coupon( $coupon_code );
				$coupon_obj   = srp_coupon_obj( $coupon_obj );
				$product_list = $coupon_obj['product_ids'];
				if ( ! empty( $product_list ) ) {
					if ( in_array( $product_id, $product_list ) ) {
						$coupon_amount[ $coupon_code ][ $product_id ] = $discount_total;
					}
				} else {
					$coupon_amount[ $coupon_code ][ $product_id ] = $discount_total;
				}
			}
			return $coupon_amount;
		}

		/**
		 * Get Product Price for included products.
		 *
		 * @param array $product_list Product List.
		 */
		public static function get_product_price_for_included_products( $product_list ) {
			$line_total = array();
			foreach ( WC()->cart->cart_contents as $item ) {
				$product_id = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
				if ( in_array( $product_id, $product_list ) ) {
					$line_total[] = $item['line_subtotal'];
				}
			}
			return array_sum( $line_total );
		}

		/**
		 * Get Product Price in Cart.
		 *
		 * @param array $referrer_args Referrer arguments.
		 */
		public static function get_product_price_in_cart( $referrer_args = array() ) {
			$price = array();
			foreach ( WC()->cart->cart_contents as $items ) {
				$args = array(
					'productid'   => $items['product_id'],
					'variationid' => $items['variation_id'],
					'item'        => $items,
				);

				$points            = check_level_of_enable_reward_point( $args );
				$totalrewardpoints = RSMemberFunction::earn_points_percentage( get_current_user_id(), (float) $points );

				if ( srp_check_is_array( $referrer_args ) ) {
					$args              = array_merge( $args, $referrer_args );
					$points            = check_level_of_enable_reward_point( $args );
					$totalrewardpoints = RSMemberFunction::earn_points_percentage( $args['referred_user'], (float) $points );
				}

				if ( empty( $totalrewardpoints ) ) {
					continue;
				}

				$price[] = $items['line_subtotal'];
			}
			return array_sum( $price );
		}

		/**
		 * Display the error message for Minimum threshold.
		 */
		public static function display_maximum_threshold_error_msg() {
			if ( 'yes' !== get_option( 'rs_enable_disable_max_earning_points_for_user' ) ) {
				return;
			}

			$max_threshold_points = get_option( 'rs_max_earning_points_for_user' );
			if ( ! $max_threshold_points ) {
				return;
			}

			if ( ! get_current_user_id() ) {
				return;
			}

			if ( ! srp_check_is_array( WC()->cart->cart_contents ) ) {
				return;
			}

			$points_data      = new RS_Points_Data( get_current_user_id() );
			$available_points = $points_data->total_available_points();
			if ( empty( $available_points ) ) {
				return;
			}

			$message = get_option( 'rs_maximum_threshold_error_message', 'Maximum Threshold Limit is <b>[threshold_value]</b>. Hence, you cannot earn points more than <b>[threshold_value]</b>' );
			$message = str_replace( '[threshold_value]', $max_threshold_points, $message );
			?>
			<div class="woocommerce-error rs-maximum-threshold-error"><?php echo esc_html( $message ); ?></div>    
			<?php
		}

		/**
		 * Display the error message for Minimum Quantity restriction.
		 */
		public static function display_minimum_quantity_restriction_error_msg() {
			if ( ! is_user_logged_in() ) {
				return;
			}

			if ( 'yes' !== get_option( 'rs_product_purchase_activated' ) ) {
				return;
			}

			$cart_contents = WC()->cart->cart_contents;
			if ( ! srp_check_is_array( $cart_contents ) ) {
				return;
			}

			$user_id = get_current_user_id();

			$min_qty_error_messages = array();
			foreach ( $cart_contents as $cart ) {

				$variation_id = isset( $cart['variation_id'] ) ? $cart['variation_id'] : '';
				$product_id   = isset( $cart['product_id'] ) ? $cart['product_id'] : '';
				$qty          = isset( $cart['quantity'] ) ? $cart['quantity'] : 1;

				$_product_id = ! empty( $variation_id ) ? $variation_id : $product_id;
				$product     = wc_get_product( $_product_id );
				if ( ! is_object( $product ) ) {
					continue;
				}

				$min_quantity = rs_get_minimum_quantity_based_on_product_total( $product_id, $variation_id );
				if ( ! $min_quantity || $qty >= $min_quantity ) {
					continue;
				}

				$args = array(
					'productid'   => $product_id,
					'variationid' => $variation_id,
					'item'        => $cart,
				);

				$reward_point = check_level_of_enable_reward_point( $args );
				$reward_point = RSMemberFunction::earn_points_percentage( $user_id, (float) $reward_point );
				if ( ! $reward_point ) {
					continue;
				}

				$message                  = get_option( 'rs_minimum_quantity_error_message', 'Minimum <b>{min_quantity}</b> quantities required to earn points by purchasing <b>{product_name}</b>' );
				$min_qty_error_messages[] = str_replace( array( '{product_name}', '{min_quantity}' ), array( $product->get_name(), $min_quantity ), $message );
			}

			if ( ! srp_check_is_array( array_filter( $min_qty_error_messages ) ) ) {
				return;
			}
			?>
			<div class="woocommerce-error rs-minimum-quantity-error-message">
				<?php echo esc_html( implode( '<br>', $min_qty_error_messages ) ); ?>
			</div>
			<?php
		}
	}

	RSFrontendAssets::init();
}
