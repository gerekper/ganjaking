<?php

namespace ACP\RequestHandler\Ajax;

use AC;
use AC\Nonce;
use ACP\ActivationTokenFactory;
use ACP\RequestAjaxHandler;
use ACP\Transient\UpdateCheckTransient;
use ACP\Updates\PluginDataUpdater;

class UpdatePlugins implements RequestAjaxHandler {

	/**
	 * @var ActivationTokenFactory
	 */
	private $activation_token_factory;

	/**
	 * @var PluginDataUpdater
	 */
	private $updater;

	/**
	 * @var UpdateCheckTransient
	 */
	private $cache;

	public function __construct( ActivationTokenFactory $activation_token_factory, PluginDataUpdater $updater, UpdateCheckTransient $cache ) {
		$this->activation_token_factory = $activation_token_factory;
		$this->updater = $updater;
		$this->cache = $cache;
	}

	public function handle() {
		$request = new AC\Request();

		if ( ! ( new Nonce\Ajax() )->verify( $request ) ) {
			wp_send_json_error();
		}

		if ( $this->cache->is_expired() ) {
			$this->updater->update( $this->activation_token_factory->create() );

			$this->cache->save( HOUR_IN_SECONDS * 12 );
		}
	}

}