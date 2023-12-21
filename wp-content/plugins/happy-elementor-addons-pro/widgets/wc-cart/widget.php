<?php
/**
 * WooCommerce cart widget class
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

class WC_Cart extends Base {

	/**
	 * Retrieve toggle widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'WC Cart', 'happy-addons-pro' );
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
		return [ 'woo', 'commerce', 'ecommerce', 'cart', 'checkout', 'shop' ];
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
			'hide_coupon',
			[
				'label'					=> __( 'Hide Coupon Field', 'happy-addons-pro' ),
				'type'					=> Controls_Manager::SWITCHER,
				'return_value'			=> 'yes',
				'frontend_available'    => true,
			]
		);

		$this->add_control(
			'hide_cross_sells',
			[
				'label'					=> __( 'Hide Cross Sells', 'happy-addons-pro' ),
				'type'					=> Controls_Manager::SWITCHER,
				'return_value'			=> 'yes',
				'frontend_available'    => true,
			]
		);

		$this->end_controls_section();
	}

	/**
     * Register widget style controls
     */
	protected function register_style_controls() {
		$this->___cart_table_style_controls();
		$this->___coupon_style_controls();
		$this->___update_cart_button_style_controls();
		$this->___totals_heading_style_controls();
		$this->___cart_totals_style_controls();
		$this->___checkout_button_style_controls();
		$this->___cross_sells_style_controls();
	}

	protected function ___cart_table_style_controls() {
		$this->start_controls_section(
			'_section_style_cart_table',
			[
				'label'                 => __( 'Cart Table', 'happy-addons-pro' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'_heading_cart_table',
			[
				'label'                 => __( 'Table', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'					=> 'section_cart_table_typography',
				'label'					=> __( 'Typography', 'happy-addons-pro' ),
				'selector'				=> '{{WRAPPER}} .woocommerce table.cart',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'section_cart_table_background',
				'types'                 => [ 'classic', 'gradient' ],
				'selector'              => '{{WRAPPER}} .woocommerce .cart',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'section_cart_table_border',
				'label'                 => __( 'Border', 'happy-addons-pro' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .woocommerce .cart',
			]
		);

		$this->add_control(
			'section_cart_table_border_radius',
			[
				'label'                 => __( 'Border Radius', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'					=> 'section_cart_table_box_shadow',
				'separator'				=> 'before',
				'selector'				=> '{{WRAPPER}} .woocommerce .cart',
			]
		);

		$this->add_control(
			'_heading_cart_table_head',
			[
				'label'                 => __( 'Table Head', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'					=> 'section_cart_table_head_typography',
				'label'					=> __( 'Typography', 'happy-addons-pro' ),
				'selector'				=> '{{WRAPPER}} .woocommerce table.cart thead th',
			]
		);

		$this->add_control(
			'section_review_order_table_head_text_color',
			[
				'label'                 => __( 'Text Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce table.cart thead th' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'section_review_order_table_head_background_color',
			[
				'label'                 => __( 'Background Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce table.cart thead th' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'_heading_cart_items',
			[
				'label'                 => __( 'Cart Items', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

        $this->add_control(
            'cart_items_row_separator_type',
            [
                'label'                 => __( 'Separator Type', 'happy-addons-pro' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'solid',
                'options'               => [
                    'none'		=> __( 'None', 'happy-addons-pro' ),
                    'solid'		=> __( 'Solid', 'happy-addons-pro' ),
                    'dotted'	=> __( 'Dotted', 'happy-addons-pro' ),
                    'dashed'	=> __( 'Dashed', 'happy-addons-pro' ),
                    'double'	=> __( 'Double', 'happy-addons-pro' ),
                ],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .woocommerce-cart-form table.cart td' => 'border-top-style: {{VALUE}};',
				],
            ]
        );

		$this->add_control(
			'cart_items_row_separator_color',
			[
				'label'                 => __( 'Separator Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .woocommerce-cart-form table.cart td' => 'border-top-color: {{VALUE}};',
				],
				'condition'             => [
					'cart_items_row_separator_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'cart_items_row_separator_size',
			[
				'label'                 => __( 'Separator Size', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => '',
				],
				'range'                => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .woocommerce-cart-form table.cart td' => 'border-top-width: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'cart_items_row_separator_type!' => 'none',
				],
			]
		);

		$this->start_controls_tabs( 'cart_items_rows_tabs_style' );

		$this->start_controls_tab(
			'cart_items_even_row',
			[
				'label'                 => __( 'Even Row', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'cart_items_even_row_text_color',
			[
				'label'                 => __( 'Text Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .cart_item:nth-child(2n) td' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_items_even_row_links_color',
			[
				'label'                 => __( 'Links Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .cart_item:nth-child(2n) a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_items_even_row_background_color',
			[
				'label'                 => __( 'Background Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .cart_item:nth-child(2n) td' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'cart_items_odd_row',
			[
				'label'                 => __( 'Odd Row', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'cart_items_odd_row_text_color',
			[
				'label'                 => __( 'Text Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .cart_item:nth-child(2n+1) td' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_items_odd_row_links_color',
			[
				'label'                 => __( 'Links Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .cart_item:nth-child(2n+1) a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_items_odd_row_background_color',
			[
				'label'                 => __( 'Background Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .cart_item:nth-child(2n+1) td' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'_heading_cart_table_image',
			[
				'label'                 => __( 'Image', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

		$this->add_responsive_control(
			'cart_items_image_width',
			[
				'label'                 => __( 'Width', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px' ],
				'default'               => [
					'size' => '',
				],
				'range'                => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce table.cart .product-thumbnail img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'cart_items_quantity_input_heading',
			[
				'label'                 => __( 'Quantity Input', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

		$this->add_responsive_control(
			'cart_items_quantity_input_width',
			[
				'label'                 => __( 'Width', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px' ],
				'default'               => [
					'size' => '',
				],
				'range'                => [
					'px' => [
						'min' => 20,
						'max' => 500,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .quantity .input-text' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'cart_items_quantity_input_padding',
			[
				'label'                 => __( 'Padding', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .quantity .input-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'cart_items_quantity_input_bg_color',
			[
				'label'                 => __( 'Background Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .quantity .input-text' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'cart_items_quantity_input_border',
				'label'                 => __( 'Border', 'happy-addons-pro' ),
				'selector'              => '{{WRAPPER}} .woocommerce .cart .quantity .input-text',
			]
		);

		$this->add_responsive_control(
			'cart_items_quantity_input_border_radius',
			[
				'label'                 => __( 'Border Radius', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .quantity .input-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'_heading_cart_table_product_remove',
			[
				'label'                 => __( 'Product Remove', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

		$this->add_control(
			'cart_items_remove_icon_color',
			[
				'label'                 => __( 'Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce table.cart .remove' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'cart_items_remove_icon_color_hover',
			[
				'label'                 => __( 'Hover Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce table.cart .remove:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'cart_items_remove_icon_bg_hover',
			[
				'label'                 => __( 'Hover Background', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce table.cart .remove:hover' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'cart_items_remove_icon_size',
			[
				'label'                 => __( 'Size', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', 'em' ],
				'default'               => [
					'size' => '',
				],
				'range'                => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce table.cart .remove' => 'font-size: {{SIZE}}{{UNIT}}; font-family: arial; display: flex; align-items: center; justify-content: center;',
				],
			]
		);

		$this->add_control(
			'_heading_cart_table_update_cart_row',
			[
				'label'                 => __( 'Update Cart Row', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

		$this->add_control(
			'cart_table_update_cart_row_bg',
			[
				'label'                 => __( 'Background', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce table.cart tr td.actions' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function ___coupon_style_controls() {
		$this->start_controls_section(
			'form_coupon_style',
			[
				'label'                 => __( 'Coupon', 'happy-addons-pro' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'form_coupon_input_heading',
			[
				'label'                 => __( 'Input', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'after',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'					=> 'form_coupon_input_typography',
				'label'					=> __( 'Typography', 'happy-addons-pro' ),
				'selector'				=> '{{WRAPPER}} .woocommerce .cart .coupon .input-text',
			]
		);

		$this->add_responsive_control(
			'form_coupon_input_width',
			[
				'label'                 => __( 'Input Width', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 130,
				],
				'range'					=> [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .coupon .input-text' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'form_coupon_input_height',
			[
				'label'                 => __( 'Input Height', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => '',
				],
				'range'					=> [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .coupon .input-text' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'form_coupon_input_padding',
			[
				'label'                 => __( 'Padding', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .coupon .input-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'form_coupon_input_border',
				'label'                 => __( 'Border', 'happy-addons-pro' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .woocommerce .cart .coupon .input-text',
			]
		);

		$this->add_control(
			'form_coupon_input_border_radius',
			[
				'label'                 => __( 'Border Radius', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .coupon .input-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'					=> 'form_coupon_input_box_shadow',
				'separator'				=> 'before',
				'selector'				=> '{{WRAPPER}} .woocommerce .cart .coupon .input-text',
			]
		);

		$this->start_controls_tabs( 'tabs_form_coupon_input_style' );

		$this->start_controls_tab(
			'tab_form_coupon_input_normal',
			[
				'label'                 => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'form_coupon_input_text_color',
			[
				'label'                 => __( 'Text Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .coupon .input-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'form_coupon_input_background_color',
			[
				'label'                 => __( 'Background Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .coupon .input-text' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_form_coupon_input_hover',
			[
				'label'                 => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'form_coupon_input_text_color_hover',
			[
				'label'                 => __( 'Text Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .coupon .input-text:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'form_coupon_input_background_color_hover',
			[
				'label'                 => __( 'Background Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .coupon .input-text:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'form_coupon_input_border_color_hover',
			[
				'label'                 => __( 'Border Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .coupon .input-text:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_form_coupon_input_focus',
			[
				'label'                 => __( 'Focus', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'form_coupon_input_text_color_focus',
			[
				'label'                 => __( 'Text Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .coupon .input-text:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'form_coupon_input_background_color_focus',
			[
				'label'                 => __( 'Background Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .coupon .input-text:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'form_coupon_input_border_color_focus',
			[
				'label'                 => __( 'Border Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .coupon .input-text:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'form_coupon_button_label_heading',
			[
				'label'                 => __( 'Coupon Button', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'form_coupon_button_typography',
                'label'                 => __( 'Typography', 'happy-addons-pro' ),
                'selector'              => '{{WRAPPER}} .woocommerce .cart .coupon .button',
            ]
        );

		$this->add_responsive_control(
			'form_coupon_button_width',
			[
				'label'                 => __( 'Width', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px' ],
				'default'               => [
					'size' => '',
				],
				'range'                => [
					'px' => [
						'min' => 50,
						'max' => 500,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .coupon .button' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'form_coupon_button_border_normal',
				'label'                 => __( 'Border', 'happy-addons-pro' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .woocommerce .cart .coupon .button',
			]
		);

		$this->add_control(
			'form_coupon_button_border_radius',
			[
				'label'                 => __( 'Border Radius', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .coupon .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'form_coupon_button_padding',
			[
				'label'                 => __( 'Padding', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .coupon .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->start_controls_tabs( 'tabs_form_coupon_button_style' );

        $this->start_controls_tab(
            'tab_form_coupon_button_normal',
            [
                'label'                 => __( 'Normal', 'happy-addons-pro' ),
            ]
        );

        $this->add_control(
            'form_coupon_button_bg_color_normal',
            [
                'label'                 => __( 'Background Color', 'happy-addons-pro' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .woocommerce .cart .coupon .button' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'form_coupon_button_text_color_normal',
            [
                'label'                 => __( 'Text Color', 'happy-addons-pro' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .woocommerce .cart .coupon .button' => 'color: {{VALUE}}',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'form_coupon_button_box_shadow',
				'selector'              => '{{WRAPPER}} .woocommerce .cart .coupon .button',
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_form_coupon_button_hover',
            [
                'label'                 => __( 'Hover', 'happy-addons-pro' ),
            ]
        );

        $this->add_control(
            'form_coupon_button_bg_color_hover',
            [
                'label'                 => __( 'Background Color', 'happy-addons-pro' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .woocommerce .cart .coupon .button:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'form_coupon_button_text_color_hover',
            [
                'label'                 => __( 'Text Color', 'happy-addons-pro' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .woocommerce .cart .coupon .button:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'form_coupon_button_border_color_hover',
            [
                'label'                 => __( 'Border Color', 'happy-addons-pro' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .woocommerce .cart .coupon .button:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'form_coupon_button_box_shadow_hover',
				'selector'              => '{{WRAPPER}} .woocommerce .cart .coupon .button:hover',
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
                'label'                 => __( 'Update Cart Button', 'happy-addons-pro' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'update_cart_button_typography',
                'label'                 => __( 'Typography', 'happy-addons-pro' ),
                'selector'              => '{{WRAPPER}} .woocommerce .cart .button[name="update_cart"]',
            ]
        );

		$this->add_responsive_control(
			'update_cart_button_width',
			[
				'label'                 => __( 'Width', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px' ],
				'default'               => [
					'size' => '',
				],
				'range'                => [
					'px' => [
						'min' => 50,
						'max' => 500,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .button[name="update_cart"]' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'update_cart_button_margin',
			[
				'label'                 => __( 'Margin', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
                'allowed_dimensions'    => 'vertical',
				'placeholder'           => [
					'top'      => '',
					'right'    => 'auto',
					'bottom'   => '',
					'left'     => 'auto',
				],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .button[name="update_cart"]' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'update_cart_button_border_normal',
				'label'                 => __( 'Border', 'happy-addons-pro' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .woocommerce .cart .button[name="update_cart"]',
			]
		);

		$this->add_control(
			'update_cart_button_border_radius',
			[
				'label'                 => __( 'Border Radius', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .button[name="update_cart"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'update_cart_button_padding',
			[
				'label'                 => __( 'Padding', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .button[name="update_cart"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->start_controls_tabs( 'tabs_update_cart_button_style' );

        $this->start_controls_tab(
            'tab_update_cart_button_normal',
            [
                'label'                 => __( 'Normal', 'happy-addons-pro' ),
            ]
        );

        $this->add_control(
            'update_cart_button_bg_color_normal',
            [
                'label'                 => __( 'Background Color', 'happy-addons-pro' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .woocommerce .cart .button[name="update_cart"]' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'update_cart_button_text_color_normal',
            [
                'label'                 => __( 'Text Color', 'happy-addons-pro' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .woocommerce .cart .button[name="update_cart"]' => 'color: {{VALUE}}',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'update_cart_button_box_shadow',
				'selector'              => '{{WRAPPER}} .woocommerce .cart .button[name="update_cart"]',
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_update_cart_button_hover',
            [
                'label'                 => __( 'Hover', 'happy-addons-pro' ),
            ]
        );

        $this->add_control(
            'update_cart_button_bg_color_hover',
            [
                'label'                 => __( 'Background Color', 'happy-addons-pro' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .woocommerce .cart .button[name="update_cart"]:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'update_cart_button_text_color_hover',
            [
                'label'                 => __( 'Text Color', 'happy-addons-pro' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .woocommerce .cart .button[name="update_cart"]:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'update_cart_button_border_color_hover',
            [
                'label'                 => __( 'Border Color', 'happy-addons-pro' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .woocommerce .cart .button[name="update_cart"]:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'update_cart_button_box_shadow_hover',
				'selector'              => '{{WRAPPER}} .woocommerce .cart .button[name="update_cart"]:hover',
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
				'label'                 => __( 'Cart Totals: Heading', 'happy-addons-pro' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'sections_headings_text_color',
			[
				'label'                 => __( 'Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart_totals > h2, {{WRAPPER}} .woocommerce .cross-sells > h2' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'					=> 'sections_headings_typography',
				'label'					=> __( 'Typography', 'happy-addons-pro' ),
				'selector'				=> '{{WRAPPER}} .woocommerce .cart_totals > h2, {{WRAPPER}} .woocommerce .cross-sells > h2',
			]
		);

		$this->add_responsive_control(
			'sections_headings_spacing',
			[
				'label'					=> __( 'Spacing', 'happy-addons-pro' ),
				'type'					=> Controls_Manager::SLIDER,
				'default'				=> [
					'size' => 5,
				],
				'range'					=> [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'				=> [
					'{{WRAPPER}} .woocommerce .cart_totals > h2, {{WRAPPER}} .woocommerce .cross-sells > h2' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function ___cart_totals_style_controls() {
		$this->start_controls_section(
			'_section_style_cart_totals',
			[
				'label'                 => __( 'Cart Totals: Table', 'happy-addons-pro' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'_heading_cart_totals_table',
			[
				'label'                 => __( 'Table', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'cart_totals_background',
			[
				'label'                 => __( 'Background Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart_totals .shop_table tr th' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .woocommerce .cart_totals .shop_table tr td' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_totals_border_type',
			[
				'label'   => __( 'Border Type', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'none'   => __( 'None', 'happy-addons-pro' ),
					'solid'  => __( 'Solid', 'happy-addons-pro' ),
					'dotted' => __( 'Dotted', 'happy-addons-pro' ),
					'dashed' => __( 'Dashed', 'happy-addons-pro' ),
					'double' => __( 'Double', 'happy-addons-pro' ),
				],
				'selectors'             => [
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
				'condition'             => [
					'cart_totals_border_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'cart_totals_border_size',
			[
				'label'   => __( 'Border Size', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => '',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart_totals .shop_table' => 'border-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-wc-cart .woocommerce .cart_totals .shop_table tr.order-total th' => 'border-top-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-wc-cart .woocommerce .cart_totals .shop_table tr.order-total td' => 'border-top-width: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'cart_totals_border_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'cart_totals_border_radius',
			[
				'label'                 => __( 'Border Radius', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart_totals .shop_table' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'					=> 'cart_totals_box_shadow',
				'separator'				=> 'before',
				'selector'				=> '{{WRAPPER}} .woocommerce .cart_totals .shop_table',
			]
		);

		$this->add_control(
			'cart_totals_text_heading',
			[
				'label'                 => __( 'Table Text', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                 => 'cart_totals_text_typography',
				'label'                => __( 'Typography', 'happy-addons-pro' ),
				'selector'             => '{{WRAPPER}} .woocommerce .cart_totals .shop_table',
			]
		);

		$this->add_control(
			'cart_totals_text_color',
			[
				'label'                 => __( 'Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart_totals .shop_table' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'_heading_cart_totals_table_heading',
			[
				'label'                 => __( 'Table Headings', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                 => 'cart_totals_headings_typography',
				'label'                => __( 'Typography', 'happy-addons-pro' ),
				'selector'             => '{{WRAPPER}} .woocommerce .cart_totals .shop_table th',
			]
		);

		$this->add_control(
			'cart_totals_headings_color',
			[
				'label'                 => __( 'Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
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
                'label'                 => __( 'Checkout Button', 'happy-addons-pro' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'checkout_button_typography',
                'label'                 => __( 'Typography', 'happy-addons-pro' ),
                'selector'              => '{{WRAPPER}} .woocommerce .cart_totals .checkout-button',
            ]
        );

		$this->add_responsive_control(
			'checkout_button_custom_width',
			[
				'label'                 => __( 'Width', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', '%' ],
				'default'               => [
					'size' => '',
				],
				'range'                => [
					'px' => [
						'min' => 50,
						'max' => 500,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart_totals .checkout-button' => 'width: {{SIZE}}{{UNIT}}; text-align: center;',
				],
			]
		);

        $this->add_responsive_control(
			'checkout_button_margin',
			[
				'label'                 => __( 'Margin', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
                'allowed_dimensions'    => 'vertical',
				'placeholder'           => [
					'top'      => '',
					'right'    => 'auto',
					'bottom'   => '',
					'left'     => 'auto',
				],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart_totals .checkout-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'checkout_button_border_normal',
				'label'                 => __( 'Border', 'happy-addons-pro' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .woocommerce .cart_totals .checkout-button',
			]
		);

		$this->add_control(
			'checkout_button_border_radius',
			[
				'label'                 => __( 'Border Radius', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart_totals .checkout-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'checkout_button_padding',
			[
				'label'                 => __( 'Padding', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart_totals .checkout-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->start_controls_tabs( 'tabs_checkout_button_style' );

        $this->start_controls_tab(
            'tab_checkout_button_normal',
            [
                'label'                 => __( 'Normal', 'happy-addons-pro' ),
            ]
        );

        $this->add_control(
            'checkout_button_bg_color_normal',
            [
                'label'                 => __( 'Background Color', 'happy-addons-pro' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .woocommerce .cart_totals .checkout-button' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'checkout_button_text_color_normal',
            [
                'label'                 => __( 'Text Color', 'happy-addons-pro' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .woocommerce .cart_totals .checkout-button' => 'color: {{VALUE}}',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'checkout_button_box_shadow',
				'selector'              => '{{WRAPPER}} .woocommerce .cart_totals .checkout-button',
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_checkout_button_hover',
            [
                'label'                 => __( 'Hover', 'happy-addons-pro' ),
            ]
        );

        $this->add_control(
            'checkout_button_bg_color_hover',
            [
                'label'                 => __( 'Background Color', 'happy-addons-pro' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .woocommerce .cart_totals .checkout-button:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'checkout_button_text_color_hover',
            [
                'label'                 => __( 'Text Color', 'happy-addons-pro' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .woocommerce .cart_totals .checkout-button:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'checkout_button_border_color_hover',
            [
                'label'                 => __( 'Border Color', 'happy-addons-pro' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .woocommerce .cart_totals .checkout-button:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'checkout_button_box_shadow_hover',
				'selector'              => '{{WRAPPER}} .woocommerce .cart_totals .checkout-button:hover',
			]
		);

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
	}

	protected function ___cross_sells_style_controls() {

        $this->start_controls_section(
            '_section_style_cross_sell',
            [
                'label'                 => __( 'Cross Sells', 'happy-addons-pro' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_control(
			'cross_sells_title_heading',
			[
				'label'                 => __( 'Title', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HEADING,
			]
		);

        $this->add_control(
            'cross_sells_title_color_normal',
            [
                'label'                 => __( 'Color', 'happy-addons-pro' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .woocommerce .cross-sells .woocommerce-loop-product__title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'cross_sells_title_color_hover',
            [
                'label'                 => __( 'Hover Color', 'happy-addons-pro' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .woocommerce .cross-sells .woocommerce-loop-product__title:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'					=> 'cross_sells_title_typography',
				'label'					=> __( 'Typography', 'happy-addons-pro' ),
				'selector'				=> '{{WRAPPER}} .woocommerce .cross-sells .woocommerce-loop-product__title',
			]
		);

		$this->add_control(
			'cross_sells_price_heading',
			[
				'label'                 => __( 'Price', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

        $this->add_control(
            'cross_sells_price_color',
            [
                'label'                 => __( 'Color', 'happy-addons-pro' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .woocommerce .cross-sells .price' => 'color: {{VALUE}}',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'					=> 'cross_sells_price_typography',
				'label'					=> __( 'Typography', 'happy-addons-pro' ),
				'selector'				=> '{{WRAPPER}} .woocommerce .cross-sells .price',
			]
		);

		$this->add_control(
			'cross_sells_button_heading',
			[
				'label'                 => __( 'Button', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'					=> 'cross_sells_button_typography',
				'label'					=> __( 'Typography', 'happy-addons-pro' ),
				'selector'				=> '{{WRAPPER}} .woocommerce .cross-sells .button',
			]
		);

		$this->start_controls_tabs( 'tabs_cross_sells_button' );

		$this->start_controls_tab(
			'tab_cross_sells_button_normal',
			[
				'label'                 => __( 'Normal', 'happy-addons-pro' ),
			]
		);

        $this->add_control(
            'cross_sells_button_color',
            [
                'label'                 => __( 'Color', 'happy-addons-pro' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .woocommerce .cross-sells .button' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'cross_sells_button_bg_color',
            [
                'label'                 => __( 'Background Color', 'happy-addons-pro' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .woocommerce .cross-sells .button' => 'background-color: {{VALUE}}',
                ],
            ]
        );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_cross_sells_hover',
			[
				'label'                 => __( 'Hover', 'happy-addons-pro' ),
			]
		);

        $this->add_control(
            'cross_sells_button_color_hover',
            [
                'label'                 => __( 'Color', 'happy-addons-pro' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .woocommerce .cross-sells .button:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'cross_sells_button_bg_color_hover',
            [
                'label'                 => __( 'Background Color', 'happy-addons-pro' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .woocommerce .cross-sells .button:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'cross_sells_sale_badge_heading',
			[
				'label'                 => __( 'Sale Badge', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

        $this->add_control(
            'cross_sells_sale_badge_color',
            [
                'label'                 => __( 'Color', 'happy-addons-pro' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .woocommerce .cross-sells .onsale' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'cross_sells_sale_badge_bg_color',
            [
                'label'                 => __( 'Background Color', 'happy-addons-pro' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .woocommerce .cross-sells .onsale' => 'background-color: {{VALUE}}',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'					=> 'cross_sells_sale_badge_typography',
				'label'					=> __( 'Typography', 'happy-addons-pro' ),
				'selector'				=> '{{WRAPPER}} .woocommerce .cross-sells .onsale',
			]
		);

        $this->end_controls_section();
	}

	protected static function _setup_env( $settings ) {
		if ( ! ha_elementor()->editor->is_edit_mode() ||
			! function_exists( 'WC' ) ||
			empty( WC()->cart ) ) {
			return;
		}

		if ( WC()->cart->get_cart_contents_count() < 1 ) {
			$products = wc_get_products( [
				'status' => [ 'publish' ],
				'type'   => [ 'simple' ],
				'return' => 'ids',
				'limit'  => 1,
			] );

			if ( ! empty( $products ) ) {
				WC()->cart->add_to_cart( $products[0], 1 );
			}
		}

	}

	public static function _apply_hook( $settings ) {
		if ( isset( $settings['hide_cross_sells'] ) && $settings['hide_cross_sells'] === 'yes' ) {
			remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
		}

		if ( isset( $settings['hide_coupon'] ) && $settings['hide_coupon'] === 'yes' ) {
			add_filter( 'woocommerce_coupons_enabled', '__return_false' );
		}
	}

	public static function _restore_env() {
		add_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
		remove_filter( 'woocommerce_coupons_enabled', '__return_false' );
	}

	public static function show_wc_missing_alert() {
		if ( current_user_can( 'activate_plugins' ) ) {
			printf(
				'<div %s>%s</div>',
				'style="margin: 1rem;padding: 1rem 1.25rem;border-left: 5px solid #f5c848;color: #856404;background-color: #fff3cd;"',
				__( 'WooCommerce is missing! Please install and activate WooCommerce.', 'happy-addons-pro' )
				);
		}
	}

	protected function render() {
		if ( ! function_exists( 'WC' ) ) {
			self::show_wc_missing_alert();
			return;
		}

		$settings = $this->get_settings_for_display();

		self::_setup_env( $settings );
		self::_apply_hook( $settings );

		echo ha_do_shortcode( 'woocommerce_cart' );

		self::_restore_env();
	}
}
