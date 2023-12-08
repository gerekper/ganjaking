<?php

namespace ACA\WC\Column\OrderSubscription\Original;

use AC;
use ACA\WC;
use ACA\WC\Editing;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;

class StartDate extends AC\Column implements ACP\Search\Searchable, ACP\Export\Exportable, ACP\Editing\Editable,
                                             ACP\Sorting\Sortable
{

    public function __construct()
    {
        $this->set_type('start_date')
             ->set_original(true);
    }

    public function get_meta_key(): string
    {
        return '_schedule_start';
    }

    public function export()
    {
        return new WC\Export\OrderSubscription\SubscriptionDate('start');
    }

    public function editing()
    {
        return new Editing\OrderSubscription\Date('start');
    }

    public function search()
    {
        return new Search\OrderMeta\IsoDate($this->get_meta_key());
    }

    public function sorting()
    {
        return new Sorting\Order\OrderMeta(
            $this->get_meta_key(),
            new ACP\Sorting\Type\DataType(ACP\Sorting\Type\DataType::DATETIME)
        );
    }

}