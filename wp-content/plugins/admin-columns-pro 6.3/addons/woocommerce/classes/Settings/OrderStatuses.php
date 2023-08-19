<?php

namespace ACA\WC\Settings;

use AC;

class OrderStatuses extends AC\Settings\Column
{

    const NAME = 'order_status';

    /**
     * @var string[]
     */
    private $default_status;

    private $order_status = [];

    public function __construct(AC\Column $column, array $default_status = ['wc-completed'])
    {
        $this->default_status = $default_status;

        parent::__construct($column);
    }

    protected function define_options()
    {
        $statuses = wc_get_order_statuses();
        $default_value = [];

        foreach ($this->default_status as $status) {
            if (array_key_exists($status, $statuses)) {
                $default_value[] = $status;
            }
        }

        return [self::NAME => $default_value];
    }

    public function create_view()
    {
        $select = $this->create_element('multi-select')
                       ->set_options(wc_get_order_statuses());

        return new AC\View([
            'label'   => __('Order Status', 'codepress-admin-columns'),
            'setting' => $select,
        ]);
    }

    public function get_order_status()
    {
        return is_array($this->order_status)
            ? $this->order_status
            : [];
    }

    public function set_order_status($status)
    {
        $this->order_status = $status;
    }

}