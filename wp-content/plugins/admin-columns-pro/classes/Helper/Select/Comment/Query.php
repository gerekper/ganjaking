<?php

namespace ACP\Helper\Select\Comment;

use AC\ApplyFilter\QueryTotalNumber;
use AC\ArrayIterator;
use AC\Helper\Select\Paginated;
use WP_Comment_Query;

class Query extends ArrayIterator
    implements Paginated
{

    /**
     * @var WP_Comment_Query
     */
    protected $query;

    public function __construct(array $args = [])
    {
        $args = array_merge([
            'number'        => (new QueryTotalNumber())->apply_filter(),
            'fields'        => 'ID',
            'orderby'       => 'comment_date_gmt',
            'paged'         => 1,
            'search'        => null,
            'no_found_rows' => false,
        ], $args);

        $args['offset'] = ($args['paged'] - 1) * $args['number'];

        $this->query = new WP_Comment_Query($args);

        parent::__construct($this->query->get_comments());
    }

    public function get_total_pages(): int
    {
        return $this->query->max_num_pages;
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