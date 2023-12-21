<?php
/**
 * Easy Digital Downloads checkout widget class
 *
 * @package Happy_Addons_Pro
 */
namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

defined( 'ABSPATH' ) || die();

class EDD_Checkout extends Base {

	/**
	 * Retrieve toggle widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'EDD Checkout', 'happy-addons-pro' );
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
		return 'hm hm-checkout-2';
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
			'_section_general',
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
		// 		'raw'             => __( '<strong>Note:</strong> EDD Checkout widget doesn\'t have any useful content control.', 'happy-addons-pro' ),
		// 		'content_classes' => ' elementor-panel-alert elementor-panel-alert-warning',
		// 	]
		// );

		$this->end_controls_section();
	}

	/**
	 * Register widget style controls
	 */
	protected function register_style_controls() {
		// $this->__sections_style_controls();
		$this->___checkout_table_style_controls();
		$this->__discount_style_controls();
		$this->__headings_style_controls();
		$this->__labels_style_controls();
		$this->__inputs_style_controls();
		$this->purchase_total();
		$this->__button_style_controls();
		// $this->___update_checkout_button_style_controls();

	}

	protected function __sections_style_controls() {
		$this->start_controls_section(
			'_section_style_sections',
			[
				'label' => __( 'Sections', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'sections_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #edd_checkout_wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'sections_bg',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} #edd_checkout_wrap',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'sections_border',
				'selector' => '{{WRAPPER}} #edd_checkout_wrap',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'sections_box_shadow',
				'selector' => '{{WRAPPER}} #edd_checkout_wrap',
			]
		);

		$this->end_controls_section();
	}

	protected function ___checkout_table_style_controls() {
		$this->start_controls_section(
			'_section_style_cart_table',
			[
				'label' => __( 'Checkout Cart Table', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'_heading_cart_table',
			[
				'label' => __( 'Table', 'happy-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		// $this->add_group_control(
		// 	Group_Control_Typography::get_type(),
		// 	[
		// 		'name'     => 'section_cart_table_typography',
		// 		'label'    => __( 'Typography', 'happy-addons-pro' ),
		// 		'selector' => '{{WRAPPER}} #edd_checkout_cart',
		// 	]
		// );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'section_cart_table_background',
				'types'    => [ 'classic', 'gradient' ],
				'exclude'	=> ['image'],
				'selector' => '{{WRAPPER}} #edd_checkout_cart',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'section_cart_table_border',
				'label'       => __( 'Border', 'happy-addons-pro' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} #edd_checkout_cart',
			]
		);

		$this->add_responsive_control(
			'section_cart_table_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #edd_checkout_cart' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'section_cart_table_box_shadow',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} #edd_checkout_cart',
			]
		);

		$this->add_control(
			'_heading_cart_table_head',
			[
				'label'     => __( 'Table Head', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'section_cart_table_head_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} #edd_checkout_cart thead th',
			]
		);

		$this->add_control(
			'section_review_order_table_head_text_color',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart thead th' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'section_review_order_table_head_background_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart thead th' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'_heading_cart_items',
			[
				'label'     => __( 'Cart Items', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
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
					'{{WRAPPER}} #edd_checkout_cart td' => 'border-bottom-style: {{VALUE}};',
					'{{WRAPPER}} #edd_checkout_cart th' => 'border-bottom-style: {{VALUE}};',
					'{{WRAPPER}} #edd_checkout_cart td:not(:last-child)' => 'border-right-style: {{VALUE}};',
					'{{WRAPPER}} #edd_checkout_cart th:not(:last-child)' => 'border-right-style: {{VALUE}};',
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
					'{{WRAPPER}} #edd_checkout_cart td' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} #edd_checkout_cart th' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} #edd_checkout_cart td:not(:last-child)' => 'border-right-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} #edd_checkout_cart th:not(:last-child)' => 'border-right-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'cart_items_row_separator_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'cart_items_row_separator_color',
			[
				'label'     => __( 'Separator Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart td' => 'border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} #edd_checkout_cart th' => 'border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} #edd_checkout_cart td:not(:last-child)' => 'border-right-color: {{VALUE}};',
					'{{WRAPPER}} #edd_checkout_cart th:not(:last-child)' => 'border-right-color: {{VALUE}};',
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
					'{{WRAPPER}} #edd_checkout_cart .edd_cart_item:nth-child(2n) td' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_items_even_row_links_color',
			[
				'label'     => __( 'Links Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart .edd_cart_item:nth-child(2n) a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_items_even_row_background_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart .edd_cart_item:nth-child(2n) td' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'cart_items_even_row_typo',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} #edd_checkout_cart .edd_cart_item:nth-child(2n) td',
			]
		);

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
					'{{WRAPPER}} #edd_checkout_cart .edd_cart_item:nth-child(2n+1) td' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_items_odd_row_links_color',
			[
				'label'     => __( 'Links Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart .edd_cart_item:nth-child(2n+1) a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_items_odd_row_background_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart .edd_cart_item:nth-child(2n+1) td' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'cart_items_odd_row_typo',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} #edd_checkout_cart .edd_cart_item:nth-child(2n+1) td',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'_heading_cart_table_image',
			[
				'label'     => __( 'Image', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'cart_items_image_width',
			[
				'label'      => __( 'Width', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => '',
				],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} #edd_checkout_cart td img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		if( function_exists( 'edd_get_option' ) && edd_get_option( 'item_quantities', false ) ){

			$this->add_control(
				'cart_items_quantity_input_heading',
				[
					'label'     => __( 'Quantity Input', 'happy-addons-pro' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'cart_items_quantity_input_width',
				[
					'label'      => __( 'Width', 'happy-addons-pro' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'default'    => [
						'size' => '',
					],
					'range'      => [
						'px' => [
							'min' => 20,
							'max' => 500,
						],
					],
					'selectors'  => [
						'{{WRAPPER}} #edd_checkout_cart input.edd-item-quantity' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'cart_items_quantity_input_padding',
				[
					'label'      => __( 'Padding', 'happy-addons-pro' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} #edd_checkout_cart input.edd-item-quantity' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'cart_items_quantity_input_bg_color',
				[
					'label'     => __( 'Background Color', 'happy-addons-pro' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} #edd_checkout_cart input.edd-item-quantity' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'     => 'cart_items_quantity_input_border',
					'label'    => __( 'Border', 'happy-addons-pro' ),
					'selector' => '{{WRAPPER}} #edd_checkout_cart input.edd-item-quantity',
				]
			);

			$this->add_responsive_control(
				'cart_items_quantity_input_border_radius',
				[
					'label'      => __( 'Border Radius', 'happy-addons-pro' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} #edd_checkout_cart input.edd-item-quantity' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'cart_items_quantity_input_space',
				[
					'label'      => __( 'Space', 'happy-addons-pro' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'default'    => [
						'size' => '',
					],
					'range'      => [
						'px' => [
							'min' => 20,
							'max' => 500,
						],
					],
					'selectors'  => [
						'{{WRAPPER}} #edd_checkout_cart input.edd-item-quantity' => 'margin-right: {{SIZE}}{{UNIT}};',
					],
				]
			);
		}

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
					'{{WRAPPER}} #edd_checkout_cart a.edd_cart_remove_item_btn' => 'font-size: {{SIZE}}{{UNIT}}; font-family: arial; display: flex; align-items: center; justify-content: center;',
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
				'selector'  => '{{WRAPPER}} #edd_checkout_cart a.edd_cart_remove_item_btn',
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
					'{{WRAPPER}} #edd_checkout_cart a.edd_cart_remove_item_btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} #edd_checkout_cart a.edd_cart_remove_item_btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} #edd_checkout_cart a.edd_cart_remove_item_btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} #edd_checkout_cart a.edd_cart_remove_item_btn' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'cart_items_remove_icon_bg_normal',
			[
				'label'     => __( 'Background', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart a.edd_cart_remove_item_btn' => 'background-color: {{VALUE}} !important;',
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
					'{{WRAPPER}} #edd_checkout_cart a.edd_cart_remove_item_btn:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);
		$this->add_control(
			'cart_items_remove_icon_bg_hover',
			[
				'label'     => __( 'Background', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart a.edd_cart_remove_item_btn:hover' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		if( function_exists( 'edd_get_option' ) && edd_get_option( 'item_quantities', false ) ){
			$this->add_control(
				'_heading_cart_update_btn',
				[
					'label'     => __( 'Update Cart Button', 'happy-addons-pro' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'      => 'update_cart_btn_text_typo',
					'label'     => __( 'Typography', 'happy-addons-pro' ),
					'selector'  => '{{WRAPPER}} input.edd-submit.button.blue[name="edd_update_cart_submit"]',
				]
			);
	
			$this->add_responsive_control(
				'update_cart_btn_padding',
				[
					'label'      => __( 'Padding', 'happy-addons-pro' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} input.edd-submit.button.blue[name="edd_update_cart_submit"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'update_cart_btn_border',
					'label' => esc_html__( 'Border', 'happy-addons-pro' ),
					'selector' => '{{WRAPPER}} input.edd-submit.button.blue[name="edd_update_cart_submit"]',
				]
			);
	
			$this->add_responsive_control(
				'update_cart_btn_border_radius',
				[
					'label'      => __( 'Border Radius', 'happy-addons-pro' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} input.edd-submit.button.blue[name="edd_update_cart_submit"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
	
			$this->start_controls_tabs(
				'ucb_style_tabs'
			);
	
			$this->start_controls_tab(
				'ucb_style_normal_tab',
				[
					'label' => esc_html__( 'Normal', 'happy-addons-pro' ),
				]
			);
	
			$this->add_control(
				'update_cart_btn_icon_color',
				[
					'label'     => __( 'Color', 'happy-addons-pro' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} input.edd-submit.button.blue[name="edd_update_cart_submit"]' => 'color: {{VALUE}} !important;',
					],
				]
			);
	
			$this->add_control(
				'update_cart_btn__bg_normal',
				[
					'label'     => __( 'Background', 'happy-addons-pro' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} input.edd-submit.button.blue[name="edd_update_cart_submit"]' => 'background-color: {{VALUE}} !important;',
					],
				]
			);
	
			$this->end_controls_tab();
	
			$this->start_controls_tab(
				'ucb_style_hover_tab',
				[
					'label' => esc_html__( 'Hover', 'happy-addons-pro' ),
				]
			);
	
			$this->add_control(
				'update_cart_btn__color_hover',
				[
					'label'     => __( 'Color', 'happy-addons-pro' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} input.edd-submit.button.blue[name="edd_update_cart_submit"]:hover' => 'color: {{VALUE}} !important;',
					],
				]
			);
			$this->add_control(
				'update_cart_btn__bg_hover',
				[
					'label'     => __( 'Background', 'happy-addons-pro' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} input.edd-submit.button.blue[name="edd_update_cart_submit"]:hover' => 'background-color: {{VALUE}} !important;',
					],
				]
			);
			$this->add_control(
				'update_cart_btn__border_hover',
				[
					'label'     => __( 'Border Color', 'happy-addons-pro' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} input.edd-submit.button.blue[name="edd_update_cart_submit"]:hover' => 'border-color: {{VALUE}} !important;',
					],
				]
			);
	
			$this->end_controls_tab();
	
			$this->end_controls_tabs();
		} //Condition end for Update cart button

		$this->add_control(
			'_heading_cart_table_footer',
			[
				'label'     => __( 'Table Footer', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'cart_table_footer_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} #edd_checkout_cart th.edd_cart_total',
			]
		);

		$this->add_control(
			'cart_table_footer_text_color',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart th.edd_cart_total' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_table_footer_background_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart th.edd_cart_total' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'cart_table_footer_border',
				'selector' => '{{WRAPPER}} #edd_checkout_cart th.edd_cart_total',
			]
		);

		// $this->add_control(
		// 	'_heading_cart_table_update_cart_row',
		// 	[
		// 		'label'                 => __( 'Update Cart Row', 'happy-addons-pro' ),
		// 		'type'                  => Controls_Manager::HEADING,
		// 		'separator'				=> 'before',
		// 	]
		// );

		// $this->add_control(
		// 	'cart_table_update_cart_row_bg',
		// 	[
		// 		'label'                 => __( 'Background', 'happy-addons-pro' ),
		// 		'type'                  => Controls_Manager::COLOR,
		// 		'selectors'             => [
		// 			'{{WRAPPER}} .woocommerce table.cart tr td.actions' => 'background-color: {{VALUE}} !important;',
		// 		],
		// 	]
		// );

		$this->end_controls_section();
	}

	protected function purchase_total()
	{
		$this->start_controls_section(
			'_section_purchase_total',
			[
				'label' => __( 'Purchase Total', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// $this->add_control(
		// 	'_heading_cart_table_pt',
		// 	[
		// 		'label'     => __( 'Purchase Total', 'happy-addons-pro' ),
		// 		'type'      => Controls_Manager::HEADING,
		// 		'separator' => 'before',
		// 	]
		// );

		$this->add_control(
			'cart_table_pt_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap #edd_final_total_wrap' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'cart_table_pt_typo',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} #edd_checkout_form_wrap #edd_final_total_wrap',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'cart_table_pt_bg',
				'types'    => [ 'classic', 'gradient' ],
				'exclude'	=> ['image'],
				'selector' => '{{WRAPPER}} #edd_checkout_form_wrap #edd_final_total_wrap',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'cart_table_pt_border',
				'selector' => '{{WRAPPER}} #edd_checkout_form_wrap #edd_final_total_wrap',
			]
		);

		$this->add_control(
			'cart_table_footer_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'happy-addons-pro' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap #edd_final_total_wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __inputs_style_controls() {
		$this->start_controls_section(
			'_section_style_inputs',
			[
				'label' => __( 'Inputs', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'inputs_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #edd_checkout_form_wrap input.edd-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'inputs_height',
			[
				'label'     => __( 'Input Height', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap input.edd-input, {{WRAPPER}} .woocommerce .form-row select' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		

		$this->add_responsive_control(
			'inputs_gap',
			[
				'label'     => __( 'Spacing', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap input.edd-input, {{WRAPPER}} .woocommerce form select' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'inputs_border',
				'selector' => '{{WRAPPER}} #edd_checkout_form_wrap input.edd-input, {{WRAPPER}} .woocommerce form select',
			]
		);

		$this->add_control(
			'inputs_text_align',
			[
				'label'       => __( 'Text Alignment', 'happy-addons-pro' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'left'   => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'     => 'left',
				'selectors'   => [
					'{{WRAPPER}} #edd_checkout_form_wrap input.edd-input, {{WRAPPER}} .woocommerce form select' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_text_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap input.edd-input, {{WRAPPER}} .woocommerce form select' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_background_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap input.edd-input, {{WRAPPER}} .woocommerce form select' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'inputs_typography',
				'selector' => '{{WRAPPER}} #edd_checkout_form_wrap input.edd-input, {{WRAPPER}} .woocommerce form select',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'inputs_box_shadow',
				'selector' => '{{WRAPPER}} #edd_checkout_form_wrap input.edd-input, {{WRAPPER}} .woocommerce form select',
			]
		);

		$this->end_controls_section();
	}

	protected function __labels_style_controls() {
		$this->start_controls_section(
			'_section_style_labels',
			[
				'label' => __( 'Labels', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'labels_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #edd_checkout_form_wrap label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		

		$this->add_responsive_control(
			'labels_gap',
			[
				'label'     => __( 'Spacing', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'labels_border',
				'selector' => '{{WRAPPER}} #edd_checkout_form_wrap label',
			]
		);

		$this->add_control(
			'labels_text_align',
			[
				'label'       => __( 'Text Alignment', 'happy-addons-pro' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'left'   => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'     => 'left',
				'selectors'   => [
					'{{WRAPPER}} #edd_checkout_form_wrap label, {{WRAPPER}} .woocommerce form select' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'label_text_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap #edd_checkout_user_info label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'label_background_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap #edd_checkout_user_info label' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'labels_typography',
				'selector' => '{{WRAPPER}} #edd_checkout_form_wrap #edd_checkout_user_info label',
			]
		);


		$this->add_control(
			'_labels_description_head',
			[
				'label'     => __( 'Label Description', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'labels_description_typography',
				'selector' => '{{WRAPPER}} #edd_checkout_form_wrap  #edd_checkout_user_info span.edd-description',
			]
		);

		$this->add_control(
			'label_description_text_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap #edd_checkout_user_info span.edd-description' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'labels_description_gap',
			[
				'label'     => __( 'Spacing', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap #edd_checkout_user_info span.edd-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function ___update_checkout_button_style_controls() {

		$this->start_controls_section(
			'_section_style_update_checkout_button',
			[
				'label' => __( 'Update Cart Button', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'update_checkout_button_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} #edd_checkout_cart input[name="edd_update_cart_submit"]',
			]
		);

		$this->add_responsive_control(
			'update_checkout_button_width',
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
					'{{WRAPPER}} #edd_checkout_cart input[name="edd_update_cart_submit"]' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'update_checkout_button_margin',
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
					'{{WRAPPER}} #edd_checkout_cart input[name="edd_update_cart_submit"]' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'update_checkout_button_border_normal',
				'label'       => __( 'Border', 'happy-addons-pro' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} #edd_checkout_cart input[name="edd_update_cart_submit"]',
			]
		);

		$this->add_control(
			'update_checkout_button_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #edd_checkout_cart input[name="edd_update_cart_submit"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'update_checkout_button_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #edd_checkout_cart input[name="edd_update_cart_submit"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_update_checkout_button_style' );

		$this->start_controls_tab(
			'tab_update_checkout_button_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'update_checkout_button_bg_color_normal',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart input[name="edd_update_cart_submit"]' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'update_checkout_button_text_color_normal',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart input[name="edd_update_cart_submit"]' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'update_checkout_button_box_shadow',
				'selector' => '{{WRAPPER}} #edd_checkout_cart input[name="edd_update_cart_submit"]',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_update_checkout_button_hover',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'update_checkout_button_bg_color_hover',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart input[name="edd_update_cart_submit"]:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'update_checkout_button_text_color_hover',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart input[name="edd_update_cart_submit"]:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'update_checkout_button_border_color_hover',
			[
				'label'     => __( 'Border Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_cart input[name="edd_update_cart_submit"]:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'update_checkout_button_box_shadow_hover',
				'selector' => '{{WRAPPER}} #edd_checkout_cart input[name="edd_update_cart_submit"]:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}
	protected function __discount_style_controls() {
		$this->start_controls_section(
			'_section_style_discount',
			[
				'label' => __( 'Discount', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'_heading_show_discount_label',
			[
				'label' => __( 'Show Discount', 'happy-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'show_discount_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap #edd_show_discount' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'show_discount_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} #edd_checkout_form_wrap #edd_show_discount',
			]
		);
		$this->add_control(
			'show_discount_heading_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap #edd_show_discount' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'show_discount_heading_text_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap #edd_show_discount' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'show_discount_heading_typography',
				'selector' => '{{WRAPPER}} #edd_show_discount',
			]
		);


		$this->add_control(
			'show_discount_heading_text_link_color',
			[
				'label'     => __( 'Link Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_show_discount .edd_discount_link' => 'color: {{VALUE}};',
				],
				// 'separator'	=> 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'show_discount_link_heading_typography',
				'label'		=> __('Link Typography', 'happy-addons-pro'),
				'selector' => '{{WRAPPER}} #edd_show_discount .edd_discount_link',
			]
		);

		$this->add_control(
			'_heading_discount_wrapper',
			[
				'label' => __( 'Discount Area', 'happy-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
				'separator'=> 'before',

			]
		);

		$this->add_control(
			'discount_area_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap #edd-discount-code-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'discount_area_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} #edd_checkout_form_wrap #edd-discount-code-wrap',
			]
		);
		$this->add_control(
			'discount_area_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap #edd-discount-code-wrap' => 'background: {{VALUE}};',
				],
			]
		);		

		$this->add_control(
			'_heading_discount_label',
			[
				'label' => __( 'Label', 'happy-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
				'separator'=> 'before',

			]
		);

		$this->add_control(
			'discount_heading_text_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd-discount-code-wrap label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'discount_heading_typography',
				'selector' => '{{WRAPPER}} #edd-discount-code-wrap label',
			]
		);

		$this->add_responsive_control(
			'discount_heading_spacing',
			[
				'label'     => __( 'Spacing', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} #edd-discount-code-wrap label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'_heading_discount_desc',
			[
				'label' => __( 'Description', 'happy-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'discount_description_text_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd-discount-code-wrap .edd-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'discount_description_typography',
				'selector' => '{{WRAPPER}} #edd-discount-code-wrap .edd-description',
			]
		);

		$this->add_responsive_control(
			'discount_description_spacing',
			[
				'label'     => __( 'Spacing', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} #edd-discount-code-wrap .edd-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __headings_style_controls() {
		$this->start_controls_section(
			'_section_style_headings',
			[
				'label' => __( 'Headings', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'headings_text_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap legend' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'headings_typography',
				'selector' => '{{WRAPPER}} #edd_checkout_form_wrap legend',
			]
		);

		$this->add_responsive_control(
			'headings_spacing',
			[
				'label'     => __( 'Spacing', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} #edd_checkout_form_wrap legend' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __button_style_controls() {
		$this->start_controls_section(
			'_section_style_button',
			[
				'label' => __( 'Button', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} #edd_purchase_form .edd-submit',
			]
		);


		$this->add_control(
			'btn_align',
			[
				'label'       => __( 'Alignment', 'happy-addons-pro' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'flex-start'   => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					],
					'flex-end'  => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'     => 'left',
				'toggle'      => true,
				'selectors'   => [
					'{{WRAPPER}} #edd_purchase_form #edd_purchase_submit #edd-purchase-button' => 'align-self: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} #edd_purchase_form .edd-submit' => 'fill: {{VALUE}}; color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'background',
				'label'          => esc_html__( 'Background', 'happy-addons-pro' ),
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'selector'       => '{{WRAPPER}} #edd_purchase_form .edd-submit',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label'     => esc_html__( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_purchase_form .edd-submit:hover' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} #edd_purchase_form .edd-submit:focus' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} #edd_purchase_form .edd-submit:hover svg' => 'fill: {{VALUE}} !important;',
					'{{WRAPPER}} .#edd_purchase_form .edd-submit:focus svg' => 'fill: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'button_background_hover',
				'label'          => esc_html__( 'Background', 'happy-addons-pro' ),
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'selector'       => '{{WRAPPER}} #edd_purchase_form .edd-submit:hover',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} #edd_purchase_form .edd-submit:hover, {{WRAPPER}} #edd_purchase_form .edd-submit:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		// $this->add_control(
		// 	'hover_animation',
		// 	[
		// 		'label' => esc_html__( 'Hover Animation', 'happy-addons-pro' ),
		// 		'type'  => Controls_Manager::HOVER_ANIMATION,
		// 	]
		// );

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'border',
				'selector'  => '{{WRAPPER}} #edd-purchase-button, {{WRAPPER}} #edd_purchase_form .edd-apply-discount',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} #edd-purchase-button, {{WRAPPER}} #edd_purchase_form .edd-apply-discount' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} #edd-purchase-button',
			]
		);

		$this->add_responsive_control(
			'text_padding',
			[
				'label'      => esc_html__( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #edd-purchase-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->end_controls_section();
	}

	protected static function _setup_env( $settings ) {
		if ( ! ha_elementor()->editor->is_edit_mode() ||
			! function_exists( 'EDD' ) ||
			empty( EDD()->cart ) ) {
			return;
		}

		if ( EDD()->cart->get_quantity() < 1 && ha_elementor()->editor->is_edit_mode() ) {
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

		$settings = $this->get_settings_for_display();

		$cart_remove_settings = [
			'cart_btn_type' => $settings['cart_btn_type'],
			'btn_text'      => $settings['btn_text'],
			'icon'          => $settings['icon'],
		];

		$this->add_render_attribute( 'cart-settings', 'data-options', wp_json_encode( $cart_remove_settings ) );

		self::_setup_env( $settings );
		if( ha_elementor()->editor->is_edit_mode() ){
			add_action( 'edd_checkout_form_top', [ $this, 'ha_edd_discount_field'], -1 );
		}
			

		echo '<div class="ha-edd-table-wrap"' . $this->get_render_attribute_string( 'cart-settings' ) . '>';
			echo ha_do_shortcode( 'download_checkout' );
		echo '</div>';
	}

	public function ha_edd_discount_field() {

		if( isset( $_GET['payment-mode'] ) && edd_is_ajax_disabled() ) {
			return; // Only show before a payment method has been selected if ajax is disabled
		}
	
		// if( ! edd_is_checkout() ) {
		// 	return;
		// }
	
		if ( edd_has_active_discounts() && edd_get_cart_total() ) :
	
			$color = edd_get_option( 'checkout_color', 'blue' );
			$color = ( $color == 'inherit' ) ? '' : $color;
			$style = edd_get_option( 'button_style', 'button' );
	?>
			<fieldset id="edd_discount_code">
				<p id="edd_show_discount" style="display:block;">
					<?php _e( 'Have a discount code?', 'happy-addons-pro' ); ?> <a href="#" class="edd_discount_link"><?php echo _x( 'Click to enter it', 'Entering a discount code', 'happy-addons-pro' ); ?></a>
				</p>
				<p id="edd-discount-code-wrap" class="edd-cart-adjustment">
					<label class="edd-label" for="edd-discount">
						<?php _e( 'Discount', 'happy-addons-pro' ); ?>
					</label>
					<span class="edd-description"><?php _e( 'Enter a coupon code if you have one.', 'happy-addons-pro' ); ?></span>
					<span class="edd-discount-code-field-wrap">
						<input class="edd-input" type="text" id="edd-discount" name="edd-discount" placeholder="<?php _e( 'Enter discount', 'happy-addons-pro' ); ?>"/>
						<input type="submit" class="edd-apply-discount edd-submit <?php echo $color . ' ' . $style; ?>" value="<?php echo _x( 'Apply', 'Apply discount at checkout', 'happy-addons-pro' ); ?>"/>
					</span>
					<span class="edd-discount-loader edd-loading" id="edd-discount-loader" style="display:none;"></span>
					<span id="edd-discount-error-wrap" class="edd_error edd-alert edd-alert-error" aria-hidden="true" style="display:none;"></span>
				</p>
			</fieldset>
	<?php
		endif;
	}
}
