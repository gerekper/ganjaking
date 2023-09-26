<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Comment;

use ACP\Search\Query\Bindings;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\FormatValue;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Sorter;
use ACP\Sorting\Type\Order;

/**
 * Sorts a comment list table on a meta key. The meta value may contain mixed values, as long
 * as the supplied formatter can process them into a string.
 */
class MetaFormat extends AbstractModel implements QueryBindings
{

    private $meta_key;

    private $formatter;

    public function __construct(FormatValue $formatter, string $meta_key)
    {
        parent::__construct();

        $this->meta_key = $meta_key;
        $this->formatter = $formatter;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $bindings->order_by(
            SqlOrderByFactory::create_with_ids(
                "$wpdb->comments.comment_ID",
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
			SELECT cc.comment_ID AS id, cm.meta_value AS value
			FROM $wpdb->comments AS cc
			LEFT JOIN $wpdb->commentmeta AS cm ON cm.comment_id = cc.comment_ID
				AND cm.meta_key = %s AND cm.meta_value <> ''
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
