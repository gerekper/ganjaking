<?php

namespace ACP\RequestHandler;

use ACP\API;
use ACP\API\Cached;
use ACP\RequestDispatcher;

class ProductsUpdate {

	/**
	 * @var RequestDispatcher
	 */
	private $api;

	public function __construct( RequestDispatcher $api ) {
		$this->api = $api;
	}

	/**
	 * @param API\Request\ProductsUpdate $request
	 *
	 * @return void
	 */
	public function handle( API\Request\ProductsUpdate $request ) {
		$api = new API\Cached( $this->api );
		$api->dispatch( $request, [
			Cached::FORCE_UPDATE => true,
		] );

		wp_clean_plugins_cache();
	}

}