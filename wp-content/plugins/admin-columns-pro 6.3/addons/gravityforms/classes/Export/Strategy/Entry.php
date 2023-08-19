<?php

namespace ACA\GravityForms\Export\Strategy;

use AC;
use ACA\GravityForms\ListScreen;
use ACA\GravityForms\ListTable;
use ACA\GravityForms\Utils\Hooks;
use ACP\Export\Strategy;

class Entry extends Strategy
{

    public function __construct(ListScreen\Entry $list_screen)
    {
        parent::__construct($list_screen);
    }

    protected function ajax_export(): void
    {
        add_filter('gform_get_entries_args_entry_list', [$this, 'set_pagination_args'], 11);
        add_action(Hooks::get_load_form_entries(), [$this, 'delayed_export']);
    }

    public function delayed_export(): void
    {
        $table = $this->list_screen->get_list_table();
        $table->prepare_items();

        $this->export(wp_list_pluck($table->items, 'id'));
    }

    public function set_pagination_args(array $args): array
    {
        $per_page = $this->get_num_items_per_iteration();

        $args['paging']['page_size'] = $per_page;
        $args['paging']['offset'] = $this->get_export_counter() * $per_page;

        $ids = $this->get_requested_ids();

        if ($ids) {
            $args['search_criteria']['field_filters'] = [
                [
                    'key'      => 'id',
                    'operator' => 'IN',
                    'value'    => $ids,
                ],
            ];
        }

        return $args;
    }

    protected function get_list_table(): AC\ListTable
    {
        return new ListTable($this->list_screen->get_list_table());
    }

}