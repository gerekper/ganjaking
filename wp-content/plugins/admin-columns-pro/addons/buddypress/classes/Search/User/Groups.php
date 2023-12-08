<?php

namespace ACA\BP\Search\User;

use AC\Helper\Select\Options\Paginated;
use ACA\BP\Helper\Select;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;
use BP_Groups_Group;

class Groups extends Comparison
    implements Comparison\SearchableValues
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        parent::__construct($operators);
    }

    /**
     * @inheritDoc
     */
    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb, $bp;

        $bindings = new Bindings();
        $join_table = $bindings->get_unique_alias('bptm');

        switch ($operator) {
            case Operators::EQ:
                $bindings->join(
                    $wpdb->prepare(
                        "
					INNER JOIN {$bp->groups->table_name_members} AS {$join_table} ON {$wpdb->users}.ID = {$join_table}.user_id 
					AND {$join_table}.group_id = %d AND {$join_table}.is_banned = 0
					",
                        (int)$value->get_value()
                    )
                );

                break;
            case Operators::IS_EMPTY:
                $bindings->where(
                    "NOT EXISTS( SELECT user_id FROM {$bp->groups->table_name_members} WHERE user_id = {$wpdb->users}.ID )"
                );

                break;
            case Operators::NOT_IS_EMPTY:
                $bindings->join(
                    "INNER JOIN {$bp->groups->table_name_members} AS {$join_table} ON {$wpdb->users}.ID = {$join_table}.user_id AND is_confirmed = 1 AND is_banned = 0"
                );

                break;
        }

        return $bindings;
    }

    public function format_label($value): string
    {
        $group = bp_get_group_by('id', $value);

        return $group instanceof BP_Groups_Group ? $group->name : $value;
    }

    public function get_values(string $search, int $page): Paginated
    {
        $groups = new Select\Groups\Query([
            'search_terms' => $search,
            'page'         => $page,
        ]);

        $options = new Select\Groups\Options($groups->get_copy());

        return new Paginated($groups, $options);
    }
}