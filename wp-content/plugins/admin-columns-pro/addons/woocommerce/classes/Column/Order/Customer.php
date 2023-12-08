<?php

namespace ACA\WC\Column\Order;

use AC;
use ACA\WC\Search;
use ACA\WC\Settings;
use ACA\WC\Sorting\Order\CustomerField;
use ACA\WC\Sorting\Order\CustomerFullname;
use ACA\WC\Sorting\Order\OrderData;
use ACP;

class Customer extends AC\Column implements ACP\Search\Searchable, ACP\Export\Exportable,
                                            ACP\ConditionalFormat\Formattable, ACP\Sorting\Sortable
{

    use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

    public function __construct()
    {
        $this->set_type('column-order_customer')
             ->set_label(__('Customer', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function register_settings()
    {
        $this->add_setting(new Settings\ShopOrder\Customer($this));
    }

    public function get_raw_value($id)
    {
        $order = wc_get_order($id);

        return $order
            ? $order->get_customer_id()
            : false;
    }

    public function search()
    {
        switch ($this->get_display_property()) {
            case AC\Settings\Column\User::PROPERTY_FIRST_NAME:
                return new Search\Order\Customer\UserMeta('first_name');
            case AC\Settings\Column\User::PROPERTY_LAST_NAME:
                return new Search\Order\Customer\UserMeta('last_name');
            case AC\Settings\Column\User::PROPERTY_NICKNAME :
                return new Search\Order\Customer\UserMeta('nickname');

            case AC\Settings\Column\User::PROPERTY_NICENAME :
            case AC\Settings\Column\User::PROPERTY_LOGIN :
            case AC\Settings\Column\User::PROPERTY_URL :
            case AC\Settings\Column\User::PROPERTY_EMAIL :
            case AC\Settings\Column\User::PROPERTY_DISPLAY_NAME :
                return new Search\Order\Customer\UserField($this->get_display_property());

            case AC\Settings\Column\User::PROPERTY_ID :
                return new Search\Order\Customer\UserId();
            case AC\Settings\Column\User::PROPERTY_FULL_NAME :

            default:
                return null;
        }
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedValue($this);
    }

    public function sorting()
    {
        switch ($this->get_display_property()) {
            case AC\Settings\Column\User::PROPERTY_FIRST_NAME:
            case AC\Settings\Column\User::PROPERTY_LAST_NAME:
                return new CustomerField($this->get_display_property());
            case AC\Settings\Column\User::PROPERTY_EMAIL:
                return new CustomerField('email');
            case AC\Settings\Column\User::PROPERTY_NICKNAME:
            case AC\Settings\Column\User::PROPERTY_LOGIN:
                return new CustomerField('username');
            case AC\Settings\Column\User::PROPERTY_FULL_NAME:
                return new CustomerFullname();
            case AC\Settings\Column\User::PROPERTY_ID:
                return new OrderData('customer_id');

            default:
                return false;
        }
    }

    public function get_display_property(): ?string
    {
        $setting = $this->get_setting('user');

        return $setting instanceof Settings\ShopOrder\Customer
            ? $setting->get_display_author_as()
            : null;
    }

}