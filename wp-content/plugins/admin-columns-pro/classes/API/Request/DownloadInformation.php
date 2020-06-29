<?php

namespace ACP\API\Request;

use ACP\API\Request;
use ACP\Type\License\Key;
use ACP\Type\SiteUrl;

/**
 * Used for installing 'add-ons'
 */
class DownloadInformation extends Request {

	/**
	 * @param string  $plugin_name e.g. 'plugin-name'
	 * @param Key     $license_key
	 * @param SiteUrl $site_url
	 */
	public function __construct( $plugin_name, Key $license_key, SiteUrl $site_url ) {
		parent::__construct( [
			'command'          => 'download_link',
			'subscription_key' => $license_key->get_value(),
			'plugin_name'      => $plugin_name,
			'site_url'         => $site_url->get_url(),
			'network_active'   => $site_url->is_network(),
		] );
	}

}