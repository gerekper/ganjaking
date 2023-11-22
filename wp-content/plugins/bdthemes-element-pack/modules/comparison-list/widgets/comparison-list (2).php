<?php

namespace ElementPack\Modules\ComparisonList\Widgets;

use Elementor\Repeater;
use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Text_Shadow;
use ElementPack\Utils;
use ElementPack\Element_Pack_Loader;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Comparison_List extends Module_Base {

	public function get_name() {
		return 'bdt-comparison-list';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Comparison List', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-comparison-list';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'comparison', 'list', 'compare', 'tabs', 'toggle' ];
	}

	public function get_style_depends() {
		if ( $this->ep_is_edit_mode() ) {
			return [ 'ep-styles' ];
		} else {
			return [ 'ep-comparison-list' ];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/7XvSgvbJM74';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_comparison_heading',
			[ 
				'label' => esc_html__( 'Comparison Heading', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'comparison_list_title',
			[ 
				'label'       => esc_html__( 'Feature Title', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Feature list', 'bdthemes-element-pack' ),
				'default'     => esc_html__( 'Feature list', 'bdthemes-element-pack' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'header_title',
			[ 
				'label'       => esc_html__( 'Title', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter your title', 'bdthemes-element-pack' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'header_sub_title',
			[ 
				'label'       => esc_html__( 'Additional Text', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Enter your text here', 'bdthemes-element-pack' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'header_button_text',
			[ 
				'label'       => esc_html__( 'Button Text', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter your button text', 'bdthemes-element-pack' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'header_link',
			[ 
				'label'       => esc_html__( 'Link', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'bdthemes-element-pack' ),
			]
		);

		$repeater->add_control(
			'header_active',
			[ 
				'label' => esc_html__( 'Active', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'comparison_header_list',
			[ 
				'label'       => esc_html__( 'Feature List', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [ 
					[ 
						'header_title'  => esc_html__( 'Free', 'bdthemes-element-pack' ),
						'header_active' => 'no',
					],
					[ 
						'header_title'  => esc_html__( 'Pro', 'bdthemes-element-pack' ),
						'header_active' => 'yes',
					],
				],
				'title_field' => '{{{ header_title }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_comparison_list',
			[ 
				'label' => esc_html__( 'Comparison List', 'bdthemes-element-pack' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			[ 
				'label'       => esc_html__( 'Title', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter your title', 'bdthemes-element-pack' ),
				'default'     => esc_html__( 'Title', 'bdthemes-element-pack' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'description',
			[ 
				'label'       => esc_html__( 'Description', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::WYSIWYG,
				'placeholder' => esc_html__( 'Type your description here', 'bdthemes-element-pack' ),
			]
		);

		$repeater->add_control(
			'feature_ability',
			[ 
				'label'       => esc_html__( 'Feature Ability', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( '0|1|0', 'bdthemes-element-pack' ),
				'label_block' => true,
				'description' => esc_html__( 'Separate with "|" pipe character. 0 for disable and 1 for enable.', 'bdthemes-element-pack' ),
				'default'     => '0|0',
			]
		);

		$this->add_control(
			'comparison_list',
			[ 
				'label'       => esc_html__( 'Comparison List', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [ 
					[ 
						'title'           => esc_html__( 'Feature Title #1', 'bdthemes-element-pack' ),
						'description'     => '#1 Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                                    labore et dolore magna aliqua.',
						'feature_ability' => '0|1',
					],
					[ 
						'title'           => esc_html__( 'Feature Title #2', 'bdthemes-element-pack' ),
						'description'     => '#2 Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                                    labore et dolore magna aliqua.',
						'feature_ability' => '0|1',
					],
					[ 
						'title'           => esc_html__( 'Feature Title #3', 'bdthemes-element-pack' ),
						'description'     => '#3 Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                                    labore et dolore magna aliqua.',
						'feature_ability' => '1|1',
					],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_style_comparison_list_header_wrap',
			[ 
				'label' => esc_html__( 'Header Wrap', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs(
			'style_tabs_comparison_list_header_wrap'
		);

		$this->start_controls_tab(
			'style_item_tab_comparison_list_header_wrap',
			[ 
				'label' => esc_html__( 'Item', 'textdomain' ),
			]
		);

		$this->add_control(
			'comparison_list_header_wrap_background',
			[ 
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-compatison-header' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'        => 'comparison_list_header_wrap_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-compatison-header',
			]
		);

		$this->add_responsive_control(
			'comparison_list_header_wrap_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-compatison-header' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'comparison_list_header_wrap_padding',
			[ 
				'label'     => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-head-title-item' => 'padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'comparison_list_header_wrap_shadow',
				'selector' => '{{WRAPPER}} .bdt-comparison-head-title-item',
			]
		);


		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_active_column_tab_comparison_list_header_wrap',
			[ 
				'label' => esc_html__( 'Active Column', 'textdomain' ),
			]
		);


		$this->add_control(
			'header_active_column_background',
			[ 
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-compatison-header .bdt-comparison-heightlight' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'        => 'header_active_column_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-compatison-header .bdt-comparison-heightlight',
			]
		);

		$this->add_responsive_control(
			'header_active_column_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-compatison-header .bdt-comparison-heightlight' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'header_active_column_shadow',
				'selector' => '{{WRAPPER}} .bdt-compatison-header .bdt-comparison-heightlight',
			]
		);


		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_comparison_list_Featured Title',
			[ 
				'label'     => esc_html__( 'Featured Title', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'comparison_list_title!' => '',
				],
			]
		);

		$this->add_control(
			'comparison_list_featured_title_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-head-title-item.bdt-comparison-head-feature-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[ 
				'name'     => 'comparison_list_featured_title_text_stroke',
				'selector' => '{{WRAPPER}} .bdt-comparison-head-title-item.bdt-comparison-head-feature-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[ 
				'name'     => 'comparison_list_featured_title_text_shadow',
				'selector' => '{{WRAPPER}} .bdt-comparison-head-title-item.bdt-comparison-head-feature-title',
			]
		);

		$this->add_responsive_control(
			'comparison_list_featured_title_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-comparison-head-title-item.bdt-comparison-head-feature-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'comparison_list_featured_title_typography',
				'selector' => '{{WRAPPER}} .bdt-comparison-head-title-item.bdt-comparison-head-feature-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_comparison_list_header_title',
			[ 
				'label' => esc_html__( 'Header Title', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// tabs
		$this->start_controls_tabs(
			'style_tabs_comparison_list_header_title'
		);

		$this->start_controls_tab(
			'style_normal_tab_comparison_list_header_title',
			[ 
				'label' => esc_html__( 'Normal', 'textdomain' ),
			]
		);

		$this->add_control(
			'comparison_list_header_title_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-head-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[ 
				'name'     => 'comparison_list_header_title_stroke',
				'selector' => '{{WRAPPER}} .bdt-comparison-head-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[ 
				'name'     => 'comparison_list_header_title_shadow',
				'selector' => '{{WRAPPER}} .bdt-comparison-head-title',
			]
		);

		$this->add_responsive_control(
			'comparison_list_header_title_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-comparison-head-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'comparison_list_header_title_typography',
				'selector' => '{{WRAPPER}} .bdt-comparison-head-title',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_active_tab_comparison_list_header_title',
			[ 
				'label' => esc_html__( 'Active', 'textdomain' ),
			]
		);


		$this->add_control(
			'comparison_list_header_title_active_color',
			[ 
				'label'     => esc_html__( 'Active Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-heightlight .bdt-comparison-head-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[ 
				'name'     => 'comparison_list_header_title_active_stroke',
				'selector' => '{{WRAPPER}} .bdt-comparison-heightlight .bdt-comparison-head-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[ 
				'name'     => 'comparison_list_header_title_active_shadow',
				'selector' => '{{WRAPPER}} .bdt-comparison-heightlight .bdt-comparison-head-title',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_comparison_list_header_text',
			[ 
				'label' => esc_html__( 'Header Text', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs(
			'style_tabs_comparison_list_header_text'
		);

		$this->start_controls_tab(
			'style_normal_tab_comparison_list_header_text',
			[ 
				'label' => esc_html__( 'Normal', 'textdomain' ),
			]
		);

		$this->add_control(
			'comparison_list_header_text_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-sub-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[ 
				'name'     => 'comparison_list_header_text_stroke',
				'selector' => '{{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-sub-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[ 
				'name'     => 'comparison_list_header_text_shadow',
				'selector' => '{{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-sub-title',
			]
		);

		$this->add_responsive_control(
			'comparison_list_header_text_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-sub-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'comparison_list_header_text_typography',
				'selector' => '{{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-sub-title',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_active_tab_comparison_list_header_text',
			[ 
				'label' => esc_html__( 'Active', 'textdomain' ),
			]
		);

		$this->add_control(
			'comparison_list_header_text_active_color',
			[ 
				'label'     => esc_html__( 'Active Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-heightlight .bdt-comparison-sub-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[ 
				'name'     => 'comparison_list_header_text_active_stroke',
				'selector' => '{{WRAPPER}} .bdt-comparison-heightlight .bdt-comparison-sub-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[ 
				'name'     => 'comparison_list_header_text_active_shadow',
				'selector' => '{{WRAPPER}} .bdt-comparison-heightlight .bdt-comparison-sub-title',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_comparison_list_header_button',
			[ 
				'label' => esc_html__( 'Header Button', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs(
			'style_tabs_comparison_list_header_button'
		);

		$this->start_controls_tab(
			'style_normal_tab_comparison_list_header_button',
			[ 
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'comparison_list_header_button_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-head-title-item .bdt-comparison-head-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_header_button_background',
			[ 
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-head-title-item .bdt-comparison-head-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'     => 'comparison_list_header_button_border',
				'selector' => '{{WRAPPER}} .bdt-comparison-head-title-item .bdt-comparison-head-button',
			]
		);

		$this->add_control(
			'comparison_list_header_button_border_radius',
			[ 
				'label'     => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-head-title-item .bdt-comparison-head-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'comparison_list_header_button_padding',
			[ 
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-comparison-head-title-item .bdt-comparison-head-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->add_responsive_control(
			'comparison_list_header_button_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-comparison-head-title-item .bdt-comparison-head-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'comparison_list_header_button_shadow',
				'selector' => '{{WRAPPER}} .bdt-comparison-head-title-item .bdt-comparison-head-button',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'comparison_list_header_button_typography',
				'selector' => '{{WRAPPER}} .bdt-comparison-head-title-item .bdt-comparison-head-button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_hover_tab_comparison_list_header_button',
			[ 
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'comparison_list_header_button_hover_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-head-title-item .bdt-comparison-head-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_header_button_hover_background',
			[ 
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-head-title-item .bdt-comparison-head-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_header_button_hover_border_color',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 
					'comparison_list_header_button_border_border!' => '',
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-head-title-item .bdt-comparison-head-button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'comparison_list_header_button_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-comparison-head-title-item .bdt-comparison-head-button:hover',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_active_tab_comparison_list_header_button',
			[ 
				'label' => esc_html__( 'Active', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'comparison_list_header_button_active_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-heightlight.bdt-comparison-head-title-item .bdt-comparison-head-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_header_button_active_background',
			[ 
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-heightlight.bdt-comparison-head-title-item .bdt-comparison-head-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_header_button_active_border_color',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 
					'comparison_list_header_button_border_border!' => '',
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-heightlight.bdt-comparison-head-title-item .bdt-comparison-head-button' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'comparison_list_header_button_active_shadow',
				'selector' => '{{WRAPPER}} .bdt-comparison-heightlight.bdt-comparison-head-title-item .bdt-comparison-head-button',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_comparison_list_item',
			[ 
				'label' => esc_html__( 'List item', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs(
			'comparison_list_item_tabs'
		);

		$this->start_controls_tab(
			'style_list_normal_item_tab',
			[ 
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'comparison_list_item_background',
			[ 
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-item' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_stripe_background',
			[ 
				'label'     => esc_html__( 'Stripe Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap li:nth-child(odd) .bdt-comparison-item' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_border',
			[ 
				'label'     => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [ 
					''       => esc_html__( 'Default', 'elementor' ),
					'none'   => esc_html__( 'None', 'elementor' ),
					'solid'  => esc_html_x( 'Solid', 'Border Control', 'elementor' ),
					'double' => esc_html_x( 'Double', 'Border Control', 'elementor' ),
					'dotted' => esc_html_x( 'Dotted', 'Border Control', 'elementor' ),
					'dashed' => esc_html_x( 'Dashed', 'Border Control', 'elementor' ),
					'groove' => esc_html_x( 'Groove', 'Border Control', 'elementor' ),
				],
				'default'   => '',
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-item' => 'border-top-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_border_color',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-item' => 'border-top-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'comparison_list_item_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'comparison_list_item_padding',
			[ 
				'label'     => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-item-title, {{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-icon' => 'padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'comparison_list_item_shadow',
				'selector' => '{{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-item',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_list_item_active_tab',
			[ 
				'label' => esc_html__( 'Active', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'comparison_list_item_active_background',
			[ 
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap li.bdt-open .bdt-comparison-item' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_active_border_color',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap li.bdt-open .bdt-comparison-item' => 'border-color: {{VALUE}};',
				],
				'condition' => [ 
					'comparison_list_item_border_border!' => '',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'comparison_list_item_active_shadow',
				'selector' => '{{WRAPPER}} .bdt-comparison-list-wrap li.bdt-open .bdt-comparison-item',
			]
		);

		$this->end_controls_tab();


		$this->start_controls_tab(
			'style_list_item_active_bg_tab',
			[ 
				'label' => esc_html__( 'Active Column', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'comparison_list_item_active_bg_background',
			[ 
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-icon.bdt-comparison-heightlight, {{WRAPPER}} .bdt-comparison-content-item.bdt-comparison-heightlight' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'     => 'comparison_list_item_active_bg_border',
				'selector' => '{{WRAPPER}} .bdt-comparison-icon.bdt-comparison-heightlight, {{WRAPPER}} .bdt-comparison-content-item.bdt-comparison-heightlight',
			]
		);



		$this->end_controls_tab();


		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_comparison_list_item_title',
			[ 
				'label' => esc_html__( 'List item Title', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs(
			'list_item_title_tabs'
		);

		$this->start_controls_tab(
			'list_item_title_normal_tab',
			[ 
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'item_item_title_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-item-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'item_item_title_hover_color',
			[ 
				'label'     => esc_html__( 'Hover Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-item-title:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_plus_icon_color',
			[ 
				'label'     => esc_html__( 'Icon Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-item-title .bdt-plus-icon::before, {{WRAPPER}} .bdt-comparison-item-title .bdt-plus-icon::after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'comparison_list_item_plus_icon_width',
			[ 
				'label'     => esc_html__( 'Icon Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min'  => 1,
						'max'  => 50,
						'step' => 1,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-item-title .bdt-plus-icon::before, {{WRAPPER}} .bdt-comparison-item-title .bdt-plus-icon::after' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		// icon spacing
		$this->add_responsive_control(
			'comparison_list_item_plus_icon_spacing',
			[ 
				'label'     => esc_html__( 'Icon Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-item-title' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'item_item_title_typography',
				'selector' => '{{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-item-title',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'list_item_title_active_tab',
			[ 
				'label' => esc_html__( 'Active', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'item_item_active_title_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap li.bdt-open .bdt-comparison-item-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_plus_icon_active_color',
			[ 
				'label'     => esc_html__( 'Icon Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-open .bdt-comparison-item-title .bdt-plus-icon::before, {{WRAPPER}} .bdt-open .bdt-comparison-item-title .bdt-plus-icon::after' => 'background-color: {{VALUE}};',
				],
			]
		);


		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// check icon style

		$this->start_controls_section(
			'section_style_comparison_list_item_check_icon',
			[ 
				'label' => esc_html__( 'Feature Ability', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs(
			'comparison_list_item_check_icon_tabs'
		);

		$this->start_controls_tab(
			'style_list_normal_item_check_icon_tab',
			[ 
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'comparison_list_item_check_icon_heading',
			[ 
				'label' => esc_html__( 'CHECKED ICON', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'comparison_list_item_check_icon_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-check-icon span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_check_icon_background',
			[ 
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-check-icon span' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_check_icon_border_color',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-check-icon span' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_close_icon_heading',
			[ 
				'label'     => esc_html__( 'CLOSE ICON', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'comparison_list_item_close_icon_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-close-icon span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_close_icon_background',
			[ 
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-close-icon span' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_close_icon_border_color',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-close-icon span' => 'border-color: {{VALUE}};',
				],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'comparison_list_item_features_text_heading',
			[ 
				'label' => esc_html__( 'TEXT', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::HEADING,
			]
		);


		$this->add_control(
			'comparison_list_item_features_text_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-item-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_features_text_background',
			[ 
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-item-text' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_features_text_border_color',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-item-text' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'comparison_list_item_features_text_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-item-text',
			]
		);

		// feature ability global style


		$this->add_responsive_control(
			'comparison_list_item_check_icon_size',
			[ 
				'label'     => esc_html__( 'Icon Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'        => 'comparison_list_item_check_icon_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'exclude'     => [ 'color' ],
				'selector'    => '{{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-icon span',

			]
		);

		$this->add_responsive_control(
			'comparison_list_item_check_icon_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-icon span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'comparison_list_item_check_icon_padding',
			[ 
				'label'     => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-icon span' => 'padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'comparison_list_item_check_icon_shadow',
				'selector' => '{{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-icon span',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_list_hover_item_check_icon_tab',
			[ 
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'comparison_list_item_check_icon_hover_heading',
			[ 
				'label'     => esc_html__( 'CHECK ICON', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'comparison_list_item_check_icon_hover_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-accordion-title:hover .bdt-comparison-icon.bdt-check-icon span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_check_icon_hover_background',
			[ 
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-accordion-title:hover .bdt-comparison-icon.bdt-check-icon span' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_check_icon_hover_border_color',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-accordion-title:hover .bdt-comparison-icon.bdt-check-icon span' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_close_icon_hover_heading',
			[ 
				'label'     => esc_html__( 'CLOSE ICON', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);


		$this->add_control(
			'comparison_list_item_close_icon_hover_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-accordion-title:hover .bdt-comparison-icon.bdt-close-icon span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_close_icon_hover_background',
			[ 
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-accordion-title:hover .bdt-comparison-icon.bdt-close-icon span' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_close_icon_hover_border_border',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-accordion-title:hover .bdt-comparison-icon.bdt-close-icon span' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_features_text_hover_heading',
			[ 
				'label'     => esc_html__( 'TEXT', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'comparison_list_item_features_text_hover_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-accordion-title:hover .bdt-comparison-item-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_features_text_hover_background',
			[ 
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-accordion-title:hover .bdt-comparison-item-text' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_features_text_hover_border_border',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-accordion-title:hover .bdt-comparison-item-text' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_list_item_check_icon_active_tab',
			[ 
				'label' => esc_html__( 'Active', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'comparison_list_item_check_icon_active_heading',
			[ 
				'label'     => esc_html__( 'CHECK ICON', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'comparison_list_item_check_icon_active_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap li.bdt-open .bdt-check-icon span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_check_icon_active_background',
			[ 
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap li.bdt-open .bdt-check-icon span' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_check_icon_active_border_color',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap li.bdt-open .bdt-check-icon span' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_close_icon_active_heading',
			[ 
				'label'     => esc_html__( 'CLOSE ICON', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'comparison_list_item_close_icon_active_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap li.bdt-open .bdt-close-icon span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_close_icon_active_background',
			[ 
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap li.bdt-open .bdt-close-icon span' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_close_icon_active_border_border',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap li.bdt-open .bdt-close-icon span' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_features_text_active_heading',
			[ 
				'label'     => esc_html__( 'TEXT', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'comparison_list_item_features_text_active_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap li.bdt-open .bdt-comparison-item-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_features_text_active_background',
			[ 
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap li.bdt-open .bdt-comparison-item-text' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_features_text_active_border_border',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap li.bdt-open .bdt-comparison-item-text' => 'border-color: {{VALUE}};',
				],
			]
		);


		$this->end_controls_tab();

		// active cloumn

		$this->start_controls_tab(
			'style_list_item_check_icon_active_column_tab',
			[ 
				'label' => esc_html__( 'Active Column', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'comparison_list_item_check_icon_active_column_heading',
			[ 
				'label'     => esc_html__( 'CHECK ICON', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'comparison_list_item_check_icon_active_column_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-icon.bdt-check-icon.bdt-comparison-heightlight span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_check_icon_active_column_background',
			[ 
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-icon.bdt-check-icon.bdt-comparison-heightlight span' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_check_icon_active_column_border_color',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-icon.bdt-check-icon.bdt-comparison-heightlight span' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_close_icon_active_column_heading',
			[ 
				'label'     => esc_html__( 'CLOSE ICON', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'comparison_list_item_close_icon_active_column_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-icon.bdt-close-icon.bdt-comparison-heightlight span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_close_icon_active_column_background',
			[ 
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-icon.bdt-close-icon.bdt-comparison-heightlight span' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_close_icon_active_column_border_border',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-icon.bdt-close-icon.bdt-comparison-heightlight span' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_features_text_active_column_heading',
			[ 
				'label'     => esc_html__( 'TEXT', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'comparison_list_item_features_text_active_column_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-icon.bdt-comparison-heightlight .bdt-comparison-item-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_features_text_active_column_background',
			[ 
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-icon.bdt-comparison-heightlight .bdt-comparison-item-text' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comparison_list_item_features_text_active_column_border_border',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-icon.bdt-comparison-heightlight .bdt-comparison-item-text' => 'border-color: {{VALUE}};',
				],
			]
		);


		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();



		// Content icon style

		$this->start_controls_section(
			'section_style_comparison_list_item_content',
			[ 
				'label' => esc_html__( 'Content', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'comparison_list_item_content_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-content-item' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'comparison_list_item_content_typography',
				'selector' => '{{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-content-item',
			]
		);

		$this->add_control(
			'comparison_list_item_content_background',
			[ 
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-accordion-content' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'comparison_list_item_content_padding',
			[ 
				'label'     => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-comparison-content-item:first-child' => 'padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'comparison_list_item_content_border_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-comparison-list-wrap .bdt-accordion-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}


	protected function render_check_icon( $class ) {
		?>
		<div class="bdt-comparison-icon bdt-check-icon <?php echo esc_attr( $class ); ?>">
			<span>
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check"
					viewBox="0 0 16 16">
					<path
						d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" />
				</svg>
			</span>
		</div>
		<?php
	}

	protected function render_close_icon( $class ) {
		?>
		<div class="bdt-comparison-icon bdt-close-icon <?php echo esc_attr( $class ); ?>">
			<span>
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x"
					viewBox="0 0 16 16">
					<path
						d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
				</svg>
			</span>
		</div>
		<?php
	}

	protected function render_text_icon( $class, $ability ) {
		?>
		<div class="bdt-comparison-icon <?php echo esc_attr( $class ); ?>">
			<span class="bdt-comparison-item-text">
				<?php echo esc_html( $ability ); ?>
			</span>
		</div>
		<?php
	}


	protected function content_columns( $class ) {
		?>
		<div class="bdt-comparison-content-item<?php echo ' ' . esc_attr( $class ); ?>"></div>
		<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$columns  = count( $settings['comparison_header_list'] );

		?>
		<div class="bdt-ep-comparison-list-container">
			<div class="bdt-comparison-list-wrap bdt-overflow-auto">

				<div
					class="bdt-compatison-header bdt-comparison-column <?php printf( 'bdt-comparison-column-%d', esc_attr( $columns ) ); ?>">
					<div class="bdt-comparison-head-title-item bdt-comparison-head-feature-title">
						<?php
						if ( isset( $settings['comparison_list_title'] ) ) {
							printf( '%s', esc_html( $settings['comparison_list_title'] ) );
						}
						?>
					</div>
					<?php
					foreach ( $settings['comparison_header_list'] as $index => $items ) :
						$class = ( 'yes' == $items['header_active'] ? 'bdt-comparison-heightlight' : '' );
						?>
						<div class="bdt-comparison-head-title-item <?php echo esc_attr( $class ); ?>">
							<?php
							// echo esc_html($items['header_title']);
							if ( ! empty( $items['header_title'] ) ) {
								printf( '<div class="bdt-comparison-head-title">%s</div>', esc_html( $items['header_title'] ) );
							}
							if ( ! empty( $items['header_sub_title'] ) ) {
								printf( '<div class="bdt-comparison-sub-title">%s</div>', esc_html( $items['header_sub_title'] ) );
							}
							if ( ! empty( $items['header_link']['url'] ) ) {
								$this->add_render_attribute( 'comparison_header_link' . $index, 'href', $items['header_link']['url'] );

								if ( $items['header_link']['is_external'] ) {
									$this->add_render_attribute( 'comparison_header_link' . $index, 'target', '_blank' );
								}

								if ( ! empty( $items['header_link']['nofollow'] ) ) {
									$this->add_render_attribute( 'comparison_header_link' . $index, 'rel', 'nofollow' );
								}

								if ( ! empty( $items['header_link']['custom_attributes'] ) ) {
									$this->add_render_attribute( 'comparison_header_link' . $index, $items['header_link']['custom_attributes'] );
								}
							}

							if ( ! empty( $items['header_link']['url'] ) ) {
								printf( '<a class="bdt-comparison-head-button" %s>%s</a>', $this->get_render_attribute_string( 'comparison_header_link' . $index ), esc_html( $items['header_button_text'] ) );
							}

							if ( empty( $items['header_link']['url'] ) && ! empty( $items['header_button_text'] ) ) {
								printf( '<a class="bdt-comparison-head-button" href="javascript:void(0);">%s</a>', esc_html( $items['header_button_text'] ) );
							}
							?>
						</div>
						<?php
					endforeach;
					?>
				</div>

				<ul class="bdt-comparison-item-list-wrap" bdt-accordion="collapsible: true">
					<?php

					foreach ( $settings['comparison_list'] as $items ) :

						?>
						<li>
							<div
								class="bdt-accordion-title bdt-comparison-item  bdt-comparison-column <?php printf( 'bdt-comparison-column-%d', esc_attr( $columns ) ); ?>">
								<div class="bdt-comparison-item-title">
									<span class="bdt-plus-icon"></span>
									<span>
										<?php printf( '%s', $items['title'] ); ?>
									</span>
								</div>
								<?php
								$feature_ability = explode( '|', $items['feature_ability'] );
								$key             = 0;
								foreach ( $feature_ability as $ability ) :

									$class = '';
									if ( isset( $settings['comparison_header_list'][ $key ]['header_active'] ) && 'yes' == $settings['comparison_header_list'][ $key ]['header_active'] ) {
										$class = 'bdt-comparison-heightlight';
									}

									$key++;

									switch ( $ability ) {
										case '0':
											$this->render_close_icon( $class );
											break;
										case '1':
											$this->render_check_icon( $class );
											break;
										default:
											$this->render_text_icon( $class, $ability );
											break;
									}
								endforeach;
								?>
							</div>
							<div class="bdt-accordion-content ">
								<div
									class="bdt-comparison-column <?php printf( 'bdt-comparison-column-%d', esc_attr( $columns ) ); ?>">
									<div class="bdt-comparison-content-item">
										<?php
										printf( '%s', wp_kses_post( $items['description'] ) );
										?>
									</div>

									<?php
									$key2 = 0;
									foreach ( $feature_ability as $ability ) :

										$class = '';
										if ( isset( $settings['comparison_header_list'][ $key2 ]['header_active'] ) && 'yes' == $settings['comparison_header_list'][ $key2 ]['header_active'] ) {
											$class = 'bdt-comparison-heightlight';
										}

										$key2++;

										$this->content_columns( $class );
									endforeach; ?>
								</div>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>

			</div>
		</div>
		<?php
	}
}
