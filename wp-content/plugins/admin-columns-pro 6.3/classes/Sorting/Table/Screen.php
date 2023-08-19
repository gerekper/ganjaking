<?php

declare(strict_types=1);

namespace ACP\Sorting\Table;

use AC;
use AC\Asset\Location;
use AC\Asset\Script;
use AC\Asset\Style;
use AC\ColumnRepository;
use ACP\Sorting;
use ACP\Sorting\ApplyFilter;
use ACP\Sorting\Controller;
use ACP\Sorting\ModelFactory;
use ACP\Sorting\NativeSortable\NativeSortableRepository;
use ACP\Sorting\Type\SortType;
use ACP\Sorting\UserPreference;

class Screen implements AC\Registerable
{

    private $list_screen;

    private $location;

    private $native_sortable_repository;

    private $model_factory;

    private $column_respository;

    private $preferred_sort;

    public function __construct(
        AC\ListScreen $list_screen,
        Location\Absolute $location,
        NativeSortableRepository $native_sortable_repository,
        ModelFactory $model_factory,
        ColumnRepository $column_respository,
        Sorting\Settings\ListScreen\PreferredSort $preferred_sort
    ) {
        $this->list_screen = $list_screen;
        $this->location = $location;
        $this->native_sortable_repository = $native_sortable_repository;
        $this->model_factory = $model_factory;
        $this->column_respository = $column_respository;
        $this->preferred_sort = $preferred_sort;
    }

    private function user_preference(): UserPreference\SortType
    {
        return new UserPreference\SortType($this->list_screen->get_storage_key());
    }

    public function register(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'scripts']);

        add_filter(
            'manage_' . $this->list_screen->get_screen_id() . '_sortable_columns',
            [$this, 'add_sortable_headings'],
            20
        );
        add_filter(
            'manage_' . $this->list_screen->get_screen_id() . '_sortable_columns',
            [$this, 'unset_original_sortable_headings'],
            21
        );

        // this needs to come first, because it overwrites order preference
        $controller_setter = new Controller\RequestSetterHandler(
            $this->user_preference(),
            $this->preferred_sort,
            new ApplyFilter\DefaultSort($this->list_screen)
        );
        $controller_setter->handle();

        $controller_vars = new Controller\ManageSortHandler(
            $this->list_screen,
            $this->model_factory
        );
        $controller_vars->handle();

        $controller_bindings = new Controller\ManageQueryHandler(
            $this->list_screen,
            $this->model_factory
        );
        $controller_bindings->handle();

        add_action('ac/table', [$this, 'add_reset_button']);
        add_action('shutdown', [$this, 'save_user_preference']);
    }

    public function add_reset_button(AC\Table\Screen $table): void
    {
        $sort_type = SortType::create_by_request(Sorting\NativeSortable\Request\Sort::create_from_globals());

        $button = $this->reset_button()->get($sort_type);

        if ($button) {
            $table->register_button($button, 40);
        }
    }

    private function reset_button(): ResetButton
    {
        return new ResetButton(
            $this->column_respository,
            $this->preferred_sort,
            new ApplyFilter\DefaultSort($this->list_screen)
        );
    }

    /**
     * When the orderby (and order) are set, save the preference
     */
    public function save_user_preference(): void
    {
        $request = Sorting\NativeSortable\Request\Sort::create_from_globals();
        $persist = (bool)apply_filters('acp/sorting/remember_last_sorting_preference', true, $this->list_screen);

        if ($persist && $request->get_order_by()) {
            $this->user_preference()->save(SortType::create_by_request($request));
        }
    }

    /**
     * @param array $sortable_columns Column name or label
     *
     * @return array Column name or Sanitized Label
     */
    public function add_sortable_headings($sortable_columns)
    {
        // Stores the default columns on the listings screen
        if ( ! wp_doing_ajax() && current_user_can(AC\Capabilities::MANAGE)) {
            $this->native_sortable_repository->update($sortable_columns ?: []);
        }

        if ( ! $this->list_screen->get_settings()) {
            return $sortable_columns;
        }

        $columns = $this->column_respository->find_all([
            ColumnRepository::ARG_FILTER => [new Filter\SortableColumns($this->model_factory)],
        ]);

        foreach ($columns as $column) {
            $column_name = $column->get_name();

            $sortable_columns[$column_name] = $column_name;
        }

        return $sortable_columns;
    }

    /**
     * @param array $sortable_columns
     *
     * @return array
     */
    public function unset_original_sortable_headings($sortable_columns)
    {
        $columns = $this->column_respository->find_all([
            ColumnRepository::ARG_FILTER => [new Filter\DisabledOriginalColumns()],
        ]);

        foreach ($columns as $column) {
            unset($sortable_columns[$column->get_name()]);
        }

        return $sortable_columns;
    }

    public function scripts(): void
    {
        $assets = [
            new Script('acp-sorting', $this->location->with_suffix('assets/sorting/js/table.js')),
            new Style('acp-sorting', $this->location->with_suffix('assets/sorting/css/table.css')),
        ];

        foreach ($assets as $asset) {
            $asset->enqueue();
        }
    }

}