<?php

declare(strict_types=1);

namespace ACA\WC\ListScreenFactory;

use AC\ListScreen;
use AC\ListScreenFactory\BaseFactory;
use ACA\WC\ListScreen\Order;
use Automattic;
use WP_Screen;

class OrderFactory extends BaseFactory
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
        return 'wc_order' === $key;
    }

    protected function create_list_screen(string $key): ListScreen
    {
        return new Order($this->column_config);
    }

    public function can_create_from_wp_screen(WP_Screen $screen): bool
    {
        $action = $_GET['action'] ?? null;
        $order = $_GET['order'] ?? null;

        return 'woocommerce_page_wc-orders' === $screen->base &&
               'woocommerce_page_wc-orders' === $screen->id &&
               $this->page_controller->is_order_screen('shop_order', 'list') &&
               'trash' !== $action &&
               ! is_array($order);
    }

    protected function create_list_screen_from_wp_screen(WP_Screen $screen): ListScreen
    {
        return new Order($this->column_config);
    }

}