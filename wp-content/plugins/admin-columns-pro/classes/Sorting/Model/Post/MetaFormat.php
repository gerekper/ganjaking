<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

use ACP\Query\Bindings;
use ACP\Sorting\FormatValue;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Sorter;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\Order;

/**
 * Sorts a post list table on a meta key. The meta value may contain mixed values, as long
 * as the supplied formatter can process them into a string.
 */
class MetaFormat implements QueryBindings
{

    protected $meta_key;

    protected $formatter;

    protected $data_type;

    public function __construct(FormatValue $formatter, string $meta_key, DataType $data_type = null)
    {
        $this->formatter = $formatter;
        $this->meta_key = $meta_key;
        $this->data_type = $data_type;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $bindings->order_by(
            SqlOrderByFactory::create_with_ids(
                "$wpdb->posts.ID",
                $this->get_sorted_ids(),
                (string)$order
            )
        );

        return $bindings;
    }

    private function get_sorted_ids(): array
    {
        global $wpdb, $current_screen;

        if ( ! $current_screen->post_type) {
            return [];
        }

        $sql = $wpdb->prepare(
            "
			SELECT pp.ID AS id, pm.meta_value AS value
			FROM $wpdb->posts AS pp
			LEFT JOIN $wpdb->postmeta AS pm ON pm.post_id = pp.ID
				AND pm.meta_key = %s AND pm.meta_value <> ''
			WHERE pp.post_type = %s
		",
            $this->meta_key,
            $current_screen->post_type
        );

        $results = $wpdb->get_results($sql);
        if ( ! $results) {
            return [];
        }

        $values = [];

        foreach ($results as $object) {
            $values[$object->id][] = $this->formatter->format_value($object->value);
        }

        foreach ($values as $id => $meta_values) {
            $values[$id] = trim(implode(' ', $meta_values));
        }

        return (new Sorter())->sort($values, $this->data_type);
    }

}
