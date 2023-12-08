<?php

namespace ACA\WC\Column\OrderSubscription;

use AC;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;

class BillingInterval extends AC\Column implements ACP\Search\Searchable, ACP\Sorting\Sortable
{

    public function __construct()
    {
        $this->set_type('column_wc_billing_interval')
             ->set_group('woocommerce_subscriptions')
             ->set_label(__('Billing Interval', 'codepress-admin-columns'));
    }

    public function get_value($id)
    {
        $intervals = wcs_get_subscription_period_interval_strings();
        $interval = wcs_get_subscription($id)->get_billing_interval();

        return array_key_exists($interval, $intervals)
            ? $intervals[$interval]
            : $interval;
    }

    public function get_raw_value($id)
    {
        return wcs_get_subscription($id)->get_billing_interval();
    }

    public function search()
    {
        return new ACP\Search\Comparison\Meta\Select(
            '_billing_interval',
            wcs_get_subscription_period_interval_strings()
        );
    }

    public function sorting()
    {
        return new Sorting\Order\OrderMeta(
            '_billing_interval',
            new ACP\Sorting\Type\DataType(ACP\Sorting\Type\DataType::NUMERIC)
        );
    }

}