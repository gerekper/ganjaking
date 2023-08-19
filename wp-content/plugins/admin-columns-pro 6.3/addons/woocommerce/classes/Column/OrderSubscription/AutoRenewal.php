<?php

namespace ACA\WC\Column\OrderSubscription;

use AC;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;

class AutoRenewal extends AC\Column implements ACP\Search\Searchable, ACP\Sorting\Sortable
{

    public function __construct()
    {
        $this->set_type('column_wc_auto_renewal')
             ->set_group('woocommerce_subscriptions')
             ->set_label(__('Auto Renewal', 'codepress-admin-columns'));
    }

    public function get_value($id)
    {
        return ac_helper()->icon->yes_or_no(! $this->get_raw_value($id));
    }

    public function get_raw_value($id)
    {
        return wcs_get_subscription($id)->is_manual();
    }

    public function search()
    {
        return new Search\OrderSubscription\AutoRenewal();
    }

    public function sorting()
    {
        return new Sorting\Order\OrderMeta('_requires_manual_renewal');
    }

}