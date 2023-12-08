<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\ConditionalFormat\Formatter\PriceFormatter;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;

class Discount extends AC\Column implements ACP\ConditionalFormat\Formattable, ACP\Search\Searchable,
                                            ACP\Sorting\Sortable
{

    public function __construct()
    {
        $this->set_type('column-wc-order_discount')
             ->set_label(__('Order Discount', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function conditional_format(): ?FormattableConfig
    {
        return new FormattableConfig(new PriceFormatter());
    }

    public function get_value($id)
    {
        $order = wc_get_order($id);

        if ( ! $order->get_total_discount()) {
            return $this->get_empty_char();
        }

        return $order->get_discount_to_display();
    }

    public function sorting()
    {
        return new ACP\Sorting\Model\Post\Meta('_cart_discount');
    }

    public function search()
    {
        return new ACP\Search\Comparison\Meta\Decimal('_cart_discount');
    }

}