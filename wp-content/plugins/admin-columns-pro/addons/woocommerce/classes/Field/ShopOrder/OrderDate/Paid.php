<?php

namespace ACA\WC\Field\ShopOrder\OrderDate;

use AC\Meta\QueryMetaFactory;
use ACA\WC\Field\ShopOrder\OrderDate;
use ACA\WC\Search;
use ACP;
use ACP\Sorting\Type\DataType;
use WC_Order;

class Paid extends OrderDate implements ACP\Sorting\Sortable, ACP\Search\Searchable
{

    public function set_label()
    {
        $this->label = __('Paid', 'codepress-admin-columns');
    }

    public function get_date(WC_Order $order)
    {
        return $order->get_date_paid();
    }

    public function sorting()
    {
        return new ACP\Sorting\Model\Post\Meta('_date_paid', new DataType(DataType::NUMERIC));
    }

    public function search()
    {
        return new ACP\Search\Comparison\Meta\DateTime\Timestamp(
            '_date_paid',
            (new QueryMetaFactory())->create_with_post_type('_date_paid', 'shop_order')
        );
    }

}