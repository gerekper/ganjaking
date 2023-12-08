<?php 
/*
Widget Name: Cascading Image/Text
Description: cascading multiple image/Text creative effects.
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
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Cascading_Image extends Widget_Base {
		
	public function get_name() {
		return 'tp-cascading-image';
	}

    public function get_title() {
        return esc_html__('Image Cascading', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-object-group theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-creatives');
    }

    protected function register_controls() {
		
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Image Cascading', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'layer_position',
			[
				'label' => esc_html__( 'Layer Position', 'theplus' ),
				'type' => Controls_Manager::HEADING,
			]
		);
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
		$repeater->add_control(
			'd_pos_width',[
				'label' => esc_html__( 'Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 150,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 2000,
						'step' => 2,
					],
				],
				'separator' => 'after',
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
		$repeater->add_control(
			't_pos_width',[
				'label' => esc_html__( 'Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 2000,
						'step' => 2,
					],
				],
				'separator' => 'after',
				'condition'    => [
					't_responsive' => [ 'yes' ],
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
		$repeater->add_control(
			'm_pos_width',[
				'label' => esc_html__( 'Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 2000,
						'step' => 2,
					],
				],
				'condition'    => [
					'm_responsive' => [ 'yes' ],
				],
			]
		);
		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();
		/*mobile end*/
		$repeater->add_control(
			'select_option',[
				'label' => esc_html__( 'Layer Type','theplus' ),
				'type' => Controls_Manager::SELECT,
				'separator' => 'before',
				'default' => 'image',				
				'options' => [
					'image' => esc_html__( 'Image','theplus' ),
					'text' => esc_html__( 'Text Content','theplus' ),
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
			'multiple_image',[
				'label' => esc_html__( 'Image Select', 'theplus' ),
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
				'name' => 'image',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'after',
				'condition'    => [
					'select_option' => [ 'image' ],
				],
			]
		);
		$repeater->add_control(
			'text_content',
			[
				'label' => esc_html__( 'Text Content', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'ThePlus Addons', 'theplus' ),
				'dynamic' => [
					'active'   => true,
				],
				'condition'    => [
					'select_option' => [ 'text' ],
				],
			]
		);
		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_content_typography',
				'label' => esc_html__( 'Text Typography', 'theplus' ),
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
				],
				'selector' => '{{WRAPPER}} .cascading-text{{CURRENT_ITEM}} .cascading-inner-content,{{WRAPPER}} .cascading-text{{CURRENT_ITEM}} .cascading-inner-content a',
				'separator' => 'before',
				'condition'    => [
					'select_option' => [ 'text' ],
				],
			]
		);
		
		$repeater->add_control(
			'text_content_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .cascading-text{{CURRENT_ITEM}} .cascading-inner-content,{{WRAPPER}} .cascading-text{{CURRENT_ITEM}} .cascading-inner-content a' => 'color: {{VALUE}}',
				],
				'separator' => 'before',
				'condition'    => [
					'select_option' => [ 'text' ],
				],
			]
		);
		$repeater->add_control(
			'text_content_hover_color',
			[
				'label' => esc_html__( 'Text Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .cascading-text{{CURRENT_ITEM}}:hover .cascading-inner-content,{{WRAPPER}} .cascading-text{{CURRENT_ITEM}}:hover .cascading-inner-content a' => 'color: {{VALUE}}',
				],
				'condition'    => [
					'select_option' => [ 'text' ],
				],
			]
		);
		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'text_content_bg',
				'label' => esc_html__( 'Text Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .cascading-text{{CURRENT_ITEM}} .cascading-inner-content',
				'separator' => 'before',
				'dynamic' => [
					'active'   => true,
				],
				'condition'    => [
					'select_option' => [ 'text' ],
				],
			]
		);
		$repeater->add_control(
			'text_content_radius',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .cascading-text{{CURRENT_ITEM}} .cascading-inner-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
				'condition'    => [
					'select_option' => [ 'text' ],
				],
			]
		);
		$repeater->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_content_shadow',
				'label' => esc_html__( 'Text Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .cascading-text{{CURRENT_ITEM}} .cascading-inner-content',
				'separator' => 'before',
				'condition'    => [
					'select_option' => [ 'text' ],
				],
			]
		);
		$repeater->add_control(
			'extra_options',[
				'label' => esc_html__( 'Extra Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'    => [
					'select_option' => [ 'image','text' ],
				],
			]
		);		
		$repeater->add_control(
			'image_effect',[
				'label' => esc_html__( 'Continues Effect','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__( 'None','theplus' ),
					'pulse' => esc_html__( 'Pulse','theplus' ),
					'floating' => esc_html__( 'Floating','theplus' ),
					'tossing' => esc_html__( 'Tossing','theplus' ),
					'rotate-continue' => esc_html__( 'Rotating','theplus' ),
					'continue-scale' => esc_html__( 'Kenburns Scale','theplus' ),
					'hover-scale' => esc_html__( 'Hover Scale','theplus' ),
					'drop-waves' => esc_html__( 'Drop Waves','theplus' ),
					'hover-drop-waves' => esc_html__( 'Hover Drop Waves','theplus' ),
				],
				'condition'    => [
					'select_option' => [ 'image','text' ],
				],
			]
		);
		$repeater->add_control(
			'drop_waves_color',
			[
				'label' => esc_html__( 'Drop Wave Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .cascading-image{{CURRENT_ITEM}} .cascading-inner-content.drop-waves:after,{{WRAPPER}} .cascading-image{{CURRENT_ITEM}} .cascading-inner-content.hover-drop-waves:after,{{WRAPPER}} .cascading-text{{CURRENT_ITEM}} .cascading-inner-content.drop-waves:after,{{WRAPPER}} .cascading-text{{CURRENT_ITEM}} .cascading-inner-content.hover-drop-waves:after' => 'background: {{VALUE}}'
				],
				'condition'    => [
					'select_option' => [ 'image','text' ],
					'image_effect' => [ 'drop-waves','hover-drop-waves' ],
				],
			]
		);
		$repeater->add_control(
			'mask_image_display',[
				'label'   => esc_html__( 'Mask Image Shape', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before',
				'description' => esc_html__('Use PNG image with the shape you want to mask around Media Image.', 'theplus' ),
				'condition'    => [
					'select_option' => [ 'image','text' ],
				],
			]
		);
		$repeater->add_control(
			'mask_shape_image',
			[
				'label' => esc_html__( 'Mask Image', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'description' => esc_html__( 'Use PNG image with the shape you want to mask around feature image.', 'theplus' ),
				'selectors' => [
					'{{WRAPPER}} .cascading-image{{CURRENT_ITEM}} .cascading-inner-content,{{WRAPPER}} .cascading-text{{CURRENT_ITEM}} .cascading-inner-content' => 'mask-image: url({{URL}});-webkit-mask-image: url({{URL}});',
				],
				'condition' => [					
					'mask_image_display' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'mask_image_shadow',
			[
				'label' => esc_html__( 'Image Shadow', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Ex. 1px 1px 4px rgba(0,0,0,0.75)', 'theplus' ),
				'description' => esc_html__( 'Ex. 1px 1px 4px rgba(0,0,0,0.75)', 'theplus' ),
				'selectors' => [
					'{{WRAPPER}} .cascading-image{{CURRENT_ITEM}},{{WRAPPER}} .cascading-text{{CURRENT_ITEM}}' => '-webkit-filter: drop-shadow({{VALUE}});-moz-filter: drop-shadow({{VALUE}});-ms-filter: drop-shadow({{VALUE}});-o-filter: drop-shadow({{VALUE}});filter: drop-shadow({{VALUE}});',
				],
				'render_type' => 'ui',
				'condition' => [					
					'mask_image_display' => 'yes',
				],
			]
		);
		
		$repeater->add_control(
			'css_background_filter',
			[
				'label' => esc_html__( 'CSS Background Filter', 'theplus' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'Default', 'theplus' ),
				'label_on' => __( 'Custom', 'theplus' ),
				'return_value' => 'yes',
				'condition' => [
					'select_option' => 'text',
				],
			]
		);
		$repeater->add_responsive_control(
			'css_background_width',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .cascading-text{{CURRENT_ITEM}} .cascading-inner-content' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'select_option' => 'text',
					'css_background_filter'    => 'yes',
				],
			]
		);
		$repeater->add_responsive_control(
			'css_background_height',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Height', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .cascading-text{{CURRENT_ITEM}} .cascading-inner-content' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'select_option' => 'text',
					'css_background_filter'    => 'yes',
				],
			]
		);
		$repeater->add_control(
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
					'select_option' => 'text',
					'css_background_filter'    => 'yes',
				],
			]
		);
		$repeater->add_control(
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
					'{{WRAPPER}} .cascading-text{{CURRENT_ITEM}} .cascading-inner-content' => '-webkit-backdrop-filter:grayscale({{box_bg_bf_grayscale.SIZE}})  blur({{box_bg_bf_blur.SIZE}}{{box_bg_bf_blur.UNIT}}) !important;backdrop-filter:grayscale({{box_bg_bf_grayscale.SIZE}})  blur({{box_bg_bf_blur.SIZE}}{{box_bg_bf_blur.UNIT}}) !important;',
				 ],
				'condition'    => [
					'select_option' => 'text',
					'css_background_filter'    => 'yes',
				],
			]
		);
		$repeater->end_popover();
		
		$repeater->add_control(
			'loop_magic_scroll',[
				'label'   => esc_html__( 'Magic Scroll', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before',
				'condition'    => [
					'select_option' => [ 'image','text' ],
				],
			]
		);
		$repeater->add_group_control(
			\Theplus_Magic_Scroll_Option_Style_Group::get_type(),
			[
				'label' => esc_html__( 'Scroll Options', 'theplus' ),
				'name'           => 'loop_scroll_option',
				'render_type'  => 'template',
				'condition'    => [
					'loop_magic_scroll' => [ 'yes' ],
				],
			]
		);
		$repeater->start_controls_tabs( 'loop_tab_magic_scroll' );
		$repeater->start_controls_tab(
			'loop_tab_scroll_from',
			[
				'label' => esc_html__( 'Initial', 'theplus' ),
				'condition'    => [
					'loop_magic_scroll' => [ 'yes' ],
				],
			]
		);
		$repeater->add_group_control(
			\Theplus_Magic_Scroll_From_Style_Group::get_type(),
			[
				'label' => esc_html__( 'Initial Position', 'theplus' ),
				'name'           => 'loop_scroll_from',
				'condition'    => [
					'loop_magic_scroll' => [ 'yes' ],
				],
			]
		);
		$repeater->end_controls_tab();
		$repeater->start_controls_tab(
			'loop_tab_scroll_to',
			[
				'label' => esc_html__( 'Final', 'theplus' ),
				'condition'    => [
					'loop_magic_scroll' => [ 'yes' ],
				],
			]
		);
		$repeater->add_group_control(
			\Theplus_Magic_Scroll_To_Style_Group::get_type(),
			[
				'label' => esc_html__( 'Final Position', 'theplus' ),
				'name'           => 'loop_scroll_to',
				'condition'    => [
					'loop_magic_scroll' => [ 'yes' ],
				],
			]
		);
		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();
		$repeater->add_control(
			'plus_tooltip',
			[
				'label'        => esc_html__( 'Tooltip', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'theplus' ),
				'label_off'    => esc_html__( 'No', 'theplus' ),
				'separator' => 'before',
			]
		);

		$repeater->start_controls_tabs( 'plus_tooltip_tabs' );

		$repeater->start_controls_tab(
			'plus_tooltip_content_tab',
			[
				'label' => esc_html__( 'Content', 'theplus' ),
				'condition' => [
					'plus_tooltip' => 'yes',
				],
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
				'condition' => [
					'plus_tooltip' => 'yes',
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
				'condition' => [
					'plus_tooltip_content_type' => 'normal_desc',
					'plus_tooltip' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'plus_tooltip_content_wysiwyg',
			[
				'label' => esc_html__( 'Tooltip Content', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'theplus' ),
				'condition' => [
					'plus_tooltip_content_type' => 'content_wysiwyg',
					'plus_tooltip' => 'yes',
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
				'selectors'  => [
					'{{WRAPPER}} .cascading-image{{CURRENT_ITEM}} .tippy-tooltip .tippy-content,{{WRAPPER}} .cascading-text{{CURRENT_ITEM}} .tippy-tooltip .tippy-content' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'plus_tooltip_content_type' => 'normal_desc',
					'plus_tooltip' => 'yes',
				],
			]
		);
		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'plus_tooltip_content_typography',
				'selector' => '{{WRAPPER}} .cascading-image{{CURRENT_ITEM}} .tippy-tooltip .tippy-content,{{WRAPPER}} .cascading-text{{CURRENT_ITEM}} .tippy-tooltip .tippy-content',
				'condition' => [
					'plus_tooltip_content_type' => ['normal_desc','content_wysiwyg'],
					'plus_tooltip' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'plus_tooltip_content_color',
			[
				'label'  => esc_html__( 'Text Color', 'theplus' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .cascading-image{{CURRENT_ITEM}} .tippy-tooltip .tippy-content,{{WRAPPER}} .cascading-image{{CURRENT_ITEM}} .tippy-tooltip .tippy-content p,{{WRAPPER}} .cascading-text{{CURRENT_ITEM}} .tippy-tooltip .tippy-content,{{WRAPPER}} .cascading-text{{CURRENT_ITEM}} .tippy-tooltip .tippy-content p' => 'color: {{VALUE}}',
				],
				'condition' => [
					'plus_tooltip_content_type' => ['normal_desc','content_wysiwyg'],
					'plus_tooltip' => 'yes',
				],
			]
		);
		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'plus_tooltip_styles_tab',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'condition' => [
					'plus_tooltip' => 'yes',
				],
			]
		);
		$repeater->add_group_control(
			\Theplus_Tooltips_Option_Group::get_type(),
			array(
				'label' => esc_html__( 'Tooltip Options', 'theplus' ),
				'name'           => 'tooltip_opt',
				'render_type'  => 'template',
				'condition'    => [
					'plus_tooltip' => [ 'yes' ],
				],
			)
		);
		$repeater->add_group_control(
			\Theplus_Loop_Tooltips_Option_Style_Group::get_type(),
			array(
				'label' => esc_html__( 'Style Options', 'theplus' ),
				'name'           => 'tooltip_style',
				'render_type'  => 'template',
				'condition'    => [
					'plus_tooltip' => [ 'yes' ],
				],
			)
		);
		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();
		$repeater->add_control(
			'special_effect',[
				'label'   => esc_html__( 'Special Effect', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before',				
				'condition'    => [
					'select_option' => [ 'image','text' ],
				],
			]
		);
		$repeater->add_control(
			'effect_color_1',[
				'label' => esc_html__('Effect Color 1', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'condition'    => [
					'special_effect' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'effect_color_2',[
				'label' => esc_html__('Effect Color 2', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '#ff214f',
				'condition'    => [
					'special_effect' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'cascading_move_parallax',[
				'label'   => esc_html__( 'Parallax Move', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before',
				'condition'    => [
					'select_option' => [ 'image','text' ],
				],
			]
		);
		$repeater->add_control(
			'cascading_move_speed_x',[
				'label' => esc_html__( 'Move Parallax (X)', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => 30,
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 2,
					],
				],
				'condition'    => [
					'cascading_move_parallax' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'cascading_move_speed_y',[
				'label' => esc_html__( 'Move Parallax (Y)', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => 30,
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 2,
					],
				],
				'condition'    => [
					'cascading_move_parallax' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'hover_parallax',[
				'label'   => esc_html__( 'On Hover Tilt', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before',
				'condition'    => [
					'select_option' => [ 'image','text' ],
				],
			]
		);
		$repeater->add_control(
			'parallax_translatez',[
				'label' => esc_html__( 'TranslateZ', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
						'step' => 2,
					],
				],
				'condition'    => [
					'hover_parallax' => 'yes',
				],
			]
		);
		$repeater->add_control(
            'link_option', [
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Link /Popup', 'theplus'),
				'default' => '',
				'separator' => 'before',
				'options' => [
                    '' => esc_html__('Select Option', 'theplus'),
                    'normal_link' => esc_html__('Link', 'theplus'),
					'popup_link' => esc_html__('Popup', 'theplus'),
                ],	
			]
        );
		$repeater->add_control(
			'image_link',
			[
				'label' => esc_html__( 'Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'theplus' ),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
				'separator' => 'after',
				'condition' => [
					'link_option' => [ 'normal_link' ],
				],
			]
		);
		$repeater->add_control(
			'popup_content',
			[
				'label' => esc_html__( 'Popup Content', 'theplus' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://www.youtube.com/embed/2ReiWfKUxIM', 'theplus' ),
				'show_external' => false,
				'description' => esc_html__('Enter direct link of Youtube,Vimeo, Google Map or any other.', 'theplus'),
				'separator' => 'after',
				'default' => [
					'url' => '',
					'is_external' => false,
					'nofollow' => true,
				],
				'separator' => 'after',
				'condition' => [
					'link_option' => [ 'popup_link' ],
				],
			]
		);
		$repeater->start_controls_tabs( 'nav_shadow_style' );
		$repeater->start_controls_tab(
			'nav_shadow_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition'    => [
					'select_option' => [ 'image' ],
				],
			]
		);
		$repeater->add_control(
			'overlay_background',
			[
				'label' => esc_html__( 'Overlay Background', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .cascading-image{{CURRENT_ITEM}} .cascading-inner-content:after' => 'background: {{VALUE}}'
				],
				'condition'    => [
					'select_option' => [ 'image' ],
				],
			]
		);
		$repeater->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'img_shadow',
				'selector' => '{{WRAPPER}} .cascading-image{{CURRENT_ITEM}} .cascading-image-inner',
				'condition'    => [
					'select_option' => [ 'image' ],
				],
			]
		);
		$repeater->add_control(
			'opacity_normal',[
				'label' => esc_html__( 'Opacity', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => 1,
				],
				'range' => [
					'%' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .cascading-image{{CURRENT_ITEM}}' => 'opacity: {{SIZE}};',
				],
				'condition'    => [
					'select_option' => [ 'image' ],
				],
			]
		);
		$repeater->add_control(
			'transform_css',
			[
				'label' => esc_html__( 'Transform css', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'rotate(10deg) scale(1.1)', 'theplus' ),
				'selectors' => [
					'{{WRAPPER}} .cascading-image{{CURRENT_ITEM}}' => 'transform: {{VALUE}};-ms-transform: {{VALUE}};-moz-transform: {{VALUE}};-webkit-transform: {{VALUE}};transform-style: preserve-3d;-ms-transform-style: preserve-3d;-moz-transform-style: preserve-3d;-webkit-transform-style: preserve-3d;'
				],
				'condition'    => [
					'select_option' => [ 'image' ],
				],
			]
		);
		$repeater->add_control(
			'border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .cascading-image{{CURRENT_ITEM}} .cascading-image-inner,{{WRAPPER}} .cascading-image{{CURRENT_ITEM}} .cascading-inner-content:after,{{WRAPPER}} .cascading-image{{CURRENT_ITEM}} .cascading-inner-content.drop-waves:after,{{WRAPPER}} .cascading-image{{CURRENT_ITEM}} .cascading-inner-content.hover-drop-waves:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
				'condition'    => [
					'select_option' => [ 'image' ],
				],
			]
		);
		$repeater->end_controls_tab();
		$repeater->start_controls_tab(
			'nav_shadow_active',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition'    => [
					'select_option' => [ 'image' ],
				],
			]
		);
		$repeater->add_control(
			'hover_overlay_background',
			[
				'label' => esc_html__( 'Overlay Background', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .cascading-image{{CURRENT_ITEM}} .cascading-inner-content:hover:after' => 'background: {{VALUE}}'
				],
				'condition'    => [
					'select_option' => [ 'image' ],
				],
			]
		);
		$repeater->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'img_hover_shadow',
				'selector' => '{{WRAPPER}} .cascading-image{{CURRENT_ITEM}}:hover',
				'condition'    => [
					'select_option' => [ 'image' ],
				],
			]
		);
		$repeater->add_control(
			'opacity_hover',[
				'label' => esc_html__( 'Hover Opacity', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => 1,
				],
				'range' => [
					'%' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .cascading-image{{CURRENT_ITEM}}:hover' => 'opacity: {{SIZE}};',
				],
				'condition'    => [
					'select_option' => [ 'image' ],
				],
			]
		);
		$repeater->add_control(
			'transform_hover_css',
			[
				'label' => esc_html__( 'Transform Hover css', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'rotate(10deg) scale(1.1)', 'theplus' ),
				'selectors' => [
					'{{WRAPPER}} .cascading-image{{CURRENT_ITEM}}:hover' => 'transform: {{VALUE}};-ms-transform: {{VALUE}};-moz-transform: {{VALUE}};-webkit-transform: {{VALUE}};'
				],
				'condition'    => [
					'select_option' => [ 'image' ],
				],
			]
		);
		$repeater->add_control(
			'border_radius_hover',
			[
				'label'      => esc_html__( 'Border Radius Hover', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .cascading-image{{CURRENT_ITEM}}:hover,{{WRAPPER}} .cascading-image{{CURRENT_ITEM}}:hover .cascading-inner-content:after,{{WRAPPER}} .cascading-image{{CURRENT_ITEM}}:hover .cascading-inner-content.drop-waves:after,{{WRAPPER}} .cascading-image{{CURRENT_ITEM}}:hover .cascading-inner-content.hover-drop-waves:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
				'condition'    => [
					'select_option' => [ 'image' ],
				],
			]
		);
		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();
		$repeater->add_control(
			'responsive_visible_opt',[
				'label'   => esc_html__( 'Responsive Visibility', 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'separator' => 'before',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),	
				'default' => 'no',
				'condition'    => [
					'select_option' => [ 'image' ],
				],
			]
		);
		$repeater->add_control(
			'desktop_opt',[
				'label'   => esc_html__( 'Desktop', 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'condition'    => [
					'select_option' => [ 'image' ],
					'responsive_visible_opt' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'tablet_opt',[
				'label'   => esc_html__( 'Tablet', 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'condition'    => [
					'select_option' => [ 'image' ],
					'responsive_visible_opt' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'mobile_opt',[
				'label'   => esc_html__( 'Mobile', 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'condition'    => [
					'select_option' => [ 'image' ],
					'responsive_visible_opt' => 'yes',
				],
			]
		);
		
		$this->add_control(
            'image_cascading',
            [
				'label' => esc_html__( 'Add Multiple Cascading Sections', 'theplus' ),
                'type' => Controls_Manager::REPEATER,
				'description' => 'Add Cascading Sections with Positions.',
                'default' => [
                    [
                        'select_option' => 'image',                       
                    ],
                ],                
				'fields' => $repeater->get_controls(),
                'title_field' => '{{{select_option}}}',
            ]
        );
		
		$this->end_controls_section();
		$this->start_controls_section(
			'styling_section',
			[
				'label' => esc_html__( 'Styling', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);	
		$this->add_responsive_control(
			'min_height',
			[
				'label' => esc_html__( 'Minimum Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 400,
				],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
				],
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_animated_image.cascading-block' => 'min-height:{{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'slide_show',
			[
				'label'   => esc_html__( 'Slide Show', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);
		$this->add_control(
            'slide_change_opt', [
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('SlideShow Type', 'theplus'),
				'default' => 'onclick',
				'options' => [
                    'onclick' => esc_html__('On Click', 'theplus'),
                    'setinterval' => esc_html__('Autoplay', 'theplus'),
                ],
				'condition' => [
					'slide_show' => [ 'yes' ],
				],		
			]
        );
		$this->add_control(
            'interval_time',
            [
                'type' => Controls_Manager::TEXT,
				'label' => esc_html__('Autoplay Duration', 'theplus'),
				'default' => 4000,
				'condition' => [
					'slide_show' => [ 'yes' ],
					'slide_change_opt' => [ 'setinterval' ],
				],
            ]
        );
		$this->add_control(
			'section_overflow_desktop',
			[
				'label'   => esc_html__( 'Overflow Hidden (Desktop)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before',
				'description' => esc_html__('You can setup over flow hidden option if your section is going our and having unwanted scrollbar.','theplus'),
			]
		);
		$this->add_control(
			'section_overflow_tablet',
			[
				'label'   => esc_html__( 'Overflow Hidden (Tablet)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);
		$this->add_control(
			'section_overflow_mobile',
			[
				'label'   => esc_html__( 'Overflow Hidden (Mobile)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);
		$this->end_controls_section();
		/*lottie style start*/
		$this->start_controls_section(
            'section_lottie_styling',
            [
                'label' => esc_html__('Lottie', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
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
					'size' => 100,
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
					'size' => 100,
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
		// $this->end_controls_section();
		$this->start_controls_tabs('style_tabs');
		$this->start_controls_tab('style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'textdomain' ),
			]
		);
		$this->add_group_control(\Elementor\Group_Control_Css_Filter::get_type(),
			[
				'name' => 'custom_css_filters',
				'selector' => '{{WRAPPER}} .cascading-inner-loop .cascading-image-inner',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('style_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'textdomain' ),
			]
		);
		$this->add_group_control(\Elementor\Group_Control_Css_Filter::get_type(),
			[
				'name' => 'custom_css_filters1',
				'selector' => '{{WRAPPER}} .cascading-inner-loop .cascading-image-inner:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

		/*lottie style end*/
	
	 protected function render() {
		$overflow_attr='';
		$settings = $this->get_settings_for_display();	
		$uid_cascading=uniqid("cascading_");
		$uid=uniqid("slide"); $attr=$wrapperClass='';
		
		$overflow_attr .=(!empty($settings["section_overflow_desktop"]) && $settings["section_overflow_desktop"]=='yes') ? ' data-overflow-desktop="yes"' : '';
		$overflow_attr .=(!empty($settings["section_overflow_tablet"]) && $settings["section_overflow_tablet"]=='yes') ? ' data-overflow-tablet="yes"' : '';
		$overflow_attr .=(!empty($settings["section_overflow_mobile"]) && $settings["section_overflow_mobile"]=='yes') ? ' data-overflow-mobile="yes"' : '';
			if(!empty($settings["slide_show"]) && $settings["slide_show"]=='yes'){
				$wrapperClass .=' slide_show_image '.esc_attr($uid);
				$attr .=' data-play="'.esc_attr($settings["slide_change_opt"]).'"';
				$attr .=' data-uid="'.esc_attr($uid).'"';
				$attr .=' data-interval_time="'.esc_attr($settings["interval_time"]).'"';						
			}
				/*--------------cascading image ----------------------------*/
				$cascading_loop=$css_loop=$hover_tilt='';
				$ij=0;
				if(!empty($settings['image_cascading'])) {
						$position='';
						$effects='';
						$animate_speed='';
						$cascading_move_parallax=$move_parallax_attr=$parallax_move='';
						
					foreach($settings['image_cascading'] as $item) {
						
						$visiblity_hide='';
						if(!empty($item['responsive_visible_opt']) && $item['responsive_visible_opt']=='yes'){
							$visiblity_hide .= (($item['desktop_opt']!='yes' && $item['desktop_opt']=='') ? 'hide-desktop ' : '' );							
							$visiblity_hide .= (($item['tablet_opt']!='yes' && $item['tablet_opt']=='') ? 'hide-tablet ' : '' );
							$visiblity_hide .= (($item['mobile_opt']!='yes' && $item['mobile_opt']=='') ? 'hide-mobile ' : '' );
						}
						
						$mask_image='';
						if(!empty($item["mask_image_display"]) && $item["mask_image_display"]=='yes'){
							$mask_image=' creative-mask-media';
						}						
						$image_effect='';
						if(!empty($item['image_effect'])){
							if($item['image_effect'] == 'pulse'){
								$image_effect = 'tp-pulse';
							}else{
								$image_effect=$item['image_effect'];
							}
						}
						$magic_class = $magic_attr = $parallax_scroll = '';
						if (!empty($item['loop_magic_scroll']) && $item['loop_magic_scroll'] == 'yes') {
							
							if(empty($item["loop_scroll_option_popover_toggle"])){
								$scroll_offset=0;
								$scroll_duration=300;
							}else{
								$scroll_offset	 = (isset($item['loop_scroll_option_scroll_offset']) ? $item['loop_scroll_option_scroll_offset'] : 0 );
								$scroll_duration = (isset($item['loop_scroll_option_scroll_duration']) ? $item['loop_scroll_option_scroll_duration'] : 300 );
							}
							if(empty($item["loop_scroll_from_popover_toggle"])){
								$scroll_x_from=0;
								$scroll_y_from=0;
								$scroll_opacity_from=1;
								$scroll_scale_from=1;
								$scroll_rotate_from=0;
							}else{
								$scroll_x_from = (isset($item['loop_scroll_from_scroll_x_from']) ? $item['loop_scroll_from_scroll_x_from'] : 0 );
								$scroll_y_from = (isset($item['loop_scroll_from_scroll_y_from']) ? $item['loop_scroll_from_scroll_y_from'] : 0 );
								$scroll_opacity_from = (isset($item['loop_scroll_from_scroll_opacity_from']) ? $item['loop_scroll_from_scroll_opacity_from'] : 1 );
								$scroll_scale_from 	= (isset($item['loop_scroll_from_scroll_scale_from']) ? $item['loop_scroll_from_scroll_scale_from'] : 1 );
								$scroll_rotate_from = (isset($item['loop_scroll_from_scroll_rotate_from']) ? $item['loop_scroll_from_scroll_rotate_from'] : 0 );
							}
							if($item["loop_scroll_to_popover_toggle"]==''){
								$scroll_x_to=0;
								$scroll_y_to=-50;
								$scroll_opacity_to=1;
								$scroll_scale_to=1;
								$scroll_rotate_to=0;
							}else{
								$scroll_x_to = (isset($item['loop_scroll_to_scroll_x_to']) ? $item['loop_scroll_to_scroll_x_to'] : 0 );
								$scroll_y_to = (isset($item['loop_scroll_to_scroll_y_to']) ? $item['loop_scroll_to_scroll_y_to'] : -50 );
								$scroll_opacity_to = (isset($item['loop_scroll_to_scroll_opacity_to']) ? $item['loop_scroll_to_scroll_opacity_to'] : 1 );
								$scroll_scale_to = (isset($item['loop_scroll_to_scroll_scale_to']) ? $item['loop_scroll_to_scroll_scale_to'] : 1 );
								$scroll_rotate_to = (isset($item['loop_scroll_to_scroll_rotate_to']) ? $item['loop_scroll_to_scroll_rotate_to'] : 0 );
							}
							$magic_attr .= ' data-scroll_type="position" ';
							$magic_attr .= ' data-scroll_offset="' . esc_attr($scroll_offset) . '" ';
							$magic_attr .= ' data-scroll_duration="' . esc_attr($scroll_duration) . '" ';
							
							$magic_attr .= ' data-scroll_x_from="' . esc_attr($scroll_x_from) . '" ';
							$magic_attr .= ' data-scroll_x_to="' . esc_attr($scroll_x_to) . '" ';
							$magic_attr .= ' data-scroll_y_from="' . esc_attr($scroll_y_from) . '" ';
							$magic_attr .= ' data-scroll_y_to="' . esc_attr($scroll_y_to) . '" ';
							$magic_attr .= ' data-scroll_opacity_from="' . esc_attr($scroll_opacity_from) . '" ';
							$magic_attr .= ' data-scroll_opacity_to="' . esc_attr($scroll_opacity_to) . '" ';
							$magic_attr .= ' data-scroll_scale_from="' . esc_attr($scroll_scale_from) . '" ';
							$magic_attr .= ' data-scroll_scale_to="' . esc_attr($scroll_scale_to) . '" ';
							$magic_attr .= ' data-scroll_rotate_from="' . esc_attr($scroll_rotate_from) . '" ';
							$magic_attr .= ' data-scroll_rotate_to="' . esc_attr($scroll_rotate_to) . '" ';
							
							$parallax_scroll .= ' parallax-scroll ';							
							$magic_class .= ' magic-scroll ';
						}
						$_tooltip = '_tooltip' . $ij;
						if( $item['plus_tooltip'] == 'yes' ) {
							
							$this->add_render_attribute( $_tooltip, 'data-tippy', '', true );

							if (!empty($item['plus_tooltip_content_type']) && $item['plus_tooltip_content_type']=='normal_desc') {
								$this->add_render_attribute( $_tooltip, 'title', $item['plus_tooltip_content_desc'], true );
							}else if (!empty($item['plus_tooltip_content_type']) && $item['plus_tooltip_content_type']=='content_wysiwyg') {
								$tooltip_content=$item['plus_tooltip_content_wysiwyg'];
								$this->add_render_attribute( $_tooltip, 'title', $tooltip_content, true );
							}
							
							$plus_tooltip_position=(!empty($item["tooltip_opt_plus_tooltip_position"])) ? $item["tooltip_opt_plus_tooltip_position"] : 'top';
							$this->add_render_attribute( $_tooltip, 'data-tippy-placement', $plus_tooltip_position, true );
							
							$tooltip_interactive =(isset($item["tooltip_opt_plus_tooltip_interactive"]) && $item["tooltip_opt_plus_tooltip_interactive"]=='yes') ? 'true' : 'false';
							$this->add_render_attribute( $_tooltip, 'data-tippy-interactive', $tooltip_interactive, true );
							
							$plus_tooltip_theme=(!empty($item["tooltip_opt_plus_tooltip_theme"])) ? $item["tooltip_opt_plus_tooltip_theme"] : 'dark';
							$this->add_render_attribute( $_tooltip, 'data-tippy-theme', $plus_tooltip_theme, true );
							
							
							$tooltip_arrow =($item["tooltip_opt_plus_tooltip_arrow"]!='none' || empty($item["tooltip_opt_plus_tooltip_arrow"])) ? 'true' : 'false';
							$this->add_render_attribute( $_tooltip, 'data-tippy-arrow', $tooltip_arrow , true );
							
							$plus_tooltip_arrow=(!empty($item["tooltip_opt_plus_tooltip_arrow"])) ? $item["tooltip_opt_plus_tooltip_arrow"] : 'sharp';
							$this->add_render_attribute( $_tooltip, 'data-tippy-arrowtype', $plus_tooltip_arrow, true );
							
							$plus_tooltip_animation=(!empty($item["tooltip_opt_plus_tooltip_animation"])) ? $item["tooltip_opt_plus_tooltip_animation"] : 'shift-toward';
							$this->add_render_attribute( $_tooltip, 'data-tippy-animation', $plus_tooltip_animation, true );
							
							$plus_tooltip_x_offset=(!empty($item["tooltip_opt_plus_tooltip_x_offset"])) ? $item["tooltip_opt_plus_tooltip_x_offset"] : 0;
							$plus_tooltip_y_offset=(!empty($item["tooltip_opt_plus_tooltip_y_offset"])) ? $item["tooltip_opt_plus_tooltip_y_offset"] : 0;
							$this->add_render_attribute( $_tooltip, 'data-tippy-offset', $plus_tooltip_x_offset .','. $plus_tooltip_y_offset, true );
							
							$tooltip_duration_in =(!empty($item["tooltip_opt_plus_tooltip_duration_in"])) ? $item["tooltip_opt_plus_tooltip_duration_in"] : 250;
							$tooltip_duration_out =(!empty($item["tooltip_opt_plus_tooltip_duration_out"])) ? $item["tooltip_opt_plus_tooltip_duration_out"] : 200;
							$tooltip_trigger =(!empty($item["tooltip_opt_plus_tooltip_triggger"])) ? $item["tooltip_opt_plus_tooltip_triggger"] : 'mouseenter';
							$tooltip_arrowtype =(!empty($item["tooltip_opt_plus_tooltip_arrow"])) ? $item["tooltip_opt_plus_tooltip_arrow"] : 'sharp';
						}
						$rand_no=rand(1000000, 1500000);
						
						if(!empty($item['hover_parallax']) && $item['hover_parallax']=='yes'){
							$css_loop .='.parallax-hover-'.esc_js($rand_no).'{-webkit-transform:translateZ('.esc_js($item["parallax_translatez"]["size"].$item["parallax_translatez"]["unit"]).') !important;-ms-transform:translateZ('.esc_js($item["parallax_translatez"]["size"].$item["parallax_translatez"]["unit"]).') !important;-moz-transform:translateZ('.esc_js($item["parallax_translatez"]["size"].$item["parallax_translatez"]["unit"]).') !important;-o-transform:translateZ('.esc_js($item["parallax_translatez"]["size"].$item["parallax_translatez"]["unit"]).') !important; transform: translateZ('.esc_js($item["parallax_translatez"]["size"].$item["parallax_translatez"]["unit"]).') !important;}';		
						}
						
						$move_parallax_attr=$parallax_move='';
						if(!empty($item['cascading_move_parallax']) && $item['cascading_move_parallax']=='yes' ){
							$cascading_move_parallax='pt-plus-move-parallax';
							$parallax_move='parallax-move';
							if(!empty($item['cascading_move_speed_x']['size'])){
								$move_parallax_attr .= ' data-move_speed_x="' . esc_attr($item['cascading_move_speed_x']['size']) . '" ';
							}else{
								$move_parallax_attr .= ' data-move_speed_x="0" ';
							}
							if(!empty($item['cascading_move_speed_y']['size'])){
								$move_parallax_attr .= ' data-move_speed_y="' . esc_attr($item['cascading_move_speed_y']['size']) . '" ';
							}else{
								$move_parallax_attr .= ' data-move_speed_y="0" ';
							}
						}
						$reveal_effects=$effect_attr='';
							if(!empty($item['special_effect']) && $item['special_effect']=='yes'){
								$effect_rand_no =uniqid('reveal');
								$effect_attr .=' data-reveal-id="'.esc_attr($effect_rand_no).'" ';
								if(!empty($item['effect_color_1'])){
									$effect_attr .=' data-effect-color-1="'.esc_attr($item['effect_color_1']).'" ';
								}else{
									$effect_attr .=' data-effect-color-1="#313131" ';
								}
								if(!empty($item['effect_color_2'])){
									$effect_attr .=' data-effect-color-2="'.esc_attr($item['effect_color_2']).'" ';
								}else{
									$effect_attr .=' data-effect-color-2="#ff214f" ';
								}
								$reveal_effects=' pt-plus-reveal '.esc_attr($effect_rand_no).' ';
							}
						$target=$nofollow=$urllink='';
						$uimg_id=uniqid("img").esc_attr($ij);
						$uid_loop=uniqid("cascading");
						if($item['select_option']=='image'){
							if($item['link_option']=='normal_link' || $item['link_option']=='popup_link'){
								$link_class="link-content";
							}else{
								$link_class='not-link-content';	
							}
							if(!empty($item['multiple_image']['id'])){
								$multiple_image=$item['multiple_image']['id'];
								$imgSrc = tp_get_image_rander( $multiple_image,$item['image_size'], [ 'class' => 'parallax_image' ] );
								$content_image =$imgSrc;					
								
								$cascading_loop .= '<div id="'.esc_attr($uid_loop).esc_attr($ij).'" class="cascading-image elementor-repeater-item-' . $item['_id'] . ' '.esc_attr($uimg_id).' '.esc_attr($visiblity_hide).' ' . esc_attr($magic_class) . ' '.esc_attr($parallax_move).'" '.$this->get_render_attribute_string( $_tooltip ).' '.$move_parallax_attr.'>';
									$cascading_loop .= '<div class="cascading-image-inner ' . esc_attr($parallax_scroll) . '" ' . $magic_attr . '>';
										$cic_bg = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($item['text_content_bg_image']) : '';
										$cascading_loop .= '<div class="cascading-inner-content parallax-hover-'.esc_attr($rand_no).' '.$image_effect.' '.esc_attr($reveal_effects).' '.esc_attr($link_class).' '.esc_attr($mask_image).' '.$cic_bg.'" '.$effect_attr.'>';
											if($item['link_option']=='normal_link' || $item['link_option']=='popup_link'){
												$data_popup='';
												if($item['link_option']=='popup_link'){
													$data_popup='data-lity=""';
												}
												if(!empty($item['popup_content']['url']) && $item['link_option']=='popup_link'){
													$target = $item['popup_content']['is_external'] ? '' : '';
													$nofollow = $item['popup_content']['nofollow'] ? ' rel="nofollow"' : '';
													$urllink = $item['popup_content']['url'];
												}
												
												if(!empty($item['image_link']['url']) && $item['link_option']=='normal_link'){
													$target = $item['image_link']['is_external'] ? ' target="_blank"' : '';
													$nofollow = $item['image_link']['nofollow'] ? ' rel="nofollow"' : '';
													$urllink = $item['image_link']['url'];
												}
												$cascading_loop .= '<a href="'.esc_url($urllink).'" '.$target.$nofollow.' '.$data_popup.'>';
											}
												$cascading_loop .=$content_image;
											if($item['link_option']=='normal_link' || $item['link_option']=='popup_link'){
												$cascading_loop .= '</a>';
											}
										$cascading_loop .='</div>';
									$cascading_loop .='</div>';
								$cascading_loop .='</div>';
								
							}	
						}
						if($item['select_option']=='text'){
							//if(!empty($item['text_content'])){
								$text_content=$item['text_content'];
								if($item['link_option']=='normal_link' || $item['link_option']=='popup_link'){
									$link_class="link-content";
								}else{
									$link_class='not-link-content';	
								}
								$cascading_loop .= '<div id="'.esc_attr($uid_loop).esc_attr($ij).'" class="cascading-text elementor-repeater-item-' . $item['_id'] . ' '.esc_attr($uimg_id).' '.esc_attr($visiblity_hide).' ' . esc_attr($magic_class) . ' '.esc_attr($parallax_move).'" '.$this->get_render_attribute_string( $_tooltip ).' '.$move_parallax_attr.'>';
									$cascading_loop .= '<div class="cascading-image-inner ' . esc_attr($parallax_scroll) . '" ' . $magic_attr . '>';
										$cic_bg1 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($item['text_content_bg_image']) : '';
										$cascading_loop .= '<div class="cascading-inner-content parallax-hover-'.esc_attr($rand_no).' '.$image_effect.' '.esc_attr($reveal_effects).' '.esc_attr($link_class).' '.esc_attr($mask_image).' '.$cic_bg1.'" '.$effect_attr.'>';
											if($item['link_option']=='normal_link' || $item['link_option']=='popup_link'){
												$data_popup='';
												if($item['link_option']=='popup_link'){
													$data_popup='data-lity=""';
												}
												if($item['popup_content']['url']!='' && $item['link_option']=='popup_link'){
													$target = $item['popup_content']['is_external'] ? '' : '';
													$nofollow = $item['popup_content']['nofollow'] ? ' rel="nofollow"' : '';
													$urllink = $item['popup_content']['url'];
												}
												
												if($item['image_link']['url']!='' && $item['link_option']=='normal_link'){
													$target = $item['image_link']['is_external'] ? ' target="_blank"' : '';
													$nofollow = $item['image_link']['nofollow'] ? ' rel="nofollow"' : '';
													$urllink = $item['image_link']['url'];
												}
												$cascading_loop .= '<a href="'.esc_url($urllink).'" '.$target.$nofollow.' '.$data_popup.'>';
											}
												$cascading_loop .=$text_content;
											if($item['link_option']=='normal_link' || $item['link_option']=='popup_link'){
												$cascading_loop .= '</a>';
											}
										$cascading_loop .='</div>';
									$cascading_loop .='</div>';
								$cascading_loop .='</div>';
							//}							
						}
						if(isset($item['select_option']) && $item['select_option'] == 'lottie'){
							if($item['link_option']=='normal_link' || $item['link_option']=='popup_link'){
								$link_class="link-content";
							}else{
								$link_class='not-link-content';	
							}
							$cascading_loop .= '<div id="'.esc_attr($uid_loop).esc_attr($ij).'" class="cascading-image elementor-repeater-item-' . $item['_id'] . ' '.esc_attr($uimg_id).' '.esc_attr($visiblity_hide).' ' . esc_attr($magic_class) . ' '.esc_attr($parallax_move).'" '.$this->get_render_attribute_string( $_tooltip ).' '.$move_parallax_attr.'>';
									$cascading_loop .= '<div class="cascading-image-inner ' . esc_attr($parallax_scroll) . '" ' . $magic_attr . '>';
										$cic_bg = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($item['text_content_bg_image']) : '';
										$cascading_loop .= '<div class="cascading-inner-content parallax-hover-'.esc_attr($rand_no).' '.$image_effect.' '.esc_attr($reveal_effects).' '.esc_attr($link_class).' '.esc_attr($mask_image).' '.$cic_bg.'" '.$effect_attr.'>';
											if($item['link_option']=='normal_link' || $item['link_option']=='popup_link'){
												$data_popup='';
												if($item['link_option']=='popup_link'){
													$data_popup='data-lity=""';
												}
												if(!empty($item['popup_content']['url']) && $item['link_option']=='popup_link'){
													$target = $item['popup_content']['is_external'] ? '' : '';
													$nofollow = $item['popup_content']['nofollow'] ? ' rel="nofollow"' : '';
													$urllink = $item['popup_content']['url'];
												}
												
												if(!empty($item['image_link']['url']) && $item['link_option']=='normal_link'){
													$target = $item['image_link']['is_external'] ? ' target="_blank"' : '';
													$nofollow = $item['image_link']['nofollow'] ? ' rel="nofollow"' : '';
													$urllink = $item['image_link']['url'];
												}
												$cascading_loop .= '<a href="'.esc_url($urllink).'" '.$target.$nofollow.' '.$data_popup.'>';
											}
												$ext = pathinfo($item['lottieUrl']['url'], PATHINFO_EXTENSION);		
												if($ext!='json'){
													$cascading_loop .= '<h3 class="theplus-posts-not-found">'.esc_html__("Opps!! Please Enter Only JSON File Extension.",'theplus').'</h3>';
												}else{
													$lottieWidth = isset($settings['lottieWidth']['size']) ? $settings['lottieWidth']['size'] : 100;
													$lottieHeight = isset($settings['lottieHeight']['size']) ? $settings['lottieHeight']['size'] : 100;
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
													$cascading_loop .= '<lottie-player src="'.esc_url($item['lottieUrl']['url']).'" style="width: '.esc_attr($lottieWidth).'px; height: '.esc_attr($lottieHeight).'px;" '.esc_attr($lottieLoopValue).'  speed="'.esc_attr($lottieSpeed).'" '.esc_attr($lottieAnim).'></lottie-player>';
												}
											if($item['link_option']=='normal_link' || $item['link_option']=='popup_link'){
												$cascading_loop .= '</a>';
											}
										$cascading_loop .='</div>';
									$cascading_loop .='</div>';
							$cascading_loop .='</div>';
						}
							$inline_tippy_js='';
							if($item['plus_tooltip'] == 'yes'){
								$inline_tippy_js ='jQuery( document ).ready(function() {
								"use strict";
									if(typeof tippy === "function"){
										tippy( "#'.esc_attr($uid_loop).esc_attr($ij).'" , {
											arrowType : "'.esc_attr($tooltip_arrowtype).'",
											duration : ['.esc_attr($tooltip_duration_in).','.esc_attr($tooltip_duration_out).'],
											trigger : "'.esc_attr($tooltip_trigger).'",
											appendTo: document.querySelector("#'.esc_attr($uid_loop).esc_attr($ij).'")
										});
									}
								});';
								$cascading_loop .= wp_print_inline_script_tag($inline_tippy_js);
							}
							$rpos='auto';$bpos='auto';$ypos='auto';$xpos='auto';
								if($item['d_left_auto']=='yes'){
									if(!empty($item['d_pos_xposition']['size']) || $item['d_pos_xposition']['size']=='0'){
										$xpos=$item['d_pos_xposition']['size'].$item['d_pos_xposition']['unit'];
									}
								}
								if($item['d_top_auto']=='yes'){
									if(!empty($item['d_pos_yposition']['size']) || $item['d_pos_yposition']['size']=='0'){
										$ypos=$item['d_pos_yposition']['size'].$item['d_pos_yposition']['unit'];
									}
								}
								if($item['d_bottom_auto']=='yes'){
									if(!empty($item['d_pos_bottomposition']['size']) || $item['d_pos_bottomposition']['size']=='0'){
										$bpos=$item['d_pos_bottomposition']['size'].$item['d_pos_bottomposition']['unit'];
									}
								}
								if($item['d_right_auto']=='yes'){
									if(!empty($item['d_pos_rightposition']['size']) || $item['d_pos_rightposition']['size']=='0'){
										$rpos=$item['d_pos_rightposition']['size'].$item['d_pos_rightposition']['unit'];
									}
								}
								$d_max_width='';
								if($item['d_pos_width']['size']){
									$width=$item['d_pos_width']['size'].$item['d_pos_width']['unit'];
									$d_max_width='max-width:'.esc_attr( $width ).';';
								}
								if($item['select_option']=='image' || $item['select_option'] == 'lottie'){
									$css_loop.='.cascading-image.'.esc_attr($uimg_id).'{top:'.esc_attr($ypos).';bottom:'.esc_attr($bpos).';left:'.esc_attr($xpos).';right:'.esc_attr($rpos).';'.$d_max_width.'margin: 0 auto;}';
								}
								if($item['select_option']=='text'){
									$css_loop.='.cascading-text.'.esc_attr($uimg_id).'{top:'.esc_attr($ypos).';bottom:'.esc_attr($bpos).';left:'.esc_attr($xpos).';right:'.esc_attr($rpos).';'.$d_max_width.'margin: 0 auto;}';
								}
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
								$t_max_width='';
								if($item['t_pos_width']['size']){
									$width=$item['t_pos_width']['size'].$item['t_pos_width']['unit'];
									$t_max_width='max-width:'.esc_attr( $width ).';';
								}
								if($item['select_option']=='image' || $item['select_option']=='lottie'){
									$css_loop.='@media (min-width:601px) and (max-width:990px){.cascading-image.'.esc_attr($uimg_id).'{top:'.esc_attr($tablet_ypos).';bottom:'.esc_attr($tablet_bpos).';left:'.esc_attr($tablet_xpos).';right:'.esc_attr($tablet_rpos).';'.$t_max_width.'margin: 0 auto;}}';
								}
								if($item['select_option']=='text'){
									$css_loop.='@media (min-width:601px) and (max-width:990px){.cascading-text.'.esc_attr($uimg_id).'{top:'.esc_attr($tablet_ypos).';bottom:'.esc_attr($tablet_bpos).';left:'.esc_attr($tablet_xpos).';right:'.esc_attr($tablet_rpos).';'.$t_max_width.'margin: 0 auto;}}';
								}
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
								$m_max_width='';
								if($item['m_pos_width']['size']){
									$width=$item['m_pos_width']['size'].$item['m_pos_width']['unit'];
									$m_max_width='max-width:'.esc_attr( $width ).';';
								}
								if($item['select_option']=='image' || $item['select_option']=='lottie'){
									$css_loop.='@media (max-width:600px){.cascading-image.'.esc_attr($uimg_id).'{top:'.esc_attr($mobile_ypos).';bottom:'.esc_attr(	$mobile_bpos).';left:'.esc_attr($mobile_xpos).';right:'.esc_attr($mobile_rpos).';'.$m_max_width.'margin: 0 auto;}}';
								}
								if($item['select_option']=='text'){
									$css_loop.='@media (max-width:600px){.cascading-text.'.esc_attr($uimg_id).'{top:'.esc_attr($mobile_ypos).';bottom:'.esc_attr(	$mobile_bpos).';left:'.esc_attr($mobile_xpos).';right:'.esc_attr($mobile_rpos).';'.$m_max_width.'margin: 0 auto;}}';
								}
							}
							
						if(!empty($item['hover_parallax']) && $item['hover_parallax']=='yes'){
							$hover_tilt='hover-tilt';
						}
						$ij++;
					}
				}
			/*--------------cascading image ----------------------------*/
			
			
			$output = '<div class="pt_plus_animated_image cascading-block  wpb_single_image '.esc_attr($uid_cascading).' ' . $wrapperClass . ' '.esc_attr($cascading_move_parallax).' '.esc_attr($hover_tilt).'" '.$attr.' '.$overflow_attr.'>';
			$output .= '<div class="cascading-inner-loop ">';
				$output .=$cascading_loop;
				$output .='</div>';
			$output .='</div>';
			$css_loop='<style>'.$css_loop.'</style>';
		echo $output.$css_loop;
	}
    protected function content_template() {
	
    }
}