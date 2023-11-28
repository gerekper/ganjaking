<?php

namespace Essential_Addons_Elementor\Pro\Elements;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

use \Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use \Elementor\Widget_Base;
use \Essential_Addons_Elementor\Classes\Helper as HelperClass;
use Essential_Addons_Elementor\Traits\Helper;

class Post_Block extends Widget_Base
{
    use Helper;

	private $page_id;

    public function get_name()
    {
        return 'eael-post-block';
    }

    public function get_title()
    {
        return __('Post Block', 'essential-addons-elementor');
    }

    public function get_icon()
    {
        return 'eaicon-post-block';
    }

    public function get_categories()
    {
        return ['essential-addons-elementor'];
    }

    public function get_keywords()
    {
        return [
            'post block',
            'ea postblock',
            'ea post block',
            'post layout',
            'flex layout',
            'post grid',
            'post showcase',
            'portfolio',
            'gallery',
            'ea',
            'essential addons'
        ];
    }

    public function get_custom_help_url()
    {
        return 'https://essential-addons.com/elementor/docs/post-block/';
    }

    protected function register_controls()
    {
        /**
         * Query And Layout Controls!
         * @source includes/elementor-helper.php
         */
        do_action('eael/controls/query', $this);
	    do_action('eael/controls/layout', $this);


        $this->start_controls_section(
            'section_post_block_links',
            [
                'label' => __('Links', 'essential-addons-elementor'),
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                       [
                          'name' => 'eael_show_image',
                          'operator' => '==',
                          'value' => 'yes',
                       ],
                       [
                          'name' => 'eael_show_title',
                          'operator' => '==',
                          'value' => 'yes',
                       ],
                       [
                          'name' => 'eael_show_read_more_button',
                          'operator' => '==',
                          'value' => 'yes',
                       ],
                       
                    ],
                 ],
            ]
        );

        $this->add_control(
            'image_link',
            [
                'label' => __('Image', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    'eael_show_image' => 'yes',
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
                    'eael_show_image' => 'yes',
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
                    'eael_show_image' => 'yes',
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
                    'eael_show_title' => 'yes',
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
                    'eael_show_title' => 'yes',
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
                    'eael_show_title' => 'yes',
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
                    'eael_show_read_more_button' => 'yes',
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
                    'eael_show_read_more_button' => 'yes',
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
                    'eael_show_read_more_button' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'eael_section_post_block_style',
            [
                'label' => __('General', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'eael_post_block_bg_color',
            [
                'label' => __('Post Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .eael-post-block-item' => 'background-color: {{VALUE}}',
                ],
                'condition' => [
                    'grid_style!' => 'post-block-style-overlay',
                ],

            ]
        );

        /*$this->add_control(
		'eael_thumbnail_overlay_color',
		[
		'label' => __( 'Thumbnail Overlay Color', 'essential-addons-elementor' ),
		'type' => Controls_Manager::COLOR,
		'default' => 'rgba(0,0,0, .5)',
		'selectors' => [
		'{{WRAPPER}} .eael-entry-overlay, {{WRAPPER}} .eael-post-block.post-block-style-overlay .eael-entry-wrapper' => 'background-color: {{VALUE}}'
		]

		]
		);*/

        $this->add_responsive_control(
            'eael_post_block_spacing',
            [
                'label' => esc_html__('Spacing Between Items', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-block-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'eael_post_block_layout' => 'post-block-layout-block'
                ],
            ]
        );

        $this->add_control(
            'eael_post_tiled_gap',
            [
                'label' => esc_html__('Grid Gap', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units'    => ['px', '%', 'em'],
                'range' => [
                    'px' => ['max' => 50],
                    '%'    => ['max'    => 50]
                ],
	            'default' => [
		            'size' => 20,
	            ],
                'selectors' => [
                    '{{WRAPPER}} .post-block-layout-tiled .eael-post-block-grid' => 'grid-gap: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'eael_post_block_layout' => 'post-block-layout-tiled'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'eael_post_block_border',
                'label' => esc_html__('Border', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-post-block-item',
            ]
        );

        $this->add_control(
            'eael_post_block_border_radius',
            [
                'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .eael-post-block-item' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'eael_post_block_box_shadow',
                'selector' => '{{WRAPPER}} .eael-post-block-item',
            ]
        );

        $this->add_responsive_control(
            'eael_post_content_box_padding',
            [
                'label' => esc_html__('Content Box Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .eael-entry-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0px {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .eael-entry-footer' => 'padding: 0px {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'grid_style!' => 'post-block-style-overlay',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_post_overlay_content_box_padding',
            [
                'label' => esc_html__('Content Box Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .eael-entry-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'grid_style' => 'post-block-style-overlay',
                ],
            ]
        );

        $this->end_controls_section();


        /**
         * Thumbnail Image Control
         */
        $this->start_controls_section(
            'eael_section_post_block_thumbnail_style',
            [
                'label' => __('Thumbnail Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'grid_style'    => 'post-block-style-default'
                ]
            ]
        );
        $this->add_control(
            'eael_post_block_thumbnail_border',
            [
                'label' => __('Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-block-item .eael-entry-media' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'eael_post_block_thumbnail_border',
                'label' => __('Border', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-post-block-item .eael-entry-media',
            ]
        );



        $this->end_controls_section();

        /**
         * Color & Typography
         */
        $this->start_controls_section(
            'eael_section_typography',
            [
                'label' => __('Color & Typography', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'eael_post_block_title_style',
            [
                'label' => __('Title Style', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'eael_post_block_title_color',
            [
                'label' => __('Title Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eael-entry-title, {{WRAPPER}} .eael-entry-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'eael_post_block_title_hover_color',
            [
                'label' => __('Title Hover Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eael-entry-title:hover, {{WRAPPER}} .eael-entry-title a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_post_block_title_alignment',
            [
                'label' => __('Title Alignment', 'essential-addons-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-entry-title' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_post_block_title_typography',
                'label' => __('Typography', 'essential-addons-elementor'),
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
                'selector' => '{{WRAPPER}} .eael-entry-title > a',
            ]
        );

        $this->add_responsive_control(
            'eael_post_title_spacing',
            [
                'label' => esc_html__('Title Spacing', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .eael-entry-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'eael_post_block_excerpt_style',
            [
                'label' => __('Excerpt Style', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'eael_post_block_excerpt_color',
            [
                'label' => __('Excerpt Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-grid-post-excerpt p' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_post_block_excerpt_alignment',
            [
                'label' => __('Excerpt Alignment', 'essential-addons-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-grid-post-excerpt p' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_post_block_excerpt_typography',
                'label' => __('Excerpt Typography', 'essential-addons-elementor'),
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
                'selector' => '{{WRAPPER}} .eael-grid-post-excerpt p',
            ]
        );

        $this->add_responsive_control(
            'eael_post_excerpt_spacing',
            [
                'label' => esc_html__('Excerpt Spacing', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .eael-grid-post-excerpt p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'content_height',
            [
                'label' => esc_html__('Content Height', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units'    => ['px', '%', 'em'],
                'range' => [
                    'px' => ['max' => 300],
                    '%'    => ['max'    => 100]
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-block-item .eael-entry-wrapper' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'eael_post_block_meta_style',
            [
                'label' => __('Meta Style', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'eael_post_block_meta_color',
            [
                'label' => __('Meta Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-entry-meta, {{WRAPPER}} .eael-entry-meta a, {{WRAPPER}} .eael-entry-meta ul li i, {{WRAPPER}} .eael-entry-meta ul li a, {{WRAPPER}} .eael-entry-meta > span, {{WRAPPER}} .eael-entry-meta > span a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_post_block_meta_alignment_footer',
            [
                'label' => __('Meta Alignment', 'essential-addons-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __('Left', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'flex-end' => [
                        'title' => __('Right', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-entry-footer' => 'justify-content: {{VALUE}};',
                ],
                'condition' => [
                    'meta_position' => 'meta-entry-footer',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_post_block_meta_alignment_header',
            [
                'label' => __('Meta Alignment', 'essential-addons-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', 'essential-addons-elementor'),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-entry-meta' => 'text-align: {{VALUE}};',
                ],
                'condition' => [
                    'meta_position' => 'meta-entry-header',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_post_block_meta_typography',
                'label' => __('Meta Typography', 'essential-addons-elementor'),
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
                'selector' => '{{WRAPPER}} .eael-entry-meta > div, {{WRAPPER}} .eael-entry-meta > span, {{WRAPPER}} .eael-entry-meta ul li i, {{WRAPPER}} .eael-entry-meta ul li a',
            ]
        );

        $this->end_controls_section();

        /**
         * Style tab: terms style
         */
        $this->start_controls_section(
            'section_meta_terms_style',
            [
                'label' => __('Terms Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'eael_post_block_terms_color',
            [
                'label' => __('Terms Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .post-meta-categories li, {{WRAPPER}} .post-meta-categories li a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_post_block_terms_typography',
                'label' => __('Meta Typography', 'essential-addons-elementor'),
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
                'selector' => '{{WRAPPER}} .post-meta-categories li, {{WRAPPER}} .post-meta-categories li a',
            ]
        );

        $this->add_control(
            'eael_post_carousel_terms_margin',
            [
                'label' => __('Margin', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .post-meta-categories' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();

        /**
         * Read More Button Style Controls
         */
        do_action('eael/controls/read_more_button_style', $this);

        /**
         * Load More Button Style Controls!
         */
        do_action('eael/controls/load_more_button_style', $this);

        /**
         * Card Hover Style
         */
        $this->start_controls_section(
            'eael_section_post_block_hover_card',
            [
                'label' => __('Hover Card Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'eael_post_block_hover_animation',
            [
                'label' => esc_html__('Animation', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'fade-in',
                'options' => [
                    'none' => esc_html__('None', 'essential-addons-elementor'),
                    'fade-in' => esc_html__('FadeIn', 'essential-addons-elementor'),
                    'zoom-in' => esc_html__('ZoomIn', 'essential-addons-elementor'),
                    'slide-up' => esc_html__('SlideUp', 'essential-addons-elementor'),
                ],
            ]
        );

        $this->add_control(
            'eael_post_block_bg_hover_icon_new',
            [
                'label' => __('Post Hover Icon', 'essential-addons-elementor'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'eael_post_block_bg_hover_icon',
                'default' => [
                    'value' => 'fas fa-long-arrow-alt-right',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'eael_post_block_hover_animation!' => 'none',
                ],
            ]
        );

        $this->add_control(
            'eael_post_block_hover_bg_color',
            [
                'label' => __('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => 'rgba(0,0,0, .75)',
                'selectors' => [
                    '{{WRAPPER}} .eael-post-block-item .eael-entry-overlay' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .eael-post-block.post-block-style-overlay .eael-entry-wrapper' => 'background-color: {{VALUE}} !important;',
                ],

            ]
        );

        $this->add_control(
            'eael_post_block_hover_icon_color',
            [
                'label' => __('Icon Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .eael-post-block-item .eael-entry-overlay > i' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'grid_style!' => 'post-block-style-overlay',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_post_block_hover_icon_fontsize',
            [
                'label' => __('Icon size', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => 'px',
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 18,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-block-item .eael-entry-overlay > i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eael-post-block-item .eael-entry-overlay .eael-post-block-hover-svg-icon' => 'width:{{SIZE}}{{UNIT}};'
                ],
                'condition' => [
                    'grid_style!' => 'post-block-style-overlay',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $ds = $this->get_settings_for_display();
        $settings = HelperClass::fix_old_query($ds);
        $args = HelperClass::get_query_args($settings);
        $args = HelperClass::get_dynamic_args($settings, $args);

        $settings ['post_block_hover_animation'] = $settings['eael_post_block_hover_animation'];
        $settings ['show_read_more_button'] = $settings['eael_show_read_more_button'];
	    $settings ['eael_post_block_bg_hover_icon'] = $settings['eael_post_block_hover_animation'] == 'none' ? '' : ( ( isset( $settings['__fa4_migrated']['eael_post_block_bg_hover_icon_new'] ) || empty( $settings['eael_post_block_bg_hover_icon'] ) ) ? $settings['eael_post_block_bg_hover_icon_new']['value'] : $settings['eael_post_block_bg_hover_icon'] );
        $settings ['expanison_indicator'] = $settings['excerpt_expanison_indicator'];

        $link_settings = [
            'image_link_nofollow' => $settings['image_link_nofollow'] ? 'rel="nofollow"' : '',
            'image_link_target_blank' => $settings['image_link_target_blank'] ? 'target="_blank"' : '',
            'title_link_nofollow' => $settings['title_link_nofollow'] ? 'rel="nofollow"' : '',
            'title_link_target_blank' => $settings['title_link_target_blank'] ? 'target="_blank"' : '',
            'read_more_link_nofollow' => $settings['read_more_link_nofollow'] ? 'rel="nofollow"' : '',
            'read_more_link_target_blank' => $settings['read_more_link_target_blank'] ? 'target="_blank"' : '',
        ];

        $this->add_render_attribute(
            'eael-post-block-wrapper',
            [
                'id' => 'eael-post-block-' . esc_attr($this->get_id()),
                'class' => [
                    'eael-post-block',
                    $settings['grid_style'],
                    $settings['eael_post_block_layout'],
                ],
            ]
        );

        $this->add_render_attribute(
            'eael-post-block-wrap-inner',
            [
                'class' => ['eael-post-block-grid', 'eael-post-appender', 'eael-post-appender-' . esc_attr($this->get_id()), $settings['eael_post_tiled_preset'], $settings['eael_post_tiled_column']],
            ]
        );

        echo '<div ' . $this->get_render_attribute_string('eael-post-block-wrapper') . '>
            <div ' . $this->get_render_attribute_string('eael-post-block-wrap-inner') . '>';

        $template = $this->get_template($settings['eael_dynamic_template_Layout']);
        $settings['loadable_file_name'] = $this->get_filename_only($template);
	    $found_posts = 0;
        if (file_exists($template)) {
            $query = new \WP_Query($args);
            if ($query->have_posts()) {
	            $found_posts      = $query->found_posts;
	            $max_page         = ceil( $found_posts / absint( $args['posts_per_page'] ) );
	            $args['max_page'] = $max_page;
                while ($query->have_posts()) {
                    $query->the_post();
                    include($template);
                }
            } else {
                _e('<p class="no-posts-found">No posts found!</p>', 'essential-addons-elementor');
            }
            wp_reset_postdata();
        } else {
            _e('<p class="no-posts-found">No layout found!</p>', 'essential-addons-elementor');
        }

        echo '</div>
		</div>';

        // normalize settings for load more
        $settings['eael_dynamic_template_Layout'] = 'default';
        if (method_exists($this, 'print_load_more_button') && $found_posts > $args['posts_per_page']) {
	        $dir_name = method_exists( $this, 'get_temp_dir_name' ) ? $this->get_temp_dir_name( $settings[ 'loadable_file_name' ] ) : "pro";
            $this->print_load_more_button($settings, $args, $dir_name);
        }
    }
}
