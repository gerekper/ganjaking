<?php 
/*
Widget Name: Progress Bar
Description: Progress Bar
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
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Progress_Bar extends Widget_Base {
		
	public function get_name() {
		return 'tp-progress-bar';
	}

    public function get_title() {
        return esc_html__('Progress Bar', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-pie-chart theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-essential');
    }
	
	public function get_keywords() {
		return [ 'pie chart', 'progress bar', 'chart'];
	}

    protected function register_controls() {
		
		/* Progress Bar */
		
		$this->start_controls_section(
			'progress_bar',
			[
				'label' => esc_html__( 'Progress Bar', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'main_style',
			[
				'label' => esc_html__( 'Select Main Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'progressbar',
				'options' => [
					'progressbar'  => esc_html__( 'Progress Bar', 'theplus' ),
					'pie_chart' => esc_html__( 'Pie Chart', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'pie_chart_style',
			[
				'label' => esc_html__( 'Pie Chart Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style_1',
				'options' => [
					'style_1' => esc_html__( 'Style 1', 'theplus' ),
					'style_2'  => esc_html__( 'Style 2', 'theplus' ),
					'style_3'  => esc_html__( 'Style 3', 'theplus' ),
				],
				'condition'    => [
					'main_style' => [ 'pie_chart' ],
				],
			]
		);
		$this->add_control(
			'progressbar_style',
			[
				'label' => esc_html__( 'Progress Bar Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style_1',
				'options' => [
					'style_1' => esc_html__( 'Style 1', 'theplus' ),
					'style_2'  => esc_html__( 'Style 2', 'theplus' ),
				],
				'condition'    => [
					'main_style' => [ 'progressbar' ],
				],
			]
		);
		$this->add_control(
			'pie_border_style',
			[
				'label' => esc_html__( 'Pie Chart Round Styles', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style_1',
				'options' => [
					'style_1' => esc_html__( 'Style 1', 'theplus' ),
					'style_2'  => esc_html__( 'Style 2', 'theplus' ),					
				],
				'condition'    => [
					'main_style' => [ 'pie_chart' ],
					],
			]
		);
		$this->add_control(
			'progress_bar_size',
			[
				'label' => esc_html__( 'Progress Bar Height', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'small',
				'options' => [
					'small' => esc_html__( 'Small Height', 'theplus' ),					
					'medium' => esc_html__( 'Medium Height', 'theplus' ),					
					'large' => esc_html__( 'Large Height', 'theplus' ),					
				],
				'condition'    => [
					'main_style' => [ 'progressbar' ],
					'progressbar_style' => [ 'style_1' ],
				],
			]
		);
		$this->add_control(
			'value_width',
			[
				'label' => esc_html__( 'Dynamic Value (0-100)', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%' ],
				'range' => [					
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'condition'    => [					
					'main_style' => [ 'progressbar' ],
				],
				'default' => [
					'unit' => '%',
					'size' => 59,
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'title',
			[
				'label' => esc_html__( 'Title', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'The Plus', 'theplus' ),
				'separator' => 'before',
				'dynamic' => ['active'   => true,],
			]
		);
		$this->add_control(
			'sub_title',
			[
				'label' => esc_html__( 'Sub Title', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'The Plus', 'theplus' ),
				'separator' => 'before',
				'dynamic' => ['active'   => true,],
			]
		);
		$this->add_control(
			'number',
			[
				'label' => esc_html__( 'Number', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( '59', 'theplus' ),
				'placeholder' => esc_html__( 'Enter Number Ex. 50 , 60', 'theplus' ),
				'separator' => 'before',
				'dynamic' => ['active'   => true,],
			]
		);
		$this->add_control(
			'symbol',
			[
				'label' => esc_html__( 'Prefix/Postfix Symbol', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( '%', 'theplus' ),
				'placeholder' => esc_html__( 'Enter Symbol', 'theplus' ),
				'dynamic' => ['active'   => true,],
			]
		);
		$this->add_control(
			'symbol_position',
			[
				'label' => esc_html__( 'Symbol Position', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'after',
				'options' => [
					'after' => esc_html__( 'After Number', 'theplus' ),
					'before'  => esc_html__( 'Before Number', 'theplus' ),
				],				
				'condition'    => [
					'symbol!' => '',
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'icon_progress_bar',
			[
				'label' => esc_html__( 'Icon', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'image_icon',
			[
				'label' => esc_html__( 'Select Icon', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'description' => esc_html__('You can select Icon, Custom Image using this option.','theplus'),
				'default' => 'icon',
				'options' => [
					''  => esc_html__( 'None', 'theplus' ),
					'icon' => esc_html__( 'Icon', 'theplus' ),
					'image' => esc_html__( 'Image', 'theplus' ),
					'lottie' => esc_html__( 'Lottie', 'theplus' ),						
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
				'dynamic' => ['active'   => true,],
				'media_type' => 'image',
				'condition' => [
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
				'condition' => ['image_icon' => 'image'],
			]
		);
		$this->add_control(
			'type',
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
					'type' => 'font_awesome',
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
					'type' => 'font_awesome_5',
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
					'type' => 'icon_mind',
				],
			]
		);
		$this->add_control(
			'icon_postition',
			[
				'label' => esc_html__( 'Icon Title Before after', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'before',
				'options' => [
					'before' => esc_html__( 'Before', 'theplus' ),
					'after'  => esc_html__( 'After', 'theplus' ),
				],
				'condition'    => [
					'image_icon' => [ 'icon','image','svg','lottie' ],
				],
			]
		);
		$this->add_control(
			'lottieUrl',
			[
				'label' => esc_html__( 'Lottie URL', 'theplus' ),
				'type' => Controls_Manager::URL,				
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'condition' => ['image_icon' => 'lottie'],
			]
		);
		$this->end_controls_section();
		/* Progress Bar*/
		/*<-----Style tag ----> */
		/* Icon Style*/
		$this->start_controls_section(
            'section_pie_chart_styling',
            [
                'label' => esc_html__('Pie Chart Setting', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'    => [
					'main_style' => [ 'pie_chart' ],
				],
            ]
        );
		$this->add_control(
			'pie_value',
			[
				'label' => esc_html__( 'Dynamic Value (0-1)', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%' ],
				'range' => [					
					'%' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 0.6,
				],
				'dynamic' => ['active'   => true,],
				'condition'    => [					
					'main_style' => [ 'pie_chart' ],
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'pie_size',
			[
				'label' => esc_html__( 'Pie Chart Circle Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [					
					'px' => [
						'min' => 0,
						'max' => 700,
						'step' => 2,
					],
				],
				'render_type' => 'template',
				'default' => [
					'unit' => 'px',
					'size' => 200,
				],
				'dynamic' => ['active'   => true,],
				'selectors' => [					
					'{{WRAPPER}} .pt-plus-circle' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
				'condition'    => [					
					'main_style' => [ 'pie_chart' ],
				],
			]
		);
		$this->add_control(
			'pie_thickness',
			[
				'label' => esc_html__( 'Thickness', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px' ],
				'range' => [					
					'%' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'condition'    => [					
					'main_style' => [ 'pie_chart' ],
				],
			]
		);
		$this->add_control(
			'data_empty_fill',
			[
				'label' => esc_html__( 'Pie Empty Color', 'theplus' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',				
				'condition'    => [
					'main_style' => [ 'pie_chart' ],
					'pie_chart_style!' => [ 'style_2' ],
				],
			]
		);		
		$this->add_control(
			'pie_empty_color',
			[
				'label' => esc_html__( 'pie Chart Empty Color', 'theplus' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'condition'    => [
					'main_style' => [ 'pie_chart1' ],
					'pie_chart_style!' => [ 'style_2' ],
				],
			]
		);
		$this->add_control(
			'pie_fill',
			[
				'label' => esc_html__( 'Chart Fill Color', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'classic' => [
						'title' => esc_html__( 'Classic', 'theplus' ),
						'icon' => 'eicon-paint-brush',
					],
					'gradient' => [
						'title' => esc_html__( 'Gradient', 'theplus' ),
						'icon' => 'eicon-barcode',
					],
				],
				'condition'    => [
					'main_style' => [ 'pie_chart' ],
				],
				'label_block' => false,
				'default' => 'classic',
			]
		);
		$this->add_control(
            'pie_fill_classic',
            [
                'label' => esc_html__('Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'orange',
				'condition' => [
					'main_style' => [ 'pie_chart' ],
					'pie_fill' => 'classic',
				],
				
            ]
        );
		$this->add_control(
            'pie_fill_gradient_color1',
            [
                'label' => esc_html__('Color 1', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'orange',
				'condition' => [
					'main_style' => [ 'pie_chart' ],
					'pie_fill' => 'gradient',
				],
				
            ]
        );
		$this->add_control(
            'pie_fill_gradient_color2',
            [
                'label' => esc_html__('Color 2', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'green',
				'condition' => [
					'main_style' => [ 'pie_chart' ],
					'pie_fill' => 'gradient',
				],
            ]
        );
		$this->end_controls_section();
		$this->start_controls_section(
            'section_title_styling',
            [
                'label' => esc_html__('Title Setting', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .progress_bar .prog-title.prog-icon .progress_bar-title,{{WRAPPER}} .pt-plus-pie_chart .progress_bar-title',
			]
		);
		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY
                ],
				'selectors' => [
					'{{WRAPPER}} span.progress_bar-title,
					{{WRAPPER}} .progress_bar-media.large .prog-title.prog-icon.large .progres-ims,
					{{WRAPPER}} .progress_bar-media.large .prog-title.prog-icon.large .progress_bar-title' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'title_margin',
			[
				'label' => esc_html__( 'Title Left Margin', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%' ],
				'range' => [					
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} span.progress_bar-title,
					{{WRAPPER}} .progress_bar-media.large .prog-title.prog-icon.large .progres-ims,
					{{WRAPPER}} .progress_bar-media.large .prog-title.prog-icon.large .progress_bar-title' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition'    => [					
					'main_style' => [ 'progressbar' ],
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
            'section_subtitle_styling',
            [
                'label' => esc_html__('Sub Title Setting', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'subtitle_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .progress_bar .prog-title.prog-icon .progress_bar-sub_title,{{WRAPPER}} .pt-plus-pie_chart .progress_bar-sub_title',
			]
		);
		$this->add_control(
			'subtitle_color',
			[
				'label' => esc_html__( 'Sub Title Color', 'theplus' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .progress_bar-sub_title' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
            'section_number_styling',
            [
                'label' => esc_html__('Number Setting', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'number_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),				
				'selector' => '{{WRAPPER}} .progress_bar .counter-number .theserivce-milestone-number',
			]
		);
		$this->add_control(
			'number_color',
			[
				'label' => esc_html__( 'Number Color', 'theplus' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY
                ],
				'selectors' => [
					'{{WRAPPER}} .progress_bar .counter-number .theserivce-milestone-number' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control('number_margin',
			[
				'label' => esc_html__( 'Margin Top', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%' ],
				'range' => [	
					'%' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-pie_chart.style-3 .pie_chart .counter-number ' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pie_chart_style' => [ 'style_3' ],
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
            'section_number_pre_pos_styling',
            [
                'label' => esc_html__('Number Prefix/Postfix', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'prefix_postfix_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .progress_bar .counter-number .theserivce-milestone-symbol',
			]
		);
		$this->add_control(
			'prefix_postfix_symbol_color',
			[
				'label' => esc_html__( 'Prefix/Postfix Color', 'theplus' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .progress_bar .counter-number .theserivce-milestone-symbol' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
            'section_icon_styling',
            [
                'label' => esc_html__('Icon/Image Setting', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
			'icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'condition'    => [					
					'image_icon' => [ 'icon' ],
				],
				'selectors' => [
					'{{WRAPPER}} span.progres-ims' => 'color: {{VALUE}}',
					'{{WRAPPER}} span.progres-ims svg' => 'fill: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px' ],
				'range' => [					
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'condition'    => [					
					'image_icon' => [ 'icon' ],
				],
				'selectors' => [
					'{{WRAPPER}} .progress_bar .prog-title.prog-icon span.progres-ims,{{WRAPPER}} .pt-plus-circle .pianumber-css .progres-ims,{{WRAPPER}} .pt-plus-pie_chart .pie_chart .progres-ims' => 'font-size: {{SIZE}}{{UNIT}};','{{WRAPPER}} .progress_bar .prog-title.prog-icon span.progres-ims svg,{{WRAPPER}} .pt-plus-circle .pianumber-css .progres-ims svg,{{WRAPPER}} .pt-plus-pie_chart .pie_chart .progres-ims svg' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',					
				],
				
			]
		);
		$this->add_control(
			'image_size',
			[
				'label' => esc_html__( 'Image Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px' ],				
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
				'condition'    => [					
					'image_icon' => [ 'image' ],
				],
				'selectors' => [
					'{{WRAPPER}} .progress_bar .progres-ims img.progress_bar-img' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',					
				],
				
			]
		);
		$this->add_responsive_control(
			'image_border_radius',
			[
				'label'      => esc_html__( 'Image Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .progress_bar .progres-ims img.progress_bar-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'    => [					
					'image_icon' => [ 'image' ],
				],
			]
		);
		$this->end_controls_section();
		/*lottie style*/
		$this->start_controls_section(
            'section_lottie_styling',
            [
                'label' => esc_html__('Lottie', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => ['image_icon' => 'lottie'],
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
		$this->start_controls_section(
            'section_progress_bar_styling',
            [
                'label' => esc_html__('Progress Bar Setting', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'    => [					
					'main_style' => [ 'progressbar' ],
				],
            ]
        );
		$this->add_control(
			'progress_bar_margin',
			[
				'label' => esc_html__( 'Progress Bar Top Margin', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%' ],
				'range' => [					
					'%' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .progress_bar-skill.skill-fill' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition'    => [					
					'main_style' => [ 'progressbar' ],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'progress_filled_color',
				'label' => esc_html__( 'Filled Color', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .progress_bar-skill-bar-filled',
			]
		);
		$this->add_control(
			'progress_empty_color',
			[
				'label' => esc_html__( 'Empty Color', 'theplus' ),
				'type' => \Elementor\Controls_Manager::COLOR,
			]
		);
		$this->add_control(
			'progress_seprator_color',
			[
				'label' => esc_html__( 'Separator Color', 'theplus' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .progress-style_2 .progress_bar-skill-bar-filled:after' => 'border-color: {{VALUE}}',
				],
				'condition'    => [					
					'progressbar_style' => [ 'style_2' ],
				],
			]
		);
		$this->end_controls_section();
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

		/*--On Scroll View Animation ---*/
			include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';

		/*--Plus Extra ---*/
			$PlusExtra_Class = "";
			include THEPLUS_PATH. 'modules/widgets/theplus-widgets-extra.php';

		$main_style = $settings['main_style'];					
		$pie_chart_style = $settings['pie_chart_style'];					
		$pie_border_style = $settings['pie_border_style'];		
		$pie_empty_color = ($settings['pie_empty_color']!='') ? $settings['pie_empty_color'] : '#8072fc';
		$progress_empty_color = ($settings['progress_empty_color']!='') ? $settings['progress_empty_color'] : '#8072fc';
		
		$progressbar_style = $settings['progressbar_style'];					
		$progress_bar_size = $settings['progress_bar_size'];												
		$pie_size = (!empty($settings['pie_size']['size'])) ? $settings['pie_size']['size'] : 200;												
		$title = $settings['title'];					
		$subtitle = $settings['sub_title'];					
		$image_icon = $settings['image_icon'];
						
		
		$title_content='';
		if(!empty($title)){
			 $title_content= '<span class="progress_bar-title"> '.esc_html($title).' </span>';
		}

		$subtitle_content='';
		if(!empty($subtitle)){
			 $subtitle_content= '<div class="progress_bar-sub_title"> '.esc_html($subtitle).' </div>';;
		}
		if($pie_size != ''){
		$inner_width = ' style="';
			$inner_width .= 'width: '.esc_attr($pie_size).'px;';
			$inner_width .= 'height: '.esc_attr($pie_size).'px;';
		$inner_width .= '"';
		}
		
		$progress_bar_img='';
		if($image_icon == 'image' && !empty($settings['select_image']["url"])){			
			$select_image=$settings['select_image']['id'];			
			$add_image= tp_get_image_rander( $select_image,$settings['select_image_thumbnail_size'], [ 'class' => 'progress_bar-img' ] );$progress_bar_img='<span class="progres-ims">'.$add_image.'</span>';
		}
		$icons = '';
		if($image_icon == 'icon'){
			//$icons = '';
			if($settings["type"]=='font_awesome'){
				$icons = $settings["icon_fontawesome"];
			}else if($settings["type"]=='font_awesome_5'){
				ob_start();
				\Elementor\Icons_Manager::render_icon( $settings['icon_fontawesome_5'], [ 'aria-hidden' => 'true' ]);
				$icons = ob_get_contents();
				ob_end_clean();
			}else if($settings["type"]=='icon_mind'){
				$icons = $settings["icons_mind"];
			}
			
			if(!empty($settings["type"]) && $settings["type"]=='font_awesome_5' && !empty($settings['icon_fontawesome_5'])){
				$progress_bar_img = '<span class="progres-ims"><span>'.$icons.'</span></span>';
			}else{
				$progress_bar_img = '<span class="progres-ims"><i class=" '.esc_attr($icons).'"></i></span>';
			}			
		}

		//lottie
		if(!empty($image_icon) && $image_icon == 'lottie'){
			$ext = pathinfo($settings['lottieUrl']['url'], PATHINFO_EXTENSION);
			if($ext!='json'){
				$icons = '<h3 class="theplus-posts-not-found">'.esc_html__("Opps!! Please Enter Only JSON File Extension.",'theplus').'</h3>';
			}else{
				$lottiedisplay = isset($settings['lottiedisplay']) ? $settings['lottiedisplay'] : 'inline-block';
				$lottieWidth = isset($settings['lottieWidth']['size']) ? $settings['lottieWidth']['size'] : 25;
				$lottieHeight = isset($settings['lottieHeight']['size']) ? $settings['lottieHeight']['size'] : 25;
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
				$icons ='<lottie-player src="'.esc_url($settings['lottieUrl']['url']).'" style="display: '.esc_attr($lottiedisplay).'; width: '.esc_attr($lottieWidth).'px; height: '.esc_attr($lottieHeight).'px;" '.esc_attr($lottieLoopValue).'  speed="'.esc_attr($lottieSpeed).'" '.esc_attr($lottieAnim).'></lottie-player>';
			}
			$progress_bar_img = '<span class="progres-ims"><span>'.$icons.'</span></span>';
		}
		
		if(!empty($image_icon) && $image_icon == 'lottie'){
			if(!empty($settings['icon_postition']) && $settings['icon_postition'] == 'after'){
				$icon_text = $title_content.$icons.$subtitle_content;
			}else if(!empty($settings['icon_postition']) && $settings['icon_postition'] == 'before'){
				$icon_text = $icons.$title_content.$subtitle_content;
			}
		}
		if($settings['icon_postition'] == 'after'){
			$icon_text = $title_content.$progress_bar_img.$subtitle_content;
		}else{
			$icon_text = $progress_bar_img.$title_content.$subtitle_content;
		}
		
		if(!empty($settings['symbol'])) {
		  if($settings['symbol_position']=="after"){
			$symbol2 = '<span class="theserivce-milestone-number icon-milestone" data-counterup-nums="'.esc_attr($settings['number']).'">'.esc_html($settings['number']).'</span><span class="theserivce-milestone-symbol">'.esc_html($settings['symbol']).'</span>';
			}elseif($settings['symbol_position']=="before"){
				$symbol2 = '<span class="theserivce-milestone-symbol">'.esc_html($settings['symbol']).'</span><span class="theserivce-milestone-number" data-counterup-nums="'.esc_attr($settings['number']).'">'.esc_html($settings['number']).'</span>';
			}
		} else {
			$symbol2 = '<span class="theserivce-milestone-number icon-milestone" data-counterup-nums="'.esc_attr($settings['number']).'">'.esc_html($settings['number']).'</span>';
		}
		if($settings['pie_fill'] =='gradient'){
			$data_fill_color = ' data-fill="{&quot;gradient&quot;: [&quot;' .esc_attr($settings['pie_fill_gradient_color1']) . '&quot;,&quot;' . esc_attr($settings['pie_fill_gradient_color2']) . '&quot;]}" ';
		}else{
		$data_fill_color = ' data-fill="{&quot;color&quot;: &quot;'.esc_attr($settings['pie_fill_classic']).'&quot;}" ';
		}
		if($main_style == 'pie_chart_style'){
			if($pie_chart_style == 'style_1'){
				if($symbol2!= ''){
				$number_markup = '<h5 class="counter-number">'.$progress_bar_img.$symbol2.'</h5>';
				}
			}else{
				if($symbol2!= ''){
				$number_markup = '<h5 class="counter-number">'.$symbol2.'</h5>';
				}
			}
		}else{
			if($symbol2!= ''){
				$number_markup = '<h5 class="counter-number">'.$symbol2.'</h5>';
				}
		}
		$pie_border_after='';
		if($pie_border_style == "style_2") {
			$pie_border_after = "pie_border_after";
			$pie_empty_color1 = "transparent";
		}else{
			$pie_empty_color1 = $pie_empty_color;
		}
		
		$progress_width= (!empty($settings["value_width"]["size"])) ? $settings["value_width"]["size"].'%' : '';
		
		$uid=uniqid("progress_bar");
		$progress_bar ='<div class="progress_bar pt-plus-peicharts progress-skill-bar '.esc_attr($uid).' progress_bar-'.esc_attr($main_style).' '.esc_attr($animated_class).'" '.$animation_attr.' data-empty="'.esc_attr($pie_empty_color).'" data-uid="'.esc_attr($uid).'" >';
			if($main_style == 'progressbar'){
				
				$lz1 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['progress_filled_color_image']) : '';
				
				if($progressbar_style == 'style_1'){			
					if($progress_bar_size != 'large'){
						$progress_bar .= '<div class="progress_bar-media">';
							$progress_bar .= '<div class="prog-title prog-icon">';
								$progress_bar .= $icon_text;
							$progress_bar .= '</div>'; 	
							$progress_bar .=$number_markup;
						$progress_bar .= '</div>';	
							
							$progress_bar .= '<div class="progress_bar-skill skill-fill '.esc_attr($progress_bar_size).'" style="background-color:'.esc_attr($progress_empty_color).'">';
								$progress_bar .= '<div class="progress_bar-skill-bar-filled '.$lz1.'" data-width="'.esc_attr($progress_width).'">	</div>';
							$progress_bar .= '</div>';
					}else{
							$progress_bar .= '<div class="progress_bar-skill skill-fill '.esc_attr($progress_bar_size).'" style="background-color:'.esc_attr($progress_empty_color).'" >';
								$progress_bar .= '<div class="progress_bar-skill-bar-filled '.$lz1.'" data-width="'.esc_attr($progress_width).'">	</div>';
								$progress_bar .= '<div class="progress_bar-media '.esc_attr($progress_bar_size).' ">';	
									$progress_bar .= '<div class="prog-title prog-icon '.esc_attr($progress_bar_size).'">';
										$progress_bar .= $progress_bar_img.$title_content; 	
									$progress_bar .= '</div>';
									$progress_bar .=$number_markup;
								$progress_bar .= '</div>';
							$progress_bar .= '</div>';
							
						}
				}else if($progressbar_style == 'style_2'){
						$progress_bar .= '<div class="progress_bar-media">';	
							$progress_bar .= '<div class="prog-title prog-icon">';
								$progress_bar .= $icon_text;
							$progress_bar .= '</div>'; 	
							$progress_bar .=$number_markup;
						$progress_bar .= '</div>';	
						$progress_bar .= '<div class="progress_bar-skill skill-fill progress-'.esc_attr($progressbar_style).'" style="background-color:'.esc_attr($progress_empty_color).'">';
							$progress_bar .= '<div class="progress_bar-skill-bar-filled '.$lz1.'"  data-width="'.esc_attr($progress_width).'">	</div>';
						$progress_bar .= '</div>';
				
				}
			}
			
			if(!empty($settings['data_empty_fill'])){
				$data_empty_fill=$settings['data_empty_fill'];
			}else{
				$data_empty_fill='transparent';
			}
			
			if($main_style == 'pie_chart'){
					$progress_bar .= '<div class="pt-plus-piechart '.esc_attr($pie_border_after).' pie-'.esc_attr($pie_chart_style).'"  '.$data_fill_color.' data-emptyfill="'.esc_attr($data_empty_fill).'" data-value="'.esc_attr($settings['pie_value']['size']).'"  data-size="'.esc_attr($settings['pie_size']['size']).'" data-thickness="'.esc_attr($settings['pie_thickness']['size']).'"  data-animation-start-value="0"  data-reverse="false">';
					
						$progress_bar .= '<div class="pt-plus-circle" '.$inner_width.'>';
							$progress_bar .='<div class="pianumber-css" >';
							if($pie_chart_style != 'style_3'){
								$progress_bar .= $number_markup;
							}else{	
								$progress_bar .= $progress_bar_img;
							}
							$progress_bar .= '</div>';	
						$progress_bar .= '</div>';
					$progress_bar .= '</div>';
						if($pie_chart_style == 'style_1'){
							$progress_bar .= '<div class="pt-plus-pie_chart" >';
								$progress_bar .= $title_content;
								$progress_bar .= $subtitle_content;
							$progress_bar .= '</div>';	
						}else if($pie_chart_style == 'style_2'){
							$progress_bar .= '<div class="pt-plus-pie_chart style-2" >';
								$progress_bar .= '<div class="pie_chart " >';
								if(!empty($settings['icon_postition']) && $settings['icon_postition'] == 'before'){
									$progress_bar .= '<div class="pie_chart " >';
										$progress_bar .= $progress_bar_img;
									$progress_bar .= '</div>';	
								}
								$progress_bar .= '<div class="pie_chart-style2">';
									$progress_bar .= $title_content;
									$progress_bar .= $subtitle_content;
								$progress_bar .= '</div>';
								if(!empty($settings['icon_postition']) && $settings['icon_postition'] == 'after'){
									$progress_bar .= '<div class="pie_chart " >';
										$progress_bar .= $progress_bar_img;
									$progress_bar .= '</div >';	
								}
								$progress_bar .= '</div>';
							$progress_bar .= '</div>';
						}else if($pie_chart_style == 'style_3'){
							$progress_bar .= '<div class="pt-plus-pie_chart style-3">';
								$progress_bar .= '<div class="pie_chart " >';
									$progress_bar .= $number_markup;
								$progress_bar .= '</div >';	
								$progress_bar .= '<div class="pie_chart-style3">';
								$progress_bar .= $title_content;
								$progress_bar .= $subtitle_content;
								$progress_bar .= '</div>';
									
							$progress_bar .= '</div>';	
						}
						
					}
		$progress_bar .='</div>';
		$progress_bar .= '<script type="text/javascript">( function ( $ ) { 
		"use strict";
		$( document ).ready(function() {
			var elements = document.querySelectorAll(".pt-plus-piechart");
			Array.prototype.slice.apply(elements).forEach(function(el) {
				var $el = jQuery(el);
				//$el.circleProgress({value: 0});
				new Waypoint({
					element: el,
					handler: function() {
						if(!$el.hasClass("done-progress")){
						setTimeout(function(){
							$el.circleProgress({
								value: $el.data("value"),
								emptyFill: $el.data("emptyfill"),
								startAngle: -Math.PI/4*2,
							});
							//  this.destroy();
						}, 800);
						$el.addClass("done-progress");
						}
					},
					offset: "80%"
				});
			});
		});
		$(window).on("load resize scroll", function(){
			$(".pt-plus-peicharts").each( function(){
				var height=$("canvas",this).outerHeight();
				var width=$("canvas",this).outerWidth();
				$(".pt-plus-circle",this).css("height",height+"px");
				$(".pt-plus-circle",this).css("width",width+"px");
			});
		});
	} ( jQuery ) );</script>';
		echo $before_content.$progress_bar.$after_content;
	}
	
    protected function content_template() {}
}