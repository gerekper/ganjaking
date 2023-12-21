<?php
/**
 * Icon Box widget class
 *
 * @package Happy_Addons
 */
namespace Happy_Addons\Elementor\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

defined( 'ABSPATH' ) || die();

class Icon_Box extends Base {

	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Icon Box', 'happy-elementor-addons' );
	}

	public function get_custom_help_url() {
		return 'https://happyaddons.com/docs/happy-addons-for-elementor/widgets/icon-box/';
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'hm hm-icon-box';
	}

	public function get_keywords() {
		return [ 'info', 'box', 'icon' ];
	}

	/**
     * Register widget content controls
     */
	protected function register_content_controls() {

		$this->start_controls_section(
			'_section_icon',
			[
				'label' => __( 'Content', 'happy-elementor-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'icon_type',
			[
				'label' => esc_html__( 'Icon Type', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => esc_html__( 'Elementor Default', 'happy-elementor-addons' ),
					'lordicon' => esc_html__( 'LordIcon', 'happy-elementor-addons' ),
				],
			]
		);

		if ( ha_is_elementor_version( '<', '2.6.0' ) ) {
			$this->add_control(
				'icon',
				[
					'show_label' => false,
					'type' => Controls_Manager::ICON,
					'label_block' => true,
					'options' => ha_get_happy_icons(),
					'default' => 'fa fa-smile-o',
					'condition' => [
						'icon_type' => 'default'
					],
				]
			);
		} else {
			$this->add_control(
				'selected_icon',
				[
					'show_label' => false,
					'type' => Controls_Manager::ICONS,
					'fa4compatibility' => 'icon',
					'label_block' => true,
					'default' => [
						'value' => 'fas fa-smile-wink',
						'library' => 'fa-solid',
					],
					'condition' => [
						'icon_type' => 'default'
					],
				]
			);
		}

		$this->add_control(
            'icon_method',
            [
                'type'        => Controls_Manager::SELECT,
                'label'       => __('Icon Method', 'happy-elementor-addons'),
                'description' => sprintf('<a target="_blank" href="%1$s">Learn how to use the Lordicon widget</a>', esc_url('https://happyaddons.com/docs/happy-addons-for-elementor/widgets/lord-icon')),
                'options'     => [
                    'cdn'  => esc_html__('Paste LordIcon URL', 'happy-elementor-addons'),
                    'file' => esc_html__('Upload LordIcon file', 'happy-elementor-addons'),
                ],
                'default'     => 'cdn',
                'label_block' => true,
				'condition' => [
					'icon_type' => 'lordicon'
				],
            ]
        );
        $this->add_control(
            'icon_cdn',
            [
                'type'        => Controls_Manager::TEXT,
                'label'       => __('Paste CDN', 'happy-elementor-addons'),
                'label_block' => true,
                'description' => sprintf(
                    'Paste icon code from <a target="_blank" href="%1$s">lordicon.com</a> <br /><br /> <a target="_blank" href="%2$s">Learn how to get Lordicon CDN</a><br><br>
                Example: https://cdn.lordicon.com/lupuorrc.json', esc_url('https://lordicon.com/'), esc_url('https://happyaddons.com/docs/happy-addons-for-elementor/widgets/lord-icon')
                ),
                'default'     => 'https://cdn.lordicon.com/lupuorrc.json',
                'condition'   => [
					'icon_method' => 'cdn',
					'icon_type' => 'lordicon',
                ],
            ]
        );
        $this->add_control(
            'icon_json',
            [
                'type'        => Controls_Manager::MEDIA,
                'label'       => __('JSON File', 'happy-elementor-addons'),
                'media_type'  => 'application/json',
                'description' => sprintf('Download Json file from <a href="%1$s" target="_blank">lordicon.com</a>', esc_url('https://lordicon.com/')),
                'default'     => [
                    'url' => HAPPY_ADDONS_ASSETS . 'vendor/lord-icon/placeholder.json',
                ],
                'condition'   => [
                    'icon_method' => 'file',
					'icon_type' => 'lordicon',
                ],
            ]
        );

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'happy-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => __( 'Happy Icon Box', 'happy-elementor-addons' ),
				'placeholder' => __( 'Type Icon Box Title', 'happy-elementor-addons' ),
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$this->add_control(
			'badge_text',
			[
				'label' => __( 'Badge Text', 'happy-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'Type Icon Badge Text', 'happy-elementor-addons' ),
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __( 'Box Link', 'happy-elementor-addons' ),
				'separator' => 'before',
				'type' => Controls_Manager::URL,
				'placeholder' => 'https://example.com',
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label' => __( 'Title HTML Tag', 'happy-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'separator' => 'before',
				'options' => [
					'h1'  => [
						'title' => __( 'H1', 'happy-elementor-addons' ),
						'icon' => 'eicon-editor-h1'
					],
					'h2'  => [
						'title' => __( 'H2', 'happy-elementor-addons' ),
						'icon' => 'eicon-editor-h2'
					],
					'h3'  => [
						'title' => __( 'H3', 'happy-elementor-addons' ),
						'icon' => 'eicon-editor-h3'
					],
					'h4'  => [
						'title' => __( 'H4', 'happy-elementor-addons' ),
						'icon' => 'eicon-editor-h4'
					],
					'h5'  => [
						'title' => __( 'H5', 'happy-elementor-addons' ),
						'icon' => 'eicon-editor-h5'
					],
					'h6'  => [
						'title' => __( 'H6', 'happy-elementor-addons' ),
						'icon' => 'eicon-editor-h6'
					]
				],
				'default' => 'h2',
				'toggle' => false,
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'happy-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-elementor-addons' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-elementor-addons' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-elementor-addons' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => __( 'Justify', 'happy-elementor-addons' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'toggle' => true,
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};'
				]
			]
		);

		$this->end_controls_section();
		$this->lordicon_settings();
	}

	protected function lordicon_settings(){
		$this->start_controls_section(
			'_section_lordicon_settings',
			[
				'label' => __( 'Lord Icon Settings', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
				'condition' =>[
					'icon_type' => 'lordicon'
				]
			]
		);

		$this->add_control(
            'animation_trigger',
            [
                'type'    => Controls_Manager::SELECT,
                'label'   => __('Animation Trigger', 'happy-elementor-addons'),
                'options' => [
                    'loop'          => esc_html__('Loop (infinite)', 'happy-elementor-addons'),
                    'click'         => esc_html__('Click', 'happy-elementor-addons'),
                    'hover'         => esc_html__('Hover', 'happy-elementor-addons'),
                    'loop-on-hover' => esc_html__('Loop on Hover', 'happy-elementor-addons'),
                    'morph'         => esc_html__('Morph', 'happy-elementor-addons'),
                    'morph-two-way' => esc_html__('Morph two way', 'happy-elementor-addons'),
                ],
                'default' => 'loop',
            ]
        );

        $this->add_control(
            'target',
            [
                'type'    => Controls_Manager::SELECT,
                'label'   => __('Target', 'happy-elementor-addons'),
                'options' => [
                    'widget'  => __('On Widget', 'happy-elementor-addons'),
                    // 'icon' => __('On Icon', 'happy-elementor-addons' ),
                    'column'  => __('On Column', 'happy-elementor-addons'),
                    'section' => __('On Section', 'happy-elementor-addons'),
                    'custom'  => __('Custom', 'happy-elementor-addons'),
                ],
                'default' => 'widget',
            ]
        );

        $this->add_control(
            'custom_target',
            [
                'type'        => Controls_Manager::TEXT,
                'label'       => __('Custom Target', 'happy-elementor-addons'),
                'placeholder' => __('.example', 'happy-elementor-addons'),
                'default'     => __('.example', 'happy-elementor-addons'),
                'condition'   => [
                    'target' => 'custom',
                ],
            ]
        );

        $this->add_control(
            'pulse_effect',
            [
                'label'        => esc_html__('Pulse Effect', 'happy-elementor-addons'),
                'type'         => Controls_Manager::SWITCHER,
                'description'  => __('This will override your box shadow', 'happy-elementor-addons'),
                'return_value' => 'yes',
                'default'      => '',
            ]
        );

        $this->add_control(
            'pulse_color',
            [
                'label'     => __('Pulse Color', 'happy-elementor-addons'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#B6B6B6',
                'condition' => [
                    'pulse_effect' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .pulse_effect' => '--icon-pulse-color:{{VALUE}}',
                ],
            ]
        );

		$this->end_controls_section();
	}

	/**
     * Register widget style controls
     */
	protected function register_style_controls() {
		$this->lord_icon_style_controls();
		$this->__icon_style_controls();
		$this->__title_style_controls();
		$this->__badge_style_controls();
	}

	protected function lord_icon_style_controls(){
        $this->start_controls_section(
            '_section_style_lord_icon',
            [
                'label' => __('Lord Icon', 'happy-elementor-addons'),
                'tab'   => Controls_Manager::TAB_STYLE,
				'condition' =>[
					'icon_type' => 'lordicon'
				]
            ]
        );

        $this->add_responsive_control(
            'lord_icon_size',
            [
                'label'   => __('Size', 'happy-elementor-addons'),
                'type'    => Controls_Manager::SLIDER,
                // 'size_units' => [ 'px' ],
                'range'   => [
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                ],
                'default' => [
                    'size' => 150,
                ],
            ]
        );

        $this->add_control(
            'primary_color',
            [
                'label'   => __('Primary Color', 'happy-elementor-addons'),
                'type'    => Controls_Manager::COLOR,
                'default' => '#121331',
            ]
        );

        $this->add_control(
            'secondary_color',
            [
                'label'   => __('Secondary Color', 'happy-elementor-addons'),
                'type'    => Controls_Manager::COLOR,
                'default' => '#08a88a',
            ]
        );

		$this->add_control(
            'tertiary_color',
            [
                'label'   => __('Tertiary Color', 'happy-elementor-addons'),
                'type'    => Controls_Manager::COLOR,
                'default' => '#0816A8',
            ]
        );

        $this->add_control(
            'quaternary_color',
            [
                'label'   => __('Quaternary Color', 'happy-elementor-addons'),
                'type'    => Controls_Manager::COLOR,
                'default' => '#2CA808',
            ]
        );

        $this->add_control(
            'lord_icon_stroke',
            [
                'label'   => __('Stroke', 'happy-elementor-addons'),
                'type'    => Controls_Manager::SLIDER,
                'range'   => [
                    'min' => 1,
                    'max' => 500,
                ],
                'default' => [
                    'size' => '20',
                ],
            ]
        );

        // $this->add_control(
        //     'lord_icon_bg_color',
        //     [
        //         'label'     => __('Background Color', 'happy-elementor-addons'),
        //         'type'      => Controls_Manager::COLOR,
        //         'selectors' => [
        //             '{{WRAPPER}} .ha-icon-box-icon lord-icon' => 'background: {{VALUE}};',
        //         ],
        //     ]
        // );

        // $this->add_group_control(
        //     Group_Control_Border::get_type(),
        //     [
        //         'name'     => 'lord_icon_border',
        //         'selector' => '{{WRAPPER}} .ha-icon-box-icon lord-icon',
        //     ]
        // );

        // $this->add_responsive_control(
        //     'lord_icon_border_radius',
        //     [
        //         'label'      => __('Border Radius', 'happy-elementor-addons'),
        //         'type'       => Controls_Manager::DIMENSIONS,
        //         'size_units' => ['px', '%'],
        //         'selectors'  => [
        //             '{{WRAPPER}} .ha-icon-box-icon lord-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        //         ],
        //     ]
        // );

        // $this->add_group_control(
        //     Group_Control_Box_Shadow::get_type(),
        //     [
        //         'name'     => 'lord_icon_shadow',
        //         'exclude'  => [
        //             'box_shadow_position',
        //         ],
        //         'selector' => '{{WRAPPER}} .ha-icon-box-icon lord-icon',
        //     ]
        // );

        $this->end_controls_section();
    }

	protected function __icon_style_controls() {

		$this->start_controls_section(
			'_section_style_icon',
			[
				'label' => __( 'Icon', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Size', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-icon-box-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label' => __( 'Padding', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ha-icon-box-icon' => 'padding: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_spacing',
			[
				'label' => __( 'Bottom Spacing', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'max' => 150,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .ha-icon-box-icon' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'icon_border',
				'selector' => '{{WRAPPER}} .ha-icon-box-icon'
			]
		);

		$this->add_responsive_control(
			'icon_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-icon-box-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'icon_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .ha-icon-box-icon'
			]
		);

		$this->start_controls_tabs( '_tabs_icon' );

		$this->start_controls_tab(
			'_tab_icon_normal',
			[
				'label' => __( 'Normal', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-icon-box-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_bg_color',
			[
				'label' => __( 'Background Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-icon-box-icon' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_bg_rotate',
			[
				'label' => __( 'Rotate Icon Box', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
				],
				'range' => [
					'deg' => [
						'min' => 0,
						'max' => 360,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-icon-box-icon' => '-webkit-transform: rotate({{SIZE}}{{UNIT}}); transform: rotate({{SIZE}}{{UNIT}});',
					'{{WRAPPER}} .ha-icon-box-icon > i' => '-webkit-transform: rotate(-{{SIZE}}{{UNIT}}); transform: rotate(-{{SIZE}}{{UNIT}});',
				],
				'condition' => [
					'icon_bg_color!' => '',
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_button_hover',
			[
				'label' => __( 'Hover', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'icon_hover_color',
			[
				'label' => __( 'Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}:hover .ha-icon-box-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_hover_bg_color',
			[
				'label' => __( 'Background Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}:hover .ha-icon-box-icon' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_hover_border_color',
			[
				'label' => __( 'Border Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}:hover .ha-icon-box-icon' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'icon_border_border!' => '',
				]
			]
		);

		$this->add_control(
			'icon_hover_bg_rotate',
			[
				'label' => __( 'Rotate Icon Box', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
				],
				'range' => [
					'deg' => [
						'min' => 0,
						'max' => 360,
					],
				],
				'selectors' => [
					'{{WRAPPER}}:hover .ha-icon-box-icon' => '-webkit-transform: rotate({{SIZE}}{{UNIT}}); transform: rotate({{SIZE}}{{UNIT}});',
					'{{WRAPPER}}:hover .ha-icon-box-icon > i' => '-webkit-transform: rotate(-{{SIZE}}{{UNIT}}); transform: rotate(-{{SIZE}}{{UNIT}});',
				],
				'condition' => [
					'icon_bg_color!' => '',
				]
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function __title_style_controls() {

		$this->start_controls_section(
			'_section_style_title',
			[
				'label' => __( 'Title', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title',
				'selector' => '{{WRAPPER}} .ha-icon-box-title',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title',
				'selector' => '{{WRAPPER}} .ha-icon-box-title',
			]
		);

		$this->start_controls_tabs( '_tabs_title' );

		$this->start_controls_tab(
			'_tab_title_normal',
			[
				'label' => __( 'Normal', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Text Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-icon-box-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_title_hover',
			[
				'label' => __( 'Hover', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label' => __( 'Text Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}:hover .ha-icon-box-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function __badge_style_controls() {

		$this->start_controls_section(
			'_section_style_badge',
			[
				'label' => __( 'Badge', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'badge_offset_toggle',
			[
				'label' => __( 'Offset', 'happy-elementor-addons' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'None', 'happy-elementor-addons' ),
				'label_on' => __( 'Custom', 'happy-elementor-addons' ),
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'badge_offset_x',
			[
				'label' => __( 'Offset Left', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'condition' => [
					'badge_offset_toggle' => 'yes'
				],
				'default' => [
					'size' => 1
				],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-badge' => '--ha-badge-translate-x: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'badge_offset_y',
			[
				'label' => __( 'Offset Top', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'condition' => [
					'badge_offset_toggle' => 'yes'
				],
				'default' => [
					'size' => 1
				],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-badge' => '--ha-badge-translate-y: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_popover();

		$this->add_responsive_control(
			'badge_padding',
			[
				'label' => __( 'Padding', 'happy-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'badge_color',
			[
				'label' => __( 'Text Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-badge' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_bg_color',
			[
				'label' => __( 'Background Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-badge' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'badge_border',
				'selector' => '{{WRAPPER}} .ha-badge',
			]
		);

		$this->add_responsive_control(
			'badge_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'badge_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .ha-badge',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'badge_typography',
				'label' => __( 'Typography', 'happy-elementor-addons' ),
				'exclude' => [
					'font_family',
					'line_height'
				],
				'default' => [
					'font_size' => ['']
				],
				'selector' => '{{WRAPPER}} .ha-badge',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Used to generate the final HTML displayed on the frontend.
	 *
	 * Note that if skin is selected, it will be rendered by the skin itself,
	 * not the widget.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render_content() {
		/**
		 * Before widget render content.
		 *
		 * Fires before Elementor widget is being rendered.
		 *
		 * @since 1.0.0
		 *
		 * @param Widget_Base $this The current widget.
		 */
		do_action( 'elementor/widget/before_render_content', $this );

		ob_start();

		$skin = $this->get_current_skin();
		if ( $skin ) {
			$skin->set_parent( $this );
			$skin->render();
		} else {
			$this->render();
		}

		$widget_content = ob_get_clean();

		if ( empty( $widget_content ) ) {
			return;
		}

		if ( ha_elementor()->editor->is_edit_mode() ) {
			$this->render_edit_tools();
		}

		$tag = 'div';
		$link = $this->get_settings_for_display( 'link' );
		$this->add_render_attribute( 'icon_box', 'class', 'elementor-widget-container' );

		if ( ! empty( $link['url'] ) ) {
			$tag = 'a';
			$this->add_render_attribute( 'icon_box', 'class', 'ha-icon-box-link' );
			$this->add_link_attributes( 'icon_box', $link );
		}
		?>
		<<?php echo $tag; ?> <?php echo $this->get_render_attribute_string( 'icon_box' ); ?>>
			<?php

			/**
			 * Render widget content.
			 *
			 * Filters the widget content before it's rendered.
			 *
			 * @since 1.0.0
			 *
			 * @param string      $widget_content The content of the widget.
			 * @param Widget_Base $this           The widget.
			 */
			$widget_content = apply_filters( 'elementor/widget/render_content', $widget_content, $this );

			echo $widget_content; // XSS ok.
			?>
		</<?php echo $tag; ?>>
		<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		//for manage loard icon global colors only
		$primary_color = $settings['primary_color'];
		if( isset($settings['__globals__']) && !empty($settings['__globals__']['primary_color']) ) { 
			$color_id = explode('=', $settings['__globals__']['primary_color']);
			$get_id = end($color_id);
			$primary_color = $this->get_golobal_color($get_id);
		} 
		
		$secondary_color = $settings['secondary_color'];
		if( isset($settings['__globals__']) && !empty($settings['__globals__']['secondary_color']) ) { 
			$color_id = explode('=', $settings['__globals__']['secondary_color']);
			$get_id = end($color_id);
			$secondary_color = $this->get_golobal_color($get_id);
		} 
		
		$tertiary_color = $settings['tertiary_color'];
		if( isset($settings['__globals__']) && !empty($settings['__globals__']['tertiary_color']) ) { 
			$color_id = explode('=', $settings['__globals__']['tertiary_color']);
			$get_id = end($color_id);
			$tertiary_color = $this->get_golobal_color($get_id);
		} 
		
		$quaternary_color = $settings['quaternary_color'];
		if( isset($settings['__globals__']) && !empty($settings['__globals__']['quaternary_color']) ) { 
			$color_id = explode('=', $settings['__globals__']['quaternary_color']);
			$get_id = end($color_id);
			$quaternary_color = $this->get_golobal_color($get_id);
		} 


		$this->add_inline_editing_attributes( 'title', 'basic' );
		$this->add_render_attribute( 'title', 'class', 'ha-icon-box-title' );

		$this->add_inline_editing_attributes( 'badge_text', 'none' );
		$this->add_render_attribute( 'badge_text', 'class', 'ha-badge ha-badge--top-right' );
		$json_url    = '';
        $method      = $settings['icon_method'];
        $target      = $settings['target'];
        $icon_size   = $settings['lord_icon_size'];
        $icon_stroke = $settings['lord_icon_stroke'];

		if( 'lordicon' == $settings[ 'icon_type' ] ){
			if ( 'file' == $method ) {
				$json_url = $settings['icon_json']['url'];
			} else {
				$json_url = $settings['icon_cdn'];
			}
		}
        $target_class = '';

        if ('custom' == $target ) {
            $target_class = $settings['custom_target'];
        } elseif ('column' == $target ) {
            $target_class = '.elementor-column';
        } elseif ('section' == $target ) {
            $target_class = '.elementor-section';
        } else {
            $target_class = '.ha-icon-box-icon';
        }
        $pulse_effect = ( 'yes' == $settings['pulse_effect'] ) ? ' pulse_effect' : '';

		?>

		<?php if ( $settings['badge_text'] ) : ?>
			<span <?php $this->print_render_attribute_string( 'badge_text' ); ?>><?php echo esc_html( $settings['badge_text'] ); ?></span>
		<?php endif; ?>

		<?php if ( 'lordicon' == $settings[ 'icon_type' ] ) :
		if ( ! empty( $json_url ) ) : ?>
			<span class="ha-icon-box-icon<?php echo esc_attr( $pulse_effect ); ?>">
				<lord-icon
					src="<?php echo esc_url( $json_url ); ?>"
					trigger="<?php echo esc_attr($settings['animation_trigger']); ?>"
					stroke="<?php echo esc_attr($icon_stroke['size']); ?>"
					target="<?php echo esc_attr($target_class); ?>"
					colors="primary:<?php echo esc_attr($primary_color); ?>,secondary:<?php echo esc_attr($secondary_color); ?>,tertiary:<?php echo esc_attr($tertiary_color); ?>,quaternary:<?php echo esc_attr($quaternary_color); ?>"
					style="width:<?php echo esc_attr($icon_size['size']); ?>px;height:<?php echo esc_attr($icon_size['size']); ?>px">
				</lord-icon>
			</span>
		<?php endif;
		else :
			if ( ! empty( $settings['icon'] ) || ! empty( $settings['selected_icon']['value'] ) ) : ?>
				<span class="ha-icon-box-icon">
					<?php ha_render_icon( $settings, 'icon', 'selected_icon' ); ?>
				</span>
		<?php endif;
		endif;

		if ( $settings['title' ] ) :
			printf( '<%1$s %2$s>%3$s</%1$s>',
				ha_escape_tags( $settings['title_tag'], 'h2' ),
				$this->get_render_attribute_string( 'title' ),
				ha_kses_basic( $settings['title' ] )
				);
		endif;
	}

	private function get_golobal_color($id) {
		$global_color = '';

		if( ! $id ) {
			return $global_color;
		}
		
		$el_page_settings 	= [];

		$ekit_id = get_option('elementor_active_kit', true);

		if ( $ekit_id ) {
			$el_page_settings = get_post_meta($ekit_id, '_elementor_page_settings', true);

			if( !empty( $el_page_settings ) && isset($el_page_settings['system_colors']) ) {
				foreach( $el_page_settings['system_colors'] as $key => $val ) {
					if( $val['_id'] == $id ) {
						$global_color = $val['color'];
					}
				}
			}

		}

		return $global_color;
	}

}
