<?php

namespace ACA\WC\Column\User;

use ACA\WC\Search;
use ACA\WC\Settings;
use ACP;

class Country extends ACP\Column\Meta
    implements ACP\Export\Exportable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-wc-user-country')
             ->set_label(__('Country', 'woocommerce'))
             ->set_group('woocommerce');
    }

    public function get_meta_key()
    {
        return $this->get_address_type() . '_country';
    }

    public function export()
    {
        return new ACP\Export\Model\Value($this);
    }

    private function get_address_type()
    {
        return $this->get_setting('address_type')->get_value();
    }

    public function register_settings()
    {
        $this->add_setting(new Settings\User\Country($this));
    }

    public function search()
    {
        return new Search\User\Country($this->get_meta_key(), WC()->countries->get_countries());
    }

    public function editing()
    {
        return new ACP\Editing\Service\Basic(
            new ACP\Editing\View\Select(WC()->countries->get_countries()),
            new ACP\Editing\Storage\User\Meta($this->get_meta_key())
        );
    }

}