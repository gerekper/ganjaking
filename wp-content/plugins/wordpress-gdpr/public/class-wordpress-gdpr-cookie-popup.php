<?php

class WordPress_GDPR_Cookie_Popup extends WordPress_GDPR
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

    public function add_popup()
    {
        $popupEnable = $this->get_option('popupEnable');
        if(!$popupEnable) {
            return false;
        }

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
        $popupAgreeColor = $this->get_option('popupAgreeColor');
        $popupAgreeBackgroundColor = $this->get_option('popupAgreeBackgroundColor');
        $popupDeclineColor = $this->get_option('popupDeclineColor');
        $popupDeclineBackgroundColor = $this->get_option('popupDeclineBackgroundColor');
        $popupLinkColor = $this->get_option('popupLinkColor');

        $privacyCenterPage = $this->get_option('privacyCenterPage');
        $privacySettingsPopupEnable = $this->get_option('privacySettingsPopupEnable');
        $cookiePolicyPage = $this->get_option('cookiePolicyPage');

        $popupCloseIcon = $this->get_option('popupCloseIcon');
        $popupCloseIconColor = $this->get_option('popupCloseIconColor');
        $popupCloseIconBackgroundColor = $this->get_option('popupCloseIconBackgroundColor');

        $renderd = false;
        ?>
        <div class="wordpress-gdpr-popup <?php echo $popupStyle . ' ' . $popupPosition ?>" 
            style="background-color: <?php echo $popupBackgroundColor ?>; color: <?php echo $popupTextColor ?>;">

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
                    <div class="wordpress-gdpr-popup-actions-buttons">
                        <?php if(!empty($popupTextAgree)) { ?>
                            <a href="#" class="wordpress-gdpr-popup-agree" style="background-color: <?php echo $popupAgreeBackgroundColor ?>; color: <?php echo $popupAgreeColor ?>;"><?php echo $popupTextAgree ?></a>
                        <?php } ?>
                    
                        <?php if(!empty($popupTextDecline)) { ?>
                            <a href="#" class="wordpress-gdpr-popup-decline" style="background-color: <?php echo $popupDeclineBackgroundColor ?>; color: <?php echo $popupDeclineColor ?>;"><?php echo $popupTextDecline ?></a>
                        <?php } ?>
                        <div class="gdpr-clear"></div>
                    </div>
                    <div class="wordpress-gdpr-popup-actions-links">
                        <?php if(!empty($popupTextPrivacyCenter) && !empty($privacyCenterPage)) { ?>
                            <a href="<?php echo get_permalink($privacyCenterPage) ?>" class="wordpress-gdpr-popup-privacy-center" style="color: <?php echo $popupLinkColor ?>;"><?php echo $popupTextPrivacyCenter ?></a>
                        <?php } ?>

                        <?php if(!empty($popupTextPrivacySettings) && !empty($privacySettingsPopupEnable)) { ?>
                            <a href="#" class="wordpress-gdpr-popup-privacy-settings-text wordpress-gdpr-open-privacy-settings-modal" style="color: <?php echo $popupLinkColor ?>;"><?php echo $popupTextPrivacySettings ?></a>
                        <?php } ?>

                        <?php if(!empty($cookiePolicyPage) && !empty($popupTextCookiePolicy)) { ?>
                            <a href="<?php echo get_permalink($cookiePolicyPage) ?>" class="wordpress-gdpr-popup-read-more" style="color: <?php echo $popupLinkColor ?>;"><?php echo $popupTextCookiePolicy ?></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}