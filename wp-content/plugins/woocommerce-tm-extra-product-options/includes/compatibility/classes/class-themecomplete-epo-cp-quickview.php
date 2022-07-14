<?php // phpcs:disable WordPress.Security.NonceVerification
/**
 * Compatibility class
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility
 * with various quick view plugins
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */
final class THEMECOMPLETE_EPO_CP_Quickview {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_Quickview|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * WooCommerce Quick View Pro REST route flag
	 *
	 * @var array
	 */
	private $routes = [];

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		add_action( 'wc_epo_add_compatibility', [ $this, 'add_compatibility' ] );

	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @since 1.0
	 */
	public function add_compatibility() {
		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ], 11 );
		add_filter( 'woocommerce_tm_quick_view', [ $this, 'woocommerce_tm_quick_view' ], 10, 3 );
		add_filter( 'wc_epo_get_quickview_array', [ $this, 'get_epo_quickview_array' ] );
		add_filter( 'wc_epo_get_quickview_containers', [ $this, 'wc_epo_get_quickview_containers' ] );
		add_filter( 'rest_request_before_callbacks', [ $this, 'rest_request_before_callbacks' ], 10, 3 );
	}

	/**
	 * Enqueue scripts
	 *
	 * @since 6.0
	 */
	public function wp_enqueue_scripts() {

		if ( THEMECOMPLETE_EPO()->can_load_scripts() ) {
			wp_enqueue_script( 'themecomplete-comp-quickview', THEMECOMPLETE_EPO_COMPATIBILITY_URL . 'assets/js/cp-quickview.js', [ 'jquery' ], THEMECOMPLETE_EPO_VERSION, true );
		}

	}

	/**
	 * Get html containers
	 *
	 * @since 1.0
	 */
	public function wc_epo_get_quickview_containers() {

		$quickview_array = $this->get_epo_quickview_array();
		$qv              = [];

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

		$quickview_array = [
			// WooCommerce Quick View (https://woocommerce.com/products/woocommerce-quick-view/).
			'woothemes_quick_view'        => [
				'container' => '.woocommerce.quick-view',
				'is'        => ( isset( $_GET['wc-api'] ) && 'WC_Quick_View' === $_GET['wc-api'] ),
			],

			// Flatsome theme.
			'theme_flatsome_quick_view'   => [
				'container' => '.product-lightbox',
				'is'        => ( isset( $_POST['action'] ) && ( 'jck_quickview' === $_POST['action'] || 'ux_quickview' === $_POST['action'] || 'flatsome_quickview' === $_POST['action'] ) ),
			],

			// kleo theme.
			'theme_kleo_quick_view'       => [
				'container' => '#productModal',
				'is'        => ( isset( $_POST['action'] ) && ( 'woo_quickview' === $_POST['action'] ) ),
			],

			// YITH WooCommerce Quick View (https://yithemes.com/themes/plugins/yith-woocommerce-quick-view/).
			'yith_quick_view'             => [
				'container' => '#yith-quick-view-modal,.yith-quick-view.yith-modal,.yith-quick-view.yith-inline',
				'is'        => ( ( isset( $_POST['action'] ) && ( 'yith_load_product_quick_view' === $_POST['action'] ) ) || ( isset( $_GET['action'] ) && ( 'yith_load_product_quick_view' === $_GET['action'] ) ) ),
			],

			// Venedor theme.
			'venedor_quick_view'          => [
				'container' => '.quickview-wrap',
				'is'        => ( isset( $_GET['action'] ) && ( 'venedor_product_quickview' === $_GET['action'] ) ),
			],

			// Rubbex theme.
			'rubbez_quick_view'           => [
				'container' => '#quickview-content',
				'is'        => ( isset( $_POST['action'] ) && ( 'product_quickview' === $_POST['action'] ) ),
			],

			// WooCommerce Quickview (https://iconicwp.com/products/woocommerce-quickview/).
			'jckqv_quick_view'            => [
				'container' => '#jckqv',
				'is'        => ( isset( $_POST['action'] ) && ( 'jckqv' === $_POST['action'] ) ),
			],

			// Themify theme quick view.
			'themify_quick_view'          => [
				'container' => '#product_single_wrapper',
				'is'        => ( isset( $_GET['ajax'] ) && 'true' === $_GET['ajax'] ),
			],

			// Porto theme.
			'porto_quick_view'            => [
				'container' => '.quickview-wrap',
				'is'        => ( isset( $_GET['action'] ) && ( 'porto_product_quickview' === $_GET['action'] ) ),
			],

			// DHWCLayout - Woocommerce Products Layouts (http://codecanyon.net/item/woocommerce-products-layouts/7384574?).
			'woocommerce_product_layouts' => [
				'container' => '.dhvc-woo-product-quickview',
				'is'        => ( isset( $_POST['action'] ) && ( 'dhvc_woo_product_quickview' === $_POST['action'] ) ),
			],

			// Woo Product Quick View (http://codecanyon.net/item/woocommerce-product-quick-view/11293528?).
			'nm_getproduct'               => [
				'container' => '#popup',
				'is'        => ( isset( $_POST['action'] ) && ( 'nm_getproduct' === $_POST['action'] ) ),
			],

			// WooCommerce LightBox PRO (http://wpbean.com/).
			'lightboxpro'                 => [
				'container' => '.wpb_wl_quick_view_content',
				'is'        => ( isset( $_POST['action'] ) && ( 'wpb_wl_quickview' === $_POST['action'] ) ),
			],

			// woodmart theme.
			'woodmart_quick_view'         => [
				'container' => '.product-quick-view',
				'is'        => ( isset( $_GET['action'] ) && ( 'woodmart_quick_view' === $_GET['action'] ) ),
			],

			// woodmart theme.
			'woodmart_quick_shop'         => [
				'container' => '.product-grid-item.product.wd-loading-quick-shop',
				'is'        => ( isset( $_GET['action'] ) && ( 'woodmart_quick_shop' === $_GET['action'] ) ),
			],

			// the gem theme.
			'thegem_product_quick_view'   => [
				'container' => '.woo-modal-product',
				'is'        => ( isset( $_POST['action'] ) && ( 'thegem_product_quick_view' === $_POST['action'] ) ),
			],

			// WooCommerce Interactive Product Quick View (https://codecanyon.net/item/woo-quick-view-interactive-product-quick-view-modal-for-woocommerce/19801709).
			'wooqv_quick_view'            => [
				'container' => '.woo-quick-view',
				'is'        => ( isset( $_POST['action'] ) && ( 'wooqv_quick_view' === $_POST['action'] ) ),
			],

			// Ocean WP Theme.
			'oceanwp_product_quick_view'  => [
				'container' => '.owp-qv-content-wrap',
				'is'        => ( isset( $_GET['action'] ) && ( 'oceanwp_product_quick_view' === $_GET['action'] ) ),
			],

			// WPC Smart Quick View for WooCommerce (https://wordpress.org/plugins/woo-smart-quick-view/).
			'woosq_quickview'             => [
				'container' => '#woosq-popup',
				'is'        => ( isset( $_GET['action'] ) && ( 'woosq_quickview' === $_GET['action'] ) ),
			],

			// WooCommerce Quick View (https://wordpress.org/plugins/woo-quick-view/).
			'wcqv_get_product'            => [
				'container' => '#wcqv_contend',
				'is'        => ( isset( $_GET['action'] ) && ( 'wcqv_get_product' === $_GET['action'] ) ),
			],

			// Grace theme (https://demo.themedelights.com/Wordpress/WP001/).
			'quickview_ajax'              => [
				'container' => '#quickview-modal',
				'is'        => ( isset( $_POST['action'] ) && ( 'quickview_ajax' === $_POST['action'] ) ),
			],

			// WP Food ordering and Restaurant Menu (https://exthemes.net/wp-food-lite).
			'wp_food'                     => [
				'container' => '#food_modal',
				'is'        => ( isset( $_POST['action'] ) && ( 'exfood_booking_info' === $_POST['action'] ) ),
			],

			// WooCommerce Quick View Pro (https://barn2.co.uk/wordpress-plugins/woocommerce-quick-view-pro/).
			'quickview_pro'               => [
				'container' => '.wc-quick-view-modal',
				'is'        => ( isset( $this->routes['wc-quick-view-pro'] ) && ( true === $this->routes['wc-quick-view-pro'] ) ),
			],

			// WooFood (https://www.wpslash.com/plugin/woofood/).
			'woofood'                     => [
				'container' => '.wf_product_view',
				'is'        => ( isset( $_POST['action'] ) && ( 'woofood_quickview_ajax' === $_POST['action'] ) ),
			],

			// WooCommerce Food (https://exthemes.net/woocommerce-food/).
			'exwoofood_booking_info'      => [
				'container' => '#food_modal',
				'is'        => ( isset( $_POST['action'] ) && ( 'exwoofood_booking_info' === $_POST['action'] ) ),
			],

			// JetElements For Elementor.
			'jet_popup_get_content'       => [
				'container' => '.jet-popup',
				'is'        => ( isset( $_POST['action'] ) && ( 'jet_popup_get_content' === $_POST['action'] ) ),
			],

			// Salient theme.
			'nectar_woo_get_product'      => [
				'container' => '.nectar-quick-view-box',
				'is'        => ( isset( $_POST['action'] ) && ( 'nectar_woo_get_product' === $_POST['action'] ) ),
			],

			// Avada quick view.
			'fusion_quick_view_load'      => [
				'container' => '.fusion-woocommerce-quick-view-container',
				'is'        => ( isset( $_POST['action'] ) && ( 'fusion_quick_view_load' === $_POST['action'] ) ),
			],

			// CiyaShop quick view.
			'ciyashop_quick_view'         => [
				'container' => '.product-quick-view',
				'is'        => ( isset( $_REQUEST['action'] ) && ( 'ciyashop_quick_view' === $_REQUEST['action'] ) ),
			],

			// Quick View for WooCommerce (https://wordpress.org/plugins/woo-quickview/).
			'wqv_popup_content'           => [
				'container' => '#wqv-quick-view-content',
				'is'        => ( isset( $_REQUEST['action'] ) && ( 'wqv_popup_content' === $_REQUEST['action'] ) ),
			],

			// xstore theme.
			'etheme_product_quick_view'   => [
				'container' => '.et-popup-content',
				'is'        => ( isset( $_REQUEST['action'] ) && ( 'etheme_product_quick_view' === $_REQUEST['action'] ) ),
			],

			// WooCommerce Quick View Builder for Elementor Page Builder.
			'mst_wcqvfepb_load_popup'     => [
				'container' => '.mst-wcqvfepb-popup-container',
				'is'        => ( isset( $_REQUEST['action'] ) && ( 'mst_wcqvfepb_load_popup' === $_REQUEST['action'] ) ),
			],

			// Quick View WooCommerce Premium (https://xootix.com/plugins/quick-view-for-woocommerce/).
			'xoo_qv_ajax'                 => [
				'container' => '.xoo-qv-container',
				'is'        => ( isset( $_REQUEST['action'] ) && ( 'xoo_qv_ajax' === $_REQUEST['action'] ) ),
			],

			// Quick View for blocksy theme (https://creativethemes.com/blocksy/).
			'blocsky_get_woo_quick_view'  => [
				'container' => '.ct-panel.quick-view-modal',
				'is'        => ( isset( $_REQUEST['action'] ) && ( 'blocsky_get_woo_quick_view' === $_REQUEST['action'] ) ),
			],

		];

		return apply_filters( 'wc_epo_quickview_array', $quickview_array );

	}

	/**
	 * Check if we are in a supported quickview
	 *
	 * @param boolean $qv if we are in a supported quick view.
	 * @since 1.0
	 */
	public function woocommerce_tm_quick_view( $qv ) {

		$quickview_array = $this->get_epo_quickview_array();

		foreach ( $quickview_array as $key => $value ) {
			if ( ! empty( $value['is'] ) ) {
				$qv = true;
			}
		}

		return apply_filters( 'wc_epo_is_quickview', $qv );

	}

	/**
	 * Filters the response before executing any REST API callbacks
	 *
	 * @param WP_REST_Response|WP_HTTP_Response|WP_Error|mixed $response Result to send to the client.
	 *                                                                   Usually a WP_REST_Response or WP_Error.
	 * @param array                                            $handler  Route handler used for the request.
	 * @param WP_REST_Request                                  $request  Request used to generate the response.
	 * @since 1.0
	 */
	public function rest_request_before_callbacks( $response, $handler, $request ) {

		if ( $request instanceof WP_REST_Request ) {
			$route = $request->get_route();
			$route = explode( '/', $route );
			if ( isset( $route[1] ) && 'wc-quick-view-pro' === $route[1] ) {
				$this->routes['wc-quick-view-pro'] = true;
				THEMECOMPLETE_EPO()->init_settings();
			}
		}

		return $response;

	}

}
