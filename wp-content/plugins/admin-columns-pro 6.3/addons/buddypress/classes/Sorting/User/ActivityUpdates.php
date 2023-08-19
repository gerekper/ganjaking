<?php

namespace ACA\BP\Sorting\User;

use ACP\Search\Query\Bindings;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\ComputationType;
use ACP\Sorting\Type\Order;

class ActivityUpdates extends AbstractModel implements QueryBindings
{

    /**
     * @var string
     */
    private $activity_type;

    public function __construct($activity_type)
    {
        parent::__construct();

        $this->activity_type = (string)$activity_type;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb, $bp;

        $bindings = new Bindings();

        $alias = $bindings->get_unique_alias('activity');
        $where = '';
        if ($this->activity_type) {
            $where = $wpdb->prepare("AND $alias.type = %s", $this->activity_type);
        }

        $bindings->join(
            "
			LEFT JOIN {$bp->activity->table_name} as $alias 
				ON {$wpdb->users}.ID = $alias.user_id {$where}
		"
        );

        $bindings->group_by("$wpdb->users.ID");
        $bindings->order_by(
            SqlOrderByFactory::create_with_computation(
                new ComputationType(ComputationType::COUNT),
                "$alias.user_id",
                (string)$order
            )
        );

        return $bindings;
    }

}