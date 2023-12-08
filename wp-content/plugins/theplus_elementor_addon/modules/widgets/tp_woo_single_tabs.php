<?php 
/*
Widget Name: Woo Single Tabs
Description: Woo Single Tabs
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

class ThePlus_Woo_Single_Tabs extends Widget_Base {
		
	public function get_name() {
		return 'tp-woo-single-tabs';
	}

    public function get_title() {
        return esc_html__('Woo Single Tabs', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-toggle-off theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-woo-builder');
    }
	public function get_keywords() {
		return ['single tabs,woocomerce', 'post','product','description','additional information','review','tabs','woo tabs'];
	}
    protected function register_controls() {
		/*content start*/
		$this->start_controls_section(
			'section_woo_single_tabs',
			[
				'label' => esc_html__( 'Woo Single Tabs', 'theplus' ),
			]
		);
		$this->add_control(
			'select_tab_type',
			[
				'label' => esc_html__( 'Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'type_tabs',
				'options' => [
					'type_tabs'  => esc_html__( 'Tabs', 'theplus' ),
					'type_individual'  => esc_html__( 'Individual', 'theplus' ),					
				],
			]
		);
		$this->add_control(
			'select_tab_layout',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'layout-1',
				'options' => [
					'layout-1'  => esc_html__( 'Style 1', 'theplus' ),
					'layout-2'  => esc_html__( 'Style 2', 'theplus' ),
					'layout-3'  => esc_html__( 'Style 3', 'theplus' ),
					'layout-4'  => esc_html__( 'Style 4', 'theplus' ),
				],
				'condition'    => [					
					'select_tab_type' => 'type_tabs',
				],
			]
		);
		$this->add_control(
			'select_ind',
			[
				'label' => esc_html__( 'Individual', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'description',
				'options' => [
					'description'  => esc_html__( 'Description', 'theplus' ),
					'aditional_information'  => esc_html__( 'Additional Information', 'theplus' ),
					'review_form'  => esc_html__( 'Review Form', 'theplus' ),
				],
				'condition'    => [					
					'select_tab_type' => 'type_individual',
				],
			]
		);
		$this->add_control('showReviews',
            [
				'label' => esc_html__( 'Enable Reviews', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),
				'default' => '',
				'condition' => [					
					'select_ind' => 'review_form',
				],
			]
		);
		$this->end_controls_section();
		/*content end*/

		/*style start*/
		/*tab header style start*/
		$this->start_controls_section(
            'tab_head_styling',
            [
                'label' => esc_html__('Tab Heading', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'    => [					
					'select_tab_type' => 'type_tabs',
				],
            ]
        );
		$this->add_control(
			'tab_head_alignment',
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
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs ul.tabs' => 'text-align:{{VALUE}};',					
				],
				'condition'    => [					
					'select_tab_type' => 'type_tabs',
					'select_tab_layout' => 'layout-1',
				],
				'toggle' => true,
			]
		);
		$this->add_responsive_control(
			'tab_head_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs ul.tabs li a,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'tab_head_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs ul.tabs li a,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'tab_head_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs ul.tabs li a,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-label',				
			]
		);
		$this->start_controls_tabs( 'tabs_tab_head_style' );
		$this->start_controls_tab(
			'tab_tab_head_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'tab_head_color',
			[
				'label' => esc_html__( 'Tab Heading Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs ul.tabs li a,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-label' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'tab_head_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs ul.tabs li a,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-label',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tab_head_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs ul.tabs li a,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-label',
			]
		);
		$this->add_responsive_control(
			'tab_head_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs ul.tabs li a,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tab_head_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs ul.tabs li a,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-label',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_tab_head_active',
			[
				'label' => esc_html__( 'Active', 'theplus' ),				
			]
		);
		$this->add_control(
			'tab_head_color_a',
			[
				'label' => esc_html__( 'Tab Heading Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs ul.tabs li.active a,
					{{WRAPPER}} .tp-woo-single-tabs input:checked + .tp-tab-label' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'tab_head_bg_a',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs ul.tabs li.active a,
					{{WRAPPER}} .tp-woo-single-tabs input:checked + .tp-tab-label',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tab_head_border_a',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs ul.tabs li.active a,
					{{WRAPPER}} .tp-woo-single-tabs input:checked + .tp-tab-label',
			]
		);
		$this->add_responsive_control(
			'tab_head_br_a',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs ul.tabs li.active a,
					{{WRAPPER}} .tp-woo-single-tabs input:checked + .tp-tab-label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tab_head_shadow_a',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs ul.tabs li.active a,
					{{WRAPPER}} .tp-woo-single-tabs input:checked + .tp-tab-label',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*tab header style end*/
		
		/*tab panel style start*/
		$this->start_controls_section(
            'tab_panel_styling',
            [
                'label' => esc_html__('Tab Panel', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'    => [					
					'select_tab_type' => 'type_tabs',
				],
            ]
        );
		$this->add_responsive_control(
			'tab_panel_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs .panel,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tabs .tp-tab' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'tab_panel_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs .panel,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tabs .tp-tab' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'tab_panel_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs .panel,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tabs .tp-tab',
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tab_panel_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs .panel,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tabs .tp-tab',
			]
		);
		$this->add_responsive_control(
			'tab_panel_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs .panel,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tabs .tp-tab' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tab_panel_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs .panel,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tabs .tp-tab',
			]
		);
		$this->end_controls_section();
		/*tab panel style end*/
		
		/*tab description title style start*/
		$this->start_controls_section(
            'tab_dh_styling',
            [
                'label' => esc_html__('Description Heading', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'    => [					
					'select_tab_type' => 'type_tabs',
				],
            ]
        );
		$this->add_responsive_control(
			'tab_dh_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs .woocommerce-Tabs-panel--description h2,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab-label.tp-tab-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'tab_dh_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs .woocommerce-Tabs-panel--description h2,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab-label.tp-tab-desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'tab_dh_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs .woocommerce-Tabs-panel--description h2,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab-label.tp-tab-desc',
				
			]
		);
		$this->add_control(
			'tab_dh_color',
			[
				'label' => esc_html__( 'Tab Description Heading Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs .woocommerce-Tabs-panel--description h2,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab-label.tp-tab-desc' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'tab_dh_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs .woocommerce-Tabs-panel--description h2,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab-label.tp-tab-desc',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tab_dh_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs .woocommerce-Tabs-panel--description h2,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab-label.tp-tab-desc',
			]
		);
		$this->add_responsive_control(
			'tab_dh_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs .woocommerce-Tabs-panel--description h2,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab-label.tp-tab-desc' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tab_dh_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs .woocommerce-Tabs-panel--description h2,
				{{WRAPPER}} .tp-woo-single-tabs .tp-tab-label.tp-tab-desc',
			]
		);
		$this->end_controls_section();
		/*tab description title style end*/
		
		/*tab description style start*/
		$this->start_controls_section(
            'tab_dh_dec_styling',
            [
                'label' => esc_html__('Description', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_responsive_control(
			'tab_dh_dec_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs .woocommerce-Tabs-panel--description p,{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content p' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'tab_dh_dec_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs .woocommerce-Tabs-panel--description p,{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'tab_dh_dec_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs .woocommerce-Tabs-panel--description p,{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content p',
			]
		);
		$this->add_control(
			'tab_dh_dec_color',
			[
				'label' => esc_html__( 'Tab Description Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs .woocommerce-Tabs-panel--description p,{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content p' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'tab_dh_dec_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs .woocommerce-Tabs-panel--description p,{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content p',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tab_dh_dec_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs .woocommerce-Tabs-panel--description p,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content p',
			]
		);
		$this->add_responsive_control(
			'tab_dh_dec_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs .woocommerce-Tabs-panel--description p,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content p' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tab_dh_dec_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs .woocommerce-Tabs-panel--description p,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content p',
			]
		);
		$this->end_controls_section();
		/*tab panel style end*/
		
		/*Additional information style start*/
		$this->start_controls_section(
            'ai_styling',
            [
                'label' => esc_html__('Additional Information', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ai_heading_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs .woocommerce-Tabs-panel--additional_information h2,
				{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content.tp-tab-c-ai h2',
			]
		);
		$this->add_control(
			'ai_heading_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-tabs .woocommerce-Tabs-panel--additional_information h2,
				{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content.tp-tab-c-ai h2' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/*Additional information style end*/
		
		/*Additional information table style start*/
		$this->start_controls_section(
            'ait_styling',
            [
                'label' => esc_html__('Additional Information Table', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );		
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ait_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--additional_information table tbody,
				{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content table tbody',
			]
		);
		$this->add_responsive_control(
			'ait_border_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--additional_information table tbody,
				{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content table tbody' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'ait_heading',
			[
				'label' => esc_html__( 'Heading', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ait_heading_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--additional_information table tbody th,
				{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content table tbody th',
			]
		);
		$this->add_control(
			'ait_heading_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--additional_information table tbody th,
				{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content table tbody th' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'ait_desc',
			[
				'label' => esc_html__( 'Description', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ait_desc_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--additional_information table tbody td,
				{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content table tbody td',
			]
		);
		$this->add_control(
			'ait_desc_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--additional_information table tbody td,
				{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content table tbody td' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'ait_bg__color',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--additional_information table tbody>tr>td, {{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--additional_information table tbody>tr>th,
				{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content table tbody>tr>td,
				{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content table tbody>tr>th' => 'background-color: {{VALUE}}',
				],
				'separator' => 'before',
				
			]
		);
		$this->add_control(
			'ait_bg_odd_color',
			[
				'label' => esc_html__( 'Odd Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--additional_information table tbody>tr:nth-child(odd)>td, {{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--additional_information table tbody>tr:nth-child(odd)>th,
				{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content table tbody>tr:nth-child(odd)>td,
				{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content table tbody>tr:nth-child(odd)>th' => 'background-color: {{VALUE}}',
				],
				
			]
		);
		
		$this->end_controls_section();
		/*Additional information table style end*/
		
		/*review heading style start*/
		$this->start_controls_section(
            'r_heading_styling',
            [
                'label' => esc_html__('Review Heading', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'r_heading_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews .woocommerce-Reviews-title,
				{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content .woocommerce-Reviews-title',
			]
		);
		$this->add_control(
			'r_heading_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews .woocommerce-Reviews-title,
				{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content .woocommerce-Reviews-title' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/*review heading style end*/
		
		/*review image style start*/
		$this->start_controls_section(
            'r_image_styling',
            [
                'label' => esc_html__('Review Image', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
			'r_image_width',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Image Size', 'theplus'),
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
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #comments .commentlist .comment_container .avatar,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #comments .commentlist .comment_container .avatar' => 'width: {{SIZE}}{{UNIT}}',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'r_image_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #comments .commentlist .comment_container .avatar,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #comments .commentlist .comment_container .avatar',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'r_image_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #comments .commentlist .comment_container .avatar,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #comments .commentlist .comment_container .avatar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'r_image_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #comments .commentlist .comment_container .avatar,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #comments .commentlist .comment_container .avatar',
			]
		);
		$this->end_controls_section();
		/*review heading style end*/
		
		
		/*review table style start*/
		$this->start_controls_section(
            'r_table_styling',
            [
                'label' => esc_html__('Review Table', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
			'r_table_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #comments .commentlist .comment_container .comment-text,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #comments .commentlist .comment_container .comment-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'r_table_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #comments .commentlist .comment_container .comment-text,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #comments .commentlist .comment_container .comment-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'r_table_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #comments .commentlist .comment_container .comment-text,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #comments .commentlist .comment_container .comment-text',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'r_table_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #comments .commentlist .comment_container .comment-text,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #comments .commentlist .comment_container .comment-text',
			]
		);
		$this->add_responsive_control(
			'r_table_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #comments .commentlist .comment_container .comment-text,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #comments .commentlist .comment_container .comment-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_control(
			'r_table_meta_heading',
			[
				'label' => esc_html__( 'Meta', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'r_table_meta_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #comments .commentlist .comment_container .comment-text .meta,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #comments .commentlist .comment_container .comment-text .meta',
			]
		);
		$this->add_control(
			'r_table_meta_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #comments .commentlist .comment_container .comment-text .meta,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #comments .commentlist .comment_container .comment-text .meta' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'r_table_description_heading',
			[
				'label' => esc_html__( 'Description', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'r_table_description_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #comments .commentlist .comment_container .comment-text .description,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #comments .commentlist .comment_container .comment-text .description',
			]
		);
		$this->add_control(
			'r_table_description_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #comments .commentlist .comment_container .comment-text .description,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #comments .commentlist .comment_container .comment-text .description' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'r_table_rating_heading',
			[
				'label' => esc_html__( 'Rating', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'r_table_rating_fill_color',
			[
				'label' => esc_html__( 'Fill Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews .commentlist .star-rating span,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content .commentlist .star-rating span' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'r_table_rating_empty_color',
			[
				'label' => esc_html__( 'Empty Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews .commentlist .star-rating::before,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content .commentlist .star-rating::before' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/*review table style end*/
		
		/*review heading style start*/
		$this->start_controls_section(
            'r_form_styling',
            [
                'label' => esc_html__('Review Form', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
			'r_form_heading',
			[
				'label' => esc_html__( 'Review Heading', 'theplus' ),
				'type' => Controls_Manager::HEADING,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'r_form_heading_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-reply-title,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-reply-title',
			]
		);
		$this->add_control(
			'r_form_heading_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-reply-title,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-reply-title' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'r_form__label_heading',
			[
				'label' => esc_html__( 'Review Label', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'r_form_label_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-form label,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-form label',
			]
		);
		$this->add_control(
			'r_form_label_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-form label,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-form label' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'r_form_start_rating_heading',
			[
				'label' => esc_html__( 'Review Start Rating', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'r_form_start_rating_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-form .comment-form-rating .stars a,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-form .comment-form-rating .stars a' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'r_form_textarea_heading',
			[
				'label' => esc_html__( 'Text Field', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'r_form_ta_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-form .comment-form-comment textarea,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-form .comment-form-comment textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'r_form_ta_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-form .comment-form-comment textarea,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-form .comment-form-comment textarea' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'r_form_ta_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-form .comment-form-comment textarea,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-form .comment-form-comment textarea',
			]
		);
		$this->add_control(
				'r_form_ta_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-form .comment-form-comment textarea,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-form .comment-form-comment textarea' => 'color: {{VALUE}};',
					],
				]
			);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'r_form_ta_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-form .comment-form-comment textarea,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-form .comment-form-comment textarea',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'r_form_ta_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-form .comment-form-comment textarea,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-form .comment-form-comment textarea',
			]
		);
		$this->add_responsive_control(
			'r_form_ta_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-form .comment-form-comment textarea,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-form .comment-form-comment textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		
		$this->add_control(
			'r_form_button_heading',
			[
				'label' => esc_html__( 'Button', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'r_form_button_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-form .form-submit .submit,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-form .form-submit .submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'r_form_button_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-form .form-submit .submit,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-form .form-submit .submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'r_form_button_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-form .form-submit .submit,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-form .form-submit .submit',
			]
		);
		$this->start_controls_tabs( 'r_form_button_tabs' );
			$this->start_controls_tab(
				'r_form_button_normal',
				[
					'label' => esc_html__( 'Normal', 'theplus' ),					
				]
			);
			$this->add_control(
				'r_form_button_n_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-form .form-submit .submit,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-form .form-submit .submit' => 'color: {{VALUE}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'r_form_button_n_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-form .form-submit .submit,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-form .form-submit .submit',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'r_form_button_n_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-form .form-submit .submit,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-form .form-submit .submit',
				]
			);
			$this->add_responsive_control(
				'r_form_button_n_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-form .form-submit .submit,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-form .form-submit .submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
					],
				]
			);
			$this->end_controls_tab();
			$this->start_controls_tab(
				'r_form_button_hover',
				[
					'label' => esc_html__( 'Hover', 'theplus' ),					
				]
			);
			$this->add_control(
				'r_form_button_h_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-form .form-submit .submit:hover,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-form .form-submit .submit:hover' => 'color: {{VALUE}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'r_form_button_h_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-form .form-submit .submit:hover,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-form .form-submit .submit:hover',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'r_form_button_h_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-form .form-submit .submit:hover,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-form .form-submit .submit:hover',
				]
			);
			$this->add_responsive_control(
				'r_form_button_h_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-form .form-submit .submit:hover,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-form .form-submit .submit:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
					],
				]
			);
			$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*review heading style end*/
		
		/*review heading style start*/
		$this->start_controls_section(
            'r_form_box_styling',
            [
                'label' => esc_html__('Review Form Box Content', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
			'r_form_box_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-form,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'r_form_box_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-form,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-form' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'r_form_box_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-form,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-form',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'r_form_box_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-form,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-form',
			]
		);
		$this->add_responsive_control(
			'r_form_box_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-single-tabs .woocommerce-Tabs-panel--reviews #reviews #review_form_wrapper .comment-form,
					{{WRAPPER}} .tp-woo-single-tabs .tp-tab .tp-tab-content #reviews #review_form_wrapper .comment-form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->end_controls_section();
		/*review heading style end*/
		/*style end*/
	}
	
	
	public function render() {
		$settings = $this->get_settings_for_display();
		if ( class_exists('woocommerce') ) {
			$select_tab_type = $settings["select_tab_type"];
			$select_ind = $settings["select_ind"];
			$select_tab_layout = !empty($settings["select_tab_layout"]) ? $settings["select_tab_layout"] : "layout-1";
			$showReviews = !empty($settings["showReviews"]) ? 1 : 0;
			$uid=uniqid("tabs");
			
			$layout='';
			if(!empty($select_tab_type) && $select_tab_type=="type_tabs"){ 
				$layout=$select_tab_layout;
			}
			global $product;
			$product = wc_get_product();
			
			if ( empty( $product ) ) {
				return;
			}
			echo '<div class="tp-woo-single-tabs '.$layout.'">';
				/*tabs start*/		 
				if(!empty($select_tab_type) && $select_tab_type=="type_tabs"){ 							
					if(!empty($layout) && $layout=='layout-1'){
						woocommerce_output_product_data_tabs();	
					}else if(!empty($layout) && ($layout=='layout-2' || $layout=='layout-3' || $layout=='layout-4')){
						
						$type='';
						if($layout=='layout-2'){
							$type ='checkbox';
						}else if($layout=='layout-3' || $layout=='layout-4'){
							$type ='radio';
						}
						
					?>
						<div class="tp-tabs">
						  <div class="tp-tab">
							<input type="<?php echo $type; ?>" name="tp-tab-sections" id="<?php echo $uid; ?>chck1" class="tp-tabs-chkbx"  checked="checked">
							<label class="tp-tab-label tp-tab-desc" for="<?php echo $uid; ?>chck1">Description</label>
							<div class="tp-tab-content tp-tab-c-desc">
							 <?php the_content(); ?>
							</div>
						  </div>
						  <div class="tp-tab">
							<input type="<?php echo $type; ?>" name="tp-tab-sections" id="<?php echo $uid; ?>chck2" class="tp-tabs-chkbx">
							<label class="tp-tab-label tp-tab-ai" for="<?php echo $uid; ?>chck2">Additional information</label>
							<div class="tp-tab-content tp-tab-c-ai">
							  <?php woocommerce_product_additional_information_tab(); ?>
							</div>
						  </div>
						   <div class="tp-tab">
							<input type="<?php echo $type; ?>" name="tp-tab-sections" id="<?php echo $uid; ?>chck3" class="tp-tabs-chkbx">
							<?php $rating_count = $product->get_rating_count(); ?>
							<label class="tp-tab-label tp-tab-review" for="<?php echo $uid; ?>chck3">Reviews(<?php echo $rating_count; ?>)</label>
							<div class="tp-tab-content tp-tab-c-review">
							  <?php wc_get_template( 'single-product-reviews.php' ); ?>
							</div>
						  </div>
						</div>
					   <?php
					}
				}else if(!empty($select_tab_type) && $select_tab_type=="type_individual"){ 
					if(!empty($select_ind) && $select_ind=="description"){
						echo '<div class="woocommerce-tabs"><div class="woocommerce-Tabs-panel--description">';
							the_content();
						echo '</div></div>';
					}else if(!empty($select_ind) && $select_ind=="aditional_information"){					
						echo '<div class="woocommerce-tabs"><div class="woocommerce-Tabs-panel--additional_information">';
							woocommerce_product_additional_information_tab();
						echo '</div></div>';
					}else if(!empty($select_ind) && $select_ind=="review_form"){ 				
						echo '<div class="woocommerce-tabs"><div class="woocommerce-Tabs-panel--reviews">';
							if(!empty($showReviews)){
								comments_template();
							}else{
								wc_get_template( 'single-product-reviews.php' );
							}
							//do_action( 'woocommerce_after_single_product_summary' );
						echo '</div></div>';
					}
				}
				/*tabs end*/
			echo '</div>';
		}
	}
}