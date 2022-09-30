<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border as Group_Control_Border;
use Elementor\Group_Control_Box_Shadow as Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use ElementorPro\Base\Base_Widget_Trait;

if( !defined( 'ABSPATH' ) ) {
    exit;
}

Class BetterDocs_Elementor_Tab_View extends Widget_Base{
    
    public function get_name() {
        return 'betterdocs-tab-view-list';
    }

    public function get_title()
    {
        return __('Betterdocs Tab View List', 'betterdocs-pro');
    }

    public function get_icon()
    {
        return 'betterdocs-icon-date';
    }

    public function get_categories()
    {
        return ['betterdocs-elements', 'docs-archive'];
    }

    public function get_keywords()
    {
        return ['betterdocs-elements', 'betterdocs-tab-view-list', 'betterdocs', 'docs', 'tab-view'];
    }

    public function get_custom_help_url()
    {
        return 'https://betterdocs.co/#pricing';
    }

    protected function register_controls()
    {
        do_action('betterdocs/elementor/widgets/query', $this, 'knowledge_base');

        $this->tab_view_list_layout();
        $this->tab_view_list_tabs();
        $this->all_category_boxes();
        $this->tab_view_buttons();
        $this->all_cat_box_icons();
        $this->all_box_cat_list();
    }

    protected function render() {
       $settings = $this->get_settings_for_display();
       $multiple_kb_status = BetterDocs_Elementor::get_betterdocs_multiple_kb_status();

       if( $multiple_kb_status != true ) {
            echo sprintf('<p class="elementor-alert elementor-alert-warning">%1$s <strong>%2$s</strong> %3$s <strong>%4$s</strong></p>.',
            __('Whoops! It seems like you have the', 'betterdocs-pro'),
            __('‘Multiple Knowledge Base’', 'betterdocs-pro'),
            __('option disabled. Make sure to enable this option from your', 'betterdocs-pro'),
            __('WordPress Dashboard -> BetterDocs -> Settings -> General.', 'betterdocs-pro'));
            return;
       }

        $terms_object = array(
            'taxonomy'   => 'knowledge_base',
            'hide_empty' => true,
            'parent'     => 0,
            'order'      => $settings['order'],
            'offset'     => $settings['offset'],
            'number'     => $settings['box_per_page']
        );

        if ($settings['orderby'] == 'betterdocs_order') {
            $terms_object['meta_key'] = 'kb_order';
            $terms_object['orderby'] = 'meta_value_num';
            $terms_object['order'] = 'ASC';
        } else {
            $terms_object['orderby'] = $settings['orderby'];
        }
        
        if($settings['include'])
        {
            $terms_object['include'] = array_diff($settings['include'], (array) $settings['exclude']);
        }

        if ($settings['exclude'])
        {
            $terms_object['exclude'] = $settings['exclude'];
        }


        $taxonomy_objects = get_terms( apply_filters( 'betterdocs_kb_terms_object', $terms_object ) );

        if ($taxonomy_objects && !is_wp_error($taxonomy_objects)) {
            $class = ['betterdocs-categories-wrap betterdocs-tab-grid ash-bg multiple-kb'];

            echo '<div class="' . implode(' ', $class) . '">';
            ?>
            <div class="betterdocs-tab-list tabs-nav">
                <?php
                foreach ($taxonomy_objects as $kb) {
                    if ($kb->count > 0) {
                        echo '<a href="" class="icon-wrap" data-toggle-target=".'.$kb->slug .'">'.$kb->name .'</a>';
                    }
                }
                ?>
            </div>
            <div class="tabs-content">
                <?php
                foreach ($taxonomy_objects as $kb) {
                    if ($kb->count > 0) {
                        echo '<div class="betterdocs-tab-content '.$kb->slug.'">';
                        echo '<div class="betterdocs-tab-categories">';
                        $category_objects = BetterDocs_Helper::taxonomy_object(true, '', $settings['order'], $settings['orderby'], $kb->slug, $settings['nested_subcategory_tab_list']);
                        if ($category_objects && !is_wp_error($category_objects)) {
                            // display category grid by order
                            foreach ($category_objects as $term) {
                                $term_id = $term->term_id;
                                $term_slug = $term->slug;
                                $count = $term->count;
                                $get_term_count = betterdocs_get_postcount($count, $term_id, $settings['nested_subcategory_tab_list']);
                                $term_count = apply_filters('betterdocs_postcount', $get_term_count, true, $term_id, $term_slug, $count, $settings['nested_subcategory_tab_list'], $kb->slug);

                                if ($term_count > 0) {
                                    $cat_icon_id = get_term_meta($term_id, 'doc_category_image-id', true);

                                    if( $settings['show_icon_button_tab_list'] ) {
                                        if ($cat_icon_id) {
                                            $cat_icon_url = wp_get_attachment_image_url($cat_icon_id, 'thumbnail');
                                            $cat_icon = '<img class="docs-cat-icon" src="' . $cat_icon_url . '" alt="">';
                                        } else {
                                            $cat_icon = '<img class="docs-cat-icon" src="' . BETTERDOCS_ADMIN_URL . 'assets/img/betterdocs-cat-icon.svg" alt="">';
                                        }
                                    } else {
                                        $cat_icon = '';
                                    }
                                    
                                    if( $settings['show_title_button_tab_list'] ) {
                                        $title_name = $term->name;
                                    } else {
                                        $title_name = '';
                                    }

                                    $term_permalink = BetterDocs_Helper::term_permalink('doc_category', $term->slug);
                                    echo '<div class="docs-single-cat-wrap">
                                        <div class="docs-cat-title-inner">
                                            <div class="docs-cat-title">' . $cat_icon . '<a href="' . esc_url($term_permalink) . '"><'.$settings['tabview-list-title'].' class="docs-cat-heading">' . $title_name . '</'.$settings['tabview-list-title'].'></a></div>
                                        </div>
                                        <div class="docs-item-container">';
                                            if (isset($settings['post_per_tab'])) {
                                                $posts_per_grid = $settings['post_per_tab'];
                                            }

                                            $list_args = BetterDocs_Helper::list_query_arg('docs', true, $term_slug, $posts_per_grid, $settings['tab_list_posts_orderby'], $settings['tab_list_order'], $kb->slug);
                                            $args = apply_filters('betterdocs_articles_args', $list_args, $term->term_id);
                                            $post_query = new WP_Query($args);
                                            if ($post_query->have_posts()) :
                                                echo '<ul>';
                                                while ($post_query->have_posts()) : $post_query->the_post();
                                                    $attr = ['href="' . get_the_permalink() . '"'];
                                                    echo '<li>' . BetterDocs_Helper::list_svg() . '<a ' . implode(' ', $attr) . '>' .  wp_kses(get_the_title(), BETTERDOCS_PRO_KSES_ALLOWED_HTML) . '</a></li>';
                                                endwhile;
                                                echo '</ul>';
                                            endif;
                                            wp_reset_query();

                                            // Sub category query
                                            if ($settings['nested_subcategory_tab_list'] == true) {
                                                nested_category_list(
                                                    $term_id,
                                                    true,
                                                    '',
                                                    'docs',
                                                    $settings['nested_sub_cat_orderby'],
                                                    $settings['nested_sub_cat_order'],
                                                    $settings['orderby'],
                                                    $settings['order'],
                                                    '',
                                                    $kb->slug,
                                                    $settings['nested_posts_per_page']
                                                );
                                            }

                                            // Read More Button
                                            if ($settings['explore_more_button_tab_list'] == true) {
                                                echo '<a class="docs-cat-link-btn" href="' . $term_permalink . '">' . esc_html__($settings['explore_more_button_tab_list_text'], 'betterdocs-pro') . '</a>';
                                            }
                                        echo '</div>
                                    </div>';
                                }
                            }
                        }
                        echo '</div>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
            <?php

            if( \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
                    $this->render_editor_script();
            }
        }
    }

    /**
     * ----------------------------------------------------------
     * Section: Select Tab View List Layout
     * ----------------------------------------------------------
     */
    public function tab_view_list_layout(){
        $this->start_controls_section(
            'section_tabviewlist_title_tag',
            [
                'label' => __('Layout Options', 'betterdocs-pro')
            ]
        );

        $this->add_control(
            'tabview-list-title',
            [
                'label' => __('Tab Title Tag', 'betterdocs-pro'),
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

        $this->add_control(
            'explore_more_button_tab_list',
            [
                'label' => __('Explore Button', 'betterdocs-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'betterdocs-pro'),
                'label_off' => __('Hide', 'betterdocs-pro'),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'show_icon_button_tab_list',
            [
                'label'        => __('Show Icon', 'betterdocs-pro'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __('Show', 'betterdocs-pro'),
                'label_off'    => __('Hide', 'betterdocs-pro'),
                'return_value' => 'true',
                'default'      => 'true',
            ]
        );

        $this->add_control(
            'show_title_button_tab_list',
            [
                'label'        => __('Show Title', 'betterdocs-pro'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __('Show', 'betterdocs-pro'),
                'label_off'    => __('Hide', 'betterdocs-pro'),
                'return_value' => 'true',
                'default'      => 'true',
            ]
        );

        $this->add_control(
            'explore_more_button_tab_list_text',
            [
                'label'     => __('Explore Button Text', 'betterdocs-pro'),
                'default'   => 'Explore Button',
                'type'      => Controls_Manager::TEXT,
                'dynamic'   => [ 'active' => true ]
            ]
        );

        $this->end_controls_section();
    }

    /**
     * ----------------------------------------------------------
     * Section: Tab Styles
     * ----------------------------------------------------------
     */
    public function tab_view_list_tabs(){
        $this->start_controls_section(
            'tabview_list_tabs',
            [
                'label' => __('Tab', 'betterdocs-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabview_colors');

        /** Normal State Tab Start **/
        $this->start_controls_tab(
           'tabview_list_normal',
           ['label' => esc_html__('Normal', 'betterdocs-pro')]
        );

        $this->add_responsive_control(
            'tabview_list_margin_normal',
            [
                'label'      => __('Tab Margin', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-categories-wrap .betterdocs-tab-list .icon-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'tabview_list_padding_normal',
            [
                'label'      => __('Tab Padding', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-categories-wrap .betterdocs-tab-list .icon-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ]
            ]
        );

        $this->add_responsive_control(
            'tabview_list_border_radius_normal',
            [
                'label'      => __('Border Radius', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-categories-wrap .betterdocs-tab-list .icon-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ]
            ]
        );

        $this->add_control(
            'tabview_list_active_tabs_color_head_normal',
            [
                'label'     => __('Active Tab List Background Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'tabview_list_active_tab_color_normal',
				'types'    => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .betterdocs-categories-wrap .betterdocs-tab-list a.active',
			]
		);

        $this->add_control(
            'tabview_list_active_tabs_font_color_head_normal',
            [
                'label'     => __('Active Tab List Font Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'tabview_list_active_tabs_font_color_normal',
            [
                'label'     => esc_html__('Active Tab Font Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-categories-wrap .betterdocs-tab-list a.active' => 'color : {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'tabview_list_tabs_color_normal',
            [
                'label'     => __('Tab Background Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'tabview_list_tabs_colors_normal',
				'types'    => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .betterdocs-categories-wrap .betterdocs-tab-list .icon-wrap',
			]
		);

        $this->add_control(
            'tabview_list_tabs_border_normal',
            [
                'label'     => __('Tab Border', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tabview_list_borders_normal',
				'label' => __( 'Border', 'betterdocs-pro' ),
				'selector' => '{{WRAPPER}} .betterdocs-categories-wrap .betterdocs-tab-list .icon-wrap',
			]
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tabview_list_box_shadow_normal',
				'label' => __( 'Tab Shadow', 'betterdocs-pro' ),
				'selector' => '{{WRAPPER}} .betterdocs-categories-wrap .betterdocs-tab-list .icon-wrap',
			]
		);

        $this->add_control(
            'tabview_list_tabs_font_typography',
            [
                'label'     => __('Tab Font Typography', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'tabview_list_tabs_font_typo_normal',
                'selector' => '{{WRAPPER}} .betterdocs-categories-wrap .betterdocs-tab-list .icon-wrap'
            ]
        );

        $this->add_control(
            'tabview_list_tabs_font_color_normal',
            [
                'label'     => esc_html__('Tab Font Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-categories-wrap .betterdocs-tab-list .icon-wrap' => 'color : {{VALUE}};',
                ],
            ]
        );


        $this->add_control(
            'tabview_list_tabs_box_space_head_normal',
            [
                'label'     => __('Space Between Tab And Box', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );


        $this->add_responsive_control(
            'tabview_list_tabs_box_space_margin_normal',
            [
                'label'      => __('Spacing', 'betterdocs-pro'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range'      => [
                    'px' => [
                        'max'  => 500,
                        'step' => 1,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-categories-wrap .betterdocs-tab-list' => 'margin-bottom:{{SIZE}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'tabview_list_tabs_whole_box_color_head_normal',
            [
                'label'     => __('Tab Whole Box Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'tabview_list_tabs_whole_box_color_normal',
            [
                'label'     => esc_html__('Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-categories-wrap .betterdocs-tab-list' => 'background-color:{{VALUE}};'
                ],
            ]
        );

        $this->end_controls_tab();
        /** Normal State Tab End **/

        /** Hover State Tab Start **/
        $this->start_controls_tab(
            'tabview_list_tabs_hover',
            ['label' => esc_html__('Hover', 'betterdocs-pro')]
        );

        $this->add_responsive_control(
            'tabview_list_margin_hover',
            [
                'label'      => __('Tab Margin', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-categories-wrap .betterdocs-tab-list .icon-wrap:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'tabview_list_padding_hover',
            [
                'label'      => __('Tab Padding', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-categories-wrap .betterdocs-tab-list .icon-wrap:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ]
            ]
        );

        $this->add_responsive_control(
            'tabview_list_border_radius_hover',
            [
                'label'      => __('Border Radius', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-categories-wrap .betterdocs-tab-list .icon-wrap:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ]
            ]
        );

        $this->add_control(
            'tabview_list_active_tabs_color_head_hover',
            [
                'label'     => __('Active Tab List Background Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'tabview_list_active_tab_color_hover',
				'types'    => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .betterdocs-categories-wrap .betterdocs-tab-list a.active:hover',
			]
		);

        $this->add_control(
            'tabview_list_active_tabs_font_color_head_hover',
            [
                'label'     => __('Active Tab List Font Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'tabview_list_active_tabs_font_color_hover',
            [
                'label'     => esc_html__('Active Tab Font Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-categories-wrap .betterdocs-tab-list a.active:hover' => 'color : {{VALUE}};',
                ],
            ]
        );


        $this->add_control(
            'tabview_list_tabs_color_hover',
            [
                'label'     => __('Tab Background Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'tabview_list_tabs_colors_hover',
				'types'    => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .betterdocs-categories-wrap .betterdocs-tab-list a:hover',
			]
		);

        $this->add_control(
            'tabview_list_tabs_border_hover',
            [
                'label'     => __('Tab Border', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tabview_list_tabs_border',
				'label' => __( 'Border', 'betterdocs-pro' ),
				'selector' => '{{WRAPPER}} .betterdocs-categories-wrap .betterdocs-tab-list a:hover',
			]
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tabview_list_tabs_shadow_hover',
				'label' => __( 'Tab Shadow', 'betterdocs-pro' ),
				'selector' => '{{WRAPPER}} .betterdocs-categories-wrap .betterdocs-tab-list a:hover',
			]
		);


        $this->add_control(
            'tabview_list_tabs_font_typography_hover',
            [
                'label'     => __('Tab Font Typography', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'tabview_list_tabs_font_typo_hover',
                'selector' => '{{WRAPPER}} .betterdocs-categories-wrap .betterdocs-tab-list a:hover'
            ]
        );

        $this->add_control(
            'tabview_list_tabs_font_color_hover',
            [
                'label'     => esc_html__('Tab Font Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-categories-wrap .betterdocs-tab-list a:hover' => 'color:{{VALUE}};'
                ],
            ]
        );

        $this->add_control(
            'tabview_list_tabs_box_space_head_hover',
            [
                'label'     => __('Space Between Tab And Box', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );


        $this->add_responsive_control(
            'tabview_list_tabs_box_space_margin_hover',
            [
                'label'      => __('Spacing', 'betterdocs-pro'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range'      => [
                    'px' => [
                        'max'  => 500,
                        'step' => 1,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-categories-wrap .betterdocs-tab-list:hover' => 'margin-bottom:{{SIZE}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'tabview_list_tabs_box_color_head_hover',
            [
                'label'     => __('Tab Whole Box Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'tabview_list_tabs_whole_box_color_hover',
            [
                'label'     => esc_html__('Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-categories-wrap .betterdocs-tab-list:hover' => 'background-color:{{VALUE}};'
                ],
            ]
        );


        /** Hover State Tab End **/

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
                'label' => __('Category Box', 'betterdocs-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabview_list_boxes_hover_normal');

        /** Normal State Tab Start **/
        $this->start_controls_tab(
            'tabview_list_boxes_normal',
            ['label' => esc_html__('Normal', 'betterdocs-pro')]
        );

        $this->add_responsive_control(
            'tabview_list_boxes_margin_normal',
            [
                'label'      => __('Box Margin', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-categories-wrap .tabs-content .betterdocs-tab-categories .docs-single-cat-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'tabview_list_boxes_padding_normal',
            [
                'label'      => __('Box Padding', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-categories-wrap .tabs-content .betterdocs-tab-categories .docs-single-cat-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'tabview_list_boxes_back_color_head',
            [
                'label'     => __('Box Background Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'tabview_list_boxes_back_colors_normal',
				'types'    => [ 'classic', 'gradient', 'video'],
				'selector' => '{{WRAPPER}} .betterdocs-categories-wrap .tabs-content .betterdocs-tab-categories .docs-single-cat-wrap',
			]
		);

        $this->add_control(
            'tabview_list_boxes_border_head_normal',
            [
                'label'     => __('Box Border', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tabview_list_boxes_border_normal',
				'label' => __( 'Box Border', 'betterdocs-pro' ),
				'selector' => '{{WRAPPER}} .betterdocs-categories-wrap .tabs-content .betterdocs-tab-categories .docs-single-cat-wrap',
			]
		);

        $this->add_control(
            'tabview_list_boxes_nested_head_normal',
            [
                'label'     => __('Nested Subcategory', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );


        $this->add_control(
			'tabview_list_boxes_nested_color_normal',
			[
				'label' => __( 'Heading Text Color', 'betterdocs-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-sub-cat-title a' => 'color:{{VALUE}};'
				],
			]
		);
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'tabview_list_boxes_nested_typo_normal',
                'selector' => '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-sub-cat-title a'
            ]
        );

        $this->add_responsive_control(
            'tabview_list_boxes_nested_arrow_size_normal',
            [
                'label'      => __('Arrow Size', 'betterdocs-pro'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range'      => [
                    '%' => [
                        'max'  => 100,
                        'step' => 1,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-sub-cat-title svg' => 'font-size:{{SIZE}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'tabview_list_boxes_nested_arrow_spacing_normal',
            [
                'label'      => __('Arrow Margin', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-sub-cat-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'tabview_list_whole_cat_box_back_color_head_normal',
            [
                'label'     => __('Whole Category Box', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
			'tabview_list_whole_cat_box_back_color_normal',
			[
				'label' => __( 'Whole Box Color', 'betterdocs-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .betterdocs-tab-content' => 'background-color:{{VALUE}};'
				],
			]
		);


        $this->add_responsive_control(
            'tabview_list_whole_cat_box_padding_normal',
            [
                'label'      => __('Whole Box Padding', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-tab-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );


        $this->end_controls_tab();
        /** Normal State Tab End **/

        /** Hover State Tab Start **/
        $this->start_controls_tab(
            'tabview_list_boxes_hover',
            ['label' => esc_html__('Hover', 'betterdocs-pro')]
        );

        $this->add_responsive_control(
            'tabview_list_boxes_margin_hover',
            [
                'label'      => __('Box Margin', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-categories-wrap .tabs-content .betterdocs-tab-categories .docs-single-cat-wrap:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'tabview_list_boxes_padding_hover',
            [
                'label'      => __('Box Padding', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-categories-wrap .tabs-content .betterdocs-tab-categories .docs-single-cat-wrap:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'tabview_list_boxes_back_color_head_hover',
            [
                'label'     => __('Box Background Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'tabview_list_boxes_back_colors_hover',
				'types'    => [ 'classic', 'gradient', 'video'],
				'selector' => '{{WRAPPER}} .betterdocs-categories-wrap .tabs-content .betterdocs-tab-categories .docs-single-cat-wrap:hover',
			]
		);


        $this->add_control(
            'tabview_list_boxes_border_head_hover',
            [
                'label'     => __('Box Border', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tabview_list_boxes_border_hover',
				'label' => __( 'Box Border', 'betterdocs-pro' ),
				'selector' => '{{WRAPPER}} .betterdocs-categories-wrap .tabs-content .betterdocs-tab-categories .docs-single-cat-wrap:hover',
			]
		);

        $this->add_control(
            'tabview_list_boxes_nested_head_hover',
            [
                'label'     => __('Nested Subcategory', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );


        $this->add_control(
			'tabview_list_boxes_nested_color_hover',
			[
				'label' => __( 'Heading Text Color', 'betterdocs-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-sub-cat-title a:hover' => 'color:{{VALUE}};'
				],
			]
		);
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'tabview_list_boxes_nested_typo_hover',
                'selector' => '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-sub-cat-title a:hover'
            ]
        );

        $this->add_responsive_control(
            'tabview_list_boxes_nested_arrow_size_hover',
            [
                'label'      => __('Arrow Size', 'betterdocs-pro'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range'      => [
                    '%' => [
                        'max'  => 100,
                        'step' => 1,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-sub-cat-title svg:hover' => 'font-size:{{SIZE}}{{UNIT}};'
                ]
            ]
        );
        
        $this->add_responsive_control(
            'tabview_list_boxes_nested_arrow_spacing_hover',
            [
                'label'      => __('Arrow Margin', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-sub-cat-title:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'tabview_list_whole_cat_box_back_color_head_hover',
            [
                'label'     => __('Whole Category Box', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
			'tabview_list_whole_cat_box_back_color_hover',
			[
				'label' => __( 'Whole Box Color', 'betterdocs-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .betterdocs-tab-content:hover' => 'background-color:{{VALUE}};'
				],
			]
		);

        $this->add_responsive_control(
            'tabview_list_whole_cat_box_padding_hover',
            [
                'label'      => __('Whole Box Padding', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-tab-content:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->end_controls_tab();
        /** Hover State Tab End **/
        
        $this->end_controls_tabs();

        $this->end_controls_section();

    }

   /**
    * ----------------------------------------------------------
    * Section: View Button Style
    * ----------------------------------------------------------
    */
    public function tab_view_buttons(){
        $this->start_controls_section(
            'tabview_list_view_button_section',
            [
                'label' => __('Explore Button', 'betterdocs-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabview_list_view_button_hover_normal');

        /** Normal State Tab Start **/
        $this->start_controls_tab(
            'tabview_list_view_normal',
            ['label' => esc_html__('Normal', 'betterdocs-pro')]
        );

        $this->add_control(
            'tabview_list_boxes_view_normal',
            [
                'label'     => __('Explore More', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'tabview_list_view_back_color_normal',
				'types'    => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn'
			]
		);

        $this->add_control(
			'tabview_list_view_text_color_normal',
			[
				'label' => __( 'Button Text Color', 'betterdocs-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn' => 'color:{{VALUE}};'
				],
			]
		);       

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'tabview_list_view_font_typo_normal',
                'selector' => '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn'
            ]
        );

        $this->add_responsive_control(
            'tabview_list_view_font_border_radius_normal',
            [
                'label'      => __('Button Border Radius', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'tabview_list_view_font_padding_normal',
            [
                'label'      => __('Button Padding', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'tabview_list_view_font_margin_normal',
            [
                'label'      => __('Button Positioning', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tabview_list_view_border_normal',
				'label' => __( 'Box Border', 'betterdocs-pro' ),
				'selector' => '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn'
			]
		);

        $this->end_controls_tab();
        /** Normal State Tab End **/

        /** Hover State Tab Start **/
        $this->start_controls_tab(
            'tabview_list_boxes_view_hover_heading',
            ['label' => esc_html__('Hover', 'betterdocs-pro')]
        );

        $this->add_control(
            'tabview_list_boxes_view_hover',
            [
                'label'     => __('Explore More', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'tabview_list_view_back_color_hover',
				'types'    => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn:hover',
			]
		);

        $this->add_control(
			'tabview_list_view_text_color_hover',
			[
				'label' => __( 'Button Text Color', 'betterdocs-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn:hover' => 'color:{{VALUE}};'
				],
			]
		);   

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'tabview_list_view_font_typo_hover',
                'selector' => '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn:hover'
            ]
        );

        $this->add_responsive_control(
            'tabview_list_view_font_border_radius_hover',
            [
                'label'      => __('Button Border Radius', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'tabview_list_view_font_padding_hover',
            [
                'label'      => __('Button Padding', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'tabview_list_view_font_margin_hover',
            [
                'label'      => __('Button Positioning', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );



        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tabview_list_view_border_hover',
				'label' => __( 'Box Border', 'betterdocs-pro' ),
				'selector' => '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn:hover'
			]
		);

        $this->end_controls_tab();
        /** Hover State Tab End **/

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
                'label' => __('Box Icon', 'betterdocs-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('box_icons_tabs_normal_hover');

        /** Normal State Tab Start **/
        $this->start_controls_tab(
            'box_icons_tab_normal_heading',
            ['label' => esc_html__('Normal', 'betterdocs-pro')]
        );


        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'box_icons_normal_border',
				'label'    => __( 'Icon Border', 'betterdocs-pro' ),
				'selector' => '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-cat-title-inner .docs-cat-title .docs-cat-icon'
			]
		);

        $this->add_control(
			'box_icons_normal_back_color',
			[
				'label' => __( 'Icon Color', 'betterdocs-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-cat-title-inner .docs-cat-title .docs-cat-icon' => 'background-color:{{VALUE}};'
				],
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => __( 'Category Title Typography', 'betterdocs-pro' ),
                'name'     => 'tabview-list_icon-title_typo_normal',
                'selector' => '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-cat-title-inner .docs-cat-title a h2'
            ]
        );

        $this->end_controls_tab();
        /** Normal State Tab End **/

        /** Hover State Tab Start **/
        $this->start_controls_tab( 
            'box_icons_tab_hover_heading',
            ['label' => esc_html__('Hover', 'betterdocs-pro')]
        );
        
        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'box_icons_hover_border',
				'label'    => __( 'Icon Border', 'betterdocs-pro' ),
				'selector' => '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-cat-title-inner .docs-cat-title .docs-cat-icon:hover'
			]
		);

        $this->add_control(
			'box_icons_hover_color',
			[
				'label' => __( 'Icon Color', 'betterdocs-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
                    '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-cat-title-inner .docs-cat-title .docs-cat-icon:hover' => 'background-color:{{VALUE}};'
				],
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => __( 'Category Title Typography', 'betterdocs-pro' ),
                'name'     => 'tabview-list_icon-title_typo_hover',
                'selector' => '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-cat-title-inner .docs-cat-title a h2:hover'
            ]
        );

        $this->end_controls_tab();
        /** Hover State Tab End **/

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

   /**
    * ----------------------------------------------------------
    * Section: List Settinggs
    * ----------------------------------------------------------
    */
    public function all_box_cat_list(){

        $this->start_controls_section(
            'cat_list_tabview',
            [
                'label' => __('Category List', 'betterdocs-pro'),
                'tab'   => Controls_Manager::TAB_STYLE
            ]
        );


        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'tabview_list_item_typography',
                'selector' => '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container ul li a'
            ]
        );

        $this->add_control(
            'tab_view_list_color',
            [
                'label'     => esc_html__('Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container ul li a' => 'color:{{VALUE}};'
                ]
            ]
        );

        $this->add_control(
            'tabview_list_hover_color',
            [
                'label'     => esc_html__('Hover Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container ul li a:hover' => 'color:{{VALUE}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'tabview_cat_list_margin',
            [
                'label'      => esc_html__('List Item Spacing', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container ul li a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );


        $this->add_responsive_control(
            'tabview_list_area_padding',
            [
                'label'              => esc_html__('List Area Padding', 'betterdocs-pro'),
                'type'               => Controls_Manager::DIMENSIONS,
                'allowed_dimensions' => 'vertical',
                'size_units'         => ['px', 'em', '%'],
                'selectors'          => [
                    '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container ul li a' => 'padding-top: {{TOP}}{{UNIT}}; padding-bottom:{{BOTTOM}}{{UNIT}};'   
                ]
            ]
        );


        $this->add_control(
            'tabview_icon_settings_heading',
            [
                'label'     => esc_html__('Icon', 'betterdocs-pro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );


        $this->add_control(
            'tabview_list_icon_color',
            [
                'label'     => esc_html__('Icon Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container ul li svg' => 'fill:{{VALUE}}'
                ]
            ]
        );

        $this->add_control(
            'tabview_list_icon_hover_color',
            [
                'label'     => esc_html__('Icon Hover Color', 'betterdocs-pro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container ul li svg:hover' => 'fill:{{VALUE}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'tabview_list_icon_size',
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
                'selectors'  => [
                    '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container ul li svg' => 'height:{{SIZE}}{{UNIT}}; width:auto;'
                ]
            ]
        );

        $this->add_responsive_control(
            'tabview_list_icon_spacing',
            [
                'label'      => esc_html__('Spacing', 'betterdocs-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container ul li svg' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

    }

    /**
     * Get Post Categories
     *
     * @return array
     */
    public static function get_terms_list($taxonomy = 'category', $key = 'term_id')
    {
        $options = [];
        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => true,
        ]);

        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $options[$term->{$key}] = $term->name;
            }
        }

        return $options;
    }


    protected function render_editor_script() {
        ?>
            <script>
                jQuery(document).ready(function($) {
                    $('.betterdocs-tab-list a').first().addClass('active');
                    $('.betterdocs-tab-content').first().addClass('active');
                    $('.tab-content-1').addClass('active');
                    $('.betterdocs-tab-list a').click(function(e) {
                        e.preventDefault();
                        $(this).siblings('a').removeClass('active').end().addClass('active');
                        let sel = this.getAttribute('data-toggle-target');
                        $('.betterdocs-tab-content').removeClass('active').filter(sel).addClass('active');
                    });
                });
            </script>
        <?php
    }

}