<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

class Betterdocs_Elementor_Popular_View extends Widget_Base{

    public function get_name() {
        return 'betterdocs-popular-view';
    }

    public function get_title()
    {
        return __('Betterdocs Popular Docs', 'betterdocs-pro');
    }

    public function get_icon()
    {
        return 'betterdocs-icon-title';
    }

    public function get_categories()
    {
        return ['betterdocs-elements', 'docs-archive'];
    }

    public function get_keywords()
    {
        return ['betterdocs-elements', 'betterdocs-popular-view', 'betterdocs', 'docs'];
    }

    public function get_custom_help_url()
    {
        return 'https://betterdocs.co/#pricing';
    }

    protected function register_controls()
    {
        /**
         * Query Popular Articles
         */
        $this->start_controls_section(
            'query_popular_articles',
            [
                'label' => __('Query', 'betterdocs-pro')
            ]
        );

        $this->add_control(
            'articles_sort',
            [
                'label' => __('Sort Articles', 'betterdocs-pro'),
                'label_block' => true,
                'type' => Controls_Manager::SELECT2,
                'options' => array(
                    'ASC'  => 'Least Popular',
                    'DESC' => 'Most Popular'
                ),
                'multiple' => false,
                'default' => 'DESC',
                'select2options' => [
                    'placeholder' => __('Select', 'betterdocs-pro'),
                    'allowClear' => true,
                ]
            ]
        );

        $this->add_control(
            'popular_posts_number',
            [
                'label' => __('Number Of Posts', 'betterdocs-pro'),
                'type' => Controls_Manager::NUMBER,
                'default' => '8',
            ]
        );

        $this->add_control(
            'popular_docs_name',
            [
                'label'     => __('Popular Docs Text', 'betterdocs-pro'),
                'default'   => 'Popular Docs',
                'type'      => Controls_Manager::TEXT,
                'dynamic'   => [ 'active' => true ]
            ]
        );

        $this->end_controls_section();

        $this->popular_view_layout();
        $this->box_background_style();
        $this->popular_lists_style();
    }

    protected function render()
    {
        $settings  = $this->get_settings_for_display();
        $multiple_kb_status = BetterDocs_Elementor::get_betterdocs_multiple_kb_status();

        $class = ['betterdocs-categories-wrap betterdocs-popular-list'];

        if ($multiple_kb_status) {
            $class[] = 'multiple-kb';
        } else {
            $class[] = 'single-kb';
        }

        echo '<div class="' . implode(' ', $class) . '">';
        echo '<'.$settings['popular-layout-title-tag'].' class="popular-title">' .esc_html__($settings['popular_docs_name'], 'betterdocs-pro'). '</'.$settings['popular-layout-title-tag'].'>';
        $args = array(
            'post_type'      => 'docs',
            'posts_per_page' => $settings['popular_posts_number'],
            'meta_key'       => '_betterdocs_meta_views',
            'orderby'        => 'meta_value_num',
            'order'          => $settings['articles_sort'],
        );
        $args = apply_filters('betterdocs_articles_args', $args);
        $post_query = new WP_Query($args);
        if ($post_query->have_posts()) :
            echo '<ul>';
            while ($post_query->have_posts()) : $post_query->the_post();
                $icon = '<svg viewBox="0 0 18 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                   <g clip-path="url(#clip0)">
                   <path d="M13.15 5.40903H4.84447C4.4615 5.40903 4.15234 5.73849 4.15234 6.14662C4.15234 6.55476 4.4615 6.88422 4.84447 6.88422H13.15C13.533 6.88422 13.8422 6.55476 13.8422 6.14662C13.8422 5.73849 13.533 5.40903 13.15 5.40903Z"/>
                   <path d="M13.15 8.85112H4.84447C4.4615 8.85112 4.15234 9.18058 4.15234 9.58872C4.15234 9.99685 4.4615 10.3263 4.84447 10.3263H13.15C13.533 10.3263 13.8422 9.99685 13.8422 9.58872C13.8422 9.18058 13.533 8.85112 13.15 8.85112Z"/>
                   <path d="M13.15 12.2933H4.84447C4.4615 12.2933 4.15234 12.6227 4.15234 13.0309C4.15234 13.439 4.4615 13.7685 4.84447 13.7685H13.15C13.533 13.7685 13.8422 13.439 13.8422 13.0309C13.8422 12.6227 13.533 12.2933 13.15 12.2933Z"/>
                   <path d="M10.3815 15.7354H4.84447C4.4615 15.7354 4.15234 16.0648 4.15234 16.473C4.15234 16.8811 4.4615 17.2106 4.84447 17.2106H10.3815C10.7645 17.2106 11.0736 16.8811 11.0736 16.473C11.0736 16.0648 10.7645 15.7354 10.3815 15.7354Z"/>
                   <path d="M15.9236 0H9.00231H2.07639C0.927455 0 0 0.988377 0 2.21279V19.7872C0 21.0116 0.927455 22 2.07639 22H9.00231H15.9282C17.0772 22 18.0046 21.0116 18.0046 19.7872V2.21279C18 0.988377 17.0725 0 15.9236 0ZM16.6157 19.7872C16.6157 20.1954 16.3066 20.5248 15.9236 20.5248H9.00231H2.07639C1.69341 20.5248 1.38426 20.1954 1.38426 19.7872V2.21279C1.38426 1.80465 1.69341 1.47519 2.07639 1.47519H6.9213H9.00231H11.0833H15.9282C16.3112 1.47519 16.6204 1.80465 16.6204 2.21279V19.7872H16.6157Z"/>
                   </g>
                   </svg>';
                echo '<li>'.$icon.'<a href="' . get_the_permalink() . '">' .  wp_kses(get_the_title(), BETTERDOCS_PRO_KSES_ALLOWED_HTML) . '</a></li>';
            endwhile;
            echo '</ul>';
        endif;
        wp_reset_query();
        echo '</div>';
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
                'label' => __('Layout Options', 'betterdocs-pro')
            ]
        );

        $this->add_control(
            'popular-layout-title-tag',
            [
                'label' => __('Popular Title Tag', 'betterdocs-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'h2',
                'options' => [
                    'h1' => __('H1', 'betterdocs-pro'),
                    'h2' => __('H2', 'betterdocs-pro'),
                    'h3' => __('H3', 'betterdocs-pro'),
                    'h4' => __('H4', 'betterdocs-pro'),
                    'h5' => __('H5', 'betterdocs-pro'),
                    'h6' => __('H6', 'betterdocs-pro'),
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label'     => esc_html__('Popular Title Typography', 'betterdocs-pro'),
                'name'     => 'popular_title_typo',
                'selector' => '{{WRAPPER}} .betterdocs-categories-wrap .popular-title'
            ]
        );

        $this->add_control(
            'popular_title_color',
            [
                'label'     => esc_html__('Popular Title Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-categories-wrap .popular-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'popular_title_color_hover',
            [
                'label'     => esc_html__('Popular Title Hover Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-categories-wrap .popular-title:hover' => 'color: {{VALUE}};',
                ],
            ]
        );


        $this->add_control(
            'popular_title_alignment',
            [
                'label'    => __( 'Title Alignment', 'betterdocs-pro' ),
                'type'     => Controls_Manager::SELECT,
                'options'  => [
                    'left'   => __('Left', 'betterdocs-pro'),
                    'center' => __('Center', 'betterdocs-pro'),
                    'right'  => __( 'Right', 'betterdocs-pro' )
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-popular-list .popular-title' => 'text-align:{{VALUE}};'
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * ----------------------------------------------------------
     * Section: Box Background Styles
     * ----------------------------------------------------------
     */
    public function box_background_style() {
        $this->start_controls_section(
            'popular_box_background_section',
            [
                'label' => __('Popular List Background', 'betterdocs-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'box_background_color_1',
                'types'    => [ 'classic', 'gradient', 'video' ],
                'selector' => '{{WRAPPER}} .betterdocs-categories-wrap',
            ]
        );

        $this->add_control(
            'box_background_color_hover_1',
            [
                'label'     => esc_html__('Background Hover Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-categories-wrap:hover' => 'background-color : {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

    /**
     * ----------------------------------------------------------
     * Section: Popular List Styles
     * ----------------------------------------------------------
     */
    public function popular_lists_style() {
        $this->start_controls_section(
            'popular_box_lists_section',
            [
                'label' => __('Popular List Style', 'betterdocs-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'popular_title',
            [
                'label'     => esc_html__('Popular Title', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'popular_title_padding',
            [
                'label'      => __('Popular Title Padding', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-categories-wrap .popular-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'popular_title_margin',
            [
                'label'      => __('Popular Title Margin', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-categories-wrap .popular-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_control(
            'popular_list_head',
            [
                'label'     => esc_html__('Popular List', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label'     => esc_html__('Popular List Typography', 'betterdocs-pro'),
                'name'     => 'popular_list_typo',
                'selector' => '{{WRAPPER}} .betterdocs-categories-wrap ul li a'
            ]
        );


        $this->add_control(
            'popular_list_color',
            [
                'label'     => esc_html__('List Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-categories-wrap ul li a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'popular_list_color_hover',
            [
                'label'     => esc_html__('List Hover Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-categories-wrap ul li a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'popular_list_margin',
            [
                'label'      => esc_html__('List Item Spacing', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-categories-wrap ul li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'popular_list_padding',
            [
                'label'              => esc_html__('Popular List Padding', 'betterdocs-pro'),
                'type'               => Controls_Manager::DIMENSIONS,
                'size_units'         => ['px', 'em', '%'],
                'selectors'          => [
                    '{{WRAPPER}} .betterdocs-categories-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->add_responsive_control(
            'popular_list_margin_2',
            [
                'label'              => esc_html__('Popular List Margin', 'betterdocs-pro'),
                'type'               => Controls_Manager::DIMENSIONS,
                'size_units'         => ['px', 'em', '%'],
                'selectors'          => [
                    '{{WRAPPER}} .betterdocs-categories-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'popular_icons',
            [
                'label'     => esc_html__('Popular List Icon', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'popular_list_icon_color',
            [
                'label'     => esc_html__('Icon Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-categories-wrap ul li svg path' => 'fill: {{VALUE}}!important;',
                ],
                'default'   => '#000000',
            ]
        );

        $this->add_responsive_control(
            'popular_list_icon_size',
            [
                'label'      => __('Size', 'betterdocs-pro'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range'      => [
                    '%' => [
                        'max'  => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'size' => 15,
                    'unit' => 'px',
                ],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-categories-wrap ul li svg' => 'width: {{SIZE}}{{UNIT}}; min-width:1px'
                ],
            ]
        );

        $this->add_responsive_control(
            'popular_list_icon_spacing',
            [
                'label'      => esc_html__('Icon Spacing', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-categories-wrap ul li svg' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

    }
}