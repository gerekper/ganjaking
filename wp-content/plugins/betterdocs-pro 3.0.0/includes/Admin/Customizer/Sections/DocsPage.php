<?php

namespace WPDeveloper\BetterDocsPro\Admin\Customizer\Sections;

use WP_Customize_Control;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\TitleControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\ToggleControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\DimensionControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\SeparatorControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\AlphaColorControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\RangeValueControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Sections\DocsPage as FreeDocsPage;

class DocsPage extends FreeDocsPage {
    public function layout() {
        $this->customizer->add_setting(
            'betterdocs_doc_list_desc_switch_layout6',
            [
                'default'           => $this->defaults['betterdocs_doc_list_desc_switch_layout6'],
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => [$this->sanitizer, 'checkbox']
            ]
        );

        $this->customizer->add_control(
            new ToggleControl(
                $this->customizer, 'betterdocs_doc_list_desc_switch_layout6',
                [
                    'label'    => __( 'Category Description PRO', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_list_desc_switch_layout6',
                    'type'     => 'light', // light, ios, flat
                    'priority' => 56
                ]
            )
        );
    }

    public function list_img_switch_layout6() {
        $this->customizer->add_setting(
            'betterdocs_doc_list_img_switch_layout6',
            [
                'default'           => $this->defaults['betterdocs_doc_list_img_switch_layout6'],
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => [$this->sanitizer, 'checkbox']
            ]
        );

        $this->customizer->add_control(
            new ToggleControl(
                $this->customizer, 'betterdocs_doc_list_img_switch_layout6',
                [
                    'label'    => __( 'Category Image', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_list_img_switch_layout6',
                    'type'     => 'light', // light, ios, flat
                    'priority' => 17
                ]
            )
        );
    }

    public function list_img_width_layout6() {
        $this->customizer->add_setting( 'betterdocs_doc_list_img_width_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_img_width_layout6'],
            'transport'         => 'postMessage',
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ]
        );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_doc_list_img_width_layout6', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_list_img_width_layout6',
                'label'       => __( 'Category Image Width', 'betterdocs-pro' ),
                'priority'    => 17,
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

    public function title_padding_bottom_layout6() {
        $this->customizer->add_setting( 'betterdocs_doc_page_cat_title_padding_bottom_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_cat_title_padding_bottom_layout6'],
            'transport'         => 'postMessage',
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ]
        );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_doc_page_cat_title_padding_bottom_layout6', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_cat_title_padding_bottom_layout6',
                'label'       => __( 'Category Title Padding Bottom', 'betterdocs-pro' ),
                'priority'    => 17,
                'input_attrs' => [
                    'class'  => 'betterdocs-range-value',
                    'min'    => 0,
                    'max'    => 500,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ]
            ] )
        );
    }

    public function title_font_size_layout6() {
        $this->customizer->add_setting( 'betterdocs_doc_page_cat_title_font_size_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_cat_title_font_size_layout6'],
            'transport'         => 'postMessage',
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ]
        );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_doc_page_cat_title_font_size_layout6', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_cat_title_font_size_layout6',
                'label'       => __( 'Category Title Font Size', 'betterdocs-pro' ),
                'priority'    => 17,
                'input_attrs' => [
                    'class'  => 'betterdocs-range-value',
                    'min'    => 0,
                    'max'    => 500,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ]
            ] )
        );
    }

    public function item_count_font_size_layout6() {
        $this->customizer->add_setting( 'betterdocs_doc_page_item_count_font_size_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_item_count_font_size_layout6'],
            'transport'         => 'postMessage',
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ]
        );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_doc_page_item_count_font_size_layout6', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_item_count_font_size_layout6',
                'label'       => __( 'Item Count Font Size', 'betterdocs-pro' ),
                'priority'    => 29,
                'input_attrs' => [
                    'class'  => 'betterdocs-range-value',
                    'min'    => 0,
                    'max'    => 500,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ]
            ] )
        );
    }

    public function item_count_color_layout6() {
        $this->customizer->add_setting( 'betterdocs_doc_page_item_count_color_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_item_count_color_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_doc_page_item_count_color_layout6',
                [
                    'label'    => __( 'Item Count Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_page_item_count_color_layout6',
                    'priority' => 30
                ]
            )
        );
    }

    public function item_count_back_color_layout6() {
        $this->customizer->add_setting( 'betterdocs_doc_page_item_count_back_color_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_item_count_back_color_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_doc_page_item_count_back_color_layout6',
                [
                    'label'    => __( 'Item Count Background Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_page_item_count_back_color_layout6',
                    'priority' => 29
                ]
            )
        );
    }

    public function item_count_border_type_layout6() {
        $this->customizer->add_setting( 'betterdocs_doc_page_item_count_border_type_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_item_count_border_type_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'choices']
        ] );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_doc_page_item_count_border_type_layout6',
                [
                    'label'    => __( 'Item Count Border Style', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_page_item_count_border_type_layout6',
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
                    'priority' => 30
                ] )
        );
    }

    public function item_count_border_color_layout6() {
        $this->customizer->add_setting( 'betterdocs_doc_page_item_count_border_color_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_item_count_border_color_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_doc_page_item_count_border_color_layout6',
                [
                    'label'    => __( 'Item Count Border Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_page_item_count_border_color_layout6',
                    'priority' => 31
                ]
            )
        );
    }

    public function tem_count_border_width_layout6() {
        $this->customizer->add_setting( 'betterdocs_doc_page_item_count_border_width_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_item_count_border_width_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_doc_page_item_count_border_width_layout6', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_item_count_border_width_layout6',
                'label'       => __( 'Item Count Border Width', 'betterdocs-pro' ),
                'priority'    => 32,
                'input_attrs' => [
                    'id'    => 'betterdocs_doc_page_item_count_border_width_layout6',
                    'class' => 'betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_doc_page_item_count_border_width_top_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_item_count_border_width_top_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_page_item_count_border_width_top_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_page_item_count_border_width_top_layout6',
                    'label'       => __( 'Top', 'betterdocs-pro' ),
                    'priority'    => 33,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_page_item_count_border_width_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        $this->customizer->add_setting( 'betterdocs_doc_page_item_count_border_width_right_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_item_count_border_width_right_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_page_item_count_border_width_right_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_page_item_count_border_width_right_layout6',
                    'label'       => __( 'Right', 'betterdocs-pro' ),
                    'priority'    => 34,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_page_item_count_border_width_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Item Count Border Width Bottom (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_page_item_count_border_width_bottom_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_item_count_border_width_bottom_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_page_item_count_border_width_bottom_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_page_item_count_border_width_bottom_layout6',
                    'label'       => __( 'Bottom', 'betterdocs-pro' ),
                    'priority'    => 35,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_page_item_count_border_width_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Item Count Border Width Left (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_page_item_count_border_width_left_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_item_count_border_width_left_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_page_item_count_border_width_left_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_page_item_count_border_width_left_layout6',
                    'label'       => __( 'Left', 'betterdocs-pro' ),
                    'priority'    => 36,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_page_item_count_border_width_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );
    }

    public function item_count_border_radius_layout6() {
        $this->customizer->add_setting( 'betterdocs_doc_page_item_count_border_radius_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_item_count_border_radius_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_doc_page_item_count_border_radius_layout6', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_item_count_border_radius_layout6',
                'label'       => __( 'Item Count Border Radius', 'betterdocs-pro' ),
                'priority'    => 37,
                'input_attrs' => [
                    'id'    => 'betterdocs_doc_page_item_count_border_radius_layout6',
                    'class' => 'betterdocs-dimension'
                ]
            ] ) );

        // Item Count Border Radius Top Left (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_page_item_count_border_radius_top_left_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_item_count_border_radius_top_left_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_page_item_count_border_radius_top_left_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_page_item_count_border_radius_top_left_layout6',
                    'label'       => __( 'Top Left', 'betterdocs-pro' ),
                    'priority'    => 38,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_page_item_count_border_radius_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Item Count Border Radius Top Right (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_page_item_count_border_radius_top_right_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_item_count_border_radius_top_right_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_page_item_count_border_radius_top_right_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_page_item_count_border_radius_top_right_layout6',
                    'label'       => __( 'Top Right', 'betterdocs-pro' ),
                    'priority'    => 39,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_page_item_count_border_radius_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Item Count Border Radius Bottom Right (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_page_item_count_border_radius_bottom_right_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_item_count_border_radius_bottom_right_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_page_item_count_border_radius_bottom_right_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_page_item_count_border_radius_bottom_right_layout6',
                    'label'       => __( 'Bottom Right', 'betterdocs-pro' ),
                    'priority'    => 40,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_page_item_count_border_radius_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Item Count Border Radius Bottom Left (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_page_item_count_border_radius_bottom_left_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_item_count_border_radius_bottom_left_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_page_item_count_border_radius_bottom_left_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_page_item_count_border_radius_bottom_left_layout6',
                    'label'       => __( 'Bottom Left', 'betterdocs-pro' ),
                    'priority'    => 41,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_page_item_count_border_radius_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );
    }

    public function item_count_margin_layout6() {
        $this->customizer->add_setting( 'betterdocs_doc_page_item_count_margin_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_item_count_margin_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_doc_page_item_count_margin_layout6', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_item_count_margin_layout6',
                'label'       => __( 'Item Count Margin', 'betterdocs-pro' ),
                'priority'    => 42,
                'input_attrs' => [
                    'id'    => 'betterdocs_doc_page_item_count_margin_layout6',
                    'class' => 'betterdocs-dimension'
                ]
            ] )
        );

        // Item Count Margin Top (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_page_item_count_margin_top_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_item_count_margin_top_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_page_item_count_margin_top_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_page_item_count_margin_top_layout6',
                    'label'       => __( 'Top', 'betterdocs-pro' ),
                    'priority'    => 43,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_page_item_count_margin_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Item Count Margin Right (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_page_item_count_margin_right_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_item_count_margin_right_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_page_item_count_margin_right_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_page_item_count_margin_right_layout6',
                    'label'       => __( 'Right', 'betterdocs-pro' ),
                    'priority'    => 44,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_page_item_count_margin_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Item Count Margin Bottom (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_page_item_count_margin_bottom_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_item_count_margin_bottom_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_page_item_count_margin_bottom_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_page_item_count_margin_bottom_layout6',
                    'label'       => __( 'Bottom', 'betterdocs-pro' ),
                    'priority'    => 45,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_page_item_count_margin_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Item Count Margin Left (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_page_item_count_margin_left_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_item_count_margin_left_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_page_item_count_margin_left_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_page_item_count_margin_left_layout6',
                    'label'       => __( 'Left', 'betterdocs-pro' ),
                    'priority'    => 46,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_page_item_count_margin_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );
    }

    public function item_count_padding_layout6() {
        $this->customizer->add_setting( 'betterdocs_doc_page_item_count_padding_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_item_count_padding_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_doc_page_item_count_padding_layout6', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_item_count_padding_layout6',
                'label'       => __( 'Item Count Padding', 'betterdocs-pro' ),
                'priority'    => 47,
                'input_attrs' => [
                    'id'    => 'betterdocs_doc_page_item_count_padding_layout6',
                    'class' => 'betterdocs-dimension'
                ]
            ] ) );

        // Item Count Padding Top (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_page_item_count_padding_top_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_item_count_padding_top_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_page_item_count_padding_top_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_page_item_count_padding_top_layout6',
                    'label'       => __( 'Top', 'betterdocs-pro' ),
                    'priority'    => 48,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_page_item_count_padding_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Item Count Padding Right (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_page_item_count_padding_right_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_item_count_padding_right_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_page_item_count_padding_right_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_page_item_count_padding_right_layout6',
                    'label'       => __( 'Right', 'betterdocs-pro' ),
                    'priority'    => 49,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_page_item_count_padding_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Item Count Padding Bottom (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_page_item_count_padding_bottom_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_item_count_padding_bottom_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_page_item_count_padding_bottom_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_page_item_count_padding_bottom_layout6',
                    'label'       => __( 'Bottom', 'betterdocs-pro' ),
                    'priority'    => 50,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_page_item_count_padding_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Item Count Padding Left (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_page_item_count_padding_left_layout6', [
            'default'           => $this->defaults['betterdocs_doc_page_item_count_padding_left_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_page_item_count_padding_left_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_page_item_count_padding_left_layout6',
                    'label'       => __( 'Left', 'betterdocs-pro' ),
                    'priority'    => 51,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_page_item_count_padding_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );
    }

    public function doc_list_layout6_separator() {
        $this->customizer->add_setting(
            'betterdocs_doc_list_layout6_separator',
            [
                'default'           => '',
                'sanitize_callback' => 'esc_html'
            ]
        );

        $this->customizer->add_control(
            new SeparatorControl(
                $this->customizer,
                'betterdocs_doc_list_layout6_separator',
                [
                    'label'    => __( 'Doc List', 'betterdocs-pro' ),
                    'settings' => 'betterdocs_doc_list_layout6_separator',
                    'section'  => 'betterdocs_doc_page_settings',
                    'priority' => 52
                ]
            )
        );
    }

    public function list_font_size_layout6() {
        $this->customizer->add_setting(
            'betterdocs_doc_list_font_size_layout6',
            [
                'default'           => $this->defaults['betterdocs_doc_list_font_size_layout6'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ]
        );

        $this->customizer->add_control(
            new RangeValueControl(
                $this->customizer,
                'betterdocs_doc_list_font_size_layout6',
                [
                    'type'        => 'betterdocs-range-value',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_font_size_layout6',
                    'label'       => __( 'List Font Size', 'betterdocs-pro' ),
                    'priority'    => 53,
                    'input_attrs' => [
                        'class'  => '',
                        'min'    => 0,
                        'max'    => 500,
                        'step'   => 1,
                        'suffix' => 'px' //optional suffix
                    ]
                ]
            )
        );
    }

    public function list_font_line_height_layout6() {
        $this->customizer->add_setting(
            'betterdocs_doc_list_font_line_height_layout6',
            [
                'default'           => $this->defaults['betterdocs_doc_list_font_line_height_layout6'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ]
        );

        $this->customizer->add_control(
            new RangeValueControl(
                $this->customizer,
                'betterdocs_doc_list_font_line_height_layout6',
                [
                    'type'        => 'betterdocs-range-value',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_font_line_height_layout6',
                    'label'       => __( 'List Font Line Height', 'betterdocs-pro' ),
                    'priority'    => 54,
                    'input_attrs' => [
                        'class'  => '',
                        'min'    => 0,
                        'max'    => 500,
                        'step'   => 1,
                        'suffix' => 'px' //optional suffix
                    ]
                ]
            )
        );
    }

    public function list_font_weight_layout6() {
        $this->customizer->add_setting(
            'betterdocs_doc_list_font_weight_layout6',
            [
                'default'           => $this->defaults['betterdocs_doc_list_font_weight_layout6'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'choices']
            ]
        );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_doc_list_font_weight_layout6',
                [
                    'label'    => __( 'List Font Weight', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_list_font_weight_layout6',
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
                    'priority' => 55
                ]
            )
        );
    }

    public function list_desc_switch_layout6() {
        $this->customizer->add_setting(
            'betterdocs_doc_list_desc_switch_layout6',
            [
                'default'           => $this->defaults['betterdocs_doc_list_desc_switch_layout6'],
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => [$this->sanitizer, 'checkbox']
            ]
        );

        $this->customizer->add_control(
            new ToggleControl(
                $this->customizer, 'betterdocs_doc_list_desc_switch_layout6',
                [
                    'label'    => __( 'Category Description', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_list_desc_switch_layout6',
                    'type'     => 'light', // light, ios, flat
                    'priority' => 56
                ]
            )
        );
    }

    public function list_desc_color_layout6() {
        $this->customizer->add_setting( 'betterdocs_doc_list_desc_color_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_desc_color_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_doc_list_desc_color_layout6',
                [
                    'label'    => __( 'Description Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_list_desc_color_layout6',
                    'priority' => 57
                ]
            )
        );
    }

    public function list_desc_font_size_layout6() {
        $this->customizer->add_setting(
            'betterdocs_doc_list_desc_font_size_layout6',
            [
                'default'           => $this->defaults['betterdocs_doc_list_desc_font_size_layout6'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ]
        );

        $this->customizer->add_control(
            new RangeValueControl(
                $this->customizer,
                'betterdocs_doc_list_desc_font_size_layout6',
                [
                    'type'        => 'betterdocs-range-value',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_desc_font_size_layout6',
                    'label'       => __( 'Description Font Size', 'betterdocs-pro' ),
                    'priority'    => 58,
                    'input_attrs' => [
                        'class'  => '',
                        'min'    => 0,
                        'max'    => 500,
                        'step'   => 1,
                        'suffix' => 'px' //optional suffix
                    ]
                ]
            )
        );
    }

    public function ist_desc_font_weight_layout6() {
        $this->customizer->add_setting(
            'betterdocs_doc_list_desc_font_weight_layout6',
            [
                'default'           => $this->defaults['betterdocs_doc_list_desc_font_weight_layout6'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'choices']
            ]
        );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_doc_list_desc_font_weight_layout6',
                [
                    'label'    => __( 'Description Font Weight', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_list_desc_font_weight_layout6',
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
                    'priority' => 59
                ]
            )
        );
    }

    public function list_desc_line_height_layout6() {
        $this->customizer->add_setting(
            'betterdocs_doc_list_desc_line_height_layout6',
            [
                'default'           => $this->defaults['betterdocs_doc_list_desc_line_height_layout6'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ]
        );

        $this->customizer->add_control(
            new RangeValueControl(
                $this->customizer,
                'betterdocs_doc_list_desc_line_height_layout6',
                [
                    'type'        => 'betterdocs-range-value',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_desc_line_height_layout6',
                    'label'       => __( 'Description Font Line Height', 'betterdocs-pro' ),
                    'priority'    => 60,
                    'input_attrs' => [
                        'class'  => '',
                        'min'    => 0,
                        'max'    => 500,
                        'step'   => 1,
                        'suffix' => 'px' //optional suffix
                    ]
                ]
            )
        );
    }

    public function list_desc_margin_layout6() {
        $this->customizer->add_setting( 'betterdocs_doc_list_desc_margin_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_desc_margin_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_doc_list_desc_margin_layout6', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_list_desc_margin_layout6',
                'label'       => __( 'Description Margin', 'betterdocs-pro' ),
                'priority'    => 61,
                'input_attrs' => [
                    'id'    => 'betterdocs_doc_list_desc_margin_layout6',
                    'class' => 'betterdocs-dimension'
                ]
            ] ) );

        // Doc List Description Margin Top (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_desc_margin_top_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_desc_margin_top_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_desc_margin_top_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_desc_margin_top_layout6',
                    'label'       => __( 'Top', 'betterdocs-pro' ),
                    'priority'    => 62,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_desc_margin_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Doc List Description Margin Right (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_desc_margin_right_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_desc_margin_right_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_desc_margin_right_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_desc_margin_right_layout6',
                    'label'       => __( 'Right', 'betterdocs-pro' ),
                    'priority'    => 63,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_desc_margin_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Doc List Description Margin Bottom (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_desc_margin_bottom_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_desc_margin_bottom_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_desc_margin_bottom_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_desc_margin_bottom_layout6',
                    'label'       => __( 'Bottom', 'betterdocs-pro' ),
                    'priority'    => 64,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_desc_margin_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Doc List Description Margin Left (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_desc_margin_left_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_desc_margin_left_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_desc_margin_left_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_desc_margin_left_layout6',
                    'label'       => __( 'Left', 'betterdocs-pro' ),
                    'priority'    => 65,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_desc_margin_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );
    }

    public function list_font_color_layout6() {
        $this->customizer->add_setting( 'betterdocs_doc_list_font_color_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_font_color_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_doc_list_font_color_layout6',
                [
                    'label'    => __( 'List Font Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_list_font_color_layout6',
                    'priority' => 66
                ]
            )
        );
    }

    public function list_font_color_hover_layout6() {
        $this->customizer->add_setting( 'betterdocs_doc_list_font_color_hover_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_font_color_hover_layout6'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_doc_list_font_color_hover_layout6',
                [
                    'label'    => __( 'List Font Color Hover', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_list_font_color_hover_layout6',
                    'priority' => 67
                ]
            )
        );
    }

    public function list_back_color_hover_layout6() {
        $this->customizer->add_setting( 'betterdocs_doc_list_back_color_hover_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_back_color_hover_layout6'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_doc_list_back_color_hover_layout6',
                [
                    'label'    => __( 'List Background Color Hover', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_list_back_color_hover_layout6',
                    'priority' => 67
                ] )
        );
    }

    public function list_border_color_hover_layout6() {
        $this->customizer->add_setting( 'betterdocs_doc_list_border_color_hover_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_border_color_hover_layout6'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_doc_list_border_color_hover_layout6',
                [
                    'label'    => __( 'List Background Border Color Hover', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_list_border_color_hover_layout6',
                    'priority' => 67
                ]
            )
        );
    }

    public function doc_list_margin_layout6() {
        $this->customizer->add_setting( 'betterdocs_doc_list_margin_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_margin_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_doc_list_margin_layout6', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_list_margin_layout6',
                'label'       => __( 'List Margin', 'betterdocs-pro' ),
                'priority'    => 68,
                'input_attrs' => [
                    'id'    => 'betterdocs_doc_list_margin_layout6',
                    'class' => 'betterdocs-dimension'
                ]
            ] ) );

        // Doc List Margin Top (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_margin_top_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_margin_top_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_margin_top_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_margin_top_layout6',
                    'label'       => __( 'Top', 'betterdocs-pro' ),
                    'priority'    => 69,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_margin_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Doc List Margin Right (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_margin_right_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_margin_right_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_margin_right_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_margin_right_layout6',
                    'label'       => __( 'Right', 'betterdocs-pro' ),
                    'priority'    => 70,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_margin_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Doc List Margin Bottom (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_margin_bottom_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_margin_bottom_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_margin_bottom_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_margin_bottom_layout6',
                    'label'       => __( 'Bottom', 'betterdocs-pro' ),
                    'priority'    => 71,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_margin_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Doc List Margin Left (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_margin_left_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_margin_left_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_margin_left_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_margin_left_layout6',
                    'label'       => __( 'Left', 'betterdocs-pro' ),
                    'priority'    => 72,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_margin_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );
    }

    public function list_padding_layout6() {
        $this->customizer->add_setting( 'betterdocs_doc_list_padding_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_padding_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_doc_list_padding_layout6', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_list_padding_layout6',
                'label'       => __( 'List Padding', 'betterdocs-pro' ),
                'priority'    => 73,
                'input_attrs' => [
                    'id'    => 'betterdocs_doc_list_padding_layout6',
                    'class' => 'betterdocs-dimension'
                ]
            ] ) );

        // Doc List Padding Top (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_padding_top_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_padding_top_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_padding_top_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_padding_top_layout6',
                    'label'       => __( 'Top', 'betterdocs-pro' ),
                    'priority'    => 74,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_padding_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Doc List Padding Right (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_padding_right_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_padding_right_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_padding_right_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_padding_right_layout6',
                    'label'       => __( 'Right', 'betterdocs-pro' ),
                    'priority'    => 75,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_padding_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Doc List Padding Bottom (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_padding_bottom_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_padding_bottom_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_padding_bottom_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_padding_bottom_layout6',
                    'label'       => __( 'Bottom', 'betterdocs-pro' ),
                    'priority'    => 76,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_padding_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Doc List Padding Left (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_padding_left_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_padding_left_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_padding_left_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_padding_left_layout6',
                    'label'       => __( 'Left', 'betterdocs-pro' ),
                    'priority'    => 77,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_padding_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );
    }

    public function list_border_style_layout6() {
        $this->customizer->add_setting( 'betterdocs_doc_list_border_style_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_border_style_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'choices']
        ] );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_doc_list_border_style_layout6',
                [
                    'label'    => __( 'List Border Style', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_list_border_style_layout6',
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
                    'priority' => 77
                ] )
        );
    }

    public function list_border_layout6() {
        // Doc List Border Width (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_border_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_border_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_doc_list_border_layout6', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_list_border_layout6',
                'label'       => __( 'List Border Width', 'betterdocs-pro' ),
                'priority'    => 77,
                'input_attrs' => [
                    'id'    => 'betterdocs_doc_list_border_layout6',
                    'class' => 'betterdocs-dimension'
                ]
            ] )
        );

        // Doc List Border Width Top (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_border_top_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_border_top_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_border_top_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_border_top_layout6',
                    'label'       => __( 'Top', 'betterdocs-pro' ),
                    'priority'    => 77,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_border_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Doc List Border Width Right (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_border_right_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_border_right_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_border_right_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_border_right_layout6',
                    'label'       => __( 'Right', 'betterdocs-pro' ),
                    'priority'    => 77,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_border_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Doc List Border Width Bottom (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_border_bottom_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_border_bottom_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_border_bottom_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_border_bottom_layout6',
                    'label'       => __( 'Bottom', 'betterdocs-pro' ),
                    'priority'    => 77,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_border_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Doc List Border Width Left (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_border_left_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_border_left_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_border_left_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_border_left_layout6',
                    'label'       => __( 'Left', 'betterdocs-pro' ),
                    'priority'    => 77,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_border_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );
    }

    public function list_border_hover_layout6() {
        $this->customizer->add_setting( 'betterdocs_doc_list_border_hover_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_border_hover_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_doc_list_border_hover_layout6', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_list_border_hover_layout6',
                'label'       => __( 'List Border Width Hover', 'betterdocs-pro' ),
                'priority'    => 77,
                'input_attrs' => [
                    'id'    => 'betterdocs_doc_list_border_hover_layout6',
                    'class' => 'betterdocs-dimension'
                ]
            ] ) );

        // Doc List Border Width Hover Top (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_border_hover_top_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_border_hover_top_layout6'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_border_hover_top_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_border_hover_top_layout6',
                    'label'       => __( 'Top', 'betterdocs-pro' ),
                    'priority'    => 77,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_border_hover_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Doc List Border Width Hover Right (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_border_hover_right_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_border_hover_right_layout6'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_border_hover_right_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_border_hover_right_layout6',
                    'label'       => __( 'Right', 'betterdocs-pro' ),
                    'priority'    => 77,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_border_hover_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Doc List Border Width Hover Bottom (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_border_hover_bottom_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_border_hover_bottom_layout6'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_border_hover_bottom_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_border_hover_bottom_layout6',
                    'label'       => __( 'Bottom', 'betterdocs-pro' ),
                    'priority'    => 77,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_border_hover_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Doc List Border Width Hover Left (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_border_hover_left_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_border_hover_left_layout6'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_border_hover_left_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_border_hover_left_layout6',
                    'label'       => __( 'Left', 'betterdocs-pro' ),
                    'priority'    => 77,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_border_hover_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );
    }

    public function list_border_color_top_layout6() {
        $this->customizer->add_setting(
            'betterdocs_doc_list_border_color_top_layout6',
            [
                'default'           => $this->defaults['betterdocs_doc_list_border_color_top_layout6'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'rgba']
            ]
        );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_doc_list_border_color_top_layout6',
                [
                    'label'    => __( 'List Border Color Top', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_list_border_color_top_layout6',
                    'priority' => 79
                ]
            )
        );
    }

    public function list_border_color_right_layout6() {
        $this->customizer->add_setting(
            'betterdocs_doc_list_border_color_right_layout6',
            [
                'default'           => $this->defaults['betterdocs_doc_list_border_color_right_layout6'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'rgba']
            ]
        );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_doc_list_border_color_right_layout6',
                [
                    'label'    => __( 'List Border Color Right', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_list_border_color_right_layout6',
                    'priority' => 79
                ] )
        );
    }

    public function list_border_color_bottom_layout6() {
        $this->customizer->add_setting(
            'betterdocs_doc_list_border_color_bottom_layout6',
            [
                'default'           => $this->defaults['betterdocs_doc_list_border_color_bottom_layout6'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'rgba']
            ]
        );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_doc_list_border_color_bottom_layout6',
                [
                    'label'    => __( 'List Border Color Bottom', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_list_border_color_bottom_layout6',
                    'priority' => 79
                ]
            )
        );
    }

    public function list_border_color_left_layout6() {
        $this->customizer->add_setting(
            'betterdocs_doc_list_border_color_left_layout6',
            [
                'default'           => $this->defaults['betterdocs_doc_list_border_color_left_layout6'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'rgba']
            ]
        );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_doc_list_border_color_left_layout6',
                [
                    'label'    => __( 'List Border Color Left', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_list_border_color_left_layout6',
                    'priority' => 79
                ] )
        );
    }

    public function list_arrow_height_layout6() {
        $this->customizer->add_setting(
            'betterdocs_doc_list_arrow_height_layout6',
            [
                'default'           => $this->defaults['betterdocs_doc_list_arrow_height_layout6'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ]
        );

        $this->customizer->add_control(
            new RangeValueControl(
                $this->customizer,
                'betterdocs_doc_list_arrow_height_layout6',
                [
                    'type'        => 'betterdocs-range-value',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_arrow_height_layout6',
                    'label'       => __( 'List Arrow Height', 'betterdocs-pro' ),
                    'priority'    => 80,
                    'input_attrs' => [
                        'class'  => '',
                        'min'    => 0,
                        'max'    => 500,
                        'step'   => 1,
                        'suffix' => 'px' //optional suffix
                    ]
                ]
            )
        );
    }

    public function arrow_width_layout6() {
        $this->customizer->add_setting(
            'betterdocs_doc_list_arrow_width_layout6',
            [
                'default'           => $this->defaults['betterdocs_doc_list_arrow_width_layout6'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ]
        );

        $this->customizer->add_control(
            new RangeValueControl(
                $this->customizer,
                'betterdocs_doc_list_arrow_width_layout6',
                [
                    'type'        => 'betterdocs-range-value',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_arrow_width_layout6',
                    'label'       => __( 'List Arrow Width', 'betterdocs-pro' ),
                    'priority'    => 81,
                    'input_attrs' => [
                        'class'  => '',
                        'min'    => 0,
                        'max'    => 500,
                        'step'   => 1,
                        'suffix' => 'px' //optional suffix
                    ]
                ]
            )
        );
    }

    public function list_arrow_color_layout6() {
        $this->customizer->add_setting(
            'betterdocs_doc_list_arrow_color_layout6',
            [
                'default'           => $this->defaults['betterdocs_doc_list_arrow_color_layout6'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'rgba']
            ]
        );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_doc_list_arrow_color_layout6',
                [
                    'label'    => __( 'List Arrow Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_list_arrow_color_layout6',
                    'priority' => 82
                ] )
        );
    }

    public function list_explore_more_separator() {
        $this->customizer->add_setting(
            'betterdocs_doc_list_explore_more_separator',
            [
                'default'           => '',
                'sanitize_callback' => 'esc_html'
            ]
        );

        $this->customizer->add_control(
            new SeparatorControl(
                $this->customizer,
                'betterdocs_doc_list_explore_more_separator',
                [
                    'label'    => __( 'Explore More', 'betterdocs-pro' ),
                    'settings' => 'betterdocs_doc_list_explore_more_separator',
                    'section'  => 'betterdocs_doc_page_settings',
                    'priority' => 83
                ]
            )
        );
    }

    public function list_explore_more_font_size_layout6() {
        $this->customizer->add_setting(
            'betterdocs_doc_list_explore_more_font_size_layout6',
            [
                'default'           => $this->defaults['betterdocs_doc_list_explore_more_font_size_layout6'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ]
        );

        $this->customizer->add_control(
            new RangeValueControl(
                $this->customizer,
                'betterdocs_doc_list_explore_more_font_size_layout6',
                [
                    'type'        => 'betterdocs-range-value',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_explore_more_font_size_layout6',
                    'label'       => __( 'Font Size', 'betterdocs-pro' ),
                    'priority'    => 84,
                    'input_attrs' => [
                        'class'  => '',
                        'min'    => 0,
                        'max'    => 500,
                        'step'   => 1,
                        'suffix' => 'px' //optional suffix
                    ]
                ]
            )
        );
    }

    public function list_explore_more_font_line_height_layout6() {
        $this->customizer->add_setting(
            'betterdocs_doc_list_explore_more_font_line_height_layout6',
            [
                'default'           => $this->defaults['betterdocs_doc_list_explore_more_font_line_height_layout6'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ]
        );

        $this->customizer->add_control(
            new RangeValueControl(
                $this->customizer,
                'betterdocs_doc_list_explore_more_font_line_height_layout6',
                [
                    'type'        => 'betterdocs-range-value',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_explore_more_font_line_height_layout6',
                    'label'       => __( 'Line Height', 'betterdocs-pro' ),
                    'priority'    => 85,
                    'input_attrs' => [
                        'class'  => '',
                        'min'    => 0,
                        'max'    => 500,
                        'step'   => 1,
                        'suffix' => 'px' //optional suffix
                    ]
                ]
            )
        );
    }

    public function ist_explore_more_font_color_layout6() {
        $this->customizer->add_setting(
            'betterdocs_doc_list_explore_more_font_color_layout6',
            [
                'default'           => $this->defaults['betterdocs_doc_list_explore_more_font_color_layout6'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'rgba']
            ]
        );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_doc_list_explore_more_font_color_layout6',
                [
                    'label'    => __( 'Font Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_list_explore_more_font_color_layout6',
                    'priority' => 86
                ]
            )
        );
    }

    public function list_explore_more_font_weight_layout6() {
        $this->customizer->add_setting( 'betterdocs_doc_list_explore_more_font_weight_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_explore_more_font_weight_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'choices']
        ] );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_doc_list_explore_more_font_weight_layout6',
                [
                    'label'    => __( 'Font Weight', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_list_explore_more_font_weight_layout6',
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
                    'priority' => 87
                ]
            )
        );
    }

    public function list_explore_more_padding_layout6() {
        $this->customizer->add_setting( 'betterdocs_doc_list_explore_more_padding_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_explore_more_padding_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_doc_list_explore_more_padding_layout6', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_list_explore_more_padding_layout6',
                'label'       => __( 'Padding', 'betterdocs-pro' ),
                'priority'    => 88,
                'input_attrs' => [
                    'id'    => 'betterdocs_doc_list_explore_more_padding_layout6',
                    'class' => 'betterdocs-dimension'
                ]
            ] )
        );

        //Explore More Top Padding (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_explore_more_padding_top_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_explore_more_padding_top_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer,
                'betterdocs_doc_list_explore_more_padding_top_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_explore_more_padding_top_layout6',
                    'label'       => __( 'Top', 'betterdocs-pro' ),
                    'priority'    => 89,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_explore_more_padding_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Explore More Padding Right (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_explore_more_padding_right_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_explore_more_padding_right_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_explore_more_padding_right_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_explore_more_padding_right_layout6',
                    'label'       => __( 'Right', 'betterdocs-pro' ),
                    'priority'    => 90,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_explore_more_padding_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Explore More Padding Bottom (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_explore_more_padding_bottom_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_explore_more_padding_bottom_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_explore_more_padding_bottom_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_explore_more_padding_bottom_layout6',
                    'label'       => __( 'Bottom', 'betterdocs-pro' ),
                    'priority'    => 91,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_explore_more_padding_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Explore More Padding Left (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_explore_more_padding_left_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_explore_more_padding_left_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_explore_more_padding_left_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_explore_more_padding_left_layout6',
                    'label'       => __( 'Left', 'betterdocs-pro' ),
                    'priority'    => 92,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_explore_more_padding_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );
    }

    public function list_explore_more_margin_layout6() {
        $this->customizer->add_setting( 'betterdocs_doc_list_explore_more_margin_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_explore_more_margin_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_doc_list_explore_more_margin_layout6', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_list_explore_more_margin_layout6',
                'label'       => __( 'Margin', 'betterdocs-pro' ),
                'priority'    => 93,
                'input_attrs' => [
                    'id'    => 'betterdocs_doc_list_explore_more_margin_layout6',
                    'class' => 'betterdocs-dimension'
                ]
            ] )
        );

        // Explore More Margin Top (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_explore_more_margin_top_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_explore_more_margin_top_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_explore_more_margin_top_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_explore_more_margin_top_layout6',
                    'label'       => __( 'Top', 'betterdocs-pro' ),
                    'priority'    => 94,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_explore_more_margin_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Explore More Margin Right (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_explore_more_margin_right_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_explore_more_margin_right_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_explore_more_margin_right_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_explore_more_margin_right_layout6',
                    'label'       => __( 'Right', 'betterdocs-pro' ),
                    'priority'    => 95,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_explore_more_margin_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Explore More Margin Bottom (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_explore_more_margin_bottom_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_explore_more_margin_bottom_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_explore_more_margin_bottom_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_explore_more_margin_bottom_layout6',
                    'label'       => __( 'Bottom', 'betterdocs-pro' ),
                    'priority'    => 96,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_explore_more_margin_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Explore More Margin Left (Doc Page Layout 6)
        $this->customizer->add_setting( 'betterdocs_doc_list_explore_more_margin_left_layout6', [
            'default'           => $this->defaults['betterdocs_doc_list_explore_more_margin_left_layout6'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_doc_list_explore_more_margin_left_layout6',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_explore_more_margin_left_layout6',
                    'label'       => __( 'Left', 'betterdocs-pro' ),
                    'priority'    => 97,
                    'input_attrs' => [
                        'class' => 'betterdocs_doc_list_explore_more_margin_layout6 betterdocs-dimension'
                    ]
                ]
            )
        );
    }

    public function list_explore_more_arrow_height_layout6() {
        $this->customizer->add_setting(
            'betterdocs_doc_list_explore_more_arrow_height_layout6',
            [
                'default'           => $this->defaults['betterdocs_doc_list_explore_more_arrow_height_layout6'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ]
        );

        $this->customizer->add_control(
            new RangeValueControl(
                $this->customizer,
                'betterdocs_doc_list_explore_more_arrow_height_layout6',
                [
                    'type'        => 'betterdocs-range-value',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_explore_more_arrow_height_layout6',
                    'label'       => __( 'Arrow Height', 'betterdocs-pro' ),
                    'priority'    => 98,
                    'input_attrs' => [
                        'class'  => '',
                        'min'    => 0,
                        'max'    => 500,
                        'step'   => 1,
                        'suffix' => 'px' //optional suffix
                    ]
                ]
            )
        );
    }

    public function list_explore_more_arrow_width_layout6() {
        $this->customizer->add_setting(
            'betterdocs_doc_list_explore_more_arrow_width_layout6',
            [
                'default'           => $this->defaults['betterdocs_doc_list_explore_more_arrow_width_layout6'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ]
        );

        $this->customizer->add_control(
            new RangeValueControl(
                $this->customizer,
                'betterdocs_doc_list_explore_more_arrow_width_layout6',
                [
                    'type'        => 'betterdocs-range-value',
                    'section'     => 'betterdocs_doc_page_settings',
                    'settings'    => 'betterdocs_doc_list_explore_more_arrow_width_layout6',
                    'label'       => __( 'Arrow Width', 'betterdocs-pro' ),
                    'priority'    => 99,
                    'input_attrs' => [
                        'class'  => '',
                        'min'    => 0,
                        'max'    => 500,
                        'step'   => 1,
                        'suffix' => 'px' //optional suffix
                    ]
                ]
            )
        );
    }

    public function list_explore_more_arrow_color_layout6() {
        $this->customizer->add_setting(
            'betterdocs_doc_list_explore_more_arrow_color_layout6',
            [
                'default'           => $this->defaults['betterdocs_doc_list_explore_more_arrow_color_layout6'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'rgba']
            ]
        );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_doc_list_explore_more_arrow_color_layout6',
                [
                    'label'    => __( 'Arrow Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_list_explore_more_arrow_color_layout6',
                    'priority' => 100
                ]
            )
        );
    }

    public function popular_docs_switch() {
        $this->customizer->add_setting( 'betterdocs_docs_page_popular_docs_switch', [
            'default'           => $this->defaults['betterdocs_docs_page_popular_docs_switch'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'checkbox']
        ] );

        $this->customizer->add_control( new ToggleControl(
            $this->customizer, 'betterdocs_docs_page_popular_docs_switch', [
                'label'    => __( 'Popular Docs Show', 'betterdocs-pro' ),
                'section'  => 'betterdocs_doc_page_settings',
                'settings' => 'betterdocs_docs_page_popular_docs_switch',
                'type'     => 'light', // light, ios, flat
                'priority' => 34
            ] )
        );
    }

    public function article_list_bg_color_2() {
        $this->customizer->add_setting( 'betterdocs_doc_page_article_list_bg_color_2', [
            'default'           => $this->defaults['betterdocs_doc_page_article_list_bg_color_2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_doc_page_article_list_bg_color_2',
                [
                    'label'    => __( 'Popular Docs Background Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_page_article_list_bg_color_2',
                    'priority' => 34
                ]
            )
        );
    }

    public function article_list_color_2() {
        $this->customizer->add_setting( 'betterdocs_doc_page_article_list_color_2', [
            'default'           => $this->defaults['betterdocs_doc_page_article_list_color_2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_doc_page_article_list_color_2',
                [
                    'label'    => __( 'Popular Docs List Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_page_article_list_color_2',
                    'priority' => 35
                ]
            )
        );
    }

    public function article_list_hover_color_2() {
        $this->customizer->add_setting( 'betterdocs_doc_page_article_list_hover_color_2', [
            'default'           => $this->defaults['betterdocs_doc_page_article_list_hover_color_2'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_doc_page_article_list_hover_color_2',
                [
                    'label'    => __( 'Popular Docs List Hover Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_page_article_list_hover_color_2',
                    'priority' => 36
                ]
            )
        );
    }

    public function article_list_font_size_2() {
        $this->customizer->add_setting( 'betterdocs_doc_page_article_list_font_size_2', [
            'default'           => $this->defaults['betterdocs_doc_page_article_list_font_size_2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_doc_page_article_list_font_size_2', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_article_list_font_size_2',
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

    public function article_title_font_size_2() {
        $this->customizer->add_setting( 'betterdocs_doc_page_article_title_font_size_2', [
            'default'           => $this->defaults['betterdocs_doc_page_article_title_font_size_2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_doc_page_article_title_font_size_2', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_article_title_font_size_2',
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

    public function article_title_color_2() {
        $this->customizer->add_setting( 'betterdocs_doc_page_article_title_color_2', [
            'default'           => $this->defaults['betterdocs_doc_page_article_title_color_2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_doc_page_article_title_color_2',
                [
                    'label'    => __( 'Popular Title Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_page_article_title_color_2',
                    'priority' => 38
                ]
            )
        );
    }

    public function article_title_color_hover_2() {
        $this->customizer->add_setting( 'betterdocs_doc_page_article_title_color_hover_2', [
            'default'           => $this->defaults['betterdocs_doc_page_article_title_color_hover_2'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_doc_page_article_title_color_hover_2',
                [
                    'label'    => __( 'Popular Title Hover Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_page_article_title_color_hover_2',
                    'priority' => 38
                ]
            )
        );
    }

    public function article_list_icon_color_2() {
        $this->customizer->add_setting( 'betterdocs_doc_page_article_list_icon_color_2', [
            'default'           => $this->defaults['betterdocs_doc_page_article_list_icon_color_2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_doc_page_article_list_icon_color_2',
                [
                    'label'    => __( 'Popular List Icon Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_doc_page_settings',
                    'settings' => 'betterdocs_doc_page_article_list_icon_color_2',
                    'priority' => 38
                ]
            )
        );
    }

    public function article_list_icon_font_size_2() {
        $this->customizer->add_setting( 'betterdocs_doc_page_article_list_icon_font_size_2', [
            'default'           => $this->defaults['betterdocs_doc_page_article_list_icon_font_size_2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_doc_page_article_list_icon_font_size_2', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_article_list_icon_font_size_2',
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

    public function popular_title_margin() {
        $this->customizer->add_setting( 'betterdocs_doc_page_popular_title_margin', [
            'default'           => $this->defaults['betterdocs_doc_page_popular_title_margin'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_doc_page_popular_title_margin', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_popular_title_margin',
                'label'       => __( 'Popular Docs Title Margin', 'betterdocs-pro' ),
                'priority'    => 44,
                'input_attrs' => [
                    'id'    => 'betterdocs_doc_page_popular_title_margin',
                    'class' => 'betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_doc_page_popular_title_margin_top', [
            'default'           => $this->defaults['betterdocs_doc_page_popular_title_margin_top'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_doc_page_popular_title_margin_top', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_popular_title_margin_top',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'priority'    => 44,
                'input_attrs' => [
                    'class' => 'betterdocs_doc_page_popular_title_margin betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_doc_page_popular_title_margin_right', [
            'default'           => $this->defaults['betterdocs_doc_page_popular_title_margin_right'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_doc_page_popular_title_margin_right', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_popular_title_margin_right',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'priority'    => 44,
                'input_attrs' => [
                    'class' => 'betterdocs_doc_page_popular_title_margin betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_doc_page_popular_title_margin_bottom', [
            'default'           => $this->defaults['betterdocs_doc_page_popular_title_margin_bottom'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_doc_page_popular_title_margin_bottom', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_popular_title_margin_bottom',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'priority'    => 44,
                'input_attrs' => [
                    'class' => 'betterdocs_doc_page_popular_title_margin betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_doc_page_popular_title_margin_left', [
            'default'           => $this->defaults['betterdocs_doc_page_popular_title_margin_left'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_doc_page_popular_title_margin_left', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_popular_title_margin_left',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'priority'    => 44,
                'input_attrs' => [
                    'class' => 'betterdocs_doc_page_popular_title_margin betterdocs-dimension'
                ]
            ] )
        );
    }

    public function article_list_margin_2() {
        $this->customizer->add_setting( 'betterdocs_doc_page_article_list_margin_2', [
            'default'           => $this->defaults['betterdocs_doc_page_article_list_margin_2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_doc_page_article_list_margin_2', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_article_list_margin_2',
                'label'       => __( 'Popular Docs List Margin', 'betterdocs-pro' ),
                'priority'    => 44,
                'input_attrs' => [
                    'id'    => 'betterdocs_doc_page_article_list_margin_2',
                    'class' => 'betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_doc_page_article_list_margin_top_2', [
            'default'           => $this->defaults['betterdocs_doc_page_article_list_margin_top_2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_doc_page_article_list_margin_top_2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_article_list_margin_top_2',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'priority'    => 44,
                'input_attrs' => [
                    'class' => 'betterdocs_doc_page_article_list_margin_2 betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_doc_page_article_list_margin_right_2', [
            'default'           => $this->defaults['betterdocs_doc_page_article_list_margin_right_2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_doc_page_article_list_margin_right_2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_article_list_margin_right_2',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'priority'    => 44,
                'input_attrs' => [
                    'class' => 'betterdocs_doc_page_article_list_margin_2 betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_doc_page_article_list_margin_bottom_2', [
            'default'           => $this->defaults['betterdocs_doc_page_article_list_margin_bottom_2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_doc_page_article_list_margin_bottom_2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_article_list_margin_bottom_2',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'priority'    => 44,
                'input_attrs' => [
                    'class' => 'betterdocs_doc_page_article_list_margin_2 betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_doc_page_article_list_margin_left_2', [
            'default'           => $this->defaults['betterdocs_doc_page_article_list_margin_left_2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_doc_page_article_list_margin_left_2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_article_list_margin_left_2',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'priority'    => 44,
                'input_attrs' => [
                    'class' => 'betterdocs_doc_page_article_list_margin_2 betterdocs-dimension'
                ]
            ] )
        );
    }

    public function popular_docs_padding() {
        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_doc_page_popular_docs_padding', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_popular_docs_padding',
                'label'       => __( 'Popular Docs Padding', 'betterdocs-pro' ),
                'priority'    => 44,
                'input_attrs' => [
                    'id'    => 'betterdocs_article_popular_docs_padding',
                    'class' => 'betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_doc_page_popular_docs_padding_top', [
            'default'           => $this->defaults['betterdocs_doc_page_popular_docs_padding_top'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_doc_page_popular_docs_padding_top', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_popular_docs_padding_top',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'priority'    => 44,
                'input_attrs' => [
                    'class' => 'betterdocs_article_popular_docs_padding betterdocs-dimension'
                ]
            ] ) );

        $this->customizer->add_setting( 'betterdocs_doc_page_popular_docs_padding_right', [
            'default'           => $this->defaults['betterdocs_doc_page_popular_docs_padding_right'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_doc_page_popular_docs_padding_right', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_popular_docs_padding_right',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'priority'    => 44,
                'input_attrs' => [
                    'class' => 'betterdocs_article_popular_docs_padding betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_doc_page_popular_docs_padding_bottom', [
            'default'           => $this->defaults['betterdocs_doc_page_popular_docs_padding_bottom'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_doc_page_popular_docs_padding_bottom', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_popular_docs_padding_bottom',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'priority'    => 44,
                'input_attrs' => [
                    'class' => 'betterdocs_article_popular_docs_padding betterdocs-dimension'
                ]
            ] )
        );

        $this->customizer->add_setting( 'betterdocs_doc_page_popular_docs_padding_left', [
            'default'           => $this->defaults['betterdocs_doc_page_popular_docs_padding_left'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_doc_page_popular_docs_padding_left', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_popular_docs_padding_left',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'priority'    => 44,
                'input_attrs' => [
                    'class' => 'betterdocs_article_popular_docs_padding betterdocs-dimension'
                ]
            ] )
        );
    }

    public function content_overlap() {
        $this->customizer->add_setting( 'betterdocs_doc_page_content_overlap', [
            'default'           => $this->defaults['betterdocs_doc_page_content_overlap'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_doc_page_content_overlap', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_content_overlap',
                'label'       => __( 'Content Overlap', 'betterdocs-pro' ),
                'priority'    => 16,
                'input_attrs' => [
                    'min'    => 0,
                    'max'    => 500,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ]
            ] )
        );
    }

    public function cat_icon_size_l_3_4() {
        $this->customizer->add_setting( 'betterdocs_doc_page_cat_icon_size_l_3_4', [
            'default'           => $this->defaults['betterdocs_doc_page_cat_icon_size_l_3_4'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_doc_page_cat_icon_size_l_3_4', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_cat_icon_size_l_3_4',
                'label'       => __( 'Box Icon Size', 'betterdocs-pro' ),
                'priority'    => 24,
                'input_attrs' => [
                    'min'    => 0,
                    'max'    => 200,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ]
            ] )
        );
    }

    public function cat_title_font_size2() {
        $this->customizer->add_setting( 'betterdocs_doc_page_cat_title_font_size2', [
            'default'           => $this->defaults['betterdocs_doc_page_cat_title_font_size2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_doc_page_cat_title_font_size2', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_doc_page_settings',
                'settings'    => 'betterdocs_doc_page_cat_title_font_size2',
                'label'       => __( 'Docs List Title Font Size', 'betterdocs-pro' ),
                'priority'    => 34,
                'input_attrs' => [
                    'min'    => 0,
                    'max'    => 100,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ]
            ] )
        );
    }
}
