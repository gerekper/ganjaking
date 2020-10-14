<?php

/**
 * Class SearchWP_CRO_Settings
 *
 * This class powers the settings screen UI within the SearchWP settings screen
 */
class SearchWP_CRO_Settings {

	public $public               = true;
	public $slug                 = 'custom-results-order';
	public $name                 = 'Custom Results Order';
	public $min_searchwp_version = '3.0';

	private $url;
	private $prefix   = 'searchwp_cro_';
	private $settings = array();

	/**
	 * Settings constructor.
	 */
	public function __construct() {
		$this->url = plugins_url( 'searchwp-custom-results-order' );
	}

	/**
	 * Initializer
	 */
	public function init() {
		add_filter( 'searchwp\extensions', array( $this, 'register' ), 10 );
		add_filter( 'searchwp_extensions', array( $this, 'register' ), 10 );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ), 999 );
	}

	/**
	 * Output the view for the settings screen
	 */
	public function view() {
		$action_url = add_query_arg( array(
			'page'      => 'searchwp',
			'tab'       => 'extensions',
			'extension' => 'custom-results-order',
		), admin_url( 'options-general.php' ) );

		echo '<div id="searchwp-cro"></div>';
	}

	/**
	 * Callback for SearchWP Extension registration
	 *
	 * @param $extensions
	 *
	 * @return mixed
	 */
	public function register( $extensions ) {

		// When instantiating, SearchWP core forces a prefix of 'SearchWP' and it needs
		// to match the name of this class right here, so we need to get creative :boo:
		$extensions['_CRO_Settings'] = __FILE__;

		return $extensions;
	}

	public function get_data() {
		$settings = get_option( 'searchwp_cro_settings' );

		if ( class_exists( '\\SearchWP\\Settings' ) ) {
			$engines_settings = \SearchWP\Settings::get_engines();
			$engines = [];

			foreach ( $engines_settings as $engine_settings ) {
				$engines[ $engine_settings->get_name() ] = [
					'label' => $engine_settings->get_label(),
				];
			}
		} else if ( function_exists( 'SWP' ) ) {
			$engines = SWP()->settings['engines'];
		}

		return array(
			'nonce'    => wp_create_nonce( 'searchwp-custom-results-order' ),
			'engines'  => $engines,
			'settings' => $settings,
		);
	}

	public function register_assets() {
		$debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true ) || ( isset( $_GET['script_debug'] ) ) ? '' : '.min'; // CSRF okay.

		wp_register_script(
			'searchwpcro',
			SEARCHWP_CRO_PLUGIN_URL . "assets/js/dist/bundle${debug}.js",
			array( 'jquery' ),
			SEARCHWP_CRO_VERSION,
			true
		);

		wp_register_style(
			'searchwpcro',
			SEARCHWP_CRO_PLUGIN_URL . "assets/js/dist/bundle${debug}.css",
			array(),
			SEARCHWP_CRO_VERSION
		);
	}

	public function localize_script() {
		wp_localize_script(
			'searchwpcro',
			'_SEARCHWP_CRO_VARS',
			$this->get_data()
		);
	}

	/**
	 * Enqueue assets callback
	 *
	 * @param $hook
	 */
	public function assets( $hook ) {

		if (
			'settings_page_searchwp' !== $hook ||
			! isset( $_GET['extension'] ) ||
			! $_GET['extension'] === $this->slug
		) {
			return;
		}

		$this->register_assets();
		$this->localize_script();

		wp_enqueue_script( 'searchwpcro' );

		wp_enqueue_style( 'searchwpcro' );
	}
}
