<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Cache.
 *
 * Handles cache clearings from the frontend in the backend.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
final class Cache_Model {

	/**
	 * Clears frontend cache when global post gets saved.
	 *
	 * @since 2.0.0
	 */
	public static function clear_global_snippets_ids() {

		delete_transient( 'wpb_rs/cache/global_snippets_ids' );
	}


	/**
	 * Clears cache of a singular post.
	 *
	 * @param int $post_id
	 *
	 * @since 2.0.0
	 *
	 */
	public static function clear_singular_snippet( $post_id ) {

		# back compat
		delete_transient( 'wpb_rs/cache/snippets_' . (string) $post_id );

		# new
		delete_transient( 'wpb_rs/cache/snippets/singular/' . (string) $post_id );
	}


	/**
	 * Clears rule cache for a singular snippet.
	 *
	 * @param int $post_id
	 *
	 * @since 2.0.0
	 *
	 */
	public static function clear_singular_rule( $post_id ) {

		delete_transient( 'wpb_rs/cache/rule_' . (string) $post_id );
	}


	/**
	 * Clears all snippet caches.
	 *
	 * @since 2.8.0
	 */
	public static function clear_all_snippets() {
		global $wpdb;

		return $wpdb->query(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE '%wpb_rs/cache/snippets%'"
		);
	}


	/**
	 * Clears all caches.
	 *
	 * @return false|int
	 * @since 2.0.0
	 *
	 */
	public static function clear_all_caches() {

		global $wpdb;

		/**
		 * Clear Cache Action.
		 *
		 * Allows third party plugins to perform an action before caches are cleared.
		 *
		 * @hook  wpbuddy/rich_snippets/clear_call_caches
		 * @since 2.0.0
		 */
		do_action( 'wpbuddy/rich_snippets/clear_call_caches' );

		return $wpdb->query(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE '%wpb_rs/r_cache/%' OR option_name LIKE '%wpb_rs/cache/%'"
		);
	}


	/**
	 * Returns the current cache key.
	 *
	 * @return string
	 * @since 2.1.3
	 *
	 */
	public static function get_cache_key() {

		$key = 'wpb_rs/cache/snippets/';

		if ( is_singular() ) {
			return $key . 'singular/' . Helper_Model::instance()->get_current_post_id();
		}

		if ( get_queried_object() instanceof \WP_Term ) {
			return $key . 'term/' . get_queried_object_id();
		}

		if ( is_tax() ) {
			return $key . 'tax/' . get_queried_object_id();
		}

		if ( is_author() ) {
			return $key . 'author/' . get_queried_object_id();
		}

		if ( is_tag() ) {
			return $key . 'category/' . get_queried_object_id();
		}

		if ( is_front_page() ) {
			return $key . 'front_page';
		}

		if ( is_404() ) {
			return $key . 'term/404';
		}

		$helper = Helper_Model::instance();

		# avoid unpredictable caching
		return $key . $helper->get_short_hash( wp_guess_url() );

	}
}
