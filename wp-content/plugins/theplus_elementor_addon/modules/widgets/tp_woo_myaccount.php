<?php 
/*
Widget Name: Woo MyAccount
Description: Woo MyAccount
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

class ThePlus_Woo_Myaccount extends Widget_Base {
		
	public function get_name() {
		return 'tp-woo-myaccount';
	}

    public function get_title() {
        return esc_html__('Woo My Account', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-id-badge theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-woo-builder');
    }
	public function get_keywords() {
		return ['myaccount page', 'myaccount', 'WooCommerce' ,'woo myaccount','my account'];
	}
	
    protected function register_controls() {	
		$this->start_controls_section(
			'section_myaccount_page',
			[
				'label' => esc_html__( 'My Account', 'theplus' ),
			]
		);
		$this->add_control(
			'select_ma_type',
			[
				'label' => esc_html__( 'Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'type_shortcode',
				'options' => [
					'type_shortcode'  => esc_html__( 'Full Shortcode', 'theplus' ),
					'type_individual'  => esc_html__( 'Individual', 'theplus' ),					
				],
			]
		);
		$this->add_control(
			'ma_layout',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'tp_ma_l_1',
				'options' => [
					'tp_ma_l_1'  => esc_html__( 'Style 1', 'theplus' ),
					'tp_ma_l_2'  => esc_html__( 'Style 2', 'theplus' ),
				],
				'condition'		=> [
					'select_ma_type' => 'type_shortcode',
				],
			]
		);
		$this->add_responsive_control(
            'ma_sec_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Left Section Width', 'theplus'),
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper.tp_ma_l_2 .woocommerce-MyAccount-navigation' => 'width: {{SIZE}}%',					
					'{{WRAPPER}} .tp-myaccount-page-wrapper.tp_ma_l_2 .woocommerce-MyAccount-content' => 'width: calc(100% - ({{SIZE}}% + 2%));',
				],
				'separator' => 'before',
				'condition'		=> [
					'select_ma_type' => 'type_shortcode',
					'ma_layout' => 'tp_ma_l_2',
				],
            ]
        );		
		$repeater = new \Elementor\Repeater();		
		$repeater->add_control(
			'sortfield',[
				'label' => esc_html__( 'Select Tab','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => [
					'Dashboard' => esc_html__( 'Dashboard','theplus' ),	
					'Orders' => esc_html__( 'Orders','theplus' ),	
					'Downloads' => esc_html__( 'Downloads','theplus' ),	
					'Addresses' => esc_html__( 'Addresses','theplus' ),	
					'Accountdetails' => esc_html__( 'Account details','theplus' ),	
				],
			]
		);
		$repeater->add_control(
			'ordertitle',[
				'label'   => esc_html__( 'Hide Title', 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper.tp-myaccount-page-ind .woocommerce-MyAccount-content > h2' => 'display:none',
				],
			]
		);
		$repeater->add_control(
			'maxorder',
			[
				'label' => esc_html__('Max. Order', 'theplus'),
				'type' => Controls_Manager::NUMBER,
				'min' => -1,
				'max' => 25,
				'step' => 1,
				'default' => -1,
				'condition' => [
					'sortfield' => 'Orders',
				],
			]
		);
		$repeater->add_control(
			'odrernotfound',
			[
				'label' => esc_html__( 'No Order', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'No order has been made yet.', 'theplus' ),
				'condition' => [
					'sortfield' => 'Orders',
				],
			]
		);
		$this->add_control(
            'maSort',
            [
				'label' => esc_html__( 'Sortable', 'theplus' ),
                'type' => Controls_Manager::REPEATER,
                'default' => [
                    [
                        'sortfield' => 'Dashboard',
                    ],
                    [
                        'sortfield' => 'Orders',
                    ],						
					[
                       'sortfield' => 'Downloads',
                    ],	
                    [
                        'sortfield' => 'Addresses',
                    ],
					[
                        'sortfield' => 'Accountdetails',
                    ],
                ],
                'separator' => 'before',
				'fields' => $repeater->get_controls(),
                'title_field' => '{{{ sortfield }}}',
				'condition'		=> [
					'select_ma_type' => 'type_individual',
				],
            ]
        ); 
		$this->end_controls_section();
		
		$this->start_controls_section(
			'section_prev_myaccount_page',
			[
				'label' => esc_html__( 'Preview', 'theplus' ),
				'condition'	=> [
					'select_ma_type' => 'type_shortcode',
				],
			]
		);
		$this->add_control(
			'select_preview',
			[
				'label' => esc_html__( 'Select Preview', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''  => esc_html__( 'Dashboard', 'theplus' ),
					'orders'  => esc_html__( 'Orders', 'theplus' ),
					'downloads'  => esc_html__( 'Downloads', 'theplus' ),
					'edit-address'  => esc_html__( 'Addresses', 'theplus' ),
					'edit-account'  => esc_html__( 'Account Details', 'theplus' ),
					'payment-methods'  => esc_html__( 'Payment Methods', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'select_preview_mode',
			[
				'label' => esc_html__( 'Addresses', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'adefaulr',
				'options' => [
					'adefaulr'  => esc_html__( 'Default', 'theplus' ),
					'aform'  => esc_html__( 'Form', 'theplus' ),
				],
				'condition' => [
					'select_preview' => 'edit-address',
				],
			]
		);
		$this->add_control(
			'form_login_register',[
				'label'   => esc_html__( 'Login & Register Form Preview', 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),				
			]
		);
		$this->end_controls_section();
		
		/*icon option start*/
		$this->start_controls_section(
			'section_icon_opt',
			[
				'label' => esc_html__( 'Extra Options', 'theplus' ),
				'condition'		=> [
					'select_ma_type' => 'type_shortcode',
				],
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
				'raw' => 'Note : Font awesome icon using below options.<a href="https://fontawesome.com/v4.7.0/icons" target="_blank">( Get Font Awesome Icon Id. )</a>',
				'content_classes' => 'tp-widget-description',
			]
		);
		$this->add_control(
			'dashboard_icon',
			[
				'label' => esc_html__( 'Dashboard', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( '\f02b', 'theplus' ),
				'selectors' => [
                    '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-navigation ul li.woocommerce-MyAccount-navigation-link--dashboard a:before' => ' content:"{{VALUE}}";',
				],
			]
		);
		$this->add_control(
			'order_icon',
			[
				'label' => esc_html__( 'Order', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( '\f02b', 'theplus' ),
				'selectors' => [
                    '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-navigation ul li.woocommerce-MyAccount-navigation-link--orders a:before' => ' content:"{{VALUE}}";',
				],
			]
		);
		$this->add_control(
			'download_icon',
			[
				'label' => esc_html__( 'Download', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( '\f02b', 'theplus' ),
				'selectors' => [
                    '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-navigation ul li.woocommerce-MyAccount-navigation-link--downloads a:before' => ' content:"{{VALUE}}";',
				],
			]
		);
		$this->add_control(
			'address_icon',
			[
				'label' => esc_html__( 'Address', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( '\f02b', 'theplus' ),
				'selectors' => [
                    '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-navigation ul li.woocommerce-MyAccount-navigation-link--edit-address a:before' => ' content:"{{VALUE}}";',
				],
			]
		);
		$this->add_control(
			'account_icon',
			[
				'label' => esc_html__( 'Account', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( '\f02b', 'theplus' ),
				'selectors' => [
                    '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-navigation ul li.woocommerce-MyAccount-navigation-link--edit-account a:before' => ' content:"{{VALUE}}";',
				],
			]
		);
		$this->add_control(
			'payment_method_icon',
			[
				'label' => esc_html__( 'Payment Methods', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( '\f02b', 'theplus' ),
				'selectors' => [
                    '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-navigation ul li.woocommerce-MyAccount-navigation-link--payment-methods a:before' => ' content:"{{VALUE}}";',
				],
			]
		);
		$this->add_control(
			'logout_icon',
			[
				'label' => esc_html__( 'Logout', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( '\f02b', 'theplus' ),
				'selectors' => [
                    '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-navigation ul li.woocommerce-MyAccount-navigation-link--customer-logout a:before' => ' content:"{{VALUE}}";',
				],
			]
		);
		$this->end_controls_section();
		/*icon option end*/
		/*style start*/
		/*menu content start*/
		$this->start_controls_section(
            'myaccount_menu_content_styling',
            [
                'label' => esc_html__('Navigation', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'		=> [
					'select_ma_type' => 'type_shortcode',
				],
            ]
        );
		$this->add_responsive_control(
			'ma_menu_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-navigation ul' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'ma_menu_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'separator'	=> 'before',
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-navigation ul' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'		=> [
					'ma_layout' => 'tp_ma_l_1',
				],
			]
		);
		$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'ma_menu_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-navigation ul',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'ma_menu_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-navigation ul',
				]
			);
			$this->add_responsive_control(
				'ma_menu_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-navigation ul' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'ma_menu_shadow',
					'label' => esc_html__( 'Box Shadow', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-navigation ul',
				]
			);	
		/*inner menu opt start*/
		$this->add_control(
			'ma_sec_in_menu_opt',
			[
				'label' => esc_html__( 'Inner Menu Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'ma_sec_in_menu_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-navigation ul li a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'ma_sec_in_menu_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-navigation ul li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_control(
			'ma_sec_in_menu_align',
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
				'default' => 'left',
				'toggle' => true,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-navigation ul li a' => 'text-align:{{VALUE}};',
				],
				'condition' => [
					'ma_layout' => 'tp_ma_l_2',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_sec_in_menu_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],								
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-navigation ul li a',
				
			]
		);	
		$this->start_controls_tabs( 'masecinmenu_tabs' );
		$this->start_controls_tab(
			'masecinmenu_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),					
			]
		);
		$this->add_control(
			'masecinmenu_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-navigation ul li a' => 'color: {{VALUE}}',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'masecinmenu_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-navigation ul li a',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'masecinmenu_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-navigation ul li a',
			]
		);
		$this->add_responsive_control(
			'masecinmenu_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-navigation ul li a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'masecinmenu_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-navigation ul li a',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'masecinmenu_Hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),					
			]
		);
		$this->add_control(
			'masecinmenu_h_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-navigation ul li a:hover,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-navigation ul li.is-active a' => 'color: {{VALUE}}',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'masecinmenu_h_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-navigation ul li a:hover,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-navigation ul li.is-active a',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'masecinmenu_h_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-navigation ul li a:hover,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-navigation ul li.is-active a',
			]
		);
		$this->add_responsive_control(
			'masecinmenu_h_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-navigation ul li a:hover,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-navigation ul li.is-active a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'masecinmenu_h_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-navigation ul li a:hover,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-navigation ul li.is-active a',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		/*inner menu opt end*/
		$this->end_controls_section();
		/*menu content end*/
		
		/*navigaton icon start*/
		$this->start_controls_section(
            'myaccount_navigation_icon_styling',
            [
                'label' => esc_html__('Navigation Icon', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'		=> [
					'select_ma_type' => 'type_shortcode',
				],
            ]
        );
		$this->add_responsive_control(
			'nav_icon_size',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
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
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-navigation ul li a:before' => 'font-size: {{SIZE}}{{UNIT}}',
				],				
			]
		);
		$this->add_responsive_control(
			'nav_icon_offset',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Offset', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-navigation ul li a:before' => 'margin-right: {{SIZE}}{{UNIT}}',
				],				
			]
		);
		$this->start_controls_tabs( 'nav_icon_tabs' );
		$this->start_controls_tab(
			'nav_icon_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),					
			]
		);
		$this->add_control(
			'nav_icon_color_n',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-navigation ul li a:before' => 'color: {{VALUE}}',
				],
				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'nav_icon_active',
			[
				'label' => esc_html__( 'Active', 'theplus' ),					
			]
		);
		$this->add_control(
			'nav_icon_color_a',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-navigation ul li:hover a:before,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-navigation ul li.is-active a:before' => 'color: {{VALUE}}',
				],
				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*navigaton icon end*/
		
		/*myaccount inner content start*/
		$this->start_controls_section(
            'myaccount_inn_content_styling',
            [
                'label' => esc_html__('Content', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'		=> [
					'select_ma_type' => 'type_shortcode',
				],
            ]
        );
		$this->add_responsive_control(
			'ma_i_c_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'ma_i_c_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'		=> [
					'ma_layout' => 'tp_ma_l_1',
				],
			]
		);
		$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'ma_i_c_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content',
					'separator' => 'before',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'ma_i_c_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content',
				]
			);
			$this->add_responsive_control(
				'ma_i_c_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'ma_i_c_shadow',
					'label' => esc_html__( 'Box Shadow', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content',
				]
			);
			$this->add_control(
				'ma_i_con_heading',
				[
					'label' => esc_html__( 'Inner Content Options', 'theplus' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before'
				]
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'ma_i_con_typography',
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content a,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content p',				
				]
			);
			$this->add_control(
				'ma_i_con_txt_color',
				[
					'label' => esc_html__( 'Text Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content p' => 'color: {{VALUE}}',
					],					
				]
			);
			$this->add_control(
				'ma_i_con_link_color',
				[
					'label' => esc_html__( 'Link Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'separator' => 'before',
					'selectors' => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content a' => 'color: {{VALUE}}',
					],					
				]
			);
			$this->add_control(
				'ma_i_con_link_h_color',
				[
					'label' => esc_html__( 'Hover Link Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content a:hover' => 'color: {{VALUE}}',
					],					
				]
			);
		$this->end_controls_section();
		/*myaccount box content start*/
		
		/*myaccount Dashboard tab start*/
		$this->start_controls_section(
            'myaccount_dashboard_tb_styling',
            [
                'label' => esc_html__('Dashboard Tab', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'		=> [
					'select_ma_type' => 'type_individual',
				],
            ]
        );
		$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'mai_i_con_typography',
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper a,{{WRAPPER}} .tp-myaccount-page-wrapper p',				
				]
			);
			$this->add_control(
				'mai_i_con_txt_color',
				[
					'label' => esc_html__( 'Text Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper p' => 'color: {{VALUE}}',
					],					
				]
			);
			$this->add_control(
				'mai_i_con_link_color',
				[
					'label' => esc_html__( 'Link Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'separator' => 'before',
					'selectors' => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper a' => 'color: {{VALUE}}',
					],					
				]
			);
			$this->add_control(
				'mai_i_con_link_h_color',
				[
					'label' => esc_html__( 'Hover Link Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper a:hover' => 'color: {{VALUE}}',
					],					
				]
			);
		$this->end_controls_section();
		/*myaccount Dashboard tab start*/
		
		/*myaccount order/download No Order Info tab start*/
		$this->start_controls_section(
            'myaccount_ordr_dwnd_styling',
            [
                'label' => esc_html__('Order/Download Empty Info', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'od_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-info',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'od_message_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-info',
			]
		);
		$this->add_responsive_control(
			'od_message_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-info' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'od_message_shadow',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-info',				
			]
		);
		$this->add_control(
			'od_message_text_heading',
			[
				'label' => esc_html__( 'Message Text', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'od_message_text_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-message,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Message',
			]
		);
		$this->add_control(
			'od_message_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-message,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Message' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'od_message_before_icon_heading',
			[
				'label' => esc_html__( 'Icon Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'od_before_icn_size',
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
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-message::before,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Message::before' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'condition'		=> [
					'cart_icon' => 'icon_default',
				],
            ]
        );
		$this->add_control(
			'od_before_icn_color',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-message::before,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Message::before' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'od_button',
			[
				'label' => esc_html__( 'Button Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'od_b_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-message .woocommerce-Button,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Message .woocommerce-Button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'od_b_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-message .woocommerce-Button,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Message .woocommerce-Button',				
			]
		);
		$this->start_controls_tabs( 'od_b_tabs' );
		$this->start_controls_tab(
			'od_b_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),					
			]
		);
		$this->add_control(
			'od_b_n_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-message .woocommerce-Button,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Message .woocommerce-Button' => 'color: {{VALUE}}',
				],
				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'od_b_n_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-message .woocommerce-Button,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Message .woocommerce-Button',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'od_b_n_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-message .woocommerce-Button,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Message .woocommerce-Button',
			]
		);
		$this->add_responsive_control(
			'od_b_n_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-message .woocommerce-Button,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Message .woocommerce-Button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'od_b_n_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-message .woocommerce-Button,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Message .woocommerce-Button',
				'separator' => 'before',
			]
		);	
		$this->end_controls_tab();
		$this->start_controls_tab(
			'od_b_hover',
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
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-message .woocommerce-Button:hover,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Message .woocommerce-Button:hover' => 'color: {{VALUE}}',
				],
				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'od_b_h_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-message .woocommerce-Button:hover,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Message .woocommerce-Button:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'od_b_h_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-message .woocommerce-Button:hover,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Message .woocommerce-Button:hover',
			]
		);
		$this->add_responsive_control(
			'od_b_h_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-message .woocommerce-Button:hover,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Message .woocommerce-Button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'od_b_h_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-message .woocommerce-Button:hover,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Message .woocommerce-Button:hover',
				'separator' => 'before',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*myaccount order/download tab empty start*/
		
		/*myaccount order tab start*/
		$this->start_controls_section(
            'myaccount_order_tb_styling',
            [
                'label' => esc_html__('Order Tab', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_responsive_control(
			'ma_ot_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ma_ot_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders,
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td,
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders th',
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ma_ot_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders',
			]
		);
		$this->add_responsive_control(
			'ma_ot_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ma_ot_shadow',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders',
			]
		);
		$this->add_control(
			'ma_ot_in_heading',
			[
				'label' => esc_html__( 'Inner Border Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ma_ot_in_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td,
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders th',
			]
		);
		$this->add_control(
			'ma_ot_head_heading',
			[
				'label' => esc_html__( 'Heading Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_ot_head_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders th',				
			]
		);
		$this->add_control(
			'ma_ot_head_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders th' => 'color: {{VALUE}};',
				],
			]
		);	
		$this->add_control(
			'ma_ot_sub_text_heading',
			[
				'label' => esc_html__( 'Sub Text Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_ot_sub_text_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td',				
			]
		);
		$this->add_control(
			'ma_ot_sub_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td' => 'color: {{VALUE}};',
				],
			]
		);	
		$this->add_control(
			'ma_ot_on_heading',
			[
				'label' => esc_html__( 'Order Number Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_ot_on_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td a',				
			]
		);
		$this->add_control(
			'ma_ot_on_color',
			[
				'label' => esc_html__( 'Order Number Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td a' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'ma_ot_on_h_color',
			[
				'label' => esc_html__( 'Order Number Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td a:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'ma_ot_vo_button',
			[
				'label' => esc_html__( 'View Order Button Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'ma_ot_vo_btn_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .woocommerce-button,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .view' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'ma_ot_vo_btn_width',
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
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .woocommerce-button,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .view' => 'width: {{SIZE}}{{UNIT}}',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_ot_vo_btn_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .woocommerce-button,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .view',
				'separator' => 'before',
				
			]
		);
		$this->add_control(
			'ma_ot_vo_btn_align',
			[
				'label' => esc_html__( 'Text Alignment', 'theplus' ),
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
				'default' => 'left',
				'toggle' => true,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .woocommerce-button,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .view' => 'text-align:{{VALUE}};',
				],
			]
		);
		$this->start_controls_tabs( 'ma_ot_vo_btn_tabs' );
			$this->start_controls_tab(
				'ma_ot_vo_btn_normal',
				[
					'label' => esc_html__( 'Normal', 'theplus' ),					
				]
			);
			$this->add_control(
				'ma_ot_vo_btn_n_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .woocommerce-button,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .view' => 'color: {{VALUE}}',
					],
					
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'ma_ot_vo_btn_n_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .woocommerce-button,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .view',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'ma_ot_vo_btn_n_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .woocommerce-button,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .view',
				]
			);
			$this->add_responsive_control(
				'ma_ot_vo_btn_n_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .woocommerce-button,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .view' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'ma_ot_vo_btn_n_shadow',
					'label' => esc_html__( 'Box Shadow', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .woocommerce-button,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .view',
				]
			);	
			$this->end_controls_tab();
			$this->start_controls_tab(
				'ma_ot_vo_btn_hover',
				[
					'label' => esc_html__( 'Hover', 'theplus' ),					
				]
			);
			$this->add_control(
				'ma_ot_vo_btn_h_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .woocommerce-button:hover,
						{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .view:hover' => 'color: {{VALUE}}',
					],					
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'ma_ot_vo_btn_h_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .woocommerce-button:hover,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .view:hover',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'ma_ot_vo_btn_h_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .woocommerce-button:hover,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .view:hover',
				]
			);
			$this->add_responsive_control(
				'ma_ot_vo_btn_h_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .woocommerce-button:hover,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .view:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'ma_ot_vo_btn_h_shadow',
					'label' => esc_html__( 'Box Shadow', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .woocommerce-button:hover,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .my_account_orders td .view:hover',
				]
			);
			$this->end_controls_tab();
		$this->end_controls_tabs();	

		$this->add_control(
			'ma_ot_noorder_heading',
			[
				'label' => esc_html__( 'No Order Found', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'ma_ot_noorder_align',
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
				'default' => 'left',
				'toggle' => true,				
				'selectors' => [
					'{{WRAPPER}} .tp-woo-myacc-order-notfound' => 'text-align:{{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_ot_noorder_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-woo-myacc-order-notfound',				
			]
		);
		$this->add_control(
			'ma_ot_noorder_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-myacc-order-notfound' => 'color: {{VALUE}}',
				],
				
			]
		);
		$this->end_controls_section();
		/*myaccount order tab start*/
		
		/*myaccount download tab start*/
		$this->start_controls_section(
            'myaccount_dwnd_tb_styling',
            [
                'label' => esc_html__('Download Tab', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_responsive_control(
			'ma_ot_dwnd_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .woocommerce-table--order-downloads' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ma_ot_dwnd_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .woocommerce-table--order-downloads,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .woocommerce-table--order-downloads td,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .woocommerce-table--order-downloads th',
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ma_ot_dwnd_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .woocommerce-table--order-downloads',
			]
		);
		$this->add_responsive_control(
			'ma_ot_dwnd_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .woocommerce-table--order-downloads' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ma_ot_dwnd_shadow',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .woocommerce-table--order-downloads',
			]
		);
		$this->add_control(
			'ma_ot_dwnd_heading',
			[
				'label' => esc_html__( 'Inner Border Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ma_ot_dwnd_in_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .woocommerce-table--order-downloads td,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .woocommerce-table--order-downloads th',
			]
		);
		$this->add_control(
			'ma_ot_dwnd_head_heading',
			[
				'label' => esc_html__( 'Heading Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_ot_dwnd_head_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .woocommerce-table--order-downloads th',				
			]
		);
		$this->add_control(
			'ma_ot_dwnd_head_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .woocommerce-table--order-downloads th' => 'color: {{VALUE}};',
				],
			]
		);	
		$this->add_control(
			'ma_ot_dwnd_sub_heading',
			[
				'label' => esc_html__( 'Sub Text Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_ot_dwnd_sub_text_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .woocommerce-table--order-downloads td',				
			]
		);
		$this->add_control(
			'ma_ot_dwnd_sub_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .woocommerce-table--order-downloads td' => 'color: {{VALUE}};',
				],
			]
		);	
		$this->add_control(
			'ma_ot_dwnd_pro_heading',
			[
				'label' => esc_html__( 'Product Name Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_ot_dwnd_pro_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .woocommerce-table--order-downloads .download-product a',				
			]
		);
		$this->add_control(
			'ma_ot_dwnd_pro_color',
			[
				'label' => esc_html__( 'Product Name Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .woocommerce-table--order-downloads .download-product a' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'ma_ot_dwnd_pro_h_color',
			[
				'label' => esc_html__( 'Product Name Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .woocommerce-table--order-downloads .download-product a:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'ma_ot_d_btn_heading',
			[
				'label' => esc_html__( 'Download File Button Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'ma_ot_d_btn_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .download-file .woocommerce-MyAccount-downloads-file' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'ma_ot_d_btn_width',
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
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .download-file .woocommerce-MyAccount-downloads-file' => 'width: {{SIZE}}{{UNIT}}',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_ot_d_btn_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .download-file .woocommerce-MyAccount-downloads-file',
				'separator' => 'before',
				
			]
		);
		$this->start_controls_tabs( 'ma_ot_d_btn_tabs' );
			$this->start_controls_tab(
				'ma_ot_d_btn_normal',
				[
					'label' => esc_html__( 'Normal', 'theplus' ),					
				]
			);
			$this->add_control(
				'ma_ot_d_btn_n_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .download-file .woocommerce-MyAccount-downloads-file' => 'color: {{VALUE}}',
					],					
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'ma_ot_d_btn_n_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .download-file .woocommerce-MyAccount-downloads-file',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'ma_ot_d_btn_n_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .download-file .woocommerce-MyAccount-downloads-file',
				]
			);
			$this->add_responsive_control(
				'ma_ot_d_btn_n_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .download-file .woocommerce-MyAccount-downloads-file' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'ma_ot_d_btn_n_shadow',
					'label' => esc_html__( 'Box Shadow', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .download-file .woocommerce-MyAccount-downloads-file',
				]
			);	
			$this->end_controls_tab();
			$this->start_controls_tab(
				'ma_ot_d_btn_hover',
				[
					'label' => esc_html__( 'Hover', 'theplus' ),					
				]
			);
			$this->add_control(
				'ma_ot_d_btn_h_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .download-file .woocommerce-MyAccount-downloads-file:hover' => 'color: {{VALUE}}',
					],					
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'ma_ot_d_btn_h_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .download-file .woocommerce-MyAccount-downloads-file:hover',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'ma_ot_d_btn_h_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .download-file .woocommerce-MyAccount-downloads-file:hover',
				]
			);
			$this->add_responsive_control(
				'ma_ot_d_btn_h_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .download-file .woocommerce-MyAccount-downloads-file:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'ma_ot_d_btn_h_shadow',
					'label' => esc_html__( 'Box Shadow', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-order-downloads .download-file .woocommerce-MyAccount-downloads-file:hover',
				]
			);
			$this->end_controls_tab();
		$this->end_controls_tabs();		
		$this->end_controls_section();
		/*myaccount download tab start*/
		
		/*myaccount Addresses tab start*/
		$this->start_controls_section(
            'myaccount_ad_tb_styling',
            [
                'label' => esc_html__('Addresses Tab', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_responsive_control(
			'ma_ad_m_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Addresses .woocommerce-Address' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'ma_ad_m_bg',
				'types'     => [ 'classic', 'gradient' ],
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Addresses .woocommerce-Address',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ma_ad_m_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Addresses .woocommerce-Address',
			]
		);
		$this->add_responsive_control(
			'ma_ad_m_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Addresses .woocommerce-Address' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ma_ad_m_shadow',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Addresses .woocommerce-Address',
			]
		);
		$this->add_control(
			'ma_ad_heading_h',
			[
				'label' => esc_html__( 'Heading Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_ad_heading_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Addresses h3,
							{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .addresses .woocommerce-Address-title h3',
			]
		);
		$this->add_control(
			'ma_ad_heading_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Addresses h3,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .addresses .woocommerce-Address-title h3' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'ma_ad_sub_heading_h',
			[
				'label' => esc_html__( 'Sub Text Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_ad_sub_txt_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Addresses .woocommerce-Address  address,
								{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .addresses .woocommerce-Address .woocommerce-Address-title address',
			]
		);
		$this->add_control(
			'ma_ad_sub_txt_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Addresses .woocommerce-Address  address,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .addresses .woocommerce-Address .woocommerce-Address-title address' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'ma_ad_edit_heading_h',
			[
				'label' => esc_html__( 'Edit Button Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
			]
		);
		$this->add_responsive_control(
			'ma_ad_edit_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Addresses .woocommerce-Address .edit,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .addresses .woocommerce-Address .woocommerce-Address-title .edit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_ad_edt_txt_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Addresses .woocommerce-Address .edit,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .addresses .woocommerce-Address .woocommerce-Address-title .edit',
			]
		);
		$this->start_controls_tabs( 'ma_ad_edt_tabs' );
		$this->start_controls_tab(
			'ma_ad_edt_n_tab',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'ma_ad_edt_txt_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Addresses .woocommerce-Address .edit,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .addresses .woocommerce-Address .woocommerce-Address-title .edit' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'ma_ad_edt_txt_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Addresses .woocommerce-Address .edit,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .addresses .woocommerce-Address .woocommerce-Address-title .edit',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ma_ad_edt_txt_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Addresses .woocommerce-Address .edit,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .addresses .woocommerce-Address .woocommerce-Address-title .edit',
			]
		);
		$this->add_responsive_control(
			'ma_ad_edt_txt_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Addresses .woocommerce-Address .edit,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .addresses .woocommerce-Address .woocommerce-Address-title .edit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ma_ad_edt_txt_shadow',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Addresses .woocommerce-Address .edit,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .addresses .woocommerce-Address .woocommerce-Address-title .edit',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'ma_ad_edt_h_tab',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'ma_ad_edt_txt_color_h',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Addresses .woocommerce-Address .edit:hover,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .addresses .woocommerce-Address .woocommerce-Address-title .edit:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'ma_ad_edt_txt_bg_h',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Addresses .woocommerce-Address .edit:hover,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .addresses .woocommerce-Address .woocommerce-Address-title .edit:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ma_ad_edt_txt_border_h',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Addresses .woocommerce-Address .edit:hover,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .addresses .woocommerce-Address .woocommerce-Address-title .edit:hover',
			]
		);
		$this->add_responsive_control(
			'ma_ad_edt_txt_br_h',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Addresses .woocommerce-Address .edit:hover,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .addresses .woocommerce-Address .woocommerce-Address-title .edit:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ma_ad_edt_txt_shadow_h',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-Addresses .woocommerce-Address .edit:hover,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .addresses .woocommerce-Address .woocommerce-Address-title .edit:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*myaccount Addresses tab end*/
		
		/*billing/shipping form heading start*/
		$this->start_controls_section(
            'bs_form_head_styling',
            [
                'label' => esc_html__('Billing/Shipping Form Heading', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_responsive_control(
			'bs_head_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form h3' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'bs_head_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form h3',
				
			]
		);
		$this->add_control(
			'bs_head_color',
			[
				'label' => esc_html__( 'Heading Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form h3' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_section();
		/*billing/shipping form heading end*/
		
		/*billing/shipping form label start*/
		$this->start_controls_section(
            'bs_form_label_styling',
            [
                'label' => esc_html__('Billing/Shipping Form Label', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_responsive_control(
			'bs_label_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'bs_label_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'bs_label_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields label',
			]
		);
		$this->add_control(
			'bs_label_color',
			[
				'label' => esc_html__( 'Label Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields label' => 'color: {{VALUE}}',
				],
			]
		);		
		$this->add_control(
			'bs_req_symbol_color',
			[
				'label' => esc_html__( 'Required Symbol', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields label .required' => 'color: {{VALUE}} !important',
				],
			]
		);		
		$this->end_controls_section();
		/*billing/shipping form label end*/
		
		/*billing/shipping form Input Fields start*/
		$this->start_controls_section(
            'bs_form_input_styling',
            [
                'label' => esc_html__('Billing/Shipping Form Input Field', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_responsive_control(
			'bs_input_inner_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=text],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=tel],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=email]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'bs_input_inner_margin',
			[
			'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=text],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=tel],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=email],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper .select2-selection__rendered' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'bs_input_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=text],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=tel],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=email],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper .select2-selection__rendered',
			]
		);		
		$this->add_control(
			'bs_input_placeholder_color',
			[
				'label'     => esc_html__( 'Placeholder Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=text],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=tel],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=email],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper .select2-selection__rendered' => 'color: {{VALUE}};',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_bs_input_field_style' );
		$this->start_controls_tab(
			'tab_bs_input_field_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'bs_input_field_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=text],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=tel],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=email],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper .select2-selection__rendered' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'bs_input_field_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=text],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=tel],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=email],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper .select2-selection__rendered',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'bs_box_border_color',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=text],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=tel],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=email],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper .select2-selection__rendered',
			]
		);		
		
		$this->add_responsive_control(
			'bs_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=text],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=tel],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=email],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper .select2-selection__rendered' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'bs_box_shadow',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=text],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=tel],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=email],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper .select2-selection__rendered',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_bs_input_field_focus',
			[
				'label' => esc_html__( 'Focus', 'theplus' ),
			]
		);
		$this->add_control(
			'bs_input_field_focus_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=text]:focus,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=tel]:focus,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=email]:focus,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper .select2-selection__rendered:focus' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'bs_input_field_focus_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=text]:focus,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=tel]:focus,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=email]:focus,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper .select2-selection__rendered:focus',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'bs_box_border_hover',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=text]:focus,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=tel]:focus,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=email]:focus,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper .select2-selection__rendered:focus',				
			]
		);		
		$this->add_responsive_control(
			'bs_border_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=text]:focus,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=tel]:focus,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=email]:focus,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper .select2-selection__rendered:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'bs_box_active_shadow',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=text]:focus,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=tel]:focus,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper input[type=email]:focus,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields .woocommerce-input-wrapper .select2-selection__rendered:focus',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();	
		$this->end_controls_section();
		/*billing form Input Fields end*/
		
		/*billing/shipping button start*/
		$this->start_controls_section(
            'bs_form_button_styling',
            [
                'label' => esc_html__('Billing/Shipping Button', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_responsive_control(
			'bs_b_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields button.button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'bs_b_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields button.button',				
			]
		);
		$this->start_controls_tabs( 'bs_b_tabs' );
		$this->start_controls_tab(
			'bs_b_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),					
			]
		);
		$this->add_control(
			'bs_b_n_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields button.button' => 'color: {{VALUE}}',
				],
				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'bs_b_n_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields button.button',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'bs_b_n_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields button.button',
			]
		);
		$this->add_responsive_control(
			'bs_b_n_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields button.button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'bs_b_n_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields button.button',
				'separator' => 'before',
			]
		);	
		$this->end_controls_tab();
		$this->start_controls_tab(
			'bs_b_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),					
			]
		);
		$this->add_control(
			'bs_b_h_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields button.button:hover' => 'color: {{VALUE}}',
				],
				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'bs_b_h_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields button.button:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'bs_b_h_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields button.button:hover',
			]
		);
		$this->add_responsive_control(
			'bs_b_h_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields button.button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'bs_b_h_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields button.button:hover',
				'separator' => 'before',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*billing form button end*/
		
		/*billing/shipping form start*/
		$this->start_controls_section(
            'bs_main_styling',
            [
                'label' => esc_html__('Billing/Shipping Form', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_responsive_control(
			'bs_main_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'bs_main_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'bs_main_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'bs_main_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields',
				]
			);
			$this->add_responsive_control(
				'bs_main_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'bs_main_shadow',
					'label' => esc_html__( 'Box Shadow', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .woocommerce-MyAccount-content form .woocommerce-address-fields',
				]
			);	
		$this->end_controls_section();
		/*billing/shipping form end*/
		
		/*myaccount Account details tab start*/
		$this->start_controls_section(
            'myaccount_details_tab_styling',
            [
                'label' => esc_html__('Account Details Tab', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_control(
			'ma_dt_label',
			[
				'label' => esc_html__( 'Label Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,				
			]
		);
		$this->add_responsive_control(
			'ma_dt_label_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account label,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'ma_dt_label_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account label,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_dt_label_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account label,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm label',
			]
		);
		$this->add_control(
			'ma_dt_label_color',
			[
				'label' => esc_html__( 'Label Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account label,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm label' => 'color: {{VALUE}}',
					'separator' => 'after',
				],
			]
		);		
		$this->add_control(
			'ma_dt_req_symbel_color',
			[
				'label' => esc_html__( 'Required Symbol', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account label .required,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm label .required' => 'color: {{VALUE}} !important',
				],
			]
		);		
		$this->add_control(
			'ma_dt_input',
			[
				'label' => esc_html__( 'Input Field Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'ma_dt_input_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="text"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="email"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="password"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="text"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="email"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="password"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'ma_dt_input_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="text"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="email"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="password"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="text"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="email"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="password"]' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_dt_input_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="text"],
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="email"],
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="password"],
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="text"],
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="email"],
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="password"]',
			]
		);		
		$this->add_control(
			'ma_dt_input_placeholder_color',
			[
				'label'     => esc_html__( 'Placeholder Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input::-webkit-input-placeholder,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account email::-webkit-input-placeholder
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input::-webkit-input-placeholder,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm email::-webkit-input-placeholder' => 'color: {{VALUE}};',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_ma_dt_input_style' );
		$this->start_controls_tab(
			'tab_ma_dt_input_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'dt_in_n_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="text"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="email"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="password"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="text"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="email"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="password"]' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'dt_in_n_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="text"],
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="email"],
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="password"],
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="text"],
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="email"],
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="password"]',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'dt_in_n_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="text"],
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="email"],
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="password"],
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="text"],
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="email"],
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="password"]',
			]
		);
		$this->add_responsive_control(
			'dt_in_n_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="text"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="email"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="password"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="text"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="email"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="password"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],						
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'dt_in_n_shadow',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="text"],
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="email"],
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="password"],
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="text"],
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="email"],
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="password"]',
			]
		);
		$this->end_controls_tab();
		
		$this->start_controls_tab(
			'tab_dt_in_focus',
			[
				'label' => esc_html__( 'Focus', 'theplus' ),
			]
		);
		$this->add_control(
			'dt_in_h_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="text"]:focus,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="email"]:focus,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="password"]:focus,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="text"]:focus,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="email"]:focus,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="password"]:focus' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'dt_in_h_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="text"]:focus,
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="email"]:focus,
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="password"]:focus,
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="text"]:focus,
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="email"]:focus,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="password"]:focus',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'dt_in_h_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="text"]:focus,
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="email"]:focus,
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="password"]:focus,
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="text"]:focus,
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="email"]:focus,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="password"]:focus',
			]
		);				
		$this->add_responsive_control(
			'dt_in_h_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="text"]:focus,
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="email"]:focus,
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="password"]:focus,
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="text"]:focus,
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="email"]:focus,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="password"]:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],						
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'dt_in_h_shadow',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="text"]:focus,
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="email"]:focus,
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account input[type="password"]:focus,
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="text"]:focus,
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="email"]:focus,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm input[type="password"]:focus',
			]
		);	
		$this->end_controls_tab();
		$this->end_controls_tabs();	
		$this->add_control(
			'ma_dt_display_name_set',
			[
				'label' => esc_html__( 'Display Name Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_dt_dn_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm span em,
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account span em',
			]
		);
		$this->add_control(
			'ma_dt_dn_color',
			[
				'label' => esc_html__( 'Heading Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm span em,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account span em' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'ma_dt_field_set_heading',
			[
				'label' => esc_html__( 'Field Set Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_dt_fs_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm fieldset,
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account fieldset',
			]
		);
		$this->add_control(
			'ma_dt_fs_color',
			[
				'label' => esc_html__( 'Heading Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm fieldset,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account fieldset' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ma_dt_fs_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm fieldset,
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account fieldset',
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ma_dt_fs_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm fieldset,
				{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account fieldset',
			]
		);
		$this->add_control(
			'ma_dt_ac_btn_heading',
			[
				'label' => esc_html__( 'Button Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator'	=> 'before',
			]
		);
		$this->add_responsive_control(
			'dt_ac_btn_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account .woocommerce-Button,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm .woocommerce-Button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'dt_ac_btn_width',
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
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account .woocommerce-Button,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm .woocommerce-Button' => 'width: {{SIZE}}{{UNIT}}',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'dt_ac_btn_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account .woocommerce-Button,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm .woocommerce-Button',
				'separator' => 'before',
				
			]
		);
		$this->start_controls_tabs( 'dt_ac_btn_tabs' );
			$this->start_controls_tab(
				'dt_ac_btn_normal',
				[
					'label' => esc_html__( 'Normal', 'theplus' ),					
				]
			);
			$this->add_control(
				'dt_ac_btn_n_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account .woocommerce-Button,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm .woocommerce-Button' => 'color: {{VALUE}}',
					],
					
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'dt_ac_btn_n_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account .woocommerce-Button,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm .woocommerce-Button',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'dt_ac_btn_n_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account .woocommerce-Button,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm .woocommerce-Button',
				]
			);
			$this->add_responsive_control(
				'dt_ac_btn_n_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account .woocommerce-Button,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm .woocommerce-Button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'dt_ac_btn_n_shadow',
					'label' => esc_html__( 'Box Shadow', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account .woocommerce-Button,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm .woocommerce-Button',
				]
			);	
			$this->end_controls_tab();
			$this->start_controls_tab(
				'dt_ac_btn_hover',
				[
					'label' => esc_html__( 'Hover', 'theplus' ),					
				]
			);
			$this->add_control(
				'dt_ac_btn_h_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account .woocommerce-Button:hover,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm .woocommerce-Button:hover' => 'color: {{VALUE}}',
					],					
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'dt_ac_btn_h_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account .woocommerce-Button:hover,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm .woocommerce-Button:hover',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'dt_ac_btn_h_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account .woocommerce-Button:hover,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm .woocommerce-Button:hover',
				]
			);
			$this->add_responsive_control(
				'dt_ac_btn_h_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account .woocommerce-Button:hover,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm .woocommerce-Button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'dt_ac_btn_h_shadow',
					'label' => esc_html__( 'Box Shadow', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .edit-account .woocommerce-Button:hover,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-MyAccount-content .woocommerce-EditAccountForm .woocommerce-Button:hover',
				]
			);
			$this->end_controls_tab();
		$this->end_controls_tabs();		
		$this->end_controls_section();
		/*myaccount Account details tab end*/
		
		/*myaccount payment details tab start*/
		$this->start_controls_section(
            'myaccount_payment_tab_styling',
            [
                'label' => esc_html__('Payment Tab', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_control(
			'map_label_heading',
			[
				'label' => esc_html__( 'Label', 'theplus' ),
				'type' => Controls_Manager::HEADING,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'dmap_label_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} #add_payment_method #payment ul.payment_methods li label',
				
			]
		);	
		$this->add_control(
			'map_label_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #add_payment_method #payment ul.payment_methods li label' => 'color: {{VALUE}}',
				],					
			]
		);
		$this->add_control(
			'dmap_pb_label_heading',
			[
				'label' => esc_html__( 'Payment Box', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'dmap_pb_label_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} #add_payment_method #payment div.payment_box p, {{WRAPPER}} #add_payment_method #payment div.payment_box p a',
				
			]
		);	
		$this->add_control(
			'dmap_pb_label_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #add_payment_method #payment div.payment_box p, {{WRAPPER}} #add_payment_method #payment div.payment_box p a' => 'color: {{VALUE}}',
				],					
			]
		);
		$this->add_control(
			'dmap_fieldset_heading',
			[
				'label' => esc_html__( 'Fieldset Label', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'dmap_fieldset_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} #add_payment_method #payment div.payment_box .wc-credit-card-form label',
				
			]
		);	
		$this->add_control(
			'dmap_fieldset_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #add_payment_method #payment div.payment_box .wc-credit-card-form label' => 'color: {{VALUE}}',
				],					
			]
		);
		$this->end_controls_section();
		/*myaccount payment details tab end*/
		
		/*myaccount login form start*/		
		$this->start_controls_section(
            'myaccount_login_form_styling',
            [
                'label' => esc_html__('Login Form', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );		
		$this->add_control(
			'lf_form_heading',
			[
				'label' => esc_html__( 'Form Heading Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'lf_form_heading_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce h2',				
			]
		);
		$this->add_control(
			'lf_form_heading_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce h2' => 'color: {{VALUE}};',
				],
			]
		);	
		$this->add_control(
			'lf_label_heading',
			[
				'label' => esc_html__( 'Label Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator'	=> 'before',
			]
		);
		$this->add_responsive_control(
			'lf_label_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login label,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login .woocommerce-LostPassword a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'lf_label_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login label,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login .woocommerce-LostPassword a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'lf_label_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login label',
			]
		);
		$this->add_control(
			'lf_label_color',
			[
				'label' => esc_html__( 'Label Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login label' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'lf_label_req_symbol_color',
			[
				'label' => esc_html__( 'Required Symbol', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login label .required' => 'color: {{VALUE}} !important',
				],
			]
		);	
		$this->add_control(
			'lf_input_heading',
			[
				'label' => esc_html__( 'Input Field Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'lf_input_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input[type="text"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input[type="password"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'lf_input_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input[type="text"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input[type="password"]' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'lf_input_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input[type="text"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input[type="password"]',
			]
		);		
		$this->add_control(
			'lf_input_placeholder_color',
			[
				'label'     => esc_html__( 'Placeholder Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input::-webkit-input-placeholder,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login  password::-webkit-input-placeholder' => 'color: {{VALUE}};',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_lf_input' );
		$this->start_controls_tab(
			'tab_lf_input_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'lf_input_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input[type="text"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input[type="password"]' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'lf_input_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input[type="text"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input[type="password"]',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'lf_input_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input[type="text"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input[type="password"]',
			]
		);		
		
		$this->add_responsive_control(
			'lf_input_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input[type="text"],{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input[type="password"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'lf_input_shadow',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input[type="text"],{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input[type="password"]',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_lf_input_focus',
			[
				'label' => esc_html__( 'Focus', 'theplus' ),
			]
		);
		$this->add_control(
			'lf_input_focus_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input[type="text"]:focus,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input[type="password"]:focus' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'lf_input_focus_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input[type="text"]:focus,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input[type="password"]:focus',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'lf_input_hover_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input[type="text"]:focus,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input[type="password"]:focus',
			]
		);		
		$this->add_responsive_control(
			'lf_input_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input[type="text"]:focus,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input[type="password"]:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'lf_input_hover_shadow',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input[type="text"]:focus,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login input[type="password"]:focus',
			]
		);		
		$this->end_controls_tab();
		$this->end_controls_tabs();	
		$this->add_control(
			'lf_remember_me_heading',
			[
				'label' => esc_html__( 'Remember Me Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'lf_remember_me_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login label span',
			]
		);
		$this->add_control(
			'lf_remember_me_color',
			[
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login label span' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'lf_lost_pass_heading',
			[
				'label' => esc_html__( 'Lost Password Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'lf_loastpass_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login .lost_password a',
			]
		);
		$this->add_control(
			'lf_lostpass_color',
			[
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login .lost_password a' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'lf_lostpass_h_color',
			[
				'label'     => esc_html__( 'Hover Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login .lost_password a:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'lf_button_heading',
			[
				'label' => esc_html__( 'Login Button Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'lf_button_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login .woocommerce-form-login__submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'lf_button_width',
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
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login .woocommerce-form-login__submit' => 'width: {{SIZE}}{{UNIT}}',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'lf_button_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login .woocommerce-form-login__submit',				
			]
		);
		$this->start_controls_tabs( 'lf_button_tabs' );
			$this->start_controls_tab(
				'lf_button_normal',
				[
					'label' => esc_html__( 'Normal', 'theplus' ),					
				]
			);
			$this->add_control(
				'lf_button_n_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login .woocommerce-form-login__submit' => 'color: {{VALUE}}',
					],
					
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'lf_button_n_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login .woocommerce-form-login__submit',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'lf_button_n_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login .woocommerce-form-login__submit',
				]
			);
			$this->add_responsive_control(
				'lf_button_n_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login .woocommerce-form-login__submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'lf_button_n_shadow',
					'label' => esc_html__( 'Box Shadow', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login .woocommerce-form-login__submit',
				]
			);	
			$this->end_controls_tab();
			$this->start_controls_tab(
				'lf_button_hover',
				[
					'label' => esc_html__( 'Hover', 'theplus' ),					
				]
			);
			$this->add_control(
				'lf_button_h_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login .woocommerce-form-login__submit:hover' => 'color: {{VALUE}}',
					],
					
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'lf_button_h_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login .woocommerce-form-login__submit:hover',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'lf_button_h_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login .woocommerce-form-login__submit:hover',
				]
			);
			$this->add_responsive_control(
				'lf_button_h_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login .woocommerce-form-login__submit:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'lf_button_h_shadow',
					'label' => esc_html__( 'Box Shadow', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login .woocommerce-form-login__submit:hover',
				]
			);
			$this->end_controls_tab();
		$this->end_controls_tabs();		
		$this->end_controls_section();
		/*myaccount login form end*/
		
		/*myaccount lost password form start*/
		$this->start_controls_section(
            'myaccount_lost_pass_form_styling',
            [
                'label' => esc_html__('Lost Password Form', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_control(
			'lp_heading',
			[
				'label' => esc_html__( 'Heading Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'lp_heading_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password p:first-of-type',
			]
		);
		$this->add_control(
			'lp_heading_color',
			[
				'label' => esc_html__( 'Label Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password p:first-of-type' => 'color: {{VALUE}};',
				],
			]
		);		
		$this->add_control(
			'lp_label',
			[
				'label' => esc_html__( 'Label Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'lp_label_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password label',
			]
		);
		$this->add_control(
			'lp_label_color',
			[
				'label' => esc_html__( 'Label Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password label' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'lp_input_field',
			[
				'label' => esc_html__( 'Input Field Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'lp_input_field_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password input[type="text"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'co_bf_input_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password input[type="text"]',
			]
		);
		$this->start_controls_tabs( 'tabs_co_bf_input_field_style' );
		$this->start_controls_tab(
			'tab_co_bf_input_field_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'co_bf_input_field_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password input[type="text"]' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'co_bf_input_field_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password input[type="text"]',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'co_bf_box_border_color',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password input[type="text"]',
			]
		);		
		
		$this->add_responsive_control(
			'co_bf_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password input[type="text"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'co_bf_box_shadow',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password input[type="text"]',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_co_bf_input_field_focus',
			[
				'label' => esc_html__( 'Focus', 'theplus' ),
			]
		);
		$this->add_control(
			'co_bf_input_field_focus_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password input[type="text"]:focus' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'co_bf_input_field_focus_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password input[type="text"]:focus',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'co_bf_box_border_hover',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password input[type="text"]:focus',
			]
		);		
		$this->add_responsive_control(
			'co_bf_border_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password input[type="text"]:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'co_bf_box_active_shadow',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password input[type="text"]:focus',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();	
		$this->add_control(
			'lp_reset_button',
			[
				'label' => esc_html__( 'Reset Password Button Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'lp_reset_btn_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
            'lp_reset_btn_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
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
					'{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password .button' => 'width: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'lp_reset_btn_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password .button',
				
			]
		);		
		$this->start_controls_tabs( 'lp_reset_btn_tabs' );
			$this->start_controls_tab(
				'lp_reset_btn_normal',
				[
					'label' => esc_html__( 'Normal', 'theplus' ),					
				]
			);
			$this->add_control(
				'lp_reset_btn_n_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password .button' => 'color: {{VALUE}}',
					],
					
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'lp_reset_btn_n_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password .button',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'lp_reset_btn_n_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password .button',
				]
			);
			$this->add_responsive_control(
				'lp_reset_btn_n_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'lp_reset_btn_shadow',
					'label' => esc_html__( 'Box Shadow', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password .button',
				]
			);	
			$this->end_controls_tab();
			$this->start_controls_tab(
				'lp_reset_btn_hover',
				[
					'label' => esc_html__( 'Hover', 'theplus' ),					
				]
			);
			$this->add_control(
				'clp_reset_btn_h_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password .button:hover' => 'color: {{VALUE}}',
					],
					
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'lp_reset_btn_h_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password .button:hover',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'lp_reset_btn_h_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password .button:hover',
				]
			);
			$this->add_responsive_control(
				'lp_reset_btn_h_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password .button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'lp_reset_btn_h_shadow',
					'label' => esc_html__( 'Box Shadow', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password .button:hover',
				]
			);
			$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*myaccount lost password form end*/
		
		/*myaccount register form start*/		
		$this->start_controls_section(
            'myaccount_register_form_styling',
            [
                'label' => esc_html__('Register Form', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
			'rlf_label_heading',
			[
				'label' => esc_html__( 'Label Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator'	=> 'before',
			]
		);
		$this->add_responsive_control(
			'rlf_label_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'rlf_label_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'rlf_label_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register label',
			]
		);
		$this->add_control(
			'rlf_label_color',
			[
				'label' => esc_html__( 'Label Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register label' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'rlf_label_req_symbol_color',
			[
				'label' => esc_html__( 'Required Symbol', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register label .required' => 'color: {{VALUE}} !important',
				],
			]
		);	
		$this->add_control(
			'rlf_input_heading',
			[
				'label' => esc_html__( 'Input Field Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'rlf_input_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input[type="text"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input[type="password"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'rlf_input_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input[type="text"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input[type="password"]' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'rlf_input_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input[type="text"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input[type="password"]',
			]
		);		
		$this->add_control(
			'rlf_input_placeholder_color',
			[
				'label'     => esc_html__( 'Placeholder Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input::-webkit-input-placeholder,
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register  password::-webkit-input-placeholder' => 'color: {{VALUE}};',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_rlf_input' );
		$this->start_controls_tab(
			'tab_rlf_input_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'rlf_input_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input[type="text"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input[type="password"]' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'rlf_input_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input[type="text"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input[type="password"]',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'rlf_input_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input[type="text"],
					{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input[type="password"]',
			]
		);		
		
		$this->add_responsive_control(
			'rlf_input_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input[type="text"],{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input[type="password"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'rlf_input_shadow',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input[type="text"],{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input[type="password"]',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_rlf_input_focus',
			[
				'label' => esc_html__( 'Focus', 'theplus' ),
			]
		);
		$this->add_control(
			'rlf_input_focus_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input[type="text"]:focus,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input[type="password"]:focus' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'rlf_input_focus_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input[type="text"]:focus,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input[type="password"]:focus',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'rlf_input_hover_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input[type="text"]:focus,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input[type="password"]:focus',
			]
		);		
		$this->add_responsive_control(
			'rlf_input_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input[type="text"]:focus,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input[type="password"]:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'rlf_input_hover_shadow',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input[type="text"]:focus,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register input[type="password"]:focus',
			]
		);		
		$this->end_controls_tab();
		$this->end_controls_tabs();		
		$this->add_control(
			'rlf_button_heading',
			[
				'label' => esc_html__( 'Register Button Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'rlf_button_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register .woocommerce-form-register__submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'rlf_button_width',
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
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register .woocommerce-form-register__submit' => 'width: {{SIZE}}{{UNIT}}',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'rlf_button_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register .woocommerce-form-register__submit',	
			]
		);
		$this->start_controls_tabs( 'rlf_button_tabs' );
			$this->start_controls_tab(
				'rlf_button_normal',
				[
					'label' => esc_html__( 'Normal', 'theplus' ),					
				]
			);
			$this->add_control(
				'rlf_button_n_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register .woocommerce-form-register__submit' => 'color: {{VALUE}}',
					],
					
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'rlf_button_n_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register .woocommerce-form-register__submit',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'rlf_button_n_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register .woocommerce-form-register__submit',
				]
			);
			$this->add_responsive_control(
				'rlf_button_n_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register .woocommerce-form-register__submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'rlf_button_n_shadow',
					'label' => esc_html__( 'Box Shadow', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register .woocommerce-form-register__submit',
				]
			);	
			$this->end_controls_tab();
			$this->start_controls_tab(
				'rlf_button_hover',
				[
					'label' => esc_html__( 'Hover', 'theplus' ),					
				]
			);
			$this->add_control(
				'rlf_button_h_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register .woocommerce-form-register__submit:hover' => 'color: {{VALUE}}',
					],
					
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'rlf_button_h_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register .woocommerce-form-register__submit:hover',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'rlf_button_h_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register .woocommerce-form-register__submit:hover',
				]
			);
			$this->add_responsive_control(
				'rlf_button_h_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register .woocommerce-form-register__submit:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'rlf_button_h_shadow',
					'label' => esc_html__( 'Box Shadow', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register .woocommerce-form-register__submit:hover',
				]
			);
			$this->end_controls_tab();
		$this->end_controls_tabs();		
		$this->end_controls_section();
		/*myaccount register form end*/
		
		/*form background start*/
		$this->start_controls_section(
            'form_box_styling',
            [
                'label' => esc_html__('Form Box', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );		
		$this->add_responsive_control(
			'form_box_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register,{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'form_box_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register,{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'form_box_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register,{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'form_box_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register,{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password',
			]
		);
		$this->add_responsive_control(
			'form_box_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register,{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'form_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .login,{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce .register,{{WRAPPER}} .tp-myaccount-page-wrapper .lost_reset_password',
			]
		);	
		$this->end_controls_section();
		/*form background end*/
		
		/*error message start*/
		$this->start_controls_section(
            'myaccount_error_message_styling',
            [
                'label' => esc_html__('Error Message', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_control(
			'em_icon',
			[
				'label' => esc_html__( 'Icon Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'em_icon_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-notices-wrapper .woocommerce-error:before',				
			]
		);
		$this->add_responsive_control(
            'em_icon_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
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
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-notices-wrapper .woocommerce-error:before' => 'font-size: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_control(
			'em_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-notices-wrapper .woocommerce-error:before' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
            'em_icon_offset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Right Offset', 'theplus'),
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
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
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-notices-wrapper .woocommerce-error li strong:first-of-type' => 'margin-left: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_control(
			'em_text',
			[
				'label' => esc_html__( 'Error Message Text Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'em_text_typography',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-notices-wrapper .woocommerce-error li',
			]
		);
		$this->add_control(
			'em_text_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-notices-wrapper .woocommerce-error li' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'em_link_color',
			[
				'label' => esc_html__( 'Link Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-notices-wrapper .woocommerce-error li a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'em_link_h_color',
			[
				'label' => esc_html__( 'Link Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-notices-wrapper .woocommerce-error li a:hover' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'em_content',
			[
				'label' => esc_html__( 'Box Content Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'em_content_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-notices-wrapper .woocommerce-error' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'em_content_bg',
				'types'     => [ 'classic', 'gradient' ],
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-notices-wrapper .woocommerce-error',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'em_content_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-notices-wrapper .woocommerce-error',
			]
		);
		$this->add_responsive_control(
			'em_content_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-notices-wrapper .woocommerce-error' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'em_content_shadow',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper .woocommerce-notices-wrapper .woocommerce-error',
			]
		);
		$this->end_controls_section();
		/*error message end*/
		
		/*myaccount box content start*/
		$this->start_controls_section(
            'myaccount_box_content_styling',
            [
                'label' => esc_html__('My Account Background', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_responsive_control(
			'ma_box_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'ma_box_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',					
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'ma_box_bg',
				'types'     => [ 'classic', 'gradient' ],
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .tp-myaccount-page-wrapper',
				]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ma_box_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper',
			]
		);
		$this->add_responsive_control(
			'ma_box_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-myaccount-page-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ma_box_shadow',
				'selector' => '{{WRAPPER}} .tp-myaccount-page-wrapper',
			]
		);
		$this->end_controls_section();
		/*myaccount box content end*/
		/*style end*/		
	}
	
	// private function get_shortcode() {
	// 	$settings = $this->get_settings();		
	// 	$this->add_render_attribute( 'shortcode', 'woocommerce_my_account' );
	// 	$shortcode   = [];
	// 	$shortcode[] = sprintf( '[%s]', $this->get_render_attribute_string( 'shortcode' ) );
	// 	return implode("", $shortcode);
	// }
	public function render() {
		$settings = $this->get_settings_for_display();
		if ( class_exists('woocommerce') ) {
		$select_ma_type= !empty($settings['select_ma_type']) ? $settings['select_ma_type'] : "type_shortcode";
		
		if(!empty($select_ma_type) && $select_ma_type=='type_shortcode'){
			
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() && ! empty( $settings['select_preview'] ) ) {
				global $wp;
				if($settings['select_preview']=='edit-address' && (!empty($settings['select_preview_mode']) && $settings['select_preview_mode']=='aform')){
					$wp->query_vars[ $settings['select_preview'] ] = 1;
				}else{
					$wp->query_vars[ $settings['select_preview'] ] = '';
				}				
			}
			
			$ma_layout= $settings['ma_layout'];
		
			$output ='<div class="tp-myaccount-page-wrapper '.$ma_layout.'">';
				//$output .= do_shortcode($this->get_shortcode());	
				$output .= do_shortcode('[woocommerce_my_account]');				
			$output .= '</div>';
			
			echo $output;
			/*login register preview start*/
				if ( \Elementor\Plugin::$instance->editor->is_edit_mode() &&  (isset( $settings['form_login_register'] ) && $settings['form_login_register']=='yes') ) {
					echo '<div class="tp-myaccount-page-wrapper '.$ma_layout.'">';
					echo '<div class="woocommerce">';
						include_once dirname( WC_PLUGIN_FILE ) . '/templates/myaccount/form-login.php';
					echo '</div>';
					echo '</div>';
				}
				/*login register preview end*/
		}else if(!empty($select_ma_type) && $select_ma_type=='type_individual'){
			echo "<div class='tp-myaccount-page-wrapper tp-myaccount-page-ind'>";
			echo "<div class='woocommerce-MyAccount-content'>";
			$loop_content=$settings["maSort"];
			if(!empty($loop_content)) {
				$index=0;
				$current_user=$user= wp_get_current_user();
				foreach($loop_content as $index => $item) {
					if(!empty($item['sortfield']) && $item['sortfield']=='Dashboard'){
						include_once dirname( WC_PLUGIN_FILE ) . '/templates/myaccount/dashboard.php';
					}
					
					if(!empty($item['sortfield']) && $item['sortfield']=='Orders'){
						$order_count = !empty($item['maxorder']) ? $item['maxorder'] : -1;
						if(is_wc_endpoint_url( 'view-order' ) && !\Elementor\Plugin::$instance->editor->is_edit_mode()){
							global $wp;							
							$order_id  = $wp->query_vars['view-order'];
							$order = wc_get_order( $order_id );
							$order_data = $order->get_data();

							if ( ! $order ) {
								return;
							}
							
							$order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
							$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
							$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
							$downloads             = $order->get_downloadable_items();
							$show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();
							
							if ( $show_downloads ) {
								wc_get_template(
									'order/order-downloads.php',
									array(
										'downloads'  => $downloads,
										'show_title' => true,
									)
								);
							}
							?>
							<section class="woocommerce-order-details">
								<?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>
							
								<h2 class="woocommerce-order-details__title"><?php esc_html_e( 'Order details', 'theplus' ); ?></h2>
							
								<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">
							
									<thead>
										<tr>
											<th class="woocommerce-table__product-name product-name"><?php esc_html_e( 'Product', 'theplus' ); ?></th>
											<th class="woocommerce-table__product-table product-total"><?php esc_html_e( 'Total', 'theplus' ); ?></th>
										</tr>
									</thead>
							
									<tbody>
										<?php
										do_action( 'woocommerce_order_details_before_order_table_items', $order );
							
										foreach ( $order_items as $item_id => $item ) {
											$product = $item->get_product();
							
											wc_get_template(
												'order/order-details-item.php',
												array(
													'order'              => $order,
													'item_id'            => $item_id,
													'item'               => $item,
													'show_purchase_note' => $show_purchase_note,
													'purchase_note'      => $product ? $product->get_purchase_note() : '',
													'product'            => $product,
												)
											);
										}
							
										do_action( 'woocommerce_order_details_after_order_table_items', $order );
										?>
									</tbody>
							
									<tfoot>
										<?php
										foreach ( $order->get_order_item_totals() as $key => $total ) {
											?>
												<tr>
													<th scope="row"><?php echo esc_html( $total['label'] ); ?></th>
													<td><?php echo ( 'payment_method' === $key ) ? esc_html( $total['value'] ) : wp_kses_post( $total['value'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
												</tr>
												<?php
										}
										?>
										<?php if ( $order->get_customer_note() ) : ?>
											<tr>
												<th><?php esc_html_e( 'Note:', 'theplus' ); ?></th>
												<td><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
											</tr>
										<?php endif; ?>
									</tfoot>
								</table>
							
								<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
							</section>
							
							<?php
							/**
							 * Action hook fired after the order details.
							 *
							 * @since 4.4.0
							 * @param WC_Order $order Order data.
							 */
							do_action( 'woocommerce_after_order_details', $order );
							
							if ( $show_customer_details ) {
								wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
							}

							
												
						}else{
							include_once dirname( WC_PLUGIN_FILE ) . '/templates/myaccount/my-orders.php';
							$customer_orders = get_posts( array(
								'numberposts' => 1,
								'meta_key'    => '_customer_user',
								'meta_value'  => get_current_user_id(),
								'post_type'   => 'shop_order',
								'post_status' => ['wc-pending','wc-processing','wc-on-hold','wc-completed','wc-cancelled','wc-refunded','wc-failed'],
								'fields'      => 'ids',
							) );
							if(count($customer_orders) === 0){
								$odrernotfound = !empty($item['odrernotfound']) ? $item['odrernotfound'] :'';
								if($odrernotfound){
									echo '<div class="tp-woo-myacc-order-notfound">'.esc_attr($odrernotfound).'</div>';
								}
							}
						}						
					}
					
					if(!empty($item['sortfield']) && $item['sortfield']=='Downloads'){
						include_once dirname( WC_PLUGIN_FILE ) . '/templates/myaccount/downloads.php';
					}
					
					if(!empty($item['sortfield']) && $item['sortfield']=='Addresses'){
						include_once dirname( WC_PLUGIN_FILE ) . '/templates/myaccount/my-address.php';
					}
					
					if(!empty($item['sortfield']) && $item['sortfield']=='Accountdetails'){
						include_once dirname( WC_PLUGIN_FILE ) . '/templates/myaccount/form-edit-account.php';
					}
					
					$index++;
				}   	  
			}
			
			echo '</div></div>';
		}
		}
	}	
}	