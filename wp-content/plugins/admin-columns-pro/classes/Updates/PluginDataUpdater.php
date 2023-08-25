<?php

declare(strict_types=1);

namespace ACP\Updates;

use ACP\API;
use ACP\ApiFactory;
use ACP\Storage;
use ACP\Type\ActivationToken;
use ACP\Type\SiteUrl;

class PluginDataUpdater
{

    private $api_factory;

    private $site_url;

    private $storage_factory;

    public function __construct(ApiFactory $api_factory, SiteUrl $site_url, Storage\PluginsDataFactory $storage_factory)
    {
        $this->api_factory = $api_factory;
        $this->site_url = $site_url;
        $this->storage_factory = $storage_factory;
    }

    public function update(ActivationToken $token = null): void
    {
        $response = $this->api_factory->create()->dispatch(
            new API\Request\ProductsUpdate($this->site_url, $token)
        );
 
        if ($response->has_error()) {
            return;
        }

        $this->storage_factory->create()->save((array)$response->get_body());
    }

}