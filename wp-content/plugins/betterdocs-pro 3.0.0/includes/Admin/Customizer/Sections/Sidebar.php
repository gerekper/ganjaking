<?php

namespace WPDeveloper\BetterDocsPro\Admin\Customizer\Sections;

use WP_Customize_Control;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\TitleControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\DimensionControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\SeparatorControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\AlphaColorControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\RangeValueControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Sections\Sidebar as FreeSidebar;

class Sidebar extends FreeSidebar {

    public function seperator_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_seperator_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_seperator_layout6'],
            'sanitize_callback' => 'esc_html'
        ] );

        $this->customizer->add_control( new SeparatorControl(
            $this->customizer, 'betterdocs_sidebar_seperator_layout6', [
                'label'       => __( 'Sidebar Bohemian Layout', 'betterdocs-pro' ),
                'settings'    => 'betterdocs_sidebar_seperator_layout6',
                'section'     => 'betterdocs_sidebar_settings',
                'input_attrs' => [
                    'class' => 'bohemian-layout'
                ],
                'priority'    => 301
            ] )
        );
    }

    public function bg_color_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_bg_color_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_bg_color_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_sidebar_bg_color_layout6',
                [
                    'label'    => __( 'Background Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_sidebar_settings',
                    'settings' => 'betterdocs_sidebar_bg_color_layout6',
                    'priority' => 301
                ]
            )
        );
    }

    public function active_bg_color_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_active_bg_color_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_active_bg_color_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_sidebar_active_bg_color_layout6',
                [
                    'label'    => __( 'Active Background Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_sidebar_settings',
                    'settings' => 'betterdocs_sidebar_active_bg_color_layout6',
                    'priority' => 301
                ]
            )
        );
    }

    public function active_bg_border_color_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_active_bg_border_color_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_active_bg_border_color_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_sidebar_active_bg_border_color_layout6',
                [
                    'label'    => __( 'Active Background Border Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_sidebar_settings',
                    'settings' => 'betterdocs_sidebar_active_bg_border_color_layout6',
                    'priority' => 301
                ]
            )
        );
    }

    public function padding_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_padding_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_padding_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_sidebar_padding_layout6', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_padding_layout6',
                'label'       => __( 'Sidebar Padding', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_sidebar_padding_layout6',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 302
            ] )
        );

        // Sidebar Padding Top Layout 6(For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_padding_top_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_padding_top_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_padding_top_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_padding_top_layout6',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_padding_layout6 betterdocs-dimension'
                ],
                'priority'    => 303
            ] )
        );

        // Sidebar Padding Right Layout 6(For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_padding_right_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_padding_right_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_padding_right_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_padding_right_layout6',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_padding_layout6 betterdocs-dimension'
                ],
                'priority'    => 304
            ] )
        );

        // Sidebar Padding Bottom Layout 6(For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_padding_bottom_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_padding_bottom_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_padding_bottom_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_padding_bottom_layout6',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_padding_layout6 betterdocs-dimension'
                ],
                'priority'    => 305
            ] )
        );

        // Sidebar Padding Left Layout 6(For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_padding_left_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_padding_left_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_padding_left_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_padding_left_layout6',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_padding_layout6 betterdocs-dimension'
                ],
                'priority'    => 306
            ] )
        );
    }

    public function margin_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_margin_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_margin_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_sidebar_margin_layout6', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_margin_layout6',
                'label'       => __( 'Sidebar Margin', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_sidebar_margin_layout6',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 307
            ] )
        );

        // Sidebar Margin Top Layout 6(For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_margin_top_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_margin_top_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_margin_top_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_margin_top_layout6',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_margin_layout6 betterdocs-dimension'
                ],
                'priority'    => 308
            ] )
        );

        // Sidebar Margin Right Layout 6(For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_margin_right_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_margin_right_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_margin_right_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_margin_right_layout6',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_margin_layout6 betterdocs-dimension'
                ],
                'priority'    => 309
            ] )
        );

        // Sidebar Margin Bottom Layout 6(For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_margin_bottom_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_margin_bottom_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_margin_bottom_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_margin_bottom_layout6',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_margin_layout6 betterdocs-dimension'
                ],
                'priority'    => 310
            ] )
        );

        // Sidebar Margin Left Layout 6(For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_margin_left_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_margin_left_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_margin_left_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_margin_left_layout6',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_margin_layout6 betterdocs-dimension'
                ],
                'priority'    => 311
            ] )
        );
    }

    public function border_radius_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_border_radius_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_border_radius_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_sidebar_border_radius_layout6', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_border_radius_layout6',
                'label'       => __( 'Sidebar Border Radius', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_sidebar_border_radius_layout6',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 313
            ] )
        );

        // Sidebar Border Radius Top Left Layout 6(For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_border_radius_top_left_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_border_radius_top_left_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_border_radius_top_left_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_border_radius_top_left_layout6',
                'label'       => __( 'Top Left', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_border_radius_layout6 betterdocs-dimension'
                ],
                'priority'    => 313
            ] )
        );

        // Sidebar Border Radius Top Right Layout 6(For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_border_radius_top_right_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_border_radius_top_right_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_border_radius_top_right_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_border_radius_top_right_layout6',
                'label'       => __( 'Top Right', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_border_radius_layout6 betterdocs-dimension'
                ],
                'priority'    => 313
            ] )
        );

        // Sidebar Border Radius Bottom Right Layout 6(For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_border_radius_bottom_right_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_border_radius_bottom_right_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_border_radius_bottom_right_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_border_radius_bottom_right_layout6',
                'label'       => __( 'Bottom Right', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_border_radius_layout6 betterdocs-dimension'
                ],
                'priority'    => 314
            ] )
        );

        // Sidebar Border Radius Bottom Left Layout 6(For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_border_radius_bottom_left_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_border_radius_bottom_left_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_border_radius_bottom_left_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_border_radius_bottom_left_layout6',
                'label'       => __( 'Bottom Left', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_border_radius_layout6 betterdocs-dimension'
                ],
                'priority'    => 315
            ] )
        );
    }

    public function title_tag_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_title_tag_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_title_tag_layout6'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'choices']
        ] );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_sidebar_title_tag_layout6',
                [
                    'label'    => __( 'Category Title Tag', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_sidebar_settings',
                    'settings' => 'betterdocs_sidebar_title_tag_layout6',
                    'type'     => 'select',
                    'choices'  => [
                        'h1' => 'h1',
                        'h2' => 'h2',
                        'h3' => 'h3',
                        'h4' => 'h4',
                        'h5' => 'h5',
                        'h6' => 'h6'
                    ],
                    'priority' => 317
                ]
            )
        );
    }

    public function title_bg_color_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_title_bg_color_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_title_bg_color_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_sidebar_title_bg_color_layout6',
                [
                    'label'    => __( 'Title Background Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_sidebar_settings',
                    'settings' => 'betterdocs_sidebar_title_bg_color_layout6',
                    'priority' => 318
                ]
            )
        );
    }

    public function active_title_bg_color_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_active_title_bg_color_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_active_title_bg_color_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_sidebar_active_title_bg_color_layout6',
                [
                    'label'    => __( 'Active Title Background Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_sidebar_settings',
                    'settings' => 'betterdocs_sidebar_active_title_bg_color_layout6',
                    'priority' => 318
                ]
            )
        );
    }

    public function title_color_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_title_color_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_title_color_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_sidebar_title_color_layout6',
                [
                    'label'    => __( 'Title Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_sidebar_settings',
                    'settings' => 'betterdocs_sidebar_title_color_layout6',
                    'priority' => 319
                ]
            )
        );
    }

    public function title_hover_color_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_title_hover_color_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_title_hover_color_layout6'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_sidebar_title_hover_color_layout6',
                [
                    'label'    => __( 'Title Hover Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_sidebar_settings',
                    'settings' => 'betterdocs_sidebar_title_hover_color_layout6',
                    'priority' => 319
                ]
            )
        );
    }

    public function title_font_size_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_title_font_size_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_title_font_size_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_sidebar_title_font_size_layout6', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_title_font_size_layout6',
                'label'       => __( 'Title Font Size', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 50,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ],
                'priority'    => 320
            ] )
        );
    }

    public function title_font_line_height_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_title_font_line_height_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_title_font_line_height_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_sidebar_title_font_line_height_layout6', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_title_font_line_height_layout6',
                'label'       => __( 'Title Font Line Height', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 200,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ],
                'priority'    => 321
            ] )
        );
    }

    public function title_font_weight_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_title_font_weight_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_title_font_weight_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'choices']
        ] );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_sidebar_title_font_weight_layout6',
                [
                    'label'    => __( 'Title Font Weight', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_sidebar_settings',
                    'settings' => 'betterdocs_sidebar_title_font_weight_layout6',
                    'type'     => 'select',
                    'choices'  => [
                        'normal' => 'Normal',
                        '100'    => '100',
                        '200'    => '200',
                        '300'    => '300',
                        '400'    => '400',
                        '500'    => '500',
                        '600'    => '600',
                        '700'    => '700',
                        '800'    => '800',
                        '900'    => '900'
                    ],
                    'priority' => 322
                ]
            )
        );
    }

    public function title_padding_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_title_padding_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_title_padding_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_sidebar_title_padding_layout6', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_title_padding_layout6',
                'label'       => __( 'Title Padding', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_sidebar_title_padding_layout6',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 323
            ] )
        );

        // Sidebar Title Padding Top Layout 6 (For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_title_padding_top_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_title_padding_top_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_title_padding_top_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_title_padding_top_layout6',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_title_padding_layout6 betterdocs-dimension'
                ],
                'priority'    => 324
            ] )
        );

        // Sidebar Title Padding Right Layout 6 (For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_title_padding_right_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_title_padding_right_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_title_padding_right_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_title_padding_right_layout6',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_title_padding_layout6 betterdocs-dimension'
                ],
                'priority'    => 325
            ] )
        );

        // Sidebar Title Padding Bottom Layout 6 (For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_title_padding_bottom_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_title_padding_bottom_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_title_padding_bottom_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_title_padding_bottom_layout6',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_title_padding_layout6 betterdocs-dimension'
                ],
                'priority'    => 326
            ] )
        );

        // Sidebar Title Padding Left Layout 6 (For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_title_padding_left_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_title_padding_left_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_title_padding_left_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_title_padding_left_layout6',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_title_padding_layout6 betterdocs-dimension'
                ],
                'priority'    => 327
            ] )
        );
    }

    public function title_margin_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_title_margin_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_title_margin_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_sidebar_title_margin_layout6', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_title_margin_layout6',
                'label'       => __( 'Title Margin', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_sidebar_title_margin_layout6',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 328
            ] )
        );

        // Sidebar Title Margin Top Layout 6 (For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_title_margin_top_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_title_margin_top_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_title_margin_top_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_title_margin_top_layout6',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_title_margin_layout6 betterdocs-dimension'
                ],
                'priority'    => 329
            ] )
        );

        // Sidebar Title Margin Right Layout 6 (For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_title_margin_right_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_title_margin_right_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_title_margin_right_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_title_margin_right_layout6',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_title_margin_layout6 betterdocs-dimension'
                ],
                'priority'    => 330
            ] )
        );

        // Sidebar Title Margin Bottom Layout 6 (For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_title_margin_bottom_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_title_margin_bottom_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_title_margin_bottom_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_title_margin_bottom_layout6',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_title_margin_layout6 betterdocs-dimension'
                ],
                'priority'    => 331
            ] )
        );

        // Sidebar Title Margin Left Layout 6 (For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_title_margin_left_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_title_margin_left_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_title_margin_left_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_title_margin_left_layout6',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_title_margin_layout6 betterdocs-dimension'
                ],
                'priority'    => 332
            ] )
        );
    }

    public function list_border_type_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_term_list_border_type_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_list_border_type_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'choices']
        ] );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_sidebar_term_list_border_type_layout6',
                [
                    'label'    => __( 'Term List Border Type', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_sidebar_settings',
                    'settings' => 'betterdocs_sidebar_term_list_border_type_layout6',
                    'type'     => 'select',
                    'choices'  => [
                        'none'   => 'none',
                        'solid'  => 'solid',
                        'dashed' => 'dashed',
                        'dotted' => 'dotted',
                        'double' => 'double',
                        'groove' => 'groove',
                        'ridge'  => 'ridge',
                        'inset'  => 'inset',
                        'outset' => 'outset'
                    ],
                    'priority' => 333
                ]
            )
        );
    }

    public function border_width_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_term_border_width_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_border_width_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_sidebar_term_border_width_layout6', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_term_border_width_layout6',
                'label'       => __( 'Term List Border Width', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_sidebar_term_border_width_layout6',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 334
            ] )
        );

        // Sidebar Term List Border Top Width Layout 6 (For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_term_border_top_width_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_border_top_width_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_term_border_top_width_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_term_border_top_width_layout6',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_term_border_width_layout6 betterdocs-dimension'
                ],
                'priority'    => 335
            ] )
        );

        // Sidebar Term List Border Right Width Layout 6 (For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_term_border_right_width_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_border_right_width_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_term_border_right_width_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_term_border_right_width_layout6',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_term_border_width_layout6 betterdocs-dimension'
                ],
                'priority'    => 336
            ] )
        );

        // Sidebar Term List Border Bottom Width Layout 6 (For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_term_border_bottom_width_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_border_bottom_width_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_term_border_bottom_width_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_term_border_bottom_width_layout6',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_term_border_width_layout6 betterdocs-dimension'
                ],
                'priority'    => 337
            ] )
        );

        // Sidebar Term List Border Left Width Layout 6 (For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_term_border_left_width_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_border_left_width_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_term_border_left_width_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_term_border_left_width_layout6',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_term_border_width_layout6 betterdocs-dimension'
                ],
                'priority'    => 338
            ] )
        );
    }

    public function border_width_color_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_term_border_width_color_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_border_width_color_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_sidebar_term_border_width_color_layout6',
                [
                    'label'    => __( 'Border Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_sidebar_settings',
                    'settings' => 'betterdocs_sidebar_term_border_width_color_layout6',
                    'priority' => 339
                ]
            )
        );
    }

    public function list_item_color_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_term_list_item_color_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_list_item_color_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_sidebar_term_list_item_color_layout6',
                [
                    'label'    => __( 'List Item Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_sidebar_settings',
                    'settings' => 'betterdocs_sidebar_term_list_item_color_layout6',
                    'priority' => 340
                ]
            )
        );
    }

    public function list_item_hover_color_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_term_list_item_hover_color_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_list_item_hover_color_layout6'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_sidebar_term_list_item_hover_color_layout6',
                [
                    'label'    => __( 'List Item Color Hover', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_sidebar_settings',
                    'settings' => 'betterdocs_sidebar_term_list_item_hover_color_layout6',
                    'priority' => 340
                ]
            )
        );
    }

    public function list_item_font_size_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_term_list_item_font_size_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_list_item_font_size_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_sidebar_term_list_item_font_size_layout6', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_term_list_item_font_size_layout6',
                'label'       => __( 'List Item Font Size', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 50,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ],
                'priority'    => 340
            ] )
        );
    }

    public function list_item_icon_color_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_term_list_item_icon_color_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_list_item_icon_color_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_sidebar_term_list_item_icon_color_layout6',
                [
                    'label'    => __( 'List Icon Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_sidebar_settings',
                    'settings' => 'betterdocs_sidebar_term_list_item_icon_color_layout6',
                    'priority' => 340
                ]
            )
        );
    }

    public function list_item_icon_size_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_term_list_item_icon_size_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_list_item_icon_size_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_sidebar_term_list_item_icon_size_layout6', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_term_list_item_icon_size_layout6',
                'label'       => __( 'List Item Icon Size', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 50,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ],
                'priority'    => 340
            ] )
        );
    }

    public function list_item_padding_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_term_list_item_padding_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_list_item_padding_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_sidebar_term_list_item_padding_layout6', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_term_list_item_padding_layout6',
                'label'       => __( 'List Item Padding', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_sidebar_term_list_item_padding_layout6',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 340
            ] )
        );

        //Sidebar Term List Item Padding Top(For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_term_list_item_padding_top_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_list_item_padding_top_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_term_list_item_padding_top_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_term_list_item_padding_top_layout6',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_term_list_item_padding_layout6 betterdocs-dimension'
                ],
                'priority'    => 340
            ] )
        );

        //Sidebar Term List Item Padding Right(For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_term_list_item_padding_right_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_list_item_padding_right_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_term_list_item_padding_right_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_term_list_item_padding_right_layout6',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_term_list_item_padding_layout6 betterdocs-dimension'
                ],
                'priority'    => 340
            ] )
        );

        //Sidebar Term List Item Padding Bottom(For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_term_list_item_padding_bottom_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_list_item_padding_bottom_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_term_list_item_padding_bottom_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_term_list_item_padding_bottom_layout6',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_term_list_item_padding_layout6 betterdocs-dimension'
                ],
                'priority'    => 340
            ] )
        );

        //Sidebar Term List Item Padding Left(For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_term_list_item_padding_left_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_list_item_padding_left_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_term_list_item_padding_left_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_term_list_item_padding_left_layout6',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_term_list_item_padding_layout6 betterdocs-dimension'
                ],
                'priority'    => 340
            ] )
        );
    }

    public function list_active_item_color_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_term_list_active_item_color_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_list_active_item_color_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_sidebar_term_list_active_item_color_layout6',
                [
                    'label'    => __( 'Active List Item Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_sidebar_settings',
                    'settings' => 'betterdocs_sidebar_term_list_active_item_color_layout6',
                    'priority' => 340
                ]
            )
        );
    }

    public function item_counter_border_type_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_term_item_counter_border_type_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_item_counter_border_type_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'choices']
        ] );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_sidebar_term_item_counter_border_type_layout6',
                [
                    'label'    => __( 'Count Border Style', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_sidebar_settings',
                    'settings' => 'betterdocs_sidebar_term_item_counter_border_type_layout6',
                    'type'     => 'select',
                    'choices'  => [
                        'none'   => 'none',
                        'solid'  => 'solid',
                        'dashed' => 'dashed',
                        'dotted' => 'dotted',
                        'double' => 'double',
                        'groove' => 'groove',
                        'ridge'  => 'ridge',
                        'inset'  => 'inset',
                        'outset' => 'outset'
                    ],
                    'priority' => 340
                ]
            )
        );
    }

    public function item_counter_border_width_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_term_item_counter_border_width_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_item_counter_border_width_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_sidebar_term_item_counter_border_width_layout6', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_term_item_counter_border_width_layout6',
                'label'       => __( 'Count Border Width', 'betterdocs-pro' ),
                'priority'    => 341,
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

    public function tem_counter_font_size_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_term_item_counter_font_size_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_item_counter_font_size_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_sidebar_term_item_counter_font_size_layout6', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_term_item_counter_font_size_layout6',
                'label'       => __( 'Count Font Size', 'betterdocs-pro' ),
                'priority'    => 341,
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

    public function item_counter_font_weight_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_term_item_counter_font_weight_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_item_counter_font_weight_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'choices']
        ] );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_sidebar_term_item_counter_font_weight_layout6',
                [
                    'label'    => __( 'Count Font Weight', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_sidebar_settings',
                    'settings' => 'betterdocs_sidebar_term_item_counter_font_weight_layout6',
                    'type'     => 'select',
                    'choices'  => [
                        'normal' => 'Normal',
                        '100'    => '100',
                        '200'    => '200',
                        '300'    => '300',
                        '400'    => '400',
                        '500'    => '500',
                        '600'    => '600',
                        '700'    => '700',
                        '800'    => '800',
                        '900'    => '900'
                    ],
                    'priority' => 342
                ]
            )
        );
    }

    public function item_counter_font_line_height_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_term_item_counter_font_line_height_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_item_counter_font_line_height_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_sidebar_term_item_counter_font_line_height_layout6', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_term_item_counter_font_line_height_layout6',
                'label'       => __( 'Count Font Line Height', 'betterdocs-pro' ),
                'priority'    => 343,
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

    public function item_counter_color_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_term_item_counter_color_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_item_counter_color_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_sidebar_term_item_counter_color_layout6',
                [
                    'label'    => __( 'Count Font Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_sidebar_settings',
                    'settings' => 'betterdocs_sidebar_term_item_counter_color_layout6',
                    'priority' => 344
                ] )
        );
    }

    public function item_counter_back_color_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_term_item_counter_back_color_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_item_counter_back_color_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_sidebar_term_item_counter_back_color_layout6',
                [
                    'label'    => __( 'Count Background Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_sidebar_settings',
                    'settings' => 'betterdocs_sidebar_term_item_counter_back_color_layout6',
                    'priority' => 345
                ]
            )
        );
    }

    public function item_counter_border_radius_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_term_item_counter_border_radius_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_item_counter_border_radius_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_sidebar_term_item_counter_border_radius_layout6', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_term_item_counter_border_radius_layout6',
                'label'       => __( 'Count Border Radius', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_sidebar_term_item_counter_border_radius_layout6',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 346
            ] )
        );

        // Sidebar Term List Item Counter Border Radius Top Left Layout 6 (For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_term_item_counter_border_radius_top_left_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_item_counter_border_radius_top_left_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_sidebar_term_item_counter_border_radius_top_left_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_sidebar_settings',
                    'settings'    => 'betterdocs_sidebar_term_item_counter_border_radius_top_left_layout6',
                    'label'       => __( 'Top Left', 'betterdocs-pro' ),
                    'priority'    => 347,
                    'input_attrs' => [
                        'class' => 'betterdocs_sidebar_term_item_counter_border_radius_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Sidebar Term List Item Counter Border Radius Top Right Layout 6 (For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_term_item_counter_border_radius_top_right_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_item_counter_border_radius_top_right_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_sidebar_term_item_counter_border_radius_top_right_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_sidebar_settings',
                    'settings'    => 'betterdocs_sidebar_term_item_counter_border_radius_top_right_layout6',
                    'label'       => __( 'Top Right', 'betterdocs-pro' ),
                    'priority'    => 348,
                    'input_attrs' => [
                        'class' => 'betterdocs_sidebar_term_item_counter_border_radius_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Sidebar Term List Item Counter Border Radius Bottom Right Layout 6 (For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_term_item_counter_border_radius_bottom_right_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_item_counter_border_radius_bottom_right_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_sidebar_term_item_counter_border_radius_bottom_right_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_sidebar_settings',
                    'settings'    => 'betterdocs_sidebar_term_item_counter_border_radius_bottom_right_layout6',
                    'label'       => __( 'Bottom Right', 'betterdocs-pro' ),
                    'priority'    => 349,
                    'input_attrs' => [
                        'class' => 'betterdocs_sidebar_term_item_counter_border_radius_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Sidebar Term List Item Counter Border Radius Bottom Left Layout 6 (For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_term_item_counter_border_radius_bottom_left_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_item_counter_border_radius_bottom_left_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_sidebar_term_item_counter_border_radius_bottom_left_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_sidebar_settings',
                    'settings'    => 'betterdocs_sidebar_term_item_counter_border_radius_bottom_left_layout6',
                    'label'       => __( 'Bottom Left', 'betterdocs-pro' ),
                    'priority'    => 350,
                    'input_attrs' => [
                        'class' => 'betterdocs_sidebar_term_item_counter_border_radius_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );
    }

    public function item_counter_padding_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_term_item_counter_padding_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_item_counter_padding_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_sidebar_term_item_counter_padding_layout6', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_term_item_counter_padding_layout6',
                'label'       => __( 'Count Padding', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_sidebar_term_item_counter_padding_layout6',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 351
            ] )
        );

        // Sidebar Term List Item Counter Padding Top Layout 6 (For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_term_item_counter_padding_top_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_item_counter_padding_top_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_term_item_counter_padding_top_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_term_item_counter_padding_top_layout6',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_term_item_counter_padding_layout6 betterdocs-dimension'
                ],
                'priority'    => 352
            ] )
        );

        // Sidebar Term List Item Counter Padding Right Layout 6 (For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_term_item_counter_padding_right_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_item_counter_padding_right_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_term_item_counter_padding_right_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_term_item_counter_padding_right_layout6',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_term_item_counter_padding_layout6 betterdocs-dimension'
                ],
                'priority'    => 353
            ] )
        );

        // Sidebar Term List Item Counter Padding Bottom Layout 6 (For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_term_item_counter_padding_bottom_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_item_counter_padding_bottom_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_term_item_counter_padding_bottom_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_term_item_counter_padding_bottom_layout6',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_term_item_counter_padding_layout6 betterdocs-dimension'
                ],
                'priority'    => 354
            ] )
        );

        // Sidebar Term List Item Counter Padding Left Layout 6 (For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_term_item_counter_padding_left_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_item_counter_padding_left_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_term_item_counter_padding_left_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_term_item_counter_padding_left_layout6',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_term_item_counter_padding_layout6 betterdocs-dimension'
                ],
                'priority'    => 355
            ] )
        );
    }

    public function item_counter_margin_layout6() {
        $this->customizer->add_setting( 'betterdocs_sidebar_term_item_counter_margin_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_item_counter_margin_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_sidebar_term_item_counter_margin_layout6', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_term_item_counter_margin_layout6',
                'label'       => __( 'Count Margin', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_sidebar_term_item_counter_margin_layout6',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 356
            ] ) );

        // Sidebar Term List Item Counter Margin Top Layout 6 (For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_term_item_counter_margin_top_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_item_counter_margin_top_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_term_item_counter_margin_top_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_term_item_counter_margin_top_layout6',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_term_item_counter_margin_layout6 betterdocs-dimension'
                ],
                'priority'    => 357
            ] )
        );

        // Sidebar Term List Item Counter Margin Right Layout 6 (For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_term_item_counter_margin_right_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_item_counter_margin_right_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_term_item_counter_margin_right_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_term_item_counter_margin_right_layout6',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_term_item_counter_margin_layout6 betterdocs-dimension'
                ],
                'priority'    => 358
            ] )
        );

        // Sidebar Term List Item Counter Margin Bottom Layout 6 (For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_term_item_counter_margin_bottom_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_item_counter_margin_bottom_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_term_item_counter_margin_bottom_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_term_item_counter_margin_bottom_layout6',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_term_item_counter_margin_layout6 betterdocs-dimension'
                ],
                'priority'    => 359
            ] )
        );

        // Sidebar Term List Item Counter Margin Left Layout 6 (For Single Doc Layout 6)
        $this->customizer->add_setting( 'betterdocs_sidebar_term_item_counter_margin_left_layout6', [
            'default'           => $this->defaults['betterdocs_sidebar_term_item_counter_margin_left_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_sidebar_term_item_counter_margin_left_layout6', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_sidebar_settings',
                'settings'    => 'betterdocs_sidebar_term_item_counter_margin_left_layout6',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_sidebar_term_item_counter_margin_layout6 betterdocs-dimension'
                ],
                'priority'    => 360
            ] )
        );
    }
}
