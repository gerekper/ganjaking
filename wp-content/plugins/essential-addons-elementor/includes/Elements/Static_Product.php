<?php

namespace Essential_Addons_Elementor\Pro\Elements;

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use \Elementor\Utils;
use \Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}
// If this file is called directly, abort.

class Static_Product extends Widget_Base
{

    public function get_name()
    {
        return 'eael-static-product';
    }

    public function get_title()
    {
        return esc_html__('Static Product', 'essential-addons-elementor');
    }

    public function get_icon()
    {
        return 'eaicon-static-product';
    }

    public function get_style_depends()
    {
        return [
            'font-awesome-5-all',
            'font-awesome-4-shim',
        ];
    }

    public function get_categories()
    {
        return ['essential-addons-elementor'];
    }

    public function get_keywords()
    {
        return [
            'product',
            'ea product',
            'ea static product',
            'static product',
            'product showcase',
            'product feed',
            'cta',
            'ea',
            'essential addons',
        ];
    }

    public function get_custom_help_url()
    {
        return 'https://essential-addons.com/elementor/docs/static-product/';
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'eael_section_layout_static_product',
            [
                'label' => __('Layout Settings', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'eael_static_product_layout',
            [
                'label'   => __('Choose Layout', 'essential-addons-elementor'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => __('Default Style', 'essential-addons-elementor'),
                    'two'     => __('Cart Button On Hover Image', 'essential-addons-elementor'),
                    'three'   => __('All Content On Hover Image', 'essential-addons-elementor'),
                ],
            ]
        );

        $this->add_control(
            'important_note',
            [
                'type'            => \Elementor\Controls_Manager::RAW_HTML,
                'raw'             => __('Goto <strong>product details</strong> panel and active add to cart button', 'essential-addons-elementor'),
                'content_classes' => 'eael-warning',
                'condition'       => [
                    'eael_static_product_layout'                  => 'two',
                    'eael_static_product_show_add_to_cart_button' => '',
                ],
            ]
        );

        $this->end_controls_section();

        // Content Controls
        $this->start_controls_section(
            'eael_section_static_product_content',
            [
                'label' => esc_html__('Product Details', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'eael_static_product_show_details_btn',
            [
                'label'     => esc_html__('Show Details Button?', 'essential-addons-elementor'),
                'type'      => Controls_Manager::SWITCHER,
                'label_on'  => __('yes', 'essential-addons-elementor'),
                'label_off' => __('no', 'essential-addons-elementor'),
                'default'   => 'yes',
            ]
        );
        $this->add_control(
            'eael_static_product_show_add_to_cart_button',
            [
                'label'     => esc_html__('Show Add To Cart Button?', 'essential-addons-elementor'),
                'type'      => Controls_Manager::SWITCHER,
                'label_on'  => __('yes', 'essential-addons-elementor'),
                'label_off' => __('no', 'essential-addons-elementor'),
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'eael_static_product_image',
            [
                'label'   => __('Product Image', 'essential-addons-elementor'),
                'type'    => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'ai' => [
                    'active' => false,
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_heading',
            [
                'label'       => __('Product Heading', 'essential-addons-elementor'),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'default'     => __('Product Name','essential-addons-elementor'),
                'placeholder' => __('Enter heading for the product', 'essential-addons-elementor'),
                'title'       => __('Enter heading for the product', 'essential-addons-elementor'),
                'dynamic'     => ['active' => true],
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'eael_static_product_description',
            [
                'label'   => __('Product Description', 'essential-addons-elementor'),
                'type'    => Controls_Manager::WYSIWYG,
                'default' => __('Click to inspect, then edit as needed.', 'essential-addons-elementor'),
            ]
        );
        $this->add_control(
            'eael_static_product_is_show_price',
            [
                'label'        => __('Show Price', 'essential-addons-elementor'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __('Show', 'essential-addons-elementor'),
                'label_off'    => __('Hide', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );
        $this->add_control(
            'eael_static_product_price',
            [
                'label'       => __('Price', 'essential-addons-elementor'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'dynamic'               => [
                    'active'       => true,
                    'categories'   => [
                        TagsModule::NUMBER_CATEGORY,
                    ],
                ],
                'default'     => __( '$77.5', 'essential-addons-elementor' ),
                'placeholder' => __( 'Type Your Price', 'essential-addons-elementor' ),
                'condition'   => [
                    'eael_static_product_is_show_price' => 'yes',
                ],
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'eael_static_product_is_show_rating',
            [
                'label'        => __('Show Rating', 'essential-addons-elementor'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __('Show', 'essential-addons-elementor'),
                'label_off'    => __('Hide', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );
        $this->add_control(
            'eael_static_product_review',
            [
                'label'       => __('Review', 'essential-addons-elementor'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'dynamic'               => [
                    'active'       => true
                ],
                'default'     => __( '(4.5 REVIEWS)', 'essential-addons-elementor' ),
                'placeholder' => __( 'Type Your Reviews', 'essential-addons-elementor' ),
                'condition'   => [
                    'eael_static_product_is_show_rating' => 'yes',
                ],
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'eael_static_product_title_buttons',
            [
                'label'     => __('Links & Buttons', 'essential-addons-elementor'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'eael_static_product_link_url',
            [
                'label'       => __('Product Link URL', 'essential-addons-elementor'),
                'type'        => Controls_Manager::TEXT,
                'dynamic'               => [
                    'active'       => true,
                    'categories'   => [
                        TagsModule::POST_META_CATEGORY,
                        TagsModule::URL_CATEGORY,
                    ],
                ],
                'label_block' => true,
                'default'     => '#',
                'placeholder' => __('Enter link URL for the promo', 'essential-addons-elementor'),
                'title'       => __('Enter URL for the product', 'essential-addons-elementor'),
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'eael_static_product_link_target',
            [
                'label'     => esc_html__('Open in new window?', 'essential-addons-elementor'),
                'type'      => Controls_Manager::SWITCHER,
                'label_on'  => __('_blank', 'essential-addons-elementor'),
                'label_off' => __('_self', 'essential-addons-elementor'),
                'default'   => '_self',
            ]
        );

        $this->add_control(
            'eael_static_product_demo_link_url',
            [
                'label'       => __('Live Demo URL', 'essential-addons-elementor'),
                'type'        => Controls_Manager::TEXT,
                'dynamic'               => [
                    'active'       => true,
                    'categories'   => [
                        TagsModule::POST_META_CATEGORY,
                        TagsModule::URL_CATEGORY,
                    ],
                ],
                'label_block' => true,
                'default'     => '#',
                'placeholder' => __('Enter link URL for live demo', 'essential-addons-elementor'),
                'title'       => __('Enter URL for the promo', 'essential-addons-elementor'),
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'eael_static_product_demo_is_used_icon',
            [
                'label'        => __('Show Live Demo Icon?', 'essential-addons-elementor'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __('Yes', 'essential-addons-elementor'),
                'label_off'    => __('No', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'eael_static_product_demo_icon',
            [
                'label'     => __('Live Demo Icon', 'essential-addons-elementor'),
                'type'      => \Elementor\Controls_Manager::ICONS,
                'default'   => [
                    'value'   => 'fas fa-eye',
                    'library' => 'solid',
                ],
                'condition' => [
                    'eael_static_product_demo_is_used_icon' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_demo_text',
            [
                'label'     => esc_html__('Live Demo Text', 'essential-addons-elementor'),
                'type'      => Controls_Manager::TEXT,
                'dynamic'   => ['active' => true],
                'default'   => esc_html__( 'Live Demo', 'essential-addons-elementor' ),
                'condition' => [
                    'eael_static_product_demo_is_used_icon' => '',
                ],
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'eael_static_product_demo_link_target',
            [
                'label'     => esc_html__('Open in new window?', 'essential-addons-elementor'),
                'type'      => Controls_Manager::SWITCHER,
                'label_on'  => __('_blank', 'essential-addons-elementor'),
                'label_off' => __('_self', 'essential-addons-elementor'),
                'default'   => '_blank',
            ]
        );

        $this->end_controls_section();

        // read more button
        $this->start_controls_section(
            'read_more_button_content_section',
            [
                'label'     => __('Read More Button', 'essential-addons-elementor'),
                'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'eael_static_product_show_details_btn' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_btn',
            [
                'label'     => esc_html__('Button Text', 'essential-addons-elementor'),
                'type'      => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default'   => esc_html__( 'View Details', 'essential-addons-elementor' ),
                'condition' => [
                    'eael_static_product_show_details_btn' => 'yes',
                ],
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'eael_static_product_btn_icon_new',
            [
                'label'            => esc_html__('Icon', 'essential-addons-elementor'),
                'type'             => Controls_Manager::ICONS,
                'fa4compatibility' => 'eael_static_product_btn_icon',
                'condition'        => [
                    'eael_static_product_show_details_btn' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // add to cart button
        $this->start_controls_section(
            'add_to_cart_button_content_section',
            [
                'label'     => __('Add To Cart Button', 'essential-addons-elementor'),
                'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'eael_static_product_show_add_to_cart_button' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_add_to_cart_btn_product_ID',
            [
                'label'       => esc_html__('Product ID', 'essential-addons-elementor'),
                'description' => esc_html__('add product id to generate add to cart url', 'essential-addons-elementor'),
                'type'        => Controls_Manager::TEXT,
                'dynamic' => [ 'active' => true ],
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'eael_static_product_add_to_cart_btn',
            [
                'label'   => esc_html__('Button Text', 'essential-addons-elementor'),
                'type'    => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__( 'Add To Cart', 'essential-addons-elementor' ),
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'eael_static_product_add_to_cart_button_icon_new',
            [
                'label' => esc_html__('Icon', 'essential-addons-elementor'),
                'type'  => Controls_Manager::ICONS,
            ]
        );

        $this->end_controls_section();

        // Style Controls
        $this->start_controls_section(
            'eael_section_eael_static_product_settings',
            [
                'label' => esc_html__('Product Style', 'essential-addons-elementor'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'eael_static_product_container_width',
            [
                'label'     => esc_html__('Set max width for the container?', 'essential-addons-elementor'),
                'type'      => Controls_Manager::SWITCHER,
                'label_on'  => __('yes', 'essential-addons-elementor'),
                'label_off' => __('no', 'essential-addons-elementor'),
                'default'   => 'no',
            ]
        );

        $this->add_responsive_control(
            'eael_static_product_container_width_value',
            [
                'label'      => __('Container Max Width (% or px)', 'essential-addons-elementor'),
                'type'       => Controls_Manager::SLIDER,
                'default'    => [
                    'size' => 480,
                    'unit' => 'px',
                ],
                'size_units' => ['px', '%'],
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 1000,
                        'step' => 5,
                    ],
                    '%'  => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .eael-static-product' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
                'condition'  => [
                    'eael_static_product_container_width' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_static_product_text_alignment',
            [
                'label'       => esc_html__('Content Alignment', 'essential-addons-elementor'),
                'separator'   => 'before',
                'type'        => Controls_Manager::CHOOSE,
                'label_block' => true,
                'options'     => [
                    'left'   => [
                        'title' => esc_html__('Left', 'essential-addons-elementor'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'essential-addons-elementor'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'  => [
                        'title' => esc_html__('Right', 'essential-addons-elementor'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'default'     => 'center',
                'selectors'   => [
                    '{{WRAPPER}} .eael-static-product-details'  => 'text-align: {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'eael_static_product_button_alignment',
            [
                'label'       => esc_html__('Button Alignment', 'essential-addons-elementor'),
                'separator'   => 'before',
                'type'        => Controls_Manager::CHOOSE,
                'label_block' => true,
                'options'     => [
                    'flex-start'   => [
                        'title' => esc_html__('Left', 'essential-addons-elementor'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'essential-addons-elementor'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'flex-end'  => [
                        'title' => esc_html__('Right', 'essential-addons-elementor'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'selectors'   => [
                    '{{WRAPPER}} .eael-static-product-btn-wrap' => 'justify-content: {{VALUE}}',
                ],
                'default'     => 'center',
                'condition' => [
                    'eael_static_product_layout'    => 'three'
                ]
            ]
        );

        $this->add_control(
            'eael_static_product_content_padding',
            [
                'label'      => esc_html__('Content Padding', 'essential-addons-elementor'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'selectors'  => [
                    '{{WRAPPER}} .eael-static-product-details' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'eael_static_product_border',
                'selector' => '{{WRAPPER}} .eael-static-product',
            ]
        );

        $this->add_control(
            'eael_static_product_border_radius',
            [
                'label'     => esc_html__('Border Radius', 'essential-addons-elementor'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-static-product' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'      => 'eael_static_product_box_shadow',
                'selector'  => '{{WRAPPER}} .eael-static-product',
                'separator' => '',
            ]
        );
        // price and rating style
        $this->add_control(
            'eael_static_product_price_and_rating_style_title',
            [
                'label'     => __('Price & Rating Style', 'essential-addons-elementor'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'eael_static_product_price_and_rating_box',
            [
                'label'      => __('Price & Rating Box Margin', 'essential-addons-elementor'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .eael-static-product .eael-static-product-details .eael-static-product-price-and-reviews' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'eael_static_product_space_between_price_and_rating',
            [
                'label'      => __('Space Between Price And Rating', 'essential-addons-elementor'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 1000,
                        'step' => 5,
                    ],
                    '%'  => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => 5,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .eael-static-product .eael-static-product-details .eael-static-product-price-and-reviews .eael-static-product-reviews' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'eael_static_product_price_typography',
                'label'    => __('Price Typography', 'essential-addons-elementor'),
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
                'selector' => '{{WRAPPER}} .eael-static-product .eael-static-product-details .eael-static-product-price-and-reviews .eael-static-product-price',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'eael_static_product_reviews_typography',
                'label'    => __('Reviews Typography', 'essential-addons-elementor'),
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
                'selector' => '{{WRAPPER}} .eael-static-product .eael-static-product-details .eael-static-product-price-and-reviews .eael-static-product-reviews',
            ]
        );

        $this->add_control(
            'eael_static_product_price_color',
            [
                'label'     => __('Price Color', 'essential-addons-elementor'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eael-static-product .eael-static-product-details .eael-static-product-price-and-reviews .eael-static-product-price' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_reviews_color',
            [
                'label'     => __('Reviews Color', 'essential-addons-elementor'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eael-static-product .eael-static-product-details .eael-static-product-price-and-reviews .eael-static-product-reviews' => 'color: {{VALUE}}',
                ],
            ]
        );

        // static product box hover style
        $this->add_control(
            'eael_static_product_hover_style_title',
            [
                'label'     => __('Hover Style', 'essential-addons-elementor'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'eael_static_product_hover_border',
                'selector' => '{{WRAPPER}} .eael-static-product:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'      => 'eael_static_product_hover_box_shadow',
                'selector'  => '{{WRAPPER}} .eael-static-product:hover',
                'separator' => '',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'eael_section_eael_static_product_styles',
            [
                'label' => esc_html__('Colors &amp; Typography', 'essential-addons-elementor'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'eael_static_product_thumbnail_overlay_heading',
            [
                'label' => __('Product Thumbnail Overlay', 'essential-addons-elementor'),
                'type'  => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'eael_static_product_is_gradient_background',
            [
                'label'        => __('Gradient Background?', 'essential-addons-elementor'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __('Yes', 'essential-addons-elementor'),
                'label_off'    => __('No', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name'        => 'eael_static_product_overlay_color',
                'label'       => __('background', 'essential-addons-elementor'),
                'description' => __('Product Thumbnail Overlay Color', 'essential-addons-elementor'),
                'types'       => ['classic', 'gradient'],
                'selector'    => '{{WRAPPER}} .eael-static-product-thumb-overlay',
                'condition'   => [
                    'eael_static_product_is_gradient_background' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_overlay_content_position',
            [
                'label'      => __('Overlay Position', 'essential-addons-elementor'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .eael-static-product.eael-static-product--style-three .eael-static-product-thumb-overlay' => 'top: {{TOP}}{{UNIT}}; right: {{RIGHT}}{{UNIT}}; bottom: {{BOTTOM}}{{UNIT}}; left: {{LEFT}}{{UNIT}};',
                ],
                'default'    => [
                    'top'      => "25",
                    'right'    => "25",
                    'bottom'   => "25",
                    'left'     => "25",
                    'isLinked' => true,
                ],
                'condition'  => [
                    'eael_static_product_layout' => 'three',
                ],
            ]
        );
        $this->add_control(
            'eael_static_product_overlay_content_radius',
            [
                'label'      => __('Border Radius', 'essential-addons-elementor'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .eael-static-product.eael-static-product--style-three .eael-static-product-thumb-overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition'  => [
                    'eael_static_product_layout' => 'three',
                ],
            ]
        );
        $this->add_control(
            'eael_static_product_overlay_color',
            [
                'label'     => esc_html__('Background', 'essential-addons-elementor'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eael-static-product-thumb-overlay' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'eael_static_product_is_gradient_background' => '',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_overlay_link_icon_or_text_heading',
            [
                'label' => __('Product Thumbnail Overlay link Text/Icon', 'essential-addons-elementor'),
                'type'  => Controls_Manager::HEADING,
            ]
        );

        // tabs
        $this->start_controls_tabs(
            'eael_static_product_overlay_link_icon_or_text_tabs_style'
        );

        $this->start_controls_tab(
            'eael_static_product_overlay_link_icon_or_text_normal_tab',
            [
                'label' => __('Normal', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'eael_static_product_live_link_color',
            [
                'label'     => esc_html__('Color', 'essential-addons-elementor'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eael-static-product-media a.eael-static-product-live-demo-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'eael_static_product_live_link_typography',
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
                'selector' => '{{WRAPPER}} .eael-static-product-media a.eael-static-product-live-demo-btn',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name'     => 'eael_static_product_live_link_background',
                'label'    => __('Background', 'essential-addons-elementor'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .eael-static-product-media a.eael-static-product-live-demo-btn > span',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'eael_static_product_live_link_shadow',
                'label'    => __('Shadow', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-static-product-media a.eael-static-product-live-demo-btn > span',
            ]
        );

        $this->end_controls_tab();
        $this->start_controls_tab(
            'eael_static_product_overlay_link_icon_or_text_hover_tab',
            [
                'label' => __('Hover', 'essential-addons-elementor'),
            ]
        );
        $this->add_control(
            'eael_static_product_live_link_hover_color',
            [
                'label'     => esc_html__('Color', 'essential-addons-elementor'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eael-static-product-media a.eael-static-product-live-demo-btn:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name'     => 'eael_static_product_live_link_hover_background',
                'label'    => __('Background', 'essential-addons-elementor'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .eael-static-product-media a.eael-static-product-live-demo-btn:hover > span',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'eael_static_product_live_link_hover_shadow',
                'label'    => __('Shadow', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-static-product-media a.eael-static-product-live-demo-btn:hover > span',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'eael_static_product_live_link_padding',
            [
                'label'      => __('Padding', 'essential-addons-elementor'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .eael-static-product-media a.eael-static-product-live-demo-btn > span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'eael_static_product_live_link_margin',
            [
                'label'      => __('Margin', 'essential-addons-elementor'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .eael-static-product-media a.eael-static-product-live-demo-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_live_link_radius',
            [
                'label'      => __('Border Radius', 'essential-addons-elementor'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .eael-static-product-media a.eael-static-product-live-demo-btn > span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_title_heading',
            [
                'label' => __('Product Title', 'essential-addons-elementor'),
                'type'  => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'eael_static_product_title_color',
            [
                'label'     => esc_html__('Color', 'essential-addons-elementor'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#303133',
                'selectors' => [
                    '{{WRAPPER}} .eael-static-product-details > h2 > a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'eael_static_product_title_typography',
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
                'selector' => '{{WRAPPER}} .eael-static-product-details > h2 > a',
            ]
        );

        $this->add_control(
            'eael_static_product_content_heading',
            [
                'label' => __('Product Content', 'essential-addons-elementor'),
                'type'  => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'eael_static_product_content_color',
            [
                'label'     => esc_html__('Content', 'essential-addons-elementor'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#7a7a7a',
                'selectors' => [
                    '{{WRAPPER}} .eael-static-product-details > p' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_is_content_gradient_background',
            [
                'label'        => __('Gradient Background?', 'essential-addons-elementor'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __('Yes', 'essential-addons-elementor'),
                'label_off'    => __('No', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name'      => 'eael_static_product_content_background',
                'label'     => __('Background', 'essential-addons-elementor'),
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .eael-static-product-details',
                'condition' => [
                    'eael_static_product_is_content_gradient_background' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_content_background',
            [
                'label'     => esc_html__('Background Color', 'essential-addons-elementor'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .eael-static-product-details' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'eael_static_product_is_content_gradient_background' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'eael_static_product_content_typography',
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
                'selector' => '{{WRAPPER}} .eael-static-product-details > p',
            ]
        );

        $this->end_controls_section();

        // read more button
        $this->start_controls_section(
            'read_more_button_style_section',
            [
                'label'     => __('Read More Button', 'essential-addons-elementor'),
                'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'eael_static_product_show_details_btn' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_btn_icon_align',
            [
                'label'     => esc_html__('Icon Position', 'essential-addons-elementor'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'left',
                'options'   => [
                    'left'  => esc_html__('Before', 'essential-addons-elementor'),
                    'right' => esc_html__('After', 'essential-addons-elementor'),
                ],
                'condition' => [
                    'eael_static_product_show_details_btn' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_btn_icon_indent',
            [
                'label'     => esc_html__('Icon Spacing', 'essential-addons-elementor'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'condition' => [
                    'eael_static_product_show_details_btn' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-static-product-button-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eael-static-product-button-icon-left'  => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_btn_icon_size',
            [
                'label'     => esc_html__('Icon Size', 'essential-addons-elementor'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 26,
                    'unit' => 'px',
                ],
                'range'     => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'condition' => [
                    'eael_static_product_show_details_btn' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-static-product-button-icon-right, {{WRAPPER}} .eael-static-product-button-icon-left'                                                                         => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eael-static-product-button-icon-right.eael-static-product-button-svg-icon, {{WRAPPER}} .eael-static-product-button-icon-left.eael-static-product-button-svg-icon' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_btn_padding',
            [
                'label'      => esc_html__('Padding', 'essential-addons-elementor'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'selectors'  => [
                    '{{WRAPPER}} .eael-static-product-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition'  => [
                    'eael_static_product_show_details_btn' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_btn_margin',
            [
                'label'      => esc_html__('Margin', 'essential-addons-elementor'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'selectors'  => [
                    '{{WRAPPER}} .eael-static-product-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ],
                'condition'  => [
                    'eael_static_product_show_details_btn' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_btn_border_radius',
            [
                'label'     => esc_html__('Button Border Radius', 'essential-addons-elementor'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-static-product-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'eael_static_product_show_details_btn' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'eael_static_product_btn_typography',
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
                'selector'  => '{{WRAPPER}} .eael-static-product-btn .eael-static-product-btn-inner',
                'condition' => [
                    'eael_static_product_show_details_btn' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_readmore_btn_is_used_gradient_bg',
            [
                'label'        => __('Button Gradient Background', 'essential-addons-elementor'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __('Show', 'essential-addons-elementor'),
                'label_off'    => __('Hide', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );

        $this->start_controls_tabs('eael_static_product_btn_content_tabs');

        $this->start_controls_tab(
            'normal_default_content',
            [
                'label'     => esc_html__('Normal', 'essential-addons-elementor'),
                'condition' => [
                    'eael_static_product_show_details_btn' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_btn_text_color',
            [
                'label'     => esc_html__('Text Color', 'essential-addons-elementor'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .eael-static-product-btn' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'eael_static_product_show_details_btn' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name'      => 'eael_static_product_btn_background_color',
                'label'     => __('Background', 'essential-addons-elementor'),
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .eael-static-product-btn',
                'condition' => [
                    'eael_static_product_readmore_btn_is_used_gradient_bg' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_btn_background_color',
            [
                'label'     => esc_html__('Background Color', 'essential-addons-elementor'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#646464',
                'selectors' => [
                    '{{WRAPPER}} .eael-static-product-btn' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'eael_static_product_show_details_btn'                 => 'yes',
                    'eael_static_product_readmore_btn_is_used_gradient_bg' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'      => 'eael_static_product_btn_border',
                'selector'  => '{{WRAPPER}} .eael-static-product-btn',
                'condition' => [
                    'eael_static_product_show_details_btn' => 'yes',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'eael_static_product_btn_hover',
            [
                'label'     => esc_html__('Hover', 'essential-addons-elementor'),
                'condition' => [
                    'eael_static_product_show_details_btn' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_btn_hover_text_color',
            [
                'label'     => esc_html__('Text Color', 'essential-addons-elementor'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .eael-static-product-btn:hover' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'eael_static_product_show_details_btn' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name'      => 'eael_static_product_btn_hover_background_color',
                'label'     => __('Background', 'essential-addons-elementor'),
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .eael-static-product-btn:hover',
                'condition' => [
                    'eael_static_product_readmore_btn_is_used_gradient_bg' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_btn_hover_background_color',
            [
                'label'     => esc_html__('Background Color', 'essential-addons-elementor'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#272727',
                'selectors' => [
                    '{{WRAPPER}} .eael-static-product-btn:hover' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'eael_static_product_show_details_btn'                 => 'yes',
                    'eael_static_product_readmore_btn_is_used_gradient_bg' => '',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_btn_hover_border_color',
            [
                'label'     => esc_html__('Border Color', 'essential-addons-elementor'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eael-static-product-btn:hover' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'eael_static_product_show_details_btn' => 'yes',
                ],
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();

        // add to cart
        $this->start_controls_section(
            'add_to_cart_button_style_section',
            [
                'label'     => __('Add To Cart Button', 'essential-addons-elementor'),
                'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'eael_static_product_show_add_to_cart_button' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_add_to_cart_btn_icon_align',
            [
                'label'   => esc_html__('Icon Position', 'essential-addons-elementor'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'left',
                'options' => [
                    'left'  => esc_html__('Before', 'essential-addons-elementor'),
                    'right' => esc_html__('After', 'essential-addons-elementor'),
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_add_to_cart_btn_icon_indent',
            [
                'label'     => esc_html__('Icon Spacing', 'essential-addons-elementor'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-static-product-add-to-cart .eael-static-product-button-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eael-static-product-add-to-cart .eael-static-product-button-icon-left'  => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_add_to_cart_btn_icon_size',
            [
                'label'     => esc_html__('Icon Size', 'essential-addons-elementor'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 26,
                    'unit' => 'px',
                ],
                'range'     => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-static-product-add-to-cart .eael-static-product-button-icon-right, {{WRAPPER}} .eael-static-product-add-to-cart .eael-static-product-button-icon-left'                                                                         => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eael-static-product-add-to-cart .eael-static-product-button-icon-right.eael-static-product-button-svg-icon, {{WRAPPER}} .eael-static-product-add-to-cart .eael-static-product-button-icon-left.eael-static-product-button-svg-icon' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_add_to_cart_btn_padding',
            [
                'label'      => esc_html__('Padding', 'essential-addons-elementor'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'selectors'  => [
                    '{{WRAPPER}} .eael-static-product-add-to-cart ' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_add_to_cart_btn_margin',
            [
                'label'      => esc_html__('Margin', 'essential-addons-elementor'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'selectors'  => [
                    '{{WRAPPER}} .eael-static-product-add-to-cart ' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_add_to_cart_btn_border_radius',
            [
                'label'     => esc_html__('Button Border Radius', 'essential-addons-elementor'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-static-product-add-to-cart' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'eael_static_product_add_to_cart_btn_typography',
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
                'selector' => '{{WRAPPER}} .eael-static-product-add-to-cart .eael-static-product-btn-inner',
            ]
        );

        $this->add_control(
            'eael_static_product_addtocart_btn_is_used_gradient_bg',
            [
                'label'        => __('Button Gradient Background', 'essential-addons-elementor'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __('Show', 'essential-addons-elementor'),
                'label_off'    => __('Hide', 'essential-addons-elementor'),
                'return_value' => 'yes',
            ]
        );

        $this->start_controls_tabs('eael_static_product_add_to_cart_btn_content_tabs');

        $this->start_controls_tab('add_to_cart_button_nomal_tab_content', ['label' => esc_html__('Normal', 'essential-addons-elementor')]);

        $this->add_control(
            'eael_static_product_add_to_cart_btn_text_color',
            [
                'label'     => esc_html__('Text Color', 'essential-addons-elementor'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .eael-static-product-add-to-cart' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name'      => 'eael_static_product_add_to_cart_btn_background_color',
                'label'     => __('Background', 'essential-addons-elementor'),
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .eael-static-product-add-to-cart',
                'condition' => [
                    'eael_static_product_addtocart_btn_is_used_gradient_bg' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_add_to_cart_btn_background_color',
            [
                'label'     => esc_html__('Background Color', 'essential-addons-elementor'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#646464',
                'selectors' => [
                    '{{WRAPPER}} .eael-static-product-add-to-cart' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'eael_static_product_addtocart_btn_is_used_gradient_bg' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'eael_static_product_add_to_cart_btn_border',
                'selector' => '{{WRAPPER}} .eael-static-product-add-to-cart',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'add_to_cart_button_hover_tab_content',
            [
                'label' => esc_html__('Hover', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'eael_static_product_add_to_cart_btn_hover_text_color',
            [
                'label'     => esc_html__('Text Color', 'essential-addons-elementor'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .eael-static-product-add-to-cart:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name'      => 'eael_static_product_add_to_cart_btn_hover_background_color',
                'label'     => __('Background', 'essential-addons-elementor'),
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .eael-static-product-add-to-cart:hover',
                'condition' => [
                    'eael_static_product_addtocart_btn_is_used_gradient_bg' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_add_to_cart_btn_hover_background_color',
            [
                'label'     => esc_html__('Background Color', 'essential-addons-elementor'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#272727',
                'selectors' => [
                    '{{WRAPPER}} .eael-static-product-add-to-cart:hover' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'eael_static_product_addtocart_btn_is_used_gradient_bg' => '',
                ],
            ]
        );

        $this->add_control(
            'eael_static_product_add_to_cart_btn_hover_border_color',
            [
                'label'     => esc_html__('Border Color', 'essential-addons-elementor'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eael-static-product-add-to-cart:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function render()
    {

        $settings = $this->get_settings_for_display();
        $static_product_image = $this->get_settings('eael_static_product_image');
        $icon_migrated = isset($settings['__fa4_migrated']['eael_static_product_btn_icon_new']);
        $icon_is_new = empty($settings['eael_static_product_btn_icon']);
        $eael_static_product_layout = (!empty($settings['eael_static_product_layout']) ? $settings['eael_static_product_layout'] : '');
        $add_to_cart_icon = empty($settings['eael_static_product_add_to_cart_button_icon_new']);
        $product_ID = (!empty($settings['eael_static_product_add_to_cart_btn_product_ID']) ? $settings['eael_static_product_add_to_cart_btn_product_ID'] : '');

	    // WC Notices
	    if ( class_exists( 'woocommerce' ) ) {
		    woocommerce_output_all_notices();
	    }

        // template markup
        $cartButtonMarkup = '';
        if ($settings['eael_static_product_show_add_to_cart_button'] === 'yes' && !empty($settings['eael_static_product_add_to_cart_btn'])) :
            $cartButtonMarkup .= '<a class="eael-static-product-add-to-cart" href="' . (($product_ID !== "") ? esc_attr(do_shortcode('[add_to_cart_url id="' . $product_ID . '"]')) : '') . '">
																																                <span class="eael-static-product-btn-inner">';

            if ($settings['eael_static_product_add_to_cart_btn_icon_align'] == 'left') :
                if (!$add_to_cart_icon) {
                    if (isset($settings['eael_static_product_add_to_cart_button_icon_new']['value']['url'])) :
                        $cartButtonMarkup .= '<img class="eael-static-product-button-icon-left eael-static-product-button-svg-icon" src="' . esc_url($settings['eael_static_product_add_to_cart_button_icon_new']['value']['url']) . '" alt="' . esc_attr(get_post_meta($settings['eael_static_product_add_to_cart_button_icon_new']['value']['id'], '_wp_attachment_image_alt', true)) . '">';
                    else :
                        $cartButtonMarkup .= '<i class="' . esc_attr($settings['eael_static_product_add_to_cart_button_icon_new']['value']) . ' eael-static-product-button-icon-left" aria-hidden="true"></i>';
                    endif;
                } else {
                    $cartButtonMarkup .= '<i class="' . esc_attr($settings['eael_static_product_add_to_cart_button_icon_new']) . ' eael-static-product-button-icon-left" aria-hidden="true"></i>';
                }
            endif;

            $cartButtonMarkup .= esc_attr($settings['eael_static_product_add_to_cart_btn']);

            if ($settings['eael_static_product_add_to_cart_btn_icon_align'] == 'right') :
                if (!$add_to_cart_icon) {
                    if (isset($settings['eael_static_product_add_to_cart_button_icon_new']['value']['url'])) :
                        $cartButtonMarkup .= '<img class="eael-static-product-button-icon-right eael-static-product-button-svg-icon" src="' . esc_url($settings['eael_static_product_add_to_cart_button_icon_new']['value']['url']) . '" alt="' . esc_attr(get_post_meta($settings['eael_static_product_add_to_cart_button_icon_new']['value']['id'], '_wp_attachment_image_alt', true)) . '">';
                    else :
                        $cartButtonMarkup .= '<i class="' . esc_attr($settings['eael_static_product_add_to_cart_button_icon_new']['value']) . ' eael-static-product-button-icon-right" aria-hidden="true"></i>';
                    endif;
                } else {
                    $cartButtonMarkup .= '<i class="' . esc_attr($settings['eael_static_product_add_to_cart_button_icon_new']) . ' eael-static-product-button-icon-right" aria-hidden="true"></i>';
                }
            endif;
            $cartButtonMarkup .= '</span>
																																            </a>';
        endif;
        if ($eael_static_product_layout === 'three') :
?>
            <div id="eael-static-product-<?php echo esc_attr($this->get_id()); ?>" class="eael-static-product eael-static-product--style-three">
                <div class="eael-static-product-media">
                    <div class="eael-static-product-thumb-overlay">
                        <div class="eael-static-product-details">
                            <?php if (!empty($settings['eael_static_product_heading'])) : ?>
                                <h2><a href="<?php echo esc_attr($settings['eael_static_product_link_url']); ?>" target="<?php echo esc_attr($settings['eael_static_product_link_target']); ?>"><?php echo esc_attr($settings['eael_static_product_heading']); ?></a></h2>
                            <?php endif; ?>
                            <?php if ($settings['eael_static_product_is_show_price'] === 'yes' || $settings['eael_static_product_is_show_rating'] === 'yes') : ?>
                                <div class="eael-static-product-price-and-reviews">
                                    <?php if (isset($settings['eael_static_product_is_show_price']) && $settings['eael_static_product_is_show_price'] === 'yes') : ?>
                                        <span class="eael-static-product-price"><?php echo (!empty($settings['eael_static_product_price']) ? $settings['eael_static_product_price'] : ''); ?></span>
                                    <?php endif; ?>
                                    <?php if (isset($settings['eael_static_product_is_show_rating']) && $settings['eael_static_product_is_show_rating'] === 'yes') : ?>
                                        <span class="eael-static-product-reviews"><?php echo (!empty($settings['eael_static_product_review']) ? $settings['eael_static_product_review'] : ''); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <p><?php echo $settings['eael_static_product_description']; ?></p>
                            <?php if ($settings['eael_static_product_show_details_btn'] === 'yes' || $settings['eael_static_product_show_add_to_cart_button'] === 'yes') : ?>
                                <div class="eael-static-product-btn-wrap">
                                    <?php if ($settings['eael_static_product_show_details_btn'] === 'yes' && !empty($settings['eael_static_product_btn'])) : ?>
                                        <a href="<?php echo esc_attr($settings['eael_static_product_link_url']); ?>" target="<?php echo esc_attr($settings['eael_static_product_link_target']); ?>" class="eael-static-product-btn">
                                            <span class="eael-static-product-btn-inner">
                                                <?php if ($settings['eael_static_product_btn_icon_align'] == 'left') : ?>
                                                    <?php if ($icon_migrated || $icon_is_new) { ?>
                                                        <?php if (isset($settings['eael_static_product_btn_icon_new']['value']['url'])) : ?>
                                                            <img class="eael-static-product-button-icon-left eael-static-product-button-svg-icon" src="<?php echo esc_url($settings['eael_static_product_btn_icon_new']['value']['url']); ?>" alt="<?php echo esc_attr(get_post_meta($settings['eael_static_product_btn_icon_new']['value']['id'], '_wp_attachment_image_alt', true)); ?>">
                                                        <?php else : ?>
                                                            <i class="<?php echo esc_attr($settings['eael_static_product_btn_icon_new']['value']); ?> eael-static-product-button-icon-left" aria-hidden="true"></i>
                                                        <?php endif; ?>
                                                    <?php } else { ?>
                                                        <i class="<?php echo esc_attr($settings['eael_static_product_btn_icon']); ?> eael-static-product-button-icon-left" aria-hidden="true"></i>
                                                    <?php } ?>
                                                <?php endif; ?>

                                                <?php echo esc_attr($settings['eael_static_product_btn']); ?>

                                                <?php if ($settings['eael_static_product_btn_icon_align'] == 'right') : ?>
                                                    <?php if ($icon_migrated || $icon_is_new) { ?>
                                                        <?php if (isset($settings['eael_static_product_btn_icon_new']['value']['url'])) : ?>
                                                            <img class="eael-static-product-button-icon-right eael-static-product-button-svg-icon" src="<?php echo esc_url($settings['eael_static_product_btn_icon_new']['value']['url']); ?>" alt="<?php echo esc_attr(get_post_meta($settings['eael_static_product_btn_icon_new']['value']['id'], '_wp_attachment_image_alt', true)); ?>">
                                                        <?php else : ?>
                                                            <i class="<?php echo esc_attr($settings['eael_static_product_btn_icon_new']['value']); ?> eael-static-product-button-icon-right" aria-hidden="true"></i>
                                                        <?php endif; ?>
                                                    <?php } else { ?>
                                                        <i class="<?php echo esc_attr($settings['eael_static_product_btn_icon']); ?> eael-static-product-button-icon-right" aria-hidden="true"></i>
                                                    <?php } ?>
                                                <?php endif; ?>
                                            </span>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($settings['eael_static_product_demo_text'])) : ?>
                                        <a class="eael-static-product-live-demo-btn" href="<?php echo esc_attr($settings['eael_static_product_demo_link_url']); ?>" target="<?php echo esc_attr($settings['eael_static_product_demo_link_target']); ?>">
                                            <?php if ($settings['eael_static_product_demo_is_used_icon'] == 'yes') : ?>
                                                <span class="<?php echo esc_attr($settings['eael_static_product_demo_icon']['value']); ?>"></span>
                                            <?php else : ?>
                                                <span><?php echo esc_attr($settings['eael_static_product_demo_text']); ?></span>
                                            <?php endif; ?>
                                        </a>
                                    <?php endif; ?>

                                    <?php print $cartButtonMarkup; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="eael-static-product-thumb">
                        <?php echo '<img src="' . $static_product_image['url'] . '" alt="' . esc_attr(get_post_meta($static_product_image['id'], '_wp_attachment_image_alt', true)) . '">'; ?>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <div id="eael-static-product-<?php echo esc_attr($this->get_id()); ?>" class="eael-static-product">
                <div class="eael-static-product-media">
                    <div class="eael-static-product-thumb-overlay">
                        <a class="eael-static-product-live-demo-btn" href="<?php echo esc_attr($settings['eael_static_product_demo_link_url']); ?>" target="<?php echo esc_attr($settings['eael_static_product_demo_link_target']); ?>">
                            <?php if ($settings['eael_static_product_demo_is_used_icon'] == 'yes') : ?>
                                <span class="<?php echo esc_attr($settings['eael_static_product_demo_icon']['value']); ?>"></span>
                            <?php else : ?>
                                <span><?php echo esc_attr($settings['eael_static_product_demo_text']); ?></span>
                            <?php endif; ?>
                        </a>
                        <?php
                        if ($eael_static_product_layout === 'two') {
                            print $cartButtonMarkup;
                        }
                        ?>
                    </div>
                    <div class="eael-static-product-thumb">
                        <?php echo '<img src="' . $static_product_image['url'] . '" alt="' . esc_attr(get_post_meta($static_product_image['id'], '_wp_attachment_image_alt', true)) . '">'; ?>
                    </div>
                </div>
                <div class="eael-static-product-details">
                    <?php if (!empty($settings['eael_static_product_heading'])) : ?>
                        <h2><a href="<?php echo esc_attr($settings['eael_static_product_link_url']); ?>" target="<?php echo esc_attr($settings['eael_static_product_link_target']); ?>"><?php echo esc_attr($settings['eael_static_product_heading']); ?></a></h2>
                    <?php endif; ?>
                    <p><?php echo $settings['eael_static_product_description']; ?></p>
                    <?php if ($settings['eael_static_product_is_show_price'] === 'yes' || $settings['eael_static_product_is_show_rating'] === 'yes') : ?>
                        <div class="eael-static-product-price-and-reviews">
                            <?php if (isset($settings['eael_static_product_is_show_price']) && $settings['eael_static_product_is_show_price'] === 'yes') : ?>
                                <span class="eael-static-product-price"><?php echo (!empty($settings['eael_static_product_price']) ? $settings['eael_static_product_price'] : ''); ?></span>
                            <?php endif; ?>
                            <?php if (isset($settings['eael_static_product_is_show_rating']) && $settings['eael_static_product_is_show_rating'] === 'yes') : ?>
                                <span class="eael-static-product-reviews"><?php echo (!empty($settings['eael_static_product_review']) ? $settings['eael_static_product_review'] : ''); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($settings['eael_static_product_show_details_btn'] === 'yes' || $settings['eael_static_product_show_add_to_cart_button'] === 'yes') : ?>
                        <div class="eael-static-product-btn-wrap">
                            <?php if ($settings['eael_static_product_show_details_btn'] === 'yes' && !empty($settings['eael_static_product_btn'])) : ?>
                                <a href="<?php echo esc_attr($settings['eael_static_product_link_url']); ?>" target="<?php echo esc_attr($settings['eael_static_product_link_target']); ?>" class="eael-static-product-btn">
                                    <span class="eael-static-product-btn-inner">
                                        <?php if ($settings['eael_static_product_btn_icon_align'] == 'left') : ?>
                                            <?php if ($icon_migrated || $icon_is_new) { ?>
                                                <?php if (isset($settings['eael_static_product_btn_icon_new']['value']['url'])) : ?>
                                                    <img class="eael-static-product-button-icon-left eael-static-product-button-svg-icon" src="<?php echo esc_url($settings['eael_static_product_btn_icon_new']['value']['url']); ?>" alt="<?php echo esc_attr(get_post_meta($settings['eael_static_product_btn_icon_new']['value']['id'], '_wp_attachment_image_alt', true)); ?>">
                                                <?php else : ?>
                                                    <i class="<?php echo esc_attr($settings['eael_static_product_btn_icon_new']['value']); ?> eael-static-product-button-icon-left" aria-hidden="true"></i>
                                                <?php endif; ?>
                                            <?php } else { ?>
                                                <i class="<?php echo esc_attr($settings['eael_static_product_btn_icon']); ?> eael-static-product-button-icon-left" aria-hidden="true"></i>
                                            <?php } ?>
                                        <?php endif; ?>

                                        <?php echo esc_attr($settings['eael_static_product_btn']); ?>

                                        <?php if ($settings['eael_static_product_btn_icon_align'] == 'right') : ?>
                                            <?php if ($icon_migrated || $icon_is_new) { ?>
                                                <?php if (isset($settings['eael_static_product_btn_icon_new']['value']['url'])) : ?>
                                                    <img class="eael-static-product-button-icon-right eael-static-product-button-svg-icon" src="<?php echo esc_url($settings['eael_static_product_btn_icon_new']['value']['url']); ?>" alt="<?php echo esc_attr(get_post_meta($settings['eael_static_product_btn_icon_new']['value']['id'], '_wp_attachment_image_alt', true)); ?>">
                                                <?php else : ?>
                                                    <i class="<?php echo esc_attr($settings['eael_static_product_btn_icon_new']['value']); ?> eael-static-product-button-icon-right" aria-hidden="true"></i>
                                                <?php endif; ?>
                                            <?php } else { ?>
                                                <i class="<?php echo esc_attr($settings['eael_static_product_btn_icon']); ?> eael-static-product-button-icon-right" aria-hidden="true"></i>
                                            <?php } ?>
                                        <?php endif; ?>
                                    </span>
                                </a>
                            <?php endif; ?>
                            <?php
                            if ($eael_static_product_layout === 'default') {
                                print $cartButtonMarkup;
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
<?php
        endif;
    }

    protected function content_template()
    {
    }
}
