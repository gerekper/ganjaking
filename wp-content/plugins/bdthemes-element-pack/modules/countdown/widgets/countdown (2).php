<?php
namespace ElementPack\Modules\Countdown\Widgets;

use DateTime;
use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use ElementPack\Utils;

use ElementPack\Modules\Countdown\Skins;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Countdown extends Module_Base {

	public function get_name() {
		return 'bdt-countdown';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Countdown', 'bdthemes-element-pack' );
	}
 
	public function get_icon() {
		return 'bdt-wi-countdown';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'countdown', 'timer', 'schedule' ];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return [ 'ep-countdown' ];
		}
	}

	public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-scripts'];
        } else {
			return [ 'ep-countdown' ];
        }
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/HtsshsQxqEA';
	}

	protected function register_skins() {
		if (class_exists('Tribe__Events__Main')) {
			$this->add_skin( new Skins\Skin_Event_Countdown( $this ) );
		}
		$this->add_skin( new Skins\Skin_Tiny_Countdown( $this ) );
	}


	protected function register_controls() {
		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__( 'Layout', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'due_date',
			[
				'label'       => esc_html__( 'Due Date', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::DATE_TIME,
				'default'     => date( 'Y-m-d H:i', strtotime( '+1 month' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ),
				'description' => sprintf( __( 'Date set according to your timezone: %s.', 'bdthemes-element-pack' ), Utils::get_timezone_string() ),
				// 'condition'   => [
				// 	'_skin!' => 'bdt-event-countdown',
				// 	// 'loop_time!' => 'yes',
				// ],
				'conditions' => [
                    //'relation' => 'or',
                    'terms'    => [
                        [
                            'name'     => '_skin',
                            'operator' => '!=',
                            'value'    => 'bdt-event-countdown'
                        ],
                        [
                            'name'     => 'loop_time',
                            'operator' => '==',
                            'value'    => ''
                        ],
                    ]
                ]
				 
			]
		);

		$this->add_control(
			'loop_time',
			[
				'label'   => esc_html__( 'Loop Time', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'condition'   => [
					'_skin!' => 'bdt-event-countdown', 
				],
			]
		);

		$this->add_control(
			'loop_hours',
			[
				'label'          => esc_html__( 'Input Loop Time (Hours)', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'           => Controls_Manager::NUMBER,
				'default'        => '3',
				'conditions' => [
                    'relation' => 'and',
                    'terms'    => [
                        [
                            'name'     => '_skin',
                            'operator' => '!=',
                            'value'    => 'bdt-event-countdown'
                        ],
                        [
                            'name'     => 'loop_time',
                            'operator' => '==',
                            'value'    => 'yes'
                        ],
                    ]
                ]
			]
		);

		$this->add_control(
            'loop_show_note',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => __( 'This Loop time option will only work on client side. If you logged in, this will not work. You can test it on incognito mode of your browser. The Last 15 minutes will work randomly.', 'bdthemes-element-pack' ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                'condition'   => [
					'loop_time' => 'yes',
					'_skin!' => 'bdt-event-countdown',
				],
            ]
        );


		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_count',
			[
				'label' => esc_html__( 'Count Layout', 'bdthemes-element-pack' ),
			]
		);

		$this->add_responsive_control(
			'count_column',
			[
				'label'          => esc_html__( 'Count Column', 'bdthemes-element-pack' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '4',
				'tablet_default' => '2',
				'mobile_default' => '2',
				'options'        => [
					''  => esc_html__( 'Default', 'bdthemes-element-pack' ),
					'1' => esc_html__( '1 Columns', 'bdthemes-element-pack' ),
					'2' => esc_html__( '2 Columns', 'bdthemes-element-pack' ),
					'3' => esc_html__( '3 Column', 'bdthemes-element-pack' ),
					'4' => esc_html__( '4 Columns', 'bdthemes-element-pack' ),
				],
				'condition' => [
					'_skin' => '',
				]
			]
		);

		$this->add_control(
			'count_gap',
			[
				'label'   => esc_html__( 'Gap', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					''         => esc_html__( 'Default', 'bdthemes-element-pack' ),
					'small'    => esc_html__( 'Small', 'bdthemes-element-pack' ),
					'medium'   => esc_html__( 'Medium', 'bdthemes-element-pack' ),
					'large'    => esc_html__( 'Large', 'bdthemes-element-pack' ),
					'collapse' => esc_html__( 'Collapse', 'bdthemes-element-pack' ),
				],
				'condition' => [
					'_skin!' => 'bdt-tiny-countdown'
				],
			]
		);

		$this->add_responsive_control(
			'number_label_gap',
			[
				'label'   => esc_html__( 'Number & Label Gap', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}}.bdt-countdown--label-block .bdt-countdown-number'  => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.bdt-countdown--label-inline .bdt-countdown-number' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_labels!' => '',
					'_skin!' => 'bdt-tiny-countdown'
				],
			]
		);

		$this->add_responsive_control(
			'tiny_item_spacing',
			[
				'label'   => esc_html__( 'Item Gap', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}}.elementor-widget-bdt-countdown .bdt-countdown-skin-tiny .bdt-countdown-item-wrapper'  => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'_skin' => 'bdt-tiny-countdown'
				],
			]
		);

		$this->add_responsive_control(
			'tiny_number_label_gap',
			[
				'label'   => esc_html__( 'Number & Label Gap', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}}.elementor-widget-bdt-countdown .bdt-countdown-skin-tiny .bdt-countdown-number'  => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_labels!' => '',
					'_skin' => 'bdt-tiny-countdown'
				],
			]
		);

		$this->add_control(
			'alignment',
			[
				'label'        => __( 'Text Alignment', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'left'   => [
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'      => 'center',
				'condition' => [
					'_skin!' => 'bdt-tiny-countdown'
				],
				'prefix_class' => 'bdt-countdown--align-',
				'render_type' => 'template'
			]
		);

		$this->add_responsive_control(
			'tiny_alignment',
			[
				'label'        => __( 'Alignment', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'left'   => [
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'      => 'center',
				'selectors'  => [
					'{{WRAPPER}} .bdt-countdown-skin-tiny' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'_skin' => 'bdt-tiny-countdown'
				],
				'render_type' => 'template'
			]
		);

		$this->add_responsive_control(
			'container_width',
			[
				'label'   => esc_html__( 'Container Width', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					// 'size' => 70,
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 2000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ '%', 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-countdown-wrapper' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;',
				],
				'condition' => [
					'_skin!' => 'bdt-tiny-countdown'
				],
			]
		);

		$this->add_control(
			'content_align',
			[
				'label'       => __( 'Content Align', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => [
					'left' => [
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-countdown-wrapper' => 'margin-{{VALUE}}: 0;',
				],
				'condition' => [
					'_skin!' => 'bdt-tiny-countdown'
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_additional',
			[
				'label' => esc_html__( 'Additional Options', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'label_display',
			[
				'label'   => esc_html__( 'View', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'block'  => esc_html__( 'Block', 'bdthemes-element-pack' ),
					'inline' => esc_html__( 'Inline', 'bdthemes-element-pack' ),
				],
				'default'      => 'block',
				'prefix_class' => 'bdt-countdown--label-',
				'condition' => [
					'_skin!' => 'bdt-tiny-countdown'
				],
			]
		);

		$this->add_control(
			'show_days',
			[
				'label'   => esc_html__( 'Days', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_hours',
			[
				'label'   => esc_html__( 'Hours', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_minutes',
			[
				'label'   => esc_html__( 'Minutes', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_seconds',
			[
				'label'   => esc_html__( 'Seconds', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_labels',
			[
				'label'   => esc_html__( 'Show Label', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'custom_labels',
			[
				'label'        => esc_html__( 'Custom Label', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'condition'    => [
					'show_labels!' => '',
				],
			]
		);

		$this->add_control(
			'label_days',
			[
				'label'       => esc_html__( 'Days', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Days', 'bdthemes-element-pack' ),
				'placeholder' => esc_html__( 'Days', 'bdthemes-element-pack' ),
				'condition'   => [
					'show_labels!'   => '',
					'custom_labels!' => '',
					'show_days'      => 'yes',
				],
			]
		);

		$this->add_control(
			'label_hours',
			[
				'label'       => esc_html__( 'Hours', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Hours', 'bdthemes-element-pack' ),
				'placeholder' => esc_html__( 'Hours', 'bdthemes-element-pack' ),
				'condition'   => [
					'show_labels!'   => '',
					'custom_labels!' => '',
					'show_hours'     => 'yes',
				],
			]
		);

		$this->add_control(
			'label_minutes',
			[
				'label'       => esc_html__( 'Minutes', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Minutes', 'bdthemes-element-pack' ),
				'placeholder' => esc_html__( 'Minutes', 'bdthemes-element-pack' ),
				'condition'   => [
					'show_labels!'   => '',
					'custom_labels!' => '',
					'show_minutes'   => 'yes',
				],
			]
		);

		$this->add_control(
			'label_seconds',
			[
				'label'       => esc_html__( 'Seconds', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Seconds', 'bdthemes-element-pack' ),
				'placeholder' => esc_html__( 'Seconds', 'bdthemes-element-pack' ),
				'condition'   => [
					'show_labels!'   => '',
					'custom_labels!' => '',
					'show_seconds'   => 'yes',
				],
			]
		);

		$this->add_control(
			'show_separator',
			[
				'label' => esc_html__('Show Separator', 'bdthemes-element-pack') . BDTEP_NC,
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'separator',
			[
				'label' => __('Symbol', 'bdthemes-element-pack'),
				'type' => Controls_Manager::TEXT,
				'default' => ':',
				'condition' => [
					'show_separator' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_end_action',
			[
				'label' => __('End Action', 'bdthemes-element-pack') . BDTEP_NC,
				'tab'   => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'loop_time!' => 'yes',
				],
			]
		);

		$this->add_control(
			'end_action_note',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'Choose which action you want to at the end of countdown.', 'bdthemes-element-pack' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
			]
		);

		$this->add_control(
			'end_action_type',
			[
				'label'       => esc_html__('Type', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'none'    => esc_html__('None', 'bdthemes-element-pack'),
					'message' => esc_html__('Message', 'bdthemes-element-pack'),
					'url'     => esc_html__('Redirection Link', 'bdthemes-element-pack'),
					'coupon-code'     => esc_html__('Coupon Code', 'bdthemes-element-pack'),
					'trigger'     => esc_html__('On trigger', 'bdthemes-element-pack'),
				],
				'default' => 'none'
			]
		);

		$this->add_control(
            'id_for_coupon_code',
            [
                'label'       => __('ID for Coupon Code', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => ['active' => true],
                'label_block' => true,
				'condition'	  => [
					'end_action_type' => 'coupon-code'
				]
            ]
        );

		$this->add_control(
            'selector_for_trigger',
            [
                'label'       => __('Trigger ID', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => ['active' => true],
                'label_block' => true,
				'condition'	  => [
					'end_action_type' => 'trigger'
				]
            ]
        );

		$this->add_control(
			'end_message',
			[
				'label'       => __('End Message', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::WYSIWYG,
				'default'     => __('Countdown End!', 'bdthemes-element-pack'),
				'placeholder' => __('Type your message here', 'bdthemes-element-pack'),
				'condition'   => [
					'end_action_type' => 'message'
				],
			]
		);

		$this->add_control(
			'end_redirect_link',
			[
				'label' => __('Redirection Link', 'bdthemes-element-pack'),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __('https://elementpack.pro/', 'bdthemes-element-pack'),
				'condition' => [
					'end_action_type' => 'url'
				],
			]
		);

		$this->add_control(
			'link_redirect_delay',
			[
				'label' => __('Redirection Delay (s)', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range'      => [
                    'px' => [
                        'min' => 0,
                        'max' => 10,
                    ],
                ],
				'condition' => [
					'end_action_type' => 'url'
				],
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_count_style',
			[
				'label' => esc_html__( 'Items Style', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
            'glassmorphism_effect',
            [
                'label' => esc_html__('Glassmorphism', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
				'description' => sprintf( __( 'This feature will not work in the Firefox browser untill you enable browser compatibility so please %1s look here %2s', 'bdthemes-element-pack' ), '<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/backdrop-filter#Browser_compatibility" target="_blank">', '</a>' ),
            
            ]
		);
		
		$this->add_control(
            'glassmorphism_blur_level',
            [
                'label'       => __('Blur Level', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SLIDER,
                'range'       => [
                    'px' => [
                        'min'  => 0,
                        'step' => 1,
                        'max'  => 50,
                    ]
                ],
                'default'     => [
                    'size' => 5
                ],
                'selectors'   => [
                    '{{WRAPPER}} .bdt-countdown-item' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);'
				],
				'condition' => [
					'glassmorphism_effect' => 'yes',
				]
            ]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'count_background_color',
				'selector'  => '{{WRAPPER}} .bdt-countdown-item',
				'condition' => [
					'individual_style' => ''
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'count_border',
				'label'    => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-countdown-item',
				'separator' => 'before',
				'condition' => [
					'individual_style' => ''
				]
			]
		);

		$this->add_responsive_control(
			'count_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-countdown-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'individual_style' => ''
				]
			]
		);

		$this->add_responsive_control(
			'count_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-countdown-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'individual_style' => ''
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'count_shadow',
				'selector' => '{{WRAPPER}} .bdt-countdown-item',
				'condition' => [
					'individual_style' => ''
				]
			]
		);

		$this->add_control(
			'individual_style',
			[
				'label'     => esc_html__( 'Individual Style', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before'
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_number_style',
			[
				'label' => esc_html__( 'Number', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'individual_style' => ''
				]
			]
		);

		$this->add_control(
			'number_color',
			[
				'label'  => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-countdown-number' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'number_background',
				'selector'  => '{{WRAPPER}} .bdt-countdown-number',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'number_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-countdown-number',
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'number_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-countdown-number' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'number_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-countdown-number' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'label' => __( 'Text Shadow', 'bdthemes-element-pack' ) . BDTEP_NC,
				'selector' => '{{WRAPPER}} .bdt-countdown-number',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'number_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-countdown-number',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'number_typography',
				'selector' => '{{WRAPPER}} .bdt-countdown-number',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_label_style',
			[
				'label'     => esc_html__( 'Label', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_labels' => 'yes',
					'individual_style' => '',
				],
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-countdown-label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'label_background',
				'selector'  => '{{WRAPPER}} .bdt-countdown-label',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'label_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-countdown-label',
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'label_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-countdown-label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'label_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-countdown-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'label_margin',
			[
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-countdown-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'label_shadow',
				'label' => __( 'Text Shadow', 'bdthemes-element-pack' ) . BDTEP_NC,
				'selector' => '{{WRAPPER}} .bdt-countdown-label',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'label_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-countdown-label',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .bdt-countdown-label',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_days_style',
			[
				'label'     => esc_html__( 'Days Style', 'bdthemes-element-pack' ) . BDTEP_NC,
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_days' => 'yes',
					'individual_style' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'days_background',
				'selector'  => '{{WRAPPER}} .bdt-countdown-item-wrapper .bdt-days-wrapper',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'days_border',
				'selector'    => '{{WRAPPER}} .bdt-countdown-item-wrapper .bdt-days-wrapper',
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'days_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-countdown-item-wrapper .bdt-days-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'days_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-countdown-item-wrapper .bdt-days-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'days_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-countdown-item-wrapper .bdt-days-wrapper',
			]
		);

		$this->start_controls_tabs( 'tabs_days_number' );

		$this->start_controls_tab( 
			'tab_days_number',
			[
				'label' => __( 'N u m b e r', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'days_number_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-days-wrapper .bdt-countdown-number' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'days_number_background',
				'selector'  => '{{WRAPPER}} .bdt-days-wrapper .bdt-countdown-number',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'days_number_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-days-wrapper .bdt-countdown-number',
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'days_number_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-days-wrapper .bdt-countdown-number' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'days_number_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-days-wrapper .bdt-countdown-number' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'days_number_shadow',
				'label' => __( 'Text Shadow', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-days-wrapper .bdt-countdown-number',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'days_number_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-days-wrapper .bdt-countdown-number',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'days_number_typography',
				'selector' => '{{WRAPPER}} .bdt-days-wrapper .bdt-countdown-number',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_days_label',
			[
				'label' => __( 'L a b e l', 'bdthemes-element-pack' ),
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_control(
			'days_label_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-days-wrapper .bdt-countdown-label' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'days_label_background',
				'selector'  => '{{WRAPPER}} .bdt-days-wrapper .bdt-countdown-label',
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'days_label_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-days-wrapper .bdt-countdown-label',
				'separator' => 'before',
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'days_label_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-days-wrapper .bdt-countdown-label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'days_label_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-days-wrapper .bdt-countdown-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'days_label_margin',
			[
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-days-wrapper .bdt-countdown-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'days_label_shadow',
				'label' => __( 'Text Shadow', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-days-wrapper .bdt-countdown-label',
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'days_label_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-days-wrapper .bdt-countdown-label',
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'days_label_typography',
				'selector' => '{{WRAPPER}} .bdt-days-wrapper .bdt-countdown-label',
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_hours_style',
			[
				'label'     => esc_html__( 'Hours Style', 'bdthemes-element-pack' ) . BDTEP_NC,
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_hours' => 'yes',
					'individual_style' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'hours_background',
				'selector'  => '{{WRAPPER}} .bdt-countdown-item-wrapper .bdt-hours-wrapper',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'hours_border',
				'selector'    => '{{WRAPPER}} .bdt-countdown-item-wrapper .bdt-hours-wrapper',
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'hours_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-countdown-item-wrapper .bdt-hours-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'hours_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-countdown-item-wrapper .bdt-hours-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'hours_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-countdown-item-wrapper .bdt-hours-wrapper',
			]
		);

		$this->start_controls_tabs( 'tabs_hours_number' );

		$this->start_controls_tab( 
			'tab_hours_number',
			[
				'label' => __( 'N u m b e r', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'hours_number_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-hours-wrapper .bdt-countdown-number' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'hours_number_background',
				'selector'  => '{{WRAPPER}} .bdt-hours-wrapper .bdt-countdown-number',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'hours_number_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-hours-wrapper .bdt-countdown-number',
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'hours_number_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-hours-wrapper .bdt-countdown-number' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'hours_number_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-hours-wrapper .bdt-countdown-number' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'hours_number_shadow',
				'label' => __( 'Text Shadow', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-hours-wrapper .bdt-countdown-number',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'hours_number_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-hours-wrapper .bdt-countdown-number',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'hours_number_typography',
				'selector' => '{{WRAPPER}} .bdt-hours-wrapper .bdt-countdown-number',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_hours_label',
			[
				'label' => __( 'L a b e l', 'bdthemes-element-pack' ),
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_control(
			'hours_label_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-hours-wrapper .bdt-countdown-label' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'hours_label_background',
				'selector'  => '{{WRAPPER}} .bdt-hours-wrapper .bdt-countdown-label',
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'hours_label_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-hours-wrapper .bdt-countdown-label',
				'separator' => 'before',
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'hours_label_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-hours-wrapper .bdt-countdown-label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'hours_label_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-hours-wrapper .bdt-countdown-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'hours_label_margin',
			[
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-hours-wrapper .bdt-countdown-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'hours_label_shadow',
				'label' => __( 'Text Shadow', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-hours-wrapper .bdt-countdown-label',
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'hours_label_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-hours-wrapper .bdt-countdown-label',
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'hours_label_typography',
				'selector' => '{{WRAPPER}} .bdt-hours-wrapper .bdt-countdown-label',
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_minutes_style',
			[
				'label'     => esc_html__( 'Minutes Style', 'bdthemes-element-pack' ) . BDTEP_NC,
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_minutes' => 'yes',
					'individual_style' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'minutes_background',
				'selector'  => '{{WRAPPER}} .bdt-countdown-item-wrapper .bdt-minutes-wrapper',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'minutes_border',
				'selector'    => '{{WRAPPER}} .bdt-countdown-item-wrapper .bdt-minutes-wrapper',
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'minutes_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-countdown-item-wrapper .bdt-minutes-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'minutes_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-countdown-item-wrapper .bdt-minutes-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'minutes_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-countdown-item-wrapper .bdt-minutes-wrapper',
			]
		);

		$this->start_controls_tabs( 'tabs_minutes_number' );

		$this->start_controls_tab( 
			'tab_minutes_number',
			[
				'label' => __( 'N u m b e r', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'minutes_number_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-minutes-wrapper .bdt-countdown-number' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'minutes_number_background',
				'selector'  => '{{WRAPPER}} .bdt-minutes-wrapper .bdt-countdown-number',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'minutes_number_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-minutes-wrapper .bdt-countdown-number',
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'minutes_number_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-minutes-wrapper .bdt-countdown-number' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'minutes_number_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-minutes-wrapper .bdt-countdown-number' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'minutes_number_shadow',
				'label' => __( 'Text Shadow', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-minutes-wrapper .bdt-countdown-number',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'minutes_number_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-minutes-wrapper .bdt-countdown-number',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'minutes_number_typography',
				'selector' => '{{WRAPPER}} .bdt-minutes-wrapper .bdt-countdown-number',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_minutes_label',
			[
				'label' => __( 'L a b e l', 'bdthemes-element-pack' ),
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_control(
			'minutes_label_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-minutes-wrapper .bdt-countdown-label' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'minutes_label_background',
				'selector'  => '{{WRAPPER}} .bdt-minutes-wrapper .bdt-countdown-label',
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'minutes_label_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-minutes-wrapper .bdt-countdown-label',
				'separator' => 'before',
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'minutes_label_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-minutes-wrapper .bdt-countdown-label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'minutes_label_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-minutes-wrapper .bdt-countdown-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'minutes_label_margin',
			[
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-minutes-wrapper .bdt-countdown-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'minutes_label_shadow',
				'label' => __( 'Text Shadow', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-minutes-wrapper .bdt-countdown-label',
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'minutes_label_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-minutes-wrapper .bdt-countdown-label',
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'minutes_label_typography',
				'selector' => '{{WRAPPER}} .bdt-minutes-wrapper .bdt-countdown-label',
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_seconds_style',
			[
				'label'     => esc_html__( 'Seconds Style', 'bdthemes-element-pack' ) . BDTEP_NC,
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_seconds' => 'yes',
					'individual_style' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'seconds_background',
				'selector'  => '{{WRAPPER}} .bdt-countdown-item-wrapper .bdt-seconds-wrapper',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'seconds_border',
				'selector'    => '{{WRAPPER}} .bdt-countdown-item-wrapper .bdt-seconds-wrapper',
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'seconds_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-countdown-item-wrapper .bdt-seconds-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'seconds_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-countdown-item-wrapper .bdt-seconds-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'seconds_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-countdown-item-wrapper .bdt-seconds-wrapper',
			]
		);

		$this->start_controls_tabs( 'tabs_seconds_number' );

		$this->start_controls_tab( 
			'tab_seconds_number',
			[
				'label' => __( 'N u m b e r', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'seconds_number_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-seconds-wrapper .bdt-countdown-number' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'seconds_number_background',
				'selector'  => '{{WRAPPER}} .bdt-seconds-wrapper .bdt-countdown-number',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'seconds_number_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-seconds-wrapper .bdt-countdown-number',
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'seconds_number_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-seconds-wrapper .bdt-countdown-number' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'seconds_number_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-seconds-wrapper .bdt-countdown-number' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'seconds_number_shadow',
				'label' => __( 'Text Shadow', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-seconds-wrapper .bdt-countdown-number',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'seconds_number_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-seconds-wrapper .bdt-countdown-number',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'seconds_number_typography',
				'selector' => '{{WRAPPER}} .bdt-seconds-wrapper .bdt-countdown-number',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_seconds_label',
			[
				'label' => __( 'L a b e l', 'bdthemes-element-pack' ),
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_control(
			'seconds_label_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-seconds-wrapper .bdt-countdown-label' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'seconds_label_background',
				'selector'  => '{{WRAPPER}} .bdt-seconds-wrapper .bdt-countdown-label',
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'seconds_label_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-seconds-wrapper .bdt-countdown-label',
				'separator' => 'before',
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'seconds_label_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-seconds-wrapper .bdt-countdown-label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'seconds_label_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-seconds-wrapper .bdt-countdown-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'seconds_label_margin',
			[
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-seconds-wrapper .bdt-countdown-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'seconds_label_shadow',
				'label' => __( 'Text Shadow', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-seconds-wrapper .bdt-countdown-label',
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'seconds_label_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-seconds-wrapper .bdt-countdown-label',
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'seconds_label_typography',
				'selector' => '{{WRAPPER}} .bdt-seconds-wrapper .bdt-countdown-label',
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		//Separator Style
		$this->start_controls_section(
			'section_separator_style',
			[
				'label'     => esc_html__( 'Separator', 'bdthemes-element-pack' ) . BDTEP_NC,
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_separator' => 'yes',
				],
			]
		);

		$this->add_control(
			'separator_color',
			[
				'label' => __('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-countdown-wrapper .bdt-countdown-divider' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'separator_size',
			[
				'label' => __('Size', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .bdt-countdown-wrapper .bdt-countdown-divider' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'divider_offset_popover',
			[
				'label'        => esc_html__('Offset', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'render_type'  => 'ui',
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'divider_horizontal_offset',
			[
				'label' => __( 'Horizontal', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'condition' => [
					'divider_offset_popover' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-countdown-separator-h-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'divider_vertical_offset',
			[
				'label' => __( 'Vertical', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-countdown-separator-v-offset: {{SIZE}}px;'
				],
				'condition' => [
					'divider_offset_popover' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'divider_rotate',
			[
				'label'   => esc_html__( 'Rotate', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -360,
						'max' => 360,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-countdown-separator-rotate: {{SIZE}}deg;'
				],
				'condition' => [
					'divider_offset_popover' => 'yes',
				],
			]
		);
		
		$this->end_popover();
		
		$this->end_controls_section();

		$this->start_controls_section(
			'section_end_message_style',
			[
				'label' => esc_html__( 'End Message', 'bdthemes-element-pack' ) . BDTEP_NC,
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'end_action_type' => 'message',
					'loop_time!' => 'yes',
				],
			]
		); 

		$this->add_control(
			'end_message_color',
			[
				'label'  => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-countdown-end-message' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'end_message_background',
				'selector'  => '{{WRAPPER}} .bdt-countdown-end-message',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'end_message_border',
				'selector'    => '{{WRAPPER}} .bdt-countdown-end-message',
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'end_message_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-countdown-end-message' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'end_message_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-countdown-end-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'end_message_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-countdown-end-message',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'end_message_typography',
				'selector' => '{{WRAPPER}} .bdt-countdown-end-message',
			]
		);

		$this->add_responsive_control(
			'end_message_alignment',
			[
				'label'        => __( 'Alignment', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'left'   => [
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'      => 'center',
				'selectors'  => [
					'{{WRAPPER}} .bdt-countdown-end-message' => 'text-align: {{VALUE}};',
				],
				'render_type' => 'template'
			]
		);

		$this->end_controls_section();
	}

	public function get_strftime( $settings ) {
		$string = '';
		if ( $settings['show_days'] ) {
			$string .= $this->render_countdown_item( $settings, 'label_days', 'bdt-days-wrapper', 'bdt-countdown-days' );
		}
		if ( $settings['show_hours'] ) {
			$string .= $this->render_countdown_item( $settings, 'label_hours', 'bdt-hours-wrapper', 'bdt-countdown-hours' );
		}
		if ( $settings['show_minutes'] ) {
			$string .= $this->render_countdown_item( $settings, 'label_minutes', 'bdt-minutes-wrapper', 'bdt-countdown-minutes' );
		}
		if ( $settings['show_seconds'] ) {
			$string .= $this->render_countdown_item( $settings, 'label_seconds', 'bdt-seconds-wrapper', 'bdt-countdown-seconds' );
		}

		return $string;
	}

	private $_default_countdown_labels;

	private function _init_default_countdown_labels() {
		$this->_default_countdown_labels = [
			'label_months'  => esc_html__( 'Months', 'bdthemes-element-pack' ),
			'label_weeks'   => esc_html__( 'Weeks', 'bdthemes-element-pack' ),
			'label_days'    => esc_html__( 'Days', 'bdthemes-element-pack' ),
			'label_hours'   => esc_html__( 'Hours', 'bdthemes-element-pack' ),
			'label_minutes' => esc_html__( 'Minutes', 'bdthemes-element-pack' ),
			'label_seconds' => esc_html__( 'Seconds', 'bdthemes-element-pack' ),
		];
	}

	public function get_default_countdown_labels() {
		if ( ! $this->_default_countdown_labels ) {
			$this->_init_default_countdown_labels();
		}

		return $this->_default_countdown_labels;
	}

	private function render_countdown_item( $settings, $label, $wrapper_class, $part_class ) {
		$string  = '<div class="bdt-countdown-item-wrapper">';
		$string .= '<div class="bdt-countdown-item ' . $wrapper_class . '">';
		$string .= '<span class="bdt-countdown-number ' . $part_class . ' bdt-text-'.esc_attr($this->get_settings('alignment')).'"></span>';

		if ( $settings['show_labels'] ) {
			$default_labels = $this->get_default_countdown_labels();
			$label          = ( $settings['custom_labels'] ) ? $settings[ $label ] : $default_labels[ $label ];
			$string        .= ' <span class="bdt-countdown-label bdt-text-'.esc_attr($this->get_settings('alignment')).'">' . $label . '</span>';
		}

		if ('yes' == $settings['show_separator'] ) {
			$string .= '<span class="bdt-countdown-divider">'. esc_attr($settings['separator']) .'</span>';
		}

		$string .= '</div>';
		$string .= '</div>';

		return $string;
	}

	public function wp_current_time() { 

		$wp_current_time = date( 'Y-m-d H:i', current_time( 'timestamp' ) - ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
		$wp_current_time      = new DateTime($wp_current_time);
		$wp_current_time    = $wp_current_time->format('c');
		return strtotime($wp_current_time);

	}

	public function wp_final_time() { 
		$settings      = $this->get_settings_for_display();
		$due_date      = $settings['due_date'];
		
		$with_gmt_time = date( 'Y-m-d H:i', strtotime( $due_date ) - ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );		
		$datetime      = new DateTime($with_gmt_time);

		return  $datetime->format('c');
	
	}
	protected function render() { 
		$settings      = $this->get_settings_for_display();
		$due_date      = isset($settings['due_date']) ? $settings['due_date'] : '';
		$string        = $this->get_strftime( $settings );
		
		$with_gmt_time = date( 'Y-m-d H:i', strtotime( $due_date ) - ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );		
		$datetime      = new DateTime($with_gmt_time);

		$final_time    = $datetime->format('c');



		//

		$ended_message = '';
		$wp_current_timeX = current_time( 'timestamp' ) - ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
		$end_timeX = strtotime($final_time);

		if( $wp_current_timeX > $end_timeX ){
			$ended_message = 'ended';
		}

		$count_column_mobile = isset($settings['count_column_mobile']) ? $settings['count_column_mobile'] : 2;
		$count_column_tablet = isset($settings['count_column_tablet']) ? $settings['count_column_tablet'] : 2;
		$count_column 		 = isset($settings['count_column']) ? $settings['count_column'] : 4;

		$this->add_render_attribute(
			[
				'countdown' => [
					'id' 	=> 'bdt-countdown-' . $this->get_id() . '-timer',
					'class' => [
						'bdt-grid',
						$settings['count_gap'] ? 'bdt-grid-' . esc_attr($settings['count_gap']) : '',
						'bdt-child-width-1-' . esc_attr($count_column_mobile),
						'bdt-child-width-1-' . esc_attr($count_column_tablet) . '@s',
						'bdt-child-width-1-' . esc_attr($count_column) . '@m'
					],
					'data-bdt-countdown' => [
						isset($settings['loop_time']) && ($settings['loop_time'] == 'yes') ?  '' : 'date: ' . $final_time
					],
					'data-bdt-grid' => '',
					'style' => ($settings['end_action_type'] == 'message') && (!$this->ep_is_edit_mode()) && (!empty($ended_message)) ? 'display:none;' : 'x'
				],
			]
		);

		 

		$end_time = strtotime($final_time);

		if(is_user_logged_in()){
			$is_logged = true;
		}else{
			$is_logged = false;
		}

		$msg_id = 'bdt-countdown-msg-' . $this->get_id() . '';

		$id       = $this->get_id();
		$coupon_tricky_id  = !empty($settings['id_for_coupon_code']) ? 'bdt-sf-' . $settings['id_for_coupon_code'] :  'bdt-sf-' . $id;

		$trigger_id  = !empty($settings['selector_for_trigger']) ? $settings['selector_for_trigger'] :  false;

		// $coupon_tricky_id  = !empty($settings['_element_id']) ? 'bdt-sf-' . $settings['_element_id'] :  'bdt-sf-' . $id;

		$this->add_render_attribute(
			[
				'countdown_wrapper' => [
					'class' => 'bdt-countdown-wrapper bdt-countdown-skin-default',
					'data-settings' => [
						wp_json_encode([
							'id'             => '#bdt-countdown-' . $this->get_id(), 
							'msgId'			 => '#' . $msg_id,
							'adminAjaxUrl'   => admin_url("admin-ajax.php"),
							'endActionType'	 => $settings['end_action_type'],
							'redirectUrl'	 => $settings['end_redirect_link'],
							'redirectDelay'	 => (empty($settings['link_redirect_delay']['size'])) ? 1000 : ($settings['link_redirect_delay']['size']) * 1000,
							'finalTime'		 => isset($settings['loop_time']) && ($settings['loop_time'] == 'yes') ?  '' :  $final_time,
							'wpCurrentTime'  => $this->wp_current_time(),
							'endTime'		 => $end_time,
							'loopHours'      => $settings['loop_time'] == 'yes' ?  $settings['loop_hours'] : false,
							'isLogged'       => $is_logged,
							'couponTrickyId' => $coupon_tricky_id,
							'triggerId' => $trigger_id
						]),
					],
				],
			]
		);
 
		?>


		<div <?php echo $this->get_render_attribute_string('countdown_wrapper'); ?>>
			<div <?php echo $this->get_render_attribute_string( 'countdown' ); ?>>
				<?php echo wp_kses_post($string); ?>
			</div>

			<?php if ($settings['end_action_type'] == 'message') : ?> 
			<div id="<?php echo $msg_id; ?>" class="bdt-countdown-end-message" style="display:none;">
				<?php echo wp_kses_post($settings['end_message']); ?>
			</div>
			<?php endif; ?>

		</div>

		<?php
	}
}
