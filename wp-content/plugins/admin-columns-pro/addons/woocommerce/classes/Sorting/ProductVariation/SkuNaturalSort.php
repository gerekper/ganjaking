<?php

namespace ACA\WC\Sorting\ProductVariation;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Post\PostRequestTrait;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class SkuNaturalSort implements QueryBindings
{

    use PostRequestTrait;

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        return (new Bindings())->order_by(
            SqlOrderByFactory::create_with_ids(
                "$wpdb->posts.ID",
                $this->get_sorted_ids(),
                $order
            )
        );
    }

    private function get_sorted_ids(): array
    {
        global $wpdb;

        $sql = "
			SELECT pp.ID AS id, COALESCE( NULLIF( acsort_postmeta.meta_value, '' ), acsort_parentmeta.meta_value ) AS sku
			FROM $wpdb->posts AS pp
			INNER JOIN $wpdb->posts AS acsort_parent ON acsort_parent.ID = pp.post_parent
				AND acsort_parent.post_type = 'product'
			LEFT JOIN $wpdb->postmeta AS acsort_postmeta ON acsort_postmeta.post_id = pp.ID 
				AND acsort_postmeta.meta_key = '_sku'
			LEFT JOIN $wpdb->postmeta AS acsort_parentmeta ON acsort_parentmeta.post_id = acsort_parent.ID 
				AND acsort_parentmeta.meta_key = '_sku'
			WHERE pp.post_type = 'product_variation'
		";

        $status = $this->get_var_post_status();

        if ($status) {
            $sql .= $wpdb->prepare("\nAND pp.post_status = %s", $status);
        }

        $results = $wpdb->get_results($sql);

        if (empty($results)) {
            return [];
        }

        $ids = [];

        foreach ($results as $object) {
            if ($object->sku) {
                $ids[$object->id] = $object->sku;
            }
        }

        natcasesort($ids);

        return array_keys($ids);
    }

}