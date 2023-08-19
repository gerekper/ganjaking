<?php

declare(strict_types=1);

namespace ACA\MLA\Export;

use AC;
use AC\ThirdParty\MediaLibraryAssistant\ListTable;
use AC\ThirdParty\MediaLibraryAssistant\WpListTableFactory;
use ACA\MLA\ListScreen;
use ACP;

class Strategy extends ACP\Export\Strategy
{

    public function __construct(ListScreen\MediaLibrary $list_screen)
    {
        parent::__construct($list_screen);
    }

    private function get_wp_list_table_factory(): WpListTableFactory
    {
        return new WpListTableFactory();
    }

    protected function get_list_table(): ?AC\ListTable
    {
        return new ListTable($this->get_wp_list_table_factory()->create());
    }

    public function get_total_items(): ?int
    {
        // will be populated through JS
        return 1;
    }

    protected function ajax_export(): void
    {
        add_filter('mla_list_table_query_final_terms', [$this, 'query'], 1e6);
        add_action('mla_list_table_prepare_items', [$this, 'prepare_items'], 10, 2);
        add_filter('posts_clauses', [$this, 'filter_ids']);

        // Trigger above hooks early by initiating list table. This prevents "headers already sent".
        $this->get_wp_list_table_factory()->create();
    }

    public function filter_ids($clauses)
    {
        global $wpdb;

        $ids = $this->get_requested_ids();

        if ($ids) {
            $clauses['where'] .= sprintf("\nAND $wpdb->posts.ID IN( %s )", implode(',', $ids));
        }

        return $clauses;
    }

    public function query($request)
    {
        $per_page = $this->get_num_items_per_iteration();

        $request['offset'] = $this->get_export_counter() * $per_page;
        $request['posts_per_page'] = $per_page;
        $request['posts_per_archive_page'] = $per_page;

        return $request;
    }

    public function prepare_items($query): void
    {
        $this->export(wp_list_pluck($query->items, 'ID'));
    }

}