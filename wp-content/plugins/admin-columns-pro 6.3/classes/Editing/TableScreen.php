<?php

declare(strict_types=1);

namespace ACP\Editing;

use AC;
use AC\Asset\Location;
use AC\Asset\Style;
use AC\Registerable;
use ACP\Editing\Factory\BulkEditFactory;
use ACP\Editing\Factory\InlineEditFactory;
use ACP\Editing\Preference\EditState;
use ACP\Export;

class TableScreen implements Registerable
{

    /**
     * @var AC\ListScreen
     */
    private $list_screen;

    /**
     * @var Location\Absolute
     */
    private $location;

    /**
     * @var InlineEditFactory
     */
    private $inline_edit_factory;

    /**
     * @var BulkEditFactory
     */
    private $bulk_edit_factory;

    public function __construct(
        AC\ListScreen $list_screen,
        Location\Absolute $location,
        InlineEditFactory $inline_edit_factory,
        BulkEditFactory $bulk_edit_factory
    ) {
        $this->list_screen = $list_screen;
        $this->location = $location;
        $this->inline_edit_factory = $inline_edit_factory;
        $this->bulk_edit_factory = $bulk_edit_factory;
    }

    public function register(): void
    {
        add_action('ac/table_scripts', [$this, 'register_scripts']);
    }

    public function register_scripts()
    {
        $supports = [
            'inline_edit' => (bool)$this->inline_edit_factory->create(),
            'bulk_edit'   => (bool)$this->bulk_edit_factory->create(),
            'bulk_delete' => $this->is_bulk_delete_enabled(),
            'export'      => $this->is_export_enabled(),
        ];

        // Bail if nothing is supported
        if ( ! in_array(true, $supports, true)) {
            return;
        }

        add_action('ac/table/actions', [$this, 'edit_button']);

        $script = new Asset\Script\Table(
            'acp-editing-table',
            $this->location->with_suffix('assets/editing/js/table.js'),
            $this->list_screen,
            new EditableDataFactory($this->inline_edit_factory, $this->bulk_edit_factory),
            new Preference\EditState(),
            $supports
        );

        $script->enqueue();

        // CSS
        $style = new Style(
            'acp-editing-table',
            $this->location->with_suffix('assets/editing/css/table.css'),
            ['ac-utilities']
        );
        $style->enqueue();

        // Select 2
        wp_enqueue_script('ac-select2');
        wp_enqueue_style('ac-select2');

        // WP Media picker
        wp_enqueue_media();
        wp_enqueue_style('ac-jquery-ui');

        // WP Color picker
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('wp-color-picker');

        // WP Content Editor
        wp_enqueue_editor();

        do_action('ac/table_scripts/editing', $this->list_screen);
    }

    public function edit_button()
    {
        if ( ! $this->list_screen->has_id() || ! $this->inline_edit_factory->create()) {
            return;
        }

        $view = new AC\View([
            'is_active' => $this->is_edit_state_active(),
        ]);

        echo $view->set_template('table/edit-button');
    }

    private function is_edit_state_active()
    {
        return (new EditState())->is_active($this->list_screen->get_key());
    }

    public function is_export_enabled(): bool
    {
        return Export\TableScreen::is_exportable($this->list_screen);
    }

    public function is_bulk_delete_enabled(): bool
    {
        if ( ! $this->list_screen instanceof BulkDelete\ListScreen || ! $this->list_screen->has_id()) {
            return false;
        }

        $option = new HideOnScreen\BulkDelete();

        if ($option->is_hidden($this->list_screen)) {
            return false;
        }

        return $this->list_screen->deletable()->user_can_delete();
    }

}