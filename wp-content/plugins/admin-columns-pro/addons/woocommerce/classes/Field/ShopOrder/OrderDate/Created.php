<?php

namespace ACA\WC\Field\ShopOrder\OrderDate;

use ACA\WC\Field\ShopOrder\OrderDate;
use ACP;
use WC_Order;

class Created extends OrderDate implements ACP\Sorting\Sortable, ACP\Search\Searchable
{

    public function set_label()
    {
        $this->label = __('Created', 'codepress-admin-columns');
    }

    public function get_date(WC_Order $order)
    {
        return $order->get_date_created();
    }

    public function sorting()
    {
        return new ACP\Sorting\Model\Post\PostField(
            'post_date',
            new ACP\Sorting\Type\DataType(ACP\Sorting\Type\DataType::DATETIME)
        );
    }

    public function search()
    {
        return new ACP\Search\Comparison\Post\Date\PostDate('shop_order');
    }

}