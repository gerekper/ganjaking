<?php

namespace ACP\Export\Strategy;

use AC;
use AC\ListTable;
use ACP\Export\Strategy;
use WP_User_Query;

class User extends Strategy
{

    protected function get_list_table(): ?ListTable
    {
        return (new AC\ListTableFactory())->create_from_globals();
    }

    protected function ajax_export(): void
    {
        add_filter('users_list_table_query_args', [$this, 'catch_users_query'], PHP_INT_MAX - 100);
    }

    /**
     * Modify the users query to use the correct pagination arguments, and export the resulting
     * items. This should be attached to the users_list_table_query_args hook when an AJAX request
     * is sent
     *
     * @param $args
     *
     * @see   filter:users_list_table_query_args
     * @since 1.0
     */
    public function catch_users_query($args): void
    {
        $per_page = $this->get_num_items_per_iteration();

        $args['offset'] = $this->get_export_counter() * $per_page;
        $args['number'] = $per_page;
        $args['fields'] = 'ids';

        $ids = $this->get_requested_ids();

        if ($ids) {
            $args['include'] = isset($args['include']) && is_array($args['include'])
                ? array_merge($ids, $args['include'])
                : $ids;
        }

        $query = new WP_User_Query($args);

        $this->export($query->get_results());
    }

}