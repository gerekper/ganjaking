<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YWSBS_Multivendor class to add compatibility with YITH WooCommerce Multivendor
 *
 * @class   YWSBS_Multivendor
 * @since   1.0.0
 * @author  YITH
 * @package YITH WooCommerce Subscription
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YWSBS_Multivendor' ) && function_exists( 'YITH_Vendors' ) ) {
	/**
	 * Class YWSBS_Multivendor
	 */
	class YWSBS_Multivendor {

		/**
		 * Single instance of the class
		 *
		 * @var YWSBS_Multivendor
		 */
		protected static $instance;

		/**
		 * Current vendor
		 *
		 * @var YITH_Vendor|false
		 */
		protected $vendor = false;


		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 * @return YWSBS_Multivendor
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 *
		 * Initialize class and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 */
		public function __construct() {
			add_filter( 'yith_wcmv_register_taxonomy_object_type', array( $this, 'remove_vendor_taxonomy_from_subscription_object_type' ), 20 );
			add_filter( 'ywsbs_order_id_on_payment_complete', array( $this, 'retreive_parent_order_id_from_suborder' ) );
			add_action( 'init', array( $this, 'init' ), 20 );

			add_action( 'woocommerce_checkout_order_processed', array( $this, 'add_note_to_vendor_suborder' ), 110, 2 );

			// Create suborder for renew order.
			add_action( 'ywsbs_renew_subscription', array( $this, 'check_suborder' ), 10, 2 );
		}

		/**
		 * Init the integration.
		 */
		public function init() {

			$this->vendor = yith_get_vendor( 'current', 'user' );
			if ( 'yes' === get_option( 'yith_wpv_vendors_option_shipping_management', 'no' ) ) {
				add_action( 'yith_wcmv_add_shipping_order_item', array( $this, 'add_shipping_order_item' ), 10, 2 );
			}

			if ( $this->vendor && $this->vendor->is_valid() && $this->vendor->has_limited_access() ) {
				if ( 'yes' === get_option( 'yith_wpv_vendors_option_subscription_management', 'no' ) ) {
					add_filter( 'ywsbs_subscription_status_counter_query', array( $this, 'filter_subscription_status_counter_query' ), 10, 1 );
					add_filter( 'ywsbs_payment_method_filter_query', array( $this, 'filter_payment_method_query' ), 10, 1 );
					add_action( 'admin_menu', array( $this, 'vendor_admin_init' ), 4 );
					add_filter( 'yith_wpv_vendors_allowed_post_types', array( $this, 'add_allowed_post_types_for_vendors' ) );
					add_filter( 'yith_wcmv_vendor_disabled_manage_other_vendors_posts', array( $this, 'add_cap_to_role' ) );
					add_filter( 'ywsbs_renew_order_id', array( $this, 'get_renew_order_id_suborder' ), 10, 2 );
				} elseif ( is_admin() && function_exists( 'YWSBS_Product_Post_Type_Admin' ) ) {
					remove_action( 'woocommerce_variation_options', array( YWSBS_Product_Post_Type_Admin(), 'add_type_variation_options' ), 10 );
					remove_filter( 'product_type_options', array( YWSBS_Product_Post_Type_Admin(), 'add_type_options' ) );
				}
			}
		}

		/**
		 * Get a vendor ID. Useful to add compatibility with Multi Vendor 4.0
		 *
		 * @since  4.2.0
		 * @author Francesco Licandro
		 * @param YITH_Vendor $vendor The vendor instance.
		 * @return integer
		 */
		public function get_vendor_id( $vendor ) {
			return method_exists( $vendor, 'get_id' ) ? $vendor->get_id() : $vendor->id;
		}

		/**
		 * Get suborders from given order ID
		 *
		 * @since  4.2.0
		 * @author Francesco Licandro
		 * @param integer $order_id The parent order ID
		 * @return array
		 */
		public function get_suborders( $order_id ) {
			if ( version_compare( YITH_WPV_VERSION, '4.0.0', '>=' ) ) {
				$suborders = YITH_Vendors_Orders::get_suborders( $order_id );
			} else {
				$suborders = YITH_Vendors()->orders->get_suborder( $order_id );
			}

			return $suborders;
		}

		/**
		 * Return the suborder of a renew order.
		 *
		 * @param int                $order_id     Order id.
		 * @param YWSBS_Subscription $subscription Subscription.
		 *
		 * @return mixed
		 */
		public function get_renew_order_id_suborder( $order_id, $subscription ) {
			return $this->get_relative_subscription_suborder( $order_id, $this->vendor );
		}

		/**
		 * Return the suborder by parent order.
		 *
		 * @param int         $order_id Order id.
		 * @param YITH_Vendor $vendor   Vendor.
		 *
		 * @return mixed
		 */
		public function get_relative_subscription_suborder( $order_id, $vendor ) {
			$suborder_ids = $this->get_suborders( $order_id );

			foreach ( $suborder_ids as $suborder_id ) {
				$suborder  = wc_get_order( $suborder_id );
				$vendor_id = $suborder->get_meta( 'vendor_id', 1 );

				if ( $vendor_id == $this->get_vendor_id( $vendor ) ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
					return $suborder_id;
				}
			}

			return $order_id;
		}

		/**
		 * Filter the subscription custom post type to enable or not the access.
		 *
		 * @return bool
		 */
		public function add_cap_to_role() {

			if ( isset( $_GET['post'] ) ) {                     // phpcs:ignore
				$sbs = ywsbs_get_subscription( $_GET['post'] ); // phpcs:ignore
				if ( $sbs ) {
					$suborder_ids = $this->get_suborders( $sbs->get_order_id() );
					foreach ( $suborder_ids as $suborder_id ) {
						$suborder = wc_get_order( $suborder_id );
						if ( ! $suborder ) {
							continue;
						}

						$vendor_id = $suborder->get_meta( 'vendor_id', 1 );
						if ( $vendor_id == $this->get_vendor_id( $this->vendor ) ) { // phpcs:ignore
							return false;
						}
					}
				}
			}

			return true;
		}

		/**
		 * Add hook in backend.
		 */
		public function vendor_admin_init() {
			$this->add_subscription_capabilities();
			add_filter( 'ywsbs_register_panel_create_menu_page', '__return_true' );
			add_filter( 'ywsbs_register_panel_parent_page', array( $this, 'admin_vendor_parent_page' ) );
			add_filter( 'ywsbs_register_panel_capabilities', array( $this, 'admin_vendor_register_panel_capabilities' ) );
			add_filter( 'ywsbs_register_panel_tabs', array( $this, 'admin_vendor_register_panel_tabs' ) );
			// Filter subscription list table content.
			if ( version_compare( YITH_WPV_VERSION, '4.0.0', '>=' ) ) {
				add_filter( 'yith_wcmv_skip_ywsbs_subscription_filter_count_post', '__return_true' );
				add_action( 'yith_wcmv_vendor_filter_content_ywsbs_subscription', array( $this, 'filter_subscriptions' ), 10, 2 );
			} else {
				add_action( 'pre_get_posts', array( $this, 'filter_subscriptions' ), 20, 1 );
			}
			add_filter( 'ywsbs_activities_list_table_join', array( $this, 'admin_vendor_activities_list_table_join' ), 10, 2 );
			add_filter( 'ywsbs_activities_status_join', array( $this, 'admin_vendor_activities_list_table_join' ), 10, 2 );
			add_filter( 'ywsbs_activities_status_where', array( $this, 'admin_vendor_activities_list_table_where' ), 10, 2 );
			add_filter( 'ywsbs_activities_list_table_where', array( $this, 'admin_vendor_activities_list_table_where' ), 10, 2 );
			add_filter( 'ywsbs_last_renew_order', array( $this, 'change_last_renew_order' ), 10, 2 );
		}

		/**
		 * Filter the counter query inside the subscription list table.
		 *
		 * @param string $query Query String.
		 *
		 * @return string
		 */
		public function filter_subscription_status_counter_query( $query ) {
			global $wpdb;

			$products = $this->vendor->get_products();
			if ( ! empty( $products ) ) {
				$query = $wpdb->prepare(
					"SELECT count(*) as counter, ywsbs_pm.meta_value as status FROM {$wpdb->posts} as ywsbs_p 
LEFT JOIN {$wpdb->postmeta} as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
LEFT JOIN {$wpdb->postmeta} as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id ) 
WHERE ywsbs_p.post_type = %s AND ( ywsbs_pm2.meta_key = 'product_id' and ywsbs_pm2.meta_value IN (" . implode( ',', $products ) . ")  ) AND ywsbs_pm.meta_key = 'status' GROUP BY ywsbs_pm.meta_value",
					YITH_YWSBS_POST_TYPE
				); //phpcs:ignore
			}

			return $query;
		}

		/**
		 * Filter the counter query inside the subscription list table.
		 *
		 * @param string $query Query String.
		 *
		 * @return string
		 */
		public function filter_payment_method_query( $query ) {
			global $wpdb;

			$products = $this->vendor->get_products();
			if ( ! empty( $products ) ) {
				$query = $wpdb->prepare(
					"SELECT count(*) as counter, ywsbs_pm.meta_value as payment_method FROM {$wpdb->posts} as ywsbs_p 
LEFT JOIN {$wpdb->postmeta} as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
LEFT JOIN {$wpdb->postmeta} as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id ) 
WHERE ywsbs_p.post_type = %s AND ( ywsbs_pm2.meta_key = 'product_id' and ywsbs_pm2.meta_value IN (" . implode( ',', $products ) . ")  ) AND ywsbs_pm.meta_key = 'payment_method' GROUP BY ywsbs_pm.meta_value",
					YITH_YWSBS_POST_TYPE
				); //phpcs:ignore
			}

			return $query;
		}


		/**
		 * Add the capability to the vendor.
		 */
		public function add_subscription_capabilities() {
			// gets the admin and shop_manager roles.
			$vendor_role = get_role( 'manage_vendor_store' );
			if ( $vendor_role ) {
				foreach ( YWSBS_Subscription_Helper::get_subscription_capabilities() as $key => $cap ) {
					$vendor_role->add_cap( $cap );
				}
			}
		}

		/**
		 * Filter subscriptions by Vendor
		 *
		 * @param WP_Query $query WP_Query.
		 * @param YITH_Vendor $vendor The current vendor.
		 */
		public function filter_subscriptions( $query, $vendor = false ) {

			if ( ! empty( YITH_Vendors()->addons->compatibility ) ) {
				remove_action( 'pre_get_posts', array( YITH_Vendors()->addons->compatibility, 'filter_vendor_post_types' ) );
			}

			if ( $query->is_main_query() && isset( $query->query['post_type'] ) && YITH_YWSBS_POST_TYPE === $query->query['post_type'] ) {
				$meta_query = ! ! $query->get( 'meta_query' ) ? $query->get( 'meta_query' ) : array();
				$products   = $this->vendor->get_products();

				$meta_query[] = array(
					'key'     => 'product_id',
					'value'   => $products,
					'compare' => 'IN',
				);

				$query->set( 'meta_query', $meta_query );
			}

		}

		/**
		 * Permit vendor to see the subscription menu in administration panel
		 *
		 * @access public
		 * @since  1.0.0
		 * @param string $parent_page Parent page.
		 * @return string
		 */
		public function admin_vendor_parent_page( $parent_page ) {
			return '';
		}

		/**
		 * Permit vendor to see the subscription and activities panel
		 *
		 * @access public
		 * @since  1.0.0
		 * @param array $capabilities Capabilities.
		 * @return string
		 */
		public function admin_vendor_register_panel_capabilities( $capabilities ) {
			return 'manage_vendor_store';
		}

		/**
		 * Permit vendor to see the subscription and activities panel
		 *
		 * @access public
		 * @since  1.0.0
		 * @param array $tabs Tabs.
		 * @return array
		 */
		public function admin_vendor_register_panel_tabs( $tabs ) {
			$tabs = array(
				'subscription' => esc_html__( 'Subscriptions', 'yith-woocommerce-subscription' ),
			);

			return $tabs;
		}


		/**
		 * Change last renew order.
		 *
		 * @param int $last_order Last order id.
		 * @return mixed
		 */
		public function change_last_renew_order( $last_order ) {
			$suborder_ids = $this->get_suborders( $last_order );
			foreach ( $suborder_ids as $suborder_id ) {
				$suborder  = wc_get_order( $suborder_id );
				if ( ! $suborder ) {
					continue;
				}

				$vendor_id = $suborder->get_meta( 'vendor_id' );
				if ( $vendor_id == $this->get_vendor_id( $this->vendor ) ) { // phpcs:ignore
					$last_order = $suborder_id;
				}
			}

			return $last_order;
		}

		/**
		 * Permit vendor to see the subscription and activities panel
		 *
		 * @access public
		 * @since  1.0.0
		 * @param string $tablename Tablename.
		 * @param string $join      Join.
		 * @return string
		 */
		public function admin_vendor_activities_list_table_join( $join, $tablename ) {
			global $wpdb;

			$join .= ' LEFT JOIN ' . $wpdb->prefix . 'postmeta as sub_pm ON ( act.subscription = sub_pm.post_id ) ';
			$join .= ' LEFT JOIN ' . $wpdb->prefix . 'posts as sub_p ON ( sub_p.ID = act.subscription ) ';

			return $join;
		}

		/**
		 * Permit vendor to see the subscription and activities panel
		 *
		 * @access public
		 * @since  1.0.0
		 * @param string $where     Where.
		 * @param string $tablename Tablename.
		 * @return string
		 */
		public function admin_vendor_activities_list_table_where( $where, $tablename ) {
			$products = $this->vendor->get_products();
			$where    .= " AND ( sub_pm.meta_key='product_id' AND sub_pm.meta_value IN  (" . implode( ',', $products ) . ')  ) ';

			return $where;
		}

		/**
		 * Add Allowed Post Types for Vendors
		 *
		 * @since  1.0.0
		 * @param array $allowed_post_types the allowed post types for Vendors; default are 'product' and 'shop_coupon'.
		 * @return array
		 */
		public function add_allowed_post_types_for_vendors( $allowed_post_types ) {
			$allowed_post_types[] = 'ywsbs_subscription';

			return $allowed_post_types;
		}

		/**
		 * Remove vendor taxonomy from subscription object type.
		 *
		 * @param array $object_type Object type.
		 *
		 * @return array
		 */
		public function remove_vendor_taxonomy_from_subscription_object_type( $object_type ) {
			$key = array_search( 'ywsbs_subscription', $object_type, true );

			if ( false !== $key ) {
				unset( $object_type[ $key ] );
			}

			return $object_type;
		}

		/**
		 * Get the parent order id from a suborder id.
		 *
		 * @param int $order_id Order id.
		 * @return bool|false|int
		 */
		public function retreive_parent_order_id_from_suborder( $order_id ) {
			$post_parent_id = wp_get_post_parent_id( $order_id );

			if ( $post_parent_id != 0 ) { // phpcs:ignore
				$order_id = $post_parent_id;
			}

			return $order_id;
		}

		/**
		 * Add shipping order item.
		 *
		 * @param int      $suborder_id  Suborder id.
		 * @param WC_Order $vendor_order Vendor order.
		 * @throws WC_Data_Exception Trigger an Error.
		 */
		public function add_shipping_order_item( $suborder_id, $vendor_order ) {
			if ( function_exists( 'YITH_Vendor_Shipping' ) ) {
				$parent_order_id = wp_get_post_parent_id( $suborder_id );

				if ( $parent_order_id ) {
					$parent_order = wc_get_order( $parent_order_id );

					if ( $parent_order instanceof WC_Order ) {
						$is_a_renew = $parent_order->get_meta( 'is_a_renew' );
						if ( 'yes' === $is_a_renew ) {
							$packages = $parent_order->get_shipping_methods();
							if ( ! empty( $packages ) ) {
								foreach ( $packages as $line_item_id => $line_item ) {
									$suborder = wc_get_order( $suborder_id );
									if ( $suborder instanceof WC_Order ) {
										/**
										 * Line item
										 *
										 * @var WC_Order_Item_Shipping $line_item
										 */
										$id            = $line_item->get_id();
										$label         = $line_item->get_method_title();
										$cost          = $line_item->get_total();
										$taxes         = $line_item->get_taxes();
										$method_id     = $line_item->get_method_id();
										$shipping_rate = new WC_Shipping_Rate( $id, $label, $cost, $taxes, $method_id );
										$vendor_order->add_shipping( $suborder, $shipping_rate );
									}
								}
							}
						}
					}
				}
			}
		}

		/**
		 * Check suborder
		 *
		 * @param int $parent_order_id Parent order id.
		 * @param int $subscription_id Subscription id.
		 */
		public function check_suborder( $parent_order_id, $subscription_id ) {
			if ( ! empty( YITH_Vendors()->orders ) ) {
				$suborder_ids = YITH_Vendors()->orders->check_suborder( $parent_order_id, array(), true );
				if ( $suborder_ids ) {
					foreach ( $suborder_ids as $suborder_id ) {
						$suborder = wc_get_order( $suborder_id );
						if ( ! $suborder ) {
							continue;
						}

						$suborder->add_order_note( sprintf( '%s %s', __( 'This order has been created to renew subscription', 'yith-woocommerce-subscription' ), $subscription_id ) );
						$suborder->update_meta_data( 'is_a_renew', 'yes' );
						$suborder->save();

						do_action( 'yith_suborder_renew_created', $parent_order_id );
					}
				}
			}
		}

		/**
		 * Add a note to the vendor suborder.
		 *
		 * @param int   $parent_order_id Parent order id.
		 * @param array $posted          Post array.
		 */
		public function add_note_to_vendor_suborder( $parent_order_id, $posted ) {
			if ( $parent_order_id ) {
				$parent_order = wc_get_order( $parent_order_id );
				if ( $parent_order instanceof WC_Order ) {
					$has_sub_order = $parent_order->get_meta( 'has_sub_order' );
					if ( $has_sub_order ) {
						$is_a_renew    = $parent_order->get_meta( 'is_a_renew' );
						$subscriptions = $parent_order->get_meta( 'subscriptions' );
						if ( 'yes' !== $is_a_renew && ! empty( $subscriptions ) ) {
							$suborder_ids = $this->get_suborders( $parent_order_id );

							foreach ( $suborder_ids as $suborder_id ) {
								if ( $suborder_id != $parent_order_id ) { // phpcs:ignore
									$suborder = wc_get_order( $suborder_id );
									if ( $suborder instanceof WC_Order ) {
										$suborder->add_order_note(
											sprintf(
												'%s #%s %s ',
												_x( 'A new subscription', '[Part of]: A new subscription has been created from this order', 'yith-woocommerce-subscription' ),
												ywsbs_get_subscription_number_by_subscription_id( $subscriptions[0] ), // is the Subscription number.
												_x( 'has been created from this order', '[Part of]: A new subscription has been created from this order', 'yith-woocommerce-subscription' )
											)
										);
									}
								}
							}
						}
					}
				}
			}
		}
	}
}

/**
 * Unique access to instance of YWSBS_Multivendor class
 *
 * @return YWSBS_Multivendor
 */
function YWSBS_Multivendor() { //phpcs:ignore
	return YWSBS_Multivendor::get_instance();
}
