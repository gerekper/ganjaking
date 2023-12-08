<?php 
/*
Widget Name: Morphing Layouts
Description: Unique Animation SVG morphing shapes layouts
Author: Theplus
Author URI: https://posimyth.com
*/
namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Background;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class ThePlus_MorphingLayouts extends Widget_Base {
		
	public function get_name() {
		return 'tp-morphing-layouts';
	}

    public function get_title() {
        return esc_html__('Morphing Layouts', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-balance-scale theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-creatives');
    }
	public function get_keywords() {
		return ['morphing', 'morphing sections', 'blob section', 'blob builder', 'SVG Sections'];
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
			'morph_layout',
			[
				'label' => esc_html__( 'Morphing Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => [
					'normal'  => esc_html__( 'Normal ', 'theplus' ),
					'sec_bg'  => esc_html__( 'Section Background', 'theplus' ),
					'column_bg'  => esc_html__( 'Column Background', 'theplus' ),
					'fixed_scroll'  => esc_html__( 'Fixed Scroll', 'theplus' ),
				],
			]
		);
		$this->start_controls_tabs( 'morph_width_height' );
		$this->start_controls_tab( 'morph_mw_mh',
			[
				'label' => esc_html__( 'Max-Size', 'theplus' ),
			]
		);
		$this->add_responsive_control(
			'morph_maxwidth',
			[
				'label' => esc_html__( 'Maximum Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 2000,
						'step' => 2,
					],
				],
				'selectors' => [
					'.morph-{{ID}}.plus-morphing-svg-wrapper' => 'max-width: {{SIZE}}{{UNIT}};margin: 0 auto;',
				],
			]
		);
		$this->add_responsive_control(
			'morph_maxheight',
			[
				'label' => esc_html__( 'Maximum Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 1000,
						'step' => 2,
					],
				],
				'selectors' => [
					'.morph-{{ID}}.plus-morphing-svg-wrapper' => 'max-height: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab( 'viebox_mw_mh',
			[
				'label' => esc_html__( 'ViewBox Size', 'theplus' ),
			]
		);
		$this->add_responsive_control(
			'viewbox_width',
			[
				'label' => esc_html__( 'ViewBox Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 2000,
						'step' => 2,
					],
				],
			]
		);
		$this->add_responsive_control(
			'viewbox_height',
			[
				'label' => esc_html__( 'ViewBox Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 1000,
						'step' => 2,
					],
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'morph_align',
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
				'toggle' => false,
				'separator' => 'before',
				'condition'    => [
					'morph_layout' => [ 'sec_bg','column_bg','fixed_scroll' ],
				],
			]
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'layouts',
			[
				'label' => esc_html__( 'Morphing Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1-a',
				'options' => [
					'style-1-a'  => esc_html__( 'Style 1-A', 'theplus' ),
					'style-1-b'  => esc_html__( 'Style 1-B', 'theplus' ),
					'style-1-c'  => esc_html__( 'Style 1-C', 'theplus' ),
					'style-2-a'  => esc_html__( 'Style 2-A', 'theplus' ),
					'style-2-b'  => esc_html__( 'Style 2-B', 'theplus' ),
					'style-2-c'  => esc_html__( 'Style 2-C', 'theplus' ),
					'style-3-a'  => esc_html__( 'Style 3-A', 'theplus' ),
					'style-3-b'  => esc_html__( 'Style 3-B', 'theplus' ),
					'style-3-c'  => esc_html__( 'Style 3-C', 'theplus' ),
					'style-3-d'  => esc_html__( 'Style 3-D', 'theplus' ),
					'style-3-e'  => esc_html__( 'Style 3-E', 'theplus' ),
					'style-4-a'  => esc_html__( 'Style 4-A', 'theplus' ),
					'style-4-b'  => esc_html__( 'Style 4-B', 'theplus' ),
					'style-4-c'  => esc_html__( 'Style 4-C', 'theplus' ),
					'style-4-d'  => esc_html__( 'Style 4-D', 'theplus' ),
					'custom' => esc_html__( 'Custom Morphing', 'theplus' ),
				],
			]
		);
		$repeater->add_control(
			'custom_layouts',
			[
				'label' => esc_html__( 'Custom Morphing Code', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 5,
				'placeholder' => esc_html__( 'Morphing Custom Code', 'theplus' ),
				'condition'    => [
					'layouts' => [ 'custom' ],
				],
			]
		);
		$repeater->add_control(
			'fixed_scroll_alt',
			[
				'label' => esc_html__( 'Fixed Scroll Alternate', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none' => esc_html__( 'Used Fixed Scroll', 'theplus' ),
					'style-1-a'  => esc_html__( 'Style 1-A', 'theplus' ),
					'style-1-b'  => esc_html__( 'Style 1-B', 'theplus' ),
					'style-1-c'  => esc_html__( 'Style 1-C', 'theplus' ),
					'style-2-a'  => esc_html__( 'Style 2-A', 'theplus' ),
					'style-2-b'  => esc_html__( 'Style 2-B', 'theplus' ),
					'style-2-c'  => esc_html__( 'Style 2-C', 'theplus' ),
					'style-3-a'  => esc_html__( 'Style 3-A', 'theplus' ),
					'style-3-b'  => esc_html__( 'Style 3-B', 'theplus' ),
					'style-3-c'  => esc_html__( 'Style 3-C', 'theplus' ),
					'style-3-d'  => esc_html__( 'Style 3-D', 'theplus' ),
					'style-3-e'  => esc_html__( 'Style 3-E', 'theplus' ),
					'style-4-a'  => esc_html__( 'Style 4-A', 'theplus' ),
					'style-4-b'  => esc_html__( 'Style 4-B', 'theplus' ),
					'style-4-c'  => esc_html__( 'Style 4-C', 'theplus' ),
					'style-4-d'  => esc_html__( 'Style 4-D', 'theplus' ),
					'custom' => esc_html__( 'Custom Morphing', 'theplus' ),
				],
				'description' => esc_html__('Note: If, enable (Fixed Scroll) scrolling option used for styles.','theplus'),
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'custom_layouts_alt',
			[
				'label' => esc_html__( 'Custom Alternate Morphing Code', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 5,
				'placeholder' => esc_html__( 'Morphing Custom Code', 'theplus' ),
				'condition'    => [
					'fixed_scroll_alt' => [ 'custom' ],
				],
			]
		);
		
		$repeater->start_controls_tabs( 'add_options_tabs' );
		$repeater->start_controls_tab( 'tab_path_option',
			[
				'label' => esc_html__( 'Path', 'theplus' ),
			]
		);
		$repeater->add_control(
			'path_duration',
			[
				'label' => esc_html__( 'Path Duration', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => 300,
						'max' => 10000,
						'step' => 20,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 3000,
				],
				
			]
		);
		$repeater->add_control(
			'path_elasticity',
			[
				'label' => esc_html__( 'Elasticity', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => 10,
						'max' => 5000,
						'step' => 10,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 600,
				],
				
			]
		);
		$repeater->add_control(
			'path_easing',
			[
				'label' => esc_html__( 'Path Easing', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'easeOutElastic',
				'options' => theplus_anime_animation_easing(),
			]
		);
		$repeater->end_controls_tab();
		$repeater->start_controls_tab( 'tab_svg_option',
			[
				'label' => esc_html__( 'SVG', 'theplus' ),				
			]
		);
		$repeater->add_control(
			'svg_duration',
			[
				'label' => esc_html__( 'SVG Duration', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => 300,
						'max' => 10000,
						'step' => 20,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 2000,
				],
				
			]
		);
		$repeater->add_control(
			'svg_easing',
			[
				'label' => esc_html__( 'SVG Easing', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'easeOutElastic',
				'options' => theplus_anime_animation_easing(),
			]
		);
		$repeater->end_controls_tab();
		$repeater->start_controls_tab( 'tab_fill_option',
			[
				'label' => esc_html__( 'Fill Color', 'theplus' ),				
			]
		);
		$repeater->add_control(
			'fill_duration',
			[
				'label' => esc_html__( 'Fill Duration', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => 10,
						'max' => 5000,
						'step' => 20,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 500,
				],
				
			]
		);
		$repeater->add_control(
			'fill_easing',
			[
				'label' => esc_html__( 'Fill Easing', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'linear',
				'options' => theplus_anime_animation_easing(),
			]
		);
		$repeater->add_control(
			'fill_color_loop',
			[
				'label' => esc_html__( 'Fill Color', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),
				'default' => 'no',
				'description' => esc_html__('If, Select "Fixed Scroll" Morphing type then use on Fill Color.','theplus'),
			]
		);
		$repeater->add_control(
			'fill_color',
			[
				'label' => esc_html__( 'Fill Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'condition'    => [
					'fill_color_loop' => 'yes',
				],
			]
		);
		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();
		$repeater->add_control(
			'extra_options',
			[
				'label' => esc_html__( 'Additional Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'scaleX',
			[
				'label' => esc_html__( 'ScaleX', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => 0.6,
						'max' => 3,
						'step' => 0.02,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1,
				],
			]
		);
		$repeater->add_control(
			'scaleY',
			[
				'label' => esc_html__( 'ScaleY', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => 0.6,
						'max' => 3,
						'step' => 0.02,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1,
				],
			]
		);
		$repeater->add_control(
			'rotate',
			[
				'label' => esc_html__( 'Rotate', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => -360,
						'max' => 360,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
			]
		);
		$repeater->add_control(
			'trans_x',
			[
				'label' => esc_html__( 'Horizontal Adjust', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
			]
		);
		$repeater->add_control(
			'trans_y',
			[
				'label' => esc_html__( 'Vertical Adjust', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
			]
		);
		$this->add_control(
			'morphing_loop',
			[
				'label' => esc_html__( 'Svg Morphing Loop', 'theplus' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'layouts' => 'style-1-a',
						'fixed_scroll_alt' => 'none',
					],
					[
						'layouts' => 'style-2-a',
						'fixed_scroll_alt' => 'none',
					],
				],
				'title_field' => '{{{ layouts }}}',
			]
		);
		$this->add_control(
			'morph_type',
			[
				'label' => esc_html__( 'Select Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'color',
				'options' => [
					'color'  => esc_html__( 'Color ', 'theplus' ),
					'gradient'  => esc_html__( 'Gradient', 'theplus' ),
					'image'  => esc_html__( 'Image', 'theplus' ),
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'morph_color',
			[
				'label' => esc_html__( 'Fill Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'condition'    => [
					'morph_type' => [ 'color' ],
				],
			]
		);
		$this->add_control(
			'grad_x1',
			[
				'label' => esc_html__( 'Position X1', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 0.5,
				'default' => 0,
				'condition'    => [
					'morph_type' => [ 'gradient' ],
				],
			]
		);
		$this->add_control(
			'grad_x2',
			[
				'label' => esc_html__( 'Position X2', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 0.5,
				'default' => 100,
				'condition'    => [
					'morph_type' => [ 'gradient' ],
				],
			]
		);
		$this->add_control(
			'grad_y1',
			[
				'label' => esc_html__( 'Position Y1', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 0.5,
				'default' => 70.5,
				'condition'    => [
					'morph_type' => [ 'gradient' ],
				],
			]
		);
		$this->add_control(
			'grad_y2',
			[
				'label' => esc_html__( 'Position Y2', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 0.5,
				'default' => 100,
				'condition'    => [
					'morph_type' => [ 'gradient' ],
				],
			]
		);
		$this->start_controls_tabs( 'gradient_tabs' );
		$this->start_controls_tab( 'tab_grad_color1',
			[
				'label' => esc_html__( 'Color 1', 'theplus' ),
				'condition'    => [
					'morph_type' => [ 'gradient' ],
				],
			]
		);
		$this->add_control(
			'grad_color1',
			[
				'label' => esc_html__( 'Color 1', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'condition'    => [
					'morph_type' => [ 'gradient' ],
				],
			]
		);
		$this->add_control(
			'grad_color1_offset',
			[
				'label' => esc_html__( 'Offset', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 0.5,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'condition'    => [
					'morph_type' => [ 'gradient' ],
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_grad_color2',
			[
				'label' => esc_html__( 'Color 2', 'theplus' ),
				'condition'    => [
					'morph_type' => [ 'gradient' ],
				],
			]
		);		
		$this->add_control(
			'grad_color2',
			[
				'label' => esc_html__( 'Color 2', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'condition'    => [
					'morph_type' => [ 'gradient' ],
				],
			]
		);
		$this->add_control(
			'grad_color2_offset',
			[
				'label' => esc_html__( 'Offset', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 0.5,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'condition'    => [
					'morph_type' => [ 'gradient' ],
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'morph_image',
			[
				'label' => esc_html__( 'Masking Image', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'condition'    => [
					'morph_type' => [ 'image' ],
				],
			]
		);
		$this->add_control(
			'image_x',
			[
				'label' => esc_html__( 'Position X', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 0.5,
				'default' => 0,
				'condition'    => [
					'morph_type' => [ 'image' ],					
				],
			]
		);
		$this->add_control(
			'image_y',
			[
				'label' => esc_html__( 'Position Y', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 0.5,
				'default' => 0,
				'condition'    => [
					'morph_type' => [ 'image' ],
				],
			]
		);
		$this->add_control(
			'image_dimension',
			[
				'label' => esc_html__( 'Image Dimension', 'theplus' ),
				'type' => Controls_Manager::IMAGE_DIMENSIONS,
				'description' => esc_html__( 'Set custom width or height to keep the original size ratio.', 'theplus' ),
				'default' => [
					'width' => '',
					'height' => '',
				],
				'condition'    => [
					'morph_type' => [ 'image' ],
				],
			]
		);
		$this->add_control(
			'custom_morph_path_blobmaker',
			[
				'label' => esc_html__( 'Custom SVG from Blob Maker?', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),
				'default' => 'no',
				'description' => 'If you are using custom SVG code from blob maker, Turn on this option.<a href="https://www.blobmaker.app/" target="_blank">Click</a>',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'morphing_overflow',
			[
				'label' => esc_html__( 'Overflow Morphing', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Hidden', 'theplus' ),
				'label_off' => esc_html__( 'Default', 'theplus' ),
				'default' => 'hidden',
				'return_value' => 'hidden',
				'separator' => 'before',
				'selectors' => [
					'.plus-morphing-svg-wrapper.morph-{{ID}}' => 'overflow: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'morph_fixed_scroll_bg',
			[
				'label' => esc_html__( 'Body Background', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition'    => [
					'morph_layout' => [ 'fixed_scroll' ],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'fixed_scroll_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '.plus-morph-fixed-scroll-bg.morph-fixed-{{ID}}',
				'condition'    => [
					'morph_layout' => [ 'fixed_scroll' ],
					'morph_fixed_scroll_bg' => [ 'yes' ],
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'morph_path_style',
			[
				'label' => esc_html__( 'Morphing Style', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,				
			]
		);
		$this->add_control(
			'hover_path',
			[
				'label' => esc_html__( 'Morphing Change Hover', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),
				'separator' => 'before',
				'condition'    => [
					'morph_layout' => [ 'normal' ],
				],
			]
		);
		$this->add_control(
			'duration',
			[
				'label' => esc_html__( 'Duration', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => 300,
						'max' => 10000,
						'step' => 20,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 3500,
				],
				
			]
		);
		$this->add_control(
			'morph_easing',
			[
				'label' => esc_html__( 'Easing', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'linear',
				'options' => theplus_anime_animation_easing(),
				'separator' => 'before',
			]
		);
		$this->add_control(
			'adv_options',
			[
				'label' => esc_html__( 'Additional Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'scaleX',
			[
				'label' => esc_html__( 'ScaleX', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => 0.6,
						'max' => 3,
						'step' => 0.02,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1,
				],
			]
		);
		$this->add_control(
			'scaleY',
			[
				'label' => esc_html__( 'ScaleY', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => 0.6,
						'max' => 3,
						'step' => 0.02,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1,
				],
			]
		);
		$this->add_control(
			'rotate',
			[
				'label' => esc_html__( 'Rotate', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => -360,
						'max' => 360,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
			]
		);
		$this->add_control(
			'trans_x',
			[
				'label' => esc_html__( 'Horizontal Adjust', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
			]
		);
		$this->add_control(
			'trans_y',
			[
				'label' => esc_html__( 'Vertical Adjust', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'masking_image_style',
			[
				'label' => esc_html__( 'Image Style', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition'    => [
					'morph_type' => [ 'image' ],
				],
			]
		);
		$this->add_control(
			'image_duration',
			[
				'label' => esc_html__( 'Image Duration', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => 300,
						'max' => 10000,
						'step' => 20,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 800,
				],
			]
		);
		$this->add_control(
			'image_elasticity',
			[
				'label' => esc_html__( 'Image Elasticity', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => 0,
						'max' => 2000,
						'step' => 10,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 300,
				],
			]
		);
		
		$this->start_controls_tabs( 'image_style_tabs' );
		$this->start_controls_tab( 'tab_image_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'image_scaleX',
			[
				'label' => esc_html__( 'ScaleX', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => 0.6,
						'max' => 3,
						'step' => 0.02,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1,
				],
			]
		);
		$this->add_control(
			'image_scaleY',
			[
				'label' => esc_html__( 'ScaleY', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => 0.6,
						'max' => 3,
						'step' => 0.02,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1,
				],
			]
		);
		$this->add_control(
			'image_rotate',
			[
				'label' => esc_html__( 'Rotate', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => -360,
						'max' => 360,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
			]
		);
		$this->add_control(
			'image_trans_x',
			[
				'label' => esc_html__( 'Horizontal Adjust', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
			]
		);
		$this->add_control(
			'image_trans_y',
			[
				'label' => esc_html__( 'Vertical Adjust', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_image_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),				
			]
		);
		$this->add_control(
			'image_on_hover',
			[
				'label' => esc_html__( 'Image Hover', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),
			]
		);
		$this->add_control(
			'image_hover_scaleX',
			[
				'label' => esc_html__( 'ScaleX', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => 0.6,
						'max' => 3,
						'step' => 0.02,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1,
				],
				'condition'    => [
					'image_on_hover' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'image_hover_scaleY',
			[
				'label' => esc_html__( 'ScaleY', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => 0.6,
						'max' => 3,
						'step' => 0.02,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1,
				],
				'condition'    => [
					'image_on_hover' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'image_hover_rotate',
			[
				'label' => esc_html__( 'Rotate', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => 0,
						'max' => 360,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
				'condition'    => [
					'image_on_hover' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'image_hover_trans_x',
			[
				'label' => esc_html__( 'Horizontal Adjust', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
				'condition'    => [
					'image_on_hover' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'image_hover_trans_y',
			[
				'label' => esc_html__( 'Vertical Adjust', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
				'condition'    => [
					'image_on_hover' => [ 'yes' ],
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}
	
    protected function render() {
		$settings = $this->get_settings_for_display();				
		$data_attr='';
		
		$uid=uniqid("morph");
		$data_class = $uid;
		$data_class .= ' morph-'.esc_attr($this->get_id());
		$data_class .= (!empty($settings["morph_align"])) ? ' morph-'.esc_attr($settings["morph_align"]) : ' morph-center';
		if(!empty($settings['morph_layout']) && $settings['morph_layout']=='sec_bg'){
			$data_class .= ' morph-row';
			$data_attr .= ' data-morphrow="yes"';
		}
		if(!empty($settings['morph_layout']) && $settings['morph_layout']=='column_bg'){
			$data_class .= ' morph-column';
			$data_attr .= ' data-morphcolumn="yes"';
		}
		if(!empty($settings['morph_layout']) && $settings['morph_layout']=='fixed_scroll'){
			$data_class .= ' morph-fixed';
			$data_attr .= ' data-morphfixed="yes"';
		}
		
		$data_attr .= ' data-id="'.esc_attr($uid).'"';
		$data_attr .= ' data-morph-id="morph-'.esc_attr($this->get_id()).'"';
		
		
		$first_morph=$first_morph_alt='';
		$json_arr=array();
		
		
		$i=0;	$morph_style='';	
		if(!empty($settings["morphing_loop"])){
			foreach ( $settings['morphing_loop'] as $index => $item ) :
				
				if($item["layouts"]!='custom'){
					if($i>0 || (!empty($settings['morph_layout']) && $settings['morph_layout']=='fixed_scroll')){
						$json_arr["path"][]=$this->get_morphing_style($item["layouts"]);
					}else if($i==0){
						$first_morph=$this->get_morphing_style($item["layouts"]);
					}
					$morph_style='yes';
				}else{
					if($i>0 || (!empty($settings['morph_layout']) && $settings['morph_layout']=='fixed_scroll')){
						$json_arr["path"][]=$item["custom_layouts"];
					}else if($i==0 && (!empty($settings['morph_layout']) && $settings['morph_layout']!='fixed_scroll')){						
						$first_morph=$item["custom_layouts"];
					}
				}
				if($item["fixed_scroll_alt"]!='custom' && $item["fixed_scroll_alt"]!='none'){
					if($i>0 || (!empty($settings['morph_layout']) && $settings['morph_layout']=='fixed_scroll')){
						$json_arr["pathAlt"][]=$this->get_morphing_style($item["fixed_scroll_alt"]);
					}else if($i==0){
						$first_morph_alt=$this->get_morphing_style($item["fixed_scroll_alt"]);
					}
				}else if($item["fixed_scroll_alt"]=='custom'){
					if($i>0 || (!empty($settings['morph_layout']) && $settings['morph_layout']=='fixed_scroll')){
						$json_arr["pathAlt"][]=$item["custom_layouts_alt"];
					}else if($i==0 && (!empty($settings['morph_layout']) && $settings['morph_layout']!='fixed_scroll')){				
						$first_morph_alt=$item["custom_layouts_alt"];
					}
				}
				$json_arr['animation']['path']["duration"][]=(!empty($item["path_duration"]["size"])) ? $item["path_duration"]["size"] : 3000;
				$json_arr['animation']['path']["easing"][]=(!empty($item["path_easing"])) ? $item["path_easing"] : 'easeOutElastic';
				$json_arr['animation']['path']["elasticity"][]=(!empty($item["path_elasticity"]['size'])) ? $item["path_elasticity"]['size'] : 600;
				
				$json_arr['animation']['svg']["duration"][]=(!empty($item["svg_duration"]['size'])) ? $item["svg_duration"]['size'] : 2000;
				$json_arr['animation']['svg']["easing"][]=(!empty($item["svg_easing"])) ? $item["svg_easing"] : 'easeOutElastic';
				
				$json_arr['animation']['fill']["duration"][]=(!empty($item["fill_duration"]['size'])) ? $item["fill_duration"]['size'] : 500;
				$json_arr['animation']['fill']["easing"][]=(!empty($item["fill_easing"])) ? $item["fill_easing"] : 'linear';
				if(!empty($item['fill_color_loop']) && $item['fill_color_loop']=='yes'){
					$json_arr['animation']['fill']["color"][]=(!empty($item["fill_color"])) ? $item["fill_color"] : 'none';
				}else{
					$json_arr['animation']['fill']["color"][]=(!empty($settings["morph_color"])) ? $settings["morph_color"] : 'none';
				}
				
				$json_arr['animation']["scaleX"][]=(!empty($item["scaleX"]['size'])) ? $item["scaleX"]['size'] : 1;
				$json_arr['animation']["scaleY"][]=(!empty($item["scaleY"]['size'])) ? $item["scaleY"]['size'] : 1;
				$json_arr['animation']["rotate"][]=(!empty($item["rotate"]['size'])) ? $item["rotate"]['size'] : 0;
				$json_arr['animation']["tx"][]=(!empty($item['trans_x']['size'])) ? $item['trans_x']['size'] : 0;
				$json_arr['animation']["ty"][]=(!empty($item['trans_y']['size'])) ? $item['trans_y']['size'] : 0;
				
				$i++;
			endforeach;
			if(!empty($settings['morph_layout']) && $settings['morph_layout']!='fixed_scroll'){
				$json_arr["path"][]=$first_morph;
				if($first_morph_alt){
					$json_arr["pathAlt"][]=$first_morph_alt;
				}
			}else{
				$first_morph=$json_arr["path"][0];
			}
		}
		
		if(!empty($settings['scaleX']['size'])){
			$json_arr["scaleX"]=$settings['scaleX']['size'];
		}
		if(!empty($settings['scaleY']['size'])){
			$json_arr["scaleY"]=$settings['scaleY']['size'];
		}
		$json_arr["rotate"]=(!empty($settings["rotate"]['size'])) ? $settings["rotate"]['size'] : 0;
		$json_arr["tx"]=(!empty($settings['trans_x']['size'])) ? $settings['trans_x']['size'] : 0;
		$json_arr["ty"]=(!empty($settings['trans_y']['size'])) ? $settings['trans_y']['size'] : 0;
		$json_arr["duration"]=(!empty($settings["duration"]["size"])) ? $settings["duration"]["size"] : 3500;
		$json_arr["easing"]=(!empty($settings["morph_easing"])) ? $settings["morph_easing"] : 'linear';
		$json_arr["fill"]["color"]=(!empty($settings["morph_color"])) ? $settings["morph_color"] : 'none';		
		$json_arr["fill"]["easing"]='linear';
		if($settings['morph_layout']=='normal'){
			$json_arr['hover_path']=(!empty($settings["hover_path"])) ? 'yes' : 'no';
		}else{
			$json_arr['hover_path']='no';
		}
		$json_array=json_encode($json_arr);
		
		
		$morph_width=(!empty($settings['morph_maxwidth']['size'])) ? $settings['morph_maxwidth']['size'] : 1200;
		$morph_height=(!empty($settings['morph_maxheight']['size'])) ? $settings['morph_maxheight']['size'] : 550;
		$viewbox_width=(!empty($settings['viewbox_width']['size'])) ? $settings['viewbox_width']['size'] : 600;
		$viewbox_height=(!empty($settings['viewbox_height']['size'])) ? $settings['viewbox_height']['size'] : 600;
		
		$data_attr .= ' data-morph-width="'.$morph_width.'"';
		$data_attr .= (!empty($settings['morph_maxwidth_tablet']['size'])) ? ' data-morph-wt="'.$settings['morph_maxwidth_tablet']['size'].'"' : 'data-morph-width-tablet=""';
		$data_attr .= (!empty($settings['morph_maxwidth_mobile']['size'])) ? ' data-morph-wm="'.$settings['morph_maxwidth_mobile']['size'].'"' : 'data-morph-width-mobile=""';
		$data_attr .= ' data-morph-height="'.$morph_height.'"';
		$data_attr .= (!empty($settings['morph_maxheight_tablet']['size'])) ? ' data-morph-ht="'.$settings['morph_maxheight_tablet']['size'].'"' : 'data-morph-height-tablet=""';
		$data_attr .= (!empty($settings['morph_maxheight_mobile']['size'])) ? ' data-morph-hm="'.$settings['morph_maxheight_mobile']['size'].'"' : 'data-morph-height-mobile=""';
		$data_attr .= ' data-viewbox-width="'.$viewbox_width.'"';
		$data_attr .= (!empty($settings['viewbox_width_tablet']['size'])) ? ' data-viewbox-wt="'.$settings['viewbox_width_tablet']['size'].'"' : 'data-viewbox-width-tablet=""';
		$data_attr .= (!empty($settings['viewbox_width_mobile']['size'])) ? ' data-viewbox-wm="'.$settings['viewbox_width_mobile']['size'].'"' : 'data-viewbox-width-mobile=""';
		$data_attr .= ' data-viewbox-height="'.$viewbox_height.'"';
		$data_attr .= (!empty($settings['viewbox_height_tablet']['size'])) ? ' data-viewbox-ht="'.$settings['viewbox_height_tablet']['size'].'"' : 'data-viewbox-height-tablet=""';
		$data_attr .= (!empty($settings['viewbox_height_mobile']['size'])) ? ' data-viewbox-hm="'.$settings['viewbox_height_mobile']['size'].'"' : 'data-viewbox-height-mobile=""';
		
		$shape_trasnform ='';
		if($morph_style=='yes' || $settings['custom_morph_path_blobmaker']=='yes'){
			$shape_trasnform =' transform="translate(300,300)"';
		}
		
		$image_json_array=$gradient_svg=$fill_style=$clippath_st=$clippath_end=$image_clip='';
		if(!empty($settings["morph_type"]) && $settings["morph_type"]=='gradient'){
			$grad_color1=(!empty($settings['grad_color1'])) ? $settings['grad_color1'] : "rgb(95,54,152)";
			$grad_color1_offset=(!empty($settings['grad_color1_offset']['size'])) ? $settings['grad_color1_offset']['size'].'%' : "0%";
			$grad_color2=(!empty($settings['grad_color2'])) ? $settings['grad_color2'] : "rgb(247,109,138)";
			$grad_color2_offset=(!empty($settings['grad_color2_offset']['size'])) ? $settings['grad_color2_offset']['size'].'%' : "100%";
			$grad_x1=(!empty($settings['grad_x1'])) ? $settings['grad_x1'].'%' : "0%";
			$grad_x2=(!empty($settings['grad_x2'])) ? $settings['grad_x2'].'%' : "100%";
			$grad_y1=(!empty($settings['grad_y1'])) ? $settings['grad_y1'].'%' : "70.711%";
			$grad_y2=(!empty($settings['grad_y2'])) ? $settings['grad_y2'].'%' : "100%";
						
			$gradient_svg='<linearGradient id="mo_'.esc_attr($this->get_id()).'" x1="'.esc_attr($grad_x1).'" x2="'.esc_attr($grad_x2).'" y1="'.esc_attr($grad_y1).'" y2="'.esc_attr($grad_y2).'"><stop offset="'.esc_attr($grad_color1_offset).'" stop-color="'.esc_attr($grad_color1).'" stop-opacity="1" /><stop offset="'.esc_attr($grad_color2_offset).'" stop-color="'.esc_attr($grad_color2).'" stop-opacity="1" /></linearGradient>';
			$fill_style = 'style="fill:url(#mo_'.esc_attr($this->get_id()).');"';
			
		}else if(!empty($settings["morph_type"]) && $settings["morph_type"]=='image'){
			$morph_image=(!empty($settings['morph_image']["url"])) ? $settings['morph_image']["url"] : '';
			$image_x=(!empty($settings['image_x']["size"])) ? $settings['image_x']["size"].'%' : '0';
			$image_y=(!empty($settings['image_y']["size"])) ? $settings['image_y']["size"].'%' : '0';
			$image_width=(!empty($settings['image_dimension']["width"])) ? $settings['image_dimension']["width"].'px' : '100%';
			$image_height=(!empty($settings['image_dimension']["height"])) ? $settings['image_dimension']["height"].'px' : '100%';
			
			$image_json=array();			
			$image_json['scaleX']=(!empty($settings["image_scaleX"]["size"])) ? $settings["image_scaleX"]["size"] : 1;
			$image_json['scaleY']=(!empty($settings["image_scaleY"]["size"])) ? $settings["image_scaleY"]["size"] : 1;
			$image_json['rotate']=(!empty($settings["image_rotate"]["size"])) ? $settings["image_rotate"]["size"] : 0;
			$image_json['trans_x']=(!empty($settings["image_trans_x"]["size"])) ? $settings["image_trans_x"]["size"] : 0;
			$image_json['trans_y']=(!empty($settings["image_trans_y"]["size"])) ? $settings["image_trans_y"]["size"] : 0;
			$image_json['duration']=(!empty($settings["image_duration"]["size"])) ? $settings["image_duration"]["size"] : 800;
			$image_json['elasticity']=(!empty($settings["image_elasticity"]["size"])) ? $settings["image_elasticity"]["size"] : 300;						
			$image_hover='';
			
			$image_json['image_hover']=(!empty($settings["image_on_hover"])) ? 'yes' : 'no';
			if(!empty($settings['image_on_hover']) && $settings['image_on_hover']=='yes'){
				$image_json['hover_scaleX']=(!empty($settings["image_hover_scaleX"]["size"])) ? $settings["image_hover_scaleX"]["size"] : 1;
				$image_json['hover_scaleY']=(!empty($settings["image_hover_scaleY"]["size"])) ? $settings["image_hover_scaleY"]["size"] : 1;
				$image_json['hover_rotate']=(!empty($settings["image_hover_rotate"]["size"])) ? $settings["image_hover_rotate"]["size"] : 0;
				$image_json['hover_trans_x']=(!empty($settings["image_hover_trans_x"]["size"])) ? $settings["image_hover_trans_x"]["size"] : 0;
				$image_json['hover_trans_y']=(!empty($settings["image_hover_trans_y"]["size"])) ? $settings["image_hover_trans_y"]["size"] : 0;				
			}
			$image_json_array=json_encode($image_json);
			$clippath_st='<clipPath id="mo_'.esc_attr($this->get_id()).'">';
			$clippath_end='</clipPath>';
			
			$image_clip='<g clip-path="url(#mo_'.esc_attr($this->get_id()).')"><image class="morph-image" xlink:href="'.esc_url($morph_image).'" x="'.esc_attr($image_x).'" y="'.esc_attr($image_y).'" height="'.esc_attr($image_height).'" width="'.esc_attr($image_width).'" ></image></g>';
			
		}else{
			$fill_style = 'style="fill:'.esc_attr($settings["morph_color"]).'"';;
		}
		
		$output ='<div class="plus-morphing-svg-wrapper '.$data_class.'" id="'.esc_attr($uid).'" '.$data_attr.' data-morph=\'' . $json_array . '\' data-morphimage=\''.$image_json_array.'\'>';
			$output .='<svg class="morph" width="'.$morph_width.'" height="'.$morph_height.'" viewBox="0 0 '.$viewbox_width.' '.$viewbox_height.'">';
				$output .= $clippath_st.$gradient_svg.'<path '.$fill_style.' d="'.$first_morph.'" '.$shape_trasnform.'  />'.$clippath_end.$image_clip;
			$output .='</svg>';
		$output .='</div>';
		if(!empty($settings['morph_fixed_scroll_bg']) && $settings['morph_fixed_scroll_bg']=='yes' && $settings['morph_layout']=='fixed_scroll'){
			$output .='<div class="plus-morph-fixed-scroll-bg morph-fixed-'.esc_attr($this->get_id()).'" id="fixed'.esc_attr($uid).'" data-id="fixed'.esc_attr($uid).'" data-morph-fixed="morph-fixed-'.esc_attr($this->get_id()).'"></div>';
		}
		echo $output;
	}
	
	protected function get_morphing_style($style=''){
		$output='';
		if($style=='style-1-a'){
			$output ='M139.6,-155.2C177.7,-134.5,203.1,-87.4,209.5,-38.8C215.8,9.8,203.2,59.9,176.3,97.5C149.5,135,108.4,160.1,61.3,183.9C14.3,207.7,-38.9,230.2,-82,217.8C-125.1,205.4,-158.3,157.9,-186.1,108.2C-213.9,58.4,-236.5,6.4,-228.1,-40.1C-219.8,-86.6,-180.6,-127.6,-137.4,-147.5C-94.3,-167.4,-47.1,-166.2,1.8,-168.3C50.8,-170.5,101.6,-176,139.6,-155.2Z';
		}
		if($style=='style-1-b'){
			$output = 'M108.5,-128.3C141.2,-101.9,168.6,-68.2,192.8,-20.7C217,26.799999999999997,238,88.1,215.2,121C192.4,153.9,125.8,158.2,68.9,170.9C12.1,183.6,-35,204.5,-76.6,195.8C-118.2,187,-154.4,148.5,-171.9,105.4C-189.4,62.3,-188.3,14.7,-187.5,-40.6C-186.7,-95.8,-186.2,-158.6,-155,-185.3C-123.8,-211.9,-61.9,-202.5,-12,-188.2C37.9,-173.9,75.8,-154.7,108.5,-128.3Z';
		}
		if($style=='style-1-c'){
			$output = 'M128.8,-150.8C171.5,-117.7,213.8,-81.4,224.7,-37C235.7,7.4,215.3,60,185.4,102.7C155.6,145.5,116.3,178.5,75.7,181.8C35.2,185.1,-6.5,158.8,-58.6,146.2C-110.7,133.7,-173.1,134.8,-200.9,106.4C-228.6,78,-221.6,20.1,-212.3,-38.2C-202.9,-96.5,-191.1,-155.1,-155.3,-189.5C-119.6,-223.8,-59.8,-233.9,-8.4,-224C43.1,-214,86.1,-184,128.8,-150.8Z';
		}
		if($style=='style-2-a'){
			$output ='M133.1,-145.1C173.6,-124.6,208.4,-83.7,215.8,-38.6C223.1,6.6,202.9,56,175.6,99.5C148.3,143,113.8,180.5,74.3,187.7C34.9,194.8,-9.6,171.7,-51.8,152.1C-94,132.5,-134.1,116.5,-155.9,86.3C-177.8,56.1,-181.5,11.7,-174.8,-31.7C-168.2,-75.1,-151.3,-117.6,-120.4,-139.8C-89.6,-162.1,-44.8,-164,0.6999999999999993,-164.9C46.3,-165.8,92.6,-165.6,133.1,-145.1Z';
		}
		if($style=='style-2-b'){
			$output = 'M130.3,-151.9C173.1,-119.3,215.1,-82.3,226.4,-37.1C237.8,8.1,218.5,61.6,191.9,113.9C165.4,166.3,131.6,217.5,86.2,231.9C40.8,246.2,-16.2,223.6,-74.6,203.2C-133.1,182.7,-193,164.4,-208,126C-223.1,87.7,-193.3,29.4,-179,-28.1C-164.7,-85.6,-165.9,-142.4,-138.7,-177.8C-111.4,-213.1,-55.7,-227.1,-6,-219.9C43.7,-212.8,87.4,-184.5,130.3,-151.9Z';
		}
		if($style=='style-2-c'){
			$output = 'M110.8,-147.7C135.9,-111,143.3,-68.8,151.8,-26C160.2,16.8,169.7,60.1,152.4,86C135.1,111.9,91,120.3,46,144.6C1,168.8,-44.8,208.9,-75.1,200.5C-105.5,192,-120.3,135,-149,85.8C-177.7,36.5,-220.3,-4.9,-224.1,-49.2C-227.8,-93.5,-192.6,-140.6,-148.8,-174C-105,-207.5,-52.5,-227.2,-4.8,-221.5C42.9,-215.7,85.7,-184.5,110.8,-147.7Z';
		}
		if($style=='style-3-a'){
			$output ='M152.5,-211.8C189.5,-183.1,205.5,-127.6,214.5,-75.5C223.5,-23.4,225.4,25.2,206.9,62.6C188.4,99.9,149.4,126,111.6,160.9C73.7,195.7,36.8,239.4,1.2,237.7C-34.5,236.1,-69,189.3,-109.2,155.2C-149.5,121.1,-195.5,99.8,-214,64.5C-232.4,29.2,-223.2,-20.2,-204.1,-62.4C-185,-104.6,-156,-139.7,-120.3,-168.8C-84.6,-197.8,-42.3,-220.9,7.7,-231.6C57.8,-242.2,115.6,-240.4,152.5,-211.8Z';
		}
		if($style=='style-3-b'){
			$output = 'M125.3,-166C167.2,-142.1,209.1,-111.8,216,-74.1C222.9,-36.4,194.6,8.899999999999999,169.7,45.6C144.7,82.4,122.9,110.8,95.1,134.9C67.4,159.1,33.7,179.1,-8.2,190.4C-50.2,201.7,-100.3,204.4,-128.5,180.4C-156.7,156.4,-163,105.7,-162.5,63.2C-162,20.7,-154.7,-13.6,-153.8,-60.4C-152.9,-107.2,-158.5,-166.4,-133.9,-196C-109.3,-225.5,-54.7,-225.2,-6.5,-216.3C41.7,-207.4,83.5,-189.9,125.3,-166Z';
		}
		if($style=='style-3-c'){
			$output = 'M105.4,-155.3C130.2,-127.2,139.3,-87.7,153,-48.9C166.8,-10.100000000000001,185.1,28,176.6,58.3C168,88.6,132.5,111,98.6,141.5C64.7,172,32.3,210.5,-2.8999999999999995,214.5C-38.2,218.6,-76.4,188.2,-101.6,154.9C-126.8,121.6,-139,85.3,-164.9,44.4C-190.8,3.5,-230.5,-42.1,-227.1,-80.4C-223.8,-118.6,-177.4,-149.5,-132.4,-171.1C-87.4,-192.6,-43.7,-204.8,-1.7,-202.5C40.4,-200.2,80.7,-183.4,105.4,-155.3Z';
		}
		if($style=='style-3-d'){
			$output = 'M138.9,-187C168.1,-170,171.5,-113.4,189.7,-60.8C208,-8.2,240.9,40.4,241.1,90.2C241.2,140,208.5,190.9,162.8,201.6C117.2,212.3,58.6,182.6,3.8999999999999995,177.2C-50.7,171.8,-101.5,190.7,-147.3,180.1C-193.1,169.5,-234,129.4,-244.3,82.9C-254.6,36.4,-234.3,-16.6,-203.8,-52.6C-173.4,-88.7,-132.9,-107.9,-97.2,-122.8C-61.5,-137.7,-30.8,-148.3,12,-164.9C54.9,-181.5,109.7,-204,138.9,-187Z';
		}
		if($style=='style-3-e'){
			$output = 'M133.3,-199.9C158.8,-164.9,156,-107.3,174.7,-55.4C193.4,-3.5,233.6,42.7,225.6,75C217.5,107.3,161.1,125.7,115.4,139C69.8,152.3,34.9,160.7,0,160.7C-34.9,160.7,-69.8,152.3,-110.6,137.5C-151.5,122.6,-198.5,101.2,-215.4,66C-232.4,30.8,-219.4,-18.1,-196.4,-56.1C-173.4,-94.1,-140.5,-121.1,-106.1,-153.2C-71.7,-185.4,-35.9,-222.7,9,-235.1C53.9,-247.5,107.8,-235,133.3,-199.9Z';
		}
		if($style=='style-4-a'){
			$output = 'M185.7,-143.4C221.3,-103.5,217.4,-27.4,193.8,31.5C170.3,90.5,127,132.2,76.3,155.4C25.6,178.7,-32.5,183.5,-91.2,164.7C-149.8,145.9,-209,103.6,-228,45C-247,-13.7,-225.9,-88.5,-181.2,-130.5C-136.6,-172.6,-68.3,-181.8,3.4,-184.5C75.1,-187.2,150.1,-183.4,185.7,-143.4Z';
		}
		if($style=='style-4-b'){
			$output = 'M169.1,-146.7C201.1,-94.7,196.6,-25.4,175.9,30.1C155.2,85.7,118.4,127.5,70.5,153.3C22.6,179,-36.4,188.6,-91.3,169.8C-146.2,151,-196.9,103.8,-207.7,49.4C-218.5,-4.9,-189.4,-66.4,-148.1,-120.5C-106.9,-174.5,-53.4,-221.3,7.6,-227.3C68.5,-233.3,137.1,-198.7,169.1,-146.7Z';
		}
		if($style=='style-4-c'){
			$output = 'M96.8,-59.8C139.4,-23.8,197.4,17.3,200.1,61.8C202.8,106.4,150.2,154.6,98,164.8C45.8,175.1,-5.9,147.5,-38.3,117.3C-70.6,87.2,-83.6,54.6,-99.9,14C-116.2,-26.7,-135.9,-75.4,-119.8,-105.4C-103.7,-135.4,-51.9,-146.7,-12.4,-136.8C27.1,-126.9,54.2,-95.9,96.8,-59.8Z';
		}
		if($style=='style-4-d'){
			$output = 'M67,-49.7C101.1,-10.6,152.9,18.1,154,44C155.1,69.9,105.6,93.1,66.7,95C27.9,97,-0.1,77.8,-51.7,68.1C-103.3,58.3,-178.4,58.1,-197.8,29.1C-217.2,0.1,-180.9,-57.6,-138.7,-98.6C-96.4,-139.6,-48.2,-163.8,-15.9,-151.1C16.4,-138.4,32.8,-88.9,67,-49.7Z';
		}
		return $output;
	}
	protected function content_template() {
	
	}
}