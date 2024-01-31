<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Base functionality for WpBakery editors.
 *
 * @since 7.4
 */
abstract class Vc_Editor {
	/**
	 * @var string
	 */
	public $post_custom_css;
	/**
	 * @since 7.0
	 * @var string
	 */
	public $post_custom_layout;
	/**
	 * @since 7.0
	 * @var string
	 */
	public $post_custom_js_header;
	/**
	 * @since 7.0
	 * @var string
	 */
	public $post_custom_js_footer;
	/**
	 * @since 7.4
	 * @var string
	 */
	public $post_custom_seo_settings;

	/**
	 * Set post meta related to VC.
	 *
	 * @since 7.4
	 * @param WP_Post | null $post
	 */
	public function set_post_meta( $post ) {
		$this->post_custom_css = wp_strip_all_tags( get_post_meta( $post->ID, '_wpb_post_custom_css', true ) );
		$this->post_custom_js_header = get_post_meta( $post->ID, '_wpb_post_custom_js_header', true );
		$this->post_custom_js_footer = get_post_meta( $post->ID, '_wpb_post_custom_js_footer', true );
		$this->post_custom_seo_settings = get_post_meta( $post->ID, '_wpb_post_custom_seo_settings', true );

		$this->post_custom_layout = wpb_get_name_post_custom_layout();
	}
}
