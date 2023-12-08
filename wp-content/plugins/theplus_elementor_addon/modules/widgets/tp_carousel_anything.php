<?php 
/*
Widget Name: Carousel Anything
Description: template section slide for carousel.
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
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use TheplusAddons\Theplus_Element_Load;
 
if (!defined('ABSPATH')) exit; // Exit if accessed directly
 
class ThePlus_Carousel_Anything extends Widget_Base {

	public $TpDoc = THEPLUS_TPDOC;
 
	public function get_name() {
		return 'tp-carousel-anything';
	}
 
    public function get_title() {
        return esc_html__('Carousel Anything', 'theplus');
    }
 
    public function get_icon() {
        return 'fa fa-sliders theplus_backend_icon';
    }

	public function get_custom_help_url() {
		$DocUrl = $this->TpDoc . "carousel-anything";

		return esc_url($DocUrl);
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
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'tab_title',
			[
				'label' => esc_html__( 'Title', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Slide 1' , 'theplus' ),
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'content_template_type',
			[
				'label' => esc_html__( 'Content Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'dropdown',
				'options' => [
					'dropdown'  => esc_html__( 'Template', 'theplus' ),					
					'manually' => esc_html__( 'Shortcode', 'theplus' ),
				],
			]
		);
		$repeater->add_control(
			'content_template',
			[
				'label' => wp_kses_post( "Select Content <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "connect-carousel-remote-with-carousel-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '0',
				'options'     => theplus_get_templates(),
				'label_block' => 'true',
				'condition' => [
					'content_template_type' => 'dropdown',
				],
			]
		);
		$repeater->add_control(
			'content_template_id',
			[
				'label' => esc_html__( 'Enter Elementor Template Shortcode', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'default' => '',
				'placeholder' => '[elementor-template id="70"]',
				'condition' => [
					'content_template_type' => 'manually',
				],
			]
		);
		$this->add_control(
			'carousel_content',
			[
				'label' => '',
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'tab_title' => esc_html__( 'Slide #1', 'theplus' ),						
					],
					[
						'tab_title' => esc_html__( 'Slide #2', 'theplus' ),						
					],
				],
				'title_field' => '{{{ tab_title }}}',
			]
		);
		$this->add_control(
			'slide_overflow_hidden',
			[
				'label'   => esc_html__( 'Overflow Hidden', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
				'description' => esc_html__( 'If any content goes outside of section and conflict with another. We suggest to turn this option on.','theplus'),
			]
		);
		$this->add_control(
			'carousel_unique_id',
			[
				'label' => wp_kses_post( "Unique Carousel ID <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "connect-infobox-with-carousel-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'separator' => 'before',
				'description' => esc_html__('Keep this blank or Setup Unique id for carousel which you can use with "Carousel Remote" widget.','theplus'),
			]
		);
		$this->end_controls_section();
		/* Extra Option*/
		$this->start_controls_section(
			'extra_opts_section',
			[
				'label' => esc_html__( 'Extra Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);	
		$this->add_control(
			'slide_random_order',
			[
				'label'   => esc_html__( 'Slide Random Order', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',			
			]
		);
		$this->end_controls_section();
		/* Extra Option*/
		/*carousel option*/
		$this->start_controls_section(
            'section_carousel_options_styling',
            [
                'label' => esc_html__('Carousel Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
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
			'how_vertical_works',
			[
				'label' => wp_kses_post( "<a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "tabs-tours-elementor-widget-settings-overview/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> How vertical works <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'slider_direction' => 'vertical',
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
				'label' => wp_kses_post( "Desktop Columns <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "multiple-columned-elementor-carousel-slider/' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
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
				'label' => esc_html__( 'Slide Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '10',
					'bottom' => '',
					'left' => '10',					
					],
				],
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-initialized .slick-slide' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'label' => wp_kses_post( "Infinite Mode <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "carousel-infinite-loop-scroll-in-elementor-slider/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
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
			'slider_autoplay',
			[
				'label' => wp_kses_post( "Autoplay <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "elementor-carousel-slider-autoplay/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
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
				'label' => wp_kses_post( "Show Dots <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "showhide-arrows-dots-in-elementor-carousel/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
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
			'overlay_content_dots',
			[
				'label'   => esc_html__( 'Overlay Content Dots', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_dots' => 'yes',					
				],
			]
		);
		$this->add_control(
			'direction_dots',
			[
				'label'   => esc_html__( 'Direction Dots', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Vertical', 'theplus' ),
				'label_off' => esc_html__( 'Horizontal', 'theplus' ),				
				'default' => 'no',
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
				'label' => wp_kses_post( "Show Arrows <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "showhide-arrows-dots-in-elementor-carousel/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
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
				'label' => wp_kses_post( "Center Mode <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "increase-center-slide-in-elementor-carousel/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
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
				'render_type' => 'ui',
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
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-slide' => '-webkit-transform: scale({{SIZE}});-moz-transform:    scale({{SIZE}});-ms-transform:     scale({{SIZE}});-o-transform:      scale({{SIZE}});transform:scale({{SIZE}});transition: .3s all linear;',
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
				'selector' => '{{WRAPPER}} .list-carousel-slick .slick-slide.slick-current.slick-active.slick-center .slide-content-inner',
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
				'render_type' => 'ui',
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
				'label' => wp_kses_post( "Tablet Columns <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "multiple-columned-elementor-carousel-slider/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
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
				'label' => wp_kses_post( "Infinite Mode <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "carousel-infinite-loop-scroll-in-elementor-slider/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
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
				 'label' => wp_kses_post( "Autoplay <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "elementor-carousel-slider-autoplay/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
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
				'label' => wp_kses_post( "Mobile Columns <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "multiple-columned-elementor-carousel-slider/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
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
				'label' => wp_kses_post( "Infinite Mode <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "carousel-infinite-loop-scroll-in-elementor-slider/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
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
				'label' => wp_kses_post( "Autoplay <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "elementor-carousel-slider-autoplay/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
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
 
		/*--On Scroll View Animation ---*/
		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation.php';
		include THEPLUS_PATH. 'modules/widgets/theplus-needhelp.php';
	}
 
    protected function render() {
		$settings = $this->get_settings_for_display();
		$uid=uniqid("carousel");
		$id_int = substr( $this->get_id_int(), 0, 3 );
 
		$slide_overflow_hidden=($settings["slide_overflow_hidden"]=='yes') ? 'slide-overflow-hidden' : '';
 
		/*--On Scroll View Animation ---*/
		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';
 
		//carousel option
		$isotope =$data_slider =$arrow_class=$data_carousel='';		
 
		$slider_direction = ($settings['slider_direction']=='vertical') ? 'true' : 'false';
		$data_slider .=' data-slider_direction="'.esc_attr($slider_direction).'"';
		$data_slider .=' data-slide_speed="'.(isset($settings["slide_speed"]["size"]) ? esc_attr($settings["slide_speed"]["size"]) : 1500).'"';
		$slide_fade_inout= ($settings['slider_direction']=='horizontal' && $settings["slide_fade_inout"]=='fadeinout') ? 'true' : 'false';
 
		$data_slider .=' data-slide_fade_inout="'.esc_attr($slide_fade_inout).'"';
 
		$data_slider .=' data-slider_desktop_column="'.(isset($settings["slider_desktop_column"]) ? esc_attr($settings['slider_desktop_column']) : 4).'"';
		$data_slider .=' data-steps_slide="'.(isset($settings['steps_slide']) ? esc_attr($settings['steps_slide']) : 1).'"';
 
		$slider_draggable= ($settings["slider_draggable"]=='yes') ? 'true' : 'false';
		$multi_drag= ($settings["multi_drag"]=='yes') ? 'true' : 'false';
		$data_slider .=' data-slider_draggable="'.esc_attr($slider_draggable).'"';
		$data_slider .=' data-multi_drag="'.esc_attr($multi_drag).'"';
		$slider_infinite= ($settings["slider_infinite"]=='yes') ? 'true' : 'false';
		$data_slider .=' data-slider_infinite="'.esc_attr($slider_infinite).'"';
		$slider_pause_hover= ($settings["slider_pause_hover"]=='yes') ? 'true' : 'false';
		$data_slider .=' data-slider_pause_hover="'.esc_attr($slider_pause_hover).'"';
		$slider_adaptive_height= ($settings["slider_adaptive_height"]=='yes') ? 'true' : 'false';
		$data_slider .=' data-slider_adaptive_height="'.esc_attr($slider_adaptive_height).'"';
		$slider_animation = (isset($settings['slider_animation']) ? $settings['slider_animation'] : 'ease');
		$data_slider .=' data-slider_animation="'.esc_attr($slider_animation).'"';
		$slider_autoplay= ($settings["slider_autoplay"]=='yes') ? 'true' : 'false';
		$data_slider .=' data-slider_autoplay="'.esc_attr($slider_autoplay).'"';
		$data_slider .=' data-autoplay_speed="'.(isset($settings["autoplay_speed"]["size"]) ? esc_attr($settings["autoplay_speed"]["size"]) : 3000).'"';
 
		//tablet
		$data_slider .=' data-slider_tablet_column="'.(isset($settings['slider_tablet_column']) ? esc_attr($settings['slider_tablet_column']) : 3).'"';
		$data_slider .=' data-tablet_steps_slide="'.(isset($settings['tablet_steps_slide']) ? esc_attr($settings['tablet_steps_slide']) : 1).'"';
		$slider_responsive_tablet=$settings['slider_responsive_tablet'];
		$data_slider .=' data-slider_responsive_tablet="'.esc_attr($slider_responsive_tablet).'"';
		if(!empty($settings['slider_responsive_tablet']) && $settings['slider_responsive_tablet']=='yes'){				
			$tablet_slider_draggable= ($settings["tablet_slider_draggable"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-tablet_slider_draggable="'.esc_attr($tablet_slider_draggable).'"';
			$tablet_slider_infinite= ($settings["tablet_slider_infinite"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-tablet_slider_infinite="'.esc_attr($tablet_slider_infinite).'"';
			$tablet_slider_autoplay= ($settings["tablet_slider_autoplay"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-tablet_slider_autoplay="'.esc_attr($tablet_slider_autoplay).'"';
			$data_slider .=' data-tablet_autoplay_speed="'.esc_attr(isset($settings["tablet_autoplay_speed"]["size"]) ? $settings["tablet_autoplay_speed"]["size"] : 1500).'"';
			$tablet_slider_dots= ($settings["tablet_slider_dots"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-tablet_slider_dots="'.esc_attr($tablet_slider_dots).'"';
			$tablet_slider_arrows= ($settings["tablet_slider_arrows"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-tablet_slider_arrows="'.esc_attr($tablet_slider_arrows).'"';
			$data_slider .=' data-tablet_slider_rows="'.(isset($settings["tablet_slider_rows"]) ? esc_attr($settings["tablet_slider_rows"]) : 1).'"';
			$tablet_center_mode= ($settings["tablet_center_mode"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-tablet_center_mode="'.esc_attr($tablet_center_mode).'" ';
			$data_slider .=' data-tablet_center_padding="'.esc_attr(isset($settings["tablet_center_padding"]["size"]) ? $settings["tablet_center_padding"]["size"] : 0).'" ';
		}
 
		//mobile 
		$data_slider .=' data-slider_mobile_column="'.(isset($settings['slider_mobile_column']) ? esc_attr($settings['slider_mobile_column']) : 2).'"';
		$data_slider .=' data-mobile_steps_slide="'.(isset($settings['mobile_steps_slide']) ? esc_attr($settings['mobile_steps_slide']) : 1).'"';
		$slider_responsive_mobile=$settings['slider_responsive_mobile'];			
		$data_slider .=' data-slider_responsive_mobile="'.esc_attr($slider_responsive_mobile).'"';
		if(!empty($settings['slider_responsive_mobile']) && $settings['slider_responsive_mobile']=='yes'){
			$mobile_slider_draggable= ($settings["mobile_slider_draggable"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-mobile_slider_draggable="'.esc_attr($mobile_slider_draggable).'"';
			$mobile_slider_infinite= ($settings["mobile_slider_infinite"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-mobile_slider_infinite="'.esc_attr($mobile_slider_infinite).'"';
			$mobile_slider_autoplay= ($settings["mobile_slider_autoplay"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-mobile_slider_autoplay="'.esc_attr($mobile_slider_autoplay).'"';
			$data_slider .=' data-mobile_autoplay_speed="'.esc_attr(isset($settings["mobile_autoplay_speed"]["size"]) ? $settings["mobile_autoplay_speed"]["size"] : 1500).'"';
			$mobile_slider_dots= ($settings["mobile_slider_dots"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-mobile_slider_dots="'.esc_attr($mobile_slider_dots).'"';
			$mobile_slider_arrows= ($settings["mobile_slider_arrows"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-mobile_slider_arrows="'.esc_attr($mobile_slider_arrows).'"';
			$data_slider .=' data-mobile_slider_rows="'.(isset($settings["mobile_slider_rows"]) ? esc_attr($settings["mobile_slider_rows"]) : 1).'"';
			$mobile_center_mode= ($settings["mobile_center_mode"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-mobile_center_mode="'.esc_attr($mobile_center_mode).'" ';
			$data_slider .=' data-mobile_center_padding="'.esc_attr(isset($settings["mobile_center_padding"]["size"]) ? $settings["mobile_center_padding"]["size"] : 0).'" ';
		}
 
		$slider_dots= ($settings["slider_dots"]=='yes') ? 'true' : 'false';
		$data_slider .=' data-slider_dots="'.esc_attr($slider_dots).'"';
		$data_slider .=' data-slider_dots_style="slick-dots '.(isset($settings["slider_dots_style"]) ? esc_attr($settings["slider_dots_style"]) : 'style-1').'" ';
 
 
		$slider_arrows= ($settings["slider_arrows"]=='yes') ? 'true' : 'false';
		$data_slider .=' data-slider_arrows="'.esc_attr($slider_arrows).'"';
		$data_slider .=' data-slider_arrows_style="'.(isset($settings["slider_arrows_style"]) ? esc_attr($settings["slider_arrows_style"]) : 'style-1').'" ';
		$data_slider .=' data-arrows_position="'.(isset($settings["arrows_position"]) ? esc_attr($settings["arrows_position"]) : 'top-right').'" ';
		$data_slider .=' data-arrow_bg_color="'.(isset($settings["arrow_bg_color"]) ? esc_attr($settings["arrow_bg_color"]) : '#c44d48').'" ';
		$data_slider .=' data-arrow_icon_color="'.(isset($settings["arrow_icon_color"]) ? esc_attr($settings["arrow_icon_color"]) : '#fff').'" ';
		$data_slider .=' data-arrow_hover_bg_color="'.(isset($settings["arrow_hover_bg_color"]) ? esc_attr($settings["arrow_hover_bg_color"]) : '#fff').'" ';
		$data_slider .=' data-arrow_hover_icon_color="'.(isset($settings["arrow_hover_icon_color"]) ? esc_attr($settings["arrow_hover_icon_color"]) : '#c44d48').'" ';  
 
		$slider_center_mode= ($settings["slider_center_mode"]=='yes') ? 'true' : 'false';
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
		if(!empty($settings["hover_show_dots"]) && $settings["hover_show_dots"]=='yes'){
			$data_carousel .=' hover-slider-dots';
		}
		if(!empty($settings["hover_show_arrow"]) && $settings["hover_show_arrow"]=='yes'){
			$data_carousel .=' hover-slider-arrow';
		}
		if(!empty($settings["outer_section_arrow"]) && $settings["outer_section_arrow"]=='yes' && ($settings["slider_arrows_style"]=='style-1' || $settings["slider_arrows_style"]=='style-2' || $settings["slider_arrows_style"]=='style-5' || $settings["slider_arrows_style"]=='style-6')){
			$data_carousel .=' outer-slider-arrow';
		}
		if(!empty($settings["overlay_content_dots"]) && $settings["overlay_content_dots"]=='yes' && $settings["slider_dots"]=='yes'){
			$data_carousel .=' overlay-content-dots';
		}
		if(!empty($settings["direction_dots"]) && $settings["direction_dots"]=='yes' && $settings["slider_dots"]=='yes'){
			$data_carousel .=' vertical-dots';
		}
 
		$tab_id='';
		$carousel_bg_conn ='';
		if(!empty($settings["carousel_unique_id"])){
			$uid="tpca_".$settings["carousel_unique_id"];
			$tab_id="tptab_".$settings["carousel_unique_id"];
			$carousel_bg_conn = ' data-carousel-bg-conn="bgcarousel'.esc_attr($settings["carousel_unique_id"]).'"';
		}
		?>
 
		<div id="<?php echo esc_attr($uid); ?>" class="theplus-carousel-anything-wrapper <?php echo esc_attr($isotope); ?> <?php echo esc_attr($arrow_class); ?> <?php echo esc_attr($data_carousel); ?> <?php echo esc_attr($uid); ?> <?php echo esc_attr($animated_class); ?>" data-id="<?php echo esc_attr($uid); ?>" data-connection="<?php echo esc_attr($tab_id); ?>" <?php echo $animation_attr; ?> <?php echo $data_slider; ?> <?php echo $carousel_bg_conn; ?>>
			<div class="plus-carousel-inner post-inner-loop">
			<?php
			if(!empty($settings['carousel_content'])){
				if(!empty($settings['slide_random_order']) && $settings['slide_random_order']== 'yes' ){
					shuffle($settings['carousel_content']);
				}
				foreach ( $settings['carousel_content'] as $index => $item ) :
					$tab_count = $index + 1;
 
					$tab_content_setting_key = $this->get_repeater_setting_key( 'tab_content', 'carousel_content', $index );
 
					$this->add_render_attribute( $tab_content_setting_key, [
						'id' => 'slide-content-' . $id_int . $tab_count,
						'class' => [ 'plus-slide-content','grid-item',$slide_overflow_hidden ],
					] );
 
					?>
					<div <?php echo $this->get_render_attribute_string( $tab_content_setting_key ); ?>>
						<div class="slide-content-inner">
							<?php
							if((!empty($item['content_template_type']) && $item['content_template_type']=='manually') && !empty($item['content_template_id'])){								
								echo '<div class="plus-content-editor">'.Theplus_Element_Load::elementor()->frontend->get_builder_content_for_display(  substr($item['content_template_id'], 24, -2) ).'</div>';
							}else{
								if(!empty($item['content_template'])){
									echo '<div class="plus-content-editor">'.Theplus_Element_Load::elementor()->frontend->get_builder_content_for_display( $item['content_template'] ).'</div>';
								}
							}
 
							?>						
						</div>
					</div>
				<?php 
				endforeach;
			} ?>
			</div>
		</div>
		<?php
	}
 
	protected function content_template() {
 
	}
}