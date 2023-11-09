<?php

namespace WPDeveloper\BetterDocsPro\Admin\Customizer\Sections;

use WP_Customize_Control;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\ToggleControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\SeparatorControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\AlphaColorControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\RadioImageControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\RangeValueControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Controls\MultiDimensionControl;
use WPDeveloper\BetterDocs\Admin\Customizer\Sections\FaqBuilder as FreeFaqBuilder;

class FaqBuilder extends FreeFaqBuilder {

    public function section_mkb_seperator() {
        $this->customizer->add_setting( 'betterdocs_faq_section_mkb_seperator', [
            'default'           => $this->defaults['betterdocs_faq_section_mkb_seperator'],
            'sanitize_callback' => 'esc_html'
        ] );

        $this->customizer->add_control( new SeparatorControl(
            $this->customizer, 'betterdocs_faq_section_mkb_seperator', [
                'label'    => __( 'Multiple KB FAQ', 'betterdocs-pro' ),
                'settings' => 'betterdocs_faq_section_mkb_seperator',
                'section'  => 'betterdocs_faq_section',
                'priority' => 601
            ] )
        );
    }

    public function switch_mkb() {
        $this->customizer->add_setting( 'betterdocs_faq_switch_mkb', [
            'default'    => $this->defaults['betterdocs_faq_switch_mkb'],
            'capability' => 'edit_theme_options'
        ] );

        $this->customizer->add_control( new ToggleControl(
            $this->customizer,
            'betterdocs_faq_switch_mkb', [
                'label'    => __( 'Enable FAQ', 'betterdocs-pro' ),
                'section'  => 'betterdocs_faq_section',
                'settings' => 'betterdocs_faq_switch_mkb',
                'type'     => 'light', // light, ios, flat
                'priority' => 601
            ] )
        );
    }

    /**
     * @todo
     */
    public function select_specific_faq_mkb() {
        $this->customizer->add_setting( 'betterdocs_select_specific_faq_mkb', [
            'default'    => $this->defaults['betterdocs_select_specific_faq_mkb'],
            'capability' => 'edit_theme_options'
        ] );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_select_specific_faq_mkb',
                [
                    'label'    => __( 'Select FAQ Groups', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_faq_section',
                    'settings' => 'betterdocs_select_specific_faq_mkb',
                    'type'     => 'select',
                    'choices'  => betterdocs()->query->get_faq_terms( [
                        'all' => __( 'Show All', 'betterdocs-pro' )
                    ] ),
                    'priority' => 602
                ] )
        );
    }

    public function select_faq_template_mkb() {
        $this->customizer->add_setting( 'betterdocs_select_faq_template_mkb', [
            'default'           => $this->defaults['betterdocs_select_faq_template_mkb'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => [$this->sanitizer, 'select']
        ] );

        $this->customizer->add_control(
            new RadioImageControl(
                $this->customizer,
                'betterdocs_select_faq_template_mkb',
                [
                    'type'     => 'betterdocs-radio-image',
                    'settings' => 'betterdocs_select_faq_template_mkb',
                    'section'  => 'betterdocs_faq_section',
                    'label'    => __( 'Select FAQ Layout', 'betterdocs-pro' ),
                    'priority' => 603,
                    'choices'  => [
                        'layout-1' => [
                            'label' => __( 'Modern Layout', 'betterdocs-pro' ),
                            'image' => $this->assets->icon( 'customizer/faq/layout-1.png', true )
                        ],
                        'layout-2' => [
                            'label' => __( 'Classic Layout', 'betterdocs-pro' ),
                            'image' => $this->assets->icon( 'customizer/faq/layout-2.png', true )
                        ]
                    ]
                ]
            )
        );
    }

    public function title_text_mkb() {
        $this->customizer->add_setting( 'betterdocs_faq_title_text_mkb', [
            'default'           => $this->defaults['betterdocs_faq_title_text_mkb'],
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => 'esc_html'
        ] );

        $this->customizer->add_control(
            new WP_Customize_Control(
                $this->customizer,
                'betterdocs_faq_title_text_mkb',
                [
                    'label'    => __( 'Section Title Text', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_faq_section',
                    'priority' => 604,
                    'settings' => 'betterdocs_faq_title_text_mkb',
                    'type'     => 'text'
                ]
            )
        );
    }

    public function title_margin_mkb_layout_1() {
        $this->customizer->add_setting( 'betterdocs_faq_title_margin_mkb_layout_1', [
            'default'    => $this->defaults['betterdocs_faq_title_margin_mkb_layout_1'],
            'transport'  => 'postMessage',
            'capability' => 'edit_theme_options'
        ] );

        $this->customizer->add_control(
            new MultiDimensionControl(
                $this->customizer,
                'betterdocs_faq_title_margin_mkb_layout_1',
                [
                    'label'        => __( 'FAQ Section Title Margin (PX)', 'betterdocs-pro' ),
                    'section'      => 'betterdocs_faq_section',
                    'settings'     => 'betterdocs_faq_title_margin_mkb_layout_1',
                    'priority'     => 605,
                    'input_fields' => [
                        'input1' => __( 'top', 'betterdocs-pro' ),
                        'input2' => __( 'right', 'betterdocs-pro' ),
                        'input3' => __( 'bottom', 'betterdocs-pro' ),
                        'input4' => __( 'left', 'betterdocs-pro' )
                    ],
                    'defaults'     => [
                        'input1' => 0,
                        'input2' => 0,
                        'input3' => 0,
                        'input4' => 0
                    ]
                ]
            )
        );
    }

    public function title_color_mkb_layout_1() {
        $this->customizer->add_setting( 'betterdocs_faq_title_color_mkb_layout_1', [
            'default'           => $this->defaults['betterdocs_faq_title_color_mkb_layout_1'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_faq_title_color_mkb_layout_1',
                [
                    'label'    => __( 'Section Title Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_faq_section',
                    'settings' => 'betterdocs_faq_title_color_mkb_layout_1',
                    'priority' => 606
                ]
            )
        );
    }

    public function title_font_size_mkb_layout_1() {
        $this->customizer->add_setting( 'betterdocs_faq_title_font_size_mkb_layout_1', [
            'default'           => $this->defaults['betterdocs_faq_title_font_size_mkb_layout_1'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_faq_title_font_size_mkb_layout_1', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_faq_section',
                'settings'    => 'betterdocs_faq_title_font_size_mkb_layout_1',
                'label'       => __( 'Section Title Font Size', 'betterdocs-pro' ),
                'priority'    => 607,
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 50,
                    'step'   => 1,
                    'suffix' => 'px' // optional suffix
                ]
            ] )
        );
    }

    public function category_title_color_mkb_layout_1() {
        $this->customizer->add_setting( 'betterdocs_faq_category_title_color_mkb_layout_1', [
            'default'           => $this->defaults['betterdocs_faq_category_title_color_mkb_layout_1'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_faq_category_title_color_mkb_layout_1',
                [
                    'label'    => __( 'Group Title Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_faq_section',
                    'priority' => 608,
                    'settings' => 'betterdocs_faq_category_title_color_mkb_layout_1'
                ]
            )
        );
    }

    public function category_name_font_size_mkb_layout_1() {
        $this->customizer->add_setting( 'betterdocs_faq_category_name_font_size_mkb_layout_1', [
            'default'           => $this->defaults['betterdocs_faq_category_name_font_size_mkb_layout_1'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_faq_category_name_font_size_mkb_layout_1', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_faq_section',
                'settings'    => 'betterdocs_faq_category_name_font_size_mkb_layout_1',
                'label'       => __( 'Group Title Font Size', 'betterdocs-pro' ),
                'priority'    => 609,
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 50,
                    'step'   => 1,
                    'suffix' => 'px' // optional suffix
                ]
            ] )
        );
    }

    public function category_name_padding_mkb_layout_1() {
        $this->customizer->add_setting( 'betterdocs_faq_category_name_padding_mkb_layout_1', [
            'default'    => $this->defaults['betterdocs_faq_category_name_padding_mkb_layout_1'],
            'transport'  => 'postMessage',
            'capability' => 'edit_theme_options'
        ] );

        $this->customizer->add_control(
            new MultiDimensionControl(
                $this->customizer,
                'betterdocs_faq_category_name_padding_mkb_layout_1',
                [
                    'label'        => __( 'Group Title Padding (PX)', 'betterdocs-pro' ),
                    'section'      => 'betterdocs_faq_section',
                    'settings'     => 'betterdocs_faq_category_name_padding_mkb_layout_1',
                    'priority'     => 610,
                    'input_fields' => [
                        'input1' => __( 'top', 'betterdocs-pro' ),
                        'input2' => __( 'right', 'betterdocs-pro' ),
                        'input3' => __( 'bottom', 'betterdocs-pro' ),
                        'input4' => __( 'left', 'betterdocs-pro' )
                    ],
                    'defaults'     => [
                        'input1' => 20,
                        'input2' => 20,
                        'input3' => 20,
                        'input4' => 20
                    ]
                ]
            )
        );
    }

    public function list_color_mkb_layout_1() {
        $this->customizer->add_setting( 'betterdocs_faq_list_color_mkb_layout_1', [
            'default'           => $this->defaults['betterdocs_faq_list_color_mkb_layout_1'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_faq_list_color_mkb_layout_1',
                [
                    'label'    => __( 'FAQ List Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_faq_section',
                    'settings' => 'betterdocs_faq_list_color_mkb_layout_1',
                    'priority' => 611
                ]
            )
        );
    }

    public function list_background_color_mkb_layout_1() {
        $this->customizer->add_setting( 'betterdocs_faq_list_background_color_mkb_layout_1', [
            'default'           => $this->defaults['betterdocs_faq_list_background_color_mkb_layout_1'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_faq_list_background_color_mkb_layout_1',
                [
                    'label'    => __( 'FAQ List Background Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_faq_section',
                    'settings' => 'betterdocs_faq_list_background_color_mkb_layout_1',
                    'priority' => 612
                ]
            )
        );
    }

    public function list_content_background_color_mkb_layout_1() {
        $this->customizer->add_setting( 'betterdocs_faq_list_content_background_color_mkb_layout_1', [
            'default'           => $this->defaults['betterdocs_faq_list_content_background_color_mkb_layout_1'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_faq_list_content_background_color_mkb_layout_1',
                [
                    'label'    => __( 'FAQ List Content Background Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_faq_section',
                    'settings' => 'betterdocs_faq_list_content_background_color_mkb_layout_1',
                    'priority' => 613
                ]
            )
        );
    }

    public function ist_content_color_mkb_layout_1() {
        $this->customizer->add_setting( 'betterdocs_faq_list_content_color_mkb_layout_1', [
            'default'           => $this->defaults['betterdocs_faq_list_content_color_mkb_layout_1'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_faq_list_content_color_mkb_layout_1',
                [
                    'label'    => __( 'FAQ List Content Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_faq_section',
                    'settings' => 'betterdocs_faq_list_content_color_mkb_layout_1',
                    'priority' => 614
                ]
            )
        );
    }

    public function list_content_font_size_mkb_layout_1() {
        $this->customizer->add_setting( 'betterdocs_faq_list_content_font_size_mkb_layout_1', [
            'default'           => $this->defaults['betterdocs_faq_list_content_font_size_mkb_layout_1'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_faq_list_content_font_size_mkb_layout_1', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_faq_section',
                'settings'    => 'betterdocs_faq_list_content_font_size_mkb_layout_1',
                'label'       => __( 'FAQ List Content Font Size', 'betterdocs-pro' ),
                'priority'    => 615,
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

    public function list_font_size_mkb_layout_1() {
        $this->customizer->add_setting( 'betterdocs_faq_list_font_size_mkb_layout_1', [
            'default'           => $this->defaults['betterdocs_faq_list_font_size_mkb_layout_1'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_faq_list_font_size_mkb_layout_1', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_faq_section',
                'settings'    => 'betterdocs_faq_list_font_size_mkb_layout_1',
                'label'       => __( 'FAQ List Font Size', 'betterdocs-pro' ),
                'priority'    => 616,
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 50,
                    'step'   => 1,
                    'suffix' => 'px' // optional suffix
                ]
            ] )
        );
    }

    public function list_padding_mkb_layout_1() {
        $this->customizer->add_setting( 'betterdocs_faq_list_padding_mkb_layout_1', [
            'default'    => $this->defaults['betterdocs_faq_list_padding_mkb_layout_1'],
            'transport'  => 'postMessage',
            'capability' => 'edit_theme_options'
        ] );

        $this->customizer->add_control(
            new MultiDimensionControl(
                $this->customizer,
                'betterdocs_faq_list_padding_mkb_layout_1',
                [
                    'label'        => __( 'FAQ List Padding (PX)', 'betterdocs-pro' ),
                    'section'      => 'betterdocs_faq_section',
                    'settings'     => 'betterdocs_faq_list_padding_mkb_layout_1',
                    'priority'     => 617,
                    'input_fields' => [
                        'input1' => __( 'top', 'betterdocs-pro' ),
                        'input2' => __( 'right', 'betterdocs-pro' ),
                        'input3' => __( 'bottom', 'betterdocs-pro' ),
                        'input4' => __( 'left', 'betterdocs-pro' )
                    ],
                    'defaults'     => [
                        'input1' => 20,
                        'input2' => 20,
                        'input3' => 20,
                        'input4' => 20
                    ]
                ]
            )
        );
    }

    public function category_title_color_mkb_layout_2() {
        $this->customizer->add_setting( 'betterdocs_faq_category_title_color_mkb_layout_2', [
            'default'           => $this->defaults['betterdocs_faq_category_title_color_mkb_layout_2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_faq_category_title_color_mkb_layout_2',
                [
                    'label'    => __( 'Group Title Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_faq_section',
                    'priority' => 618,
                    'settings' => 'betterdocs_faq_category_title_color_mkb_layout_2'
                ]
            )
        );
    }

    public function category_name_font_size_mkb_layout_2() {
        $this->customizer->add_setting( 'betterdocs_faq_category_name_font_size_mkb_layout_2', [
            'default'           => $this->defaults['betterdocs_faq_category_name_font_size_mkb_layout_2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_faq_category_name_font_size_mkb_layout_2', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_faq_section',
                'settings'    => 'betterdocs_faq_category_name_font_size_mkb_layout_2',
                'label'       => __( 'Group Title Font Size', 'betterdocs-pro' ),
                'priority'    => 618,
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 50,
                    'step'   => 1,
                    'suffix' => 'px' // optional suffix
                ]
            ] )
        );
    }

    public function category_name_padding_mkb_layout_2() {
        $this->customizer->add_setting( 'betterdocs_faq_category_name_padding_mkb_layout_2', [
            'default'    => $this->defaults['betterdocs_faq_category_name_padding_mkb_layout_2'],
            'transport'  => 'postMessage',
            'capability' => 'edit_theme_options'
        ] );

        $this->customizer->add_control(
            new MultiDimensionControl(
                $this->customizer,
                'betterdocs_faq_category_name_padding_mkb_layout_2',
                [
                    'label'        => __( 'Group Title Padding (PX)', 'betterdocs-pro' ),
                    'section'      => 'betterdocs_faq_section',
                    'settings'     => 'betterdocs_faq_category_name_padding_mkb_layout_2',
                    'priority'     => 618,
                    'input_fields' => [
                        'input1' => __( 'top', 'betterdocs-pro' ),
                        'input2' => __( 'right', 'betterdocs-pro' ),
                        'input3' => __( 'bottom', 'betterdocs-pro' ),
                        'input4' => __( 'left', 'betterdocs-pro' )
                    ],
                    'defaults'     => [
                        'input1' => 20,
                        'input2' => 20,
                        'input3' => 20,
                        'input4' => 20
                    ]
                ]
            )
        );
    }

    public function list_color_mkb_layout_2() {
        $this->customizer->add_setting( 'betterdocs_faq_list_color_mkb_layout_2', [
            'default'           => $this->defaults['betterdocs_faq_list_color_mkb_layout_2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_faq_list_color_mkb_layout_2',
                [
                    'label'    => __( 'FAQ List Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_faq_section',
                    'settings' => 'betterdocs_faq_list_color_mkb_layout_2',
                    'priority' => 618
                ]
            )
        );
    }

    public function list_background_color_mkb_layout_2() {
        $this->customizer->add_setting( 'betterdocs_faq_list_background_color_mkb_layout_2', [
            'default'           => $this->defaults['betterdocs_faq_list_background_color_mkb_layout_2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_faq_list_background_color_mkb_layout_2',
                [
                    'label'    => __( 'FAQ List Background Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_faq_section',
                    'settings' => 'betterdocs_faq_list_background_color_mkb_layout_2',
                    'priority' => 618
                ]
            )
        );
    }

    public function list_content_background_color_mkb_layout_2() {
        $this->customizer->add_setting( 'betterdocs_faq_list_content_background_color_mkb_layout_2', [
            'default'           => $this->defaults['betterdocs_faq_list_content_background_color_mkb_layout_2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_faq_list_content_background_color_mkb_layout_2',
                [
                    'label'    => __( 'FAQ List Content Background Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_faq_section',
                    'settings' => 'betterdocs_faq_list_content_background_color_mkb_layout_2',
                    'priority' => 618
                ]
            )
        );
    }

    public function list_content_color_mkb_layout_2() {
        $this->customizer->add_setting( 'betterdocs_faq_list_content_color_mkb_layout_2', [
            'default'           => $this->defaults['betterdocs_faq_list_content_color_mkb_layout_2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'rgba']
        ] );

        $this->customizer->add_control(
            new AlphaColorControl(
                $this->customizer,
                'betterdocs_faq_list_content_color_mkb_layout_2',
                [
                    'label'    => __( 'FAQ List Content Color', 'betterdocs-pro' ),
                    'section'  => 'betterdocs_faq_section',
                    'settings' => 'betterdocs_faq_list_content_color_mkb_layout_2',
                    'priority' => 618
                ]
            )
        );
    }

    public function list_content_font_size_mkb_layout_2() {
        $this->customizer->add_setting( 'betterdocs_faq_list_content_font_size_mkb_layout_2', [
            'default'           => $this->defaults['betterdocs_faq_list_content_font_size_mkb_layout_2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']
        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_faq_list_content_font_size_mkb_layout_2', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_faq_section',
                'settings'    => 'betterdocs_faq_list_content_font_size_mkb_layout_2',
                'label'       => __( 'FAQ List Content Font Size', 'betterdocs-pro' ),
                'priority'    => 618,
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

    public function list_font_size_mkb_layout_2() {
        $this->customizer->add_setting( 'betterdocs_faq_list_font_size_mkb_layout_2', [
            'default'           => $this->defaults['betterdocs_faq_list_font_size_mkb_layout_2'],
            'capability'        => 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => [$this->sanitizer, 'integer']

        ] );

        $this->customizer->add_control( new RangeValueControl(
            $this->customizer, 'betterdocs_faq_list_font_size_mkb_layout_2', [
                'type'        => 'betterdocs-range-value',
                'section'     => 'betterdocs_faq_section',
                'settings'    => 'betterdocs_faq_list_font_size_mkb_layout_2',
                'label'       => __( 'FAQ List Font Size', 'betterdocs-pro' ),
                'priority'    => 618,
                'input_attrs' => [
                    'class'  => '',
                    'min'    => 0,
                    'max'    => 50,
                    'step'   => 1,
                    'suffix' => 'px' // optional suffix
                ]
            ] )
        );
    }

    public function list_padding_mkb_layout_2() {
        $this->customizer->add_setting( 'betterdocs_faq_list_padding_mkb_layout_2', [
            'default'    => $this->defaults['betterdocs_faq_list_padding_mkb_layout_2'],
            'transport'  => 'postMessage',
            'capability' => 'edit_theme_options'
        ] );

        $this->customizer->add_control(
            new MultiDimensionControl(
                $this->customizer,
                'betterdocs_faq_list_padding_mkb_layout_2',
                [
                    'label'        => __( 'FAQ List Padding (PX)', 'betterdocs-pro' ),
                    'section'      => 'betterdocs_faq_section',
                    'settings'     => 'betterdocs_faq_list_padding_mkb_layout_2',
                    'priority'     => 618,
                    'input_fields' => [
                        'input1' => __( 'top', 'betterdocs-pro' ),
                        'input2' => __( 'right', 'betterdocs-pro' ),
                        'input3' => __( 'bottom', 'betterdocs-pro' ),
                        'input4' => __( 'left', 'betterdocs-pro' )
                    ],
                    'defaults'     => [
                        'input1' => 20,
                        'input2' => 20,
                        'input3' => 20,
                        'input4' => 20
                    ]
                ]
            )
        );
    }

}
