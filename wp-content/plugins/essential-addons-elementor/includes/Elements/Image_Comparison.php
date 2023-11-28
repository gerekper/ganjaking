<?php
namespace Essential_Addons_Elementor\Pro\Elements;

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use \Elementor\Utils;
use \Elementor\Widget_Base;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// If this file is called directly, abort.

class Image_Comparison extends Widget_Base {
    public function get_name() {
        return 'eael-image-comparison';
    }

    public function get_title() {
        return esc_html__( 'Image Comparison', 'essential-addons-elementor' );
    }

    public function get_icon() {
        return 'eaicon-image-comparison';
    }

    public function get_categories() {
        return ['essential-addons-elementor'];
    }

    public function get_keywords() {
        return [
            'image',
            'compare',
            'ea image compare',
            'ea image comparison',
            'table',
            'before after image',
            'before and after image',
            'before after slider',
            'ea',
            'essential addons',
        ];
    }

    public function get_custom_help_url() {
        return 'https://essential-addons.com/elementor/docs/image-comparison/';
    }

    protected function register_controls() {

        // Content Controls
        $this->start_controls_section(
            'eael_image_comparison_images',
            [
                'label' => esc_html__( 'Images', 'essential-addons-elementor' ),
            ]
        );

        $this->add_control(
            'before_image_label',
            [
                'label'       => __( 'Label Before', 'essential-addons-elementor' ),
                'type'        => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'label_block' => true,
                'default'     => 'Before',
                'title'       => __( 'Input before image label', 'essential-addons-elementor' ),
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'before_image',
            [
                'label'   => __( 'Choose Before Image', 'essential-addons-elementor' ),
                'type'    => Controls_Manager::MEDIA,
	            'dynamic' => [
		            'active' => true,
	            ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'ai' => [
                    'active' => false,
                ],
            ]
        );

        $this->add_control(
            'before_image_alt',
            [
                'label'       => __( 'Before Image ALT Tag', 'essential-addons-elementor' ),
                'type'        => Controls_Manager::TEXT,
                'dynamic' => [ 'active' => true ],
                'label_block' => true,
                'default'     => '',
                'placeholder' => __( 'Enter alter tag for the image', 'essential-addons-elementor' ),
                'title'       => __( 'Input image alter tag here', 'essential-addons-elementor' ),
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'after_image_label',
            [
                'label'       => __( 'Label After', 'essential-addons-elementor' ),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ],
                'default'     => 'After',
                'title'       => __( 'Input after image label', 'essential-addons-elementor' ),
                'ai' => [
					'active' => false,
				],
            ]
        );
        $this->add_control(
            'after_image',
            [
                'label'   => __( 'Choose After Image', 'essential-addons-elementor' ),
                'type'    => Controls_Manager::MEDIA,
	            'dynamic' => [
		            'active' => true,
	            ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'ai' => [
                    'active' => false,
                ],
            ]
        );

        $this->add_control(
            'after_image_alt',
            [
                'label'       => __( 'After Image ALT Tag', 'essential-addons-elementor' ),
                'type'        => Controls_Manager::TEXT,
                'dynamic' => [ 'active' => true ],
                'label_block' => true,
                'default'     => '',
                'placeholder' => __( 'Enter alter tag for the image', 'essential-addons-elementor' ),
                'title'       => __( 'Input image alter tag here', 'essential-addons-elementor' ),
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'eael_image_comparison_settings',
            [
                'label' => esc_html__( 'Settings', 'essential-addons-elementor' ),
            ]
        );

        $this->add_control(
            'eael_image_comp_offset',
            [
                'label'      => esc_html__( 'Original Image Visibility', 'essential-addons-elementor' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'range'      => ['%' => ['min' => 10, 'max' => 90]],
                'default'    => ['size' => 70, 'unit' => '%'],
            ]
        );

        $this->add_control(
            'eael_image_comp_orientation',
            [
                'label'   => esc_html__( 'Orientation', 'essential-addons-elementor' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'horizontal' => __( 'Horizontal', 'essential-addons-elementor' ),
                    'vertical'   => __( 'Vertical', 'essential-addons-elementor' ),
                ],
                'default' => 'horizontal',
            ]
        );

        $this->add_control(
            'eael_image_comp_overlay',
            [
                'label'     => esc_html__( 'Wants Overlay ?', 'essential-addons-elementor' ),
                'type'      => Controls_Manager::SWITCHER,
                'label_on'  => __( 'yes', 'essential-addons-elementor' ),
                'label_off' => __( 'no', 'essential-addons-elementor' ),
                'default'   => 'yes',
            ]
        );

        $this->add_control(
            'eael_image_comp_move',
            [
                'label'     => esc_html__( 'Move Slider On Hover', 'essential-addons-elementor' ),
                'type'      => Controls_Manager::SWITCHER,
                'label_on'  => __( 'yes', 'essential-addons-elementor' ),
                'label_off' => __( 'no', 'essential-addons-elementor' ),
                'default'   => 'no',
            ]
        );

        $this->add_control(
            'eael_image_comp_click',
            [
                'label'     => esc_html__( 'Move Slider On Click', 'essential-addons-elementor' ),
                'type'      => Controls_Manager::SWITCHER,
                'label_on'  => __( 'yes', 'essential-addons-elementor' ),
                'label_off' => __( 'no', 'essential-addons-elementor' ),
                'default'   => 'no',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'eael_image_comparison_styles',
            [
                'label' => esc_html__( 'Image Container Styles', 'essential-addons-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'eael_image_container_width',
            [
                'label'     => esc_html__( 'Set max width for the container?', 'essential-addons-elementor' ),
                'type'      => Controls_Manager::SWITCHER,
                'label_on'  => __( 'yes', 'essential-addons-elementor' ),
                'label_off' => __( 'no', 'essential-addons-elementor' ),
                'default'   => 'yes',
            ]
        );

        $this->add_responsive_control(
            'eael_image_container_width_value',
            [
                'label'      => __( 'Container Max Width', 'essential-addons-elementor' ),
                'type'       => Controls_Manager::SLIDER,
                'default'    => [
                    'size' => 80,
                    'unit' => '%',
                ],
                'size_units' => ['%', 'px'],
                'range'      => [
                    '%'  => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .eael-img-comp-container' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
                'condition'  => [
                    'eael_image_container_width' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'eael_img_comp_border',
                'selector' => '{{WRAPPER}} .eael-img-comp-container',
            ]
        );

        $this->add_control(
            'eael_img_comp_border_radius',
            [
                'label'     => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-img-comp-container' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        /**
         * Style tab: overlay background
         */

        $this->start_controls_section(
            'section_overlay_style',
            [
                'label'     => __( 'Overlay', 'essential-addons-elementor' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'eael_image_comp_overlay' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name'     => 'eael_img_cmp_overlay_background',
                'label'    => __( 'Background', 'essential-addons-elementor' ),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .eael-img-comp-container .twentytwenty-overlay:hover',
            ]
        );

        $this->end_controls_section();

        /**
         * Style Tab: Handle
         */
        $this->start_controls_section(
            'section_handle_style',
            [
                'label' => __( 'Handle', 'essential-addons-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs( 'tabs_handle_style' );

        $this->start_controls_tab(
            'tab_handle_normal',
            [
                'label' => __( 'Normal', 'essential-addons-elementor' ),
            ]
        );

        $this->add_control(
            'handle_icon_color',
            [
                'label'     => __( 'Icon Color', 'essential-addons-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
	                '{{WRAPPER}} .twentytwenty-left-arrow'  => 'border-right-color: {{VALUE}}',
	                '{{WRAPPER}} .twentytwenty-right-arrow' => 'border-left-color: {{VALUE}}',
	                '{{WRAPPER}} .twentytwenty-up-arrow'    => 'border-bottom-color: {{VALUE}}',
	                '{{WRAPPER}} .twentytwenty-down-arrow'  => 'border-top-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'handle_background',
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .twentytwenty-handle',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'handle_border',
                'label'       => __( 'Border', 'essential-addons-elementor' ),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .twentytwenty-handle',
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'handle_border_radius',
            [
                'label'      => __( 'Border Radius', 'essential-addons-elementor' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .twentytwenty-handle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'handle_box_shadow',
                'selector' => '{{WRAPPER}} .twentytwenty-handle',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_handle_hover',
            [
                'label' => __( 'Hover', 'essential-addons-elementor' ),
            ]
        );

        $this->add_control(
            'handle_icon_color_hover',
            [
                'label'     => __( 'Icon Color', 'essential-addons-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
	                '{{WRAPPER}} .twentytwenty-handle:hover .twentytwenty-left-arrow'  => 'border-right-color: {{VALUE}}',
	                '{{WRAPPER}} .twentytwenty-handle:hover .twentytwenty-right-arrow' => 'border-left-color: {{VALUE}}',
	                '{{WRAPPER}} .twentytwenty-handle:hover .twentytwenty-up-arrow'    => 'border-bottom-color: {{VALUE}}',
	                '{{WRAPPER}} .twentytwenty-handle:hover .twentytwenty-down-arrow'  => 'border-top-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'handle_background_hover',
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .twentytwenty-handle:hover',
            ]
        );

        $this->add_control(
            'handle_border_color_hover',
            [
                'label'     => __( 'Border Color', 'essential-addons-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .twentytwenty-handle:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        /**
         * Style Tab: Divider
         */
        $this->start_controls_section(
            'section_divider_style',
            [
                'label' => __( 'Divider', 'essential-addons-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'divider_color',
            [
                'label'     => __( 'Color', 'essential-addons-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .twentytwenty-horizontal .twentytwenty-handle:before, {{WRAPPER}} .twentytwenty-horizontal .twentytwenty-handle:after, {{WRAPPER}} .twentytwenty-vertical .twentytwenty-handle:before, {{WRAPPER}} .twentytwenty-vertical .twentytwenty-handle:after' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'divider_width',
            [
                'label'          => __( 'Width', 'essential-addons-elementor' ),
                'type'           => Controls_Manager::SLIDER,
                'default'        => [
                    'size' => 3,
                    'unit' => 'px',
                ],
                'size_units'     => ['px', '%'],
                'range'          => [
                    'px' => [
                        'max' => 20,
                    ],
                ],
                'tablet_default' => [
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'unit' => 'px',
                ],
                'selectors'      => [
                    '{{WRAPPER}} .twentytwenty-horizontal .twentytwenty-handle:before, {{WRAPPER}} .twentytwenty-horizontal .twentytwenty-handle:after' => 'width: {{SIZE}}{{UNIT}}; margin-left: calc(-{{SIZE}}{{UNIT}}/2);',
                    '{{WRAPPER}} .twentytwenty-vertical .twentytwenty-handle:before, {{WRAPPER}} .twentytwenty-vertical .twentytwenty-handle:after' => 'height: {{SIZE}}{{UNIT}}; margin-top: calc(-{{SIZE}}{{UNIT}}/2);',
                ],
            ]
        );

        $this->end_controls_section();

        /**
         * Style Tab: Label
         */
        $this->start_controls_section(
            'section_label_style',
            [
                'label' => __( 'Label', 'essential-addons-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'label_horizontal_position',
            [
                'label'        => __( 'Position', 'essential-addons-elementor' ),
                'type'         => Controls_Manager::CHOOSE,
                'label_block'  => false,
                'default'      => 'top',
                'options'      => [
                    'top'    => [
                        'title' => __( 'Top', 'essential-addons-elementor' ),
                        'icon'  => 'eicon-v-align-top',
                    ],
                    'middle' => [
                        'title' => __( 'Middle', 'essential-addons-elementor' ),
                        'icon'  => 'eicon-v-align-middle',
                    ],
                    'bottom' => [
                        'title' => __( 'Bottom', 'essential-addons-elementor' ),
                        'icon'  => 'eicon-v-align-bottom',
                    ],
                ],
                'prefix_class' => 'eael-ic-label-horizontal-',
                'condition'    => [
                    'orientation' => 'horizontal',
                ],
            ]
        );

        $this->add_control(
            'label_vertical_position',
            [
                'label'        => __( 'Position', 'essential-addons-elementor' ),
                'type'         => Controls_Manager::CHOOSE,
                'label_block'  => false,
                'options'      => [
                    'left'   => [
                        'title' => __( 'Left', 'essential-addons-elementor' ),
                        'icon'  => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'essential-addons-elementor' ),
                        'icon'  => 'eicon-h-align-center',
                    ],
                    'right'  => [
                        'title' => __( 'Right', 'essential-addons-elementor' ),
                        'icon'  => 'eicon-h-align-right',
                    ],
                ],
                'default'      => 'center',
                'prefix_class' => 'eael-ic-label-vertical-',
                'condition'    => [
                    'orientation' => 'vertical',
                ],
            ]
        );

        $this->add_responsive_control(
            'label_align',
            [
                'label'      => __( 'Align', 'essential-addons-elementor' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range'      => [
                    'px' => [
                        'max' => 200,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}}.eael-ic-label-horizontal-top .twentytwenty-horizontal .twentytwenty-before-label:before,
                    {{WRAPPER}}.eael-ic-label-horizontal-top .twentytwenty-horizontal .twentytwenty-after-label:before'    => 'top: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .twentytwenty-horizontal .twentytwenty-before-label:before'                                                   => 'left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .twentytwenty-horizontal .twentytwenty-after-label:before'                                                    => 'right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.eael-ic-label-horizontal-bottom .twentytwenty-horizontal .twentytwenty-before-label:before,
                    {{WRAPPER}}.eael-ic-label-horizontal-bottom .twentytwenty-horizontal .twentytwenty-after-label:before' => 'bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .twentytwenty-vertical .twentytwenty-before-label:before'                                                     => 'top: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .twentytwenty-vertical .twentytwenty-after-label:before'                                                      => 'bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.eael-ic-label-vertical-left .twentytwenty-vertical .twentytwenty-before-label:before,
                    {{WRAPPER}}.eael-ic-label-vertical-left .twentytwenty-vertical .twentytwenty-after-label:before'       => 'left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.eael-ic-label-vertical-right .twentytwenty-vertical .twentytwenty-before-label:before,
                    {{WRAPPER}}.eael-ic-label-vertical-right .twentytwenty-vertical .twentytwenty-after-label:before'      => 'right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_label_style' );

        $this->start_controls_tab(
            'tab_label_before',
            [
                'label' => __( 'Before', 'essential-addons-elementor' ),
            ]
        );

        $this->add_control(
            'label_text_color_before',
            [
                'label'     => __( 'Text Color', 'essential-addons-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .twentytwenty-before-label:before' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'label_bg_color_before',
            [
                'label'     => __( 'Background Color', 'essential-addons-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .twentytwenty-before-label:before' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'label_border',
                'label'       => __( 'Border', 'essential-addons-elementor' ),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .twentytwenty-before-label:before',
            ]
        );

        $this->add_control(
            'label_border_radius',
            [
                'label'      => __( 'Border Radius', 'essential-addons-elementor' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .twentytwenty-before-label:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_label_after',
            [
                'label' => __( 'After', 'essential-addons-elementor' ),
            ]
        );

        $this->add_control(
            'label_text_color_after',
            [
                'label'     => __( 'Text Color', 'essential-addons-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .twentytwenty-after-label:before' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'label_bg_color_after',
            [
                'label'     => __( 'Background Color', 'essential-addons-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .twentytwenty-after-label:before' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'label_border_after',
                'label'       => __( 'Border', 'essential-addons-elementor' ),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .twentytwenty-after-label:before',
            ]
        );

        $this->add_control(
            'label_border_radius_after',
            [
                'label'      => __( 'Border Radius', 'essential-addons-elementor' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .twentytwenty-after-label:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'label_typography',
                'label'     => __( 'Typography', 'essential-addons-elementor' ),
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_ACCENT
                ],
                'selector'  => '{{WRAPPER}} .twentytwenty-before-label:before, {{WRAPPER}} .twentytwenty-after-label:before',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'label_padding',
            [
                'label'      => __( 'Padding', 'essential-addons-elementor' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .twentytwenty-before-label:before, {{WRAPPER}} .twentytwenty-after-label:before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator'  => 'before',
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {
        /**
         * Getting the options from user.
         */
        $settings = $this->get_settings_for_display();
        $before_image = $settings['before_image'];
        $after_image = $settings['after_image'];

        $this->add_render_attribute(
            'wrapper',
            [
                'id'                => 'eael-image-comparison-' . esc_attr( $this->get_id() ),
                'class'             => ['eael-img-comp-container','twentytwenty-container'],
                'data-offset'       => ( $settings['eael_image_comp_offset']['size'] / 100 ),
                'data-orientation'  => $settings['eael_image_comp_orientation'],
                'data-before_label' => $settings['before_image_label'],
                'data-after_label'  => $settings['after_image_label'],
                'data-overlay'      => $settings['eael_image_comp_overlay'],
                'data-onhover'      => $settings['eael_image_comp_move'],
                'data-onclick'      => $settings['eael_image_comp_click'],
            ]
        );

        echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>
			<img class="eael-before-img" alt="' . esc_attr( $settings['before_image_alt'] ) . '" src="' . esc_url( $before_image['url'] ) . '">
			<img class="eael-after-img" alt="' . esc_attr( $settings['after_image_alt'] ) . '" src="' . esc_url( $after_image['url'] ) . '">
        </div>';
    }
}
