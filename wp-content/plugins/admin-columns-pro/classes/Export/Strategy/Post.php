<?php

namespace ACP\Export\Strategy;

use AC\ListScreenPost;
use AC\ListTable;
use AC\ListTableFactory;
use ACP\Export\Strategy;
use WP_Query;

class Post extends Strategy
{

    public function __construct(ListScreenPost $list_screen)
    {
        parent::__construct($list_screen);
    }

    protected function get_list_table(): ?ListTable
    {
        return (new ListTableFactory())->create_from_globals();
    }

    protected function ajax_export(): void
    {
        add_action('pre_get_posts', [$this, 'modify_posts_query'], 16);
        add_filter('the_posts', [$this, 'catch_posts'], 10, 2);
        add_filter('posts_clauses', [$this, 'filter_ids'], 20, 2);
    }

    public function filter_ids($clauses, WP_Query $query)
    {
        global $wpdb;

        if ( ! $query->is_main_query()) {
            return $clauses;
        }

        $ids = $this->get_requested_ids();

        if ($ids) {
            $clauses['where'] .= sprintf("\nAND $wpdb->posts.ID IN( %s )", implode(',', $ids));
        }

        return $clauses;
    }

    /**
     * Modify the main posts query to use the correct pagination arguments. This should be attached
     * to the pre_get_posts hook when an AJAX request is sent
     * @see   action:pre_get_posts
     */
    public function modify_posts_query(WP_Query $query): void
    {
        if ($query->is_main_query()) {
            $per_page = $this->get_num_items_per_iteration();

            $query->set('nopaging', false);
            $query->set('offset', $this->get_export_counter() * $per_page);
            $query->set('posts_per_page', $per_page);
            $query->set('posts_per_archive_page', $per_page);
            $query->set('fields', 'all');
        }
    }

    /**
     * Run the actual export when the posts query is finalized. This should be attached to the
     * the_posts filter when an AJAX request is run
     *
     * @param array    $posts
     * @param WP_Query $query
     *
     * @return array
     */
    public function catch_posts($posts, WP_Query $query)
    {
        if ($query->is_main_query()) {
            $this->export(wp_list_pluck($posts, 'ID'));
        }

        return $posts;
    }

}