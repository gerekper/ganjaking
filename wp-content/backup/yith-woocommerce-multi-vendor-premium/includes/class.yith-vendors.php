<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WPV_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Vendors
 * @package    Yithemes
 * @since      Version 2.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_Vendors' ) ) {
	/**
	 * Class YITH_Vendors
	 *
	 * @author Andrea Grillo <andrea.grillo@yithemes.com>
	 */
	class YITH_Vendors {

		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0
		 */
		public $version = YITH_WPV_VERSION;

		/**
		 * Taxonomy Name
		 *
		 * @var string
		 * @since 1.0
		 * @access protected
		 */
		protected $_taxonomy_name = 'yith_shop_vendor';

		/**
		 * User Meta Key
		 *
		 * @var string
		 * @since 1.0
		 * @access protected
		 */
		protected $_user_meta_key = 'yith_product_vendor';

		/**
		 * User Meta Key
		 *
		 * @var string
		 * @since 1.0
		 * @access protected
		 */
		protected $_user_meta_owner = 'yith_product_vendor_owner';

		/**
		 * Main Instance
		 *
		 * @var string
		 * @since 1.0
		 * @access protected
		 */
		protected static $_instance = null;

		/**
		 * Main Admin Instance
		 *
		 * @var YITH_Vendors_Admin | YITH_Vendors_Admin_Premium
		 * @since 1.0
		 */
		public $admin = null;

        /**
		 * Main Frontpage Instance
		 *
		 * @var YITH_Vendors_Frontend | YITH_Vendors_Frontend_Premium
		 * @since 1.0
		 */
		public $frontend = null;

         /**
		 * Main Orders Instance
		 *
		 * @var YITH_Orders | YITH_Orders_Premium
		 * @since 1.0
		 */
		public $orders = null;

		/**
		 * Main Shipping Class
		 *
		 * @var YITH_Vendor_Shipping
		 * @since 1.9.17
		 */
		public $shipping = null;

		/**
		 * Required classes
		 *
		 * @var YITH_Orders | YITH_Orders_Premium
		 * @since 1.0
		 */
		public $require = array();

		/**
		 * termmeta table
		 *
		 * The table woocommerce_termmeta was removed in WooCommerce 2.6
		 * use termmeta table instead
		 *
		 * @var string
		 * @since 1.9.8
		 */
		public $termmeta_table = '';

		/**
		 * termmeta term_id
		 *
		 * The table woocommerce_termmeta was removed in WooCommerce 2.6
		 * use termmeta table instead, use term_id field instead of woocommerce_term_id
		 *
		 *
		 * @var string
		 * @since 1.9.8
		 */
		public $termmeta_term_id = 'term_id';

		/**
		 * Check if WooCommerce is lower of 2.6
		 *
		 * @var string
		 * @since 1.9.8
		 */
		public $is_wc_lower_2_6;

		/**
		 * Check if WooCommerce run version 2.6.x
		 *
		 * @var string
		 * @since 1.9.8
		 */
		public $is_wc_2_6;

        /**
         * Check if WooCommerce run version 2.7 or greater
         *
         * @var string
         * @since 1.9.8
         */
        public $is_wc_2_7_or_greather;

		/**
		 * Check if WooCommerce run version 3.2 or greater
		 *
		 * @var string
		 * @since 1.9.8
		 */
		public $is_wc_3_2_or_greather;

		/**
		 * Check if WooCommerce run version 3.3 or greater
		 *
		 * @var string
		 * @since 1.9.8
		 */
		public $is_wc_3_3_or_greather;

		/**
		 * Check if WooCommerce run version 3.7 or greater
		 *
		 * @var string
		 * @since 1.9.8
		 */
		public $is_wc_3_7_or_greather;

		/**
		 * Check if WordPress run version 4.9.6 or greater
		 *
		 * @var string
		 * @since 1.9.8
		 */
		public $is_wp_4_9_6_or_greater;

		/**
		 * Role Name
		 *
		 * @var string
		 * @since 1.7
		 */
		protected static $_role_name = 'yith_vendor';

		/**
		 * Vendor Singular label
		 *
		 * @var string
		 * @since 3.2.14
		 */
		protected $_singular_label = '';

		/**
		 * Vendor Plural label
		 *
		 * @var string
		 * @since 3.2.14
		 */
		protected $_plural_label = '';


		/**
		 * Constructor
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return mixed|YITH_Vendors
		 * @since  1.0.0
		 * @access public
		 */
		public function __construct() {
			/* === Set plural and Singular for vendor/vendors labels === */
			$singular_label = get_option( 'yith_wpv_vendor_label_singular_text', _x( "Vendor", 'default singular vendor label', 'yith-woocommerce-product-vendors' ) );
			$plural_label   = get_option( 'yith_wpv_vendor_label_plural_text', _x( "Vendors", 'default plural vendors label', 'yith-woocommerce-product-vendors' ) );
			$this->set_singular_label( $singular_label );
			$this->set_plural_label( $plural_label );

			/* === Main Classes to Load === */
			$this->require = apply_filters( 'yith_wcpv_require_class',
				array(
					'common' => array(
                        'includes/functions.yith-update.php',
                        'includes/functions.yith-vendors.php',
						'includes/class.yith-vendor.php',
						'includes/class.yith-commission.php',
						'includes/class.yith-commissions.php',
						'includes/class.yith-vendors-frontend.php',
                        'includes/class.yith-orders.php',
						'includes/class.yith-reports-analytics.php',
						'includes/lib/class.yith-walker-category-dropdown.php',
						'includes/widgets/class.yith-woocommerce-vendors-widget.php',
					),
					'admin' => array(
						'includes/class.yith-vendors-admin.php',
						'includes/class.yith-vendors-privacy.php',
					)
				)
			);

			$wc_version = WC()->version;

			global $wp_version;
			$this->is_wp_4_9_6_or_greater   = version_compare( $wp_version, '4.9.6', '>=' );
			$this->is_wc_lower_2_6          = version_compare( $wc_version, '2.6', '<' );
			$this->is_wc_2_7_or_greather    = version_compare( $wc_version, '2.7', '>=' );
			$this->is_wc_3_2_or_greather    = version_compare( $wc_version, '3.2', '>=' );
			$this->is_wc_3_3_or_greather    = version_compare( $wc_version, '3.3', '>=' );
			$this->is_wc_3_7_or_greather    = version_compare( $wc_version, '3.7', '>=' );
			$this->is_wc_2_6				= ! $this->is_wc_lower_2_6 && ! $this->is_wc_2_7_or_greather;

			/* Check for WooCommerce Version */
			add_action( 'init', array( $this, 'select_termmeta_table' ) );

			$this->_require( $this->require );

            $this->_add_plugin_image_size();

			/* === START Hooks === */

			/* plugins loaded */
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

			/* init */
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'init', array( $this, 'register_vendors_taxonomy' ), 5 );
			add_action( 'init', array( $this, 'flush_rewrite_rules' ), 20 );

			/* widget */
			add_action( 'widgets_init', array( $this, 'widgets_init' ) );

			/* === END Hooks === */
		}

		/**
		 * Main plugin Instance
		 *
		 * @return YITH_Vendors Main instance
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
        public static function instance() {

            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            if ( is_null( $self::$_instance ) ) {
                $self::$_instance = new $self;
            }

            return $self::$_instance;
        }

		/**
		 * Class Initializzation
		 *
		 * Instance the admin or frontend classes
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0
		 * @return void
		 * @access protected
		 */
		public function init() {
			if ( is_admin() ) {
				if( ! is_ajax() ){
					$this->admin   = new YITH_Vendors_Admin();
				}
			}

			if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				$this->frontend = new YITH_Vendors_Frontend();
			}

            $this->orders = new YITH_Orders();
		}

		/**
		 * Add the main classes file
		 *
		 * Include the admin and frontend classes
		 *
		 * @param $main_classes array The require classes file path
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0
		 *
		 * @return void
		 * @access protected
		 */
		protected function _require( $main_classes ) {
			foreach ( $main_classes as $section => $classes ) {
				foreach ( $classes as $class ) {
					if ( ( 'common' == $section || ( 'frontend' == $section && ! is_admin() ) || ( 'admin' == $section && is_admin() ) ) && file_exists( YITH_WPV_PATH . $class ) ) {
						require_once( YITH_WPV_PATH . $class );
					}
				}
			}
		}

		/**
		 * Load plugin framework
		 *
		 * @author Andrea Gr  illo <andrea.grillo@yithemes.com>
		 * @since  1.0
		 * @return void
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}

		/**
		 * Get the protected attribute taxonomy name
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0.0
		 * @return string The taxonomy name
		 */
		public function get_taxonomy_name() {
			return $this->_taxonomy_name;
		}

		/**
		 * Register taxonomy for vendors
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0
		 * @return void
		 */
		public function register_vendors_taxonomy() {
			$slug = get_option( 'yith_wpv_vendor_taxonomy_rewrite', 'vendor' );
			$args = apply_filters( 'yith_wcmv_vendor_taxonomy_args', array(
					'public'            => true,
					'hierarchical'      => false,
					'show_admin_column' => true,
					'show_in_menu' 		=> false,
					'show_in_nav_menus'	=> true,
					'labels'            => $this->get_vendors_taxonomy_label(),
					'rewrite'           => array( 'slug' => ! empty( $slug ) ? $slug : 'vendor' ),
					"meta_box_cb"       => 'YITH_Vendors_Admin::single_taxonomy_meta_box',
				)
			);
			$taxonomies_object_type = apply_filters( 'yith_wcmv_register_taxonomy_object_type', array( 'product' ) );
			register_taxonomy( $this->_taxonomy_name, $taxonomies_object_type, $args );
			foreach( $taxonomies_object_type as $taxonomy_object_type ){
				register_taxonomy_for_object_type( $this->_taxonomy_name, $taxonomy_object_type );
			}

			if( ! get_option( 'yith_wcmv_setup' ) ){
				self::setup( 'add_role' );
				add_option( 'yith_wcmv_setup', 1 );
			}
		}

		/**
		 * Get the vendors taxonomy label
		 *
		 * @param        $arg string The string to return. Defaul empty. If is empty return all taxonomy labels
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0.0
		 *
		 * @return Array The taxonomy label
		 * @fire yith_product_vendors_taxonomy_label hooks
		 */
		public function get_vendors_taxonomy_label( $arg = '' ) {
			$vendor_singular_label = $this->get_singular_label();
			$vendor_plural_label   = $this->get_plural_label();

			$label = apply_filters( 'yith_product_vendors_taxonomy_label', array(
					'name'                       => $vendor_singular_label,
					'singular_name'              => $vendor_singular_label,
					'menu_name'                  => $vendor_plural_label,
					'search_items'               => sprintf( '%s %s', _x( 'Search', '[Part of] Search Vendors', 'yith-woocommerce-product-vendors' ), $vendor_plural_label ),
					'all_items'                  => sprintf( '%s %s', _x( 'All', '[Part of] All Vendors', 'yith-woocommerce-product-vendors' ), $vendor_plural_label ),
					'parent_item'                => sprintf( '%s %s', _x( 'Parent', '[Part of] Parent Vendor','yith-woocommerce-product-vendors' ), $vendor_singular_label ),
					'parent_item_colon'          => sprintf( '%s %s:', _x( 'Parent', '[Part of] Parent Vendor','yith-woocommerce-product-vendors' ), $vendor_singular_label ),
					'view_item'                  => sprintf( '%s %s', _x( 'View', '[Part of] View Vendor','yith-woocommerce-product-vendors' ), $vendor_singular_label ),
					'edit_item'                  => sprintf( '%s %s', _x( 'Edit', '[Part of] Edit Vendor','yith-woocommerce-product-vendors' ), $vendor_singular_label ),
					'update_item'                => sprintf( '%s %s', _x( 'Update', '[Part of] Update Vendor','yith-woocommerce-product-vendors' ), $vendor_singular_label ),
					'add_new_item'               => sprintf( '%s %s', _x( 'Add New', '[Part of] Add New Vendor','yith-woocommerce-product-vendors' ), $vendor_singular_label ),
					'new_item_name'              => sprintf( "%s %s's %s", _x( "New", "[Part of] New Vendor's Name",'yith-woocommerce-product-vendors' ), $vendor_singular_label, _x( "New", "[Part of] New Vendor's Name",'yith-woocommerce-product-vendors' ) ),
					'popular_items'              => null, //don't remove!
					'separate_items_with_commas' => sprintf( "%s %s %s", _x( "Separate", "[Part of] Separate vendors with commas",'yith-woocommerce-product-vendors' ), $this->get_plural_label( 'strtolower' ), _x( "with commas", "[Part of] Separate vendors with commas",'yith-woocommerce-product-vendors' ) ),
					'add_or_remove_items'        => sprintf( '%s %s', _x( 'Add or remove', '[Part of] Add or remove vendors','yith-woocommerce-product-vendors' ), $this->get_plural_label( 'strtolower' ) ),
					'choose_from_most_used'      => sprintf( '%s %s', _x( 'Choose from most used', '[Part of] Choose from most used vendors','yith-woocommerce-product-vendors' ), $vendor_plural_label ),
					'not_found'                  => sprintf( '%s %s', $vendor_plural_label, _x( 'not found', '[Part of] Vendors not found','yith-woocommerce-product-vendors' ) ),
					'back_to_items'              => sprintf( '%s %s %s', '&larr;', _x( 'Back to', '[Part of] Back to Vendors', 'yith-woocommerce-product-vendors' ), $vendor_plural_label ),
				)
			);

			return ! empty( $arg ) ? $label[ $arg ] : $label;
		}

		/**
		 * Set up array of vendor admin capabilities
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 *
		 * @return array Vendor capabilities
		 * @since  1.0
		 */
		public function vendor_enabled_capabilities() {
			$caps = array(
				"read"                      => true,
				"edit_product"              => true,
				"read_product"              => true,
				"delete_product"            => true,
				"edit_products"             => true,
				"edit_others_products"      => true,
				"delete_products"           => true,
				"delete_published_products" => true,
				"delete_others_products"    => true,
				"edit_published_products"   => true,
				"assign_product_terms"      => true,
				"upload_files"              => true,
				"manage_bookings"           => true,
				"manage_vendor_store"       => true,
				"edit_posts"                => true,
				"delete_posts"              => true
			);

			 /* === Orders === */
            if( 'yes' == get_option( 'yith_wpv_vendors_option_order_management', 'no' ) ){
                $caps['edit_shop_orders']             = true;
                $caps['edit_others_shop_orders']      = true;
                $caps['read_shop_orders']             = true;
                $caps['delete_shop_orders']           = true;
                $caps['publish_shop_orders']          = true;
                $caps['edit_published_shop_orders']   = true;
                $caps['delete_published_shop_orders'] = true;
            }

			return $caps;
		}

		/**
		 * Return the user meta key
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0.0
		 * @return string The protected attribute User Meta Key
		 */
		public function get_user_meta_key() {
			$meta_key = $this->_user_meta_key;

			if( is_multisite() && get_current_blog_id() != 1 ){
				$meta_key = $meta_key . '_' . get_current_blog_id();
			}

			return $meta_key;
		}

		/**
		 * Return the user meta key
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0.0
		 * @return string The protected attribute User Meta Key
		 */
		public function get_user_meta_owner() {
			$meta_key = $this->_user_meta_owner;

			if( is_multisite() && get_current_blog_id() != 1 ){
				$meta_key = $meta_key . '_' . get_current_blog_id();
			}

			return $meta_key;
		}

		/**
		 * Get the vendor commission
		 *
		 * @Author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return string The vendor commission
		 * @fire yith_vendor_base_commission filter
		 */
		public function get_base_commission() {
			return apply_filters( 'yith_vendor_base_commission', floatval( get_option( 'yith_vendor_base_commission', '50' ) ) / 100 );
		}

		/**
		 * Get vendors list
		 *
		 * @param array $args
		 *
		 * @return Array Vendor Objects
		 *
		 * @since  1.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function get_vendors( $args = array() ) {
			$args = wp_parse_args( $args, array(
				'enabled_selling'   => '',
                'fields'            => '',
                'pending'           => '',
			) );

			$query_args = array(
                'hide_empty' => false,
                'number'  => isset( $args['number'] ) ? $args['number'] : ''
            );

            $exclude_selling = $exclude_owner = array();

            if ( '' !== $args['enabled_selling'] ) {
                global $wpdb;
                $query = $wpdb->prepare( "SELECT DISTINCT {$this->termmeta_term_id} FROM {$this->termmeta_table} WHERE meta_key = %s AND meta_value = %s", 'enable_selling', $args['enabled_selling'] ? 'no' : 'yes' );

                if( isset( $args['owner'] ) && $args['owner'] === false ){
                    $query .= $wpdb->prepare( " AND $this->termmeta_term_id NOT IN ( SELECT DISTINCT $this->termmeta_term_id FROM $this->termmeta_table WHERE meta_key = %s AND meta_value = %s )", 'owner', '' );
                }

                $query_args['exclude'] = $wpdb->get_col( $query );

            }

            // filter for pending vendors
            if( ! empty( $args['pending'] ) && 'yes' == $args['pending'] ){
                global $wpdb;
                $query = $wpdb->prepare( "SELECT {$this->termmeta_term_id} FROM {$this->termmeta_table} WHERE meta_key = %s AND meta_value = %s", 'pending', $args['pending'] );

                $query_args['include'] = $wpdb->get_col( $query );

                if ( empty( $query_args['include'] ) ) {
                    return array();
                }
            }

            if( ! empty( $args['vacation_selling'] ) && 'disabled' == $args['vacation_selling'] ){
                global $wpdb;
                $query = $wpdb->prepare( "SELECT {$this->termmeta_term_id} FROM {$this->termmeta_table} WHERE meta_key = %s AND meta_value = %s", 'vacation_selling', $args['vacation_selling'] );

                $vendors_vacation = $wpdb->get_col( $query );
                $query_args['include'] = isset( $query_args['include'] ) ? array_merge( $query_args['include'], $vendors_vacation ) : $vendors_vacation;

                if ( empty( $query_args['include'] ) ) {
                    return array();
                }
            }

             // add pagination (use to shortcodes)
            if( isset( $args['pagination'] ) && isset( $args['pagination']['number'] ) && isset( $args['pagination']['offset'] ) ){
                $query_args['offset'] = $args['pagination']['offset'];
                $query_args['number'] = $args['pagination']['number'];
            }

			// add order (use to shortcodes)
			if( isset( $args['order'] ) ){
				$query_args['order'] = $args['order'];
			}

			// add orderby (use to shortcodes)
			if( isset( $args['orderby'] ) ){
				$query_args['orderby'] = $args['orderby'];
			}

			if( isset( $args['include'] ) ){
            	$include = is_array( $args['include'] ) ? $args['include'] : explode( ',', $args['include'] );
            	$query_args['include'] = ! empty( $query_args['include'] ) ? array_intersect( $include, $query_args['include'] ) : $include;
			}

			$vendors = get_terms( $this->_taxonomy_name, $query_args );

			if ( empty( $vendors ) || is_wp_error( $vendors ) ) {
				return array();
			}

            $res = array();

			foreach ( $vendors as $vendor ) {
                $res[] = 'ids' == $args['fields'] ? $vendor->term_id : yith_get_vendor( $vendor );
			}

			return $res;
		}

		/**
		 * Widgets Initializzation
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 * @fire yith_wcpv_widgets filter
		 */
		public function widgets_init() {
			$widgets = apply_filters( 'yith_wpv_register_widgets', array( 'YITH_Woocommerce_Vendors_Widget' ) );
			foreach ( $widgets as $widget ) {
				register_widget( $widget );
			}
		}

		/**
		 * Remove new post and comments wp bar admin menu for vendor
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.5.1
		 * @return void
		 */
        public function remove_wp_bar_admin_menu() {
            $vendor = yith_get_vendor( 'current', 'user' );

            if( $vendor->is_valid() && $vendor->has_limited_access() ){
                remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
                remove_action( 'admin_bar_menu', 'wp_admin_bar_new_content_menu', 70 );
            }
        }

		 /**
         * Add Vendor Role.
         *
	  	 * @fire register_activation_hook
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.6.5
         * @return void
         */
		public static function add_vendor_role(){
			add_role( self::$_role_name, YITH_Vendors()->get_vendors_taxonomy_label( 'singular_name' ), YITH_Vendors()->vendor_enabled_capabilities() );
		}

		/**
         * Remove Vendor Role.
         *
	  	 * @fire register_deactivation_hook
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.6.5
         * @return void
         */
		public static function remove_vendor_role(){
			remove_role( self::$_role_name );
		}

		/**
         * Plugin Setup
         *
	  	 * @fire register_activation_hook
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.6.5
         * @return void
         */
		public static function setup( $method = array() ){
			$method	 = empty( $method ) || ! is_string( $method ) ? 'remove_role' : $method;
			$vendors = YITH_Vendors()->get_vendors();
			$caps 	 = YITH_Vendors()->vendor_enabled_capabilities();
			foreach( $vendors as $vendor ){
				/** @var $vendor YITH_Vendor */
				if( $vendor->is_valid() ) {
					$admins = $vendor->get_admins();
					foreach( $admins as $admin ){
						$user = get_user_by( 'id', $admin );
						if ( $user instanceof WP_User ) {
							if ( 'remove_role' == $method ) {
								foreach ( $caps as $cap ) {
									$user->remove_cap( $cap );
								}
							}
							$user->$method( self::$_role_name );
						}
					}
				}
			}

			if( 'remove_role' == $method ) {
				delete_option( 'yith_wcmv_setup');
				delete_option( 'yith_wcmv_version');
			}

			do_action( 'yith_wcmv_after_setup' );

			//regenerate permalink
			flush_rewrite_rules();
		}

		/**
         * Get protected attribute _role_name
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.6.5
         * @return string
         */
		public function get_role_name(){
			return self::$_role_name;
		}

		/**
         * Return if VAT/SSN is required or not
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.7
         * @return string
         */
		public function is_vat_require(){
            return 'yes' == get_option( 'yith_wpv_vendors_my_account_required_vat', 'no' );
        }

		/**
		 * Return if terms and conditions is required or not
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since 1.7
		 * @return string
		 */
		public function is_terms_and_conditions_require(){
			return 'yes' == get_option( 'yith_wpv_vendors_registration_required_terms_and_conditions', 'no' );
		}

		/**
		 * Return if PayPal Email is required or not
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since 1.7
		 * @return string
		 */
		public function is_paypal_email_enabled(){
			return 'yes' == get_option( 'yith_wpv_vendors_registration_show_paypal_email', 'yes' );
		}

		/**
		 * Return if PayPal Email is required or not
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since 1.7
		 * @return string
		 */
		public function is_paypal_email_required(){
			//By default PayPal Email isn't mandatory
			$required = false;

			if( $this->is_paypal_email_enabled() ){
				$required = 'yes' == get_option( 'yith_wpv_vendors_registration_required_paypal_email', 'no' );
			}

			return $required;
		}

		/**
		 * Refresh rewrite rules for frontpage
		 *
		 * @return void
		 * @since    1.6.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function flush_rewrite_rules(){
			if( get_option( 'yith_wcmv_flush_rewrite_rules', false ) ){
				flush_rewrite_rules();
				update_option( 'yith_wcmv_flush_rewrite_rules', false );
			}
		}

		/**
		 *
		 * Update the term meta
		 *
		 * @param mixed $term_id
		 * @param string $meta_key
		 * @param mixed $meta_value
		 * @param string $prev_value (default: '')
		 * @return bool
		 *
		 * @author Andrea Grillo <andrea.grillo@yitheme.com>
		 * @since  1.9.8
		 * @return array
		 */
		public function update_term_meta( $term_id, $meta_key, $meta_value, $prev_value = '' ) {
			return ! $this->is_wc_lower_2_6 ? update_term_meta( $term_id, $meta_key, $meta_value, $prev_value ) : update_metadata( 'woocommerce_term', $term_id, $meta_key, $meta_value, $prev_value );
		}

		/**
		 *
		 * Delete the term meta
		 *
		 * @param mixed $term_id
		 * @param string $meta_key
		 * @param mixed $meta_value
		 * @return bool
		 *
		 * @author Andrea Grillo <andrea.grillo@yitheme.com>
		 * @since  1.9.8
		 * @return array
		 */
		public function delete_term_meta( $term_id, $meta_key, $meta_value = '' ) {
			return ! $this->is_wc_lower_2_6 ? delete_term_meta( $term_id, $meta_key, $meta_value ) : delete_metadata( 'woocommerce_term', $term_id, $meta_key, $meta_value );
		}

		/**
		 *
		 * Add the term meta
		 *
		 * @param mixed $term_id
		 * @param string $meta_key
		 * @param mixed $meta_value
		 * @param bool $unique (default: false)
		 * @return bool
		 *
		 * @author Andrea Grillo <andrea.grillo@yitheme.com>
		 * @since  1.9.8
		 * @return array
		 */
		public function add_term_meta( $term_id, $meta_key, $meta_value, $unique = false ) {
			return ! $this->is_wc_lower_2_6 ? add_term_meta( $term_id, $meta_key, $meta_value, $unique ) : add_metadata( 'woocommerce_term', $term_id, $meta_key, $meta_value, $unique );
		}

		/**
		 *
		 * Get the term meta
		 *
		 * @param mixed $term_id
		 * @param string $key
		 * @param bool $single (default: true)
		 * @return mixed
		 *
		 * @author Andrea Grillo <andrea.grillo@yitheme.com>
		 * @since  1.9.8
		 * @return array
		 */
		public function get_term_meta( $term_id, $key, $single = true ) {
			return ! $this->is_wc_lower_2_6 ? get_term_meta( $term_id, $key, $single ) : get_metadata( 'woocommerce_term', $term_id, $key, $single );
		}

		/**
		 *
		 * Select the termeta table.
		 * The table woocommerce_termeta was removed in WooCommerce 2.6
		 *
		 * @author Andrea Grillo <andrea.grillo@yitheme.com>
		 * @since  1.9.8
		 * @return void
		 */
		public function select_termmeta_table(){
			global $wpdb;
			if( $this->is_wc_lower_2_6 ){
				$this->termmeta_table 	= $wpdb->woocommerce_termmeta;
				$this->termmeta_term_id = 'woocommerce_' . $this->termmeta_term_id;
			}

			else{
				$this->termmeta_table 	= $wpdb->termmeta;
			}
		}

        /**
         *
         * Add image size
         *
         * @author Andrea Grillo <andrea.grillo@yitheme.com>
         * @since  1.11.4
         * @return void
         */
        protected function _add_plugin_image_size(){
            $images = array(
                'yith_vendors_gravatar' => array(
                    'width'     => get_option( 'yith_vendors_gravatar_image_size', '62' ),
                    'height'    => get_option( 'yith_vendors_gravatar_image_size', '62' ),
                    'crop'      => true,
                ),

                'yith_vendors_header' => array(
                    'width'     => get_option( 'yith_vendors_header_image_width', 0 ),
                    'height'    => get_option( 'yith_vendors_header_image_height', 0 ),
                    'crop'      => true
                ),
            );

            foreach( $images as $image_name => $image_size ){
                extract( $image_size );
                add_image_size( $image_name, $width, $height, $crop );
            }
        }

        /**
         * Get the image size name
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.11.4
         * @return string
         */
        public function get_image_size( $image_type ){
             return 'yith_vendors_' . $image_type;
        }

		/**
		 * Get social feed array - Not available on SuperClass
		 * @author Andrea Grilllo <andrea.grillo@yithemes.com>
		 *
		 * @return array empty array
		 */
		public function get_social_fields() {
			return array();
		}

		/**
		 * Get the vendor singular label
		 * @author Andrea Grilllo <andrea.grillo@yithemes.com>
		 *
		 * @param string $singular_label
		 * @return string
		 */
		public function get_singular_label( $callback = '' ) {
			$singular = $this->_singular_label;
			if( ! empty( $callback ) && function_exists( $callback ) ){
				$singular = $callback( $singular );
			}
			$singular = call_user_func( '__', $singular, 'yith-woocommerce-product-vendors' );
			return $singular;
		}

		/**
		 * Set the vendor singular label
		 * @author Andrea Grilllo <andrea.grillo@yithemes.com>
		 *
		 * @param string $singular_label
		 * @return void
		 */
		public function set_singular_label( $singular_label ) {
			$this->_singular_label = $singular_label;
		}

		/**
		 * Get the vendor  plural  label
		 * @author Andrea Grilllo <andrea.grillo@yithemes.com>
		 *
		 * @param string $singular_label
		 * @return string
		 */
		public function get_plural_label( $callback = '' ) {
			$plural = $this->_plural_label;
			if( ! empty( $callback ) && function_exists( $callback ) ){
				$plural = $callback( $plural );
			}
			$plural = call_user_func( '__', $plural, 'yith-woocommerce-product-vendors' );
			return $plural;
		}

		/**
		 * Set the vendor plural label
		 * @author Andrea Grilllo <andrea.grillo@yithemes.com>
		 *
		 * @param string $singular_label
		 * @return void
		 */
		public function set_plural_label( $plural_label ) {
			$this->_plural_label = $plural_label;
		}
	}
}