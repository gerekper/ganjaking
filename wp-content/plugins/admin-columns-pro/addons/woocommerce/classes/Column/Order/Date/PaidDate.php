<?php

namespace ACA\WC\Column\Order\Date;

use AC;
use ACA\WC\Column\Order\FilterableDateTrait;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;

class PaidDate extends AC\Column implements ACP\Search\Searchable, ACP\ConditionalFormat\Formattable,
                                            ACP\Sorting\Sortable
{

    use FilterableDateTrait;

    public function __construct()
    {
        $this->set_type('column-order_date_paid')
             ->set_label(__('Date Paid', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_raw_value($id)
    {
        $order = wc_get_order($id);
        $date = $order ? $order->get_date_paid() : null;

        return $date
            ? $date->format('Y-m-d H:i:s')
            : false;
    }

    public function register_settings()
    {
        $this->add_setting(new AC\Settings\Column\Date($this));
        $this->add_setting(new ACP\Filtering\Settings\Date($this, ['future_past']));
    }

    public function search()
    {
        return new Search\Order\Date\PaidDate();
    }

    public function sorting()
    {
        return new Sorting\Order\Date\PaidDate();
    }

    public function conditional_format(): ?FormattableConfig
    {
        return new ACP\ConditionalFormat\FormattableConfig(
            new ACP\ConditionalFormat\Formatter\DateFormatter\FormatFormatter('Y-m-d H:i:s')
        );
    }

}