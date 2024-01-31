<?php

declare(strict_types=1);

namespace ACP\Helper\Select\CommentField;

use AC\ApplyFilter\QueryTotalNumber;
use AC\ArrayIterator;
use AC\Helper\Select\Paginated;

class Query extends ArrayIterator implements Paginated
{

    private $field;

    private $args;

    public function __construct(string $field, array $args = [])
    {
        $this->field = $field;
        $this->args = array_merge([
            'limit'  => (new QueryTotalNumber())->apply_filter(),
            'search' => null,
            'page'   => 1,
        ], $args);

        parent::__construct($this->get());
    }

    public function get(): array
    {
        global $wpdb;

        $search = $this->args['search'];
        $limit = (int)$this->args['limit'];

        $sql = sprintf(
            "
			SELECT DISTINCT %s AS cfield 
			FROM $wpdb->comments 
			WHERE %s != ''
		",
            esc_sql($this->field),
            esc_sql($this->field)
        );

        if ($search) {
            $sql .= sprintf(
                "\nWHERE %s LIKE '%s'",
                esc_sql($this->field),
                '%' . $wpdb->esc_like($search) . '%'
            );
        }

        $sql .= sprintf("\nLIMIT %d", $limit);

        $rows = $wpdb->get_col($sql);

        natcasesort($rows);

        return array_values($rows);
    }

    public function get_total_pages(): int
    {
        return 1;
    }

    public function get_page(): int
    {
        return 1;
    }

    public function is_last_page(): bool
    {
        return true;
    }

}