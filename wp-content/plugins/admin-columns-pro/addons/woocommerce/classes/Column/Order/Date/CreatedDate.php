<?php

namespace ACA\WC\Column\Order\Date;

use AC;
use ACA\WC\Column\Order\FilterableDateTrait;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;

class CreatedDate extends AC\Column implements ACP\Search\Searchable, ACP\ConditionalFormat\Formattable,
                                               ACP\Sorting\Sortable, ACP\Filtering\FilterableDateSetting
{

    use FilterableDateTrait;

    public function __construct()
    {
        $this->set_type('column-order_date_created')
             ->set_label(__('Date Created', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_raw_value($id)
    {
        $order = wc_get_order($id);
        $date = $order ? $order->get_date_created() : null;

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
        return new Search\Order\Date\CreatedDate();
    }

    public function sorting()
    {
        return new Sorting\Order\OrderBy('date_created');
    }

    public function conditional_format(): ?FormattableConfig
    {
        return new ACP\ConditionalFormat\FormattableConfig(
            new ACP\ConditionalFormat\Formatter\DateFormatter\FormatFormatter('Y-m-d H:i:s')
        );
    }

}