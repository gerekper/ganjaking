<?php

namespace ACP\API\Request;

use ACP\API\Request;
use ACP\Type\ActivationToken;
use ACP\Type\SiteUrl;

class Deactivate extends Request {

	public function __construct( ActivationToken $token, SiteUrl $site_url ) {
		parent::__construct( [
			'command'          => 'deactivate',
			'activation_url'   => $site_url->get_url(),
			$token->get_type() => $token->get_token(),
		] );
	}

}