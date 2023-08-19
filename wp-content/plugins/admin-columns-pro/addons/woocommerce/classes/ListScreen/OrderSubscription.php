<?php

declare(strict_types=1);

namespace ACA\WC\ListScreen;

use AC;
use AC\ColumnRepository;
use AC\Type\Uri;
use ACA\WC\Editing;
use ACA\WC\Export;
use ACA\WC\ListTable\ManageValue;
use ACA\WC\ListTable\Orders;
use ACA\WC\Sorting;
use ACA\WC\Type\OrderSubscriptionTableUrl;
use ACP;
use Automattic;

class OrderSubscription extends AC\ListScreen implements ACP\Export\ListScreen, ACP\Editing\ListScreen,
                                                         AC\ListScreen\ManageValue, AC\ListScreen\ListTable
{

    private $column_config;

    public function __construct(array $column_config)
    {
        parent::__construct('wc_order_subscription', 'woocommerce_page_wc-orders--shop_subscription');

        $this->label = __('Subscription', 'woocommerce');
        $this->meta_type = 'wc_order_subscription';
        $this->group = 'woocommerce';
        $this->column_config = $column_config;
    }

    public function list_table(): AC\ListTable
    {
        return new Orders(
            wc_get_container()->get(Automattic\WooCommerce\Internal\Admin\Orders\ListTable::class)
        );
    }

    public function get_table_url(): Uri
    {
        return new OrderSubscriptionTableUrl($this->has_id() ? $this->get_id() : null);
    }

    public function manage_value(): AC\Table\ManageValue
    {
        return new ManageValue\Order('shop_subscription', new ColumnRepository($this));
    }

    protected function register_column_types(): void
    {
        $this->register_column_types_from_list($this->column_config);
    }

    public function editing()
    {
        return new Editing\Strategy\Order();
    }

    public function export()
    {
        return new Export\Strategy\Order($this, 'shop_subscription');
    }

}