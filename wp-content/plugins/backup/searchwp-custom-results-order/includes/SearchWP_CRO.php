<?php
/**
 * Class SearchWP_CRO
 */
class SearchWP_CRO {

	public $settings;

	/**
	 * Constructor.
	 */
	public function __construct() {}

	/**
	 * Initialize
	 */
	public function init() {
		require_once SEARCHWP_CRO_PLUGIN_DIR . '/vendor/autoload.php';

		SearchWP_CRO_I18n::init();

		add_action( 'searchwp\query\before', array( $this, 'deploy_buoys_compat' ) );
		add_filter( 'searchwp_terms', array( $this, 'deploy_buoys' ), 99, 2 );

		if ( is_admin() ) {
			// Not using PSR-4 because of the way SearchWP Extensions are implemented (can't use namespaces)
			require_once SEARCHWP_CRO_PLUGIN_DIR . '/admin/settings.php';

			$this->settings = new SearchWP_CRO_Settings();
			$this->settings->init();

			new \SearchWP_CRO\Ajax();
		}
	}

	public function deploy_buoys_compat( $query ) {
		$this->deploy_buoys( $query->get_keywords(), $query->get_engine()->get_name() );
	}

	/**
	 * Deploy buoys when applicable.
	 *
	 * @since 1.1
	 *
	 * @param string $query The search query.
	 * @param string $engine The search engine.
	 *
	 * @return string The search query.
	 */
	public function deploy_buoys( $query, $engine ) {
		$settings = searchwp_cro_get_settings();

		if ( empty( $settings ) || ! is_array( $settings ) ) {
			return $query;
		}

		$deploy_buoy  = false;
		$search_query = strtolower( $query );
		$buoy_key     = searchwp_cro_get_buoy_key( $search_query, $engine );

		// Process triggers.
		$partial_match_triggers = array();

		foreach ( $settings as $trigger ) {
			if ( $engine !== $trigger['engine'] ) {
				continue;
			}

			if ( $trigger['exact'] && $search_query === $trigger['query'] ) {
				// We have an exact match, we can bail right away.
				$deploy_buoy = true;
				break;
			} else {
				$partial_match_triggers[] = $trigger;
			}
		}

		// Check against partial match triggers.
		if ( empty( $deploy_buoy ) ) {
			foreach ( $partial_match_triggers as $partial_match_trigger ) {
				if ( false !== strpos( $search_query, $partial_match_trigger['query'] ) ) {
					// We need to fix the buoy to account for the partial match.
					$buoy_key = searchwp_cro_get_buoy_key( $partial_match_trigger['query'], $engine );

					// Thanks to the proper buoy key, we can deploy the buoy.
					$deploy_buoy = true;
					break;
				}
			}
		}

		// If there's a trigger for this search, deploy a buoy.
		if ( $deploy_buoy ) {
			new \SearchWP_CRO\Buoy( $search_query, $engine, $buoy_key );
		}

		remove_filter( 'searchwp_terms', array( $this, 'deploy_buoys' ), 99, 2 );

		return $query;
	}
}
