<?php

namespace ACP;

use AC\Registerable;
use WP_Screen;
use WP_Term_Query;

final class TermQueryInformation implements Registerable
{

    private $is_main_query = false;

    private $taxonomy;

    private const KEY = 'ac_is_main_term_query';

    public function register(): void
    {
        add_action('current_screen', [$this, 'init']);
    }

    public function init(WP_Screen $screen): void
    {
        if ( ! $screen->taxonomy) {
            return;
        }

        $this->taxonomy = $screen->taxonomy;

        add_filter("edit_{$this->taxonomy}_per_page", [$this, 'set_main_query_true']);
        add_filter('admin_title', [$this, 'set_main_query_false']);
        add_action('parse_term_query', [$this, 'check_if_main_query'], 1);
    }

    public function set_main_query_true($value)
    {
        remove_filter("edit_{$this->taxonomy}_per_page", [$this, __FUNCTION__]);

        $this->is_main_query = true;

        return $value;
    }

    public function set_main_query_false($value)
    {
        $this->is_main_query = false;

        return $value;
    }

    public function check_if_main_query(WP_Term_Query $query): void
    {
        if ($this->is_main_query) {
            $this->set_main_query($query);
        }
    }

    private function set_main_query(WP_Term_Query $query): void
    {
        $query->query_vars[self::KEY] = true;
    }

    public static function is_main_query(WP_Term_Query $query): bool
    {
        return isset($query->query_vars[self::KEY]) && $query->query_vars[self::KEY];
    }

    public static function is_main_query_by_args($args): bool
    {
        return isset($args[self::KEY]) && $args[self::KEY];
    }

}