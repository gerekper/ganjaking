<?php

namespace ACP\Admin\NetworkPageFactory;

use AC;
use AC\Admin\PageFactoryInterface;
use AC\Asset\Location;
use AC\ListScreenRepository\Storage;
use AC\Table\ListKeysFactoryInterface;
use ACP\Admin\MenuNetworkFactory;
use ACP\Admin\Page;
use ACP\Migrate\Admin\Section\Export;
use ACP\Migrate\Admin\Section\Import;

class Tools implements PageFactoryInterface
{

    private $location;

    private $storage;

    private $menu_factory;

    private $list_keys_factory;

    public function __construct(
        Location\Absolute $location,
        Storage $storage,
        MenuNetworkFactory $menu_factory,
        ListKeysFactoryInterface $list_keys_factory
    ) {
        $this->location = $location;
        $this->storage = $storage;
        $this->menu_factory = $menu_factory;
        $this->list_keys_factory = $list_keys_factory;
    }

    public function create()
    {
        $page = new Page\Tools(
            $this->location,
            new AC\Admin\View\Menu($this->menu_factory->create('import-export'))
        );

        $page->add_section(new Export($this->storage, $this->list_keys_factory, true))
             ->add_section(new Import());

        return $page;
    }

}