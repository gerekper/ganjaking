<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Post SEO settings for page are displayed here.
 *
 * @since 7.4
 */
class Vc_Post_Seo {

	/**
	 * @since 7.4
	 * @var Vc_Editor
	 */
	protected $editor;

	/**
	 * @since 7.4
	 * @param Vc_Editor $editor
	 */
	public function __construct( $editor ) {
		$this->editor = $editor;
	}

	/**
	 * @since 7.4
	 * @return Vc_Editor
	 */
	public function editor() {
		return $this->editor;
	}

	/**
	 * Render popup template.
	 * @since 7.4
	 */
	public function render_ui_template() {
		global $post;

		$post_id = empty( $post->ID ) ? 0 : $post->ID;

		vc_include_template(
			'editors/popups/vc_ui-panel-post-seo.tpl.php',
			[
				'box' => $this,
				'can_unfiltered_html_cap' =>
					vc_user_access()->part( 'unfiltered_html' )->checkStateAny( true, null )->get(),
				'template_variables' => [
					'categories' => [
						esc_html__( 'General', 'js_composer' ),
						esc_html__( 'Content Analysis', 'js_composer' ),
						esc_html__( 'Social', 'js_composer' ),
					],
					'is_default_tab' => true,
					'templates' => [
						'editors/popups/seo/seo-general-tab.tpl.php',
						'editors/popups/seo/seo-analysis-tab.tpl.php',
						'editors/popups/seo/seo-social-tab.tpl.php',
					],
				],
				'post' => $post,
				'post_id' => $post_id,
				'vc_post_seo' => $this,
				'permalink_structure' => get_option( 'permalink_structure' ),
			]
		);
	}

	/**
	 * Returns a base URL, takes permalink structure into account.
	 *
	 * @since 7.4
	 * @param int $post_id
	 * @return string
	 */
	public function base_url( $post_id ) {
		global $pagenow;

		// The default base is the home_url.
		$base_url = home_url( '/', null );

		if ( 'post-new.php' === $pagenow ) {
			return $base_url;
		}

		$permalink = get_sample_permalink( $post_id );

		// If %postname% is the last tag, just strip it and use that as a base.
		if ( preg_match( '#%postname%/?$#', $permalink[0] ) === 1 ) {
			$base_url = preg_replace( '#%postname%/?$#', '', $permalink[0] );
		}

		// If %pagename% is the last tag, just strip it and use that as a base.
		if ( preg_match( '#%pagename%/?$#', $permalink[0] ) === 1 ) {
			$base_url = preg_replace( '#%pagename%/?$#', '', $permalink[0] );
		}

		$parse = wp_parse_url( $base_url );
		if ( ! empty( $parse['host'] ) ) {
			$base_url = $parse['host'];
		}

		return $base_url;
	}

	/**
	 * Get list of social networks.
	 *
	 * @since 7.4
	 * @return array
	 */
	public function get_social_network_list() {
		return [
			'facebook' => 'Facebook',
			'x' => 'X',
		];
	}

	/**
	 * Get image by id.
	 *
	 * @since 7.4
	 * @param int $image_id
	 * @return string
	 */
	public function get_image_by_id( $image_id ) {
		$image = wp_get_attachment_image_src( $image_id, 'full' );

		if ( ! isset( $image[0] ) ) {
			return '';
		}

		return $image[0];
	}
}
