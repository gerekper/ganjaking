<?php

namespace WPDeveloper\BetterDocsPro\Editors\Elementor\Widget;

use Elementor\Plugin;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use WPDeveloper\BetterDocs\Editors\Elementor\BaseWidget;
use Elementor\Group_Control_Border as Group_Control_Border;
use Elementor\Group_Control_Box_Shadow as Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

Class TabViewList extends BaseWidget {

    public function get_name() {
        return 'betterdocs-tab-view-list';
    }

    public function get_title() {
        return __( 'BetterDocs Tab View List', 'betterdocs-pro' );
    }

    public function get_icon() {
        return 'betterdocs-icon-date';
    }

    public function get_categories() {
        return ['betterdocs-elements', 'docs-archive'];
    }

    public function get_keywords() {
        return ['betterdocs-elements', 'betterdocs-tab-view-list', 'betterdocs', 'docs', 'tab-view'];
    }

    public function get_style_depends() {
        return ['betterdocs-category-tab-grid'];
    }

    public function get_script_depends(){
        return ['betterdocs-pro-mkb-tab-grid'];
    }

    public function get_custom_help_url() {
        return 'https://betterdocs.co/#pricing';
    }

    /**
     * Query  Controls!
     * @source includes/elementor-helper.php
     */
    public function betterdocs_do_action() {
        do_action( 'betterdocs/elementor/widgets/query', $this, 'knowledge_base' );
    }

    protected function register_controls() {
        $this->betterdocs_do_action();

        $this->tab_view_list_layout();
        $this->tab_view_list_tabs();
        $this->all_category_boxes();
        $this->tab_view_buttons();
        $this->all_cat_box_icons();
        $this->all_box_cat_list();
    }


    /**
     * ----------------------------------------------------------
     * Section: Select Nav Item View List Layout
     * ----------------------------------------------------------
     */
    public function tab_view_list_layout() {
        $this->start_controls_section(
            'section_tabviewlist_title_tag',
            [
                'label' => __( 'Layout Options', 'betterdocs-pro' )
            ]
        );

        $this->add_control(
            'tabview-list-title',
            [
                'label'   => __( 'Nav Item Title Tag', 'betterdocs-pro' ),
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

        $this->add_control(
            'explore_more_button_tab_list',
            [
                'label'        => __( 'Explore Button', 'betterdocs-pro' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Show', 'betterdocs-pro' ),
                'label_off'    => __( 'Hide', 'betterdocs-pro' ),
                'return_value' => 'true',
                'default'      => 'true'
            ]
        );

        $this->add_control(
            'show_icon_button_tab_list',
            [
                'label'        => __( 'Show Icon', 'betterdocs-pro' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Show', 'betterdocs-pro' ),
                'label_off'    => __( 'Hide', 'betterdocs-pro' ),
                'return_value' => 'true',
                'default'      => 'true'
            ]
        );

        $this->add_control(
            'show_title_button_tab_list',
            [
                'label'        => __( 'Show Title', 'betterdocs-pro' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Show', 'betterdocs-pro' ),
                'label_off'    => __( 'Hide', 'betterdocs-pro' ),
                'return_value' => 'true',
                'default'      => 'true'
            ]
        );

        $this->add_control(
            'explore_more_button_tab_list_text',
            [
                'label'   => __( 'Explore Button Text', 'betterdocs-pro' ),
                'default' => 'Explore Button',
                'type'    => Controls_Manager::TEXT,
                'dynamic' => ['active' => true]
            ]
        );

        $this->end_controls_section();
    }

    /**
     * ----------------------------------------------------------
     * Section: Nav Item Styles
     * ----------------------------------------------------------
     */
    public function tab_view_list_tabs() {
        $this->start_controls_section(
            'tabview_list_tabs',
            [
                'label' => __( 'Navbar', 'betterdocs-pro' ),
                'tab'   => Controls_Manager::TAB_STYLE
            ]
        );

        $this->add_responsive_control(
            'tabview_list_tabs_box_space_margin_normal',
            [
                'label'      => __( 'Margin Bottom', 'betterdocs-pro' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range'      => [
                    'px' => [
                        'max'  => 500,
                        'step' => 1
                    ]
                ],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-tab-list' => 'margin-bottom:{{SIZE}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'popular_title_padding',
            [
                'label'      => __( 'Navbar Padding', 'betterdocs-pro' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-tab-list' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'tabview_list_tabs_whole_box_color_normal',
            [
                'label'     => __( 'Navbar Background Color', 'betterdocs-pro' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-tab-list' => 'background-color:{{VALUE}};'
                ]
            ]
        );

        $this->add_control(
            'tabview_list_tabs_whole_box_color_head_normal',
            [
                'label'     => __( 'Navbar Items', 'betterdocs-pro' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );
        $this->start_controls_tabs( 'tabview_colors' );

        /** Normal State Nav Item Start **/
        $this->start_controls_tab(
            'tabview_list_normal',
            ['label' => __( 'Normal', 'betterdocs-pro' )]
        );

        $this->add_responsive_control(
            'tabview_list_margin_normal',
            [
                'label'      => __( 'Margin', 'betterdocs-pro' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-tab-list a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'tabview_list_padding_normal',
            [
                'label'      => __( 'Padding', 'betterdocs-pro' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-tab-list a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'tabview_list_border_radius_normal',
            [
                'label'      => __( 'Border Radius', 'betterdocs-pro' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-tab-list a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'tabview_list_tabs_colors_normal',
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .betterdocs-tab-list a'
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'tabview_list_borders_normal',
                'label'    => __( 'Border', 'betterdocs-pro' ),
                'selector' => '{{WRAPPER}} .betterdocs-tab-list a'
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'tabview_list_box_shadow_normal',
                'label'    => __( 'Shadow', 'betterdocs-pro' ),
                'selector' => '{{WRAPPER}} .betterdocs-tab-list a'
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'tabview_list_tabs_font_typo_normal',
                'selector' => '{{WRAPPER}} .betterdocs-tab-list a'
            ]
        );

        $this->add_control(
            'tabview_list_tabs_font_color_normal',
            [
                'label'     => __( 'Font Color', 'betterdocs-pro' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-tab-list a' => 'color : {{VALUE}};'
                ]
            ]
        );

        $this->end_controls_tab();
        /** Normal State Nav Item End **/

        /** Hover State Nav Item Start **/
        $this->start_controls_tab(
            'tabview_list_tabs_hover',
            ['label' => __( 'Hover', 'betterdocs-pro' )]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'tabview_list_active_tab_color_hover',
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .betterdocs-tab-list a:hover'
            ]
        );

        $this->add_control(
            'tabview_list_active_tabs_font_color_hover',
            [
                'label'     => __( 'Item Color', 'betterdocs-pro' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-tab-list a:hover' => 'color : {{VALUE}};'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'tabview_list_tabs_colors_hover',
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .betterdocs-tab-list a:hover'
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'tabview_list_tabs_border',
                'label'    => __( 'Border', 'betterdocs-pro' ),
                'selector' => '{{WRAPPER}} .betterdocs-tab-list a:hover'
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'tabview_list_tabs_shadow_hover',
                'label'    => __( 'Shadow', 'betterdocs-pro' ),
                'selector' => '{{WRAPPER}} .betterdocs-tab-list a:hover'
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'tabview_list_tabs_font_typo_hover',
                'selector' => '{{WRAPPER}} .betterdocs-tab-list a:hover'
            ]
        );

        $this->add_control(
            'tabview_list_tabs_font_color_hover',
            [
                'label'     => __( 'Font Color', 'betterdocs-pro' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-tab-list a:hover' => 'color:{{VALUE}};'
                ]
            ]
        );
        $this->end_controls_tab();
        /** Hover State Nav Item End **/

        /** Hover State Nav Item Start **/
        $this->start_controls_tab(
            'tabview_list_tabs_active',
            ['label' => __( 'Active', 'betterdocs-pro' )]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'tabview_list_active_tab_color_normal',
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .betterdocs-tab-list a.active'
            ]
        );

        $this->add_control(
            'tabview_list_active_tabs_font_color_normal',
            [
                'label'     => __( 'Font Color', 'betterdocs-pro' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-tab-list a.active' => 'color : {{VALUE}};'
                ]
            ]
        );

        $this->end_controls_tabs();
        $this->end_controls_section();
    }

    /**
     * ----------------------------------------------------------
     * Section: All Category Boxes Style
     * ----------------------------------------------------------
     */
    public function all_category_boxes() {
        $this->start_controls_section(
            'tabview_list_boxes',
            [
                'label' => __( 'Category Box', 'betterdocs-pro' ),
                'tab'   => Controls_Manager::TAB_STYLE
            ]
        );

        $this->add_control(
            'tabview_list_whole_cat_box_back_color_normal',
            [
                'label'     => __( 'Background Color', 'betterdocs-pro' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-tabgrid-contents-wrapper' => 'background-color:{{VALUE}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'tabview_list_whole_cat_box_padding_normal',
            [
                'label'      => __( 'Padding', 'betterdocs-pro' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-tabgrid-contents-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'tabview_list_category_items',
            [
                'label'     => __( 'Category Items', 'betterdocs-pro' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->start_controls_tabs( 'tabview_list_boxes_hover_normal' );

        /** Normal State Nav Item Start **/
        $this->start_controls_tab(
            'tabview_list_boxes_normal',
            ['label' => __( 'Normal', 'betterdocs-pro' )]
        );

        $this->add_responsive_control(
            'tabview_list_boxes_margin_normal',
            [
                'label'      => __( 'Margin', 'betterdocs-pro' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper .betterdocs-single-category-inner' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'tabview_list_boxes_padding_normal',
            [
                'label'      => __( 'Padding', 'betterdocs-pro' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper .betterdocs-single-category-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'tabview_list_boxes_back_colors_normal',
                'types'    => ['classic', 'gradient', 'video'],
                'selector' => '{{WRAPPER}} .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper .betterdocs-single-category-inner'
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'tabview_list_boxes_border_normal',
                'label'    => __( 'Border', 'betterdocs-pro' ),
                'selector' => '{{WRAPPER}} .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper .betterdocs-single-category-inner'
            ]
        );

        $this->add_control(
            'tabview_list_boxes_nested_head_normal',
            [
                'label'     => __( 'Nested Subcategory', 'betterdocs-pro' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'tabview_list_boxes_nested_color_normal',
            [
                'label'     => __( 'Heading Text Color', 'betterdocs-pro' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-single-category-inner .betterdocs-articles-list .betterdocs-nested-category-title a' => 'color:{{VALUE}};'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'tabview_list_boxes_nested_typo_normal',
                'selector' => '{{WRAPPER}} .betterdocs-single-category-inner .betterdocs-articles-list .betterdocs-nested-category-title a'
            ]
        );

        $this->add_responsive_control(
            'tabview_list_boxes_nested_arrow_size_normal',
            [
                'label'      => __( 'Arrow Size', 'betterdocs-pro' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range'      => [
                    '%' => [
                        'max'  => 100,
                        'step' => 1
                    ]
                ],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-single-category-inner .betterdocs-articles-list .betterdocs-nested-category-title svg' => 'font-size:{{SIZE}}{{UNIT}}; width: auto;'
                ]
            ]
        );

        $this->add_responsive_control(
            'tabview_list_boxes_nested_arrow_spacing_normal',
            [
                'label'      => __( 'Arrow Margin', 'betterdocs-pro' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-single-category-inner .betterdocs-articles-list .betterdocs-nested-category-title svg' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->end_controls_tab();
        /** Normal State Nav Item End **/

        /** Hover State Nav Item Start **/
        $this->start_controls_tab(
            'tabview_list_boxes_hover',
            ['label' => __( 'Hover', 'betterdocs-pro' )]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'tabview_list_boxes_back_colors_hover',
                'types'    => ['classic', 'gradient', 'video'],
                'selector' => '{{WRAPPER}} .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper .betterdocs-single-category-inner:hover'
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'tabview_list_boxes_border_hover',
                'label'    => __( 'Border', 'betterdocs-pro' ),
                'selector' => '{{WRAPPER}} .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper .betterdocs-single-category-inner:hover'
            ]
        );

        $this->add_control(
            'tabview_list_boxes_nested_head_hover',
            [
                'label'     => __( 'Nested Subcategory', 'betterdocs-pro' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'tabview_list_boxes_nested_color_hover',
            [
                'label'     => __( 'Heading Text Color', 'betterdocs-pro' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-single-category-inner .betterdocs-articles-list .betterdocs-nested-category-title a:hover' => 'color:{{VALUE}};'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'tabview_list_boxes_nested_typo_hover',
                'selector' => '{{WRAPPER}} .betterdocs-single-category-inner .betterdocs-articles-list .betterdocs-nested-category-title a:hover'
            ]
        );

        $this->end_controls_tab();
        /** Hover State Nav Item End **/

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    /**
     * ----------------------------------------------------------
     * Section: View Button Style
     * ----------------------------------------------------------
     */
    public function tab_view_buttons() {
        $this->start_controls_section(
            'tabview_list_view_button_section',
            [
                'label' => __( 'Explore Button', 'betterdocs-pro' ),
                'tab'   => Controls_Manager::TAB_STYLE
            ]
        );

        $this->start_controls_tabs( 'tabview_list_view_button_hover_normal' );

        /** Normal State Nav Item Start **/
        $this->start_controls_tab(
            'tabview_list_view_normal',
            ['label' => __( 'Normal', 'betterdocs-pro' )]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'tabview_list_view_back_color_normal',
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .betterdocs-footer a'
            ]
        );

        $this->add_control(
            'tabview_list_view_text_color_normal',
            [
                'label'     => __( 'Text Color', 'betterdocs-pro' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-footer a' => 'color:{{VALUE}};'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'tabview_list_view_font_typo_normal',
                'selector' => '{{WRAPPER}} .betterdocs-footer a'
            ]
        );

        $this->add_responsive_control(
            'tabview_list_view_font_border_radius_normal',
            [
                'label'      => __( 'Border Radius', 'betterdocs-pro' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-footer a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'tabview_list_view_font_padding_normal',
            [
                'label'      => __( 'Padding', 'betterdocs-pro' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-footer a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'tabview_list_view_font_margin_normal',
            [
                'label'      => __( 'Margin', 'betterdocs-pro' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-footer a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'tabview_list_view_border_normal',
                'label'    => __( 'Border', 'betterdocs-pro' ),
                'selector' => '{{WRAPPER}} .betterdocs-footer a'
            ]
        );

        $this->end_controls_tab();
        /** Normal State Nav Item End **/

        /** Hover State Nav Item Start **/
        $this->start_controls_tab(
            'tabview_list_boxes_view_hover_heading',
            ['label' => __( 'Hover', 'betterdocs-pro' )]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'tabview_list_view_back_color_hover',
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .betterdocs-footer a:hover'
            ]
        );

        $this->add_control(
            'tabview_list_view_text_color_hover',
            [
                'label'     => __( 'Text Color', 'betterdocs-pro' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-footer a:hover' => 'color:{{VALUE}};'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'tabview_list_view_font_typo_hover',
                'selector' => '{{WRAPPER}} .betterdocs-footer a:hover'
            ]
        );

        $this->add_responsive_control(
            'tabview_list_view_font_border_radius_hover',
            [
                'label'      => __( 'Border Radius', 'betterdocs-pro' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-footer a:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'tabview_list_view_font_padding_hover',
            [
                'label'      => __( 'Padding', 'betterdocs-pro' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-footer a:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'tabview_list_view_font_margin_hover',
            [
                'label'      => __( 'Margin', 'betterdocs-pro' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-footer a:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'tabview_list_view_border_hover',
                'label'    => __( 'Border', 'betterdocs-pro' ),
                'selector' => '{{WRAPPER}} .betterdocs-footer a:hover'
            ]
        );

        $this->end_controls_tab();
        /** Hover State Nav Item End **/

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    /**
     * ----------------------------------------------------------
     * Section: Catergory Box Icon Styles
     * ----------------------------------------------------------
     */
    public function all_cat_box_icons() {
        $this->start_controls_section(
            'box_icon_styles_head',
            [
                'label' => __( 'Category Icon', 'betterdocs-pro' ),
                'tab'   => Controls_Manager::TAB_STYLE
            ]
        );

        $this->add_control(
            'category_settings_area',
            [
                'label' => __( 'Area', 'betterdocs' ),
                'type'  => Controls_Manager::HEADING
            ]
        );

        $this->add_responsive_control(
            'category_settings_icon_area_size_normal',
            [
                'label'      => esc_html__( 'Size', 'betterdocs' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range'      => [
                    'px' => [
                        'max' => 500
                    ]
                ],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-category-icon' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'box_icons_normal_border',
                'label'    => __( 'Icon Border', 'betterdocs-pro' ),
                'selector' => '{{WRAPPER}} .betterdocs-category-icon .betterdocs-category-icon-img'
            ]
        );

        $this->end_controls_section();
    }

    /**
     * ----------------------------------------------------------
     * Section: List Settinggs
     * ----------------------------------------------------------
     */
    public function all_box_cat_list() {

        $this->start_controls_section(
            'cat_list_tabview',
            [
                'label' => __( 'Category List', 'betterdocs-pro' ),
                'tab'   => Controls_Manager::TAB_STYLE
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'tabview_list_item_typography',
                'selector' => '{{WRAPPER}} .betterdocs-single-category-wrapper .betterdocs-single-category-inner .betterdocs-articles-list li a'
            ]
        );

        $this->add_control(
            'tab_view_list_color',
            [
                'label'     => __( 'Color', 'betterdocs-pro' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-single-category-wrapper .betterdocs-single-category-inner .betterdocs-articles-list li a' => 'color:{{VALUE}};'
                ]
            ]
        );

        $this->add_control(
            'tabview_list_hover_color',
            [
                'label'     => __( 'Hover Color', 'betterdocs-pro' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-single-category-wrapper .betterdocs-single-category-inner .betterdocs-articles-list li a:hover' => 'color:{{VALUE}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'tabview_cat_list_margin',
            [
                'label'      => __( 'List Item Spacing', 'betterdocs-pro' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-single-category-wrapper .betterdocs-single-category-inner .betterdocs-articles-list li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'tabview_list_area_padding',
            [
                'label'              => __( 'List Area Padding', 'betterdocs-pro' ),
                'type'               => Controls_Manager::DIMENSIONS,
                'allowed_dimensions' => 'vertical',
                'size_units'         => ['px', 'em', '%'],
                'selectors'          => [
                    '{{WRAPPER}} .betterdocs-single-category-wrapper .betterdocs-single-category-inner .betterdocs-articles-list li' => 'padding-top: {{TOP}}{{UNIT}}; padding-bottom:{{BOTTOM}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'tabview_icon_settings_heading',
            [
                'label'     => __( 'Icon', 'betterdocs-pro' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'tabview_list_icon_color',
            [
                'label'     => __( 'Icon Color', 'betterdocs-pro' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-single-category-wrapper .betterdocs-single-category-inner .betterdocs-articles-list li svg' => 'fill:{{VALUE}}'
                ]
            ]
        );

        $this->add_control(
            'tabview_list_icon_hover_color',
            [
                'label'     => __( 'Icon Hover Color', 'betterdocs-pro' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-single-category-wrapper .betterdocs-single-category-inner .betterdocs-articles-list li svg:hover' => 'fill:{{VALUE}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'tabview_list_icon_size',
            [
                'label'      => __( 'Size', 'betterdocs-pro' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range'      => [
                    '%' => [
                        'max'  => 100,
                        'step' => 1
                    ]
                ],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-single-category-wrapper .betterdocs-single-category-inner .betterdocs-articles-list li svg' => 'height:{{SIZE}}{{UNIT}}; width:auto;'
                ]
            ]
        );

        $this->add_responsive_control(
            'tabview_list_icon_spacing',
            [
                'label'      => __( 'Spacing', 'betterdocs-pro' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-single-category-wrapper .betterdocs-single-category-inner .betterdocs-articles-list li svg' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );
    }

    public function view_params() {
        $settings = $this->attributes;

        $this->attributes['terms_order']        = $settings['order'];
        $this->attributes['terms_orderby']      = $settings['orderby'];
        $this->attributes['nested_subcategory'] = $settings['nested_subcategory_tab_list'];

        $_terms_args = [
            'taxonomy'   => 'knowledge_base',
            'hide_empty' => true,
            'parent'     => 0,
            'order'      => $settings['order'],
            'offset'     => $settings['offset'],
            'number'     => $settings['box_per_page'],
            'orderby'    => $settings['orderby'],
            'meta_key'   => 'kb_order'
        ];

        if ( $settings['include'] ) {
            $_terms_args['include'] = array_diff( $settings['include'], (array) $settings['exclude'] );
        }
        if ( $settings['exclude'] ) {
            $_terms_args['exclude'] = $settings['exclude'];
        }

        $kb_terms_query = betterdocs()->query->terms_query( $_terms_args );

        $this->attributes['nested_subcategory'] = $this->attributes['nested_subcategory_tab_list'];
        $this->attributes['show_title']         = $this->attributes['show_title_button_tab_list'];
        $this->attributes['number']             = 'all';

        return [
            'wrapper_attr'       => [
                'class' => ['betterdocs-category-tab-grid-wrapper betterdocs-wrapper betterdocs-wraper']
            ],
            'kb_terms'           => get_terms( $kb_terms_query ),

            'show_header'        => true,
            'show_title'         => $settings['show_title_button_tab_list'],
            'title_tag'          => $settings['tabview-list-title'],
            'show_list'          => true,

            'show_count'         => false,
            'show_button'        => $settings['explore_more_button_tab_list'],
            'button_text'        => $settings['explore_more_button_tab_list_text'],
            'nested_subcategory' => $settings['nested_subcategory_tab_list'],
            'show_icon'          => true
        ];
    }

    public function betterdocs_template_params( $_params, $layout, $term, $widget_type ) {
        $_params['query_args'] = betterdocs()->query->docs_query_args( array_merge( $_params['query_args'], [
            'posts_per_page' => $this->attributes['post_per_tab'],
            'orderby'        => $this->attributes['tab_list_posts_orderby'],
            'order'          => $this->attributes['tab_list_order']
        ] ) );

        $_params['nested_subcategory'] = $this->attributes['nested_subcategory_tab_list'];

        return $_params;
    }

    public function betterdocs_nested_docs_args( $_params ) {
        return [
            'multiple_kb'    => true,
            'posts_per_page' => $this->attributes['nested_posts_per_page'],
            'order'          => $this->attributes['nested_sub_cat_order'],
            'orderby'        => $this->attributes['nested_sub_cat_orderby']
        ];
    }

    protected function render_callback() {
        $multiple_kb_status = betterdocs()->editor->get( 'elementor' )->multiple_kb_status();

        if ( $multiple_kb_status != true ) {
            betterdocs()->views->get( 'admin/notices/enable-kb' );
            return;
        }

        if ( (bool) $this->attributes['nested_subcategory_tab_list'] ) {
            add_filter( 'betterdocs_nested_docs_args', [$this, 'betterdocs_nested_docs_args'], 10 );
        }

        add_filter( 'betterdocs_template_params', [$this, 'betterdocs_template_params'], 10, 4 );

        $this->views( 'layouts/tab-grid/default' );

        remove_filter( 'betterdocs_template_params', [$this, 'betterdocs_template_params'], 10 );
        if ( (bool) $this->attributes['nested_subcategory_tab_list'] ) {
            remove_filter( 'betterdocs_nested_docs_args', [$this, 'betterdocs_nested_docs_args'], 10 );
        }

        if ( Plugin::instance()->editor->is_edit_mode() ) {
            $this->render_editor_script();
        }
    }

    public function reset_attributes(){
        $this->attributes['post_orderby'] = $this->attributes['tab_list_posts_orderby'];
        $this->attributes['post_order']   = $this->attributes['tab_list_order'];
    }


    public function render_editor_script() {
        $this->views( 'layouts/tab-grid/editor' );
    }
}
