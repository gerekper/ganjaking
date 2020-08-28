<?php

/**
 * Class SearchWP_Related
 */
class SearchWP_Related {

	public $settings;

	private $meta_box;
	private $template;
	private $related;
	private $engine;

	public $meta_key;
	public $post;

	/**
	 * SearchWP_Related constructor.
	 */
	function __construct() {
		$this->meta_key = 'searchwp_related';

		require_once SEARCHWP_RELATED_PLUGIN_DIR . '/vendor/autoload.php';
		require_once SEARCHWP_RELATED_PLUGIN_DIR . '/admin/settings.php';

		$this->settings = new SearchWP_Related_Settings();
		$this->settings->init();
	}

	/**
	 * Setter for engine name
	 *
	 * @param string $engine Valid engine name
	 */
	public function set_engine( $engine = 'default' ) {
		if ( function_exists( 'SWP' ) ) {
			$this->engine = SWP()->is_valid_engine( $engine ) ? $engine : 'default';
		} else if ( class_exists( '\\SearchWP\\Settings' ) ) {
			$engine_valid = \SearchWP\Settings::get_engine_settings( $engine );
			$this->engine = $engine_valid ? $engine : 'default';
		} else {
			$this->engine = 'default';
		}
	}

	/**
	 * Initialize
	 */
	function init() {

		add_action( 'wp', array( $this, 'set_post' ) );

		add_action( 'admin_init', array( $this, 'admin_init' ) );

		if ( ! is_admin() ) {
			$this->template = new SearchWP_Related\Template();
			$this->template->init();
		}
	}

	/**
	 * Check for edit screen in WP Admin
	 *
	 * @param null $new_edit
	 *
	 * @return bool
	 */
	function is_edit_page( $new_edit = null ) {
		global $pagenow;

		if ( ! is_admin() ) {
			return false;
		}

		if ( 'edit' === $new_edit ) {
			return in_array( $pagenow, array( 'post.php' ), true );
		}  elseif ( 'new' === $new_edit ) {
			return in_array( $pagenow, array( 'post-new.php' ), true );
		} else {
			return in_array( $pagenow, array( 'post.php', 'post-new.php' ), true );
		}
	}

	/**
	 * Callback for admin_init; implements meta box
	 */
	function admin_init() {

		if ( ! $this->is_edit_page() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return;
		}

		$this->meta_box = new SearchWP_Related\Meta_Box();
		$this->meta_box->init();
	}

	/**
	 * Setter for the post object to work with
	 *
	 * @param null $post
	 */
	public function set_post( $post = null ) {
		if ( empty( $post ) || ! $post instanceof WP_Post ) {
			$post = get_queried_object();
		}

		if ( $post instanceof WP_Post ) {
			$this->post = $post;
		}
	}

	/**
	 * Determine a fallback/default set of keywords if none are found
	 *
	 * @param int $post_id
	 *
	 * @return string
	 */
	function maybe_get_fallback_keywords( $post_id = 0 ) {
		$keywords = '';

		// The keywords may have been intentionally removed
		$skipped = get_post_meta( $post_id, $this->meta_key . '_skip', true );

		if ( ! empty( $skipped ) ) {
			return $keywords;
		}

		// If there are no terms, it likely means this plugin was installed
		// after content already existed, so let's assume the title works
		if ( apply_filters( 'searchwp_related_use_fallback_keywords', true, $post_id ) ) {

			$keywords = apply_filters( 'searchwp_related_default_keywords', get_the_title( $post_id ) );

			if ( 'auto-draft' === get_post_status( $post_id ) ) {
				$keywords = '';
			}

			$keywords = $this->clean_string( $keywords );

			if ( ! empty( $keywords ) ) {
				update_post_meta( $post_id, $this->meta_key, sanitize_text_field( $keywords ) );
			}
		}

		return $keywords;
	}

	/**
	 * Generate a somewhat-tokenized string of keywords
	 *
	 * @param $keywords
	 *
	 * @return array|string
	 */
	public function clean_string( $keywords ) {
		// Titles often have HTML entities
		$keywords = html_entity_decode( $keywords );

		// Pre-process the string (e.g. remove common words)
		if ( function_exists( 'SWP' ) ) {
			$keywords = SWP()->sanitize_terms( $keywords );
		} else if ( class_exists( '\\SearchWP\\Tokens' )) {
			$tokens   = new SearchWP\Tokens( $keywords );
			$keywords = $tokens->get();
		}

		// Number of search terms is purposely NOT limited here both because editors
		// will be manually controlling the search string and further there is no way
		// to accurately determine which engine is being used at this time so we're
		// not guaranteed to have the proper hook to run as you can limit max terms
		// per engine if you'd like.

		$keywords = implode( ' ', $keywords );

		return $keywords;
	}

	/**
	 * Retrieve the related content for a specific post
	 *
	 * @param array $args
	 *
	 * @param int $post_id
	 *
	 * @return array The related posts
	 */
	public function get( $args = array(), $post_id = 0 ) {

		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}

		$post_id = absint( $post_id );

		$defaults = array(
			'engine'         => $this->engine, // Engine to use
			's'              => '',            // Terms to search
			'fields'         => 'ids',         // Return IDs only
			'posts_per_page' => 3,             // How many results to return
			'log'            => false,         // Log the search?
			'post__in'       => array(),       // Limit results pool?
			'post__not_in'   => array(),       // Exclude posts?
		);

		// Process our arguments
		$args = wp_parse_args( $args, $defaults );

		// If there are no terms, it likely means this plugin was installed
		// after content already existed, so let's retrieve fallback keywords
		if ( empty( $args['s'] ) ) {
			$args['s'] = $this->maybe_get_fallback_keywords( $post_id );
		}

		// Format post__in
		if ( ! is_array( $args['post__in'] ) ) {
			$args['post__in'] = array( $args['post__in'] );
		}

		// Format post__not_in
		if ( ! is_array( $args['post__not_in'] ) ) {
			$args['post__not_in'] = array( $args['post__not_in'] );
		}

		// We always want to force exclude the current post
		$args['post__not_in'][] = $post_id;
		$args['post__not_in'][] = get_queried_object_id();
		$args['post__not_in']   = array_unique( $args['post__not_in'] );

		// Prevent the search from being logged
		if ( empty( $args['log'] ) ) {
			add_filter( 'searchwp_log_search', 'searchwp_related_disable_hook' );
			add_filter( 'searchwp_metrics_log_search', 'searchwp_related_disable_hook' );
			add_filter( 'searchwp\statistics\log', 'searchwp_related_disable_hook' );
		}

		// If there is anything included, force that in at the top of the results, and reduce the posts_per_page
		$always_include = get_post_meta( $post_id, $this->meta_key . '_always_include', true );
		if ( ! empty( $always_include ) ) {
			// If there are more always included results than the posts per page, we need to chop it down.
			if ( $args['posts_per_page'] < count( $always_include ) ) {
				$always_include = array_slice( $always_include, 0, $args['posts_per_page'] );
			}

			// Adjust the posts per page for the search based on the number of always included entries.
			$args['posts_per_page'] -= count( $always_include );

			// We might not need to perform the search if everything is occupied by the forced entries.
			if ( $args['posts_per_page'] <= 0 ) {
				return $always_include;
			}

			// We also need to exclude (from the search) anything that's been forcefully included.
			$args['post__not_in'] = array_unique( array_merge( $always_include, $args['post__not_in'] ) );
		}

		do_action( 'searchwp_related_pre_search', $args );

		$args = apply_filters( 'searchwp_related_query_args', $args );

		// If there's no search query, there's nothing to do.
		if ( empty( $args['s'] ) ) {
			return array();
		}

		$transient_key = 'searchwp_related_query_' . md5( wp_json_encode( $args ) );
		$related       = get_transient( $transient_key );
		$cache_related = apply_filters( 'searchwp_related_cache_enabled', true );

		// If there's no Transient (or we're debugging in any capacity) grab a fresh set.
		if (
			false === $related
			|| ! $cache_related
			|| ( defined( 'WP_DEBUG' ) && WP_DEBUG )
			|| apply_filters( 'searchwp_debug', false )
			|| apply_filters( 'searchwp\debug', false )
		) {
			// We need to enforce our (lack of) term limit by setting the max to the number of
			// terms in this particular search query, but only if we're finding Related.
			add_filter( 'searchwp_max_search_terms', function( $max ) use ( $args ) {
				if ( did_action( 'searchwp_related_pre_search' ) &&  ! did_action( 'searchwp_related_post_search' ) ) {
					$new_max = count( explode( ' ', $args['s'] ) ) + 2; // Adding 2 to account for unknown edge cases, I don't know.

					// We don't want to go nuts.
					$max_terms = (int) apply_filters( 'searchwp_related_max_terms', 25, $args );
					if ( $new_max > $max_terms ) {
						$new_max = $max_terms;
					}

					return $new_max;
				} else {
					return $max;
				}
			}, 190 );

			$related = new SWP_Query( $args );

			// It's possible that AND logic provided too few results, so we may need to fire a second search here.
			// It's not a guarantee that AND logic prevented all results here, but we don't have a way to tell
			// whether AND logic ran and limited the results, so we'll just eat the extra query to find out.
			if (
				apply_filters( 'searchwp_related_fill_slots', true )
				&& count( $related->posts ) < absint( $args['posts_per_page'] )
			) {
				$related = $this->fill_empty_slots( $related, $args );
			}

			$related = $related->posts;

			if ( ! empty( $always_include ) ) {
				$related = array_merge( array_values( $always_include ), array_values( $related ) );
			}

			if ( $cache_related ) {
				$ttl = apply_filters( 'searchwp_related_cache_length', 12 * HOUR_IN_SECONDS );

				set_transient( $transient_key, $related, absint( $ttl ) );
			}
		}

		$this->related = $related;

		do_action( 'searchwp_related_post_search', $args );

		// Undo previous hooks for this search only
		if ( empty( $args['log'] ) ) {
			remove_filter( 'searchwp_log_search', 'searchwp_related_disable_hook' );
			remove_filter( 'searchwp_metrics_log_search', 'searchwp_related_disable_hook' );
			remove_filter( 'searchwp\statistics\log', 'searchwp_related_disable_hook' );
		}

		return $this->related;
	}

	/**
	 * Attempts to ensure that posts_per_page is filled with results.
	 *
	 * @since 1.3
	 */
	private function fill_empty_slots( $related, $args ) {
		add_filter( 'searchwp_and_logic', array( $this, 'return_false' ) );

		$filler_args = $args;

		$filler_args['post__not_in']   = array_merge( $args['post__not_in'], $related->posts );
		$filler_args['posts_per_page'] = $filler_args['posts_per_page'] - count( $related->posts );

		$filler_results = new SWP_Query( $filler_args );

		remove_filter( 'searchwp_and_logic', array( $this, 'return_false' ) );

		$related->posts = array_merge( $related->posts, $filler_results->posts );

		return $related;
	}

	/**
	 * Identifiable callback to return false (so it can be easily removed once we're done with it)
	 *
	 * @since 1.3
	 */
	public function return_false() {
		return false;
	}
}
