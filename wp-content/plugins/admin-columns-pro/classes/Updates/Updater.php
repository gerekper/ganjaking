<?php

namespace ACP\Updates;

use AC\Plugin\Version;
use AC\PluginInformation;
use AC\Registrable;
use ACP\API;
use ACP\Plugins;
use ACP\RequestDispatcher;
use ACP\Type\License\Key;
use ACP\Type\SiteUrl;

/**
 * Hooks into the WordPress update process for plugins
 */
class Updater implements Registrable {

	/** @var PluginInformation */
	private $plugin;

	/** @var RequestDispatcher */
	private $api;

	/**
	 * @var SiteUrl
	 */
	private $site_url;

	/**
	 * @var Plugins
	 */
	private $plugins;

	/**
	 * @var Key|null
	 */
	private $license_key;

	public function __construct( PluginInformation $plugin, RequestDispatcher $api, SiteUrl $site_url, Plugins $plugins, Key $license_key = null ) {
		$this->plugin = $plugin;
		$this->api = $api;
		$this->site_url = $site_url;
		$this->plugins = $plugins;
		$this->license_key = $license_key;
	}

	public function register() {
		add_action( 'pre_set_site_transient_update_plugins', [ $this, 'check_update' ] );
	}

	public function check_update( $transient ) {
		$response = $this->api->dispatch( new API\Request\ProductsUpdate( $this->site_url, $this->plugins, $this->license_key ) );

		if ( ! $response || $response->has_error() ) {
			return $transient;
		}

		$plugin_data = $response->get( $this->plugin->get_dirname() );

		if ( empty( $plugin_data ) ) {
			return $transient;
		}

		$plugin_data = (object) $plugin_data;

		if ( $this->plugin->get_version()->is_lt( new Version( $plugin_data->new_version ) ) ) {
			$transient->response[ $this->plugin->get_basename() ] = $plugin_data;
		}

		return $transient;
	}

}