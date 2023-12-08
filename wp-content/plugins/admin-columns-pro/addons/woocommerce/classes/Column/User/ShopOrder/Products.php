<?php

namespace ACA\WC\Column\User\ShopOrder;

use AC;
use ACA\WC\ConditionalFormat\FilteredHtmlIntegerFormatterTrait;
use ACA\WC\Search;
use ACA\WC\Settings;
use ACA\WC\Sorting\User\ShopOrder\ProductCount;
use ACA\WC\Sorting\User\ShopOrder\ProductCountUnique;
use ACP;
use stdClass;

class Products extends AC\Column
    implements ACP\Sorting\Sortable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable
{

    use FilteredHtmlIntegerFormatterTrait;

    public function __construct()
    {
        $this->set_type('column-wc-user_products')
             ->set_label(__('Products', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function register_settings()
    {
        $this->add_setting(new Settings\User\Products($this));
    }

    public function get_raw_value($id)
    {
        $products = $this->get_products((int)$id);

        if ($this->is_uniquely_purchased()) {
            return count($products);
        }

        $count = 0;

        foreach ($products as $product) {
            $count += $product->qty;
        }

        return $count;
    }

    /**
     * @param int $id User ID
     *
     * @return stdClass[] [ $product_id, $order_id, $qty ]
     */
    private function get_products(int $id): array
    {
        global $wpdb;

        // Unique products
        $sql_parts = [
            'select' => '
				SELECT DISTINCT oim.meta_value AS product_id',
            'from'   => "
				FROM $wpdb->postmeta AS pm",
            'joins'  => [
                "
				INNER JOIN $wpdb->posts AS p 
					ON p.ID = pm.post_id 
					AND p.post_status = 'wc-completed'",
                "
				INNER JOIN {$wpdb->prefix}woocommerce_order_items AS oi
					ON oi.order_id = p.ID 
					AND oi.order_item_type = 'line_item'",
                "
				INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim
					ON oi.order_item_id = oim.order_item_id AND oim.meta_key = '_product_id'",
            ],
            'where'  => "WHERE pm.meta_key = '_customer_user'
				AND pm.meta_value = %d",
        ];

        // Total products
        if ( ! $this->is_uniquely_purchased()) {
            $sql_parts['select'] = '
				SELECT oim.meta_value AS product_id, pm.post_id AS order_id, oim2.meta_value as qty';

            $sql_parts['joins'][] = "
				INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim2 
					ON oi.order_item_id = oim2.order_item_id 
					AND oim2.meta_key = '_qty'";
        }

        $sql = $this->built_sql($sql_parts);

        $stmt = $wpdb->prepare($sql, [$id]);
        $results = $wpdb->get_results($stmt);

        if (empty($results)) {
            return [];
        }

        return $results;
    }

    private function built_sql(array $parts): string
    {
        $sql = '';

        foreach ($parts as $part) {
            if (is_array($part)) {
                $sql .= $this->built_sql($part);
            } else {
                $sql .= ' ' . $part;
            }
        }

        return $sql;
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
            return new ProductCountUnique();
        }

        return new ProductCount();
    }

    public function search()
    {
        return new Search\User\ShopOrder\Products();
    }

}