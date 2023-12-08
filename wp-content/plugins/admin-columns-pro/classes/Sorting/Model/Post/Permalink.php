<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Model\WarningAware;
use ACP\Sorting\Type\Order;

class Permalink implements WarningAware, QueryBindings
{

    use PostRequestTrait;

    private $post_type;

    public function __construct(string $post_type)
    {
        $this->post_type = $post_type;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        return (new Bindings())->order_by(
            SqlOrderByFactory::create_with_ids(
                "$wpdb->posts.ID",
                $this->get_sorted_ids(),
                (string)$order
            )
        );
    }

    private function get_sorted_ids(): array
    {
        global $wpdb;

        // only fetch the fields needed for `get_permalink()`
        $sql = $wpdb->prepare(
            "
			SELECT pp.ID, pp.post_type, pp.post_status, pp.post_name, pp.post_date, pp.post_parent
			FROM $wpdb->posts AS pp
			WHERE pp.post_type = %s AND pp.post_name <> ''
		",
            $this->post_type
        );

        $status = $this->get_var_post_status();

        if ($status) {
            $sql .= $wpdb->prepare("\nAND pp.post_status = %s", $status);
        }

        $results = $wpdb->get_results($sql);

        if ( ! $results) {
            return [];
        }

        $values = [];

        foreach ($results as $object) {
            $link = get_permalink(get_post($object));

            if ($link && is_string($link)) {
                $values[$object->ID] = $link;
            }
        }

        natcasesort($values);

        return array_keys($values);
    }

}