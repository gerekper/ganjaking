<?php
/**
 * For Developers, Support Soft Mode. Now this mode is
 * experimental, ongoing features in Porto Theme.
 *
 * @author     P-THEMES
 * @package    Porto
 * @subpackage Core
 * @since      2.3.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Porto Soft Mode(Legacy Mode) Class
 *
 * @since 2.3.0
 */
class Porto_Soft_Mode {
	/**
	 * The Instance Object.
	 *
	 * @since 2.3.0
	 */
	public static $instance;

	/**
	 * Is legacy Mode?
	 *
	 * @param bool
	 * @since 2.3.0
	 */
	public $legacy_mode;

	/**
	 * If active soft mode, should remove
	 *
	 * @since 2.3.0
	 */
	public static $should_remove = array(
		'header-type',
		'search-cats',
		'search-cats-mobile',
		'search-placeholder',
		'search-sub-cats',

		'show-header-top',
		'show-sticky-logo',
		'change-header-logo',
		'show-sticky-searchform',
		'show-sticky-minicart',
		'show-sticky-menu-custom-content',
		'show-sticky-contact-info',

		'show-header-tooltip',
		'header-tooltip',
		'minicart-type',
		'minicart-icon',
		'minicart-content',
		'welcome-msg',
		'header-contact-info',
		'header-copyright',
		'wl-offcanvas',

		'show-account-dropdown',
		'account-menu-font',
		'account-dropdown-bgc',
		'account-dropdown-hbgc',
		'account-dropdown-lc',

		'menu-align',
		'menu-block',

		'show-footer-tooltip',
		'footer-tooltip',
		'footer-type',
		'footer-customize',
		'footer-widget1',
		'footer-widget2',
		'footer-widget3',
		'footer-widget4',
		'footer-logo',
		'footer-copyright',
		'footer-copyright-pos',
		'footer-payments',
		'footer-payments-image',
		'footer-payments-image-alt',
		'footer-payments-link',

		// Skin
		'footer-font',
		'footer-heading-font',

		'body-bg',
		'body-bg-gradient',
		'body-bg-gcolor',
		'content-bg',
		'content-bg-gradient',
		'content-bg-gcolor',
		'content-bottom-bg',
		'content-bottom-bg-gradient',
		'content-bottom-bg-gcolor',
		'content-bottom-padding',

		'header-wrap-bg',
		'header-wrap-bg-gradient',
		'header-wrap-bg-gcolor',
		'header-bg',
		'header-bg-gradient',
		'header-bg-gcolor',
		'header-text-color',
		'header-link-color',
		'header-top-border',
		'header-margin',
		'header-main-padding',
		'header-main-padding-mobile',
		'sticky-header-bg-gradient',
		'sticky-header-bg-gcolor',
		'header-opacity',
		'searchform-opacity',
		'menuwrap-opacity',
		'menu-opacity',
		'header-fixed-show-bottom',
		'header-top-bg-color',
		'header-top-height',
		'header-top-font-size',
		'header-top-bottom-border',
		'header-top-text-color',
		'header-top-link-color',
		'header-top-menu-padding',
		'header-top-menu-hide-sep',
		'header-bottom-bg-color',
		'header-bottom-container-bg-color',
		'header-bottom-height',
		'header-bottom-text-color',
		'header-bottom-link-color',
		'side-social-bg-color',
		'side-social-color',
		'side-copyright-color',

		'mainmenu-wrap-bg-color',
		'mainmenu-wrap-bg-color-sticky',
		'mainmenu-wrap-padding',
		'mainmenu-bg-color',
		'menu-custom-text-color',
		'menu-custom-link',
		'footer-bg',
		'footer-bg-gradient',
		'footer-bg-gcolor',
		'footer-parallax',
		'footer-parallax-speed',
		'footer-main-bg',
		'footer-main-bg-gradient',
		'footer-main-bg-gcolor',
		'footer-heading-color',
		'footer-label-color',
		'footer-text-color',
		'footer-link-color',
		'footer-top-bg',
		'footer-top-bg-gradient',
		'footer-top-bg-gcolor',
		'footer-top-padding',
		'footer-bottom-bg',
		'footer-bottom-bg-gradient',
		'footer-bottom-bg-gcolor',
		'footer-bottom-text-color',
		'footer-bottom-link-color',
		'footer-opacity',
		'footer-social-bg-color',
		'footer-social-link-color',

		'post-format',
		'post-zoom',
		'post-metas',
		'post-meta-position',
		'post-layout',
		'post-style',
		'grid-columns',
		'post-link',
		'blog-infinite',
		'blog-post-share',
		'blog-post-share-position',
		'blog-excerpt',
		'blog-excerpt-length',
		'blog-excerpt-base',
		'blog-excerpt-type',
		'blog-date-format',
		'blog-title',
		'blog-banner_pos',
		'blog-footer_view',
		'blog-banner_type',
		'blog-master_slider',
		'blog-rev_slider',
		'blog-banner_block',
		'blog-content_top',
		'blog-content_inner_top',
		'blog-content_inner_bottom',
		'blog-content_bottom',
		'post-banner-block',
		'post-content-layout',
		'post-replace-pos',
		'post-title-style',
		'post-slideshow',
		'post-title',
		'post-share',
		'post-share-position',
		'post-author',
		'post-comments',
		'post-related',
		'post-related-count',
		'post-related-orderby',
		'post-related-cols',
		'post-backto-blog',
		'post-content_bottom',
		'post-related-style',
		'post-related-excerpt-length',
		'post-related-thumb-bg',
		'post-related-thumb-image',
		'post-related-thumb-borders',
		'post-related-author',
		'post-related-btn-style',
		'post-related-btn-size',
		'post-related-btn-color',
		'portfolio-zoom',
		'portfolio-metas',
		'portfolio-subtitle',
		'portfolio-title',
		'portfolio-archive-ajax',
		'portfolio-archive-ajax-modal',
		'portfolio-infinite',
		'portfolio-cat-orderby',
		'portfolio-cat-order',
		'portfolio-cat-sort-pos',
		'portfolio-cat-sort-style',
		'portfolio-cat-ft',
		'portfolio-archive-image-counter',
		'portfolio-layout',
		'portfolio-archive-masonry-ratio',
		'portfolio-grid-columns',
		'portfolio-grid-view',
		'portfolio-archive-thumb',
		'portfolio-archive-thumb-style',
		'portfolio-archive-thumb-bg',
		'portfolio-archive-thumb-image',
		'portfolio-archive-readmore',
		'portfolio-archive-readmore-label',
		'portfolio-archive-link-zoom',
		'portfolio-archive-img-lightbox-thumb',
		'portfolio-archive-link',
		'portfolio-archive-all-images',
		'portfolio-archive-images-count',
		'portfolio-archive-zoom',
		'portfolio-external-link',
		'portfolio-show-content',
		'portfolio-show-testimonial',
		'portfolio-excerpt',
		'portfolio-excerpt-length',
		'portfolio-banner-block',
		'portfolio-page-nav',
		'portfolio-image-count',
		'portfolio-content-layout',
		'portfolio-slider',
		'portfolio-slider-thumbs-count',
		'portfolio-share',
		'portfolio-author',
		'portfolio-comments',
		'portfolio-related',
		'portfolio-related-count',
		'portfolio-related-orderby',
		'portfolio-related-cols',
		'portfolio-content_bottom',
		'portfolio-related-style',
		'portfolio-related-thumb',
		'portfolio-related-thumb-bg',
		'portfolio-related-thumb-image',
		'portfolio-related-link',
		'portfolio-related-show-content',
		'event-title',
		'event-sub-title',
		'event-archive-layout',
		'event-archive-countdown',
		'event-excerpt',
		'event-excerpt-length',
		'event-readmore',
		'event-banner-block',
		'event-single-countdown',
		'member-zoom',
		'member-social-target',
		'member-social-nofollow',
		'member-title',
		'member-sub-title',
		'member-archive-ajax',
		'member-archive-ajax-modal',
		'member-infinite',
		'member-cat-orderby',
		'member-cat-order',
		'member-cat-sort-pos',
		'member-cat-sort-style',
		'member-cat-ft',
		'member-view-type',
		'member-columns',
		'custom-member-zoom',
		'member-image-size',
		'member-archive-readmore',
		'member-archive-readmore-label',
		'member-external-link',
		'member-overview',
		'member-excerpt',
		'member-excerpt-length',
		'member-socials',
		'member-social-link-style',
		'member-page-style',
		'member-banner-block',
		'member-related',
		'member-related-count',
		'member-related-orderby',
		'member-related-cols',
		'single-member-socials',
		'single-member-social-link-style',
		'member-socials-pos',
		'member-content_bottom',

		'product-infinite',
		'category-view-mode',
		'shop-product-cols',
		'shop-product-cols-mobile',
		'product-cols',
		'product-cols-mobile',
		'cat-view-type',
		'category-image-hover',
		'category-addlinks-pos',
		'product-categories',
		'product-review',
		'product-price',
		'product-desc',
		'product-wishlist',
		'product-quickview',
		'product-compare',
		'product-single-content-layout',
		'product-single-content-builder',
		'product-nav',
		'product-tabs-pos',
		'product-short-desc',
		'product-related',
		'product-related-count',
		'product-related-cols',
		'product-upsells',
		'product-upsells-count',
		'product-upsells-cols',
		'product-share',
		'product-content_bottom',
		'product-sticky-addcart',
	);

	/**
	 * Get the instance.
	 *
	 * @since 2.3.0
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * The Constructor.
	 *
	 * @since 2.3.0
	 */
	public function __construct() {
		global $porto_settings_optimize;
		if ( empty( $porto_settings_optimize ) ) {
			if ( ! is_customize_preview() ) {
				$porto_settings_optimize = get_option( 'porto_settings_optimize', array() );
			} else {
				$porto_settings_optimize = array();
			}
		}
		$this->init();
	}

	/**
	 * Initialize.
	 *
	 * @since 2.3.0
	 */
	public function init() {
		global $porto_settings_optimize;
		if ( isset( $porto_settings_optimize['legacy_mode'] ) ) {
			$this->legacy_mode = $porto_settings_optimize['legacy_mode'];
		} else {
			$this->legacy_mode = true;
		}
		add_filter( 'porto_legacy_mode', array( $this, 'get_legacy_mode' ) );

		if ( $this->legacy_mode ) {
			add_filter( 'porto_view_meta_fields', array( $this, 'add_meta_fields' ) );
			add_filter( 'porto_skin_meta_fields', array( $this, 'add_skin_meta_fields' ), 10, 2 );
			add_filter( 'porto_product_meta_fields', array( $this, 'add_product_meta_fields' ) );
		}

		// Override function
		$legacy_mode = $this->legacy_mode;
		require_once PORTO_SOFT_MODE_PATH . 'override.php';
	}

	/**
	 * Get the legacy mode.
	 *
	 * @since 2.3.0
	 */
	public function get_legacy_mode() {
		return $this->legacy_mode;
	}

	/**
	 * Add meta fields.
	 *
	 * @since 2.3.0
	 */
	public function add_meta_fields( $fields ) {
		// Add Meta fields in legacy mode.
		$banner_pos     = porto_ct_banner_pos();
		$banner_type    = porto_ct_banner_type();
		$header_view    = porto_ct_header_view();
		$footer_view    = porto_ct_footer_view();
		$master_sliders = porto_ct_master_sliders();
		$rev_sliders    = porto_ct_rev_sliders();

		// Get menus
		$menus        = wp_get_nav_menus( array( 'orderby' => 'name' ) );
		$menu_options = array();
		if ( ! empty( $menus ) ) {
			foreach ( $menus as $menu ) {
				$menu_options[ $menu->term_id ] = $menu->name;
			}
		}

		if ( function_exists( 'porto_options_breadcrumbs_types' ) ) {
			$breadcrumb_types = porto_options_breadcrumbs_types();
			foreach ( $breadcrumb_types as $key => $b ) {
				$breadcrumb_types[ $key ] = $b['alt'];
			}
		} else {
			$breadcrumb_types = array();
		}

		$field1 = array(
			// Loading Overlay
			'loading_overlay'  => array(
				'name'    => 'loading_overlay',
				'title'   => __( 'Loading Overlay', 'porto-functionality' ),
				'type'    => 'radio',
				'default' => '',
				'options' => porto_ct_show_options(),
			),
			// Breadcrumbs
			'breadcrumbs'      => array(
				'name'  => 'breadcrumbs',
				'title' => __( 'Breadcrumbs', 'porto-functionality' ),
				'desc'  => __( 'Do not Show', 'porto-functionality' ),
				'type'  => 'checkbox',
			),
			// Breadcrumb Type
			'breadcrumbs_type' => array(
				'name'     => 'breadcrumbs_type',
				'title'    => __( 'Breadcrumbs Type', 'porto-functionality' ),
				'type'     => 'select',
				'required' => array(
					'name'  => 'breadcrumbs',
					'value' => '',
				),
				'std'      => '',
				'options'  => $breadcrumb_types,
			),
		);

		$field2 = array(
			// Header
			'header'         => array(
				'name'  => 'header',
				'title' => __( 'Header', 'porto-functionality' ),
				'desc'  => __( 'Do not Show', 'porto-functionality' ),
				'type'  => 'checkbox',
			),
			// Sticky Header
			'sticky_header'  => array(
				'name'     => 'sticky_header',
				'title'    => __( 'Sticky Header', 'porto-functionality' ),
				'type'     => 'radio',
				'default'  => '',
				'required' => array(
					'name'  => 'header',
					'value' => '',
				),
				'options'  => porto_ct_show_options(),
			),
			// Header View
			'header_view'    => array(
				'name'     => 'header_view',
				'title'    => __( 'Header View', 'porto-functionality' ),
				'type'     => 'radio',
				'default'  => 'default',
				'required' => array(
					'name'  => 'header',
					'value' => '',
				),
				'options'  => $header_view,
			),
			// Footer
			'footer'         => array(
				'name'  => 'footer',
				'title' => __( 'Footer', 'porto-functionality' ),
				'desc'  => __( 'Do not Show', 'porto-functionality' ),
				'type'  => 'checkbox',
			),
			// Footer View
			'footer_view'    => array(
				'name'     => 'footer_view',
				'title'    => __( 'Footer View', 'porto-functionality' ),
				'type'     => 'radio',
				'default'  => '',
				'required' => array(
					'name'  => 'footer',
					'value' => '',
				),
				'options'  => $footer_view,
			),
			// Main Menu
			'main_menu'      => array(
				'name'    => 'main_menu',
				'title'   => __( 'Main Menu', 'porto-functionality' ),
				'type'    => 'select',
				'default' => '',
				'options' => $menu_options,
			),
			// Secondary Menu
			'secondary_menu' => array(
				'name'    => 'secondary_menu',
				'title'   => __( 'Secondary Menu', 'porto-functionality' ),
				'type'    => 'select',
				'default' => '',
				'options' => $menu_options,
			),
			// Sidebar Menu
			'sidebar_menu'   => array(
				'name'    => 'sidebar_menu',
				'title'   => __( 'Sidebar Menu', 'porto-functionality' ),
				'type'    => 'select',
				'default' => '',
				'options' => $menu_options,
			),
		);

		$field3 = array(
			// Banner Position
			'banner_pos'           => array(
				'name'    => 'banner_pos',
				'title'   => __( 'Banner Position', 'porto-functionality' ),
				'type'    => 'radio',
				'default' => '',
				'options' => $banner_pos,
			),
			// Banner Type
			'banner_type'          => array(
				'name'    => 'banner_type',
				'title'   => __( 'Banner Type', 'porto-functionality' ),
				'type'    => 'select',
				'options' => $banner_type,
			),
			// Revolution Slider
			'rev_slider'           => array(
				'name'     => 'rev_slider',
				'title'    => __( 'Revolution Slider', 'porto-functionality' ),
				'desc'     => __( 'Please select <strong>Banner Type</strong> to <strong>Revolution Slider</strong> and select a slider.', 'porto-functionality' ),
				'type'     => 'select',
				'required' => array(
					'name'  => 'banner_type',
					'value' => 'rev_slider',
				),
				'options'  => $rev_sliders,
			),
			// Master Slider
			'master_slider'        => array(
				'name'     => 'master_slider',
				'title'    => __( 'Master Slider', 'porto-functionality' ),
				'desc'     => __( 'Please select <strong>Banner Type</strong> to <strong>Master Slider</strong> and select a slider.', 'porto-functionality' ),
				'type'     => 'select',
				'required' => array(
					'name'  => 'banner_type',
					'value' => 'master_slider',
				),
				'options'  => $master_sliders,
			),
			// Banner
			'banner_block'         => array(
				'name'     => 'banner_block',
				'title'    => __( 'Banner Block', 'porto-functionality' ),
				'desc'     => __( 'Please select <strong>Banner Type</strong> to <strong>Banner Block</strong> and input a block slug name. You can create a block in <strong>Porto -> Templates Builder -> Add New</strong>.', 'porto-functionality' ),
				'type'     => 'text',
				'required' => array(
					'name'  => 'banner_type',
					'value' => 'banner_block',
				),
			),
			// Content Top
			'content_top'          => array(
				'name'  => 'content_top',
				'title' => __( 'Content Top', 'porto-functionality' ),
				'desc'  => __( 'Please input comma separated block slug names.', 'porto-functionality' ),
				'type'  => 'text',
			),
			// Content Inner Top
			'content_inner_top'    => array(
				'name'  => 'content_inner_top',
				'title' => __( 'Content Inner Top', 'porto-functionality' ),
				'desc'  => __( 'Please input comma separated block slug names.', 'porto-functionality' ),
				'type'  => 'text',
			),
			// Content Inner Bottom
			'content_inner_bottom' => array(
				'name'  => 'content_inner_bottom',
				'title' => __( 'Content Inner Bottom', 'porto-functionality' ),
				'desc'  => __( 'Please input comma separated block slug names.', 'porto-functionality' ),
				'type'  => 'text',
			),
			// Content Bottom
			'content_bottom'       => array(
				'name'  => 'content_bottom',
				'title' => __( 'Content Bottom', 'porto-functionality' ),
				'desc'  => __( 'Please input comma separated block slug names.', 'porto-functionality' ),
				'type'  => 'text',
			),
		);
		if ( function_exists( 'porto_header_type_is_preset' ) && porto_header_type_is_preset() && '19' != porto_get_header_type() ) {
			unset( $field2['secondary_menu'] );
		}
		$this->array_insert( $fields, $field1, 'page_title', 'map', 'before' );
		$this->array_insert( $fields, $field2, 'page_sub_title' );
		$this->array_insert( $fields, $field3, 'mobile_sidebar' );
		return $fields;
	}

	/**
	 * Add skin meta fields.
	 *
	 * @since 2.3.0
	 */
	public function add_skin_meta_fields( $fields, $tax_meta_fields ) {
		$bg_repeat     = porto_ct_bg_repeat();
		$bg_size       = porto_ct_bg_size();
		$bg_attachment = porto_ct_bg_attachment();
		$bg_position   = porto_ct_bg_position();

		if ( ! $tax_meta_fields ) {
			$tabs = array(
				'body'           => array( 'body', __( 'Body', 'porto-functionality' ) ),
				'header'         => array( 'header', __( 'Header', 'porto-functionality' ) ),
				'sticky_header'  => array( 'sticky_header', __( 'Sticky Header', 'porto-functionality' ) ),
				'breadcrumbs'    => array( 'breadcrumbs', __( 'Breadcrumbs', 'porto-functionality' ) ),
				'page'           => array( 'page', __( 'Page Content', 'porto-functionality' ) ),
				'content_bottom' => array( 'content_bottom', __( 'Content Bottom Widgets Area', 'porto-functionality' ) ),
				'footer_top'     => array( 'footer_top', __( 'Footer Top Widget Area', 'porto-functionality' ) ),
				'footer'         => array( 'footer', __( 'Footer', 'porto-functionality' ) ),
				'footer_main'    => array( 'footer_main', __( 'Footer Widgets Area', 'porto-functionality' ) ),
				'footer_bottom'  => array( 'footer_bottom', __( 'Footer Bottom Widget Area', 'porto-functionality' ) ),
			);
		} else {
			$tabs = array(
				'body'           => array( 'body', __( 'Body Background', 'porto-functionality' ) ),
				'header'         => array( 'header', __( 'Header Background', 'porto-functionality' ) ),
				'sticky_header'  => array( 'sticky_header', __( 'Sticky Header Background', 'porto-functionality' ) ),
				'breadcrumbs'    => array( 'breadcrumbs', __( 'Breadcrumbs Background', 'porto-functionality' ) ),
				'page'           => array( 'page', __( 'Page Content Background', 'porto-functionality' ) ),
				'content_bottom' => array( 'content_bottom', __( 'Content Bottom Widgets Area Background', 'porto-functionality' ) ),
				'footer_top'     => array( 'footer_top', __( 'Footer Top Widget Area Background', 'porto-functionality' ) ),
				'footer'         => array( 'footer', __( 'Footer Background', 'porto-functionality' ) ),
				'footer_main'    => array( 'footer_main', __( 'Footer Widgets Area Background', 'porto-functionality' ) ),
				'footer_bottom'  => array( 'footer_bottom', __( 'Footer Bottom Widget Area Background', 'porto-functionality' ) ),
			);
		}

		foreach ( $tabs as $key => $value ) {
			$fields[ $key . '_bg_color' ]      = array(
				'name'  => $key . '_bg_color',
				'title' => __( 'Background Color', 'porto-functionality' ),
				'type'  => 'color',
				'tab'   => $value,
			);
			$fields[ $key . '_bg_image' ]      = array(
				'name'  => $key . '_bg_image',
				'title' => __( 'Background Image', 'porto-functionality' ),
				'type'  => 'upload',
				'tab'   => $value,
			);
			$fields[ $key . '_bg_repeat' ]     = array(
				'name'    => $key . '_bg_repeat',
				'title'   => __( 'Background Repeat', 'porto-functionality' ),
				'type'    => 'select',
				'options' => $bg_repeat,
				'tab'     => $value,
			);
			$fields[ $key . '_bg_size' ]       = array(
				'name'    => $key . '_bg_size',
				'title'   => __( 'Background Size', 'porto-functionality' ),
				'type'    => 'select',
				'options' => $bg_size,
				'tab'     => $value,
			);
			$fields[ $key . '_bg_attachment' ] = array(
				'name'    => $key . '_bg_attachment',
				'title'   => __( 'Background Attachment', 'porto-functionality' ),
				'type'    => 'select',
				'options' => $bg_attachment,
				'tab'     => $value,
			);
			$fields[ $key . '_bg_position' ]   = array(
				'name'    => $key . '_bg_position',
				'title'   => __( 'Background Position', 'porto-functionality' ),
				'type'    => 'select',
				'options' => $bg_position,
				'tab'     => $value,
			);
		}

		return $fields;
	}

	/**
	 * Add product meta fields.
	 *
	 * @since 2.3.0
	 */
	public function add_product_meta_fields( $fields ) {
		$field1 = array(
			'product_layout'         => array(
				'name'    => 'product_layout',
				'title'   => __( 'Product Layout', 'porto-functionality' ),
				'type'    => 'select',
				'default' => 'theme',
				'options' => array(
					''                       => __( 'Theme Options', 'porto-functionality' ),
					'default'                => __( 'Default', 'porto-functionality' ),
					'extended'               => __( 'Extended', 'porto-functionality' ),
					'full_width'             => __( 'Full Width', 'porto-functionality' ),
					'grid'                   => __( 'Grid', 'porto-functionality' ),
					'sticky_info'            => __( 'Sticky Info', 'porto-functionality' ),
					'sticky_both_info'       => __( 'Sticky Left & Right Info', 'porto-functionality' ),
					'transparent'            => __( 'Transparent Images', 'porto-functionality' ),
					'centered_vertical_zoom' => __( 'Centered Vertical Zoom', 'porto-functionality' ),
					'left_sidebar'           => __( 'Left Sidebar', 'porto-functionality' ),
				),
			),
			'product_image_on_hover' => array(
				'name'    => 'product_image_on_hover',
				'title'   => __( 'Show image on hover', 'porto-functionality' ),
				'desc'    => __( 'If you select "Yes", the first image of Product gallery will be displayed on hover.', 'porto-functionality' ),
				'type'    => 'radio',
				'default' => 'yes',
				'options' => array(
					'yes' => __( 'Yes', 'porto-functionality' ),
					'no'  => __( 'No', 'porto-functionality' ),
				),
			),
			'product_custom_block'   => array(
				'name'  => 'product_custom_block',
				'title' => __( 'Custom Block', 'porto-functionality' ),
				'desc'  => __( 'Please input block slug name. This is used for Extended, Sticky Info and Wide Grid layout.', 'porto-functionality' ),
				'type'  => 'text',
			),
			// Share
			'product_share'          => array(
				'name'    => 'product_share',
				'title'   => __( 'Share', 'porto-functionality' ),
				'type'    => 'radio',
				'default' => '',
				'options' => porto_ct_share_options(),
			),
			// Read More Link
			'product_more_link'      => array(
				'name'  => 'product_more_link',
				'title' => __( 'Read More Link in Catalog Mode', 'porto-functionality' ),
				'type'  => 'text',
			),
		);
		if ( empty( count( $fields ) ) ) {
			$fields = $field1;
		} else {
			$this->array_insert( $fields, $field1, 'custom_tab_title1', 'map', 'before' );
		}
		return $fields;
	}

	/**
	 * Insert array into array.
	 *
	 * @param array  $arr1      Array to insert.
	 * @param array  $arr2      Array to be inserted.
	 * @param string $of        The insert position.
	 * @param string $type      The type of array. map|array
	 * @param string $at        before|after
	 *
	 * @since 2.3.0
	 */
	public function array_insert( &$arr1, $arr2, $of, $type = 'map', $at = 'after' ) {
		$res = array();
		if ( 'map' == $type ) {
			foreach ( $arr1 as $key => $value ) {
				if ( $key == $of ) {
					if ( 'after' == $at ) {
						$res[ $key ] = $value;
						$res         = array_merge( $res, $arr2 );
					} else {
						$res         = array_merge( $res, $arr2 );
						$res[ $key ] = $value;
					}
				} else {
					$res[ $key ] = $value;
				}
			}
		} else {
			$offset = array_search( $of, $arr1 );
			if ( false === $offset ) {
				return;
			}
			if ( 'after' == $at ) {
				$offset++;
			}
			array_splice( $arr1, $offset, 0, $arr2 );
			return;
		}
		$arr1 = $res;
		return;
	}
}

Porto_Soft_Mode::get_instance();
