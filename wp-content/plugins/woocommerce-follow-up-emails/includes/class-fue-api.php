<?php

/**
 * FUE_API class
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class FUE_API {
	/**
	 * This is the major version for the REST API and takes
	 * first-order position in endpoint URLs.
	 */
	const VERSION = 1;

	/** @var FUE_API_Server the REST API server */
	public $server;

	/** @var FUE_API_Authentication REST API authentication class instance */
	public $authentication;

	/**
	 * class constructor
	 */
	public function __construct() {
		// Add query vars.
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );

		// Register API endpoints.
		add_action( 'init', array( $this, 'add_endpoint' ), 0 );

		// Handle REST API requests.
		add_action( 'parse_request', array( $this, 'handle_rest_api_requests' ), 0 );

		// Handle fue-api endpoint requests.
		add_action( 'parse_request', array( $this, 'handle_api_requests' ), 0 );
	}

	/**
	 * add_query_vars function.
	 *
	 * @since 4.1
	 * @param array $vars
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'fue-api';
		$vars[] = 'fue-api-version';
		$vars[] = 'fue-api-route';
		return $vars;
	}

	/**
	 * add_endpoint function.
	 *
	 * @since 4.1
	 * @return void
	 */
	public function add_endpoint() {

		// REST API.
		add_rewrite_rule( '^fue-api/v1/?$', 'index.php?fue-api-version=1&fue-api-route=/', 'top' );
		add_rewrite_rule( '^fue-api/v1(.*)?', 'index.php?fue-api-version=1&fue-api-route=$matches[1]', 'top' );

		add_rewrite_endpoint( 'fue-api', EP_ALL );
	}

	/**
	 * Handle REST API requests.
	 *
	 * @since 4.1
	 */
	public function handle_rest_api_requests() {
		global $wp;

		if ( ! empty( $_GET['fue-api-version'] ) ) {
			$wp->query_vars['fue-api-version'] = $_GET['fue-api-version'];
		}

		if ( ! empty( $_GET['fue-api-route'] ) ) {
			$wp->query_vars['fue-api-route'] = $_GET['fue-api-route'];
		}

		// REST API request
		if ( ! empty( $wp->query_vars['fue-api-version'] ) && ! empty( $wp->query_vars['fue-api-route'] ) ) {

			define( 'FUE_API_REQUEST', true );
			define( 'FUE_API_REQUEST_VERSION', absint( $wp->query_vars['fue-api-version'] ) );

			$this->includes();

			$this->server = new FUE_API_Server( $wp->query_vars['fue-api-route'] );

			// load API resource classes
			$this->register_resources( $this->server );

			// Fire off the request
			$this->server->serve_request();

			exit;
		}
	}

	/**
	 * API request - Trigger any API requests.
	 *
	 * @since 4.1
	 * @return void
	 */
	public function handle_api_requests() {
		global $wp;

		if ( ! empty( $_GET['fue-api'] ) ) {
			$wp->query_vars['fue-api'] = sanitize_key( wp_unslash( $_GET['fue-api'] ) );
		}

		// fue-api endpoint requests.
		if ( ! empty( $wp->query_vars['fue-api'] ) ) {

			// Buffer, we won't want any output here.
			ob_start();

			// Get API trigger
			$api = strtolower( fue_clean( $wp->query_vars['fue-api'] ) );

			if ( has_action( 'fue_api_' . $api ) ) {
				// Let's deprecate this. I don't like the idea of instantiating classes from user input.
				fue_deprecated_hook( 'fue_api_' . $api, '4.8.20', 'fue_api_handle_api_request', 'Using this action implies security risk! It is important to update your code to use the new action.' );

				// Load class if exists
				if ( class_exists( $api ) )
					$api_class = new $api();

				// Trigger actions
				do_action( 'fue_api_' . $api );
			}

			do_action( 'fue_api_handle_api_request', $api );

			// Done, clear buffer and exit.
			ob_end_clean();
			die( '1' );
		}
	}

	/**
	 * Include required files for REST API request.
	 *
	 * @since 4.1
	 */
	public function includes() {

		// API server / response handlers.
		include_once( 'api/class-fue-api-exception.php' );
		include_once( 'api/class-fue-api-server.php' );
		include_once( 'api/interface-fue-api-handler.php' );
		include_once( 'api/class-fue-api-json-handler.php' );

		// authentication
		include_once( 'api/class-fue-api-authentication.php' );
		$this->authentication = new FUE_API_Authentication();

		include_once( 'api/class-fue-api-resource.php' );

		// Allow plugins to load other response handlers or resource classes.
		do_action( 'fue_api_loaded' );
	}

	/**
	 * Register available API resources.
	 *
	 * @since 4.1
	 * @param FUE_API_Server $server the REST server
	 */
	public function register_resources( $server ) {

		$api_classes = apply_filters( 'fue_api_classes',
			array(
				'FUE_API_Emails',
				'FUE_API_Queue',
				'FUE_API_Reports',
				'FUE_API_Campaigns',
				'FUE_API_Newsletter',
			)
		);

		foreach ( $api_classes as $api_class ) {
			$this->$api_class = new $api_class( $server );
		}
	}

}
