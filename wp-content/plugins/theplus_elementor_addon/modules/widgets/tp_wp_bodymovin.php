<?php 
/*
Widget Name: Bodymovin Animations
Description: json parse animation moving
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

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Bodymovin_Animations extends Widget_Base {
	
	public function get_name() {
		return 'tp-wp-bodymovin';
	}

    public function get_title() {
        return esc_html__('LottieFiles Animation', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-scissors theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-creatives');
    }
	
	public function get_keywords() {
		return [ 'bodymoving', 'animations', 'lottiefiles', 'bodylines'];
	}
	
    protected function register_controls() {
		
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Lottie Content', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'json_code_url',
			[
				'label' => esc_html__( 'JSON Input', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'code',
				'description' => 'Note : Download JSON file <a href="https://lottiefiles.com/14288-surfing-waveboard" class="theplus-btn" target="_blank">(example link)</a> and import It’s code/url/file at space below.',
				'options' => [
					'code'  => esc_html__( 'Code', 'theplus' ),
					'url' => esc_html__( 'URL', 'theplus' ),					
					'file' => esc_html__( 'File', 'theplus' ),					
				],
			]
		);
		$this->add_control(
			'content_parse_json_url',
			[
				'label' => esc_html__( 'JSON URL', 'theplus' ),
				'type' => Controls_Manager::URL,				
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'condition' => [
					'json_code_url' => 'url',
				],
			]
		);
		$this->add_control(
			'content_parse_json_file',
			[
				'label' => esc_html__( 'File', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'media_type' => 'application/json',
				'frontend_available' => true,
				'condition' => [
					'json_code_url' => 'file',
				],
			]
		);
		$this->add_control(
            'bm_load_backend',
            [
				'label'   => esc_html__( 'Load in Backend', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',				
			]
		);
		$this->add_control(
            'popup',
            [
				'label'   => esc_html__( 'Elementor Popup', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',				
			]
		);
		$this->add_control(
			'content_parse_json',
			[
				'label' => esc_html__( 'JSON Code', 'theplus' ),
				'type' => Controls_Manager::CODE,
				'language' => 'json',
				'rows' => 20,
				'condition' => [
					'json_code_url' => 'code',
				],
			]
		);
		
		$this->end_controls_section();
		/*extra options start*/
		$this->start_controls_section(
			'section_bm_extra_option',
			[
				'label' => esc_html__( 'Main Settings', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);		
		$this->add_control(
			'play_action_on',
			[
				'label' => __( 'Play on', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'autoplay',
				'options' => [
					''         => __( 'Default', 'theplus' ),
					'autoplay' => __( 'Auto Play', 'theplus' ),
					'hover'    => __( 'On Hover', 'theplus' ),
					'click'    => __( 'On Click', 'theplus' ),
					'column'   => __( 'Column Hover', 'theplus' ),
					'section'  => __( 'Section Hover', 'theplus' ),					
					'mouseinout'  => __( 'Mouse In-Out Effect', 'theplus' ),
					'mousescroll'  => __( 'Scroll Parallax', 'theplus' ),
					'viewport'  => __( 'View Port Based', 'theplus' ),
				],				
			]
		);
		$this->add_control(
			'loop',
			[
				'label' => esc_html__( 'Loop Animation', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'ON', 'theplus' ),
				'label_off' => esc_html__( 'OFF', 'theplus' ),
				'return_value' => 'true',
				'default' => 'true',
				'separator' => 'before',
				'condition' => [
					'play_action_on!' => '',
				],
			]
		);
		$this->add_control(
			'loop_time',
			[
				'label' => esc_html__( 'Total Loops', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 10,
				'step' => 1,
				'condition' => [
					'loop' => 'true',
				],
			]
		);
		$this->add_control(
			'speed',
			[
				'label' => esc_html__( 'Animation Play Speed', 'theplus' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0.1,
						'max' => 1,
                        'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0.5,
				],
				'condition' => [
					'play_action_on!' => ['','mousescroll','mouseinout','hover','click','column','section'],
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'bm_scrollbased',
			[
				'label' => __( 'On Scroll Animation Height', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'bm_custom',
				'options' => [
					'bm_custom' => __( 'Custom Height', 'theplus' ),
					'bm_document'  => __( 'Document Height', 'theplus' ),
				],
				'description' => __( 'Note : If you select "Document height", Animation will start and end based on whole page\'s height. In Custom height, You will be able to select offset and total height for animation.', 'theplus' ),
				'separator' => 'before',
				'condition' => [
					'play_action_on' => 'mousescroll',
				],
			]
		);
		
		$this->add_control(
			'bm_section_duration',
			[
				'label' => __( 'Duration', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 2000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 500,
				],
				'condition' => [
					'play_action_on' => 'mousescroll',
					'bm_scrollbased' => 'bm_custom',
				],
			]
		);
		$this->add_control(
			'bm_section_offset',
			[
				'label' => __( 'Offset', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition' => [
					'play_action_on' => 'mousescroll',
					'bm_scrollbased' => 'bm_custom',
				],
			]
		);
		$this->add_control(
			'bm_start_custom',
			[
				'label' => esc_html__( 'Custom Animation Start Time', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'ON', 'theplus' ),
				'label_off' => esc_html__( 'OFF', 'theplus' ),
				'condition' => [
					'play_action_on' => ['autoplay','hover','click','column','section','mouseinout','mousescroll','viewport'],
				],
			]
		);
		$this->add_control(
			'bm_start_time',
			[
				'label' => esc_html__( 'Animation Start Time', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 5000,
				'step' => 1,
				'condition' => [
					'play_action_on' => ['autoplay','hover','click','column','section','mouseinout','mousescroll','viewport'],
					'bm_start_custom' => 'yes',
				],
			]
		);
		$this->add_control(
			'bm_end_custom',
			[
				'label' => esc_html__( 'Custom Animation End Time', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'ON', 'theplus' ),
				'label_off' => esc_html__( 'OFF', 'theplus' ),
				'condition' => [
					'play_action_on' => ['autoplay','hover','click','column','section','mouseinout','mousescroll','viewport'],
				],				
			]
		);
		$this->add_control(
			'bm_end_time',
			[
				'label' => esc_html__( 'Animation End Time', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 5000,
				'step' => 1,
				'condition' => [
					'play_action_on' => ['autoplay','hover','click','column','section','mouseinout','mousescroll','viewport'],
					'bm_end_custom' => 'yes',
				],
			]
		);
		$this->add_control(
			'bm_start_end_note',
			[
				'label' => ( 'Note : You need to enter Custom Start Time and End Time from Lottiefiles Web Player. You need to use same format e.g. 30,239, 699 etc.'),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'play_action_on' => ['mouseinout','mousescroll'],					
				],
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [																
								[								
									'name'     => 'bm_start_custom','operator' => '==','value'    => 'yes',
								],
								[									
									'name'     => 'bm_end_custom','operator' => '==','value'    => 'yes',
								],
							],
						],
					],
				],
			]
		);
		$this->add_control(
			'tp_bm_link',
			[
				'label' => esc_html__( 'URL', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),				
				'default' => 'false',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'tp_bm_link_type',
			[
				'label' => esc_html__( 'Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => [
					'normal'  => esc_html__( 'Normal', 'theplus' ),
					'dynamic' => esc_html__( 'Dynamic', 'theplus' ),
				],
				'condition' => [
					'tp_bm_link' => 'yes',					
				],				
			]
		);
		$this->add_control(
			'tp_bm_link_url',
			[
				'label' => esc_html__( 'URL', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'default' => '#',
				'condition' => [
					'tp_bm_link' => 'yes',					
					'tp_bm_link_type' => 'normal',					
				],
			]
		);
		$this->add_control(
			'tp_bm_link_url_dynamic',
			[
				'label' => esc_html__( 'URL', 'theplus' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'show_external' => false,
				'default' => [
					'url' => '#',
					'is_external' => false,
					'nofollow' => false,
				],
				'condition' => [
					'tp_bm_link' => 'yes',					
					'tp_bm_link_type' => 'dynamic',					
				],
			]
		);
		$this->add_control(
			'tp_bm_link_delay',
			[
				'label' => esc_html__( 'Click Delay', 'theplus' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 10000,
                        'step' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1000,
				],
				'condition' => [
					'tp_bm_link' => 'yes',					
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'tp_bm_link_delay_note',
			[
				'label' => ( 'Note : We have added option of Delay in Click for Style “On Click”, You can add delay to finish your animation and after that link will be open.'),
				'type' => Controls_Manager::HEADING,				
			]
		);
		/*$this->add_control(
			'autoplay_viewport',
			[
				'label' => esc_html__( 'Autoplay when in Viewport', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'ON', 'theplus' ),
				'label_off' => esc_html__( 'OFF', 'theplus' ),
				'return_value' => 'true',
				'default' => 'false',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'autostop_viewport',
			[
				'label' => esc_html__( 'Autostop when out of Viewport', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'ON', 'theplus' ),
				'label_off' => esc_html__( 'OFF', 'theplus' ),
				'return_value' => 'true',
				'default' => 'false',
				'separator' => 'before',
			]
		);*/
		$this->add_control(
			'tp_bm_head',
			[
				'label' => esc_html__( 'Animation Heading', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),				
				'default' => 'false',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'tp_bm_head_text',
			[
				'label' => esc_html__( 'Heading', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 3,
				'default' => esc_html__( 'Heading', 'theplus' ),
				'placeholder' => esc_html__( 'Type your heading here', 'theplus' ),
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'tp_bm_head' => 'yes',
				],
			]
		);
		$this->add_control(
			'tp_bm_description',
			[
				'label' => esc_html__( 'Animation Description', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),				
				'default' => 'false',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'tp_bm_description_text',
			[
				'label' => esc_html__( 'Description', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 3,
				'default' => esc_html__( 'Lorem Ipsum is simply dummy text for the LottieFiles Animation. ', 'theplus' ),
				'placeholder' => esc_html__( 'Type your description here', 'theplus' ),
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'tp_bm_description' => 'yes',
				],
			]
		);
		$this->add_control(
			'anim_renderer',
			[
				'label' => esc_html__( 'Animation Renderer', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'svg',
				'options' => [
					'svg'  => esc_html__( 'SVG', 'theplus' ),
					'canvas' => esc_html__( 'Canvas', 'theplus' ),
					'html' => esc_html__( 'HTML', 'theplus' ),
				],
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/*extra options end*/
		
		$this->start_controls_section(
			'section_layout_option',
			[
				'label' => esc_html__( 'Layout Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
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
				'prefix_ class' => 'text-%s',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'max_width',
			[
				'label' => esc_html__( 'Maximum Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'separator' => 'before',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-bodymovin' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'minimum_height',
			[
				'label' => esc_html__( 'Minimum Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-bodymovin' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
		
		/*style tab*/
		/*heading start*/
		$this->start_controls_section(
            'bm_heading_style',
            [
                'label' => esc_html__('Heading Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'tp_bm_head' => 'yes',
				],
            ]
        );
		$this->add_responsive_control(
			'bm_head_align',
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
				'default' => 'left',
				'selectors'  => [
					'{{WRAPPER}} .theplus-bodymovin-hd .theplus-bodymovin-heading' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'bm_heading_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .theplus-bodymovin-hd .theplus-bodymovin-heading' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'bm_heading_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .theplus-bodymovin-hd .theplus-bodymovin-heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'bm_heading_typography',
				'selector' => '{{WRAPPER}} .theplus-bodymovin-hd .theplus-bodymovin-heading',
			]
		);
		$this->start_controls_tabs( 'bm_head_tabs' );
		$this->start_controls_tab(
			'bm_head_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),					
			]
		);
		$this->add_control(
			'bm_heading_color',
			[
				'label' => esc_html__( 'Heading Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-bodymovin-hd .theplus-bodymovin-heading' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'bm_head_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),					
			]
		);
		$this->add_control(
			'bm_heading_color_h',
			[
				'label' => esc_html__( 'Heading Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-bodymovin-hd:hover .theplus-bodymovin-heading' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*heading end*/
		
		/*description start*/
		$this->start_controls_section(
            'bm_description_style',
            [
                'label' => esc_html__('Description Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'tp_bm_description' => 'yes',
				],
            ]
        );
		$this->add_responsive_control(
			'bm_description_align',
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
				'default' => 'left',
				'selectors'  => [
					'{{WRAPPER}} .theplus-bodymovin-hd .theplus-bodymovin-description' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'bm_description_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .theplus-bodymovin-hd .theplus-bodymovin-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'bm_description_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .theplus-bodymovin-hd .theplus-bodymovin-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'bm_description_typography',
				'selector' => '{{WRAPPER}} .theplus-bodymovin-hd .theplus-bodymovin-description',
			]
		);
		$this->start_controls_tabs( 'bm_desc_tabs' );
		$this->start_controls_tab(
			'bm_desc_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),					
			]
		);
		$this->add_control(
			'bm_description_color',
			[
				'label' => esc_html__( 'Description Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-bodymovin-hd .theplus-bodymovin-description' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'bm_desc_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),					
			]
		);
		$this->add_control(
			'bm_description_color_h',
			[
				'label' => esc_html__( 'Description Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-bodymovin-hd:hover .theplus-bodymovin-description' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*description end*/
		
		/*content bg start*/
		$this->start_controls_section(
            'section_c_bg_style',
            [
                'label' => esc_html__('Content Background', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [								
								[
									'name'     => 'tp_bm_head','operator' => '==','value'    => 'yes',
								],
								[
									'name'     => 'tp_bm_description','operator' => '==','value'    => 'yes',
								],									
							],
						],
					],
				],
            ]
        );
		$this->add_responsive_control(
			'bmc_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .theplus-bodymovin-hd' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'bmc_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .theplus-bodymovin-hd' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
				'separator' => 'after',
			]
		);
		$this->start_controls_tabs( 'bmc_tabs' );
		$this->start_controls_tab(
			'bmc_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),					
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'bmc_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .theplus-bodymovin-hd',
			]
		);
		$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'bmc_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .theplus-bodymovin-hd',
					'separator' => 'before',
				]
			);
			$this->add_responsive_control(
				'bmc_border_radius',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .theplus-bodymovin-hd' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'bmc_bg_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-bodymovin-hd',
				'separator' => 'before',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'bmc_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),					
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'bmc_bg_h',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .theplus-bodymovin-hd:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'bmc_border_h',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-bodymovin-hd:hover',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'bmc_border_radius_h',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-bodymovin-hd:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
		Group_Control_Box_Shadow::get_type(),
		[
			'name' => 'bmc_bg_shadow_h',
			'label' => esc_html__( 'Box Shadow', 'theplus' ),
			'selector' => '{{WRAPPER}} .theplus-bodymovin-hd:hover',
			'separator' => 'before',
		]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();		
		$this->end_controls_section();
		/*content bg start*/
		
		/*Lottie style*/		
		$this->start_controls_section(
            'section_lottie_styling',
            [
                'label' => esc_html__('Lottie Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->start_controls_tabs( 'lottie__tabs' );
		$this->start_controls_tab(
			'lottie__normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),					
			]
		);
		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'lottie__css_n',
				'selector' => '{{WRAPPER}} .theplus-bodymovin-hd,{{WRAPPER}} .pt-plus-bodymovin',
			]
		);
		$this->add_control(
			'lottie__opacity_n',
			[
				'label' => esc_html__( 'Opacity', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 1,
				'step' => 0.1,
				'selectors' => [
					'{{WRAPPER}} .theplus-bodymovin-hd,{{WRAPPER}} .pt-plus-bodymovin' => 'opacity: {{VALUE}}',
				],
			]
		);
		$this->add_control(
            'lottie__transition',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Transition Duration', 'theplus'),				
				'range' => [
					'px' => [						
						'max'	=> 5,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-bodymovin-hd,{{WRAPPER}} .pt-plus-bodymovin' => 'transition : {{SIZE}}s',
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'lottie__hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),					
			]
		);
		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'lottie__css_h',
				'selector' => '{{WRAPPER}} .theplus-bodymovin-hd:hover,{{WRAPPER}} .pt-plus-bodymovin:hover',
			]
		);
		$this->add_control(
			'lottie__opacity_h',
			[
				'label' => esc_html__( 'Opacity', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 1,
				'step' => 0.1,
				'selectors' => [
					'{{WRAPPER}} .theplus-bodymovin-hd:hover,{{WRAPPER}} .pt-plus-bodymovin:hover' => 'opacity: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Lottie style*/
		
		/*--On Scroll View Animation ---*/
			include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation.php';

		/*style end*/
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
	}
	
	protected function tp_json_validator($data=NULL) {
	  if (!empty($data)) {				
				@json_decode($data);
				return (json_last_error() === JSON_ERROR_NONE);
		}
		return false;
	}

	 protected function render() {
        $settings = $this->get_settings_for_display();
		$style_atts = $classes = '';
		
		
		$bm_start_time=$bm_end_time='';
		if(!empty($settings['bm_start_custom']) && $settings['bm_start_custom']=='yes'){
			$bm_start_time = ($settings['bm_start_time']!='') ? $settings['bm_start_time'] : 1;
		}
		if(!empty($settings['bm_end_custom']) && $settings['bm_end_custom']=='yes'){
			$bm_end_time = ($settings['bm_end_time']!='') ? $settings['bm_end_time'] : 100;
		}
		$bm_scrollbased = (!empty($settings['bm_scrollbased'])) ? $settings['bm_scrollbased'] : 'bm_custom';
		$bm_section_duration=500;
		if(!empty($settings['bm_section_duration']['size'])){
			$bm_section_duration = $settings['bm_section_duration']['size'];
		}
		$bm_section_offset=0;
		if(!empty($settings['bm_section_offset']['size'])){
			$bm_section_offset = $settings['bm_section_offset']['size'];
		}
		
		$options=array();
		
		$anim_renderer=$settings["anim_renderer"];
		$content_align=$settings["content_align"];
		$loop =(!empty($settings['loop']) && $settings['loop']=='true') ? true : false;
		
		if((!empty($settings['loop']) && $settings['loop']=='true') && !empty($settings['loop_time'])){
			$loop = $settings['loop_time'] - 1;
		}
		
		$max_width =(!empty($settings['max_width']["size"])) ? $settings['max_width']["size"].$settings['max_width']["unit"] : '100%';		
		$minimum_height =(!empty($settings['minimum_height']["size"])) ? $settings['minimum_height']["size"].$settings['minimum_height']["unit"] : '';
		$speed =(!empty($settings['speed'])) ? $settings['speed'] : '0.5';
		
		$autoplay_viewport=$autostop_viewport=false;
		if(!empty($settings['play_action_on']) && $settings['play_action_on']=='viewport'){
			$autoplay_viewport =true;
			$autostop_viewport =true;
		}
		$play_action_on ='';
		if(!empty($settings['play_action_on'])){
			$play_action_on =$settings['play_action_on'];
		}
		
		/*--On Scroll View Animation ---*/
			include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';

		/*--Plus Extra ---*/
			$PlusExtra_Class = "";
			include THEPLUS_PATH. 'modules/widgets/theplus-widgets-extra.php';
		/*--Plus Extra ---*/

		$id=uniqid("movin");
		$uid=uniqid();
		
		$options = array(			
			'id'      => $uid,
			'container_id'      => $id,
			'autoplay_viewport' => $autoplay_viewport,
			'autostop_viewport' => $autostop_viewport,
			'loop'              => $loop,
			'width'             => $max_width,
			'height'            => $minimum_height,
			'lazyload'          => false,
			'playSpeed'          => $speed,
			'play_action' => $play_action_on,
			'bm_scrollbased' => $bm_scrollbased,
			'bm_section_duration' => $bm_section_duration,
			'bm_section_offset' => $bm_section_offset,
			'bm_start_time' => $bm_start_time,
			'bm_end_time' => $bm_end_time,
		);
		if ( !empty($settings['content_parse_json']) ) {
			//$options['animation_data'] = sanitize_textarea_field($settings['content_parse_json']);			
			$options['animation_data'] = $this->tp_json_validator($settings['content_parse_json']) ? $settings['content_parse_json'] : "Invalid";
		}
		
		if ( !empty($settings['content_parse_json_url']['url']) ) {		
			$ext = pathinfo($settings['content_parse_json_url']['url'], PATHINFO_EXTENSION);			
			if($ext!='json'){
				echo '<h3 class="theplus-posts-not-found">'.esc_html__("Opps!! Please Enter Only JSON File Extension.",'theplus').'</h3>';
				return false;
			}else{
				$get_json = wp_remote_get($settings['content_parse_json_url']['url']);
				$URL_StatusCode = wp_remote_retrieve_response_code($get_json);
				if($URL_StatusCode == 200){
					$json_code = wp_remote_retrieve_body($get_json);
					//$options['animation_data'] = sanitize_text_field($json_code);
					$options['animation_data'] = $this->tp_json_validator($json_code) ? $json_code : "Invalid";
				}
			}
		}
		
		if ( !empty($settings['content_parse_json_file']['url']) ) {		
			$ext = pathinfo($settings['content_parse_json_file']['url'], PATHINFO_EXTENSION);			
			if($ext!='json'){
				echo '<h3 class="theplus-posts-not-found">'.esc_html__("Opps!! Please Enter Only JSON File Extension.",'theplus').'</h3>';
				return false;
			}else{
				$get_json = wp_remote_get($settings['content_parse_json_file']['url']);
				$URL_StatusCode = wp_remote_retrieve_response_code($get_json);
				if($URL_StatusCode == 200){
					$json_code = wp_remote_retrieve_body($get_json);
					//$options['animation_data'] = sanitize_text_field($json_code);
					$options['animation_data'] = $this->tp_json_validator($json_code) ? $json_code : "Invalid";
				}
			}
		}

		if ( !isset( $options['autoplay_onload'] ) ) {
			$options['autoplay_onload'] = true;
		}
		if ( $settings["anim_renderer"] ) {
			$options['renderer'] = esc_attr($settings["anim_renderer"]);
		}
		
	
		if ( $content_align ) {
			$classes .= ' align-' . $content_align;
		}
		if ( !empty( $anim_renderer ) ) {
			$classes .= ' renderer-' . $anim_renderer;
		}
		
		if ( !empty( $anim_renderer ) && $anim_renderer == 'html' ) {
			$style_atts .= 'position: relative;';
		}
		if ( !empty( $content_align ) && $content_align == 'right'  ) {
			$style_atts .= 'margin-left: auto;';
		} elseif ( !empty( $content_align ) && $content_align == 'center' ) {
			$style_atts .= 'margin-right: auto;';
			$style_atts .= 'margin-left: auto;';
		}
		/*$data_attr ='';
		$data_options=json_encode($options);
			$data_attr .= ' data-body-movin-opt=\'' . $data_options . '\'';
			*/
		$settings_opt = '';
		if(!empty($settings['content_parse_json']) || !empty($settings['content_parse_json_url']['url']) || !empty($settings['content_parse_json_file']['url'])){
			if(\Elementor\Plugin::$instance->editor->is_edit_mode() || (!empty($settings['popup']) && $settings['popup']=='yes')){
				if((!empty($settings['bm_load_backend']) && $settings['bm_load_backend']=='yes') || (!empty($settings['popup']) && $settings['popup']=='yes')){
					$settings_opt =  'data-settings=\''.htmlspecialchars(wp_json_encode($options), ENT_QUOTES, 'UTF-8').'\'';
					$settings_opt .= 'data-editor-load="yes"';
					
					if((!empty($settings['popup']) && $settings['popup']=='yes')){
						$settings_opt .= 'data-popup-load="yes"';
					}
					$theplus_conn_opt = get_option( 'theplus_api_connection_data' );
					
					if( !empty($theplus_conn_opt) && !array_key_exists("bodymovin_load_js_check",$theplus_conn_opt)){
						echo '<h3 class="theplus-posts-not-found">'.esc_html__( "Make sure, Your Backend load is enabled from 'ThePlus Addons Settings -> Extra Options' as well.", "theplus" ).'</h3>';
					}
					if((!empty($settings['popup']) && $settings['popup']=='yes')){
						wp_enqueue_script( 'theplus-bodymovin' );
					}
				}else{
					$settings_opt = 'data-editor-load="no"';
					$settings_opt .= 'data-popup-load="no"';
				}
			}else{
				wp_enqueue_script( 'theplus-bodymovin' );
			}
			
			//if(empty($settings['popup'])){
				$this->render_text( $options );
			//}
			$output ='';			
			if((!empty($settings['tp_bm_link']) && $settings['tp_bm_link'] == 'yes') && ((!empty($settings['tp_bm_link_url'])) || (!empty($settings['tp_bm_link_url_dynamic']['url'])) ) && !empty($settings["tp_bm_link_delay"])){				
					
				if(!empty($settings['tp_bm_link_url_dynamic']['url'])){
					$this->add_link_attributes( 'buttondynamic', $settings['tp_bm_link_url_dynamic'] );
					$this->add_render_attribute( 'buttondynamic', 'class', 'theplus-bodymovin-link' );
					$output .='<a '.$this->get_render_attribute_string( "buttondynamic" ).'>';
				}else{
					$inline_bm_delay_js ='(function($){
						"use strict";
							$( document ).ready(function() {
								$("a.theplus-bodymovin-link").click(function (e) {
								e.preventDefault();
								var storeurl = this.getAttribute("href");
								setTimeout(function(){
									 window.location = storeurl;
								}, '.esc_attr($settings["tp_bm_link_delay"]["size"]).');
							}); 
						});
					})(jQuery);';
					$output .= wp_print_inline_script_tag($inline_bm_delay_js);
					$output .='<a class="theplus-bodymovin-link" href="'.esc_url($settings['tp_bm_link_url']).'">';
				}
				
			}
			
			if((!empty($settings['tp_bm_head']) && $settings['tp_bm_head'] == 'yes') || (!empty($settings['tp_bm_description']) && $settings['tp_bm_description'] == 'yes')){
				$lz1 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['bmc_bg_image'],$settings['bmc_bg_h_image']) : '';
				$output .='<div class="theplus-bodymovin-hd '.esc_attr($lz1).'">';
			}
			
				$output .='<div id="' . esc_attr( $id ) . '" class="pt-plus-bodymovin '.esc_attr($classes).' '.esc_attr($animated_class).'" '.$animation_attr.' style="'.$style_atts.'" '.$settings_opt.'>';
				$output .='</div>';
			
			if((!empty($settings['tp_bm_head']) && $settings['tp_bm_head'] == 'yes') || (!empty($settings['tp_bm_description']) && $settings['tp_bm_description'] == 'yes')){
				
				if(!empty($settings['tp_bm_head']) && $settings['tp_bm_head'] == 'yes'){
					$output .='<div class="theplus-bodymovin-heading">'.esc_html($settings['tp_bm_head_text']).'</div>';
				}
				if(!empty($settings['tp_bm_description']) && $settings['tp_bm_description'] == 'yes'){
					$output .='<div class="theplus-bodymovin-description">'.esc_html($settings['tp_bm_description_text']).'</div>';
				}
			$output .='</div>';
			}
				
			if(!empty($settings['tp_bm_link']) && $settings['tp_bm_link'] == 'yes'){
				$output .='</a>';
			}
			
		}else{
			$output ='<h3 class="theplus-posts-not-found">'.esc_html__( "JSON Parse Not Working", "theplus" ).'</h3>';
		}
			echo $before_content.$output.$after_content;
			
		if((!empty($settings['tp_bm_head']) && $settings['tp_bm_head'] == 'yes') || (!empty($settings['tp_bm_description']) && $settings['tp_bm_description'] == 'yes')){
			echo '<style>.theplus-bodymovin-hd{position: relative;display: block;width: 100%;-webkit-transition:all 0.5s linear;moz-transition:all 0.5s linear;-o-transition:all 0.5s linear;-ms-transition:all 0.5s linear;transition:all 0.5s linear;overflow:hidden}.theplus-bodymovin-hd .theplus-bodymovin-heading,.theplus-bodymovin-hd .theplus-bodymovin-description {position: relative;display: block;width: 100%;}.theplus-bodymovin-hd .theplus-bodymovin-heading {margin-bottom: 15px;}</style>';
		}
	}
	
	protected function render_text($options = array()) {
		$settings = $this->get_settings_for_display();
		
		if($options){	
			\Theplus_BodyMovin::plus_addAnimation($options);
		}else{
			return;
		}
	}
	
    protected function content_template() {
	
    }
}