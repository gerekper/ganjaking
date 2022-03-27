<?php

namespace ACP\Updates;

use ACP\API;
use ACP\API\Cached;
use ACP\RequestDispatcher;
use ACP\Type\ActivationToken;
use ACP\Type\SiteUrl;

class ProductsUpdater {

	/**
	 * @var RequestDispatcher
	 */
	private $api;

	/**
	 * @var SiteUrl
	 */
	private $site_url;

	public function __construct( RequestDispatcher $api, SiteUrl $site_url ) {
		$this->api = $api;
		$this->site_url = $site_url;
	}

	public function update( ActivationToken $token = null ) {
		$request = new API\Request\ProductsUpdate(
			$this->site_url,
			$token
		);

		$api = new API\Cached( $this->api );
		$api->dispatch( $request, [
			Cached::FORCE_UPDATE => true,
		] );

		wp_clean_plugins_cache();
		wp_update_plugins();
	}

}