<?php

namespace ACP\Helper\Select\Post;

use AC\ApplyFilter\QueryTotalNumber;
use AC\ArrayIterator;
use AC\Helper\Select\Paginated;
use WP_Query;

class Query extends ArrayIterator
    implements Paginated
{

    /**
     * @var WP_Query
     */
    protected $query;

    /**
     * @var array
     */
    protected $search_fields = [];

    public function __construct(array $args = [])
    {
        $args = array_merge([
            'posts_per_page' => (new QueryTotalNumber())->apply_filter(),
            'post_type'      => 'any',
            'orderby'        => 'title',
            'order'          => 'ASC',
            'paged'          => 1,
            's'              => null,
            'post_status'    => 'any',
            'search_fields'  => ['post_title', 'ID'],
        ], $args);

        $this->search_fields = $args['search_fields'];

        add_filter('posts_search', [$this, 'set_search_fields'], 20, 2);

        $this->query = new WP_Query($args);

        parent::__construct($this->query->get_posts());
    }

    public function set_search_fields($search_where, WP_Query $wp_query)
    {
        global $wpdb;

        remove_filter('posts_search', [$this, __FUNCTION__], 20);

        // Empty search
        if ( ! $search_where) {
            return $search_where;
        }

        $search_term = $wp_query->query_vars['s'];

        $like = '%' . $wpdb->esc_like($search_term) . '%';

        $where_parts = [];

        if (in_array('post_title', $this->search_fields, true)) {
            $where_parts[] = $wpdb->prepare("$wpdb->posts.post_title LIKE %s", $like);
        }
        if (in_array('post_content', $this->search_fields, true)) {
            $where_parts[] = $wpdb->prepare("$wpdb->posts.post_content LIKE %s", $like);
        }
        if (in_array('post_excerpt', $this->search_fields, true)) {
            $where_parts[] = $wpdb->prepare("$wpdb->posts.post_excerpt LIKE %s", $like);
        }
        if (in_array('post_name', $this->search_fields, true)) {
            $where_parts[] = $wpdb->prepare("$wpdb->posts.post_name LIKE %s", $like);
        }
        if (is_numeric($search_term) && in_array('ID', $this->search_fields, true)) {
            $where_parts[] = $wpdb->prepare("$wpdb->posts.ID = %d", $search_term);
        }

        if ($where_parts) {
            $search_where = sprintf(" AND ( %s ) ", implode(' OR ', $where_parts));
        }

        return $search_where;
    }

    public function get_total_pages(): int
    {
        $per_page = $this->query->get('posts_per_page');

        return (int)ceil($this->query->found_posts / $per_page);
    }

    public function get_page(): int
    {
        return (int)$this->query->get('paged');
    }

    public function is_last_page(): bool
    {
        return $this->get_total_pages() <= $this->get_page();
    }

}