<?php

namespace ACP\API\Request;

use ACP\API\Request;
use ACP\Type\LicenseKey;
use ACP\Type\SiteUrl;

class Activate extends Request {

	public function __construct( LicenseKey $license_key, SiteUrl $site_url ) {
		parent::__construct( [
			'command'          => 'activate',
			'subscription_key' => $license_key->get_token(),
			'activation_url'   => $site_url->get_url(),
		] );
	}

}