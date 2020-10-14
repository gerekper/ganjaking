<?php

/**
 * Class SearchWP_Redirects
 */
class SearchWP_Redirects {

	public $settings;

	private $engine;

	/**
	 * SearchWP_Redirects constructor.
	 */
	function __construct() {
		$this->meta_key = 'searchwp_redirects';

		require_once SEARCHWP_REDIRECTS_PLUGIN_DIR . '/vendor/autoload.php';

		// Not using PSR-4 because of the way SearchWP Extensions are implemented (can't use namespaces)
		require_once SEARCHWP_REDIRECTS_PLUGIN_DIR . '/admin/settings.php';

		$this->settings = new SearchWP_Redirects_Settings();
		$this->settings->init();

		add_action( 'searchwp\query\before', array( $this, 'init' ) );
		add_action( 'searchwp_before_query_index', array( $this, 'init' ) ); // SearchWP 3.x compat.
	}

	/**
	 * Redirect if necessary
	 *
	 * @param array $args
	 */
	function init( $args = array() ) {

		// This should only fire as a callback to searchwp_before_query_index
		if ( empty( $args ) ) {
			return;
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( is_admin() || did_action('wp') ) {
			return;
		}

		$settings = $this->settings->get();

		$redirects = isset( $settings['redirects'] ) ? $settings['redirects'] : array();

		if ( empty( $redirects ) ) {
			return;
		}

		$original_keywords = '';
		if ( ! is_array( $args ) ) {
			// This is not SearchWP 3.x. Override the provided context with what is expected.
			$original_keywords = $args->get_keywords();
			$args = array(
				'engine' => $args->get_engine()->get_name(),
				'terms'  => $args->get_tokens(),
			);
		}

		foreach ( $redirects as $redirect ) {
			// Does this engine apply to the redirect?
			if ( is_null( $redirect['engines'] ) || ( is_array( $redirect['engines'] ) && in_array( $args['engine'], $redirect['engines'], true ) ) ) {

				$destination = false;

				// We're going to work with the original query so as to exclude potential issues of
				// common words triggering failed matches etc
				if ( function_exists( 'SWP' ) ) {
					$search_query = SWP()->original_query;
				} else {
					$search_query = $original_keywords;
				}
				if ( empty( $search_query ) && ! empty( $args['terms'] ) ) {
					$search_query = implode( ' ', $args['terms'] );
				}

				// Does this search query apply to the redirect
				if ( ! empty( $redirect['partial'] ) ) {

					// We're going to normalize the Redirect trigger word and then strpos each search term for partial matches
					if ( class_exists( '\\SearchWP\\Tokens' ) ) {
						$tokens =  new \SearchWP\Tokens( $redirect['query'] );
						$source = $tokens->get();
					} else if ( function_exists( 'SWP' ) ) {
						$source = explode( ' ', SWP()->clean_term_string( $redirect['query'] ) );
					}

					foreach ( $source as $term ) {
						if ( false !== strpos( $search_query, $term ) ) {
							$destination = $redirect['redirect'];
							break;
						}
					}

				} elseif ( strtolower( $redirect['query'] ) === strtolower( $search_query ) ) {
					$destination = $redirect['redirect'];
				}

				if ( $destination ) {
					wp_safe_redirect( home_url( $destination ) );
					die();
				}
			}
		}
	}

	/**
	 * Setter for engine name
	 *
	 * @param string $engine Valid engine name
	 */
	public function set_engine( $engine = 'default' ) {
		if ( class_exists( '\\SearchWP\\Settings' ) ) {
			$engine_valid = \SearchWP\Settings::get_engine_settings( $engine );
			$this->engine = $engine_valid ? $engine : 'default';
		} else if ( function_exists( 'SWP' ) ) {
			$this->engine = SWP()->is_valid_engine( $engine ) ? $engine : 'default';
		} else {
			$this->engine = 'default';
		}
	}
}
