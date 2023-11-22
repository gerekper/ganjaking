<?php

namespace ElementPack\Modules\WcSlider\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Icons_Manager;
use ElementPack\Utils;
use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;
use ElementPack\Traits\Global_Widget_Controls;
use WP_Query;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class WC_Slider extends Module_Base {
    use Group_Control_Query;
    use Global_Widget_Controls;

    private $_query = null;

    public function get_name() {
        return 'bdt-wc-slider';
    }

    public function get_title() {
        return BDTEP . __('WC - Slider', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-wc-slider';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['slider', 'woocommerce', 'wc', 'product'];
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-font', 'ep-wc-slider'];
        }
    }

    public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['imagesloaded', 'ep-woocommerce', 'ep-scripts'];
        } else {
            return ['imagesloaded', 'ep-woocommerce', 'ep-wc-slider'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/ic8p-3jO35U';
    }

    public function get_query() {
        return $this->_query;
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_content_layout',
            [
                'label' => __('Layout', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'slider_size_ratio',
            [
                'label'       => esc_html__('Size Ratio', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::IMAGE_DIMENSIONS,
                'description' => 'Slider ratio to widht and height, such as 16:9',
            ]
        );

        $this->add_control(
            'slider_min_height',
            [
                'label' => esc_html__('Minimum Height', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 1024,
                    ],
                ],
            ]
        );

        $this->add_control(
            'slider_fullscreen',
            [
                'label' => __('Slideshow Fullscreen', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'text_align',
            [
                'label'   => __('Text Align', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::CHOOSE,
                'toggle'  => false,
                'default' => 'left',
                'options' => [
                    'left'    => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center'  => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'   => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-justify',
                    ],
                ],
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'vertical_align',
            [
                'label'   => __('Vertical Align', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::CHOOSE,
                'toggle'  => false,
                'default' => 'middle',
                'options' => [
                    'top'    => [
                        'title' => __('Top', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-v-align-top',
                    ],
                    'middle' => [
                        'title' => __('Middle', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-v-align-middle',
                    ],
                    'bottom' => [
                        'title' => __('Bottom', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-v-align-bottom',
                    ],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'    => 'image',
                'label'   => __('Image Size', 'bdthemes-element-pack'),
                'exclude' => ['custom'],
                'default' => 'full',
            ]
        );

        $this->add_control(
            'content_reverse',
            [
                'label' => __('Content Reverse', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_additional',
            [
                'label' => __('Additional', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'show_price',
            [
                'label'   => __('Price', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label'   => __('Show Title', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'title_tags',
            [
                'label'     => __('Title HTML Tag', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'h2',
                'options'   => element_pack_title_tags(),
                'condition' => [
                    'show_title' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'show_rating',
            [
                'label'   => __('Rating', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_text',
            [
                'label' => __('Show Text', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'show_cart',
            [
                'label'   => __('Add to Cart', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_readmore',
            [
                'label'   => esc_html__('Read More', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_badge',
            [
                'label'   => __('Show Badge', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        // $this->add_control(
        // 	'show_thumbnav',
        // 	[
        // 		'label'   => __( 'Show Thumbnav', 'bdthemes-element-pack' ),
        // 		'type'    => Controls_Manager::SWITCHER,
        // 		'default' => 'yes',
        // 	]
        // );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_readmore',
            [
                'label'     => __('Read More', 'bdthemes-element-pack'),
                'condition' => [
                    'show_readmore' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'readmore_text',
            [
                'label'       => esc_html__('Read More Text', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__('Read More', 'bdthemes-element-pack'),
                'placeholder' => esc_html__('Read More', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'wc_slider_readmore_icon',
            [
                'label'            => esc_html__('Icon', 'bdthemes-element-pack'),
                'type'             => Controls_Manager::ICONS,
                'fa4compatibility' => 'readmore_icon',
                'label_block' => false,
				'skin' => 'inline'
            ]
        );

        $this->add_control(
            'readmore_icon_align',
            [
                'label'     => esc_html__('Icon Position', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'right',
                'options'   => [
                    'left'  => esc_html__('Before', 'bdthemes-element-pack'),
                    'right' => esc_html__('After', 'bdthemes-element-pack'),
                ],
                'condition' => [
                    'wc_slider_readmore_icon[value]!' => '',
                ],
            ]
        );

        $this->add_control(
            'readmore_icon_indent',
            [
                'label'     => esc_html__('Icon Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 8,
                ],
                'range'     => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'condition' => [
                    'wc_slider_readmore_icon[value]!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-button-icon-align-right' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-button-icon-align-left'  => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_navigation',
            [
                'label' => __('Navigation', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'navigation',
            [
                'label'   => __('Navigation', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'arrows',
                'options' => [
                    'both'   => __('Arrows and Dots', 'bdthemes-element-pack'),
                    'arrows' => __('Arrows', 'bdthemes-element-pack'),
                    'dots'   => __('Dots', 'bdthemes-element-pack'),
                    'none'   => __('None', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->add_control(
            'nav_arrows_icon',
            [
                'label'     => esc_html__('Arrows Icon', 'bdthemes-element-pack') . BDTEP_NC,
                'type'      => Controls_Manager::SELECT,
                'default'   => '0',
                'options'   => [
                    '0'        => esc_html__('Default', 'bdthemes-element-pack'),
                    '1'        => esc_html__('Style 1', 'bdthemes-element-pack'),
                    '2'        => esc_html__('Style 2', 'bdthemes-element-pack'),
                    '3'        => esc_html__('Style 3', 'bdthemes-element-pack'),
                    '4'        => esc_html__('Style 4', 'bdthemes-element-pack'),
                    '5'        => esc_html__('Style 5', 'bdthemes-element-pack'),
                    '6'        => esc_html__('Style 6', 'bdthemes-element-pack'),
                    '7'        => esc_html__('Style 7', 'bdthemes-element-pack'),
                    '8'        => esc_html__('Style 8', 'bdthemes-element-pack'),
                    '9'        => esc_html__('Style 9', 'bdthemes-element-pack'),
                    '10'       => esc_html__('Style 10', 'bdthemes-element-pack'),
                    '11'       => esc_html__('Style 11', 'bdthemes-element-pack'),
                    '12'       => esc_html__('Style 12', 'bdthemes-element-pack'),
                    '13'       => esc_html__('Style 13', 'bdthemes-element-pack'),
                    '14'       => esc_html__('Style 14', 'bdthemes-element-pack'),
                    '15'       => esc_html__('Style 15', 'bdthemes-element-pack'),
                    '16'       => esc_html__('Style 16', 'bdthemes-element-pack'),
                    '17'       => esc_html__('Style 17', 'bdthemes-element-pack'),
                    '18'       => esc_html__('Style 18', 'bdthemes-element-pack'),
                    'circle-1' => esc_html__('Style 19', 'bdthemes-element-pack'),
                    'circle-2' => esc_html__('Style 20', 'bdthemes-element-pack'),
                    'circle-3' => esc_html__('Style 21', 'bdthemes-element-pack'),
                    'circle-4' => esc_html__('Style 22', 'bdthemes-element-pack'),
                    'square-1' => esc_html__('Style 23', 'bdthemes-element-pack'),
                ],
                'condition' => [
                    'navigation' => ['both', 'arrows'],
                ],
            ]
        );

        $this->add_control(
            'both_position',
            [
                'label'     => __('Arrows and Dots Position', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'center',
                'options'   => element_pack_navigation_position(),
                'condition' => [
                    'navigation' => 'both',
                ],
            ]
        );

        $this->add_control(
            'arrows_position',
            [
                'label'     => __('Arrows Position', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'bottom-right',
                'options'   => element_pack_navigation_position(),
                'condition' => [
                    'navigation' => ['arrows'],
                ],
            ]
        );

        $this->add_control(
            'dots_position',
            [
                'label'     => __('Dots Position', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'bottom-center',
                'options'   => element_pack_pagination_position(),
                'condition' => [
                    'navigation' => 'dots',
                ],
            ]
        );

        $this->add_control(
            'hide_arrow_on_mobile',
            [
                'label'   => __('Hide Arrow on Mobile ?', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_slider_settings',
            [
                'label' => __('Slider Settings', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'slider_animations',
            [
                'label'     => esc_html__('Slider Animations', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'separator' => 'before',
                'default'   => 'slide',
                'options'   => [
                    'slide' => esc_html__('Slide', 'bdthemes-element-pack'),
                    'fade'  => esc_html__('Fade', 'bdthemes-element-pack'),
                    'scale' => esc_html__('Scale', 'bdthemes-element-pack'),
                    'push'  => esc_html__('Push', 'bdthemes-element-pack'),
                    'pull'  => esc_html__('Pull', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label'   => __('Autoplay', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'autoplay_interval',
            [
                'label'     => __('Autoplay Interval(ms)', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::NUMBER,
                'default'   => 7000,
                'condition' => [
                    'autoplay' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'pause_on_hover',
            [
                'label' => __('Pause on Hover', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'velocity',
            [
                'label' => __('Animation Speed', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min'  => 0.1,
                        'max'  => 1,
                        'step' => 0.1,
                    ],
                ],
            ]
        );

        $this->end_controls_section();

        //New Query Builder Settings
        $this->start_controls_section(
            'section_post_query_builder',
            [
                'label' => __('Query', 'bdthemes-element-pack') . BDTEP_NC,
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->register_query_builder_controls();
        $this->register_wc_query_additional('3');
        $this->end_controls_section();


        $this->start_controls_section(
            'section_style_content',
            [
                'label' => __('Content', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );


        $this->add_control(
            'content_background',
            [
                'label'     => __('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-slider-item-content' => 'background: {{VALUE}};',
                ],

            ]
        );

        $this->add_responsive_control(
            'content_padding',
            [
                'label'      => __('Content Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-slideshow-content-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'item_background',
            [
                'label'     => __('Item Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-slideshow-item-inner' => 'background: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_price',
            [
                'label'     => __('Price', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_price' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'old_price_heading',
            [
                'label' => __('Old Price', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'old_price_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-wc-slider-price del, .bdt-wc-slider .bdt-slider-skin-price del' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'old_price_margin',
            [
                'label'      => __('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-wc-slider-price del, .bdt-wc-slider .bdt-slider-skin-price del' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'old_price_typography',
                'label'    => __('Typography', 'bdthemes-element-pack'),
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} .bdt-wc-slider .bdt-wc-slider-price del, .bdt-wc-slider .bdt-slider-skin-price del',
            ]
        );

        $this->add_control(
            'sale_price_heading',
            [
                'label'     => __('Sale Price', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'sale_price_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-wc-slider-price, .bdt-wc-slider .bdt-slider-skin-price, {{WRAPPER}} .bdt-wc-slider .bdt-wc-slider-price ins, .bdt-wc-slider .bdt-slider-skin-price ins' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'sale_price_margin',
            [
                'label'      => __('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-wc-slider-price, .bdt-wc-slider .bdt-slider-skin-price, {{WRAPPER}} .bdt-wc-slider .bdt-wc-slider-price ins, .bdt-wc-slider .bdt-slider-skin-price ins' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'sale_price_typography',
                'label'    => __('Typography', 'bdthemes-element-pack'),
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} .bdt-wc-slider .bdt-wc-slider-price, .bdt-wc-slider .bdt-slider-skin-price, {{WRAPPER}} .bdt-wc-slider .bdt-wc-slider-price ins, .bdt-wc-slider .bdt-slider-skin-price ins',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_title',
            [
                'label'     => __('Title', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_title' => ['yes'],
                ],
            ]
        );

        // $this->add_responsive_control(
        // 	'title_width',
        // 	[
        // 		'label'   => __( 'Width (px)', 'bdthemes-element-pack' ),
        // 		'type'    => Controls_Manager::SLIDER,
        // 		'range' => [
        // 			'px' => [
        // 				'min' => 50,
        // 				'max' => 550,
        // 			],
        // 		],
        // 		'selectors' => [
        // 			'{{WRAPPER}} .bdt-wc-slider-slade-skin .bdt-wc-slider-title' => 'max-width: {{SIZE}}{{UNIT}};',
        // 		],
        // 		'condition' => [
        // 			'_skin!' => '',
        // 		],
        // 	]
        // );

        // $this->add_control(
        //     'show_text_stroke',
        //     [
        //         'label'        => esc_html__('Text Stroke', 'bdthemes-element-pack') . BDTEP_NC,
        //         'type'         => Controls_Manager::SWITCHER,
        //         'prefix_class' => 'bdt-text-stroke--',
        //     ]
        // );

        // $this->add_responsive_control(
        //     'text_stroke_width',
        //     [
        //         'label'     => esc_html__('Text Stroke Width', 'bdthemes-element-pack') . BDTEP_NC,
        //         'type'      => Controls_Manager::SLIDER,
        //         'selectors' => [
        //             '{{WRAPPER}} .bdt-wc-slider .bdt-slideshow-items .bdt-wc-slider-title' => '-webkit-text-stroke-width: {{SIZE}}{{UNIT}};',
        //         ],
        //         'condition' => [
        //             'show_text_stroke' => 'yes'
        //         ]
        //     ]
        // );

        $this->add_control(
            'title_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-slideshow-items .bdt-wc-slider-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_background',
            [
                'label'     => __('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-slideshow-items .bdt-wc-slider-title' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-slideshow-items .bdt-wc-slider-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_radius',
            [
                'label'      => __('Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-slideshow-items .bdt-wc-slider-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'label'    => __('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-wc-slider .bdt-slideshow-items .bdt-wc-slider-title',
            ]
        );

        $this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'title_text_stroke',
                'label' => __('Text Stroke', 'bdthemes-element-pack') . BDTEP_NC,
				'selector' => '{{WRAPPER}} .bdt-wc-slider .bdt-slideshow-items .bdt-wc-slider-title',
			]
		);

        $this->add_responsive_control(
            'title_spacing',
            [
                'label'      => __('Spacing', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-slideshow-items .bdt-wc-slider-title' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_rating',
            [
                'label'     => __('Rating', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_rating' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'rating_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#e7e7e7',
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .star-rating:before' => 'color: {{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'active_rating_color',
            [
                'label'     => __('Active Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#FFCC00',
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .star-rating span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'rating_spacing',
            [
                'label'      => __('Spacing', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-wc-rating' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_text',
            [
                'label' => __('Text', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        // $this->add_responsive_control(
        // 	'text_width',
        // 	[
        // 		'label'   => __( 'Width (px)', 'bdthemes-element-pack' ),
        // 		'type'    => Controls_Manager::SLIDER,
        // 		'range' => [
        // 			'px' => [
        // 				'min' => 50,
        // 				'max' => 650,
        // 			],
        // 		],
        // 		'selectors' => [
        // 			'{{WRAPPER}} .bdt-wc-slider-slade-skin .bdt-wc-slider-text' => 'max-width: {{SIZE}}{{UNIT}};',
        // 		],
        // 		'condition' => [
        // 			'_skin!' => '',
        // 		],
        // 	]
        // );

        $this->add_control(
            'text_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-slideshow-items .bdt-wc-slider-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'text_background',
            [
                'label'     => __('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-slideshow-items .bdt-wc-slider-text' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'text_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-slideshow-items .bdt-wc-slider-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'text_typography',
                'label'    => __('Text Typography', 'bdthemes-element-pack'),
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} .bdt-wc-slider .bdt-slideshow-items .bdt-wc-slider-text, .bdt-wc-slider .bdt-slideshow-items .bdt-wc-slider-text p',
            ]
        );

        $this->add_responsive_control(
            'text_spacing',
            [
                'label'      => __('Spacing', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-slideshow-items .bdt-wc-slider-text' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_button',
            [
                'label'     => __('Add to Cart Button', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_cart' => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_button_style');

        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'button_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-wc-add-to-cart a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background',
            [
                'label'     => __('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-wc-add-to-cart a' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'button_shadow',
                'selector' => '{{WRAPPER}} .bdt-wc-slider .bdt-wc-add-to-cart a',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'button_border',
                'label'       => __('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-wc-slider .bdt-wc-add-to-cart a',
                'separator'   => 'before',
            ]
        );

        $this->add_responsive_control(
            'button_radius',
            [
                'label'      => __('Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-wc-add-to-cart a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-wc-add-to-cart a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'add_to_cart_spacing',
            [
                'label'      => __('Spacing', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-wc-add-to-cart-readmore' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'button_typography',
                'label'    => __('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-wc-slider .bdt-wc-add-to-cart a',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'button_hover_background',
            [
                'label'     => __('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-wc-add-to-cart a:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-wc-add-to-cart a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_border_color',
            [
                'label'     => __('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'button_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-wc-add-to-cart a:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_readmore',
            [
                'label'     => esc_html__('Read More', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_readmore' => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_readmore_style');

        $this->start_controls_tab(
            'tab_readmore_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'readmore_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider-readmore'     => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-wc-slider-readmore svg'     => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'readmore_background',
            [
                'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider-readmore' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'readmore_border',
                'label'       => esc_html__('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-wc-slider-readmore',
                'separator'   => 'before',
            ]
        );

        $this->add_responsive_control(
            'readmore_radius',
            [
                'label'      => esc_html__('Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-wc-slider-readmore' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'readmore_shadow',
                'selector' => '{{WRAPPER}} .bdt-wc-slider-readmore',
            ]
        );

        $this->add_responsive_control(
            'readmore_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-wc-slider-readmore' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator'  => 'before',
            ]
        );

        $this->add_responsive_control(
            'readmore_space_between',
            [
                'label'     => esc_html__('Space Between', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-wc-slider-readmore' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'readmore_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} .bdt-wc-slider-readmore',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_readmore_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'readmore_hover_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider-readmore:hover'     => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-wc-slider-readmore:hover svg'     => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'readmore_hover_background',
            [
                'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider-readmore:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'readmore_hover_border_color',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'readmore_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider-readmore:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'readmore_hover_animation',
            [
                'label' => esc_html__('Animation', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::HOVER_ANIMATION,
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_badge',
            [
                'label'     => __('Badge', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_badge' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'badge_text_color',
            [
                'label'     => __('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-badge' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'badge_background',
            [
                'label'     => __('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-badge' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'badge_border',
                'label'       => __('Border Color', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-wc-slider .bdt-badge',
                'separator'   => 'before',
            ]
        );

        $this->add_responsive_control(
            'badge_radius',
            [
                'label'      => __('Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'badge_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'badge_margin',
            [
                'label'      => __('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-wc-slider-badge' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'badge_typography',
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} .bdt-wc-slider .bdt-badge',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_navigation',
            [
                'label'     => __('Navigation', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'navigation' => ['arrows', 'dots', 'both'],
                ],
            ]
        );

        $this->add_control(
            'heading_arrows',
            [
                'label'     => __('Arrows', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'after',
                'condition' => [
                    'navigation!' => ['none', 'dots'],
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
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-navigation-prev i, {{WRAPPER}} .bdt-wc-slider .bdt-navigation-next i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'navigation!' => ['none', 'dots'],
                ],
            ]
        );

        $this->add_control(
            'arrows_background',
            [
                'label'     => __('Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-navigation-prev i, {{WRAPPER}} .bdt-wc-slider .bdt-navigation-next i' => 'background-color: {{VALUE}}',
                ],
                'condition' => [
                    'navigation!' => ['none', 'dots'],
                ],
            ]
        );

        $this->add_control(
            'arrows_hover_background',
            [
                'label'     => __('Hover Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-navigation-prev:hover i, {{WRAPPER}} .bdt-wc-slider .bdt-navigation-next:hover i' => 'background-color: {{VALUE}}',
                ],
                'condition' => [
                    'navigation!' => ['none', 'dots'],
                ],
            ]
        );

        $this->add_control(
            'arrows_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-navigation-prev i, {{WRAPPER}} .bdt-wc-slider .bdt-navigation-next i' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'navigation!' => ['none', 'dots'],
                ],
            ]
        );

        $this->add_control(
            'arrows_hover_color',
            [
                'label'     => __('Hover Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-navigation-prev:hover i, {{WRAPPER}} .bdt-wc-slider .bdt-navigation-next:hover i' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'navigation!' => ['none', 'dots'],
                ],
            ]
        );

        $this->add_responsive_control(
            'arrows_padding',
            [
                'label'     => __('Padding', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-navigation-prev i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-wc-slider .bdt-navigation-next i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'navigation!' => ['none', 'dots'],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'arrows_border',
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-wc-slider .bdt-navigation-prev i, {{WRAPPER}} .bdt-wc-slider .bdt-navigation-next i',
                'condition'   => [
                    'navigation!' => ['none', 'dots'],
                ],
            ]
        );

        $this->add_control(
            'arrows_radius',
            [
                'label'      => __('Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-navigation-prev i, {{WRAPPER}} .bdt-wc-slider .bdt-navigation-next i' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition'  => [
                    'navigation!' => ['none', 'dots'],
                ],
            ]
        );

        $this->add_control(
            'arrows_space',
            [
                'label'     => __('Space Between Arrows', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-navigation-prev' => 'margin-right: {{SIZE}}px;',
                    '{{WRAPPER}} .bdt-wc-slider .bdt-navigation-next' => 'margin-left: {{SIZE}}px;',
                ],
                'condition' => [
                    'navigation!' => ['dots', 'progressbar', 'none'],
                ],
            ]
        );

        $this->add_control(
            'heading_dots',
            [
                'label'     => __('Dots', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'after',
                'condition' => [
                    'navigation!' => ['arrows', 'none'],
                ],
            ]
        );

        $this->add_control(
            'dots_size',
            [
                'label'     => __('Size', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 5,
                        'max' => 20,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-dotnav li a' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'navigation!' => ['arrows', 'none'],
                ],
            ]
        );

        $this->add_control(
            'dots_width',
            [
                'label'     => __('Active Size', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 5,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-dotnav li.bdt-active a' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'navigation!' => ['arrows', 'none'],
                ],
            ]
        );

        $this->add_responsive_control(
            'active_dot_radius',
            [
                'label'      => __('Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-dotnav li.bdt-active a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition'  => [
                    'navigation!' => ['arrows', 'none'],
                ],
            ]
        );

        $this->add_control(
            'dots_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-dotnav li a' => 'background-color: {{VALUE}}',
                ],
                'condition' => [
                    'navigation!' => ['arrows', 'none'],
                ],
            ]
        );

        $this->add_control(
            'active_dot_color',
            [
                'label'     => __('Active Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-dotnav li.bdt-active a' => 'background-color: {{VALUE}}',
                ],
                'condition' => [
                    'navigation!' => ['arrows', 'none'],
                ],
            ]
        );

        $this->add_control(
            'heading_position',
            [
                'label'     => __('Position', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'condition' => [
                    'navigation!' => 'none',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrows_ncx_position',
            [
                'label'      => __('Arrows Horizontal Offset', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'separator'  => 'before',
                'default'    => [
                    'size' => 0,
                ],
                'range'      => [
                    'px' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name'     => 'navigation',
                            'operator' => 'in',
                            'value'    => ['arrows'],
                        ],
                        [
                            'name'     => 'arrows_position',
                            'operator' => '!=',
                            'value'    => 'center',
                        ],
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}}' => '--ep-swiper-carousel-arrows-ncx: {{SIZE}}px;'
                ],
            ]
        );

        $this->add_responsive_control(
            'arrows_ncy_position',
            [
                'label'      => __('Arrows Vertical Offset', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'default'    => [
                    'size' => 0,
                ],
                'range'      => [
                    'px' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}}' => '--ep-swiper-carousel-arrows-ncy: {{SIZE}}px;'
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name'     => 'navigation',
                            'operator' => 'in',
                            'value'    => ['arrows'],
                        ],
                        [
                            'name'     => 'arrows_position',
                            'operator' => '!=',
                            'value'    => 'center',
                        ],
                    ],
                ],
            ]
        );

        $this->add_responsive_control(
            'arrows_acx_position',
            [
                'label'      => __('Arrows Horizontal Offset', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'default'    => [
                    'size' => 0,
                ],
                'range'      => [
                    'px' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-navigation-prev' => 'left: {{SIZE}}px;',
                    '{{WRAPPER}} .bdt-wc-slider .bdt-navigation-next' => 'right: {{SIZE}}px;',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name'     => 'navigation',
                            'operator' => 'in',
                            'value'    => ['arrows'],
                        ],
                        [
                            'name'  => 'arrows_position',
                            'value' => 'center',
                        ],
                    ],
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_nnx_position',
            [
                'label'      => __('Horizontal Offset', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'default'    => [
                    'size' => 0,
                ],
                'range'      => [
                    'px' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name'  => 'navigation',
                            'value' => 'dots',
                        ],
                        [
                            'name'     => 'dots_position',
                            'operator' => '!=',
                            'value'    => '',
                        ],
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}}' => '--ep-swiper-carousel-dots-nnx: {{SIZE}}px;'
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_nny_position',
            [
                'label'      => __('Vertical Offset', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'default'    => [
                    'size' => 30,
                ],
                'range'      => [
                    'px' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}}' => '--ep-swiper-carousel-dots-nny: {{SIZE}}px;'
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name'  => 'navigation',
                            'value' => 'dots',
                        ],
                        [
                            'name'     => 'dots_position',
                            'operator' => '!=',
                            'value'    => '',
                        ],
                    ],
                ],
            ]
        );

        $this->add_responsive_control(
            'both_ncx_position',
            [
                'label'      => __('Horizontal Offset', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'default'    => [
                    'size' => 0,
                ],
                'range'      => [
                    'px' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name'  => 'navigation',
                            'value' => 'both',
                        ],
                        [
                            'name'     => 'both_position',
                            'operator' => '!=',
                            'value'    => 'center',
                        ],
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}}' => '--ep-swiper-carousel-both-ncx: {{SIZE}}px;'
                ],
            ]
        );

        $this->add_responsive_control(
            'both_ncy_position',
            [
                'label'      => __('Vertical Offset', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'default'    => [
                    'size' => 40,
                ],
                'range'      => [
                    'px' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}}' => '--ep-swiper-carousel-both-ncy: {{SIZE}}px;'
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name'  => 'navigation',
                            'value' => 'both',
                        ],
                        [
                            'name'     => 'both_position',
                            'operator' => '!=',
                            'value'    => 'center',
                        ],
                    ],
                ],
            ]
        );

        $this->add_responsive_control(
            'both_cx_position',
            [
                'label'      => __('Arrows Offset', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'default'    => [
                    'size' => 20,
                ],
                'range'      => [
                    'px' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-navigation-prev' => 'left: {{SIZE}}px;',
                    '{{WRAPPER}} .bdt-wc-slider .bdt-navigation-next' => 'right: {{SIZE}}px;',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name'  => 'navigation',
                            'value' => 'both',
                        ],
                        [
                            'name'  => 'both_position',
                            'value' => 'center',
                        ],
                    ],
                ],
            ]
        );

        $this->add_responsive_control(
            'both_cy_position',
            [
                'label'      => __('Dots Offset', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'default'    => [
                    'size' => -40,
                ],
                'range'      => [
                    'px' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-wc-slider .bdt-dots-container' => 'transform: translateY({{SIZE}}px);',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name'  => 'navigation',
                            'value' => 'both',
                        ],
                        [
                            'name'  => 'both_position',
                            'value' => 'center',
                        ],
                    ],
                ],
            ]
        );

        $this->end_controls_section();
    }

    public function render_query($posts_per_page) {
        $settings = $this->get_settings();
        $default = $this->getGroupControlQueryArgs();
        $args    = [];
        if ($posts_per_page) {
            $args['posts_per_page'] = $posts_per_page;
        }
        $args['post_type'] = 'product';
        $product_visibility_term_ids = wc_get_product_visibility_term_ids();
        if ('yes' == $settings['product_hide_free']) {
            $args['meta_query'][] = [
                'key'     => '_price',
                'value'   => 0,
                'compare' => '>',
                'type'    => 'DECIMAL',
            ];
        }

        if ('yes' == $settings['product_hide_out_stock']) {
            $args['tax_query'][] = [
                [
                    'taxonomy' => 'product_visibility',
                    'field'    => 'term_taxonomy_id',
                    'terms'    => $product_visibility_term_ids['outofstock'],
                    'operator' => 'NOT IN',
                ],
            ];
        }

        switch ($settings['product_show_product_type']) {
            case 'featured':
                $args['tax_query'][] = [
                    'taxonomy' => 'product_visibility',
                    'field'    => 'term_taxonomy_id',
                    'terms'    => $product_visibility_term_ids['featured'],
                ];
                break;
            case 'onsale':
                $product_ids_on_sale    = wc_get_product_ids_on_sale();
                $product_ids_on_sale[]  = 0;
                $args['post__in'] = $product_ids_on_sale;
                break;
        }
        switch ($settings['posts_orderby']) {
            case 'price':
                $args['meta_key'] = '_price'; // WPCS: slow query ok.
                $args['orderby']  = 'meta_value_num';
                break;
            case 'sales':
                $args['meta_key'] = 'total_sales'; // WPCS: slow query ok.
                $args['orderby']  = 'meta_value_num';
                break;
            default:
                $args['orderby'] = $settings['posts_orderby'];
        }
        $args              = array_merge($default, $args);
        $wp_query          = new WP_Query($args);
        return $wp_query;
    }

    public function render_navigation() {
        $settings             = $this->get_settings_for_display();
        $hide_arrow_on_mobile = $settings['hide_arrow_on_mobile'] ? 'bdt-visible@m' : '';

?>
        <div class="bdt-position-z-index bdt-position-<?php echo esc_attr($settings['arrows_position'] . ' ' . $hide_arrow_on_mobile); ?>">
            <div class="bdt-arrows-container bdt-slidenav-container">
                <a href="" class="bdt-navigation-prev bdt-slidenav-previous bdt-icon bdt-slidenav" data-bdt-slideshow-item="previous">
                    <i class="ep-icon-arrow-left-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
                </a>
                <a href="" class="bdt-navigation-next bdt-slidenav-next bdt-icon bdt-slidenav" data-bdt-slideshow-item="next">
                    <i class="ep-icon-arrow-right-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    <?php
    }

    public function render_dotnavs() {
        $settings             = $this->get_settings_for_display();
        $hide_arrow_on_mobile = $settings['hide_arrow_on_mobile'] ? 'bdt-visible@m' : '';

    ?>
        <div class="bdt-position-z-index bdt-position-<?php echo esc_attr($settings['dots_position'] . ' ' . $hide_arrow_on_mobile); ?>">
            <div class="bdt-dotnav-wrapper bdt-dots-container">
                <ul class="bdt-dotnav bdt-flex-center">

                    <?php
                    $bdt_counter = 0;

                    // TODO need to delete after v6.5
                    if (isset($settings['posts']) and $settings['posts_per_page'] == 6) {
                        $limit = $settings['posts'];
                    } else {
                        $limit = $settings['posts_per_page'];
                    }

                    $wp_query = $this->render_query($limit);

                    while ($wp_query->have_posts()) : $wp_query->the_post();
                        $active = (0 == $bdt_counter) ? ' bdt-active' : '';
                        echo '<li class="bdt-slideshow-dotnav' . $active . '" data-bdt-slideshow-item=" ' . $bdt_counter . ' "><a href="#"></a></li>';
                        $bdt_counter++;
                    endwhile;
                    wp_reset_postdata(); ?>

                </ul>
            </div>
        </div>
    <?php
    }

    public function render_both_navigation() {
        $settings             = $this->get_settings_for_display();
        $hide_arrow_on_mobile = $settings['hide_arrow_on_mobile'] ? 'bdt-visible@m' : '';
    ?>

        <div class="bdt-position-z-index bdt-position-<?php echo esc_attr($settings['both_position']); ?>">
            <div class="bdt-arrows-dots-container bdt-slidenav-container ">

                <div class="bdt-flex bdt-flex-middle">
                    <div class="<?php echo esc_attr($hide_arrow_on_mobile); ?>">
                        <a href="" class="bdt-navigation-prev bdt-slidenav-previous bdt-icon bdt-slidenav" data-bdt-slideshow-item="previous">
                            <i class="ep-icon-arrow-left-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
                        </a>

                    </div>

                    <?php if ('center' !== $settings['both_position']) : ?>
                        <div class="bdt-dotnav-wrapper bdt-dots-container">
                            <ul class="bdt-dotnav">
                                <?php
                                $bdt_counter = 0;
                                // TODO need to delete after v6.5
                                if (isset($settings['posts']) and $settings['posts_per_page'] == 6) {
                                    $limit = $settings['posts'];
                                } else {
                                    $limit = $settings['posts_per_page'];
                                }

                                $wp_query = $this->render_query($limit);

                                while ($wp_query->have_posts()) : $wp_query->the_post();
                                    echo '<li class="bdt-slideshow-dotnav" data-bdt-slideshow-item="' . $bdt_counter . '"><a href="#"></a></li>';
                                    $bdt_counter++;
                                endwhile;
                                wp_reset_postdata(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="<?php echo esc_attr($hide_arrow_on_mobile); ?>">
                        <a href="" class="bdt-navigation-next bdt-slidenav-next bdt-icon bdt-slidenav" data-bdt-slideshow-item="next">
                            <i class="ep-icon-arrow-right-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
                        </a>

                    </div>

                </div>
            </div>
        </div>

    <?php
    }

    public function render_item_image() {
        $settings  = $this->get_settings_for_display();
        $image_src = wp_get_attachment_image_url(get_post_thumbnail_id(), $settings['image_size']);

        if ($image_src) :
            echo '<img src="' . esc_url($image_src) . '" alt="' . get_the_title() . '">';
        endif;

        return 0;
    }

    public function render_header() {
        $settings = $this->get_settings_for_display();

        $ratio = ($settings['slider_size_ratio']['width'] && $settings['slider_size_ratio']['height']) ? $settings['slider_size_ratio']['width'] . ":" . $settings['slider_size_ratio']['height'] : '1920:768';

        $this->add_render_attribute(
            [
                'slider_settings' => [
                    'data-bdt-slideshow' => [
                        wp_json_encode(array_filter([
                            "animation"         => $settings["slider_animations"],
                            "ratio"             => $ratio,
                            "min-height"        => $settings["slider_min_height"]["size"],
                            "autoplay"          => ($settings["autoplay"]) ? true : false,
                            "autoplay-interval" => $settings["autoplay_interval"],
                            "pause-on-hover"    => ("yes" === $settings["pause_on_hover"]) ? true : false,
                            "velocity"          => ($settings["velocity"]["size"]) ? $settings["velocity"]["size"] : 1,
                        ])),
                    ],
                ],
            ]
        );

        $this->add_render_attribute('slider_settings', 'class', 'bdt-wc-slider');

        if ('both' == $settings['navigation']) {
            $this->add_render_attribute('slider_settings', 'class', 'bdt-arrows-dots-align-' . $settings['both_position']);
        } elseif ('arrows' == $settings['navigation']) {
            $this->add_render_attribute('slider_settings', 'class', 'bdt-arrows-align-' . $settings['arrows_position']);
        } elseif ('dots' == $settings['navigation']) {
            $this->add_render_attribute('slider_settings', 'class', 'bdt-dots-align-' . $settings['dots_position']);
        }

        $slider_fullscreen = ($settings['slider_fullscreen']) ? ' data-bdt-height-viewport="offset-top: true"' : '';

    ?>
        <div <?php echo $this->get_render_attribute_string('slider_settings'); ?>>
            <div class="bdt-position-relative bdt-visible-toggle">
                <ul class="bdt-slideshow-items bdt-child-width-1-1" <?php echo esc_attr($slider_fullscreen); ?>>
                <?php
            }

            public function render_footer() {
                $settings = $this->get_settings_for_display();

                ?>
                </ul>
                <?php if ('both' == $settings['navigation']) : ?>
                    <?php $this->render_both_navigation(); ?>

                    <?php if ('center' === $settings['both_position']) : ?>
                        <?php $this->render_dotnavs(); ?>
                    <?php endif; ?>

                <?php elseif ('arrows' == $settings['navigation']) : ?>
                    <?php $this->render_navigation(); ?>
                <?php elseif ('dots' == $settings['navigation']) : ?>
                    <?php $this->render_dotnavs(); ?>
                <?php endif; ?>
            </div>
        </div>
    <?php
            }

            public function render_readmore() {
                $settings = $this->get_settings_for_display();

                $animation = ($this->get_settings('readmore_hover_animation')) ? ' elementor-animation-' . $this->get_settings('readmore_hover_animation') : '';

                if (!isset($settings['readmore_icon']) && !Icons_Manager::is_migration_allowed()) {
                    // add old default
                    $settings['readmore_icon'] = 'fas fa-arrow-right';
                }

                $migrated = isset($settings['__fa4_migrated']['wc_slider_readmore_icon']);
                $is_new   = empty($settings['readmore_icon']) && Icons_Manager::is_migration_allowed();

    ?>

        <a href="<?php echo esc_url(get_permalink()) ?>" class="bdt-wc-slider-readmore <?php echo esc_attr($animation); ?>">
            <?php echo esc_html($this->get_settings('readmore_text')); ?>

            <?php if ($settings['wc_slider_readmore_icon']['value']) : ?>
                <span class="bdt-button-icon-align-<?php echo esc_attr($this->get_settings('readmore_icon_align')); ?>">

                    <?php if ($is_new || $migrated) :
                        Icons_Manager::render_icon($settings['wc_slider_readmore_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
                    else : ?>
                        <i class="<?php echo esc_attr($settings['readmore_icon']); ?>" aria-hidden="true"></i>
                    <?php endif; ?>

                </span>
            <?php endif; ?>

        </a>
    <?php
            }

            public function render_item_content() {
                $settings = $this->get_settings_for_display();

                $this->add_render_attribute('bdt-wc-slider-title', 'class', 'bdt-wc-slider-title', true);

    ?>
        <div class="bdt-slideshow-content-wrapper bdt-padding bdt-text-<?php echo esc_attr($settings['text_align']); ?>">

            <?php if ($settings['show_price']) : ?>
                <div class="bdt-wc-slider-price">
                    <div class="wae-product-price"><?php woocommerce_template_single_price(); ?></div>
                </div>
            <?php endif; ?>

            <?php if ($settings['show_title']) : ?>
                <<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?> <?php echo $this->get_render_attribute_string('bdt-wc-slider-title'); ?>><?php the_title(); ?></<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?>>
            <?php endif; ?>

            <?php if ($settings['show_rating']) : ?>
                <div class="bdt-wc-rating bdt-flex bdt-flex-<?php echo esc_attr($settings['text_align']); ?>">
                    <?php woocommerce_template_loop_rating(); ?>
                </div>
            <?php endif; ?>

            <?php if ($settings['show_text']) : ?>
                <div class="bdt-wc-slider-text"><?php the_excerpt(); ?></div>
            <?php endif; ?>

            <?php if ($settings['show_cart'] or $settings['show_readmore']) : ?>
                <div class="bdt-wc-add-to-cart-readmore bdt-flex bdt-flex-<?php echo esc_attr($settings['text_align']); ?> bdt-flex-middle">
                    <?php if ($settings['show_cart']) : ?>
                        <div class="bdt-wc-add-to-cart">
                            <?php woocommerce_template_loop_add_to_cart(); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($settings['show_readmore']) : ?>
                        <?php $this->render_readmore(); ?>
                    <?php endif; ?>

                </div>
            <?php endif; ?>

        </div>
        <?php
            }

            public function render() {
                $settings = $this->get_settings_for_display();

                $content_reverse = $settings['content_reverse'] ? ' bdt-flex-first' : '';

                $this->render_header();

                // TODO need to delete after v6.5
                if (isset($settings['posts']) and $settings['posts_per_page'] == 6) {
                    $limit = $settings['posts'];
                } else {
                    $limit = $settings['posts_per_page'];
                }

                $wp_query = $this->render_query($limit);

                while ($wp_query->have_posts()) : $wp_query->the_post();
                    global $post, $product; ?>

            <li class="bdt-slideshow-item">
                <div class="bdt-slideshow-item-inner bdt-grid bdt-grid-collapse bdt-height-1-1" data-bdt-grid>
                    <div class="bdt-width-1-2@m bdt-flex bdt-flex-<?php echo esc_attr($settings['vertical_align']); ?> bdt-slider-item-content">
                        <?php $this->render_item_content(); ?>
                    </div>

                    <div class="bdt-width-1-2@m bdt-flex bdt-flex-<?php echo esc_attr($settings['vertical_align']); ?> bdt-mobile-order<?php echo esc_attr($content_reverse); ?>">
                        <div class="bdt-position-relative bdt-wc-slider-image">

                            <?php $this->render_item_image(); ?>

                            <?php if ($settings['show_badge'] and !$product->is_in_stock()) : ?>
                                <div class="bdt-badge bdt-position-top-left bdt-position-small">
                                    <?php //woocommerce_show_product_loop_sale_flash();
                                    ?>
                                    <?php echo apply_filters('woocommerce_product_is_in_stock', '<span class="bdt-onsale">' . esc_html__('Out of Stock!', 'woocommerce') . '</span>', $post, $product); ?>
                                </div>
                            <?php elseif ($settings['show_badge'] and $product->is_on_sale()) : ?>
                                <div class="bdt-badge bdt-position-top-left bdt-position-small">
                                    <?php //woocommerce_show_product_loop_sale_flash();
                                    ?>
                                    <?php echo apply_filters('woocommerce_sale_flash', '<span class="bdt-onsale">' . esc_html__('Sale!', 'woocommerce') . '</span>', $post, $product); ?>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>

                </div>
            </li>

<?php endwhile;
                wp_reset_postdata();

                $this->render_footer();
            }
        }
