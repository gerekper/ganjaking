<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YWSBS_Subscription Object.
 *
 * @class   YITH_WC_Subscription
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

require_once YITH_YWSBS_INC . 'legacy/abstract.ywsbs-subscription-legacy.php';

if ( ! class_exists( 'YWSBS_Subscription' ) ) {

	/**
	 * Class YWSBS_Subscription
	 */
	class YWSBS_Subscription extends YWSBS_Subscription_Legacy {


		/**
		 * Single instance of the class
		 *
		 * @var YWSBS_Subscription
		 */
		protected static $instance;

		/**
		 * The subscription (post) ID.
		 *
		 * @var int
		 */
		public $id = 0;

		/**
		 * $post Stores post data
		 *
		 * @var $post WP_Post
		 */
		public $post = null;

		/**
		 * Stores the properties of a subscription.
		 *
		 * @var array
		 */
		protected $array_prop = array();

		/**
		 * Subscription main order
		 *
		 * @var WC_Order
		 */
		public $order = null;

		/**
		 * Subscription product
		 *
		 * @var WC_Product
		 */
		public $product = null;

		/**
		 * Returns single instance of the class
		 *
		 * @return YWSBS_Subscription
		 * @since  1.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 *
		 * Initialize the YWSBS_Subscription Object
		 *
		 * @param int   $subscription_id Subscription ID.
		 * @param array $args Arguments.
		 *
		 * @since 1.0.0
		 */
		public function __construct( $subscription_id = 0, $args = array() ) {

			// populate the subscription if $subscription_id is defined.
			if ( $subscription_id ) {
				$this->set( 'id', $subscription_id );
				$this->empty_cache();
				$this->post = get_post( $subscription_id );
			}

			// create a new subscription if $args is passed.
			if ( empty( $subscription_id ) && ! empty( $args ) ) {
				$this->add_subscription( $args );
			}

		}

		/**
		 * Return an array of all custom fields subscription.
		 *
		 * @return array
		 * @since  1.0.0
		 */
		protected function get_default_meta_data() {
			return array(
				'status'                  => 'pending',
				'start_date'              => '',
				'payment_due_date'        => '',
				'expired_date'            => '',
				'cancelled_date'          => '',
				'end_date'                => '',
				'num_of_pauses'           => 0,
				'date_of_pauses'          => array(),
				'expired_pause_date'      => '',
				'sum_of_pauses'           => '',
				'paypal_subscriber_id'    => '',
				'paypal_transaction_id'   => '',
				'payed_order_list'        => array(),
				'product_id'              => '',
				'variation_id'            => '',
				'variation'               => '',
				'product_name'            => '',
				'quantity'                => '',
				'line_subtotal'           => '',
				'line_total'              => '',
				'line_subtotal_tax'       => '',
				'line_tax'                => '',
				'line_tax_data'           => '',
				'cart_discount'           => '',
				'cart_discount_tax'       => '',
				'coupons'                 => '',
				'order_total'             => '',
				'order_subtotal'          => '',
				'order_tax'               => '',
				'order_discount'          => '',
				'order_shipping'          => '',
				'order_shipping_tax'      => '',
				'order_currency'          => '',
				'renew_order'             => 0,
				'prices_include_tax'      => '',
				'payment_method'          => '',
				'payment_method_title'    => '',
				'transaction_id'          => '',
				'subscriptions_shippings' => '',
				'subscription_total'      => '',
				'price_is_per'            => '',
				'price_time_option'       => '',
				'max_length'              => '',
				'trial_per'               => '',
				'trial_time_option'       => '',
				'fee'                     => '',
				'num_of_rates'            => '',
				'rates_payed'             => '',
				'order_ids'               => array(),
				'order_id'                => '',
				'order_item_id'           => '',
				'user_id'                 => 0,
			);
		}

		/*
		|--------------------------------------------------------------------------
		| Magic Methods
		|--------------------------------------------------------------------------
		*/

		/**
		 * Magic Method __get function.
		 *
		 * @param string $key Key.
		 *
		 * @return mixed
		 */
		public function __get( $key ) {

			if ( ! isset( $this->array_prop[ $key ] ) ) {
				$this->array_prop[ $key ] = get_post_meta( $this->id, $key, true );
			}

			return $this->array_prop[ $key ];
		}

		/**
		 * Magic Method isset.
		 *
		 * @param string $key Key.
		 *
		 * @return bool
		 */
		public function __isset( $key ) {
			if ( ! $this->id ) {
				return false;
			}

			return metadata_exists( 'post', $this->id, $key );
		}

		/*
		|--------------------------------------------------------------------------
		| General Getters as Setters
		|--------------------------------------------------------------------------
		*/
		/**
		 * Get function.
		 *
		 * @param string $prop Property name.
		 * @param string $context Change this string if you want the value stored in database.
		 *
		 * @return mixed
		 */
		public function get( $prop, $context = 'view' ) {

			$value = $this->$prop;
			if ( 'view' === $context ) {
				// APPLY_FILTER : ywsbs_subscription_{$key}: filtering the post meta of a subscription.
				$value = apply_filters( 'ywsbs_subscription_' . $prop, $value, $this );
			}

			return $value;
		}

		/**
		 * Set function.
		 *
		 * @param string $prop Property name.
		 * @param mixed  $value Value of property.
		 *
		 * @return bool|int
		 */
		public function set( $prop, $value ) {
			$old_value                 = $this->$prop;
			$this->$prop               = $value;
			$this->array_prop[ $prop ] = $value;

			$return = update_post_meta( $this->id, $prop, $value );

			if ( $return ) {
				do_action( 'ywsbs_updated_prop', $this, $prop, $value, $old_value );
			}
			return $return;
		}

		/**
		 * Unset function.
		 *
		 * @param string $prop Property name.
		 *
		 * @return void
		 */
		public function unset_prop( $prop ) {
			unset( $this->$prop );
			unset( $this->array_prop[ $prop ] );
			delete_post_meta( $this->id, $prop );

			do_action( 'ywsbs_unset_prop', $this, $prop );
		}

		/*
		|--------------------------------------------------------------------------
		| Getters
		|--------------------------------------------------------------------------
		*/

		/**
		 * Returns the unique ID for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return int
		 * @since  1.7.2
		 */
		public function get_id( $context = 'view' ) {
			return (int) $this->get( 'id', $context );
		}

		/**
		 * Returns the post object for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return WP_Post
		 * @since  2.3.0
		 */
		public function get_post( $context = 'view' ) {
			return $this->get( 'post', $context );
		}


		/**
		 * Returns the number for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return int
		 * @since  2.2
		 */
		public function get_number( $context = 'view' ) {
			return apply_filters( 'ywsbs_get_number', '#' . $this->get_id(), $this );
		}


		/**
		 * Returns the status for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return string
		 * @since  2.0.0
		 */
		public function get_status( $context = 'view' ) {
			return $this->get( 'status', $context );
		}

		/**
		 * Returns the product name for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return string
		 * @since  2.0.0
		 */
		public function get_product_name( $context = 'view' ) {
			return $this->get( 'product_name', $context );
		}

		/**
		 * Returns the start date for this object in timestamp format
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return int
		 * @since  2.0.0
		 */
		public function get_start_date( $context = 'view' ) {
			return (int) $this->get( 'start_date', $context );
		}

		/**
		 * Returns the payment due date for this object in timestamp format
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return int
		 * @since  2.0.0
		 */
		public function get_payment_due_date( $context = 'view' ) {
			return (int) $this->get( 'payment_due_date', $context );
		}

		/**
		 * Returns the expired pause date for this object in timestamp format
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return int
		 * @since  2.0.0
		 */
		public function get_expired_pause_date( $context = 'view' ) {
			return (int) $this->get( 'expired_pause_date', $context );
		}

		/**
		 * Returns the expired date for this object in timestamp format
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return int
		 * @since  2.0.0
		 */
		public function get_expired_date( $context = 'view' ) {
			return (int) $this->get( 'expired_date', $context );
		}


		/**
		 * Returns the cancelled date for this object in timestamp format
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return int
		 * @since  2.4.0
		 */
		public function get_cancelled_date( $context = 'view' ) {
			return (int) $this->get( 'cancelled_date', $context );
		}

		/**
		 * Returns the end date for this object in timestamp format
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return int
		 * @since  2.0.0
		 */
		public function get_end_date( $context = 'view' ) {
			return (int) $this->get( 'end_date', $context );
		}

		/**
		 * Returns the payment method title for this object
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return string
		 * @since  2.0.0
		 */
		public function get_payment_method_title( $context = 'view' ) {
			return $this->get( 'payment_method_title', $context );
		}

		/**
		 * Returns the line_subtotal_tax for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return float
		 * @since  2.0.0
		 */
		public function get_line_subtotal_tax( $context = 'view' ) {
			return (float) $this->get( 'line_subtotal_tax', $context );
		}

		/**
		 * Returns the line_subtotal_tax for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return float
		 * @since  2.0.0
		 */
		public function get_line_subtotal( $context = 'view' ) {
			return (float) $this->get( 'line_subtotal', $context );
		}

		/**
		 * Returns the line_total for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return float
		 * @since  2.0.0
		 */
		public function get_line_total( $context = 'view' ) {
			return (float) $this->get( 'line_total', $context );
		}

		/**
		 * Returns the line_tax for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return float
		 * @since  2.0.0
		 */
		public function get_line_tax( $context = 'view' ) {
			return (float) $this->get( 'line_tax', $context );
		}

		/**
		 * Returns the cart_discount for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return float
		 * @since  2.0.0
		 */
		public function get_cart_discount( $context = 'view' ) {
			return (float) $this->get( 'cart_discount', $context );
		}


		/**
		 * Returns the cart_discount_tax for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return float
		 * @since  2.0.0
		 */
		public function get_cart_discount_tax( $context = 'view' ) {
			return (float) $this->get( 'cart_discount_tax', $context );
		}

		/**
		 * Returns the line_tax_data for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return array
		 * @since  2.0.0
		 */
		public function get_line_tax_data( $context = 'view' ) {
			$line_tax_data = $this->get( 'line_tax_data', $context );
			return empty( $line_tax_data ) ? array() : (array) $line_tax_data;
		}

		/**
		 * Returns the order currency for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return string
		 * @since  2.0.0
		 */
		public function get_order_currency( $context = 'view' ) {
			return $this->get( 'order_currency', $context );
		}

		/**
		 * Returns the user id for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return int
		 * @since  1.7.2
		 */
		public function get_user_id( $context = 'view' ) {
			return (int) $this->get( 'user_id', $context );
		}

		/**
		 * Returns the num of pauses for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return int
		 * @since  1.7.2
		 */
		public function get_num_of_pauses( $context = 'view' ) {
			return (int) $this->get( 'num_of_pauses', $context );
		}

		/**
		 * Returns the max length for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return int
		 * @since  2.0.0
		 */
		public function get_max_length( $context = 'view' ) {
			return (int) $this->get( 'max_length', $context );
		}

		/**
		 * Returns the price time option for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return string
		 * @since  2.0.0
		 */
		public function get_price_time_option( $context = 'view' ) {
			return $this->get( 'price_time_option', $context );
		}

		/**
		 * Returns the price_is_per option for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return int
		 * @since  2.0.0
		 */
		public function get_price_is_per( $context = 'view' ) {
			return (int) $this->get( 'price_is_per', $context );
		}

		/**
		 * Returns the product id for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return int
		 * @since  2.0.0
		 */
		public function get_product_id( $context = 'view' ) {
			return (int) $this->get( 'product_id', $context );
		}

		/**
		 * Returns the variation id for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return int
		 * @since  2.0.0
		 */
		public function get_variation_id( $context = 'view' ) {
			return (int) $this->get( 'variation_id', $context );
		}

		/**
		 * Returns the variations for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return array
		 * @since  2.0.0
		 */
		public function get_variation( $context = 'view' ) {
			$variation = $this->get( 'variation', $context );
			return empty( $variation ) ? array() : (array) $variation;
		}

		/**
		 * Returns the renew order id for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return int
		 * @since  2.0.0
		 */
		public function get_renew_order_id( $context = 'view' ) {
			return (int) $this->get( 'renew_order', $context );
		}

		/**
		 * Returns the renew_order object for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return WC_Order
		 * @since  2.0.0
		 */
		public function get_renew_order( $context = 'view' ) {
			$renew_order = wc_get_order( $this->get_renew_order_id() );
			return $renew_order;
		}

		/**
		 * Returns the subscriptions shippings for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return array
		 * @since  2.0.0
		 */
		public function get_subscriptions_shippings( $context = 'view' ) {
			$subscriptions_shippings = $this->get( 'subscriptions_shippings', $context );
			return empty( $subscriptions_shippings ) ? array() : (array) $subscriptions_shippings;
		}

		/**
		 * Returns the quantity for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return int
		 * @since  2.0.0
		 */
		public function get_quantity( $context = 'view' ) {
			return (int) $this->get( 'quantity', $context );
		}

		/**
		 * Returns the num_of_rates for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return int
		 * @since  2.0.0
		 */
		public function get_num_of_rates( $context = 'view' ) {
			return (int) $this->get( 'num_of_rates', $context );
		}

		/**
		 * Returns the rates_payed for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return int
		 * @since  2.0.0
		 */
		public function get_paid_rates( $context = 'view' ) {
			return (int) $this->get( 'rates_payed', $context );
		}

		/**
		 * Returns the payed_order_list for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return array
		 * @since  2.0.0
		 */
		public function get_paid_order_list( $context = 'view' ) {
			$paid_order_list = $this->get( 'payed_order_list', $context );
			return empty( $paid_order_list ) ? array() : (array) $paid_order_list;
		}


		/**
		 * Returns the order_shipping for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return float
		 * @since  2.0.0
		 */
		public function get_order_shipping( $context = 'view' ) {
			return (float) $this->get( 'order_shipping', $context );
		}

		/**
		 * Returns the order_shipping_tax for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return float
		 * @since  2.0.0
		 */
		public function get_order_shipping_tax( $context = 'view' ) {
			return (float) $this->get( 'order_shipping_tax', $context );
		}

		/**
		 * Returns the order_shipping_tax for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return float
		 * @since  2.0.0
		 */
		public function get_order_tax( $context = 'view' ) {
			return (float) $this->get( 'order_tax', $context );
		}

		/**
		 * Returns the subscription_total for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return float
		 * @since  2.0.0
		 */
		public function get_subscription_total( $context = 'view' ) {
			return (float) $this->get( 'subscription_total', $context );
		}


		/**
		 * Returns the subscription_total for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return float
		 * @since  2.4.0
		 */
		public function get_order_total( $context = 'view' ) {
			return (float) $this->get( 'order_total', $context );
		}

		/**
		 * Returns the subscription_total for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return float
		 * @since  2.4.0
		 */
		public function get_order_subtotal( $context = 'view' ) {
			return (float) $this->get( 'order_subtotal', $context );
		}


		/**
		 * Returns the fee for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return float
		 * @since  2.0.0
		 */
		public function get_fee( $context = 'view' ) {
			return (float) $this->get( 'fee', $context );
		}

		/**
		 * Returns the order_id for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return int
		 * @since  2.0.0
		 */
		public function get_order_id( $context = 'view' ) {
			return (int) $this->get( 'order_id', $context );
		}


		/**
		 * Returns the order_id for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return int
		 * @since  2.3.0
		 */
		public function get_order_item_id( $context = 'view' ) {
			return (int) $this->get( 'order_item_id', $context );
		}

		/**
		 * Returns the order_ids for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return array
		 * @since  2.0.0
		 */
		public function get_order_ids( $context = 'view' ) {
			$order_ids = $this->get( 'order_ids', $context );
			return empty( $order_ids ) ? array() : (array) $order_ids;
		}

		/**
		 * Get the order object.
		 *
		 * @return WC_Order
		 */
		public function get_order() {
			$this->order = ! is_null( $this->order ) ? $this->order : wc_get_order( $this->get( 'order_id' ) );
			return $this->order;
		}

		/**
		 * Get the product object.
		 *
		 * @return WC_Product
		 */
		public function get_product() {
			if ( is_null( $this->product ) ) {
				$variation_id  = $this->get( 'variation_id' );
				$this->product = wc_get_product( ( isset( $variation_id ) && ! empty( $variation_id ) ) ? $variation_id : $this->get( 'product_id' ) );
			}

			return $this->product;
		}


		/**
		 * Returns the unique ID for this object.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return int
		 * @since  1.7.2
		 */
		public function get_created_via( $context = 'view' ) {
			$created_via = $this->get( 'created_via', $context );
			return empty( $created_via ) ? 'checkout' : $created_via;
		}

		/**
		 * Get method of payment.
		 *
		 * @return mixed|string
		 */
		public function get_payment_method() {
			return apply_filters( 'ywsbs_get_payment_method', $this->get( 'payment_method', 'edit' ), $this );
		}

		/**
		 * Get the conversion date of a subscription.
		 *
		 * @return int|mixed|string
		 */
		public function get_conversion_date() {
			$conversion_date = '';
			if ( (int) $this->get( 'trial_per' ) > 0 && $this->get_status() !== 'trial' ) {
				$conversion_date = $this->get( 'conversion_date' );
				if ( empty( $conversion_date ) ) {
					$orders = $this->get_paid_order_list();
					if ( count( $orders ) > 1 ) {
						$first_renew_order = wc_get_order( $orders[1] );
						if ( $first_renew_order ) {
							$conversion_date = $first_renew_order->get_date_created()->getTimestamp();
							$this->set( 'conversion_date', $conversion_date );
						}
					}
				}
			}

			return $conversion_date;
		}

		/**
		 * Get last billing date
		 *
		 * @return WC_DateTime|string object if the date is set or empty string if there is no date.
		 */
		public function get_last_billing_date() {
			$paid_order_list = $this->get_paid_order_list();
			$paid_date       = '';

			if ( ! $paid_order_list ) {
				return $paid_date;
			}

			$paid_order_list = array_reverse( $paid_order_list );

			foreach ( $paid_order_list as $order_id ) {
				$order = wc_get_order( $order_id );
				if ( $order instanceof WC_Order ) {
					$paid_date = $order->get_date_paid();
					if ( ! is_null( $paid_date ) ) {
						break;
					}
				}
			}

			return $paid_date;
		}

		/**
		 * Get subscription customer billing or shipping fields.
		 *
		 * @param string  $type Type of information.
		 * @param boolean $no_type No type.
		 * @param string  $prefix Prefix.
		 *
		 * @return array
		 * @throws Exception Return error.
		 */
		public function get_address_fields( $type = 'billing', $no_type = false, $prefix = '' ) {

			$indentation = '--------';
			$message     = $indentation . 'Check for ' . $type;

			$fields         = array();
			$value_to_check = $this->get( '_' . $type . '_first_name' );
			$order          = $this->get_order();

			if ( apply_filters( 'yith_subscription_get_address_by_order', false ) || empty( $value_to_check ) ) {
				$fields = $this->get_address_fields_from_order( $type, $no_type, $prefix );
			} else {

				if ( $order instanceof WC_Order ) {
					$meta_fields = $order->get_address( $type );

					foreach ( $meta_fields as $key => $value ) {
						$field_key = $no_type ? $key : $type . '_' . $key;

						$fields[ $prefix . $field_key ] = $this->get( '_' . $type . '_' . $key );
						$message                        = $indentation . $indentation . $fields[ $prefix . $field_key ] . ' ' . $this->get( $field_key );
						yith_subscription_log( $message, 'subscription_payment' );
					}
				}
			}

			return apply_filters( 'yith_ywsbs_billing_fields', $fields, $order, $this );
		}

		/**
		 * Get billing customer first name
		 *
		 * @return string
		 */
		public function get_billing_first_name() {

			$billing_fields     = $this->get_address_fields( 'billing' );
			$billing_first_name = isset( $billing_fields['billing_first_name'] ) ? $billing_fields['billing_first_name'] : '';

			return apply_filters( 'ywsbs_customer_billing_first_name', $billing_first_name, $this );
		}

		/**
		 * Get billing customer last name
		 *
		 * @return string
		 * @since 2.0.4
		 */
		public function get_billing_last_name() {

			$billing_fields    = $this->get_address_fields( 'billing' );
			$billing_last_name = isset( $billing_fields['billing_last_name'] ) ? $billing_fields['billing_last_name'] : '';

			return apply_filters( 'ywsbs_customer_billing_last_name', $billing_last_name, $this );
		}

		/**
		 * Get billing customer email
		 *
		 * @return string
		 */
		public function get_billing_email() {
			$billing_email = $this->get( 'billing_email' );
			if ( empty( $billing_email ) ) {
				$order = $this->get_order();
				if ( $order ) {
					$billing_email = $order->get_billing_email();
				}
			}

			return apply_filters( 'ywsbs_customer_billing_email', $billing_email, $this );
		}

		/**
		 * Get billing customer phone
		 *
		 * @return string
		 */
		public function get_billing_phone() {
			$billing_phone = $this->get( 'billing_phone' );
			if ( empty( $billing_phone ) ) {
				$order         = $this->get_order();
				$billing_phone = $order ? $order->get_billing_phone() : '';
			}

			return apply_filters( 'ywsbs_customer_billing_phone', $billing_phone, $this );
		}

		/**
		 * Return the fields billing or shipping from the parent order
		 *
		 * @param string $type Type of information.
		 * @param bool   $no_type No type.
		 * @param string $prefix Prefix.
		 *
		 * @return array
		 */
		public function get_address_fields_from_order( $type = 'billing', $no_type = false, $prefix = '' ) {
			$fields = array();
			$order  = $this->get_order();

			if ( $order ) {
				$meta_fields = $order->get_address( $type );

				if ( is_array( $meta_fields ) ) {
					foreach ( $meta_fields as $key => $value ) {
						$field_key                      = $no_type ? $key : $type . '_' . $key;
						$fields[ $prefix . $field_key ] = $value;
					}
				}
			}

			return $fields;
		}

		/**
		 * Return the customer order note of subscription or parent order.
		 *
		 * @return mixed
		 * @since  1.4.0
		 */
		public function get_customer_order_note() {
			$customer_note = $this->get( 'customer_note' );
			if ( empty( $customer_note ) ) {
				$order         = $this->get_order();
				$customer_note = $order ? $order->get_customer_note() : '';
			}

			return $customer_note;
		}

		/**
		 * Return the next payment due date if there are rates not payed.
		 *
		 * @param int $trial_period Trial period.
		 * @param int $start_date Start date.
		 *
		 * @return bool|int
		 * @since  1.0.0
		 */
		public function get_next_payment_due_date( $trial_period = 0, $start_date = 0 ) {

			$timestamp  = 0;
			$start_date = ( $start_date ) ? $start_date : current_time( 'timestamp' ); // phpcs:ignore

			$paid_rates   = $this->get_paid_rates();
			$num_of_rates = $this->get_num_of_rates();

			if ( 0 === $num_of_rates || ( $num_of_rates - $paid_rates ) > 0 ) {
				$payment_due_date = ( $this->get( 'payment_due_date' ) === '' ) ? $start_date : $this->get( 'payment_due_date' );
				if ( 0 !== $trial_period ) {
					$timestamp = $start_date + $trial_period;
				} else {
					$timestamp = ywsbs_get_timestamp_from_option( $payment_due_date, $this->get( 'price_is_per' ), $this->get( 'price_time_option' ) );
				}
			}

			return $timestamp;
		}

		/**
		 * Return the Monthly Recurring Revenue of a Subscription.
		 *
		 * @return float
		 */
		public function get_mrr() {
			$mrr            = 0;
			$daily_amount   = 0;
			$day_left       = false;
			$exclude_status = apply_filters( 'ywsbs_exclude_status_from_mrr_calculation', array( 'paused', 'cancelled', 'trial', 'pending', 'expired' ) );
			if ( in_array( $this->get_status(), $exclude_status, true ) ) {
				return $mrr;
			}

			$expired_date = $this->get_expired_date();
			if ( ! empty( $expired_date ) && $expired_date > time() ) {
				$day_left = (int) ( ( $expired_date - time() ) / DAY_IN_SECONDS );
			}

			$price = $this->get_subscription_total();
			if ( $price > 0 ) {
				$price_per         = $this->get_price_is_per();
				$price_time_option = $this->get_price_time_option();
				$daily_amount      = ywsbs_calculate_daily_amount( $price_per, $price_time_option, $price );
			}

			$day_left = ( ! $day_left || $day_left >= 30 ) ? 30 : $day_left;

			return wc_format_decimal( $day_left * $daily_amount );
		}


		/**
		 * Return the Annual Recurring Revenue of a Subscription.
		 *
		 * @return float
		 */
		public function get_arr() {
			$arr            = 0;
			$daily_amount   = 0;
			$day_left       = false;
			$exclude_status = apply_filters( 'ywsbs_exclude_status_from_arr_calculation', array( 'paused', 'cancelled', 'trial', 'pending', 'expired' ) );

			if ( in_array( $this->get_status(), $exclude_status, true ) ) {
				return $arr;
			}

			$price = $this->get_subscription_total();
			if ( $price > 0 ) {
				$price_per         = $this->get_price_is_per();
				$price_time_option = $this->get_price_time_option();
				$daily_amount      = ywsbs_calculate_daily_amount( $price_per, $price_time_option, $price );
			}

			$expired_date = $this->get_expired_date();
			if ( ! empty( $expired_date ) && $expired_date > time() ) {
				$day_left = (int) ( ( $expired_date - time() ) / DAY_IN_SECONDS );
			}

			$day_left = ( ! $day_left || $day_left >= 365 ) ? 365 : $day_left;

			return wc_format_decimal( $day_left * $daily_amount );
		}

		/**
		 * Return the date (timestamp) until that the subscription was paid.
		 *
		 * @return int
		 */
		public function get_confirmed_valid_date() {

			$valid_date = 0;

			if ( ! empty( $this->get_payment_due_date() ) ) {
				$valid_date = $this->get_payment_due_date();
			} elseif ( ! empty( $this->get_expired_date() ) ) {
				$valid_date = $this->get_expired_date();
			} elseif ( ! empty( $this->get_end_date() ) ) {
				$valid_date = $this->get_end_date();
			}

			$valid_date = $valid_date < time() ? 0 : $valid_date;

			return apply_filters( 'ywsbs_confirmed_valid_date', $valid_date, $this );
		}

		/**
		 * Return the daily cost of a subscription.
		 *
		 * @return float|int
		 */
		public function get_daily_amount() {
			$_ywsbs_price_is_per      = $this->get_price_is_per();
			$_ywsbs_price_time_option = $this->get_price_time_option();
			$_price                   = (float) $this->get( 'line_subtotal' );

			return ywsbs_calculate_daily_amount( $_ywsbs_price_is_per, $_ywsbs_price_time_option, $_price );
		}

		/**
		 * Return the total number of days of a subscription
		 *
		 * @return int
		 */
		public function get_total_period_in_days() {
			return ( $this->get_price_is_per() * ywsbs_get_period_in_seconds( $this->get_price_time_option() ) ) / DAY_IN_SECONDS;
		}
		/*
		|--------------------------------------------------------------------------
		| Utility Methods
		|--------------------------------------------------------------------------
		*/

		/**
		 * Add new subscription.
		 *
		 * @param array $args List of args.
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function add_subscription( $args ) {

			$params = array(
				'post_status' => 'publish',
				'post_type'   => YITH_YWSBS_POST_TYPE,
			);

			$subscription_id = wp_insert_post( $params );

			if ( $subscription_id ) {
				$this->set( 'id', $subscription_id );
				// APPLY_FILTER: ywsbs_add_subscription_args : to filter the meta data of a subscription before the creation.
				$meta = apply_filters( 'ywsbs_add_subscription_args', ( wp_parse_args( $args, array_filter( $this->get_default_meta_data() ) ) ), $this );
				$this->update_subscription_meta( $meta );

				YITH_WC_Activity()->add_activity( $subscription_id, 'new', 'success', $this->get( 'order_id' ), __( 'Subscription successfully created.', 'yith-woocommerce-subscription' ) );
			}
		}

		/**
		 * Update post meta in subscription
		 *
		 * @param array $meta Array of meta.
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function update_subscription_meta( $meta ) {
			foreach ( $meta as $key => $value ) {
				$this->set( $key, $value );
			}
		}

		/**
		 * Updates status of subscription
		 *
		 * @param string $new_status Status to change.
		 * @param string $from Who make the change.
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function update_status( $new_status, $from = '' ) {

			if ( ! $this->id ) {
				return false;
			}

			$old_status     = $this->get( 'status' );
			$from_list      = ywsbs_get_from_list();
			$status_updated = false;

			if ( $new_status !== $old_status || ! in_array( $new_status, array_keys( ywsbs_get_status() ), true ) ) {

				$from_text = ( '' !== $from && isset( $from_list[ $from ] ) ) ? esc_html_x( 'By ', 'Followed by who requested the subscrition status change', 'yith-woocommerce-subscription' ) . $from_list[ $from ] : '';

				switch ( $new_status ) {
					case 'active':
						// reset some custom data.
						$this->set( 'expired_pause_date', '' );
						// Check if subscription is cancelled. Es. for echeck payments.
						if ( 'cancelled' === $old_status ) {
							if ( 'administrator' === $from ) {
								$this->set( 'status', $new_status );
								do_action( 'ywsbs_customer_subscription_actived_mail', $this );
								// translators: %s: Who set the new status request.
								YITH_WC_Activity()->add_activity( $this->id, 'activated', 'success', $this->get( 'order_id' ), sprintf( esc_html_x( 'Subscription is now active. %s ', '%s: Who set the new status request', 'yith-woocommerce-subscription' ), $from_text ) );

								$this->set( 'payment_due_date', $this->get( 'end_date' ) );
								$this->set( 'end_date', '' );
								$this->set( 'cancelled_date', '' );
							} else {
								$this->set( 'end_date', $this->get( 'payment_due_date' ) );
								$this->set( 'payment_due_date', '' );
								do_action( 'ywsbs_no_activated_just_cancelled', $this );

								return false;
							}
						} else {
							$this->set( 'status', $new_status );
							do_action( 'ywsbs_customer_subscription_actived_mail', $this );
							// translators: %s: Who set the new status request.
							YITH_WC_Activity()->add_activity( $this->id, 'activated', 'success', $this->get( 'order_id' ), sprintf( esc_html_x( 'Subscription is now active. %s', '%s: Who set the new status request', 'yith-woocommerce-subscription' ), $from_text ) );
						}

						break;

					case 'paused':
						$pause_options = YWSBS_Subscription_Helper()->get_subscription_product_pause_options( $this );

						// add the date of pause.
						$date_of_pauses   = (array) $this->get( 'date_of_pauses' );
						$date_of_pauses[] = current_time( 'timestamp' ); // phpcs:ignore
						$this->set( 'date_of_pauses', array_filter( $date_of_pauses ) );

						// increase the num of pauses done.
						$this->set( 'num_of_pauses', $this->get_num_of_pauses() + 1 );

						// expired_pause_date.
						if ( $pause_options['max_pause_duration'] > 0 ) {
							$this->set( 'expired_pause_date', current_time( 'timestamp' ) + $pause_options['max_pause_duration'] * 86400 ); // phpcs:ignore
						}

						// Update the subscription status.
						$this->set( 'status', $new_status );
						do_action( 'ywsbs_customer_subscription_paused_mail', $this );
						// translators: %s: Who set the new status request.
						YITH_WC_Activity()->add_activity( $this->id, 'paused', 'success', $this->get( 'order_id' ), sprintf( esc_html_x( 'Subscription paused. %s', '%s: Who set the new status request', 'yith-woocommerce-subscription' ), $from_text ) );

						break;
					case 'resume':
						$this->set( 'expired_pause_date', '' );

						// change payment_due_date.
						$offset           = YWSBS_Subscription_Helper()->get_payment_due_date_paused_offset( $this );
						$payment_due_date = $this->get_payment_due_date() + $offset;
						$product          = $this->get_product();

						if ( YWSBS_Subscription_Synchronization()->is_synchronizable( $product ) ) {
							$payment_due_date = YWSBS_Subscription_Synchronization()->get_next_payment_due_date_sync( $payment_due_date, $product );
						}

						$this->set( 'sum_of_pauses', (int) $this->get( 'sum_of_pauses' ) + $offset );
						$this->set( 'payment_due_date', $payment_due_date );

						if ( $this->get( 'expired_date' ) ) {
							// shift expiry date.
							$this->set( 'expired_date', $this->get_expired_date() + $offset );
						}

						// Update the subscription status.
						$this->set( 'status', 'active' );
						do_action( 'ywsbs_customer_subscription_resumed_mail', $this );
						// translators: $1: Date of next payment. $2: Who set the new status request.
						YITH_WC_Activity()->add_activity( $this->id, 'resumed', 'success', $this->get( 'order_id' ), sprintf( esc_html_x( 'Subscription resumed. Payment due on %1$s. %2$s.', '$1: Date of next payment. $2: Who set the new status request', 'yith-woocommerce-subscription' ), date_i18n( wc_date_format(), $payment_due_date ), $from_text ) );

						break;

					case 'overdue':
						// Update the subscription status.
						$this->set( 'status', $new_status );
						do_action( 'ywsbs_customer_subscription_request_payment_mail', $this );
						YITH_WC_Activity()->add_activity( $this->id, 'overdue', 'success', $this->get( 'order_id' ), esc_html__( 'Overdue subscription.', 'yith-woocommerce-subscription' ) );
						break;

					case 'trial':
						if ( 'cancelled' === $old_status ) {
							$this->set( 'end_date', $this->get( 'payment_due_date' ) );
							$this->set( 'payment_due_date', '' );
							do_action( 'ywsbs_no_activated_just_cancelled', $this );

							return false;
						} else {
							$time_option_string = ywsbs_get_time_options();
							$this->set( 'status', $new_status );
							YITH_WC_Activity()->add_activity( $this->id, 'trial', 'success', $this->get( 'order_id' ), esc_html_x( 'Started a trial period of ', '$1: Integer Number ; $2: Time of period ( days, months.. )', 'yith-woocommerce-subscription' ) . ' ' . sprintf( ' %s %s', $this->get( 'trial_per' ), $time_option_string[ $this->get( 'trial_time_option' ) ] ) );
						}
						break;

					case 'cancelled':
						// if the subscription is cancelled the payment_due_date become the expired_date.
						// the subscription will be active until the date of the next payment.
						$this->set( 'end_date', $this->get_payment_due_date() );
						$this->set( 'payment_due_date', '' );
						$this->set( 'cancelled_date', current_time( 'timestamp' ) ); // phpcs:ignore
						$this->set( 'status', $new_status );
						$this->set( 'cancelled_by', $from );
						$this->cancel_renew_order();
						do_action( 'ywsbs_customer_subscription_cancelled_mail', $this );
						// translators: '%s: Who set the new status request.
						YITH_WC_Activity()->add_activity( $this->id, 'cancelled', 'success', $this->get( 'order_id' ), sprintf( esc_html_x( 'The subscription has been cancelled. %s', '%s: Who set the new status request', 'yith-woocommerce-subscription' ), $from_text ) );
						break;
					case 'cancel-now':
						// if the subscription is cancelled now the end_date is the current timestamp.
						$new_status = 'cancelled';
						$tstamp     = current_time( 'timestamp' ); // phpcs:ignore
						$this->set( 'end_date', $tstamp );
						$this->set( 'payment_due_date', '' );
						$this->set( 'cancelled_date', $tstamp );
						$this->set( 'status', $new_status );
						$this->set( 'cancelled_by', $from );
						$this->cancel_renew_order();
						do_action( 'ywsbs_customer_subscription_cancelled_mail', $this );
						// translators: %s: Who set the new status request.
						YITH_WC_Activity()->add_activity( $this->id, 'cancelled', 'success', $this->get( 'order_id' ), sprintf( esc_html_x( 'The subscription has been NOW cancelled. %s', '%s: Who set the new status request', 'yith-woocommerce-subscription' ), $from_text ) );
						break;
					case 'expired':
						$this->set( 'status', $new_status );
						$this->cancel_renew_order();
						do_action( 'ywsbs_customer_subscription_expired_mail', $this );
						YITH_WC_Activity()->add_activity( $this->id, 'expired', 'success', $this->get( 'order_id' ), esc_html__( 'Subscription expired.', 'yith-woocommerce-subscription' ) );
						break;
					case 'suspended':
						$this->set( 'status', $new_status );

						do_action( 'ywsbs_customer_subscription_suspended_mail', $this );
						YITH_WC_Activity()->add_activity( $this->id, 'suspended', 'success', $this->get( 'order_id' ), esc_html__( 'Subscription suspended.', 'yith-woocommerce-subscription' ) );
						break;
					default:
				}

				// Status was changed.
				do_action( 'ywsbs_subscription_status_' . $new_status, $this->id );
				do_action( 'ywsbs_subscription_status_' . $old_status . '_to_' . $new_status, $this->id );
				do_action( 'ywsbs_subscription_status_changed', $this->id, $old_status, $new_status );
				do_action( 'ywsbs_subscription_admin_mail', $this );

				$status_updated = true;
			}

			return $status_updated;
		}

		/**
		 * Schedule actions for this subscription.
		 */
		public function schedule_actions() {

			$date_to_change = YWSBS_Subscription_Scheduler::get_date_changes_to_schedule();

			if ( $date_to_change ) {
				foreach ( $date_to_change as $date_key ) {
					do_action( 'ywsbs_updated_subscription_date', $this, $date_key, $this->get( $date_key ), $this->get( $date_key ) );
				}
			}

			$this->set( 'ywsbs_version', YITH_YWSBS_VERSION );

		}

		/**
		 * Reset the data saved on subscription object.
		 */
		public function empty_cache() {
			$this->array_prop = array();
		}

		/**
		 * Change the status of renew order if exists
		 *
		 * @param string $note Order note.
		 */
		public function cancel_renew_order( $note = '' ) {
			$renew_order = $this->get( 'renew_order' );
			if ( $renew_order ) {
				$order = wc_get_order( $renew_order );
				if ( $order ) {
					$order->update_status( 'cancelled' );
					if ( ! empty( $note ) ) {
						$order->add_order_note( $note );
					}
				}
			}
		}

		/**
		 * Calculate subscription total from other total meta data.
		 *
		 * @return void
		 * @since  1.4.5
		 */
		public function recalculate_prices() {
			YWSBS_Subscription_Helper()->calculate_taxes( $this );
			YWSBS_Subscription_Helper()->calculate_totals_from_changes( $this );
		}

		/**
		 * Register a failed attempt on the parent order of a subscription.
		 *
		 * @param bool          $attempts Attempts.
		 * @param bool          $latest_attempt If is the last attempt doesn't send email.
		 * @param string        $next_attempt_date Next attempt date.
		 * @param WC_Order|null $order Order.
		 *
		 * @since 1.1.3
		 */
		public function register_failed_attempt( $attempts = false, $latest_attempt = false, $next_attempt_date = '', $order = null ) {

			if ( ! $order ) {
				$order = $this->get_order();
			}

			if ( false === $attempts ) {
				$failed_attempt = $order->get_meta( 'failed_attemps' );
				$attempts       = intval( $failed_attempt ) + 1;
			}

			if ( ! $latest_attempt ) {
				// translators:%d: Order id.
				YITH_WC_Activity()->add_activity( $this->id, 'failed-payment', 'success', $order->get_id(), sprintf( esc_html_x( 'Failed payment for order %d', '%d: Order id', 'yith-woocommerce-subscription' ), $order->get_id() ) );
				$order->update_meta_data( 'failed_attemps', $attempts );

				// DO_ACTION : ywsbs_customer_subscription_payment_failed_mail : do action when the subscription is failed.
				do_action( 'ywsbs_customer_subscription_payment_failed_mail', $this );
			}

			if ( ! empty( $next_attempt_date ) ) {
				$order->update_meta_data( 'next_payment_attempt', $next_attempt_date );
			}

			$this->set( 'next_attempt_date', $next_attempt_date );
			$this->set( 'check_the_renew_order', 0 );
			$this->set( 'check_the_renew_order_id', 0 );

			$order->save();

			$suspend_subscription = apply_filters( 'ywsbs_suspend_for_failed_recurring_payment', get_option( 'ywsbs_suspend_for_failed_recurring_payment', 'no' ) );

			// Suspend the subscription if is activated.
			if ( 'yes' === $suspend_subscription ) {
				if ( ! $this->has_status( 'suspended' ) ) {
					$this->update_status( 'suspended', $this->get_payment_method() );
					// translators: $1: Order id, $2: Subscription id.
					yith_subscription_log( sprintf( 'Subscription suspended. Order %1$s. Subscription %2$s', $order->get_id(), $this->id ) );
				}
			}

		}

		/**
		 * Set meta to change status after that a payment failed.
		 * These changes depends on general settings options.
		 */
		public function set_status_during_the_renew() {
			$status_after_fail       = get_option( 'ywsbs_change_status_after_renew_order_creation' );
			$status_after_fail_step2 = get_option( 'ywsbs_change_status_after_renew_order_creation_step_2' );

			if ( $this->has_status( array( 'active', 'trial' ) ) ) {
				$wait       = empty( $status_after_fail['wait_for'] ) ? 48 : (int) $status_after_fail['wait_for'] * HOUR_IN_SECONDS;
				$new_status = $status_after_fail['status'];

				if ( 0 === $wait ) {
					$this->update_status( $new_status );
					if ( 'cancelled' !== $new_status ) {
						$this->set( 'next_failed_status_change_date', ( current_time( 'timestamp' ) + (int)$status_after_fail['length'] * DAY_IN_SECONDS ) ); // phpcs:ignore
						$this->set( 'next_failed_status', $status_after_fail_step2['status'] );
					} else {
						$this->set( 'next_failed_status_change_date', 0 );
						$this->unset_prop( 'next_failed_status' );
					}
				} else {
					$this->set( 'next_failed_status', $new_status );
					$this->set( 'next_failed_status_change_date', $wait + current_time( 'timestamp' ) ); // phpcs:ignore
				}
			}
		}

		/**
		 * Triggered by hook yswbw_schedule_next_failed_status_change
		 */
		public function update_failed_status() {
			$new_status = $this->get( 'next_failed_status' );
			if ( ! empty( $new_status ) ) {
				$status_after_fail       = get_option( 'ywsbs_change_status_after_renew_order_creation' );
				$status_after_fail_step2 = get_option( 'ywsbs_change_status_after_renew_order_creation_step_2' );

				if ( 'cancelled' === $new_status ) {
					$this->set( 'next_failed_status_change_date', 0 );
					$this->unset_prop( 'next_failed_status' );
				}

				if ( $status_after_fail['status'] === $new_status ) {
					$this->set( 'next_failed_status_change_date', ( current_time( 'timestamp' ) + (int)$status_after_fail['length'] * DAY_IN_SECONDS ) ); // phpcs:ignore
					$this->set( 'next_failed_status', $status_after_fail_step2['status'] );
				} else {
					$this->set( 'next_failed_status_change_date', ( current_time( 'timestamp' ) + (int)$status_after_fail_step2['length'] * DAY_IN_SECONDS ) ); // phpcs:ignore
					$this->set( 'next_failed_status', 'cancelled' );
				}

				$this->update_status( $new_status );
			}
		}

		/**
		 * Clear data to reset schedule action and unuseful meta.
		 */
		public function clear_all_failed_meta() {
			$this->set( 'next_failed_status_change_date', 0 );
			$this->set( 'next_attempt_date', 0 );
			$this->unset_prop( 'next_failed_status' );
		}

		/**
		 * Update the payment method after that the order is completed
		 *
		 * @return void
		 */
		public function update_payment_method() {
			$order = $this->get_order();
			if ( $order instanceof WC_Order ) {
				return;
			}

			$this->set( 'payment_method', $order->get_payment_method() );
			$this->set( 'payment_method_title', $order->get_payment_method_title() );
		}

		/**
		 * Update the subscription prices by admin.
		 *
		 * @param array $posted Array of fields.
		 *
		 * @since 1.4.5
		 */
		public function update_prices( $posted ) {

			$new_values = array();
			$old_values = array();

			if ( isset( $posted['ywsbs_quantity'] ) ) {
				$new_values['quantity'] = (int) wc_sanitize_textarea( $posted['ywsbs_quantity'] );
				$old_values['quantity'] = $this->get_quantity();
			}

			if ( isset( $posted['ywsbs_line_total'] ) ) {
				$new_values['line_total'] = wc_format_decimal( $posted['ywsbs_line_total'] );
				$old_values['line_total'] = $this->get_line_total();
			}

			if ( isset( $posted['ywsbs_line_tax'] ) ) {
				$new_values['line_tax'] = wc_format_decimal( $posted['ywsbs_line_tax'] );
				$old_values['line_tax'] = $this->get_line_tax();
			}

			if ( isset( $posted['ywsbs_shipping_cost_line_cost'] ) ) {
				$new_values['order_shipping'] = wc_format_decimal( $posted['ywsbs_shipping_cost_line_cost'] );
				$old_values['order_shipping'] = $this->get_order_shipping();
			}

			if ( isset( $posted['ywsbs_shipping_cost_line_tax'] ) ) {
				$new_values['order_shipping_tax'] = wc_format_decimal( $posted['ywsbs_shipping_cost_line_tax'] );
				$old_values['order_shipping_tax'] = $this->get_order_shipping_tax();
			}

			$changes = array_diff_assoc( $new_values, $old_values );

			if ( $changes ) {
				$message = '';
				foreach ( $changes as $key => $change ) {
					$currency = 'quantity' !== $key ? get_woocommerce_currency_symbol( $this->get( 'order_currency' ) ) : '';
					// translators: 1: Field changed, 2: Old value, 3: New value.
					$message .= sprintf( _x( '%1$s from %2$s to %3$s', '1: Field changed, 2: Old value, 3: New value', 'yith-woocommerce-subscription' ), str_replace( '_', ' ', $key ), $old_values[ $key ] . "{$currency}", $new_values[ $key ] . "{$currency}<br>" );
				}
				// translators: placeholder: dynamic list of changes.
				YITH_WC_Activity()->add_activity( $this->id, 'changed', $status = 'success', $order = 0, sprintf( esc_html_x( 'Changed %s ', 'placeholder: list of changes', 'yith-woocommerce-subscription' ), $message ) );
			}
			// Save the array of shipping.
			$new_values['subscriptions_shippings'] = $this->get( 'subscriptions_shippings' );

			if ( isset( $posted['ywsbs_shipping_method_name'] ) ) {
				$new_values['subscriptions_shippings']['name'] = wc_sanitize_textarea( $posted['ywsbs_shipping_method_name'] );
			}
			if ( isset( $new_values['order_shipping'] ) ) {
				$new_values['subscriptions_shippings']['cost'] = $new_values['order_shipping'];
			}

			$changes['subscriptions_shippings'] = $new_values['subscriptions_shippings'];

			if ( $changes ) {
				$this->update_subscription_meta( $changes );
			}

			YWSBS_Subscription_Helper()->calculate_totals_from_changes( $this );
		}

		/*
		|--------------------------------------------------------------------------
		| Subscription Lifecycle
		|--------------------------------------------------------------------------
		*/

		/**
		 * Start the subscription if a first payment is done
		 * order_id is the id of the first order created
		 *
		 * @param int $order_id Order ID.
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function start( $order_id ) {

			$payed = $this->get_paid_order_list();

			// do not nothing if this subscription has payed with this order.
			if ( ! empty( $payed ) && in_array( $order_id, $payed, true ) ) {
				return;
			}

			$new_status = 'active';
			$paid_rates = 1;

			if ( '' === $this->get( 'start_date' ) ) {
				$this->set( 'start_date', current_time( 'timestamp' ) ); // phpcs:ignore
			}

			$trial_period = 0;
			$trial_per    = $this->get( 'trial_per' );
			$has_trial    = ( '' !== $trial_per && $trial_per > 0 );

			// if there's a trial period shift the date of payment due.
			if ( $has_trial ) {
				$trial_period = ywsbs_get_timestamp_from_option( 0, $trial_per, $this->get( 'trial_time_option' ) );
				$paid_rates   = 0; // if there's a trial period the first payment is for signup.
				$new_status   = 'trial';
			}

			if ( $this->get( 'payment_due_date' ) === '' ) {
				$payment_due_date = apply_filters( 'ywsbs_payment_due_date_at_start', $this->get_next_payment_due_date( $trial_period, $this->get( 'start_date' ) ), $this );
				// Change the next payment_due_date.
				$this->set( 'payment_due_date', $payment_due_date );
			}

			if ( $this->get( 'expired_date' ) === '' && $this->get( 'max_length' ) !== '' ) {
				$expired_date = ywsbs_get_timestamp_from_option( current_time( 'timestamp' ), $this->get( 'max_length' ), $this->get( 'price_time_option' ) ) + $trial_period; // phpcs:ignore
				$this->set( 'expired_date', $expired_date );
			}

			if ( $this->get( 'created_via' ) === 'backend' ) {
				$order = wc_get_order( $order_id );
				$this->set( 'payment_method', $order->get_payment_method() );
				$this->set( 'payment_method_title', $order->get_payment_method_title() );
			}

			// Change the status to new status.
			$update = $this->update_status( $new_status );

			if ( $update ) {
				// translators: %s: number of subscription.
				YITH_WC_Activity()->add_activity( $this->id, 'activated', 'success', $this->get( 'order_id' ), sprintf( esc_html_x( 'Payment received for #%s', '%s: number of subscription', 'yith-woocommerce-subscription' ), $order_id ) );
			} else {
				// translators: %s: number of subscription.
				YITH_WC_Activity()->add_activity( $this->id, 'activated', 'info', $this->get( 'order_id' ), sprintf( esc_html_x( 'Payment received for #%s no status changed', '%s: number of subscription', 'yith-woocommerce-subscription' ), $order_id ) );
			}

			// correct the payment methods.
			$this->update_payment_method();

			if ( 'trial' !== $new_status ) {
				// DO_ACTION: ywsbs_customer_subscription_payment_done_mail : used to send an email to customer after the payment.
				do_action( 'ywsbs_customer_subscription_payment_done_mail', $this );
			}

			$this->set( 'rates_payed', $paid_rates );
			$this->set( 'payed_order_list', array( $order_id ) );

			// if there's an upgrade/downgrade.
			$subscription_to_cancel_info = get_post_meta( $order_id, '_ywsbs_subscritpion_to_cancel', true );

			if ( ! empty( $subscription_to_cancel_info ) ) {

				YITH_WC_Subscription()->cancel_subscription_after_upgrade( $subscription_to_cancel_info['subscription_to_cancel'] );
				update_post_meta( $subscription_to_cancel_info['subscription_to_cancel'], 'ywsbs_switched', 'yes' );

				if ( 'upgrade' === $subscription_to_cancel_info['process_type'] ) {
					delete_user_meta( $subscription_to_cancel_info['user_id'], 'ywsbs_upgrade_' . $subscription_to_cancel_info['product_id'] );
				} elseif ( 'downgrade' === $subscription_to_cancel_info['process_type'] ) {
					delete_user_meta( $subscription_to_cancel_info['user_id'], 'ywsbs_downgrade_' . $subscription_to_cancel_info['product_id'] );
				}
			}

			// DO_ACTION: ywsbs_subscription_started : trigger an action after that the subscription started.
			do_action( 'ywsbs_subscription_started', $this->id );
		}

		/**
		 * Update the subscription.
		 * Usually is called after a payment of a renew order.
		 *
		 * @param int $order_id Order ID.
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function update( $order_id ) {

			$payed = (array) $this->payed_order_list;
			$order = wc_get_order( $order_id );

			// do not nothing if this subscription has payed with this order.
			if ( ! empty( $payed ) && is_array( $payed ) && in_array( $order_id, $payed, true ) && ! $order ) {
				return;
			}

			if ( 'trial' === $this->get_status() ) {
				$this->set( 'conversion_date', time() );
			}

			// Change the status to active.
			$this->update_status( 'active' );

			// update _payed_order_list.
			$payed[] = $order_id;
			$this->set( 'payed_order_list', $payed );
			$this->set( 'rates_payed', $this->get_paid_rates() + 1 );

			// Change the next payment_due_date.

			$this->set( 'previous_payment_due_date', $this->get_payment_due_date() );
			$this->set( 'payment_due_date', $this->get_next_payment_due_date() );

			$parent_order = $this->get_order();
			// reset failed payment in order parent.
			ywsbs_reset_order_failed_attempts( $parent_order );
			// reset failed payment in renew order.
			ywsbs_reset_order_failed_attempts( $order );

			$this->clear_all_failed_meta();

			// log.
			$message = sprintf( 'Payment received for #%s. Next payment due date set.', $order_id );
			yith_subscription_log( $message, 'subscription_payment' );

			YITH_WC_Activity()->add_activity( $this->id, 'activated', 'success', $order_id, $message );

			// DO_ACTION: ywsbs_customer_subscription_payment_done_mail : it is used to send email to customer for payment done.
			do_action( 'ywsbs_customer_subscription_payment_done_mail', $this );

			// DO_ACTION: ywsbs_renew_order_payed : trigger an action after that the subscription renew order is paid.
			do_action( 'ywsbs_renew_order_payed', $this->id, $order_id );

			// reset _renew_order.
			$this->set( 'renew_order', 0 );
			$this->set( 'check_the_renew_order', 0 );

			// DO_ACTION: ywsbs_subscription_updated : trigger an action after that the subscription was updated.
			do_action( 'ywsbs_subscription_updated', $this->id );
		}

		/**
		 * Cancel the subscription
		 *
		 * @param bool $now Force the cancellation now.
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function cancel( $now = true ) {

			if ( 'cancelled' !== $this->get_status() ) {
				// Change the status to cancelled.
				$this->update_status( $now ? 'cancel-now' : 'cancelled' );
			}
			do_action( 'ywsbs_subscription_cancelled', $this->id );

			// if there's a pending order for this subscription change the status of the order to cancelled.
			// translators: placeholder subscription number.
			$note = sprintf( __( 'This order has been cancelled because subscription %s has been cancelled', 'yith-woocommerce-subscription' ), $this->get_number() );
			$this->cancel_renew_order( $note );
		}

		/**
		 * Delete the subscription
		 *
		 * @since 1.0.0
		 */
		public function delete() {

			do_action( 'ywsbs_before_subscription_deleted', $this->id );

			// Cancel the subscription before delete.
			$this->cancel();

			wp_delete_post( $this->id, true );
			do_action( 'ywsbs_subscription_deleted', $this->id );
		}

		/**
		 * Trash the subscription
		 *
		 * @since 2.0.0
		 */
		public function trash() {
			// Cancel the subscription before trash.
			$this->cancel();

			wp_trash_post( $this->id );
			do_action( 'ywsbs_subscription_trashed', $this->id );
		}


		/**
		 * Untrash the subscription
		 *
		 * @since 2.0.0
		 */
		public function untrash() {
			wp_untrash_post( $this->id );
			do_action( 'ywsbs_subscription_untrashed', $this->id );
		}

		/*
		|--------------------------------------------------------------------------
		| Permissions Methods
		|--------------------------------------------------------------------------
		*/

		/**
		 * Return if the subscription can be stopped by user
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function can_be_paused() {

			$can_be_paused = false;
			$pause_info    = YWSBS_Subscription_Helper()->get_subscription_product_pause_options( $this );

			if ( 'yes' === $pause_info['allow_pause'] && $this->has_status( 'active' ) && ( 0 === $pause_info['max_pause'] || ( $pause_info['max_pause'] > 0 && $this->get_num_of_pauses() < $pause_info['max_pause'] ) ) ) {
				$can_be_paused = true;
			}

			return $can_be_paused;
		}

		/**
		 * Return if the subscription can be set active.
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function can_be_active() {
			$can_be_active = false;

			$status = array( 'pending', 'overdue', 'suspended', 'cancelled' );

			// the administrator and shop manager can switch the status to active.
			$post_type_object = get_post_type_object( YITH_YWSBS_POST_TYPE );
			if ( current_user_can( $post_type_object->cap->delete_post, $this->get_id() ) && $this->has_status( $status ) && ! ( $this->has_status( 'cancelled' ) && 'customer' === $this->get( 'cancelled_by' ) ) ) {
				$can_be_active = true;
			}

			return apply_filters( 'ywsbs_subscription_can_be_active', $can_be_active, $this );
		}

		/**
		 * Return if the subscription can be set as suspended.
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function can_be_suspended() {

			if ( ! ywsbs_get_suspension_time() ) {
				return false;
			}

			$can_be_suspended = false;
			$status           = array( 'active', 'overdue', 'cancelled' );

			// the administrator and shop manager can switch the status to suspended.
			$post_type_object = get_post_type_object( YITH_YWSBS_POST_TYPE );
			if ( current_user_can( $post_type_object->cap->delete_post, $this->get_id() ) && $this->has_status( $status ) ) {
				$can_be_suspended = true;
			}

			return apply_filters( 'ywsbs_subscription_can_be_suspended', $can_be_suspended, $this );
		}

		/**
		 * Return if the subscription can be set as suspended
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function can_be_overdue() {

			if ( ! ywsbs_get_overdue_time() ) {
				return false;
			}

			$can_be_overdue = false;
			$status         = array( 'active', 'suspended', 'cancelled' );

			// the administrator and shop manager can switch the status to cancelled.
			$post_type_object = get_post_type_object( YITH_YWSBS_POST_TYPE );
			if ( current_user_can( $post_type_object->cap->delete_post, $can_be_overdue ) && $this->has_status( $status ) ) {
				$can_be_overdue = true;
			}

			return apply_filters( 'ywsbs_subscription_can_be_overdue', $can_be_overdue, $this );
		}

		/**
		 * Return if the subscription can be resumed by user
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function can_be_resumed() {
			return apply_filters( 'ywsbs_subscription_can_be_resumed', $this->has_status( 'paused' ), $this );
		}

		/**
		 * Return if the subscription can be resumed by user
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function can_be_expired() {
			$expired_date   = $this->get_expired_date();
			$can_be_expired = ( $this->has_status( 'active' ) && ! empty( $expired_date ) && $expired_date <= current_time( 'timestamp' ) ); // phpcs:ignore
			return apply_filters( 'ywsbs_subscription_can_be_expired', $can_be_expired, $this );
		}

		/**
		 * Return if the subscription can be reactivate by user.
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function can_be_renewed() {
			$can_be_renewed = ( $this->has_status( array( 'cancelled', 'expired' ) ) && get_option( 'ywsbs_allow_customer_renew_subscription' ) === 'yes' );
			return apply_filters( 'ywsbs_subscription_can_be_renewed', $can_be_renewed, $this );
		}

		/**
		 * Return if the subscription can be editable
		 *
		 * @param string $key Field editable.
		 * @return bool
		 * @since  1.0.0
		 */
		public function can_be_editable( $key ) {
			$is_editable = false;
			$status      = array( 'cancelled', 'expired' );
			$gateway     = ywsbs_get_payment_gateway_by_subscription( $this );

			if ( ! $this->has_status( $status ) ) {
				if ( $gateway ) {
					if ( ! $gateway->supports( 'yith_subscriptions' ) && 'yes' === get_option( 'ywsbs_enable_manual_renews' ) ) {
						$is_editable = true;
					} else {
						switch ( $key ) {
							case 'payment_date':
								$is_editable = $gateway->supports( 'yith_subscriptions_payment_date' );
								break;
							case 'recurring_amount':
								$is_editable = $gateway->supports( 'yith_subscriptions_recurring_amount' );
								break;
							default:
						}
					}
				} else {
					$is_editable = true;
				}
			}

			return apply_filters( 'ywsbs_subscription_is_editable', $is_editable, $key, $this );
		}

		/**
		 * Return if the subscription can be cancelled by user
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function can_be_cancelled() {
			$can_be_cancelled        = false;
			$status                  = array( 'pending', 'overdue', 'suspended', 'cancelled' );
			$product                 = $this->get_product();
			$can_be_cancelled_option = $product instanceof WC_Product && $product->get_meta( '_ywsbs_override_cancellation_settings' ) === 'yes' ? $product->get_meta( '_ywsbs_can_be_cancelled' ) : get_option( 'ywsbs_allow_customer_cancel_subscription' );

			$post_type_object = get_post_type_object( YITH_YWSBS_POST_TYPE );
			if ( is_admin() && current_user_can( $post_type_object->cap->delete_post, $this->get_id() ) && ! $this->has_status( 'cancelled' ) ) {
				$can_be_cancelled = true;
			} elseif ( ! $this->has_status( $status ) && 'yes' === $can_be_cancelled_option ) {
				$can_be_cancelled = true;
			}

			return apply_filters( 'ywsbs_can_be_cancelled', $can_be_cancelled, $this );
		}

		/**
		 * Return if the a renew order can be created.
		 *
		 * @return bool|integer
		 * @since  1.0.0
		 */
		public function can_be_create_a_renew_order() {
			$can_be_create_a_renew = false;
			$status                = array( 'pending', 'expired' );

			// exit if no valid subscription status.
			if ( $this->has_status( $status ) || $this->get_payment_due_date() === $this->get_expired_date() ) {
				yith_subscription_log( 'a renew order cannot created because the subscription is  ' . $this->get( 'status' ), 'subscription_payment' );
				return $can_be_create_a_renew;
			}

			if ( $this->get_end_date() !== 0 ) {
				$paid_orders = $this->get_paid_order_list();
				$num_rates   = $this->get_num_of_rates();
				if ( ! empty( $paid_orders ) && ! empty( $num_rates ) && $paid_orders >= $num_rates ) {
					return $can_be_create_a_renew;
				}
			}

			// check if the subscription have a renew order.
			$renew_order = $this->get_renew_order();

			// if order doesn't exist, or is cancelled, we create order.
			if ( ! $renew_order || ( $renew_order && ( $renew_order->get_status() === 'cancelled' ) ) ) {
				$can_be_create_a_renew = true;
			} else {
				$can_be_create_a_renew = $renew_order->get_id();
			}

			return apply_filters( 'ywsbs_can_be_create_a_renew_order', $can_be_create_a_renew, $this );
		}

		/**
		 * Return if the shipping address can be edited.
		 *
		 * @return bool
		 * @since  1.4.0
		 */
		public function can_edit_shipping() {
			$status = array( 'active', 'suspended', 'overdue' );
			return ( $this->needs_shipping() && in_array( $this->get( 'status' ), $status, true ) );
		}


		/**
		 * Return if the customer can resubscribe the product.
		 *
		 * @return bool
		 * @since  1.4.0
		 */
		public function can_be_resubscribed() {
			$status  = array( 'cancelled', 'expired' );
			$product = $this->get_product();

			if ( ! $product ) {
				return false;
			}

			$has_child = $this->get( 'child_subscription' ) !== '';
			remove_filter( 'woocommerce_is_purchasable', array( 'YITH_WC_Subscription_Limit', 'is_purchasable' ) );
			$resubscribe = ( ! $has_child && $product->is_purchasable() && get_option( 'ywsbs_resubscribe_on_my_account', 'no' ) === 'yes' && $this->has_status( $status ) );
			add_filter( 'woocommerce_is_purchasable', array( 'YITH_WC_Subscription_Limit', 'is_purchasable' ), 10, 2 );
			return apply_filters( 'ywsbs_can_be_resubscribed', $resubscribe, $this );
		}

		/**
		 * Return if the subscription can be switchable
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function can_be_switchable() {

			return YWSBS_Subscription_Switch::is_a_switchable_subscription( $this );
		}

		/*
		|--------------------------------------------------------------------------
		| Checking Methods
		|--------------------------------------------------------------------------
		*/

		/**
		 * Return if the subscription as a specific status
		 *
		 * @param string|array $status Status to check.
		 *
		 * @return bool
		 * @since  2.0.0
		 */
		public function has_status( $status ) {
			$status = (array) $status;
			return in_array( $this->get_status(), $status, true );
		}

		/**
		 * Return the renew order if exists
		 *
		 * @return bool|WC_Order
		 * @since  1.1.5
		 */
		public function has_a_renew_order() {

			$return         = false;
			$renew_order_id = $this->get( 'renew_order' );

			if ( $renew_order_id ) {
				$order = wc_get_order( $renew_order_id );
				if ( $order instanceof WC_Order ) {
					$return = $order;
				}
			}

			return $return;
		}

		/**
		 * Check if the subscription product must be shipping
		 *
		 * @return bool
		 * @since  1.4.0
		 */
		public function needs_shipping() {
			return ( ! empty( $this->get( 'subscriptions_shippings' ) && apply_filters( 'ywsbs_edit_shipping_address', true, $this ) ) );
		}

		/**
		 * Return an array with the details of failed attempts.
		 *
		 * @return array|bool
		 */
		public function has_failed_attempts() {
			$return = false;
			$order  = $this->get_order();

			if ( ! $order ) {
				return $return;
			}

			$payment_method = $order->get_payment_method();
			$renew_order    = $this->get( 'renew_order' ) ? wc_get_order( $this->get( 'renew_order' ) ) : false;

			$order_ref            = ( $renew_order && ywsbs_support_scheduling( $payment_method ) ) ? $renew_order : $order;
			$failed_attempts      = $order_ref->get_meta( 'failed_attemps' );
			$next_payment_attempt = $order_ref->get_meta( 'next_payment_attempt' );

			$max_attempts         = ywsbs_get_max_failed_attempts_by_gateway( $payment_method );
			$gap_between_attempts = ywsbs_get_num_of_days_between_attempts_by_gateway( $payment_method );

			$return = array(
				'num_of_failed_attempts' => $failed_attempts,
				'max_failed_attempts'    => $max_attempts,
				'day_between_attempts'   => $gap_between_attempts,
				'next_payment_attempt'   => $next_payment_attempt,
			);

			return $return;
		}

		/**
		 * Return the data of subscription.
		 *
		 * @return array
		 */
		public function get_data() {

			$order = $this->get_order();
			if ( $order ) {
				$prices_include_tax = $order->get_prices_include_tax();
			} else {
				$prices_include_tax = wc_prices_include_tax();
			}

			$data = array(
				'id'                    => $this->get_id(),
				'status'                => $this->get_status(),
				'order_id'              => $this->get_order_id(),
				'customer_id'           => $this->get_user_id(),
				'currency'              => $this->get_order_currency(),
				'version'               => $this->get( 'ywsbs_version' ),
				'date_created'          => $this->post->post_date,
				'date_created_gmt'      => $this->post->post_date_gmt,
				'date_modified'         => $this->post->post_modified,
				'date_modified_gmt'     => $this->post->post_modified_gmt,
				'start_date'            => $this->get_start_date(),
				'next_payment_date'     => $this->get_payment_due_date(),
				'expired_date'          => $this->get_expired_date(),
				'cancelled_date'        => $this->get_cancelled_date(),
				'end_date'              => $this->get_end_date(),
				'expired_pause_date'    => $this->get_expired_date(),
				'product_id'            => $this->get_product_id(),
				'variation_id'          => $this->get_variation_id(),
				'product_name'          => $this->get_product_name(),
				'subscription_interval' => $this->get_price_is_per(),
				'subscription_period'   => $this->get_price_time_option(),
				'subscription_length'   => $this->get_max_length() > 0 ? $this->get_max_length() : '',
				'trial_period'          => empty( $this->get( 'trial_per' ) ) ? '' : $this->get( 'trial_per' ),
				'trial_interval'        => empty( $this->get( 'trial_per' ) ) ? '' : $this->get( 'trial_time_option' ),
				'quantity'              => $this->get_quantity(),
				'order_item_id'         => $this->get_order_item_id(),
				'payment_method'        => $this->get_payment_method(),
				'payment_method_title'  => $this->get_payment_method_title(),
				'created_via'           => $this->get_created_via(),
				'prices_include_tax'    => $prices_include_tax,
				'discount_total'        => $this->get_cart_discount(),
				'discount_tax'          => $this->get_cart_discount_tax(),
				'shipping_total'        => $this->get_order_shipping(),
				'shipping_tax'          => $this->get_order_shipping_tax(),
				'line_subtotal'         => $this->get_line_subtotal(),
				'line_subtotal_tax'     => $this->get_line_subtotal_tax(),
				'line_total'            => $this->get_line_total(),
				'line_tax'              => $this->get_line_tax(),
				'line_tax_data'         => $this->get_line_tax_data(),
				'order_total'           => $this->get_order_total(),
				'order_tax'             => $this->get_order_tax(),
				'order_subtotal'        => $this->get_order_subtotal(),
				'fee'                   => $this->get_fee(),
				'total'                 => $this->get_subscription_total(),
				'billing'               => $this->get_address_fields( 'billing', true ),
				'shipping'              => $this->get_address_fields( 'shipping', true ),
				'customer_order_note'   => $this->get_customer_order_note(),
				'shipping_data'         => $this->get_shipping_data(),
				'paid_orders'           => $this->get_paid_order_list(),
				'editable'              => $this->can_be_editable( 'payment_date' ) && $this->can_be_editable( 'recurring_amount' ),
			);

			$delivery_objects = YWSBS_Subscription_Delivery_Schedules()->get_delivery_schedules_ordered( $this->get_id() );
			if ( $delivery_objects ) {
				foreach ( $delivery_objects as $delivery ) {
					if ( isset( $delivery->subscription_id ) ) {
						unset( $delivery->subscription_id );
					}
					$data['delivery_schedules'][] = $delivery;
				}
			} else {
				$data['delivery_schedules'] = array();
			}

			return apply_filters( 'ywsbs_subscription_data', $data, $this );
		}

		/**
		 * Return the shipping data.
		 *
		 * @return array
		 */
		private function get_shipping_data() {
			$shipping_data  = $this->get( 'subscriptions_shippings' );
			$shipping_lines = array();
			if ( empty( $shipping_data ) ) {
				return array();
			}

			$order = $this->get_order();
			if ( $order ) {
				$shipping_order_items = $order->get_items( 'shipping' );
				foreach ( $shipping_order_items as $shipping_item ) {
					if ( $shipping_item->get_method_id() . ':' . $shipping_item->get_instance_id() === $shipping_data['method_id'] ) {
						$shipping_lines = array(
							'id'           => $shipping_item->get_id(),
							'method_id'    => $shipping_item->get_method_id(),
							'method_title' => $shipping_item->get_name(),
							'instance_id'  => $shipping_item->get_instance_id(),
							'total'        => $shipping_item->get_total(),
							'total_tax'    => $shipping_item->get_total_tax(),
							'taxes'        => $shipping_item->get_taxes(),
						);

					}
				}
			}
			return array( $shipping_lines );
		}
	}
}
