<?php

namespace WPDeveloper\BetterDocsPro\Editors;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border as Group_Control_Border;
use WPDeveloper\BetterDocs\Editors\Elementor as FreeElementor;
use WPDeveloper\BetterDocsPro\Editors\Elementor\Widget\MultipleKB;
use Elementor\Group_Control_Typography as Group_Control_Typography;
use WPDeveloper\BetterDocsPro\Editors\Elementor\Widget\PopularView;
use WPDeveloper\BetterDocsPro\Editors\Elementor\Widget\TabViewList;

class Elementor extends FreeElementor {

    // public function __construct( Settings $settings, Enqueue $enqueue, Helper $helper ) {
    //     parent::__construct( $settings, $enqueue, $helper );
    // }

    public function init() {
        if ( ! $this->is_elementor_active ) {
            return;
        }

        add_action( 'betterdocs/elementor/widgets/advanced-search/switcher', [$this, 'advanced_search'], 10, 1 );
        add_action( 'betterdocs/elementor/widgets/advanced-search/controllers', [$this, 'advanced_search_controls'], 10, 1 );

        if ( $this->is_elementor_pro_active ) {
            add_filter( 'betterdocs_elementor_pro_widgets', [$this, 'pro_widgets'] );
        }

        parent::init();
    }

    public function advanced_search( $wp ) {
        if ( $wp->get_name() === 'betterdocs-search-form' ) {
            $wp->add_control(
                'betterdocs_category_search_toogle',
                [
                    'label'        => __( 'Enable Category Search', 'betterdocs-pro' ),
                    'type'         => Controls_Manager::SWITCHER,
                    'label_on'     => __( 'On', 'betterdocs-pro' ),
                    'label_off'    => __( 'Off', 'betterdocs-pro' ),
                    'return_value' => 'true',
                    'default'      => false
                ]
            );

            $wp->add_control(
                'betterdocs_search_button_toogle',
                [
                    'label'        => __( 'Enable Search Button', 'betterdocs-pro' ),
                    'type'         => Controls_Manager::SWITCHER,
                    'label_on'     => __( 'On', 'betterdocs-pro' ),
                    'label_off'    => __( 'Off', 'betterdocs-pro' ),
                    'return_value' => 'true',
                    'default'      => false
                ]
            );

            $wp->add_control(
                'betterdocs_popular_search_toogle',
                [
                    'label'        => __( 'Enable Popular Search', 'betterdocs-pro' ),
                    'type'         => Controls_Manager::SWITCHER,
                    'label_on'     => __( 'On', 'betterdocs-pro' ),
                    'label_off'    => __( 'Off', 'betterdocs-pro' ),
                    'return_value' => 'true',
                    'default'      => false
                ]
            );
        }
    }

    public function advanced_search_controls( $wp ) {
        if ( $wp->get_name() === 'betterdocs-search-form' ) {
            $wp->start_controls_section(
                'advance_search_controls',
                [
                    'label' => __( 'Advanced Search', 'betterdocs-pro' ),
                    'tab'   => Controls_Manager::TAB_STYLE
                ]
            );

            $wp->add_control(
                'advance_category_search_bd',
                [
                    'label' => __( 'Category Search', 'betterdocs-pro' ),
                    'type'  => Controls_Manager::HEADING
                ]
            );

            $wp->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name'     => 'advance_search_category_search_typography',
                    'selector' => '{{WRAPPER}}  .betterdocs-searchform .betterdocs-search-category'
                ]
            );

            $wp->add_control(
                'advance_search_category_search_font_color',
                [
                    'label'     => __( 'Font Color', 'betterdocs-pro' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .betterdocs-searchform .betterdocs-search-category' => 'color: {{VALUE}};'
                    ]
                ]
            );

            $wp->add_control(
                'advance_search_search_button_heading',
                [
                    'label' => __( 'Search Button', 'betterdocs-pro' ),
                    'type'  => Controls_Manager::HEADING
                ]
            );

            $wp->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name'     => 'advance_search_search_button_typography',
                    'selector' => '{{WRAPPER}} .betterdocs-searchform .search-submit'
                ]
            );

            $wp->add_control(
                'advance_search_search_button_font_color',
                [
                    'label'     => __( 'Font Color', 'betterdocs-pro' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .betterdocs-searchform .search-submit' => 'color: {{VALUE}};'
                    ]
                ]
            );

            $wp->add_control(
                'advance_search_search_button_background_color',
                [
                    'label'     => __( 'Background Color', 'betterdocs-pro' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .betterdocs-searchform .search-submit' => 'background-color: {{VALUE}};'
                    ]
                ]
            );

            $wp->add_control(
                'advance_search_search_button_background_color_hover',
                [
                    'label'     => __( 'Background Hover Color', 'betterdocs-pro' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .betterdocs-searchform .search-submit:hover' => 'background-color: {{VALUE}};'
                    ]
                ]
            );

            $wp->add_responsive_control(
                'advance_search_search_button_border_radius',
                [
                    'label'      => __( 'Border Radius', 'betterdocs-pro' ),
                    'type'       => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors'  => [
                        '{{WRAPPER}} .betterdocs-searchform .search-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                    ]
                ]
            );

            $wp->add_responsive_control(
                'advance_search_search_button_padding',
                [
                    'label'      => __( 'Padding', 'betterdocs-pro' ),
                    'type'       => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors'  => [
                        '{{WRAPPER}} .betterdocs-searchform .search-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                    ]
                ]
            );

            $wp->add_control(
                'advance_search_popular_search',
                [
                    'label' => __( 'Popular Search', 'betterdocs-pro' ),
                    'type'  => Controls_Manager::HEADING
                ]
            );

            $wp->add_responsive_control(
                'advance_search_popular_search_margin',
                [
                    'label'      => __( 'Margin', 'betterdocs-pro' ),
                    'type'       => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors'  => [
                        '{{WRAPPER}} .betterdocs-popular-search-keyword' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                    ]
                ]
            );

            $wp->add_control(
                'advance_search_popular_search_title_placeholder',
                [
                    'label'   => __( 'Title Placeholder', 'betterdocs-pro' ),
                    'type'    => Controls_Manager::TEXT,
                    'default' => __( 'Popular Search', 'betterdocs-pro' )
                ]
            );

            $wp->add_control(
                'advance_search_popular_search_title_color',
                [
                    'label'     => __( 'Title Color', 'betterdocs-pro' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .betterdocs-popular-search-keyword .popular-search-title' => 'color: {{VALUE}};'
                    ]
                ]
            );

            $wp->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'label'    => __( 'Title Typography', 'betterdocs-pro' ),
                    'name'     => 'advance_search_popular_search_title_typography',
                    'selector' => '{{WRAPPER}} .betterdocs-popular-search-keyword .popular-search-title'
                ]
            );

            $wp->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'label'    => __( 'Keyword Typography', 'betterdocs-pro' ),
                    'name'     => 'advance_search_popular_search_keyword_typography',
                    'selector' => '{{WRAPPER}}  .betterdocs-popular-search-keyword .popular-keyword'
                ]
            );

            $wp->add_control(
                'advance_search_popular_search_keyword_background_color',
                [
                    'label'     => __( 'Keyword Background Color', 'betterdocs-pro' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .betterdocs-popular-search-keyword .popular-keyword' => 'background-color: {{VALUE}};'
                    ]
                ]
            );

            $wp->add_control(
                'advance_search_popular_search_keyword_background_color_hover',
                [
                    'label'     => __( 'Keyword Background Hover Color', 'betterdocs-pro' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .betterdocs-popular-search-keyword .popular-keyword:hover' => 'background-color: {{VALUE}};'
                    ]
                ]
            );

            $wp->add_control(
                'advance_search_popular_search_keyword_text_color',
                [
                    'label'     => __( 'Keyword Text Color', 'betterdocs-pro' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .betterdocs-popular-search-keyword .popular-keyword' => 'color: {{VALUE}};'
                    ]
                ]
            );

            $wp->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name'           => 'advance_search_popular_search_keyword_border_type',
                    'label'          => __( 'Border', 'betterdocs-pro' ),
                    'fields_options' => [
                        'border' => [
                            'default' => 'solid'
                        ],
                        'width'  => [
                            'default' => [
                                'top'      => '1',
                                'right'    => '1',
                                'bottom'   => '1',
                                'left'     => '1',
                                'isLinked' => false
                            ]
                        ],
                        'color'  => [
                            'default' => '#DDDEFF'
                        ]
                    ],
                    'selector'       => '{{WRAPPER}} .betterdocs-popular-search-keyword .popular-keyword'
                ]
            );

            $wp->add_responsive_control(
                'advance_search_popular_search_keyword_border_radius',
                [
                    'label'      => __( 'Keyword Border Radius', 'betterdocs-pro' ),
                    'type'       => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors'  => [
                        '{{WRAPPER}} .betterdocs-popular-search-keyword .popular-keyword' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                    ]
                ]
            );

            $wp->add_responsive_control(
                'advance_search_popular_search_keyword_padding',
                [
                    'label'      => __( 'Keyword Padding', 'betterdocs-pro' ),
                    'type'       => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors'  => [
                        '{{WRAPPER}} .betterdocs-popular-search-keyword .popular-keyword' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                    ]
                ]
            );

            $wp->add_responsive_control(
                'advance_search_popular_search_keyword_margin',
                [
                    'label'      => __( 'Keyword Margin', 'betterdocs-pro' ),
                    'type'       => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors'  => [
                        '{{WRAPPER}} .betterdocs-popular-search-keyword .popular-keyword' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                    ]
                ]
            );

            $wp->end_controls_section();
        }
    }

    public function pro_widgets( $widgets ) {
        $widgets['betterdocs-elementor-multiple-kb']   = MultipleKB::class;
        $widgets['betterdocs-elementor-popular-view']  = PopularView::class;
        $widgets['betterdocs-elementor-tab-view-list'] = TabViewList::class;

        return $widgets;
    }
}
