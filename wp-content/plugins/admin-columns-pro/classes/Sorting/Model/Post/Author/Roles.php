<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post\Author;

use ACP\Query\Bindings;
use ACP\Sorting\FormatValue;
use ACP\Sorting\Model\Post\PostRequestTrait;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class Roles implements QueryBindings
{

    use PostRequestTrait;

    private $formatter;

    public function __construct()
    {
        $this->formatter = new FormatValue\Roles();
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

    private function get_post_ids(): array
    {
        global $wpdb;

        $where = '';

        $status = $this->get_var_post_status();

        if ($status) {
            $where = $wpdb->prepare("\nAND pp.post_status = %s", $status);
        }

        $sql = $wpdb->prepare(
            "
            SELECT pp.ID AS id, um.meta_value AS caps
            FROM $wpdb->posts AS pp
            INNER JOIN $wpdb->usermeta AS um ON pp.post_author = um.user_id 
				AND um.meta_key = 'wp_capabilities'
            WHERE pp.post_type = %s
                $where
            ",
            $this->get_var_post_type()
        );

        $ids = [];

        foreach ($wpdb->get_results($sql) as $row) {
            $role = $this->formatter->format_value($row->caps);

            if ( ! $role) {
                continue;
            }

            $ids[$row->id] = (string)$role;
        }

        natcasesort($ids);

        return array_keys($ids);
    }

}