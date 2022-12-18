<?php
/**
 * Bundle of overrided functions.
 *
 * @author     P-THEMES
 * @package    Porto
 * @subpackage Core
 * @since      2.3.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( ! $legacy_mode ) :
	// Post page Meta Field
	function porto_add_post_meta_boxes() {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( function_exists( 'add_meta_box' ) && $screen && 'post' == $screen->base && 'post' == $screen->id ) {
			add_meta_box( 'post-meta-box', __( 'Post Options', 'porto-functionality' ), 'porto_post_meta_box', 'post', 'normal', 'high' );
		}
	}
	// Post Taxonomy Related Functions
	function porto_edit_category_meta_fields() {}
	function porto_save_category_meta_values() {}
	function porto_delete_category_meta_values() {}

	// Product Page Meta Field
	function porto_add_product_meta_boxes() {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( function_exists( 'add_meta_box' ) && $screen && 'post' == $screen->base && 'product' == $screen->id ) {
			add_meta_box( 'product-meta-box', __( 'Product Options', 'porto-functionality' ), 'porto_product_meta_box', 'product', 'normal', 'high' );
			add_meta_box( 'video-meta-box', __( 'Porto Video Thumbnail', 'porto-functionality' ), 'porto_product_video_meta_box', 'product', 'side', 'low' );
		}
	}
	// Product Taxonomy Functions
	function porto_product_cat_meta_fields() {

		$meta_fields = array(
			'category_icon'  => array(
				'name'  => 'category_icon',
				'title' => __( 'Category Icon', 'porto-functionality' ),
				'type'  => 'text',
				'desc'  => __( 'Input font icon class such as "fas fa-user". This field is used for porto product categories element when you select "Media Type" option to "Icon" to display icon instead of category thumbnail.', 'porto-functionality' ),
			),
			'category_image' => array(
				'name'  => 'category_image',
				'title' => __( 'Category Image', 'porto-functionality' ),
				'type'  => 'upload',
			),
		);

		return $meta_fields;
	}

	// Portfolio page Meta Field
	function porto_add_portfolio_meta_boxes() {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( function_exists( 'add_meta_box' ) && $screen && 'post' == $screen->base && 'portfolio' == $screen->id ) {
			add_meta_box( 'portfolio-meta-box', __( 'Portfolio Options', 'porto-functionality' ), 'porto_portfolio_meta_box', 'portfolio', 'normal', 'high' );
		}
	}
	// Portfolio Category Functions
	function porto_portfolio_cat_meta_fields() {
		// Category Image
		$meta_fields = array(
			'category_image' => array(
				'name'  => 'category_image',
				'title' => __( 'Category Image', 'porto-functionality' ),
				'type'  => 'upload',
			),
		);
		return $meta_fields;
	}

	// Member Page Meta Field
	function porto_add_member_meta_boxes() {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( function_exists( 'add_meta_box' ) && $screen && 'post' == $screen->base && 'member' == $screen->id ) {
			add_meta_box( 'member-meta-box', __( 'Member Options', 'porto-functionality' ), 'porto_member_meta_box', 'member', 'normal', 'high' );
		}
	}
	// Member Taxonomy Related Functions
	function porto_edit_member_cat_meta_fields() {}
	function porto_save_member_cat_meta_values() {}
	function porto_delete_member_cat_meta_values() {}

	// Event Page Meta Field
	function porto_add_event_meta_boxes() {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( function_exists( 'add_meta_box' ) && $screen && 'post' == $screen->base && 'event' == $screen->id ) {
			add_meta_box( 'event-meta-box', __( 'Event Options', 'porto-functionality' ), 'porto_event_meta_box', 'event', 'normal', 'high' );
		}
	}
endif;
