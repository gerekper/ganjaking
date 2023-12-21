<?php

namespace Happy_Addons_Pro;

use Happy_Addons\Elementor\Widgets_Manager as Free_Widgets_Manager;

defined('ABSPATH') || die();

class Widgets_Manager {

	const COMMON_WIDGET_KEY = 'common-pro';

	/**
	 * Initialize
	 */
	public static function init() {
		add_filter('happyaddons_get_widgets_map', [__CLASS__, 'add_widgets_map']);

		if (hapro_get_appsero()->license()->is_valid()) {
			add_action( 'happyaddons/widgets/register', [__CLASS__, 'register'], 20 );
		}
	}

	public static function add_widgets_map($widgets) {
		$widgets = array_replace_recursive($widgets, self::get_local_widgets_map());
		$common_widget_key = Free_Widgets_Manager::get_base_widget_key();

		// Pro widgets common css
		if (isset(
			$widgets[$common_widget_key],
			$widgets[$common_widget_key]['css']
		)) {
			$widgets[$common_widget_key]['css'][] = self::COMMON_WIDGET_KEY;
		}

		return $widgets;
	}

	public static function get_local_widgets_map() {
		$widget_map = [
			'advanced-heading' => [
				'cat' => 'general',
				'title' => __('Advanced Heading', 'happy-addons-pro'),
				'icon' => 'hm hm-advanced-heading',
				'is_pro' => true,
				'css' => ['advanced-heading'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'list-group' => [
				'cat' => 'general',
				'title' => __('List Group', 'happy-addons-pro'),
				'icon' => 'hm hm-list-group',
				'is_pro' => true,
				'css' => ['list-group'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'hover-box' => [
				'cat' => 'creative',
				'title' => __('Hover Box', 'happy-addons-pro'),
				'icon' => 'hm hm-finger-point',
				'is_pro' => true,
				'css' => ['hover-box'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'countdown' => [
				'cat' => 'general',
				'title' => __('Countdown', 'happy-addons-pro'),
				'icon' => 'hm hm-refresh-time',
				'is_pro' => true,
				'css' => ['countdown'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => ['jquery-countdown'],
				],
			],
			'team-carousel' => [
				'cat' => 'slider-&-carousel',
				'title' => __('Team Carousel', 'happy-addons-pro'),
				'icon' => 'hm hm-team-carousel',
				'is_pro' => true,
				'css' => ['team-carousel'],
				'js' => [],
				'vendor' => [
					'css' => ['slick', 'slick-theme'],
					'js' => ['jquery-slick'],
				],
			],
			'logo-carousel' => [
				'cat' => 'slider-&-carousel',
				'title' => __('Logo Carousel', 'happy-addons-pro'),
				'icon' => 'hm hm-logo-carousel',
				'is_pro' => true,
				'css' => ['logo-carousel'],
				'js' => [],
				'vendor' => [
					'css' => ['slick', 'slick-theme'],
					'js' => ['jquery-slick'],
				],
			],
			'source-code' => [
				'cat' => 'general',
				'title' => __('Source Code', 'happy-addons-pro'),
				'icon' => 'hm hm-code-browser',
				'is_pro' => true,
				'css' => ['source-code'],
				'js' => [],
				'vendor' => [
					'css' => ['prism'],
					'js' => ['prism'],
				],
			],
			'feature-list' => [
				'cat' => 'general',
				'title' => __('Feature List', 'happy-addons-pro'),
				'icon' => 'hm hm-list-2',
				'is_pro' => true,
				'css' => ['feature-list'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'testimonial-carousel' => [
				'cat' => 'slider-&-carousel',
				'title' => __('Testimonial Carousel', 'happy-addons-pro'),
				'icon' => 'hm hm-testimonial-carousel',
				'is_pro' => true,
				'css' => ['testimonial-carousel'],
				'js' => [],
				'vendor' => [
					'css' => ['slick', 'slick-theme'],
					'js' => ['jquery-slick'],
				],
			],
			'advanced-tabs' => [
				'cat' => 'general',
				'title' => __('Advanced Tabs', 'happy-addons-pro'),
				'icon' => 'hm hm-tab',
				'is_pro' => true,
				'css' => ['advanced-tabs'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'flip-box' => [
				'cat' => 'creative',
				'title' => __('Flip Box', 'happy-addons-pro'),
				'icon' => 'hm hm-flip-card1',
				'is_pro' => true,
				'css' => ['flip-box'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'animated-text' => [
				'cat' => 'creative',
				'title' => __('Animated Text', 'happy-addons-pro'),
				'icon' => 'hm hm-text-animation',
				'is_pro' => true,
				'css' => ['animated-text'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => ['animated-text'],
				],
			],
			'timeline' => [
				'cat' => 'general',
				'title' => __('Timeline', 'happy-addons-pro'),
				'icon' => 'hm hm-timeline',
				'is_pro' => true,
				'css' => ['timeline'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => ['elementor-waypoints'],
				],
			],
			'instagram-feed' => [
				'cat' => 'social-media',
				'title' => __('Instagram Feed', 'happy-addons-pro'),
				'icon' => 'hm hm-instagram',
				'is_pro' => true,
				'css' => ['instagram-feed'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'scrolling-image' => [
				'cat' => 'creative',
				'title' => __('Scrolling Image', 'happy-addons-pro'),
				'icon' => 'hm hm-scrolling-image',
				'is_pro' => true,
				'css' => ['scrolling-image'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => ['jquery-keyframes'],
				],
			],
			'pricing-table' => [
				'cat' => 'marketing',
				'title' => __('Pricing Table', 'happy-addons-pro'),
				'icon' => 'hm hm-file-cabinet',
				'is_pro' => true,
				'css' => ['pricing-table'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'business-hour' => [
				'cat' => 'general',
				'title' => __('Business Hour', 'happy-addons-pro'),
				'icon' => 'hm hm-hand-watch',
				'is_pro' => true,
				'css' => ['business-hour'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'accordion' => [
				'cat' => 'general',
				'title' => __('Advanced Accordion', 'happy-addons-pro'),
				'icon' => 'hm hm-accordion-vertical',
				'is_pro' => true,
				'css' => ['accordion'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'toggle' => [
				'cat' => 'general',
				'title' => __('Advanced Toggle', 'happy-addons-pro'),
				'icon' => 'hm hm-accordion-vertical',
				'is_pro' => true,
				'css' => ['toggle'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'promo-box' => [
				'cat' => 'marketing',
				'title' => __('Promo Box', 'happy-addons-pro'),
				'icon' => 'hm hm-promo',
				'is_pro' => true,
				'css' => ['promo-box'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'hotspots' => [
				'cat' => 'creative',
				'title' => __('Hotspots', 'happy-addons-pro'),
				'icon' => 'hm hm-accordion-vertical',
				'is_pro' => true,
				'css' => ['hotspots'],
				'js' => [],
				'vendor' => [
					'css' => ['tipso'],
					'js' => ['jquery-tipso'],
				],
			],
			'price-menu' => [
				'cat' => 'marketing',
				'title' => __('Price Menu', 'happy-addons-pro'),
				'icon' => 'hm hm-menu-price',
				'is_pro' => true,
				'css' => ['price-menu'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'facebook-feed' => [
				'cat' => 'social-media',
				'title' => __('Facebook Feed', 'happy-addons-pro'),
				'icon' => 'hm hm-facebook',
				'is_pro' => true,
				'css' => ['facebook-feed'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'line-chart' => [
				'cat' => 'chart',
				'title' => __('Line Chart', 'happy-addons-pro'),
				'icon' => 'hm hm-line-graph-pointed',
				'is_pro' => true,
				'css' => [],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => ['chart-js'],
				],
			],
			'pie-chart' => [
				'cat' => 'chart',
				'title' => __('Pie & Doughnut Chart', 'happy-addons-pro'),
				'icon' => 'hm hm-graph-pie',
				'is_pro' => true,
				'css' => [],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => ['chart-js'],
				],
			],
			'polar-chart' => [
				'cat' => 'chart',
				'title' => __('Polar area Chart', 'happy-addons-pro'),
				'icon' => 'hm hm-graph-pie',
				'is_pro' => true,
				'css' => [],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => ['chart-js'],
				],
			],
			'radar-chart' => [
				'cat' => 'chart',
				'title' => __('Radar Chart', 'happy-addons-pro'),
				'icon' => 'hm hm-graph-pie',
				'is_pro' => true,
				'css' => [],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => ['chart-js'],
				],
			],
			'post-tiles' => [
				'cat' => 'post',
				'title' => __('Post Tiles', 'happy-addons-pro'),
				'icon' => 'hm hm-article',
				'is_pro' => true,
				'css' => ['post-tiles'],
				'js' => [],
				'vendor' => [
					'css' => [],
				],
			],
			'post-carousel' => [
				'cat' => 'post',
				'title' => __('Post Carousel', 'happy-addons-pro'),
				'icon' => 'hm hm-graph-pie',
				'is_pro' => true,
				'css' => ['post-carousel'],
				'js' => [],
				'vendor' => [
					'css' => ['slick', 'slick-theme'],
					'js' => ['jquery-slick'],
				],
			],
			'smart-post-list' => [
				'cat' => 'post',
				'title' => __('Smart Post List', 'happy-addons-pro'),
				'icon' => 'hm hm-post-list',
				'is_pro' => true,
				'css' => ['smart-post-list'],
				'js' => [],
				'vendor' => [
					'css' => ['nice-select'],
					'js' => ['jquery-nice-select'],
				],
			],
			'breadcrumbs' => [
				'cat' => 'general',
				'title' => __('Breadcrumbs', 'happy-addons-pro'),
				'icon' => 'hm hm-breadcrumbs',
				'is_pro' => true,
				'css' => ['breadcrumbs'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'twitter-carousel' => [
				'cat' => 'social-media',
				'title' => __('Twitter Feed Carousel', 'happy-addons-pro'),
				'icon' => 'hm hm-twitter',
				'is_pro' => true,
				'css' => ['twitter-carousel'],
				'js' => [],
				'vendor' => [
					'css' => ['slick', 'slick-theme'],
					'js' => ['jquery-slick'],
				],
			],
			'author-list' => [
				'cat' => 'post',
				'title' => __('Author List', 'happy-addons-pro'),
				'icon' => 'hm hm-user-male',
				'is_pro' => true,
				'css' => ['author-list'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			// 'post-grid' => [
			// 	'cat' => 'post',
			// 	'title' => __('Post Grid [deprecated]', 'happy-addons-pro'),
			// 	'icon' => 'hm hm-post-grid',
			// 	'is_pro' => true,
			// 	'css' => ['post-grid'],
			// 	'js' => [],
			// 	'vendor' => [
			// 		'css' => [],
			// 		'js' => [],
			// 	],
			// ],
			'post-grid-new' => [
				'cat' => 'post',
				'title' => __('Post Grid', 'happy-addons-pro'),
				'icon' => 'hm hm-post-grid',
				'is_pro' => true,
				'css' => ['post-grid'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'sticky-video' => [
				'cat' => 'general',
				'title' => __('Sticky Video', 'happy-addons-pro'),
				'icon' => 'hm hm-sticky-video',
				'is_pro' => true,
				'css' => ['sticky-video'],
				'js' => [],
				'vendor' => [
					'css' => ['plyr'],
					'js' => ['plyr'],
				],
			],
			// 'product-carousel' => [
			// 	'cat' => 'woocommerce',
			// 	'title' => __('Product Carousel [deprecated]', 'happy-addons-pro'),
			// 	'icon' => 'hm hm-Product-Carousel',
			// 	'is_pro' => true,
			// 	'css' => ['product-carousel', 'product-quick-view'],
			// 	'js' => [],
			// 	'vendor' => [
			// 		'css' => ['slick', 'slick-theme', 'magnific-popup'],
			// 		'js' => ['jquery-slick', 'jquery-magnific-popup'],
			// 	]
			// ],
			'product-carousel-new' => [
				'cat' => 'woocommerce',
				'title' => __('Product Carousel', 'happy-addons-pro'),
				'icon' => 'hm hm-Product-Carousel',
				'is_pro' => true,
				'css' => ['product-carousel', 'product-quick-view'],
				'js' => [],
				'vendor' => [
					'css' => ['slick', 'slick-theme', 'magnific-popup'],
					'js' => ['jquery-slick', 'jquery-magnific-popup'],
				]
			],
			// 'product-category-carousel' => [
			// 	'cat' => 'woocommerce',
			// 	'title' => __('Product Category Carousel [deprecated]', 'happy-addons-pro'),
			// 	'icon' => 'hm hm-Category-Carousel',
			// 	'is_pro' => true,
			// 	'css' => ['product-category-carousel'],
			// 	'js' => [],
			// 	'vendor' => [
			// 		'css' => ['slick', 'slick-theme'],
			// 		'js' => ['jquery-slick'],
			// 	],
			// ],
			'product-category-carousel-new' => [
				'cat' => 'woocommerce',
				'title' => __('Product Category Carousel', 'happy-addons-pro'),
				'icon' => 'hm hm-Category-Carousel',
				'is_pro' => true,
				'css' => ['product-category-carousel'],
				'js' => [],
				'vendor' => [
					'css' => ['slick', 'slick-theme'],
					'js' => ['jquery-slick'],
				],
			],
			// 'product-grid' => [
			// 	'cat' => 'woocommerce',
			// 	'title' => __('Product Grid [deprecated]', 'happy-addons-pro'),
			// 	'icon' => 'hm hm-product-grid',
			// 	'is_pro' => true,
			// 	'css' => ['product-grid', 'product-quick-view'],
			// 	'js' => [],
			// 	'vendor' => [
			// 		'css' => ['elementor-icons-fa-solid', 'magnific-popup'],
			// 		'js' => ['jquery-magnific-popup'],
			// 	],
			// ],
			'product-grid-new' => [
				'cat' => 'woocommerce',
				'title' => __('Product Grid', 'happy-addons-pro'),
				'icon' => 'hm hm-Product-Grid',
				'is_pro' => true,
				'css' => ['product-grid', 'product-quick-view'],
				'js' => [],
				'vendor' => [
					'css' => ['elementor-icons-fa-solid', 'magnific-popup'],
					'js' => ['jquery-magnific-popup'],
				],
			],
			// 'product-category-grid' => [
			// 	'cat' => 'woocommerce',
			// 	'title' => __('Product Category Grid [deprecated]', 'happy-addons-pro'),
			// 	'icon' => 'hm hm-Category-Carousel',
			// 	'is_pro' => true,
			// 	'css' => ['product-category-grid'],
			// 	'js' => [],
			// 	'vendor' => [
			// 		'css' => [],
			// 		'js' => [],
			// 	],
			// ],
			'product-category-grid-new' => [
				'cat' => 'woocommerce',
				'title' => __('Product Category Grid', 'happy-addons-pro'),
				'icon' => 'hm hm-Category-Carousel',
				'is_pro' => true,
				'css' => ['product-category-grid'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			// 'single-product' => [
			// 	'cat' => 'woocommerce',
			// 	'title' => __('Single Product [deprecated]', 'happy-addons-pro'),
			// 	'icon' => 'hm hm-Category-Carousel',
			// 	'is_pro' => true,
			// 	'css' => ['single-product', 'product-quick-view'],
			// 	'js' => [],
			// 	'vendor' => [
			// 		'css' => ['elementor-icons-fa-solid', 'magnific-popup'],
			// 		'js' => ['jquery-magnific-popup'],
			// 	],
			// ],
			'single-product-new' => [
				'cat' => 'woocommerce',
				'title' => __('Single Product', 'happy-addons-pro'),
				'icon' => 'hm hm-product-list-single',
				'is_pro' => true,
				'css' => ['single-product', 'product-quick-view'],
				'js' => [],
				'vendor' => [
					'css' => ['elementor-icons-fa-solid', 'magnific-popup'],
					'js' => ['jquery-magnific-popup'],
				],
			],
			'advanced-data-table' => [
				'cat' => 'general',
				'title' => __('Advanced Data Table', 'happy-addons-pro'),
				'icon' => 'hm hm-data-table',
				'is_pro' => true,
				'css' => ['advanced-data-table'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => ['data-table'],
				],
			],
			'modal-popup' => [
				'cat' => 'general',
				'title' => __('Modal Popup', 'happy-addons-pro'),
				'icon' => 'hm hm-popup',
				'is_pro' => true,
				'css' => ['modal-popup'],
				'js' => [],
				'vendor' => [
					'css' => ['animate-css'],
					'js' => [],
				],
			],
			'one-page-nav' => [
				'cat' => 'creative',
				'title' => __('One Page Nav', 'happy-addons-pro'),
				'icon' => 'hm hm-dot-navigation',
				'is_pro' => true,
				'css' => ['one-page-nav'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'advanced-slider' => [
				'cat' => 'slider-&-carousel',
				'title' => __('Advanced Slider', 'happy-addons-pro'),
				'icon' => 'hm hm-slider',
				'is_pro' => true,
				'css' => ['advanced-slider'],
				'js' => [],
				'vendor' => [
					'css' => ['ha-swiper'],
					'js' => ['ha-swiper'],
				],
			],
			'mini-cart' => [
				'cat' => 'woocommerce',
				'title' => __('Mini Cart', 'happy-addons-pro'),
				'icon' => 'hm hm-mini-cart',
				'is_pro' => true,
				'css' => ['mini-cart'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'wc-cart' => [
				'cat' => 'woocommerce',
				'title' => __('WooCommerce Cart', 'happy-addons-pro'),
				'icon' => 'hm hm-cart',
				'is_pro' => true,
				'css' => ['wc-cart'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'wc-checkout' => [
				'cat' => 'woocommerce',
				'title' => __('WooCommerce Checkout', 'happy-addons-pro'),
				'icon' => 'hm hm-checkout-2',
				'is_pro' => true,
				'css' => ['wc-checkout'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => ['wc-checkout'],
				],
			],
			'image-scroller' => [
				'cat' => 'creative',
				'title' => __('Single Image Scroll', 'happy-addons-pro'),
				'icon' => 'hm hm-image-scroll',
				'is_pro' => true,
				'css' => ['image-scroller'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'nav-menu' => [
				'cat' => 'general',
				'title' => __('Happy Menu', 'happy-addons-pro'),
				'icon' => 'hm hm-mega-menu',
				'is_pro' => true,
				'css' => ['nav-menu'],
				'js' => ['ha-nav-menu'],
				'vendor' => [
					'css' => [],
					'js' => [],
				]
			],
			'off-canvas' => [
				'cat' => 'creative',
				'title' => __('Off Canvas', 'happy-addons-pro'),
				'icon' => 'hm hm-offcanvas-menu',
				'is_pro' => true,
				'css' => ['off-canvas'],
				'js' => [],
				'vendor' => [
					'css' => ['hamburgers'],
					'js' => [],
				]
			],
			'unfold' => [
				'cat' => 'general',
				'title' => __( 'Unfold', 'happy-addons-pro' ),
				'icon' => 'hm hm-unfold-paper',
				'is_pro' => true,
				'css' => ['unfold'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				]
			],
			'edd-product-grid' => [
				'cat' => 'Easy Digital Downloads',
				'title' => __('EDD Product Grid', 'happy-addons-pro'),
				'icon' => 'hm hm-product-grid',
				'is_pro' => true,
				'css' => ['edd-product-grid', 'edd-quick-view'],
				'js' => [],
				'vendor' => [
					'css' => ['elementor-icons-fa-solid', 'magnific-popup'],
					'js' => ['jquery-magnific-popup'],
				],
			],
			'edd-product-carousel' => [
				'cat' => 'Easy Digital Downloads',
				'title' => __('EDD Product Carousel', 'happy-addons-pro'),
				'icon' => 'hm hm-Product-Carousel',
				'is_pro' => true,
				'css' => ['edd-product-carousel', 'edd-quick-view'],
				'js' => [],
				'vendor' => [
					'css' => ['slick', 'slick-theme', 'magnific-popup'],
					'js' => ['jquery-slick', 'jquery-magnific-popup'],
				]
			],
			'edd-single-product' => [
				'cat' => 'Easy Digital Downloads',
				'title' => __('EDD Single Product', 'happy-addons-pro'),
				'icon' => 'hm hm-Category-Carousel',
				'is_pro' => true,
				'css' => ['edd-single-product', 'edd-quick-view'],
				'js' => [],
				'vendor' => [
					'css' => ['elementor-icons-fa-solid', 'magnific-popup'],
					'js' => ['jquery-magnific-popup'],
				],
			],
			'edd-category-grid' => [
				'cat' => 'Easy Digital Downloads',
				'title' => __('EDD Category Grid', 'happy-addons-pro'),
				'icon' => 'hm hm-Category-Carousel',
				'is_pro' => true,
				'css' => ['product-category-grid'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'edd-category-carousel' => [
				'cat' => 'Easy Digital Downloads',
				'title' => __('EDD Category Carousel', 'happy-addons-pro'),
				'icon' => 'hm hm-Category-Carousel',
				'is_pro' => true,
				'css' => ['edd-category-carousel'],
				'js' => [],
				'vendor' => [
					'css' => ['slick', 'slick-theme'],
					'js' => ['jquery-slick'],
				],
			],
			'edd-cart' => [
				'cat' => 'easy-digital-downloads',
				'title' => __('EDD Cart', 'happy-addons-pro'),
				'icon' => 'hm hm-cart',
				'is_pro' => true,
				'css' => ['edd-cart'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'edd-checkout' => [
				'cat' => 'easy-digital-downloads',
				'title' => __('EDD Checkout', 'happy-addons-pro'),
				'icon' => 'hm hm-checkout-2',
				'is_pro' => true,
				'css' => ['edd-checkout'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'edd-login' => [
				'cat' => 'easy-digital-downloads',
				'title' => __('EDD Login', 'happy-addons-pro'),
				'icon' => 'hm hm-checkout-2',
				'is_pro' => true,
				'css' => ['edd-login'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'edd-register' => [
				'cat' => 'easy-digital-downloads',
				'title' => __('EDD Register', 'happy-addons-pro'),
				'icon' => 'hm hm-user-plus',
				'is_pro' => true,
				'css' => ['edd-register'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'edd-purchase' => [
				'cat' => 'easy-digital-downloads',
				'title' => __('EDD Purchase', 'happy-addons-pro'),
				'icon' => 'hm hm-user-plus',
				'is_pro' => true,
				'css' => ['edd-purchase'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'edd-download' => [
				'cat' => 'easy-digital-downloads',
				'title' => __('EDD Download', 'happy-addons-pro'),
				'icon' => 'hm hm-Download-circle',
				'is_pro' => true,
				'css' => ['edd-purchase'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'google-map' => [
				'cat' => 'general',
				'title' => __('Advanced Google Map', 'happy-addons-pro'),
				'icon' => 'hm hm-map-marker',
				'is_pro' => true,
				'css' => ['advanced-google-maps'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => ['ha-google-maps'],
				],
			],
			'image-swap' => [
				'cat' => 'general',
				'title' => __( 'Image Swap', 'happy-addons-pro' ),
				'icon' => 'hm hm-image-scroll',
				'is_pro' => true,
				'css' => ['image-swap'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				]
			],
			'shipping-bar' => [
				'cat' => 'woocommerce',
				'title' => __('Shipping Bar', 'happy-addons-pro'),
				'icon' => 'hm hm-shipping-address',
				'is_pro' => true,
				'css' => ['shipping-bar'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'remote-carousel' => [
				'cat' => 'slider-&-carousel',
				'title' => __('Remote Carousel', 'happy-addons-pro'),
				'icon' => 'hm hm-remote_carousel',
				'is_pro' => true,
				'css' => ['remote-carousel'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'table-of-contents' => [
				'cat' => 'general',
				'title' => __('Table of Contents', 'happy-addons-pro'),
				'icon' => 'hm hm-list-2',
				'is_pro' => true,
				'css' => ['table-of-contents'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => ['ha-toc'],
				],
			],
			'creative-slider' => [
				'cat' => 'slider-&-carousel',
				'title' => __('Creative Slider', 'happy-addons-pro'),
				'icon' => 'hm hm-slider',
				'is_pro' => true,
				'css' => ['creative-slider'],
				'js' => [],
				'vendor' => [
					'css' => ['owl-carousel', 'owl-theme-default', 'animate'],
					'js' => ['owl-carousel-js'],
				],
			],
		];

		return self::skin_widget_registration_decision($widget_map);
	}

	public static function skin_widget_registration_decision($widget_map) {

		$used_skin_widgets = [];

		$old_skin_widgets = [
			'post-grid' => [
				'cat' => 'post',
				'title' => __('Post Grid [deprecated]', 'happy-addons-pro'),
				'icon' => 'hm hm-post-grid',
				'is_pro' => true,
				'css' => ['post-grid'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'product-carousel' => [
				'cat' => 'woocommerce',
				'title' => __('Product Carousel [deprecated]', 'happy-addons-pro'),
				'icon' => 'hm hm-Product-Carousel',
				'is_pro' => true,
				'css' => ['product-carousel', 'product-quick-view'],
				'js' => [],
				'vendor' => [
					'css' => ['slick', 'slick-theme', 'magnific-popup'],
					'js' => ['jquery-slick', 'jquery-magnific-popup'],
				]
			],
			'product-category-carousel' => [
				'cat' => 'woocommerce',
				'title' => __('Product Category Carousel [deprecated]', 'happy-addons-pro'),
				'icon' => 'hm hm-Category-Carousel',
				'is_pro' => true,
				'css' => ['product-category-carousel'],
				'js' => [],
				'vendor' => [
					'css' => ['slick', 'slick-theme'],
					'js' => ['jquery-slick'],
				],
			],
			'product-grid' => [
				'cat' => 'woocommerce',
				'title' => __('Product Grid [deprecated]', 'happy-addons-pro'),
				'icon' => 'hm hm-Product-Grid',
				'is_pro' => true,
				'css' => ['product-grid', 'product-quick-view'],
				'js' => [],
				'vendor' => [
					'css' => ['elementor-icons-fa-solid', 'magnific-popup'],
					'js' => ['jquery-magnific-popup'],
				],
			],
			'product-category-grid' => [
				'cat' => 'woocommerce',
				'title' => __('Product Category Grid [deprecated]', 'happy-addons-pro'),
				'icon' => 'hm hm-Category-Carousel',
				'is_pro' => true,
				'css' => ['product-category-grid'],
				'js' => [],
				'vendor' => [
					'css' => [],
					'js' => [],
				],
			],
			'single-product' => [
				'cat' => 'woocommerce',
				'title' => __('Single Product [deprecated]', 'happy-addons-pro'),
				'icon' => 'hm hm-product-list-single',
				'is_pro' => true,
				'css' => ['single-product', 'product-quick-view'],
				'js' => [],
				'vendor' => [
					'css' => ['elementor-icons-fa-solid', 'magnific-popup'],
					'js' => ['jquery-magnific-popup'],
				],
			],

		];

		$cached_used_skin_widgets = get_option('hapro_used_skin_widgets', false);

		if ($cached_used_skin_widgets) {
			if (empty($cached_used_skin_widgets)) {
				return $widget_map;
			}else{
				$used_skin_widgets = $cached_used_skin_widgets;
			}
		} else {
			$used_skin_widgets = self::get_skin_usage_data($old_skin_widgets);
		}

		foreach ($old_skin_widgets as $widget_key => $widget_value) {
			if (in_array($widget_key, $used_skin_widgets)) {
				$widget_map[$widget_key] = $old_skin_widgets[$widget_key];
			}
		}

		return $widget_map;
	}

	public static function get_skin_usage_data($old_skin_widgets) {
		$used_skin_widgets = [];
		/** @var Module $module */
		$module = \Elementor\Modules\Usage\Module::instance();

		$format = 'raw';
		if (is_array($module->get_formatted_usage($format)) || is_object($module->get_formatted_usage($format))) {
			foreach ($module->get_formatted_usage($format) as $doc_type => $data) {

				if (is_array($data['elements']) || is_object($data['elements'])) {
					foreach ($data['elements'] as $element => $count) {

						$is_happy_widget = strpos($element, "ha-") !== false;
						$widget_key = str_replace('ha-', '', $element);

						if ($is_happy_widget && array_key_exists($widget_key, $old_skin_widgets)) {
							$used_skin_widgets[] = $widget_key;
						}
					}
				}
			}
		}

		update_option('hapro_used_skin_widgets', $used_skin_widgets);

		return $used_skin_widgets;
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
	public static function register( $widgets_manager = null ) {
		include_once(HAPPY_ADDONS_PRO_DIR_PATH . 'base/widget-base.php');
		include_once(HAPPY_ADDONS_PRO_DIR_PATH . 'traits/lazy-query-builder.php');

		$inactive_widgets = Free_Widgets_Manager::get_inactive_widgets();

		foreach (self::get_local_widgets_map() as $widget_key => $data) {
			if (!in_array($widget_key, $inactive_widgets)) {
				self::register_widget($widget_key, $widgets_manager);
			}
		}
	}

	protected static function register_widget( $widget_key, $widgets_manager = null ) {
		$widget_file = HAPPY_ADDONS_PRO_DIR_PATH . 'widgets/' . $widget_key . '/widget.php';
		if (is_readable($widget_file)) {
			include_once($widget_file);
			$widget_class = '\Happy_Addons_Pro\Widget\\' . str_replace('-', '_', $widget_key);
			if (class_exists($widget_class)) {
				$widgets_manager->register( new $widget_class() );
			}
		}
	}
}

Widgets_Manager::init();
