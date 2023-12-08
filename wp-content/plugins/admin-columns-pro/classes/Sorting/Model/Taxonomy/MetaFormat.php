<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Taxonomy;

use ACP\Query\Bindings;
use ACP\Sorting\FormatValue;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Sorter;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\Order;

class MetaFormat implements QueryBindings
{

    private $taxonomy;

    private $formatter;

    private $meta_key;

    protected $data_type;

    public function __construct(string $taxonomy, FormatValue $formatter, string $meta_key, DataType $data_type = null)
    {
        $this->taxonomy = $taxonomy;
        $this->formatter = $formatter;
        $this->meta_key = $meta_key;
        $this->data_type = $data_type ?: new DataType(DataType::STRING);
    }

    public function create_query_bindings(Order $order): Bindings
    {
        $bindings = new Bindings();

        $bindings->group_by("t.term_id");
        $bindings->order_by(
            SqlOrderByFactory::create_with_ids(
                "t.term_id",
                $this->get_sorted_ids(),
                (string)$order
            )
        );

        return $bindings;
    }

    private function get_sorted_ids(): array
    {
        global $wpdb;

        $sql = $wpdb->prepare(
            "
			SELECT terms.term_id AS id, tm.meta_value AS value
			FROM $wpdb->terms AS terms
			LEFT JOIN $wpdb->term_taxonomy AS tt ON tt.term_id = terms.term_id
			    AND tt.taxonomy = %s
			LEFT JOIN $wpdb->termmeta AS tm ON tm.term_id = terms.term_id
				AND tm.meta_key = %s AND tm.meta_value <> ''
		",
            $this->taxonomy,
            $this->meta_key
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
