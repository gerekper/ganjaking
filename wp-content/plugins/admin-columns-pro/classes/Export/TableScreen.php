<?php

namespace ACP\Export;

use AC;
use AC\Asset\Location;
use AC\ColumnRepository;
use AC\Registerable;
use ACP\Export\Asset\TableScriptFactory;
use ACP\Export\ColumnRepository\Filter;

class TableScreen implements Registerable
{

    protected $location;

    public function __construct(Location $location)
    {
        $this->location = $location;
    }

    public function register(): void
    {
        add_action('ac/table/list_screen', [$this, 'load_list_screen']);
        add_filter('ac/table/body_class', [$this, 'add_hide_export_button_class'], 10, 2);
    }

    /**
     * Load a list screen and potentially attach the proper exporting information to it
     */
    public function load_list_screen(AC\ListScreen $list_screen): void
    {
        if ( ! self::is_exportable($list_screen)) {
            return;
        }

        if ($list_screen instanceof ListScreen) {
            $list_screen->export()->attach();
        }

        add_action('ac/table', [$this, 'register_screen_option']);
        add_action('ac/table/admin_footer', [$this, 'scripts']);
    }

    public static function is_exportable(AC\ListScreen $list_screen): bool
    {
        if ( ! $list_screen instanceof ListScreen || ! $list_screen->has_id()) {
            return false;
        }

        $column_repository = new ColumnRepository($list_screen);
        $columns = $column_repository->find_all([
            'filter' => [
                new Filter\ExportableColumns(),
            ],
        ]);

        if ( ! $columns) {
            return false;
        }

        $is_active = ! (new HideOnScreen\Export())->is_hidden($list_screen);

        return (new ApplyFilter\ListScreenActive($list_screen))->apply_filters($is_active);
    }

    public function scripts(AC\ListScreen $list_screen): void
    {
        if ( ! $list_screen instanceof ListScreen) {
            return;
        }

        $style = new AC\Asset\Style(
            'acp-export-listscreen',
            $this->location->with_suffix('assets/export/css/listscreen.css')
        );
        $style->enqueue();

        $factory = new TableScriptFactory($this->location);
        $factory->create($list_screen, $this->get_export_button_setting($list_screen))
                ->enqueue();
    }

    public function register_screen_option(AC\Table\Screen $table): void
    {
        $list_screen = $table->get_list_screen();

        $check_box = new AC\Form\Element\Checkbox('acp_export_show_export_button');
        $check_box->set_options([1 => __('Export Button', 'codepress-admin-columns')])
                  ->set_value($this->get_export_button_setting($list_screen) ? 1 : 0);

        $table->register_screen_option($check_box);
    }

    public function preferences(): UserPreference\ShowExportButton
    {
        return new UserPreference\ShowExportButton();
    }

    private function get_export_button_setting(AC\ListScreen $list_screen): bool
    {
        $setting = $this->preferences()->get($list_screen->get_key());

        // No setting found, enable export
        if ($setting === null) {
            $setting = 1;
        }

        return 1 === $setting;
    }

    /**
     * @param string          $classes
     * @param AC\Table\Screen $table
     *
     * @return string
     */
    public function add_hide_export_button_class($classes, $table)
    {
        if ( ! $this->get_export_button_setting($table->get_list_screen())) {
            $classes .= ' ac-hide-export-button';
        }

        return $classes;
    }

}