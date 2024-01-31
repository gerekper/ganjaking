<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\Search;
use ACP;

class SubscriptionRelationship extends AC\Column
    implements ACP\Export\Exportable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('subscription_relationship')
             ->set_original(true);
    }

    public function export()
    {
        return false;
    }

}