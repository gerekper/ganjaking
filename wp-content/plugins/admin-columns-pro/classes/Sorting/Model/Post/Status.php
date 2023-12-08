<?php

namespace ACP\Sorting\Model\Post;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Type\Order;

class Status implements QueryBindings
{

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $fields = implode("','", array_map('esc_sql', $this->get_stati()));

        return $bindings->order_by(
            sprintf("FIELD( $wpdb->posts.post_status, '%s' ) %s", $fields, $order)
        );
    }

    private function get_stati(): array
    {
        $translated_stati = [];

        foreach (get_post_stati(null, 'objects') as $key => $post_status) {
            $translated_stati[$key] = $post_status->label;
        }

        natcasesort($translated_stati);

        return array_keys($translated_stati);
    }

}