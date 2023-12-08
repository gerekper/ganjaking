<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Search;
use ACP;

class Coupons extends AC\Column
    implements ACP\Export\Exportable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

    public function __construct()
    {
        $this->set_type('column-wc-product_coupons')
             ->set_label(__('Coupons', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $coupon_ids = $this->get_raw_value($id);

        if (empty($coupon_ids)) {
            return $this->get_empty_char();
        }

        $values = [];

        foreach ($coupon_ids as $coupon_id) {
            $values[] = ac_helper()->html->link(get_edit_post_link($coupon_id), get_the_title($coupon_id));
        }

        return implode($this->get_separator(), $values);
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function get_raw_value($id)
    {
        global $wpdb;

        $sql = "SELECT p.ID 
				FROM $wpdb->posts as p
				INNER JOIN $wpdb->postmeta as pm ON p.ID = pm.post_id
				WHERE post_type = 'shop_coupon'
				AND meta_key = 'product_ids'
				AND FIND_IN_SET( %d, pm.meta_value )";

        $query = $wpdb->prepare($sql, [$id]);

        return $wpdb->get_col($query);
    }

    public function search()
    {
        return new Search\Product\Coupons();
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedValue($this);
    }

}