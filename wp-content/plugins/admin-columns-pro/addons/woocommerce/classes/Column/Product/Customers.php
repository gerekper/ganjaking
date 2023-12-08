<?php

namespace ACA\WC\Column\Product;

use AC;
use AC\View;
use ACA\WC\Sorting;
use ACA\WC\Type\OrderTableUrl;
use ACP;
use Automattic\WooCommerce\Utilities\OrderUtil;
use DateTime;

class Customers extends AC\Column
    implements ACP\Sorting\Sortable, ACP\ConditionalFormat\Formattable, AC\Column\AjaxValue
{

    use ACP\ConditionalFormat\IntegerFormattableTrait;

    public function __construct()
    {
        $this->set_type('column-wc-product_customers')
             ->set_label(__('Customers', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $count = $this->count_unique_customers_by_product((int)$id);

        if ($count < 1) {
            return $this->get_empty_char();
        }

        $count = sprintf(_n('%d customer', '%d customers', $count, 'codepress-admin-columns'), $count);

        return ac_helper()->html->get_ajax_modal_link(
            $count,
            [
                'title'     => get_the_title($id),
                'edit_link' => get_edit_post_link($id),
                'class'     => "-nopadding -w-large",
            ]
        );
    }

    public function get_raw_value($id)
    {
        return $this->count_unique_customers_by_product((int)$id);
    }

    private function count_unique_customers_by_product(int $id): int
    {
        return count($this->get_customers_by_product($id));
    }

    private function get_customers_by_product(int $id, int $limit = null): array
    {
        global $wpdb;

        $sql = $wpdb->prepare(
            "
            SELECT o.customer_id, o.id, o.date_created_gmt
            FROM {$wpdb->prefix}wc_orders as o 
            INNER JOIN {$wpdb->prefix}wc_order_product_lookup opl
                ON o.id = opl.order_id AND opl.product_id = %d
            WHERE
                o.type = 'shop_order'
                AND o.status = %s
            ORDER BY o.date_created_gmt DESC
        ",
            $id,
            'wc-completed'
        );

        if ($limit) {
            $sql .= "LIMIT $limit";
        }

        $customers = [];

        foreach ($wpdb->get_results($sql) as $row) {
            if (isset($customers[$row->customer_id][$row->id])) {
                continue;
            }
            $customers[$row->customer_id][$row->id] = [
                'date' => DateTime::createFromFormat('Y-m-d H:i:s', $row->date_created_gmt),
                'id'   => $row->id,
            ];
        }

        return $customers;
    }

    private function get_user_items_by_product(int $id): array
    {
        $users = [];

        foreach ($this->get_customers_by_product($id, 50) as $customer_id => $orders) {
            $user = get_userdata($customer_id);

            if ( ! $user || ! $user->ID) {
                continue;
            }

            $name = ac_helper()->user->get_display_name($user, 'full_name') ?: $user->display_name;

            $edit_user = get_edit_user_link($user->ID);

            if ($edit_user) {
                $name = sprintf(
                    '<a href="%s">%s</a>',
                    $edit_user,
                    $name
                );
            }

            $count = count($orders);
            $count = sprintf(_n('%d order', '%d orders', $count, 'codepress-admin-columns'), $count);
            $count = sprintf(
                '<a href="%s">%s</a>',
                (new OrderTableUrl())->with_arg('_customer_user', $customer_id),
                $count
            );

            $recent_order = reset($orders);
            $date = $recent_order['date'];
            $date = sprintf(
                '<a href="%s">%s</a>',
                OrderUtil::get_order_admin_edit_url($recent_order['id']),
                date_i18n(get_option('date_format'), $date->getTimeStamp(), true)
            );

            $users[] = [
                'id'           => sprintf('#%s', $user->ID),
                'name'         => $name,
                'orders'       => $count,
                'recent_order' => $date,
            ];
        }

        return $users;
    }

    public function get_ajax_value($id)
    {
        $users = $this->get_user_items_by_product((int)$id);

        if ( ! $users) {
            return false;
        }

        $view = new View([
            'items' => $users,
        ]);

        return $view->set_template('modal-value/customers')->render();
    }

    public function sorting()
    {
        return new Sorting\Product\Order\Customers();
    }

}