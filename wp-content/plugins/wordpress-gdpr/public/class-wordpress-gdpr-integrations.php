<?php

class WordPress_GDPR_Integrations extends WordPress_GDPR
{
    protected $plugin_name;
    protected $version;
    protected $options;

    /**
     * Construct GDPR Integrations
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @param   string                         $plugin_name
     * @param   string                         $version
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Init
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @return  boolean
     */
    public function init()
    {
        global $wordpress_gdpr_options;

        $this->options = $wordpress_gdpr_options;

        return true;
    }

    /**
     * WooCommerce
     * Add Privacy Policy Checkbox to Checkout
     * 
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     */
    public function add_privacy_policy_checkbox_to_checkout()
    {
        if(!$this->get_option('integrationsWooCommerce') || !$this->get_option('integrationsWooCommerceCheckoutCheckbox')) {
            return false;
        }

        $text = $this->get_option('integrationsWooCommercePolicyAcceptText');
        $link = get_permalink($this->get_option('integrationsWooCommercePolicyPage'));
        echo '
        <p class="form-row terms wc-privacy-policy">
            <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                <input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="privacy_policy" id="privacy_policy" required> <a href="' . $link . '" target="_blank" class="woocommerce-privacy-policy-link"><span>'
                . $text . ' </a></span> <span class="required">*</span>
            </label>
        </p>';
    }

    /**
     * WooCommerce
     * Add Privacy Policy Checkbox to Registration
     * 
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     */
    public function add_privacy_policy_checkbox_to_registration()
    {
        if(!$this->get_option('integrationsWooCommerce') || !$this->get_option('integrationsWooCommerceRegistrationCheckbox')) {
            return false;
        }

        $text = $this->get_option('integrationsWooCommercePolicyAcceptText');
        $link = get_permalink($this->get_option('integrationsWooCommercePolicyPage'));
        echo '
        <p class="form-row terms wc-privacy-policy">
            <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                <input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="privacy_policy" id="privacy_policy" required> <a href="' . $link . '" target="_blank" class="woocommerce-privacy-policy-link"><span>'
                . $text . ' </a></span> <span class="required">*</span>
            </label>
        </p>';
    }

    /**
     * WooCommerce
     * Add Privacy Policy Checkbox to Product Review Form
     * 
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     */
    public function add_privacy_policy_checkbox_to_review_form($comment_form)
    {
        if(!$this->get_option('integrationsWooCommerce') || $this->get_option('integrationsComments')) {
            return $comment_form;
        }

        $accepted = "";
        if($_COOKIE['wordpress_gdpr_privacy_policy_accepted'] == "true") {
            $accepted = 'checked="checked"';
        }

        $text = $this->get_option('integrationsWooCommercePolicyAcceptText');
        $link = get_permalink($this->get_option('integrationsWooCommercePolicyPage'));
        $comment_form['comment_field'] .= '
        <p class="form-row terms wc-privacy-policy">
            <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                <input ' . $accepted . ' type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="privacy_policy" id="privacy_policy" required> <a href="' . $link . '" target="_blank" class="woocommerce-privacy-policy-link"><span>'
                . $text . ' </a></span> <span class="required">*</span>
            </label>
        </p>';
        return $comment_form;
    }

    /**
     * Add Privacy Policy Checkbox to Comment Form
     * 
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     */
    public function add_privacy_policy_checkbox_to_comment_form($submit_field)
    {
        if(!$this->get_option('integrationsComments')) {
            return $submit_field;
        }

        $accepted = "";
        if(isset($_COOKIE['wordpress_gdpr_privacy_policy_accepted']) && $_COOKIE['wordpress_gdpr_privacy_policy_accepted'] == "true") {
            $accepted = 'checked="checked"';
        }

        $text = $this->get_option('integrationsCommentsPolicyAcceptText');
        $link = get_permalink($this->get_option('integrationsCommentsPolicyPage'));
        $checkbox = '
        <p class="form-row terms wc-privacy-policy">
            <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                <input ' . $accepted . ' type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="privacy_policy" id="privacy_policy" required> <a href="' . $link . '" target="_blank" class="woocommerce-privacy-policy-link"><span>'
                . $text . ' </a></span> <span class="required">*</span>
            </label>
        </p>';
        $submit_field = $checkbox . $submit_field;
        return $submit_field;
    }

    /**
     * WooCommerce
     * Add Privacy Policy Checkbox to BuddyPress Registration
     * 
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     */
    public function add_privacy_policy_checkbox_to_buddypress_registration()
    {
        if(!$this->get_option('integrationsBuddyPress')) {
            return false;
        }

        $accepted = "";
        if($_COOKIE['wordpress_gdpr_privacy_policy_accepted'] == "true") {
            $accepted = 'checked="checked"';
        }

        $text = $this->get_option('integrationsBuddyPressPolicyAcceptText');
        $link = get_permalink($this->get_option('integrationsBuddyPressPolicyPage'));
        $checkbox = '
        <p class="gdpr-buddypress-checkbox-container">
            <label class="buddypress-form__label buddypress-form__label-for-checkbox checkbox">
                <input ' . $accepted . ' type="checkbox" class="buddypress-form__input buddypress-form__input-checkbox input-checkbox" name="privacy_policy" id="privacy_policy" required> <a href="' . $link . '" target="_blank" class="buddypress-privacy-policy-link"><span>'
                . $text . ' </a></span> <span class="required">*</span>
            </label>
        </p>';

        echo $checkbox;
    }

    /**
     * Maybe Override comment registration base
     * 
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     */
    public function maybe_override_comment_registration($value, $option)
    {
        if($this->get_option('integrationsCommentsOnlyRegistered')) {
            $value = $this->get_option('integrationsCommentsOnlyRegistered');
        }
        return $value;
    }

    /**
     * WooCommerce
     * Disable the Guest Checkout
     * 
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     */
    public function maybe_disable_woocommerce_guest_checkout($value, $option)
    {
        if($this->get_option('integrationsWooCommerceDisableGuestCheckout')) {
            $value = 'no';
        }
        return $value;
    }

    /**
     * WooCommerce
     * Validate the Privacy Policy Checkbox
     * 
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     */
    public function validate_privacy_policy_checkbox($data, $errors)
    {
        if(!$this->get_option('integrationsWooCommerce') || !$this->get_option('integrationsWooCommerceCheckoutCheckbox')) {
            return false;
        }

        if(!isset($_POST['privacy_policy'])) {
            $errors->add( 'terms', __( 'You must accept our Privacy Policy.', 'wordpress-gdpr' ) );
        }
    }

    /**
     * WooCommerce
     * Add Privacy Center to the My Account Page
     * 
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     */
    public function add_privacy_center_to_my_account_page( $items ) 
    {
        if(!$this->get_option('integrationsWooCommerce')) {
            return $items;
        }

        $privacyCenterPage = $this->get_option('privacyCenterPage');
        if(empty($privacyCenterPage)) {
            return $items;
        }
        
        $privacyCenterPage = get_post_field('post_name', $privacyCenterPage);

        // Remove the logout menu item.
        $logout = $items['customer-logout'];
        unset( $items['customer-logout'] );

        // Insert your custom endpoint.
        $items[$privacyCenterPage] = __( 'Privacy Center', 'wordpress-gdpr' );

        // Insert back the logout item.
        $items['customer-logout'] = $logout;

        return $items;
    }
     
    /**
     * CF7
     * Save / Not Save Data
     * 
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     */
    public function flamingo_save_data_check($form)
    {
        if(!$this->get_option('integrationsFlamingoDB')) {
            return $form;
        }

        $fieldName = $this->get_option('integrationsFlamingoDBField');
        if(empty($fieldName)) {
            return $form;
        }

        $wpcf7      = WPCF7_ContactForm::get_current();
        $submission = WPCF7_Submission::get_instance();
        
        if ( $submission ) {
            
            $posted_data  = $submission->get_posted_data();

            // CF7 checkbox named opt-in
            $optIn    = $posted_data[$fieldName][0];

            if (!isset( $posted_data[$fieldName])) {
                return $form;
            }

            if ( $optIn ) {

                $email = wpcf7_flamingo_get_value( 'email', $form );
                $name = wpcf7_flamingo_get_value( 'name', $form );
                $subject = wpcf7_flamingo_get_value( 'subject', $form );

                $wpcf7->set_properties( array (  
                    'additional_settings' =>  'do_not_store: false\nflamingo_subject: "'.$subject.'"\nflamingo_name: "'.$name.'"\nflamingo_email: "'.$email.'"' )
                );
        
            } else {
        
                $wpcf7->set_properties(array(
                    'additional_settings' => 'do_not_store: true',
                ));
            }
        }

        return $form;
    }

    /**
     * Mailster
     * Add Privacy Policy Checkbox
     * 
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     */
    public function add_privacy_policy_checkbox_to_mailster_registration($fields, $ID, $form)
    {
        if(!$this->get_option('integrationsMailsterCheckbox')) {
            return $fields;
        }

        $text = $this->get_option('integrationsMailsterPolicyAcceptText');
        $link = get_permalink($this->get_option('integrationsMailsterPolicyPage'));
        if(isset($fields['_submit'])) {
            $checkbox = '
            <p class="gdpr-mailster-checkbox-container">
                <label class="mailster-form__label mailster-form__label-for-checkbox checkbox">
                    <input type="checkbox" class="mailster-form__input mailster-form__input-checkbox input-checkbox" name="privacy_policy" id="privacy_policy" required> <a href="' . $link . '" target="_blank" class="mailster-privacy-policy-link"><span>'
                    . $text . ' </a></span> <span class="required">*</span>
                </label>
            </p>';

            $fields['_submit'] = $checkbox . $fields['_submit'];
        }

        return $fields;
    }

    /**
     * PixelYourSite
     * Disable PixelYourSite
     * 
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     */
    public function maybe_disable_pixelyoursite()
    {
        if(!$this->get_option('integrationsPixelYourSite')) {
            return false;
        }

        $args = array(
            'post_type' => 'gdpr_service',
            'posts_per_page' => -1,
        );
        $services = get_posts($args);

        $tmp = array();
        foreach ($services as $service) {
            $fb_true = get_post_meta($service->ID, 'pixelyoursite' , true);
            if($fb_true !== "1") {
                continue;
            }
            $pixelyoursite_service = $service->ID;
        }

        $firstTimeAllowAllCookies = $this->get_option('firstTimeAllowAllCookies');
        if(!isset($_COOKIE['wordpress_gdpr_cookies_allowed']) && $firstTimeAllowAllCookies) {
            return false;
        }

        $temp = explode(',', $_COOKIE["wordpress_gdpr_allowed_services"]);
        $allowed_service_cookies = array_combine($temp, $temp);
        if(!in_array($pixelyoursite_service, $allowed_service_cookies)) {
            return true;
        }

        return false;
    }

    /**
     * PolyLang
     * Post Type Translation support
     * 
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     */
    public function add_cpt_to_pll( $post_types, $is_settings ) {
        $post_types['gdpr_service'] = 'gdpr_service';
       
        return $post_types;
    }

    /**
     * PolyLang
     * Taxonomy Translation support
     * 
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     */
    public function add_tax_to_pll( $taxonomies, $is_settings ) {
        $taxonomies['gdpr_service_categories'] = 'gdpr_service_categories';
       
        return $taxonomies;
    }

    /**
     * Gravity Forms
     * Remove IP from saving 
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @param   [type]                       $ip [description]
     * @return  [type]                           [description]
     */
    public function remove_gform_ip_saving($ip)
    {
        if(!$this->get_option('integrationsGravityForms')) {
            return $ip;
        }

        return 'GDPR-NO-IP';
    }
}