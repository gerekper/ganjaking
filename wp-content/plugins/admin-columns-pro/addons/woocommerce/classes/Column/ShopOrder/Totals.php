<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\ConditionalFormat\Formatter\PriceFormatter;
use ACA\WC\Settings;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;
use WC_Order;

class Totals extends AC\Column
    implements ACP\Sorting\Sortable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable
{

    /**
     * @var WC_Order[]
     */
    private $orders;

    public function __construct()
    {
        $this->set_type('column-wc-order_totals')
             ->set_label(__('Totals', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function conditional_format(): ?FormattableConfig
    {
        return new FormattableConfig(new PriceFormatter());
    }

    public function get_meta_key()
    {
        switch ($this->get_setting_total_property()) {
            case 'total' :
                return '_order_total';
            case 'discount' :
                return '_cart_discount';
            case 'shipping' :
                return '_order_shipping';
            default:
                return null;
        }
    }

    public function get_value($id)
    {
        $price = $this->get_raw_value($id);

        if ( ! $price) {
            return $this->get_empty_char();
        }

        return wc_price(
            $this->get_raw_value($id),
            [
                'currency' => $this->get_order($id)->get_currency(),
            ]
        );
    }

    public function get_raw_value($id)
    {
        switch ($this->get_setting_total_property()) {
            case 'fees' :
                return $this->get_order($id)->get_fees();
            case 'subtotal' :
                return $this->get_order($id)->get_subtotal();
            case 'discount' :
                return $this->get_order($id)->get_total_discount();
            case 'refunded' :
                return $this->get_order($id)->get_total_refunded();
            case 'tax' :
                return $this->get_order($id)->get_total_tax();
            case 'shipping' :
                return $this->get_order($id)->get_shipping_total();
            case 'paid' :
                if ($this->get_order($id)->is_paid()) {
                    return $this->get_order($id)->get_total() - $this->get_order($id)->get_total_refunded();
                }

                return 0;
            default :
                return $this->get_order($id)->get_total();
        }
    }

    /**
     * @param int $id
     *
     * @return WC_Order
     */
    private function get_order($id)
    {
        if ( ! isset($this->orders[$id])) {
            $this->orders[$id] = wc_get_order($id);
        }

        return $this->orders[$id];
    }

    /**
     * @return string|false
     */
    private function get_setting_total_property()
    {
        $setting = $this->get_setting('order_total_property');

        if ( ! $setting instanceof Settings\ShopOrder\Totals) {
            return false;
        }

        return $setting->get_order_total_property();
    }

    public function register_settings()
    {
        $this->add_setting(new Settings\ShopOrder\Totals($this));
    }

    public function search()
    {
        if ( ! $this->get_meta_key()) {
            return false;
        }

        return new ACP\Search\Comparison\Meta\Decimal($this->get_meta_key());
    }

    public function sorting()
    {
        if ( ! $this->get_meta_key()) {
            return null;
        }

        return new ACP\Sorting\Model\Post\Meta($this->get_meta_key());
    }

}