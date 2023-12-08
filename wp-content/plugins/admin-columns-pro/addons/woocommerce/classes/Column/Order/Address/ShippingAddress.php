<?php

namespace ACA\WC\Column\Order\Address;

use AC;
use ACA\WC\ColumnValue\OrderAddress;
use ACA\WC\Editing;
use ACA\WC\Search;
use ACA\WC\Settings;
use ACA\WC\Sorting;
use ACA\WC\Type\AddressType;
use ACP;

class ShippingAddress extends AC\Column implements ACP\Search\Searchable, ACP\Editing\Editable, ACP\Export\Exportable,
                                                   ACP\ConditionalFormat\Formattable, ACP\Sorting\Sortable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-order_shipping_address')
             ->set_label(__('Shipping Address', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $value = new OrderAddress(
            $this->get_address_type(),
            $this->get_display_property(),
            $this->get_empty_char()
        );

        return $value->render($id);
    }

    private function get_display_property(): string
    {
        $setting = $this->get_setting('address_property');

        return $setting instanceof Settings\Address
            ? $setting->get_address_property()
            : '';
    }

    protected function register_settings()
    {
        parent::register_settings();

        $this->add_setting(new Settings\Address($this));
    }

    private function get_address_type(): AddressType
    {
        return new AddressType(AddressType::SHIPPING);
    }

    public function search()
    {
        return (new Search\Order\AddressesComparisonFactory($this->get_address_type()))->create(
            $this->get_display_property()
        );
    }

    public function sorting()
    {
        return (new Sorting\Order\AddressesFactory($this->get_address_type()))->create(
            $this->get_display_property()
        );
    }

    public function editing()
    {
        return (new Editing\Order\AddressServiceFactory($this->get_address_type()))->create(
            $this->get_display_property()
        );
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedValue($this);
    }

}