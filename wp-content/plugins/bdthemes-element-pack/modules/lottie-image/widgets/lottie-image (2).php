<?php

namespace ElementPack\Modules\LottieImage\Widgets;

use Elementor\Modules\DynamicTags\Module as TagsModule;
use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Css_Filter;

use ElementPack\Element_Pack_Loader;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class Lottie_Image extends Module_Base {

    public function get_name() {
        return 'bdt-lottie-image';
    }

    public function get_title() {
        return BDTEP . esc_html__('Lottie Image', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-lottie-image';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['lottie', 'animation', 'bodymovin', 'transition', 'image', 'svg'];
    }

    public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['lottie', 'ep-scripts'];
        } else {
            return ['lottie', 'ep-lottie-image'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/CbODBtLTxWc';
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_content_layout',
            [
                'label' => esc_html__('Lottie Image', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'lottie_json_source',
            [
                'label'   => __('Select JSON Source', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'url',
                'options' => [
                    'url'    => __('Load From URL', 'bdthemes-element-pack'),
                    'local'  => __('Self Hosted', 'bdthemes-element-pack'),
                    'custom' => __('Custom JSON Code', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->add_control(
            'lottie_json_path',
            [
                'label'         => __('Lottie JSON URL', 'bdthemes-element-pack'),
                'description'   => sprintf(__('Enter your lottie josn file, if you don\'t understand lottie json file so please %1s look here %2s', 'bdthemes-element-pack'), '<a href="https://lottiefiles.com/featured" target="_blank">', '</a>'),
                'type'          => Controls_Manager::TEXT,
                'autocomplete'  => false,
                'show_external' => false,
                'label_block'   => true,
                'show_label'    => false,
                'default'       => BDTEP_ASSETS_URL . 'others/teamwork.json',
                'placeholder'   => __('Enter your json URL', 'bdthemes-element-pack'),
                'condition'     => [
                    'lottie_json_source' => 'url',
                ],
                'dynamic'       => [
                    'active' => true,
                ],

            ]
        );

        $this->add_control(
            'upload_json_file',
            [
                'label'       => __('Select JSON File', 'bdthemes-element-pack'),
                'type'        => 'json-upload',
                'label_block' => true,
                'show_label'  => true,
                'condition'   => [
                    'lottie_json_source' => 'local',
                ],
                'dynamic'     => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'lottie_json_code',
            [
                'label'       => __('Paste JSON Code', 'bdthemes-element-pack'),
                'description' => sprintf(__('Enter your lottie josn text, if you don\'t understand lottie json file so please %1s look here %2s', 'bdthemes-element-pack'), '<a href="https://lottiefiles.com/featured" target="_blank">', '</a>'),
                'type'        => Controls_Manager::TEXTAREA,
                'label_block' => true,
                'show_label'  => true,
                'dynamic'     => [
                    'active' => true,
                ],
                'placeholder' => __('Enter your json TEXT', 'bdthemes-element-pack'),
                'condition'   => [
                    'lottie_json_source' => 'custom',
                ],

            ]
        );


        $this->add_responsive_control(
            'align',
            [
                'label'     => __('Alignment', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'left'   => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'  => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'caption_source',
            [
                'label'              => __('Caption', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SELECT,
                'default'            => 'none',
                'options'            => [
                    'none'           => __('None', 'bdthemes-element-pack'),
                    // 'title_caption'  => __( 'Title', 'bdthemes-element-pack' ),
                    'custom_caption' => __('Custom', 'bdthemes-element-pack'),
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'caption',
            [
                'label'       => __('Custom Caption', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'default'     => '',
                'placeholder' => __('Enter your image caption', 'bdthemes-element-pack'),
                'condition'   => [
                    'caption_source' => 'custom_caption'
                ],
                'dynamic'     => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'link_to',
            [
                'label'   => __('Link', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none'   => __('None', 'bdthemes-element-pack'),
                    'custom' => __('Custom URL', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->add_control(
            'link',
            [
                'label'       => __('Link', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::URL,
                'dynamic'     => [
                    'active' => true,
                ],
                'placeholder' => __('https://your-link.com', 'bdthemes-element-pack'),
                'condition'   => [
                    'link_to' => 'custom',
                ],
                'show_label'  => false,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_additional',
            [
                'label' => esc_html__('Additional Settings', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'play_action',
            [
                'label'   => __('Play Action', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'autoplay',
                'options' => [
                    ''         => __('None', 'bdthemes-element-pack'),
                    'autoplay' => __('Auto Play', 'bdthemes-element-pack'),
                    'hover'    => __('Play on Hover', 'bdthemes-element-pack'),
                    'click'    => __('Play on Click', 'bdthemes-element-pack'),
                    'column'   => __('Play on Hover Column', 'bdthemes-element-pack'),
                    'section'  => __('Play on Hover Section', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->add_control(
            'view_type',
            [
                'label'     => esc_html__('Start When', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'pageload' => esc_html__('Page Loaded', 'bdthemes-element-pack'),
                    'scroll'   => esc_html__('When Scroll', 'bdthemes-element-pack'),
                ],
                'default'   => 'pageload',
                'separator' => 'before',
            ]
        );

        // $this->add_control(
        //  'lottie_trigger',
        //  [
        //      'label'   => __( 'Trigger', 'bdthemes-element-pack' ),
        //      'type'    => Controls_Manager::SELECT,
        //      'default' => 'arriving_to_viewport',
        //      'options' => [
        //          'arriving_to_viewport' => __( 'Viewport', 'bdthemes-element-pack' ),
        //          'bind_to_scroll'       => __( 'Scroll', 'bdthemes-element-pack' ),
        //          'none'                 => __( 'None', 'bdthemes-element-pack' ),
        //      ],
        //      'frontend_available' => true,
        //      'separator'          => 'before',
        //  ]
        // );

        // $this->add_control(
        //  'lottie_viewport',
        //  [
        //      'label' => __( 'Viewport', 'bdthemes-element-pack' ),
        //      'type' => Controls_Manager::SLIDER,
        //      'render_type' => 'none',
        //      'conditions' => [
        //          'relation' => 'or',
        //          'terms' => [
        //              [
        //                  'name' => 'lottie_trigger',
        //                  'operator' => '===',
        //                  'value' => 'arriving_to_viewport',
        //              ],
        //              [
        //                  'name' => 'lottie_trigger',
        //                  'operator' => '===',
        //                  'value' => 'bind_to_scroll',
        //              ],
        //          ],
        //      ],
        //      'default' => [
        //          'sizes' => [
        //              'start' => 0,
        //              'end' => 100,
        //          ],
        //          'unit' => '%',
        //      ],
        //      'labels' => [
        //          __( 'Bottom', 'bdthemes-element-pack' ),
        //          __( 'Top', 'bdthemes-element-pack' ),
        //      ],
        //      'scales' => 1,
        //      'handles' => 'range',
        //      'frontend_available' => true,
        //  ]
        // );

        // $this->add_control(
        //  'lottie_effects_relative_to',
        //  [
        //      'label' => __( 'Effects Relative To', 'bdthemes-element-pack' ),
        //      'type' => Controls_Manager::SELECT,
        //      'render_type' => 'none',
        //      'condition' => [
        //          'lottie_trigger' => 'bind_to_scroll',
        //      ],
        //      'default' => 'lottie_viewport',
        //      'options' => [
        //          'lottie_viewport' => __( 'Viewport', 'bdthemes-element-pack' ),
        //          'page' => __( 'Entire Page', 'bdthemes-element-pack' ),
        //      ],
        //      'frontend_available' => true,
        //  ]
        // );

        $this->add_control(
            'loop',
            [
                'label'   => esc_html__('Loop', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'lottie_number_of_times',
            [
                'label' => __('Times', 'bdthemes-element-pack'),
                'type' => Controls_Manager::NUMBER,
                'render_type' => 'content',
                // 'conditions' => [
                //  'relation' => 'and',
                //  'terms' => [
                //      [
                //          'name' => 'lottie_trigger',
                //          'operator' => '!==',
                //          'value' => 'bind_to_scroll',
                //      ],
                //      [
                //          'name' => 'loop',
                //          'operator' => '===',
                //          'value' => 'yes',
                //      ],
                //  ],
                // ],
                'min' => 0,
                'step' => 1,
                'frontend_available' => true,
                'condition' => [
                    'loop' => ['yes'],
                ]
            ]
        );

        $this->add_control(
            'speed',
            [
                'label'   => esc_html__('Play Speed', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SLIDER,
                'range'   => [
                    'px' => [
                        'min'  => 0.1,
                        'max'  => 5,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'size' => '1',
                ],
            ]
        );



        // $this->add_control(
        //  'lottie_link_timeout',
        //  [
        //      'label' => __( 'Link Timeout', 'bdthemes-element-pack' ) . ' (ms)',
        //      'type' => Controls_Manager::NUMBER,
        //      'render_type' => 'none',
        //      'conditions' => [
        //          'relation' => 'and',
        //          'terms' => [
        //              [
        //                  'name' => 'link_to',
        //                  'operator' => '===',
        //                  'value' => 'custom',
        //              ],
        //              [
        //                  'name' => 'lottie_trigger',
        //                  'operator' => '===',
        //                  'value' => 'on_click',
        //              ],
        //              [
        //                  'name' => 'custom_link[url]',
        //                  'operator' => '!==',
        //                  'value' => '',
        //              ],
        //          ],
        //      ],
        //      'description' => __( 'Redirect to link after selected timeout', 'bdthemes-element-pack' ),
        //      'min' => 0,
        //      'max' => 5000,
        //      'step' => 1,
        //      'frontend_available' => true,
        //  ]
        // );

        // $this->add_control(
        //  'lottie_on_hover_out',
        //  [
        //      'label' => __( 'On Hover Out', 'bdthemes-element-pack' ),
        //      'type' => Controls_Manager::SELECT,
        //      'render_type' => 'none',
        //      'condition' => [
        //          'lottie_trigger' => 'on_hover',
        //      ],
        //      'default' => 'default',
        //      'options' => [
        //          'default' => __( 'Default', 'bdthemes-element-pack' ),
        //          'reverse' => __( 'Reverse', 'bdthemes-element-pack' ),
        //          'pause' => __( 'Pause', 'bdthemes-element-pack' ),
        //      ],
        //      'frontend_available' => true,
        //  ]
        // );

        // $this->add_control(
        //  'lottie_hover_area',
        //  [
        //      'label' => __( 'Hover Area', 'bdthemes-element-pack' ),
        //      'type' => Controls_Manager::SELECT,
        //      'render_type' => 'none',
        //      'condition' => [
        //          'lottie_trigger' => 'on_hover',
        //      ],
        //      'default' => 'animation',
        //      'options' => [
        //          'animation' => __( 'Animation', 'bdthemes-element-pack' ),
        //          'column' => __( 'Column', 'bdthemes-element-pack' ),
        //          'section' => __( 'Section', 'bdthemes-element-pack' ),
        //      ],
        //      'frontend_available' => true,
        //  ]
        // );


        $this->add_control(
            'lottie_start_point',
            [
                'label' => __('Start Point', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'frontend_available' => true,
                'render_type' => 'content',
                'default' => [
                    'size' => '0',
                    'unit' => '%',
                ],
                'size_units' => ['%'],
            ]
        );

        $this->add_control(
            'lottie_end_point',
            [
                'label' => __('End Point', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'frontend_available' => true,
                'render_type' => 'content',
                'default' => [
                    'size' => '100',
                    'unit' => '%',
                ],
                'size_units' => ['%'],
            ]
        );

        // $this->add_control(
        //  'lottie_reverse_animation',
        //  [
        //      'label' => __( 'Reverse', 'bdthemes-element-pack' ),
        //      'type' => Controls_Manager::SWITCHER,
        //      'render_type' => 'none',
        //      'conditions' => [
        //          'relation' => 'and',
        //          'terms' => [
        //              [
        //                  'name' => 'lottie_trigger',
        //                  'operator' => '!==',
        //                  'value' => 'bind_to_scroll',
        //              ],
        //              [
        //                  'name' => 'lottie_trigger',
        //                  'operator' => '!==',
        //                  'value' => 'on_hover',
        //              ],
        //          ],
        //      ],
        //      'return_value' => 'yes',
        //      'default' => '',
        //      'frontend_available' => true,
        //  ]
        // );

        $this->add_control(
            'lottie_renderer',
            [
                'label' => __('Renderer', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'svg',
                'options' => [
                    'svg' => __('SVG', 'bdthemes-element-pack'),
                    'canvas' => __('Canvas', 'bdthemes-element-pack'),
                ],
                'separator' => 'before',
            ]
        );

        // $this->add_control(
        //  'lottie_lazyload',
        //  [
        //      'label' => __( 'Lazy Load', 'bdthemes-element-pack' ),
        //      'type' => Controls_Manager::SWITCHER,
        //      'return_value' => 'yes',
        //      'default' => '',
        //      'frontend_available' => true,
        //  ]
        // );

        $this->end_controls_section();

        //Style
        $this->start_controls_section(
            'section_style_image',
            [
                'label' => __('Lottie', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'width',
            [
                'label'          => __('Width', 'bdthemes-element-pack'),
                'type'           => Controls_Manager::SLIDER,
                'default'        => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units'     => ['%', 'px', 'vw'],
                'range'          => [
                    '%'  => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                    'vw' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors'      => [
                    '{{WRAPPER}} .bdt-lottie-image svg' => 'width: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'space',
            [
                'label'          => __('Max Width', 'bdthemes-element-pack') . ' (%)',
                'type'           => Controls_Manager::SLIDER,
                'default'        => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units'     => ['%'],
                'range'          => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors'      => [
                    '{{WRAPPER}} .bdt-lottie-image svg' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'separator_panel_style',
            [
                'type'  => Controls_Manager::DIVIDER,
                'style' => 'thick',
            ]
        );

        $this->start_controls_tabs('image_effects');

        $this->start_controls_tab(
            'normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'opacity',
            [
                'label'     => __('Opacity', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'max'  => 1,
                        'min'  => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-lottie-image svg' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name'     => 'css_filters',
                'selector' => '{{WRAPPER}} .bdt-lottie-image svg',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'opacity_hover',
            [
                'label'     => __('Opacity', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'max'  => 1,
                        'min'  => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-lottie-image:hover svg' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name'     => 'css_filters_hover',
                'selector' => '{{WRAPPER}} .bdt-lottie-image:hover svg',
            ]
        );

        $this->add_control(
            'background_hover_transition',
            [
                'label'     => __('Transition Duration', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'max'  => 3,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-lottie-image svg' => 'transition-duration: {{SIZE}}s',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'      => 'image_border',
                'selector'  => '{{WRAPPER}} .bdt-lottie-image svg',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'image_border_radius',
            [
                'label'      => __('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-lottie-image svg' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'image_box_shadow',
                'exclude'  => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} .bdt-lottie-image svg',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_caption',
            [
                'label'     => __('Caption', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'caption_show' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'caption_align',
            [
                'label'     => __('Alignment', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'left'    => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center'  => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'   => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-justify',
                    ],
                ],
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .widget-image-caption' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .widget-image-caption' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'caption_background_color',
            [
                'label'     => __('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .widget-image-caption' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'caption_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .widget-image-caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'caption_typography',
                'selector' => '{{WRAPPER}} .widget-image-caption',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name'     => 'caption_text_shadow',
                'selector' => '{{WRAPPER}} .widget-image-caption',
            ]
        );

        $this->add_responsive_control(
            'caption_space',
            [
                'label'     => __('Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .widget-image-caption' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    private function get_link_url($settings) {

        if ('custom' === $settings['link_to']) {
            if (empty($settings['link']['url'])) {
                return false;
            }
            return $settings['link'];
        } else {
            return false;
        }
    }

    private function get_caption($settings) {

        if ('custom_caption' === $settings['caption_source']) {
            return $settings['caption'];
        } else if ('title_caption' === $settings['caption_source']) {
            return get_the_title($settings['upload_json_file']);
        }

        return '';
    }

    protected function render() {
        $settings    = $this->get_settings_for_display();
        $json_code   = '';
        $json_path   = '';
        $is_json_url = true;

        if ($settings['lottie_json_source'] == 'url') {
            $json_path = $settings['lottie_json_path'];
        } elseif ($settings['lottie_json_source'] == 'local') {
            $json_path = $settings['upload_json_file'];
        } elseif ($settings['lottie_json_source'] == 'custom') {
            $json_code   = $settings['lottie_json_code'];
            $is_json_url = false;
        }

        $this->add_render_attribute('wrapper', 'class', 'bdt-lottie-image');

        if (!empty($settings['shape'])) {
            $this->add_render_attribute('wrapper', 'class', 'elementor-image-shape-' . $settings['shape']);
        }

        $link = $this->get_link_url($settings);

        if ($link) {

            $this->add_render_attribute('link', 'href', $link['url']);

            if (Element_Pack_Loader::elementor()->editor->is_edit_mode()) {
                $this->add_render_attribute('link', [
                    'class' => 'elementor-clickable',
                ]);
            }

            if (!empty($link['is_external'])) {
                $this->add_render_attribute('link', 'target', '_blank');
            }

            if (!empty($link['nofollow'])) {
                $this->add_render_attribute('link', 'rel', 'nofollow');
            }
        }
        $lottie_start_point = (!empty($settings['lottie_start_point']['size']) ? $settings['lottie_start_point']['size'] : 0);
        $lottie_end_point   = (isset($settings['lottie_end_point']['size'])) ? $settings['lottie_end_point']['size'] : 0;
        $lottie_end_point = (strlen($lottie_end_point) > 0) ? $lottie_end_point : 100;


        $loopSet = '';
        if (isset($settings['loop'])) {
            $loopSet = ($settings['loop']) ? true : false;
        }

        if (!empty($settings['lottie_number_of_times']) && strlen($settings['lottie_number_of_times']) > 0) {
            $loopSet = ($settings['lottie_number_of_times']) - 1;
        }

        $this->add_render_attribute(
            [
                'lottie' => [
                    'id'            => 'bdt-lottie-' . $this->get_id(),
                    'class'         => 'bdt-lottie-container',
                    'data-settings' => [
                        wp_json_encode([
                            'loop'        => $loopSet,
                            'is_json_url' => $is_json_url,
                            'json_path'   => $json_path,
                            'json_code'   => $json_code,
                            'view_type'   => $settings['view_type'],
                            'speed'       => ($settings['speed']['size']) ? $settings['speed']['size'] : 1,
                            'play_action' => $settings['play_action'],
                            'start_point' => $lottie_start_point,
                            'end_point'   => $lottie_end_point,
                            'lottie_renderer' => $settings['lottie_renderer'],

                        ])
                    ]
                ]
            ]
        );

        $caption = $this->get_caption($settings);


?>

        <div <?php echo $this->get_render_attribute_string('wrapper'); ?>>

            <?php if ('custom_caption' === $settings['caption_source']) : ?>
                <figure class="wp-caption">
                <?php endif; ?>

                <?php if ($link) : ?>
                    <a <?php echo $this->get_render_attribute_string('link'); ?>>
                    <?php endif; ?>
                    <div <?php echo $this->get_render_attribute_string('lottie'); ?>></div>
                    <?php if ($link) : ?>
                    </a>
                <?php endif; ?>

                <?php if ('custom_caption' === $settings['caption_source']) : ?>
                    <figcaption class="widget-image-caption wp-caption-text">
                        <?php echo $caption; ?>
                    </figcaption>

                </figure>
            <?php endif; ?>

        </div>


<?php
    }
}
