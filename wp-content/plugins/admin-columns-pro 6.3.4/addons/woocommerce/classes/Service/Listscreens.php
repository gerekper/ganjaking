<?php

declare(strict_types=1);

namespace ACA\WC\Service;

use AC\Registerable;
use AC\Table\ListKeyCollection;
use AC\Type\ListKey;

class Listscreens implements Registerable
{

    public function register(): void
    {
        add_action('ac/list_keys', [$this, 'add_key']);
        add_filter('ac/post_types', [$this, 'deregister_shop_order']);
    }

    public function add_key(ListKeyCollection $keys): void
    {
        $keys->add(new ListKey('wc_order'));
        $keys->add(new ListKey('wc_order_subscription'));
    }

    public function deregister_shop_order(array $post_types): array
    {
        unset($post_types['shop_order']);

        return $post_types;
    }

}