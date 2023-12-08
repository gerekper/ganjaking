<?php 
/*
Widget Name: Dynamic Devices
Description: layout of devices isplay content.
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

class ThePlus_Dynamic_Devices extends Widget_Base {

	public $TpDoc = THEPLUS_TPDOC;
		
	public function get_name() {
		return 'tp-dynamic-device';
	}

    public function get_title() {
        return esc_html__('Dynamic Device', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-laptop theplus_backend_icon';
    }

	public function get_custom_help_url() {
		$DocUrl = $this->TpDoc . "dynamic-device";

		return esc_url($DocUrl);
	}

    public function get_categories() {
        return array('plus-creatives');
    }
	
	public function get_keywords() {
		return ['dynamic custom skin', 'dynamic loop', 'loop builder', 'skin builder', 'custom skin', 'post skin', 'post loop','dynamic listing', 'dynamic custom post type listing', 'custom post type listing', 'post type'];
	}

    protected function register_controls() {
		
		$this->start_controls_section(
			'device_section',
			[
				'label' => esc_html__( 'Content', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'device_mode',
			[
				'label' => esc_html__( 'Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => [
					'normal'  => esc_html__( 'Normal', 'theplus' ),
					'carousal' => esc_html__( 'Special Carousel', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'device_mockup',
			[
				'label' => esc_html__( 'Type', 'theplus' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'mobile' => [
						'title' => esc_html__( 'Mobile', 'theplus' ),
						'icon' => 'eicon-device-mobile',
					],
					'tablet' => [
						'title' => esc_html__( 'Tablet', 'theplus' ),
						'icon' => 'eicon-device-tablet',
					],
					'laptop' => [
						'title' => esc_html__( 'Laptop', 'theplus' ),
						'icon' => 'eicon-device-laptop',
					],
					'desktop' => [
						'title' => esc_html__( 'Desktop', 'theplus' ),
						'icon' => 'eicon-device-desktop',
					],
					'custom' => [
						'title' => esc_html__( 'Custom', 'theplus' ),
						'icon' => 'eicon-upload-circle-o',
					],
				],
				'default' => 'laptop',
				'toggle' => true,
				'condition'    => [
					'device_mode' => [ 'normal' ],
				],
			]
		);
		$this->add_control(
			'custom_image',
			[
				'label' => esc_html__( 'Custom Mockup', 'theplus' ),
				'type' => Controls_Manager::MEDIA,				
				'dynamic' => [
					'active'   => true,
				],
				'condition'    => [
					'device_mode' => [ 'normal' ],
					'device_mockup' => [ 'custom' ],
				],
			]
		);
		$this->add_control(
			'device_mockup_carousal',
			[
				'label' => esc_html__( 'Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'mobile'  => esc_html__( 'Mobile', 'theplus' ),
					'laptop'  => esc_html__( 'Laptop', 'theplus' ),
					'desktop'  => esc_html__( 'Desktop', 'theplus' ),
					'custom'  => esc_html__( 'Custom', 'theplus' ),
				],
				'default' => 'mobile',
				'toggle' => true,
				'condition'    => [
					'device_mode' => [ 'carousal' ],
				],
			]
		);
		$this->add_control(
			'device_mockup_carousal_image',
			[
				'label' => esc_html__( 'Media Image', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'dynamic' => [
					'active'   => true,
				],
				'condition'    => [
					'device_mode' => [ 'carousal' ],
					'device_mockup_carousal' => [ 'custom' ],
				],
			]
		);
		$this->add_control(
			'device_mobile',
			[
				'label' => esc_html__( 'Mobile Device', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'iphone-white-flat',
				'options' => [
					'iphone-white-flat'  => esc_html__( 'iPhone White (320px x 594px)', 'theplus' ),
					'iphone-x-black' => esc_html__( 'iPhone X Black (320px x 672px)', 'theplus' ),
					'iphone-browser' => esc_html__( 'iPhone Browser (320px x 470px)', 'theplus' ),
					'iphone-minimal' => esc_html__( 'iPhone Minimal (300px x 527px)', 'theplus' ),
					'iphone-minimal-white' => esc_html__( 'iPhone Minimal White (320px x 564px)', 'theplus' ),
					's9-black' => esc_html__( 'S9 Black (320px x 668px)', 'theplus' ),
					's9-jet-black' => esc_html__( 'S9 Jet Black (320px x 672px)', 'theplus' ),
					's9-white' => esc_html__( 'S9 White (320px x 668px)', 'theplus' ),
				],
				'condition'    => [
					'device_mode' => [ 'normal' ],
					'device_mockup' => [ 'mobile' ],
				],
			]
		);
		$this->add_control(
			'device_mobile_carousal',
			[
				'label' => esc_html__( 'Mobile Device', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'iphone-white-flat-carousal',
				'options' => [
					'iphone-white-flat-carousal'  => esc_html__( 'iPhone White (500px x 890px)', 'theplus' ),
					'iphone-x-black' => esc_html__( 'iPhone X Black (320px x 672px)', 'theplus' ),
					'iphone-browser' => esc_html__( 'iPhone Browser (320px x 470px)', 'theplus' ),
					'iphone-minimal' => esc_html__( 'iPhone Minimal (300px x 527px)', 'theplus' ),
					'iphone-minimal-white' => esc_html__( 'iPhone Minimal White (320px x 564px)', 'theplus' ),
					's9-black' => esc_html__( 'S9 Black (320px x 668px)', 'theplus' ),
					's9-jet-black' => esc_html__( 'S9 Jet Black (320px x 672px)', 'theplus' ),
					's9-white' => esc_html__( 'S9 White (320px x 668px)', 'theplus' ),
				],
				'condition'    => [
					'device_mode' => [ 'carousal' ],
					'device_mockup_carousal' => [ 'mobile' ],
				],
			]
		);
		$this->add_control(
			'device_tablet',
			[
				'label' => esc_html__( 'Tablet Device', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'ipad-vertical-white',
				'options' => [
					'ipad-vertical-white'  => esc_html__( 'Ipad Vertical White (480px x 646px)', 'theplus' ),
					'ipad-horizontal-white'  => esc_html__( 'Ipad Horizontal White (470px x 348px)', 'theplus' ),
					'ipad-browser'  => esc_html__( 'Ipad Browser (550px x 625px)', 'theplus' ),
				],
				'condition'    => [
					'device_mode' => [ 'normal' ],
					'device_mockup' => [ 'tablet' ],
				],
			]
		);
		$this->add_control(
			'device_laptop',
			[
				'label' => esc_html__( 'Laptop Device', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'laptop-macbook-black',
				'options' => [
					'laptop-macbook-black'  => esc_html__( 'Macbook Black (800px x 500px)', 'theplus' ),
					'laptop-macbook-minimal'  => esc_html__( 'Macbook Minimal (700px x 414px)', 'theplus' ),
					'laptop-macbook-white-minimal'  => esc_html__( 'Macbook White Minimal(770px x 480px)', 'theplus' ),
					'laptop-macbook-white'  => esc_html__( 'Macbook White (800px x 525px)', 'theplus' ),
					'laptop-windows'  => esc_html__( 'Windows Laptop (800px x 471px)', 'theplus' ),
				],
				'condition'    => [
					'device_mode' => [ 'normal' ],
					'device_mockup' => [ 'laptop' ],
				],
			]
		);
		$this->add_control(
			'device_desktop',
			[
				'label' => esc_html__( 'Desktop Device', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'desktop-imac-minimal',
				'options' => [
					'desktop-imac-minimal'  => esc_html__( 'iMac Minimal (1000px x 565px)', 'theplus' ),
				],
				'condition'    => [
					'device_mode' => [ 'normal' ],
					'device_mockup' => [ 'desktop' ],
				],
			]
		);
		$this->add_control(
			'device_laptop_carousal',
			[
				'label' => esc_html__( 'Laptop Device', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'laptop-macbook-black',
				'options' => [
					'laptop-macbook-black'  => esc_html__( 'Macbook Black (800px x 500px)', 'theplus' ),
					'laptop-macbook-minimal'  => esc_html__( 'Macbook Minimal (700px x 414px)', 'theplus' ),
					'laptop-macbook-white-minimal'  => esc_html__( 'Macbook White Minimal(770px x 480px)', 'theplus' ),
					'laptop-macbook-white'  => esc_html__( 'Macbook White (800px x 525px)', 'theplus' ),
					'laptop-windows'  => esc_html__( 'Windows Laptop (800px x 471px)', 'theplus' ),
				],
				'condition'    => [
					'device_mode' => [ 'carousal' ],
					'device_mockup_carousal' => [ 'laptop' ],
				],				
			]
		);
		$this->add_control(
			'device_desktop_carousal',
			[
				'label' => esc_html__( 'Desktop Device', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'desktop-imac-minimal',
				'options' => [
					'desktop-imac-minimal'  => esc_html__( 'iMac Minimal (1000px x 565px)', 'theplus' ),
				],				
				'condition'    => [
					'device_mode' => [ 'carousal' ],
					'device_mockup_carousal' => [ 'desktop' ],
				],
			]
		);
		$this->add_control(
			'content_type',
			[
				'label' => esc_html__( 'Content', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => [
					'image'  => esc_html__( 'Image', 'theplus' ),
					'template'  => esc_html__( 'Template', 'theplus' ),
					'iframe'  => esc_html__( 'IFrame', 'theplus' ),
				],
				'condition'    => [
					'device_mode' => [ 'normal' ],					
				],				
			]
		);
		$this->add_control(
			'iframe_link',
			[
				'label' => wp_kses_post( "IFrame URL <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "live-website-in-mockup-using-iframes-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'condition'    => [
					'device_mode' => [ 'normal' ],
					'content_type' => [ 'iframe' ],
				],
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
			]
		);	
		$this->add_control(
			'media_image',
			[
				'label' => wp_kses_post( "Media Image <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "create-website-portfolio-showcase-page-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'dynamic' => [
					'active'   => true,
				],
				'condition'    => [
					'device_mode' => [ 'normal' ],
					'content_type' => [ 'image' ],
				],
			]
		);
		$this->add_control(
			'content_template',
			[
				'label' => wp_kses_post( "Elementor Templates <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "elementor-templates-inside-a-device-mockup/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '0',
				'options'     => theplus_get_templates(),
				'label_block' => 'true',
				'condition'   => [
					'device_mode' => [ 'normal' ],
					'content_type' => [ 'template' ],
				],
			]
		);
		$this->add_control(
			'slider_gallery',
			[
				'label' => wp_kses_post( "Select Multiple Images <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "device-slider-in-elementor-for-smartwatch-tablet-custom-device/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => \Elementor\Controls_Manager::GALLERY,
				'default' => [],
				'condition'    => [
					'device_mode' => [ 'carousal' ],
				],
			]
		);
		$this->add_control(
			'device_link_popup',
			[
				'label' => esc_html__( 'Select Link/Popup', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''  => esc_html__( 'Select Option', 'theplus' ),
					'link'  => esc_html__( 'Link', 'theplus' ),
					'popup'  => esc_html__( 'Popup', 'theplus' ),
					
				],
				'condition'    => [
					'device_mode' => [ 'normal' ],
					'content_type' => [ 'image' ],
				],
			]
		);
		$this->add_control(
			'device_link',
			[
				'label' => esc_html__( 'Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'separator' => 'before',
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'default' => [
					'url' => '',
				],
				'condition'    => [
					'device_mode' => [ 'normal' ],
					'content_type' => [ 'image' ],
					'device_link_popup!' => '', 
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
            'section_icon_content',
            [
                'label' => esc_html__('Icon Options', 'theplus'),
                'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'device_mode' => 'normal',
					'content_type!' => 'iframe',
				],
            ]
        );
		$this->add_control(
			'icon_show',
			[
				'label' => esc_html__( 'Show Icon', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'label_on' => esc_html__( 'On', 'theplus' ),
				'default' => 'no',
			]
		);
		$this->add_control(
			'icon_image',
			[
				'label' => esc_html__( 'Upload Icon', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'condition'    => [
					'icon_show' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
            'section_icon_styling',
            [
                'label' => esc_html__('Icon Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'device_mode' => 'normal',
					'icon_show' => 'yes',
				],
            ]
        );
		$this->add_control(
			'icon_continuous_animation',
			[
				'label'        => esc_html__( 'Continuous Animation', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'theplus' ),
				'label_off'    => esc_html__( 'No', 'theplus' ),
				'default' => 'no',
			]
		);
		$this->add_control(
			'icon_animation_effect',
			[
				'label' => esc_html__( 'Animation Effect', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'pulse',
				'options' => [
					'pulse'  => esc_html__( 'Pulse', 'theplus' ),
					'floating'  => esc_html__( 'Floating', 'theplus' ),
					'tossing'  => esc_html__( 'Tossing', 'theplus' ),
					'rotating'  => esc_html__( 'Rotating', 'theplus' ),
					'drop_waves'  => esc_html__( 'Drop Waves', 'theplus' ),
				],
				'render_type'  => 'template',
				'condition' => [
					'icon_continuous_animation' => 'yes',
				],
			]
		);
		$this->add_control(
			'icon_animation_hover',
			[
				'label'        => esc_html__( 'Hover Animation', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'theplus' ),
				'label_off'    => esc_html__( 'No', 'theplus' ),					
				'render_type'  => 'template',
				'condition' => [
					'icon_continuous_animation' => 'yes',
				],
			]
		);
		$this->add_control(
			'icon_animation_duration',
			[	
				'label' => esc_html__( 'Duration Time', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => 's',
				'range' => [
					's' => [
						'min' => 0.5,
						'max' => 50,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 's',
					'size' => 2.5,
				],
				'selectors'  => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-icon .plus-device-icon-inner' => 'animation-duration: {{SIZE}}{{UNIT}};-webkit-animation-duration: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'icon_continuous_animation' => 'yes',
					'icon_animation_effect!' => 'drop_waves',
				],
			]
		);
		$this->add_control(
			'icon_transform_origin',
			[
				'label' => esc_html__( 'Transform Origin', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'center center',
				'options' => [
					'top left'  => esc_html__( 'Top Left', 'theplus' ),
					'top center"'  => esc_html__( 'Top Center', 'theplus' ),
					'top right'  => esc_html__( 'Top Right', 'theplus' ),
					'center left'  => esc_html__( 'Center Left', 'theplus' ),
					'center center'  => esc_html__( 'Center Center', 'theplus' ),
					'center right'  => esc_html__( 'Center Right', 'theplus' ),
					'bottom left'  => esc_html__( 'Bottom Left', 'theplus' ),
					'bottom center'  => esc_html__( 'Bottom Center', 'theplus' ),
					'bottom right'  => esc_html__( 'Bottom Right', 'theplus' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-icon .plus-device-icon-inner' => '-webkit-transform-origin: {{VALUE}};-moz-transform-origin: {{VALUE}};-ms-transform-origin: {{VALUE}};-o-transform-origin: {{VALUE}};transform-origin: {{VALUE}};',
				],
				'render_type'  => 'template',
				'condition' => [
					'icon_continuous_animation' => 'yes',
					'icon_animation_effect' => 'rotating',
				],
			]
		);
		$this->add_control(
			'drop_waves_color',
			[
				'label' => esc_html__( 'Drop Wave Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-icon .plus-device-icon-inner.image-drop_waves:after,{{WRAPPER}} .plus-device-wrapper .plus-device-icon .plus-device-icon-inner.hover_drop_waves:after' => 'background: {{VALUE}}'
				],
				'condition' => [
					'icon_continuous_animation' => 'yes',
					'icon_animation_effect' => 'drop_waves',
				],
			]
		);
		$this->add_control(
			'icon_radius',
			[
				'label'      => esc_html__( 'Icon Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-icon img,{{WRAPPER}} .plus-device-wrapper .plus-device-icon .plus-device-icon-inner,{{WRAPPER}} .plus-device-wrapper .plus-device-icon .plus-device-icon-inner.image-drop_waves:after,{{WRAPPER}} .plus-device-wrapper .plus-device-icon .plus-device-icon-inner.hover_drop_waves:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'icon_size',
			[	
				'label' => esc_html__( 'Icon Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 90,
				],
				'selectors'  => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-icon img' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
            'section_carousal_styling',
            [
                'label' => esc_html__('Carousal Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'device_mode' => 'carousal',
				],
            ]
        );
		$this->add_control(
			'carousal_columns',
			[
				'label' => esc_html__( 'Carousal Column', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'multiple',
				'options' => [
					'single'  => esc_html__( 'Single Slide', 'theplus' ),
					'multiple' => esc_html__( 'Multiple', 'theplus' ),
				],
			]
		);
		
		$this->add_control(
			'carousal_infinite',
			[
				'label' => esc_html__( 'Infinite', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
			]
		);
		$this->add_control(
			'carousal_autoplay',
			[
				'label' => esc_html__( 'Autoplay', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
			]
		);		
		$this->add_control(
			'carousal_autoplay_speed',
			[
				'label' => esc_html__( 'Autoplay Speed', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => 500,
						'max' => 10000,
						'step' => 10,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 4000,
				],
				'condition' => [
					'carousal_autoplay' => 'yes',
				],
			]
		);
		$this->add_control(
			'carousal_speed',
			[
				'label' => esc_html__( 'Slide Speed', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'' => [
						'min' => 200,
						'max' => 5000,
						'step' => 10,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 700,
				],
			]
		);
		$this->add_control(
			'carousal_dots',
			[
				'label' => esc_html__( 'Dots', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
			]
		);
		$this->add_control(
			'carousal_arrows',
			[
				'label' => esc_html__( 'Arrows', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
            'section_carousel_slide_styling',
            [
                'label' => esc_html__('Carousel Slide', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'device_mode' => 'carousal',
				],
            ]
        );
		$this->add_responsive_control(
			'carousal_slide_gap',
			[
				'label' => esc_html__( 'Slide Gap/Space', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -10,
						'max' => 300,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .plus-device-slide.slick-slide' => 'margin-left: {{SIZE}}{{UNIT}};margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'carousal_slide_margin',
			[
				'label' => esc_html__( 'List Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .slick-slider .slick-list' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'carousal_slide_vertical',
			[
				'label' => esc_html__( 'Adjust Slide Space', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .plus-device-slide.slick-slide' => 'margin-top: {{SIZE}}{{UNIT}};margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'carousal_width',
			[
				'label' => esc_html__( 'Carousal Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
						'step' => 2,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 330,
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-carousal-device-mokeup,{{WRAPPER}} .plus-device-wrapper .plus-device-carousal.column-single' => 'max-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .plus-device-slide.slick-slide' => 'width: calc({{SIZE}}{{UNIT}} - 15px);',
				],
			]
		);
		$this->add_responsive_control(
			'carousal_mockup_width',
			[
				'label' => esc_html__( 'Mockup Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
						'step' => 2,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],				
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-carousal-device-mokeup,{{WRAPPER}} .plus-device-wrapper .plus-device-carousal.column-single' => 'max-width: {{SIZE}}{{UNIT}};',					
				],
			]
		);
		$this->add_responsive_control(
			'carousal_mockup_height',
			[
				'label' => esc_html__( 'Mockup Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
						'step' => 2,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'condition'    => [
					'device_mode' => [ 'carousal' ],
				],				
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-carousal-device-mokeup,{{WRAPPER}} .plus-device-wrapper .plus-device-carousal.column-single' => 'min-height: {{SIZE}}{{UNIT}};max-height: {{SIZE}}{{UNIT}};',					
				],
			]
		);
		$this->add_responsive_control(
			'carousal_mockup_top_offset',
			[
				'label' => esc_html__( 'Mockup Offset', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
						'step' => 10,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],				
				'selectors' => [
					'{{WRAPPER}} .plus-carousal-device-mokeup' => 'top: {{SIZE}}{{UNIT}};',					
				],
			]
		);
		$this->add_control(
			'mockup_zindex',
			[
				'label' => esc_html__( 'Z-Index', 'theplus' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => -100,
				'max' => 10000,
				'step' => 10,				
				'selectors' => [
					'{{WRAPPER}} .plus-carousal-device-mokeup' => 'z-index: {{VALUE}};',
				],
			]
		);
		$this->start_controls_tabs( 'slide_shadow_style' );
		$this->start_controls_tab(
			'slide_shadow_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'slide_box_shadow',
				'selector' => '{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .plus-device-slide.slick-slide:not(.slick-center)',
			]
		);
		$this->add_control(
			'slide_opacity_normal',[
				'label' => esc_html__( 'Slide Opacity', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '',
					'size' => 1,
				],
				'range' => [
					'' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .plus-device-slide.slick-slide:not(.slick-center)' => 'opacity: {{SIZE}};',
				],
			]
		);
		$this->add_control(
			'slide_opacity_scale',[
				'label' => esc_html__( 'Slide Scale', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '',
					'size' => 1,
				],
				'range' => [
					'' => [
						'max' => 2,
						'min' => -0.5,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .plus-device-slide.slick-slide:not(.slick-center)' => 'transform: scale({{SIZE}});',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'slide_shadow_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'slide_box_hover_shadow',
				'selector' => '{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .plus-device-slide.slick-slide:hover:not(.slick-center)',
			]
		);
		$this->add_control(
			'slide_opacity_hover',[
				'label' => esc_html__( 'Slide Hover Opacity', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '',
					'size' => 1,
				],
				'range' => [
					'' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .plus-device-slide.slick-slide:hover:not(.slick-center)' => 'opacity: {{SIZE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		/*dots start*/
		$this->start_controls_section(
            'section_carousel_dots_styling',
            [
                'label' => esc_html__('Carousel Dots', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'device_mode' => 'carousal',
					'carousal_dots' => 'yes',
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_carousel_dots_style' );
		$this->start_controls_tab(
			'tab_cd_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_responsive_control(
            'cd_font_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-dots button::before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_control(
			'cd_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-dots button::before' => 'color:{{VALUE}};opacity:1;',
				],
			]
		);		
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_cd_active',
			[
				'label' => esc_html__( 'Active', 'theplus' ),
			]
		);
		$this->add_responsive_control(
            'cd_font_size_ah',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-dots .slick-active button::before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_control(
			'cd_color_ah',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-dots .slick-active button::before' => 'color:{{VALUE}};opacity:1;',
				],
			]
		);	
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control(
            'cd_gap',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Gap', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 150,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-dots li' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_responsive_control(
            'cd_offset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Offset', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-dots' => 'bottom: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->end_controls_section();
		/*dots end*/

		/*arrows start*/
		$this->start_controls_section(
            'section_carousel_arrows_styling',
            [
                'label' => esc_html__('Carousel Arrows', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'device_mode' => 'carousal',
					'carousal_arrows' => 'yes',
				],
            ]
        );
		$this->add_responsive_control(
            'ca_offset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Gap', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-prev' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-next' => 'right: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_responsive_control(
            'ca_offset_up_down',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Offset', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -700,
						'max' => 700,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-prev,{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-next' => 'top: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_responsive_control(
            'ca_icon_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 180,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-prev:before,
					{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-next:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_responsive_control(
            'ca_bg_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Background Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-prev,
					{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-next' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_carousel_arrows_style' );
		$this->start_controls_tab(
			'tab_ca_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'can_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-prev:before,{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-next:before' => 'color:{{VALUE}};',
				],
			]
		);
		$this->add_control(
			'can_background',
			[
				'label' => esc_html__( 'Background', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-prev,{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-next' => 'background:{{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'can_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-prev,{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-next',
			]
	    );
		$this->add_responsive_control(
			'can_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-prev,{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'can_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-prev,{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-next',				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_ca_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'cah_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-prev:hover:before,{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-next:hover:before' => 'color:{{VALUE}};',
				],
			]
		);
		$this->add_control(
			'cah_background',
			[
				'label' => esc_html__( 'Background', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-prev:hover,{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-next:hover' => 'background:{{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'cah_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-prev:hover,{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-next:hover',
			]
	    );
		$this->add_responsive_control(
			'cah_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-prev:hover,{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-next:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'cah_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-prev:hover,{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .slick-next:hover',				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*arrows end*/

		$this->start_controls_section(
            'section_device_styling',
            [
                'label' => esc_html__('Device Layout', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
            'device_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width Adjust(%)', 'theplus'),
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min'	=> 10,
						'max'	=> 100,
						'step' => 0.5,
					],
				],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default' => [
					'size' => 100,
					'unit' => '%',
				],
				'tablet_default' => [
					'size' => 100,
					'unit' => '%',
				],
				'mobile_default' => [
					'size' => 100,
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper' => 'width: {{SIZE}}%;margin: 0 auto;text-align: center;display: block;',
				],
				'render_type' => 'ui',
            ]
        );
		$this->add_responsive_control(
			'device_alignment',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'unset' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'default' => 'unset',
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper' => 'float: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'device_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'allowed_dimensions' => 'vertical',
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'device_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
            'section_device_bg_styling',
            [
                'label' => esc_html__('Device Background', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
			'scroll_image_effect',
			[
				'label' => esc_html__( 'Scroll Image', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,				
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'default' => 'no',
				'condition'    => [
					'device_mode' => [ 'normal' ],
					'content_type' => [ 'image' ],
				],
			]
		);
		$this->add_control(
			'scroll_image_effect_manual',
			[
				'label' => esc_html__( 'Manual Scroll', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,				
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'device_mode' => [ 'normal' ],
					'content_type' => [ 'image' ],
					'scroll_image_effect' => 'yes',
				],
			]
		);
		$this->add_control(
            'scroll_image_effect_manual_image',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => [ '%' ],
				'range' => [
					'px' => [
						'step' => 1,
						'min'  => 1,
						'max'  => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-media-inner .creative-scroll-image.manual img' => 'width: {{SIZE}}%;',
				],
				'condition' => [
					'device_mode' => [ 'normal' ],
					'content_type' => [ 'image' ],
					'scroll_image_effect' => 'yes',
					'scroll_image_effect_manual' => 'yes',
				],
            ]
        );
		$this->add_responsive_control(
			'transition_duration',
			[
				'label' => wp_kses_post( "Transition Duration <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "create-website-portfolio-showcase-page-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 4,
				],
				'range' => [
					'px' => [
						'step' => 0.1,
						'min'  => 0.1,
						'max'  => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-media-inner .creative-scroll-image' => 'transition: background-position {{SIZE}}s ease-in-out;-webkit-transition: background-position {{SIZE}}s ease-in-out;',
				],
				'condition' => [
					'device_mode' => [ 'normal' ],
					'content_type' => [ 'image' ],
					'scroll_image_effect' => 'yes',
				],
			]
		);
		$this->add_control(
			'dd_unique_id',
			[
				'label' => esc_html__( 'Dynamic Device Connection ID', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'description' => 'Note : This option is to connect Multiple Dynamic Device for Same Hover Effect',
				'condition' => [
					'device_mode' => [ 'normal' ],
					'content_type' => [ 'image' ],
					'scroll_image_effect' => 'yes',
					'scroll_image_effect_manual!' => 'yes',
				],
			]
		);
		$this->add_control(
			'shadow_options',
			[
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'type' => Controls_Manager::HEADING,				
			]
		);
		$this->start_controls_tabs( 'shadow_style' );
		$this->start_controls_tab(
			'shadow_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control('drop_shadow',
			[
				'label'=>__('Drop Shadow','theplus'),
				'type'=>Controls_Manager::POPOVER_TOGGLE,
				'label_off'=>__('Disable','theplus'),
				'label_on'=>__('Enable','theplus'),
				'return_value'=>'yes',
				'default'=>'no',
			]
		);
		$this->start_popover();
		$this->add_control(
            'dd_ds_x',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('X', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
            ]
        );
		$this->add_control(
            'dd_ds_y',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Y', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
            ]
        );
		$this->add_control(
            'dd_ds_blur',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Blur', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
						'step' => 0.1,
					],
				],				
				'render_type' => 'ui',
            ]
        );
		$this->add_control(
			'dd_ds_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-shape,{{WRAPPER}} .plus-device-wrapper .plus-carousal-device-mokeup' => 'filter : drop-shadow({{dd_ds_x.SIZE}}{{dd_ds_x.UNIT}} {{dd_ds_y.SIZE}}{{dd_ds_y.UNIT}} {{dd_ds_blur.SIZE}}{{dd_ds_blur.UNIT}} {{VALUE}})',
				],
			]
		);
		$this->end_popover();
		$this->end_controls_tab();
		$this->start_controls_tab(
			'shadow_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control('drop_shadow_h',
			[
				'label'=>__('Drop Shadow','theplus'),
				'type'=>Controls_Manager::POPOVER_TOGGLE,
				'label_off'=>__('Disable','theplus'),
				'label_on'=>__('Enable','theplus'),
				'return_value'=>'yes',
				'default'=>'no',
			]
		);
		$this->start_popover();
		$this->add_control(
            'dd_ds_x_h',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('X', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
            ]
        );
		$this->add_control(
            'dd_ds_y_h',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Y', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
            ]
        );
		$this->add_control(
            'dd_ds_blur_h',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Blur', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
						'step' => 0.1,
					],
				],				
				'render_type' => 'ui',
            ]
        );
		$this->add_control(
			'dd_ds_color_h',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper:hover .plus-device-shape,{{WRAPPER}} .plus-device-wrapper .plus-carousal-device-mokeup:hover' => 'filter : drop-shadow({{dd_ds_x_h.SIZE}}{{dd_ds_x_h.UNIT}} {{dd_ds_y_h.SIZE}}{{dd_ds_y_h.UNIT}} {{dd_ds_blur_h.SIZE}}{{dd_ds_blur_h.UNIT}} {{VALUE}})',
				],
			]
		);
		$this->end_popover();		
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		/*image slide*/
		$this->start_controls_section(
            'section_image_slide_styling',
            [
                'label' => esc_html__('Image', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'    => [
					'device_mode' => [ 'carousal' ],					
				],
            ]
        );
		$this->add_responsive_control(
			'image_border_radius_slide',
			[
				'label'      => esc_html__( 'Image Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .plus-device-slide img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'cimage_width', [
				'label' => esc_html__( 'Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px','%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .plus-device-slide img' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);
		$this->add_responsive_control(
			'cimage_height', [
				'label' => esc_html__( 'Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px','%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .plus-device-slide img' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'cimage_offset', [
				'label' => esc_html__( 'Top Offset', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .plus-device-slide img' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'cimage_l_offset', [
				'label' => esc_html__( 'Left Offset', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .plus-device-slide img' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'cmedia_transform',
			[
				'label' => esc_html__( 'Transform', 'theplus' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'Default', 'theplus' ),
				'label_on' => __( 'Custom', 'theplus' ),
				'return_value' => 'yes',
			]
		);
		$this->start_popover();
		$this->add_control(
			'ctransform_x',
			[
				'label' => esc_html__( 'X', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 1000,
						'min' => -1000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'    => [
					'cmedia_transform' => 'yes',
				],
			]
		);
		$this->add_control(
			'ctransform_y',
			[
				'label' => esc_html__( 'Y', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 1000,
						'min' => -1000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'    => [
					'cmedia_transform' => 'yes',
				],
			]
		);
		$this->add_control(
			'ctransform_rotate',
			[
				'label' => esc_html__( 'Rotate', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 360,
						'min' => -360,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal .plus-device-slide img' => 'transform: rotate({{ctransform_rotate.SIZE}}deg) translateY({{ctransform_y.SIZE}}{{ctransform_y.UNIT}}) translateX({{ctransform_x.SIZE}}{{ctransform_x.UNIT}});-ms-transform:rotate({{ctransform_rotate.SIZE}}deg) translateY({{ctransform_y.SIZE}}{{ctransform_y.UNIT}}) translateX({{ctransform_x.SIZE}}{{ctransform_x.UNIT}});-moz-transform:rotate({{ctransform_rotate.SIZE}}deg) translateY({{ctransform_y.SIZE}}{{ctransform_y.UNIT}}) translateX({{ctransform_x.SIZE}}{{ctransform_x.UNIT}});-webkit-transform:rotate({{ctransform_rotate.SIZE}}deg) translateY({{ctransform_y.SIZE}}{{ctransform_y.UNIT}}) translateX({{ctransform_x.SIZE}}{{ctransform_x.UNIT}});',
				 ],
				'condition'    => [
					'cmedia_transform' => 'yes',
				],
			]
		);
		$this->end_popover();
		$this->add_control(
			'image_zindex_slide',
			[
				'label' => esc_html__( 'Z-Index', 'theplus' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => -100,
				'max' => 10000,
				'step' => 1,
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-carousal' => 'z-index: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/*image slide*/

		/*image start*/
		$this->start_controls_section(
            'section_image_styling',
            [
                'label' => esc_html__('Image', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'    => [
					'device_mode' => [ 'normal' ],
					'content_type' => [ 'image' ],
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
					'{{WRAPPER}} .plus-device-wrapper .plus-device-media img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'image_outer_border_radius',
			[
				'label'      => esc_html__( 'Scroll Image Outer Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-device-wrapper .plus-media-inner .creative-scroll-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'scroll_image_effect' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'image_width', [
				'label' => esc_html__( 'Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px','%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],				
				'condition'    => [
					'device_mode' => [ 'normal' ],
					'device_mockup' => [ 'custom' ],
					'scroll_image_effect!' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper.device-type-custom.custom-device-mockup .plus-device-inner .plus-device-media img' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);
		$this->add_responsive_control(
			'image_height', [
				'label' => esc_html__( 'Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px','%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],				
				'condition'    => [
					'device_mode' => [ 'normal' ],
					'device_mockup' => [ 'custom' ],
					'scroll_image_effect!' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper.device-type-custom.custom-device-mockup .plus-device-inner .plus-device-media img' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'image_offset', [
				'label' => esc_html__( 'Top Offset', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],				
				'condition'    => [
					'device_mode' => [ 'normal' ],
					'device_mockup' => [ 'custom' ],
					'scroll_image_effect!' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper.device-type-custom.custom-device-mockup .plus-device-inner .plus-device-media img' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'image_l_offset', [
				'label' => esc_html__( 'Left Offset', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],				
				'condition'    => [
					'device_mode' => [ 'normal' ],
					'device_mockup' => [ 'custom' ],
					'scroll_image_effect!' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper.device-type-custom.custom-device-mockup .plus-device-inner .plus-device-media img' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'image_s_width', [
				'label' => esc_html__( 'Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px','%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],				
				'condition'    => [
					'device_mode' => [ 'normal' ],
					'device_mockup' => [ 'custom' ],
					'scroll_image_effect' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper.device-type-custom.custom-device-mockup .plus-device-inner .plus-device-media.tp-img-scrl-enable' => 'width: {{SIZE}}{{UNIT}}  !important;',
				],
			]
		);
		$this->add_responsive_control(
			'image_s_height', [
				'label' => esc_html__( 'Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px','%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],				
				'condition'    => [
					'device_mode' => [ 'normal' ],
					'device_mockup' => [ 'custom' ],
					'scroll_image_effect' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper.device-type-custom.custom-device-mockup .plus-device-inner .plus-device-media.tp-img-scrl-enable' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'image_s_offset', [
				'label' => esc_html__( 'Top Offset', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],				
				'condition'    => [
					'device_mode' => [ 'normal' ],
					'device_mockup' => [ 'custom' ],
					'scroll_image_effect' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper.device-type-custom.custom-device-mockup .plus-device-inner .plus-device-media.tp-img-scrl-enable' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'image_s_l_offset', [
				'label' => esc_html__( 'Left Offset', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],				
				'condition'    => [
					'device_mode' => [ 'normal' ],
					'device_mockup' => [ 'custom' ],
					'scroll_image_effect' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper.device-type-custom.custom-device-mockup .plus-device-inner .plus-device-media.tp-img-scrl-enable' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'image_zindex',
			[
				'label' => esc_html__( 'Z-Index', 'theplus' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => -100,
				'max' => 10000,
				'step' => 10,
				'condition'    => [
					'device_mode' => [ 'normal' ],
					'device_mockup' => [ 'custom' ],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper.device-type-custom.custom-device-mockup .plus-device-inner .plus-device-media' => 'z-index: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'media_transform',
			[
				'label' => esc_html__( 'Transform', 'theplus' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'Default', 'theplus' ),
				'label_on' => __( 'Custom', 'theplus' ),
				'return_value' => 'yes',
			]
		);
		$this->start_popover();
		$this->add_control(
			'transform_x',
			[
				'label' => esc_html__( 'X', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 1000,
						'min' => -1000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'    => [
					'media_transform' => 'yes',
				],
			]
		);
		$this->add_control(
			'transform_y',
			[
				'label' => esc_html__( 'Y', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 1000,
						'min' => -1000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'    => [
					'media_transform' => 'yes',
				],
			]
		);
		$this->add_control(
			'transform_rotate',
			[
				'label' => esc_html__( 'Rotate', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 360,
						'min' => -360,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-media' => 'transform: rotate({{transform_rotate.SIZE}}deg) translateY({{transform_y.SIZE}}{{transform_y.UNIT}}) translateX({{transform_x.SIZE}}{{transform_x.UNIT}});-ms-transform:rotate({{transform_rotate.SIZE}}deg) translateY({{transform_y.SIZE}}{{transform_y.UNIT}}) translateX({{transform_x.SIZE}}{{transform_x.UNIT}});-moz-transform:rotate({{transform_rotate.SIZE}}deg) translateY({{transform_y.SIZE}}{{transform_y.UNIT}}) translateX({{transform_x.SIZE}}{{transform_x.UNIT}});-webkit-transform:rotate({{transform_rotate.SIZE}}deg) translateY({{transform_y.SIZE}}{{transform_y.UNIT}}) translateX({{transform_x.SIZE}}{{transform_x.UNIT}});',
				 ],
				'condition'    => [
					'media_transform' => 'yes',
				],
			]
		);
		$this->end_popover();
		$this->end_controls_section();
		/*image end*/

		/*template end*/		
		$this->start_controls_section(
            'section_template_styling',
            [
                'label' => esc_html__('Template', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'    => [
					'device_mode' => [ 'normal' ],
					'content_type' => [ 'template' ],
				],
            ]
        );
		$this->add_responsive_control(
			'template_width', [
				'label' => esc_html__( 'Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px','%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],				
				'condition'    => [					
					'device_mockup' => [ 'custom' ],
					'scroll_image_effect!' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-inner .plus-device-media' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'template_height', [
				'label' => esc_html__( 'Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px','%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],				
				'condition'    => [					
					'device_mockup' => [ 'custom' ],
					'scroll_image_effect!' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-inner .plus-device-media' => 'height: {{SIZE}}{{UNIT}};overflow:hidden;',
				],
			]
		);
		$this->add_responsive_control(
			'template_offset', [
				'label' => esc_html__( 'Top Offset', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-inner .plus-device-media' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'template_l_offset', [
				'label' => esc_html__( 'Left Offset', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-inner .plus-device-media' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'template_zindex',
			[
				'label' => esc_html__( 'Z-Index', 'theplus' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => -100,
				'max' => 10000,
				'step' => 10,
				'condition'    => [					
					'device_mockup' => [ 'custom' ],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-device-inner .plus-device-media' => 'z-index: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'scroll_template_effect',
			[
				'label' => esc_html__( 'On Hover Scroll', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,				
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'default' => 'no',
			]
		);
		$this->add_responsive_control(
			'temp_transition_duration',
			[
				'label'   => esc_html__( 'Transition Duration', 'theplus' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'step' => 1,
						'min'  => 1,
						'max'  => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-device-wrapper .plus-media-inner .elementor>.elementor-inner>.elementor-section-wrap' => '-webkit-transition:all {{SIZE}}s linear;-moz-transition:all {{SIZE}}s linear;-o-transition:all {{SIZE}}s linear;-ms-transition:all {{SIZE}}s linear;transition:all {{SIZE}}s linear;',
				],
				'condition' => [
					'scroll_template_effect' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*template end*/

		/*image Scrolling Bar*/
		$this->start_controls_section(
            'section_device_scroll_styling',
            [
                'label' => esc_html__('Image Scrolling Bar', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'scroll_image_effect' => 'yes',
					'scroll_image_effect_manual' => 'yes',
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_scrolling_bar_style' );
		$this->start_controls_tab(
			'tab_scrolling_bar_scrollbar',
			[
				'label' => esc_html__( 'Scrollbar', 'theplus' ),				
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
					'{{WRAPPER}} .plus-device-wrapper .plus-media-inner .creative-scroll-image.manual::-webkit-scrollbar' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'scroll_scrollbar_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-device-wrapper .plus-media-inner .creative-scroll-image.manual::-webkit-scrollbar',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_scrolling_bar_thumb',
			[
				'label' => esc_html__( 'Thumb', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'scroll_thumb_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-device-wrapper .plus-media-inner .creative-scroll-image.manual::-webkit-scrollbar-thumb',
			]
		);
		$this->add_responsive_control(
			'scroll_thumb_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-device-wrapper .plus-media-inner .creative-scroll-image.manual::-webkit-scrollbar-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',					
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'scroll_thumb_shadow',
				'selector' => '{{WRAPPER}} .plus-device-wrapper .plus-media-inner .creative-scroll-image.manual::-webkit-scrollbar-thumb',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_scrolling_bar_track',
			[
				'label' => esc_html__( 'Track', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'scroll_track_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-device-wrapper .plus-media-inner .creative-scroll-image.manual::-webkit-scrollbar-track',
			]
		);
		$this->add_responsive_control(
			'scroll_track_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-device-wrapper .plus-media-inner .creative-scroll-image.manual::-webkit-scrollbar-track' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'scroll_track_shadow',
				'selector' => '{{WRAPPER}} .plus-device-wrapper .plus-media-inner .creative-scroll-image.manual::-webkit-scrollbar-track',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
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
		include THEPLUS_PATH. 'modules/widgets/theplus-needhelp.php';
	}
	
    protected function render() {
		$settings = $this->get_settings_for_display();
		$dd_unique_id = !empty($settings['dd_unique_id']) ? $settings['dd_unique_id'] : '';

		/*--On Scroll View Animation ---*/
			include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';

		/*--Plus Extra ---*/
			$PlusExtra_Class = "plus-flip-box-widget";
			include THEPLUS_PATH. 'modules/widgets/theplus-widgets-extra.php';

		$media_content=$layout_shape=$device_class ='';
		if($settings["device_mode"]=='normal'){
		
			if($settings["device_mockup"]=='mobile'){
				$layout_shape='<img src="'.THEPLUS_ASSETS_URL.'images/devices/'.$settings["device_mobile"].'.png" class="plus-device-image" alt="Plus mobile device">';
				$device_class .= $settings["device_mobile"];
			}else if($settings["device_mockup"]=='tablet'){
				$layout_shape='<img src="'.THEPLUS_ASSETS_URL.'images/devices/'.$settings["device_tablet"].'.png" class="plus-device-image" alt="Plus tablet device">';
				$device_class .= $settings["device_tablet"];
			}else if($settings["device_mockup"]=='laptop'){
				$layout_shape='<img src="'.THEPLUS_ASSETS_URL.'images/devices/'.$settings["device_laptop"].'.png" class="plus-device-image" alt="Plus laptop device">';
				$device_class .= $settings["device_laptop"];
			}else if($settings["device_mockup"]=='desktop'){
				$layout_shape='<img src="'.THEPLUS_ASSETS_URL.'images/devices/'.$settings["device_desktop"].'.png" class="plus-device-image" alt="Plus desktop device">';
				$device_class .= $settings["device_desktop"];
			}else if($settings["device_mockup"]=='custom'){
				$custom_image = !empty($settings['custom_image']['url']) ? $settings['custom_image']['url'] : '';
				$layout_shape='<img src="'.$custom_image.'" class="plus-device-image" alt="Plus custom device">';
				$device_class .= ' custom-device-mockup';
			}
			
			$device_url=$device_url_close='';
			if ( !empty($settings["device_link_popup"]) && ! empty( $settings['device_link']['url'] ) ) {
				$this->add_render_attribute( 'device_url', 'href', $settings['device_link']['url'] );
				if ( $settings['device_link']['is_external'] ) {
					$this->add_render_attribute( 'device_url', 'target', '_blank' );
				}
				if ( $settings['device_link']['nofollow'] ) {
					$this->add_render_attribute( 'device_url', 'rel', 'nofollow' );
				}
				if(!empty($settings["device_link_popup"]) && $settings["device_link_popup"]=='popup'){
					$this->add_render_attribute( 'device_url', 'data-lity', '' );
				}
				$device_url = '<a '.$this->get_render_attribute_string( "device_url" ).' class="plus-media-link">';
				$device_url_close = '</a>';
			}
			$icon_effect='';
			if(!empty($settings["icon_continuous_animation"]) && $settings["icon_continuous_animation"]=='yes'){
				if($settings["icon_animation_hover"]=='yes'){
					$animation_class='hover_';
				}else{
					$animation_class='image-';
				}
				$icon_effect=$animation_class.$settings["icon_animation_effect"];
			}
			$device_icon_center='';
			if(!empty($settings["icon_show"]) && $settings["icon_show"]=='yes' && !empty($settings["icon_image"]["url"])){
				$image_id=$settings["icon_image"]["id"];
				$imgSrc= tp_get_image_rander( $image_id,'full');
				$device_icon_center .= '<div class="plus-device-icon">';
					$device_icon_center .= '<div class="plus-device-icon-inner '.esc_attr($icon_effect).'">';
						$device_icon_center .= $imgSrc;
					$device_icon_center .= '</div>';
				$device_icon_center .= '</div>';
			}

			$content_type = !empty($settings['content_type']) ? $settings['content_type'] : '';
			$imgAlt = !empty($settings['media_image']['alt']) ? $settings['media_image']['alt'] : '';

			$scroll_image=$scroll_image_content=$scroll_class='';
			if(!empty($settings["scroll_image_effect"]) && $settings['scroll_image_effect']=='yes'){				
				$scroll_image='scroll-image-wrap';
				$scroll_image_effect_manual = isset($settings['scroll_image_effect_manual']) ? $settings['scroll_image_effect_manual'] : 'no';
				if(isset($scroll_image_effect_manual) && $scroll_image_effect_manual === 'yes' && !empty($settings['media_image']['url']) && !empty($content_type) && $content_type=='image'){
					$scroll_image_content ='<div class="creative-scroll-image manual"><img src="'.esc_url($settings['media_image']['url']).'" alt="'.esc_attr($imgAlt).'"/></div>';
				}else{
					$this->add_render_attribute( 'scroll-image', 'style', 'background-image: url(' . esc_url($settings['media_image']['url']) . ');' );
					$scroll_image_content ='<div class="creative-scroll-image" ' . $this->get_render_attribute_string( 'scroll-image' ) . '></div>';
				}

				$scroll_class = ' tp-img-scrl-enable';
			}
			
			if(!empty($layout_shape)){
				$media_content= '<div class="plus-media-inner">';
					$media_content .= '<div class="plus-media-screen">';
						$media_content .= '<div class="plus-media-screen-inner '.esc_attr($scroll_image).'">';

							
							$content_template = !empty($settings['content_template']) ? $settings['content_template'] : '';
							
							if(!empty($content_type) && $content_type=='iframe' && !empty($settings['iframe_link']['url'])){
								$media_content .= '<iframe width="100%" height="100%" frameborder="0" src="'.esc_url($settings['iframe_link']['url']).'"/>';
							}else if(!empty($content_type) && $content_type=='template' && !empty($content_template)){
								$media_content .= Theplus_Element_Load::elementor()->frontend->get_builder_content_for_display( $content_template );
							}else{
								if(!empty($settings["scroll_image_effect"]) && $settings['scroll_image_effect']=='yes'){
									$media_content .= $scroll_image_content;
								}else if(!empty($settings["media_image"]["url"])){
									$image_id=$settings["media_image"]["id"];
									$imgSrc1= tp_get_image_rander( $image_id,'full', [ 'class' => 'plus-media-image' ] );								
									$media_content .=$imgSrc1;
								}
							}							
							$media_content .= $device_url;
								$media_content .= $device_icon_center;
							$media_content .= $device_url_close;
						$media_content .= '</div>';
					$media_content .= '</div>';
				$media_content .= '</div>';
			}
		}
		
		$slide_image=$carousal_device=$carousal_attr='';
		if($settings["device_mode"]=='carousal'){
			if($settings["device_mockup_carousal"]=='mobile'){
				$layout_shape='<img src="'.THEPLUS_ASSETS_URL.'images/devices/'.$settings["device_mobile_carousal"].'.png" class="plus-device-image" alt="Device mobile">';
				$carousal_device .= $settings["device_mobile_carousal"];
			}
			if($settings["device_mockup_carousal"]=='laptop'){
				$layout_shape='<img src="'.THEPLUS_ASSETS_URL.'images/devices/'.$settings["device_laptop_carousal"].'.png" class="plus-device-image" alt="Device mobile">';
				$carousal_device .= $settings["device_laptop_carousal"];
			}
			if($settings["device_mockup_carousal"]=='desktop'){
				$layout_shape='<img src="'.THEPLUS_ASSETS_URL.'images/devices/'.$settings["device_desktop_carousal"].'.png" class="plus-device-image" alt="Device mobile">';
				$carousal_device .= $settings["device_desktop_carousal"];
			}

			if($settings["device_mockup_carousal"]=='custom'){
				$custom_image = !empty($settings['device_mockup_carousal_image']['url']) ? $settings['device_mockup_carousal_image']['url'] : '';
				$layout_shape='<img src="'.$custom_image.'" class="plus-device-image" alt="Plus custom device">';
				$carousal_device .= ' custom-device-mockup';
			}

			if(!empty($settings['slider_gallery'])){
				foreach ( $settings['slider_gallery'] as $image ) {
					$image_id=$image["id"];
					$imgSrc2= tp_get_image_rander( $image_id,'full');
					
					$slide_image .= '<div class="plus-device-slide">';
						$slide_image .= $imgSrc2;
					$slide_image .= '</div>';
				}
			}
			
			$infinite = ($settings['carousal_infinite']=='yes') ? 'true' : 'false';
			$autoplay = ($settings['carousal_autoplay']=='yes') ? 'true' : 'false';
			$autoplay_speed = (!empty($settings['carousal_autoplay_speed']["size"])) ? $settings['carousal_autoplay_speed']["size"] : '4000';
			$speed = (!empty($settings['carousal_speed']["size"])) ? $settings['carousal_speed']["size"] : '700';
			
			$carousal_dots = ($settings['carousal_dots']=='yes') ? 'true' : 'false';
			$carousal_arrows = ($settings['carousal_arrows']=='yes') ? 'true' : 'false';

			$carousal_attr .= ' data-infinite="'.esc_attr($infinite).'"';
			$carousal_attr .= ' data-autoplay="'.esc_attr($autoplay).'"';
			$carousal_attr .= ' data-dots="'.esc_attr($carousal_dots).'"';
			$carousal_attr .= ' data-arrows="'.esc_attr($carousal_arrows).'"';
			$carousal_attr .= ' data-autoplay_speed="'.esc_attr($autoplay_speed).'"';
			$carousal_attr .= ' data-speed="'.esc_attr($speed).'"';
		}
		$uid=uniqid("device");
		$device_mockup=$settings["device_mockup"];

		$ddclass=$ddattr='';
		if(!empty($dd_unique_id) && !empty($settings["device_mode"]) && $settings["device_mode"]=='normal' && !empty($settings["scroll_image_effect"]) && $settings["scroll_image_effect"]=='yes'){
			$ddclass= 'tp-dd-multi-connect '.$dd_unique_id;
			$ddattr = ' data-connectdd=".'.esc_attr($dd_unique_id).'"';
		}		
		$output= '<div class="plus-device-wrapper device-type-'.esc_attr($device_mockup).' '.esc_attr($device_class).' '.esc_attr($animated_class).' '.esc_attr($ddclass).'" '.$animation_attr.' '.$ddattr .'>';
			$output .= '<div class="plus-device-inner">';
				if($settings["device_mode"]=='normal'){
					$scrolljsclass='';
					if((!empty($settings["scroll_image_effect"]) && $settings['scroll_image_effect']=='yes') || (!empty($settings['scroll_template_effect']) && $settings['scroll_template_effect']=='yes')){
						$scrolljsclass = ' tp-scroll-img-js';
					}

					$output .= '<div class="plus-device-content '.esc_attr($scrolljsclass).'">';
						$output .= '<div class="plus-device-shape">';
							$output .= $layout_shape;
						$output .= '</div>';
						$output .= '<div class="plus-device-media '.esc_attr($scroll_class).'">';
							$output .= $media_content;
						$output .= '</div>';
					$output .= '</div>';
					
				}else if($settings["device_mode"]=='carousal'){
					$output .= '<div class="plus-carousal-device-mokeup">';
						$output .= '<div class="plus-device-content">';
							$output .= $layout_shape;
						$output .= '</div>';
					$output .= '</div>';
					$output .='<div class="plus-device-carousal column-'.esc_attr($settings["carousal_columns"]).' '.esc_attr($uid).'" data-id="'.esc_attr($uid).'" '.$carousal_attr.'>';
						$output .= $slide_image;
					$output .= '</div>';
				}
			$output .= '</div>';
		$output .= '</div>';
		
		echo $before_content.$output.$after_content;
	}

	protected function content_template() {
	
	}
}