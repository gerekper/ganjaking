<?php

namespace WPDeveloper\BetterDocsPro\Admin\Customizer\Sections;

use WP_Customize_Control;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\TitleControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\DimensionControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\SeparatorControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\AlphaColorControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\RangeValueControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Sections\ArchivePage as FreeArchivePage;

class ArchivePage extends FreeArchivePage {

    public function title_tag_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_title_tag_layout2', [
            'default'           => $this->defaults['betterdocs_archive_title_tag_layout2'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'choices']
        ] );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_archive_title_tag_layout2',
                [
                    'label'    => __( 'Category Title Tag', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_title_tag_layout2',
                    'type'     => 'select',
                    'choices'  => [
                        'h1' => 'h1',
                        'h2' => 'h2',
                        'h3' => 'h3',
                        'h4' => 'h4',
                        'h5' => 'h5',
                        'h6' => 'h6'
                    ],
                    'priority' => 401
                ] )
        );
    }

    public function inner_content_back_color_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_inner_content_back_color_layout2', [
            'default'           => $this->defaults['betterdocs_archive_inner_content_back_color_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_archive_inner_content_back_color_layout2',
                [
                    'label'    => __( 'Inner Content Background Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_inner_content_back_color_layout2',
                    'priority' => 402
                ]
            )
        );
    }

    public function inner_content_image_size_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_inner_content_image_size_layout2', [
            'default'           => $this->defaults['betterdocs_archive_inner_content_image_size_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_archive_inner_content_image_size_layout2', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_inner_content_image_size_layout2',
                'label'       => __( 'Content Image Size', 'betterdocs-pro' ),
                'input_attrs' => [
                    'min'    => 0,
                    'max'    => 100,
                    'step'   => 1,
                    'suffix' => '%' //optional suffix
                ],
                'priority'    => 402
            ] )
        );
    }

    public function inner_content_image_padding_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_inner_content_image_padding_layout2', [
            'default'           => $this->defaults['betterdocs_archive_inner_content_image_padding_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_archive_inner_content_image_padding_layout2', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_inner_content_image_padding_layout2',
                'label'       => __( 'Content Image Padding', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_archive_inner_content_image_padding_layout2',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 402
            ] )
        );

        // Archive Page Category Inner Content Image Padding Top(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_inner_content_image_padding_top_layout2', [
            'default'           => $this->defaults['betterdocs_archive_inner_content_image_padding_top_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_inner_content_image_padding_top_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_inner_content_image_padding_top_layout2',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_inner_content_image_padding_layout2 betterdocs-dimension'
                ],
                'priority'    => 402
            ] )
        );

        // Archive Page Category Inner Content Image Padding Right(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_inner_content_image_padding_right_layout2', [
            'default'           => $this->defaults['betterdocs_archive_inner_content_image_padding_right_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_inner_content_image_padding_right_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_inner_content_image_padding_right_layout2',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_inner_content_image_padding_layout2 betterdocs-dimension'
                ],
                'priority'    => 402
            ] )
        );

        // Archive Page Category Inner Content Image Padding Bottom(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_inner_content_image_padding_bottom_layout2', [
            'default'           => $this->defaults['betterdocs_archive_inner_content_image_padding_bottom_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_inner_content_image_padding_bottom_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_inner_content_image_padding_bottom_layout2',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_inner_content_image_padding_layout2 betterdocs-dimension'
                ],
                'priority'    => 402
            ] )
        );

        // Archive Page Category Inner Content Image Padding Left(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_inner_content_image_padding_left_layout2', [
            'default'           => $this->defaults['betterdocs_archive_inner_content_image_padding_left_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_inner_content_image_padding_left_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_inner_content_image_padding_left_layout2',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_inner_content_image_padding_layout2 betterdocs-dimension'
                ],
                'priority'    => 402
            ] )
        );
    }

    public function inner_content_image_margin_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_inner_content_image_margin_layout2', [
            'default'           => $this->defaults['betterdocs_archive_inner_content_image_margin_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_archive_inner_content_image_margin_layout2', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_inner_content_image_margin_layout2',
                'label'       => __( 'Content Image Margin', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_archive_inner_content_image_margin_layout2',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 402
            ] )
        );

        // Archive Page Category Inner Content Image Margin Top(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_inner_content_image_margin_top_layout2', [
            'default'           => $this->defaults['betterdocs_archive_inner_content_image_margin_top_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_inner_content_image_margin_top_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_inner_content_image_margin_top_layout2',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_inner_content_image_margin_layout2 betterdocs-dimension'
                ],
                'priority'    => 402
            ] )
        );

        // Archive Page Category Inner Content Image Margin Right(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_inner_content_image_margin_right_layout2', [
            'default'           => $this->defaults['betterdocs_archive_inner_content_image_margin_right_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_inner_content_image_margin_right_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_inner_content_image_margin_right_layout2',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_inner_content_image_margin_layout2 betterdocs-dimension'
                ],
                'priority'    => 402
            ] )
        );

        // Archive Page Category Inner Content Image Margin Bottom(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_inner_content_image_margin_bottom_layout2', [
            'default'           => $this->defaults['betterdocs_archive_inner_content_image_margin_bottom_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_inner_content_image_margin_bottom_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_inner_content_image_margin_bottom_layout2',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_inner_content_image_margin_layout2 betterdocs-dimension'
                ],
                'priority'    => 402
            ] )
        );

        // Archive Page Category Inner Content Image Margin Left(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_inner_content_image_margin_left_layout2', [
            'default'           => $this->defaults['betterdocs_archive_inner_content_image_margin_left_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_inner_content_image_margin_left_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_inner_content_image_margin_left_layout2',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_inner_content_image_margin_layout2 betterdocs-dimension'
                ],
                'priority'    => 402
            ] )
        );
    }

    public function title_color_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_title_color_layout2', [
            'default'           => $this->defaults['betterdocs_archive_title_color_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl
            (
                $this->customizer,
                'betterdocs_archive_title_color_layout2',
                [
                    'label'    => __( 'Title Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_title_color_layout2',
                    'priority' => 402
                ]
            )
        );
    }

    public function title_font_size_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_title_font_size_layout2', [
            'default'           => $this->defaults['betterdocs_archive_title_font_size_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_archive_title_font_size_layout2', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_title_font_size_layout2',
                'label'       => __( 'Title Font Size', 'betterdocs-pro' ),
                'input_attrs' => [
                    'min'    => 0,
                    'max'    => 100,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ],
                'priority'    => 403
            ] )
        );
    }

    public function title_margin_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_title_margin_layout2', [
            'default'           => $this->defaults['betterdocs_archive_title_margin_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_archive_title_margin_layout2', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_title_margin_layout2',
                'label'       => __( 'Archive Title Margin', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_archive_title_margin_layout2',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 404
            ] )
        );

        // Archive Page Title Margin Top (Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_title_margin_top_layout2', [
            'default'           => $this->defaults['betterdocs_archive_title_margin_top_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_title_margin_top_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_title_margin_top_layout2',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_title_margin_layout2 betterdocs-dimension'
                ],
                'priority'    => 405
            ] )
        );

        // Archive Page Title Margin Right (Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_title_margin_right_layout2', [
            'default'           => $this->defaults['betterdocs_archive_title_margin_right_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_title_margin_right_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_title_margin_right_layout2',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_title_margin_layout2 betterdocs-dimension'
                ],
                'priority'    => 406
            ] )
        );

        // Archive Page Title Margin Bottom (Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_title_margin_bottom_layout2', [
            'default'           => $this->defaults['betterdocs_archive_title_margin_bottom_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_title_margin_bottom_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_title_margin_bottom_layout2',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_title_margin_layout2 betterdocs-dimension'
                ],
                'priority'    => 407
            ] )
        );

        // Archive Page Title Margin Left (Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_title_margin_left_layout2', [
            'default'           => $this->defaults['betterdocs_archive_title_margin_left_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_title_margin_left_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_title_margin_left_layout2',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_title_margin_layout2 betterdocs-dimension'
                ],
                'priority'    => 408
            ] )
        );
    }

    public function description_color_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_description_color_layout2', [
            'default'           => $this->defaults['betterdocs_archive_description_color_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_archive_description_color_layout2',
                [
                    'label'    => __( 'Description Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_description_color_layout2',
                    'priority' => 409
                ]
            )
        );
    }

    public function description_font_size_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_description_font_size_layout2', [
            'default'           => $this->defaults['betterdocs_archive_description_font_size_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_archive_description_font_size_layout2', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_description_font_size_layout2',
                'label'       => __( 'Description Font Size', 'betterdocs-pro' ),
                'input_attrs' => [
                    'min'    => 0,
                    'max'    => 50,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ],
                'priority'    => 410
            ] )
        );
    }

    public function description_margin_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_description_margin_layout2', [
            'default'           => $this->defaults['betterdocs_archive_description_margin_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_archive_description_margin_layout2', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_description_margin_layout2',
                'label'       => __( 'Archive Description Margin', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_archive_description_margin_layout2',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 411
            ] )
        );

        // Archive Page Description Margin Top (Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_description_margin_top_layout2', [
            'default'           => $this->defaults['betterdocs_archive_description_margin_top_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_description_margin_top_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_description_margin_top_layout2',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_description_margin_layout2 betterdocs-dimension'
                ],
                'priority'    => 412
            ] )
        );

        // Archive Page Description Margin Right (Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_description_margin_right_layout2', [
            'default'           => $this->defaults['betterdocs_archive_description_margin_right_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_description_margin_right_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_description_margin_right_layout2',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_description_margin_layout2 betterdocs-dimension'
                ],
                'priority'    => 413
            ] )
        );

        // Archive Page Description Margin Bottom (Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_description_margin_bottom_layout2', [
            'default'           => $this->defaults['betterdocs_archive_description_margin_bottom_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_description_margin_bottom_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_description_margin_bottom_layout2',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_description_margin_layout2 betterdocs-dimension'
                ],
                'priority'    => 414
            ] )
        );

        // Archive Page Description Margin Left (Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_description_margin_left_layout2', [
            'default'           => $this->defaults['betterdocs_archive_description_margin_left_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_description_margin_left_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_description_margin_left_layout2',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_description_margin_layout2 betterdocs-dimension'
                ],
                'priority'    => 415
            ] )
        );
    }

    public function list_item_color_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_list_item_color_layout2', [
            'default'           => $this->defaults['betterdocs_archive_list_item_color_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_archive_list_item_color_layout2',
                [
                    'label'    => __( 'List Item Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_list_item_color_layout2',
                    'priority' => 416
                ]
            )
        );
    }

    public function list_item_color_hover_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_list_item_color_hover_layout2', [
            'default'           => $this->defaults['betterdocs_archive_list_item_color_hover_layout2'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_archive_list_item_color_hover_layout2',
                [
                    'label'    => __( 'List Item Hover Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_list_item_color_hover_layout2',
                    'priority' => 417
                ]
            )
        );
    }

    public function list_back_color_hover_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_list_back_color_hover_layout2', [
            'default'           => $this->defaults['betterdocs_archive_list_back_color_hover_layout2'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_archive_list_back_color_hover_layout2',
                [
                    'label'    => __( 'List Background Color Hover', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_list_back_color_hover_layout2',
                    'priority' => 417
                ]
            )
        );
    }

    public function list_border_color_hover_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_list_border_color_hover_layout2', [
            'default'           => $this->defaults['betterdocs_archive_list_border_color_hover_layout2'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_archive_list_border_color_hover_layout2',
                [
                    'label'    => __( 'List Background Border Color Hover', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_list_border_color_hover_layout2',
                    'priority' => 417
                ]
            )
        );
    }

    public function list_border_width_hover_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_list_border_width_hover_layout2', [
            'default'           => $this->defaults['betterdocs_archive_list_border_width_hover_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_archive_list_border_width_hover_layout2', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_list_border_width_hover_layout2',
                'label'       => __( 'List Item Border Width Hover', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_archive_list_border_width_hover_layout2',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 417
            ] )
        );

        // Archive Page List Item Hover Border Width Top(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_list_border_width_top_hover_layout2', [
            'default'           => $this->defaults['betterdocs_archive_list_border_width_top_hover_layout2'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_list_border_width_top_hover_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_list_border_width_top_hover_layout2',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_list_border_width_hover_layout2 betterdocs-dimension'
                ],
                'priority'    => 417
            ] )
        );

        // Archive Page List Item Hover Border Width Right(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_list_border_width_right_hover_layout2', [
            'default'           => $this->defaults['betterdocs_archive_list_border_width_right_hover_layout2'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_list_border_width_right_hover_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_list_border_width_right_hover_layout2',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_list_border_width_hover_layout2 betterdocs-dimension'
                ],
                'priority'    => 417
            ] )
        );

        // Archive Page List Item Hover Border Width Bottom(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_list_border_width_bottom_hover_layout2', [
            'default'           => $this->defaults['betterdocs_archive_list_border_width_bottom_hover_layout2'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_list_border_width_bottom_hover_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_list_border_width_bottom_hover_layout2',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_list_border_width_hover_layout2 betterdocs-dimension'
                ],
                'priority'    => 417
            ] )
        );

        // Archive Page List Item Hover Border Width Left(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_list_border_width_left_hover_layout2', [
            'default'           => $this->defaults['betterdocs_archive_list_border_width_left_hover_layout2'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_list_border_width_left_hover_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_list_border_width_left_hover_layout2',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_list_border_width_hover_layout2 betterdocs-dimension'
                ],
                'priority'    => 417
            ] )
        );
    }

    public function list_border_style_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_list_border_style_layout2', [
            'default'           => $this->defaults['betterdocs_archive_list_border_style_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'choices']
        ] );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_archive_list_border_style_layout2',
                [
                    'label'    => __( 'List Border Style', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_list_border_style_layout2',
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
                    'priority' => 417
                ]
            )
        );
    }

    public function list_border_width_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_list_border_width_layout2', [
            'default'           => $this->defaults['betterdocs_archive_list_border_width_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_archive_list_border_width_layout2', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_list_border_width_layout2',
                'label'       => __( 'List Border Width', 'betterdocs-pro' ),
                'priority'    => 417,
                'input_attrs' => [
                    'id'    => 'betterdocs_archive_list_border_width_layout2',
                    'class' => 'betterdocs-dimension'
                ]
            ] )
        );

        // Archive Page List Item Border Width Top(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_list_border_width_top_layout2', [
            'default'           => $this->defaults['betterdocs_archive_list_border_width_top_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_archive_list_border_width_top_layout2',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_archive_page_settings',
                    'settings'    => 'betterdocs_archive_list_border_width_top_layout2',
                    'label'       => __( 'Top', 'betterdocs-pro' ),
                    'priority'    => 417,
                    'input_attrs' => [
                        'class' => 'betterdocs_archive_list_border_width_layout2 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Archive Page List Item Border Width Right(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_list_border_width_right_layout2', [
            'default'           => $this->defaults['betterdocs_archive_list_border_width_right_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_archive_list_border_width_right_layout2',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_archive_page_settings',
                    'settings'    => 'betterdocs_archive_list_border_width_right_layout2',
                    'label'       => __( 'Right', 'betterdocs-pro' ),
                    'priority'    => 417,
                    'input_attrs' => [
                        'class' => 'betterdocs_archive_list_border_width_layout2 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Archive Page List Item Border Width Bottom(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_list_border_width_bottom_layout2', [
            'default'           => $this->defaults['betterdocs_archive_list_border_width_bottom_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_archive_list_border_width_bottom_layout2',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_archive_page_settings',
                    'settings'    => 'betterdocs_archive_list_border_width_bottom_layout2',
                    'label'       => __( 'Bottom', 'betterdocs-pro' ),
                    'priority'    => 417,
                    'input_attrs' => [
                        'class' => 'betterdocs_archive_list_border_width_layout2 betterdocs-dimension'
                    ]
                ]
            )
        );

        // Archive Page List Item Border Width Left(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_list_border_width_left_layout2', [
            'default'           => $this->defaults['betterdocs_archive_list_border_width_left_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control(
            new DimensionControl(
                $this->customizer, 'betterdocs_archive_list_border_width_left_layout2',
                [
                    'type'        => 'betterdocs-dimension',
                    'section'     => 'betterdocs_archive_page_settings',
                    'settings'    => 'betterdocs_archive_list_border_width_left_layout2',
                    'label'       => __( 'Left', 'betterdocs-pro' ),
                    'priority'    => 417,
                    'input_attrs' => [
                        'class' => 'betterdocs_archive_list_border_width_layout2 betterdocs-dimension'
                    ]
                ]
            )
        );
    }

    public function list_border_width_color_top_layout2() {
        $this->customizer->add_setting(
            'betterdocs_archive_list_border_width_color_top_layout2',
            [
                'default'           => $this->defaults['betterdocs_archive_list_border_width_color_top_layout2'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'rgba']
            ]
        );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_archive_list_border_width_color_top_layout2',
                [
                    'label'    => __( 'List Border Color Top', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_list_border_width_color_top_layout2',
                    'priority' => 417
                ]
            )
        );
    }

    public function list_border_width_color_right_layout2() {
        $this->customizer->add_setting(
            'betterdocs_archive_list_border_width_color_right_layout2',
            [
                'default'           => $this->defaults['betterdocs_archive_list_border_width_color_right_layout2'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'rgba']
            ]
        );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_archive_list_border_width_color_right_layout2',
                [
                    'label'    => __( 'List Border Color Right', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_list_border_width_color_right_layout2',
                    'priority' => 417
                ]
            )
        );
    }

    public function list_border_width_color_bottom_layout2() {
        $this->customizer->add_setting(
            'betterdocs_archive_list_border_width_color_bottom_layout2',
            [
                'default'           => $this->defaults['betterdocs_archive_list_border_width_color_bottom_layout2'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'rgba']
            ]
        );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_archive_list_border_width_color_bottom_layout2',
                [
                    'label'    => __( 'List Border Color Bottom', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_list_border_width_color_bottom_layout2',
                    'priority' => 417
                ]
            )
        );
    }

    public function list_border_width_color_left_layout2() {
        $this->customizer->add_setting(
            'betterdocs_archive_list_border_width_color_left_layout2',
            [
                'default'           => $this->defaults['betterdocs_archive_list_border_width_color_left_layout2'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'rgba']
            ]
        );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_archive_list_border_width_color_left_layout2',
                [
                    'label'    => __( 'List Border Color Left', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_list_border_width_color_left_layout2',
                    'priority' => 417
                ]
            )
        );
    }

    public function list_item_font_size_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_list_item_font_size_layout2', [
            'default'           => $this->defaults['betterdocs_archive_list_item_font_size_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_archive_list_item_font_size_layout2', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_list_item_font_size_layout2',
                'label'       => __( 'List Item Font Size', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 50,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ],
                'priority'    => 418
            ] )
        );
    }

    public function article_list_margin_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_article_list_margin_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_margin_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_archive_article_list_margin_layout2', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_margin_layout2',
                'label'       => __( 'Docs List Margin', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_archive_article_list_margin_layout2',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 419
            ] )
        );

        // Archive Page Docs List Margin Top(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_margin_top_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_margin_top_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_margin_top_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_margin_top_layout2',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_margin_layout2 betterdocs-dimension'
                ],
                'priority'    => 420
            ] )
        );

        // Archive Page Docs List Margin Right(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_margin_right_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_margin_right_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_margin_right_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_margin_right_layout2',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_margin_layout2 betterdocs-dimension'
                ],
                'priority'    => 421
            ] )
        );

        // Archive Page Docs List Margin Bottom(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_margin_bottom_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_margin_bottom_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_margin_bottom_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_margin_bottom_layout2',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_margin_layout2 betterdocs-dimension'
                ],
                'priority'    => 422
            ] )
        );

        // Archive Page Docs List Margin Left(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_margin_left_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_margin_left_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_margin_left_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_margin_left_layout2',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_margin_layout2 betterdocs-dimension'
                ],
                'priority'    => 423
            ] )
        );
    }

    public function rticle_list_font_weight_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_article_list_font_weight_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_font_weight_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'choices']
        ] );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_archive_article_list_font_weight_layout2',
                [
                    'label'    => __( 'List Font Weight', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_article_list_font_weight_layout2',
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
                    'priority' => 425
                ]
            )
        );
    }

    public function list_item_line_height_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_list_item_line_height_layout2', [
            'default'           => $this->defaults['betterdocs_archive_list_item_line_height_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_archive_list_item_line_height_layout2', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_list_item_line_height_layout2',
                'label'       => __( 'List Item Line Height', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 200,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ],
                'priority'    => 426
            ] )
        );
    }

    public function list_item_arrow_height_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_list_item_arrow_height_layout2', [
            'default'           => $this->defaults['betterdocs_archive_list_item_arrow_height_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_archive_list_item_arrow_height_layout2', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_list_item_arrow_height_layout2',
                'label'       => __( 'List Item Arrow Height', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 200,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ],
                'priority'    => 427
            ] )
        );
    }

    public function list_item_arrow_width_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_list_item_arrow_width_layout2', [
            'default'           => $this->defaults['betterdocs_archive_list_item_arrow_width_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_archive_list_item_arrow_width_layout2', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_list_item_arrow_width_layout2',
                'label'       => __( 'List Item Arrow Width', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 200,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ],
                'priority'    => 428
            ] )
        );
    }

    public function list_item_arrow_color_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_list_item_arrow_color_layout2', [
            'default'           => $this->defaults['betterdocs_archive_list_item_arrow_color_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_archive_list_item_arrow_color_layout2',
                [
                    'label'    => __( 'List Item Arrow Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_list_item_arrow_color_layout2',
                    'priority' => 429
                ]
            )
        );
    }

    public function list_arrow_margin_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_article_list_arrow_margin_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_arrow_margin_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_archive_article_list_arrow_margin_layout2', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_arrow_margin_layout2',
                'label'       => __( 'Docs List Arrow Margin', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_archive_article_list_arrow_margin_layout2',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 430
            ] )
        );

        // Archive Page Docs List Arrow Margin Top(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_arrow_margin_top_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_arrow_margin_top_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_arrow_margin_top_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_arrow_margin_top_layout2',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_arrow_margin_layout2 betterdocs-dimension'
                ],
                'priority'    => 431
            ] )
        );

        // Archive Page Docs List Arrow Margin Right(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_arrow_margin_right_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_arrow_margin_right_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_arrow_margin_right_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_arrow_margin_right_layout2',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_arrow_margin_layout2 betterdocs-dimension'
                ],
                'priority'    => 432
            ] )
        );

        // Archive Page Docs List Arrow Margin Bottom(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_arrow_margin_bottom_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_arrow_margin_bottom_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_arrow_margin_bottom_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_arrow_margin_bottom_layout2',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_arrow_margin_layout2 betterdocs-dimension'
                ],
                'priority'    => 433
            ] )
        );

        // Archive Page Docs List Arrow Margin Left(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_arrow_margin_left_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_arrow_margin_left_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_arrow_margin_left_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_arrow_margin_left_layout2',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_arrow_margin_layout2 betterdocs-dimension'
                ],
                'priority'    => 434
            ] )
        );
    }

    public function list_excerpt_font_weight_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_article_list_excerpt_font_weight_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_excerpt_font_weight_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'choices']
        ] );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_archive_article_list_excerpt_font_weight_layout2',
                [
                    'label'    => __( 'Excerpt Font Weight', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_article_list_excerpt_font_weight_layout2',
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
                    'priority' => 435
                ]
            )
        );
    }

    public function article_list_excerpt_font_size_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_article_list_excerpt_font_size_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_excerpt_font_size_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_archive_article_list_excerpt_font_size_layout2', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_excerpt_font_size_layout2',
                'label'       => __( 'Excerpt Font Size', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 200,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ],
                'priority'    => 436
            ] )
        );
    }

    public function article_list_excerpt_font_line_height_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_article_list_excerpt_font_line_height_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_excerpt_font_line_height_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_archive_article_list_excerpt_font_line_height_layout2', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_excerpt_font_line_height_layout2',
                'label'       => __( 'Excerpt Line Height', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 200,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ],
                'priority'    => 437
            ] )
        );
    }

    public function article_list_excerpt_font_color_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_article_list_excerpt_font_color_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_excerpt_font_color_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_archive_article_list_excerpt_font_color_layout2',
                [
                    'label'    => __( 'Excerpt Font Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_article_list_excerpt_font_color_layout2',
                    'priority' => 438
                ]
            )
        );
    }

    public function article_list_excerpt_margin_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_article_list_excerpt_margin_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_excerpt_margin_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_archive_article_list_excerpt_margin_layout2', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_excerpt_margin_layout2',
                'label'       => __( 'Excerpt Margin', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_archive_article_list_excerpt_margin_layout2',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 439
            ] )
        );

        // Archive Page Excerpt Margin Top(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_excerpt_margin_top_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_excerpt_margin_top_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_excerpt_margin_top_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_excerpt_margin_top_layout2',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_excerpt_margin_layout2 betterdocs-dimension'
                ],
                'priority'    => 440
            ] )
        );

        // Archive Page Excerpt Margin Right(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_excerpt_margin_right_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_excerpt_margin_right_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_excerpt_margin_right_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_excerpt_margin_right_layout2',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_excerpt_margin_layout2 betterdocs-dimension'
                ],
                'priority'    => 441
            ] )
        );

        // Archive Page Excerpt Margin Bottom(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_excerpt_margin_bottom_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_excerpt_margin_bottom_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_excerpt_margin_bottom_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_excerpt_margin_bottom_layout2',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_excerpt_margin_layout2 betterdocs-dimension'
                ],
                'priority'    => 442
            ] )
        );

        // Archive Page Excerpt Margin Left(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_excerpt_margin_left_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_excerpt_margin_left_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_excerpt_margin_left_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_excerpt_margin_left_layout2',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_excerpt_margin_layout2 betterdocs-dimension'
                ],
                'priority'    => 443
            ] )
        );
    }

    public function article_list_excerpt_padding_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_article_list_excerpt_padding_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_excerpt_padding_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_archive_article_list_excerpt_padding_layout2', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_excerpt_padding_layout2',
                'label'       => __( 'Excerpt Padding', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_archive_article_list_excerpt_padding_layout2',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 444
            ] )
        );

        // Archive Page Excerpt Padding Top(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_excerpt_padding_top_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_excerpt_padding_top_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_excerpt_padding_top_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_excerpt_padding_top_layout2',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_excerpt_padding_layout2 betterdocs-dimension'
                ],
                'priority'    => 445
            ] )
        );

        // Archive Page Excerpt Padding Right(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_excerpt_padding_right_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_excerpt_padding_right_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_excerpt_padding_right_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_excerpt_padding_right_layout2',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_excerpt_padding_layout2 betterdocs-dimension'
                ],
                'priority'    => 446
            ] )
        );

        // Archive Page Excerpt Padding Bottom(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_excerpt_padding_bottom_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_excerpt_padding_bottom_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_excerpt_padding_bottom_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_excerpt_padding_bottom_layout2',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_excerpt_padding_layout2 betterdocs-dimension'
                ],
                'priority'    => 447
            ] )
        );

        // Archive Page Excerpt Padding Left(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_excerpt_padding_left_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_excerpt_padding_left_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_excerpt_padding_left_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_excerpt_padding_left_layout2',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_excerpt_padding_layout2 betterdocs-dimension'
                ],
                'priority'    => 448
            ] )
        );
    }

    public function article_list_counter_seperator_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_article_list_counter_seperator_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_counter_seperator_layout2'],
            'sanitize_callback' => 'esc_html'
        ] );

        $this->customizer->add_control( new SeparatorControl(
            $this->customizer, 'betterdocs_archive_article_list_counter_seperator_layout2', [
                'label'    => __( 'Category Item Count', 'betterdocs-pro' ),
                'settings' => 'betterdocs_archive_article_list_counter_seperator_layout2',
                'section'  => 'betterdocs_archive_page_settings',
                'priority' => 449
            ] )
        );
    }

    public function article_list_counter_font_weight_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_article_list_counter_font_weight_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_counter_font_weight_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'choices']
        ] );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_archive_article_list_counter_font_weight_layout2',
                [
                    'label'    => __( 'Count Font Weight', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_article_list_counter_font_weight_layout2',
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
                    'priority' => 450
                ]
            )
        );
    }

    public function article_list_counter_font_size_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_article_list_counter_font_size_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_counter_font_size_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_archive_article_list_counter_font_size_layout2', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_counter_font_size_layout2',
                'label'       => __( 'Count Font Size', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 200,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ],
                'priority'    => 451
            ] )
        );
    }

    public function article_list_counter_font_line_height_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_article_list_counter_font_line_height_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_counter_font_line_height_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_archive_article_list_counter_font_line_height_layout2', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_counter_font_line_height_layout2',
                'label'       => __( 'Count Font Line Height', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 200,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ],
                'priority'    => 452
            ] )
        );
    }

    public function article_list_counter_font_color_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_article_list_counter_font_color_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_counter_font_color_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_archive_article_list_counter_font_color_layout2',
                [
                    'label'    => __( 'Count Font Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_article_list_counter_font_color_layout2',
                    'priority' => 452
                ]
            )
        );
    }

    public function article_list_counter_border_radius_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_article_list_counter_border_radius_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_counter_border_radius_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_archive_article_list_counter_border_radius_layout2', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_counter_border_radius_layout2',
                'label'       => __( 'Count Border Radius', 'betterdocs-pro' ),
                'priority'    => 453,
                'input_attrs' => [
                    'id'    => 'betterdocs_archive_article_list_counter_border_radius_layout2',
                    'class' => 'betterdocs-dimension'
                ]
            ] )
        );

        //Archive Page Category Item Count Border Radius Top Left(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_counter_border_radius_top_left_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_counter_border_radius_top_left_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_counter_border_radius_top_left_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_counter_border_radius_top_left_layout2',
                'label'       => __( 'Top Left', 'betterdocs-pro' ),
                'priority'    => 454,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_counter_border_radius_layout2 betterdocs-dimension'
                ]
            ] )
        );

        //Archive Page Category Item Count Border Radius Top Right(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_counter_border_radius_top_right_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_counter_border_radius_top_right_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_counter_border_radius_top_right_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_counter_border_radius_top_right_layout2',
                'label'       => __( 'Top Right', 'betterdocs-pro' ),
                'priority'    => 455,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_counter_border_radius_layout2 betterdocs-dimension'
                ]
            ] )
        );

        //Archive Page Category Item Count Border Radius Bottom Right(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_counter_border_radius_bottom_right_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_counter_border_radius_bottom_right_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_counter_border_radius_bottom_right_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_counter_border_radius_bottom_right_layout2',
                'label'       => __( 'Bottom Right', 'betterdocs-pro' ),
                'priority'    => 456,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_counter_border_radius_layout2 betterdocs-dimension'
                ]
            ] )
        );

        //Archive Page Category Item Count Border Radius Bottom Left(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_counter_border_radius_bottom_left_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_counter_border_radius_bottom_left_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_counter_border_radius_bottom_left_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_counter_border_radius_bottom_left_layout2',
                'label'       => __( 'Bottom Left', 'betterdocs-pro' ),
                'priority'    => 457,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_counter_border_radius_layout2 betterdocs-dimension'
                ]
            ] )
        );
    }

    public function article_list_counter_margin_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_article_list_counter_margin_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_counter_margin_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_archive_article_list_counter_margin_layout2', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_counter_margin_layout2',
                'label'       => __( 'Count Margin', 'betterdocs-pro' ),
                'priority'    => 458,
                'input_attrs' => [
                    'id'    => 'betterdocs_archive_article_list_counter_margin_layout2',
                    'class' => 'betterdocs-dimension'
                ]
            ] )
        );

        //Archive Page Category Item Count Margin Top(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_counter_margin_top_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_counter_margin_top_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_counter_margin_top_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_counter_margin_top_layout2',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'priority'    => 459,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_counter_margin_layout2 betterdocs-dimension'
                ]
            ] )
        );

        //Archive Page Category Item Count Margin Right(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_counter_margin_right_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_counter_margin_right_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_counter_margin_right_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_counter_margin_right_layout2',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'priority'    => 460,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_counter_margin_layout2 betterdocs-dimension'
                ]
            ] ) );

        //Archive Page Category Item Count Margin Bottom(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_counter_margin_bottom_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_counter_margin_bottom_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_counter_margin_bottom_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_counter_margin_bottom_layout2',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'priority'    => 461,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_counter_margin_layout2 betterdocs-dimension'
                ]
            ] ) );

        //Archive Page Category Item Count Margin Left(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_counter_margin_left_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_counter_margin_left_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_counter_margin_left_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_counter_margin_left_layout2',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'priority'    => 462,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_counter_margin_layout2 betterdocs-dimension'
                ]
            ] )
        );
    }

    public function article_list_counter_padding_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_article_list_counter_padding_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_counter_padding_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_archive_article_list_counter_padding_layout2', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_counter_padding_layout2',
                'label'       => __( 'Count Padding', 'betterdocs-pro' ),
                'priority'    => 463,
                'input_attrs' => [
                    'id'    => 'betterdocs_archive_article_list_counter_padding_layout2',
                    'class' => 'betterdocs-dimension'
                ]
            ] ) );

        //Archive Page Category Item Count Padding Top(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_counter_padding_top_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_counter_padding_top_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_counter_padding_top_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_counter_padding_top_layout2',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'priority'    => 464,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_counter_padding_layout2 betterdocs-dimension'
                ]
            ] )
        );

        //Archive Page Category Item Count Padding Right(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_counter_padding_right_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_counter_padding_right_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_counter_padding_right_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_counter_padding_right_layout2',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'priority'    => 465,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_counter_padding_layout2 betterdocs-dimension'
                ]
            ] )
        );

        //Archive Page Category Item Count Padding Bottom(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_counter_padding_bottom_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_counter_padding_bottom_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_counter_padding_bottom_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_counter_padding_bottom_layout2',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'priority'    => 466,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_counter_padding_layout2 betterdocs-dimension'
                ]
            ] )
        );

        //Archive Page Category Item Count Padding Left(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_article_list_counter_padding_left_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_counter_padding_left_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_article_list_counter_padding_left_layout2', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_article_list_counter_padding_left_layout2',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'priority'    => 467,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_article_list_counter_padding_layout2 betterdocs-dimension'
                ]
            ] )
        );
    }

    public function article_list_counter_border_color_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_article_list_counter_border_color_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_counter_border_color_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_archive_article_list_counter_border_color_layout2',
                [
                    'label'    => __( 'Count Border Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_article_list_counter_border_color_layout2',
                    'priority' => 468
                ]
            )
        );
    }

    public function article_list_counter_back_color_layout2() {
        $this->customizer->add_setting( 'betterdocs_archive_article_list_counter_back_color_layout2', [
            'default'           => $this->defaults['betterdocs_archive_article_list_counter_back_color_layout2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_archive_article_list_counter_back_color_layout2',
                [
                    'label'    => __( 'Count Background Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_article_list_counter_back_color_layout2',
                    'priority' => 468
                ]
            )
        );
    }

    public function other_categories_seperator() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_seperator', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_seperator'],
            'sanitize_callback' => 'esc_html'
        ] );

        $this->customizer->add_control( new SeparatorControl(
            $this->customizer, 'betterdocs_archive_other_categories_seperator', [
                'label'    => __( 'Other Categories', 'betterdocs-pro' ),
                'settings' => 'betterdocs_archive_other_categories_seperator',
                'section'  => 'betterdocs_archive_page_settings',
                'priority' => 469
            ] )
        );
    }

    public function other_categories_heading_text() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_heading_text', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_heading_text'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => 'esc_html'
        ] );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_archive_other_categories_heading_text',
                [
                    'label'    => __( 'Heading Text', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_other_categories_heading_text',
                    'type'     => 'text',
                    'priority' => 470
                ]
            )
        );
    }

    public function other_categories_load_more_text() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_load_more_text', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_load_more_text'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => 'esc_html'
        ] );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_archive_other_categories_load_more_text',
                [
                    'label'    => __( 'Load More Text', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_other_categories_load_more_text',
                    'type'     => 'text',
                    'priority' => 470
                ]
            )
        );
    }

    public function other_categories_title_color() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_title_color', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_title_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_archive_other_categories_title_color',
                [
                    'label'    => __( 'Title Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_other_categories_title_color',
                    'priority' => 470
                ]
            )
        );
    }

    public function ther_categories_title_hover_color() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_title_hover_color', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_title_hover_color'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_archive_other_categories_title_hover_color',
                [
                    'label'    => __( 'Title Hover Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_other_categories_title_hover_color',
                    'priority' => 470
                ]
            )
        );
    }

    public function other_categories_title_font_weight() {
        $this->customizer->add_setting(
            'betterdocs_archive_other_categories_title_font_weight',
            [
                'default'           => $this->defaults['betterdocs_archive_other_categories_title_font_weight'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'choices']
            ]
        );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_archive_other_categories_title_font_weight',
                [
                    'label'    => __( 'Title Font Weight', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_other_categories_title_font_weight',
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
                    'priority' => 471
                ]
            )
        );
    }

    public function other_categories_title_font_size() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_title_font_size', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_title_font_size'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_archive_other_categories_title_font_size', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_title_font_size',
                'label'       => __( 'Title Font Size', 'betterdocs-pro' ),
                'priority'    => 472,
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

    public function other_categories_title_line_height() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_title_line_height', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_title_line_height'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_archive_other_categories_title_line_height', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_title_line_height',
                'label'       => __( 'Title Line Height', 'betterdocs-pro' ),
                'priority'    => 473,
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

    public function other_categories_image_size() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_image_size', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_image_size'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_archive_other_categories_image_size', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_image_size',
                'label'       => __( 'Image Size', 'betterdocs-pro' ),
                'priority'    => 473,
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 200,
                    'step'   => 1,
                    'suffix' => '%' //optional suffix
                ]
            ] )
        );
    }

    public function other_categories_title_padding() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_title_padding', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_title_padding'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_archive_other_categories_title_padding', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_title_padding',
                'label'       => __( 'Title Padding', 'betterdocs-pro' ),
                'priority'    => 474,
                'input_attrs' => [
                    'id'    => 'betterdocs_archive_other_categories_title_padding',
                    'class' => 'betterdocs-dimension'
                ]
            ] )
        );

        // Archive Page Category Other Categories Title Padding Top(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_title_padding_top', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_title_padding_top'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_title_padding_top', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_title_padding_top',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'priority'    => 475,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_title_padding betterdocs-dimension'
                ]
            ] )
        );

        // Archive Page Category Other Categories Title Padding Right(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_title_padding_right', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_title_padding_right'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_title_padding_right', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_title_padding_right',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_title_padding betterdocs-dimension'
                ],
                'priority'    => 476
            ] )
        );

        // Archive Page Category Other Categories Title Padding Bottom(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_title_padding_bottom', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_title_padding_bottom'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_title_padding_bottom', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_title_padding_bottom',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_title_padding betterdocs-dimension'
                ],
                'priority'    => 477
            ] )
        );

        // Archive Page Category Other Categories Title Padding Left(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_title_padding_left', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_title_padding_left'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_title_padding_left', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_title_padding_left',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_title_padding betterdocs-dimension'
                ],
                'priority'    => 478
            ] )
        );
    }

    public function other_categories_title_margin() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_title_margin', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_title_margin'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_archive_other_categories_title_margin', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_title_margin',
                'label'       => __( 'Title Margin', 'betterdocs-pro' ),
                'priority'    => 479,
                'input_attrs' => [
                    'id'    => 'betterdocs_archive_other_categories_title_margin',
                    'class' => 'betterdocs-dimension'
                ]
            ] )
        );

        // Archive Page Category Other Categories Title Margin Top(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_title_margin_top', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_title_margin_top'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_title_margin_top', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_title_margin_top',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'priority'    => 480,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_title_margin betterdocs-dimension'
                ]
            ] )
        );

        // Archive Page Category Other Categories Title Margin Right(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_title_margin_right', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_title_margin_right'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_title_margin_right', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_title_margin_right',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'priority'    => 481,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_title_margin betterdocs-dimension'
                ]
            ] )
        );

        // Archive Page Category Other Categories Title Margin Bottom(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_title_margin_bottom', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_title_margin_bottom'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_title_margin_bottom', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_title_margin_bottom',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'priority'    => 482,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_title_margin betterdocs-dimension'
                ]
            ] )
        );

        // Archive Page Category Other Categories Title Margin Left(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_title_margin_left', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_title_margin_left'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_title_margin_left', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_title_margin_left',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'priority'    => 483,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_title_margin betterdocs-dimension'
                ]
            ] )
        );
    }

    public function other_categories_count_color() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_count_color', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_count_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_archive_other_categories_count_color',
                [
                    'label'    => __( 'Count Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_other_categories_count_color',
                    'priority' => 484
                ]
            )
        );
    }

    public function other_categories_count_back_color() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_count_back_color', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_count_back_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_archive_other_categories_count_back_color',
                [
                    'label'    => __( 'Count Background Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_other_categories_count_back_color',
                    'priority' => 485
                ] )
        );
    }

    public function other_categories_count_back_color_hover() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_count_back_color_hover', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_count_back_color_hover'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_archive_other_categories_count_back_color_hover',
                [
                    'label'    => __( 'Count Background Hover Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_other_categories_count_back_color_hover',
                    'priority' => 486
                ]
            )
        );
    }

    public function other_categories_count_line_height() {
        $this->customizer->add_setting(
            'betterdocs_archive_other_categories_count_line_height',
            [
                'default'           => $this->defaults['betterdocs_archive_other_categories_count_line_height'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ]
        );

        $this->customizer->add_control(
            new RangeValueControl(
                $this->customizer,
                'betterdocs_archive_other_categories_count_line_height',
                [
                    'type'        => 'betterdocs-range-value',
                    'section'     => 'betterdocs_archive_page_settings',
                    'settings'    => 'betterdocs_archive_other_categories_count_line_height',
                    'label'       => __( 'Count Line Height', 'betterdocs-pro' ),
                    'priority'    => 487,
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

    public function other_categories_count_font_weight() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_count_font_weight', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_count_font_weight'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'choices']
        ] );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_archive_other_categories_count_font_weight',
                [
                    'label'    => __( 'Font Weight', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_other_categories_count_font_weight',
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
                    'priority' => 488
                ]
            )
        );
    }

    public function other_categories_count_font_size() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_count_font_size', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_count_font_size'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_archive_other_categories_count_font_size', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_count_font_size',
                'label'       => __( 'Count Font Size', 'betterdocs-pro' ),
                'priority'    => 489,
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

    public function other_categories_count_border_radius() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_count_border_radius', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_count_border_radius'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_archive_other_categories_count_border_radius', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_count_border_radius',
                'label'       => __( 'Count Border Radius', 'betterdocs-pro' ),
                'priority'    => 490,
                'input_attrs' => [
                    'id'    => 'betterdocs_archive_other_categories_count_border_radius',
                    'class' => 'betterdocs-dimension'
                ]
            ] ) );

        // Archive Page Category Other Categories Count Border Radius Top Left(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_count_border_radius_topleft', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_count_border_radius_topleft'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_count_border_radius_topleft', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_count_border_radius_topleft',
                'label'       => __( 'Top Left', 'betterdocs-pro' ),
                'priority'    => 490,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_count_border_radius betterdocs-dimension'
                ]
            ] ) );

        // Archive Page Category Other Categories Count Border Radius Top Right(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_count_border_radius_topright', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_count_border_radius_topright'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_count_border_radius_topright', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_count_border_radius_topright',
                'label'       => __( 'Top Right', 'betterdocs-pro' ),
                'priority'    => 491,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_count_border_radius betterdocs-dimension'
                ]
            ] ) );

        // Archive Page Category Other Categories Count Border Radius Bottom Right(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_count_border_radius_bottomright', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_count_border_radius_bottomright'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_count_border_radius_bottomright', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_count_border_radius_bottomright',
                'label'       => __( 'Bottom Right', 'betterdocs-pro' ),
                'priority'    => 492,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_count_border_radius betterdocs-dimension'
                ]
            ] ) );

        // Archive Page Category Other Categories Count Border Radius Bottom Left(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_count_border_radius_bottomleft', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_count_border_radius_bottomleft'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_count_border_radius_bottomleft', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_count_border_radius_bottomleft',
                'label'       => __( 'Bottom Left', 'betterdocs-pro' ),
                'priority'    => 493,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_count_border_radius betterdocs-dimension'
                ]
            ] ) );
    }

    public function other_categories_count_padding() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_count_padding', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_count_padding'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_archive_other_categories_count_padding', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_count_padding',
                'label'       => __( 'Count Padding', 'betterdocs-pro' ),
                'priority'    => 494,
                'input_attrs' => [
                    'id'    => 'betterdocs_archive_other_categories_count_padding',
                    'class' => 'betterdocs-dimension'
                ]
            ] ) );

        // Archive Page Category Other Categories Count Padding Top(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_count_padding_top', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_count_padding_top'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_count_padding_top', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_count_padding_top',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'priority'    => 495,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_count_padding betterdocs-dimension'
                ]
            ] )
        );

        // Archive Page Category Other Categories Count Padding Right(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_count_padding_right', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_count_padding_right'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_count_padding_right', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_count_padding_right',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'priority'    => 496,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_count_padding betterdocs-dimension'
                ]
            ] )
        );

        // Archive Page Category Other Categories Count Padding Bottom(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_count_padding_bottom', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_count_padding_bottom'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_count_padding_bottom', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_count_padding_bottom',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'priority'    => 497,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_count_padding betterdocs-dimension'
                ]
            ] )
        );

        // Archive Page Category Other Categories Count Padding Left(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_count_padding_left', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_count_padding_left'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_count_padding_left', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_count_padding_left',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'priority'    => 498,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_count_padding betterdocs-dimension'
                ]
            ] )
        );
    }

    public function other_categories_count_margin() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_count_margin', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_count_margin'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_archive_other_categories_count_margin', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_count_margin',
                'label'       => __( 'Count Margin', 'betterdocs-pro' ),
                'priority'    => 499,
                'input_attrs' => [
                    'id'    => 'betterdocs_archive_other_categories_count_margin',
                    'class' => 'betterdocs-dimension'
                ]
            ] )
        );

        // Archive Page Category Other Categories Count Margin Top(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_count_margin_top', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_count_margin_top'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_count_margin_top', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_count_margin_top',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'priority'    => 500,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_count_margin betterdocs-dimension'
                ]
            ] )
        );

        // Archive Page Category Other Categories Count Margin Right(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_count_margin_right', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_count_margin_right'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_count_margin_right', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_count_margin_right',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'priority'    => 501,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_count_margin betterdocs-dimension'
                ]
            ] )
        );

        // Archive Page Category Other Categories Count Margin Bottom(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_count_margin_bottom', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_count_margin_bottom'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_count_margin_bottom', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_count_margin_bottom',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'priority'    => 502,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_count_margin betterdocs-dimension'
                ]
            ] )
        );

        // Archive Page Category Other Categories Count Margin Left(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_count_margin_left', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_count_margin_left'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_count_margin_left', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_count_margin_left',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'priority'    => 503,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_count_margin betterdocs-dimension'
                ]
            ] )
        );
    }

    public function _other_categories_description_color() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_description_color', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_description_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_archive_other_categories_description_color',
                [
                    'label'    => __( 'Description Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_other_categories_description_color',
                    'priority' => 504
                ]
            )
        );
    }

    public function other_categories_description_font_weight() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_description_font_weight', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_description_font_weight'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'choices']
        ] );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_archive_other_categories_description_font_weight',
                [
                    'label'    => __( 'Description Font Weight', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_other_categories_description_font_weight',
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
                    'priority' => 505
                ]
            )
        );
    }

    public function other_categories_description_font_size() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_description_font_size', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_description_font_size'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_archive_other_categories_description_font_size', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_description_font_size',
                'label'       => __( 'Description Font Size', 'betterdocs-pro' ),
                'priority'    => 506,
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

    public function other_categories_description_line_height() {
        $this->customizer->add_setting(
            'betterdocs_archive_other_categories_description_line_height',
            [
                'default'           => $this->defaults['betterdocs_archive_other_categories_description_line_height'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ]
        );

        $this->customizer->add_control(
            new RangeValueControl(
                $this->customizer,
                'betterdocs_archive_other_categories_description_line_height',
                [
                    'type'        => 'betterdocs-range-value',
                    'section'     => 'betterdocs_archive_page_settings',
                    'settings'    => 'betterdocs_archive_other_categories_description_line_height',
                    'label'       => __( 'Description Font Line Height', 'betterdocs-pro' ),
                    'priority'    => 507,
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

    public function other_categories_description_padding() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_description_padding', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_description_padding'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_archive_other_categories_description_padding', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_description_padding',
                'label'       => __( 'Description Padding', 'betterdocs-pro' ),
                'priority'    => 508,
                'input_attrs' => [
                    'id'    => 'betterdocs_archive_other_categories_description_padding',
                    'class' => 'betterdocs-dimension'
                ]
            ] ) );

        // Archive Page Category Other Categories Description Padding Top(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_description_padding_top', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_description_padding_top'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_description_padding_top', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_description_padding_top',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'priority'    => 509,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_description_padding betterdocs-dimension'
                ]
            ] ) );

        // Archive Page Category Other Categories Description Padding Right(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_description_padding_right', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_description_padding_right'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_description_padding_right', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_description_padding_right',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'priority'    => 510,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_description_padding betterdocs-dimension'
                ]
            ] ) );

        // Archive Page Category Other Categories Description Padding Bottom(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_description_padding_bottom', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_description_padding_bottom'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_description_padding_bottom', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_description_padding_bottom',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'priority'    => 511,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_description_padding betterdocs-dimension'
                ]
            ] ) );

        // Archive Page Category Other Categories Description Padding Left(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_description_padding_left', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_description_padding_left'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_description_padding_left', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_description_padding_left',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'priority'    => 512,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_description_padding betterdocs-dimension'
                ]
            ] )
        );
    }

    public function other_categories_button_color() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_button_color', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_button_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_archive_other_categories_button_color',
                [
                    'label'    => __( 'Button Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_other_categories_button_color',
                    'priority' => 513
                ]
            )
        );
    }

    public function other_categories_button_back_color() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_button_back_color', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_button_back_color'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_archive_other_categories_button_back_color',
                [
                    'label'    => __( 'Button Background Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_other_categories_button_back_color',
                    'priority' => 514
                ]
            )
        );
    }

    public function other_categories_button_back_color_hover() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_button_back_color_hover', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_button_back_color_hover'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_archive_other_categories_button_back_color_hover',
                [
                    'label'    => __( 'Button Background Hover Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_other_categories_button_back_color_hover',
                    'priority' => 514
                ]
            )
        );
    }

    public function other_categories_button_font_weight() {
        $this->customizer->add_setting(
            'betterdocs_archive_other_categories_button_font_weight',
            [
                'default'           => $this->defaults['betterdocs_archive_other_categories_button_font_weight'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'choices']
            ]
        );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_archive_other_categories_button_font_weight',
                [
                    'label'    => __( 'Button Font Weight', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_archive_page_settings',
                    'settings' => 'betterdocs_archive_other_categories_button_font_weight',
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
                    'priority' => 515
                ]
            )
        );
    }

    public function other_categories_button_font_size() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_button_font_size', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_button_font_size'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_archive_other_categories_button_font_size', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_button_font_size',
                'label'       => __( 'Button Font Size', 'betterdocs-pro' ),
                'priority'    => 516,
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 100,
                    'step'   => 1,
                    'suffix' => 'px' //optional suffix
                ]
            ] ) );
    }

    public function other_categories_button_font_line_height() {
        $this->customizer->add_setting(
            'betterdocs_archive_other_categories_button_font_line_height',
            [
                'default'           => $this->defaults['betterdocs_archive_other_categories_button_font_line_height'],
                'capability'        => 'edit_theme_options',
                'transport'         => 'postMessage',
                'sanitize_callback' => [$this->sanitizer, 'integer']
            ]
        );

        $this->customizer->add_control(
            new RangeValueControl(
                $this->customizer,
                'betterdocs_archive_other_categories_button_font_line_height',
                [
                    'type'        => 'betterdocs-range-value',
                    'section'     => 'betterdocs_archive_page_settings',
                    'settings'    => 'betterdocs_archive_other_categories_button_font_line_height',
                    'label'       => __( 'List Font Line Height', 'betterdocs-pro' ),
                    'priority'    => 517,
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

    public function other_categories_button_border_radius() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_button_border_radius', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_button_border_radius'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_archive_other_categories_button_border_radius', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_button_border_radius',
                'label'       => __( 'Button Border Radius', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_archive_other_categories_button_border_radius',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 518
            ] ) );

        // Archive Page Category Other Categories Button Border Radius Top Left(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_button_border_radius_top_left', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_button_border_radius_top_left'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ]
        );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_button_border_radius_top_left', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_button_border_radius_top_left',
                'label'       => __( 'Top Left', 'betterdocs-pro' ),
                'priority'    => 519,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_button_border_radius betterdocs-dimension'
                ]
            ] )
        );

        // Archive Page Category Other Categories Button Border Radius Top Right(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_button_border_radius_top_right', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_button_border_radius_top_right'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_button_border_radius_top_right', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_button_border_radius_top_right',
                'label'       => __( 'Top Right', 'betterdocs-pro' ),
                'priority'    => 520,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_button_border_radius betterdocs-dimension'
                ]
            ] )
        );

        // Archive Page Category Other Categories Button Border Radius Bottom Right(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_button_border_radius_bottom_right', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_button_border_radius_bottom_right'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_button_border_radius_bottom_right', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_button_border_radius_bottom_right',
                'label'       => __( 'Bottom Right', 'betterdocs-pro' ),
                'priority'    => 521,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_button_border_radius betterdocs-dimension'
                ]
            ] )
        );

        // Archive Page Category Other Categories Button Border Radius Bottom Left(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_button_border_radius_bottom_left', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_button_border_radius_bottom_left'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_button_border_radius_bottom_left', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_button_border_radius_bottom_left',
                'label'       => __( 'Bottom Left', 'betterdocs-pro' ),
                'priority'    => 522,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_button_border_radius betterdocs-dimension'
                ]
            ] )
        );
    }

    public function other_categories_button_padding() {
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_button_padding', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_button_padding'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new TitleControl(
            $this->customizer, 'betterdocs_archive_other_categories_button_padding', [
                'type'        => 'betterdocs-title',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_button_padding',
                'label'       => __( 'Button Padding', 'betterdocs-pro' ),
                'input_attrs' => [
                    'id'    => 'betterdocs_archive_other_categories_button_padding',
                    'class' => 'betterdocs-dimension'
                ],
                'priority'    => 523
            ] ) );

        // Archive Page Category Other Categories Button Padding Top(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_button_padding_top', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_button_padding_top'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_button_padding_top', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_button_padding_top',
                'label'       => __( 'Top', 'betterdocs-pro' ),
                'priority'    => 524,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_button_padding betterdocs-dimension'
                ]
            ] )
        );

        // Archive Page Category Other Categories Button Padding Right(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_button_padding_right', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_button_padding_right'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_button_padding_right', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_button_padding_right',
                'label'       => __( 'Right', 'betterdocs-pro' ),
                'priority'    => 525,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_button_padding betterdocs-dimension'
                ]
            ] )
        );

        // Archive Page Category Other Categories Button Padding Bottom(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_button_padding_bottom', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_button_padding_bottom'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_button_padding_bottom', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_button_padding_bottom',
                'label'       => __( 'Bottom', 'betterdocs-pro' ),
                'priority'    => 526,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_button_padding betterdocs-dimension'
                ]
            ] )
        );

        // Archive Page Category Other Categories Button Padding Left(Archive Page Layout 2)
        $this->customizer->add_setting( 'betterdocs_archive_other_categories_button_padding_left', [
            'default'           => $this->defaults['betterdocs_archive_other_categories_button_padding_left'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new DimensionControl(
            $this->customizer, 'betterdocs_archive_other_categories_button_padding_left', [
                'type'        => 'betterdocs-dimension',
                'section'     => 'betterdocs_archive_page_settings',
                'settings'    => 'betterdocs_archive_other_categories_button_padding_left',
                'label'       => __( 'Left', 'betterdocs-pro' ),
                'priority'    => 527,
                'input_attrs' => [
                    'class' => 'betterdocs_archive_other_categories_button_padding betterdocs-dimension'
                ]
            ] )
        );
    }
}
