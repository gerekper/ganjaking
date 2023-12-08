<?php

namespace ACA\BP\Sorting;

use ACA\BP\Column;
use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\CastType;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\Order;

class Profile implements QueryBindings
{

    protected $column;

    protected $data_type;

    public function __construct(Column\Profile $column, DataType $data_type = null)
    {
        $this->column = $column;
        $this->data_type = $data_type ?: new DataType(DataType::STRING);
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb, $bp;

        $bindings = new Bindings();

        $bindings->join(
            $wpdb->prepare(
                "
                LEFT JOIN {$bp->profile->table_name_data} as acsort_pd 
				    ON $wpdb->users.ID = acsort_pd.user_id AND acsort_pd.field_id = %d
		        ",
                $this->column->get_buddypress_field_id()
            )
        );
        $bindings->group_by("$wpdb->users.ID");
        $bindings->order_by(
            SqlOrderByFactory::create(
                "acsort_pd.value",
                (string)$order,
                [
                    'cast_type' => (string)CastType::create_from_data_type($this->data_type),
                ]
            )
        );

        return $bindings;
    }

}