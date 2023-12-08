<?php 
/*
Widget Name: Header Extras
Description: header extra icons search bar, mini cart and toggle content..etc
Author: Theplus
*/

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Header_Extras extends Widget_Base {
		
	public function get_name() {
		return 'tp-header-extras';
	}

    public function get_title() {
        return esc_html__('Header Meta Content', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-info theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-header');
    }
	public function get_keywords() {
		return [ 'header search', 'search bar', 'search icon', 'cart menu', 'mini cart','woo cart', 'music', 'music header', 'music bar', 'header extra content', 'header meta content', 'header extras', 'header extra info', 'language switcher', 'header call to action' ];
	}

protected function register_controls() {
		
		$this->start_controls_section(
			'meta_content_sections',
			[
				'label' => esc_html__( 'Meta Content', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'select_icon_list',
			[
				'label' => esc_html__( 'Select Options', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'search'  => esc_html__( 'Search Bar', 'theplus' ),
					'cart' => esc_html__( 'Mini Cart', 'theplus' ),
					'extra_toggle' => esc_html__( 'Extra Toggle Bar', 'theplus' ),					
					'wpml_lang' => esc_html__( 'Language Switcher', 'theplus' ),					
					'music' => esc_html__( 'Music', 'theplus' ),
					'action_1' => esc_html__( 'Call to Action 1', 'theplus' ),
					'action_2' => esc_html__( 'Call to Action 2', 'theplus' ),
				],
			]
		);
		$repeater->add_responsive_control(
			'icon_left_space',
			[
				'label' => esc_html__( 'Icon Left Space', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons ul.icons-content-list > li{{CURRENT_ITEM}}' => 'padding-left: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$repeater->add_responsive_control(
			'icon_right_space',
			[
				'label' => esc_html__( 'Icon Right Space', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons ul.icons-content-list > li{{CURRENT_ITEM}}' => 'padding-right: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$repeater->add_control(
			'responsive_icon_hidden_options',
			[
				'label' => esc_html__( 'Responsive Device', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'responsive_hidden_desktop',
			[
				'label' => esc_html__( 'Hide On Desktop', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Hide', 'theplus' ),
				'label_off' => esc_html__( 'Show', 'theplus' ),				
				'default' => 'no',				
			]
		);
		$repeater->add_control(
			'responsive_hidden_tablet',
			[
				'label' => esc_html__( 'Hide On Tablet', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Hide', 'theplus' ),
				'label_off' => esc_html__( 'Show', 'theplus' ),	
				'default' => 'no',
			]
		);
		$repeater->add_control(
			'responsive_hidden_mobile',
			[
				'label' => esc_html__( 'Hide On Mobile', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Hide', 'theplus' ),
				'label_off' => esc_html__( 'Show', 'theplus' ),	
				'default' => 'no',
			]
		);
		$this->add_control(
			'sequence_icons',
			[
				'label' => '',
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'select_icon_list' => 'search',
					],
				],
				'title_field' => '{{{ select_icon_list }}}',
			]
		);
		$this->end_controls_section();
		/*search bar*/
		$this->start_controls_section(
            'section_search_options',
            [
                'label' => esc_html__('Search Options', 'theplus'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
		$this->add_control(
			'display_search_bar',
			[
				'label' => esc_html__( 'Display Search Bar', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'search_icon_style',
			[
				'label' => esc_html__( 'Icon Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'style-custom-icon'  => esc_html__( 'Custom Icon', 'theplus' ),
					'style-custom-image'  => esc_html__( 'Custom Image', 'theplus' ),
				],
				'condition'   => [
					'display_search_bar' => 'yes',
				],
			]
		);
		$this->add_control(
			'search_custom_icon',
			[
				'label' => esc_html__( 'Custom Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fab fa-searchengin',
					'library' => 'solid',
				],
				'condition' => [
					'display_search_bar' => 'yes',
					'search_icon_style' => 'style-custom-icon',					
				],	
			]
		);
		$this->add_control(
			'search_custom_image',
			[
				'label' => esc_html__( 'Custom Image', 'theplus' ),
				'type' => Controls_Manager::MEDIA,				
				'media_type' => 'image',
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'display_search_bar' => 'yes',
					'search_icon_style' => 'style-custom-image',					
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'sci_thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'after',
				'condition' => [
					'display_search_bar' => 'yes',
					'search_icon_style' => 'style-custom-image',					
				],
			]
		);
		$this->add_control(
			'search_bar_content_style',
			[
				'label' => esc_html__( 'Search Content Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'style-2'  => esc_html__( 'Style 2', 'theplus' ),
					'style-3'  => esc_html__( 'Style 3', 'theplus' ),
					'style-4'  => esc_html__( 'Style 4', 'theplus' ),					
				],
				'condition'   => [
					'display_search_bar' => 'yes',
				],
			]
		);
		$this->add_control(
			'search_bar_open_content_style',
			[
				'label' => esc_html__( 'Open Content Position', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'sboc_left',
				'options' => [
					'sboc_left'  => esc_html__( 'Left', 'theplus' ),
					'sboc_right'  => esc_html__( 'Right', 'theplus' ),				
				],
				'condition'   => [
					'display_search_bar' => 'yes',
					'search_bar_content_style' => ['style-3','style-4'],
				],
			]
		);
		$this->add_control(
			'search_placeholder_text',
			[
				'label' => esc_html__( 'Search Placeholder Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Search...', 'theplus' ),
				'condition'   => [
					'display_search_bar' => 'yes',
				],
			]
		);
		
		$this->end_controls_section();
		/*search bar*/
		/*search bar style*/
		$this->start_controls_section(
            'section_search_bar_styling',
            [
                'label' => esc_html__('Search Bar Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'display_search_bar' => 'yes',
				],
            ]
        );
		$this->add_control(
			'search_icon_svg_size',
			[
				'label' => esc_html__( 'Svg Icon Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [						
						'min' => 0,
						'max' => 150,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons li.search-icon .plus-post-search-icon svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .header-extra-icons li.search-icon .plus-post-search-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_search_icon_style' );
		$this->start_controls_tab(
			'tab_search_icon_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'search_icon_color',
			[
				'label' => esc_html__( 'Search Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons li.search-icon .plus-post-search-icon svg,{{WRAPPER}} .header-extra-icons li.search-icon .plus-post-search-icon svg path' => 'fill: {{VALUE}};stroke: {{VALUE}}',
					'{{WRAPPER}} .header-extra-icons .icons-content-list .search-icon .plus-post-search-icon i' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'form_content_background',
				'label' => esc_html__( 'Content Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .header-extra-icons .plus-search-form.plus-search-form-content,
				{{WRAPPER}} .plus-search-form.style-4 .plus-search-section input.plus-search-field',
				'condition'   => [
					'search_bar_content_style' => ['style-1','style-3','style-4'],
				],
			]
		);
		$this->add_control(
			'form_content_background_2',
			[
				'label' => esc_html__( 'Content Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .plus-search-form.plus-search-form-content.style-2' => 'background: {{VALUE}}',
					'{{WRAPPER}} .plus-search-form.style-2 .plus-search-section:before' => 'border-bottom-color: {{VALUE}}',
				],
				'condition'   => [
					'search_bar_content_style' => 'style-2',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_search_icon_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'search_icon_color_hover',
			[
				'label' => esc_html__( 'Search Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ff5a6e',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons li.search-icon .plus-post-search-icon:hover svg,{{WRAPPER}} .header-extra-icons li.search-icon .plus-post-search-icon:hover svg path' => 'fill: {{VALUE}};stroke: {{VALUE}}',
					'{{WRAPPER}} .header-extra-icons .icons-content-list .search-icon .plus-post-search-icon:hover i' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'search_field_heading_options',
			[
				'label' => esc_html__( 'Search Field Style', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'search_field_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
				'selector' => '{{WRAPPER}} .plus-search-form.plus-search-form-content input.plus-search-field',				
			]
		);
		$this->start_controls_tabs( 'tabs_search_field_style' );
		$this->start_controls_tab(
			'tab_search_field_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'search_field_placeholder_color',
			[
				'label' => esc_html__( 'Placeholder Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#888',
				'selectors' => [
					'{{WRAPPER}} .plus-search-form.plus-search-form-content input.plus-search-field::-webkit-input-placeholder' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'search_field_color',
			[
				'label' => esc_html__( 'Field Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .plus-search-form.plus-search-form-content input.plus-search-field' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'search_field_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-search-form.style-4 .plus-search-section input.plus-search-field',
				'condition'   => [
					'search_bar_content_style' => 'style-4',
				],	
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'search_field_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-search-form.style-4 .plus-search-section input.plus-search-field' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
				'condition'   => [
					'search_bar_content_style' => 'style-4',
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'search_field_shadow',
				'selector' => '{{WRAPPER}} .plus-search-form.style-4 .plus-search-section input.plus-search-field',
				'condition'   => [
					'search_bar_content_style' => 'style-4',
				],				
			]
		);
		$this->add_control(
			'field_border_color_1',
			[
				'label' => esc_html__( 'Border Bottom Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .plus-search-form.plus-search-form-content input.plus-search-field' => 'border-bottom-color: {{VALUE}}',
				],
				'condition'   => [
					'search_bar_content_style' => 'style-1',
				],
			]
		);
		$this->add_control(
			'search_field_bg_2',
			[
				'label' => esc_html__( 'Field Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .plus-search-form.plus-search-form-content input.plus-search-field' => 'background: {{VALUE}}',
				],
				'condition'   => [
					'search_bar_content_style' => 'style-2',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_search_field_focus',
			[
				'label' => esc_html__( 'Focus', 'theplus' ),
			]
		);
		$this->add_control(
			'search_field_placeholder_focus_color',
			[
				'label' => esc_html__( 'Placeholder Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#888',
				'selectors' => [
					'{{WRAPPER}} .plus-search-form.plus-search-form-content input.plus-search-field:focus::-webkit-input-placeholder' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'search_field_focus_color',
			[
				'label' => esc_html__( 'Field Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .plus-search-form.plus-search-form-content input.plus-search-field:focus' => 'color: {{VALUE}}',
				],
			]
		);
		
		$this->add_control(
			'field_focus_border_color_1',
			[
				'label' => esc_html__( 'Border Bottom Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .plus-search-form.plus-search-form-content.style-1 input.plus-search-field:focus' => 'border-bottom-color: {{VALUE}}',
				],
				'condition'   => [
					'search_bar_content_style' => 'style-1',
				],
			]
		);
		$this->add_control(
			'search_field_focus_bg_2',
			[
				'label' => esc_html__( 'Field Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .plus-search-form.plus-search-form-content input.plus-search-field:focus' => 'background: {{VALUE}}',
				],
				'condition'   => [
					'search_bar_content_style' => 'style-2',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'search_field_border_bottom_1',
			[
				'label' => esc_html__( 'Search Field Border Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 2,
				],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .plus-search-form.plus-search-form-content.style-1 input.plus-search-field' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
				],
				'condition'   => [
					'search_bar_content_style' => 'style-1',
				],
			]
		);
		$this->add_control(
			'search_submit_btn_heading_options',
			[
				'label' => esc_html__( 'Submit Button', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs( 'tabs_search_submit_btn_style' );
		$this->start_controls_tab(
			'tab_search_submit_btn_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'search_submit_btn_color',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors'  => [
					'{{WRAPPER}} .header-extra-icons li.search-icon .plus-search-form .plus-search-submit svg, .header-extra-icons li.search-icon .plus-search-form .plus-search-submit svg path' => 'fill: {{VALUE}};stroke: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_search_submit_btn_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'search_submit_btn_color_hover',
			[
				'label' => esc_html__( 'Icon Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ff5a6e',
				'selectors'  => [
					'{{WRAPPER}} .header-extra-icons li.search-icon .plus-search-form .plus-search-submit:hover svg, .header-extra-icons li.search-icon .plus-search-form .plus-search-submit:hover svg path' => 'fill: {{VALUE}};stroke: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		//Search Close Button Style
		$this->add_control(
			'search_close_btn_heading_options',
			[
				'label' => esc_html__( 'Search Close Button', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'   => [
					'search_bar_content_style' => ['style-1','style-3'],
				],
			]
		);
		$this->add_control(
			'close_btn_border',
			[
				'label' => esc_html__( 'Box Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'condition'   => [
					'search_bar_content_style' => ['style-1','style-3'],
				],
			]
		);
		$this->add_responsive_control(
			'close_btn_border_width',
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
					'{{WRAPPER}} .plus-search-form .plus-search-close' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'search_bar_content_style' => ['style-1','style-3'],
					'close_btn_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'close_btn_border_style',
			[
				'label'   => esc_html__( 'Border Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'none'   => esc_html__( 'None', 'theplus' ),
					'solid'  => esc_html__( 'Solid', 'theplus' ),
					'dotted' => esc_html__( 'Dotted', 'theplus' ),
					'dashed' => esc_html__( 'Dashed', 'theplus' ),
					'groove' => esc_html__( 'Groove', 'theplus' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .plus-search-form .plus-search-close' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'search_bar_content_style' => ['style-1','style-3'],
					'close_btn_border' => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_search_close_style' , [
			'condition' => [
				'search_bar_content_style' => ['style-1','style-3'],
			],
		]);
		$this->start_controls_tab(
			'tab_search_close_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'search_bar_content_style' => ['style-1','style-3'],
				],
			]
		);
		$this->add_control(
			'search_close_color',
			[
				'label' => esc_html__( 'Close Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors'  => [
					'{{WRAPPER}} .plus-search-form .plus-search-close .search-close:before,{{WRAPPER}} .plus-search-form .plus-search-close .search-close:after' => 'background: {{VALUE}};',
				],
				'condition' => [
					'search_bar_content_style' => ['style-1','style-3'],
				],
			]
		);
		$this->add_control(
			'close_btn_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}}  .plus-search-form .plus-search-close' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'search_bar_content_style' => ['style-1','style-3'],
					'close_btn_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'close_btn_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-search-form .plus-search-close' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'search_bar_content_style' => ['style-1','style-3'],
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_search_close_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'search_bar_content_style' => ['style-1','style-3'],
				],
			]
		);
		$this->add_control(
			'search_close_color_hover',
			[
				'label' => esc_html__( 'Close Icon Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ff5a6e',
				'selectors'  => [
					'{{WRAPPER}} .plus-search-form .plus-search-close:hover .search-close:before,{{WRAPPER}} .plus-search-form .plus-search-close:hover .search-close:after' => 'background: {{VALUE}};',
				],
				'condition' => [
					'search_bar_content_style' => ['style-1','style-3'],
				],
			]
		);
		$this->add_control(
			'close_btn_border_hover_color',
			[
				'label' => esc_html__( 'Border Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .plus-search-form .plus-search-close:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'search_bar_content_style' => ['style-1','style-3'],
					'close_btn_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'close_btn_border_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-search-form .plus-search-close:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'search_bar_content_style' => ['style-1','style-3'],
				],
			]
		);	
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'close_btn_bg_options',
			[
				'label' => esc_html__( 'Background Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'search_bar_content_style' => ['style-1','style-3'],
				],
			]
		);
		$this->start_controls_tabs( 'tabs_close_btn_background_style' , [
			'condition' => [
				'search_bar_content_style' => ['style-1','style-3'],
			],
		]);
		$this->start_controls_tab(
			'tab_close_btn_background_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'search_bar_content_style' => ['style-1','style-3'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'close_btn_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-search-form .plus-search-close',
				'condition' => [
					'search_bar_content_style' => ['style-1','style-3'],
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_close_btn_background_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'search_bar_content_style' => ['style-1','style-3'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'close_btn_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-search-form .plus-search-close:hover',
				'condition' => [
					'search_bar_content_style' => ['style-1','style-3'],
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'close_btn_shadow_options',
			[
				'label' => esc_html__( 'Box Shadow Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'search_bar_content_style' => ['style-1','style-3'],
				],
			]
		);
		$this->start_controls_tabs( 'tabs_close_btn_shadow_style' , [
			'condition' => [
				'search_bar_content_style' => ['style-1','style-3'],
			],
		]);
		$this->start_controls_tab(
			'tab_close_btn_shadow_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'search_bar_content_style' => ['style-1','style-3'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'close_btn_shadow',
				'selector' => '{{WRAPPER}} .plus-search-form .plus-search-close',
				'condition' => [
					'search_bar_content_style' => ['style-1','style-3'],
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_close_btn_shadow_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'search_bar_content_style' => ['style-1','style-3'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'close_btn_hover_shadow',
				'selector' => '{{WRAPPER}} .plus-search-form .plus-search-close:hover',
				'condition' => [
					'search_bar_content_style' => ['style-1','style-3'],
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'search_custom_img_heads',
			[
				'label' => esc_html__( 'Custom Image', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'search_icon_style' => ['style-custom-image'],
				],
			]
		);
		$this->add_responsive_control(
            'search_custom_img_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Image Size', 'theplus'),
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
					'{{WRAPPER}} .plus-post-search-icon.style-custom-image .tp-icon-img' => 'width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'search_icon_style' => ['style-custom-image'],			
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'search_custom_img_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-post-search-icon.style-custom-image .tp-icon-img',
				'separator' => 'before',
				'condition' => [
					'search_icon_style' => ['style-custom-image'],
				],
			]
		);
		$this->add_responsive_control(
			'search_custom_img_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-post-search-icon.style-custom-image .tp-icon-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',					
				],
				'condition' => [
					'search_icon_style' => ['style-custom-image'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'search_custom_img_shadow',
				'selector' => '{{WRAPPER}} .plus-post-search-icon.style-custom-image .tp-icon-img',
				'separator' => 'before',
				'condition' => [
					'search_icon_style' => ['style-custom-image'],
				],
			]
		);
		$this->add_responsive_control(
            'search_custom_img_top_offset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Open Content Top Offset', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-search-form.plus-search-form-content.style-4' => 'top: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'search_bar_content_style' => ['style-4'],		
				],
            ]
        );
		$this->end_controls_section();
		/*search bar style*/
		/*Mini Cart*/
		$this->start_controls_section(
            'section_mini_cart_options',
            [
                'label' => esc_html__('Cart Options', 'theplus'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
		$this->add_control(
			'display_mini_cart',
			[
				'label' => esc_html__( 'Display Cart', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'cart_icon_style',
			[
				'label' => esc_html__( 'Toggle Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'style-2'  => esc_html__( 'Style 2', 'theplus' ),
				],
				'condition'   => [
					'display_mini_cart' => 'yes',
				],
			]
		);
		$this->add_control(
			'cart_icon_direction',
			[
				'label' => esc_html__( 'Open Content Direction', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'right'  => esc_html__( 'Right', 'theplus' ),
					'left' => esc_html__( 'Left', 'theplus' ),									
				],
				'condition'   => [
					'display_mini_cart' => 'yes',
					'cart_icon_style' => 'style-2',
				],
			]
		);
		
		$this->add_control(
			'cart_icon_width_option',
			[
				'label' => esc_html__( 'Content Width', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'custom',
				'options' => [
					'custom'  => esc_html__( 'Custom Width/Height', 'theplus' ),
					'fullwidth' => esc_html__( 'Full-Width/Height', 'theplus' ),
				],
				'condition'   => [
					'display_mini_cart' => 'yes',
					'cart_icon_style' => 'style-2',
				],
			]
		);
		$this->add_responsive_control(
			'cart_extra_content_width',
			[
				'label' => esc_html__( 'Custom Content Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 400,
				],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon.style-2 .tpmc-header-extra-toggle-content.left,
					{{WRAPPER}} .header-extra-icons .mini-cart-icon.style-2 .tpmc-header-extra-toggle-content.right' => 'max-width: {{SIZE}}{{UNIT}};',					
				],
				'condition'   => [					
					'display_mini_cart' => 'yes',
					'cart_icon_style' => 'style-2',
					'cart_icon_width_option' => 'custom',
				],
			]
		);
		$this->add_responsive_control(
			'cart_extra_content_width_st1',
			[
				'label' => esc_html__( 'Open Content Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget.woocommerce.widget_shopping_cart,
					{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart .cart_list,
					{{WRAPPER}} .header-extra-icons .mini-cart-icon .tpmc-header-extra-toggle-content-ext' => 'width: {{SIZE}}{{UNIT}};max-width: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};',					
				],
				'condition'   => [					
					'display_mini_cart' => 'yes',
					'cart_icon_style' => 'style-1',
				],
			]
		);
		$this->add_responsive_control(
			'cart_extra_content_height_st1',
			[
				'label' => esc_html__( 'Open Content Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget.woocommerce.widget_shopping_cart,
					{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart .cart_list,
					{{WRAPPER}} .header-extra-icons .mini-cart-icon .tpmc-header-extra-toggle-content-ext' => 'height: {{SIZE}}{{UNIT}};max-height: {{SIZE}}{{UNIT}};min-height: {{SIZE}}{{UNIT}};',
				],
				'condition'   => [					
					'display_mini_cart' => 'yes',
					'cart_icon_style' => 'style-1',
				],
			]
		);
		$this->add_responsive_control(
			'cart_pro_ani_speed',
			[
				'label'   => esc_html__( 'Cart Product Transition Duration', 'theplus' ),
				'type'    => Controls_Manager::SLIDER,				
				'range' => [
					'px' => [
						'step' => 0.1,
						'min'  => 0.1,
						'max'  => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget.woocommerce.widget_shopping_cart' => 'transition: all {{SIZE}}s ;-webkit-transition: all {{SIZE}}s;-moz-transition: all {{SIZE}}s ;-ms-transition: all {{SIZE}}s;',
				],
				'condition' => [
					'display_mini_cart' => 'yes',			
				],
			]
		);
		
		$this->add_control(
			'cart_icon',
			[
				'label' => esc_html__( 'Cart Icon', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => esc_html__( 'Default', 'theplus' ),
					'cart_custom_icon'  => esc_html__( 'Custom Icon', 'theplus' ),
					'cart_custom_image'  => esc_html__( 'Custom Image', 'theplus' ),
				],
				'condition'   => [
					'display_mini_cart' => 'yes',
				],
			]
		);
		$this->add_control(
			'cart_icon_icon',
			[
				'label' => esc_html__( 'Custom Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-cart-arrow-down',
					'library' => 'solid',
				],
				'condition' => [
					'display_mini_cart' => 'yes',
					'cart_icon' => 'cart_custom_icon',					
				],	
			]
		);
		$this->add_control(
			'cart_icon_custom_image',
			[
				'label' => esc_html__( 'Custom Image', 'theplus' ),
				'type' => Controls_Manager::MEDIA,				
				'media_type' => 'image',
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'display_mini_cart' => 'yes',
					'cart_icon' => 'cart_custom_image',	
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'cici_thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'after',
				'condition' => [
					'display_mini_cart' => 'yes',
					'cart_icon' => 'cart_custom_image',	
				],	
			]
		);
		$this->add_control(
			'cart_offer_text',
			[
				'label' => esc_html__( 'Mini Cart Offer Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Free Shipping on All Orders Over $100', 'theplus' ),
				'separator' => 'before',
				'condition'   => [
					'display_mini_cart' => 'yes',
				],
			]
		);
		$this->add_control(
			'cart_offer_text_offset',
			[
				'label' => esc_html__( 'Offset', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -400,
						'max' => 400,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .mc-extra-bottom-con' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition'   => [
					'display_mini_cart' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*Mini Cart*/
		/*cart style*/
		$this->start_controls_section(
            'section_cart_styling',
            [
                'label' => esc_html__('Cart Icon Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'display_mini_cart' => 'yes',
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_cart_icon_style' );
		$this->start_controls_tab(
			'tab_cart_icon_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'cart_icon_color',
			[
				'label' => esc_html__( 'Cart Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons li.mini-cart-icon .plus-cart-icon.style-1 svg,{{WRAPPER}} .header-extra-icons li.mini-cart-icon .plus-cart-icon.style-1 svg path,{{WRAPPER}} .header-extra-icons li.mini-cart-icon .plus-cart-icon.style-2 svg,{{WRAPPER}} .header-extra-icons li.mini-cart-icon .plus-cart-icon.style-2 svg path' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .cart_custom_icon i' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'cart_icon_size_img_icn',
			[
				'label' => esc_html__( 'Cart Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .cart_custom_icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .cart_custom_icon svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .mini-cart-icon .plus-cart-icon.cart_custom_image .tp-icon-img' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'cart_icon' => ['cart_custom_icon','cart_custom_image'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'cart_icon_size_img_icn_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .mini-cart-icon .plus-cart-icon.cart_custom_image .tp-icon-img',
				'condition' => [
					'cart_icon' => 'cart_custom_image',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'cart_icon_size_img_icn_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .mini-cart-icon .plus-cart-icon.cart_custom_image .tp-icon-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
				'condition' => [
					'cart_icon' => 'cart_custom_image',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_cart_icon_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'cart_icon_color_hover',
			[
				'label' => esc_html__( 'Cart Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons li.mini-cart-icon .plus-cart-icon.style-1:hover svg,{{WRAPPER}} .header-extra-icons li.mini-cart-icon .plus-cart-icon.style-1:hover svg path,{{WRAPPER}} .header-extra-icons li.mini-cart-icon .plus-cart-icon.style-2:hover svg,{{WRAPPER}} .header-extra-icons li.mini-cart-icon .plus-cart-icon.style-2:hover svg path' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .plus-cart-icon.cart_custom_icon:hover i' => 'color: {{VALUE}}',
				],
			]
		);
		
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'cart_count_style_heading_options',
			[
				'label' => esc_html__( 'Cart Count', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cart_count_typography',
				'label' => esc_html__( 'Cart Count Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
				'selector' => '{{WRAPPER}} .header-extra-icons li.mini-cart-icon .plus-cart-icon .cart-wrap span',				
			]
		);
		$this->start_controls_tabs( 'tabs_cart_count_style' );
		$this->start_controls_tab(
			'tab_cart_count_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'cart_count_color',
			[
				'label' => esc_html__( 'Count Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons li.mini-cart-icon .plus-cart-icon .cart-wrap span' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'cart_count_background',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .header-extra-icons li.mini-cart-icon .plus-cart-icon .cart-wrap span',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_cart_count_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'cart_count_color_hover',
			[
				'label' => esc_html__( 'Count Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons li.mini-cart-icon .plus-cart-icon:hover .cart-wrap span' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'cart_count_background_hover',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .header-extra-icons li.mini-cart-icon .plus-cart-icon:hover .cart-wrap span',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		/*Mini Cart background Options*/
		$this->add_control(
			'mini_cart_style_heading_options',
			[
				'label' => esc_html__( 'Mini Cart Background', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs( 'tabs_mini_cart_bg_style' );
		$this->start_controls_tab(
			'tab_mini_cart_bg_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_responsive_control(
			'min_cart_box_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon.style-1 .widget_shopping_cart_content,
					{{WRAPPER}} .header-extra-icons .mini-cart-icon.style-2 .tpmc-header-extra-toggle-content,
					{{WRAPPER}} .header-extra-icons .mini-cart-icon .tpmc-header-extra-toggle-content-ext.open' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'mini_cart_bg_background',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .header-extra-icons .mini-cart-icon.style-1 .widget_shopping_cart_content,
				{{WRAPPER}} .header-extra-icons .mini-cart-icon.style-2 .tpmc-header-extra-toggle-content,
					{{WRAPPER}} .header-extra-icons .mini-cart-icon .tpmc-header-extra-toggle-content-ext.open',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'min_cart_box_shadow',
				'selector' => '{{WRAPPER}} .header-extra-icons .mini-cart-icon .tpmc-header-extra-toggle-content-ext.open',
				'separator' => 'before',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_mini_cart_bg_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_responsive_control(
			'min_cart_box_radius_hover',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon.style-1 .widget_shopping_cart_content:hover,
					{{WRAPPER}} .header-extra-icons .mini-cart-icon.style-2 .tpmc-header-extra-toggle-content:hover,
					{{WRAPPER}} .header-extra-icons .mini-cart-icon .tpmc-header-extra-toggle-content-ext.open:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'mini_cart_bg_hover',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .header-extra-icons .mini-cart-icon.style-1 .widget_shopping_cart_content:hover,
				{{WRAPPER}} .header-extra-icons .mini-cart-icon.style-2 .tpmc-header-extra-toggle-content:hover,
					{{WRAPPER}} .header-extra-icons .mini-cart-icon .tpmc-header-extra-toggle-content-ext.open:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'min_cart_box_shadow_hover',
				'selector' => '{{WRAPPER}} .header-extra-icons .mini-cart-icon .tpmc-header-extra-toggle-content-ext.open:hover',
				'separator' => 'before',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		
		/*Mini Cart extra text background Options*/
		$this->add_control(
			'mini_cart_style_heading_options_bt',
			[
				'label' => esc_html__( 'Mini Cart Bottom Text Background', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'mini_cart_etext_typography',
				'label' => esc_html__( 'Cart Title Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
				'selector' => '{{WRAPPER}} .header-extra-icons .mini-cart-icon .mc-extra-bottom-con',				
			]
		);

		$this->start_controls_tabs( 'tabs_mini_cart_bg_style_bt' );
		$this->start_controls_tab(
			'tab_mini_cart_bg_normal_bt',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'cart_etext_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .mc-extra-bottom-con' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'min_cart_box_radius_bt',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .mc-extra-bottom-con' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'mini_cart_bg_background_bt',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .header-extra-icons .mini-cart-icon .tpmc-header-extra-toggle-content-ext.open,{{WRAPPER}} .header-extra-icons .mini-cart-icon .mc-extra-bottom-con',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'min_cart_box_shadow_bt',
				'selector' => '{{WRAPPER}} .header-extra-icons .mini-cart-icon .mc-extra-bottom-con',
				'separator' => 'before',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_mini_cart_bg_hover_bt',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'cart_etext_color_h',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .tpmc-header-extra-toggle-content-ext.open:hover .mc-extra-bottom-con' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'min_cart_box_radius_hover_bt',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .tpmc-header-extra-toggle-content-ext.open:hover .mc-extra-bottom-con' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'mini_cart_bg_hover_bt',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .header-extra-icons .mini-cart-icon .tpmc-header-extra-toggle-content-ext.open:hover .mc-extra-bottom-con',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'min_cart_box_shadow_hover_bt',
				'selector' => '{{WRAPPER}} .header-extra-icons .mini-cart-icon .tpmc-header-extra-toggle-content-ext.open:hover .mc-extra-bottom-con',
				'separator' => 'before',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*cart style*/
		/*Mini cart Dropdown style*/
		$this->start_controls_section(
            'section_mini_cart_styling',
            [
                'label' => esc_html__('Mini Cart Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'display_mini_cart' => 'yes',
				],
            ]
        );
		$this->add_responsive_control(
			'cart_inner_padding',
			[
				'label' => esc_html__( 'Open Content Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon.style-2 .tpmc-header-extra-toggle-content.open' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'   => [
					'display_mini_cart' => 'yes',
					'cart_icon_style' => 'style-2',
				],				
			]
		);	
		$this->add_control(
			'mini_cart_empty_heading',
			[
				'label' => esc_html__( 'Empty Cart Style', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'mini_cart_empty_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-mini-cart__empty-message:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'mini_cart_empty_icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .woocommerce-mini-cart__empty-message:before' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'mini_cart_empty_text_size',
			[
				'label' => esc_html__( 'Text Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .widget_shopping_cart_content .woocommerce-mini-cart__empty-message' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'mini_cart_empty_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .widget_shopping_cart_content .woocommerce-mini-cart__empty-message' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'mini_cart_title_heading',
			[
				'label' => esc_html__( 'Title Style', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'mini_cart_title_typography',
				'label' => esc_html__( 'Cart Title Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
				'selector' => '{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart .cart_list li > a:not(.remove)',				
			]
		);
		$this->start_controls_tabs( 'tabs_mini_cart_title_style' );
		$this->start_controls_tab(
			'tab_cart_title_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'cart_title_color',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart .cart_list li > a:not(.remove)' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_cart_title_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'cart_title_color_hover',
			[
				'label' => esc_html__( 'Count Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#8072fc',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart .cart_list li > a:not(.remove):hover' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'mini_cart_quantity_heading',
			[
				'label' => esc_html__( 'Quantity Style', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'mini_cart_quantity_typography',
				'label' => esc_html__( 'Cart Quantity Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
				'selector' => '{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart .cart_list li .quantity',				
			]
		);
		$this->start_controls_tabs( 'tabs_mini_cart_quantity_style' );
		$this->start_controls_tab(
			'tab_cart_quantity_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'cart_quantity_color',
			[
				'label' => esc_html__( 'Quantity Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#848484',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart .cart_list li .quantity,{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart .cart_list li .quantity span' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'cart_remove_color',
			[
				'label' => esc_html__( 'Remove Cart Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#848484',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart .cart_list li a.remove' => 'color: {{VALUE}} !important',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_cart_quantity_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'cart_quantity_color_hover',
			[
				'label' => esc_html__( 'Count Quantity Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#848484',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart .cart_list li:hover .quantity,{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart .cart_list li:hover .quantity span' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'cart_remove_color_hover',
			[
				'label' => esc_html__( 'Remove Cart Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#848484',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart .cart_list li a.remove:hover' => 'color: {{VALUE}} !important',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'mini_cart_sep_heading',
			[
				'label' => esc_html__( 'Separator Style', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'mini_cart_sep_border_style',
			[
				'label' => esc_html__( 'Separator Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'selectors'  => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart .cart_list > li' => 'border-bottom: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'mini_cart_sep_border_width',
			[
				'label' => esc_html__( 'Separator Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart .cart_list > li' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'mini_cart_sep_border_color',
			[
				'label' => esc_html__( 'Separator Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart .cart_list > li' => 'border-color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'mini_cart_in_img_heading',
			[
				'label' => esc_html__( 'Cart Product Image Style', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'mini_cart_in_img_size',
			[
				'label' => esc_html__( 'Image Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 500,
						'step' => 1,
					],
				],				
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart .cart_list li > a > img' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'mini_cart_in_img_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart .cart_list li > a > img',				
			]
		);
		$this->add_responsive_control(
			'mini_cart_in_img_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart .cart_list li > a > img,{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart .widget_shopping_cart_content .elementor-menu-cart__product a > img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',	
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'mini_cart_in_img_shadow',
				'selector' => '{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart .cart_list li > a > img',				
			]
		);
		$this->add_control(
			'mini_cart_subtotal_heading',
			[
				'label' => esc_html__( 'SubTotal/Price Style', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs( 'tabs_min_cart_subtotal_style' );
		$this->start_controls_tab(
			'tab_min_cart_subtotal_normal',
			[
				'label' => esc_html__( 'SubTotal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'mini_cart_subtotal_typography',
				'label' => esc_html__( 'Sub-Total Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
				'selector' => '{{WRAPPER}} .header-extra-icons .mini-cart-icon .total strong,
				{{WRAPPER}} .header-extra-icons .elementor-menu-cart__subtotal strong',
			]
		);
		$this->add_control(
			'cart_subtotal_color',
			[
				'label' => esc_html__( 'Sub-Total Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .total strong,
					{{WRAPPER}} .header-extra-icons .elementor-menu-cart__subtotal strong' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_min_cart_subtotal_price',
			[
				'label' => esc_html__( 'Total Price', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'mini_cart_price_typography',
				'label' => esc_html__( 'Total Price Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
				'selector' => '{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart .total span.amount,
				{{WRAPPER}} .header-extra-icons .elementor-menu-cart__subtotal span.amount',
			]
		);
		$this->add_control(
			'cart_price_color',
			[
				'label' => esc_html__( 'Total Price Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart .total span.amount,
					{{WRAPPER}} .header-extra-icons .elementor-menu-cart__subtotal span.amount' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'mini_cart_btn_heading',
			[
				'label' => esc_html__( 'Buttons Style', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs( 'tabs_min_cart_btn_style' );
		$this->start_controls_tab(
			'tab_min_cart_btn_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'min_cart_btn_border_style',
			[
				'label'   => esc_html__( 'Border Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'   => esc_html__( 'None', 'theplus' ),
					'solid'  => esc_html__( 'Solid', 'theplus' ),
					'dotted' => esc_html__( 'Dotted', 'theplus' ),
					'dashed' => esc_html__( 'Dashed', 'theplus' ),
					'groove' => esc_html__( 'Groove', 'theplus' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.button' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'min_cart_btn_border_width',
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
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.button' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'min_cart_btn_border_style!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'min_cart_btn_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'min_cart_btn_shadow',
				'selector' => '{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.button',				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_min_cart_btn_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_responsive_control(
			'min_cart_btn_hover_radius',
			[
				'label'      => esc_html__( 'Hover Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'min_cart_btn_hover_shadow',
				'selector' => '{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.button:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		/*View Cart Button*/
		$this->add_control(
			'mini_cart_view_btn_heading',
			[
				'label' => esc_html__( 'View Cart Button', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'mini_cart_view_btn_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
							'top' => '10',
							'right' => '15',
							'bottom' => '10',
							'left' => '15',
							'isLinked' => false 
				],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.button:not(.checkout),
					{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.elementor-button--view-cart' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'mini_cart_view_btn_typography',
				'selector' => '{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.button:not(.checkout),
				{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.elementor-button--view-cart',
			]
		);
		$this->start_controls_tabs( 'tabs_min_cart_view_btn_style' );
		$this->start_controls_tab(
			'tab_min_cart_view_btn_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'min_cart_view_btn_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.button:not(.checkout),
					{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.elementor-button--view-cart' => 'color: {{VALUE}};',					
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'min_cart_view_btn_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.button:not(.checkout),
				{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.elementor-button--view-cart',
			]
		);
		$this->add_control(
			'min_cart_view_btn_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.button:not(.checkout),
					{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.elementor-button--view-cart' => 'border-color: {{VALUE}};',					
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_min_cart_view_btn_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'min_cart_view_btn_text_color_hover',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.button:not(.checkout):hover,
					{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.elementor-button--view-cart:hover' => 'color: {{VALUE}};',					
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'min_cart_view_btn_background_hover',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.button:not(.checkout):hover,
				{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.elementor-button--view-cart:hover',
			]
		);
		$this->add_control(
			'min_cart_view_btn_border_hover_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.button:not(.checkout):hover,
					{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.elementor-button--view-cart:hover' => 'border-color: {{VALUE}};',					
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		/*View Cart Button*/
		/*Checkout Cart Button*/
		$this->add_control(
			'mini_cart_checkout_btn_heading',
			[
				'label' => esc_html__( 'Checkout Button', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'mini_cart_checkout_btn_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
							'top' => '10',
							'right' => '15',
							'bottom' => '10',
							'left' => '15',
							'isLinked' => false 
				],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.button.checkout,
					{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.elementor-button--checkout' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'mini_cart_checkout_btn_typography',
				'selector' => '{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.button.checkout,
				{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.elementor-button--checkout',
			]
		);
		$this->start_controls_tabs( 'tabs_min_cart_checkout_btn_style' );
		$this->start_controls_tab(
			'tab_min_cart_checkout_btn_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'min_cart_checkout_btn_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.button.checkout,
					{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.elementor-button--checkout' => 'color: {{VALUE}};',					
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'min_cart_checkout_btn_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.button.checkout,
				{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.elementor-button--checkout',
			]
		);
		$this->add_control(
			'min_cart_checkout_btn_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.button.checkout,
					{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.elementor-button--checkout' => 'border-color: {{VALUE}};',					
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_min_cart_checkout_btn_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'min_cart_checkout_btn_text_color_hover',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.button.checkout:hover,
					{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.elementor-button--checkout:hover' => 'color: {{VALUE}};',					
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'min_cart_checkout_btn_background_hover',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.button.checkout:hover,
				{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.elementor-button--checkout:hover',
			]
		);
		$this->add_control(
			'min_cart_checkout_btn_border_hover_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.button.checkout:hover,
					{{WRAPPER}} .header-extra-icons .mini-cart-icon .widget_shopping_cart a.elementor-button--checkout:hover' => 'border-color: {{VALUE}};',					
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		/*checkout cart Button*/
		
		/*scrollbar mini cart start*/
		$this->add_control(
			'display_scrolling_bar',
			[
				'label' => esc_html__( 'Content Scrolling Bar', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),				
				'default' => 'yes',
				'separator' => 'before',
			]
		);
		
		$this->start_controls_tabs( 'tabs_scrolling_bar_style' , [
			'condition' => [
				'display_scrolling_bar' => 'yes',
			],
		]);
		$this->start_controls_tab(
			'tab_scrolling_bar_scrollbar',
			[
				'label' => esc_html__( 'Scrollbar', 'theplus' ),
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->add_control(
			'scroll_scrollbar_width',
			[
				'label' => esc_html__( 'ScrollBar Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'.header-extra-icons .mini-cart-icon .widget_shopping_cart.open .cart_list::-webkit-scrollbar' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'scroll_scrollbar_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '.header-extra-icons .mini-cart-icon .widget_shopping_cart.open .cart_list::-webkit-scrollbar',
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_scrolling_bar_thumb',
			[
				'label' => esc_html__( 'Thumb', 'theplus' ),
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'scroll_thumb_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '.header-extra-icons .mini-cart-icon .widget_shopping_cart.open .cart_list::-webkit-scrollbar-thumb',
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'scroll_thumb_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.header-extra-icons .mini-cart-icon .widget_shopping_cart.open .cart_list::-webkit-scrollbar-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',					
				],
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'scroll_thumb_shadow',
				'selector' => '.header-extra-icons .mini-cart-icon .widget_shopping_cart.open .cart_list::-webkit-scrollbar-thumb',
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_scrolling_bar_track',
			[
				'label' => esc_html__( 'Track', 'theplus' ),
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'scroll_track_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '.header-extra-icons .mini-cart-icon .widget_shopping_cart.open .cart_list::-webkit-scrollbar-track',
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'scroll_track_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.header-extra-icons .mini-cart-icon .widget_shopping_cart.open .cart_list::-webkit-scrollbar-track' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'scroll_track_shadow',
				'selector' => '.header-extra-icons .mini-cart-icon .widget_shopping_cart.open .cart_list::-webkit-scrollbar-track',
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		/*scrollbar mini cart end*/
		
		/*mini cart close icon start*/
		$this->add_control(
			'mc_close_heading_options',
			[
				'label' => esc_html__( 'Close Icon Style', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'   => [
					'cart_icon_style' => 'style-2',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_mc_toggle_close_style' , [
			'condition' => [
				'cart_icon_style' => 'style-2',
			],
		]);
		$this->start_controls_tab(
			'tab_mc_toggle_close_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition'   => [
					'cart_icon_style' => 'style-2',
				],
			]
		);
		$this->add_control(
			'mc_toggle_icon_close_color',
			[
				'label' => esc_html__( 'Close Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon.style-2 .tpmc-extra-toggle-close-menu:before,{{WRAPPER}} .header-extra-icons .mini-cart-icon.style-2 .tpmc-extra-toggle-close-menu:after' => 'background: {{VALUE}}',
				],
				'condition'   => [
					'cart_icon_style' => 'style-2',
				],
			]
		);
		$this->add_control(
			'mc_toggle_icon_close_bg',
			[
				'label' => esc_html__( 'Close Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ff5a6e',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon.style-2 .tpmc-extra-toggle-close-menu' => 'background: {{VALUE}}',
				],
				'condition'   => [
					'cart_icon_style' => 'style-2',
				],
			]
		);
		$this->add_control(
			'mc_toggle_icon_close_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon.style-2 .tpmc-extra-toggle-close-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'   => [
					'cart_icon_style' => 'style-2',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'mc_toggle_icon_close_box_shadow',
				'label' => esc_html__( 'Hover Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .header-extra-icons .mini-cart-icon.style-2 .tpmc-extra-toggle-close-menu',
				'condition'   => [
					'cart_icon_style' => 'style-2',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_mc_toggle_close_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition'   => [
					'cart_icon_style' => 'style-2',
				],
			]
		);
		$this->add_control(
			'mc_toggle_icon_close_color_hover',
			[
				'label' => esc_html__( 'Close Icon Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ff5a6e',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon.style-2 .tpmc-extra-toggle-close-menu:hover:before,
					{{WRAPPER}} .header-extra-icons .mini-cart-icon.style-2 .tpmc-extra-toggle-close-menu:hover:after' => 'background: {{VALUE}}',
				],
				'condition'   => [
					'cart_icon_style' => 'style-2',
				],
			]
		);
		$this->add_control(
			'mc_toggle_icon_close_bg_hover',
			[
				'label' => esc_html__( 'Close Hover Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon.style-2 .tpmc-extra-toggle-close-menu:hover' => 'background: {{VALUE}}',
				],
				'condition'   => [
					'cart_icon_style' => 'style-2',
				],
			]
		);
		$this->add_control(
			'mc_toggle_icon_close_border_radius_hover',
			[
				'label' => esc_html__( 'Hover Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .mini-cart-icon.style-2 .tpmc-extra-toggle-close-menu:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'   => [
					'cart_icon_style' => 'style-2',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'mc_toggle_icon_close_box_shadow_hover',
				'label' => esc_html__( 'Hover Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .header-extra-icons .mini-cart-icon.style-2 .tpmc-extra-toggle-close-menu:hover',
				'condition'   => [
					'cart_icon_style' => 'style-2',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		/*mini cart close icon end*/		
		
		$this->end_controls_section();
		/*Mini cart style*/
		/*Extra Toogle Bar*/
		$this->start_controls_section(
            'section_extra_toggle_bar_options',
            [
                'label' => esc_html__('Extra Toggle Bar', 'theplus'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
		$this->add_control(
			'display_extra_toggle_bar',
			[
				'label' => esc_html__( 'Display Toggle Bar', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'extra_toggle_style',
			[
				'label' => esc_html__( 'Toggle Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'style-2'  => esc_html__( 'Style 2', 'theplus' ),
					'style-3'  => esc_html__( 'Style 3', 'theplus' ),
					'style-4'  => esc_html__( 'Style 4', 'theplus' ),
					'style-5'  => esc_html__( 'Custom', 'theplus' ),
				],
				'condition'   => [
					'display_extra_toggle_bar' => 'yes',
				],
			]
		);
		$this->add_control(
			'extra_toggle_style_custom',
			[
				'label' => esc_html__( 'Custom', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'custom_icon',
				'options' => [
					'custom_icon'  => esc_html__( 'Icon', 'theplus' ),
					'custom_img'  => esc_html__( 'Image', 'theplus' ),
				],
				'condition'   => [
					'extra_toggle_style' => 'style-5',
				],
			]
		);
		$this->add_control(
			'extra_toggle_custom_icon',
			[
				'label' => esc_html__( 'Custom Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fab fa-searchengin',
					'library' => 'solid',
				],
				'condition' => [
					'display_extra_toggle_bar' => 'yes',
					'extra_toggle_style' => 'style-5',
					'extra_toggle_style_custom' => 'custom_icon',					
				],	
			]
		);
		$this->add_control(
			'extra_toggle_custom_image',
			[
				'label' => esc_html__( 'Custom Image', 'theplus' ),
				'type' => Controls_Manager::MEDIA,				
				'media_type' => 'image',
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'display_extra_toggle_bar' => 'yes',
					'extra_toggle_style' => 'style-5',
					'extra_toggle_style_custom' => 'custom_img',					
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'etci_thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'after',
				'condition' => [
					'display_extra_toggle_bar' => 'yes',
					'extra_toggle_style' => 'style-5',
					'extra_toggle_style_custom' => 'custom_img',					
				],
			]
		);
		$this->add_control(
			'extra_content_template',
			[
				'label'       => esc_html__( 'Elementor Templates', 'theplus' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '0',
				'options'     => theplus_get_templates(),
				'label_block' => 'true',
				'condition'   => [
					'display_extra_toggle_bar' => 'yes',
				],
			]
		);
		$this->add_control(
			'extra_toggle_bar_direction',
			[
				'label' => esc_html__( 'Open Content Direction', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'right'  => esc_html__( 'Right', 'theplus' ),
					'left' => esc_html__( 'Left', 'theplus' ),
					'top' => esc_html__( 'Top', 'theplus' ),
					'bottom' => esc_html__( 'Bottom', 'theplus' ),					
				],
				'condition'   => [
					'display_extra_toggle_bar' => 'yes',
				],
			]
		);
		$this->add_control(
			'extra_content_width_option',
			[
				'label' => esc_html__( 'Content Width', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'custom',
				'options' => [
					'custom'  => esc_html__( 'Custom Width/Height', 'theplus' ),
					'fullwidth' => esc_html__( 'Full-Width/Height', 'theplus' ),
				],
				'condition'   => [
					'display_extra_toggle_bar' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'extra_content_width',
			[
				'label' => esc_html__( 'Custom Content Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 400,
				],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .extra-toggle-icon .header-extra-toggle-content.left,{{WRAPPER}} .header-extra-icons .extra-toggle-icon .header-extra-toggle-content.right' => 'max-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .header-extra-icons .extra-toggle-icon .header-extra-toggle-content.top,{{WRAPPER}} .header-extra-icons .extra-toggle-icon .header-extra-toggle-content.bottom' => 'max-height: {{SIZE}}{{UNIT}};',
				],
				'condition'   => [					
					'display_extra_toggle_bar' => 'yes',
					'extra_content_width_option' => 'custom',
				],
			]
		);
		$this->end_controls_section();
		/*Extra Toogle Bar*/
		/*Extra Toogle Bar*/
		$this->start_controls_section(
            'section_extra_toggle_bar_styling',
            [
                'label' => esc_html__('Extra Toggle Bar', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'display_extra_toggle_bar' => 'yes',
				],
            ]
        );
		$this->add_control(
			'extra_toggle_icon_size',
			[
				'label' => esc_html__( 'Toggle Icon Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 200,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .extra-toggle-icon .header-extra-toggle-click.style-1,
					{{WRAPPER}} .header-extra-icons .extra-toggle-icon .et_icon_img_st5 i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .header-extra-icons .extra-toggle-icon .header-extra-toggle-click.style-1 svg,
					{{WRAPPER}} .header-extra-icons .extra-toggle-icon .et_icon_img_st5 svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .header-extra-icons .extra-toggle-icon .header-extra-toggle-click.style-1,
					{{WRAPPER}} .header-extra-icons .extra-toggle-icon .tp-icon-img' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'   => [
					'extra_toggle_style' => ['style-1','style-5'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'extra_toggle_custom_img_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .header-extra-icons .extra-toggle-icon .tp-icon-img',
				'separator' => 'before',
				'condition' => [
					'extra_toggle_style' => ['style-5'],
					'extra_toggle_style_custom' => ['custom_img'],
				],
			]
		);
		$this->add_responsive_control(
			'extra_toggle_custom_img_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .header-extra-icons .extra-toggle-icon .tp-icon-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',					
				],
				'condition' => [
					'extra_toggle_style' => ['style-5'],
					'extra_toggle_style_custom' => ['custom_img'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'extra_toggle_custom_img_shadow',
				'selector' => '{{WRAPPER}} .header-extra-icons .extra-toggle-icon .tp-icon-img',
				'separator' => 'before',
				'condition' => [
					'extra_toggle_style' => ['style-5'],
					'extra_toggle_style_custom' => ['custom_img'],
				],
			]
		);
		$this->start_controls_tabs( 'tabs_extra_toggle_style' );
		$this->start_controls_tab(
			'tab_extra_toggle_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'extra_toggle_icon_color',
			[
				'label' => esc_html__( 'Toggle Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .extra-toggle-icon .header-extra-toggle-click.style-1 .menu_line,
					{{WRAPPER}} .header-extra-toggle-click.style-2 .tp-menu-st2,{{WRAPPER}} .header-extra-toggle-click.style-2 .tp-menu-st2::before, {{WRAPPER}} .header-extra-toggle-click.style-2 .tp-menu-st2::after,{{WRAPPER}} .header-extra-toggle-click.style-2 .tp-menu-st2-h, {{WRAPPER}} .header-extra-toggle-click.style-2 .tp-menu-st2-h::before,{{WRAPPER}} .header-extra-toggle-click.style-2 .tp-menu-st2-h::after,
					{{WRAPPER}} .header-extra-toggle-click.style-3 .tp-menu-st3,{{WRAPPER}} .header-extra-toggle-click.style-3 .tp-menu-st3::before, {{WRAPPER}} .header-extra-toggle-click.style-3 .tp-menu-st3::after,
					{{WRAPPER}} .header-extra-toggle-click.style-4 span' => 'background: {{VALUE}}',
					'{{WRAPPER}} .header-extra-icons .extra-toggle-icon .et_icon_img_st5 i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .header-extra-icons .extra-toggle-icon .et_icon_img_st5 svg' => 'fill: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_extra_toggle_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'extra_toggle_icon_color_hover',
			[
				'label' => esc_html__( 'Toggle Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ff5a6e',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .extra-toggle-icon .header-extra-toggle-click.style-1:hover .menu_line,
					{{WRAPPER}} .header-extra-toggle-click.style-2 .tp-menu-st2-h,{{WRAPPER}} .header-extra-toggle-click.style-2 .tp-menu-st2-h::before, {{WRAPPER}} .header-extra-toggle-click.style-2 .tp-menu-st2-h::after,
					{{WRAPPER}} .header-extra-toggle-click.style-3.open .tp-menu-st3::before,{{WRAPPER}} .header-extra-toggle-click.style-3.open .tp-menu-st3::after,
					{{WRAPPER}} .header-extra-toggle-click.style-4.open span:nth-last-child(3),{{WRAPPER}} .header-extra-toggle-click.style-4.open span:nth-last-child(1)' => 'background: {{VALUE}} !important',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'extra_toggle_content_heading_options',
			[
				'label' => esc_html__( 'Toggle Content Style', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'content_inner_padding',
			[
				'label' => esc_html__( 'Content Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px','em' ],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .extra-toggle-icon .header-extra-toggle-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'extra_toggle_content_background',
				'label' => esc_html__( 'Content Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .header-extra-icons .extra-toggle-icon .header-extra-toggle-content',				
			]
		);
		$this->add_control(
			'extra_toggle_close_heading_options',
			[
				'label' => esc_html__( 'Toggle Close Icon Style', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs( 'tabs_extra_toggle_close_style' );
		$this->start_controls_tab(
			'tab_extra_toggle_close_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'extra_toggle_icon_close_color',
			[
				'label' => esc_html__( 'Close Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .extra-toggle-icon .extra-toggle-close-menu:before,{{WRAPPER}} .header-extra-icons .extra-toggle-icon .extra-toggle-close-menu:after' => 'background: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'extra_toggle_icon_close_bg',
			[
				'label' => esc_html__( 'Close Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ff5a6e',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .extra-toggle-icon .extra-toggle-close-menu' => 'background: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'extra_toggle_icon_close_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .extra-toggle-icon .extra-toggle-close-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'extra_toggle_icon_close_box_shadow',
				'label' => esc_html__( 'Hover Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .header-extra-icons .extra-toggle-icon .extra-toggle-close-menu',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_extra_toggle_close_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'extra_toggle_icon_close_color_hover',
			[
				'label' => esc_html__( 'Close Icon Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ff5a6e',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .extra-toggle-icon .extra-toggle-close-menu:hover:before,{{WRAPPER}} .header-extra-icons .extra-toggle-icon .extra-toggle-close-menu:hover:after' => 'background: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'extra_toggle_icon_close_bg_hover',
			[
				'label' => esc_html__( 'Close Hover Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .extra-toggle-icon .extra-toggle-close-menu:hover' => 'background: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'extra_toggle_icon_close_border_radius_hover',
			[
				'label' => esc_html__( 'Hover Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .extra-toggle-icon .extra-toggle-close-menu:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'extra_toggle_icon_close_box_shadow_hover',
				'label' => esc_html__( 'Hover Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .header-extra-icons .extra-toggle-icon .extra-toggle-close-menu:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'extra_toggle_overlay_heading_options',
			[
				'label' => esc_html__( 'Overlay Style', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'extra_toggle_overlay_background',
				'label' => esc_html__( 'Overlay Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .header-extra-icons .extra-toggle-icon .extra-toggle-content-overlay',
			]
		);
		$this->end_controls_section();
		/*Extra Toogle Bar*/
		
		/*Language Switcher*/
		$this->start_controls_section(
            'section_language_switcher_wpml_styling',
            [
                'label' => esc_html__('WPML Language Switcher', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'display_language_switcher' => 'yes',
					'select_trans' => 'p_wpml',
				],
            ]
        );
		$this->add_responsive_control(
			'wpml_ls_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown a,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown .wpml-ls-current-language>a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
            'wpml_ls_max_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Max Width', 'theplus'),
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 300,
						'step' => 1,
					],
				],
				'separator' => 'before',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .wpml-ls-legacy-dropdown' => 'max-width: {{SIZE}}{{UNIT}}',
				],
				'condition'   => [
					'wpml_style' => 'wpml_style_2',
				],
            ]
        );
		$this->add_responsive_control(
            'wpml_ls_min_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Min Width', 'theplus'),
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 300,
						'step' => 1,
					],
				],
				'separator' => 'before',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item' => 'min-width: {{SIZE}}{{UNIT}}',
				],
				'condition'   => [
					'wpml_style' => 'wpml_style_1',
				],
            ]
        );
		$this->add_responsive_control(
            'wpml_ls_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
						'step' => 1,
					],
				],
				'separator' => 'before',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown a,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown .wpml-ls-current-language>a' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item img,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown a .wpml-ls-flag' => 'width: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_wpml_ls' );
		$this->start_controls_tab(
			'tab_wpml_ls_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'wpml_ls_color_n',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown a,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown .wpml-ls-current-language>a' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'wpml_ls_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown a,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown .wpml-ls-current-language>a',				
			]
		);	
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'wpml_ls_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown a,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown .wpml-ls-current-language>a',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'wpml_ls_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown a,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown .wpml-ls-current-language>a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'wpml_ls_shadow',
				'selector' => '{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown a,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown .wpml-ls-current-language>a',				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_wpml_ls_active',
			[
				'label' => esc_html__( 'Active/Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'wpml_ls_color_a',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item.tp-wpml-item__active,
					{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item:hover,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown a:hover,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown a:focus,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown .wpml-ls-current-language:hover>a' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'wpml_ls_background_a',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item.tp-wpml-item__active,
					{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item:hover,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown a:hover,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown a:focus,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown .wpml-ls-current-language:hover>a',				
			]
		);	
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'wpml_ls_border_a',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' =>'{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item.tp-wpml-item__active,
					{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item:hover,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown a:hover,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown a:focus,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown .wpml-ls-current-language:hover>a',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'wpml_ls_br_a',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item.tp-wpml-item__active,
					{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item:hover,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown a:hover,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown a:focus,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown .wpml-ls-current-language:hover>a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'wpml_ls_shadow_a',
				'selector' => '{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item.tp-wpml-item__active,
					{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item:hover,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown a:hover,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown a:focus,
					{{WRAPPER}} .header-extra-icons .wpml-ls-legacy-dropdown .wpml-ls-current-language:hover>a',				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'wpml_ba_text_style_heading',
			[
				'label' => esc_html__( 'Before/After Text Style', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'wpml_ba_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-before-lt,{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-after-lt,{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item .tp-wpml-before-lt,{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item .tp-wpml-after-lt' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'wpml_ba_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-before-lt,{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-after-lt,{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item .tp-wpml-before-lt,{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item .tp-wpml-after-lt' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'wpml_ba_typography',
				'selector' => '{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-before-lt,{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-after-lt,{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item .tp-wpml-before-lt,{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item .tp-wpml-after-lt',
			]
		);
		$this->add_control(
			'wpml_ba_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-before-lt,{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-after-lt,{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item .tp-wpml-before-lt,{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item .tp-wpml-after-lt' => 'color: {{VALUE}} !important;',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'wpml_ba_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-before-lt,{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-after-lt,{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item .tp-wpml-before-lt,{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item .tp-wpml-after-lt',				
			]
		);	
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'wpml_ba_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-before-lt,{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-after-lt,{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item .tp-wpml-before-lt,{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item .tp-wpml-after-lt',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'wpml_ba_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-before-lt,{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-after-lt,{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item .tp-wpml-before-lt,{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item .tp-wpml-after-lt' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'wpml_ba_shadow',
				'selector' => '{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-before-lt,{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-after-lt,{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item .tp-wpml-before-lt,{{WRAPPER}} .header-extra-icons .tp-wpml-wrapper .tp-wpml-item .tp-wpml-after-lt',				
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
            'section_language_switcher_translatepress_styling',
            [
                'label' => esc_html__('Translatepress Language Switcher', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'display_language_switcher' => 'yes',
					'select_trans' => 'p_translatepress',
				],
            ]
        );
		$this->add_control(
			'transp_box_styling',
			[
				'label' => esc_html__( 'Box Styling', 'theplus' ),
				'type' => Controls_Manager::HEADING,
			]
		);
		$this->add_control(
			'transp_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-translatepress-wrapper .trp-language-switcher > div' => 'background-color: {{VALUE}} !important;',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'transp_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-translatepress-wrapper .trp-language-switcher > div',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'transp_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-translatepress-wrapper .trp-language-switcher > div' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'transp_shadow',
				'selector' => '{{WRAPPER}} .tp-translatepress-wrapper .trp-language-switcher > div',				
			]
		);
		$this->add_control(
			'transp_text_options',
			[
				'label' => esc_html__( 'Text Styling', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'transp_text_typography',
				'selector' => '{{WRAPPER}} .tp-translatepress-wrapper .trp-language-switcher > div a',
			]
		);
		$this->start_controls_tabs( 'tabs_transp_text_style' );
		$this->start_controls_tab(
			'tab_transp_text_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'transp_text_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-translatepress-wrapper .trp-language-switcher > div a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tp-translatepress-wrapper .trp-language-switcher > div' => 'background-image: linear-gradient(45deg, transparent 50%, {{VALUE}} 50%),linear-gradient(135deg, {{VALUE}} 50%, transparent 50%);',
				],
			]
		);
		$this->add_control(
			'transp_text_bg',
			[
				'label' => esc_html__( 'Background', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-translatepress-wrapper .trp-language-switcher > div a' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'transp_text_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-translatepress-wrapper .trp-language-switcher > div a',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'transp_text_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-translatepress-wrapper .trp-language-switcher > div a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_transp_text_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'transp_text_color_h',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-translatepress-wrapper .trp-language-switcher > div a:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'transp_text_bg_h',
			[
				'label' => esc_html__( 'Background', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-translatepress-wrapper .trp-language-switcher > div a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'transp_text_border_h',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-translatepress-wrapper .trp-language-switcher > div a:hover',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'transp_text_br_h',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-translatepress-wrapper .trp-language-switcher > div a:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'transp_image_size',
			[
				'label' => esc_html__( 'Image Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .tp-translatepress-wrapper .trp-language-switcher > div a .trp-flag-image' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'transp_image_height_size',
			[
				'label' => esc_html__( 'Image Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .tp-translatepress-wrapper .trp-language-switcher > div a .trp-flag-image' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'transp_bottom_space',
			[
				'label' => esc_html__( 'Bottom Space', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .tp-translatepress-wrapper .trp-ls-shortcode-language a:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'transp_ba_text_style_heading',
			[
				'label' => esc_html__( 'Before/After Text Style', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'transp_ba_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tp-translatepress-wrapper .tp-translatepress-before-lt,{{WRAPPER}} .tp-translatepress-wrapper .tp-translatepress-after-lt' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'transp_ba_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tp-translatepress-wrapper .tp-translatepress-before-lt,{{WRAPPER}} .tp-translatepress-wrapper .tp-translatepress-after-lt' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'transp_ba_typography',
				'selector' => '{{WRAPPER}} .tp-translatepress-wrapper .tp-translatepress-before-lt,{{WRAPPER}} .tp-translatepress-wrapper .tp-translatepress-after-lt',
			]
		);
		$this->add_control(
			'transp_ba_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-translatepress-wrapper .tp-translatepress-before-lt,{{WRAPPER}} .tp-translatepress-wrapper .tp-translatepress-after-lt' => 'color: {{VALUE}} !important;',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'transp_ba_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-translatepress-wrapper .tp-translatepress-before-lt,{{WRAPPER}} .tp-translatepress-wrapper .tp-translatepress-after-lt',				
			]
		);	
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'transp_ba_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-translatepress-wrapper .tp-translatepress-before-lt,{{WRAPPER}} .tp-translatepress-wrapper .tp-translatepress-after-lt',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'transp_ba_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-translatepress-wrapper .tp-translatepress-before-lt,{{WRAPPER}} .tp-translatepress-wrapper .tp-translatepress-after-lt' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'transp_ba_shadow',
				'selector' => '{{WRAPPER}} .tp-translatepress-wrapper .tp-translatepress-before-lt,{{WRAPPER}} .tp-translatepress-wrapper .tp-translatepress-after-lt',				
			]
		);
		$this->end_controls_section();		
		
		/*Language Switcher*/
		
		/*wpml language switcher*/		
		$this->start_controls_section(
            'section_wpml_lang_switch_options',
            [
                'label' => esc_html__('Language Switcher', 'theplus'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
		$this->add_control(
			'display_language_switcher',
			[
				'label' => esc_html__( 'Display Language Switcher', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'select_trans',
			[
				'label' => esc_html__( 'Select', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'p_wpml',
				'options' => [
					'p_wpml'  => esc_html__( 'WPML', 'theplus' ),
					'p_translatepress'  => esc_html__( 'Translatepress', 'theplus' ),
				],
				'condition'   => [
					'display_language_switcher' => 'yes',
				],
			]
		);
		$this->add_control(
			'wpml_style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'wpml_style_2',
				'options' => [					
					'wpml_style_2' => esc_html__( 'Style 1', 'theplus' ),
					'wpml_style_1'  => esc_html__( 'Style 2', 'theplus' ),
				],
				'condition'   => [
					'select_trans' => 'p_wpml',
					'display_language_switcher' => 'yes',
				],
			]
		);
		$this->add_control(
			'wpml_style_layout',
			[
				'label' => esc_html__( 'Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'tp-wpml-layout-v',
				'options' => [
					'tp-wpml-layout-v'  => esc_html__( 'Vertical', 'theplus' ),
					'tp-wpml-layout-h' => esc_html__( 'Horizontal', 'theplus' ),
				],
				'condition'   => [
					'select_trans' => 'p_wpml',
					'display_language_switcher' => 'yes',
					'wpml_style' => 'wpml_style_1',
				],
			]
		);
		$this->add_control(
			'skip_language',			[
				
				'label' => esc_html__( 'Skip Language', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 1,
				'default' => 0,
				'condition'   => [
					'select_trans' => 'p_wpml',
					'display_language_switcher' => 'yes',
					'wpml_style' => 'wpml_style_1',
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'display_country_flag',
			[
				'label' => esc_html__( 'Country Flag', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
				'condition'   => [
					'select_trans' => 'p_wpml',
					'display_language_switcher' => 'yes',
				],
			]
		);

		$this->add_control(
			'display_native_name',
			[
				'label' => esc_html__( 'Native Name', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
				'condition'   => [
					'select_trans' => 'p_wpml',
					'display_language_switcher' => 'yes',
				],
			]
		);

		$this->add_control(
			'display_translated_name',
			[
				'label' => esc_html__( 'Translated Name', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
				'condition'   => [
					'select_trans' => 'p_wpml',
					'display_language_switcher' => 'yes',
				],
			]
		);

		$this->add_control(
			'display_language_code',
			[
				'label' => esc_html__( 'Language Code', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
				'condition'   => [
					'select_trans' => 'p_wpml',
					'display_language_switcher' => 'yes',
					'wpml_style' => 'wpml_style_1',
				],
			]
		);
		$this->add_control(
			'before_language_text',
			[
				'label' => esc_html__( 'Before Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'condition'   => [
					'select_trans' => ['p_wpml','p_translatepress'],
					'display_language_switcher' => 'yes',
				],
			]
		);
		$this->add_control(
			'after_language_text',
			[
				'label' => esc_html__( 'After Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'condition'   => [
					'select_trans' => ['p_wpml','p_translatepress'],
					'display_language_switcher' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*wpml language switcher*/
		/*Music Bar*/
		$this->start_controls_section(
            'section_music_bar_options',
            [
                'label' => esc_html__('Music Options', 'theplus'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
		$this->add_control(
			'display_music_bar',
			[
				'label' => esc_html__( 'Display Music', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'music_icon_style',
			[
				'label' => esc_html__( 'Icon Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'style-2'  => esc_html__( 'Style 2', 'theplus' ),
				],
				'condition'   => [
					'display_music_bar' => 'yes',
				],
			]
		);
		$this->add_control(
			'music_audio_file',
			[
				'label' => esc_html__( 'Audio .Mp3/.Ogg', 'theplus' ),
				'type' => Controls_Manager::URL,
				'show_external' => false,
				'default' => [
					'url' => '',
				],
				'placeholder' => esc_html__( 'Paste Audio file .mp3/.ogg extension', 'theplus' ),
				'condition' => [					
					'display_music_bar' => 'yes',
				],
			]
		);
		$this->add_control(
			'music_volume',
			[
				'label' => esc_html__( 'Music Volume', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => 0,
						'max' => 100,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 50,
				],
				'condition' => [					
					'display_music_bar' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*Music bar*/
		/*Music bar Style*/
		$this->start_controls_section(
            'section_music_bar_styling',
            [
                'label' => esc_html__('Music Bar Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [					
					'display_music_bar' => 'yes',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'music_bar_typography',
				'selector' => '{{WRAPPER}} .header-extra-icons .header-music-bar .header-plus-music-toggle.style-2:before',
				'condition' => [					
					'music_icon_style' => 'style-2',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_music_bar_style' );
		$this->start_controls_tab(
			'tab_music_bar_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'music_bar_icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .header-music-bar .header-plus-music-toggle > div > span' => 'background: {{VALUE}};',
					'{{WRAPPER}} .header-extra-icons .header-music-bar .header-plus-music-toggle.style-2:before' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'music_bar_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .header-extra-icons .header-music-bar .header-plus-music-toggle',
				'condition' => [					
					'music_icon_style!' => 'style-2',
				],
			]
		);
		$this->add_control(
			'music_bar_border_style',
			[
				'label'   => esc_html__( 'Border Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'   => esc_html__( 'None', 'theplus' ),
					'solid'  => esc_html__( 'Solid', 'theplus' ),
					'dotted' => esc_html__( 'Dotted', 'theplus' ),
					'dashed' => esc_html__( 'Dashed', 'theplus' ),
					'groove' => esc_html__( 'Groove', 'theplus' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .header-extra-icons .header-music-bar .header-plus-music-toggle' => 'border-style: {{VALUE}};',
				],
				'separator' => 'before',
				'condition' => [					
					'music_icon_style!' => 'style-2',
				],
			]
		);

		$this->add_responsive_control(
			'music_bar_border_width',
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
					'{{WRAPPER}} .header-extra-icons .header-music-bar .header-plus-music-toggle' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'music_icon_style!' => 'style-2',
					'music_bar_border_style!' => 'none',
				],
			]
		);

		$this->add_control(
			'music_bar_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#313131',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .header-music-bar .header-plus-music-toggle' => 'border-color: {{VALUE}};',					
				],
				'condition' => [
					'music_icon_style!' => 'style-2',
					'music_bar_border_style!' => 'none'
				],
			]
		);

		$this->add_responsive_control(
			'music_bar_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .header-extra-icons .header-music-bar .header-plus-music-toggle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'music_icon_style!' => 'style-2',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'music_bar_shadow',
				'selector' => '{{WRAPPER}} .header-extra-icons .header-music-bar .header-plus-music-toggle',
				'condition' => [
					'music_icon_style!' => 'style-2',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_music_bar_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'music_bar_icon_hover_color',
			[
				'label' => esc_html__( 'Icon Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .header-music-bar .header-plus-music-toggle:hover > div > span' => 'background: {{VALUE}};',					
					'{{WRAPPER}} .header-extra-icons .header-music-bar .header-plus-music-toggle.style-2:hover:before' => 'color: {{VALUE}};',
				],
				'condition' => [
					'music_icon_style' => 'style-2',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'music_bar_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .header-extra-icons .header-music-bar .header-plus-music-toggle:hover',
				'condition' => [
					'music_icon_style!' => 'style-2',
				],
			]
		);
		$this->add_control(
			'music_bar_border_hover_color',
			[
				'label'     => esc_html__( 'Hover Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#313131',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .header-music-bar .header-plus-music-toggle:hover' => 'border-color: {{VALUE}};',					
				],
				'separator' => 'before',
				'condition' => [
					'music_icon_style!' => 'style-2',
					'button_1_border_style!' => 'none'
				],
			]
		);

		$this->add_responsive_control(
			'music_bar_hover_radius',
			[
				'label'      => esc_html__( 'Hover Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .header-extra-icons .header-music-bar .header-plus-music-toggle:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',					
				],
				'condition' => [
					'music_icon_style!' => 'style-2',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'music_bar_hover_shadow',
				'selector' => '{{WRAPPER}} .header-extra-icons .header-music-bar .header-plus-music-toggle:hover',
				'condition' => [
					'music_icon_style!' => 'style-2',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Music Bar Style*/
		
		/*Call to Action 1*/
		$this->start_controls_section(
            'section_call_to_action_1_options',
            [
                'label' => esc_html__('Call To Action 1', 'theplus'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
		$this->add_control(
			'display_call_to_action_1',
			[
				'label' => esc_html__( 'Display Call To Action', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'button_1_text',
			[
				'label' => esc_html__( 'Button Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Button 1', 'theplus' ),
				'condition' => [					
					'display_call_to_action_1' => 'yes',
				],
			]
		);
		$this->add_control(
			'button_1_link',
			[
				'label' => esc_html__( 'Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'default' => [
					'url' => '#',
				],
				'label_block' => false,
				'condition' => [					
					'display_call_to_action_1' => 'yes',
				],
			]
		);
		$this->add_control(
			'button_1_icon_style',
			[
				'label' => esc_html__( 'Icon Font', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'font_awesome',
				'options' => [					
					'font_awesome'  => esc_html__( 'Font Awesome', 'theplus' ),
					'font_awesome_5'  => esc_html__( 'Font Awesome 5', 'theplus' ),
					'icon_mind' => esc_html__( 'Icons Mind', 'theplus' ),
					'none'  => esc_html__( 'None', 'theplus' ),
				],
				'separator' => 'before',
				'condition' => [					
					'display_call_to_action_1' => 'yes',
				],
			]
		);
		$this->add_control(
			'button_1_icon',
			[
				'label' => esc_html__( 'Icon', 'theplus' ),
				'type' => Controls_Manager::ICON,
				'label_block' => false,
				'default' => 'fa fa-chevron-right',
				'condition' => [
					'display_call_to_action_1' => 'yes',
					'button_1_icon_style' => 'font_awesome',
				],
			]
		);
		$this->add_control(
			'button_1_icon_5',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-plus',
					'library' => 'solid',
				],
				'condition' => [
					'display_call_to_action_1' => 'yes',
					'button_1_icon_style' => 'font_awesome_5',
				],
			]
		);
		$this->add_control(
			'button_1_icons_mind',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'default' => '',
				'label_block' => true,
				'options' => theplus_icons_mind(),
				'condition' => [
					'display_call_to_action_1' => 'yes',
					'button_1_icon_style' => 'icon_mind',
				],
			]
		);
		$this->add_control(
			'button_1_before_after',
			[
				'label' => esc_html__( 'Icon Position', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'after',
				'options' => [
					'after' => esc_html__( 'After', 'theplus' ),
					'before' => esc_html__( 'Before', 'theplus' ),
				],
				'condition' => [
					'display_call_to_action_1' => 'yes',
					'button_1_icon_style!' => 'none',
				],
			]
		);
		$this->add_control(
			'button_1_icon_spacing',
			[
				'label' => esc_html__( 'Icon Spacing', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'condition' => [
					'display_call_to_action_1' => 'yes',
					'button_1_icon_style!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .call-to-action-1 .plus-action-button .btn-icon.button-after' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .header-extra-icons .call-to-action-1 .plus-action-button .btn-icon.button-before' => 'margin-right: {{SIZE}}{{UNIT}};',					
				],
			]
		);
		$this->add_control(
			'button_1_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 200,
					],
				],
				'condition' => [
					'display_call_to_action_1' => 'yes',
					'button_1_icon_style!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .call-to-action-1 .plus-action-button .btn-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .header-extra-icons .call-to-action-1 .plus-action-button .btn-icon svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'button_1_css_id',
			[
				'label' => esc_html__( 'Button ID', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'title' => esc_html__( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'theplus' ),
				'label_block' => false,
				'description' => esc_html__( 'Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'theplus' ),
				'separator' => 'before',
				'condition' => [
					'display_call_to_action_1' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*Call to Action 1*/
		/*Call to Action 1 Style*/
		$this->start_controls_section(
            'section_styling',
            [
                'label' => esc_html__('Call To Action 1', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [					
					'display_call_to_action_1' => 'yes',
				],
            ]
        );
		$this->add_responsive_control(
			'button_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
							'top' => '10',
							'right' => '15',
							'bottom' => '10',
							'left' => '15',
							'isLinked' => false 
				],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .call-to-action-1 .plus-action-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_1_typography',
				'selector' => '{{WRAPPER}} .header-extra-icons .call-to-action-1 .plus-action-button',
			]
		);
		$this->add_responsive_control(
			'button_1_svg_icon',
			[
				'label' => esc_html__( 'Svg Icon Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .call-to-action-1 .plus-action-button svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_button_1_style' );
		$this->start_controls_tab(
			'tab_button_1_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'button_1_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .call-to-action-1 .plus-action-button' => 'color: {{VALUE}};',					
					'{{WRAPPER}} .header-extra-icons .call-to-action-1 .plus-action-button svg' => 'fill: {{VALUE}};',					
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_1_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .header-extra-icons .call-to-action-1 .plus-action-button',
			]
		);
		$this->add_control(
			'button_1_border_style',
			[
				'label'   => esc_html__( 'Border Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'none'   => esc_html__( 'None', 'theplus' ),
					'solid'  => esc_html__( 'Solid', 'theplus' ),
					'dotted' => esc_html__( 'Dotted', 'theplus' ),
					'dashed' => esc_html__( 'Dashed', 'theplus' ),
					'groove' => esc_html__( 'Groove', 'theplus' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .header-extra-icons .call-to-action-1 .plus-action-button' => 'border-style: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'button_1_border_width',
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
					'{{WRAPPER}} .header-extra-icons .call-to-action-1 .plus-action-button' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'button_1_border_style!' => 'none',
				],
			]
		);

		$this->add_control(
			'button_1_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#313131',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .call-to-action-1 .plus-action-button' => 'border-color: {{VALUE}};',					
				],
				'condition' => [
					'button_1_border_style!' => 'none'
				],
			]
		);

		$this->add_responsive_control(
			'button_1_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .header-extra-icons .call-to-action-1 .plus-action-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_1_shadow',
				'selector' => '{{WRAPPER}} .header-extra-icons .call-to-action-1 .plus-action-button',				
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_1_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'button_1_text_hover_color',
			[
				'label' => esc_html__( 'Text Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .call-to-action-1 .plus-action-button:hover' => 'color: {{VALUE}};',					
					'{{WRAPPER}} .header-extra-icons .call-to-action-1 .plus-action-button:hover svg' => 'fill: {{VALUE}};',					
					
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_1_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .header-extra-icons .call-to-action-1 .plus-action-button:hover',
			]
		);
		$this->add_control(
			'button_1_border_hover_color',
			[
				'label'     => esc_html__( 'Hover Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#313131',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .call-to-action-1 .plus-action-button:hover' => 'border-color: {{VALUE}};',					
				],
				'separator' => 'before',
				'condition' => [					
					'button_1_border_style!' => 'none'
				],
			]
		);

		$this->add_responsive_control(
			'button_1_hover_radius',
			[
				'label'      => esc_html__( 'Hover Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .header-extra-icons .call-to-action-1 .plus-action-button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_1_hover_shadow',
				'selector' => '{{WRAPPER}} .header-extra-icons .call-to-action-1 .plus-action-button:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Call to Action 1 Style*/
		/*Call to Action 2*/
		$this->start_controls_section(
            'section_call_to_action_2_options',
            [
                'label' => esc_html__('Call To Action 2', 'theplus'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
		$this->add_control(
			'display_call_to_action_2',
			[
				'label' => esc_html__( 'Display Call To Action', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'button_2_text',
			[
				'label' => esc_html__( 'Button Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Button 2', 'theplus' ),
				'condition' => [					
					'display_call_to_action_2' => 'yes',
				],
			]
		);
		$this->add_control(
			'button_2_link',
			[
				'label' => esc_html__( 'Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'default' => [
					'url' => '#',
				],
				'label_block' => false,
				'condition' => [					
					'display_call_to_action_2' => 'yes',
				],
			]
		);
		$this->add_control(
			'button_2_icon_style',
			[
				'label' => esc_html__( 'Icon Font', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'font_awesome',
				'options' => [					
					'font_awesome'  => esc_html__( 'Font Awesome', 'theplus' ),
					'font_awesome_5'  => esc_html__( 'Font Awesome 5', 'theplus' ),
					'icon_mind' => esc_html__( 'Icons Mind', 'theplus' ),
					'none'  => esc_html__( 'None', 'theplus' ),
				],
				'separator' => 'before',
				'condition' => [					
					'display_call_to_action_2' => 'yes',
				],
			]
		);
		$this->add_control(
			'button_2_icon',
			[
				'label' => esc_html__( 'Icon', 'theplus' ),
				'type' => Controls_Manager::ICON,
				'label_block' => false,
				'default' => 'fa fa-chevron-right',
				'condition' => [
					'display_call_to_action_2' => 'yes',
					'button_2_icon_style' => 'font_awesome',
				],
			]
		);
		$this->add_control(
			'button_2_icon_5',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-plus',
					'library' => 'solid',
				],
				'condition' => [
					'display_call_to_action_2' => 'yes',
					'button_2_icon_style' => 'font_awesome_5',
				],
			]
		);
		$this->add_control(
			'button_2_icons_mind',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'default' => '',
				'label_block' => true,
				'options' => theplus_icons_mind(),
				'condition' => [
					'display_call_to_action_2' => 'yes',
					'button_2_icon_style' => 'icon_mind',
				],
			]
		);
		$this->add_control(
			'button_2_before_after',
			[
				'label' => esc_html__( 'Icon Position', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'after',
				'options' => [
					'after' => esc_html__( 'After', 'theplus' ),
					'before' => esc_html__( 'Before', 'theplus' ),
				],
				'condition' => [
					'display_call_to_action_2' => 'yes',
					'button_2_icon_style!' => 'none',
				],
			]
		);
		$this->add_control(
			'button_2_icon_spacing',
			[
				'label' => esc_html__( 'Icon Spacing', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'condition' => [
					'display_call_to_action_2' => 'yes',
					'button_2_icon_style!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .call-to-action-2 .plus-action-button .btn-icon.button-after' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .header-extra-icons .call-to-action-2 .plus-action-button .btn-icon.button-before' => 'margin-right: {{SIZE}}{{UNIT}};',					
				],
			]
		);
		$this->add_control(
			'button_2_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 200,
					],
				],
				'condition' => [
					'display_call_to_action_2' => 'yes',
					'button_2_icon_style!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .call-to-action-2 .plus-action-button .btn-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .header-extra-icons .call-to-action-2 .plus-action-button .btn-icon svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'button_2_css_id',
			[
				'label' => esc_html__( 'Button ID', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'title' => esc_html__( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'theplus' ),
				'label_block' => false,
				'description' => esc_html__( 'Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'theplus' ),
				'separator' => 'before',
				'condition' => [
					'display_call_to_action_2' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*Call to Action 2*/
		/*Call to Action 2 Style*/
		$this->start_controls_section(
            'section_button_2_styling',
            [
                'label' => esc_html__('Call To Action 2', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'display_call_to_action_2' => 'yes',
				],
            ]
        );
		$this->add_responsive_control(
			'button_2_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
							'top' => '10',
							'right' => '20',
							'bottom' => '10',
							'left' => '20',
							'isLinked' => false 
				],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .call-to-action-2 .plus-action-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_2_typography',
				'selector' => '{{WRAPPER}} .header-extra-icons .call-to-action-2 .plus-action-button',
			]
		);
		$this->add_responsive_control(
			'button_2_svg_icon',
			[
				'label' => esc_html__( 'Svg Icon Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .call-to-action-2 .plus-action-button svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_button_2_style' );
		$this->start_controls_tab(
			'tab_button_2_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'button_2_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .call-to-action-2 .plus-action-button' => 'color: {{VALUE}};',					
					'{{WRAPPER}} .header-extra-icons .call-to-action-2 .plus-action-button svg' => 'fill: {{VALUE}};',					
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_2_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .header-extra-icons .call-to-action-2 .plus-action-button',
			]
		);
		$this->add_control(
			'button_2_border_style',
			[
				'label'   => esc_html__( 'Border Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'none'   => esc_html__( 'None', 'theplus' ),
					'solid'  => esc_html__( 'Solid', 'theplus' ),
					'dotted' => esc_html__( 'Dotted', 'theplus' ),
					'dashed' => esc_html__( 'Dashed', 'theplus' ),
					'groove' => esc_html__( 'Groove', 'theplus' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .header-extra-icons .call-to-action-2 .plus-action-button' => 'border-style: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'button_2_border_width',
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
					'{{WRAPPER}} .header-extra-icons .call-to-action-2 .plus-action-button' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'button_2_border_style!' => 'none',
				],
			]
		);

		$this->add_control(
			'button_2_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#313131',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .call-to-action-2 .plus-action-button' => 'border-color: {{VALUE}};',					
				],
				'condition' => [
					'button_2_border_style!' => 'none'
				],
			]
		);

		$this->add_responsive_control(
			'button_2_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .header-extra-icons .call-to-action-2 .plus-action-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_2_shadow',
				'selector' => '{{WRAPPER}} .header-extra-icons .call-to-action-2 .plus-action-button',				
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_2_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'button_2_text_hover_color',
			[
				'label' => esc_html__( 'Text Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .call-to-action-2 .plus-action-button:hover' => 'color: {{VALUE}};',					
					'{{WRAPPER}} .header-extra-icons .call-to-action-2 .plus-action-button:hover svg' => 'fill: {{VALUE}};',					
					
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_2_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .header-extra-icons .call-to-action-2 .plus-action-button:hover',
			]
		);
		$this->add_control(
			'button_2_border_hover_color',
			[
				'label'     => esc_html__( 'Hover Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#313131',
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons .call-to-action-2 .plus-action-button:hover' => 'border-color: {{VALUE}};',					
				],
				'separator' => 'before',
				'condition' => [					
					'button_2_border_style!' => 'none'
				],
			]
		);

		$this->add_responsive_control(
			'button_2_hover_radius',
			[
				'label'      => esc_html__( 'Hover Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .header-extra-icons .call-to-action-2 .plus-action-button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_2_hover_shadow',
				'selector' => '{{WRAPPER}} .header-extra-icons .call-to-action-2 .plus-action-button:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Call to Action 2 Style*/
		/* Extra Options*/
		$this->start_controls_section(
            'section_extra_options',
            [
                'label' => esc_html__('Extra Options', 'theplus'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
		$this->add_control(
			'icon_alignment',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-left' => [
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
				'default' => 'flex-start',
				'toggle' => true,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons ul.icons-content-list' => ' -webkit-justify-content: {{VALUE}};-moz-justify-content: {{VALUE}};-ms-justify-content: {{VALUE}};justify-content: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'icon_between_padding',
			[
				'label' => esc_html__( 'Icon Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px','em' ],
				'selectors' => [
					'{{WRAPPER}} .header-extra-icons ul.icons-content-list > li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
		/* Extra Options*/
		
		/*extra option*/
		$this->start_controls_section(
            'section_extra_option',
            [
                'label' => esc_html__('Extra Option', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_control(
			'sticky_options',
			[
				'label' => esc_html__( 'Sticky Options', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',				
			]
		);
		$this->start_controls_tabs( 'tabs_sticky_style' , [
			'condition' => [
				'sticky_options' => 'yes',
			],
		]);
		$this->start_controls_tab(
			'tab_sticky_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [					
					'sticky_options' => 'yes',
				],
			]
		);
		$this->add_control(
			'sticky_search_heading',
			[
				'label' => esc_html__( 'Search Option', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'condition' => [					
					'sticky_options' => 'yes',
					'display_search_bar' => 'yes',
				],
			]
		);
		$this->add_control(
			's_search_icon_color',
			[
				'label' => esc_html__( 'Search Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras  .header-extra-icons li.search-icon .plus-post-search-icon svg,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons li.search-icon .plus-post-search-icon svg path' => 'fill: {{VALUE}} !important;stroke: {{VALUE}} !important',
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras  .header-extra-icons .icons-content-list .search-icon .plus-post-search-icon i' => 'color: {{VALUE}} !important;',
				],
				'condition' => [					
					'sticky_options' => 'yes',
					'display_search_bar' => 'yes',
				],
			]
		);
		$this->add_control(
			'sticky_cart_heading',
			[
				'label' => esc_html__( 'Cart Option', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [					
					'sticky_options' => 'yes',
					'display_mini_cart' => 'yes',
				],
			]
		);
		$this->add_control(
			's_cart_icon_color',
			[
				'label' => esc_html__( 'Cart Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons li.mini-cart-icon .plus-cart-icon.style-1 svg,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons li.mini-cart-icon .plus-cart-icon.style-1 svg path,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons li.mini-cart-icon .plus-cart-icon.style-2 svg,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons li.mini-cart-icon .plus-cart-icon.style-2 svg path' => 'fill: {{VALUE}} !important',
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .mini-cart-icon .cart_custom_icon i' => 'color: {{VALUE}} !important',
				],
				'condition' => [					
					'sticky_options' => 'yes',
					'display_mini_cart' => 'yes',
				],
			]
		);
		$this->add_control(
			's_cart_count_color',
			[
				'label' => esc_html__( 'Count Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons li.mini-cart-icon .plus-cart-icon .cart-wrap span' => 'color: {{VALUE}} !important',
				],
				'condition' => [					
					'sticky_options' => 'yes',
					'display_mini_cart' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 's_cart_count_background',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons li.mini-cart-icon .plus-cart-icon .cart-wrap span',
				'condition' => [					
					'sticky_options' => 'yes',
					'display_mini_cart' => 'yes',
				],
			]
		);
		$this->add_control(
			'sticky_extra_toggle_heading',
			[
				'label' => esc_html__( 'Extra Toggle Option', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',				
				'condition' => [					
					'sticky_options' => 'yes',
					'display_extra_toggle_bar' => 'yes',
				],
			]
		);
		$this->add_control(
			's_extra_toggle_icon_color',
			[
				'label' => esc_html__( 'Toggle Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .extra-toggle-icon .header-extra-toggle-click.style-1 .menu_line,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-toggle-click.style-2 .tp-menu-st2,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-toggle-click.style-2 .tp-menu-st2::before, .plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-toggle-click.style-2 .tp-menu-st2::after,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-toggle-click.style-2 .tp-menu-st2-h, .plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-toggle-click.style-2 .tp-menu-st2-h::before,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-toggle-click.style-2 .tp-menu-st2-h::after,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-toggle-click.style-3 .tp-menu-st3,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-toggle-click.style-3 .tp-menu-st3::before, .plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-toggle-click.style-3 .tp-menu-st3::after,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-toggle-click.style-4 span' => 'background: {{VALUE}} !important',
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .extra-toggle-icon .et_icon_img_st5 i' => 'color: {{VALUE}} !important',
				],
				'condition' => [					
					'sticky_options' => 'yes',
					'display_extra_toggle_bar' => 'yes',
				],
			]
		);
		$this->add_control(
			'sticky_music_bar_heading',
			[
				'label' => esc_html__( 'Music Bar Option', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',				
				'condition' => [					
					'sticky_options' => 'yes',
					'display_music_bar' => 'yes',
				],
			]
		);
		$this->add_control(
			's_music_bar_icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .header-music-bar .header-plus-music-toggle > div > span' => 'background: {{VALUE}} !important;',
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .header-music-bar .header-plus-music-toggle.style-2:before' => 'color: {{VALUE}} !important;',
				],
				'condition' => [					
					'sticky_options' => 'yes',
					'display_music_bar' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 's_music_bar_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .header-music-bar .header-plus-music-toggle',
				'condition' => [					
					'sticky_options' => 'yes',
					'display_music_bar' => 'yes',
				],
			]
		);
		$this->add_control(
			'sticky_cta1_heading',
			[
				'label' => esc_html__( 'Call to Action 1 Option', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',				
				'condition' => [					
					'sticky_options' => 'yes',
					'display_call_to_action_1' => 'yes',
				],
			]
		);
		$this->add_control(
			's_button_1_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .call-to-action-1 .plus-action-button' => 'color: {{VALUE}} !important;',					
				],
				'condition' => [					
					'sticky_options' => 'yes',
					'display_call_to_action_1' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 's_button_1_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .call-to-action-1 .plus-action-button',
				'condition' => [					
					'sticky_options' => 'yes',
					'display_call_to_action_1' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 's_button_1_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .call-to-action-1 .plus-action-button',
				'condition' => [					
					'sticky_options' => 'yes',
					'display_call_to_action_1' => 'yes',
				],	
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			's_button_1_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .call-to-action-1 .plus-action-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'condition' => [					
					'sticky_options' => 'yes',
					'display_call_to_action_1' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 's_button_1_shadow',
				'selector' => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .call-to-action-1 .plus-action-button',				
				'condition' => [					
					'sticky_options' => 'yes',
					'display_call_to_action_1' => 'yes',
				],
			]
		);
		$this->add_control(
			'sticky_cta2_heading',
			[
				'label' => esc_html__( 'Call to Action 2 Option', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',				
				'condition' => [					
					'sticky_options' => 'yes',
					'display_call_to_action_2' => 'yes',
				],
			]
		);
		$this->add_control(
			's_button_2_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .call-to-action-2 .plus-action-button' => 'color: {{VALUE}} !important;',					
				],
				'condition' => [					
					'sticky_options' => 'yes',
					'display_call_to_action_2' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 's_button_2_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .call-to-action-2 .plus-action-button',
				'condition' => [					
					'sticky_options' => 'yes',
					'display_call_to_action_2' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 's_search_field_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .call-to-action-2 .plus-action-button',
				'condition' => [					
					'sticky_options' => 'yes',
					'display_call_to_action_2' => 'yes',
				],	
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			's_button_2_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .call-to-action-2 .plus-action-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'condition' => [					
					'sticky_options' => 'yes',
					'display_call_to_action_2' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 's_button_2_shadow',
				'selector' => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .call-to-action-2 .plus-action-button',				
				'condition' => [					
					'sticky_options' => 'yes',
					'display_call_to_action_2' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_sticky_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),				
				'condition' => [					
					'sticky_options' => 'yes',
				],
			]
		);
		$this->add_control(
			'sticky_search_heading_h',
			[
				'label' => esc_html__( 'Search Option', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'condition' => [					
					'sticky_options' => 'yes',
					'display_search_bar' => 'yes',
				],
			]
		);
		$this->add_control(
			's_search_icon_color_hover',
			[
				'label' => esc_html__( 'Search Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ff5a6e',
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons li.search-icon .plus-post-search-icon:hover svg,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons li.search-icon .plus-post-search-icon:hover svg path' => 'fill: {{VALUE}} !important;stroke: {{VALUE}} !important',
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .icons-content-list .search-icon .plus-post-search-icon:hover i' => 'color: {{VALUE}} !important;',
				],
				'condition' => [					
					'sticky_options' => 'yes',
					'display_search_bar' => 'yes',
				],
			]
		);
		$this->add_control(
			'sticky_cart_heading_h',
			[
				'label' => esc_html__( 'Cart Option', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',				
				'condition' => [					
					'sticky_options' => 'yes',
					'display_mini_cart' => 'yes',
				],
			]
		);
		$this->add_control(
			's_cart_icon_color_hover',
			[
				'label' => esc_html__( 'Cart Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons li.mini-cart-icon .plus-cart-icon.style-1:hover svg,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons li.mini-cart-icon .plus-cart-icon.style-1:hover svg path,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons li.mini-cart-icon .plus-cart-icon.style-2:hover svg,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons li.mini-cart-icon .plus-cart-icon.style-2:hover svg path' => 'fill: {{VALUE}} !important',
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .mini-cart-icon .plus-cart-icon.cart_custom_icon:hover i' => 'color: {{VALUE}} !important',
				],
				'condition' => [					
					'sticky_options' => 'yes',
					'display_mini_cart' => 'yes',
				],
			]
		);
		$this->add_control(
			's_cart_count_color_hover',
			[
				'label' => esc_html__( 'Count Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons li.mini-cart-icon .plus-cart-icon:hover .cart-wrap span' => 'color: {{VALUE}} !important',
				],
				'condition' => [					
					'sticky_options' => 'yes',
					'display_mini_cart' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 's_cart_count_background_hover',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons li.mini-cart-icon .plus-cart-icon:hover .cart-wrap span',
				'condition' => [					
					'sticky_options' => 'yes',
					'display_mini_cart' => 'yes',
				],
			]
		);
		$this->add_control(
			'sticky_extra_toggle_heading_h',
			[
				'label' => esc_html__( 'Extra Toggle Option', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',				
				'condition' => [					
					'sticky_options' => 'yes',
					'display_extra_toggle_bar' => 'yes',
				],
			]
		);
		$this->add_control(
			's_extra_toggle_icon_color_hover',
			[
				'label' => esc_html__( 'Toggle Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .extra-toggle-icon .header-extra-toggle-click.style-1:hover .menu_line,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-toggle-click.style-2:hover .tp-menu-st2-h,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-toggle-click.style-2:hover .tp-menu-st2-h::before, .plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-toggle-click.style-2:hover .tp-menu-st2-h::after,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .extra-toggle-icon .header-extra-toggle-click.style-3.open .tp-menu-st3::before,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .extra-toggle-icon .header-extra-toggle-click.style-3.open .tp-menu-st3::after,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras  .extra-toggle-icon .header-extra-toggle-click.style-4.open span:nth-last-child(3),.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras  .extra-toggle-icon .header-extra-toggle-click.style-4.open span:nth-last-child(1)' => 'background: {{VALUE}} !important',
				],
				'condition' => [					
					'sticky_options' => 'yes',
					'display_extra_toggle_bar' => 'yes',
				],
			]
		);
		$this->add_control(
			's_sticky_music_bar_heading_h',
			[
				'label' => esc_html__( 'Music Bar Option', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [					
					'sticky_options' => 'yes',
					'display_music_bar' => 'yes',
				],
			]
		);
		$this->add_control(
			's_music_bar_icon_hover_color',
			[
				'label' => esc_html__( 'Icon Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .header-music-bar .header-plus-music-toggle:hover > div > span' => 'background: {{VALUE}} !important;',					
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .header-music-bar .header-plus-music-toggle.style-2:hover:before' => 'color: {{VALUE}} !important;',
				],
				'condition' => [					
					'sticky_options' => 'yes',
					'display_music_bar' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 's_music_bar_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .header-music-bar .header-plus-music-toggle:hover',
				'condition' => [					
					'sticky_options' => 'yes',
					'display_music_bar' => 'yes',
				],
			]
		);
		$this->add_control(
			'sticky_cta1_heading_h',
			[
				'label' => esc_html__( 'Call to Action 1 Option', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',				
				'condition' => [					
					'sticky_options' => 'yes',
					'display_call_to_action_1' => 'yes',
				],
			]
		);
		$this->add_control(
		's_button_1_text_hover_color',
			[
				'label' => esc_html__( 'Text Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .call-to-action-1 .plus-action-button:hover' => 'color: {{VALUE}} !important;',					
				],
				'condition' => [					
					'sticky_options' => 'yes',
					'display_call_to_action_1' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 's_button_1_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .call-to-action-1 .plus-action-button:hover',
				'condition' => [					
					'sticky_options' => 'yes',
					'display_call_to_action_1' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 's_button_1_hover_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .call-to-action-1 .plus-action-button:hover',
				'condition' => [					
					'sticky_options' => 'yes',
					'display_call_to_action_1' => 'yes',
				],	
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			's_button_1_hover_radius',
			[
				'label'      => esc_html__( 'Hover Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .call-to-action-1 .plus-action-button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'condition' => [					
					'sticky_options' => 'yes',
					'display_call_to_action_1' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 's_button_1_hover_shadow',
				'selector' => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .call-to-action-1 .plus-action-button:hover',
				'condition' => [					
					'sticky_options' => 'yes',
					'display_call_to_action_1' => 'yes',
				],
			]
		);
		$this->add_control(
			'sticky_cta2_heading_h',
			[
				'label' => esc_html__( 'Call to Action 2 Option', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [					
					'sticky_options' => 'yes',
					'display_call_to_action_2' => 'yes',
				],
			]
		);
		$this->add_control(
			's_button_2_text_hover_color',
			[
				'label' => esc_html__( 'Text Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .call-to-action-2 .plus-action-button:hover' => 'color: {{VALUE}} !important;',										
				],
				'condition' => [					
					'sticky_options' => 'yes',
					'display_call_to_action_2' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 's_button_2_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .call-to-action-2 .plus-action-button:hover',
				'condition' => [					
					'sticky_options' => 'yes',
					'display_call_to_action_2' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 's_button_2_border_h',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .call-to-action-2 .plus-action-button:hover',					
				'separator' => 'before',
				'condition' => [					
					'sticky_options' => 'yes',
					'display_call_to_action_2' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			's_button_2_hover_radius',
			[
				'label'      => esc_html__( 'Hover Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .call-to-action-2 .plus-action-button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'condition' => [					
					'sticky_options' => 'yes',
					'display_call_to_action_2' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 's_button_2_hover_shadow',
				'selector' => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-header-extras .header-extra-icons .call-to-action-2 .plus-action-button:hover',
				'condition' => [					
					'sticky_options' => 'yes',
					'display_call_to_action_2' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/* Extra Options*/
	}
	private function get_shortcode() {
		$settings = $this->get_settings_for_display();	
		
		$flags=$native=$translated='';
		if(!empty($settings['display_country_flag']) && $settings['display_country_flag']=='yes'){
			$flags =' flags=1';
		}else{
			$flags =' flags=0';
		}
		if(!empty($settings['display_native_name']) && $settings['display_native_name']=='yes'){
			$native=' native=1';
		}else{
			$native=' native=0';
		}
		if(!empty($settings['display_translated_name']) && $settings['display_translated_name']=='yes'){
			$translated=' translated=1';
		}else{
			$translated=' translated=0';
		}
		
		$shortcode   = [];
		$shortcode[] = sprintf( '[wpml_language_switcher type="widget" '.$flags.' '.$native.' '.$translated.'][/wpml_language_switcher]');

		return implode("", $shortcode);
	}
	private function translatepress_get_shortcode() {
		$settings = $this->get_settings_for_display();
		
		$shortcode   = [];
		$shortcode[] = sprintf( '[language-switcher]');
		
		return implode("", $shortcode);
		
	}

	 protected function render() {

        $settings = $this->get_settings_for_display();		
		$search_icon_style = (!empty($settings["search_icon_style"]) ? $settings["search_icon_style"] : "style-1");
		$cart_icon = (!empty($settings["cart_icon"]) ? $settings["cart_icon"] : "default");
		$search_bar_content_style = (!empty($settings["search_bar_content_style"]) ? $settings["search_bar_content_style"] : "style-1");
		$search_text = (!empty($settings["search_placeholder_text"]) ? $settings["search_placeholder_text"] : '');
		$widget_uid = $this->get_id();
		
		if($settings['display_search_bar'] == 'yes' && $search_icon_style == 'style-custom-icon'){
			ob_start();
				\Elementor\Icons_Manager::render_icon( $settings['search_custom_icon'], [ 'aria-hidden' => 'true' ]);
				$search_custom_icon = ob_get_contents();
			ob_end_clean();
		}
		if($settings['display_mini_cart'] == 'yes' && $cart_icon == 'cart_custom_icon'){
			ob_start();
				\Elementor\Icons_Manager::render_icon( $settings['cart_icon_icon'], [ 'aria-hidden' => 'true' ]);
				$cart_custom_icon = ob_get_contents();
			ob_end_clean();
		}
		if($settings['display_extra_toggle_bar'] == 'yes' && $settings['extra_toggle_style'] == 'style-5' && $settings['extra_toggle_style_custom'] == 'custom_icon'){
			ob_start();
				\Elementor\Icons_Manager::render_icon( $settings['extra_toggle_custom_icon'], [ 'aria-hidden' => 'true' ]);
				$extra_toggle_custom_icon = ob_get_contents();
			ob_end_clean();						
		}		
		
		$search_custom_image = !empty($settings['search_custom_image']['id']) ? $settings['search_custom_image']['id'] :'';
		$img = wp_get_attachment_image_src($search_custom_image,$settings['sci_thumbnail_size']);
		if(!empty($img[0])){
			$search_custom_image = $img[0];
		}else{
			$search_custom_image= \Elementor\Utils::get_placeholder_image_src();
		}
		
		$cart_icon_custom_image = !empty($settings['cart_icon_custom_image']['id']) ? $settings['cart_icon_custom_image']['id'] : '';
		$img = wp_get_attachment_image_src($cart_icon_custom_image,$settings['cici_thumbnail_size']);
		if(!empty($img[0])){
			$cart_custom_image = $img[0];
		}else{
			$cart_custom_image = \Elementor\Utils::get_placeholder_image_src();
		}
		
		$extra_toggle_custom_image = !empty($settings['extra_toggle_custom_image']['id']) ? $settings['extra_toggle_custom_image']['id'] : '';
		$img = wp_get_attachment_image_src($extra_toggle_custom_image,$settings['etci_thumbnail_size']);
		if(!empty($img[0])){
			$etst5_custom_image = $img[0];
		}else{
			$etst5_custom_image = \Elementor\Utils::get_placeholder_image_src();
		}
		
		$meta_content ='<div class="header-extra-icons">';
			$meta_content .='<div class="header-icons-inner">';
				if(!empty($settings['sequence_icons'])){
					$meta_content .='<ul class="icons-content-list">';					
						foreach ( $settings['sequence_icons'] as $index => $item ) :
							$select_icon_list = $item["select_icon_list"];
							$responsive_class_attr='';
							if($item["responsive_hidden_desktop"] == 'yes'){
								$responsive_class_attr .= ' header-extra-icons-hidden-desktop';
							}
							if($item["responsive_hidden_tablet"] == 'yes'){
								$responsive_class_attr .= ' header-extra-icons-hidden-tablet';
							}
							if($item["responsive_hidden_mobile"] == 'yes'){
								$responsive_class_attr .= ' header-extra-icons-hidden-mobile';
							}

							if($settings["display_search_bar"] == 'yes' && $select_icon_list == 'search'){
								$meta_content .='<li class="search-icon elementor-repeater-item-' . esc_attr($item['_id']) . ' '.esc_attr($responsive_class_attr).'">';
									$meta_content .='<div class="content-icon-list">';
										$meta_content .='<div class="plus-post-search-icon '.esc_attr($search_icon_style).'">';
											if($search_icon_style == 'style-1'){
												$meta_content .= '<?xml version="1.0" encoding="UTF-8"?><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 50 50" version="1.1" width="100px" height="100px"><g id="surface1"><path style=" " d="M 21 3 C 11.621094 3 4 10.621094 4 20 C 4 29.378906 11.621094 37 21 37 C 24.710938 37 28.140625 35.804688 30.9375 33.78125 L 44.09375 46.90625 L 46.90625 44.09375 L 33.90625 31.0625 C 36.460938 28.085938 38 24.222656 38 20 C 38 10.621094 30.378906 3 21 3 Z M 21 5 C 29.296875 5 36 11.703125 36 20 C 36 28.296875 29.296875 35 21 35 C 12.703125 35 6 28.296875 6 20 C 6 11.703125 12.703125 5 21 5 Z "/></g></svg>';
											}else if($search_icon_style=='style-custom-icon'){
												$meta_content .= $search_custom_icon;
											}else if($search_icon_style=='style-custom-image'){												
												if(!empty($search_custom_image)){
													$search_custom_img = $search_custom_image;
												}else{
													$search_custom_img = '';
												}
													$meta_content .='<img class="tp-icon-img" src='.esc_url($search_custom_img).' />';
											}
										$meta_content .='</div>';
											$st3_4_search_op='';
											if($search_bar_content_style == 'style-3' || $search_bar_content_style == 'style-4'){
												$st3_4_search_op = (!empty($settings['search_bar_open_content_style']) ? $settings['search_bar_open_content_style'] : 'sboc_left');
											}
											$meta_content .='<div class="plus-search-form plus-search-form-content '.esc_attr($search_bar_content_style).' '.esc_attr($st3_4_search_op).'" data-style="'.esc_attr($search_bar_content_style).'">';
												$meta_content .='<div class="plus-search-close"><div class="search-close"></div></div>';
												$meta_content .='<div class="plus-search-section">';
												$meta_content .='<form action="'.esc_url(home_url()).'" method="get">';
													$meta_content .='<input type="text" class="plus-search-field" placeholder="'.esc_attr($search_text).'" name="s" autocomplete="off">';
													$meta_content .='<div class="plus-submit-icon-container">
																		<button type="submit" class="plus-search-submit"">
																		<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 50 50" version="1.1" width="100px" height="100px"><g id="surface1"><path style=" " d="M 21 3 C 11.621094 3 4 10.621094 4 20 C 4 29.378906 11.621094 37 21 37 C 24.710938 37 28.140625 35.804688 30.9375 33.78125 L 44.09375 46.90625 L 46.90625 44.09375 L 33.90625 31.0625 C 36.460938 28.085938 38 24.222656 38 20 C 38 10.621094 30.378906 3 21 3 Z M 21 5 C 29.296875 5 36 11.703125 36 20 C 36 28.296875 29.296875 35 21 35 C 12.703125 35 6 28.296875 6 20 C 6 11.703125 12.703125 5 21 5 Z "></path></g></svg></button>
																	</div>';													
												$meta_content .='</form>';
												$meta_content .='</div>';
												
											$meta_content .='</div>';
											
									$meta_content .='</div>';
								$meta_content .='</li>';								
							}
							if($settings["display_extra_toggle_bar"] == 'yes' && $select_icon_list == 'extra_toggle'){
								$open_direction = (!empty($settings["extra_toggle_bar_direction"]) ? $settings["extra_toggle_bar_direction"] : 'right');
								$fullwidth_content = ($settings['extra_content_width_option'] == 'fullwidth') ? 'full-width-content' : '';
								$meta_content .='<li class="extra-toggle-icon elementor-repeater-item-' . esc_attr($item['_id']) . ' '.esc_attr($responsive_class_attr).'">';
									$meta_content .='<div class="content-icon-list">';
										$meta_content .='<div class="header-extra-toggle-click '.esc_attr($settings['extra_toggle_style']).'">';
											if($settings["extra_toggle_style"] == 'style-1'){
												$meta_content .='<span class="menu_line menu_line--top"></span>
																	<span class="menu_line menu_line--center"></span>
																	<span class="menu_line menu_line--bottom"></span>';											
											}else if($settings["extra_toggle_style"]=='style-2'){
												$meta_content .='<div class="tp-menu-st2"></div><div class="tp-menu-st2-h"></div>';
											}else if($settings["extra_toggle_style"]=='style-3'){
												$meta_content .='<div class="tp-menu-st3"></div>';
											}else if($settings["extra_toggle_style"]=='style-4'){
												$meta_content .='<span></span><span></span><span></span>';
											}else if($settings["extra_toggle_style"]=='style-5'){												
												if($settings['extra_toggle_style_custom']=='custom_icon' && !empty($extra_toggle_custom_icon)){
													$meta_content .='<span class="extra_toggle_open et_icon_img_st5">'.$extra_toggle_custom_icon.'</span>';
												}else if($settings['extra_toggle_style_custom']=='custom_img'){
													if(!empty($etst5_custom_image)){
														$extra_toggle_custom_img = $etst5_custom_image;														
													}else{
														$extra_toggle_custom_img = '';														
													}
													$meta_content .='<img class="tp-icon-img" src='.esc_url($extra_toggle_custom_img).' />';
												}
											}
										$meta_content .='</div>';
										if(!empty($settings['extra_content_template'])){
											$meta_content .='<div class="header-extra-toggle-content '.esc_attr($fullwidth_content).' '.esc_attr($open_direction).'">';
												$meta_content .='<div class="extra-toggle-close-menu"></div>';
												$meta_content .= Theplus_Element_Load::elementor()->frontend->get_builder_content_for_display( $settings['extra_content_template'] );
											$meta_content .='</div>';
											$meta_content .='<div class="extra-toggle-content-overlay"></div>';
										}
									$meta_content .='</div>';
								$meta_content .='</li>';
							}
							/*wpml language switcher start*/						
							if((!empty($settings["display_language_switcher"]) && $settings["display_language_switcher"] == 'yes') && $select_icon_list == 'wpml_lang'){
								if(!empty($settings["select_trans"]) && $settings["select_trans"] == 'p_wpml'){
									$wpml_style = !empty($settings['wpml_style']) ? $settings['wpml_style'] : 'wpml_style_2';
									$wpml_style_layout = !empty($settings['wpml_style_layout']) ? $settings['wpml_style_layout'] : 'tp-wpml-layout-v';
									$skip_language = !empty($settings['skip_language']) ? $settings['skip_language'] : 0;
									$display_country_flag = !empty($settings['display_country_flag']) ? $settings['display_country_flag'] : '';
									$display_native_name = !empty($settings['display_native_name']) ? $settings['display_native_name'] : '';
									$display_translated_name = !empty($settings['display_translated_name']) ? $settings['display_translated_name'] : '';
									$display_language_code = !empty($settings['display_language_code']) ? $settings['display_language_code'] : '';
									$before_language_text = !empty($settings['before_language_text']) ? $settings['before_language_text'] : '';
									$after_language_text = !empty($settings['after_language_text']) ? $settings['after_language_text'] : '';
									
									$wpml = apply_filters( 'wpml_active_languages', NULL, array(
										'skip_language' => $skip_language,
									) );
									if( !empty( $wpml ) ) { 									
											$meta_content .= '<li class="tp-wpml-wrapper elementor-repeater-item-' . esc_attr($item['_id']) . ' '.esc_attr($responsive_class_attr).'">';
												
												if(!empty($settings['wpml_style']) && $settings['wpml_style'] == 'wpml_style_1'){
													$wpml_layout='';
													if(!empty($wpml_style_layout) && $wpml_style_layout == 'tp-wpml-layout-v'){
														$wpml_layout = 'tp-wpml-layout-v';
													}else if(!empty($wpml_style_layout) && $wpml_style_layout == 'tp-wpml-layout-h'){
														$wpml_layout = 'tp-wpml-layout-h';
													}

													$meta_content .= '<ul class="tp-wpml-menu '.esc_attr($wpml_layout).'">';
													foreach( $wpml as $language ){
														$meta_content .=  '<li class="tp-wpml-menu-item">';
															
															$meta_content .=  ( $language['active'] ) ? '<a href="' . esc_url($language['url']) . '" class="tp-wpml-item tp-wpml-item__active">' : '<a href="' . esc_url($language['url']) . '" class="tp-wpml-item">';
															
																if($before_language_text){
																	$meta_content .= '<span class="tp-wpml-before-lt">' .esc_html($before_language_text).'</span>';	
																}
																
																if($display_country_flag){
																	$meta_content .= '<span class="tp-wpml-country-flag"><img src="' . esc_url($language['country_flag_url']) . '" alt="'.esc_attr($language['language_code']).'" /></span>';
																}
																
																if($display_native_name){
																	$meta_content .= '<span class="tp-wpml-native-name">'.esc_html($language['native_name']).'</span>';
																}
																
																if($display_translated_name){
																	$meta_content .= '<span class="tp-wpml-translated-name">'.esc_html($language['translated_name']).'</span>';	
																}
																if($display_language_code){
																	$meta_content .=  '<span class="tp-wpml-language-code">'.esc_html($language['language_code']).'</span>';
																}
																if($after_language_text){
																	$meta_content .=  '<span class="tp-wpml-after-lt">'.esc_html($after_language_text).'</span>';
																}
																
															$meta_content .=  '</a>';
															
														$meta_content .=  '</li>';
													}
													$meta_content .=  '</ul>';
													
												}else if(!empty($settings['wpml_style']) && $settings['wpml_style'] == 'wpml_style_2'){
													if($before_language_text){
														$meta_content .=  '<span class="tp-wpml-before-lt">'.esc_html($before_language_text).'</span>';
													}
													$meta_content .= do_shortcode( $this->get_shortcode() );
													if($after_language_text){
														$meta_content .=  '<span class="tp-wpml-after-lt">'.esc_html($after_language_text).'</span>';
													}
												}												
											$meta_content .='</li>';
									}
								}else if(!empty($settings["select_trans"]) && $settings["select_trans"]=='p_translatepress'){
									$before_language_text = !empty($settings['before_language_text']) ? $settings['before_language_text'] : '';
									$meta_content .= '<li class="tp-translatepress-wrapper elementor-repeater-item-' . esc_attr($item['_id']) . ' '.esc_attr($responsive_class_attr).'">';
										$after_language_text = !empty($settings['after_language_text']) ? $settings['after_language_text'] : '';
										if($before_language_text){
											$meta_content .=  '<span class="tp-translatepress-before-lt">'.esc_html($before_language_text).'</span>';
										}
										$meta_content .= do_shortcode( $this->translatepress_get_shortcode() );									
										if($after_language_text){
											$meta_content .=  '<span class="tp-translatepress-after-lt">'.esc_html($after_language_text).'</span>';
										}
									$meta_content .='</li>';
								}								
							}
							/*wpml language switcher end*/							
							
							if($settings["display_mini_cart"] == 'yes' && $select_icon_list == 'cart'){
								global $woocommerce;
								if ($woocommerce) {
									//$count=WC()->cart->cart_contents_count;
									$cart_icon_style = $settings["cart_icon_style"];
									$meta_content .='<li class="mini-cart-icon '.esc_attr($cart_icon_style).' elementor-repeater-item-' . esc_attr($item['_id']) . ' '.esc_attr($responsive_class_attr).'">';
										$meta_content .='<div class="content-icon-list">';
										
											$meta_content .='<a href="'.wc_get_cart_url().'" class="plus-cart-icon '.esc_attr($cart_icon_style).' '.esc_attr($cart_icon).'">';
											if($cart_icon_style=='style-1' || $cart_icon_style=='style-2'){
												if(!empty($cart_icon) && $cart_icon == 'default'){
												$meta_content .='<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" enable-background="new 0 0 96 96" height="100%" id="bag" version="1.1" viewBox="0 0 96 96" width="100%" xml:space="preserve" style="&#10;"><path d="M68,24v-4C68,8.954,59.046,0,48,0S28,8.954,28,20v4H12v60c0,6.63,5.37,12,12,12h48c6.63,0,12-5.37,12-12V24H68z M36,20  c0-6.627,5.373-12,12-12c6.627,0,12,5.373,12,12v4H36V20z M76,84c0,2.21-1.79,4-4,4H24c-2.21,0-4-1.79-4-4V32h56V84z"/></svg>';
												}else if(!empty($cart_icon) && $cart_icon == 'cart_custom_icon'){
													$meta_content .=$cart_custom_icon;
												}else if(!empty($cart_icon) && $cart_icon == 'cart_custom_image'){
													if(!empty($cart_custom_image)){
														$cart_custom_img= $cart_custom_image;
													}else{
														$cart_custom_img='';
													}
													$meta_content .='<img class="tp-icon-img" src='.esc_url($cart_custom_img).' />';
												}
												
												if (! \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
													if ( null === WC()->cart ) {
														return;
													}
													$meta_content .= '<div class="cart-wrap"><span>'.WC()->cart->get_cart_contents_count().'</span></div>';
												}else{
													$meta_content .= '<div class="cart-wrap"><span>0</span></div>';
												}
											}											
											$meta_content .='</a>';
											if(!empty($cart_icon_style) && $cart_icon_style=='style-2'){
												$mc_fullwidth_content = ($settings['cart_icon_width_option'] == 'fullwidth') ?  'cart_icon_width_option' : '';
												$meta_content .='<div class="tpmc-header-extra-toggle-content  '.esc_attr($mc_fullwidth_content).' '.esc_attr($settings['cart_icon_direction']).'">';												
												$meta_content .='<div class="tpmc-extra-toggle-close-menu"></div>';
											}
											if(!empty($cart_icon_style) && $cart_icon_style=='style-1'){												
												$meta_content .='<div class="tpmc-header-extra-toggle-content-ext">';												
											}
											if (! \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
													ob_start();
														the_widget( 'WC_Widget_Cart', 'title= ' );
													$captured_cart_content = ob_get_clean();
												$meta_content .= $captured_cart_content;
											}else{	
												ob_start();
														the_widget( 'WC_Widget_Cart' );
													$captured_cart_content = ob_get_clean();
												$meta_content .= $captured_cart_content;
											}
											if(!empty($cart_icon_style) && $cart_icon_style == 'style-2'){
													if(!empty($settings['cart_offer_text'])){
														$meta_content .='<div class="mc-extra-bottom-con">'.wp_kses_post($settings['cart_offer_text']).'</div>';
													}
												$meta_content .='</div>';											
												
												$meta_content .='<div class="tpmc-extra-toggle-content-overlay"></div>';
											}
											if(!empty($settings['cart_offer_text'])){
												if(!empty($cart_icon_style) && $cart_icon_style == 'style-1'){
													$meta_content .='<div class="mc-extra-bottom-con">'.wp_kses_post($settings['cart_offer_text']).'</div>';
													$meta_content .='</div>';
												}
											}
										$meta_content .='</div>';
									$meta_content .='</li>';
								}
							} 
							if($settings["display_call_to_action_1"]=='yes' && $select_icon_list=='action_1'){
								if ( ! empty( $settings['button_1_link']['url'] ) ) {
									$this->add_render_attribute( 'button_1', 'href', $settings['button_1_link']['url'] );
									if ( $settings['button_1_link']['is_external'] ) {
										$this->add_render_attribute( 'button_1', 'target', '_blank' );
									}
									if ( $settings['button_1_link']['nofollow'] ) {
										$this->add_render_attribute( 'button_1', 'rel', 'nofollow' );
									}
								}
								if ( ! empty( $settings['button_1_css_id'] ) ) {
									$this->add_render_attribute( 'button_1', 'id', $settings['button_1_css_id'] );
								}
								$this->add_render_attribute( 'button_1', 'class', 'plus-action-button call-to-action-button' );
								$meta_content .='<li class="call-to-action-1 elementor-repeater-item-' . esc_attr($item['_id']) . ' '.esc_attr($responsive_class_attr).'">';
									$meta_content .='<div class="content-icon-list">';
										$meta_content .='<a '.$this->get_render_attribute_string( "button_1" ).'>';
											$meta_content .= $this->render_text_one();
										$meta_content .='</a>';
									$meta_content .='</div>';
								$meta_content .='</li>';
							}
							if($settings["display_call_to_action_2"]=='yes' && $select_icon_list=='action_2'){
								if ( ! empty( $settings['button_2_link']['url'] ) ) {
									$this->add_render_attribute( 'button_2', 'href', $settings['button_2_link']['url'] );
									if ( $settings['button_2_link']['is_external'] ) {
										$this->add_render_attribute( 'button_2', 'target', '_blank' );
									}
									if ( $settings['button_2_link']['nofollow'] ) {
										$this->add_render_attribute( 'button_2', 'rel', 'nofollow' );
									}
								}
								if ( ! empty( $settings['button_2_css_id'] ) ) {
									$this->add_render_attribute( 'button_2', 'id', $settings['button_2_css_id'] );
								}
								$this->add_render_attribute( 'button_2', 'class', 'plus-action-button call-to-action-button' );
								$meta_content .='<li class="call-to-action-2 elementor-repeater-item-' . esc_attr($item['_id']) . ' '.esc_attr($responsive_class_attr).'">';
									$meta_content .='<div class="content-icon-list">';
										$meta_content .='<a '.$this->get_render_attribute_string( "button_2" ).'>';
											$meta_content .= $this->render_text_two();
										$meta_content .='</a>';
									$meta_content .='</div>';
								$meta_content .='</li>';
							}
							if($settings["display_music_bar"]=='yes' && $select_icon_list=='music'){
								$music_icon_style=$settings['music_icon_style'];
								$data_attr='';
								$data_attr .=' data-bgmusic_load="off"';								
								if(!empty($settings['music_volume']["size"])){
									$data_attr .=' data-bgmusic_volume="'.esc_attr($settings['music_volume']["size"]).'"';
								}else{
									$data_attr .=' data-bgmusic_volume="50"';
								}
								if ( ! empty( $settings['music_audio_file']['url'] ) ) {
									$data_attr .=' data-bgmusic="'. esc_url($settings['music_audio_file']['url']).'"';	
								}
								
								$meta_content .='<li class="header-music-bar elementor-repeater-item-' . esc_attr($item['_id']) . ' '.esc_attr($responsive_class_attr).'">';
									$meta_content .='<div class="content-icon-list">';
										$meta_content .='<div id="plus_music_toggle" class="header-plus-music-toggle '.esc_attr($music_icon_style).'" '.$data_attr.'>
											<div>
												<span></span>
												<span></span>
												<span></span>
												<span></span>
												<span></span>
											</div>
										</div>';
									$meta_content .='</div>';
								$meta_content .='</li>';
							}
						endforeach;
					$meta_content .='</ul>';
				}
			$meta_content .='</div>';				
		$meta_content .='</div>';
		
		echo $meta_content;
	}
	
    protected function content_template() {
	
    }
	
	protected function render_text_one(){
		$icons_after=$icons_before=$button_text='';
		$settings = $this->get_settings_for_display();
		
		$before_after = $settings['button_1_before_after'];
		$button_text = $settings['button_1_text'];
		
		if($settings["button_1_icon_style"]=='font_awesome'){
			$icons=$settings["button_1_icon"];
		}else if($settings["button_1_icon_style"]=='icon_mind'){
			$icons=$settings["button_1_icons_mind"];
		}else if($settings["button_1_icon_style"]=='font_awesome_5'){
			ob_start();
			\Elementor\Icons_Manager::render_icon( $settings['button_1_icon_5'], [ 'aria-hidden' => 'true' ]);
			$icons = ob_get_contents();
			ob_end_clean();
		}else{
			$icons='';
		}
		
		if($before_after=='before' && !empty($icons)){
			if((!empty($settings["button_1_icon_style"])) && $settings["button_1_icon_style"]=='font_awesome_5'){			
				$icons_before = '<span class="btn-icon button-before">'.$icons.'</span>';
			}else{
				$icons_before = '<i class="btn-icon button-before '.esc_attr($icons).'"></i>';
			}
			
		}
		if($before_after=='after' && !empty($icons)){
			if(!empty($settings["button_1_icon_style"]) && $settings["button_1_icon_style"]=='font_awesome_5'){
				$icons_after = '<span class="btn-icon button-after">'.$icons.'</span>';
			}else{
				$icons_after = '<i class="btn-icon button-after '.esc_attr($icons).'"></i>';
			}
		   
		}
		
		$button_text =$icons_before.$button_text . $icons_after;
		
		return $button_text;
	}
	protected function render_text_two(){
		$icons_after=$icons_before=$button_text='';
		$settings = $this->get_settings_for_display();
		
		$before_after = $settings['button_2_before_after'];
		$button_text = $settings['button_2_text'];
		
		if($settings["button_2_icon_style"]=='font_awesome'){
			$icons=$settings["button_2_icon"];
		}else if($settings["button_2_icon_style"]=='icon_mind'){
			$icons=$settings["button_2_icons_mind"];
		}else if($settings["button_2_icon_style"]=='font_awesome_5'){
			ob_start();
			\Elementor\Icons_Manager::render_icon( $settings['button_2_icon_5'], [ 'aria-hidden' => 'true' ]);
			$icons = ob_get_contents();
			ob_end_clean();
		}else{
			$icons='';
		}
		
		if($before_after=='before' && !empty($icons)){
			if(!empty($settings["button_2_icon_style"]) && $settings["button_2_icon_style"]=='font_awesome_5'){
				$icons_before = '<span class="btn-icon button-before">'.$icons.'</span>';
			}else{
				$icons_before = '<i class="btn-icon button-before '.esc_attr($icons).'"></i>';
			}
		}
		if($before_after=='after' && !empty($icons)){
		   if(!empty($settings["button_2_icon_style"]) && $settings["button_2_icon_style"]=='font_awesome_5'){
				$icons_after = '<span class="btn-icon button-after">'.$icons.'</span>';
			}else{
				$icons_after = '<i class="btn-icon button-after '.esc_attr($icons).'"></i>';
			}
		}
		
		$button_text =$icons_before.$button_text . $icons_after;
		
		return $button_text;
	}
}