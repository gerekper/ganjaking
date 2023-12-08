<?php 
/*
Widget Name: Info Box 
Description: Display Infobox.
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
use Elementor\Group_Control_Image_Size;
 
if (!defined('ABSPATH')) exit; // Exit if accessed directly
 
class ThePlus_Info_Box extends Widget_Base {
 
	public function get_name() {
		return 'tp-info-box';
	}
 
    public function get_title() {
        return esc_html__('Info Box', 'theplus');
    }
 
    public function get_icon() {
        return 'fa fa-info-circle theplus_backend_icon';
    }
 
    public function get_categories() {
        return array('plus-essential');
    }
 
    protected function register_controls() {
 
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'info_box_layout',
			[
				'label' => esc_html__( 'Select Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'single_layout',
				'options' => [
					'single_layout'  => esc_html__( 'Listing', 'theplus' ),
					'carousel_layout' => esc_html__( 'Carousel', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'main_style',
			[
				'label' => esc_html__( 'Info Box Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style_1',
				'options' => [
					'style_1'  => esc_html__( 'Style-1', 'theplus' ),
					'style_2' => esc_html__( 'Style-2', 'theplus' ),
					'style_3' => esc_html__( 'Style-3', 'theplus' ),
					'style_4' => esc_html__( 'Style-4', 'theplus' ),
					'style_7' => esc_html__( 'Style-5', 'theplus' ),
					'style_11' => esc_html__( 'Style-6', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'loop_select_icon',
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
					'lottie' => esc_html__( 'Lottie', 'theplus' ),
				],
				'condition' => [
					'info_box_layout' => 'carousel_layout',
				],
			]
		);
 
		$this->add_control(
			'loop_display_button',
			[
				'label' => esc_html__( 'Button', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'info_box_layout' => 'carousel_layout',
				],
			]
		);
		$this->add_control(
            'loop_button_style', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Button Style', 'theplus'),
                'default' => 'style-7',
                'options' => [
                    'style-7' => esc_html__('Style 1', 'theplus'),
                    'style-8' => esc_html__('Style 2', 'theplus'),
                    'style-9' => esc_html__('Style 3', 'theplus'),                    
                ],
				'condition' => [
					'info_box_layout' => 'carousel_layout',
					'loop_display_button' => 'yes',
				],
            ]
        );
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
		'loop_title',
			[
				'label' => esc_html__( 'Title', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'The Plus', 'theplus' ),
				'dynamic' => [
					'active'   => true,
				],
			]
		);
		$repeater->add_control(
			'loop_content_desc',
			[
				'label' => esc_html__( 'Description', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'theplus' ),
				'dynamic' => [
					'active'   => true,
				],
			]
		);
		$repeater->add_control(
			'loop_url_link',
			[
				'label' => esc_html__( 'Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'theplus' ),
				'show_external' => true,
				'default' => [
					'url' => '',
				],
				'dynamic' => [
					'active'   => true,
				],
			]
		);
		$repeater->add_control(
			'loop_image_icon',
			[
				'label' => esc_html__( 'Select Icon', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'description' => esc_html__('You can select Icon, Custom Image or SVG using this option.','theplus'),
				'default' => 'icon',
				'options' => [
					''  => esc_html__( 'None', 'theplus' ),
					'icon' => esc_html__( 'Icon', 'theplus' ),
					'image' => esc_html__( 'Image', 'theplus' ),
					'svg' => esc_html__( 'Svg', 'theplus' ),
					'lottie' => esc_html__( 'Lottie', 'theplus' ),
				],
			]
		);
		$repeater->add_control(
			'loop_svg_icon',
			[
				'label' => esc_html__( 'Svg Select Option', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'img',
				'options' => [
					'img'  => esc_html__( 'Custom Upload', 'theplus' ),
					'svg' => esc_html__( 'Pre Built SVG Icon', 'theplus' ),
				],
				'condition' => [
					'loop_image_icon' => 'svg',
				],
			]
		);
		$repeater->add_control(
			'loop_svg_image',
			[
				'label' => esc_html__( 'Only Svg', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'description' => esc_html__('Select Only .svg File from media library.','theplus'),
				'default' => [
					'url' => '',
				],
				'media_type' => 'image',
				'condition' => [
					'loop_image_icon' => 'svg',
					'loop_svg_icon' => 'img',
				],
			]
		);
		$repeater->add_control(
			'loop_svg_d_icon',
			[
				'label' => esc_html__( 'Select Svg Icon', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'app.svg',
				'options' => theplus_svg_icons_list(),
				'condition' => [
					'loop_image_icon' => 'svg',
					'loop_svg_icon' => 'svg',
				],
			]
		);
		$repeater->add_control(
            'loop_max_width',
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
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box {{CURRENT_ITEM}}.info-box-inner .info_box_svg svg' => 'max-width: {{SIZE}}{{UNIT}};max-height: {{SIZE}}{{UNIT}};width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'loop_image_icon' => 'svg',
					'loop_svg_icon' => ['svg','img'],
				],
            ]
        );
		$repeater->add_control(
			'loop_select_image',
			[
				'label' => esc_html__( 'Use Image As icon', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'media_type' => 'image',
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'loop_image_icon' => 'image',
				],
			]
		);		
		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'loop_select_image_thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'after',
				'condition' => [
					'loop_image_icon' => 'image',
				],
			]
		);
		$repeater->add_control(
			'lottieUrl',
			[
				'label' => esc_html__( 'Lottie URL', 'theplus' ),
				'type' => Controls_Manager::URL,				
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'condition' => [
					'loop_image_icon' => 'lottie',
				],
			]
		);
		$repeater->add_control(
			'loop_icon_style',
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
					'loop_image_icon' => 'icon',
				],
			]
		);
		$repeater->add_control(
			'loop_icon_fontawesome',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICON,
				'default' => 'fa fa-bank',
				'condition' => [
					'loop_image_icon' => 'icon',
					'loop_icon_style' => 'font_awesome',
				],	
			]
		);
		$repeater->add_control(
			'loop_icon_fontawesome_5',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-plus',
					'library' => 'solid',
				],
				'condition' => [
					'loop_image_icon' => 'icon',
					'loop_icon_style' => 'font_awesome_5',
				],
			]
		);
		$repeater->add_control(
			'loop_icons_mind',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'default' => '',
				'label_block' => true,
				'options' => theplus_icons_mind(),
				'condition' => [
					'loop_image_icon' => 'icon',
					'loop_icon_style' => 'icon_mind',
				],
			]
		);
		$repeater->add_control(
			'loop_button_text',
			[
				'label' => esc_html__( 'Button Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => esc_html__( 'Read More', 'theplus' ),
				'placeholder' => esc_html__( 'Read More', 'theplus' ),
			]
		);
		$repeater->add_control(
			'loop_button_link',
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
			]
		);
		$repeater->add_control(
			'loopbgimageheading',
			[
				'label' => esc_html__( 'Background Image Normal & Hover', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'loopbgimage',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt_plus_info_box {{CURRENT_ITEM}}.info-box-inner .info-box-bg-box',
			]
		);
		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'loopbgimagehover',
				'types'     => [ 'classic', 'gradient' ],
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .pt_plus_info_box.hover_normal {{CURRENT_ITEM}}.info-box-inner:hover .info-box-bg-box,
								{{WRAPPER}} .pt_plus_info_box.hover_normal {{CURRENT_ITEM}}.info-box-inner.tp-info-active .info-box-bg-box,
								{{WRAPPER}} .pt_plus_info_box.hover_fadein .infobox-overlay-color,
								{{WRAPPER}} .pt_plus_info_box.hover_slide_left .infobox-overlay-color,
								{{WRAPPER}} .pt_plus_info_box.hover_slide_right .infobox-overlay-color,
								{{WRAPPER}} .pt_plus_info_box.hover_slide_top .infobox-overlay-color,
								{{WRAPPER}} .pt_plus_info_box.hover_slide_bottom .infobox-overlay-color',
			]
		);
 
		$repeater->add_control(
			'r_full_infobox_switch',
			[
				'label' => esc_html__( 'Full Infobox Link', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',				
			]
		);
		$repeater->add_control(
			'r_full_infobox_link',
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
				'condition' => [					
					'r_full_infobox_switch' => 'yes',
				],
			]
		);
		$this->add_control(
            'loop_content',
            [
				'label' => esc_html__( 'Carousel InfoBox', 'theplus' ),
                'type' => Controls_Manager::REPEATER,
                'default' => [
                    [
                        'loop_title' => 'The Plus',                       
                    ],
					[
                        'loop_title' => 'The Plus 2',
                    ],
					[
                        'loop_title' => 'The Plus 3',
                    ],
					[
                        'loop_title' => 'The Plus 4',
                    ],
                ],                
				'fields' => $repeater->get_controls(),
                'title_field' => '{{{ loop_title }}}',
				'condition' => [
					'info_box_layout' => 'carousel_layout',
				],
            ]
        );
		$this->add_control(
			'loop_title_tag',
			[
				'label' => esc_html__( 'Title Tag', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'h6',
				'options' => theplus_get_tags_options(),
				'separator' => 'before',
				'condition' => [
					'info_box_layout' => 'carousel_layout',
				],
			]
		);
		$this->add_control(
			'connection_switch',
			[
				'label' => esc_html__( 'Carousel Anything Connection', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'info_box_layout' => 'carousel_layout',
				],
			]
		);
		$this->add_control(
			'connection_unique_id',
			[
				'label' => esc_html__( 'Connection Carousel ID', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'condition' => [
					'info_box_layout' => 'carousel_layout',
					'connection_switch' => 'yes',
				],				
			]
		);
		$this->add_control(
			'default_active',
			[
				'label' => esc_html__( 'Default Active', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '50',
				'options' => [					
					'0'  => esc_html__( '1', 'theplus' ),
					'1'  => esc_html__( '2', 'theplus' ),
					'2'  => esc_html__( '3', 'theplus' ),
					'3'  => esc_html__( '4', 'theplus' ),
					'4'  => esc_html__( '5', 'theplus' ),
					'5'  => esc_html__( '6', 'theplus' ),
					'6'  => esc_html__( '7', 'theplus' ),
					'7'  => esc_html__( '8', 'theplus' ),
					'8'  => esc_html__( '9', 'theplus' ),
					'9'  => esc_html__( '10', 'theplus' ),
					'10'  => esc_html__( '11', 'theplus' ),
					'11'  => esc_html__( '12', 'theplus' ),
					'12'  => esc_html__( '13', 'theplus' ),
					'13'  => esc_html__( '14', 'theplus' ),
					'14'  => esc_html__( '15', 'theplus' ),
					'15'  => esc_html__( '16', 'theplus' ),
					'16'  => esc_html__( '17', 'theplus' ),
					'17'  => esc_html__( '18', 'theplus' ),
					'18'  => esc_html__( '19', 'theplus' ),
					'19'  => esc_html__( '20', 'theplus' ),
					'50'  => esc_html__( 'None', 'theplus' ),
				],
				'condition' => [
					'info_box_layout' => 'carousel_layout',
					'connection_switch' => 'yes',
				],
			]
		);
		$this->add_control(
			'connection_hover_click',
			[
				'label' => esc_html__( 'Effect on', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'con_pro_hover',
				'options' => [
					'con_pro_hover'  => esc_html__( 'Hover', 'theplus' ),
					'con_pro_click' => esc_html__( 'Click', 'theplus' ),
				],
				'condition' => [
					'info_box_layout' => 'carousel_layout',
					'connection_switch' => 'yes',
				],
			]
		);
		$this->add_control(
			'title',
			[
				'label' => esc_html__( 'Title Of Info Box', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'The Plus', 'theplus' ),
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'info_box_layout' => 'single_layout',
				],
			]
		);
		$this->add_control(
			'content_desc',
			[
				'label' => esc_html__( 'Description', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'theplus' ),
				'placeholder' => esc_html__( 'Type your description here', 'theplus' ),
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'info_box_layout' => 'single_layout',
				],
			]
		);
		$this->add_control(
			'text_align',
			[
				'label' => esc_html__( 'Info Box Alignment', 'theplus' ),
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
				'toggle' => true,
				'separator' => 'before',
				'condition' => [
					'main_style' => 'style_3',
				],
			]
		);
		$this->add_control(
			'url_link',
			[
				'label' => esc_html__( 'Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'theplus' ),
				'show_external' => true,
				'default' => [
					'url' => '',
				],
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'info_box_layout' => 'single_layout',
				],
			]
		);
		$this->add_control(
			'image_icon',
			[
				'label' => esc_html__( 'Select Icon', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'description' => esc_html__('You can select Icon, Custom Image or SVG using this option.','theplus'),
				'default' => 'icon',
				'options' => [
					''  => esc_html__( 'None', 'theplus' ),
					'icon' => esc_html__( 'Icon', 'theplus' ),
					'image' => esc_html__( 'Image', 'theplus' ),
					'svg' => esc_html__( 'Svg', 'theplus' ),
					'lottie' => esc_html__( 'Lottie', 'theplus' ),
				],
				'separator' => 'before',
				'condition' => [
					'info_box_layout' => 'single_layout',
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
					'info_box_layout' => 'single_layout',
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
					'info_box_layout' => 'single_layout',
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
					'info_box_layout' => 'single_layout',
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
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'info_box_layout' => 'single_layout',
					'image_icon' => 'image',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'select_image_thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'after',
				'condition' => [
					'info_box_layout' => 'single_layout',
					'image_icon' => 'image',
				],
			]
		);
		$this->add_control(
			'lottieUrl',
			[
				'label' => esc_html__( 'Lottie URL', 'theplus' ),
				'type' => Controls_Manager::URL,				
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'condition' => [
					'info_box_layout' => 'single_layout',
					'image_icon' => 'lottie',
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
					'icon_image' => esc_html__( 'Icon Image', 'theplus' ),
				],
				'condition' => [
					'info_box_layout' => 'single_layout',
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
					'info_box_layout' => 'single_layout',
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
					'value' => 'fas fa-plus',
					'library' => 'solid',
				],
				'condition' => [
					'info_box_layout' => 'single_layout',
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
					'info_box_layout' => 'single_layout',
					'image_icon' => 'icon',
					'icon_font_style' => 'icon_mind',
				],
			]
		);
		$this->add_control(
			'icons_image',
			[
				'label' => esc_html__( 'Use Image As icon', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'media_type' => 'image',
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'info_box_layout' => 'single_layout',
					'image_icon' => 'icon',
					'icon_font_style' => 'icon_image',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'icons_image_thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'after',
				'condition' => [
					'info_box_layout' => 'single_layout',
					'image_icon' => 'icon',
					'icon_font_style' => 'icon_image',
				],
			]
		);
		$this->add_control(
			'display_button',
			[
				'label' => esc_html__( 'Button', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'info_box_layout' => 'single_layout',
				],
			]
		);
		$this->add_control(
            'button_style', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Button Style', 'theplus'),
                'default' => 'style-7',
                'options' => [
                    'style-7' => esc_html__('Style 1', 'theplus'),
                    'style-8' => esc_html__('Style 2', 'theplus'),
                    'style-9' => esc_html__('Style 3', 'theplus'),                    
                ],
				'condition' => [
					'info_box_layout' => 'single_layout',
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
				'default' => esc_html__( 'Read More', 'theplus' ),
				'placeholder' => esc_html__( 'Read More', 'theplus' ),
				'condition' => [
					'info_box_layout' => 'single_layout',
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
					'info_box_layout' => 'single_layout',
					'display_button' => 'yes',
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
					'info_box_layout' => 'single_layout',
					'display_button' => 'yes',
					'button_style!' => ['style-7','style-9'],
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
					'info_box_layout' => 'single_layout',
					'display_button' => 'yes',
					'button_style!' => ['style-7','style-9'],
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
					'value' => 'fas fa-plus',
					'library' => 'solid',
				],
				'condition' => [
					'info_box_layout' => 'single_layout',
					'display_button' => 'yes',
					'button_style!' => ['style-7','style-9'],
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
					'info_box_layout' => 'single_layout',
					'display_button' => 'yes',
					'button_style!' => ['style-7','style-9'],
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
					'info_box_layout' => 'single_layout',
					'display_button' => 'yes',
					'button_style!' => ['style-7','style-9'],
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
					'info_box_layout' => 'single_layout',
					'display_button' => 'yes',
					'button_style!' => ['style-7','style-9'],
					'button_icon_style!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .button-link-wrap .button-after' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .button-link-wrap .button-before' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'hover_info_button',
			[
				'label'   => esc_html__( 'Hover Button InfoBox', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition' => [
					'info_box_layout' => 'single_layout',
					'display_button' => 'yes',
					'button_style' => ['style-8'],					
				],
			]
		);
		$this->add_control(
			'display_pin_text',
			[
				'label' => esc_html__( 'Display Pin Text', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'info_box_layout' => 'single_layout',
					'main_style' => 'style_3',
				],
			]
		);
		$this->add_control(
			'pin_text_title',
			[
				'label' => esc_html__( 'Pin Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'New', 'theplus' ),
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'info_box_layout' => 'single_layout',
					'main_style' => 'style_3',
					'display_pin_text' => 'yes',
				],
			]
		);
		$this->add_control(
			'title_tag',
			[
				'label' => esc_html__( 'Title Tag', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'div',
				'options' => theplus_get_tags_options(),
				'separator' => 'before',
				'condition' => [
					'info_box_layout' => 'single_layout',
				],
			]
		);
		$this->add_control(
			'full_infobox_switch',
			[
				'label' => esc_html__( 'Full Infobox Link', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'description' => esc_html__('Note : If you enable this option, There will be only one link for whole infobox. Rest links will be removed.','theplus'),
				'condition' => [
					'info_box_layout' => 'single_layout',					
				],
			]
		);
		$this->add_control(
			'full_infobox_link',
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
				'condition' => [
					'info_box_layout' => 'single_layout',
					'full_infobox_switch' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*title style*/
		$this->start_controls_section(
            'section_title_styling',
            [
                'label' => esc_html__('Title Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-title',
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
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-title' => 'color: {{VALUE}}',
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
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-title' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{title_gradient_color1.VALUE}} {{title_gradient_color1_control.SIZE}}{{title_gradient_color1_control.UNIT}}, {{title_gradient_color2.VALUE}} {{title_gradient_color2_control.SIZE}}{{title_gradient_color2_control.UNIT}})',
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
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-title' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{title_gradient_color1.VALUE}} {{title_gradient_color1_control.SIZE}}{{title_gradient_color1_control.UNIT}}, {{title_gradient_color2.VALUE}} {{title_gradient_color2_control.SIZE}}{{title_gradient_color2_control.UNIT}})',
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
				'default' => '#3351a6',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .service-title,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .service-title' => 'color: {{VALUE}}',
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
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .service-title,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .service-title' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{title_hover_gradient_color1.VALUE}} {{title_hover_gradient_color1_control.SIZE}}{{title_hover_gradient_color1_control.UNIT}}, {{title_hover_gradient_color2.VALUE}} {{title_hover_gradient_color2_control.SIZE}}{{title_hover_gradient_color2_control.UNIT}})',
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
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .service-title,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .service-title' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{title_hover_gradient_color1.VALUE}} {{title_hover_gradient_color1_control.SIZE}}{{title_hover_gradient_color1_control.UNIT}}, {{title_hover_gradient_color2.VALUE}} {{title_hover_gradient_color2_control.SIZE}}{{title_hover_gradient_color2_control.UNIT}})',
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
		$this->add_control(
            'title_top_space',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Title Top Space', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'step' => 2,
						'min' => -150,
						'max' => 150,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'render_type' => 'ui',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box.info-box-style_1 .info-box-inner .service-title,{{WRAPPER}} .pt_plus_info_box.info-box-style_2 .info-box-inner .service-title,{{WRAPPER}} .pt_plus_info_box.info-box-style_3 .info-box-inner .service-title,{{WRAPPER}} .pt_plus_info_box.info-box-style_4 .info-box-inner .service-media,{{WRAPPER}} .pt_plus_info_box.info-box-style_7 .info-box-inner .service-title' => 'margin-top : {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_control(
            'title_btm_space',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Title Bottom Space', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'step' => 2,
						'min' => -150,
						'max' => 150,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box.info-box-style_1 .info-box-inner .service-title,{{WRAPPER}} .pt_plus_info_box.info-box-style_2 .info-box-inner .service-title,{{WRAPPER}} .pt_plus_info_box.info-box-style_3 .info-box-inner .service-title,{{WRAPPER}} .pt_plus_info_box.info-box-style_4 .info-box-inner .service-media,{{WRAPPER}} .pt_plus_info_box.info-box-style_7 .info-box-inner .service-title' => 'margin-bottom : {{SIZE}}{{UNIT}}',
				],
            ]
        );
 
		$this->end_controls_section();
		/*title style*/
		/*title bottom border */
		$this->start_controls_section(
            'section_title_border_styling',
            [
                'label' => esc_html__('Bottom Border Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
			]
        );
		$this->add_control(
			'border_check',
			[
				'label' => esc_html__( 'Display Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'yes',
				'description' => esc_html__('By checking up this option you can turn on underline/border under the title.','theplus'),
			]
		);
		$this->add_control(
            'border_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Border Width', 'theplus'),
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 5,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 20,
				],
				'render_type' => 'ui',
				'condition' => [
					'border_check' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-border' => 'width: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_control(
            'border_height',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Border Height', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
				'render_type' => 'ui',
				'condition' => [
					'border_check' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-border' => 'border-width: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_control(
			'title_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-border' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'border_check' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*title bottom border */
		/*desc style*/
		$this->start_controls_section(
            'section_desc_styling',
            [
                'label' => esc_html__('Description Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
			]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'desc_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-desc,{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-desc p',
			]
		);
		$this->add_control(
			'desc_color',
			[
				'label' => esc_html__( 'Desc Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-desc,{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-desc p' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'desc_hover_color',
			[
				'label' => esc_html__( 'Desc Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .service-desc,{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .service-desc p,
					{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .service-desc,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .service-desc p' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_section();
		/*desc style*/
		/*background option*/
		$this->start_controls_section(
            'section_bg_option_styling',
            [
                'label' => esc_html__('Background Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
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
				'condition' => [
					'main_style' => ['style_1','style_2','style_3','style_4','style_7','style_11'],
				],
			]
		);
		$this->add_control(
			'box_border_style',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'condition' => [
					'box_border' => 'yes',
					'main_style' => ['style_1','style_2','style_3','style_4','style_7','style_11'],
				],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .info-box-bg-box' => 'border-style: {{VALUE}};',
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
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .info-box-bg-box' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'main_style' => ['style_1','style_2','style_3','style_4','style_7','style_11'],
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
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .info-box-bg-box' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'main_style' => ['style_1','style_2','style_3','style_4','style_7','style_11'],
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
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .info-box-bg-box,{{WRAPPER}} .pt_plus_info_box .info-box-inner .infobox-overlay-color' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'main_style' => ['style_1','style_2','style_3','style_4','style_7','style_11'],
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
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .info-box-bg-box,
					{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .info-box-bg-box' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'main_style' => ['style_1','style_2','style_3','style_4','style_7','style_11'],
					'box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'boxborder_hover_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_info_box:hover .info-box-inner .info-box-bg-box' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'main_style' => ['style_1','style_2','style_3','style_4','style_7','style_11'],
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
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .info-box-bg-box,{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .infobox-overlay-color,
					{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .info-box-bg-box,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .infobox-overlay-color' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'main_style' => ['style_1','style_2','style_3','style_4','style_7','style_11'],
					'box_border' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'border_check_right',
			[
				'label' => esc_html__( 'Side image Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'yes',
				'condition' => [
					'main_style' => ['style_1','style_2'],
				],
			]
		);
		$this->add_control(
			'border_right_color',
			[
				'label' => esc_html__( 'Border Right Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'condition' => [
					'main_style' => ['style_1','style_2'],
					'border_check_right' => 'yes',
				],
			]
		);
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
				'selector'  => '{{WRAPPER}} .pt_plus_info_box .info-box-inner .info-box-bg-box',
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
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .infobox-overlay-color' => 'background: {{VALUE}};',
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
				'selector'  => '{{WRAPPER}} .pt_plus_info_box.hover_normal .info-box-inner:hover .info-box-bg-box,
								{{WRAPPER}} .pt_plus_info_box.hover_normal .info-box-inner.tp-info-active .info-box-bg-box,
								{{WRAPPER}} .pt_plus_info_box.hover_fadein .infobox-overlay-color,
								{{WRAPPER}} .pt_plus_info_box.hover_slide_left .infobox-overlay-color,
								{{WRAPPER}} .pt_plus_info_box.hover_slide_right .infobox-overlay-color,
								{{WRAPPER}} .pt_plus_info_box.hover_slide_top .infobox-overlay-color,
								{{WRAPPER}} .pt_plus_info_box.hover_slide_bottom .infobox-overlay-color',
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
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .infobox-overlay-color,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .infobox-overlay-color' => 'background: {{VALUE}};',
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
				'selector' => '{{WRAPPER}} .pt_plus_info_box .info-box-inner .info-box-bg-box',
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
				'selector' => '{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .info-box-bg-box,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .info-box-bg-box',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'box_bg_bf',
			[
				'label' => esc_html__( 'Backdrop Filter', 'theplus' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'Default', 'theplus' ),
				'label_on' => __( 'Custom', 'theplus' ),
				'return_value' => 'yes',
			]
		);
		$this->add_control(
			'box_bg_bf_blur',
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
					'box_bg_bf' => 'yes',
				],
			]
		);
		$this->add_control(
			'box_bg_bf_grayscale',
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
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .info-box-bg-box' => '-webkit-backdrop-filter:grayscale({{box_bg_bf_grayscale.SIZE}})  blur({{box_bg_bf_blur.SIZE}}{{box_bg_bf_blur.UNIT}}) !important;backdrop-filter:grayscale({{box_bg_bf_grayscale.SIZE}})  blur({{box_bg_bf_blur.SIZE}}{{box_bg_bf_blur.UNIT}}) !important;',
				 ],
				'condition'    => [
					'box_bg_bf' => 'yes',
				],
			]
		);
		$this->end_popover();
		$this->end_controls_section();
		/*background option*/
		/*button style*/
		$this->start_controls_section(
            'section_button_styling',
            [
                'label' => esc_html__('Button Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [								
								[
									'name'     => 'display_button','operator' => '==','value'    => 'yes',
								],
								[
									'name'     => 'loop_display_button','operator' => '==','value'    => 'yes',
								],									
							],
						],
					],
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
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .pt-plus-button-wrapper' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
				'separator' => 'after',
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [								
								[
									'name'     => 'display_button','operator' => '==','value'    => 'yes',
								],
								[
									'name'     => 'loop_display_button','operator' => '==','value'    => 'yes',
								],									
							],
						],
					],
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
							'top' => '15',
							'right' => '30',
							'bottom' => '15',
							'left' => '30',
							'isLinked' => false 
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button .button-link-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .pt_plus_button .button-link-wrap',
			]
		);
		$this->add_responsive_control(
			'button_icon_size',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button .button-link-wrap i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pt_plus_button .button-link-wrap svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .pt_plus_button .button-link-wrap svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .pt_plus_button.button-style-7 .button-link-wrap:after' => 'border-color: {{VALUE}};',
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
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [								
								[
									'name'     => 'button_style','operator' => '==','value'    => 'style-8',
								],
								[
									'name'     => 'loop_button_style','operator' => '==','value'    => 'style-8',
								],									
							],
						],
					],
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
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [								
								[
									'name'     => 'button_style','operator' => '==','value'    => 'style-8',
								],
								[
									'name'     => 'loop_button_style','operator' => '==','value'    => 'style-8',
								],									
							],
						],
					],
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
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [								
								[
									'name'     => 'button_style','operator' => '==','value'    => 'style-8',
								],
								[
									'name'     => 'loop_button_style','operator' => '==','value'    => 'style-8',
								],							
							],
						],
					],
				],
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
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [								
								[
									'name'     => 'button_style','operator' => '==','value'    => 'style-8',
								],
								[
									'name'     => 'loop_button_style','operator' => '==','value'    => 'style-8',
								],								
							],
						],
					],
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
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [								
								[
									'name'     => 'button_style','operator' => '==','value'    => 'style-8',
								],
								[
									'name'     => 'loop_button_style','operator' => '==','value'    => 'style-8',
								],																
							],
						],
					],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_shadow',
				'selector' => '
							   {{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap',
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [								
								[
									'name'     => 'button_style','operator' => '==','value'    => 'style-8',
								],
								[
									'name'     => 'loop_button_style','operator' => '==','value'    => 'style-8',
								],							
							],
						],
					],
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
					'{{WRAPPER}} .pt_plus_button .button-link-wrap:hover,{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .pt_plus_button .hover_box_button,
					{{WRAPPER}} .info-box-inner.tp-info-active .pt_plus_button .button-link-wrap,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .pt_plus_button .hover_box_button,{{WRAPPER}} .info-box-inner:hover .pt_plus_button .button-link-wrap' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pt_plus_button .button-link-wrap:hover svg,{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .pt_plus_button .hover_box_button svg,
					{{WRAPPER}} .info-box-inner.tp-info-active .pt_plus_button .button-link-wrap svg,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .pt_plus_button .hover_box_button svg,{{WRAPPER}} .info-box-inner:hover .pt_plus_button .button-link-wrap svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .pt_plus_button .hover_box_button,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .pt_plus_button .hover_box_button,{{WRAPPER}} .info-box-inner:hover .pt_plus_button .button-link-wrap,{{WRAPPER}} .info-box-inner:hover .pt_plus_button .button-link-wrap',
				'separator' => 'after',
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [								
								[
									'name'     => 'button_style','operator' => '==','value'    => 'style-8',
								],
								[
									'name'     => 'loop_button_style','operator' => '==','value'    => 'style-8',
								],									
							],
						],
					],
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
					'{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .pt_plus_button .hover_box_button,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .pt_plus_button .hover_box_button,{{WRAPPER}} .info-box-inner:hover .pt_plus_button .button-link-wrap,{{WRAPPER}} .info-box-inner:hover .pt_plus_button .button-link-wrap' => 'border-color: {{VALUE}};',
				],
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [								
								[
									'name'     => 'button_style','operator' => '==','value'    => 'style-8',
								],
								[
									'name'     => 'loop_button_style','operator' => '==','value'    => 'style-8',
								],								
							],
						],
					],
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
					'{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .pt_plus_button .hover_box_button,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .pt_plus_button .hover_box_button,{{WRAPPER}} .info-box-inner:hover .pt_plus_button .button-link-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [								
								[
									'name'     => 'button_style','operator' => '==','value'    => 'style-8',
								],
								[
									'name'     => 'loop_button_style','operator' => '==','value'    => 'style-8',
								],							
							],
						],
					],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_hover_shadow',
				'selector' => '{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .pt_plus_button .hover_box_button,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .pt_plus_button .hover_box_button,{{WRAPPER}} .info-box-inner:hover .pt_plus_button .button-link-wrap',
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [								
								[
									'name'     => 'button_style','operator' => '==','value'    => 'style-8',
								],
								[
									'name'     => 'loop_button_style','operator' => '==','value'    => 'style-8',
								],							
							],
						],
					],
				],
			]
		);
		$this->end_controls_tab();
 
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*button style*/
		/*svg style*/
		$this->start_controls_section(
            'section_svg_styling',
            [
                'label' => esc_html__('Svg Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'     => 'loop_select_icon',
							'operator' => '==',
							'value'    => 'svg',
						],
						[
							'name'     => 'image_icon',
							'operator' => '==',
							'value'    => 'svg',
						],
					],
				],
            ]
        );
		$this->add_control(
			'svg_icon_style',
			[
				'label' => esc_html__( 'SVG Icon Styles', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''  => esc_html__( 'None', 'theplus' ),
					'square' => esc_html__( 'Square', 'theplus' ),
					'rounded' => esc_html__( 'Rounded', 'theplus' ),
					'hexagon' => esc_html__( 'Hexagon', 'theplus' ),
					'pentagon' => esc_html__( 'Pentagon', 'theplus' ),
					'square-rotate' => esc_html__( 'Square Rotate', 'theplus' ),
				],
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'     => 'loop_select_icon',
							'operator' => '==',
							'value'    => 'svg',
						],
						[
							'name'     => 'image_icon',
							'operator' => '==',
							'value'    => 'svg',
						],
					],
				],
			]
		);
		$this->add_responsive_control(
			'svg_icon_width',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Background Width', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .pt_plus_animated_svg .svg_inner_block' => 'width: {{SIZE}}{{UNIT}} !important;height: {{SIZE}}{{UNIT}} !important;line-height: {{SIZE}}{{UNIT}} !important;text-align: center;',					
				],
			]
		);
		$this->add_control(
			'svg_stroke_none',
			[
				'label' => esc_html__( 'SVG Stroke None', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),
				'default' => 'no',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .info_box_svg svg' => 'stroke: none;',
				],
			]
		);
		$this->add_control(
			'svg_fill_color',
			[
				'label' => esc_html__( 'SVG Fill Color', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),				
				'default' => 'no',
				'separator' => 'after',
			]
		);		
		$this->start_controls_tabs( 'svg_icon_n_h' );
		$this->start_controls_tab(
			'svg_icn_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'border_stroke_color',
			[
				'label' => esc_html__( 'Border/Stoke Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ff0000',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .info_box_svg svg' => 'stroke: {{VALUE}}',
				],
				'condition' => [
					'svg_stroke_none!' => 'yes',
				],
			]
		);
		$this->add_control(
			'svg_fill_color_f',
			[
				'label' => esc_html__( 'Fill Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .info_box_svg svg,{{WRAPPER}} .pt_plus_info_box .info-box-inner .info_box_svg svg *:not(g)' => 'fill: {{VALUE}}',
				],
				'condition' => [
					'svg_fill_color' => 'yes',
				],
 
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'svg_icon_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt_plus_info_box .pt_plus_animated_svg .svg_inner_block',				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'svg_icon_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_info_box .pt_plus_animated_svg .svg_inner_block',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'svg_icon_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_info_box .pt_plus_animated_svg .svg_inner_block' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'svg_icon_n_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_info_box .pt_plus_animated_svg .svg_inner_block',
				'condition' => [
						'svg_icon_style' => ['','square','rounded'],
				],
			]
		);
		$this->add_control(
			'svg_icon_rn_shadow',
			[
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Ex. 1px 1px 4px rgba(0,0,0,0.75)', 'theplus' ),
				'description' => esc_html__( 'Ex. 1px 1px 4px rgba(0,0,0,0.75)', 'theplus' ),
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .pt_plus_animated_svg' => '-webkit-filter: drop-shadow({{VALUE}});-moz-filter: drop-shadow({{VALUE}});-ms-filter: drop-shadow({{VALUE}});-o-filter: drop-shadow({{VALUE}});filter: drop-shadow({{VALUE}});',
				],
				'condition' => [
						'svg_icon_style!' => ['','square','rounded'],
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'svg_icn_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'border_stroke_color_hover',
			[
				'label' => esc_html__( 'Border/Stoke Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .info_box_svg svg,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .info_box_svg svg' => 'stroke: {{VALUE}}',
				],
				'condition' => [
					'svg_stroke_none!' => 'yes',
				],
			]
		);
		$this->add_control(
			'svg_fill_color_hover',
			[
				'label' => esc_html__( 'Fill Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .info_box_svg svg,{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .info_box_svg svg *:not(g),{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .info_box_svg svg,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .info_box_svg svg *:not(g)' => 'fill: {{VALUE}}',
				],
				'condition' => [
					'svg_fill_color' => 'yes',
				],
 
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'svg_icon_background_h',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .pt_plus_animated_svg .svg_inner_block,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .pt_plus_animated_svg .svg_inner_block',				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'svg_icon_border_h',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .pt_plus_animated_svg .svg_inner_block,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .pt_plus_animated_svg .svg_inner_block',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'svg_icon_border_radius_h',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .pt_plus_animated_svg .svg_inner_block,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .pt_plus_animated_svg .svg_inner_block' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'svg_icon_h_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .pt_plus_animated_svg .svg_inner_block,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .pt_plus_animated_svg .svg_inner_block',
				'condition' => [
						'svg_icon_style' => ['','square','rounded'],
				],
			]
		);
		$this->add_control(
			'svg_icon_rh_shadow',
			[
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Ex. 1px 1px 4px rgba(0,0,0,0.75)', 'theplus' ),
				'description' => esc_html__( 'Ex. 1px 1px 4px rgba(0,0,0,0.75)', 'theplus' ),
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .pt_plus_animated_svg,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .pt_plus_animated_svg' => '-webkit-filter: drop-shadow({{VALUE}});-moz-filter: drop-shadow({{VALUE}});-ms-filter: drop-shadow({{VALUE}});-o-filter: drop-shadow({{VALUE}});filter: drop-shadow({{VALUE}});',
				],
				'condition' => [
						'svg_icon_style!' => ['','square','rounded'],
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();	
		$this->add_control(
			'svg_type',
			[
				'label' => esc_html__( 'Select Style Image', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'delayed',
				'options' => theplus_svg_type(),
				'separator' => 'before',
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'     => 'loop_select_icon',
							'operator' => '==',
							'value'    => 'svg',
						],
						[
							'name'     => 'image_icon',
							'operator' => '==',
							'value'    => 'svg',
						],
					],
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
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'     => 'loop_select_icon',
							'operator' => '==',
							'value'    => 'svg',
						],
						[
							'name'     => 'image_icon',
							'operator' => '==',
							'value'    => 'svg',
						],
					],
				],
            ]
        );
		$this->add_responsive_control(
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
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .info_box_svg svg' => 'max-width: {{SIZE}}{{UNIT}};max-height: {{SIZE}}{{UNIT}};width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',
				],				
				'condition' => [
					'image_icon' => 'svg',
					'svg_icon' => ['svg','img'],
				],
            ]
        );
 
		$this->add_control(
			'draw_animated_svg',
			[
				'label' => esc_html__( 'Disable Draw SVG', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'     => 'loop_select_icon',
							'operator' => '==',
							'value'    => 'svg',
						],
						[
							'name'     => 'image_icon',
							'operator' => '==',
							'value'    => 'svg',
						],
					],
				],
			]
		);
		$this->end_controls_section();
		/*svg style*/
 
		/*lottie style*/
		$this->start_controls_section(
            'section_lottie_styling',
            [
                'label' => esc_html__('Lottie Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'     => 'loop_select_icon',
							'operator' => '==',
							'value'    => 'lottie',
						],
						[
							'name'     => 'image_icon',
							'operator' => '==',
							'value'    => 'lottie',
						],
					],
				],
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
					'size' => 50,
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
					'size' => 50,
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
 
		/*icon style*/
		$this->start_controls_section(
            'section_icon_styling',
            [
                'label' => esc_html__('Icon Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'     => 'loop_select_icon',
							'operator' => '==',
							'value'    => 'icon',
						],
						[
							'name'     => 'image_icon',
							'operator' => '==',
							'value'    => 'icon',
						],
					],
				],
            ]
        );
		$this->add_control(
			'icon_style',
			[
				'label' => esc_html__( 'Icon Styles', 'theplus' ),
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
		$this->add_responsive_control(
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
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner i.service-icon,
					{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-icon i' => 'font-size: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-icon svg' => 'width:{{SIZE}}{{UNIT}} !important;height:{{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-icon .icon-image-set' => 'max-width: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_responsive_control(
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
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-icon,
					{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-icon i' => 'width: {{SIZE}}{{UNIT}} !important;height: {{SIZE}}{{UNIT}} !important;line-height: {{SIZE}}{{UNIT}} !important;text-align: center;',
					//'{{WRAPPER}} .pt_plus_info_box .info-box-bg-box .icon_shine_show' => 'background-position: -{{SIZE}}{{UNIT}} -{{SIZE}}{{UNIT}}, 0 0',
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
				'label_block' => false, 
				'default' => 'solid',
			]
		);
		$this->add_control(
			'icon_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-icon:before,
					{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-icon i:before' => 'color: {{VALUE}};background: transparent;-webkit-background-clip: unset;-webkit-text-fill-color: initial;',
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-icon svg' => 'fill: {{VALUE}};background: transparent;-webkit-background-clip: unset;-webkit-text-fill-color: initial;',
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
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-icon:before,
					{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-icon i:before' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{icon_gradient_color1.VALUE}} {{icon_gradient_color1_control.SIZE}}{{icon_gradient_color1_control.UNIT}}, {{icon_gradient_color2.VALUE}} {{icon_gradient_color2_control.SIZE}}{{icon_gradient_color2_control.UNIT}});-webkit-transition: all 0.3s linear;-moz-transition: all 0.3s linear;-o-transition: all 0.3s linear;-ms-transition: all 0.3s linear;transition: all 0.3s linear;',
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
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-icon:before,
					{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-icon i:before' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{icon_gradient_color1.VALUE}} {{icon_gradient_color1_control.SIZE}}{{icon_gradient_color1_control.UNIT}}, {{icon_gradient_color2.VALUE}} {{icon_gradient_color2_control.SIZE}}{{icon_gradient_color2_control.UNIT}});-webkit-transition: all 0.3s linear;-moz-transition: all 0.3s linear;-o-transition: all 0.3s linear;-ms-transition: all 0.3s linear;transition: all 0.3s linear;',
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
				'selector'  => '{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-icon,
				{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-icon i',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'icon_border_style_n',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => theplus_get_border_style_with_none(),
				'condition' => [
					'icon_style' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-icon,
					{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-icon i' => 'border-style: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'icon_border_width_n',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-icon,
					{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-icon i' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'icon_style' => '',
				],
			]
		);
		$this->add_control(
			'icon_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-icon,
					{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-icon i' => 'border-color: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'icon_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-icon,
					{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-icon i' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_box_shadow',
				'selector' => '{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-icon,
				{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-icon i',
			]
		);
		$this->add_control(
			'icon_rn_shadow',
			[
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Ex. 1px 1px 4px rgba(0,0,0,0.75)', 'theplus' ),
				'description' => esc_html__( 'Ex. 1px 1px 4px rgba(0,0,0,0.75)', 'theplus' ),
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-icon-wrap' => '-webkit-filter: drop-shadow({{VALUE}});-moz-filter: drop-shadow({{VALUE}});-ms-filter: drop-shadow({{VALUE}});-o-filter: drop-shadow({{VALUE}});filter: drop-shadow({{VALUE}});',
				],
				'condition' => [
						'icon_style!' => ['','square','rounded'],
				],
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
				'label_block' => false,
				'default' => 'solid',
			]
		);
 
		$this->add_control(
			'icon_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .service-icon:before,{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .service-icon i:before,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .service-icon:before,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .service-icon i:before' => 'color: {{VALUE}};background: transparent;-webkit-background-clip: unset;-webkit-text-fill-color: initial;',
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .service-icon svg,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .service-icon svg' => 'fill: {{VALUE}};background: transparent;-webkit-background-clip: unset;-webkit-text-fill-color: initial;',
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
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .service-icon:before,{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .service-icon i:before,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .service-icon:before,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .service-icon i:before' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{icon_hover_gradient_color1.VALUE}} {{icon_hover_gradient_color1_control.SIZE}}{{icon_hover_gradient_color1_control.UNIT}}, {{icon_hover_gradient_color2.VALUE}} {{icon_hover_gradient_color2_control.SIZE}}{{icon_hover_gradient_color2_control.UNIT}})',
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
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .service-icon:before,{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .service-icon i:before,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .service-icon:before,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .service-icon i:before' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{icon_hover_gradient_color1.VALUE}} {{icon_hover_gradient_color1_control.SIZE}}{{icon_hover_gradient_color1_control.UNIT}}, {{icon_hover_gradient_color2.VALUE}} {{icon_hover_gradient_color2_control.SIZE}}{{icon_hover_gradient_color2_control.UNIT}})',
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
				'selector'  => '{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .service-icon,{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .service-icon i,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .service-icon,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .service-icon i',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'icon_border_style_h',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => theplus_get_border_style_with_none(),
				'condition' => [
					'icon_style' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .service-icon,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .service-icon' => 'border-style: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'icon_border_width_h',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .service-icon,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .service-icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'icon_style' => '',
				],
			]
		);
		$this->add_control(
			'icon_border_hover_color',
			[
				'label' => esc_html__( 'Hover Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .service-icon,{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .service-icon i,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .service-icon,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .service-icon i' => 'border-color: {{VALUE}}',
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
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .service-icon,{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .service-icon i,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .service-icon,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .service-icon i' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_hover_box_shadow',
				'selector' => '{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .service-icon,{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .service-icon i,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .service-icon,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .service-icon i',
			]
		);
		$this->add_control(
			'icon_rh_shadow',
			[
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Ex. 1px 1px 4px rgba(0,0,0,0.75)', 'theplus' ),
				'description' => esc_html__( 'Ex. 1px 1px 4px rgba(0,0,0,0.75)', 'theplus' ),
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .service-icon-wrap,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .service-icon-wrap' => '-webkit-filter: drop-shadow({{VALUE}});-moz-filter: drop-shadow({{VALUE}});-ms-filter: drop-shadow({{VALUE}});-o-filter: drop-shadow({{VALUE}});filter: drop-shadow({{VALUE}});',
				],
				'condition' => [
						'icon_style!' => ['','square','rounded'],
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'icon_overlay',
			[
				'label' => esc_html__( 'Icon Overlay', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'info_box_layout' => 'single_layout',
					'main_style' => ['style_1','style_2','style_3'],
				],
			]
		);
		$this->add_control(
			'icon_overlay_adjust',
			[
				'label' => esc_html__( 'Icon Adjust', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -400,
						'max' => 400,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box.info-box-style_1 .icon-overlay .m-r-16,{{WRAPPER}} .pt_plus_info_box.info-box-style_2 .icon-overlay .m-l-16' => 'top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'info_box_layout' => 'single_layout',					
					'main_style' => ['style_1','style_2'],
					'icon_overlay' => 'yes',
				],
			]
		);
		$this->add_control(
			'icon_shine_effect',
			[
				'label' => esc_html__( 'Icon Shine Effect', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->end_controls_section();		
		/*icon style*/
		/*Image Style*/
		$this->start_controls_section(
            'section_image_styling',
            [
                'label' => esc_html__('Image Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'     => 'loop_select_icon',
							'operator' => '==',
							'value'    => 'image',
						],
						[
							'name'     => 'image_icon',
							'operator' => '==',
							'value'    => 'image',
						],
					],
				],
            ]
        );
		$this->add_responsive_control(
            'img_max_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Max Width', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .service-img,{{WRAPPER}} .pt_plus_info_box.list-carousel-slick .ts-icon-img.icon-img-b img' => 'max-width: {{SIZE}}{{UNIT}} !important;',
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_image_style' );
		$this->start_controls_tab(
			'tab_image_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_responsive_control(
			'image_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .service-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'image_box_shadow',
				'selector' => '{{WRAPPER}} .pt_plus_info_box .info-box-inner  .service-img',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_image_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_responsive_control(
			'image_hover_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .service-img,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .service-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'image_hover_box_shadow',
				'selector' => '{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover  .service-img,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active  .service-img',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Image Style*/
		/*Pin Text Style*/
		$this->start_controls_section(
            'section_pin_text_styling',
            [
                'label' => esc_html__('Pin Text Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'info_box_layout' => 'single_layout',
					'main_style' => 'style_3',
					'display_pin_text' => 'yes',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'pin_text_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_info_box .info-box-inner .info-pin-text',
			]
		);
		$this->add_responsive_control('pin_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .info-pin-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'pin_text_border',
			[
				'label' => esc_html__( 'Pin Text Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
			]
		);
		$this->add_control(
			'pin_text_border_style',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'condition' => [
					'pin_text_border' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .info-pin-text' => 'border-style: {{VALUE}}',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_pin_text_style' );
		$this->start_controls_tab(
			'tab_pin_text_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'pin_text_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .info-pin-text' => 'color: {{VALUE}}',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'pin_text_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt_plus_info_box .info-box-inner .info-pin-text',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'pin_text_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .info-pin-text' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pin_text_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'pin_text_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .info-pin-text' => 'border-color: {{VALUE}}',
				],
				'separator' => 'before',
				'condition' => [
					'pin_text_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'pin_text_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .info-pin-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'pin_text_box_shadow',
				'selector' => '{{WRAPPER}} .pt_plus_info_box .info-box-inner .info-pin-text',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_pin_text_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'pin_text_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .info-pin-text,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .info-pin-text' => 'color: {{VALUE}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'pin_text_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .info-pin-text,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .info-pin-text',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'pin_text_hover_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .info-pin-text,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .info-pin-text' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pin_text_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'pin_text_border_hover_color',
			[
				'label' => esc_html__( 'Hover Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .info-pin-text,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .info-pin-text' => 'border-color: {{VALUE}}',
				],
				'separator' => 'before',
				'condition' => [
					'pin_text_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'pin_text_hover_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .info-pin-text,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .info-pin-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'pin_text_hover_box_shadow',
				'selector' => '{{WRAPPER}} .pt_plus_info_box .info-box-inner:hover .info-pin-text,{{WRAPPER}} .pt_plus_info_box .info-box-inner.tp-info-active .info-pin-text',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'square_pin',
			[
				'label' => esc_html__( 'Square Pin', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'pin_width',
			[
				'label' => esc_html__( 'Pin Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 42,
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .info-pin-text.square-pin' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'square_pin' => 'yes',
				],
			]
		);
		$this->add_responsive_control('pin_height',
			[
				'label' => esc_html__( 'Pin Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 42,
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .info-pin-text.square-pin' => 'height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}} !important;',
				],
				'condition' => [
					'square_pin' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'pin_horiz_adjust',
			[
				'label' => esc_html__( 'Pin Horizontal Adjust', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -150,
						'max' => 150,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 45,
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .info-pin-text.square-pin' => 'left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'square_pin' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'pin_vertical_adjust',
			[
				'label' => esc_html__( 'Pin Vertical Adjust', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -150,
						'max' => 150,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .info-pin-text.square-pin' => 'top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'square_pin' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*Pin Text Style*/
		/*carousel option*/
		$this->start_controls_section(
            'section_carousel_options_styling',
            [
                'label' => esc_html__('Carousel Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'info_box_layout' => 'carousel_layout',
				],
            ]
        );
		$this->add_control(
			'carousel_unique_id',
			[
				'label' => esc_html__( 'Unique Carousel ID', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'separator' => 'after',
				'description' => esc_html__('Keep this blank or Setup Unique id for carousel which you can use with "Carousel Remote" widget.','theplus'),
			]
		);
		$this->add_control(
			'slider_direction',
			[
				'label'   => esc_html__( 'Slider Mode', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'horizontal'  => esc_html__( 'Horizontal', 'theplus' ),
					'vertical' => esc_html__( 'Vertical', 'theplus' ),
				],
			]
		);		
		$this->add_control(
            'slide_speed',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Slide Speed', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 10000,
						'step' => 100,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1500,
				],
            ]
        );
 
		$this->start_controls_tabs( 'tabs_carousel_style' );
		$this->start_controls_tab(
			'tab_carousel_desktop',
			[
				'label' => esc_html__( 'Desktop', 'theplus' ),
			]
		);
		$this->add_control(
			'slider_desktop_column',
			[
				'label'   => esc_html__( 'Desktop Columns', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '4',
				'options' => theplus_carousel_desktop_columns(),
			]
		);
		$this->add_control(
			'steps_slide',
			[
				'label'   => esc_html__( 'Next Previous', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'description' => esc_html__( 'Select option of column scroll on previous or next in carousel.','theplus' ),
				'options' => [
					'1'  => esc_html__( 'One Column', 'theplus' ),
					'2' => esc_html__( 'All Visible Columns', 'theplus' ),
				],
			]
		);
		$this->add_responsive_control(
            'slider_padding',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Slide Padding', 'theplus'),
				'size_units' => [ 'px' ],
 
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick:not(.multi-row) .slick-initialized .slick-slide' => 'margin: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .list-carousel-slick.multi-row .slick-initialized .slick-slide' => 'margin: 0 {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .list-carousel-slick.multi-row .slick-initialized .slick-slide > div' => 'margin: {{SIZE}}{{UNIT}} 0',
				],
            ]
        );		
		$this->add_control(
			'slider_draggable',
			[
				'label'   => esc_html__( 'Draggable', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
			]
		);
		$this->add_control(
			'multi_drag',
			[
				'label'   => esc_html__( 'Multi Drag', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),				
				'default' => 'no',
				'condition' => [
					'slider_draggable' => 'yes',
				],
			]
		);
		$this->add_control(
			'slider_infinite',
			[
				'label'   => esc_html__( 'Infinite Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
			]
		);
		$this->add_control(
			'slider_pause_hover',
			[
				'label'   => esc_html__( 'Pause On Hover', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'slider_adaptive_height',
			[
				'label'   => esc_html__( 'Adaptive Height', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'slide_fade_inout',
			[
				'label' => esc_html__( 'Slide Animation', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'  => esc_html__( 'Default', 'theplus' ),
					'fadeinout' => esc_html__( 'Fade in/Fade out', 'theplus' ),
				],
				'condition' => [
					'slider_direction' => 'horizontal',
				],
			]
		);
		$this->add_control(
			'slide_fade_inout_notice',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => 'Note : Just for single column layout.',
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'slider_direction' => 'horizontal',
					'slide_fade_inout' => 'fadeinout',
				],
			]
		);
		$this->add_control(
			'slider_animation',
			[
				'label'   => esc_html__( 'Animation Type', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'ease',
				'options' => [
					'ease' => esc_html__( 'With Hold', 'theplus' ),
					'linear' => esc_html__( 'Continuous', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'slider_autoplay',
			[
				'label'   => esc_html__( 'Autoplay', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
			]
		);
		$this->add_control(
            'autoplay_speed',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Autoplay Speed', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 500,
						'max' => 10000,
						'step' => 200,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 3000,
				],
				'condition' => [
					'slider_autoplay' => 'yes',
				],
            ]
        );
 
		$this->add_control(
			'slider_dots',
			[
				'label'   => esc_html__( 'Show Dots', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'slider_dots_style',
			[
				'label'   => esc_html__( 'Dots Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1' => esc_html__( 'Style 1', 'theplus' ),
					'style-2' => esc_html__( 'Style 2', 'theplus' ),
					'style-3' => esc_html__( 'Style 3', 'theplus' ),
					'style-4' => esc_html__( 'Style 4', 'theplus' ),
					'style-5' => esc_html__( 'Style 5', 'theplus' ),
					'style-6' => esc_html__( 'Style 6', 'theplus' ),
					'style-7' => esc_html__( 'Style 7', 'theplus' ),
				],
				'condition'    => [
					'slider_dots' => ['yes'],
				],
			]
		);
		$this->add_control(
            'dots_size_123',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-dots.style-1 li,{{WRAPPER}}  .slick-dots.style-2 li,{{WRAPPER}}  .slick-dots.style-3 li' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],				
				'condition'    => [
					'slider_dots' => 'yes',
					'slider_dots_style' => ['style-1','style-2','style-3'],
				],
            ]
        );
		$this->add_control(
            'dots_size_57',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-dots.style-5 button,{{WRAPPER}} .slick-dots.style-7 button' => 'width: {{SIZE}}{{UNIT}};',
				],				
				'condition'    => [
					'slider_dots' => 'yes',
					'slider_dots_style' => ['style-5','style-7'],
				],
            ]
        );
		$this->add_control(
            'dots_size_57_height',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Height', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-dots.style-5 button,{{WRAPPER}} .slick-dots.style-7 button' => 'height: {{SIZE}}{{UNIT}};',
				],				
				'condition'    => [
					'slider_dots' => 'yes',
					'slider_dots_style' => ['style-5','style-7'],
				],
            ]
        );
		$this->add_control(
            'dots_size_57_active',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Active Width', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-dots.style-5 .slick-active button,{{WRAPPER}} .slick-dots.style-5 li:hover button' => 'width: {{SIZE}}{{UNIT}} !important;',
				],				
				'condition'    => [
					'slider_dots' => 'yes',
					'slider_dots_style' => ['style-5'],
				],
            ]
        );
		$this->add_control(
			'dots_border_color',
			[
				'label' => esc_html__( 'Dots Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-1 li button,{{WRAPPER}} .list-carousel-slick .slick-dots.style-6 li button' => '-webkit-box-shadow:inset 0 0 0 8px {{VALUE}};-moz-box-shadow: inset 0 0 0 8px {{VALUE}};box-shadow: inset 0 0 0 8px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-1 li.slick-active button' => '-webkit-box-shadow:inset 0 0 0 1px {{VALUE}};-moz-box-shadow: inset 0 0 0 1px {{VALUE}};box-shadow: inset 0 0 0 1px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-2 li button' => 'border-color:{{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick ul.slick-dots.style-3 li button' => '-webkit-box-shadow: inset 0 0 0 1px {{VALUE}};-moz-box-shadow: inset 0 0 0 1px {{VALUE}};box-shadow: inset 0 0 0 1px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-3 li.slick-active button' => '-webkit-box-shadow: inset 0 0 0 8px {{VALUE}};-moz-box-shadow: inset 0 0 0 8px {{VALUE}};box-shadow: inset 0 0 0 8px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick ul.slick-dots.style-4 li button' => '-webkit-box-shadow: inset 0 0 0 0px {{VALUE}};-moz-box-shadow: inset 0 0 0 0px {{VALUE}};box-shadow: inset 0 0 0 0px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-1 li button:before' => 'color: {{VALUE}};',
				],
				'condition' => [
					'slider_dots_style' => ['style-1','style-2','style-3','style-5'],
					'slider_dots' => 'yes',
				],
			]
		);
		$this->add_control(
			'dots_bg_color',
			[
				'label' => esc_html__( 'Dots Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-2 li button,{{WRAPPER}} .list-carousel-slick ul.slick-dots.style-3 li button,{{WRAPPER}} .list-carousel-slick .slick-dots.style-4 li button:before,{{WRAPPER}} .list-carousel-slick .slick-dots.style-5 button,{{WRAPPER}} .list-carousel-slick .slick-dots.style-7 button' => 'background: {{VALUE}};',
				],
				'condition' => [
					'slider_dots_style' => ['style-2','style-3','style-4','style-5','style-7'],
					'slider_dots' => 'yes',
				],
			]
		);
		$this->add_control(
			'dots_active_border_color',
			[
				'label' => esc_html__( 'Dots Active Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-2 li::after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-4 li.slick-active button' => '-webkit-box-shadow: inset 0 0 0 1px {{VALUE}};-moz-box-shadow: inset 0 0 0 1px {{VALUE}};box-shadow: inset 0 0 0 1px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-6 .slick-active button:after' => 'color: {{VALUE}};',
				],
				'condition' => [
					'slider_dots_style' => ['style-2','style-4','style-6'],
					'slider_dots' => 'yes',
				],
			]
		);
		$this->add_control(
			'dots_active_bg_color',
			[
				'label' => esc_html__( 'Dots Active Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-2 li::after,{{WRAPPER}} .list-carousel-slick .slick-dots.style-4 li.slick-active button:before,{{WRAPPER}} .list-carousel-slick .slick-dots.style-5 .slick-active button,{{WRAPPER}} .list-carousel-slick .slick-dots.style-7 .slick-active button' => 'background: {{VALUE}};',					
				],
				'condition' => [
					'slider_dots_style' => ['style-2','style-4','style-5','style-7'],
					'slider_dots' => 'yes',
				],
			]
		);
		$this->add_control(
            'dots_top_padding',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Dots Top Padding', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-slider.slick-dotted' => 'padding-bottom: {{SIZE}}{{UNIT}};',					
				],				
				'condition'    => [
					'slider_dots' => 'yes',
				],
            ]
        );
		$this->add_control(
			'hover_show_dots',
			[
				'label'   => esc_html__( 'On Hover Dots', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'slider_dots' => 'yes',
				],
			]
		);
		$this->add_control(
			'slider_arrows',
			[
				'label'   => esc_html__( 'Show Arrows', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'slider_arrows_style',
			[
				'label'   => esc_html__( 'Arrows Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1' => esc_html__( 'Style 1', 'theplus' ),
					'style-2' => esc_html__( 'Style 2', 'theplus' ),
					'style-3' => esc_html__( 'Style 3', 'theplus' ),
					'style-4' => esc_html__( 'Style 4', 'theplus' ),
					'style-5' => esc_html__( 'Style 5', 'theplus' ),
					'style-6' => esc_html__( 'Style 6', 'theplus' ),
				],
				'condition'    => [
					'slider_arrows' => ['yes'],
				],
			]
		);
		$this->add_control(
			'arrows_position',
			[
				'label'   => esc_html__( 'Arrows Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top-right',
				'options' => [
					'top-right' => esc_html__( 'Top-Right', 'theplus' ),
					'bottm-left' => esc_html__( 'Bottom-Left', 'theplus' ),
					'bottom-center' => esc_html__( 'Bottom-Center', 'theplus' ),
					'bottom-right' => esc_html__( 'Bottom-Right', 'theplus' ),
				],				
				'condition'    => [
					'slider_arrows' => ['yes'],
					'slider_arrows_style' => ['style-3','style-4'],
				],
			]
		);
		$this->add_control(
			'arrow_bg_color',
			[
				'label' => esc_html__( 'Arrow Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#c44d48',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-nav.slick-prev.style-1,{{WRAPPER}} .list-carousel-slick .slick-nav.slick-next.style-1,{{WRAPPER}} .list-carousel-slick .slick-nav.style-3:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-3:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-6:before,{{WRAPPER}} .list-carousel-slick .slick-next.style-6:before' => 'background: {{VALUE}};',					
					'{{WRAPPER}} .list-carousel-slick .slick-prev.style-4:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-4:before' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'slider_arrows_style' => ['style-1','style-3','style-4','style-6'],
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control(
			'arrow_icon_color',
			[
				'label' => esc_html__( 'Arrow Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-nav.slick-prev.style-1:before,{{WRAPPER}} .list-carousel-slick .slick-nav.slick-next.style-1:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-3:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-3:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-4:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-4:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-6 .icon-wrap' => 'color: {{VALUE}};',					
					'{{WRAPPER}} .list-carousel-slick .slick-prev.style-2 .icon-wrap:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-2 .icon-wrap:after,{{WRAPPER}} .list-carousel-slick .slick-next.style-2 .icon-wrap:before,{{WRAPPER}} .list-carousel-slick .slick-next.style-2 .icon-wrap:after,{{WRAPPER}} .list-carousel-slick .slick-prev.style-5 .icon-wrap:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-5 .icon-wrap:after,{{WRAPPER}} .list-carousel-slick .slick-next.style-5 .icon-wrap:before,{{WRAPPER}} .list-carousel-slick .slick-next.style-5 .icon-wrap:after' => 'background: {{VALUE}};',
				],
				'condition' => [
					'slider_arrows_style' => ['style-1','style-2','style-3','style-4','style-5','style-6'],
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control(
			'arrow_hover_bg_color',
			[
				'label' => esc_html__( 'Arrow Hover Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-nav.slick-prev.style-1:hover,{{WRAPPER}} .list-carousel-slick .slick-nav.slick-next.style-1:hover,{{WRAPPER}} .list-carousel-slick .slick-prev.style-2:hover::before,{{WRAPPER}} .list-carousel-slick .slick-next.style-2:hover::before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-3:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-3:hover:before' => 'background: {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-prev.style-4:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-4:hover:before' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'slider_arrows_style' => ['style-1','style-2','style-3','style-4'],
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control(
			'arrow_hover_icon_color',
			[
				'label' => esc_html__( 'Arrow Hover Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#c44d48',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-nav.slick-prev.style-1:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.slick-next.style-1:hover:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-3:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-3:hover:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-4:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-4:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-6:hover .icon-wrap' => 'color: {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-prev.style-2:hover .icon-wrap::before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-2:hover .icon-wrap::after,{{WRAPPER}} .list-carousel-slick .slick-next.style-2:hover .icon-wrap::before,{{WRAPPER}} .list-carousel-slick .slick-next.style-2:hover .icon-wrap::after,{{WRAPPER}} .list-carousel-slick .slick-prev.style-5:hover .icon-wrap::before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-5:hover .icon-wrap::after,{{WRAPPER}} .list-carousel-slick .slick-next.style-5:hover .icon-wrap::before,{{WRAPPER}} .list-carousel-slick .slick-next.style-5:hover .icon-wrap::after' => 'background: {{VALUE}};',
				],
				'condition' => [
					'slider_arrows_style' => ['style-1','style-2','style-3','style-4','style-5','style-6'],
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control(
			'outer_section_arrow',
			[
				'label'   => esc_html__( 'Outer Content Arrow', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'slider_arrows' => 'yes',
					'slider_arrows_style' => ['style-1','style-2','style-5','style-6'],
				],
			]
		);
		$this->add_control(
			'hover_show_arrow',
			[
				'label'   => esc_html__( 'On Hover Arrow', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control(
			'slider_center_mode',
			[
				'label'   => esc_html__( 'Center Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
            'center_padding',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Center Padding', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
				'condition'    => [
					'slider_center_mode' => ['yes'],
				],
            ]
        );
		$this->add_control(
			'slider_center_effects',
			[
				'label'   => esc_html__( 'Center Slide Effects', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => theplus_carousel_center_effects(),
				'condition'    => [
					'slider_center_mode' => ['yes'],
				],
			]
		);
		$this->add_control(
            'scale_center_slide',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Center Slide Scale', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0.3,
						'max' => 2,
						'step' => 0.02,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-slide.slick-current.slick-active.slick-center,
					{{WRAPPER}} .list-carousel-slick .slick-slide.scc-animate' => '-webkit-transform: scale({{SIZE}});-moz-transform:    scale({{SIZE}});-ms-transform:     scale({{SIZE}});-o-transform:      scale({{SIZE}});transform:scale({{SIZE}});opacity:1;',
				],
				'condition' => [
					'slider_center_mode' => 'yes',
					'slider_center_effects' => 'scale',
				],
            ]
        );
		$this->add_control(
            'scale_normal_slide',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Normal Slide Scale', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0.3,
						'max' => 2,
						'step' => 0.02,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0.8,
				],
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-slide' => '-webkit-transform: scale({{SIZE}});-moz-transform:    scale({{SIZE}});-ms-transform:     scale({{SIZE}});-o-transform:      scale({{SIZE}});transform:scale({{SIZE}});',
				],
				'condition' => [
					'slider_center_mode' => 'yes',
					'slider_center_effects' => 'scale',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'shadow_active_slide',
				'selector' => '{{WRAPPER}} .list-carousel-slick .slick-slide.slick-current.slick-active.slick-center .info-box-bg-box',
				'condition' => [
					'slider_center_mode' => 'yes',
					'slider_center_effects' => 'shadow',
				],
			]
		);
		$this->add_control(
            'opacity_normal_slide',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Normal Slide Opacity', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0.1,
						'max' => 1,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0.7,
				],
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-slide' => 'opacity:{{SIZE}}',
				],
				'condition' => [
					'slider_center_mode' => 'yes',
					'slider_center_effects!' => 'none',
				],
            ]
        );
		$this->add_control(
			'slider_rows',
			[
				'label'   => esc_html__( 'Number Of Rows', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					"1" => esc_html__("1 Row", 'theplus'),
					"2" => esc_html__("2 Rows", 'theplus'),
					"3" => esc_html__("3 Rows", 'theplus'),
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
            'slide_row_top_space',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Row Top Space', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick[data-slider_rows="2"] .slick-slide > div:last-child,{{WRAPPER}} .list-carousel-slick[data-slider_rows="3"] .slick-slide > div:nth-last-child(-n+2)' => 'padding-top:{{SIZE}}px',
				],
				'condition'    => [
					'slider_rows' => ['2','3'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_carousel_tablet',
			[
				'label' => esc_html__( 'Tablet', 'theplus' ),
			]
		);
		$this->add_control(
			'slider_tablet_column',
			[
				'label'   => esc_html__( 'Tablet Columns', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '3',
				'options' => theplus_carousel_tablet_columns(),
			]
		);
		$this->add_control(
			'tablet_steps_slide',
			[
				'label'   => esc_html__( 'Next Previous', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'description' => esc_html__( 'Select option of column scroll on previous or next in carousel.','theplus' ),
				'options' => [
					'1'  => esc_html__( 'One Column', 'theplus' ),
					'2' => esc_html__( 'All Visible Columns', 'theplus' ),
				],
			]
		);
 
		$this->add_control(
			'slider_responsive_tablet',
			[
				'label'   => esc_html__( 'Responsive Tablet', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'tablet_slider_draggable',
			[
				'label'   => esc_html__( 'Draggable', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_slider_infinite',
			[
				'label'   => esc_html__( 'Infinite Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_slider_autoplay',
			[
				'label'   => esc_html__( 'Autoplay', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
            'tablet_autoplay_speed',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Autoplay Speed', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 500,
						'max' => 10000,
						'step' => 200,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1500,
				],
				'condition' => [
					'slider_responsive_tablet' => 'yes',
					'tablet_slider_autoplay' => 'yes',
				],
            ]
        );
		$this->add_control(
			'tablet_slider_dots',
			[
				'label'   => esc_html__( 'Show Dots', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_slider_arrows',
			[
				'label'   => esc_html__( 'Show Arrows', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_slider_rows',
			[
				'label'   => esc_html__( 'Number Of Rows', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					"1" => esc_html__("1 Row", 'theplus'),
					"2" => esc_html__("2 Rows", 'theplus'),
					"3" => esc_html__("3 Rows", 'theplus'),
				],
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_center_mode',
			[
				'label'   => esc_html__( 'Center Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
            'tablet_center_padding',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Center Padding', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
					'tablet_center_mode' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_carousel_mobile',
			[
				'label' => esc_html__( 'Mobile', 'theplus' ),
			]
		);
		$this->add_control(
			'slider_mobile_column',
			[
				'label'   => esc_html__( 'Mobile Columns', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '2',
				'options' => theplus_carousel_mobile_columns(),
			]
		);
		$this->add_control(
			'mobile_steps_slide',
			[
				'label'   => esc_html__( 'Next Previous', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'description' => esc_html__( 'Select option of column scroll on previous or next in carousel.','theplus' ),
				'options' => [
					'1'  => esc_html__( 'One Column', 'theplus' ),
					'2' => esc_html__( 'All Visible Columns', 'theplus' ),
				],
			]
		);
 
		$this->add_control(
			'slider_responsive_mobile',
			[
				'label'   => esc_html__( 'Responsive Mobile', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'mobile_slider_draggable',
			[
				'label'   => esc_html__( 'Draggable', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_slider_infinite',
			[
				'label'   => esc_html__( 'Infinite Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_slider_autoplay',
			[
				'label'   => esc_html__( 'Autoplay', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
            'mobile_autoplay_speed',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Autoplay Speed', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 500,
						'max' => 10000,
						'step' => 200,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1500,
				],
				'condition' => [
					'slider_responsive_mobile' => 'yes',
					'mobile_slider_autoplay' => 'yes',
				],
            ]
        );
		$this->add_control(
			'mobile_slider_dots',
			[
				'label'   => esc_html__( 'Show Dots', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_slider_arrows',
			[
				'label'   => esc_html__( 'Show Arrows', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_slider_rows',
			[
				'label'   => esc_html__( 'Number Of Rows', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					"1" => esc_html__("1 Row", 'theplus'),
					"2" => esc_html__("2 Rows", 'theplus'),
					"3" => esc_html__("3 Rows", 'theplus'),
				],
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_center_mode',
			[
				'label'   => esc_html__( 'Center Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
            'mobile_center_padding',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Center Padding', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
					'mobile_center_mode' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*carousel option*/
		/*Extra options*/
		$this->start_controls_section(
            'section_extra_option_styling',
            [
                'label' => esc_html__('Extra Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
			'box_padding',
			[
				'label' => esc_html__( 'Box Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default' =>[
					'top' => '15',
					'right' => '15',
					'bottom' => '15',
					'left' => '15',
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .info-box-bg-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .pt_plus_info_box.info-box-style_3 .icon-overlay ' => 'top: calc(0% - {{TOP}}{{UNIT}});',
					'{{WRAPPER}} .pt_plus_info_box.info-box-style_1 .icon-overlay .m-r-16' => 'left: calc(0% - {{LEFT}}{{UNIT}});',
					'{{WRAPPER}} .pt_plus_info_box.info-box-style_2 .icon-overlay .m-l-16' => 'right: calc(0% - {{RIGHT}}{{UNIT}});',
				],
			]
		);
		$this->add_control(
			'vertical_center',
			[
				'label' => esc_html__( 'Vertical Center', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'main_style' => ['style_1','style_2','style_4'],
				],
			]
		);
 
		$this->add_control(
			'tilt_parallax',
			[
				'label'        => esc_html__( 'Tilt 3D Parallax', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'theplus' ),
				'label_off'    => esc_html__( 'No', 'theplus' ),					
				'render_type'  => 'template',
				'separator' => 'before',
				'condition'    => [
					'main_style' => ['style_3'],
				],
			]
		);
		$this->add_group_control(
			\Theplus_Tilt_Parallax_Group::get_type(),
			[
				'label' => esc_html__( 'Tilt Options', 'theplus' ),
				'name'           => 'tilt_opt',
				'render_type'  => 'template',
				'condition'    => [
					'main_style' => ['style_3'],
					'tilt_parallax' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'messy_column',
			[
				'label' => esc_html__( 'Messy Columns', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'info_box_layout' => 'carousel_layout',
				],
			]
		);
		$this->add_control(
            'messy_column_even',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Even Columns', 'theplus'),
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 2,
					],
					'%' => [
						'min' => 70,
						'max' => 70,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .slick-initialized .slick-slide.info-box-inner:nth-child(2n+1)' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition'    => [
					'info_box_layout' => 'carousel_layout',
					'messy_column' => ['yes'],
				],
            ]
        );
		$this->add_control(
            'messy_column_odd',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Odd Columns', 'theplus'),
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 2,
					],
					'%' => [
						'min' => 70,
						'max' => 70,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 70,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .slick-initialized .slick-slide.info-box-inner:nth-child(2n+2)' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition'    => [
					'info_box_layout' => 'carousel_layout',
					'messy_column' => ['yes'],
				],
            ]
        );
		$this->add_control(
			'min_height_section',[
				'label'   => esc_html__( 'Minimum Height Section', 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'minimum_height',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Minimum Height', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 40,
						'max' => 700,
						'step' => 5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 350,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box .info-box-inner .info-box-bg-box' => 'min-height: {{SIZE}}{{UNIT}};display: -webkit-box;display: -ms-flexbox;display: flex;-webkit-box-orient: vertical;-webkit-align-items: center;-ms-align-items: center;align-items: center;',				
				],
				'condition'    => [
					'min_height_section' => 'yes',
				],
            ]
        );
		$this->add_responsive_control(
			'minimum_height_align_st3',
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
				'condition'    => [
					'main_style' => 'style_3',
					'min_height_section' => 'yes',			
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box.info-box-style_3 .info-box-inner .info-box-bg-box' => '-webkit-justify-content: {{VALUE}};-moz-justify-content: {{VALUE}};-ms-justify-content: {{VALUE}};justify-content: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'minimum_height_align_st2',
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
				'default' => 'flex-end',
				'condition'    => [
					'main_style' => 'style_2',
					'min_height_section' => 'yes',			
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_info_box.info-box-style_2 .info-box-inner .info-box-bg-box' => '-webkit-justify-content: {{VALUE}};-moz-justify-content: {{VALUE}};-ms-justify-content: {{VALUE}};justify-content: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'box_hover_effects',
			[
				'label'   => esc_html__( 'Box Hover Effects', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => theplus_get_content_hover_effect_options(),
				'separator' => 'before',
			]
		);
		$this->add_control(
            'box_hover_shadow_color',
            [
                'label' => esc_html__('Shadow Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'rgba(0, 0, 0, 0.6)',
				'condition'    => [
					'box_hover_effects' => ['float_shadow','grow_shadow','shadow_radial'],
				],
            ]
        );
		$this->add_control(
			'box_hover_shadow_color_note',
			[				
				'type' => Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Note : Works in Frontend.', 'theplus' ),
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'box_hover_effects' => ['float_shadow','grow_shadow','shadow_radial'],
				],	
			]
		);
		$this->add_control(
			'responsive_visible_opt',[
				'label'   => esc_html__( 'Responsive Visibility', 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'separator' => 'before',
			]
		);
		$this->add_control(
			'desktop_opt',[
				'label'   => esc_html__( 'Desktop', 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'condition'    => [
					'responsive_visible_opt' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_opt',[
				'label'   => esc_html__( 'Tablet', 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'condition'    => [
					'responsive_visible_opt' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_opt',[
				'label'   => esc_html__( 'Mobile', 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'condition'    => [
					'responsive_visible_opt' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*box padding*/
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
		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation.php';
	}
	protected function render() {
        $settings = $this->get_settings_for_display();
 
		$info_box_layout = !empty($settings["info_box_layout"]) ? $settings["info_box_layout"] : 'single_layout';
		$main_style = !empty($settings["main_style"]) ? $settings["main_style"] : 'style_1';
		$full_infobox_switch = $settings["full_infobox_switch"];
		$full_infobox_link = !empty($settings["full_infobox_link"]["url"]) ? $settings["full_infobox_link"]["url"] : '#';
		$fullInfoboxBlank = !empty($settings["full_infobox_link"]['is_external']) ? '_blank' : '';
		$fc_load_class='';
		//r_full_infobox_switch
		if((!empty($full_infobox_switch) && $full_infobox_switch == 'yes') || (!empty($item['r_full_infobox_switch']) && $item['r_full_infobox_switch'] == 'yes')){
			$fc_load_class = 'tp-info-fbc';
		}else{
			$fc_load_class = 'tp-info-nc';
		}
 
		$icon_shine='';
		if(!empty($settings["icon_shine_effect"]) && $settings["icon_shine_effect"] == 'yes'){
			$icon_shine = 'icon_shine_show';
		}
 
		$hover_class  = $hover_attr = '';
		$hover_uniqid = uniqid('hover-effect');
		if ($settings["box_hover_effects"] == "float_shadow" || $settings["box_hover_effects"] == "grow_shadow" || $settings["box_hover_effects"] == "shadow_radial") {
			$hover_attr .= 'data-hover_uniqid="' . esc_attr($hover_uniqid) . '" ';
			$hover_attr .= ' data-hover_shadow="' . esc_attr($settings["box_hover_shadow_color"]) . '" ';
			$hover_attr .= ' data-content_hover_effects="' . esc_attr($settings["box_hover_effects"]) . '" ';
		}
		$box_hover_effects = $settings["box_hover_effects"];
		if ($box_hover_effects == "grow") {
			$hover_class .= 'content_hover_grow';
		} elseif ($box_hover_effects == "push") {
			$hover_class .= 'content_hover_push';
		} elseif ($box_hover_effects == "bounce-in") {
			$hover_class .= 'content_hover_bounce_in';
		} elseif ($box_hover_effects == "float") {
			$hover_class .= 'content_hover_float';
		} elseif ($box_hover_effects == "wobble_horizontal") {
			$hover_class .= 'content_hover_wobble_horizontal';
		} elseif ($box_hover_effects == "wobble_vertical") {
			$hover_class .= 'content_hover_wobble_vertical';
		} elseif ($box_hover_effects == "float_shadow") {
			$hover_class .= ' ' . esc_attr($hover_uniqid) . ' content_hover_float_shadow';
		} elseif ($box_hover_effects == "grow_shadow") {
			$hover_class .= ' ' . esc_attr($hover_uniqid) . ' content_hover_grow_shadow';
		} elseif ($box_hover_effects == "shadow_radial") {
			$hover_class .= '' . esc_attr($hover_uniqid) . ' content_hover_radial';
		}
 
		/*--OnScroll View Animation ---*/
		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';
		/*--Plus Extra ---*/
		$PlusExtra_Class = "";
		$wname = "tpinfobox";
		include THEPLUS_PATH. 'modules/widgets/theplus-widgets-extra.php';
 
		$service_title = $description= $service_img = $service_center= $service_align = $service_border = $service_icon_style= $service_space = $serice_box_border =$serice_img_border=$border_right_css=$imge_content=$title_css=$subtitle_css=$output='';
 
		$text_align=$settings["text_align"];
		if($text_align == 'left'){
			$service_align = 'text-left';
		}
		if($text_align == 'center'){
			$service_align = 'text-center';
		}
		if($text_align == 'right'){
			$service_align = 'text-right';
		} 
		if($settings['box_border'] == 'yes'){
			$serice_box_border ='service-border-box';		
		}
		if($settings['vertical_center'] == 'yes'){
			$service_center = 'vertical-center';
		}
 
		if ( ! empty( $settings['url_link']['url'] ) ) {			
			$this->add_link_attributes( 'box_link', $settings['url_link'] );
		}
 
		//Image
		$image_icon=$settings["image_icon"];
		if(!empty($image_icon) && $image_icon == 'lottie'){
			$ext = pathinfo($settings['lottieUrl']['url'], PATHINFO_EXTENSION);			
			if($ext!='json'){
				$service_img = '<h3 class="theplus-posts-not-found">'.esc_html__("Opps!! Please Enter Only JSON File Extension.",'theplus').'</h3>';
			}else{
				$lottieWidth = isset($settings['lottieWidth']['size']) ? $settings['lottieWidth']['size'] : 50;
				$lottieHeight = isset($settings['lottieHeight']['size']) ? $settings['lottieHeight']['size'] : 50;
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
 
				$service_img ='<lottie-player src="'.esc_url($settings['lottieUrl']['url']).'" style="width: '.esc_attr($lottieWidth).'px; height: '.esc_attr($lottieHeight).'px;" '.esc_attr($lottieLoopValue).'  speed="'.esc_attr($lottieSpeed).'" '.esc_attr($lottieAnim).'></lottie-player>';
			}
		}
		if($image_icon == 'image'){
			$image_alt='';
			$imgSrc = '';
			if(!empty($settings["select_image"]["url"])){
				$image_id=$settings["select_image"]["id"];				
				$imgSrc= tp_get_image_rander( $image_id,$settings['select_image_thumbnail_size'], [ 'class' => 'service-img' ] );
			}
			$service_a_start=$service_a_end='';
			if (!empty($settings['url_link']['url'])){
				$service_a_start= '<a '.$this->get_render_attribute_string( "box_link" ).' >';
				$service_a_end= '</a>';
			}
			$service_img = wp_kses_post($service_a_start).$imgSrc.wp_kses_post($service_a_end);
		}
 
		//font Icon
		$icon_style=$settings["icon_style"];
		$svg_icon_style=$settings["svg_icon_style"];
		if(($icon_style == 'square' && ($image_icon=='icon' || $settings["loop_select_icon"]=='icon')) || ($svg_icon_style == 'square' && ($image_icon=='svg' || $settings["loop_select_icon"]=='svg'))){
			$service_icon_style = 'icon-squre';
		} 
		if(($icon_style == 'rounded' && ($image_icon=='icon' || $settings["loop_select_icon"]=='icon')) || ($svg_icon_style == 'rounded' && ($image_icon=='svg' || $settings["loop_select_icon"]=='svg'))){
			$service_icon_style = 'icon-rounded';
		} 	
		if(($icon_style == 'hexagon' && ($image_icon=='icon' || $settings["loop_select_icon"]=='icon')) || ($svg_icon_style == 'hexagon' && ($image_icon=='svg' || $settings["loop_select_icon"]=='svg'))){
			$service_icon_style = 'icon-hexagon';
		} 	
		if(($icon_style == 'pentagon' && ($image_icon=='icon' || $settings["loop_select_icon"]=='icon')) || ($svg_icon_style == 'pentagon' && ($image_icon=='svg' || $settings["loop_select_icon"]=='svg'))){
			$service_icon_style = 'icon-pentagon';
		}  	
		if(($icon_style == 'square-rotate' && ($image_icon=='icon' || $settings["loop_select_icon"]=='icon')) || ($svg_icon_style == 'square-rotate' && ($image_icon=='svg' || $settings["loop_select_icon"]=='svg'))){
			$service_icon_style = 'icon-square-rotate';
		}
		if($image_icon == 'icon'){
			if($settings["icon_font_style"]=='font_awesome'){
				$icons = $settings["icon_fontawesome"];
			}else if($settings["icon_font_style"]=='icon_mind'){
				$icons = $settings["icons_mind"];
			}else if($settings["icon_font_style"]=='font_awesome_5'){
				ob_start();
					\Elementor\Icons_Manager::render_icon( $settings['icon_fontawesome_5'], [ 'aria-hidden' => 'true' ]);
					$icons = ob_get_contents();
				ob_end_clean();
			}else if($settings["icon_font_style"]=='icon_image' && !empty($settings["icons_image"]["url"])){
				$image_id=$settings["icons_image"]["id"];				
				$icons_image_src= tp_get_image_rander( $image_id,$settings['icons_image_thumbnail_size'], [ 'class' => 'icon-image-set' ] );
 
 
				$icons_image = '<div class="service-icon-image '.esc_attr($icon_shine).' service-icon '.esc_attr($service_icon_style).'">'.$icons_image_src.'</div>';
			}else{
				$icons = $icons_image='';
			}
			if(!empty($icons)){
				if(!empty($settings["icon_font_style"]) && $settings["icon_font_style"]=='font_awesome_5'){
					$service_img = '<div class="service-icon-wrap"><span class=" service-icon '.esc_attr($icon_shine).' '.esc_attr($service_icon_style).'">'.$icons.'</span></div>';
				}else{
					$service_img = '<div class="service-icon-wrap"><i class=" '.esc_attr($icons).' service-icon '.esc_attr($icon_shine).' '.esc_attr($service_icon_style).'"></i></div>';
				}
			}
			if(!empty($icons_image)){
				$service_img = $icons_image;
			}
		}
 
		//Svg Icon
		if(!empty($settings['border_stroke_color'])){
			$border_stroke_color = $settings['border_stroke_color'];
		}else{
			$border_stroke_color = 'none';
		}
 
		if(!empty($settings['border_stroke_color_hover'])){
			$border_stroke_color_hover = $settings['border_stroke_color_hover'];
		}else{
			$border_stroke_color_hover = '';
		}
 
		if(!empty($settings['svg_stroke_none']) && $settings['svg_stroke_none'] == 'yes'){
			$border_stroke_color = 'none';
			$border_stroke_color_hover = 'none';
		}
 
		if(!empty($settings['draw_animated_svg']) && $settings['draw_animated_svg']=='yes'){
			$duration = 1;										
		}else{
			$duration = (!empty($settings["duration"]["size"])) ? $settings["duration"]["size"] : 30;
		}
		if($settings['svg_fill_color'] == 'yes'){
			$svg_fill_color = $settings['svg_fill_color_f'];
			$svg_fill_color_hover = $settings['svg_fill_color_hover'];
		}else{
			$svg_fill_color = 'none';
			$svg_fill_color_hover = '';
		}
		if($image_icon == 'svg'){
			if($settings['svg_icon'] == 'img'){
				$svg_url = $settings['svg_image']['url'];
			}else{
				$svg_url = THEPLUS_URL.'assets/images/svg/'.esc_attr($settings["svg_d_icon"]); 
			}
			$rand_no = rand(1000000, 1500000);

			$service_img ='<div class="pt_plus_animated_svg  svg-'.esc_attr($rand_no).'" data-id="svg-'.esc_attr($rand_no).'" data-type="'.esc_attr($settings["svg_type"]).'" data-duration="'.esc_attr($duration).'" data-stroke="'.esc_attr($border_stroke_color).'" data-fill_color="'.esc_attr($svg_fill_color).'" data-fillhover="'.esc_attr($svg_fill_color_hover).'" data-strokehover="'.esc_attr($border_stroke_color_hover).'">';
				$service_img .='<div class="info_box_svg svg_inner_block '.esc_attr($service_icon_style).'">';
					//@since 4.1.9
					$svg_url_pass='';
					$ext = pathinfo($svg_url, PATHINFO_EXTENSION);
					if(!empty($svg_url) && $ext=='svg' ){						
						$svg_url_pass = $svg_url;
					}
					$service_img .='<object aria-label="'.$settings["title"].'" id="svg-'.esc_attr($rand_no).'" type="image/svg+xml" data="'.esc_url($svg_url_pass).'" style="max-width:'.$settings["max_width"]["size"].$settings["max_width"]["unit"].';max-height:'.$settings["max_width"]["size"].$settings["max_width"]["unit"].';margin: 0 auto;" ></object>';
				$service_img .='</div>';
			$service_img .='</div>';
		}
		if($settings['border_check_right'] == 'yes'){
			$serice_img_border ='service-img-border';
			$border_right_css = ' style="';
			if(!empty($settings['border_right_color'])) {
			$border_right_css .= 'border-color: '.esc_attr($settings["border_right_color"]).';';
			}		
			$border_right_css .= '"';
		}
 
		$title_tag=!empty($settings['title_tag']) ? $settings['title_tag'] : 'div';
		if(!empty($settings["title"])){			 
			if ((!empty($settings['url_link']['url'])) && ($full_infobox_switch != 'yes')){
				$service_title= '<a '.$this->get_render_attribute_string( "box_link" ).' ><'.theplus_validate_html_tag($title_tag).' class="service-title "> '.wp_kses_post($settings["title"]).' </'.theplus_validate_html_tag($title_tag).'></a>';
			}else{
				$service_title= '<'.theplus_validate_html_tag($title_tag).' class="service-title "> '.wp_kses_post($settings["title"]).' </'.theplus_validate_html_tag($title_tag).'>';
			}
		}
 
		$border_check=$settings["border_check"];
		if($border_check == 'yes'){
			$service_border = '<div class="service-border"> </div>' ;
		}
 
		$content_desc = $settings['content_desc'];
		if(!empty($content_desc)){
			 $description='<div class="service-desc"> '.wp_kses_post($content_desc).' </div>';
		}
		//carousel option
		$isotope =$data_slider =$arrow_class=$data_carousel='';
		if($info_box_layout == 'carousel_layout'){
 
			$slider_direction = ($settings['slider_direction'] == 'vertical') ? 'true' : 'false';
			$data_slider .=' data-slider_direction="'.esc_attr($slider_direction).'"';
			$data_slider .=' data-slide_speed="'.(isset($settings["slide_speed"]["size"]) ? esc_attr($settings["slide_speed"]["size"]) : 1500).'"';
 
			$data_slider .=' data-slider_desktop_column="'.(isset($settings["slider_desktop_column"]) ? esc_attr($settings['slider_desktop_column']) : 4).'"';
			$data_slider .=' data-steps_slide="'.(isset($settings['steps_slide']) ? esc_attr($settings['steps_slide']) : 1).'"';
 
			$slider_draggable = ($settings["slider_draggable"] == 'yes') ? 'true' : 'false';
			$multi_drag = ($settings["multi_drag"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_draggable="'.esc_attr($slider_draggable).'"';
			$data_slider .=' data-multi_drag="'.esc_attr($multi_drag).'"';
			$slider_infinite = ($settings["slider_infinite"] == 'yes') ? 'true' : 'false';
			$data_slider .=' data-slider_infinite="'.esc_attr($slider_infinite).'"';
			$slider_pause_hover = ($settings["slider_pause_hover"] == 'yes') ? 'true' : 'false';
			$data_slider .=' data-slider_pause_hover="'.esc_attr($slider_pause_hover).'"';
			$slider_adaptive_height = ($settings["slider_adaptive_height"] == 'yes') ? 'true' : 'false';
			$data_slider .=' data-slider_adaptive_height="'.esc_attr($slider_adaptive_height).'"';
 
			$slide_fade_inout= ($settings['slider_direction']=='horizontal' && $settings["slide_fade_inout"]=='fadeinout') ? 'true' : 'false';		
			$data_slider .=' data-slide_fade_inout="'.esc_attr($slide_fade_inout).'"';
 
 
			$slider_animation = (isset($settings['slider_animation']) ? $settings['slider_animation'] : 'ease');
			$data_slider .=' data-slider_animation="'.esc_attr($slider_animation).'"';
			$slider_autoplay = ($settings["slider_autoplay"] == 'yes') ? 'true' : 'false';
			$data_slider .=' data-slider_autoplay="'.esc_attr($slider_autoplay).'"';
			$data_slider .=' data-autoplay_speed="'.(isset($settings["autoplay_speed"]["size"]) ? esc_attr($settings["autoplay_speed"]["size"]) : 3000).'"';
 
			//tablet
			$data_slider .=' data-slider_tablet_column="'.(isset($settings['slider_tablet_column']) ? esc_attr($settings['slider_tablet_column']) : 3).'"';
			$data_slider .=' data-tablet_steps_slide="'.(isset($settings['tablet_steps_slide']) ? esc_attr($settings['tablet_steps_slide']) : 1).'"';
			$slider_responsive_tablet = $settings['slider_responsive_tablet'];
			$data_slider .=' data-slider_responsive_tablet="'.esc_attr($slider_responsive_tablet).'"';
			if(!empty($slider_responsive_tablet) && $slider_responsive_tablet == 'yes'){
				$tablet_slider_draggable = ($settings["tablet_slider_draggable"] == 'yes') ? 'true' : 'false';
				$data_slider .=' data-tablet_slider_draggable="'.esc_attr($tablet_slider_draggable).'"';
				$tablet_slider_infinite = ($settings["tablet_slider_infinite"] == 'yes') ? 'true' : 'false';
				$data_slider .=' data-tablet_slider_infinite="'.esc_attr($tablet_slider_infinite).'"';
				$tablet_slider_autoplay= ($settings["tablet_slider_autoplay"] == 'yes') ? 'true' : 'false';
				$data_slider .=' data-tablet_slider_autoplay="'.esc_attr($tablet_slider_autoplay).'"';
				$data_slider .=' data-tablet_autoplay_speed="'.(isset($settings["tablet_autoplay_speed"]["size"]) ? esc_attr($settings["tablet_autoplay_speed"]["size"]) : 1500).'"';
				$tablet_slider_dots= ($settings["tablet_slider_dots"] == 'yes') ? 'true' : 'false';
				$data_slider .=' data-tablet_slider_dots="'.esc_attr($tablet_slider_dots).'"';
				$tablet_slider_arrows = ($settings["tablet_slider_arrows"] == 'yes') ? 'true' : 'false';
				$data_slider .=' data-tablet_slider_arrows="'.esc_attr($tablet_slider_arrows).'"';
				$data_slider .=' data-tablet_slider_rows="'.(isset($settings["tablet_slider_rows"]) ? esc_attr($settings["tablet_slider_rows"]) : 1).'"';
				$tablet_center_mode = ($settings["tablet_center_mode"] == 'yes') ? 'true' : 'false';
				$data_slider .=' data-tablet_center_mode="'.esc_attr($tablet_center_mode).'" ';
				$data_slider .=' data-tablet_center_padding="'.(isset($settings["tablet_center_padding"]["size"]) ? esc_attr($settings["tablet_center_padding"]["size"]) : 0).'" ';
			}
 
			//mobile 
			$data_slider .=' data-slider_mobile_column="'.(isset($settings['slider_mobile_column']) ? esc_attr($settings['slider_mobile_column']) : 2).'"';
			$data_slider .=' data-mobile_steps_slide="'.(isset($settings['mobile_steps_slide']) ? esc_attr($settings['mobile_steps_slide']) : 1).'"';
			$slider_responsive_mobile = $settings['slider_responsive_mobile'];			
			$data_slider .=' data-slider_responsive_mobile="'.esc_attr($slider_responsive_mobile).'"';
			if(!empty($slider_responsive_mobile) && $slider_responsive_mobile == 'yes'){
				$mobile_slider_draggable = ($settings["mobile_slider_draggable"] == 'yes') ? 'true' : 'false';
				$data_slider .=' data-mobile_slider_draggable="'.esc_attr($mobile_slider_draggable).'"';
				$mobile_slider_infinite = ($settings["mobile_slider_infinite"] == 'yes') ? 'true' : 'false';
				$data_slider .=' data-mobile_slider_infinite="'.esc_attr($mobile_slider_infinite).'"';
				$mobile_slider_autoplay = ($settings["mobile_slider_autoplay"] == 'yes') ? 'true' : 'false';
				$data_slider .=' data-mobile_slider_autoplay="'.esc_attr($mobile_slider_autoplay).'"';
				$data_slider .=' data-mobile_autoplay_speed="'.(isset($settings["mobile_autoplay_speed"]["size"]) ? esc_attr($settings["mobile_autoplay_speed"]["size"]) : 1500).'"';    
				$mobile_slider_dots = ($settings["mobile_slider_dots"] == 'yes') ? 'true' : 'false';
				$data_slider .=' data-mobile_slider_dots="'.esc_attr($mobile_slider_dots).'"';
				$mobile_slider_arrows = ($settings["mobile_slider_arrows"] == 'yes') ? 'true' : 'false';
				$data_slider .=' data-mobile_slider_arrows="'.esc_attr($mobile_slider_arrows).'"';
				$data_slider .=' data-mobile_slider_rows="'.(isset($settings["mobile_slider_rows"]) ? esc_attr($settings["mobile_slider_rows"]) : 1).'"';
				$mobile_center_mode = ($settings["mobile_center_mode"] == 'yes') ? 'true' : 'false';
				$data_slider .=' data-mobile_center_mode="'.esc_attr($mobile_center_mode).'" ';
				$data_slider .=' data-mobile_center_padding="'.(isset($settings["mobile_center_padding"]["size"]) ? esc_attr($settings["mobile_center_padding"]["size"]) : 0).'"';
			}
 
			$slider_dots = ($settings["slider_dots"] == 'yes') ? 'true' : 'false';
			$data_slider .=' data-slider_dots="'.esc_attr($slider_dots).'"';
			$data_slider .=' data-slider_dots_style="slick-dots '.(isset($settings["slider_dots_style"]) ? esc_attr($settings["slider_dots_style"]) : 'style-1').'" ';
 
			$slider_arrows = ($settings["slider_arrows"] == 'yes') ? 'true' : 'false';
			$data_slider .=' data-slider_arrows="'.esc_attr($slider_arrows).'"';
			$data_slider .=' data-slider_arrows_style="'.(isset($settings["slider_arrows_style"]) ? esc_attr($settings["slider_arrows_style"]) : 'style-1').'" ';
			$data_slider .=' data-arrows_position="'.(isset($settings["arrows_position"]) ? esc_attr($settings["arrows_position"]) : 'top-right').'" ';
			$data_slider .=' data-arrow_bg_color="'.(isset($settings["arrow_bg_color"]) ? esc_attr($settings["arrow_bg_color"]) : '#c44d48').'" ';
			$data_slider .=' data-arrow_icon_color="'.(isset($settings["arrow_icon_color"]) ? esc_attr($settings["arrow_icon_color"]) : '#fff').'" ';
			$data_slider .=' data-arrow_hover_bg_color="'.(isset($settings["arrow_hover_bg_color"]) ? esc_attr($settings["arrow_hover_bg_color"]) : '#fff').'" ';
			$data_slider .=' data-arrow_hover_icon_color="'.(isset($settings["arrow_hover_icon_color"]) ? esc_attr($settings["arrow_hover_icon_color"]) : '#c44d48').'" ';  
 
			$slider_center_mode = ( $settings["slider_center_mode"] == 'yes' ) ? 'true' : 'false';
			$data_slider .=' data-slider_center_mode="'.esc_attr($slider_center_mode).'" ';
			$data_slider .=' data-center_padding="'.(isset($settings["center_padding"]["size"]) ? esc_attr($settings["center_padding"]["size"]) : 0).'" ';
			$data_slider .=' data-scale_center_slide="'.(isset($settings["scale_center_slide"]["size"]) ? esc_attr($settings["scale_center_slide"]["size"]) : 1).'" ';
			$data_slider .=' data-scale_normal_slide="'.(isset($settings["scale_normal_slide"]["size"]) ? esc_attr($settings["scale_normal_slide"]["size"]) : 0.8).'" ';
			$data_slider .=' data-opacity_normal_slide="'.(isset($settings["opacity_normal_slide"]["size"]) ? esc_attr($settings["opacity_normal_slide"]["size"]) : 0.7).'" ';
			$data_slider .=' data-slider_rows="'.(isset($settings["slider_rows"]) ? esc_attr($settings["slider_rows"]) : 1).'" ';
			$isotope = 'list-carousel-slick';
 
			if($settings["slider_arrows_style"]=='style-3' || $settings["slider_arrows_style"]=='style-4'){
				$arrow_class=$settings["arrows_position"];
			}
			if(($settings["slider_rows"] > 1) || ($settings["tablet_slider_rows"] > 1) || ($settings["mobile_slider_rows"] > 1)){
				$arrow_class .= ' multi-row';
			}
			if(!empty($settings["hover_show_dots"]) && $settings["hover_show_dots"]=='yes'){
				$data_carousel .=' hover-slider-dots';
			}
			if(!empty($settings["hover_show_arrow"]) && $settings["hover_show_arrow"]=='yes'){
				$data_carousel .=' hover-slider-arrow';
			}
			if(!empty($settings["outer_section_arrow"]) && $settings["outer_section_arrow"]=='yes' && ($settings["slider_arrows_style"]=='style-1' || $settings["slider_arrows_style"]=='style-2' || $settings["slider_arrows_style"]=='style-5' || $settings["slider_arrows_style"]=='style-6')){
				$data_carousel .=' outer-slider-arrow';
			}
 
		}
		$the_button='';
		if($settings['display_button'] == 'yes'){
		if ( ! empty( $settings['button_link']['url'] ) ) {
			$this->add_link_attributes( 'button', $settings['button_link'] );
		}
		$this->add_render_attribute( 'button', 'class', 'button-link-wrap' );
		$hover_box_class = (!empty($settings["hover_info_button"]) && $settings["hover_info_button"]=='yes') ? ' hover_box_button' : '';
		$this->add_render_attribute( 'button', 'class', $hover_box_class );
		$this->add_render_attribute( 'button', 'role', 'button' );
 
		$button_style = $settings['button_style'];
		$button_text = $settings['button_text'];
		$btn_uid=uniqid('btn');
		$data_class = $btn_uid;
		$data_class .=' button-'.$button_style.' ';
 
		$the_button ='<div class="pt-plus-button-wrapper">';
			$the_button .='<div class="button_parallax">';
				$the_button .='<div class="ts-button">';
					$the_button .='<div class="pt_plus_button '.esc_attr($data_class).'">';
						$the_button .= '<div class="animted-content-inner">';
							if(!empty($full_infobox_switch) && $full_infobox_switch=='yes'){
								$the_button .='<div class="button-link-wrap">';
							}else{
								$the_button .='<a '.$this->get_render_attribute_string( "button" ).'>';
							}							
							$the_button .= $this->render_text();
							if(!empty($full_infobox_switch) && $full_infobox_switch=='yes'){
								$the_button .='</div>';
							}else{
								$the_button .='</a>';
							}							
						$the_button .='</div>';
					$the_button .='</div>';
				$the_button .='</div>';
			$the_button .='</div>';
		$the_button .='</div>';
		}
 
		if ( $info_box_layout == 'carousel_layout' ){
			if( !empty($settings["loop_content"]) ) {
				$index=0;
				foreach($settings["loop_content"] as $item) {
					$tp_infobox_count = $index;
					$on_load_class='';
					$default_active=$settings['default_active'];

					if($tp_infobox_count==$default_active && $settings['connection_switch']=='yes'){
						$on_load_class = 'tp-info-active';		
					}

					if((!empty($full_infobox_switch) && $full_infobox_switch=='yes') || (!empty($item['r_full_infobox_switch']) && $item['r_full_infobox_switch']=='yes')){
						$fc_load_class = 'tp-info-fbc';
					}else{
						$fc_load_class = 'tp-info-nc';
					}

					$loop_svg_d_icon=$svg_type=$loop_image_icon=$svg_image=$loop_max_width=$description=$loop_title=$list_subtitle=$list_title=$loop_btn_text=$list_img='';

					if ( ! empty( $item['loop_url_link']['url'] ) ) {
						$this->add_link_attributes( 'loop_box_link'.$index, $item['loop_url_link'] );
					}

					if(!empty($item['loop_title'])){
						$loop_title = $item['loop_title'];
						$loop_title_tag = !empty($settings['loop_title_tag']) ? $settings['loop_title_tag'] : 'h6';
						if ((!empty($item['loop_url_link']['url'])) && ($item['r_full_infobox_switch'] !='yes')){
							$list_title = '<a '.$this->get_render_attribute_string( "loop_box_link".$index ).'><'.theplus_validate_html_tag($loop_title_tag).' class="service-title ">'.wp_kses_post($loop_title).'</'.theplus_validate_html_tag($loop_title_tag).'></a>';						
						}else{
							$list_title = '<'.theplus_validate_html_tag($loop_title_tag).' class="service-title">'.wp_kses_post($loop_title).'</'.theplus_validate_html_tag($loop_title_tag).'>';
						}							
					}

					$loop_content_desc = $item['loop_content_desc'];
					if(!empty($loop_content_desc)){
							$description='<div class="service-desc"> '.wp_kses_post($loop_content_desc).' </div>';
					}

					//Icon style
					if(!empty($item['loop_image_icon'])){

						$loop_svg_d_icon= $item['loop_svg_d_icon'];
						$loop_max_width_size=$loop_max_width_unit='';
						$loop_max_width_size = (!empty($item['loop_max_width']["size"])) ? $item['loop_max_width']["size"] : 100;
						$loop_max_width_unit = (!empty($item['loop_max_width']["unit"])) ? $item['loop_max_width']["unit"] : "px";

						$loop_max_width = $loop_max_width_size.$loop_max_width_unit;							

						//Icon Image
						if(isset($item['loop_image_icon']) && $item['loop_image_icon'] == 'image'){
							$loop_imgSrc='';
							if(!empty($item["loop_select_image"]["url"])){
								$image_id = $item["loop_select_image"]["id"];
								$loop_imgSrc= tp_get_image_rander( $image_id,$item['loop_select_image_thumbnail_size']);
							}
							$list_img ='<div class="ts-icon-img icon-img-b " >';
								$list_img .=$loop_imgSrc;
							$list_img .='</div>';
						}else if(isset($item['loop_image_icon']) && $item['loop_image_icon'] == 'lottie'){
							$ext = pathinfo($item['lottieUrl']['url'], PATHINFO_EXTENSION);			
							if($ext!='json'){
								$list_img = '<h3 class="theplus-posts-not-found">'.esc_html__("Opps!! Please Enter Only JSON File Extension.",'theplus').'</h3>';
							}else{
								$lottieWidth = isset($settings['lottieWidth']['size']) ? $settings['lottieWidth']['size'] : 50;
								$lottieHeight = isset($settings['lottieHeight']['size']) ? $settings['lottieHeight']['size'] : 50;
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

								$list_img='<lottie-player src="'.esc_url($item['lottieUrl']['url']).'" style="width: '.esc_attr($lottieWidth).'px; height: '.esc_attr($lottieHeight).'px;" '.esc_attr($lottieLoopValue).'  speed="'.esc_attr($lottieSpeed).'" '.esc_attr($lottieAnim).'></lottie-player>';
							}									
						}else if(isset($item['loop_image_icon']) && $item['loop_image_icon'] == 'icon'){
							$icons='';
							if(!empty($item["loop_icon_style"]) && $item["loop_icon_style"]=='font_awesome'){
								$icons=$item["loop_icon_fontawesome"];
							}else if(!empty($item["loop_icon_style"]) && $item["loop_icon_style"]=='icon_mind'){
								$icons=$item["loop_icons_mind"];
							}else if(!empty($item["loop_icon_style"]) && $item["loop_icon_style"]=='font_awesome_5'){
								ob_start();
									\Elementor\Icons_Manager::render_icon( $item['loop_icon_fontawesome_5'], [ 'aria-hidden' => 'true' ]);
									$icons = ob_get_contents();
								ob_end_clean();
							}

							if(!empty($item["loop_icon_style"]) && $item["loop_icon_style"]=='font_awesome_5'){
								$list_img = '<span class=" service-icon '.esc_attr($icon_shine).' '.esc_attr($service_icon_style).'" >'.$icons.'</span>';
							}else{
								$list_img = '<i class=" '.esc_attr($icons).' service-icon '.esc_attr($icon_shine).' '.esc_attr($service_icon_style).'" ></i>';
							}


						}else if(isset($item['loop_image_icon']) && $item['loop_image_icon'] == 'svg'){
							$loop_svg_url='';
							if($item['loop_svg_icon']== 'img'){						
								if(!empty($item['loop_svg_image']["url"])){
									$loop_svg_url= $item['loop_svg_image']["url"];
								}
							}else{
								$loop_svg_url = THEPLUS_URL.'assets/images/svg/'.esc_attr($loop_svg_d_icon); 
							}

							$rand_no=rand(1000000, 1500000);

							$list_img ='<div class="pt_plus_animated_svg svg-'.esc_attr($rand_no).' " data-id="svg-'.esc_attr($rand_no).'" data-type="'.esc_attr($settings["svg_type"]).'" data-duration="'.esc_attr($duration).'" data-stroke="'.esc_attr($border_stroke_color).'" data-fill_color="'.esc_attr($svg_fill_color).'" data-fillhover="'.esc_attr($svg_fill_color_hover).'" data-strokehover="'.esc_attr($border_stroke_color_hover).'">';
								$list_img .='<div class="info_box_svg svg_inner_block '.esc_attr($service_icon_style).'">';
									//@since 4.1.9
									$svg_loop_url_pass='';
									$ext = pathinfo($loop_svg_url, PATHINFO_EXTENSION);
									if(!empty($loop_svg_url) && $ext=='svg' ){						
										$svg_loop_url_pass = $loop_svg_url;
									}
									$list_img .='<object id="svg-'.esc_attr($rand_no).'" type="image/svg+xml" data="'.esc_url($svg_loop_url_pass).'"  style="max-width:'.esc_attr($loop_max_width).';max-height:'.esc_attr($loop_max_width).';margin:0 auto;"></object>';
								$list_img .='</div>';
							$list_img .='</div>';

						}	
					}

					$loop_button='';
					if($settings['loop_display_button'] == 'yes'){
						$link_key = 'link_' . $index;

						if ( ! empty( $item['loop_button_link']['url'] ) ) {
							$this->add_link_attributes( $link_key, $item['loop_button_link']);							
						}

						$this->add_render_attribute( $link_key, 'class', 'button-link-wrap' );
						$this->add_render_attribute( $link_key, 'role', 'button' );
 
						$button_style = $settings['loop_button_style'];
						$button_text = $item['loop_button_text'];
						$btn_uid = uniqid('btn');
						$data_class = $btn_uid;
						$data_class .=' button-'.$button_style.' ';
 
						if( $button_style == 'style-7' ){
							$button_text = wp_kses_post($button_text).'<span class="btn-arrow"></span>';
						}

						if( $button_style=='style-8' ){
							$button_text = wp_kses_post($button_text);
						}

						if( $button_style == 'style-9' ){
							$button_text = wp_kses_post($button_text).'<span class="btn-arrow"><i class="fa-show fa fa-chevron-right" aria-hidden="true"></i><i class="fa-hide fa fa-chevron-right" aria-hidden="true"></i></span>';
						}
 
						$loop_button ='<div class="pt-plus-button-wrapper">';
							$loop_button .='<div class="button_parallax">';
								$loop_button .='<div class="ts-button">';
									$loop_button .='<div class="pt_plus_button '.esc_attr($data_class).'">';
										$loop_button .= '<div class="animted-content-inner">';
											if(!empty($item['r_full_infobox_switch']) && $item['r_full_infobox_switch']=='yes'){
												$loop_button .='<div class="button-link-wrap">';
											}else{
												$loop_button .='<a '.$this->get_render_attribute_string( $link_key ).'>';
											}											
											$loop_button .= $button_text;
											if(!empty($item['r_full_infobox_switch']) && $item['r_full_infobox_switch']=='yes'){
												$loop_button .='</div>';
											}else{
												$loop_button .='</a>';												
											}											
										$loop_button .='</div>';
									$loop_button .='</div>';
								$loop_button .='</div>';
							$loop_button .='</div>';
						$loop_button .='</div>';
					}

					$FullInfoboxLinkr = !empty($item['r_full_infobox_link']['is_external']) ? '_blank' : '';
					
					if(( !empty($item['r_full_infobox_switch']) && $item['r_full_infobox_switch'] == 'yes' ) && !empty($item['r_full_infobox_link']) ){
						$output .= '<div class="info-box-inner elementor-repeater-item-' . esc_attr($item['_id']) . ' '.esc_attr($on_load_class).' '.esc_attr($fc_load_class).'"><a href="'.esc_url($item['r_full_infobox_link']['url']).'" target="'.esc_attr($FullInfoboxLinkr).'">';
					}else{
						$output .= '<div class="info-box-inner elementor-repeater-item-' . esc_attr($item['_id']) . ' '.esc_attr($on_load_class).' '.esc_attr($fc_load_class).'">';
					}
 
					if( $main_style == 'style_1' ){
						$output .= '<div class="info-box-bg-box '.esc_attr($serice_box_border).' content_hover_effect '. esc_attr($hover_class) .'">';

							$output .= '<div class="service-media text-left '.esc_attr($service_center).' ">';	
							if(!empty($list_img)){				
								$output .= '<div class="m-r-16 '.esc_attr($serice_img_border).'" '.$border_right_css.'> '.$list_img.' </div>';
							}
								$output .= '<div class="service-content ">';
									$output .= $list_title;
									$output .= $service_border;
									$output .= $description;
									$output .= $loop_button;
								$output .= '</div>';
							$output .= '</div>';
							$output .= '<div class="infobox-overlay-color"></div>';
						$output .= '</div>';					
					}

					if( $main_style == 'style_2' ){
						$output .= '<div class="info-box-bg-box '.esc_attr($serice_box_border).' content_hover_effect '. esc_attr($hover_class) .'">';

							$output .= '<div class="service-media text-right '.esc_attr($service_center).' ">';
								$output .= '<div class="service-content">';
									$output .= $list_title;
									$output .= $service_border;
									$output .= $description;
									$output .= $loop_button;
								$output .= '</div>';			
								if($list_img != ''){					
								$output .=  '<div class="m-l-16 serice_img_border" '.$border_right_css.'>'.$list_img.'</div>';
								}
							$output .= '</div>';
							$output .= '<div class="infobox-overlay-color"></div>';
						$output .= '</div>';
					}

					if( $main_style == 'style_3' ){
						$output .= '<div class="info-box-bg-box '.esc_attr($serice_box_border).' content_hover_effect '. esc_attr($hover_class) .' '.esc_attr($inner_js_tilt).'" '.$this->get_render_attribute_string( 'tilt_parallax' ).'>';
							$output .= '<div class="'.esc_attr($service_align).'">';
								$output .= '<div class="service-center  ">';
									$output .= $list_img;
									$output .= $list_title;
									$output .= $service_border;
									$output .= $description;
									$output .= $loop_button;
								$output .= '</div>';				
							$output .= '</div>';
							$output .= '<div class="infobox-overlay-color"></div>';
						$output .= '</div>';
					}

					if( $main_style == 'style_4' ){
						$output .= '<div class="info-box-bg-box content_hover_effect '. esc_attr($hover_class) .' '.esc_attr($serice_box_border).'" >';
							$output .= '<div class="">';
								$output .= '<div class="service-media service-left '.esc_attr($service_center).'">';
									$output .= $list_img;
									$output .= '<div class="service-content">';
										$output .= $list_title;
									$output .= '</div>';								
								$output .= '</div>';	
									$output .= $service_border;
									$output .= $description;
									$output .= $loop_button;
							$output .= '</div>';
							$output .= '<div class="infobox-overlay-color"></div>';
						$output .= '</div>';
					}

					if( $main_style == 'style_7' ){
						$output .= '<div class="info-box-bg-box">';
							$output .= '<div class="service-media text-left '.esc_attr($service_center).' ">';	
							if(!empty($list_img)){	
								$output .= '<div class="m-r-16 service-bg-7 '.esc_attr($serice_img_border).'" '.$border_right_css.'> '.$list_img.' </div>';
							}
								$output .= '<div class="service-content ">';
									$output .= $list_title;
									$output .= $service_border;
									$output .= $description;
									$output .= $the_button;
								$output .= '</div>';						
							$output .= '</div>';
							$output .= '<div class="infobox-overlay-color"></div>';
						$output .= '</div>';
					}

					if( $main_style == 'style_11' ){
						$output .= '<div class="info-box-bg-box content_hover_effect '. esc_attr($hover_class) .'">';
							$output .= '<div class="info-box style-11 text-center">';	
								$output .= '<div class="info-box-all">';
									$output .= '<div class="info-box-wrapper ">';
										$output .= '<div class="info-box-conetnt">';
											$output .= '<div class="info-box-icon-img">';
												$output .= $list_img;
											$output .= '</div>';	
											$output .= $list_title;	
											$output .= '<div class="info-box-title-hide">'.wp_kses_post($item["loop_title"]).' </div>';	
											$output .= $service_border;
											$output .= $description;
											$output .= $loop_button;
										$output .= '</div>';
									$output .= '</div>';
								$output .= '</div>';
								$output .= '</div>';								
							$output .= '<div class="infobox-overlay-color"></div>';
						$output .= '</div>';	
					}

					if( (!empty($item['r_full_infobox_switch']) && $item['r_full_infobox_switch']=='yes') && !empty($item['r_full_infobox_link']) ){
						$output .= '</a></div>';
					}else{
						$output .= '</div>';
					}

					$index++;
				}
			}
		}

		if ( $info_box_layout == 'single_layout' ){
			if( (!empty($full_infobox_switch) && $full_infobox_switch=='yes') && !empty($full_infobox_link)){				
				$output = '<a href="'.esc_url($full_infobox_link).'" target="'.esc_attr($fullInfoboxBlank).'"> <div class="info-box-inner content_hover_effect '. esc_attr($hover_class) .' '.esc_attr($fc_load_class).'"  ' . $hover_attr . ' >';
			}else{
				$output = '<div class="info-box-inner content_hover_effect '. esc_attr($hover_class) .' '.esc_attr($fc_load_class).'"  ' . $hover_attr . ' >';	
			}

			$lazy_bg = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['box_background_image'],$settings['box_hover_background_image']) : '';
			$lazy_ol_bg = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['box_hover_background_image']) : '';
 
			if( $main_style == 'style_1' ){
				$icon_overlay_style='';
				if(!empty($settings['icon_overlay']) && $settings['icon_overlay']=='yes'){
					$icon_overlay_style = 'icon-overlay';
				}
				
				$output .= '<div class="info-box-bg-box '.esc_attr($lazy_bg).' '.esc_attr($icon_overlay_style).' '.esc_attr($serice_box_border).'">';
					$output .= '<div class="service-media text-left '.esc_attr($service_center).' ">';	

						if( !empty($service_img) ){
							$output .= '<div class="m-r-16  '.esc_attr($serice_img_border).'" '.$border_right_css.'> '.$service_img.' </div>';
						}

						$output .= '<div class="service-content ">';
							$output .= $service_title;
							$output .= $service_border;
							$output .= $description;
							$output .= $the_button;
						$output .= '</div>';

					$output .= '</div>';
					$output .= '<div class="infobox-overlay-color '.esc_attr($lazy_ol_bg).'"></div>';
				$output .= '</div>';	
			}

			if( $main_style == 'style_2' ){
				$icon_overlay_style='';
				if(!empty($settings['icon_overlay']) && $settings['icon_overlay']=='yes'){
					$icon_overlay_style = 'icon-overlay';
				}
				$output .= '<div class="info-box-bg-box '.esc_attr($lazy_bg).' '.esc_attr($icon_overlay_style).' '.esc_attr($serice_box_border).'">';
					$output .= '<div class="service-media text-right '.esc_attr($service_center).' ">';
						$output .= '<div class="service-content">';
							$output .= $service_title;
							$output .= $service_border;
							$output .= $description;
							$output .= $the_button;
						$output .= '</div>';			
					if(!empty($service_img)){				
						$output .=  '<div class="m-l-16 '.esc_attr($serice_img_border).' " '.$border_right_css.'>'.$service_img.'</div>';
					}
					$output .= '</div>';
					$output .= '<div class="infobox-overlay-color '.esc_attr($lazy_ol_bg).'"></div>';
				$output .= '</div>';
			}

			if( $main_style == 'style_3' ){
				$pin_text=$square_pin='';
				if(!empty($settings["display_pin_text"]) && $settings["display_pin_text"]=='yes'){
					if(!empty($settings["square_pin"]) && $settings["square_pin"]=='yes'){
						$square_pin = 'square-pin';
					}
					$pin_text='<div class="info-pin-text '.esc_attr($square_pin).'">'.wp_kses_post($settings["pin_text_title"]).'</div>';
				}

				$icon_overlay_style='';
				if(!empty($settings['icon_overlay']) && $settings['icon_overlay']=='yes'){
					$icon_overlay_style = 'icon-overlay';
				}

				$output .= '<div class="info-box-bg-box '.esc_attr($lazy_bg).' '.esc_attr($icon_overlay_style).' '.esc_attr($serice_box_border).'  '.esc_attr($inner_js_tilt).'" '.$this->get_render_attribute_string( 'tilt_parallax' ).'>';
					$output .= '<div class="'.esc_attr($service_align).'">';
						$output .= '<div class="service-center  ">';
							$output .= '<div class="info-icon-content">'.$pin_text.$service_img.'</div>';
							$output .= $service_title;
							$output .= $service_border;
							$output .= $description;
							$output .= $the_button;
							$output .= '</div>';				
					$output .= '</div>';
					$output .= '<div class="infobox-overlay-color '.esc_attr($lazy_ol_bg).'"></div>';
				$output .= '</div>';
			}

			if( $main_style == 'style_4' ){
				$output .= '<div class="info-box-bg-box '.esc_attr($lazy_bg).' '.esc_attr($serice_box_border).'">';
					$output .= '<div class="">';
						$output .= '<div class="service-media service-left '.esc_attr($service_center).'">';
							$output .= $service_img;
							$output .= '<div class="service-content">';
								$output .= $service_title;
							$output .= '</div>';
						$output .= '</div>';	
							$output .= $service_border;
							$output .= $description;
							$output .= $the_button;
					$output .= '</div>';
					$output .= '<div class="infobox-overlay-color '.esc_attr($lazy_ol_bg).'"></div>';
				$output .= '</div>';
			}

			if( $main_style == 'style_7' ){
				$output .= '<div class="info-box-bg-box '.esc_attr($lazy_bg).'">';
					$output .= '<div class="service-media text-left '.esc_attr($service_center).' ">';	
						if(!empty($service_img)){	
							$output .= '<div class="m-r-16 service-bg-7 '.esc_attr($serice_img_border).'" '.$border_right_css.'> '.$service_img.' </div>';
						}

						$output .= '<div class="service-content ">';
							$output .= $service_title;
							$output .= $service_border;
							$output .= $description;
							$output .= $the_button;
						$output .= '</div>';						
					$output .= '</div>';
					$output .= '<div class="infobox-overlay-color '.esc_attr($lazy_ol_bg).'"></div>';
				$output .= '</div>';
			}

			if( $main_style == 'style_11' ){
				$output .= '<div class="info-box-bg-box '.esc_attr($lazy_bg).'">';	
					$output .= '<div class="info-box style-11 text-center">';	
						$output .= '<div class="info-box-all">';
							$output .= '<div class="info-box-wrapper ">';
								$output .= '<div class="info-box-conetnt">';
									$output .= '<div class="info-box-icon-img">';
										$output .= $service_img;
									$output .= '</div>';	
									$output .= $service_title;	
									$output .= '<div class="info-box-title-hide"> '.wp_kses_post($settings["title"]).' </div>';	
									$output .= $service_border;
									$output .= $description;
									$output .= $the_button;
								$output .= '</div>';
							$output .= '</div>';
						$output .= '</div>';
					$output .= '</div>';
					$output .= '<div class="infobox-overlay-color '.esc_attr($lazy_ol_bg).'"></div>';
				$output .= '</div>';	
			}

			$output .= '</div>';

			if( (!empty($full_infobox_switch) && $full_infobox_switch == 'yes') && !empty($full_infobox_link) ){
				$output .= '</a>';
			}
		}
 
		$visiblity_hide='';
			if(!empty($settings['responsive_visible_opt']) && $settings['responsive_visible_opt']=='yes'){
				$visiblity_hide .= (($settings['desktop_opt']!='yes' && $settings['desktop_opt']=='') ? 'desktop-hide ' : '' );							
				$visiblity_hide .= (($settings['tablet_opt']!='yes' && $settings['tablet_opt']=='') ? 'tablet-hide ' : '' );
				$visiblity_hide .= (($settings['mobile_opt']!='yes' && $settings['mobile_opt']=='') ? 'mobile-hide ' : '' );
			}
 
		$uid=uniqid('info_box');
		$carousel_bg = '';
		if(!empty($settings["carousel_unique_id"])){
			$uid="tpca_".$settings["carousel_unique_id"];
			$carousel_bg = ' data-carousel-bg-conn="bgcarousel'.esc_attr($settings["carousel_unique_id"]).'"';
		}
 
		//active infobox options and connect carousel
		$connect_carousel =$connection_hover_click='';
		if(!empty($settings["connection_unique_id"])){
			$connect_carousel='tpca_'.$settings["connection_unique_id"];
			$uid="tptab_".$settings["connection_unique_id"];
			$connection_hover_click=$settings["connection_hover_click"];
		}	
 
		$info_box ='<div id="'.esc_attr($uid).'" class="pt_plus_info_box '.esc_attr($settings['bg_hover_animation']).' '.esc_attr($isotope).' '.esc_attr($arrow_class).' '.esc_attr($data_carousel).' '.esc_attr($uid).' info-box-'.esc_attr($main_style).' '.esc_attr($animated_class).'  '.esc_attr($service_space).' '.esc_attr($tilt_hover_class).'"  data-id="'.esc_attr($uid).'" '.$animation_attr.' '.$data_slider.' '.$visiblity_hide.' data-connection="'.esc_attr($connect_carousel).'" data-eventtype="'.esc_attr($connection_hover_click).'" '.$carousel_bg.'>';
			$info_box .= '<div class="post-inner-loop ">';
				$info_box .= $output;
			$info_box .='</div>';
		$info_box .='</div>';
		
		echo $before_content.$info_box.$after_content;
	}
    
	protected function render_text() {	
		$icons_after=$icons_before='';
		$settings = $this->get_settings_for_display();
 
		$button_style = $settings['button_style'];
		$before_after = $settings['before_after'];
		$button_text = $settings['button_text'];
 
		if($settings["button_icon_style"] == 'font_awesome'){
			$icons=$settings["button_icon"];
		}else if($settings["button_icon_style"] == 'icon_mind'){
			$icons=$settings["button_icons_mind"];
		}else if($settings["button_icon_style"] == 'font_awesome_5'){
			ob_start();
				\Elementor\Icons_Manager::render_icon( $settings['button_icon_5'], [ 'aria-hidden' => 'true' ]);
				$icons = ob_get_contents();
			ob_end_clean();
		}else{
			$icons='';
		}

		if( $before_after == 'before' && !empty($icons) ){
			if(!empty($settings["button_icon_style"]) && $settings["button_icon_style"]=='font_awesome_5'){
				$icons_before = '<span class="btn-icon button-before">'.$icons.'</span>';
			}else{
				$icons_before = '<i class="btn-icon button-before '.esc_attr($icons).'"></i>';
			}			
		}

		if( $before_after == 'after' && !empty($icons) ){
			if(!empty($settings["button_icon_style"]) && $settings["button_icon_style"]=='font_awesome_5'){
				 $icons_after = '<span class="btn-icon button-after">'.$icons.'</span>';
			}else{
				 $icons_after = '<i class="btn-icon button-after '.esc_attr($icons).'"></i>';
			}
 
		}
 
		if( $button_style == 'style-8' ){
			$button_text = $icons_before . $button_text . $icons_after;
		}
 
		if( $button_style == 'style-7' ){
			$button_text =$button_text.'<span class="btn-arrow"></span>';
		}

		if( $button_style == 'style-9' ){
			$button_text =$button_text.'<span class="btn-arrow"><i class="fa-show fa fa-chevron-right" aria-hidden="true"></i><i class="fa-hide fa fa-chevron-right" aria-hidden="true"></i></span>';
		}
		return $button_text;
	}

	protected function content_template() {
 
    }
}