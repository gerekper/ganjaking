<?php

namespace ElementPack\Modules\LearnPressGrid\Widgets;

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

class Learnpress_Grid extends Module_Base {
    use Global_Widget_Controls;
    use Group_Control_Query;

    /**
     * @var \WP_Query
     */
    private $_query = null;
    public function get_name() {
        return 'bdt-learnpress-grid';
    }

    public function get_title() {
        return esc_html__('LearnPress Grid', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-learnpress-grid bdt-new';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['learnpress', 'lms', 'course', 'learning', 'management', 'grid'];
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-font', 'ep-learnpress-grid'];
        }
    }


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
        $this->render_style_controls_instructor();
        $this->render_style_controls_meta();
        $this->render_style_controls_price();
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
        $this->add_control(
            'layout_type',
            [
                'label'          => esc_html__('Layout Type', 'bdthemes-element-pack'),
                'type'           => Controls_Manager::SELECT,
                'default'        => 'grid',
                'options'        => [
                    'grid' => esc_html__('Grid', 'bdthemes-element-pack'),
                    'list' => esc_html__('List', 'bdthemes-element-pack'),
                ]
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
                    '{{WRAPPER}} .ep-learnpress-lms-wrap' => 'grid-template-columns: repeat({{VALUE}}, 1fr)'
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
                    '{{WRAPPER}} .ep-learnpress-lms-wrap' => 'grid-column-gap: {{SIZE}}px;',
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
                    '{{WRAPPER}} .ep-learnpress-lms-wrap' => 'grid-row-gap: {{SIZE}}px;',
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
                'default'       => 'left',
                'selectors' => [
                    '{{WRAPPER}} .ep-learnpress-content-wrap' => 'text-align:{{VALUE}}'
                ],
                'toggle' => false,
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
                'default' => 'yes'
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
            'show_instructor',
            [
                'label'     => esc_html__('Instructor', 'bdthemes-element-pack'),
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
            'show_meta',
            [
                'label'   => esc_html__('Meta', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'show_level',
            [
                'label'   => esc_html__('Level', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before',
                'condition' => [
                    'show_meta' => 'yes'
                ]
            ]
        );
        $this->add_control(
            'show_lessons',
            [
                'label'   => esc_html__('Lessons', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'show_meta' => 'yes'
                ]
            ]
        );
        $this->add_control(
            'show_quizzes',
            [
                'label'   => esc_html__('Quizzes', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'show_meta' => 'yes'
                ]

            ]
        );
        $this->add_control(
            'show_student',
            [
                'label'   => esc_html__('Student', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'show_meta' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'show_duration',
            [
                'label'   => esc_html__('Duration', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before'

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
                'default'   => 'lp_course',
                'options' => [
                    'lp_course' => "LearnPress Courses",
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
                    'post_type' => 'lp_course'
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
                'selector'  => '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-item',
            ]
        );
        $this->add_responsive_control(
            'item_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'item_border',
                'label'       => esc_html__('Border Color', 'bdthemes-element-pack'),
                'selector'    => '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-item',
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
                    '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'item_shadow',
                'selector' => '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-item',
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
                'selector'  => '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-item:hover',
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
                    '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-item:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'item_hover_shadow',
                'selector' => '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-item:hover',
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
                    '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hover_title_color',
            [
                'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-title a:hover' => 'color: {{VALUE}};',
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
                    '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-title a',
            ]
        );

        $this->end_controls_section();
    }
    public function render_style_controls_instructor() {
        $this->start_controls_section(
            'section_style_instructor',
            [
                'label'     => esc_html__('Instructor', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_instructor' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'instructor_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-instructor a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hover_instructor_color',
            [
                'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-instructor a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'instructor_margin',
            [
                'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-instructor' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'instructor_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-instructor a',
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
                    '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-category a' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'category_background_color',
                'label'     => __('Background', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-lms-wrap .ep-learnpress-category a',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'           => 'category_border',
                'label'          => __('Border', 'bdthemes-element-pack'),
                'selector'       => '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-category a',
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
                    '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-category a'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-category' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'category_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-category a',
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'category_shadow',
                'selector' => '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-category a',
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
                    '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-category a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'hover_category_bg_color',
                'label'     => __('Background', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-lms-wrap .ep-learnpress-category a:hover',
            ]
        );
        $this->add_control(
            'hover_category_border_color',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-category a:hover' => 'border-color: {{VALUE}};',
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

        $this->start_controls_tabs(
            'tabs_style_price'
        );
        $this->start_controls_tab(
            'tab_style_price_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'price_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-price' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'price_background',
                'label'     => __('Background', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-price',
            ]
        );

        $this->add_responsive_control(
            'price_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-price' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before'
            ]
        );
        $this->add_responsive_control(
            'price_margin',
            [
                'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'      => 'price_border',
                'label'     => __('Border', 'bdthemes-element-pack'),
                'selector'  => '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-price',
            ]
        );

        $this->add_responsive_control(
            'price_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-price' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'price_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-price',
            ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
            'tab_style_price_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'price_hover_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-price:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'price_hover_background',
                'label'     => __('Background', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-price:hover',
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
    }
    public function render_style_controls_meta() {
        $this->start_controls_section(
            'section_style_meta',
            [
                'label'     => esc_html__('Meta', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_meta' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'meta_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-meta-item' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'meta_separator_color',
            [
                'label'     => esc_html__('Separator Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-item .bdt-divider' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'meta_column_spacing',
            [
                'label'         => __('Column Spacing', 'bdthemes-element-pack'),
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
                    '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-meta-wrap' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'meta_row_spacing',
            [
                'label'         => __('Row Spacing', 'bdthemes-element-pack'),
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
                    '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-meta-wrap' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'meta_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .ep-learnpress-grid .ep-learnpress-meta-item',
            ]
        );
        $this->end_controls_section();
    }
    public function render_style_controls_button() {
        $this->start_controls_section(
            'section_style_button',
            [
                'label'     => esc_html__('Button', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_cart' => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_button_style');

        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-products .bdt-wc-add-to-cart a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'background_color',
            [
                'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-products .bdt-wc-add-to-cart a' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'border',
                'label'       => esc_html__('Border', 'bdthemes-element-pack'),
                'placehr' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-wc-products .bdt-wc-add-to-cart a',
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-wc-products .bdt-wc-add-to-cart a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'button_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-wc-products .bdt-wc-add-to-cart a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'button_fullwidth',
            [
                'label'     => esc_html__('Fullwidth Button', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SWITCHER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-products .bdt-wc-add-to-cart a' => 'width: 100%;',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'button_shadow',
                'selector' => '{{WRAPPER}} .bdt-wc-products .bdt-wc-add-to-cart a',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'button_typography',
                'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector'  => '{{WRAPPER}} .bdt-wc-products .bdt-wc-add-to-cart a',
                'separator' => 'before',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'hover_color',
            [
                'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-products .bdt-wc-add-to-cart a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background_hover_color',
            [
                'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-products .bdt-wc-add-to-cart a:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_border_color',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-products .bdt-wc-add-to-cart a:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

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
            $terms = get_the_terms(get_the_ID(), 'course_category');
            if (!empty($terms)) {
                foreach ($terms as $term) {
                    $product_categories[] = esc_attr($term->slug);
                };
            }
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
            <div data-bdt-dropdown="mode: click; boundary: !.bdt-ep-grid-filters-wrapper; flip:false;" class="bdt-dropdown bdt-margin-remove-top bdt-margin-remove-bottom">
                <ul class="bdt-nav bdt-dropdown-nav">

                    <li class="bdt-ep-grid-filter bdt-active" data-bdt-filter-control>
                        <a href="#"><?php esc_html_e('All Courses', 'bdthemes-element-pack'); ?></a>
                    </li>

                    <?php foreach ($product_categories as $product_category => $value) : ?>
                        <?php $filter_name = get_term_by('slug', $value, 'course_category'); ?>
                        <li class="bdt-ep-grid-filter" data-bdt-filter-control="[data-filter*='bdtf-<?php echo esc_attr(trim($value)); ?>']">
                            <a href="#"><?php echo esc_html($filter_name->name); ?></a>
                        </li>
                    <?php endforeach; ?>

                </ul>
            </div>


            <ul class="bdt-ep-grid-filters bdt-visible@m" data-bdt-margin>
                <li class="bdt-ep-grid-filter bdt-active" data-bdt-filter-control>
                    <a href="#"><?php esc_html_e('All Courses', 'bdthemes-element-pack'); ?></a>
                </li>

                <?php foreach ($product_categories as $product_category => $value) : ?>
                    <?php $filter_name = get_term_by('slug', $value, 'course_category'); ?>
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
        $this->add_render_attribute('learnpress-grid', 'class', ['ep-learnpress-grid', 'ep-edd-content-position-' . $settings['alignment'] . ''], true);
        if ($settings['show_filter_bar']) {
            $this->add_render_attribute('learnpress-grid', 'data-bdt-filter', 'target: #ep-learnpress-grid-' . $this->get_id());
        } ?>

        <div <?php $this->print_render_attribute_string('learnpress-grid'); ?>>
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
            $id       = 'ep-learnpress-grid-' . $this->get_id();
            $this->query_product();
            $wp_query = $this->get_query();

            if ($wp_query->have_posts()) {


                $this->add_render_attribute(['learnpress-wrapper' => ['class' => ['ep-learnpress-lms-wrap ep-learnpress-lms-' . $settings['layout_type'] . ''], 'id' => esc_attr($id),],]); ?>
            <div <?php $this->print_render_attribute_string('learnpress-wrapper'); ?>>
                <?php while ($wp_query->have_posts()) {
                    $wp_query->the_post();
                    $course = learn_press_get_course(get_the_ID());
                    $lessons  = $course->count_items(LP_LESSON_CPT);
                    $quizzes  = $course->count_items(LP_QUIZ_CPT);
                    $students = $course->count_students();
                    $level = learn_press_get_post_level(get_the_ID());

                    if ($settings['show_filter_bar'] === 'yes') {
                        $terms = get_the_terms(get_the_ID(), 'course_category');
                        $product_filter_cat = [];
                        if (!empty($terms)) {
                            foreach ($terms as $term) {
                                $product_filter_cat[] = 'bdtf-' . esc_attr($term->slug);
                            };
                        }
                        $this->add_render_attribute('learnpress-item', 'data-filter', implode(' ', $product_filter_cat), true);
                    }
                    $this->add_render_attribute('learnpress-item', 'class', 'ep-learnpress-item', true);
                ?>
                    <div <?php $this->print_render_attribute_string('learnpress-item'); ?>>
                        <a class="ep-learnpress-image-wrap" href="<?php the_permalink(); ?>">
                            <img src="<?php echo wp_get_attachment_image_url(get_post_thumbnail_id(), $settings['image_size']); ?>" alt="<?php echo get_the_title(); ?>">
                        </a>

                        <div class="ep-learnpress-content-wrap">
                            <?php if ($settings['show_categories']) : ?>
                                <div class="ep-learnpress-category">
                                    <?php
                                    echo get_the_term_list(get_the_ID(), 'course_category');
                                    ?>
                                </div>
                            <?php
                            endif; ?>
                            <?php if ($settings['show_instructor']) : ?>
                                <div class="ep-learnpress-instructor">
                                    <?php echo wp_kses_post($course->get_instructor_html()); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ('yes' === $settings['show_title']) :
                                printf('<%1$s class="ep-learnpress-title"><a href="%2$s">%3$s</a></%1$s>', $settings['title_tags'], esc_url(get_the_permalink()), esc_html(get_the_title()));
                            endif;
                            ?>

                            <?php if ('yes' === $settings['show_meta']) : ?>
                                <div class="ep-learnpress-meta-wrap">
                                    <?php if ('yes' === $settings['show_level']) : ?>
                                        <div class="ep-learnpress-meta-item ep-learnpress-meta-item-level">
                                            <span>
                                                <i class="ep-learnpress-meta-icon ep-icon-bar"></i>
                                                <?php esc_html_e($level, 'bdthemes-element-pack'); ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ('yes' === $settings['show_lessons']) : ?>
                                        <div class="ep-learnpress-meta-item ep-learnpress-meta-item-lesson">
                                            <span class="ep-learnpress-meta-number">
                                                <i class="ep-learnpress-meta-icon ep-icon-copy"></i>
                                                <?php esc_html_e($lessons . '&nbsp;lessions', 'bdthemes-element-pack'); ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ('yes' === $settings['show_quizzes']) : ?>
                                        <div class="ep-learnpress-meta-item ep-learnpress-meta-item-quiz">
                                            <span class="ep-learnpress-meta-number">
                                                <i class="ep-learnpress-meta-icon ep-icon-puzzle"></i>
                                                <?php esc_html_e($quizzes . '&nbsp;quizzes', 'bdthemes-element-pack'); ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ('yes' === $settings['show_student']) : ?>
                                        <div class="ep-learnpress-meta-item ep-learnpress-meta-item-student">
                                            <span class="ep-learnpress-meta-number">
                                                <i class="ep-learnpress-meta-icon ep-icon-graduation"></i>
                                                <?php esc_html_e($students . '&nbsp;students', 'bdthemes-element-pack'); ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>


                            <div class="bdt-divider"></div>
                            <div class="bdt-bottom-content">
                                <?php if ('yes' === $settings['show_duration']) : ?>
                                    <div class="ep-learnpress-meta-item bdt-clock">
                                        <i class="ep-icon-clock"></i>
                                        <span><?php echo learn_press_get_post_translated_duration(get_the_ID(), esc_html__('Lifetime access', 'learnpress')); ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if ('yes' === $settings['show_price']) :
                                    printf('<div class="ep-learnpress-price">%1$s</div>', $course->get_course_price_html());
                                endif; ?>

                            </div>
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
            $args['post_type'] = 'lp_course';
            $args['posts_per_page'] = $settings['posts_per_page'];

            $default = $this->getGroupControlQueryArgs();
            $args = array_merge($default, $args);
            $this->_query =  new WP_Query($args);
        }
    }
