<?php
/**
 * Admin Premium class
 *
 * @author YITH
 * @package YITH WooCommerce One-Click Checkout Premium
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WOCC' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WOCC_Admin_Premium' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WOCC_Admin_Premium extends YITH_WOCC_Admin {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WOCC_Admin_Premium
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $version = YITH_WOCC_VERSION;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WOCC_Admin_Premium
		 * @since 1.0.0
		 */
		public static function get_instance(){
			if( is_null( self::$instance ) ){
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function __construct() {

			parent::__construct();

			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			// add panel tab
			add_filter( 'yith_wocc_admin_tabs', array( $this, 'add_tabs' ), 10, 1 );

			// custom tab
			add_action( 'yith_wocc_exclusion_table', array( $this, 'exclusion_table' ) );

			// admin panel css
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// search product category in ajax
			add_action( 'wp_ajax_yith_wocc_search_product_cat', array( $this, 'search_product_cat_ajax' ) );
			add_action( 'wp_ajax_nopriv_yith_wocc_search_product_cat', array( $this, 'search_product_cat_ajax' ) );

			// select categories
			add_action( 'woocommerce_admin_field_yith_wocc_select_cat', array( $this, 'select_categories' ), 10, 1 );
			// sanitize option value for select categories
            add_filter( 'woocommerce_admin_settings_sanitize_option_yith-wocc-excluded-cat', array( $this, 'sanitize_option_categories' ), 10, 3 );
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once( YITH_WOCC_DIR . 'plugin-fw/licence/lib/yit-licence.php' );
				require_once( YITH_WOCC_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php' );
			}

			YIT_Plugin_Licence()->register( YITH_WOCC_INIT, YITH_WOCC_SECRET_KEY, YITH_WOCC_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function register_plugin_for_updates() {
			if( ! class_exists( 'YIT_Plugin_Licence' ) ){
				require_once( YITH_WOCC_DIR . 'plugin-fw/lib/yit-upgrade.php' );
			}

			YIT_Upgrade()->register( YITH_WOCC_SLUG, YITH_WOCC_INIT );
		}

		/**
		 * Add premium tab to plugin settings panel
		 *
		 * @since 1.0.0
		 * @param $tabs
		 * @return mixed
		 * @author Francesco Licandro
		 */
		public function add_tabs( $tabs ) {
			$tabs['exclusions'] = __( 'Product List', 'yith-woocommerce-one-click-checkout' );

			return $tabs;
		}

		/**
		 * Print exclusion table
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function exclusion_table() {
			if( file_exists( YITH_WOCC_DIR . '/templates/admin/exclusions-tab.php' ) ) {
				include_once( YITH_WOCC_DIR . '/templates/admin/exclusions-tab.php' );
			}
		}

		/**
		 * Admin panel style and scripts
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function enqueue_scripts() {
			if ( isset( $_GET['page'] ) && $_GET['page'] == $this->_panel_page ) {
				$min = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';
				wp_enqueue_style( 'yith-wocc-admin-style', YITH_WOCC_ASSETS_URL . '/css/yith-wocc-admin.css', array(), false, 'all' );
				wp_enqueue_script( 'yith-wocc-admin-script', YITH_WOCC_ASSETS_URL . '/js/yith-wocc-admin'.$min.'.js', array( 'jquery' ), false, true );
			}
		}

		/**
		 * Ajax action search categories
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function search_product_cat_ajax(){
			ob_start();

			check_ajax_referer( 'search-products', 'security' );

			$term = (string) wc_clean( stripslashes( $_GET['term'] ) );

			if ( empty( $term ) ) {
				die();
			}

			$args = array(
				'orderby'           => 'name',
				'order'             => 'ASC',
				'hide_empty'        => false,
				'exclude'           => array(),
				'exclude_tree'      => array(),
				'include'           => array(),
				'number'            => '',
				'fields'            => 'all',
				'slug'              => '',
				'parent'            => '',
				'hierarchical'      => true,
				'child_of'          => 0,
				'childless'         => false,
				'get'               => '',
				'name__like'        => $term,
				'pad_counts'        => false,
				'offset'            => '',
				'search'            => '',
			);

			$terms = get_terms( 'product_cat', $args);
			$found_categories = array();

			if ( $terms ) {
				foreach ( $terms as $term ) {
					$found_categories[ $term->term_id ] = rawurldecode( $term->name );
				}
			}

			wp_send_json( $found_categories );
		}

		/**
		 * Add select categories
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function select_categories( $args = array() ) {

			if ( ! empty( $args ) ) {
				$args['categories'] = get_option( $args['id'], $args['default'] );

				extract( $args );

                ! is_array( $categories ) && $categories = explode( ',', $categories );
                // remove empty
                $categories = array_filter( $categories );
                $json_ids    = array();

                foreach ( $categories as $category ) {
                    $term_obj = get_term_by( 'id', $category, 'product_cat' );
                    if ( $term_obj ) {
                        $json_ids[ $category ] = wp_kses_post( $term_obj->name );
                    }
                }

                if( file_exists( YITH_WOCC_TEMPLATE_PATH . '/admin/select-categories.php' ) )
					include( YITH_WOCC_TEMPLATE_PATH . '/admin/select-categories.php' );
			}
		}

		/**
         * Sanitize option for select catagories
         *
         * @since 1.1.0
         * @author Francesco Licandro
         * @param mixed $value
         * @param array $option
         * @param mixed $raw_value
         * @return mixed
         */
		public function sanitize_option_categories( $value, $option, $raw_value ) {
		    return is_null( $value ) ? array() : $value;
        }

	}
}

/**
 * Unique access to instance of YITH_WOCC_Admin_Premium class
 *
 * @return \YITH_WOCC_Admin_Premium
 * @since 1.0.0
 */
function YITH_WOCC_Admin_Premium(){
	return YITH_WOCC_Admin_Premium::get_instance();
}