<?php

namespace ACA\MLA\Service;

use AC\Asset\Location\Absolute;
use AC\Asset\Script;
use AC\ListScreen;
use AC\Registerable;
use ACA\MLA\ListScreen\MediaLibrary;

class TableScreen implements Registerable
{

    private $location;

    public function __construct(Absolute $location)
    {
        $this->location = $location;
    }

    public function register(): void
    {
        add_action('ac/table_scripts', [$this, 'table_scripts'], 11);
    }

    public function table_Scripts(ListScreen $list_screen)
    {
        if ( ! $list_screen instanceof MediaLibrary) {
            return;
        }

        $script = new Script('aca-mla-table', $this->location->with_suffix('assets/js/table.js'));
        $script->enqueue();
    }

}