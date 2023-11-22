<?php

namespace ElementPack\Modules\DeviceSlider\Widgets;

use Elementor\Repeater;
use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use ElementPack\Utils;
use element_pack_helper;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Device_Slider extends Module_Base {

	public function get_name() {
		return 'bdt-device-slider';
	}

	public function get_title() {
		return BDTEP . esc_html__('Device Slider', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-device-slider';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['device', 'slider', 'desktop', 'laptop', 'mobile'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-font', 'ep-device-slider'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['imagesloaded', 'ep-scripts'];
		} else {
			return ['imagesloaded'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/GACXtqun5Og';
	}

	protected function register_controls() {
		$this->register_query_section_controls();
	}

	private function register_query_section_controls() {

		$this->start_controls_section(
			'section_content_sliders',
			[
				'label' => esc_html__('Sliders', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'device_type',
			[
				'label'   => esc_html__('Select Device', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'desktop',
				'options' => [
					'chrome'      => esc_html__('Chrome', 'bdthemes-element-pack'),
					'chrome-dark' => esc_html__('Chrome Dark', 'bdthemes-element-pack'),
					'desktop'     => esc_html__('Desktop', 'bdthemes-element-pack'),
					'edge'        => esc_html__('Edge', 'bdthemes-element-pack'),
					'edge-dark'   => esc_html__('Edge Dark', 'bdthemes-element-pack'),
					'firefox'     => esc_html__('Firefox', 'bdthemes-element-pack'),
					'iphonex'     => esc_html__('iPhone X', 'bdthemes-element-pack'),
					'imac'        => esc_html__('iMac', 'bdthemes-element-pack'),
					'mobile'      => esc_html__('Mobile', 'bdthemes-element-pack'),
					'macbookpro'  => esc_html__('Macbook Pro', 'bdthemes-element-pack'),
					'macbookair'  => esc_html__('Macbook Air', 'bdthemes-element-pack'),
					'safari'      => esc_html__('Safari', 'bdthemes-element-pack'),
					'tablet'      => esc_html__('Tablet', 'bdthemes-element-pack'),
					'custom'      => esc_html__('Custom', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'hr_1',
			[
				'type'    => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'rotation_state',
			[
				'label'   => esc_html__('Horizontal Rotation State', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'     => 'device_type',
							'value'    => 'tablet',
						],
						[
							'name'     => 'device_type',
							'value'    => 'mobile',
						],
					],
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__('Title', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__('Slide Title', 'bdthemes-element-pack'),
				'label_block' => true,
				'dynamic'     => ['active' => true],
			]
		);

		$repeater->add_control(
			'title_link',
			[
				'label'         => esc_html__('Title Link', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::URL,
				'default'       => ['url' => ''],
				'show_external' => false,
				'dynamic'       => ['active' => true],
				'condition'     => [
					'title!' => ''
				]
			]
		);

		$repeater->add_control(
			'background',
			[
				'label'   => esc_html__('Background', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'color',
				'options' => [
					'color'   => [
						'title' => esc_html__('Color', 'bdthemes-element-pack'),
						'icon'  => 'eicon-paint-brush',
					],
					'image'   => [
						'title' => esc_html__('Image', 'bdthemes-element-pack'),
						'icon'  => 'eicon-image',
					],
					'video'   => [
						'title' => esc_html__('Video', 'bdthemes-element-pack'),
						'icon'  => 'eicon-play',
					],
					'youtube' => [
						'title' => esc_html__('Youtube', 'bdthemes-element-pack'),
						'icon'  => 'eicon-youtube',
					],
				],
			]
		);

		$repeater->add_control(
			'color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#14ABF4',
				'condition' => [
					'background' => 'color'
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'background-color: {{VALUE}}',
				],
			]
		);

		$repeater->add_control(
			'image',
			[
				'label'     => esc_html__('Image', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::MEDIA,
				'default'   => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'background' => 'image'
				],
				'dynamic'   => ['active' => true],
			]
		);

		$repeater->add_control(
			'video_link',
			[
				'label'     => esc_html__('Video Link', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'condition' => [
					'background' => 'video'
				],
				'default'   => '//clips.vorwaerts-gmbh.de/big_buck_bunny.mp4',
				'dynamic'   => ['active' => true],
			]
		);

		$repeater->add_control(
			'youtube_link',
			[
				'label'     => esc_html__('Youtube Link', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'condition' => [
					'background' => 'youtube'
				],
				'default'   => 'https://youtu.be/YE7VzlLtp-4',
				'dynamic'   => ['active' => true],
			]
		);

		$this->add_control(
			'slides',
			[
				'label'       => esc_html__('Slider Items', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'title' => esc_html__('Slide Item 1', 'bdthemes-element-pack'),
					],
					[
						'title' => esc_html__('Slide Item 2', 'bdthemes-element-pack'),
					],
					[
						'title' => esc_html__('Slide Item 3', 'bdthemes-element-pack'),
					],
					[
						'title' => esc_html__('Slide Item 4', 'bdthemes-element-pack'),
					],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->add_responsive_control(
			'slider_size',
			[
				'label'       => esc_html__('Slider Size', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min' => 180,
						'max' => 1200,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .bdt-device-slider-container' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'align',
			[
				'label'        => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'left'   => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'prefix_class' => 'bdt-device-slider-align-',
				'condition'    => [
					'slider_size!' => [''],
				],
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'   => esc_html__('Show Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_notch',
			[
				'label'   => esc_html__('Show Notch', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'     => 'device_type',
							'value'    => 'tablet',
						],
						[
							'name'     => 'device_type',
							'value'    => 'mobile',
						],
					],
				],
				'separator' => 'before',
				'prefix_class' => 'bdt-ds-notch--',
			]
		);

		$this->add_control(
			'show_buttons',
			[
				'label'   => esc_html__('Show Buttons', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'     => 'device_type',
							'value'    => 'tablet',
						],
						[
							'name'     => 'device_type',
							'value'    => 'mobile',
						],
					],
				],
				'prefix_class' => 'bdt-ds-buttons--',
			]
		);

		$this->add_control(
			'navigation',
			[
				'label'   => esc_html__('Navigation', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'arrows',
				'options' => [
					'arrows'      => esc_html__('Arrows', 'bdthemes-element-pack'),
					'dots'        => esc_html__('Dots', 'bdthemes-element-pack'),
					'arrows_dots' => esc_html__('Arrows and Dots', 'bdthemes-element-pack'),
					'none'        => esc_html__('None', 'bdthemes-element-pack'),
				],
				'separator' => 'before'
			]
		);

		$this->add_control(
			'nav_arrows_icon',
			[
				'label'   => esc_html__('Arrows Icon', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SELECT,
				'default' => '5',
				'options' => [
					'1' => esc_html__('Style 1', 'bdthemes-element-pack'),
					'2' => esc_html__('Style 2', 'bdthemes-element-pack'),
					'3' => esc_html__('Style 3', 'bdthemes-element-pack'),
					'4' => esc_html__('Style 4', 'bdthemes-element-pack'),
					'5' => esc_html__('Style 5', 'bdthemes-element-pack'),
					'6' => esc_html__('Style 6', 'bdthemes-element-pack'),
					'7' => esc_html__('Style 7', 'bdthemes-element-pack'),
					'8' => esc_html__('Style 8', 'bdthemes-element-pack'),
					'9' => esc_html__('Style 9', 'bdthemes-element-pack'),
					'10' => esc_html__('Style 10', 'bdthemes-element-pack'),
					'11' => esc_html__('Style 11', 'bdthemes-element-pack'),
					'12' => esc_html__('Style 12', 'bdthemes-element-pack'),
					'13' => esc_html__('Style 13', 'bdthemes-element-pack'),
					'14' => esc_html__('Style 14', 'bdthemes-element-pack'),
					'15' => esc_html__('Style 15', 'bdthemes-element-pack'),
					'16' => esc_html__('Style 16', 'bdthemes-element-pack'),
					'17' => esc_html__('Style 17', 'bdthemes-element-pack'),
					'18' => esc_html__('Style 18', 'bdthemes-element-pack'),
					'circle-1' => esc_html__('Style 19', 'bdthemes-element-pack'),
					'circle-2' => esc_html__('Style 20', 'bdthemes-element-pack'),
					'circle-3' => esc_html__('Style 21', 'bdthemes-element-pack'),
					'circle-4' => esc_html__('Style 22', 'bdthemes-element-pack'),
					'square-1' => esc_html__('Style 23', 'bdthemes-element-pack'),
				],
				'condition' => [
					'navigation' => ['arrows', 'arrows_dots'],
				],
			]
		);

		$this->add_control(
			'global_link',
			[
				'label'   => esc_html__('Item Link', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-ds-item-link--',
			]
		);



		$this->end_controls_section();

		$this->start_controls_section(
			'section_custom_device',
			[
				'label'     => esc_html__('Custom Device', 'bdthemes-element-pack') . BDTEP_NC,
				'condition' => [
					'device_type' => 'custom'
				],
			]
		);

		$this->add_control(
			'slider_size_ratio',
			[
				'label'       => esc_html__('Size Ratio', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::IMAGE_DIMENSIONS,
				'description' => 'Slider ratio to width and height, such as 600:1280',
			]
		);

		$this->add_control(
			'custom_device_buttons',
			[
				'label'   => esc_html__('B U T T O N S', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_left_button_1',
			[
				'label'   => esc_html__('Show Left Button 1', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'prefix_class' => 'bdt-ds-left-button-1--',
			]
		);

		$this->add_control(
			'show_left_button_2',
			[
				'label'   => esc_html__('Show Left Button 2', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'prefix_class' => 'bdt-ds-left-button-2--',
			]
		);

		$this->add_control(
			'show_left_button_3',
			[
				'label'   => esc_html__('Show Left Button 3', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'prefix_class' => 'bdt-ds-left-button-3--',
			]
		);

		$this->add_control(
			'show_right_button_1',
			[
				'label'   => esc_html__('Show Right Button 1', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'prefix_class' => 'bdt-ds-right-button-1--',
			]
		);

		$this->add_control(
			'show_right_button_2',
			[
				'label'   => esc_html__('Show Right Button 2', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'prefix_class' => 'bdt-ds-right-button-2--',
			]
		);

		$this->add_control(
			'custom_device_notch',
			[
				'label'   => esc_html__('N O T C H', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_custom_notch',
			[
				'label'   => esc_html__('Show notch', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'select_notch',
			[
				'label'   => esc_html__('Type', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'large-notch',
				'options' => [
					'large-notch' => esc_html__('Large Notch', 'bdthemes-element-pack'),
					'small-notch' => esc_html__('Small Notch', 'bdthemes-element-pack'),
					'drop-notch'  => esc_html__('Drop Notch', 'bdthemes-element-pack'),
				],
				'condition' => [
					'show_custom_notch' => 'yes'
				]
			]
		);

		$this->add_control(
			'custom_device_lens',
			[
				'label'   => esc_html__('L E N S', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_custom_notch' => ''
				]
			]
		);

		$this->add_control(
			'show_custom_lens',
			[
				'label'   => esc_html__('Show Lens', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'show_custom_notch' => ''
				]
			]
		);

		$this->add_responsive_control(
			'lens_size',
			[
				'label'   => esc_html__('Size', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-device-slider.bdt-device-slider-custom .phone-lens' => 'height: {{SIZE}}px; width: {{SIZE}}px;',
				],
				'condition' => [
					'show_custom_lens' => 'yes',
					'show_custom_notch' => ''
				]
			]
		);

		$this->add_responsive_control(
			'lens_horizontal',
			[
				'label'   => esc_html__('Horizontal Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 50
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-device-slider.bdt-device-slider-custom .phone-lens' => 'left: {{SIZE}}%;',
				],
				'condition' => [
					'show_custom_lens' => 'yes',
					'show_custom_notch' => ''
				]
			]
		);

		$this->add_responsive_control(
			'lens_vertical',
			[
				'label'   => esc_html__('Vertical Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 5
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-device-slider.bdt-device-slider-custom .phone-lens' => 'top: {{SIZE}}%;',
				],
				'condition' => [
					'show_custom_lens' => 'yes',
					'show_custom_notch' => ''
				]
			]
		);

		$this->add_control(
			'custom_device_bazel',
			[
				'label'   => esc_html__('B A Z E L', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'custom_device_border_width',
			[
				'label'      => __('Width', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'default' => [
					'top'      => '20',
					'right'    => '20',
					'bottom'   => '20',
					'left'     => '20',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-device-slider.bdt-device-slider-custom .bdt-slideshow-items' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .bdt-device-slider.bdt-device-slider-custom .phone-notch svg' => 'top: calc({{TOP}}{{UNIT}} - 1px);'
				],
			]
		);

		$this->add_responsive_control(
			'custom_device_border_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'default' => [
					'top'      => '40',
					'right'    => '40',
					'bottom'   => '40',
					'left'     => '40',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-device-slider.bdt-device-slider-custom .bdt-slideshow-items' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_layout',
			[
				'label'     => esc_html__('Title Layout', 'bdthemes-element-pack'),
				'condition' => [
					'show_title' => ['yes'],
				],
			]
		);

		$this->add_control(
			'content_position',
			[
				'label'   => esc_html__('Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'center',
				'options' => element_pack_position(),
			]
		);


		$this->add_responsive_control(
			'content_align',
			[
				'label'   => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left'    => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__('Justified', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-justify',
					],
				],
			]
		);

		$this->add_control(
			'title_tags',
			[
				'label'   => __('Title HTML Tag', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => element_pack_title_tags(),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_animation',
			[
				'label' => esc_html__('Animation', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'   => esc_html__('Autoplay', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'autoplay_interval',
			[
				'label'     => esc_html__('Autoplay Interval', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 7000,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label' => esc_html__('Pause on Hover', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'velocity',
			[
				'label' => __('Animation Speed', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0.1,
						'max'  => 1,
						'step' => 0.1,
					],
				],
			]
		);

		$this->add_control(
			'slider_animations',
			[
				'label'     => esc_html__('Slider Animations', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'separator' => 'before',
				'default'   => 'slide',
				'options'   => [
					'slide' => esc_html__('Slide', 'bdthemes-element-pack'),
					'fade'  => esc_html__('Fade', 'bdthemes-element-pack'),
					'scale' => esc_html__('Scale', 'bdthemes-element-pack'),
					'push'  => esc_html__('Push', 'bdthemes-element-pack'),
					'pull'  => esc_html__('Pull', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'kenburns_animation',
			[
				'label'     => esc_html__('Kenburns Animation', 'bdthemes-element-pack'),
				'separator' => 'before',
				'type'      => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'kenburns_reverse',
			[
				'label'     => esc_html__('Kenburn Reverse', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'kenburns_animation' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_style_device',
			[
				'label' => esc_html__('Device', 'bdthemes-element-pack') . BDTEP_NC,
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'device_type' => ['mobile', 'tablet', 'custom']
				],
			]
		);

		$this->add_control(
			'device_color_1',
			[
				'label'   => esc_html__('Color 1', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-device-slider svg .bdt-ds-color-1' => 'fill: {{VALUE}};'
				],
				'condition' => [
					'device_type!' => 'custom'
				],
			]
		);

		$this->add_control(
			'device_color_2',
			[
				'label'   => esc_html__('Color 2', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-device-slider svg .bdt-ds-color-2' => 'fill: {{VALUE}};'
				],
				'condition' => [
					'device_type!' => 'custom'
				],
			]
		);

		$this->add_control(
			'custom_device_border_color_1',
			[
				'label'   => esc_html__('Color 1', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::COLOR,
				'default' => '#343434',
				'selectors' => [
					'{{WRAPPER}} .bdt-device-slider.bdt-device-slider-custom .bdt-slideshow-items' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .bdt-device-slider.bdt-device-slider-custom .phone-notch svg .bdt-ds-color-1' => 'fill: {{VALUE}};'
				],
				'condition' => [
					'device_type' => 'custom'
				],
			]
		);

		$this->add_control(
			'custom_device_border_color_2',
			[
				'label'   => esc_html__('Color 2', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-device-slider.bdt-device-slider-custom .phone-notch svg .bdt-ds-color-2' => 'fill: {{VALUE}};'
				],
				'condition' => [
					'device_type' => 'custom'
				],
			]
		);

		$this->add_control(
			'device_buttons_color',
			[
				'label'   => esc_html__('Buttons Color', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-device-slider .bdt-ds-buttons .bdt-ds-color-1' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .bdt-device-slider-container:before, {{WRAPPER}} .bdt-device-slider-custom:after, {{WRAPPER}} .bdt-device-slider-custom:before, {{WRAPPER}} .bdt-device-slider-custom .bdt-slideshow:after, {{WRAPPER}} .bdt-device-slider-custom .bdt-slideshow:before' => 'background: {{VALUE}};'
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'or',
							'terms' => [
								[
									'name'     => 'device_type',
									'value'    => 'mobile'
								],
								[
									'name'     => 'device_type',
									'value'    => 'tablet'
								],
							],
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name'     => 'show_left_button_1',
									'value'    => 'yes'
								],
								[
									'name'     => 'show_left_button_2',
									'value'    => 'yes'
								],
								[
									'name'     => 'show_left_button_3',
									'value'    => 'yes'
								],
								[
									'name'     => 'show_right_button_1',
									'value'    => 'yes'
								],
								[
									'name'     => 'show_right_button_2',
									'value'    => 'yes'
								],
							]
						]
					]
				]
			]
		);

		$this->add_responsive_control(
			'buttons_width',
			[
				'label'     => esc_html__('Buttons Width', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-device-slider-container:before, {{WRAPPER}} .bdt-device-slider-custom:after, {{WRAPPER}} .bdt-device-slider-custom:before, {{WRAPPER}} .bdt-device-slider-custom .bdt-slideshow:after, {{WRAPPER}} .bdt-device-slider-custom .bdt-slideshow:before' => 'width: {{SIZE}}{{UNIT}};',
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name'     => 'device_type',
							'value'    => 'custom'
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name'     => 'show_left_button_1',
									'value'    => 'yes'
								],
								[
									'name'     => 'show_left_button_2',
									'value'    => 'yes'
								],
								[
									'name'     => 'show_left_button_3',
									'value'    => 'yes'
								],
								[
									'name'     => 'show_right_button_1',
									'value'    => 'yes'
								],
								[
									'name'     => 'show_right_button_2',
									'value'    => 'yes'
								],
							]
						]
					]
				]
			]
		);

		$this->add_responsive_control(
			'right_button_vertical',
			[
				'label'   => esc_html__('Right Button Y Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-device-slider-custom:after' => 'top: {{SIZE}}%;',
					'{{WRAPPER}} .bdt-device-slider-custom:before' => 'top: calc(9% + {{SIZE}}%);',
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name'     => 'device_type',
							'value'    => 'custom'
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name'     => 'show_right_button_1',
									'value'    => 'yes'
								],
								[
									'name'     => 'show_right_button_2',
									'value'    => 'yes'
								],
							]
						]
					]
				]
			]
		);

		$this->add_responsive_control(
			'left_button_vertical',
			[
				'label'   => esc_html__('Left Button Y Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-device-slider-container:before' => 'top: {{SIZE}}%;',
					'{{WRAPPER}} .bdt-device-slider-custom .bdt-slideshow:after' => 'top: calc(8% + {{SIZE}}%);',
					'{{WRAPPER}} .bdt-device-slider-custom .bdt-slideshow:before' => 'top: calc(18% + {{SIZE}}%);',
				],
				'condition' => [
					'device_type' => 'custom'
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name'     => 'device_type',
							'value'    => 'custom'
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name'     => 'show_left_button_1',
									'value'    => 'yes'
								],
								[
									'name'     => 'show_left_button_2',
									'value'    => 'yes'
								],
								[
									'name'     => 'show_left_button_3',
									'value'    => 'yes'
								],
							]
						]
					]
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_slider',
			[
				'label' => esc_html__('Slider', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'overlay',
			[
				'label'   => esc_html__('Overlay', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'       => esc_html__('None', 'bdthemes-element-pack'),
					'background' => esc_html__('Background', 'bdthemes-element-pack'),
					'blend'      => esc_html__('Blend', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'overlay_color',
			[
				'label'     => esc_html__('Overlay Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'overlay' => ['background', 'blend']
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-device-slider-container .bdt-overlay-default' => 'background-color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'blend_type',
			[
				'label'     => esc_html__('Blend Type', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'multiply',
				'options'   => element_pack_blend_options(),
				'condition' => [
					'overlay' => 'blend',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label'     => esc_html__('Title', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title' => ['yes'],
				],
			]
		);

		$this->add_control(
			'show_text_stroke',
			[
				'label'        => esc_html__('Text Stroke', 'bdthemes-element-pack') . BDTEP_NC,
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-text-stroke--',
			]
		);

		$this->add_responsive_control(
			'text_stroke_width',
			[
				'label' => esc_html__('Text Stroke Width', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-device-slider-container .bdt-slideshow-items .bdt-device-slider-title' => '-webkit-text-stroke-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_text_stroke' => 'yes'
				]
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-device-slider-container .bdt-slideshow-items .bdt-device-slider-title' => 'color: {{VALUE}}; -webkit-text-stroke-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-device-slider-container .bdt-slideshow-items .bdt-device-slider-title' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-device-slider-container .bdt-slideshow-items .bdt-device-slider-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_radius',
			[
				'label'      => esc_html__('Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-device-slider-container .bdt-slideshow-items .bdt-device-slider-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-device-slider-container .bdt-slideshow-items .bdt-device-slider-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_navigation',
			[
				'label'     => esc_html__('Navigation', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'navigation!' => 'none',
				],
			]
		);

		$this->add_control(
			'heading_arrows',
			[
				'label'     => esc_html__('Arrows', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
				'condition' => [
					'navigation' => ['arrows', 'arrows_dots'],
				],
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label'     => esc_html__('Arrows Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-device-slider-container .bdt-navigation-arrows' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => ['arrows', 'arrows_dots'],
				],
			]
		);

		$this->add_control(
			'arrows_hover_color',
			[
				'label'     => esc_html__('Arrows Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-device-slider-container .bdt-navigation-arrows:hover' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => ['arrows', 'arrows_dots'],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_spacing',
			[
				'label'       => esc_html__('Arrows Spacing', 'bdthemes-element-pack') . BDTEP_NC,
				'type'        => Controls_Manager::SLIDER,
				'selectors'   => [
					'{{WRAPPER}} .bdt-device-slider .bdt-navigation-arrows' => 'margin: 10px {{SIZE}}px;',
				],
				'condition' => [
					'navigation' => ['arrows', 'arrows_dots'],
				],
			]
		);

		$this->add_control(
			'heading_dots',
			[
				'label'     => esc_html__('Dots', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'navigation' => ['dots', 'arrows_dots'],
				],
			]
		);

		$this->add_control(
			'dots_color',
			[
				'label'     => esc_html__('Dots Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-device-slider-container .bdt-dotnav li a' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => ['dots', 'arrows_dots'],
				],
			]
		);

		$this->add_control(
			'active_dot_color',
			[
				'label'     => esc_html__('Active Dot Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-device-slider-container .bdt-dotnav li.bdt-active a' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => ['dots', 'arrows_dots'],
				],
			]
		);

		$this->add_control(
			'dots_size',
			[
				'label'     => esc_html__('Dots Size', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 5,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-device-slider-container .bdt-dotnav a' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation' => ['dots', 'arrows_dots'],
				],
			]
		);

		$this->add_control(
			'dots_spacing',
			[
				'label'     => esc_html__('Dots Spacing', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-device-slider .bdt-slideshow .bdt-dotnav-wrapper' => 'bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation' => ['dots', 'arrows_dots'],
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render_header() {
		$settings    = $this->get_settings_for_display();
		$device_type = $settings['device_type'];
		$ratio       = '1280:720';

		$custom_ratio = (isset($settings['slider_size_ratio']) && !empty($settings['slider_size_ratio']['width']) && !empty($settings['slider_size_ratio']['height'])) ? $settings['slider_size_ratio']['width'] . ":" . $settings['slider_size_ratio']['height'] : '600:1280';

		if ('desktop' === $device_type) {
			$ratio = '1280:720';
		} elseif ('safari' === $device_type) {
			$ratio = '1400:727';
		} elseif ('chrome' === $device_type) {
			$ratio = '1400:788';
		} elseif ('chrome-dark' === $device_type) {
			$ratio = '1400:788';
		} elseif ('firefox' === $device_type) {
			$ratio = '1280:651';
		} elseif ('edge' === $device_type) {
			$ratio = '1280:651';
		} elseif ('edge-dark' === $device_type) {
			$ratio = '1280:651';
		} elseif ('macbookpro' === $device_type) {
			$ratio = '1280:815';
		} elseif ('macbookair' === $device_type) {
			$ratio = '1280:810';
		} elseif ('tablet' === $device_type and $settings['rotation_state'] == '') {
			$ratio = '768:1024';
		} elseif ('tablet' === $device_type and $settings['rotation_state'] == 'yes') {
			$ratio = '1024:768';
		} elseif ('iphonex' === $device_type) {
			$ratio = '600:1280';
		} elseif ('mobile' === $device_type and $settings['rotation_state'] == '') {
			$ratio = '600:1287';
		} elseif ('mobile' === $device_type and $settings['rotation_state'] == 'yes') {
			$ratio = '1287:600';
		} elseif ('imac' === $device_type) {
			$ratio = '1280:720';
		} elseif ('custom' === $device_type) {
			$ratio = $custom_ratio;
		}

		$this->add_render_attribute(
			[
				'slider_settings' => [
					'data-bdt-slideshow' => [
						wp_json_encode(array_filter([
							"animation"         => $settings["slider_animations"],
							"ratio"             => $ratio,
							"autoplay"          => ("yes" === $settings["autoplay"]) ? true : false,
							"autoplay-interval" => $settings["autoplay_interval"],
							"pause-on-hover"    => ("yes" === $settings["pause_on_hover"]) ? true : false,
							"velocity"          => ($settings["velocity"]["size"]) ? $settings["velocity"]["size"] : 1,
						])),
					],
				],
			]
		);

		$rotation_state = ('yes' == $settings['rotation_state']) ? '-hr' : '';

?>
		<div class="bdt-device-slider-container">
			<div class="bdt-device-slider bdt-device-slider-<?php echo esc_attr($device_type) . esc_attr($rotation_state); ?>">
				<div <?php echo $this->get_render_attribute_string('slider_settings'); ?>>
					<div class="bdt-position-relative bdt-visible-toggle">
						<ul class="bdt-slideshow-items">
						<?php
					}

					protected function render_footer() {
						$settings    = $this->get_settings_for_display();
						$device_type = $settings['device_type'];
						$rotation_state = ('yes' == $settings['rotation_state']) ? '-hr' : '';
						$svg_uri = BDTEP_ASSETS_PATH . 'images/devices/' . $device_type . $rotation_state . '.svg';
						$svg_url = BDTEP_ASSETS_URL . 'images/devices/' . $device_type . $rotation_state . '.svg';

						$notch_type = $settings['select_notch'];
						$notch_svg_uri = BDTEP_ASSETS_PATH . 'images/devices/' . $notch_type . '.svg';

						?>
						</ul>
						<?php if ('arrows' == $settings['navigation'] or 'arrows_dots' == $settings['navigation']) : ?>
							<a class="bdt-navigation-arrows bdt-position-center-left bdt-position-small bdt-hidden-hover" href="#" data-bdt-slideshow-item="previous">
								<i class="ep-icon-arrow-left-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
							</a>
							<a class="bdt-navigation-arrows bdt-position-center-right bdt-position-small bdt-hidden-hover" href="#" data-bdt-slideshow-item="next">
								<i class="ep-icon-arrow-right-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
							</a>
						<?php endif; ?>


						<?php if ('dots' == $settings['navigation'] or 'arrows_dots' == $settings['navigation']) : ?>
							<div class="bdt-dotnav-wrapper">
								<ul class="bdt-dotnav bdt-flex-center">

									<?php
									$bdt_counter    = 0;
									$slideshow_dots = $settings['slides'];

									foreach ($slideshow_dots as $dot) :

										echo '<li class="bdt-slideshow-dotnav bdt-active" bdt-slideshow-item="' . $bdt_counter . '"><a href="#"></a></li>';
										$bdt_counter++;

									endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<div class="bdt-device-slider-device">
					<?php if ($settings['device_type'] !== 'custom') : ?>
						<?php if ($settings['device_type'] == 'mobile' or $settings['device_type'] == 'tablet') : ?>
							<?php echo element_pack_load_svg($svg_uri); ?>
						<?php else : ?>
							<img src="<?php echo esc_url($svg_url)  ?>" alt="Device Slider">
						<?php endif; ?>
					<?php endif; ?>

					<?php if ($settings['device_type'] == 'custom' and 'yes' == $settings['show_custom_lens']) : ?>
						<img class="phone-lens" src="<?php echo BDTEP_ASSETS_URL; ?>images/devices/phone-lens.svg" alt="Device Slider">
					<?php endif; ?>

					<?php if ($settings['device_type'] == 'custom' and 'yes' == $settings['show_custom_notch']) : ?>
						<span class="phone-notch">
							<?php echo element_pack_load_svg($notch_svg_uri); ?>
						</span>
					<?php endif; ?>

				</div>
			</div>
		</div>
	<?php
					}

					protected function rendar_item_image($image, $alt = '') {
						$image_src = wp_get_attachment_image_src($image['image']['id'], 'full');

						if ($image_src) :
							echo '<img src="' . esc_url($image_src[0]) . '" alt=" ' . esc_html($alt) . '" bdt-cover>';
						endif;
					}

					protected function rendar_item_video($link) {
						$video_src = $link['video_link'];

	?>
		<video autoplay loop muted playsinline bdt-cover>
			<source src="<?php echo esc_url($video_src); ?>" type="video/mp4">
		</video>
	<?php
					}

					protected function rendar_item_youtube($link) {

						$id  = (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $link['youtube_link'], $match)) ? $match[1] : false;
						$url = '//www.youtube.com/embed/' . $id . '?autoplay=1&mute=1&amp;controls=0&amp;showinfo=0&amp;rel=0&amp;loop=1&amp;modestbranding=1&amp;wmode=transparent&amp;playsinline=1&playlist=' . $id;

	?>
		<iframe src="<?php echo esc_url($url); ?>" allowfullscreen bdt-cover></iframe>
	<?php
					}

					protected function rendar_item_content($content) {
						$settings = $this->get_settings_for_display();

	?>
		<div class="bdt-slideshow-content-wrapper bdt-position-z-index bdt-position-<?php echo esc_attr($settings['content_position']); ?> bdt-position-large bdt-text-<?php echo esc_attr($settings['content_align']); ?>">

			<?php if ($content['title'] && ('yes' == $settings['show_title'])) : ?>
				<div>
					<<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?> class="bdt-device-slider-title
                bdt-display-inline-block" data-bdt-slideshow-parallax="x:300, -300">
						<?php if ('' !== $content['title_link']['url']) : ?>
							<a href="<?php echo esc_url($content['title_link']['url']); ?>">
							<?php endif; ?>
							<?php echo wp_kses_post($content['title']); ?>
							<?php if ('' !== $content['title_link']['url']) : ?>
							</a>
						<?php endif; ?>
					</<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?>>
				</div>
			<?php endif; ?>

		</div>
		<?php
					}

					public function render() {
						$settings         = $this->get_settings_for_display();
						$kenburns_reverse = $settings['kenburns_reverse'] ? ' bdt-animation-reverse' : '';

						$this->render_header();

						foreach ($settings['slides'] as $slide) :
							if ('yes' == $settings['global_link']) {
								$this->add_render_attribute('global-link', 'onclick', "window.open('" . esc_url($slide['title_link']['url']) . "', '_self')", true);
							}

		?>

			<li class="bdt-slideshow-item elementor-repeater-item-<?php echo esc_attr($slide['_id']); ?>" <?php echo ($settings['global_link'] == 'yes') ? $this->get_render_attribute_string('global-link') : ''; ?>>
				<?php if ('yes' == $settings['kenburns_animation']) : ?>
					<div class="bdt-position-cover bdt-animation-kenburns<?php echo esc_attr($kenburns_reverse); ?> bdt-transform-origin-center-left">
					<?php endif; ?>

					<?php if (($slide['background'] == 'image') && $slide['image']) : ?>



						<?php $this->rendar_item_image($slide, $slide['title']); ?>



					<?php elseif (($slide['background'] == 'video') && $slide['video_link']) : ?>
						<?php $this->rendar_item_video($slide); ?>
					<?php elseif (($slide['background'] == 'youtube') && $slide['youtube_link']) : ?>
						<?php $this->rendar_item_youtube($slide); ?>
					<?php endif; ?>

					<?php if ('yes' == $settings['kenburns_animation']) : ?>
					</div>
				<?php endif; ?>

				<?php if ('none' !== $settings['overlay']) :
								$blend_type = ('blend' == $settings['overlay']) ? ' bdt-blend-' . $settings['blend_type'] : ''; ?>
					<div class="bdt-overlay-default bdt-position-cover<?php echo esc_attr($blend_type); ?>"></div>
				<?php endif; ?>

				<?php $this->rendar_item_content($slide); ?>
			</li>

<?php endforeach;

						$this->render_footer();
					}
				}
