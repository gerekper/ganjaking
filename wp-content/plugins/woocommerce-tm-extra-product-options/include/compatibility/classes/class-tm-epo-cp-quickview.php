<?php
/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * various quick view plugins
 *
 * @package Extra Product Options/Compatibility
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_CP_quickview {

	/**
	 * The single instance of the class
	 *
	 * @since 1.0
	 */
	protected static $_instance = NULL;

	private $routes = array();

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		add_action( 'wc_epo_add_compatibility', array( $this, 'add_compatibility' ) );

	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @since 1.0
	 */
	public function add_compatibility() {
		add_filter( 'woocommerce_tm_quick_view', array( $this, 'woocommerce_tm_quick_view' ), 10, 3 );
		add_filter( 'wc_epo_get_quickview_array', array( $this, 'get_epo_quickview_array' ) );
		add_filter( 'wc_epo_get_quickview_containers', array( $this, 'wc_epo_get_quickview_containers' ) );
		add_filter( 'rest_request_before_callbacks', array( $this, 'rest_request_before_callbacks' ), 10, 3 );
	}

	/**
	 * Get html containers
	 *
	 * @since 1.0
	 */
	public function wc_epo_get_quickview_containers() {

		$quickview_array = $this->get_epo_quickview_array();
		$qv              = array();

		foreach ( $quickview_array as $key => $value ) {
			$qv[ $key ] = $value ['container'];
		}

		return $qv;

	}

	/**
	 * Get supported quickviews
	 *
	 * @since 1.0
	 */
	public function get_epo_quickview_array() {

		$quickview_array = array(
			'woothemes_quick_view'        => array( 'container' => '.woocommerce.quick-view', 'is' => ( isset( $_GET['wc-api'] ) && $_GET['wc-api'] == 'WC_Quick_View' ) ),
			'theme_flatsome_quick_view'   => array( 'container' => '.product-lightbox', 'is' => ( isset( $_POST['action'] ) && ( $_POST['action'] == 'jck_quickview' || $_POST['action'] == 'ux_quickview' || $_POST['action'] == 'flatsome_quickview' ) ) ),
			'theme_kleo_quick_view'       => array( 'container' => '#productModal', 'is' => ( isset( $_POST['action'] ) && ( $_POST['action'] == 'woo_quickview' ) ) ),
			'yith_quick_view'             => array( 'container' => '#yith-quick-view-modal,.yith-quick-view.yith-modal,.yith-quick-view.yith-inline', 'is' => ( ( isset( $_POST['action'] ) && ( $_POST['action'] == 'yith_load_product_quick_view' ) ) || ( isset( $_GET['action'] ) && ( $_GET['action'] == 'yith_load_product_quick_view' ) ) ) ),
			'venedor_quick_view'          => array( 'container' => '.quickview-wrap', 'is' => ( isset( $_GET['action'] ) && ( $_GET['action'] == 'venedor_product_quickview' ) ) ),
			'rubbez_quick_view'           => array( 'container' => '#quickview-content', 'is' => ( isset( $_POST['action'] ) && ( $_POST['action'] == 'product_quickview' ) ) ),
			'jckqv_quick_view'            => array( 'container' => '#jckqv', 'is' => ( isset( $_POST['action'] ) && ( $_POST['action'] == 'jckqv' ) ) ),// WooCommerce Quickview https://iconicwp.com/products/woocommerce-quickview/
			'themify_quick_view'          => array( 'container' => '#product_single_wrapper', 'is' => ( isset( $_GET['ajax'] ) && $_GET['ajax'] == 'true' ) ), //Themify theme quick view
			'porto_quick_view'            => array( 'container' => '.quickview-wrap', 'is' => ( isset( $_GET['action'] ) && ( $_GET['action'] == 'porto_product_quickview' ) ) ),
			'woocommerce_product_layouts' => array( 'container' => '.dhvc-woo-product-quickview', 'is' => ( isset( $_POST['action'] ) && ( $_POST['action'] == 'dhvc_woo_product_quickview' ) ) ),// DHWCLayout - Woocommerce Products Layouts http://codecanyon.net/item/woocommerce-products-layouts/7384574?
			'nm_getproduct'               => array( 'container' => '#popup', 'is' => ( isset( $_POST['action'] ) && ( $_POST['action'] == 'nm_getproduct' ) ) ),// Woo Product Quick View http://codecanyon.net/item/woocommerce-product-quick-view/11293528?
			'lightboxpro'                 => array( 'container' => '.wpb_wl_quick_view_content', 'is' => ( isset( $_POST['action'] ) && ( $_POST['action'] == 'wpb_wl_quickview' ) ) ),// WooCommerce LightBox PRO http://wpbean.com/
			'woodmart_quick_view'         => array( 'container' => '.product-quick-view', 'is' => ( isset( $_GET['action'] ) && ( $_GET['action'] == 'woodmart_quick_view' ) ) ),// woodmart theme
			'woodmart_quick_shop'         => array( 'container' => '.product-grid-item.product', 'is' => ( isset( $_GET['action'] ) && ( $_GET['action'] == 'woodmart_quick_shop' ) ) ),// woodmart theme
			'thegem_product_quick_view'   => array( 'container' => '.woo-modal-product', 'is' => ( isset( $_POST['action'] ) && ( $_POST['action'] == 'thegem_product_quick_view' ) ) ),// the gem theme
			'wooqv_quick_view'            => array( 'container' => '.woo-quick-view', 'is' => ( isset( $_POST['action'] ) && ( $_POST['action'] == 'wooqv_quick_view' ) ) ),// WooCommerce Interactive Product Quick View https://codecanyon.net/item/woo-quick-view-interactive-product-quick-view-modal-for-woocommerce/19801709
			'oceanwp_product_quick_view'  => array( 'container' => '.owp-qv-content-wrap', 'is' => ( isset( $_GET['action'] ) && ( $_GET['action'] == 'oceanwp_product_quick_view' ) ) ),// Ocean WP Theme
			'woosq_quickview'             => array( 'container' => '#woosq-popup', 'is' => ( isset( $_GET['action'] ) && ( $_GET['action'] == 'woosq_quickview' ) ) ),// WPC Smart Quick View for WooCommerce https://wordpress.org/plugins/woo-smart-quick-view/
			'wcqv_get_product'            => array( 'container' => '#wcqv_contend', 'is' => ( isset( $_GET['action'] ) && ( $_GET['action'] == 'wcqv_get_product' ) ) ),// WooCommerce Quick View https://wordpress.org/plugins/woo-quick-view/
			'quickview_ajax'              => array( 'container' => '#quickview-modal', 'is' => ( isset( $_POST['action'] ) && ( $_POST['action'] == 'quickview_ajax' ) ) ),// Grace theme https://demo.themedelights.com/Wordpress/WP001/
			'wp_food'                     => array( 'container' => '#food_modal', 'is' => ( isset( $_POST['action'] ) && ( $_POST['action'] == 'exfood_booking_info' ) ) ),// WP Food ordering and Restaurant Menu https://exthemes.net/wp-food-lite
			'quickview_pro'               => array( 'container' => '.wc-quick-view-modal', 'is' => ( isset( $this->routes["wc-quick-view-pro"] ) && ( $this->routes["wc-quick-view-pro"] === TRUE ) ) ),// WooCommerce Quick View Pro https://barn2.co.uk/wordpress-plugins/woocommerce-quick-view-pro/
			'woofood'                     => array( 'container' => '.wf_product_view', 'is' => ( isset( $_POST['action'] ) && ( $_POST['action'] == 'woofood_quickview_ajax' ) ) ),// WooFood https://www.wpslash.com/plugin/woofood/
			'exwoofood_booking_info'      => array( 'container' => '#food_modal', 'is' => ( isset( $_POST['action'] ) && ( $_POST['action'] == 'exwoofood_booking_info' ) ) ),// WooCommerce Food https://exthemes.net/woocommerce-food/
			'jet_popup_get_content'       => array( 'container' => '.jet-popup', 'is' => ( isset( $_POST['action'] ) && ( $_POST['action'] == 'jet_popup_get_content' ) ) ),// JetElements For Elementor
			'nectar_woo_get_product'      => array( 'container' => '.nectar-quick-view-box', 'is' => ( isset( $_POST['action'] ) && ( $_POST['action'] == 'nectar_woo_get_product' ) ) ),// Salient theme
			'fusion_quick_view_load'      => array( 'container' => '.fusion-woocommerce-quick-view-container', 'is' => ( isset( $_POST['action'] ) && ( $_POST['action'] == 'fusion_quick_view_load' ) ) ),// Avada quick view
		);

		return apply_filters( 'wc_epo_quickview_array', $quickview_array );

	}

	/**
	 * Check if we are in a supported quickview
	 *
	 * @since 1.0
	 */
	public function woocommerce_tm_quick_view( $qv ) {

		$quickview_array = $this->get_epo_quickview_array();

		foreach ( $quickview_array as $key => $value ) {
			if ( ! empty( $value['is'] ) ) {
				$qv = TRUE;
			}
		}

		return apply_filters( 'wc_epo_is_quickview', $qv );

	}

	/**
	 * Filters the response before executing any REST API callbacks
	 *
	 * @since 1.0
	 */
	public function rest_request_before_callbacks( $response, $handler, $request ) {

		if ( $request instanceof WP_REST_Request ) {
			$route = $request->get_route();
			$route = explode( "/", $route );
			if ( isset( $route[1] ) && $route[1] === "wc-quick-view-pro" ) {
				$this->routes["wc-quick-view-pro"] = TRUE;
				THEMECOMPLETE_EPO()->init_settings();
			}
		}


		return $response;

	}

}
