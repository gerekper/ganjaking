<?php

namespace ACP\Admin\PageFactory;

use AC;
use AC\Asset\Location;
use ACP\Admin\MenuFactory;
use ACP\Admin\Section;
use ACP\Sorting\Admin\Section\ResetSorting;

class Settings extends AC\Admin\PageFactory\Settings
{

    public function __construct(Location\Absolute $location, MenuFactory $menu_factory)
    {
        parent::__construct($location, $menu_factory, true);
    }

    public function create()
    {
        $page = parent::create();
        $page->add_section(new ResetSorting(), 30);

        $general_section = $page->get_section(AC\Admin\Section\General::NAME);
        if ($general_section instanceof AC\Admin\Section\General) {
            $general_section->add_option(new Section\Partial\LayoutTabs());
        }

        return $page;
    }

}