<?php

namespace ElementPack\Modules\LearnpressCarousel\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;

use ElementPack\Traits\Global_Widget_Controls;
use ElementPack\Traits\Global_Swiper_Controls;
use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;
use WP_Query;

if (!defined('ABSPATH')) {
    exit;
}

// Exit if accessed directly

class Learnpress_Carousel extends Module_Base {
    use Global_Widget_Controls;
    use Global_Swiper_Controls;
    use Group_Control_Query;



    /**
     * @var \WP_Query
     */
    private $_query = null;
    public function get_name() {
        return 'bdt-learnpress-carousel';
    }

    public function get_title() {
        return BDTEP . esc_html__('LearnPress Carousel', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-learnpress-carousel bdt-new';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['learnpress', 'lms', 'course', 'learning', 'management', 'carousel'];
    }


    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-font', 'ep-learnpress-carousel'];
        }
    }
    public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-scripts'];
        } else {
            return ['ep-learnpress-carousel'];
        }
    }
    // public function get_custom_help_url() {
    //     return 'https://youtu.be/3VkvEpVaNAM';
    // }

    public function get_query() {
        return $this->_query;
    }


    protected function register_controls() {
        $this->render_controls_layout();
        $this->render_controls_query();
        $this->render_style_controls_item();
        $this->render_controls_additional();
        $this->render_style_controls_category();
        $this->render_style_controls_title();
        $this->render_style_controls_instructor();
        $this->render_style_controls_meta();
        $this->render_style_controls_price();
        $this->render_swiper_navigation();
    }

    public function render_controls_additional() {
        $this->start_controls_section(
            'section_additional',
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
    public function render_controls_layout() {
        $this->start_controls_section(
            'section_content_layout',
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
                'frontend_available' => true,
            ]
        );

        $this->add_responsive_control(
            'item_gap',
            [
                'label'   => esc_html__('Column Gap', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 35,
                ],
                'range' => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 100,
                        'step' => 5,
                    ],
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
                    '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-content-wrap' => 'text-align:{{VALUE}}'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'    => 'image',
                'label'   => esc_html__('Image Size', 'bdthemes-element-pack'),
                'exclude' => ['custom'],
                'default' => 'medium',
            ]
        );
        $this->end_controls_section();
    }
    public function render_swiper_navigation() {
        //Navigation Controls
        $this->start_controls_section(
            'section_content_navigation',
            [
                'label' => __('Navigation', 'bdthemes-element-pack'),
            ]
        );

        //Global Navigation Controls
        $this->register_navigation_controls();

        $this->end_controls_section();

        //Global Carousel Settings Controls
        $this->register_carousel_settings_controls();

        //Navigation Style
        $this->start_controls_section(
            'section_style_navigation',
            [
                'label'      => __('Navigation', 'bdthemes-element-pack'),
                'tab'        => Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'relation' => 'or',
                    'terms'    => [
                        [
                            'name'     => 'navigation',
                            'operator' => '!=',
                            'value'    => 'none',
                        ],
                        [
                            'name'  => 'show_scrollbar',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );

        //Global Navigation Style Controls
        $this->register_navigation_style_controls('ep-learnpress-carousel');

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
                'selector'  => '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-item',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'item_border',
                'label'       => esc_html__('Border Color', 'bdthemes-element-pack'),
                'selector'    => '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-item',
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
                    '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'item_shadow',
                'selector' => '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-item',
            ]
        );
        $this->add_responsive_control(
            'item_shadow_padding',
            [
                'label'       => __('Match Padding', 'bdthemes-element-pack'),
                'description' => __('You have to add padding for matching overlaping normal/hover box shadow when you used Box Shadow option.', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SLIDER,
                'range'       => [
                    'px' => [
                        'min'  => 0,
                        'step' => 1,
                        'max'  => 50,
                    ]
                ],
                'selectors'   => [
                    '{{WRAPPER}} .swiper-carousel' => 'padding: {{SIZE}}{{UNIT}}; margin: 0 -{{SIZE}}{{UNIT}};'
                ],
            ]
        );
        $this->add_responsive_control(
            'item_padding',
            [
                'label'      => esc_html__('Item Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-content-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                'selector'  => '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-item:hover',
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
                    '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-item:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'item_hover_shadow',
                'selector' => '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-item:hover',
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
                    '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hover_title_color',
            [
                'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-title a:hover' => 'color: {{VALUE}};',
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
                    '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-title a',
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
                    '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-category a' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'category_bg_color',
                'label'     => __('Background', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-category a',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'           => 'category_border',
                'label'          => __('Border', 'bdthemes-element-pack'),
                'selector'       => '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-category a',
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
                    '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-category a'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-category' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'category_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-category a',
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'category_shadow',
                'selector' => '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-category a',
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
                    '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-category a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'hover_category_bg_color',
                'label'     => __('Background', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-category a:hover',
            ]
        );

        $this->add_control(
            'hover_category_border_color',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-category a:hover' => 'border-color: {{VALUE}};',
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
                    '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-instructor a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hover_instructor_color',
            [
                'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-instructor a:hover' => 'color: {{VALUE}};',
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
                    '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-instructor' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'instructor_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-instructor a',
            ]
        );

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
                    '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-meta-item' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'meta_separator_color',
            [
                'label'     => esc_html__('Separator Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-item .bdt-divider' => 'background-color: {{VALUE}};',
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
                    '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-meta-wrap' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
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
                    '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-meta-wrap' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'meta_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-meta-item',
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
                    '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-price' => 'color: {{VALUE}};',
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
                    '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'price_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-price',
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
                //'scheme'    => Schemes\Typography::TYPOGRAPHY_4,
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


    public function render_header() {
        $settings = $this->get_settings_for_display();

        //Global Function
        $this->render_swiper_header_attribute('ep-learnpress-carousel');

        $this->add_render_attribute('carousel', 'class', ['ep-learnpress-carousel', 'ep-learnpress-lms-grid']); ?>
        <div <?php echo $this->get_render_attribute_string('carousel'); ?>>
            <div <?php echo $this->get_render_attribute_string('swiper'); ?>>
                <div class="swiper-wrapper">
                    <?php
                }

                public function render() {
                    $this->render_header();
                    $this->render_loop_item();
                    $this->render_footer();
                }
                public function render_loop_item() {
                    $settings = $this->get_settings_for_display();
                    $id       = 'ep-learnpress-carousel-' . $this->get_id();
                    $this->query_product();
                    $wp_query = $this->get_query();

                    if ($wp_query->have_posts()) {
                        while ($wp_query->have_posts()) {
                            $wp_query->the_post();
                            $course = learn_press_get_course(get_the_ID());
                            $lessons  = $course->count_items(LP_LESSON_CPT);
                            $quizzes  = $course->count_items(LP_QUIZ_CPT);
                            $students = $course->count_students();
                            $level = learn_press_get_post_level(get_the_ID());

                            $this->add_render_attribute('learnpress-item', 'class', ['ep-learnpress-item', 'swiper-slide'], true);
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
                            wp_reset_postdata();
                        }
                    }
                }
                public function query_product() {
                    $settings = $this->get_settings_for_display();
                    $args = [];
                    $default = $this->getGroupControlQueryArgs();
                    $args['post_type'] = 'lp_course';
                    $args['posts_per_page'] = $settings['posts_per_page'];
                    $default = $this->getGroupControlQueryArgs();
                    $args = array_merge($default, $args);
                    $this->_query =  new WP_Query($args);
                }
            }
