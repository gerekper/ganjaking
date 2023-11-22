<?php

namespace ElementPack\Traits;

use Elementor\Controls_Manager;

defined('ABSPATH') || die();
trait Global_Terms_Query_Controls {
    protected function render_terms_query_controls($taxonomy = 'category') {

        $this->start_controls_section(
            'section_term_query',
            [
                'label' => __('Query', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'display_category',
            [
                'label' => __('Type', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'all',
                'options' => [
                    'all' => __('All', 'bdthemes-element-pack'),
                    'parents' => __('Only Parents', 'bdthemes-element-pack'),
                    'child' => __('Only Child', 'bdthemes-element-pack')
                ],
            ]
        );

        // $this->add_control(
        // 	'item_limit',
        // 	[
        // 		'label' => esc_html__('Item Limit', 'bdthemes-element-pack'),
        // 		'type'  => Controls_Manager::SLIDER,
        // 		'range' => [
        // 			'px' => [
        // 				'min' => 1,
        // 				'max' => 20,
        // 			],
        // 		],
        // 		'default' => [
        // 			'size' => 6,
        // 		],
        // 	]
        // );

        $this->start_controls_tabs(
            'tabs_terms_include_exclude',
            [
                'condition' => ['display_category' => 'all']
            ]
        );
        $this->start_controls_tab(
            'tab_term_include',
            [
                'label' => __('Include', 'bdthemes-element-pack'),
                'condition' => ['display_category' => 'all']
            ]
        );

        $this->add_control(
            'cats_include_by_id',
            [
                'label' => __('Categories', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'label_block' => true,
                'condition' => [
                    'display_category' => 'all'
                ],
                'options' => element_pack_get_terms($taxonomy),
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_term_exclude',
            [
                'label' => __('Exclude', 'bdthemes-element-pack'),
                'condition' => ['display_category' => 'all']
            ]
        );

        $this->add_control(
            'cats_exclude_by_id',
            [
                'label' => __('Categories', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'label_block' => true,
                'condition' => [
                    'display_category' => 'all'
                ],
                'options' => element_pack_get_terms($taxonomy),
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_control(
            'child_cats_notice',
            [
                'type'              => Controls_Manager::RAW_HTML,
                'raw'               => __('WARNING!, Must Select Parent Category from Child Categories of.', 'bdthemes-element-pack'),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
                'condition' => [
                    'display_category' => 'child',
                    'parent_cats' => 'none'
                ],
            ],
        );
        $this->add_control(
            'parent_cats',
            [
                'label' => __('Child Categories of', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => element_pack_get_only_parent_cats($taxonomy),
                'condition' => [
                    'display_category' => 'child'
                ],
            ]
        );


        $this->add_control(
            'orderby',
            [
                'label' => __('Order By', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'name',
                'options' => [
                    'name'       => esc_html__('Name', 'bdthemes-element-pack'),
                    'count'  => esc_html__('Count', 'bdthemes-element-pack'),
                    'slug' => esc_html__('Slug', 'bdthemes-element-pack'),
                    // 'menu_order' => esc_html__('Menu Order', 'bdthemes-element-pack'),
                    // 'rand'       => esc_html__('Random', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => __('Order', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'desc',
                'options' => [
                    'desc' => __('Descending', 'bdthemes-element-pack'),
                    'asc' => __('Ascending', 'bdthemes-element-pack'),
                ],
            ]
        );
        $this->add_control(
            'hide_empty',
            [
                'label'         => __('Hide Empty', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SWITCHER,
            ]
        );

        $this->end_controls_section();
    }
}
