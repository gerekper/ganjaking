<?php

namespace ElementPack\Modules\ContactForm;

use ElementPack\Base\Element_Pack_Module_Base;
use ElementPack\Utils;

if ( !defined('ABSPATH') ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

    public function get_name() {
        return 'contact-form';
    }

    public function get_widgets() {

        $widgets = ['Contact_Form'];

        return $widgets;
    }

    public function is_valid_captcha() {

        $ep_api_settings = get_option('element_pack_api_settings');

        if ( isset($_POST['g-recaptcha-response']) and !empty($ep_api_settings['recaptcha_secret_key']) ) {
            $request  = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret=' . $ep_api_settings['recaptcha_secret_key'] . '&response=' . esc_textarea($_POST["g-recaptcha-response"]) . '&remoteip=' . $_SERVER["REMOTE_ADDR"]);
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

    public function contact_form() {

        $email               = get_bloginfo('admin_email');
        $error_empty         = esc_html__('Please fill in all the required fields.', 'bdthemes-element-pack');
        $error_noemail       = esc_html__('Please enter a valid e-mail.', 'bdthemes-element-pack');
        $error_same_as_admin = esc_html__('You can not use this e-mail due to security issues.', 'bdthemes-element-pack');
        $error_spam_email    = esc_html__('You are trying to send e-mail by banned e-mail. Multiple tries can ban you permanently!', 'bdthemes-element-pack');
        $result              = esc_html__('Unknown error! Please check your settings.', 'bdthemes-element-pack');
        $ep_api_settings     = get_option('element_pack_api_settings');
        $api_settings        = get_option('element_pack_api_settings');;

        if ( !empty($ep_api_settings['contact_form_email']) ) {
            $email = $ep_api_settings['contact_form_email'];
        }

        if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

            if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'simpleContactForm')) {
                $result = esc_html__('Security check failed!', 'bdthemes-element-pack');
                echo '<span class="bdt-text-warning">' . esc_html($result) . '</span>';
                wp_die();
            }

            $post_id   = sanitize_text_field($_REQUEST['page_id']);
            $widget_id = sanitize_text_field($_REQUEST['widget_id']);

            $error = false;

            // this part fetches everything that has been POSTed, sanitizes them and lets us use them as $form_data['subject']
            foreach ( $_POST as $field => $value ) {
                if(is_email($value)){
                    $value = sanitize_email( $value );
                } else {
                    $value = sanitize_textarea_field( $value );
                }
                
                $form_data[$field] = strip_tags($value);
            }

            

            foreach ( $form_data as $key => $value ) {
                $value = trim($value);
                if ( empty($value) ) {
                    $error  = true;
                    $result = $error_empty;
                }
            }

            $success = sprintf(esc_html__('Hi, %s. We got your e-mail. We\'ll reply you very soon. Thanks for being with us...', 'bdthemes-element-pack'), $form_data['name']);

            // and if the e-mail is not valid, switch $error to TRUE and set the result text to the shortcode attribute named 'error_noemail'
            if ( !is_email($form_data['email']) ) {
                $error  = true;
                $result = $error_noemail;
            }

            // Stop spamming
            if ( !$error ) {
                $admin_email = get_option('admin_email');
                if ( $admin_email == trim($form_data['email']) || $email == trim($form_data['email']) ) {
                    $error  = true;
                    $result = $error_same_as_admin;
                } else {
                    if ( isset($api_settings['contact_form_spam_email']) ) {
                        $spam_email_list = $api_settings['contact_form_spam_email'];
                        $final_spam_list = explode(',', $spam_email_list);
                        foreach ( $final_spam_list as $spam_email ) {
                            if ( trim($form_data['email']) == trim($spam_email) ) {
                                $error  = true;
                                $result = $error_spam_email;
                                break;
                            }
                        }
                    }
                }
            }
            /** Recaptcha*/


            $result_recaptcha = $this->get_widget_settings($post_id, $widget_id);

            if ( isset($result_recaptcha['show_recaptcha']) && $result_recaptcha['show_recaptcha'] == 'yes' ) {
                if ( !empty($ep_api_settings['recaptcha_site_key']) and !empty($ep_api_settings['recaptcha_secret_key']) ) {
                    if ( !$this->is_valid_captcha() ) {
                        $error  = true;
                        $result = esc_html__("reCAPTCHA is invalid!", "bdthemes-element-pack");
                    }
                }
            }


            $contact_number  = isset($form_data['contact']) ? esc_attr($form_data['contact']) : '';
            $contact_subject = isset($form_data['subject']) ? esc_attr($form_data['subject']) : '';

            // but if $error is still FALSE, put together the POSTed variables and send the e-mail!
            if ( $error == false ) {
                // get the website's name and puts it in front of the subject
                $email_subject = "[" . get_bloginfo('name') . "] " . $contact_subject;
                // get the message from the form and add the IP address of the user below it
                $email_message = $this->message_html($form_data['message'], $form_data['name'], $form_data['email'], $contact_number);
                // set the e-mail headers with the user's name, e-mail address and character encoding
                $headers = "Reply-To: " . $form_data['name'] . " <" . $form_data['email'] . ">\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\n";
                $headers .= "Content-Transfer-Encoding: 8bit\n";
                // send the e-mail with the shortcode attribute named 'email' and the POSTed data
                wp_mail($email, html_entity_decode($email_subject), $email_message, $headers);
                // and set the result text to the shortcode attribute named 'success'
                $result = $success;
                // ...and switch the $sent variable to TRUE
                $sent = true;
            }


            $redirect_url = (isset($form_data['redirect-url']) && !empty($form_data['redirect-url'])) ? esc_url($form_data['redirect-url']) : 'no';
            $is_external  = (isset($form_data['is-external']) && !empty($form_data['is-external'])) ? esc_attr($form_data['is-external']) : 'no';

            $reset_status = (isset($form_data['reset-after-submit']) && ($form_data['reset-after-submit'] == 'yes')) ? 'yes' : 'no';

            if ( $error == false ) {
                echo '<span class="bdt-text-success" data-resetstatus="' . esc_html($reset_status) . '"  data-redirect="' . $redirect_url . '" data-external="' . $is_external . '">' . esc_html($result) . '</span>';
                // wp_redirect( $form_data['redirect_url'] );
            } else {
                echo '<span class="bdt-text-warning">' . esc_html($result) . '</span>';
            }
        }

        die;
    }

    public function message_html($message, $name, $email, $number = '') {

        $fullmsg = "<html lang='en-US'><body style='background-color: #f5f5f5; padding: 35px;'>";
        $fullmsg .= "<div style='max-width: 768px; margin: 0 auto; background-color: #fff; padding: 50px 35px;'>";
        $fullmsg .= nl2br($message);
        $fullmsg .= "<br><br>";
        $fullmsg .= "<b>" . esc_html($name) . "<b><br>";
        $fullmsg .= esc_html($email) . "<br>";
        $fullmsg .= ($number) ? esc_html($number) . "<br>" : "";
        $fullmsg .= "<em>IP: " . Utils::get_client_ip() . "</em>";
        $fullmsg .= "</div>";
        $fullmsg .= "</body></html>";

        return $fullmsg;
    }

    public function __construct() {
        parent::__construct();

        add_action('wp_ajax_element_pack_contact_form', [$this, 'contact_form']);
        add_action('wp_ajax_nopriv_element_pack_contact_form', [$this, 'contact_form']);
    }
}
