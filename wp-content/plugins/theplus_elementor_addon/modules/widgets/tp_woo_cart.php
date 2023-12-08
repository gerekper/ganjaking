<?php 
/*
Widget Name: Woo Cart
Description: Woo Cart
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
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Woo_Cart extends Widget_Base {
		
	public function get_name() {
		return 'tp-woo-cart';
	}

    public function get_title() {
        return esc_html__('Woo Cart', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-shopping-cart theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-woo-builder');
    }
	public function get_keywords() {
		return ['cart page', 'cart', 'WooCommerce' ,'woo cart'];
	}
    protected function register_controls() {
		/*content start*/
		$this->start_controls_section(
			'section_cart_layout',
			[
				'label' => esc_html__( 'Layout', 'theplus' ),
			]
		);
		$this->add_control(
			'cart_layout',
			[
				'label' => esc_html__( 'Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'cart_default',
				'options' => [
					'cart_default'  => esc_html__( 'Default', 'theplus' ),
					'cart_custom'  => esc_html__( 'Custom', 'theplus' ),
				],
			]
		);		
		$repeater = new \Elementor\Repeater();		
		$repeater->add_control(
			'sortfield',[
				'label' => esc_html__( 'Select','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => [
					'CartData' => esc_html__( 'Cart Data','theplus' ),	
					'CartTotal' => esc_html__( 'Cart Total','theplus' ),	
					'CheckoutButton' => esc_html__( 'Checkout Button','theplus' ),
				],
			]
		);
		$repeater->add_responsive_control(
            'CartDataSize',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 700,
						'step' => 1,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form' => 'width: {{SIZE}}{{UNIT}};float:left',
				],
				'condition'		=> [
					'sortfield' => 'CartData',
				],
            ]
        );
		$repeater->add_responsive_control(
            'CartTotalSize',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 700,
						'step' => 1,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals' => 'width: {{SIZE}}{{UNIT}};float:left',
				],
				'condition'		=> [
					'sortfield' => 'CartTotal',
				],
            ]
        );
		$repeater->add_control(
			'CheckoutButtonText',
			[
				'label' => esc_html__( 'Button Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => esc_html__( 'Proceed to checkout', 'theplus' ),
				'placeholder' => esc_html__( 'Enter Text', 'theplus' ),
				'condition'		=> [
					'sortfield' => 'CheckoutButton',
				],
			]
		);
		$repeater->add_responsive_control(
            'CheckoutButtonSize',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 700,
						'step' => 1,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .wc-proceed-to-checkout' => 'width: {{SIZE}}{{UNIT}};float:left',
				],
				'condition'		=> [
					'sortfield' => 'CheckoutButton',
				],
            ]
        );
		$this->add_control(
            'cartSort',
            [
				'label' => esc_html__( 'Sortable', 'theplus' ),
                'type' => Controls_Manager::REPEATER,
                'default' => [
                    [
                        'sortfield' => 'CartData',
                    ],
                    [
                        'sortfield' => 'CartTotal',
                    ],						
					[
                       'sortfield' => 'CheckoutButton',
                    ],
                ],
                'separator' => 'before',
				'fields' => $repeater->get_controls(),
                'title_field' => '{{{ sortfield }}}',
				'condition'		=> [
					'cart_layout' => 'cart_custom',
				],
            ]
        ); 
		$this->end_controls_section();
		
		$this->start_controls_section(
			'section_extra_opt',
			[
				'label' => esc_html__( 'Cart with Items', 'theplus' ),
				'condition'		=> [
					'cart_layout' => 'cart_default',
				],
			]
		);		
		$this->add_control(
            'display_cart_table',
            [
				'label'   => esc_html__( 'Display Cart', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form' => 'display: block;',
				],
			]
		);
		$this->add_control(
            'display_cross_sell',
            [
				'label'   => esc_html__( 'Display Cross Sell', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',				
			]
		);		
		$this->add_control(
            'coupons_enabled',
            [
				'label'   => esc_html__( 'Hide Coupon Option', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'condition' => [
					'display_cart_table' => 'yes',
				],
				'separator' => 'before',
			]
		);		
		$this->add_control(
            'cart_total',
            [
				'label'   => esc_html__( 'Display Cart Total', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .cart_totals' => 'display: block;',
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
            'cart_total_full_width',
            [
				'label'   => esc_html__( 'Full Width Cart Total', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,				
				'selectors' => [					
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .cart_totals' => 'width: 100%;margin: 0 auto;float: unset;',
				],
				'condition' => [
					'cart_total' => 'yes',
				],
			]
		);		
		$this->end_controls_section();
		/*extra option end*/
		/*cart page start*/
		$this->start_controls_section(
			'section_cart_page',
			[
				'label' => esc_html__( 'Empty Cart', 'theplus' ),
			]
		);		
		$this->add_control(
			'cart_icon',
			[
				'label' => esc_html__( 'Empty Cart Icon', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'icon_default',
				'options' => [
					'icon_default'  => esc_html__( 'Default', 'theplus' ),
					'icon_custom'  => esc_html__( 'Custom', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'cart_icon_custom',
			[
				'label' => esc_html__( 'Upload Image', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-empty:before' => 'content: url({{URL}});',
				],
				'condition'		=> [
					'cart_icon' => 'icon_custom',
				],
			]
		);		
		$this->add_control(
			'empty_cart_text',
			[
				'label' => esc_html__( 'Empty Cart Message', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Your Cart is currently empty.', 'theplus' ),
				'placeholder' => esc_html__( 'Empty Cart Text', 'theplus' ),
				'dynamic' => ['active'   => true,],
				'separator'	=> 'before',
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-empty:after' => 'content: "{{VALUE}}";',				                    
				],
			]
		);
		$this->add_control(
            'hide_empty_cart',
            [
				'label'   => esc_html__( 'Hide Empty Cart', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'separator'	=> 'before',
				'condition' => [
					'cart_layout' => 'cart_custom',
				],
			]
		);	
		$this->end_controls_section();
		/*cart page end*/
		
		/*extra opt start*/
		$this->start_controls_section(
			'section_cart_ext_opt_page',
			[
				'label' => esc_html__( 'Extra Options', 'theplus' ),
			]
		);
		$this->add_control(
			'updateIcons',
			[
				'label' => esc_html__( 'Update Icons', 'theplus' ),
				'type' => Controls_Manager::HEADING,
			]
		);
		$this->add_control(
			'updateIconsnote',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => 'Note : Replace default icons with another font awesome icon using below options.<a href="https://fontawesome.com/v4.7.0/icons" target="_blank">( Get Font Awesome Icon Id. )</a>',
				'content_classes' => 'tp-widget-description',
			]
		);
		$this->add_control(
			'ac_icon',
			[
				'label' => esc_html__( 'Apply Coupon', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( '\f02b', 'theplus' ),
				'selectors' => [
                    '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .button:before' => ' content:"{{VALUE}}";',
				],
			]
		);
		$this->add_control(
			'update_btn_icon',
			[
				'label' => esc_html__( 'Update Cart', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( '\f217', 'theplus' ),
				'selectors' => [
                    '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .button:before' => ' content:"{{VALUE}}";',
				],
			]
		);
		$this->add_control(
			'cart_total_icon',
			[
				'label' => esc_html__( 'Cart Total', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( '\f217', 'theplus' ),
				'selectors' => [
                    '{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .cart_totals>h2:before' => ' content:"{{VALUE}}";',
				],
			]
		);
		$this->add_control(
			'pro_2_chkout_icon',
			[
				'label' => esc_html__( 'Proceed to Checkout', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( '\f00c', 'theplus' ),
				'selectors' => [
                    '{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .wc-proceed-to-checkout .checkout-button:before' => ' content:"{{VALUE}}";',
				],
			]
		);
		$this->end_controls_section();
		/*extra opt end*/
		
		/*style start*/
		/*empty cart start*/
		$this->start_controls_section(
            'empty_cart_styling',
            [
                'label' => esc_html__('Empty Cart', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_control(
			'ec_icon_heading',
			[
				'label' => esc_html__( 'Empty Cart Icon', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'condition'		=> [
					'cart_icon' => 'icon_default',
				],
			]
		);
		$this->add_responsive_control(
            'ec_icon_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-empty:before ' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'condition'		=> [
					'cart_icon' => 'icon_default',
				],
            ]
        );
		$this->add_control(
			'ec_icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-empty:before ' => 'color: {{VALUE}};',
				],
				'separator' => 'after',
				'condition'		=> [
					'cart_icon' => 'icon_default',
				],
			]
		);
		$this->add_control(
			'ec_text_heading',
			[
				'label' => esc_html__( 'Empty Cart Text', 'theplus' ),
				'type' => Controls_Manager::HEADING,				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ec_text_typography',
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart-empty:after',				
			]
		);
		$this->add_control(
			'ec_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-empty:after' => 'color: {{VALUE}};',
				],
			]
		);		
		$this->add_responsive_control(
            'ec_text_top_offset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Offset', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-empty:after' => 'top: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_control(
			'ec_rtn_shop_heading',
			[
				'label' => esc_html__( 'Return to Shop Button', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ec_rtn_shop_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .button.wc-backward',
				
			]
		);
		$this->add_responsive_control(
			'ec_rtn_shop_width',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Button Width', 'theplus'),
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],				
				'render_type' => 'ui',			
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .button.wc-backward' => 'width: {{SIZE}}{{UNIT}}',
				],				
			]
		);
		$this->start_controls_tabs( 'rtn_shop_tabs' );
			$this->start_controls_tab(
				'rtn_shop_normal',
				[
					'label' => esc_html__( 'Normal', 'theplus' ),					
				]
			);
			$this->add_control(
				'rtn_shop_n_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-cart-page-wrapper .button.wc-backward' => 'color: {{VALUE}}',
					],
					
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'rtn_shop_n_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .button.wc-backward',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'rtn_shop_n_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .button.wc-backward',
				]
			);
			$this->add_responsive_control(
				'rtn_shop_n_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-cart-page-wrapper .button.wc-backward' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'rtn_shop_n_shadow',
					'label' => esc_html__( 'Box Shadow', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .button.wc-backward',
					'separator' => 'before',
				]
			);	
			$this->end_controls_tab();
			$this->start_controls_tab(
				'rtn_shop_hover',
				[
					'label' => esc_html__( 'Hover', 'theplus' ),					
				]
			);
			$this->add_control(
				'rtn_shop_h_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-cart-page-wrapper .button.wc-backward:hover' => 'color: {{VALUE}}',
					],
					
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'rtn_shop_h_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .button.wc-backward:hover',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'rtn_shop_h_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .button.wc-backward:hover',
				]
			);
			$this->add_responsive_control(
				'rtn_shop_h_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-cart-page-wrapper .button.wc-backward:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'rtn_shop_h_shadow',
					'label' => esc_html__( 'Box Shadow', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .button.wc-backward:hover',
					'separator' => 'before',
				]
			);
			$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*empty cart end*/
		
		/*product list start*/
		$this->start_controls_section(
            'cart_product_list_styling',
            [
                'label' => esc_html__('Product List', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		
		$this->add_responsive_control(
			'product_list_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form,
					{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals' => 'width:auto;',
				],
				'condition'		=> [
					'cart_layout' => 'cart_default',
				],
			]
		);
		$this->add_responsive_control(
			'product_list_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .shop_table' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'		=> [
					'cart_layout' => 'cart_default',
				],
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'product_list_margin2',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper.tp-cart_custom .woocommerce .woocommerce-cart-form' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'		=> [
					'cart_layout' => 'cart_custom',
				],
			]
		);
		$this->add_responsive_control(
			'product_list_padding2',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper.tp-cart_custom .woocommerce .woocommerce-cart-form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'		=> [
					'cart_layout' => 'cart_custom',
				],
				'separator' => 'after',
			]
		);
		$this->add_control(
			'product_list_alignment',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce .woocommerce-cart-form .shop_table,
					{{WRAPPER}} .tp-cart-page-wrapper .woocommerce .woocommerce-cart-form .shop_table th,
					{{WRAPPER}} .tp-cart-page-wrapper .woocommerce .woocommerce-cart-form .shop_table td,
					{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-page .woocommerce-cart-form .shop_table' => 'text-align : {{VALUE}}',
				],
				'default' => 'center',
				'toggle' => true,
				'label_block' => false,				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'product_list_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce .woocommerce-cart-form .shop_table,{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-page .woocommerce-cart-form .shop_table',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'product_list_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce .woocommerce-cart-form .shop_table,{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-page .woocommerce-cart-form .shop_table',
			]
		);
		$this->add_responsive_control(
			'product_list_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce .woocommerce-cart-form .shop_table,{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-page .woocommerce-cart-form .shop_table' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'product_list_shadow',
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce .woocommerce-cart-form .shop_table,{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-page .woocommerce-cart-form .shop_table',				
			]
		);
		$this->add_control(
			'product_table_heading',
			[
				'label' => esc_html__( 'Product Table Heading', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'product_table_heading_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .shop_table thead tr th',
				
			]
		);
		$this->add_control(
			'product_table_heading_color',
			[
				'label' => esc_html__( 'Heading Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .shop_table thead tr th' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'product_table_heading_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .shop_table thead tr th',
			]
		);
		$this->add_control(
			'product_inner_table_heading_main',
			[
				'label' => esc_html__( 'Product Inner Table Heading Border', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'product_inner_table_h_border',
				'label' => esc_html__( 'Heading Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce .woocommerce-cart-form .shop_table tr th',
			]
		);
			
		$this->add_control(
			'product_inner_table_heading',
			[
				'label' => esc_html__( 'Product Inner Table Border', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'product_inner_table_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce .woocommerce-cart-form .shop_table tr:not(:last-child) td,{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-page .woocommerce-cart-form .shop_table td',				
			]
		);
		/*remove item start*/
		$this->add_control(
			'product_remove_item_heading',
			[
				'label' => esc_html__( 'Remove Item Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs( 'pri_tabs' );
			$this->start_controls_tab(
				'pri_normal',
				[
					'label' => esc_html__( 'Normal', 'theplus' ),					
				]
			);
			$this->add_control(
				'pri_n_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,					
					'selectors' => [
						'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce a.remove' => 'color: {{VALUE}} !important;',
					],
					
				]
			);
			$this->add_control(
				'pri_n_bg_color',
				[
					'label' => esc_html__( 'Background Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,					
					'selectors' => [
						'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce a.remove' => 'background-color: {{VALUE}}',
					],
					
				]
			);
			$this->end_controls_tab();
			$this->start_controls_tab(
				'pri_hover',
				[
					'label' => esc_html__( 'Hover', 'theplus' ),					
				]
			);
			$this->add_control(
				'pri_h_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,					
					'selectors' => [
						'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce a.remove:hover' => 'color: {{VALUE}} !important;',
					],
					
				]
			);
			$this->add_control(
				'pri_h_bg_color',
				[
					'label' => esc_html__( 'Background Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,					
					'selectors' => [
						'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce a.remove:hover' => 'background-color: {{VALUE}}',
					],
					
				]
			);
			$this->end_controls_tab();
		$this->end_controls_tabs( );
		/*remove item end*/
		
		/*image options start*/
		$this->add_control(
			'product_img_heading',
			[
				'label' => esc_html__( 'Product Image Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'pf_img_size',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Image Max Size', 'theplus'),
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
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce .woocommerce-cart-form .shop_table .product-thumbnail img,{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-page .woocommerce-cart-form .shop_table .product-thumbnail img' => 'max-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .tp-cart-page-wrapper table.cart .product-thumbnail img' => 'width:{{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pf_img_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce .woocommerce-cart-form .shop_table .product-thumbnail img,{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-page .woocommerce-cart-form .shop_table .product-thumbnail img',
			]
		);
		$this->add_responsive_control(
			'pf_img_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce .woocommerce-cart-form .shop_table .product-thumbnail img,{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-page .woocommerce-cart-form .shop_table .product-thumbnail img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'pf_img_shadow',
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce .woocommerce-cart-form .shop_table .product-thumbnail img,{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-page .woocommerce-cart-form .shop_table .product-thumbnail img',
			]
		);
		/*image options end*/
		
		/*product name options start*/
		$this->add_control(
			'product_title_heading',
			[
				'label' => esc_html__( 'Product Title Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'pto_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .product-name a',
			]
		);
		$this->start_controls_tabs( 'pto_tabs' );
			$this->start_controls_tab(
				'pto_normal',
				[
					'label' => esc_html__( 'Normal', 'theplus' ),					
				]
			);
			$this->add_control(
				'pto_n_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,					
					'selectors' => [
						'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .product-name a' => 'color: {{VALUE}}',
					],
					
				]
			);
			$this->end_controls_tab();
			$this->start_controls_tab(
				'pto_hover',
				[
					'label' => esc_html__( 'Hover', 'theplus' ),					
				]
			);
			$this->add_control(
				'pto_h_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,					
					'selectors' => [
						'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .product-name a:hover' => 'color: {{VALUE}}',
					],
					
				]
			);
			$this->end_controls_tab();
		$this->end_controls_tabs( );
		/*prodcut name options end*/
		
		/*variation product name options start*/
		$this->add_control(
			'variation_product_title_heading',
			[
				'label' => esc_html__( 'Variation Product Info Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'variation_pto_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce td.product-name dl.variation',
			]
		);
		$this->start_controls_tabs( 'variation_pto_tabs' );
			$this->start_controls_tab(
				'variation_pto_normal',
				[
					'label' => esc_html__( 'Normal', 'theplus' ),					
				]
			);
			$this->add_control(
				'variation_pto_n_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,					
					'selectors' => [
						'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce td.product-name dl.variation' => 'color: {{VALUE}}',
					],
					
				]
			);
			$this->end_controls_tab();
			$this->start_controls_tab(
				'variation_pto_hover',
				[
					'label' => esc_html__( 'Hover', 'theplus' ),					
				]
			);
			$this->add_control(
				'variation_pto_h_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,					
					'selectors' => [
						'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce td.product-name dl.variation:hover' => 'color: {{VALUE}}',
					],
					
				]
			);
			$this->end_controls_tab();
		$this->end_controls_tabs( );
		/*variation prodcut name options end*/
		
		/*product price options start*/
		$this->add_control(
			'product_price_heading',
			[
				'label' => esc_html__( 'Product Price Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'p_price_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .product-price .woocommerce-Price-amount,{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .product-price .amount',
			]
		);
		$this->add_control(
			'p_price_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .product-price .woocommerce-Price-amount,{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .product-price .amount' => 'color: {{VALUE}}',
				],
				
			]
		);
		/*product price options end*/
		/*product qty options start*/
		$this->add_control(
			'product_total_heading',
			[
				'label' => esc_html__( 'Product Qty. Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'display_cart_table_qty',
			[
				'label' => esc_html__( 'Quantity Indicator Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'layout-1',
				'options' => [
					'layout-1'  => esc_html__( 'Style 1', 'theplus' ),
					'layout-2'  => esc_html__( 'Style 2', 'theplus' ),
				],
				'condition' => [
					'cart_layout' => 'cart_default',
					'display_cart_table' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'pttlo_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .product-quantity .quantity input[type=number]',
			]
		);
		$this->add_control(
			'pttlo_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,					
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .product-quantity .quantity input[type=number]' => 'color: {{VALUE}}',
				],
				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'pttlo_bg',
				'types'     => [ 'classic', 'gradient' ],				
				'selector'  => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .product-quantity .quantity input[type=number]',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pttlo_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .product-quantity .quantity input[type=number]',
			]
		);
		$this->add_responsive_control(
			'pttlo_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .product-quantity .quantity input[type=number]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		/*product qty options end*/
		
		/*product qty layout 2 options start*/
		$this->add_control(
			'product_qty_pm_heading',
			[
				'label' => esc_html__( 'Product Qty. Plus Minus Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'cart_layout' => 'cart_default',
					'display_cart_table_qty' => 'layout-2',
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
					'{{WRAPPER}} .tp-cart-page-wrapper.layout-2 .tp-quantity-arrow' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'cart_layout' => 'cart_default',
					'display_cart_table_qty' => 'layout-2',
				],	
            ]
        );
		$this->add_control(
			'product_qty_pm_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper.layout-2 .tp-quantity-arrow' => 'color: {{VALUE}};',
				],
				'condition' => [
					'cart_layout' => 'cart_default',
					'display_cart_table_qty' => 'layout-2',
				],	
			]
		);
		$this->add_control(
			'product_qty_w_heading',
			[
				'label' => esc_html__( 'Qty Box Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'cart_layout' => 'cart_default',
					'display_cart_table_qty' => 'layout-2',
				],				
			]
		);
		$this->add_responsive_control(
			'p_qty_w_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper.layout-2 .woocommerce-cart-form__cart-item .product-quantity .quantity' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'cart_layout' => 'cart_default',
					'display_cart_table_qty' => 'layout-2',
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'p_qty_w_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper.layout-2 .woocommerce-cart-form__cart-item .product-quantity .quantity',
				'condition' => [
					'cart_layout' => 'cart_default',
					'display_cart_table_qty' => 'layout-2',
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'p_qty_w_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper.layout-2 .woocommerce-cart-form__cart-item .product-quantity .quantity',
				'condition' => [
					'cart_layout' => 'cart_default',
					'display_cart_table_qty' => 'layout-2',
				],	
			]
		);
		$this->add_responsive_control(
			'p_qty_w_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-cart-page-wrapper.layout-2 .woocommerce-cart-form__cart-item .product-quantity .quantity' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
				'condition' => [
					'cart_layout' => 'cart_default',
					'display_cart_table_qty' => 'layout-2',
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'p_qty_w_shadow',
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper.layout-2 .woocommerce-cart-form__cart-item .product-quantity .quantity',
				'condition' => [
					'cart_layout' => 'cart_default',
					'display_cart_table_qty' => 'layout-2',
				],	
			]
		);
		/*product qty layout 2 options end*/
		
		/*product total options start*/
		$this->add_control(
			'product_sub_total_heading',
			[
				'label' => esc_html__( 'Product Sub Total Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'p_sub_total_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .product-subtotal .woocommerce-Price-amount,{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .product-subtotal .amount',
			]
		);
		$this->add_control(
			'p_sub_total_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,					
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .product-subtotal .woocommerce-Price-amount,{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .product-subtotal .amount' => 'color: {{VALUE}}',
				],
				
			]
		);
		$this->add_control(
			'p_sub_total_alignment',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce .woocommerce-cart-form .shop_table .product-subtotal' => 'text-align : {{VALUE}}',
				],
				'default' => 'center',
				'toggle' => true,
				'label_block' => false,				
			]
		);
		/*product qty options start*/
				
		$this->end_controls_section();
		/*product list end*/
		
		/*apply coupon start*/
		$this->start_controls_section(
            'cart_apply_coupon_styling',
            [
                'label' => esc_html__('Apply Coupon', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'		=> [
					'coupons_enabled!' => 'yes',
				],
            ]
        );		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ca_input_typography',
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .input-text',
			]
		);
		$this->add_responsive_control(
            'ca_input_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Input Width', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],
				'render_type' => 'ui',			
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .input-text' => 'width: {{SIZE}}{{UNIT}} !important',
				],
            ]
        );
		$this->add_control(
			'ca_input_placeholder_color',
			[
				'label'     => esc_html__( 'Placeholder Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .input-text::placeholder' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'ca_input_inner_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .input-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'ca_input_inner_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .input-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);		
		$this->start_controls_tabs( 'ca_tabs_input_field_style' );
		$this->start_controls_tab(
			'ca_tab_input_field_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'ca_input_field_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .input-text' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'ca_input_field_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .input-text',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ca_input_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .input-text',
			]
		);
		$this->add_responsive_control(
			'ca_input_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .input-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ca_input_shadow',
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .input-text',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'ca_tab_input_field_focus',
			[
				'label' => esc_html__( 'Focus', 'theplus' ),
			]
		);
		$this->add_control(
			'ca_input_field_focus_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .input-text:focus' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'ca_input_field_focus_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .input-text:focus',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ca_input_focus_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .input-text:focus',
			]
		);
		$this->add_responsive_control(
			'ca_input_focus_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .input-text:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ca_input_focus_shadow',
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .input-text:focus',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		
		$this->add_control(
			'ca_button',
			[
				'label' => esc_html__( 'Apply Coupon Button Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'acb_btn_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],				
			]
		);
		$this->add_responsive_control(
			'acb_btn_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],				
			]
		);
		$this->add_responsive_control(
			'acb_btn_width',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Button Width', 'theplus'),
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],				
				'render_type' => 'ui',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .button' => 'width: {{SIZE}}{{UNIT}}',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'acb_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .button',
				
			]
		);
		$this->start_controls_tabs( 'acb_tabs' );
			$this->start_controls_tab(
				'acb_normal',
				[
					'label' => esc_html__( 'Normal', 'theplus' ),					
				]
			);
			$this->add_control(
				'acb_n_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .button' => 'color: {{VALUE}}',
					],
					
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'acb_n_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .button',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'acb_n_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .button',
				]
			);
			$this->add_responsive_control(
				'acb_n_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'acb_n_shadow',
					'label' => esc_html__( 'Box Shadow', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .button',
				]
			);	
			$this->end_controls_tab();
			$this->start_controls_tab(
				'acb_hover',
				[
					'label' => esc_html__( 'Hover', 'theplus' ),					
				]
			);
			$this->add_control(
				'acb_h_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .button:hover' => 'color: {{VALUE}}',
					],
					
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'acb_h_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .button:hover',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'acb_h_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .button:hover',
				]
			);
			$this->add_responsive_control(
				'acb_h_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'acb_h_shadow',
					'label' => esc_html__( 'Box Shadow', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart .actions .coupon .button:hover',
				]
			);
			$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();		
		/*apply coupon end*/
		
		/*update cart btn start*/
		$this->start_controls_section(
            'cart_update_cart_btn_styling',
            [
                'label' => esc_html__('Update Cart Button', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_responsive_control(
			'ucb__btn_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart  .actions .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'ucb__btn_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart  .actions .button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'ucb__btn_width',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Button Width', 'theplus'),
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],				
				'render_type' => 'ui',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart  .actions .button' => 'width: {{SIZE}}{{UNIT}}',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ucb_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],								
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart  .actions .button',
				
			]
		);		
		$this->add_control(
			'ucb_n_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart  .actions .button' => 'color: {{VALUE}}',
				],
				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ucb_n_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart  .actions .button',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ucb_n_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart  .actions .button',
			]
		);
		$this->add_responsive_control(
			'ucb_n_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart  .actions .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ucb_n_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-cart-form .cart  .actions .button',
			]
		);				
		$this->end_controls_section();
		/*update cart btn end*/
		
		/*cart total start*/
		$this->start_controls_section(
            'cart_total_styling',
            [
                'label' => esc_html__('Cart Total', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
			'cart_total_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .cart_totals' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'cart_total_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .cart_totals' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);		
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'cart_total_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .cart_totals',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'cttl_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .cart_totals',
			]
		);
		$this->add_responsive_control(
			'cttl_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .cart_totals' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'cttl_shadow',
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .cart_totals',
			]
		);
		/*cart total heading start*/
		$this->add_control(
			'cttl_heading',
			[
				'label' => esc_html__( 'Heading Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'cttl_heading_alignment',
			[
				'label' => esc_html__( 'Heading Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .cart_totals>h2' => 'text-align : {{VALUE}}',
				],
				'default' => 'center',
				'toggle' => true,
				'label_block' => false,				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cttl_heading_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .cart_totals>h2',
				
			]
		);
		$this->add_control(
			'cttl_heading_color',
			[
				'label' => esc_html__( 'Heading Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .cart_totals>h2' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'cttl_heading_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .cart_totals>h2',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'cttl_1_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .cart_totals>h2',
			]
		);		
		/*cart total heading end*/
		
		/*cart total inner text start*/
		$this->add_control(
			'cttl_inner_txt_heading',
			[
				'label' => esc_html__( 'Cart Total Inner Text Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'cttl_table_raw_alignment',
			[
				'label' => esc_html__( 'Table Raw Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .cart_totals  .shop_table tr th' => 'text-align : {{VALUE}} !important',
				],
				'default' => 'center',
				'toggle' => true,
				'label_block' => false,				
			]
		);
		$this->add_control(
			'cttl_table_data_alignment',
			[
				'label' => esc_html__( 'Table Data Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .cart_totals  .shop_table td' => 'text-align : {{VALUE}}',
				],
				'default' => 'center',
				'toggle' => true,
				'label_block' => false,				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cttl_inner_txt_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals th,
							  {{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals ul#shipping_method li label,
							  {{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .woocommerce-shipping-destination,
							  {{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .shipping-calculator-button,
							  {{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .woocommerce-Price-amount',
				
			]
		);
		$this->add_control(
			'cttl_heading1_color',
			[
				'label' => esc_html__( 'Heading Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals th' => 'color: {{VALUE}}',
				],
				
			]
		);
		$this->add_control(
			'cttl_label_color',
			[
				'label' => esc_html__( 'Label Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals ul#shipping_method li label,
					{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .woocommerce-shipping-destination,
					{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .shipping-calculator-button' => 'color: {{VALUE}}',
				],
				'condition'		=> [
					'cart_layout' => 'cart_default',
				],
				
			]
		);
		$this->add_control(
			'cttl_price_color',
			[
				'label' => esc_html__( 'Price Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .woocommerce-Price-amount,
					{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .woocommerce-shipping-totals.shipping td' => 'color: {{VALUE}}',
				],
				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'cttl_outer_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .cart-collaterals .cart_totals .shop_table tr:not(:last-child) th,{{WRAPPER}} .cart-collaterals .cart_totals .shop_table tr:not(:last-child) td',
				'separator' => 'before',
			]
		);	
		/*cart total inner text end*/
		
		/*total start*/
		$this->add_control(
			'co_ca_total_heading',
			[
				'label' => esc_html__( 'Cart Total', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'co_ca_total__alignment',
			[
				'label' => esc_html__( 'Total Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .cart_totals  .shop_table tr:last-child th' => 'text-align : {{VALUE}} !important',
				],
				'default' => 'center',
				'toggle' => true,
				'label_block' => false,				
			]
		);
		$this->add_control(
			'co_ca_total_price_alignment',
			[
				'label' => esc_html__( 'Price Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .cart_totals  .shop_table .order-total td:last-child' => 'text-align : {{VALUE}}',
				],
				'default' => 'center',
				'toggle' => true,
				'label_block' => false,				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'co_ca_total_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .cart_totals .shop_table .order-total th,
				{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .cart_totals .shop_table .order-total td',
				'separator' => 'before',
			]
		);
		/*total end*/
		$this->end_controls_section();
		/*cart total end*/
		
		/*cart change address start*/
		$this->start_controls_section(
            'cart_chng_add_styling',
            [
                'label' => esc_html__('Cart Calculator', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'		=> [
					'cart_layout' => 'cart_default',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cart_chng_add_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart_totals .shipping-calculator-button',
			]
		);
		$this->add_control(
			'cart_chng_add_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart_totals .shipping-calculator-button' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'cart_chng_add_form_heading',
			[
				'label' => esc_html__( 'Form Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'ccaf_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart_totals .shipping-calculator-form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'ccaf_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart_totals .shipping-calculator-form' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ccaf_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart_totals .shipping-calculator-form',
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ccaf_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart_totals .shipping-calculator-form',
			]
		);
		$this->add_responsive_control(
			'ccaf_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart_totals .shipping-calculator-form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ccaf_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart_totals .shipping-calculator-form',
			]
		);
		$this->add_control(
			'ccaf_input_field',
			[
				'label' => esc_html__( 'Form Field Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ccaf_input_field_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .select2-container--default .select2-selection--single,
				{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-shipping-totals .shipping-calculator-form input[type=text]',
				
			]
		);
		$this->add_control(
			'ccaf_input_field_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .select2-container--default .select2-selection--single,
				{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-shipping-totals .shipping-calculator-form input[type=text],
				.woocommerce-cart {{WRAPPER}} .tp-cart-page-wrapper .select2-container .select2-selection--single .select2-selection__rendered' => 'color: {{VALUE}}',
					'{{WRAPPER}} .tp-cart-page-wrapper .select2-container--default .select2-selection--single .select2-selection__arrow b' => 'border-color: {{VALUE}} transparent transparent transparent',
				],				
			]
		);
		$this->add_control(
			'ccaf_input_field_placeholder_color',
			[
				'label'     => esc_html__( 'Placeholder Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .select2-container--default .select2-selection--single::-webkit-input-placeholder,
				{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-shipping-totals .shipping-calculator-form input[type=text]::-webkit-input-placeholder' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ccaf_input_field_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .select2-container--default .select2-selection--single,
				{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-shipping-totals .shipping-calculator-form input[type=text]',
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ccaf_input_field_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .select2-container--default .select2-selection--single,
				{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-shipping-totals .shipping-calculator-form input[type=text]',
			]
		);
		$this->add_responsive_control(
			'ccaf_input_field_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-cart-page-wrapper .select2-container--default .select2-selection--single,
				{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-shipping-totals .shipping-calculator-form input[type=text]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ccaf_input_field_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .select2-container--default .select2-selection--single,
				{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-shipping-totals .shipping-calculator-form input[type=text]',
			]
		);
		/*button start*/
		$this->add_control(
			'co_ca_add_btn_heading',
			[
				'label' => esc_html__( 'Button Update', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'co_ca_add_btn_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .woocommerce-shipping-calculator .shipping-calculator-form .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'co_ca_add_btn_width',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Button Width', 'theplus'),
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
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .woocommerce-shipping-calculator .shipping-calculator-form .button' => 'width: {{SIZE}}{{UNIT}}',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'co_ca_add_btn_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .woocommerce-shipping-calculator .shipping-calculator-form .button',
				'separator' => 'before',
				
			]
		);
		$this->start_controls_tabs( 'co_ca_add_btn_tabs' );
			$this->start_controls_tab(
				'co_ca_add_btn_normal',
				[
					'label' => esc_html__( 'Normal', 'theplus' ),					
				]
			);
			$this->add_control(
				'co_ca_add_btn_n_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .woocommerce-shipping-calculator .shipping-calculator-form .button' => 'color: {{VALUE}}',
					],
					
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'co_ca_add_btn_n_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .woocommerce-shipping-calculator .shipping-calculator-form .button',
					'separator' => 'before',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'co_ca_add_btn_n_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .woocommerce-shipping-calculator .shipping-calculator-form .button',
				]
			);
			$this->add_responsive_control(
				'co_ca_add_btn_n_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .woocommerce-shipping-calculator .shipping-calculator-form .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'co_ca_add_btn_n_shadow',
					'label' => esc_html__( 'Box Shadow', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .woocommerce-shipping-calculator .shipping-calculator-form .button',
				]
			);	
			$this->end_controls_tab();
			$this->start_controls_tab(
				'co_ca_add_btn_hover',
				[
					'label' => esc_html__( 'Hover', 'theplus' ),					
				]
			);
			$this->add_control(
				'co_ca_add_btn_h_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .woocommerce-shipping-calculator .shipping-calculator-form .button:hover' => 'color: {{VALUE}}',
					],
					
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'co_ca_add_btn_h_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .woocommerce-shipping-calculator .shipping-calculator-form .button:hover',
					'separator' => 'before',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'co_ca_add_btn_h_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .woocommerce-shipping-calculator .shipping-calculator-form .button:hover',
				]
			);
			$this->add_responsive_control(
				'co_ca_add_btn_h_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .woocommerce-shipping-calculator .shipping-calculator-form .button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'co_ca_add_btn_h_shadow',
					'label' => esc_html__( 'Box Shadow', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .woocommerce-shipping-calculator .shipping-calculator-form .button:hover',
				]
			);
			$this->end_controls_tab();
		$this->end_controls_tabs();		
		/*button end*/
		$this->end_controls_section();
		/*cart change address end*/
		
		/*Proceed to checkout btn start*/
		$this->start_controls_section(
            'cart_pro_to_ckhout_btn_styling',
            [
                'label' => esc_html__('Proceed to Checkout Button', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
			'cptc_btn_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .wc-proceed-to-checkout .checkout-button,
					{{WRAPPER}} .tp-cart-page-wrapper .wc-proceed-to-checkout .checkout-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'cptc_btn_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .wc-proceed-to-checkout .checkout-button,
					{{WRAPPER}} .tp-cart-page-wrapper .wc-proceed-to-checkout .checkout-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'cptc_btn_align',
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
				'default' => 'center',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .wc-proceed-to-checkout' => 'justify-content:{{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'cptc_btn_width',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Button Width', 'theplus'),
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'separator' => 'before',
				'render_type' => 'ui',			
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .wc-proceed-to-checkout .checkout-button,
					{{WRAPPER}} .tp-cart-page-wrapper .wc-proceed-to-checkout .checkout-button' => 'width: {{SIZE}}{{UNIT}}',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cptc_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .wc-proceed-to-checkout .checkout-button,
				{{WRAPPER}} .tp-cart-page-wrapper .wc-proceed-to-checkout .checkout-button',
				
			]
		);
		$this->start_controls_tabs( 'cptc_tabs' );
			$this->start_controls_tab(
				'cptc_normal',
				[
					'label' => esc_html__( 'Normal', 'theplus' ),					
				]
			);
			$this->add_control(
				'cptc_n_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .wc-proceed-to-checkout .checkout-button,{{WRAPPER}} .tp-cart-page-wrapper .wc-proceed-to-checkout .checkout-button' => 'color: {{VALUE}}',
					],
					
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'cptc_n_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .wc-proceed-to-checkout .checkout-button,{{WRAPPER}} .tp-cart-page-wrapper .wc-proceed-to-checkout .checkout-button',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'cptc_n_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .wc-proceed-to-checkout .checkout-button,{{WRAPPER}} .tp-cart-page-wrapper .wc-proceed-to-checkout .checkout-button',
				]
			);
			$this->add_responsive_control(
				'cptc_n_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .wc-proceed-to-checkout .checkout-button,{{WRAPPER}} .tp-cart-page-wrapper .wc-proceed-to-checkout .checkout-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'cptc_n_shadow',
					'label' => esc_html__( 'Box Shadow', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .wc-proceed-to-checkout .checkout-button,{{WRAPPER}} .tp-cart-page-wrapper .wc-proceed-to-checkout .checkout-button',
				]
			);	
			$this->end_controls_tab();
			$this->start_controls_tab(
				'cptc_hover',
				[
					'label' => esc_html__( 'Hover', 'theplus' ),					
				]
			);
			$this->add_control(
				'cptc_h_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .wc-proceed-to-checkout .checkout-button:hover,{{WRAPPER}} .tp-cart-page-wrapper .wc-proceed-to-checkout .checkout-button:hover' => 'color: {{VALUE}}',
					],
					
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'cptc_h_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .wc-proceed-to-checkout .checkout-button:hover,{{WRAPPER}} .tp-cart-page-wrapper .wc-proceed-to-checkout .checkout-button:hover',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'cptc_h_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .wc-proceed-to-checkout .checkout-button:hover,{{WRAPPER}} .tp-cart-page-wrapper .wc-proceed-to-checkout .checkout-button:hover',
				]
			);
			$this->add_responsive_control(
				'cptc_h_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .wc-proceed-to-checkout .checkout-button:hover,{{WRAPPER}} .tp-cart-page-wrapper .wc-proceed-to-checkout .checkout-button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'cptc_h_shadow',
					'label' => esc_html__( 'Box Shadow', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .cart-collaterals .wc-proceed-to-checkout .checkout-button:hover,{{WRAPPER}} .tp-cart-page-wrapper .wc-proceed-to-checkout .checkout-button:hover',
				]
			);
			$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Proceed to checkout btn end*/
		
		/*message start*/
		$this->start_controls_section(
            'cart_message_styling',
            [
                'label' => esc_html__('Notices/Messages', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_control(
            'backendvisibilitynotice',
            [
				'label'   => esc_html__( 'Backend Visibility', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),				
				'selectors' => [
					'.elementor-editor-active .tp-cart-page-wrapper111' => 'display:block !important;flex-direction: column',				                    
				],
			]
		);
		$this->add_responsive_control(
			'cart_message_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-message,.elementor-editor-active .tp-cart-page-wrapper111 .woocommerce-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',					
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'cart_message_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-message,.elementor-editor-active .tp-cart-page-wrapper111 .woocommerce-message',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'cart_message_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-message,.elementor-editor-active .tp-cart-page-wrapper111 .woocommerce-message',
			]
		);
		$this->add_responsive_control(
			'cart_message_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-message,.elementor-editor-active .tp-cart-page-wrapper111 .woocommerce-message' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'cart_message_shadow',
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-message,.elementor-editor-active .tp-cart-page-wrapper111 .woocommerce-message',				
			]
		);
		
		$this->add_control(
			'cart_message_text_heading',
			[
				'label' => esc_html__( 'Cart Message Text', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cart_message_text_typography',
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-message,.elementor-editor-active .tp-cart-page-wrapper111 .woocommerce-message',				
			]
		);
		$this->add_control(
			'cart_message_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-message,.elementor-editor-active .tp-cart-page-wrapper111 .woocommerce-message' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'cart_message_before_icon_heading',
			[
				'label' => esc_html__( 'Icon Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'cart_before_icn_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
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
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-message::before,.elementor-editor-active .tp-cart-page-wrapper111 .woocommerce-message::before' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'condition'		=> [
					'cart_icon' => 'icon_default',
				],
            ]
        );
		$this->add_control(
			'cart_before_icn_color',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-message::before,.elementor-editor-active .tp-cart-page-wrapper111 .woocommerce-message::before' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'cart_message_undo_text_heading',
			[
				'label' => esc_html__( 'Undo Product Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cart_message_undo_text_typography',
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-message .restore-item,.elementor-editor-active .tp-cart-page-wrapper111 .woocommerce-message .restore-item',				
			]
		);
		$this->add_control(
			'cart_message_undo_text_color',
			[
				'label' => esc_html__( 'Undo Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper .woocommerce-message .restore-item,.elementor-editor-active .tp-cart-page-wrapper111 .woocommerce-message .restore-item' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/*message end*/
		
		/*cart box content start*/
		$this->start_controls_section(
            'cart_box_content_styling',
            [
                'label' => esc_html__('Content Background', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_responsive_control(
			'cart_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'cart_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_control(
			'cart_height',
			[
				'label' => esc_html__( 'Height', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'max_content',
				'options' => [
					'max_content'  => esc_html__( 'Max Content', 'theplus' ),
					'height_custom'  => esc_html__( 'Custom', 'theplus' ),
				],
			]
		);
		$this->add_responsive_control(
            'cart_max_height',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Min Height', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 2000,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-cart-page-wrapper' => 'min-height: {{SIZE}}{{UNIT}}',
				],
				'condition'		=> [
					'cart_height' => 'height_custom',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'cart_bg',
				'types'     => [ 'classic', 'gradient' ],
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .tp-cart-page-wrapper',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'cart_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper',
			]
		);
		$this->add_responsive_control(
			'cart_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-cart-page-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'cart_shadow',
				'selector' => '{{WRAPPER}} .tp-cart-page-wrapper',
			]
		);
		$this->end_controls_section();
		/*cart box content end*/
		/*style end*/
	}
	
	private function get_shortcode() {
		$settings = $this->get_settings();		
		$this->add_render_attribute( 'shortcode', 'woocommerce_cart' );
		$shortcode   = [];
		$shortcode[] = sprintf( '[%s]', $this->get_render_attribute_string( 'shortcode' ) );
		return implode("", $shortcode);
	}
	
	public function render() {
		$settings = $this->get_settings_for_display();
		$id = $this->get_id();
		if ( class_exists('woocommerce') ) {
			if(\Elementor\Plugin::$instance->editor->is_edit_mode() && (!empty($settings['backendvisibilitynotice']) && $settings['backendvisibilitynotice']=='yes')){
				echo '<div class="tp-cart-page-wrapper111" style="display:none;"><div class="woocommerce"><div style="width:100%;" class="woocommerce-notices-wrapper"><div class="woocommerce-message" role="alert">"WordPress Pennant” removed. <a href="#" class="restore-item">Undo?</a></div></div></div></div>';
			}	
			$cart_layout = !empty($settings['cart_layout']) ? $settings['cart_layout'] : '';
			$output ='';
			$display_cart_table_qty = !empty($settings['display_cart_table_qty']) ? $settings['display_cart_table_qty'] : '';
			if($cart_layout=='cart_default'){
				$output .='<div class="tp-cart-page-wrapper '.esc_attr($id).' '.$display_cart_table_qty.' tp-wcart-'.$settings['product_list_alignment'].'">';
				
				if(!empty($settings['display_cross_sell']) && $settings['display_cross_sell'] == 'no'){
					remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
				}
					if (!empty($settings['coupons_enabled']) && $settings['coupons_enabled']=='yes') {
						add_filter( 'woocommerce_coupons_enabled', '__return_false' );
					}				
					$output .= do_shortcode($this->get_shortcode());			
				$output .= '</div>';
				echo $output;
			}else if($cart_layout=='cart_custom'){
				echo '<div class="tp-cart-page-wrapper tp-wcart-'.$settings['product_list_alignment'].' tp-'.esc_attr($cart_layout).'"><div class="woocommerce">';
				if(!\Elementor\Plugin::$instance->editor->is_edit_mode()){
					echo '<div class="woocommerce-notices-wrapper">'.wc_print_notices().'</div>';
				}

				if (is_admin() && function_exists('WC') && sizeof( WC()->cart->get_cart() ) === 0) return false;
				
				if(function_exists('WC') && sizeof( WC()->cart->get_cart() ) === 0 && (isset($settings['hide_empty_cart']) && $settings['hide_empty_cart']!='yes')){
					do_action( 'woocommerce_cart_is_empty' );
					if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
						<p class="return-to-shop">
							<a class="button wc-backward" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
								<?php
									echo esc_html( apply_filters( 'woocommerce_return_to_shop_text', __( 'Return to shop', 'woocommerce' ) ) );
								?>
							</a>
						</p>
					<?php endif;
				}else if(function_exists('WC') && sizeof( WC()->cart->get_cart() ) > 0){
					//cart custom start					
					$loop_content=$settings["cartSort"];
					if(!empty($loop_content)) {
						$index=0;
						foreach($loop_content as $index => $item) {
														
							if(!empty($item['sortfield']) && $item['sortfield']=='CartData'){
								?>
								<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
									<?php do_action( 'woocommerce_before_cart_table' ); ?>

									<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
										<thead>
											<tr>
												<th class="product-remove">&nbsp;</th>
												<th class="product-thumbnail">&nbsp;</th>
												<th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
												<th class="product-price"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
												<th class="product-quantity"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
												<th class="product-subtotal"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
											</tr>
										</thead>
										<tbody>
											<?php do_action( 'woocommerce_before_cart_contents' ); ?>

											<?php
											foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
												$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
												$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

												if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
													$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
													?>
													<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

														<td class="product-remove">
															<?php
																echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
																	'woocommerce_cart_item_remove_link',
																	sprintf(
																		'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
																		esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
																		esc_html__( 'Remove this item', 'woocommerce' ),
																		esc_attr( $product_id ),
																		esc_attr( $_product->get_sku() )
																	),
																	$cart_item_key
																);
															?>
														</td>

														<td class="product-thumbnail">
														<?php
														$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

														if ( ! $product_permalink ) {
															echo $thumbnail; // PHPCS: XSS ok.
														} else {
															printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
														}
														?>
														</td>

														<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
														<?php
														if ( ! $product_permalink ) {
															echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
														} else {
															echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
														}

														do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

														// Meta data.
														echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

														// Backorder notification.
														if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
															echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
														}
														?>
														</td>

														<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
															<?php
																echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
															?>
														</td>

														<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
														<?php
														if ( $_product->is_sold_individually() ) {
															$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
														} else {
															$product_quantity = woocommerce_quantity_input(
																array(
																	'input_name'   => "cart[{$cart_item_key}][qty]",
																	'input_value'  => $cart_item['quantity'],
																	'max_value'    => $_product->get_max_purchase_quantity(),
																	'min_value'    => '0',
																	'product_name' => $_product->get_name(),
																),
																$_product,
																false
															);
														}

														echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
														?>
														</td>

														<td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>">
															<?php
																echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
															?>
														</td>
													</tr>
													<?php
												}
											}
											?>

											<?php do_action( 'woocommerce_cart_contents' ); ?>

											<tr>
												<td colspan="6" class="actions">

													<?php if ( wc_coupons_enabled() ) { ?>
														<div class="coupon">
															<label for="coupon_code"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" /> <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?></button>
															<?php do_action( 'woocommerce_cart_coupon' ); ?>
														</div>
													<?php } ?>

													<button type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>

													<?php do_action( 'woocommerce_cart_actions' ); ?>

													<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
												</td>
											</tr>

											<?php do_action( 'woocommerce_after_cart_contents' ); ?>
										</tbody>
									</table>
									<?php do_action( 'woocommerce_after_cart_table' ); ?>
								</form>
								<?php
							}
							
							if(!empty($item['sortfield']) && $item['sortfield']=='CartTotal'){
								?>
								<div class="cart-collaterals">
								<div class="cart_totals <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">

								<?php do_action( 'woocommerce_before_cart_totals' ); ?>

								<h2><?php esc_html_e( 'Cart totals', 'woocommerce' ); ?></h2>

								<table cellspacing="0" class="shop_table shop_table_responsive">

									<tr class="cart-subtotal">
										<th><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
										<td data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>"><?php wc_cart_totals_subtotal_html(); ?></td>
									</tr>

									<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
										<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
											<th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
											<td data-title="<?php echo esc_attr( wc_cart_totals_coupon_label( $coupon, false ) ); ?>"><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
										</tr>
									<?php endforeach; ?>

									<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

										<?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>

										<?php wc_cart_totals_shipping_html(); ?>

										<?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>

									<?php elseif ( WC()->cart->needs_shipping() && 'yes' === get_option( 'woocommerce_enable_shipping_calc' ) ) : ?>

										<tr class="shipping">
											<th><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></th>
											<td data-title="<?php esc_attr_e( 'Shipping', 'woocommerce' ); ?>"><?php woocommerce_shipping_calculator(); ?></td>
										</tr>

									<?php endif; ?>

									<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
										<tr class="fee">
											<th><?php echo esc_html( $fee->name ); ?></th>
											<td data-title="<?php echo esc_attr( $fee->name ); ?>"><?php wc_cart_totals_fee_html( $fee ); ?></td>
										</tr>
									<?php endforeach; ?>

									<?php
									if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) {
										$taxable_address = WC()->customer->get_taxable_address();
										$estimated_text  = '';

										if ( WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping() ) {
											/* translators: %s location. */
											$estimated_text = sprintf( ' <small>' . esc_html__( '(estimated for %s)', 'woocommerce' ) . '</small>', WC()->countries->estimated_for_prefix( $taxable_address[0] ) . WC()->countries->countries[ $taxable_address[0] ] );
										}

										if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
											foreach ( WC()->cart->get_tax_totals() as $code => $tax ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
												?>
												<tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
													<th><?php echo esc_html( $tax->label ) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
													<td data-title="<?php echo esc_attr( $tax->label ); ?>"><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
												</tr>
												<?php
											}
										} else {
											?>
											<tr class="tax-total">
												<th><?php echo esc_html( WC()->countries->tax_or_vat() ) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
												<td data-title="<?php echo esc_attr( WC()->countries->tax_or_vat() ); ?>"><?php wc_cart_totals_taxes_total_html(); ?></td>
											</tr>
											<?php
										}
									}
									?>

									<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

									<tr class="order-total">
										<th><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
										<td data-title="<?php esc_attr_e( 'Total', 'woocommerce' ); ?>"><?php wc_cart_totals_order_total_html(); ?></td>
									</tr>

									<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

								</table>

								

								<?php do_action( 'woocommerce_after_cart_totals' ); ?>

							</div>
							</div>
							<?php
							}
							
							if(!empty($item['sortfield']) && $item['sortfield']=='CheckoutButton'){
								$CheckoutButtonText = !empty($item['CheckoutButtonText']) ? $item['CheckoutButtonText'] : ''; 
								
								?>
								<div class="wc-proceed-to-checkout">
									<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="checkout-button button alt wc-forward">
										<?php echo esc_html($CheckoutButtonText); ?>
									</a>
								</div>	
								<?php
								
							}
							
							$index++;
						}   	  
					}					
						
				}	
				//cart custom end
				echo '</div></div>';
			}
		}			
	}
}	