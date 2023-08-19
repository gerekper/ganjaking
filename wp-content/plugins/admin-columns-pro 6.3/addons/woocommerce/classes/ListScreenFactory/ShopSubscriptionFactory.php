<?php

namespace ACA\WC\ListScreenFactory;

use AC\ListScreen;
use AC\ListScreenFactory;
use ACA\WC\ListScreen\Subscriptions;
use WP_Screen;

class ShopSubscriptionFactory extends ListScreenFactory\BaseFactory
{

    private const KEY = 'shop_subscription';

    public function can_create(string $key): bool
    {
        return self::KEY === $key;
    }

    protected function create_list_screen(string $key): ListScreen
    {
        return new Subscriptions();
    }

    public function can_create_from_wp_screen(WP_Screen $screen): bool
    {
        return $screen->base === 'edit' && $screen->post_type === self::KEY;
    }

    protected function create_list_screen_from_wp_screen(WP_Screen $screen): ListScreen
    {
        return $this->create_list_screen(self::KEY);
    }

}