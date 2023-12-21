<?php

/**
 * Product Carousel widget class
 *
 * @package Happy_Addons_Pro
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Happy_Addons_Pro\Traits\Lazy_Query_Builder;
use WP_Query;

defined('ABSPATH') || die();

class Product_Carousel_New extends Base {

    use Lazy_Query_Builder;

    protected static $_query = null;

    public function get_title() {
        return __('Product Carousel', 'happy-addons-pro');
    }

    public function get_icon() {
        return 'hm hm-Product-Carousel';
    }

    public function get_keywords() {
        return ['ecommerce', 'woocommerce', 'product', 'carousel', 'sale', 'ha-skin'];
    }

    /**
     * Overriding default function to add custom html class.
     *
     * @return string
     */
    public function get_html_wrapper_class() {
        $html_class = parent::get_html_wrapper_class();
        $html_class .= ' ' . str_replace('-new', '', $this->get_name());
        return $html_class;
    }

    public function get_query() {
        $args = $this->get_query_args();
        $args['posts_per_page'] = $this->get_settings_for_display('posts_per_page');

        if (is_null(self::$_query)) {
            self::$_query = new WP_Query();
        }

        self::$_query->query($args);

        return self::$_query;
    }

    protected function register_content_controls() {
        $this->start_controls_section(
            '_section_post_layout',
            [
                'label' => __('Layout', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );


        $this->add_control(
            'skin',
            [
                'label' => __('Skin', 'happy-addons-pro'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'classic' => __('Classic', 'happy-addons-pro'),
                    'modern' => __('Modern', 'happy-addons-pro'),
                    'remote_carousel' => __('Remote Carousel', 'happy-addons-pro'),
                ],
                'default' => 'classic',
                'render_type' => 'template',
            ]
        );

        $this->add_control(
			'product_carousel_rcc_unique_id',
			[
				'label' => __( 'Unique ID', 'happy-addons-pro' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => __( 'Enter remote carousel unique id', 'happy-addons-pro' ),
                'description' => __('Input carousel ID that you want to remotely connect', 'happy-addons-pro'),
                'condition' => [ 'skin' => 'remote_carousel' ]
			]
		);

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'post_image',
                'default' => 'large',
                'exclude' => [
                    'custom'
                ]
            ]
        );

        $this->add_control(
            'product_on_sale_show',
            [
                'label' => __('Show On Sale Badge', 'happy-addons-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'happy-addons-pro'),
                'label_off' => __('Hide', 'happy-addons-pro'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'product_ratings_show',
            [
                'label' => __('Show Ratings', 'happy-addons-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'happy-addons-pro'),
                'label_off' => __('Hide', 'happy-addons-pro'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'product_add_to_cart_show',
            [
                'label' => __('Show Add To cart', 'happy-addons-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'happy-addons-pro'),
                'label_off' => __('Hide', 'happy-addons-pro'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'product_quick_view_show',
            [
                'label' => __('Show Quick View', 'happy-addons-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'happy-addons-pro'),
                'label_off' => __('Hide', 'happy-addons-pro'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'content_alignment',
            [
                'label' => __('Content Alignment', 'happy-addons-pro'),
                'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-right',
					]
				],
                'toggle' => true,
                'selectors_dictionary' => [
                    'left' => 'align-items: flex-start',
                    'center' => 'align-items: center',
                    'right' => 'align-items: flex-end',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-item-inner' => '{{VALUE}};'
                ]
            ]
        );

        $this->add_control(
            'title_tag',
            [
                'label' => __('Title HTML Tag', 'happy-addons-pro'),
                'type' => Controls_Manager::SELECT,
                // 'separator' => 'before',
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'div',
                    'span' => 'span',
                    'p' => 'p',
                ],
                'default' => 'h2',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            '_section_query',
            [
                'label' => __('Query', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->register_query_controls();
        $this->update_control(
            'posts_post_type',
            [
                'type' => Controls_Manager::HIDDEN,
                'default' => 'product'
            ]
        );
        $this->remove_control('posts_selected_ids');
        $this->update_control(
            'posts_include_by',
            [
                'options' => [
                    'terms' => __('Terms', 'happy-addons-pro'),
                    'featured' => __('Featured Products', 'happy-addons-pro'),
                ]
            ]
        );
        $this->remove_control('posts_include_author_ids');
        $this->update_control(
            'posts_exclude_by',
            [
                'options' => [
                    'current_post'      => __('Current Product', 'happy-addons-pro'),
                    'manual_selection'  => __('Manual Selection', 'happy-addons-pro'),
                    'terms'             => __('Terms', 'happy-addons-pro'),
                ]
            ]
        );
        $this->remove_control('posts_exclude_author_ids');
        $this->update_control(
            'posts_include_term_ids',
            [
                'description' => __('Select product categories and tags', 'happy-addons-pro'),
            ]
        );
        $this->update_control(
            'posts_exclude_term_ids',
            [
                'description' => __('Select product categories and tags', 'happy-addons-pro'),
            ]
        );
        $this->update_control(
            'posts_select_date',
            [
                'type' => Controls_Manager::HIDDEN,
                'default' => 'anytime'
            ]
        );
        $this->remove_control('posts_date_before');
        $this->remove_control('posts_date_after');
        $this->update_control(
            'posts_orderby',
            [
                'options' => [
                    'comment_count' => __('Review Count', 'happy-addons-pro'),
                    'date'          => __('Date', 'happy-addons-pro'),
                    'ID'            => __('ID', 'happy-addons-pro'),
                    'menu_order'    => __('Menu Order', 'happy-addons-pro'),
                    'rand'          => __('Random', 'happy-addons-pro'),
                    'title'         => __('Title', 'happy-addons-pro'),
                ],
                'default' => 'title',
            ]
        );
        $this->update_control(
            'posts_order',
            [
                'default' => 'asc',
            ]
        );
        $this->remove_control('posts_ignore_sticky_posts');
        $this->update_control(
            'posts_only_with_featured_image',
            [
                'type' => Controls_Manager::HIDDEN,
                'default' => false
            ]
        );
        $this->add_control(
            'posts_per_page',
            [
                'label' => __('Number Of Products', 'happy-addons-pro'),
                'description' => __('Only visible products will be shown in the products grid. Hence number of products in the grid may differ from number of products setting.', 'happy-addons-pro'),
                'type' => Controls_Manager::NUMBER,
                'default' => 9,
            ]
        );

        $this->add_control(
            'add_to_cart_text',
            [
                'label' => __('Add To Cart Text', 'happy-addons-pro'),
                'type' => Controls_Manager::TEXT,
                'label_block' => false,
                'default' => __('Add To Cart', 'happy-addons-pro'),
                'dynamic' => [
                    'active' => true
                ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            '_section_settings',
            [
                'label' => __('Carousel Settings', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'animation_speed',
            [
                'label' => __('Animation Speed', 'happy-addons-pro'),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'step' => 10,
                'max' => 10000,
                'default' => 800,
                'description' => __('Slide speed in milliseconds', 'happy-addons-pro'),
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => __('Autoplay?', 'happy-addons-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'happy-addons-pro'),
                'label_off' => __('No', 'happy-addons-pro'),
                'return_value' => 'yes',
                'default' => 'yes',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label' => __('Autoplay Speed', 'happy-addons-pro'),
                'type' => Controls_Manager::NUMBER,
                'min' => 100,
                'step' => 100,
                'max' => 10000,
                'default' => 2000,
                'description' => __('Autoplay speed in milliseconds', 'happy-addons-pro'),
                'condition' => [
                    'autoplay' => 'yes'
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'loop',
            [
                'label' => __('Infinite Loop?', 'happy-addons-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'happy-addons-pro'),
                'label_off' => __('No', 'happy-addons-pro'),
                'return_value' => 'yes',
                'default' => 'yes',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'navigation',
            [
                'label' => __('Navigation', 'happy-addons-pro'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'none' => __('None', 'happy-addons-pro'),
                    'arrow' => __('Arrow', 'happy-addons-pro'),
                    'dots' => __('Dots', 'happy-addons-pro'),
                    'both' => __('Arrow & Dots', 'happy-addons-pro'),
                ],
                'default' => 'arrow',
                'frontend_available' => true,
                'style_transfer' => true,
            ]
        );

        $this->add_responsive_control(
            'slides_to_show',
            [
                'label' => __('Slides To Show', 'happy-addons-pro'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    1 => __('1 Slide', 'happy-addons-pro'),
                    2 => __('2 Slides', 'happy-addons-pro'),
                    3 => __('3 Slides', 'happy-addons-pro'),
                    4 => __('4 Slides', 'happy-addons-pro'),
                    5 => __('5 Slides', 'happy-addons-pro'),
                    6 => __('6 Slides', 'happy-addons-pro'),
                ],
                'desktop_default' => 3,
                'tablet_default' => 2,
                'mobile_default' => 1,
                'frontend_available' => true,
                'style_transfer' => true,
            ]
        );

        $this->end_controls_section();
    }

    protected function register_style_controls() {
        $this->start_controls_section(
            '_section_common_style',
            [
                'label' => __('Carousel Item', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'carousel_item_heght',
            [
                'label' => __('Height', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 1200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-item-inner' => 'height: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_responsive_control(
            'carousel_item_spacing',
            [
                'label' => __('Space between Items', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'carousel_item_border',
                'selector' => '{{WRAPPER}} .ha-product-carousel-item-inner',
            ]
        );

        $this->add_responsive_control(
            'carousel_item_border_radius',
            [
                'label' => __('Border Radius', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-item-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'item_box_shadow',
                'selector' => '{{WRAPPER}} .ha-product-carousel-item-inner',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'carousel_item_background',
                'types' => ['classic', 'gradient'],
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .ha-product-carousel-item-inner'
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            '_section_feature_image',
            [
                'label' => __('Image & Badge', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'feature_image_width',
            [
                'label' => __('Width', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 2000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-image img' => 'width: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_responsive_control(
            'feature_image_height',
            [
                'label' => __('Height', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 2000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-image img' => 'height: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_responsive_control(
            'feature_image_border_radius',
            [
                'label' => __('Border Radius', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .ha-product-carousel-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'image_box_shadow',
                'selector' => '{{WRAPPER}} .ha-product-carousel-image',
            ]
        );

        $this->add_control(
            '_heading_badge',
            [
                'label' => __('Badge', 'happy-addons-pro'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'badge_note',
            [
                'label' => false,
                'type' => Controls_Manager::RAW_HTML,
                'raw' => __('<strong>Badge</strong> is Switched off on "Layout"', 'happy-addons-pro'),
                'condition' => [
                    'product_on_sale_show!' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'badge_position_toggle',
            [
                'label' => __('Position', 'happy-addons-pro'),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'label_off' => __('None', 'happy-addons-pro'),
                'label_on' => __('Custom', 'happy-addons-pro'),
                'return_value' => 'yes',
            ]
        );

        $this->start_popover();

        $this->add_responsive_control(
            'badge_position_y',
            [
                'label' => __('Vertical', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'condition' => [
                    'badge_position_toggle' => 'yes'
                ],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 1000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-on-sale' => 'top: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_responsive_control(
            'badge_position_x',
            [
                'label' => __('Horizontal', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'condition' => [
                    'badge_position_toggle' => 'yes'
                ],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 1000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-on-sale' => 'left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_popover();

        $this->add_responsive_control(
            'badge_padding',
            [
                'label' => __('Padding', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-on-sale span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'badge_border_radius',
            [
                'label' => __('Border Radius', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-on-sale span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'badge_box_shadow',
                'selector' => '{{WRAPPER}} .ha-product-carousel-on-sale span',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'badge_typography',
                'label' => __('Typography', 'happy-addons-pro'),
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
                'selector' => '{{WRAPPER}} .ha-product-carousel-on-sale span',
            ]
        );

        $this->add_control(
            'badge_background_color',
            [
                'label' => __('Background Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-on-sale span' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'badge_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-on-sale span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            '_section_content_style',
            [
                'label' => __('Content', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            '_heading_name',
            [
                'label' => __('Name', 'happy-addons-pro'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_responsive_control(
            'name_spacing',
            [
                'label' => __('Bottom Spacing', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-title' => 'margin-bottom: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'name_typography',
                'label' => __('Typography', 'happy-addons-pro'),
                'selector' => '{{WRAPPER}} .ha-product-carousel-title',
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
            ]
        );

        $this->add_control(
            'name_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-title a' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->add_control(
            'name_hover_color',
            [
                'label' => __('Hover Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-title a:hover' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->add_control(
            '_heading_price',
            [
                'label' => __('Price', 'happy-addons-pro'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'label' => __('Typography', 'happy-addons-pro'),
                'selector' => '{{WRAPPER}} .ha-product-carousel-price',
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-price' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->add_control(
            '_heading_rating',
            [
                'label' => __('Ratings', 'happy-addons-pro'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'ratings_note',
            [
                'label' => false,
                'type' => Controls_Manager::RAW_HTML,
                'raw' => __('<strong>Ratings</strong> is not selected on "Layout"', 'happy-addons-pro'),
                'condition' => [
                    'product_ratings_show!' => 'yes'
                ],
            ]
        );

        $this->add_control(
            'ratings_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-ratings .star-rating span:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            '_section_style_add_to_cart',
            [
                'label' => __('Add to Cart Button', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'add_to_cart_note',
            [
                'label' => false,
                'type' => Controls_Manager::RAW_HTML,
                'raw' => __('<strong>Add To Cart</strong> is not selected on "Layout"', 'happy-addons-pro'),
                'condition' => [
                    'product_add_to_cart_show!' => 'yes'
                ],
            ]
        );

        $this->add_responsive_control(
            'add_to_cart_spacing',
            [
                'label' => __('Spacing', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-add-to-cart' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'add_to_cart_padding',
            [
                'label' => __('Padding', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-add-to-cart a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'add_to_cart_border_radius',
            [
                'label' => __('Border Radius', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-add-to-cart a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'add_to_cart_border',
                'selector' => '{{WRAPPER}} .ha-product-carousel-add-to-cart a',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'add_to_cart_typography',
                'label' => __('Typography', 'happy-addons-pro'),
                'selector' => '{{WRAPPER}} .ha-product-carousel-add-to-cart a',
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
            ]
        );

        $this->start_controls_tabs('_tab_add_to_cart_colors');
        $this->start_controls_tab(
            '_tab_links_normal',
            [
                'label' => __('Normal', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'add_to_cart_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-add-to-cart a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'add_to_cart_background_color',
            [
                'label' => __('Background Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-add-to-cart a' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->start_controls_tab(
            '_tab_add_to_cart_hover',
            [
                'label' => __('Hover', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'add_to_cart_hover_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-add-to-cart a:hover, {{WRAPPER}} .ha-product-carousel-add-to-cart a:focus' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'add_to_cart_hover_background_color',
            [
                'label' => __('Background Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-add-to-cart a:hover, {{WRAPPER}} .ha-product-carousel-add-to-cart a:focus' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'add_to_cart_hover_border_color',
            [
                'label' => __('Border Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-add-to-cart a:hover, {{WRAPPER}} .ha-product-carousel-add-to-cart a:focus' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'add_to_cart_border_border!' => '',
                ]
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            '_section_style_arrow',
            [
                'label' => __('Navigation - Arrow', 'happy-addons-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'arrow_position_toggle',
            [
                'label' => __('Position', 'happy-addons-pro'),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'label_off' => __('None', 'happy-addons-pro'),
                'label_on' => __('Custom', 'happy-addons-pro'),
                'return_value' => 'yes',
            ]
        );

        $this->start_popover();

        $this->add_control(
            'arrow_sync_position',
            [
                'label' => __('Sync Position', 'happy-addons-pro'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'yes' => [
                        'title' => __('Yes', 'happy-addons-pro'),
                        'icon' => 'eicon-sync',
                    ],
                    'no' => [
                        'title' => __('No', 'happy-addons-pro'),
                        'icon' => 'eicon-h-align-stretch',
                    ]
                ],
                'condition' => [
                    'arrow_position_toggle' => 'yes'
                ],
                'default' => 'no',
                'toggle' => false,
                'prefix_class' => 'ha-arrow-sync-'
            ]
        );

        $this->add_control(
            'sync_position_alignment',
            [
                'label' => __('Alignment', 'happy-addons-pro'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-right',
					]
				],
                'condition' => [
                    'arrow_position_toggle' => 'yes',
                    'arrow_sync_position' => 'yes'
                ],
                'default' => 'center',
                'toggle' => false,
                'selectors_dictionary' => [
                    'left' => 'left: calc(0px + 80px)',
                    'center' => 'left: 50%',
                    'right' => 'left: calc(100% - 50px)',
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => '{{VALUE}}'
                ]
            ]
        );

        $this->add_responsive_control(
            'arrow_position_y',
            [
                'label' => __('Vertical', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'condition' => [
                    'arrow_position_toggle' => 'yes'
                ],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 1000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'top: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_responsive_control(
            'arrow_position_x',
            [
                'label' => __('Horizontal', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'condition' => [
                    'arrow_position_toggle' => 'yes'
                ],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 1200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}.ha-arrow-sync-no .slick-prev' => 'left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.ha-arrow-sync-no .slick-next' => 'right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.ha-arrow-sync-yes .slick-next, {{WRAPPER}}.ha-arrow-sync-yes .slick-prev' => 'left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrow_spacing',
            [
                'label' => __('Space between Arrows', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'condition' => [
                    'arrow_position_toggle' => 'yes',
                    'arrow_sync_position' => 'yes'
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 40,
                ],
                'selectors' => [
                    '{{WRAPPER}}.ha-arrow-sync-yes .slick-next' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_popover();

        $this->add_responsive_control(
            'arrow_size',
            [
                'label' => __('Box Size', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 5,
                        'max' => 70,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-prev' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .slick-next' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrow_font_size',
            [
                'label' => __('Icon Size', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 2,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-prev' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .slick-next' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'arrow_border',
                'selector' => '{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next',
            ]
        );

        $this->add_responsive_control(
            'arrow_border_radius',
            [
                'label' => __('Border Radius', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->start_controls_tabs('_tabs_arrow');

        $this->start_controls_tab(
            '_tab_arrow_normal',
            [
                'label' => __('Normal', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'arrow_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrow_bg_color',
            [
                'label' => __('Background Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            '_tab_arrow_hover',
            [
                'label' => __('Hover', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'arrow_hover_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .slick-prev:hover, {{WRAPPER}} .slick-next:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrow_hover_bg_color',
            [
                'label' => __('Background Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .slick-prev:hover, {{WRAPPER}} .slick-next:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrow_hover_border_color',
            [
                'label' => __('Border Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'arrow_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-prev:hover, {{WRAPPER}} .slick-next:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            '_section_style_dots',
            [
                'label' => __('Navigation - Dots', 'happy-addons-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'dots_nav_position_y',
            [
                'label' => __('Vertical Position', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-dots' => 'bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_nav_spacing',
            [
                'label' => __('Space Between', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li' => 'margin-right: calc({{SIZE}}{{UNIT}} / 2); margin-left: calc({{SIZE}}{{UNIT}} / 2);',
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_nav_align',
            [
                'label' => __('Alignment', 'happy-addons-pro'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'happy-addons-pro'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'happy-addons-pro'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'happy-addons-pro'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .slick-dots' => 'text-align: {{VALUE}}'
                ]
            ]
        );

        $this->start_controls_tabs('_tabs_dots');
        $this->start_controls_tab(
            '_tab_dots_normal',
            [
                'label' => __('Normal', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'dots_nav_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li button:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            '_tab_dots_hover',
            [
                'label' => __('Hover', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'dots_nav_hover_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li button:hover:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            '_tab_dots_active',
            [
                'label' => __('Active', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'dots_nav_active_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .slick-dots .slick-active button:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            '_section_style_qv_button',
            [
                'label' => __('Quick View Button', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'qv_btn_padding',
            [
                'label' => __('Padding', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-quick-view-wrap a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'qv_btn_border_radius',
            [
                'label' => __('Border Radius', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-quick-view-wrap a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'qv_btn_typography',
                'label' => __('Typography', 'happy-addons-pro'),
                'selector' => '{{WRAPPER}} .ha-product-carousel-quick-view-wrap',
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
            ]
        );

        $this->start_controls_tabs('_tab_qv_btn_stats');
        $this->start_controls_tab(
            '_tab_qv_btn_stat_normal',
            [
                'label' => __('Normal', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'qv_btn_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-quick-view-wrap a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'qv_btn_bg_color',
            [
                'label' => __('Background Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-quick-view-wrap a' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->start_controls_tab(
            '_tab_qv_btn_stat_hover',
            [
                'label' => __('Hover', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'qv_btn_hover_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-quick-view-wrap a:hover, {{WRAPPER}} .ha-product-carousel-quick-view-wrap a:focus' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'qv_btn_hover_bg_color',
            [
                'label' => __('Background Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-product-carousel-quick-view-wrap a:hover, {{WRAPPER}} .ha-product-carousel-quick-view-wrap a:focus' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            '_section_style_qv_modal',
            [
                'label' => __('Quick View Modal', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            '_heading_qv_title',
            [
                'label' => __('Title', 'happy-addons-pro'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_responsive_control(
            'qv_title_spacing',
            [
                'label' => __('Bottom Spacing', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__title' => 'margin-bottom: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'qv_title_typography',
                'label' => __('Typography', 'happy-addons-pro'),
                'selector' => '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__title',
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
            ]
        );

        $this->add_control(
            'qv_title_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__title' => 'color: {{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            '_heading_qv_rating',
            [
                'label' => __('Rating', 'happy-addons-pro'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_responsive_control(
            'qv_rating_spacing',
            [
                'label' => __('Bottom Spacing', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__rating' => 'margin-bottom: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_control(
            'qv_rating_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__rating' => 'color: {{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            '_heading_qv_price',
            [
                'label' => __('Price', 'happy-addons-pro'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_responsive_control(
            'qv_price_spacing',
            [
                'label' => __('Bottom Spacing', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__price' => 'margin-bottom: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'qv_price_typography',
                'label' => __('Typography', 'happy-addons-pro'),
                'selector' => '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__price',
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
            ]
        );

        $this->add_control(
            'qv_price_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__price' => 'color: {{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            '_heading_qv_summary',
            [
                'label' => __('Summary', 'happy-addons-pro'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'qv_summary_spacing',
            [
                'label' => __('Bottom Spacing', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__summary' => 'margin-bottom: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'qv_summary_typography',
                'label' => __('Typography', 'happy-addons-pro'),
                'selector' => '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__summary',
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
            ]
        );

        $this->add_control(
            'qv_summary_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__summary' => 'color: {{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            '_heading_qv_cart',
            [
                'label' => __('Add To Cart', 'happy-addons-pro'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_responsive_control(
            'qv_cart_padding',
            [
                'label' => __('Padding', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'qv_cart_border_radius',
            [
                'label' => __('Border Radius', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'qv_cart_border',
                'selector' => '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'qv_cart_typography',
                'label' => __('Typography', 'happy-addons-pro'),
                'selector' => '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button',
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
            ]
        );

        $this->start_controls_tabs('_tab_qv_cart_stats');
        $this->start_controls_tab(
            '_tab_qv_cart_normal',
            [
                'label' => __('Normal', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'qv_cart_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'qv_cart_bg_color',
            [
                'label' => __('Background Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->start_controls_tab(
            '_tab_qv_cart_hover',
            [
                'label' => __('Hover', 'happy-addons-pro'),
            ]
        );

        $this->add_control(
            'qv_cart_hover_color',
            [
                'label' => __('Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button:hover, .ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button:focus' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'qv_cart_hover_bg_color',
            [
                'label' => __('Background Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button:hover, .ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button:focus' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'qv_cart_hover_border_color',
            [
                'label' => __('Border Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button:hover, .ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button:focus' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'qv_cart_border_border!' => '',
                ]
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    public function custom_add_to_cart_text($text, $product) {
        $add_to_cart_text = $this->get_settings_for_display('add_to_cart_text');

        if ($product->get_type() === 'simple' && $product->is_purchasable() && $product->is_in_stock() && !empty($add_to_cart_text)) {
            $text = $add_to_cart_text;
        }

        return $text;
    }

    public function print_quick_view_button($product_id) {
        $url = add_query_arg(
            [
                'action'     => 'ha_show_product_quick_view',
                'product_id' => $product_id,
                'nonce'      => wp_create_nonce('ha_show_product_quick_view'),
            ],
            admin_url('admin-ajax.php')
        );

        $quick_view_text = ('classic' == $this->get_settings_for_display('skin')) ? '<span>' . esc_html__('Quick View', 'happy-addons-pro') . '</span>' : '<span class="ha-screen-reader-text">' . esc_html__('Quick View', 'happy-addons-pro') . '</span></a>';

        printf(
            '<a href="#" data-mfp-src="%s" class="ha-pqv-btn" data-modal-class="ha-pqv--%s"><i class="far fa-eye"></i>%s</a>',
            esc_url($url),
            $this->get_id(),
            $quick_view_text
        );
    }

    public function render() {
        $settings = $this->get_settings_for_display();

        if (!class_exists('WooCommerce')) {
            printf('<div class="ha-product-carousel-error">%s</div>', __('Please Install/Activate Woocommerce Plugin.', 'happy-addons-pro'));

            return;
        }

        $loop = $this->get_query();

        $this->add_render_attribute(
            'wrapper',
            'class',
            [
                'ha-product-carousel-wrapper',
                'ha-layout-' . $settings['skin'],
                'ha-product-carousel-' . $settings['skin'],
            ]
        );

        $harcc_uid = !empty($settings['product_carousel_rcc_unique_id']) && $settings['skin'] == 'remote_carousel' ? 'harccuid_' . $settings['product_carousel_rcc_unique_id'] : '';
        
        ?>

        <div data-ha_rcc_uid="<?php echo esc_attr( $harcc_uid ); ?>" <?php $this->print_render_attribute_string('wrapper'); ?>>
            <?php
            if ($loop->have_posts()) :
                if($settings['skin'] == 'classic') :
                    add_filter('woocommerce_product_add_to_cart_text', [$this, 'custom_add_to_cart_text'], 10, 2);
                endif;

                while ($loop->have_posts()) : $loop->the_post();
                    global $product;
            ?>
                    <article class="ha-product-carousel-item" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
                        <div class="ha-product-carousel-item-inner">
                            <div class="ha-product-carousel-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php echo woocommerce_get_product_thumbnail($settings['post_image_size']); ?>
                                </a>

                                <?php if ($settings['product_on_sale_show'] == 'yes') : ?>
                                    <div class="ha-product-carousel-on-sale"><?php woocommerce_show_product_loop_sale_flash(); ?></div>
                                <?php endif; ?>

                                <?php if ($settings['product_quick_view_show'] == 'yes' || $settings['product_add_to_cart_show'] == 'yes') : ?>
                                    <div class="ha-product-carousel-quick-view-wrap">
                                        <?php if ($settings['product_quick_view_show'] == 'yes') : ?>
                                            <?php $this->print_quick_view_button($product->get_id()); ?>
                                        <?php endif; ?>

                                        <?php if ($settings['skin'] == 'modern' && $settings['product_add_to_cart_show'] == 'yes') : ?>
                                            <div class="ha-product-carousel-add-to-cart">
                                                <?php woocommerce_template_loop_add_to_cart(); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                            </div>

                            <?php if ($settings['product_ratings_show'] == 'yes' && $product->get_average_rating()) : ?>
                                <div class="ha-product-carousel-ratings"><?php woocommerce_template_loop_rating();  ?></div>
                            <?php endif; ?>

                            <<?php echo ha_escape_tags($settings['title_tag'], 'h2') . ' class="ha-product-carousel-title"'; ?>>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </<?php echo ha_escape_tags($settings['title_tag'], 'h2'); ?>>

                            <div class="ha-product-carousel-price"><?php echo $product->get_price_html(); ?></div>

                            <?php if ($settings['skin'] == 'classic' && $settings['product_add_to_cart_show'] == 'yes') : ?>
                                <div class="ha-product-carousel-add-to-cart">
                                    <?php woocommerce_template_loop_add_to_cart(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>

            <?php
                endwhile;

                wp_reset_postdata();

                if($settings['skin'] == 'classic') :
                    remove_filter('woocommerce_product_add_to_cart_text', [$this, 'custom_add_to_cart_text'], 10, 2);
                endif;

            else :
                if (is_admin()) {
                    return printf('<div class="ha-product-carousel-error">%s</div>', __('Nothing Found. Please Add Products.', 'happy-addons-pro'));
                }
            endif;
            ?>
        </div>

        <?php
    }

}
