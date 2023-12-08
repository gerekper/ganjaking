<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class Taxonomy implements QueryBindings
{

    private $taxonomy;

    public function __construct(string $taxonomy)
    {
        $this->taxonomy = $taxonomy;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();
        $alias = $bindings->get_unique_alias('tax');

        $sub_query = $wpdb->prepare(
            "
            SELECT *
            FROM (
                SELECT DISTINCT acsort_tr.object_id, acsort_t.slug
                FROM $wpdb->term_taxonomy AS acsort_tt
                INNER JOIN $wpdb->term_relationships acsort_tr
                    ON acsort_tt.term_taxonomy_id = acsort_tr.term_taxonomy_id
                INNER JOIN $wpdb->terms AS acsort_t
                    ON acsort_t.term_id = acsort_tt.term_id
                WHERE taxonomy = %s
                ORDER BY acsort_t.slug
            ) AS acsort_main
            GROUP BY acsort_main.object_id
        ",
            $this->taxonomy
        );

        $bindings->join(
            "LEFT JOIN ($sub_query) as $alias ON $wpdb->posts.ID = $alias.object_id"
        );
        $bindings->order_by(
            SqlOrderByFactory::create("$alias.slug", (string)$order)
        );

        return $bindings;
    }

}