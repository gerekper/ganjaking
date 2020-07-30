<?php

namespace ACP\API\Request;

use ACP\API\Request;
use ACP\Plugins;
use ACP\Type\License\Key;
use ACP\Type\SiteUrl;

/**
 * Used for the WordPress plugin updater
 */
class ProductsUpdate extends Request {

	public function __construct( SiteUrl $site_url, Plugins $plugins, Key $license_key = null ) {
		parent::__construct( [
			'command'          => 'products_update',
			'subscription_key' => $license_key ? $license_key->get_value() : null,
			'site_url'         => $site_url->get_url(),
			'network_active'   => $site_url->is_network(),
			'versions'         => $this->format_versions( $plugins ),
		] );
	}

	/**
	 * @param Plugins $plugins
	 *
	 * @return array
	 */
	public function format_versions( Plugins $plugins ) {
		$args = [];

		foreach ( $plugins->all() as $plugin ) {
			$args[ $plugin->get_basename() ] = $plugin->get_version();
		}

		return $args;
	}

}