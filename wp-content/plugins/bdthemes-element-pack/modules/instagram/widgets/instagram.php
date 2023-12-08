<?php

namespace ElementPack\Modules\Instagram\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use ElementPack\Modules\Instagram\Skins;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class Instagram extends Module_Base {

    public function get_name() {
        return 'bdt-instagram';
    }

    public function get_title() {
        return BDTEP . esc_html__('Instagram', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-instagram-feed';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['instagram', 'gallery', 'photos', 'images'];
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-font', 'ep-instagram'];
        }
    }

    public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-scripts'];
        } else {
            return ['ep-instagram'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/uj9WpuFIZb8';
    }

    public function register_skins() {
        $this->add_skin(new Skins\Skin_Carousel($this));
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content_layout',
            [
                'label' => esc_html__('Layout', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'instagram_user_token',
            [
                'label'       => esc_html__('Instagram Token (Optional)', 'bdthemes-element-pack'),
                'description' => esc_html__('Enter instagram User Token if you want to show separated user\'s photos', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'masonry',
            [
                'label'     => esc_html__('Masonry', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SWITCHER,
                'condition' => [
                    '_skin' => '',
                ],
            ]
        );

        $this->add_control(
            'item_ratio',
            [
                'label'     => esc_html__('Image Height', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 250,
                ],
                'range'     => [
                    'px' => [
                        'min'  => 50,
                        'max'  => 500,
                        'step' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-instagram-thumbnail *' => 'height: {{SIZE}}px',
                ],
                'condition' => [
                    'masonry!' => 'yes',

                ],
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label'          => esc_html__('Columns', 'bdthemes-element-pack'),
                'type'           => Controls_Manager::SELECT,
                'default'        => '4',
                'tablet_default' => '3',
                'mobile_default' => '2',
                'options'        => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
            ]
        );

        $this->add_control(
            'items',
            [
                'label'   => esc_html__('Item Limit', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SLIDER,
                'range'   => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 12,
                ],
            ]
        );

        $this->add_control(
            'column_gap',
            [
                'label'   => esc_html__('Column Gap', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'small',
                'options' => [
                    'small'    => esc_html__('Small', 'bdthemes-element-pack'),
                    'medium'   => esc_html__('Medium', 'bdthemes-element-pack'),
                    'large'    => esc_html__('Large', 'bdthemes-element-pack'),
                    'collapse' => esc_html__('Collapse', 'bdthemes-element-pack'),
                ],
            ]
        );

        // $this->add_control(
        //  'show_profile',
        //  [
        //      'label'     => esc_html__( 'Profile', 'bdthemes-element-pack' ),
        //      'type'      => Controls_Manager::SWITCHER,
        //      'condition' => [
        //          'layout!' => 'carousel',
        //      ],
        //  ]
        // );

        // $this->add_control(
        //     'show_loadmore',
        //     [
        //         'label' => esc_html__('Show Load More', 'bdthemes-element-pack'),
        //         'type'  => Controls_Manager::SWITCHER,
        //     ]
        // );

        $this->add_control(
            'show_follow_me',
            [
                'label'     => esc_html__('Follow Me', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SWITCHER,
                'condition' => [
                    '_skin' => 'bdt-instagram-carousel',
                ],
            ]
        );

        $this->add_control(
            'follow_me_text',
            [
                'label'       => esc_html__('Follow Me Text', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => esc_html__('follow me @', 'bdthemes-element-pack'),
                'default'     => esc_html__('follow me @', 'bdthemes-element-pack'),
                'condition'   => [
                    '_skin'          => 'bdt-instagram-carousel',
                    'show_follow_me' => 'yes',
                ],
            ]
        );


        $this->add_control(
            'show_lightbox',
            [
                'label'     => esc_html__('Lightbox', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'lightbox_animation',
            [
                'label'     => esc_html__('Lightbox Animation', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'slide',
                'options'   => [
                    'slide' => esc_html__('Slide', 'bdthemes-element-pack'),
                    'fade'  => esc_html__('Fade', 'bdthemes-element-pack'),
                    'scale' => esc_html__('Scale', 'bdthemes-element-pack'),
                ],
                'condition' => [
                    'show_lightbox' => 'yes',
                ],
                //'separator' => 'before',
            ]
        );

        $this->add_control(
            'lightbox_autoplay',
            [
                'label'     => __('Lightbox Autoplay', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SWITCHER,
                'condition' => [
                    'show_lightbox' => 'yes',
                ]
            ]
        );

        $this->add_control(
            'lightbox_pause',
            [
                'label'     => __('Lightbox Pause on Hover', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SWITCHER,
                'condition' => [
                    'show_lightbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_link',
            [
                'label'     => esc_html__('Link Image to Post', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SWITCHER,
                'condition' => [
                    'show_lightbox!' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'target_blank',
            [
                'label'     => esc_html__('Open in new window', 'bdthemes-element-pack') . BDTEP_NC,
                'type'      => Controls_Manager::SWITCHER,
                'condition' => [
                    'show_lightbox!' => 'yes',
                    'show_link'      => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_overlay',
            [
                'label'      => esc_html__('Show Overlay', 'bdthemes-element-pack') . BDTEP_NC,
                'type'       => Controls_Manager::SWITCHER,
                'default'    => 'yes',
                'conditions' => [
                    'relation' => 'or',
                    'terms'    => [
                        [
                            'name'  => 'show_lightbox',
                            'value' => 'yes',
                        ],
                        [
                            'name'  => 'show_link',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );

        $this->add_control(
            'show_link_icon',
            [
                'label'     => esc_html__('Link Icon', 'bdthemes-element-pack') . BDTEP_NC,
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'yes',
                'condition' => [
                    'show_overlay' => 'yes'
                ],
            ]
        );

        $this->add_control(
            'alignment',
            [
                'label'     => __('Alignment', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'left'   => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'  => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'default'   => 'center',
                'selectors' => [
                    '{{WRAPPER}} .bdt-instagram .bdt-instagram-profile' => 'text-align: {{VALUE}}',
                ],
                'condition' => [
                    'show_profile' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_content_additional',
            [
                'label' => esc_html__('Additional Settings', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'cache_gallery',
            [
                'label'   => esc_html__('Cache the Gallery', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'cache_time',
            [
                'label'       => esc_html__('Cache Time', 'bdthemes-element-pack'),
                'description' => esc_html__('How much hour(s) you want to cache.', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::NUMBER,
                'default'     => 12,
                'condition'   => [
                    'cache_gallery' => 'yes'
                ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_item',
            [
                'label' => __('Item', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_item_style');

        $this->start_controls_tab(
            'tab_item_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'item_background',
            [
                'label'     => __('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-instagram .bdt-instagram-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-instagram .bdt-instagram-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator'  => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'item_border',
                'label'       => __('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-instagram .bdt-instagram-item',
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'item_border_radius',
            [
                'label'      => __('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-instagram .bdt-instagram-item, {{WRAPPER}} .bdt-instagram .bdt-overlay.bdt-overlay-default, {{WRAPPER}} .bdt-instagram .swiper-carousel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_control(
            'image_section_layout',
            [
                'label'     => __('Image/Video', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'image_border',
                'label'       => __('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-instagram .bdt-instagram-thumbnail *',
            ]
        );

        $this->add_control(
            'image_border_radius',
            [
                'label'      => __('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-instagram .bdt-instagram-thumbnail *' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name'     => 'css_filters',
                'selector' => '{{WRAPPER}} .bdt-instagram .bdt-instagram-item img, {{WRAPPER}} .bdt-instagram .bdt-instagram-item video',
            ]
        );

        $this->add_control(
            'item_opacity',
            [
                'label'     => __('Opacity (%)', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 1,
                ],
                'range'     => [
                    'px' => [
                        'max'  => 1,
                        'min'  => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-instagram .bdt-instagram-item img, {{WRAPPER}} .bdt-instagram .bdt-instagram-item video' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_control(
            'shadow_mode',
            [
                'label'        => esc_html__('Shadow Mode', 'bdthemes-element-pack'),
                'type'         => Controls_Manager::SWITCHER,
                'prefix_class' => 'bdt-ep-shadow-mode-',
                'condition'    => [
                    '_skin' => 'bdt-instagram-carousel',
                ],
            ]
        );

        $this->add_control(
            'shadow_color',
            [
                'label'      => esc_html__('Shadow Color', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .elementor-widget-container:before' => 'background: linear-gradient(to right, {{VALUE}} 0%,rgba(255,255,255,0) 100%);',
                    '{{WRAPPER}} .elementor-widget-container:after'  => 'background: linear-gradient(to right, rgba(255,255,255,0) 0%, {{VALUE}} 100%);',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name'  => '_skin',
                            'value' => 'bdt-instagram-carousel',
                        ],
                        [
                            'name'  => 'shadow_mode',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_item_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
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
                    '{{WRAPPER}} .bdt-instagram .bdt-instagram-item:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name'     => 'css_filters_hover',
                'selector' => '{{WRAPPER}} .bdt-instagram .bdt-instagram-item:hover img, {{WRAPPER}} .bdt-instagram .bdt-instagram-item:hover video',
            ]
        );

        $this->add_control(
            'item_hover_opacity',
            [
                'label'     => __('Opacity (%)', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 1,
                ],
                'range'     => [
                    'px' => [
                        'max'  => 1,
                        'min'  => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-instagram .bdt-instagram-item:hover img, {{WRAPPER}} .bdt-instagram .bdt-instagram-item:hover video' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_overlay',
            [
                'label'      => esc_html__('Overlay', 'bdthemes-element-pack'),
                'tab'        => Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'relation' => 'or',
                    'terms'    => [
                        [
                            'name'  => 'show_lightbox',
                            'value' => 'yes',
                        ],
                        [
                            'name'  => 'show_link',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );

        $this->add_responsive_control(
            'overlay_gap',
            [
                'label'     => __('Overlay Gap', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 15
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-overlay' => 'margin: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'overlay_icon_size',
            [
                'label'     => __('Overlay Icon Size', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 40
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-instagram .bdt-instagram-item .bdt-overlay span' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'overlay_background',
            [
                'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-instagram .bdt-overlay.bdt-overlay-default' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'overlay_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-instagram .bdt-instagram-item.bdt-transition-toggle *' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_follow_me',
            [
                'label'      => __('Follow Me', 'bdthemes-element-pack'),
                'tab'        => Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'terms' => [
                        [
                            'name'  => '_skin',
                            'value' => 'bdt-instagram-carousel',
                        ],
                        [
                            'name'  => 'show_follow_me',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );

        $this->add_control(
            'follow_me_background',
            [
                'label'     => __('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-instagram-follow-me a' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'follow_me_text_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-instagram-follow-me a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'follow_me_text_hover_color',
            [
                'label'     => __('Text Hover Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-instagram-follow-me a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'follow_me_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-instagram-follow-me a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator'  => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'follow_me_border',
                'label'       => __('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-instagram-follow-me a',
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'follow_me_radius',
            [
                'label'      => __('Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-instagram-follow-me a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'follow_me_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} .bdt-instagram-follow-me a',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_navigation',
            [
                'label'     => __('Navigation', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    '_skin' => 'bdt-instagram-carousel',
                ],
            ]
        );

        $this->add_control(
            'arrows_size',
            [
                'label'     => __('Size', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 12,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-instagram .bdt-slidenav-previous svg, {{WRAPPER}} .bdt-instagram .bdt-slidenav-next svg' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'arrows_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-instagram .bdt-slidenav-previous svg, {{WRAPPER}} .bdt-instagram .bdt-slidenav-next svg' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'arrows_hover_color',
            [
                'label'     => __('Hover Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-instagram .bdt-slidenav-previous:hover svg, {{WRAPPER}} .bdt-instagram .bdt-slidenav-next:hover svg' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'arrows_background',
            [
                'label'     => __('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-instagram .bdt-slidenav-previous, {{WRAPPER}} .bdt-instagram .bdt-slidenav-next' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'arrows_hover_background',
            [
                'label'     => __('Hover Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-instagram .bdt-slidenav-previous:hover, {{WRAPPER}} .bdt-instagram .bdt-slidenav-next:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrows_padding',
            [
                'label'     => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .bdt-instagram .bdt-slidenav-previous' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-instagram .bdt-slidenav-next'     => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'arrows_radius',
            [
                'label'      => __('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-instagram .bdt-slidenav-previous, {{WRAPPER}} .bdt-instagram .bdt-slidenav-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrows_position',
            [
                'label'     => __('Position', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 150,
                        'step' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-slidenav-previous' => 'transform: translateY(-50%) translateY(0px) translateX(-{{SIZE}}px);',
                    '{{WRAPPER}} .bdt-slidenav-next'     => 'transform: translateY(-50%) translateY(0px) translateX({{SIZE}}px);',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function filter_response($response) {
        if (is_wp_error($response)) {
            $response = array(
                'status'  => 422,
                'message' => $response->get_error_message()
            );
        } else {
            $response = array(
                'status'  => wp_remote_retrieve_response_code($response),
                'message' => wp_remote_retrieve_response_message($response),
                'body'    => json_decode(wp_remote_retrieve_body($response)),
            );
        }


        return (object)$response;
    }

    protected function remote_get($url) {
        $response = wp_remote_get(
            $url,
            array(
                'timeout'    => 100,
                'user-agent' => $_SERVER['HTTP_USER_AGENT'],
            )
        );

        return $this->filter_response($response);
    }

    protected $graph_url = 'https://graph.instagram.com';

    protected function get_access_token_transit_key($user_token) {

        $widget_id  = strtolower($this->get_id());
        $user_token = md5($user_token);
        $user_token = strtolower($user_token);
        return 'ep_instagram_long_lived_access_token_' . $widget_id . '_' . $user_token;
    }

    protected function get_instagram_account_transit_key($user_token) {

        $widget_id  = strtolower($this->get_id());
        $user_token = md5($user_token);
        $user_token = strtolower($user_token);
        return 'ep_instagram_account_id_' . $widget_id . '_' . $user_token;
    }

    protected function get_instagram_media_data_transit_key($user_token) {

        $widget_id  = strtolower($this->get_id());
        $user_token = md5($user_token);
        $user_token = strtolower($user_token);
        return 'ep_instagram_media_data_' . $widget_id . '_' . $user_token;
    }

    protected function refresh_token($token, $app_secret) {
        $url    = $this->graph_url . "/refresh_access_token?grant_type=ig_refresh_token&access_token=$token";
        $result = $this->remote_get($url);

        if ($result->status == 200) {
            return $result->body->access_token;
        }
        return $result;
    }

    protected function get_access_token($user_token, $app_secret) {
        $cache_key       = $this->get_access_token_transit_key($user_token);
        $accessTokenData = get_transient($cache_key);

        if (!$accessTokenData) {
            $url    = $this->graph_url . "/refresh_access_token?grant_type=ig_refresh_token&&access_token=$user_token";
            $result = $this->remote_get($url);

            if ($result->status == 200) {
                $accessTokenData = $result->body->access_token;
                $accessTokenData = $accessTokenData . '_bdthemes_' . time();
                set_transient($cache_key, $accessTokenData);
            } else {
                return $result;
            }
        }

        $accessTokenArr = explode('_bdthemes_', $accessTokenData);
        if (count($accessTokenArr) == 2) {
            $access_token  = $accessTokenArr[0];
            $generatedTime = $accessTokenArr[1];
            $now           = time(); // or your date as well
            $datediff      = $now - $generatedTime;
            $totalDays     = round($datediff / (60 * 60 * 24));
            

            if ($totalDays > 40) {
                $access_token = $this->refresh_token($access_token, $app_secret);
                if (is_string($access_token)) {
                    $accessTokenData = $access_token . '_bdthemes_' . time();
                    set_transient($cache_key, $accessTokenData);
                }
            }
            return $access_token;
        }
    }

    private function get_instagram_account_id($access_token) {
        $cache_key  = $this->get_instagram_account_transit_key($access_token);
        $account_id = get_transient($cache_key);

        if (!$account_id) {
            $url    = $this->graph_url . "/me?fields=id&access_token=$access_token";
            $result = $this->remote_get($url);
            if ($result->status == 200) {
                $account_id = $result->body->id;
                set_transient($cache_key, $account_id, DAY_IN_SECONDS * 100);
            } else {
                return $result;
            }
        }

        return $account_id;
    }

    public function get_media($access_token, $account_id) {

        $cache_key = $this->get_instagram_media_data_transit_key($access_token);
        $settings   = $this->get_settings_for_display();
        $data       = '';

        $isCacheEnabled = isset($settings['cache_gallery']) && $settings['cache_gallery'] == 'yes';

        if ($isCacheEnabled) {
            $data           = get_transient($cache_key);
        } else {
            delete_transient($cache_key);
        }
        
        if (!$data) {
            $url    = $this->graph_url . "/$account_id/media?fields=id,media_type,media_url,permalink,username,timestamp&access_token=$access_token&limit=100";
            $result = $this->remote_get($url);
            if ($result->status == 200) {
                $data       = $result->body;

                if ($isCacheEnabled) {
                    $cache_time = isset($settings['cache_time']) ? intval($settings['cache_time']) : 1;
                    if ($cache_time < 1) {
                        $cache_time = 1;
                    }
                    set_transient($cache_key, $data, (HOUR_IN_SECONDS * $cache_time));
                }
            } else {
                return $result;
            }
        }

        return $data;
    }

    public function get_collect_data($app_secret) {
        $settings             = $this->get_settings_for_display();
        $options              = get_option('element_pack_api_settings');

        if ($settings['instagram_user_token']) {
            $instagram_user_token = $settings['instagram_user_token'];
        } elseif (!empty($options['instagram_access_token'])) {
            $instagram_user_token = $options['instagram_access_token'];
        } else {
            element_pack_alert('Ops! You did not set Instagram User Token!');
            return false;
        }

        // $access_token = $this->get_access_token($instagram_user_token, $app_secret);
        $access_token = $instagram_user_token;

        if (is_string($access_token) && strlen($access_token) > 20) {
            $account_id = $this->get_instagram_account_id($access_token);
            if (is_string($account_id) && strlen($account_id) > 5) {
                return $this->get_media($access_token, $account_id);
            } else {
                return $account_id;
            }
        }


        return $access_token;
    }

    public function get_instagram_data($app_secret) {
        $data = $this->get_collect_data($app_secret);

        if (isset($data->data)) {
            return $data->data;
        } else {
            if (isset($data->status) && $data->status == 422) {
                element_pack_alert($data->message);
            }
        }
        return [];
    }

    public function render() {
        $settings = $this->get_settings_for_display();

        $options = get_option('element_pack_api_settings');

        $instagram_app_secret = (!empty($options['instagram_app_secret'])) ? $options['instagram_app_secret'] : '';

        if (!$instagram_app_secret) {
            element_pack_alert('Ops! You did not set Instagram App Secret in element pack settings!');
            return;
        }

        /**
         * This is the data
         */
        $data = $this->get_instagram_data($instagram_app_secret);

        $this->add_render_attribute('instagram-wrapper', 'class', 'bdt-instagram');

        $this->add_render_attribute('instagram', 'class', 'bdt-grid');

        $this->add_render_attribute('instagram', 'class', 'bdt-grid-' . esc_attr($settings["column_gap"]));


        $columns_mobile = isset($settings['columns_mobile']) ? $settings['columns_mobile'] : 2;
        $columns_tablet = isset($settings['columns_tablet']) ? $settings['columns_tablet'] : 3;
        $columns        = isset($settings['columns']) ? $settings['columns'] : 4;

        $this->add_render_attribute('instagram', 'class', 'bdt-child-width-1-' . esc_attr($columns) . '@m');
        $this->add_render_attribute('instagram', 'class', 'bdt-child-width-1-' . esc_attr($columns_tablet) . '@s');
        $this->add_render_attribute('instagram', 'class', 'bdt-child-width-1-' . esc_attr($columns_mobile));

        $this->add_render_attribute('instagram', 'data-bdt-grid', '');
        if ($settings['masonry']) {
            $this->add_render_attribute('instagram', 'data-bdt-grid', 'masonry: true;');
        }


        $this->add_render_attribute('instagram', 'class', 'bdt-instagram-grid');


        if ('yes' == $settings['show_lightbox']) {
            $this->add_render_attribute('instagram', 'data-bdt-lightbox', 'animation:' . $settings['lightbox_animation'] . ';');
            if ($settings['lightbox_autoplay']) {
                $this->add_render_attribute('instagram', 'data-bdt-lightbox', 'autoplay: 500;');

                if ($settings['lightbox_pause']) {
                    $this->add_render_attribute('instagram', 'data-bdt-lightbox', 'pause-on-hover: true;');
                }
            }
        }

        $this->add_render_attribute(
            [
                'instagram-wrapper' => [
                    'data-settings' => [
                        wp_json_encode(array_filter([
                            // 'action'              => 'element_pack_instagram_ajax_load',
                            // 'show_link'           => ( $settings['show_link'] ) ? true : false,
                            // 'show_lightbox'       => ( $settings['show_lightbox'] ) ? true : false,
                            'current_page'        => 1,
                            // 'load_more_per_click' => 4,
                            // 'item_per_page'       => $settings["items"]["size"],
                        ]))
                    ]
                ]
            ]
        );

?>
        <div <?php echo $this->get_render_attribute_string('instagram-wrapper'); ?>>

            <div <?php echo $this->get_render_attribute_string('instagram'); ?>>

                <?php
                // TODO need to fix load more
                $limit = 1;
                foreach ($data as $item) { ?>

                    <div class="bdt-instagram-item-wrapper feed-type-video bdt-first-column">
                        <div class="bdt-instagram-item bdt-transition-toggle bdt-position-relative bdt-scrollspy-inview bdt-animation-fade">
                            <div class="bdt-instagram-thumbnail">
                                <?php if ('VIDEO' == $item->media_type) : ?>
                                    <video src="<?php echo esc_url($item->media_url); ?>" title="Image by: <?php echo esc_html($item->username); ?>">
                                    <?php else : ?>
                                        <img src="<?php echo esc_url($item->media_url); ?>" alt="Image by: <?php echo esc_html($item->username); ?>" loading="lazy">
                                    <?php endif; ?>
                            </div>

                            <?php
                            if ($settings['show_lightbox'] or $settings['show_link']) :

                                $target_href = (isset($settings['show_link']) && ($settings['show_link'] == 'yes')) ? $item->permalink : $item->media_url;
                                $target_blank = (isset($settings['target_blank']) && ('yes' == $settings['target_blank'])) ? '_blank' : '_self';

                            ?>
                                <a target="<?php echo esc_attr($target_blank); ?>" href="<?php echo esc_url($target_href); ?>" data-elementor-open-lightbox="no">

                                    <?php if ($settings['show_overlay']) : ?>
                                        <div class="bdt-transition-fade bdt-inline-clip bdt-position-cover bdt-overlay bdt-overlay-default ">

                                            <?php if ($settings['show_link_icon']) : ?>
                                                <?php if ('VIDEO' == $item->media_type) : ?>
                                                    <span class='bdt-position-center ep-icon-play'></span>
                                                <?php else : ?>
                                                    <span class='bdt-position-center ep-icon-plus'></span>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                        </div>
                                    <?php endif; ?>

                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php

                    if ($limit++ == $settings['items']['size']) {
                        break;
                    }
                }

                ?>

            </div>

        </div>

<?php
    }
}
