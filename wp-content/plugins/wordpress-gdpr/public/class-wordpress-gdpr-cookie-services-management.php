<?php

class WordPress_GDPR_Cookie_Services_Management extends WordPress_GDPR
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
     * Get All Services
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    public function get_services()
    {
        $args = array(
            'post_type' => 'gdpr_service',
            'posts_per_page' => -1,
        );
        $services = get_posts($args);

        $tmp = array();
        foreach ($services as $service) {
            $tmp[$service->ID] = array(
                'id' => $service->ID,
                'name' => $service->post_title,
                'cookies' => get_post_meta($service->ID, 'cookies' , true),
                'deactivatable' => get_post_meta($service->ID, 'deactivatable' , true),
                'head_script' => get_post_meta($service->ID, 'head_script' , true),
                'body_script' => get_post_meta($service->ID, 'body_script' , true),
                'adsense' => get_post_meta($service->ID, 'adsense' , true),
                'defaultEnabled' => get_post_meta($service->ID, 'defaultEnabled' , true),
            );
        }
        $services = $tmp;

        $services = apply_filters('wordpress_gdpr_services', $services);

        return $services;
    }

    /**
     * Allow All Cookies
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    public function allow_cookies($firstTime = false)
    {
        $domain = $this->get_option('domainName');

        $cookieLifetime = $this->get_option('cookieLifetime'); 
        $cookieLifetime = time() + (60*60*24*$cookieLifetime);

        $services = $this->get_services();
        $allowed_service_cookies = array();
        foreach ($services as $serviceID => $service) {
            $allowed_service_cookies[$serviceID] = "true";
        }

        setcookie('wordpress_gdpr_allowed_services', implode(',', array_keys($allowed_service_cookies)), $cookieLifetime, '/');
        if(!$firstTime) {
            setcookie('wordpress_gdpr_cookies_allowed', 'true', $cookieLifetime, '/');
        }
        setcookie('wordpress_gdpr_cookies_declined', 'false', $cookieLifetime, '/');

        $_COOKIE['wordpress_gdpr_cookies_declined'] = 'false';

        do_action('wordpress_gdpr_allow_cookies');
    }

    /**
     * Decline All Cookies
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    public function decline_cookies()
    {
        $cookieLifetime = $this->get_option('cookieLifetime'); 
        $cookieLifetime = time() + (60*60*24*$cookieLifetime);
    
        $services = $this->get_services();

        setcookie('wordpress_gdpr_allowed_services', '', $cookieLifetime, '/');
        setcookie('wordpress_gdpr_cookies_allowed', 'false', $cookieLifetime, '/');
        setcookie('wordpress_gdpr_cookies_declined', 'true', $cookieLifetime, '/');

        $allowed_cookies = array(
            'wordpress_gdpr_allowed_services',
            'wordpress_gdpr_cookies_allowed',
            'wordpress_gdpr_cookies_declined',
            'wordpress_test_cookie',
            'wordpress_logged_in_',
            'wordpress_sec',
            'wordpress_gdpr_terms_conditions_accepted',
            'wordpress_gdpr_privacy_policy_accepted',
            'quform',
            'woocommerce_cart_hash',
            'woocommerce_items_in_cart',
            'woocommerce_session',
            'wordpress_gdpr_first_time',
            'wordpress_gdpr_first_time_url'
        );

        foreach ($services as $service) {
            if($service['deactivatable'] !== "0" || empty($service['cookies'])) {
                continue;
            }

            $cookies = explode(',', $service['cookies']);
            $allowed_cookies =  array_merge($allowed_cookies, $cookies);
        }

        $allowed_cookies = apply_filters('wordpress_gdpr_necessary_cookies', $allowed_cookies);
        $this->delete_non_allowed_cookies($allowed_cookies);
        
        do_action('wordpress_gdpr_decline_cookies', array('wordpress_gdpr_cookies_declined' => 'true') );
    }

    /**
     * Check Single Privacy Setting
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    public function check_privacy_setting() 
    {
        $setting = $_POST['setting'];
        $allowed = isset($_COOKIE[$setting]);
        $declined = false;
        $firstTime = false;

        $cookieLifetime = $this->get_option('cookieLifetime'); 
        $cookieLifetime = time() + (60*60*24*$cookieLifetime);

        if(isset($_COOKIE['wordpress_gdpr_cookies_declined']) && ($_COOKIE['wordpress_gdpr_cookies_declined'] == "true")) {
            $declined = true;
        }

        if(current_user_can('administrator')) {
            $allowed = true;
        }

        $loggedInAllowAllCookies = $this->get_option('loggedInAllowAllCookies');
        if($loggedInAllowAllCookies && is_user_logged_in()) {
            $allowed = true;
        }

        $continueVisitingAllowAllCookies = $this->get_option('continueVisitingAllowAllCookies');
        if($continueVisitingAllowAllCookies && !isset($_COOKIE['wordpress_gdpr_cookies_allowed']) && !isset($_COOKIE['wordpress_gdpr_cookies_declined']) ) {

            if(!isset($_COOKIE['wordpress_gdpr_first_time_url'])) {
                setcookie('wordpress_gdpr_first_time_url', $_SERVER['HTTP_REFERER'], $cookieLifetime, '/');
            }

            if(!isset($_COOKIE['wordpress_gdpr_first_time'])) {
                setcookie('wordpress_gdpr_first_time', 'true', $cookieLifetime, '/');
            } elseif(isset($_COOKIE['wordpress_gdpr_first_time']) && ($_COOKIE['wordpress_gdpr_first_time'] == "true")) {

                $current_page_id = isset($_POST['current_page_id']) && !empty($_POST['current_page_id']) ? intval( $_POST['current_page_id']) : '';
                if( (!empty($current_page_id) && ($current_page_id == $this->get_option('privacyPolicyPage'))) || 
                    (!empty($_COOKIE['wordpress_gdpr_first_time_url']) && ($_COOKIE['wordpress_gdpr_first_time_url'] == $_SERVER['HTTP_REFERER'])) ) {
                } else {
                    $this->allow_cookies();
                    $allowed = true;
                    setcookie('wordpress_gdpr_first_time', 'false', $cookieLifetime, '/');
                }
            }
        }

        $firstTimeAllowAllCookies = $this->get_option('firstTimeAllowAllCookies');
        if(!isset($_COOKIE['wordpress_gdpr_cookies_allowed']) && $firstTimeAllowAllCookies) {
            $this->allow_cookies(true);
            $allowed = false;
            $firstTime = true;
        }

        echo json_encode(array('allowed' => $allowed, 'declined' => $declined, 'firstTime' => $firstTime));
        wp_die();
    }

    public function check_privacy_settings($settings = array()) 
    {
        if(empty($settings) && isset($_POST['settings']) && is_array($_POST['settings'])) {
            $settings = $_POST['settings'];
        }

        if(empty($settings) || !is_array($settings)) {
            return false;
        }

        $services = $this->get_services();

        $return = array();
 
        if(!isset($_COOKIE["wordpress_gdpr_allowed_services"]) || empty($_COOKIE["wordpress_gdpr_allowed_services"])) {
            $allowed_service_cookies = array();
        } else {
            $temp = explode(',', $_COOKIE["wordpress_gdpr_allowed_services"]);
            $allowed_service_cookies = array_combine($temp, $temp);
        }

        $allowed_cookies = array(
            'wordpress_gdpr_allowed_services',
            'wordpress_gdpr_cookies_allowed',
            'wordpress_gdpr_cookies_declined',
            'wordpress_test_cookie',
            'wordpress_logged_in_',
            'wordpress_sec',
            'wordpress_gdpr_terms_conditions_accepted',
            'wordpress_gdpr_privacy_policy_accepted',
            'quform',
            'woocommerce_cart_hash',
            'woocommerce_items_in_cart',
            'woocommerce_session',
            'wordpress_gdpr_first_time',
            'wordpress_gdpr_first_time_url'
        );

        foreach ($settings as $setting) {
            
            $service = $services[$setting];
            $allowed = isset($allowed_service_cookies[$setting]) ? true : false;

            if(!isset($_COOKIE['wordpress_gdpr_cookies_allowed']) && ($service['defaultEnabled'] == "1")) {
                $allowed = true;
            }

            // Allow for admins & logged in if enabled
            if(current_user_can('administrator') || ($this->get_option('loggedInAllowAllCookies') && is_user_logged_in())) {
                $allowed = true;
            }

            if($service['deactivatable'] == "0") {
                $allowed = true;
            }

            $head = '';
            $body = '';
            $adsense = $service['adsense'];

            if($allowed) {

                $head = $service['head_script'];
                $body = $service['body_script'];

                $cookies = explode(',', $service['cookies']);
                if(!empty($cookies)) {
                    $allowed_cookies =  array_merge($allowed_cookies, $cookies);
                }
            }

            $return[$setting] = array(
                'allowed' => $allowed,
                'head' => $head,
                'body' => $body,
                'adsense' => $adsense,
            );
        }

        $allowed_cookies = apply_filters('wordpress_gdpr_necessary_cookies', $allowed_cookies);
        if($this->get_option('useCookieWhitelist') || (isset($_COOKIE['wordpress_gdpr_cookies_declined']) && ($_COOKIE['wordpress_gdpr_cookies_declined'] == "true"))) {
            $this->delete_non_allowed_cookies($allowed_cookies);
        }

        echo json_encode($return);
        wp_die();
    }

    public function update_privacy_setting()
    {
        $setting = $_POST['setting'];
        $checked = $_POST['checked'];

        $cookieLifetime = $this->get_option('cookieLifetime'); 
        $cookieLifetime = time() + (60*60*24*$cookieLifetime);

        if(!empty($_COOKIE["wordpress_gdpr_allowed_services"])) {
            $temp = explode(',', $_COOKIE["wordpress_gdpr_allowed_services"]);
            $allowed_service_cookies = array_combine($temp, $temp);
        } else {
            $allowed_service_cookies = array();
        }

        if(!isset($_COOKIE['wordpress_gdpr_cookies_allowed']) && !isset($_COOKIE['wordpress_gdpr_cookies_declined'])) {
            setcookie('wordpress_gdpr_cookies_allowed', 'true', $cookieLifetime, '/');
            setcookie('wordpress_gdpr_cookies_declined', 'false', $cookieLifetime, '/');
        }

        do_action('wordpress_gdpr_update_cookie', array($setting => $checked));

        if($checked == "false") {
            unset($allowed_service_cookies[$setting]);
        } else {
            $allowed_service_cookies[$setting] = $checked;
        }
        
        setcookie('wordpress_gdpr_allowed_services', implode(',', array_keys($allowed_service_cookies)), $cookieLifetime, '/');
        $_COOKIE["wordpress_gdpr_allowed_services"] = implode(',', array_keys($allowed_service_cookies));

        $this->check_privacy_settings(array($setting));
    }

    public function delete_cookies($cookies = array(), $domain = "")
    {
        $past = time() - 3600;
        if(empty($domain)) {
            $domain = $this->get_option('domainName');
        }

        foreach ($cookies as $cookie) {
            if(isset($_COOKIE[$cookie])) {
                setcookie($cookie, 'false', $past, '/');
                setcookie($cookie, 'false', $past, '/', $domain);
                setcookie($cookie, 'false', $past);
                continue;
            }
        } 
    }

    public function delete_non_allowed_cookies($allowed_cookies)
    {
        $past = time() - 3600;
        $domain = $this->get_option('domainName');
        
        foreach ( $_COOKIE as $key => $value ) {
            if(!empty($allowed_cookies)) {
                foreach ($allowed_cookies as $allowed_cookie) {
                    $allowed_cookie = trim($allowed_cookie);
                    if (!empty($allowed_cookie) && strpos($key, $allowed_cookie) !== FALSE) { 
                        continue 2;
                    }
                }
            }

            if(is_array($value)) {
                continue;
            }

            setcookie( $key, $value, $past, '/');
            setcookie( $key, $value, $past, '/', $domain);
            setcookie( $key, $value, $past);
        }
    }

    public function update_privacy_policy_term()
    {
        $setting = $_POST['setting'];
        $checked = $_POST['checked'];

        if($setting !== "wordpress_gdpr_terms_conditions_accepted" && $setting !== "wordpress_gdpr_privacy_policy_accepted") {
            return false;
        }

        $cookieLifetime = $this->get_option('cookieLifetime'); 
        $cookieLifetime = time() + (60*60*24*$cookieLifetime);
        setcookie($setting, $checked, $cookieLifetime, '/');
    }
}