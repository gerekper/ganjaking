<?php
namespace WPDeveloper\BetterDocsPro\Admin\Customizer\Sections;

use WP_Customize_Control;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\TitleControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\ToggleControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\DimensionControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\SeparatorControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\AlphaColorControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\RangeValueControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Sections\LiveSearch as FreeLiveSearch;

class LiveSearch extends FreeLiveSearch {
    public function category_search() {
        $this->customizer->add_setting( 'betterdocs_category_search_toggle', [
            'default'    => $this->defaults['betterdocs_category_search_toggle'],
            'capability' => 'edit_theme_options'

        ] );

        $this->customizer->add_control( new ToggleControl(
            $this->customizer, 'betterdocs_category_search_toggle', [
                'label'    => __( 'Enable Category Search', 'betterdocs' ),
                'section'  => 'betterdocs_live_search_settings',
                'settings' => 'betterdocs_category_search_toggle',
                'type'     => 'light', // light, ios, flat
                'priority' => 500
            ] )
        );
    }

    public function search_button() {
        $this->customizer->add_setting( 'betterdocs_search_button_toggle', [
            'default'    => $this->defaults['betterdocs_search_button_toggle'],
            'capability' => 'edit_theme_options'

        ] );

        $this->customizer->add_control( new ToggleControl(
            $this->customizer, 'betterdocs_search_button_toggle', [
                'label'    => __( 'Enable Search Button', 'betterdocs' ),
                'section'  => 'betterdocs_live_search_settings',
                'settings' => 'betterdocs_search_button_toggle',
                'type'     => 'light', // light, ios, flat
                'priority' => 501
            ] )
        );
    }

    public function popular_search() {
        $this->customizer->add_setting( 'betterdocs_popular_search_toggle', [
            'default'    => $this->defaults['betterdocs_popular_search_toggle'],
            'capability' => 'edit_theme_options'

        ] );

        $this->customizer->add_control( new ToggleControl(
            $this->customizer, 'betterdocs_popular_search_toggle', [
                'label'    => __( 'Enable Popular Search', 'betterdocs' ),
                'section'  => 'betterdocs_live_search_settings',
                'settings' => 'betterdocs_popular_search_toggle',
                'type'     => 'light', // light, ios, flat
                'priority' => 501
            ] )
        );
    }

    public function category_select_settings() {
        $this->customizer->add_setting( 'betterdocs_category_select_search_section', [
            'default'           => $this->defaults['betterdocs_category_select_search_section'],
            'sanitize_callback' => 'esc_html'
        ] );

        $this->customizer->add_control( new SeparatorControl(
            $this->customizer, 'betterdocs_category_select_search_section', [
                'label'    => esc_html__( 'Category Select Settings', 'betterdocs-pro' ),
                'settings' => 'betterdocs_category_select_search_section',
                'section'  => 'betterdocs_live_search_settings',
                'priority' => 570
            ] ) );
    }

    public function category_select_font_size() {
        //Category Select Font Size
        $this->customizer->add_setting( 'betterdocs_category_select_font_size', [
            'default'           => $this->defaults['betterdocs_category_select_font_size'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_category_select_font_size', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_category_select_font_size',
                'label'       => esc_html__( 'Font Size', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 200,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ],
                'priority'    => 572
            ] ) );
    }

    public function category_select_font_weight() {
        //Category Select Font Weight
        $this->customizer->add_setting( 'betterdocs_category_select_font_weight', [
            'default'           => $this->defaults['betterdocs_category_select_font_weight'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'choices']
        ] );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_category_select_font_weight',
                [
                    'label'    => esc_html__( 'Font Weight', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_live_search_settings',
                    'settings' => 'betterdocs_category_select_font_weight',
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
                    'priority' => 573
                ] )
        );
    }

    public function category_select_text_transform() {
        $this->customizer->add_setting( 'betterdocs_category_select_text_transform', [
            'default'           => $this->defaults['betterdocs_category_select_text_transform'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'choices']
        ] );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_category_select_text_transform',
                [
                    'label'    => esc_html__( 'Font Text Transform', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_live_search_settings',
                    'settings' => 'betterdocs_category_select_text_transform',
                    'type'     => 'select',
                    'choices'  => [
                        'none'       => 'none',
                        'capitalize' => 'capitalize',
                        'uppercase'  => 'uppercase',
                        'lowercase'  => 'lowercase',
                        'initial'    => 'initial',
                        'inherit'    => 'inherit'
                    ],
                    'priority' => 574
                ] )
        );
    }

    public function category_select_text_color() {
        $this->customizer->add_setting( 'betterdocs_category_select_text_color', [
            'default'           => $this->defaults['betterdocs_category_select_text_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_category_select_text_color',
                [
                    'label'    => esc_html__( 'Font Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_live_search_settings',
                    'settings' => 'betterdocs_category_select_text_color',
                    'priority' => 575
                ] )
        );
    }

    public function search_button_settings() {
        $this->customizer->add_setting( 'betterdocs_search_button_section', [
            'default'           => $this->defaults['betterdocs_search_button_section'],
            'sanitize_callback' => 'esc_html'
        ] );

        $this->customizer->add_control( new SeparatorControl(
            $this->customizer, 'betterdocs_search_button_section', [
                'label'    => esc_html__( 'Search Button Settings', 'betterdocs-pro' ),
                'settings' => 'betterdocs_search_button_section',
                'section'  => 'betterdocs_live_search_settings',
                'priority' => 576
            ] ) );
    }

    public function search_button_font_size() {
        $this->customizer->add_setting( 'betterdocs_new_search_button_font_size', [
            'default'           => $this->defaults['betterdocs_new_search_button_font_size'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_new_search_button_font_size', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_new_search_button_font_size',
                'label'       => esc_html__( 'Font Size', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 200,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ],
                'priority'    => 578
            ] ) );
    }

    public function search_button_letter_spacing() {
        //Search Button Letter Spacing
        $this->customizer->add_setting( 'betterdocs_new_search_button_letter_spacing', [
            'default'           => $this->defaults['betterdocs_new_search_button_letter_spacing'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_new_search_button_letter_spacing', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_new_search_button_letter_spacing',
                'label'       => esc_html__( 'Font Letter Spacing', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 200,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ],
                'priority'    => 579
            ] ) );
    }

    public function search_button_font_weight() {
        //Search Button Font Weight
        $this->customizer->add_setting( 'betterdocs_new_search_button_font_weight', [
            'default'           => $this->defaults['betterdocs_new_search_button_font_weight'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'choices']
        ] );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_new_search_button_font_weight',
                [
                    'label'    => esc_html__( 'Font Weight', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_live_search_settings',
                    'settings' => 'betterdocs_new_search_button_font_weight',
                    'type'     => 'select',
                    'choices'  => [
                        '100' => '100',
                        '200' => '200',
                        '300' => '300',
                        '400' => '400',
                        '500' => '500',
                        '600' => '600',
                        '700' => '700',
                        '800' => '800',
                        '900' => '900'
                    ],
                    'priority' => 579
                ] )
        );
    }

    public function search_button_text_transform() {
        //Search Button Text Transform
        $this->customizer->add_setting( 'betterdocs_new_search_button_text_transform', [
            'default'           => $this->defaults['betterdocs_new_search_button_text_transform'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'choices']
        ] );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_new_search_button_text_transform',
                [
                    'label'    => esc_html__( 'Font Text Transform', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_live_search_settings',
                    'settings' => 'betterdocs_new_search_button_text_transform',
                    'type'     => 'select',
                    'choices'  => [
                        'none'       => 'none',
                        'capitalize' => 'capitalize',
                        'uppercase'  => 'uppercase',
                        'lowercase'  => 'lowercase',
                        'initial'    => 'initial',
                        'inherit'    => 'inherit'
                    ],
                    'priority' => 580
                ] )
        );
    }

    public function search_button_text_color() {
        // Search Button Text Color
        $this->customizer->add_setting( 'betterdocs_search_button_text_color', [
            'default'           => $this->defaults['betterdocs_search_button_text_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_search_button_text_color',
                [
                    'label'    => esc_html__( 'Text Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_live_search_settings',
                    'settings' => 'betterdocs_search_button_text_color',
                    'priority' => 582
                ] )
        );
    }

    public function search_button_background_color() {
        // Search Button Background Color
        $this->customizer->add_setting( 'betterdocs_search_button_background_color', [
            'default'           => $this->defaults['betterdocs_search_button_background_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_search_button_background_color',
                [
                    'label'    => esc_html__( 'Background Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_live_search_settings',
                    'settings' => 'betterdocs_search_button_background_color',
                    'priority' => 583
                ] )
        );
    }

    public function search_button_background_color_hover() {
        $this->customizer->add_setting( 'betterdocs_search_button_background_color_hover', [
            'default'           => $this->defaults['betterdocs_search_button_background_color_hover'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_search_button_background_color_hover',
                [
                    'label'    => esc_html__( 'Background Hover Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_live_search_settings',
                    'settings' => 'betterdocs_search_button_background_color_hover',
                    'priority' => 583
                ] )
        );
    }

    public function search_button_border_radius() {
        $this->customizer->add_setting( 'betterdocs_search_button_borderr_radius', [
            'default'           => $this->defaults['betterdocs_search_button_borderr_radius'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_search_button_borderr_radius', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_search_button_borderr_radius',
                'label'       => esc_html__( 'Border Radius', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_search_button_borderr_radius',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 584
            ] ) );
    }

    public function border_radius_top_left() {
        $this->customizer->add_setting( 'betterdocs_search_button_borderr_left_top', [
            'default'           => $this->defaults['betterdocs_search_button_borderr_left_top'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_search_button_borderr_left_top', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_search_button_borderr_left_top',
                'label'       => esc_html__( 'Left Top', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_search_button_borderr_radius betterdocs-dimension'
                ],
                'priority'    => 584
            ] ) );
    }

    public function border_radius_top_right() {
        $this->customizer->add_setting( 'betterdocs_search_button_borderr_right_top', [
            'default'           => $this->defaults['betterdocs_search_button_borderr_right_top'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_search_button_borderr_right_top', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_search_button_borderr_right_top',
                'label'       => esc_html__( 'Right Top', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_search_button_borderr_radius betterdocs-dimension'
                ],
                'priority'    => 584
            ] ) );
    }

    public function border_radius_bottom_left() {
        $this->customizer->add_setting( 'betterdocs_search_button_borderr_left_bottom', [
            'default'           => $this->defaults['betterdocs_search_button_borderr_left_bottom'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_search_button_borderr_left_bottom', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_search_button_borderr_left_bottom',
                'label'       => esc_html__( 'Left Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_search_button_borderr_radius betterdocs-dimension'
                ],
                'priority'    => 584
            ] ) );
    }

    public function border_radius_bottom_right() {
        $this->customizer->add_setting( 'betterdocs_search_button_borderr_right_bottom', [
            'default'           => $this->defaults['betterdocs_search_button_borderr_right_bottom'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_search_button_borderr_right_bottom', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_search_button_borderr_right_bottom',
                'label'       => esc_html__( 'Right Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_search_button_borderr_radius betterdocs-dimension'
                ],
                'priority'    => 584
            ] ) );
    }

    public function button_padding() {
        $this->customizer->add_setting( 'betterdocs_search_button_padding', [
            'default'           => $this->defaults['betterdocs_search_button_padding'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_search_button_padding', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_search_button_padding',
                'label'       => esc_html__( 'Padding', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_search_button_padding',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 589
            ] ) );
    }

    public function button_padding_top() {
        $this->customizer->add_setting( 'betterdocs_search_button_padding_top',
            apply_filters( 'betterdocs_search_button_padding_top', [
                'default'           => $this->defaults['betterdocs_search_button_padding_top'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ] )
        );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_search_button_padding_top', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_search_button_padding_top',
                'label'       => esc_html__( 'Top', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_search_button_padding betterdocs-dimension'
                ],
                'priority'    => 589
            ] ) );
    }

    public function button_padding_right() {
        $this->customizer->add_setting( 'betterdocs_search_button_padding_right',
            apply_filters( 'betterdocs_search_button_padding_right', [
                'default'           => $this->defaults['betterdocs_search_button_padding_right'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ] )
        );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_search_button_padding_right', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_search_button_padding_right',
                'label'       => esc_html__( 'Right', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_search_button_padding betterdocs-dimension'
                ],
                'priority'    => 589
            ] ) );
    }

    public function button_padding_bottom() {
        $this->customizer->add_setting( 'betterdocs_search_button_padding_bottom',
            apply_filters( 'betterdocs_search_button_padding_bottom', [
                'default'           => $this->defaults['betterdocs_search_button_padding_bottom'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ] )
        );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_search_button_padding_bottom', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_search_button_padding_bottom',
                'label'       => esc_html__( 'Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_search_button_padding betterdocs-dimension'
                ],
                'priority'    => 589
            ] )
        );
    }

    public function button_padding_left() {
        $this->customizer->add_setting( 'betterdocs_search_button_padding_left', [
            'default'           => $this->defaults['betterdocs_search_button_padding_left'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_search_button_padding_left', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_search_button_padding_left',
                'label'       => esc_html__( 'Left', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_search_button_padding betterdocs-dimension'
                ],
                'priority'    => 589
            ] )
        );
    }

    public function popular_search_settings() {
        $this->customizer->add_setting( 'betterdocs_popular_search_section', [
            'default'           => $this->defaults['betterdocs_popular_search_section'],
            'sanitize_callback' => 'esc_html'
        ] );

        $this->customizer->add_control( new SeparatorControl(
            $this->customizer, 'betterdocs_popular_search_section', [
                'label'    => esc_html__( 'Popular Search Settings', 'betterdocs-pro' ),
                'settings' => 'betterdocs_popular_search_section',
                'section'  => 'betterdocs_live_search_settings',
                'priority' => 599
            ] ) );
    }

    public function popular_search_margin() {
        $this->customizer->add_setting( 'betterdocs_popular_search_margin', [
            'default'           => $this->defaults['betterdocs_popular_search_margin'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_popular_search_margin', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_search_margin',
                'label'       => esc_html__( 'Popular Search Margin', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_popular_search_margin',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 601
            ] ) );
    }

    public function popular_search_margin_top() {
        $this->customizer->add_setting( 'betterdocs_popular_search_margin_top',
            apply_filters( 'betterdocs_popular_search_margin_top', [
                'default'           => $this->defaults['betterdocs_popular_search_margin_top'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ] )
        );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_popular_search_margin_top', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_search_margin_top',
                'label'       => esc_html__( 'Top', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_popular_search_margin betterdocs-dimension'
                ],
                'priority'    => 601
            ] ) );

    }

    public function popular_search_margin_right() {
        $this->customizer->add_setting( 'betterdocs_popular_search_margin_right',
            apply_filters( 'betterdocs_popular_search_margin_right', [
                'default'           => $this->defaults['betterdocs_popular_search_margin_right'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ] )
        );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_popular_search_margin_right', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_search_margin_right',
                'label'       => esc_html__( 'Right', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_popular_search_margin betterdocs-dimension'
                ],
                'priority'    => 601
            ] ) );
    }

    public function popular_search_margin_bottom() {
        $this->customizer->add_setting( 'betterdocs_popular_search_margin_bottom',
            apply_filters( 'betterdocs_popular_search_margin_bottom', [
                'default'           => $this->defaults['betterdocs_popular_search_margin_bottom'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ] )
        );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_popular_search_margin_bottom', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_search_margin_bottom',
                'label'       => esc_html__( 'Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_popular_search_margin betterdocs-dimension'
                ],
                'priority'    => 601
            ] ) );
    }

    public function popular_search_margin_left() {
        $this->customizer->add_setting( 'betterdocs_popular_search_margin_left', [
            'default'           => $this->defaults['betterdocs_popular_search_margin_left'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_popular_search_margin_left', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_search_margin_left',
                'label'       => esc_html__( 'Left', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_popular_search_margin betterdocs-dimension'
                ],
                'priority'    => 601
            ] ) );
    }

    public function popular_search_text_subheading() {
        $this->customizer->add_setting( 'betterdocs_popular_search_text', [
            'default'           => $this->defaults['betterdocs_popular_search_text'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => 'esc_html'
        ] );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_popular_search_text',
                [
                    'label'    => esc_html__( 'Sub Heading', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_live_search_settings',
                    'settings' => 'betterdocs_popular_search_text',
                    'type'     => 'text',
                    'priority' => 606
                ]
            )
        );
    }

    public function popular_title_text_color() {
        $this->customizer->add_setting( 'betterdocs_popular_search_title_text_color', [
            'default'           => $this->defaults['betterdocs_popular_search_title_text_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_popular_search_title_text_color',
                [
                    'label'    => esc_html__( 'Title Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_live_search_settings',
                    'settings' => 'betterdocs_popular_search_title_text_color',
                    'priority' => 606
                ] )
        );
    }

    public function popular_title_font_size() {
        //Popular Title Font Size
        $this->customizer->add_setting( 'betterdocs_popular_search_title_font_size', [
            'default'           => $this->defaults['betterdocs_popular_search_title_font_size'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_popular_search_title_font_size', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_search_title_font_size',
                'label'       => esc_html__( 'Title Font Size', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 200,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ],
                'priority'    => 607
            ] ) );
    }

    public function popular_search_font_size() {
        $this->customizer->add_setting( 'betterdocs_popular_search_font_size', [
            'default'           => $this->defaults['betterdocs_popular_search_font_size'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_popular_search_font_size', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_search_font_size',
                'label'       => esc_html__( 'Keyword Font Size', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 200,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ],
                'priority'    => 608
            ] ) );
    }

    public function popular_search_keyword_border() {
        //Keyword Border Type
        $this->customizer->add_setting( 'betterdocs_popular_search_keyword_border', [
            'default'           => $this->defaults['betterdocs_popular_search_keyword_border'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'choices']
        ] );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_popular_search_keyword_border',
                [
                    'label'    => esc_html__( 'Keyword Border Type', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_live_search_settings',
                    'settings' => 'betterdocs_popular_search_keyword_border',
                    'type'     => 'select',
                    'choices'  => [
                        'none'   => 'none',
                        'solid'  => 'solid',
                        'double' => 'double',
                        'dotted' => 'dotted',
                        'dashed' => 'dashed',
                        'groove' => 'groove'
                    ],
                    'priority' => 608
                ] )
        );
    }

    public function popular_keyword_border_color() {
        $this->customizer->add_setting( 'betterdocs_popular_search_keyword_border_color', [
            'default'           => $this->defaults['betterdocs_popular_search_keyword_border_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_popular_search_keyword_border_color',
                [
                    'label'    => esc_html__( 'Keyword Border Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_live_search_settings',
                    'settings' => 'betterdocs_popular_search_keyword_border_color',
                    'priority' => 608
                ] )
        );
    }

    public function popular_keyword_border_width() {
        //Keyword Border Width
        $this->customizer->add_setting( 'betterdocs_popular_search_keyword_border_width', [
            'default'           => $this->defaults['betterdocs_popular_search_keyword_border_width'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_popular_search_keyword_border_width', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_search_keyword_border_width',
                'label'       => esc_html__( 'Keyword Border Width', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_popular_search_keyword_border_width',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 608
            ] ) );
    }

    public function popular_keyword_border_width_top() {
        $this->customizer->add_setting( 'betterdocs_popular_search_keyword_border_width_top',
            apply_filters( 'betterdocs_popular_search_keyword_border_width_top', [
                'default'           => $this->defaults['betterdocs_popular_search_keyword_border_width_top'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ] )
        );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_popular_search_keyword_border_width_top', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_search_keyword_border_width_top',
                'label'       => esc_html__( 'Top', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_popular_search_keyword_border_width betterdocs-dimension'
                ],
                'priority'    => 608
            ] ) );
    }

    public function popular_keyword_border_width_right() {
        $this->customizer->add_setting( 'betterdocs_popular_search_keyword_border_width_right',
            apply_filters( 'betterdocs_popular_search_padding_right', [
                'default'           => $this->defaults['betterdocs_popular_search_keyword_border_width_right'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ] )
        );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_popular_search_keyword_border_width_right', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_search_keyword_border_width_right',
                'label'       => esc_html__( 'Right', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_popular_search_keyword_border_width betterdocs-dimension'
                ],
                'priority'    => 608
            ] ) );
    }

    public function popular_keyword_border_width_bottom() {
        $this->customizer->add_setting( 'betterdocs_popular_search_keyword_border_width_bottom',
            apply_filters( 'betterdocs_popular_search_keyword_border_width_bottom', [
                'default'           => $this->defaults['betterdocs_popular_search_keyword_border_width_bottom'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ] )
        );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_popular_search_keyword_border_width_bottom', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_search_keyword_border_width_bottom',
                'label'       => esc_html__( 'Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_popular_search_keyword_border_width betterdocs-dimension'
                ],
                'priority'    => 608
            ] ) );
    }

    public function popular_keyword_border_width_left() {
        $this->customizer->add_setting( 'betterdocs_popular_search_keyword_border_width_left', [
            'default'           => $this->defaults['betterdocs_popular_search_keyword_border_width_left'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_popular_search_keyword_border_width_left', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_search_keyword_border_width_left',
                'label'       => esc_html__( 'Left', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_popular_search_keyword_border_width betterdocs-dimension'
                ],
                'priority'    => 608
            ] ) );
    }

    public function popular_search_background_color() {
        $this->customizer->add_setting( 'betterdocs_popular_search_background_color', [
            'default'           => $this->defaults['betterdocs_popular_search_background_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_popular_search_background_color',
                [
                    'label'    => esc_html__( 'Keyword Background Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_live_search_settings',
                    'settings' => 'betterdocs_popular_search_background_color',
                    'priority' => 609
                ] )
        );
    }

    public function popular_search_keyword_text_color() {
        $this->customizer->add_setting( 'betterdocs_popular_keyword_text_color', [
            'default'           => $this->defaults['betterdocs_popular_keyword_text_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_popular_keyword_text_color',
                [
                    'label'    => esc_html__( 'Keyword Text Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_live_search_settings',
                    'settings' => 'betterdocs_popular_keyword_text_color',
                    'priority' => 610
                ] )
        );
    }

    public function popular_search_keyword_border_radius() {
        $this->customizer->add_setting( 'betterdocs_popular_keyword_border_radius', [
            'default'           => $this->defaults['betterdocs_popular_keyword_border_radius'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_popular_keyword_border_radius', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_keyword_border_radius',
                'label'       => esc_html__( 'Keyword Border Radius', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_popular_keyword_border_radius',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 610
            ] ) );
    }

    public function popular_search_keyword_border_radius_top_left() {
        $this->customizer->add_setting( 'betterdocs_popular_keyword_border_radius_left_top', [
            'default'           => $this->defaults['betterdocs_popular_keyword_border_radius_left_top'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_popular_keyword_border_radius_left_top', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_keyword_border_radius_left_top',
                'label'       => esc_html__( 'Left Top', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_popular_keyword_border_radius betterdocs-dimension'
                ],
                'priority'    => 610
            ] ) );
    }

    public function popular_search_keyword_border_radius_top_right() {
        $this->customizer->add_setting( 'betterdocs_popular_keyword_border_radius_right_top', [
            'default'           => $this->defaults['betterdocs_popular_keyword_border_radius_right_top'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_popular_keyword_border_radius_right_top', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_keyword_border_radius_right_top',
                'label'       => esc_html__( 'Right Top', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_popular_keyword_border_radius betterdocs-dimension'
                ],
                'priority'    => 610
            ] ) );
    }

    public function popular_search_keyword_border_radius_bottom_right() {
        $this->customizer->add_setting( 'betterdocs_popular_keyword_border_radius_right_bottom', [
            'default'           => $this->defaults['betterdocs_popular_keyword_border_radius_right_bottom'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_popular_keyword_border_radius_right_bottom', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_keyword_border_radius_right_bottom',
                'label'       => esc_html__( 'Right Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_popular_keyword_border_radius betterdocs-dimension'
                ],
                'priority'    => 610
            ] ) );
    }

    public function popular_search_keyword_border_radius_bottom_left() {
        $this->customizer->add_setting( 'betterdocs_popular_keyword_border_radius_left_bottom', [
            'default'           => $this->defaults['betterdocs_popular_keyword_border_radius_left_bottom'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_popular_keyword_border_radius_left_bottom', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_keyword_border_radius_left_bottom',
                'label'       => esc_html__( 'Left Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_popular_keyword_border_radius betterdocs-dimension'
                ],
                'priority'    => 610
            ] ) );
    }

    public function popular_search_padding() {
        $this->customizer->add_setting( 'betterdocs_popular_search_padding', [
            'default'           => $this->defaults['betterdocs_popular_search_padding'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_popular_search_padding', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_search_padding',
                'label'       => esc_html__( 'Keyword Padding', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_popular_search_padding',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 611
            ] ) );
    }

    public function popular_search_padding_top() {
        $this->customizer->add_setting( 'betterdocs_popular_search_padding_top',
            apply_filters( 'betterdocs_popular_search_padding_top', [
                'default'           => $this->defaults['betterdocs_popular_search_padding_top'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ] )
        );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_popular_search_padding_top', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_search_padding_top',
                'label'       => esc_html__( 'Top', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_popular_search_padding betterdocs-dimension'
                ],
                'priority'    => 611
            ] ) );
    }

    public function popular_search_padding_right() {
        $this->customizer->add_setting( 'betterdocs_popular_search_padding_right',
            apply_filters( 'betterdocs_popular_search_padding_right', [
                'default'           => $this->defaults['betterdocs_popular_search_padding_right'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ] )
        );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_popular_search_padding_right', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_search_padding_right',
                'label'       => esc_html__( 'Right', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_popular_search_padding betterdocs-dimension'
                ],
                'priority'    => 611
            ] ) );
    }

    public function popular_search_padding_bottom() {
        $this->customizer->add_setting( 'betterdocs_popular_search_padding_bottom',
            apply_filters( 'betterdocs_popular_search_padding_bottom', [
                'default'           => $this->defaults['betterdocs_popular_search_padding_bottom'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ] )
        );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_popular_search_padding_bottom', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_search_padding_bottom',
                'label'       => esc_html__( 'Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_popular_search_padding betterdocs-dimension'
                ],
                'priority'    => 611
            ] ) );
    }

    public function popular_search_padding_left() {
        $this->customizer->add_setting( 'betterdocs_popular_search_padding_left', [
            'default'           => $this->defaults['betterdocs_popular_search_padding_left'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_popular_search_padding_left', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_search_padding_left',
                'label'       => esc_html__( 'Left', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_popular_search_padding betterdocs-dimension'
                ],
                'priority'    => 611
            ] ) );
    }

    public function popular_search_keyword_margin() {
        $this->customizer->add_setting( 'betterdocs_popular_search_keyword_margin', [
            'default'           => $this->defaults['betterdocs_popular_search_keyword_margin'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_popular_search_keyword_margin', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_search_keyword_margin',
                'label'       => esc_html__( 'Keyword Margin', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_popular_search_keyword_margin',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 616
            ] ) );
    }

    public function popular_search_keyword_margin_top() {
        $this->customizer->add_setting( 'betterdocs_popular_search_keyword_margin_top',
            apply_filters( 'betterdocs_popular_search_keyword_margin_top', [
                'default'           => $this->defaults['betterdocs_popular_search_keyword_margin_top'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ] )
        );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_popular_search_keyword_margin_top', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_search_keyword_margin_top',
                'label'       => esc_html__( 'Top', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_popular_search_keyword_margin betterdocs-dimension'
                ],
                'priority'    => 616
            ] ) );
    }

    public function popular_search_keyword_margin_right() {
        $this->customizer->add_setting( 'betterdocs_popular_search_keyword_margin_right',
            apply_filters( 'betterdocs_popular_search_keyword_margin_right', [
                'default'           => $this->defaults['betterdocs_popular_search_keyword_margin_right'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ] )
        );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_popular_search_keyword_margin_right', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_search_keyword_margin_right',
                'label'       => esc_html__( 'Right', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_popular_search_keyword_margin betterdocs-dimension'
                ],
                'priority'    => 616
            ] ) );
    }

    public function popular_search_keyword_margin_bottom() {
        $this->customizer->add_setting( 'betterdocs_popular_search_keyword_margin_bottom',
            apply_filters( 'betterdocs_popular_search_keyword_margin_bottom', [
                'default'           => $this->defaults['betterdocs_popular_search_keyword_margin_bottom'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ] )
        );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_popular_search_keyword_margin_bottom', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_search_keyword_margin_bottom',
                'label'       => esc_html__( 'Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_popular_search_keyword_margin betterdocs-dimension'
                ],
                'priority'    => 616
            ] ) );
    }

    public function popular_search_keyword_margin_left() {
        $this->customizer->add_setting( 'betterdocs_popular_search_keyword_margin_left', [
            'default'           => $this->defaults['betterdocs_popular_search_keyword_margin_left'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_popular_search_keyword_margin_left', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_live_search_settings',
                'settings'    => 'betterdocs_popular_search_keyword_margin_left',
                'label'       => esc_html__( 'Left', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_popular_search_keyword_margin betterdocs-dimension'
                ],
                'priority'    => 616
            ] ) );
    }
}
