<?php

namespace ACP\API\Request;

use ACP\API\Request;
use ACP\Type\SiteUrl;

class ServerCheck extends Request {

	public function __construct( SiteUrl $site_url ) {
		parent::__construct( [
			'command'        => 'domain_check',
			'site_url'       => $site_url->get_url(),
			'network_active' => $site_url->is_network(),
		] );
	}

}