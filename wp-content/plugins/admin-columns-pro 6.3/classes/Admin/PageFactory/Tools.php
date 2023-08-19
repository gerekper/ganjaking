<?php

namespace ACP\Admin\PageFactory;

use AC;
use AC\Admin\PageFactoryInterface;
use AC\Asset\Location;
use AC\ListScreenRepository\Storage;
use ACP;
use ACP\Admin\Page;
use ACP\Migrate\Admin\Section\Export;
use ACP\Migrate\Admin\Section\Import;

class Tools implements PageFactoryInterface
{

    private $location;

    private $storage;

    private $menu_factory;

    private $list_keys_factory;

    private $segment_storage;

    public function __construct(
        Location\Absolute $location,
        Storage $storage,
        ACP\Admin\MenuFactory $menu_factory,
        AC\Table\ListKeysFactoryInterface $list_keys_factory,
        ACP\Search\SegmentRepository\Storage $segment_storage
    ) {
        $this->location = $location;
        $this->storage = $storage;
        $this->menu_factory = $menu_factory;
        $this->list_keys_factory = $list_keys_factory;
        $this->segment_storage = $segment_storage;
    }

    public function create()
    {
        $page = new Page\Tools(
            $this->location,
            new AC\Admin\View\Menu($this->menu_factory->create('import-export'))
        );
        $page->add_section(new Export($this->storage, $this->list_keys_factory, $this->segment_storage))
             ->add_section(new Import());

        return $page;
    }

}