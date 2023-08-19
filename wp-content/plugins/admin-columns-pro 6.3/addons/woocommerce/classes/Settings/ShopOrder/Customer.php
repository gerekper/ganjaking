<?php

namespace ACA\WC\Settings\ShopOrder;

use AC;
use ACA\WC\Helper;
use ACP;

class Customer extends ACP\Settings\Column\User
{

    protected function get_display_options()
    {
        $options = parent::get_display_options();

        $_options = [
            'billing_address'  => __('Billing Address', 'woocommerce'),
            'billing_company'  => __('Billing Company', 'codepress-admin-columns'),
            'billing_country'  => __('Billing Country', 'codepress-admin-columns'),
            'billing_email'    => __('Billing Email', 'codepress-admin-columns'),
            'customer_since'   => __('Customer Since', 'codepress-admin-columns'),
            'order_count'      => __('Order Count', 'codepress-admin-columns'),
            'shipping_address' => __('Shipping Address', 'woocommerce'),
            'total_sales'      => __('Total Sales', 'codepress-admin-columns'),
        ];

        natcasesort($_options);

        $options[] = [
            'title'   => __('WooCommerce', 'codepress-admin-columns'),
            'options' => $_options,
        ];

        return $options;
    }

    public function get_dependent_settings()
    {
        switch ($this->get_display_author_as()) {
            case 'customer_since' :
                return [new AC\Settings\Column\Date($this->column)];

            default :
                $dependent_settings = parent::get_dependent_settings();
                // Overwrite the UserLink setting
                $dependent_settings[] = new CustomerLink($this->column);

                return $dependent_settings;
        }
    }

    /**
     * @param string $country_code
     *
     * @return string|null
     */
    private function get_country($country_code)
    {
        $countries = WC()->countries->get_countries();

        if ( ! isset($countries[$country_code])) {
            return null;
        }

        return $countries[$country_code];
    }

    /**
     * @param int $user_id
     * @param int $order_id
     *
     * @return string|false
     */
    public function format($user_id, $order_id)
    {
        switch ($this->get_display_author_as()) {
            case 'billing_company' :
                return wc_get_order($order_id)->get_billing_company();

            case 'billing_country' :
                return $this->get_country((string)wc_get_order($order_id)->get_billing_country());

            case 'billing_email' :
                return wc_get_order($order_id)->get_billing_email();

            case 'billing_address' :
                return wc_get_order($order_id)->get_formatted_billing_address();

            case 'customer_since' :
                $fto = $this->get_first_order_for_user($user_id);

                if ( ! $fto) {
                    return false;
                }

                return get_post_field('post_date', $fto);

            case 'order_count' :
                $orders = $this->get_order_ids_for_user($user_id);

                if ( ! $orders) {
                    return false;
                }

                return ac_helper()->html->link(add_query_arg('_customer_user', $user_id), count($orders));

            case 'shipping_address' :

                return wc_get_order($order_id)->get_formatted_shipping_address();
            case 'total_sales' :

                return $this->get_total_spent_for_user($user_id);
            default :

                return parent::format($user_id, $order_id);
        }
    }

    /**
     * @param int $user_id
     *
     * @return int[]|false
     */
    private function get_order_ids_for_user($user_id)
    {
        if ( ! $user_id) {
            return false;
        }

        $args = [
            'post_type'      => wc_get_order_types(),
            'post_status'    => array_keys(wc_get_order_statuses()),
            'meta_query'     => [
                [
                    'key'   => '_customer_user',
                    'value' => $user_id,
                ],
            ],
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'ASC',
            'fields'         => 'ids',
        ];

        return get_posts($args);
    }

    /**
     * @param int $user_id
     *
     * @return int|false
     */
    private function get_first_order_for_user($user_id)
    {
        $orders = $this->get_order_ids_for_user($user_id);

        if (empty($orders)) {
            return false;
        }

        return current($orders);
    }

    /**
     * @param $user_id
     *
     * @return false|string
     */
    private function get_total_spent_for_user($user_id)
    {
        $values = [];

        foreach ((new Helper\User)->get_shop_order_totals_for_user($user_id) as $currency => $total) {
            if ($total) {
                $values[] = wc_price($total);
            }
        }

        if ( ! $values) {
            return false;
        }

        return implode(' | ', $values);
    }

}