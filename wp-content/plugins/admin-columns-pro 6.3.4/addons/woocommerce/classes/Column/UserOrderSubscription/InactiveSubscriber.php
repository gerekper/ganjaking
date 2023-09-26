<?php

namespace ACA\WC\Column\UserOrderSubscription;

use AC;
use ACA\WC\Search;
use ACP\Export\Exportable;
use ACP\Export\Model\StrippedValue;
use ACP\Search\Searchable;
use DateTime;
use WC_Subscription;

class InactiveSubscriber extends AC\Column
    implements Searchable, Exportable
{

    public function __construct()
    {
        $this->set_type('column-user_subscription_inactive')
             ->set_label('Inactive subscriber')
             ->set_group('woocommerce_subscriptions');
    }

    public function get_value($id)
    {
        $values = [];

        foreach ($this->get_raw_value($id) as $subscription) {
            $status = $subscription->get_status();

            $values[] = sprintf(
                '<div class="subscription subscription-%s" %s>%s <small>%s</small></div>'
                ,
                esc_attr($status)
                ,
                ac_helper()->html->get_tooltip_attr($this->get_order_tooltip($subscription))
                ,
                ac_helper()->html->link(
                    get_edit_post_link($subscription->get_id()),
                    wcs_get_subscription_status_name($subscription->get_status())
                )
                ,
                esc_attr($this->get_subscription_description($subscription))
            );
        }

        return implode($values) ?: $this->get_empty_char();
    }

    private function format_date(DateTime $date): string
    {
        return ac_format_date(wc_date_format(), $date->getTimestamp());
    }

    private function get_subscription_description(WC_Subscription $subscription): ?string
    {
        $date = $this->get_inactive_subscription_date($subscription);

        return $date
            ? sprintf(__('since %s', 'codepress-admin-columns'), $this->format_date($date))
            : null;
    }

    private function get_inactive_subscription_date(WC_Subscription $subscription): ?DateTime
    {
        switch ($subscription->get_status()) {
            case 'on-hold' :
            case 'refunded' :
            case 'cancelled' :
            case 'expired':
                $date = DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    (string)$subscription->get_date('date_created')
                );

                return $date ?: null;
            case 'active':
            case 'switched':
            case 'pending-cancel':
            default :
                return null;
        }
    }

    private function get_order_tooltip(WC_Subscription $subscription): string
    {
        $date = $this->get_inactive_subscription_date($subscription);

        $status = wcs_get_subscription_status_name($subscription->get_status());

        return $date
            ? sprintf(__('%s since %s', 'codepress-admin-columns'), $status, $date->format('Y-m-d H:i:s'))
            : $status;
    }

    public function get_raw_value($user_id)
    {
        if (wcs_user_has_subscription($user_id, '', 'active')) {
            return [];
        }

        return wcs_get_users_subscriptions($user_id);
    }

    public function search()
    {
        return new Search\UserOrderSubscription\InactiveSubscriber();
    }

    public function export()
    {
        return new StrippedValue($this);
    }

}