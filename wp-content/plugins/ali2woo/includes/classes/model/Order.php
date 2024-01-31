<?php

/**
 * Description of Order
 *
 * @author Ali2Woo Team
 */

namespace Ali2Woo;

class Order
{
    private \WC_Order $WocommerceOrder;

    public function __construct(\WC_Order $WocommerceOrder)
    {
        $this->WocommerceOrder = $WocommerceOrder;
    }

    public function getUserInfo(): array
    {
        $order = $this->WocommerceOrder;

        $shipping_country = $order->get_shipping_country();
        $billing_country = $order->get_billing_country();
        if ($order->has_shipping_address()) {
            $state_code = $order->get_shipping_state();
            $city = $order->get_shipping_city();
            $address1 = $order->get_shipping_address_1();
            $address2 = $order->get_shipping_address_2();
            $post_code = $order->get_shipping_postcode();
        } else {
            $state_code = $order->get_billing_state();
            $city = $order->get_billing_city();
            $address1 = $order->get_billing_address_1();
            $address2 = $order->get_billing_address_2();
            $post_code = $order->get_billing_postcode();
        }

        $passport_no = $order->get_meta('_shipping_passport_no');
        $passport_no_date = $order->get_meta('_shipping_passport_no_date');
        $passport_organization = $order->get_meta('_shipping_passport_organization');
        $tax_number = $order->get_meta('_shipping_tax_number');
        $foreigner_passport_no = $order->get_meta('_shipping_foreigner_passport_no');
        $is_foreigner = $order->get_meta('_shipping_is_foreigner');
        $vat_no = $order->get_meta('_shipping_vat_no');
        $tax_company = $order->get_meta('_shipping_tax_company');

        $woo_country = $shipping_country ? $shipping_country : $billing_country;
        $country = ProductShippingMeta::normalize_country($woo_country);
        $country_name = Country::get_country($country);
        $phone_country = Utils::get_phone_country_code($country);
        $phone = $order->get_billing_phone();
        $default_phone_number = get_setting('fulfillment_phone_number', '');
        $default_phone_code = get_setting('fulfillment_phone_code', '');
        if ($phone && (!$default_phone_number /* || $country !== $default_phone_country*/)) {
            $phone = str_replace($phone_country, '', $phone);
            if (!$phone_country && function_exists('WC')) {
                $phone_country = WC()->countries->get_country_calling_code($woo_country);
            }
        } else {
            $phone = $default_phone_number;
            if ($default_phone_code) {
                $phone_country = $default_phone_code;
            }
        }
        $states = Country::get_states($woo_country);
        $name = trim($order->get_formatted_shipping_full_name());
        if (!$name) {
            $name = trim($order->get_formatted_billing_full_name());
        }
        if (!$name) {
            $user = $order->get_user();
            if ($user) {
                if (!empty($user->display_name)) {
                    $name = $user->display_name;
                } elseif (!empty($user->user_nicename)) {
                    $name = $user->user_nicename;
                } elseif (!empty($user->user_login)) {
                    $name = $user->user_login;
                }
            }
        }
        if ($state_code) {
            $state = isset($states[$state_code]) ? $states[$state_code] : $state_code;
        } else {
            $state = $city;
        }

        $billingNumber = $order->get_meta('_billing_number');
        $shippingNumber = $order->get_meta('_shipping_number');

        $streetNumber = $shippingNumber ? $shippingNumber : ($billingNumber ? $billingNumber : '');
        $streetNumber = $streetNumber ? preg_replace("/[^0-9]/", "", $streetNumber) : '';

        $shippingNeighborhood = $order->get_meta('_shipping_neighborhood');

        $result = array(
            'name' => remove_accents($name),
            'phone' => Utils::sanitize_phone_number($phone),
            'street' => remove_accents($address1),
            'address2' => remove_accents($address2),
            'city' => $city,
            'state_code' => remove_accents($state_code),
            'state' => remove_accents($state),
            'country' => remove_accents($country),
            'countryName' => $country_name,
            'postcode' => $post_code,
            'phoneCountry' => $phone_country,
            'cpf' => $this->getOrderCpf($country),
            'rutNo' => $this->getOrderRutNo($country),
            'fromOrderId' => $order->get_id(),
            'passport_no' => $passport_no,
            'passport_no_date' => $passport_no_date,
            'passport_organization' => $passport_organization,
            'tax_number' => $tax_number,
            'foreigner_passport_no' => $foreigner_passport_no,
            'is_foreigner' => $is_foreigner,
            'vat_no' => $vat_no,
            'tax_company' => $tax_company,
            'street_number' => $streetNumber,
            'shipping_neighborhood' => $shippingNeighborhood,
        );

        return apply_filters('a2w_order_user_info', $result, $order);
    }

    private function getOrderCpf(string $country): string
    {
        if ($country !== 'BR') {
            return "";
        }

        $cpfMetaKey = get_setting('fulfillment_cpf_meta_key', '');

        if (!$cpfMetaKey){
            return "";    
        }

        $cpfMeta = $this->WocommerceOrder->get_meta($cpfMetaKey);

        if (!$cpfMeta){
            return "";
        }

        //todo: move this to address fixer
        $cpfMeta = substr(Utils::sanitize_phone_number($cpfMeta), 0, 11);

        return $cpfMeta;  
    }

    private function getOrderRutNo(string $country): string
    {
        if ($country !== 'CL') {
            return "";
        }

        $rutMetaKey = get_setting('fulfillment_rut_meta_key', '');

        if (!$rutMetaKey) {
            return "";
        }

        $rutMeta = $this->WocommerceOrder->get_meta($rutMetaKey);

        if (!$rutMeta) {
            return "";
        }

        //todo: move this to address fixer
        $rutMeta = substr($rutMeta, 0, 12);
        
        return $rutMeta;
    }
}
