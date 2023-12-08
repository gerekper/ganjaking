<?php

namespace ACP\Search\Comparison\Taxonomy;

use AC\Helper\Select\Options;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Comparison\RemoteValues;
use ACP\Search\Operators;
use ACP\Search\Value;

class ParentTerm extends Comparison implements RemoteValues
{

    private $taxonomy;

    public function __construct(string $taxonomy)
    {
        parent::__construct(new Operators([Operators::EQ]), Value::INT);

        $this->taxonomy = $taxonomy;
    }

    public function format_label(string $value): string
    {
        $term = get_term($value, $this->taxonomy);

        return $term->name ?? $value;
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $subquery = $wpdb->prepare(
            "SELECT s.term_id 
			FROM $wpdb->term_taxonomy as s
			WHERE s.parent = %d
		",
            $value->get_value()
        );

        return $bindings->where("t.term_id IN( $subquery )");
    }

    public function get_values(): Options
    {
        global $wpdb;

        $sql = $wpdb->prepare(
            "SELECT DISTINCT(t.name) AS name, tt.parent as parentId
			FROM $wpdb->term_taxonomy as tt
			JOIN $wpdb->term_taxonomy as tt2 ON tt.parent = tt2.term_id
			JOIN $wpdb->terms as t ON tt2.term_id = t.term_id
			WHERE tt.taxonomy = %s
			",
            $this->taxonomy
        );

        $options = [];
        foreach ($wpdb->get_results($sql) as $row) {
            $options[$row->parentId] = $row->name;
        }

        asort($options);

        return Options::create_from_array($options);
    }

}