<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWPAR_VERSION' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;

class YITH_WC_Points_Rewards_Product_Points_Elementor_Widget extends \Elementor\Widget_Base {


	public function get_name() {
		return 'yith-points-and-rewards-product-points';
	}

	public function get_title() {
		return __( 'YITH Points and Rewards Product Points', 'yith-woocommerce-points-and-rewards' );
	}

	public function get_icon() {
		return 'eicon-table-of-contents';
	}

	public function get_categories() {
		return array( 'yith', 'woocommerce-elements-single' );
	}

	public function get_keywords() {
		return array( 'woocommerce', 'shop', 'store', 'points', 'rewards', 'product' );
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_button',
			array(
				'label' => __( 'YITH Points and Rewards Product Points', 'yith-woocommerce-points-and-rewards' ),
			)
		);

		$this->add_control(
			'product',
			array(
				'label'       => __( 'Product ID', 'yith-woocommerce-request-a-quote' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => defined( 'ELEMENTOR_PRO_VERSION' ),
				),
				'default'     => __( '', 'yith-woocommerce-request-a-quote' ),
				'placeholder' => __( 'Leave empty for single product page', 'yith-woocommerce-request-a-quote' ),
			)
		);
		$this->add_control(
			'message',
			array(
				'label'   => __( 'Message', 'yith-woocommerce-points-and-rewards' ),
				'type'    => Controls_Manager::TEXTAREA,
				'dynamic' => array(
					'active' => false,
				),
				'default' => YITH_WC_Points_Rewards()->get_option( 'single_product_message' ),
			)
		);

		$this->add_control(
			'wc_style_warning',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => _x( '{points} number of points earned;<br>{points_label} label of points;<br>{price_discount_fixed_conversion} the value corresponding to points ', 'do not translate the text inside the brackets','yith-woocommerce-points-and-rewards' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		if ( ! isset( $settings['product'] ) || $settings['product'] == '' ) {
			global $product;
		} else {
			$product = wc_get_product( $settings['product'] );
		}
		$message   = $settings['message'];
		$shortcode = do_shortcode( '[yith_points_product_message message="' . $message . '" product_id="' . $product->get_id() . '"]' );
		?>
		<div class="elementor-shortcode"><?php echo $shortcode; //phpcs:ignore ?></div>
		<?php

	}

}
