<?php

namespace ACA\WC\Column\Order;

use AC;
use ACA\WC\ConditionalFormat\Formatter\PriceFormatter;
use ACA\WC\Scheme\OrderOperationalData;
use ACA\WC\Search;
use ACA\WC\Sorting\Order\OperationalData;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\Sorting\Type\DataType;

class Discount extends AC\Column implements ACP\Search\Searchable, ACP\Export\Exportable,
                                            ACP\ConditionalFormat\Formattable, ACP\Sorting\Sortable
{

    public function __construct()
    {
        $this->set_type('column-order_discount')
             ->set_label(__('Discount Total', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $order = wc_get_order($id);

        return $order && $order->get_total_discount() ? $order->get_discount_to_display() : $this->get_empty_char();
    }

    public function get_raw_value($id)
    {
        $order = wc_get_order($id);

        return $order
            ? $order->get_total_discount()
            : false;
    }

    public function search()
    {
        return new Search\Order\Discount();
    }

    public function sorting()
    {
        return new OperationalData(
            OrderOperationalData::DISCOUNT_TOTAL_AMOUNT,
            new DataType(DataType::NUMERIC)
        );
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedValue($this);
    }

    public function conditional_format(): ?FormattableConfig
    {
        return new FormattableConfig(new PriceFormatter());
    }

}