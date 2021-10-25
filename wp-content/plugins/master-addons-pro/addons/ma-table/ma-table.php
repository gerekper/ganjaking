<?php

namespace MasterAddons\Addons;

// Elementor Classes
use \Elementor\Widget_Base;
use \Elementor\Utils;
use \Elementor\Controls_Manager;
use \Elementor\Repeater;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Typography;
use \Elementor\Scheme_Typography;
use \Elementor\Group_Control_Background;


use MasterAddons\Inc\Helper\Master_Addons_Helper;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 19/02/2020
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Master Addons Table
 */


class Dynamic_Table extends Widget_Base
{
    public function get_name()
    {
        return 'ma-table';
    }

    public function get_title()
    {
        return __('Dynamic Table', MELA_TD);
    }

    public function get_categories()
    {
        return ['master-addons'];
    }

    public function get_icon()
    {
        return 'ma-el-icon eicon-table';
    }

    public function get_style_depends()
    {
        return [
            'font-awesome-5-all',
            'font-awesome-4-shim'
        ];
    }

    public function get_keywords()
    {
        return ['table', 'tables', 'data tables', 'responsive table', 'pricing table', 'comparison table'];
    }


    protected function _register_controls()
    {

        /*
			 * MA Table: Header Section
			 */
        $this->start_controls_section(
            'ma_el_table_section',
            [
                'label' => __('Table Head', MELA_TD),
            ]
        );


        $repeater = new Repeater();
        $repeater->start_controls_tabs('ma_el_table_head_contents');
        $repeater->start_controls_tab('head_tab_contents', ['label' => __('Content', MELA_TD)]);

        $repeater->add_control(
            'title',
            [
                'type'          => Controls_Manager::TEXT,
                'label_block'   => true,
                'label'         => __('Column Name', MELA_TD),
                'default'       => __('Table Header', MELA_TD),
                'dynamic'       => [
                    'active' => true,
                ]
            ]
        );

        $repeater->add_control(
            'icon_type',
            [
                'label'        => __('Type', MELA_TD),
                'type'         => Controls_Manager::CHOOSE,
                'default'     => 'none',
                'options'               => [
                    'none'        => [
                        'title'   => esc_html__('None', MELA_TD),
                        'icon'    => 'fa fa-ban',
                    ],
                    'icon'        => [
                        'title'   => esc_html__('Icon', MELA_TD),
                        'icon'    => 'fa fa-star',
                    ],
                    'image'       => [
                        'title'   => esc_html__('Image', MELA_TD),
                        'icon'    => 'fa fa-picture-o',
                    ],
                ]
            ]
        );


        $repeater->add_control(
            'header_icon',
            [
                'label'             => esc_html__('Icon', MELA_TD),
                'description'       => esc_html__('Please choose an icon from the list.', MELA_TD),
                'type'              => Controls_Manager::ICONS,
                'fa4compatibility'  => 'icon',
                'default'           => [
                    'value'     => 'fab fa-elementor',
                    'library'   => 'brand',
                ],
                'render_type'      => 'template',
                'condition'      => [
                    'icon_type'  => 'icon'
                ],
            ]
        );

        $repeater->add_control(
            'header_image',
            [
                'label'         => __('Image', MELA_TD),
                'type'             => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition'        => [
                    'icon_type'    => 'image'
                ],
            ]
        );

        $repeater->add_control(
            'header_image_size',
            [
                'label'         => __('Image Size(px)', MELA_TD),
                'type' => Controls_Manager::NUMBER,
                'label_block' => false,
                'default' => '30',
                'condition'        => [
                    'icon_type'    => 'image'
                ],
            ]
        );
        $repeater->end_controls_tab();


        $repeater->start_controls_tab('ma_el_table_head_tab_options', ['label' => __('Options', MELA_TD)]);

        $repeater->add_control(
            'colspannumber',
            [
                'label'         => __('Column Span', MELA_TD),
                'type'          => Controls_Manager::TEXT
            ]
        );

        $repeater->add_control(
            'customwidth',
            [
                'label'         => __('Custom Width', MELA_TD),
                'type'          => Controls_Manager::SWITCHER,
                'label_off'     => __('No', MELA_TD),
                'label_on'      => __('Yes', MELA_TD),
            ]
        );

        $repeater->add_control(
            'width',
            [
                'label'         => __('Width', MELA_TD),
                'type'          => Controls_Manager::SLIDER,
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                ],
                'default' => [
                    'size' => 30,
                    'unit' => '%',
                ],
                'size_units'    => ['%', 'px'],
                'condition' => [
                    'customwidth'   => 'yes',
                ],
                'selectors'     => [
                    '{{WRAPPER}} .ma-el-table {{CURRENT_ITEM}}' => 'width: {{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $repeater->add_control(
            'align',
            [
                'label'         => __('Alignment', MELA_TD),
                'type'          => Controls_Manager::CHOOSE,
                'default'       => '',
                'options'       => [
                    'left'  => [
                        'title' => __('Left', MELA_TD),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', MELA_TD),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', MELA_TD),
                        'icon' => 'fa fa-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', MELA_TD),
                        'icon' => 'fa fa-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ma-el-table {{CURRENT_ITEM}}' => 'text-align: {{VALUE}};',
                ]
            ]
        );

        $repeater->add_control(
            'decoration',
            [
                'label'         => __('Decoration', MELA_TD),
                'type'          => Controls_Manager::SELECT,
                'options'       => [
                    ''                  => __('Default', MELA_TD),
                    'underline'         => __('Underline', MELA_TD),
                    'overline'          => __('Overline', MELA_TD),
                    'line-through'      => __('Line Through', MELA_TD),
                    'none'              => __('None', MELA_TD),
                ],
                'default'               => '',
                'selectors' => [
                    '{{WRAPPER}} .ma-el-table {{CURRENT_ITEM}}' => 'text-decoration: {{VALUE}};',
                ],
            ]
        );
        $repeater->end_controls_tab();

        $repeater->end_controls_tabs();



        $this->add_control(
            'ma_el_table_header',
            [
                'label'             => __('Table Header Cell', MELA_TD),
                'type'              => Controls_Manager::REPEATER,
                'seperator'         => 'before',
                'default'     => [
                    [
                        'title'                                 => __('First Name', MELA_TD),
                    ],
                    [
                        'title'                                 => __('Last Name', MELA_TD),
                    ],
                    [
                        'title'                                 => __('Job Title', MELA_TD),
                    ],
                    [
                        'title'                                 => __('Twitter', MELA_TD),
                    ]
                ],
                'fields' 	    => $repeater->get_controls(),
                'title_field'   => '{{{ title }}}'
            ]
        );
        $this->end_controls_section();



        /*
			 * MA Table Body Section
			 */
        $this->start_controls_section(
            'ma_el_table_body_section',
            [
                'label' => __('Table Body', MELA_TD),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();
        $repeater->add_control(
            'body_row',
            [
                'label'         => __('New Row?', MELA_TD),
                'type'          => Controls_Manager::SWITCHER,
                'label_off'     => __('No', MELA_TD),
                'label_on'      => __('Yes', MELA_TD),
            ]
        );

        $repeater->start_controls_tabs('ma_el_table_body_contents');

        $repeater->start_controls_tab('body_tab_contents', ['label' => __('Content', MELA_TD)]);

        $repeater->add_control(
            'text',
            [
                'type'          => Controls_Manager::TEXTAREA,
                'label_block'   => true,
                'label'         => __('Body Text', MELA_TD),
                'default'       => __('Table Body', MELA_TD),
                'dynamic'       => [
                    'active' => true,
                ]
            ]
        );

        $repeater->add_control(
            'icon_type',
            [
                'label'        => __('Type', MELA_TD),
                'type'         => Controls_Manager::CHOOSE,
                'default'     => 'none',
                'options'               => [
                    'none'        => [
                        'title'   => esc_html__('None', MELA_TD),
                        'icon'    => 'fa fa-ban',
                    ],
                    'icon'        => [
                        'title'   => esc_html__('Icon', MELA_TD),
                        'icon'    => 'fa fa-star',
                    ],
                    'image'       => [
                        'title'   => esc_html__('Image', MELA_TD),
                        'icon'    => 'fa fa-picture-o',
                    ],
                ],
            ]
        );


        $repeater->add_control(
            'body_icon',
            [
                'label'             => esc_html__('Icon', MELA_TD),
                'description'       => esc_html__('Please choose an icon from the list.', MELA_TD),
                'type'              => Controls_Manager::ICONS,
                'fa4compatibility'  => 'icon',
                'default'           => [
                    'value'     => 'fab fa-elementor',
                    'library'   => 'brand',
                ],
                'render_type'      => 'template',
                'condition'      => [
                    'icon_type'  => 'icon'
                ],
            ]
        );


        $repeater->add_control(
            'body_image',
            [
                'label'         => __('Image', MELA_TD),
                'type'             => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition'        => [
                    'icon_type'    => 'image'
                ],
            ]
        );

        $repeater->add_control(
            'body_image_size',
            [
                'label'         => __('Image Size(px)', MELA_TD),
                'type'          => Controls_Manager::NUMBER,
                'label_block'   => false,
                'default'       => '30',
                'condition'        => [
                    'icon_type'    => 'image'
                ],
            ]
        );

        $repeater->end_controls_tab();



        $repeater->start_controls_tab('ma_el_table_body_tab_options', ['label' => __('Options', MELA_TD)]);
        $repeater->add_control(
            'colspannumber',
            [
                'label'         => __('Column Span', MELA_TD),
                'type'          => Controls_Manager::TEXT
            ]
        );
        $repeater->add_control(
            'rowspannumber',
            [
                'label'         => __('Row Span', MELA_TD),
                'type'          => Controls_Manager::TEXT,
                'placeholder'   => __('', MELA_TD),
                'default'       => __('', MELA_TD),
            ]
        );

        $repeater->add_control(
            'customwidth',
            [
                'label'         => __('Custom Width', MELA_TD),
                'type'          => Controls_Manager::SWITCHER,
                'label_off'     => __('No', MELA_TD),
                'label_on'      => __('Yes', MELA_TD),
            ]
        );

        $repeater->add_control(
            'width',
            [
                'label'         => __('Width', MELA_TD),
                'type'          => Controls_Manager::SLIDER,
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                ],
                'default' => [
                    'size' => 30,
                    'unit' => '%',
                ],
                'size_units'    => ['%', 'px'],
                'condition' => [
                    'customwidth'   => 'yes',
                ],
                'selectors'     => [
                    '{{WRAPPER}} .ma-el-table {{CURRENT_ITEM}}' => 'width: {{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $repeater->add_control(
            'body_align',
            [
                'label'         => __('Alignment', MELA_TD),
                'type'          => Controls_Manager::CHOOSE,
                'default'       => '',
                'options'       => [
                    'left'  => [
                        'title' => __('Left', MELA_TD),
                        'icon'  => 'fa fa-align-left',
                    ],
                    'center'    => [
                        'title' => __('Center', MELA_TD),
                        'icon'  => 'fa fa-align-center',
                    ],
                    'right'     => [
                        'title' => __('Right', MELA_TD),
                        'icon'  => 'fa fa-align-right',
                    ],
                    'justify'   => [
                        'title' => __('Justified', MELA_TD),
                        'icon'  => 'fa fa-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ma-el-table {{CURRENT_ITEM}}' => 'text-align: {{VALUE}};',
                ]
            ]
        );


        $repeater->add_control(
            'decoration',
            [
                'label'         => __('Decoration', MELA_TD),
                'type'          => Controls_Manager::SELECT,
                'options'       => [
                    ''                  => __('Default', MELA_TD),
                    'underline'         => __('Underline', MELA_TD),
                    'overline'          => __('Overline', MELA_TD),
                    'line-through'      => __('Line Through', MELA_TD),
                    'none'              => __('None', MELA_TD),
                ],
                'default'               => '',
                'selectors' => [
                    '{{WRAPPER}} .ma-el-table {{CURRENT_ITEM}}' => 'text-decoration: {{VALUE}};'
                ],
            ]
        );

        $repeater->end_controls_tab();



        $repeater->end_controls_tabs();

        $this->add_control(
            'ma_el_table_body',
            [
                'label'             => __('Table Body Cell', MELA_TD),
                'type'              => Controls_Manager::REPEATER,
                'seperator'         => 'before',
                'default'     => [
                    [
                        'text'                                 => __('Liton', MELA_TD),
                    ],
                    [
                        'text'                                 => __('Arefin', MELA_TD),
                    ],
                    [
                        'text'                                 => __('Developer', MELA_TD),
                    ],
                    [
                        'text'                                 => __('Litonice11', MELA_TD),
                    ],
                    [
                        'body_row'                             => 'yes',
                        'text'                                 => __('Roy', MELA_TD),
                    ],
                    [
                        'text'                                 => __('Jemee', MELA_TD),
                    ],
                    [
                        'text'                                 => __('Content Writer', MELA_TD),
                    ],
                    [
                        'text'                                 => __('@Litonice11', MELA_TD),
                    ],

                    [
                        'body_row'                             => 'yes',
                        'text'                                 => __('Akbar', MELA_TD),
                    ],
                    [
                        'text'                                 => __('Hossain', MELA_TD),
                    ],
                    [
                        'text'                                 => __('Designer', MELA_TD),
                    ],
                    [
                        'text'                                 => __('@AkbarHo33850947', MELA_TD),
                    ],

                    [
                        'body_row'                             => 'yes',
                        'text'                                 => __('Jewel', MELA_TD),
                    ],
                    [
                        'text'                                 => __('Theme', MELA_TD),
                    ],
                    [
                        'text'                                 => __('Website', MELA_TD),
                    ],
                    [
                        'text'                                 => __('@jwthemeltd', MELA_TD),
                    ]
                ],
                'fields' 	    => $repeater->get_controls(),
                'title_field'   => '{{{ text }}}'
            ]
        );


        $this->end_controls_section();






        /**
         * Content Tab: Docs Links
         */
        $this->start_controls_section(
            'jltma_section_help_docs',
            [
                'label' => esc_html__('Help Docs', MELA_TD),
            ]
        );


        $this->add_control(
            'help_doc_1',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/demos/dynamic-table/" target="_blank" rel="noopener">', '</a>'),
                'content_classes' => 'jltma-editor-doc-links',
            ]
        );

        $this->add_control(
            'help_doc_2',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/dynamic-table-element/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
                'content_classes' => 'jltma-editor-doc-links',
            ]
        );

        $this->add_control(
            'help_doc_3',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=bn0TvaGf9l8" target="_blank" rel="noopener">', '</a>'),
                'content_classes' => 'jltma-editor-doc-links',
            ]
        );
        $this->end_controls_section();



        //Upgrade to Pro
       


        /*
            * MA Table: Style
            */

        $this->start_controls_section(
            'ma_el_table_style_section',
            [
                'label' => __('Global Style', MELA_TD),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );


        $this->add_control(
            'ma_el_table_body_striped_bg',
            [
                'label' => __('Striped Table?', MELA_TD),
                'type' => Controls_Manager::SWITCHER,
                'default'      => 'yes',
                'return_value' => 'yes'
            ]
        );
        $this->add_control(
            'ma_el_table_body_striped_bg_color',
            [
                'label' => __('Striped Background Color', MELA_TD),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'ma_el_table_body_striped_bg' => 'yes',
                ],
                'default'      => '#eee',
                'selectors' => [
                    '{{WRAPPER}} .ma-el-table tr:nth-of-type(odd)' => 'background: {{VALUE}};',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'ma_el_table_background',
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .ma-el-table'
            ]
        );

        $this->add_control(
            'ma_el_table_padding',
            [
                'label' => __('Inner Cell Padding', MELA_TD),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ma-el-table td,{{WRAPPER}} .ma-el-table th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ma_el_table_border',
                'label' => __('Border', MELA_TD),
                'selector' => '{{WRAPPER}} .ma-el-table td,{{WRAPPER}} .ma-el-table th',
            ]
        );
        $this->end_controls_section();


        /*
            * Table Header
            */

        $this->start_controls_section(
            'ma_el_table_header_style',
            [
                'label' => __('Table Header Style', MELA_TD),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'ma_el_table_header_bg_color',
            [
                'label' => __('Background Color', MELA_TD),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ma-el-table .ma-el-table-header th' => 'background-color: {{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'ma_el_table_header_text_color',
            [
                'label' => __('Text Color', MELA_TD),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ma-el-table .ma-el-table-header th' => 'color: {{VALUE}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'ma_el_table_header_align',
            [
                'label' => __('Alignment', MELA_TD),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', MELA_TD),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', MELA_TD),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', MELA_TD),
                        'icon' => 'fa fa-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', MELA_TD),
                        'icon' => 'fa fa-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ma-el-table .ma-el-table-header th' => 'text-align: {{VALUE}};',
                ],
            ]
        );


        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ma_el_table_header_typography',
                'selector' => '{{WRAPPER}} .ma-el-table .ma-el-table-header th',
                'scheme' => Scheme_Typography::TYPOGRAPHY_3,
            ]
        );

        $this->add_control(
            'ma_el_table_header_padding',
            [
                'label' => __('Inner Cell Padding', MELA_TD),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ma-el-table .ma-el-table-header td,{{WRAPPER}} .ma-el-table .ma-el-table-header th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();



        $this->start_controls_section(
            'ma_el_table_body_style',
            [
                'label' => __('Table Body', MELA_TD),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'ma_el_table_body_bg_color',
            [
                'label' => __('Background Color', MELA_TD),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ma-el-table .ma-el-table-body' => 'background-color: {{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'ma_el_table_body_text_color',
            [
                'label' => __('Text Color', MELA_TD),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ma-el-table .ma-el-table-body' => 'color: {{VALUE}};',
                ]
            ]
        );


        $this->add_control(
            'ma_el_table_body_icon_color',
            [
                'label' => __('Icon Color', MELA_TD),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ma-el-table .ma-el-table-body span i' => 'color: {{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'ma_el_table_body_icon_padding',
            [
                'label' => __('Icon Padding', MELA_TD),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ma-el-table .ma-el-table-body span i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ma_el_table_body_align',
            [
                'label' => __('Alignment', MELA_TD),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', MELA_TD),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', MELA_TD),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', MELA_TD),
                        'icon' => 'fa fa-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', MELA_TD),
                        'icon' => 'fa fa-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ma-el-table .ma-el-table-body tr, {{WRAPPER}} .ma-el-table .ma-el-table-body td' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ma_el_table_body_typography',
                'selector' => '{{WRAPPER}} .ma-el-table .ma-el-table-body tr, {{WRAPPER}} .ma-el-table .ma-el-table-body td',
                'scheme' => Scheme_Typography::TYPOGRAPHY_3,
            ]
        );

        $this->add_control(
            'ma_el_table_body_padding',
            [
                'label' => __('Inner Cell Padding', MELA_TD),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ma-el-table .ma-el-table-body tr,{{WRAPPER}} .ma-el-table .ma-el-table-body td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings       = $this->get_settings_for_display();

        $this->add_render_attribute(
            'ma_el_table_wrap',
            'class',
            [
                'ma-el-table',
                'table',
                ('yes' === $settings['ma_el_table_body_striped_bg']) ? "table-striped" : ""
            ]
        );
?>

        <table <?php echo $this->get_render_attribute_string('ma_el_table_wrap'); ?>>
            <thead class="ma-el-table-header">
                <tr>
                    <?php foreach ($settings['ma_el_table_header'] as $index => $thead) {
                        $repeater_setting_key   = $this->get_repeater_setting_key('title', 'ma_el_table_header', $index);
                        $icon_key                 = $this->get_repeater_setting_key('icon', 'ma_el_table_header', $index);
                        $this->add_inline_editing_attributes($repeater_setting_key);

                        $colspan = ($thead['colspannumber']) ? 'colSpan="' . $thead['colspannumber'] . '"' : '';
                    ?>

                        <th scope="jltma-row" class="elementor-inline-editing elementor-repeater-item-<?php echo $thead['_id']; ?>" <?php echo $colspan; ?> <?php echo $this->get_render_attribute_string($repeater_setting_key); ?>>

                            <?php if ('icon' === $thead['icon_type'] && (!empty($thead['header_icon']) || !empty($thead['header_icon']['value']))) { ?>
                                <span <?php echo $this->get_render_attribute_string($repeater_setting_key); ?>>
                                    <?php Master_Addons_Helper::jltma_fa_icon_picker('fab fa-elementor', 'icon', $thead['header_icon'], 'header_icon'); ?>
                                </span>

                            <?php } elseif ('image' === $thead['icon_type']) {

                                $this->add_render_attribute('ma_el_thead_img' . $index, [
                                    'src'    => esc_url($thead['header_image']['url']),
                                    'class'    => 'ma-el-thead-img',
                                    'style'    => "width:{$thead['header_image_size']}px;",
                                    'alt'    => esc_attr(get_post_meta($thead['header_image']['id'], '_wp_attachment_image_alt', true))
                                ]);
                            ?>
                                <img <?php echo $this->get_render_attribute_string('ma_el_thead_img' . $index); ?>>
                            <?php } ?>

                            <?php echo esc_html($thead['title']); ?>

                        </th>

                    <?php } ?>
                </tr>
            </thead>

            <tbody class="ma-el-table-body">
                <tr>
                    <?php
                    $th_values = $settings['ma_el_table_header'];
                    $counter = 0;
                    foreach ($settings['ma_el_table_body'] as $index => $tbody) {
                        $table_body_key = $this->get_repeater_setting_key('text', 'ma_el_table_body', $index);
                        $icon_key         = $this->get_repeater_setting_key('icon', 'ma_el_table_body', $index);

                        $this->add_render_attribute($table_body_key, 'class', 'elementor-repeater-item-' . $tbody['_id']);
                        $this->add_inline_editing_attributes($table_body_key);

                        if ($tbody['body_row'] == 'yes') {
                            echo '</tr><tr>';
                            $counter = 0;
                        }

                        $colspan = ($tbody['colspannumber']) ? 'colSpan="' . $tbody['colspannumber'] . '"' : '';
                        $rowspan = ($tbody['rowspannumber']) ? 'rowSpan="' . $tbody['rowspannumber'] . '"' : '';
                    ?>

                        <td data-column="<?php echo $th_values[$counter]['title']; ?>" <?php echo $colspan; ?> <?php echo $rowspan; ?> <?php echo $this->get_render_attribute_string($table_body_key); ?>>

                            <?php echo $tbody['text']; ?>

                            <?php if ('icon' === $tbody['icon_type'] && (!empty($tbody['body_icon']) || !empty($tbody['body_icon']['value']))) { ?>
                                <span <?php echo $this->get_render_attribute_string($table_body_key); ?>>
                                    <?php Master_Addons_Helper::jltma_fa_icon_picker('fab fa-elementor', 'icon', $tbody['body_icon'], 'body_icon'); ?>
                                </span>
                            <?php } elseif ('image' === $tbody['icon_type']) {

                                $this->add_render_attribute('ma_el_tbody_img' . $index, [
                                    'src'    => esc_url($tbody['body_image']['url']),
                                    'class'    => 'ma-el-tbody-img',
                                    'style'    => "width:{$tbody['body_image_size']}px;",
                                    'alt'    => esc_attr(get_post_meta($tbody['body_image']['id'], '_wp_attachment_image_alt', true))
                                ]);
                            ?>
                                <img <?php echo $this->get_render_attribute_string('ma_el_tbody_img' . $index); ?>>
                            <?php } ?>


                        </td>
                    <?php
                        $counter++;
                    }
                    ?>

                </tr>
            </tbody>
        </table>


<?php
    }
}
