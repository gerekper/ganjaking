<?php

declare(strict_types=1);

namespace ACA\WC\ListScreenFactory;

use AC\ListScreen;
use AC\ListScreenFactory\BaseFactory;
use ACA\WC\ListScreen\OrderSubscription;
use Automattic;
use WP_Screen;

class OrderSubscriptionFactory extends BaseFactory
{

    private $column_config;

    private $page_controller;

    public function __construct(
        array $column_config,
        Automattic\WooCommerce\Internal\Admin\Orders\PageController $page_controller
    ) {
        $this->column_config = $column_config;
        $this->page_controller = $page_controller;
    }

    public function can_create(string $key): bool
    {
        return 'wc_order_subscription' === $key;
    }

    protected function create_list_screen(string $key): ListScreen
    {
        return new OrderSubscription($this->column_config);
    }

    public function can_create_from_wp_screen(WP_Screen $screen): bool
    {
        return 'woocommerce_page_wc-orders--shop_subscription' === $screen->base &&
               'woocommerce_page_wc-orders--shop_subscription' === $screen->id &&
               $this->page_controller->is_order_screen('shop_subscription', 'list');
    }

    protected function create_list_screen_from_wp_screen(WP_Screen $screen): ListScreen
    {
        return new OrderSubscription($this->column_config);
    }

}