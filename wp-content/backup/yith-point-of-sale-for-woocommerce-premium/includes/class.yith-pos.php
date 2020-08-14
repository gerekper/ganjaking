<?php
! defined( 'YITH_POS' ) && exit; // Exit if accessed directly


if ( ! class_exists( 'YITH_POS' ) ) {
	/**
	 * Class YITH_POS
	 * Main Class
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	class YITH_POS {

		/** @var YITH_POS */
		private static $_instance;

		/** @var YITH_POS_Admin */
		public $admin;

		/** @var YITH_POS_Frontend */
		public $frontend;

		/** @var YITH_POS_Orders */
		public $orders;

		/** @var YITH_POS_Assets */
		public $assets;

		/** @var YITH_POS_Products */
		public $products;

		/** @var string */
		public static $page_template = 'yith-pos-page.php';

		/** @var array */
		public $post_page_templates = array();

		/**
		 * Singleton implementation
		 *
		 * @return YITH_POS
		 */
		public static function get_instance() {
			return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
		}

		/**
		 * YITH_POS constructor.
		 */
		private function __construct() {
			$this->load();

			// Load Plugin Framework
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

			//add new gateways
			add_filter( 'woocommerce_payment_gateways', array( $this, 'add_pos_gateways' ) );

			// allow Cashiers and managers to list products through REST API
			add_action( 'woocommerce_rest_check_permissions', array( $this, 'filter_rest_permissions' ), 10, 4 );


			//add POS page
			$this->post_page_templates = array(
				self::$page_template => __( 'YITH POS template Page', 'yith-point-of-sale-for-woocommerce' ),
			);
			add_action( 'init', array( $this, 'add_pos_page' ) );
			add_filter( 'template_include', array( $this, 'view_pos_template' ) );
		}


		/**
		 * Include required core files used in admin and on the frontend.
		 */
		public function load() {

			require_once( YITH_POS_INCLUDES_PATH . 'class.yith-pos-orders.php' );
			require_once( YITH_POS_INCLUDES_PATH . 'class.yith-pos-products.php' );
			require_once( YITH_POS_INCLUDES_PATH . 'class.yith-pos-ajax.php' );
			require_once( YITH_POS_INCLUDES_PATH . 'class.yith-pos-register-session.php' );
			require_once( YITH_POS_INCLUDES_PATH . 'class.yith-pos-stock-management.php' );
			require_once( YITH_POS_INCLUDES_PATH . 'class.yith-pos-settings.php' );

			/**
			 * Gateways classes.
			 */
			require_once( YITH_POS_INCLUDES_PATH . 'gateways/class.yith-pos-payment-gateway-cache.php' );
			require_once( YITH_POS_INCLUDES_PATH . 'gateways/class.yith-pos-payment-gateway-chip-pin.php' );


			/**
			 * Objects
			 */
			require_once( YITH_POS_INCLUDES_PATH . 'objects/abstract.yith-post-cpt-object.php' );
			require_once( YITH_POS_INCLUDES_PATH . 'objects/class.yith-pos-store.php' );
			require_once( YITH_POS_INCLUDES_PATH . 'objects/class.yith-pos-register.php' );
			require_once( YITH_POS_INCLUDES_PATH . 'objects/class.yith-pos-receipt.php' );

			/**
			 * Rest
			 */
			require_once( YITH_POS_INCLUDES_PATH . 'rest-api/Loader.php' );

			/**
			 * Loading...
			 */
			\YITH\POS\RestApi\Loader::get_instance();

			YITH_POS_Post_Types::init();
			YITH_POS_Ajax::get_instance();
			YITH_POS_Stock_Management::get_instance();

			$this->orders   = YITH_POS_Orders::get_instance();
			$this->products = YITH_POS_Products::get_instance();

			if ( self::is_request( 'admin' ) || self::is_request( 'frontend' ) ) {
				require_once( YITH_POS_INCLUDES_PATH . 'class.yith-pos-assets.php' );
				$this->assets = YITH_POS_Assets::get_instance();
			}

			if ( self::is_request( 'admin' ) ) {
				require_once( YITH_POS_INCLUDES_PATH . 'admin/class.yith-pos-store-post-type-admin.php' );
				require_once( YITH_POS_INCLUDES_PATH . 'admin/class.yith-pos-register-post-type-admin.php' );
				require_once( YITH_POS_INCLUDES_PATH . 'admin/class.yith-pos-receipt-post-type-admin.php' );

				require_once( YITH_POS_INCLUDES_PATH . 'class.yith-pos-admin.php' );
				$this->admin = YITH_POS_Admin();
			}

			require_once( YITH_POS_INCLUDES_PATH . 'class.yith-pos-frontend.php' );
			if ( self::is_request( 'frontend' ) ) {
				$this->frontend = YITH_POS_Frontend();
			}


		}

		/**
		 * What type of request is this?
		 *
		 * @param string $type admin, ajax, cron or frontend.
		 *
		 * @return bool
		 */
		public static function is_request( $type ) {
			switch ( $type ) {
				case 'admin':
					return is_admin() && ! defined( 'DOING_AJAX' ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX && ( ! isset( $_REQUEST[ 'context' ] ) || ( isset( $_REQUEST[ 'context' ] ) && $_REQUEST[ 'context' ] !== 'frontend' ) ) );
				case 'ajax':
					return defined( 'DOING_AJAX' );
				case 'frontend':
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}

			return false;
		}

		/**
		 * Add the POS Gateway inside the WC list.
		 *
		 * @param $gateways
		 *
		 * @return array
		 */
		public function add_pos_gateways( $gateways ) {
			$gateways[] = 'YITH_POS_Payment_Gateway_Cash';
			$gateways[] = 'YITH_POS_Payment_Gateway_Chip_Pin';

			return $gateways;
		}


		/**
		 * Allow Cashiers and managers to list products through REST API
		 *
		 * @param bool   $permission
		 * @param string $context
		 * @param int    $object_id
		 * @param string $object
		 *
		 * @return bool
		 */
		public function filter_rest_permissions( $permission, $context, $object_id, $object ) {
			$permissions_map = array(
				'product'           => array(
					'yith_pos_view_products'   => array( 'read' ),
					'yith_pos_create_products' => array( 'create' ),
				),
				'product_variation' => array(
					'yith_pos_view_products'   => array( 'read' ),
					'yith_pos_create_products' => array( 'create' ),
				),
				'product_cat'       => array(
					'yith_pos_view_product_cats' => array( 'read' ),
				),
				'shop_order'        => array(
					'yith_pos_create_orders' => array( 'create' ),
					'yith_pos_view_orders'   => array( 'read' ),
				),
				'shop_coupon'       => array(
					'yith_pos_view_coupons' => array( 'read' ),
				),
				'reports'           => array(
					'yith_pos_view_reports' => array( 'read' ),
				),
				'user' => array(
					'yith_pos_view_users'   => array( 'read' ),
					'yith_pos_edit_users'   => array( 'edit' ),
					'yith_pos_create_users' => array( 'create', 'edit' ),
				),
				'settings'          => array(
					'yith_pos_use_pos' => array( 'read' ),
				),
				'shipping_methods'          => array(
					'yith_pos_use_pos' => array( 'read' ),
				),
			);

			if ( ! $permission ) {
				$caps = array_key_exists( $object, $permissions_map ) ? $permissions_map[ $object ] : array();
				foreach ( $caps as $_cap => $_contexts ) {
					if ( current_user_can( $_cap ) && in_array( $context, (array) $_contexts ) ) {
						$permission = true;
						break;
					}
				}
			}

			return $permission;
		}

		/**
		 * Load Plugin Framework
		 *
		 * @access public
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
		 * Add the page Pos
		 */
		public function add_pos_page() {

			$option_name  = 'settings_pos_page';
			$option_value = get_option( $option_name );

			//the page exists
			if ( $option_value && get_post( $option_value ) ) {
				//check if the template is set
				update_post_meta( $option_value, '_wp_page_template', self::$page_template );

				return;
			}

			global $wpdb;
			$slug       = esc_sql( _x( 'pos', 'slug of the page', 'yith-point-of-sale-for-woocommerce' ) );
			$page_found = $wpdb->get_var( $wpdb->prepare( "SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_name` = '%s' LIMIT 1;", $slug ) );

			if ( $page_found ) {
				! $option_value && update_option( $option_name, $page_found );
			} else {
				$page_data = array(
					'post_status'    => 'publish',
					'post_type'      => 'page',
					'post_author'    => 1,
					'post_name'      => $slug,
					'post_title'     => __( 'YITH POS', 'yith-point-of-sale-for-woocommerce' ),
					'post_content'   => '',
					'post_parent'    => 0,
					'comment_status' => 'closed'
				);

				$page_id = wp_insert_post( $page_data );
				add_post_meta( $page_id, '_wp_page_template', self::$page_template );
				update_option( $option_name, $page_id );
			}
		}

		/**
		 * Checks if the template is assigned to the page
		 */
		public function view_pos_template( $template ) {
			global $post;

			if ( ! $post ) {
				return $template;
			}

			// Return default template if we don't have a custom one defined
			$post_page_template = get_post_meta( $post->ID, '_wp_page_template', true );
			if ( ! isset( $this->post_page_templates[ $post_page_template ] ) ) {
				return $template;
			}

			$file = get_stylesheet_directory() . '/' . get_post_meta( $post->ID, '_wp_page_template', true );
			if ( file_exists( $file ) ) {
				return $file;
			}

			$file = get_template_directory() . '/' . get_post_meta( $post->ID, '_wp_page_template', true );
			if ( file_exists( $file ) ) {
				return $file;
			}

			$file = YITH_POS_TEMPLATE_PATH . get_post_meta( $post->ID, '_wp_page_template', true );

			return file_exists( $file ) ? $file : $template;
		}
	}
}

if ( ! function_exists( 'YITH_POS' ) ) {
	/**
	 * Unique access to instance of YITH_POS class
	 *
	 * @return YITH_POS
	 * @since 1.0.0
	 */
	function YITH_POS() {
		return YITH_POS::get_instance();
	}
}