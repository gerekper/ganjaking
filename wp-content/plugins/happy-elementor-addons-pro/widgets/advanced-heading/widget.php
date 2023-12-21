<?php
/**
 * Advanced Heading widget class
 *
 * @package Happy_Addons_Pro
 */
namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Happy_Addons\Elementor\Controls\Group_Control_Foreground;

defined( 'ABSPATH' ) || die();

class Advanced_Heading extends Base {

    /**
     * Get widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return __( 'Advanced Heading', 'happy-addons-pro' );
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
        return 'hm hm-advanced-heading';
    }

    public function get_keywords() {
        return [ 'gradient', 'advanced', 'heading', 'title' ];
    }

	/**
     * Register widget content controls
     */
    protected function register_content_controls() {

        $this->start_controls_section(
            '_section_title',
            [
                'label' => __( 'Advanced Heading', 'happy-addons-pro' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'heading_before',
            [
                'label' => __( 'Before Text', 'happy-addons-pro' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Happy', 'happy-addons-pro' ),
                'placeholder' => __( 'Before Text', 'happy-addons-pro' ),
				'dynamic' => [
					'active' => true,
				]
            ]
        );

        $this->add_control(
            'heading_center',
            [
                'label' => __( 'Center Text', 'happy-addons-pro' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Addon', 'happy-addons-pro' ),
                'placeholder' => __( 'Center Text', 'happy-addons-pro' ),
				'dynamic' => [
					'active' => true,
				]
            ]
        );

        $this->add_control(
            'heading_after',
            [
                'label' => __( 'After Text', 'happy-addons-pro' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Rocks', 'happy-addons-pro' ),
                'placeholder' => __( 'After Text', 'happy-addons-pro' ),
				'dynamic' => [
					'active' => true,
				]
            ]
        );

		$this->add_control(
			'show_background_text',
			[
				'label' => __( 'Background Text', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'happy-addons-pro' ),
				'label_off' => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'no',
                'style_transfer' => true,
			]
		);

		$this->add_control(
			'background_text',
			[
				'label' => __( 'Text', 'happy-addons-pro' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Background', 'happy-addons-pro' ),
				'placeholder' => __( 'Background Text', 'happy-addons-pro' ),
				'condition' => [
					'show_background_text' => 'yes'
				],
				'dynamic' => [
					'active' => true,
				]
			]
		);

        $this->add_control(
            'link',
            [
                'label' => __( 'Link', 'happy-addons-pro' ),
                'type' => Controls_Manager::URL,
                'placeholder' => __( 'https://example.com/', 'happy-addons-pro' ),
				'separator' => 'after',
				'dynamic' => [
					'active' => true,
				]
            ]
        );

        $this->add_control(
            'title_tag',
            [
                'label' => __( 'HTML Tag', 'happy-addons-pro' ),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'h2',
                'options' => [
                    'h1'  => [
                        'title' => __( 'H1', 'happy-addons-pro' ),
                        'icon' => 'eicon-editor-h1'
                    ],
                    'h2'  => [
                        'title' => __( 'H2', 'happy-addons-pro' ),
                        'icon' => 'eicon-editor-h2'
                    ],
                    'h3'  => [
                        'title' => __( 'H3', 'happy-addons-pro' ),
                        'icon' => 'eicon-editor-h3'
                    ],
                    'h4'  => [
                        'title' => __( 'H4', 'happy-addons-pro' ),
                        'icon' => 'eicon-editor-h4'
                    ],
                    'h5'  => [
                        'title' => __( 'H5', 'happy-addons-pro' ),
                        'icon' => 'eicon-editor-h5'
                    ],
                    'h6'  => [
                        'title' => __( 'H6', 'happy-addons-pro' ),
                        'icon' => 'eicon-editor-h6'
                    ]
                ],
                'toggle' => false,
            ]
        );

        $this->add_responsive_control(
            'heading_align',
            [
                'label' => __( 'Alignment', 'happy-addons-pro' ),
                'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-right',
					]
				],
                'default' => 'left',
                'toggle' => false,
                'prefix_class' => 'ha-align-',
                'selectors_dictionary' => [
                    'left' => 'justify-content: flex-start',
                    'center' => 'justify-content: center',
                    'right' => 'justify-content: flex-end',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-heading-tag' => '{{VALUE}}'
                ]
            ]
        );

        $this->add_responsive_control(
            'heading_position',
            [
                'label' => __( 'Layout', 'happy-addons-pro' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'inline' => [
                        'title' => __( 'Inline', 'happy-addons-pro' ),
                        'icon' => 'eicon-ellipsis-h',
                    ],
                    'block' => [
                        'title' => __( 'Block', 'happy-addons-pro' ),
                        'icon' => 'eicon-menu-bar',
                    ]
                ],
                'toggle' => false,
                'selectors_dictionary' => [
                    'inline' => 'flex-direction: row',
                    'block' => 'flex-direction: column',
                ],
                'default' => 'inline',
                'prefix_class' => 'ha-layout-',
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-heading-wrap' => '{{VALUE}}',
                ]
            ]
        );

        $this->end_controls_section();

    }

	/**
     * Register widget style controls
     */
    protected function register_style_controls() {
		$this->__before_text_style_controls();
		$this->__center_text_style_controls();
		$this->__after_text_style_controls();
		$this->__border_style_controls();
		$this->__background_style_controls();
	}

    protected function __before_text_style_controls() {

        $this->start_controls_section(
            '_section_before_text',
            [
                'label' => __( 'Before Text', 'happy-addons-pro' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'before_text_padding',
            [
                'label' => __( 'Padding', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-heading-before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'before_text_spacing',
            [
                'label' => __( 'Spacing', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}}.ha-layout-inline .ha-advanced-heading-before' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.ha-layout-block .ha-advanced-heading-before' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'before_text_border',
                'selector' => '{{WRAPPER}} .ha-advanced-heading-before',
            ]
        );

        $this->add_control(
            'before_text_border_radius',
            [
                'label' => __( 'Border Radius', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-heading-before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'before_text_typography',
                'selector' => '{{WRAPPER}} .ha-advanced-heading-before',
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'before_text_shadow',
                'label' => __( 'Text Shadow', 'happy-addons-pro' ),
                'selector' => '{{WRAPPER}} .ha-advanced-heading-before',
            ]
        );

        $this->add_group_control(
            Group_Control_Foreground::get_type(),
            [
                'name' => 'before_text_gradient',
                'selector' => '{{WRAPPER}} .ha-advanced-heading-before',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'before_text_background',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'condition' => [
                    'before_text_gradient_color_type' => 'classic'
                ],
                'selector' => '{{WRAPPER}} .ha-advanced-heading-before',
            ]
        );

        $this->add_control(
            'before_text_blend_mode',
            [
                'label' => __( 'Blend Mode', 'happy-addons-pro' ),
                'type' => Controls_Manager::SELECT,
                'separator' => 'none',
                'options' => [
                    '' => __( 'Normal', 'happy-addons-pro' ),
                    'multiply' => 'Multiply',
                    'screen' => 'Screen',
                    'overlay' => 'Overlay',
                    'darken' => 'Darken',
                    'lighten' => 'Lighten',
                    'color-dodge' => 'Color Dodge',
                    'saturation' => 'Saturation',
                    'color' => 'Color',
                    'difference' => 'Difference',
                    'exclusion' => 'Exclusion',
                    'hue' => 'Hue',
                    'luminosity' => 'Luminosity',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-heading-before' => 'mix-blend-mode: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
	}

    protected function __center_text_style_controls() {

        $this->start_controls_section(
            '_section_center_text',
            [
                'label' => __( 'Center Text', 'happy-addons-pro' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'center_text_padding',
            [
                'label' => __( 'Padding', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-heading-center' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'center_text_spacing',
            [
                'label' => __( 'Spacing', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}}.ha-layout-inline .ha-advanced-heading-center' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.ha-layout-block .ha-advanced-heading-center' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'center_text_border',
                'selector' => '{{WRAPPER}} .ha-advanced-heading-center',
            ]
        );

        $this->add_control(
            'center_text_border_radius',
            [
                'label' => __( 'Border Radius', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-heading-center' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'center_text_typography',
                'selector' => '{{WRAPPER}} .ha-advanced-heading-center',
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'center_text_shadow',
                'label' => __( 'Text Shadow', 'happy-addons-pro' ),
                'selector' => '{{WRAPPER}} .ha-advanced-heading-center',
            ]
        );

        $this->add_group_control(
            Group_Control_Foreground::get_type(),
            [
                'name' => 'center_text_gradient',
                'selector' => '{{WRAPPER}} .ha-advanced-heading-center',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'center_text_background',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'condition' => [
                    'center_text_gradient_color_type' => 'classic'
                ],
                'selector' => '{{WRAPPER}} .ha-advanced-heading-center',
            ]
        );

        $this->add_control(
            'center_text_blend_mode',
            [
                'label' => __( 'Blend Mode', 'happy-addons-pro' ),
                'type' => Controls_Manager::SELECT,
                'separator' => 'none',
                'options' => [
                    '' => __( 'Normal', 'happy-addons-pro' ),
                    'multiply' => 'Multiply',
                    'screen' => 'Screen',
                    'overlay' => 'Overlay',
                    'darken' => 'Darken',
                    'lighten' => 'Lighten',
                    'color-dodge' => 'Color Dodge',
                    'saturation' => 'Saturation',
                    'color' => 'Color',
                    'difference' => 'Difference',
                    'exclusion' => 'Exclusion',
                    'hue' => 'Hue',
                    'luminosity' => 'Luminosity',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-heading-center' => 'mix-blend-mode: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
	}

    protected function __after_text_style_controls() {

        $this->start_controls_section(
            '_section_after_text',
            [
                'label' => __( 'After Text', 'happy-addons-pro' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'after_text_padding',
            [
                'label' => __( 'Padding', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-heading-after' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'after_text_spacing',
            [
                'label' => __( 'Spacing', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}}.ha-layout-inline .ha-advanced-heading-after' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.ha-layout-block .ha-advanced-heading-after' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'after_text_border',
                'selector' => '{{WRAPPER}} .ha-advanced-heading-after',
            ]
        );

        $this->add_control(
            'after_text_border_radius',
            [
                'label' => __( 'Border Radius', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-heading-after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'after_text_typography',
                'selector' => '{{WRAPPER}} .ha-advanced-heading-after',
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'after_text_shadow',
                'label' => __( 'Text Shadow', 'happy-addons-pro' ),
                'selector' => '{{WRAPPER}} .ha-advanced-heading-after',
            ]
        );

        $this->add_group_control(
            Group_Control_Foreground::get_type(),
            [
                'name' => 'after_text_gradient',
                'selector' => '{{WRAPPER}} .ha-advanced-heading-after',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'after_text_background',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'condition' => [
                    'after_text_gradient_color_type' => 'classic'
                ],
                'selector' => '{{WRAPPER}} .ha-advanced-heading-after',
            ]
        );

        $this->add_control(
            'after_text_blend_mode',
            [
                'label' => __( 'Blend Mode', 'happy-addons-pro' ),
                'type' => Controls_Manager::SELECT,
                'separator' => 'none',
                'options' => [
                    '' => __( 'Normal', 'happy-addons-pro' ),
                    'multiply' => 'Multiply',
                    'screen' => 'Screen',
                    'overlay' => 'Overlay',
                    'darken' => 'Darken',
                    'lighten' => 'Lighten',
                    'color-dodge' => 'Color Dodge',
                    'saturation' => 'Saturation',
                    'color' => 'Color',
                    'difference' => 'Difference',
                    'exclusion' => 'Exclusion',
                    'hue' => 'Hue',
                    'luminosity' => 'Luminosity',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-heading-after' => 'mix-blend-mode: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
	}

    protected function __border_style_controls() {

        $this->start_controls_section(
            '_section_style_border',
            [
                'label' => __( 'Border', 'happy-addons-pro' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'border_type',
            [
                'label' => __( 'Border Type', 'happy-addons-pro' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'none' => __( 'None', 'happy-addons-pro' ),
                    'solid' => __( 'Solid', 'happy-addons-pro' ),
                    'double' => __( 'Double', 'happy-addons-pro' ),
                    'dotted' => __( 'Dotted', 'happy-addons-pro' ),
                    'dashed' => __( 'Dashed', 'happy-addons-pro' ),
                    'groove' => __( 'Groove', 'happy-addons-pro' ),
                ],
                'default' => 'solid',
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-heading-border:after' => 'border-bottom-style: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'border_length',
            [
                'label' => __( 'Length', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 800,
                    ],
                ],
                'condition' => [
                    'border_type!' => 'none',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-heading-border:after' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'border_width',
            [
                'label' => __( 'Width', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => 'px',
                    'size' => 3
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 20,
                    ],
                ],
                'condition' => [
                    'border_type!' => 'none',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-heading-border:after' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'border_color',
            [
                'label' => __( 'Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'border_type!' => 'none',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-heading-border:after' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'border_radius',
            [
                'label' => __( 'Border radius', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'condition' => [
                    'border_type!' => 'none',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-heading-border:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ],
            ]
        );

        $this->add_control(
            'border_offset_toggle',
            [
                'label' => __( 'Offset', 'happy-addons-pro' ),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'label_off' => __( 'None', 'happy-addons-pro' ),
                'label_on' => __( 'Custom', 'happy-addons-pro' ),
                'return_value' => 'yes',
                'condition' => [
                    'border_type!' => 'none',
                ],
            ]
        );

        $this->start_popover();

        $this->add_responsive_control(
            'border_horizontal_position',
            [
                'label' => __( 'Horizontal Position', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'default' => [
                    'unit' => '%',
                ],
                'range' => [
                    '%' => [
                        'min' => -20,
                        'max' => 100,
                    ],
                ],
                'condition' => [
                   'border_offset_toggle' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-heading-border:after' => 'left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'border_vertical_position',
            [
                'label' => __( 'Vertical Position', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'default' => [
                    'unit' => '%',
                ],
                'range' => [
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'condition' => [
                    'border_offset_toggle' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-heading-border:after' => 'bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_popover();

        $this->end_controls_section();
	}

    protected function __background_style_controls() {

        $this->start_controls_section(
            '_section_style_background',
            [
                'label' => __( 'Background Text', 'happy-addons-pro' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'background_note',
            [
                'label' => false,
                'type' => Controls_Manager::RAW_HTML,
                'raw' => __( '<strong>Background Text</strong> is Hidden on Content Tab', 'happy-addons-pro' ),
                'condition' => [
                    'show_background_text!' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'background_text_color',
            [
                'label' => __( 'Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'show_background_text' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-heading-wrap:before' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'background_text_typography',
                'selector' => '{{WRAPPER}} .ha-advanced-heading-wrap:before',
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
                'condition' => [
                    'show_background_text' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'background_offset_toggle',
            [
                'label' => __( 'Offset', 'happy-addons-pro' ),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'label_off' => __( 'None', 'happy-addons-pro' ),
                'label_on' => __( 'Custom', 'happy-addons-pro' ),
                'return_value' => 'yes',
                'condition' => [
                    'show_background_text' => 'yes',
                ],
            ]
        );

        $this->start_popover();

        $this->add_responsive_control(
            'background_horizontal_position',
            [
                'label' => __( 'Horizontal Position', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'default' => [
                    'unit' => '%',
                ],
                'range' => [
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'condition' => [
                    'background_offset_toggle' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-heading-wrap:before' => 'left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'background_vertical_position',
            [
                'label' => __( 'Vertical Position', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'default' => [
                    'unit' => '%',
                ],
                'range' => [
                    '%' => [
                        'min' => -100,
                        'max' => 200,
                    ],
                ],
                'condition' => [
                    'background_offset_toggle' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-heading-wrap:before' => 'top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_popover();

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
		$has_link = false;

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'link', $settings['link'] );
			$has_link = true;
		}
		?>

		<<?php echo ha_escape_tags($settings['title_tag']); ?> class="ha-advanced-heading-tag">
			<?php if ( $has_link ) : ?>
			<a <?php $this->print_render_attribute_string( 'link' ) ?>>
			<?php endif; ?>
			<div class="ha-advanced-heading-wrap" data-background-text="<?php echo esc_attr( strip_tags( $settings['background_text'] ) ); ?>">
				<span class="ha-advanced-heading-before"><?php echo ha_kses_basic( $settings[ 'heading_before' ] ); ?></span>
				<span class="ha-advanced-heading-center"><?php echo ha_kses_basic( $settings[ 'heading_center' ] ); ?></span>
				<span class="ha-advanced-heading-after"><?php echo ha_kses_basic( $settings[ 'heading_after' ] ); ?></span>
				<span class="ha-advanced-heading-border"></span>
			</div>
			<?php if ( $has_link ) : ?>
			</a>
			<?php endif; ?>
		</<?php echo ha_escape_tags( $settings['title_tag'] ); ?>>

		<?php
    }
}
