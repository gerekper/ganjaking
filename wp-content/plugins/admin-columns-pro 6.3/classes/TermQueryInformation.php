<?php

namespace ACP;

use AC\Registerable;
use WP_Term_Query;

final class TermQueryInformation implements Registerable
{

    private const KEY = 'ac_is_main_term_query';

    public function register(): void
    {
        add_action('parse_term_query', [$this, 'check_if_main_query'], 1);
    }

    public function check_if_main_query(WP_Term_Query $query): void
    {
        if ( ! isset($query->query_vars['echo']) && ('all' === $query->query_vars['fields'] || 'count' === $query->query_vars['fields'])) {
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