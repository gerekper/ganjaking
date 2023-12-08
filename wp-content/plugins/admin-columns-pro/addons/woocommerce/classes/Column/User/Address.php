<?php

namespace ACA\WC\Column\User;

use ACA\WC\Settings;
use ACP;

class Address extends ACP\Column\Meta
    implements ACP\Export\Exportable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-wc-user-address')
             ->set_label(__('Address', 'woocommerce'))
             ->set_group('woocommerce');
    }

    public function get_raw_value($id)
    {
        $meta_key = $this->get_meta_key();

        if ( ! $meta_key) {
            return wc_get_account_formatted_address($this->get_address_type(), $id);
        }

        return parent::get_raw_value($id);
    }

    public function get_meta_key()
    {
        if ( ! $this->get_address_property()) {
            return false;
        }

        return $this->get_address_type() . '_' . $this->get_address_property();
    }

    public function export()
    {
        return new ACP\Export\Model\Value($this);
    }

    public function search()
    {
        if ( ! $this->get_meta_key()) {
            return false;
        }

        return new ACP\Search\Comparison\Meta\Text($this->get_meta_key());
    }

    public function editing()
    {
        switch ($this->get_address_property()) {
            case '' :
                return false;

            case 'country' :
                $options = array_merge(['' => __('None', 'codepress-admin-columns')], WC()->countries->get_countries());

                return new ACP\Editing\Service\Basic(
                    new ACP\Editing\View\Select($options),
                    new ACP\Editing\Storage\User\Meta($this->get_meta_key())
                );

            default :
                return new ACP\Editing\Service\Basic(
                    (new ACP\Editing\View\Text())->set_placeholder(
                        $this->get_setting_address_property()->get_address_property_label()
                    ),
                    new ACP\Editing\Storage\User\Meta($this->get_meta_key())
                );
        }
    }

    /**
     * @return string e.g. billing or shipping
     */
    private function get_address_type(): string
    {
        $setting = $this->get_setting('address_type');

        return $setting instanceof Settings\User\AddressType
            ? $setting->get_address_type()
            : '';
    }

    private function get_address_property(): string
    {
        $setting = $this->get_setting_address_property();

        return $setting
            ? $setting->get_address_property()
            : '';
    }

    public function get_setting_address_property(): ?Settings\Address
    {
        $setting = $this->get_setting('address_property');

        if ( ! $setting instanceof Settings\Address) {
            return null;
        }

        return $setting;
    }

    public function register_settings()
    {
        $this->add_setting(new Settings\User\AddressType($this));
    }

}