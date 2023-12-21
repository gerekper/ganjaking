<?php

namespace ACP\Search\TableScreen;

use ACP\Search\TableScreen;

class Taxonomy extends TableScreen
{

    public function register(): void
    {
        add_action('in_admin_footer', [$this, 'filters_markup'], 1);

        parent::register();
    }

}