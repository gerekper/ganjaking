<?php

class GPLS_Rule_Embed_Url extends GPLS_Rule {
	private $type    = 'all';
	private $url     = '';
	private $post_id = '';

	public static function load( $rule_data, $form_id = false ) {
		$rule       = new self;
		$embed_type = $rule_data['rule_embed_url'];
		$rule->type = $embed_type;
		if ( $embed_type == 'full' ) {
			$rule->url = $rule_data['rule_embed_url_value_full'];
		}
		if ( $embed_type == 'post_id' ) {
			$rule->post_id = $rule_data['rule_embed_url_value_post'];
		}

		return $rule;
	}

	public function get_current_url() {
		return untrailingslashit( GFFormsModel::get_current_page_url() );
	}

	public function get_url() {
		return untrailingslashit( $this->url );
	}

	public function get_post_url() {
		return untrailingslashit( get_permalink( $this->post_id ) );
	}

	public function context() {
		global $post;

		// rule targets specific embed url so make sure user is on that url now
		if ( $this->type !== 'all' ) {

			if ( $this->type === 'full' ) {
				$test_url = $this->get_url();
			} else {
				$test_url = $this->get_post_url();
			}

			// When limiting by a post ID, determine context by the current post global rather than the URL.
			if ( $this->post_id && $post && $post->ID == $this->post_id ) {
				return true;
			} elseif ( $this->match_base_url() ) {

				// match base URL
				if ( strpos( $this->get_current_url(), $test_url ) === false ) {
					return false;
				}
			} else {

				// match exact URL
				if ( $this->get_current_url() !== $test_url ) {
					return false;
				}
			}
		}

		return true;
	}

	public function match_base_url() {
		/**
		 * Match the base URL rather than the exact URL.
		 *
		 * By default, http://gravitywiz.com/ and http://gravitywiz.com/?param=value are unique URLs. Setting this filter
		 * to true will match both URLs when searching for http://gravitywiz.com.
		 *
		 * @since 1.0
		 *
		 * @param bool $match_base_url Whether to match the base URL; defaults to false.
		 */
		return apply_filters( 'gpls_match_base_url', false );
	}

	public function query() {
		global $wpdb;
		if ( $this->type == 'all' ) {
			// setting is all so we use the current page url to ensure the rule is applied
			return $wpdb->prepare( '(e.source_url = %s OR e.source_url = %s)', $this->get_current_url(), trailingslashit( $this->get_current_url() ) );
		}
		if ( $this->type == 'full' ) {
			// match full url provided
			if ( $this->match_base_url() ) {
				// like matching to prevent user appending the url to avoid limit
				return $wpdb->prepare( 'e.source_url LIKE %s', $this->get_url() . '%' );
			} else {
				return $wpdb->prepare( '(e.source_url = %s OR e.source_url = %s)', $this->get_url(), trailingslashit( $this->get_url() ) );
			}
		}
		if ( $this->type == 'post_id' ) {

			// match permalink for post id provided
			if ( $this->match_base_url() ) {
				return $wpdb->prepare( 'e.source_url LIKE %s', $this->get_post_url() . '%' );
			} else {
				return $wpdb->prepare( '(e.source_url = %s OR e.source_url = %s)', $this->get_post_url(), trailingslashit( $this->get_post_url() ) );
			}
		}
	}

	/**
	 * @param GFAddOn $gfaddon
	 */
	public function render_option_fields( $gfaddon ) {

		$gfaddon->settings_select(
			array(
				'label'   => __( 'User ID', 'gp-limit-submissions' ),
				'name'    => 'rule_embed_url_{i}',
				'class'   => 'rule_value_selector rule_embed_url rule_embed_url_{i} gpls-secondary-field',
				'choices' => array(
					array(
						'label' => __( 'All URLs', 'gp-limit-submissions' ),
						'value' => 'all',
					),
					array(
						'label' => __( 'Specific URL', 'gp-limit-submissions' ),
						'value' => 'full',
					),
					array(
						'label' => __( 'Post/Page', 'gp-limit-submissions' ),
						'value' => 'post_id',
					),
				),
			)
		);
		$gfaddon->settings_text(
			array(
				'label' => __( 'Embed URL', 'gp-limit-submissions' ),
				'name'  => 'rule_embed_url_value_full_{i}',
				'class' => 'rule_value_selector rule_embed_url_value_full rule_embed_url_value_full_{i} gpls-secondary-field',
			)
		);
		$post_choices = array();
		/**
		 * Filter the arguments passed to get_posts() when pulling posts to display for the Embed URL rule.
		 *
		 * @since 1.0
		 *
		 * @param array $args An array of WP_Query arguments.
		 */
		$posts = get_posts( apply_filters( 'gpls_rule_get_post_args', array(
			'post_type'   => array( 'post', 'page' ),
			'numberposts' => 1000,
		) ) );
		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$post_choices[] = array(
					'label' => $post->post_title ? $post->post_title : '(no title)',
					'value' => $post->ID,
				);
			}
		} else {
			$post_choices[] = array(
				'label' => __( 'There are no posts available to select.', 'gp-limit-submissions' ),
				'value' => 0,
			);
		}
		$gfaddon->settings_select(
			array(
				'label'   => __( 'Embed URL', 'gp-limit-submissions' ),
				'name'    => 'rule_embed_url_value_post_{i}',
				'class'   => 'rule_value_selector rule_embed_url_value_post rule_embed_url_value_post_{i} gpls-secondary-field',
				'choices' => $post_choices,
			)
		);
	}

	public function get_type() {
		return 'embed_url';
	}
}
