<?php

namespace ACA\WC\Column\OrderSubscription;

use AC;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;

class BillingPeriod extends AC\Column implements ACP\Search\Searchable, ACP\Sorting\Sortable
{

    public function __construct()
    {
        $this->set_type('column_wc_billing_period')
             ->set_group('woocommerce_subscriptions')
             ->set_label(__('Billing Period', 'codepress-admin-columns'));
    }

    public function get_raw_value($id)
    {
        $periods = wcs_get_available_time_periods();
        $period = wcs_get_subscription($id)->get_billing_period();

        return array_key_exists($period, $periods)
            ? $periods[$period]
            : $period;
    }

    public function search()
    {
        return new ACP\Search\Comparison\Meta\Select(
            '_billing_period',
            wcs_get_available_time_periods()
        );
    }

    public function sorting()
    {
        return new Sorting\Order\OrderMeta('_billing_period');
    }

}