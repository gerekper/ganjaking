<?php

namespace ACA\WC\Column\Order;

use AC;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;
use WC_Order;
use WC_Order_Item_Product;

class TotalWeight extends AC\Column implements ACP\Export\Exportable, ACP\ConditionalFormat\Formattable
{

    public function __construct()
    {
        $this->set_type('column-order_weight')
             ->set_label(__('Total Order Weight', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $order = wc_get_order($id);
        $weight = $this->get_order_weight($order);

        return $weight > 0
            ? sprintf('%s %s', wc_format_decimal($weight), get_option('woocommerce_weight_unit'))
            : $this->get_empty_char();
    }

    private function get_order_weight(WC_Order $order): float
    {
        $total_weight = 0;

        foreach ($order->get_items() as $item) {
            if ( ! $item instanceof WC_Order_Item_Product || ! $item->get_product()) {
                continue;
            }

            $weight = (int)$item->get_quantity() * (float)$item->get_product()->get_weight();
            $total_weight += $weight;
        }

        return $total_weight;
    }

    public function export()
    {
        return new ACP\Export\Model\Value($this);
    }

    public function conditional_format(): ?FormattableConfig
    {
        return new FormattableConfig(
            ACP\ConditionalFormat\Formatter\SanitizedFormatter::from_ignore_strings(
                new ACP\ConditionalFormat\Formatter\FloatFormatter()
            )
        );
    }

}