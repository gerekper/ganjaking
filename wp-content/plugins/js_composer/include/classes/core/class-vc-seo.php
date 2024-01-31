<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery SEO module controller class.
 * @since 7.4
 */
class Vc_Seo {

	/**
	 * Post plugin settings seo meta.
	 * @since 7.4
	 * @var array
	 */
	public $post_seo_meta;

	/**
	 * Post plugin settings seo meta key.
	 * @since 7.4
	 * @var string
	 */
	const POST_SEO_META_KEY = '_wpb_post_custom_seo_settings';

	/**
	 * Set plugin seo post meta.
	 *
	 * @since 7.4
	 */
	public function set_plugin_seo_post_meta() {
		$this->post_seo_meta = $this->get_plugin_seo_post_meta();
	}

	/**
	 * Get plugin seo post meta.
	 *
	 * @since 7.4
	 * @return array
	 */
	public function get_plugin_seo_post_meta() {

		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return [];
		}

		$post_seo_meta = get_post_meta( get_the_ID(), self::POST_SEO_META_KEY, true );
		if ( empty( $post_seo_meta ) ) {
			return [];
		}

		$post_seo_meta = json_decode( $post_seo_meta, true );
		if ( ! is_array( $post_seo_meta ) ) {
			return [];
		}

		return $post_seo_meta;
	}

	/**
	 * Replace title with plugin seo title.
	 *
	 * @since 7.4
	 * @param string $title
	 * @return string
	 */
	public function filter_title( $title ) {
		$seo_title = $this->get_settings_seo_title();
		if ( ! $seo_title ) {
			return $title;
		}

		remove_filter( 'pre_get_document_title', [ $this, 'filter_title' ], 15 );
		$title = $seo_title;
		add_filter( 'pre_get_document_title', [ $this, 'filter_title' ], 15 );

		return $title;
	}


	/**
	 * Get plugin seo title.
	 *
	 * @since 7.4
	 * @return string
	 */
	public function get_settings_seo_title() {
		if ( empty( $this->post_seo_meta['title'] ) ) {
			return '';
		}

		$title = $this->post_seo_meta['title'];
		// Remove excess whitespace.
		$title = preg_replace( '[\s\s+]', ' ', $title );

		$title = wp_strip_all_tags( stripslashes( $title ), true );
		return convert_smilies( esc_html( $title ) );
	}

	/**
	 * Presents the head in the front-end. Resets wp_query if it's not the main query.
	 * @since 7.4
	 */
	public function add_seo_head() {
		global $wp_query;

		$old_wp_query = $wp_query;
		// Reason: The recommended function, wp_reset_postdata, doesn't reset wp_query.
        // phpcs:ignore WordPress.WP.DiscouragedFunctions.wp_reset_query_wp_reset_query
		wp_reset_query();

		$this->output_seo_head();

		// Reason: we have to restore the query.
        // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$GLOBALS['wp_query'] = $old_wp_query;
	}

	/**
	 * Output seo tags in the head.
	 * @since 7.4
	 */
	public function output_seo_head() {
		$this->output_meta_description();

		$this->output_meta_facebook();
		$this->output_meta_twitter();
	}

	/**
	 * Output meta description.
	 * @since 7.4
	 */
	public function output_meta_description() {
		if ( empty( $this->post_seo_meta['description'] ) ) {
			return;
		}

		$description = trim( wp_strip_all_tags( stripslashes( $this->post_seo_meta['description'] ) ) );

		echo '<meta name="description" content="' . esc_attr( $description ) . '">';
	}

	/**
	 * Check if post key phrase is present in other posts.
	 *
	 * @since 7.4
	 * @return bool
	 */
	public function check_key_phrase_in_other_posts() {
		$key_phrase = trim( sanitize_text_field( vc_post_param( 'key_phrase', '' ) ) );

		if ( empty( $key_phrase ) ) {
			return false;
		}

		$current_post_id = vc_post_param( 'post_id', '' );
		$args = [
			'post_type' => 'any',
			'posts_per_page' => 2,
			'meta_query' => [
				[
					'key' => self::POST_SEO_META_KEY,
					'value' => '"focus-keyphrase":"' . $key_phrase . '"',
					'compare' => 'LIKE',
				],
			],
			'post__not_in' => [ $current_post_id ],
		];
		$post = get_posts( $args );

		return is_array( $post ) && count( $post ) >= 2;
	}

	/**
	 * Output meta facebook.
	 * @since 7.4
	 */
	public function output_meta_facebook() {
		if ( empty( $this->post_seo_meta['social-title-facebook'] ) || empty( $this->post_seo_meta['social-description-facebook'] ) ) {
			return;
		}

		$meta = $this->collect_page_social_meta();

		$meta['og:title'] = $this->post_seo_meta['social-title-facebook'];
		$meta['og:description'] = $this->post_seo_meta['social-description-facebook'];

		$meta = $this->add_facebook_image_meta( $meta );

		foreach ( $meta as $key => $value ) {
			echo '<meta property="' . esc_attr( $key ) . '" content="' . esc_attr( $value ) . '">';
		}
	}

	/**
	 * Add facebook page social meta.
	 *
	 * @since 7.4
	 * @return array
	 */
	public function collect_page_social_meta() {
		$site_social_meta['og:locale'] = get_locale();
		$site_social_meta['og:type'] = 'article';
		$site_social_meta['og:url'] = get_permalink();
		$site_social_meta['og:site_name'] = get_bloginfo();

		return $site_social_meta;
	}

	/**
	 * Add facebook image meta.
	 *
	 * @since 7.4
	 * @param array $meta
	 * @return array
	 */
	public function add_facebook_image_meta( $meta ) {
		if ( empty( $this->post_seo_meta['social-image-facebook'] ) ) {
			return $meta;
		}

		$image_id = $this->post_seo_meta['social-image-facebook'];
		$image_data = wp_get_attachment_image_src( $image_id, 'full' );
		if ( is_array( $image_data ) ) {
			$meta['og:image'] = $image_data[0];
			$meta['og:image:width'] = $image_data[1];
			$meta['og:image:height'] = $image_data[2];
		}

		$path = wp_get_original_image_path( $image_id );
		if ( $path ) {
			$meta['og:image:type'] = wp_get_image_mime( wp_get_original_image_path( $image_id ) );
		}

		return $meta;
	}

	/**
	 * Output meta X (twitter).
	 * @since 7.4
	 */
	public function output_meta_twitter() {
		if ( empty( $this->post_seo_meta['social-title-x'] ) || empty( $this->post_seo_meta['social-description-x'] ) ) {
			return;
		}

		$meta['twitter:card'] = 'summary_large_image';
		$meta['twitter:title'] = $this->post_seo_meta['social-title-x'];
		$meta['twitter:description'] = $this->post_seo_meta['social-description-x'];

		$meta = $this->add_twitter_image_meta( $meta );

		foreach ( $meta as $key => $value ) {
			echo '<meta property="' . esc_attr( $key ) . '" content="' . esc_attr( $value ) . '">';
		}
	}

	/**
	 * Add twitter image meta.
	 *
	 * @since 7.4
	 * @param array $meta
	 * @return array
	 */
	public function add_twitter_image_meta( $meta ) {
		if ( empty( $this->post_seo_meta['social-image-twitter'] ) ) {
			return $meta;
		}

		$image_id = $this->post_seo_meta['social-image-twitter'];
		$image_data = wp_get_attachment_image_src( $image_id, 'full' );
		if ( is_array( $image_data ) ) {
			$meta['twitter:image'] = $image_data[0];
		}

		return $meta;
	}
}
