<?php 
/*
Widget Name: Posts Search
Description: Post Search Form Of Styles.
Author: Theplus
Author URI: https://posimyth.com
*/

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
	
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Post_Search extends Widget_Base {
		
	public function get_name() {
		return 'tp-post-search';
	}

    public function get_title() {
        return esc_html__('Posts Search', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-search theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-essential');
    }

    protected function register_controls() {
		/*Layout Content*/
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Layout', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
            'deprecated_notice_post_search',
            [
                'type' => \Elementor\Controls_Manager::DEPRECATED_NOTICE,
				'widget' => 'Posts Search',
				'since' => '5.0.4',
				'last' => '6.0.5',
				'plugin' => 'in',
				'replacement' => 'WP Search Bar',
            ]
        );
		$this->add_control(
			'form_style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => theplus_get_style_list(2),
			]
		);
		$this->add_responsive_control(
			'content_align',
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
				'default' => 'center',
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
			]
		);
		$this->end_controls_section();
		/*Layout Content*/
		/*Search Field*/
		$this->start_controls_section(
			'search_field_section',
			[
				'label' => esc_html__( 'Search Field', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'search_field_placeholder',
			[
				'label'       => esc_html__( 'Search Field Placeholder', 'theplus' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'default'     => esc_html__( 'Type your keyword to search...', 'theplus' ),
				'placeholder' => esc_html__( 'Type your keyword to search...', 'theplus' ),
			]
		);
		$this->add_control(
			'search_icon',
			[
				'label' => esc_html__( 'Icon Font', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'font_awesome',
				'options' => [
					'font_awesome'  => esc_html__( 'Font Awesome', 'theplus' ),
					'font_awesome_5'  => esc_html__( 'Font Awesome 5', 'theplus' ),
				],
				
			]
		);
		$this->add_control(
			'search_icon_fontawesome',
			[
				'label' => esc_html__( 'Search Icon Prefix', 'theplus' ),
				'type' => Controls_Manager::ICON,
				'default' => 'fa fa-search',
				'condition' => [
					'search_icon' => 'font_awesome',
				],
			]
		);
		$this->add_control(
			'search_icon_fontawesome_5',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-search',
					'library' => 'solid',
				],
				'condition' => [
					'search_icon' => 'font_awesome_5',
				],	
			]
		);
		$this->end_controls_section();
		/*Search Field*/
		/*search Button*/
		$this->start_controls_section(
			'search_button_section',
			[
				'label' => esc_html__( 'Search Button', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'button_text',
			[
				'label'       => esc_html__( 'Button Text', 'theplus' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => esc_html__( 'Search', 'theplus' ),
				'default'     => esc_html__( 'Search', 'theplus' ),
			]
		);
		$this->add_control(
			'button_icon_style',
			[
				'label' => esc_html__( 'Icon Font', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'  => esc_html__( 'None', 'theplus' ),
					'font_awesome'  => esc_html__( 'Font Awesome', 'theplus' ),
					'font_awesome_5'  => esc_html__( 'Font Awesome 5', 'theplus' ),
					'icon_mind' => esc_html__( 'Icons Mind', 'theplus' ),
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'button_icon_fontawesome',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICON,
				'default' => 'fa fa-search',
				'condition' => [
					'button_icon_style' => 'font_awesome',
				],
			]
		);
		$this->add_control(
			'button_icon_fontawesome_5',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-search',
					'library' => 'solid',
				],
				'condition' => [
					'button_icon_style' => 'font_awesome_5',
				],
			]
		);
		$this->add_control(
			'button_icons_mind',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'default' => '',
				'label_block' => true,
				'options' => theplus_icons_mind(),
				'condition' => [
					'button_icon_style' => 'icon_mind',
				],
			]
		);
		$this->add_control(
			'icon_align',
			[
				'label'   => esc_html__( 'Icon Position', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'left'   => esc_html__( 'Left', 'theplus' ),
					'right'  => esc_html__( 'Right', 'theplus' ),
				],
				'condition' => [
					'button_icon_style!' => 'none',
				],
			]
		);
		$this->add_control(
			'button_icon_indent',
			[
				'label' => esc_html__( 'Icon Spacing', 'theplus' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'default' => [
					'size' => 8,
				],
				'condition' => [
					'button_icon_style!' => 'none',
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-post-search-form .search-btn-icon.btn-after'  => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .theplus-post-search-form .search-btn-icon.btn-before'   => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'button_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'theplus' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'condition' => [
					'button_icon_style!' => 'none',
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-post-search-form .search-btn-icon'  => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .theplus-post-search-form .search-btn-icon svg'  => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};'
				],
			]
		);
		$this->end_controls_section();
		/*Search Button*/
		/*Prefix Icon*/
		$this->start_controls_section(
			'section_prefix_icon_input',
			[
				'label' => esc_html__( 'Prefix Search Icon', 'theplus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
            'prefix_icon_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 8,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .theplus-post-search-wrapper .plus-newsletter-input-wrapper span.prefix-icon' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .theplus-post-search-wrapper .plus-newsletter-input-wrapper span.prefix-icon svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_control(
			'prefix_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-post-search-wrapper .plus-newsletter-input-wrapper span.prefix-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .theplus-post-search-wrapper .plus-newsletter-input-wrapper span.prefix-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
            'prefix_icon_adjust',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Adjust', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -50,
						'max' => 50,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .theplus-post-search-wrapper .plus-newsletter-input-wrapper span.prefix-icon' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->end_controls_section();
		/*Prefix Icon*/
		/*Search Field Style*/
		$this->start_controls_section(
			'section_style_input',
			[
				'label' => esc_html__( 'Search Field', 'theplus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'search_typography',
				'selector' => '{{WRAPPER}} .theplus-post-search-form input.form-control',
			]
		);
		$this->add_control(
			'search_placeholder_color',
			[
				'label'     => esc_html__( 'Placeholder Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-post-search-form input.form-control::placeholder' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'search_inner_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .theplus-post-search-form input.form-control' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->start_controls_tabs( 'tabs_search_field_style' );
		$this->start_controls_tab(
			'tab_search_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'input_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-post-search-form input.form-control' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'search_field_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .theplus-post-search-form input.form-control',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_search_focus',
			[
				'label' => esc_html__( 'Focus', 'theplus' ),
			]
		);
		$this->add_control(
			'input_focus_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-post-search-form input.form-control:focus' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'search_field_focus_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .theplus-post-search-form input.form-control:focus',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'border_options',
			[
				'label' => esc_html__( 'Border Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'box_border',
			[
				'label' => esc_html__( 'Box Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
			]
		);
		
		$this->add_control(
			'border_style',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'selectors'  => [
					'{{WRAPPER}} .theplus-post-search-form input.form-control' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'box_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .theplus-post-search-form input.form-control' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_border_style' );
		$this->start_controls_tab(
			'tab_border_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'box_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .theplus-post-search-form input.form-control' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-post-search-form input.form-control' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_border_hover',
			[
				'label' => esc_html__( 'Focus', 'theplus' ),
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'box_border_hover_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors'  => [
					'{{WRAPPER}} .theplus-post-search-form input.form-control:focus' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'border_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-post-search-form input.form-control:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'shadow_options',
			[
				'label' => esc_html__( 'Box Shadow Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs( 'tabs_shadow_style' );
		$this->start_controls_tab(
			'tab_shadow_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .theplus-post-search-form input.form-control',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_shadow_hover',
			[
				'label' => esc_html__( 'Focus', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_active_shadow',
				'selector' => '{{WRAPPER}} .theplus-post-search-form input.form-control:focus',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Search Field Style*/
		$this->start_controls_section(
            'section_search_button_styling',
            [
                'label' => esc_html__('Search Button', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .theplus-post-search-wrapper button.search-btn-submit',
			]
		);
		$this->add_responsive_control(
			'button_inner_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .theplus-post-search-wrapper button.search-btn-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'button_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .theplus-post-search-wrapper button.search-btn-submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->start_controls_tabs( 'tabs_button_style' );
		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'button_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-post-search-wrapper button.search-btn-submit' => 'color: {{VALUE}};',
					'{{WRAPPER}} .theplus-post-search-wrapper button.search-btn-submit svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .theplus-post-search-wrapper button.search-btn-submit',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'button_hover_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-post-search-wrapper button.search-btn-submit:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .theplus-post-search-wrapper button.search-btn-submit:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_hover_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .theplus-post-search-wrapper button.search-btn-submit:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'button_border_options',
			[
				'label' => esc_html__( 'Border Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'button_box_border',
			[
				'label' => esc_html__( 'Box Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
			]
		);
		
		$this->add_control(
			'button_border_style',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'selectors'  => [
					'{{WRAPPER}} .theplus-post-search-wrapper button.search-btn-submit' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'button_box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'button_box_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .theplus-post-search-wrapper button.search-btn-submit' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'button_box_border' => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_button_border_style' );
		$this->start_controls_tab(
			'tab_button_border_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'button_box_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'button_box_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .theplus-post-search-wrapper button.search-btn-submit' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'button_box_border' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-post-search-wrapper button.search-btn-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'button_box_border' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_button_border_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'button_box_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'button_box_border_hover_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors'  => [
					'{{WRAPPER}} .theplus-post-search-wrapper button.search-btn-submit:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'button_box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'button_border_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-post-search-wrapper button.search-btn-submit:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'button_box_border' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'button_shadow_options',
			[
				'label' => esc_html__( 'Box Shadow Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs( 'tabs_button_shadow_style' );
		$this->start_controls_tab(
			'tab_button_shadow_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_shadow',
				'selector' => '{{WRAPPER}} .theplus-post-search-wrapper button.search-btn-submit',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_button_shadow_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_hover_shadow',
				'selector' => '{{WRAPPER}} .theplus-post-search-wrapper button.search-btn-submit:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		
		$this->start_controls_section(
            'section_responsive_styling',
            [
                'label' => esc_html__('Responsive', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
            'content_max_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Maximum Width', 'theplus'),
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 250,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .theplus-post-search-wrapper .theplus-post-search-form' => 'max-width: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->end_controls_section();
		/*Adv tab*/
		$this->start_controls_section(
            'section_plus_extra_adv',
            [
                'label' => esc_html__('Plus Extras', 'theplus'),
                'tab' => Controls_Manager::TAB_ADVANCED,
            ]
        );
		$this->end_controls_section();
		/*Adv tab*/

		/*--On Scroll View Animation ---*/
			include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation.php';
	}
	
	 protected function render() {
        $settings = $this->get_settings_for_display();
		$style = $settings["form_style"];
		$content_align =!empty($settings['content_align']) ? 'text-'.$settings['content_align'] : '';
		$content_align_tablet = !empty($settings['content_align_tablet']) ? 'text--tablet'.$settings['content_align_tablet'] : '';
		$content_align_mobile =!empty($settings['content_align_mobile']) ? 'text--mobile'.$settings['content_align_mobile'] : '';

		/*--Plus Extra ---*/
			$PlusExtra_Class = "";
			include THEPLUS_PATH. 'modules/widgets/theplus-widgets-extra.php';

		/*--On Scroll View Animation ---*/
			include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';

			$output ='<div class="theplus-post-search-wrapper form-'.esc_attr($style).' '.esc_attr($animated_class).'" '.$animation_attr.'>';
				$output .='<form action="'.esc_url(home_url()).'" method="get" class="theplus-post-search-form '.esc_attr($content_align).' '.esc_attr($content_align_tablet).' '.esc_attr($content_align_mobile).'">';
					$output .='<div class="plus-newsletter-input-wrapper">';
						$icons='';
						if(!empty($settings['search_icon']) && $settings['search_icon']=='font_awesome_5' && !empty($settings['search_icon_fontawesome_5'])){
							ob_start();
							\Elementor\Icons_Manager::render_icon( $settings['search_icon_fontawesome_5'], [ 'aria-hidden' => 'true' ]);
							$icons = ob_get_contents();
							ob_end_clean();
							$output .='<span class="prefix-icon"><span>'.$icons.'</span></span>';
						}else{
							if(!empty($settings["search_icon_fontawesome"])){
								$output .='<span class="prefix-icon"><i class="'.esc_attr($settings["search_icon_fontawesome"]).'"></i></span>';
							}
						}						
						
						$lz1 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['search_field_bg_image'],$settings['search_field_focus_bg_image']) : '';
						$output .='<input type="text" name="s" placeholder="'.esc_attr($settings["search_field_placeholder"]).'" required class="form-control '.esc_attr($lz1).'" />';
						$lz2 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['button_bg_image'],$settings['button_hover_bg_image']) : '';
						$output .='<button class="search-btn-submit '.esc_attr($lz2).'">'.$this->render_text($settings).'</button>';
					$output .='</div>';

					$output .='<div class="theplus-notification"><div class="search-response"></div></div>';
				$output .= '</form>';
			$output .= '</div>';
		echo $before_content.$output.$after_content;
	}
	public function render_text($settings) {
		$this->add_render_attribute( 'content-wrapper', 'class', 'theplus-search-btn-wrapper' );
		
		$btn_icon='';
		if($settings["button_icon_style"] != 'none'){
			if($settings["button_icon_style"]=='font_awesome' && !empty($settings["button_icon_fontawesome"])){
				$btn_icon = $settings["button_icon_fontawesome"];				
			}
			if($settings["button_icon_style"]=='font_awesome_5' && !empty($settings["button_icon_fontawesome_5"])){
				ob_start();
				\Elementor\Icons_Manager::render_icon( $settings['button_icon_fontawesome_5'], [ 'aria-hidden' => 'true' ]);
				$btn_icon = ob_get_contents();
				ob_end_clean();			
			}
			if($settings["button_icon_style"] == 'icon_mind' && !empty($settings["button_icons_mind"])){
				$btn_icon = $settings["button_icons_mind"];				
			}
		}
		$btn_before=$btn_after='';
		
		if($settings["button_icon_style"]=='font_awesome_5' && !empty($settings["button_icon_fontawesome_5"])){
			if($settings["icon_align"] == 'left' && !empty($btn_icon)){
				$btn_before='<span class="search-btn-icon btn-before">'.$btn_icon.'</span>';
			}
			if($settings["icon_align"] == 'right' && !empty($btn_icon)){
				$btn_after='<span class="search-btn-icon btn-after">'.$btn_icon.'</span>';
			}
		}else{
			if($settings["icon_align"] == 'left' && !empty($btn_icon)){
				$btn_before='<i class="search-btn-icon btn-before '.esc_attr($btn_icon).'" aria-hidden="true"></i>';
			}
			if($settings["icon_align"] == 'right' && !empty($btn_icon)){
				$btn_after='<i class="search-btn-icon btn-after '.esc_attr($btn_icon).'" aria-hidden="true"></i>';
			}
		}		
		
		$search_button = $btn_before.wp_kses_post($settings['button_text']).$btn_after;

		return $search_button;
	}
    protected function content_template() {

    }
}