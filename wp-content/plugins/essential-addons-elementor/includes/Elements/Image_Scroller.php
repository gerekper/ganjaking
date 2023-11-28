<?php

namespace Essential_Addons_Elementor\Pro\Elements;

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Background;
use \Elementor\Core\Schemes\Typography;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use \Elementor\Utils;
use \Elementor\Widget_Base;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

class Image_Scroller extends Widget_Base
{
    public function get_name()
    {
        return 'eael-image-scroller';
    }

    public function get_title()
    {
        return esc_html__('Image Scroller', 'essential-addons-elementor');
    }

    public function get_icon()
    {
        return 'eaicon-image-scroller';
    }

    public function get_categories()
    {
        return ['essential-addons-elementor'];
    }

    public function get_keywords()
    {
        return [
            'ea image scroller',
            'ea image scrolling effect',
            'ea scroller',
            'scrolling image',
            'vertical scrolling',
            'horizontal scrolling',
            'scrolling effect',
            'ea',
            'essential addons'
        ];
    }

    public function get_custom_help_url()
    {
        return 'https://essential-addons.com/elementor/docs/ea-image-scroller/';
    }

    protected function register_controls()
    {
        /**
         * General Settings
         */
        $this->start_controls_section(
            'eael_image_scroller_section_general',
            [
                'label' => esc_html__('General', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'eael_image_scroller_bg_img',
            [
                'label' => __('Background Image', 'essential-addons-elementor'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'ai' => [
                    'active' => false,
                ],
            ]
        );

        $this->add_control(
            'eael_image_scroller_container_height',
            [
                'label' => __('Container Height', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'description' => 'Container height/width should be less than the image height/width. Otherwise scroll will not work.',
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 1000,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 300,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-image-scroller' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'eael_image_scroller_direction',
            [
                'label' => __('Scroll Direction', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'vertical',
                'options' => [
                    'horizontal' => __('Horizontal', 'essential-addons-elementor'),
                    'vertical' => __('Vertical', 'essential-addons-elementor'),
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'eael_image_scroller_auto_scroll',
            [
                'label' => esc_html__('Auto Scroll', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'eael_image_scroller_duration',
            [
                'label' => __('Scroll Duration', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 10000,
                        'step' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 1000,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-image-scroller.eael-image-scroller-hover img' => 'transition-duration: {{SIZE}}ms;',
                ],
                'condition' => [
                    'eael_image_scroller_auto_scroll' => 'yes',
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();

        /**
         * Style Settings
         */
        $this->start_controls_section(
            'eael_image_scroller_section_style',
            [
                'label' => __('General Style', 'essential-addons-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'eael_image_scroller_radius',
            [
                'label' => __('Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}}, {{WRAPPER}} .eael-image-scroller' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'eael_image_scroller_shadow',
                'label' => __('Shadow', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-image-scroller',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $wrap_classes = ['eael-image-scroller', 'eael-image-scroller-' . $settings['eael_image_scroller_direction']];

        if ($settings['eael_image_scroller_auto_scroll'] === 'yes') {
            $wrap_classes[] = 'eael-image-scroller-hover';
        }

        echo '<div class="' . implode(' ', $wrap_classes) . '">
			<img src="' . $settings['eael_image_scroller_bg_img']['url'] . '" alt="' . esc_attr(get_post_meta($settings['eael_image_scroller_bg_img']['id'], '_wp_attachment_image_alt', true)) . '">
		</div>';
    }
}
