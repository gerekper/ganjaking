<?php

class WordPress_GDPR_Public extends WordPress_GDPR
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
     * Enqueue Styles
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @return  boolean
     */
    public function enqueue_styles()
    {
        global $wordpress_gdpr_options;

        $this->options = $wordpress_gdpr_options;

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__).'css/wordpress-gdpr-public.css', array(), $this->version, 'all');
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', array(), '4.7.0', 'all');

        $customCSS = $this->get_option('customCSS');
        if(!empty($customCSS)) {
            file_put_contents(dirname(__FILE__)  . '/css/wordpress-gdpr-custom.css', $customCSS);
            wp_enqueue_style($this->plugin_name.'-custom', plugin_dir_url(__FILE__) . 'css/wordpress-gdpr-custom.css', array(), $this->version, 'all');
        }

        return true;
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @return  boolean
     */
    public function enqueue_scripts()
    {
        global $wordpress_gdpr_options;

        $this->options = $wordpress_gdpr_options;

        wp_enqueue_script($this->plugin_name.'-public', plugin_dir_url(__FILE__).'js/wordpress-gdpr-public.js', array('jquery'), $this->version, true);
        
        $forJS = array(
            'ajaxURL' => admin_url('admin-ajax.php'),
            'cookieLifetime' => $this->get_option('cookieLifetime'),
            'geoIP' => $this->get_option('geoIP'),
            'popupExcludePages' => $this->get_option('popupExcludePages'),
        );

        $checks = array('wordpress_gdpr_cookies_allowed');

        if($this->get_option('integrationsGoogleAnalytics')) {
            $checks[] = 'wordpress_gdpr_analytics_allowed';
        }

        if($this->get_option('integrationsGoogleAdwords')) {
            $checks[] = 'wordpress_gdpr_adwords_allowed';
        }

        if($this->get_option('integrationsGoogleTagManager')) {
            $checks[] = 'wordpress_gdpr_tag_manager_allowed';
        }

        if($this->get_option('integrationsHotJar')) {
            $checks[] = 'wordpress_gdpr_hot_jar_allowed';
        }

        if($this->get_option('integrationsFacebook')) {
            $checks[] = 'wordpress_gdpr_facebook_allowed';
        }    

        if($this->get_option('integrationsPiwik')) {
            $checks[] = 'wordpress_gdpr_piwik_allowed';
        }   

        if($this->get_option('integrationsAdsense')) {
            $checks[] = 'wordpress_gdpr_adsense_allowed';
        }   

        if($this->get_option('integrationsCustom')) {
            $checks[] = 'wordpress_gdpr_custom_allowed';
        }

        if($this->get_option('privacyPolicyAccept')) {
            $checks[] = 'wordpress_gdpr_privacy_policy_accepted';
        }

        if($this->get_option('termsConditionsAccept')) {
            $checks[] = 'wordpress_gdpr_terms_conditions_accepted';
        }

        $customIntegrations = $this->get_option('integrationsCustoms');
        if (!empty($customIntegrations)) {
            foreach ($customIntegrations as $key => $customIntegration) {
                if(empty($customIntegration['title']) || empty($customIntegration['description'])) {
                    continue;
                }
                $checks[] = 'wordpress_gdpr_custom_' . $key . '_allowed';
            }
        }

        $forJS['checks'] = $checks;

        wp_localize_script($this->plugin_name.'-public', 'gdpr_options', $forJS);

        return true;
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

    public function add_gdpr_popup()
    {
        $popupEnable = $this->get_option('popupEnable');
        if(!$popupEnable) {
            return false;
        }

        $popupPrivacySettingsModal = $this->get_option('popupPrivacySettingsModal');
        $privacySettings = $this->get_privacy_settings();

        $popupText = $this->get_option('popupText');
        $popupTextAgree = $this->get_option('popupTextAgree');
        $popupTextCookiePolicy = $this->get_option('popupTextCookiePolicy');
        $popupTextDecline = $this->get_option('popupTextDecline');
        $popupTextPrivacySettings = $this->get_option('popupTextPrivacySettings');
        $popupTextPrivacyCenter = $this->get_option('popupTextPrivacyCenter');

        $popupStyle = $this->get_option('popupStyle');
        $popupPosition = $this->get_option('popupPosition');
        $popupBackgroundColor = $this->get_option('popupBackgroundColor');
        $popupTextColor = $this->get_option('popupTextColor');

        $privacyCenterPage = $this->get_option('privacyCenterPage');
        $privacySettingsPage = $this->get_option('privacySettingsPage');
        $cookiePolicyPage = $this->get_option('cookiePolicyPage');

        $popupCloseIcon = $this->get_option('popupCloseIcon');
        $popupCloseIconColor = $this->get_option('popupCloseIconColor');
        $popupCloseIconBackgroundColor = $this->get_option('popupCloseIconBackgroundColor');

        $renderd = false;
        ?>
        <div class="wordpress-gdpr-popup <?php echo $popupStyle . ' ' . $popupPosition ?>" 
            style="background-color: <?php echo $popupBackgroundColor ?>; color: <?php echo $popupTextColor ?>;">
            <?php if($popupPrivacySettingsModal && $popupPosition == "wordpress-gdpr-popup-bottom" && $popupStyle !== "wordpress-gdpr-popup-overlay") { 
                $renderd = true;
                ?>
                <div class="wordpress-gdpr-popup-privacy-settings-modal" 
                    style="background-color: <?php echo $popupBackgroundColor ?>; color: <?php echo $popupTextColor ?>;">
                    <div class="wordpress-gdpr-popup-privacy-settings-title"><?php echo $popupTextPrivacySettings ?></div>
                    <div class="wordpress-gdpr-popup-privacy-settings">
                        <table class="table wordpress-gdpr-popup-privacy-settings-table">
                            <thead>
                                <tr>
                                    <th><?php echo __('Name', 'wordpress-gdpr')?></th>
                                    <!-- <th><?php echo __('Reason', 'wordpress-gdpr')?></th> -->
                                    <th><?php echo __('Enabled', 'wordpress-gdpr')?></th>
                                </tr>
                            </thead>
                            <?php foreach ($privacySettings as $privacySetting) { ?>
                                <tr>
                                    <td class="wordpress-gdpr-popup-privacy-settings-table-title">
                                        <b><?php echo $privacySetting['title'] ?></b><br>
                                        <span class="wordpress-gdpr-popup-privacy-settings-table-reason"><?php echo $privacySetting['reason'] ?></span>
                                    </td>
                                    <!-- <td class="wordpress-gdpr-popup-privacy-settings-table-reason"><?php echo $privacySetting['reason'] ?></td> -->
                                    <td class="wordpress-gdpr-popup-privacy-settings-table-checkbox"><?php echo $privacySetting['checkbox'] ?></td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            <?php } ?>
            <?php if($popupStyle == "wordpress-gdpr-popup-overlay") {  ?>
                <div class="wordpress-gdpr-popup-overlay-backdrop"></div>
                <div class="wordpress-gdpr-popup-container" style="background-color: <?php echo $popupBackgroundColor ?>; color: <?php echo $popupTextColor ?>;">
            <?php } else { ?>
                <div class="wordpress-gdpr-popup-container">
            <?php } ?>
                <a href="#" id="wordpress-gdpr-popup-close" class="wordpress-gdpr-popup-close" style="background-color: <?php echo $popupCloseIconBackgroundColor ?>;">
                    <i style="color: <?php echo $popupCloseIconColor ?>;" class="<?php echo $popupCloseIcon ?>"></i>
                </a>
                <div class="wordpress-gdpr-popup-text"><?php echo wpautop($popupText) ?></div>
                <div class="wordpress-gdpr-popup-actions">

                    <?php if(!empty($popupTextAgree)) { ?>
                        <a href="#" class="wordpress-gdpr-popup-agree"><?php echo $popupTextAgree ?></a>
                    <?php } ?>

                    <?php if(!empty($popupTextDecline)) { ?>
                        <a href="#" class="wordpress-gdpr-popup-decline"><?php echo $popupTextDecline ?></a>
                    <?php } ?>

                    <?php if(!empty($popupTextPrivacyCenter) && !empty($privacyCenterPage)) { ?>
                        <a href="<?php echo get_permalink($privacyCenterPage) ?>" class="wordpress-gdpr-popup-privacy-center"><?php echo $popupTextPrivacyCenter ?></a>
                    <?php } ?>

                    <?php if(!empty($popupTextPrivacySettings) && !empty($privacySettingsPage) && !$popupPrivacySettingsModal) { ?>
                        <a href="<?php echo get_permalink($privacySettingsPage) ?>" class="wordpress-gdpr-popup-privacy-settings-text"><?php echo $popupTextPrivacySettings ?></a>
                    <?php } ?>

                    <?php if(!empty($cookiePolicyPage) && !empty($popupTextCookiePolicy)) { ?>
                        <a href="<?php echo get_permalink($cookiePolicyPage) ?>" class="wordpress-gdpr-popup-read-more"><?php echo $popupTextCookiePolicy ?></a>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php if($popupPrivacySettingsModal && !$renderd) { ?>
        <div class="wordpress-gdpr-popup-privacy-settings-modal wordpress-gdpr-popup-privacy-settings-modal-bottom" 
            style="background-color: <?php echo $popupBackgroundColor ?>; color: <?php echo $popupTextColor ?>;">
            <div class="wordpress-gdpr-popup-privacy-settings-title"><?php echo $popupTextPrivacySettings ?></div>
            <div class="wordpress-gdpr-popup-privacy-settings">
                <table class="table wordpress-gdpr-popup-privacy-settings-table">
                    <thead>
                        <tr>
                            <th><?php echo __('Name', 'wordpress-gdpr')?></th>
                            <!-- <th><?php echo __('Reason', 'wordpress-gdpr')?></th> -->
                            <th><?php echo __('Enabled', 'wordpress-gdpr')?></th>
                        </tr>
                    </thead>
                    <?php foreach ($privacySettings as $privacySetting) { ?>
                        <tr>
                            <td class="wordpress-gdpr-popup-privacy-settings-table-title">
                                <b><?php echo $privacySetting['title'] ?></b><br>
                                <span class="wordpress-gdpr-popup-privacy-settings-table-reason"><?php echo $privacySetting['reason'] ?></span>
                            </td>
                            <!-- <td class="wordpress-gdpr-popup-privacy-settings-table-reason"><?php echo $privacySetting['reason'] ?></td> -->
                            <td class="wordpress-gdpr-popup-privacy-settings-table-checkbox"><?php echo $privacySetting['checkbox'] ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
        <?php } ?>
        <?php
    }

    public function get_privacy_center($atts)
    {
        $btnText = __('Learn More', 'wordpress-gdpr');
        
        ob_start();

        $this->check_confirmation();

        echo '<div class="wordpress-gdpr-privacy-center">';
            echo '<div class="wordpress-gdpr-privacy-center-items">';

            $privacyCenterItems = array(
                'contactDPO' => array(
                    'icon' => 'fa-user-circle-o',
                    'title' => __('Contact DPO', 'wordpress-gdpr'),
                    'desc' => __('Contact our Data Protection Officer.', 'wordpress-gdpr'),
                ),
                'cookiePolicy' => array(
                    'icon' => ' fa-list-alt',
                    'title' => __('Cookie Policy', 'wordpress-gdpr'),
                    'desc' => __('Read more about our Cookie Policy.', 'wordpress-gdpr'),
                ),
                'DMCA' => array(
                    'icon' => 'fa-gavel',
                    'title' => __('DMCA', 'wordpress-gdpr'),
                    'desc' => __('Report a Copyright violation.', 'wordpress-gdpr'),
                ),
                'dataRectification' => array(
                    'icon' => 'fa-pencil',
                    'title' => __('Data Rectification', 'wordpress-gdpr'),
                    'desc' => __('Contact us for data change request.', 'wordpress-gdpr'),
                ),
                'disclaimer' => array(
                    'icon' => 'fa-gavel',
                    'title' => __('Disclaimer', 'wordpress-gdpr'),
                    'desc' => __('Learn more about our liabilities.', 'wordpress-gdpr'),
                ),
                'forgetMe' => array(
                    'icon' => 'fa-eraser',
                    'title' => __('Forget Me', 'wordpress-gdpr'),
                    'desc' => __('Submit a request to erase your private data.', 'wordpress-gdpr'),
                ),
                'imprint' => array(
                    'icon' => 'fa-file-text-o',
                    'title' => __('Imprint', 'wordpress-gdpr'),
                    'desc' => __('Learn about our ownership and authorship.', 'wordpress-gdpr'),
                ),
                'mediaCredits' => array(
                    'icon' => 'fa-file-image-o',
                    'title' => __('Media Credits', 'wordpress-gdpr'),
                    'desc' => __('Get to know the Credits of our Media.', 'wordpress-gdpr'),
                ),
                'requestData' => array(
                    'icon' => 'fa-database',
                    'title' => __('Request Data', 'wordpress-gdpr'),
                    'desc' => __('Submit a request to get a copy of all your data stored.', 'wordpress-gdpr'),
                ),
                'privacyPolicy' => array(
                    'icon' => 'fa-file-text',
                    'title' => __('Privacy Policy', 'wordpress-gdpr'),
                    'desc' => __('Read about our Privacy Policy here.', 'wordpress-gdpr'),
                ),
                'privacySettings' => array(
                    'icon' => 'fa-user-secret',
                    'title' => __('Privacy Settings', 'wordpress-gdpr'),
                    'desc' => __('Configure your personal privacy settings here.', 'wordpress-gdpr'),
                ),
                'termsConditions' => array(
                    'icon' => 'fa-align-left',
                    'title' => __('Terms & Conditions', 'wordpress-gdpr'),
                    'desc' => __('Read more about the Terms & Conditions of our website.', 'wordpress-gdpr'),
                ),
                'unsubscribe' => array(
                    'icon' => 'fa-envelope',
                    'title' => __('Unsubscribe', 'wordpress-gdpr'),
                    'desc' => __('Unsubscribe from our Newsletters.', 'wordpress-gdpr'),
                ),
            );

            $i = 0;
            $privacyCenterItems = apply_filters('wordpress_gdpr_privacy_center_items', $privacyCenterItems);
            foreach ($privacyCenterItems as $privacyCenterItemKey => $privacyCenterItem) {

                $class = '';

                $page = $this->get_option($privacyCenterItemKey . 'Page');
                if(empty($page)) {
                    continue;
                }

                $enabled = $this->get_option($privacyCenterItemKey . 'Enable');
                if(empty($enabled)) {
                    continue;
                }

                $page = get_permalink($page);

                if($page == "unsubscribePage") {

                    if($this->get_option('integrationsMailster')) { 
                        $mailsterOptions = get_option('mailster_options');
                        if(isset($mailsterOptions['homepage']) && !empty($mailsterOptions['homepage'])) {
                            $page = get_permalink($mailsterOptions['homepage']) . 'unsubscribe/';
                        } else {
                            $page = $page . 'unsubscribe/';
                        }
                    }
                }
                
                if( $i % 3 == 2) {
                    $class = ' wordpress-gdpr-privacy-center-item-last';
                }
                echo '<a href="' . $page . '" class="wordpress-gdpr-privacy-center-item' . $class . '">';
                    echo '<div class="wordpress-gdpr-privacy-center-item-' . $privacyCenterItemKey . '">';
                        echo '<i class="fa ' . $privacyCenterItem['icon'] . '"></i>';
                        echo '<h2 class="wordpress-gdpr-privacy-center-item-headline">' . $privacyCenterItem['title'] . '</h2>';
                        echo '<p class="wordpress-gdpr-privacy-center-item-desc">' . $privacyCenterItem['desc'] . '</p>';
                        echo '<div class="wordpress-gdpr-privacy-center-item-action">' . $btnText . '</div>';
                    echo '</div>';
                echo '</a>';

                $i++;
            }

            echo '</div>';
        echo '</div>';

        $html = ob_get_clean();
        return $html;
    }

    public function check_confirmation()
    {
        if(!isset($_GET['confirm']) || !isset($_GET['key']) || empty($_GET['confirm']) || empty($_GET['key'])) {
            return false;
        }

        $post_id = intval($_GET['confirm']);
        $key = $_GET['key'];
        $request = get_post($post_id);

        $unique = get_post_meta($post_id, 'gdpr_unique', true);
        if($unique !== $key) {
            return false;
        }

        update_post_meta($post_id, 'gdpr_confirmed', __('Yes', 'wordpress-gdpr'));

        $recipient = $this->get_option('contactDPOEmail');

        if(empty($recipient)) {
            return false;
        }
        $type = get_post_meta($post_id, 'gdpr_type', true);
        $mail = wp_mail($recipient, __('GDPR Request - ', 'wordpress-gdpr') . $type, __('A new GDPR Request is available.', 'wordpress-gdpr'));
        if($mail) {
            echo '<div class="alert alert-success">';
                echo __('Email Address confirmed and your request is in process now.', 'wordpress-gdpr');
            echo '</div>';
        } else {
            echo '<div class="alert alert-success">';
                echo __('Email Address could not be confirmed. Please send individual request.', 'wordpress-gdpr');
            echo '</div>';
        }
    }

    public function allow_cookies()
    {
        $domain = $this->get_option('domainName');

        $cookieLifetime = $this->get_option('cookieLifetime'); 
        $cookieLifetime = time() + (60*60*24*$cookieLifetime);

        $allCookies = array(
            'wordpress_gdpr_cookies_allowed' => 'true',
            'wordpress_gdpr_cookies_declined' => 'false',
            'wordpress_gdpr_analytics_allowed' => 'true',
            'wordpress_gdpr_adwords_allowed' => 'true',
            'wordpress_gdpr_tag_manager_allowed' => 'true',
            'wordpress_gdpr_hot_jar_allowed' => 'true',
            'wordpress_gdpr_facebook_allowed' => 'true',
            'wordpress_gdpr_piwik_allowed' => 'true',
            'wordpress_gdpr_adsense_allowed' => 'true',
            'wordpress_gdpr_custom_allowed' => 'true',
            'wordpress_gdpr_privacy_policy_accepted' => 'true',
            'wordpress_gdpr_terms_conditions_accepted' => 'true',
        );

        $customIntegrations = $this->get_option('integrationsCustoms');
        if (!empty($customIntegrations)) {
            foreach ($customIntegrations as $key => $customIntegration) {
                if(empty($customIntegration['title']) || empty($customIntegration['description'])) {
                    continue;
                }
                $allCookies['wordpress_gdpr_custom_' . $key . '_allowed'] = 'true';
            }
        }

        foreach ($allCookies as $cookie => $value) {
            setcookie($cookie, $value, $cookieLifetime, '/');
        }

        $_COOKIE['wordpress_gdpr_cookies_declined'] = 'false';

        do_action('wordpress_gdpr_allow_cookies', $allCookies);
    }

    public function decline_cookies()
    {
        $cookieLifetime = $this->get_option('cookieLifetime'); 
        $cookieLifetime = time() + (60*60*24*$cookieLifetime);
        setcookie('wordpress_gdpr_cookies_declined', 'true', $cookieLifetime, '/');

        $allowed_cookies = array(
            'woocommerce_cart_hash',
            'woocommerce_items_in_cart',
            '__cfduid',
            'wordpress_gdpr_cookies_declined',
            'wordpress_gdpr_cookies_allowed',
        );

        $customAllowedCookies = $this->get_option('customAllowedCookies');
        if(!empty($customAllowedCookies)) {
            $customAllowedCookies = array_map('trim', explode(',', $customAllowedCookies));
            if(is_array($customAllowedCookies) && !empty($customAllowedCookies)) {
                $allowed_cookies = array_merge($allowed_cookies, $customAllowedCookies);
            }
        }

        $allowed_cookies = apply_filters('wordpress_gdpr_necessary_cookies', $allowed_cookies);
        $this->delete_non_allowed_cookies($allowed_cookies, false);
        
        do_action('wordpress_gdpr_decline_cookies', array('wordpress_gdpr_cookies_declined' => 'true') );
    }

    public function disable_js_cookie_set()
    {
        if(!isset($_COOKIE["wordpress_gdpr_cookies_allowed"])) {
            echo "<script>
                if(!document.__defineGetter__) {
                Object.defineProperty(document, 'cookie', {
                    get: function(){return ''},
                    set: function(){return true},
                });
                } else {
                    document.__defineGetter__('cookie', function() { return '';} );
                    document.__defineSetter__('cookie', function() {} );
                }
            </script>";
        }
    }

    public function get_privacy_settings_page()
    {
        $privacy_settings = $this->get_privacy_settings();

        ob_start();

        $privacyCenterPage = $this->get_option('privacyCenterPage');
        if(!empty($privacyCenterPage)) {
            $privacyCenterPage = get_permalink($privacyCenterPage);
            echo '<a class="wordpress-gdpr-back-link" href="' . $privacyCenterPage . '">&larr; ' . __('Return to Privacy Center', 'wordpress-gdpr') . '</a>';
        }

        echo '<div class="wordpress-gdpr-privacy-settings">';
            echo '<table class="table wordpress-gdpr-privacy-settings-table">';
                echo '<thead>';
                    echo '<tr>';
                        echo '<th>' . __('Name', 'wordpress-gdpr') . '</th>';
                        echo '<th>' . __('Reason', 'wordpress-gdpr') . '</th>';
                        echo '<th>' . __('Enabled', 'wordpress-gdpr') . '</th>';
                    echo '</tr>';
                echo '</thead>';
                foreach ($privacy_settings as $privacy_setting) {
                    echo '<tr>';
                        echo '<td>' . $privacy_setting['title'] . '</td>';
                        echo '<td>' . $privacy_setting['reason'] . '</td>';
                        echo '<td>' . $privacy_setting['checkbox'] . '</td>';
                    echo '</tr>';
                }

            echo '</table>';
        echo '</div>';

        $html = ob_get_clean();
        return $html;
    }

    public function get_privacy_settings()
    {
        $privacy_settings = array();

        $customAllowedCookies = $this->get_option('customAllowedCookies');
        if(!empty($customAllowedCookies)) {
            $privacy_settings['customCookies'] = array(
                'title' => __('Technical Cookies', 'wordpress-gdpr'),
                'reason' => sprintf( __('In order to use this website we use the following technically required cookies: %s.', 'wordpress-gdpr'), $customAllowedCookies),
                'checkbox' => '<input class="form-control" disabled checked="checked" type="checkbox" name="wordpress_gdpr_custom_cookies_allowed" value="1" required>',
            );
        }

        $privacy_settings['cookies'] = array(
            'title' => __('Cookies', 'wordpress-gdpr'),
            'reason' => __('We use Cookies to give you a better website experience.', 'wordpress-gdpr'),
            'checkbox' => '<input class="form-control" type="checkbox" name="wordpress_gdpr_cookies_allowed" value="1" required>',
        );
        if($this->get_option('privacyPolicyAccept')) {
            $privacy_settings['privacy_policy_accepted'] = array(
                'title' => __('Privacy Policy', 'wordpress-gdpr'),
                'reason' => __('Check if you have accepted our privacy policy.', 'wordpress-gdpr'),
                'checkbox' => '<input class="form-control" type="checkbox" name="wordpress_gdpr_privacy_policy_accepted" value="1" required>',
            );
        }
        if($this->get_option('termsConditionsAccept')) {
            $privacy_settings['terms_conditions_accepted'] = array(
                'title' => __('Terms & Conditions', 'wordpress-gdpr'),
                'reason' => __('Check if you have accepted our terms & conditions.', 'wordpress-gdpr'),
                'checkbox' => '<input class="form-control" type="checkbox" name="wordpress_gdpr_terms_conditions_accepted" value="1" required>',
            );
        }
        if($this->get_option('integrationsCloudflare')) {
            $privacy_settings['cloudflare'] = array(
                'title' => __('Cloudflare', 'wordpress-gdpr'),
                'reason' => __('For perfomance reasons we use Cloudflare as a CDN network. This saves a cookie "__cfduid" to apply security settings on a per-client basis. This cookie is strictly necessary for Cloudflare\'s security features and cannot be turned off.', 'wordpress-gdpr'),
                'checkbox' => '<input class="form-control" disabled checked="checked" type="checkbox" name="wordpress_gdpr_cloudflare_allowed" value="1" required>',
            );
        }
        if($this->get_option('integrationsWooCommerce')) {
            $privacy_settings['woocommerce'] = array(
                'title' => __('WooCommerce', 'wordpress-gdpr'),
                'reason' => __('We use WooCommerce as a shopping system. For cart and order processing 2 cookies will be stored: woocommerce_cart_hash & woocommerce_items_in_cart. This cookies are strictly necessary and can not be turned off.', 'wordpress-gdpr'),
                'checkbox' => '<input class="form-control" disabled checked="checked" type="checkbox" name="wordpress_gdpr_cloudflare_allowed" value="1" required>',
            );
        }
        if($this->get_option('integrationsGoogleAnalytics')) {
            $privacy_settings['analytics'] = array(
                'title' => __('Google Analytics', 'wordpress-gdpr'),
                'reason' => __('We track anonymized user information to improve our website.', 'wordpress-gdpr'),
                'checkbox' => '<input class="form-control" type="checkbox" name="wordpress_gdpr_analytics_allowed" value="1" required>',
            );
        }
        if($this->get_option('integrationsGoogleAdwords')) {
            $privacy_settings['adwords'] = array(
                'title' => __('Google Adwords', 'wordpress-gdpr'),
                'reason' => __('We use Adwords to track our Conversions through Google Clicks.', 'wordpress-gdpr'),
                'checkbox' => '<input class="form-control" type="checkbox" name="wordpress_gdpr_adwords_allowed" value="1" required>',
            );
        }
        if($this->get_option('integrationsGoogleTagManager')) {
            $privacy_settings['tagmanager'] = array(
                'title' => __('Google Tag Manager', 'wordpress-gdpr'),
                'reason' => __('We use Google Tag Manager to monitor our traffic and to help us AB test new features    ', 'wordpress-gdpr'),
                'checkbox' => '<input class="form-control" type="checkbox" name="wordpress_gdpr_tag_manager_allowed" value="1" required>',
            );
        }
        if($this->get_option('integrationsHotJar')) {
            $privacy_settings['hotjar'] = array(
                'title' => __('Hot Jar', 'wordpress-gdpr'),
                'reason' => __('We use HotJar to track what users click on and engage with on this site ', 'wordpress-gdpr'),
                'checkbox' => '<input class="form-control" type="checkbox" name="wordpress_gdpr_hot_jar_allowed" value="1" required>',
            );
        }
        if($this->get_option('integrationsFacebook')) {
            $privacy_settings['facebook'] = array(
                'title' => __('Facebook', 'wordpress-gdpr'),
                'reason' => __('We use Facebook to track connections to social media channels.', 'wordpress-gdpr'),
                'checkbox' => '<input class="form-control" type="checkbox" name="wordpress_gdpr_facebook_allowed" value="1" required>',
            );
        }
        if($this->get_option('integrationsPiwik')) {
            $privacy_settings['piwik'] = array(
                'title' => __('Piwik', 'wordpress-gdpr'),
                'reason' => __('We use Piwik to track user information to improve our website.', 'wordpress-gdpr'),
                'checkbox' => '<input class="form-control" type="checkbox" name="wordpress_gdpr_piwik_allowed" value="1" required>',
            );
        }

        if($this->get_option('integrationsAdsense')) {
            $privacy_settings['adsense'] = array(
                'title' => __('AdSense', 'wordpress-gdpr'),
                'reason' => __('We use Google AdSense to show online advertisements on our website.', 'wordpress-gdpr'),
                'checkbox' => '<input class="form-control" type="checkbox" name="wordpress_gdpr_adsense_allowed" value="1" required>',
            );
        }

        if($this->get_option('integrationsCustom')) {
            $privacy_settings['custom'] = array(
                'title' => __('Custom', 'wordpress-gdpr'),
                'reason' => __('Use Loco Translate to change Text for custom Code', 'wordpress-gdpr'),
                'checkbox' => '<input class="form-control" type="checkbox" name="wordpress_gdpr_custom_allowed" value="1" required>',
            );
        }

        $customIntegrations = $this->get_option('integrationsCustoms');
        if (!empty($customIntegrations)) {
            foreach ($customIntegrations as $key => $customIntegration) {
                if(empty($customIntegration['title']) || empty($customIntegration['description'])) {
                    continue;
                }
                $privacy_settings['custom_' . $key] = array(
                    'title' => $customIntegration['title'],
                    'reason' => $customIntegration['url'],
                    'checkbox' => '<input class="form-control" type="checkbox" name="wordpress_gdpr_custom_' . $key . '_allowed" value="1" required>',
                );
            }
        }

        $privacy_settings = apply_filters('wordpress_gdpr_privacy_settings', $privacy_settings);

        return $privacy_settings;
    }

    public function check_privacy_setting() 
    {
        $setting = $_POST['setting'];
        $allowed = isset($_COOKIE[$setting]);
        $declined = false;
        $firstTime = false;

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

        $firstTimeAllowAllCookies = $this->get_option('firstTimeAllowAllCookies');
        if(!isset($_COOKIE['wordpress_gdpr_cookies_allowed']) && $firstTimeAllowAllCookies) {
            $this->allow_cookies();
            $allowed = true;
            $firstTime = true;
        }

        echo json_encode(array('allowed' => $allowed, 'declined' => $declined, 'firstTime' => $firstTime));
        wp_die();
    }

    public function check_privacy_settings() 
    {
        $settings = $_POST['settings'];

        $allowed_cookies = array(
            'woocommerce_cart_hash',
            'woocommerce_items_in_cart',
            '__cfduid',
            'wordpress_gdpr_cookies_declined',
            'wordpress_gdpr_cookies_allowed',
        );

        $customAllowedCookies = $this->get_option('customAllowedCookies');
        if(!empty($customAllowedCookies)) {
            $customAllowedCookies = array_map('trim', explode(',', $customAllowedCookies));
            if(is_array($customAllowedCookies) && !empty($customAllowedCookies)) {
                $allowed_cookies = array_merge($allowed_cookies, $customAllowedCookies);
            }
        }

        $return = array();
        $allowAll = false;
        foreach ($settings as $setting) {

            $allowed = isset($_COOKIE[$setting]);
            $declined = false;
        
            // Disallow all other cookies if cookies generally not allowed
            if( (!isset($_COOKIE['wordpress_gdpr_cookies_allowed']) || !$_COOKIE['wordpress_gdpr_cookies_allowed']) && ($settings !== 'wordpress_gdpr_cookies_allowed')) {
                $allowed = false;
            }

            // If user declined Cookies 
            if(isset($_COOKIE['wordpress_gdpr_cookies_declined']) && ($_COOKIE['wordpress_gdpr_cookies_declined']== "true")) {
                $declined = true;
                $allowed = false;
            }

            // Allow for admins & logged in if enabled
            if(current_user_can('administrator') || ($this->get_option('loggedInAllowAllCookies') && is_user_logged_in())) {
                $allowed = true;
            }

            $head = '';
            $body = '';

            if(!$allowed) {
                if($setting == "wordpress_gdpr_cookies_allowed") {
                    $head = "<script>
                        if(!document.__defineGetter__) {
                        Object.defineProperty(document, 'cookie', {
                            get: function(){return ''},
                            set: function(){return true},
                        });
                        } else {
                            document.__defineGetter__('cookie', function() { return '';} );
                            document.__defineSetter__('cookie', function() {} );
                        }
                    </script>";
                
                    $allowed_cookies = apply_filters('wordpress_gdpr_necessary_cookies', $allowed_cookies);
                    $this->delete_non_allowed_cookies($allowed_cookies, false);
                }
            }

            if($allowed) {
                
                switch ($setting) {
                    // Analytics
                    case 'wordpress_gdpr_analytics_allowed':
                        $head = $this->get_option('integrationsGoogleAnalyticsCode');
                        $allowed_cookies =  
                        array_merge($allowed_cookies, array(
                            'wordpress_gdpr_analytics_allowed',
                            '_ga',
                            '_gid',
                            '_gat',
                        ));
                        break;
                    // Adwords
                    case 'wordpress_gdpr_adwords_allowed':
                        $head = $this->get_option('integrationsGoogleAdwordsCode');
                        $allowed_cookies =  
                        array_merge($allowed_cookies, array(
                            'wordpress_gdpr_adwords_allowed',
                        ));
                        $allowAll = true;
                        break;
                    // Adsense
                    case 'wordpress_gdpr_adsense_allowed':
                        $head = $this->get_option('integrationsAdsenseCode');
                        $allowed_cookies =  
                        array_merge($allowed_cookies, array(
                            'wordpress_gdpr_adsense_allowed',
                            '_tlc',
                            '_tli',
                            '_tlp',
                            '_tlv',
                            'DSID',
                            'id',
                            'IDE',
                            'test_cookie',
                        ));
                        break;
                    // Facbeook
                    case 'wordpress_gdpr_facebook_allowed':
                        $head = $this->get_option('integrationsFacebookCode');
                        $allowed_cookies =  
                        array_merge($allowed_cookies, array(
                            'wordpress_gdpr_facebook_allowed',
                            'm_pixel_ratio',
                            'presence',
                            'sb',
                            'wd',
                            'xs',
                            'fr',
                            'tr',
                            'c_user',
                            'datr'
                        ));
                        break;
                    // Tag Manager
                    case 'wordpress_gdpr_tag_manager_allowed':
                        $head = $this->get_option('integrationsGoogleTagManagerCode');
                        $body = $this->get_option('integrationsGoogleTagManagerCodeBody');
                        $allowed_cookies =  
                        array_merge($allowed_cookies, array(
                            'wordpress_gdpr_tag_manager_allowed',
                        ));
                        $allowAll = true;
                        break;
                    // Hot Jar
                    case 'wordpress_gdpr_hot_jar_allowed':
                        $head = $this->get_option('integrationsHotJarCode');
                        $allowed_cookies =  
                            array_merge($allowed_cookies, array(
                            'wordpress_gdpr_hot_jar_allowed',
                            '_hjClosedSurveyInvites',
                            '_hjDonePolls',
                            '_hjMinimizedPolls',
                            '_hjDoneTestersWidgets',
                            '_hjMinimizedTestersWidgets',
                            '_hjIncludedInSample',
                            '1P_JAR'
                        ));
                        break;
                    // Piwik
                    case 'wordpress_gdpr_piwik_allowed':
                        $head = $this->get_option('integrationsPiwikCode');
                        $allowed_cookies =  
                        array_merge($allowed_cookies, array(
                            'wordpress_gdpr_piwik_allowed',
                            '_pk_ref',
                            '_pk_cvar',
                            '_pk_id',
                            '_pk_ses',
                            '_pk_uid',
                        ));
                        break;
                    // Custom 
                    case 'wordpress_gdpr_custom_allowed':
                        $head = $this->get_option('integrationsCustomCode');
                        $allowed_cookies =  
                        array_merge($allowed_cookies, array(
                            'wordpress_custom_allowed',
                        ));
                        $allowAll = true;
                        break;
                }

                $customIntegrations = $this->get_option('integrationsCustoms');
                if (!empty($customIntegrations)) {

                    foreach ($customIntegrations as $key => $customIntegration) {

                        if($setting == 'wordpress_gdpr_custom_' . $key . '_allowed') {
                            $head = $customIntegration['description'];
                            $allowed_cookies =  
                            array_merge($allowed_cookies, array(
                                'wordpress_gdpr_custom_' . $key . '_allowed',
                            ));
                            break;
                        }

                    }
                }
            }

            $return[$setting] = array(
                'allowed' => $allowed,
                'head' => $head,
                'body' => $body,
            );
        }

        if($this->get_option('useCookieWhitelist')) {
            $allowed_cookies = apply_filters('wordpress_gdpr_necessary_cookies', $allowed_cookies);
            $this->delete_non_allowed_cookies($allowed_cookies, $allowAll);
        }

        echo json_encode($return);
        wp_die();
    }

    public function update_privacy_setting()
    {
        $setting = $_POST['setting'];
        $checked = $_POST['checked'];

        do_action('wordpress_gdpr_update_cookie', array($setting => $checked));

        if($checked == "false") {
            setcookie($setting, 'false', time()-1000, '/');

            // Remove all cookies - they will be readded by scripts again
            if($setting == "wordpress_gdpr_cookies_allowed") {
                $domain = $this->get_option('domainName');
                $past = time() - 3600;
                $allowed_cookies = array(
                    'woocommerce_cart_hash',
                    'woocommerce_items_in_cart',
                    '__cfduid',
                    'wordpress_gdpr_cookies_declined',
                    'wordpress_gdpr_cookies_allowed',
                    'wordpress_gdpr_analytics_allowed',
                    'wordpress_gdpr_adwords_allowed',
                    'wordpress_gdpr_adsense_allowed',
                    'wordpress_gdpr_facebook_allowed',
                    'wordpress_gdpr_tag_manager_allowed',
                    'wordpress_gdpr_hot_jar_allowed',
                    'wordpress_gdpr_piwik_allowed',
                    'wordpress_gdpr_custom_allowed',
                    'wordpress_gdpr_privacy_policy_accepted',
                    'wordpress_gdpr_terms_conditions_accepted',
                );
                $allowed_cookies = apply_filters('wordpress_gdpr_necessary_cookies', $allowed_cookies);

                $customAllowedCookies = $this->get_option('customAllowedCookies');
                if(!empty($customAllowedCookies)) {
                    $customAllowedCookies = array_map('trim', explode(',', $customAllowedCookies));
                    if(is_array($customAllowedCookies) && !empty($customAllowedCookies)) {
                        $allowed_cookies = array_merge($allowed_cookies, $customAllowedCookies);
                    }
                }

                foreach ( $_COOKIE as $key => $value ) {
                    if(!empty($allowed_cookies)) {
                        foreach ($allowed_cookies as $allowed_cookie) {
                            if (strpos($key, $allowed_cookie) !== FALSE) { 
                                continue 2;
                            }
                        }
                    }
                    // if(in_array($key, $allowed_cookies)) {
                    //     continue;
                    // }

                    setcookie( $key, $value, $past, '/');
                    setcookie( $key, $value, $past, '/', $domain);
                    setcookie( $key, $value, $past);
                    continue;
                }
            }
        } else {
            if($setting == "wordpress_gdpr_cookies_allowed") {
                setcookie('wordpress_gdpr_cookies_declined', 'false', time()-1000, '/');
            }
            $cookieLifetime = $this->get_option('cookieLifetime'); 
            $cookieLifetime = time() + (60*60*24*$cookieLifetime);
            setcookie($setting, 'true', $cookieLifetime, '/');
        }
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

    public function delete_non_allowed_cookies($allowed_cookies, $allowAll)
    {
        if($allowAll) {
            return;
        }
        $past = time() - 3600;
        $domain = $this->get_option('domainName');
        
        foreach ( $_COOKIE as $key => $value ) {
            if(!empty($allowed_cookies)) {
                foreach ($allowed_cookies as $allowed_cookie) {
                    if (strpos($key, $allowed_cookie) !== FALSE) { 
                        continue 2;
                    }
                }
            }
            // if(in_array($key, $allowed_cookies)) {
            //     continue;
            // }

            setcookie( $key, $value, $past, '/');
            setcookie( $key, $value, $past, '/', $domain);
            setcookie( $key, $value, $past);
        }
    }

    public function get_privacy_policy_accept()
    {
        if(!$this->get_option('privacyPolicyAccept')) {
            return false;
        }
        $text = $this->get_option('privacyPolicyAcceptText');

        $html = '
        <p class="form-row">
            <label class="checkbox">
                <input type="checkbox" class="input-checkbox" name="wordpress_gdpr_privacy_policy_accepted" id="accept-privacy-policy-checkbox" required><span>'
                . $text . ' </span><span class="required">*</span>
            </label><br>
        </p>';
        return $html;
    }

    public function get_terms_conditions_accept()
    {
        if(!$this->get_option('termsConditionsAccept')) {
            return false;
        }
        $text = $this->get_option('termsConditionsAcceptText');

        $html = '
        <p class="form-row">
            <label class="checkbox">
                <input type="checkbox" class="input-checkbox" name="wordpress_gdpr_terms_conditions_accepted" id="accept-terms-conditions-checkbox" required><span>'
                . $text . ' </span><span class="required">*</span>
            </label><br>
        </p>';
        return $html;
    }
}