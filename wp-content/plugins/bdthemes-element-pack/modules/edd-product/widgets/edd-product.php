<?php

namespace ElementPack\Modules\EddProduct\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;

use ElementPack\Traits\Global_Widget_Controls;
use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;
use WP_Query;

if (!defined('ABSPATH')) {
    exit;
}

// Exit if accessed directly

class EDD_Product extends Module_Base {
    use Global_Widget_Controls;
    use Group_Control_Query;

    /**
     * @var \WP_Query
     */
    private $_query = null;
    public function get_name() {
        return 'bdt-edd-product';
    }

    public function get_title() {
        return esc_html__('EDD Product', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-edd-product bdt-new';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['product', 'easy', 'digital', 'download', 'edd', 'grid', 'ecommerce'];
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['element-pack-font', 'ep-edd-product'];
        }
    }

    // public function get_custom_help_url() {
    //     return 'https://youtu.be/3VkvEpVaNAM';
    // }

    public function get_query() {
        return $this->_query;
    }
    protected function register_controls() {
        /**
         * ! render controls layout
         */
        $this->render_controls_layout();
        $this->render_controls_query();
        $this->render_controls_additional();

        /**
         * ! render style controls
         */
        $this->render_style_controls_item();
        $this->render_style_controls_category();
        $this->render_style_controls_title();
        $this->render_style_controls_price();
        $this->render_style_controls_action_btn();
        $this->register_style_controls_filter();
        $this->render_style_controls_pagination();
    }

    /**
     * ! render layout controls
     */
    public function render_controls_layout() {
        $this->start_controls_section(
            'section_woocommerce_layout',
            [
                'label' => esc_html__('Layout', 'bdthemes-element-pack'),
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label'          => esc_html__('Columns', 'bdthemes-element-pack'),
                'type'           => Controls_Manager::SELECT,
                'default'        => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options'        => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ep-edd-product  .ep-edd-product-wrapper' => 'grid-template-columns: repeat({{VALUE}}, 1fr)'
                ]
            ]
        );

        $this->add_responsive_control(
            'items_columns_gap',
            [
                'label'     => esc_html__('Columns Gap', 'ultimate-wook'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 30,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ep-edd-product .ep-edd-product-wrapper' => 'grid-column-gap: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_responsive_control(
            'items_row_gap',
            [
                'label'     => esc_html__('Row Gap', 'ultimate-wook'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 30,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ep-edd-product .ep-edd-product-wrapper' => 'grid-row-gap: {{SIZE}}px;',
                ],
            ]
        );
        $this->add_control(
            'alignment',
            [
                'label'         => __('Alignment', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::CHOOSE,
                'options'       => [
                    'left'      => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-left',
                    ],
                    'center'    => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-center',
                    ],
                    'right'     => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-right',
                    ],
                ],
                'default'       => 'center',
                'selectors' => [
                    '{{WRAPPER}} .ep-edd-product .ep-edd-content' => 'text-align:{{VALUE}}'
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'      => 'image',
                'label'     => esc_html__('Image Size', 'bdthemes-element-pack'),
                'exclude'   => ['custom'],
                'default'   => 'medium',
            ]
        );

        $this->add_control(
            'show_filter_bar',
            [
                'label' => esc_html__('Show Filter', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'active_hash',
            [
                'label'       => esc_html__('Hash Location', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SWITCHER,
                'default'     => 'no',
                'condition' => [
                    'show_filter_bar' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'hash_top_offset',
            [
                'label'     => esc_html__('Top Offset ', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'size_units' => ['px', ''],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                        'step' => 5,
                    ],

                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 70,
                ],
                'condition' => [
                    'active_hash' => 'yes',
                    'show_filter_bar' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'hash_scrollspy_time',
            [
                'label'     => esc_html__('Scrollspy Time', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'size_units' => ['ms', ''],
                'range' => [
                    'px' => [
                        'min' => 500,
                        'max' => 5000,
                        'step' => 1000,
                    ],
                ],
                'default'   => [
                    'unit' => 'px',
                    'size' => 1000,
                ],
                'condition' => [
                    'active_hash' => 'yes',
                    'show_filter_bar' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'show_pagination',
            [
                'label'     => esc_html__('Pagination', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SWITCHER,
            ]
        );
        $this->end_controls_section();
    }
    public function render_controls_additional() {
        $this->start_controls_section(
            'section_edd_additional',
            [
                'label' => esc_html__('Additional', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'show_categories',
            [
                'label'     => esc_html__('Categories', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SWITCHER,
                'default' => 'yes',
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
            'title_tags',
            [
                'label'   => __('Title HTML Tag', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'h2',
                'options' => element_pack_title_tags(),
                'condition' => [
                    'show_title' => 'yes'
                ]
            ]
        );


        $this->add_control(
            'show_price',
            [
                'label'   => esc_html__('Price', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );




        // $this->add_control(
        //     'grid_animation_type',
        //     [
        //         'label'   => esc_html__('Grid Entrance Animation', 'bdthemes-element-pack'),
        //         'type'    => Controls_Manager::SELECT,
        //         'default' => '',
        //         'options' => element_pack_transition_options(),
        //         'separator' => 'before',
        //     ]
        // );

        // $this->add_control(
        //     'grid_anim_delay',
        //     [
        //         'label'      => esc_html__('Animation delay', 'bdthemes-element-pack'),
        //         'type'       => Controls_Manager::SLIDER,
        //         'size_units' => ['ms', ''],
        //         'range'      => [
        //             'ms' => [
        //                 'min'  => 0,
        //                 'max'  => 1000,
        //                 'step' => 5,
        //             ],
        //         ],
        //         'default'    => [
        //             'unit' => 'ms',
        //             'size' => 300,
        //         ],
        //         'condition' => [
        //             'grid_animation_type!' => '',
        //         ],
        //     ]
        // );
        $this->end_controls_section();
    }
    public function render_controls_query() {
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
                'type'      => Controls_Manager::SELECT,
                'default'   => 'download',
                'options' => [
                    'download' => "Download",
                    'manual_selection'   => __('Manual Selection', 'bdthemes-element-pack'),
                    'current_query'      => __('Current Query', 'bdthemes-element-pack'),
                    '_related_post_type' => __('Related', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->update_control(
            'posts_selected_ids',
            [
                'query_args'  => [
                    'query' => 'posts',
                    'post_type' => 'download'
                ],
            ]
        );
        $this->update_control(
            'posts_offset',
            [
                'label' => __('Offset', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::NUMBER,
                'default'   => 0,

            ]
        );

        $this->end_controls_section();
    }

    /**
     * ! render styles controls
     */

    public function render_style_controls_item() {
        $this->start_controls_section(
            'section_style_item',
            [
                'label'     => esc_html__('Item', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_item_style');

        $this->start_controls_tab(
            'tab_item_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'item_background',
                'label'     => __('Background', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .ep-edd-product .ep-edd-product-item',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'item_border',
                'label'       => esc_html__('Border Color', 'bdthemes-element-pack'),
                'selector'    => '{{WRAPPER}} .ep-edd-product .ep-edd-product-item',
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
                    '{{WRAPPER}} .ep-edd-product .ep-edd-product-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'item_shadow',
                'selector' => '{{WRAPPER}} .ep-edd-product .ep-edd-product-item',
            ]
        );

        $this->add_responsive_control(
            'item_padding',
            [
                'label'      => esc_html__('Item Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-edd-product .ep-edd-product-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_padding',
            [
                'label'      => esc_html__('Content Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-edd-product .ep-edd-product-item .ep-edd-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'item_hover_background',
                'label'     => __('Background', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .ep-edd-product .ep-edd-product-item:hover',
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
                    '{{WRAPPER}} .ep-edd-product .ep-edd-product-item:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'item_hover_shadow',
                'selector' => '{{WRAPPER}} .ep-edd-product .ep-edd-product-item:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    public function render_style_controls_title() {
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
                    '{{WRAPPER}} .ep-edd-product .ep-edd-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hover_title_color',
            [
                'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-edd-product .ep-edd-title a:hover' => 'color: {{VALUE}};',
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
                    '{{WRAPPER}} .ep-edd-product .ep-edd-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} .ep-edd-product .ep-edd-title a',
            ]
        );

        $this->end_controls_section();
    }

    public function render_style_controls_category() {
        $this->start_controls_section(
            'section_style_category',
            [
                'label'     => esc_html__('Category', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_categories' => 'yes',
                ],
            ]
        );
        $this->start_controls_tabs(
            'category_tabs'
        );
        $this->start_controls_tab(
            'category_tab_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'category_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-edd-product .ep-edd-category a' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'category_bg_color',
                'selector'  => '{{WRAPPER}} .ep-edd-product .ep-edd-category a',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'           => 'category_border',
                'label'          => __('Border', 'bdthemes-element-pack'),
                'selector'       => '{{WRAPPER}} .ep-edd-product .ep-edd-category a',
                'separator' => 'before'
            ]
        );
        $this->add_responsive_control(
            'category_radius',
            [
                'label'                 => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => ['px', '%', 'em'],
                'selectors'             => [
                    '{{WRAPPER}} .ep-edd-product .ep-edd-category a'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'category_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-edd-product .ep-edd-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'category_margin',
            [
                'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-edd-product .ep-edd-category' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'category_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .ep-edd-product .ep-edd-category a',
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'category_shadow',
                'selector' => '{{WRAPPER}} .ep-edd-product .ep-edd-category a',
            ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
            'category_tab_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'hover_category_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-edd-product .ep-edd-category a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'hover_category_bg_color',
                'selector'  => '{{WRAPPER}} .ep-edd-product .ep-edd-category a:hover',
            ]
        );
        $this->add_control(
            'hover_category_border_color',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-edd-product .ep-edd-category a:hover' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'category_border_border!' => ''
                ],
                'separator' => 'before'
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
    }
    public function render_style_controls_action_btn() {
        $this->start_controls_section(
            'style_action_btn',
            [
                'label' => esc_html__('Action Button', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->start_controls_tabs(
            'action_btn_tabs'
        );
        $this->start_controls_tab(
            'view_details_tab',
            [
                'label' => esc_html__('View Details', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'view_details_normal_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-edd-product .ep-details-button a' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'view_details_bg',
                'label'     => __('Title', 'plugin-domain'),
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .ep-edd-product .ep-details-button a',
            ]
        );
        $this->add_control(
            'heading_view_details_hover',
            [
                'label'     => esc_html__('Hover', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'view_details_hover_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-edd-product .ep-details-button a:hover' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'view_details_hover_bg',
                'label'     => __('Title', 'plugin-domain'),
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .ep-edd-product .ep-details-button a:hover',
                'separator' => 'after'
            ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
            'purchase_btn_tab',
            [
                'label' => esc_html__('Purchase', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'purchase_btn_normal_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-edd-product .ep-action-button .blue' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'purchase_btn_bg',
                'label'     => __('Title', 'plugin-domain'),
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .ep-edd-product .ep-action-button .blue',
            ]
        );
        $this->add_control(
            'heading_purchase_btn_hover',
            [
                'label'     => esc_html__('Hover', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'purchase_btn_hover_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-edd-product .ep-action-button .blue:hover' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'purchase_btn_hover_bg',
                'label'     => __('Title', 'plugin-domain'),
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .ep-edd-product .ep-action-button .blue:hover',
                'separator' => 'after'
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'      => 'action_btn_border',
                'label'     => esc_html__('Border', 'bdthemes-element-pack'),
                'selector'  => '{{WRAPPER}} .ep-edd-product .ep-action-button a',
                'separator' => 'before'
            ]
        );
        $this->add_responsive_control(
            'action_btn_radius',
            [
                'label'                 => esc_html__('Radius', 'bdthemes-element-pack'),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => ['px', '%', 'em'],
                'selectors'             => [
                    '{{WRAPPER}} .ep-edd-product .ep-action-button a'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'action_btn_padding',
            [
                'label'                 => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => ['px', '%', 'em'],
                'selectors'             => [
                    '{{WRAPPER}} .ep-edd-product .ep-action-button a'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'action_btn_margin',
            [
                'label'                 => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => ['px', '%', 'em'],
                'selectors'             => [
                    '{{WRAPPER}} .ep-edd-product .ep-action-button'    => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'action_btn_space_between',
            [
                'label'         => __('Space Between', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SLIDER,
                'size_units'    => ['px'],
                'range'         => [
                    'px'        => [
                        'min'   => 0,
                        'max'   => 50,
                        'step'  => 1,
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .ep-edd-product .ep-edd-product-item .ep-action-button' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'action_btn_typography',
                'label'     => __('Typography', 'bdthemes-element-pack'),
                'selector'  => '{{WRAPPER}} .ep-edd-product .ep-action-button a',
            ]
        );
        $this->end_controls_section();
    }
    public function render_style_controls_price() {
        $this->start_controls_section(
            'section_style_price',
            [
                'label'     => esc_html__('Price', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_price' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-edd-product .ep-edd-price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'price_margin',
            [
                'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-edd-product .ep-edd-price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'price_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} .ep-edd-product .ep-edd-price',
            ]
        );
        $this->end_controls_section();
    }


    public function render_style_controls_pagination() {
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
        $this->add_responsive_control(
            'pagination_spacing',
            [
                'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} ul.bdt-pagination'    => 'margin-top: {{SIZE}}px;',
                    '{{WRAPPER}} .dataTables_paginate' => 'margin-top: {{SIZE}}px;',
                ],
            ]
        );
        $this->add_control(
            'pagination_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ul.bdt-pagination li a'    => 'color: {{VALUE}};',
                    '{{WRAPPER}} ul.bdt-pagination li span' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .paginate_button'          => 'color: {{VALUE}} !important;',
                ],
            ]
        );
        $this->add_control(
            'active_pagination_color',
            [
                'label'     => esc_html__('Active Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ul.bdt-pagination li.bdt-active a' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .paginate_button.current'          => 'color: {{VALUE}} !important;',
                ],
            ]
        );
        $this->add_responsive_control(
            'pagination_margin',
            [
                'label'     => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} ul.bdt-pagination li a'    => 'margin: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                    '{{WRAPPER}} ul.bdt-pagination li span' => 'margin: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                    '{{WRAPPER}} .paginate_button'          => 'margin: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
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
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'pagination_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} ul.bdt-pagination li a, {{WRAPPER}} ul.bdt-pagination li span, {{WRAPPER}} .dataTables_paginate',
            ]
        );
        $this->end_controls_section();
    }
    /**
     * ! render content template
     */
    public function render_filter_menu() {
        $settings           = $this->get_settings_for_display();
        $product_categories = [];
        $this->query_product();
        $wp_query = $this->get_query();

        while ($wp_query->have_posts()) : $wp_query->the_post();
            $terms = get_the_terms(get_the_ID(), 'download_category');
            foreach ($terms as $term) {
                $product_categories[] = esc_attr($term->slug);
            };
        endwhile;

        wp_reset_postdata();

        $product_categories = array_unique($product_categories);
        $this->add_render_attribute(
            [
                'portfolio-gallery-hash-data' => [
                    'data-hash-settings' => [
                        wp_json_encode(
                            array_filter([
                                "id"       => 'bdt-products-' . $this->get_id(),
                                'activeHash'          => $settings['active_hash'],
                                'hashTopOffset'      => isset($settings['hash_top_offset']['size']) ? $settings['hash_top_offset']['size'] : 70,
                                'hashScrollspyTime' => isset($settings['hash_scrollspy_time']['size']) ? $settings['hash_scrollspy_time']['size'] : 1000,
                            ])
                        ),
                    ],
                ],
            ]
        ); ?>

        <div class="bdt-ep-grid-filters-wrapper" id="<?php echo 'bdt-products-' . $this->get_id(); ?>" <?php echo $this->get_render_attribute_string('portfolio-gallery-hash-data'); ?>>
            <button class="bdt-button bdt-button-default bdt-hidden@m" type="button"><?php esc_html_e('Filter', 'bdthemes-element-pack'); ?></button>
            <div data-bdt-dropdown="mode: click;" class="bdt-dropdown bdt-margin-remove-top bdt-margin-remove-bottom bdt-hidden@m">
                <ul class="bdt-nav bdt-dropdown-nav">

                    <li class="bdt-ep-grid-filter bdt-active" data-bdt-filter-control><a href="#"><?php esc_html_e('All Products', 'bdthemes-element-pack'); ?></a></li>

                    <?php foreach ($product_categories as $product_category => $value) : ?>
                        <?php $filter_name = get_term_by('slug', $value, 'download_category'); ?>
                        <li class="bdt-ep-grid-filter" data-bdt-filter-control="[data-filter*='bdtf-<?php echo esc_attr(trim($value)); ?>']">
                            <a href="#"><?php echo esc_html($filter_name->name); ?></a>
                        </li>
                    <?php endforeach; ?>

                </ul>
            </div>


            <ul class="bdt-ep-grid-filters bdt-visible@m" data-bdt-margin>
                <li class="bdt-ep-grid-filter bdt-active" data-bdt-filter-control><a href="#"><?php esc_html_e('All Products', 'bdthemes-element-pack'); ?></a></li>

                <?php foreach ($product_categories as $product_category => $value) : ?>
                    <?php $filter_name = get_term_by('slug', $value, 'download_category'); ?>
                    <li class="bdt-ep-grid-filter" data-bdt-filter-control="[data-filter*='bdtf-<?php echo esc_attr(trim($value)); ?>']">
                        <a href="#"><?php echo esc_html($filter_name->name); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php
    }
    public function render_header() {
        $settings = $this->get_settings_for_display();
        $this->add_render_attribute('ep-edd-product-grid', 'class', ['ep-edd-product', 'ep-edd-content-position-' . $settings['alignment'] . ''], true);
        if ($settings['show_filter_bar']) {
            $this->add_render_attribute('ep-edd-product-grid', 'data-bdt-filter', 'target: #bdt-edd-product-' . $this->get_id());
        } ?>

        <div <?php $this->print_render_attribute_string('ep-edd-product-grid'); ?>>
            <?php if ($settings['show_filter_bar']) {
                $this->render_filter_menu();
            }
        }
        public function render_footer() { ?>
        </div>
        <?php
        }

        public function render() {
            $this->render_header();
            $this->render_loop_item();
            $this->render_footer();
        }
        public function render_loop_item() {
            $settings = $this->get_settings_for_display();
            $id       = 'bdt-edd-product-' . $this->get_id();
            $this->query_product();
            $wp_query = $this->get_query();
            if ($wp_query->have_posts()) {

                $this->add_render_attribute(
                    [
                        'edd-products-wrapper' => [
                            'class' => [
                                'ep-edd-product-wrapper'
                            ],
                            'id' => esc_attr($id),
                        ],
                    ]
                );
        ?>
            <div <?php echo $this->get_render_attribute_string('edd-products-wrapper'); ?>>
                <?php
                while ($wp_query->have_posts()) {
                    $wp_query->the_post();
                    if ($settings['show_filter_bar']) {
                        $terms = get_the_terms(get_the_ID(), 'download_category');
                        $product_filter_cat = [];
                        foreach ($terms as $term) {
                            $product_filter_cat[] = 'bdtf-' . esc_attr($term->slug);
                        };
                        $this->add_render_attribute('edd-product-item', 'data-filter', implode(' ', $product_filter_cat), true);
                    }
                    $this->add_render_attribute('edd-product-item', 'class', 'ep-edd-product-item', true);
                ?>
                    <div <?php $this->print_render_attribute_string('edd-product-item'); ?>>
                        <div class="ep-edd-product-image-wrapper">
                            <div class="ep-edd-product-image">
                                <a href="<?php the_permalink(); ?>">
                                    <img src="<?php echo wp_get_attachment_image_url(get_post_thumbnail_id(), $settings['image_size']); ?>" alt="<?php echo get_the_title(); ?>">
                                </a>
                                <div class="ep-action-button">
                                    <?php if (function_exists('edd_price')) { ?>
                                        <?php if (!edd_has_variable_prices(get_the_ID())) { ?>
                                            <?php echo edd_get_purchase_link(get_the_ID(), 'Add to Cart', 'button'); ?>
                                        <?php } ?>
                                    <?php } ?>
                                    <div class="ep-details-button">
                                        <a href="<?php the_permalink(); ?>"><span><?php esc_html_e('View Details', 'bdthemes-element-pack'); ?></span></a>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div class="ep-edd-content">
                            <?php
                            if ($settings['show_categories']) :
                                $category_list = wp_get_post_terms(get_the_ID(), 'download_category');
                                foreach ($category_list as $term) {
                                    $term_link = get_term_link($term);
                                    echo '<span class="ep-edd-category"><a href="' . esc_url($term_link) . '">' . esc_html($term->name) . '</a></span> ';
                                }
                            endif;

                            if ($settings['show_title']) :
                                printf('<%1$s class="ep-edd-title"><a href="%2$s">%3$s</a></%1$s>', $settings['title_tags'], esc_url(get_the_permalink()), esc_html(get_the_title()));
                            endif;

                            if ($settings['show_price']) : ?>
                                <div class="ep-edd-price">
                                    <?php if (edd_has_variable_prices(get_the_ID())) {
                                        esc_html_e('Starting at: ', 'bdthemes-element-pack');
                                        edd_price(get_the_ID());
                                    } else {
                                        edd_price(get_the_ID());
                                    }
                                    ?>
                                </div>
                            <?php
                            endif; ?>

                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
            <?php
                if ($settings['show_pagination']) {
            ?>
                <div class="ep-pagination">
                    <?php element_pack_post_pagination($wp_query); ?>
                </div>
<?php
                    wp_reset_postdata();
                }
            }
        }
        public function query_product() {
            $settings = $this->get_settings_for_display();
            $args = [];
            if ($settings['show_pagination']) {
                $args['paged']  = max(1, get_query_var('paged'), get_query_var('page'));
            }
            $default = $this->getGroupControlQueryArgs();
            $args['post_type'] = 'download';
            $args['posts_per_page'] = $settings['posts_per_page'];
            $default = $this->getGroupControlQueryArgs();
            $args = array_merge($default, $args);
            $this->_query =  new WP_Query($args);
        }
    }
