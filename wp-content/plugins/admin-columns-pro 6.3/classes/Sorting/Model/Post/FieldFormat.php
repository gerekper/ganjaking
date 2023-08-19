<?php

namespace ACP\Sorting\Model\Post;

use ACP\Search\Query\Bindings;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\FormatValue;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class FieldFormat extends AbstractModel implements QueryBindings
{

    use PostResultsTrait;

    public function __construct(string $db_column, FormatValue $formatter)
    {
        parent::__construct();

        $this->db_columns = [$db_column];
        $this->formatter = $formatter;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        return (new Bindings())->order_by(
            SqlOrderByFactory::create_with_ids(
                "$wpdb->posts.ID",
                $this->get_post_ids(),
                (string)$order
            )
        );
    }

}