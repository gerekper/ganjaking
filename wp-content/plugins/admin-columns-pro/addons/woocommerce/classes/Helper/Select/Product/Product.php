<?php

namespace ACA\WC\Helper\Select\Product;

use AC\ApplyFilter\QueryTotalNumber;
use AC\ArrayIterator;
use AC\Helper\Select\Paginated;
use ACP\Helper\Select;
use WP_Query;

class Product extends ArrayIterator
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
            'post_type'      => 'product',
            'orderby'        => 'title',
            'order'          => 'ASC',
            'paged'          => 1,
            'post_status'    => 'any',
            'search_fields'  => ['post_title', 'sku', 'ID'],
            's'              => null,
            'fields'         => 'ids',
        ], $args);

        $this->search_fields = $args['search_fields'];

        add_filter('posts_join', [$this, 'join_postmeta']);
        add_filter('posts_search', [$this, 'add_search_fields'], 30, 2);
        add_filter('posts_groupby', [$this, 'group_post_ids']);

        $this->query = new WP_Query($args);

        $found_ids = $this->query->get_posts();

        $products = [];

        foreach ($found_ids as $id) {
            $products[] = wc_get_product($id);
        }

        $products = array_filter($products);

        parent::__construct($products);
    }

    public function add_search_fields($search_where, WP_Query $wp_query)
    {
        global $wpdb;

        remove_filter('posts_search', __FUNCTION__);

        // Empty search
        if ( ! $search_where) {
            return $search_where;
        }

        $search_term = $wp_query->query_vars['s'];

        $like = '%' . $wpdb->esc_like($search_term) . '%';

        $where_parts = [];

        if (in_array('post_title', $this->search_fields, true)) {
            $where_parts[] = $wpdb->prepare("{$wpdb->posts}.post_title LIKE %s", $like);
        }
        if (in_array('sku', $this->search_fields, true)) {
            $where_parts[] = $wpdb->prepare("acpm_sku.meta_value LIKE %s", $like);
        }
        if (is_numeric($search_term) && in_array('ID', $this->search_fields, true)) {
            $where_parts[] = $wpdb->prepare("$wpdb->posts.ID = %d", $search_term);
        }

        if ($where_parts) {
            $search_where = sprintf(" AND ( %s ) ", implode(' OR ', $where_parts));
        }

        return $search_where;
    }

    public function join_postmeta($join)
    {
        global $wpdb;

        remove_filter('posts_join', __FUNCTION__);

        if (in_array('sku', $this->search_fields, true)) {
            $join .= " LEFT JOIN $wpdb->postmeta acpm_sku ON $wpdb->posts.ID = acpm_sku.post_id AND acpm_sku.meta_key = '_sku'";
        }

        return $join;
    }

    /**
     * @return string
     */
    public function group_post_ids()
    {
        global $wpdb;

        remove_filter('posts_groupby', __FUNCTION__);

        return $wpdb->posts . '.ID';
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