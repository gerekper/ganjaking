<?php

namespace ACP\Service;

use AC\Capabilities;
use AC\Registrable;
use ACP\ActivationTokenFactory;
use ACP\Transient\UpdateCheckTransientHourly;
use ACP\Updates\PluginDataUpdater;

class ForcePluginUpdate implements Registrable {

	/**
	 * @var ActivationTokenFactory
	 */
	private $activation_token_factory;

	/**
	 * @var PluginDataUpdater
	 */
	private $updater;

	public function __construct( ActivationTokenFactory $activation_token_factory, PluginDataUpdater $updater ) {
		$this->activation_token_factory = $activation_token_factory;
		$this->updater = $updater;
	}

	public function register() {
		add_action( 'admin_init', [ $this, 'force_plugin_updates' ] );

		add_action( 'load-plugins.php', [ $this, 'force_plugin_updates_cached' ], 9 );
		add_action( 'load-update-core.php', [ $this, 'force_plugin_updates_cached' ], 9 );
		add_action( 'load-update.php', [ $this, 'force_plugin_updates_cached' ], 9 );
	}

	/**
	 * @return bool
	 */
	private function is_force_check_request() {
		global $pagenow;

		return '1' === filter_input( INPUT_GET, 'force-check' ) && $pagenow === 'update-core.php' && current_user_can( Capabilities::MANAGE );
	}

	/**
	 * Forces to check for updates on a manual request
	 * @return void
	 */
	public function force_plugin_updates() {
		if ( ! $this->is_force_check_request() ) {
			return;
		}

		$this->updater->update( $this->activation_token_factory->create() );
	}

	/**
	 * Forces to check for updates on plugins page
	 * @return void
	 */
	public function force_plugin_updates_cached() {
		$cache = new UpdateCheckTransientHourly();

		if ( $cache->is_expired() ) {
			$this->updater->update( $this->activation_token_factory->create() );

			$cache->save();
		}
	}

}