<?php

namespace ACP\Export\Strategy;

use AC\ListTable;
use AC\ListTableFactory;
use ACP;
use ACP\Export\Strategy;
use WP_Term_Query;

class Taxonomy extends Strategy
{

    public function __construct(ACP\ListScreen\Taxonomy $list_screen)
    {
        parent::__construct($list_screen);
    }

    protected function get_list_table(): ?ListTable
    {
        return (new ListTableFactory())->create_from_globals();
    }

    protected function ajax_export(): void
    {
        add_action('parse_term_query', [$this, 'terms_query'], PHP_INT_MAX - 100);
    }

    /**
     * Catch the terms query and run it with altered parameters for pagination. This should be
     * attached to the parse_term_query hook when an AJAX request is sent
     *
     * @param WP_Term_Query $query
     *
     * @see   action:parse_term_query
     * @since 1.0
     */
    public function terms_query(WP_Term_Query $query): void
    {
        if ($query->query_vars['fields'] !== 'count') {
            return;
        }

        remove_action('parse_term_query', [$this, __FUNCTION__], PHP_INT_MAX - 100);

        $per_page = $this->get_num_items_per_iteration();

        $query->query_vars['offset'] = $this->get_export_counter() * $per_page;
        $query->query_vars['number'] = $per_page;
        $query->query_vars['fields'] = 'ids';

        $ids = $this->get_requested_ids();

        if ($ids) {
            $query->query_vars['include'] = isset($query->query_vars['include'])
                ? array_merge($ids, wp_parse_id_list($query->query_vars['include']))
                : $ids;
        }

        $modified_query = new WP_Term_Query($query->query_vars);
        $terms = $modified_query->get_terms();

        $this->export($terms);
    }

}