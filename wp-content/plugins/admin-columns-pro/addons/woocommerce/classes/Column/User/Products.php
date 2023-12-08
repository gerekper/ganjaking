<?php

declare(strict_types=1);

namespace ACA\WC\Column\User;

use AC;
use ACA\WC\ConditionalFormat\FilteredHtmlIntegerFormatterTrait;
use ACA\WC\Helper;
use ACA\WC\Search;
use ACA\WC\Settings;
use ACA\WC\Sorting;
use ACP;

class Products extends AC\Column
    implements ACP\Sorting\Sortable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable, AC\Column\AjaxValue
{

    use FilteredHtmlIntegerFormatterTrait;

    public function __construct()
    {
        $this->set_type('column-wc-user_products')
             ->set_label(__('Products', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $count = $this->is_uniquely_purchased()
            ? (new Helper\User())->get_uniquely_sold_product_count($id)
            : (new Helper\User())->get_sold_product_count($id);

        return $count
            ? ac_helper()->html->get_ajax_modal_link((string)$count, [
                'title' => sprintf(
                    '%s %s',
                    __('Products by', 'codepress-admin-columns'),
                    ac_helper()->user->get_display_name($id)
                ),
                'class' => "-nopadding",
            ])
            : $this->get_empty_char();
    }

    public function register_settings()
    {
        $this->add_setting(new Settings\User\Products($this));
    }

    private function is_uniquely_purchased(): bool
    {
        $setting = $this->get_setting(Settings\User\Products::NAME);

        if ( ! $setting instanceof Settings\User\Products) {
            return false;
        }

        return 'unique' === $setting->get_user_products();
    }

    public function sorting()
    {
        if ($this->is_uniquely_purchased()) {
            return new Sorting\User\ProductsUnique();
        }

        return new Sorting\User\Products();
    }

    public function search()
    {
        return new Search\User\Products();
    }

    /**
     * @param int $user_id
     *
     * @return object[]
     */
    private function get_ordered_items(int $user_id): array
    {
        global $wpdb;

        $statuses = array_map('esc_sql', wc_get_is_paid_statuses());
        $statuses_sql = "( 'wc-" . implode("','wc-", $statuses) . "' )";

        $sql = $wpdb->prepare(
            "
            SELECT CONCAT( wcopl.product_id, '#', wcopl.variation_id ) as pid, SUM( wcopl.product_qty ) as qty
            FROM {$wpdb->prefix}wc_orders AS wco
            LEFT JOIN {$wpdb->prefix}wc_order_product_lookup AS wcopl ON wcopl.order_id = wco.id
            WHERE wco.customer_id = %d
                AND wco.status IN $statuses_sql
            GROUP BY pid
        ",
            $user_id
        );

        return $wpdb->get_results($sql);
    }

    public function get_ajax_value($user_id)
    {
        $products = $this->get_ordered_items((int)$user_id);

        if (count($products) < 1) {
            return false;
        }

        $items = [];

        foreach ($products as $row) {
            $ids = explode('#', $row->pid);
            $variation_id = $ids[1] !== '0' ? $ids[1] : $ids[0];

            $items[] = [
                'title' => get_the_title($variation_id)
                    ?: __(
                           'Product removed',
                           'codepress-admin-columns'
                       ) . ' #' . $variation_id,
                'link'  => get_edit_post_link($variation_id),
                'total' => $row->qty,
            ];
        }

        $view = new AC\View([
            'items' => $items,
        ]);

        echo $view->set_template('modal-value/products-by-user')->render();
        exit;
    }

}