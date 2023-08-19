<?php

namespace ACA\WC\Column\Order;

use AC;
use ACP;
use WC_Coupon;

class CouponsUsed extends AC\Column implements ACP\Export\Exportable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

    public function __construct()
    {
        $this->set_type('column-order_coupons_used')
             ->set_label(__('Coupons Used', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $order = wc_get_order($id);

        $coupons = $order->get_coupon_codes();

        if (empty($coupons)) {
            return $this->get_empty_char();
        }

        $used_coupons = [];

        foreach ($coupons as $code) {
            $coupon = new WC_Coupon($code);
            $used_coupons[] = ac_helper()->html->link(get_edit_post_link($coupon->get_id()), $code);
        }

        return implode(' | ', $used_coupons);
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedValue($this);
    }

}