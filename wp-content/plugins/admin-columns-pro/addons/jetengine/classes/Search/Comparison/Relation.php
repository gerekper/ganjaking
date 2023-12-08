<?php

namespace ACA\JetEngine\Search\Comparison;

use ACP;
use ACP\Query\Bindings;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;
use ACP\Search\Value;
use Jet_Engine\Relations\Relation as JetEngineRelation;
use Jet_Engine\Relations\Storage;

abstract class Relation extends ACP\Search\Comparison implements SearchableValues
{

    private $relation;

    private $is_parent;

    public function __construct(JetEngineRelation $relation, bool $is_parent)
    {
        parent::__construct(
            new Operators([
                Operators::EQ,
                Operators::NEQ,
                Operators::IS_EMPTY,
                Operators::NOT_IS_EMPTY,
            ])
        );

        $this->relation = $relation;
        $this->is_parent = $is_parent;
    }

    private function get_db_id_column(): string
    {
        global $wpdb;

        $argument = $this->is_parent ? 'parent_object' : 'child_object';
        $field = explode('::', $this->relation->get_args($argument))[0];

        switch ($field) {
            case 'mix':
                return sprintf('%s.%s', $wpdb->users, 'ID');
            case 'terms':
                return 't.term_id';
            case 'posts':
                return sprintf('%s.%s', $wpdb->posts, 'ID');
        }

        return '';
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        switch ($operator) {
            case Operators::IS_EMPTY:
            case Operators::NOT_IS_EMPTY:
                return $this->create_empty_bindings($operator);
            case Operators::EQ:
            case Operators::NEQ:
            default:
                return $this->create_specific_bindings($operator, $value);
        }
    }

    private function create_empty_bindings($operator)
    {
        global $wpdb;

        $field = $this->is_parent ? 'parent_object_id' : 'child_object_id';
        $bindings = new ACP\Query\Bindings();
        $relation_table = $this->relation->db->table();
        $table_alias = $bindings->get_unique_alias('rel');
        $id = $this->get_db_id_column();
        $join_type = $operator === Operators::NOT_IS_EMPTY ? 'INNER' : 'LEFT';

        $bindings->join(
            $wpdb->prepare(
                "{$join_type} JOIN {$relation_table} AS $table_alias ON {$id} = $table_alias.$field AND $table_alias.rel_id = %d",
                $this->relation->get_id()
            )
        );

        if (Operators::IS_EMPTY === $operator) {
            $bindings->where("$table_alias.rel_id IS NULL");
        }

        $bindings->group_by($id);

        return $bindings;
    }

    private function create_specific_bindings(string $operator, Value $value): Bindings
    {
        $ids = array_unique($this->get_related_ids($value));
        $in_type = $operator === Operators::EQ ? 'IN' : 'NOT IN';

        $ids = implode("','", array_map('esc_sql', $ids));
        $column = $this->get_db_id_column();

        $bindings = new Bindings();
        $bindings->where("{$column} {$in_type}( '{$ids}' )");

        return $bindings;
    }

    protected function get_related_ids(Value $value): array
    {
        $query_arg = $this->is_parent ? 'child_object_id' : 'parent_object_id';
        $field = $this->is_parent ? 'parent_object_id' : 'child_object_id';

        /** @var Storage\DB $db */
        $db = $this->relation->db;

        $results = $db->query([
            'rel_id'   => $this->relation->get_id(),
            $query_arg => $value->get_value(),
        ]);

        return wp_list_pluck($results, $field);
    }

}