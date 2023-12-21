<?php

namespace Happy_Addons\Elementor;

use Elementor\Element_Base;
// use Happy_Addons\Elementor\Dashboard;

defined('ABSPATH') || die();

class Widgets_Manager {


	const WIDGETS_DB_KEY = 'happyaddons_inactive_widgets';
	// public static $catwise_widget_map = [];

	/**
	 * Initialize
	 */
	public static function init() {

		// legacy support hook
		if( defined('HAPPY_ADDONS_PRO_VERSION') && HAPPY_ADDONS_PRO_VERSION <= '2.7.0' ){
			add_action( 'elementor/widgets/widgets_registered', [__CLASS__, 'register']);
		}

		// original hook for register widgets
		add_action('elementor/widgets/register', [__CLASS__, 'register']);

		add_action('elementor/frontend/before_render', [__CLASS__, 'add_global_widget_render_attributes']);
	}

	public static function add_global_widget_render_attributes(Element_Base $widget) {
		if ($widget->get_name() === 'global' && method_exists($widget, 'get_original_element_instance')) {
			$original_instance = $widget->get_original_element_instance();
			if (method_exists($original_instance, 'get_html_wrapper_class') && strpos($original_instance->get_data('widgetType'), 'ha-') !== false) {
				$widget->add_render_attribute('_wrapper', [
					'class' => $original_instance->get_html_wrapper_class(),
				]);
			}
		}
	}

	public static function get_inactive_widgets() {
		return get_option(self::WIDGETS_DB_KEY, []);
	}

	public static function save_inactive_widgets($widgets = []) {
		update_option(self::WIDGETS_DB_KEY, $widgets);
	}

	public static function get_widgets_map() {
		$widgets_map = [
			self::get_base_widget_key() => [
				'css'    => ['common'],
				'js'     => [],
				'vendor' => [
					'js'  => [],
					'css' => ['happy-icons', 'font-awesome'],
				],
			],
		];

		$local_widgets_map = self::get_local_widgets_map();
		$widgets_map       = array_merge($widgets_map, $local_widgets_map);

		// This will be remove after march/2022 pro relese
		// $pro_widget_map = array_replace_recursive(self::get_pro_widget_map(), apply_filters( 'happyaddons_get_widgets_map', [] ));

		// This will be used after march/2022 pro relese
		// $pro_widget_map = apply_filters( 'happyaddons_get_widgets_map', self::get_pro_widget_map() );

		// return array_merge($widgets_map, $pro_widget_map);
		return apply_filters('happyaddons_get_widgets_map', $widgets_map);
	}

	/**
	 * Get the pro widgets map for dashboard only
	 *
	 * @return array
	 */
	public static function get_pro_widget_map() {
		return [
			'advanced-heading'          => [
				'cat'    => 'general',
				'title'  => __('Advanced Heading', 'happy-elementor-addons'),
				'icon'   => 'hm hm-advanced-heading',
				'is_pro' => true,
			],
			'list-group'                => [
				'cat'    => 'general',
				'title'  => __('List Group', 'happy-elementor-addons'),
				'icon'   => 'hm hm-list-group',
				'is_pro' => true,
			],
			'hover-box'                 => [
				'cat'    => 'creative',
				'title'  => __('Hover Box', 'happy-elementor-addons'),
				'icon'   => 'hm hm-finger-point',
				'is_pro' => true,
			],
			'countdown'                 => [
				'cat'    => 'general',
				'title'  => __('Countdown', 'happy-elementor-addons'),
				'icon'   => 'hm hm-refresh-time',
				'is_pro' => true,
			],
			'team-carousel'             => [
				'cat'    => 'slider-&-carousel',
				'title'  => __('Team Carousel', 'happy-elementor-addons'),
				'icon'   => 'hm hm-team-carousel',
				'is_pro' => true,
			],
			'logo-carousel'             => [
				'cat'    => 'slider-&-carousel',
				'title'  => __('Logo Carousel', 'happy-elementor-addons'),
				'icon'   => 'hm hm-logo-carousel',
				'is_pro' => true,
			],
			'source-code'               => [
				'cat'    => 'general',
				'title'  => __('Source Code', 'happy-elementor-addons'),
				'icon'   => 'hm hm-code-browser',
				'is_pro' => true,
			],
			'feature-list'              => [
				'cat'    => 'general',
				'title'  => __('Feature List', 'happy-elementor-addons'),
				'icon'   => 'hm hm-list-2',
				'is_pro' => true,
			],
			'testimonial-carousel'      => [
				'cat'    => 'slider-&-carousel',
				'title'  => __('Testimonial Carousel', 'happy-elementor-addons'),
				'icon'   => 'hm hm-testimonial-carousel',
				'is_pro' => true,
			],
			'advanced-tabs'             => [
				'cat'    => 'general',
				'title'  => __('Advanced Tabs', 'happy-elementor-addons'),
				'icon'   => 'hm hm-tab',
				'is_pro' => true,
			],
			'advanced-flip-box'         => [
				'cat'    => 'creative',
				'title'  => __('Advanced Flip Box', 'happy-elementor-addons'),
				'icon'   => 'hm hm-flip-card1',
				'is_pro' => true,
			],
			'animated-text'             => [
				'cat'    => 'creative',
				'title'  => __('Animated Text', 'happy-elementor-addons'),
				'icon'   => 'hm hm-text-animation',
				'is_pro' => true,
			],
			'timeline'                  => [
				'cat'    => 'general',
				'title'  => __('Timeline', 'happy-elementor-addons'),
				'icon'   => 'hm hm-timeline',
				'is_pro' => true,
			],
			'instagram-feed'            => [
				'cat'    => 'social-media',
				'title'  => __('Instagram Feed', 'happy-elementor-addons'),
				'icon'   => 'hm hm-instagram',
				'is_pro' => true,
			],
			'scrolling-image'           => [
				'cat'    => 'creative',
				'title'  => __('Scrolling Image', 'happy-elementor-addons'),
				'icon'   => 'hm hm-scrolling-image',
				'is_pro' => true,
			],
			'advanced-pricing-table'    => [
				'cat'    => 'marketing',
				'title'  => __('Advanced Pricing Table', 'happy-elementor-addons'),
				'icon'   => 'hm hm-file-cabinet',
				'is_pro' => true,
			],
			'business-hour'             => [
				'cat'    => 'general',
				'title'  => __('Business Hour', 'happy-elementor-addons'),
				'icon'   => 'hm hm-hand-watch',
				'is_pro' => true,
			],
			'accordion'                 => [
				'cat'    => 'general',
				'title'  => __('Advanced Accordion', 'happy-elementor-addons'),
				'icon'   => 'hm hm-accordion-vertical',
				'is_pro' => true,
			],
			'toggle'                    => [
				'cat'    => 'general',
				'title'  => __('Advanced Toggle', 'happy-elementor-addons'),
				'icon'   => 'hm hm-accordion-vertical',
				'is_pro' => true,
			],
			'promo-box'                 => [
				'cat'    => 'marketing',
				'title'  => __('Promo Box', 'happy-elementor-addons'),
				'icon'   => 'hm hm-promo',
				'is_pro' => true,
			],
			'hotspots'                  => [
				'cat'    => 'creative',
				'title'  => __('Hotspots', 'happy-elementor-addons'),
				'icon'   => 'hm hm-accordion-vertical',
				'is_pro' => true,
			],
			'price-menu'                => [
				'cat'    => 'marketing',
				'title'  => __('Price Menu', 'happy-elementor-addons'),
				'icon'   => 'hm hm-menu-price',
				'is_pro' => true,
			],
			'facebook-feed'             => [
				'cat'    => 'social-media',
				'title'  => __('Facebook Feed', 'happy-elementor-addons'),
				'icon'   => 'hm hm-facebook',
				'is_pro' => true,
			],
			'line-chart'                => [
				'cat'    => 'chart',
				'title'  => __('Line Chart', 'happy-elementor-addons'),
				'icon'   => 'hm hm-line-graph-pointed',
				'is_pro' => true,
			],
			'pie-chart'                 => [
				'cat'    => 'chart',
				'title'  => __('Pie & Doughnut Chart', 'happy-elementor-addons'),
				'icon'   => 'hm hm-graph-pie',
				'is_pro' => true,
			],
			'polar-chart'               => [
				'cat'    => 'chart',
				'title'  => __('Polar area Chart', 'happy-elementor-addons'),
				'icon'   => 'hm hm-graph-pie',
				'is_pro' => true,
			],
			'radar-chart'               => [
				'cat'    => 'chart',
				'title'  => __('Radar Chart', 'happy-elementor-addons'),
				'icon'   => 'hm hm-graph-pie',
				'is_pro' => true,
			],
			'post-tiles'                => [
				'cat'    => 'post',
				'title'  => __('Post Tiles', 'happy-elementor-addons'),
				'icon'   => 'hm hm-article',
				'is_pro' => true,
			],
			'post-carousel'             => [
				'cat'    => 'post',
				'title'  => __('Post Carousel', 'happy-elementor-addons'),
				'icon'   => 'hm hm-graph-pie',
				'is_pro' => true,
			],
			'smart-post-list'           => [
				'cat'    => 'post',
				'title'  => __('Smart Post List', 'happy-elementor-addons'),
				'icon'   => 'hm hm-post-list',
				'is_pro' => true,
			],
			'breadcrumbs'               => [
				'cat'    => 'general',
				'title'  => __('Breadcrumbs', 'happy-elementor-addons'),
				'icon'   => 'hm hm-breadcrumbs',
				'is_pro' => true,
			],
			'twitter-carousel'          => [
				'cat'    => 'social-media',
				'title'  => __('Twitter Feed Carousel', 'happy-elementor-addons'),
				'icon'   => 'hm hm-twitter',
				'is_pro' => true,
			],
			'author-list'               => [
				'cat'    => 'post',
				'title'  => __('Author List', 'happy-elementor-addons'),
				'icon'   => 'hm hm-user-male',
				'is_pro' => true,
			],
			'post-grid-new'                 => [
				'cat'    => 'post',
				'title'  => __('Post Grid', 'happy-elementor-addons'),
				'icon'   => 'hm hm-post-grid',
				'is_pro' => true,
			],
			'sticky-video'              => [
				'cat'    => 'general',
				'title'  => __('Sticky Video', 'happy-elementor-addons'),
				'icon'   => 'hm hm-sticky-video',
				'is_pro' => true,
			],
			'product-carousel-new'          => [
				'cat'    => 'woocommerce',
				'title'  => __('Product Carousel', 'happy-elementor-addons'),
				'icon'   => 'hm hm-Product-Carousel',
				'is_pro' => true,
			],
			'product-category-carousel-new' => [
				'cat'    => 'woocommerce',
				'title'  => __('Product Category Carousel', 'happy-elementor-addons'),
				'icon'   => 'hm hm-Category-Carousel',
				'is_pro' => true,
			],
			'product-grid-new'              => [
				'cat'    => 'woocommerce',
				'title'  => __('Product Grid', 'happy-elementor-addons'),
				'icon'   => 'hm hm-Product-Grid',
				'is_pro' => true,
			],
			'product-category-grid-new'     => [
				'cat'    => 'woocommerce',
				'title'  => __('Product Category Grid', 'happy-elementor-addons'),
				'icon'   => 'hm hm-Category-Carousel',
				'is_pro' => true,
			],
			'single-product-new'            => [
				'cat'    => 'woocommerce',
				'title'  => __('Single Product', 'happy-elementor-addons'),
				'icon'   => 'hm hm-Category-Carousel',
				'is_pro' => true,
			],
			'advanced-data-table'       => [
				'cat'    => 'general',
				'title'  => __('Advanced Data Table', 'happy-elementor-addons'),
				'icon'   => 'hm hm-data-table',
				'is_pro' => true,
			],
			'modal-popup'               => [
				'cat'    => 'general',
				'title'  => __('Modal Popup', 'happy-elementor-addons'),
				'icon'   => 'hm hm-popup',
				'is_pro' => true,
			],
			'one-page-nav'              => [
				'cat'    => 'creative',
				'title'  => __('One Page Nav', 'happy-elementor-addons'),
				'icon'   => 'hm hm-dot-navigation',
				'is_pro' => true,
			],
			'advanced-slider'           => [
				'cat'    => 'slider-&-carousel',
				'title'  => __('Advanced Slider', 'happy-elementor-addons'),
				'icon'   => 'hm hm-slider',
				'is_pro' => true,
			],
			'mini-cart'                 => [
				'cat'    => 'woocommerce',
				'title'  => __('Mini Cart', 'happy-elementor-addons'),
				'icon'   => 'hm hm-mini-cart',
				'is_pro' => true,
			],
			'wc-cart'                   => [
				'cat'    => 'woocommerce',
				'title'  => __('WooCommerce Cart', 'happy-elementor-addons'),
				'icon'   => 'hm hm-cart',
				'is_pro' => true,
			],
			'wc-checkout'               => [
				'cat'    => 'woocommerce',
				'title'  => __('WooCommerce Checkout', 'happy-elementor-addons'),
				'icon'   => 'hm hm-cart',
				'is_pro' => true,
			],
			'image-scroller'            => [
				'cat'    => 'creative',
				'title'  => __('Single Image Scroll', 'happy-elementor-addons'),
				'icon'   => 'hm hm-image-scroll',
				'is_pro' => true,
			],
			'nav-menu'                  => [
				'cat'    => 'general',
				'title'  => __('Happy Menu', 'happy-elementor-addons'),
				'icon'   => 'hm hm-mega-menu',
				'is_pro' => true
			],
			'off-canvas'                => [
				'cat'    => 'creative',
				'title'  => __('Off Canvas', 'happy-elementor-addons'),
				'icon'   => 'hm hm-offcanvas-menu',
				'is_pro' => true
			],
			'unfold'                    => [
				'cat'    => 'general',
				'title'  => __('Unfold', 'happy-elementor-addons'),
				'icon'   => 'hm hm-unfold-paper',
				'is_pro' => true
			],
			'edd-product-grid'          => [
				'cat'    => 'Easy Digital Downloads',
				'title'  => __('EDD Product Grid', 'happy-elementor-addons'),
				'icon'   => 'hm hm-product-grid',
				'is_pro' => true
			],
			'edd-product-carousel'      => [
				'cat'    => 'Easy Digital Downloads',
				'title'  => __('EDD Product Carousel', 'happy-elementor-addons'),
				'icon'   => 'hm hm-Product-Carousel',
				'is_pro' => true
			],
			'edd-single-product'        => [
				'cat'    => 'Easy Digital Downloads',
				'title'  => __('EDD Single Product', 'happy-elementor-addons'),
				'icon'   => 'hm hm-Category-Carousel',
				'is_pro' => true
			],
			'edd-category-grid'         => [
				'cat'    => 'Easy Digital Downloads',
				'title'  => __('EDD Category Grid', 'happy-elementor-addons'),
				'icon'   => 'hm hm-Category-Carousel',
				'is_pro' => true
			],
			'edd-category-carousel'     => [
				'cat'    => 'Easy Digital Downloads',
				'title'  => __('EDD Category Carousel', 'happy-elementor-addons'),
				'icon'   => 'hm hm-Category-Carousel',
				'is_pro' => true
			],
			'google-map'                => [
				'cat'    => 'general',
				'title'  => __('Advanced Google Map', 'happy-elementor-addons'),
				'icon'   => 'hm hm-map-marker',
				'is_pro' => true
			],
			'image-swap' => [
				'cat' => 'general',
				'title' => __( 'Image Swap', 'happy-elementor-addons' ),
				'icon' => 'hm hm-image-scroll',
				'is_pro' => true,
			],
			'remote-carousel' => [
				'cat' => 'slider-&-carousel',
				'title' => __('Remote Carousel', 'happy-elementor-addons'),
				'icon' => 'hm hm-remote_carousel',
				'is_pro' => true,
			],
			'table-of-contents' => [
				'cat' => 'general',
				'title' => __('Table of Contents', 'happy-elementor-addons'),
				'icon' => 'hm hm-list-2',
				'is_pro' => true,
			]
		];
	}

	/**
	 * Get the free widgets map
	 *
	 * @return array
	 */
	public static function get_local_widgets_map() {
		// All the widgets are listed below with respective map

		return [
			'infobox'             => [
				'cat'       => 'general',
				'is_active' => true,
				'demo'      => 'https://happyaddons.com/go/demo-info-box',
				'title'     => __('Info Box', 'happy-elementor-addons'),
				'icon'      => 'hm hm-info',
				'css'       => ['btn', 'infobox'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => ['lord-icon'],
				],
			],
			'card'                => [
				'cat'       => 'creative',
				'is_active' => false,
				'demo'      => 'https://happyaddons.com/go/demo-card',
				'title'     => __('Card', 'happy-elementor-addons'),
				'icon'      => 'hm hm-card',
				'css'       => ['btn', 'badge', 'card'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'cf7'                 => [
				'cat'       => 'forms',
				'is_active' => true,
				'demo'      => 'https://happyaddons.com/go/demo-contact-form7',
				'title'     => __('Contact Form 7', 'happy-elementor-addons'),
				'icon'      => 'hm hm-form',
				'css'       => [],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'icon-box'            => [
				'cat'       => 'general',
				'is_active' => true,
				'demo'      => 'https://happyaddons.com/go/demo-icon-box',
				'title'     => __('Icon Box', 'happy-elementor-addons'),
				'icon'      => 'hm hm-icon-box',
				'css'       => ['badge', 'icon-box'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => ['lord-icon'],
				],
			],
			'member'              => [
				'cat'       => 'general',
				'is_active' => true,
				'demo'      => 'https://happyaddons.com/go/demo-team-member',
				'title'     => __('Team Member', 'happy-elementor-addons'),
				'icon'      => 'hm hm-team-member',
				'css'       => ['btn', 'member'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'review'              => [
				'cat'       => 'general',
				'is_active' => true,
				'demo'      => 'https://happyaddons.com/go/demo-review',
				'title'     => __('Review', 'happy-elementor-addons'),
				'icon'      => 'hm hm-review',
				'css'       => ['review'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'image-compare'       => [
				'cat'       => 'general',
				'is_active' => true,
				'demo'      => 'https://happyaddons.com/go/demo-image-compare',
				'title'     => __('Image Compare', 'happy-elementor-addons'),
				'icon'      => 'hm hm-image-compare',
				'css'       => ['image-comparison'],
				'js'        => [],
				'vendor'    => [
					'css' => ['twentytwenty'],
					'js'  => ['jquery-event-move', 'jquery-twentytwenty', 'imagesloaded'],
				],
			],
			'justified-gallery'   => [
				'cat'       => 'creative',
				'is_active' => false,
				'demo'      => 'https://happyaddons.com/go/demo-justified-grid',
				'title'     => __('Justified Grid', 'happy-elementor-addons'),
				'icon'      => 'hm hm-brick-wall',
				'css'       => ['justified-gallery', 'gallery-filter'],
				'js'        => [],
				'vendor'    => [
					'css' => ['justifiedGallery', 'magnific-popup'],
					'js'  => ['jquery-justifiedGallery', 'jquery-magnific-popup'],
				],
			],
			'image-grid'          => [
				'cat'       => 'general',
				'is_active' => true,
				'demo'      => 'https://happyaddons.com/go/demo-image-grid',
				'title'     => __('Image Grid', 'happy-elementor-addons'),
				'icon'      => 'hm hm-grid-even',
				'css'       => ['image-grid', 'gallery-filter'],
				'js'        => [],
				'vendor'    => [
					'css' => ['magnific-popup'],
					'js'  => ['jquery-isotope', 'jquery-magnific-popup', 'imagesloaded'],
				],
			],
			'slider'              => [
				'cat'       => 'slider-&-carousel',
				'is_active' => true,
				'demo'      => 'https://happyaddons.com/go/demo-slider',
				'title'     => __('Slider', 'happy-elementor-addons'),
				'icon'      => 'hm hm-image-slider',
				'css'       => ['slider-carousel'],
				'js'        => [],
				'vendor'    => [
					'css' => ['slick', 'slick-theme'],
					'js'  => ['jquery-slick'],
				],
			],
			'carousel'            => [
				'cat'       => 'slider-&-carousel',
				'is_active' => true,
				'demo'      => 'https://happyaddons.com/go/demo-image-carousel',
				'title'     => __('Image Carousel', 'happy-elementor-addons'),
				'icon'      => 'hm hm-carousal',
				'css'       => ['slider-carousel'],
				'js'        => [],
				'vendor'    => [
					'css' => ['slick', 'slick-theme'],
					'js'  => ['jquery-slick'],
				],
			],
			'skills'              => [
				'cat'       => 'general',
				'is_active' => true,
				'demo'      => 'https://happyaddons.com/go/demo-skill-bar',
				'title'     => __('Skill Bars', 'happy-elementor-addons'),
				'icon'      => 'hm hm-progress-bar',
				'css'       => ['skills'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => ['elementor-waypoints', 'jquery-numerator'],
				],
			],
			'gradient-heading'    => [
				'cat'       => 'creative',
				'is_active' => true,
				'demo'      => 'https://happyaddons.com/go/demo-gradient-heading',
				'title'     => __('Gradient Heading', 'happy-elementor-addons'),
				'icon'      => 'hm hm-drag',
				'css'       => ['gradient-heading'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'wpform'              => [
				'cat'       => 'forms',
				'is_active' => true,
				'demo'      => 'https://happyaddons.com/go/demo-wpforms',
				'title'     => __('WPForms', 'happy-elementor-addons'),
				'icon'      => 'hm hm-form',
				'css'       => [],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'ninjaform'           => [
				'cat'       => 'forms',
				'is_active' => true,
				'demo'      => 'https://happyaddons.com/go/demo-ninja-forms',
				'title'     => __('Ninja Forms', 'happy-elementor-addons'),
				'icon'      => 'hm hm-form',
				'css'       => [],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'calderaform'         => [
				'cat'       => 'forms',
				'is_active' => true,
				'demo'      => 'https://happyaddons.com/go/demo-caldera-forms',
				'title'     => __('Caldera Forms', 'happy-elementor-addons'),
				'icon'      => 'hm hm-form',
				'css'       => [],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'weform'              => [
				'cat'       => 'forms',
				'is_active' => true,
				'demo'      => 'https://happyaddons.com/go/demo-weforms',
				'title'     => __('weForms', 'happy-elementor-addons'),
				'icon'      => 'hm hm-form',
				'css'       => [],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'logo-grid'           => [
				'cat'       => 'general',
				'is_active' => true,
				'demo'      => 'https://happyaddons.com/go/demo-logo-grid',
				'title'     => __('Logo Grid', 'happy-elementor-addons'),
				'icon'      => 'hm hm-logo-grid',
				'css'       => ['logo-grid'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'dual-button'         => [
				'cat'       => 'general',
				'is_active' => true,
				'demo'      => 'https://happyaddons.com/go/demo-dual-button',
				'title'     => __('Dual Button', 'happy-elementor-addons'),
				'icon'      => 'hm hm-accordion-horizontal',
				'css'       => ['dual-btn'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'testimonial'         => [
				'cat'       => 'general',
				'is_active' => true,
				'demo'      => 'https://happyaddons.com/go/demo-testimonial',
				'title'     => __('Testimonial', 'happy-elementor-addons'),
				'icon'      => 'hm hm-testimonial',
				'css'       => ['testimonial'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'number'              => [
				'cat'       => 'creative',
				'is_active' => true,
				'demo'      => 'https://happyaddons.com/go/demo-number-widget',
				'title'     => __('Number', 'happy-elementor-addons'),
				'icon'      => 'hm hm-madel',
				'css'       => ['number'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => ['elementor-waypoints', 'jquery-numerator'],
				],
			],
			'flip-box'            => [
				'cat'       => 'creative',
				'is_active' => true,
				'demo'      => 'https://happyaddons.com/gp/demo-flip-box',
				'title'     => __('Flip Box', 'happy-elementor-addons'),
				'icon'      => 'hm hm-flip-card1',
				'css'       => ['flip-box'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'calendly'            => [
				'cat'       => 'general',
				'is_active' => true,
				'demo'      => 'https://happyaddons.com/go/demo-calendly',
				'title'     => __('Calendly', 'happy-elementor-addons'),
				'icon'      => 'hm hm-calendar',
				'css'       => [],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'pricing-table'       => [
				'cat'       => 'marketing',
				'is_active' => true,
				'demo'      => 'https://happyaddons.com/go/demo-pricing-table',
				'title'     => __('Pricing Table', 'happy-elementor-addons'),
				'icon'      => 'hm hm-file-cabinet',
				'css'       => ['pricing-table'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'step-flow'           => [
				'cat'       => 'general',
				'is_active' => true,
				'demo'      => 'https://happyaddons.com/go/demo-step-flow',
				'title'     => __('Step Flow', 'happy-elementor-addons'),
				'icon'      => 'hm hm-step-flow',
				'css'       => ['steps-flow'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'gravityforms'        => [
				'cat'       => 'forms',
				'is_active' => true,
				'title'     => __('Gravity Forms', 'happy-elementor-addons'),
				'icon'      => 'hm hm-form',
				'css'       => [],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'news-ticker'         => [
				'cat'       => 'general',
				'is_active' => true,
				'title'     => __('News Ticker', 'happy-elementor-addons'),
				'icon'      => 'hm hm-slider',
				'css'       => ['news-ticker'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => ['jquery-keyframes'],
				],
			],
			'fun-factor'          => [
				'cat'       => 'creative',
				'is_active' => true,
				'title'     => __('Fun Factor', 'happy-elementor-addons'),
				'icon'      => 'hm hm-slider',
				'css'       => ['fun-factor'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => ['elementor-waypoints', 'jquery-numerator'],
				],
			],
			'bar-chart'           => [
				'cat'       => 'chart',
				'is_active' => true,
				'demo'      => '',
				'title'     => __('Bar Chart', 'happy-elementor-addons'),
				'icon'      => 'hm hm-graph-bar',
				'css'       => ['chart'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => ['chart-js'],
				],
			],
			'social-icons'        => [
				'cat'       => 'social-media',
				'is_active' => true,
				'title'     => __('Social Icons', 'happy-elementor-addons'),
				'icon'      => 'hm hm-bond2',
				'css'       => ['social-icons'],
				'js'        => [],
				'vendor'    => [
					'css' => ['hover-css'],
					'js'  => [],
				],
			],
			'twitter-feed'        => [
				'cat'       => 'social-media',
				'is_active' => true,
				'title'     => __('Twitter Feed', 'happy-elementor-addons'),
				'icon'      => 'hm hm-twitter-feed',
				'css'       => ['twitter-feed'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'post-list'           => [
				'cat'       => 'post',
				'is_active' => true,
				'title'     => __('Post List', 'happy-elementor-addons'),
				'icon'      => 'hm hm-post-list',
				'css'       => ['post-list'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'post-tab'            => [
				'cat'       => 'post',
				'is_active' => true,
				'title'     => __('Post Tab', 'happy-elementor-addons'),
				'icon'      => 'hm hm-post-tab',
				'css'       => ['post-tab'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'taxonomy-list'       => [
				'cat'       => 'post',
				'is_active' => true,
				'title'     => __('Taxonomy List', 'happy-elementor-addons'),
				'icon'      => 'hm hm-clip-board',
				'css'       => ['taxonomy-list'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'threesixty-rotation' => [
				'cat'       => 'general',
				'is_active' => true,
				'title'     => __('360° Rotation', 'happy-elementor-addons'),
				'icon'      => 'hm hm-3d-rotate',
				'css'       => ['threesixty-rotation'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => ['circlr', 'ha-simple-magnify'],
				],
			],
			'fluent-form'         => [
				'cat'       => 'forms',
				'is_active' => true,
				'title'     => __('Fluent Form', 'happy-elementor-addons'),
				'icon'      => 'hm hm-form',
				'css'       => [],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'data-table'          => [
				'cat'       => 'general',
				'is_active' => true,
				'title'     => __('Data Table', 'happy-elementor-addons'),
				'icon'      => 'hm hm-data-table',
				'css'       => ['data-table'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'horizontal-timeline' => [
				'cat'       => 'general',
				'is_active' => true,
				'title'     => __('Horizontal Timeline', 'happy-elementor-addons'),
				'icon'      => 'hm hm-timeline',
				'css'       => ['horizontal-timeline'],
				'js'        => [],
				'vendor'    => [
					'css' => ['slick', 'slick-theme', 'magnific-popup'],
					'js'  => ['jquery-slick', 'jquery-magnific-popup'],
				],
			],
			'social-share'        => [
				'cat'       => 'social-media',
				'is_active' => true,
				'title'     => __('Social Share', 'happy-elementor-addons'),
				'icon'      => 'hm hm-share',
				'css'       => ['social-share'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => ['sharer-js'],
				],
			],
			'image-hover-effect'  => [
				'cat'       => 'creative',
				'is_active' => true,
				'title'     => __('Image Hover Effect', 'happy-elementor-addons'),
				'icon'      => 'hm hm-cursor-hover-click',
				'css'       => ['image-hover-effect'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'event-calendar'      => [
				'cat'       => 'general',
				'is_active' => true,
				'title'     => __('Event Calendar', 'happy-elementor-addons'),
				'icon'      => 'hm hm-event-calendar',
				'css'       => ['event-calendar'],
				'js'        => [],
				'vendor'    => [
					'css' => ['ha-fullcalendar'],
					'js'  => ['ha-fullcalendar', 'ha-fullcalendar-locales'],
				],
			],
			'link-hover'          => [
				'cat'       => 'creative',
				'is_active' => true,
				'title'     => __('Animated Link', 'happy-elementor-addons'),
				'icon'      => 'hm hm-animated-link',
				'css'       => ['link-hover'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'mailchimp'           => [
				'cat'       => 'forms',
				'is_active' => true,
				'title'     => __('MailChimp', 'happy-elementor-addons'),
				'icon'      => 'hm hm-mail-chimp',
				'css'       => ['mailchimp'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'image-accordion'     => [
				'cat'       => 'general',
				'is_active' => true,
				'title'     => __('Image Accordion', 'happy-elementor-addons'),
				'icon'      => 'hm hm-slider-image',
				'css'       => ['image-accordion'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'content-switcher'    => [
				'cat'       => 'general',
				'is_active' => true,
				'title'     => __('Content Switcher', 'happy-elementor-addons'),
				'icon'      => 'hm hm-switcher',
				'css'       => ['content-switcher'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'image-stack-group'   => [
				'cat'       => 'creative',
				'is_active' => true,
				'title'     => __('Image Stack Group', 'happy-elementor-addons'),
				'icon'      => 'hm hm-lens',
				'css'       => ['circle-image-group'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'creative-button'     => [
				'cat'       => 'creative',
				'is_active' => true,
				'title'     => __('Creative Button', 'happy-elementor-addons'),
				'icon'      => 'hm hm-motion-button',
				'css'       => ['creative-button'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'pdf-view'            => [
				'cat'       => 'creative',
				'is_active' => true,
				'title'     => __('PDF View', 'happy-elementor-addons'),
				'icon'      => 'hm hm-pdf2',
				'css'       => ['pdf'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => ['pdf-js'],
				],
			],
			'comparison-table'    => [
				'cat'       => 'creative',
				'is_active' => true,
				'title'     => __('Comparison Table', 'happy-elementor-addons'),
				'icon'      => 'hm hm-scale',
				'css'       => ['comparison-table'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'photo-stack'         => [
				'cat'       => 'creative',
				'is_active' => true,
				'title'     => __('Photo Stack', 'happy-elementor-addons'),
				'icon'      => 'hm hm-lens',
				'css'       => ['photo-stack'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => [],
				],
			],
			'lordicon'            => [
				'cat'       => 'creative',
				'is_active' => true,
				'title'     => __('LordIcon', 'happy-elementor-addons'),
				'icon'      => 'hm hm-icon-box',
				'css'       => ['lordicon'],
				'js'        => [],
				'vendor'    => [
					'css' => [],
					'js'  => ['lord-icon'],
				],
			],
			'page-title' => [
				'cat' => 'theme-builder',
				'is_active' => false,
				'title' => __('Page Title', 'happy-elementor-addons'),
				'icon' => 'hm hm-tb-page-title',
				'css' => [''],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'post-title' => [
				'cat' => 'theme-builder',
				'is_active' => true,
				'title' => __('Post Title', 'happy-elementor-addons'),
				'icon' => 'hm hm-tb-page-title',
				'css' => [''],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'post-content' => [
				'cat' => 'theme-builder',
				'is_active' => true,
				'title' => __('Post Content', 'happy-elementor-addons'),
				'icon' => 'hm hm-tb-post-content',
				'css' => [''],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'post-excerpt' => [
				'cat' => 'theme-builder',
				'is_active' => false,
				'title' => __('Post Excerpt', 'happy-elementor-addons'),
				'icon' => 'hm hm-tb-post-excerpt',
				'css' => [''],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'site-logo' => [
				'cat' => 'theme-builder',
				'is_active' => true,
				'title' => __('Site Logo', 'happy-elementor-addons'),
				'icon' => 'hm hm-tb-site-logo',
				'css' => [''],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'site-title' => [
				'cat' => 'theme-builder',
				'is_active' => true,
				'title' => __('Site Title', 'happy-elementor-addons'),
				'icon' => 'hm hm-tb-site-title',
				'css' => ['site-title'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'site-tagline' => [
				'cat' => 'theme-builder',
				'is_active' => false,
				'title' => __('Site Tagline', 'happy-elementor-addons'),
				'icon' => 'hm hm-tag',
				'css' => ['site-tagline'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'author-meta' => [
				'cat' => 'theme-builder',
				'is_active' => true,
				'title' => __('Author Meta', 'happy-elementor-addons'),
				'icon' => 'hm hm-tb-author-meta',
				'css' => ['author'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'post-info' => [
				'cat' => 'theme-builder',
				'is_active' => true,
				'title' => __('Post Meta', 'happy-elementor-addons'),
				'icon' => 'hm hm-tb-post-info',
				'css' => ['post-info'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'archive-title' => [
				'cat' => 'theme-builder',
				'is_active' => true,
				'title' => __('Archive Title', 'happy-elementor-addons'),
				'icon' => 'hm hm-tb-archieve-title',
				'css' => [''],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'archive-posts' => [
				'cat' => 'theme-builder',
				'is_active' => true,
				'title' => __('Archive Posts', 'happy-elementor-addons'),
				'icon' => 'hm hm-tb-archieve-content',
				'css' => ['archive-posts'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'post-comments' => [
				'cat' => 'theme-builder',
				'is_active' => true,
				'title' => __('Post Comments', 'happy-elementor-addons'),
				'icon' => 'hm hm-comment-square',
				'css' => [''],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'post-navigation' => [
				'cat' => 'theme-builder',
				'is_active' => true,
				'title' => __('Post Navigation', 'happy-elementor-addons'),
				'icon' => 'hm hm-breadcrumbs',
				'css' => ['post-navigation'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'post-featured-image' => [
				'cat' => 'theme-builder',
				'is_active' => true,
				'title' => __('Post featured image', 'happy-elementor-addons'),
				'icon' => 'hm hm-tb-featured-image',
				'css' => [''],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'navigation-menu' => [
				'cat' => 'theme-builder',
				'is_active' => true,
				'title' => __('Nav Menu', 'happy-elementor-addons'),
				'icon' => 'hm hm-clip-board',
				'css' => ['navigation-menu'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'age-gate' => [
				'cat' => 'general',
				'is_active' => true,
				'title' => __('Age Gate', 'happy-elementor-addons'),
				'icon' => 'hm hm-age-gate',
				'css' => ['age-gate'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
		];
	}

	public static function get_base_widget_key() {
		return apply_filters('happyaddons_get_base_widget_key', '_happyaddons_base');
	}

	public static function get_default_active_widget() {
		$default_active = array_filter(self::get_local_widgets_map(), function ($var) {
			return $var['is_active'] == true;
		});
		return array_keys($default_active);
	}

	/**
	 * Init Widgets
	 *
	 * Include widgets files and register them
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public static function register( $widgets_manager = null) {
		include_once HAPPY_ADDONS_DIR_PATH . 'base/widget-base.php';
		include_once HAPPY_ADDONS_DIR_PATH . 'traits/button-renderer.php';
		include_once HAPPY_ADDONS_DIR_PATH . 'traits/link-hover-markup.php';
		include_once HAPPY_ADDONS_DIR_PATH . 'traits/creative-button-markup.php';

		$inactive_widgets = self::get_inactive_widgets();

		foreach (self::get_local_widgets_map() as $widget_key => $data) {
			if (!in_array($widget_key, $inactive_widgets)) {
				self::register_widget($widget_key, $widgets_manager);
			}
		}

		/**
		 * After widgets registered.
		 *
		 * Fires after HappyAddons widgets are registered.
		 *
		 * @since 3.8.0
		 *
		 * @param Widgets_Manager $widgets_manager The widgets manager.
		 */
		do_action('happyaddons/widgets/register', $widgets_manager);
	}

	protected static function register_widget($widget_key, $widgets_manager = null) {
		$widget_file = HAPPY_ADDONS_DIR_PATH . 'widgets/' . $widget_key . '/widget.php';

		if (is_readable($widget_file)) {

			include_once $widget_file;

			$widget_class = '\Happy_Addons\Elementor\Widget\\' . str_replace('-', '_', $widget_key);
			if (class_exists($widget_class)) {
				$widgets_manager->register(new $widget_class());
			}
		}
	}
}

Widgets_Manager::init();
