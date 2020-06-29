<?php

namespace ACP\API\Request;

use ACP\API\Request;
use ACP\Type\License\Key;
use ACP\Type\SiteUrl;

class Activation extends Request {

	public function __construct( Key $license_key, SiteUrl $site_url ) {
		parent::__construct( [
			'command'          => 'activation',
			'subscription_key' => $license_key->get_value(),
			'site_url'         => $site_url->get_url(),
			'network_active'   => $site_url->is_network(),
		] );
	}

}