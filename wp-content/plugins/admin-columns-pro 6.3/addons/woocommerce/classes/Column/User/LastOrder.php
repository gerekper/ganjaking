<?php

namespace ACA\WC\Column\User;

use AC\Column;
use ACA\WC\Helper;
use ACA\WC\Settings;
use ACA\WC\Settings\OrderStatuses;
use ACA\WC\Sorting;
use ACP\Export\Exportable;
use ACP\Export\Model\StrippedValue;
use ACP\Sorting\Sortable;

class LastOrder extends Column implements Exportable, Sortable
{

    public function __construct()
    {
        $this->set_type('column-wc-user-last_order')
             ->set_group('woocommerce')
             ->set_label(__('Last Order', 'codepress-admin-columns'));
    }

    protected function get_last_order($user_id)
    {
        return (new Helper\User())->get_last_order($user_id, $this->get_order_statuses());
    }

    private function get_order_statuses(): array
    {
        $setting = $this->get_setting(OrderStatuses::NAME);

        return $setting instanceof OrderStatuses
            ? $setting->get_order_status()
            : [];
    }

    public function get_value($user_id)
    {
        $order = $this->get_last_order($user_id);

        if ( ! $order) {
            return $this->get_empty_char();
        }

        return $this->get_setting(Settings\User\Order::NAME)->format($order, $order);
    }

    public function register_settings()
    {
        $this->add_setting(new Settings\OrderStatuses($this));
        $this->add_setting(new Settings\User\Order($this));
    }

    public function export()
    {
        return new StrippedValue($this);
    }

    public function sorting()
    {
        return new Sorting\User\OrderExtrema('max', $this->get_order_statuses());
    }

}