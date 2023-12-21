<?php

namespace ACP\Admin\PageFactory;

use AC;
use AC\Admin\PageFactoryInterface;
use AC\Asset\Location;
use AC\ListScreenRepository\Storage;
use ACP;
use ACP\Admin\Page;
use ACP\Migrate\Admin\Section;

class Tools implements PageFactoryInterface
{

    private $location;

    private $storage;

    private $menu_factory;

    private $list_keys_factory;

    private $template_repository;

    public function __construct(
        Location\Absolute $location,
        Storage $storage,
        ACP\Admin\MenuFactory $menu_factory,
        AC\Table\ListKeysFactoryInterface $list_keys_factory,
        ACP\ListScreenRepository\Template $template_repository
    ) {
        $this->location = $location;
        $this->storage = $storage;
        $this->menu_factory = $menu_factory;
        $this->list_keys_factory = $list_keys_factory;
        $this->template_repository = $template_repository;
    }

    public function create()
    {
        $page = new Page\Tools(
            $this->location,
            new AC\Admin\View\Menu($this->menu_factory->create('import-export'))
        );

        $page->add_section(new Section\Export($this->storage, $this->list_keys_factory))
             ->add_section(new Section\Import())
             ->add_section(new Section\Templates($this->template_repository, false));

        return $page;
    }

}