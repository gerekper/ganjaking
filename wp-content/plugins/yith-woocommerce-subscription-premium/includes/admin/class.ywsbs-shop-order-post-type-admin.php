<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YWSBS_Shop_Order_Post_Type_Admin Class.
 *
 * Add custom information inside WC_Order Admin.
 *
 * @class   YWSBS_Shop_Order_Post_Type_Admin
 * @package YITH WooCommerce Subscription
 * @since   2.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YWSBS_Shop_Order_Post_Type_Admin' ) ) {

	/**
	 * Class YWSBS_Shop_Order_Post_Type_Admin
	 */
	class YWSBS_Shop_Order_Post_Type_Admin {


		/**
		 * Single instance of the class
		 *
		 * @var YWSBS_Shop_Order_Post_Type_Admin
		 */
		protected static $instance;


		/**
		 * Returns single instance of the class
		 *
		 * @return YWSBS_Shop_Order_Post_Type_Admin
		 * @since  1.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 *
		 * Initialize actions and filters to be used
		 *
		 * @since 2.0.0
		 */
		public function __construct() {

			// add the column subscription on order list.
			add_filter( 'manage_shop_order_posts_columns', array( $this, 'manage_shop_order_columns' ), 20 );
			add_action( 'manage_shop_order_posts_custom_column', array( $this, 'show_subscription_ref' ) );

			add_action( 'add_meta_boxes', array( $this, 'show_related_subscription' ) );
		}

		/**
		 * Add the metabox to show the info of subscription
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function show_related_subscription() {
			add_meta_box( 'ywsbs-related-subscription', esc_html__( 'Related subscriptions', 'yith-woocommerce-subscription' ), array( $this, 'show_related_subscription_metabox' ), 'shop_order', 'normal', 'core' );
		}


		/**
		 * Metabox to show the related subscriptions inside the order editor
		 *
		 * @access public
		 *
		 * @param object $post WP_Post.
		 *
		 * @return void
		 *
		 * @since 1.0.0
		 */
		public function show_related_subscription_metabox( $post ) {
			$order = wc_get_order( $post->ID );

			if ( ! $order ) {
				return;
			}

			$subscription_list = $order->get_meta( 'subscriptions' );
			if ( empty( $subscription_list ) ) {
				return;
			}

			$subscriptions = array();

			foreach ( $subscription_list as $subscription_id ) {
				$subscription = ywsbs_get_subscription( $subscription_id );

				if ( is_null( $subscription->post ) ) {
					continue;
				}

				array_push( $subscriptions, $subscription );
			}

			$args = array( 'subscriptions' => $subscriptions );

			wc_get_template( 'admin/metabox/metabox_related_subscriptions.php', $args, '', YITH_YWSBS_TEMPLATE_PATH . '/' );

		}

		/**
		 * Add subscription column
		 *
		 * @param  array $columns Column list.
		 * @return array
		 * @since  1.4.5
		 */
		public function manage_shop_order_columns( $columns ) {

			$order_items = array( 'subscription_ref' => esc_html__( 'Subscription', 'yith-woocommerce-subscription' ) );
			$ref_pos     = array_search( 'order_date', array_keys( $columns ) ); //phpcs:ignore
			$columns     = array_slice( $columns, 0, $ref_pos + 1, true ) + $order_items + array_slice( $columns, $ref_pos + 1, count( $columns ) - 1, true );

			$order_items = array( 'subscription_payment_type' => esc_html__( 'Payment type', 'yith-woocommerce-subscription' ) );
			$ref_pos     = array_search( 'order_status', array_keys( $columns ) ); //phpcs:ignore
			$columns     = array_slice( $columns, 0, $ref_pos + 1, true ) + $order_items + array_slice( $columns, $ref_pos + 1, count( $columns ) - 1, true );

			return $columns;
		}

		/**
		 * Show the subscription number inside the order list.
		 *
		 * @param  string $column Column.
		 * @return void
		 */
		public function show_subscription_ref( $column ) {
			global $post, $the_order;

			if ( empty( $the_order ) || ( ( $the_order instanceof WC_Order ) && $the_order->get_id() !== $post->ID ) ) {
				$the_order = wc_get_order( $post->ID );
			}

			$subscriptions = $the_order->get_meta( 'subscriptions' );
			if ( ! $subscriptions && in_array( $column, array( 'subscription_ref', 'subscription_payment_type' ), true ) ) {
				echo '';
			}
			if ( 'subscription_ref' === $column ) {
				$links = array();
				if ( $subscriptions ) {
					foreach ( $subscriptions as $subscription_id ) {
						$subscription = ywsbs_get_subscription( $subscription_id );
						$links[]      = sprintf( '<a href="%s">%s</a>', get_edit_post_link( $subscription_id ), apply_filters( 'yswbw_subscription_number', $subscription->get_number() ) );
					}
				}

				if ( $links ) {
					echo wp_kses_post( implode( ', ', $links ) );
				}
			}

			if ( 'subscription_payment_type' === $column ) {
				global $post, $the_order;

				if ( empty( $the_order ) || ( ( $the_order instanceof WC_Order ) && $the_order->get_id() !== $post->ID ) ) {
					$the_order = wc_get_order( $post->ID );
				}

				$is_first_payment = $the_order->get_meta( '_ywsbs_order_version' );
				$is_a_renew       = $the_order->get_meta( 'is_a_renew' );
				$show             = ( '' !== $is_first_payment ) ? esc_html__( 'First Payment', 'yith-woocommerce-subscription' ) : '';
				$show             = ( 'yes' === $is_a_renew ) ? esc_html__( 'Renew', 'yith-woocommerce-subscription' ) : $show;

				echo esc_html( $show );

			}

		}
	}
}
