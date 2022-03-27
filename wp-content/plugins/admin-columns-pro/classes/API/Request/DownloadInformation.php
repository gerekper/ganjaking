<?php

namespace ACP\API\Request;

use ACP\API\Request;
use ACP\Type\ActivationToken;
use ACP\Type\SiteUrl;

/**
 * Used for installing 'add-ons'
 */
class DownloadInformation extends Request {

	/**
	 * @param string          $plugin_name e.g. 'plugin-name'
	 * @param ActivationToken $token
	 * @param SiteUrl         $site_url
	 */
	public function __construct( $plugin_name, ActivationToken $token, SiteUrl $site_url ) {
		parent::__construct( [
			'command'          => 'download_link',
			'activation_url'   => $site_url->get_url(),
			'plugin_name'      => $plugin_name,
			$token->get_type() => $token->get_token(),
		] );
	}

}