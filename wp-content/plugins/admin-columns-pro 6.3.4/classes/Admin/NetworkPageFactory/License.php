<?php

namespace ACP\Admin\NetworkPageFactory;

use AC\Asset\Location;
use AC\Entity\Plugin;
use ACP\Access\ActivationStorage;
use ACP\Access\PermissionsStorage;
use ACP\ActivationTokenFactory;
use ACP\Admin;
use ACP\Admin\MenuNetworkFactory;
use ACP\LicenseKeyRepository;
use ACP\Type\SiteUrl;

class License extends Admin\PageFactory\License
{

    public function __construct(
        Location\Absolute $location,
        MenuNetworkFactory $menu_factory,
        SiteUrl $site_url,
        ActivationTokenFactory $activation_token_factory,
        ActivationStorage $activation_storage,
        PermissionsStorage $permission_storage,
        LicenseKeyRepository $license_key_repository,
        Plugin $plugin
    ) {
        parent::__construct(
            $location,
            $menu_factory,
            $site_url,
            $activation_token_factory,
            $activation_storage,
            $permission_storage,
            $license_key_repository,
            $plugin
        );
    }

}