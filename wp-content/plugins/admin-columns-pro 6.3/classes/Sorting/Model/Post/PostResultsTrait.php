<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

trait PostResultsTrait
{

    use PostRequestTrait;

    protected $db_columns;

    protected $formatter;

    protected $max_value_length = 100;

    protected $sort_numeric = false;

    public function get_post_ids(): array
    {
        $ids = [];

        foreach ($this->get_query_results() as $row) {
            $ids[$row->id] = $this->format_rows($row);
        }

        $ids = array_filter($ids);

        $this->sort_numeric
            ? asort($ids, SORT_NUMERIC)
            : natcasesort($ids);

        return array_keys($ids);
    }

    private function format_rows(object $row): string
    {
        $value = '';

        foreach ($this->db_columns as $column) {
            $value .= $this->formatter->format_value($row->{$column});
        }

        return $value;
    }

    private function get_query_results(): array
    {
        global $wpdb;

        $fields = "";

        foreach ($this->db_columns as $column) {
            $fields .= $this->max_value_length > 0
                ? sprintf(
                    ', LEFT( %1$s.%2$s, %3$d ) AS %2$s',
                    $wpdb->posts,
                    esc_sql($column),
                    $this->max_value_length
                )
                : sprintf(
                    ', %1$s.%2$s AS %2$s',
                    $wpdb->posts,
                    esc_sql($column)
                );
        }

        $where = '';

        $status = $this->get_var_post_status();

        if ($status) {
            $where = $wpdb->prepare("\nAND $wpdb->posts.post_status = %s", $status);
        }

        $sql = $wpdb->prepare(
            "
            SELECT $wpdb->posts.ID AS id $fields 
            FROM $wpdb->posts
            WHERE $wpdb->posts.post_type = %s
                $where
            ",
            $this->get_var_post_type()
        );

        return $wpdb->get_results($sql);
    }
}