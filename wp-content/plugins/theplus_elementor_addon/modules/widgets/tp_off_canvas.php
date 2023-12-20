<?php 
/*
Widget Name: Popup Builder
Description: Toggle Content off canvas.
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
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Off_Canvas extends Widget_Base {

	public $TpDoc = THEPLUS_TPDOC;
		
	public function get_name() {
		return 'tp-off-canvas';
	}

    public function get_title() {
        return esc_html__('Popup Builder / Off Canvas', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-bars theplus_backend_icon';
    }

	public function get_custom_help_url() {
		$DocUrl = $this->TpDoc . "popup";

		return esc_url($DocUrl);
	}

    public function get_categories() {
        return array('plus-creatives');
    }

	public function get_keywords() {
		return [ 'offcanvas', 'popup', 'modal box', 'modal popup','popup builder'];
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
			'content_open_style',
			[
				'label' => wp_kses_post( "Popup Type <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "off-canvas-popup-menu-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => [
					'popup' => esc_html__( 'Modal Popup', 'theplus' ),
					'reveal' => esc_html__( 'Reveal Content', 'theplus' ),
					'corner-box' => esc_html__( 'Corner Box', 'theplus' ),	
					'slide'  => esc_html__( 'Slide', 'theplus' ),
					//'push' => esc_html__( 'Push Content', 'theplus' ),					
					//'slide-along' => esc_html__( 'Slide Along Content', 'theplus' ),
				],
				'selectors' => [
					'.plus-{{ID}}-open .plus-{{ID}}.plus-canvas-content-wrap:not(.plus-popup).plus-visible' => '-webkit-transform: translate3d(0,0,0);transform: translate3d(0,0,0);',
					//'.plus-{{ID}}-open .plus-{{ID}}.plus-canvas-content-wrap.plus-popup.plus-visible' => '-webkit-transform: translateY(-50%) scale(1);transform: translateY(-50%) scale(1);',
				],
			]
		);
		$this->add_control(
			'content_open_direction_popup',
			[
				'label' => esc_html__( 'Open Direction', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'center',
				'options' => [
					'top-left'  => esc_html__( 'Top Left', 'theplus' ),
					'top-center'  => esc_html__( 'Top Center', 'theplus' ),
					'top-right'  => esc_html__( 'Top Right', 'theplus' ),
					'left'  => esc_html__( 'Left', 'theplus' ),
					'right'  => esc_html__( 'Right', 'theplus' ),
					'center'  => esc_html__( 'Center', 'theplus' ),
					'bottom-left'  => esc_html__( 'Bottom Left', 'theplus' ),
					'bottom-center'  => esc_html__( 'Bottom Center', 'theplus' ),
					'bottom-right'  => esc_html__( 'Bottom Right', 'theplus' ),
				],
				'condition' => [
					'content_open_style' => 'popup',					
				],
			]
		);
		$this->add_responsive_control('content_open_height',
			[
				'label' => esc_html__( 'Open Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'vh' ],
				'range' => [
					'vh' => [
						'min' => 10,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'vh',
					'size' => 100,
				],
				'selectors' => [
					'.plus-{{ID}}.plus-canvas-content-wrap.plus-slide' => 'height: {{SIZE}}{{UNIT}};',
					'.plus-{{ID}}.plus-canvas-content-wrap.plus-slide .plus-content-editor' => 'height: 100%;',	
					'.plus-{{ID}}.plus-canvas-content-wrap.plus-slide .plus-stylist-list-wrapper' => 'height: {{SIZE}}{{UNIT}};',	
				],
				'condition' => [
					'content_open_style' => 'slide',					
				],
			]
		);
		$this->add_control(
			'content_open_direction',
			[
				'label' => esc_html__( 'Open Direction', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left'  => esc_html__( 'Left', 'theplus' ),
					'right' => esc_html__( 'Right', 'theplus' ),
					'top' => esc_html__( 'Top', 'theplus' ),
					'bottom' => esc_html__( 'Bottom', 'theplus' ),
				],
				'condition' => [
					'content_open_style!' => ['corner-box','popup'],					
				],
			]
		);
		$this->add_control(
			'content_open_corner_box_direction',
			[
				'label' => esc_html__( 'Corner Box Direction', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'top-left',
				'options' => [
					'top-left'  => esc_html__( 'Top Left', 'theplus' ),
					'top-right' => esc_html__( 'Top Right', 'theplus' ),					
				],
				'condition' => [
					'content_open_style' => 'corner-box',					
				],
			]
		);
		$this->add_responsive_control(
			'content_open_width',
			[
				'label' => wp_kses_post( "Open Width <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "full-width-menu-popup-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 800,
						'step' => 2,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 300,
				],
				'selectors' => [
					'.plus-{{ID}}.plus-canvas-content-wrap.plus-top,.plus-{{ID}}.plus-canvas-content-wrap.plus-bottom' => 'width: 100%;height: {{SIZE}}{{UNIT}};',
					'.plus-{{ID}}.plus-canvas-content-wrap' => 'width: {{SIZE}}{{UNIT}};',			
					'.plus-{{ID}}-open.plus-push.plus-open.plus-left .plus-offcanvas-container,.plus-{{ID}}-open.plus-reveal.plus-open.plus-left .plus-offcanvas-container,.plus-{{ID}}-open.plus-slide-along.plus-open.plus-left .plus-offcanvas-container' => '-webkit-transform: translate3d({{SIZE}}{{UNIT}}, 0, 0);transform: translate3d({{SIZE}}{{UNIT}}, 0, 0);',
					'.plus-{{ID}}-open.plus-push.plus-open.plus-right .plus-offcanvas-container,.plus-{{ID}}-open.plus-reveal.plus-open.plus-right .plus-offcanvas-container,.plus-{{ID}}-open.plus-slide-along.plus-open.plus-right .plus-offcanvas-container' => '-webkit-transform: translate3d(-{{SIZE}}{{UNIT}}, 0, 0);transform: translate3d(-{{SIZE}}{{UNIT}}, 0, 0);',
					'.plus-{{ID}}-open.plus-push.plus-open.plus-top .plus-offcanvas-container,.plus-{{ID}}-open.plus-reveal.plus-open.plus-top .plus-offcanvas-container,.plus-{{ID}}-open.plus-slide-along.plus-open.plus-top .plus-offcanvas-container' => '-webkit-transform: translate3d(0,{{SIZE}}{{UNIT}}, 0);transform: translate3d( 0,{{SIZE}}{{UNIT}}, 0);',
					'.plus-{{ID}}-open.plus-push.plus-open.plus-bottom .plus-offcanvas-container,.plus-{{ID}}-open.plus-reveal.plus-open.plus-bottom .plus-offcanvas-container,.plus-{{ID}}-open.plus-slide-along.plus-open.plus-bottom .plus-offcanvas-container' => '-webkit-transform: translate3d(0,-{{SIZE}}{{UNIT}}, 0);transform: translate3d( 0,-{{SIZE}}{{UNIT}}, 0);',
					'.plus-{{ID}}.plus-canvas-content-wrap.plus-corner-box' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					'.plus-{{ID}}.plus-canvas-content-wrap.plus-top-left.plus-corner-box' => '-webkit-transform: translate3d(-{{SIZE}}{{UNIT}},-{{SIZE}}{{UNIT}},0);transform: translate3d(-{{SIZE}}{{UNIT}},-{{SIZE}}{{UNIT}},0);',
					'.plus-{{ID}}.plus-canvas-content-wrap.plus-top-right.plus-corner-box' => '-webkit-transform: translate3d({{SIZE}}{{UNIT}},-{{SIZE}}{{UNIT}},0);transform: translate3d({{SIZE}}{{UNIT}},-{{SIZE}}{{UNIT}},0);',
				],
				'condition' => [
					'content_open_style!' => 'popup',					
				],
			]
		);		
		$this->add_responsive_control(
			'content_open_popup_width',
			[
				'label' => esc_html__( 'Popup Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 800,
						'step' => 2,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'.plus-{{ID}}.plus-canvas-content-wrap.plus-popup' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'content_open_style' => 'popup',					
				],
			]
		);
		$this->add_responsive_control(
			'content_open_popup_height',
			[
				'label' => esc_html__( 'Popup Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 800,
						'step' => 2,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'.plus-{{ID}}.plus-canvas-content-wrap.plus-popup' => 'max-height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'content_open_style' => 'popup',					
				],
			]
		);
		$this->add_responsive_control(
			'content_open_left_right_padding',
			[
				'label' => esc_html__( 'Popup Left/Right Space', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 2,
					],
				],
				'selectors' => [
					'.plus-{{ID}}.plus-canvas-content-wrap.plus-popup' => 'margin-left :{{SIZE}}{{UNIT}};width: calc(100% - {{SIZE}}{{UNIT}} * 2);',
				],
				'condition' => [
					'content_open_style' => 'popup',					
				],
			]
		);
		$this->add_control(
			'content_template_type',
			[
				'label' => esc_html__( 'Content Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'tp-template',				
				'separator' => 'before',
				'options' => [
					'tp-template'  => esc_html__( 'Template', 'theplus' ),					
					'tp-content'  => esc_html__( 'Content', 'theplus' ),					
					'tp-manually' => esc_html__( 'Shortcode', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'content_template',
			[
				'label' => wp_kses_post( "Select Content <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "popup-with-elementor-templates/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '0',
				'options'     => theplus_get_templates(),
				'label_block' => 'true',
				'condition' => [
					'content_template_type' => 'tp-template',
				],
			]
		);
		$this->add_control(
			'content_description',
			[
				'label' => esc_html__( 'Content', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'theplus' ),
				'placeholder' => esc_html__( 'Type your content here', 'theplus' ),
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'content_template_type' => 'tp-content',
				],
			]
		);
		$this->add_control(
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
					'content_template_type' => 'tp-manually',
				],
			]
		);
		$this->add_control(
			'select_toggle_canvas',
			[
				'label' => wp_kses_post( "Select Option <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "open-popup-on-button-click-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'button',
				'options' => [
					'icon'  => esc_html__( 'Icon', 'theplus' ),
					'button' => esc_html__( 'Call To Action', 'theplus' ),					
					'hide' => esc_html__( 'Hidden', 'theplus' ),
					'lottie' => esc_html__( 'Lottie', 'theplus' ),					
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'toggle_icon_style',
			[
				'label' => esc_html__( 'Icon Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'style-2' => esc_html__( 'Style 2', 'theplus' ),					
					'style-3' => esc_html__( 'Style 3', 'theplus' ),
					'custom' => esc_html__( 'Custom', 'theplus' ),					
				],				
				'condition' => [
					'select_toggle_canvas' => 'icon',
				],
			]
		);
		$this->add_control(
			'image_svg_icn',
			[
				'label' => esc_html__( 'Choose Image/SVG', 'theplus' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'select_toggle_canvas' => 'icon',
					'toggle_icon_style' => 'custom',
				],
			]
		);
		$this->add_control(
			'toggle_img_svg_size',
			[
				'label' => esc_html__( 'Image/Svg Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 500,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 60,
				],
				'condition' => [
					'select_toggle_canvas' => 'icon',
					'toggle_icon_style' => 'custom',
				],
				'selectors' => [
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-custom .off-can-img-svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'toggle_icon_size',
			[
				'label' => esc_html__( 'Icon Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 150,
					],
				],
				'condition' => [
					'select_toggle_canvas' => 'icon',
					'toggle_icon_style!' => 'custom',
				],
				'selectors' => [
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'toggle_icon_weight',
			[
				'label' => esc_html__( 'Icon Weight', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 5,
						'step' => 0.5,
					],
				],
				'condition' => [
					'select_toggle_canvas' => 'icon',
					'toggle_icon_style!' => 'custom',
				],
				'selectors' => [
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1 span.menu_line,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2 span.menu_line,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3 span.menu_line' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'toggle_icon_padding',
			[
				'label' => esc_html__( 'Icon Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'select_toggle_canvas' => 'icon',
					'toggle_icon_style!' => 'custom',
				],
			]
		);
		$this->add_control(
			'button_text',
			[
				'label' => esc_html__( 'Button Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Click Here', 'theplus' ),
				'dynamic' => ['active'   => true,],
				'condition' => [					
					'select_toggle_canvas' => 'button',
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
					'font_awesome'  => esc_html__( 'Font Awesome', 'theplus' ),
					'font_awesome_5'  => esc_html__( 'Font Awesome 5', 'theplus' ),
					'icon_mind' => esc_html__( 'Icons Mind', 'theplus' ),
					'none'  => esc_html__( 'None', 'theplus' ),
				],
				'separator' => 'before',
				'condition' => [					
					'select_toggle_canvas' => 'button',
				],
			]
		);
		$this->add_control(
			'button_icon',
			[
				'label' => esc_html__( 'Select Icon', 'theplus' ),
				'type' => Controls_Manager::ICON,
				'label_block' => false,
				'default' => 'fa fa-chevron-right',
				'condition' => [
					'select_toggle_canvas' => 'button',
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
					'select_toggle_canvas' => 'button',
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
					'select_toggle_canvas' => 'button',
					'button_icon_style' => 'icon_mind',
				],
			]
		);
		$this->add_control(
			'button_before_after',
			[
				'label' => esc_html__( 'Icon Position', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'after',
				'options' => [
					'after' => esc_html__( 'After', 'theplus' ),
					'before' => esc_html__( 'Before', 'theplus' ),
				],
				'condition' => [
					'select_toggle_canvas' => 'button',
					'button_icon_style!' => 'none',
				],
			]
		);
		$this->add_responsive_control(
			'button_icon_spacing',
			[
				'label' => esc_html__( 'Icon Spacing', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'condition' => [
					'select_toggle_canvas' => 'button',
					'button_icon_style!' => 'none',
				],
				'selectors' => [
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn .btn-icon.button-after' => 'padding-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn .btn-icon.button-before' => 'padding-right: {{SIZE}}{{UNIT}};',					
				],
			]
		);
		$this->add_responsive_control(
			'button_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 200,
					],
				],
				'condition' => [
					'select_toggle_canvas' => 'button',
					'button_icon_style!' => 'none',
				],
				'selectors' => [
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn .btn-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn .btn-icon svg' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',
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
					'select_toggle_canvas' => 'lottie',
				],
			]
		);
		$this->add_responsive_control(
			'button_align',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
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
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'prefix_class' => 'text-%s',
				'default' => 'center',
				'condition' => [
					'select_toggle_canvas!' => 'hide',
				],
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/*Call to Action 1 Style*/
		/*Extra Options Content*/
		$this->start_controls_section(
			'extra_option_content_section',
			[
				'label' => esc_html__( 'Extra Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'event_esc_close_content',
			[
				'label' => wp_kses_post( "Esc Button Close Content <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "close-elementor-popup-with-esc-or-by-clicking-outside/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
			'event_body_click_close_content',
			[
				'label' => wp_kses_post( "Outer Click Close Content <a class='tp-docs-link' href='" . esc_url($this->TpDoc)."close-elementor-popup-with-esc-or-by-clicking-outside/' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
			'click_offcanvas_close',
			[
				'label' => esc_html__( 'On Click Link Popup Close', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		/*Fixed Buton Toggle*/
		$this->add_control(
			'fixed_toggle_button',
			[
				'label' => esc_html__( 'Fixed Toggle Button', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),
				'default' => '',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'show_scroll_window_offset',
			[
				'label' => esc_html__( 'Show Menu Scroll Offset', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'scroll_top_offset_value',
			[
				'label' => esc_html__( 'Scroll Top Offset Value', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => 'px',
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5000,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'condition' => [
					'fixed_toggle_button' => [ 'yes' ],
					'show_scroll_window_offset' => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'fixed_toggle_position' );
		/*desktop  start*/
		$this->start_controls_tab( 'fixed_toggle_desktop',
			[
				'label' => esc_html__( 'Desktop', 'theplus' ),
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
				],
			]
		);		
		$this->add_control(
			'd_left_auto', [
				'label'   => esc_html__( 'Left (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),		
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
				],
			]
		);

		$this->add_control(
			'd_pos_xposition', [
				'label' => esc_html__( 'Left', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
					'px' => [
						'min' => -100,
						'max' => 2000,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
					'd_left_auto' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'd_right_auto',[
				'label'   => esc_html__( 'Right (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],					
				],
			]
		);
		$this->add_control(
			'd_pos_rightposition',[
				'label' => esc_html__( 'Right', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
					'px' => [
						'min' => -100,
						'max' => 2000,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
					'd_right_auto' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'd_top_auto', [
				'label'   => esc_html__( 'Top (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],				
				],
			]
		);
		$this->add_control(
			'd_pos_yposition', [
				'label' => esc_html__( 'Top', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => '%',
					'size' => 5,
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
					'px' => [
						'min' => -100,
						'max' => 800,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
					'd_top_auto' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'd_bottom_auto', [
				'label'   => esc_html__( 'Bottom (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],					
				],
			]
		);
		$this->add_control(
			'd_pos_bottomposition', [
				'label' => esc_html__( 'Bottom', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
					'px' => [
						'min' => -100,
						'max' => 800,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
					'd_bottom_auto' => [ 'yes' ],
				],
			]
		);
		/*extra effect*/
		$this->add_control(
			'contentextraeffect',
			[
				'label' => esc_html__( 'Content Transform', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'content_open_style!' => 'popup',
				],
			]
		);
		$this->add_control(
			'contentextraeffectrotatex', [
				'label' => esc_html__( 'Rotate X', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
					'size' => '0',
				],
				'range' => [
					'deg' => [
						'min' => -360,
						'max' => 360,
						'step' => 15,
					],
				],
				'condition'    => [
					'content_open_style!' => 'popup',
					'contentextraeffect' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'contentextraeffectrotatey', [
				'label' => esc_html__( 'Rotate Y', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
					'size' => '-20',
				],
				'range' => [
					'deg' => [
						'min' => -360,
						'max' => 360,
						'step' => 15,
					],
				],				
				'condition'    => [
					'content_open_style!' => 'popup',
					'contentextraeffect' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'contentextraeffecttranslatex', [
				'label' => esc_html__( 'Translate X', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default' => [
					'unit' => 'px',
					'size' => '0',
				],
				'range' => [
					'deg' => [
						'min' => -200,
						'max' => 200,
						'step' => 15,
					],
				],
				'condition'    => [
					'content_open_style!' => 'popup',
					'contentextraeffect' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'contentextraeffecttranslatey', [
				'label' => esc_html__( 'Translate Y', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default' => [
					'unit' => 'px',
					'size' => '0',
				],
				'range' => [
					'deg' => [
						'min' => -200,
						'max' => 200,
						'step' => 15,
					],
				],
				'condition'    => [
					'content_open_style!' => 'popup',
					'contentextraeffect' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'contentextraeffectsacle', [
				'label' => esc_html__( 'Scale', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default' => [
					'unit' => 'px',
					'size' => '1',
				],
				'range' => [
					'deg' => [
						'min' => .1,
						'max' => 1,
						'step' => .1,
					],
				],
				'selectors' => [
					'.plus-{{ID}}-open body' => '-webkit-perspective:1500px;perspective: 1500px;',
					'.plus-{{ID}}-open .plus-offcanvas-container' => '-webkit-transform: translate3d(100px, 0, -600px) rotateY({{contentextraeffectrotatey.SIZE}}{{contentextraeffectrotatey.UNIT}}) rotateX({{contentextraeffectrotatex.SIZE}}{{contentextraeffectrotatex.UNIT}}) translateX({{contentextraeffecttranslatex.SIZE}}{{contentextraeffecttranslatex.UNIT}}) translateY({{contentextraeffecttranslatey.SIZE}}{{contentextraeffecttranslatey.UNIT}}) scale({{contentextraeffectsacle.SIZE}});
					transform: translate3d(100px, 0, -600px) rotateY({{contentextraeffectrotatey.SIZE}}{{contentextraeffectrotatey.UNIT}}) rotateX({{contentextraeffectrotatex.SIZE}}{{contentextraeffectrotatex.UNIT}}) translateX({{contentextraeffecttranslatex.SIZE}}{{contentextraeffecttranslatex.UNIT}}) translateY({{contentextraeffecttranslatey.SIZE}}{{contentextraeffecttranslatey.UNIT}}) scale({{contentextraeffectsacle.SIZE}});',
				],
				'condition'    => [
					'content_open_style!' => 'popup',
					'contentextraeffect' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
            'contentextraeffectbg',
            [
                'label' => esc_html__('Background Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '.plus-{{ID}}-open body,.plus-{{ID}}-open' => 'background:{{VALUE}};',
                ],
				'condition'    => [
					'content_open_style!' => 'popup',
					'contentextraeffect' => [ 'yes' ],
				],
            ]
        );
		$this->add_control(
			'popinanimationheading',
			[
				'label' => esc_html__( 'Model Popup In/Out Animation', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'content_open_style' => 'popup',
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'popinanimation',
			[
				'label' => esc_html__( 'In Animation', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'  => esc_html__( 'Select Animation', 'theplus' ),
					'fadeIn'  => esc_html__( 'FadeIn', 'theplus' ),
					'fadeInDown'  => esc_html__( 'FadeInDown', 'theplus' ),
					'fadeInDownBig'  => esc_html__( 'FadeInDownBig', 'theplus' ),
					'fadeInLeft'  => esc_html__( 'FadeInLeft', 'theplus' ),
					'fadeInLeftBig'  => esc_html__( 'FadeInLeftBig', 'theplus' ),
					'fadeInRight'  => esc_html__( 'FadeInRight', 'theplus' ),
					'fadeInRightBig'  => esc_html__( 'FadeInRightBig', 'theplus' ),
					'fadeInUp'  => esc_html__( 'FadeInUp', 'theplus' ),
					'fadeInUpBig'  => esc_html__( 'FadeInUpBig', 'theplus' ),
					'fadeInTopLeft'  => esc_html__( 'FadeInTopLeft', 'theplus' ),
					'fadeInTopRight'  => esc_html__( 'FadeInTopRight', 'theplus' ),
					'fadeInBottomLeft'  => esc_html__( 'FadeInBottomLeft', 'theplus' ),
					'fadeInBottomRight'  => esc_html__( 'FadeInBottomRight', 'theplus' ),
					'zoomIn'  => esc_html__( 'ZoomIn', 'theplus' ),
					'slideInDown'  => esc_html__( 'SlideInDown', 'theplus' ),
					'slideInLeft'  => esc_html__( 'SlideInLeft', 'theplus' ),
					'slideInRight'  => esc_html__( 'SlideInRight', 'theplus' ),
					'slideInUp'  => esc_html__( 'SlideInUp', 'theplus' ),
					'flipInX'  => esc_html__( 'FlipInX', 'theplus' ),
					'flipInY'  => esc_html__( 'FlipInY', 'theplus' ),
				],				
				'condition' => [
					'content_open_style' => 'popup',					
				],
			]
		);
		$this->add_control(
			'popoutanimation',
			[
				'label' => esc_html__( 'Out Animation', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'  => esc_html__( 'Select Animation', 'theplus' ),
					'fadeOut'  => esc_html__( 'FadeOut', 'theplus' ),
					'fadeOutDown'  => esc_html__( 'FadeOutDown', 'theplus' ),
					'fadeOutDownBig'  => esc_html__( 'FadeOutDownBig', 'theplus' ),
					'fadeOutLeft'  => esc_html__( 'FadeOutLeft', 'theplus' ),
					'fadeOutLeftBig'  => esc_html__( 'FadeOutLeftBig', 'theplus' ),
					'fadeOutRight'  => esc_html__( 'FadeOutRight', 'theplus' ),
					'fadeOutRightBig'  => esc_html__( 'FadeOutRightBig', 'theplus' ),
					'fadeOutUp'  => esc_html__( 'FadeOutUp', 'theplus' ),
					'fadeOutUpBig'  => esc_html__( 'FadeOutUpBig', 'theplus' ),
					'fadeOutTopLeft'  => esc_html__( 'FadeOutTopLeft', 'theplus' ),
					'fadeOutTopRight'  => esc_html__( 'FadeOutTopRight', 'theplus' ),
					'fadeOutBottomLeft'  => esc_html__( 'FadeOutBottomLeft', 'theplus' ),
					'fadeOutBottomRight'  => esc_html__( 'FadeOutBottomRight', 'theplus' ),
					'zoomOut'  => esc_html__( 'ZoomOut', 'theplus' ),
					'slideOutDown'  => esc_html__( 'SlideOutDown', 'theplus' ),
					'slideOutLeft'  => esc_html__( 'SlideOutLeft', 'theplus' ),
					'slideOutRight'  => esc_html__( 'SlideOutRight', 'theplus' ),
					'slideOutUp'  => esc_html__( 'SlideOutUp', 'theplus' ),
					'flipOutX'  => esc_html__( 'FlipOutX', 'theplus' ),
					'flipOutY'  => esc_html__( 'FlipOutY', 'theplus' ),
				],
				'condition' => [
					'content_open_style' => 'popup',					
				],
			]
		);
		$this->add_control(
			'popoutanimationdelay',
			[
				'label' => esc_html__( 'Delay', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'  => esc_html__( 'Default', 'theplus' ),
					'delay-1s'  => esc_html__( '1s', 'theplus' ),
					'delay-2s'  => esc_html__( '2s', 'theplus' ),
					'delay-3s'  => esc_html__( '3s', 'theplus' ),
					'delay-4s'  => esc_html__( '4s', 'theplus' ),
					'delay-5s'  => esc_html__( '5s', 'theplus' ),
				],
				'condition' => [
					'content_open_style' => 'popup',					
				],
			]
		);
		$this->add_control(
			'popoutanimationspeed',
			[
				'label' => esc_html__( 'Speed', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'  => esc_html__( 'Default', 'theplus' ),
					'faster'  => esc_html__( 'Faster', 'theplus' ),
					'fast'  => esc_html__( 'Fast', 'theplus' ),
					'slow'  => esc_html__( 'Slow', 'theplus' ),
					'slower'  => esc_html__( 'Slower', 'theplus' ),
				],
				'condition' => [
					'content_open_style' => 'popup',					
				],
			]
		);
		$this->end_controls_tab();
		/*desktop end*/
		/*tablet start*/
		$this->start_controls_tab( 'fixed_toggle_tablet',
			[
				'label' => esc_html__( 'Tablet', 'theplus' ),
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			't_responsive', [
				'label'   => esc_html__( 'Responsive Values', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			't_left_auto', [
				'label'   => esc_html__( 'Left (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
					't_responsive' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			't_pos_xposition', [
				'label' => esc_html__( 'Left', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
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
					'px' => [
						'min' => -100,
						'max' => 1200,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
					't_responsive' => [ 'yes' ],
					't_left_auto' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			't_right_auto',[
				'label'   => esc_html__( 'Right (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
					't_responsive' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			't_pos_rightposition',[
				'label' => esc_html__( 'Right', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
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
					'px' => [
						'min' => -100,
						'max' => 1200,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
					't_responsive' => [ 'yes' ],
					't_right_auto' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			't_top_auto', [
				'label'   => esc_html__( 'Top (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
					't_responsive' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			't_pos_yposition', [
				'label' => esc_html__( 'Top', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
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
					'px' => [
						'min' => -100,
						'max' => 800,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
					't_responsive' => [ 'yes' ],
					't_top_auto' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			't_bottom_auto', [
				'label'   => esc_html__( 'Bottom (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
					't_responsive' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			't_pos_bottomposition', [
				'label' => esc_html__( 'Bottom', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
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
					'px' => [
						'min' => -100,
						'max' => 800,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
					't_responsive' => [ 'yes' ],
					't_bottom_auto' => [ 'yes' ],
				],
			]
		);
		$this->end_controls_tab();
		/*tablet end*/
		/*mobile start*/
		$this->start_controls_tab( 'fixed_toggle_mobile',
			[
				'label' => esc_html__( 'Mobile', 'theplus' ),
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'm_responsive', [
				'label'   => esc_html__( 'Responsive Values', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'm_left_auto', [
				'label'   => esc_html__( 'Left (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
					'm_responsive' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'm_pos_xposition', [
				'label' => esc_html__( 'Left', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
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
					'px' => [
						'min' => -100,
						'max' => 700,
						'step' => 1,
					],
				],
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
					'm_responsive' => [ 'yes' ],
					'm_left_auto' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'm_right_auto',[
				'label'   => esc_html__( 'Right (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
					'm_responsive' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'm_pos_rightposition',[
				'label' => esc_html__( 'Right', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
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
					'px' => [
						'min' => -100,
						'max' => 700,
						'step' => 1,
					],
				],
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
					'm_responsive' => [ 'yes' ],
					'm_right_auto' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'm_top_auto', [
				'label'   => esc_html__( 'Top (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
					'm_responsive' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'm_pos_yposition', [
				'label' => esc_html__( 'Top', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
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
					'px' => [
						'min' => -100,
						'max' => 700,
						'step' => 1,
					],
				],
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
					'm_responsive' => [ 'yes' ],
					'm_top_auto' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'm_bottom_auto', [
				'label'   => esc_html__( 'Bottom (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
					'm_responsive' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'm_pos_bottomposition', [
				'label' => esc_html__( 'Bottom', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
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
					'px' => [
						'min' => -100,
						'max' => 700,
						'step' => 1,
					],
				],
				'condition'    => [
					'fixed_toggle_button' => [ 'yes' ],
					'm_responsive' => [ 'yes' ],
					'm_bottom_auto' => [ 'yes' ],
				],
			]
		);
		$this->end_controls_tab();
		/*mobile end*/
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Extra Options Content*/
		/*Popup Display Content*/
		$this->start_controls_section(
			'popup_display_content_section',
			[
				'label' => esc_html__( 'Display Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		/*$this->add_control(
			'openTrigger',
			[
				'label' => esc_html__( 'On Button Click', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
			'openTriggerNote',
			[
				'label' => esc_html__( 'Note: You need to Select Option Hidden from Popup Builder Content Tab', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'openTrigger!' => 'yes',
				],	
			]
		);*/
		$this->add_control(
			'pageload',
			[
				'label' => wp_kses_post( "On Page Load <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "trigger-popup-on-page-load-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'scroll',
			[
				'label' => wp_kses_post( "On Scroll <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "popup-after-page-scroll-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
            'scrollHeight',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__( 'Scroll Offset (PX)L', 'theplus' ),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5000,
						'step' => 10,
					],
				],
				'render_type' => 'ui',			
				'condition' => [
					'scroll' => 'yes',
				],			
            ]
        );
		$this->add_control(
			'exit',
			[
				'label' => wp_kses_post( "On Exit Inlet <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "exit-intent-popup-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'inactivity',
			[
				'label' => wp_kses_post( "After Inactivity <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "popup-on-user-inactivity-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'inactivitySec',
			[
				'label' => esc_html__( 'Inactivity MilliSecond', 'theplus' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 10000,
				'step' => 100,				
				'condition' => [
					'inactivity' => 'yes',
				],
			]
		);
		$this->add_control(
			'pageviews',
			[
				'label' => wp_kses_post( "After X Page Views <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "popup-on-page-views-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'pageViewsCount',
			[
				'label' => esc_html__( 'Page View Count', 'theplus' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 50,
				'step' => 1,				
				'condition' => [
					'pageviews' => 'yes',
				],
			]
		);
		$this->add_control(
			'prevurl',
			[
				'label' => wp_kses_post( "Arriving From Specific URL <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "show-elementor-popup-arriving-from-a-specific-url/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'previousUrl',
			[
				'label' => esc_html__( 'Source URL', 'theplus' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'http://', 'theplus' ),
				'default' => [
					'url' => '',
				],
				'condition' => [
					'prevurl' => 'yes',
				],
			]
		);
		$this->add_control(
			'extraclick',
			[
				'label' => wp_kses_post( "On Any Other Element\'s Click <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "elementor-popup-on-other-element-click/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'extraId',
			[
				
				'label'   => esc_html__( 'Unique Class (Open)', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Unique Class', 'theplus' ),
				'condition' => [
					'extraclick' => 'yes',
				],
			]
		);
		$this->add_control(
			'extraIdClose',
			[
				'label' => esc_html__( 'Unique Class (Close)', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Unique Class', 'theplus' ),
				'condition' => [
					'extraclick' => 'yes',
				],
			]
		);
		$this->add_control(
            'showTime',
            [
				'label' => wp_kses_post( "Show For Specific Time <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "elementor-popup-on-specific-date-and-time/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);
		$this->add_control(
			'dateStart',
			[
				
				'label'   => esc_html__( 'Start Date', 'theplus' ),
				'type' => Controls_Manager::DATE_TIME,
				'condition' => [
					'showTime' => 'yes',
				],
			]
		);
		$this->add_control(
			'dateEnd',
			[
				'label'   => esc_html__( 'End Date', 'theplus' ),
				'type' => Controls_Manager::DATE_TIME,
				'condition' => [
					'showTime' => 'yes',
				],
			]
		);
		$this->add_control(
            'showRestricted',
            [
				'label' => wp_kses_post( "Show X Times per User <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "elementor-popup-once-per-website-session/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);
		$this->add_control(
			'showXTimes',
			[
				'label'   => esc_html__( 'Number of Timesr', 'theplus' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 50,
				'step' => 1,
				'condition' => [
					'showRestricted' => 'yes',
				],
			]
		);
		$this->add_control(
			'showXDays',
			[
				'label' => esc_html__( 'Inactive Days', 'theplus' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 365,
				'step' => 1,
				'condition' => [
					'showRestricted' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*Popup Display Content*/
		/*Toggle Content Style*/
		$this->start_controls_section(
            'toggle_content_section_styling',
            [
                'label' => esc_html__('Open Content', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_responsive_control(
			'open_content_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
							'top' => '10',
							'right' => '25',
							'bottom' => '10',
							'left' => '25',
							'isLinked' => false 
				],
				'selectors' => [
					'.plus-{{ID}}.plus-canvas-content-wrap .plus-content-editor' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'label' => esc_html__('Content Text Typography', 'theplus'),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_TEXT
                ],
                'selector' => '.plus-{{ID}}.plus-canvas-content-wrap .plus-content-editor,.plus-{{ID}}.plus-canvas-content-wrap .plus-content-editor p',
            ]
        );
		$this->add_control(
            'content_color',
            [
                'label' => esc_html__('Content Text Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '#888',
                'selectors' => [
                    '.plus-{{ID}}.plus-canvas-content-wrap .plus-content-editor,.plus-{{ID}}.plus-canvas-content-wrap .plus-content-editor p' => 'color:{{VALUE}};',
                ],
            ]
        );
		$this->start_controls_tabs( 'tabs_open_content_style' );
		$this->start_controls_tab(
			'tab_open_content_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'open_content_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '.plus-{{ID}}.plus-canvas-content-wrap',
			]
		);
		$this->add_responsive_control(
			'open_content_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.plus-{{ID}}.plus-canvas-content-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'open_content_shadow',
				'selector' => '.plus-{{ID}}.plus-canvas-content-wrap',				
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_open_content_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'open_content_hover_shadow',
				'selector' => '.plus-{{ID}}.plus-canvas-content-wrap:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'open_content_close_icon_heading',
			[
				'label' => esc_html__( 'Close Icon', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'open_content_close_icon_display',
			[
				'label' => esc_html__( 'Display Close Icon', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
            'close_icon_color',
            [
                'label' => esc_html__( 'Close Icon Color', 'theplus' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#000',  
                'selectors' => [
                    '.plus-canvas-content-wrap.plus-{{ID}} .plus-offcanvas-close:before, .plus-canvas-content-wrap.plus-{{ID}} .plus-offcanvas-close:after' => 'border-color: {{VALUE}};',
                ],
                
            ],
        );
		$this->add_control(
			'open_close_icon_sticky',
			[
				'label' => esc_html__( 'Sticky/Fixed Close Icon', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),				
				'default' => 'no',
				'condition' => [					
					'open_content_close_icon_display' => 'yes',
				],
			]
		);
		$this->add_control(
			'close_image_custom',
			[
				'label' => esc_html__( 'Custom Close Icon', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),				
				'default' => 'no',
				'condition' => [					
					'open_content_close_icon_display' => 'yes',
				],
			]
		);
		$this->add_control(
			'close_image_custom_source',
			[
				'label' => esc_html__( 'Choose Image', 'theplus' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'condition' => [					
					'open_content_close_icon_display' => 'yes',
					'close_image_custom' => 'yes',
				],
			]
		);
		$this->add_control(
			'open_content_close_icon_align',
			[
				'label' => esc_html__( 'Icon Alignment', 'theplus' ),
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
				'default' => 'right',
				'toggle' => true,
				'label_block' => false,
				'condition' => [					
					'open_content_close_icon_display' => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_open_content_close_style' );
		$this->start_controls_tab(
			'tab_open_content_close_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [					
					'open_content_close_icon_display' => 'yes',
				],
			]
		);
		$this->add_control(
			'open_content_close_color',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.plus-{{ID}}.plus-canvas-content-wrap .plus-offcanvas-close:before,.plus-{{ID}}.plus-canvas-content-wrap .plus-offcanvas-close:after' => 'border-bottom-color: {{VALUE}}',
				],
				'condition' => [					
					'open_content_close_icon_display' => 'yes',
					'close_image_custom' => 'no',
				],
			]
		);
		$this->add_control(
			'off_cus_close_img',
			[
				'label' => esc_html__( 'Close Image Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'selectors' => [
					'.plus-{{ID}}.plus-canvas-content-wrap .plus-offcanvas-close,.plus-{{ID}}.plus-canvas-content-wrap .off-close-image .close-custom_img' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'open_content_close_icon_display' => 'yes',
					'close_image_custom' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'open_content_close_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '.plus-{{ID}}.plus-canvas-content-wrap .plus-offcanvas-close',
				'condition' => [					
					'open_content_close_icon_display' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'open_content_close_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.plus-{{ID}}.plus-canvas-content-wrap .plus-offcanvas-close,.plus-{{ID}}.plus-canvas-content-wrap .off-close-image .close-custom_img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [					
					'open_content_close_icon_display' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'open_content_close_shadow',
				'selector' => '.plus-{{ID}}.plus-canvas-content-wrap .plus-offcanvas-close',
				'condition' => [					
					'open_content_close_icon_display' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_open_content_close_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [					
					'open_content_close_icon_display' => 'yes',
				],
			]
		);
		$this->add_control(
			'open_content_close_hover_color',
			[
				'label' => esc_html__( 'Icon Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.plus-{{ID}}.plus-canvas-content-wrap .plus-offcanvas-close:hover:before,.plus-{{ID}}.plus-canvas-content-wrap .plus-offcanvas-close:hover:after' => 'border-bottom-color: {{VALUE}}',
				],
				'condition' => [					
					'open_content_close_icon_display' => 'yes',
					'close_image_custom' => 'no',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'open_content_close_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '.plus-{{ID}}.plus-canvas-content-wrap .plus-offcanvas-close:hover',
				'condition' => [					
					'open_content_close_icon_display' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'open_content_close_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.plus-{{ID}}.plus-canvas-content-wrap .plus-offcanvas-close:hover,.plus-{{ID}}.plus-canvas-content-wrap .off-close-image .close-custom_img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [					
					'open_content_close_icon_display' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'open_content_close_hover_shadow',
				'selector' => '.plus-{{ID}}.plus-canvas-content-wrap .plus-offcanvas-close:hover',
				'condition' => [					
					'open_content_close_icon_display' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'open_content_overlay_heading',
			[
				'label' => esc_html__( 'Popup Background Overlay', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'open_content_overlay_background',
				'label' => esc_html__( 'Overlay Color', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '.plus-offcanvas-content-widget.plus-{{ID}}-open .plus-offcanvas-container:after',
			]
		);
		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'open_content_filter',
				'selector' => '.plus-{{ID}}-open .plus-offcanvas-container',
			]
		);
		$this->add_control(
			'open_content_overflow',
			[
				'label' => esc_html__( 'Overflow', 'theplus' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Hidden', 'theplus' ),
				'label_off' => __( 'Visible', 'theplus' ),
				'selectors' => [
					'.plus-{{ID}}.plus-canvas-content-wrap' => 'overflow:hidden;',
				],
				'separator' => 'before',
			]
		);				
		$this->end_controls_section();
		/*Toggle Content Style*/
		/*Toggle Icon Style*/
		$this->start_controls_section(
            'toggle_icon_style_section_styling',
            [
                'label' => esc_html__('Toggle Icon/Hamburger', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [					
					'select_toggle_canvas' => 'icon',
				],
            ]
        );
		$this->add_control(
			'icon_border',
			[
				'label' => esc_html__( 'Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'icon_border_style',
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
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3,
					{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-custom .off-can-img-svg' => 'border-style: {{VALUE}};',
				],
				'condition' => [					
					'icon_border' => 'yes',
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
			'icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1 span.menu_line,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2 span.menu_line,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3 span.menu_line' => 'background: {{VALUE}};',
				],
				'condition' => [					
					'toggle_icon_style!' => 'custom',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'icon_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-custom .off-can-img-svg',
			]
		);
		$this->add_responsive_control(
			'icon_border_width',
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
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-custom .off-can-img-svg' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'icon_border' => 'yes',
					'icon_border_style!' => 'none',
				],
			]
		);
		$this->add_control(
			'icon_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#313131',
				'selectors' => [
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3,
					{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-custom .off-can-img-svg' => 'border-color: {{VALUE}};',					
				],
				'condition' => [
					'icon_border' => 'yes',
					'icon_border_style!' => 'none'
				],
			]
		);
		$this->add_responsive_control(
			'icon_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3,
					{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-custom .off-can-img-svg' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_shadow',
				'selector' => '{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3,
				{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-custom .off-can-img-svg',
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
			'icon_hover_color',
			[
				'label' => esc_html__( 'Icon Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1:hover span.menu_line,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2:hover span.menu_line,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3:hover span.menu_line' => 'background: {{VALUE}};',
				],
				'condition' => [					
					'toggle_icon_style!' => 'custom',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'icon_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1:hover,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2:hover,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3:hover,
				{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-custom .off-can-img-svg:hover',
			]
		);
		$this->add_control(
			'icon_border_hover_color',
			[
				'label'     => esc_html__( 'Hover Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#313131',
				'selectors' => [
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1:hover,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2:hover,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3:hover,
					{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-custom .off-can-img-svg:hover' => 'border-color: {{VALUE}};',					
				],
				'separator' => 'before',
				'condition' => [
					'icon_border' => 'yes',
					'icon_border_style!' => 'none'
				],
			]
		);
		$this->add_responsive_control(
			'icon_hover_radius',
			[
				'label'      => esc_html__( 'Hover Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1:hover,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2:hover,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3:hover,
					{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-custom .off-can-img-svg:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_hover_shadow',
				'selector' => '{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1:hover,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2:hover,{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3:hover,
				{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-custom .off-can-img-svg:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Toggle icon Style*/
		/*Toggle Button Style*/
		$this->start_controls_section(
            'toggle_style_section_styling',
            [
                'label' => esc_html__('Toggle Button', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [					
					'select_toggle_canvas' => 'button',
				],
            ]
        );
		$this->add_control(
			'button_full_width',
			[
				'label' => esc_html__( 'Full Width Button', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_responsive_control(
			'button_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
							'top' => '10',
							'right' => '25',
							'bottom' => '10',
							'left' => '25',
							'isLinked' => false 
				],
				'selectors' => [
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn',
			]
		);
		$this->add_control(
			'button_border',
			[
				'label' => esc_html__( 'Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
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
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn' => 'border-style: {{VALUE}};',
				],
				'condition' => [					
					'button_border' => 'yes',
				],
				'separator' => 'before',
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
			'button_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn' => 'color: {{VALUE}};',
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn',
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
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'button_border' => 'yes',
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
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn' => 'border-color: {{VALUE}};',					
				],
				'condition' => [
					'button_border' => 'yes',
					'button_border_style!' => 'none'
				],
			]
		);
		$this->add_responsive_control(
			'button_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_shadow',
				'selector' => '{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn',				
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
			'button_text_hover_color',
			[
				'label' => esc_html__( 'Text Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn:hover',
			]
		);
		$this->add_control(
			'button_border_hover_color',
			[
				'label'     => esc_html__( 'Hover Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#313131',
				'selectors' => [
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn:hover' => 'border-color: {{VALUE}};',					
				],
				'separator' => 'before',
				'condition' => [
					'button_border' => 'yes',
					'button_border_style!' => 'none'
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
					'{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_hover_shadow',
				'selector' => '{{WRAPPER}} .plus-offcanvas-wrapper .offcanvas-toggle-btn:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Toggle Button Style*/
		/*lottie style*/
		$this->start_controls_section(
            'section_lottie_styling',
            [
                'label' => esc_html__('Lottie', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'select_toggle_canvas' => 'lottie',
				],
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
		$this->start_controls_section(
            'content_scrolling_bar_section_styling',
            [
                'label' => esc_html__('Content Scrolling Bar', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
				
            ]
        );
		$this->add_control(
			'display_scrolling_bar',
			[
				'label' => esc_html__( 'Content Scrolling Bar', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),				
				'default' => 'yes',
			]
		);
		$this->start_controls_tabs( 'tabs_scrolling_bar_style' );
		$this->start_controls_tab(
			'tab_scrolling_bar_scrollbar',
			[
				'label' => esc_html__( 'Scrollbar', 'theplus' ),
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->add_control(
			'scroll_scrollbar_width',
			[
				'label' => esc_html__( 'ScrollBar Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'.plus-canvas-content-wrap.plus-{{ID}}::-webkit-scrollbar' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'scroll_scrollbar_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '.plus-canvas-content-wrap.plus-{{ID}}::-webkit-scrollbar',
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_scrolling_bar_thumb',
			[
				'label' => esc_html__( 'Thumb', 'theplus' ),
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'scroll_thumb_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '.plus-canvas-content-wrap.plus-{{ID}}::-webkit-scrollbar-thumb',
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'scroll_thumb_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.plus-canvas-content-wrap.plus-{{ID}}::-webkit-scrollbar-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',					
				],
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'scroll_thumb_shadow',
				'selector' => '.plus-canvas-content-wrap.plus-{{ID}}::-webkit-scrollbar-thumb',
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_scrolling_bar_track',
			[
				'label' => esc_html__( 'Track', 'theplus' ),
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'scroll_track_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '.plus-canvas-content-wrap.plus-{{ID}}::-webkit-scrollbar-track',
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'scroll_track_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.plus-canvas-content-wrap.plus-{{ID}}::-webkit-scrollbar-track' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'scroll_track_shadow',
				'selector' => '.plus-canvas-content-wrap.plus-{{ID}}::-webkit-scrollbar-track',
				'condition' => [
					'display_scrolling_bar' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Toggle Button Style*/		
		/*Sticky Navigation Style*/
		$this->start_controls_section(
            'content_sticky_navigation_styling',
            [
                'label' => esc_html__('Sticky Navigation Connection', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
				
            ]
        );
        $this->add_control(
			'Pop_stickNav_Note',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => 'Note: This Option Is Related To Navigation Menu Widget’s Sticky Menu Settings.',
				'content_classes' => 'tp-widget-description',
			]
		);
		$this->start_controls_tabs( 'sticky_navigation_tabs' );
		$this->start_controls_tab( 'sticky_navigation_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'sn_button_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn' => 'color: {{VALUE}};',					
				],
				'condition' => [					
					'select_toggle_canvas' => 'button',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'sn_button_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn',
				'condition' => [					
					'select_toggle_canvas' => 'button',
				],
			]
		);
		$this->add_control(
			'sn_button_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn' => 'border-color: {{VALUE}};',					
				],
				'condition' => [
					'select_toggle_canvas' => 'button',
					'button_border' => 'yes',
					'button_border_style!' => 'none'
				],
			]
		);
		$this->add_control(
			'sn_icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1 span.menu_line,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2 span.menu_line,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3 span.menu_line' => 'background: {{VALUE}};',
				],
				'condition' => [
					'select_toggle_canvas' => 'icon',
					'toggle_icon_style!' => 'custom',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'sn_icon_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-custom .off-can-img-svg',
				'condition' => [					
					'select_toggle_canvas' => 'icon',
				],
			]
		);		
		$this->add_control(
			'sn_icon_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-custom .off-can-img-svg' => 'border-color: {{VALUE}};',					
				],
				'condition' => [
					'select_toggle_canvas' => 'icon',
					'icon_border' => 'yes',
					'icon_border_style!' => 'none'
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab( 'sticky_navigation_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'sn_button_text_hover_color',
			[
				'label' => esc_html__( 'Text Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn:hover' => 'color: {{VALUE}};',
				],
				'condition' => [					
					'select_toggle_canvas' => 'button',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'sn_button_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn:hover',
				'condition' => [					
					'select_toggle_canvas' => 'button',
				],
			]
		);
		$this->add_control(
			'sn_button_border_hover_color',
			[
				'label'     => esc_html__( 'Hover Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#313131',
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn:hover' => 'border-color: {{VALUE}};',					
				],
				'separator' => 'before',
				'condition' => [
					'select_toggle_canvas' => 'button',
					'button_border' => 'yes',
					'button_border_style!' => 'none'
				],
			]
		);
		$this->add_control(
			'sn_icon_hover_color',
			[
				'label' => esc_html__( 'Icon Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1:hover span.menu_line,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2:hover span.menu_line,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3:hover span.menu_line' => 'background: {{VALUE}};',
				],
				'condition' => [
					'select_toggle_canvas' => 'icon',
					'toggle_icon_style!' => 'custom',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'sn_icon_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1:hover,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2:hover,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3:hover,
				.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-custom .off-can-img-svg:hover',
				'condition' => [
					'select_toggle_canvas' => 'icon',
				],
			]
		);
		$this->add_control(
			'sn_icon_border_hover_color',
			[
				'label'     => esc_html__( 'Hover Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1:hover,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2:hover,.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3:hover,
					.plus-nav-sticky-sec.plus-fixed-sticky .elementor-widget-tp-off-canvas .elementor-widget-container .plus-offcanvas-wrapper .offcanvas-toggle-btn.humberger-custom .off-can-img-svg:hover' => 'border-color: {{VALUE}};',					
				],
				'separator' => 'before',
				'condition' => [
					'select_toggle_canvas' => 'icon',
					'icon_border' => 'yes',
					'icon_border_style!' => 'none'
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Sticky Navigation Style*/
		/*--On Scroll View Animation ---*/
		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation.php';
		include THEPLUS_PATH. 'modules/widgets/theplus-needhelp.php';
	}
	
	
	 protected function render() {
        $settings = $this->get_settings_for_display();
		$widget_uid='canvas-'.$this->get_id();
		
		$offsetTime = wp_timezone_string();		
		$now        = new \DateTime('NOW', new \DateTimeZone($offsetTime));
		$flag = true;
		
		if(!empty($settings['showTime']) && $settings['showTime'] == 'yes') {			
			$dateStart  = new \DateTime($settings['dateStart'], new \DateTimeZone($offsetTime));
			$dateEnd    = new \DateTime($settings['dateEnd'], new \DateTimeZone($offsetTime));
			
			if(($dateStart <= $now) && ($now <= $dateEnd)) {
				$flag = true;				
			} else {
				$flag = false;			
			}			
		}
		
		$content_id=$this->get_id();
		$fixed_toggle_button = ($settings["fixed_toggle_button"]=='yes') ? 'position-fixed' : '';
		$show_scroll_window_offset = ($settings["fixed_toggle_button"]=='yes' && $settings['show_scroll_window_offset']=='yes') ? 'scroll-view' : '';
		$scroll_top_offset_value = ($settings["fixed_toggle_button"]=='yes' && $settings['show_scroll_window_offset']=='yes') ? 'data-scroll-view="'.esc_attr($settings['scroll_top_offset_value']["size"]).'"' : '';		
		$content_open_style = $settings["content_open_style"];
		
		//$openTrigger = !empty($settings["openTrigger"]) ? $settings["openTrigger"] : 'no';
		$prevurl = !empty($settings["prevurl"]) ? $settings["prevurl"] : 'no';
		$extraclick = !empty($settings["extraclick"]) ? $settings["extraclick"] : 'no';
		$inactivity = !empty($settings["inactivity"]) ? $settings["inactivity"] : 'no';
		$scroll = !empty($settings["scroll"]) ? $settings["scroll"] : 'no';
		$pageload = !empty($settings["pageload"]) ? $settings["pageload"] : 'no';
		$exit = !empty($settings["exit"]) ? $settings["exit"] : 'no';
		$pageviews = !empty($settings["pageviews"]) ? $settings["pageviews"] : 'no';
		
		$previousUrl=$extraId=$extraIdClose=$inactivitySec='';
		if(!empty($prevurl) && $prevurl == 'yes' && !empty($settings["previousUrl"]["url"])){
			$previousUrl = $settings["previousUrl"]["url"];
		}
		if(!empty($extraclick) && $extraclick == 'yes' && !empty($settings["extraId"])){
			 $extraId = $settings["extraId"];
		}
		if(!empty($extraclick) && $extraclick == 'yes' && !empty($settings["extraIdClose"])){
			 $extraIdClose = $settings["extraIdClose"];
		}
		if(!empty($inactivity) && $inactivity == 'yes' && !empty($settings["inactivitySec"])){
			 $inactivitySec = $settings["inactivitySec"];
		}

		$content_open_direction = $settings["content_open_direction"];
		$display_scrolling_bar = ($settings["display_scrolling_bar"]!='yes') ? 'scroll-bar-disable' : '';
		$event_esc_close_content = ($settings["event_esc_close_content"]=='yes') ? 'yes' : 'no';
		$event_body_click_close_content=($settings["event_body_click_close_content"]=='yes') ? 'yes' : 'no';
		if($content_open_style == 'corner-box'){
			$content_open_direction = $settings["content_open_corner_box_direction"];
		}elseif($content_open_style == 'popup' ) {
            $content_open_direction = "popup";
        }

		/*--On Scroll View Animation ---*/
			include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';

		$off_canvas='';

		if($flag) {
			$uid = uniqid("canvas-");
			
			$scrollHeight =$PVC=$PVCStyle= '';
			if((!empty($scroll) && $scroll == 'yes') && !empty($settings['scrollHeight']['size'])){
				$scrollHeight = $settings['scrollHeight']['size'];
			}
			if(!empty($pageviews) && $pageviews == 'yes' && !empty($settings['pageViewsCount'])){
				$PVC = $settings['pageViewsCount'];
				$PVCStyle = 'style="display:none;"';
			}
			$sr=$srxtime=$srxdays='';
			if(!empty($settings['showRestricted']) && $settings['showRestricted']=='yes' && !empty($settings['showXTimes']) && !empty($settings['showXDays'])){
				$sr = $settings['showRestricted'];
				$srxtime = $settings['showXTimes'];
				$srxdays = $settings['showXDays'];
				$PVCStyle = 'style="display:none;"';
			}
			$data_attr = 'data-settings={"content_id":"'.esc_attr($content_id).'","transition":"'.esc_attr($content_open_style).'","direction":"'.esc_attr($content_open_direction).'","esc_close":"'.esc_attr($event_esc_close_content).'","body_click_close":"'.esc_attr($event_body_click_close_content).'","trigger":"yes","tpageload":"' . esc_attr ( $pageload ) . '","tscroll":"' . esc_attr ( $scroll ) . '","scrollHeight":"'. esc_attr($scrollHeight). '","texit":"' . esc_attr ( $exit ) . '","tinactivity":"' . esc_attr ( $inactivity ) . '","tpageviews":"' . esc_attr ( $pageviews ) . '","tpageviewscount":"' . esc_attr ( $PVC ) . '","tprevurl":"' . esc_attr ( $prevurl ) . '","previousUrl":"'. esc_attr($previousUrl). '","textraclick":"' . esc_attr ( $extraclick ) . '","extraId":"'. esc_attr($extraId). '","extraIdClose":"'. esc_attr($extraIdClose). '","inactivitySec":"'. esc_attr($inactivitySec). '","sr":"'. esc_attr($sr). '","srxtime":"'. esc_attr($srxtime). '","srxdays":"'. esc_attr($srxdays). '"}';
			//button
			$toggle_content='';
			$full_width_button=($settings["select_toggle_canvas"]=='button' && !empty($settings['button_full_width']) && $settings['button_full_width'] == 'yes') ? 'btn_full_width' : '';
			if($settings["select_toggle_canvas"]=='button'){
				$toggle_content .='<div class="offcanvas-toggle-btn toggle-button-style '.esc_attr($fixed_toggle_button).' '.esc_attr($full_width_button).'">';
					$toggle_content .= $this->render_text_one();
				$toggle_content .='</div>';
			}
			//lottie
			$lottie_icon = $settings["select_toggle_canvas"];
			if(!empty($lottie_icon) && $lottie_icon == 'lottie'){
				$ext = pathinfo($settings['lottieUrl']['url'], PATHINFO_EXTENSION);
				if($ext!='json'){
					$toggle_content .= '<h3 class="theplus-posts-not-found">'.esc_html__("Opps!! Please Enter Only JSON File Extension.",'theplus').'</h3>';
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
					$toggle_content .='<lottie-player src="'.esc_url($settings['lottieUrl']['url']).'" style="display: '.esc_attr($lottiedisplay).'; width: '.esc_attr($lottieWidth).'px; height: '.esc_attr($lottieHeight).'px;" '.esc_attr($lottieLoopValue).'  speed="'.esc_attr($lottieSpeed).'" '.esc_attr($lottieAnim).'></lottie-player>';
				}
			}
			if($settings["select_toggle_canvas"] == 'icon' && !empty($settings["toggle_icon_style"])){
				if($settings["toggle_icon_style"] == 'style-1' || $settings["toggle_icon_style"] == 'style-2' || $settings["toggle_icon_style"] == 'style-3'){
					$toggle_content .='<div class="offcanvas-toggle-btn humberger-'.esc_attr($settings["toggle_icon_style"]).' '.esc_attr($fixed_toggle_button).'">';
						$toggle_content .='<span class="menu_line menu_line--top"></span>';
						$toggle_content .='<span class="menu_line menu_line--center"></span>';
						$toggle_content .='<span class="menu_line menu_line--bottom"></span>';
					$toggle_content .='</div>';
				}else if($settings["toggle_icon_style"] == 'custom'){
					$toggle_content .='<div class="offcanvas-toggle-btn humberger-'.esc_attr($settings["toggle_icon_style"]).' '.esc_attr($fixed_toggle_button).'">';

						$alt='';
						if(!empty($settings['image_svg_icn']['id'])){						
							$alt = get_post_meta($settings['image_svg_icn']['id'], '_wp_attachment_image_alt', true);
						}				
						
						$toggle_content .='<img src="'.esc_url($settings['image_svg_icn']['url']).'" alt="'.esc_attr($alt).'" class="off-can-img-svg" />';
					$toggle_content .='</div>';	
				}
			}

			$off_canvas ='<div class="plus-offcanvas-wrapper '.esc_attr($widget_uid).' '.esc_attr($animated_class).' '.esc_attr($show_scroll_window_offset).'" data-canvas-id="'.esc_attr($widget_uid).'" '.$data_attr.' '.$scroll_top_offset_value.' '.$animation_attr.' '.esc_attr($PVCStyle).'>';

				$off_canvas .='<div class="offcanvas-toggle-wrap">';
				if($lottie_icon == 'lottie'){
					$off_canvas .='<div class="offcanvas-toggle-btn custom-lottie">';
				}
					$off_canvas .= $toggle_content;
				if($lottie_icon == 'lottie'){
					$off_canvas .='</div>';
				}
				$off_canvas .='</div>';
				
				$popupdirclass="";
				if((!empty($settings['content_open_style']) && $settings['content_open_style']=='popup') && !empty($settings['content_open_direction_popup'])){
					$popupdirclass=!empty($settings['content_open_direction_popup']) ? 'tp-popup-dir-'.$settings['content_open_direction_popup'] : 'tp-popup-dir-center';
				}
				$popinoutaniclass=$popinanimation=$popoutanimation=$popoutanimationspeed=$popoutanimationdelay="";
				if((!empty($settings['popinanimation']) && $settings['popinanimation']!="none") || (!empty($settings['popoutanimation']) && $settings['popoutanimation']!="none") || (!empty($settings['popoutanimationdelay']) && $settings['popoutanimationdelay']!="none") || (!empty($settings['popoutanimationspeed']) && $settings['popoutanimationspeed']!="none")){
					$popinoutaniclass = 'tp_animate__animated';
					$popinanimation = 'animate__'.$settings['popinanimation'];
					$popoutanimation = 'animate__'.$settings['popoutanimation'];
					$popoutanimationdelay = 'animate__'.$settings['popoutanimationdelay'];
					$popoutanimationspeed = 'animate__'.$settings['popoutanimationspeed'];
				}
				$off_canvas .='<div class="plus-canvas-content-wrap '.esc_attr($popinoutaniclass).' '.esc_attr($popinanimation).' '.esc_attr($popoutanimation).' tp-outer-'.esc_attr($event_body_click_close_content).' '.esc_attr($popupdirclass).' plus-'.esc_attr($content_id).' plus-'.esc_attr($content_open_direction).' plus-'.esc_attr($content_open_style).' '.esc_attr($display_scrolling_bar).'">';
					if(!empty($settings["open_content_close_icon_display"]) && $settings["open_content_close_icon_display"] == 'yes'){
						$sticky_btn = (!empty($settings["open_close_icon_sticky"]) && $settings["open_close_icon_sticky"] == 'yes') ? 'sticky-close-btn' : '';
						$close_icon_class = (!empty($settings["close_image_custom"]) && $settings["close_image_custom"] == 'yes') ? 'off-close-image' : '';
						
						$off_canvas .='<div class="plus-offcanvas-header direction-'.esc_attr($settings["open_content_close_icon_align"]).' '.esc_attr($sticky_btn).'"><div class="plus-offcanvas-close plus-offcanvas-close-'.esc_attr($content_id).' '.esc_attr($close_icon_class).'" role="button">';
							if(!empty($settings["close_image_custom"]) && $settings["close_image_custom"] == 'yes' && !empty($settings['close_image_custom_source']['url'])){
								$off_canvas .='<img src="'.esc_url($settings['close_image_custom_source']['url']).'" class="close-custom_img"/>';
							}
							$off_canvas .='</div></div>';
					}
					
					$content_template_type=!empty($settings['content_template_type']) ? $settings['content_template_type'] : 'tp-template';
					$content_description=!empty($settings['content_description']) ? $settings['content_description'] : '';
					$content_template_id=!empty($settings['content_template_id']) ? $settings['content_template_id'] : '';
					
					if(!empty($content_template_type)){
						if($content_template_type=='tp-content' && !empty($content_description)){
							$off_canvas .='<div class="plus-content-editor">'.$content_description.'</div>';
						}else if($content_template_type=='tp-manually' && !empty($settings['content_template_id'])){
							$off_canvas .='<div class="plus-content-editor">'.Theplus_Element_Load::elementor()->frontend->get_builder_content_for_display(  substr($settings['content_template_id'], 24, -2) ).'</div>';
						}else if(!empty($settings['content_template'])){
							$off_canvas .='<div class="plus-content-editor">'.Theplus_Element_Load::elementor()->frontend->get_builder_content_for_display( $settings['content_template'] ).'</div>';
						}
					}
				$off_canvas .='</div>';
				
			$off_canvas .='</div>';
			
			if(!empty($settings["fixed_toggle_button"]) && $settings["fixed_toggle_button"] == 'yes'){
				$off_canvas .='<style>';
				$rpos='auto';$bpos='auto';$ypos='auto';$xpos='auto';
					if($settings['d_left_auto'] == 'yes'){
						if(!empty($settings['d_pos_xposition']['size']) || $settings['d_pos_xposition']['size'] == '0'){
							$xpos=$settings['d_pos_xposition']['size'].$settings['d_pos_xposition']['unit'];
						}
					}
					if($settings['d_top_auto'] == 'yes'){
						if(!empty($settings['d_pos_yposition']['size']) || $settings['d_pos_yposition']['size'] == '0'){
							$ypos=$settings['d_pos_yposition']['size'].$settings['d_pos_yposition']['unit'];
						}
					}
					if($settings['d_bottom_auto'] == 'yes'){
						if(!empty($settings['d_pos_bottomposition']['size']) || $settings['d_pos_bottomposition']['size'] == '0'){
							$bpos=$settings['d_pos_bottomposition']['size'].$settings['d_pos_bottomposition']['unit'];
						}
					}
					if($settings['d_right_auto'] == 'yes'){
						if(!empty($settings['d_pos_rightposition']['size']) || $settings['d_pos_rightposition']['size'] == '0'){
							$rpos=$settings['d_pos_rightposition']['size'].$settings['d_pos_rightposition']['unit'];
						}
					}
					
					$off_canvas.='.'.esc_attr($widget_uid).' .offcanvas-toggle-wrap .offcanvas-toggle-btn.position-fixed{top:'.esc_attr($ypos).';bottom:'.esc_attr($bpos).';left:'.esc_attr($xpos).';right:'.esc_attr($rpos).';}';
					
					if(!empty($settings['t_responsive']) && $settings['t_responsive']=='yes'){
						$tablet_xpos='auto';$tablet_ypos='auto';$tablet_bpos='auto';$tablet_rpos='auto';
						if($settings['t_left_auto'] == 'yes'){
							if(!empty($settings['t_pos_xposition']['size']) || $settings['t_pos_xposition']['size'] == '0'){
								$tablet_xpos=$settings['t_pos_xposition']['size'].$settings['t_pos_xposition']['unit'];
							}
						}
						if($settings['t_top_auto'] == 'yes'){
							if(!empty($settings['t_pos_yposition']['size']) || $settings['t_pos_yposition']['size'] == '0'){
								$tablet_ypos=$settings['t_pos_yposition']['size'].$settings['t_pos_yposition']['unit'];
							}
						}
						if($settings['t_bottom_auto'] == 'yes'){
							if(!empty($settings['t_pos_bottomposition']['size']) || $settings['t_pos_bottomposition']['size'] == '0'){
								$tablet_bpos=$settings['t_pos_bottomposition']['size'].$settings['t_pos_bottomposition']['unit'];
							}
						}
						if($settings['t_right_auto'] == 'yes'){
							if(!empty($settings['t_pos_rightposition']['size']) || $settings['t_pos_rightposition']['size'] == '0'){
								$tablet_rpos=$settings['t_pos_rightposition']['size'].$settings['t_pos_rightposition']['unit'];
							}
						}
						
						$off_canvas.='@media (min-width:601px) and (max-width:990px){.'.esc_attr($widget_uid).' .offcanvas-toggle-wrap .offcanvas-toggle-btn.position-fixed{top:'.esc_attr($tablet_ypos).';bottom:'.esc_attr($tablet_bpos).';left:'.esc_attr($tablet_xpos).';right:'.esc_attr($tablet_rpos).';}';
						
						$off_canvas.='}';
					}
					if(!empty($settings['m_responsive']) && $settings['m_responsive']=='yes'){
						$mobile_xpos='auto';$mobile_ypos='auto';$mobile_bpos='auto';$mobile_rpos='auto';
						if($settings['m_left_auto'] == 'yes'){
							if(!empty($settings['m_pos_xposition']['size']) || $settings['m_pos_xposition']['size'] == '0'){
								$mobile_xpos=$settings['m_pos_xposition']['size'].$settings['m_pos_xposition']['unit'];
							}
						}
						if($settings['m_top_auto'] == 'yes'){
							if(!empty($settings['m_pos_yposition']['size']) || $settings['m_pos_yposition']['size'] == '0'){
								$mobile_ypos=$settings['m_pos_yposition']['size'].$settings['m_pos_yposition']['unit'];
							}
						}
						if($settings['m_bottom_auto'] == 'yes'){
							if(!empty($settings['m_pos_bottomposition']['size']) || $settings['m_pos_bottomposition']['size'] == '0'){
								$mobile_bpos=$settings['m_pos_bottomposition']['size'].$settings['m_pos_bottomposition']['unit'];
							}
						}
						if($settings['m_right_auto'] == 'yes'){
							if(!empty($settings['m_pos_rightposition']['size']) || $settings['m_pos_rightposition']['size'] == '0'){
								$mobile_rpos=$settings['m_pos_rightposition']['size'].$settings['m_pos_rightposition']['unit'];
							}
						}
						$off_canvas.='@media (max-width:600px){.'.esc_attr($widget_uid).' .offcanvas-toggle-wrap .offcanvas-toggle-btn.position-fixed{top:'.esc_attr($mobile_ypos).';bottom:'.esc_attr($mobile_bpos).';left:'.esc_attr($mobile_xpos).';right:'.esc_attr($mobile_rpos).';}';
						
						$off_canvas.='}';
					}
				$off_canvas .='</style>';
			}		
				
			if(!empty($settings['click_offcanvas_close']) && $settings['click_offcanvas_close']=='yes'){
				$off_canvas.='<script type="text/javascript">';
					$off_canvas .='jQuery(document).ready(function(i){
									"use strict";
									jQuery(".plus-content-editor a:not(.dropdown-toggle),.plus-content-editor .tp-search-filter .tp-range-silder").on("click",function(){							
										jQuery(this).closest(".plus-canvas-content-wrap").find( ".plus-offcanvas-close").trigger( "click" );
									})
									
									jQuery(".plus-content-editor .tp-search-filter .tp-search-form").on("change",function(){
										jQuery(this).closest(".plus-canvas-content-wrap").find( ".plus-offcanvas-close").trigger( "click" );
									})';
					$off_canvas.='});';
				$off_canvas.='</script>';
			}
		}
		echo $off_canvas;
	}
	
    protected function content_template() {}
	protected function render_text_one(){
		$icons_after=$icons_before=$button_text='';
		$settings = $this->get_settings_for_display();
		
		$before_after = $settings['button_before_after'];
		$button_text = $settings['button_text'];
		
		$icons='';
		if($settings["button_icon_style"] == 'font_awesome'){
			$icons=$settings["button_icon"];
		}else if($settings["button_icon_style"] == 'icon_mind'){
			$icons=$settings["button_icons_mind"];
		}else if($settings["button_icon_style"] == 'font_awesome_5'){
			ob_start();
				\Elementor\Icons_Manager::render_icon( $settings['button_icon_5'], [ 'aria-hidden' => 'true' ]);
				$icons = ob_get_contents();
			ob_end_clean();
		}
		
		if($before_after == 'before' && !empty($icons)){
			if(!empty($settings["button_icon_style"]) && $settings["button_icon_style"]=='font_awesome_5'){
				$icons_before = '<span class="btn-icon button-before">'.$icons.'</span>';
			}else{
				$icons_before = '<i class="btn-icon button-before '.esc_attr($icons).'"></i>';
			}			
		}
		if($before_after == 'after' && !empty($icons)){
			if(!empty($settings["button_icon_style"]) && $settings["button_icon_style"]=='font_awesome_5'){
				 $icons_after = '<span class="btn-icon button-after">'.$icons.'</span>';
			}else{
				 $icons_after = '<i class="btn-icon button-after '.esc_attr($icons).'"></i>';
			}			  
		}
		
		$button_text = $icons_before.'<span class="btn-text">'.wp_kses_post($button_text).'</span>'. $icons_after;
		
		return $button_text;
	}
}