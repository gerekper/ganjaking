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
use ACA\WC\Type\OrderTableUrl;
use ACP;
use Automattic;

class Order extends AC\ListScreen implements ACP\Export\ListScreen, ACP\Editing\ListScreen,
                                             AC\ListScreen\ManageValue, AC\ListScreen\ListTable
{

    private $column_config;

    public function __construct(array $column_config)
    {
        parent::__construct('wc_order', 'woocommerce_page_wc-orders');

        $this->label = __('Orders', 'woocommerce');
        $this->meta_type = 'wc_order';
        $this->group = 'woocommerce';
        $this->column_config = $column_config;
    }

    public function list_table(): AC\ListTable
    {
        return new Orders(
            wc_get_container()->get(Automattic\WooCommerce\Internal\Admin\Orders\ListTable::class)
        );
    }

    public function get_list_table()
    {
        return wc_get_container()->get(Automattic\WooCommerce\Internal\Admin\Orders\ListTable::class);
    }

    protected function get_object($id)
    {
        return wc_get_order($id);
    }

    public function get_table_url(): Uri
    {
        return new OrderTableUrl($this->has_id() ? $this->get_id() : null);
    }

    public function manage_value(): AC\Table\ManageValue
    {
        return new ManageValue\Order('shop_order', new ColumnRepository($this));
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
        return new Export\Strategy\Order($this);
    }

}