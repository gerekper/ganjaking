<?php
namespace ElementPack\Modules\Iframe\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Iframe extends Module_Base {

	public function get_name() {
		return 'bdt-iframe';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Iframe', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-iframe';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'iframe', 'embed' ];
	}

	public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return [ 'ep-iframe' ];
        }
    }

	public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return [ 'recliner', 'ep-scripts' ];
        } else {
			return [ 'recliner', 'ep-iframe' ];
        }
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/wQPgsmrxZHM';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__( 'Layout', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'source',
			[
				'label'         => esc_html__( 'Content Source', 'bdthemes-element-pack' ),
				'type'          => Controls_Manager::URL,
				'dynamic'       => [ 'active' => true ],
				'default'       => [ 'url' => 'https://example.com' ],
				'placeholder'   => esc_html__( 'https://example.com', 'bdthemes-element-pack' ),
				'description'   => esc_html__( 'You can put here any website url, youtube, vimeo, document or image embed url.( But please make sure about your link. If your website have SSL Certificate, please use SSL Certified Link here. Otherwise, Iframe will not work. )', 'bdthemes-element-pack' ),
				'label_block'   => true,
				'show_external' => false,
			]
		);

		$this->add_control(
			'auto_height',
			[
				'label'   => esc_html__( 'Auto Height', 'bdthemes-element-pack' ),
				'description'   => esc_html__( 'Auto height only works when cross domain with "allow origin all in header".'  , 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => [
					'show_iframe_device' => ''
				]
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label'     => esc_html__( 'Iframe Height', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'   => 100,
						'max'   => 1500,
						'step' => 10,
					],
					'vw' => [
						'min'   => 1,
						'max'   => 100,
					],
					'%' => [
						'min'   => 1,
						'max'   => 100,
					],
				],
				'size_units' => [ 'px', 'vh' ],
				'default' => [
					'size' => 640,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-iframe iframe' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition'   => [
					'auto_height!' => 'yes',
					'show_responsive_ratio!' => 'yes',
					'show_iframe_device' => ''
				],
			]
		);

		$this->add_responsive_control(
			'iframe_size',
			[
				'label'       => esc_html__( 'Iframe Container Width', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min' => 180,
						'max' => 1200,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .bdt-device-container' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'render_type' => 'template',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_responsive_ratio',
			[
				'label'   => esc_html__( 'Responsive Ratio', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition'   => [
					'auto_height!' => 'yes',
					'show_iframe_device' => ''
				],
			]
		);

		$this->add_control(
			'responsive_ratio_size',
			[
				'label'       => esc_html__('Size Ratio', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::IMAGE_DIMENSIONS,
				'description' => esc_html__( 'Iframe ratio to width and height, such as 600/1280', 'bdthemes-element-pack' ),
				'condition'   => [
					'show_responsive_ratio' => 'yes',
					'auto_height!' => 'yes',
					'show_iframe_device' => ''
				],
				'default' => [
					'width' => 1280,
					'height' => 720,
				]
			]
		);
		
		$this->add_control(
			'align',
			[
				'label'        => esc_html__( 'Alignment', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'left'   => [
						'title' => esc_html__( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'prefix_class' => 'bdt-iframe-align-',
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_iframe_settings',
			[
				'label' => esc_html__( 'Lazyload Settings', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'lazyload',
			[
				'label'   => esc_html__( 'Lazyload', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes'
			]
		);

		$this->add_control(
			'throttle',
			[
				'label'       => esc_html__('Throttle', 'bdthemes-element-pack'),
				'description' => esc_html__('millisecond interval at which to process events', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 300,
				'condition'   => [
					'lazyload' => 'yes',
				],
			]
		);

		$this->add_control(
			'threshold',
			[
				'label'       => esc_html__('Threshold', 'bdthemes-element-pack'),
				'description' => esc_html__('scroll distance from element before its loaded', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::NUMBER,
				'separator'   => 'before',
				'default'     => 100,
				'condition'   => [
					'lazyload' => 'yes',
				],
			]
		);

		$this->add_control(
			'live',
			[
				'label'       => esc_html__( 'Live', 'bdthemes-element-pack' ),
				'description' => esc_html__('auto bind lazy loading to ajax loaded elements', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
				'separator'   => 'before',
				'default'     => 'yes',
				'condition'   => [
					'lazyload' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_additional',
			[
				'label' => esc_html__( 'Additional Settings', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'allowfullscreen',
			[
				'label'       => esc_html__( 'Allow Fullscreen', 'bdthemes-element-pack' ),
				'description' => esc_html__('Maybe you need this when you use youtube or video embed link.', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes'
			]
		);

		$this->add_control(
			'scrolling',
			[
				'label'       => esc_html__( 'Show Scroll Bar', 'bdthemes-element-pack' ),
				'description' => esc_html__('Specifies whether or not to display scrollbars', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sandbox',
			[
				'label'       => esc_html__( 'Sandbox', 'bdthemes-element-pack' ),
				'description' => esc_html__('Enables an extra set of restrictions for the content', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'sandbox_allowed_attributes',
			[
				'label'       => esc_html__('Sandbox Allowed Attributes', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options'     => [
                    'allow-forms'                             => esc_html__('Forms', 'bdthemes-element-pack'),
                    'allow-modals'                            => esc_html__('Modals', 'bdthemes-element-pack'),
                    'allow-orientation-lock'                  => esc_html__('Orientation Lock', 'bdthemes-element-pack'),
                    'allow-pointer-lock'                      => esc_html__('Pointer Lock', 'bdthemes-element-pack'),
                    'allow-popups'                            => esc_html__('Popups', 'bdthemes-element-pack'),
                    'allow-popups-to-escape-sandbox'          => esc_html__('Popups to Escape Sandbox', 'bdthemes-element-pack'),
                    'allow-presentation'                      => esc_html__('Presentation', 'bdthemes-element-pack'),
                    'allow-same-origin'                       => esc_html__('Same Origin', 'bdthemes-element-pack'),
                    'allow-scripts'                           => esc_html__('Scripts', 'bdthemes-element-pack'),
                    'allow-top-navigation'                    => esc_html__('Top Navigation', 'bdthemes-element-pack'),
                    'allow-top-navigation-by-user-activation' => esc_html__('Top Navigation by User', 'bdthemes-element-pack'),
				],
				'condition' => [
					'sandbox' => 'yes'
				]
			]
		);

		$this->add_control(
			'custom_attributes',
			[
				'label' => __( 'Custom Attributes', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'key|value', 'bdthemes-element-pack' ),
				'description' => sprintf( __( 'Set custom attributes for the iframe tag. Each attribute in a separate line. Separate attribute key from the value using %s character.', 'bdthemes-element-pack' ), '<code>|</code>' ),
				'classes' => 'elementor-control-direction-ltr',
			]
		);

		//allowvr="yes" allow="vr; xr; accelerometer; magnetometer; gyroscope; autoplay

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_device',
			[
				'label' => esc_html__( 'Iframe Device', 'bdthemes-element-pack' ) . BDTEP_NC,
			]
		);

		$this->add_control(
			'show_iframe_device',
			[
				'label' => esc_html__( 'Show Iframe Device', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'device_type',
			[
				'label'   => esc_html__( 'Select Device', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'imac',
				'options' => [
					'chrome'      => esc_html__( 'Chrome', 'bdthemes-element-pack' ),
					'chrome-dark' => esc_html__( 'Chrome Dark', 'bdthemes-element-pack' ),
					'imac'        => esc_html__( 'Desktop', 'bdthemes-element-pack' ),
					'edge'        => esc_html__( 'Edge', 'bdthemes-element-pack' ),
					'edge-dark'   => esc_html__( 'Edge Dark', 'bdthemes-element-pack' ),
					'firefox'     => esc_html__( 'Firefox', 'bdthemes-element-pack' ),
					'mobile'      => esc_html__( 'Mobile', 'bdthemes-element-pack' ),
					'safari'      => esc_html__( 'Safari', 'bdthemes-element-pack' ),
					'tablet'      => esc_html__( 'Tablet', 'bdthemes-element-pack' ),
					'custom'      => esc_html__( 'Custom', 'bdthemes-element-pack' ),
				],
				'condition' => [
					'show_iframe_device' => 'yes'
				]
			]
		);

		$this->add_control(
			'rotation_state',
			[
				'label'   => esc_html__( 'Horizontal Rotation State', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'conditions'   => [
					'relation' => 'and',
					'terms' => [
						[
							'name'     => 'show_iframe_device',
							'value'    => 'yes',
						],
						[
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
					],
				],
			]
		);

		$this->add_control(
			'show_notch',
			[
				'label'   => esc_html__( 'Show Notch', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'conditions'   => [
					'relation' => 'and',
					'terms' => [
						[
							'name'     => 'show_iframe_device',
							'value'    => 'yes',
						],
						[
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
					],
				],
				'prefix_class' => 'bdt-ds-notch--',
			]
		);

		$this->add_control(
			'show_buttons',
			[
				'label'   => esc_html__( 'Show Buttons', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'conditions'   => [
					'relation' => 'and',
					'terms' => [
						[
							'name'     => 'show_iframe_device',
							'value'    => 'yes',
						],
						[
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
					],
				],
				'prefix_class' => 'bdt-ds-buttons--',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_custom_device',
			[
				'label'     => esc_html__( 'Custom Device', 'bdthemes-element-pack' ) . BDTEP_NC,
				'condition' => [
					'show_iframe_device' => 'yes',
					'device_type' => 'custom'
				],
			]
		);

		$this->add_control(
			'slider_size_ratio',
			[
				'label'       => esc_html__('Size Ratio', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::IMAGE_DIMENSIONS,
				'description' => esc_html__( 'Iframe ratio to width and height, such as 600/1280', 'bdthemes-element-pack' ),
				'default' => [
					'width' => 600,
					'height' => 1200,
				]
			]
		);

		$this->add_control(
			'custom_device_buttons',
			[
				'label'   => esc_html__( 'BUTTONS', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_left_button_1',
			[
				'label'   => esc_html__( 'Show Left Button 1', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'prefix_class' => 'bdt-ds-left-button-1--',
			]
		);

		$this->add_control(
			'show_left_button_2',
			[
				'label'   => esc_html__( 'Show Left Button 2', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'prefix_class' => 'bdt-ds-left-button-2--',
			]
		);

		$this->add_control(
			'show_left_button_3',
			[
				'label'   => esc_html__( 'Show Left Button 3', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'prefix_class' => 'bdt-ds-left-button-3--',
			]
		);

		$this->add_control(
			'show_right_button_1',
			[
				'label'   => esc_html__( 'Show Right Button 1', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'prefix_class' => 'bdt-ds-right-button-1--',
			]
		);

		$this->add_control(
			'show_right_button_2',
			[
				'label'   => esc_html__( 'Show Right Button 2', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'prefix_class' => 'bdt-ds-right-button-2--',
			]
		);

		$this->add_control(
			'custom_device_notch',
			[
				'label'   => esc_html__( 'NOTCH', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_custom_notch',
			[
				'label'   => esc_html__( 'Show notch', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'select_notch',
			[
				'label'   => esc_html__( 'Type', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'large-notch',
				'options' => [
					'large-notch' => esc_html__( 'Large Notch', 'bdthemes-element-pack' ),
					'small-notch' => esc_html__( 'Small Notch', 'bdthemes-element-pack' ),
					'drop-notch'  => esc_html__( 'Drop Notch', 'bdthemes-element-pack' ),
				],
				'condition' => [
					'show_custom_notch' => 'yes'
				]
			]
		);

		$this->add_control(
			'custom_device_lens',
			[
				'label'   => esc_html__( 'LENS', 'bdthemes-element-pack' ),
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
				'label'   => esc_html__( 'Show Lens', 'bdthemes-element-pack' ),
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
				'label'   => esc_html__( 'Size', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-iframe.bdt-device-custom .phone-lens' => 'height: {{SIZE}}px; width: {{SIZE}}px;',
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
				'label'   => esc_html__( 'Horizontal Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 50
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-iframe.bdt-device-custom .phone-lens' => 'left: {{SIZE}}%;',
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
				'label'   => esc_html__( 'Vertical Offset', 'bdthemes-element-pack' ),
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
					'{{WRAPPER}} .bdt-iframe.bdt-device-custom .phone-lens' => 'top: {{SIZE}}%;',
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
				'label'   => esc_html__( 'BAZEL', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'custom_device_border_width',
			[
				'label'      => __( 'Width', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default' => [
					'top'      => '20',
					'right'    => '20',
					'bottom'   => '20',
					'left'     => '20',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-iframe.bdt-device-custom iframe' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .bdt-iframe.bdt-device-custom .phone-notch svg' => 'top: calc({{TOP}}{{UNIT}} - 1px);'
				],
			]
		);

		$this->add_responsive_control(
			'custom_device_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'      => '40',
					'right'    => '40',
					'bottom'   => '40',
					'left'     => '40',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-iframe.bdt-device-custom iframe' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_style_device',
			[
				'label' => esc_html__( 'Device', 'bdthemes-element-pack' ) . BDTEP_NC,
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_iframe_device' => 'yes',
					'device_type' => ['mobile', 'tablet', 'custom']
				],
			]
		);
		
		$this->add_control(
			'device_color_1',
			[
				'label'   => esc_html__( 'Color 1', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-iframe svg .bdt-ds-color-1' => 'fill: {{VALUE}};'
				],
				'condition' => [
					'device_type!' => 'custom'
				],
			]
		);
		
		$this->add_control(
			'device_color_2',
			[
				'label'   => esc_html__( 'Color 2', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-iframe svg .bdt-ds-color-2' => 'fill: {{VALUE}};'
				],
				'condition' => [
					'device_type!' => 'custom'
				],
			]
		);

		$this->add_control(
			'custom_device_border_color_1',
			[
				'label'   => esc_html__( 'Color 1', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#343434',
				'selectors' => [
					'{{WRAPPER}} .bdt-iframe.bdt-device-custom iframe' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .bdt-iframe.bdt-device-custom .phone-notch svg .bdt-ds-color-1' => 'fill: {{VALUE}};'
				],
				'condition' => [
					'device_type' => 'custom'
				],
			]
		);

		$this->add_control(
			'custom_device_border_color_2',
			[
				'label'   => esc_html__( 'Color 2', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-iframe.bdt-device-custom .phone-notch svg .bdt-ds-color-2' => 'fill: {{VALUE}};'
				],
				'condition' => [
					'device_type' => 'custom'
				],
			]
		);
		
		$this->add_control(
			'device_buttons_color',
			[
				'label'   => esc_html__( 'Buttons Color', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-iframe .bdt-ds-buttons .bdt-ds-color-1' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .bdt-device-container:before, {{WRAPPER}} .bdt-device-custom:after, {{WRAPPER}} .bdt-device-custom:before, {{WRAPPER}} .bdt-device-custom .bdt-iframe-device:after, {{WRAPPER}} .bdt-device-custom .bdt-iframe-device:before' => 'background: {{VALUE}};'
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
				'label'     => esc_html__( 'Buttons Width', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-device-container:before, {{WRAPPER}} .bdt-device-custom:after, {{WRAPPER}} .bdt-device-custom:before, {{WRAPPER}} .bdt-device-custom .bdt-iframe-device:after, {{WRAPPER}} .bdt-device-custom .bdt-iframe-device:before' => 'width: {{SIZE}}{{UNIT}};',
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
				'label'   => esc_html__( 'Right Button Y Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-device-custom:after' => 'top: {{SIZE}}%;',
					'{{WRAPPER}} .bdt-device-custom:before' => 'top: calc(9% + {{SIZE}}%);',
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
				'label'   => esc_html__( 'Left Button Y Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-device-container:before' => 'top: {{SIZE}}%;',
					'{{WRAPPER}} .bdt-device-custom .bdt-iframe-device:after' => 'top: calc(8% + {{SIZE}}%);',
					'{{WRAPPER}} .bdt-device-custom .bdt-iframe-device:before' => 'top: calc(18% + {{SIZE}}%);',
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

	}

	protected function render_device() {
		$settings    = $this->get_settings_for_display();

		if ( ! $this->get_settings( 'show_iframe_device' ) ) {
			return;
		}

		$device_type = $settings['device_type'];
		$rotation_state = ('yes' == $settings['rotation_state']) ? '-hr' : '';
		$svg_uri = BDTEP_ASSETS_PATH . 'images/devices/' . $device_type . $rotation_state . '.svg';
		$svg_url = BDTEP_ASSETS_URL . 'images/devices/' . $device_type . $rotation_state . '.svg';
		
		$notch_type = $settings['select_notch'];
		$notch_svg_uri = BDTEP_ASSETS_PATH . 'images/devices/' . $notch_type . '.svg';

		?>
		<div class="bdt-iframe-device">

			<?php if ($settings['device_type'] !== 'custom') : ?>
				<?php if ($settings['device_type'] == 'mobile' or $settings['device_type'] == 'tablet') : ?>
					<?php echo element_pack_load_svg( $svg_uri ); ?>
				<?php else : ?>
					<img src="<?php echo esc_url($svg_url)  ?>" alt="Device Slider">
				<?php endif; ?>
			<?php endif; ?>

			<?php if ($settings['device_type'] == 'custom' and 'yes' == $settings['show_custom_lens']) : ?>
			<img class="phone-lens" src="<?php echo BDTEP_ASSETS_URL; ?>images/devices/phone-lens.svg" alt="Device Slider">
			<?php endif; ?>

			<?php if ($settings['device_type'] == 'custom' and 'yes' == $settings['show_custom_notch']) : ?>
			<span class="phone-notch">
				<?php echo element_pack_load_svg( $notch_svg_uri ); ?>
			</span>
			<?php endif; ?>

		</div>
		<?php
	}

	public function render() {
		$settings = $this->get_settings_for_display();

		$device_type = $settings['device_type'];
		
		if ( 'imac' === $device_type ) {
			$this->add_render_attribute( 'iframe', [ 'width' => 2560, 'height' => 1440 ] );
		} elseif ( 'safari' === $device_type ) {
			$this->add_render_attribute( 'iframe', [ 'width' => 2800, 'height' => 1454 ] );
		} elseif ( 'chrome' === $device_type ) {
			$this->add_render_attribute( 'iframe', [ 'width' => 2800, 'height' => 1576 ] );
		} elseif ( 'chrome-dark' === $device_type ) {
			$this->add_render_attribute( 'iframe', [ 'width' => 2800, 'height' => 1576 ] );
		} elseif ( 'firefox' === $device_type ) {
			$this->add_render_attribute( 'iframe', [ 'width' => 2560, 'height' => 1302 ] );
		} elseif ( 'edge' === $device_type ) {
			$this->add_render_attribute( 'iframe', [ 'width' => 2580, 'height' => 1302 ] );
		} elseif ( 'edge-dark' === $device_type ) {
			$this->add_render_attribute( 'iframe', [ 'width' => 2580, 'height' => 1302 ] );
		} elseif ( 'tablet' === $device_type and $settings['rotation_state'] == '' ) {
			$this->add_render_attribute( 'iframe', [ 'width' => 1536, 'height' => 2048 ] );
		} elseif ( 'tablet' === $device_type and $settings['rotation_state'] == 'yes' ) {
			$this->add_render_attribute( 'iframe', [ 'width' => 2048, 'height' => 1536 ] );
		} elseif ( 'mobile' === $device_type and $settings['rotation_state'] == '' ) {
			$this->add_render_attribute( 'iframe', [ 'width' => 1200, 'height' => 2574 ] );
		} elseif ( 'mobile' === $device_type and $settings['rotation_state'] == 'yes' ) {
			$this->add_render_attribute( 'iframe', [ 'width' => 2574, 'height' => 1200 ] );
		} elseif ( 'custom' === $device_type ) {
			$this->add_render_attribute( 'iframe', [ 'width' => $settings['slider_size_ratio']['width'], 'height' => $settings['slider_size_ratio']['height'] ] );
		}

		$rotation_state = ('yes' == $settings['rotation_state']) ? '-hr' : '';

		$this->add_render_attribute( 'iframe-container', 'class', 'bdt-iframe bdt-device-'.$device_type . $rotation_state );

		if ('yes' == $settings['lazyload']) {
			$this->add_render_attribute( 'iframe', 'class', 'bdt-lazyload' );
			$this->add_render_attribute( 'iframe', 'data-throttle', esc_attr($settings['throttle']) );
			$this->add_render_attribute( 'iframe', 'data-threshold', esc_attr($settings['threshold']) );
			$this->add_render_attribute( 'iframe', 'data-live', $settings['live'] ? 'true' : 'false' );
			$this->add_render_attribute( 'iframe', 'data-src', esc_url( do_shortcode( $settings['source']['url']) ) );
		} else {
			$this->add_render_attribute( 'iframe', 'src', esc_url( do_shortcode( $settings['source']['url'] ) ) );
		}

		if (! $settings['scrolling']) {
			$this->add_render_attribute( 'iframe', 'scrolling', 'no' );
		}

		if($settings['show_iframe_device']) {
			$this->add_render_attribute( 'iframe', 'bdt-responsive' );
		} elseif ($settings['show_responsive_ratio']) {
			$this->add_render_attribute( 'iframe', 'bdt-responsive' );
			$this->add_render_attribute( 'iframe', [ 'width' => $settings['responsive_ratio_size']['width'], 'height' => $settings['responsive_ratio_size']['height'] ] );
		} else {
			$this->add_render_attribute( 'iframe', 'data-auto_height', ($settings['auto_height']) ? 'true' : 'false' );
		}

		
		if ('yes' == $settings['allowfullscreen']) {
			$this->add_render_attribute( 'iframe', 'allowfullscreen' );
		} else {
			$this->add_render_attribute( 'iframe', 'donotallowfullscreen' );
		}

		if ($settings['sandbox']) {
			$this->add_render_attribute( 'iframe', 'sandbox' );

			if ($settings['sandbox_allowed_attributes']) {
				$this->add_render_attribute( 'iframe', 'sandbox', $settings['sandbox_allowed_attributes'] );
			}
		}

		if ( ! empty( $settings['custom_attributes'] ) ) {
			$attributes = explode( "\n", $settings['custom_attributes'] );

			$reserved_attr = [ 'class', 'onload', 'onclick', 'onfocus', 'onblur', 'onchange', 'onresize', 'onmouseover', 'onmouseout', 'onkeydown', 'onkeyup', 'onerror', 'sandbox', 'allowfullscreen', 'donotallowfullscreen', 'scrolling', 'data-throttle', 'data-threshold', 'data-live', 'data-src' ];

			foreach ( $attributes as $attribute ) {
				if ( ! empty( $attribute ) ) {
					$attr = explode( '|', $attribute, 2 );
					if ( ! isset( $attr[1] ) ) {
						$attr[1] = '';
					}

					if ( ! in_array( strtolower( $attr[0] ), $reserved_attr ) ) {
						$this->add_render_attribute( 'iframe', trim( $attr[0] ), trim( $attr[1] ) );
					}
				}
			}
		}

		?>
		<div class="bdt-device-container">
			<div <?php echo $this->get_render_attribute_string('iframe-container'); ?>>
				<iframe <?php echo $this->get_render_attribute_string('iframe'); ?>></iframe>
				<?php $this->render_device(); ?>
			</div>
		</div>
		<?php
	}
}
