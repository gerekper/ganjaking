<?php

namespace ACP\Editing\RequestHandler\Query;

use AC\Request;
use ACP\Editing\ApplyFilter\RowsPerIteration;
use ACP\Editing\RequestHandler;
use ACP\Editing\Response;
use WP_Post;
use WP_Query;

final class Post implements RequestHandler
{

    /**
     * @var Request
     */
    private $request;

    public function handle(Request $request)
    {
        $this->request = $request;

        $this->register();
    }

    private function register()
    {
        add_action('pre_get_posts', [$this, 'set_query_vars'], PHP_INT_MAX - 100);
        add_filter('the_posts', [$this, 'send'], 10, 2);
    }

    /**
     * @return int
     */
    private function get_rows_per_iteration()
    {
        return (new RowsPerIteration($this->request))->apply_filters(2000);
    }

    /**
     * @return int
     */
    protected function get_offset()
    {
        $page = (int)$this->request->filter('ac_page', 1, FILTER_SANITIZE_NUMBER_INT);

        return ($page - 1) * $this->get_rows_per_iteration();
    }

    /**
     * @param WP_Post[] $posts
     * @param WP_Query  $query
     *
     * @return void|WP_Post[]
     */
    public function send($posts, WP_Query $query)
    {
        if ( ! $query->is_main_query()) {
            return $posts;
        }

        $post_ids = wp_list_pluck($posts, 'ID');

        $response = new Response\QueryRows($post_ids, $this->get_rows_per_iteration());
        $response->success();
    }

    /**
     * @param WP_Query $query
     */
    public function set_query_vars(WP_Query $query)
    {
        if ( ! $query->is_main_query()) {
            return;
        }

        $query->set('nopaging', false);
        $query->set('offset', $this->get_offset());
        $query->set('posts_per_page', $this->get_rows_per_iteration());
        $query->set('posts_per_archive_page', $this->get_rows_per_iteration());
        $query->set('fields', 'all');
        $query->set('suppress_filters', false);
    }

}