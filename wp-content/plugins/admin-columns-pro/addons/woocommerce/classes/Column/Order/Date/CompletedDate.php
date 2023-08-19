<?php

namespace ACA\WC\Column\Order\Date;

use AC;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;

class CompletedDate extends AC\Column implements ACP\Search\Searchable, ACP\ConditionalFormat\Formattable,
                                                 ACP\Sorting\Sortable
{

    public function __construct()
    {
        $this->set_type('column-order_date_completed')
             ->set_label(__('Date Completed', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_raw_value($id)
    {
        $order = wc_get_order($id);
        $date = $order ? $order->get_date_completed() : null;

        return $date
            ? $date->format('Y-m-d H:i:s')
            : false;
    }

    public function register_settings()
    {
        $this->add_setting(new AC\Settings\Column\Date($this));
    }

    public function search()
    {
        return new Search\Order\Date\CompletedDate();
    }

    public function sorting()
    {
        return new Sorting\Order\Date\CompletedDate();
    }

    public function conditional_format(): ?FormattableConfig
    {
        return new ACP\ConditionalFormat\FormattableConfig(
            new ACP\ConditionalFormat\Formatter\DateFormatter\FormatFormatter('Y-m-d H:i:s')
        );
    }

}