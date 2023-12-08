<?php

namespace ACA\WC\Column\Order;

use AC;
use ACA\WC\Search;
use ACA\WC\Sorting\Order\OperationalData;
use ACP;

class CreatedVersion extends AC\Column implements ACP\Search\Searchable, ACP\ConditionalFormat\Formattable,
                                                  ACP\Sorting\Sortable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-order_created_version')
             ->set_label(__('WooCommerce Version', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_raw_value($id)
    {
        $order = wc_get_order($id);

        return $order
            ? $order->get_version()
            : false;
    }

    public function search()
    {
        return new Search\Order\CreatedVersion();
    }

    public function sorting()
    {
        return new OperationalData('woocommerce_version');
    }

}