<?php 
/*
Widget Name: Woo Single Pricing
Description: Woo Single Pricing
Author: Posimyth
Author URI: http://posimyth.com
*/

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Woo_Single_Pricing extends Widget_Base {
		
	public function get_name() {
		return 'tp-woo-single-pricing';
	}

    public function get_title() {
        return esc_html__('Woo Single Pricing', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-money theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-woo-builder');
    }
	public function get_keywords() {
		return ['Single pricing, woocomerce','post','product','cart','add to cart','add cart','price','sale price','stock','woo stock','Sold','attributes','woo attributes','product attributes'];
	}
    protected function register_controls() {
		/*content start*/
		$this->start_controls_section(
			'section_woo_single_pricing',
			[
				'label' => esc_html__( 'Woo Single Pricing', 'theplus' ),
			]
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'select',
			[
				'label' => esc_html__( 'Select', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'add_to_cart',
				'options' => [
					'add_to_cart'  => esc_html__( 'Add to Cart', 'theplus' ),
					'price'  => esc_html__( 'Price', 'theplus' ),
					'stock'  => esc_html__( 'Stock', 'theplus' ),
					'sold'  => esc_html__( 'Sold', 'theplus' ),
					'attributes'  => esc_html__( 'Attributes', 'theplus' ),			
				],
			]
		);		
		$repeater->add_control(
			'display_cart__oty',
			[
				'label' => esc_html__( 'Quantity Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'layout-1',
				'options' => [
					'layout-1'  => esc_html__( 'Layout 1', 'theplus' ),
					'layout-2'  => esc_html__( 'Layout 2', 'theplus' ),
				],
				'condition' => [
					'select' => 'add_to_cart',
				],
			]
		);
		$repeater->add_control(
			'dis_before',
			[
				'label'       => esc_html__( 'Stock Prefix', 'theplus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => ['active' => true,],
				'default' => esc_html__( 'Availability : ', 'theplus' ),
				'condition'		=> [
					'select' => 'stock',
				],
			]
		);
		$repeater->add_control(
			'dis_after',
			[
				'label'       => esc_html__( 'Stock Postfix', 'theplus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => ['active' => true,],
				'default' => esc_html__( ' In stock', 'theplus' ),
				'condition'		=> [
					'select' => 'stock',
				],
			]
		);
		$repeater->add_control(
			'stockout',
			[
				'label'       => esc_html__( 'Out of Stock Notice', 'theplus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => ['active' => true,],
				'default' => esc_html__( ' Out of stock', 'theplus' ),
				'condition'		=> [
					'select' => 'stock',
				],
			]
		);
		$repeater->add_control(
			'stockbackorderallow',
			[
				'label'       => esc_html__( 'Backorders Allowed Notice', 'theplus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => ['active' => true,],
				'default' => esc_html__( 'Available on backorder', 'theplus' ),
				'condition'		=> [
					'select' => 'stock',
				],
			]
		);		
		$repeater->add_control(
			'dis_before_price',
			[
				'label'       => esc_html__( 'Price Prefix', 'theplus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => ['active' => true,],
				'default' => esc_html__( 'Price : ', 'theplus' ),
				'condition'		=> [
					'select' => 'price',
				],
			]
		);
		$repeater->add_control(
			'dis_after_price',
			[
				'label'       => esc_html__( 'Price Postfix', 'theplus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => ['active' => true,],
				'default' => '',
				'condition'		=> [
					'select' => 'price',
				],
			]
		);
		
		$repeater->add_control(
			'sold_before',
			[
				'label'       => esc_html__( 'Sold Prefix', 'theplus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => ['active' => true,],
				'default' => esc_html__( 'Sold ', 'theplus' ),
				'condition'		=> [
					'select' => 'sold',
				],
			]
		);
		$repeater->add_control(
			'sold_after',
			[
				'label'       => esc_html__( 'Sold Postfix', 'theplus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => ['active' => true,],
				'default' => esc_html__( ' in last hours', 'theplus' ),
				'condition'		=> [
					'select' => 'sold',
				],
			]
		);
		$repeater->add_control(
			'select_attributes_type',
			[
				'label' => esc_html__( 'Display', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'tp_inline_att',
				'options' => [
					'tp_inline_att'  => esc_html__( 'Inline', 'theplus' ),
					'tp_newline_att'  => esc_html__( 'Block', 'theplus' ),
				],
				'condition'		=> [
					'select' => 'attributes',
				],
			]
		);
		$repeater->add_responsive_control(
            'select_attributes_inline_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Label Minimum Width', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 300,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-meta.tp_newline_att .tp-woo-sm .tp-woo-sm-label' => 'width: {{SIZE}}{{UNIT}};display: inline-flex;',
				],
				'condition'		=> [
					'select' => 'attributes',
					'select_attributes_type' => 'tp_newline_att',
				],
            ]
        );
		$repeater->add_control(
			'cattext',
			[
				'label'       => esc_html__( 'Category Prefix', 'theplus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => ['active' => true,],
				'default' => esc_html__( 'Category : ', 'theplus' ),
				'condition'		=> [
					'select' => 'attributes',
				],
			]
		);
		$repeater->add_control(
			'tagtext',
			[
				'label'       => esc_html__( 'Tag Prefix', 'theplus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => ['active' => true,],
				'default' => esc_html__( 'Tag : ', 'theplus' ),
				'condition'		=> [
					'select' => 'attributes',
				],
			]
		);
		$repeater->add_control(
			'skutext',
			[
				'label'       => esc_html__( 'SKU Prefix', 'theplus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => ['active' => true,],
				'default' => esc_html__( 'SKU : ', 'theplus' ),
				'condition'		=> [
					'select' => 'attributes',
				],
			]
		);
		$this->add_control(
            'loop_content',
            [
				'label' => esc_html__( 'Woo Single Pricing', 'theplus' ),
                'type' => Controls_Manager::REPEATER,
                'default' => [
                    [
                        'select' => 'add_to_cart',
                    ],
					[
                        'select' => 'price',
                    ],
					[
                        'select' => 'stock',
                    ],
					[
                        'select' => 'sold',
                    ],
					[
                        'select' => 'attributes',
                    ],
                ],
                'separator' => 'before',
				'fields' => $repeater->get_controls(),
                'title_field' => '{{{ select }}}',				
            ]
        );
		$this->end_controls_section();
		
		$this->start_controls_section(
			'section_extra_options',
			[
				'label' => esc_html__( 'Extra Options', 'theplus' ),
			]
		);
		$this->add_control(
            'display_instock_status',
            [
				'label'   => esc_html__( 'Add to Cart : Instock Status', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-add-to-cart .stock' => 'display: block;',
				],
			]
		);
		$this->end_controls_section();
		/*content end*/
		
		/*style start*/
		/*add to cart start*/
		/*add to cart button start*/
		$this->start_controls_section(
			'section_atc_buttonstyle',
			[
				'label' => esc_html__( 'Add to Cart : Button', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);		
		$this->add_responsive_control(
			'atc_button_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-add-to-cart .cart .single_add_to_cart_button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'atc_button_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-add-to-cart .cart .single_add_to_cart_button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
            'atc_button_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Button Size', 'theplus'),
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-add-to-cart .cart .single_add_to_cart_button' => 'width: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'atc_button_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .tp-woo-add-to-cart .cart .single_add_to_cart_button',
			]
		);
		$this->start_controls_tabs( 'tabs_atc_button_style' );
		$this->start_controls_tab(
			'tab_atc_button_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'atcb_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-add-to-cart .cart .single_add_to_cart_button' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'atcb_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .tp-woo-add-to-cart .cart .single_add_to_cart_button',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'atcb_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .tp-woo-add-to-cart .cart .single_add_to_cart_button',
			]
		);
		$this->add_responsive_control(
			'atcb_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-add-to-cart .cart .single_add_to_cart_button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'atcb_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .tp-woo-add-to-cart .cart .single_add_to_cart_button',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_atc_button_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),				
			]
		);
		$this->add_control(
			'atcb_h_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-add-to-cart .cart .single_add_to_cart_button:hover' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'atcb_h_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .tp-woo-add-to-cart .cart .single_add_to_cart_button:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'atcb_h_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .tp-woo-add-to-cart .cart .single_add_to_cart_button:hover',
			]
		);
		$this->add_responsive_control(
			'atcb_h_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-add-to-cart .cart .single_add_to_cart_button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'atcb_h_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .tp-woo-add-to-cart .cart .single_add_to_cart_button:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*add to cart button end*/
		/*add to cart simple start*/
		$this->start_controls_section(
			'section_add_to_cart_style',
			[
				'label' => esc_html__( 'Add to Cart : Simple Product', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'atc_qty',
			[
				'label' => esc_html__( 'Quantity', 'theplus' ),
				'type' => Controls_Manager::HEADING,				
			]
		);
		$this->add_responsive_control(
            'atc_qty_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 300,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'.woocommerce  {{WRAPPER}}  .tp-woo-add-to-cart .quantity .qty,
					{{WRAPPER}} .tp-woo-single-pricing .tp-woo-add-to-cart .cart .quantity' => 'width: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'atc_qty_typography',
				'selector' => '.woocommerce  {{WRAPPER}} .tp-woo-add-to-cart .quantity .qty',
			]
		);
		$this->add_control(
			'atc_qty_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.woocommerce  {{WRAPPER}} .tp-woo-add-to-cart .quantity .qty' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'atc_qty_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '.woocommerce  {{WRAPPER}} .tp-woo-add-to-cart .quantity .qty',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'atc_qty_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '.woocommerce  {{WRAPPER}} .tp-woo-add-to-cart .quantity .qty',
			]
		);
		$this->add_responsive_control(
			'atc_qty_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.woocommerce  {{WRAPPER}} .tp-woo-add-to-cart .quantity .qty' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'atc_qty_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '.woocommerce  {{WRAPPER}} .tp-woo-add-to-cart .quantity .qty',
			]
		);
		/*product qty layout 2 options start*/
		$this->add_control(
			'product_qty_pm_heading',
			[
				'label' => esc_html__( 'Quantity Plus Minus Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [					
					'display_cart__oty' => 'layout-2',
				],				
			]
		);
		$this->add_responsive_control(
            'product_qty_pm_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 30,
						'step' => 1,
					],
				],
				'render_type' => 'ui',			
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing.layout-2 .tp-woo-add-to-cart .cart .quantity .tp-quantity-arrow' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'condition' => [					
					'display_cart__oty' => 'layout-2',
				],	
            ]
        );
		$this->add_control(
			'product_qty_pm_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing.layout-2 .tp-woo-add-to-cart .cart .quantity .tp-quantity-arrow' => 'color: {{VALUE}};',
				],
				'condition' => [					
					'display_cart__oty' => 'layout-2',
				],
			]
		);
		$this->add_control(
			'product_qty_w_heading',
			[
				'label' => esc_html__( 'Quantity Box Options (Layout 2)', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'p_qty_w_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing.layout-2 .tp-woo-add-to-cart .cart .quantity' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'p_qty_w_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing.layout-2 .tp-woo-add-to-cart .cart .quantity',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'p_qty_w_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing.layout-2 .tp-woo-add-to-cart .cart .quantity',
			]
		);
		$this->add_responsive_control(
			'p_qty_w_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-single-pricing.layout-2 .tp-woo-add-to-cart .cart .quantity' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'p_qty_w_shadow',
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing.layout-2 .tp-woo-add-to-cart .cart .quantity',
			]
		);
		/*product qty layout 2 options end*/
		$this->add_control(
			'atc_stock',
			[
				'label' => esc_html__( 'Stock', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'atc_stock_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .tp-woo-add-to-cart .stock',
			]
		);
		$this->add_control(
			'atc_stock_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-add-to-cart .stock' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'atc_ostock',
			[
				'label' => esc_html__( 'Out of Stock', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'atc_ostock_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .tp-woo-add-to-cart .stock.out-of-stock',
			]
		);
		$this->add_control(
			'atc_ostock_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-add-to-cart .stock.out-of-stock' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/*add to cart simple end*/
		
		/*add to cart Variations Product start*/
		$this->start_controls_section(
			'section_atc_varation_style',
			[
				'label' => esc_html__( 'Add to Cart : Variations Product', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'atc_gp_variations_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],
				'selectors' => [
				'{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'atc_gp_variations_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],
				'selectors' => [
				'{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'atc_gp_variations_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart',
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'atc_gp_variations_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart',				
			]
		);
		$this->add_responsive_control(
			'atc_gp_variations_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'atc_gp_variations_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart',
			]
		);
		$this->add_control(
			'atc_gp_var_label',
			[
				'label' => esc_html__( 'Variations Product Label', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'atc_gp_var_label_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart .variations td.label,{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart .variations th.label',			
			]
		);
		$this->add_control(
			'atc_gp_var_label_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart .variations td.label,{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart .variations th.label' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'atc_gp_var_option',
			[
				'label' => esc_html__( 'Variations Product Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'atc_gp_var_option_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart .variations td.value select',
			]
		);
		$this->add_control(
			'atc_gp_var_option_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart .variations td.value select' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'atc_gp_var_option_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart .variations td.value select',
			]
		);
		$this->add_control(
			'atc_gp_var_reset',
			[
				'label' => esc_html__( 'Variations Product Reset', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'atc_gp_var_reset_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart .variations td.value .reset_variations' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'atc_gp_var_reset_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart .variations td.value .reset_variations',
			]
		);
		$this->start_controls_tabs( 'tabs_atc_gp_var_reset_style' );
		$this->start_controls_tab(
			'tab_atc_gp_var_reset_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'atc_gp_var_reset_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart .variations td.value .reset_variations' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'atc_gp_var_reset_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart .variations td.value .reset_variations',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'atc_gp_var_reset_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart .variations td.value .reset_variations',
			]
		);
		$this->add_responsive_control(
			'atc_gp_var_reset_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart .variations td.value .reset_variations' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'atc_gp_var_reset_shadow',
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart .variations td.value .reset_variations',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_atc_gp_var_reset_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),				
			]
		);
		$this->add_control(
			'atc_gp_var_reset_color_h',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart .variations td.value .reset_variations:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'atc_gp_var_reset_bg_h',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart .variations td.value .reset_variations:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'atc_gp_var_reset_border_h',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart .variations td.value .reset_variations:hover',
			]
		);
		$this->add_responsive_control(
			'atc_gp_var_reset_br_h',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart .variations td.value .reset_variations:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'atc_gp_var_reset_shadow_h',
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart .variations td.value .reset_variations:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();		
		$this->add_control(
			'atc_gp_var_price',
			[
				'label' => esc_html__( 'Variations Product Price', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'atc_gp_var_price_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart .single_variation_wrap .woocommerce-variation-price span',
			]
		);
		$this->add_control(
			'atc_gp_var_price_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart .single_variation_wrap .woocommerce-variation-price span' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'atc_gp_var_description',
			[
				'label' => esc_html__( 'Variations Product Description', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'atc_gp_var_description_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart .single_variation_wrap .woocommerce-variation-description',
			]
		);
		$this->add_control(
			'atc_gp_var_description_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .variations_form.cart .single_variation_wrap .woocommerce-variation-description' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/*add to cart end*/
		
		/*swatches start*/
		$this->start_controls_section(
			'section_swatches_style',
			[
				'label' => esc_html__( 'Add to Cart : Swatches Product', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control('swatchesloop',
            [
				'label' => esc_html__( 'Custom Loop Skin', 'theplus' ),
				'description' => esc_html__( 'Note : If this option enabled, You can use this in Custom Loop Skip feature and It will load required js and CSS related to WooCommerce for that.', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
			]
		);
		$this->add_control(
			'swatcheslayout',
			[
				'label' => esc_html__( 'Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'inline',
				'options' => [
					'default'  => esc_html__( 'Default', 'theplus' ),
					'inline'  => esc_html__( 'Inline', 'theplus' ),		
				],
			]
		);
		$this->add_control(
			'swatchesstyle',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => esc_html__( 'Default', 'theplus' ),
					'withtitle'  => esc_html__( 'With Title', 'theplus' ),
					'tooltip'  => esc_html__( 'Tooltip', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'swatches_title_style',
			[
				'label' => esc_html__( 'Title Style', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'swatchesstyle' => 'withtitle',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'swatches_title_typography',
				'selector' => '{{WRAPPER}} .tp-woo-swatches .tp-swatches-tooltip',
				'condition' => [
					'swatchesstyle' => 'withtitle',
				],
			]
		);
		$this->add_control(
			'swatches_title_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000ad',
				'selectors' => [
					'{{WRAPPER}} .tp-woo-swatches .tp-swatches-tooltip' => 'color: {{VALUE}};',
				],
				'condition' => [
					'swatchesstyle' => 'withtitle',
				],
			]
		);
		$this->add_control(
			'swatches_tooltip_style',
			[
				'label' => esc_html__( 'Tooltip Style', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'swatchesstyle' => 'tooltip',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'swatches_tooltip_typography',
				'selector' => '{{WRAPPER}} .swatchesstyletooltip .tp-woo-swatches .tp-swatches-tooltip',
				'condition' => [
					'swatchesstyle' => 'tooltip',
				],
			]
		);
		$this->add_control(
			'swatches_tooltip_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swatchesstyletooltip .tp-woo-swatches .tp-swatches-tooltip' => 'color: {{VALUE}};',
				],
				'condition' => [
					'swatchesstyle' => 'tooltip',
				],
			]
		);
		$this->add_control(
			'swatches_tooltip_bg',
			[
				'label' => esc_html__( 'Background', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swatchesstyletooltip .tp-woo-swatches .tp-swatches-tooltip,
				{{WRAPPER}} .swatchesstyletooltip .tp-woo-swatches .tp-swatches-tooltip:after' => 'background: {{VALUE}};',
				],
				'condition' => [
					'swatchesstyle' => 'tooltip',
				],
			]
		);
		$this->add_responsive_control(
            'swatches_tooltip_border',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Border Radius', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .swatchesstyletooltip .tp-woo-swatches .tp-swatches-tooltip' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'swatchesstyle' => 'tooltip',
				],
            ]
        );
		$this->add_control(
			'swatches_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'swatches_color_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}  .tp-woo-swatches .tp-swatches.tp-swatches-color' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_responsive_control(
            'swatches_color_space',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Offset Right', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-color' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_responsive_control(
            'swatches_color_space_bottom',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Offset Bottom', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-color' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_swatches_color_style' );
		$this->start_controls_tab(
			'tab_swatches_color_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'swatches_color_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-color',
			]
		);
		$this->add_responsive_control(
			'swatches_color_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-color' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'swatches_color_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-color',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_swatches_color_active',
			[
				'label' => esc_html__( 'Active', 'theplus' ),				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'swatches_color_border_a',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-color.selected',
			]
		);
		$this->add_responsive_control(
			'swatches_color_br_a',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-color.selected' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'swatches_color_shadow_a',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-color.selected',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		
		$this->add_control(
			'swatches_image',
			[
				'label' => esc_html__( 'Image', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'swatches_image_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}  .tp-woo-swatches .tp-swatches.tp-swatches-image img' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_responsive_control(
            'swatches_image_space',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Offset Right', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-image' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_responsive_control(
            'swatches_image_space_bottom',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Offset Bottom', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-image img' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_swatches_image_style' );
		$this->start_controls_tab(
			'tab_swatches_image_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'swatches_image_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-image img',
			]
		);
		$this->add_responsive_control(
			'swatches_image_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'swatches_image_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-image img',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_swatches_image_active',
			[
				'label' => esc_html__( 'Active', 'theplus' ),				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'swatches_image_border_a',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-image.selected img',
			]
		);
		$this->add_responsive_control(
			'swatches_image_br_a',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-image.selected img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'swatches_image_shadow_a',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-image.selected img',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		
		$this->add_control(
			'swatches_button',
			[
				'label' => esc_html__( 'Button', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'swatches_button_typography',
				'selector' => '{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-button',
			]
		);
		$this->add_responsive_control(
            'swatches_button_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-button' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_responsive_control(
            'swatches_button_space',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Offset Right', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-button' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_responsive_control(
            'swatches_button_space_bottom',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Offset Bottom', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-button' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_swatches_button_style' );
		$this->start_controls_tab(
			'tab_swatches_button_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'swatches_button_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-button' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'swatches_button_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-button',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'swatches_button_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-button',
			]
		);
		$this->add_responsive_control(
			'swatches_button_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'swatches_button_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-button',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_swatches_button_active',
			[
				'label' => esc_html__( 'Active', 'theplus' ),				
			]
		);
		$this->add_control(
			'swatches_button_color_a',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-button.selected' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'swatches_button_bg_a',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-button.selected',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'swatches_button_border_a',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-button.selected',
			]
		);
		$this->add_responsive_control(
			'swatches_button_br_a',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-button.selected' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'swatches_button_shadow_a',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-swatches .tp-swatches.tp-swatches-button.selected',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*swatches end*/
		
		/*add to cart grouped Product start*/
		$this->start_controls_section(
			'section_atc_grouped_style',
			[
				'label' => esc_html__( 'Add to Cart : Grouped Product', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'atc_grouped_pro_name',
			[
				'label' => esc_html__( 'Product Name', 'theplus' ),
				'type' => Controls_Manager::HEADING,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'grouped_pro_name_typography',
				'selector' => '{{WRAPPER}} .tp-woo-add-to-cart.grouped .woocommerce-grouped-product-list-item__label a',
			]
		);
		$this->add_control(
			'grouped_pro_name_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-add-to-cart.grouped .woocommerce-grouped-product-list-item__label a' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'grouped_pro_price',
			[
				'label' => esc_html__( 'Price', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'grouped_pro_price_typography',
				'selector' => '{{WRAPPER}} .tp-woo-add-to-cart.grouped .woocommerce-grouped-product-list-item__price',
			]
		);
		$this->add_control(
			'grouped_pro_price_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-add-to-cart.grouped .woocommerce-grouped-product-list-item__price' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'grouped_pro_button',
			[
				'label' => esc_html__( 'Button', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'grouped_pro_button_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-woo-add-to-cart.grouped .woocommerce-grouped-product-list-item .woocommerce-grouped-product-list-item__quantity .product_type_variable' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
            'grouped_pro_button_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 300,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-woo-add-to-cart.grouped .woocommerce-grouped-product-list-item .woocommerce-grouped-product-list-item__quantity .product_type_variable' => 'width: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'grouped_pro_button_typography',
				'selector' => '{{WRAPPER}} .tp-woo-add-to-cart.grouped .woocommerce-grouped-product-list-item .woocommerce-grouped-product-list-item__quantity .product_type_variable',
			]
		);
		$this->start_controls_tabs( 'tabs_gpb_button_style' );
		$this->start_controls_tab(
			'tab_gpb_button_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'gpb_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-woo-add-to-cart.grouped .woocommerce-grouped-product-list-item .woocommerce-grouped-product-list-item__quantity .product_type_variable' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'gpb_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-woo-add-to-cart.grouped .woocommerce-grouped-product-list-item .woocommerce-grouped-product-list-item__quantity .product_type_variable',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'gpb_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-add-to-cart.grouped .woocommerce-grouped-product-list-item .woocommerce-grouped-product-list-item__quantity .product_type_variable',
			]
		);
		$this->add_responsive_control(
			'gpb_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-add-to-cart.grouped .woocommerce-grouped-product-list-item .woocommerce-grouped-product-list-item__quantity .product_type_variable' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'gpb_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-add-to-cart.grouped .woocommerce-grouped-product-list-item .woocommerce-grouped-product-list-item__quantity .product_type_variable',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_gpb_button_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),				
			]
		);
		$this->add_control(
			'gpb_h_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-woo-add-to-cart.grouped .woocommerce-grouped-product-list-item .woocommerce-grouped-product-list-item__quantity .product_type_variable:hover' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'gpb_h_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-woo-add-to-cart.grouped .woocommerce-grouped-product-list-item .woocommerce-grouped-product-list-item__quantity .product_type_variable:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'gpb_h_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-add-to-cart.grouped .woocommerce-grouped-product-list-item .woocommerce-grouped-product-list-item__quantity .product_type_variable:hover',
			]
		);
		$this->add_responsive_control(
			'gpb_h_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-add-to-cart.grouped .woocommerce-grouped-product-list-item .woocommerce-grouped-product-list-item__quantity .product_type_variable:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'gpb_h_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-add-to-cart.grouped .woocommerce-grouped-product-list-item .woocommerce-grouped-product-list-item__quantity .product_type_variable:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*add to cart grouped Product end*/
		
		/*price start*/
		$this->start_controls_section(
			'section_price_style',
			[
				'label' => esc_html__( 'Price', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'price_alignment',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-price' => 'justify-content : {{VALUE}}',
				],
				'default' => 'left',
				'toggle' => true,
				'label_block' => false,				
			]
		);
		$this->add_responsive_control(
            'price_offset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Space', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 150,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-price .price del>.woocommerce-Price-amount.amount:nth-child(1),
					{{WRAPPER}} .tp-woo-single-pricing .tp-woo-price .price .woocommerce-Price-amount.amount:nth-child(1) bdi' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-price .price .woocommerce-Price-amount.amount:nth-child(2) bdi' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_control(
			'sale_price',
			[
				'label' => esc_html__( 'Sale Price', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sale_price_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .tp-woo-price .price ins .woocommerce-Price-amount,
				{{WRAPPER}} .tp-woo-single-pricing .tp-woo-price .price .woocommerce-Price-amount',
			]
		);
		$this->add_control(
			'sale_price_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-price .price ins,{{WRAPPER}} .tp-woo-single-pricing .tp-woo-price .price ins .woocommerce-Price-amount,{{WRAPPER}} .tp-woo-single-pricing .tp-woo-price .price,{{WRAPPER}} .tp-woo-single-pricing .tp-woo-price .price .woocommerce-Price-amount' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'previous_price',
			[
				'label' => esc_html__( 'Previous Price', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'previous_price_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .tp-woo-price .price del .woocommerce-Price-amount',
			]
		);
		$this->add_control(
			'previous_price_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-price .price del,{{WRAPPER}} .tp-woo-single-pricing .tp-woo-price .price del .woocommerce-Price-amount' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'ba_text_price',
			[
				'label' => esc_html__( 'Prefix/Postfix Text', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'ba_text_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-price .tp-woo-price-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ba_text_price_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .tp-woo-price .tp-woo-price-text',
			]
		);
		$this->add_control(
			'ba_text_price_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-price .tp-woo-price-text' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/*price end*/
		
		/*stock start*/
		$this->start_controls_section(
			'section_stock_style',
			[
				'label' => esc_html__( 'Stock', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'stock_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-stock' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'stock_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-stock' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'stock_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .tp-woo-stock',
			]
		);
		$this->add_control(
			'stock_color',
			[
				'label'     => esc_html__( 'Stock Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-stock' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'stock_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .tp-woo-stock',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'stock_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .tp-woo-stock',
			]
		);
		$this->add_responsive_control(
			'stock_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-stock' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'stock_shadow',
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .tp-woo-stock',
			]
		);
		$this->add_control(
			'out_of_stock_opt',
			[
				'label' => esc_html__( 'Out Of Stock', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'outofstock_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-stock.tp-woo-stock-out' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'outofstock_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-stock.tp-woo-stock-out' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'outofstock_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .tp-woo-stock.tp-woo-stock-out',
			]
		);
		$this->add_control(
			'outofstock_color',
			[
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-stock.tp-woo-stock-out' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'outofstock_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .tp-woo-stock.tp-woo-stock-out',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'outofstock_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .tp-woo-stock.tp-woo-stock-out',
			]
		);
		$this->add_responsive_control(
			'outofstock_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-stock.tp-woo-stock-out' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'outofstock_shadow',
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .tp-woo-stock.tp-woo-stock-out',
			]
		);
		$this->end_controls_section();
		/*stock end*/
		
		/*sold start*/
		$this->start_controls_section(
			'section_sold_style',
			[
				'label' => esc_html__( 'Sold', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'sold_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-sold-product' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'sold_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-sold-product' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sold_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .tp-woo-sold-product',
			]
		);
		$this->add_control(
			'sold_color',
			[
				'label'     => esc_html__( 'Sold Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-sold-product' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'sold_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .tp-woo-sold-product',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'sold_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .tp-woo-sold-product',
			]
		);
		$this->add_responsive_control(
			'sold_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-single-pricing .tp-woo-sold-product' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'sold_shadow',
				'selector' => '{{WRAPPER}} .tp-woo-single-pricing .tp-woo-sold-product',
			]
		);
		$this->end_controls_section();
		/*sold end*/
		
		/*attributes start*/
		$this->start_controls_section(
			'section_attributes_style',
			[
				'label' => esc_html__( 'Attributes', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'attributes_label',
			[
				'label' => esc_html__( 'Label', 'theplus' ),
				'type' => Controls_Manager::HEADING,				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'attributes_label_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-meta .tp-woo-sm .tp-woo-sm-label',
			]
		);
		$this->add_control(
			'attributes_label_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-meta .tp-woo-sm .tp-woo-sm-label' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'attributes_value',
			[
				'label' => esc_html__( 'Value', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'attributes_value_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-meta .tp-woo-sm .tp-woo-sm-value',
			]
		);
		$this->start_controls_tabs( 'tabs_attributes_value_style' );
		$this->start_controls_tab(
			'tab_attributes_value_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'attributes_value_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-meta .tp-woo-sm .tp-woo-sm-value,{{WRAPPER}} .tp-woo-single-meta .tp-woo-sm .tp-woo-sm-value a' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_attributes_value_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),				
			]
		);
		$this->add_control(
			'attributes_value_color_h',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-meta .tp-woo-sm .tp-woo-sm-value:hover,{{WRAPPER}} .tp-woo-single-meta .tp-woo-sm .tp-woo-sm-value:hover a' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*attributes end*/
		/*style end*/
		
	}
	
	
	public function render() {
		$settings = $this->get_settings_for_display();		
		$loop_content=$settings["loop_content"];
		if ( class_exists('woocommerce') ) {
			global $product;
			$product = wc_get_product();

			if ( empty( $product ) ) {
				return;
			}
			
			if(!empty($loop_content)) {
				foreach($loop_content as $item){
					$select = $item['select'];
					/*add to cart start*/
					$swatcheslayout = !empty($settings['swatcheslayout']) ? $settings['swatcheslayout'] : 'inline';
					$swatchesstyle = !empty($settings['swatchesstyle']) ? $settings['swatchesstyle'] : 'default';
					
					if(!empty($select) && $select=="add_to_cart"){ 
					$display_cart__oty = !empty($item['display_cart__oty']) ? $item['display_cart__oty'] : '';
						?>
						<div class="tp-woo-single-pricing <?php echo $display_cart__oty; ?>">
							<div class="tp-woo-add-to-cart <?php echo esc_attr( wc_get_product()->get_type() ); ?> swatcheslayout<?php echo $swatcheslayout; ?> swatchesstyle<?php echo $swatchesstyle; ?>">
								<?php woocommerce_template_single_add_to_cart(); ?>
							</div>
						</div>
						<?php		
					}
					/*add to cart end*/
					
					/*stock start*/
					if(!empty($select) && $select=="stock"){
						if($product->get_type() == 'simple' && $product->get_stock_quantity() > 0){
							?>
							<div class="tp-woo-single-pricing">
								<div class="tp-woo-stock">
									<?php 
										$dis_before = !empty($item['dis_before']) ? $item['dis_before'] : '';
										$dis_after = !empty($item['dis_after']) ? $item['dis_after'] : '';
										
										echo $dis_before . $product->get_stock_quantity() . $dis_after;
									?>
								</div>
							</div>
							<?php	
						}else if($product->get_type() == 'simple' && $product->get_stock_quantity() == 0){
							?>
							<div class="tp-woo-single-pricing">
								<div class="tp-woo-stock tp-woo-stock-out">
									<?php 
										$stockout = !empty($item['stockout']) ? $item['stockout'] : '';
										
										if($product->backorders_allowed()) { 
											$stockout = !empty($item['stockbackorderallow']) ? $item['stockbackorderallow'] : '';
										}
										echo $stockout;
									?>
								</div>
							</div>
							<?php
						}else if($product->get_type() == 'variable' && $product->get_stock_quantity() > 0){ ?>
							<div class="tp-woo-single-pricing">
								<div class="tp-woo-stock">
									<?php 
										$dis_before = !empty($item['dis_before']) ? $item['dis_before'] : '';
										$dis_after = !empty($item['dis_after']) ? $item['dis_after'] : '';
										
										echo $dis_before . $product->get_stock_quantity() . $dis_after;
									?>
								</div>
							</div> <?php
						}
					}
					/*stock end*/
					
					/*sold start*/
					if(!empty($select) && $select=="sold"){ 
					$units_sold = $product->get_total_sales();
						if ( $units_sold ){
							?>
							<div class="tp-woo-single-pricing">
								<div class="tp-woo-sold-product">
									<?php 
											$sold_before = !empty($item['sold_before']) ? $item['sold_before'] : '';
											$sold_after = !empty($item['sold_after']) ? $item['sold_after'] : '';
											
											echo '<div>' . sprintf( __( ''.$sold_before.' %s '.$sold_after.'', 'theplus' ), $units_sold ) . '</div>';						
									?>
								</div>
							</div>
							<?php
						}
					}
					/*stock end*/
					
					/*price start*/
					if(!empty($select) && $select=="price"){ 
						$dis_before_price = !empty($item['dis_before_price']) ? $item['dis_before_price'] : '';
						$dis_after_price = !empty($item['dis_after_price']) ? $item['dis_after_price'] : '';
						?>
						<div class="tp-woo-single-pricing">
							<div class="tp-woo-price"> 
								<?php if(!empty($dis_before_price)){ ?><span class="tp-woo-price-text"><?php echo wp_kses_post($dis_before_price); ?></span><?php } ?> 
									<?php woocommerce_template_single_price(); ?>
								<?php if(!empty($dis_after_price)){ ?><span class="tp-woo-price-text"><?php echo wp_kses_post($dis_after_price); ?></span><?php } ?>
							</div>
						</div>
						<?php		
					}
					/*price end*/
					
					/*attributes start*/
					if(!empty($select) && $select=="attributes"){
						echo '<div class="tp-woo-single-meta '.$item['select_attributes_type'].'">';
							/*sku start*/
							if(!empty($product->get_sku())){
								echo '<div class="tp-woo-sm tp-woo-sm-sku">';
								if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) {
									echo '<span class="tp-woo-sm-label tp-woo-sm-sku-label">'.esc_html($item['skutext']).'</span>';
									echo '<span class="tp-woo-sm-value tp-woo-sm-sku-value">'.$sku = $product->get_sku().'</span>';
								}
								echo '</div>';
							}
							/*sku end*/
							
							/*category start*/
							if(!empty(wc_get_product_category_list($product->get_id()))){
								echo '<div class="tp-woo-sm tp-woo-sm-category">';
									echo '<span class="tp-woo-sm-label tp-woo-sm-category-label">'.esc_html($item['cattext']).'</span>';
									echo '<span class="tp-woo-sm-value tp-woo-sm-category-value">'.wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in"></span>' ).'</span>';
								echo '</div>';
							}
							/*category end*/
							
							/*tag start*/
							if(!empty(wc_get_product_tag_list($product->get_id()))){
								echo '<div class="tp-woo-sm tp-woo-sm-tag">';
									echo '<span class="tp-woo-sm-label tp-woo-sm-tag-label">'.esc_html($item['tagtext']).'</span>';
									echo '<span class="tp-woo-sm-value tp-woo-sm-tag-value">'.wc_get_product_tag_list( $product->get_id(), ', ' ).'</span>';				
								echo '</div>';
							}
							/*tag end*/			
						echo '</div>';
					}
					/*attributes end*/
				}
			}
		}
	}
}