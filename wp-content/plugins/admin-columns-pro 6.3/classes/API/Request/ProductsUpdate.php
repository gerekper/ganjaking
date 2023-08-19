<?php

namespace ACP\API\Request;

use ACP\API\Request;
use ACP\Type\ActivationToken;
use ACP\Type\SiteUrl;

class ProductsUpdate extends Request
{

    public function __construct(SiteUrl $site_url, ActivationToken $activation_token = null)
    {
        $args = [
            'command'        => 'products_update',
            'activation_url' => $site_url->get_url(),
        ];

        if ($activation_token) {
            $args[$activation_token->get_type()] = $activation_token->get_token();
        }

        parent::__construct($args);
    }

}