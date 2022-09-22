<?php

namespace Yoast\WP\SEO\Premium\Integrations\Front_End;

use WP_Query;
use Yoast\WP\SEO\Conditionals\Front_End_Conditional;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Class Crawl_Cleanup_Searches.
 */
class Crawl_Cleanup_Searches implements Integration_Interface {

	/**
	 * Patterns to match against to find spam.
	 *
	 * @var array
	 */
	private $patterns = [
		'/[：（）【】［］]+/u',
		'/(TALK|QQ)\:/iu',
	];

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * Crawl_Cleanup_Searches integration constructor.
	 *
	 * @param Options_Helper $options_helper The option helper.
	 */
	public function __construct( Options_Helper $options_helper ) {
		$this->options_helper = $options_helper;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		if ( $this->options_helper->get( 'search_cleanup' ) ) {
			\add_filter( 'pre_get_posts', [ $this, 'validate_search' ] );
		}
	}

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array The array of conditionals.
	 */
	public static function get_conditionals() {
		return [ Front_End_Conditional::class ];
	}

	/**
	 * Check if we want to allow this search to happen.
	 *
	 * @param WP_Query $query The main query.
	 *
	 * @return WP_Query
	 */
	public function validate_search( WP_Query $query ) {
		if ( ! $query->is_search() ) {
			return $query;
		}
		// First check against emoji and patterns we might not want.
		$this->check_unwanted_patterns( $query );

		// Then limit characters if still needed.
		$this->limit_characters();

		return $query;
	}

	/**
	 * Check query against unwanted search patterns.
	 *
	 * @param WP_Query $query The main WordPress query.
	 *
	 * @return void
	 */
	private function check_unwanted_patterns( WP_Query $query ) {
		$s = \rawurldecode( $query->query_vars['s'] );
		if ( $this->options_helper->get( 'search_cleanup_emoji' ) && $this->has_emoji( $s ) ) {
			$this->redirect_away( 'We don\'t allow searches with emojis and other special characters.' );
		}

		if ( ! $this->options_helper->get( 'search_cleanup_patterns' ) ) {
			return;
		}
		foreach ( $this->patterns as $pattern ) {
			$outcome = \preg_match( $pattern, $s, $matches );
			if ( $outcome && $matches !== [] ) {
				$this->redirect_away( 'Your search matched a common spam pattern.' );
			}
		}
	}

	/**
	 * Redirect to the homepage for invalid searches.
	 *
	 * @param string $reason The reason for redirecting away.
	 * @param string $to_url The URL to redirect to.
	 *
	 * @return void
	 */
	private function redirect_away( $reason, $to_url = '' ) {
		if ( empty( $to_url ) ) {
			$to_url = \get_home_url();
		}

		\wp_safe_redirect( $to_url, 301, 'Yoast Search Filtering: ' . $reason );
		exit;
	}

	/**
	 * Limits the number of characters in the search query.
	 *
	 * @return void
	 */
	private function limit_characters() {
		$s = \get_search_query();
		if ( \mb_strlen( $s, 'UTF-8' ) > $this->options_helper->get( 'search_character_limit' ) ) {
			$new_s = \mb_substr( $s, 0, $this->options_helper->get( 'search_character_limit' ), 'UTF-8' );
			$this->redirect_away( 'Your search exceeded the number of allowed characters.', \get_bloginfo( 'url' ) . '/?s=' . \rawurlencode( $new_s ) );
		}
	}

	/**
	 * Determines if a text string contains an emoji or not.
	 *
	 * @param string $text The text string to detect emoji in.
	 *
	 * @return bool
	 */
	private function has_emoji( $text ) {
		$emojis_regex = '/([^-\p{L}\x00-\x7F]+)/u';
		\preg_match( $emojis_regex, $text, $matches );
		return ! empty( $matches );
	}
}
