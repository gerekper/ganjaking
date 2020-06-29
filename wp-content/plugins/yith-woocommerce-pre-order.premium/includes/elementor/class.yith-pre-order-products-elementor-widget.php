<?php


use Elementor\Controls_Manager;

class YITH_Pre_Order_Products_Elementor_Widget extends \Elementor\Widget_Base {


	public function get_name() {
		return 'yith-pre-order-products';
	}

	public function get_title() {
		return esc_html__( 'YITH Pre-Order - Show Pre-Order Products', 'yith-pre-order-for-woocommerce' );
	}

	public function get_icon() {
		return 'eicon-product-stock';
	}

	public function get_categories() {
		return [ 'yith', 'woocommerce-elements-single' ];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'pre-order', 'products', 'pagination', 'loop' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_button',
			array(
				'label' => esc_html__( 'YITH Pre-Order - Show Pre-Order Products', 'yith-pre-order-for-woocommerce' ),
			)
		);

		$this->add_control(
			'wc_style_warning',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'This widget shows the current Pre-Order products.', 'yith-pre-order-for-woocommerce' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'columns',
			array(
				'label' => esc_html__( 'Number of columns', 'yith-pre-order-for-woocommerce' ),
				'type' => Controls_Manager::TEXT,
				'default' => '4',
				'placeholder' => esc_html__( 'Enter a number', 'yith-pre-order-for-woocommerce' ),
			)
		);

		$orderby_options = array(
			'none' => esc_html__( 'No order', 'yith-pre-order-for-woocommerce' ),
			'ID' => esc_html__( 'Product ID', 'yith-pre-order-for-woocommerce' ),
			'author' => esc_html__( 'Author', 'yith-pre-order-for-woocommerce' ),
			'title' => esc_html__( 'Post title', 'yith-pre-order-for-woocommerce' ),
			'name' => esc_html__( 'Post name (post slug)', 'yith-pre-order-for-woocommerce' ),
			'date' => esc_html__( 'Date', 'yith-pre-order-for-woocommerce' ),
			'modified' => esc_html__( 'Last modified date', 'yith-pre-order-for-woocommerce' ),
			'rand' => esc_html__( 'Random order', 'yith-pre-order-for-woocommerce' ),
			'comment_count' => esc_html__( 'Number of comments', 'yith-pre-order-for-woocommerce' ),
		);

		$this->add_control(
			'orderby',
			array(
				'label' => esc_html__( 'Order by:', 'yith-pre-order-for-woocommerce' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'title',
				'options' => $orderby_options,
			)
		);

		$order_options = array(
			'asc' => esc_html__( 'Ascending', 'yith-pre-order-for-woocommerce' ),
			'desc' => esc_html__( 'Descending', 'yith-pre-order-for-woocommerce' ),
		);

		$this->add_control(
			'order',
			array(
				'label' => esc_html__( 'Order', 'yith-pre-order-for-woocommerce' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'asc',
				'options' => $order_options,
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label' => esc_html__( 'Posts per page', 'yith-pre-order-for-woocommerce' ),
				'type' => Controls_Manager::TEXT,
				'default' => '8',
				'placeholder' => esc_html__( 'Enter a number', 'yith-pre-order-for-woocommerce' ),
			)
		);

		$this->add_control(
			'show_variable',
			array(
				'label' => esc_html__( 'Show variable products', 'yith-pre-order-for-woocommerce' ),
				'type' => Controls_Manager::SWITCHER,
			)
		);

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( class_exists( 'YITH_Pre_Order_Frontend_Premium' ) ) {
			wp_enqueue_style( 'wcpo-frontend', YITH_WCPO_ASSETS_URL . 'css/frontend.css', array(), YITH_WCPO_VERSION );
			wp_enqueue_script( 'yith-wcpo-frontend-single-product' );
			$atts = array(
				'columns'        => ! empty( $settings['columns'] ) ? $settings['columns'] : '4',
				'orderby'        => ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'title',
				'order'          => ! empty( $settings['order'] ) ? $settings['order'] : 'asc',
				'posts_per_page' => ! empty( $settings['posts_per_page'] ) ? $settings['posts_per_page'] : '8',
				'show_variable'  => ! empty( $settings['show_variable'] && 'yes' == $settings['show_variable'] ) ? true : false
			);
			echo YITH_Pre_Order_Premium::instance()->frontend->pre_order_products_loop( $atts );
		}
	}

}
