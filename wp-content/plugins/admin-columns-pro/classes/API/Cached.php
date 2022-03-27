<?php

namespace ACP\API;

use AC\Transient;
use ACP\RequestDispatcher;

class Cached implements RequestDispatcher {

	const EXPIRATION = 'expiration';
	const FORCE_UPDATE = 'force_update';

	/**
	 * @var RequestDispatcher
	 */
	private $api;

	public function __construct( RequestDispatcher $api ) {
		$this->api = $api;
	}

	/**
	 * @param Request $request
	 *
	 * @return Transient
	 */
	protected function get_cache( Request $request ) {
		$key = md5( serialize( $request->get_body() ) );

		return new Transient( 'ac_api_request_' . $key );
	}

	/**
	 * @param Transient $cache
	 *
	 * @return Response|null
	 */
	protected function get_cached_response( Transient $cache ) {
		$value = $cache->get();

		$response = is_string( $value )
			? unserialize( $value )
			: null;

		return $response instanceof Response
			? $response
			: null;
	}

	public function dispatch( Request $request, array $args = [] ) {
		$args = array_merge( [
			self::EXPIRATION   => HOUR_IN_SECONDS,
			self::FORCE_UPDATE => false,
		], $args );

		$cache = $this->get_cache( $request );
		$response = $this->get_cached_response( $cache );

		if ( $args[ self::FORCE_UPDATE ] || $cache->is_expired() || null === $response ) {
			$response = $this->api->dispatch( $request );

			$cache->save( serialize( $response ), (int) $args[ self::EXPIRATION ] );
		}

		return $response;
	}

}