<?php

declare(strict_types=1);

namespace ACA\WC\Column\Order;

use WC_Order;

trait OrderTitle
{

    public function get_order_title(WC_Order $order): string
    {
        $buyer = '';

        if ($order->get_billing_first_name() || $order->get_billing_last_name()) {
            $buyer = trim(
                sprintf(
                    _x('%1$s %2$s', 'full name', 'woocommerce'),
                    $order->get_billing_first_name(),
                    $order->get_billing_last_name()
                )
            );
        } elseif ($order->get_billing_company()) {
            $buyer = trim($order->get_billing_company());
        } elseif ($order->get_customer_id()) {
            $user = get_user_by('id', $order->get_customer_id());
            $buyer = ucwords($user->display_name);
        }

        return sprintf(
            '#%s %s',
            esc_attr($order->get_order_number()),
            esc_html($buyer)
        );
    }

}