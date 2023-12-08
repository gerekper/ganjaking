<?php 
/*
Widget Name: Number Counter 
Description: Display style of count numbers.
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

class ThePlus_Number_Counter extends Widget_Base {
		
	public function get_name() {
		return 'tp-number-counter';
	}

    public function get_title() {
        return esc_html__('Number Counter', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-hashtag theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-essential');
    }

    protected function register_controls() {
		
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Style Counter', 'theplus' ),
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
			'title',
			[
				'label' => esc_html__( 'Title', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Title', 'theplus' ),
				'dynamic' => ['active'   => true,],
			]
		);		
		$this->add_responsive_control(
			'alignment',
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
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'default' => 'center',
				'prefix_class' => 'text-%s',
				'label_block' => false,
				'toggle' => false,
				'condition' => [
					'style' => 'style-1',
				],
			]
		);
		$this->add_responsive_control(
			'alignment_2',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
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
				'prefix_class' => 'text-%s',
				'label_block' => false,
				'toggle' => false,
				'condition' => [
					'style' => 'style-2',
				],
			]
		);
		$this->end_controls_section();
		/*Number Content*/
		
		$this->start_controls_section(
			'number_content_section',
			[
				'label' => esc_html__( 'Number Counting', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'max_number',
			[
				'label' => esc_html__( 'Number Value', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'default' => esc_html__( '1000', 'theplus' ),
				'description' => esc_html__('Enter the value of number/digits you want to showcase in icon counter. E.g, 100,999,etc..','theplus' ),
				'dynamic' => ['active'   => true,],
			]
		);
		$this->add_control(
			'min_number',
			[
				'label' => esc_html__( 'Animation Starting Number Value', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'default' => esc_html__( '0', 'theplus' ),
				'description' => esc_html__('Enter the digit from which you want to start the animation on scroll. E.g. 0,10,80, etc','theplus' ),
				'dynamic' => ['active'   => true,],				
			]
		);
		$this->add_control(
            'increment_number',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Number Gap For Animation', 'theplus'),
				'default' => [
					'unit' => '',
					'size' => 5,
				],
				'range' => [
					'' => [
						'min'	=> 0,
						'max'	=> 5000,
						'step' => 5,
					],
				],
				'dynamic' => ['active'   => true,],
				'description' => esc_html__('Enter the value of number you want while animation.','theplus' ),
            ]
        );
		$this->add_control(
            'delay_number',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Time delay In Animation Gap', 'theplus'),
				'default' => [
					'unit' => '',
					'size' => 5,
				],
				'range' => [
					'' => [
						'min'	=> 0,
						'max'	=> 10000,
						'step' => 10,
					],
				],
				'dynamic' => ['active'   => true,],
            ]
        );
		
		$this->add_control(
			'symbol',
			[
				'label' => esc_html__( 'Symbol', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'description' => esc_html__('You can add any value in this option which will be setup as prefix or postfix on Digits. e.g. +,%,etc.','theplus' ),
				'separator' => 'before',
			]
		);
		$this->add_control(
			'symbol_position',
			[
				'label' => esc_html__( 'Symbol Position', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'after',
				'options' => [
					'after'  => esc_html__( 'After Number', 'theplus' ),
					'before' => esc_html__( 'Before Number', 'theplus' ),
				],
				'description' => esc_html__('You can Select Symbol position using this option.','theplus'),
			]
		);
		$this->end_controls_section();
		/*Number Content*/
		/*Icon Content*/
		$this->start_controls_section(
			'icon_content_section',
			[
				'label' => esc_html__( 'Icon', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'icon_type',
			[
				'label' => esc_html__( 'Select Icon', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'icon',
				'options' => [
					''  => esc_html__( 'None', 'theplus' ),
					'icon'  => esc_html__( 'Icon', 'theplus' ),
					'image'  => esc_html__( 'Image', 'theplus' ),
					'svg'  => esc_html__( 'Svg', 'theplus' ),
					'lottie' => esc_html__( 'Lottie', 'theplus' ),
				],
				'description' => esc_html__('You can select Icon, Custom Image or SVG or Lottie using this option.','theplus'),
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
					'icon_type' => 'icon',
				],
			]
		);
		$this->add_control(
			'icon_fontawesome',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICON,
				'default' => 'fa fa-download',
				'condition' => [
					'icon_type' => 'icon',
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
					'value' => 'fas fa-download',
					'library' => 'solid',
				],
				'condition' => [
					'icon_type' => 'icon',
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
					'icon_type' => 'icon',
					'icon_font_style' => 'icon_mind',
				],
			]
		);
		$this->add_control(
			'svg_icon',
			[
				'label' => esc_html__( 'SVG Select Option', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'img',
				'options' => [
					'img'  => esc_html__( 'Custom Upload', 'theplus' ),
					'svg' => esc_html__( 'Pre Built SVG Icon', 'theplus' ),
				],
				'condition' => [
					'icon_type' => 'svg',
				],
			]
		);
		$this->add_control(
			'svg_image',
			[
				'label' => esc_html__( 'Only SVG', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'description' => esc_html__('Select Only .svg File from media library.','theplus'),
				'default' => [
					'url' => '',
				],
				'media_type' => 'image',
				'condition' => [
					'icon_type' => 'svg',
					'svg_icon' => 'img',
				],
			]
		);
		$this->add_control(
			'svg_d_icon',
			[
				'label' => esc_html__( 'Select SVG Icon', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'app.svg',
				'options' => theplus_svg_icons_list(),
				'condition' => [
					'icon_type' => 'svg',
					'svg_icon' => 'svg',
				],
			]
		);
		$this->add_control(
			'icon_image',
			[
				'label' => esc_html__( 'Choose Image', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'dynamic' => ['active'   => true,],
				'condition' => [
					'icon_type' => 'image',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'icon_image_thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'after',
				'condition' => [
					'icon_type' => 'image',
				],
			]
		);
		$this->add_control(
			'lottieUrl',
			[
				'label' => esc_html__( 'Lottie URL', 'theplus' ),
				'type' => Controls_Manager::URL,				
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'condition' => ['icon_type' => 'lottie'],
			]
		);
		$this->add_control(
			'url_link',
			[
				'label' => esc_html__( 'Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'theplus' ),
				'show_external' => true,
				'dynamic' => ['active'   => true,],
				'default' => [
					'url' => '',
				],
			]
		);
		$this->end_controls_section();
		/*Icon Content*/
		/*svg style*/
		$this->start_controls_section(
            'section_svg_styling',
            [
                'label' => esc_html__('SVG', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'icon_type' => 'svg',
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
            ]
        );
		$this->add_control(
            'max_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Max Width SVG', 'theplus'),
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
				'condition' => [
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
			]
		);
		$this->end_controls_section();
		/*svg style*/
		/*icon style*/
		$this->start_controls_section(
            'section_icon_styling',
            [
                'label' => esc_html__('Icon', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'icon_type'	=> 'icon',
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
					'{{WRAPPER}} .plus-number-counter .counter-icon-inner .counter-icon' => 'font-size: {{SIZE}}{{UNIT}} !important;',
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
					'{{WRAPPER}} .plus-number-counter .counter-icon-inner' => 'width: {{SIZE}}{{UNIT}} !important;height: {{SIZE}}{{UNIT}} !important;line-height: {{SIZE}}{{UNIT}} !important;',
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
					'{{WRAPPER}} .plus-number-counter .counter-icon-inner .counter-icon' => 'color: {{VALUE}}',
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
					'{{WRAPPER}} .plus-number-counter .counter-icon-inner .counter-icon' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{icon_gradient_color1.VALUE}} {{icon_gradient_color1_control.SIZE}}{{icon_gradient_color1_control.UNIT}}, {{icon_gradient_color2.VALUE}} {{icon_gradient_color2_control.SIZE}}{{icon_gradient_color2_control.UNIT}})',
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
					'{{WRAPPER}} .plus-number-counter .counter-icon-inner .counter-icon' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{icon_gradient_color1.VALUE}} {{icon_gradient_color1_control.SIZE}}{{icon_gradient_color1_control.UNIT}}, {{icon_gradient_color2.VALUE}} {{icon_gradient_color2_control.SIZE}}{{icon_gradient_color2_control.UNIT}})',
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
				'selector'  => '{{WRAPPER}} .plus-number-counter .counter-icon-inner',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'icon_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-number-counter .counter-icon-inner' => 'border-color: {{VALUE}}',
				],
				'separator' => 'before',
				'condition' => [
					'icon_style!' => ['','hexagon','pentagon','square-rotate'],
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
					'{{WRAPPER}} .plus-number-counter .counter-icon-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
				'condition' => [
					'icon_style!' => ['hexagon','pentagon','square-rotate'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_box_shadow',
				'selector' => '{{WRAPPER}} .plus-number-counter .counter-icon-inner',
				'condition' => [
					'icon_style!' => ['hexagon','pentagon','square-rotate'],
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
					'{{WRAPPER}} .plus-number-counter .number-counter-inner-block:hover .counter-icon-inner .counter-icon' => 'color: {{VALUE}}',
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
					'{{WRAPPER}} .plus-number-counter .number-counter-inner-block:hover .counter-icon-inner .counter-icon' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{icon_hover_gradient_color1.VALUE}} {{icon_hover_gradient_color1_control.SIZE}}{{icon_hover_gradient_color1_control.UNIT}}, {{icon_hover_gradient_color2.VALUE}} {{icon_hover_gradient_color2_control.SIZE}}{{icon_hover_gradient_color2_control.UNIT}})',
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
					'{{WRAPPER}} .plus-number-counter .number-counter-inner-block:hover .counter-icon-inner .counter-icon' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{icon_hover_gradient_color1.VALUE}} {{icon_hover_gradient_color1_control.SIZE}}{{icon_hover_gradient_color1_control.UNIT}}, {{icon_hover_gradient_color2.VALUE}} {{icon_hover_gradient_color2_control.SIZE}}{{icon_hover_gradient_color2_control.UNIT}})',
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
				'selector'  => '{{WRAPPER}} .plus-number-counter .number-counter-inner-block:hover .counter-icon-inner',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'icon_border_hover_color',
			[
				'label' => esc_html__( 'Hover Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-number-counter .number-counter-inner-block:hover .counter-icon-inner' => 'border-color: {{VALUE}}',
				],
				'separator' => 'before',
				'condition' => [
					'icon_style!' => ['','hexagon','pentagon','square-rotate'],
				],
			]
		);
		$this->add_responsive_control(
			'icon__hover_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-number-counter .number-counter-inner-block:hover .counter-icon-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
				'condition' => [
					'icon_style!' => ['hexagon','pentagon','square-rotate'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_hover_box_shadow',
				'selector' => '{{WRAPPER}} .plus-number-counter .number-counter-inner-block:hover .counter-icon-inner',
				'condition' => [
					'icon_style!' => ['hexagon','pentagon','square-rotate'],
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();		
		$this->end_controls_section();		
		/*icon style*/
		/*icon Image*/
		$this->start_controls_section(
            'section_icon_image_styling',
            [
                'label' => esc_html__('Image', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'icon_type'	=> 'image',
				],
            ]
        );
		$this->add_responsive_control(
            'image_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Image Width', 'theplus'),
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
					'{{WRAPPER}} .plus-number-counter .counter-image-inner' => 'max-width: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->end_controls_section();
		/*icon Image*/
		/*lottie style*/
		$this->start_controls_section(
            'section_lottie_styling',
            [
                'label' => esc_html__('Lottie', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => ['icon_type' => 'lottie'],
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
		/*lottie style*/
		/*title style*/
		$this->start_controls_section(
            'section_title_styling',
            [
                'label' => esc_html__('Title', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-number-counter .number-counter-inner-block .counter-title,{{WRAPPER}} .plus-number-counter .number-counter-inner-block .counter-title a',
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
					'{{WRAPPER}} .plus-number-counter .number-counter-inner-block .counter-title,{{WRAPPER}} .plus-number-counter .number-counter-inner-block .counter-title a' => 'color: {{VALUE}}',
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
					'{{WRAPPER}} .plus-number-counter .number-counter-inner-block .counter-title,{{WRAPPER}} .plus-number-counter .number-counter-inner-block .counter-title a' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{title_gradient_color1.VALUE}} {{title_gradient_color1_control.SIZE}}{{title_gradient_color1_control.UNIT}}, {{title_gradient_color2.VALUE}} {{title_gradient_color2_control.SIZE}}{{title_gradient_color2_control.UNIT}})',
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
					'{{WRAPPER}} .plus-number-counter .number-counter-inner-block .counter-title,{{WRAPPER}} .plus-number-counter .number-counter-inner-block .counter-title a' => 'background-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{title_gradient_color1.VALUE}} {{title_gradient_color1_control.SIZE}}{{title_gradient_color1_control.UNIT}}, {{title_gradient_color2.VALUE}} {{title_gradient_color2_control.SIZE}}{{title_gradient_color2_control.UNIT}})',
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
					'{{WRAPPER}} .plus-number-counter .number-counter-inner-block:hover .counter-title,{{WRAPPER}} .plus-number-counter .number-counter-inner-block:hover .counter-title a' => 'color: {{VALUE}}',
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
					'{{WRAPPER}} .plus-number-counter .number-counter-inner-block:hover .counter-title,{{WRAPPER}} .plus-number-counter .number-counter-inner-block:hover .counter-title a' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{title_hover_gradient_color1.VALUE}} {{title_hover_gradient_color1_control.SIZE}}{{title_hover_gradient_color1_control.UNIT}}, {{title_hover_gradient_color2.VALUE}} {{title_hover_gradient_color2_control.SIZE}}{{title_hover_gradient_color2_control.UNIT}})',
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
					'{{WRAPPER}} .plus-number-counter .number-counter-inner-block:hover .counter-title,{{WRAPPER}} .plus-number-counter .number-counter-inner-block:hover .counter-title a' => 'background-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{title_hover_gradient_color1.VALUE}} {{title_hover_gradient_color1_control.SIZE}}{{title_hover_gradient_color1_control.UNIT}}, {{title_hover_gradient_color2.VALUE}} {{title_hover_gradient_color2_control.SIZE}}{{title_hover_gradient_color2_control.UNIT}})',
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
					'{{WRAPPER}} .plus-number-counter .number-counter-inner-block .counter-title' => 'margin-top : {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .plus-number-counter .number-counter-inner-block .counter-title' => 'margin-bottom : {{SIZE}}{{UNIT}}',
				],
            ]
        );
		
		$this->end_controls_section();
		/*title style*/
		/* digits */
		$this->start_controls_section(
				'section_digit_option',
				[
					'label' => esc_html__('Digit', 'theplus'),
					'tab' => Controls_Manager::TAB_STYLE,
				]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'digit_typography',
				'label' => esc_html__( 'Digit Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .plus-number-counter .number-counter-inner-block .counter-number',
	       ]
		);
		$this->add_control(
			'style_color',
			[
				'label' => esc_html__( 'Digit Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-number-counter .number-counter-inner-block .counter-number' => 'color: {{VALUE}}',
				],
			]
		);
        $this->add_control(
			'style_hover_color',
			[
				'label' => esc_html__( 'Digit Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-number-counter .number-counter-inner-block:hover .counter-number' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
            'number_top_space',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Number Top Space', 'theplus'),
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
					'{{WRAPPER}} .plus-number-counter .number-counter-inner-block .counter-number' => 'margin-top : {{SIZE}}{{UNIT}}',
				],
            ]
        );
       $this->end_controls_section();
	    /* digits */
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
			]
		);
		$this->add_control(
			'border_style',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'options' => theplus_get_border_style(),
				'default' => 'solid',
				'selectors'  => [
					'{{WRAPPER}} .plus-number-counter .number-counter-inner-block' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_border_style' );
		$this->start_controls_tab(
			'tab_border_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'box_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .plus-number-counter .number-counter-inner-block' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .plus-number-counter .number-counter-inner-block' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .plus-number-counter .number-counter-inner-block' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_border_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'box_border_hover_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .plus-number-counter .number-counter-inner-block:hover' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .plus-number-counter .number-counter-inner-block:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector'  => '{{WRAPPER}} .plus-number-counter .number-counter-inner-block',
				
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
				'selector'  => '{{WRAPPER}} .plus-number-counter .number-counter-inner-block:hover',
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
				'selector' => '{{WRAPPER}} .plus-number-counter .number-counter-inner-block',
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
				'selector' => '{{WRAPPER}} .plus-number-counter .number-counter-inner-block:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*background option*/
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
					'{{WRAPPER}} .plus-number-counter .number-counter-inner-block' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'vertical_center',
			[
				'label' => esc_html__( 'Vertical Center', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'style' => ['style-2'],
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
		$this->end_controls_section();
		/*Extra options*/
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
		$style = $settings['style'];
		$alignment = 'text-'.$settings['alignment'];

		/*--On Scroll View Animation ---*/
			include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';

		/*--Plus Extra ---*/
			$PlusExtra_Class = "";
			include THEPLUS_PATH. 'modules/widgets/theplus-widgets-extra.php';

		//content Hover effect
		$hover_class  = $hover_attr = '';
		$hover_uniqid = uniqid('hover-effect');
		if ($settings["box_hover_effects"] == "float_shadow" || $settings["box_hover_effects"] == "grow_shadow" || $settings["box_hover_effects"] == "shadow_radial") {
			$hover_attr .= 'data-hover_uniqid="' . esc_attr($hover_uniqid) . '" ';
			$hover_attr .= ' data-hover_shadow="' . esc_attr($settings["box_hover_shadow_color"]) . '" ';
			$hover_attr .= ' data-content_hover_effects="' . esc_attr($settings["box_hover_effects"]) . '" ';
		}
		$box_hover_effects=$settings["box_hover_effects"];
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
		
		//url
		$icon_link_a=$icon_link_a_close='';
		if ( ! empty( $settings['url_link']['url'] ) ) {
			$this->add_render_attribute( 'url_link', 'href', $settings['url_link']['url'] );
			if ( $settings['url_link']['is_external'] ) {
				$this->add_render_attribute( 'url_link', 'target', '_blank' );
			}
			if ( $settings['url_link']['nofollow'] ) {
				$this->add_render_attribute( 'url_link', 'rel', 'nofollow' );
			}
			$icon_link_a= '<a '.$this->get_render_attribute_string( "url_link" ).'>';
			$icon_link_a_close= '</a>';
		}
		
		//Symbol and Number
		$min_number = $settings['min_number'];
		$max_number = $settings['max_number'];
		$symbol = $settings['symbol'];
		$delay_number = $settings['delay_number']["size"];
		$increment_number = $settings['increment_number']["size"];
		$symbol_position = $settings['symbol_position'];
		if(!empty($symbol)) {
		  if($symbol_position == "after"){
				$number_symbol = '<span class="counter-number-inner numscroller" data-min="'.esc_attr($min_number).'" data-max="'.esc_attr($max_number).'" data-delay="'.esc_attr($delay_number).'" data-increment="'.esc_attr($increment_number).'">'.esc_html($min_number).'</span><span>'.esc_html($symbol).'</span>';
			}else if($symbol_position == "before"){
				$number_symbol = '<span>'.esc_html($symbol).'</span><span class="counter-number-inner numscroller"  data-min="'.esc_attr($min_number).'" data-max="'.esc_attr($max_number).'" data-delay="'.esc_attr($delay_number).'" data-increment="'.esc_attr($increment_number).'">'.esc_html($min_number).'</span>';
			}
		} else {
			$number_symbol = '<span class="counter-number-inner numscroller" data-min="'.esc_attr($min_number).'" data-max="'.esc_attr($max_number).'" data-delay="'.esc_attr($delay_number).'" data-increment="'.esc_attr($increment_number).'">'.esc_html($min_number).'</span>';
		}
		
		//Icon
		$icon_img_ic ='';	
		if($settings["icon_type"] =="image" && !empty($settings["icon_image"]) && !empty($settings["icon_image"]["url"])){
			$icon_img_ic .='<div class="counter-image-inner">';
				$icon_image = $settings['icon_image']['id'];
				$imgSrc= tp_get_image_rander( $icon_image,$settings['icon_image_thumbnail_size'], [ 'class' => 'counter-icon-image' ] );
				if(!empty($imgSrc) && isset($imgSrc)){
					$icon_img_ic .= $imgSrc;
				}else{
					$icon_img_ic .='<img class="counter-icon-image" src='.Utils::get_placeholder_image_src().' alt="" />';
				}
				
			$icon_img_ic .='</div>';
		}else if($settings["icon_type"] == "icon"){
			$icons = '';
			if($settings["icon_font_style"] == 'font_awesome'){
				$icons = $settings["icon_fontawesome"];
			}else if($settings["icon_font_style"] == 'font_awesome_5'){				
				$icons = $settings['icon_fontawesome_5']['value'];
			}else if($settings["icon_font_style"] == 'icon_mind'){
				$icons = $settings["icons_mind"];
			}
			$icon_bg = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['icon_background_image'],$settings['icon_hover_background_image']) : '';
			$icon_img_ic .='<div class="counter-icon-inner '.esc_attr($icon_bg).' shape-icon-'.esc_attr($settings["icon_style"]).'">';			$icon_img_ic .='<span class="counter-icon '.esc_attr($icons).'"></span>';
			$icon_img_ic .='</div>';
			
		}else if($settings["icon_type"] == 'svg'){
			if($settings['svg_icon'] == 'img'){
				$svg_url = $settings['svg_image']['url'];
			}else{
				$svg_url = THEPLUS_URL.'assets/images/svg/'.esc_attr($settings["svg_d_icon"]); 
			}
			
			if(!empty($settings['border_stroke_color'])){
				$border_stroke_color = $settings['border_stroke_color'];
			}else{
				$border_stroke_color = 'none';
			}
			$svg_uid=uniqid('svg');
			$icon_img_ic ='<div class="pt_plus_animated_svg '.esc_attr($svg_uid).'" data-id="'.esc_attr($svg_uid).'" data-type="'.esc_attr($settings["svg_type"]).'" data-duration="'.esc_attr($settings["duration"]["size"]).'" data-stroke="'.esc_attr($border_stroke_color).'" data-fill_color="none">';
				$icon_img_ic .='<div class="svg_inner_block" style="max-width:'.esc_attr($settings["max_width"]["size"]).esc_attr($settings["max_width"]["unit"]).';max-height:'.esc_attr($settings["max_width"]["size"]).esc_attr($settings["max_width"]["unit"]).';">';
					$icon_img_ic .='<object id="'.esc_attr($svg_uid).'" type="image/svg+xml" data="'.esc_url($svg_url).'" ></object>';
				$icon_img_ic .='</div>';
			$icon_img_ic .='</div>';
		}else if(!empty($settings["icon_type"]) && $settings["icon_type"] == 'lottie'){
			$ext = pathinfo($settings['lottieUrl']['url'], PATHINFO_EXTENSION);
			if($ext!='json'){
				$icon_img_ic .= '<h3 class="theplus-posts-not-found">'.esc_html__("Opps!! Please Enter Only JSON File Extension.",'theplus').'</h3>';
			}else{
				$lottiedisplay = isset($settings['lottiedisplay']) ? $settings['lottiedisplay'] : 'inline-block';
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
				$icon_img_ic .='<lottie-player src="'.esc_url($settings['lottieUrl']['url']).'" style="display: '.esc_attr($lottiedisplay).'; width: '.esc_attr($lottieWidth).'px; height: '.esc_attr($lottieHeight).'px;" '.esc_attr($lottieLoopValue).'  speed="'.esc_attr($lottieSpeed).'" '.esc_attr($lottieAnim).'></lottie-player>';
			}
		}
		
		//Number
		$number_markup ='';
		if(!empty($settings['max_number'])){
			$number_markup = '<h5 class="counter-number">'.$number_symbol.'</h5>';
		}
		//Title
		$title ='';
		if(!empty($settings['title'])){
			$title = '<h6 class="counter-title">'.$icon_link_a.wp_kses_post($settings['title']).$icon_link_a_close.'</h6>';
		}
		$vertical_center='';
		if($style=='style-2' && $settings["vertical_center"] == 'yes'){
			$vertical_center = "vertical-center";
		}
		
		//Style
		$nc_bg = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['box_background_image'],$settings['box_hover_background_image']) : '';
		$counter_content = '<div class="number-counter-inner-block '.esc_attr($nc_bg).' '.esc_attr($vertical_center).'">';
			if($style == 'style-1'){
					$counter_content .='<div class="counter-wrap-content" >';
						$counter_content .= $icon_link_a.$icon_img_ic.$icon_link_a_close;
						$counter_content .= $number_markup;
						$counter_content .= $title;
					$counter_content .= '</div>';
			}elseif($style == 'style-2'){
				$counter_content .= '<div class="icn-header">';
					$counter_content .= $icon_link_a.$icon_img_ic.$icon_link_a_close;
				$counter_content .= '</div>';
				$counter_content .= '<div class="counter-content">';
					$counter_content .= $number_markup;
					$counter_content .= $title;
				$counter_content .= '</div>';
			}else{
				$counter_content .='<div class="counter-wrap-content" >'.$number_markup.' '.$title.' </div>';
			}
		$counter_content .= '</div>';
		
		$uid=uniqid('counter');
		$icon_counter  = '<div class=" content_hover_effect ' . esc_attr($hover_class) . '" ' . $hover_attr . '>';
			$icon_counter .='<div class="plus-number-counter counter-'.esc_attr($style).' '.esc_attr($uid).' '.$animated_class.'" data-id="'.esc_attr($uid).'" '.$animation_attr.'>';
					$icon_counter .= $counter_content;
			$icon_counter .='</div>';
		$icon_counter .='</div>';
		
		echo $before_content.$icon_counter.$after_content;
	}
	
    protected function content_template() {
	
    }
}