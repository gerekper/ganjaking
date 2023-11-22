<?php

namespace ElementPack\Includes;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ElementPack_Google_Recaptcha {

    function __construct() {
        add_action('element_pack_google_rechatcha_render', array($this, 'element_pack_google_rechatcha_render'), 10, 3);
        add_filter('element_pack_google_recaptcha_validation', array($this, 'is_valid_captcha'), 10, 1);
    }

    // google captcha hooks

    public function find_element_recursive($elements, $form_id) {

        foreach ( $elements as $element ) {
            if ( $form_id === $element['id'] ) {
                return $element;
            }

            if ( !empty($element['elements']) ) {
                $element = $this->find_element_recursive($element['elements'], $form_id);

                if ( $element ) {
                    return $element;
                }
            }
        }

        return false;
    }

    function is_valid_captcha($recaptchaResponse) {

        $ep_api_settings = get_option('element_pack_api_settings');
        $secretkey       = isset($ep_api_settings['recaptcha_secret_key']) ? $ep_api_settings['recaptcha_secret_key'] : '';

        if ( !empty($recaptchaResponse) && !empty($secretkey) ) {
            $request  = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret=' . $secretkey . '&response=' . esc_textarea($recaptchaResponse) . '&remoteip=' . $_SERVER["REMOTE_ADDR"]);
            $response = wp_remote_retrieve_body($request);

            $result = json_decode($response, TRUE);

            if ( isset($result['success']) && $result['success'] == 1 ) {
                // Captcha ok
                return true;
            } else {
                // Captcha failed;
                return false;
            }
        }
        return false;
    }

    function element_pack_google_rechatcha_render($instance, $callback, $selector = 'button') {
        $ep_api_settings = get_option('element_pack_api_settings');
        if ( !empty($ep_api_settings['recaptcha_site_key']) and !empty($ep_api_settings['recaptcha_secret_key']) ) {
            ?>
            <div data-type="v3" data-action="Form"
                 id="element_pack_recaptcha_<?php echo esc_attr($instance->get_id()) ?>"
                 class="g-recaptcha element-pack-google-recaptcha"
                 data-sitekey="<?php echo esc_attr($ep_api_settings['recaptcha_site_key']) ?>"
                 data-callback="<?php echo esc_attr($callback) ?>" data-size="invisible"></div>
            <?php
        }
    }

}

new ElementPack_Google_Recaptcha();