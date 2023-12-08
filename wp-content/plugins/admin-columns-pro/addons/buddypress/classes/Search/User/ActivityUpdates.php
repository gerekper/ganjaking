<?php

namespace ACA\BP\Search\User;

use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class ActivityUpdates extends Comparison
{

    /** @var string */
    private $activity;

    public function __construct($activity)
    {
        $this->activity = $activity;
        $operators = new Operators([
            Operators::BETWEEN,
            Operators::GT,
            Operators::GTE,
            Operators::LT,
            Operators::LTE,
        ]);

        parent::__construct($operators, Value::INT);
    }

    /**
     * @inheritDoc
     */
    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb, $bp;

        $bindings = new Bindings();
        $q_value = $value->get_value();
        $having = '';
        $where = '';

        switch ($operator) {
            case Operators::GT:
                $having = $wpdb->prepare(' AND activities > %d', $q_value[0]);
                break;
            case Operators::LT:
                $having = $wpdb->prepare(' AND activities < %d', $q_value[0]);
                break;
            case Operators::BETWEEN:
                $having = $wpdb->prepare(' AND activities BETWEEN %d AND %d', $q_value[0], $q_value[1]);
                break;
        }

        if ($this->activity) {
            $where = $wpdb->prepare('WHERE type = %s', $this->activity);
        }

        $sub_query = "
			(SELECT user_id, COUNT(user_id) as activities
			FROM {$bp->activity->table_name}
			{$where}
			GROUP BY user_id
			HAVING activities != '' {$having}
			ORDER BY activities)";

        $bindings->join("INNER JOIN {$sub_query} as ac_AU ON {$wpdb->users}.id = ac_AU.user_id");

        return $bindings;
    }
}