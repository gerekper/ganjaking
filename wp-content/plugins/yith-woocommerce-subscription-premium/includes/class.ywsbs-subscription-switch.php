<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YWSBS_Subscription_Switch set all plugin shortcodes
 *
 * @class   YWSBS_Subscription_Switch
 * @package YITH WooCommerce Subscription
 * @since   2.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Implements the YWSBS_Subscription_Switch class.
 *
 * @class   YWSBS_Subscription_Switch
 * @package YITH
 * @since   2.0.0
 * @author  YITH
 */
class YWSBS_Subscription_Switch {

	/**
	 * Single instance of the class
	 *
	 * @var YWSBS_Subscription_Switch
	 */
	protected static $instance;
	/**
	 * Save inside and array the switchable variation of a product.
	 *
	 * @var array
	 */
	protected static $switchable_variations = array();

	/**
	 * Returns single instance of the class
	 *
	 * @return YWSBS_Subscription_Switch
	 * @since  1.0.0
	 */
	public static function get_instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * Constructor for the shortcode class
	 */
	public function __construct() {
		add_action( 'wp_loaded', array( $this, 'subscription_switch' ), 30 );
		add_filter( 'woocommerce_cart_item_price', array( $this, 'change_price_in_cart_html' ), 99, 3 );

		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'set_switch_changes_on_cart' ), 201 );
		add_filter( 'ywsbs_subscription_subtotal_html', array( $this, 'change_subtotal_html_on_checkout' ), 10, 3 );
		add_filter( 'woocommerce_cart_item_name', array( $this, 'change_cart_item_name' ), 10, 2 );

		// cancel the old subscription.
		add_action( 'ywsbs_subscription_started', array( $this, 'cancel_the_previous_subscription' ) );
	}

	/**
	 * Add a text before the product name during the switch.
	 *
	 * @param string $name Product name.
	 * @param array  $cart_item Cart item.
	 *
	 * @return string
	 */
	public function change_cart_item_name( $name, $cart_item ) {
		if ( ! isset( $cart_item['ywsbs-subscription-switch'] ) ) {
			return $name;
		}

		$name = get_option( 'ywsbs_text_new_plan_on_cart', __( 'Change plan to:', 'yith-woocommerce-subscription' ) ) . ' ' . $name;

		return $name;
	}


	/**
	 * Set the product on cart to set the switch
	 *
	 * @param array $cart_item Cart item.
	 *
	 * @return array
	 */
	public function set_switch_changes_on_cart( $cart_item ) {

		if ( isset( $cart_item['ywsbs-subscription-switch'] ) ) {
			$switch_info  = $cart_item['ywsbs-subscription-switch'];
			$subscription = ywsbs_get_subscription( $switch_info['subscription_id'] );
			$product      = $cart_item['data'];

			if ( $subscription ) {

				$subscription_price = $switch_info['calculated_fee'];

				$cart_item['data']->set_price( $subscription_price );
				$cart_item['ywsbs-subscription-info']['recurring_price']       = $switch_info['recurring_price'];
				$cart_item['ywsbs-subscription-info']['price_is_per']          = $product->get_meta( '_ywsbs_price_is_per' );
				$cart_item['ywsbs-subscription-info']['price_time_option']     = $product->get_meta( '_ywsbs_price_time_option' );
				$cart_item['ywsbs-subscription-info']['fee']                   = $switch_info['fee'];
				$cart_item['ywsbs-subscription-info']['trial_per']             = 0;
				$cart_item['ywsbs-subscription-info']['max_length']            = YWSBS_Subscription_Helper::get_subscription_product_max_length( $product );
				$cart_item['ywsbs-subscription-info']['next_payment_due_date'] = $switch_info['next_payment_due_date'];
				$cart_item['ywsbs-subscription-info']['switching']             = 1;

			}
		}

		return $cart_item;
	}

	/**
	 * Change price in cart.
	 *
	 * @param string $price_html HTML price.
	 * @param array  $cart_item Cart Item.
	 * @param string $cart_item_key Cart Item Key.
	 *
	 * @return mixed|void
	 */
	public function change_price_in_cart_html( $price_html, $cart_item, $cart_item_key ) {
		if ( ! isset( $cart_item['ywsbs-subscription-switch'], $cart_item['data'] ) ) {
			return $price_html;
		}

		$product_id        = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];
		$subscription_info = $cart_item['ywsbs-subscription-info'];

		$product       = $cart_item['data'];
		$price         = apply_filters( 'ywsbs_change_price_in_cart_html', $subscription_info['recurring_price'], $cart_item['data'] );
		$price_current = apply_filters( 'ywsbs_change_price_current_in_cart_html', $product->get_price(), $product );
		$product->set_price( $price );
		$price_html = YWSBS_Subscription_Cart()->change_general_price_html( $product, 1, true, $cart_item );
		$price_html = apply_filters( 'ywsbs_get_price_html', $price_html, $cart_item, $product_id );
		$product->set_price( $cart_item['data']->get_price() );

		return $price_html;

	}


	/**
	 * Change the subtotal price for the switch.
	 *
	 * @param string     $price_html Price in html format.
	 * @param WC_Product $product Cart item data.
	 * @param array      $cart_item Cart item.
	 */
	public function change_subtotal_html_on_checkout( $price_html, $product, $cart_item ) {
		if ( ! isset( $cart_item['ywsbs-subscription-switch'], $cart_item['ywsbs-subscription-info'] ) ) {
			return $price_html;
		}

		$switch_info       = $cart_item['ywsbs-subscription-switch'];
		$subscription_info = $cart_item['ywsbs-subscription-info'];

		$product_id = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];

		$product       = $cart_item['data'];
		$price_current = apply_filters( 'ywsbs_change_subtotal_price_current_in_cart_html', $product->get_price(), $product );
		$price         = apply_filters( 'ywsbs_change_subtotal_price_in_cart_html', $switch_info['fee'], $cart_item['data'], $cart_item );
		// get the recurring price.
		$product->set_price( $price );

		$price_html = apply_filters(
			'ywsbs_get_price_html',
			wc_price(
				wc_get_price_to_display(
					$product,
					array(
						'qty'   => $cart_item['quantity'],
						'price' => $price,
					)
				)
			),
			$cart_item,
			$product_id
		);

		// set the original cart price.
		$product->set_price( $price_current );

		return $price_html;

	}

	/**
	 * Switch the subscription.
	 */
	public function subscription_switch() {
		if ( ! isset( $_GET['plan'], $_GET['_nonce'] ) ) { // phpcs:ignore
			return;
		}

		$subscription_id = sanitize_text_field( wp_unslash( $_GET['subscription'] ) ); // phpcs:ignore
		$subscription    = ywsbs_get_subscription( $subscription_id );
		$new_plan_id     = sanitize_text_field( wp_unslash( $_GET['plan'] ) );
		/**
		 * Product variation.
		 *
		 * @var WC_Product_Variation
		 */
		$new_plan = wc_get_product( $new_plan_id );

		if ( ! $subscription || ! $subscription->can_be_switchable() || wp_verify_nonce( $_GET['_nonce'], 'ywsbs-switch-' . $subscription_id ) === false ) { // phpcs:ignore
			wc_add_notice( __( 'There was an error with your request. Please try again.', 'yith-woocommerce-subscription' ), 'error' );

			return;
		}

		WC()->cart->empty_cart();

		$switch_info = $this->switch_info( $subscription, $new_plan );

		$item_data['ywsbs-subscription-switch'] = $switch_info;

		$cart_item_key = WC()->cart->add_to_cart( $subscription->get_product_id(), $switch_info['quantity'], $new_plan_id, array(), $item_data );

		if ( ! $cart_item_key ) {
			wc_add_notice( __( 'It is not possible complete your request. Please try again.', 'yith-woocommerce-subscription' ), 'error' );
		}

	}

	/**
	 * Check if the subscription can be switch
	 *
	 * @param YWSBS_Subscription $subscription Current subscription.
	 *
	 * @return bool
	 */
	public static function is_a_switchable_subscription( $subscription ) {

		$switchable_variations = self::get_switchable_variations( $subscription );

		return $switchable_variations && count( $switchable_variations ) > 0;

	}


	/**
	 * Return a list of switchable variation products
	 *
	 * @param YWSBS_Subscription $subscription Current subscription.
	 *
	 * @return boolean|array
	 */
	public static function get_switchable_variations( $subscription ) {

		$variation_id = $subscription->get_variation_id();

		if ( ! $variation_id || ! $subscription->has_status( 'active' ) ) {
			return false;
		}

		if ( isset( self::$switchable_variations[ $variation_id ] ) ) {
			return self::$switchable_variations[ $variation_id ];
		}

		$variation = wc_get_product( $variation_id );

		if ( ! self::is_a_switchable_product_variation( $variation ) ) {
			return false;
		}

		$main_product_id = $variation->get_parent_id();
		$main_product    = wc_get_product( $main_product_id );

		if ( ! $main_product ) {
			return false;
		}

		$childs                = $main_product->get_children();
		$switchable_variations = array();

		if ( $childs ) {
			foreach ( $childs as $child_id ) {
				if ( $variation->get_id() !== $child_id ) {
					$child         = wc_get_product( $child_id );
					$is_switchable = self::is_a_switchable_product_variation( $child );

					if ( false === $is_switchable || ! $child->is_purchasable() ) {
						continue;
					}

					if ( 'upgrade' === $is_switchable ) {
						// check the level difference to upgrade.
						$variation_priority = (int) $variation->get_meta( '_ywsbs_switchable_priority' );
						$child_priority     = (int) $child->get_meta( '_ywsbs_switchable_priority' );
						if ( $variation_priority > $child_priority ) {
							continue;
						}
					}

					array_push( $switchable_variations, $child->get_id() );
				}
			}
		}

		self::$switchable_variations[ $variation->get_id() ] = $switchable_variations;

		return apply_filters( 'ywsbs_get_switchable_variations', $switchable_variations, $subscription );

	}


	/**
	 * Check if a variation is a subscription switchable.
	 *
	 * @param int|WC_Product_Variation $variation Variation.
	 *
	 * @return boolean|string
	 */
	public static function is_a_switchable_product_variation( $variation ) {
		$result = false;

		if ( is_numeric( $variation ) ) {
			$variation = wc_get_product( $variation );
		}

		if ( ! $variation ) {
			return false;
		}

		$is_subscription = ywsbs_is_subscription_product( $variation );

		if ( $is_subscription ) {
			$is_switchable = $variation->get_meta( '_ywsbs_switchable' );

			$result = ( 'no' === $is_switchable ) ? false : $is_switchable;

		}

		return apply_filters( 'ywsbs_is_a_switchable_subscription', $result, $variation );
	}

	/**
	 * Calculate fee amount
	 *
	 * @param YWSBS_Subscription   $subscription Current subscription.
	 * @param WC_Product_Variation $new New Variation.
	 *
	 * @return float
	 */
	public function calculate_fee( $subscription, $new ) {
		$new_fee_amount = (float) ywsbs_get_product_fee( $new );

		if ( empty( $new_fee_amount ) ) {
			return 0;
		}

		$prorate_fee_new = $new->get_meta( '_ywsbs_prorate_fee' );

		switch ( $prorate_fee_new ) {
			case 'no':
				$fee_amount = 0;
				break;
			case 'difference':
				$prorate_fee_old = $subscription->get_fee();
				$difference      = $prorate_fee_new - $prorate_fee_old;
				$fee_amount      = $difference > 0 ? $difference : 0;
				break;
			case 'yes':
				$fee_amount = $new_fee_amount;
				break;
		}

		return apply_filters( 'ywsbs_switch_calculate_fee_amount', $fee_amount, $subscription, $new );
	}

	/**
	 * Get the switch relationship.
	 *
	 * @param WC_Product_Variation $old Old Variation.
	 * @param WC_Product_Variation $new New Variation.
	 *
	 * @return string
	 */
	public static function get_switch_relationship( $old, $new ) {

		$priority_new = $new->get_meta( '_ywsbs_switchable_priority' );
		$priority_old = $old->get_meta( '_ywsbs_switchable_priority' );

		if ( $priority_new > $priority_old ) {
			$switch_relationship = 'upgrade';
		} elseif ( $priority_new < $priority_old ) {
			$switch_relationship = 'downgrade';
		} else {
			$switch_relationship = 'crossgrade';
		}

		return apply_filters( 'ywsbs_switch_relationship', $switch_relationship, $old, $new );
	}


	/**
	 * Get the switch relationship text.
	 *
	 * @param WC_Product_Variation $old Old Variation.
	 * @param WC_Product_Variation $new New Variation.
	 *
	 * @return string
	 */
	public static function get_switch_relationship_text( $old, $new ) {

		$relationship = self::get_switch_relationship( $old, $new );
		$text         = '';

		switch ( $relationship ) {
			case 'upgrade':
				$text = __( 'Upgrade to', 'yith-woocommerce-subscription' );
				break;
			case 'downgrade':
				$text = __( 'Downgrade to', 'yith-woocommerce-subscription' );
				break;
			default:
				$text = __( 'Change to', 'yith-woocommerce-subscription' );

		}

		return apply_filters( 'ywsbs_switch_relationship_text', $text, $relationship, $old, $new );
	}

	/**
	 * Return the amount that customer does not used of a subscription.
	 *
	 * @param YWSBS_Subscription $subscription Current subscription.
	 */
	public static function calculate_unused_amount( $subscription ) {
		$unused_period = ywsbs_get_unused_subscription_days( $subscription );
		$daily_amount  = $subscription->get_daily_amount();

		return $unused_period * $daily_amount;
	}


	/**
	 * Return an array with the switch information.
	 *
	 * @param YWSBS_Subscription $subscription Current subscription.
	 * @param int                $new_product_id Variation to switch.
	 *
	 * @return array
	 */
	public function switch_info( $subscription, $new_product_id ) {

		$subscription_daily_amount = $subscription->get_daily_amount();
		$unused_period             = ywsbs_get_unused_subscription_days( $subscription );

		$new_product              = wc_get_product( $new_product_id );
		$new_product_daily_amount = ywsbs_get_daily_amount_of_a_product( $new_product );
		$prorate_recurring_amount = $new_product->get_meta( '_ywsbs_prorate_recurring_payment' );

		$relationship = self::get_switch_relationship( $subscription->get_product(), $new_product );
		$gap_amount   = ( $new_product_daily_amount - $subscription_daily_amount ) * $unused_period;

		$next_payment_due_date = $subscription->get_confirmed_valid_date();

		// todo:check combinations.
		if ( YWSBS_Subscription_Synchronization()->is_synchronizable( $new_product ) ) {
			$next_payment_due_date = YWSBS_Subscription_Synchronization()->get_next_payment_due_date_sync( $next_payment_due_date, $new_product );

			$today                           = new DateTime();
			$next_payment_due_date_date_time = new DateTime( '@' . $next_payment_due_date );

			if ( $today->format( 'Y-m-d' ) === $next_payment_due_date_date_time->format( 'Y-m-d' ) ) {
				$gap_amount            = $new_product->get_price();
				$next_payment_due_date = YWSBS_Subscription_Helper::get_billing_payment_due_date( $new_product );
			} else {
				$gap_amount = YWSBS_Subscription_Synchronization()->get_new_price_sync( $gap_amount, $new_product, $next_payment_due_date );
			}
		}

		// if gap amount is negative the admin has a debit to customer.

		if ( 'crossgrade' === $relationship ) {
			$relationship = ( $subscription_daily_amount <= $new_product_daily_amount ) ? 'upgrade' : 'downgrade';
		}

		$switch_info = array(
			'recurring_price'           => $new_product->get_price(),
			'subscription_id'           => $subscription->get_id(),
			'fee'                       => 0,
			'calculated_fee'            => $this->calculate_fee( $subscription, $new_product ),
			'next_payment_due_date'     => 0,
			'quantity'                  => $subscription->get_quantity(),
			'subscription_daily_amount' => $subscription_daily_amount,
			'new_product_daily_amount'  => $new_product_daily_amount,
			'relationship'              => $relationship,
			'unused_period'             => $unused_period,
		);

		if ( apply_filters( 'ywsbs_jump_switch_rules', false ) ) {
			$switch_info['fee']            = $new_product->get_price() + $switch_info['calculated_fee'];
			$switch_info['calculated_fee'] = 0;

			return $switch_info;
		}

		$rest_from_old = $unused_period * $subscription_daily_amount;
		$to_pay        = ( $new_product->get_price() - $rest_from_old );

		switch ( $relationship ) {
			case 'upgrade':
				if ( in_array( $prorate_recurring_amount, array( 'no', 'downgrade' ), true ) ) {
					$switch_info['gap_amount']            = $gap_amount;
					$switch_info['next_payment_due_date'] = $next_payment_due_date;
					$switch_info['fee']                   = ( $gap_amount >= 0 ) ? $gap_amount : 0;

				} else {

					$switch_info['to_pay'] = $to_pay;
					if ( $to_pay >= 0 ) {
						$switch_info['fee']                   = $to_pay;
						$switch_info['next_payment_due_date'] = ywsbs_get_timestamp_from_option( time(), $new_product->get_meta( '_ywsbs_price_is_per' ), $new_product->get_meta( '_ywsbs_price_time_option' ) );
					} else {
						$num_days_of_delay                    = floor( abs( $to_pay ) / $new_product_daily_amount );
						$switch_info['next_payment_due_date'] = ywsbs_get_timestamp_from_option( time(), $num_days_of_delay, 'days' );
						$switch_info['fee']                   = 0;
						$switch_info['num_days_of_delay']     = $num_days_of_delay;
					}
				}
				break;
			case 'downgrade':
				if ( in_array( $prorate_recurring_amount, array( 'no', 'upgrade' ), true ) ) {
					$switch_info['gap_amount']            = $gap_amount;
					$switch_info['next_payment_due_date'] = $next_payment_due_date;
					$switch_info['fee']                   = ( $gap_amount >= 0 ) ? $gap_amount : 0;
				} else {
					$switch_info['to_pay'] = $to_pay;
					if ( $to_pay >= 0 ) {
						$switch_info['fee']                   = $to_pay;
						$switch_info['next_payment_due_date'] = ywsbs_get_timestamp_from_option( time(), $new_product->get_meta( '_ywsbs_price_is_per' ), $new_product->get_meta( '_ywsbs_price_time_option' ) );
					} else {
						$num_days_of_delay                    = floor( abs( $to_pay ) / $new_product_daily_amount );
						$switch_info['next_payment_due_date'] = ywsbs_get_timestamp_from_option( time(), $num_days_of_delay, 'days' );
						$switch_info['fee']                   = 0;
						$switch_info['num_days_of_delay']     = $num_days_of_delay;
					}
				}
				break;
		}

		$switch_info['next_payment_due_date_readable'] = date_i18n( wc_date_format(), $switch_info['next_payment_due_date'] );
		return apply_filters( 'ywsbs_switch_info', $switch_info, $subscription, $new_product );

	}

	/**
	 * Cancel the previous subscription when the new start.
	 *
	 * @param int $new_subscription_id New Subscription.
	 */
	public function cancel_the_previous_subscription( $new_subscription_id ) {
		$subscription = ywsbs_get_subscription( $new_subscription_id );
		if ( '' !== $subscription->get( 'switched_from' ) ) {
			$old_subscription = ywsbs_get_subscription( $subscription->get( 'switched_from' ) );
			$old_subscription->update_status( 'cancel-now' );
			/* translators: %s: The new subscription number */
			YITH_WC_Activity()->add_activity( $old_subscription->get_id(), 'switched', 'success', $old_subscription->get_order_id(), sprintf( esc_html_x( 'This subscription has been switched to the subscription %s', '%s: The new subscription number', 'yith-woocommerce-subscription' ), $subscription->get_number() ) );
			/* translators: %s: The new subscription number */
			YITH_WC_Activity()->add_activity( $subscription->get_id(), 'switched', 'success', $subscription->get_order_id(), sprintf( esc_html_x( 'This subscription has been switched from the subscription %s', '%s: The new subscription number', 'yith-woocommerce-subscription' ), $old_subscription->get_number() ) );
		}
	}

}
