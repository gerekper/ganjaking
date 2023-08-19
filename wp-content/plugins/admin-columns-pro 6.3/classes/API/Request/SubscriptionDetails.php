<?php

namespace ACP\API\Request;

use AC\Integration;
use AC\IntegrationRepository;
use ACP\API\Request;
use ACP\Type\ActivationToken;
use ACP\Type\SiteUrl;

/**
 * Used for updating subscription information, such as expiration date.
 */
class SubscriptionDetails extends Request
{

    public function __construct(SiteUrl $site_url, ActivationToken $activation_token, IntegrationRepository $repository)
    {
        $args = [
            'command'        => 'subscription_details',
            'activation_url' => $site_url->get_url(),
        ];

        $args[$activation_token->get_type()] = $activation_token->get_token();

        /**
         * @var Integration $integration
         */
        foreach ($repository->find_all_active() as $integration) {
            $args['meta'][$integration->get_slug()] = ACP_VERSION;
        }

        parent::__construct($args);
    }

}