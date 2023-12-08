<?php

namespace ACP\Sorting\Model\User;

use ACP\Query\Bindings;
use ACP\Sorting\FormatValue;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Sorter;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\Order;

/**
 * Sorts a user list table on a meta key. The meta value may contain mixed values, as long
 * as the supplied formatter can process them into a string.
 */
class MetaFormat implements QueryBindings
{

    private $meta_key;

    private $formatter;

    protected $data_type;

    public function __construct(FormatValue $formatter, string $meta_key, DataType $data_type = null)
    {
        $this->formatter = $formatter;
        $this->meta_key = $meta_key;
        $this->data_type = $data_type ?: new DataType(DataType::STRING);
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        return (new Bindings())->order_by(
            SqlOrderByFactory::create_with_ids(
                "$wpdb->users.ID",
                $this->get_sorted_ids(),
                (string)$order
            )
        );
    }

    private function get_sorted_ids(): array
    {
        global $wpdb;

        $sql = $wpdb->prepare(
            "
			SELECT uu.ID AS id, um.meta_value AS value
			FROM $wpdb->users AS uu
			LEFT JOIN $wpdb->usermeta AS um ON uu.ID = um.user_id
				AND um.meta_key = %s AND um.meta_value <> ''
		",
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
