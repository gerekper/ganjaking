<?php

class WordPress_GDPR_Consent_Log extends WordPress_GDPR
{
    protected $plugin_name;
    protected $version;
    protected $options;

    /**
     * Store Locator Plugin Construct
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
     * Init the Public
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

        if (!$this->get_option('enable')) {
            return false;
        }

        return true;
    }

    /**
     * Update Cookies Consent
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @param   [type]                       $login [description]
     * @param   [type]                       $user  [description]
     * @return  [type]                              [description]
     */
    public function update_consent_log($cookies) 
    {
        $user_id = get_current_user_id();
        if(!$user_id) {
            return false;
        }

        $current_cookies = get_user_meta( $user_id, 'wordpress_gdpr_consents', true );
        if(!$current_cookies || empty($current_cookies) || !is_array($current_cookies)) {
            update_user_meta( $user_id, 'wordpress_gdpr_consents', $cookies );
        } else {
            $cookies = array_merge($current_cookies, $cookies);
            update_user_meta( $user_id, 'wordpress_gdpr_consents', $cookies );
        }
    }
}