<?php

namespace ACP\API\Request;

use ACP\API\Request;
use ACP\Plugins;
use ACP\Type\ActivationToken;
use ACP\Type\SiteUrl;

/**
 * Used for updating subscription information, such as expiration date.
 */
class SubscriptionDetails extends Request {

	public function __construct( SiteUrl $site_url, Plugins $plugins, ActivationToken $activation_token ) {
		$args = [
			'command'        => 'subscription_details',
			'activation_url' => $site_url->get_url(),
		];

		$args[ $activation_token->get_type() ] = $activation_token->get_token();

		// @since 5.7
		foreach ( $plugins->all() as $plugin ) {
			$args['meta'][ $plugin->get_dirname() ] = $plugin->get_version()->get_value();
		}

		parent::__construct( $args );
	}

}