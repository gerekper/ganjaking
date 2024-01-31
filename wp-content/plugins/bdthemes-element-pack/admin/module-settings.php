<?php

namespace ElementPack\Admin;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

if (!function_exists('is_plugin_active')) {
	include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

class ModuleService {

	public static function get_widget_settings($callable) {

		$settings_fields                                      = [
			'element_pack_active_modules'   => [
				[
					'name'         => 'accordion',
					'label'        => esc_html__('Accordion', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'free',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/accordion/',
					'video_url'    => 'https://youtu.be/DP3XNV1FEk0',
				],
				[
					'name'         => 'advanced-button',
					'label'        => esc_html__('Advanced Button', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/advanced-button/',
					'video_url'    => 'https://youtu.be/Lq_st2IWZiE',
				],
				[
					'name'         => 'advanced-calculator',
					'label'        => esc_html__('Advanced Calculator', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/advanced-calculator/',
					'video_url'    => 'https://youtu.be/vw28HW6duXE',
				],
				[
					'name'         => 'advanced-counter',
					'label'        => esc_html__('Advanced Counter', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/advanced-counter/',
					'video_url'    => 'https://youtu.be/Ydok6ImEQvE',
				],
				[
					'name'         => 'advanced-divider',
					'label'        => esc_html__('Advanced Divider', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/advanced-divider/',
					'video_url'    => 'https://youtu.be/HbtNHQJm3m0',
				],
				[
					'name'         => 'advanced-gmap',
					'label'        => esc_html__('Advanced Google Map', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/advanced-google-map/',
					'video_url'    => 'https://youtu.be/qaZ-hv6UPDY',
				],
				[
					'name'         => 'advanced-heading',
					'label'        => esc_html__('Advanced Heading', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/advanced-heading/',
					'video_url'    => 'https://youtu.be/E1jYInKYTR0',
				],
				[
					'name'         => 'advanced-icon-box',
					'label'        => esc_html__('Advanced Icon Box', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/advanced-icon-box/',
					'video_url'    => 'https://youtu.be/IU4s5Cc6CUA',
				],
				[
					'name'         => 'advanced-image-gallery',
					'label'        => esc_html__('Advanced Image Gallery', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'custom gallery',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/advanced-image-gallery/',
					'video_url'    => 'https://youtu.be/se7BovYbDok',
				],
				[
					'name'         => 'advanced-progress-bar',
					'label'        => esc_html__('Advanced Progress Bar', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/advanced-progress-bar/',
					'video_url'    => 'https://youtu.be/7hnmMdd2-Yo',
				],
				[
					'name'         => 'age-gate',
					'label'        => esc_html__('Age Gate', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/age-gate/',
					'video_url'    => 'https://youtu.be/I32wKLfNIes',
				],
				[
					'name'         => 'air-pollution',
					'label'        => esc_html__('Air Pollution', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/air-pollution/',
					'video_url'    => 'https://youtu.be/m38ddVi52-Q',
				],
				[
					'name'         => 'animated-card',
					'label'        => esc_html__('Animated Card', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/animated-card/',
					'video_url'    => 'https://youtu.be/gfXpQ-dTr9g',
				],
				[
					'name'         => 'animated-heading',
					'label'        => esc_html__('Animated Heading', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/animated-heading/',
					'video_url'    => 'https://youtu.be/xypAmQodUYA',
				],
				[
					'name'         => 'animated-link',
					'label'        => esc_html__('Animated Link', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/animated-link/',
					'video_url'    => 'https://youtu.be/qs0gEVh0x7w',
				],
				[
					'name'         => 'audio-player',
					'label'        => esc_html__('Audio Player', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/audio-player/',
					'video_url'    => 'https://youtu.be/VHAEO1xLVxU',
				],
				[
					'name'         => 'barcode',
					'label'        => esc_html__('Barcode', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/barcode',
					'video_url'    => 'https://youtu.be/PWxNP2zLqDg',
				],
				[
					'name'         => 'brand-grid',
					'label'        => esc_html__('Brand Grid', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/brand-grid',
					'video_url'    => 'https://youtu.be/a_wJL950Kz4',
				],
				[
					'name'         => 'brand-carousel',
					'label'        => esc_html__('Brand Carousel', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/brand-carousel',
					'video_url'    => 'https://youtu.be/LdCxFzpYuO0',
				],
				[
					'name'         => 'breadcrumbs',
					'label'        => esc_html__('Breadcrumbs', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/breadcrumbs',
					'video_url'    => 'https://youtu.be/32yrjPHq-AA',
				],
				[
					'name'         => 'business-hours',
					'label'        => esc_html__('Business Hours', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/business-hours',
					'video_url'    => 'https://youtu.be/1QfZ-os75rQ',
				],
				[
					'name'         => 'dual-button',
					'label'        => esc_html__('Dual Button', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/dual-button/',
					'video_url'    => 'https://youtu.be/7hWWqHEr6s8',
				],
				[
					'name'         => 'chart',
					'label'        => esc_html__('Chart', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/charts',
					'video_url'    => 'https://youtu.be/-1WVTzTyti0',

				],
				[
					'name'         => 'calendly',
					'label'        => esc_html__('Calendly', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'free',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/calendly/',
					'video_url'    => 'https://youtu.be/nl4zC46SrhY',
				],
				[
					'name'         => 'call-out',
					'label'        => esc_html__('Call Out', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'free',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/call-out/',
					'video_url'    => 'https://youtu.be/1tNppRHvSvQ',

				],
				[
					'name'         => 'carousel',
					'label'        => esc_html__('Carousel', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'post carousel',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/carousel',
					'video_url'    => 'https://youtu.be/biF3GtBf0qc',

				],
				[
					'name'         => 'changelog',
					'label'        => esc_html__('Changelog', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/changelog',
					'video_url'    => 'https://youtu.be/835Fsi2jGRI',

				],
				[
					'name'         => 'circle-menu',
					'label'        => esc_html__('Circle Menu', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/circle-menu/',
					'video_url'    => 'https://www.youtube.com/watch?v=rfW22T-U7Ag',

				],
				[
					'name'         => 'comparison-list',
					'label'        => esc_html__('Comparison List', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom new',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/comparison-list/',
					'video_url'    => 'https://youtu.be/7XvSgvbJM74',
				],
				[
					'name'         => 'circle-info',
					'label'        => esc_html__('Circle Info', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/circle-info/',
					'video_url'    => 'https://youtu.be/PIQ6BJtNpNU',

				],
				[
					'name'         => 'content-switcher',
					'label'        => esc_html__('Content Switcher', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom new',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/content-switcher/',
					'video_url'    => 'https://youtu.be/4NjUGf9EY0U',
				],
				[
					'name'         => 'cookie-consent',
					'label'        => esc_html__('Cookie Consent', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'free',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/cookie-consent/',
					'video_url'    => 'https://youtu.be/BR4t5ngDzqM',

				],
				[
					'name'         => 'countdown',
					'label'        => esc_html__('Countdown', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'free',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/event-calendar-countdown',
					'video_url'    => 'https://youtu.be/oxqHEDyzvIM',

				],
				[
					'name'         => 'contact-form',
					'label'        => esc_html__('Simple Contact Form', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'free',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/simple-contact-form/',
					'video_url'    => 'https://youtu.be/faIeyW7LOJ8',

				],
				[
					'name'         => 'comment',
					'label'        => esc_html__('Comment', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/comment/',
					'video_url'    => 'https://youtu.be/csvMTyUx7Hs',
				],
				[
					'name'         => 'custom-gallery',
					'label'        => esc_html__('Custom Gallery', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'free',
					'content_type' => 'custom gallery',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/custom-gallery/',
					'video_url'    => 'https://youtu.be/2fAF8Rt7FbQ',

				],
				[
					'name'         => 'custom-carousel',
					'label'        => esc_html__('Custom Carousel', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'custom carousel',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/custom-carousel/',
					'video_url'    => 'https://youtu.be/TMwdfYDmTQo',

				],
				[
					'name'         => 'creative-button',
					'label'        => esc_html__('Creative Button', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/creative-button/',
					'video_url'    => 'https://youtu.be/6f2t-79MfnU',
				],
				[
					'name'         => 'crypto-currency-card',
					'label'        => esc_html__('Crypto Currency Card', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/crypto-currency-card/',
					'video_url'    => 'https://youtu.be/F13YPkFkLso',
				],
				[
					'name'         => 'crypto-currency-table',
					'label'        => esc_html__('Crypto Currency Table', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/crypto-currency-table/',
					'video_url'    => 'https://youtu.be/F13YPkFkLso',
				],
				[
					'name'         => 'crypto-currency-grid',
					'label'        => esc_html__('Crypto Currency Grid', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/crypto-currency-grid/',
					'video_url'    => '',
				],
				[
					'name'         => 'crypto-currency-carousel',
					'label'        => esc_html__('Crypto Currency Carousel', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/crypto-currency-carousel/',
					'video_url'    => '',
				],
				[
					'name'         => 'crypto-currency-ticker',
					'label'        => esc_html__('Crypto Currency Ticker', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/crypto-currency-ticker/',
					'video_url'    => '',
				],
				[
					'name'         => 'crypto-currency-chart',
					'label'        => esc_html__('Crypto Currency Chart', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/crypto-currency-chart/',
					'video_url'    => '',
				],
				[
					'name'         => 'crypto-currency-chart-carousel',
					'label'        => esc_html__('Crypto Currency Chart Carousel', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/crypto-currency-chart/',
					'video_url'    => '',
				],
				[
					'name'         => 'crypto-currency-list',
					'label'        => esc_html__('Crypto Currency list', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/crypto-currency-list/',
					'video_url'    => '',
				],
				[
					'name'         => 'coupon-code',
					'label'        => esc_html__('Coupon Code', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/coupon-code/',
					'video_url'    => 'https://youtu.be/xru1Xu3ISZ0',

				],
				[
					'name'         => 'dark-mode',
					'label'        => esc_html__('Dark Mode', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/dark-mode',
					'video_url'    => 'https://youtu.be/nuYa-0sWFxU',

				],

				[
					'name'         => 'document-viewer',
					'label'        => esc_html__('Document Viewer', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/document-viewer',
					'video_url'    => 'https://www.youtube.com/watch?v=8Ar9NQe93vg',

				],

				[
					'name'         => 'device-slider',
					'label'        => esc_html__('Device Slider', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'custom slider',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/device-slider/',
					'video_url'    => 'https://youtu.be/GACXtqun5Og',

				],
				[
					'name'         => 'dropbar',
					'label'        => esc_html__('Dropbar', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'free',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/dropbar/',
					'video_url'    => 'https://youtu.be/cXMq8nOCdqk',

				],
				[
					'name'         => 'dynamic-grid',
					'label'        => esc_html__('Dynamic Grid', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/dynamic-grid/',
					'video_url'    => 'https://youtu.be/3H6eSrLkse4',

				],
				[
					'name'         => 'dynamic-carousel',
					'label'        => esc_html__('Dynamic Carousel', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/dynamic-carousel/',
					'video_url'    => 'https://youtu.be/0j1KGXujc78',

				],
				[
					'name'         => 'facebook-feed',
					'label'        => esc_html__('Facebook Feed', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/facebook-feed/',
					'video_url'    => 'https://youtu.be/iNUl6q2yRDU',

				],
				[
					'name'         => 'facebook-feed-carousel',
					'label'        => esc_html__('Facebook Feed Carousel', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/facebook-feed-carousel/',
					'video_url'    => 'https://youtu.be/wMumsINLfUA',

				],
				[
					'name'         => 'fancy-card',
					'label'        => esc_html__('Fancy Card', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/fancy-card/',
					'video_url'    => 'https://youtu.be/BXdVB1pLfXE',

				],
				[
					'name'         => 'fancy-list',
					'label'        => esc_html__('Fancy List', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/fancy-list/',
					'video_url'    => 'https://youtu.be/t1_5uys8bto',

				],
				[
					'name'         => 'fancy-icons',
					'label'        => esc_html__('Fancy Icons', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/fancy-icons/',
					'video_url'    => 'https://youtu.be/Y4NoiuW2yBM',

				],
				[
					'name'         => 'fancy-slider',
					'label'        => esc_html__('Fancy Slider', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/fancy-slider/',
					'video_url'    => 'https://youtu.be/UGBnjbp90eA',

				],
				[
					'name'         => 'fancy-tabs',
					'label'        => esc_html__('Fancy Tabs', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/fancy-tabs/',
					'video_url'    => 'https://youtu.be/wBTRSjofce4',

				],
				[
					'name'         => 'flip-box',
					'label'        => esc_html__('Flip Box', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/flip-box/',
					'video_url'    => 'https://youtu.be/FLmKzk9KbQg',

				],
				[
					'name'         => 'floating-knowledgebase',
					'label'        => esc_html__('Floating Knowledgebase', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'post new',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/floating-knowledgebase/',
					'video_url'    => 'https://youtu.be/02xNh5syhZ0',

				],
				[
					'name'         => 'featured-box',
					'label'        => esc_html__('Featured Box', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/featured-box/',
					'video_url'    => 'https://youtu.be/Qe4yYXajhQg',

				],
				[
					'name'         => 'google-reviews',
					'label'        => esc_html__('Google Reviews', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'slider',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/google-reviews/',
					'video_url'    => 'https://youtu.be/pp0mQpyKqfs',

				],
				[
					'name'         => 'helpdesk',
					'label'        => esc_html__('Help Desk', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/help-desk/',
					'video_url'    => 'https://youtu.be/bO__skhy4yk',

				],
				[
					'name'         => 'hover-box',
					'label'        => esc_html__('Hover Box', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/hover-box/',
					'video_url'    => 'https://youtu.be/lWdF9-SV-2I',

				],
				[
					'name'         => 'hover-video',
					'label'        => esc_html__('Hover Video', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/hover-video/',
					'video_url'    => 'https://youtu.be/RgoWlIm5KOo',

				],
				[
					'name'         => 'honeycombs',
					'label'        => esc_html__('Honeycombs', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/honeycombs/',
					'video_url'    => 'https://youtu.be/iTWXzc329vQ',

				],
				[
					'name'         => 'horizontal-scroller',
					'label'        => esc_html__('Horizontal Scroller', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/horizontal-scroller/',
					'video_url'    => 'https://youtu.be/x6vpXQt6__k',

				],
				[
					'name'         => 'icon-mobile-menu',
					'label'        => esc_html__('Icon Mobile Menu', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'menu',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/icon-mobile-menu/',
					'video_url'    => 'https://youtu.be/lJxkFDzrDeY',
				],
				[
					'name'         => 'iconnav',
					'label'        => esc_html__('Icon Nav', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/icon-nav/',
					'video_url'    => 'https://youtu.be/Q4YY8pf--ig',
				],
				[
					'name'         => 'iframe',
					'label'        => esc_html__('Iframe', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/iframe/',
					'video_url'    => 'https://youtu.be/wQPgsmrxZHM',
				],
				[
					'name'         => 'instagram',
					'label'        => esc_html__('Instagram', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others carousel',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/instagram-feed/',
					'video_url'    => 'https://youtu.be/uj9WpuFIZb8',
				],
				[
					'name'         => 'image-accordion',
					'label'        => esc_html__('Image Accordion', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/image-accordion/',
					'video_url'    => 'https://youtu.be/jQWU4kxXJpM',
				],
				[
					'name'         => 'image-compare',
					'label'        => esc_html__('Image Compare', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'free',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/image-compare/',
					'video_url'    => 'https://youtu.be/-Kwjlg0Fwk0',
				],
				[
					'name'         => 'image-expand',
					'label'        => esc_html__('Image Expand', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/image-expand/',
					'video_url'    => 'https://youtu.be/gNg7vpypycY',
				],
				[
					'name'         => 'image-stack',
					'label'        => esc_html__('Image Stack', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/image-stack/',
					'video_url'    => 'https://youtu.be/maLIlug2RwM',
				],
				[
					'name'         => 'image-magnifier',
					'label'        => esc_html__('Image Magnifier', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'free',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/image-magnifier/',
					'video_url'    => 'https://youtu.be/GSy3pLihNPY',
				],
				[
					'name'         => 'interactive-card',
					'label'        => esc_html__('Interactive Card', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/interactive-card/',
					'video_url'    => 'https://youtu.be/r8IXJUD3PA4',
				],
				[
					'name'         => 'interactive-tabs',
					'label'        => esc_html__('Interactive Tabs', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/interactive-tabs/',
					'video_url'    => 'https://youtu.be/O3VFyW0G6_Q',
				],
				[
					'name'         => 'lightbox',
					'label'        => esc_html__('Lightbox', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'free',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/lightbox/',
					'video_url'    => 'https://youtu.be/1iKQD4HfZG4',
				],
				[
					'name'         => 'lottie-image',
					'label'        => esc_html__('Lottie Image', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/lottie-image/',
					'video_url'    => 'https://youtu.be/CbODBtLTxWc',
				],
				[
					'name'         => 'lottie-icon-box',
					'label'        => esc_html__('Lottie Icon Box', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/lottie-icon-box/',
					'video_url'    => 'https://youtu.be/1jKFSglW6qE',
				],
				[
					'name'         => 'logo-grid',
					'label'        => esc_html__('Logo Grid', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/logo-grid/',
					'video_url'    => 'https://youtu.be/Go1YE3O23J4',
				],
				[
					'name'         => 'logo-carousel',
					'label'        => esc_html__('Logo Carousel', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/logo-carousel/',
					'video_url'    => 'https://youtu.be/xe_SA0ZgAvA',
				],
				[
					'name'         => 'mega-menu',
					'label'        => esc_html__('Mega Menu', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/mega-menu/',
					'video_url'    => 'https://youtu.be/ZOBLWIZvGLs',
				],
				[
					'name'         => 'marquee',
					'label'        => esc_html__('Marquee', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/marquee/',
					'video_url'    => 'https://youtu.be/3Dnxt9V0mzc',
				],
				[
					'name'         => 'modal',
					'label'        => esc_html__('Modal', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/modal/',
					'video_url'    => 'https://youtu.be/4qRa-eYDGZU',
				],
				[
					'name'         => 'mailchimp',
					'label'        => esc_html__('Mailchimp', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/mailchimp/',
					'video_url'    => 'https://youtu.be/hClaXvxvkXM',
				],
				[
					'name'         => 'marker',
					'label'        => esc_html__('Marker', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/marker/',
					'video_url'    => 'https://youtu.be/1iKQD4HfZG4',
				],
				[
					'name'         => 'member',
					'label'        => esc_html__('Member', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'free',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/member/',
					'video_url'    => 'https://youtu.be/m8_KOHzssPA',
				],
				[
					'name'         => 'navbar',
					'label'        => esc_html__('Navbar', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'free',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/navbar/',
					'video_url'    => 'https://youtu.be/ZXdDAi9tCxE',
				],
				[
					'name'         => 'news-ticker',
					'label'        => esc_html__('News Ticker', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'post',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/news-ticker',
					'video_url'    => 'https://youtu.be/FmpFhNTR7uY',
				],
				[
					'name'         => 'notification',
					'label'        => esc_html__('Notification', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/notification',
					'video_url'    => 'https://youtu.be/eI4UG1NYAYk',
				],
				[
					'name'         => 'offcanvas',
					'label'        => esc_html__('Offcanvas', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/offcanvas/',
					'video_url'    => 'https://youtu.be/CrrlirVfmQE',
				],
				[
					'name'         => 'open-street-map',
					'label'        => esc_html__('Open Street Map', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'free',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/open-street-map',
					'video_url'    => 'https://youtu.be/DCQ5g7yleyk',
				],
				[
					'name'         => 'price-list',
					'label'        => esc_html__('Price List', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'free',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/price-list/',
					'video_url'    => 'https://youtu.be/QsXkIYwfXt4',
				],
				[
					'name'         => 'price-table',
					'label'        => esc_html__('Price Table', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/pricing-table',
					'video_url'    => 'https://youtu.be/D8_inzgdvyg',
				],
				[
					'name'         => 'product-grid',
					'label'        => esc_html__('Product Grid', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/product-grid',
					'video_url'    => 'https://youtu.be/-UJhU-ak5_k',
				],
				[
					'name'         => 'product-carousel',
					'label'        => esc_html__('Product Carousel', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'carousel',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/product-carousel',
					'video_url'    => 'https://youtu.be/ZFpkJIctXic',
				],
				[
					'name'         => 'panel-slider',
					'label'        => esc_html__('Panel Slider', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'free',
					'content_type' => 'custom slider',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/panel-slider/',
					'video_url'    => 'https://youtu.be/_piVTeJd0g4',
				],
				[
					'name'         => 'post-slider',
					'label'        => esc_html__('Post Slider', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'post slider',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/post-slider',
					'video_url'    => 'https://youtu.be/oPYzWVLPF7A',
				],
				[
					'name'         => 'post-card',
					'label'        => esc_html__('Post Card', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'post',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/post-card/',
					'video_url'    => 'https://youtu.be/VKtQCjnEJvE',
				],
				[
					'name'         => 'post-block',
					'label'        => esc_html__('Post Block', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'post',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/post-block/',
					'video_url'    => 'https://youtu.be/bFEyizMaPmw',
				],
				[
					'name'         => 'post-block-modern',
					'label'        => esc_html__('Post Block Modern', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'post',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/post-block/',
					'video_url'    => 'https://youtu.be/bFEyizMaPmw',
				],
				[
					'name'         => 'progress-pie',
					'label'        => esc_html__('Progress Pie', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'free',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/progress-pie/',
					'video_url'    => 'https://youtu.be/c5ap86jbCeg',
				],
				[
					'name'         => 'post-gallery',
					'label'        => esc_html__('Post Gallery', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'post gallery',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/post-gallery',
					'video_url'    => 'https://youtu.be/iScykjTKlNA',
				],
				[
					'name'         => 'post-grid',
					'label'        => esc_html__('Post Grid', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'post',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/post%20grid/',
					'video_url'    => 'https://youtu.be/z3gWwPIsCkg',
				],
				[
					'name'         => 'post-grid-tab',
					'label'        => esc_html__('Post Grid Tab', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'post',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/post-grid-tab',
					'video_url'    => 'https://youtu.be/kFEL4AGnIv4',
				],
				[
					'name'         => 'post-list',
					'label'        => esc_html__('Post List', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'post',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/post-list/',
					'video_url'    => 'https://youtu.be/5aQTAsLRF0o',
				],
				[
					'name'         => 'profile-card',
					'label'        => esc_html__('Profile Card', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'post',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/profile-card/',
					'video_url'    => 'https://youtu.be/Slnx_mxDBqo',
				],
				[
					'name'         => 'protected-content',
					'label'        => esc_html__('Protected Content', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/protected-content/',
					'video_url'    => 'https://youtu.be/jcLWace-JpE',

				],
				[
					'name'         => 'qrcode',
					'label'        => esc_html__('QR Code', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/qr-code/',
					'video_url'    => 'https://youtu.be/3ofLAjpnmO8',
				],
				[
					'name'         => 'reading-progress',
					'label'        => esc_html__('Reading Progress', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/reading-progress/',
					'video_url'    => 'https://youtu.be/cODL1E2f9FI',
				],
				[
					'name'         => 'reading-timer',
					'label'        => esc_html__('Reading Timer', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'others new',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/reading-timer/',
					'video_url'    => 'https://youtu.be/7lRyOmR6yqo?si=iuO-Ax-6wNmkmSuM',
				],
				[
					'name'         => 'remote-arrows',
					'label'        => esc_html__('Remote Arrows', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/remote-arrows/',
					'video_url'    => 'https://youtu.be/w0CEROpvjjA',
				],
				[
					'name'         => 'remote-fraction',
					'label'        => esc_html__('Remote Fraction', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/remote-fraction/',
					'video_url'    => 'https://youtu.be/UfmwcTjX7L8',
				],
				[
					'name'         => 'remote-pagination',
					'label'        => esc_html__('Remote Pagination', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/remote-pagination/',
					'video_url'    => 'https://youtu.be/eZWSkb7HeUA',
				],
				[
					'name'         => 'remote-thumbs',
					'label'        => esc_html__('Remote Thumbs', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/remote-thumbs/',
					'video_url'    => 'https://youtu.be/PKKnqB0vhzE',
				],
				[
					'name'         => 'review-card',
					'label'        => esc_html__('Review Card', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/review-card/',
					'video_url'    => 'https://youtu.be/xFtjeR1qgSE',
				],
				[
					'name'         => 'review-card-grid',
					'label'        => esc_html__('Review Card Grid', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/review-card-grid/',
					'video_url'    => 'https://youtu.be/hIKLXU9Rh-8',
				],
				[
					'name'         => 'review-card-carousel',
					'label'        => esc_html__('Review Card Carousel', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'custom carousel',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/review-card-carousel/',
					'video_url'    => 'https://youtu.be/7kMyajVai6E',
				],
				[
					'name'         => 'slider',
					'label'        => esc_html__('Slider', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'free',
					'content_type' => 'custom slider',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/layer-slider/',
					'video_url'    => 'https://youtu.be/SI4K4zuNOoE',
				],
				[
					'name'         => 'slideshow',
					'label'        => esc_html__('Slideshow', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/slideshow/',
					'video_url'    => 'https://youtu.be/BrrKmDfJ5ZI',
				],
				[
					'name'         => 'slinky-vertical-menu',
					'label'        => esc_html__('Slinky Vertical Menu', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/slinky-vertical-menu/',
					'video_url'    => 'https://youtu.be/5RE9w-JqKwk',
				],
				[
					'name'        => 'scrollnav',
					'label'       => esc_html__('Scrollnav', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'on',
					'widget_type' => 'pro',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/scrollnav/',
					'video_url'   => 'https://youtu.be/P3DfE53_w5I',
				],
				[
					'name'         => 'search',
					'label'        => esc_html__('Search', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'free',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/search/',
					'video_url'    => 'https://youtu.be/H3F1LHc97Gk',
				],
				[
					'name'         => 'scroll-button',
					'label'        => esc_html__('Scroll Button', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'free',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/search/',
					'video_url'    => 'https://youtu.be/y8LJCO3tQqk',
				],
				[
					'name'         => 'scroll-image',
					'label'        => esc_html__('Scroll Image', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/scroll-image',
					'video_url'    => 'https://youtu.be/UpmtN1GsJkQ',
				],
				[
					'name'         => 'source-code',
					'label'        => esc_html__('Source Code', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/source-code',
					'video_url'    => 'https://youtu.be/vnqpD9aAmzg',
				],
				[
					'name'         => 'stacker',
					'label'        => esc_html__('Stacker', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom new',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/stacker',
					'video_url'    => 'https://youtu.be/fZSTyJc5W7E?si=GkkUhdv9aXPTlVxS',
				],
				[
					'name'         => 'static-carousel',
					'label'        => esc_html__('Static Carousel', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'custom carousel',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/static-carousel',
					'video_url'    => 'https://youtu.be/8A2a8ws6364',
				],
				[
					'name'         => 'static-grid-tab',
					'label'        => esc_html__('Static Grid Tab', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/static-grid-tab',
					'video_url'    => 'https://www.youtube.com/watch?v=HIvQX9eLWU8',
				],
				[
					'name'         => 'single-post',
					'label'        => esc_html__('Single Post', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'post',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/single-post',
					'video_url'    => 'https://youtu.be/32g-F4_Avp4',
				],
				[
					'name'         => 'social-share',
					'label'        => esc_html__('Social Share', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/social-share/',
					'video_url'    => 'https://youtu.be/3OPYfeVfcb8',
				],
				[
					'name'         => 'social-proof',
					'label'        => esc_html__('Social Proof', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/social-proof/',
					'video_url'    => 'https://youtu.be/jpIX4VHzSxA',
				],
				[
					'name'         => 'step-flow',
					'label'        => esc_html__('Step Flow', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/step-flow/',
					'video_url'    => 'https://youtu.be/YNjbt-5GO4k',
				],
				[
					'name'         => 'sub-menu',
					'label'        => esc_html__('Sub Menu', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/sub-menu/',
					'video_url'    => 'https://youtu.be/YuwB964kQMw',
				],
				[
					'name'         => 'switcher',
					'label'        => esc_html__('Switcher', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/switcher/',
					'video_url'    => 'https://youtu.be/BIEFRxDF1UE',
				],
				[
					'name'         => 'svg-blob',
					'label'        => esc_html__('SVG Blob', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/svg-blob/',
					'video_url'    => 'https://youtu.be/sgyUOC7TXPA',
				],
				[
					'name'         => 'svg-image',
					'label'        => esc_html__('SVG Image', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/svg-image/',
					'video_url'    => 'https://youtu.be/XRbjpcp5dJ0',

				],
				[
					'name'         => 'svg-maps',
					'label'        => esc_html__('SVG Maps', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/svg-maps/',
					'video_url'    => 'https://youtu.be/07WomY1e9-U',
				],
				[
					'name'         => 'tabs',
					'label'        => esc_html__('Tabs', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/tabs/',
					'video_url'    => 'https://youtu.be/1BmS_8VpBF4',
				],
				[
					'name'         => 'table',
					'label'        => esc_html__('Table', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/table/',
					'video_url'    => 'https://youtu.be/dviKkEPsg04',
				],
				[
					'name'         => 'table-of-content',
					'label'        => esc_html__('Table Of Content', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'post',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/table-of-content-test-post/',
					'video_url'    => 'https://youtu.be/DbPrqUD8cOY',
				],
				[
					'name'         => 'tags-cloud',
					'label'        => esc_html__('Tags Cloud', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/tags-cloud/',
					'video_url'    => 'https://youtu.be/LW_WFs9gybU',
				],
				[
					'name'         => 'timeline',
					'label'        => esc_html__('Timeline', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'post',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/timeline/',
					'video_url'    => 'https://youtu.be/lp4Zqn6niXU',
				],
				[
					'name'         => 'time-zone',
					'label'        => esc_html__('Time Zone', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/time-zone/',
					'video_url'    => 'https://youtu.be/WOMIk_FVRz4',
				],
				[
					'name'         => 'total-count',
					'label'        => esc_html__('Total Count', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/total-count/',
					'video_url'    => 'https://youtu.be/1KgG9vTrY8I',
				],
				[
					'name'         => 'trailer-box',
					'label'        => esc_html__('Trailer Box', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'free',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/trailer-box/',
					'video_url'    => 'https://youtu.be/3AR5RlBAAYg',
				],
				[
					'name'         => 'thumb-gallery',
					'label'        => esc_html__('Thumb Gallery', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'post gallery',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/thumb-gallery/',
					'video_url'    => 'https://youtu.be/NJ5ZR-9ODus',
				],
				[
					'name'         => 'toggle',
					'label'        => esc_html__('Read More Toggle', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'free',
					'content_type' => 'custom',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/toggle/',
					'video_url'    => 'https://youtu.be/7_jk_NvbKls',
				],
				[
					'name'         => 'twitter-carousel',
					'label'        => esc_html__('Twitter Carousel', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others carousel',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/twitter-carousel/',
					'video_url'    => 'https://youtu.be/eeyR1YtUFZw',
				],
				[
					'name'         => 'twitter-grid',
					'label'        => esc_html__('Twitter Grid', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'free',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/twitter-grid/',
					'video_url'    => 'https://youtu.be/cYqDPiDpsEY',
				],
				[
					'name'         => 'twitter-slider',
					'label'        => esc_html__('Twitter Slider', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others slider',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/twitter-slider',
					'video_url'    => 'https://youtu.be/Bd3I7ipqMms',
				],
				[
					'name'         => 'threesixty-product-viewer',
					'label'        => esc_html__('360 Product Viewer', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/360-product-viewer/',
					'video_url'    => 'https://youtu.be/60Q4sK-FzLI',
				],
				[
					'name'         => 'user-login',
					'label'        => esc_html__('User Login', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/user-login/',
					'video_url'    => 'https://youtu.be/JLdKfv_-R6c',
				],
				[
					'name'         => 'user-register',
					'label'        => esc_html__('User Register', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'on',
					'widget_type'  => 'free',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/user-register/',
					'video_url'    => 'https://youtu.be/hTjZ1meIXSY',
				],
				[
					'name'         => 'vertical-menu',
					'label'        => esc_html__('Vertical Menu', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/vertical-menu/',
					'video_url'    => 'https://youtu.be/ezZBOistuF4',
				],
				[
					'name'         => 'video-gallery',
					'label'        => esc_html__('Video Gallery', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'custom gallery',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/video-gallery/',
					'video_url'    => 'https://youtu.be/wbkou6p7l3s',
				],
				[
					'name'         => 'video-player',
					'label'        => esc_html__('Video Player', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/video-player/',
					'video_url'    => 'https://youtu.be/ksy2uZ5Hg3M',
				],
				[
					'name'         => 'weather',
					'label'        => esc_html__('Weather', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/weather/',
					'video_url'    => 'https://youtu.be/Vjyl4AAAufg',
				],
				[
					'name'         => 'webhook-form',
					'label'        => esc_html__('Webhook Form', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'others new',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/webhook-form/',
					'video_url'    => 'https://youtu.be/l6h9xFh723A?si=4RICL4bZap5i9fQo',
				],
			],

			'element_pack_elementor_extend' => [
				[
					'name'        => 'adblock-detector',
					'label'       => esc_html__('AdBlock Detector', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'pro',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/adblock-detector',
					'video_url'   => 'https://youtu.be/DGmEHqIM4XA',
				],
				[
					'name'        => 'animated-gradient-background',
					'label'       => esc_html__('Animated Gradient BG', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'free',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/animated-gradient-background/',
					'video_url'   => 'https://youtu.be/Hdq06W-2KDw',
				],
				[
					'name'        => 'backdrop-filter',
					'label'       => esc_html__('Backdrop Filter', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'free',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/backdrop-filter',
					'video_url'   => 'https://youtu.be/XuS3D-czTJc',
				],
				[
					'name'        => 'background-expand',
					'label'       => esc_html__('Background Expand', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'pro',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/background-expand',
					'video_url'   => 'https://youtu.be/VJ5ZnhLgLMs',
				],
				[
					'name'        => 'background-overlay',
					'label'       => esc_html__('Background Overlay', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'free',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/background-overlay/',
					'video_url'   => 'https://youtu.be/Px7PMsFK3Jg',
				],
				[
					'name'        => 'background-parallax',
					'label'       => esc_html__('BG Parallax Effects', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'on',
					'widget_type' => 'pro',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/parallax-background/',
					'video_url'   => 'https://youtu.be/UI3xKt2IlCQ',
				],
				[
					'name'        => 'confetti-effects',
					'label'       => esc_html__('Confetti Effects', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'pro',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/confetti-effects',
					'video_url'   => 'https://youtu.be/NcKHFeeUXqg',
				],
				[
					'name'        => 'cursor-effects',
					'label'       => esc_html__('Cursor Effects', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'pro',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/cursor-effects/',
					'video_url'   => 'https://youtu.be/Pnev5lPByEc',
				],
				[
					'name'        => 'custom-js',
					'label'       => esc_html__('Custom CSS / JS', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'free',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/custom-js/',
					'video_url'   => 'https://youtu.be/e-_qQl6dBbE?t=312',
				],
				[
					'name'        => 'equal-height',
					'label'       => esc_html__('Widget Equal Height', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'free',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/widget-equal-height/',
					'video_url'   => 'https://youtu.be/h19c3FOxYlc',
				],
				[
					'name'        => 'floating-effects',
					'label'       => esc_html__('Floating Effects', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'free',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/floating-effects',
					'video_url'   => 'https://youtu.be/hVFqjc9b3dE',
				],
				[
					'name'        => 'hash-link',
					'label'       => esc_html__('Hash Link', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'pro',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/hash-link/',
					'video_url'   => '',
				],
				[
					'name'        => 'grid-line',
					'label'       => esc_html__('Grid Line', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'free',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/grid-line',
					'video_url'   => 'https://youtu.be/SzC8En2Xl9c',
				],
				[
					'name'        => 'content-protector',
					'label'       => esc_html__('Content Protector', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'pro',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/content-protector',
					'video_url'   => 'https://youtu.be/ZN-8fnhWeI0?si=6WBFa6YYrXxYYLdF',
				],
				[
					'name'         => 'image-hover-effects',
					'label'        => esc_html__('Image Hover Effects', 'bdthemes-element-pack'),
					'type'         => 'checkbox',
					'default'      => 'off',
					'widget_type'  => 'pro',
					'content_type' => 'new',
					'demo_url'     => 'https://www.elementpack.pro/demo/element/image-hover-effects/',
					'video_url'    => '',
				],
				[
					'name'        => 'image-parallax',
					'label'       => esc_html__('Section Image Parallax', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'on',
					'widget_type' => 'pro',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/parallax-section/',
					'video_url'   => 'https://youtu.be/nMzk55831MY',
				],
				[
					'name'        => 'notation',
					'label'       => esc_html__('Notation', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'pro',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/notation',
					'video_url'   => 'https://youtu.be/DTz91mthFGE',
				],
				[
					'name'        => 'parallax-effects',
					'label'       => esc_html__('Parallax/Scrolling Effects', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'on',
					'widget_type' => 'pro',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/element-parallax',
					'video_url'   => 'https://youtu.be/Aw9TnT_L1g8',
				],
				[
					'name'        => 'particles',
					'label'       => esc_html__('Section Particles', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'on',
					'widget_type' => 'pro',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/section-particles/',
					'video_url'   => 'https://youtu.be/8mylXgB2bYg',
				],
				[
					'name'        => 'reveal-effects',
					'label'       => esc_html__('Reveal Effects', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'pro',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/reveal-effects/',
					'video_url'   => 'https://youtu.be/mSnoY510IUE',
				],
				[
					'name'        => 'ripple-effects',
					'label'       => esc_html__('Ripple Effects', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'pro',
					'content_type' => 'new',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/ripple-effects/',
					'video_url'   => '',
				],
				[
					'name'        => 'scroll-box',
					'label'       => esc_html__('Scroll Box', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'pro',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/scroll-box/',
					'video_url'   => 'https://youtu.be/Wj_4NS0lSd8',
				],
				[
					'name'        => 'scroll-fill-effect',
					'label'       => esc_html__('Scroll Fill Effect', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'pro',
					'content_type' => 'new',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/scroll-fill-effect/',
					'video_url'   => '',
				],
				[
					'name'        => 'section-sticky',
					'label'       => esc_html__('Section Sticky', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'on',
					'widget_type' => 'pro',
					'demo_url'    => 'https://www.elementpack.pro/demo/sticky-section/',
					'video_url'   => 'https://youtu.be/Vk0EoQSX0K8',
				],
				[
					'name'        => 'sound-effects',
					'label'       => esc_html__('Sound Effects', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'pro',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/sound-effects',
					'video_url'   => 'https://youtu.be/L1Sy1ZDfp3A',
				],
				[
					'name'        => 'threed-text',
					'label'       => esc_html__('3D Text', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'free',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/threed-text',
					'video_url'   => 'https://youtu.be/lhqgA4EyYKc',
				],
				[
					'name'        => 'tile-scroll',
					'label'       => esc_html__('Tile Scroll', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'pro',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/tile-scroll/',
					'video_url'   => 'https://youtu.be/rH4h03C4FE0',
				],
				[
					'name'        => 'realistic-image-shadow',
					'label'       => esc_html__('Realistic Image Shadow', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'pro',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/widget-tooltip/',
					'video_url'   => 'https://youtu.be/oVXwG-38g2Y',
				],
				[
					'name'        => 'tooltip',
					'label'       => esc_html__('Widget Tooltip', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'free',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/widget-tooltip/',
					'video_url'   => 'https://youtu.be/LJgF8wt7urw',
				],
				[
					'name'        => 'transform-effects',
					'label'       => esc_html__('Transform Effects', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'free',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/transform-example/',
					'video_url'   => 'https://youtu.be/Djc6bP7CF18',
				],
				[
					'name'        => 'visibility-controls',
					'label'       => esc_html__('Visibility Controls', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'pro',
					'content_type' => 'update',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/visibility-controls/',
					'video_url'   => 'https://youtu.be/E18TikPHBq4',
				],
				[
					'name'        => 'wrapper-link',
					'label'       => esc_html__('Wrapper Link', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'free',
					'demo_url'    => 'https://www.elementpack.pro/demo/element/wrapper-link',
					'video_url'   => 'https://youtu.be/ZVgGDY_FM1U',
				],
			],
			'element_pack_api_settings'     => [
				[
					'name'              => 'google_map_key',
					'label'             => esc_html__('Google Map API Key', 'bdthemes-element-pack'),
					'desc'              => __('Go to <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">https://developers.google.com</a> and <a href="https://console.cloud.google.com/google/maps-apis/overview">generate the API key</a> and insert here. This API key needs for show Advanced Google Map widget correctly. API Key also works for Google Review widget so you must enabled Places API too.', 'bdthemes-element-pack'),
					'placeholder'       => '------------- -------------------------',
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field',
					'video_url'         => 'https://youtu.be/cssyofmylFA',
				],
				[
					'name'              => 'disqus_user_name',
					'label'             => esc_html__('Disqus User Name', 'bdthemes-element-pack'),
					'desc'              => __('Go to <a href="https://help.disqus.com/customer/portal/articles/1255134-updating-your-disqus-settings#account" target="_blank">https://help.disqus.com/</a> for know how to get user name of your disqus account.', 'bdthemes-element-pack'),
					'placeholder'       => 'for example: bdthemes',
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field'
				],

				[
					'name'  => 'social_login_group_start',
					'label' => esc_html__('Social Login Access', 'bdthemes-element-pack'),
					'desc'  => __('Please fill up below fields for enbled your social login feature in user login widget.', 'bdthemes-element-pack'),
					'type'  => 'start_group',
				],

				[
					'name'              => 'facebook_app_id',
					'label'             => esc_html__('Facebook APP ID', 'bdthemes-element-pack'),
					'desc'              => __('Go to <a href="https://developers.facebook.com/docs/apps/register#create-app" target="_blank">https://developers.facebook.com</a> for create your facebook APP ID.', 'bdthemes-element-pack'),
					'placeholder'       => '---------------',
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field'
				],
				[
					'name'              => 'facebook_app_secret',
					'label'             => esc_html__('Facebook APP Secret', 'bdthemes-element-pack'),
					'desc'              => __('Go to your Google <a href="https://developers.facebook.com/docs/apps/register#create-app" target="_blank">developer</a>', 'bdthemes-element-pack'),
					'placeholder'       => '---------------',
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field'
				],
				[
					'name'              => 'google_client_id',
					'label'             => esc_html__('Google Client ID', 'bdthemes-element-pack'),
					'desc'              => __('Go to your Google <a href="https://console.developers.google.com/" target="_blank">developer</a> > Account.', 'bdthemes-element-pack'),
					'placeholder'       => '---------------',
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field'
				],

				[
					'name' => 'social_login_group_end',
					'type' => 'end_group',
				],


				//                [
				//                    'name'              => 'instagram_access_token',
				//                    'label'             => esc_html__( 'Instagram Access Token', 'bdthemes-element-pack' ),
				//                    'desc'              => __( 'Go to <a href="https://instagram.pixelunion.net/" target="_blank">This Link</a> and Generate the access token then copy and paste here.', 'bdthemes-element-pack' ),
				//                    'placeholder'       => '---------------',
				//                    'type'              => 'text',
				//                    'sanitize_callback' => 'sanitize_text_field'
				//                ],
				[
					'name'      => 'instagram_group_start',
					'label'     => esc_html__('Instagram Access', 'bdthemes-element-pack'),
					'desc'      => __('Go to <a href="https://developers.facebook.com/docs/instagram-basic-display-api/getting-started" target="_blank">https://developers.facebook.com/docs/instagram-basic-display-api/getting-started</a> for create your Consumer key and Access Token.', 'bdthemes-element-pack'),
					'type'      => 'start_group',
					'video_url' => 'https://youtu.be/IrQVteaaAow',
				],

				[
					'name'              => 'instagram_app_id',
					'label'             => esc_html__('Instagram App ID', 'bdthemes-element-pack'),
					'placeholder'       => '---------------',
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field'
				],
				[
					'name'              => 'instagram_app_secret',
					'label'             => esc_html__('Instagram App Secret', 'bdthemes-element-pack'),
					'placeholder'       => '---------------',
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field'
				],

				[
					'name'              => 'instagram_access_token',
					'label'             => esc_html__('Instagram Access Token', 'bdthemes-element-pack'),
					'desc'              => __('Go to <a href="https://developers.facebook.com/docs/instagram-basic-display-api/getting-started" target="_blank">This Link</a> and Generate the access token then copy and paste here.', 'bdthemes-element-pack'),
					'placeholder'       => '---------------',
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field'
				],

				[
					'name' => 'instagram_group_end',
					'type' => 'end_group',
				],

				[
					'name'      => 'twitter_group_start',
					'label'     => esc_html__('Twitter Access', 'bdthemes-element-pack'),
					'desc'      => __('Go to <a href="https://developer.twitter.com/en" target="_blank">https://developer.twitter.com/en</a> for create your Consumer key and Access Token.', 'bdthemes-element-pack'),
					'type'      => 'start_group',
					'video_url' => 'https://youtu.be/IrQVteaaAow',
				],

				[
					'name'              => 'twitter_name',
					'label'             => esc_html__('User Name', 'bdthemes-element-pack'),
					'placeholder'       => 'for example: bdthemescom',
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field'
				],
				[
					'name'              => 'twitter_consumer_key',
					'label'             => esc_html__('Consumer Key', 'bdthemes-element-pack'),
					'placeholder'       => '',
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field'
				],
				[
					'name'              => 'twitter_consumer_secret',
					'label'             => esc_html__('Consumer Secret', 'bdthemes-element-pack'),
					'placeholder'       => '',
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field'
				],
				[
					'name'              => 'twitter_access_token',
					'label'             => esc_html__('Access Token', 'bdthemes-element-pack'),
					'placeholder'       => '',
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field'
				],
				[
					'name'              => 'twitter_access_token_secret',
					'label'             => esc_html__('Access Token Secret', 'bdthemes-element-pack'),
					'placeholder'       => '',
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field'
				],
				[
					'name' => 'twitter_group_end',
					'type' => 'end_group',
				],

				[
					'name'  => 'recaptcha_group_start',
					'label' => esc_html__('reCAPTCHA Access', 'bdthemes-element-pack'),
					'desc'  => __('Go to your Google <a href="https://www.google.com/recaptcha/" target="_blank">reCAPTCHA</a> > Account > Generate Keys (reCAPTCHA V2 > Invisible) and Copy and Paste here.', 'bdthemes-element-pack'),
					'type'  => 'start_group',
				],

				[
					'name'              => 'recaptcha_site_key',
					'label'             => esc_html__('Site key', 'bdthemes-element-pack'),
					'placeholder'       => '',
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field'
				],
				[
					'name'              => 'recaptcha_secret_key',
					'label'             => esc_html__('Secret key', 'bdthemes-element-pack'),
					'placeholder'       => '',
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field'
				],

				[
					'name' => 'recaptcha_group_end',
					'type' => 'end_group',
				],

				[
					'name'  => 'mailchimp_group_start',
					'label' => esc_html__('Mailchimp Access', 'bdthemes-element-pack'),
					'desc'  => __('Go to your Mailchimp > Website > Domains > Extras > API Keys (<a href="http://prntscr.com/xqo78x" target="_blank">http://prntscr.com/xqo78x</a>) then create a key and paste here. You will get the audience ID here: <a href="http://prntscr.com/xqnt5z" target="_blank">http://prntscr.com/xqnt5z</a>', 'bdthemes-element-pack'),
					'type'  => 'start_group',
				],


				[
					'name'              => 'mailchimp_api_key',
					'label'             => esc_html__('Mailchimp API Key', 'bdthemes-element-pack'),
					'placeholder'       => '',
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field'
				],
				[
					'name'              => 'mailchimp_list_id',
					'label'             => esc_html__('Audience ID', 'bdthemes-element-pack'),
					'placeholder'       => '',
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field'
				],

				[
					'name' => 'mailchimp_group_end',
					'type' => 'end_group',
				],
				[
					'name'  => 'weather_group_start',
					'label' => esc_html__('Weather API Access', 'bdthemes-element-pack'),
					'desc'  => __('Please choose your Weather provider, both provider has the free and paid package.', 'bdthemes-element-pack'),
					'type'  => 'start_group',
				],
				[
					'name'              => 'weatherstack_api_key',
					'label'             => esc_html__('WeatherStack Key', 'bdthemes-element-pack'),
					'desc'              => __('Go to <a href="https://weatherstack.com/quickstart" target="_blank">https://weatherstack.com/quickstart</a> > Copy Key and Paste here.', 'bdthemes-element-pack'),
					'placeholder'       => '',
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field'
				],

				[
					'name'              => 'open_weather_api_key',
					'label'             => esc_html__('Open Weather Map Key', 'bdthemes-element-pack'),
					'desc'              => __('Go to <a href="https://home.openweathermap.org/api_keys" target="_blank">https://home.openweathermap.org/api_keys</a> > Copy Key and Paste here. This api key also works for <strong>Air Pollution Widget</strong>', 'bdthemes-element-pack'),
					'placeholder'       => '',
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field'
				],
				[
					'name' => 'weather_group_end',
					'type' => 'end_group',
				],
				[
					'name'              => 'open_street_map_access_token',
					'label'             => esc_html__('MapBox Access Token (for Open Street Map)', 'bdthemes-element-pack'),
					'desc'              => __('<a href="https://www.mapbox.com/account/access-tokens" target="_blank">Click Here</a> to get access token. This Access Token needs for show Open Street Map widget correctly.', 'bdthemes-element-pack'),
					'placeholder'       => '------------- -------------------------',
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field'
				],

				[
					'name'  => 'contact_form_group_start',
					'label' => esc_html__('Simple Contact Form ', 'bdthemes-element-pack'),
					'desc'  => __('Set your simple contact form settings from here.', 'bdthemes-element-pack'),
					'type'  => 'start_group',
				],

				[
					'name'              => 'contact_form_email',
					'label'             => esc_html__('Contact Form Email', 'bdthemes-element-pack'),
					'desc'              => __('You can set alternative email for simple contact form', 'bdthemes-element-pack'),
					'placeholder'       => 'example@email.com',
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field'
				],
				[
					'name'              => 'contact_form_spam_email',
					'label'             => esc_html__('Spam Email List', 'bdthemes-element-pack'),
					'desc'              => __('add spam email here for block spamming from your contact form. multiple email separated by comma (,).', 'bdthemes-element-pack'),
					'placeholder'       => 'example@email.com, example2@email.com',
					'type'              => 'textarea',
					'sanitize_callback' => 'sanitize_text_field'
				],

				[
					'name' => 'contact_form_group_end',
					'type' => 'end_group',
				],

				[
					'name'  => 'yelp_social_group_start',
					'label' => esc_html__('Yelp Access', 'bdthemes-element-pack'),
					'desc'  => __('Go to your <a href="https://www.yelp.com/developers/v3/manage_app" target="_blank">Yelp Developer Account</a> to get access client ID and Key. This credential need for Social Proof widget.', 'bdthemes-element-pack'),
					'type'  => 'start_group',
				],

				[
					'name'              => 'yelp_client_id',
					'label'             => esc_html__('Yelp Client ID', 'bdthemes-element-pack'),
					'placeholder'       => '',
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field'
				],
				[
					'name'              => 'yelp_api_key',
					'label'             => esc_html__('Yelp API Key', 'bdthemes-element-pack'),
					'placeholder'       => '',
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field'
				],

				[
					'name' => 'yelp_social_group_end',
					'type' => 'end_group',
				],
				[
					'name'  => 'fb_social_group_start',
					'label' => esc_html__('Facebook Social Access', 'bdthemes-element-pack'),
					'desc'  => __('Go to your <a href="https://developers.facebook.com/apps/" target="_blank">Facebook Developer Account</a> to get access Page ID and Access Token. This credential need for Social Feeds widget.', 'bdthemes-element-pack'),
					'type'  => 'start_group',
				],
				[
					'name'              => 'fb_page_id',
					'label'             => esc_html__('Facebook Page ID', 'bdthemes-element-pack'),
					'placeholder'       => '',
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field'
				],
				[
					'name'              => 'fb_access_token',
					'label'             => esc_html__('Facebook Access Token', 'bdthemes-element-pack'),
					'placeholder'       => '',
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field'
				],
				[
					'name' => 'fb_social_group_end',
					'type' => 'end_group',
				],

			],
			'element_pack_other_settings'   => [

				[
					'name'  => 'minified_asset_manager_group_start',
					'label' => esc_html__('Asset Manager', 'bdthemes-element-pack'),
					'desc'  => __('If you want to combine your JS and css and load in a single file so enable it. When you enable it all widgets css and JS will combine in a single file.', 'bdthemes-element-pack'),
					'type'  => 'start_group',
				],

				[
					'name'        => 'asset-manager',
					'label'       => esc_html__('Asset Manager', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'free',
					'demo_url'    => 'https://www.elementpack.pro/knowledge-base/how-to-use-element-pack-asset-manager/',
					'video_url'   => 'https://youtu.be/nytQFZv_CSs',
				],

				[
					'name' => 'minified_asset_manager_group_end',
					'type' => 'end_group',
				],

				[
					'name'  => 'live_copy_group_start',
					'label' => esc_html__('Live Copy or Paste', 'bdthemes-element-pack'),
					'desc'  => __('Live copy is a copy feature that allow you to copy and paste content from one domain to another. For example you can copy demo content directly from our demo website.', 'bdthemes-element-pack'),
					'type'  => 'start_group',
				],

				[
					'name'        => 'live-copy',
					'label'       => esc_html__('Live Copy/Paste', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'free',
					'demo_url'    => 'https://www.elementpack.pro/knowledge-base/how-to-use-live-copy-option/',
					'video_url'   => 'https://youtu.be/jOdWVw2TCmo',

				],

				[
					'name' => 'live_copy_group_end',
					'type' => 'end_group',
				],

				[
					'name'  => 'essential_shortcodes_group_start',
					'label' => esc_html__('Essential Shortcodes', 'bdthemes-element-pack'),
					'desc'  => __('If you need element pack essential shortcodes feature so you can do that from here. it\'s included some basic content feature that not possible by element pack.', 'bdthemes-element-pack'),
					'type'  => 'start_group',
				],

				[
					'name'        => 'essential-shortcodes',
					'label'       => esc_html__('Essential Shortcodes', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'pro',
					'demo_url'    => 'https://www.elementpack.pro/knowledge-base/how-to-use-element-pack-essential-shortcodes/',
					'video_url'   => 'https://youtu.be/fUMoYNa_WLY',

				],

				[
					'name' => 'essential_shortcodes_group_end',
					'type' => 'end_group',
				],

				[
					'name'  => 'template_library_group_start',
					'label' => esc_html__('Template Library (in Editor)', 'bdthemes-element-pack'),
					'desc'  => __('If you need to show element pack template library in your editor so please enable this option. It\'s amazing feature for elementor.', 'bdthemes-element-pack'),
					'type'  => 'start_group',
				],

				[
					'name'        => 'template-library',
					'label'       => esc_html__('Template Library (in Editor)', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'free',
					'demo_url'    => 'https://www.elementpack.pro/knowledge-base/how-to-use-element-pack-template-library/',
					'video_url'   => 'https://youtu.be/IZw_iRBWbC8',


				],

				[
					'name' => 'template_library_group_end',
					'type' => 'end_group',
				],

				[
					'name'  => 'context_menu_group_start',
					'label' => esc_html__('Context Menu', 'bdthemes-element-pack'),
					'desc'  => __('Turn on this switcher to enable the Context Menu inside "Site Settings" of Elementor Editor Page to use the right-click menu.', 'bdthemes-element-pack'),
					'type'  => 'start_group',
				],

				[
					'name'        => 'context-menu',
					'label'       => esc_html__('Context Menu', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'pro',
					'demo_url'    => 'https://www.elementpack.pro/knowledge-base/how-to-use-element-pack-context-menu/',
					'video_url'   => 'https://youtu.be/LptQctJ22S0',
				],

				[
					'name' => 'context_menu_group_end',
					'type' => 'end_group',
				],

				[
					'name'  => 'duplicator_group_start',
					'label' => esc_html__('Duplicator', 'bdthemes-element-pack'),
					'desc'  => __('Just hit the button below to enable the duplicator. It can duplicate anything like posts,pages and elementor templates. A masterclass duplication with just one click.', 'bdthemes-element-pack'),
					'type'  => 'start_group',
				],

				[
					'name'        => 'duplicator',
					'label'       => esc_html__('Duplicator', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'free',
					'demo_url'    => 'https://www.elementpack.pro/knowledge-base/how-to-use-element-pack-duplicator/',
					'video_url'   => '',
				],

				[
					'name' => 'duplicator_group_end',
					'type' => 'end_group',
				],
				[
					'name'  => 'mega_menu_group_start',
					'label' => esc_html__('Mega Menu', 'bdthemes-element-pack'),
					'desc'  => __('The Mega Menu by Element Pack Pro allows the users to create organized and oversized menus with full custom layouts having images, columns, sliders, icons, forms, buttons, and lots of links. This is one of the most demanding feature and now it is in your hands.', 'bdthemes-element-pack'),
					'type'  => 'start_group',
				],

				[
					'name'        => 'mega-menu',
					'label'       => esc_html__('Mega Menu', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'pro',
					//'demo_url'    => 'https://www.elementpack.pro/knowledge-base/how-to-use-element-pack-megamenu/',
					'video_url'   => 'https://youtu.be/ZOBLWIZvGLs',
				],

				[
					'name' => 'mega_menu_group_end',
					'type' => 'end_group',
				],
				[
					'name'  => 'smooth_scroller_group_start',
					'label' => esc_html__('Smooth Scroller', 'bdthemes-element-pack'),
					'desc'  => __('Turn on this switcher to enable the Smooth Scroller Features, The Smooth Scroller feature enhances user experience by providing seamless, visually pleasing content navigation through animated transitions, ensuring a polished and user-friendly interface.', 'bdthemes-element-pack'),
					'type'  => 'start_group',
					'content_type' => 'new',
				],

				[
					'name'        => 'smooth-scroller',
					'label'       => esc_html__('Smooth Scroller', 'bdthemes-element-pack'),
					'type'        => 'checkbox',
					'default'     => 'off',
					'widget_type' => 'pro',
					'demo_url'    => 'https://www.elementpack.pro/knowledge-base/how-to-use-element-pack-smooth-scroller/',
					'video_url'   => '',
				],

				[
					'name' => 'smooth_scroller_group_end',
					'type' => 'end_group',
				]
			]
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'acf-accordion',
			'label'       => esc_html__('ACF Accordion', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'off',
			'plugin_name' => 'advanced-custom-fields-pro',
			'plugin_path' => 'advanced-custom-fields-pro/acf.php',
			'paid'        => 'https://www.advancedcustomfields.com/pro/',
			'widget_type' => 'pro',
			'content_type' => 'acf new',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/acf-accordion',
			'video_url'   => '',
		];
		
		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'acf-gallery',
			'label'       => esc_html__('ACF Gallery', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'off',
			'plugin_name' => 'advanced-custom-fields-pro',
			'plugin_path' => 'advanced-custom-fields-pro/acf.php',
			'paid'        => 'https://www.advancedcustomfields.com/pro/',
			'widget_type' => 'pro',
			'content_type' => 'acf new',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/acf-gallery',
			'video_url'   => '',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'bbpress-forum-form',
			'label'       => esc_html__('bbPress Forum Form', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'off',
			'plugin_name' => 'bbpress',
			'plugin_path' => 'bbpress/bbpress.php',
			'widget_type' => 'pro',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/bbpress-forum-form',
			'video_url'   => '',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'bbpress-forum-index',
			'label'       => esc_html__('bbPress Forum Index', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'off',
			'plugin_name' => 'bbpress',
			'plugin_path' => 'bbpress/bbpress.php',
			'widget_type' => 'pro',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/bbpress-forum-index',
			'video_url'   => '',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'bbpress-single-forum',
			'label'       => esc_html__('bbPress Single Forum', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'off',
			'plugin_name' => 'bbpress',
			'plugin_path' => 'bbpress/bbpress.php',
			'widget_type' => 'pro',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/bbpress-single-forum',
			'video_url'   => '',
		];
		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'bbpress-topic-index',
			'label'       => esc_html__('bbPress Topic Index', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'off',
			'plugin_name' => 'bbpress',
			'plugin_path' => 'bbpress/bbpress.php',
			'widget_type' => 'pro',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/bbpress-topic-index',
			'video_url'   => '',
		];
		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'bbpress-topic-form',
			'label'       => esc_html__('bbPress Topic Form', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'off',
			'plugin_name' => 'bbpress',
			'plugin_path' => 'bbpress/bbpress.php',
			'widget_type' => 'pro',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/bbpress-topic-form',
			'video_url'   => '',
		];
		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'bbpress-single-topic',
			'label'       => esc_html__('bbPress Single Topic', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'off',
			'plugin_name' => 'bbpress',
			'plugin_path' => 'bbpress/bbpress.php',
			'widget_type' => 'pro',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/bbpress-single-topic',
			'video_url'   => '',
		];
		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'bbpress-reply-form',
			'label'       => esc_html__('bbPress Reply Form', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'off',
			'plugin_name' => 'bbpress',
			'plugin_path' => 'bbpress/bbpress.php',
			'widget_type' => 'pro',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/bbpress-reply-form',
			'video_url'   => '',
		];
		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'bbpress-single-reply',
			'label'       => esc_html__('bbPress Single Reply', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'off',
			'plugin_name' => 'bbpress',
			'plugin_path' => 'bbpress/bbpress.php',
			'widget_type' => 'pro',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/bbpress-single-reply',
			'video_url'   => '',
		];
		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'bbpress-topic-tags',
			'label'       => esc_html__('bbPress Topic Tags', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'off',
			'plugin_name' => 'bbpress',
			'plugin_path' => 'bbpress/bbpress.php',
			'widget_type' => 'pro',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/bbpress-topic-tags',
			'video_url'   => '',
		];
		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'bbpress-single-tag',
			'label'       => esc_html__('bbPress Single Tag', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'off',
			'plugin_name' => 'bbpress',
			'plugin_path' => 'bbpress/bbpress.php',
			'widget_type' => 'pro',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/bbpress-single-tag',
			'video_url'   => '',
		];
		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'bbpress-single-view',
			'label'       => esc_html__('bbPress Single View', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'off',
			'plugin_name' => 'bbpress',
			'plugin_path' => 'bbpress/bbpress.php',
			'widget_type' => 'pro',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/bbpress-single-view',
			'video_url'   => '',
		];
		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'bbpress-stats',
			'label'       => esc_html__('bbPress Stats', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'off',
			'plugin_name' => 'bbpress',
			'plugin_path' => 'bbpress/bbpress.php',
			'widget_type' => 'pro',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/bbpress-stats',
			'video_url'   => '',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'buddypress-friends',
			'label'       => esc_html__('BuddyPress Friends', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'on',
			'plugin_name' => 'buddypress',
			'plugin_path' => 'buddypress/bp-loader.php',
			'widget_type' => 'pro',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/buddypress-friends/',
			'video_url'   => 'https://youtu.be/t6t0M5kGEig',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'buddypress-group',
			'label'       => esc_html__('BuddyPress Group', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'on',
			'plugin_name' => 'buddypress',
			'plugin_path' => 'buddypress/bp-loader.php',
			'widget_type' => 'pro',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/buddypress-group/',
			'video_url'   => 'https://youtu.be/CccODcBw_9w',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'buddypress-member',
			'label'       => esc_html__('BuddyPress Member', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'on',
			'plugin_name' => 'buddypress',
			'plugin_path' => 'buddypress/bp-loader.php',
			'widget_type' => 'pro',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/buddypress-member/',
			'video_url'   => 'https://youtu.be/CLV9RCdq09k',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'booked-calendar',
			'label'       => esc_html__('Booked Calendar', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'on',
			'plugin_name' => 'booked',
			'plugin_path' => 'booked/booked.php',
			'paid'        => 'https://codecanyon.net/item/booked-appointments-appointment-booking-for-wordpress/9466968',
			'widget_type' => 'pro',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/booked-calendar/',
			'video_url'   => 'https://youtu.be/bodvi_5NkDU',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'caldera-forms',
			'label'        => esc_html__('Caldera Forms', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'on',
			'plugin_name'  => 'caldera-forms',
			'plugin_path'  => 'caldera-forms/caldera-core.php',
			'widget_type'  => 'free',
			'content_type' => 'forms',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/caldera-form/',
			'video_url'    => 'https://youtu.be/2EiVSLows20',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'contact-form-7',
			'label'       => esc_html__('Contact Form 7', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'on',
			'plugin_name' => 'contact-form-7',
			'plugin_path' => 'contact-form-7/wp-contact-form-7.php',
			'widget_type' => 'free',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/contact-form-7/',
			'video_url'   => 'https://youtu.be/oWepfrLrAN4',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'charitable-campaigns',
			'label'        => esc_html__('Charitable Campaigns', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'off',
			'plugin_name'  => 'charitable',
			'plugin_path'  => 'charitable/charitable.php',
			'widget_type'  => 'pro',
			'content_type' => 'others',
			'demo_url'     => 'https: //elementpack.pro/demo/element/charitable-campaigns/',
			'video_url'    => 'https://youtu.be/ugKfZyvSbGA',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'charitable-donations',
			'label'        => esc_html__('Charitable Donations', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'off',
			'plugin_name'  => 'charitable',
			'plugin_path'  => 'charitable/charitable.php',
			'widget_type'  => 'pro',
			'content_type' => 'others',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/charitable-donations/',
			'video_url'    => 'https://youtu.be/C38sbaKx9x0',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'charitable-donors',
			'label'        => esc_html__('Charitable Donors', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'off',
			'plugin_name'  => 'charitable',
			'plugin_path'  => 'charitable/charitable.php',
			'widget_type'  => 'pro',
			'content_type' => 'others',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/charitable-donors/',
			'video_url'    => 'https://youtu.be/ljnbE8JVg7w',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'charitable-donation-form',
			'label'        => esc_html__('Charitable Donation Form', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'off',
			'plugin_name'  => 'charitable',
			'plugin_path'  => 'charitable/charitable.php',
			'widget_type'  => 'pro',
			'content_type' => 'others',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/charitable-donation-form/',
			'video_url'    => 'https://youtu.be/aufVwEUZJhY',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'charitable-stat',
			'label'        => esc_html__('Charitable Stat', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'off',
			'plugin_name'  => 'charitable',
			'plugin_path'  => 'charitable/charitable.php',
			'widget_type'  => 'pro',
			'content_type' => 'others',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/charitable-stat/',
			'video_url'    => 'https://youtu.be/54cw85jmhtg',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'charitable-login',
			'label'        => esc_html__('Charitable Login', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'off',
			'plugin_name'  => 'charitable',
			'plugin_path'  => 'charitable/charitable.php',
			'widget_type'  => 'pro',
			'content_type' => 'others',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/charitable-login/',
			'video_url'    => 'https://youtu.be/c0A90DdfGGM',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'charitable-registration',
			'label'        => esc_html__('Charitable Registration', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'off',
			'plugin_name'  => 'charitable',
			'plugin_path'  => 'charitable/charitable.php',
			'widget_type'  => 'pro',
			'content_type' => 'others',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/charitable-registration/',
			'video_url'    => 'https://youtu.be/N-IMBmjGJsA',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'charitable-profile',
			'label'        => esc_html__('Charitable Profile', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'off',
			'plugin_name'  => 'charitable',
			'plugin_path'  => 'charitable/charitable.php',
			'widget_type'  => 'pro',
			'content_type' => 'others',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/charitable-profile/',
			'video_url'    => 'https://youtu.be/DD7ZiMpxK-w',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'download-monitor',
			'label'       => esc_html__('Download Monitor', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'on',
			'plugin_name' => 'download-monitor',
			'plugin_path' => 'download-monitor/download-monitor.php',
			'widget_type' => 'pro',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/download-monitor',
			'video_url'   => 'https://youtu.be/7LaBSh3_G5A',

		];

		if (ModuleService::_is_plugin_installed('easy-digital-downloads', 'easy-digital-downloads/easy-digital-downloads.php') === true && is_plugin_active('easy-digital-downloads-pro/easy-digital-downloads.php') !== true) {
			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-cart',
				'label'       => esc_html__('EDD Cart', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'off',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-cart/',
				'video_url'   => '',
			];

			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-category-grid',
				'label'       => esc_html__('EDD Category Grid', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'off',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-category-grid/',
				'video_url'   => '',
			];

			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-category-carousel',
				'label'       => esc_html__('EDD Category Carousel', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'off',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-category-carousel/',
				'video_url'   => '',
			];

			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-checkout',
				'label'       => esc_html__('EDD Checkout', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'off',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-checkout/',
				'video_url'   => '',
			];
			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-login',
				'label'       => esc_html__('EDD Login', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'off',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-login/',
				'video_url'   => '',
			];
			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-mini-cart',
				'label'       => esc_html__('EDD Mini Cart', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'off',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-mini-cart/',
				'video_url'   => '',
			];
			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-product',
				'label'       => esc_html__('EDD Product', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'off',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-product/',
				'video_url'   => '',
			];

			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-product-carousel',
				'label'       => esc_html__('EDD Product Carousel', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'off',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-product-carousel/',
				'video_url'   => '',
			];

			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-product-reviews',
				'label'       => esc_html__('EDD Product Reviews', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'off',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-product-reviews/',
				'video_url'   => 'https://youtu.be/drn_yEGoC_E?si=Qh5D4urdf-biAinR',
			];

			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-product-review-carousel',
				'label'       => esc_html__('EDD Product Review Carousel', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'off',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-product-review-carousel/',
				'video_url'   => '',
			];

			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-profile-editor',
				'label'       => esc_html__('EDD Profile Editor', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'on',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-profile-editor/',
				'video_url'   => 'https://youtu.be/f2v7EFla94c',
			];

			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-purchase-history',
				'label'       => esc_html__('EDD Purchase History', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'on',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-purchase-history/',
				'video_url'   => 'https://youtu.be/oUppcuQTB7M',
			];

			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-register',
				'label'       => esc_html__('EDD Register', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'off',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-register/',
				'video_url'   => '',
			];

			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-tabs',
				'label'       => esc_html__('EDD Tabs', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'off',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-tabs/',
				'video_url'   => '',
			];

			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'easy-digital-downloads',
				'label'       => esc_html__('Easy Digital Download', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'on',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/easy-digital-downloads/',
				'video_url'   => 'https://youtu.be/f2v7EFla94c',
			];

			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-download-history',
				'label'       => esc_html__('EDD History', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'on',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-download-history/',
				'video_url'   => 'https://youtu.be/taM7whXxmNY',
			];
		} else {
			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-cart',
				'label'       => esc_html__('EDD Cart', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'off',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads-pro/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-cart/',
				'video_url'   => '',
			];

			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-category-grid',
				'label'       => esc_html__('EDD Category Grid', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'off',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads-pro/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-category-grid/',
				'video_url'   => '',
			];

			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-category-carousel',
				'label'       => esc_html__('EDD Category Carousel', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'off',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads-pro/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-category-carousel/',
				'video_url'   => '',
			];

			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-checkout',
				'label'       => esc_html__('EDD Checkout', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'off',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads-pro/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-checkout/',
				'video_url'   => '',
			];
			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-login',
				'label'       => esc_html__('EDD Login', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'off',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads-pro/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-login/',
				'video_url'   => '',
			];
			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-mini-cart',
				'label'       => esc_html__('EDD Mini Cart', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'off',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads-pro/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-mini-cart/',
				'video_url'   => '',
			];
			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-product',
				'label'       => esc_html__('EDD Product', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'off',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads-pro/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-product/',
				'video_url'   => '',
			];

			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-product-carousel',
				'label'       => esc_html__('EDD Product Carousel', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'off',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads-pro/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-product-carousel/',
				'video_url'   => '',
			];

			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-product-reviews',
				'label'       => esc_html__('EDD Product Reviews', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'off',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads-pro/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-product-reviews/',
				'video_url'   => '',
			];

			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-product-review-carousel',
				'label'       => esc_html__('EDD Product Review Carousel', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'off',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads-pro/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-product-review-carousel/',
				'video_url'   => '',
			];

			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-profile-editor',
				'label'       => esc_html__('EDD Profile Editor', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'on',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads-pro/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-profile-editor/',
				'video_url'   => 'https://youtu.be/f2v7EFla94c',
			];

			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-purchase-history',
				'label'       => esc_html__('EDD Purchase History', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'on',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads-pro/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-purchase-history/',
				'video_url'   => 'https://youtu.be/oUppcuQTB7M',
			];

			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-register',
				'label'       => esc_html__('EDD Register', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'off',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads-pro/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-register/',
				'video_url'   => '',
			];

			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-tabs',
				'label'       => esc_html__('EDD Tabs', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'off',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads-pro/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-tabs/',
				'video_url'   => '',
			];

			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'easy-digital-downloads',
				'label'       => esc_html__('Easy Digital Download', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'on',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads-pro/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/easy-digital-downloads/',
				'video_url'   => 'https://youtu.be/f2v7EFla94c',
			];

			$settings_fields['element_pack_third_party_widget'][] = [
				'name'        => 'edd-download-history',
				'label'       => esc_html__('EDD History', 'bdthemes-element-pack'),
				'type'        => 'checkbox',
				'default'     => 'on',
				'plugin_name' => 'easy-digital-downloads',
				'plugin_path' => 'easy-digital-downloads-pro/easy-digital-downloads.php',
				'widget_type' => 'pro',
				'demo_url'    => 'https://www.elementpack.pro/demo/element/edd-download-history/',
				'video_url'   => 'https://youtu.be/taM7whXxmNY',
			];
		}
		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'everest-forms',
			'label'        => esc_html__('Everest Forms', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'off',
			'plugin_name'  => 'everest-forms',
			'plugin_path'  => 'everest-forms/everest-forms.php',
			'widget_type'  => 'free',
			'content_type' => 'forms',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/everest-forms/',
			'video_url'    => 'https://youtu.be/jfZhIFpdvcg',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'events-calendar-grid',
			'label'       => esc_html__('Events Calendar Grid', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'off',
			'plugin_name' => 'the-events-calendar',
			'plugin_path' => 'the-events-calendar/the-events-calendar.php',
			'widget_type' => 'free',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/events-calendar-grid/',
			'video_url'   => 'https://youtu.be/QeqrcDx1Vus',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'events-calendar-carousel',
			'label'       => esc_html__('Events Calendar Carousel', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'off',
			'plugin_name' => 'the-events-calendar',
			'plugin_path' => 'the-events-calendar/the-events-calendar.php',
			'widget_type' => 'pro',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/events-calendar-carousel/',
			'video_url'   => 'https://youtu.be/_ZPPBmKmGGg',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'events-calendar-list',
			'label'       => esc_html__('Events Calendar List', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'off',
			'plugin_name' => 'the-events-calendar',
			'plugin_path' => 'the-events-calendar/the-events-calendar.php',
			'widget_type' => 'pro',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/events-calendar-list/',
			'video_url'   => 'https://youtu.be/2J4XhOe8J0o',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'faq',
			'label'        => esc_html__('FAQ', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'off',
			'plugin_name'  => 'bdthemes-faq',
			'plugin_path'  => 'bdthemes-faq/bdthemes-faq.php',
			'paid'         => 'https://bdthemes.com/secure/plugins/bdthemes-faq.zip?key=40fb823b8016d31411a7fe281f41044g',
			'widget_type'  => 'pro',
			'content_type' => 'post',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/carousel/faq/',
			'video_url'    => 'https://youtu.be/jGGdCuSjesY',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'fluent-forms',
			'label'        => esc_html__('Fluent Forms', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'off',
			'plugin_name'  => 'fluentform',
			'plugin_path'  => 'fluentform/fluentform.php',
			'widget_type'  => 'free',
			'content_type' => 'forms',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/fluent-forms/',
			'video_url'    => 'https://youtu.be/BWPuKe4PfQ4',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'formidable-forms',
			'label'        => esc_html__('Formidable Forms', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'off',
			'plugin_name'  => 'formidable',
			'plugin_path'  => 'formidable/formidable.php',
			'widget_type'  => 'free',
			'content_type' => 'forms',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/formidable-forms/',
			'video_url'    => 'https://youtu.be/ZQzcED7S-XI',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'forminator-forms',
			'label'        => esc_html__('Forminator Forms', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'off',
			'plugin_name'  => 'forminator',
			'plugin_path'  => 'forminator/forminator.php',
			'widget_type'  => 'pro',
			'content_type' => 'forms',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/forminator-forms/',
			'video_url'    => 'https://youtu.be/DdBvY0dnGsk',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'give-donation-history',
			'label'        => esc_html__('Give Donation History', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'off',
			'plugin_name'  => 'give',
			'plugin_path'  => 'give/give.php',
			'widget_type'  => 'pro',
			'content_type' => 'forms',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/give-donation-history/',
			'video_url'    => 'https://youtu.be/n2Cnlubi-E8',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'give-donor-wall',
			'label'        => esc_html__('Give Donor Wall', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'off',
			'plugin_name'  => 'give',
			'plugin_path'  => 'give/give.php',
			'widget_type'  => 'pro',
			'content_type' => 'forms',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/give-donor-wall/',
			'video_url'    => 'https://youtu.be/W_RRrE4cmEo',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'give-form-grid',
			'label'        => esc_html__('Give Form Grid', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'off',
			'plugin_name'  => 'give',
			'plugin_path'  => 'give/give.php',
			'widget_type'  => 'pro',
			'content_type' => 'forms',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/give-form-grid/',
			'video_url'    => 'https://youtu.be/hq4ElaX0nrE',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'give-form',
			'label'        => esc_html__('Give Form', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'off',
			'plugin_name'  => 'give',
			'plugin_path'  => 'give/give.php',
			'widget_type'  => 'pro',
			'content_type' => 'forms',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/give-form/',
			'video_url'    => 'https://youtu.be/k18Mgivy9Mw',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'give-goal',
			'label'        => esc_html__('Give Goal', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'off',
			'plugin_name'  => 'give',
			'plugin_path'  => 'give/give.php',
			'widget_type'  => 'pro',
			'content_type' => 'forms',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/give-goal/',
			'video_url'    => 'https://youtu.be/WdRBJL7fOvk',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'give-login',
			'label'        => esc_html__('Give Login', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'off',
			'plugin_name'  => 'give',
			'plugin_path'  => 'give/give.php',
			'widget_type'  => 'pro',
			'content_type' => 'forms',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/give-login/',
			'video_url'    => 'https://youtu.be/_mgg8ms12Gw',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'give-profile-editor',
			'label'        => esc_html__('Give Profile Editor', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'off',
			'plugin_name'  => 'give',
			'plugin_path'  => 'give/give.php',
			'widget_type'  => 'pro',
			'content_type' => 'forms',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/give-profile-editor/',
			'video_url'    => 'https://youtu.be/oaUUPA7eX2A',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'give-receipt',
			'label'        => esc_html__('Give Receipt', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'off',
			'plugin_name'  => 'give',
			'plugin_path'  => 'give/give.php',
			'widget_type'  => 'pro',
			'content_type' => 'forms',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/give-receipt/',
			'video_url'    => 'https://youtu.be/2xoXNi_Hx3k',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'give-register',
			'label'        => esc_html__('Give Register', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'off',
			'plugin_name'  => 'give',
			'plugin_path'  => 'give/give.php',
			'widget_type'  => 'pro',
			'content_type' => 'forms',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/give-register/',
			'video_url'    => 'https://youtu.be/4pO-fTXuW3Q',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'give-totals',
			'label'        => esc_html__('Give Totals', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'off',
			'plugin_name'  => 'give',
			'plugin_path'  => 'give/give.php',
			'widget_type'  => 'pro',
			'content_type' => 'forms',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/give-totals/',
			'video_url'    => 'https://youtu.be/fZMljNFdvKs',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'gravity-forms',
			'label'        => esc_html__('Gravity Forms', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'on',
			'plugin_name'  => 'gravityforms',
			'plugin_path'  => 'gravityforms/gravityforms.php',
			'paid'         => 'https://www.gravityforms.com/',
			'widget_type'  => 'pro',
			'content_type' => 'forms',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/gravity-forms/',
			'video_url'    => 'https://youtu.be/452ZExESiBI',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'instagram-feed',
			'label'       => esc_html__('Instagram Feed', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'on',
			'plugin_name' => 'instagram-feed',
			'plugin_path' => 'instagram-feed/instagram-feed.php',
			'widget_type' => 'pro',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/instagram-feed/',
			'video_url'   => 'https://youtu.be/Wf7naA7EL7s',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'layer-slider',
			'label'        => esc_html__('Layer Slider', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'on',
			'plugin_name'  => 'LayerSlider',
			'plugin_path'  => 'LayerSlider/layerslider.php',
			'paid'         => 'https://codecanyon.net/item/layerslider-responsive-wordpress-slider-plugin/1362246',
			'widget_type'  => 'pro',
			'content_type' => 'slider',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/layer-slider/',
			'video_url'    => 'https://youtu.be/I2xpXLyCkkE',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'learnpress-grid',
			'label'       => esc_html__('LearnPress Grid', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'off',
			'plugin_name' => 'learnpress',
			'plugin_path' => 'learnpress/learnpress.php',
			'widget_type' => 'pro',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/learnpress-grid/',
			'video_url'   => '',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'learnpress-carousel',
			'label'        => esc_html__('LearnPress Carousel', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'off',
			'plugin_name'  => 'learnpress',
			'plugin_path'  => 'learnpress/learnpress.php',
			'widget_type'  => 'pro',
			'content_type' => 'carousel',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/learnpress-carousel/',
			'video_url'    => '',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'mailchimp-for-wp',
			'label'       => esc_html__('Mailchimp For WP', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'on',
			'plugin_name' => 'mailchimp-for-wp',
			'plugin_path' => 'mailchimp-for-wp/mailchimp-for-wp.php',
			'widget_type' => 'pro',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/mailchimp-for-wordpress',
			'video_url'   => 'https://youtu.be/AVqliwiyMLg',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'ninja-forms',
			'label'        => esc_html__('Ninja Forms', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'on',
			'plugin_name'  => 'ninja-forms',
			'plugin_path'  => 'ninja-forms/ninja-forms.php',
			'widget_type'  => 'free',
			'content_type' => 'forms',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/ninja-forms/',
			'video_url'    => 'https://youtu.be/rMKAUIy1fKc',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'portfolio-gallery',
			'label'        => esc_html__('Portfolio Gallery', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'on',
			'plugin_name'  => 'bdthemes-portfolio',
			'plugin_path'  => 'bdthemes-portfolio/bdthemes-portfolio.php',
			'paid'         => 'https://bdthemes.com/secure/plugins/bdthemes-portfolio.zip?key=40fb823b8016d31411a7fe281f41044g',
			'widget_type'  => 'pro',
			'content_type' => 'post',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/portfolio-gallery/',
			'video_url'    => 'https://youtu.be/dkKPuZwWFks',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'portfolio-carousel',
			'label'        => esc_html__('Portfolio Carousel', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'on',
			'plugin_name'  => 'bdthemes-portfolio',
			'plugin_path'  => 'bdthemes-portfolio/bdthemes-portfolio.php',
			'paid'         => 'https://bdthemes.com/secure/plugins/bdthemes-portfolio.zip?key=40fb823b8016d31411a7fe281f41044g',
			'widget_type'  => 'pro',
			'content_type' => 'post',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/portfolio-carousel/',
			'video_url'    => 'https://youtu.be/6fMQzv47HTU',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'portfolio-list',
			'label'        => esc_html__('Portfolio List', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'on',
			'plugin_name'  => 'bdthemes-portfolio',
			'plugin_path'  => 'bdthemes-portfolio/bdthemes-portfolio.php',
			'paid'         => 'https://bdthemes.com/secure/plugins/bdthemes-portfolio.zip?key=40fb823b8016d31411a7fe281f41044g',
			'widget_type'  => 'pro',
			'content_type' => 'post',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/portfolio-list/',
			'video_url'    => 'https://youtu.be/WdXZMoEEn4I',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'quform',
			'label'        => esc_html__('QuForm', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'on',
			'plugin_name'  => 'quform',
			'plugin_path'  => 'quform/quform.php',
			'paid'         => 'https://codecanyon.net/item/quform-wordpress-form-builder/706149',
			'widget_type'  => 'pro',
			'content_type' => 'forms',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/quform/',
			'video_url'    => 'https://youtu.be/LM0JtQ58UJM',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'revolution-slider',
			'label'       => esc_html__('Revolution Slider', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'on',
			'plugin_name' => 'revslider',
			'plugin_path' => 'revslider/revslider.php',
			'paid'        => 'https://codecanyon.net/item/slider-revolution-responsive-wordpress-plugin/2751380',
			'widget_type' => 'pro',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/revolution-slider/',
			'video_url'   => 'https://youtu.be/S3bs8FfTBsI',

		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'tablepress',
			'label'       => esc_html__('TablePress', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'on',
			'plugin_name' => 'tablepress',
			'plugin_path' => 'tablepress/tablepress.php',
			'widget_type' => 'pro',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/tablepress/',
			'video_url'   => 'https://youtu.be/TGnc0ap-cWs',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'testimonial-carousel',
			'label'        => esc_html__('Testimonial Carousel', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'on',
			'plugin_name'  => 'bdthemes-testimonials',
			'plugin_path'  => 'bdthemes-testimonials/bdthemes-testimonials.php',
			'paid'         => 'https://bdthemes.com/secure/plugins/bdthemes-testimonials.zip?key=40fb823b8016d31411a7fe281f41044g',
			'widget_type'  => 'pro',
			'content_type' => 'post carousel',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/testimonial-carousel/',
			'video_url'    => 'https://youtu.be/VbojVJzayvE',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'testimonial-grid',
			'label'        => esc_html__('Testimonial Grid', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'on',
			'plugin_name'  => 'bdthemes-testimonials',
			'plugin_path'  => 'bdthemes-testimonials/bdthemes-testimonials.php',
			'paid'         => 'https://bdthemes.com/secure/plugins/bdthemes-testimonials.zip?key=40fb823b8016d31411a7fe281f41044g',
			'widget_type'  => 'free',
			'content_type' => 'post',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/testimonial-grid/',
			'video_url'    => 'https://youtu.be/pYMTXyDn8g4',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'testimonial-slider',
			'label'        => esc_html__('Testimonial Slider', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'on',
			'plugin_name'  => 'bdthemes-testimonials',
			'plugin_path'  => 'bdthemes-testimonials/bdthemes-testimonials.php',
			'paid'         => 'https://bdthemes.com/secure/plugins/bdthemes-testimonials.zip?key=40fb823b8016d31411a7fe281f41044g',
			'widget_type'  => 'pro',
			'content_type' => 'post',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/testimonial-slider/',
			'video_url'    => 'https://youtu.be/pI-DLKNlTGg',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'the-newsletter',
			'label'        => esc_html__('The Newsletter', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'off',
			'plugin_name'  => 'newsletter',
			'plugin_path'  => 'newsletter/plugin.php',
			'widget_type'  => 'pro',
			'content_type' => 'forms',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/the-newsletter/',
			'video_url'    => 'https://youtu.be/nFbzp1Pttf4',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'tutor-lms-course-grid',
			'label'       => esc_html__('Tutor LMS Grid', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'on',
			'plugin_name' => 'tutor',
			'plugin_path' => 'tutor/tutor.php',
			'widget_type' => 'free',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/tutor-lms-course-grid/',
			'video_url'   => 'https://youtu.be/WWCE-_Po1uo',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'        => 'tutor-lms-course-carousel',
			'label'       => esc_html__('Tutor LMS Carousel', 'bdthemes-element-pack'),
			'type'        => 'checkbox',
			'default'     => 'on',
			'plugin_name' => 'tutor',
			'plugin_path' => 'tutor/tutor.php',
			'widget_type' => 'free',
			'demo_url'    => 'https://www.elementpack.pro/demo/element/tutor-lms-course-carousel/',
			'video_url'   => 'https://youtu.be/VYrIYQESjXs',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'wc-products',
			'label'        => esc_html__('Woocommerce Products', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'on',
			'plugin_name'  => 'woocommerce',
			'plugin_path'  => 'woocommerce/woocommerce.php',
			'widget_type'  => 'pro',
			'content_type' => 'ecommerce grid gallery',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/woocommerce-products/',
			'video_url'    => 'https://youtu.be/3VkvEpVaNAM',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'wc-add-to-cart',
			'label'        => esc_html__('WooCommerce Add To Cart', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'on',
			'plugin_name'  => 'woocommerce',
			'plugin_path'  => 'woocommerce/woocommerce.php',
			'widget_type'  => 'pro',
			'content_type' => 'ecommerce',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/woocommerce-add-to-cart/',
			'video_url'    => 'https://youtu.be/1gZJm2-xMqY',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'wc-elements',
			'label'        => esc_html__('WooCommerce Elements', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'on',
			'plugin_name'  => 'woocommerce',
			'plugin_path'  => 'woocommerce/woocommerce.php',
			'widget_type'  => 'pro',
			'content_type' => 'ecommerce grid',
			'demo_url'     => '',
			'video_url'    => '',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'wc-categories',
			'label'        => esc_html__('WooCommerce Categories', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'on',
			'plugin_name'  => 'woocommerce',
			'plugin_path'  => 'woocommerce/woocommerce.php',
			'widget_type'  => 'pro',
			'content_type' => 'ecommerce grid gallery',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/woocommerce-categories/',
			'video_url'    => 'https://youtu.be/SJuArqtnC1U',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'wc-carousel',
			'label'        => esc_html__('WooCommerce Carousel', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'on',
			'plugin_name'  => 'woocommerce',
			'plugin_path'  => 'woocommerce/woocommerce.php',
			'widget_type'  => 'pro',
			'content_type' => 'ecommerce carousel',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/woocommerce-carousel/',
			'video_url'    => 'https://youtu.be/5lxli5E9pc4',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'wc-slider',
			'label'        => esc_html__('WooCommerce Slider', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'on',
			'plugin_name'  => 'woocommerce',
			'plugin_path'  => 'woocommerce/woocommerce.php',
			'widget_type'  => 'pro',
			'content_type' => 'ecommerce slider',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/woocommerce-slider',
			'video_url'    => 'https://youtu.be/ic8p-3jO35U',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'wc-mini-cart',
			'label'        => esc_html__('WooCommerce Mini Cart', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'on',
			'plugin_name'  => 'woocommerce',
			'plugin_path'  => 'woocommerce/woocommerce.php',
			'widget_type'  => 'pro',
			'content_type' => 'ecommerce slider',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/woocommerce-slider',
			'video_url'    => 'https://youtu.be/ic8p-3jO35U',
		];

		$settings_fields['element_pack_third_party_widget'][] = [
			'name'         => 'we-forms',
			'label'        => esc_html__('weForms', 'bdthemes-element-pack'),
			'type'         => 'checkbox',
			'default'      => 'off',
			'plugin_name'  => 'weforms',
			'plugin_path'  => 'weforms/weforms.php',
			'widget_type'  => 'free',
			'content_type' => 'forms',
			'demo_url'     => 'https://www.elementpack.pro/demo/element/we-forms/',
			'video_url'    => 'https://youtu.be/D-vUfbMclOk',
		];

		if (ModuleService::_is_plugin_installed('wpforms-lite', 'wpforms-lite/wpforms.php') === true && is_plugin_active('wpforms/wpforms.php') !== true) {
			$settings_fields['element_pack_third_party_widget'][] = [
				'name'         => 'wp-forms',
				'label'        => esc_html__('Wp Forms', 'bdthemes-element-pack'),
				'type'         => 'checkbox',
				'default'      => 'on',
				'plugin_name'  => 'wpforms-lite',
				'plugin_path'  => 'wpforms-lite/wpforms.php',
				'paid'         => 'https://wpforms.com/pricing/',
				'widget_type'  => 'pro',
				'content_type' => 'forms',
				'demo_url'     => 'https://www.elementpack.pro/demo/element/wp-forms/',
				'video_url'    => 'https://youtu.be/p_FRLsEVNjQ',
			];
		} else {
			$settings_fields['element_pack_third_party_widget'][] = [
				'name'         => 'wp-forms',
				'label'        => esc_html__('Wp Forms', 'bdthemes-element-pack'),
				'type'         => 'checkbox',
				'default'      => 'on',
				'plugin_name'  => 'wpforms',
				'plugin_path'  => 'wpforms/wpforms.php',
				'paid'         => 'https://wpforms.com/pricing/',
				'widget_type'  => 'pro',
				'content_type' => 'forms',
				'demo_url'     => 'https://www.elementpack.pro/demo/element/wp-forms/',
				'video_url'    => 'https://youtu.be/p_FRLsEVNjQ',
			];
		}


		$settings                    = [];
		$settings['settings_fields'] = $settings_fields;

		return $callable($settings);
	}

	private static function _is_plugin_installed($plugin, $plugin_path) {
		$installed_plugins = get_plugins();
		return isset($installed_plugins[$plugin_path]);
	}

	public static function is_module_active($module_id, $options) {
		if (!isset($options[$module_id])) {
			if (file_exists(BDTEP_MODULES_PATH . $module_id . '/module.info.php')) {
				$module_data = require BDTEP_MODULES_PATH . $module_id . '/module.info.php';
				return $module_data['default_activation'];
			}
		} else {
			return $options[$module_id] == 'on';
		}
	}

	public static function is_plugin_active($plugin_path) {
		if ($plugin_path) {
			return is_plugin_active($plugin_path);
		}
	}

	public static function has_module_style($module_id) {
		if (file_exists(BDTEP_MODULES_PATH . $module_id . '/module.info.php')) {
			$module_data = require BDTEP_MODULES_PATH . $module_id . '/module.info.php';

			if (isset($module_data['has_style'])) {
				return $module_data['has_style'];
			}
		}
	}

	public static function has_module_script($module_id) {
		if (file_exists(BDTEP_MODULES_PATH . $module_id . '/module.info.php')) {
			$module_data = require BDTEP_MODULES_PATH . $module_id . '/module.info.php';

			if (isset($module_data['has_script'])) {
				return $module_data['has_script'];
			}
		}
	}
}
