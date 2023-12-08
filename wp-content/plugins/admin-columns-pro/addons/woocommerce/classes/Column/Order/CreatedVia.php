<?php

namespace ACA\WC\Column\Order;

use AC;
use ACA\WC\Scheme\OrderOperationalData;
use ACA\WC\Search;
use ACA\WC\Sorting\Order\OperationalData;
use ACP;

class CreatedVia extends AC\Column implements ACP\Search\Searchable, ACP\Export\Exportable,
                                              ACP\ConditionalFormat\Formattable, ACP\Sorting\Sortable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-order_created_via')
             ->set_label(__('Created via', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $order = wc_get_order($id);

        $created_via = $order
            ? $order->get_created_via()
            : null;

        return $created_via ?: $this->get_empty_char();
    }

    public function search()
    {
        return new Search\Order\CreatedVia();
    }

    public function sorting()
    {
        return new OperationalData(OrderOperationalData::CREATED_VIA);
    }

    public function export()
    {
        return new ACP\Export\Model\Value($this);
    }
}