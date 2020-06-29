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

        $useFontAwesome5 = $this->get_option('useFontAwesome5');
        if(!$useFontAwesome5) {
            wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', array(), '4.7.0', 'all');
        } else {

            if($this->get_option('useFontAwesome5Load')) {
                wp_enqueue_style('font-awesome-5', 'https://use.fontawesome.com/releases/v5.8.1/css/all.css', array(), '5.8.1', 'all');
            }
        }

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
            'acceptanceText' => __( 'You must accept our Privacy Policy.', 'wordpress-gdpr' ),
            'termsAcceptanceText' => __( 'You must accept our Terms and Conditions.', 'wordpress-gdpr' ),
        );

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

    /**
     * Get Privacy Center
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @param   [type]                       $atts [description]
     * @return  [type]                             [description]
     */
    public function get_privacy_center($atts)
    {
        $btnText = __('Learn More', 'wordpress-gdpr');

        $fontAwesomePrefix = 'fa';
        $useFontAwesome5 = $this->get_option('useFontAwesome5');
        if($useFontAwesome5) {
            $fontAwesomePrefix = $this->get_option('fontAwesomePrefix');
        }

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

            if($useFontAwesome5) {
                $privacyCenterItems['contactDPO']['icon'] = 'fa-user-circle';
                $privacyCenterItems['dataRectification']['icon'] = 'fa-pencil-alt';
                $privacyCenterItems['imprint']['icon'] = 'fa-file-alt';
                $privacyCenterItems['privacyPolicy']['icon'] = 'fa-file-alt';
            }

            $i = 0;
            $privacyCenterItems = apply_filters('wordpress_gdpr_privacy_center_items', $privacyCenterItems);
            foreach ($privacyCenterItems as $privacyCenterItemKey => $privacyCenterItem) {

                $class = '';

                if($privacyCenterItemKey == "privacySettings" && ($this->get_option('privacySettingsPopupEnable') || $this->get_option('privacySettingsUseShortcode'))) {

                    if($this->get_option('privacySettingsUseShortcode') && !empty($this->get_option('privacySettingsUseShortcodePage'))) {
                        $page = get_permalink($this->get_option('privacySettingsUseShortcodePage'));
                    } else {
                        $page = '#';
                        $class = ' wordpress-gdpr-open-privacy-settings-modal';
                    }
                } else {

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
                 }
                
                if( $i % 3 == 2) {
                    $class .= ' wordpress-gdpr-privacy-center-item-last';
                }
                echo '<a href="' . $page . '" class="wordpress-gdpr-privacy-center-item' . $class . '">';
                    echo '<div class="wordpress-gdpr-privacy-center-item-' . $privacyCenterItemKey . '">';
                        echo '<i class="' . $fontAwesomePrefix . ' ' . $privacyCenterItem['icon'] . '"></i>';
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

    /**
     * Check confirmed Emails
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
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
}