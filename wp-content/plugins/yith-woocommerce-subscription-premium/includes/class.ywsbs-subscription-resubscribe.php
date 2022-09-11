<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Implements YITH WooCommerce Subscription
 *
 * @class   YWSBS_Subscription_Resubscribe
 * @package YITH WooCommerce Subscription
 * @since   2.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'YWSBS_Subscription_Resubscribe' ) ) {

	/**
	 * Class YWSBS_Subscription_Resubscribe
	 */
	class YWSBS_Subscription_Resubscribe {


		/**
		 * Single instance of the class
		 *
		 * @var YWSBS_Subscription_Resubscribe
		 */
		protected static $instance;


		/**
		 * Returns single instance of the class
		 *
		 * @return YWSBS_Subscription_Resubscribe
		 * @since  2.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'wp_loaded', array( $this, 'resubscribe' ), 10 );
			add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'set_resubscribe_changes_on_cart' ), 200 );

			add_action( 'ywsbs_subscription_created', array( $this, 'register_resubscribed_subscription_in_parent' ) );
			add_action( 'woocommerce_single_product_summary', array( $this, 'show_resuscribe_button_on_product' ), 30 );
			add_filter( 'woocommerce_load_cart_from_session', array( $this, 'validate_cart_contents' ), 1 );
			add_filter( 'woocommerce_cart_loaded_from_session', array( $this, 'cart_loaded_from_session' ), 10 );
		}

		/**
		 * Filter is purchasable on load cart from session
		 *
		 * @param WC_Cart $cart Cart.
		 */
		public function cart_loaded_from_session( $cart ) {
			add_filter( 'woocommerce_is_purchasable', array( 'YITH_WC_Subscription_Limit', 'is_purchasable' ), 10, 2 );
		}

		/**
		 * Validate cart content.
		 *
		 * @param WC_Cart $cart Cart.
		 */
		public function validate_cart_contents( $cart ) {
			remove_filter( 'woocommerce_is_purchasable', array( 'YITH_WC_Subscription_Limit', 'is_purchasable' ), 10 );
			return $cart;
		}

		/**
		 * Filter is_purchasable property of a product.
		 *
		 * @param bool       $is_purchasable Current is purchasable value.
		 * @param WC_Product $product Current product.
		 */
		public static function is_purchasable( $is_purchasable, $product ) {

			if ( ! $is_purchasable && self::get_resubscribes_product_subscripition( $product ) ) {
				$is_purchasable = true;
			}

			return $is_purchasable;
		}

		/**
		 * Resubscribe
		 *
		 * @return void
		 */
		public function resubscribe() {
			if ( ! isset( $_GET['ywsbs_resubscribe'] ) || ! isset( $_GET['_nonce'] ) ) { // phpcs:ignore
				return;
			}

			$subscription_id = sanitize_text_field( wp_unslash( $_GET['ywsbs_resubscribe'] ) ); // phpcs:ignore
			$subscription    = ywsbs_get_subscription( $subscription_id );

			if ( ! $subscription || ! $subscription->can_be_resubscribed() || wp_verify_nonce( $_GET['_nonce'], 'ywsbs-resubscribe-' . $subscription_id ) === false ) { // phpcs:ignore
				wc_add_notice( __( 'There was an error with your request. Please try again.', 'yith-woocommerce-subscription' ), 'error' );
				return;
			}

			WC()->cart->empty_cart();

			remove_filter( 'woocommerce_is_purchasable', array( 'YITH_WC_Subscription_Limit', 'is_purchasable' ), 10 );

			$product = $subscription->get_product();

			if ( ! $product || ! $product->is_purchasable() ) {
				wc_add_notice( __( 'It is not possible complete your request. This product can be purchasable.', 'yith-woocommerce-subscription' ), 'error' );
				return;
			}

			$item_data['ywsbs-subscription-resubscribe'] = array(
				'subscription_id' => $subscription_id,
			);

			$cart_item_key = WC()->cart->add_to_cart( $subscription->product_id, $subscription->quantity, $subscription->variation_id, $subscription->variation, $item_data );

			if ( ! $cart_item_key ) {
				wc_add_notice( __( 'It is not possible complete your request. Please try again.', 'yith-woocommerce-subscription' ), 'error' );
			}
		}

		/**
		 * Set the product on cart to set the resubscribe
		 *
		 * @param array $cart_item Cart item.
		 *
		 * @return array
		 */
		public function set_resubscribe_changes_on_cart( $cart_item ) {

			if ( isset( $cart_item['ywsbs-subscription-resubscribe'], $cart_item['ywsbs-subscription-info'] ) ) {

				if ( get_option( 'ywsbs_resubscribe_condition', 'yes' ) === 'yes' ) {
					$subscription = ywsbs_get_subscription( $cart_item['ywsbs-subscription-resubscribe']['subscription_id'] );
					$quantity     = $cart_item['quantity'];
					if ( $subscription ) {
						if ( 'yes' === get_option( 'woocommerce_prices_include_tax', 'no' ) ) {
							$subscription_price = ( $subscription->get_line_subtotal() + $subscription->get_line_subtotal_tax() ) / $quantity;
						} else {
							$subscription_price = $subscription->get_line_subtotal() / $quantity;
						}

						$cart_item['data']->set_price( $subscription_price );

						$cart_item['ywsbs-subscription-info']['price_is_per']      = $subscription->get_price_is_per();
						$cart_item['ywsbs-subscription-info']['price_time_option'] = $subscription->get_price_time_option();
						$cart_item['ywsbs-subscription-info']['fee']               = 0;
						$cart_item['ywsbs-subscription-info']['trial_per']         = 0;
						$cart_item['ywsbs-subscription-info']['max_length']        = $subscription->get_max_length();
					}
				} else {
					$cart_item['ywsbs-subscription-info']['fee']       = 0;
					$cart_item['ywsbs-subscription-info']['trial_per'] = 0;
				}
			}

			return $cart_item;
		}

		/**
		 * Register the resubscribed subscription inside the parent subscription.
		 *
		 * @param int $subscription_id ID of resubscribed subscription.
		 */
		public function register_resubscribed_subscription_in_parent( $subscription_id ) {
			$resubscribed_subscription = ywsbs_get_subscription( $subscription_id );

			if ( $resubscribed_subscription && $resubscribed_subscription->get( 'parent_subscription' ) !== '' ) {
				$subscription_parent = ywsbs_get_subscription( $resubscribed_subscription->get( 'parent_subscription' ) );
				if ( $subscription_parent ) {
					$subscription_parent->set( 'child_subscription', $subscription_id );
					$subscription_parent_order = $subscription_parent->get_order();
					if ( $subscription_parent_order ) {
						$subscription_parent_order->update_meta_data( '_child_subscription', $subscription_id );
						$subscription_parent_order->save();
					}
				}
			}
		}

		/**
		 * Return true if this product can be resubscribed.
		 *
		 * @param WC_Product $product Product.
		 * @return bool|YWSBS_Subscription
		 */
		public static function get_resubscribes_product_subscripition( $product ) {

			if ( ! $product ) {
				return false;
			}

			$user_subscriptions = YWSBS_Subscription_User::get_subscriptions_by_product( get_current_user_id(), $product->get_id() );
			$result             = false;
			if ( $user_subscriptions ) {
				foreach ( $user_subscriptions as $subscription ) {
					if ( ! $subscription->can_be_resubscribed() ) {
						$result = false;
						break;
					} else {
						$result = $subscription;
					}
				}
			}

			return apply_filters( 'ywsbs_can_be_a_resubscribes_product', $result, $product );
		}

		/**
		 * Show a message if the product can be purchased to resubscribe.
		 *
		 * @return void
		 */
		public static function show_resuscribe_button_on_product() {
			global $product;
			YITH_WC_Subscription_Limit::is_limited( $product ) ;

			if ( ! $product || ! YITH_WC_Subscription_Limit::is_limited( $product ) ) {
				return;
			}

			$resubscribe_button        = '';
			$resubscribed_subscription = self::get_resubscribes_product_subscripition( $product );

			if ( $resubscribed_subscription ) {
				$resubscribe_button = '<div class="ywsbs-resubscribe"> 
					<a href="' . esc_url( ywsbs_get_resubscribe_subscription_url( $resubscribed_subscription ) ) . '" class="btn button">' . esc_html__( 'Resubscribe', 'yith-woocommerce-subscription' ) . '</a>
				</div>';
			}

			echo apply_filters( 'ywsbs_show_resuscribe_button_on_product', $resubscribe_button, $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}


	}
}

/**
 * Unique access to instance of YWSBS_Subscription_Resubscribe class
 *
 * @return YWSBS_Subscription_Resubscribe
 */
function YWSBS_Subscription_Resubscribe() { //phpcs:ignore
	return YWSBS_Subscription_Resubscribe::get_instance();
}
