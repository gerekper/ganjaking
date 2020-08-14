<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Compatibility_Role_Based_Prices_Multivendor' ) ) {

	class Compatibility_Role_Based_Prices_Multivendor {
		/**
		 * @var Compatibility_Role_Based_Prices_Multivendor
		 */
		protected static $instance;

		/**
		 * @author YITHEMES
		 * Compatibility_Role_Based_Prices_Multivendor constructor.
		 */
		public function __construct() {
			if ( $this->has_multivendor_active() ) {

				add_filter( 'yith_role_based_prices_args_post_type', array( $this, 'change_args_post_type' ), 15 );
				add_filter( 'yith_wc_role_based_prices_back_link', array( $this, 'change_back_link' ) );
				add_action( 'admin_menu', array( $this, 'add_new_in_menu' ), 99 );
				add_action( 'admin_menu', array( $this, 'add_role_based_prices_tab_for_vendor' ), 5 );
				add_filter( 'yith_wpv_vendor_menu_items', array( $this, 'add_item_to_vendors_admin_menu' ) );
				add_filter( 'yith_wc_role_based_prices_filter_product', array( $this, 'add_product_params' ) );
				add_filter( 'yith_wc_role_based_price_params_rule', array( $this, 'add_rule_params' ), 10, 2 );
			}
		}

		/**
		 * @author YITHEMES
		 * @since 1.0.0
		 * check if a vendor is valid
		 * @return bool
		 */
		public function vendor_is_valid() {

			$vendor = yith_get_vendor( 'current', 'user' );

			return $vendor->is_valid() && $vendor->has_limited_access() && $this->has_role_based_price_enabled();
		}

		/**
		 * check if multivendor is installed and active
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return bool
		 */
		public function has_multivendor_active() {

			return defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM && defined( 'YITH_WPV_VERSION' ) && version_compare( YITH_WPV_VERSION, apply_filters( 'yith_wcpsc_multivendor_min_version', '1.9.5' ), '>=' );

		}

		/**
		 * check if vendor has role based option enabled
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return bool
		 */
		public function has_role_based_price_enabled() {

			$option = get_option( 'yith_wpv_vendors_option_role_based_prices_management', 'no' );

			return $option === 'yes';
		}


		/**
		 * show post type in menu
		 * @author YITHEMES
		 * @sincr 1.0.0
		 *
		 * @param $post_type_args
		 *
		 * @return mixed
		 */
		public function change_args_post_type( $post_type_args ) {

			if ( ! $this->vendor_is_valid() ) {
				return $post_type_args;
			}

			$post_type_args['show_in_menu'] = true;

			return $post_type_args;
		}

		/**
		 * change back link for vendor
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param $link
		 *
		 * @return string
		 */
		public function change_back_link( $link ) {

			if ( ! $this->vendor_is_valid() ) {
				return $link;
			}

			$admin_url = admin_url( 'edit.php' );
			$params    = array(
				'page'      => 'yith_vendor_role_based_prices_settings',
				'post_type' => 'yith_price_rule'
			);

			$back_link = esc_url( add_query_arg( $params, $admin_url ) );

			return $back_link;
		}

		/**
		 * add new submenupage for vendor
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function add_new_in_menu() {

			if ( ! $this->vendor_is_valid() ) {
				return;
			}

			$add_new_label = YITH_Role_Based_Type()->get_taxonomy_label( 'add_new_item' );
			add_submenu_page( 'edit.php?post_type=yith_price_rule', $add_new_label, $add_new_label, 'edit_price_rules', 'post-new.php?post_type=yith_price_rule' );
		}

		/**
		 * add role based menu for vendor
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function add_role_based_prices_tab_for_vendor() {

			if ( ! $this->vendor_is_valid() ) {
				return;
			}


			remove_submenu_page( 'edit.php?post_type=yith_price_rule', 'edit.php?post_type=yith_price_rule' );
			remove_submenu_page( 'edit.php?post_type=yith_price_rule', 'post-new.php?post_type=yith_price_rule' );
			$admin_tabs = array(

				'price-rules' => __( 'Price rules', 'yith-woocommerce-role-based-prices' ),

			);

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => __( 'All Role Based Prices', 'yith-woocommerce-role-based-prices' ),
				'menu_title'       => __( 'All Role Based Prices', 'yith-woocommerce-role-based-prices' ),
				'capability'       => 'manage_vendor_store',
				'parent_page'      => 'edit.php?post_type=yith_price_rule',
				'page'             => 'yith_vendor_role_based_prices_settings',
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YWCRBP_DIR . '/plugin-options',
				'icon_url'         => 'dashicons-admin-plugins',
				'position'         => 0
			);

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once( YWCRBP_DIR . '/plugin-fw/lib/yit-plugin-panel-wc.php' );
			}

			$this->_vendor_panel = new YIT_Plugin_Panel_WooCommerce( $args );


		}

		/**
		 * Returns single instance of the class
		 * @author YITHEMES
		 * @return \Compatibility_Role_Based_Prices_Multivendor
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * add item to vendors
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param $menu_items
		 *
		 * @return array
		 */
		public function add_item_to_vendors_admin_menu( $menu_items ) {

			if ( ! $this->vendor_is_valid() ) {
				return $menu_items;
			}

			return array_unique( array_merge( $menu_items, array( 'admin.php?page=yith_vendor_role_based_prices_settings' ) ) );
		}

		/**
		 * filter product by vendor and by admin
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param $product_args
		 *
		 * @return mixed
		 */
		public function add_product_params( $product_args ) {

			if ( $this->has_multivendor_active()  ) {

				$vendor = yith_get_vendor( 'current', 'user' );

				if ( $vendor->is_valid() || $vendor->is_user_admin() ) {
					if ( ! $vendor->has_limited_access() ) {

						$product_args['tax_query'] = array(
							array(
								'taxonomy' => YITH_Vendors()->get_taxonomy_name(),
								'field'    => 'id',
								'terms'    => YITH_Vendors()->get_vendors( array( 'fields' => 'ids' ) ),
								'operator' => 'NOT IN'
							)
						);
					} else {
						$product_args['post__in'] = $vendor->get_products();
					}
				}
			}

			return $product_args;
		}

		/**
		 * filter price rule by vendor
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param $rule_args
		 * @param int $product_id
		 *
		 * @return mixed
		 */
		public function add_rule_params( $rule_args, $product_id ) {

			if ( $this->has_multivendor_active() && $this->has_role_based_price_enabled() && $product_id ) {

				$vendor = yith_get_vendor( $product_id, 'product' );

				if ( ! $vendor->is_valid() ) {

					$vendors  = YITH_Vendors()->get_vendors( array( 'fields' => 'ids' ) );
					$operator = 'NOT IN';

				} else {
					$vendors  = array( $vendor->id );
					$operator = 'IN';
				}
				$rule_args['tax_query'] = array(
					array(
						'taxonomy' => YITH_Vendors()->get_taxonomy_name(),
						'field'    => 'id',
						'terms'    => $vendors,
						'operator' => $operator
					)
				);


			}

			return $rule_args;
		}

	}
}
/**
 * Unique access to instance of Compatibility_Role_Based_Prices_Multivendor class
 * @author YITHEMES
 * @return Compatibility_Role_Based_Prices_Multivendor
 * @since 1.0.0
 */
function YITH_Role_Based_Prices_Multivendor_Compatibility() {
	return Compatibility_Role_Based_Prices_Multivendor::get_instance();
}

YITH_Role_Based_Prices_Multivendor_Compatibility();