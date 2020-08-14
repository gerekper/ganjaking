<?php
/**
 * Admin Premium class
 *
 * @author YITH
 * @package YITH WooCommerce Compare
 * @version 2.0.0
 */

if ( ! defined( 'YITH_WOOCOMPARE' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_Woocompare_Admin_Premium' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_Woocompare_Admin_Premium extends YITH_Woocompare_Admin {

		/**
		 * @var boolean
		 * @since 2.0.8
		 */
		public $is_wc27 = false;
		
		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function __construct() {

			parent::__construct();

			$this->is_wc27 = version_compare( WC()->version, '2.7', '<' );

			add_action( 'admin_menu', array( $this, 'add_custom_type' ) );

			// Register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			// add admin tabs
			add_filter( 'yith_woocompare_admin_tabs', array( $this, 'add_admin_tabs' ), 10, 1 );

			// search product category in ajax
			add_action( 'wp_ajax_yith_woocompare_search_product_cat', array( $this, 'yith_woocompare_search_product_cat_ajax' ) );
			add_action( 'wp_ajax_nopriv_yith_woocompare_search_product_cat', array( $this, 'yith_woocompare_search_product_cat_ajax' ) );

			add_action( 'yith_woocompare_shortcode_tab', array( $this, 'shortcode_tab' ) );

		}

		/**
		 * Add upload type to standard woo
		 *
		 * @since 2.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function add_custom_type() {
			// select category
			add_action( 'woocommerce_admin_field_yith_woocompare_select_cat', array( $this, 'yith_woocompare_select_cat' ), 10, 1 );
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since    2.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_activation() {
			if( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once( YITH_WOOCOMPARE_DIR . 'plugin-fw/licence/lib/yit-licence.php' );
				require_once( YITH_WOOCOMPARE_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php' );
			}
			YIT_Plugin_Licence()->register( YITH_WOOCOMPARE_INIT, YITH_WOOCOMPARE_SECRET_KEY, YITH_WOOCOMPARE_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since    2.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_updates() {
			if( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once( YITH_WOOCOMPARE_DIR . 'plugin-fw/lib/yit-upgrade.php' );
			}
			YIT_Upgrade()->register( YITH_WOOCOMPARE_SLUG, YITH_WOOCOMPARE_INIT );
		}

		/**
		 * Add select cate
		 */
		public function yith_woocompare_select_cat( $args = array() ) {

			if ( ! empty( $args ) ) {
				$args['value'] = get_option( $args['id'], $args['default'] );
				extract( $args );
				
				// build data selected array
				$data_selected   = array();
				$categories = ! is_array( $value ) ? explode( ',', $value ) : $value;
				
				foreach ( $categories as $category ) {
					$term_obj = get_term_by( 'id', $category, 'product_cat' );
					if ( $term_obj ) {
						$data_selected[ $category ] = wp_kses_post( $term_obj->name );
					}
				}
				
				if( $this->is_wc27 ) {
					$value = implode( ',', array_keys( $data_selected ) );
				}

				include( YITH_WOOCOMPARE_TEMPLATE_PATH . '/admin/yith_woocompare_select_cat.php' );
			}
		}

		/**
		 * Ajax action search product
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function yith_woocompare_search_product_cat_ajax(){
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
			$found_products = array();

			if ( $terms ) {
				foreach ( $terms as $term ) {
					$found_products[ $term->term_id ] = rawurldecode( $term->name );
				}
			}

			wp_send_json( $found_products );
		}

		/**
		 * Add premium admin tabs
		 *
		 * @since 2.0.0
		 * @access public
		 * @param mixed $tabs
		 * @return mixed
		 * @author Francesco Licandro
		 */
		public function add_admin_tabs( $tabs ) {

			$tabs['table']   	= __( 'Comparison Table', 'yith-woocommerce-compare' );
			$tabs['share']   	= __( 'Social Network Sites Sharing', 'yith-woocommerce-compare' );
			$tabs['related'] 	= __( 'Related Products', 'yith-woocommerce-compare' );
			$tabs['style']   	= __( 'Style', 'yith-woocommerce-compare' );
			$tabs['shortcode']  = __( 'Build Shortcode', 'yith-woocommerce-compare' );

			return $tabs;
		}

		/**
		 * Content of build shortcode tab in plugin setting
		 *
		 * @access public
		 * @since 2.0.3
		 * @author Francesco Licandro
		 */
		public function shortcode_tab() {
			$shortcode_tab_template = YITH_WOOCOMPARE_TEMPLATE_PATH . '/admin/yith_woocompare_shortcode_tab.php';
			if( file_exists( $shortcode_tab_template ) ) {
				include_once( $shortcode_tab_template );
			}
		}
	}
}
