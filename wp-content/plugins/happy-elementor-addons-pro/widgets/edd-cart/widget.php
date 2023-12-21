<?php
/**
 * EDD cart widget class
 *
 * @package Happy_Addons_Pro
 */
namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

defined( 'ABSPATH' ) || die();

class EDD_Cart extends Base {

	/**
	 * Retrieve toggle widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'EDD Cart', 'happy-addons-pro' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'hm hm-cart';
	}

	public function get_keywords() {
		return [ 'edd', 'commerce', 'ecommerce', 'cart', 'checkout', 'shop' ];
	}

	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Register widget content controls
	 */
	protected function register_content_controls() {

		$this->start_controls_section(
			'_section_content_general',
			[
				'label' => __( 'General', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'cart_btn_type',
			[
				'label'              => __( 'Cart Button Type', 'happy-addons-pro' ),
				'type'               => Controls_Manager::CHOOSE,
				'options'            => [
					'text' => [
						'title' => __( 'Text', 'happy-addons-pro' ),
						'icon'  => 'eicon-text',
					],
					'icon' => [
						'title' => __( 'Icon', 'happy-addons-pro' ),
						'icon'  => 'eicon-library-upload',
					],
				],
				'default'            => 'text',
				'toggle'             => false,
				'frontend_available' => true,
			]
		);
		$this->add_control(
			'btn_text',
			[
				'label'              => __( 'Text', 'happy-addons-pro' ),
				'type'               => Controls_Manager::TEXT,
				'default'            => __( 'Remove', 'happy-addons-pro' ),
				'placeholder'        => __( 'Type your text here', 'happy-addons-pro' ),
				'frontend_available' => true,
				'condition'          => [
					'cart_btn_type' => 'text',
				],
			]
		);

		$this->add_control(
			'icon',
			[
				'label'              => __( 'Icon', 'happy-addons-pro' ),
				'type'               => Controls_Manager::ICONS,
				'default'            => [
					'value'   => 'fas fa-times-circle',
					'library' => 'solid',
				],
				'frontend_available' => true,
				'skin'				=> 'inline',
				'exclude_inline_options'=> ['svg'],
				'condition'          => [
					'cart_btn_type' => 'icon',
				],
			]
		);

		// $this->add_control(
		// 	'important_note',
		// 	[
		// 		'label'           => false,
		// 		'type'            => Controls_Manager::RAW_HTML,
		// 		'raw'             => __( '<strong>Note:</strong> EDD Cart widget doesn\'t have any useful content control.', 'happy-addons-pro' ),
		// 		'content_classes' => ' elementor-panel-alert elementor-panel-alert-warning',
		// 	]
		// );

		$this->end_controls_section();
	}

	/**
	 * Register widget style controls
	 */
	protected function register_style_controls() {
		$this->___cart_table_style_controls();
		$this->___totals_heading_style_controls();
		$this->___checkout_button_style_controls();
	}

	protected function ___cart_table_style_controls() {
		$this->start_controls_section(
			'_section_style_cart_table',
			[
				'label' => __( 'Cart Table', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// edd-cart-number-of-items
		$this->add_control(
			'_heading_cart_table_heading',
			[
				'label' => __( 'Heading', 'happy-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'section_cart_table_items_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-edd-table-wrap .edd-cart-number-of-items',
			]
		);

		$this->add_control(
			'cart_table_items_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .edd-cart-number-of-items' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_table_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .edd-cart-number-of-items' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'_heading_cart_table',
			[
				'label'     => __( 'Table', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		// $this->add_group_control(
		// 	Group_Control_Typography::get_type(),
		// 	[
		// 		'name'     => 'section_cart_table_typography',
		// 		'label'    => __( 'Typography', 'happy-addons-pro' ),
		// 		'selector' => '{{WRAPPER}} .ha-edd-table-wrap .edd-cart .edd-cart-item-title, {{WRAPPER}} .ha-edd-table-wrap .edd-cart .edd-cart-item-quantity, {{WRAPPER}} .ha-edd-table-wrap .edd-cart .edd-cart-item-price',
		// 	]
		// );

		// $this->add_group_control(
		// 	Group_Control_Background::get_type(),
		// 	[
		// 		'name'     => 'section_cart_table_background',
		// 		'types'    => [ 'classic', 'gradient' ],
		// 		'selector' => '{{WRAPPER}} .ha-edd-table-wrap .edd-cart',
		// 	]
		// );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'section_cart_table_border',
				'label'       => __( 'Border', 'happy-addons-pro' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .ha-edd-table-wrap .edd-cart',
			]
		);

		$this->add_responsive_control(
			'section_cart_table_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-edd-table-wrap .edd-cart' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'section_cart_table_box_shadow',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .ha-edd-table-wrap .edd-cart',
			]
		);

		// $this->add_control(
		// 	'_heading_cart_table_head',
		// 	[
		// 		'label'                 => __( 'Table Head', 'happy-addons-pro' ),
		// 		'type'                  => Controls_Manager::HEADING,
		// 		'separator'				=> 'before',
		// 	]
		// );

		$this->add_control(
			'_heading_cart_items',
			[
				'label'     => __( 'Cart Items', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'heading_cart_items_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .edd-cart .edd-cart-item',
			]
		);

		$this->add_control(
			'heading_cart_items_text_color',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .edd-cart .edd-cart-item' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_items_row_separator_type',
			[
				'label'     => __( 'Separator Type', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => [
					'none'   => __( 'None', 'happy-addons-pro' ),
					'solid'  => __( 'Solid', 'happy-addons-pro' ),
					'dotted' => __( 'Dotted', 'happy-addons-pro' ),
					'dashed' => __( 'Dashed', 'happy-addons-pro' ),
					'double' => __( 'Double', 'happy-addons-pro' ),
				],
				'selectors' => [
					'{{WRAPPER}} .edd-cart .edd-cart-item' => 'border-bottom-style: {{VALUE}};',
					'{{WRAPPER}} .edd-cart .cart_item.edd_total' => 'border-bottom-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_items_row_separator_color',
			[
				'label'     => __( 'Separator Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .edd-cart .edd-cart-item' => 'border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} .edd-cart .cart_item.edd_total' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'cart_items_row_separator_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'cart_items_row_separator_size',
			[
				'label'     => __( 'Separator Size', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => '',
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .edd-cart .edd-cart-item' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .edd-cart .cart_item.edd_total' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'cart_items_row_separator_type!' => 'none',
				],
			]
		);

		$this->start_controls_tabs( 'cart_items_rows_tabs_style' );

		$this->start_controls_tab(
			'cart_items_even_row',
			[
				'label' => __( 'Even Row', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'cart_items_even_row_text_color',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .edd-cart .edd-cart-item:nth-child(2n)' => 'color: {{VALUE}};',
				],
			]
		);

		// $this->add_control(
		// 	'cart_items_even_row_links_color',
		// 	[
		// 		'label'     => __( 'Links Color', 'happy-addons-pro' ),
		// 		'type'      => Controls_Manager::COLOR,
		// 		'selectors' => [
		// 			'{{WRAPPER}} .edd-cart .edd-cart-item:nth-child(2n) a' => 'color: {{VALUE}};',
		// 		],
		// 	]
		// );

		$this->add_control(
			'cart_items_even_row_background_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .edd-cart .edd-cart-item:nth-child(2n)' => 'background-color: {{VALUE}};',
				],
			]
		);

		// $this->add_group_control(
		// 	Group_Control_Typography::get_type(),
		// 	[
		// 		'name'     => 'cart_items_even_row_typo',
		// 		'label'    => __( 'Typography', 'happy-addons-pro' ),
		// 		'selector' => '{{WRAPPER}} .edd-cart .edd-cart-item:nth-child(2n)',
		// 	]
		// );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'cart_items_odd_row',
			[
				'label' => __( 'Odd Row', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'cart_items_odd_row_text_color',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .edd-cart .edd-cart-item:nth-child(2n+1)' => 'color: {{VALUE}};',
				],
			]
		);

		// $this->add_control(
		// 	'cart_items_odd_row_links_color',
		// 	[
		// 		'label'     => __( 'Links Color', 'happy-addons-pro' ),
		// 		'type'      => Controls_Manager::COLOR,
		// 		'selectors' => [
		// 			'{{WRAPPER}} .edd-cart .edd-cart-item:nth-child(2n+1) a' => 'color: {{VALUE}};',
		// 		],
		// 	]
		// );

		$this->add_control(
			'cart_items_odd_row_background_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .edd-cart .edd-cart-item:nth-child(2n+1)' => 'background-color: {{VALUE}};',
				],
			]
		);
		// $this->add_group_control(
		// 	Group_Control_Typography::get_type(),
		// 	[
		// 		'name'     => 'cart_items_odd_row_typo',
		// 		'label'    => __( 'Typography', 'happy-addons-pro' ),
		// 		'selector' => '{{WRAPPER}} .edd-cart .edd-cart-item:nth-child(2n+1)',
		// 	]
		// );

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'_heading_cart_table_product_remove',
			[
				'label'     => __( 'Product Remove', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'cart_items_remove_icon_size',
			[
				'label'      => __( 'Size', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'size' => '',
				],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .edd-cart .edd-cart-item a.edd-remove-from-cart' => 'font-size: {{SIZE}}{{UNIT}}; font-family: arial; display: flex; align-items: center; justify-content: center;',
				],
				'condition'  => [
					'cart_btn_type' => 'icon',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'cart_items_remove_text_typo',
				'label'     => __( 'Typography', 'happy-addons-pro' ),
				'selector'  => '{{WRAPPER}} .edd-cart .edd-cart-item a.edd-remove-from-cart',
				'condition' => [
					'cart_btn_type' => 'text',
				],
			]
		);

		$this->add_responsive_control(
			'cart_items_remove_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .edd-cart .edd-cart-item a.edd-remove-from-cart' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'cart_items_remove_margin',
			[
				'label'      => __( 'Margin', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .edd-cart .edd-cart-item a.edd-remove-from-cart' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'cart_items_remove_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .edd-cart .edd-cart-item a.edd-remove-from-cart' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs(
			'pr_style_tabs'
		);

		$this->start_controls_tab(
			'pr_style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'cart_items_remove_icon_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .edd-cart .edd-cart-item a.edd-remove-from-cart' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'cart_items_remove_icon_bg_normal',
			[
				'label'     => __( 'Background', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .edd-cart .edd-cart-item a.edd-remove-from-cart' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pr_style_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'cart_items_remove_icon_color_hover',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .edd-cart .edd-cart-item a.edd-remove-from-cart:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);
		$this->add_control(
			'cart_items_remove_icon_bg_hover',
			[
				'label'     => __( 'Background', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .edd-cart .edd-cart-item a.edd-remove-from-cart:hover' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}



	protected function ___update_cart_button_style_controls() {

		$this->start_controls_section(
			'_section_style_update_cart_button',
			[
				'label' => __( 'Update Cart Button', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'update_cart_button_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .woocommerce .cart .button[name="update_cart"]',
			]
		);

		$this->add_responsive_control(
			'update_cart_button_width',
			[
				'label'      => __( 'Width', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => '',
				],
				'range'      => [
					'px' => [
						'min' => 50,
						'max' => 500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce .cart .button[name="update_cart"]' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'update_cart_button_margin',
			[
				'label'              => __( 'Margin', 'happy-addons-pro' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px', 'em', '%' ],
				'allowed_dimensions' => 'vertical',
				'placeholder'        => [
					'top'    => '',
					'right'  => 'auto',
					'bottom' => '',
					'left'   => 'auto',
				],
				'selectors'          => [
					'{{WRAPPER}} .woocommerce .cart .button[name="update_cart"]' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'update_cart_button_border_normal',
				'label'       => __( 'Border', 'happy-addons-pro' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .woocommerce .cart .button[name="update_cart"]',
			]
		);

		$this->add_responsive_control(
			'update_cart_button_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce .cart .button[name="update_cart"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'update_cart_button_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce .cart .button[name="update_cart"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_update_cart_button_style' );

		$this->start_controls_tab(
			'tab_update_cart_button_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'update_cart_button_bg_color_normal',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce .cart .button[name="update_cart"]' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'update_cart_button_text_color_normal',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce .cart .button[name="update_cart"]' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'update_cart_button_box_shadow',
				'selector' => '{{WRAPPER}} .woocommerce .cart .button[name="update_cart"]',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_update_cart_button_hover',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'update_cart_button_bg_color_hover',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce .cart .button[name="update_cart"]:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'update_cart_button_text_color_hover',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce .cart .button[name="update_cart"]:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'update_cart_button_border_color_hover',
			[
				'label'     => __( 'Border Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .woocommerce .cart .button[name="update_cart"]:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'update_cart_button_box_shadow_hover',
				'selector' => '{{WRAPPER}} .woocommerce .cart .button[name="update_cart"]:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function ___totals_heading_style_controls() {
		$this->start_controls_section(
			'_section_style_totals_heading',
			[
				'label' => __( 'Cart Footer: Totals', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'cart_foolter_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .edd-cart .cart_item.edd-cart-meta.edd_total' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'sections_headings_text_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .edd-cart .cart_item.edd-cart-meta.edd_total' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sections_headings_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .edd-cart .cart_item.edd-cart-meta.edd_total',
			]
		);

		$this->end_controls_section();
	}

	protected function ___cart_totals_style_controls() {
		$this->start_controls_section(
			'_section_style_cart_totals',
			[
				'label' => __( 'Cart Totals: Table', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'_heading_cart_totals_table',
			[
				'label' => __( 'Table', 'happy-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'cart_totals_background',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce .cart_totals .shop_table tr th' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .woocommerce .cart_totals .shop_table tr td' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_totals_border_type',
			[
				'label'     => __( 'Border Type', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => [
					'none'   => __( 'None', 'happy-addons-pro' ),
					'solid'  => __( 'Solid', 'happy-addons-pro' ),
					'dotted' => __( 'Dotted', 'happy-addons-pro' ),
					'dashed' => __( 'Dashed', 'happy-addons-pro' ),
					'double' => __( 'Double', 'happy-addons-pro' ),
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce .cart_totals .shop_table' => 'border-style: {{VALUE}};',
					'{{WRAPPER}}.ha-wc-cart .woocommerce .cart_totals .shop_table tr.order-total th' => 'border-top-style: {{VALUE}};',
					'{{WRAPPER}}.ha-wc-cart .woocommerce .cart_totals .shop_table tr.order-total td' => 'border-top-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_totals_border_color',
			[
				'label'     => __( 'Border Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce .cart_totals .shop_table' => 'border-color: {{VALUE}};',
					'{{WRAPPER}}.ha-wc-cart .woocommerce .cart_totals .shop_table tr.order-total th' => 'border-top-color: {{VALUE}};',
					'{{WRAPPER}}.ha-wc-cart .woocommerce .cart_totals .shop_table tr.order-total td' => 'border-top-color: {{VALUE}};',
				],
				'condition' => [
					'cart_totals_border_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'cart_totals_border_size',
			[
				'label'     => __( 'Border Size', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => '',
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce .cart_totals .shop_table' => 'border-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-wc-cart .woocommerce .cart_totals .shop_table tr.order-total th' => 'border-top-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-wc-cart .woocommerce .cart_totals .shop_table tr.order-total td' => 'border-top-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'cart_totals_border_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'cart_totals_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce .cart_totals .shop_table' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'cart_totals_box_shadow',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .woocommerce .cart_totals .shop_table',
			]
		);

		$this->add_control(
			'cart_totals_text_heading',
			[
				'label'     => __( 'Table Text', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'cart_totals_text_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .woocommerce .cart_totals .shop_table',
			]
		);

		$this->add_control(
			'cart_totals_text_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce .cart_totals .shop_table' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'_heading_cart_totals_table_heading',
			[
				'label'     => __( 'Table Headings', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'cart_totals_headings_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .woocommerce .cart_totals .shop_table th',
			]
		);

		$this->add_control(
			'cart_totals_headings_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce .cart_totals .shop_table th' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function ___checkout_button_style_controls() {
		$this->start_controls_section(
			'_section_style_checkout_button',
			[
				'label' => __( 'Checkout Button', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'checkout_button_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .cart_item.edd_checkout a',
			]
		);

		// $this->add_responsive_control(
		// 	'checkout_button_custom_width',
		// 	[
		// 		'label'      => __( 'Width', 'happy-addons-pro' ),
		// 		'type'       => Controls_Manager::SLIDER,
		// 		'size_units' => [ 'px', '%' ],
		// 		'default'    => [
		// 			'size' => '',
		// 		],
		// 		'range'      => [
		// 			'px' => [
		// 				'min' => 50,
		// 				'max' => 500,
		// 			],
		// 			'%'  => [
		// 				'min' => 0,
		// 				'max' => 100,
		// 			],
		// 		],
		// 		'selectors'  => [
		// 			'{{WRAPPER}} .cart_item.edd_checkout a' => 'width: {{SIZE}}{{UNIT}}; text-align: center;',
		// 		],
		// 	]
		// );

		$this->add_responsive_control(
			'checkout_button_margin',
			[
				'label'              => __( 'Margin', 'happy-addons-pro' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px', 'em', '%' ],
				'allowed_dimensions' => 'vertical',
				'placeholder'        => [
					'top'    => '',
					'right'  => 'auto',
					'bottom' => '',
					'left'   => 'auto',
				],
				'selectors'          => [
					'{{WRAPPER}} .cart_item.edd_checkout a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'checkout_button_border_normal',
				'label'       => __( 'Border', 'happy-addons-pro' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .cart_item.edd_checkout a',
			]
		);

		$this->add_responsive_control(
			'checkout_button_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .cart_item.edd_checkout a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'checkout_button_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .cart_item.edd_checkout a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_checkout_button_style' );

		$this->start_controls_tab(
			'tab_checkout_button_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'checkout_button_bg_color_normal',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .cart_item.edd_checkout a' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'checkout_button_text_color_normal',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .cart_item.edd_checkout a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'checkout_button_box_shadow',
				'selector' => '{{WRAPPER}} .cart_item.edd_checkout a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_checkout_button_hover',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'checkout_button_bg_color_hover',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .cart_item.edd_checkout a:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'checkout_button_text_color_hover',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .cart_item.edd_checkout a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'checkout_button_border_color_hover',
			[
				'label'     => __( 'Border Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .cart_item.edd_checkout a:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'checkout_button_box_shadow_hover',
				'selector' => '{{WRAPPER}} .cart_item.edd_checkout a:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected static function _setup_env( $settings ) {
		if ( ! ha_elementor()->editor->is_edit_mode() || ! function_exists( 'EDD' ) || ! empty( EDD()->cart->get_quantity() ) ) {
			return;
		}

		if ( EDD()->cart->get_quantity() < 1 ) {
			$args = array(
				'fields'        => 'ids',
				'post_type'     => 'download',
				'no_found_rows' => true,
			);

			$products = get_posts( $args );

			if ( ! empty( $products ) ) {
				EDD()->cart->add( $products[0] );
				EDD()->cart->add( $products[0] );
			}
		}
	}

	public static function apply_hook( $settings ) {

	}

	public static function restore_env() {

	}

	public static function show_edd_missing_alert() {
		if ( current_user_can( 'activate_plugins' ) ) {
			printf(
				'<div %s>%s</div>',
				'style="margin: 1rem;padding: 1rem 1.25rem;border-left: 5px solid #f5c848;color: #856404;background-color: #fff3cd;"',
				__( 'Easy Digital Downloads is missing! Please install and activate Easy Digital Downloads.', 'happy-addons-pro' )
			);
		}
	}

	protected function render() {
		if ( ! function_exists( 'EDD' ) ) {
			self::show_edd_missing_alert();
			return;
		}

		$settings             = $this->get_settings_for_display();
		$cart_remove_settings = [
			'cart_btn_type' => $settings['cart_btn_type'],
			'btn_text'      => $settings['btn_text'],
			'icon'          => $settings['icon'],
		];

		$this->add_render_attribute( 'cart-settings', 'data-options', wp_json_encode( $cart_remove_settings ) );

		self::_setup_env( $settings );
		// self::apply_hook( $settings );

		echo '<div class="ha-edd-table-wrap" ' . $this->get_render_attribute_string( 'cart-settings' ) . '>';
			echo ha_do_shortcode( 'download_cart' );
		echo '</div>';

		// self::restore_env();
	}

	protected function render_cart_table( $settings ) {
		$cart_items    = edd_get_cart_contents();
		$cart_quantity = edd_get_cart_quantity();
		?>
			<ul class="edd-cart>">
			<?php if ( $cart_items ) : ?>
				<ul>
					<li class="product-thumbnail">Image</li>
					<li class="product-name">Product</li>
					<li class="product-price">Price</li>
					<li class="product-quantity">Quantity</li>
					<!-- <th class="product-subtotal">Subtotal</th> -->
					<li class="product-remove">Actions</li>
				</ul>

					<?php foreach ( $cart_items as $key => $item ) :

						$id         = is_array( $item ) ? $item['id'] : $item;
						$options    = ! empty( $item['options'] ) ? $item['options'] : array();
						$price      = edd_get_cart_item_price( $id, $options );
						$quantity   = edd_get_cart_item_quantity( $id, $options );
						$subtotal   = edd_currency_filter( edd_format_amount( edd_get_cart_subtotal() ) );
						$remove_url = edd_remove_item_url( $key );
						?>

					<li class="edd-cart-item">
						<span class="product-thumbnail">
							<?php echo get_the_post_thumbnail( $id, 'thumbnail' ); ?>				
						</span>
						<span class="product-name">
							<a href="<?php echo get_the_permalink( $id ); ?>"><?php echo get_the_title( $id ); ?></a>						
						</span>
						<span class="product-price" data-title="Price">
							<?php echo edd_currency_filter( edd_format_amount( $price ) ); ?>					
						</span>
						<span class="product-quantity" data-title="Quantity">
							<?php echo $quantity; ?>
						</span>
						<span class="product-remove">
							<a href="<?php echo esc_url( $remove_url ); ?>" data-nonce="<?php echo wp_create_nonce( 'edd-remove-cart-widget-item' ); ?>" data-cart-item="<?php echo esc_attr( $key ); ?>" data-download-id="<?php echo esc_attr( $id ); ?>" data-action="edd_remove_from_cart" class="edd-remove-from-cart">Remove</a>						
						</span>
					</li>
					<?php endforeach; ?>
					
				<?php else : ?>
					<?php echo edd_empty_cart_message(); ?>
				<?php endif; ?>
			</ul>
		<?php
	}
}
