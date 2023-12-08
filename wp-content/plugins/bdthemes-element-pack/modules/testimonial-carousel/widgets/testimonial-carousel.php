<?php

namespace ElementPack\Modules\TestimonialCarousel\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use ElementPack\Base\Module_Base;
use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;
use ElementPack\Modules\TestimonialCarousel\Skins;
use ElementPack\Traits\Global_Swiper_Controls;
use ElementPack\Traits\Global_Widget_Controls;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

class Testimonial_Carousel extends Module_Base
{
    use Group_Control_Query;
    use Global_Widget_Controls;
    use Global_Swiper_Controls;
    private $_query = null;

    public function get_name()
    {
        return 'bdt-testimonial-carousel';
    }

    public function get_title()
    {
        return BDTEP . esc_html__('Testimonial Carousel', 'bdthemes-element-pack');
    }

    public function get_icon()
    {
        return 'bdt-wi-testimonial-carousel';
    }

    public function get_categories()
    {
        return ['element-pack'];
    }

    public function get_keywords()
    {
        return ['testimonial', 'carousel'];
    }

    public function get_style_depends()
    {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-font', 'ep-testimonial-carousel'];
        }
    }
    public function get_script_depends()
    {
        if ($this->ep_is_edit_mode()) {
            return ['ep-scripts'];
        } else {
            return ['ep-testimonial-carousel'];
        }
    }

    public function get_custom_help_url()
    {
        return 'https://youtu.be/VbojVJzayvE';
    }
    public function get_query()
    {
        return $this->_query;
    }
    protected function register_skins()
    {
        $this->add_skin(new Skins\Skin_Twyla($this));
        $this->add_skin(new Skins\Skin_Vyxo($this));
    }

    protected function register_controls()
    {
        $slides_per_view = range(1, 10);
        $slides_per_view = array_combine($slides_per_view, $slides_per_view);

        $this->start_controls_section(
            'section_content_layout',
            [
                'label' => esc_html__('Layout', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
			'layout_style',
			[
				'label'   => esc_html__('Layout Style', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => '01',
					'style-2'  => '02',
					'style-3'  => '03',
				],
				'condition' => [
					'_skin' => 'bdt-twyla',
				],
			]
		);

        $this->add_responsive_control(
            'columns',
            [
                'label' => esc_html__('Columns', 'bdthemes-element-pack'),
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
                'frontend_available' => true,
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
            'show_image',
            [
                'label' => esc_html__('Testimonial Image', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before',
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
            'show_address',
            [
                'label' => esc_html__('Address', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'meta_multi_line',
            [
                'label' => esc_html__('Meta Multiline', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_comma',
            [
                'label' => esc_html__('Show Comma After Title', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'show_text',
            [
                'label' => esc_html__('Text', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'text_limit',
            [
                'label' => esc_html__('Text Limit', 'bdthemes-element-pack'),
                'description' => esc_html__('It\'s just work for main content, but not working with excerpt. If you set 0 so you will get full main content.', 'bdthemes-element-pack'),
                'type' => Controls_Manager::NUMBER,
                'default' => 40,
                'condition' => [
                    'show_text' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'strip_shortcode',
            [
                'label' => esc_html__('Strip Shortcode', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'show_text' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_rating',
            [
                'label' => esc_html__('Rating', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'rating_bullet',
            [
                'label' => esc_html__('Rating Bullet', 'bdthemes-element-pack') . BDTEP_NC,
                'type' => Controls_Manager::SWITCHER,
                'prefix_class' => 'bdt-rating-bullet--',
                'render_type' => 'template',
                'condition' => [
                    'show_rating' => 'yes',
                    // '_skin' => 'bdt-vyxo',
                ],
            ]
        );

        $this->add_control(
            'rating_position',
            [
                'label' => esc_html__('Rating Position', 'bdthemes-element-pack') . BDTEP_NC,
                'type' => Controls_Manager::SELECT,
                'default' => 'bottom',
                'options' => [
                    'top' => __('Top', 'bdthemes-element-pack'),
                    'bottom' => __('Bottom', 'bdthemes-element-pack'),
                ],
                'condition' => [
                    'show_rating' => 'yes',
                    '_skin' => 'bdt-vyxo',
                ],
            ]
        );

        $this->add_control(
            'show_review_platform',
            [
                'label' => esc_html__('Review Platform', 'bdthemes-element-pack') . BDTEP_NC,
                'type' => Controls_Manager::SWITCHER,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'content_alignment',
            [
                'label' => esc_html__('Alignment', 'bdthemes-element-pack'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item-wrapper, {{WRAPPER}} .bdt-testimonial-carousel.skin-vyxo .bdt-testimonial-carousel-item' => 'text-align: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'item_match_height',
            [
                'label' => esc_html__('Item Match Height', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'render_type' => 'template',
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
                'label' => __('Source', 'bdthemes-element-pack'),
                'type' => Controls_Manager::HIDDEN,
                'options' => $this->getGroupControlQueryPostTypes(),
                'default' => 'bdthemes-testimonial',

            ]
        );
        $this->update_control(
            'posts_per_page',
            [
                'default' => 10,
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
            'section_style_item',
            [
                'label' => esc_html__('Items', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // content padding
        $this->add_responsive_control(
            'content_padding',
            [
                'label' => esc_html__('Content Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .skin-twyla .bdt-twyla-content-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    '_skin' => 'bdt-twyla',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_item_style');

        $this->start_controls_tab(
            'tab_item_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'item_background',
            [
                'label' => esc_html__('Background', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item-wrapper' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'item_border',
                'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'item_border_radius',
            [
                'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item, {{WRAPPER}} .bdt-testimonial-carousel .swiper-carousel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'item_shadow',
                'selector' => '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item',
            ]
        );

        $this->add_responsive_control(
            'item_padding',
            [
                'label' => esc_html__('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'shadow_mode',
            [
                'label' => esc_html__('Shadow Mode', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'prefix_class' => 'bdt-ep-shadow-mode-',
            ]
        );

        $this->add_control(
            'shadow_color',
            [
                'label' => esc_html__('Shadow Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'shadow_mode' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-widget-container:before' => is_rtl() ? 'background: linear-gradient(to left, {{VALUE}} 5%,rgba(255,255,255,0) 100%);' : 'background: linear-gradient(to right, {{VALUE}} 5%,rgba(255,255,255,0) 100%);',
                    '{{WRAPPER}} .elementor-widget-container:after' => is_rtl() ? 'background: linear-gradient(to left, rgba(255,255,255,0) 0%, {{VALUE}} 95%);' : 'background: linear-gradient(to right, rgba(255,255,255,0) 0%, {{VALUE}} 95%);',
                ],
            ]
        );

        $this->add_control(
            'item_opacity',
            [
                'label' => esc_html__('Opacity', 'bdthemes-element-pack') . BDTEP_NC,
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'step' => 0.1,
                        'max' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_item_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'item_hover_background',
            [
                'label' => esc_html__('Background', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item-wrapper:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'item_hover_border_color',
            [
                'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'item_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'item_hover_shadow',
                'selector' => '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item:hover',
            ]
        );

        $this->add_responsive_control(
            'item_shadow_padding',
            [
                'label' => __('Match Padding', 'bdthemes-element-pack'),
                'description' => __('You have to add padding for matching overlaping hover shadow', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'step' => 1,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .swiper-carousel' => 'padding: {{SIZE}}{{UNIT}}; margin: 0 -{{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'item_hover_opacity',
            [
                'label' => esc_html__('Opacity', 'bdthemes-element-pack') . BDTEP_NC,
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'step' => 0.1,
                        'max' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item:hover' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_item_active',
            [
                'label' => __('Active', 'bdthemes-element-pack') . BDTEP_NC,
            ]
        );

        $this->add_control(
            'item_active_background',
            [
                'label' => __('Background', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item.swiper-slide-active .bdt-testimonial-carousel-item-wrapper' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'item_active_border_color',
            [
                'label' => __('Border Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'item_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item.swiper-slide-active' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'item_active_shadow',
                'selector' => '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item.swiper-slide-active',
            ]
        );

        $this->add_control(
            'item_active_opacity',
            [
                'label' => esc_html__('Opacity', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'step' => 0.1,
                        'max' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item.swiper-slide-active' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_image',
            [
                'label' => esc_html__('Image', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_image' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'image_background_color',
                'selector' => '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-img-wrapper',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'image_border',
                'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-img-wrapper',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'image_hover_border_color',
            [
                'label' => esc_html__('Hover Border Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'image_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-img-wrapper:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_border_radius',
            [
                'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-img-wrapper, {{WRAPPER}} .bdt-testimonial-carousel-img-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_padding',
            [
                'label' => esc_html__('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-img-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'image_box_shadow',
                'selector' => '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-img-wrapper',
            ]
        );

        $this->add_responsive_control(
            'image_size',
            [
                'label' => esc_html__('Size', 'bdthemes-element-pack') . BDTEP_NC,
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-img-wrapper' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'image_offset',
            [
                'label' => esc_html__('Vertical Spacing', 'bdthemes-element-pack') . BDTEP_NC,
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-img-wrapper' => 'transform: translateY({{SIZE}}px);',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_title',
            [
                'label' => esc_html__('Title', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
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
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_active_color',
            [
                'label' => esc_html__('Active Color', 'bdthemes-element-pack') . BDTEP_NC,
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .swiper-slide-active .bdt-testimonial-carousel-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => esc_html__('Typography', 'bdthemes-element-pack'),
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-title',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_address',
            [
                'label' => esc_html__('Address', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_address' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'address_color',
            [
                'label' => esc_html__('Company Name/Address Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-address' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'address_active_color',
            [
                'label' => esc_html__('Company Name/Address Active Color', 'bdthemes-element-pack') . BDTEP_NC,
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .swiper-slide-active .bdt-testimonial-carousel-address' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'address_margin',
            [
                'label' => esc_html__('Margin', 'bdthemes-element-pack') . BDTEP_NC,
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel-address' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'address_typography',
                'label' => esc_html__('Typography', 'bdthemes-element-pack'),
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-address',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_text',
            [
                'label' => esc_html__('Text', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_text' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'text_active_color',
            [
                'label' => esc_html__('Active Color', 'bdthemes-element-pack') . BDTEP_NC,
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .swiper-slide-active .bdt-testimonial-carousel-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'text_top_border_color',
            [
                'label' => esc_html__('Top Border Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-text' => 'border-top-color: {{VALUE}};',
                ],
                'condition' => [
                    '_skin' => '',
                ],
            ]
        );

        $this->add_control(
            'active_text_top_border_color',
            [
                'label' => esc_html__('Top Border Active Color', 'bdthemes-element-pack') . BDTEP_NC,
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .swiper-slide-active .bdt-testimonial-carousel-text' => 'border-top-color: {{VALUE}};',
                ],
                'condition' => [
                    '_skin' => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'text_padding',
            [
                'label' => esc_html__('Padding', 'bdthemes-element-pack') . BDTEP_NC,
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'text_margin',
            [
                'label' => esc_html__('Margin', 'bdthemes-element-pack') . BDTEP_NC,
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'selector' => '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-text',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_rating',
            [
                'label' => esc_html__('Rating', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_rating' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'original_color',
            [
                'label' => esc_html__('Enable Original Color', 'bdthemes-element-pack') . BDTEP_NC,
                'type' => Controls_Manager::SWITCHER,
                'condition' => [
                    'show_review_platform' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'rating_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'default' => '#e7e7e7',
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-rating .bdt-rating-item' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'original_color' => '',
                ],
            ]
        );

        $this->add_control(
            'active_rating_color',
            [
                'label' => esc_html__('Active Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'default' => '#FFCC00',
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-rating.bdt-rating-1 .bdt-rating-item:nth-child(1)' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-rating.bdt-rating-2 .bdt-rating-item:nth-child(-n+2)' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-rating.bdt-rating-3 .bdt-rating-item:nth-child(-n+3)' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-rating.bdt-rating-4 .bdt-rating-item:nth-child(-n+4)' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-rating.bdt-rating-5 .bdt-rating-item:nth-child(-n+5)' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'original_color' => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'rating_size',
            [
                'label' => esc_html__('Size', 'bdthemes-element-pack') . BDTEP_NC,
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-widget-container .bdt-rating .bdt-rating-item' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'rating_spacing',
            [
                'label' => esc_html__('Spacing', 'bdthemes-element-pack') . BDTEP_NC,
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-widget-container .bdt-rating .bdt-rating-item' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'rating_margin',
            [
                'label' => esc_html__('Margin', 'bdthemes-element-pack') . BDTEP_NC,
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-rating' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_review_platform',
            [
                'label' => __('Review Platform', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_review_platform' => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_platform_style');

        $this->start_controls_tab(
            'tab_platform_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'platform_text_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-review-platform i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'platform_background_color',
                'selector' => '{{WRAPPER}} .bdt-review-platform',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(), [
                'name' => 'platform_border',
                'label' => esc_html__('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .bdt-review-platform',
            ]
        );

        $this->add_responsive_control(
            'platform_border_radius',
            [
                'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-review-platform' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'platform_text_padding',
            [
                'label' => esc_html__('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-review-platform' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'platform_text_margin',
            [
                'label' => esc_html__('Margin', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-review-platform' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'platform_shadow',
                'selector' => '{{WRAPPER}} .bdt-review-platform',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'platform_typography',
                'selector' => '{{WRAPPER}} .bdt-review-platform',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_platform_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'platform_hover_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-review-platform:hover i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'platform_background_hover_color',
                'selector' => '{{WRAPPER}} .bdt-review-platform:hover',

            ]
        );

        $this->add_control(
            'platform_hover_border_color',
            [
                'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'platform_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-review-platform:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
			'section_style_quatation',
			[
				'label' => esc_html__('Quatation', 'bdthemes-element-pack') . BDTEP_NC,
				'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    '_skin' => 'bdt-twyla',
                    'layout_style!' => 'style-1',
                ],
			]
		);

		$this->add_control(
			'quatation_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .skin-twyla .testimonial-item-header::after' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'quatation_background_color',
                'selector' => '{{WRAPPER}} .skin-twyla .testimonial-item-header::after',
            ]
        );
        
        $this->add_group_control(
            Group_Control_Border::get_type(), [
                'name'        => 'quatation_border',
                'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .skin-twyla .testimonial-item-header::after',
            ]
        );
        
        $this->add_responsive_control(
            'quatation_border_radius',
            [
                'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .skin-twyla .testimonial-item-header::after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
		$this->add_responsive_control(
			'testimonial_quatation_size',
			[
				'label'     => esc_html__('Size', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 10,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .skin-twyla .testimonial-item-header::after' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; line-height: calc(20px + {{SIZE}}{{UNIT}});',
				],
			]
		);

        $this->add_responsive_control(
            'quatation_margin',
            [
                'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .skin-twyla .testimonial-item-header::after' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'quatation_typography',
				'selector' => '{{WRAPPER}} .skin-twyla .testimonial-item-header::after',
			]
		);

        $this->add_control(
            'quatation_offset_toggle',
            [
                'label' => __('Offset', 'bdthemes-element-pack'),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'label_off' => __('None', 'bdthemes-element-pack'),
                'label_on' => __('Custom', 'bdthemes-element-pack'),
                'return_value' => 'yes',
                'separator' => 'before',

            ]
        );

        $this->start_popover();

        $this->add_responsive_control(
            'quatation_horizontal_offset',
            [
                'label' => __('Horizontal Offset', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'tablet_default' => [
                    'size' => 0,
                ],
                'mobile_default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => -300,
                        'step' => 2,
                        'max' => 300,
                    ],
                ],
                'condition' => [
                    'quatation_offset_toggle' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-testimonial-carousel-quatation-h-offset: {{SIZE}}px;'
                ],
            ]
        );

        $this->add_responsive_control(
            'quatation_vertical_offset',
            [
                'label' => __('Vertical Offset', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'tablet_default' => [
                    'size' => 0,
                ],
                'mobile_default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => -300,
                        'step' => 2,
                        'max' => 300,
                    ],
                ],
                'condition' => [
                    'quatation_offset_toggle' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-testimonial-carousel-quatation-v-offset: {{SIZE}}px;'
                ],
            ]
        );

        $this->add_responsive_control(
            'quatation_rotate',
            [
                'label' => esc_html__('Rotate', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'tablet_default' => [
                    'size' => 0,
                ],
                'mobile_default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => -360,
                        'max' => 360,
                        'step' => 5,
                    ],
                ],
                'condition' => [
                    'quatation_offset_toggle' => 'yes'
                ],
                'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-testimonial-carousel-quatation-rotate: {{SIZE}}deg;'
                ],
            ]
        );

        $this->end_popover();


		$this->end_controls_section();


        //Navigation Style
        $this->start_controls_section(
            'section_style_navigation',
            [
                'label' => __('Navigation', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'navigation',
                            'operator' => '!=',
                            'value' => 'none',
                        ],
                        [
                            'name' => 'show_scrollbar',
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

    public function render_review_platform($post_id)
    {
        $settings = $this->get_settings_for_display();

        if (!$settings['show_review_platform']) {
            return;
        }

        $platform = get_post_meta($post_id, 'bdthemes_tm_platform', true);
        $review_link = get_post_meta($post_id, 'bdthemes_tm_review_link', true);

        if (!$platform) {
            $platform = 'self';
        }

        if (!$review_link) {
            $review_link = '#';
        }

        ?>
        <a href="<?php echo $review_link; ?>" class="bdt-review-platform bdt-flex-inline" bdt-tooltip="<?php echo $platform; ?>">
            <i class="ep-icon-<?php echo strtolower($platform); ?> bdt-platform-icon bdt-flex bdt-flex-middle bdt-flex-center" aria-hidden="true"></i>
        </a>
        <?php
}

    public function render_image($image_id)
    {
        $settings = $this->get_settings_for_display();

        if ('yes' != $settings['show_image']) {
            return;
        }

        $testimonial_thumb = wp_get_attachment_image_src(get_post_thumbnail_id($image_id), 'medium');

        if (!$testimonial_thumb) {
            $testimonial_thumb = BDTEP_ASSETS_URL . 'images/member.svg';
        } else {
            $testimonial_thumb = $testimonial_thumb[0];
        }

        ?>
        <div class="bdt-width-auto bdt-flex bdt-position-relative">
            <div class="bdt-testimonial-carousel-img-wrapper bdt-overflow-hidden bdt-border-circle bdt-background-cover">
                <img src="<?php echo esc_url($testimonial_thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
            </div>
            <?php $this->render_review_platform(get_the_ID());?>
        </div>
    <?php
}

    public function render_title($post_id)
    {
        $settings = $this->get_settings_for_display();

        if ('yes' != $settings['show_title']) {
            return;
        }

        ?>
        <h4 class="bdt-testimonial-carousel-title bdt-margin-remove-bottom" itemprop="name"><?php echo esc_attr(get_the_title($post_id)); ?><?php if ($settings['show_comma']) {
            echo (($settings['show_title']) and ($settings['show_address'])) ? ', ' : '';
        }?></h4>
    <?php
}

    public function render_address($post_id)
    {
        $settings = $this->get_settings_for_display();

        if (!$settings['show_address']) {
            return;
        }

        ?>
        <p class="bdt-testimonial-carousel-address bdt-text-meta">
            <?php echo get_post_meta($post_id, 'bdthemes_tm_company_name', true); ?>
        </p>
    <?php
}

    public function render_excerpt()
    {

        if (!$this->get_settings('show_text')) {
            return;
        }

        $strip_shortcode = $this->get_settings_for_display('strip_shortcode');

        ?>
        <div class="bdt-testimonial-carousel-text" itemprop="description">
            <?php
if (has_excerpt()) {
            the_excerpt();
        } else {
            echo element_pack_custom_excerpt($this->get_settings_for_display('text_limit'), $strip_shortcode);
        }
        ?>
        </div>
    <?php
}

    public function render_rating($post_id)
    {
        $settings = $this->get_settings_for_display();

        if ('yes' != $settings['show_rating']) {
            return;
        }

        ?>
        <meta itemprop="datePublished" content="<?php echo get_the_date(); ?>">
        <ul class="bdt-rating bdt-rating-<?php echo get_post_meta($post_id, 'bdthemes_tm_rating', true); ?> bdt-grid bdt-grid-collapse" data-bdt-grid itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
            <li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
            <li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
            <li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
            <li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
            <li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
        </ul>
        <meta itemprop="worstRating" content="1">
        <meta itemprop="ratingValue" content="<?php echo get_post_meta($post_id, 'bdthemes_tm_rating', true); ?>">
        <meta itemprop="bestRating" content="5">
    <?php
}

    public function render_header($skin = 'default')
    {
        $settings = $this->get_settings_for_display();

        //Global Function
        $this->render_swiper_header_attribute('testimonial-carousel');

        $this->add_render_attribute('carousel', 'class', 'bdt-testimonial-carousel bdt-testimonials-twyla-'.$settings['layout_style'].' skin-' . $skin);
        

        if ('yes' == $settings['item_match_height']) {
            $this->add_render_attribute('carousel', 'data-bdt-height-match', 'target: > div > div > div > div > .bdt-testimonial-carousel-text');
        }

        ?>
        <div <?php echo $this->get_render_attribute_string('carousel'); ?>>
            <div <?php echo $this->get_render_attribute_string('swiper'); ?>>
                <div class="swiper-wrapper">
                    <?php
}

    public function render_query($posts_per_page)
    {
        $args = [];
        $args['posts_per_page'] = $posts_per_page;
        $args['paged'] = max(1, get_query_var('paged'), get_query_var('page'));

        $default = $this->getGroupControlQueryArgs();
        $args = array_merge($default, $args);

        return $this->_query = new \WP_Query($args);
    }
    public function render_loop_item()
    {
        $settings = $this->get_settings_for_display();

        // TODO need to delete after v6.5
        if (isset($settings['posts']) and $settings['posts_per_page'] == 10) {
            $limit = $settings['posts'];
        } else {
            $limit = $settings['posts_per_page'];
        }

        $wp_query = $this->render_query($limit);

        if ($wp_query->have_posts()) {
            while ($wp_query->have_posts()): $wp_query->the_post();

                $platform = get_post_meta(get_the_ID(), 'bdthemes_tm_platform', true);
                ?>
	                            <div class="swiper-slide bdt-testimonial-carousel-item bdt-review-<?php echo strtolower($platform); ?>" itemprop="review" itemscope itemtype="http://schema.org/Review">
	                                <div class="bdt-testimonial-carousel-item-wrapper">
	                                    <div class="testimonial-item-header">
	                                        <div class="bdt-grid bdt-grid-small bdt-flex-middle" data-bdt-grid>

	                                            <?php
    $this->render_image(get_the_ID());

                if ($settings['show_rating'] || $settings['show_text'] || $settings['show_address']): ?>
	                                                <div class="bdt-width-expand">
	                                                    <div class="bdt-testimonial-meta <?php echo ($settings['meta_multi_line']) ? '' : 'bdt-meta-multi-line'; ?>">
	                                                        <?php
    $this->render_title(get_the_ID());
                $this->render_address(get_the_ID());
                if ($settings['show_rating'] && ('yes' != $settings['show_text'])): ?>
	                                                            <div class="bdt-testimonial-carousel-rating bdt-margin-small-top bdt-padding-remove">
	                                                                <?php $this->render_rating(get_the_ID());?>
	                                                            </div>
	                                                        <?php endif;?>
                                                    </div>
                                                </div>
                                            <?php endif;?>
                                        </div>
                                    </div>

                                    <?php $this->render_excerpt();?>

                                    <?php if ($settings['show_rating'] && $settings['show_text']): ?>
                                        <div class="bdt-testimonial-carousel-rating">
                                            <?php $this->render_rating(get_the_ID());?>
                                        </div>
                                    <?php endif;?>
                                </div>
                            </div>
            <?php endwhile;
            wp_reset_postdata();
        } else {
            echo '<div class="bdt-alert-warning" bdt-alert>Oppps!! There is no post, please select actual post or categories.<div>';
        }
    }

    public function render()
    {
        $this->render_header();
        $this->render_loop_item();
        $this->render_footer();
    }
}
