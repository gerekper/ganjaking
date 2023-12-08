<?php

namespace ACA\GravityForms\Search;

final class Query extends \ACP\Query
{

    /**
     * @var int
     */
    private $form_id;

    /**
     * @var string
     */
    private $status;

    public function register(): void
    {
        add_filter('gform_get_entries_args_entry_list', [$this, 'catch_list_details'], 10, 3);
        add_filter('gform_gf_query_sql', [$this, 'parse_search_query']);
    }

    public function catch_list_details(array $args): array
    {
        $this->form_id = (int)$args['form_id'];
        $this->status = (string)$args['search_criteria']['status'];

        return $args;
    }

    public function parse_search_query(array $query): array
    {
        foreach ($this->bindings as $binding) {
            if ($binding->get_where()) {
                $query['where'] .= "\nAND " . $binding->get_where();
            }

            if ($binding->get_join()) {
                $query['join'] .= "\n" . $binding->get_join();
            }
        }

        return $query;
    }

}