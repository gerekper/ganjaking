<?php

namespace ACP\API\Request;

use ACP\API\Request;
use ACP\Type\License\Key;
use ACP\Type\SiteUrl;

/**
 * Used for updating subscription information, such as expiration date.
 */
class SubscriptionDetails extends Request {

	public function __construct( Key $license_key, SiteUrl $site_url ) {
		parent::__construct( [
			'command'          => 'subscription_details',
			'subscription_key' => $license_key->get_value(),
			'site_url'         => $site_url->get_url(),
			'network_active'   => $site_url->is_network(),
		] );
	}

}