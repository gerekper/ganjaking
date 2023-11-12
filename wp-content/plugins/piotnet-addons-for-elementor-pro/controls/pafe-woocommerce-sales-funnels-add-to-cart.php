<?php
require_once( __DIR__ . '/controls-manager.php' );

class PAFE_Woocommerce_Sales_Funnels_Add_To_Cart extends \Elementor\Widget_Base {
	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-woocommerce-sales-funnels-add-to-cart';
	}

	public function pafe_register_controls( $element, $section_id ) {

		$element->start_controls_section(
			'pafe_woocommerce_sales_funnels_add_to_cart_section',
			[
				'label' => __( 'PAFE Woocommerce Sales Funnels Add To Cart', 'pafe' ),
				'tab' => PAFE_Controls_Manager::TAB_PAFE,
			]
		);

		$element->add_control(
			'pafe_woocommerce_sales_funnels_add_to_cart_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$element->add_control(
			'pafe_woocommerce_sales_funnels_add_to_cart_product_id',
			[
				'label' => __( 'Product ID* (Required)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'pafe_woocommerce_sales_funnels_add_to_cart_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_woocommerce_sales_funnels_add_to_cart_variation_id',
			[
				'label' => __( 'Variation ID', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'pafe_woocommerce_sales_funnels_add_to_cart_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_woocommerce_sales_funnels_add_to_cart_quantity',
			[
				'label' => __( 'Quantity* (Required)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 1,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'pafe_woocommerce_sales_funnels_add_to_cart_enable' => 'yes',
				],
			]
		);

		// $repeater = new \Elementor\Repeater();

		// $repeater->add_control(
		// 	'pafe_add_to_cart_checkbox_product_id',
		// 	[
		// 		'label' => __( 'Product ID* (Required)', 'pafe' ),
		// 		'type' => \Elementor\Controls_Manager::TEXT,
		// 		'dynamic' => [
		// 			'active' => true,
		// 		],
		// 	]
		// );

		// $repeater->add_control(
		// 	'pafe_add_to_cart_checkbox_quantity',
		// 	[
		// 		'label' => __( 'Quantity* (Required)', 'pafe' ),
		// 		'type' => \Elementor\Controls_Manager::NUMBER,
		// 		'default' => 1,
		// 		'dynamic' => [
		// 			'active' => true,
		// 		],
		// 	]
		// );

		// $repeater->add_control(
		// 	'pafe_add_to_cart_checkbox_auto_get_all_product_variations',
		// 	[
		// 		'label' => __( 'Auto get all product variations', 'pafe' ),
		// 		'type' => \Elementor\Controls_Manager::SWITCHER,
		// 		'default' => 'yes',
		// 		'label_on' => 'Yes',
		// 		'label_off' => 'No',
		// 		'return_value' => 'yes',
		// 	]
		// );

		// $repeater->add_control(
		// 	'pafe_add_to_cart_checkbox_variation_id',
		// 	[
		// 		'label' => __( 'Variation ID', 'pafe' ),
		// 		'type' => \Elementor\Controls_Manager::TEXT,
		// 		'dynamic' => [
		// 			'active' => true,
		// 		],
		// 		'condition' => [
		// 			'pafe_add_to_cart_checkbox_auto_get_all_product_variations' => '',
		// 		],
		// 	]
		// );

		// $repeater->add_control(
		// 	'pafe_add_to_cart_checkbox_label',
		// 	[
		// 		'label' => __( 'Label', 'pafe' ),
		// 		'type' => \Elementor\Controls_Manager::TEXT,
		// 		'dynamic' => [
		// 			'active' => true,
		// 		],
		// 		'condition' => [
		// 			'pafe_add_to_cart_checkbox_auto_get_all_product_variations' => '',
		// 		],
		// 	]
		// );

		$element->add_control(
			'pafe_woocommerce_sales_funnels_add_to_cart_message_success',
			[
				'label' => __( 'Success Message', 'pafe' ),
				'label_block' => true,
				'default' => __( 'This item has been added to your cart.','pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'pafe_woocommerce_sales_funnels_add_to_cart_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_woocommerce_sales_funnels_add_to_cart_message_success_color',
			[
				'label' => __( 'Success Message Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                ],
				'selectors' => [
					'{{WRAPPER}} .pafe-woocommerce-sales-funnels-add-to-cart-message--success' => 'color: {{VALUE}};',
				],
				'condition' => [
					'pafe_woocommerce_sales_funnels_add_to_cart_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_woocommerce_sales_funnels_add_to_cart_message_out_of_stock',
			[
				'label' => __( 'Out of stock Message', 'pafe' ),
				'label_block' => true,
				'default' => __( 'This item is out of stock.','pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'pafe_woocommerce_sales_funnels_add_to_cart_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_woocommerce_sales_funnels_add_to_cart_message_out_of_stock_color',
			[
				'label' => __( 'Out of stock Message Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                ],
				'selectors' => [
					'{{WRAPPER}} .pafe-woocommerce-sales-funnels-add-to-cart-message--out-of-stock' => 'color: {{VALUE}};',
				],
				'condition' => [
					'pafe_woocommerce_sales_funnels_add_to_cart_enable' => 'yes',
				],
			]
		);

		$element->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'pafe_woocommerce_sales_funnels_add_to_cart_message_typography',
				'label' => __( 'Message Typography', 'pafe' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_ACCENT,
                ],
				'selector' => '{{WRAPPER}} .pafe-woocommerce-sales-funnels-add-to-cart-message',
			]
		);

		$element->add_responsive_control(
			'pafe_woocommerce_sales_funnels_add_to_cart_message_padding',
			[
				'label' => __( 'Message Padding', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .pafe-woocommerce-sales-funnels-add-to-cart-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_woocommerce_sales_funnels_add_to_cart_message_margin',
			[
				'label' => __( 'Message Margin', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .pafe-woocommerce-sales-funnels-add-to-cart-message' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$element->end_controls_section();

	}

	public function after_render_element($element) {
		$settings = $element->get_settings_for_display();
		if( !empty($settings['pafe_woocommerce_sales_funnels_add_to_cart_enable']) && !empty($settings['pafe_woocommerce_sales_funnels_add_to_cart_product_id']) && !empty($settings['pafe_woocommerce_sales_funnels_add_to_cart_quantity']) ) {
			$options = array(
				'product_id' => $settings['pafe_woocommerce_sales_funnels_add_to_cart_product_id'],
				'quantity' => $settings['pafe_woocommerce_sales_funnels_add_to_cart_quantity'],
				'variation_id' => $settings['pafe_woocommerce_sales_funnels_add_to_cart_variation_id'],
				'message_success' => $settings['pafe_woocommerce_sales_funnels_add_to_cart_message_success'],
				'message_out_of_stock' => $settings['pafe_woocommerce_sales_funnels_add_to_cart_message_out_of_stock'],
			);

			$element->add_render_attribute( '_wrapper', [
				'data-pafe-woocommerce-sales-funnels-add-to-cart' => json_encode($options),
			] );
		}
	}

	protected function init_control() {
		add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/container/section_layout/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/common/_section_background/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/section/before_render', [ $this, 'after_render_element'], 10, 1 );
		add_action( 'elementor/frontend/container/before_render', [ $this, 'after_render_element'], 10, 1 );
		add_action( 'elementor/frontend/column/before_render', [ $this, 'after_render_element'], 10, 1 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'after_render_element'], 10, 1 );
	}

}
