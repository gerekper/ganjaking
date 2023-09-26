<?php

namespace ACP\Admin\NetworkPageFactory;

use AC\Asset\Location;
use AC\IntegrationRepository;
use ACP\Admin;
use ACP\Admin\MenuNetworkFactory;

class Addons extends Admin\PageFactory\Addons
{

    public function __construct(
        Location\Absolute $location,
        IntegrationRepository $integrations,
        MenuNetworkFactory $menu_factory
    ) {
        parent::__construct($location, $integrations, $menu_factory);
    }

}