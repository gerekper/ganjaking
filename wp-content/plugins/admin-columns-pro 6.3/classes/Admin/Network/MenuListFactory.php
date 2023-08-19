<?php

declare(strict_types=1);

namespace ACP\Admin\Network;

use AC;
use AC\Admin\MenuListItems;
use AC\Admin\Type\MenuListItem;
use AC\ListScreen;
use AC\ListScreenFactory;
use AC\Table\ListKeysFactoryInterface;

class MenuListFactory implements AC\Admin\MenuListFactory
{

    private $list_keys_factory;

    private $list_screen_factory;

    public function __construct(ListKeysFactoryInterface $factory, ListScreenFactory\Aggregate $list_screen_factory)
    {
        $this->list_keys_factory = $factory;
        $this->list_screen_factory = $list_screen_factory;
    }

    private function create_menu_item(ListScreen $list_screen): MenuListItem
    {
        return new MenuListItem(
            $list_screen->get_key(),
            $list_screen->get_label(),
            $list_screen->get_group() ?: 'other'
        );
    }

    public function create(): MenuListItems
    {
        $menu = new MenuListItems();

        foreach ($this->list_keys_factory->create()->all() as $list_key) {
            if ( ! $list_key->is_network()) {
                continue;
            }

            if ($this->list_screen_factory->can_create((string)$list_key)) {
                $menu->add($this->create_menu_item($this->list_screen_factory->create((string)$list_key)));
            }
        }

        do_action('acp/admin/network/menu_list', $menu);

        return $menu;
    }

}