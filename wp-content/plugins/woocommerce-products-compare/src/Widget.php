<?php
/**
 * Products Compare Widget.
 *
 * @since 1.3.0
 */

namespace KoiLab\WC_Products_Compare;

defined( 'ABSPATH' ) || exit;

/**
 * Products Compare Widget class.
 */
class Widget extends \WC_Widget {

	/**
	 * Constructor
	 *
	 * @since 1.3.0
	 */
	public function __construct() {
		$this->widget_id          = 'compared_products';
		$this->widget_name        = __( 'WooCommerce Products Compare', 'woocommerce-products-compare' );
		$this->widget_cssclass    = 'woocommerce woocommerce-products-compare-widget';
		$this->widget_description = __( 'Displays a running list of compared products.', 'woocommerce-products-compare' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'label' => __( 'Title:', 'woocommerce-products-compare' ),
				'std'   => __( 'Compare products', 'woocommerce-products-compare' ),
			),
		);

		parent::__construct();
	}

	/**
	 * Display the widget on the frontend.
	 *
	 * @since 1.3.0
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Widget settings for this instance.
	 */
	public function widget( $args, $instance ) {
		$this->widget_start( $args, $instance );

		$product_ids = \WC_Products_Compare_Frontend::get_compared_products();

		$params = array(
			'products'    => array_values( array_filter( array_map( 'wc_get_product', $product_ids ) ) ),
			'compare_url' => site_url( \WC_Products_Compare_Frontend::get_endpoint() ),
		);

		wc_get_template( 'content-widget-products-compare.php', $params, '', WC_PRODUCTS_COMPARE_PATH . 'templates/' );

		$this->widget_end( $args );
	}
}
