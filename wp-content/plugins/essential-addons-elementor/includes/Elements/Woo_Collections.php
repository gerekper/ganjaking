<?php

namespace Essential_Addons_Elementor\Pro\Elements;

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Typography;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use \Elementor\Utils;
use \Elementor\Widget_Base;
use \Essential_Addons_Elementor\Pro\Classes\Helper;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

class Woo_Collections extends Widget_Base
{

    public function get_name()
    {
        return 'eael-woo-collections';
    }

    public function get_title()
    {
        return esc_html__('Woo Product Collections', 'essential-addons-elementor');
    }

    public function get_icon()
    {
        return 'eaicon-woo-product-collections';
    }

	public function get_categories() {
		return [ 'essential-addons-elementor', 'woocommerce-elements' ];
	}

    public function get_keywords()
    {
        return [
            'woo product collections',
            'ea woo product collections',
            'woocommerce product collections',
            'ea woocommerce product collections',
            'ecommerce product collections',
            'woocommerce',
            'product list',
            'woo',
            'product feed',
            'ecommerce',
            'ea',
            'essential addons',
        ];
    }

    public function get_custom_help_url()
    {
        return 'https://essential-addons.com/elementor/docs/ea-woo-product-collections/';
    }

    protected function register_controls()
    {
        /**
         * General Settings
         */
        $this->start_controls_section(
            'eael_woo_collections_section_general',
            [
                'label' => esc_html__('General', 'essential-addons-elementor'),
            ]
        );

        if (!apply_filters('eael/is_plugin_active', 'woocommerce/woocommerce.php')) {
            $this->add_control(
                'ea_woo_collections_woo_required',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'raw' => __('<strong>WooCommerce</strong> is not installed/activated on your site. Please install and activate <a href="plugin-install.php?s=woocommerce&tab=search&type=term" target="_blank">WooCommerce</a> first.', 'essential-addons-for-elementor-lite'),
                    'content_classes' => 'eael-warning',
                ]
            );
        }

        $this->add_control(
            'eael_woo_collections_type',
            [
                'label' => esc_html__('Collection Type', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'category',
                'label_block' => false,
                'options' => [
                    'category' => esc_html__('Category', 'essential-addons-elementor'),
                    'tags' => esc_html__('Tags', 'essential-addons-elementor'),
                    'attributes' => esc_html__('Attributes', 'essential-addons-elementor'),
                ],
            ]
        );

        $this->add_control(
            'eael_woo_collections_category',
            [
                'label' => esc_html__('Category', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'label_block' => false,
                'options' => Helper::get_terms_list('product_cat'),
                'condition' => [
                    'eael_woo_collections_type' => 'category',
                ],
            ]
        );

        $this->add_control(
            'eael_woo_collections_tags',
            [
                'label' => esc_html__('Tag', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'label_block' => false,
                'options' => Helper::get_woo_product_tags(),
                'condition' => [
                    'eael_woo_collections_type' => 'tags',
                ],
            ]
        );

        $this->add_control(
            'eael_woo_collections_attributes',
            [
                'label' => esc_html__('Attribute', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'label_block' => false,
                'options' => Helper::get_woo_product_atts(),
                'condition' => [
                    'eael_woo_collections_type' => 'attributes',
                ],
            ]
        );

        $this->add_control(
            'eael_woo_collections_bg_img',
            [
                'label' => __('Background Image', 'essential-addons-elementor'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'separator' => 'before',
                'ai' => [
                    'active' => false,
                ],
            ]
        );

        $this->add_control(
            'eael_woo_collections_subtitle',
            [
                'label' => __('Subtitle', 'essential-addons-elementor'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __('Collections', 'essential-addons-elementor'),
                'separator' => 'before',
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'eael_woo_collections_is_show_badge',
            [
                'label' => __('Show Badge', 'essential-addons-elementor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'essential-addons-elementor'),
                'label_off' => __('Hide', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'eael_woo_collections_badge_label',
            [
                'label' => __('Badge Label', 'essential-addons-elementor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Sale', 'essential-addons-elementor'),
                'placeholder' => __('Type your lable here', 'essential-addons-elementor'),
                'condition' => [
                    'eael_woo_collections_is_show_badge'    => 'yes'
                ],
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->end_controls_section();

        /**
         * Style: General
         */
        $this->start_controls_section(
            'eael_woo_collections_section_style_general',
            [
                'label' => esc_html__('General', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'eael_woo_collections_layout',
            [
                'label' => __('Choose Layout', 'essential-addons-elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    ''  => __('Default Style', 'essential-addons-elementor'),
                    'two' => __('Style Two', 'essential-addons-elementor'),
                ],
            ]
        );
        $this->add_control(
            'eael_woo_collections_item_heading_style',
            [
                'label' => __('Layout Style', 'essential-addons-elementor'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
//                'condition' => [
//                    'eael_woo_collections_layout'   => 'two'
//                ]
            ]
        );

	    $this->add_control(
		    'eael_woo_collections_main_area_padding',
		    [
			    'label' => __('Padding', 'essential-addons-elementor'),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => ['px', '%', 'em'],
			    'default'   => [
				    'top'   => '20',
				    'right'   => '20',
				    'bottom'   => '20',
				    'left'   => '20',
				    'isLinked'   => true,
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .eael-woo-collections-layout-two' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				    '{{WRAPPER}} .eael-woo-collections-layout-two .eael-woo-collections-overlay' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'condition' => [
				    'eael_woo_collections_layout'   => 'two'
			    ]
		    ]
	    );

        $this->add_control(
            'eael_woo_collections_overlay_padding',
            [
                'label' => __('Overlay Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default'   => [
                    'top'   => '20',
                    'right'   => '20',
                    'bottom'   => '20',
                    'left'   => '20',
                    'isLinked'   => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-woo-collections-overlay' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'eael_woo_collections_main_area_radius',
            [
                'label' => __('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default'   => [
                    'top'   => '5',
                    'right'   => '5',
                    'bottom'   => '5',
                    'left'   => '5',
                    'isLinked'   => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-woo-collections-layout-, {{WRAPPER}} .eael-woo-collections-layout-two' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
//                'condition' => [
//                    'eael_woo_collections_layout'   => 'two'
//                ]
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'eael_woo_collections_main_area_border',
                'label' => __('Border', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-woo-collections-layout-two, {{WRAPPER}} .eael-woo-collections-layout-',
//                'condition' => [
//                    'eael_woo_collections_layout'   => 'two'
//                ]
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'eael_woo_collections_main_area_shadow',
                'label' => __('Shadow', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-woo-collections-layout-two, {{WRAPPER}} .eael-woo-collections-layout-',
//                'condition' => [
//                    'eael_woo_collections_layout'   => 'two'
//                ]
            ]
        );

        // thumbnail style
        $this->add_control(
            'eael_woo_collections_thumbnail_heading_style',
            [
                'label' => __('Thumbnail Style', 'essential-addons-elementor'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'eael_woo_collections_overlay_space',
            [
                'label' => __('Overlay Spacing', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .eael-woo-collections-overlay' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'eael_woo_collections_layout'   => ''
                ]
            ]
        );

        $this->add_control(
            'eael_woo_collections_overlay_bg',
            [
                'label' => __('Overlay Background', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#0000004d',
                'selectors' => [
                    '{{WRAPPER}} .eael-woo-collections-overlay' => 'background-color: {{VALUE}}',
                ],
                'condition' => [
                    'eael_woo_collections_layout'   => ''
                ]
            ]
        );

        $this->add_control(
            'eael_woo_collections_overlay_bg_hover',
            [
                'label' => __('Overlay Background Hover', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#00000080',
                'selectors' => [
                    '{{WRAPPER}} .eael-woo-collections-overlay:hover' => 'background-color: {{VALUE}}',
                ],
                'condition' => [
                    'eael_woo_collections_layout'   => ''
                ]
            ]
        );

	    $this->add_control(
		    'eael_woo_collections_overlay_radius',
		    [
			    'label' => __('Border Radius', 'essential-addons-elementor'),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => ['px', '%', 'em'],
			    'selectors' => [
				    '{{WRAPPER}} .eael-woo-collections-layout- .eael-woo-collections-overlay, {{WRAPPER}} .eael-woo-collections-layout-two > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
		    ]
	    );

        $this->add_control(
            'eael_woo_collections_overlay_content_hr',
            [
                'label' => esc_html__('Horizontal Align', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'eael-woo-collections-overlay-left',
                'label_block' => false,
                'options' => [
                    'eael-woo-collections-overlay-left' => esc_html__('Left', 'essential-addons-elementor'),
                    'eael-woo-collections-overlay-center' => esc_html__('Center', 'essential-addons-elementor'),
                    'eael-woo-collections-overlay-right' => esc_html__('Right', 'essential-addons-elementor'),
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'eael_woo_collections_overlay_content_vr',
            [
                'label' => esc_html__('Vertical Align', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'eael-woo-collections-overlay-inner-bottom',
                'label_block' => false,
                'options' => [
                    'eael-woo-collections-overlay-inner-top' => esc_html__('Top', 'essential-addons-elementor'),
                    'eael-woo-collections-overlay-inner-middle' => esc_html__('Middle', 'essential-addons-elementor'),
                    'eael-woo-collections-overlay-inner-bottom' => esc_html__('Bottom', 'essential-addons-elementor'),
                ],
                'condition' => [
                    'eael_woo_collections_layout'   => ''
                ]
            ]
        );

        $this->add_control(
            'eael_woo_collections_content_alignment',
            [
                'label' => __('Content Alignment', 'essential-addons-elementor'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
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
                'default' => 'center',
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .eael-woo-collections-overlay-inner'  => 'text-align: {{VALUE}}',
                ],
                'condition' => [
                    'eael_woo_collections_layout'   => 'two'
                ]
            ]
        );

        $this->add_control(
            'eael_woo_collections_bg_hover_effect',
            [
                'label' => esc_html__('Image Hover Effect', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'eael-woo-collections-bg-hover-zoom-in',
                'label_block' => false,
                'options' => [
                    'eael-woo-collections-bg-hover-none' => esc_html__('None', 'essential-addons-elementor'),
                    'eael-woo-collections-bg-hover-zoom-in' => esc_html__('ZoomIn', 'essential-addons-elementor'),
                    'eael-woo-collections-bg-hover-zoom-out' => esc_html__('ZoomOut', 'essential-addons-elementor'),
                    'eael-woo-collections-bg-hover-blur' => esc_html__('Blur', 'essential-addons-elementor'),
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();


        /**
         * Style: General
         */
        $this->start_controls_section(
            'eael_woo_collections_section_style_typography',
            [
                'label' => esc_html__('Typography', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_woo_collections_title_typography',
                'label' => __('Title', 'essential-addons-elementor'),
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
                'selector' => '{{WRAPPER}} .eael-woo-collections-overlay-inner h2',
            ]
        );

        $this->add_control(
            'eael_woo_collections_title_color',
            [
                'label' => __('Title Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .eael-woo-collections-overlay-inner h2' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'eael_woo_collections_title_color_hover',
            [
                'label' => __('Title Color Hover', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .eael-woo-collections:hover .eael-woo-collections-overlay-inner h2' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .eael-woo-collections-layout-two .eael-woo-collections-overlay-inner h2:hover' => 'color: {{VALUE}}',
                ],
                'separator' => 'after',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_woo_collections_span_typography',
                'label' => __('Subtitle', 'essential-addons-elementor'),
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
                'selector' => '{{WRAPPER}} .eael-woo-collections-overlay-inner span',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'eael_woo_collections_span_color',
            [
                'label' => __('Subtitle Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .eael-woo-collections-overlay-inner span' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'eael_woo_collections_title_span_hover',
            [
                'label' => __('Subtitle Color Hover', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .eael-woo-collections:hover .eael-woo-collections-overlay-inner span' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .eael-woo-collections-layout-two .eael-woo-collections-overlay-inner span:hover' => 'color: {{VALUE}}',
                ],
                'separator' => 'after',
            ]
        );

        $this->end_controls_section();

        /**
         * Badge Style
         */
        $this->start_controls_section(
            'eael_woo_collections_badge_style',
            [
                'label' => esc_html__('Badge', 'essential-addons-elementor'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'eael_woo_collections_is_show_badge'   => 'yes'
                ]
            ]
        );
        $this->add_control(
            'eael_woo_collections_badge_padding',
            [
                'label' => __('Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .eael-woo-collection-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'eael_woo_collections_badge_radius',
            [
                'label' => __('Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .eael-woo-collection-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'eael_woo_collections_badge_background',
            [
                'label' => __('Background Color', 'essential-addons-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eael-woo-collection-badge' => 'background: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'eael_woo_collections_badge_color',
            [
                'label' => __('Text Color', 'essential-addons-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eael-woo-collection-badge' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        if (!apply_filters('eael/is_plugin_active', 'woocommerce/woocommerce.php')) {
            return;
        }

        $term = [];

        if ($settings['eael_woo_collections_type'] == 'category' && $settings['eael_woo_collections_category']) {
            $term = get_term($settings['eael_woo_collections_category']);
        } else if ($settings['eael_woo_collections_type'] == 'tags' && $settings['eael_woo_collections_tags']) {
            $term = get_term($settings['eael_woo_collections_tags']);
        } else if ($settings['eael_woo_collections_type'] == 'attributes' && $settings['eael_woo_collections_attributes']) {
            $term = get_term($settings['eael_woo_collections_attributes']);
        }

        $link = (!is_wp_error($term) && !empty($term)) ? get_term_link($term) : '#';
        $name = (!is_wp_error($term) && !empty($term)) ? $term->name : __('Collection Name', 'essential-addons-elementor');

        $this->add_render_attribute('eael-woo-collections-bg', [
            'class' => ['eael-woo-collections-bg', $settings['eael_woo_collections_bg_hover_effect']],
            'src' => $settings['eael_woo_collections_bg_img']['url'],
            'alt' => esc_attr(get_post_meta($settings['eael_woo_collections_bg_img']['id'], '_wp_attachment_image_alt', true)),
        ]);

	    $badge = '';

        if ( $settings['eael_woo_collections_is_show_badge'] == 'yes' && !empty($settings['eael_woo_collections_badge_label'])) {
        	$badge = '<div class="eael-woo-collection-badge">'.$settings['eael_woo_collections_badge_label'].'</div>';
        }

        echo '<div class="eael-woo-collections eael-woo-collections-layout-'.$settings['eael_woo_collections_layout'].'">
            <a href="' . $link . '">
				<img ' . $this->get_render_attribute_string('eael-woo-collections-bg') . '>
				<div class="eael-woo-collections-overlay ' . $settings['eael_woo_collections_overlay_content_hr'] . '">
					<div class="eael-woo-collections-overlay-inner ' . $settings['eael_woo_collections_overlay_content_vr'] . '">
						'.$badge.'
						<span>' . sprintf(esc_html__('%s', 'essential-addons-elementor'), ($settings['eael_woo_collections_subtitle'] ?: '')) . '</span>
						<h2>' . sprintf(esc_html__('%s', 'essential-addons-elementor'), $name) . '</h2>
					</div>
				</div>
			</a>
		</div>';
    }
}
