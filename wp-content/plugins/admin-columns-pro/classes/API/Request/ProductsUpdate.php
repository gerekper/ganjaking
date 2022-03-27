<?php

namespace ACP\API\Request;

use ACP\API\Request;
use ACP\Plugins;
use ACP\Type\ActivationToken;
use ACP\Type\SiteUrl;

/**
 * Used for the WordPress plugin updater
 */
class ProductsUpdate extends Request {

	public function __construct( SiteUrl $site_url, ActivationToken $activation_token = null ) {
		$args = [
			'command'        => 'products_update',
			'activation_url' => $site_url->get_url(),
		];

		if ( $activation_token ) {
			$args[ $activation_token->get_type() ] = $activation_token->get_token();
		}

		parent::__construct( $args );
	}

	/**
	 * @param Plugins $plugins
	 *
	 * @return array
	 */
	public function format_versions( Plugins $plugins ) {
		$args = [];

		foreach ( $plugins->all() as $plugin ) {
			$args[ $plugin->get_basename() ] = $plugin->get_version()->get_value();
		}

		return $args;
	}

}