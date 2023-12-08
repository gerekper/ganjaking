<?php 
/*
Widget Name: Icon Stylist List
Description: Text of icon list stylist.
Author: Theplus
Author URI: https://posimyth.com
*/

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Style_List extends Widget_Base {
		
	public function get_name() {
		return 'tp-style-list';
	}

    public function get_title() {
        return esc_html__('Style Lists', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-list theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-essential');
    }
	
    protected function register_controls() {
		
		$this->start_controls_section(
			'content_section',
			[
			'label' => esc_html__( 'Stylist List', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);		
		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'content_description',
			[
				'label' => esc_html__( 'Description', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'theplus' ),
				'placeholder' => esc_html__( 'Type your description here', 'theplus' ),
				'dynamic' => ['active'   => true,],
			]
		);
		$repeater->add_control(
			'icon_style',
			[
				'label' => esc_html__( 'Icon Font', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'font_awesome',
				'options' => [
					''  => esc_html__( 'None', 'theplus' ),
					'font_awesome'  => esc_html__( 'Font Awesome', 'theplus' ),
					'font_awesome_5'  => esc_html__( 'Font Awesome 5', 'theplus' ),
					'icon_mind' => esc_html__( 'Icons Mind', 'theplus' ),
				],
			]
		);
		$repeater->add_control(
			'icon_fontawesome',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICON,
				'default' => 'fa fa-plus',
				'separator' => 'before',
				'condition' => [
					'icon_style' => 'font_awesome',
				],
			]
		);
		$repeater->add_control(
			'icon_fontawesome_5',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-plus',
					'library' => 'solid',
				],
				'condition' => [
					'icon_style' => 'font_awesome_5',
				],
			]
		);
		$repeater->add_control(
			'icons_mind',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'default' => 'iconsmind-Add',
				'label_block' => true,
				'options' => theplus_icons_mind(),
				'condition' => [
					'icon_style' => 'icon_mind',
				],
			]
		);
		$repeater->add_control(
			'link',
			[
				'label' => esc_html__( 'Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'label_block' => true,
				'placeholder' => esc_html__( 'https://your-link.com', 'theplus' ),
				'separator' => 'after',
				'dynamic' => ['active'   => true,],
			]
		);
		$repeater->add_control(
			'show_pin_hint',
			[
				'label' => esc_html__( 'Pin Hint', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'separator' => 'before',
				'default' => 'no',
			]
		);
		$repeater->add_control(
			'hint_text',
			[
				'label' => esc_html__( 'Hint Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Featured', 'theplus' ),
				'placeholder' => esc_html__( 'Ex. Unique,Top,Featured...', 'theplus' ),
				'dynamic' => ['active'   => true,],
				'condition' => [
					'show_pin_hint' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'hint_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-stylist-list-wrapper {{CURRENT_ITEM}} .plus-icon-list-text span.plus-hint-text' => 'color: {{VALUE}}'
				],
				'condition' => [
					'show_pin_hint' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'hint_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-stylist-list-wrapper {{CURRENT_ITEM}} .plus-icon-list-text span.plus-hint-text' => 'background: {{VALUE}}'
				],
				'dynamic' => ['active'   => true,],
				'condition' => [
					'show_pin_hint' => 'yes',
				],
			]
		);
		$repeater->add_responsive_control(
			'hint_left_space',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Horizontal Adjust', 'theplus'),
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'range' => [
					'px' => [
						'min'	=> -200,
						'max'	=> 200,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-stylist-list-wrapper {{CURRENT_ITEM}} .plus-icon-list-text span.plus-hint-text' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_pin_hint' => 'yes',
				],
			]
		);
		$repeater->add_responsive_control(
			'hint_top_space',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Vertical Adjust', 'theplus'),
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min'	=> -150,
						'max'	=> 150,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-stylist-list-wrapper {{CURRENT_ITEM}} .plus-icon-list-text span.plus-hint-text' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_pin_hint' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'show_background_style',
			[
				'label' => esc_html__( 'Interactive Hover Background Style', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'separator' => 'before',
				'default' => 'no',
			]
		);
		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'background_hover',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-bg-hover-effect {{CURRENT_ITEM}}',
				'condition' => [
					'show_background_style' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'show_tooltips',
			[
				'label'        => esc_html__( 'Tooltip', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'theplus' ),
				'label_off'    => esc_html__( 'No', 'theplus' ),
				'render_type'  => 'template',
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'content_type',
			[
				'label' => esc_html__( 'Content Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'normal_desc',
				'options' => [
					'normal_desc'  => esc_html__( 'Content Text', 'theplus' ),
					'content_wysiwyg'  => esc_html__( 'Content WYSIWYG', 'theplus' ),
					'template' => esc_html__( 'Template', 'theplus' ),
				],
				'condition' => [
					'show_tooltips' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'tooltip_content_desc',
			[
				'label' => esc_html__( 'Description', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 5,
				'default' => esc_html__( 'Luctus nec ullamcorper mattis', 'theplus' ),
				'dynamic' => ['active'   => true,],
				'condition' => [
					'content_type' => 'normal_desc',
					'show_tooltips' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'tooltip_content_wysiwyg',
			[
				'label' => esc_html__( 'Tooltip Content', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'theplus' ),
				'dynamic' => ['active'   => true,],
				'condition' => [
					'content_type' => 'content_wysiwyg',
					'show_tooltips' => 'yes',
				],
			]				
		);
		$repeater->add_control(
			'tooltip_content_align',
			[
				'label'   => esc_html__( 'Text Alignment', 'theplus' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left'    => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors'  => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .tippy-tooltip .tippy-content' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'content_type' => 'normal_desc',
					'show_tooltips' => 'yes',
				],
			]
		);
		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tooltip_content_typography',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .tippy-tooltip .tippy-content',
				'condition' => [
					'content_type' => ['normal_desc','content_wysiwyg'],
					'show_tooltips' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'tooltip_content_color',
			[
				'label'  => esc_html__( 'Text Color', 'theplus' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .tippy-tooltip .tippy-content,{{WRAPPER}} {{CURRENT_ITEM}} .tippy-tooltip .tippy-content p' => 'color: {{VALUE}}',
				],
				'condition' => [
					'content_type' => ['normal_desc','content_wysiwyg'],
					'show_tooltips' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'tooltip_content_template',
			[
				'label'       => esc_html__( 'Elementor Templates', 'theplus' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '0',
				'options'     => theplus_get_templates(),
				'label_block' => 'true',
				'condition' => [
					'content_type' => 'template',
					'show_tooltips' => 'yes',
				],
			]
		);
		$this->add_control(
			'icon_list',
			[
				'label' => '',
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'content_description' => esc_html__( 'List Item 1', 'theplus' ),
						'icon_fontawesome' => 'fa fa-check',
					],
					[
						'content_description' => esc_html__( 'List Item 2', 'theplus' ),
						'icon_fontawesome' => 'fa fa-times',
					],
					[
						'content_description' => esc_html__( 'List Item 3', 'theplus' ),
						'icon_fontawesome' => 'fa fa-dot-circle-o',
					],
				],
				'title_field' => '{{{ content_description }}}',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'section_read_more_toggle',
			[
				'label' => esc_html__( 'Read More Toggle', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'read_more_toggle',
			[
				'label'        => esc_html__( 'Read More Toggle', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'theplus' ),
				'label_off'    => esc_html__( 'No', 'theplus' ),
				'render_type'  => 'template',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'load_show_list_toggle',
			[
				'label' => esc_html__( 'List Open Default', 'theplus' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'default' => 3,
				'condition' => [
					'read_more_toggle' => 'yes',
				],
			]
		);
		$this->add_control(
			'read_show_option',
			[
				'label' => esc_html__( 'Expand Section Title', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( '+ Show all options', 'theplus' ),
				'separator' => 'before',
				'dynamic' => ['active'   => true,],
				'condition' => [
					'read_more_toggle' => 'yes',
				],
			]
		);
		$this->add_control(
			'read_less_option',
			[
				'label' => esc_html__( 'Shrink Section Title', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( '- Less options', 'theplus' ),
				'dynamic' => ['active'   => true,],
				'condition' => [
					'read_more_toggle' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'section_icon_list',
			[
				'label' => esc_html__( 'List', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'space_between',
			[
				'label' => esc_html__( 'Space Between', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-icon-list-items .plus-icon-list-item:not(:last-child)' => 'padding-bottom: calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .plus-icon-list-items .plus-icon-list-item:not(:first-child)' => 'margin-top: calc({{SIZE}}{{UNIT}}/2)',			
				],
				'condition' => [
					'layout' => 'default',
				],
			]
		);
		$this->add_responsive_control(
			'space_between_h_right',
			[
				'label' => esc_html__( 'Space Between', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [					
					'{{WRAPPER}} .plus-stylist-list-wrapper.tp-sl-l-horizontal .plus-icon-list-items .plus-icon-list-item' => 'margin-right: calc({{SIZE}}{{UNIT}}/2) !important',					
				],
				'condition' => [
					'layout' => 'tp_sl_l_horizontal',
				],
			]
		);
		$this->add_responsive_control(
			'space_between_h_bottom',
			[
				'label' => esc_html__( 'Bottom Space', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-stylist-list-wrapper.tp-sl-l-horizontal .plus-icon-list-items .plus-icon-list-item' => 'margin-bottom: calc({{SIZE}}{{UNIT}}/2) !important',
				],
				'condition' => [
					'layout' => 'tp_sl_l_horizontal',
				],
			]
		);
		$this->add_responsive_control(
			'min_width_h_list',
			[
				'label' => esc_html__( 'Min. Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [						
						'min' => 1,
						'max' => 500,
						'step' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-stylist-list-wrapper.tp-sl-l-horizontal .plus-icon-list-items .plus-icon-list-item' => 'min-width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'layout' => 'tp_sl_l_horizontal',
				],
			]
		);
		$this->add_responsive_control('icon_align',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-h-align-center',
					],
					'flex-end' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .plus-stylist-list-wrapper' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-items' => 'align-items: {{VALUE}};',
					'{{WRAPPER}} .plus-stylist-list-wrapper.tp-sl-l-horizontal .plus-icon-list-items' => 'justify-content: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'icon_border_bottom_color',
			[
				'label' => esc_html__( 'Separate Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-icon-list-items .plus-icon-list-item:not(:last-child)' => 'border-bottom: 1px solid {{VALUE}};width: 100%;',
				],
			]
		);

		$this->add_responsive_control(
			'stylishlist_padding',
			[
				'label' => esc_html__( 'List Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-items .plus-icon-list-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_stylishlist' );
		$this->start_controls_tab(
			'tab_stylishlist_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'stylishlist_bg_n',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-items .plus-icon-list-item',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'stylishlist_border_n',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-items .plus-icon-list-item',
			]
		);
		$this->add_responsive_control(
			'stylishlist_br_n',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-items .plus-icon-list-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'stylishlist_shadow_n',
				'selector' => '{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-items .plus-icon-list-item',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_stylishlist_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'stylishlist_bg_h',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-items .plus-icon-list-item:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'stylishlist_border_h',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-items .plus-icon-list-item:hover',
			]
		);
		$this->add_responsive_control(
			'stylishlist_br_h',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-items .plus-icon-list-item:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'stylishlist_shadow_h',
				'selector' => '{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-items .plus-icon-list-item:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		
		/*extra options*/
		$this->start_controls_section(
			'section_extra_options',
			[
				'label' => esc_html__( 'Extra Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'hover_background_style',
			[
				'label' => esc_html__( 'Interactive Links', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
			]
		);
		$this->add_control(
			'sl_display_counter',
			[
				'label' => esc_html__( 'Display Counter', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',				
			]
		);
		$this->add_control(
			'sl_display_counter_style',
			[
				'label' => esc_html__( 'Counter Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'number-normal',
				'options' => [									
					'number-normal'  => esc_html__( 'Normal', 'theplus' ),
					'decimal-leading-zero'  => esc_html__( 'Decimal Leading Zero', 'theplus' ),
					'upper-alpha'  => esc_html__( 'Upper Alpha', 'theplus' ),
					'lower-alpha'  => esc_html__( 'Lower Alpha', 'theplus' ),
					'lower-roman'  => esc_html__( 'Lower Roman', 'theplus' ),
					'upper-roman'  => esc_html__( 'Upper Roman', 'theplus' ),
					'lower-greek'  => esc_html__( 'Lower Greek', 'theplus' ),
				],
				'condition'    => [
					'sl_display_counter' => 'yes',					
				],
			]
		);

		$this->start_controls_tabs( 'tabs_layout_style' );
		$this->start_controls_tab( 'tab_layout_desktop', [
				'label' => esc_html__( 'Desktop', 'theplus' ),
			]
		);
		$this->add_control( 'layout', 
			[
				'label' => esc_html__( 'Desktop Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => esc_html__( 'Default', 'theplus' ),
					'tp_sl_l_horizontal'  => esc_html__( 'Horizontal', 'theplus' ),
				],
			]
		);
		$this->end_controls_tab();	
		$this->start_controls_tab( 'tab_layout_tablet', 
			[
				'label' => esc_html__( 'Tablet', 'theplus' ),
			]
		);
		$this->add_control( 'tablet_layout', 
			[
				'label' => esc_html__( 'Tablet Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => esc_html__( 'Default', 'theplus' ),
					'tp_sl_l_horizontal'  => esc_html__( 'Horizontal', 'theplus' ),
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_layout_mobile', 
			[
				'label' => esc_html__( 'Mobile', 'theplus' ),
			]
		);
		$this->add_control( 'mobile_layout', [
				'label' => esc_html__( 'Mobile Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => esc_html__( 'Default', 'theplus' ),
					'tp_sl_l_horizontal'  => esc_html__( 'Horizontal', 'theplus' ),
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
		
		$this->start_controls_section(
			'section_icon_style',
			[
				'label' => esc_html__( 'Icon', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control('icon_position', 
			[
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Position', 'theplus'),
				'default' => 'before',
				'options' => [
					'before' => esc_html__('Before', 'theplus'),
					'after' => esc_html__('After', 'theplus'),
				],		
			]
		);

		$this->start_controls_tabs( 'icon_style_tab' );
		$this->start_controls_tab( 'icon_style_normal', 
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'icon_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .plus-icon-list-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .plus-icon-list-icon svg' => 'fill: {{VALUE}};',
				],
				'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY
                ],
			]
		);
		$this->end_controls_tab();	
		$this->start_controls_tab( 'icon_style_hover', 
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'icon_color_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .plus-icon-list-item:hover .plus-icon-list-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .plus-icon-list-item:hover .plus-icon-list-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 14,
				],
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .plus-icon-list-icon' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .plus-icon-list-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .plus-icon-list-icon svg' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'icon_indent',
			[
				'label' => esc_html__( 'Icon Indent', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 250,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-icon-list-icon' => is_rtl() ? 'padding-right: {{SIZE}}{{UNIT}};' : 'padding-left: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'vertical_center',
			[
				'label' => esc_html__( 'Vertical Center', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),				
				'default' => 'yes',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'adv_icon_style',
			[
				'label' => esc_html__( 'Advanced Style', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'icon_inner_width',
			[
				'label' => esc_html__( 'Icon Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 45,
				],
				'selectors' => [
					'{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-icon' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};text-align:center;align-items: center;justify-content: center;',
				],
				'condition' => [
					'adv_icon_style' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'icon_border',
				'label' => esc_html__( 'Icon Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-icon',
				'condition' => [
					'adv_icon_style' => 'yes',
				],
			]
		);
		$this->start_controls_tabs('icon_adv_style_tabs');
		$this->start_controls_tab(
			'icon_adv_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'adv_icon_style' => 'yes',
				],
			]
		);
		$this->add_control(
			'icon_adv_radius',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'adv_icon_style' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'icon_adv_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-icon',
				'condition' => [
					'adv_icon_style' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'icon_adv_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-icon',
				'condition' => [
					'adv_icon_style' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'icon_adv_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'adv_icon_style' => 'yes',
				],
			]
		);		
		$this->add_control(
			'icon_border_hover',
			[
				'label' => esc_html__( 'Border Hover', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-item:hover .plus-icon-list-icon' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'adv_icon_style' => 'yes',
				],
			]
		);
		$this->add_control(
			'icon_adv_hover_radius',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-item:hover .plus-icon-list-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'adv_icon_style' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'icon_adv_hover_bg',
				'label' => esc_html__( 'Background Hover', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-item:hover .plus-icon-list-icon',
				'condition' => [
					'adv_icon_style' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'icon_adv_hover_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-item:hover .plus-icon-list-icon',
				'condition' => [
					'adv_icon_style' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
		
		/*display counter start*/
		$this->start_controls_section(
            'section_display_counter_styling',
            [
                'label' => esc_html__('Display Counter Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'sl_display_counter' => 'yes',					
				],
            ]
        );
		$this->add_responsive_control(
			'display_counter_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .plus-stylist-list-wrapper ul li .tp-sl-dc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'display_counter_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .plus-stylist-list-wrapper ul li .tp-sl-dc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'dc_box_size',
			[
				'label' => esc_html__( 'Box Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-stylist-list-wrapper ul li .tp-sl-dc' => 'width: {{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'display_counter_typography',
				'selector' => '{{WRAPPER}} .plus-stylist-list-wrapper ul li .tp-sl-dc',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'dc_align',
			[
				'label'   => esc_html__( 'Alignment', 'theplus' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'flex-start'    => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon'  => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .plus-stylist-list-wrapper ul li .tp-sl-dc' => 'justify-content: {{VALUE}};',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_display_counter' );
		$this->start_controls_tab(
			'tab_display_counter_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'display_counter_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-stylist-list-wrapper ul li .tp-sl-dc' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'display_counter_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-stylist-list-wrapper ul li .tp-sl-dc',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'display_counter_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-stylist-list-wrapper ul li .tp-sl-dc',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'display_counter_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-stylist-list-wrapper ul li .tp-sl-dc' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'display_counter_shadow',
				'selector' => '{{WRAPPER}} .plus-stylist-list-wrapper ul li .tp-sl-dc',				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_display_counter_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'display_counter_color_h',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-stylist-list-wrapper ul li.plus-icon-list-item:hover .tp-sl-dc' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'display_counter_background_h',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-stylist-list-wrapper ul li.plus-icon-list-item:hover .tp-sl-dc',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'display_counter_border_h',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-stylist-list-wrapper ul li.plus-icon-list-item:hover .tp-sl-dc',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'display_counter_radius_h',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-stylist-list-wrapper ul li.plus-icon-list-item:hover .tp-sl-dc' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'display_counter_shadow_h',
				'selector' => '{{WRAPPER}} .plus-stylist-list-wrapper ul li.plus-icon-list-item:hover .tp-sl-dc,
				{{WRAPPER}} ',				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*display counter end*/
		
		$this->start_controls_section(
            		'section_styling',
	            	[
	                	'label' => esc_html__('Content Options', 'theplus'),
	                	'tab' => Controls_Manager::TAB_STYLE,
	            	]
	        );		
		$this->add_control(
			'text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .plus-icon-list-text,{{WRAPPER}} .plus-icon-list-text p' => 'color: {{VALUE}};',
				],
				'global' => [
                    'default' => Global_Colors::COLOR_SECONDARY
                ],
			]
		);
		$this->add_control(
			'text_color_hover',
			[
				'label' => esc_html__( 'Text Hover', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .plus-icon-list-item:hover .plus-icon-list-text,{{WRAPPER}} .plus-icon-list-item:hover .plus-icon-list-text p' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'text_indent',
			[
				'label' => esc_html__( 'Text Indent', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 250,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-icon-list-text,{{WRAPPER}} .plus-icon-list-text p' => is_rtl() ? 'padding-right: {{SIZE}}{{UNIT}};' : 'padding-left: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control('contant_height',
			[
				'label' => esc_html__( 'Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-stylist-list-wrapper' => 'height:{{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'selector' => '{{WRAPPER}} .plus-icon-list-item,{{WRAPPER}} .plus-icon-list-item p',
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
            'section_toggle_expand_styling',
            [
                'label' => esc_html__('Read More Toggle', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'read_more_toggle' => 'yes',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'toggle_expand_typography',
				'label' => esc_html__( 'Expand/Toggle Text Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-stylist-list-wrapper a.read-more-options',
			]
		);
		$this->add_control(
			'toggle_expand_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-stylist-list-wrapper a.read-more-options' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'top_toggle_indent',
			[
				'label' => esc_html__( 'Top Indent', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -10,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-stylist-list-wrapper a.read-more-options' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
            'section_hint_text_styling',
            [
                'label' => esc_html__('Hint Text Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
			'hint_align',
			[
				'label' => esc_html__( 'Hint Text Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'label_block' => false,
				'default' => 'right',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'hint_typography',
				'selector' => '{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-text span.plus-hint-text',
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'hint_box_shadow',
				'selector' => '{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-text span.plus-hint-text',
			]
		);
		$this->add_responsive_control(
			'hint_padding',
			[
				'label' => esc_html__( 'Hint Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-text span.plus-hint-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_responsive_control(
            'hint_left_space',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Horizontal Adjust', 'theplus'),
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'range' => [
					'px' => [
						'min'	=> -200,
						'max'	=> 200,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-text span.plus-hint-text' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_responsive_control(
            'hint_left_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Min Width Adjust', 'theplus'),
				'default' => [
					'unit' => 'px',
					'size' => 60,
				],
				'range' => [
					'px' => [
						'min'	=> 0,
						'max'	=> 300,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'condition' => [
					'hint_align' => 'left',
				],
				'selectors' => [
					'{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-text span.plus-hint-text.left' => 'min-width: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_responsive_control(
            'hint_right_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Min Width Adjust', 'theplus'),				
				'range' => [
					'px' => [
						'min'	=> 0,
						'max'	=> 400,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'condition' => [
					'hint_align' => 'right',
				],
				'selectors' => [
					'{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-text span.plus-hint-text.right' => 'min-width: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_responsive_control(
            'hint_top_space',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Vertical Adjust', 'theplus'),
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min'	=> -150,
						'max'	=> 150,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-text span.plus-hint-text' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_control(
			'hint_bf',
			[
				'label' => esc_html__( 'Backdrop Filter', 'theplus' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'Default', 'theplus' ),
				'label_on' => __( 'Custom', 'theplus' ),
				'return_value' => 'yes',
			]
		);
		$this->add_control(
			'hint_bf_blur',
			[
				'label' => esc_html__( 'Blur', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 100,
						'min' => 1,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'condition'    => [
					'hint_bf' => 'yes',
				],
			]
		);
		$this->add_control(
			'hint_bf_grayscale',
			[
				'label' => esc_html__( 'Grayscale', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .plus-stylist-list-wrapper .plus-icon-list-text span.plus-hint-text' => '-webkit-backdrop-filter:grayscale({{hint_bf_grayscale.SIZE}})  blur({{hint_bf_blur.SIZE}}{{hint_bf_blur.UNIT}}) !important;backdrop-filter:grayscale({{hint_bf_grayscale.SIZE}})  blur({{hint_bf_blur.SIZE}}{{hint_bf_blur.UNIT}}) !important;',
				 ],
				'condition'    => [
					'hint_bf' => 'yes',
				],
			]
		);
		$this->end_popover();
		$this->end_controls_section();
		/*Tooltip Option*/
		$this->start_controls_section(
            'section_tooltip_styling',
            [
                'label' => esc_html__('Tooltip Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			\Theplus_Tooltips_Option_Group::get_type(),
			array(
				'label' => esc_html__( 'Tooltip Options', 'theplus' ),
				'name'           => 'tooltip_common_option',
			)
		);
		$this->add_group_control(
			\Theplus_Tooltips_Option_Style_Group::get_type(),
			array(
				'label' => esc_html__( 'Style Options', 'theplus' ),
				'name'           => 'tooltip_common_style',
			)
		);
		$this->add_control(
			'tooltip_bf',
			[
				'label' => esc_html__( 'Backdrop Filter', 'theplus' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'Default', 'theplus' ),
				'label_on' => __( 'Custom', 'theplus' ),
				'return_value' => 'yes',
			]
		);
		$this->add_control(
			'tooltip_bf_blur',
			[
				'label' => esc_html__( 'Blur', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 100,
						'min' => 1,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'condition'    => [
					'tooltip_bf' => 'yes',
				],
			]
		);
		$this->add_control(
			'tooltip_bf_grayscale',
			[
				'label' => esc_html__( 'Grayscale', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .plus-icon-list-item .tippy-popper' => '-webkit-backdrop-filter:grayscale({{tooltip_bf_grayscale.SIZE}})  blur({{tooltip_bf_blur.SIZE}}{{tooltip_bf_blur.UNIT}}) !important;backdrop-filter:grayscale({{tooltip_bf_grayscale.SIZE}})  blur({{tooltip_bf_blur.SIZE}}{{tooltip_bf_blur.UNIT}}) !important;',
				 ],
				'condition'    => [
					'tooltip_bf' => 'yes',
				],
			]
		);
		$this->end_popover();
		$this->end_controls_section();
		/*Tooltip Option*/
		/*Extra Option*/
		$this->start_controls_section(
            'section_extra_option_styling',
            [
                'label' => esc_html__('Extra Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
			'hover_inverse_effect',
			[
				'label' => esc_html__( 'On Hover Inverse Effect', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'unhover_item_opacity',
			[
				'label' => esc_html__( 'NotSelected Item Opacity', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.01,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0.6,
				],
				'selectors' => [
					'{{WRAPPER}} .plus-stylist-list-wrapper.hover-inverse-effect:hover .on-hover .plus-icon-list-item' => 'opacity: {{SIZE}} !important;',
					'{{WRAPPER}} .plus-stylist-list-wrapper.hover-inverse-effect:hover .on-hover .plus-icon-list-item:hover,
					body.hover-stylist-global {{WRAPPER}} .hover-inverse-effect-global .on-hover .plus-icon-list-item:hover' => 'opacity: 1 !important;',
				],
				'condition'    => [
					'hover_inverse_effect' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'label' => esc_html__( 'NotSelected Item CSS Filter', 'theplus' ),
				'name' => 'unhover_item_css_filters',
				'selector' => '{{WRAPPER}} .plus-stylist-list-wrapper.hover-inverse-effect:hover .on-hover .plus-icon-list-item',
				'condition'    => [
					'hover_inverse_effect' => 'yes',
				],
			]
		);
		$this->add_control(
			'hover_effect_area',
			[
				'label' => esc_html__( 'Effect Area', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'individual',
				'options' => [
					'individual'  => esc_html__( 'Individual', 'theplus' ),
					'global' => esc_html__( 'Global', 'theplus' ),
				],
				'condition'    => [
					'hover_inverse_effect' => 'yes',
				],
			]
		);
		$this->add_control(
			'global_hover_item_id',
			[
				'label' => esc_html__( 'Global List Connection Id', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'description' => esc_html__( 'Note : Use unique id here and put same in all connected lists.', 'theplus' ),
				'condition'    => [
					'hover_inverse_effect' => 'yes',
					'hover_effect_area' => 'global',
				],
			]
		);
		$this->end_controls_section();
		/*Extra Option*/
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
		$this->start_controls_section(
            'section_animation_styling',
            [
                'label' => esc_html__('On Scroll View Animation', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
			'animation_effects',
			[
				'label'   => esc_html__( 'Choose Animation Effect', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'no-animation',
				'options' => theplus_get_animation_options(),
			]
		);		
		$this->add_control(
            'animation_delay',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Animation Delay', 'theplus'),
				'default' => [
					'unit' => '',
					'size' => 50,
				],
				'range' => [
					'' => [
						'min'	=> 0,
						'max'	=> 4000,
						'step' => 15,
					],
				],
				'condition' => [
					'animation_effects!' => 'no-animation',
				],
            ]
        );
		$this->add_control(
			'animated_column_list',
			[
				'label'   => esc_html__( 'List Load Animation', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''  => esc_html__( 'Content Animation Block', 'theplus' ),
					'stagger' => esc_html__( 'Stagger Based Animation', 'theplus' ),
				],
				'condition'    => [
					'animation_effects!' => [ 'no-animation' ],
				],
			]
		);
		$this->add_control(
            'animation_stagger',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Animation Stagger', 'theplus'),
				'default' => [
					'unit' => '',
					'size' => 150,
				],
				'range' => [
					'' => [
						'min'	=> 0,
						'max'	=> 6000,
						'step' => 10,
					],
				],
				'condition' => [
					'animation_effects!' => [ 'no-animation' ],
					'animated_column_list' => 'stagger',
				],
            ]
        );
		$this->add_control(
            'animation_duration_default',
            [
				'label'   => esc_html__( 'Animation Duration', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'condition'    => [
					'animation_effects!' => 'no-animation',
				],
			]
		);
		$this->add_control(
            'animate_duration',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Duration Speed', 'theplus'),
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'range' => [
					'px' => [
						'min'	=> 100,
						'max'	=> 10000,
						'step' => 100,
					],
				],
				'condition' => [
					'animation_effects!' => 'no-animation',
					'animation_duration_default' => 'yes',
				],
            ]
        );
		$this->add_control(
			'animation_out_effects',
			[
				'label'   => esc_html__( 'Out Animation Effect', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'no-animation',
				'options' => theplus_get_out_animation_options(),
				'separator' => 'before',
				'condition' => [
					'animation_effects!' => 'no-animation',
				],
			]
		);
		$this->add_control(
            'animation_out_delay',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Out Animation Delay', 'theplus'),
				'default' => [
					'unit' => '',
					'size' => 50,
				],
				'range' => [
					'' => [
						'min'	=> 0,
						'max'	=> 4000,
						'step' => 15,
					],
				],
				'condition' => [
					'animation_effects!' => 'no-animation',
					'animation_out_effects!' => 'no-animation',
				],
            ]
        );
		$this->add_control(
            'animation_out_duration_default',
            [
				'label'   => esc_html__( 'Out Animation Duration', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'condition' => [
					'animation_effects!' => 'no-animation',
					'animation_out_effects!' => 'no-animation',
				],
			]
		);
		$this->add_control(
            'animation_out_duration',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Duration Speed', 'theplus'),
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'range' => [
					'px' => [
						'min'	=> 100,
						'max'	=> 10000,
						'step' => 100,
					],
				],
				'condition' => [
					'animation_effects!' => 'no-animation',
					'animation_out_effects!' => 'no-animation',
					'animation_out_duration_default' => 'yes',
				],
            ]
        );
		$this->end_controls_section();
	}
	 protected function render() {

        $settings = $this->get_settings_for_display();
		$display_counter_class='';

		$sl_display_counter = isset($settings['sl_display_counter']) ? $settings['sl_display_counter'] : '';
		$sl_display_counter_style = !empty($settings['sl_display_counter_style']) ? $settings['sl_display_counter_style'] : 'number-normal';
		if($sl_display_counter == 'yes' && !empty($sl_display_counter_style)){
			if($sl_display_counter_style == 'number-normal'){
				$display_counter_class = 'number_normal';
			}else if($sl_display_counter_style == 'decimal-leading-zero'){
				$display_counter_class = 'decimal_leading_zero';
			}else if($sl_display_counter_style == 'upper-alpha'){
				$display_counter_class = 'upper_alpha';
			}else if($sl_display_counter_style == 'lower-alpha'){
				$display_counter_class = 'lower_alpha';
			}else if($sl_display_counter_style == 'lower-roman'){
				$display_counter_class = 'lower_roman';
			}else if($sl_display_counter_style == 'upper-roman'){
				$display_counter_class = 'upper_roman';
			}else if($sl_display_counter_style == 'lower-greek'){
				$display_counter_class = 'lower_greek';
			}
		}
		
		$vertical_center =($settings["vertical_center"]=='yes') ? 'd-flex-center' : 'd-flex-top';
		
		$hover_inverse_effect=$hover_inverse_attr_id =$hover_inverse_id='';
		if($settings["hover_inverse_effect"]=='yes'){
			$hover_inverse_effect = ($settings["hover_effect_area"]=='global') ? 'hover-inverse-effect-global' : 'hover-inverse-effect';
			$hover_inverse_attr_id = ($settings["hover_effect_area"]=='global' && !empty($settings["global_hover_item_id"])) ? 'data-hover-inverse="hover-'.esc_attr($settings["global_hover_item_id"]).'"' : '';
			$hover_inverse_id = ($settings["hover_effect_area"]=='global' && !empty($settings["global_hover_item_id"])) ? 'hover-'.esc_attr($settings["global_hover_item_id"]) : '';
		}
		
		$animation_effects=$settings["animation_effects"];
		$animation_delay= (!empty($settings["animation_delay"]["size"])) ? $settings["animation_delay"]["size"] : 50;
		$animation_stagger=(!empty($settings["animation_stagger"]["size"])) ? $settings["animation_stagger"]["size"] : 150;
		$animated_columns='';		
		if($animation_effects=='no-animation'){
			$animated_class='';
			$animation_attr='';
		}else{
			$animate_offset = theplus_scroll_animation();
			$animated_class = 'animate-general';
			$animation_attr = ' data-animate-type="'.esc_attr($animation_effects).'" data-animate-delay="'.esc_attr($animation_delay).'"';
			$animation_attr .= ' data-animate-offset="'.esc_attr($animate_offset).'"';
			if($settings["animated_column_list"]=='stagger'){
				$animated_columns='animated-columns';
				$animation_attr .=' data-animate-columns="stagger"';
				$animation_attr .=' data-animate-stagger="'.esc_attr($animation_stagger).'"';
			}
			if($settings["animation_duration_default"]=='yes'){
				$animate_duration=$settings["animate_duration"]["size"];
				$animation_attr .= ' data-animate-duration="'.esc_attr($animate_duration).'"';
			}
			if(!empty($settings["animation_out_effects"]) && $settings["animation_out_effects"]!='no-animation'){
				$animation_attr .= ' data-animate-out-type="'.esc_attr($settings["animation_out_effects"]).'" data-animate-out-delay="'.esc_attr($settings["animation_out_delay"]["size"]).'"';					
				if($settings["animation_out_duration_default"]=='yes'){						
					$animation_attr .= ' data-animate-out-duration="'.esc_attr($settings["animation_out_duration"]["size"]).'"';
				}
			}
		}

		/*--Plus Extra ---*/
			$PlusExtra_Class = "";
			include THEPLUS_PATH. 'modules/widgets/theplus-widgets-extra.php';
		/*--Plus Extra ---*/
		
		?>
		
		<?php echo $before_content; ?>
		
		<?php if($settings["hover_background_style"]=='yes'){ ?>
				<div class="plus-bg-hover-effect">
				<?php
					$j=0;
					foreach ( $settings['icon_list'] as $index => $item ) :
						if($j==0){
						$active_class='active';
						}else{
						$active_class='';
						}
						$lz1 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($item['background_hover_image']) : '';
						echo '<div class="hover-item-content elementor-repeater-item-'.esc_attr($item['_id']).' '.esc_attr($active_class).' '.esc_attr($lz1).'"></div>';
						$j++;
					endforeach;
				?>
				</div>
			<?php }
			
			$layout = !empty($settings['layout']) ? $settings['layout'] : 'default';
			$tablet_layout = !empty($settings['tablet_layout']) ? $settings['tablet_layout'] : 'default';
			$mobile_layout = !empty($settings['mobile_layout']) ? $settings['mobile_layout'] : 'default';
			
			$layout_class=$tablayout=$moblayout='';
			if(!empty($layout) && $layout==='tp_sl_l_horizontal'){
				$layout_class = 'tp-sl-l-horizontal';
			}
			if(!empty($tablet_layout) && $tablet_layout==='tp_sl_l_horizontal'){
				$layout_class = 'tp-sl-l-horizontal';
			}
			if(!empty($mobile_layout) && $mobile_layout==='tp_sl_l_horizontal'){
				$layout_class = 'tp-sl-l-horizontal';
			}
				$layout_attr = '';
				$layout_attr = ['desktop' => $layout, 'tablet' => $tablet_layout, 'mobile' => $mobile_layout ];
				$layout_attr = htmlspecialchars(json_encode($layout_attr, true));
			?>
		<div class="plus-stylist-list-wrapper <?php echo esc_attr($animated_class); ?> <?php echo esc_attr($hover_inverse_effect); ?> <?php echo esc_attr($hover_inverse_id); ?> <?php //echo esc_attr($layout_class); ?>" <?php echo $animation_attr; ?> <?php echo $hover_inverse_attr_id; ?> data-layout="<?php echo esc_attr($layout_attr); ?>" >
			
		<ul class="plus-icon-list-items <?php echo esc_attr($vertical_center); ?>">
			<?php
			$ij=0;
			$i=0;
			foreach ( $settings['icon_list'] as $index => $item ) :
				$repeater_setting_key = $this->get_repeater_setting_key( 'text', 'icon_list', $index );

				$this->add_inline_editing_attributes( $repeater_setting_key );
				$tooltip_class='';
				if($item["show_tooltips"]=='yes'){
					$tooltip_class='plus-tooltip';
				}
				$uniqid=uniqid("tooltip");
				if($i==0){
					$active_class='active';
				}else{
					$active_class='';
				}
				$_tooltip='_tooltip_'.$i;
				if( $item['show_tooltips'] == 'yes' ) {
					
					$this->add_render_attribute( $_tooltip, 'data-tippy', '', true );

					if (!empty($item['content_type']) && $item['content_type']=='normal_desc') {
						$this->add_render_attribute( $_tooltip, 'title', $item['tooltip_content_desc'], true );
					}else if (!empty($item['content_type']) && $item['content_type']=='content_wysiwyg') {
						$tooltip_content=$item['tooltip_content_wysiwyg'];
						$this->add_render_attribute( $_tooltip, 'title', $tooltip_content, true );
					}else if($item["content_type"]=='template' && !empty($item['tooltip_content_template'])){
						$tooltip_content=Theplus_Element_Load::elementor()->frontend->get_builder_content_for_display( $item['tooltip_content_template'] );
						$this->add_render_attribute( $_tooltip, 'title', $tooltip_content, true );
					}
					
					$plus_tooltip_position=(!empty($settings["tooltip_common_option_plus_tooltip_position"])) ? $settings["tooltip_common_option_plus_tooltip_position"] : 'top';
					$this->add_render_attribute( $_tooltip, 'data-tippy-placement', $plus_tooltip_position, true );
					
					$tooltip_interactive =($settings["tooltip_common_option_plus_tooltip_interactive"]=='' || $settings["tooltip_common_option_plus_tooltip_interactive"]=='yes') ? 'true' : 'false';
					$this->add_render_attribute( $_tooltip, 'data-tippy-interactive', $tooltip_interactive, true );
					
					$plus_tooltip_theme=(!empty($settings["tooltip_common_option_plus_tooltip_theme"])) ? $settings["tooltip_common_option_plus_tooltip_theme"] : 'dark';
					$this->add_render_attribute( $_tooltip, 'data-tippy-theme', $plus_tooltip_theme, true );
					
					
					$tooltip_arrow =($settings["tooltip_common_option_plus_tooltip_arrow"]!='none' || $settings["tooltip_common_option_plus_tooltip_arrow"]=='') ? 'true' : 'false';
					$this->add_render_attribute( $_tooltip, 'data-tippy-arrow', $tooltip_arrow , true );
					
					$plus_tooltip_arrow=(($settings["tooltip_common_option_plus_tooltip_arrow"])) ? $settings["tooltip_common_option_plus_tooltip_arrow"] : 'sharp';
					$this->add_render_attribute( $_tooltip, 'data-tippy-arrowtype', $plus_tooltip_arrow, true );
					
					$plus_tooltip_animation=(!empty($settings["tooltip_common_option_plus_tooltip_animation"])) ? $settings["tooltip_common_option_plus_tooltip_animation"] : 'shift-toward';
					$this->add_render_attribute( $_tooltip, 'data-tippy-animation', $plus_tooltip_animation, true );
					
					$plus_tooltip_x_offset=(isset($settings["tooltip_common_option_plus_tooltip_x_offset"])) ? $settings["tooltip_common_option_plus_tooltip_x_offset"] : 0;
					$plus_tooltip_y_offset=(isset($settings["tooltip_common_option_plus_tooltip_y_offset"])) ? $settings["tooltip_common_option_plus_tooltip_y_offset"] : 0;
					$this->add_render_attribute( $_tooltip, 'data-tippy-offset', $plus_tooltip_x_offset .','. $plus_tooltip_y_offset, true );
					
					$tooltip_duration_in =(isset($settings["tooltip_common_option_plus_tooltip_duration_in"])) ? $settings["tooltip_common_option_plus_tooltip_duration_in"] : 250;
					$tooltip_duration_out =(isset($settings["tooltip_common_option_plus_tooltip_duration_out"])) ? $settings["tooltip_common_option_plus_tooltip_duration_out"] : 200;
					$tooltip_trigger =(!empty($settings["tooltip_common_option_plus_tooltip_triggger"])) ? $settings["tooltip_common_option_plus_tooltip_triggger"] : 'mouseenter';
					$tooltip_arrowtype =(!empty($settings["tooltip_common_option_plus_tooltip_arrow"])) ? $settings["tooltip_common_option_plus_tooltip_arrow"] : 'sharp';
				}
				
				?>
				<li id="<?php echo esc_attr($uniqid); ?>" class="plus-icon-list-item elementor-repeater-item-<?php echo esc_attr($item['_id']); ?> <?php echo esc_attr($tooltip_class); ?> <?php echo esc_attr($animated_columns); ?> <?php echo esc_attr($active_class); ?>" data-local="true" <?php echo $this->get_render_attribute_string( $_tooltip ); ?>>				
				
					<?php
					if($sl_display_counter == 'yes' && !empty($sl_display_counter_style)){
						$lz3 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['display_counter_background_image'],$settings['display_counter_background_h_image']) : '';
						echo '<div class="tp-sl-dc '.esc_attr($display_counter_class).' '.esc_attr($lz3).'"></div>';
					}
					
					
					if ( ! empty( $item['link']['url'] ) ) {
						$link_key = 'link_' . $index;
						$this->add_link_attributes( $link_key, $item['link'] );
						echo '<a ' . $this->get_render_attribute_string( $link_key ) . '>';
					}

					$icon_position = $settings['icon_position'];

					$icons='';
					$icon_html = '';
					if($item['icon_style']=='font_awesome'){
						$icons=$item['icon_fontawesome'];									
					}else if($item['icon_style']=='icon_mind'){
						$icons=$item['icons_mind'];									
					}else if($item['icon_style']=='font_awesome_5'){
						ob_start();
						\Elementor\Icons_Manager::render_icon( $item['icon_fontawesome_5'], [ 'aria-hidden' => 'true' ]);
						$icons = ob_get_contents();
						ob_end_clean();
					}

					if ( !empty($icons) && $icon_position == 'before' ) {
						$lz2 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['icon_adv_bg_image'],$settings['icon_adv_hover_bg_image']) : '';
						?>
						<div class="plus-icon-list-icon <?php echo $lz2; ?>"> 
							<?php 
							if(!empty($item['icon_style']) && $item['icon_style'] == 'font_awesome_5'){ ?>
								<span><?php echo $icons; ?></span> <?php
							}else{ ?>
									<i class="<?php echo esc_attr( $icons ); ?>" aria-hidden="true"></i>
								<?php
							} ?>
						</div>
					<?php }

					$inline_class='';
					if( $item['show_pin_hint'] == 'yes' ){
						$inline_class = ' pin-hint-inline';
					} ?>

					<div class="plus-icon-list-text <?php echo esc_attr($inline_class); ?>" <?php echo $this->get_render_attribute_string( $repeater_setting_key ); ?>>
						<?php echo wp_kses_post($item['content_description']);

						if( $item['show_pin_hint'] == 'yes' ){ ?>

							<span class="plus-hint-text <?php echo esc_attr($settings['hint_align']); ?>">
								<span class="plus-hint-text-inner">
									<?php echo esc_html($item['hint_text']); ?>
								</span>
							</span>

						<?php } ?>
					</div>

					<?php 
						if ( !empty($icons) && $icon_position == 'after' ) {
					
						$lz2 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['icon_adv_bg_image'],$settings['icon_adv_hover_bg_image']) : '';
						?>
						<div class="plus-icon-list-icon <?php echo $lz2; ?>"> 
							<?php 
							if(!empty($item['icon_style']) && $item['icon_style']=='font_awesome_5'){ ?>
								<span><?php echo $icons; ?></span> <?php
							}else{ ?>
									<i class="<?php echo esc_attr( $icons ); ?>" aria-hidden="true"></i>
								<?php
							} ?>
						</div>
					<?php }

						if ( ! empty( $item['link']['url'] ) ) : ?>
							</a>
					<?php endif;
					

					$item_inline_tippy_js='';
					if($item['show_tooltips'] == 'yes'){
						$item_inline_tippy_js ='jQuery( document ).ready(function() {
						"use strict";
							if(typeof tippy === "function"){
								tippy( "#'.esc_attr($uniqid).'" , {
									arrowType : "'.esc_attr($tooltip_arrowtype).'",
									duration : ['.esc_attr($tooltip_duration_in).','.esc_attr($tooltip_duration_out).'],
									trigger : "'.esc_attr($tooltip_trigger).'",
									appendTo: document.querySelector("#'.esc_attr($uniqid).'")
								});
							}
						});';
						
						echo wp_print_inline_script_tag($item_inline_tippy_js);
					} ?>
				</li>
				<?php
				
				$i++;
				$ij++;
			endforeach;
			?>
		</ul>
		<?php 
		$default_load=$settings['load_show_list_toggle'];
		if($settings["read_more_toggle"]=='yes' && $ij > $default_load){
			$default_load=$default_load-1;
			echo '<a href="#" class="read-more-options more" data-default-load="'.esc_attr($default_load).'" data-more-text="'.esc_attr($settings["read_show_option"]).'" data-less-text="'.esc_attr($settings["read_less_option"]).'">'.esc_html($settings["read_show_option"]).'</a>';
		}
		?>
		</div>		
		<?php
		echo $after_content;
		if(!empty($hover_inverse_effect) && $hover_inverse_effect=="hover-inverse-effect-global" && !empty($hover_inverse_id)){

			$custom= $settings["unhover_item_css_filters_css_filter"];
			$blur='blur( '.(isset($settings["unhover_item_css_filters_blur"]["size"]) ? $settings["unhover_item_css_filters_blur"]["size"] : ''). (isset($settings["unhover_item_css_filters_blur"]["unit"]) ? $settings["unhover_item_css_filters_blur"]["unit"] : '').')';
			$brightness=' brightness('.(isset($settings["unhover_item_css_filters_brightness"]["size"]) ? $settings["unhover_item_css_filters_brightness"]["size"] : '').'%)';
			$contrast=' contrast('.(isset($settings["unhover_item_css_filters_contrast"]["size"]) ? $settings["unhover_item_css_filters_contrast"]["size"] : '').'%)';
			$saturate=' saturate('.(isset($settings["unhover_item_css_filters_saturate"]["size"]) ? $settings["unhover_item_css_filters_saturate"]["size"] : '').'%)';
			$hue=' hue-rotate('.(isset($settings["unhover_item_css_filters_hue"]["size"]) ? $settings["unhover_item_css_filters_hue"]["size"] : '').'deg)';
				echo '<style>body.hover-stylist-global .hover-inverse-effect-global.'.esc_attr($hover_inverse_id).' .on-hover .plus-icon-list-item{opacity:'.esc_attr($settings["unhover_item_opacity"]["size"]).';';
			if($custom=='custom'){
				echo 'filter:'.$blur.$brightness.$contrast.$saturate.$hue.';';
			}
			echo '}</style>';
		}
	}
	
    protected function content_template() {
	
    }


}
