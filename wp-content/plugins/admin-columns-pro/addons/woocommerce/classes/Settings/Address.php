<?php

namespace ACA\WC\Settings;

use AC;
use AC\View;

/**
 * @since 3.0
 */
class Address extends AC\Settings\Column
    implements AC\Settings\FormatValue
{

    /**
     * @var string
     */
    private $address_property;

    protected function set_name()
    {
        $this->name = 'address_property';
    }

    protected function define_options()
    {
        return [
            'address_property' => '',
        ];
    }

    public function create_view()
    {
        $select = $this->create_element('select')
                       ->set_attribute('data-label', 'update')
                       ->set_attribute('data-refresh', 'column')
                       ->set_options($this->get_display_options());

        return new View([
            'label'   => __('Display', 'codepress-admin-columns'),
            'setting' => $select,
        ]);
    }

    protected function get_display_options()
    {
        return [
            ''           => __('Full Address', 'codepress-admin-columns'),
            'first_name' => __('First Name', 'woocommerce'),
            'last_name'  => __('Last Name', 'woocommerce'),
            'full_name'  => __('Full Name', 'codepress-admin-columns'),
            'company'    => __('Company', 'woocommerce'),
            'address_1'  => sprintf(__('Address line %s', 'codepress-admin-columns'), 1),
            'address_2'  => sprintf(__('Address line %s', 'codepress-admin-columns'), 2),
            'city'       => __('City', 'woocommerce'),
            'postcode'   => __('Postcode', 'woocommerce'),
            'country'    => __('Country', 'woocommerce'),
            'state'      => __('State', 'woocommerce'),
            'email'      => __('Email', 'woocommerce'),
            'phone'      => __('Phone', 'woocommerce'),
        ];
    }

    public function format($value, $original_value)
    {
        switch ($this->get_address_property()) {
            case 'country' :
                $countries = WC()->countries->get_countries();

                if (isset($countries[$value])) {
                    $value = $countries[$value];
                }

                break;
        }

        return $value;
    }

    /**
     * @return string
     */
    public function get_address_property_label()
    {
        $labels = $this->get_display_options();

        if ( ! isset($labels[$this->address_property])) {
            return false;
        }

        return $labels[$this->address_property];
    }

    /**
     * @return string
     */
    public function get_address_property()
    {
        return $this->address_property;
    }

    /**
     * @param string $address_property
     */
    public function set_address_property($address_property)
    {
        $this->address_property = $address_property;
    }

}