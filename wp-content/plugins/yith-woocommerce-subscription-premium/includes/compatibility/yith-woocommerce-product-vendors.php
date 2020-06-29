<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly
}


/**
 * YWSBS_Multivendor class to add compatibility with YITH WooCommerce Multivendor
 *
 * @class   YWSBS_Multivendor
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YWSBS_Multivendor' ) && function_exists( 'YITH_Vendors' ) ) {

	class YWSBS_Multivendor {

		/**
		 * Single instance of the class
		 *
		 * @var \YWSBS_Multivendor
		 */
		protected static $instance;



		/**
		 * Returns single instance of the class
		 *
		 * @return \YWSBS_Multivendor
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize class and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {
			add_filter( 'yith_wcmv_register_taxonomy_object_type', array( $this, 'remove_vendor_taxonomy_from_subscription_object_type' ), 20 );
			add_filter( 'ywsbs_order_id_on_payment_complete', array( $this, 'retreive_parent_order_id_from_suborder' ) );
			add_action( 'init', array( $this, 'init' ), 20 );

			add_action( 'woocommerce_checkout_order_processed', array( $this, 'add_note_to_vendor_suborder' ), 110, 2 );

			// Create suborder for renew order
			add_action( 'ywsbs_renew_subscription', array( $this, 'check_suborder' ), 10, 2 );
		}

		public function init() {
			$vendor = yith_get_vendor( 'current', 'user' );

			if ( 'yes' == get_option( 'yith_wpv_vendors_option_shipping_management', 'no' ) ) {
				add_action( 'yith_wcmv_add_shipping_order_item', array( $this, 'add_shipping_order_item' ), 10, 2 );
			}

			if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
				if ( 'yes' == get_option( 'yith_wpv_vendors_option_subscription_management', 'no' ) ) {
					add_action( 'admin_menu', array( $this, 'vendor_admin_init' ), 4 );
					add_filter( 'yith_wpv_vendors_allowed_post_types', array( $this, 'add_allowed_post_types_for_vendors' ) );
					add_filter( 'yith_wcmv_vendor_disabled_manage_other_vendors_posts', array( $this, 'add_cap_to_role' ) );
					add_filter( 'ywsbs_renew_order_id', array( $this, 'get_renew_order_id_suborder' ), 10, 2 );
				} else {
					remove_action( 'woocommerce_variation_options', array( YITH_WC_Subscription_Admin(), 'add_type_variation_options' ), 10, 3 );
					remove_filter( 'product_type_options', array( YITH_WC_Subscription_Admin(), 'add_type_options' ) );
				}
			}
		}

		public function get_renew_order_id_suborder( $order_id, $subscription ) {
			$vendor = yith_get_vendor( 'current', 'user' );
			return $this->get_relative_subscription_suborder( $order_id, $vendor );
		}

		/**
		 * Return the suborder by parent order.
		 *
		 * @param $order_id
		 * @param $vendor
		 *
		 * @return mixed
		 */
		public function get_relative_subscription_suborder( $order_id, $vendor ) {
			$suborder_ids = YITH_Orders::get_suborder( $order_id );
			foreach ( $suborder_ids as $suborder_id ) {
				$suborder  = wc_get_order( $suborder_id );
				$vendor_id = $suborder->get_meta( 'vendor_id', 1 );

				if ( $vendor_id == $vendor->id ) {
					return $suborder_id;
				}
			}

			return $order_id;
		}

		/**
		 * Filter the subscription custom post type to enable or not the access.
		 *
		 * @param $vendor
		 *
		 * @return bool
		 */
		public function add_cap_to_role( $vendor ) {
			$vendor = yith_get_vendor( 'current', 'user' );

			if ( isset( $_GET['post'] ) ) {
				$sbs = ywsbs_get_subscription( $_GET['post'] );
				if ( $sbs ) {
					$suborder_ids = YITH_Orders::get_suborder( $sbs->order_id );
					foreach ( $suborder_ids as $suborder_id ) {
						$suborder  = wc_get_order( $suborder_id );
						$vendor_id = $suborder->get_meta( 'vendor_id', 1 );

						if ( $vendor_id == $vendor->id ) {
							return false;
						}
					}
				}
			}

			return true;
		}


		public function vendor_admin_init() {
			$vendor = yith_get_vendor( 'current', 'user' );

			if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
				add_filter( 'ywsbs_register_panel_create_menu_page', array( $this, 'admin_vendor_create_menu_page' ) );
				add_filter( 'ywsbs_register_panel_parent_page', array( $this, 'admin_vendor_parent_page' ) );
				add_filter( 'ywsbs_register_panel_capabilities', array( $this, 'admin_vendor_register_panel_capabilities' ) );
				add_filter( 'ywsbs_register_panel_tabs', array( $this, 'admin_vendor_register_panel_tabs' ) );

				add_filter( 'ywsbs_subscriptions_list_table_join', array( $this, 'admin_vendor_subscriptions_list_table_join' ) );
				add_filter( 'ywsbs_subscriptions_list_table_where', array( $this, 'admin_vendor_subscriptions_list_table_where' ) );
				add_filter( 'ywsbs_activities_list_table_join', array( $this, 'admin_vendor_activities_list_table_join' ), 10, 2 );
				add_filter( 'ywsbs_activities_list_table_where', array( $this, 'admin_vendor_activities_list_table_where' ), 10, 2 );
				add_filter( 'ywsbs_last_renew_order', array( $this, 'change_last_renew_order' ), 10, 2 );

				/* Add/Remove Subscription capabilities to vendors */
			}
		}

		/**
		 * Permit vendor to see the subscription menu in administration panel
		 *
		 * @access   public
		 *
		 * @param bool $create_menu_page
		 *
		 * @return bool
		 * @since    1.0.0
		 */
		public function admin_vendor_create_menu_page( $create_menu_page ) {

			return true;
		}

		/**
		 * Permit vendor to see the subscription menu in administration panel
		 *
		 * @access public
		 *
		 * @param string $parent_page
		 *
		 * @return string
		 * @since  1.0.0
		 */
		public function admin_vendor_parent_page( $parent_page ) {
			return '';
		}

		/**
		 * Permit vendor to see the subscription and activities panel
		 *
		 * @access public
		 *
		 * @param array $capabilities
		 *
		 * @return array
		 * @since  1.0.0
		 */
		public function admin_vendor_register_panel_capabilities( $capabilities ) {

			$capabilities = 'manage_vendor_store';
			return $capabilities;
		}

		/**
		 * Permit vendor to see the subscription and activities panel
		 *
		 * @access public
		 *
		 * @param array $tabs
		 *
		 * @return array
		 * @since  1.0.0
		 */
		public function admin_vendor_register_panel_tabs( $tabs ) {

			$tabs = array(
				'subscriptions' => __( 'Subscriptions', 'yith-woocommerce-subscription' ),
				'activities'    => __( 'Activities', 'yith-woocommerce-subscription' ),

			);

			return $tabs;
		}

		/**
		 * Permit vendor to see the subscription and activities panel
		 *
		 * @access public
		 *
		 * @param array $join
		 *
		 * @return string
		 * @since  1.0.0
		 */
		public function admin_vendor_subscriptions_list_table_join( $join ) {
			global $wpdb;

			$join .= ' LEFT JOIN ' . $wpdb->prefix . 'postmeta as sub_pm ON ( ywsbs_p.ID = sub_pm.post_id ) ';
			// $join .= " LEFT JOIN " . $wpdb->prefix . "postmeta as sub_pm2 ON ( ywsbs_p.ID = sub_pm2.post_id AND ( sub_pm2.meta_key='variation_id') ) ";

			return $join;
		}

		/**
		 * Permit vendor to see the subscription and activities panel
		 *
		 * @access public
		 *
		 * @param array $where
		 *
		 * @return string
		 * @since  1.0.0
		 */
		public function admin_vendor_subscriptions_list_table_where( $where ) {
			$vendor   = yith_get_vendor( 'current', 'user' );
			$products = $vendor->get_products();
			$where   .= " AND ( sub_pm.meta_key='product_id' AND sub_pm.meta_value IN  (" . implode( ',', $products ) . ')  ) ';

			return $where;
		}

		/**
		 * @param $last_order
		 *
		 * @return mixed
		 */
		public function change_last_renew_order( $last_order ) {
			$vendor = yith_get_vendor( 'current', 'user' );

			if ( $vendor && ! empty( YITH_Vendors()->orders ) ) {
				$suborder_ids = YITH_Orders::get_suborder( $last_order );
				foreach ( $suborder_ids as $suborder_id ) {
					$suborder  = wc_get_order( $suborder_id );
					$vendor_id = $suborder->get_meta( 'vendor_id', 1 );
					if ( $vendor_id == $vendor->id ) {
						$last_order = $suborder_id;
					}
				}
			}

			return $last_order;
		}

		/**
		 * Permit vendor to see the subscription and activities panel
		 *
		 * @access public
		 *
		 * @param array     $join
		 *
		 * @param $tablename
		 *
		 * @return string
		 * @since  1.0.0
		 */
		public function admin_vendor_activities_list_table_join( $join, $tablename ) {
			global $wpdb;

			$join .= ' LEFT JOIN ' . $wpdb->prefix . 'postmeta as sub_pm ON ( act.subscription = sub_pm.post_id ) ';
			$join .= ' LEFT JOIN ' . $wpdb->prefix . 'posts as sub_p ON ( sub_p.ID = act.subscription ) ';
			// $join .= " LEFT JOIN " . $wpdb->prefix . "postmeta as sub_pm2 ON ( ywsbs_p.ID = sub_pm2.post_id AND ( sub_pm2.meta_key='variation_id') ) ";

			return $join;
		}

		/**
		 * Permit vendor to see the subscription and activities panel
		 *
		 * @access public
		 *
		 * @param array     $where
		 *
		 * @param $tablename
		 *
		 * @return string
		 * @since  1.0.0
		 */
		public function admin_vendor_activities_list_table_where( $where, $tablename ) {
			$vendor   = yith_get_vendor( 'current', 'user' );
			$products = $vendor->get_products();
			$where   .= " AND ( sub_pm.meta_key='product_id' AND sub_pm.meta_value IN  (" . implode( ',', $products ) . ')  ) ';

			return $where;
		}



		/**
		 * Add Allowed Post Types for Vendors
		 *
		 * @param array $allowed_post_types the allowed post types for Vendors; default are 'product' and 'shop_coupon'
		 *
		 * @since  1.0.0
		 * @return array
		 */
		public function add_allowed_post_types_for_vendors( $allowed_post_types ) {

			$allowed_post_types[] = 'ywsbs_subscription';

			return $allowed_post_types;
		}

		public function remove_vendor_taxonomy_from_subscription_object_type( $object_type ) {
			$key = array_search( 'ywsbs_subscription', $object_type );

			if ( $key !== false ) {
				unset( $object_type[ $key ] );
			}

			return $object_type;
		}

		public function retreive_parent_order_id_from_suborder( $order_id ) {
			$post_parent_id = wp_get_post_parent_id( $order_id );

			if ( $post_parent_id != 0 ) {
				$order_id = $post_parent_id;
			}
			return $order_id;
		}

		public function add_shipping_order_item( $suborder_id, $vendor_order ) {
			if ( function_exists( 'YITH_Vendor_Shipping' ) ) {
				$parent_order_id = wp_get_post_parent_id( $suborder_id );
				if ( $parent_order_id ) {
					$parent_order = wc_get_order( $parent_order_id );

					if ( $parent_order instanceof WC_Order ) {
						$is_a_renew = yit_get_prop( $parent_order, 'is_a_renew', true );
						if ( 'yes' == $is_a_renew ) {
							$packages = $parent_order->get_shipping_methods();
							if ( ! empty( $packages ) ) {
								foreach ( $packages as $line_item_id => $line_item ) {
									$suborder = wc_get_order( $suborder_id );
									if ( $suborder instanceof WC_Order ) {
										/**
										 * @var WC_Order_Item_Shipping $line_item
										 * @var WC_Order $vendor_order
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

		public function check_suborder( $parent_order_id, $subscription_id ) {
			if ( ! empty( YITH_Vendors()->orders ) ) {
				$suborder_ids = YITH_Vendors()->orders->check_suborder( $parent_order_id, array(), true );
				foreach ( $suborder_ids as $suborder_id ) {
					$suborder = wc_get_order( $suborder_id );
					$suborder->add_order_note( sprintf( '%s %s', __( 'This order has been created to renew subscription', 'yith-woocommerce-subscription' ), $subscription_id ) );
					$suborder->update_meta_data( 'is_a_renew', 'yes' );
					$suborder->save();

					do_action( 'yith_suborder_renew_created', $parent_order_id );
				}
			}
		}

		public function add_note_to_vendor_suborder( $parent_order_id, $posted ) {
			if ( $parent_order_id ) {
				$parent_order = wc_get_order( $parent_order_id );
				if ( $parent_order instanceof WC_Order ) {
					$has_sub_order = yit_get_prop( $parent_order, 'has_sub_order', true );
					if ( $has_sub_order ) {
						$is_a_renew    = yit_get_prop( $parent_order, 'is_a_renew', true );
						$subscriptions = yit_get_prop( $parent_order, 'subscriptions', true );
						if ( 'yes' != $is_a_renew && ! empty( $subscriptions ) ) {
							$suborder_ids = YITH_Orders::get_suborder( $parent_order_id );
							foreach ( $suborder_ids as $suborder_id ) {
								if ( $suborder_id != $parent_order_id ) {
									$suborder = wc_get_order( $suborder_id );
									if ( $suborder instanceof WC_Order ) {
										$suborder->add_order_note(
											sprintf(
												'%s #%s %s ',
												_x( 'A new subscription', '[Part of]: A new subscription has been created from this order', 'yith-woocommerce-subscription' ),
												$subscriptions[0], // is the Subscription ID
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
 * @return \YWSBS_Multivendor
 */
function YWSBS_Multivendor() {
	return YWSBS_Multivendor::get_instance();
}
