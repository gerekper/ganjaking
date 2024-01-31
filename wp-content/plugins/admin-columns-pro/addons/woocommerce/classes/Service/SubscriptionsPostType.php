<?php

namespace ACA\WC\Service;

use AC;
use AC\Registerable;
use ACA\WC\Column;
use ACA\WC\ListScreen\Product;
use ACA\WC\ListScreen\ShopOrder;
use ACA\WC\ListScreenFactory;
use ACA\WC\Search;

final class SubscriptionsPostType implements Registerable
{

    public function register(): void
    {
        add_action('ac/column_groups', [$this, 'register_column_groups']);
        add_action('ac/column_types', [$this, 'add_product_columns']);
        add_action('ac/column_types', [$this, 'add_user_columns']);
        add_action('ac/column_types', [$this, 'add_order_columns']);

        AC\ListScreenFactory\Aggregate::add(new ListScreenFactory\ShopSubscriptionFactory());
    }

    public function register_column_groups(AC\Groups $groups): void
    {
        $groups->add('woocommerce_subscriptions', __('WooCommerce Subscriptions', 'codepress-admin-columns'), 15);
    }

    public function add_product_columns(AC\ListScreen $list_screen): void
    {
        if ($list_screen instanceof Product) {
            $columns = [
                Column\ProductSubscription\Expires::class,
                Column\ProductSubscription\FreeTrial::class,
                Column\ProductSubscription\LimitSubscription::class,
                Column\ProductSubscription\Period::class,
            ];

            foreach ($columns as $column) {
                $list_screen->register_column_type(new $column());
            }
        }
    }

    public function add_user_columns(AC\ListScreen $list_screen): void
    {
        if ($list_screen instanceof AC\ListScreen\User) {
            $columns = [
                Column\UserSubscription\ActiveSubscriber::class,
                Column\UserSubscription\InactiveSubscriber::class,
                Column\UserSubscription\Subscriptions::class,
            ];

            foreach ($columns as $column) {
                $list_screen->register_column_type(new $column());
            }
        }
    }

    public function add_order_columns(AC\ListScreen $list_screen): void
    {
        if ($list_screen instanceof ShopOrder) {
            $columns = [
                Column\ShopOrder\SubscriptionRelationship::class,
            ];

            foreach ($columns as $column) {
                $list_screen->register_column_type(new $column());
            }
        }
    }

}