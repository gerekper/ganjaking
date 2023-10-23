<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 * @class      YITH_WCFM_For_Vendor
 * @package    Yithemes
 * @since      Version 1.7
 * @author     YITH <plugins@yithemes.com>
 *
 */
if ( ! class_exists( 'YITH_Frontend_Manager_For_Vendor' ) ) {

	/**
	 * YITH_Frontend_Manager_For_Vendor Class
	 */
	class YITH_Frontend_Manager_For_Vendor {

		/**
		 * Main instance
		 */
		private static $_instance = null;

		/**
		 * check if current user is a valid vendor
		 */
		protected $current_user_is_vendor = null;

		/**
		 * check if current user is a valid vendor
		 */
		public $vendor = null;

		/**
		 * Construct
		 */
		public function __construct() {

			$this->vendor                 = yith_get_vendor( 'current', 'user' );
			$this->current_user_is_vendor = $this->vendor->is_valid() && $this->vendor->has_limited_access();

			if ( ! is_admin() ) {
				//Instance of vendors admin classes on front
				$admin_multivendor_classes = YITH_Vendors()->require['admin'];

				foreach ( $admin_multivendor_classes as $class ) {
					require_once( YITH_WPV_PATH . $class );
				}

				$main_admin_class = 'YITH_Vendors_Admin';

				if ( class_exists( $main_admin_class . '_Premium' ) ) {
					$main_admin_class = $main_admin_class . '_Premium';
				}

				YITH_Vendors()->admin = new $main_admin_class();

				if ( 'yes' == get_option( 'yith_wpv_vendors_option_shipping_management' ) ) {

					add_filter( 'body_class', array( $this, 'vendor_body_class' ), 25 );

					$shipping_classes = array(
						'YITH_Vendor_Shipping_Admin'    => 'includes/shipping/class.yith-wcmv-shipping-admin.php',
						'YITH_Vendor_Shipping_Frontend' => 'includes/shipping/class.yith-wcmv-shipping-frontend.php',
						'YITH_Vendor_Shipping'          => 'includes/modules/module.yith-vendor-shipping.php'
					);

					foreach ( $shipping_classes as $class => $filename ) {
						if ( ! class_exists( $class ) && file_exists( YITH_WPV_PATH . $filename ) ) {
							require_once( YITH_WPV_PATH . $filename );
						}
					}
				}
			}

			if ( $this->current_user_is_vendor ) {
				$is_vendor_owner = get_current_user_id() == $this->vendor->get_owner();
				// Vendors admin limitation
				if ( ! $is_vendor_owner ) {
					// Allow commissions section only for vendor store owner
					add_filter( 'yith_wcfm_print_commissions_section', '__return_false' );
					add_filter( 'yith_wcfm_remove_commissions_menu_item', '__return_true' );
				}

				//Allow vendors to manage WooCommerce on front
				add_filter( 'yith_wcfm_access_capability', array( $this, 'allow_vendor_to_manage_store_on_front' ) );

				//Vendor can't manage taxonomy and we need to remove the add new tags and add new category button
				add_filter( 'yith_wcfm_show_add_new_product_taxonomy_term', '__return_false' );

				if ( ! is_admin() && function_exists( 'YITH_Vendor_Shipping' ) ) {

					YITH_Vendor_Shipping()->admin = new YITH_Vendor_Shipping_Admin();
					/* === Shipping Modules === */
					add_action( 'wp_enqueue_scripts', array( YITH_Vendor_Shipping()->admin, 'enqueue_scripts' ), 20 );
					add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_shipping_scripts' ), 10 );
					add_filter( 'yith_wcmv_is_shipping_tab', 'YITH_Frontend_Manager_For_Vendor::is_shipping_tab' );
				}

				/* === Manage Section === */
				add_filter( 'yith_wcfm_get_section_enabled_id_from_object', array( $this, 'get_section_enabled_id_from_object' ) );

				/* === Products === */
				add_action( 'init', array( $this, 'products_management' ), 20 );
				add_filter( 'yith_wcfm_premium_products_subsections', array( $this, 'prevent_vendor_edit_product_taxonomies' ), 10, 4 );

				/* === Coupons === */
				if ( ! is_admin() && ! class_exists( 'YITH_Vendor_Coupons' ) && YITH_Vendors()->is_wc_2_7_or_greather ) {
					$coupon_classes = array(
						YITH_WPV_PATH . 'includes/modules/coupons/abstract.module.yith-vendor-coupons.php',
						YITH_WPV_PATH . "includes/modules/coupons/module.yith-vendor-coupons.php"
					);

					foreach ( $coupon_classes as $class ) {
						if ( file_exists( $class ) ) {
							require_once( $class );
						}
					}
				}

				add_action( 'init', array( $this, 'coupons_management' ), 20 );

				/* === Orders === */
				add_action( 'init', array( $this, 'orders_management' ), 20 );
				add_filter( 'yith_wcfm_get_subsections_in_print_navigation', array( $this, 'orders_subsections' ), 10, 2 );

				/* === Vendor Panel: Search Customer ===  */
				add_action( 'wc_ajax_json_search_customers', 'YITH_Vendors_Admin::json_search_admins', 5 );

				/* === Vendors Reports === */
				add_filter( 'yith_wcfm_reports_subsections', array( $this, 'add_vendor_commissions_reports_subsections' ), 10, 2 );
				add_filter( 'yith_wcfm_orders_reports_type', array( $this, 'orders_reports_type' ) );
				add_filter( 'yith_wcfm_print_dashboard_section_args', array( $this, 'net_sales_this_month_hack' ) );

				/* === Dashboard === */
				add_filter( 'yith_wcfm_outofstock_count_transient', '__return_false' );
				add_filter( 'yith_wcfm_low_stock_count_transient', '__return_false' );
				add_filter( 'yith_wcfm_save_stock_transient', '__return_false' );
				add_filter( 'woocommerce_reports_get_order_report_query', array( $this, 'get_order_report_query_for_dashboard' ), 20 );
				add_action( 'yith_wcfm_dashboard_info', array( $this, 'show_unpaid_commissions' ) );
                add_filter( 'yith_wcfm_query_vars_for_product_query', array( $this, 'get_product_query_by_vendor' ), 10,2 );

				/* === My Account URL === */
				add_filter( 'yith_wcmv_my_vendor_dashboard_uri', 'yith_wcfm_get_main_page_url' );

				/* === Get Vendor Avatar === */
				add_filter( 'yith_wcfm_user_avatar', array( $this, 'get_vendor_avatar' ), 10, 2 );

				/* === Live Chat === */
				add_action( 'init', array( $this, 'live_chat_management' ), 20 );
				/* === SMS Notifications === */
				add_action( 'init', array( $this, 'sms_management' ), 20 );

				/* === Stripe Connect Support === */
				$stripe_connect = YITH_Vendors_Gateway( 'stripe-connect' );
				add_action( 'yith_wcmv_vendor_panel_payments', array( $stripe_connect, 'stripe_connect_account_page' ) );
				add_filter( 'yith_wcsc_connect_account_template_args', array( $this, 'stripe_connect_account_template_args' ) );
				add_filter( 'yith_wcsc_account_page_script_data', array( $this, 'stripe_connect_account_template_args' ) );

				/* === Privacy Policy and Terms and conditions revision === */
				add_action( 'yith_wcmf_before_print_section', array( $this, 'print_check_revision_message' ), 5 );



			} else { //WebSite Admin

				/* === Reports === */
				add_filter( 'yith_wcfm_reports_subsections', array( $this, 'add_vendor_reports_admin_subsections' ), 15, 2 );

				/* === Orders === */
				add_action( 'yith_wcfm_order_cols_suborder', array( YITH_Vendors()->orders, 'render_shop_order_columns' ), 10, 2 );

				/* === Create and Save Section Option for Vendor === */
				add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'admin_settings_sanitize_option' ), 10, 3 );
				add_action( 'woocommerce_admin_field_yith-wcfm-double-checkbox', 'yith_wcfm_double_checkbox', 10, 1 );
				add_filter( 'yith_wcfm_section_option_type', 'YITH_Frontend_Manager_For_Vendor::section_option_type', 10, 2 );
				add_filter( 'yith_wcfm_section_option_title', 'YITH_Frontend_Manager_For_Vendor::section_option_title', 10, 2 );

				/* === Products === */
				add_action( 'yith_wcfm_product_save', array( YITH_Vendors()->admin, 'save_product_commission_meta' ), 10, 2 );
				add_filter( 'yith_wcmv_single_product_commission_value_object', array( $this, 'single_product_commission_value_object' ) );
				add_filter( 'post_column_taxonomy_links', array( $this, 'post_column_taxonomy_links' ), 10, 3 );
				add_filter( 'yith_wcfm_products_list_query_args', array( $this, 'filter_product_list' ) );
			}

			/* === Register Style === */
			add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );

			/* === Both Actions for Admin and Vendors === */
			add_filter( 'yith_wcfm_print_shortcode_template_args', array( $this, 'orders_template_args' ) );

			/* === Reports === */
			add_action( 'yith_wcmv_after_setup', 'YITH_Frontend_Manager::regenerate_transient_rewrite_rule_transient' );

			$YITH_Vendors_Admin = YITH_Vendors()->admin;

			if ( empty( $YITH_Vendors_Admin ) ) {
				$YITH_Vendors_Admin_Class = $this->include_admin_class();
				$YITH_Vendors_Admin       = new $YITH_Vendors_Admin_Class();
			}

			/* === Products === */
			add_action( 'yith_wcfm_show_product_metaboxes', array( $this, 'show_vendor_admin_metaboxes' ), 20, 1 );
			add_action( 'yith_wcfm_product_save', array( $YITH_Vendors_Admin, 'add_vendor_taxonomy_to_product' ), 10, 2 );
			add_action( 'yith_wcfm_add_new_product', array( $YITH_Vendors_Admin, 'add_vendor_taxonomy_to_product' ), 10, 2 );
			add_action( 'yith_wcfm_after_product_duplicate', array( $YITH_Vendors_Admin, 'add_vendor_taxonomy_to_product' ), 10, 2 );

			if ( ! is_admin() && ! wp_doing_ajax() ) {
				/* === Reports === */
				add_filter( 'woocommerce_reports_get_order_report_data_args', array( $this, 'filter_dashboard_values' ) );

				remove_action( 'admin_menu', array( $YITH_Vendors_Admin, 'vendor_settings' ) );

				add_filter( 'yith_wcmv_edit_order_uri', array( $this, 'edit_order_uri' ), 10, 2 );
				add_filter( 'wp_count_posts', 'YITH_Vendors_Admin::vendor_count_posts', 10, 3 );

				/* === Orders === */
				add_filter( 'yith_wcmv_commissions_attribute_label_is_edit_order_page', array( $this, 'is_edit_order_page' ) );
				add_filter( 'yith_wcmv_commissions_attribute_label_order_object', array( $this, 'single_order_page_object' ) );

				/* === URI Management === */
				add_filter( 'yith_wcmv_commissions_list_table_commission_url', 'yith_wcfm_commission_url', 10, 2 );
				add_filter( 'yith_wcmv_commissions_list_table_product_url', 'yith_wcfm_product_url', 10, 3 );
				add_filter( 'yith_wcmv_commissions_list_table_order_url', 'yith_wcfm_order_url', 10, 3 );
				add_filter( 'yith_wcmv_commission_details_current_user_can_edit_users', '__return_false' );

				/* === Products === */
				remove_action( 'save_post', array( $YITH_Vendors_Admin, 'add_vendor_taxonomy_to_product' ), 10, 2 );
			}
		}

		/**
		 * is edit order page
		 *
		 * @since  1.0
		 * @return bool
		 */
		public function is_edit_order_page( $check ) {
			$obj = YITH_Frontend_Manager()->gui->get_current_section_obj();
			if ( ! empty( $obj ) && $obj->is_current( 'product_order' ) ) {
				$check = true;
			}

			return $check;
		}

		/**
		 * is edit order page
		 *
		 * @since  1.0
		 * @return bool
		 */
		public function single_order_page_object( $order ) {
			if ( ! empty( $_GET['id'] ) ) {
				$try_get_order = wc_get_order( $_GET['id'] );
				if ( $try_get_order instanceof WC_Order ) {
					$order = $try_get_order;
				}
			}

			return $order;
		}


		/**
		 * Main plugin Instance
		 *
		 * @static
		 * @return YITH_Frontend_Manager_For_Vendor Main instance
		 *
		 * @since  1.7
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Show vendor metabox on vedit product
		 *
		 * @since  1.0
		 * @return void
		 */
		public function show_vendor_admin_metaboxes( $_post = null ) {
			global $post;
			$old_post = $post;

			if ( ! empty( $_post ) ) {
				$post = $_post;
			}

			ob_start(); ?>

            <p class="form-field">
                <label>
					<?php echo YITH_Vendors()->get_vendors_taxonomy_label( 'singular_name' ); ?>
                </label>
				<?php
				$YITH_Vendors_Admin = ! empty( YITH_Vendors()->admin ) ? YITH_Vendors()->admin : new YITH_Vendors_Admin();
				$YITH_Vendors_Admin->single_taxonomy_meta_box( YITH_Vendors_Taxonomy::TAXONOMY_NAME, __return_empty_string() );
				?>
            </p>

			<?php
			echo ob_get_clean();

			$post = $old_post;

		}

		/**
		 * Filter dashboard value
		 *
		 * @param $args
		 *
		 * @return mixed
		 */
		public function filter_dashboard_values( $args ) {

			$current_section = YITH_Frontend_Manager()->gui->get_current_section_obj()->get_id();

			if ( 'dashboard' == $current_section ) {
				if ( $this->vendor->is_valid() && $this->vendor->has_limited_access() ) {
					$args['where'] = array(
						array(
							'key'      => 'posts.ID',
							'operator' => 'in',
							'value'    => $this->vendor->get_orders( 'all' )
						)
					);
				} elseif ( $this->vendor->is_super_user() ) {

					$args['where'] = array(
						array(
							'key'      => 'posts.post_parent',
							'operator' => '=',
							'value'    => 0
						)
					);
				}
			}

			return $args;
		}

		/**
		 * @param $type
		 *
		 * @return string
		 */
		public static function section_option_type( $type, $section_obj ) {
			/**
			 * APPLY_FILTERS: yith_wcmf_section_allowed_only_for_vendors
			 *
			 * Filters the section allowed for vendors.
			 *
			 * @param array $vendor_section The vendor sections.
			 * @return array
			 */
			$only_for_vendors = apply_filters( 'yith_wcmf_section_allowed_only_for_vendors', array( 'vendor-panel' ) );
			$type = ! empty( $section_obj ) && ! in_array( $section_obj->id, $only_for_vendors ) ? 'yith-wcfm-double-checkbox' : 'checkbox';

			return $type;
		}

		/**
		 * @param $type
		 *
		 * @return string
		 */
		public static function section_option_title( $title, $section_obj ) {
			/**
			 * APPLY_FILTERS: yith_wcmf_section_allowed_only_for_vendors
			 *
			 * Filters the section allowed for vendors.
			 *
			 * @param array $vendor_section The vendor sections.
			 * @return array
			 */
			$only_for_vendors = apply_filters( 'yith_wcmf_section_allowed_only_for_vendors', array( 'vendor-panel' ) );
			if ( ! empty( $section_obj ) && in_array( $section_obj->id, $only_for_vendors ) ) {
				$title = sprintf( '%s (%s)', $title, __( 'only available for vendors', 'yith-frontend-manager-for-woocommerce' ) );
			}

			return $title;
		}

		/**
		 * Register style and script
		 */
		public function register_scripts() {
			wp_register_style( 'jquery-chosen', YITH_WCFM_URL . 'plugin-fw/assets/css/chosen/chosen.css', array(), YITH_WPV_VERSION );
			wp_register_style( 'yith-wc-product-vendors-admin', YITH_WPV_ASSETS_URL . 'css/admin.css', array( 'jquery-chosen' ), YITH_WPV_VERSION );
		}

		/**
		 * Allow vendor to manager store on front
		 * if the current vendor is enabled
		 * (not pending account)
		 *
		 * @static
		 * @return string Vendor role
		 *
		 * @since  1.0
		 */
		public function allow_vendor_to_manage_store_on_front( $cap ) {
			$vendor_have_pending_account = $this->vendor->get_pending();
			if ( empty( $vendor_have_pending_account ) || 'no' == $this->vendor->get_pending() ) {
				$cap = YITH_Vendors()->get_role_name();
			}

			return $cap;
		}

		/**
		 * filter the section enabled id for vendor
		 *
		 * @param $option_id
		 *
		 * @return string  Vendor section enabled id
		 */
		public function get_section_enabled_id_from_object( $option_id ) {
			return ! empty( preg_match( '/_vendor$/', $option_id ) ) ? $option_id : $option_id . '_vendor';
		}


		/* === START PRODUCTS === */

		/**
		 * add products management action
		 *
		 * @since  1.0
		 * @return void
		 */
		public function products_management() {
			add_filter( 'yith_wcfm_products_list_query_args', array( $this, 'filter_product_list' ) );
			add_filter( 'clean_url', array( $this, 'change_blank_state_url' ), 10, 3 );
			add_filter( 'yith_wcfm_products_list_cols', array( $this, 'remove_vendor_col_in_product_list' ) );
			add_filter( 'yith_wcfm_print_product_section', array( $this, 'check_for_vendor_product' ), 10, 4 );
			add_filter( 'manage_product_posts_columns', array( $this, 'render_product_columns' ), 15 );
			add_filter( 'yith_wcfm_allowed_product_status', array( $this, 'allowed_product_status' ) );
			add_filter( 'yith_wcmv_add_vendor_taxonomy_to_product', array( $this, 'skip_add_vendor_taxonomy_to_product' ), 10, 3 );
			add_filter( 'yith_wcfm_skip_taxonomy', array( $this, 'skip_taxonomy_without_terms' ), 10, 2 );
            add_filter( 'yith_wcfm_get_subsections_in_print_navigation', array( $this, 'prevent_vendor_add_new_products' ),10,2 );
            add_filter( 'yith_wcfm_show_add_new_product_button', array( $this, 'hide_add_new_product' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'remove_import_products' ), 25 );
		}

		/**
		 * Add vendor tax query arg
		 *
		 * @return array query args
		 *
		 * @since  1.0
		 */
		public function filter_product_list( $query_args ) {
			$vendor          = $this->vendor;
			$vendor_taxonomy   = YITH_Vendors_Taxonomy::TAXONOMY_NAME;
			$vendor_query_args = array();
			if( ! $this->vendor->is_valid() && ! empty( $_GET[ $vendor_taxonomy ] ) ){
				$vendor = yith_get_vendor( sanitize_textarea_field( $_GET[ $vendor_taxonomy ] ), 'vendor' );
			}

			if( $vendor->is_valid() ){
				$vendor_query_args = $vendor->get_query_products_args();
				if ( ! empty( $vendor_query_args['tax_query'] ) ) {
					$query_args['tax_query'] = ! empty( $query_args['tax_query'] ) ? array_merge( $query_args['tax_query'], $vendor_query_args['tax_query'] ) : $vendor_query_args['tax_query'];
				}
			}

			return $query_args;
		}

		/**
		 * Add vendor tax query arg
		 *
		 * @return string product url
		 *
		 * @since  1.0
		 */
		public function change_blank_state_url( $good_protocol_url, $original_url, $_context ) {
			$product_blank_state_url = admin_url( 'post-new.php?post_type=product&tutorial=true' );
			if ( $original_url == $product_blank_state_url && is_admin() && ! YITH_Frontend_Manager()->is_admin ) {
				$sections             = YITH_Frontend_Manager()->gui->get_sections();
				$products_section_obj = ! empty( $sections['products'] ) ? $sections['products'] : null;
				if ( ! is_null( $products_section_obj ) ) {
					$good_protocol_url = $products_section_obj->get_url( 'product' );
				}
			}

			return $good_protocol_url;
		}

		/**
		 * Remove vendor taxonomies col in product list table
		 *
		 * @since  1.0
		 * @return array cols
		 */
		public function remove_vendor_col_in_product_list( $cols ) {
			if ( $this->vendor->is_valid() && isset( $cols['taxonomy-yith_shop_vendor'] ) ) {
				unset( $cols['taxonomy-yith_shop_vendor'] );
			}

			return $cols;
		}

		/**
		 * Remove taxonomies management for vendors
		 *
		 * @since  1.0
		 * @return array product sebsections
		 */
		public function prevent_vendor_edit_product_taxonomies( $subsections, $free_subsections, $premium_subsections, $obj ) {
			return $free_subsections;
		}

		/**
		 * Check if the current product are assign to vendor or not
		 * If not an error message shown
		 *
		 * @since  1.0
		 * @return bool $check = true if vendor can edit this product, not otherwise
		 */
		public function check_for_vendor_product( $check, $subsection, $section, $atts ) {

			if ( ! empty( $_GET['product_id'] ) && $this->vendor->is_valid() ) {
				$product_id = $_GET['product_id'];
				$vendor_id        = $this->vendor->id;
				$type             = apply_filters( 'wpml_element_type', YITH_Vendors_Taxonomy::TAXONOMY_NAME );
				$trid             = apply_filters( 'wpml_element_trid', null, $vendor_id, $type );
				$vendors          = apply_filters( 'wpml_get_element_translations', array(), $trid, $type );
				$current_language = apply_filters( 'wpml_current_language', '' );

				if ( ! empty( $vendors[ $current_language ] ) ) {
					$wpml_vendor_args = $vendors[ $current_language ];
					$vendor_id = $wpml_vendor_args->element_id;
				}

				$check = has_term( $vendor_id, YITH_Vendors_Taxonomy::TAXONOMY_NAME, $product_id );
			} else {
				if ( method_exists( YITH_Vendors()->admin, 'vendor_can_add_products' ) ) {
					$section = $atts['section_obj'];

					if ( array_key_exists( 'product', $section->get_current_subsection() ) ) {
						$check = YITH_Vendors()->admin->vendor_can_add_products( $this->vendor, 'product' );
						if ( ! $check ) {
							add_filter( "yith_wcfm_restricted_products_section_args", array( $this, 'product_amount_limit_message' ) );
						}
					}
				}
			}

			return $check;
		}

		public function product_amount_limit_message( $args ) {
			$products_limit        = get_option( 'yith_wpv_vendors_product_limit', 25 );
			$args['alert_message'] = sprintf( __( 'You are not allowed to create more than %1$s products', 'yith-frontend-manager-for-woocommerce' ), $products_limit );

			return $args;
		}

		/**
		 * Check for featured management
		 *
		 * Allowed or Disabled for vendor
		 *
		 * @since  1.3
		 *
		 * @param $columns The product column name
		 *
		 * @return array
		 */
		public function render_product_columns( $columns ) {
			if ( $this->vendor->is_valid() && $this->vendor->has_limited_access() && 'no' == $this->vendor->featured_products ) {
				unset( $columns['featured'] );
			}

			return $columns;
		}

		/**
		 * Get select for product status
		 *
		 * @since  1.0
		 * @return array product allowed status
		 */
		public function allowed_product_status( $product_status ) {
			$is_edit_product = ! empty( $_GET['product_id'] );
			$is_add_product  = empty( $_GET['product_id'] );
			$back_to_review  = 'yes' == get_option( 'yith_wpv_vendors_option_pending_post_status', 'no' );

			if ( ( $is_edit_product && $back_to_review ) || ( $is_add_product && 'no' == $this->vendor->skip_review ) ) {
				$not_allowed = array( 'publish' );
				foreach ( $not_allowed as $remove ) {
					if ( isset( $product_status[ $remove ] ) ) {
						unset( $product_status[ $remove ] );
					}
				}
			}

			return $product_status;
		}

		/**
		 * Get default product for single commission
		 *
		 * @since  1.0
		 * @return $post object
		 */
		public function single_product_commission_value_object( $_product ) {
			if ( ! empty( $_GET['product_id'] ) ) {
				$_product = wc_get_product( $_GET['product_id'] );
			}

			return $_product;
		}

		/**
		 * Skip add vendor to product if current user is a vendor
		 *
		 * @param $check
		 * @param $post
		 * @param $vendor
		 *
		 *
		 * @return bool
		 */
		public function skip_add_vendor_taxonomy_to_product( $check, $post, $vendor ) {
			if ( $check && ! empty( YITH_Frontend_Manager()->gui ) && ! YITH_Frontend_Manager()->gui->is_main_page() ) {
				$check = false;
			}

			return $check;
		}

		/**
		 * Skip show taxonomy boc for taxonomy without term vendor to product if current user is a vendor
		 *
		 * @param $skip     bool
		 * @param $tax_slug string taxonomy slug
		 *
		 *
		 * @return bool
		 */
		public function skip_taxonomy_without_terms( $skip, $tax_slug ) {
			$taxonomy_as_terms = wp_count_terms( $tax_slug );
			$skip              = $taxonomy_as_terms == 0 ? true : $skip;

			return $skip;
		}

		/**
		 * Remove Import products on Add product page for vendor
		 *
		 *
		 * @return void
		 */
		public function remove_import_products() {
			/* === Add Product Page === */
			wp_add_inline_style( 'yith-wc-product-vendors-admin', '.yith-wcfm-section-products .woocommerce-BlankState a.woocommerce-BlankState-cta.button:not(.button-primary){display:none !important;}' );
			wp_add_inline_script( 'yith-wpv-admin', "jQuery( '.woocommerce-BlankState' ).find( 'a.woocommerce-BlankState-cta.button' ).not( '.button-primary'  ).remove();" );
		}

		/**
		 * Filters the links in `$taxonomy` column of product table
		 *
		 * @param string[]  $term_links Array of term editing links.
		 * @param string    $taxonomy   Taxonomy name.
		 * @param WP_Term[] $terms      Array of term objects appearing in the post row.
		 *
		 * @return array Array of tem editing links
		 */
		public function post_column_taxonomy_links( $term_links, $taxonomy, $terms ){
			if( YITH_Vendors_Taxonomy::TAXONOMY_NAME === $taxonomy ){
				$temp_term_links = $term_links;
				foreach ( $temp_term_links as $k => $term_link ) {
					$search           = 'edit.php?post_type=product&#038;';
					$replace          = yith_wcfm_get_section_url( 'products' ) . '?';
					$term_links[ $k ] = str_replace( $search, $replace, $term_link );
				}
			}

			return $term_links;
		}

		/* === END PRODUCTS === */

		/* === START COUPONS === */

		/**
		 * add coupons management action
		 *
		 * @since  1.0
		 * @return void
		 */
		public function coupons_management() {
			add_filter( 'yith_wcfm_query_coupons_args', array( $this, 'filter_coupons_list' ) );
			add_filter( 'yith_wcfm_print_coupons_section', array( $this, 'check_for_vendor_coupon' ), 10, 4 );
			add_filter( 'yith_wcfm_coupons_args', array( $this, 'vendor_coupons_type' ), 99 );
			add_filter( 'yith_wcfm_print_section_path', array( $this, 'add_edit_coupon_template' ), 10, 3 );
			if ( class_exists( 'YITH_Vendor_Coupons' ) && method_exists( 'YITH_Vendor_Coupons', 'prevent_vendor_created_cart_percent_coupon' ) ) {
				add_action( 'yith_wcfm_coupon_updated', 'YITH_Vendor_Coupons::prevent_vendor_created_cart_percent_coupon', 99 );
			}
		}

		/**
		 * Only show current vendor's coupon
		 *
		 *
		 * @param  array $request Current request
		 *
		 * @return array Modified request
		 * @since  1.0
		 */
		public function filter_coupons_list( $args ) {
			if ( $this->vendor->is_valid() ) {
				$args['author__in'] = $this->vendor->admins;
			}

			return $args;
		}

		/**
		 *
		 */
		public function add_edit_coupon_template( $section, $subsection, $section_id ) {
			if ( 'coupons' == $section_id && 'coupon' == $subsection ) {
				$section = 'multi-vendor';
			}

			return $section;
		}

		/**
		 * Check if the current coupon are assign to vendor or not
		 * If not an error message shown
		 *
		 * @since  1.0
		 * @return bool $check = true if vendor can edit this product, not otherwise
		 */
		public function check_for_vendor_coupon( $check, $subsection, $section, $atts ) {
			if ( ! empty( $_GET['coupons'] ) && 'coupon' == $_GET['coupons'] && ! empty( $_GET['code'] ) && $this->vendor->is_valid() ) {
				global $wpdb;
				$coupon_code = $_GET['code'];
				$sql         = $wpdb->prepare( "SELECT post_author FROM $wpdb->posts WHERE post_title = %s AND post_type = 'shop_coupon' LIMIT 1;", $coupon_code );
				$post_author = $wpdb->get_var( $sql );
				$check       = in_array( $post_author, $this->vendor->get_admins() );
			}

			return $check;
		}

		/**
		 * Only show vendor coupon type
		 *
		 *
		 * @param  array $atts template args
		 *
		 * @return array $atts template args
		 * @since  1.0
		 */
		public function vendor_coupons_type( $atts ) {
			if ( ! empty( $atts['coupon_types'] ) ) {
				$to_disabled = YITH_Vendors()->is_wc_2_7_or_greather ? array( 'fixed_cart' ) : array( 'percent', 'fixed_cart' );
				foreach ( $to_disabled as $disabled ) {
					if ( isset( $atts['coupon_types'][ $disabled ] ) ) {
						unset( $atts['coupon_types'][ $disabled ] );
					}
				}
			}

			return $atts;
		}

		/* === END COUPONS === */

		/* === START ORDERS === */

		/**
		 * add products management action
		 *
		 * @since  1.0
		 * @return void
		 */
		public function orders_management() {
			add_filter( 'yith_wcfm_print_orders_section', array( $this, 'check_for_vendor_order' ), 10, 4 );
			add_filter( 'yith_wcmv_is_vendor_order_details_page', array( $this, 'is_vendor_order_details_page' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'allow_vendor_to_manage_refunds' ), 20 );
			add_filter( 'yith_wcmv_print_orders_list_shortcode', array( $this, 'vendors_orders_list' ), 99, 2 );

			if ( 'no' == get_option( 'yith_wpv_vendors_option_order_management', 'no' ) ) {
				$ajax_events = array(
					'add_order_note'    => false,
					'delete_order_note' => false,
				);

				foreach ( $ajax_events as $ajax_event => $nopriv ) {
					add_action( "wp_ajax_woocommerce_{$ajax_event}", array( __CLASS__, $ajax_event ), 4 );
					$nopriv && add_action( "wp_ajax_nopriv_woocommerce_{$ajax_event}", array( __CLASS__, $ajax_event, 3 ) );
				}
			}

			if ( 'yes' == get_option( 'yith_wpv_vendors_option_order_hide_shipping_billing', 'no' ) ) {
				add_filter( 'yith_wcfm_save_billing_and_shipping_address', '__return_false' );
			}
		}

		/**
		 * Add order note via ajax to vendor's order
		 * if vendor haven't order capabilities
		 *
		 * @since  1.1.1
		 * @return void
		 */
		public static function add_order_note() {
			check_ajax_referer( 'add-order-note', 'security' );

			$post_id   = absint( $_REQUEST['post_id'] );
			$note      = wp_kses_post( trim( stripslashes( $_POST['note'] ) ) );
			$note_type = $_POST['note_type'];

			$is_customer_note = ( 'customer' === $note_type ) ? 1 : 0;

			if ( $post_id > 0 ) {
				$order      = wc_get_order( $post_id );
				$comment_id = $order->add_order_note( $note, $is_customer_note, true );

				echo '<li rel="' . esc_attr( $comment_id ) . '" class="note ';
				if ( $is_customer_note ) {
					echo 'customer-note';
				}
				echo '"><div class="note_content">';
				echo wpautop( wptexturize( $note ) );
				echo '</div><p class="meta"><a href="#" class="delete_note">' . __( 'Delete note', 'woocommerce' ) . '</a></p>';
				echo '</li>';
			}
			wp_die();
		}

		/**
		 * Delete order note via ajax to vendor's order
		 * if vendor haven't order capabilities
		 *
		 * @since  1.1.1
		 * @return void
		 */
		public static function delete_order_note() {
			check_ajax_referer( 'delete-order-note', 'security' );

			$note_id = (int) $_POST['note_id'];

			if ( $note_id > 0 ) {
				wp_delete_comment( $note_id );
			}
			wp_die();
		}

		/**
		 * check if vendor can manage refunds or not
		 *
		 * @since  1.0
		 * @return void
		 */
		public function allow_vendor_to_manage_refunds() {
			if ( $this->current_user_is_vendor && YITH_Vendors()->orders->is_vendor_order_details_page() ) {
				$refund_management = 'yes' == get_option( 'yith_wpv_vendors_option_order_refund_synchronization', 'no' );
				if ( ! $refund_management ) {
					$js = "jQuery( 'button.refund-items' ).remove();";
					wp_add_inline_script( 'yith-frontend-manager-order-js', $js );
				}
			}
		}

		/**
		 * Remove add order cap for vendors
		 *
		 * @since  1.0
		 * @return Orders section for vendors
		 */
		public function orders_subsections( $subsections, $section ) {
			if ( YITH_Frontend_Manager()->gui->get_section( 'product_orders' ) == $section ) {
				unset( $subsections['product_order'] );
			}

			return $subsections;
		}

		/**
		 * Check if the current page is an order tails page
		 * if yes add script to remove the payments and customer infotmation
		 * for vendors, if options is enabled
		 *
		 * @param $check
		 *
		 * @since  1.0
		 * @return bool yes if it0s an order details page on front, false otherwise
		 */
		public function is_vendor_order_details_page( $check ) {
			if ( YITH_Frontend_Manager()->gui ) {
				$current_section = YITH_Frontend_Manager()->gui->get_current_section_obj();
				if ( $current_section instanceof YITH_Frontend_Manager_Section && $current_section->is_current() && ! empty( $_GET['id'] ) ) {
					$check = true;
				}
			}

			return $check;
		}

		/**
		 * Check if the current order are assign to vendor or not
		 * If not an error message shown
		 *
		 * @since  1.0
		 * @return bool $check = true if vendor can edit this product, not otherwise
		 */
		public function check_for_vendor_order( $check, $subsection, $section, $atts ) {
			if ( ! empty( $_GET['product_orders'] ) && 'product_order' == $_GET['product_orders'] && ! empty( $_GET['id'] ) && $this->vendor->is_valid() ) {
				$check = in_array( $_GET['id'], $this->vendor->get_orders() );
			}

			return $check;
		}

		/**
		 * Suborders cols in orders list
		 */
		public function show_suborders_in_list( $column, $order ) {
			$suborder_ids = YITH_Orders::get_suborder( $order->id );
			if ( $suborder_ids ) {
				foreach ( $suborder_ids as $suborder_id ) {
					$suborder  = wc_get_order( $suborder_id );
					$vendor    = yith_get_vendor( $suborder->post->post_author, 'user' );
					$order_uri = esc_url( 'post.php?post=' . absint( $suborder_id ) . '&action=edit' );

					printf( '<mark class="%s tips" data-tip="%s">%s</mark> <strong><a href="%s">#%s</a></strong> <small class="yith-wcmv-suborder-owner">(%s %s)</small>',
					        sanitize_title( $suborder->get_status() ),
					        wc_get_order_status_name( $suborder->get_status() ),
					        wc_get_order_status_name( $suborder->get_status() ),
					        $order_uri,
					        $suborder_id,
					        _x( 'in', 'Order table details', 'yith-frontend-manager-for-woocommerce' ),
					        $vendor->name
					);

					do_action( 'yith_wcmv_after_suborder_details', $suborder );
				}
			} else {
				echo '<span class="na">&ndash;</span>';
			}
		}

		/**
		 * Orders template args
		 *
		 * @param $args
		 *
		 * @since  1.0
		 * @return array template args
		 */
		public function orders_template_args( $args ) {
			if ( ! $this->current_user_is_vendor ) {
				/* Add suborder col */
				$new_cols = array( 'suborder' => __( 'Suborders', 'yith-frontend-manager-for-woocommerce' ) );

				$cols           = $args['columns'];
				$orders_col_pos = array_search( 'order', array_keys( $cols ) );
				$first          = array_slice( $cols, 0, $orders_col_pos + 1, true );
				$last           = array_slice( $cols, $orders_col_pos + 1, count( $cols ), true );
				$cols           = array_merge( $first, $new_cols, $last );

				$args['columns'] = $cols;

				/* Filter orders list */
				$args['query_args']['post_parent'] = 0;
			} else {
				$vendor_suborders                  = $this->vendor->get_orders( 'suborder' );
				$args['query_args']['post_status'] = array_keys( wc_get_order_statuses() );
				$args['query_args']['post__in']    = empty( $vendor_suborders ) ? array( - 1 ) : $vendor_suborders;
			}

			return $args;
		}

		/**
		 * Retreive the vendor orders list
		 *
		 * @param $orders
		 * @param $args
		 *
		 * @since  1.0
		 * @return $orders order object array
		 */
		public function vendors_orders_list( $orders, $args ) {
            if ( $this->current_user_is_vendor ) {
                if( ! empty( $_GET['order_status'] ) ){
                    $args['post_status'] = array( esc_html( $_GET['order_status'] ) );
                }
                $order_ids = get_posts( $args );
                if ( ! empty( $order_ids ) ) {
                    $orders = array();
                    foreach ( $order_ids as $order_id ) {
                        $orders[] = $order_id ;
                    }
                }
            }

            return $orders;
        }

		/**
		 * Hack edit suborder uri
		 *
		 * @param $uri
		 * @param $order_id
		 *
		 * @return mixed
		 */
		public function edit_order_uri( $uri, $order_id ) {
			if ( ! empty( YITH_Frontend_Manager()->gui ) ) {
				$sections       = YITH_Frontend_Manager()->gui->get_sections();
				$orders_section = ! empty( $sections['product_orders'] ) ? $sections['product_orders'] : null;

				if ( $orders_section ) {
					$uri = $orders_section::get_edit_order_permalink( $order_id );
				}
			}

			return $uri;
		}

		/* === END ORDERS === */

		/* === START REPORTS === */

		/**
		 * hide coupon usage report for vendors
		 *
		 * @since  1.0
		 * @return array  report subsections
		 */
		public function orders_reports_type( $types ) {
			if ( isset( $types['coupon_usage'] ) ) {
				unset( $types['coupon_usage'] );
			}

			return $types;
		}

		/**
		 * add reports management action
		 *
		 * @since  1.0
		 * @return mixed commissions report subsections
		 */
		public function add_vendor_commissions_reports_subsections( $subsections, $obj ) {
			$new_subsection = array(
				'commissions-report' => array(
					'slug' => $obj->get_option( 'slug', $obj->id . '_commissions-report', 'commissions-report' ),
					'name' => __( 'Commissions', 'yith-frontend-manager-for-woocommerce' )
				),
			);

			if ( $this->current_user_is_vendor && ! is_admin() ) {
				unset( $subsections['customers-report'] );
			}

			return array_merge( $subsections, $new_subsection );
		}

		/**
		 * Add vendor reports for admin
		 *
		 * @param $subsections
		 * @param $obj
		 *
		 * @return array subsections
		 */
		public function add_vendor_reports_admin_subsections( $subsections, $obj ) {
			$new_subsection = array(
				'vendors' => array(
					'slug' => $obj->get_option( 'vendors', $obj->id . '_vendors', 'vendors' ),
					'name' => YITH_Vendors()->get_vendors_taxonomy_label( 'menu_name' )
				),
			);

			if ( ! $this->current_user_is_vendor && ! is_admin() ) {
				unset( $subsections['commissions'] );
			}

			return array_merge( $subsections, $new_subsection );
		}

		/**
		 * Change the net sales amount for vendors
		 *
		 * @param $args
		 *
		 * @return array reports args
		 */
		public function net_sales_this_month_hack( $args ) {
			if ( $this->current_user_is_vendor ) {
				$report_class = YITH_WPV_PATH . 'includes/reports/class.yith-report-sales-by-date.php';

				if ( ! class_exists( 'YITH_Report_Sales_By_Date' ) && file_exists( $report_class ) ) {
					require_once $report_class;
				}

				if ( class_exists( 'YITH_Report_Sales_By_Date' ) ) {
					$reports = new YITH_Report_Sales_By_Date();
					$reports->calculate_current_range( 'month' );
					$args['report_data']->net_sales = $orders_net_amount = $reports->get_report_data()->orders_net_amount;
					$args                           = $this->dashboard_section_args( $args );
				}
			}

			return $args;
		}

		/* === END REPORTS === */

		/**
		 * Sanitize value for double checkbox option type
		 *
		 *
		 * @since  1.0
		 *
		 * @param $value
		 * @param $option
		 * @param $raw_value
		 *
		 * @return string
		 */
		public function admin_settings_sanitize_option( $value, $option, $raw_value ) {
			if ( 'yith-wcfm-double-checkbox' == $option['type'] ) {
				$value                 = is_null( $raw_value ) ? 'no' : 'yes';
				$vendor_section_option = $option['id'] . '_vendor';
				$vendor_raw_value      = isset( $_POST[ $vendor_section_option ] ) ? wp_unslash( $_POST[ $vendor_section_option ] ) : null;
				$vendor_value          = is_null( $vendor_raw_value ) ? 'no' : 'yes';
				update_option( $vendor_section_option, $vendor_value );
			}

			return $value;
		}

		/**
		 * include admin class
		 *
		 * @since  1.0
		 * @return string classname
		 */
		public function include_admin_class() {
			$classname = 'YITH_Vendors_Admin';
			if ( ! class_exists( 'YITH_Vendors_Admin' ) ) {
				$admin_class = YITH_WPV_PATH . 'includes/admin/class.yith-vendors-admin.php';
				if ( file_exists( $admin_class ) ) {
					require_once( $admin_class );
				}

				$admin_premium_class = YITH_WPV_PATH . 'includes/admin/class.yith-vendors-admin-premium.php';
				if ( file_exists( $admin_premium_class ) ) {
					require_once( $admin_premium_class );
					$classname = 'YITH_Vendors_Admin_Premium';
				}
			}

			return trim( $classname );
		}

		/**
		 * Check if the current vendor show shipping tabs
		 *
		 * @since  1.0
		 * @return bool true if the current page is the shipping tab, false otherwise
		 */
		public static function is_shipping_tab( $is_shipping_tab ) {
			if ( ! is_admin() && ! wp_doing_ajax() ) {
				$is_shipping_tab = ! empty( $_GET['page'] ) && YITH_Vendors()->admin->vendor_panel_page == $_GET['page'] && ! empty( $_GET['tab'] ) && 'vendor-shipping' == $_GET['tab'];
			}

			return $is_shipping_tab;
		}

		/**
		 * Load the WooCommerce admin scripts for shipping method
		 *
		 * @since  1.0.11
		 * @return void
		 */
		public function enqueue_shipping_scripts() {
			if ( self::is_shipping_tab( false ) ) {
				wp_enqueue_style( 'woocommerce_admin_styles' );
				wp_enqueue_style( 'select2' );

				if ( wp_script_is( 'wc-enhanced-select' ) ) {
					wp_deregister_script( 'wc-enhanced-select' );
				}
			}
		}

		/**
		 * Add vendor body class
		 *
		 * @since  1.0.11
		 * @return array body classes
		 */
		public function vendor_body_class( $classes ) {
			if ( self::is_shipping_tab( false ) && $this->vendor->is_valid() && $this->vendor->has_limited_access() ) {
				$classes[] = 'vendor_limited_access';
			}

			return $classes;
		}

		/**
		 * Get the vendor avatar image
		 *
		 *
		 * @param $user_avatar
		 * @param $avatar_size
		 *
		 * @since  1.0.11
		 * @return string avatar image
		 */
		public function get_vendor_avatar( $user_avatar, $avatar_size ) {
			if ( ! empty( $this->vendor->avatar ) ) {
				$user_avatar = wp_get_attachment_image( $this->vendor->avatar, array( $avatar_size, $avatar_size ), false, array( 'class' => 'avatar' ) );
			}

			return $user_avatar;
		}

		/**
		 * Filter the report query for vendor
		 *
		 * @param $query
		 *
		 * @return array body classes
		 * @since  1.0.15
		 */
		public function get_order_report_query_for_dashboard( $query ) {
			$orders = $this->vendor->get_orders();

			if ( empty( $orders ) ) {
				$query['where'] .= " AND posts.ID in (-1)";
			}

			return $query;
		}

		/**
		 * Add unpaid commissions box
		 *
		 * @return void
		 * @since  1.5.5
		 */
		public function show_unpaid_commissions() {
			if ( $this->vendor->is_valid() ) {
				ob_start();
				?>
                <li id="yith-wcfm-dashboard-unpaid-commissions">
                    <span class="dashicons dashicons-chart-bar"></span>
					<?php _e( 'Unpaid commissions', 'yith-woocommerce-product-vendors' ); ?>
                    <strong><?php echo wc_price( $this->vendor->get_unpaid_commissions_amount() ); ?></strong>
                </li>
				<?php
				echo ob_get_clean();
			}
		}

		/**
		 * Change the label from Net sales this month to Net Commissions this month
		 *
		 * @param $args array Dashboard template args
		 *
		 * @return array $args
		 * @since  1.1.1
		 */
		public function dashboard_section_args( $args ) {
			$args['labels']['net_sales'] = __( 'Net commissions this month', 'yith-frontend-manager-for-woocommerce' );

			return $args;
		}

		/**
		 * add live chat management action
		 *
		 * @since  1.0
		 * @return void
		 */
		public function live_chat_management() {
			add_filter( 'yith_wcfm_ylc_macro_list_query_args', array( $this, 'filter_product_list' ) );
			add_filter( 'yith_wcfm_print_live_chat_section', array( $this, 'check_for_vendor_chat' ), 10 );

		}

		/**
		 * add live chat management action
		 *
		 * @since  1.0
		 * @return void
		 */
		public function sms_management() {
			add_filter( 'yith_wcfm_print_sms_section', array( $this, 'check_for_vendor_sms' ), 10 );

		}

		/**
		 * check if live chat is enabled for vendors
		 *
		 * @since  1.0
		 * @return boolean
		 */
		public function check_for_vendor_chat() {
			return version_compare( YLC_VERSION, '1.4.0', '<' ) ? YITH_Live_Chat()->multivendor_check() : ylc_multivendor_check();
		}

		/**
		 * check if sms are enabled for vendors
		 *
		 * @since  1.0
		 * @return boolean
		 */
		public function check_for_vendor_sms() {
			return ( get_option( 'yith_wpv_vendors_enable_sms' ) == 'yes' );
		}

		/**
		 * Remove "New product" subsection for vendors if the restriction is valid
		 *
		 * @param array $subsections Dashboard subsections
		 * @param string $section Currenct section
		 *
		 * @return array mixed Menu subsections
		 */
		public function prevent_vendor_add_new_products( $subsections, $section ){
			if ( method_exists( YITH_Vendors()->admin, 'vendor_can_add_products' ) ) {
				$check = $this->vendor->can_add_products();
				if ( ! $check && isset( $subsections['product'] ) ) {
					unset( $subsections['product'] );
				}
			}
			return $subsections;
		}


		/**
		 * Hide "Add new product" button on top of the page if the restrcition is valid
		 *
		 * @param bool $show Show/hide the button
		 *
		 * @return bool
		 */
		public function hide_add_new_product( $show ){
			if ( method_exists( YITH_Vendors()->admin, 'vendor_can_add_products' ) ) {
				$check = $this->vendor->can_add_products();
				if ( ! $check ) {
					$show = false;
				}
			}
			return $show;
		}

		/**
		 * Filter the account connection template args
		 *
		 * @since  2.6.0
		 * @return array template arguments
		 */
		public function stripe_connect_account_template_args( $args ) {
			if ( $this->current_user_is_vendor && ! empty( YITH_Frontend_Manager()->gui ) && YITH_Frontend_Manager()->gui->is_main_page() && $this->vendor->is_valid() && $this->vendor->has_limited_access() ) {
				$OAuth_link                = add_query_arg( array( 'redirect_uri' => yith_wcfm_get_stripe_redirect_uri_for_vendors( true ) ), $args['oauth_link'] );
				$args['oauth_link']        = $OAuth_link;
				$args['count_commissions'] = 0;
			}

			return $args;
		}

		/**
		 * print the error message on admin area for vendors
		 * @throws Exception
		 */
		public function print_check_revision_message() {
			$message = YITH_Vendors()->admin->get_revision_message();
			if ( ! empty( $message ) ) {
				wc_print_notice( $message, 'error' );
			}
		}
        /**
         * Include specific vendor on product query.
         * @param array $query - Args for WP_Query.
         * @param array $query_vars - Query vars from WC_Product_Query.
         * @return array modified $query
         */
        public function get_product_query_by_vendor( $query, $query_args ) {

            if( $this->current_user_is_vendor ) {
                $query['tax_query'][] = array(
                    'taxonomy' => YITH_Vendors_Taxonomy::TAXONOMY_NAME,
                    'field'    => 'term_id',
                    'operator' => 'IN',
                    'terms'    => $this->vendor->get_id(),
                );
            }

            return $query;
        }
	}
}

/**
 * Main instance of plugin
 *
 * @return /YITH_Frontend_Manager_For_Vendor
 * @since  1.9
 */
if ( ! function_exists( 'YITH_Frontend_Manager_For_Vendor' ) ) {
	function YITH_Frontend_Manager_For_Vendor() {
		return YITH_Frontend_Manager_For_Vendor::instance();
	}
}
