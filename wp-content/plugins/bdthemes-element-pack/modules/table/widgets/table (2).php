<?php

namespace ElementPack\Modules\Table\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Control_Media;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Table extends Module_Base {

    const API_URL = 'https://sheets.googleapis.com/v4/spreadsheets/';

    public function get_name() {
        return 'bdt-table';
    }

    public function get_title() {
        return BDTEP . __('Table', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-table';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['table', 'row', 'column'];
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['datatables', 'datatables-uikit', 'ep-table'];
        }
    }

    public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['datatables-uikit', 'ep-scripts'];
        } else {
            return ['datatables-uikit', 'ep-table'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/dviKkEPsg04';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content_table',
            [
                'label' => __('Table', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'source',
            [
                'label'   => __('Type', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'custom',
                'options' => [
                    'custom'       => __('Custom HTML Table', 'bdthemes-element-pack'),
                    'static'       => __('Static Data', 'bdthemes-element-pack'),
                    'csv_file'     => __('CSV File', 'bdthemes-element-pack'),
                    'google_sheet' => __('Google Sheet', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->add_control(
            'content',
            [
                'label'       => __('Table HTML', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXTAREA,
                'default'     => '<table><thead><tr><th>Name</th><th>Age</th><th>Phone</th></tr></thead><tbody><tr><td>Tom</td><td>5</td><td>010281065</td></tr><tr><td>Jerry</td><td>4</td><td>012540515</td></tr><tr><td>Halum</td><td>12</td><td>011511441</td></tr></tbody></table>', 'bdthemes-element-pack',
                'placeholder' => __('Table Data', 'bdthemes-element-pack'),
                'rows'        => 10,
                'condition'   => [
                    'source' => 'custom',
                ],
                'dynamic'     => ['active' => true],
            ]
        );

        $this->add_control(
            'file',
            [
                'label'         => __('Enter a CSV File URL', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::URL,
                'show_external' => false,
                'label_block'   => true,
                'default'       => [
                    'url' => BDTEP_ASSETS_URL . 'others/table.csv',
                ],
                'condition'     => [
                    'source' => 'csv_file',
                ],
                'dynamic'       => ['active' => true],
            ]
        );

        $this->add_control(
            'delimiter',
            [
                'label'       => __('Delimiter', 'bdthemes-element-pack'),
                'description' => __('You can set your appropreate delimiter as you need. for example: Semicolon (;) Comma (,) etc.', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'default'     => ';',
                'condition'     => [
                    'source' => 'csv_file',
                ],
            ]
        );

        //            $this->add_control(
        //                'header_tag',
        //                [
        //                    'label'   => esc_html__('Table Head Tag', 'bdthemes-element-pack'),
        //                    'type'    => Controls_Manager::SWITCHER,
        //                    'default' => 'yes',
        //                    'condition'     => [
        //                        'source' => 'csv_file',
        //                    ],
        //                ]
        //            );

        $this->add_responsive_control(
            'header_align',
            [
                'label'     => __('Header Alignment', 'bdthemes-element-pack'),
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
                'default'   => 'center',
                'selectors' => [
                    '{{WRAPPER}} .bdt-table th' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'body_align',
            [
                'label'     => __('Body Alignment', 'bdthemes-element-pack'),
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
                'default'   => 'center',
                'selectors' => [
                    '{{WRAPPER}} .bdt-table table, {{WRAPPER}} .bdt-static-table .bdt-static-body-row-cell' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'use_data_table',
            [
                'label'   => esc_html__('Datatable', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'hide_header',
            [
                'label'   => esc_html__('Hide Header', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'prefix_class' => 'bdt-header-hidden-',
                'render_type'  => 'template',
            ]
        );

        $this->add_control(
            'table_responsive_control',
            [
                'label'     => __('Responsive', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'table_responsive_2',
                'options'   => [
                    'table_responsive_no' => esc_html__('No Responsive', 'bdthemes-element-pack'),
                    'table_responsive_1'  => esc_html__('Responsive 1', 'bdthemes-element-pack'),
                    'table_responsive_2'  => esc_html__('Responsive 2', 'bdthemes-element-pack'),
                    'horizontal_scroll'  => esc_html__('Horizontal Scroll', 'bdthemes-element-pack'),
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_google_sheet',
            [
                'label'     => __('Google Sheet Settings', 'bdthemes-element-pack') . BDTEP_NC,
                'tab'       => Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'source' => 'google_sheet',
                ],
            ]
        );

        $this->add_control(
            'google_sheet_range',
            [
                'label'       => __('Sheet Range', 'bdthemes-element-pack'),
                'description' => __('Example - A1:D7', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'google_sheet_id',
            [
                'label'       => __('Sheet ID', 'bdthemes-element-pack'),
                'description' => __('The spreadsheet ID serves as a unique identifier for a Google Sheet. The ID is the value between \'/d/\' and \'/edit\' in the URL of the Google Sheet.', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'google_sheet_cache',
            [
                'label'   => esc_html__('Cache Google Sheet', 'bdthemes-element-pack') . BDTEP_NC,
                'type'    => Controls_Manager::SWITCHER,
                'description' => esc_html__('Note:- Please use this cache option to reduce your request of API Calls.', 'bdthemes-element-pack'),
                // 'condition' => [
                // 	'source' => 'google_sheet'
                // ]
            ]
        );

        $this->add_control(
            'cache_refresh',
            array(
                'label'   => __('Reload Cache after ', 'bdthemes-element-pack') . BDTEP_NC,
                'type'    => Controls_Manager::SELECT,
                'default' => '1',
                'options' => array(
                    '15'  => __('15 Minutes', 'bdthemes-element-pack'),
                    '30'  => __('30 Minutes', 'bdthemes-element-pack'),
                    '1'  => __('1 Hour', 'bdthemes-element-pack'),
                    '3'  => __('3 Hour', 'bdthemes-element-pack'),
                    '6'  => __('6 Hour', 'bdthemes-element-pack'),
                    '12'  => __('12 Hour', 'bdthemes-element-pack'),
                    '24'  => __('24 Hour', 'bdthemes-element-pack'),
                ),
                'condition' => [
                    'google_sheet_cache' => 'yes'
                ]
            )
        );

        $this->end_controls_section();

        // Static Controls Start
        $this->start_controls_section(
            'section_static_table_column',
            [
                'label'     => __('Table Head', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'source' => 'static',
                    'hide_header' => ''
                ],
            ]
        );

        $repeater = new Repeater();

        $repeater->start_controls_tabs('tabs_static_column');

        $repeater->start_controls_tab(
            'tab_static_column_content',
            [
                'label' => __('Content', 'bdthemes-element-pack'),
            ]
        );

        $repeater->add_control(
            'static_column_name',
            [
                'label'       => __('Title', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' => __('Column Name', 'bdthemes-element-pack'),
                'default'     => __('Column One', 'bdthemes-element-pack'),
                'dynamic'     => [
                    'active' => true,
                ]
            ]
        );

        $repeater->add_control(
            'static_column_span',
            [
                'label' => __('Col Span', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::NUMBER,
                'min'   => 0,
                'max'   => 50,
                'step'  => 1
            ]
        );

        $repeater->add_responsive_control(
            'static_column_media',
            [
                'label'       => __('Media', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::CHOOSE,
                'label_block' => false,
                'toggle'      => false,
                'default'     => 'none',
                'options'     => [
                    'none'  => [
                        'title' => __('None', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-editor-close',
                    ],
                    'icon'  => [
                        'title' => __('Icon', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-info-circle',
                    ],
                    'image' => [
                        'title' => __('Image', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-image-bold',
                    ],
                ]
            ]
        );

        $repeater->add_control(
            'static_column_icons',
            [
                'label'            => __('Icon', 'bdthemes-element-pack'),
                'type'             => Controls_Manager::ICONS,
                'fa4compatibility' => 'column_icon',
                'label_block'      => true,
                'condition'        => [
                    'static_column_media' => 'icon'
                ],
            ]
        );

        $repeater->add_control(
            'static_column_image',
            [
                'label'     => __('Image', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::MEDIA,
                'default'   => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'dynamic'   => [
                    'active' => true,
                ],
                'condition' => [
                    'static_column_media' => 'image'
                ]
            ]
        );

        $repeater->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'      => 'static_column_thumbnail',
                'default'   => 'thumbnail',
                'separator' => 'none',
                'condition' => [
                    'static_column_media' => 'image'
                ]
            ]
        );

        $repeater->end_controls_tab();

        $repeater->start_controls_tab(
            'tabs_static_column_style',
            [
                'label' => __('Style', 'bdthemes-element-pack'),
            ]
        );

        $repeater->add_control(
            'header_custom_text_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.bdt-static-column-cell' => 'color: {{VALUE}}',
                ],
            ]
        );

        $repeater->add_control(
            'header_custom_bg_color',
            [
                'label'     => __('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.bdt-static-column-cell' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $repeater->add_control(
            'header_custom_border_color',
            [
                'label'     => __('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.bdt-static-column-cell' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $repeater->add_control(
            'header_custom_icon_color',
            [
                'label'     => __('Icon Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'static_column_media' => 'icon'
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .bdt-static-column-cell-icon i' => 'color: {{VALUE}}',
                ],
            ]
        );

        $repeater->add_responsive_control(
            'header_custom_column_width',
            [
                'label' => __('Column Width', 'bdthemes-element-pack') . BDTEP_NC,
                'type'  => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%', 'vw'],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.bdt-static-column-cell' => 'width: {{SIZE}}{{UNIT}} !important;',
                ],
                'separator' => 'before'
            ]
        );

        $repeater->end_controls_tab();

        $repeater->end_controls_tabs();

        $this->add_control(
            'static_columns_data',
            [
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => [
                    [
                        'static_column_name' => __('Name', 'bdthemes-element-pack')
                    ],
                    [
                        'static_column_name' => __('Age', 'bdthemes-element-pack')
                    ],
                    [
                        'static_column_name' => __('Phone', 'bdthemes-element-pack')
                    ],
                ],
                'title_field' => '{{{ static_column_name }}}'
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_table_static_row',
            [
                'label'     => __('Table Body', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'source' => 'static',
                ],
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'static_row_column_type',
            [
                'label'   => __('Row/Column', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'row',
                'options' => [
                    'row'    => __('Row', 'bdthemes-element-pack'),
                    'column' => __('Column', 'bdthemes-element-pack'),
                ],
            ]
        );

        $repeater->start_controls_tabs('tabs_static_row');

        $repeater->start_controls_tab(
            'tab_static_row_content',
            [
                'label'     => __('Content', 'bdthemes-element-pack'),
                'condition' => [
                    'static_row_column_type' => 'column'
                ],
            ]
        );

        $repeater->add_control(
            'static_cell_name',
            [
                'label'       => __('Title', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' => __('Cell Name', 'bdthemes-element-pack'),
                'dynamic'     => [
                    'active' => true,
                ],
                'condition'   => [
                    'static_row_column_type' => 'column'
                ],
            ]
        );

        $repeater->add_control(
            'static_row_column_span',
            [
                'label'     => __('Col Span', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::NUMBER,
                'min'       => 0,
                'max'       => 50,
                'step'      => 1,
                'condition' => [
                    'static_row_column_type' => 'column'
                ],
            ]
        );

        $repeater->add_control(
            'static_row_span',
            [
                'label'     => __('Row Span', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::NUMBER,
                'min'       => 0,
                'max'       => 50,
                'step'      => 1,
                'condition' => [
                    'static_row_column_type' => 'column'
                ],
            ]
        );

        $repeater->add_control(
            'static_row_media',
            [
                'label'       => __('Media', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::CHOOSE,
                'label_block' => false,
                'toggle'      => false,
                'default'     => 'none',
                'condition'   => [
                    'static_row_column_type' => 'column'
                ],
                'options'     => [
                    'icon'  => [
                        'title' => __('Icon', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-info-circle',
                    ],
                    'image' => [
                        'title' => __('Image', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-image-bold',
                    ],
                    'none'  => [
                        'title' => __('None', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-editor-close',
                    ],
                ]
            ]
        );

        $repeater->add_control(
            'static_row_icons',
            [
                'label'       => __('Icon', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::ICONS,
                'label_block' => true,
                'condition'   => [
                    'static_row_media'       => 'icon',
                    'static_row_column_type' => 'column'
                ],
            ]
        );

        $repeater->add_control(
            'static_row_image',
            [
                'label'     => __('Image', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::MEDIA,
                'default'   => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'static_row_media'       => 'image',
                    'static_row_column_type' => 'column'
                ],
                'dynamic'   => [
                    'active' => true,
                ]
            ]
        );

        $repeater->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'      => 'static_row_thumbnail',
                'default'   => 'thumbnail',
                'separator' => 'none',
                'exclude'   => ['custom'],
                'condition' => [
                    'static_row_media'       => 'image',
                    'static_row_column_type' => 'column'
                ],
            ]
        );

        $repeater->end_controls_tab();

        $repeater->start_controls_tab(
            'tabs_static_row_style',
            [
                'label'     => __('Style', 'bdthemes-element-pack'),
                'condition' => [
                    'static_row_column_type' => 'column'
                ],
            ]
        );

        $repeater->add_control(
            'static_row_custom_background_color',
            [
                'label'     => __('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'static_row_column_type' => 'column'
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.bdt-static-body-row-cell' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $repeater->add_control(
            'static_row_custom_text_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'static_row_column_type' => 'column'
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .bdt-static-body-row-cell-text' => 'color: {{VALUE}}',
                ],
            ]
        );

        $repeater->add_control(
            'static_row_custom_icon_color',
            [
                'label'     => __('Icon Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'static_row_column_type' => 'column',
                    'static_row_media'       => 'icon'
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .bdt-static-body-row-cell-icon i' => 'color: {{VALUE}}',
                ],
            ]
        );

        $repeater->add_responsive_control(
            'static_row_custom_icon_size',
            [
                'label'     => __('Icon/Image Size', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'condition' => [
                    'static_row_column_type' => 'column'
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .bdt-static-body-row-cell-icon i'   => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} {{CURRENT_ITEM}} .bdt-static-body-row-cell-icon img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} {{CURRENT_ITEM}} .bdt-static-body-row-cell-icon svg' => 'width: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $repeater->end_controls_tab();

        $repeater->end_controls_tabs();

        $this->add_control(
            'static_row_starts',
            [
                'label'     => false,
                'type'      => Controls_Manager::HIDDEN,
                'default'   => __('Row Starts', 'bdthemes-element-pack'),
                'condition' => [
                    'static_row_column_type' => 'row'
                ],
            ]
        );

        $this->add_control(
            'static_rows_data',
            [
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => [
                    [
                        'static_row_column_type' => 'row',
                        'static_row_starts'      => __('Row Starts', 'bdthemes-element-pack'),
                    ],
                    [
                        'static_row_column_type' => 'column',
                        'static_cell_name'       => __('Tom', 'bdthemes-element-pack')
                    ],
                    [
                        'static_row_column_type' => 'column',
                        'static_cell_name'       => '5'
                    ],
                    [
                        'static_row_column_type' => 'column',
                        'static_cell_name'       => '012540515'
                    ],
                    [
                        'static_row_column_type' => 'row',
                        'static_row_starts'      => __('Row Starts', 'bdthemes-element-pack'),
                    ],
                    [
                        'static_row_column_type' => 'column',
                        'static_cell_name'       => __('Jerry', 'bdthemes-element-pack')
                    ],
                    [
                        'static_row_column_type' => 'column',
                        'static_cell_name'       => '4'
                    ],
                    [
                        'static_row_column_type' => 'column',
                        'static_cell_name'       => '010281065'
                    ],
                    [
                        'static_row_column_type' => 'row',
                        'static_row_starts'      => __('Row Starts', 'bdthemes-element-pack'),
                    ],
                    [
                        'static_row_column_type' => 'column',
                        'static_cell_name'       => __('Halum', 'bdthemes-element-pack')
                    ],
                    [
                        'static_row_column_type' => 'column',
                        'static_cell_name'       => '12'
                    ],
                    [
                        'static_row_column_type' => 'column',
                        'static_cell_name'       => '011511441'
                    ],
                ],
                'title_field' => '<# print( (static_row_column_type == "column" ) ? static_cell_name : ("Row Starts") ) #>',
            ]
        );

        $this->end_controls_section();
        // Static Controls End

        $this->start_controls_section(
            'section_style_data_table',
            [
                'label'     => __('Data Table Settings', 'bdthemes-element-pack'),
                'condition' => [
                    'use_data_table' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_searching',
            [
                'label'   => esc_html__('Search', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_ordering',
            [
                'label'   => esc_html__('Ordering', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label'   => esc_html__('Pagination', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_info',
            [
                'label'   => esc_html__('Info', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        //Style
        $this->start_controls_section(
            'section_style_table',
            [
                'label' => __('Table', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'stripe_style',
            [
                'label' => __('Stripe Style', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'remove_vertical_border',
            [
                'label' => __('Remove Vertical Border', 'bdthemes-element-pack') . BDTEP_NC,
                'type'  => Controls_Manager::SWITCHER,
                'prefix_class' => 'vertical-border-remove-'
            ]
        );

        $this->add_control(
            'table_border_style',
            [
                'label'     => __('Border Style', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'solid',
                'options'   => [
                    'none'   => __('None', 'bdthemes-element-pack'),
                    'solid'  => __('Solid', 'bdthemes-element-pack'),
                    'double' => __('Double', 'bdthemes-element-pack'),
                    'dotted' => __('Dotted', 'bdthemes-element-pack'),
                    'dashed' => __('Dashed', 'bdthemes-element-pack'),
                    'groove' => __('Groove', 'bdthemes-element-pack'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-table table' => 'border-style: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'table_border_width',
            [
                'label'     => __('Border Width', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 1,
                ],
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 4,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-table table' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'table_border_color',
            [
                'label'     => __('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ccc',
                'selectors' => [
                    '{{WRAPPER}} .bdt-table table' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'leading_column_show',
            [
                'label' => __('Leading Column Style', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_header',
            [
                'label' => __('Header', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'hide_header' => ''
                ]
            ]
        );

        $this->add_control(
            'header_background',
            [
                'label'     => __('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#e7ebef',
                'selectors' => [
                    '{{WRAPPER}} .bdt-table th' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'header_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#333',
                'selectors' => [
                    '{{WRAPPER}} .bdt-table th' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'header_border_style',
            [
                'label'     => __('Border Style', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'solid',
                'options'   => [
                    'none'   => __('None', 'bdthemes-element-pack'),
                    'solid'  => __('Solid', 'bdthemes-element-pack'),
                    'double' => __('Double', 'bdthemes-element-pack'),
                    'dotted' => __('Dotted', 'bdthemes-element-pack'),
                    'dashed' => __('Dashed', 'bdthemes-element-pack'),
                    'groove' => __('Groove', 'bdthemes-element-pack'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-table th' => 'border-style: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'header_border_width',
            [
                'label'     => __('Border Width', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 1,
                ],
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 20,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-table th' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'header_border_style!' => 'none'
                ]
            ]
        );

        $this->add_control(
            'header_border_color',
            [
                'label'     => __('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ccc',
                'selectors' => [
                    '{{WRAPPER}} .bdt-table th' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'header_border_style!' => 'none'
                ]
            ]
        );

        $this->add_responsive_control(
            'header_border_radius',
            [
                'label'      => __('Radius', 'bdthemes-element-pack') . BDTEP_NC,
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-table th:first-child' => 'border-radius: {{TOP}}{{UNIT}} 0 0 {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-table th:last-child' => 'border-radius: 0 {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} 0;',
                ],
                'description'     => __('Radius option will not work Border, it just work inner. If you set background color you can see change raidus.', 'bdthemes-element-pack'),
            ]
        );

        $this->add_responsive_control(
            'header_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'default'    => [
                    'top'    => 1,
                    'bottom' => 1,
                    'left'   => 1,
                    'right'  => 2,
                    'unit'   => 'em'
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-table th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'static_header_image_icon_spacing',
            [
                'label'     => __('Icon/Image Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}}.elementor-widget-bdt-table .bdt-static-table .bdt-static-column-cell-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'source' => 'static',
                ],
            ]
        );

        $this->add_responsive_control(
            'static_header_image_size',
            [
                'label'     => __('Image Size', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}}.elementor-widget-bdt-table .bdt-static-table .bdt-static-column-cell-icon img' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'source' => 'static',
                ],
            ]
        );

        $this->add_responsive_control(
            'static_header_image_radius',
            [
                'label'      => esc_html__('Image Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}}.elementor-widget-bdt-table .bdt-static-table .bdt-static-column-cell-icon img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition'  => [
                    'source' => 'static',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'header_text_typography',
                'selector' => '{{WRAPPER}} .bdt-table th',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_body',
            [
                'label' => __('Body', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_body_style');

        $this->start_controls_tab(
            'tab_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'normal_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-table td' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'normal_background',
            [
                'label'     => __('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .bdt-table td' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'cell_border_style',
            [
                'label'     => __('Border Style', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'solid',
                'options'   => [
                    'none'   => __('None', 'bdthemes-element-pack'),
                    'solid'  => __('Solid', 'bdthemes-element-pack'),
                    'double' => __('Double', 'bdthemes-element-pack'),
                    'dotted' => __('Dotted', 'bdthemes-element-pack'),
                    'dashed' => __('Dashed', 'bdthemes-element-pack'),
                    'groove' => __('Groove', 'bdthemes-element-pack'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-table td' => 'border-style: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'cell_border_width',
            [
                'label'     => __('Border Width', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 1,
                ],
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 20,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-table td' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'cell_border_style!' => 'none'
                ]
            ]
        );

        $this->add_control(
            'normal_border_color',
            [
                'label'     => __('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ccc',
                'selectors' => [
                    '{{WRAPPER}} .bdt-table td' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'cell_border_style!' => 'none'
                ]
            ]
        );

        $this->add_responsive_control(
            'cell_border_radius',
            [
                'label'      => __('Radius', 'bdthemes-element-pack') . BDTEP_NC,
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-table td:first-child' => 'border-radius: {{TOP}}{{UNIT}} 0 0 {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-table td:last-child' => 'border-radius: 0 {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} 0;',
                ],
                'description'     => __('Radius option will not work Border, it just work inner. If you set background color you can see change raidus.', 'bdthemes-element-pack'),
            ]
        );

        $this->add_responsive_control(
            'cell_padding',
            [
                'label'      => __('Cell Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'default'    => [
                    'top'    => 0.5,
                    'bottom' => 0.5,
                    'left'   => 1,
                    'right'  => 1,
                    'unit'   => 'em'
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-table td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'body_text_typography',
                'selector' => '{{WRAPPER}} .bdt-table td',
            ]
        );

        $this->add_control(
            'static_body_image_icon_heading',
            [
                'label'     => __('Icon/Image', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'condition' => [
                    'source' => 'static',
                ],
                'separator' => 'before'
            ]
        );

        $this->add_responsive_control(
            'static_body_image_icon_spacing',
            [
                'label'     => __('Icon/Image Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}}.elementor-widget-bdt-table .bdt-static-table .bdt-static-body-row-cell-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'source' => 'static',
                ],
            ]
        );

        $this->add_responsive_control(
            'static_body_image_size',
            [
                'label'     => __('Image Size', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}}.elementor-widget-bdt-table .bdt-static-table .bdt-static-body-row-cell-icon img' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'source' => 'static',
                ],
            ]
        );

        $this->add_responsive_control(
            'static_body_image_radius',
            [
                'label'      => esc_html__('Image Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}}.elementor-widget-bdt-table .bdt-static-table .bdt-static-body-row-cell-icon img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition'  => [
                    'source' => 'static',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'row_hover_background',
            [
                'label'     => __('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}.elementor-widget-bdt-table .bdt-table table tr:hover td' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'row_hover_text_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}.elementor-widget-bdt-table .bdt-table table tr:hover td' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_stripe',
            [
                'label'     => __('Stripe', 'bdthemes-element-pack'),
                'condition' => [
                    'stripe_style' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'stripe_background',
            [
                'label'     => __('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#f5f5f5',
                'selectors' => [
                    '{{WRAPPER}} .bdt-table tr:nth-child(even) td' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'stripe_style' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'stripe_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-table tr:nth-child(even) td' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'stripe_style' => 'yes',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();


        $this->start_controls_section(
            'section_style_leading_column',
            [
                'label'     => __('Leading Column', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'leading_column_show' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'leading_column_border_style',
            [
                'label'     => __('Border Style', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'solid',
                'options'   => [
                    'none'   => __('None', 'bdthemes-element-pack'),
                    'solid'  => __('Solid', 'bdthemes-element-pack'),
                    'double' => __('Double', 'bdthemes-element-pack'),
                    'dotted' => __('Dotted', 'bdthemes-element-pack'),
                    'dashed' => __('Dashed', 'bdthemes-element-pack'),
                    'groove' => __('Groove', 'bdthemes-element-pack'),
                ],
                'selectors' => [
                    '{{WRAPPER}}.elementor-widget-bdt-table .bdt-table tr td:nth-child(1)' => 'border-style: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'leading_column_border_width',
            [
                'label'     => __('Border Width', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 1,
                ],
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 20,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}.elementor-widget-bdt-table .bdt-table tr td:nth-child(1)' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'leading_column_padding',
            [
                'label'      => __('Cell Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'default'    => [
                    'top'    => 0.5,
                    'bottom' => 0.5,
                    'left'   => 1,
                    'right'  => 1,
                    'unit'   => 'em'
                ],
                'selectors'  => [
                    '{{WRAPPER}}.elementor-widget-bdt-table .bdt-table tr td:nth-child(1)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'leading_column_text_typography',
                'selector' => '{{WRAPPER}}.elementor-widget-bdt-table .bdt-table tr td:nth-child(1)',
            ]
        );

        $this->start_controls_tabs('tabs_leading_column_normal_style');

        $this->start_controls_tab(
            'leading_column_tab_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'leading_column_normal_background',
            [
                'label'     => __('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#fff',
                'selectors' => [
                    '{{WRAPPER}}.elementor-widget-bdt-table .bdt-table tr td:nth-child(1)' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'leading_column_normal_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}.elementor-widget-bdt-table .bdt-table tr td:nth-child(1)' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'leading_column_normal_border_color',
            [
                'label'     => __('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ccc',
                'selectors' => [
                    '{{WRAPPER}}.elementor-widget-bdt-table .bdt-table tr td:nth-child(1)' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'leading_column_tab_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'leading_column_row_hover_background',
            [
                'label'     => __('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}.elementor-widget-bdt-table .bdt-table table tr:hover > td:nth-child(1)' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'leading_column_row_hover_text_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}.elementor-widget-bdt-table .bdt-table table tr:hover > td:nth-child(1)' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        // $this->start_controls_tab(
        //     'leading_column_tab_stripe',
        //     [
        //         'label'     => __('Stripe', 'bdthemes-element-pack'),
        //         'condition' => [
        //             'stripe_style' => 'yes',
        //         ],
        //     ]
        // );

        // $this->add_control(
        //     'leading_column_stripe_background',
        //     [
        //         'label'     => __('Background', 'bdthemes-element-pack'),
        //         'type'      => Controls_Manager::COLOR,
        //         'default'   => '#f5f5f5',
        //         'selectors' => [
        //             '{{WRAPPER}}.elementor-widget-bdt-table .bdt-table tr:nth-child(even) td' => 'background-color: {{VALUE}};',
        //         ],
        //         'condition' => [
        //             'stripe_style' => 'yes',
        //         ],
        //     ]
        // );

        // $this->add_control(
        //     'leading_column_stripe_color',
        //     [
        //         'label'     => __('Text Color', 'bdthemes-element-pack'),
        //         'type'      => Controls_Manager::COLOR,
        //         'selectors' => [
        //             '{{WRAPPER}}.elementor-widget-bdt-table .bdt-table tr:nth-child(even) td' => 'color: {{VALUE}};',
        //         ],
        //         'condition' => [
        //             'stripe_style' => 'yes',
        //         ],
        //     ]
        // );

        // $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();


        $this->start_controls_section(
            'section_filter_style',
            [
                'label' => esc_html__('Filter', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('filter_style');

        $this->start_controls_tab(
            'filter_header_style',
            [
                'label' => __('Header', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'datatable_header_text_color',
            [
                'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-table .dataTables_length label, {{WRAPPER}} .bdt-table .dataTables_filter label' => 'color: {{VALUE}};',
                ],
                'separator' => 'after',
            ]
        );


        $this->add_control(
            'datatable_header_input_color',
            [
                'label'     => esc_html__('Input Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-table .dataTables_filter input, {{WRAPPER}} .bdt-table .dataTables_length select' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'datatable_header_input_background',
            [
                'label'     => esc_html__('Input Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-table .dataTables_filter input, {{WRAPPER}} .bdt-table .dataTables_length select' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'datatable_header_input_padding',
            [
                'label'      => esc_html__('Input Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-table .dataTables_filter input, {{WRAPPER}} .bdt-table .dataTables_length select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'datatable_header_input_border',
                'label'       => esc_html__('Input Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-table .dataTables_filter input, {{WRAPPER}} .bdt-table .dataTables_length select',
            ]
        );

        $this->add_responsive_control(
            'datatable_header_input_radius',
            [
                'label'      => esc_html__('Input Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-table .dataTables_filter input, {{WRAPPER}} .bdt-table .dataTables_length select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'datatable_header_input_box_shadow',
                'selector' => '{{WRAPPER}} .bdt-table .dataTables_filter input, {{WRAPPER}} .bdt-table .dataTables_length select',
            ]
        );

        $this->add_control(
            'datatable_header_space',
            [
                'label'     => __('Space', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 1,
                ],
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 40,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-table .dataTables_filter' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'filter_footer_style',
            [
                'label' => __('Footer', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'datatable_footer_text_color',
            [
                'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-table .dataTables_info, {{WRAPPER}} .bdt-table .dataTables_paginate' => 'color: {{VALUE}};',
                ],
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'datatable_footer_pagination_color',
            [
                'label'     => esc_html__('Pagination Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-table .dataTables_paginate a' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'datatable_footer_pagination_active_color',
            [
                'label'     => esc_html__('Pagination Active Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-table .dataTables_paginate a.current' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'datatable_footer_space',
            [
                'label'     => __('Space', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 1,
                ],
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 40,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-table table' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }


    public function csv_to_table() {

        $settings = $this->get_settings_for_display();


        if (filter_var($settings['file']['url'], FILTER_VALIDATE_URL) === false) {
            return element_pack_alert(esc_html__('invalid URL', 'bdthemes-element-pack'));
        }

        $response = wp_remote_get($settings['file']['url']);

        if (200 !== wp_remote_retrieve_response_code($response)) {
            return element_pack_alert(esc_html__('invalid URL', 'bdthemes-element-pack'));
        }

        $csv = wp_remote_retrieve_body($response);

        $output = element_pack_parse_csv($csv, $settings['delimiter']);

        return $output;
    }

    public function static_table() {

        $settings = $this->get_settings_for_display();

        $static_table_row = [];
        $static_table_cell = [];
        foreach ($settings['static_rows_data'] as $row) {
            $row_id = uniqid();

            if ($row['static_row_column_type'] == 'row') {
                $static_table_row[] = [
                    'id'   => $row_id,
                    'type' => $row['static_row_column_type'],
                ];
            }

            if ($row['static_row_column_type'] == 'column') {
                $static_table_row_keys = array_keys($static_table_row);
                $cell_key = end($static_table_row_keys);

                $static_table_cell[] = [
                    'repeater_id'               => $row['_id'],
                    'row_id'                    => $static_table_row[$cell_key]['id'],
                    'title'                     => $row['static_cell_name'],
                    'static_row_span'           => $row['static_row_span'],
                    'static_row_column_span'    => $row['static_row_column_span'],
                    'static_row_icons'          => !empty($row['static_row_icons']['value']) ? $row['static_row_icons'] : '',
                    'static_row_icon_show'      => !empty($row['static_row_icon_show']) ? $row['static_row_icon_show'] : '',
                    'static_row_image'          => array_key_exists('static_row_image', $row) ? $row['static_row_image'] : '',
                    'static_row_thumbnail_size' => !empty($row['static_row_thumbnail_size']) ? $row['static_row_thumbnail_size'] : '',
                ];
            }
        }
?>

        <table class="bdt-static-table">

            <?php if ('' == $settings['hide_header']) : ?>
                <thead>
                    <tr>
                        <?php foreach ($settings['static_columns_data'] as $index => $column_cell) :

                            $column_repeater_key = $this->get_repeater_setting_key('static_column_span', 'static_columns_data', $index);
                            $this->add_render_attribute($column_repeater_key, 'class', 'bdt-static-column-cell');
                            $this->add_render_attribute($column_repeater_key, 'class', 'elementor-repeater-item-' . $column_cell['_id']);

                            if ($column_cell['static_column_span']) {
                                $this->add_render_attribute($column_repeater_key, 'colspan', $column_cell['static_column_span']);
                            }
                        ?>

                            <th <?php echo $this->get_render_attribute_string($column_repeater_key); ?>>
                                <div class="bdt-static-column-cell-wrap">

                                    <div class="bdt-static-column-cell-text"><?php echo $column_cell['static_column_name']; ?></div>

                                    <?php if ($column_cell['static_column_media'] == 'icon' && !empty($column_cell['static_column_icons'])) : ?>
                                        <div class="bdt-static-column-cell-icon">
                                            <?php Icons_Manager::render_icon($column_cell['static_column_icons']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (isset($column_cell['static_column_image']['url']) || isset($column_cell['static_column_image']['id'])) :

                                        $this->add_render_attribute('static_column_image', 'src', $column_cell['static_column_image']['url']);

                                        $this->add_render_attribute('static_column_image', 'alt', Control_Media::get_image_alt($column_cell['static_column_image']));

                                        $this->add_render_attribute('static_column_image', 'title', Control_Media::get_image_title($column_cell['static_column_image']));

                                    ?>
                                        <div class="bdt-static-column-cell-icon">
                                            <?php echo Group_Control_Image_Size::get_attachment_image_html($column_cell, 'static_column_thumbnail', 'static_column_image'); ?>
                                        </div>
                                    <?php endif; ?>

                                </div>
                            </th>

                        <?php endforeach; ?>
                    </tr>
                </thead>
            <?php endif; ?>

            <tbody>
                <?php for ($i = 0; $i < count($static_table_row); $i++) : ?>
                    <tr>
                        <?php
                        for ($j = 0; $j < count($static_table_cell); $j++) :

                            if ($static_table_row[$i]['id'] == $static_table_cell[$j]['row_id']) :

                                $row_span_repeater_key = $this->get_repeater_setting_key('static_row_span', 'static_rows_data', $static_table_cell[$j]['row_id'] . $i . $j);

                                $this->add_render_attribute($row_span_repeater_key, 'class', 'bdt-static-body-row-cell');
                                $this->add_render_attribute($row_span_repeater_key, 'class', 'elementor-repeater-item-' . $static_table_cell[$j]['repeater_id']);

                                if (!empty($static_table_cell[$j]['static_row_column_span'])) {
                                    $this->add_render_attribute($row_span_repeater_key, 'colspan', $static_table_cell[$j]['static_row_column_span']);
                                }

                                if (!empty($static_table_cell[$j]['static_row_span'])) {
                                    $this->add_render_attribute($row_span_repeater_key, 'rowspan', $static_table_cell[$j]['static_row_span']);
                                }
                        ?>
                                <td <?php echo $this->get_render_attribute_string($row_span_repeater_key); ?>>

                                    <div class="bdt-static-body-row-cell-wrap">
                                        <div class="bdt-static-body-row-cell-text"><?php echo $static_table_cell[$j]['title']; ?></div>

                                        <?php if (!empty($static_table_cell[$j]['static_row_icons'])) : ?>
                                            <div class="bdt-static-body-row-cell-icon">
                                                <?php Icons_Manager::render_icon($static_table_cell[$j]['static_row_icons']); ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php
                                        if (isset($static_table_cell[$j]['static_row_image']['url']) || isset($static_table_cell[$j]['static_row_image']['id'])) :

                                        ?>
                                            <div class="bdt-static-body-row-cell-icon">

                                                <?php
                                                $thumb_url  = wp_get_attachment_image_src($static_table_cell[$j]['static_row_image']['id'], $static_table_cell[$j]['static_row_thumbnail_size'], true);
                                                if (!$thumb_url) {
                                                    printf('<img src="%1$s" alt="%2$s">', $static_table_cell[$j]['static_row_image']['url'], esc_html($static_table_cell[$j]['title']));
                                                } else {
                                                    print(wp_get_attachment_image(
                                                        $static_table_cell[$j]['static_row_image']['id'],
                                                        $static_table_cell[$j]['static_row_thumbnail_size'],
                                                        false,
                                                        [
                                                            'alt' => esc_html($static_table_cell[$j]['title'])
                                                        ]
                                                    ));
                                                }
                                                ?>


                                            </div>
                                        <?php endif; ?>
                                    </div>

                                </td>
                        <?php
                            endif;
                        endfor;
                        ?>
                    </tr>
                <?php endfor; ?>
            </tbody>

        </table>

    <?php
    }

    public function get_transient_expire($settings) {
        $expire_value = $settings['cache_refresh'];
        $expire_time  = 1 * HOUR_IN_SECONDS;

        if ('1' === $expire_value) {
            $expire_time = 1 * HOUR_IN_SECONDS;
        } elseif ('3' === $expire_value) {
            $expire_time = 3 * HOUR_IN_SECONDS;
        } elseif ('6' === $expire_value) {
            $expire_time = 6 * HOUR_IN_SECONDS;
        } elseif ('12' === $expire_value) {
            $expire_time = 12 * HOUR_IN_SECONDS;
        } elseif ('24' === $expire_value) {
            $expire_time = 24 * HOUR_IN_SECONDS;
        } elseif ('15' === $expire_value) {
            $expire_time = 15 * MINUTE_IN_SECONDS;
        } elseif ('30' === $expire_value) {
            $expire_time = 30 * MINUTE_IN_SECONDS;
        }

        return $expire_time;
    }

    public function fetch_google_sheet() {
        $settings = $this->get_settings_for_display();

        $ep_api_settings = get_option('element_pack_api_settings');
        $api_key = !empty($ep_api_settings['google_map_key']) ? $ep_api_settings['google_map_key'] : '';

        $range = $settings['google_sheet_range'];
        $sheet_id = $settings['google_sheet_id'];

        $parameters = '?dateTimeRenderOption=FORMATTED_STRING&majorDimension=ROWS&valueRenderOption=FORMATTED_VALUE&key=';
        $url = self::API_URL . $sheet_id . '/values/' . $range . $parameters . $api_key;


        $expireTime = $this->get_transient_expire($settings);
        $transient_key = sprintf('bdt-table-google-sheet-data-%s', md5($sheet_id));

        $sheet_data = get_transient($transient_key);

        if (false === $sheet_data && $settings['google_sheet_cache'] == 'yes') {
            $data = wp_remote_get($url);
            $sheet_data = json_decode(wp_remote_retrieve_body($data), true);
            set_transient($transient_key, $sheet_data, apply_filters('element-pack/table/cached-time', $expireTime));
        } else if ($settings['google_sheet_cache'] != 'yes') {
            delete_transient($transient_key);
            $data = wp_remote_get($url);
            $sheet_data = json_decode(wp_remote_retrieve_body($data), true);
        }

        return $sheet_data;
    }

    public function google_sheet_table() {
        $settings = $this->get_settings_for_display();
        $sheet_data = $this->fetch_google_sheet();

        if (isset($sheet_data['error'])) {

            // print_r($sheet_data['error']);//Array ( [error] => Array ( [code] => 404 [message] => Requested entity was not found. [status] => NOT_FOUND ) )

            $errors = __('Requested entity was not found.', 'bdthemes-element-pack');

            printf('<div data-bdt-alert>
                        <a class="bdt-alert-close" data-bdt-close></a>
                        <p>%s</p>
                    </div>', $errors);

            return;
        }


        $table_columns = $sheet_data['values'][0];
        $table_rows = array_splice($sheet_data['values'], 1, count($sheet_data['values']));

    ?>

        <table class="bdt-static-table">

            <?php if ('' == $settings['hide_header']) : ?>
                <thead>
                    <tr>
                        <?php foreach ($table_columns as $column) : ?>
                            <th>
                                <?php echo esc_html($column); ?>
                            </th>
                        <?php endforeach; ?>

                    </tr>
                </thead>
            <?php endif; ?>

            <tbody>
                <?php
                for ($i = 0; $i < count($table_rows); $i++) :
                    if (count($table_columns) > count($table_rows[$i])) {
                        $dif = count($table_columns) - count($table_rows[$i]);
                        for ($j = 0; $j < $dif; $j++) {
                            array_push($table_rows[$i], null);
                        }
                    }
                ?>
                    <tr>
                        <?php
                        foreach ($table_rows[$i] as $row) :
                            $cell = $row == null ? '' : $row;
                        ?>
                            <td class="bdt-t--">
                                <?php echo esc_html($cell); ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endfor; ?>
            </tbody>

        </table>

    <?php
    }



    protected function render() {
        $settings = $this->get_settings_for_display();
        $id = 'bdt-table-' . $this->get_id();

        if ('custom' == $settings['source'] and '' == $settings['content']) {
            element_pack_alert(esc_html__('Opps!! You didn\'t enter any table data!', 'bdthemes-element-pack'));
        } elseif ('csv_file' == $settings['source'] and '' == $settings['file']['url']) {
            element_pack_alert(esc_html__('Opps!! You didn\'t add any CSV file', 'bdthemes-element-pack'));
        }

        if ('table_responsive_no' == $settings['table_responsive_control']) {
            $this->add_render_attribute('table-wrapper', 'class', ['bdt-table']);
        }

        if ('table_responsive_1' == $settings['table_responsive_control']) {
            $this->add_render_attribute('table-wrapper', 'class', ['bdt-table', 'bdt-table-responsive']);
        }

        if ('table_responsive_2' == $settings['table_responsive_control']) {
            $this->add_render_attribute('table-wrapper', 'class', ['bdt-table', 'bdt-table-default-responsive']);
        }

        if ('horizontal_scroll' == $settings['table_responsive_control']) {
            $this->add_render_attribute('table-wrapper', 'class', ['bdt-table', 'bdt-overflow-auto']);
        }

        $this->add_render_attribute('table-wrapper', 'class', $settings['stripe_style'] ? 'bdt-stripe' : '');
        $this->add_render_attribute('table-wrapper', 'id', $id);

        if ('yes' == $settings['use_data_table']) :

            $this->add_render_attribute('table-wrapper', 'class', 'bdt-data-table');

            $this->add_render_attribute(
                [
                    'table-wrapper' => [
                        'data-settings' => [
                            wp_json_encode([
                                'paging'    => ('yes' == $settings['show_pagination']) ? true : false,
                                'info'      => ('yes' == $settings['show_info']) ? true : false,
                                'searching' => ('yes' == $settings['show_searching']) ? true : false,
                                'ordering'  => ('yes' == $settings['show_ordering']) ? true : false,
                            ])
                        ]
                    ]
                ]
            );

        endif;

    ?>
        <div <?php echo $this->get_render_attribute_string('table-wrapper'); ?>>


            <?php
            if ('custom' == $settings['source']) {
                echo do_shortcode($settings['content']);
            } elseif ('csv_file' == $settings['source'] and '' !== $settings['file']['url']) {
                echo $this->csv_to_table();
            } elseif ('static' == $settings['source']) {
                $this->static_table();
            } elseif ('google_sheet' == $settings['source']) {
                $ep_api_settings = get_option('element_pack_api_settings');
                $api_key = !empty($ep_api_settings['google_map_key']) ? $ep_api_settings['google_map_key'] : '';
                $errors = '';
                if (empty($api_key)) {
                    $errors = __('API Key is Empty.', 'bdthemes-element-pack');
                }
                if (empty($settings['google_sheet_id'])) {
                    $errors = __('Google Sheet ID is Empty.', 'bdthemes-element-pack');
                }
                if (empty($settings['google_sheet_range'])) {
                    $errors = __('Google Sheet Range is Empty.', 'bdthemes-element-pack');
                }

                if (!empty($errors)) {
                    printf('<div data-bdt-alert>
                        <a class="bdt-alert-close" data-bdt-close></a>
                        <p>%s</p>
                    </div>', $errors);
                }

                if (empty($errors)) {
                    $this->google_sheet_table();
                }
            }
            ?>

        </div>
<?php
    }
}
