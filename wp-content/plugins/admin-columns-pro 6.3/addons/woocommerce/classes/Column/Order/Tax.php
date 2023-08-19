<?php

namespace ACA\WC\Column\Order;

use AC;
use ACA\WC\Sorting\Order\OrderData;
use ACP\ConditionalFormat\FilteredHtmlFormatTrait;
use ACP\ConditionalFormat\Formattable;
use ACP\Sorting\Sortable;
use ACP\Sorting\Type\DataType;

class Tax extends AC\Column implements Formattable, Sortable
{

    use FilteredHtmlFormatTrait;

    public function __construct()
    {
        $this->set_type('column-order_tax')
             ->set_label(__('Tax', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $order = wc_get_order($id);
        $taxes = $order ? $order->get_tax_totals() : false;

        if (empty($taxes)) {
            return $this->get_empty_char();
        }

        $result = [];

        foreach ($taxes as $tax) {
            $result[] = sprintf('<small><strong>%s: </strong></small> %s', $tax->label, $tax->formatted_amount);
        }

        return implode('<br>', $result);
    }

    public function sorting()
    {
        return new OrderData('tax_amount', new DataType(DataType::NUMERIC));
    }

}