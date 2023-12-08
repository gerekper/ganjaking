<?php	
use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Theplus_Column_Responsive extends Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init();
	}

	public function get_name() {
		return 'plus-column-responsive';
	}

	public function tp_register_controls( $element, $section_id ) {
		$plus_extras=theplus_get_option('general','extras_elements');
		
		if($element->get_name() == 'column') {
		
			$element->start_controls_section(
				'plus_column_responsive_section',
				[
					'label' => esc_html__( 'Plus Extras', 'theplus' ),
					'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
				]
			);
			if((isset($plus_extras) && empty($plus_extras)) || (!empty($plus_extras) && in_array('column_mouse_cursor',$plus_extras))){
				$element->add_control(
					'plus_column_cursor_point',
					[
						'label'        =>   esc_html__( 'Mouse Cursor Pointer', 'theplus' ),
						'type'         => \Elementor\Controls_Manager::SWITCHER,
						'label_on'     =>  esc_html__( 'Yes', 'theplus' ),
						'label_off'    =>  esc_html__( 'No', 'theplus' ),					
						'default'      => 'no',
					]
				);
				$element->add_control(
					'plus_pointer_style',
					[
						'label' => esc_html__( 'Mouse Cursor Style', 'theplus' ),
						'type' => Controls_Manager::SELECT,
						'default' => 'cursor-icon',
						'options' => [
							'cursor-icon'  => esc_html__( 'Cursor Icon', 'theplus' ),
							'follow-image' => esc_html__( 'Follow Image', 'theplus' ),
							'follow-text' => esc_html__( 'Follow Text', 'theplus' ),
						],
						'condition' => [
							'plus_column_cursor_point' => 'yes',
						],
					]
				);
				$element->add_control(
					'plus_pointer_icon',
					[
						'label' => esc_html__( 'Cursor Icon', 'theplus' ),
						'type' => Controls_Manager::MEDIA,
						'default' => [
							'url' => '',
						],
						'condition' => [
							'plus_column_cursor_point' => 'yes',
							'plus_pointer_style' => ['cursor-icon','follow-image'],
						],
					]
				);
				$element->add_control(
					'plus_pointer_iconwidth',
					[
						'label' => esc_html__( 'Icon Max Width', 'theplus' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 500,
								'step' => 1,
							],
						],
						'default' => [
							'unit' => 'px',
							'size' => 100,
						],
						'selectors' => [
							'{{WRAPPER}}.plus_column_{{ID}} .plus-cursor-pointer-follow' => 'max-width: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'plus_column_cursor_point' => 'yes',
							'plus_pointer_style' => ['follow-image'],
						],
					]
				);
				$element->add_control(
					'plus_pointer_text',
					[
						'label' => esc_html__( 'Follow Text', 'theplus' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => esc_html__( 'Follow Text', 'theplus' ),
						'placeholder' => esc_html__( 'Follow Text', 'theplus' ),
						'condition' => [
							'plus_column_cursor_point' => 'yes',
							'plus_pointer_style' => 'follow-text',
						],
					]
				);
				$element->add_control(
					'plus_pointer_left_offset',
					[
						'label' => esc_html__( 'Cursor Left Offset', 'theplus' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => -500,
								'max' => 500,
								'step' => 1,
							],
						],
						'default' => [
							'unit' => 'px',
							'size' => 10,
						],						
						'condition' => [
							'plus_column_cursor_point' => 'yes',
							'plus_pointer_style' => ['follow-text','follow-image'],
						],
					]
				);
				$element->add_control(
					'plus_pointer_top_offset',
					[
						'label' => esc_html__( 'Cursor Top Offset', 'theplus' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => -500,
								'max' => 500,
								'step' => 1,
							],
						],
						'default' => [
							'unit' => 'px',
							'size' => 10,
						],
						'condition' => [
							'plus_column_cursor_point' => 'yes',
							'plus_pointer_style' => ['follow-text','follow-image'],
						],
					]
				);
				
				$element->add_control(
					'plus_column_click_cursor',
					[
						'label'        =>  esc_html__( 'Mouse Cursor Click', 'theplus' ),
						'type'         => \Elementor\Controls_Manager::SWITCHER,
						'label_on'     =>  esc_html__( 'Yes', 'theplus' ),
						'label_off'    =>  esc_html__( 'No', 'theplus' ),					
						'default'      => 'no',
						'condition' => [
							'plus_column_cursor_point' => 'yes',
						],
					]
				);
				$element->add_control(
					'plus_pointer_click_icon',
					[
						'label' => esc_html__( 'Cursor Click Icon', 'theplus' ),
						'type' => Controls_Manager::MEDIA,
						'default' => [
							'url' => '',
						],
						'condition' => [
							'plus_column_cursor_point' => 'yes',
							'plus_column_click_cursor' => 'yes',
							'plus_pointer_style' => ['cursor-icon','follow-image'],
						],
					]
				);
				$element->add_control(
					'plus_pointer_click_text',
					[
						'label' => esc_html__( 'Click Follow Text', 'theplus' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => esc_html__( 'See More', 'theplus' ),
						'placeholder' => esc_html__( 'See More', 'theplus' ),
						'condition' => [
							'plus_column_cursor_point' => 'yes',
							'plus_column_click_cursor' => 'yes',
							'plus_pointer_style' => 'follow-text',
						],
					]
				);
				$element->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name' => 'pointer_typography',
						'selector' => '{{WRAPPER}}.plus_column_{{ID}} .plus-cursor-pointer-follow-text',
						'condition' => [
							'plus_column_cursor_point' => 'yes',
							'plus_pointer_style' => 'follow-text',
						],
						'separator' => 'before',
					]
				);
				$element->add_control(
					'pointer_text_color',
					[
						'label' => esc_html__( 'Text Color', 'theplus' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}}.plus_column_{{ID}} .plus-cursor-pointer-follow-text' => 'color: {{VALUE}}',
						],
						'condition' => [
							'plus_column_cursor_point' => 'yes',
							'plus_pointer_style' => 'follow-text',
						],
					]
				);
				$element->add_responsive_control(
					'pointer_padding',
					[
						'label' => esc_html__( 'Text Padding', 'theplus' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em' ],
						'default' =>[
							'top' => '10',
							'right' => '15',
							'bottom' => '10',
							'left' => '15',
						],
						'selectors' => [
							'{{WRAPPER}}.plus_column_{{ID}} .plus-cursor-pointer-follow-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',							
						],
						'condition' => [
							'plus_column_cursor_point' => 'yes',
							'plus_pointer_style' => 'follow-text',
						],
					]
				);
				$element->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' => 'pointer_border',
						'label' => esc_html__( 'Border', 'theplus' ),
						'selector' => '{{WRAPPER}}.plus_column_{{ID}} .plus-cursor-pointer-follow-text',
						'condition' => [
							'plus_column_cursor_point' => 'yes',
							'plus_pointer_style' => 'follow-text',
						],
					]
				);
				$element->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name' => 'pointer_background',
						'label' => esc_html__( 'Background', 'theplus' ),
						'types' => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}}.plus_column_{{ID}} .plus-cursor-pointer-follow-text',
						'condition' => [
							'plus_column_cursor_point' => 'yes',
							'plus_pointer_style' => 'follow-text',
						],
					]
				);
				$element->add_control(
					'pointer_border_radius',
					[
						'label' => esc_html__( 'Border Radius', 'theplus' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em' ],
						'selectors' => [
							'{{WRAPPER}}.plus_column_{{ID}} .plus-cursor-pointer-follow-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'plus_column_cursor_point' => 'yes',
							'plus_pointer_style' => 'follow-text',
						],
					]
				);
				$element->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'pointer_box_shadow',
						'label' => esc_html__( 'Text Box Shadow', 'theplus' ),
						'selector' => '{{WRAPPER}}.plus_column_{{ID}} .plus-cursor-pointer-follow-text',
						'condition' => [
							'plus_column_cursor_point' => 'yes',
							'plus_pointer_style' => 'follow-text',
						],
					]
				);
				
				
			}
			if((isset($plus_extras) && empty($plus_extras)) || (!empty($plus_extras) && in_array('column_sticky',$plus_extras))){
				$element->add_control(
					'plus_column_sticky',
					[
						'label'        =>  esc_html__( 'Sticky Column', 'theplus' ),
						'type'         => \Elementor\Controls_Manager::SWITCHER,
						'label_on'     =>  esc_html__( 'Yes', 'theplus' ),
						'label_off'    =>  esc_html__( 'No', 'theplus' ),
						'return_value' => 'true',
						'default'      => 'false',
						'separator'		=> 'before',
					]
				);

				$element->add_control(
					'plus_sticky_top_spacing',
					[
						'label'   =>  esc_html__( 'Top Spacing', 'theplus' ),
						'type'    => \Elementor\Controls_Manager::NUMBER,
						'default' => 40,
						'min'     => 0,
						'max'     => 500,
						'step'    => 1,
						'condition' => [
							'plus_column_sticky' => 'true',
						],
					]
				);

				$element->add_control(
					'plus_sticky_bottom_spacing',
					[
						'label'   =>  esc_html__( 'Bottom Spacing', 'theplus' ),
						'type'    => \Elementor\Controls_Manager::NUMBER,
						'default' => 40,
						'min'     => 0,
						'max'     => 500,
						'step'    => 1,
						'condition' => [
							'plus_column_sticky' => 'true',
						],
					]
				);
				$element->add_responsive_control(
					'plus_sticky_padding',
					[
						'label' => esc_html__( 'Padding', 'theplus' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em' ],
						'selectors' => [
							'{{WRAPPER}} .inner-wrapper-sticky' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'plus_column_sticky' => 'true',
						],
					]
				);
				$element->add_control(
					'plus_sticky_enable_desktop',
					[
						'label' => esc_html__( 'Sticky Desktop', 'theplus' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_on' => esc_html__( 'Enable', 'theplus' ),
						'label_off' => esc_html__( 'Disable', 'theplus' ),					
						'default' => 'yes',
						'condition' => [
							'plus_column_sticky' => 'true',
						],
					]
				);
				$element->add_control(
					'plus_sticky_enable_tablet',
					[
						'label' => esc_html__( 'Sticky Tablet', 'theplus' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_on' => esc_html__( 'Enable', 'theplus' ),
						'label_off' => esc_html__( 'Disable', 'theplus' ),					
						'default' => 'yes',
						'condition' => [
							'plus_column_sticky' => 'true',
						],
					]
				);
				$element->add_control(
					'plus_sticky_enable_mobile',
					[
						'label' => esc_html__( 'Sticky Mobile', 'theplus' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_on' => esc_html__( 'Enable', 'theplus' ),
						'label_off' => esc_html__( 'Disable', 'theplus' ),					
						'default' => 'no',
						'condition' => [
							'plus_column_sticky' => 'true',
						],
					]
				);
			}
			if((isset($plus_extras) && empty($plus_extras)) || (!empty($plus_extras) && in_array('order_sort_column',$plus_extras))){
				$element->add_responsive_control(
					'plus_column_width',
					[
						'label' => esc_html__( 'Column Width', 'theplus' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'label_block' => true,
						'description' => 'E.g. 300px, 40%, calc(100%-400px)',					
						'selectors' => [
							'{{WRAPPER}}' => 'width: {{VALUE}} !important;',
						],
						'separator' => 'before',
					]
				);
				$element->add_responsive_control(
					'plus_column_order',
					[
						'label' => esc_html__( 'Column Order', 'theplus' ),
						'type' => \Elementor\Controls_Manager::NUMBER,
						'min' => 0,
						'max' => 20,
						'step' => 1,
						'default' => '',
						'separator' => 'before',
						'description' => 'E.g. 0,1,2,3,etc.',
						'selectors' => [
							'{{WRAPPER}}' => 'order: {{VALUE}}',
						],
					]
				);
			}
			if((isset($plus_extras) && empty($plus_extras)) || (!empty($plus_extras) && in_array('custom_width_column',$plus_extras))){
				$element->add_control(
					'plus_responsive_column_heading',
					[
						'label' => esc_html__( 'Responsive Options for Breakpoints', 'theplus' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);
				$repeater = new \Elementor\Repeater();
				$repeater->add_control(
					'plus_media_max_width',
					[
						'label' => esc_html__( '@Media Max-Width Value', 'theplus' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'default' => 'no',
						'label_on' => 'Yes',
						'label_off' => 'No',
						'return_value' => 'yes',
					]
				);
				$repeater->add_control(
					'media_max_width',
					[
						'label' => esc_html__( 'Select Max-Width Value(px)', 'theplus' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 3000,
								'step' => 1,
							],
						],
						'default' => [
							'unit' => 'px',
							'size' => 0,
						],
						'condition' => [
							'plus_media_max_width' => 'yes',
						],
					]
				);
				$repeater->add_control(
					'plus_media_min_width',
					[
						'label' => esc_html__( '@Media Min-Width Value', 'theplus' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'default' => 'no',
						'label_on' => 'Yes',
						'label_off' => 'No',
						'return_value' => 'yes',
					]
				);
				$repeater->add_control(
					'media_min_width',
					[
						'label' => esc_html__( 'Select Min-Width Value(px)', 'theplus' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 3000,
								'step' => 1,
							],
						],
						'default' => [
							'unit' => 'px',
							'size' => 0,
						],
						'condition' => [
							'plus_media_min_width' => 'yes',
						],
					]
				);
				
				$repeater->add_control(
					'plus_column_width',
					[
						'label' => esc_html__( 'Column Width', 'theplus' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'label_block' => true,
						'description' => 'E.g. 300px, 40%, calc(100%-400px)',
						'separator' => 'before',
					]
				);
				$repeater->add_responsive_control(
					'plus_column_margin',
					[
						'label' => esc_html__( 'Margin', 'theplus' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em' ],
						'selectors' => [
							'{{WRAPPER}} .your-class' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
				$repeater->add_responsive_control(
					'plus_column_padding',
					[
						'label' => esc_html__( 'Padding', 'theplus' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em' ],
						'selectors' => [
							'{{WRAPPER}} .your-class' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
				$repeater->add_control(
					'plus_column_hide',
					[
						'label' => esc_html__( 'Column Visibility', 'theplus' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'default' => '',
						'label_on' => 'Hide',
						'label_off' => 'Show',
						'return_value' => 'yes',
						'separator' => 'before',
					]
				);
				$repeater->add_control(
					'plus_column_order',
					[
						'label' => esc_html__( 'Column Order', 'theplus' ),
						'type' => \Elementor\Controls_Manager::NUMBER,
						'min' => 0,
						'max' => 20,
						'step' => 1,
						'default' => '',
						'separator' => 'before',
						'description' => 'E.g. 0,1,2,3,etc.',
					]
				);
				$element->add_control(
					'plus_column_responsive_list',
					[
						'type'    => \Elementor\Controls_Manager::REPEATER,
						'fields'  => $repeater->get_controls(),
						'title_field' => 'Min: {{{plus_media_min_width}}} - Max: {{{plus_media_min_width}}}',
					]
				);
			}
			if((isset($plus_extras) && empty($plus_extras)) || (!empty($plus_extras) && in_array('column_custom_css',$plus_extras))){			
				$element->add_control(
					'plus_custom_css',
					[
						'label' => esc_html__( 'Custom CSS', 'theplus' ),
						'type' => \Elementor\Controls_Manager::CODE,
						'language' => 'css',
						'render_type' => 'ui',					
						'separator' => 'none',
						'rows' => 20,
						'separator' => 'before',
					]
				);
			}
			$element->end_controls_section();
		}
	}
	public function section_register_controls( $element, $section_id ) {
		$plus_extras=theplus_get_option('general','extras_elements');
		
		if($element->get_name() == 'section' || $element->get_name() == 'container') {
		
			$element->start_controls_section(
				'plus_section_responsive_section',
				[
					'label' => esc_html__( 'Plus Extras', 'theplus' ),
					'tab' => Controls_Manager::TAB_ADVANCED,
				]
			);
			if((isset($plus_extras) && empty($plus_extras)) || (!empty($plus_extras) && in_array('section_scroll_animation',$plus_extras))){
				$element->add_control(
					'plus_section_scroll_animation_in',
					[
						'label' => esc_html__( 'In/Entrance Animation', 'theplus' ),
						'type' => Controls_Manager::SELECT,
						'default' => 'none',
						'options' => [
							'none'  => esc_html__( 'None', 'theplus' ),						
							'scale-smaller' => esc_html__( 'Scale smaller', 'theplus' ),
							'fade' => esc_html__( 'Fade in', 'theplus' ),
							'rotate-back' => esc_html__( '3D Rotate backward', 'theplus' ),
							'rotate-forward' => esc_html__( '3D Rotate forward', 'theplus' ),
							'carousel' => esc_html__( 'Carousel forward', 'theplus' ),
							'fly-up' => esc_html__( 'Fly up', 'theplus' ),
							'fly-left' => esc_html__( 'Fly left', 'theplus' ),
							'fly-right' => esc_html__( 'Fly right', 'theplus' ),
							'cube' => esc_html__( '3D cube (use 3D cube entrance animation on the next row to make this look good)', 'theplus' ),
						],
					]
				);
				$element->add_control(
					'plus_section_scroll_animation_out',
					[
						'label' => esc_html__( 'Exit Animation', 'theplus' ),
						'type' => Controls_Manager::SELECT,
						'default' => 'none',
						'options' => [						
							'none' => esc_html__( 'None', 'theplus' ),						
							'scale-smaller' => esc_html__( 'Scale smaller', 'theplus' ),
							'fade' => esc_html__( 'Fade out', 'theplus' ),
							'rotate-back' => esc_html__( '3D Rotate backward', 'theplus' ),
							'rotate-forward' => esc_html__( '3D Rotate forward', 'theplus' ),
							'carousel' => esc_html__( 'Carousel forward', 'theplus' ),
							'fly-up' => esc_html__( 'Fly up', 'theplus' ),
							'fly-left' => esc_html__( 'Fly left', 'theplus' ),
							'fly-right' => esc_html__( 'Fly right', 'theplus' ),
							'cube' => esc_html__( '3D cube (use 3D cube entrance animation on the next row to make this look good)', 'theplus' ),
						],
					]
				);
				$element->add_control(
					'plus_section_scroll_in_delay',
					[
						'label' => esc_html__( 'Entrance Delay', 'theplus' ),
						'type' => Controls_Manager::NUMBER,
						'min' => -20,
						'max' => 20,
						'step' => 1,
						'default' => '',
						'description' => esc_html__( 'Add any value from -20 to 20. Tip: Positive numbers lengthen the entrance animation duration, and negative numbers shorten it.','theplus' ),
						'condition' => [
							'plus_section_scroll_animation_in' => ['fly-up','fly-left','fly-right','scale-smaller','fade','rotate-forward','rotate-back'],
						],
					]
				);
				$element->add_control(
					'plus_section_scroll_out_delay',
					[
						'label' => esc_html__( 'Exit Delay', 'theplus' ),
						'type' => Controls_Manager::NUMBER,
						'min' => -20,
						'max' => 20,
						'step' => 1,
						'default' => '',
						'description' => esc_html__( 'The exit animation triggers when your container reaches more than 1/3th of the screen from the top (some effects are different, some are unchangeable). Add any value from -20 to 20.(Tip: Positive numbers lengthen the entrance animation duration, and negative numbers shorten it.)','theplus' ),
						'condition' => [
							'plus_section_scroll_animation_out' => ['fly-up','fly-left','fly-right','scale-smaller','fade','rotate-forward','rotate-back'],
						],
					]
				);
				$element->add_control(
					'plus_section_scroll_overflow',
					[
						'label' => esc_html__( 'Fix Body Overflow', 'theplus' ),
						'type' => Controls_Manager::SWITCHER,
						'label_on' => esc_html__( 'Yes', 'theplus' ),
						'label_off' => esc_html__( 'No', 'theplus' ),
						'return_value' => 'yes',
						'default' => 'no',
						'description' => esc_html__('Check this if you see an unwanted scrollbar that shows up for a very short time while scrolling with some effects & some themes.', 'theplus' ),
					]
				);
				$element->add_control(
					'plus_section_scroll_mobile_disable',
					[
						'label' => esc_html__( 'Mobile Disable/Off', 'theplus' ),
						'type' => Controls_Manager::SWITCHER,
						'label_on' => esc_html__( 'Yes', 'theplus' ),
						'label_off' => esc_html__( 'No', 'theplus' ),
						'return_value' => 'yes',
						'default' => 'no',
						'description' => esc_html__('Note : If you disable this option, It will be disabled for all sections of page.', 'theplus' ),
					]
				);
			}
			if(isset($plus_extras) && (empty($plus_extras) || (!empty($plus_extras) && in_array('section_custom_css',$plus_extras)))){
				$element->add_control(
					'plus_section_overflow_hidden',
					[
						'label' => esc_html__( 'Section Overflow Hidden', 'theplus' ),
						'type' => Controls_Manager::SWITCHER,
						'label_on' => esc_html__( 'Yes', 'theplus' ),
						'label_off' => esc_html__( 'No', 'theplus' ),
						'return_value' => 'yes',
						'default' => 'no',
						'description' => esc_html__('If, Check Yes section scrollbar Overflow Hidden.', 'theplus' ),
						'separator' => 'before',
					]
				);
				$element->add_control(
					'plus_custom_css',
					[
						'label' => esc_html__( 'Custom CSS', 'theplus' ),
						'type' => \Elementor\Controls_Manager::CODE,
						'language' => 'css',
						'render_type' => 'ui',
						'separator' => 'none',
						'rows' => 20,
						'separator' => 'before',
					]
				);
			}
			$element->end_controls_section();
		}
	}
	
	public function before_render_element($element) {
		$settings = $element->get_settings();
		$column_wrapper_id= $element->get_id();
		$post_id = get_the_ID();				
		$data     = $element->get_data();
		$type     = isset( $data['elType'] ) ? $data['elType'] : 'column';
		//$settings = $data['settings'];
		$plus_extras=theplus_get_option('general','extras_elements');
		
		if ( 'column' !== $type ) {
			return;
		}
		
		if((isset($plus_extras) && empty($plus_extras)) || (!empty($plus_extras) && in_array('column_mouse_cursor',$plus_extras))){
		
			if ( isset( $settings['plus_column_cursor_point'] ) && $settings['plus_column_cursor_point']=='yes') {
			
				$cursor_pointer = array();				
				$cursor_pointer['style'] = (!empty($settings['plus_pointer_style'])) ? $settings['plus_pointer_style'] : '';
				if(!empty($settings['plus_pointer_style']) && ($settings['plus_pointer_style'] =='cursor-icon' || $settings['plus_pointer_style'] =='follow-image' ) ){				
					$cursor_pointer['cursor_icon'] = (!empty($settings['plus_pointer_icon']['url'])) ? $settings['plus_pointer_icon']['url'] : '';
					
					if( !empty($settings['plus_column_click_cursor']) && $settings['plus_column_click_cursor'] == 'yes' && !empty($settings['plus_pointer_click_icon']['url']) ){
					
						$cursor_pointer['cursor_see_more'] = 'yes';
						$cursor_pointer['cursor_see_icon'] = (!empty($settings['plus_pointer_click_icon']['url'])) ? $settings['plus_pointer_click_icon']['url'] : '';
						$cursor_pointer['cursor_adjust_left'] = (!empty($settings["plus_pointer_left_offset"]["size"])) ? $settings["plus_pointer_left_offset"]["size"] : 0;
						$cursor_pointer['cursor_adjust_top'] = (!empty($settings["plus_pointer_top_offset"]["size"])) ? $settings["plus_pointer_top_offset"]["size"] : 0;
					}
				}else if(!empty($settings['plus_pointer_style']) && $settings['plus_pointer_style'] =='follow-text'){
					$cursor_pointer['cursor_text'] = (!empty($settings['plus_pointer_text'])) ? $settings['plus_pointer_text'] : '';
					
					if( !empty($settings['plus_column_click_cursor']) && $settings['plus_column_click_cursor'] == 'yes' && !empty($settings['plus_pointer_click_text']) ){
					
						$cursor_pointer['cursor_see_more'] = 'yes';
						$cursor_pointer['cursor_see_text'] = (!empty($settings['plus_pointer_click_text'])) ? $settings['plus_pointer_click_text'] : '';
						$cursor_pointer['cursor_adjust_left'] = (!empty($settings["plus_pointer_left_offset"]["size"])) ? $settings["plus_pointer_left_offset"]["size"] : 0;
						$cursor_pointer['cursor_adjust_top'] = (!empty($settings["plus_pointer_top_offset"]["size"])) ? $settings["plus_pointer_top_offset"]["size"] : 0;
						
					}
					
				}
				if(!empty($settings['plus_pointer_style'])){
					$element->add_render_attribute( '_wrapper', [
						'class' => 'plus_cursor_pointer plus_column_'.$element->get_id().' plus-'.$settings['plus_pointer_style'],
						'data-plus-cursor-settings' => json_encode( $cursor_pointer ),
					] );
				}
			}
			
		}
		
			if((isset($plus_extras) && empty($plus_extras)) || (!empty($plus_extras) && in_array('column_sticky',$plus_extras))){
			if ( isset( $settings['plus_column_sticky'] ) && $settings['plus_column_sticky']=='true') {
				
				$array_enable=array();
				if(isset($settings['plus_sticky_enable_desktop']) && $settings['plus_sticky_enable_desktop']=='yes'){
					$array_enable[]= 'desktop';
				}
				if(isset($settings['plus_sticky_enable_tablet']) && $settings['plus_sticky_enable_tablet']=='yes'){
					$array_enable[]= 'tablet';
				}
				if(isset($settings['plus_sticky_enable_mobile']) && $settings['plus_sticky_enable_mobile']=='yes'){
					$array_enable[]= 'mobile';
				}
				
				$column_settings = array(
					'id'            => $data['id'],
					'sticky'        => filter_var( $settings['plus_column_sticky'], FILTER_VALIDATE_BOOLEAN ),
					'topSpacing'    => isset( $settings['plus_sticky_top_spacing'] ) ? $settings['plus_sticky_top_spacing'] : 40,
					'bottomSpacing' => isset( $settings['plus_sticky_bottom_spacing'] ) ? $settings['plus_sticky_bottom_spacing'] : 40,
					'stickyOn'      => !empty( $array_enable ) ? $array_enable : array( 'desktop', 'tablet' ),
				);

				if ( filter_var( $settings['plus_column_sticky'], FILTER_VALIDATE_BOOLEAN ) ) {

					$element->add_render_attribute( '_wrapper', array(
						'class' => 'plus-sticky-column-sticky',
						'data-plus-sticky-column-settings' => json_encode( $column_settings ),
					) );
				}

				$this->columns_data[ $data['id'] ] = $column_settings;
			}
		}
		
		if((isset($plus_extras) && empty($plus_extras)) || (!empty($plus_extras) && in_array('custom_width_column',$plus_extras))){
			if ( array_key_exists( 'plus_column_responsive_list',$settings ) ) {
				$list = $settings['plus_column_responsive_list'];	
				if( !empty($list[0]['media_max_width']['size']) || !empty($list[0]['media_min_width']['size']) ) {

					$media_query = '@media ';
					
					$index = 0;
					$style=$max_width=$min_width=$betwn_and='';
					foreach ($list as $item) {
						$index++;
						$max_width=$min_width=$betwn_and='';
						if(!empty($item['media_max_width']['size']) && !empty($item["plus_media_max_width"]) && $item["plus_media_max_width"]=='yes') {
							$max_width = '(max-width: '.$item['media_max_width']['size'].$item['media_max_width']['unit'].') ';
						}
						if(!empty($item['media_min_width']['size']) && !empty($item["plus_media_min_width"]) && $item["plus_media_min_width"]=='yes') {
							$min_width = ' (min-width: '.$item['media_min_width']['size'].$item['media_min_width']['unit'].') ';
						}
						if(!empty($item['media_max_width']['size']) && !empty($item['media_min_width']['size']) && $item["plus_media_max_width"]=='yes' && $item["plus_media_min_width"]=='yes'){
							$betwn_and =' and ';
						}
						$style .= $media_query . $max_width . $betwn_and . $min_width .'{';
							$style .= '.elementor-element.elementor-column.elementor-element-'.$column_wrapper_id.'{';
							if(!empty($item['plus_column_width'])){
								$style .= 'width : '. $item['plus_column_width'].' !important;';
							}
							if(!empty($item['plus_column_hide'])){
								$style .= 'display : none;';
							}
							if(!empty($item['plus_column_order'])){
								$style .= 'order : '.$item['plus_column_order'].';';
							}
							
							$style .= '}';
							$style .= '.elementor-element.elementor-column.elementor-element-'.$column_wrapper_id.' > .elementor-column{';
							if(!empty($item['plus_column_margin']["top"]) || !empty($item['plus_column_margin']["right"]) || !empty($item['plus_column_margin']["bottom"]) || !empty($item['plus_column_margin']["left"])){
								$top_margin = ($item['plus_column_margin']["top"]) ? $item['plus_column_margin']["top"].$item['plus_column_margin']["unit"] : 0;
								$right_margin = ($item['plus_column_margin']["right"]) ? $item['plus_column_margin']["right"].$item['plus_column_margin']["unit"] : 0;
								$bottom_margin = ($item['plus_column_margin']["bottom"]) ? $item['plus_column_margin']["bottom"].$item['plus_column_margin']["unit"] : 0;
								$left_margin = ($item['plus_column_margin']["left"]) ? $item['plus_column_margin']["left"].$item['plus_column_margin']["unit"] : 0;
								
								$style .= 'margin : '.$top_margin.' '.$right_margin.' '.$bottom_margin.' '.$left_margin.' !important;';
							}
							if(!empty($item['plus_column_padding']["top"]) || !empty($item['plus_column_padding']["right"]) || !empty($item['plus_column_padding']["bottom"]) || !empty($item['plus_column_padding']["left"])){
								$top_padding = ($item['plus_column_padding']["top"]) ? $item['plus_column_padding']["top"].$item['plus_column_padding']["unit"] : 0;
								$right_padding = ($item['plus_column_padding']["right"]) ? $item['plus_column_padding']["right"].$item['plus_column_padding']["unit"] : 0;
								$bottom_padding = ($item['plus_column_padding']["bottom"]) ? $item['plus_column_padding']["bottom"].$item['plus_column_padding']["unit"] : 0;
								$left_padding = ($item['plus_column_padding']["left"]) ? $item['plus_column_padding']["left"].$item['plus_column_padding']["unit"] : 0;
								
								$style .= 'padding : '.$top_padding.' '.$right_padding.' '.$bottom_padding.' '.$left_padding.' !important;';
							}
							$style .= '}';
						
						$style .= '}';
						
					}
					if(!empty($style)){
						echo '<style>'.$style.'</style>';
					}
				}
			}
		}
	}
	
	/**
	 * Before Section Render
	 *
	 */
	public function plus_before_render_section($element) {
		$settings = $element->get_settings();
		//$settings = $element->get_settings_for_display();
		$scroll_animation_in = $settings['plus_section_scroll_animation_in'];
		$scroll_animation_out = $settings['plus_section_scroll_animation_out'];
		$in_delay = ($settings['plus_section_scroll_in_delay']!='') ? $settings['plus_section_scroll_in_delay'] : '-5';
		$out_delay = ($settings['plus_section_scroll_out_delay']!='') ? $settings['plus_section_scroll_out_delay'] : '-5';
		$overflow_section= ($settings['plus_section_scroll_overflow']=='yes') ? 'true' : 'false';
		$mobile_disable= ($settings['plus_section_scroll_mobile_disable']=='yes') ? 'true' : 'false';
		
		$plus_extras=theplus_get_option('general','extras_elements');
		
		if(isset($plus_extras) && (empty($plus_extras) || (!empty($plus_extras) && in_array('section_custom_css',$plus_extras)))){
			
			$section_overflow_hidden= ($settings['plus_section_overflow_hidden']=='yes') ? 'yes' : 'no';			
			if($section_overflow_hidden=='yes'){
				$element->add_render_attribute( '_wrapper', [
					'class' => 'plus_row_scroll_overflow',
				] );
			}
		}
		if($scroll_animation_in != 'none' || $scroll_animation_out != 'none' ){			
			$element->add_render_attribute( '_wrapper', [
				'class' => 'plus_row_scroll',
				'data-row-exit' => $scroll_animation_out,
				'data-row-entrance' => $scroll_animation_in,
				'data-body-overflow' => $overflow_section,
				'data-row-mobile-off' => $mobile_disable,
			] );
			//cube out
			if($scroll_animation_out=='cube'){
				$element->add_render_attribute( '_wrapper', [
				'data-5p-top-bottom' => 'transform: perspective(1000px) scale(1) rotateX(91deg) translateX(0vw) translateY(0vh);transform-origin: !50% 100%;',
				'data-20p-center-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);transform-origin: !50% 100%;',
				] );
			}
			//fly-up out
			if($scroll_animation_out=='fly-up'){
				$element->add_render_attribute( '_wrapper', [
				'data-top-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(-30vh);',
				'data-'.$out_delay.'p-center-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);',
				] );
			}
			//fly-left out
			if($scroll_animation_out=='fly-left'){
				$element->add_render_attribute( '_wrapper', [
				'data-top-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(-100vw) translateY(0vh);',
				'data-'.$out_delay.'p-center-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);',
				] );
			}
			//fly-right out
			if($scroll_animation_out=='fly-right'){
				$element->add_render_attribute( '_wrapper', [
				'data-top-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(100vw) translateY(0vh);opacity: 0;',
				'data-'.$out_delay.'p-center-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);opacity: 1;',
				] );
			}
			//scale-smaller out
			if($scroll_animation_out=='scale-smaller'){
				$element->add_render_attribute( '_wrapper', [
				'data-top-bottom' => 'transform: perspective(1000px) scale(0.7) rotateX(0deg) translateX(0vw)  translateY(0vh);transform-origin: !50% 100%;opacity: 0.4;',
				'data-'.$out_delay.'p-center-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw)  translateY(0vh);transform-origin: !50% 100%;opacity: 1;',
				] );
			}
			//fade out
			if($scroll_animation_out=='fade'){
				$element->add_render_attribute( '_wrapper', [
				'data-top-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);opacity: 0.0;',
				'data-'.$out_delay.'p-center-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);opacity: 1;',
				] );
			}
			//carousel out
			if($scroll_animation_out=='carousel'){
				$element->add_render_attribute( '_wrapper', [
				'data-top-bottom' => 'transform: perspective(1000px) scale(0.4) rotateX(0deg) translateX(0vw) translateY(50vh);opacity: 1;z-index: 0;',
				'data--1-top-bottom' => 'transform: perspective(1000px) scale(0.4) rotateX(0deg) translateX(0vw) translateY(50vh);opacity: 0;z-index: 0;',
				'data--20p-center-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);opacity: 1;z-index: 0;',
				'data--19.999p-center-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);opacity: 1;z-index: 1;',
				] );
			}
			//stick out
			if($scroll_animation_out=='stick'){
				$element->add_render_attribute( '_wrapper', [
				'data--1-top-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(50vh);transform-origin: !50% 0%;opacity: 0;z-index: 1;',
				'data-top-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(50vh);transform-origin: !50% 0%;opacity: 1;z-index: 1;',
				'data-center-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);transform-origin: !50% 0%;opacity: 1;z-index: 1;',
				'data-smooth-scrolling' => 'off',
				] );
			}
			
			//stick-fly-left out
			if($scroll_animation_out=='stick-fly-left'){
				$element->add_render_attribute( '_wrapper', [
				'data-top-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(-100vw) translateY(100vh);z-index: 2;opacity: 1;',
				'data-bottom-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);z-index: 2;opacity: 1;',
				'data-1-bottom-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);z-index: 1;opacity: 1;',
				] );
			}
			
			//stick-fly-right out
			if($scroll_animation_out=='stick-fly-right'){
				$element->add_render_attribute( '_wrapper', [
				'data-top-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(100vw) translateY(100vh);z-index: 2;transform-origin: !50% 50%;opacity: 1;',
				'data-bottom-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);z-index: 2;transform-origin: !50% 50%;opacity: 1;',
				'data-1-bottom-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);z-index: 1;transform-origin: !50% 50%;opacity: 1;',				
				'data-smooth-scrolling-exit' => "off",
				] );
			}
			//stick-fly-down out
			if($scroll_animation_out=='stick-fly-down'){
				$element->add_render_attribute( '_wrapper', [
				'data-top-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(200%);z-index: 2;opacity: 1;transform-origin: !50% 50%;',
				'data-bottom-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);z-index: 2;opacity: 1;transform-origin: !50% 50%;',
				'data-1-bottom-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);z-index: 1;opacity: 1;transform-origin: !50% 50%;',				
				'data--1-top-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);z-index: 1;opacity: 0;transform-origin: !50% 50%;',				
				] );
			}
			//rotate-forward out
			if($scroll_animation_out=='rotate-forward'){
				$element->add_render_attribute( '_wrapper', [
				'data-top-bottom' => 'transform: perspective(1000px) scale(1) rotateX(-70deg) translateX(0vw) translateY(0vh);transform-origin: !50% 100%;opacity: 1;',
				'data-'.$out_delay.'p-center-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);transform-origin: !50% 100%;opacity: 1;',
				] );
			}
			//rotate-back out
			if($scroll_animation_out=='rotate-back'){
				$element->add_render_attribute( '_wrapper', [
				'data-top-bottom' => 'transform: perspective(1000px) scale(1) rotateX(70deg) translateX(0vw) translateY(0vh);transform-origin: !50% 100%;',
				'data-'.$out_delay.'p-center-bottom' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);transform-origin: !50% 100%;',			
				] );
			}
			
			
			
			//cube in
			if($scroll_animation_in=='cube'){
				$element->add_render_attribute( '_wrapper', [
				'data--20p-center-top' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);transform-origin: !50% 0%;',
				'data--5p-bottom-top' => 'transform: perspective(1000px) scale(1) rotateX(-91deg) translateX(0vw) translateY(0vh);transform-origin: !50% 0%;',
				] );
			}			
			//fly-up in
			if($scroll_animation_in=='fly-up'){
				$element->add_render_attribute( '_wrapper', [
				'data-'.$in_delay.'p-center-top' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);transform-origin: !50% 100%;',
				'data-bottom-top' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(30vh);transform-origin: !50% 100%;',
				] );
			}
			//fly-left in
			if($scroll_animation_in=='fly-left'){
				$element->add_render_attribute( '_wrapper', [
				'data-'.$in_delay.'p-center-top' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw)  translateY(0vh);',
				'data-bottom-top' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(100vw)  translateY(0vh);',
				] );
			}
			//fly-right in
			if($scroll_animation_in=='fly-right'){
				$element->add_render_attribute( '_wrapper', [
				'data-'.$in_delay.'p-center-top' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw)  translateY(0vh);opacity: 1;',
				'data-bottom-top' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(-100vw)  translateY(0vh);opacity: 1;',
				] );
			}
			//scale-smaller in
			if($scroll_animation_in=='scale-smaller'){
				$element->add_render_attribute( '_wrapper', [
				'data-'.$in_delay.'p-center-top' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);transform-origin: !50% 0%;opacity: 1;',
				'data-bottom-top' => 'transform: perspective(1000px) scale(1.2) rotateX(0deg) translateX(0vw) translateY(0vh);transform-origin: !50% 0%;opacity: 0.4;',
				] );
			}
			//fade in
			if($scroll_animation_in=='fade'){
				$element->add_render_attribute( '_wrapper', [
				'data-'.$in_delay.'p-center-top' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);transform-origin: !50% 50%;opacity: 1;',
				'data-bottom-top' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);transform-origin: !50% 100%;transform-origin: !50% 50%;opacity: 0.0;',
				] );
			}
			
			//carousel in
			if($scroll_animation_in=='carousel'){
				$element->add_render_attribute( '_wrapper', [
				'data-20p-center-top' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);opacity: 1;z-index: 0;',
				'data-19.9999p-center-top' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);opacity: 1;z-index: 1;',
				'data-bottom-top' => 'transform: perspective(1000px) scale(0.4) rotateX(0deg) translateX(0vw) translateY(-50vh);opacity: 1;z-index: 1;',
				'data-1-bottom-top' => 'transform: perspective(1000px) scale(0.4) rotateX(0deg) translateX(0vw) translateY(-50vh);opacity: 0;z-index: 0;',
				] );
			}
			
			//stick in
			if($scroll_animation_in=='stick'){
				$element->add_render_attribute( '_wrapper', [
				'data-1-top-top' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);transform-origin: !50% 0%;opacity: 1;z-index: 1;',
				'data-2-top-top' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);transform-origin: !50% 0%;opacity: 1;z-index: 0;',
				'data-bottom-top' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(-100vh);transform-origin: !50% 0%;opacity: 1;z-index: 0;',
				'data-1-bottom-top' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);transform-origin: !50% 0%;opacity: 0;z-index: 0;',
				'data-smooth-scrolling' => 'off',
				] );
			}
			
			
			//rotate-forward in
			if($scroll_animation_in=='rotate-forward'){
				$element->add_render_attribute( '_wrapper', [
				'data-'.$in_delay.'p-center-top' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);transform-origin: !50% 0%;',
				'data-bottom-top' => 'transform: perspective(1000px) scale(1) rotateX(-70deg) translateX(0vw) translateY(0vh);transform-origin: !50% 0%;',			
				] );
			}
			
			//rotate-back in
			if($scroll_animation_in=='rotate-back'){
				$element->add_render_attribute( '_wrapper', [
				'data-'.$in_delay.'p-center-top' => 'transform: perspective(1000px) scale(1) rotateX(0deg) translateX(0vw) translateY(0vh);transform-origin: !50% 0%;',
				'data-bottom-top' => 'transform: perspective(1000px) scale(1) rotateX(70deg) translateX(0vw) translateY(0vh);transform-origin: !50% 0%;',			
				] );
			}
		}
	}
	
	
	/**
	 * @param $post_css Post
	 * @param $element  Element_Base
	 */
	public function add_post_css( $post_css, $element ) {		
		if ( $post_css instanceof Dynamic_CSS ) {
			return;
		}

		$element_settings = $element->get_settings();

		if ( empty( $element_settings['plus_custom_css'] ) ) {
			return;
		}

		$css = trim( $element_settings['plus_custom_css'] );

		if ( empty( $css ) ) {
			return;
		}
		$css = str_replace( 'selector', $post_css->get_element_unique_selector( $element ), $css );

		$css = $css;

		$post_css->get_stylesheet()->add_raw_css( $css );
	}
	
	protected function init($data='') {
		$theplus_options=get_option('theplus_options');
		$plus_extras=theplus_get_option('general','extras_elements');
		
		if((isset($plus_extras) && empty($plus_extras) && empty($theplus_options)) || (!empty($plus_extras) && (in_array('column_sticky',$plus_extras) || in_array('custom_width_column',$plus_extras) || in_array('order_sort_column',$plus_extras) || in_array('column_custom_css',$plus_extras)))){
			add_action( 'elementor/element/column/section_advanced/after_section_end', [ $this, 'tp_register_controls' ], 10, 2 );
		}
		if((isset($plus_extras) && empty($plus_extras) && empty($theplus_options)) || (!empty($plus_extras) && in_array('section_scroll_animation',$plus_extras))){
			add_action( 'elementor/frontend/section/before_render', [ $this, 'plus_before_render_section'], 10, 1 );

			$experiments_manager = Plugin::$instance->experiments;		
			if($experiments_manager->is_feature_active( 'container' )){
				add_action( 'elementor/frontend/container/before_render', [ $this, 'plus_before_render_section'], 10, 1 );
			}
		}
		
		add_action( 'elementor/frontend/column/before_render', [ $this, 'before_render_element'], 10, 1 );		
		add_action( 'elementor/frontend/element/before_render', array( $this, 'before_render_element' ) );
		
		add_action( 'elementor/element/parse_css', [ $this, 'add_post_css' ], 10, 2 );
		
		if((isset($plus_extras) && empty($plus_extras) && empty($theplus_options)) || (!empty($plus_extras) && (in_array('section_scroll_animation',$plus_extras) || in_array('section_custom_css',$plus_extras)))){			
			add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'section_register_controls' ], 10, 2 );

			$experiments_manager = Plugin::$instance->experiments;		
			if($experiments_manager->is_feature_active( 'container' )){
				add_action( 'elementor/element/container/section_layout/after_section_end', [ $this, 'section_register_controls' ], 10, 2  );
			}			
		}
	}

}