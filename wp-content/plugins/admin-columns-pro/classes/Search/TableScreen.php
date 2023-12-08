<?php

namespace ACP\Search;

use AC;
use AC\Asset\Enqueueable;
use AC\Registerable;
use ACP\Search\Settings\HideOnScreen;

abstract class TableScreen implements Registerable
{

    /**
     * @var AC\ListScreen
     */
    protected $list_screen;

    /**
     * @var Enqueueable[]
     */
    protected $assets;

    public function __construct(AC\ListScreen $list_screen, array $assets)
    {
        $this->list_screen = $list_screen;
        $this->assets = $assets;
    }

    public function register(): void
    {
        add_action('ac/table_scripts', [$this, 'scripts']);
    }

    public function scripts()
    {
        foreach ($this->assets as $asset) {
            $asset->enqueue();
        }
        wp_enqueue_style('wp-pointer');
    }

    /**
     * Display the markup on the current list screen
     */
    public function filters_markup()
    {
        ?>

		<div id="ac-s"></div>

        <?php
    }

    /**
     * @return bool
     */
    private function is_segment_hidden()
    {
        return (new HideOnScreen\SavedFilters())->is_hidden($this->list_screen);
    }

}