<?php

namespace ACP\Service;

use AC\PluginInformation;
use AC\Registrable;
use ACP\API;
use ACP\PluginRepository;
use ACP\Storage\PluginsData;
use ACP\Updates\UpdatePlugin;
use ACP\Updates\ViewPluginDetails;

class PluginUpdater implements Registrable {

	/**
	 * @var API
	 */
	private $api;

	/**
	 * @var PluginRepository
	 */
	private $plugin_repository;

	/**
	 * @var PluginsData
	 */
	private $storage;

	public function __construct( API $api, PluginRepository $plugin_repository, PluginsData $storage ) {
		$this->api = $api;
		$this->plugin_repository = $plugin_repository;
		$this->storage = $storage;
	}

	public function register() {
		$plugins = $this->plugin_repository->find_all()->all();

		array_map( [ $this, 'register_update_plugin' ], $plugins );
		array_map( [ $this, 'register_view_plugin_details' ], $plugins );
	}

	private function register_update_plugin( PluginInformation $plugin ) {
		$updater = new UpdatePlugin(
			$plugin->get_basename(),
			$plugin->get_version()->get_value(),
			$this->storage
		);

		$updater->register();
	}

	private function register_view_plugin_details( PluginInformation $plugin ) {
		$view_details = new ViewPluginDetails(
			$plugin->get_dirname(),
			$this->api
		);

		$view_details->register();
	}

}