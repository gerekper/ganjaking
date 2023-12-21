<?php

/**
 * Member widget class
 *
 * @package Happy_Addons
 */

namespace Happy_Addons\Elementor\Widget;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

defined('ABSPATH') || die();

class Image_Accordion extends Base {

    /**
     * Get widget title.
     *
     * @since 2.24.1
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return __('Image Accordion', 'happy-elementor-addons');
    }

    // public function __construct($data = [], $args = null) {
    // 	parent::__construct($data, $args);
    // }

    /**
     * Get widget icon.
     *
     * @since 2.24.1
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'hm hm-slider-image';
    }

    public function get_keywords() {
        return ['image', 'accordion', 'image accordion'];
    }

    protected function content_common() {
        $this->start_controls_section(
            '_section_content',
            [
                'label' => __('Content', 'happy-elementor-addons'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'background_image',
            [
                'label' => __('Choose Image', 'happy-elementor-addons'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'label',
            [
                'label'   => __('Label', 'happy-elementor-addons'),
                'type'    => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __('Accordion Label', 'happy-elementor-addons'),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'title',
            [
                'label'   => __('Title', 'happy-elementor-addons'),
                'type'    => Controls_Manager::TEXTAREA,
                'rows' => 5,
                'label_block' => true,
                'default' => __('Image Accordion', 'happy-elementor-addons'),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'icon',
            [
                'label' => __('Title Icon', 'happy-elementor-addons'),
                'type'  => Controls_Manager::ICONS,
                'label_block' => false,
                'skin' => 'inline',
                'exclude_inline_options' => ['svg'],
            ]
        );

        $repeater->add_control(
            'icon_align',
            [
                'label'   => __('Icon Position', 'happy-elementor-addons'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'left',
                'options' => [
                    'left'  => __('Left', 'happy-elementor-addons'),
                    'right' => __('Right', 'happy-elementor-addons'),
                ],
            ]
        );

        $repeater->add_control(
            'description',
            [
                'label' => __('Description', 'happy-elementor-addons'),
                'type' => Controls_Manager::TEXTAREA,
                'description' => ha_get_allowed_html_desc( 'intermediate' ),
                'default' => __('Image accordion content.', 'happy-elementor-addons'),
                'placeholder' => __('Type your description here', 'happy-elementor-addons'),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'enable_button',
            [
                'label'        => __('Enable Button', 'happy-elementor-addons'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __('Yes', 'happy-elementor-addons'),
                'label_off'    => __('No', 'happy-elementor-addons'),
                'return_value' => 'yes',
                'default'      => 'no',
                'separator'    => 'before'
            ]
        );

        $repeater->add_control(
            'button_label',
            [
                'label'   => __('Button Label', 'happy-elementor-addons'),
                'type'    => Controls_Manager::TEXT,
                'default' => __('Read More', 'happy-elementor-addons'),
                'condition' => [
                    'enable_button' => 'yes',
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'button_url',
            [
                'label'   => __('Button URL', 'happy-elementor-addons'),
                'type'    => Controls_Manager::URL,
                'condition' => [
                    'enable_button' => 'yes',
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'enable_popup',
            [
                'label'        => __('Enable Popup', 'happy-elementor-addons'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __('Yes', 'happy-elementor-addons'),
                'label_off'    => __('No', 'happy-elementor-addons'),
                'return_value' => 'yes',
                'default'      => 'no',
                'separator'    => 'before'
            ]
        );

        $repeater->add_control(
            'popup_icon',
            [
                'label' => __('Popup Icon', 'happy-elementor-addons'),
                'type'  => Controls_Manager::ICONS,
                'label_block' => false,
                'default' => [
                    'value' => 'hm hm-popup',
                    'library' => 'solid',
                ],
                'skin' => 'inline',
                'exclude_inline_options' => ['svg'],
                'condition' => [
                    'enable_popup' => 'yes',
                ],
            ]
        );

        $repeater->add_control(
            'enable_link',
            [
                'label'        => __('Enable Link', 'happy-elementor-addons'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __('Yes', 'happy-elementor-addons'),
                'label_off'    => __('No', 'happy-elementor-addons'),
                'return_value' => 'yes',
                'default'      => 'no',
                'separator'    => 'before'
            ]
        );

        $repeater->add_control(
            'link_url',
            [
                'label'   => __('Link URL', 'happy-elementor-addons'),
                'type'    => Controls_Manager::URL,
                'condition' => [
                    'enable_link' => 'yes',
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'link_icon',
            [
                'label' => __('Link Icon', 'happy-elementor-addons'),
                'type'  => Controls_Manager::ICONS,
                'label_block' => false,
                'default' => [
                    'value' => 'hm hm-link',
                    'library' => 'solid',
                ],
                'skin' => 'inline',
                'exclude_inline_options' => ['svg'],
                'condition' => [
                    'enable_link' => 'yes',
                ],
            ]
        );

        $repeater->add_control(
            'active',
            [
                'label'        => __('Active', 'happy-elementor-addons'),
                'type'         => Controls_Manager::SWITCHER,
                'description'  => __('Active on Load', 'happy-elementor-addons'),
                'label_on'     => __('Yes', 'happy-elementor-addons'),
                'label_off'    => __('No', 'happy-elementor-addons'),
                'return_value' => 'yes',
                'default'      => 'no',
                'separator'    => 'before'
            ]
        );

        $this->add_control(
            'accordion_items',
            [
                'label'         => __('Items', 'happy-elementor-addons'),
                'type'          => Controls_Manager::REPEATER,
                'fields'        => $repeater->get_controls(),
                'prevent_empty' => true,
                'default'       => [
                    [
                        'label'         => __('Accordion Label', 'happy-elementor-addons'),
                        'title'         => __('Image Accordion 1', 'happy-elementor-addons'),
                        'description'         => __('Image accordion content.', 'happy-elementor-addons'),
                        'enable_button'  => 'yes',
                        'active'        => 'yes',
                    ],
                    [
                        'label'         => __('Accordion Label', 'happy-elementor-addons'),
                        'title'         => __('Image Accordion 2', 'happy-elementor-addons'),
                        'description'         => __('Image accordion content.', 'happy-elementor-addons'),
                        'enable_button'  => 'yes',
                    ],
                    [
                        'label'         => __('Accordion Label', 'happy-elementor-addons'),
                        'title'         => __('Image Accordion 3', 'happy-elementor-addons'),
                        'description'         => __('Image accordion content.', 'happy-elementor-addons'),
                        'enable_button'  => 'yes',
                    ],
                    [
                        'label'         => __('Accordion Label', 'happy-elementor-addons'),
                        'title'         => __('Image Accordion 4', 'happy-elementor-addons'),
                        'description'         => __('Image accordion content.', 'happy-elementor-addons'),
                        'enable_button'  => 'yes',
                    ],
                ],
                'title_field'   => '{{{ title }}}',
            ]
        );

        $this->add_responsive_control(
            'items_style',
            [
                'label'         => esc_html__('Style', 'happy-elementor-addons'),
                'type'          => Controls_Manager::SELECT,
                'options'       => [
                    'horizontal'    => esc_html__('Horizontal', 'happy-elementor-addons'),
                    'vertical'      => esc_html__('Vertical', 'happy-elementor-addons'),
                ],
                'default'       => 'horizontal',
                'prefix_class'  => 'ha-image-accordion%s-',
            ]
        );

        $this->add_control(
            'active_behavior',
            [
                'label'         => esc_html__('Active Behavior', 'happy-elementor-addons'),
                'type'          => Controls_Manager::SELECT,
                'options'       => [
                    'click' => esc_html__('Click', 'happy-elementor-addons'),
                    'hover' => esc_html__('Hover', 'happy-elementor-addons'),
                ],
                'default'       => 'click',
                'prefix_class'  => 'ha-image-accordion-',
            ]
        );

        $this->add_control(
            'active_behavior_notice',
            [
                'raw' => '<strong>' . esc_html__('Please note!', 'happy-elementor-addons') . '</strong> ' . esc_html__('Active on load won\'t be working with this active behavior.', 'happy-elementor-addons'),
                'type' => Controls_Manager::RAW_HTML,
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
                'render_type' => 'ui',
                'condition' => [
                    'active_behavior' => 'hover',
                ],
            ]
        );

        $this->add_control(
            'content_text_align',
            [
                'label' => __('Text Align', 'happy-elementor-addons'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __('Left', 'happy-elementor-addons'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'happy-elementor-addons'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'flex-end' => [
                        'title' => __('Right', 'happy-elementor-addons'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-content-wrapper' => 'align-items: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'content_h_align',
            [
                'label' => __('Horizontal Align', 'happy-elementor-addons'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __('Left', 'happy-elementor-addons'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'happy-elementor-addons'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'flex-end' => [
                        'title' => __('Right', 'happy-elementor-addons'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => 'center',
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .ha-overlay' => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'content_v_align',
            [
                'label' => __('Vertical Align', 'happy-elementor-addons'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __('Top', 'happy-elementor-addons'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'center' => [
                        'title' => __('Center', 'happy-elementor-addons'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'flex-end' => [
                        'title' => __('Bottom', 'happy-elementor-addons'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'default' => 'center',
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .ha-overlay' => 'align-items: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'content_animation',
            [
                'label'        => __('Enable Content Animation?', 'happy-elementor-addons'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __('Yes', 'happy-elementor-addons'),
                'label_off'    => __('No', 'happy-elementor-addons'),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Register content related controls
     */
    protected function register_content_controls() {
        $this->content_common();
    }

    protected function style_common() {
        $this->start_controls_section(
            '_section_common',
            [
                'label' => __('Common', 'happy-elementor-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'common_height',
            [
                'label' => __('Height', 'happy-elementor-addons'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vh'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 5,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'vh' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 500,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-gallery-wrap' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'common_margin',
            [
                'label' => __('Margin', 'happy-elementor-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'common_padding',
            [
                'label' => __('Padding', 'happy-elementor-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-gallery-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'common_border',
                'label' => __('Border', 'happy-elementor-addons'),
                'selector' => '{{WRAPPER}} .ha-ia-gallery-wrap',
            ]
        );

        $this->add_responsive_control(
            'common_border_radius',
            [
                'label' => __('Border Radius', 'happy-elementor-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-gallery-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'common_box_shadow',
                'label' => __('Box Shadow', 'happy-elementor-addons'),
                'selector' => '{{WRAPPER}} .ha-ia-gallery-wrap',
            ]
        );

        $this->add_control(
            'common_background_color',
            [
                'label' => __('Background Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-gallery-wrap' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
			'common_image_control_heading',
			[
				'label' => __( 'Image Controls', 'happy-elementor-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_responsive_control(
            'image_gutter',
            [
                'label' => __('Gutter', 'happy-elementor-addons'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 16,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
                'selectors' => [
                    '{{WRAPPER}}.ha-image-accordion-horizontal .ha-ia-item' => '--ha-ia-gutter-margin: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.ha-image-accordion-tablet-horizontal .ha-ia-item' => '--ha-ia-gutter-margin: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.ha-image-accordion-mobile-horizontal .ha-ia-item' => '--ha-ia-gutter-margin: {{SIZE}}{{UNIT}};',

                    '{{WRAPPER}}.ha-image-accordion-vertical .ha-ia-item' => '--ha-ia-gutter-margin: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.ha-image-accordion-tablet-vertical .ha-ia-item' => '--ha-ia-gutter-margin: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.ha-image-accordion-mobile-vertical .ha-ia-item' => '--ha-ia-gutter-margin: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_radius',
            [
                'label' => __('Radius', 'happy-elementor-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-gallery-wrap .ha-ia-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
			'common_overlay_color_heading',
			[
				'label' => __( 'Overlay Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::HEADING,
			]
		);

        $this->start_controls_tabs('common_color');

        $this->start_controls_tab(
            'common_normal',
            [
                'label' => __('Normal', 'happy-elementor-addons'),

            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'common_overlay_color',
				'label' => __( 'Overlay Color', 'happy-elementor-addons' ),
				'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .ha-ia-item .ha-overlay',
			]
		);

        // $this->add_control(
        //     'common_overlay_color',
        //     [
        //         'label' => __('Overlay Color', 'happy-elementor-addons'),
        //         'type' => Controls_Manager::COLOR,
        //         'selectors' => [
        //             '{{WRAPPER}} .ha-ia-item .ha-overlay' => 'background-color: {{VALUE}}',
        //         ],
        //     ]
        // );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'common_hover',
            [
                'label' => __('Hover', 'happy-elementor-addons'),

            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'common_overlay_color_hover',
				'label' => __( 'Overlay Color', 'happy-elementor-addons' ),
				'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .ha-ia-item:hover .ha-overlay',
			]
		);

        // $this->add_control(
        //     'common_overlay_color_hover',
        //     [
        //         'label' => __('Overlay Color', 'happy-elementor-addons'),
        //         'type' => Controls_Manager::COLOR,
        //         'selectors' => [
        //             '{{WRAPPER}} .ha-ia-item:hover .ha-overlay' => 'background-color: {{VALUE}}',
        //         ],
        //     ]
        // );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'common_active',
            [
                'label' => __('Active', 'happy-elementor-addons'),

            ]
        );


        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'common_overlay_color_active',
				'label' => __( 'Overlay Color', 'happy-elementor-addons' ),
				'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .ha-ia-item.active .ha-overlay',
			]
		);

        // $this->add_control(
        //     'common_overlay_color_active',
        //     [
        //         'label' => __('Overlay Color', 'happy-elementor-addons'),
        //         'type' => Controls_Manager::COLOR,
        //         'selectors' => [
        //             '{{WRAPPER}} .ha-ia-item.active .ha-overlay' => 'background-color: {{VALUE}}',
        //         ],
        //     ]
        // );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function style_title() {
        $this->start_controls_section(
            '_section_style_title',
            [
                'label' => __('Title', 'happy-elementor-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label' => __('Margin', 'happy-elementor-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-content-icon-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-content-icon-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => __('Typography', 'happy-elementor-addons'),
                'fields_options' => [
                    'typography' => [
                        'default' => 'yes'
                    ],
                    'font_family' => [
                        'default' => 'Nunito',
                    ],
                    'font_weight' => [
                        'default' => 'bold', // 100, 200, 300, 400, 500, 600, 700, 800, 900, normal, bold
                    ],
                ],
                'selector' => '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-content-icon-title',
            ]
        );

        $this->add_responsive_control(
            'space_between_title_icon',
            [
                'label' => __('Space Between Title Icon', 'happy-elementor-addons'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-content-icon-title.ha-ia-icon-left i + span' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-content-icon-title.ha-ia-icon-right i + span' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function style_content() {

        $this->start_controls_section(
            '_section_style_content',
            [
                'label' => __('Content', 'happy-elementor-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'content_margin',
            [
                'label' => __('Margin', 'happy-elementor-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-container .ha-ia-content-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_padding',
            [
                'label' => __('Padding', 'happy-elementor-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => '15',
                    'right' => '15',
                    'bottom' => '15',
                    'left' => '15',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-container .ha-ia-content-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'label_heading',
            [
                'label' => __('Label', 'happy-elementor-addons'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'label_margin',
            [
                'label' => __('Margin', 'happy-elementor-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-container .ha-ia-content-wrapper .ha-ia-content-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'label_color',
            [
                'label' => __('Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-container .ha-ia-content-wrapper .ha-ia-content-label' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'label_typography',
                'label' => __('Typography', 'happy-elementor-addons'),
                'fields_options' => [
                    'typography' => [
                        'default' => 'yes'
                    ],
                    'font_family' => [
                        'default' => 'Nunito',
                    ],
                    'font_weight' => [
                        'default' => '400', // 100, 200, 300, 400, 500, 600, 700, 800, 900, normal, bold
                    ],
                ],
                'selector' => '{{WRAPPER}} .ha-ia-container .ha-ia-content-wrapper .ha-ia-content-label',
            ]
        );

        $this->add_control(
            'description_heading',
            [
                'label' => __('Description', 'happy-elementor-addons'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'description_margin',
            [
                'label' => __('Margin', 'happy-elementor-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-container .ha-ia-content-wrapper .ha-ia-content-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->add_control(
            'description_color',
            [
                'label' => __('Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-container .ha-ia-content-wrapper .ha-ia-content-description' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'label' => __('Typography', 'happy-elementor-addons'),
                'fields_options' => [
                    'typography' => [
                        'default' => 'yes'
                    ],
                    'font_family' => [
                        'default' => 'Nunito',
                    ],
                    'font_weight' => [
                        'default' => '500', // 100, 200, 300, 400, 500, 600, 700, 800, 900, normal, bold
                    ],
                ],
                'selector' => '{{WRAPPER}} .ha-ia-container .ha-ia-content-wrapper .ha-ia-content-description',
            ]
        );

        $this->end_controls_section();
    }

    protected function style_button() {
        $this->start_controls_section(
            '_section_style_button',
            [
                'label' => __('Button', 'happy-elementor-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => __('Padding', 'happy-elementor-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => '8',
                    'right' => '15',
                    'bottom' => '8',
                    'left' => '15',
                    'isLinked' => false,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-content-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'label' => __('Typography', 'happy-elementor-addons'),
                'fields_options' => [
                    'typography' => [
                        'default' => 'yes'
                    ],
                    'font_family' => [
                        'default' => 'Nunito',
                    ],
                    'font_weight' => [
                        'default' => '400', // 100, 200, 300, 400, 500, 600, 700, 800, 900, normal, bold
                    ],
                ],
                'selector' => '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-content-button',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'label' => __('Border', 'happy-elementor-addons'),
                'selector' => '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-content-button',
            ]
        );

        $this->add_responsive_control(
            'button_border_radius',
            [
                'label' => __('Border Radius', 'happy-elementor-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-content-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('button_color_tab');

        $this->start_controls_tab(
            'button_normal',
            [
                'label' => __('Normal', 'happy-elementor-addons'),

            ]
        );

        $this->add_control(
            'button_color',
            [
                'label' => __('Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-content-button' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'button_background',
                'label' => __('Background', 'happy-elementor-addons'),
                'types' => ['classic', 'gradient'],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-content-button',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'button_hover',
            [
                'label' => __('Hover', 'happy-elementor-addons'),

            ]
        );

        $this->add_control(
            'button_color_hover',
            [
                'label' => __('Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-content-button:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'button_background_hover',
                'label' => __('Background', 'happy-elementor-addons'),
                'types' => ['classic', 'gradient'],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-content-button:hover',
            ]
        );

        $this->add_control(
            'button_border_color_hover',
            [
                'label' => __('Border Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'button_border_border!' => ''
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-content-button:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function style_actions() {
        $this->start_controls_section(
            '_section_style_actions',
            [
                'label' => __('Actions', 'happy-elementor-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'action_margin',
            [
                'label' => __('Margin', 'happy-elementor-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => '',
                    'right' => '',
                    'bottom' => '10',
                    'left' => '',
                    'isLinked' => false,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-actions' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'action_icon_size',
            [
                'label' => __('Icon Size', 'happy-elementor-addons'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 22,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-actions .ha-ia-popup' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-actions .ha-ia-link' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'action_icon_space_between',
            [
                'label' => __('Space Between', 'happy-elementor-addons'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 15,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-actions .ha-ia-popup+.ha-ia-link' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'action_icon_border',
                'label' => __('Border', 'happy-elementor-addons'),
                'selector' => '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-actions span',
            ]
        );

        $this->add_responsive_control(
            'action_icon_border_radius',
            [
                'label' => __('Border Radius', 'happy-elementor-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => '100',
                    'right' => '100',
                    'bottom' => '100',
                    'left' => '100',
                    'unit' => '%',
                    'isLinked' => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-actions span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('action_icon_color_tab');

        $this->start_controls_tab(
            'action_icon_normal',
            [
                'label' => __('Normal', 'happy-elementor-addons'),

            ]
        );

        $this->add_control(
            'action_popup_icon_color',
            [
                'label' => __('Popup Icon Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-actions .ha-ia-popup i' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'action_popup_icon_background_color',
            [
                'label' => __('Popup Background Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-actions .ha-ia-popup' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'action_popup_icon_border_color',
            [
                'label' => __('Popup Border Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'action_icon_border_border!' => ''
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-actions .ha-ia-popup' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'action_link_icon_color',
            [
                'label' => __('Link Icon Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-actions .ha-ia-link i' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'action_link_icon_background_color',
            [
                'label' => __('Link Background Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-actions .ha-ia-link' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'action_link_icon_border_color',
            [
                'label' => __('Link Border Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'action_icon_border_border!' => ''
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-actions .ha-ia-link' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'action_icon_hover',
            [
                'label' => __('Hover', 'happy-elementor-addons'),

            ]
        );

        $this->add_control(
            'action_popup_icon_color_hover',
            [
                'label' => __('Popup Icon Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-actions .ha-ia-popup:hover i' => 'color: {{VALUE}}',
                ],
            ]
        );


        $this->add_control(
            'action_popup_icon_background_color_hover',
            [
                'label' => __('Popup Background Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-actions .ha-ia-popup:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'action_popup_icon_border_color_hover',
            [
                'label' => __('Popup Border Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'action_icon_border_border!' => ''
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-actions .ha-ia-popup:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'action_link_icon_color_hover',
            [
                'label' => __('Link Icon Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-actions .ha-ia-link:hover i' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'action_link_icon_background_color_hover',
            [
                'label' => __('Link Background Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-actions .ha-ia-link:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'action_link_icon_border_color_hover',
            [
                'label' => __('Link Border Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'action_icon_border_border!' => ''
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-ia-content-wrapper .ha-ia-actions .ha-ia-link:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    /**
     * Register styles related controls
     */
    protected function register_style_controls() {
        $this->style_common();
        $this->style_title();
        $this->style_content();
        $this->style_button();
        $this->style_actions();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $animation_class = ( ($settings['content_animation'] == 'yes')? 'ha_fadeInUp': 'ha_noAnimation' );
?>
        <div class="ha-image-accordion-wrapper">
            <div class="ha-ia-container">
                <div class="ha-ia-gallery-wrap">
                    <?php foreach ($settings['accordion_items'] as $inx => $item) : ?>
                        <div style="background-image: url('<?php echo esc_url($item['background_image']['url']); ?>');" class="ha-ia-item <?php echo esc_attr(($item['active'] == 'yes') ? 'active' : ''); ?>">
                            <div class="ha-overlay">
                                <div class="ha-ia-content-wrapper <?php echo esc_attr($animation_class); ?>">
                                    <?php if ($item['enable_popup'] == 'yes' || $item['enable_link'] == 'yes') : ?>
                                        <div class="ha-ia-actions">
                                            <?php if ($item['enable_popup'] == 'yes') : ?>
                                                <span class="ha-ia-popup">
                                                    <a href="<?php echo esc_url($item['background_image']['url']); ?>" data-elementor-open-lightbox="yes">
                                                        <?php ha_render_icon($item, null, 'popup_icon'); ?>
                                                    </a>
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($item['enable_link'] == 'yes') : ?>
                                                <span class="ha-ia-link">
                                                    <a href="<?php echo esc_url($item['link_url']['url']); ?>" <?php echo esc_attr($item['link_url']['is_external'] ? 'target=_blank' : ''); ?> <?php echo esc_attr($item['link_url']['nofollow'] ? 'rel=nofollow' : ''); ?>>
                                                        <?php ha_render_icon($item, null, 'link_icon'); ?>
                                                    </a>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($item['label'])) : ?>
                                        <span class="ha-ia-content-label"><?php echo esc_html($item['label']); ?></span>
                                    <?php endif; ?>
                                    <div class="ha-ia-content-icon-title ha-ia-icon-<?php echo esc_attr($item['icon_align']); ?>">
                                        <?php ha_render_icon($item, null, 'icon'); ?>
                                        <span class="ha-ia-content-title"><?php echo esc_html($item['title']); ?></span>
                                    </div>
                                    <?php if (!empty($item['description'])) :
                                        printf('<div class="ha-ia-content-description">%s</div>', ha_kses_intermediate( $item['description'] ));
                                    endif; ?>
                                    <?php if ($item['enable_button'] == 'yes') : ?>
                                        <a class="ha-ia-content-button" href="<?php echo esc_attr($item['button_url']['url']); ?>" <?php echo esc_attr($item['button_url']['is_external'] ? 'target=_blank' : ''); ?> <?php echo esc_attr($item['button_url']['nofollow'] ? 'rel=nofollow' : ''); ?>>
                                            <?php echo esc_html($item['button_label']); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
<?php
    }
}
