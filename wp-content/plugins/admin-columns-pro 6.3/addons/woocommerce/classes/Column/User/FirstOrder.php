<?php

namespace ACA\WC\Column\User;

use AC\Column;
use ACA\WC\Helper;
use ACA\WC\Settings;
use ACA\WC\Sorting;
use ACP\ConditionalFormat\ConditionalFormatTrait;
use ACP\ConditionalFormat\Formattable;
use ACP\Export\Exportable;
use ACP\Export\Model\StrippedValue;
use ACP\Sorting\Sortable;

class FirstOrder extends Column implements Exportable, Formattable, Sortable
{

    use ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-wc-user-first_order')
             ->set_group('woocommerce')
             ->set_label(__('First Order', 'codepress-admin-columns'));
    }

    protected function get_first_order($user_id)
    {
        return (new Helper\User())->get_first_completed_order($user_id);
    }

    public function get_value($user_id)
    {
        $order = $this->get_first_order($user_id);

        if ( ! $order) {
            return $this->get_empty_char();
        }

        return $this->get_setting(Settings\User\Order::NAME)->format($order, $order);
    }

    public function register_settings()
    {
        $this->add_setting(new Settings\User\Order($this));
    }

    public function export()
    {
        return new StrippedValue($this);
    }

    public function sorting()
    {
        return new Sorting\User\OrderExtrema('min');
    }

}