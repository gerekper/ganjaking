<?php

namespace ElementPack\Modules\CouponCode;

use ElementPack\Base\Element_Pack_Module_Base;

if ( !defined('ABSPATH') ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {
    public function __construct() {
        parent::__construct();

        add_action('wp_ajax_element_pack_coupon_code', [$this, 'coupon_decryption']);
        add_action('wp_ajax_nopriv_element_pack_coupon_code', [$this, 'coupon_decryption']);
    }

    public function get_name() {
        return 'coupon-code';
    }

    public function get_widgets() {

        $widgets = ['Coupon_Code'];

        return $widgets;
    }

    public function coupon_decryption() {
        $encryption = sanitize_post($_POST['coupon_code']);
        // Store the cipher method
        $ciphering = "AES-128-CTR";
        $options   = 0;
        // Non-NULL Initialization Vector for decryption
        $decryption_iv = '1234567891011121';

        // Store the decryption key
        $decryption_key = "ElementPack";

        // Use openssl_decrypt() function to decrypt the data
        $decryption = openssl_decrypt(
            $encryption,
            $ciphering,
            $decryption_key,
            $options,
            $decryption_iv
        );

        echo esc_html($decryption);

        wp_die();
    }
}
