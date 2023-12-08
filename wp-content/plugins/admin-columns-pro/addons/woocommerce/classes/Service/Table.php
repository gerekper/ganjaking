<?php

namespace ACA\WC\Service;

use AC\ListScreen;
use AC\Registerable;
use ACA\WC\ListScreen\Order;
use ACA\WC\ListScreen\Product;
use ACP\ListScreen\Post;

class Table implements Registerable
{

    public function register(): void
    {
        add_action('ac/table/list_screen', [$this, 'fix_manual_product_sort'], 12); // After Sorting is applied
        add_filter(
            'acp/sorting/remember_last_sorting_preference',
            [
                $this,
                'disable_product_sorting_mode_preference',
            ],
            10,
            2
        );
        add_filter('acp/sticky_header/enable', [$this, 'disable_sticky_headers']);
        add_filter('acp/table/query_args_whitelist', [$this, 'add_query_arg_customer_to_whitelist'], 10, 2);
    }

    public function add_query_arg_customer_to_whitelist(array $args, ListScreen $list_screen): array
    {
        if ($list_screen instanceof Order) {
            $args[] = '_customer_user';
        }

        return $args;
    }

    public function fix_manual_product_sort(ListScreen $list_screen): void
    {
        if (
            isset($_GET['orderby']) &&
            $list_screen instanceof Post &&
            $list_screen->get_post_type() === 'product' &&
            strpos($_GET['orderby'], 'menu_order') !== false &&
            ! filter_input(INPUT_GET, 'orderby')
        ) {
            unset($_GET['orderby']);
        }
    }

    public function disable_sticky_headers(bool $enabled): bool
    {
        if (
            'product' === filter_input(INPUT_GET, 'post_type') &&
            'menu_order title' === filter_input(INPUT_GET, 'orderby')
        ) {
            return false;
        }

        return $enabled;
    }

    public function disable_product_sorting_mode_preference(bool $enabled, ListScreen $list_screen): bool
    {
        if ($list_screen instanceof Product && 'menu_order title' === filter_input(INPUT_GET, 'orderby')) {
            return false;
        }

        return $enabled;
    }

}