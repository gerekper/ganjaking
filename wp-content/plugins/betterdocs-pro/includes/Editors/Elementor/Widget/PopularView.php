<?php

namespace WPDeveloper\BetterDocsPro\Editors\Elementor\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use WPDeveloper\BetterDocs\Editors\Elementor\BaseWidget;

class PopularView extends BaseWidget {

    public function get_name() {
        return 'betterdocs-popular-view';
    }

    public function get_title() {
        return __( 'BetterDocs Popular Docs', 'betterdocs-pro' );
    }

    public function get_icon() {
        return 'betterdocs-icon-title';
    }

    public function get_categories() {
        return ['betterdocs-elements', 'docs-archive'];
    }

    public function get_keywords() {
        return ['betterdocs-elements', 'betterdocs-popular-view', 'betterdocs', 'docs'];
    }

    public function get_style_depends() {
        return ['betterdocs-el-articles-list'];
    }

    public function get_custom_help_url() {
        return 'https://betterdocs.co/docs/wordpress';
    }

    protected function register_controls() {
        /**
         * Query Popular Articles
         */
        $this->start_controls_section(
            'query_popular_articles',
            [
                'label' => __( 'Query', 'betterdocs-pro' )
            ]
        );

        $this->add_control(
            'articles_sort',
            [
                'label'          => __( 'Sort By Docs', 'betterdocs-pro' ),
                'label_block'    => true,
                'type'           => Controls_Manager::SELECT2,
                'options'        => [
                    'ASC'      => 'Least Popular',
                    'DESC'     => 'Most Popular',
                    'MODIFIED' => 'Last Updated',
                    'CREATED'  => 'Last Created'
                ],
                'multiple'       => false,
                'default'        => 'DESC',
                'select2options' => [
                    'placeholder' => __( 'Select', 'betterdocs-pro' ),
                    'allowClear'  => true
                ]
            ]
        );

        $this->add_control(
            'popular_posts_number',
            [
                'label'   => __( 'Number Of Docs', 'betterdocs-pro' ),
                'type'    => Controls_Manager::NUMBER,
                'default' => '8'
            ]
        );

        $this->add_control(
            'popular_docs_name',
            [
                'label'   => __( 'Popular Docs Text', 'betterdocs-pro' ),
                'default' => 'Popular Docs',
                'type'    => Controls_Manager::TEXT,
                'dynamic' => ['active' => true]
            ]
        );

        $this->end_controls_section();

        $this->popular_view_layout();
        $this->box_style();
        $this->list_style();
    }

    /**
     * ----------------------------------------------------------
     * Section: Select Popular-View Layout
     * ----------------------------------------------------------
     */
    public function popular_view_layout() {
        $this->start_controls_section(
            'section_popular_layout',
            [
                'label' => __( 'Title', 'betterdocs-pro' ),
                'tab'   => Controls_Manager::TAB_STYLE
            ]
        );

        $this->add_control(
            'popular-layout-title-tag',
            [
                'label'   => __( 'Title Tag', 'betterdocs-pro' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'h2',
                'options' => [
                    'h1' => __( 'H1', 'betterdocs-pro' ),
                    'h2' => __( 'H2', 'betterdocs-pro' ),
                    'h3' => __( 'H3', 'betterdocs-pro' ),
                    'h4' => __( 'H4', 'betterdocs-pro' ),
                    'h5' => __( 'H5', 'betterdocs-pro' ),
                    'h6' => __( 'H6', 'betterdocs-pro' )
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label'    => __( 'Typography', 'betterdocs-pro' ),
                'name'     => 'popular_title_typo',
                'selector' => '{{WRAPPER}} .betterdocs-popular-articles-wrapper .betterdocs-popular-articles-heading'
            ]
        );

        $this->add_control(
            'popular_title_color',
            [
                'label'     => __( 'Color', 'betterdocs-pro' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-popular-articles-wrapper .betterdocs-popular-articles-heading' => 'color: {{VALUE}};'
                ]
            ]
        );

        $this->add_control(
            'popular_title_color_hover',
            [
                'label'     => __( 'Hover Color', 'betterdocs-pro' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-popular-articles-wrapper .betterdocs-popular-articles-heading:hover' => 'color: {{VALUE}};'
                ]
            ]
        );

        $this->add_control(
            'popular_title_alignment',
            [
                'label'     => __( 'Alignment', 'betterdocs-pro' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'left'   => __( 'Left', 'betterdocs-pro' ),
                    'center' => __( 'Center', 'betterdocs-pro' ),
                    'right'  => __( 'Right', 'betterdocs-pro' )
                ],
                'default'   => 'left',
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-popular-articles-wrapper .betterdocs-popular-articles-heading' => 'text-align:{{VALUE}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'popular_title_padding',
            [
                'label'      => __( 'Padding', 'betterdocs-pro' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-popular-articles-wrapper .betterdocs-popular-articles-heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'popular_title_margin',
            [
                'label'      => __( 'Margin', 'betterdocs-pro' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-popular-articles-wrapper .betterdocs-popular-articles-heading' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->end_controls_section();
    }

    /**
     * ----------------------------------------------------------
     * Section: Box Background Styles
     * ----------------------------------------------------------
     */
    public function box_style() {
        $this->start_controls_section(
            'popular_box_background_section',
            [
                'label' => __( 'Box', 'betterdocs-pro' ),
                'tab'   => Controls_Manager::TAB_STYLE
            ]
        );

        $this->add_responsive_control(
            'popular_list_padding',
            [
                'label'      => __( 'Popular List Padding', 'betterdocs-pro' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-popular-articles-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'popular_list_margin_2',
            [
                'label'      => __( 'Popular List Margin', 'betterdocs-pro' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-popular-articles-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'box_background_color_1',
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .betterdocs-popular-articles-wrapper'
            ]
        );

        $this->add_control(
            'box_background_color_hover_1',
            [
                'label'     => __( 'Background Hover Color', 'betterdocs-pro' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-popular-articles-wrapper:hover' => 'background-color : {{VALUE}};'
                ]
            ]
        );

        $this->end_controls_section();
    }

    /**
     * ----------------------------------------------------------
     * Section: Box Background Styles
     * ----------------------------------------------------------
     */
    public function list_style() {
        $this->start_controls_section(
            'popular_list_background_section',
            [
                'label' => __( 'List Item', 'betterdocs-pro' ),
                'tab'   => Controls_Manager::TAB_STYLE
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label'    => __( 'Typography', 'betterdocs-pro' ),
                'name'     => 'popular_list_typo',
                'selector' => '{{WRAPPER}} .betterdocs-popular-articles-wrapper .betterdocs-articles-list li a'
            ]
        );

        $this->add_control(
            'popular_list_color',
            [
                'label'     => __( 'Color', 'betterdocs-pro' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-popular-articles-wrapper .betterdocs-articles-list li a' => 'color: {{VALUE}};'
                ]
            ]
        );

        $this->add_control(
            'popular_list_color_hover',
            [
                'label'     => __( 'Hover Color', 'betterdocs-pro' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-popular-articles-wrapper .betterdocs-articles-list li a:hover' => 'color: {{VALUE}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'popular_list_margin',
            [
                'label'      => __( 'Spacing', 'betterdocs-pro' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-popular-articles-wrapper .betterdocs-articles-list li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'popular_icons',
            [
                'label'     => __( 'Icon', 'betterdocs-pro' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'popular_list_icon_color',
            [
                'label'     => __( 'Icon Color', 'betterdocs-pro' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-popular-articles-wrapper .betterdocs-articles-list li svg path' => 'fill: {{VALUE}}!important;'
                ],
                'default'   => '#000000'
            ]
        );

        $this->add_responsive_control(
            'popular_list_icon_size',
            [
                'label'      => __( 'Icon Size', 'betterdocs-pro' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range'      => [
                    '%' => [
                        'max'  => 100,
                        'step' => 1
                    ]
                ],
                'default'    => [
                    'size' => 15,
                    'unit' => 'px'
                ],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-popular-articles-wrapper .betterdocs-articles-list li svg' => 'width: {{SIZE}}{{UNIT}}; min-width:1px'
                ]
            ]
        );

        $this->add_responsive_control(
            'popular_list_icon_spacing',
            [
                'label'      => __( 'Icon Spacing', 'betterdocs-pro' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-popular-articles-wrapper .betterdocs-articles-list li svg' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->end_controls_section();
    }

    protected function render_callback() {
        $this->views( 'layouts/popular-articles/default' );
    }

    public function view_params() {
        $settings = &$this->attributes;

        $multiple_kb_status = betterdocs()->editor->get( 'elementor' )->multiple_kb_status();

        $class   = ['betterdocs-popular-articles-wrapper'];
        $class[] = $multiple_kb_status ? 'multiple-kb' : 'single-kb';

        return [
            'wrapper_attr'       => [
                'class' => $class
            ],
            'nested_subcategory' => false,
            'list_icon_name'     => 'list',
            'title_tag'          => $settings['popular-layout-title-tag'],
            'title'              => $settings['popular_docs_name'],
            'query_args'         => $this->betterdocs( 'query' )->docs_query_args( [
                'post_type'      => 'docs',
                'posts_per_page' => $settings['popular_posts_number'],
                'meta_key'       => '_betterdocs_meta_views',
                'orderby'        => 'meta_value_num',
                'order'          => $settings['articles_sort']
            ], ['tax_query'] )
        ];
    }
}
