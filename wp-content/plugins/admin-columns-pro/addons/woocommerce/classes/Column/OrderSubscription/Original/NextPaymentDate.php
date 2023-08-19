<?php

namespace ACA\WC\Column\OrderSubscription\Original;

use AC;
use ACA\WC\Export;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;

class NextPaymentDate extends AC\Column implements ACP\Search\Searchable, ACP\Export\Exportable, ACP\Sorting\Sortable
{

    public function __construct()
    {
        $this->set_type('next_payment_date')
             ->set_original(true);
    }

    protected function get_meta_key(): string
    {
        return '_schedule_next_payment';
    }

    public function search()
    {
        return new Search\Meta\Date\ISO($this->get_meta_key(), 'order_subscription');
    }

    public function export()
    {
        return new Export\OrderSubscription\SubscriptionDate('next_payment');
    }

    public function sorting()
    {
        return new Sorting\Order\OrderMeta(
            $this->get_meta_key(),
            new ACP\Sorting\Type\DataType(ACP\Sorting\Type\DataType::DATETIME)
        );
    }
}