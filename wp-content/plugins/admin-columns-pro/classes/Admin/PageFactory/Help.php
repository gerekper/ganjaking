<?php

namespace ACP\Admin\PageFactory;

use AC;
use AC\Asset\Location;
use ACP\Admin\MenuFactory;

class Help extends AC\Admin\PageFactory\Help
{

    public function __construct(Location\Absolute $location, MenuFactory $menu_factory)
    {
        parent::__construct($location, $menu_factory);
    }

}