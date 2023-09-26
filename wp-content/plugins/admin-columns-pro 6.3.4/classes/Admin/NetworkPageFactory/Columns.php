<?php

namespace ACP\Admin\NetworkPageFactory;

use AC;
use AC\Admin\PageFactoryInterface;
use AC\Asset\Location;
use AC\Controller\Middleware\ListScreenAdmin;
use AC\DefaultColumnsRepository;
use AC\ListScreenRepository\Storage;
use AC\Request;
use ACP\Admin;
use InvalidArgumentException;

class Columns implements PageFactoryInterface
{

    private $location_core;

    private $list_screen_factory;

    protected $list_screen_uninitialized;

    protected $menu_list_factory;

    private $storage;

    private $list_keys_factory;

    private $menu_factory;

    public function __construct(
        Location\Absolute $location_core,
        AC\ListScreenFactory\Aggregate $list_screen_factory,
        AC\Admin\ListScreenUninitialized $list_screen_uninitialized,
        Admin\Network\MenuListFactory $menu_list_factory,
        Storage $storage,
        AC\Table\ListKeysFactoryInterface $list_keys_factory,
        Admin\MenuNetworkFactory $menu_factory
    ) {
        $this->location_core = $location_core;
        $this->list_screen_factory = $list_screen_factory;
        $this->list_screen_uninitialized = $list_screen_uninitialized;
        $this->menu_list_factory = $menu_list_factory;
        $this->storage = $storage;
        $this->list_keys_factory = $list_keys_factory;
        $this->menu_factory = $menu_factory;
    }

    public function create()
    {
        $request = new Request();

        $request->add_middleware(
            new ListScreenAdmin(
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

        return new AC\Admin\Page\Columns(
            $this->location_core,
            $list_screen,
            new DefaultColumnsRepository(),
            $this->list_screen_uninitialized->find_all_network(),
            new AC\Admin\Section\Partial\Menu($this->menu_list_factory),
            new AC\Admin\View\Menu($this->menu_factory->create('columns'))
        );
    }

}