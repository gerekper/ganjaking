<?php

namespace ElementPack\Modules\CryptoCurrencyTable\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use ElementPack\Base\Module_Base;

if (!defined('ABSPATH')) {
    exit;
}

// Exit if accessed directly

class Crypto_Currency_Table extends Module_Base {

    public $weather_data = [];

    public $open_weather_api_current_url = 'http://api.openweathermap.org/data/2.5/air_pollution';

    public function get_name() {
        return 'bdt-crypto-currency-table';
    }

    public function get_title() {
        return BDTEP . esc_html__('Crypto Currency Table', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-crypto-currency-table';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-font', 'datatables', 'ep-crypto-currency-table'];
        }
    }

    public function get_script_depends() {
        return ['chart', 'datatables-uikit', 'ep-crypto-currency-table'];
    }

    public function get_keywords() {
        return ['crypto-currency'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__('Crypto Currency', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'crypto_currency',
            [
                'label'       => __('Crypto Currency', 'bdthemes-element-pack'),
                'description' => __('If you want to show any selected crypto currency in your table so type those currency name here. For example: bitcoin,ethereum,litecoin', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => __('bitcoin,ethereum', 'bdthemes-element-pack'),
                'label_block' => true,
                'dynamic'     => ['active' => true],
            ]
        );

        $this->add_control(
            'currency',
            [
                'label'   => esc_html__('Currency', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'USD',
                'options' => [
                    'USD' => esc_html__('USD', 'bdthemes-element-pack'),
                    'EUR' => esc_html__('EUR', 'bdthemes-element-pack'),
                    'CRC' => esc_html__('CRC', 'bdthemes-element-pack'),
                    'GBP' => esc_html__('GBP', 'bdthemes-element-pack'),
                    'INR' => esc_html__('INR', 'bdthemes-element-pack'),
                    'JPY' => esc_html__('JPY', 'bdthemes-element-pack'),
                    'KRW' => esc_html__('KRW', 'bdthemes-element-pack'),
                    'NGN' => esc_html__('NGN', 'bdthemes-element-pack'),
                    'PHP' => esc_html__('PHP', 'bdthemes-element-pack'),
                    'PLN' => esc_html__('PLN', 'bdthemes-element-pack'),
                    'PYG' => esc_html__('PYG', 'bdthemes-element-pack'),
                    'THB' => esc_html__('THB', 'bdthemes-element-pack'),
                    'UAH' => esc_html__('UAH', 'bdthemes-element-pack'),
                    'VND' => esc_html__('VND', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->add_control(
            'limit',
            [
                'label'   => esc_html__('Limit', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::NUMBER,
                'default' => 10,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_data_table',
            [
                'label' => __('Data Table Settings', 'bdthemes-element-pack'),
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

        $this->start_controls_section(
            'section_additional_option',
            [
                'label' => __('Additional Option', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'table_responsive_control',
            [
                'label'   => __('Responsive', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'table_responsive_2',
                'options' => [
                    'table_responsive_no' => esc_html__('No Responsive', 'bdthemes-element-pack'),
                    'table_responsive_1'  => esc_html__('Responsive 1', 'bdthemes-element-pack'),
                    'table_responsive_2'  => esc_html__('Responsive 2', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->add_control(
            'show_stripe',
            [
                'label'     => __('Row Stripe', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'show_row_hover',
            [
                'label' => __('Row Hover', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->end_controls_section();

        //Style
        $this->start_controls_section(
            'section_cryptocurrency_table_header_style',
            [
                'label' => __('Table Header', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_cryptocurrency_table_header_style');

        $this->start_controls_tab(
            'tab_cryptocurrency_table_header_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'cryptocurrency_header_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .bdt-crypto-currency-table .bdt-crypto-header th' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'cryptocurrency_header_background',
                'selector' => '{{WRAPPER}} .bdt-crypto-currency-table .bdt-crypto-header th',
            ]
        );

        //border color
        $this->add_control(
            'cryptocurrency_header_border_color',
            [
                'label'     => __('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#eaeaea',
                'selectors' => [
                    '{{WRAPPER}} .bdt-crypto-currency-table .bdt-data-table-wrap th' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'header_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-crypto-currency-table .bdt-crypto-header th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'header_typography',
                'selector' => '{{WRAPPER}} .bdt-crypto-currency-table .bdt-crypto-header th',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_cryptocurrency_header_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'cryptocurrency_header_hover_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .bdt-crypto-currency-table .bdt-crypto-header:hover th' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'cryptocurrency_header_background_hover',
                'selector' => '{{WRAPPER}} .bdt-crypto-currency-table .bdt-crypto-header:hover th',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_body',
            [
                'label' => __('Table Body', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'      => 'cell_border',
                'selector'  => '{{WRAPPER}} .bdt-crypto-currency-table .bdt-data-table-wrap tbody tr td',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'cell_padding',
            [
                'label'      => __('Cell Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-crypto-currency-table .bdt-data-table-wrap tbody tr td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'after',
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
            'normal_background',
            [
                'label'     => __('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-crypto-currency-table .bdt-data-table-wrap tbody tr' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'normal_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-crypto-currency-table .bdt-data-table-wrap tbody tr td' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'normal_border_color',
            [
                'label'     => __('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-crypto-currency-table .bdt-data-table-wrap tbody tr td' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_hover',
            [
                'label'     => __('Hover', 'bdthemes-element-pack'),
                'condition' => [
                    'show_row_hover' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'row_hover_background',
            [
                'label'     => __('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-table-hover>tr:hover, {{WRAPPER}} .bdt-table-hover tbody tr:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'row_hover_text_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-table-hover>tr:hover, {{WRAPPER}} .bdt-table-hover tbody tr:hover td' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_stripe',
            [
                'label'     => __('Stripe', 'bdthemes-element-pack'),
                'condition' => [
                    'show_stripe' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'stripe_background',
            [
                'label'     => __('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-crypto-currency-table .bdt-table-striped>tr:nth-of-type(odd), {{WRAPPER}} .bdt-crypto-currency-table .bdt-table-striped tbody tr:nth-of-type(odd)' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'stripe_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-crypto-currency-table .bdt-table-striped>tr:nth-of-type(odd) td, {{WRAPPER}} .bdt-crypto-currency-table .bdt-table-striped tbody tr:nth-of-type(odd) td' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_cryptocurrency_image_style',
            [
                'label' => __('Currency Image', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
                // 'condition' => [
                //     'show_currency_image' => 'yes',
                // ],
            ]
        );

        $this->add_responsive_control(
            'logo_image_width',
            [
                'label'   => __('Width', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'unit' => 'px',
                ],
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min' => 5,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-crypto-currency-table .bdt-data-table-wrap .bdt-coin-image img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_cryptocurrency_name_style',
            [
                'label' => __('Currency Name', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
                // 'condition' => [
                //     'show_currency_name' => 'yes',
                // ],
            ]
        );

        $this->add_control(
            'cryptocurrency_name_color',
            [
                'label'     => __('Name Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .bdt-crypto-currency-table .bdt-data-table-wrap .bdt-coin-name' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'name_typography',
                'selector' => '{{WRAPPER}} .bdt-crypto-currency-table .bdt-data-table-wrap .bdt-coin-name',
            ]
        );

        $this->add_control(
            'cryptocurrency_short_name_color',
            [
                'label'     => __('Short Name Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .bdt-crypto-currency-table .bdt-data-table-wrap .bdt-coin-symbol' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'short_name_typography',
                'selector' => '{{WRAPPER}} .bdt-crypto-currency-table .bdt-data-table-wrap .bdt-coin-symbol',
            ]
        );

        $this->add_responsive_control(
            'cryptocurrency_name_spacing',
            [
                'label' => esc_html__('Spacing', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-crypto-currency-table .bdt-data-table-wrap .bdt-coin-name' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_cryptocurrency_text_style',
            [
                'label' => __('Currency Text', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'cryptocurrency_text_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .bdt-crypto-currency-table .bdt-data-table-wrap td' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'text_typography',
                'selector' => '{{WRAPPER}} .bdt-crypto-currency-table .bdt-data-table-wrap td',
            ]
        );

        $this->add_responsive_control(
            'text_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-crypto-currency-table .bdt-data-table-wrap td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();

        // search control
        $this->start_controls_section(
            'section_search_style',
            [
                'label'     => __('Search', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_searching' => 'yes',
                ],
            ]
        );

        //text color
        $this->add_control(
            'search_text_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-crypto-currency-table label' => 'color: {{VALUE}};',
                ],
            ]
        );

        //typography
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'search_typography',
                'selector' => '{{WRAPPER}} .bdt-crypto-currency-table label',
            ]
        );

        $this->add_control(
            'search_input_color',
            [
                'label'     => __('Input Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-crypto-currency-table .dataTables_filter input' => 'color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        //background color
        $this->add_control(
            'search_bg_color',
            [
                'label'     => __('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-crypto-currency-table .dataTables_filter input' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'search_border',
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-crypto-currency-table .dataTables_filter input',
            ]
        );

        //border group control

        //border
        $this->add_responsive_control(
            'search_border_radius',
            [
                'label'      => __('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-crypto-currency-table .dataTables_filter input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );

        //padding
        $this->add_responsive_control(
            'search_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-crypto-currency-table .dataTables_filter input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );

        //margin
        $this->add_responsive_control(
            'search_margin',
            [
                'label'      => __('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-crypto-currency-table .dataTables_filter input' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );

        $this->end_controls_section();

        // order control
        $this->start_controls_section(
            'section_order_style',
            [
                'label'     => __('Order', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_ordering' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'order_input_color',
            [
                'label'     => __('Input Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} div.dataTables_wrapper div.dataTables_length select' => 'color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );
        //background color
        $this->add_control(
            'order_bg_color',
            [
                'label'     => __('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} div.dataTables_wrapper div.dataTables_length select' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'order_border',
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} div.dataTables_wrapper div.dataTables_length select',
            ]
        );

        $this->add_responsive_control(
            'order_margin',
            [
                'label'      => __('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .dataTables_wrapper .dataTables_length' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );

        $this->end_controls_section();

        // info control
        $this->start_controls_section(
            'section_info_style',
            [
                'label'     => __('Info', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_info' => 'yes',
                ],
            ]
        );
        //color
        $this->add_control(
            'info_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} div.dataTables_wrapper div.dataTables_info' => 'color: {{VALUE}};',
                ],
            ]
        );
        //typography
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'info_typography',
                'selector' => '{{WRAPPER}} div.dataTables_wrapper div.dataTables_info',
            ]
        );
        //padding
        $this->add_responsive_control(
            'info_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} div.dataTables_wrapper div.dataTables_info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );
        //margin
        $this->add_responsive_control(
            'info_margin',
            [
                'label'      => __('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} div.dataTables_wrapper div.dataTables_info' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );
        $this->end_controls_section();

        // pagination control
        $this->start_controls_section(
            'section_pagination_style',
            [
                'label'     => __('Pagination', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_pagination' => 'yes',
                ],
            ]
        );
        //color
        $this->add_control(
            'pagination_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-pagination>*>*' => 'color: {{VALUE}};',
                ],
            ]
        );

        //background color
        $this->add_control(
            'pagination_bg_color',
            [
                'label'     => __('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-pagination>*>*' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'pagination_border',
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-pagination>*>*',
            ]
        );

        //radius
        $this->add_responsive_control(
            'pagination_radius',
            [
                'label'      => __('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-pagination>*>*' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );

        //padding
        $this->add_responsive_control(
            'pagination_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-pagination>*>*' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );
        //margin
        $this->add_responsive_control(
            'pagination_margin',
            [
                'label'      => __('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-pagination>*>*' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );

        //typography
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'pagination_typography',
                'selector' => '{{WRAPPER}} .bdt-pagination>*>*',
            ]
        );
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $id = 'bdt-crypto-currency-' . $this->get_id();

        $crypto_currency = ($settings['crypto_currency']) ? $settings['crypto_currency'] : 'all';

        $this->add_render_attribute('crypto', [
            'class' => 'bdt-crypto-currency-table',
            'id' => $id,
            'data-settings' => wp_json_encode(
                [
                    'id' => '#' . $id,
                    'tableId' => '#' . $id . '-table',
                    'ids' => $crypto_currency,
                    'currency' => $settings['currency'],
                    'limit' => !empty($settings['limit']) ? $settings['limit'] : 100,
                    'order' => 'market_cap_desc',
                    'pageLength' => !empty($settings['limit']) ? $settings['limit'] : 10,
                    'searching' => ('yes' == $settings['show_searching']) ? true : false,
                    'ordering' => ('yes' == $settings['show_ordering']) ? true : false,
                    'paging' => ('yes' == $settings['show_pagination']) ? true : false,
                    'info' => ('yes' == $settings['show_info']) ? true : false,
                ]
            ),
        ]);

        $this->add_render_attribute('crypto-table', 'class', 'bdt-data-table-wrap');

        if ('table_responsive_no' == $settings['table_responsive_control']) {
            $this->add_render_attribute('crypto-table', 'class', ['bdt-table']);
        }

        if ('table_responsive_1' == $settings['table_responsive_control']) {
            $this->add_render_attribute('crypto-table', 'class', ['bdt-table', 'bdt-table-responsive']);
        }

        if ('table_responsive_2' == $settings['table_responsive_control']) {
            $this->add_render_attribute('crypto-table', 'class', ['bdt-table', 'bdt-table-responsive-2']);
        }

        if ($settings['show_row_hover']) {
            $this->add_render_attribute('crypto-table', 'class', 'bdt-table-hover');
        }

        if ($settings['show_stripe']) {
            $this->add_render_attribute('crypto-table', 'class', 'bdt-table-striped');
        } else {
            $this->add_render_attribute('crypto-table', 'class', 'bdt-table-divider');
        }

?>
        <div <?php $this->print_render_attribute_string('crypto'); ?>>
            <table id="<?php echo esc_attr($id); ?>-table" <?php echo $this->get_render_attribute_string('crypto-table'); ?>>
                <thead>
                    <tr class="bdt-crypto-header">
                        <th class="bdt-hash-head">#</th>
                        <th class="bdt-coin-head"><?php echo esc_html('coin', 'bdthemes-element-pack');?></th>
                        <th class="bdt-price-head"><?php echo esc_html('price', 'bdthemes-element-pack');?></th>
                        <th class="bdt-price-24h-pecentage"><?php echo esc_html('Change', 'bdthemes-element-pack');?></th>
                        <th class="bdt-marketcap-head"><?php echo esc_html('marketcap', 'bdthemes-element-pack');?></th>
                        <th class="bdt-volume-head"><?php echo esc_html('Volume (24h)', 'bdthemes-element-pack');?></th>
                        <th class="bdt-chat-head"><?php echo esc_html('Supply', 'bdthemes-element-pack');?></th>
                        <th class="bdt-last-24h-changes"><?php echo esc_html('LAST 24H', 'bdthemes-element-pack');?></th>
                    </tr>
                </thead>
            </table>
        </div>
<?php
    }
}
