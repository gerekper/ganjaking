<?php

namespace ElementPack\Modules\TestimonialGrid\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;
use ElementPack\Traits\Global_Widget_Controls;

if (!defined('ABSPATH')) exit; // Exit if accessed directly
class Testimonial_Grid extends Module_Base {
    use Group_Control_Query;
    use Global_Widget_Controls;
    private $_query = null;
    public function get_name() {
        return 'bdt-testimonial-grid';
    }

    public function get_title() {
        return BDTEP . esc_html__('Testimonial Grid', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-testimonial-grid';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['testimonial', 'grid'];
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-font', 'ep-testimonial-grid'];
        }
    }

    public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['imagesloaded', 'ep-scripts'];
        } else {
            return ['imagesloaded'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/pYMTXyDn8g4';
    }

    public function get_query() {
        return $this->_query;
    }

    public function register_controls() {

        $this->start_controls_section(
            'section_content_layout',
            [
                'label' => esc_html__('Layout', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'layout',
            [
                'label'   => esc_html__('Layout', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => '1',
                'options' => [
                    '1' => esc_html__('Default', 'bdthemes-element-pack'),
                    '2' => esc_html__('Top Avatar', 'bdthemes-element-pack'),
                    '3' => esc_html__('Bottom Avatar', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label'              => esc_html__('Columns', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SELECT,
                'default'            => '2',
                'tablet_default'     => '2',
                'mobile_default'     => '1',
                'options'            => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ],
                'frontend_available' => true,
            ]
        );

        // $this->add_control(
        //     'posts',
        //     [
        //         'label'   => esc_html__('Posts Per Page', 'bdthemes-element-pack'),
        //         'type'    => Controls_Manager::NUMBER,
        //         'default' => 4,
        //     ]
        // );

        $this->add_control(
            'show_pagination',
            [
                'label' => esc_html__('Pagination', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_responsive_control(
            'item_gap',
            [
                'label'     => esc_html__('Column Gap', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 35,
                ],
                'range'     => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 100,
                        'step' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-grid > .bdt-grid'     => 'margin-left: -{{SIZE}}px',
                    '{{WRAPPER}} .bdt-testimonial-grid > .bdt-grid > *' => 'padding-left: {{SIZE}}px',
                ],
            ]
        );

        $this->add_responsive_control(
            'row_gap',
            [
                'label'     => esc_html__('Row Gap', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 35,
                ],
                'range'     => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 100,
                        'step' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-grid > .bdt-grid'     => 'margin-top: -{{SIZE}}px',
                    '{{WRAPPER}} .bdt-testimonial-grid > .bdt-grid > *' => 'margin-top: {{SIZE}}px',
                ],
            ]
        );

        $this->add_control(
            'show_image',
            [
                'label'   => esc_html__('Testimonial Image', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label'   => esc_html__('Title', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_address',
            [
                'label'   => esc_html__('Address', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'meta_multi_line',
            [
                'label'   => esc_html__('Meta Multiline', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_comma',
            [
                'label' => esc_html__('Show Comma After Title', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'show_text',
            [
                'label'   => esc_html__('Text', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'text_limit',
            [
                'label'       => esc_html__('Text Limit', 'bdthemes-element-pack'),
                'description' => esc_html__('It\'s just work for main content, but not working with excerpt. If you set 0 so you will get full main content.', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::NUMBER,
                'default'     => 25,
                'condition'   => [
                    'show_text' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'strip_shortcode',
            [
                'label'   => esc_html__('Strip Shortcode', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition'   => [
                    'show_text' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_rating',
            [
                'label'   => esc_html__('Rating', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_review_platform',
            [
                'label'   => esc_html__('Review Platform', 'bdthemes-element-pack') . BDTEP_NC,
                'type'    => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'show_filter_bar',
            [
                'label' => esc_html__('Filter Bar', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
                'separator' => 'before'
            ]
        );

        $post_types = $this->getGroupControlQueryPostTypes();

        foreach ($post_types as $key => $post_type) {
            $taxonomies = $this->get_taxonomies($key);
            if (!$taxonomies[$key]) {
                continue;
            }
            $this->add_control(
                'taxonomy_' . $key,
                [
                    'label'     => __('Taxonomies', 'bdthemes-element-pack'),
                    'type'      => Controls_Manager::SELECT,
                    'options'   => $taxonomies[$key],
                    'default'   => key($taxonomies[$key]),
                    'condition' => [
                        'posts_source' => $key,
                        'show_filter_bar' => 'yes'
                    ],
                ]
            );
        }

        $this->add_control(
            'item_match_height',
            [
                'label' => esc_html__('Item Match Height', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'item_masonry',
            [
                'label' => esc_html__('Masonry', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->end_controls_section();

        //New Query Builder Settings
        $this->start_controls_section(
            'section_post_query_builder',
            [
                'label' => __('Query', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->register_query_builder_controls();

        $this->update_control(
            'posts_source',
            [
                'label'     => __('Source', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'options'   => $this->getGroupControlQueryPostTypes(),
                'default'   => 'bdthemes-testimonial',

            ]
        );
        $this->update_control(
            'posts_per_page',
            [
                'default' => 4,
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_item',
            [
                'label' => esc_html__('Item', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_item_style');

        $this->start_controls_tab(
            'tab_item_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'item_background',
            [
                'label'     => esc_html__('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-item-inner' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'item_border',
                'label'       => esc_html__('Border Color', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-item-inner',
                'separator'   => 'before',
            ]
        );

        $this->add_responsive_control(
            'item_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-item-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'item_shadow',
                'selector' => '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-item-inner',
            ]
        );

        $this->add_responsive_control(
            'item_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-item-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_item_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'item_hover_background',
            [
                'label'     => esc_html__('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-item-inner:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'item_hover_border_color',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'item_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-item-inner:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'item_hover_shadow',
                'selector' => '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-item-inner:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_image',
            [
                'label'     => esc_html__('Image', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_image' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'image_border',
                'label'       => esc_html__('Border Color', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-img-wrapper',
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'image_hover_border_color',
            [
                'label'     => esc_html__('Hover Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'image_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-img-wrapper:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-img-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_margin',
            [
                'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-img-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_size',
            [
                'label' => esc_html__('Size', 'bdthemes-element-pack') . BDTEP_NC,
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-img-wrapper' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_title',
            [
                'label'     => esc_html__('Title', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-title',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_address',
            [
                'label'     => esc_html__('Address', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_address' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'address_color',
            [
                'label'     => esc_html__('Company Name/Address Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-address' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'address_margin',
            [
                'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-address' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'address_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-address',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_text',
            [
                'label'     => esc_html__('Text', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_text' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'text_margin',
            [
                'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'text_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-text',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_rating',
            [
                'label'     => esc_html__('Rating', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_rating' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'original_color',
            [
                'label'   => esc_html__('Enable Original Color', 'bdthemes-element-pack') . BDTEP_NC,
                'type'    => Controls_Manager::SWITCHER,
                'condition' => [
                    'show_review_platform' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'rating_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#e7e7e7',
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-grid .bdt-rating .bdt-rating-item' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'original_color' => ''
                ]
            ]
        );

        $this->add_control(
            'active_rating_color',
            [
                'label'     => esc_html__('Active Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#FFCC00',
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-grid .bdt-rating.bdt-rating-1 .bdt-rating-item:nth-child(1)'    => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-testimonial-grid .bdt-rating.bdt-rating-2 .bdt-rating-item:nth-child(-n+2)' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-testimonial-grid .bdt-rating.bdt-rating-3 .bdt-rating-item:nth-child(-n+3)' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-testimonial-grid .bdt-rating.bdt-rating-4 .bdt-rating-item:nth-child(-n+4)' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-testimonial-grid .bdt-rating.bdt-rating-5 .bdt-rating-item:nth-child(-n+5)' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'original_color' => ''
                ]
            ]
        );

        $this->add_responsive_control(
            'rating_margin',
            [
                'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-testimonial-grid .bdt-rating' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'rating_size',
            [
                'label' => esc_html__('Size', 'bdthemes-element-pack') . BDTEP_NC,
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-widget-container .bdt-rating .bdt-rating-item' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'rating_spacing',
            [
                'label' => esc_html__('Spacing', 'bdthemes-element-pack') . BDTEP_NC,
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-widget-container .bdt-rating .bdt-rating-item' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // FILTER Bar Style
        $this->register_style_controls_filter();

        $this->start_controls_section(
            'section_style_review_platform',
            [
                'label'      => __('Review Platform', 'bdthemes-element-pack'),
                'tab'        => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_review_platform' => 'yes'
                ],
            ]
        );

        $this->start_controls_tabs('tabs_platform_style');

        $this->start_controls_tab(
            'tab_platform_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'platform_text_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-review-platform i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'platform_background_color',
                'selector' => '{{WRAPPER}} .bdt-review-platform',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'platform_border',
                'label'       => esc_html__('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-review-platform',
            ]
        );

        $this->add_responsive_control(
            'platform_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-review-platform' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'platform_text_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-review-platform' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'platform_text_margin',
            [
                'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-review-platform' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'platform_shadow',
                'selector' => '{{WRAPPER}} .bdt-review-platform',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'platform_typography',
                'selector' => '{{WRAPPER}} .bdt-review-platform',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_platform_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'platform_hover_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-review-platform:hover i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'platform_background_hover_color',
                'selector' => '{{WRAPPER}} .bdt-review-platform:hover',

            ]
        );

        $this->add_control(
            'platform_hover_border_color',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'platform_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-review-platform:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_pagination',
            [
                'label'     => esc_html__('Pagination', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_pagination' => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_pagination_style');

        $this->start_controls_tab(
            'tab_pagination_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'pagination_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ul.bdt-pagination li a, {{WRAPPER}} ul.bdt-pagination li span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'pagination_background',
                'selector'  => '{{WRAPPER}} ul.bdt-pagination li a',
                'separator' => 'after',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'pagination_border',
                'label'    => esc_html__('Border', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} ul.bdt-pagination li a',
            ]
        );

        $this->add_responsive_control(
            'pagination_offset',
            [
                'label'     => esc_html__('Offset', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-pagination' => 'margin-top: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_responsive_control(
            'pagination_space',
            [
                'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-pagination'     => 'margin-left: {{SIZE}}px;',
                    '{{WRAPPER}} .bdt-pagination > *' => 'padding-left: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_responsive_control(
            'pagination_padding',
            [
                'label'     => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} ul.bdt-pagination li a' => 'padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );

        $this->add_responsive_control(
            'pagination_radius',
            [
                'label'     => esc_html__('Radius', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} ul.bdt-pagination li a' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );

        $this->add_responsive_control(
            'pagination_arrow_size',
            [
                'label'     => esc_html__('Arrow Size', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} ul.bdt-pagination li a svg' => 'height: {{SIZE}}px; width: auto;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'pagination_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} ul.bdt-pagination li a, {{WRAPPER}} ul.bdt-pagination li span',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_pagination_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'pagination_hover_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ul.bdt-pagination li a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_hover_border_color',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ul.bdt-pagination li a:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'pagination_hover_background',
                'selector' => '{{WRAPPER}} ul.bdt-pagination li a:hover',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_pagination_active',
            [
                'label' => esc_html__('Active', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'pagination_active_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ul.bdt-pagination li.bdt-active a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_active_border_color',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ul.bdt-pagination li.bdt-active a' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'pagination_active_background',
                'selector' => '{{WRAPPER}} ul.bdt-pagination li.bdt-active a',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }


    public function get_taxonomies($post_type = '') {
        $_taxonomies = [];
        if ($post_type) {
            $taxonomies = get_taxonomies(['public' => true, 'object_type' => [$post_type]], 'object');
            $tax = array_diff_key(wp_list_pluck($taxonomies, 'label', 'name'), []);
            $_taxonomies[$post_type] = count($tax) !== 0 ? $tax : '';
        }
        return $_taxonomies;
    }

    public function filter_menu_terms() {

        $settings = $this->get_settings_for_display();
        $taxonomy = $settings['taxonomy_' . $settings['posts_source']];
        $categories = get_the_terms(get_the_ID(), $taxonomy);
        $_categories = [];
        if ($categories) {
            foreach ($categories as $category) {
                $_categories[$category->slug] = strtolower($category->slug);
            }
        }
        return implode(' ', $_categories);
    }
    protected function filter_menu_categories() {
        $settings = $this->get_settings_for_display();
        $include_Categories = $settings['posts_include_term_ids'];
        $exclude_Categories = $settings['posts_exclude_term_ids'];
        $post_options = [];
        if (isset($settings['taxonomy_' . $settings['posts_source']])) {
            $taxonomy = $settings['taxonomy_' . $settings['posts_source']];
            $params = [
                'taxonomy' => $taxonomy,
                'hide_empty' => true,
                'include' => $include_Categories,
                'exclude' => $exclude_Categories,
            ];
            $post_categories = get_terms($params);
            if (is_wp_error($post_categories)) {
                return $post_options;
            }
            if (false !== $post_categories and is_array($post_categories)) {
                foreach ($post_categories as $category) {
                    $post_options[$category->slug] = $category->name;
                }
            }
        }

        return $post_options;
    }


    public function render_query($posts_per_page) {
        $settings = $this->get_settings();
        $args = [];
        $args['posts_per_page'] = $posts_per_page;
        if ($settings['show_pagination']) {
            $args['paged']  = max(1, get_query_var('paged'), get_query_var('page'));
        }

        $default = $this->getGroupControlQueryArgs();
        $args = array_merge($default, $args);

        return $this->_query = new \WP_Query($args);
    }
    public function render_image($image_id) {
        $settings = $this->get_settings_for_display();

        if (!$settings['show_image']) {
            return;
        }

        $testimonial_thumb = wp_get_attachment_image_src(get_post_thumbnail_id($image_id), 'medium');

?>
        <div class="bdt-flex bdt-position-relative">
            <div class="bdt-testimonial-grid-img-wrapper bdt-overflow-hidden bdt-border-circle bdt-background-cover">
                <img src="<?php echo esc_url($testimonial_thumb[0]); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
            </div>
            <?php $this->render_review_platform(get_the_ID()); ?>
        </div>
    <?php
    }

    public function render_title($post_id) {
        $settings = $this->get_settings_for_display();
        if (!$settings['show_title']) {
            return;
        }

    ?>
        <h4 class="bdt-testimonial-grid-title bdt-margin-remove-bottom bdt-margin-remove-top"><?php echo esc_attr(get_the_title($post_id)); ?>
            <?php if ($settings['show_comma']) {
                echo (($settings['show_title']) and ($settings['show_address'])) ? ', ' : '';
            } ?></h4>
    <?php
    }

    public function render_address($post_id) {
        $settings = $this->get_settings_for_display();

        if (!$settings['show_address']) {
            return;
        }

    ?>
        <p class="bdt-testimonial-grid-address bdt-text-meta bdt-margin-remove">
            <?php echo get_post_meta($post_id, 'bdthemes_tm_company_name', true); ?>
        </p>
    <?php
    }

    public function render_excerpt() {

        if (!$this->get_settings('show_text')) {
            return;
        }

        $strip_shortcode = $this->get_settings_for_display('strip_shortcode');

    ?>
        <div class="bdt-testimonial-grid-text">
            <?php
            if (has_excerpt()) {
                the_excerpt();
            } else {
                echo element_pack_custom_excerpt($this->get_settings_for_display('text_limit'), $strip_shortcode);
            }
            ?>
        </div>
    <?php

    }

    public function render_review_platform($post_id) {
        $settings = $this->get_settings_for_display();

        if (!$settings['show_review_platform']) {
            return;
        }

        $platform = get_post_meta($post_id, 'bdthemes_tm_platform', true);
        $review_link = get_post_meta($post_id, 'bdthemes_tm_review_link', true);

        if (!$platform) {
            $platform = 'self';
        }

        if (!$review_link) {
            $review_link = '#';
        }

    ?>
        <a href="<?php echo $review_link; ?>" class="bdt-review-platform bdt-flex-inline" bdt-tooltip="<?php echo $platform; ?>">
            <i class="ep-icon-<?php echo strtolower($platform); ?> bdt-platform-icon bdt-flex bdt-flex-middle bdt-flex-center" aria-hidden="true"></i>
        </a>
    <?php
    }

    public function render_rating($post_id) {
        $settings = $this->get_settings_for_display();

        if (!$settings['show_rating']) {
            return;
        }

    ?>
        <div class="bdt-testimonial-grid-rating">
            <ul class="bdt-rating bdt-rating-<?php echo get_post_meta($post_id, 'bdthemes_tm_rating', true); ?> bdt-grid bdt-grid-collapse" data-bdt-grid>
                <li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
                <li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
                <li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
                <li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
                <li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
            </ul>
        </div>
    <?php
    }

    public function render_filter_menu() {
        $testi_categories = $this->filter_menu_categories(); ?>
        <div class="bdt-ep-grid-filters-wrapper">
            <button class="bdt-button bdt-button-default bdt-hidden@m" type="button"><?php esc_html_e('Filter', 'bdthemes-element-pack'); ?></button>
            <div data-bdt-dropdown="mode: click;" class="bdt-dropdown bdt-margin-remove-top bdt-margin-remove-bottom bdt-hidden@m">
                <ul class="bdt-nav bdt-dropdown-nav">
                    <li class="bdt-ep-grid-filter bdt-active" data-bdt-filter-control>
                        <a href="#"><?php esc_html_e('All', 'bdthemes-element-pack'); ?></a>
                    </li>
                    <?php foreach ($testi_categories as $category) { ?>
                        <li class="bdt-ep-grid-filter" data-bdt-filter-control="[data-filter*='<?php echo esc_attr(strtolower($category)); ?>']">
                            <a href="#"><?php echo esc_html($category); ?></a>
                        </li>
                    <?php } ?>
                </ul>
            </div>


            <ul class="bdt-ep-grid-filters bdt-visible@m" data-bdt-margin>
                <li class="bdt-ep-grid-filter bdt-active" data-bdt-filter-control>
                    <a href="#"><?php esc_html_e('All', 'bdthemes-element-pack'); ?></a>
                </li>
                <?php foreach ($testi_categories as $category) : ?>
                    <li class="bdt-ep-grid-filter" data-bdt-filter-control="[data-filter*='<?php echo esc_attr(strtolower($category)); ?>']">
                        <a href="#"><?php echo esc_html($category); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php
    }

    public function render_header() {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute('testimonial-grid-wrapper', 'class', ['bdt-testimonial-grid-layout-' . $settings['layout'], 'bdt-testimonial-grid', 'bdt-ep-grid-filter-container']);



        if ($settings['show_filter_bar']) {
            $this->add_render_attribute('testimonial-grid-wrapper', 'data-bdt-filter', 'target: #bdt-testimonial-grid-' . $this->get_id());
        }

    ?>
        <div <?php echo $this->get_render_attribute_string('testimonial-grid-wrapper'); ?>>

            <?php if ($settings['show_filter_bar']) {
                $this->render_filter_menu();
            }

            ?>

        <?php
    }

    public function render_footer() {
        ?>
        </div>
        <?php
    }



    public function render_loop_item() {
        $settings = $this->get_settings_for_display();

        // TODO need to delete after v6.5
        if (isset($settings['posts']) and $settings['posts_per_page'] == 4) {
            $limit = $settings['posts'];
        } else {
            $limit = $settings['posts_per_page'];
        }

        $wp_query = $this->render_query($limit);

        $this->add_render_attribute('testimonial-grid', 'data-bdt-grid', '');
        $this->add_render_attribute('testimonial-grid', 'class', 'bdt-grid');

        if ($settings['item_match_height']) {
            $this->add_render_attribute('testimonial-grid', 'data-bdt-height-match', 'div > .bdt-testimonial-grid-item-inner');
        }

        if ($settings['item_masonry']) {
            $this->add_render_attribute('testimonial-grid', 'data-bdt-grid', 'masonry: true;');
        }

        if ($wp_query->have_posts()) {


        ?>
            <div id="bdt-testimonial-grid-<?php echo $this->get_id(); ?>" <?php echo $this->get_render_attribute_string('testimonial-grid'); ?>>
                <?php

                while ($wp_query->have_posts()) : $wp_query->the_post();

                    $columns_mobile = isset($settings['columns_mobile']) ? $settings['columns_mobile'] : 1;
                    $columns_tablet = isset($settings['columns_tablet']) ? $settings['columns_tablet'] : 2;
                    $columns         = isset($settings['columns']) ? $settings['columns'] : 2;


                    $this->add_render_attribute('testimonial-grid-item' . get_the_Id(), 'class', 'bdt-width-1-' . esc_attr($columns_mobile));
                    $this->add_render_attribute('testimonial-grid-item' . get_the_Id(), 'class', 'bdt-width-1-' . esc_attr($columns_tablet) . '@s');
                    $this->add_render_attribute('testimonial-grid-item' . get_the_Id(), 'class', 'bdt-width-1-' . esc_attr($columns) . '@m');

                    $platform = get_post_meta(get_the_ID(), 'bdthemes_tm_platform', true);

                    $this->add_render_attribute('testimonial-grid-item' . get_the_Id(), 'class', 'bdt-testimonial-grid-item bdt-review-' . strtolower($platform));

                ?>
                    <?php if ($settings['show_filter_bar']) {
                        $this->add_render_attribute('testimonial-grid-item' . get_the_Id(), 'data-filter', $this->filter_menu_terms());
                    } ?>

                    <div <?php echo $this->get_render_attribute_string('testimonial-grid-item' . get_the_Id()); ?>>
                        <?php if ('1' == $settings['layout']) : ?>
                            <div class="bdt-testimonial-grid-item-inner">
                                <div class="bdt-grid bdt-position-relative bdt-grid-small bdt-flex-middle" data-bdt-grid>
                                    <?php $this->render_image(get_the_ID()); ?>
                                    <?php if ($settings['show_title'] || $settings['show_address']) : ?>
                                        <div class="bdt-testimonial-grid-title-address <?php echo ($settings['meta_multi_line']) ? 'bdt-meta-multi-line' : ''; ?>">
                                            <?php
                                            $this->render_title(get_the_ID());
                                            $this->render_address(get_the_ID());

                                            if ($settings['show_rating']) : ?>
                                                <?php if ('3' <= $settings['columns']) : ?>
                                                    <?php $this->render_rating(get_the_ID()); ?>
                                                <?php endif; ?>

                                                <?php if ('2' >= $settings['columns']) : ?>
                                                    <div class="bdt-position-center-right bdt-text-right">
                                                        <?php $this->render_rating(get_the_ID()); ?>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php $this->render_excerpt(); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ('2' == $settings['layout']) : ?>
                            <div class="bdt-testimonial-grid-item-inner bdt-position-relative bdt-text-center">
                                <div class="bdt-position-relative bdt-flex-inline">
                                    <?php $this->render_image(get_the_ID()); ?>
                                </div>
                                <?php if ($settings['show_title'] || $settings['show_address']) : ?>
                                    <div class="bdt-testimonial-grid-title-address <?php echo ($settings['meta_multi_line']) ? 'bdt-meta-multi-line' : ''; ?>">
                                        <?php
                                        $this->render_title(get_the_ID());
                                        $this->render_address(get_the_ID());
                                        ?>
                                    </div>
                                <?php endif; ?>
                                <?php $this->render_excerpt(); ?>
                                <?php $this->render_rating(get_the_ID()); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ('3' == $settings['layout']) : ?>
                            <div class="bdt-testimonial-grid-item-inner">
                                <?php $this->render_excerpt(); ?>
                                <div class="bdt-grid bdt-position-relative bdt-grid-small bdt-flex-middle" data-bdt-grid>
                                    <?php $this->render_image(get_the_ID()); ?>
                                    <?php if ($settings['show_title'] || $settings['show_address']) : ?>
                                        <div class="bdt-testimonial-grid-title-address <?php echo ($settings['meta_multi_line']) ? 'bdt-meta-multi-line' : ''; ?>">
                                            <?php
                                            $this->render_title(get_the_ID());
                                            $this->render_address(get_the_ID());

                                            if ($settings['show_rating']) : ?>
                                                <?php if ('3' <= $settings['columns']) : ?>
                                                    <?php $this->render_rating(get_the_ID()); ?>
                                                <?php endif; ?>

                                                <?php if ('2' >= $settings['columns']) : ?>
                                                    <div class="bdt-position-center-right bdt-text-right">
                                                        <?php $this->render_rating(get_the_ID()); ?>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>

            </div>

            <?php
            if ($settings['show_pagination']) { ?>
                <div class="ep-pagination">
                    <?php element_pack_post_pagination($wp_query); ?>
                </div>
<?php
            }

            wp_reset_postdata();
        } else {
            echo '<div class="bdt-alert-warning" bdt-alert>Oppps!! There is no post, please select actual post or categories.<div>';
        }
    }

    public function render() {
        $this->render_header();
        $this->render_loop_item();
        $this->render_footer();
    }
}
