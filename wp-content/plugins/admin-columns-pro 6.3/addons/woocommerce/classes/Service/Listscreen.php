<?php

declare(strict_types=1);

namespace ACA\WC\Service;

use AC\Registerable;
use AC\Table\ListKeyCollection;
use AC\Type\ListKey;

class Listscreen implements Registerable
{

    public function register(): void
    {
        add_action('ac/list_keys', [$this, 'add_key']);
    }

    public function add_key(ListKeyCollection $keys): void
    {
        $keys->add(new ListKey('wc_order'));
        $keys->add(new ListKey('wc_order_subscription'));
    }

}