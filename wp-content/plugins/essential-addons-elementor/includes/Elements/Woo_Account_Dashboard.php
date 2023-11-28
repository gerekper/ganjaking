<?php

namespace Essential_Addons_Elementor\Pro\Elements;

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Repeater;
use \Elementor\Widget_Base;
use Essential_Addons_Elementor\Traits\Helper;

class Woo_Account_Dashboard extends Widget_Base {
	use Helper;
    
    /**
     * @var int
     */
    protected $page_id;

	public function get_name() {
		return 'eael-woo-account-dashboard';
	}

	public function get_title() {
		return esc_html__( 'Woo Account Dashboard', 'essential-addons-elementor' );
	}

	public function get_icon() {
		return 'eaicon-woo-account-dashboard';
	}

	public function get_categories() {
		return [ 'essential-addons-elementor' ];
	}

	public function get_style_depends() {
		return [
			'font-awesome-5-all',
			'font-awesome-4-shim',
			'ea-icon-frontend',
		];
	}

	public function get_keywords() {
		return [
			'woo account dashboard',
            'ea woo account dashboard',
            'woocommerce account dashboard',
            'ea woocommerce account dashboard',
            'ecommerce account dashboard',
            'woocommerce',
            'my account',
            'account',
            'woo',
            'account dashboard',
            'dashboard',
            'ecommerce',
            'ea',
            'essential addons',
		];
	}

	public function get_custom_help_url() {
		return 'https://essential-addons.com/elementor/docs/ea-woo-account-dashboard/';
	}

	protected function register_controls() {
		$this->init_content_wc_notice_controls();
        if ( !function_exists( 'WC' ) ) {
            return;
        }

		// Content Controls
        $this->eael_account_dashboard_layout();

        $this->eael_account_dashboard_tabs();

        // $this->eael_account_dashboard_content();

        $this->eael_account_dashboard_container_style();

       	$this->eael_account_dashboard_tabs_style();

        $this->eael_account_dashboard_content_style();

		$this->eael_account_dashboard_table_style();

		$this->eael_account_dashboard_form_style();

		$this->eael_account_dashboard_pages_style();
	}

	protected function init_content_wc_notice_controls() {
		if ( ! function_exists( 'WC' ) ) {
			$this->start_controls_section( 'eael_global_warning', [
				'label' => __( 'Warning!', 'essential-addons-elementor' ),
			] );
			$this->add_control( 'eael_global_warning_text', [
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( '<strong>WooCommerce</strong> is not installed/activated on your site. Please install and activate <a href="plugin-install.php?s=woocommerce&tab=search&type=term" target="_blank">WooCommerce</a> first.', 'essential-addons-elementor' ),
				'content_classes' => 'eael-warning',
			] );
			$this->end_controls_section();

			return;
		}
	}

	protected function eael_account_dashboard_layout() {

	    $this->start_controls_section(
		    'eael_section_account_dashboard_layouts',
		    [
			    'label' => esc_html__( 'Layout', 'essential-addons-elementor' ),
		    ]
	    );

	    $this->add_control(
		    'eael_dynamic_template_layout',
		    [
			    'label'   => esc_html__( 'Layout', 'essential-addons-elementor' ),
			    'type'    => Controls_Manager::SELECT,
			    'default' => 'preset-1',
			    'options' => $this->get_template_list_for_dropdown(true),
		    ]
	    );

        $this->end_controls_section();
    }
	
	protected function eael_account_dashboard_tabs() {

	    $this->start_controls_section(
			'eael_section_account_dashboard_tabs',
			[
				'label' => esc_html__( 'Tabs', 'essential-addons-elementor' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'eael_account_dashboard_tab_name',
			[
				'label' => esc_html__( 'Tab Name', 'essential-addons-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'ai'	=> [
					'active' => false,
				]
			]
		);

		$repeater->add_control(
			'eael_account_dashboard_order_display_note',
			[
				'raw' => esc_html__( 'Note: By default, only the last order is displayed while editing the orders section.', 'essential-addons-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
				'condition' => [
					'field_key' => 'orders',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_tabs',
			[
				'label' => '',
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'item_actions' => [
					'add' => false,
					'duplicate' => false,
					'remove' => false,
					'sort' => false,
				],
				'default' => [
					[
						'field_key' => 'dashboard',
						'field_label' => esc_html__( 'Dashboard', 'essential-addons-elementor' ),
						'eael_account_dashboard_tab_name' => esc_html__( 'Dashboard', 'essential-addons-elementor' ),
					],
					[
						'field_key' => 'orders',
						'field_label' => esc_html__( 'Orders', 'essential-addons-elementor' ),
						'eael_account_dashboard_tab_name' => esc_html__( 'Orders', 'essential-addons-elementor' ),
					],
					[
						'field_key' => 'downloads',
						'field_label' => esc_html__( 'Downloads', 'essential-addons-elementor' ),
						'eael_account_dashboard_tab_name' => esc_html__( 'Downloads', 'essential-addons-elementor' ),
					],
					[
						'field_key' => 'edit-address',
						'field_label' => esc_html__( 'Addresses', 'essential-addons-elementor' ),
						'eael_account_dashboard_tab_name' => esc_html__( 'Addresses', 'essential-addons-elementor' ),
					],
					[
						'field_key' => 'edit-account',
						'field_label' => esc_html__( 'Account Details', 'essential-addons-elementor' ),
						'eael_account_dashboard_tab_name' => esc_html__( 'Account Details', 'essential-addons-elementor' ),
					],
					[
						'field_key' => 'customer-logout',
						'field_label' => esc_html__( 'Logout', 'essential-addons-elementor' ),
						'eael_account_dashboard_tab_name' => esc_html__( 'Logout', 'essential-addons-elementor' ),
					],
				],
				'title_field' => '{{{ eael_account_dashboard_tab_name }}}',
			]
		);

		$this->add_control(
			'eael_account_dashboard_tabs_account_profile',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Account Profile', 'essential-addons-elementor' ),
				'separator' => 'before',
			]
		);
		
		$this->add_control(
            'eael_account_dashboard_tabs_account_profile_avatar_show',
            [
                'label'        => esc_html__('Avatar', 'essential-addons-elementor'),
                'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('Show', 'essential-addons-elementor'),
                'label_off'    => __('Hide', 'essential-addons-elementor'),
                'default'      => 'yes',
                'return_value' => 'yes',
				'condition' => [
					'eael_dynamic_template_layout!' => 'preset-1'
				]
            ]
        );

		$this->add_control(
            'eael_account_dashboard_tabs_account_profile_greeting_show',
            [
                'label'        => esc_html__('Greeting', 'essential-addons-elementor'),
                'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('Show', 'essential-addons-elementor'),
                'label_off'    => __('Hide', 'essential-addons-elementor'),
                'default'      => 'yes',
                'return_value' => 'yes',
				'condition' => [
					'eael_dynamic_template_layout!' => 'preset-1'
				]
            ]
        );

		$this->add_control(
            'eael_account_dashboard_tabs_account_profile_name_show',
            [
                'label'        => esc_html__('Name', 'essential-addons-elementor'),
                'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('Show', 'essential-addons-elementor'),
                'label_off'    => __('Hide', 'essential-addons-elementor'),
                'default'      => 'yes',
                'return_value' => 'yes',
				'condition' => [
					'eael_dynamic_template_layout!' => 'preset-1'
				]
            ]
        );

		$this->add_control(
            'eael_account_dashboard_tabs_account_profile_avatar_show_preset_1',
            [
                'label'        => esc_html__('Avatar', 'essential-addons-elementor'),
                'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('Show', 'essential-addons-elementor'),
                'label_off'    => __('Hide', 'essential-addons-elementor'),
                'default'      => '',
                'return_value' => 'yes',
				'condition' => [
					'eael_dynamic_template_layout' => 'preset-1'
				]
            ]
        );

		$this->add_control(
            'eael_account_dashboard_tabs_account_profile_greeting_show_preset_1',
            [
                'label'        => esc_html__('Greeting', 'essential-addons-elementor'),
                'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('Show', 'essential-addons-elementor'),
                'label_off'    => __('Hide', 'essential-addons-elementor'),
                'default'      => '',
                'return_value' => 'yes',
				'condition' => [
					'eael_dynamic_template_layout' => 'preset-1'
				]
            ]
        );

		$this->add_control(
            'eael_account_dashboard_tabs_account_profile_name_show_preset_1',
            [
                'label'        => esc_html__('Name', 'essential-addons-elementor'),
                'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('Show', 'essential-addons-elementor'),
                'label_off'    => __('Hide', 'essential-addons-elementor'),
                'default'      => '',
                'return_value' => 'yes',
				'condition' => [
					'eael_dynamic_template_layout' => 'preset-1'
				]
            ]
        );

		$this->add_control(
			'eael_account_dashboard_tabs_account_profile_greeting_text',
			[
				'label'     => esc_html__( 'Greeting Text', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar .eael-account-profile .eael-account-profile-details p' => 'color: {{VALUE}};',
				],
                'default'   => __('Hello', 'essential-addons-elementor'),
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->end_controls_section();
    }
	
	protected function eael_account_dashboard_container_style() {

	    $this->start_controls_section(
			'eael_section_account_dashboard_container_style',
			[
				'label' => esc_html__( 'Container', 'essential-addons-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'eael_account_dashboard_container_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_account_dashboard_container_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_container_border_radius',
			[
				'label'     => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
					],
				],
				'default'   => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-container' => 'border-radius: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_container_normal_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'	=> '#F6F7FF',
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-container' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'eael_account_dashboard_container_normal_border',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-container',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'eael_account_dashboard_container_normal_box_shadow',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-container',
			]
		);

		$this->end_controls_section();
    }
	
	protected function eael_account_dashboard_tabs_style() {
		$this->start_controls_section(
			'eael_section_account_dashboard_tabs_style',
			[
				'label' => esc_html__( 'Tabs', 'essential-addons-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'eael_account_dashboard_tabs_container',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Tabs Container', 'essential-addons-elementor' ),
			]
		);

		$this->add_responsive_control(
			'eael_account_dashboard_tabs_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'size' => 15 ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper:not(.preset-1) .eael-account-dashboard-navbar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'eael_dynamic_template_layout!' => 'preset-1'
				]
			]
		);
		
		$this->add_responsive_control(
			'eael_account_dashboard_tabs_padding_preset_1',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'size' => 15 ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper.preset-1 .eael-account-dashboard-navbar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'eael_dynamic_template_layout' => 'preset-1'
				]
			]
		);

		$this->add_control(
			'eael_account_dashboard_tabs_border_radius',
			[
				'label'     => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar' => 'border-radius: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_tabs_normal_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper:not(.preset-1) .eael-account-dashboard-navbar' => 'background: {{VALUE}};',
				],
				'condition' => [
					'eael_dynamic_template_layout!' => 'preset-1'
				]
			]
		);

		$this->add_control(
			'eael_account_dashboard_tabs_normal_background_color_preset_1',
			[
				'label'     => esc_html__( 'Background Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper.preset-1 .eael-account-dashboard-navbar' => 'background: {{VALUE}};',
				],
				'condition' => [
					'eael_dynamic_template_layout' => 'preset-1'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'eael_account_dashboard_tabs_normal_border',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'eael_account_dashboard_tabs_normal_box_shadow',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar',
			]
		);

		$this->end_controls_tab();

		$this->add_control(
			'eael_account_dashboard_tab_item',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Tab Item', 'essential-addons-elementor' ),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_account_dashboard_tab_item_typography',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar ul li a',
			]
		);

		$this->add_responsive_control(
			'eael_account_dashboard_tab_item_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper:not(.preset-1) .eael-account-dashboard-navbar ul li a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'eael_dynamic_template_layout!' => 'preset-1'
				]
			]
		);
		
		$this->add_responsive_control(
			'eael_account_dashboard_tab_item_margin_preset_1',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper.preset-1 .eael-account-dashboard-navbar ul li a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'eael_dynamic_template_layout' => 'preset-1'
				]
			]
		);

		$this->add_responsive_control(
			'eael_account_dashboard_tab_item_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'size' => 15 ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper:not(.preset-1) .eael-account-dashboard-navbar ul li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'eael_dynamic_template_layout!' => 'preset-1'
				]
			]
		);
		
		$this->add_responsive_control(
			'eael_account_dashboard_tab_item_padding_preset_1',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'size' => 15 ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper.preset-1 .eael-account-dashboard-navbar ul li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'eael_dynamic_template_layout' => 'preset-1'
				]
			]
		);

		$this->add_control(
			'eael_account_dashboard_tab_item_border_radius',
			[
				'label'     => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar ul li' => 'border-radius: {{SIZE}}px;',
				],
			]
		);

		$this->start_controls_tabs( 'eael_account_dashboard_tab_item_controls_tabs' );

		$this->start_controls_tab( 'eael_account_dashboard_tab_item_control_normal', [
			'label' => esc_html__( 'Normal', 'essential-addons-elementor' ),
		] );

		$this->add_control(
			'eael_account_dashboard_tab_item_normal_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar ul li a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_tab_item_normal_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar ul li' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'eael_account_dashboard_tab_item_normal_border',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar ul li',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'eael_account_dashboard_tab_item_normal_box_shadow',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar ul li',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'eael_account_dashboard_tab_item_control_hover', [
			'label' => esc_html__( 'Hover', 'essential-addons-elementor' ),
		] );

		$this->add_control(
			'eael_account_dashboard_tab_item_hover_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar ul li a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar ul li.is-active a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_tab_item_hover_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar ul li:hover' => 'background: {{VALUE}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar ul li.is-active:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'eael_account_dashboard_tab_item_hover_border',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar ul li:hover, {{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar ul li.is-active:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'eael_account_dashboard_tab_item_hover_box_shadow',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar ul li:hover, {{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar ul li.is-active:hover',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'eael_account_dashboard_tab_item_control_active', [
			'label' => esc_html__( 'Active', 'essential-addons-elementor' ),
		] );

		$this->add_control(
			'eael_account_dashboard_tab_item_active_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar ul li.is-active a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_tab_item_active_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar ul li.is-active' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'eael_account_dashboard_tab_item_active_border',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar ul li.is-active',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'eael_account_dashboard_tab_item_active_box_shadow',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar ul li.is-active',
			]
		);

		$this->add_control(
			'eael_account_dashboard_tab_item_active_highlight',
			[
				'label'     => esc_html__( 'Highlight Line', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper.preset-2 .eael-account-dashboard-navbar ul li.is-active a:before' => 'border-left: 2px solid {{VALUE}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper.preset-3 .eael-account-dashboard-navbar ul li.is-active a:after' => 'border-left: 4px solid {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'eael_account_dashboard_tab_icon',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Tab Icon', 'essential-addons-elementor' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'eael_account_dashboard_tab_icon_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper.preset-1 .eael-account-dashboard-navbar .woocommerce-MyAccount-navigation ul li a:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper.preset-3 .eael-account-dashboard-navbar .woocommerce-MyAccount-navigation ul li a:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
            'eael_account_dashboard_tab_icon_size',
            [
                'label'      => __('Size', 'essential-addons-elementor'),
                'type'       => Controls_Manager::SLIDER,
                'default'    => [
                    'size' => 20,
                    'unit' => 'px',
                ],
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 100,
                        'step' => 1,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .eael-account-dashboard-wrapper.preset-1 .eael-account-dashboard-navbar .woocommerce-MyAccount-navigation ul li a:before'   => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eael-account-dashboard-wrapper.preset-3 .eael-account-dashboard-navbar .woocommerce-MyAccount-navigation ul li a:before'   => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

		$this->add_control(
			'eael_account_dashboard_account_profile',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Account Profile', 'essential-addons-elementor' ),
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
            'eael_account_dashboard_account_profile_size',
            [
                'label'      => __('Avatar Size', 'essential-addons-elementor'),
                'type'       => Controls_Manager::SLIDER,
                'default'    => [
                    'size' => 39,
                    'unit' => 'px',
                ],
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 100,
                        'step' => 1,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar .eael-account-profile .eael-account-profile-image'   => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
				'condition' => [
					'eael_account_dashboard_tabs_account_profile_avatar_show' => 'yes'
				]
            ]
        );

		$this->start_controls_tabs( 'eael_account_dashboard_account_profile_controls_tabs' );

		$this->start_controls_tab( 'eael_account_dashboard_account_profile_control_tab_name', [
			'label' => esc_html__( 'Name', 'essential-addons-elementor' ),
			'condition' => [
				'eael_account_dashboard_tabs_account_profile_name_show' => 'yes'
			]
		] );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_account_dashboard_account_profile_name_typography',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar .eael-account-profile .eael-account-profile-details h5',
				'condition' => [
					'eael_account_dashboard_tabs_account_profile_name_show' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_account_dashboard_account_profile_name_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar .eael-account-profile .eael-account-profile-details h5' => 'color: {{VALUE}};',
				],
				'condition' => [
					'eael_account_dashboard_tabs_account_profile_name_show' => 'yes'
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'eael_account_dashboard_account_profile_control_tab_greeting', [
			'label' => esc_html__( 'Greeting', 'essential-addons-elementor' ),
			'condition' => [
				'eael_account_dashboard_tabs_account_profile_greeting_show' => 'yes'
			]
		] );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_account_dashboard_account_profile_greeting_typography',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar .eael-account-profile .eael-account-profile-details p',
				'condition' => [
					'eael_account_dashboard_tabs_account_profile_greeting_show' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_account_dashboard_account_profile_greeting_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-navbar .eael-account-profile .eael-account-profile-details p' => 'color: {{VALUE}};',
				],
				'condition' => [
					'eael_account_dashboard_tabs_account_profile_greeting_show' => 'yes'
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

        $this->end_controls_section();
    }
	
	protected function eael_account_dashboard_content_style() {

	    $this->start_controls_section(
			'eael_section_account_dashboard_content_style',
			[
				'label' => esc_html__( 'Content', 'essential-addons-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'eael_account_dashboard_content_container',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Content Container', 'essential-addons-elementor' ),
			]
		);

		$this->add_responsive_control(
			'eael_account_dashboard_content_container_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_account_dashboard_content_container_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'size' => 15 ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_content_container_border_radius',
			[
				'label'     => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content' => 'border-radius: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_content_container_normal_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'	=> '#fff',
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'eael_account_dashboard_content_container_normal_border',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'eael_account_dashboard_content_container_normal_box_shadow',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content',
			]
		);

		$this->add_control(
			'eael_account_dashboard_content',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Content', 'essential-addons-elementor' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'eael_account_dashboard_content_general',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'General', 'essential-addons-elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_account_dashboard_content_general_typography',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content p, {{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tbody tr td, {{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tfoot tr th, {{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tfoot tr td, {{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-Addresses .woocommerce-Address address, {{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-customer-details address',
			]
		);

		$this->add_control(
			'eael_account_dashboard_content_general_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content p' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content p strong' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content p mark' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tbody tr td:not(.woocommerce-orders-table__cell-order-status)' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tfoot tr:not(:last-child) th' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tfoot tr:not(:last-child) td' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-Addresses .woocommerce-Address address' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-customer-details address' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_content_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Heading', 'essential-addons-elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_account_dashboard_content_heading_typography',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-order-downloads__title, {{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-order-details__title',
			]
		);

		$this->add_control(
			'eael_account_dashboard_content_heading_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'	=> '#1a1a21',
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-order-downloads__title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-order-details__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_account_dashboard_content_heading_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-order-downloads__title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-order-details__title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_account_dashboard_content_heading_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-order-downloads__title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-order-details__title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_content_paragraph',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Paragraph', 'essential-addons-elementor' ),
			]
		);

		$this->add_responsive_control(
			'eael_account_dashboard_content_paragraph_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_account_dashboard_content_paragraph_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content p' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_content_link',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Link', 'essential-addons-elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_account_dashboard_content_link_typography',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content p a',
			]
		);

		$this->start_controls_tabs( 'eael_account_dashboard_content_link_controls_tabs' );

		$this->start_controls_tab( 'eael_account_dashboard_content_link_control_normal', [
			'label' => esc_html__( 'Normal', 'essential-addons-elementor' ),
		] );

		$this->add_control(
			'eael_account_dashboard_content_link_color_normal',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'	=> '#787c8a',
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content p:not(.order-again) a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'eael_account_dashboard_content_link_control_hover', [
			'label' => esc_html__( 'Hover', 'essential-addons-elementor' ),
		] );

		$this->add_control(
			'eael_account_dashboard_content_link_color_hover',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content p a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'eael_account_dashboard_content_button',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Button', 'essential-addons-elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_account_dashboard_content_button_typography',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .order-again a',
			]
		);

		$this->add_responsive_control(
			'eael_account_dashboard_content_button_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .order-again a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_account_dashboard_content_button_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .order-again a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_content_button_border_radius',
			[
				'label'     => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .order-again a' => 'border-radius: {{SIZE}}px;',
				],
			]
		);

		$this->start_controls_tabs( 'eael_account_dashboard_content_button_controls_tabs' );

		$this->start_controls_tab( 'eael_account_dashboard_content_button_control_normal', [
			'label' => esc_html__( 'Normal', 'essential-addons-elementor' ),
		] );

		$this->add_control(
			'eael_account_dashboard_content_button_color_normal',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .order-again a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_content_button_background_normal',
			[
				'label'     => esc_html__( 'Background', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .order-again a' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'eael_account_dashboard_content_button_normal_border',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .order-again a',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'eael_account_dashboard_content_button_normal_box_shadow',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .order-again a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'eael_account_dashboard_content_button_control_hover', [
			'label' => esc_html__( 'Hover', 'essential-addons-elementor' ),
		] );

		$this->add_control(
			'eael_account_dashboard_content_button_color_hover',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .order-again a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_content_button_background_hover',
			[
				'label'     => esc_html__( 'Background', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .order-again a:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'eael_account_dashboard_content_button_hover_border',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .order-again a:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'eael_account_dashboard_content_button_hover_box_shadow',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .order-again a:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

        $this->end_controls_section();
    }
	
	protected function eael_account_dashboard_table_style() {

	    $this->start_controls_section(
			'eael_section_account_dashboard_table_style',
			[
				'label' => esc_html__( 'Table', 'essential-addons-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_general',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'General', 'essential-addons-elementor' ),
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_link',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Link', 'essential-addons-elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_account_dashboard_table_link_typography',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table td:not(.woocommerce-orders-table__cell-order-actions) a',
			]
		);

		$this->start_controls_tabs( 'eael_account_dashboard_table_link_controls_tabs' );

		$this->start_controls_tab( 'eael_account_dashboard_table_link_control_normal', [
			'label' => esc_html__( 'Normal', 'essential-addons-elementor' ),
		] );

		$this->add_control(
			'eael_account_dashboard_table_link_color_normal',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table td:not(.woocommerce-orders-table__cell-order-actions) a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'eael_account_dashboard_table_link_control_hover', [
			'label' => esc_html__( 'Hover', 'essential-addons-elementor' ),
		] );

		$this->add_control(
			'eael_account_dashboard_table_link_color_hover',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table td:not(.woocommerce-orders-table__cell-order-actions) a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'eael_account_dashboard_table_button',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Button', 'essential-addons-elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_account_dashboard_table_button_typography',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table td .woocommerce-button, {{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-MyAccount-downloads-file',
			]
		);

		$this->add_responsive_control(
			'eael_account_dashboard_table_button_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table td .woocommerce-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-MyAccount-downloads-file' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_account_dashboard_table_button_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table td .woocommerce-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-MyAccount-downloads-file' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_button_border_radius',
			[
				'label'     => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table td .woocommerce-button' => 'border-radius: {{SIZE}}px;',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-MyAccount-downloads-file' => 'border-radius: {{SIZE}}px;',
				],
			]
		);

		$this->start_controls_tabs( 'eael_account_dashboard_table_button_controls_tabs' );

		$this->start_controls_tab( 'eael_account_dashboard_table_button_control_normal', [
			'label' => esc_html__( 'Normal', 'essential-addons-elementor' ),
		] );

		$this->add_control(
			'eael_account_dashboard_table_button_color_normal',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table td .woocommerce-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table td .woocommerce-button.view:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-MyAccount-downloads-file' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-MyAccount-downloads-file:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_button_background_normal',
			[
				'label'     => esc_html__( 'Background', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table td .woocommerce-button' => 'background: {{VALUE}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-MyAccount-downloads-file' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'eael_account_dashboard_table_button_normal_border',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table td .woocommerce-button, {{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-MyAccount-downloads-file',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'eael_account_dashboard_table_button_normal_box_shadow',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table td .woocommerce-button, {{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-MyAccount-downloads-file',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'eael_account_dashboard_table_button_control_hover', [
			'label' => esc_html__( 'Hover', 'essential-addons-elementor' ),
		] );

		$this->add_control(
			'eael_account_dashboard_table_button_color_hover',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table td .woocommerce-button:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table td .woocommerce-button.view:hover:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-MyAccount-downloads-file:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-MyAccount-downloads-file:hover:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_button_background_hover',
			[
				'label'     => esc_html__( 'Background', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table td .woocommerce-button:hover' => 'background: {{VALUE}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-MyAccount-downloads-file:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'eael_account_dashboard_table_button_hover_border',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table td .woocommerce-button:hover, {{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-MyAccount-downloads-file:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'eael_account_dashboard_table_button_hover_box_shadow',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table td .woocommerce-button:hover, {{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-MyAccount-downloads-file:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'eael_account_dashboard_table_order_status',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Order Status', 'essential-addons-elementor' ),
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_status_completed_color',
			[
				'label'     => esc_html__( 'Completed', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'	=> '#00B05C',
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-orders-table__row--status-completed .woocommerce-orders-table__cell-order-status' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper.preset-1 .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-orders-table__row--status-completed .woocommerce-orders-table__cell-order-status:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper.preset-3 .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-orders-table__row--status-completed .woocommerce-orders-table__cell-order-status:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_status_on_hold_color',
			[
				'label'     => esc_html__( 'On Hold', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'	=> '#EE0000',
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-orders-table__row--status-on-hold .woocommerce-orders-table__cell-order-status' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_status_processing_color',
			[
				'label'     => esc_html__( 'Processing', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'	=> '#EEBA00',
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-orders-table__row--status-processing .woocommerce-orders-table__cell-order-status' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_header',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Table Header', 'essential-addons-elementor' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_header_column',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Column', 'essential-addons-elementor' ),
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_header_column_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'	=> '#1a1a21',
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table thead tr th' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_header_background',
			[
				'label'     => esc_html__( 'Background', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table thead tr th' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_account_dashboard_table_header_column_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table thead tr th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_header_column_border_radius',
			[
				'label'     => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table thead tr th:first-child' => 'border-top-left-radius: {{SIZE}}px; border-bottom-left-radius: {{SIZE}}px;',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table thead tr th:last-child' => 'border-top-right-radius: {{SIZE}}px; border-bottom-right-radius: {{SIZE}}px;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'eael_account_dashboard_table_header_column_border',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table thead tr th',
			]
		);

		$this->add_control(
            'eael_account_dashboard_table_header_column_alignment',
            [
                'label'                 => __( 'Alignment', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::CHOOSE,
                'default'               => 'left',
                'options'               => [
                    'left'          => [
                        'title'     => __( 'Left', 'essential-addons-elementor' ),
                        'icon'      => 'eicon-h-align-left',
                    ],
                    'center'        => [
                        'title'     => __( 'Center', 'essential-addons-elementor' ),
                        'icon'      => 'eicon-h-align-center',
                    ],
                    'right'         => [
                        'title'     => __( 'Right', 'essential-addons-elementor' ),
                        'icon'      => 'eicon-h-align-right',
                    ],
                ],
                'frontend_available'    => true,
				'selectors' 		=> [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table thead tr' => 'text-align: {{VALUE}};',
				],
            ]
        );
		
		$this->add_control(
			'eael_account_dashboard_table_header_first_column',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'First Column', 'essential-addons-elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'eael_account_dashboard_table_header_first_column_border',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table thead tr th:first-child',
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_header_last_column',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Last Column', 'essential-addons-elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'eael_account_dashboard_table_header_last_column_border',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table thead tr th:last-child',
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_body',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Table Body', 'essential-addons-elementor' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_body_column',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Column', 'essential-addons-elementor' ),
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_body_background',
			[
				'label'     => esc_html__( 'Background', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tbody tr td' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_account_dashboard_table_body_column_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tbody tr td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_body_column_border_radius',
			[
				'label'     => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tbody tr td:first-child' => 'border-top-left-radius: {{SIZE}}px; border-bottom-left-radius: {{SIZE}}px;',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tbody tr td:last-child' => 'border-top-right-radius: {{SIZE}}px; border-bottom-right-radius: {{SIZE}}px;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'eael_account_dashboard_table_body_column_border',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tbody tr td',
			]
		);

		$this->add_control(
            'eael_account_dashboard_table_body_column_alignment',
            [
                'label'                 => __( 'Alignment', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::CHOOSE,
                'default'               => 'left',
                'options'               => [
                    'left'          => [
                        'title'     => __( 'Left', 'essential-addons-elementor' ),
                        'icon'      => 'eicon-h-align-left',
                    ],
                    'center'        => [
                        'title'     => __( 'Center', 'essential-addons-elementor' ),
                        'icon'      => 'eicon-h-align-center',
                    ],
                    'right'         => [
                        'title'     => __( 'Right', 'essential-addons-elementor' ),
                        'icon'      => 'eicon-h-align-right',
                    ],
                ],
                'frontend_available'    => true,
				'selectors' 		=> [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tbody tr' => 'text-align: {{VALUE}};',
				],
            ]
        );
		
		$this->add_control(
			'eael_account_dashboard_table_body_first_column',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'First Column', 'essential-addons-elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'eael_account_dashboard_table_body_first_column_border',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tbody tr td:first-child',
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_body_last_column',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Last Column', 'essential-addons-elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'eael_account_dashboard_table_body_last_column_border',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tbody tr td:last-child',
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_footer',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Table Footer', 'essential-addons-elementor' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_footer_column',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Column', 'essential-addons-elementor' ),
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_footer_background',
			[
				'label'     => esc_html__( 'Background', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tfoot tr:not(:last-child) th' => 'background: {{VALUE}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tfoot tr:not(:last-child) td' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_account_dashboard_table_footer_column_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tfoot tr:not(:last-child) th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tfoot tr:not(:last-child) td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_footer_column_border_radius',
			[
				'label'     => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tfoot tr th:first-child' => 'border-top-left-radius: {{SIZE}}px; border-bottom-left-radius: {{SIZE}}px;',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tfoot tr td:last-child' => 'border-top-right-radius: {{SIZE}}px; border-bottom-right-radius: {{SIZE}}px;',
				],
			]
		);
		
		$this->add_control(
			'eael_account_dashboard_table_footer_first_column',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'First Column', 'essential-addons-elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'eael_account_dashboard_table_footer_first_column_border',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tfoot tr th:first-child',
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_footer_last_column',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Last Column', 'essential-addons-elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'eael_account_dashboard_table_footer_last_column_border',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tfoot tr td:last-child',
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_footer_last_row',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Last Row', 'essential-addons-elementor' ),
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_footer_last_row_background',
			[
				'label'     => esc_html__( 'Background', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tfoot tr:last-child th' => 'background: {{VALUE}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tfoot tr:last-child td' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_account_dashboard_table_footer_last_row_column_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tfoot tr:last-child th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tfoot tr:last-child td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_table_footer_last_row_border_radius',
			[
				'label'     => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tfoot tr:last-child th:first-child' => 'border-top-left-radius: {{SIZE}}px; border-bottom-left-radius: {{SIZE}}px;',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content table tfoot tr:last-child td:last-child' => 'border-top-right-radius: {{SIZE}}px; border-bottom-right-radius: {{SIZE}}px;',
				],
			]
		);

        $this->end_controls_section();
    }

	protected function eael_account_dashboard_form_style() {

	    $this->start_controls_section(
			'eael_section_account_dashboard_form_style',
			[
				'label' => esc_html__( 'Form', 'essential-addons-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'eael_account_dashboard_form_label',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Label', 'essential-addons-elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_account_dashboard_form_label_typography',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-EditAccountForm .woocommerce-form-row label',
			]
		);
		
		$this->add_control(
			'eael_account_dashboard_form_label_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-EditAccountForm .woocommerce-form-row label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_account_dashboard_form_label_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-EditAccountForm .woocommerce-form-row label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_account_dashboard_form_label_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-EditAccountForm .woocommerce-form-row label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_form_input',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Input', 'essential-addons-elementor' ),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_account_dashboard_form_input_typography',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-EditAccountForm .woocommerce-form-row input',
			]
		);
		
		$this->add_control(
			'eael_account_dashboard_form_input_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-EditAccountForm .woocommerce-form-row input' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_account_dashboard_form_input_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-EditAccountForm .woocommerce-form-row input' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_account_dashboard_form_input_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-EditAccountForm .woocommerce-form-row input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_form_button',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Button', 'essential-addons-elementor' ),
				'separator' => 'before',
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_account_dashboard_form_button_typography',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-EditAccountForm > p .woocommerce-Button',
			]
		);

		$this->add_responsive_control(
			'eael_account_dashboard_form_button_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-EditAccountForm > p .woocommerce-Button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_account_dashboard_form_button_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-EditAccountForm > p .woocommerce-Button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_form_button_border_radius',
			[
				'label'     => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-EditAccountForm > p .woocommerce-Button' => 'border-radius: {{SIZE}}px;',
				],
			]
		);

		$this->start_controls_tabs( 'eael_account_dashboard_form_button_controls_tabs' );

		$this->start_controls_tab( 'eael_account_dashboard_form_button_control_normal', [
			'label' => esc_html__( 'Normal', 'essential-addons-elementor' ),
		] );

		$this->add_control(
			'eael_account_dashboard_form_button_color_normal',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-EditAccountForm > p .woocommerce-Button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_form_button_background_normal',
			[
				'label'     => esc_html__( 'Background', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-EditAccountForm > p .woocommerce-Button' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'eael_account_dashboard_form_button_normal_border',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-EditAccountForm > p .woocommerce-Button',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'eael_account_dashboard_form_button_normal_box_shadow',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-EditAccountForm > p .woocommerce-Button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'eael_account_dashboard_form_button_control_hover', [
			'label' => esc_html__( 'Hover', 'essential-addons-elementor' ),
		] );

		$this->add_control(
			'eael_account_dashboard_form_button_color_hover',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-EditAccountForm > p .woocommerce-Button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_form_button_background_hover',
			[
				'label'     => esc_html__( 'Background', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-EditAccountForm > p .woocommerce-Button:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'eael_account_dashboard_form_button_hover_border',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-EditAccountForm > p .woocommerce-Button:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'eael_account_dashboard_form_button_hover_box_shadow',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-EditAccountForm > p .woocommerce-Button:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

        $this->end_controls_section();
    }

	protected function eael_account_dashboard_pages_style() {

	    $this->start_controls_section(
			'eael_section_account_dashboard_pages_style',
			[
				'label' => esc_html__( 'Pages', 'essential-addons-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'eael_account_dashboard_pages_addresses',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Addresses', 'essential-addons-elementor' ),
			]
		);

		$this->add_control(
			'eael_account_dashboard_pages_addresses_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Title', 'essential-addons-elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_account_dashboard_pages_addresses_title_typography',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-customer-details .woocommerce-column__title, {{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-Address-title h3',
			]
		);
		
		$this->add_control(
			'eael_account_dashboard_pages_addresses_title_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'	=> '#1a1a21',
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-customer-details .woocommerce-column__title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-Address-title h3' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_account_dashboard_pages_account_details',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Account Details', 'essential-addons-elementor' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'eael_account_dashboard_pages_account_details_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Title', 'essential-addons-elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_account_dashboard_pages_account_details_title_typography',
				'selector' => '{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-EditAccountForm fieldset legend',
			]
		);
		
		$this->add_control(
			'eael_account_dashboard_pages_account_details_title_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'	=> '#1a1a21',
				'selectors' => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-EditAccountForm fieldset legend' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_account_dashboard_pages_account_details_title_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-account-dashboard-wrapper .eael-account-dashboard-content .woocommerce-MyAccount-content .woocommerce-EditAccountForm fieldset' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_section();
    }

	private function get_current_endpoint() {
		global $wp_query;
		$current = '';

		$pages = $this->get_account_pages();

		foreach ( $pages as $page => $val ) {
			if ( isset( $wp_query->query[ $page ] ) ) {
				$current = $page;
				break;
			}
		}

		if ( '' === $current && isset( $wp_query->query_vars['page'] ) ) {
			$current = 'dashboard';
		}

		return $current;
	}

	private function get_account_pages() {
		$pages = [
			'dashboard' => '',
			'orders' => '',
			'downloads' => '',
			'edit-address' => '',
			'edit-account' => '',
		];

		$support_payment_methods = false;
		foreach ( WC()->payment_gateways->get_available_payment_gateways() as $gateway ) {
			if ( $gateway->supports( 'add_payment_method' ) || $gateway->supports( 'tokenization' ) ) {
				$support_payment_methods = true;
				break;
			}
		}

		if ( $support_payment_methods ) {
			$pages['payment-methods'] = '';
			$pages['add-payment-method'] = '';
		}

		$recent_order = wc_get_orders( [
			'limit' => 1,
			'orderby'  => 'date',
			'order'    => 'DESC',
		] );

		if ( ! empty( $recent_order ) ) {
			$pages['view-order'] = $recent_order[0]->get_id();
		}

		return $pages;
	}

	public function eael_account_dashboard_menu_items( $items, $endpoints ) {
		$settings = $this->get_settings_for_display();

		if ( ! empty( $settings['eael_account_dashboard_tabs'] ) ) {
			foreach ( $settings['eael_account_dashboard_tabs'] as $tab ) {
				if ( isset( $tab['eael_account_dashboard_tab_name'] ) && isset( $items[ $tab['field_key'] ] ) ) {
					$items[ $tab['field_key'] ] = $tab['eael_account_dashboard_tab_name'];
				}
			}
		}

		return $items;
	}

	protected function eael_wc_endpoint_title() {
		$action         = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
		$endpoint       = is_wc_endpoint_url() ? WC()->query->get_current_endpoint() : '';
		$endpoint_title = $endpoint ? WC()->query->get_endpoint_title( $endpoint, $action ) : __('My Account', 'essential-addons-elementor');

		$endpoint_title = is_user_logged_in() ? $endpoint_title : __('Login', 'essential-addons-elementor');
		return $endpoint_title;
	}

	protected function get_woo_account_dashboard_settings() {
		$settings 												= $this->get_settings_for_display();
		
		$woo_account_dashboard 									= [];
		$woo_account_dashboard['layout'] 						= ! empty( $settings['eael_dynamic_template_layout'] ) ? $settings['eael_dynamic_template_layout'] : 'preset-1';
		$woo_account_dashboard['header_alignment_class']		= ! empty( $settings['eael_account_dashboard_header_alignment'] ) ? $settings['eael_account_dashboard_header_alignment'] : '';
		$woo_account_dashboard['account_profile_avatar_show']	= ! empty( $settings['eael_account_dashboard_tabs_account_profile_avatar_show'] ) && 'yes' === $settings['eael_account_dashboard_tabs_account_profile_avatar_show'] ? 1 : 0;
		$woo_account_dashboard['account_profile_greeting_show']	= ! empty( $settings['eael_account_dashboard_tabs_account_profile_greeting_show'] ) && 'yes' === $settings['eael_account_dashboard_tabs_account_profile_greeting_show'] ? 1 : 0;
		$woo_account_dashboard['account_profile_name_show']		= ! empty( $settings['eael_account_dashboard_tabs_account_profile_name_show'] ) && 'yes' === $settings['eael_account_dashboard_tabs_account_profile_name_show'] ? 1 : 0;
		$woo_account_dashboard['account_profile_greeting_text']	= ! empty( $settings['eael_account_dashboard_tabs_account_profile_greeting_text'] ) ? $settings['eael_account_dashboard_tabs_account_profile_greeting_text'] : __('Hello', 'essential-addons-elementor');

		if( 'preset-1' === $woo_account_dashboard['layout'] ){
			$woo_account_dashboard['account_profile_avatar_show']	= ! empty( $settings['eael_account_dashboard_tabs_account_profile_avatar_show_preset_1'] ) && 'yes' === $settings['eael_account_dashboard_tabs_account_profile_avatar_show_preset_1'] ? 1 : 0;
			$woo_account_dashboard['account_profile_greeting_show']	= ! empty( $settings['eael_account_dashboard_tabs_account_profile_greeting_show_preset_1'] ) && 'yes' === $settings['eael_account_dashboard_tabs_account_profile_greeting_show_preset_1'] ? 1 : 0;
			$woo_account_dashboard['account_profile_name_show']		= ! empty( $settings['eael_account_dashboard_tabs_account_profile_name_show_preset_1'] ) && 'yes' === $settings['eael_account_dashboard_tabs_account_profile_name_show_preset_1'] ? 1 : 0;
		}

		return $woo_account_dashboard;
	}

	protected function get_account_dashboard_navbar($current_user){
		$show_hide_class = is_user_logged_in() ? '' : 'eael-d-none';
		$woo_account_dashboard = $this->get_woo_account_dashboard_settings();
		?>
		<div class="eael-account-dashboard-navbar <?php echo esc_attr( $show_hide_class ); ?>">
			<?php wc_get_template( 'myaccount/navigation.php' ); ?>

			<?php 
			if ( is_user_logged_in() ) :
				if ( ($current_user instanceof \WP_User) ) : ?> 
					<?php if( $woo_account_dashboard['account_profile_avatar_show'] || $woo_account_dashboard['account_profile_greeting_show'] || $woo_account_dashboard['account_profile_name_show'] ) : ?>
					<div class="eael-account-profile">
						<?php if( $woo_account_dashboard['account_profile_avatar_show'] ) : ?>
						<div class="eael-account-profile-image">
							<?php echo get_avatar( $current_user->ID, 100 ); ?>
						</div>
						<?php endif; ?>

						<div class="eael-account-profile-details">
							<?php if( $woo_account_dashboard['account_profile_greeting_show'] ): ?>
							<p class="info"><?php _e($woo_account_dashboard['account_profile_greeting_text'], 'essential-addons-elementor'); ?></p>
							<?php endif; ?>

							<?php if( $woo_account_dashboard['account_profile_name_show'] ) : ?>
							<h5 class="name"><?php echo esc_html($current_user->display_name); ?></h5>
							<?php endif; ?>
						</div>
					</div>
					<?php endif; ?>
				<?php 
				endif;
			endif;
			?>
		</div>
		<?php
	}

	protected function get_account_dashboard_content($current_user, $is_editor){
		?>
		<div class="eael-account-dashboard-content">
			<?php 
			if ( $is_editor ) { ?>
				<div class="woocommerce">
					<div class="woocommerce-MyAccount-content">
				<?php
				$pages = $this->get_account_pages();
			
				global $wp_query;
				foreach ( $pages as $page => $page_value ) {
					foreach ( $pages as $unset_tab => $unset_tab_value ) {
						unset( $wp_query->query_vars[ $unset_tab ] );
					}
					$wp_query->query_vars[ $page ] = $page_value;

					if ( 'dashboard' === $page ) {
						echo "<div class='tab-content tab-dashboard active'>";
						wc_get_template(
							'myaccount/dashboard.php',
							[
								'current_user' => $current_user,
							]
						);
						echo "</div>";
					} else {
						echo "<div class='tab-content tab-$page'>";
						if( 'downloads' === $page ) {
							if( ! empty( WC()->customer ) ) {
								do_action( 'woocommerce_account_' . $page . '_endpoint', $page_value );
							}
						} else {
							do_action( 'woocommerce_account_' . $page . '_endpoint', $page_value );
						}
						echo "</div>";
					}
				}
				?>
					</div>
				</div>
				<?php 
			} else {
				echo do_shortcode( '[woocommerce_my_account]' );
			}
			?>
		</div>
		<?php
	}

	protected function render() {
		add_filter( 'woocommerce_account_menu_items', [ $this, 'eael_account_dashboard_menu_items' ], 10, 2 );
		do_action( 'eael/woo-account-dashboard/before-content', $this );

		$show_hide_class = apply_filters( 'eael/woo-account-dashboard/is-user-logged-in', is_user_logged_in() ) ? '' : 'eael-d-none';
		$current_user = get_user_by( 'id', apply_filters( 'eael/woo-account-dashboard/get-current-user-id', get_current_user_id() ) );

		if ( !function_exists( 'WC' ) ) {
            return;
        }

        $settings 	= $this->get_settings_for_display();
		$is_editor 	= Plugin::instance()->editor->is_edit_mode();

		$woo_account_dashboard = $this->get_woo_account_dashboard_settings();
        ?>

        <div>
            <?php
			$template = $this->get_template( $woo_account_dashboard[ 'layout' ] );
			if ( file_exists( $template ) ):
				include( $template );
			else:
				_e( '<p class="eael-no-posts-found">No layout found!</p>', 'essential-addons-elementor' );
			endif; 
			?>
        </div>

		<?php
		remove_action( 'woocommerce_account_menu_items', [ $this, 'eael_account_dashboard_menu_items' ], 10 );
		do_action( 'eael/woo-account-dashboard/after-content', $this );
	}
}
