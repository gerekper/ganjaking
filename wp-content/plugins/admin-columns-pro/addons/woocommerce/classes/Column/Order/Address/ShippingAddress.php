<?php

namespace ACA\WC\Column\Order\Address;

use AC;
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

    public function sorting()
    {
        $display_property = $this->get_display_property();

        switch ($display_property) {
            case 'address_1':
            case 'address_2':
            case 'city':
            case 'company':
            case 'country':
            case 'first_name':
            case 'last_name':
            case 'postcode':
            case 'phone':
            case 'state':
                return new Sorting\Order\AddressField($display_property, $this->get_address_type());
            case 'full_name':
                return new Sorting\Order\FullNameAddress($this->get_address_type());
            default:
                return false;
        }
    }

    public function get_value($id)
    {
        $order = wc_get_order($id);

        $method = $this->get_address_map($this->get_display_property());

        $value = method_exists($order, $method)
            ? $order->$method()
            : false;

        return $value ?: $this->get_empty_char();
    }

    private function get_display_property(): string
    {
        $setting = $this->get_setting('address_property');

        return $setting instanceof Settings\Address
            ? $setting->get_address_property()
            : '';
    }

    private function get_address_map(string $property): string
    {
        $mapping = [
            'address_1'  => 'get_shipping_address_1',
            'address_2'  => 'get_shipping_address_2',
            'city'       => 'get_shipping_city',
            'company'    => 'get_shipping_company',
            'country'    => 'get_shipping_country',
            'first_name' => 'get_shipping_first_name',
            'last_name'  => 'get_shipping_last_name',
            'full_name'  => 'get_formatted_shipping_full_name',
            'postcode'   => 'get_shipping_postcode',
            'state'      => 'get_shipping_state',
            'email'      => 'get_shipping_email',
            'phone'      => 'get_shipping_phone',
        ];

        return $mapping[$property] ?? 'get_formatted_billing_address';
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

    public function editing()
    {
        $factory = new Editing\Order\AddressServiceFactory($this->get_address_type());

        return $factory->create($this->get_display_property());
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedValue($this);
    }

}