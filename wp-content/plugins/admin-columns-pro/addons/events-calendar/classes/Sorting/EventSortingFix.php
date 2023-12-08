<?php

namespace ACA\EC\Sorting;

use AC;
use ACP\Sorting\ModelFactory;
use WP_Query;

class EventSortingFix
{

    /**
     * @var AC\ListScreen
     */
    private $list_screen;

    public function __construct(AC\ListScreen $list_screen)
    {
        $this->list_screen = $list_screen;
    }

    public function register()
    {
        add_action('pre_get_posts', [$this, 'deregister_tribe_sorting_hooks']);
    }

    public function deregister_tribe_sorting_hooks(WP_Query $wp_query)
    {
        if ( ! class_exists('Tribe__Events__Main') || ! class_exists(
                'Tribe__Events__Admin_List'
            ) || ! $wp_query->is_main_query()) {
            return;
        }

        $column = $this->list_screen->get_column_by_name($wp_query->get('orderby'));

        if ( ! $column) {
            return;
        }

        $model_factory = new ModelFactory();
        $has_model = $model_factory->create_model($column) || $model_factory->create_bindings($column);

        if ( ! $has_model) {
            return;
        }

        remove_filter('posts_fields', ['Tribe__Events__Admin_List', 'events_search_fields'], 10);
        remove_filter('posts_clauses', ['Tribe__Events__Admin_List', 'sort_by_event_date'], 11);
    }

}