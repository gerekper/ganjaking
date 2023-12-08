<?php 
/*
Widget Name: Pricing Table
Description: unique design of pricing table.
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
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Pricing_Table extends Widget_Base {
		
	public function get_name() {
		return 'tp-pricing-table';
	}

    public function get_title() {
        return esc_html__('Pricing Table', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-money theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-essential');
    }
	
    protected function register_controls() {
		
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Layout', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'pricing_table_style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'style-2'  => esc_html__( 'Style 2', 'theplus' ),
					'style-3'  => esc_html__( 'Style 3', 'theplus' ),
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'title_content_section',
			[
				'label' => esc_html__( 'Title Section', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'title_heading',
			[
				'label' => esc_html__( 'Title', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'title_style',
			[
				'label' => esc_html__( 'Title Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style 1', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'pricing_title',
			[
				'label' => esc_html__( 'Title', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Professional', 'theplus' ),
				'dynamic' => ['active'   => true,],
			]
		);
		$this->add_control(
			'pricing_subtitle',
			[
				'label' => esc_html__( 'Sub Title', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => ['active'   => true,],
			]
		);
		$this->add_control(
			'icons_heading',
			[
				'label' => esc_html__( 'Icon Options', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'image_icon',
			[
				'label' => esc_html__( 'Select Icon', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'description' => esc_html__('You can select Icon, Custom Image or SVG using this option.','theplus'),
				'default' => '',
				'options' => [
					''  => esc_html__( 'None', 'theplus' ),
					'icon' => esc_html__( 'Icon', 'theplus' ),
					'image' => esc_html__( 'Image', 'theplus' ),
					'svg' => esc_html__( 'Svg', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'svg_icon',
			[
				'label' => esc_html__( 'Svg Select Option', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'img',
				'options' => [
					'img'  => esc_html__( 'Custom Upload', 'theplus' ),
					'svg' => esc_html__( 'Pre Built SVG Icon', 'theplus' ),
				],
				'condition' => [
					'image_icon' => 'svg',
				],
			]
		);
		$this->add_control(
			'svg_image',
			[
				'label' => esc_html__( 'Only Svg', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'description' => esc_html__('Select Only .svg File from media library.','theplus'),
				'default' => [
					'url' => '',
				],
				'media_type' => 'image',
				'condition' => [
					'image_icon' => 'svg',
					'svg_icon' => 'img',
				],
			]
			);
		$this->add_control(
			'svg_d_icon',
			[
				'label' => esc_html__( 'Select Svg Icon', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'app.svg',
				'options' => theplus_svg_icons_list(),
				'condition' => [
					'image_icon' => 'svg',
					'svg_icon' => 'svg',
				],
			]
		);
		$this->add_control(
			'select_image',
			[
				'label' => esc_html__( 'Use Image As icon', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'media_type' => 'image',
				'dynamic' => ['active'   => true,],
				'condition' => [
					'image_icon' => 'image',
				],
			]
		);
		$this->add_control(
			'icon_font_style',
			[
				'label' => esc_html__( 'Icon Font', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'font_awesome',
				'options' => [
					'font_awesome'  => esc_html__( 'Font Awesome', 'theplus' ),
					'font_awesome_5'  => esc_html__( 'Font Awesome 5', 'theplus' ),
					'icon_mind' => esc_html__( 'Icons Mind', 'theplus' ),
				],
				'condition' => [
					'image_icon' => 'icon',
				],
			]
		);
		$this->add_control(
			'icon_fontawesome',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICON,
				'default' => 'fa fa-bank',
				'condition' => [
					'image_icon' => 'icon',
					'icon_font_style' => 'font_awesome',
				],
			]
		);
		$this->add_control(
			'icon_fontawesome_5',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-university',
					'library' => 'solid',
				],
				'condition' => [
					'image_icon' => 'icon',
					'icon_font_style' => 'font_awesome_5',
				],	
			]
		);
		$this->add_control(
			'icons_mind',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'default' => '',
				'label_block' => true,
				'options' => theplus_icons_mind(),
				'condition' => [
					'image_icon' => 'icon',
					'icon_font_style' => 'icon_mind',
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'price_content_section',
			[
				'label' => esc_html__( 'Price', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'price_style',
			[
				'label' => esc_html__( 'Price Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'style-2'  => esc_html__( 'Style 2', 'theplus' ),
					'style-3'  => esc_html__( 'Style 3', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'price_prefix',
			[
				'label' => esc_html__( 'Prefix Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( '$', 'theplus' ),
				'placeholder' => esc_html__( 'Enter text of Price Prefix.. Ex. $,Rs,...', 'theplus' ),
				'dynamic' => ['active'   => true,],
			]
		);
		$this->add_control(
			'price',
			[
				'label' => esc_html__( 'Value Of Price', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( '59.99', 'theplus' ),
				'placeholder' => esc_html__( 'Enter value of Price.. Ex. 49,69...', 'theplus' ),
				'dynamic' => ['active'   => true,],
			]
		);
		$this->add_control(
			'price_postfix',
			[
				'label' => esc_html__( 'Postfix Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Per Month', 'theplus' ),
				'placeholder' => esc_html__( 'Enter text of Price Postfix.. Ex. Per Month...', 'theplus' ),
				'dynamic' => ['active'   => true,],
			]
		);
		$this->end_controls_section();
		/*Previous Price*/
		$this->start_controls_section(
			'previous_price_content_section',
			[
				'label' => esc_html__( 'Previous Price', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'show_previous_price',
			[
				'label'        => esc_html__( 'Display Previous Price', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'theplus' ),
				'label_off'    => esc_html__( 'No', 'theplus' ),
			]
		);
		$this->add_control(
			'previous_price_prefix',
			[
				'label' => esc_html__( 'Prefix Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( '$', 'theplus' ),
				'placeholder' => esc_html__( 'Enter text of Price Prefix.. Ex. $,Rs,...', 'theplus' ),
				'dynamic' => ['active'   => true,],
				'condition' => [
					'show_previous_price' => 'yes',
				],
			]
		);
		$this->add_control(
			'previous_price',
			[
				'label' => esc_html__( 'Value Of Price', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( '59.99', 'theplus' ),
				'placeholder' => esc_html__( 'Enter value of Price.. Ex. 49,69...', 'theplus' ),
				'dynamic' => ['active'   => true,],
				'condition' => [
					'show_previous_price' => 'yes',
				],
			]
		);
		$this->add_control(
			'previous_price_postfix',
			[
				'label' => esc_html__( 'Postfix Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Enter text of Price Postfix.. Ex. Rs,%..', 'theplus' ),
				'dynamic' => ['active'   => true,],
				'condition' => [
					'show_previous_price' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*Previous Price*/
		$this->start_controls_section(
			'content_description_section',
			[
				'label' => esc_html__( 'Content Description', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'content_style',
			[
				'label' => esc_html__( 'Content Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'stylist_list',
				'options' => [
					'stylist_list'  => esc_html__( 'Stylish List', 'theplus' ),
					'wysiwyg_content'  => esc_html__( 'WYSIWYG', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'content_list_style',
			[
				'label' => esc_html__( 'Content List Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'style-2'  => esc_html__( 'Style 2', 'theplus' ),
				],
				'condition' => [
					'content_style' => 'stylist_list',
				],
			]
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'list_description',
			[
				'label' => esc_html__( 'List Description', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'I am text block.', 'theplus' ),
				'placeholder' => esc_html__( 'Type your description here', 'theplus' ),
				'dynamic' => ['active'   => true,],
			]
		);
		$repeater->add_control(
			'list_icon_style',
			[
				'label' => esc_html__( 'Icon Font', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'font_awesome',
				'options' => [
					'font_awesome'  => esc_html__( 'Font Awesome', 'theplus' ),
					'font_awesome_5'  => esc_html__( 'Font Awesome 5', 'theplus' ),
					'icon_mind' => esc_html__( 'Icons Mind', 'theplus' ),
				],
			]
		);
		$repeater->add_control(
			'list_icon_fontawesome',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICON,
				'default' => 'fa fa-plus',
				'separator' => 'before',
				'condition' => [
					'list_icon_style' => 'font_awesome',
				],
			]
		);
		$repeater->add_control(
			'list_icon_fontawesome_5',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-check-circle',
					'library' => 'solid',
				],
				'condition' => [
					'list_icon_style' => 'font_awesome_5',
				],
			]
		);
		$repeater->add_control(
			'list_icons_mind',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'default' => 'iconsmind-Add',
				'label_block' => true,
				'options' => theplus_icons_mind(),
				'condition' => [
					'list_icon_style' => 'icon_mind',
				],
			]
		);
		$repeater->add_control(
			'show_tooltips',
			[
				'label'        => esc_html__( 'Tooltip Options', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'theplus' ),
				'label_off'    => esc_html__( 'No', 'theplus' ),
				'render_type'  => 'template',
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'show_tooltips_on',
			[
				'label' => esc_html__( 'Tooltip On', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'box',
				'options' => [
					'box'  => esc_html__( 'Box', 'theplus' ),
					'icon'  => esc_html__( 'Icon', 'theplus' ),
				],
				'condition' => [
					'show_tooltips' => 'yes',
				],
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
		$this->add_control(
			'icon_list',
			[
				'label' => '',
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'list_description' => esc_html__( 'List Item 1', 'theplus' ),
						'list_icon_fontawesome' => 'fa fa-check-circle',
					],
					[
						'list_description' => esc_html__( 'List Item 2', 'theplus' ),
						'list_icon_fontawesome' => 'fa fa-check-circle',
					],
					[
						'list_description' => esc_html__( 'List Item 3', 'theplus' ),
						'list_icon_fontawesome' => 'fa fa-check-circle',
					],
				],
				'title_field' => '{{{ list_description }}}',
				'condition' => [
					'content_style' => 'stylist_list',
				],
			]
		);
		$this->add_control(
			'load_show_list_toggle',
			[
				'label' => esc_html__( 'List Open Default', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'default' => 3,
				'condition' => [
					'content_style' => 'stylist_list',
					'content_list_style' => 'style-1'
				],
			]
		);
		$this->add_control(
			'list_style_show_option',
			[
				'label' => esc_html__( 'Expand Section Title', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( '+ Show all options', 'theplus' ),
				'description' => esc_html__( 'Expand and Shrink Options will be available only for more than 3 list items.', 'theplus' ),
				'separator' => 'before',
				'dynamic' => ['active'   => true,],
				'condition' => [
					'content_style' => 'stylist_list',
					'content_list_style' => 'style-1',
				],
			]
		);
		$this->add_control(
			'list_style_less_option',
			[
				'label' => esc_html__( 'Shrink Section Title', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( '- Less options', 'theplus' ),
				'description' => esc_html__( 'Expand and Shrink Options will be available only for more than 3 list items.', 'theplus' ),
				'dynamic' => ['active'   => true,],
				'condition' => [
					'content_style' => 'stylist_list',
					'content_list_style' => 'style-1',
				],
			]
		);
		$this->add_control(
			'content_wysiwyg_style',
			[
				'label' => esc_html__( 'Content Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'style-2'  => esc_html__( 'Style 2', 'theplus' ),
				],
				'condition' => [
					'content_style' => 'wysiwyg_content',
				],
			]
		);
		$this->add_control(
			'content_wysiwyg',
			[
				'label' => esc_html__( 'Content', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'theplus' ),
				'dynamic' => ['active'   => true,],
				'condition' => [
					'content_style' => 'wysiwyg_content',
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
            'button_section',
            [
                'label' => esc_html__('Button', 'theplus'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
		$this->add_control(
			'display_button',
			[
				'label' => esc_html__( 'Button', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
			]
		);
		$this->add_control(
            'button_style', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Button Style', 'theplus'),
                'default' => 'style-8',
                'options' => [
                    'style-7' => esc_html__('Style 1', 'theplus'),
                    'style-8' => esc_html__('Style 2', 'theplus'),
                    'style-9' => esc_html__('Style 3', 'theplus'),                    
                ],
				'condition' => [
					'display_button' => 'yes',
				],
            ]
        );
		$this->add_control(
			'button_text',
			[
				'label' => esc_html__( 'Button Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => esc_html__( 'Free Trial', 'theplus' ),
				'dynamic' => ['active'   => true,],
				'condition' => [
					'display_button' => 'yes',
				],
			]
		);
		$this->add_control(
			'button_link',
			[
				'label' => esc_html__( 'Button Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'default' => [
					'url' => '#',
				],
				'condition' => [
					'display_button' => 'yes',
				],
			]
		);
		$this->add_control(
			'button_icon_type',
			[
				'label' => esc_html__( 'Icon Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'icon',
				'options' => [
					'icon' => esc_html__( 'Icon', 'theplus' ),
					'lottie'  => esc_html__( 'Lottie', 'theplus' ),
				],
				'condition' => ['display_button' => 'yes'],
			]
		);
		$this->add_control(
			'lottieUrl',
			[
				'label' => esc_html__( 'Lottie URL', 'theplus' ),
				'type' => Controls_Manager::URL,				
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'condition' => [
					'display_button' => 'yes',
					'button_icon_type' => 'lottie',
				],
			]
		);
		$this->add_control(
			'lottieNote',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => 'Note: The <b>Lottie</b> option will only work for <b>Button Style 2 (Two)<b/>.',
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'display_button' => 'yes',
					'button_icon_type' => 'lottie',
				],
			]
		);
		$this->add_control(
			'button_icon_style',
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
				'condition' => [
					'display_button' => 'yes',
					'button_style!' => ['style-7','style-9'],
					'button_icon_type' => 'icon',
				],
			]
		);
		$this->add_control(
			'button_icon',
			[
				'label' => esc_html__( 'Icon', 'theplus' ),
				'type' => Controls_Manager::ICON,
				'label_block' => true,
				'default' => 'fa fa-chevron-right',
				'condition' => [
					'display_button' => 'yes',
					'button_style!' => ['style-7','style-9'],
					'button_icon_type' => 'icon',
					'button_icon_style' => 'font_awesome',
				],
			]
		);
		$this->add_control(
			'button_icon_5',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-chevron-right',
					'library' => 'solid',
				],
				'condition' => [
					'display_button' => 'yes',
					'button_style!' => ['style-7','style-9'],
					'button_icon_type' => 'icon',
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
					'display_button' => 'yes',
					'button_style!' => ['style-7','style-9'],
					'button_icon_type' => 'icon',
					'button_icon_style' => 'icon_mind',
				],
			]
		);
		$this->add_control(
			'before_after',
			[
				'label' => esc_html__( 'Icon Position', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'after',
				'options' => [
					'after' => esc_html__( 'After', 'theplus' ),
					'before' => esc_html__( 'Before', 'theplus' ),
				],
				'condition' => [
					'display_button' => 'yes',
					'button_style!' => ['style-7','style-9'],
					'button_icon_type' => ['icon','lottie'],
					'button_icon_style!' => '',
				],
			]
		);
		$this->add_control(
			'icon_spacing',
			[
				'label' => esc_html__( 'Icon Spacing', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'condition' => [
					'display_button' => 'yes',
					'button_style!' => ['style-7','style-9'],
					'button_icon_type' => 'icon',
					'button_icon_style!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .button-link-wrap i.button-after,{{WRAPPER}} .button-link-wrap .button-after i,{{WRAPPER}} .button-link-wrap span.btn-icon.button-after' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .button-link-wrap i.button-before,{{WRAPPER}} .button-link-wrap .button-before i,{{WRAPPER}} .button-link-wrap span.btn-icon.button-before' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]			
		);
		$this->end_controls_section();
		/* button content*/
		/*Call to Action*/
		$this->start_controls_section(
            'call_to_action_section',
            [
                'label' => esc_html__('Call to Action', 'theplus'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
		$this->add_control(
			'call_to_action_text',
			[
				'label' => esc_html__( 'Call To Action(CTA) Text', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => '',
				'dynamic' => ['active'   => true,],
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/*Call to Action*/
		/*Ribbon/pin */
		$this->start_controls_section(
            'ribbon_pin_section',
            [
                'label' => esc_html__('Ribbon', 'theplus'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
		$this->add_control(
			'display_ribbon_pin',
			[
				'label' => esc_html__( 'Display Ribbon', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),
				'default' => 'no',
			]
		);
		$this->add_control(
			'ribbon_pin_style',
			[
				'label' => esc_html__( 'Ribbon Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'style-2'  => esc_html__( 'Style 2', 'theplus' ),
					'style-3'  => esc_html__( 'Style 3', 'theplus' ),
				],
				'condition' => [
					'display_ribbon_pin' => 'yes',
				],
			]
		);
		$this->add_control(
			'ribbon_pin_text',
			[
				'label' => esc_html__( 'Ribbon/Pin Text', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Recommended', 'theplus' ),
				'dynamic' => ['active'   => true,],
				'condition' => [
					'display_ribbon_pin' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*Ribbon/pin */
		/*svg style*/
		$this->start_controls_section(
            'section_svg_styling',
            [
                'label' => esc_html__('SVG', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'image_icon' => 'svg',
				],
            ]
        );
		$this->add_control(
			'svg_type',
			[
				'label' => esc_html__( 'Select Style Image', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'delayed',
				'options' => theplus_svg_type(),
				'condition' => [
					'image_icon' => 'svg',
				],
			]
		);
		$this->add_control(
            'duration',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Duration', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'render_type' => 'ui',
				'condition' => [
					'image_icon' => 'svg',
				],
            ]
        );
		$this->add_control(
            'max_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Max Width Svg', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'render_type' => 'ui',
				'condition' => [
					'image_icon' => 'svg',
					'svg_icon' => ['svg','img'],
				],
            ]
        );
		$this->add_control(
			'border_stroke_color',
			[
				'label' => esc_html__( 'Border/Stoke Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ff0000',
				'condition' => [
					'image_icon' => 'svg',
				],
			]
		);
		$this->end_controls_section();
		/*svg style*/
		/* icons style */
		$this->start_controls_section(
            'section_icon_styling',
            [
                'label' => esc_html__('Icon', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'image_icon' => 'icon',
				],
            ]
        );
		$this->add_control(
			'icon_style',
			[
				'label' => esc_html__( 'Icon Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'square',
				'options' => [
					''  => esc_html__( 'None', 'theplus' ),
					'square' => esc_html__( 'Square', 'theplus' ),
					'rounded' => esc_html__( 'Rounded', 'theplus' ),
					'hexagon' => esc_html__( 'Hexagon', 'theplus' ),
					'pentagon' => esc_html__( 'Pentagon', 'theplus' ),
					'square-rotate' => esc_html__( 'Square Rotate', 'theplus' ),
				],
			]
		);
		$this->add_control(
            'icon_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 25,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-table-inner .pricing-icon' => 'font-size: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .plus-pricing-table .pricing-table-inner .pricing-icon svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_control(
            'icon_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Width', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 250,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-table-inner .pricing-icon' => 'width: {{SIZE}}{{UNIT}} !important;height: {{SIZE}}{{UNIT}} !important;line-height: {{SIZE}}{{UNIT}} !important;text-align: center;',
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_icon_style' );
		$this->start_controls_tab(
			'tab_icon_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'icon_color_option',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'solid' => [
						'title' => esc_html__( 'Classic', 'theplus' ),
						'icon' => 'eicon-paint-brush',
					],
					'gradient' => [
						'title' => esc_html__( 'Gradient', 'theplus' ),
						'icon' => 'eicon-barcode',
					],
				],
				'default' => 'solid',
				'label_block' => false,
			]
		);
		$this->add_control(
			'icon_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-table-inner .pricing-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .plus-pricing-table .pricing-table-inner .pricing-icon svg' => 'fill: {{VALUE}};stroke: {{VALUE}};',
				],
				'condition' => [
					'icon_color_option' => 'solid',
				],
				'separator' => 'after',
			]
		);
		$this->add_control(
            'icon_gradient_color1',
            [
                'label' => esc_html__('Color 1', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'orange',
				'condition' => [
					'icon_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'icon_gradient_color1_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 1 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'render_type' => 'ui',
				'condition' => [
					'icon_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'icon_gradient_color2',
            [
                'label' => esc_html__('Color 2', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'cyan',
				'condition' => [
					'icon_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'icon_gradient_color2_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 2 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 100,
					],
				'render_type' => 'ui',
				'condition' => [
					'icon_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'icon_gradient_style', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Gradient Style', 'theplus'),
                'default' => 'linear',
                'options' => theplus_get_gradient_styles(),
				'condition' => [
					'icon_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'icon_gradient_angle', [
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Gradient Angle', 'theplus'),
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
					'size' => 180,
				],
				'range' => [
					'deg' => [
						'step' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-table-inner .pricing-icon' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{icon_gradient_color1.VALUE}} {{icon_gradient_color1_control.SIZE}}{{icon_gradient_color1_control.UNIT}}, {{icon_gradient_color2.VALUE}} {{icon_gradient_color2_control.SIZE}}{{icon_gradient_color2_control.UNIT}})',
				],
				'condition'    => [
					'icon_color_option' => 'gradient',
					'icon_gradient_style' => ['linear']
				],
				'of_type' => 'gradient',
				'separator' => 'after',
			]
        );
		$this->add_control(
            'icon_gradient_position', [
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Position', 'theplus'),
				'options' => theplus_get_position_options(),
				'default' => 'center center',
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-table-inner .pricing-icon' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{icon_gradient_color1.VALUE}} {{icon_gradient_color1_control.SIZE}}{{icon_gradient_color1_control.UNIT}}, {{icon_gradient_color2.VALUE}} {{icon_gradient_color2_control.SIZE}}{{icon_gradient_color2_control.UNIT}})',
				],
				'condition' => [
					'icon_color_option' => 'gradient',
					'icon_gradient_style' => 'radial',
				],
				'of_type' => 'gradient',
				'separator' => 'after',
				
			]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'icon_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-pricing-table .pricing-table-inner .pricing-icon',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'icon_border_style',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => theplus_get_border_style(),
				'selectors'  => [
					'{{WRAPPER}} .plus-pricing-table .pricing-table-inner .pricing-icon' => 'border-style: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'icon_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-table-inner .pricing-icon' => 'border-color: {{VALUE}}',
				],				
			]
		);
		$this->add_responsive_control(
			'icon_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-pricing-table .pricing-table-inner .pricing-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_box_shadow',
				'selector' => '{{WRAPPER}} .plus-pricing-table .pricing-table-inner .pricing-icon',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_icon_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'icon_hover_color_option',
			[
				'label' => esc_html__( 'Icon Hover Color', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'solid' => [
						'title' => esc_html__( 'Classic', 'theplus' ),
						'icon' => 'eicon-paint-brush',
					],
					'gradient' => [
						'title' => esc_html__( 'Gradient', 'theplus' ),
						'icon' => 'eicon-barcode',
					],
				],
				'default' => 'solid',
				'label_block' => false,
			]
		);
		$this->add_control(
			'icon_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-table-inner:hover .pricing-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .plus-pricing-table .pricing-table-inner:hover .pricing-icon svg' => 'fill: {{VALUE}};stroke: {{VALUE}};',
				],
				'condition' => [
					'icon_hover_color_option' => 'solid',
				],
				'separator' => 'after',
			]
		);
		$this->add_control(
            'icon_hover_gradient_color1',
            [
                'label' => esc_html__('Color 1', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'orange',
				'condition' => [
					'icon_hover_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'icon_hover_gradient_color1_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 1 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'render_type' => 'ui',
				'condition' => [
					'icon_hover_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'icon_hover_gradient_color2',
            [
                'label' => esc_html__('Color 2', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'cyan',
				'condition' => [
					'icon_hover_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'icon_hover_gradient_color2_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 2 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 100,
					],
				'render_type' => 'ui',
				'condition' => [
					'icon_hover_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'icon_hover_gradient_style', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Gradient Style', 'theplus'),
                'default' => 'linear',
                'options' => theplus_get_gradient_styles(),
				'condition' => [
					'icon_hover_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'icon_hover_gradient_angle', [
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Gradient Angle', 'theplus'),
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
					'size' => 180,
				],
				'range' => [
					'deg' => [
						'step' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-table-inner:hover .pricing-icon' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{icon_hover_gradient_color1.VALUE}} {{icon_hover_gradient_color1_control.SIZE}}{{icon_hover_gradient_color1_control.UNIT}}, {{icon_hover_gradient_color2.VALUE}} {{icon_hover_gradient_color2_control.SIZE}}{{icon_hover_gradient_color2_control.UNIT}})',
				],
				'condition'    => [
					'icon_hover_color_option' => 'gradient',
					'icon_hover_gradient_style' => ['linear']
				],
				'of_type' => 'gradient',
				'separator' => 'after',
			]
        );
		$this->add_control(
            'icon_hover_gradient_position', [
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Position', 'theplus'),
				'options' => theplus_get_position_options(),
				'default' => 'center center',
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-table-inner:hover .pricing-icon' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{icon_hover_gradient_color1.VALUE}} {{icon_hover_gradient_color1_control.SIZE}}{{icon_hover_gradient_color1_control.UNIT}}, {{icon_hover_gradient_color2.VALUE}} {{icon_hover_gradient_color2_control.SIZE}}{{icon_hover_gradient_color2_control.UNIT}})',
				],
				'condition' => [
					'icon_hover_color_option' => 'gradient',
					'icon_hover_gradient_style' => 'radial',
				],
				'of_type' => 'gradient',
				'separator' => 'after',
			]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'icon_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-pricing-table .pricing-table-inner:hover .pricing-icon',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'icon_border_hover_color',
			[
				'label' => esc_html__( 'Hover Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-table-inner:hover .pricing-icon' => 'border-color: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'icon__hover_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-pricing-table .pricing-table-inner:hover .pricing-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_hover_box_shadow',
				'selector' => '{{WRAPPER}} .plus-pricing-table .pricing-table-inner:hover .pricing-icon',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();		
		$this->end_controls_section();		
		/*icon style*/
		/*lottie style*/
		$this->start_controls_section(
            'section_lottie_styling',
            [
                'label' => esc_html__('Lottie', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'display_button' => 'yes',
					'button_icon_type' => 'lottie',
				],
            ]
        );
		$this->add_control(
            'lottiedisplay', 
			[
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Display', 'theplus'),
                'default' => 'inline-block',
                'options' => [
					'block'  => esc_html__( 'Block', 'theplus' ),
					'inline-block'  => esc_html__( 'Inline Block', 'theplus' ),
					'flex'  => esc_html__( 'Flex', 'theplus' ),
					'inline-flex'  => esc_html__( 'Inline Flex', 'theplus' ),
					'initial'  => esc_html__( 'Initial', 'theplus' ),
					'inherit'  => esc_html__( 'Inherit', 'theplus' ),
				],
            ]
        );
		$this->add_responsive_control(
            'lottieMright',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Margin Right', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'render_type' => 'ui',
				'condition' => ['before_after' => 'before'],
            ]
        );
		$this->add_responsive_control(
            'lottieMleft',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Margin Left', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'render_type' => 'ui',
				'condition' => ['before_after' => 'after'],
            ]
        );
		$this->add_responsive_control(
			'lottieWidth',
			[
				'label' => esc_html__( 'Width', 'theplus' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 700,
                        'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 25,
				],
			]
		);
		$this->add_responsive_control(
			'lottieHeight',
			[
				'label' => esc_html__( 'Height', 'theplus' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 700,
                        'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 25,
				],
			]
		);
		$this->add_control(
			'lottieVertical',
			[
				'label' => esc_html__( 'Vertical Align', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'middle',
				'options' => [
					'top'  => esc_html__( 'Top', 'theplus' ),
					'middle'  => esc_html__( 'Middle', 'theplus' ),
					'bottom'  => esc_html__( 'Bottom', 'theplus' ),
				],
			]
		);
		$this->add_responsive_control(
			'lottieSpeed',
			[
				'label' => esc_html__( 'Speed', 'theplus' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
                        'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
			]
		);
		$this->add_control(
			'lottieLoop',
			[
				'label' => esc_html__( 'Loop Animation', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'lottiehover',
			[
				'label' => esc_html__( 'Hover Animation', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/*lottie style*/
		/*title style*/
		$this->start_controls_section(
            'section_title_styling',
            [
                'label' => esc_html__('Title', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'pricing_title!' => '',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-pricing-table .pricing-title',
			]
		);
		$this->add_responsive_control('title_alignment',
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
					'{{WRAPPER}} .plus-pricing-table.pricing-style-1 .pricing-title,
					{{WRAPPER}} .plus-pricing-table.pricing-style-2 .pricing-title-wrap .pricing-title,
					{{WRAPPER}} .plus-pricing-table.pricing-style-3 .pricing-title-wrap .pricing-title' => 'text-align: {{VALUE}}',
				],				
			]
		);
		$this->start_controls_tabs( 'tabs_title_style' );
		$this->start_controls_tab(
			'tab_title_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'title_color_option',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'solid' => [
						'title' => esc_html__( 'Classic', 'theplus' ),
						'icon' => 'eicon-paint-brush',
					],
					'gradient' => [
						'title' => esc_html__( 'Gradient', 'theplus' ),
						'icon' => 'eicon-barcode',
					],
				],
				'label_block' => false,
				'default' => 'solid',
			]
		);
		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-title' => 'color: {{VALUE}}',
				],
				'condition' => [
					'title_color_option' => 'solid',
				],
			]
		);
		$this->add_control(
            'title_gradient_color1',
            [
                'label' => esc_html__('Color 1', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'orange',
				'condition' => [
					'title_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'title_gradient_color1_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 1 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'render_type' => 'ui',
				'condition' => [
					'title_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'title_gradient_color2',
            [
                'label' => esc_html__('Color 2', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'cyan',
				'condition' => [
					'title_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'title_gradient_color2_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 2 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 100,
					],
				'render_type' => 'ui',
				'condition' => [
					'title_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'title_gradient_style', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Gradient Style', 'theplus'),
                'default' => 'linear',
                'options' => theplus_get_gradient_styles(),
				'condition' => [
					'title_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'title_gradient_angle', [
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Gradient Angle', 'theplus'),
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
					'size' => 180,
				],
				'range' => [
					'deg' => [
						'step' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-title' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{title_gradient_color1.VALUE}} {{title_gradient_color1_control.SIZE}}{{title_gradient_color1_control.UNIT}}, {{title_gradient_color2.VALUE}} {{title_gradient_color2_control.SIZE}}{{title_gradient_color2_control.UNIT}})',
				],
				'condition'    => [
					'title_color_option' => 'gradient',
					'title_gradient_style' => ['linear']
				],
				'of_type' => 'gradient',
			]
        );
		$this->add_control(
            'title_gradient_position', [
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Position', 'theplus'),
				'options' => theplus_get_position_options(),
				'default' => 'center center',
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-title' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{title_gradient_color1.VALUE}} {{title_gradient_color1_control.SIZE}}{{title_gradient_color1_control.UNIT}}, {{title_gradient_color2.VALUE}} {{title_gradient_color2_control.SIZE}}{{title_gradient_color2_control.UNIT}})',
				],
				'condition' => [
					'title_color_option' => 'gradient',
					'title_gradient_style' => 'radial',
			],
			'of_type' => 'gradient',
			]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_title_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'title_hover_color_option',
			[
				'label' => esc_html__( 'Title Hover Color', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'solid' => [
						'title' => esc_html__( 'Classic', 'theplus' ),
						'icon' => 'eicon-paint-brush',
					],
					'gradient' => [
						'title' => esc_html__( 'Gradient', 'theplus' ),
						'icon' => 'eicon-barcode',
					],
				],
				'label_block' => false,
				'default' => 'solid',
			]
		);
		$this->add_control(
			'title_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-table-inner:hover .pricing-title' => 'color: {{VALUE}}',
				],
				'condition' => [
					'title_hover_color_option' => 'solid',
				],
			]
		);
		$this->add_control(
            'title_hover_gradient_color1',
            [
                'label' => esc_html__('Color 1', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'orange',
				'condition' => [
					'title_hover_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'title_hover_gradient_color1_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 1 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'render_type' => 'ui',
				'condition' => [
					'title_hover_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'title_hover_gradient_color2',
            [
                'label' => esc_html__('Color 2', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'cyan',
				'condition' => [
					'title_hover_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'title_hover_gradient_color2_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 2 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 100,
					],
				'render_type' => 'ui',
				'condition' => [
					'title_hover_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'title_hover_gradient_style', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Gradient Style', 'theplus'),
                'default' => 'linear',
                'options' => theplus_get_gradient_styles(),
				'condition' => [
					'title_hover_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'title_hover_gradient_angle', [
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Gradient Angle', 'theplus'),
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
					'size' => 180,
				],
				'range' => [
					'deg' => [
						'step' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-table-inner:hover .pricing-title' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{title_hover_gradient_color1.VALUE}} {{title_hover_gradient_color1_control.SIZE}}{{title_hover_gradient_color1_control.UNIT}}, {{title_hover_gradient_color2.VALUE}} {{title_hover_gradient_color2_control.SIZE}}{{title_hover_gradient_color2_control.UNIT}})',
				],
				'condition'    => [
					'title_hover_color_option' => 'gradient',
					'title_hover_gradient_style' => ['linear']
				],
				'of_type' => 'gradient',
			]
        );
		$this->add_control(
            'title_hover_gradient_position', [
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Position', 'theplus'),
				'options' => theplus_get_position_options(),
				'default' => 'center center',
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-table-inner:hover .pricing-title' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{title_hover_gradient_color1.VALUE}} {{title_hover_gradient_color1_control.SIZE}}{{title_hover_gradient_color1_control.UNIT}}, {{title_hover_gradient_color2.VALUE}} {{title_hover_gradient_color2_control.SIZE}}{{title_hover_gradient_color2_control.UNIT}})',
				],
				'condition' => [
					'title_hover_color_option' => 'gradient',
					'title_hover_gradient_style' => 'radial',
				],
			'of_type' => 'gradient',
			]
        );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*title style*/
		/*subtitle style*/
		$this->start_controls_section(
            'section_subtitle_styling',
            [
                'label' => esc_html__('SubTitle', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'pricing_subtitle!' => '',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'subtitle_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-pricing-table .pricing-table-inner .pricing-subtitle',
			]
		);
		$this->add_responsive_control('subtitle_alignment',
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
					'{{WRAPPER}} .plus-pricing-table.pricing-style-1 .pricing-subtitle,{{WRAPPER}} .plus-pricing-table.pricing-style-2 .pricing-subtitle,{{WRAPPER}} .plus-pricing-table.pricing-style-3 .pricing-subtitle' => 'text-align: {{VALUE}}',
				],				
			]
		);

		$this->start_controls_tabs( 'tabs_subtitle_style' );
		$this->start_controls_tab('tab_subtitle_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control('subtitle_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-table-inner .pricing-subtitle' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('tab_subtitle_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control('subtitle_Hover_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-table-inner:hover .pricing-subtitle' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*subtitle style*/
		/*Previous Price Style*/
		$this->start_controls_section(
            'section_previous_price_styling',
            [
                'label' => esc_html__('Previous Price', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_previous_price' => 'yes',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'previous_price_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-pricing-table .pricing-previous-price-wrap',
			]
		);
		$this->add_control(
			'previous_price_align',
			[
				'label' => esc_html__( 'Price Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => esc_html__( 'Top', 'theplus' ),
						'icon' => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => esc_html__( 'Middle', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'theplus' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'default' => 'top',
				'toggle' => true,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-previous-price-wrap' => 'vertical-align: {{VALUE}};',
				],
			]
		);
		$this->start_controls_tabs( 'previous_price_style_tab' );
		$this->start_controls_tab(
			'previous_price_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'previous_price_color',
			[
				'label' => esc_html__( 'Price Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-previous-price-wrap' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'previous_price_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'previous_price_hover_color',
			[
				'label' => esc_html__( 'Price Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-table-inner:hover .pricing-previous-price-wrap' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Previous Price Style*/		
		/*Price Style */
		$this->start_controls_section(
            'section_price_styling',
            [
                'label' => esc_html__('Price', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
			'prefix_price_style_heading',
			[
				'label' => esc_html__( 'Prefix', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'price_style' => ['style-2','style-3']
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'prefix_price_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-pricing-table .pricing-price-wrap span.price-prefix-text',
				'condition' => [
					'price_style' => ['style-2','style-3']
				],
			]
		);
		$this->add_control(
			'prefix_price_color',
			[
				'label' => esc_html__( 'Prefix Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-price-wrap span.price-prefix-text' => 'color: {{VALUE}};',
				],
				'condition' => [
					'price_style' => ['style-2','style-3']
				],
			]
		);
		$this->add_control(
			'prefix_price_hover_color',
			[
				'label' => esc_html__( 'Prefix Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-table-inner:hover .pricing-price-wrap span.price-prefix-text' => 'color: {{VALUE}};',
				],
				'separator' => 'after',
				'condition' => [
					'price_style' => ['style-2','style-3']
				],
			]
		);
		$this->add_control(
			'price_style_heading',
			[
				'label' => esc_html__( 'Price Main', 'theplus' ),
				'type' => Controls_Manager::HEADING,				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'price_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-pricing-table .pricing-price-wrap.style-1 span.price-prefix-text,{{WRAPPER}} .plus-pricing-table .pricing-price-wrap.style-1 .pricing-price,{{WRAPPER}} .plus-pricing-table .pricing-price-wrap.style-2 .pricing-price,{{WRAPPER}} .plus-pricing-table .pricing-price-wrap.style-3 .pricing-price',
			]
		);
		$this->add_responsive_control('price_alignment',
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
					'{{WRAPPER}} .plus-pricing-table.pricing-style-1 .pricing-price-wrap,{{WRAPPER}} .plus-pricing-table.pricing-style-2 .pricing-price-wrap ,{{WRAPPER}} .plus-pricing-table.pricing-style-3 .pricing-price-wrap ' => 'text-align: {{VALUE}}',
				],				
			]
		);
		$this->start_controls_tabs( 'price_style_tab' );
		$this->start_controls_tab(
			'price_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'price_color',
			[
				'label' => esc_html__( 'Price Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-price-wrap.style-1 span.price-prefix-text,{{WRAPPER}} .plus-pricing-table .pricing-price-wrap.style-1 .pricing-price,{{WRAPPER}} .plus-pricing-table .pricing-price-wrap.style-2 .pricing-price,{{WRAPPER}} .plus-pricing-table .pricing-price-wrap.style-3 .pricing-price' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'price_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'price_hover_color',
			[
				'label' => esc_html__( 'Price Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-table-inner:hover .pricing-price-wrap.style-1 span.price-prefix-text,{{WRAPPER}} .plus-pricing-table .pricing-table-inner:hover .pricing-price-wrap.style-1 .pricing-price,{{WRAPPER}} .plus-pricing-table .pricing-table-inner:hover .pricing-price-wrap.style-2 .pricing-price,{{WRAPPER}} .plus-pricing-table .pricing-table-inner:hover .pricing-price-wrap.style-3 .pricing-price' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'price_postfix_style_heading',
			[
				'label' => esc_html__( 'Postfix', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'price_postfix_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-pricing-table .pricing-price-wrap.style-1 span.price-postfix-text,{{WRAPPER}} .plus-pricing-table .pricing-price-wrap.style-2 span.price-postfix-text,{{WRAPPER}} .plus-pricing-table .pricing-price-wrap.style-3 span.price-postfix-text',
			]
		);
		$this->start_controls_tabs( 'price_postfix_style_tab' );
		$this->start_controls_tab(
			'price_postfix_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		
		$this->add_control(
			'price_postfix_color',
			[
				'label' => esc_html__( 'Postfix Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-price-wrap span.price-postfix-text' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'price_postfix_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'price_postfix_hover_color',
			[
				'label' => esc_html__( 'Postfix Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-table-inner:hover .pricing-price-wrap span.price-postfix-text' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Price Style */
		/*Content style*/
		$this->start_controls_section(
            'section_content_styling',
            [
                'label' => esc_html__('Content', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.content-desc .pricing-content',
				'condition' => [
					'content_style' => 'wysiwyg_content',
				],
			]
		);
		$this->add_control(
			'content_text_color',
			[
				'label' => esc_html__( 'Content Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.content-desc .pricing-content,{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.content-desc .pricing-content p' => 'color: {{VALUE}};',
				],
				'condition' => [
					'content_style' => 'wysiwyg_content',
				],
			]
		);
		$this->add_control(
			'content_text_color_hover_active',
			[
				'label' => esc_html__( 'Box Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table:hover .pricing-content-wrap.content-desc .pricing-content,{{WRAPPER}} .plus-pricing-table:hover .pricing-content-wrap.content-desc .pricing-content p' => 'color: {{VALUE}};',
				],
				'condition' => [
					'content_style' => 'wysiwyg_content',
				],
			]
		);
		$this->add_control(
			'content_text_color_hover',
			[
				'label' => esc_html__( 'Content Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.content-desc .pricing-content:hover,{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.content-desc .pricing-content:hover p' => 'color: {{VALUE}};',
				],
				'condition' => [
					'content_style' => 'wysiwyg_content',
				],
			]
		);		
		$this->add_control(
			'content_border_width_color',
			[
				 'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Border Width', 'theplus'),
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 2,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.content-desc.style-1 hr.border-line' => 'margin: 30px {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'content_style' => 'wysiwyg_content',
					'content_wysiwyg_style' => 'style-1'
				],
			]
		);
		$this->add_control(
			'content_border_top_color',
			[
				'label' => esc_html__( 'Border Top Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.content-desc.style-1 hr.border-line' => 'border-top:1px solid;border-top-color: {{VALUE}};',
				],
				'condition' => [
					'content_style' => 'wysiwyg_content',
					'content_wysiwyg_style' => 'style-1'
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'list_content_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-pricing-table ul.plus-icon-list-items span.plus-icon-list-text',
				'condition' => [
					'content_style' => 'stylist_list',
				],
			]
		);
		$this->add_responsive_control('desc_content_alignment',
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
					'{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.content-desc .pricing-content,{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.content-desc .pricing-content p' => 'text-align: {{VALUE}}',
				],
				'condition' => [
					'content_style' => 'wysiwyg_content',
				],
				'default' => '',
				'toggle' => true,				
			]
		);
		$this->add_responsive_control('listing_content_alignment',
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
					'{{WRAPPER}} .plus-pricing-table ul.plus-icon-list-items li' => 'justify-content: {{VALUE}}',
				],
				'condition' => [
					'content_style' => 'stylist_list',
				],
				'default' => '',
				'toggle' => true,				
			]
		);
		$this->add_control('list_icon_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('List Icon Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 14,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.listing-content li span.plus-icon-list-icon' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.listing-content li span.plus-icon-list-icon svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'content_style' => 'stylist_list',
				],
            ]
        );
		$this->add_responsive_control('content_spg',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .pricing-content-wrap.listing-content.style-1 ul.plus-icon-list-items, {{WRAPPER}} .pricing-content-wrap.listing-content.style-2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs( 'list_content_style_tab' );
		$this->start_controls_tab(
			'list_content_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'content_style' => 'stylist_list',
				],
			]
		);
		$this->add_control(
			'list_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table ul.plus-icon-list-items span.plus-icon-list-text,{{WRAPPER}} .plus-pricing-table ul.plus-icon-list-items span.plus-icon-list-text p' => 'color: {{VALUE}};',
				],
				'condition' => [
					'content_style' => 'stylist_list',
				],
			]
		);
		$this->add_control(
			'list_icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table ul.plus-icon-list-items span.plus-icon-list-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .plus-pricing-table ul.plus-icon-list-items span.plus-icon-list-icon svg' => 'fill: {{VALUE}};stroke: {{VALUE}};',
				],
				'condition' => [
					'content_style' => 'stylist_list',
				],
			]
		);
		$this->add_control(
			'list_style_2_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.listing-content.style-2 li' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'content_style' => 'stylist_list',
					'content_list_style' => 'style-2'
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'list_content_hover_box',
			[
				'label' => esc_html__( 'Box Hover', 'theplus' ),
				'condition' => [
					'content_style' => 'stylist_list',
				],
			]
		);
		$this->add_control(
			'list_text_hover_color_box',
			[
				'label' => esc_html__( 'Hover Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table:hover .pricing-content-wrap.listing-content ul.plus-icon-list-items li span.plus-icon-list-text,{{WRAPPER}} .plus-pricing-table:hover .pricing-content-wrap.listing-content ul.plus-icon-list-items li span.plus-icon-list-text p' => 'color: {{VALUE}};',
				],
				'condition' => [
					'content_style' => 'stylist_list',
				],
			]
		);
		$this->add_control(
			'list_icon_hover_color_box',
			[
				'label' => esc_html__( 'Hover Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table:hover .pricing-content-wrap.listing-content ul.plus-icon-list-items li span.plus-icon-list-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .plus-pricing-table:hover .pricing-content-wrap.listing-content ul.plus-icon-list-items li span.plus-icon-list-icon svg' => 'fill: {{VALUE}};stroke: {{VALUE}};',
				],
				'condition' => [
					'content_style' => 'stylist_list',
				],
			]
		);
		$this->add_control(
			'list_style2_hover_border_color_box',
			[
				'label' => esc_html__( 'Hover Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table:hover .pricing-content-wrap.listing-content.style-2 ul li' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'content_style' => 'stylist_list',
					'content_list_style' => 'style-2'
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'list_content_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'content_style' => 'stylist_list',
				],
			]
		);
		$this->add_control(
			'list_text_hover_color',
			[
				'label' => esc_html__( 'Hover Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.listing-content ul.plus-icon-list-items li:hover span.plus-icon-list-text,{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.listing-content ul.plus-icon-list-items li:hover span.plus-icon-list-text p' => 'color: {{VALUE}};',
				],
				'condition' => [
					'content_style' => 'stylist_list',
				],
			]
		);
		$this->add_control(
			'list_icon_hover_color',
			[
				'label' => esc_html__( 'Hover Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.listing-content ul.plus-icon-list-items li:hover span.plus-icon-list-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.listing-content ul.plus-icon-list-items li:hover span.plus-icon-list-icon svg' => 'fill: {{VALUE}};stroke: {{VALUE}};',
				],
				'condition' => [
					'content_style' => 'stylist_list',
				],
			]
		);
		$this->add_control(
			'list_style2_hover_border_color',
			[
				'label' => esc_html__( 'Hover Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.listing-content.style-2 ul li:hover' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'content_style' => 'stylist_list',
					'content_list_style' => 'style-2'
				],
			]
		);
		$this->end_controls_tab();		
		$this->end_controls_tabs();
		$this->add_control(
            'list_between_space',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('List Between Space', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 2,
					],
					],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.listing-content.style-1 li' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.listing-content.style-2 li' => 'padding: {{SIZE}}{{UNIT}} 0',
				],
				'condition' => [
					'content_style' => 'stylist_list',
				],
            ]
        );
		$this->add_responsive_control('icon_between_space',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Spacing', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 2,
					],
					],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .plus-icon-list-items .plus-icon-list-item' => 'margin-right: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'content_style' => 'stylist_list',
				],
            ]
        );
		
		$this->add_control(
			'toggle_expand_options',
			[
				'label' => esc_html__( 'Toggle Read More', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'content_style' => 'stylist_list',
					'content_list_style' => 'style-1'
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'toggle_expand_typography',
				'label' => esc_html__( 'Expand/Toggle Text Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.listing-content.style-1 a.read-more-options',
				'condition' => [
					'content_style' => 'stylist_list',
					'content_list_style' => 'style-1'
				],
			]
		);
		$this->add_control(
			'toggle_expand_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.listing-content.style-1 a.read-more-options' => 'color: {{VALUE}};',
				],
				'condition' => [
					'content_style' => 'stylist_list',
					'content_list_style' => 'style-1'
				],
			]
		);
		$this->add_control(
			'toggle_expand_border_top',
			[
				'label' => esc_html__( 'Border Top Style', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'toggle_expand_border_top' => 'yes',
					'content_style' => 'stylist_list',
					'content_list_style' => 'style-1'
				],
			]
		);
		$this->add_control(
			'toggle_expand_border_top_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.listing-content.style-1 a.read-more-options' => 'border-top:1px solid;border-top-color: {{VALUE}};',
				],
				'condition' => [
					'toggle_expand_border_top' => 'yes',
					'content_style' => 'stylist_list',
					'content_list_style' => 'style-1'
				],
			]
		);
		$this->end_controls_section();
		/*Content style*/
		/*Content background style*/
		$this->start_controls_section(
            'section_content_bg_styling',
            [
                'label' => esc_html__('Content Background', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'content_style' => 'stylist_list',
					'content_list_style' => 'style-1'
				],
            ]
        );
		$this->add_control(
			'content_box_border',
			[
				'label' => esc_html__( 'Content Box Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'separator' => 'before',
				'default' => 'no',
			]
		);
		$this->start_controls_tabs( 'content_border_style' );
		$this->start_controls_tab(
			'content_border_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'content_box_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'content_box_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#eee',
				'selectors'  => [
					'{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.listing-content.style-1 ul.plus-icon-list-items,{{WRAPPER}} .pricing-content-wrap.listing-content.style-1 a.read-more-options' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'content_box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'content_box_border_width',
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
					'{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.listing-content.style-1 ul.plus-icon-list-items,{{WRAPPER}} .pricing-content-wrap.listing-content.style-1 a.read-more-options' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'content_box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'content_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.listing-content.style-1 ul.plus-icon-list-items,{{WRAPPER}} .pricing-content-wrap.listing-content.style-1 a.read-more-options,{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.listing-content.style-1 .content-overlay-bg-color' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'content_box_border' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'content_border_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'content_box_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'content_box_border_hover_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.listing-content.style-1:hover ul.plus-icon-list-items,{{WRAPPER}} .pricing-content-wrap.listing-content.style-1:hover a.read-more-options' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'content_box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'content_border_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.listing-content.style-1:hover ul.plus-icon-list-items,{{WRAPPER}} .pricing-content-wrap.listing-content.style-1:hover a.read-more-options,{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.listing-content.style-1:hover .content-overlay-bg-color' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'content_box_border' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'content_background_options',
			[
				'label' => esc_html__( 'Background Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs( 'content_background_style' );
		$this->start_controls_tab(
			'content_background_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'content_box_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.listing-content.style-1 .content-overlay-bg-color',
				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'content_background_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'content_box_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.listing-content.style-1:hover .content-overlay-bg-color',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'content_shadow_options',
			[
				'label' => esc_html__( 'Box Shadow Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs( 'content_shadow_style' );
		$this->start_controls_tab(
			'content_shadow_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'content_box_shadow',
				'selector' => '{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.listing-content.style-1 .content-overlay-bg-color',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'content_shadow_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'content_box_hover_shadow',
				'selector' => '{{WRAPPER}} .plus-pricing-table .pricing-content-wrap.listing-content.style-1:hover .content-overlay-bg-color',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Content background style*/
		$this->start_controls_section(
            'section_tooltip_option_styling',
            [
                'label' => esc_html__('Tooltip Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			\Theplus_Tooltips_Option_Group::get_type(),
			[
				'label' => esc_html__( 'Tooltip Options', 'theplus' ),
				'name'           => 'tooltip_common_option',
				'condition' => [
					'content_style' => 'stylist_list',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			\Theplus_Tooltips_Option_Style_Group::get_type(),
			[
				'label' => esc_html__( 'Tooltip Style', 'theplus' ),
				'name'           => 'tooltip_common_style',
				'condition' => [
					'content_style' => 'stylist_list',
				],
			]
		);
		$this->add_control(
			'tt_on_icon',
			[
				'label' => esc_html__( 'Tooltip Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-info-circle',
					'library' => 'solid',
				],
				'condition' => [
					'content_style' => 'stylist_list',
				],
			]
		);
		$this->add_control(
            'tt_on_icon_margin_left',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Left Offset', 'theplus'),
				'range' => [
					'' => [
						'min'	=> 1,
						'max'	=> 50,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'render_type' => 'ui',
				'selectors'  => [
					'{{WRAPPER}} .tp-tooltip-on-icon' => 'margin-left: {{SIZE}}px;',
				],
				'condition' => [
					'content_style' => 'stylist_list',
				],
            ]
        );
		$this->add_control(
			'tt_on_icon_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-tooltip-on-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tp-tooltip-on-icon svg' => 'fill: {{VALUE}};stroke: {{VALUE}};',
				],
				'condition' => [
					'content_style' => 'stylist_list',
				],
			]
		);
		$this->add_control(
            'tt_on_icon_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'range' => [
					'' => [
						'min'	=> 1,
						'max'	=> 50,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors'  => [
					'{{WRAPPER}} .tp-tooltip-on-icon i' => 'font-size: {{SIZE}}px;',
					'{{WRAPPER}} .tp-tooltip-on-icon svg' => 'width: {{SIZE}}px;height: {{SIZE}}px;',
				],
				'condition' => [
					'content_style' => 'stylist_list',
				],
            ]
        );
		$this->end_controls_section();
		/*button style*/
		$this->start_controls_section(
            'section_button_styling',
            [
                'label' => esc_html__('Button', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'display_button' => 'yes',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .pt_plus_button .button-link-wrap',
			]
		);
		$this->add_responsive_control('button_alignment',
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
					'{{WRAPPER}} .plus-pricing-table.pricing-style-1 .pt-plus-button-wrapper,{{WRAPPER}} .plus-pricing-table.pricing-style-2 .pt-plus-button-wrapper,{{WRAPPER}} .plus-pricing-table.pricing-style-3 .pt-plus-button-wrapper ' => 'text-align: {{VALUE}}',
				],				
			]
		);
		
		$this->add_control(
            'button_top_space',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Button Above Space', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-button-wrapper' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'display_button' => 'yes',
				],
            ]
        );
		$this->add_control(
            'button_svg_icon',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Button Svg Icon', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 2,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button .button-link-wrap svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};padding-left:5px;',
				],
            ]
        );
		$this->add_responsive_control(
			'button_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],
				'default' => [
					'top' => '8',
					'right' => '35',
					'bottom' => '8',
					'left' => '35',
					'isLinked' => false
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button .button-link-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
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
			'btn_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button .button-link-wrap' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pt_plus_button.button-style-7 .button-link-wrap:after' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'btn_svg_icon_color',
			[
				'label' => esc_html__( 'Svg Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button .button-link-wrap svg' => 'fill: {{VALUE}};stroke: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap',
				'separator' => 'after',
				'condition' => [
					'button_style!' => ['style-7','style-9'],
				],
			]
		);
		$this->add_control(
			'button_border_style',
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
					'{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'button_style' => ['style-8'],
				],
			]
		);
		$this->add_responsive_control(
			'button_border_width',
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
					'{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'button_style' => ['style-8'],
					'button_border_style!' => 'none',
				]
			]
		);
		$this->add_control(
			'button_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#313131',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'button_style' => ['style-8'],
					'button_border_style!' => 'none'
				],
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'button_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'button_style' => ['style-8'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_shadow',
				'selector' => '
							   {{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap',
				'condition' => [
					'button_style' => ['style-8'],
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_box_hover',
			[
				'label' => esc_html__( 'Box Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'btn_text_box_hover_color',
			[
				'label' => esc_html__( 'Text Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table:hover .button-link-wrap' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'btn_svg_icon_color_box_h',
			[
				'label' => esc_html__( 'Svg Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table:hover .pt_plus_button .button-link-wrap svg' => 'fill: {{VALUE}};stroke: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'box_hover_btn_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-pricing-table:hover .pt_plus_button.button-style-8 .button-link-wrap',
				'separator' => 'after',
				'condition' => [
					'button_style!' => ['style-7','style-9'],
				],
			]
		);
		$this->add_control(
			'btn_border_box_hover_color',
			[
				'label'     => esc_html__( 'Hover Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#313131',
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table:hover .pt_plus_button.button-style-8 .button-link-wrap' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'button_style' => ['style-8'],
					'button_border_style!' => 'none'
				],
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'box_hover_btn_radius',
			[
				'label'      => esc_html__( 'Hover Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-pricing-table:hover .pt_plus_button.button-style-8 .button-link-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'button_style' => ['style-8'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_hover_btn_shadow',
				'selector' => '{{WRAPPER}} .plus-pricing-table:hover .pt_plus_button.button-style-8 .button-link-wrap',
				'condition' => [
					'button_style' => ['style-8'],
				],
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
			'btn_text_hover_color',
			[
				'label' => esc_html__( 'Text Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button .button-link-wrap:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'btn_svg_icon_color_h',
			[
				'label' => esc_html__( 'Svg Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button .button-link-wrap:hover svg' => 'fill: {{VALUE}};stroke: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap:hover',
				'separator' => 'after',
				'condition' => [
					'button_style!' => ['style-7','style-9'],
				],
			]
		);
		$this->add_control(
			'button_border_hover_color',
			[
				'label'     => esc_html__( 'Hover Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#313131',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'button_style' => ['style-8'],
					'button_border_style!' => 'none'
				],
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'button_hover_radius',
			[
				'label'      => esc_html__( 'Hover Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'button_style' => ['style-8'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_hover_shadow',
				'selector' => '{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap:hover',
				'condition' => [
					'button_style' => ['style-8'],
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		$this->start_controls_section(
            'section_call_to_action_styling',
            [
                'label' => esc_html__('Call To Action(CTA)', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cta_typography',
				'selector' => '{{WRAPPER}} .plus-pricing-table .pricing-table-inner .pricing-cta-text',				
			]
		);
		$this->add_control(
			'cta_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#313131',
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-table-inner .pricing-cta-text,{{WRAPPER}} .plus-pricing-table .pricing-table-inner .pricing-cta-text p' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/*button style*/
		/*Ribbon Style*/
		$this->start_controls_section(
            'section_ribbon_pin_styling',
            [
                'label' => esc_html__('Ribbon/Pin', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'display_ribbon_pin' => 'yes',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ribbon_pin_typography',
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
				'selector' => '{{WRAPPER}} .plus-pricing-table .pricing-ribbon-pin .ribbon-pin-inner',
			]
		);
		$this->add_control(
			'ribbon_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-ribbon-pin .ribbon-pin-inner,{{WRAPPER}} .plus-pricing-table .pricing-ribbon-pin .ribbon-pin-inner p' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'ribbon_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-pricing-table .pricing-ribbon-pin.style-1 .ribbon-pin-inner,{{WRAPPER}} .plus-pricing-table .pricing-ribbon-pin.style-2,{{WRAPPER}} .plus-pricing-table .pricing-ribbon-pin.style-3' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'ribbon_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-pricing-table .pricing-ribbon-pin.style-1 .ribbon-pin-inner,{{WRAPPER}} .plus-pricing-table .pricing-ribbon-pin.style-2',
				'separator' => 'before',
				'condition' => [
					'ribbon_pin_style' => ['style-1','style-2'],
				],
			]
		);
		$this->add_control(
			'ribbon_bg_style_3',
			[
				'label'     => esc_html__( 'Background Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#212121',
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-ribbon-pin.style-3' => 'background: {{VALUE}};',
					'{{WRAPPER}} .plus-pricing-table .pricing-ribbon-pin.style-3:after' => 'border-top-color: {{VALUE}};border-left-color: {{VALUE}};',
				],
				'condition' => [
					'ribbon_pin_style' => ['style-3'],
				],
			]
		);
		$this->add_responsive_control(
			'ribbon_pin_width',
			[
				'label' => esc_html__( 'Max-Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 120,
				],
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-ribbon-pin.style-2' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ribbon_pin_style' => 'style-2',
				],
			]
		);
		$this->add_responsive_control(
			'ribbon_pin_adjust',
			[
				'label' => esc_html__( 'Adjust Pin Text', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table .pricing-ribbon-pin.style-2 .ribbon-pin-inner' => 'margin-top: -{{SIZE}}{{UNIT}};margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ribbon_pin_style' => 'style-2',
				],
			]
		);
		$this->end_controls_section();
		/*Ribbon Style*/
		/*background option*/
		$this->start_controls_section(
            'section_bg_option_styling',
            [
                'label' => esc_html__('Background Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
			'bg_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table.pricing-style-1 .pricing-table-inner,{{WRAPPER}} .plus-pricing-table.pricing-style-2 .pricing-table-inner,{{WRAPPER}} .plus-pricing-table.pricing-style-3 .pricing-top-part' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'bg_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .plus-pricing-table.pricing-style-1 .pricing-table-inner,{{WRAPPER}} .plus-pricing-table.pricing-style-2 .pricing-table-inner,{{WRAPPER}} .plus-pricing-table.pricing-style-3 .pricing-top-part' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
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
					'{{WRAPPER}} .plus-pricing-table.pricing-style-1 .pricing-table-inner,{{WRAPPER}} .plus-pricing-table.pricing-style-2 .pricing-table-inner,{{WRAPPER}} .plus-pricing-table.pricing-style-3 .pricing-top-part' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .plus-pricing-table.pricing-style-1 .pricing-table-inner,{{WRAPPER}} .plus-pricing-table.pricing-style-2 .pricing-table-inner,{{WRAPPER}} .plus-pricing-table.pricing-style-3 .pricing-top-part' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .plus-pricing-table.pricing-style-1 .pricing-table-inner,{{WRAPPER}} .plus-pricing-table.pricing-style-2 .pricing-table-inner,{{WRAPPER}} .plus-pricing-table.pricing-style-3 .pricing-top-part' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'label' => esc_html__( 'Hover', 'theplus' ),
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
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .plus-pricing-table.pricing-style-1:hover .pricing-table-inner,{{WRAPPER}} .plus-pricing-table.pricing-style-2:hover .pricing-table-inner,{{WRAPPER}} .plus-pricing-table.pricing-style-3:hover .pricing-top-part' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .plus-pricing-table.pricing-style-1:hover .pricing-table-inner,{{WRAPPER}} .plus-pricing-table.pricing-style-2:hover .pricing-table-inner,{{WRAPPER}} .plus-pricing-table.pricing-style-3:hover .pricing-top-part' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'background_options',
			[
				'label' => esc_html__( 'Background Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'bg_hover_animation',
			[
				'label' => esc_html__( 'Background Hover Animation', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'hover_normal',
				'options' => [
					'hover_normal'  => esc_html__( 'Select Hover Bg Animation', 'theplus' ),
					'hover_fadein'  => esc_html__( 'FadeIn', 'theplus' ),
					'hover_slide_left' => esc_html__( 'SlideInLeft', 'theplus' ),
					'hover_slide_right' => esc_html__( 'SlideInRight', 'theplus' ),
					'hover_slide_top' => esc_html__( 'SlideInTop', 'theplus' ),
					'hover_slide_bottom' => esc_html__( 'SlideInBotton', 'theplus' ),
				],
			]
		);
		$this->start_controls_tabs( 'tabs_background_style' );
		$this->start_controls_tab(
			'tab_background_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'box_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-pricing-table.pricing-style-1 .pricing-table-inner,{{WRAPPER}} .plus-pricing-table.pricing-style-2 .pricing-table-inner,{{WRAPPER}} .plus-pricing-table.pricing-style-3 .pricing-top-part',
				
			]
		);
		$this->add_control(
			'box_overlay_bg_color',
			[
				'label' => esc_html__( 'Overlay Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'separator' => 'before',
				'default' => '',
				'selectors'  => [
					'{{WRAPPER}} .plus-pricing-table.pricing-style-1 .pricing-overlay-color,{{WRAPPER}} .plus-pricing-table.pricing-style-2 .pricing-overlay-color,{{WRAPPER}} .plus-pricing-table.pricing-style-3 .pricing-overlay-color' => 'background: {{VALUE}};',
				],
				'condition' => [
					'bg_hover_animation' => 'hover_normal',
				],
				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_background_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'box_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-pricing-table.hover_normal.pricing-style-1:hover .pricing-table-inner,
								{{WRAPPER}} .plus-pricing-table.hover_fadein .pricing-overlay-color,
								{{WRAPPER}} .plus-pricing-table.hover_slide_left .pricing-overlay-color,
								{{WRAPPER}} .plus-pricing-table.hover_slide_right .pricing-overlay-color,
								{{WRAPPER}} .plus-pricing-table.hover_slide_top .pricing-overlay-color,
								{{WRAPPER}} .plus-pricing-table.hover_slide_bottom .pricing-overlay-color,
								{{WRAPPER}} .plus-pricing-table.hover_normal.pricing-style-2:hover .pricing-table-inner,
								{{WRAPPER}} .plus-pricing-table.hover_normal.pricing-style-3:hover .pricing-top-part',
			]
		);
		$this->add_control(
			'box_hover_overlay_bg_color',
			[
				'label' => esc_html__( 'Overlay Hover Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'separator' => 'before',
				'default' => '',
				'selectors'  => [
					'{{WRAPPER}} .plus-pricing-table.pricing-style-1:hover .pricing-overlay-color,{{WRAPPER}} .plus-pricing-table.pricing-style-2:hover .pricing-overlay-color,{{WRAPPER}} .plus-pricing-table.pricing-style-3:hover .pricing-overlay-color' => 'background: {{VALUE}};',
				],
				'condition' => [
					'bg_hover_animation' => 'hover_normal',
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
				'selector' => '{{WRAPPER}} .plus-pricing-table.pricing-style-1 .pricing-table-inner,{{WRAPPER}} .plus-pricing-table.pricing-style-2 .pricing-table-inner,{{WRAPPER}} .plus-pricing-table.pricing-style-3 .pricing-top-part',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_shadow_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_hover_shadow',
				'selector' => '{{WRAPPER}} .plus-pricing-table.pricing-style-1:hover .pricing-table-inner,{{WRAPPER}} .plus-pricing-table.pricing-style-2:hover .pricing-table-inner,{{WRAPPER}} .plus-pricing-table.pricing-style-3:hover .pricing-top-part',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*background option*/
		/*Extra option*/
		$this->start_controls_section(
            'section_extra_options_styling',
            [
                'label' => esc_html__('Extra Effects', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
            'transform_scale',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Scale Zoom', 'theplus'),
				'default' => [
					'unit' => '',
					'size' => 1,
				],
				'range' => [
					'' => [
						'min'	=> 0.6,
						'max'	=> 1.8,
						'step' => 0.05,
					],
				],
				'render_type' => 'ui',
				'selectors'  => [
					'{{WRAPPER}} .plus-pricing-table.pricing-style-1 .pricing-table-inner,{{WRAPPER}} .plus-pricing-table.pricing-style-2 .pricing-table-inner,{{WRAPPER}} .plus-pricing-table.pricing-style-3 .pricing-top-part' => 'transform: scale({{SIZE}});',
				],
            ]
        );
		$this->end_controls_section();
		/*Extra option*/
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
		$pricing_style = $settings["pricing_table_style"];
		$title_style = $settings["title_style"];
		
		$pi = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['icon_background_image'],$settings['icon_hover_background_image']) : '';
		/*title */
		$pricing_title = $settings["pricing_title"];
		$title='';
		if(!empty($pricing_title)){
			$title .='<div class="pricing-title-wrap">';
				$title .='<div class="pricing-title">'.esc_attr($pricing_title).'</div>';
			$title .='</div>';
		}
		/*title */
		
		/*subtitle */
		$pricing_subtitle = $settings["pricing_subtitle"];
		$subtitle='';
		if(!empty($pricing_subtitle)){
			$subtitle .='<div class="pricing-subtitle-wrap">';
				$subtitle .='<div class="pricing-subtitle">'.esc_attr($pricing_subtitle).'</div>';
			$subtitle .='</div>';
		}
		/*subtitle */
		
		/* Icon content */
		$icons_content='';
		$image_icon=$settings["image_icon"];
		if($image_icon == 'image'){
			$imgSrc = '';
			if(!empty($settings["select_image"]["url"])){
				$image_id=$settings["select_image"]["id"];
				$imgSrc= tp_get_image_rander( $image_id,'full', [ 'class' => 'pricing-icon-img' ] );
			}
			
			$icons_content ='<div class="pricing-icon '.$pi.'">'.$imgSrc.'</div>';
		}
		$service_icon_style='';
		$icon_style=$settings["icon_style"];
			if($icon_style == 'square'){
				$service_icon_style = 'icon-squre';
			} 
			if($icon_style == 'rounded'){
				$service_icon_style = 'icon-rounded';
			} 	
			if($icon_style == 'hexagon'){
				$service_icon_style = 'icon-hexagon';
			} 	
			if($icon_style == 'pentagon'){
				$service_icon_style = 'icon-pentagon';
			}  	
			if($icon_style == 'square-rotate'){
				$service_icon_style = 'icon-square-rotate';
			}
		if($image_icon == 'icon'){
			$icons = '';
			if($settings["icon_font_style"]=='font_awesome'){
				$icons = $settings["icon_fontawesome"];
			}else if($settings["icon_font_style"]=='font_awesome_5'){				
				ob_start();
				\Elementor\Icons_Manager::render_icon( $settings['icon_fontawesome_5'], [ 'aria-hidden' => 'true' ]);
				$icons = ob_get_contents();
				ob_end_clean();
			}else if($settings["icon_font_style"]=='icon_mind'){
				$icons = $settings["icons_mind"];
			}
			
			if(!empty($icons)){
				if($settings["icon_font_style"]=='font_awesome_5'){
					$icons_content = '<div class="pricing-icon '.$pi.' '.esc_attr($service_icon_style).'"><span>'.$icons.'</span></div>';
				}else{
					$icons_content = '<div class="pricing-icon '.$pi.' '.esc_attr($service_icon_style).'"><i class=" '.esc_attr($icons).' "></i></div>';
				}				
			}
		}
		$border_stroke_color='none';
		if($image_icon == 'svg'){
			if($settings['svg_icon'] == 'img'){
				$svg_url = $settings['svg_image']['url'];
			}else{
				$svg_url = THEPLUS_URL.'assets/images/svg/'.esc_attr($settings["svg_d_icon"]); 
			}
			$uid=uniqid("svg-");
						
			if($settings['border_stroke_color'] !=''){
				$border_stroke_color=$settings['border_stroke_color'];
			}else{
				$border_stroke_color='none';
			}
			$icons_content ='<div class="pricing-icon '.$pi.' pt_plus_animated_svg  '.esc_attr($uid).'" data-id="'.esc_attr($uid).'" data-type="'.esc_attr($settings["svg_type"]).'" data-duration="'.esc_attr($settings["duration"]["size"]).'" data-stroke="'.esc_attr($border_stroke_color).'" data-fill_color="none">';
				$icons_content .='<div class="svg_inner_block" style="max-width:'.$settings["max_width"]["size"].$settings["max_width"]["unit"].';max-height:'.$settings["max_width"]["size"].$settings["max_width"]["unit"].';">';
					$icons_content .='<object id="'.esc_attr($uid).'" type="image/svg+xml" data="'.esc_url($svg_url).'" ></object>';
				$icons_content .='</div>';
			$icons_content .='</div>';
		}
		/* Icon content */
		/*content description*/
		$content_style = $settings['content_style'];
		$pricing_content ='';
		$i=0;
		if($content_style =='wysiwyg_content' && !empty($settings["content_wysiwyg"])){
			$pricing_content .='<div class="pricing-content-wrap content-desc '.esc_attr($settings["content_wysiwyg_style"]).'">';
				if($settings["content_wysiwyg_style"]=='style-1'){
					$pricing_content .='<hr class="border-line" />';
				}
				$pricing_content .='<div class="pricing-content">';
					$pricing_content .= wp_kses_post($settings["content_wysiwyg"]);
				$pricing_content .='</div>';
				$pricing_content .= '<div class="content-overlay-bg-color"></div>';
			$pricing_content .='</div>';
		}else if($content_style =='stylist_list'){
			$pricing_content .='<div class="pricing-content-wrap listing-content '.esc_attr($settings["content_list_style"]).'">';
				$pricing_content .='<ul class="plus-icon-list-items">';
					
					foreach ( $settings['icon_list'] as $index => $item ) :
						$repeater_setting_key = $this->get_repeater_setting_key( 'text', 'icon_list', $index );

						$this->add_render_attribute( $repeater_setting_key, 'class', 'plus-icon-list-text' );

						$this->add_inline_editing_attributes( $repeater_setting_key );
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
							
							$plus_tooltip_position=($settings["tooltip_common_option_plus_tooltip_position"]!='') ? $settings["tooltip_common_option_plus_tooltip_position"] : 'top';
							$this->add_render_attribute( $_tooltip, 'data-tippy-placement', $plus_tooltip_position, true );
							
							$tooltip_interactive =($settings["tooltip_common_option_plus_tooltip_interactive"]=='' || $settings["tooltip_common_option_plus_tooltip_interactive"]=='yes') ? 'true' : 'false';
							$this->add_render_attribute( $_tooltip, 'data-tippy-interactive', $tooltip_interactive, true );
							
							$plus_tooltip_theme=($settings["tooltip_common_option_plus_tooltip_theme"]!='') ? $settings["tooltip_common_option_plus_tooltip_theme"] : 'dark';
							$this->add_render_attribute( $_tooltip, 'data-tippy-theme', $plus_tooltip_theme, true );
							
							
							$tooltip_arrow =($settings["tooltip_common_option_plus_tooltip_arrow"]!='none' || $settings["tooltip_common_option_plus_tooltip_arrow"]=='') ? 'true' : 'false';
							$this->add_render_attribute( $_tooltip, 'data-tippy-arrow', $tooltip_arrow , true );
							
							$plus_tooltip_arrow=($settings["tooltip_common_option_plus_tooltip_arrow"]!='') ? $settings["tooltip_common_option_plus_tooltip_arrow"] : 'sharp';
							$this->add_render_attribute( $_tooltip, 'data-tippy-arrowtype', $plus_tooltip_arrow, true );
							
							$plus_tooltip_animation=($settings["tooltip_common_option_plus_tooltip_animation"]!='') ? $settings["tooltip_common_option_plus_tooltip_animation"] : 'shift-toward';
							$this->add_render_attribute( $_tooltip, 'data-tippy-animation', $plus_tooltip_animation, true );
							
							$plus_tooltip_x_offset=($settings["tooltip_common_option_plus_tooltip_x_offset"]!='') ? $settings["tooltip_common_option_plus_tooltip_x_offset"] : 0;
							$plus_tooltip_y_offset=($settings["tooltip_common_option_plus_tooltip_y_offset"]!='') ? $settings["tooltip_common_option_plus_tooltip_y_offset"] : 0;
							$this->add_render_attribute( $_tooltip, 'data-tippy-offset', $plus_tooltip_x_offset .','. $plus_tooltip_y_offset, true );
							
							$tooltip_duration_in =($settings["tooltip_common_option_plus_tooltip_duration_in"]!='') ? $settings["tooltip_common_option_plus_tooltip_duration_in"] : 250;
							$tooltip_duration_out =($settings["tooltip_common_option_plus_tooltip_duration_out"]!='') ? $settings["tooltip_common_option_plus_tooltip_duration_out"] : 200;
							$tooltip_trigger =($settings["tooltip_common_option_plus_tooltip_triggger"]!='') ? $settings["tooltip_common_option_plus_tooltip_triggger"] : 'mouseenter';
							$tooltip_arrowtype =($settings["tooltip_common_option_plus_tooltip_arrow"]!='') ? $settings["tooltip_common_option_plus_tooltip_arrow"] : 'sharp';
						}
						
						$uniqid=uniqid("tooltip");
						
						$show_tooltips_on = $item['show_tooltips_on'];
						$toolbox=$toolicon=$tt_on_icon='';
						if(!empty($show_tooltips_on) && $show_tooltips_on=='icon'){
							$toolbox = 'class="plus-icon-list-item elementor-repeater-item-'.esc_attr($item['_id']).'"';
							$toolicon = 'id="'.esc_attr($uniqid).'" class="plus-icon-list-item elementor-repeater-item-'.esc_attr($item['_id']).'" data-local="true" '.$this->get_render_attribute_string( $_tooltip ).'';
							
							ob_start();
							\Elementor\Icons_Manager::render_icon( $settings['tt_on_icon'] , [ 'aria-hidden' => 'true' ]);
							$tt_on_icon = ob_get_contents();
							ob_end_clean();
							
						}else{
							$toolbox = 'id="'.esc_attr($uniqid).'" class="plus-icon-list-item elementor-repeater-item-'.esc_attr($item['_id']).'" data-local="true" '.$this->get_render_attribute_string( $_tooltip ).'';							
						}
						
						
						$pricing_content .='<li '.$toolbox.'>';
						$icons='';
						if($item['list_icon_style']=='font_awesome'){
							$icons=$item['list_icon_fontawesome'];									
						}else if($item['list_icon_style']=='font_awesome_5'){							
							ob_start();
							\Elementor\Icons_Manager::render_icon( $item['list_icon_fontawesome_5'], [ 'aria-hidden' => 'true' ]);
							$icons = ob_get_contents();
							ob_end_clean();
						}else if($item['list_icon_style']=='icon_mind'){
							$icons=$item['list_icons_mind'];									
						}
						
						if ( ! empty( $icons ) ) :
							$pricing_content .='<span class="plus-icon-list-icon">';
								if($item['list_icon_style']=='font_awesome_5'){
									$pricing_content .='<span>'.$icons.'</span>';
								}else{
									$pricing_content .='<i class="'.esc_attr( $icons ).'" aria-hidden="true"></i>';
								}								
							$pricing_content .='</span>';
						endif;
						$pricing_content .='<span '.$this->get_render_attribute_string( $repeater_setting_key ).'>'.$item["list_description"].'</span>';
						if(!empty($show_tooltips_on) && $show_tooltips_on=='icon'){
							$pricing_content .='<span class="tp-tooltip-on-icon" '.$toolicon.'>'.$tt_on_icon.'</span>';
						}
						$inline_tippy_js='';
						if($item['show_tooltips'] == 'yes'){
							$inline_tippy_js ='jQuery( document ).ready(function() {
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
							$pricing_content .= wp_print_inline_script_tag($inline_tippy_js);
						}
						$pricing_content .='</li>';
						$i++;
					endforeach;
				$pricing_content .='</ul>';
				
				if(!empty($settings['load_show_list_toggle'])){
					$default_load=$settings['load_show_list_toggle'];
				}else{
					$default_load=3;
				}
				if($settings["content_list_style"]=='style-1' && $i> $default_load){
					$default_load=$default_load-1;
					$pricing_content .='<a href="#" class="read-more-options more" data-default-load="'.esc_attr($default_load).'" data-more-text="'.esc_attr($settings["list_style_show_option"]).'" data-less-text="'.esc_attr($settings["list_style_less_option"]).'">'.esc_html($settings["list_style_show_option"]).'</a>';
				}
				if($settings["content_list_style"]=='style-1'){
					$cb = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['content_box_background_image'],$settings['content_box_hover_background_image']) : '';
					$pricing_content .= '<div class="content-overlay-bg-color '.$cb.'"></div>';
				}
			$pricing_content .='</div>';
		}
		/*content description*/
		/*Previous Price*/
		$previous_price_content='';
		if(!empty($settings['show_previous_price']) && $settings['show_previous_price']=='yes'){
			$previous_price_prefix = $settings["previous_price_prefix"];
			$previous_price = $settings["previous_price"];
			$previous_price_postfix = $settings["previous_price_postfix"];
			$previous_price_content .='<span class="pricing-previous-price-wrap">'.esc_attr($previous_price_prefix).esc_attr($previous_price).esc_attr($previous_price_postfix).'</span>';			
		}
		/*Previous Price*/
		/*Price content*/
		$price_style=$settings["price_style"];
		$price_prefix = $settings["price_prefix"];
		$price = $settings["price"];
		$price_postfix = $settings["price_postfix"];
		
		$price_content ='<div class="pricing-price-wrap '.esc_attr($price_style).'">';
			$price_content .= $previous_price_content;
			if(!empty($price_prefix)){
				$price_content .='<span class="price-prefix-text">'.esc_attr($price_prefix).'</span>';
			}
			if(isset($price)){
				$price_content .='<span class="pricing-price">'.esc_attr($price).'</span>';
			}
			if(!empty($price_postfix)){
				$price_content .='<span class="price-postfix-text">'.esc_attr($price_postfix).'</span>';
			}
		$price_content .='</div>';
		/*Price content*/
		/* button */
		$the_button='';
		if($settings['display_button'] == 'yes'){
		if ( ! empty( $settings['button_link']['url'] ) ) {
			$this->add_render_attribute( 'button', 'href', $settings['button_link']['url'] );
			if ( $settings['button_link']['is_external'] ) {
				$this->add_render_attribute( 'button', 'target', '_blank' );
			}
			if ( $settings['button_link']['nofollow'] ) {
				$this->add_render_attribute( 'button', 'rel', 'nofollow' );
			}
		}
		
		$button_bg = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['button_background_image'],$settings['button_hover_background_image']) : '';
		
		$this->add_render_attribute( 'button', 'class', 'button-link-wrap'.$button_bg );
		$this->add_render_attribute( 'button', 'role', 'button' );
		
		$button_style = $settings['button_style'];
		$button_text = $settings['button_text'];
		$btn_uid=uniqid('btn');
		$data_class= $btn_uid;
		$data_class .=' button-'.esc_attr($button_style).' ';
		
		$the_button ='<div class="pt-plus-button-wrapper">';
			$the_button .='<div class="button_parallax">';
				$the_button .='<div class="ts-button">';
					$the_button .='<div class="pt_plus_button '.esc_attr($data_class).'">';
						$the_button .= '<div class="animted-content-inner">';
							$the_button .='<a '.$this->get_render_attribute_string( "button" ).'>';
							$the_button .= $this->render_text();
							$the_button .='</a>';
						$the_button .='</div>';
					$the_button .='</div>';
				$the_button .='</div>';
			$the_button .='</div>';
		$the_button .='</div>';
		}
		if(!empty($settings["call_to_action_text"])){
			$the_button .='<div class="pricing-cta-text">'.wp_kses_post($settings["call_to_action_text"]).'</div>';
		}
		/* button */
		$title_style_content='';
		if($settings["title_style"]=='style-1'){
				$title_style_content .='<div class="pricing-title-content style-1">';
					$title_style_content .=$icons_content;
					$title_style_content .=$title;
					$title_style_content .=$subtitle;
				$title_style_content .='</div>';
		}
		
		/*Ribbon Pin*/
		$ribbon_content='';
		$rpinbg = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['ribbon_background_image']) : '';
		if(!empty($settings["display_ribbon_pin"]) && $settings["display_ribbon_pin"]=='yes'){
			$ribbon_style=$settings["ribbon_pin_style"];
			$ribbon_content .='<div class="pricing-ribbon-pin '.$rpinbg.' '.esc_attr($ribbon_style).'">';
				$ribbon_content .='<div class="ribbon-pin-inner '.$rpinbg.'">';
				$ribbon_content .= wp_kses_post($settings["ribbon_pin_text"]);
				$ribbon_content .='</div>';
			$ribbon_content .='</div>';
		}
		/*Ribbon Pin*/
		/*--Plus Extra ---*/
			$PlusExtra_Class = "plus-widget-wrapper";
			include THEPLUS_PATH. 'modules/widgets/theplus-widgets-extra.php';
		/*--Plus Extra ---*/
		
		$pricing_output='';
		$ptocbg = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['box_hover_background_image']) : '';
		if($pricing_style=='style-1' || $pricing_style=='style-2'){
			$pricing_output .= $ribbon_content;
			$pricing_output .= $title_style_content;
			$pricing_output .= $price_content;
			$pricing_output .= $the_button;
			$pricing_output .= $pricing_content;
			$pricing_output .= '<div class="pricing-overlay-color '.$ptocbg.'"></div>';			
		}else if($pricing_style=='style-3'){
			$ptibg3 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['box_background_image'],$settings['box_hover_background_image']) : '';
			$pricing_output .='<div class="pricing-top-part '.$ptibg3.'">';
				$pricing_output .= $ribbon_content;
				$pricing_output .= $title_style_content;
				$pricing_output .= $price_content;
				$pricing_output .= $the_button;
				$pricing_output .= '<div class="pricing-overlay-color '.$ptocbg.'"></div>';			
			$pricing_output .='</div>';
			$pricing_output .= $pricing_content;
		}
		
		/*--On Scroll View Animation ---*/
			include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';
			$ptibg = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['box_background_image'],$settings['box_hover_background_image']) : '';
			$output = '<div id="plus-pricing-table" class="plus-pricing-table pricing-'.esc_attr($pricing_style).' '.$settings["bg_hover_animation"].' '.esc_attr($animated_class).'" '.$animation_attr.'>';
				$output .= '<div class="pricing-table-inner '.$ptibg.'">';
					$output .= $pricing_output;
				$output .='</div>';
				
			$output .='</div>';
		echo $before_content.$output.$after_content;
	}
	
    protected function content_template() {}
	protected function render_text() {	
		$icons_after=$icons_before='';
		$settings = $this->get_settings_for_display();
		
		$button_style = $settings['button_style'];
		$before_after = $settings['before_after'];
		$button_text = $settings['button_text'];
		
		$icons='';
		if($settings["button_icon_style"]=='font_awesome'){
			$icons=$settings["button_icon"];
		}else if($settings["button_icon_style"]=='font_awesome_5'){			
			ob_start();
			\Elementor\Icons_Manager::render_icon( $settings['button_icon_5'], [ 'aria-hidden' => 'true' ]);
			$icons = ob_get_contents();
			ob_end_clean();
		}else if($settings["button_icon_style"]=='icon_mind'){
			$icons=$settings["button_icons_mind"];
		}
		//lottie
		if($settings['display_button'] == 'yes'){
			if(!empty($button_style) && $button_style == 'style-8'){
				$button_icon_type = $settings["button_icon_type"];
				if(!empty($button_icon_type) && $button_icon_type == 'lottie'){
					$ext = pathinfo($settings['lottieUrl']['url'], PATHINFO_EXTENSION);
					if($ext!='json'){
						$lottie_icon = '<h3 class="theplus-posts-not-found">'.esc_html__("Opps!! Please Enter Only JSON File Extension.",'theplus').'</h3>';
					}else{
						$lottiedisplay = isset($settings['lottiedisplay']) ? $settings['lottiedisplay'] : 'inline-block';
						if(!empty($before_after) && $before_after == 'before'){
							$lottieMright = isset($settings['lottieMright']['size']) ? $settings['lottieMright']['size'] : 10;
							$lottieMleft = isset($settings['lottieMleft']['size']) ? $settings['lottieMleft']['size'] : 0;
						}else if(!empty($before_after) && $before_after == 'after'){
							$lottieMright = isset($settings['lottieMright']['size']) ? $settings['lottieMright']['size'] : 0;
							$lottieMleft = isset($settings['lottieMleft']['size']) ? $settings['lottieMleft']['size'] : 10;
						}
						$lottieWidth = isset($settings['lottieWidth']['size']) ? $settings['lottieWidth']['size'] : 25;
						$lottieHeight = isset($settings['lottieHeight']['size']) ? $settings['lottieHeight']['size'] : 25;
						$lottieVertical = isset($settings['lottieVertical']) ? $settings['lottieVertical'] : 'middle';
						$lottieSpeed = isset($settings['lottieSpeed']['size']) ? $settings['lottieSpeed']['size'] : 1;
						$lottieLoop = isset($settings['lottieLoop']) ? $settings['lottieLoop'] : 'no';
						$lottiehover = isset($settings['lottiehover']) ? $settings['lottiehover'] : 'no';
						$lottieLoopValue='';
						if(!empty($settings['lottieLoop']) && $settings['lottieLoop']=='yes'){
							$lottieLoopValue ='loop'; 
						}
						$lottieAnim='autoplay';
						if(!empty($settings['lottiehover']) && $settings['lottiehover']=='yes'){
							$lottieAnim ='hover'; 
						}
						$lottie_icon ='<lottie-player src="'.esc_url($settings['lottieUrl']['url']).'" style="display: '.esc_attr($lottiedisplay).'; width: '.esc_attr($lottieWidth).'px; height: '.esc_attr($lottieHeight).'px; margin-right: '.esc_attr($lottieMright).'px; margin-left: '.esc_attr($lottieMleft).'px; vertical-align: '.esc_attr($lottieVertical).';" '.esc_attr($lottieLoopValue).'  speed="'.esc_attr($lottieSpeed).'" '.esc_attr($lottieAnim).'></lottie-player>';
					}
				}
			}
		}
		if(!empty($button_icon_type) && $button_icon_type == 'lottie'){
			if($before_after=='before'){
				$icons_before = '<span class="btn-icon button-before">'.$lottie_icon.'</span>';
			}
			if($before_after=='after'){
			   $icons_after = '<span class="btn-icon button-after">'.$lottie_icon.'</span>';
			}
		}
		if($settings["button_icon_style"]=='font_awesome_5' && !empty($settings['button_icon_5']) && !empty($icons)){
			if($before_after=='before'){
				$icons_before = '<span class="btn-icon button-before">'.$icons.'</span>';
			}
			if($before_after=='after'){
			   $icons_after = '<span class="btn-icon button-after">'.$icons.'</span>';
			}
		}else{
			if($before_after=='before' && !empty($icons)){
				$icons_before = '<i class="btn-icon button-before '.esc_attr($icons).'"></i>';
			}
			if($before_after=='after' && !empty($icons)){
			   $icons_after = '<i class="btn-icon button-after '.esc_attr($icons).'"></i>';
			}
		}		
		
		if($button_style=='style-8'){
			$button_text =$icons_before . $button_text . $icons_after;
		}
		
		if($button_style=='style-7'){
			$button_text =$button_text.'<span class="btn-arrow"></span>';
		}
		if($button_style=='style-9'){
			$button_text =$button_text.'<span class="btn-arrow"><i class="fa-show fa fa-chevron-right" aria-hidden="true"></i><i class="fa-hide fa fa-chevron-right" aria-hidden="true"></i></span>';
		}
		return $button_text;
	}
}