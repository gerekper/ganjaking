<?php

namespace ACA\WC\Column\UserOrderSubscription;

use AC;
use ACP\ConditionalFormat\FilteredHtmlFormatTrait;
use ACP\ConditionalFormat\Formattable;
use ACP\Export\Exportable;
use ACP\Export\Model\StrippedValue;

class Subscriptions extends AC\Column
    implements Exportable, Formattable
{

    use FilteredHtmlFormatTrait;

    public function __construct()
    {
        $this->set_type('column-wc-user-subscriptions')
             ->set_label(__('Subscriptions', 'woocommerce'))
             ->set_group('woocommerce_subscriptions');
    }

    public function get_value($user_id)
    {
        $subscriptions = wcs_get_users_subscriptions($user_id);

        if (empty($subscriptions)) {
            return $this->get_empty_char();
        }

        $result = [];

        foreach ($subscriptions as $subscription) {
            $label = ac_helper()->html->tooltip('#' . $subscription->get_id(), $subscription->get_status());
            $result[] = ac_helper()->html->link(get_edit_post_link($subscription->get_id()), $label);
        }

        return implode(', ', $result);
    }

    public function get_raw_value($user_id)
    {
        return wcs_get_users_subscriptions($user_id);
    }

    public function export()
    {
        return new StrippedValue($this);
    }
}