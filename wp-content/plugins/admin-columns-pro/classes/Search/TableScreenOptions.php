<?php

namespace ACP\Search;

use AC;

class TableScreenOptions implements AC\Registerable
{

    public const INPUT_NAME = 'acp_enable_smart_filtering_button';

    /**
     * @var Preferences\SmartFiltering
     */
    private $preferences;

    /**
     * @var Settings\HideOnScreen\SmartFilters
     */
    private $hide_smart_filters;

    /**
     * @var AC\Asset\Location\Absolute
     */
    private $location;

    public function __construct(
        AC\Asset\Location\Absolute $location,
        Preferences\SmartFiltering $preferences,
        Settings\HideOnScreen\SmartFilters $hide_smart_filters
    ) {
        $this->location = $location;
        $this->preferences = $preferences;
        $this->hide_smart_filters = $hide_smart_filters;
    }

    public function register(): void
    {
        add_action('ac/table_scripts', [$this, 'scripts']);
        add_action('ac/table', [$this, 'register_screen_option']);
    }

    private function is_active(AC\ListScreen $list_screen): bool
    {
        return $this->preferences->is_active($list_screen);
    }

    public function register_screen_option(AC\Table\Screen $table): void
    {
        $list_screen = $table->get_list_screen();

        if ( ! TableScreenSupport::is_searchable($list_screen)) {
            return;
        }

        if ( ! $list_screen->has_id()) {
            return;
        }

        if ($this->hide_smart_filters->is_hidden($list_screen)) {
            return;
        }

        $check_box = new AC\Form\Element\Checkbox(self::INPUT_NAME);

        $check_box->set_options([1 => __('Smart Filtering', 'codepress-admin-columns')])
                  ->set_value($this->is_active($list_screen) ? 1 : 0);

        $table->register_screen_option($check_box);
    }

    public function scripts(AC\ListScreen $list_screen): void
    {
        if ( ! TableScreenSupport::is_searchable($list_screen)) {
            return;
        }

        $script = new AC\Asset\Script(
            'acp-search-table-screen-options',
            $this->location->with_suffix('assets/search/js/screen-options.bundle.js'),
            ['ac-table']
        );
        $script->enqueue();
    }

}