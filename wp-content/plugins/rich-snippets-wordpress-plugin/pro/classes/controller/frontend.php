<?php

namespace wpbuddy\rich_snippets\pro;

use wpbuddy\rich_snippets\Cache_Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Admin.
 *
 * Starts up all the frontend things.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.19.0
 */
class Frontend_Controller extends \wpbuddy\rich_snippets\Frontend_Controller {

	/**
	 * Prints the schema.
	 *
	 * @since 2.19.0
	 */
	public function print_snippets() {
		/**
		 * Check for global snippets.
		 */
		if ( $this->have_global_snippets() ) {

			# Run trough all snippets.
			foreach ( $this->get_global_snippet_post_ids() as $global_snippet_post_id ) {

				if ( ! $this->is_global_snippet_active( $global_snippet_post_id ) ) {
					continue;
				}

				$this->print_rich_snippets( $global_snippet_post_id );
			}
		}

		parent::print_snippets();
	}

	/**
	 * Checks if the current page has any global snippets.
	 *
	 * @return bool
	 * @since 2.0.0
	 *
	 */
	public function have_global_snippets(): bool {

		return count( $this->get_global_snippet_post_ids() ) > 0;
	}


	/**
	 * Returns the global schema post IDs.
	 *
	 * @return int[] Array with post Ids.
	 * @since 2.0.0
	 *
	 */
	public function get_global_snippet_post_ids(): array {

		if ( $this->caching ) {
			$cache = get_transient( 'wpb_rs/cache/global_snippets_ids' );

			if ( is_array( $cache ) ) {
				return $cache;
			}
		}

		$query = new \WP_Query( array(
			'post_type'      => 'wpb-rs-global',
			'fields'         => 'ids',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
		) );

		if ( ! $query->have_posts() ) {
			if ( $this->caching ) {
				set_transient( 'wpb_rs/cache/global_snippets_ids', array(), $this->caching_time * HOUR_IN_SECONDS );
			}

			return array();
		}

		$ids = $query->get_posts();

		if ( $this->caching ) {
			set_transient( 'wpb_rs/cache/global_snippets_ids', $ids, $this->caching_time * HOUR_IN_SECONDS );
		}

		return $ids;
	}


	/**
	 * Checks the rules.
	 *
	 * @param int $id
	 *
	 * @return bool
	 * @since 2.0.0
	 * @since 2.8.0 renamed from is_schema_active()
	 *
	 */
	public function is_global_snippet_active( int $id ): bool {

		$cache_key = Cache_Model::get_cache_key();

		if ( $this->caching ) {
			$cache = get_transient( $cache_key );
		}

		if ( isset( $cache )
		     && is_array( $cache )
		     && isset( $cache[ $id ] )
		     && isset( $cache[ $id ]['match_result'] )
		) {

			/**
			 * Cached match result filter.
			 *
			 * Allows to change the cached value of a matching rule.
			 *
			 * @hook  wpb_rs/cache/rule_{$id}
			 *
			 * @param {bool} $match_result The match result from the cache.
			 * @param {bool} $default_result The default result.
			 *
			 * @since 2.0.0
			 *
			 * @returns {bool}
			 */
			$value = apply_filters( 'wpb_rs/cache/rule_' . $id, $cache[ $id ]['match_result'], true );

			return boolval( $value );
		}

		$match_result = Rules_Model::get_ruleset( $id )->match();

		$cache[ $id ]['match_result'] = $match_result;

		if ( $this->caching ) {
			set_transient( $cache_key, $cache, $this->caching_time * HOUR_IN_SECONDS );
		}

		return boolval( apply_filters( 'wpb_rs/cache/rule_' . $id, $match_result, false ) );
	}

	/**
	 * Initializes values model.
	 *
	 * @since 2.19.0
	 */
	public function init_values_model() {
		new Values_Model();
		$this->values_model_initialized = true;
	}
}