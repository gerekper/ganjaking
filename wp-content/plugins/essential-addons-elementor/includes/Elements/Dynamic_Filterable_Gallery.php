<?php

namespace Essential_Addons_Elementor\Pro\Elements;

// Elementor Pro Classes
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Typography;
use Elementor\Plugin;
use \Elementor\Widget_Base;
use \Essential_Addons_Elementor\Classes\Helper as HelperClass;
use Essential_Addons_Elementor\Traits\Helper;

if (!defined('ABSPATH')) {
    exit;
} // If this file is called directly, abort.

class Dynamic_Filterable_Gallery extends Widget_Base
{
    use Helper;

	private $page_id;

    public function get_name()
    {
        return 'eael-dynamic-filterable-gallery';
    }

    public function get_title()
    {
        return esc_html__('Dynamic Gallery', 'essential-addons-elementor');
    }

    public function get_icon()
    {
        return 'eaicon-dynamic-gallery';
    }

    public function get_categories()
    {
        return ['essential-addons-elementor'];
    }

    public function get_keywords()
    {
        return [
            'dynamic  gallery',
            'ea dynamic  gallery',
            'ea dynamic filter  gallery',
            'ea dynamic filterable  gallery',
            'image gallery',
            'portfolio',
            'media gallery',
            'post gallery',
            'filterable gallery',
            'dynamic filterable gallery',
            'ea',
            'essential addons'
        ];
    }

    public function get_custom_help_url()
    {
        return 'https://essential-addons.com/elementor/docs/dynamic-filterable-gallery/';
    }

    protected function register_controls()
    {

        /**
         * Filter Gallery Settings
         */
        $this->start_controls_section(
            'eael_section_fg_settings',
            [
                'label' => esc_html__('Dynamic Gallery Settings', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'eael_dynamic_template_Layout',
            [
                'label'   => esc_html__('Template Layout', 'essential-addons-for-elementor-lite'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => $this->get_template_list_for_dropdown(),
            ]
        );

        $this->add_control(
            'eael_fg_filter_duration',
            [
                'label' => esc_html__('Animation Duration (ms)', 'essential-addons-elementor'),
                'type' => Controls_Manager::TEXT,
                'label_block' => false,
                'default' => 500,
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_responsive_control(
            'eael_fg_columns',
            [
                'label' => __('Columns', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
                'prefix_class' => 'elementor-grid%s-',
                'frontend_available' => true,
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'image',
                'exclude' => ['custom'],
                'default' => 'medium',
            ]
        );

        $this->add_control(
            'eael_fg_gallery_layout_mode',
            [
                'label' => esc_html__('Layout Style', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => esc_html__('Grid', 'essential-addons-elementor'),
                    'masonry' => esc_html__('Masonry', 'essential-addons-elementor'),
                ],
            ]
        );

        $this->add_control(
            'eael_fg_grid_item_height',
            [
                'label' => esc_html__('Grid item height', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 300,
                ],
                'range' => [
                    'px' => [
                        'max' => 800,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-wrapper .eael-cards .dynamic-gallery-thumbnail' => 'height: {{SIZE}}px;',
                    '{{WRAPPER}} .eael-filter-gallery-container.grid.eael-hoverer .dynamic-gallery-item-inner' => 'height: {{SIZE}}px;',
                ],
                'condition' => [
                    'eael_fg_gallery_layout_mode' => 'grid',
                ],
            ]
        );

        $this->add_control(
            'eael_fg_grid_style',
            [
                'label' => esc_html__('Item Style', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'eael-hoverer',
                'options' => [
                    'eael-hoverer' => esc_html__('Hoverer', 'essential-addons-elementor'),
                    'eael-cards' => esc_html__('Cards', 'essential-addons-elementor'),
                ],
            ]
        );

	    $this->add_control(
		    'eael_dfg_full_image_clickable',
		    [
			    'label' => __('Full Image Clickable', 'essential-addons-elementor'),
			    'type' => Controls_Manager::SWITCHER,
			    'default' => '',
			    'label_on' => esc_html__('Yes', 'essential-addons-elementor'),
			    'label_off' => esc_html__('No', 'essential-addons-elementor'),
			    'return_value' => 'yes',
			    'condition' => [
					'eael_fg_grid_style' => 'eael-cards'
			    ]
		    ]
	    );

        $this->add_control(
            'eael_show_hover_title',
            [
                'label' => __('Show Title', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => esc_html__('Yes', 'essential-addons-elementor'),
                'label_off' => esc_html__('No', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'eael_show_hover_excerpt',
            [
                'label' => __('Show Content', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => esc_html__('Yes', 'essential-addons-elementor'),
                'label_off' => esc_html__('No', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'eael_fg_grid_hover_style',
            [
                'label' => esc_html__('Hover Style', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'eael-fade-in',
                'options' => [
                    'eael-none' => esc_html__('None', 'essential-addons-elementor'),
                    'eael-slide-up' => esc_html__('Slide In Up', 'essential-addons-elementor'),
                    'eael-fade-in' => esc_html__('Fade In', 'essential-addons-elementor'),
                    'eael-zoom-in' => esc_html__('Zoom In ', 'essential-addons-elementor'),
                ],
                'conditions' => [
	                'relation' => 'or',
	                'terms'    => [
		                [
			                'name'     => 'eael_fg_grid_style',
			                'operator' => '==',
			                'value'    => 'eael-hoverer',
		                ],
		                [
			                'relation' => 'and',
			                'terms'    => [
				                [
					                'name'     => 'eael_fg_grid_style',
					                'operator' => '==',
					                'value'    => 'eael-cards',
				                ],
				                [
					                'name'     => 'eael_dfg_full_image_clickable',
					                'operator' => '==',
					                'value'    => '',
				                ],
			                ],
		                ],
	                ],
                ],
            ]
        );

        $this->add_control(
            'eael_section_fg_zoom_icon_new',
            [
                'label' => esc_html__('Zoom Icon', 'essential-addons-elementor'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'eael_section_fg_zoom_icon',
                'default' => [
                    'value' => 'fas fa-search-plus',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->add_control(
            'eael_section_fg_link_icon_new',
            [
                'label' => esc_html__('Link Icon', 'essential-addons-elementor'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'eael_section_fg_link_icon',
                'default' => [
                    'value' => 'fas fa-link',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->end_controls_section();

        /**
         * Query And Layout Controls!
         * @source includes/elementor-helper.php
         */
        do_action('eael/controls/query', $this);

        /**
         * Filter Gallery Content Settings
         */
        $this->start_controls_section(
            'eael_section_fg_control_settings',
            [
                'label' => esc_html__('Filter Controls', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'show_gallery_filter_controls',
            [
                'label' => __('Show filter controls', 'essential-addons-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    '1' => [
                        'title' => __('Yes', 'essential-addons-elementor'),
                        'icon' => 'fa fa-check',
                    ],
                    '0' => [
                        'title' => __('No', 'essential-addons-elementor'),
                        'icon' => 'eicon-ban',
                    ],
                ],
                'default' => '1',
            ]
        );

        $this->add_control(
            'eael_fg_all_label_text',
            [
                'label' => esc_html__('Gallery All Label', 'essential-addons-elementor'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [ 'active' => true ],
                'default' => 'All',
                'condition' => [
                    'show_gallery_filter_controls' => '1',
                ],
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'eael_post_excerpt',
            [
                'label' => __('Post Excerpt Length', 'essential-addons-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => '12',
            ]
        );

        $this->add_control(
          'eael_post_excerpt_read_more',
          [
              'label' => __('Excerpt Read More', 'essential-addons-elementor'),
              'type' => Controls_Manager::TEXT,
              'dynamic' => [ 'active' => true ],
              'default' => __('Read More', 'essential-addons-elementor'),
              'ai' => [
                'active' => false,
            ],
          ]
        );

        $this->end_controls_section();

        /**
         * Filter Gallery Popup Settings
         */
        $this->start_controls_section(
            'eael_section_fg_popup_settings',
            [
                'label' => esc_html__('Popup Settings', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'eael_fg_show_popup',
            [
                'label' => __('Show Popup', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'true',
                'label_on' => esc_html__('Yes', 'essential-addons-elementor'),
                'label_off' => esc_html__('No', 'essential-addons-elementor'),
                'return_value' => 'true',
            ]
        );

        $this->add_control(
            'eael_fg_show_popup_styles',
            [
                'label' => esc_html__('Popup Styles', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'label_block' => false,
                'default' => 'buttons',
                'options' => [
                    'buttons' => esc_html__('Buttons', 'essential-addons-elementor'),
                    'media' => esc_html__('Media', 'essential-addons-elementor'),
                ],
                'condition' => [
                    'eael_fg_show_popup!' => '',
                ],
            ]
        );

        $this->end_controls_section();

        /**
         * Content Tab: Gallery Load More Button
         */
        $this->start_controls_section(
            'section_pagination',
            [
                'label' => __('Load More Button', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'show_load_more',
            [
                'label' => __('Load More Button', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'false',
                'frontend_available' => true,
                'return_value'  => '1'
            ]
        );

        $this->add_control(
            'eael_fg_loadmore_btn_text',
            [
                'label' => esc_html__('Button text', 'essential-addons-elementor'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __('Load More','essential-addons-elementor'),
                'condition' => [
                    'show_load_more' => '1',
                ],
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'eael_fg_loadmore_btn_top_space',
            [
                'label' => esc_html__('Button top space', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 25,
                ],
                'range' => [
                    'px' => [
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-load-more-button-wrap' => 'margin-top: {{SIZE}}px;',
                ],
                'condition' => [
                    'show_load_more' => '1',
                ],
            ]
        );

        $this->add_responsive_control(
            'load_more_align',
            [
                'label' => __('Alignment', 'essential-addons-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __('Left', 'essential-addons-elementor'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'essential-addons-elementor'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'flex-end' => [
                        'title' => __('Right', 'essential-addons-elementor'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .eael-load-more-button-wrap.dynamic-filter-gallery-loadmore' => 'justify-content: {{VALUE}};',
                ],
                'condition' => [
                    'show_load_more' => '1',
                    'eael_fg_loadmore_btn_text!' => '',
                ],
            ]
        );

        $this->end_controls_section();

        /**
         * Content Tab: Links
         */

        $this->start_controls_section(
            'section_dynamic_gallery_links',
            [
                'label' => __('Links', 'essential-addons-elementor'),
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'eael_fg_grid_style',
                            'operator' => '==',
                            'value' => 'eael-cards',
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                   'name' => 'eael_fg_grid_style',
                                   'operator' => '==',
                                   'value' => 'eael-hoverer',
                                ],
                                [
                                   'name' => 'eael_fg_show_popup',
                                   'operator' => '!=',
                                   'value' => '',
                                ],
                                [
                                    'name' => 'eael_fg_show_popup_styles',
                                    'operator' => '!=',
                                    'value' => 'media',
                                 ],
                                                      
                            ],
                        ],
                    ],       
                ],
            ]
        );

        $this->add_control(
            'attachment_link',
            [
                'label' => __('Link', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'conditions' => $this->title_condition(),
            ]
        );

        $this->add_control(
            'link_nofollow',
            [
                'label' => __('No Follow', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'true',
                'conditions' => $this->title_condition(),
            ]
        );

        $this->add_control(
            'link_target_blank',
            [
                'label' => __('Target Blank', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'true',
                'conditions' => $this->title_condition(),
                'separator' => 'after',
            ]
        );

	    $this->add_control(
		    'image_attachment_link',
		    [
			    'label' => __('Image', 'essential-addons-elementor'),
			    'type' => Controls_Manager::HEADING,
			    'condition' => [
					'eael_dfg_full_image_clickable' => 'yes',
				    'eael_fg_grid_style' => 'eael-cards'
			    ],
		    ]
	    );

	    $this->add_control(
		    'image_link_nofollow',
		    [
			    'label' => __('No Follow', 'essential-addons-elementor'),
			    'type' => Controls_Manager::SWITCHER,
			    'label_on' => __('Yes', 'essential-addons-elementor'),
			    'label_off' => __('No', 'essential-addons-elementor'),
			    'return_value' => 'true',
			    'condition' => [
					'eael_dfg_full_image_clickable' => 'yes',
				    'eael_fg_grid_style' => 'eael-cards'
			    ],
		    ]
	    );

	    $this->add_control(
		    'image_link_target_blank',
		    [
			    'label' => __('Target Blank', 'essential-addons-elementor'),
			    'type' => Controls_Manager::SWITCHER,
			    'label_on' => __('Yes', 'essential-addons-elementor'),
			    'label_off' => __('No', 'essential-addons-elementor'),
			    'return_value' => 'true',
			    'condition' => [
					'eael_dfg_full_image_clickable' => 'yes',
				    'eael_fg_grid_style' => 'eael-cards'
			    ],
			    'separator' => 'after',
		    ]
	    );

        $this->add_control(
            'title_link',
            [
                'label' => __('Title', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    'eael_show_hover_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'title_link_nofollow',
            [
                'label' => __('No Follow', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'true',
                'condition' => [
                    'eael_show_hover_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'title_link_target_blank',
            [
                'label' => __('Target Blank', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'true',
                'condition' => [
                    'eael_show_hover_title' => 'yes',
                ],
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'read_more_link',
            [
                'label' => __('Read More', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    'eael_show_hover_excerpt' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'read_more_link_nofollow',
            [
                'label' => __('No Follow', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'true',
                'condition' => [
                    'eael_show_hover_excerpt' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'read_more_link_target_blank',
            [
                'label' => __('Target Blank', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'essential-addons-elementor'),
                'label_off' => __('No', 'essential-addons-elementor'),
                'return_value' => 'true',
                'condition' => [
                    'eael_show_hover_excerpt' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        /**
         * -------------------------------------------
         * Tab Style (Filterable Gallery Style)
         * -------------------------------------------
         */
        $this->start_controls_section(
            'eael_section_fg_style_settings',
            [
                'label' => esc_html__('General Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'eael_fg_bg_color',
            [
                'label' => esc_html__('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-wrapper' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_fg_container_padding',
            [
                'label' => esc_html__('Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_fg_container_margin',
            [
                'label' => esc_html__('Margin', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'eael_fg_border',
                'label' => esc_html__('Border', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-filter-gallery-wrapper',
            ]
        );

        $this->add_control(
            'eael_fg_border_radius',
            [
                'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-wrapper' => 'border-radius: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'eael_fg_shadow',
                'selector' => '{{WRAPPER}} .eael-filter-gallery-wrapper',
            ]
        );

        $this->end_controls_section();

        /**
         * -------------------------------------------
         * Tab Style (Filterable Gallery Control Style)
         * -------------------------------------------
         */
        $this->start_controls_section(
            'eael_section_fg_control_style_settings',
            [
                'label' => esc_html__('Control Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_responsive_control(
            'eael_fg_control_padding',
            [
                'label' => esc_html__('Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-control ul li.control' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_fg_control_margin',
            [
                'label' => esc_html__('Margin', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-control ul li.control' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_fg_control_typography',
                'selector' => '{{WRAPPER}} .eael-filter-gallery-control ul li.control',
            ]
        );
        // Tabs
        $this->start_controls_tabs('eael_fg_control_tabs');

        // Normal State Tab
        $this->start_controls_tab('eael_fg_control_normal', ['label' => esc_html__('Normal', 'essential-addons-elementor')]);

        $this->add_control(
            'eael_fg_control_normal_text_color',
            [
                'label' => esc_html__('Text Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#444',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-control ul li.control' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'eael_fg_control_normal_bg_color',
            [
                'label' => esc_html__('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-control ul li.control' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'eael_fg_control_normal_border',
                'label' => esc_html__('Border', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-filter-gallery-control ul li.control',
            ]
        );

        $this->add_control(
            'eael_fg_control_border_radius',
            [
                'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 20,
                ],
                'range' => [
                    'px' => [
                        'max' => 30,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-control ul li.control' => 'border-radius: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'eael_fg_control_shadow',
                'selector' => '{{WRAPPER}} .eael-filter-gallery-control ul li.control',
                'separator' => 'before',
            ]
        );

        $this->end_controls_tab();

        // Active State Tab
        $this->start_controls_tab('eael_cta_btn_hover', ['label' => esc_html__('Active', 'essential-addons-elementor')]);

        $this->add_control(
            'eael_fg_control_active_text_color',
            [
                'label' => esc_html__('Text Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-control ul li.control.active' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'eael_fg_control_active_bg_color',
            [
                'label' => esc_html__('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#3F51B5',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-control ul li.control.active' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'eael_fg_control_active_border',
                'label' => esc_html__('Border', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-filter-gallery-control ul li.control.active',
            ]
        );

        $this->add_control(
            'eael_fg_control_active_border_radius',
            [
                'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 20,
                ],
                'range' => [
                    'px' => [
                        'max' => 30,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-control ul li.control.active' => 'border-radius: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'eael_fg_control_active_shadow',
                'selector' => '{{WRAPPER}} .eael-filter-gallery-control ul li.control.active',
                'separator' => 'before',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        /**
         * -------------------------------------------
         * Tab Style (Filterable Gallery Item Style)
         * -------------------------------------------
         */
        $this->start_controls_section(
            'eael_section_fg_item_style_settings',
            [
                'label' => esc_html__('Item Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'eael_fg_item_bg_color',
            [
                'label' => __('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .dynamic-gallery-item' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .eael-filter-gallery-container .dynamic-gallery-item .dynamic-gallery-thumbnail' => 'background-color: {{VALUE}}',
                ],

            ]
        );

        $this->add_responsive_control(
            'eael_fg_item_container_padding',
            [
                'label' => esc_html__('Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .dynamic-gallery-item-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_fg_item_container_margin',
            [
                'label' => esc_html__('Margin', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .dynamic-gallery-item-inner' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'eael_fg_item_border',
                'label' => esc_html__('Border', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-filter-gallery-container .dynamic-gallery-item-inner',
            ]
        );

        $this->add_control(
            'eael_fg_item_border_radius',
            [
                'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .dynamic-gallery-item-inner' => 'border-radius: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'eael_fg_item_shadow',
                'selector' => '{{WRAPPER}} .eael-filter-gallery-container .dynamic-gallery-item-inner',
            ]
        );

        $this->end_controls_section();

        /**
         * -------------------------------------------
         * Tab Style (Card buttons style)
         * -------------------------------------------
         */
        $this->start_controls_section(
            'eael_section_fg_card_item_button_style',
            [
                'label' => esc_html__('Buttons Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'eael_fg_grid_hover_style' => 'eael-none',
                    'eael_fg_show_popup_styles' => 'buttons',
                    'eael_fg_grid_style' => 'eael-cards',
                ],
            ]
        );

        $this->start_controls_tabs('dynamic_gallery_card_button_styles');
        $this->start_controls_tab(
            'dynamic_gallery_card_button_normal',
            [
                'label' => __('Normal', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'eael_fg_card_item_icon_bg_color',
            [
                'label' => esc_html__('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ff622a',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container.grid.eael-cards .card-buttons > a' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'eael_fg_card_item_icon_color',
            [
                'label' => esc_html__('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container.grid.eael-cards .card-buttons > a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_fg_card_item_icon_size',
            [
                'label' => esc_html__('Icon size', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 40,
                ],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container.grid.eael-cards .card-buttons > a' => 'height: {{SIZE}}px; width:  {{SIZE}}px; line-height:  {{SIZE}}px;',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_fg_card_item_icon_font_size',
            [
                'label' => esc_html__('Icon font size', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 18,
                ],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container.grid.eael-cards .card-buttons > a' => 'font-size: {{SIZE}}px;'
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_fg_card_item_icon_border',
            [
                'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 50,
                ],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container.grid.eael-cards .card-buttons > a' => 'border-radius: {{SIZE}}px;',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'dynamic_gallery_card_button_hover',
            [
                'label' => __('Hover', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'eael_fg_card_item_icon_hover_bg_color',
            [
                'label' => esc_html__('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ff622a',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container.grid.eael-cards .card-buttons > a' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'eael_fg_card_item_hover_icon_color',
            [
                'label' => esc_html__('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container.grid.eael-cards .card-buttons > a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
        /**
         * --------------------------------------------------
         * Tab Style (Filterable Gallery Item Caption Style)
         * --------------------------------------------------
         */
        $this->start_controls_section(
            'eael_section_fg_item_cap_style_settings',
            [
                'label' => esc_html__('Item Caption Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'eael_fg_grid_hover_style!' => 'eael-none',
                ],
            ]
        );

        $this->add_control(
            'eael_fg_item_cap_bg_color',
            [
                'label' => esc_html__('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => 'rgba(0,0,0,0.7)',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .dynamic-gallery-item .caption' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_fg_item_cap_container_padding',
            [
                'label' => esc_html__('Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .dynamic-gallery-item .caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'eael_fg_item_cap_border',
                'label' => esc_html__('Border', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-filter-gallery-container .dynamic-gallery-item .caption',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'eael_fg_item_cap_shadow',
                'selector' => '{{WRAPPER}} .eael-filter-gallery-container .dynamic-galley-item .caption'
            ]
        );

        $this->add_responsive_control(
            'eael_fg_item_caption_button_alignment',
            [
                'label' => esc_html__('Caption Button Alignment', 'essential-addons-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => true,
                'separator' => 'before',
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .caption .buttons'   => 'text-align:{{VALUE}};'
                ],
                'condition' => [
                    'eael_fg_grid_style' => 'eael-cards'
                ]
            ]
        );

        $this->add_responsive_control(
            'eael_fg_item_caption_alignment',
            [
                'label' => esc_html__('Caption Alignment', 'essential-addons-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => true,
                'separator' => 'before',
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .caption .item-content' => 'text-align:{{VALUE}};',
                    '{{WRAPPER}} .eael-filter-gallery-container .caption .buttons'   => 'text-align:{{VALUE}};'
                ],
                'condition' => [
                    'eael_fg_grid_style' => 'eael-hoverer'
                ]
            ]
        );

        $this->add_control(
            'eael_fg_item_caption_title_style',
            [
                'label' => esc_html__('Caption Title', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'eael_fg_grid_style' => 'eael-hoverer'
                ]
            ]
        );

        $this->add_control(
            'eael_fg_item_caption_title_color',
            [
                'label' => esc_html__('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .caption .item-content .title a' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .caption .item-content .title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_fg_item_caption_title_typography',
                'selector' => '{{WRAPPER}} .eael-filter-gallery-container .caption .item-content .title, {{WRAPPER}} .eael-filter-gallery-container .caption .item-content .title a',
                'eael_fg_grid_style' => 'eael-hoverer'
            ]
        );

        $this->add_control(
            'eael_fg_item_caption_content_style',
            [
                'label' => esc_html__('Caption Content', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'eael_fg_grid_style' => 'eael-hoverer'
            ]
        );

        $this->add_control(
            'eael_fg_item_caption_content_color',
            [
                'label' => esc_html__('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .caption .item-content' => 'color: {{VALUE}};',
                    'eael_fg_grid_style' => 'eael-hoverer'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_fg_item_caption_content_typography',
                'selector' => '{{WRAPPER}} .caption .item-content',
                'eael_fg_grid_style' => 'eael-hoverer'
            ]
        );

        $this->add_control(
            'eael_fg_item_caption_readmore_style',
            [
                'label' => esc_html__('Caption Read More', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'eael_fg_grid_style' => 'eael-hoverer'
            ]
        );

        $this->add_control(
            'eael_fg_item_caption_readmore_color',
            [
                'label' => esc_html__('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .caption .item-content .eael_post_excerpt_read_more' => 'color: {{VALUE}};',
                    'eael_fg_grid_style' => 'eael-hoverer'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_fg_item_caption_readmore_typography',
                'selector' => '{{WRAPPER}} .caption .item-content .eael_post_excerpt_read_more',
                'eael_fg_grid_style' => 'eael-hoverer'
            ]
        );

        $this->add_control(
            'eael_fg_item_caption_hover_icon',
            [
                'label' => esc_html__('Hover Icon', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->start_controls_tabs('dynamic_gallery_button_styles');
        $this->start_controls_tab(
            'dynamic_gallery_button_normal',
            [
                'label' => __('Normal', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'eael_fg_item_icon_bg_color',
            [
                'label' => esc_html__('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ff622a',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .caption > a.popup-media > i' => 'background: {{VALUE}};',
                    '{{WRAPPER}} .eael-filter-gallery-container .caption .buttons a' => 'background: {{VALUE}};'
                ],
            ]
        );

        $this->add_control(
            'eael_fg_item_icon_color',
            [
                'label' => esc_html__('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .caption > a.popup-media > i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .eael-filter-gallery-container .caption .buttons a' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .eael-filter-gallery-container .caption .buttons a svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_fg_item_icon_size',
            [
                'label' => esc_html__('Icon size', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 40,
                ],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .caption > a.popup-media > i' => 'height: {{SIZE}}px; width:  {{SIZE}}px; line-height:  {{SIZE}}px;',
                    '{{WRAPPER}} .eael-filter-gallery-container .buttons .eael-dnmcg-svg-icon' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eael-filter-gallery-container .caption .buttons a' => 'height: {{SIZE}}px; width:  {{SIZE}}px; line-height:  {{SIZE}}px;',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_fg_item_icon_font_size',
            [
                'label' => esc_html__('Icon font size', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 18,
                ],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .caption > a.popup-media > i' => 'font-size: {{SIZE}}px;',
                    '{{WRAPPER}} .eael-filter-gallery-container .caption .buttons .eael-dnmcg-svg-icon' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eael-filter-gallery-container .caption .buttons a > i' => 'font-size: {{SIZE}}px;',
                    '{{WRAPPER}} .eael-filter-gallery-container .caption .buttons a svg' => 'width: {{SIZE}}px;height: {{SIZE}}px;line-height: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_fg_item_icon_border',
            [
                'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 50,
                ],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .caption > a.popup-media > i' => 'border-radius: {{SIZE}}px;',
                    '{{WRAPPER}} .eael-filter-gallery-container .caption .buttons a' => 'border-radius: {{SIZE}}px;',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'dynamic_gallery_button_hover',
            [
                'label' => __('Hover', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'eael_fg_item_icon_hover_bg_color',
            [
                'label' => esc_html__('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ff622a',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .caption > a.popup-media > i:hover' => 'background: {{VALUE}};',
                    '{{WRAPPER}} .eael-filter-gallery-container .caption .buttons a:hover' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'eael_fg_item_hover_icon_color',
            [
                'label' => esc_html__('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .caption > a.popup-media > i:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .eael-filter-gallery-container .caption .buttons a:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .eael-filter-gallery-container .caption .buttons a:hover svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        /**
         * -------------------------------------------
         * Tab Style (Filterable Gallery Item Content Style)
         * -------------------------------------------
         */
        $this->start_controls_section(
            'eael_section_fg_item_content_style_settings',
            [
                'label' => esc_html__('Item Content Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'eael_fg_grid_style' => 'eael-cards',
                ],
            ]
        );

        $this->add_control(
            'eael_fg_item_content_bg_color',
            [
                'label' => esc_html__('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#f2f2f2',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container.eael-cards .item-content' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_fg_item_content_container_padding',
            [
                'label' => esc_html__('Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container.eael-cards .item-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'eael_fg_item_content_border',
                'label' => esc_html__('Border', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-filter-gallery-container.eael-cards .item-content',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'eael_fg_item_content_shadow',
                'selector' => '{{WRAPPER}} .eael-filter-gallery-container.eael-cards .item-content',
            ]
        );

        $this->add_control(
            'eael_fg_item_content_title_typography_settings',
            [
                'label' => esc_html__('Title Typography', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'eael_fg_item_content_title_color',
            [
                'label' => esc_html__('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#F56A6A',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container.eael-cards .item-content .title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'eael_fg_item_content_title_hover_color',
            [
                'label' => esc_html__('Hover Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container.eael-cards .item-content .title a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_fg_item_content_title_typography',
                'selector' => '{{WRAPPER}} .eael-filter-gallery-container.eael-cards .item-content .title a',
            ]
        );

        $this->add_control(
            'eael_fg_item_content_text_typography_settings',
            [
                'label' => esc_html__('Content Typography', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'eael_fg_item_content_text_color',
            [
                'label' => esc_html__('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#444',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container.eael-cards .item-content p' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_fg_item_content_text_typography',
                'selector' => '{{WRAPPER}} .eael-filter-gallery-container.eael-cards .item-content p',
            ]
        );

        $this->add_control(
          'eael_fg_item_readmore_text_typography_settings',
          [
              'label' => esc_html__('Read More Typography', 'essential-addons-elementor'),
              'type' => Controls_Manager::HEADING,
              'separator' => 'before',
          ]
      );

      $this->add_control(
          'eael_fg_item_content_readmore_color',
          [
              'label' => esc_html__('Color', 'essential-addons-elementor'),
              'type' => Controls_Manager::COLOR,
              'default' => '#444',
              'selectors' => [
                  '{{WRAPPER}} .eael-filter-gallery-container.eael-cards .item-content .eael_post_excerpt_read_more' => 'color: {{VALUE}};',
              ],
          ]
      );

      $this->add_group_control(
          Group_Control_Typography::get_type(),
          [
              'name' => 'eael_fg_item_content_readmore_typography',
              'selector' => '{{WRAPPER}} .eael-filter-gallery-container.eael-cards .item-content .eael_post_excerpt_read_more',
          ]
      );

        $this->add_responsive_control(
            'eael_fg_item_content_alignment',
            [
                'label' => esc_html__('Content Alignment', 'essential-addons-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => true,
                'separator' => 'before',
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container.eael-cards .item-content' => 'text-align:{{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        /**
         * Load More Button Style Controls!
         */
        do_action('eael/controls/load_more_button_style', $this);
    }

    protected function title_condition(){
        return  [
            'relation' => 'or',
            'terms' => [
                [
                    'name' => 'eael_fg_grid_style',
                    'operator' => '==',
                    'value' => 'eael-hoverer',
                ],
                [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'eael_fg_grid_style',
                            'operator' => '==',
                            'value' => 'eael-cards',
                        ],
                        [
                            'name' => 'eael_fg_show_popup',
                            'operator' => '!=',
                            'value' => '',
                        ],
                        [
                            'name' => 'eael_fg_show_popup_styles',
                            'operator' => '!=',
                            'value' => 'media',
                        ],
                                                
                    ],  
                ]  
                                        
            ],
                        
        ];
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $settings = HelperClass::fix_old_query($settings);
        $args = HelperClass::get_query_args($settings);
        
        $this->add_render_attribute(
            'eael_dynamic_filter_gallery_wrap',
            [
                'id' => 'eael-filter-gallery-wrapper-' . $this->get_id(),
                'class' => [
                    'eael-filter-gallery-wrapper',
                ],
            ]
        );

        $this->add_render_attribute(
            'eael_dynamic_gallery_container',
            [
                'class' => [
                    'eael-filter-gallery-container',
                    'eael-post-appender',
                    'eael-post-appender-' . $this->get_id(),
                    $settings['eael_fg_gallery_layout_mode'],
                    esc_attr($settings['eael_fg_grid_style']),
                    esc_attr($settings['eael_fg_columns']),
                ],
                'data-settings' => wp_json_encode([
                    'item_style' => $settings['eael_fg_grid_style'],
                    'duration' => (!empty($settings['eael_fg_filter_duration'])) ? $settings['eael_fg_filter_duration'] : '500',
                    'layout_mode' => $settings['eael_fg_gallery_layout_mode'],
                ]),
            ]
        );

        echo '<div ' . $this->get_render_attribute_string('eael_dynamic_filter_gallery_wrap') . '>';
        // filter controls
        if (1 == $settings['show_gallery_filter_controls']) {
            echo '<div class="eael-filter-gallery-control">
                    <ul>
                        <li class="control active dynamic-gallery-category " data-filter="*">' . (isset($settings['eael_fg_all_label_text']) && !empty($settings['eael_fg_all_label_text']) ? esc_attr($settings['eael_fg_all_label_text']) : 'All') . '</li>';

            if (!empty($args['tax_query'])) {
                foreach ($args['tax_query'] as $taxonomy) {
                    if (!empty($taxonomy['terms'])) {
                        foreach ($taxonomy['terms'] as $term_id) {
                            $term = get_term($term_id, $taxonomy['taxonomy']);
                            echo '<li class="control dynamic-gallery-category " data-termid="' . esc_attr($term->term_id) . '" data-taxonomy="' . esc_attr($term->taxonomy) . '" data-filter=".' . esc_attr(urldecode($term->slug)) . '">' . ucfirst($term->name) . '</li>';
                        }
                    }
                }
            }
            echo '</ul>
                </div>';
        }
         $settings['eael_section_fg_zoom_icon'] = (isset($settings['__fa4_migrated']['eael_section_fg_zoom_icon_new']) || empty($settings['eael_section_fg_zoom_icon']) ? $settings['eael_section_fg_zoom_icon_new']['value'] : $settings['eael_section_fg_zoom_icon']);
         $settings['eael_section_fg_link_icon'] = (isset($settings['__fa4_migrated']['eael_section_fg_link_icon_new']) || empty($settings['eael_section_fg_link_icon']) ? $settings['eael_section_fg_link_icon_new']['value'] : $settings['eael_section_fg_link_icon']);
         $settings['show_load_more_text'] = $settings['eael_fg_loadmore_btn_text'];
         $settings['layout_mode'] = isset($settings['layout_mode']) ? $settings['layout_mode'] : 'masonry';
         $settings['eael_show_hover_title'] = $settings['eael_show_hover_title'];
         $settings['eael_show_hover_excerpt'] = $settings['eael_show_hover_excerpt'];
         $settings['eael_post_excerpt_read_more'] = $settings['eael_post_excerpt_read_more'];

        // content
            echo '<div ' . $this->get_render_attribute_string('eael_dynamic_gallery_container') . '>';
                $template = $this->get_template($this->get_settings('eael_dynamic_template_Layout'));
	            $found_posts = 0;

                if(file_exists($template)){

                    $query = new \WP_Query($args);

	                if ( $query->have_posts() ) {
		                $found_posts      = $query->found_posts;
		                $ppp              = empty( $args['posts_per_page'] ) ? get_option( 'posts_per_page' ) : $args['posts_per_page'];
		                $max_page         = ceil( $found_posts / absint( $ppp ) );
		                $args['max_page'] = $max_page;

		                while ( $query->have_posts() ) {
			                $query->the_post();
			                include( $template );
		                }
	                } else {
		                _e( '<p class="no-posts-found">No posts found!</p>', 'essential-addons-elementor' );
	                }
                } else {
                    _e('<p class="no-posts-found">No Layout Found!</p>', 'essential-addons-elementor');
                }
        
                wp_reset_postdata();
        
            echo '</div>';

            // load more
        // normalize settigns for load more
        $settings['layout_mode'] = 'masonry'; // in master load more has static masonry text in layout_mode attr in loadmore button.
        $settings['show_load_more_text'] = $settings['eael_fg_loadmore_btn_text'];
        $template = $this->get_template($settings['eael_dynamic_template_Layout']);
        $settings['loadable_file_name'] = $this->get_filename_only($template);


	    if ( method_exists( $this, 'print_load_more_button' ) && $found_posts > $args['posts_per_page'] ) {
		    $dir_name = method_exists( $this, 'get_temp_dir_name' ) ? $this->get_temp_dir_name( $settings[ 'loadable_file_name' ] ) : "pro";
		    $this->print_load_more_button( $settings, $args, $dir_name );
	    }
        echo '</div>';

        if (Plugin::instance()->editor->is_edit_mode()) {
            $this->render_editor_script();
        }
    }

    protected function render_editor_script()
    {
        echo '<script type="text/javascript">
			jQuery(document).ready(function($) {
				$(".eael-filter-gallery-container").each(function() {
                    var $scope = jQuery(".elementor-element-' . $this->get_id() . '"),
                        $gallery = $(".eael-filter-gallery-container", $scope),
                        $settings = $gallery.data("settings"),
                        $layout_mode = $settings.layout_mode === "masonry" ? "masonry" : "fitRows";
                    
					var $isotope_gallery = $gallery.isotope({
                        itemSelector: ".dynamic-gallery-item",
                        layoutMode: $layout_mode,
                        percentPosition: true,
                        stagger: 30,
                        transitionDuration: $settings.duration + "ms",
                    });

                    $isotope_gallery.imagesLoaded().progress(function() {
                        $isotope_gallery.isotope("layout");
                    });

                    $(".dynamic-gallery-item", $gallery).resize(function() {
                        $isotope_gallery.isotope("layout");
                    });

					$scope.on("click", ".control", function(e) {
                        e.preventDefault();
                
                        var filterValue = $(this).data("filter");
                
                        $(this).siblings().removeClass("active");
                        $(this).addClass("active");
                        
                        $isotope_gallery.isotope({
                            filter: filterValue
                        });
                    });

				});
			});
		</script>';
    }
}
