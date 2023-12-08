<?php 
/*
Widget Name: Hotspot
Description: Style of pin point tooltips.
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
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Hotspot extends Widget_Base {
		
	public function get_name() {
		return 'tp-hotspot';
	}

    public function get_title() {
        return esc_html__('Hotspot', 'theplus');
    }
	
    public function get_icon() {
        return 'fa fa-thumb-tack theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-creatives');
    }
	public function get_keywords() {
		return [ 'hotspot', 'pinpoint', 'image hotspot', 'tooltip'];
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
			'hotspot_image',[
				'label' => esc_html__( 'Hotspot Image', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active'   => true,
				],
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'after',
				'exclude' => [ 'custom' ],
			]
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'layer_position',
			[
				'label' => esc_html__( 'Pin Position', 'theplus' ),
				'type' => Controls_Manager::HEADING,
			]
		);
		$repeater->add_control(
			'select_option',
			[
				'label' => esc_html__( 'Pin Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'icon',
				'options' => [
					'icon'  => esc_html__( 'Icon', 'theplus' ),
					'image'  => esc_html__( 'Image', 'theplus' ),
					'text'  => esc_html__( 'Text', 'theplus' ),
					'lottie' => esc_html__( 'Lottie', 'theplus' ),
				],
			]
		);
		$repeater->add_control(
			'lottieUrl',
			[
				'label' => esc_html__( 'Lottie URL', 'theplus' ),
				'type' => Controls_Manager::URL,				
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'condition' => ['select_option' => 'lottie'],
			]
		);
		$repeater->add_control(
			'icon_style',
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
					'select_option' => 'icon',
				],
			]
		);
		$repeater->add_control(
			'icon_fontawesome',
			[
				'label' => esc_html__( 'Icon', 'theplus' ),
				'type' => Controls_Manager::ICON,
				'label_block' => false,
				'default' => 'fa fa-chevron-right',
				'condition' => [
					'select_option' => 'icon',
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
					'select_option' => 'icon',
					'icon_style' => 'font_awesome_5',
				],
			]
		);
		$repeater->add_control(
			'icons_mind',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'default' => '',
				'label_block' => true,
				'options' => theplus_icons_mind(),
				'condition' => [
					'select_option' => 'icon',
					'icon_style' => 'icon_mind',
				],
			]
		);
		$repeater->add_control(
			'pin_image',[
				'label' => esc_html__( 'Pin Image', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active'   => true,
				],
				'condition'    => [
					'select_option' => [ 'image' ],
				],
			]
		);
		$repeater->add_control(
			'pin_image_hover',[
				'label' => esc_html__( 'Pin Hover Image', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active'   => true,
				],
				'condition'    => [
					'select_option' => [ 'image' ],
				],
			]
		);
		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'pin_thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'after',
				'condition'    => [
					'select_option' => [ 'image' ],
				],
			]
		);
		$repeater->add_control(
			'pin_text',
			[
				'label' => esc_html__( 'Pin Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Theplus', 'theplus' ),
				'dynamic' => [
					'active'   => true,
				],
				'condition'    => [
					'select_option' => [ 'text' ],
				],
			]
		);
		$repeater->start_controls_tabs( 'icon_style_options' );
		$repeater->start_controls_tab( 'icon_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$repeater->add_control(
			'icon_color',
			[
				'label'  => esc_html__( 'Icon Color', 'theplus' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pin-hotspot-loop{{CURRENT_ITEM}} .pin-loop-inner .pin-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pin-hotspot-loop{{CURRENT_ITEM}} .pin-loop-inner .pin-icon svg' => 'fill: {{VALUE}}',
				],
			]
		);
		$repeater->add_control(
			'pin_bg_color',
			[
				'label'  => esc_html__( 'Background Color', 'theplus' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pin-hotspot-loop{{CURRENT_ITEM}} .pin-loop-inner .pin-loop-content' => 'background: {{VALUE}}',
				],
			]
		);
		$repeater->end_controls_tab();
		$repeater->start_controls_tab( 'icon_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$repeater->add_control(
			'icon_hover_color',
			[
				'label'  => esc_html__( 'Icon Hover Color', 'theplus' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pin-hotspot-loop{{CURRENT_ITEM}} .pin-loop-inner:hover .pin-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pin-hotspot-loop{{CURRENT_ITEM}} .pin-loop-inner:hover .pin-icon svg' => 'fill: {{VALUE}}',
				],
			]
		);
		$repeater->add_control(
			'pin_hover_bg_color',
			[
				'label'  => esc_html__( 'Background Hover Color', 'theplus' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pin-hotspot-loop{{CURRENT_ITEM}} .pin-loop-inner:hover .pin-loop-content' => 'background: {{VALUE}}',
				],
			]
		);
		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();
		
		$repeater->start_controls_tabs( 'responsive_device' );
		$repeater->start_controls_tab( 'normal',
			[
				'label' => esc_html__( 'Desktop', 'theplus' ),
			]
		);
		/*desktop  start*/
		$repeater->add_control(
			'd_left_auto', [
				'label'   => esc_html__( 'Left (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),				
			]
		);

		$repeater->add_control(
			'd_pos_xposition', [
				'label' => esc_html__( 'Left', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => 40,
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'condition'    => [
					'd_left_auto' => [ 'yes' ],
				],
			]
		);
		$repeater->add_control(
			'd_right_auto',[
				'label'   => esc_html__( 'Right (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
			]
		);
		$repeater->add_control(
			'd_pos_rightposition',[
				'label' => esc_html__( 'Right', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => 40,
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'condition'    => [
					'd_right_auto' => [ 'yes' ],
				],
			]
		);
		$repeater->add_control(
			'd_top_auto', [
				'label'   => esc_html__( 'Top (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),				
			]
		);
		$repeater->add_control(
			'd_pos_yposition', [
				'label' => esc_html__( 'Top', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => 20,
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'condition'    => [
					'd_top_auto' => [ 'yes' ],
				],
			]
		);
		$repeater->add_control(
			'd_bottom_auto', [
				'label'   => esc_html__( 'Bottom (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
			]
		);
		$repeater->add_control(
			'd_pos_bottomposition', [
				'label' => esc_html__( 'Bottom', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => 20,
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'condition'    => [
					'd_bottom_auto' => [ 'yes' ],
				],
			]
		);
		$repeater->end_controls_tab();
		/*desktop end*/
		/*tablet start*/
		$repeater->start_controls_tab( 'tablet',
			[
				'label' => esc_html__( 'Tablet', 'theplus' ),
			]
		);
		$repeater->add_control(
			't_responsive', [
				'label'   => esc_html__( 'Responsive Values', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),
			]
		);
		$repeater->add_control(
			't_left_auto', [
				'label'   => esc_html__( 'Left (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					't_responsive' => [ 'yes' ],
				],
			]
		);
		$repeater->add_control(
			't_pos_xposition', [
				'label' => esc_html__( 'Left', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => '',
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'condition'    => [
					't_responsive' => [ 'yes' ],
					't_left_auto' => [ 'yes' ],
				],
			]
		);
		
		$repeater->add_control(
			't_right_auto',[
				'label'   => esc_html__( 'Right (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					't_responsive' => [ 'yes' ],
				],
			]
		);
		$repeater->add_control(
			't_pos_rightposition',[
				'label' => esc_html__( 'Right', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => '',
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'condition'    => [
					't_responsive' => [ 'yes' ],
					't_right_auto' => [ 'yes' ],
				],
			]
		);
		$repeater->add_control(
			't_top_auto', [
				'label'   => esc_html__( 'Top (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					't_responsive' => [ 'yes' ],
				],
			]
		);
		$repeater->add_control(
			't_pos_yposition', [
				'label' => esc_html__( 'Top', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => '',
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'condition'    => [
					't_responsive' => [ 'yes' ],
					't_top_auto' => [ 'yes' ],
				],
			]
		);
		$repeater->add_control(
			't_bottom_auto', [
				'label'   => esc_html__( 'Bottom (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					't_responsive' => [ 'yes' ],
				],
			]
		);
		$repeater->add_control(
			't_pos_bottomposition', [
				'label' => esc_html__( 'Bottom', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => '',
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],					
				],
				'separator' => 'after',
				'condition'    => [
					't_responsive' => [ 'yes' ],
					't_bottom_auto' => [ 'yes' ],
				],
			]
		);
		$repeater->end_controls_tab();
		/*tablet end*/
		/*mobile start*/
		$repeater->start_controls_tab( 'mobile',
			[
				'label' => esc_html__( 'Mobile', 'theplus' ),
			]
		);
		$repeater->add_control(
			'm_responsive', [
				'label'   => esc_html__( 'Responsive Values', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),
			]
		);
		$repeater->add_control(
			'm_left_auto', [
				'label'   => esc_html__( 'Left (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					'm_responsive' => [ 'yes' ],
				],
			]
		);
		$repeater->add_control(
			'm_pos_xposition', [
				'label' => esc_html__( 'Left', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => '',
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'condition'    => [
					'm_responsive' => [ 'yes' ],
					'm_left_auto' => [ 'yes' ],
				],
			]
		);
		$repeater->add_control(
			'm_right_auto',[
				'label'   => esc_html__( 'Right (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					'm_responsive' => [ 'yes' ],
				],
			]
		);
		$repeater->add_control(
			'm_pos_rightposition',[
				'label' => esc_html__( 'Right', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => '',
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'condition'    => [
					'm_responsive' => [ 'yes' ],
					'm_right_auto' => [ 'yes' ],
				],
			]
		);
		
		$repeater->add_control(
			'm_top_auto', [
				'label'   => esc_html__( 'Top (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					'm_responsive' => [ 'yes' ],
				],
			]
		);
		$repeater->add_control(
			'm_pos_yposition', [
				'label' => esc_html__( 'Top', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => '',
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'condition'    => [
					'm_responsive' => [ 'yes' ],
					'm_top_auto' => [ 'yes' ],
				],
			]
		);
		$repeater->add_control(
			'm_bottom_auto', [
				'label'   => esc_html__( 'Bottom (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					'm_responsive' => [ 'yes' ],
				],
			]
		);
		$repeater->add_control(
			'm_pos_bottomposition', [
				'label' => esc_html__( 'Bottom', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => '',
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'condition'    => [
					'm_responsive' => [ 'yes' ],
					'm_bottom_auto' => [ 'yes' ],
				],
			]
		);
		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();
		/*mobile end*/
		$repeater->add_control(
			'pin_content_options',[
				'label' => esc_html__( 'Pin Content', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$repeater->start_controls_tabs( 'plus_tooltip_tabs' );

		$repeater->start_controls_tab(
			'plus_tooltip_content_tab',
			[
				'label' => esc_html__( 'Content', 'theplus' ),
			]
		);
		$repeater->add_control(
			'plus_tooltip_content_type',
			[
				'label' => esc_html__( 'Content Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'normal_desc',
				'options' => [
					'normal_desc'  => esc_html__( 'Content Text', 'theplus' ),
					'content_wysiwyg'  => esc_html__( 'Content WYSIWYG', 'theplus' ),
				],
			]
		);
		$repeater->add_control(
			'plus_tooltip_content_desc',
			[
				'label' => esc_html__( 'Description', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 5,
				'default' => esc_html__( 'Luctus nec ullamcorper mattis', 'theplus' ),
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'plus_tooltip_content_type' => 'normal_desc',
				],
			]
		);
		$repeater->add_control(
			'plus_tooltip_content_wysiwyg',
			[
				'label' => esc_html__( 'Tooltip Content', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'theplus' ),
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'plus_tooltip_content_type' => 'content_wysiwyg',
				],
			]				
		);
		$repeater->add_control(
			'plus_tooltip_content_align',
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
				'label_block' => false,
				'selectors'  => [
					'{{WRAPPER}} .pin-hotspot-loop{{CURRENT_ITEM}} .tippy-tooltip .tippy-content' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'plus_tooltip_content_type' => 'normal_desc',
				],
			]
		);
		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'plus_tooltip_content_typography',
				'selector' => '{{WRAPPER}} .pin-hotspot-loop{{CURRENT_ITEM}} .tippy-tooltip .tippy-content',
				'condition' => [
					'plus_tooltip_content_type' => ['normal_desc','content_wysiwyg'],					
				],
			]
		);

		$repeater->add_control(
			'plus_tooltip_content_color',
			[
				'label'  => esc_html__( 'Text Color', 'theplus' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pin-hotspot-loop{{CURRENT_ITEM}} .tippy-tooltip .tippy-content,{{WRAPPER}} .pin-hotspot-loop{{CURRENT_ITEM}} .tippy-tooltip .tippy-content p' => 'color: {{VALUE}}',
				],
				'condition' => [
					'plus_tooltip_content_type' => ['normal_desc','content_wysiwyg'],					
				],
			]
		);
		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'plus_tooltip_styles_tab',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
			]
		);
		$repeater->add_group_control(
			\Theplus_Tooltips_Option_Group::get_type(),
			array(
				'label' => esc_html__( 'Tooltip Options', 'theplus' ),
				'name'           => 'tooltip_opt',
				'render_type'  => 'template',
			)
		);
		$repeater->add_group_control(
			\Theplus_Loop_Tooltips_Option_Style_Group::get_type(),
			array(
				'label' => esc_html__( 'Style Options', 'theplus' ),
				'name'           => 'tooltip_style',
				'render_type'  => 'template',
			)
		);
		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();
		$repeater->add_control(
			'extra_options',[
				'label' => esc_html__( 'Extra Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'image_effect',[
				'label' => esc_html__( 'Continues Effect','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'normal-drop_waves',
				'options' => [
					'' => esc_html__( 'None','theplus' ),
					'pulse' => esc_html__( 'Pulse','theplus' ),
					'floating' => esc_html__( 'Floating','theplus' ),
					'tossing' => esc_html__( 'Tossing','theplus' ),
					'normal-drop_waves' => esc_html__( 'Normal Drop Waves','theplus' ),
					'image-drop_waves' => esc_html__( 'Continue Drop Waves','theplus' ),					
					'hover_drop_waves' => esc_html__( 'Hover Drop Waves','theplus' ),
				],
			]
		);
		$repeater->add_control(
			'drop_waves_color',
			[
				'label' => esc_html__( 'Drop Wave Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pin-hotspot-loop{{CURRENT_ITEM}} .pin-loop-inner.image-drop_waves:after,{{WRAPPER}} .pin-hotspot-loop{{CURRENT_ITEM}} .pin-loop-inner.hover_drop_waves:after,{{WRAPPER}} .pin-hotspot-loop{{CURRENT_ITEM}} .pin-loop-inner.normal-drop_waves:after' => 'background: {{VALUE}}'
				],
				'condition'    => [
					'image_effect' => [ 'normal-drop_waves','image-drop_waves','hover_drop_waves' ],
				],
			]
		);
		$repeater->add_control(
			'hs_link_switch',
			[
				'label' => esc_html__( 'Link', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',				
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'hs_link',
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
				'condition'    => [
					'hs_link_switch' => 'yes',
				],
			]
		);	
		$this->add_control(
            'pin_hotspot',
            [
				'label' => esc_html__( 'Add Multiple Pin Hotspot', 'theplus' ),
                'type' => Controls_Manager::REPEATER,
				'description' => 'Add Pin Sections with Positions.',
                'default' => [
					'select_option' => '',
				],
				'fields' => $repeater->get_controls(),
                'title_field' => '{{{select_option}}}',
            ]
        );
		$this->end_controls_section();
		/*Icon Style*/
		$this->start_controls_section(
            'section_icon_styling',
            [
                'label' => esc_html__('Pin Icon', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
            'pin_icon_size',
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
					'{{WRAPPER}} .pin-hotspot-loop .pin-loop-content.pin-icon-font .pin-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pin-hotspot-loop .pin-loop-content.pin-icon-font .pin-icon svg' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',
				],
            ]
        );
		
		
		$this->add_responsive_control(
            'icon_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Pin Width', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 40,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pin-hotspot-loop .pin-loop-content.pin-icon-font' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_control(
			'icon_radius',
			[
				'label' => esc_html__( 'Icon Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .pin-hotspot-loop .pin-loop-content.pin-icon-font,{{WRAPPER}} .pin-loop-inner.image-drop_waves:after,{{WRAPPER}} .pin-loop-inner.hover_drop_waves:hover:after,{{WRAPPER}} .pin-loop-inner.normal-drop_waves:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_box_shadow',
				'selector' => '{{WRAPPER}} .pin-hotspot-loop .pin-loop-content.pin-icon-font',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_icon_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_hover_box_shadow',
				'selector' => '{{WRAPPER}} .pin-hotspot-loop .pin-loop-inner:hover .pin-loop-content.pin-icon-font',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Pin Icon Style*/
		/*Pin Image Style*/
		$this->start_controls_section(
            'section_pin_image_styling',
            [
                'label' => esc_html__('Pin Image', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
            'pin_image_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Pin Image Size', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 400,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 25,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pin-hotspot-loop .pin-loop-content.pin-icon-image img.pin-icon' => 'max-width: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_responsive_control(
            'pin_image_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Pin Image Width', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 400,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 60,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pin-hotspot-loop .pin-loop-content.pin-icon-image' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_control(
			'image_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .pin-hotspot-loop .pin-loop-content.pin-icon-image,{{WRAPPER}} .pin-loop-inner.image-drop_waves:after,{{WRAPPER}} .pin-loop-inner.hover_drop_waves:hover:after,{{WRAPPER}} .pin-loop-inner.normal-drop_waves:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'image_box_shadow',
				'selector' => '{{WRAPPER}} .pin-hotspot-loop .pin-loop-content.pin-icon-image',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_image_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'image_hover_box_shadow',
				'selector' => '{{WRAPPER}} .pin-hotspot-loop .pin-loop-inner:hover .pin-loop-content.pin-icon-image',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Pin Image Style*/
		/*Pin Text Style*/
		$this->start_controls_section(
            'section_text_styling',
            [
                'label' => esc_html__('Pin Text', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'label' => esc_html__( 'Text Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
				'selector' => '{{WRAPPER}} .pin-hotspot-loop .pin-loop-content.pin-icon-text .pin-icon',
			]
		);
		$this->add_control(
			'text_padding',
			[
				'label' => esc_html__( 'Text Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .pin-hotspot-loop .pin-loop-content.pin-icon-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'text_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .pin-hotspot-loop .pin-loop-content.pin-icon-text,{{WRAPPER}} .pin-loop-inner.image-drop_waves:after,{{WRAPPER}} .pin-loop-inner.hover_drop_waves:hover:after,{{WRAPPER}} .pin-loop-inner.normal-drop_waves:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_text_style' );
		$this->start_controls_tab(
			'tab_text_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'text_box_shadow',
				'selector' => '{{WRAPPER}} .pin-hotspot-loop .pin-loop-content.pin-icon-text',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_text_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'text_hover_box_shadow',
				'selector' => '{{WRAPPER}} .pin-hotspot-loop .pin-loop-inner:hover .pin-loop-content.pin-icon-text',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Pin Text Style*/
		
		/*Pin Lottie Style*/
		$this->start_controls_section(
            'section_lottie_styling',
            [
                'label' => esc_html__('Lottie', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
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
		/*Pin Lottie Style*/

		/*common styling Option*/
		$this->start_controls_section(
            'section_common_styling',
            [
                'label' => esc_html__('Common Styling', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
			'cs_icon_heading',[
				'label' => esc_html__( 'Icon/Text', 'theplus' ),
				'type' => Controls_Manager::HEADING,
			]
		);
		$this->start_controls_tabs( 'cs_icon_tabs' );
		$this->start_controls_tab( 'cs_icon_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'icon_color',
			[
				'label'  => esc_html__( 'Color', 'theplus' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pin-hotspot-loop .pin-loop-content .pin-icon' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'icon_background',
			[
				'label'  => esc_html__( 'Background', 'theplus' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pin-hotspot-loop .pin-loop-content' => 'background: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'cs_drop_waves_color',
			[
				'label'  => esc_html__( 'Drop Wave Color', 'theplus' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pin-hotspot-loop .pin-loop-inner:after' => 'background: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab( 'cs_icon_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'icon_color_h',
			[
				'label'  => esc_html__( 'Color', 'theplus' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pin-hotspot-loop .pin-loop-inner:hover .pin-loop-content .pin-icon' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'icon_background_h',
			[
				'label'  => esc_html__( 'Background', 'theplus' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pin-hotspot-loop .pin-loop-inner:hover .pin-loop-content' => 'background: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		
		$this->add_control(
			'cs_pin_content_heading',[
				'label' => esc_html__( 'Pin Content', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'cs_tooltip_typography',
				'selector' => '{{WRAPPER}} .pin-hotspot-loop .tippy-popper .tippy-content',
			]
		);
		$this->add_control(
			'cs_tooltip_color',
			[
				'label'  => esc_html__( 'Color', 'theplus' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pin-hotspot-loop .tippy-popper .tippy-content' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'cs_tooltip_background',
			[
				'label'  => esc_html__( 'Background', 'theplus' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pin-hotspot-loop .tippy-popper .tippy-tooltip' => 'background: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'cs_tooltip_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .pin-hotspot-loop .tippy-popper .tippy-tooltip',
			]
		);
		$this->add_responsive_control(
			'cs_tooltip_border_r',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pin-hotspot-loop .tippy-popper .tippy-tooltip' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'cs_tooltip_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .pin-hotspot-loop .tippy-popper .tippy-tooltip',
			]
		);
		$this->add_control(
			'cs_tooltip_box_bf',
			[
				'label' => esc_html__( 'Backdrop Filter', 'theplus' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'Default', 'theplus' ),
				'label_on' => __( 'Custom', 'theplus' ),
				'return_value' => 'yes',
			]
		);
		$this->add_control(
			'cs_tooltip_box_bf_blur',
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
					'cs_tooltip_box_bf' => 'yes',
				],
			]
		);
		$this->add_control(
			'cs_tooltip_box_bf_grayscale',
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
					'{{WRAPPER}} .pin-hotspot-loop .tippy-popper' => '-webkit-backdrop-filter:grayscale({{cs_tooltip_box_bf_grayscale.SIZE}})  blur({{cs_tooltip_box_bf_blur.SIZE}}{{cs_tooltip_box_bf_blur.UNIT}}) !important;backdrop-filter:grayscale({{cs_tooltip_box_bf_grayscale.SIZE}})  blur({{cs_tooltip_box_bf_blur.SIZE}}{{cs_tooltip_box_bf_blur.UNIT}}) !important;',
				 ],
				'condition'    => [
					'cs_tooltip_box_bf' => 'yes',
				],
			]
		);
		$this->end_popover();
		$this->add_control(
			'cs_tooltip_arrows_color',
			[
				'label'  => esc_html__( 'Arrows Color', 'theplus' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tippy-popper[x-placement^=left] .tippy-arrow' => 'border-left-color: {{VALUE}}',
					'{{WRAPPER}} .tippy-popper[x-placement^=right] .tippy-arrow' => 'border-right-color: {{VALUE}}',
					'{{WRAPPER}} .tippy-popper[x-placement^=top] .tippy-arrow' => 'border-top-color: {{VALUE}}',
					'{{WRAPPER}} .tippy-popper[x-placement^=bottom] .tippy-arrow' => 'border-bottom-color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_section();
		/*common styling Option*/
		
		/*Extra Option*/
		$this->start_controls_section(
            'section_extra_option_styling',
            [
                'label' => esc_html__('Extra Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
			'overlay_color_option',
			[
				'label' => esc_html__( 'Hover Overlay Color', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'overlay_background',
				'label' => esc_html__( 'Overlay Background Color', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .theplus-hotspot .theplus-hotspot-inner:after',
				'condition' => [
					'overlay_color_option' => 'yes',
				],
			]
		);
		$this->add_control(
			'tooltip_delay_visible',
			[
				'label' => __( 'Tooltip Visibility Delay', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'theplus' ),
				'label_off' => __( 'Off', 'theplus' ),
			]
		);
		$this->add_control(
			'tooltip_delay_time',
			[
				'label' => __( 'Delay Timeout', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 's' ],
				'range' => [
					's' => [
						'min' => 0,
						'max' => 15,
						'step' => 0.01,
					],
				],
				'default' => [
					'unit' => 's',
					'size' => 0,
				],
				'condition' => [
					'tooltip_delay_visible' => 'yes',
				],
			]
		);
		$this->add_control(
			'hot_spot_transform',
			[
				'label' => esc_html__( 'Transform css', 'theplus' ),
				'type' => Controls_Manager::TEXT,				
				'placeholder' => esc_html__( 'rotate(360deg)', 'theplus' ),
				'selectors' => [
					'{{WRAPPER}} .pin-hotspot-loop .pin-loop-inner:hover .pin-loop-content' => 'transform: {{VALUE}};-ms-transform: {{VALUE}};-moz-transform: {{VALUE}};-webkit-transform: {{VALUE}};transform-style: preserve-3d;-ms-transform-style: preserve-3d;-moz-transform-style: preserve-3d;-webkit-transform-style: preserve-3d;'
				],
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/*Extra Option*/
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
            'as_switch',
            [
				'label'   => esc_html__( 'Animation Stagger', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'condition' => [
					'animation_effects!' => 'no-animation',
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
					'as_switch' => 'yes',
				],
            ]
        );
		$this->add_control(
            'animation_duration_default',
            [
				'label'   => esc_html__( 'Animation Duration', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'condition' => [
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
		$overlay_color_option = ($settings["overlay_color_option"] == 'yes') ? 'overlay-bg-color' : '';
			$animated_strager_class='';
			$animation_effects = $settings["animation_effects"];
			$animation_delay = (isset($settings["animation_delay"]["size"])) ? $settings["animation_delay"]["size"] : 50;
			if($animation_effects == 'no-animation'){
				$animated_class = '';
				$animation_attr = '';
			}else{
				$animate_offset = theplus_scroll_animation();
				$animated_class = 'animate-general';
				$animation_attr = ' data-animate-type="'.esc_attr($animation_effects).'" data-animate-delay="'.esc_attr($animation_delay).'"';
				$animation_attr .= ' data-animate-offset="'.esc_attr($animate_offset).'"';

				if(!empty($settings['as_switch']) && $settings['as_switch'] == 'yes'){
					$animation_attr .=' data-animate-columns="stagger"';
					$animation_attr .=' data-animate-stagger="'.esc_attr((isset($settings["animation_stagger"]["size"])) ? $settings["animation_stagger"]["size"] : 150).'"';
					$animated_strager_class="animated-columns";
				}

				if($settings["animation_duration_default"] == 'yes'){
					$animate_duration = (isset($settings["animate_duration"]["size"]) ? $settings["animate_duration"]["size"] : 50);
					$animation_attr .= ' data-animate-duration="'.esc_attr($animate_duration).'"';
				}
				if(!empty($settings["animation_out_effects"]) && $settings["animation_out_effects"] != 'no-animation'){
					$animation_attr .= ' data-animate-out-type="'.esc_attr($settings["animation_out_effects"]).'" data-animate-out-delay="'.esc_attr((isset($settings["animation_out_delay"]["size"])) ? $settings["animation_out_delay"]["size"] : 50).'"';
					if($settings["animation_out_duration_default"] == 'yes'){
						$animation_attr .= ' data-animate-out-duration="'.esc_attr((isset($settings["animation_out_duration"]["size"])) ? $settings["animation_out_duration"]["size"] : 50).'"';
					}
				}
			}

			/*-- pin cascading ---*/
				$pin_loop='';
				if(!empty($settings['pin_hotspot'])) {
					$index=0;
					foreach($settings['pin_hotspot'] as $item) {
						$css_loop = '';
						$uid_loop = uniqid("pin").$item['_id'];
						$list_img=$list_img_hover=$select_option=$continue_effect='';
						
						if(!empty($item['image_effect'])){
							$continue_effect=$item['image_effect'];
						}

							$this->add_render_attribute( '_tooltip', 'data-tippy', '', true );

							if (!empty($item['plus_tooltip_content_type']) && $item['plus_tooltip_content_type']=='normal_desc') {
								$this->add_render_attribute( '_tooltip', 'title', $item['plus_tooltip_content_desc'], true );
							}else if (!empty($item['plus_tooltip_content_type']) && $item['plus_tooltip_content_type']=='content_wysiwyg') {
								$tooltip_content=$item['plus_tooltip_content_wysiwyg'];
								$this->add_render_attribute( '_tooltip', 'title', $tooltip_content, true );
							}
							
							$plus_tooltip_position=($item["tooltip_opt_plus_tooltip_position"]!='') ? $item["tooltip_opt_plus_tooltip_position"] : 'top';
							$this->add_render_attribute( '_tooltip', 'data-tippy-placement', $plus_tooltip_position, true );
							
							$tooltip_interactive =($item["tooltip_opt_plus_tooltip_interactive"]=='' || $item["tooltip_opt_plus_tooltip_interactive"]=='yes') ? 'true' : 'false';
							$this->add_render_attribute( '_tooltip', 'data-tippy-interactive', $tooltip_interactive, true );
							
							$plus_tooltip_theme=($item["tooltip_opt_plus_tooltip_theme"]!='') ? $item["tooltip_opt_plus_tooltip_theme"] : 'dark';
							$this->add_render_attribute( '_tooltip', 'data-tippy-theme', $plus_tooltip_theme, true );
							
							
							$tooltip_arrow =($item["tooltip_opt_plus_tooltip_arrow"]!='none' || $item["tooltip_opt_plus_tooltip_arrow"]=='') ? 'true' : 'false';
							$this->add_render_attribute( '_tooltip', 'data-tippy-arrow', $tooltip_arrow , true );
							
							$plus_tooltip_arrow=($item["tooltip_opt_plus_tooltip_arrow"]!='') ? $item["tooltip_opt_plus_tooltip_arrow"] : 'sharp';
							$this->add_render_attribute( '_tooltip', 'data-tippy-arrowtype', $plus_tooltip_arrow, true );
							
							$plus_tooltip_animation=($item["tooltip_opt_plus_tooltip_animation"]!='') ? $item["tooltip_opt_plus_tooltip_animation"] : 'shift-toward';
							$this->add_render_attribute( '_tooltip', 'data-tippy-animation', $plus_tooltip_animation, true );
							
							$plus_tooltip_x_offset=($item["tooltip_opt_plus_tooltip_x_offset"]!='') ? $item["tooltip_opt_plus_tooltip_x_offset"] : 0;
							$plus_tooltip_y_offset=($item["tooltip_opt_plus_tooltip_y_offset"]!='') ? $item["tooltip_opt_plus_tooltip_y_offset"] : 0;
							$this->add_render_attribute( '_tooltip', 'data-tippy-offset', $plus_tooltip_x_offset .','. $plus_tooltip_y_offset, true );
							
							$tooltip_duration_in =($item["tooltip_opt_plus_tooltip_duration_in"]!='') ? $item["tooltip_opt_plus_tooltip_duration_in"] : 250;
							$tooltip_duration_out =($item["tooltip_opt_plus_tooltip_duration_out"]!='') ? $item["tooltip_opt_plus_tooltip_duration_out"] : 200;
							$tooltip_trigger =($item["tooltip_opt_plus_tooltip_triggger"]!='') ? $item["tooltip_opt_plus_tooltip_triggger"] : 'mouseenter';
							$tooltip_arrowtype =($item["tooltip_opt_plus_tooltip_arrow"]!='') ? $item["tooltip_opt_plus_tooltip_arrow"] : 'sharp';
						
						if($item['select_option'] == 'icon'){
							$icons='';
							if(!empty($item["icon_style"]) && $item["icon_style"] == 'font_awesome'){
								$icons = $item["icon_fontawesome"];
							}else if(!empty($item["icon_style"]) && $item["icon_style"] == 'icon_mind'){
								$icons = $item["icons_mind"];
							}else if(!empty($item["icon_style"]) && $item["icon_style"] == 'font_awesome_5'){
								ob_start();
									\Elementor\Icons_Manager::render_icon( $item['icon_fontawesome_5'], [ 'aria-hidden' => 'true' ]);
									$icons = ob_get_contents();
								ob_end_clean();
							}
							if(!empty($item["icon_style"]) && $item["icon_style"] == 'font_awesome_5'){
								$list_img = '<span class="pin-icon" >'.$icons.'</span>';
							}else{
								$list_img = '<i class=" '.esc_attr($icons).' pin-icon" ></i>';
							}
							
							$select_option = 'pin-icon-font';
						}else if($item['select_option']=='image'){
							$image='';
							if(!empty($item["pin_image"]["url"])){
								$image_id=$item["pin_image"]["id"];				
								$image= tp_get_image_rander( $image_id,$item['pin_thumbnail_size'], [ 'class' => 'pin-icon pin-normal-icon' ] );
							}							
							
							$imagehover='';
							if(!empty($item["pin_image_hover"]["url"])){
								$imagehover_id=$item["pin_image_hover"]["id"];				
								$imagehover= tp_get_image_rander( $imagehover_id,$item['pin_thumbnail_size'], [ 'class' => 'pin-icon pin-icon-hover pin-hover-icon' ] );
							}

							$list_img = $image;
							$list_img_hover = $imagehover;
							$select_option ='pin-icon-image';
						}else if($item['select_option']=='text'){
							$text='';
							if(!empty($item["pin_text"])){
								$text = $item["pin_text"];
							}
							$list_img = '<div class="pin-icon ">'.esc_html($text).'</div>';
							$select_option ='pin-icon-text';
						}else if(isset($item['select_option']) && $item['select_option'] == 'lottie'){
							$ext = pathinfo($item['lottieUrl']['url'], PATHINFO_EXTENSION);			
							if($ext!='json'){
								$list_img .= '<h3 class="theplus-posts-not-found">'.esc_html__("Opps!! Please Enter Only JSON File Extension.",'theplus').'</h3>';
							}else{
								$lottiedisplay = isset($settings['lottiedisplay']) ? $settings['lottiedisplay'] : 'inline-block';
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
								$list_img .= '<lottie-player src="'.esc_url($item['lottieUrl']['url']).'" style="display: '.esc_attr($lottiedisplay).'; width: '.esc_attr($lottieWidth).'px; height: '.esc_attr($lottieHeight).'px; vertical-align: '.esc_attr($lottieVertical).';" '.esc_attr($lottieLoopValue).'  speed="'.esc_attr($lottieSpeed).'" '.esc_attr($lottieAnim).'></lottie-player>';
							}
						}

						/*link start*/
						$link=$target=$nofollow='';
						$link_key = 'link_' . $index;
						if(!empty($item['hs_link_switch']) && $item['hs_link_switch'] == 'yes'){
							if ( ! empty( $item['hs_link']['url'] ) ) {
								$this->add_link_attributes( $link_key, $item['hs_link'] );
							}
						}
						/*link end*/
						
					if((!empty($item['hs_link_switch']) && $item['hs_link_switch'] == 'yes') && !empty($item['hs_link']['url'])){
						$pin_loop .= '<a '.$this->get_render_attribute_string( $link_key ).'>';
					}

					$hoverclass='';
					if($item['select_option']=='image' && !empty($item["pin_image_hover"]["url"])){
						$hoverclass = ' tp-hover-image-exists';
					}

							$pin_loop .= '<div id="'.esc_attr($uid_loop).'" class="pin-hotspot-loop '.esc_attr($hoverclass).' '.esc_attr($uid_loop).' elementor-repeater-item-'.esc_attr($item['_id']). ' '.esc_attr($animated_strager_class).'" '.$this->get_render_attribute_string( '_tooltip' ).'>';
								$pin_loop .= '<div class="pin-loop-inner '.esc_attr($continue_effect).'">';
									$pin_loop .= '<div class="pin-loop-content '.esc_attr($select_option).'">';
										$pin_loop .= $list_img;
										$pin_loop .= $list_img_hover;
									$pin_loop .= '</div>';
								$pin_loop .= '</div>';
							$pin_loop .='</div>';
					if((!empty($item['hs_link_switch']) && $item['hs_link_switch'] == 'yes') && !empty($item['hs_link']['url'])){
						$pin_loop .= '</a>';
					}

						$rpos='auto';$bpos='auto';$ypos='auto';$xpos='auto';
						if($item['d_left_auto']=='yes'){
							if(!empty($item['d_pos_xposition']['size']) || $item['d_pos_xposition']['size']=='0'){
								$xpos = $item['d_pos_xposition']['size'].$item['d_pos_xposition']['unit'];
							}
						}
						if($item['d_top_auto']=='yes'){
							if(!empty($item['d_pos_yposition']['size']) || $item['d_pos_yposition']['size']=='0'){
								$ypos = $item['d_pos_yposition']['size'].$item['d_pos_yposition']['unit'];
							}
						}
						if($item['d_bottom_auto']=='yes'){
							if(!empty($item['d_pos_bottomposition']['size']) || $item['d_pos_bottomposition']['size']=='0'){
								$bpos = $item['d_pos_bottomposition']['size'].$item['d_pos_bottomposition']['unit'];
							}
						}
						if($item['d_right_auto']=='yes'){
							if(!empty($item['d_pos_rightposition']['size']) || $item['d_pos_rightposition']['size']=='0'){
								$rpos = $item['d_pos_rightposition']['size'].$item['d_pos_rightposition']['unit'];
							}
						}
						
						$css_loop.='.pin-hotspot-loop.'.esc_attr($uid_loop).'{top:'.esc_attr($ypos).';bottom:'.esc_attr($bpos).';left:'.esc_attr($xpos).';right:'.esc_attr($rpos).';margin: 0 auto;}';
						
						if(!empty($item['t_responsive']) && $item['t_responsive']=='yes'){
							$tablet_xpos='auto';$tablet_ypos='auto';$tablet_bpos='auto';$tablet_rpos='auto';
							if($item['t_left_auto']=='yes'){
								if(!empty($item['t_pos_xposition']['size']) || $item['t_pos_xposition']['size']=='0'){
									$tablet_xpos=$item['t_pos_xposition']['size'].$item['t_pos_xposition']['unit'];
								}
							}
							if($item['t_top_auto']=='yes'){
								if(!empty($item['t_pos_yposition']['size']) || $item['t_pos_yposition']['size']=='0'){
									$tablet_ypos=$item['t_pos_yposition']['size'].$item['t_pos_yposition']['unit'];
								}
							}
							if($item['t_bottom_auto']=='yes'){
								if(!empty($item['t_pos_bottomposition']['size']) || $item['t_pos_bottomposition']['size']=='0'){
									$tablet_bpos=$item['t_pos_bottomposition']['size'].$item['t_pos_bottomposition']['unit'];
								}
							}
							if($item['t_right_auto']=='yes'){
								if(!empty($item['t_pos_rightposition']['size']) || $item['t_pos_rightposition']['size']=='0'){
									$tablet_rpos=$item['t_pos_rightposition']['size'].$item['t_pos_rightposition']['unit'];
								}
							}
							
							$css_loop.='@media (min-width:601px) and (max-width:990px){.pin-hotspot-loop.'.esc_attr($uid_loop).'{top:'.esc_attr($tablet_ypos).';bottom:'.esc_attr($tablet_bpos).';left:'.esc_attr($tablet_xpos).';right:'.esc_attr($tablet_rpos).';margin: 0 auto;}}';
						}
						if(!empty($item['m_responsive']) && $item['m_responsive']=='yes'){
							$mobile_xpos='auto';$mobile_ypos='auto';$mobile_bpos='auto';$mobile_rpos='auto';
							if($item['m_left_auto']=='yes'){
								if(!empty($item['m_pos_xposition']['size']) || $item['m_pos_xposition']['size']=='0'){
									$mobile_xpos=$item['m_pos_xposition']['size'].$item['m_pos_xposition']['unit'];
								}
							}
							if($item['m_top_auto']=='yes'){
								if(!empty($item['m_pos_yposition']['size']) || $item['m_pos_yposition']['size']=='0'){
									$mobile_ypos=$item['m_pos_yposition']['size'].$item['m_pos_yposition']['unit'];
								}
							}
							if($item['m_bottom_auto']=='yes'){
								if(!empty($item['m_pos_bottomposition']['size']) || $item['m_pos_bottomposition']['size']=='0'){
									$mobile_bpos=$item['m_pos_bottomposition']['size'].$item['m_pos_bottomposition']['unit'];
								}
							}
							if($item['m_right_auto']=='yes'){
								if(!empty($item['m_pos_rightposition']['size']) || $item['m_pos_rightposition']['size']=='0'){
									$mobile_rpos=$item['m_pos_rightposition']['size'].$item['m_pos_rightposition']['unit'];
								}
							}
							$css_loop.='@media (max-width:600px){.pin-hotspot-loop.'.esc_attr($uid_loop).'{top:'.esc_attr($mobile_ypos).';bottom:'.esc_attr($mobile_bpos).';left:'.esc_attr($mobile_xpos).';right:'.esc_attr($mobile_rpos).';margin: 0 auto;}}';
						}
						if(!empty($settings["tooltip_delay_visible"]) && $settings["tooltip_delay_visible"]=='yes' && !empty($settings["tooltip_delay_time"]["size"])){
							$delay_time = $settings["tooltip_delay_time"]['size'] * 1000;
						}else{
							$delay_time = 0;
						}
						$inline_tippy_js='';
						
						$inline_tippy_js ='jQuery( document ).ready(function() {
						"use strict";
							if(typeof tippy === "function"){
								setTimeout(function(){
									tippy( "#'.esc_attr($uid_loop).'" , {
										arrowType : "'.esc_attr($tooltip_arrowtype).'",
										duration : ['.esc_attr($tooltip_duration_in).','.esc_attr($tooltip_duration_out).'],
										trigger : "'.esc_attr($tooltip_trigger).'",
										appendTo: document.querySelector("#'.esc_attr($uid_loop).'")
									});
								}, '.esc_attr($delay_time).');
							}
						});';
						$pin_loop .= wp_print_inline_script_tag($inline_tippy_js);
						$pin_loop .='<style>'.esc_attr($css_loop).'</style>';
						
						$index++;
					}
				}
			/*-- pin cascading ---*/
			
			$hotspot='<div class="theplus-hotspot '.esc_attr($animated_class).'" '.$animation_attr.'>';
				$hotspot .='<div class="theplus-hotspot-inner '.esc_attr($overlay_color_option).'">';

				if(!empty($settings['hotspot_image']["url"])){					
					$image_id=$settings["hotspot_image"]["id"];				
					$imgSrc= tp_get_image_rander( $image_id,$settings['thumbnail_size'], [ 'class' => 'hotspot-image' ] );
					$hotspot .=$imgSrc;
				}
				$hotspot .='<div class="hotspot-content-overlay">';
					$hotspot .= $pin_loop;
				$hotspot .='</div>';
				
				$hotspot .='</div>';
			$hotspot .='</div>';

		echo $hotspot;
	}
	
    protected function content_template() {
	
    }
}