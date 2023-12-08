<?php

namespace ACP\Sorting\Model\Post;

use ACP\Query\Bindings;
use ACP\Sorting\FormatValue\ShortCodeCount;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Model\WarningAware;
use ACP\Sorting\Type\Order;

class Shortcodes implements WarningAware, QueryBindings
{

    use PostResultsTrait;

    public function __construct()
    {
        $this->formatter = new ShortCodeCount();
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

    public function get_post_ids(): array
    {
        $ids = [];

        foreach ($this->get_query_results() as $row) {
            $ids[$row->id] = $this->formatter->format_value($row->value);
        }

        $ids = array_filter($ids);

        asort($ids);

        return array_keys($ids);
    }

    private function get_query_results(): array
    {
        global $wpdb;

        $where = '';

        $status = $this->get_var_post_status();

        if ($status) {
            $where = $wpdb->prepare("\nAND $wpdb->posts.post_status = %s", $status);
        }

        $sql = $wpdb->prepare(
            "
            SELECT $wpdb->posts.ID AS id, $wpdb->posts.post_content AS value
            FROM $wpdb->posts
            WHERE $wpdb->posts.post_type = %s
                AND $wpdb->posts.post_content LIKE '%[%' 
                AND $wpdb->posts.post_content LIKE '%]%'
                $where
            ",
            $this->get_var_post_type()
        );

        return $wpdb->get_results($sql);
    }

}