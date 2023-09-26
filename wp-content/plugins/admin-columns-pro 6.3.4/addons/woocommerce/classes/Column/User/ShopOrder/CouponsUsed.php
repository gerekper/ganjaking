<?php

namespace ACA\WC\Column\User\ShopOrder;

use AC;
use ACA\WC\Helper;
use ACP\ConditionalFormat\FilteredHtmlFormatTrait;
use ACP\ConditionalFormat\Formattable;

class CouponsUsed extends AC\Column implements Formattable
{

    use FilteredHtmlFormatTrait;

    public function __construct()
    {
        $this->set_type('column-wc-user_coupons_used')
             ->set_label(__('Coupons Used', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    private function get_orders_by_user($user_id)
    {
        return (new Helper\User())->get_shop_orders_by_user((int)$user_id);
    }

    public function get_value($user_id)
    {
        $coupons = [];

        foreach ($this->get_orders_by_user($user_id) as $order) {
            foreach ($order->get_coupon_codes() as $coupon) {
                $coupons[] = ac_helper()->html->link(
                    get_edit_post_link($order->get_id()),
                    $coupon,
                    ['tooltip' => 'order: #' . $order->get_id()]
                );
            }
        }

        return $coupons ? implode(' | ', $coupons) : $this->get_empty_char();
    }

    /**
     * @param int $user_id
     *
     * @return int Count
     */
    public function get_raw_value($user_id)
    {
        $coupons = [];

        foreach ($this->get_orders_by_user($user_id) as $order) {
            foreach ($order->get_coupon_codes() as $code) {
                $coupons[] = $code;
            }
        }

        return count(array_unique($coupons));
    }

}