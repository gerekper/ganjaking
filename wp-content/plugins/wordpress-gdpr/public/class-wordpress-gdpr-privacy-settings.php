<?php

class WordPress_GDPR_Privacy_Settings extends WordPress_GDPR
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
     * Privacy Settings Trigger 
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     */
    public function get_privacy_settings_trigger()
    {
        $triggerEnable = $this->get_option('privacySettingsTriggerEnable');
        if(!$triggerEnable) {
            return false;
        }

        $text = $this->get_option('privacySettingsTriggerText');
        $backgroundColor = $this->get_option('privacySettingsTriggerBackgroundColor');
        $textColor = $this->get_option('privacySettingsTriggerTextColor');
        $position = $this->get_option('privacySettingsTriggerPosition');

        $useShortcode = $this->get_option('privacySettingsUseShortcode');
        $useShortcodePage = $this->get_option('privacySettingsUseShortcodePage');

        if($useShortcode && !empty($useShortcodePage)) {
            echo '<a href="' . get_permalink($useShortcodePage) . '" class="wordpress-gdpr-privacy-settings-trigger-container ' . $position . '" style="background-color: ' . $backgroundColor . '; color: ' . $textColor . ';">';
        } else {
            echo '<a href="#" class="wordpress-gdpr-privacy-settings-trigger-container wordpress-gdpr-open-privacy-settings-modal ' . $position . '" style="background-color: ' . $backgroundColor . '; color: ' . $textColor . ';">';
        }
        ?>
            
            <div class="wordpress-gdpr-privacy-settings-trigger">
                <?php echo $text ?>
            </div>
        </a>
        <?php
    }

    public function get_privacy_settings()
    {
        $logo = "";
        $privacySettingsPopupLogo = $this->get_option('privacySettingsPopupLogo');
        $blogLogo = get_theme_mod( 'custom_logo' );

        if(isset($privacySettingsPopupLogo['url']) && !empty($privacySettingsPopupLogo['url'])) {
            $logo = '<img src="' . $privacySettingsPopupLogo['url'] . '" alt="' . $privacySettingsPopupLogo['alt'] . '">';
        } elseif(!empty($blogLogo)) {
            $image = wp_get_attachment_image_src( $blogLogo , 'full' );
            $logo = '<img src="' . $image[0] . '">';
        } else {
            $logo = get_bloginfo('name');
        }

        $privacySettingsPopupStyle = $this->get_option('privacySettingsPopupStyle');
        $privacySettingsPopupPosition = $this->get_option('privacySettingsPopupPosition');
        $privacySettingsPopupAcceptColor = $this->get_option('privacySettingsPopupAcceptColor');
        $privacySettingsPopupAcceptBackgroundColor = $this->get_option('privacySettingsPopupAcceptBackgroundColor');
        $privacySettingsPopupDeclineColor = $this->get_option('privacySettingsPopupDeclineColor');
        $privacySettingsPopupDeclineBackgroundColor = $this->get_option('privacySettingsPopupDeclineBackgroundColor');

        $privacySettingsPopupTitle = $this->get_option('privacySettingsPopupTitle');
        $privacySettingsPopupDescription = $this->get_option('privacySettingsPopupDescription');

        $useFontAwesome5 = $this->get_option('useFontAwesome5');
        $fontAwesomePrefix = 'fa';
        if($useFontAwesome5) {
            $fontAwesomePrefix = $this->get_option('fontAwesomePrefix');
        }


        $service_categories = get_terms(array(
            'taxonomy'      => 'gdpr_service_categories',
            'hide_empty'    => true,
            'parent'        => 0,
        ));

        $category_html = "";
        $service_html = "";
        $first = false;
        if(empty($service_categories)) {
            return false;
        }

        foreach ($service_categories as $service_category) {

            $category_html .= 
                '<a href="#" data-id="' . $service_category->term_id . '" class="wordpress-gdpr-popup-privacy-settings-service-category wordpress-gdpr-popup-privacy-settings-open-service-category">
                    ' . $service_category->name . '
                </a>';

            $display = '';
            if($first) {
                $display = 'style="display: none;"';
            }

            $service_html .= '<div id="wordpress-gdpr-popup-privacy-settings-services-content-' . $service_category->term_id . '" ' . $display . ' class="wordpress-gdpr-popup-privacy-settings-services-content">';
            $service_html .= '<div class="wordpress-gdpr-popup-privacy-settings-service-category-description">' . $service_category->description . '</div><hr>';

            $args = array(
                'post_type' => 'gdpr_service',
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'hierarchical' => false,
                'posts_per_page' => -1,
                'tax_query' => array(
                    array(
                    'taxonomy' => 'gdpr_service_categories',
                    'field' => 'id',
                    'terms' => $service_category->term_id,
                    'include_children' => false
                    )
                )
            );
            $services = get_posts($args);

            foreach ($services as $service) {

                $deactivatable = get_post_meta($service->ID, 'deactivatable' , true);
                $cookies = get_post_meta($service->ID, 'cookies' , true);

                $switch_disabled = '';
                $checked = '';
                if($deactivatable == "0") {
                    $switch_disabled = ' disabled="disabled"';
                    $checked = ' checked="checked"';
                }

                $service_html .= '<div class="wordpress-gdpr-popup-privacy-settings-services-content-title-box">';
                    $service_html .= '<a href="#" data-id="' . $service->ID . '" class="wordpress-gdpr-popup-privacy-settings-services-content-title"><i class="' . $fontAwesomePrefix . ' fa-caret-right"></i> ' . $service->post_title . '</a>';
                    $service_html .= '<input name="' . $service->ID . '" data-id="' . $service->ID . '" ' . $switch_disabled . $checked .' class="gdpr-service-switch" type="checkbox">';
                    $service_html .= '<div class="gdpr-clear"></div>';
                $service_html .= '</div>';
                $service_html .= '<div id="wordpress-gdpr-popup-privacy-settings-services-content-description-' . $service->ID . '" class="wordpress-gdpr-popup-privacy-settings-services-content-description">';
                    $service_html .= '<div class="wordpress-gdpr-popup-privacy-settings-services-content-reason">' . $service->post_content . '</div>';
                    if(!empty($cookies)) {
                        $service_html .= '<ul class="wordpress-gdpr-popup-privacy-settings-services-content-cookies">';
                        $cookies_txt = explode(',', $cookies);
                        foreach ($cookies_txt as $cookie_txt) {
                            $service_html .= '<li>' . $cookie_txt  . '</li>';
                        }
                        $service_html .= '</ul>';
                    }
                $service_html .= '</div><hr>';
            }
            $service_html .= '</div>';
            $first = true;
        }
        ?>

            <div class="wordpress-gdpr-privacy-settings-popup-message">
                <?php echo __('Privacy Settings saved!', 'wordpress-gdpr') ?>
            </div>
            
            <div class="wordpress-gdpr-privacy-settings-popup-header">
                <div class="wordpress-gdpr-privacy-settings-popup-logo">
                    <?php echo $logo ?>
                </div>
                <div class="wordpress-gdpr-privacy-settings-popup-info">
                    <div class="wordpress-gdpr-privacy-settings-popup-title"><?php echo $privacySettingsPopupTitle ?></div>
                    <p class="wordpress-gdpr-privacy-settings-popup-description"><?php echo $privacySettingsPopupDescription ?></p>
                </div>
                <div class="gdpr-clear"></div>
            </div>
            
            
            <div class="wordpress-gdpr-privacy-settings-popup-services-container">
                <div class="wordpress-gdpr-privacy-settings-popup-service-categories">
                    <?php echo $category_html ?>

                    <?php
                    $privacySettingsPopupTextPrivacyCenter = $this->get_option('privacySettingsPopupTextPrivacyCenter');
                    $privacyCenterPage = $this->get_option('privacyCenterPage');
                    if(!empty($privacySettingsPopupTextPrivacyCenter) && !empty($privacyCenterPage)) {
                        echo '<a href="' . get_permalink($privacyCenterPage) . '" class="wordpress-gdpr-popup-privacy-settings-service-category">' . $privacySettingsPopupTextPrivacyCenter . ' <i class="' . $fontAwesomePrefix . ' fa-external-link-alt"></i></a>';
                    }

                    $privacySettingsPopupTextPrivacyPolicy = $this->get_option('privacySettingsPopupTextPrivacyPolicy');
                    $privacyPolicyPage = $this->get_option('privacyPolicyPage');
                    if(!empty($privacySettingsPopupTextPrivacyPolicy) && !empty($privacyPolicyPage)) {
                        echo '<a href="' . get_permalink($privacyPolicyPage) . '" class="wordpress-gdpr-popup-privacy-settings-service-category">' . $privacySettingsPopupTextPrivacyPolicy . ' <i class="' . $fontAwesomePrefix . ' fa-external-link-alt"></i></a>';
                    }

                    $privacySettingsPopupTextCookiePolicy = $this->get_option('privacySettingsPopupTextCookiePolicy');
                    $cookiePolicyPage = $this->get_option('cookiePolicyPage');
                    if(!empty($privacySettingsPopupTextCookiePolicy) && !empty($cookiePolicyPage)) {
                        echo '<a href="' . get_permalink($cookiePolicyPage) . '" class="wordpress-gdpr-popup-privacy-settings-service-category">' . $privacySettingsPopupTextCookiePolicy . ' <i class="' . $fontAwesomePrefix . ' fa-external-link-alt"></i></a>';
                    }
                    ?>
                </div>
                <div class="wordpress-gdpr-privacy-settings-popup-services">
                    <?php echo $service_html ?>
                </div>
                <div class="gdpr-clear"></div>
            </div>

            <div class="wordpress-gdpr-privacy-settings-popup-services-buttons">
                <div class="wordpress-gdpr-popup-decline wordpress-gdpr-privacy-settings-popup-services-decline-all button btn button-secondary theme-btn" style="background-color: <?php echo $privacySettingsPopupDeclineBackgroundColor ?>; color: <?php echo $privacySettingsPopupDeclineColor ?>;">
                    <?php echo __('Decline all Services', 'wordpress-gdpr') ?>
                </div>
                <div class="wordpress-gdpr-popup-agree wordpress-gdpr-privacy-settings-popup-services-accept-all button btn button-secondary theme-btn" style="background-color: <?php echo $privacySettingsPopupAcceptBackgroundColor ?>;color: <?php echo $privacySettingsPopupAcceptColor ?>;">
                    <?php echo __('Accept all Services', 'wordpress-gdpr') ?>
                </div>

                <div class="gdpr-clear"></div>
            </div>
        <?php
    }
    
    /**
     * [get_privacy_policy_accept description]
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    public function get_privacy_policy_accept()
    {
        if(!$this->get_option('privacyPolicyAccept')) {
            return false;
        }
        $text = $this->get_option('privacyPolicyAcceptText');

        $accepted = "";
        if($_COOKIE['wordpress_gdpr_privacy_policy_accepted'] == "true") {
            $accepted = 'checked="checked"';
        }

        $html = '
        <p class="form-row">
            <label class="checkbox">
                <input type="checkbox" class="input-checkbox" name="wordpress_gdpr_privacy_policy_accepted" ' . $accepted . ' id="accept-privacy-policy-checkbox" required><span>'
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
       
        $accepted = "";
        if($_COOKIE['wordpress_gdpr_terms_conditions_accepted'] == "true") {
            $accepted = 'checked="checked"';
        }

        $html = '
        <p class="form-row">
            <label class="checkbox">
                <input type="checkbox" class="input-checkbox" name="wordpress_gdpr_terms_conditions_accepted" ' . $accepted . ' id="accept-terms-conditions-checkbox" required><span>'
                . $text . ' </span><span class="required">*</span>
            </label><br>
        </p>';
        return $html;
    }

    public function get_privacy_settings_popup()
    {
        $privacySettingsPopupBackgroundColor = $this->get_option('privacySettingsPopupBackgroundColor');
        $privacySettingsPopupTextColor = $this->get_option('privacySettingsPopupTextColor');
        $privacySettingsPopupCloseIcon = $this->get_option('privacySettingsPopupCloseIcon');
        $privacySettingsPopupCloseIconColor = $this->get_option('privacySettingsPopupCloseIconColor');
        $privacySettingsPopupCloseIconBackgroundColor = $this->get_option('privacySettingsPopupCloseIconBackgroundColor');

        ?>

        <div class="wordpress-gdpr-privacy-settings-popup-container">
            <div class="wordpress-gdpr-privacy-settings-popup" 
                style="background-color: <?php echo $privacySettingsPopupBackgroundColor ?>; color: <?php echo $privacySettingsPopupTextColor ?>;">
                <a href="#" id="wordpress-gdpr-privacy-settings-popup-close" title="close" class="wordpress-gdpr-privacy-settings-popup-close" style="background-color: <?php echo $privacySettingsPopupCloseIconBackgroundColor ?>;">
                    <i style="color: <?php echo $privacySettingsPopupCloseIconColor ?>;" class="<?php echo $privacySettingsPopupCloseIcon ?>"></i>
                </a>
                <?php $this->get_privacy_settings() ?>
            </div>
            <div class="wordpress-gdpr-privacy-settings-popup-backdrop"></div>
        </div>
        <?php
    }

    public function get_privacy_settings_shortcode()
    {
        $this->get_privacy_settings();
    }
}
