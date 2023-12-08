<?php

namespace ACP\Migrate\Admin\Section;

use AC\ListScreenRepository\Storage;
use AC\Renderable;
use AC\Table\ListKeysFactoryInterface;
use AC\View;
use ACP\Migrate\Admin\Table;

class Export implements Renderable
{

    private $storage;

    private $list_keys_factory;

    private $is_network;

    public function __construct(
        Storage $storage,
        ListKeysFactoryInterface $list_keys_factory,
        bool $is_network = false
    ) {
        $this->storage = $storage;
        $this->list_keys_factory = $list_keys_factory;
        $this->is_network = $is_network;
    }

    public function render(): string
    {
        $view = new View([
            'table' => new Table\Export(
                $this->storage,
                $this->list_keys_factory,
                $this->is_network
            ),
        ]);

        return $view->set_template('admin/section-export')
                    ->render();
    }

}