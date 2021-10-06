<?php

namespace ACP\Updates;

use AC\Registrable;
use ACP\API\Request;
use ACP\RequestDispatcher;
use WP_Error;

/**
 * Show changelog when "click view details".
 */
class ViewPluginDetails implements Registrable {

	/**
	 * @var string
	 */
	private $slug;

	/**
	 * @var RequestDispatcher
	 */
	private $api;

	public function __construct( $slug, RequestDispatcher $api ) {
		$this->slug = (string) $slug;
		$this->api = $api;
	}

	public function register() {
		add_filter( 'plugins_api', [ $this, 'get_plugin_information' ], 10, 3 );
	}

	/**
	 * @param mixed  $result
	 * @param string $action
	 * @param object $args
	 *
	 * @return object|WP_Error
	 */
	public function get_plugin_information( $result, $action, $args ) {
		if ( 'plugin_information' !== $action ) {
			return $result;
		}

		if ( $this->slug !== $args->slug ) {
			return $result;
		}

		$response = $this->api->dispatch( new Request\ProductInformation( $this->slug ) );

		if ( $response->has_error() ) {
			return $response;
		}

		return $response->get_body();
	}

}