<?php

namespace ElementPack\Modules\AdvancedGmap\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Utils;
use ElementPack\Base\Module_Base;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

class Advanced_Gmap extends Module_Base
{

    public function get_name()
    {
        return 'bdt-advanced-gmap';
    }

    public function get_title()
    {
        return BDTEP . esc_html__('Advanced Google Map', 'bdthemes-element-pack');
    }

    public function get_icon()
    {
        return 'bdt-wi-advanced-gmap';
    }

    public function get_categories()
    {
        return ['element-pack'];
    }

    public function get_keywords()
    {
        return ['advanced', 'gmap', 'location'];
    }

    public function get_style_depends()
    {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-advanced-gmap'];
        }
    }

    public function get_script_depends()
    {
        if ($this->ep_is_edit_mode()) {
            return ['gmap-api', 'gmap', 'ep-scripts'];
        } else {
            return ['gmap-api', 'gmap', 'ep-advanced-gmap'];
        }
    }

    public function get_custom_help_url()
    {
        return 'https://youtu.be/qaZ-hv6UPDY';
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'section_content_gmap',
            [
                'label' => esc_html__('Google Map', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'avd_google_map_zoom_control',
            [
                'label' => esc_html__('Zoom Control', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'avd_google_map_default_zoom',
            [
                'label' => esc_html__('Default Zoom', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 15,
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 24,
                    ],
                ],
                'condition' => ['avd_google_map_zoom_control' => 'yes'],
            ]
        );

        $this->add_control(
            'avd_google_map_street_view',
            [
                'label' => esc_html__('Street View Control', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'avd_google_map_type_control',
            [
                'label' => esc_html__('Map Type Control', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_responsive_control(
            'avd_google_map_height',
            [
                'label' => esc_html__('Map Height', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' => '--map-list-height: {{SIZE}}{{UNIT}}',
                ],
            ]
        );
        $this->add_control(
            'avd_google_map_show_list',
            [
                'label' => esc_html__('Show List', 'bdthemes-element-pack') . BDTEP_NC,
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'avd_google_map_list_position',
            [
                'label' => esc_html__('List Position', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'right',
                'options' => [
                    'left' => esc_html__('Left', 'bdthemes-element-pack'),
                    'right' => esc_html__('Right', 'bdthemes-element-pack'),
                ],
                'condition' => [
                    'avd_google_map_show_list' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'gmap_geocode',
            [
                'label' => esc_html__('Search Address', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'search_placeholder_text',
            [
                'label'     => esc_html__('Placeholder Text', 'bdthemes-element-pack') . BDTEP_NC,
                'type'      => Controls_Manager::TEXT,
                'default'     => esc_html__('Search...', 'bdthemes-element-pack'),
                'condition' => [
                    'gmap_geocode' => 'yes'
                ]
            ]
        );

        $this->add_responsive_control(
            'search_align',
            [
                'label' => esc_html__('Alignment', 'bdthemes-element-pack'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-gmap-search-wrapper' => 'text-align: {{VALUE}};',
                ],
                'condition' => [
                    'gmap_geocode' => 'yes',
                ],
            ]
        );

        // $this->add_responsive_control(
        //     'search_spacing',
        //     [
        //         'label' => esc_html__('Spacing', 'bdthemes-element-pack'),
        //         'type'  => Controls_Manager::SLIDER,
        //         'range' => [
        //             'px' => [
        //                 'max' => 100,
        //             ],
        //         ],
        //         'selectors' => [
        //             '{{WRAPPER}} .bdt-gmap-search-wrapper'  => 'margin-bottom: {{SIZE}}{{UNIT}}',
        //         ],
        //         'condition' => [
        //             'gmap_geocode' => 'yes',
        //         ],
        //     ]
        // );
        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_marker',
            [
                'label' => esc_html__('Marker', 'bdthemes-element-pack'),
            ]
        );

        $repeater = new Repeater();

        $repeater->start_controls_tabs('tabs_content_marker');

        $repeater->start_controls_tab(
            'tab_content_content',
            [
                'label' => esc_html__('Content', 'bdthemes-element-pack'),
            ]
        );

        $repeater->add_control(
            'marker_lat',
            [
                'label' => esc_html__('Latitude', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => ['active' => true],
                'default' => '24.8238746',
            ]
        );

        $repeater->add_control(
            'marker_lng',
            [
                'label' => esc_html__('Longitude', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => ['active' => true],
                'default' => '89.3816299',
            ]
        );

        $repeater->add_control(
            'marker_title',
            [
                'label' => esc_html__('Title', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => ['active' => true],
                'default' => 'Another Place',
            ]
        );
        $repeater->add_control(
            'marker_place',
            [
                'label' => esc_html__('Place', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => ['active' => true],
                'default' => esc_html__('Bangladesh', 'bdthemes-element-pack'),
            ]
        );

        $repeater->add_control(
            'marker_content',
            [
                'label' => esc_html__('Content', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXTAREA,
                'dynamic' => ['active' => true],
                'default' => esc_html__('Your Business Address Here', 'bdthemes-element-pack'),
            ]
        );
        $repeater->add_control(
            'marker_phone',
            [
                'label' => esc_html__('Phone', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => ['active' => true],
                'default' => esc_html__('+880123456789', 'bdthemes-element-pack'),
            ]
        );
        $repeater->add_control(
            'marker_website',
            [
                'label' => esc_html__('Website', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => ['active' => true],
                'default' => esc_html__('https://bdthemes.com', 'bdthemes-element-pack'),
            ]
        );
        $repeater->end_controls_tab();

        $repeater->start_controls_tab(
            'tab_content_marker',
            [
                'label' => esc_html__('Marker', 'bdthemes-element-pack'),
            ]
        );

        $repeater->add_control(
            'custom_marker',
            [
                'label' => esc_html__('Custom marker', 'bdthemes-element-pack'),
                'description' => esc_html__('Use max 32x32 px size icon for better result.', 'bdthemes-element-pack'),
                'type' => Controls_Manager::MEDIA,

            ]
        );
        $repeater->add_control(
            'marker_image',
            [
                'label' => esc_html__('Image', 'bdthemes-element-pack'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );
        $repeater->end_controls_tab();

        $repeater->end_controls_tabs();

        $this->add_control(
            'marker',
            [
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'marker_lat' => '24.8248746',
                        'marker_lng' => '89.3826299',
                        'marker_title' => esc_html__('BdThemes', 'bdthemes-element-pack'),
                        'marker_place' => esc_html__('Bogura', 'bdthemes-element-pack'),
                        'marker_content' => esc_html__('<strong>BdThemes Limited</strong>,<br>Latifpur, Bogra - 5800,<br>Bangladesh', 'bdthemes-element-pack'),
                        'marker_phone' => esc_html__('+880123456789', 'bdthemes-element-pack'),
                        'marker_website' => esc_html__('https://bdthemes.com', 'bdthemes-element-pack'),
                    ],
                ],
                'title_field' => '{{{ marker_title }}}',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_gmap',
            [
                'label' => esc_html__('GMap Style', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'avd_google_map_style',
            [
                'label' => esc_html__('Style Json Code', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => '',
                'description' => sprintf(__('Go to this link: %1s snazzymaps.com %2s and pick a style, copy the json code from first with [ to last with ] then come back and paste here', 'bdthemes-element-pack'), '<a href="https://snazzymaps.com/" target="_blank">', '</a>'),
            ]
        );

        $this->start_controls_tabs('tabs_style_css_filters');

        $this->start_controls_tab(
            'tab_css_filter_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'css_filters',
                'selector' => '{{WRAPPER}} .bdt-advanced-gmap',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'map_border',
                'label' => esc_html__('Border', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-advanced-gmap',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'map_border_radius',
            [
                'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-advanced-gmap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_css_filter_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'css_filters_hover',
                'selector' => '{{WRAPPER}} .bdt-advanced-gmap:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_search',
            [
                'label' => esc_html__('Search', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'gmap_geocode' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'search_background',
            [
                'label' => esc_html__('Background', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-search.bdt-search-default .bdt-search-input' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'search_color',
            [
                'label' => esc_html__('Text Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-search.bdt-search-default .bdt-search-input' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'search_placeholder_color',
            [
                'label' => esc_html__('Placeholder Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-search.bdt-search-default .bdt-search-input::placeholder' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-search.bdt-search-default span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'search_shadow',
                'selector' => '{{WRAPPER}} .bdt-search.bdt-search-default .bdt-search-input',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'search_border',
                'label' => esc_html__('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .bdt-search.bdt-search-default .bdt-search-input',
            ]
        );

        $this->add_responsive_control(
            'search_border_radius',
            [
                'label' => esc_html__('Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-search.bdt-search-default .bdt-search-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_responsive_control(
            'search_padding',
            [
                'label' => esc_html__('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-search.bdt-search-default .bdt-search-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'search_margin',
            [
                'label' => esc_html__('Margin', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-search.bdt-search-default .bdt-search-input' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'avd_google_map_show_list!' => 'yes',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'searh_typography',
                'label' => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-search.bdt-search-default .bdt-search-input',
            ]
        );

        $this->end_controls_section();
        $this->start_controls_section(
            'section_content_list',
            [
                'label' => esc_html__('Map List', 'bdthemes-element-pack') . BDTEP_NC,
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'avd_google_map_show_list' => 'yes',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'map_list_background',
                'label' => esc_html__('Backgrund', 'bdthemes-element-pack'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .bdt-gmap-lists-wrapper',
            ]
        );
        $this->add_control(
            'list_item_separator_color',
            [
                'label' => esc_html__('Separator Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-advanced-map .bdt-gmap-list-item' => 'border-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'list_item_separator_width',
            [
                'label' => esc_html__('Separator Height', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-advanced-map .bdt-gmap-list-item' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs(
            'list_item_tabs'
        );
        $this->start_controls_tab(
            'list_item_tab_title',
            [
                'label' => esc_html__('Title', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'list_item_title_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-advanced-map .bdt-gmap-list-content .bdt-title' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'list_item_title_typography',
                'label' => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-advanced-map .bdt-gmap-list-content .bdt-title',
            ]
        );

        $this->end_controls_tab();
        $this->start_controls_tab(
            'list_item_tab_place',
            [
                'label' => esc_html__('Place', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'list_item_place_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-advanced-map .bdt-gmap-list-content .bdt-place' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'list_item_place_typography',
                'label' => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-advanced-map .bdt-gmap-list-content .bdt-place',
            ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
            'list_item_tab_image',
            [
                'label' => esc_html__('Image', 'bdthemes-element-pack'),
            ]
        );
        $this->add_responsive_control(
            'list_item_iamge_radius',
            [
                'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .bdt-advanced-map .bdt-gmap-image-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
                ],
            ]
        );
        // $this->add_control(
        //     'list_item_place_color',
        //     [
        //         'label'     => esc_html__('Color', 'bdthemes-element-pack'),
        //         'type'      => Controls_Manager::COLOR,
        //         'selectors' => [
        //             '{{WRAPPER}} .bdt-advanced-map .bdt-gmap-list-content .bdt-place' => 'color: {{VALUE}}',
        //         ],
        //     ]
        // );
        // $this->add_group_control(
        //     Group_Control_Typography::get_type(),
        //     [
        //         'name'      => 'list_item_place_typography',
        //         'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
        //         'selector'  => '{{WRAPPER}} .bdt-advanced-map .bdt-gmap-list-content .bdt-place',
        //     ]
        // );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
        $this->start_controls_section(
            'section_map_tooltip',
            [
                'label' => esc_html__('Tooltip', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_responsive_control(
            'tooltip_width',
            [
                'label' => esc_html__('Tooltip Width', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 450,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-advanced-map .bdt-map-tooltip-view' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'tooltip_image_width',
            [
                'label' => esc_html__('Image Width', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 250,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-advanced-map .bdt-map-tooltip-view .bdt-map-image' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->start_controls_tabs(
            'avd_google_map_tooltip_tabs'
        );
        $this->start_controls_tab(
            'avd_google_map_tooltip_tab_title',
            [
                'label' => esc_html__('Title', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'avd_google_map_tooltip_title_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-advanced-map .bdt-map-tooltip-view .bdt-tooltip-title' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'avd_google_map_tooltip_title_spacing',
            [
                'label' => esc_html__('Spacing', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-advanced-map .bdt-map-tooltip-view .bdt-tooltip-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'avd_google_map_tooltip_title_typography',
                'label' => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-advanced-map .bdt-map-tooltip-view .bdt-tooltip-title',
            ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
            'avd_google_map_tooltip_tab_sub_place',
            [
                'label' => esc_html__('Place', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'avd_google_map_tooltip_place_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-advanced-map .bdt-map-tooltip-view .bdt-tooltip-place' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'avd_google_map_tooltip_place_spacing',
            [
                'label' => esc_html__('Spacing', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-advanced-map .bdt-map-tooltip-view .bdt-tooltip-place' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'avd_google_map_tooltip_place_typography',
                'label' => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-advanced-map .bdt-map-tooltip-view .bdt-tooltip-place',
            ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
            'avd_google_map_tooltip_tab_content',
            [
                'label' => esc_html__('Content', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'avd_google_map_tooltip_content_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-advanced-map .bdt-map-tooltip-view .bdt-tooltip-content' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'avd_google_map_tooltip_content_spacing',
            [
                'label' => esc_html__('Spacing', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-advanced-map .bdt-map-tooltip-view .bdt-tooltip-content' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'avd_google_map_tooltip_content_typography',
                'label' => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-advanced-map .bdt-map-tooltip-view .bdt-tooltip-content',
            ]
        );
        $this->end_controls_tab();

        $this->start_controls_tab(
            'avd_google_map_tooltip_tab_link',
            [
                'label' => esc_html__('Link', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'avd_google_map_tooltip_link_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-advanced-map .bdt-map-tooltip-view a' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'avd_google_map_tooltip_link_typography',
                'label' => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-advanced-map .bdt-map-tooltip-view a',
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
    }
    public function render()
    {
        $settings = $this->get_settings_for_display();
        $id = 'bdt-advanced-gmap-' . $this->get_id() . '-' . rand(10, 100);
        $ep_api_settings = get_option('element_pack_api_settings');
        $map_settings = [];
        $map_settings['el'] = '#' . $id;

        $marker_settings = [];
        $bdt_counter = 0;
        $all_markers = [];

        foreach ($settings['marker'] as $marker_item) {
            $image_src = wp_get_attachment_image_src($marker_item['marker_image']['id'], 'medium');
            $marker_settings['lat'] = (float) (($marker_item['marker_lat']) ? $marker_item['marker_lat'] : '');
            $marker_settings['lng'] = (float) (($marker_item['marker_lng']) ? $marker_item['marker_lng'] : '');
            $marker_settings['title'] = isset($marker_item['marker_title']) ? $marker_item['marker_title'] : '';
            $marker_settings['place'] = isset($marker_item['marker_place']) ? $marker_item['marker_place'] : '';
            $marker_settings['icon'] = isset($marker_item['custom_marker']['url']) ? $marker_item['custom_marker']['url'] : '';
            $marker_settings['content'] = isset($marker_item['marker_content']) ? $marker_item['marker_content'] : '';
            $marker_settings['phone'] = isset($marker_item['marker_phone']) ? $marker_item['marker_phone'] : '';
            $marker_settings['website'] = isset($marker_item['marker_website']) ? $marker_item['marker_website'] : '';
            $marker_settings['image'] = ($image_src) ? $image_src[0] : $marker_item['custom_marker']['url'];

            $all_markers[] = $marker_settings;

            $bdt_counter++;
            if (1 === $bdt_counter) {
                $map_settings['lat'] = ($marker_item['marker_lat']) ? $marker_item['marker_lat'] : '';
                $map_settings['lng'] = ($marker_item['marker_lng']) ? $marker_item['marker_lng'] : '';
            }
        };

        $map_settings['zoomControl'] = ($settings['avd_google_map_zoom_control']) ? true : false;
        $map_settings['zoom'] = isset($settings['avd_google_map_default_zoom']['size']) ? (int) $settings['avd_google_map_default_zoom']['size'] : 15;

        $map_settings['streetViewControl'] = ($settings['avd_google_map_street_view']) ? true : false;
        $map_settings['mapTypeControl'] = ($settings['avd_google_map_type_control']) ? true : false;?>
		<?php if (empty($ep_api_settings['google_map_key'])): ?>
			<div class="bdt-alert-warning" data-bdt-alert>
				<a class="bdt-alert-close" data-bdt-close></a>
				<?php $ep_setting_url = esc_url(admin_url('admin.php?page=element_pack_options#element_pack_api_settings'));?>
				<p><?php printf(__('Please set your google map api key in <a href="%s">element pack settings</a> to show your map correctly.', 'bdthemes-element-pack'), $ep_setting_url);?></p>
			</div>
		<?php endif;?>

		<?php if ($settings['gmap_geocode'] === 'yes' && $settings['avd_google_map_show_list'] !== 'yes'): ?>

			<div class="bdt-gmap-search-wrapper bdt-margin">
				<form method="post" id="<?php echo esc_attr($id); ?>form" class="bdt-search bdt-search-default">
					<span data-bdt-search-icon></span>
					<input id="<?php echo esc_attr($id); ?>address" name="address" class="bdt-search-input" type="search" placeholder="<?php echo !empty($settings['search_placeholder_text']) ? esc_html($settings['search_placeholder_text']) : 'Search...'; ?>">
				</form>
			</div>

		<?php endif;
        $this->add_render_attribute('bdt-advanced-map', 'style', 'opacity:0');
        $this->add_render_attribute('bdt-advanced-map', 'class', 'bdt-advanced-map');
        if (($settings['avd_google_map_show_list'] == 'yes')):
            $this->add_render_attribute('bdt-advanced-map', 'class', ['bdt-direction-' . $settings['avd_google_map_list_position'] . '']);
        endif;
        if (($settings['avd_google_map_show_list'] == 'yes') && ($settings['gmap_geocode'] == 'yes')):
            $this->add_render_attribute('bdt-advanced-map', 'class', ['bdt-has-lists-search-' . $settings['avd_google_map_show_list'] . '']);
        endif;
        $this->add_render_attribute('advanced-gmap', 'class', ['bdt-advanced-gmap']);
        $this->add_render_attribute('advanced-gmap', 'id', $id);
        $this->add_render_attribute('advanced-gmap', 'data-map_markers', wp_json_encode($all_markers));

        if ('' != $settings['avd_google_map_style']) {
            $this->add_render_attribute('advanced-gmap', 'data-map_style', trim(preg_replace('/\s+/', ' ', $settings['avd_google_map_style'])));
        }

        $this->add_render_attribute('advanced-gmap', 'data-map_settings', wp_json_encode($map_settings));
        $this->add_render_attribute('advanced-gmap', 'data-map_geocode', ('yes' == $settings['gmap_geocode']) ? 'true' : 'false');
        ?>
		<div <?php $this->print_render_attribute_string('bdt-advanced-map');?>>
			<div class="bdt-grid-wrap">
				<div class="bdt-advanced-map-wrapper">
					<div <?php $this->print_render_attribute_string('advanced-gmap');?>></div>
				</div>
				<?php if ($settings['avd_google_map_show_list'] === 'yes'): ?>
					<div class="bdt-gmap-lists-wrapper">
						<?php if ($settings['gmap_geocode'] === 'yes'): ?>
							<div class="bdt-gmap-search-wrapper">
								<form class="bdt-search bdt-search-default">
									<div class="search-box">
										<input type="text" placeholder="<?php echo !empty($settings['search_placeholder_text']) ? esc_html($settings['search_placeholder_text']) : 'Search Places'; ?>" class="bdt-search-input" />
									</div>
								</form>
							</div>
						<?php endif;?>
						<ul class="bdt-gmap-lists">
							<?php
foreach ($settings['marker'] as $marker_item) {
            $image_src = wp_get_attachment_image_src($marker_item['marker_image']['id'], 'medium');
            $this->add_render_attribute('bdt-gmap-list-item', 'data-settings', [
                wp_json_encode(array_filter([
                    'el' => '#' . $id,
                    'lat' => (float) (($marker_item['marker_lat']) ? $marker_item['marker_lat'] : ''),
                    'lng' => (float) (($marker_item['marker_lng']) ? $marker_item['marker_lng'] : ''),
                    'title' => isset($marker_item['marker_title']) ? $marker_item['marker_title'] : '',
                    'place' => isset($marker_item['marker_place']) ? $marker_item['marker_place'] : '',
                    'icon' => isset($marker_item['custom_marker']) ? $marker_item['custom_marker']['url'] : '',
                    'image' => $image_src,
                    'content' => isset($marker_item['marker_content']) ? $marker_item['marker_content'] : '',
                    'phone' => isset($marker_item['marker_phone']) ? $marker_item['marker_phone'] : '',
                    'website' => isset($marker_item['marker_website']) ? $marker_item['marker_website'] : '',
                ])),
            ], true)
            ?>

								<div class="bdt-gmap-list-item" <?php $this->print_render_attribute_string('bdt-gmap-list-item');?>>
									<div class="bdt-gmap-image-wrapper">
										<?php
if ((!$image_src) && ($marker_item['custom_marker']['url'] === '')) {
                printf('<img src="%s"/>', '' . BDTEP_ASSETS_URL . 'images/location.svg');
            } else if (!$image_src) {
                printf('<img src="%s"/>', $marker_item['custom_marker']['url']);
            } else {
                printf('<img src="%s"/>', $image_src[0]);
            }
            ?>

									</div>
									<div class="bdt-gmap-list-content">
										<h5 class="bdt-title"><?php echo esc_html__($marker_item['marker_title'], 'bdthemes-element-pack'); ?></h5>
										<span class="bdt-place"><?php echo esc_html__($marker_item['marker_place'], 'bdthemes-element-pack'); ?></span>
									</div>
								</div>
							<?php
};
        ?>
						</ul>
					</div>
				<?php endif;?>
			</div>
		</div>
<?php
}
}
