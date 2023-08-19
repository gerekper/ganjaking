<?php

namespace ACA\WC\Settings\ShopOrder;

use AC;

class CustomerLink extends AC\Settings\Column\UserLink
{

    public function format($value, $order_id)
    {
        $user_id = get_post_meta($order_id, '_customer_user', true);

        return parent::format($value, $user_id);
    }
}