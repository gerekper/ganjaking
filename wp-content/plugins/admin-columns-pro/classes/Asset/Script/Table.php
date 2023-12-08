<?php

declare(strict_types=1);

namespace ACP\Asset\Script;

use AC\Asset\Location\Absolute;
use AC\Asset\Script;
use AC\Asset\Script\Localize\Translation;
use AC\Capabilities;
use AC\ColumnSize;
use AC\ListScreen;
use AC\ListScreenCollection;
use AC\ListScreenRepository\Sort;
use AC\ListScreenRepository\Storage;
use AC\Type\ColumnWidth;
use AC\Type\Uri;
use ACP\Search\DefaultSegmentTrait;
use ACP\Settings\ListScreen\HideOnScreen;
use ACP\Settings\Option\LayoutStyle;
use WP_User;

class Table extends Script
{

    use DefaultSegmentTrait;

    private $list_screen;

    private $user_storage;

    private $list_storage;

    private $storage;

    public function __construct(
        Absolute $location,
        ListScreen $list_screen,
        ColumnSize\UserStorage $user_storage,
        ColumnSize\ListStorage $list_storage,
        Storage $storage
    ) {
        parent::__construct('acp-table', $location, [Script\GlobalTranslationFactory::HANDLE, 'jquery-ui-sortable']);

        $this->list_screen = $list_screen;
        $this->user_storage = $user_storage;
        $this->list_storage = $list_storage;
        $this->storage = $storage;
    }

    public function register(): void
    {
        parent::register();

        $user = wp_get_current_user();

        if ( ! $user) {
            return;
        }

        $translation = Translation::create([
            'column_sets'          => [
                'more'        => _x('%s more', 'number of items', 'codepress-admin-columns'),
                'switch_view' => __('Switch View', 'codepress-admin-columns'),
            ],
            'column_screen_option' => [
                'button_reset'              => _x('Reset', 'column-resize-button', 'codepress-admin-columns'),
                'label'                     => __('Columns', 'codepress-admin-columns'),
                'resize_columns_tool'       => __('Resize Columns', 'codepress-admin-columns'),
                'reset_confirmation'        => sprintf(
                    '%s %s',
                    __('Restore the current column widths and order to their defaults.', 'codepress-admin-columns'),
                    __('Are you sure?', 'codepress-admin-columns')
                ),
                'save_changes'              => __('Save changes', 'codepress-admin-columns'),
                'save_changes_confirmation' => sprintf(
                    '%s %s',
                    __(
                        'Save the current column widths and order changes as the new default for ALL users.',
                        'codepress-admin-columns'
                    ),
                    __('Are you sure?', 'codepress-admin-columns')
                ),
                'tip_reset'                 => __(
                    'Reset columns to their default widths and order.',
                    'codepress-admin-columns'
                ),
                'tip_save_changes'          => __(
                    'Save the current column widths and order changes.',
                    'codepress-admin-columns'
                ),
            ],
        ]);

        $this
            ->add_inline_variable('acp_table', [
                'column_sets'          => $this->get_table_views($user),
                'column_sets_style'    => $this->get_column_set_style(),
                'column_screen_option' => [
                    'has_manage_admin_cap' => current_user_can(Capabilities::MANAGE),
                ],
                'column_order'         => [
                    'active'        => $this->is_column_order_active(),
                    'current_order' => array_keys($this->list_screen->get_columns()),
                ],
                'column_width'         => [
                    'active'                    => $this->is_column_resize_active(),
                    'can_reset'                 => $this->user_storage->exists($this->list_screen->get_id()),
                    'minimal_pixel_width'       => 50,
                    'column_sizes_current_user' => $this->get_column_sizes_by_user($this->list_screen),
                    'column_sizes'              => $this->get_column_sizes($this->list_screen),
                ],
            ])->localize('acp_table_i18n', $translation);
    }

    private function is_table_views_active(): bool
    {
        return (bool)apply_filters('acp/table/views/active', true);
    }

    private function get_table_views(WP_User $user): array
    {
        if ( ! $this->is_table_views_active()) {
            return [];
        }

        return array_values(
            array_map(
                [$this, 'create_column_set_vars'],
                $this->get_list_screens($user)->get_copy()
            )
        );
    }

    private function get_list_screens(WP_User $user): ListScreenCollection
    {
        $list_screens = $this->storage->find_all_by_assigned_user(
            $this->list_screen->get_key(),
            $user,
            new Sort\UserOrder($user, $this->list_screen->get_key())
        );

        // An administrator should always be able to view the requested list screen
        if (user_can($user, Capabilities::MANAGE) && ! $list_screens->contains($this->list_screen)) {
            $list_screens->add($this->list_screen);
        }

        return $list_screens;
    }

    private function get_column_set_style(): string
    {
        $option = new LayoutStyle();

        return $option->get() ?: LayoutStyle::OPTION_DROPDOWN;
    }

    private function is_column_order_active(): bool
    {
        $hide_on_screen = new HideOnScreen\ColumnOrder();

        return (bool)apply_filters(
            'acp/column_order/active',
            ! $hide_on_screen->is_hidden($this->list_screen),
            $this->list_screen
        );
    }

    private function is_column_resize_active(): bool
    {
        $hide_on_screen = new HideOnScreen\ColumnResize();

        return (bool)apply_filters(
            'acp/resize_columns/active',
            ! $hide_on_screen->is_hidden($this->list_screen),
            $this->list_screen
        );
    }

    private function get_column_sizes_by_user(ListScreen $list_screen): array
    {
        $result = [];

        if ($list_screen->get_settings()) {
            foreach ($this->user_storage->get_all($list_screen->get_id()) as $column_name => $width) {
                $result[$column_name] = $this->create_vars($width);
            }
        }

        return $result;
    }

    private function create_vars(ColumnWidth $width): array
    {
        return [
            'value' => $width->get_value(),
            'unit'  => $width->get_unit(),
        ];
    }

    private function get_column_sizes(ListScreen $list_screen): array
    {
        $result = [];

        if ($list_screen->get_settings()) {
            foreach ($this->list_storage->get_all($list_screen) as $column_name => $width) {
                $result[$column_name] = $this->create_vars($width);
            }
        }

        return $result;
    }

    private function create_column_set_vars(ListScreen $list_screen): array
    {
        $column_set = [
            'id'                 => $list_screen->has_id() ? (string)$list_screen->get_id() : null,
            'label'              => $list_screen->get_title()
                ? htmlspecialchars_decode($list_screen->get_title())
                : $list_screen->get_label(),
            'url'                => (string)$this->add_filter_args_to_url($list_screen->get_table_url(), $list_screen),
            'pre_filtered'       => false,
            'pre_filtered_label' => null,
        ];

        $default_segment = $this->get_default_segment($list_screen);

        if ($default_segment) {
            $column_set['pre_filtered_label'] = sprintf(
                __('Filtered by: %s', 'codepress-admin-columns'),
                $default_segment->get_name()
            );
        }

        return $column_set;
    }

    private function add_filter_args_to_url(Uri $url, ListScreen $list_screen): Uri
    {
        $args = [
            'post_status',
            'author',
        ];

        switch (true) {
            case $list_screen instanceof ListScreen\User :
                $args[] = 'role';
                break;
            case $list_screen instanceof ListScreen\Comment :
                $args[] = 'comment_status';
                break;
        }

        $args = (array)apply_filters(
            'acp/table/query_args_whitelist',
            $args,
            $list_screen
        );

        foreach ($args as $arg) {
            $value = filter_input(INPUT_GET, $arg, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if ($value) {
                $url = $url->with_arg($arg, $value);
            }
        }

        return $url;
    }

}