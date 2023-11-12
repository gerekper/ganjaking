<?php
/**
 * UAEL Config.
 *
 * @package UAEL
 */

namespace UltimateElementor\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use UltimateElementor\Classes\UAEL_Helper;

/**
 * Class UAEL_Config.
 */
class UAEL_Config {

	/**
	 * Widget List
	 *
	 * @var widget_list
	 */
	public static $widget_list = null;

	/**
	 * Post skins List
	 *
	 * @var post_skins_list
	 */
	public static $post_skins_list = null;

	/**
	 * Get Widget List.
	 *
	 * @since 0.0.1
	 *
	 * @return array The Widget List.
	 */
	public static function get_widget_list() {
		if ( null === self::$widget_list ) {
			$options_url       = admin_url( 'options-general.php' );
			$integration_url   = add_query_arg(
				array(
					'page'   => UAEL_SLUG,
					'action' => 'integration',
				),
				$options_url
			);
			$post_url          = add_query_arg(
				array(
					'page'   => UAEL_SLUG,
					'action' => 'post',
				),
				$options_url
			);
			self::$widget_list = array(
				'Advanced_Heading'    => array(
					'slug'      => 'uael-advanced-heading',
					'title'     => __( 'Advanced Heading', 'uael' ),
					'keywords'  => array( 'uael', 'heading', 'advanced' ),
					'icon'      => 'uael-icon-advanced-heading',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/advanced-heading/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'preset'    => '6',
					'category'  => 'content',
				),
				'BaSlider'            => array(
					'slug'      => 'uael-ba-slider',
					'title'     => __( 'Before After Slider', 'uael' ),
					'keywords'  => array( 'uael', 'slider', 'before', 'after' ),
					'icon'      => 'uael-icon-before-after-slider',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/before-after-slider/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin',
					'category'  => 'creative',
				),
				'Business_Hours'      => array(
					'slug'      => 'uael-business-hours',
					'title'     => __( 'Business Hours', 'uael' ),
					'keywords'  => array( 'uael', 'business', 'hours', 'schedule' ),
					'icon'      => 'uael-icon-business-hour',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/business-hours/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'preset'    => '5',
					'category'  => 'content',
				),
				'Business_Reviews'    => array(
					'slug'         => 'uael-business-reviews',
					'keywords'     => array( 'uael', 'reviews', 'wp reviews', 'business', 'wp business', 'google', 'rating', 'social', 'yelp' ),
					'title'        => __( 'Business Reviews', 'uael' ),
					'icon'         => 'uael-icon-business-review',
					'title_url'    => '#',
					'default'      => true,
					'doc_url'      => UAEL_DOMAIN . 'docs-category/widgets/business-reviews/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'setting_url'  => $integration_url,
					'setting_text' => __( 'Settings', 'uael' ),
					'category'     => 'seo',
				),
				'CfStyler'            => array(
					'slug'      => 'uael-cf7-styler',
					'title'     => __( 'Contact Form 7 Styler', 'uael' ),
					'keywords'  => array( 'uael', 'form', 'cf7', 'contact', 'styler' ),
					'icon'      => 'uael-icon-contact-form-7',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/contact-form-7-styler/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'form',
				),
				'ContentToggle'       => array(
					'slug'      => 'uael-content-toggle',
					'title'     => __( 'Content Toggle', 'uael' ),
					'keywords'  => array( 'uael', 'toggle', 'content', 'show', 'hide' ),
					'icon'      => 'uael-icon-content-toggle',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/content-toggle/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'content',
				),
				'Countdown'           => array(
					'slug'      => 'uael-countdown',
					'title'     => __( 'Countdown Timer', 'uael' ),
					'keywords'  => array( 'uael', 'count', 'timer', 'countdown' ),
					'icon'      => 'uael-icon-countdown-timer',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/countdown-timer/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'preset'    => '6',
					'category'  => 'creative',
				),
				'Dual_Heading'        => array(
					'slug'      => 'uael-dual-color-heading',
					'title'     => __( 'Dual Color Heading', 'uael' ),
					'keywords'  => array( 'uael', 'dual', 'heading', 'color' ),
					'icon'      => 'uael-icon-dual-color-heading',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/dual-color-heading/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin',
					'preset'    => '5',
					'category'  => 'creative',
				),
				'Fancy_Heading'       => array(
					'slug'      => 'uael-fancy-heading',
					'title'     => __( 'Fancy Heading', 'uael' ),
					'keywords'  => array( 'uael', 'fancy', 'heading', 'ticking', 'animate' ),
					'icon'      => 'uael-icon-fancy-heading',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/fancy-heading/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'preset'    => '5',
					'category'  => 'creative',
				),
				'FAQ'                 => array(
					'slug'      => 'uael-faq',
					'title'     => __( 'FAQ Schema', 'uael' ),
					'keywords'  => array( 'uael', 'faq', 'schema', 'question', 'answer', 'accordion', 'toggle' ),
					'icon'      => 'uael-icon-faq-schema',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/faq/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'preset'    => '5',
					'category'  => 'seo',
				),
				'GoogleMap'           => array(
					'slug'         => 'uael-google-map',
					'title'        => __( 'Google Map', 'uael' ),
					'keywords'     => array( 'uael', 'google', 'map', 'location', 'address' ),
					'icon'         => 'uael-icon-google-map',
					'title_url'    => '#',
					'default'      => true,
					'setting_url'  => $integration_url,
					'setting_text' => __( 'Settings', 'uael' ),
					'doc_url'      => UAEL_DOMAIN . 'docs-category/widgets/google-maps/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'     => 'content',
				),
				'GfStyler'            => array(
					'slug'      => 'uael-gf-styler',
					'title'     => __( 'Gravity Form Styler', 'uael' ),
					'keywords'  => array( 'uael', 'form', 'gravity', 'gf', 'styler' ),
					'icon'      => 'uael-icon-gravity-form-styler',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/gravity-form-styler/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'form',
				),
				'Hotspot'             => array(
					'slug'      => 'uael-hotspot',
					'title'     => __( 'Hotspot', 'uael' ),
					'keywords'  => array( 'uael', 'hotspot', 'tour' ),
					'icon'      => 'uael-icon-hotspot',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/hotspot/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'creative',
				),
				'HowTo'               => array(
					'slug'      => 'uael-how-to',
					'title'     => __( 'How-to Schema', 'uael' ),
					'keywords'  => array( 'uael', 'how-to', 'howto', 'schema', 'steps', 'supply', 'tools', 'steps', 'cost' ),
					'icon'      => 'uael-icon-how-to-schema',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/how-to-schema/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'seo',
				),
				'Image_Gallery'       => array(
					'slug'      => 'uael-image-gallery',
					'title'     => __( 'Image Gallery', 'uael' ),
					'keywords'  => array( 'uael', 'image', 'gallery', 'carousel', 'slider', 'layout' ),
					'icon'      => 'uael-icon-image-gallery',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/image-gallery/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'content',
				),
				'Infobox'             => array(
					'slug'      => 'uael-infobox',
					'title'     => __( 'Info Box', 'uael' ),
					'keywords'  => array( 'uael', 'info', 'box', 'bar' ),
					'icon'      => 'uael-icon-info-box',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/info-box/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'preset'    => '5',
					'category'  => 'content',
				),
				'Instagram_Feed'      => array(
					'slug'         => 'uael-instagram-feed',
					'title'        => __( 'Instagram Feed', 'uael' ),
					'keywords'     => array( 'insta', 'instagram', 'feed', 'social' ),
					'icon'         => 'uael-icon-instagram-feed',
					'title_url'    => '#',
					'default'      => true,
					'setting_text' => __( 'Settings', 'uael' ),
					'setting_url'  => $integration_url,
					'doc_url'      => UAEL_DOMAIN . 'docs-category/widgets/instagram-feed/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'     => 'creative',
				),
				'LoginForm'           => array(
					'slug'         => 'uael-login-form',
					'title'        => __( 'Login Form', 'uael' ),
					'keywords'     => array( 'uael', 'form', 'login', 'facebook', 'google', 'user', 'fblogin' ),
					'icon'         => 'uael-icon-login-form',
					'title_url'    => '#',
					'default'      => true,
					'setting_text' => __( 'Settings', 'uael' ),
					'setting_url'  => $integration_url,
					'doc_url'      => UAEL_DOMAIN . 'docs-category/widgets/login-form/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'preset'       => '5',
					'category'     => 'form',
				),
				'Marketing_Button'    => array(
					'slug'      => 'uael-marketing-button',
					'title'     => __( 'Marketing Button', 'uael' ),
					'keywords'  => array( 'uael', 'button', 'marketing', 'call to action', 'cta' ),
					'icon'      => 'uael-icon-marketing-button',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/marketing-button/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'preset'    => '5',
					'category'  => 'creative',
				),
				'Modal_Popup'         => array(
					'slug'      => 'uael-modal-popup',
					'title'     => __( 'Modal Popup', 'uael' ),
					'keywords'  => array( 'uael', 'modal', 'popup', 'lighbox' ),
					'icon'      => 'uael-icon-modal-popup',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/modal-popup/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'content',
				),
				'Buttons'             => array(
					'slug'      => 'uael-buttons',
					'title'     => __( 'Multi Buttons', 'uael' ),
					'keywords'  => array( 'uael', 'buttons', 'multi', 'call to action', 'cta' ),
					'icon'      => 'uael-icon-multi-button',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/multi-buttons/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'preset'    => '3',
					'category'  => 'creative',
				),
				'Nav_Menu'            => array(
					'slug'      => 'uael-nav-menu',
					'title'     => __( 'Navigation Menu', 'uael' ),
					'keywords'  => array( 'uael', 'menu', 'nav', 'navigation', 'mega' ),
					'icon'      => 'uael-icon-navigation-menu',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/navigation-menu/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'creative',
				),
				'Offcanvas'           => array(
					'slug'      => 'uael-offcanvas',
					'title'     => __( 'Off - Canvas', 'uael' ),
					'keywords'  => array( 'uael', 'off', 'offcanvas', 'off-canvas', 'canvas', 'template', 'floating' ),
					'icon'      => 'uael-icon-off-canvas',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/off-canvas/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'creative',
				),
				'Posts'               => array(
					'slug'         => 'uael-posts',
					'title'        => __( 'Posts', 'uael' ),
					'keywords'     => array( 'uael', 'post', 'grid', 'masonry', 'carousel', 'content grid', 'content' ),
					'icon'         => 'uael-icon-posts',
					'title_url'    => '#',
					'default'      => true,
					'setting_url'  => $post_url,
					'setting_text' => __( 'Settings', 'uael' ),
					'doc_url'      => UAEL_DOMAIN . 'docs-category/widgets/posts/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'     => 'content',
				),
				'Price_Table'         => array(
					'slug'      => 'uael-price-table',
					'title'     => __( 'Price Box', 'uael' ),
					'keywords'  => array( 'uael', 'price', 'table', 'box', 'pricing' ),
					'icon'      => 'uael-icon-price-box',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/price-box/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'preset'    => '5',
					'category'  => 'creative',
				),
				'Price_List'          => array(
					'slug'      => 'uael-price-list',
					'title'     => __( 'Price List', 'uael' ),
					'keywords'  => array( 'uael', 'price', 'list', 'pricing' ),
					'icon'      => 'uael-icon-price-list',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/price-list/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'preset'    => '5',
					'category'  => 'content',
				),
				'Retina_Image'        => array(
					'slug'      => 'uael-retina-image',
					'title'     => __( 'Retina Image', 'uael' ),
					'keywords'  => array( 'uael', 'retina', 'image', '2ximage' ),
					'icon'      => 'uael-icon-retina-image',
					'title_url' => '#',
					'default'   => true,

					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/retina-image/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'creative',
				),
				'SocialShare'         => array(
					'slug'         => 'uael-social-share',
					'title'        => __( 'Social Share', 'uael' ),
					'keywords'     => array( 'uael', 'sharing', 'social', 'icon', 'button', 'like' ),
					'icon'         => 'uael-icon-social-share',
					'title_url'    => '#',
					'default'      => true,
					'setting_text' => __( 'Settings', 'uael' ),
					'setting_url'  => admin_url( 'options-general.php?page=' . UAEL_SLUG . '&action=integration' ),
					'doc_url'      => UAEL_DOMAIN . 'docs-category/widgets/social-share/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'preset'       => '5',
					'category'     => 'creative',
				),
				'Table'               => array(
					'slug'      => 'uael-table',
					'title'     => __( 'Table', 'uael' ),
					'keywords'  => array( 'uael', 'table', 'sort', 'search' ),
					'icon'      => 'uael-icon-table',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/table/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'content',
				),
				'Table_of_Contents'   => array(
					'slug'      => 'uael-table-of-contents',
					'title'     => __( 'Table of Contents', 'uael' ),
					'keywords'  => array( 'uael', 'table of contents', 'content', 'list', 'toc', 'index' ),
					'icon'      => 'uael-icon-table-of-content',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/table-of-contents/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'seo',
				),
				'Team_Member'         => array(
					'slug'      => 'uael-team-member',
					'title'     => __( 'Team Member', 'uael' ),
					'keywords'  => array( 'uael', 'team', 'member' ),
					'icon'      => 'uael-icon-team-member',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/team-member/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'preset'    => '5',
					'category'  => 'creative',
				),
				'Timeline'            => array(
					'slug'      => 'uael-timeline',
					'title'     => __( 'Timeline', 'uael' ),
					'keywords'  => array( 'uael', 'timeline', 'history', 'scroll', 'post', 'content timeline' ),
					'icon'      => 'uael-icon-timeline',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/timeline/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'preset'    => '5',
					'category'  => 'creative',
				),
				'Twitter'             => array(
					'slug'         => 'uael-twitter',
					'title'        => __( 'Twitter Feed', 'uael' ),
					'keywords'     => array( 'uael', 'twitter' ),
					'icon'         => 'uael-icon-twitter-feed-icon',
					'title_url'    => '#',
					'setting_url'  => $integration_url,
					'setting_text' => __( 'Settings', 'uael' ),
					'default'      => true,
					'doc_url'      => UAEL_DOMAIN . 'docs-category/widgets/twitter-feed/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'     => 'creative',
				),
				'RegistrationForm'    => array(
					'slug'         => 'uael-registration-form',
					'title'        => __( 'User Registration Form', 'uael' ),
					'keywords'     => array( 'uael', 'form', 'register', 'registration', 'user' ),
					'icon'         => 'uael-icon-registration-form',
					'title_url'    => '#',
					'default'      => true,
					'setting_url'  => $integration_url,
					'setting_text' => __( 'Settings', 'uael' ),
					'doc_url'      => UAEL_DOMAIN . 'docs-category/widgets/user-registration-form/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'preset'       => '5',
					'category'     => 'form',
				),
				'Video'               => array(
					'slug'      => 'uael-video',
					'title'     => __( 'Video', 'uael' ),
					'keywords'  => array( 'uael', 'video', 'youtube', 'vimeo', 'wistia', 'sticky', 'drag', 'float', 'subscribe' ),
					'icon'      => 'uael-icon-video',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/video/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'content',
				),
				'Video_Gallery'       => array(
					'slug'      => 'uael-video-gallery',
					'title'     => __( 'Video Gallery', 'uael' ),
					'keywords'  => array( 'uael', 'video', 'youtube', 'wistia', 'gallery', 'vimeo' ),
					'icon'      => 'uael-icon-video-gallery',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/video-gallery/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'content',
				),
				'Welcome_Music'       => array(
					'slug'      => 'uael-welcome-music',
					'title'     => __( 'Welcome Music', 'uael' ),
					'keywords'  => array( 'uael', 'christmas', 'music', 'background', 'audio', 'welcome' ),
					'icon'      => 'uael-icon-welcome-music',
					'title_url' => '#',
					'default'   => false,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/welcome-music/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'creative',
				),
				'Woo_Add_To_Cart'     => array(
					'slug'      => 'uael-woo-add-to-cart',
					'title'     => __( 'Woo - Add To Cart', 'uael' ),
					'keywords'  => array( 'uael', 'woo', 'cart', 'add to cart', 'products' ),
					'icon'      => 'uael-icon-woo-add-to-cart',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/woo-add-to-cart/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'woo',
				),
				'Woo_Categories'      => array(
					'slug'      => 'uael-woo-categories',
					'title'     => __( 'Woo - Categories', 'uael' ),
					'keywords'  => array( 'uael', 'woo', 'categories', 'taxomonies', 'products' ),
					'icon'      => 'uael-icon-woo-category',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/woo-categories/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'woo',
				),
				'Woo_Checkout'        => array(
					'slug'      => 'uael-woo-checkout',
					'title'     => __( 'Woo - Checkout', 'uael' ),
					'keywords'  => array( 'uael', 'woo', 'checkout', 'page', 'check' ),
					'icon'      => 'uael-icon-woo-checkout-1',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/woo-checkout/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'woo',
				),
				'Woo_Mini_Cart'       => array(
					'slug'      => 'uael-mini-cart',
					'title'     => __( 'Woo - Mini Cart', 'uael' ),
					'keywords'  => array( 'woo', 'woocommerce', 'cart', 'mini', 'minicart' ),
					'icon'      => 'uael-icon-woo-mini-cart',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/woo-mini-cart/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'woo',
				),
				'Woo_Products'        => array(
					'slug'      => 'uael-woo-products',
					'title'     => __( 'Woo - Products', 'uael' ),
					'keywords'  => array( 'uael', 'woo', 'products' ),
					'icon'      => 'uael-icon-woo-product',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/woo-products/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'woo',
				),
				'FfStyler'            => array(
					'slug'      => 'uael-ff-styler',
					'title'     => __( 'WP Fluent Forms Styler', 'uael' ),
					'keywords'  => array( 'uael', 'fluent', 'forms', 'wp' ),
					'icon'      => 'uael-icon-fluent-form-styler',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/wp-fluent-forms-styler/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'form',
				),
				'WpfStyler'           => array(
					'slug'      => 'uael-wpf-styler',
					'title'     => __( 'WPForms Styler', 'uael' ),
					'keywords'  => array( 'uael', 'form', 'wp', 'wpform', 'styler' ),
					'icon'      => 'uael-icon-wp-form-styler',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/wpforms-styler/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'form',
				),
				'DisplayConditions'   => array(
					'slug'         => 'uael-display-conditions',
					'title'        => __( 'Display Conditions', 'uael' ),
					'keywords'     => array(),
					'icon'         => '',
					'title_url'    => '#',
					'default'      => true,
					'setting_text' => __( 'Settings', 'uael' ),
					'setting_url'  => $integration_url,
					'doc_url'      => UAEL_DOMAIN . 'docs-category/widgets/display-conditions/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'     => 'extension',
				),
				'Particles'           => array(
					'slug'      => 'uael-particles',
					'title'     => __( 'Particle Backgrounds', 'uael' ),
					'keywords'  => array(),
					'icon'      => '',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/particles-background-extension/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'extension',
				),
				'PartyPropzExtension' => array(
					'slug'      => 'uael-party-propz-extension',
					'title'     => __( 'Party Propz', 'uael' ),
					'keywords'  => array(),
					'icon'      => '',
					'title_url' => '#',
					'default'   => false,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/party-propz-extensions/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'extension',
				),
				'SectionDivider'      => array(
					'slug'      => 'uael-section-divider',
					'title'     => __( 'Shape Divider', 'uael' ),
					'keywords'  => array(),
					'icon'      => '',
					'title_url' => '#',
					'default'   => false,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/uae-shape-dividers/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'extension',
				),
				'Cross_Domain'        => array(
					'slug'      => 'uael-cross-domain-copy-paste',
					'title'     => __( 'Cross-Site Copy Paste', 'uael' ),
					'keywords'  => array(),
					'icon'      => '',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/features/cross-site-copy-paste/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'feature',
				),
				'Presets'             => array(
					'slug'      => 'uael-presets',
					'title'     => __( 'Presets', 'uael' ),
					'keywords'  => array(),
					'icon'      => '',
					'title_url' => '#',
					'default'   => true,
					'doc_url'   => UAEL_DOMAIN . 'docs-category/features/presets/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
					'category'  => 'feature',
				),
			);
		}

		if ( class_exists( 'Caldera_Forms' ) || class_exists( 'Caldera_Forms_Forms' ) ) {
			$forms = \Caldera_Forms_Forms::get_forms( true );
			if ( ! empty( $forms ) ) {
				$caldera = array(
					'CafStyler' => array(
						'slug'      => 'uael-caf-styler',
						'title'     => __( 'Caldera Form Styler', 'uael' ),
						'keywords'  => array( 'uael', 'caldera', 'form', 'styler' ),
						'icon'      => 'uael-icon-caldera-form-styler',
						'title_url' => '#',
						'default'   => true,
						'doc_url'   => UAEL_DOMAIN . 'docs-category/widgets/caldera-form-styler/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin',
						'category'  => 'form',
					),
				);

				self::$widget_list = array_merge_recursive( self::$widget_list, $caldera );
			}
		}

		return self::$widget_list;
	}

	/**
	 * Get Post skins.
	 *
	 * @since 1.21.0
	 *
	 * @return array Post skins.
	 */
	public static function get_post_skin_list() {

		if ( null === self::$post_skins_list ) {
			self::$post_skins_list = array(
				'Skin_Card'     => array(
					'slug'    => 'uael-skin-card',
					'title'   => __( 'Card Skin', 'uael' ),
					'default' => true,
					'image'   => UAEL_URL . 'assets/img/uae-post-skin-card.png',
				),
				'Skin_Feed'     => array(
					'slug'    => 'uael-skin-feed',
					'title'   => __( 'Creative Feed Skin', 'uael' ),
					'default' => true,
					'image'   => UAEL_URL . 'assets/img/uae-post-skin-feed.png',
				),
				'Skin_News'     => array(
					'slug'    => 'uael-skin-news',
					'title'   => __( 'News Skin', 'uael' ),
					'default' => true,
					'image'   => UAEL_URL . 'assets/img/uae-post-skin-news.png',
				),
				'Skin_Business' => array(
					'slug'    => 'uael-skin-business',
					'title'   => __( 'Business Skin', 'uael' ),
					'default' => true,
					'image'   => UAEL_URL . 'assets/img/uae-post-skin-business.png',
				),
			);
		}

		return self::$post_skins_list;
	}

	/**
	 * Returns Script array.
	 *
	 * @return array()
	 * @since 0.0.1
	 */
	public static function get_widget_script() {
		$folder = UAEL_Helper::get_js_folder();
		$suffix = UAEL_Helper::get_js_suffix();

		$js_files = array(
			'uael-frontend-script'   => array(
				'path'      => 'assets/' . $folder . '/uael-frontend' . $suffix . '.js',
				'dep'       => array( 'jquery' ),
				'in_footer' => true,
			),
			'uael-modal-popup'       => array(
				'path'      => 'assets/' . $folder . '/uael-modal-popup' . $suffix . '.js',
				'dep'       => array( 'jquery', 'uael-cookie-lib' ),
				'in_footer' => true,
			),
			'uael-offcanvas'         => array(
				'path'      => 'assets/' . $folder . '/uael-offcanvas' . $suffix . '.js',
				'dep'       => array( 'jquery' ),
				'in_footer' => true,
			),
			'uael-google-maps'       => array(
				'path'      => 'assets/' . $folder . '/uael-google-map' . $suffix . '.js',
				'dep'       => array( 'jquery' ),
				'in_footer' => true,
			),
			'uael-posts'             => array(
				'path'      => 'assets/' . $folder . '/uael-posts' . $suffix . '.js',
				'dep'       => array( 'jquery', 'imagesloaded' ),
				'in_footer' => true,
			),
			'uael-business-reviews'  => array(
				'path'      => 'assets/' . $folder . '/uael-business-reviews' . $suffix . '.js',
				'dep'       => array( 'jquery', 'imagesloaded' ),
				'in_footer' => true,
			),
			'uael-woocommerce'       => array(
				'path'      => 'assets/' . $folder . '/uael-woocommerce' . $suffix . '.js',
				'dep'       => array( 'jquery' ),
				'in_footer' => true,
			),
			'uael-table'             => array(
				'path'      => 'assets/' . $folder . '/uael-table' . $suffix . '.js',
				'dep'       => array( 'jquery' ),
				'in_footer' => true,
			),
			'uael-table-of-contents' => array(
				'path'      => 'assets/' . $folder . '/uael-table-of-contents' . $suffix . '.js',
				'dep'       => array( 'jquery' ),
				'in_footer' => true,
			),
			'uael-registration'      => array(
				'path'      => 'assets/' . $folder . '/uael-registration' . $suffix . '.js',
				'dep'       => array( 'jquery' ),
				'in_footer' => true,
			),
			'uael-countdown'         => array(
				'path'      => 'assets/' . $folder . '/uael-countdown' . $suffix . '.js',
				'dep'       => array( 'jquery' ),
				'in_footer' => true,
			),
			'uael-nav-menu'          => array(
				'path'      => 'assets/' . $folder . '/uael-nav-menu' . $suffix . '.js',
				'dep'       => array( 'jquery' ),
				'in_footer' => true,
			),
			'uael-faq'               => array(
				'path'      => 'assets/' . $folder . '/uael-faq' . $suffix . '.js',
				'dep'       => array( 'jquery' ),
				'in_footer' => true,
			),
			'uael-particles'         => array(
				'path'      => 'assets/' . $folder . '/uael-particles' . $suffix . '.js',
				'dep'       => array( 'jquery' ),
				'in_footer' => true,
			),
			'uael-social-share'      => array(
				'path'      => 'assets/' . $folder . '/uael-social-share' . $suffix . '.js',
				'dep'       => array( 'jquery' ),
				'in_footer' => true,
			),
			/* Libraries */
			'uael-hotspot'           => array(
				'path'      => 'assets/lib/tooltipster/tooltipster.min.js',
				'dep'       => array( 'jquery' ),
				'in_footer' => true,
			),
			'uael-datatable'         => array(
				'path'      => 'assets/lib/jquery-datatables/jquery.datatables.min.js',
				'dep'       => array( 'jquery' ),
				'in_footer' => true,
			),
			'uael-twenty-twenty'     => array(
				'path'      => 'assets/lib/jquery-twentytwenty/jquery_twentytwenty.min.js',
				'dep'       => array( 'jquery' ),
				'in_footer' => true,
			),
			'uael-isotope'           => array(
				'path'      => 'assets/lib/isotope/isotope.min.js',
				'dep'       => array( 'jquery' ),
				'in_footer' => true,
			),
			'uael-move'              => array(
				'path'      => 'assets/lib/jquery-event-move/jquery_event_move.min.js',
				'dep'       => array( 'jquery' ),
				'in_footer' => true,
			),
			'uael-fancytext-typed'   => array(
				'path'      => 'assets/lib/typed/typed.min.js',
				'dep'       => array( 'jquery' ),
				'in_footer' => true,
			),
			'uael-element-resize'    => array(
				'path'      => 'assets/lib/jquery-element-resize/jquery_resize.min.js',
				'dep'       => array( 'jquery' ),
				'in_footer' => true,
			),
			'uael-fancytext-slidev'  => array(
				'path'      => 'assets/lib/rvticker/rvticker.min.js',
				'dep'       => array( 'jquery' ),
				'in_footer' => true,
			),
			'uael-cookie-lib'        => array(
				'path'      => 'assets/lib/js-cookie/js_cookie.min.js',
				'dep'       => array( 'jquery' ),
				'in_footer' => true,
			),
			'uael-element-resize'    => array(
				'path'      => 'assets/lib/jquery-element-resize/jquery_resize.min.js',
				'dep'       => array( 'jquery' ),
				'in_footer' => true,
			),
			'uael-infinitescroll'    => array(
				'path'      => 'assets/lib/infinitescroll/jquery.infinitescroll.min.js',
				'dep'       => array( 'jquery' ),
				'in_footer' => true,
			),
			'uael-fancybox'          => array(
				'path'      => 'assets/lib/fancybox/jquery_fancybox.min.js',
				'dep'       => array( 'jquery' ),
				'in_footer' => true,
			),
			'uael-justified'         => array(
				'path'      => 'assets/lib/justifiedgallery/justifiedgallery.min.js',
				'dep'       => array( 'jquery', 'uael-frontend-script' ),
				'in_footer' => true,
			),
			'uael-slick'             => array(
				'path'      => 'assets/lib/slick/slick.min.js',
				'dep'       => array( 'jquery' ),
				'in_footer' => true,
			),
		);

		return $js_files;
	}

	/**
	 * Returns Style array.
	 *
	 * @return array()
	 * @since 0.0.1
	 */
	public static function get_widget_style() {

		$is_rtl = is_rtl();

		if ( ( defined( 'UAE_DEBUG' ) && UAE_DEBUG ) ) {
			$css_files = UAEL_Helper::get_active_widget_stylesheet();
		} else {
			$path = $is_rtl ? 'assets/min-css/uael-frontend-rtl.min.css' : 'assets/min-css/uael-frontend.min.css';

			$css_files = array(
				'uael-frontend' => array(
					'path' => $path,
					'dep'  => array(),
				),
			);
		}

		return $css_files;
	}
}
