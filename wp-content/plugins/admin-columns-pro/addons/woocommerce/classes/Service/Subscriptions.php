<?php

namespace ACA\WC\Service;

use AC;
use AC\Asset\Location\Absolute;
use AC\Registerable;
use ACA\WC\Editing;
use ACA\WC\ListScreen;
use ACA\WC\ListScreen\Product;
use ACA\WC\ListScreenFactory;
use ACA\WC\Search;
use ACP;
use Automattic\WooCommerce\Internal\Admin\Orders\PageController;

final class Subscriptions implements Registerable
{

    private $location;

    public function __construct(Absolute $location)
    {
        $this->location = $location;
    }

    public function register(): void
    {
        add_action('ac/column_groups', [$this, 'register_column_groups']);
        add_action('ac/column_types', [$this, 'add_product_columns']);
        add_action('ac/column_types', [$this, 'add_user_columns']);
        add_action('ac/table/list_screen', [$this, 'register_table_rows']);

        ACP\QueryFactory::register('wc_order_subscription', Search\Query\Order::class);
        ACP\Search\TableScreenFactory::register(
            ListScreen\OrderSubscription::class,
            Search\TableScreen\OrderSubscription::class
        );
        AC\ListScreenFactory\Aggregate::add(
            new ListScreenFactory\OrderSubscriptionFactory(
                require $this->location->with_suffix('config/columns/orders_subscriptions.php')->get_path(),
                wc_get_container()->get(PageController::class)
            )
        );
    }

    public function register_table_rows(AC\ListScreen $list_screen)
    {
        if ( ! $list_screen instanceof ListScreen\OrderSubscription) {
            return;
        }

        $table_rows = new Editing\TableRows\Order(new AC\Request(), $list_screen);

        if ($table_rows->is_request()) {
            $table_rows->register();
        }
    }

    public function register_column_groups(AC\Groups $groups): void
    {
        $groups->add('woocommerce_subscriptions', __('WooCommerce Subscriptions', 'codepress-admin-columns'), 15);
    }

    public function add_product_columns(AC\ListScreen $list_screen): void
    {
        if ($list_screen instanceof Product) {
            $columns = require $this->location->with_suffix('config/columns/subscription/products.php')->get_path();

            foreach ($columns as $column) {
                $list_screen->register_column_type(new $column());
            }
        }
    }

    public function add_user_columns(AC\ListScreen $list_screen): void
    {
        if ($list_screen instanceof AC\ListScreen\User) {
            $columns = require $this->location->with_suffix('config/columns/subscription/users.php')->get_path();

            foreach ($columns as $column) {
                $list_screen->register_column_type(new $column());
            }
        }
    }

}