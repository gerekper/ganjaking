<?php 
/*
Widget Name: Advanced Text Block 
Description: Content of text text block.
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
use Elementor\Group_Control_Image_Size;

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_TimeLine extends Widget_Base {
		
	public function get_name() {
		return 'tp-timeline';
	}

    public function get_title() {
        return esc_html__('Timeline', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-ellipsis-v theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-creatives');
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
			'style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => theplus_get_style_list(2),
			]
		);
		$this->add_control(
			'timeline_inline_masonry',
			[
				'label' => esc_html__( 'Masonry Layout', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'timeline_content_align',
			[
				'label' => esc_html__( 'Content Alignment', 'theplus' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
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
				'toggle' => false,
			]
		);
		$repeater = new \Elementor\Repeater();
		
		$repeater->add_control(
			'loop_pin_select_icon',
			[
				'label' => esc_html__( 'Select Icon', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'description' => esc_html__('You can select Icon or Custom Image using this option.', 'theplus' ),
				'default' => 'icon',
				'options' => [
					''  => esc_html__( 'None', 'theplus' ),
					'icon' => esc_html__( 'Icon', 'theplus' ),
					'image' => esc_html__( 'Image', 'theplus' ),					
				],
			]
			);
		$repeater->add_control(
			'pin_icon_style',
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
					'loop_pin_select_icon' => 'icon',
				],
			]
		);
		$repeater->add_control(
			'pin_icon_fontawesome',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICON,
				'default' => 'fa fa-download',
				'condition' => [
					'loop_pin_select_icon' => 'icon',
					'pin_icon_style' => 'font_awesome',
				],
			]
		);
		$repeater->add_control(
			'pin_icon_fontawesome_5',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-download',
					'library' => 'solid',
				],
				'separator' => 'before',
				'condition' => [
					'loop_pin_select_icon' => 'icon',
					'pin_icon_style' => 'font_awesome_5',
				],	
			]
		);
		$repeater->add_control(
			'pin_icons_mind',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'default' => 'iconsmind-Download-2',
				'label_block' => true,
				'options' => theplus_icons_mind(),
				'condition' => [
					'loop_pin_select_icon' => 'icon',
					'pin_icon_style' => 'icon_mind',
				],
			]
		);
		$repeater->add_control(
			'loop_pin_image',
			[
				'label' => esc_html__( 'Use Image As icon', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'dynamic' => ['active'   => true,],
				'media_type' => 'image',
				'condition' => [
					'loop_pin_select_icon' => 'image',
				],
			]
		);
		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'loop_pin_image_thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'before',
				'condition' => [
					'loop_pin_select_icon' => 'image',
				],
			]
		);
		$repeater->add_control(
			'pin_title',
			[
				'label' => esc_html__( 'Pin Title', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( '26-06-2018' , 'theplus' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);		
		$repeater->add_control(
			'pin_title_position',
			[
				'label' => esc_html__( 'Pin Position', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'top',
				'options' => [
					'left'  => esc_html__( 'Left', 'theplus' ),
					'right' => esc_html__( 'Right', 'theplus' ),
					'top' => esc_html__( 'Top', 'theplus' ),
					'bottom' => esc_html__( 'Bottom', 'theplus' ),
				],
				'condition'    => [
					'pin_title!' => '',
				],
			]
		);
		$repeater->start_controls_tabs( 'loop_tabs_content' );
		$repeater->start_controls_tab(
			'tab_loop_content',
			[
				'label' => esc_html__( 'Content', 'theplus' ),
			]
		);
		$repeater->add_control(
			'loop_content_title',
			[
				'label' => esc_html__( 'Title', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'ThePlus Addons' , 'theplus' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$repeater->add_control(
			'loop_content_desc',
			[
				'label' => esc_html__( 'Description', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet.', 'theplus' ),
				'dynamic' => ['active'   => true,],
			]
		);
		$repeater->add_control(
			'loop_content_options',
			[
				'label' => esc_html__( 'Content Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => [
					'image'  => esc_html__( 'Image', 'theplus' ),
					'iframe' => esc_html__( 'Iframe/HTML', 'theplus' ),
					'template' => esc_html__( 'Template', 'theplus' ),
				],
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'loop_featured_image',
			[
				'label' => esc_html__( 'Featured Image', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => ['active'   => true,],
				'default' => [
					'url' => '',
				],
				'condition' => [
					'loop_content_options' => 'image',
				],
			]
		);
		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'loop_featured_image_thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'before',
				'condition' => [
					'loop_content_options' => 'image',
				],
			]
		);
		$repeater->add_control(
			'loop_featured_iframe',
			[
				'label' => esc_html__( 'Custom HTML/Iframe Code', 'theplus' ),
				'type' => Controls_Manager::CODE,
				'language' => 'html',
				'rows' => 10,
				'condition' => [
					'loop_content_options' => 'iframe',
				],
			]
		);
		$repeater->add_control(
			'loop_content_template',
			[
				'label'       => esc_html__( 'Elementor Templates', 'theplus' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '0',
				'options'     => theplus_get_templates(),
				'label_block' => 'true',
				'condition' => [
					'loop_content_options' => 'template',
				],
			]
		);
		$repeater->add_control(
			'loop_link',
			[
				'label' => esc_html__( 'Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'theplus' ),
				'show_external' => true,
				'dynamic' => ['active'   => true,],
				'default' => [
					'url' => '',
				],
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'loop_display_button',
			[
				'label' => esc_html__( 'Display Button', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'loop_button_text',
			[
				'label' => esc_html__( 'Button Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => ['active'   => true,],
				'condition' => [
					'loop_display_button' => 'yes',
				],
			]
		);
		$repeater->end_controls_tab();
		$repeater->start_controls_tab(
			'tab_loop_advance',
			[
				'label' => esc_html__( 'Advance', 'theplus' ),
			]
		);
		$repeater->add_control(
			'loop_content_alignment',
			[
				'label' => esc_html__( 'Content Alignment', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'text-left',
				'options' => [
					'text-left'  => esc_html__( 'Left', 'theplus' ),
					'text-center' => esc_html__( 'Center', 'theplus' ),
					'text-right' => esc_html__( 'Right', 'theplus' ),
				],
			]
		);
		$repeater->add_control(
			'loop_alignment_section',
			[
				'label' => esc_html__( 'Section Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
				'toggle' => false,
				'label_block' => false,
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'loop_animation_effects',
			[
				'label'   => esc_html__( 'In Animation Effect', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'no-animation',
				'options' => theplus_get_animation_options(),
				'separator' => 'before',
			]
		);
		$repeater->add_control(
            'loop_animation_delay',
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
					'loop_animation_effects!' => 'no-animation',
				],
            ]
        );
		$repeater->add_responsive_control(
            'loop_top_offset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Top Offset Space', 'theplus'),
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list {{CURRENT_ITEM}}.timeline-item-wrap' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
            ]
        );
		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();
		$this->add_control(
			'content_loop_section',
			[
				'label' => '',
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'loop_content_title' => esc_html__( 'ThePlus Addons', 'theplus' ),
						'tab_content' => esc_html__( 'I am item content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'theplus' ),
						'pin_title_position' => 'right',
						'loop_alignment_section' => 'left',
						'loop_content_alignment' => 'text-right',
					],
					[
						'loop_content_title' => esc_html__( 'ThePlus Addons', 'theplus' ),
						'tab_content' => esc_html__( 'I am item content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'theplus' ),
						'pin_title_position' => 'left',
						'loop_alignment_section' => 'right',
						'loop_content_alignment' => 'text-left',
					],
				],
				'title_field' => '{{{ loop_content_title }}}',
			]
		);
		
		$this->end_controls_section();
		
		$this->start_controls_section(
			'content_timeline_start_end_section',
			[
				'label' => esc_html__( 'Start / End Pin', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'pin_center_style',
			[
				'label' => esc_html__( 'Pin Center Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => theplus_get_style_list(2),
			]
		);
		$this->add_control(
			'pin_start_end_options',
			[
				'label' => esc_html__( 'Pin Start/End Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'tabs_pin_start_end' );
		$this->start_controls_tab(
			'tab_pin_start',
			[
				'label' => esc_html__( 'Start Pin', 'theplus' ),
			]
		);
		$this->add_control(
			'start_pin_select_icon',
			[
				'label' => esc_html__( 'Select Icon', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'description' => esc_html__('You can select Icon or Custom Image using this option.', 'theplus' ),
				'default' => 'icon',
				'options' => [
					''  => esc_html__( 'None', 'theplus' ),
					'icon' => esc_html__( 'Icon', 'theplus' ),
					'image' => esc_html__( 'Image', 'theplus' ),
					'text' => esc_html__( 'Text', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'start_pin_icon_style',
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
					'start_pin_select_icon' => 'icon',
				],
			]
		);
		$this->add_control(
			'start_pin_icon_fontawesome',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICON,
				'default' => 'fa fa-angle-double-down',
				'condition' => [
					'start_pin_select_icon' => 'icon',
					'start_pin_icon_style' => 'font_awesome',
				],
			]
		);
		$this->add_control(
			'start_pin_icon_fontawesome_5',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-angle-double-down',
					'library' => 'solid',
				],
				'condition' => [
					'start_pin_select_icon' => 'icon',
					'start_pin_icon_style' => 'font_awesome_5',
				],	
			]
		);
		$this->add_control(
			'start_pin_icons_mind',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'default' => 'iconsmind-Download-2',
				'label_block' => true,
				'options' => theplus_icons_mind(),
				'condition' => [
					'start_pin_select_icon' => 'icon',
					'start_pin_icon_style' => 'icon_mind',
				],
			]
		);
		$this->add_control(
			'start_pin_image',
			[
				'label' => esc_html__( 'Use Image As icon', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'media_type' => 'image',
				'dynamic' => ['active'   => true,],
				'condition' => [
					'start_pin_select_icon' => 'image',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'start_pin_image_thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'before',
				'condition' => [
					'start_pin_select_icon' => 'image',
				],
			]
		);
		$this->add_control(
			'start_pin_title',
			[
				'label' => esc_html__( 'Start Pin Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'START' , 'theplus' ),
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'start_pin_select_icon' => 'text',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_pin_end',
			[
				'label' => esc_html__( 'End Pin', 'theplus' ),
			]
		);
		$this->add_control(
			'end_pin_select_icon',
			[
				'label' => esc_html__( 'Select Icon', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'description' => esc_html__('You can select Icon or Custom Image using this option.', 'theplus' ),
				'default' => 'icon',
				'options' => [
					''  => esc_html__( 'None', 'theplus' ),
					'icon' => esc_html__( 'Icon', 'theplus' ),
					'image' => esc_html__( 'Image', 'theplus' ),
					'text' => esc_html__( 'Text', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'end_pin_icon_style',
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
					'end_pin_select_icon' => 'icon',
				],
			]
		);
		$this->add_control(
			'end_pin_icon_fontawesome',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICON,
				'default' => 'fa fa-stop-circle',
				'condition' => [
					'end_pin_select_icon' => 'icon',
					'end_pin_icon_style' => 'font_awesome',
				],
			]
		);
		$this->add_control(
			'end_pin_icon_fontawesome_5',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-stop-circle',
					'library' => 'solid',
				],
				'condition' => [
					'end_pin_select_icon' => 'icon',
					'end_pin_icon_style' => 'font_awesome_5',
				],
			]
		);
		$this->add_control(
			'end_pin_icons_mind',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'default' => 'iconsmind-Download-2',
				'label_block' => true,
				'options' => theplus_icons_mind(),
				'condition' => [
					'end_pin_select_icon' => 'icon',
					'end_pin_icon_style' => 'icon_mind',
				],
			]
		);
		$this->add_control(
			'end_pin_image',
			[
				'label' => esc_html__( 'Use Image As icon', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'media_type' => 'image',
				'dynamic' => ['active'   => true,],
				'condition' => [
					'end_pin_select_icon' => 'image',
				],
			]
		);		
		$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				[
					'name' => 'end_pin_image_thumbnail',
					'default' => 'full',
					'separator' => 'none',
					'separator' => 'before',
					'condition' => [
						'end_pin_select_icon' => 'image',
					],
				]
			);
		$this->add_control(
			'end_pin_title',
			[
				'label' => esc_html__( 'End Pin Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'END' , 'theplus' ),
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'end_pin_select_icon' => 'text',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();		
		$this->end_controls_section();
		/*Pin Start/End Option*/
		/*Pin Title/Icon Style*/
		$this->start_controls_section(
			'loop_pin_style_section',
			[
				'label' => esc_html__( 'Loop Pin Style', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'pin_title_heading_style',
			[
				'label' => esc_html__( 'Pin Title Style', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'pin_title_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap .timeline-text-tooltip',
			]
		);
		$this->start_controls_tabs( 'tabs_pin_title_style' );
		$this->start_controls_tab(
			'tab_pin_title_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'pin_title_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap .timeline-text-tooltip' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'pin_title_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#dddddd',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap .timeline-text-tooltip' => 'background: {{VALUE}}',
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap .timeline-text-tooltip .tooltip-arrow' => 'border-color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'pin_title_radius',
			[
				'label' => esc_html__( 'Pin Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap .timeline-text-tooltip' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_pin_title_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'pin_title_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#3351a6',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap:hover .timeline-text-tooltip' => 'color: {{VALUE}}',
				],				
			]
		);
		$this->add_control(
			'pin_title_hover_bg_color',
			[
				'label' => esc_html__( 'Hover Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#dddddd',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap:hover .timeline-text-tooltip' => 'background: {{VALUE}}',
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap:hover .timeline-text-tooltip .tooltip-arrow' => 'border-color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'pin_icon_heading_style',
			[
				'label' => esc_html__( 'Pin Icon Style', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'pin_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
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
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap .timeline-pin-icon i.point-icon-inner' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap .timeline-pin-icon svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap .timeline-pin-icon img.point-icon-inner' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'pin_icon_bg_cir_size',
			[
				'label' => esc_html__( 'Icon Background Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list.layout-both .point-icon.style-1 .timeline-tooltip-wrap' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'pin_text_center_switch',
			[
				'label' => esc_html__( 'Pin Text Center Align', 'theplus' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'theplus' ),
				'label_off' => __( 'Hide', 'theplus' ),
				'selectors' => [
					'{{WRAPPER}} .point-icon .timeline-text-tooltip' => 'top:50%;transform:translateY(-50%) !important;margin: 0 !important;',
				],
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs( 'tabs_pin_icon_style' );
		$this->start_controls_tab(
			'tab_pin_icon_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'pin_icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap .point-icon .timeline-tooltip-wrap' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap .point-icon .timeline-tooltip-wrap svg' => 'fill: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'pin_icon_bg_color',
			[
				'label' => esc_html__( 'Icon Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#e2e2e2',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap .point-icon .timeline-tooltip-wrap' => 'background: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'pin_icon_border_color',
			[
				'label' => esc_html__( 'Icon Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap .point-icon .timeline-tooltip-wrap' => 'border-color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'pin_icon_radius',
			[
				'label' => esc_html__( 'Icon Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap .point-icon .timeline-tooltip-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_pin_icon_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'pin_icon_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap:hover .point-icon .timeline-tooltip-wrap' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap:hover .point-icon .timeline-tooltip-wrap svg' => 'fill: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'pin_icon_hover_bg_color',
			[
				'label' => esc_html__( 'Hover Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#e2e2e2',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap:hover .point-icon .timeline-tooltip-wrap' => 'background: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'pin_icon_hover_border_color',
			[
				'label' => esc_html__( 'Hover Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap:hover .point-icon .timeline-tooltip-wrap' => 'border-color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Pin Title/Icon Style*/
		/*Title Style*/
		$this->start_controls_section(
			'title_style_section',
			[
				'label' => esc_html__( 'Title Style', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap .timeline-item-heading',
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
			'title_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap .timeline-item-heading' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'title_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#e5e5e5',
				'selectors' => [
					'{{WRAPPER}} .timeline-style-1 .timeline-item-wrap .timeline-item .timeline-tl-before' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .timeline-style-2 .timeline-item-wrap .border-bottom hr' => 'border-top-color: {{VALUE}}',
				],
				'condition' => [
					'style' => ['style-1','style-2']
				],
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
			'title_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#3351a6',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap:hover .timeline-item-heading' => 'color: {{VALUE}}',
				],				
			]
		);
		$this->add_control(
			'title_border_hover_color',
			[
				'label' => esc_html__( 'Border Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#e5e5e5',
				'selectors' => [
					'{{WRAPPER}} .timeline-style-1 .timeline-item-wrap:hover .timeline-item .timeline-tl-before' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .timeline-style-2 .timeline-item-wrap:hover .border-bottom hr' => 'border-top-color: {{VALUE}}',
				],
				'condition' => [
					'style' => ['style-1','style-2']
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Title Style*/
		/*Content description Style*/
		$this->start_controls_section(
			'content_desc_style_section',
			[
				'label' => esc_html__( 'Description/Content Style', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'desc_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap .timeline-item-description',
			]
		);
		$this->start_controls_tabs( 'tabs_desc_style' );
		$this->start_controls_tab(
			'tab_desc_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'desc_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#888',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap .timeline-item-description,{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap .timeline-item-description p' => 'color: {{VALUE}};-webkit-transition: all .55s;-moz-transition: all .55s;-o-transition: all .55s;-ms-transition: all .55s;transition: all .55s;',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_desc_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'desc_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#888',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap:hover .timeline-item-description,{{WRAPPER}} .pt-plus-timeline-list .timeline-item-wrap:hover .timeline-item-description p' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Content description Style*/
		/*Button Content*/
		$this->start_controls_section(
			'timeline_button_option_section',
			[
				'label' => esc_html__( 'Button Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,				
			]
		);
		$this->add_control(
			'display_button',
			[
				'label' => esc_html__( 'Display Button', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
			'button_text',
			[
				'label' => esc_html__( 'Button Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Read More', 'theplus' ),
				'condition' => [
					'display_button' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'button_padding',
			[
				'label' => esc_html__( 'Button Padding', 'theplus' ),
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
					'{{WRAPPER}} .pt_plus_button .button-link-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
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
				'condition' => [
					'display_button' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'button_top_offset',
			[
				'label' => esc_html__( 'Button Top Offset', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
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
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'display_button' => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'display_button' => 'yes',
				],
			]
		);
		$this->add_control(
			'btn_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button .button-link-wrap' => 'color: {{VALUE}};',					
				],
				'condition' => [
					'display_button' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap',
				'condition' => [
					'display_button' => 'yes',
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
				'separator' => 'before',
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'display_button' => 'yes',
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
					'display_button' => 'yes',
					'button_border_style!' => 'none',
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
				'separator' => 'after',
				'condition' => [
					'display_button' => 'yes',
					'button_border_style!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'button_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 3,
					'right'  => 3,
					'bottom' => 3,
					'left'   => 3,
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'display_button' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_shadow',
				'selector' => '{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap',
				'condition' => [
					'display_button' => 'yes',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'display_button' => 'yes',
				],
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
				'condition' => [
					'display_button' => 'yes',
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
					'display_button' => 'yes',
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
				'separator' => 'after',
				'condition' => [
					'display_button' => 'yes',
					'button_border_style!' => 'none',
				],
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
					'display_button' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_hover_shadow',
				'selector' => '{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap:hover',
				'condition' => [
					'display_button' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Button Content*/
		/*Timeline loop Content Style*/
		$this->start_controls_section(
			'loop_content_style_section',
			[
				'label' => esc_html__( 'Loop Content Background Style', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'style' => 'style-2',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_loop_content_bg_style' );
		$this->start_controls_tab(
			'tab_loop_content_bg_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'loop_content_background',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .timeline-style-2 .timeline-item-wrap .timeline-inner-block .timeline-item .timeline-item-content',
			]
		);
		$this->add_control(
			'loop_content_border',
			[
				'label' => esc_html__( 'Border', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'loop_content_border_style',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'selectors' => [
					'{{WRAPPER}} .timeline-style-2 .timeline-item-wrap .timeline-inner-block .timeline-item .timeline-item-content' => 'border-style: {{VALUE}}',
				],
				'condition' => [
					'loop_content_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'loop_content_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],
				'selectors' => [
					'{{WRAPPER}} .timeline-style-2 .timeline-item-wrap .timeline-inner-block .timeline-item .timeline-item-content' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'loop_content_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'loop_content_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .timeline-style-2 .timeline-item-wrap .timeline-inner-block .timeline-item .timeline-item-content' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'loop_content_border' => 'yes',
				],
			]
		);		
		$this->add_control(
			'loop_content_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .timeline-style-2 .timeline-item-wrap .timeline-inner-block .timeline-item .timeline-item-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'loop_content_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .timeline-style-2 .timeline-item-wrap .timeline-inner-block .timeline-item .timeline-item-content',
				
			]
		);
		$this->add_control(
			'loop_content_arrow_color',
			[
				'label' => esc_html__( 'Arrow Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .timeline-style-2 .timeline-item-wrap .timeline-tl-before' => 'border-left-color: {{VALUE}};border-right-color: {{VALUE}}',
					'{{WRAPPER}} .timeline-style-2 .timeline-item-wrap .timeline-tl-before' => 'border-left-color: {{VALUE}};border-right-color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'loop_content_arrow_style',
			[
				'label' => esc_html__( 'Arrow Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'style-2' => esc_html__( 'Style 2', 'theplus' ),
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_loop_content_hover_background',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'loop_content_hover_background',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .timeline-style-2 .timeline-item-wrap:hover .timeline-inner-block .timeline-item .timeline-item-content',
			]
		);
		$this->add_control(
			'loop_content_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .timeline-style-2 .timeline-item-wrap:hover .timeline-inner-block .timeline-item .timeline-item-content' => 'border-color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'loop_content_hover_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .timeline-style-2 .timeline-item-wrap:hover .timeline-inner-block .timeline-item .timeline-item-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'loop_content_hover_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .timeline-style-2 .timeline-item-wrap:hover .timeline-inner-block .timeline-item .timeline-item-content',
				
			]
		);
		$this->add_control(
			'loop_content_arrow_hover_color',
			[
				'label' => esc_html__( 'Arrow Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .timeline-style-2 .timeline-item-wrap:hover .timeline-tl-before' => 'border-left-color: {{VALUE}};border-right-color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Timeline loop Content Style*/
		/*Pin Start/End Style*/
		$this->start_controls_section(
			'pin_start_end_style_section',
			[
				'label' => esc_html__( 'Pin Start/End Style', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'divide_line_border_color',
			[
				'label' => esc_html__( 'Divide Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-track' => 'background: {{VALUE}}',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_pin_start_end_style' );
		$this->start_controls_tab(
			'tab_pin_start_style',
			[
				'label' => esc_html__( 'Pin Start', 'theplus' ),
			]
		);
		$this->add_control(
			'pin_start_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
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
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-beginning-icon' => 'font-size: {{SIZE}}{{UNIT}};',					
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-beginning-icon svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',					
				],
				'condition' => [
					'start_pin_select_icon' => 'icon',
				],
			]
		);
		$this->add_control(
			'pin_start_image_size',
			[
				'label' => esc_html__( 'Image Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
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
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-beginning-icon img' => 'max-width: {{SIZE}}{{UNIT}};',					
				],
				'condition' => [
					'start_pin_select_icon' => 'image',
				],
			]
		);
		$this->add_control(
			'pin_start_text_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-text.timeline-text-start' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'start_pin_select_icon' => 'text'
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'pin_start_text_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt-plus-timeline-list .timeline-text.timeline-text-start',
				'condition' => [
					'start_pin_select_icon' => 'text'
				],
			]
		);
		$this->add_control(
			'pin_start_text_icon_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-beginning-icon,{{WRAPPER}} .pt-plus-timeline-list .timeline-text.timeline-text-start' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-beginning-icon svg' => 'fill: {{VALUE}}',
				],
				'condition' => [
					'start_pin_select_icon' => ['icon','text']
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'pin_start_text_background',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .pt-plus-timeline-list .timeline-text.timeline-text-start',
				'condition' => [
					'start_pin_select_icon' => 'text'
				],
			]
		);
		$this->add_control(
			'pin_start_text_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-text.timeline-text-start' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'start_pin_select_icon' => 'text'
				],
			]
		);
		$this->add_control(
			'pin_start_text_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-text.timeline-text-start' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'start_pin_select_icon' => 'text'
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pin_start_text_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt-plus-timeline-list .timeline-text.timeline-text-start',
				'condition' => [
					'start_pin_select_icon' => 'text'
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_pin_end_style',
			[
				'label' => esc_html__( 'Pin End', 'theplus' ),
			]
		);
		$this->add_control(
			'pin_end_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
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
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-end-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-end-icon svg' => 'width: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',					
				],
				'condition' => [
					'end_pin_select_icon' => 'icon',
				],
			]
		);
		$this->add_control(
			'pin_end_image_size',
			[
				'label' => esc_html__( 'Image Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
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
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-end-icon img' => 'max-width: {{SIZE}}{{UNIT}};',					
				],
				'condition' => [
					'end_pin_select_icon' => 'image',
				],
			]
		);
		$this->add_control(
			'pin_end_text_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-text.timeline-text-end' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'end_pin_select_icon' => 'text'
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'pin_end_text_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt-plus-timeline-list .timeline-text.timeline-text-end',
				'condition' => [
					'end_pin_select_icon' => 'text'
				],
			]
		);
		$this->add_control(
			'pin_end_text_icon_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-end-icon,{{WRAPPER}} .pt-plus-timeline-list .timeline-text.timeline-text-end' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-end-icon svg' => 'fill: {{VALUE}}',
				],
				'condition' => [
					'end_pin_select_icon' => ['icon','text']
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'pin_end_text_background',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .pt-plus-timeline-list .timeline-text.timeline-text-end',
				'condition' => [
					'end_pin_select_icon' => 'text'
				],
			]
		);
		$this->add_control(
			'pin_end_text_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-text.timeline-text-end' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'end_pin_select_icon' => 'text'
				],
			]
		);
		$this->add_control(
			'pin_end_text_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-timeline-list .timeline-text.timeline-text-end' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'end_pin_select_icon' => 'text'
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pin_end_text_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt-plus-timeline-list .timeline-text.timeline-text-end',
				'condition' => [
					'end_pin_select_icon' => 'text'
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Pin Start/End Style*/
		
		/*Extra Option Style*/
		$this->start_controls_section(
			'timeline_extra_option_section',
			[
				'label' => esc_html__( 'Extra Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'style' => 'style-1'
				],
			]
		);
		$this->add_responsive_control(
			'content_gap_between',
			[
				'label' => esc_html__( 'Divider Gap Content', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
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
			]
		);
		$this->end_controls_section();
		/*Extra Option Style*/
	}	
	
	 protected function render() {
        $settings = $this->get_settings_for_display();
		$data_class=$timeline_start=$timeline_loop=$timeline_end='';
		$timeline_inline_masonry=($settings['timeline_inline_masonry']=='yes') ? 'data-masonry-type="yes" data-enable-isotope="1" data-layout-type="masonry"' : 'data-masonry-type="no"';
		
		$timeline_masonry_class=($settings['timeline_inline_masonry']=='yes') ? 'list-isotope' : '';
		$style=$settings["style"];
		$arrow_style =($style=='style-2' && !empty($settings['loop_content_arrow_style'])) ? 'arrow-'.$settings['loop_content_arrow_style'] : '';
		$uid=uniqid('timeline');
		$data_class=$uid;
		$data_class .=' timeline-'.esc_attr($style).' ';
		$ij=0;
			if(!empty($settings['content_loop_section'])){
				foreach($settings['content_loop_section'] as $item){
					
					$pin_title=$pin_title_position=$timeline_pin_icon=$content_title=$content_image=$content_desc=$section_alignment=$style_border=$title_border_bottom='';
					
					//pin position
					if(!empty($item['pin_title_position'])){
						$pin_title_position='position-'.$item['pin_title_position'];
					}else{
						$pin_title_position='position-top';
					}
					
					//pin title
					if(!empty($item['pin_title'])){
						$pin_title='<div class="timeline-text-tooltip '.esc_attr($pin_title_position).' timeline-transition" style="opacity: 1;">'.esc_html($item['pin_title']).'<div class="tooltip-arrow timeline-transition"></div></div>';
					}
					
					//pin icons
					if(!empty($item['loop_pin_select_icon'])){
						if(isset($item['loop_pin_select_icon']) && $item['loop_pin_select_icon'] == 'image'){
							$loop_imgSrc='';
							if(!empty($item["loop_pin_image"]["url"])){								
								$loop_pin_image=$item['loop_pin_image']['id'];
								$loop_imgSrc= tp_get_image_rander( $loop_pin_image,$item['loop_pin_image_thumbnail_size'], [ 'class' => 'point-icon-inner' ] );								
							}
							$list_img =$loop_imgSrc;
						}else if(isset($item['loop_pin_select_icon']) && $item['loop_pin_select_icon'] == 'icon'){		
							if(!empty($item["pin_icon_style"]) && $item["pin_icon_style"]=='font_awesome'){
								$icons=$item["pin_icon_fontawesome"];
							}else if(!empty($item["pin_icon_style"]) && $item["pin_icon_style"]=='icon_mind'){
								$icons=$item["pin_icons_mind"];
							}else{
								$icons='';
							}
							
							if(!empty($item["pin_icon_style"]) && $item["pin_icon_style"]=='font_awesome_5'){
								ob_start();
								\Elementor\Icons_Manager::render_icon( $item['pin_icon_fontawesome_5'], [ 'aria-hidden' => 'true' ]);
								$list_img = ob_get_contents();
								ob_end_clean();			
							}else{
								$list_img = '<i class=" '.esc_attr($icons).' point-icon-inner " ></i>';	
							}
													
						}
						
						$timeline_pin_icon ='<div class="timeline-pin-icon">'.$list_img.'</div>';
					}
					
					//Loop Content Title
					if(!empty($item['loop_content_title'])){
						if(!empty($item['loop_link']['url'])){
							$loop_link =$item['loop_link']['url'];
							$target = $item['loop_link']['is_external'] ? ' target="_blank"' : '';
							$nofollow = $item['loop_link']['nofollow'] ? ' rel="nofollow"' : '';
							
							$content_title='<a class="timeline-item-heading timeline-transition" href="'.esc_url( $loop_link ).'" '.$target.' '.$nofollow.'>'.esc_html($item['loop_content_title']).'</a>';
						}else{
							$content_title='<h3 class="timeline-item-heading timeline-transition">'.esc_html($item['loop_content_title']).'</h3>';
						}
					}
					
					//Loop Content Image
					if(!empty($item['loop_featured_image']['url']) && $item["loop_content_options"]=='image'){
						$image_id=$item["loop_featured_image"]["id"];
						$loop_featured_image_Src= tp_get_image_rander( $image_id,$item['loop_featured_image_thumbnail_size'], [ 'class' => 'hover__img' ] );
						
						$content_image=$loop_featured_image_Src;
					}
					if(!empty($item["loop_featured_iframe"]) && $item["loop_content_options"]=='iframe'){
						$content_image=$item["loop_featured_iframe"];
					}
					if(!empty($item['loop_content_template']) && $item["loop_content_options"]=='template'){
						$content_image= Theplus_Element_Load::elementor()->frontend->get_builder_content_for_display( $item['loop_content_template'] );
					}
					//Loop Content Description
					if(!empty($item["loop_content_desc"])){
						$content_desc='<div class="timeline-item-description timeline-transition">'.wp_kses_post($item["loop_content_desc"]).'</div>';
					}
					//section content alignment
					if(!empty($item['loop_alignment_section'])){
						$section_alignment=$item['loop_alignment_section'];							
					}
					//content text alignment
					if(!empty($item["loop_content_alignment"])){
						$align_text=$item["loop_content_alignment"];
					}
					
					//content Button
					$button='';
					if(!empty($settings["display_button"]) && $settings["display_button"]=='yes'){
						if(!empty($item['loop_display_button']) && $item['loop_display_button']=='yes'){
							$link_key = 'link_' . $ij;
							if ( ! empty( $item['loop_link']['url'] ) ) {
								$this->add_render_attribute( $link_key, 'href', $item['loop_link']['url'] );
								if ( $item['loop_link']['is_external'] ) {
									$this->add_render_attribute( $link_key, 'target', '_blank' );
								}
								if ( $item['loop_link']['nofollow'] ) {
									$this->add_render_attribute( $link_key, 'rel', 'nofollow' );
								}
							}
							
							$lz1 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['button_background_image'],$settings['button_hover_background_image']) : '';
							$this->add_render_attribute( $link_key, 'class', 'button-link-wrap '.$lz1 );
							$this->add_render_attribute( $link_key, 'role', 'button' );
							
							$button_style = 'style-8';
							$button_text = ($item['loop_button_text']!='') ? $item['loop_button_text'] : $settings["button_text"];
							$btn_uid=uniqid('btn');
							$btn_class= $btn_uid;
							$btn_class .=' button-'.$button_style.' ';
							$button .='<div class="pt_plus_button '.$btn_class.'">';										
									$button .='<a '.$this->get_render_attribute_string( $link_key ).'>';
									$button .= $button_text;
									$button .='</a>';										
							$button .='</div>';
						}
					}
					
					//Loop Animation 
					$loop_animation_effects=$item["loop_animation_effects"];
					$loop_animation_delay=  isset($item["loop_animation_delay"]["size"]) ? $item["loop_animation_delay"]["size"] : '';			
					if($loop_animation_effects=='no-animation'){
						$loop_animated_class = '';
						$loop_animation_attr = '';
					}else{
						$animate_offset = theplus_scroll_animation();
						$loop_animated_class = 'animate-general';
						$loop_animation_attr = ' data-animate-type="'.esc_attr($loop_animation_effects).'" data-animate-delay="'.esc_attr($loop_animation_delay).'"';
						$loop_animation_attr .= ' data-animate-offset="'.esc_attr($animate_offset).'"';												
					}
					
					//Timeline Style 1
					if($style=='style-1'){
						$style_border='<div class="timeline-tl-before timeline-transition"></div>';						
					}
					//Timeline Style 1
					if($style=='style-2'){
						$style_border='<div class="timeline-tl-before timeline-transition"></div>';
						$title_border_bottom='<div class="border-bottom '.esc_attr($align_text).'"><hr/></div>';
					}
					
					//Loop Content 
					$lz2 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['loop_content_background_image'],$settings['loop_content_hover_background_image']) : '';
					$timeline_loop .='<div class="grid-item timeline-item-wrap elementor-repeater-item-' . esc_attr($item['_id']) . ' timeline-'.esc_attr($section_alignment).'-content  text-pin-'.esc_attr($pin_title_position).'"  >
							<div class="timeline-inner-block timeline-transition">
								<div class="timeline-item '.esc_attr($align_text).' '.esc_attr($loop_animated_class).'" '.$loop_animation_attr.'>
									<div class="timeline-item-content timeline-transition '.esc_attr($align_text).' '.esc_attr($lz2).'">';
										$timeline_loop .=$style_border;
										if(!empty($item['loop_content_template']) && $item["loop_content_options"]=='template'){
											$timeline_loop .=$content_image;
										}else{
											$timeline_loop .=$content_title;
											$timeline_loop .=$title_border_bottom;
											$timeline_loop .='<div class="timeline-content-image">
																'.$content_image.'
																</div>';
											$timeline_loop .=$content_desc;
											$timeline_loop .=$button;											
										}
									$timeline_loop .='</div>
								</div>
								<div class="point-icon '.esc_attr($settings['pin_center_style']).'">
									<div class="timeline-tooltip-wrap">
										<div class="timeline-point-icon">
											'.$timeline_pin_icon.'
										</div>
									</div>
									'.$pin_title.'
								</div>
							</div>
					</div>';
					$ij++;
				}
				
			}
			
		//Start Pin Content	
		$timeline_start='';		
		if(!empty($settings['start_pin_select_icon'])){
			if(isset($settings['start_pin_select_icon']) && $settings['start_pin_select_icon'] == 'image'){
				$loop_imgSrc='';
				if(!empty($settings["start_pin_image"]["url"])){
					$image_id=$settings["start_pin_image"]["id"];
					$loop_imgSrc= tp_get_image_rander( $image_id,$settings['start_pin_image_thumbnail_size'],[ 'class' => '' ] );
				}
				$timeline_start ='<div class="timeline-beginning-icon">'.$loop_imgSrc.'</div>';
			}else if(isset($settings['start_pin_select_icon']) && $settings['start_pin_select_icon'] == 'icon'){
				$icons='';
				if(!empty($settings["start_pin_icon_style"]) && $settings["start_pin_icon_style"]=='font_awesome'){
					$icons=$settings["start_pin_icon_fontawesome"];
				}else if(!empty($settings["start_pin_icon_style"]) && $settings["start_pin_icon_style"]=='font_awesome_5'){				
					ob_start();
					\Elementor\Icons_Manager::render_icon( $settings['start_pin_icon_fontawesome_5'], [ 'aria-hidden' => 'true' ]);
					$icons = ob_get_contents();
					ob_end_clean();
				}else if(!empty($settings["start_pin_icon_style"]) && $settings["start_pin_icon_style"]=='icon_mind'){
					$icons=$settings["start_pin_icons_mind"];
				}
				
				if(!empty($settings["start_pin_icon_style"]) && $settings["start_pin_icon_style"]=='font_awesome_5' && !empty($settings['start_pin_icon_fontawesome_5'])){
					$timeline_start ='<div class="timeline-beginning-icon"><span>'.$icons.'</span></div>';
				}else{
					$timeline_start ='<div class="timeline-beginning-icon"><i class=" '.esc_attr($icons).'" ></i></div>';
				}
				
			}else if(isset($settings['start_pin_select_icon']) && $settings['start_pin_select_icon'] == 'text'){
				$text_start =$settings['start_pin_title'];
				$lz3 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['pin_start_text_background_image']) : '';
				$timeline_start ='<div class="timeline-text timeline-text-start '.esc_attr($lz3).'"><div class="beginning-text">'.esc_html($text_start).'</div></div>';
			}
		}
		$start_pin_none='';
		if($timeline_start==''){
			$start_pin_none= 'start-pin-none';
		}
		
		
		//End Pin Content
		$timeline_end='';		
		if(!empty($settings['end_pin_select_icon'])){
			if(isset($settings['end_pin_select_icon']) && $settings['end_pin_select_icon'] == 'image'){
				$loop_imgSrc='';
				if(!empty($settings["end_pin_image"]["url"])){
					$image_id=$settings["end_pin_image"]["id"];
					$loop_imgSrc= tp_get_image_rander( $image_id,$settings['end_pin_image_thumbnail_size'] );					
				}
				$timeline_end ='<div class="timeline-end-icon">'.$loop_imgSrc.'</div>';
			}else if(isset($settings['end_pin_select_icon']) && $settings['end_pin_select_icon'] == 'icon'){
				$icons='';
				if(!empty($settings["end_pin_icon_style"]) && $settings["end_pin_icon_style"]=='font_awesome'){
					$icons=$settings["end_pin_icon_fontawesome"];
				}else if(!empty($settings["end_pin_icon_style"]) && $settings["end_pin_icon_style"]=='font_awesome_5'){
					ob_start();
					\Elementor\Icons_Manager::render_icon( $settings['end_pin_icon_fontawesome_5'], [ 'aria-hidden' => 'true' ]);
					$icons = ob_get_contents();
					ob_end_clean();
				}else if(!empty($settings["end_pin_icon_style"]) && $settings["end_pin_icon_style"]=='icon_mind'){
					$icons=$settings["end_pin_icons_mind"];
				}
				
				if(!empty($settings["end_pin_icon_style"]) && $settings["end_pin_icon_style"]=='font_awesome_5' && !empty($settings['end_pin_icon_fontawesome_5'])){
					$timeline_end ='<div class="timeline-end-icon"><span>'.$icons.'</span></div>';
				}else{
					$timeline_end ='<div class="timeline-end-icon"><i class=" '.esc_attr($icons).'" ></i></div>';
				}
				
			}else if(isset($settings['end_pin_select_icon']) && $settings['end_pin_select_icon'] == 'text'){
				$text_end =$settings['end_pin_title'];
				$lz4 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['pin_end_text_background_image']) : '';
				$timeline_end ='<div class="timeline-text timeline-text-end '.esc_attr($lz4).'"><div class="end-text">'.esc_html($text_end).'</div></div>';
			}
		}
		$end_pin_none='';
		if($timeline_end==''){
			$end_pin_none= 'end-pin-none';
		}
		
		$timeline_layout = ($settings["timeline_content_align"]) ? 'timeline-'.esc_attr($settings["timeline_content_align"]).'-align' : 'timeline-center-align';
		
		$timeline='<div id="pt_plus_timeline" class="pt-plus-timeline-list '.esc_attr($timeline_masonry_class).' layout-both '.esc_attr($start_pin_none).' '.esc_attr($end_pin_none).' '.esc_attr($timeline_layout).' '.$data_class.'" data-id="'.esc_attr($uid).'" '.$timeline_inline_masonry.'>';
			
			$timeline .='<div class="post-inner-loop '.esc_attr($arrow_style).'">';
				$timeline .='<div class="timeline-track '.esc_attr($start_pin_none).' '.esc_attr($end_pin_none).'"></div>';
				$timeline .='<div class="timeline-track timeline-track-draw '.esc_attr($start_pin_none).' '.esc_attr($end_pin_none).'"></div>';
				$timeline .='<div class="timeline--icon">'.$timeline_start.'</div>';
				$timeline .=$timeline_loop;
				$timeline .='<div class="timeline--icon">'.$timeline_end.'</div>';
			$timeline .='</div>';
		$timeline .='</div>';
		
		$css_rule='';
		if($style=='style-1'){
			$css_rule .='<style>';
			if($settings["content_gap_between"]["size"]!=''){
				$gap=$settings["content_gap_between"]["size"].$settings["content_gap_between"]["unit"];
				$css_rule .='@media (min-width:731px){';
					$css_rule .='.'.esc_attr($uid).'.timeline-style-1 .timeline-item-wrap .timeline-item .timeline-tl-before{width:'.$gap.'}';
					$css_rule .='.'.esc_attr($uid).'.timeline-style-1.timeline-center-align .timeline-left-content{padding-right:'.esc_attr($gap).'}';
					$css_rule .='.'.esc_attr($uid).'.timeline-style-1.timeline-center-align .timeline-right-content{padding-left:'.esc_attr($gap).'}';
					$css_rule .='.'.esc_attr($uid).'.timeline-style-1.timeline-left-align .timeline-item-wrap{padding-right:'.esc_attr($gap).' !important;}';
					$css_rule .='.'.esc_attr($uid).'.timeline-style-1.timeline-right-align .timeline-item-wrap.timeline-left-content,.'.esc_attr($uid).'.timeline-style-1.timeline-right-align .timeline-item-wrap.timeline-right-content{padding-left:'.esc_attr($gap).' !important;}';
				$css_rule .='}';
			}
			if(!empty($settings["content_gap_between_tablet"]["size"])){
				$gap=$settings["content_gap_between_tablet"]["size"].$settings["content_gap_between_tablet"]["unit"];
				$css_rule .='@media (min-width:731px) and (max-width:1024px){';
					$css_rule .='.'.esc_attr($uid).'.timeline-style-1 .timeline-item-wrap .timeline-item .timeline-tl-before{width:'.$gap.'}';
					$css_rule .='.'.esc_attr($uid).'.timeline-style-1.timeline-center-align .timeline-left-content{padding-right:'.esc_attr($gap).'}';
					$css_rule .='.'.esc_attr($uid).'.timeline-style-1.timeline-center-align .timeline-right-content{padding-left:'.esc_attr($gap).'}';
					$css_rule .='.'.esc_attr($uid).'.timeline-style-1.timeline-left-align .timeline-item-wrap{padding-right:'.esc_attr($gap).' !important;}';
					$css_rule .='.'.esc_attr($uid).'.timeline-style-1.timeline-right-align .timeline-item-wrap.timeline-left-content,.'.esc_attr($uid).'.timeline-style-1.timeline-right-align .timeline-item-wrap.timeline-right-content{padding-left:'.esc_attr($gap).' !important;}';
				$css_rule .='}';
			}
			if(!empty($settings["content_gap_between_mobile"]["size"])){
				$gap=$settings["content_gap_between_mobile"]["size"].$settings["content_gap_between_mobile"]["unit"];
				$css_rule .='@media (max-width:730px){';
					$css_rule .='.'.esc_attr($uid).'.timeline-style-1 .timeline-item-wrap .timeline-item .timeline-tl-before{width:'.esc_attr($gap).'}';
					$css_rule .='.'.esc_attr($uid).'.timeline-style-1.timeline-center-align .timeline-item-wrap.timeline-left-content{padding-left:'.esc_attr($gap).' !important;}';
					$css_rule .='.'.esc_attr($uid).'.timeline-style-1.timeline-center-align .timeline-item-wrap.timeline-right-content{padding-left:'.esc_attr($gap).' !important;}';
					$css_rule .='.'.esc_attr($uid).'.timeline-style-1.timeline-left-align .timeline-item-wrap{padding-right:'.esc_attr($gap).' !important;}';
					$css_rule .='.'.esc_attr($uid).'.timeline-style-1.timeline-right-align .timeline-item-wrap.timeline-left-content,.'.esc_attr($uid).'.timeline-style-1.timeline-right-align .timeline-item-wrap.timeline-right-content{padding-left:'.$gap.' !important;}';
				$css_rule .='}';
			}
			$css_rule .='</style>';
		}
		
		echo $timeline.$css_rule;
	}
	
    protected function content_template() {
	
    }
}