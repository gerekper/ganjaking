<?php

namespace WPDeveloper\BetterDocsPro\Admin\Customizer\Sections;

use WP_Customize_Control;
use WP_Customize_Image_Control;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\TitleControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\SelectControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\ToggleControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\DimensionControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\SeparatorControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\AlphaColorControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\RadioImageControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\RangeValueControl;

class MultipleKB extends Section {
    /**
     * Section Priority
     * @var int
     */
    protected $priority = 99;

    /**
     * Get the section id.
     * @return string
     */
    public function get_id() {
        return 'betterdocs_mkb_settings';
    }

    /**
     * Get the title of the section.
     * @return string
     */
    public function get_title() {
        return __( 'Multiple KB', 'betterdocs' );
    }

    public function layout_select() {
        $this->customizer->add_setting( 'betterdocs_multikb_layout_select', [
            'default'           => $this->defaults['betterdocs_multikb_layout_select'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'select']
        ] );

        $this->customizer->add_control(
            new RadioImageControl(
                $this->customizer,
                'betterdocs_multikb_layout_select',
                [
                    'type'     => 'betterdocs-radio-image',
                    'settings' => 'betterdocs_multikb_layout_select',
                    'section'  => 'betterdocs_mkb_settings',
                    'label'    => __( 'Select Multiple KB Layout', 'theme-slug' ),
                    'priority' => 1,
                    'choices'  => [
                        'layout-1' => [
                            'label' => __( 'Grid Layout', 'betterdocs-pro' ),
                            'image' => $this->assets->icon( 'customizer/docs-page/layout-2.png', true )
                        ],
                        'layout-2' => [
                            'label' => __( 'Box Layout', 'betterdocs-pro' ),
                            'image' => $this->assets->icon( 'customizer/docs-page/layout-3.png', true )
                        ],
                        'layout-3' => [
                            'label' => __( 'Card Layout', 'betterdocs-pro' ),
                            'image' => $this->assets->icon( 'customizer/docs-page/layout-5.png', true )
                        ],
                        'layout-4' => [
                            'label' => __( 'Tabbed Layout', 'betterdocs-pro' ),
                            'image' => $this->pro_assets->icon( 'layout-tab-view.png', true )
                        ]
                    ]
                ]
            )
        );
    }

    public function content_area_bg_color() {
        $this->customizer->add_setting( 'betterdocs_mkb_background_color', [
            'default'           => $this->defaults['betterdocs_mkb_background_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_background_color',
                [
                    'label'    => __( 'Content Area Background Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_background_color',
                    'priority' => 2
                ]
            )
        );
    }

    public function content_area_bg_image() {
        $this->customizer->add_setting( 'betterdocs_mkb_background_image', [
            'default'    => $this->defaults['betterdocs_mkb_background_image'],
            'capability' => 'edit_theme_options',
            'transport'  => 'postMessage'
        ] );

        $this->customizer->add_control(
            new WP_Customize_Image_Control(
                $this->customizer, 'betterdocs_mkb_background_image', [
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_background_image',
                    'label'    => __( 'Background Image', 'betterdocs' ),
                    'priority' => 4
                ]
            )
        );
    }

    public function content_background_property() {
        $this->customizer->add_setting(
            'betterdocs_mkb_background_property',
            [
                'default'           => $this->defaults['betterdocs_mkb_background_property'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'select']
            ]
        );

        $this->customizer->add_control(
            new TitleControl(
                $this->customizer, 'betterdocs_mkb_background_property', [
                    'type'        => 'betterdocs-title',
                    'section'     => 'betterdocs_mkb_settings',
                    'settings'    => 'betterdocs_mkb_background_property',
                    'label'       => __( 'Background Property', 'betterdocs-pro' ),
                    'priority'    => 4,
                    'input_attrs' => [
                        'id'    => 'betterdocs_mkb_background_property',
                        'class' => 'betterdocs-select'
                    ]
                ]
            )
        );
    }

    public function content_background_size() {
        $this->customizer->add_setting(
            'betterdocs_mkb_background_size',
            [
                'default'           => $this->defaults['betterdocs_mkb_background_size'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'select']

            ]
        );

        $this->customizer->add_control(
            new SelectControl(
                $this->customizer,
                'betterdocs_mkb_background_size',
                [
                    'type'        => 'betterdocs-select',
                    'section'     => 'betterdocs_mkb_settings',
                    'settings'    => 'betterdocs_mkb_background_size',
                    'label'       => __( 'Size', 'betterdocs-pro' ),
                    'priority'    => 5,
                    'input_attrs' => [
                        'class' => 'betterdocs_mkb_background_property betterdocs-select'
                    ],
                    'choices'     => [
                        'auto'    => __( 'auto', 'betterdocs-pro' ),
                        'length'  => __( 'length', 'betterdocs-pro' ),
                        'cover'   => __( 'cover', 'betterdocs-pro' ),
                        'contain' => __( 'contain', 'betterdocs-pro' ),
                        'initial' => __( 'initial', 'betterdocs-pro' ),
                        'inherit' => __( 'inherit', 'betterdocs-pro' )
                    ]
                ]
            )
        );
    }

    public function content_background_repeat() {
        $this->customizer->add_setting(
            'betterdocs_mkb_background_repeat', [
                'default'           => $this->defaults['betterdocs_mkb_background_repeat'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'select']
            ]
        );

        $this->customizer->add_control(
            new SelectControl(
                $this->customizer, 'betterdocs_mkb_background_repeat', [
                    'type'        => 'betterdocs-select',
                    'section'     => 'betterdocs_mkb_settings',
                    'settings'    => 'betterdocs_mkb_background_repeat',
                    'label'       => __( 'Repeat', 'betterdocs-pro' ),
                    'priority'    => 6,
                    'input_attrs' => [
                        'class' => 'betterdocs_mkb_background_property betterdocs-select'
                    ],
                    'choices'     => [
                        'no-repeat' => __( 'no-repeat', 'betterdocs-pro' ),
                        'initial'   => __( 'initial', 'betterdocs-pro' ),
                        'inherit'   => __( 'inherit', 'betterdocs-pro' ),
                        'repeat'    => __( 'repeat', 'betterdocs-pro' ),
                        'repeat-x'  => __( 'repeat-x', 'betterdocs-pro' ),
                        'repeat-y'  => __( 'repeat-y', 'betterdocs-pro' )
                    ]
                ]
            )
        );
    }

    public function content_background_attachment() {
        $this->customizer->add_setting(
            'betterdocs_mkb_background_attachment',
            [
                'default'           => $this->defaults['betterdocs_mkb_background_attachment'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'select']
            ]
        );

        $this->customizer->add_control(
            new SelectControl(
                $this->customizer,
                'betterdocs_mkb_background_attachment',
                [
                    'type'        => 'betterdocs-select',
                    'section'     => 'betterdocs_mkb_settings',
                    'settings'    => 'betterdocs_mkb_background_attachment',
                    'label'       => __( 'Attachment', 'betterdocs-pro' ),
                    'priority'    => 7,
                    'input_attrs' => [
                        'class' => 'betterdocs_mkb_background_property betterdocs-select'
                    ],
                    'choices'     => [
                        'initial' => __( 'initial', 'betterdocs-pro' ),
                        'inherit' => __( 'inherit', 'betterdocs-pro' ),
                        'scroll'  => __( 'scroll', 'betterdocs-pro' ),
                        'fixed'   => __( 'fixed', 'betterdocs-pro' ),
                        'local'   => __( 'local', 'betterdocs-pro' )
                    ]
                ]
            )
        );
    }

    public function content_background_position() {
        $this->customizer->add_setting(
            'betterdocs_mkb_background_position',
            [
                'default'           => $this->defaults['betterdocs_mkb_background_position'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => 'esc_html'

            ]
        );

        $this->customizer->add_control(
            new SelectControl(
                $this->customizer, 'betterdocs_mkb_background_position', [
                    'type'        => 'betterdocs-select',
                    'section'     => 'betterdocs_mkb_settings',
                    'settings'    => 'betterdocs_mkb_background_position',
                    'label'       => __( 'Position', 'betterdocs-pro' ),
                    'priority'    => 8,
                    'input_attrs' => [
                        'class' => 'betterdocs_mkb_background_property betterdocs-select'
                    ],
                    'choices'     => [
                        'left top'      => __( 'left top', 'betterdocs-pro' ),
                        'left center'   => __( 'left center', 'betterdocs-pro' ),
                        'left bottom'   => __( 'left bottom', 'betterdocs-pro' ),
                        'right top'     => __( 'right top', 'betterdocs-pro' ),
                        'right center'  => __( 'right center', 'betterdocs-pro' ),
                        'right bottom'  => __( 'right bottom', 'betterdocs-pro' ),
                        'center top'    => __( 'center top', 'betterdocs-pro' ),
                        'center center' => __( 'center center', 'betterdocs-pro' ),
                        'center bottom' => __( 'center bottom', 'betterdocs-pro' )
                    ]
                ]
            )
        );
    }

    public function content_padding() {
        $this->customizer->add_setting(
            'betterdocs_mkb_content_padding', [
                'default'           => $this->defaults['betterdocs_mkb_content_padding'],
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ]
        );

        $this->customizer->add_control(
            new TitleControl(
                $this->customizer, 'betterdocs_mkb_content_padding', [
                    'type'        => 'betterdocs-title',
                    'section'     => 'betterdocs_mkb_settings',
                    'settings'    => 'betterdocs_mkb_content_padding',
                    'label'       => __( 'Content Area Padding', 'betterdocs-pro' ),
                    'priority'    => 9,
                    'input_attrs' => [
                        'id'    => 'betterdocs-doc-page-content-padding',
                        'class' => 'betterdocs-dimension'
                    ]
                ]
            )
        );

        $this->customizer->add_setting(
            'betterdocs_mkb_content_padding_top',
            apply_filters( 'betterdocs_mkb_content_padding_top', [
                'default'           => $this->defaults['betterdocs_mkb_content_padding_top'],
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ] )
        );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_mkb_content_padding_top', [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_mkb_settings',
                    'settings'    => 'betterdocs_mkb_content_padding_top',
                    'label'       => __( 'Top', 'betterdocs-pro' ),
                    'priority'    => 9,
                    'input_attrs' => [
                        'class' => 'betterdocs-doc-page-content-padding betterdocs-dimension'
                    ]
                ]
            )
        );

        $this->customizer->add_setting(
            'betterdocs_mkb_content_padding_right',
            apply_filters( 'betterdocs_mkb_content_padding_right', [
                'default'           => $this->defaults['betterdocs_mkb_content_padding_right'],
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ] )
        );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer,
                'betterdocs_mkb_content_padding_right',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_mkb_settings',
                    'settings'    => 'betterdocs_mkb_content_padding_right',
                    'label'       => __( 'Right', 'betterdocs-pro' ),
                    'priority'    => 9,
                    'input_attrs' => [
                        'class' => 'betterdocs-doc-page-content-padding betterdocs-dimension'
                    ]
                ]
            )
        );

        $this->customizer->add_setting(
            'betterdocs_mkb_content_padding_bottom',
            apply_filters( 'betterdocs_mkb_content_padding_bottom', [
                'default'           => $this->defaults['betterdocs_mkb_content_padding_bottom'],
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ] )
        );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_mkb_content_padding_bottom', [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_mkb_settings',
                    'settings'    => 'betterdocs_mkb_content_padding_bottom',
                    'label'       => __( 'Bottom', 'betterdocs-pro' ),
                    'priority'    => 9,
                    'input_attrs' => [
                        'class' => 'betterdocs-doc-page-content-padding betterdocs-dimension'
                    ]
                ]
            )
        );

        $this->customizer->add_setting(
            'betterdocs_mkb_content_padding_left',
            apply_filters( 'betterdocs_mkb_content_padding_left', [
                'default'           => $this->defaults['betterdocs_mkb_content_padding_left'],
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ] )
        );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer,
                'betterdocs_mkb_content_padding_left',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_mkb_settings',
                    'settings'    => 'betterdocs_mkb_content_padding_left',
                    'label'       => __( 'Left', 'betterdocs-pro' ),
                    'priority'    => 9,
                    'input_attrs' => [
                        'class' => 'betterdocs-doc-page-content-padding betterdocs-dimension'
                    ]
                ]
            )
        );
    }

    public function content_width() {
        $this->customizer->add_setting( 'betterdocs_mkb_content_width',
            apply_filters( 'betterdocs_mkb_content_width', [
                'default'           => $this->defaults['betterdocs_mkb_content_width'],
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => [$this->sanitizer, 'integer']

            ] )
        );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_mkb_content_width', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_content_width',
                'label'       => __( 'Content Area Width', 'betterdocs-pro' ),
                'priority'    => 14,
                'input_attrs' => [
                    'class'  => 'betterdocs-range-value',
                    'min'    => 0,
                    'max'    => 100,
                    'step'   => 1,
                    'suffix' => '%' //optional suffix
                ]
            ] )
        );
    }

    public function content_max_width() {
        $this->customizer->add_setting( 'betterdocs_mkb_content_max_width',
            apply_filters( 'betterdocs_mkb_content_max_width', [
                'default'           => $this->defaults['betterdocs_mkb_content_max_width'],
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ] )
        );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_mkb_content_max_width', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_content_max_width',
                'label'       => __( 'Content Area Maximum Width', 'betterdocs-pro' ),
                'priority'    => 15,
                'input_attrs' => [
                    'class'  => 'betterdocs-range-value',
                    'min'    => 100,
                    'max'    => 1600,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ]
            ] ) );
    }

    public function list_separator() {
        $this->customizer->add_setting( 'betterdocs_mkb_list_separator', [
            'default'           => '',
            'sanitize_callback' => 'esc_html'
        ] );

        $this->customizer->add_control( new SeparatorControl(
            $this->customizer, 'betterdocs_mkb_list_separator', [
                'label'    => __( 'Knowledge Base Tab List', 'betterdocs-pro' ),
                'settings' => 'betterdocs_mkb_list_separator',
                'section'  => 'betterdocs_mkb_settings',
                'priority' => 17
            ] )
        );
    }

    public function list_bg_color() {
        $this->customizer->add_setting( 'betterdocs_mkb_list_bg_color', [
            'default'           => $this->defaults['betterdocs_mkb_list_bg_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_list_bg_color',
                [
                    'label'    => __( 'Tab List Background Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_list_bg_color',
                    'priority' => 17
                ]
            )
        );
    }

    public function list_bg_hover_color() {
        $this->customizer->add_setting( 'betterdocs_mkb_list_bg_hover_color', [
            'default'           => $this->defaults['betterdocs_mkb_list_bg_hover_color'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_list_bg_hover_color',
                [
                    'label'    => __( 'Tab List Background Hover Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_list_bg_hover_color',
                    'priority' => 17
                ]
            )
        );
    }

    public function list_font_color() {
        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_font_color', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_font_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_tab_list_font_color',
                [
                    'label'    => __( 'Tab List Font Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_tab_list_font_color',
                    'priority' => 17
                ]
            )
        );
    }

    public function list_font_color_active() {
        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_font_color_active', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_font_color_active'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_tab_list_font_color_active',
                [
                    'label'    => __( 'Active Tab List Font Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_tab_list_font_color_active',
                    'priority' => 17
                ]
            )
        );
    }

    public function list_back_color_active() {
        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_back_color_active', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_back_color_active'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_tab_list_back_color_active',
                [
                    'label'    => __( 'Active Tab List Background Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_tab_list_back_color_active',
                    'priority' => 17
                ]
            )
        );
    }

    public function list_font_size() {
        $this->customizer->add_setting( 'betterdocs_mkb_list_font_size', [
            'default'           => $this->defaults['betterdocs_mkb_list_font_size'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_mkb_list_font_size', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_list_font_size',
                'label'       => __( 'Tab List Font Size', 'betterdocs-pro' ),
                'priority'    => 17,
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 50,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ]
            ] ) );
    }

    public function list_column_padding() {
        $this->customizer->add_setting( 'betterdocs_mkb_list_column_padding', [
            'default'           => '',
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_mkb_list_column_padding', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_list_column_padding',
                'label'       => __( 'Tab List Padding', 'betterdocs-pro' ),
                'priority'    => 17,
                'input_attrs' => [
                    'id'    => 'betterdocs_mkb_list_column_padding',
                    'class' => 'betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_mkb_list_column_padding_top', [
            'default'           => $this->defaults['betterdocs_mkb_list_column_padding_top'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_list_column_padding_top', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_list_column_padding_top',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'priority'    => 17,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_list_column_padding betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_mkb_list_column_padding_right', [
            'default'           => $this->defaults['betterdocs_mkb_list_column_padding_right'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_list_column_padding_right', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_list_column_padding_right',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'priority'    => 17,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_list_column_padding betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_mkb_list_column_padding_bottom', [
            'default'           => $this->defaults['betterdocs_mkb_list_column_padding_bottom'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_list_column_padding_bottom', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_list_column_padding_bottom',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'priority'    => 17,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_list_column_padding betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_mkb_list_column_padding_left', [
            'default'           => $this->defaults['betterdocs_mkb_list_column_padding_left'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_list_column_padding_left', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_list_column_padding_left',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'priority'    => 17,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_list_column_padding betterdocs-dimension'
                ]
            ] )
        );
    }

    public function tab_List_margin() {
        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_margin', [
            'default'           => '',
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_mkb_tab_list_margin', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_tab_list_margin',
                'label'       => __( 'Tab List Margin', 'betterdocs-pro' ),
                'priority'    => 17,
                'input_attrs' => [
                    'id'    => 'betterdocs_mkb_tab_list_margin',
                    'class' => 'betterdocs-dimension'
                ]
            ]
        ) );

        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_margin_top', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_margin_top'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_tab_list_margin_top', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_tab_list_margin_top',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'priority'    => 17,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_tab_list_margin betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_margin_right', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_margin_right'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_tab_list_margin_right', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_tab_list_margin_right',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'priority'    => 17,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_tab_list_margin betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_margin_bottom', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_margin_bottom'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_tab_list_margin_bottom', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_tab_list_margin_bottom',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'priority'    => 17,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_tab_list_margin betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_margin_left', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_margin_left'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_tab_list_margin_left', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_tab_list_margin_left',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'priority'    => 17,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_tab_list_margin betterdocs-dimension'
                ]
            ] )
        );
    }

    public function tab_list_border_radius() {
        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_border', [
            'default'           => '',
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_mkb_tab_list_border', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_tab_list_border',
                'label'       => __( 'Tab List Border Radius', 'betterdocs-pro' ),
                'priority'    => 17,
                'input_attrs' => [
                    'id'    => 'betterdocs_mkb_tab_list_border',
                    'class' => 'betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_border_topleft', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_border_topleft'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer'],
            'input_attrs'       => [
                'class' => 'betterdocs_mkb_tab_list_border betterdocs-dimension'
            ]
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_tab_list_border_topleft', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_tab_list_border_topleft',
                'label'       => __( 'Top Left', 'betterdocs-pro' ),
                'priority'    => 17,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_tab_list_border betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_border_topright', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_border_topright'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_tab_list_border_topright', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_tab_list_border_topright',
                'label'       => __( 'Top Right', 'betterdocs-pro' ),
                'priority'    => 17,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_tab_list_border betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_border_bottomright', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_border_bottomright'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_tab_list_border_bottomright', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_tab_list_border_bottomright',
                'label'       => __( 'Bottom Right', 'betterdocs-pro' ),
                'priority'    => 17,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_tab_list_border betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_border_bottomleft', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_border_bottomleft'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_tab_list_border_bottomleft', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_tab_list_border_bottomleft',
                'label'       => __( 'Bottom Left', 'betterdocs-pro' ),
                'priority'    => 17,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_tab_list_border betterdocs-dimension'
                ]
            ] )
        );
    }

    public function category_column_separator() {
        $this->customizer->add_setting( 'betterdocs_mkb_category_column_list_seprator', [
            'default'           => '',
            'sanitize_callback' => 'esc_html'
        ] );

        $this->customizer->add_control( new SeparatorControl(
            $this->customizer, 'betterdocs_mkb_category_column_list_seprator', [
                'label'    => __( 'Category Column Settings', 'betterdocs-pro' ),
                'settings' => 'betterdocs_mkb_category_column_list_seprator',
                'section'  => 'betterdocs_mkb_settings',
                'priority' => 17
            ] )
        );
    }

    public function title_tag() {
        $this->customizer->add_setting( 'betterdocs_mkb_title_tag', [
            'default'           => $this->defaults['betterdocs_mkb_title_tag'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'choices']
        ] );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_mkb_title_tag',
                [
                    'label'    => __( 'Category Title Tag', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_title_tag',
                    'type'     => 'select',
                    'choices'  => [
                        'h1' => 'h1',
                        'h2' => 'h2',
                        'h3' => 'h3',
                        'h4' => 'h4',
                        'h5' => 'h5',
                        'h6' => 'h6'
                    ],
                    'priority' => 17
                ] )
        );
    }

    public function column_space() {
        $this->customizer->add_setting( 'betterdocs_mkb_column_space',
            apply_filters( 'betterdocs_mkb_column_space', [
                'default'           => $this->defaults['betterdocs_mkb_column_space'],
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ] )
        );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_mkb_column_space', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_column_space',
                'label'       => __( 'Spacing Between Columns', 'betterdocs-pro' ),
                'priority'    => 17,
                'input_attrs' => [
                    'class'  => 'betterdocs-range-value',
                    'min'    => 0,
                    'max'    => 100,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ]
            ] )
        );
    }

    public function column_bg_color2() {
        $this->customizer->add_setting( 'betterdocs_mkb_column_bg_color2', [
            'default'           => $this->defaults['betterdocs_mkb_column_bg_color2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_column_bg_color2',
                [
                    'label'    => __( 'Column Background Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_column_bg_color2',
                    'priority' => 18
                ]
            )
        );
    }

    public function column_hover_bg_color() {
        $this->customizer->add_setting( 'betterdocs_mkb_column_hover_bg_color', [
            'default'           => $this->defaults['betterdocs_mkb_column_hover_bg_color'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_column_hover_bg_color',
                [
                    'label'    => __( 'Column Background Hover Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_column_hover_bg_color',
                    'priority' => 18
                ]
            )
        );
    }

    public function column_padding() {
        $this->customizer->add_setting( 'betterdocs_mkb_column_padding', [
            'default'           => $this->defaults['betterdocs_mkb_column_padding'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_mkb_column_padding', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_column_padding',
                'label'       => __( 'Column Padding', 'betterdocs-pro' ),
                'priority'    => 18,
                'input_attrs' => [
                    'id'    => 'betterdocs_mkb_column_padding',
                    'class' => 'betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_mkb_column_padding_top', [
            'default'           => $this->defaults['betterdocs_mkb_column_padding_top'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_column_padding_top', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_column_padding_top',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'priority'    => 18,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_column_padding betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_mkb_column_padding_right', [
            'default'           => $this->defaults['betterdocs_mkb_column_padding_right'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_column_padding_right', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_column_padding_right',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'priority'    => 18,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_column_padding betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_mkb_column_padding_bottom', [
            'default'           => $this->defaults['betterdocs_mkb_column_padding_bottom'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_column_padding_bottom', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_column_padding_bottom',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'priority'    => 18,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_column_padding betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_mkb_column_padding_left', [
            'default'           => $this->defaults['betterdocs_mkb_column_padding_left'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_column_padding_left', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_column_padding_left',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'priority'    => 18,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_column_padding betterdocs-dimension'
                ]
            ] )
        );
    }

    public function show_category_icon() {
        $this->customizer->add_setting( 'betterdocs_mkb_page_show_category_icon', [
            'default'           => $this->defaults['betterdocs_mkb_page_show_category_icon'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'checkbox']
        ] );

        $this->customizer->add_control( new ToggleControl(
            $this->customizer, 'betterdocs_mkb_page_show_category_icon', [
                'label'    => __( 'Show Category Icon', 'betterdocs' ),
                'section'  => 'betterdocs_mkb_settings',
                'settings' => 'betterdocs_mkb_page_show_category_icon',
                'type'     => 'light', // light, ios, flat
                'priority' => 24
            ]
        ) );
    }

    public function cat_icon_size() {
        $this->customizer->add_setting( 'betterdocs_mkb_cat_icon_size', [
            'default'           => $this->defaults['betterdocs_mkb_cat_icon_size'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_mkb_cat_icon_size', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_cat_icon_size',
                'label'       => __( 'Icon Size', 'betterdocs-pro' ),
                'priority'    => 24,
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 200,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ]
            ] )
        );
    }

    public function column_borderr() {
        $this->customizer->add_setting( 'betterdocs_mkb_column_borderr', [
            'default'           => $this->defaults['betterdocs_mkb_column_borderr'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_mkb_column_borderr', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_column_borderr',
                'label'       => __( 'Column Border Radius', 'betterdocs-pro' ),
                'priority'    => 24,
                'input_attrs' => [
                    'id'    => 'betterdocs_mkb_column_borderr',
                    'class' => 'betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_mkb_column_borderr_topleft', [
            'default'           => $this->defaults['betterdocs_mkb_column_borderr_topleft'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_column_borderr_topleft', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_column_borderr_topleft',
                'label'       => __( 'Top Left', 'betterdocs-pro' ),
                'priority'    => 24,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_column_borderr betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_mkb_column_borderr_topright', [
            'default'           => $this->defaults['betterdocs_mkb_column_borderr_topright'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_column_borderr_topright', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_column_borderr_topright',
                'label'       => __( 'Top Right', 'betterdocs-pro' ),
                'priority'    => 24,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_column_borderr betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_mkb_column_borderr_bottomright', [
            'default'           => $this->defaults['betterdocs_mkb_column_borderr_bottomright'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_column_borderr_bottomright', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_column_borderr_bottomright',
                'label'       => __( 'Bottom Right', 'betterdocs-pro' ),
                'priority'    => 24,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_column_borderr betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_mkb_column_borderr_bottomleft', [
            'default'           => $this->defaults['betterdocs_mkb_column_borderr_bottomleft'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_column_borderr_bottomleft', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_column_borderr_bottomleft',
                'label'       => __( 'Bottom Left', 'betterdocs-pro' ),
                'priority'    => 24,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_column_borderr betterdocs-dimension'
                ]
            ] )
        );
    }

    public function cat_title_font_size() {
        $this->customizer->add_setting( 'betterdocs_mkb_cat_title_font_size', [
            'default'           => $this->defaults['betterdocs_mkb_cat_title_font_size'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_mkb_cat_title_font_size', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_cat_title_font_size',
                'label'       => __( 'Title Font Size', 'betterdocs-pro' ),
                'priority'    => 25,
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 100,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ]
            ] )
        );
    }

    public function cat_title_color() {
        $this->customizer->add_setting( 'betterdocs_mkb_cat_title_color', [
            'default'           => $this->defaults['betterdocs_mkb_cat_title_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_cat_title_color',
                [
                    'label'    => __( 'Title Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_cat_title_color',
                    'priority' => 26
                ]
            )
        );
    }

    public function cat_title_hover_color() {
        $this->customizer->add_setting( 'betterdocs_mkb_cat_title_hover_color', [
            'default'           => $this->defaults['betterdocs_mkb_cat_title_hover_color'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_cat_title_hover_color',
                [
                    'label'    => __( 'Title Hover Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_cat_title_hover_color',
                    'priority' => 26
                ]
            )
        );
    }

    public function item_count_color() {
        $this->customizer->add_setting( 'betterdocs_mkb_item_count_color', [
            'default'           => $this->defaults['betterdocs_mkb_item_count_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_item_count_color',
                [
                    'label'    => __( 'Item Count Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_item_count_color',
                    'priority' => 29
                ]
            )
        );
    }

    public function item_count_color_hover() {
        $this->customizer->add_setting( 'betterdocs_mkb_item_count_color_hover', [
            'default'           => $this->defaults['betterdocs_mkb_item_count_color_hover'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_item_count_color_hover',
                [
                    'label'    => __( 'Item Count Hover Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_item_count_color_hover',
                    'priority' => 29
                ]
            )
        );
    }

    public function tem_count_font_size() {
        $this->customizer->add_setting( 'betterdocs_mkb_item_count_font_size', [
            'default'           => $this->defaults['betterdocs_mkb_item_count_font_size'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_mkb_item_count_font_size', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_item_count_font_size',
                'label'       => __( 'Count Font Size', 'betterdocs-pro' ),
                'priority'    => 30,
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 50,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ]
            ] )
        );
    }

    public function desc() {
        $this->customizer->add_setting( 'betterdocs_mkb_desc', [
            'default'           => $this->defaults['betterdocs_mkb_desc'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'checkbox']
        ] );

        $this->customizer->add_control( new ToggleControl(
            $this->customizer, 'betterdocs_mkb_desc', [
                'label'    => __( 'KB Description', 'betterdocs-pro' ),
                'section'  => 'betterdocs_mkb_settings',
                'settings' => 'betterdocs_mkb_desc',
                'type'     => 'light', // light, ios, flat
                'priority' => 28
            ] )
        );
    }

    public function desc_color() {
        $this->customizer->add_setting( 'betterdocs_mkb_desc_color', [
            'default'           => $this->defaults['betterdocs_mkb_desc_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_desc_color',
                [
                    'label'    => __( 'KB Description Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_desc_color',
                    'priority' => 28
                ]
            )
        );
    }

    public function column_content_space() {
        $this->customizer->add_setting( 'betterdocs_mkb_column_content_space', [
            'default'           => $this->defaults['betterdocs_mkb_column_content_space'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_mkb_column_content_space', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_column_content_space',
                'label'       => __( 'Content Space Between', 'betterdocs-pro' ),
                'priority'    => 33,
                'input_attrs' => [
                    'id'    => 'betterdocs_mkb_column_content_space',
                    'class' => 'betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_mkb_column_content_space_image', [
            'default'           => $this->defaults['betterdocs_mkb_column_content_space_image'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_column_content_space_image', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_column_content_space_image',
                'label'       => __( 'Icon', 'betterdocs-pro' ),
                'priority'    => 33,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_column_content_space betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_mkb_column_content_space_title', [
            'default'           => $this->defaults['betterdocs_mkb_column_content_space_title'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_column_content_space_title', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_column_content_space_title',
                'label'       => __( 'Title', 'betterdocs-pro' ),
                'priority'    => 33,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_column_content_space betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_mkb_column_content_space_desc', [
            'default'           => $this->defaults['betterdocs_mkb_column_content_space_desc'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_column_content_space_desc', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_column_content_space_desc',
                'label'       => __( 'Description', 'betterdocs-pro' ),
                'priority'    => 33,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_column_content_space betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_mkb_column_content_space_counter', [
            'default'           => $this->defaults['betterdocs_mkb_column_content_space_counter'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_column_content_space_counter', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_column_content_space_counter',
                'label'       => __( 'Counter', 'betterdocs-pro' ),
                'priority'    => 33,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_column_content_space betterdocs-dimension'
                ]
            ] )
        );
    }

    public function column_list_heading() {
        $this->customizer->add_setting( 'betterdocs_mkb_column_list_heading', [
            'default'           => '',
            'sanitize_callback' => 'esc_html'
        ] );

        $this->customizer->add_control( new SeparatorControl(
            $this->customizer, 'betterdocs_mkb_column_list_heading', [
                'label'    => __( 'Category Column List', 'betterdocs-pro' ),
                'settings' => 'betterdocs_mkb_column_list_heading',
                'section'  => 'betterdocs_mkb_settings',
                'priority' => 33
            ] )
        );
    }

    public function column_list_color() {
        $this->customizer->add_setting( 'betterdocs_mkb_column_list_color', [
            'default'           => $this->defaults['betterdocs_mkb_column_list_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_column_list_color',
                [
                    'label'    => __( 'Docs List Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_column_list_color',
                    'priority' => 33
                ]
            )
        );
    }

    public function column_list_hover_color() {
        $this->customizer->add_setting( 'betterdocs_mkb_column_list_hover_color', [
            'default'           => $this->defaults['betterdocs_mkb_column_list_hover_color'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_column_list_hover_color',
                [
                    'label'    => __( 'Docs List Hover Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_column_list_hover_color',
                    'priority' => 33
                ]
            )
        );
    }

    public function column_list_font_size() {
        $this->customizer->add_setting( 'betterdocs_mkb_column_list_font_size', [
            'default'           => $this->defaults['betterdocs_mkb_column_list_font_size'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_mkb_column_list_font_size', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_column_list_font_size',
                'label'       => __( 'Docs List Font Size', 'betterdocs-pro' ),
                'priority'    => 33,
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 50,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ]
            ] )
        );
    }

    public function column_list_margin() {
        $this->customizer->add_setting( 'betterdocs_mkb_column_list_margin', [
            'default'           => '',
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_mkb_column_list_margin', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_column_list_margin',
                'label'       => __( 'Docs List Margin', 'betterdocs-pro' ),
                'priority'    => 33,
                'input_attrs' => [
                    'id'    => 'betterdocs_doc_page_article_list_margin',
                    'class' => 'betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_mkb_column_list_margin_top', [
            'default'           => $this->defaults['betterdocs_mkb_column_list_margin_top'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_column_list_margin_top', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_column_list_margin_top',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'priority'    => 33,
                'input_attrs' => [
                    'class' => 'betterdocs_doc_page_article_list_margin betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_mkb_column_list_margin_right', [
            'default'           => $this->defaults['betterdocs_mkb_column_list_margin_right'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_column_list_margin_right', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_column_list_margin_right',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'priority'    => 33,
                'input_attrs' => [
                    'class' => 'betterdocs_doc_page_article_list_margin betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_mkb_list_margin_bottom', [
            'default'           => $this->defaults['betterdocs_mkb_list_margin_bottom'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_list_margin_bottom', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_list_margin_bottom',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'priority'    => 33,
                'input_attrs' => [
                    'class' => 'betterdocs_doc_page_article_list_margin betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_mkb_list_margin_left', [
            'default'           => $this->defaults['betterdocs_mkb_list_margin_left'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_list_margin_left', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_list_margin_left',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'priority'    => 33,
                'input_attrs' => [
                    'class' => 'betterdocs_doc_page_article_list_margin betterdocs-dimension'
                ]
            ] )
        );
    }

    public function tab_list_explore_btn() {
        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_explore_btn', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_explore_btn'],
            'sanitize_callback' => 'esc_html'
        ] );

        $this->customizer->add_control( new SeparatorControl(
            $this->customizer, 'betterdocs_mkb_tab_list_explore_btn', [
                'label'    => __( 'Explore More Button', 'betterdocs-pro' ),
                'settings' => 'betterdocs_mkb_tab_list_explore_btn',
                'section'  => 'betterdocs_mkb_settings',
                'priority' => 33
            ] )
        );
    }

    public function tab_list_explore_btn_bg_color() {
        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_explore_btn_bg_color', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_explore_btn_bg_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_tab_list_explore_btn_bg_color',
                [
                    'label'    => __( 'Button Background Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_tab_list_explore_btn_bg_color',
                    'priority' => 33
                ]
            )
        );
    }

    public function tab_list_explore_btn_color() {
        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_explore_btn_color', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_explore_btn_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_tab_list_explore_btn_color',
                [
                    'label'    => __( 'Button Text Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_tab_list_explore_btn_color',
                    'priority' => 33
                ]
            )
        );
    }

    public function ab_list_explore_btn_border_color() {
        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_explore_btn_border_color', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_explore_btn_border_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_tab_list_explore_btn_border_color',
                [
                    'label'    => __( 'Button Border Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_tab_list_explore_btn_border_color',
                    'priority' => 33
                ]
            )
        );
    }

    public function tab_list_explore_btn_hover_bg_color() {
        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_explore_btn_hover_bg_color', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_explore_btn_hover_bg_color'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_tab_list_explore_btn_hover_bg_color',
                [
                    'label'    => __( 'Button Background Hover Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_tab_list_explore_btn_hover_bg_color',
                    'priority' => 33
                ] )
        );
    }

    public function tab_list_explore_btn_hover_color() {
        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_explore_btn_hover_color', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_explore_btn_hover_color'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_tab_list_explore_btn_hover_color',
                [
                    'label'    => __( 'Button Text Hover Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_tab_list_explore_btn_hover_color',
                    'priority' => 33
                ]
            )
        );
    }

    public function tab_list_explore_btn_hover_border_color() {
        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_explore_btn_hover_border_color', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_explore_btn_hover_border_color'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_tab_list_explore_btn_hover_border_color',
                [
                    'label'    => __( 'Button Border Hover Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_tab_list_explore_btn_hover_border_color',
                    'priority' => 33
                ]
            )
        );
    }

    public function tab_list_explore_btn_font_size() {
        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_explore_btn_font_size', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_explore_btn_font_size'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_mkb_tab_list_explore_btn_font_size', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_tab_list_explore_btn_font_size',
                'label'       => __( 'Button Font Size', 'betterdocs-pro' ),
                'priority'    => 33,
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 50,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ]
            ] )
        );
    }

    public function tab_list_explore_btn_padding() {
        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_explore_btn_padding', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_explore_btn_padding'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_mkb_tab_list_explore_btn_padding', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_tab_list_explore_btn_padding',
                'label'       => __( 'Button Padding', 'betterdocs-pro' ),
                'priority'    => 33,
                'input_attrs' => [
                    'id'    => 'betterdocs_doc_page_explore_btn_padding',
                    'class' => 'betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_explore_btn_padding_top', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_explore_btn_padding_top'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_tab_list_explore_btn_padding_top', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_tab_list_explore_btn_padding_top',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'priority'    => 33,
                'input_attrs' => [
                    'class' => 'betterdocs_doc_page_explore_btn_padding betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_explore_btn_padding_right', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_explore_btn_padding_right'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_tab_list_explore_btn_padding_right', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_tab_list_explore_btn_padding_right',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'priority'    => 33,
                'input_attrs' => [
                    'class' => 'betterdocs_doc_page_explore_btn_padding betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_explore_btn_padding_bottom', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_explore_btn_padding_bottom'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_tab_list_explore_btn_padding_bottom', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_tab_list_explore_btn_padding_bottom',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'priority'    => 33,
                'input_attrs' => [
                    'class' => 'betterdocs_doc_page_explore_btn_padding betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_explore_btn_padding_left', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_explore_btn_padding_left'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_tab_list_explore_btn_padding_left', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_tab_list_explore_btn_padding_left',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'priority'    => 33,
                'input_attrs' => [
                    'class' => 'betterdocs_doc_page_explore_btn_padding betterdocs-dimension'
                ]
            ] )
        );
    }

    public function tab_list_explore_btn_borderr() {
        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_explore_btn_borderr', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_explore_btn_borderr'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_mkb_tab_list_explore_btn_borderr', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_tab_list_explore_btn_borderr',
                'label'       => __( 'Button Border Radius', 'betterdocs-pro' ),
                'priority'    => 33,
                'input_attrs' => [
                    'id'    => 'betterdocs_mkb_tab_list_explore_btn_borderr',
                    'class' => 'betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_explore_btn_borderr_topleft', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_explore_btn_borderr_topleft'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_tab_list_explore_btn_borderr_topleft', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_tab_list_explore_btn_borderr_topleft',
                'label'       => __( 'Top Left', 'betterdocs-pro' ),
                'priority'    => 33,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_tab_list_explore_btn_borderr betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_explore_btn_borderr_topright', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_explore_btn_borderr_topright'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_tab_list_explore_btn_borderr_topright', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_tab_list_explore_btn_borderr_topright',
                'label'       => __( 'Top Right', 'betterdocs-pro' ),
                'priority'    => 33,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_tab_list_explore_btn_borderr betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_explore_btn_borderr_bottomright', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_explore_btn_borderr_bottomright'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_tab_list_explore_btn_borderr_bottomright', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_tab_list_explore_btn_borderr_bottomright',
                'label'       => __( 'Bottom Right', 'betterdocs-pro' ),
                'priority'    => 33,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_tab_list_explore_btn_borderr betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_mkb_tab_list_explore_btn_borderr_bottomleft', [
            'default'           => $this->defaults['betterdocs_mkb_tab_list_explore_btn_borderr_bottomleft'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_tab_list_explore_btn_borderr_bottomleft', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_tab_list_explore_btn_borderr_bottomleft',
                'label'       => __( 'Bottom Left', 'betterdocs-pro' ),
                'priority'    => 33,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_tab_list_explore_btn_borderr betterdocs-dimension'
                ]
            ] )
        );
    }

    public function popular_list_settings() {
        $this->customizer->add_setting( 'betterdocs_mkb_popular_list_settings', [
            'default'           => $this->defaults['betterdocs_mkb_popular_list_settings'],
            'sanitize_callback' => 'esc_html'
        ] );

        $this->customizer->add_control( new SeparatorControl(
            $this->customizer, 'betterdocs_mkb_popular_list_settings', [
                'label'    => __( 'Popular Docs', 'betterdocs-pro' ),
                'settings' => 'betterdocs_mkb_popular_list_settings',
                'section'  => 'betterdocs_mkb_settings',
                'priority' => 34
            ] )
        );
    }

    public function popular_docs_switch() {
        $this->customizer->add_setting( 'betterdocs_mkb_popular_docs_switch', [
            'default'           => $this->defaults['betterdocs_mkb_popular_docs_switch'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'checkbox']
        ] );

        $this->customizer->add_control( new ToggleControl(
            $this->customizer, 'betterdocs_mkb_popular_docs_switch', [
                'label'    => __( 'Popular Docs Show', 'betterdocs-pro' ),
                'section'  => 'betterdocs_mkb_settings',
                'settings' => 'betterdocs_mkb_popular_docs_switch',
                'type'     => 'light', // light, ios, flat
                'priority' => 34
            ] )
        );
    }

    public function popular_list_bg_color() {
        $this->customizer->add_setting( 'betterdocs_mkb_popular_list_bg_color', [
            'default'           => $this->defaults['betterdocs_mkb_popular_list_bg_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_popular_list_bg_color',
                [
                    'label'    => __( 'Popular Docs Background Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_popular_list_bg_color',
                    'priority' => 34
                ]
            )
        );
    }

    public function popular_list_color() {
        $this->customizer->add_setting( 'betterdocs_mkb_popular_list_color', [
            'default'           => $this->defaults['betterdocs_mkb_popular_list_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_popular_list_color',
                [
                    'label'    => __( 'Popular Docs List Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_popular_list_color',
                    'priority' => 35
                ]
            )
        );
    }

    public function popular_list_hover_color() {
        $this->customizer->add_setting( 'betterdocs_mkb_popular_list_hover_color', [
            'default'           => $this->defaults['betterdocs_mkb_popular_list_hover_color'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_popular_list_hover_color',
                [
                    'label'    => __( 'Popular Docs List Hover Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_popular_list_hover_color',
                    'priority' => 36
                ]
            )
        );
    }

    public function popular_list_font_size() {
        $this->customizer->add_setting( 'betterdocs_mkb_popular_list_font_size', [
            'default'           => $this->defaults['betterdocs_mkb_popular_list_font_size'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_mkb_popular_list_font_size', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_popular_list_font_size',
                'label'       => __( 'Popular Docs List Font Size', 'betterdocs-pro' ),
                'priority'    => 37,
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 50,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ]
            ] )
        );
    }

    public function popular_title_font_size() {
        $this->customizer->add_setting( 'betterdocs_mkb_popular_title_font_size', [
            'default'           => $this->defaults['betterdocs_mkb_popular_title_font_size'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_mkb_popular_title_font_size', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_popular_title_font_size',
                'label'       => __( 'Popular Title Font Size', 'betterdocs-pro' ),
                'priority'    => 37,
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 50,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ]
            ] )
        );
    }

    public function popular_title_color() {
        // Popular Title Color(MKB)
        $this->customizer->add_setting( 'betterdocs_mkb_popular_title_color', [
            'default'           => $this->defaults['betterdocs_mkb_popular_title_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_popular_title_color',
                [
                    'label'    => __( 'Popular Title Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_popular_title_color',
                    'priority' => 38
                ]
            )
        );

    }

    public function popular_title_color_hover() {
        $this->customizer->add_setting( 'betterdocs_mkb_popular_title_color_hover', [
            'default'           => $this->defaults['betterdocs_mkb_popular_title_color_hover'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_popular_title_color_hover',
                [
                    'label'    => __( 'Popular Title Color Hover', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_popular_title_color_hover',
                    'priority' => 38
                ]
            )
        );
    }

    public function popular_list_icon_color() {
        $this->customizer->add_setting( 'betterdocs_mkb_popular_list_icon_color', [
            'default'           => $this->defaults['betterdocs_mkb_popular_list_icon_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_mkb_popular_list_icon_color',
                [
                    'label'    => __( 'Popular List Icon Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_popular_list_icon_color',
                    'priority' => 38
                ]
            )
        );
    }

    public function popular_list_icon_font_size() {
        $this->customizer->add_setting( 'betterdocs_mkb_popular_list_icon_font_size', [
            'default'           => $this->defaults['betterdocs_mkb_popular_list_icon_font_size'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_mkb_popular_list_icon_font_size', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_popular_list_icon_font_size',
                'label'       => __( 'Popular List Icon Font Size', 'betterdocs-pro' ),
                'priority'    => 39,
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 50,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ]
            ] )
        );
    }

    public function popular_list_margin() {
        $this->customizer->add_setting( 'betterdocs_mkb_popular_list_margin', [
            'default'           => $this->defaults['betterdocs_mkb_popular_list_margin'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_mkb_popular_list_margin', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_popular_list_margin',
                'label'       => __( 'Popular Docs List Margin', 'betterdocs-pro' ),
                'priority'    => 40,
                'input_attrs' => [
                    'id'    => 'betterdocs_mkb_popular_list_margin',
                    'class' => 'betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_mkb_popular_list_margin_top', [
            'default'           => $this->defaults['betterdocs_mkb_popular_list_margin_top'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_popular_list_margin_top', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_popular_list_margin_top',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'priority'    => 40,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_popular_list_margin betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_mkb_popular_list_margin_right', [
            'default'           => $this->defaults['betterdocs_mkb_popular_list_margin_right'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_popular_list_margin_right', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_popular_list_margin_right',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'priority'    => 40,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_popular_list_margin betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_mkb_popular_list_margin_bottom', [
            'default'           => $this->defaults['betterdocs_mkb_popular_list_margin_bottom'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_popular_list_margin_bottom', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_popular_list_margin_bottom',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'priority'    => 40,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_popular_list_margin betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_mkb_popular_list_margin_left', [
            'default'           => $this->defaults['betterdocs_mkb_popular_list_margin_left'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_popular_list_margin_left', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_popular_list_margin_left',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'priority'    => 40,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_popular_list_margin betterdocs-dimension'
                ]
            ] )
        );
    }

    public function popular_title_margin() {
        $this->customizer->add_setting( 'betterdocs_mkb_popular_title_margin', [
            'default'           => $this->defaults['betterdocs_mkb_popular_title_margin'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_mkb_popular_title_margin', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_popular_title_margin',
                'label'       => __( 'Popular Docs Title Margin', 'betterdocs-pro' ),
                'priority'    => 39,
                'input_attrs' => [
                    'id'    => 'betterdocs_mkb_popular_title_margin',
                    'class' => 'betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_mkb_popular_title_margin_top', [
            'default'           => $this->defaults['betterdocs_mkb_popular_title_margin_top'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_popular_title_margin_top', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_popular_title_margin_top',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'priority'    => 39,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_popular_title_margin betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_mkb_popular_title_margin_right', [
            'default'           => $this->defaults['betterdocs_mkb_popular_title_margin_right'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_popular_title_margin_right', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_popular_title_margin_right',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'priority'    => 39,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_popular_title_margin betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_mkb_popular_title_margin_bottom', [
            'default'           => $this->defaults['betterdocs_mkb_popular_title_margin_bottom'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_popular_title_margin_bottom', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_popular_title_margin_bottom',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'priority'    => 39,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_popular_title_margin betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_mkb_popular_title_margin_left', [
            'default'           => $this->defaults['betterdocs_mkb_popular_title_margin_left'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_popular_title_margin_left', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_popular_title_margin_left',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'priority'    => 39,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_popular_title_margin betterdocs-dimension'
                ]
            ] )
        );
    }

    public function popular_docs_padding() {
        $this->customizer->add_setting( 'betterdocs_mkb_popular_docs_padding', [
            'default'           => $this->defaults['betterdocs_mkb_popular_docs_padding'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_mkb_popular_docs_padding', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_popular_docs_padding',
                'label'       => __( 'Popular Docs Padding', 'betterdocs-pro' ),
                'priority'    => 45,
                'input_attrs' => [
                    'id'    => 'betterdocs_mkb_popular_docs_padding',
                    'class' => 'betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_mkb_popular_docs_padding_top', [
            'default'           => $this->defaults['betterdocs_mkb_popular_docs_padding_top'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_popular_docs_padding_top', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_popular_docs_padding_top',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'priority'    => 45,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_popular_docs_padding betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_mkb_popular_docs_padding_right', [
            'default'           => $this->defaults['betterdocs_mkb_popular_docs_padding_right'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_popular_docs_padding_right', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_popular_docs_padding_right',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'priority'    => 45,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_popular_docs_padding betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_mkb_popular_docs_padding_bottom', [
            'default'           => $this->defaults['betterdocs_mkb_popular_docs_padding_bottom'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_popular_docs_padding_bottom', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_popular_docs_padding_bottom',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'priority'    => 45,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_popular_docs_padding betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_mkb_popular_docs_padding_left', [
            'default'           => $this->defaults['betterdocs_mkb_popular_docs_padding_left'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_mkb_popular_docs_padding_left', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_mkb_settings',
                'settings'    => 'betterdocs_mkb_popular_docs_padding_left',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'priority'    => 45,
                'input_attrs' => [
                    'class' => 'betterdocs_mkb_popular_docs_padding betterdocs-dimension'
                ]
            ] )
        );
    }
}
