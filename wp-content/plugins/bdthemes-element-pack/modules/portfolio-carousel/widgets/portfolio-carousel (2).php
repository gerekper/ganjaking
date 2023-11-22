<?php

namespace ElementPack\Modules\PortfolioCarousel\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;

use ElementPack\Utils;
use ElementPack\Base\Module_Base;
use ElementPack\Traits\Global_Swiper_Controls;
use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;
use ElementPack\Modules\PortfolioCarousel\Skins;
use WP_Query;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Portfolio_Carousel extends Module_Base {
    use Group_Control_Query;
    use Global_Swiper_Controls;

    private $_query = null;

    public function get_name() {
        return 'bdt-portfolio-carousel';
    }

    public function get_title() {
        return BDTEP . esc_html__('Portfolio Carousel', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-portfolio-carousel';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['portfolio', 'gallery', 'blog', 'recent', 'news', 'works', 'portfolio-carousel'];
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-font', 'ep-portfolio-carousel'];
        }
    }

    public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['imagesloaded', 'ep-scripts'];
        } else {
            return ['imagesloaded', 'ep-portfolio-carousel'];
        }
    }

    public function register_skins() {
        $this->add_skin(new Skins\Skin_Abetis($this));
        $this->add_skin(new Skins\Skin_Fedara($this));
        $this->add_skin(new Skins\Skin_Trosia($this));
        $this->add_skin(new Skins\Skin_Janes($this));
    }

    public function on_import($element) {
        if (!get_post_type_object($element['settings']['posts_post_type'])) {
            $element['settings']['posts_post_type'] = 'post';
        }
        return $element;
    }

    public function get_query() {
        return $this->_query;
    }

    public function register_controls() {
        $this->register_section_controls();
    }

    private function register_section_controls() {

        $this->start_controls_section(
            'section_carousel_layout',
            [
                'label' => __('Layout', 'bdthemes-element-pack'),
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label' => __('Columns', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 3,
                'tablet_default' => 2,
                'mobile_default' => 1,
                'options' => [
                    1 => '1',
                    2 => '2',
                    3 => '3',
                    4 => '4',
                    5 => '5',
                    6 => '6',
                ],
            ]
        );

        $this->add_control(
            'item_gap',
            [
                'label' => __('Item Gap', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 35,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
            ]
        );

        $this->add_control(
            'match_height',
            [
                'label' => __('Item Match Height', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'         => 'thumbnail_size',
                'label'        => esc_html__('Image Size', 'bdthemes-element-pack'),
                'exclude'      => ['custom'],
                'default'      => 'medium',
                'prefix_class' => 'bdt-portfolio--thumbnail-size-',
            ]
        );

        $this->end_controls_section();

        //New Query Builder Settings
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
                'type' => Controls_Manager::SELECT,
                'default' => 'portfolio'
            ]
        );

        $this->update_control(
            'posts_per_page',
            [
                'default' => 9,
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_layout_additional',
            [
                'label' => esc_html__('Additional', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => esc_html__('Title', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'title_tag',
            [
                'label' => esc_html__('Title HTML Tag', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'options' => element_pack_title_tags(),
                'default' => 'h4',
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_excerpt',
            [
                'label' => esc_html__('Show Text', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'excerpt_limit',
            [
                'label' => esc_html__('Text Limit', 'bdthemes-element-pack'),
                'description' => esc_html__('It\'s just work for main content, but not working with excerpt. If you set 0 so you will get full main content.', 'bdthemes-element-pack'),
                'type' => Controls_Manager::NUMBER,
                'default' => 10,
                'condition' => [
                    'show_excerpt' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'strip_shortcode',
            [
                'label'   => esc_html__('Strip Shortcode', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition'   => [
                    'show_excerpt' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_category',
            [
                'label' => esc_html__('Show Category', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'show_link',
            [
                'label' => esc_html__('Show Link', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'both',
                'options' => [
                    'post' => esc_html__('Details Link', 'bdthemes-element-pack'),
                    'lightbox' => esc_html__('Lightbox Link', 'bdthemes-element-pack'),
                    'both' => esc_html__('Both', 'bdthemes-element-pack'),
                    'none' => esc_html__('None', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->add_control(
            'external_link',
            [
                'label' => esc_html__('Show in new Tab (Details Link/Title)', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'show_title',
                            'operator' => '==',
                            'value' => 'yes'
                        ],
                        [
                            'name' => 'show_link',
                            'operator' => '==',
                            'values' => ['post', 'both']
                        ],
                    ]
                ],
            ]
        );

        $this->add_control(
            'link_type',
            [
                'label' => esc_html__('Link Type', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'icon',
                'options' => [
                    'icon' => esc_html__('Icon', 'bdthemes-element-pack'),
                    'text' => esc_html__('Text', 'bdthemes-element-pack'),
                ],
                'condition' => [
                    'show_link!' => 'none',
                ]
            ]
        );

        $this->add_control(
            'lightbox_animation',
            [
                'label' => esc_html__('Lightbox Animation', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'slide',
                'options' => [
                    'slide' => esc_html__('Slide', 'bdthemes-element-pack'),
                    'fade' => esc_html__('Fade', 'bdthemes-element-pack'),
                    'scale' => esc_html__('Scale', 'bdthemes-element-pack'),
                ],
                'condition' => [
                    'show_link' => ['both', 'lightbox'],
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'lightbox_autoplay',
            [
                'label' => __('Lightbox Autoplay', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'condition' => [
                    'show_link' => ['both', 'lightbox'],
                ]
            ]
        );

        $this->add_control(
            'lightbox_pause',
            [
                'label' => __('Lightbox Pause on Hover', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'condition' => [
                    'show_link' => ['both', 'lightbox'],
                    'lightbox_autoplay' => 'yes'
                ],

            ]
        );

        $this->end_controls_section();

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

        //Style
        $this->start_controls_section(
            'section_design_layout',
            [
                'label' => esc_html__('Items', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'overlay_style_headline',
            [
                'label'     => esc_html__('Overlay', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'condition' => [
                    '_skin!' => ['bdt-janes', 'bdt-trosia'],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'overlay_skin_abetis_background',
                'label' => __('Background', 'bdthemes-element-pack'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .bdt-portfolio-carousel.skin-abetis .bdt-portfolio-inner:before, {{WRAPPER}} .bdt-portfolio-carousel.skin-fedara .bdt-portfolio-inner:before',
                'condition' => [
                    '_skin' => ['bdt-abetis', 'bdt-fedara']
                ],
            ]
        );

        $this->add_control(
            'overlay_primary_background',
            [
                'label' => esc_html__('Primary Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-portfolio-carousel.skin-default .bdt-portfolio-content-inner:before' => 'background: {{VALUE}};',
                ],
                'condition' => [
                    '_skin' => '',
                ],
            ]
        );

        $this->add_control(
            'overlay_secondary_background',
            [
                'label' => esc_html__('Secondary Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-portfolio-carousel.skin-default .bdt-portfolio-content-inner:after' => 'background: {{VALUE}};',
                ],
                'condition' => [
                    '_skin' => '',
                ],
            ]
        );

        $this->add_control(
            'portfolio_content_style_headline',
            [
                'label' => esc_html__('Content', 'bdthemes-element-pack'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'content_width',
            [
                'label'     => esc_html__('Content Width(%)', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-portfolio-carousel.skin-janes .bdt-gallery-item .bdt-portfolio-inner .bdt-portfolio-desc' => 'right: calc(100% - {{SIZE}}%);',
                ],
                'condition' => [
                    '_skin' => 'bdt-janes',
                ],
            ]
        );

        $this->add_responsive_control(
            'portfolio_content_alignment',
            [
                'label' => __('Alignment', 'bdthemes-element-pack'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'prefix_class' => 'bdt-custom-gallery-skin-fedara-style-',
                'selectors' => [
                    '{{WRAPPER}} .bdt-portfolio-carousel .bdt-portfolio-desc, {{WRAPPER}} .bdt-portfolio-carousel .bdt-portfolio-skin-fedara-desc' => 'text-align: {{VALUE}}',
                ],
                // 'condition' => [
                // 	'_skin!' => 'bdt-trosia',
                // ],
            ]
        );

        // $this->add_control(
        //     'desc_background_color',
        //     [
        //         'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
        //         'type' => Controls_Manager::COLOR,
        //         'selectors' => [
        //             '{{WRAPPER}} .bdt-portfolio-carousel .bdt-portfolio-desc, {{WRAPPER}} .bdt-portfolio-carousel .bdt-portfolio-skin-fedara-desc' => 'background: {{VALUE}};',
        //         ],
        //         'condition' => [
        //             '_skin!' => 'bdt-abetis',
        //         ],
        //     ]
        // );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'desc_background_color',
                'selector'  => '{{WRAPPER}} .bdt-portfolio-carousel .bdt-portfolio-desc, {{WRAPPER}} .bdt-portfolio-carousel .bdt-portfolio-skin-fedara-desc',
                'condition' => [
                    '_skin!' => 'bdt-abetis',
                ],
            ]
        );

        $this->add_responsive_control(
            'desc__padding',
            [
                'label' => esc_html__('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-portfolio-carousel .bdt-portfolio-desc, {{WRAPPER}} .bdt-portfolio-carousel .bdt-portfolio-skin-fedara-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-portfolio-carousel .bdt-portfolio-desc, {{WRAPPER}} .bdt-portfolio-carousel .bdt-portfolio-skin-fedara-desc' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_spacing',
            [
                'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-portfolio-carousel.skin-janes .bdt-gallery-item .bdt-gallery-item-tags' => 'padding-top: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'show_category' => 'yes',
                    '_skin' => 'bdt-janes',
                ],
            ]
        );

        $this->add_control(
            'portfolio_item_headline',
            [
                'label'     => esc_html__('Item', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->start_controls_tabs('tabs_item_style');

        $this->start_controls_tab(
            'tab_item_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack') . BDTEP_NC,
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'item_border',
                'label'       => __('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item',
            ]
        );

        $this->add_responsive_control(
            'item_border_radius',
            [
                'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item, {{WRAPPER}} .bdt-portfolio-carousel .swiper-carousel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    '_skin!' => 'bdt-janes'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'item_shadow',
                'selector' => '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_item_hover',
            [
                'label' => __('hover', 'bdthemes-element-pack') . BDTEP_NC,
            ]
        );

        $this->add_control(
            'item_hover_border_color',
            [
                'label'     => __('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'item_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'item_hover_shadow',
                'selector' => '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

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
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item .bdt-gallery-item-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_hover_color',
            [
                'label' => esc_html__('Hover Color', 'bdthemes-element-pack') . BDTEP_NC,
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item .bdt-gallery-item-title:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-gallery-item .bdt-gallery-item-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'title_shadow',
                'label' => __('Text Shadow', 'bdthemes-element-pack') . BDTEP_NC,
                'selector' => '{{WRAPPER}} .bdt-gallery-item .bdt-gallery-item-title',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_excerpt',
            [
                'label' => esc_html__('Text', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_excerpt' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'excerpt_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-portfolio-carousel .bdt-portfolio-excerpt' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'excerpt_margin',
            [
                'label' => esc_html__('Margin', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .bdt-portfolio-carousel .bdt-portfolio-excerpt' => 'margin: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'excerpt_typography',
                'label' => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-portfolio-carousel .bdt-portfolio-excerpt',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_button',
            [
                'label' => esc_html__('Button', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_link!' => 'none',
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
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item-link, {{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item-link i, {{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item-link span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'background_color',
            [
                'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item-link' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_box_shadow',
                'selector' => '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item-link',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'label' => esc_html__('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item-link',
            ]
        );

        $this->add_responsive_control(
            'button_border_radius',
            [
                'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'border_radius_advanced_show!' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'border_radius_advanced_show',
            [
                'label' => __('Advanced Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'border_radius_advanced',
            [
                'label' => esc_html__('Radius', 'bdthemes-element-pack'),
                'description' => sprintf(__('For example: <b>%1s</b> or Go <a href="%2s" target="_blank">this link</a> and copy and paste the radius value.', 'bdthemes-element-pack'), '30% 70% 82% 18% / 46% 62% 38% 54%', 'https://9elements.github.io/fancy-border-radius/'),
                'type' => Controls_Manager::TEXT,
                'size_units' => ['px', '%'],
                'default' => '30% 70% 82% 18% / 46% 62% 38% 54%',
                'selectors' => [
                    '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item-link' => 'border-radius: {{VALUE}}; overflow: hidden;',
                ],
                'condition' => [
                    'border_radius_advanced_show' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => esc_html__('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'label' => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item-link span, {{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item-link i',
                // 'condition' => [
                //     'link_type' => 'text',
                // ],
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
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item-link:hover i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item-link:hover span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'background_hover_color',
            [
                'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item-link:hover, {{WRAPPER}} .bdt-portfolio-carousel.skin-abetis .bdt-gallery-item-link:before' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_border_color',
            [
                'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item-link.bdt-link-icon:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_category',
            [
                'label' => esc_html__('Category', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_category' => 'yes',
                ]
            ]
        );

        $this->add_control(
            'category_color',
            [
                'label' => esc_html__('Category Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item-tags' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'category_separator_color',
            [
                'label' => esc_html__('Separator Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item-tags .bdt-gallery-item-tag-separator' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'category_background',
            [
                'label' => esc_html__('Background', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item-tags' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'category_border',
                'label' => esc_html__('Border', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item-tags',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'category_border_radius',
            [
                'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item-tags' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'category_padding',
            [
                'label' => esc_html__('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item-tags' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'category_box_shadow',
                'selector' => '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item-tags',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'category_typography',
                'label' => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-portfolio-carousel .bdt-gallery-item-tag',
            ]
        );

        $this->end_controls_section();

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
        $this->register_navigation_style_controls('swiper-carousel');

        $this->end_controls_section();
    }

    /**
     * Get post query builder arguments
     */
    public function query_posts($posts_per_page) {
        $settings = $this->get_settings();

        $args = [];
        if ($posts_per_page) {
            $args['posts_per_page'] = $posts_per_page;
            $args['paged']  = max(1, get_query_var('paged'), get_query_var('page'));
        }

        $default = $this->getGroupControlQueryArgs();
        $args = array_merge($default, $args);

        $this->_query = new \WP_Query($args);
    }

    public function render() {
        $settings = $this->get_settings_for_display();

        // TODO need to delete after v6.5
        if (isset($settings['limit']) and $settings['posts_per_page'] == 6) {
            $limit = $settings['limit'];
        } else {
            $limit = $settings['posts_per_page'];
        }

        $this->query_posts($limit);

        $wp_query = $this->get_query();

        if (!$wp_query->found_posts) {
            return;
        }

        $this->render_header();

        while ($wp_query->have_posts()) {
            $wp_query->the_post();

            $this->render_post();
        }

        $this->render_footer();

        wp_reset_postdata();
    }

    public function render_thumbnail() {
        $settings = $this->get_settings_for_display();

        $settings['thumbnail_size'] = [
            'id' => get_post_thumbnail_id(),
        ];

        $thumbnail_html      = Group_Control_Image_Size::get_attachment_image_html($settings, 'thumbnail_size');
        $placeholder_img_src = Utils::get_placeholder_image_src();

        if (!$thumbnail_html) {
            printf('<div class="bdt-gallery-thumbnail"><img src="%1$s" alt="%2$s"></div>', $placeholder_img_src, esc_html(get_the_title()));
        } else {
            echo '<div class="bdt-gallery-thumbnail">';
            print(wp_get_attachment_image(
                get_post_thumbnail_id(),
                $settings['thumbnail_size_size'],
                false,
                [
                    'alt' => esc_html(get_the_title())
                ]
            ));
            echo '</div>';
        }
    }

    public function render_title() {
        $settings = $this->get_settings_for_display();

        if (!$settings['show_title']) {
            return;
        }

        $tag    = $settings['title_tag'];
        $target = ($settings['external_link']) ? 'target="_blank"' : '';

?>
        <a href="<?php echo get_the_permalink(); ?>" <?php echo $target; ?>>
            <<?php echo Utils::get_valid_html_tag($tag) ?> class="bdt-gallery-item-title bdt-margin-remove">
                <?php the_title() ?>
            </<?php echo Utils::get_valid_html_tag($tag) ?>>
        </a>
    <?php
    }

    public function render_excerpt() {
        if (!$this->get_settings('show_excerpt')) {
            return;
        }

        $strip_shortcode = $this->get_settings_for_display('strip_shortcode');

    ?>
        <div class="bdt-portfolio-excerpt">
            <?php
            if (has_excerpt()) {
                the_excerpt();
            } else {
                echo element_pack_custom_excerpt($this->get_settings_for_display('excerpt_limit'), $strip_shortcode);
            }
            ?>
        </div>
    <?php

    }

    public function render_categories_names() {
        $settings = $this->get_settings_for_display();
        if (!$this->get_settings('show_category')) {
            return;
        }

        $this->add_render_attribute('portfolio-category', 'class', 'bdt-gallery-item-tags', true);

        global $post;

        $separator  = '<span class="bdt-gallery-item-tag-separator"></span>';
        $tags_array = [];

        $item_filters = get_the_terms($post->ID, 'portfolio_filter');

        foreach ($item_filters as $item_filter) {
            $tags_array[] = '<span class="bdt-gallery-item-tag">' . $item_filter->slug . '</span>';
        }

    ?>
        <div <?php echo $this->get_render_attribute_string('portfolio-category'); ?>>
            <?php echo implode($separator, $tags_array); ?>
        </div>
    <?php
    }

    public function render_overlay() {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute(
            [
                'content-position' => [
                    'class' => [
                        'bdt-position-center',
                    ]
                ]
            ],
            '',
            '',
            true
        );

    ?>
        <div <?php echo $this->get_render_attribute_string('content-position'); ?>>
            <div class="bdt-portfolio-content">
                <div class="bdt-gallery-content-inner">
                    <?php

                    $placeholder_img_src = Utils::get_placeholder_image_src();

                    $img_url = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');

                    if (!$img_url) {
                        $img_url = $placeholder_img_src;
                    } else {
                        $img_url = $img_url[0];
                    }

                    $this->add_render_attribute(
                        [
                            'lightbox-settings' => [
                                'class' => [
                                    'bdt-gallery-item-link',
                                    'bdt-gallery-lightbox-item',
                                    ('icon' == $settings['link_type']) ? 'bdt-link-icon' : 'bdt-link-text'
                                ],
                                'data-elementor-open-lightbox' => 'no',
                                'data-caption' => get_the_title(),
                                'href' => esc_url($img_url)
                            ]
                        ],
                        '',
                        '',
                        true
                    );

                    if ('none' !== $settings['show_link']) : ?>
                        <div class="bdt-flex-inline bdt-gallery-item-link-wrapper">
                            <?php if (('lightbox' == $settings['show_link']) || ('both' == $settings['show_link'])) : ?>
                                <a <?php echo $this->get_render_attribute_string('lightbox-settings'); ?>>
                                    <?php if ('icon' == $settings['link_type']) : ?>
                                        <i class="ep-icon-search" aria-hidden="true"></i>
                                    <?php elseif ('text' == $settings['link_type']) : ?>
                                        <span><?php esc_html_e('ZOOM', 'bdthemes-element-pack'); ?></span>
                                    <?php endif; ?>
                                </a>
                            <?php endif; ?>

                            <?php if (('post' == $settings['show_link']) || ('both' == $settings['show_link'])) : ?>
                                <?php
                                $link_type_class = ('icon' == $settings['link_type']) ? ' bdt-link-icon' : ' bdt-link-text';
                                $target          = ($settings['external_link']) ? 'target="_blank"' : '';

                                ?>
                                <a class="bdt-gallery-item-link<?php echo esc_attr($link_type_class); ?>" href="<?php echo esc_attr(get_permalink()); ?>" <?php echo $target; ?>>
                                    <?php if ('icon' == $settings['link_type']) : ?>
                                        <i class="ep-icon-link" aria-hidden="true"></i>
                                    <?php elseif ('text' == $settings['link_type']) : ?>
                                        <span><?php esc_html_e('VIEW', 'bdthemes-element-pack'); ?></span>
                                    <?php endif; ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php
    }

    public function render_header($skin = "default") {
        $settings        = $this->get_settings_for_display();

        //Global Function
        $this->render_swiper_header_attribute('portfolio-carousel');

        $this->add_render_attribute('carousel', 'class', ['bdt-portfolio-carousel', 'skin-' . $skin]);

        if ('yes' == $settings['match_height']) {
            $this->add_render_attribute('carousel', 'data-bdt-height-match', 'target: > div > div > .bdt-gallery-item');
        }

        if ('lightbox' === $settings['show_link'] or 'both' === $settings['show_link']) {
            $this->add_render_attribute('carousel', 'data-bdt-lightbox', 'toggle: .bdt-gallery-lightbox-item; animation:' . $settings['lightbox_animation'] . ';');
            if ($settings['lightbox_autoplay']) {
                $this->add_render_attribute('carousel', 'data-bdt-lightbox', 'autoplay: 500;');

                if ($settings['lightbox_pause']) {
                    $this->add_render_attribute('carousel', 'data-bdt-lightbox', 'pause-on-hover: true;');
                }
            }
        }

    ?>
        <div <?php echo $this->get_render_attribute_string('carousel'); ?>>
            <div <?php echo $this->get_render_attribute_string('swiper'); ?>>
                <div class="swiper-wrapper">
                <?php
            }

            public function render_desc() {
                ?>
                    <div class="bdt-portfolio-desc">
                        <?php
                        $this->render_title();
                        $this->render_excerpt();
                        ?>
                    </div>
                <?php
            }

            public function render_post() {
                $settings = $this->get_settings_for_display();
                global $post;

                $this->add_render_attribute('portfolio-item-inner', 'class', 'bdt-portfolio-inner', true);

                $this->add_render_attribute('portfolio-item', 'class', 'swiper-slide bdt-gallery-item bdt-transition-toggle', true);

                ?>
                    <div <?php echo $this->get_render_attribute_string('portfolio-item'); ?>>
                        <div <?php echo $this->get_render_attribute_string('portfolio-item-inner'); ?>>
                            <div class="bdt-portfolio-content-inner">
                                <?php
                                $this->render_thumbnail();
                                $this->render_overlay();
                                ?>
                            </div>
                            <?php $this->render_desc(); ?>
                            <?php $this->render_categories_names(); ?>
                        </div>
                    </div>
            <?php
            }
        }
