<?php

namespace ACA\WC\Column\Order\Address;

use AC;
use ACA\WC\ConditionalFormat\FilteredHtmlIntegerFormatterTrait;
use ACA\WC\Editing;
use ACA\WC\Search;
use ACA\WC\Settings;
use ACA\WC\Sorting;
use ACA\WC\Type\AddressType;
use ACP;

class BillingAddress extends AC\Column implements ACP\Search\Searchable, ACP\Editing\Editable, ACP\Export\Exportable,
                                                  ACP\ConditionalFormat\Formattable, ACP\Sorting\Sortable
{

    use FilteredHtmlIntegerFormatterTrait;

    public function __construct()
    {
        $this->set_type('column-order_billing_address')
             ->set_label(__('Billing Address', 'codepress-admin-columns'))
             ->set_group('woocommerce');
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

        return $setting instanceof Settings\Address\Billing
            ? $setting->get_address_property()
            : '';
    }

    private function get_address_map($property): string
    {
        $mapping = [
            'address_1'  => 'get_billing_address_1',
            'address_2'  => 'get_billing_address_2',
            'city'       => 'get_billing_city',
            'company'    => 'get_billing_company',
            'country'    => 'get_billing_country',
            'first_name' => 'get_billing_first_name',
            'last_name'  => 'get_billing_last_name',
            'full_name'  => 'get_formatted_billing_full_name',
            'postcode'   => 'get_billing_postcode',
            'state'      => 'get_billing_state',
            'email'      => 'get_billing_email',
            'phone'      => 'get_billing_phone',
        ];

        return array_key_exists($property, $mapping)
            ? $mapping[$property]
            : 'get_formatted_billing_address';
    }

    protected function register_settings()
    {
        parent::register_settings();

        $this->add_setting(new Settings\Address\Billing($this));
    }

    private function get_address_type(): AddressType
    {
        return new AddressType(AddressType::BILLING);
    }

    public function search()
    {
        return (new Search\Order\AddressesComparisonFactory($this->get_address_type()))->create(
            $this->get_display_property()
        );
    }

    public function sorting()
    {
        switch ($this->get_display_property()) {
            case 'address_1':
                return new Sorting\Order\AddressField('address_1', $this->get_address_type());
            case 'address_2':
                return new Sorting\Order\AddressField('address_2', $this->get_address_type());
            case 'city':
                return new Sorting\Order\AddressField('city', $this->get_address_type());
            case 'company':
                return new Sorting\Order\AddressField('company', $this->get_address_type());
            case 'country':
                return new Sorting\Order\AddressField('country', $this->get_address_type());
            case 'first_name':
                return new Sorting\Order\AddressField('first_name', $this->get_address_type());
            case 'last_name':
                return new Sorting\Order\AddressField('last_name', $this->get_address_type());
            case 'postcode':
                return new Sorting\Order\AddressField('postcode', $this->get_address_type());
            case 'phone':
                return new Sorting\Order\AddressField('phone', $this->get_address_type());
            case 'state':
                return new Sorting\Order\AddressField('state', $this->get_address_type());
            case 'full_name':
                return new Sorting\Order\FullNameAddress($this->get_address_type());
            default:
                return false;
        }
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