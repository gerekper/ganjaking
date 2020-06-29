<?php

namespace ACP;

use AC\Capabilities;
use AC\Registrable;
use ACP\API\Cached;
use ACP\Type\SiteUrl;

class Updates implements Registrable {

	/**
	 * @var API
	 */
	private $api;

	/**
	 * @var LicenseKeyRepository
	 */
	private $license_key_repository;

	/**
	 * @var SiteUrl
	 */
	private $site_url;

	/**
	 * @var Plugins
	 */
	private $plugins;

	public function __construct( API $api, LicenseKeyRepository $license_key_repository, SiteUrl $site_url, Plugins $plugins ) {
		$this->api = $api;
		$this->license_key_repository = $license_key_repository;
		$this->site_url = $site_url;
		$this->plugins = $plugins;
	}

	public function register() {
		add_action( 'init', [ $this, 'register_updater' ], 9 );
		add_action( 'init', [ $this, 'force_plugin_update_check_on_request' ] );
	}

	public function register_updater() {
		foreach ( $this->plugins->all() as $plugin ) {
			// Add plugins to update process
			$updater = new Updates\Updater( $plugin, new API\Cached( $this->api ), $this->site_url, $this->plugins, $this->license_key_repository->find() );
			$updater->register();

			// Click "view details" on plugin page
			$view_details = new Updates\ViewPluginDetails( $plugin->get_dirname(), $this->api );
			$view_details->register();
		}
	}

	public function force_plugin_update_check_on_request() {
		global $pagenow;

		if ( '1' !== filter_input( INPUT_GET, 'force-check' )
		     || $pagenow !== 'update-core.php'
		     || ! current_user_can( Capabilities::MANAGE ) ) {
			return;
		}

		$api = new API\Cached( $this->api );
		$api->dispatch(
			new API\Request\ProductsUpdate( $this->site_url, $this->plugins, $this->license_key_repository->find() ), [
				Cached::FORCE_UPDATE => true,
			]
		);
	}

}