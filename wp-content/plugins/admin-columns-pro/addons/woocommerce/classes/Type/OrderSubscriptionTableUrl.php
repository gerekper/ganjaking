<?php

declare(strict_types=1);

namespace ACA\WC\Type;

use AC\Type\ListScreenId;
use AC\Type\Url\ListTable;

class OrderSubscriptionTableUrl extends ListTable
{

    public function __construct(ListScreenId $list_id = null)
    {
        parent::__construct('admin.php', $list_id);

        $this->add_arg('page', 'wc-orders--shop_subscription');
    }
}