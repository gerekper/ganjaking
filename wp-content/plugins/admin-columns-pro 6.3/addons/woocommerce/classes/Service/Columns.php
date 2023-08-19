<?php

namespace ACA\WC\Service;

use AC\ListScreen;
use AC\Registerable;

class Columns implements Registerable
{

    private $config;

    private $list_screen_key;

    public function __construct(string $list_screen_key, array $config)
    {
        $this->list_screen_key = $list_screen_key;
        $this->config = $config;
    }

    public function register(): void
    {
        add_action('ac/column_types', [$this, 'register_columns']);
    }

    public function register_columns(ListScreen $list_screen): void
    {
        if ($this->list_screen_key === $list_screen->get_key()) {
            foreach ($this->config as $column) {
                $list_screen->register_column_type(new $column());
            }
        }
    }

}