<?php

namespace ACP\Helper\Select\User;

use AC\ApplyFilter\QueryTotalNumber;
use AC\ArrayIterator;
use AC\Helper\Select\Paginated;
use WP_User_Query;

class Query extends ArrayIterator
    implements Paginated
{

    /**
     * @var WP_User_Query
     */
    protected $query;

    /**
     * @var string
     */
    private $searchterm;

    public function __construct(array $args = [])
    {
        $args = array_merge([
            'orderby'        => 'display_name',
            'search_columns' => ['ID', 'user_login', 'user_nicename', 'user_email', 'display_name'],
            'number'         => (new QueryTotalNumber())->apply_filter(),
            'paged'          => 1,
            'search'         => null,
        ], $args);

        $this->searchterm = $args['search'];

        if ($args['search']) {
            $args['search'] = sprintf('*%s*', trim($args['search'], '*'));
        }

        add_action('pre_user_query', [$this, 'callback_meta_query'], 1);

        $this->query = new WP_User_Query($args);

        parent::__construct($this->query->get_results());
    }

    /**
     * Add meta query for user's first and last name
     */
    public function callback_meta_query(WP_User_Query $query)
    {
        remove_action('pre_user_query', __FUNCTION__, 1);

        if ( ! $this->searchterm) {
            return;
        }

        global $wpdb;

        $query->query_from .= "\n INNER JOIN {$wpdb->usermeta} AS um ON um.user_id = {$wpdb->users}.ID";
        $query->query_where .= $wpdb->prepare(
            "\n OR ( um.meta_key = 'first_name' && um.meta_value LIKE %s )",
            '%' . $wpdb->esc_like($this->searchterm) . '%'
        );
        $query->query_where .= $wpdb->prepare(
            "\n OR ( um.meta_key = 'last_name' && um.meta_value LIKE %s )",
            '%' . $wpdb->esc_like($this->searchterm) . '%'
        );
        $query->query_where .= " GROUP BY {$wpdb->users}.ID";
    }

    public function get_total_pages(): int
    {
        $per_page = (int)$this->query->query_vars['number'];

        return (int)ceil($this->query->get_total() / $per_page);
    }

    public function get_page(): int
    {
        return (int)$this->query->query_vars['paged'];
    }

    public function is_last_page(): bool
    {
        return $this->get_total_pages() <= $this->get_page();
    }

}