<?php

namespace ACP\Admin\PageFactory;

use AC;
use AC\Admin\Page;
use AC\Admin\PageFactoryInterface;
use AC\Asset\Location;
use AC\DefaultColumnsRepository;
use AC\ListScreenRepository\Storage;
use AC\Request;
use ACP\Admin;
use InvalidArgumentException;

class Columns implements PageFactoryInterface
{

    private $location_core;

    private $storage;

    private $menu_list_factory;

    private $menu_factory;

    private $list_screen_factory;

    private $list_keys_factory;

    protected $list_screen_uninitialized;

    public function __construct(
        Location\Absolute $location_core,
        Storage $storage,
        Admin\MenuFactory $menu_factory,
        AC\Admin\MenuListFactory\MenuFactory $menu_list_factory,
        AC\ListScreenFactory\Aggregate $list_screen_factory,
        AC\Table\ListKeysFactoryInterface $list_keys_factory,
        AC\Admin\ListScreenUninitialized $list_screen_uninitialized
    ) {
        $this->location_core = $location_core;
        $this->storage = $storage;
        $this->menu_factory = $menu_factory;
        $this->menu_list_factory = $menu_list_factory;
        $this->list_screen_factory = $list_screen_factory;
        $this->list_keys_factory = $list_keys_factory;
        $this->list_screen_uninitialized = $list_screen_uninitialized;
    }

    public function create()
    {
        $request = new Request();

        $request->add_middleware(
            new AC\Controller\Middleware\ListScreenAdmin(
                $this->storage,
                new AC\Admin\Preference\ListScreen(),
                $this->list_screen_factory,
                $this->list_keys_factory
            )
        );

        $list_screen = $request->get('list_screen');

        if ( ! $list_screen) {
            throw new InvalidArgumentException('Invalid screen.');
        }

        return new Page\Columns(
            $this->location_core,
            $list_screen,
            new DefaultColumnsRepository(),
            $this->list_screen_uninitialized->find_all_sites(),
            new AC\Admin\Section\Partial\Menu($this->menu_list_factory),
            new AC\Admin\View\Menu($this->menu_factory->create('columns'))
        );
    }

}