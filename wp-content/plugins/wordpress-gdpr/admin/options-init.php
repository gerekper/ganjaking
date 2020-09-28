<?php

    /**
     * For full documentation, please visit: http://docs.reduxframework.com/
     * For a more extensive sample-config file, you may look at:
     * https://github.com/reduxframework/redux-framework/blob/master/sample/sample-config.php
     */

if (! class_exists('Redux')) {
    return;
}

    // This is your option name where all the Redux data is gdprd.
    $opt_name = "wordpress_gdpr_options";

    /**
     * ---> SET ARGUMENTS
     * All the possible arguments for Redux.
     * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
     * */

    $theme = wp_get_theme(); // For use with some settings. Not necessary.

    $args = array(
        'opt_name' => 'wordpress_gdpr_options',
        'use_cdn' => true,
        'dev_mode' => false,
        'display_name' => __('WordPress GDPR', 'wordpress-gdpr'),
        'display_version' => '1.9.7',
        'page_title' => __('WordPress GDPR', 'wordpress-gdpr'),
        'update_notice' => true,
        'intro_text' => '',
        'footer_text' => '&copy; ' . date('Y') . ' weLaunch',
        'admin_bar' => false,
        'menu_type' => 'menu',
        'page_priority'     => 120,
        'menu_title' => __('GDPR', 'wordpress-gdpr'),
        'allow_sub_menu' => TRUE,
        'page_parent' => 'edit.php?post_type=gdpr_request',
        'menu_icon' => 'dashicons-businessman',
        'customizer' => false,
        'default_mark' => '*',
        'hints' => array(
            'icon_position' => 'right',
            'icon_color' => 'lightgray',
            'icon_size' => 'normal',
            'tip_style' => array(
                'color' => 'light',
            ),
            'tip_position' => array(
                'my' => 'top left',
                'at' => 'bottom right',
            ),
            'tip_effect' => array(
                'show' => array(
                    'duration' => '500',
                    'event' => 'mouseover',
                ),
                'hide' => array(
                    'duration' => '500',
                    'event' => 'mouseleave unfocus',
                ),
            ),
        ),
        'output' => true,
        'output_tag' => true,
        'settings_api' => true,
        'cdn_check_time' => '1440',
        'compiler' => true,
        'page_permissions' => 'manage_options',
        'save_defaults' => true,
        'show_import_export' => true,
        'database' => 'options',
        'transient_time' => '3600',
        'network_sites' => false,
    );

    Redux::setArgs($opt_name, $args);

    /*
     * ---> END ARGUMENTS
     */

    /*
     * ---> START HELP TABS
     */

    $tabs = array(
        array(
            'id'      => 'help-tab',
            'title'   => __('Information', 'wordpress-gdpr'),
            'content' => __('<p>Need support? Please use the comment function on codecanyon.</p>', 'wordpress-gdpr')
        ),
    );
    Redux::setHelpTab($opt_name, $tabs);

    // Set the help sidebar
    // $content = __( '<p>This is the sidebar content, HTML is allowed.</p>', 'wordpress-gdpr' );
    // Redux::setHelpSidebar( $opt_name, $content );


    /*
     * <--- END HELP TABS
     */


    /*
     *
     * ---> START SECTIONS
     *
     */

    $default_email = get_option('admin_email');
    $site_name = get_option('blogname');
    $urlparts = parse_url(site_url());
    $default_domain = str_replace('www', '', $urlparts['host']);

    Redux::setSection($opt_name, array(
        'title'  => __('Settings', 'wordpress-gdpr'),
        'id'     => 'general',
        'desc'   => __('Need support? Please use the comment function on codecanyon.', 'wordpress-gdpr'),
        'icon'   => 'el el-home',
    ));

    Redux::setSection($opt_name, array(
        'title'      => __('General', 'wordpress-gdpr'),
        'id'         => 'general-settings',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'enable',
                'type'     => 'switch',
                'title'    => __('Enable', 'wordpress-gdpr'),
                'default'  => '1',
            ),
            array(
                'id'       => 'domainName',
                'type'     => 'text',
                'title'    => __('Domain Name', 'wordpress-gdpr'),
                'subtitle' => __('Important for Cookie unsettings. E.g. ".yourdomain.com". Dot before important for GA-Cookies.', 'wordpress-gdpr'),
                'default'  => $default_domain,
                'required' => array('enable','equals','1'),
            ),
            array(
                'id'       => 'enableRecaptcha',
                'type'     => 'checkbox',
                'title'    => __('Enable Recaptcha for Forms', 'wordpress-gdpr'),
                'subtitle'    => __('Install & Setup the <a href="https://wordpress.org/plugins/invisible-recaptcha/" target="_blank">invisible recaptcha plugin from here</a>. Then check this option.', 'wordpress-gdpr'),
                'default'  => '0',
                'required' => array('enable','equals','1'),
            ),
            array(
                'id'       => 'firstTimeAllowAllCookies',
                'type'     => 'checkbox',
                'title'    => __('Allow all Cookies for all users visiting first time', 'wordpress-gdpr'),
                'subtitle'    => __('This allows all cookies until a user opts out himself.', 'wordpress-gdpr'),
                'default'  => '0',
                'required' => array('enable','equals','1'),
            ),
            array(
                'id'       => 'continueVisitingAllowAllCookies',
                'type'     => 'checkbox',
                'title'    => __('Allow all Cookies for users continue visiting your site', 'wordpress-gdpr'),
                'subtitle'    => __('When a user makes no decision, but continues to browse on your site all cookies will be enabled.', 'wordpress-gdpr'),
                'default'  => '0',
                'required' => array('enable','equals','1'),
            ),
            array(
                'id'       => 'loggedInAllowAllCookies',
                'type'     => 'checkbox',
                'title'    => __('Allow all Cookies for logged in users', 'wordpress-gdpr'),
                'subtitle'    => __('This disables cookie check consents for logged in users.', 'wordpress-gdpr'),
                'default'  => '0',
                'required' => array('enable','equals','1'),
            ),
            array(
                'id'       => 'useFontAwesome5',
                'type'     => 'checkbox',
                'title'    => __('Use Font Awesome 5', 'wordpress-gdpr'),
                'subtitle'    => __('This will disable font awesome 4 to load.', 'wordpress-gdpr'),
                'default'  => '0',
                'required' => array('enable','equals','1'),
            ),
            array(
                'id'       => 'fontAwesomePrefix',
                'type'     => 'text',
                'title'    => __('Font Awesome Prefix', 'wordpress-gdpr'),
                'subtitle' => __('Choose fas if you use free, otherwise you can use fal or far', 'wordpress-gdpr'),
                'default'  => 'fas',
                'required' => array('useFontAwesome5','equals','1'),
            ),
            array(
                'id'       => 'useFontAwesome5Load',
                'type'     => 'checkbox',
                'title'    => __('Load Font Awesome 5 Free', 'wordpress-gdpr'),
                'subtitle'    => __('If your theme does not load it, our plugin will load it when checked.', 'wordpress-gdpr'),
                'default'  => '0',
                'required' => array('useFontAwesome5','equals','1'),
            ),
            array(
                'id'       => 'cookieLifetime',
                'type'     => 'spinner',
                'title'    => __( 'Cookie Lifetime.', 'wordpress-gdpr' ),
                'subtitle'    => __( 'Days before the Cookie expires.', 'wordpress-gdpr' ),
                'min'      => '0',
                'step'     => '1',
                'max'      => '9999',
                'default'  => '180',
                'required' => array('enable','equals','1'),
            ),
            array(
                'id'   => 'installPages',
                'type' => 'info',
                'desc' => '<div style="text-align:center;">' . __('This will create all pages, where no current assigned pages were found.', 'wordpress-gdpr') . '<br>
                    <a href="' . get_admin_url() . 'admin.php?page=wordpress_gdpr_options_options&wordpress_gdpr[install-pages]=true" class="button button-success">' . __('Install all Pages', 'wordpress-gdpr') . '</a>
                    </div>',
            ),
            array(
                'id'   => 'migrateServices',
                'type' => 'info',
                'desc' => '<div style="text-align:center;">' . __('This will migrate all Service like Google Analytics etc to our new services section.', 'wordpress-gdpr') . '<br>
                    <a href="' . get_admin_url() . 'admin.php?page=wordpress_gdpr_options_options&wordpress_gdpr[migrate-services]=true" class="button button-success">' . __('Migrate Services', 'wordpress-gdpr') . '</a>
                    </div>',
            ),
        )
    ));

    Redux::setSection($opt_name, array(
        'title'      => __('Cookie Popup', 'wordpress-gdpr'),
        'id'         => 'popup-settings',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'popupEnable',
                'type'     => 'switch',
                'title'    => __('Enable Popup', 'wordpress-gdpr'),
                'default'  => '1',
            ),
            array(
                'id'       => 'popupExcludePages',
                'type'     => 'select',
                'title'    => __('Exclude Pages', 'wordpress-gdpr'),
                'subtitle' => __('Popup will not show in these pages:', 'wordpress-gdpr'),
                // 'options'  => $wordpress_gdpr_pages,
                'data'     => 'pages',
                'ajax'     => true,
                'multi'    => true,
                'required' => array('popupEnable','equals','1'),
            ), 
            array(
                'id'       => 'popupText',
                'type'     => 'editor',
                'title'    => __('Popup Text', 'wordpress-gdpr'),
                'default'  => __('We use cookies to give you the best online experience. By agreeing you accept the use of cookies in accordance with our cookie policy.', 'wordpress-gdpr'),
                'required' => array('popupEnable','equals','1'),
            ),
            array(
                'id'       => 'popupTextAgree',
                'type'     => 'text',
                'title'    => __('Popup Agree Text', 'wordpress-gdpr'),
                'subtitle' => __('Leave Empty if not needed', 'wordpress-gdpr'),
                'default'  => __('I accept', 'wordpress-gdpr'),
                'required' => array('popupEnable','equals','1'),
            ),
            array(
                'id'       => 'popupTextDecline',
                'type'     => 'text',
                'title'    => __('Popup Decline Text', 'wordpress-gdpr'),
                'subtitle' => __('Leave Empty if not needed', 'wordpress-gdpr'),
                'default'  => __('I decline', 'wordpress-gdpr'),
                'required' => array('popupEnable','equals','1'),
            ),
            array(
                'id'       => 'popupTextPrivacyCenter',
                'type'     => 'text',
                'title'    => __('Popup Privacy Center Text', 'wordpress-gdpr'),
                'subtitle' => __('Leave Empty if not needed', 'wordpress-gdpr'),
                'default'  => __('Privacy Center', 'wordpress-gdpr'),
                'required' => array('popupEnable','equals','1'),
            ),
            array(
                'id'       => 'popupTextPrivacySettings',
                'type'     => 'text',
                'title'    => __('Popup Privacy Settings Text', 'wordpress-gdpr'),
                'subtitle' => __('Leave Empty if not needed', 'wordpress-gdpr'),
                'default'  => __('Privacy Settings', 'wordpress-gdpr'),
                'required' => array('popupEnable','equals','1'),
            ),
            array(
                'id'       => 'popupTextCookiePolicy',
                'type'     => 'text',
                'title'    => __('Popup Cookie Policy Text', 'wordpress-gdpr'),
                'subtitle' => __('Leave Empty if not needed', 'wordpress-gdpr'),
                'default'  => __('Cookie Policy', 'wordpress-gdpr'),
                'required' => array('popupEnable','equals','1'),
            ),
            array(
                'id'       => 'popupStyle',
                'type'     => 'select',
                'title'    => __('Popup Style', 'wordpress-gdpr'),
                'options' => array(
                    'wordpress-gdpr-popup-full-width' => __('Full Width', 'wordpress-gdpr'),
                    'wordpress-gdpr-popup-small' => __('Small Width', 'wordpress-gdpr'),
                    'wordpress-gdpr-popup-overlay' => __('Overlay', 'wordpress-gdpr'),
                ),
                'default' => 'wordpress-gdpr-popup-overlay',
                'required' => array('popupEnable','equals','1'),
            ),
            array(
                'id'       => 'popupPosition',
                'type'     => 'select',
                'title'    => __('Popup Position', 'wordpress-gdpr'),
                'options' => array(
                    'wordpress-gdpr-popup-top' => __('Top', 'wordpress-gdpr'),
                    'wordpress-gdpr-popup-bottom' => __('Bottom', 'wordpress-gdpr'),
                ),
                'default' => 'wordpress-gdpr-popup-bottom',
                'required' => array('popupEnable','equals','1'),
            ),
            array(
                'id'     =>'popupBackgroundColor',
                'type' => 'color',
                'title' => __('Background Color', 'wordpress-gdpr'), 
                'validate' => 'color',
                'default' => '#f7f7f7',
                'required' => array('popupEnable','equals','1'),
            ),
            array(
                'id'     =>'popupTextColor',
                'type' => 'color',
                'title' => __('Text Color', 'wordpress-gdpr'), 
                'validate' => 'color',
                'default' => '#333333',
                'required' => array('popupEnable','equals','1'),
            ),
            array(
                'id'     =>'popupAgreeColor',
                'type' => 'color',
                'title' => __('Accept Button Text Color', 'wordpress-gdpr'), 
                'validate' => 'color',
                'default' => '#FFFFFF',
                'required' => array('popupEnable','equals','1'),
            ),
            array(
                'id'     =>'popupAgreeBackgroundColor',
                'type' => 'color',
                'title' => __('Accept Button Background Color', 'wordpress-gdpr'), 
                'validate' => 'color',
                'default' => '#4CAF50',
                'required' => array('popupEnable','equals','1'),
            ),
            array(
                'id'     =>'popupDeclineColor',
                'type' => 'color',
                'title' => __('Decline Button Text Color', 'wordpress-gdpr'), 
                'validate' => 'color',
                'default' => '#FFFFFF',
                'required' => array('popupEnable','equals','1'),
            ),
            array(
                'id'     =>'popupDeclineBackgroundColor',
                'type' => 'color',
                'title' => __('Decline Button Background Color', 'wordpress-gdpr'), 
                'validate' => 'color',
                'default' => '#777777',
                'required' => array('popupEnable','equals','1'),
            ),
            array(
                'id'     =>'popupLinkColor',
                'type' => 'color',
                'title' => __('Link Color', 'wordpress-gdpr'), 
                'validate' => 'color',
                'default' => '#FF5722',
                'required' => array('popupEnable','equals','1'),
            ),
            array(
                'id'       => 'popupCloseIcon',
                'type'     => 'text',
                'title'    => __('Close Icon', 'wordpress-gdpr'),
                'default'  => 'fa fa-times',
                'required' => array('popupEnable','equals','1'),
            ),
            array(
                'id'     =>'popupCloseIconBackgroundColor',
                'type' => 'color',
                'title' => __('Close Icon Background Color', 'wordpress-gdpr'), 
                'validate' => 'color',
                'default' => '#000000',
                'required' => array('popupEnable','equals','1'),
            ),
            array(
                'id'     =>'popupCloseIconColor',
                'type' => 'color',
                'title' => __('Close Icon Color', 'wordpress-gdpr'), 
                'validate' => 'color',
                'default' => '#FFFFFF',
                'required' => array('popupEnable','equals','1'),
            ),
        )
    ));

    Redux::setSection($opt_name, array(
        'title'      => __('Privacy Settings', 'wordpress-gdpr'),
        'id'         => 'privacy-settings-popup-settings',
        'subsection' => true,
        'fields'     => array(
            array(
               'id' => 'privacy-settings-trigger',
               'type' => 'section',
               'title' => __('Privacy Settings Trigger', 'wordpress-gdpr'),
               'subtitle' => __('This is the fixed button on bottom right for example to open the privacy settings popup.', 'wordpress-gdpr'),
               'indent' => false 
            ),
            array(
                'id'       => 'privacySettingsTriggerEnable',
                'type'     => 'switch',
                'title'    => __('Enable the Privacy Settings Trigger', 'wordpress-gdpr'),
                'default'  => '1',
            ),
            array(
                'id'       => 'privacySettingsTriggerText',
                'type'     => 'text',
                'title'    => __('Trigger Icon HTML', 'wordpress-gdpr'),
                'default'  => __('<i class="fa fa-lg fa-user-secret"></i>', 'wordpress-gdpr'),
                'required' => array('privacySettingsTriggerEnable','equals','1'),
            ),
            array(
                'id'     =>'privacySettingsTriggerBackgroundColor',
                'type' => 'color',
                'title' => __('Background Color', 'wordpress-gdpr'), 
                'validate' => 'color',
                'default' => '#0d0d0d',
                'required' => array('privacySettingsTriggerEnable','equals','1'),
            ),
            array(
                'id'     =>'privacySettingsTriggerTextColor',
                'type' => 'color',
                'title' => __('Text Color', 'wordpress-gdpr'), 
                'validate' => 'color',
                'default' => '#FFFFFF',
                'required' => array('privacySettingsTriggerEnable','equals','1'),
            ),
            array(
                'id'       => 'privacySettingsTriggerPosition',
                'type'     => 'select',
                'title'    => __('Trigger Position', 'wordpress-gdpr'),
                'options' => array(
                    'wordpress-gdpr-privacy-settings-trigger-bottom-left' => __('Bottom Left', 'wordpress-gdpr'),
                    'wordpress-gdpr-privacy-settings-trigger-bottom-right' => __('Bottom Right', 'wordpress-gdpr'),
                ),
                'default' => 'wordpress-gdpr-privacy-settings-trigger-bottom-right',
                'required' => array('privacySettingsTriggerEnable','equals','1'),
            ),
            array(
               'id' => 'privacy-settings-texts',
               'type' => 'section',
               'title' => __('Privacy Settings texts', 'wordpress-gdpr'),
               'subtitle' => __('Set up your privacy settings texts here.', 'wordpress-gdpr'),
               'indent' => false 
            ),
            array(
                'id'        =>'privacySettingsPopupLogo',
                'type'      => 'media',
                'url'       => true,
                'title'     => __('Set a Logo', 'wordpress-helpdesk'),
                'subtitle'  => __('The logo will be used at top left of the privacy settings popup.', 'wordpress-helpdesk'),
                'args'      => array(
                    'teeny'            => false,
                ),
            ),
            array(
                'id'       => 'privacySettingsPopupTitle',
                'type'     => 'text',
                'title'    => __('Popup Title', 'wordpress-gdpr'),
                'default'  => __('Privacy Settings', 'wordpress-gdpr'),
            ),
            array(
                'id'       => 'privacySettingsPopupDescription',
                'type'     => 'editor',
                'title'    => __('Popup Description', 'wordpress-gdpr'),
                'default'  => __('When you visit any web site, it may store or retrieve information on your browser, mostly in the form of cookies. Control your personal Cookie Services here.', 'wordpress-gdpr'),
            ),
            array(
                'id'       => 'privacySettingsPopupTextPrivacyCenter',
                'type'     => 'text',
                'title'    => __('Popup Privacy Center Text', 'wordpress-gdpr'),
                'subtitle' => __('Leave Empty if not needed', 'wordpress-gdpr'),
                'default'  => __('Privacy Center', 'wordpress-gdpr'),
            ),
            array(
                'id'       => 'privacySettingsPopupTextPrivacyPolicy',
                'type'     => 'text',
                'title'    => __('Popup Privacy Policy Text', 'wordpress-gdpr'),
                'subtitle' => __('Leave Empty if not needed', 'wordpress-gdpr'),
                'default'  => __('Privacy Policy', 'wordpress-gdpr'),
            ),
            array(
                'id'       => 'privacySettingsPopupTextCookiePolicy',
                'type'     => 'text',
                'title'    => __('Popup Cookie Policy Text', 'wordpress-gdpr'),
                'subtitle' => __('Leave Empty if not needed', 'wordpress-gdpr'),
                'default'  => __('Cookie Policy', 'wordpress-gdpr'),
            ),
            array(
               'id' => 'privacy-settings-shortcode',
               'type' => 'section',
               'title' => __('Privacy Settings Shortcode', 'wordpress-gdpr'),
               'subtitle' => __('Use Shortcode instead of popup.', 'wordpress-gdpr'),
               'indent' => false 
            ),
            array(
                'id'       => 'privacySettingsUseShortcode',
                'type'     => 'checkbox',
                'title'    => __('Use Privacy Settings Shortcode', 'wordpress-gdpr'),
                'subtitle' => __('If enabled you need to add the following shortcode to a new page: [wordpress_gdpr_privacy_settings].', 'wordpress-gdpr'),
                'default'  => '0',
            ),
            array(
                'id'       => 'privacySettingsUseShortcodePage',
                'type'     => 'select',
                'title'    => __('Privacy Settings Page', 'wordpress-gdpr'),
                'subtitle' => __('Set the page you created with the shortcode above here. Trigger and Privacy center will link there.', 'wordpress-gdpr'),
                // 'options'  => $wordpress_gdpr_pages,
                'data'     => 'pages',
                'ajax'     => true,
                'required' => array('privacySettingsUseShortcode','equals','1'),
            ),  
            array(
               'id' => 'privacy-settings-modal',
               'type' => 'section',
               'title' => __('Privacy Settings Modal', 'wordpress-gdpr'),
               'subtitle' => __('Set up your privacy settings modal here.', 'wordpress-gdpr'),
               'indent' => false 
            ),
            array(
                'id'       => 'privacySettingsPopupEnable',
                'type'     => 'switch',
                'title'    => __('Enable the Privacy Settings Popup', 'wordpress-gdpr'),
                'default'  => '1',
            ),
            array(
                'id'     =>'privacySettingsPopupBackgroundColor',
                'type' => 'color',
                'title' => __('Background Color', 'wordpress-gdpr'), 
                'validate' => 'color',
                'default' => '#FFFFFF',
                'required' => array('privacySettingsPopupEnable','equals','1'),
            ),
            array(
                'id'     =>'privacySettingsPopupTextColor',
                'type' => 'color',
                'title' => __('Text Color', 'wordpress-gdpr'), 
                'validate' => 'color',
                'default' => '#333333',
                'required' => array('privacySettingsPopupEnable','equals','1'),
            ),
            array(
                'id'     =>'privacySettingsPopupAcceptColor',
                'type' => 'color',
                'title' => __('Accept Button Text Color', 'wordpress-gdpr'), 
                'validate' => 'color',
                'default' => '#FFFFFF',
                'required' => array('privacySettingsPopupEnable','equals','1'),
            ),
            array(
                'id'     =>'privacySettingsPopupAcceptBackgroundColor',
                'type' => 'color',
                'title' => __('Accept Button Background Color', 'wordpress-gdpr'), 
                'validate' => 'color',
                'default' => '#4CAF50',
                'required' => array('privacySettingsPopupEnable','equals','1'),
            ),
            array(
                'id'     =>'privacySettingsPopupDeclineColor',
                'type' => 'color',
                'title' => __('Decline Button Text Color', 'wordpress-gdpr'), 
                'validate' => 'color',
                'default' => '#FFFFFF',
                'required' => array('privacySettingsPopupEnable','equals','1'),
            ),
            array(
                'id'     =>'privacySettingsPopupDeclineBackgroundColor',
                'type' => 'color',
                'title' => __('Decline Button Background Color', 'wordpress-gdpr'), 
                'validate' => 'color',
                'default' => '#777777',
                'required' => array('privacySettingsPopupEnable','equals','1'),
            ),
            array(
                'id'       => 'privacySettingsPopupCloseIcon',
                'type'     => 'text',
                'title'    => __('Popup Close Icon', 'wordpress-gdpr'),
                'default'  => 'fa fa-times',
                'required' => array('privacySettingsPopupEnable','equals','1'),
            ),
            array(
                'id'     =>'privacySettingsPopupCloseIconBackgroundColor',
                'type' => 'color',
                'title' => __('Background Color', 'wordpress-gdpr'), 
                'validate' => 'color',
                'default' => '#000000',
                'required' => array('privacySettingsPopupEnable','equals','1'),
            ),
            array(
                'id'     =>'privacySettingsPopupCloseIconColor',
                'type' => 'color',
                'title' => __('Icon Color', 'wordpress-gdpr'), 
                'validate' => 'color',
                'default' => '#FFFFFF',
                'required' => array('privacySettingsPopupEnable','equals','1'),
            ),
        )
    ));

    Redux::setSection($opt_name, array(
        'title'      => __('Privacy Center', 'wordpress-gdpr'),
        'id'         => 'privacyCenter',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'privacyCenterEnable',
                'type'     => 'switch',
                'title'    => __('Enable Privacy Center', 'wordpress-gdpr'),
                'subtitle' => __('Enable Privacy Center', 'wordpress-gdpr'),
                'default'  => '1',
            ),
            array(
                'id'       => 'privacyCenterPage',
                'type'     => 'select',
                'title'    => __('Privacy Center Page', 'wordpress-gdpr'),
                'subtitle' => __('Shortcode: [wordpress_gdpr_privacy_center] This will be the page, where the privacy center will be shown.', 'wordpress-gdpr'),
                // 'options'  => $wordpress_gdpr_pages,
                'data'     => 'pages',
                'ajax'     => true,
                'required' => array('privacyCenterEnable','equals','1'),
            ),         
        )
    ));

    Redux::setSection($opt_name, array(
        'title'      => __('Confirmation Email', 'wordpress-gdpr'),
        'id'         => 'confirmationEmail',
        'subsection' => true,
        'fields'     => array(
           array(
                'id'       => 'confirmationEmailInfo',
                'type'     => 'info',
                'title'    => __('Info', 'wordpress-gdpr'),
                'desc' => __('If you have enabled to use WP Core Functions in advanced settings this is no longer needed.', 'wordpress-gdpr'),
            ),
            array(
                'id'       => 'confirmationEmailSubject',
                'type'     => 'text',
                'title'    => __('Confirmation Email Subject', 'wordpress-gdpr'),
                'subtitle' => __('Subject of the confirmation Email.', 'wordpress-gdpr'),
                'default'  => $site_name . ' - ' . __('GDPR - Confirm your Request', 'wordpress-gdpr'),
                'required' => array('useWPCoreFunctions','equals','0'),
            ),
            array(
                'id'       => 'confirmationEmailText',
                'type'     => 'editor',
                'title'    => __('Confirmation Email Text', 'wordpress-gdpr'),
                'subtitle' => __('Text of the Email send out to the user to confirm his email to create a new request.', 'wordpress-gdpr'),
                'default'  => __('Dear %s,
                                <br><br>
                                we have received your %s. If this is correct, please click on the link below. Otherwise ignore this email!', 'wordpress-gdpr'),
                'required' => array('useWPCoreFunctions','equals','0'),
            ),
        )
    ));

    Redux::setSection($opt_name, array(
        'title'      => __('Contact DPO', 'wordpress-gdpr'),
        'id'         => 'contactDPO',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'contactDPOEnable',
                'type'     => 'switch',
                'title'    => __('Enable Contact DPO', 'wordpress-gdpr'),
                'subtitle' => __('Enable Data Privacy Officer Contact form.', 'wordpress-gdpr'),
                'default'  => '1',
            ),
            array(
                'id'       => 'contactDPOPage',
                'type'     => 'select',
                'title'    => __('Contact DPO Page', 'wordpress-gdpr'),
                'subtitle' => __('Shortcode: [wordpress_gdpr_contact_dpo]', 'wordpress-gdpr'),
                // 'options'  => $wordpress_gdpr_pages,
                'data'     => 'pages',
                'ajax'     => true,
                'required' => array('contactDPOEnable','equals','1'),
            ),
            array(
                'id'       => 'contactDPODisableUserExistsCheck',
                'type'     => 'checkbox',
                'title'    => __('Disable User Exists Check', 'wordpress-gdpr'),
                'subtitle' => __('This allows all users to create requests for manually processing.', 'wordpress-gdpr'),
                'default'  => '1',
                'required' => array('contactDPOEnable','equals','1'),
            ),
            array(
                'id'       => 'contactDPOEmail',
                'type'     => 'text',
                'title'    => __('DPO Email', 'wordpress-gdpr'),
                'subtitle' => __('Emails will be sent there.', 'wordpress-gdpr'),
                'default'  => $default_email,
                'required' => array('contactDPOEnable','equals','1'),
            ),
            array(
                'id'       => 'contactDPOSubject',
                'type'     => 'text',
                'title'    => __('DPO Email Subject', 'wordpress-gdpr'),
                'subtitle' => __('Subject of the DPO contact Email.', 'wordpress-gdpr'),
                'default'  => $site_name . ' - ' . __('GDPR - New Request for DPO', 'wordpress-gdpr'),
                'required' => array('contactDPOEnable','equals','1'),
            ),
        )
    ));

    Redux::setSection($opt_name, array(
        'title'      => __('Cookie Policy', 'wordpress-gdpr'),
        'id'         => 'cookiePolicy',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'cookiePolicyEnable',
                'type'     => 'switch',
                'title'    => __('Enable Cookie Policy', 'wordpress-gdpr'),
                'subtitle' => __('Enable Cookie Policy Page', 'wordpress-gdpr'),
                'default'  => '1',
            ),
            array(
                'id'       => 'cookiePolicyPage',
                'type'     => 'select',
                'title'    => __('Cookie Policy Page', 'wordpress-gdpr'),
                'subtitle' => __('Add your Cookie Policy here.', 'wordpress-gdpr'),
                // 'options'  => $wordpress_gdpr_pages,
                'data'     => 'pages',
                'ajax'     => true,
                'required' => array('cookiePolicyEnable','equals','1'),
            ),         
        )
    ));

    Redux::setSection($opt_name, array(
        'title'      => __('Data Breach', 'wordpress-gdpr'),
        'id'         => 'data-breach',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'dataBreachEnable',
                'type'     => 'switch',
                'title'    => __('Enable Data Breach', 'wordpress-gdpr'),
                'subtitle' => __('Enable Data Breach form.', 'wordpress-gdpr'),
                'default'  => '1',
            ),
            array(
                'id'       => 'dataBreachEmail',
                'type'     => 'text',
                'title'    => __('Data Breach from Email', 'wordpress-gdpr'),
                'subtitle' => __('The FROM Email used for data breach issues.', 'wordpress-gdpr'),
                'default'  => $default_email,
                'required' => array('dataBreachEnable','equals','1'),
            ),
            array(
                'id'       => 'dataBreachSubject',
                'type'     => 'text',
                'title'    => __('Data Breach Email Subject', 'wordpress-gdpr'),
                'subtitle' => __('Subject of the Email.', 'wordpress-gdpr'),
                'default'  => $site_name . ' - ' . __('GDPR - Data Breach', 'wordpress-gdpr'),
                'required' => array('dataBreachEnable','equals','1'),
            ),
            array(
                'id'       => 'dataBreachText',
                'type'     => 'editor',
                'title'    => __('Data Breach Email Text', 'wordpress-gdpr'),
                'subtitle' => __('Text of the Email regarding a data breach..', 'wordpress-gdpr'),
                'default'  => __('Dear %s,
                                <br><br>
                                We value your business and respect the privacy of your information, which is why, as a
                                precautionary measure, we are writing to let you know about a data security incident that may
                                involve/involves your personal information.
                                <br><br>
                                Between XX and XX we discovered a foreign access to our data.', 'wordpress-gdpr'),
                'required' => array('dataBreachEnable','equals','1'),
            ),
            array(
                'id'   => 'dataBreachSend',
                'type' => 'info',
                'desc' => '<div style="text-align:center;">
                    <a href="' . get_admin_url() . 'admin.php?page=wordpress_gdpr_options_options&wordpress_gdpr[send-data-breach]=true" class="button button-success">' . __('Send Data Breach Email', 'wordpress-gdpr') . '</a>
                    </div>',
                'required' => array('dataBreachEnable','equals','1'),
            ),
        )
    ));

    Redux::setSection($opt_name, array(
        'title'      => __('Data Rectification', 'wordpress-gdpr'),
        'id'         => 'dataRectification',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'dataRectificationEnable',
                'type'     => 'switch',
                'title'    => __('Enable Data Rectification', 'wordpress-gdpr'),
                'subtitle' => __('Enable Data Rectification form.', 'wordpress-gdpr'),
                'default'  => '1',
            ),
            array(
                'id'       => 'dataRectificationPage',
                'type'     => 'select',
                'title'    => __('Data Rectification Page', 'wordpress-gdpr'),
                'subtitle' => __('Shortcode: [wordpress_gdpr_data_rectification]', 'wordpress-gdpr'),
                // 'options'  => $wordpress_gdpr_pages,
                'data'     => 'pages',
                'ajax'     => true,
                'required' => array('dataRectificationEnable','equals','1'),
            ),
            array(
                'id'       => 'dataRectificationDisableUserExistsCheck',
                'type'     => 'checkbox',
                'title'    => __('Disable User Exists Check', 'wordpress-gdpr'),
                'subtitle' => __('This allows all users to create requests for manually processing.', 'wordpress-gdpr'),
                'default'  => '1',
                'required' => array('dataRectificationEnable','equals','1'),
            ),
            array(
                'id'       => 'dataRectificationEmail',
                'type'     => 'text',
                'title'    => __('Data Rectification Email', 'wordpress-gdpr'),
                'subtitle' => __('Emails will be sent there.', 'wordpress-gdpr'),
                'default'  => $default_email,
                'required' => array('dataRectificationEnable','equals','1'),
            ),
            array(
                'id'       => 'dataRectificationSubject',
                'type'     => 'text',
                'title'    => __('Data Rectification Email Subject', 'wordpress-gdpr'),
                'subtitle' => __('Subject of the Data Rectification contact Email.', 'wordpress-gdpr'),
                'default'  => $site_name . ' - ' . __('GDPR - New Request for Data Rectification', 'wordpress-gdpr'),
                'required' => array('dataRectificationEnable','equals','1'),
            ),
        )
    ));

    Redux::setSection($opt_name, array(
        'title'      => __('Data Retention', 'wordpress-gdpr'),
        'id'         => 'data-retention',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'dataRetentionEnable',
                'type'     => 'switch',
                'title'    => __('Enable Data Retention', 'wordpress-gdpr'),
                'subtitle' => __('Enable Data Retention.', 'wordpress-gdpr'),
                'default'  => '0',
            ),
            array(
                'id'       => 'dataRetentionDays',
                'type'     => 'spinner',
                'title'    => __( 'Delete User after X Days.', 'wordpress-gdpr' ),
                'subtitle'    => __( 'When user has not logged in since X days, all his data will be deleted.', 'wordpress-gdpr' ),
                'min'      => '1',
                'step'     => '1',
                'max'      => '99999',
                'default'  => '1080',
                'required' => array('dataRetentionEnable','equals','1'),
            ),
            array(
                'id'   => 'dataRententionUpdateusers',
                'type' => 'info',
                'desc' => '<div style="text-align:center;">
                    <a href="' . get_admin_url() . 'admin.php?page=wordpress_gdpr_options_options&wordpress_gdpr[update-last-logged-in]=true" class="button button-success">' . __('Update the "last logged in"-Meta with Todays Time for Users without an existing value.', 'wordpress-gdpr') . '</a>
                    </div>',
                'required' => array('dataRetentionEnable','equals','1'),
            ),
        )
    ));

   Redux::setSection($opt_name, array(
        'title'      => __('Disclaimer', 'wordpress-gdpr'),
        'id'         => 'disclaimer',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'disclaimerEnable',
                'type'     => 'switch',
                'title'    => __('Enable Disclaimer', 'wordpress-gdpr'),
                'subtitle' => __('Enable Disclaimer form.', 'wordpress-gdpr'),
                'default'  => '1',
            ),
            array(
                'id'       => 'disclaimerPage',
                'type'     => 'select',
                'title'    => __('Disclaimer Page', 'wordpress-gdpr'),
                'subtitle' => __('Set your Disclaimer Page here.', 'wordpress-gdpr'),
                // 'options' => $wordpress_gdpr_pages,
                'data'     => 'pages',
                'ajax'     => true,
                'required' => array('disclaimerEnable','equals','1'),
            ),
        )
    ));

   Redux::setSection($opt_name, array(
        'title'      => __('DMCA', 'wordpress-gdpr'),
        'id'         => 'DMCA',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'DMCAEnable',
                'type'     => 'switch',
                'title'    => __('Enable DMCA', 'wordpress-gdpr'),
                'subtitle' => __('Enable DMCA form.', 'wordpress-gdpr'),
                'default'  => '0',
            ),
            array(
                'id'       => 'DMCAPage',
                'type'     => 'select',
                'title'    => __('DMCA Page', 'wordpress-gdpr'),
                'subtitle' => __('Place your DMCA Contact form on this page.', 'wordpress-gdpr'),
                // 'options'  => $wordpress_gdpr_pages,
                'data'     => 'pages',
                'ajax'     => true,
                'required' => array('DMCAEnable','equals','1'),
            ),
        )
    ));


    Redux::setSection($opt_name, array(
        'title'      => __('Forget Me', 'wordpress-gdpr'),
        'id'         => 'forgetMe',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'forgetMeEnable',
                'type'     => 'switch',
                'title'    => __('Enable Forget Me', 'wordpress-gdpr'),
                'subtitle' => __('Enable Forget Me form.', 'wordpress-gdpr'),
                'default'  => '1',
            ),
            array(
                'id'       => 'forgetMePage',
                'type'     => 'select',
                'title'    => __('Forget Me Page', 'wordpress-gdpr'),
                'subtitle' => __('Shortcode: [wordpress_gdpr_forget_me]', 'wordpress-gdpr'),
                // 'options'  => $wordpress_gdpr_pages,
                'data'     => 'pages',
                'ajax'     => true,
                'required' => array('forgetMeEnable','equals','1'),
            ),
            array(
                'id'       => 'forgetMeLoggedInButton',
                'type'     => 'checkbox',
                'title'    => __('Show Delete Data Button for Logged in', 'wordpress-gdpr'),
                'subtitle' => __('This will show logged in users a button where they can delete data theirselves.', 'wordpress-gdpr'),
                'default'  => '0',
                'required' => array('forgetMeEnable','equals','1'),
            ),
            array(
                'id'       => 'forgetMeDisableUserExistsCheck',
                'type'     => 'checkbox',
                'title'    => __('Disable User Exists Check', 'wordpress-gdpr'),
                'subtitle' => __('This allows all users to create requests for manually processing.', 'wordpress-gdpr'),
                'default'  => '0',
                'required' => array(
                    array('forgetMeEnable','equals','1'),
                    array('useWPCoreFunctions','equals','0')
                ),
            ),
            array(
                'id'       => 'forgetMeDeletePosts',
                'type'     => 'checkbox',
                'title'    => __('Delete Posts & Pages (& all other post types)', 'wordpress-gdpr'),
                'default'  => '0',
                'required' => array('forgetMeEnable','equals','1'),
            ),
            array(
                'id'       => 'forgetMeDeleteComments',
                'type'     => 'checkbox',
                'title'    => __('Delete Comments', 'wordpress-gdpr'),
                'default'  => '1',
                'required' => array('forgetMeEnable','equals','1'),
            ),
            // array(
            //     'id'       => 'forgetMeReassignUser',
            //     'type'     => 'select',
            //     'title'    => __('Reassign Posts to User', 'wordpress-gdpr'),
            //     'subtitle' => __('Posts created by the user to be deleted will be reassigned to this user.', 'wordpress-gdpr'),
            //     'data' => 'users',
            // ),
            array(
                'id'       => 'forgetMeSubject',
                'type'     => 'text',
                'title'    => __('Forget me Deleted Email Subject', 'wordpress-gdpr'),
                'subtitle' => __('Subject of the Email.', 'wordpress-gdpr'),
                'default'  => $site_name . ' - ' . __('GDPR - Your Data has been Deleted', 'wordpress-gdpr'),
                'required' => array('forgetMeEnable','equals','1'),
            ),
            array(
                'id'       => 'forgetMeText',
                'type'     => 'editor',
                'title'    => __('Forget me Deleted Email Text', 'wordpress-gdpr'),
                'subtitle' => __('Text of the Email informing the user, that his data has been deleted.', 'wordpress-gdpr'),
                'default'  => __('Dear %s,
                                <br><br>
                                we have successfully deleted all your personal data from our Website.', 'wordpress-gdpr'),
                'required' => array('forgetMeEnable','equals','1'),
            ),
        )
    ));

    Redux::setSection($opt_name, array(
        'title'      => __('Imprint', 'wordpress-gdpr'),
        'id'         => 'imprint',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'imprintEnable',
                'type'     => 'switch',
                'title'    => __('Enable Imprint', 'wordpress-gdpr'),
                'subtitle' => __('Enable Imprint Page', 'wordpress-gdpr'),
                'default'  => '1',
            ),
            array(
                'id'       => 'imprintPage',
                'type'     => 'select',
                'title'    => __('Imprint Page', 'wordpress-gdpr'),
                'subtitle' => __('Add your Imprint here.', 'wordpress-gdpr'),
                // 'options'  => $wordpress_gdpr_pages,
                'data'     => 'pages',
                'ajax'     => true,
                'required' => array('imprintEnable','equals','1'),
            ),         
        )
    ));

    // WooCommerce
    // Contact Form 7
    // Mailchimp
    // BuddyPress
    Redux::setSection($opt_name, array(
        'title'      => __('Integrations', 'wordpress-gdpr'),
        'id'         => 'integrations',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'integrationsComments',
                'type'     => 'switch',
                'title'    => __('Enable Comments', 'wordpress-gdpr'),
                'subtitle' => __('Activated this will add a checkbox to comment form to accept your privay policy.', 'wordpress-gdpr'),
                'default'  => '0',
            ),
            array(
                'id'       => 'integrationsCommentsOnlyRegistered',
                'type'     => 'checkbox',
                'title'    => __('Only registered users can comment', 'wordpress-gdpr'),
                'subtitle' => __('Either you go to settings > discussions and check "Users must be registered" or you activate this setting here. If you do not do this, user deletion & export will not work, because no accounts are created!', 'wordpress-gdpr'),
                'default'  => '0',
                'required' => array('integrationsComments','equals','1'),
            ),
            array(
                'id'       => 'integrationsCommentsPolicyPage',
                'type'     => 'select',
                'title'    => __('Comments Privacy Policy Page', 'wordpress-gdpr'),
                'subtitle' => __('Assign your privacy policy page here. You could also use a custom one for Comments', 'wordpress-gdpr'),
                // 'options'  => $wordpress_gdpr_pages,
                'data'     => 'pages',
                'ajax'     => true,
                'required' => array('integrationsComments','equals','1'),
            ),
            array(
                'id'       => 'integrationsCommentsPolicyAcceptText',
                'type'     => 'text',
                'title'    => __('Privacy Policy Accept text', 'wordpress-gdpr'),
                'subtitle' => __('Text for users to agree on privacy policy on checkout page.', 'wordpress-gdpr'),
                'default'  => __('I’ve read and accept the privacy policy.', 'wordpress-gdpr'),
                'required' => array('integrationsComments','equals','1'),
            ),
            array(
                'id'       => 'integrationsWooCommerce',
                'type'     => 'switch',
                'title'    => __('Enable WooCommerce', 'wordpress-gdpr'),
                'subtitle' => __('Activated this will add a checkbox to the checkout, registration and product review form to accept your privay policy. It also adds a menu item to the my account page for the privacy center. In addtion to this make sure you have setup a terms and conditions page in WooCommerce settings. With WooCommerce Version 3.4. you may not need this anymore', 'wordpress-gdpr'),
                'default'  => '0',
            ),
            array(
                'id'       => 'integrationsWooCommerceForgetMe',
                'type'     => 'checkbox',
                'title'    => __('Delete WooCommerce Orders', 'wordpress-gdpr'),
                'subtitle' => __('If enabled WooCommerce Orders will be deleted on Forget Me Request.', 'wordpress-gdpr'),
                'default'  => '0',
                'required' => array('integrationsWooCommerce','equals','1'),
            ),
            array(
                'id'       => 'integrationsWooCommerceCheckoutCheckbox',
                'type'     => 'checkbox',
                'title'    => __('Privacy Acceptance Checkbox on Checkout', 'wordpress-gdpr'),
                'subtitle' => __('Adds a privacy acceptance checkbox on checkout.', 'wordpress-gdpr'),
                'default'  => '0',
                'required' => array('integrationsWooCommerce','equals','1'),
            ),
            array(
                'id'       => 'integrationsWooCommerceRegistrationCheckbox',
                'type'     => 'checkbox',
                'title'    => __('Privacy Acceptance Checkbox on Registration', 'wordpress-gdpr'),
                'subtitle' => __('Adds a privacy acceptance checkbox on Registration.', 'wordpress-gdpr'),
                'default'  => '0',
                'required' => array('integrationsWooCommerce','equals','1'),
            ),
            array(
                'id'       => 'integrationsWooCommerceDisableGuestCheckout',
                'type'     => 'checkbox',
                'title'    => __('Disable Guest Checkout', 'wordpress-gdpr'),
                'subtitle' => __('Either you go to WooCommerce > Settings > Checkout and disable Guest Checkout or you activate this setting here. If you do not do this, user deletion & export will not work, because no accounts are created!', 'wordpress-gdpr'),
                'default'  => '0',
                'required' => array('integrationsWooCommerce','equals','1'),
            ),
            array(
                'id'       => 'integrationsWooCommercePolicyPage',
                'type'     => 'select',
                'title'    => __('WooCommerce Privacy Policy Page', 'wordpress-gdpr'),
                'subtitle' => __('Assign your privacy policy page here. You could also use a custom one for WooCommerce', 'wordpress-gdpr'),
                // 'options'  => $wordpress_gdpr_pages,
                'data'     => 'pages',
                'ajax'     => true,
                'required' => array('integrationsWooCommerce','equals','1'),
            ),
            array(
                'id'       => 'integrationsWooCommercePolicyAcceptText',
                'type'     => 'text',
                'title'    => __('Privacy Policy Accept text', 'wordpress-gdpr'),
                'subtitle' => __('Text for users to agree on privacy policy on checkout page.', 'wordpress-gdpr'),
                'default'  => __('I’ve read and accept the privacy policy.', 'wordpress-gdpr'),
                'required' => array('integrationsWooCommerce','equals','1'),
            ),
            array(
                'id'       => 'integrationsBuddyPress',
                'type'     => 'switch',
                'title'    => __('Enable BuddyPress', 'wordpress-gdpr'),
                'subtitle' => __('This will add checkbox to BuddyPress Account registration form.', 'wordpress-gdpr'),
                'default'  => '0',
            ),
            array(
                'id'       => 'integrationsBuddyPressPolicyPage',
                'type'     => 'select',
                'title'    => __('BuddyPress Privacy Policy Page', 'wordpress-gdpr'),
                'subtitle' => __('Assign your privacy policy page here. You could also use a custom one for BuddyPress', 'wordpress-gdpr'),
                // 'options'  => $wordpress_gdpr_pages,
                'data'     => 'pages',
                'ajax'     => true,
                'required' => array('integrationsBuddyPress','equals','1'),
            ),
            array(
                'id'       => 'integrationsBuddyPressPolicyAcceptText',
                'type'     => 'text',
                'title'    => __('Privacy Policy Accept text', 'wordpress-gdpr'),
                'subtitle' => __('Text for users to agree on privacy policy on BuddyPress Registration page.', 'wordpress-gdpr'),
                'default'  => __('By registering an Account I have read and accept the privacy policy.', 'wordpress-gdpr'),
                'required' => array('integrationsBuddyPress','equals','1'),
            ),
            array(
                'id'       => 'integrationsCF7',
                'type'     => 'switch',
                'title'    => __('Enable CF7', 'wordpress-gdpr'),
                'subtitle' => __('Make sure you add a checkbox to your forms to accept privacy policy.', 'wordpress-gdpr'),
                'default'  => '0',
            ),
            array(
                'id'       => 'integrationsFlamingoDB',
                'type'     => 'switch',
                'title'    => __('Enable Flamingo DB (Deprecated)', 'wordpress-gdpr'),
                'subtitle' => __('No longer needed with CF7 Version 5.0.3 - <a target="_blank" href="https://plugins.db-dzine.com/wordpress-gdpr/documentation/faq/cf7/">see here</a>. This will depending on the opt-in checkbox setting save data or not.', 'wordpress-gdpr'),
                'default'  => '0',
            ),
            array(
                'id'       => 'integrationsFlamingoDBField',
                'type'     => 'text',
                'title'    => __('Opt-In Field name', 'wordpress-gdpr'),
                'subtitle' => __('Enter the field name for the opt in checkbox here.', 'wordpress-gdpr'),
                'default'  => __('opt-in', 'wordpress-gdpr'),
                'required' => array('integrationsFlamingoDB','equals','1'),
            ),
            array(
                'id'       => 'integrationsFlamingoDBForgetMe',
                'type'     => 'checkbox',
                'title'    => __('Delete Flamingo DB Records', 'wordpress-gdpr'),
                'subtitle' => __('On Forget Me Request, delete Flamingo DB records.', 'wordpress-gdpr'),
                'default'  => '0',
                'required' => array('integrationsFlamingoDB','equals','1'),
            ),
            array(
                'id'       => 'integrationsQuform',
                'type'     => 'switch',
                'title'    => __('Enable Quform', 'wordpress-gdpr'),
                'subtitle' => __('This will add Quform entries to data export. Checkbox needs to be added to each form manually.', 'wordpress-gdpr'),
                'default'  => '0',
            ),
            array(
                'id'       => 'integrationsQuformForgetMe',
                'type'     => 'checkbox',
                'title'    => __('Delete Quform Records', 'wordpress-gdpr'),
                'subtitle' => __('On Forget Me Request, delete Quform records.', 'wordpress-gdpr'),
                'default'  => '0',
                'required' => array('integrationsQuform','equals','1'),
            ),
            array(
                'id'       => 'integrationsFormidable',
                'type'     => 'switch',
                'title'    => __('Enable Formidable', 'wordpress-gdpr'),
                'subtitle' => __('This will add Formidable entries to data export. Checkbox needs to be added to each form manually.', 'wordpress-gdpr'),
                'default'  => '0',
            ),
            array(
                'id'       => 'integrationsFormidableForgetMe',
                'type'     => 'checkbox',
                'title'    => __('Delete Formidable Records', 'wordpress-gdpr'),
                'subtitle' => __('On Forget Me Request, delete Formidable records.', 'wordpress-gdpr'),
                'default'  => '0',
                'required' => array('integrationsFormidable','equals','1'),
            ),
            array(
                'id'       => 'integrationsGravityForms',
                'type'     => 'switch',
                'title'    => __('Enable GravityForms', 'wordpress-gdpr'),
                'subtitle' => __('This will add Gravity Forms entries to data export & removes IP saving. Checkbox needs to be added to each form manually.', 'wordpress-gdpr'),
                'default'  => '0',
            ),
            array(
                'id'       => 'integrationsGravityFormsForgetMe',
                'type'     => 'checkbox',
                'title'    => __('Delete GravityForms Records', 'wordpress-gdpr'),
                'subtitle' => __('On Forget Me Request, delete Gravity Forms records.', 'wordpress-gdpr'),
                'default'  => '0',
                'required' => array('integrationsGravityForms','equals','1'),
            ),
            array(
                'id'       => 'integrationsMailster',
                'type'     => 'switch',
                'title'    => __('Enable Mailster', 'wordpress-gdpr'),
                'subtitle' => __('Activate our Mailster Integration.', 'wordpress-gdpr'),
                'default'  => '0',
            ),
            array(
                'id'       => 'integrationsMailsterCheckbox',
                'type'     => 'checkbox',
                'title'    => __('Enable Mailster Checkbox', 'wordpress-gdpr'),
                'subtitle' => __('This will add checkbox to Mailster Subscribe form.', 'wordpress-gdpr'),
                'default'  => '0',
                'required' => array('integrationsMailster','equals','1'),
            ),
            array(
                'id'       => 'integrationsPixelYourSite',
                'type'     => 'switch',
                'title'    => __('Enable PixelYourSite', 'wordpress-gdpr'),
                'subtitle' => __('Disable PixelYourSite Loading if no service found. Make sure you add a service and check the pixelyoursite checkbox. This does not work with caching due to PixelYourSite plugin.', 'wordpress-gdpr'),
                'default'  => '0',
            ),
            array(
                'id'       => 'integrationsMailsterPolicyPage',
                'type'     => 'select',
                'title'    => __('Mailster Privacy Policy Page', 'wordpress-gdpr'),
                'subtitle' => __('Assign your privacy policy page here. You could also use a custom one for Mailster', 'wordpress-gdpr'),
                // 'options'  => $wordpress_gdpr_pages,
                'data'     => 'pages',
                'ajax'     => true,
                'required' => array('integrationsMailsterCheckbox','equals','1'),
            ),
            array(
                'id'       => 'integrationsMailsterPolicyAcceptText',
                'type'     => 'text',
                'title'    => __('Privacy Policy Accept text', 'wordpress-gdpr'),
                'subtitle' => __('Text for users to agree on privacy policy on Mailster Registration page.', 'wordpress-gdpr'),
                'default'  => __('By subscribing to this newsletter I have read and accept the privacy policy.', 'wordpress-gdpr'),
                'required' => array('integrationsMailsterCheckbox','equals','1'),
            ),
        )
    ));

    Redux::setSection($opt_name, array(
        'title'      => __('Media Credits', 'wordpress-gdpr'),
        'id'         => 'mediaCredits',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'mediaCreditsEnable',
                'type'     => 'switch',
                'title'    => __('Enable Media Credits', 'wordpress-gdpr'),
                'subtitle' => __('Enable Media Credits page where you can inform about image + author + license etc.', 'wordpress-gdpr'),
                'default'  => '1',
            ),
            array(
                'id'       => 'mediaCreditsPage',
                'type'     => 'select',
                'title'    => __('Media Credits Page', 'wordpress-gdpr'),
                'subtitle' => __('Place your Media Credits there.', 'wordpress-gdpr'),
                // 'options'  => $wordpress_gdpr_pages,
                'data'     => 'pages',
                'ajax'     => true,
                'required' => array('mediaCreditsEnable','equals','1'),
            ),
        )
    ));

    Redux::setSection($opt_name, array(
        'title'      => __('Privacy Policy', 'wordpress-gdpr'),
        'id'         => 'privacyPolicy',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'privacyPolicyEnable',
                'type'     => 'switch',
                'title'    => __('Enable Privacy Policy', 'wordpress-gdpr'),
                'subtitle' => __('Enable Privacy Policy', 'wordpress-gdpr'),
                'default'  => '1',
            ),
            array(
                'id'       => 'privacyPolicyPage',
                'type'     => 'select',
                'title'    => __('Privacy Policy Page', 'wordpress-gdpr'),
                'subtitle' => __('Add your privacy policy to this page!', 'wordpress-gdpr'),
                // 'options'  => $wordpress_gdpr_pages,
                'data'     => 'pages',
                'ajax'     => true,
                'required' => array('privacyPolicyEnable','equals','1'),
            ),
            array(
                'id'       => 'privacyPolicyAccept',
                'type'     => 'checkbox',
                'title'    => __('Show Accept Checkbox', 'wordpress-gdpr'),
                'subtitle' => __('Place the following Shortcode at the bottom of your privacy policy: [wordpress_gdpr_privacy_policy_accept]', 'wordpress-gdpr'),
                'default'  => '0',
                'required' => array('privacyPolicyEnable','equals','1'),
            ),
            array(
                'id'       => 'privacyPolicyAcceptText',
                'type'     => 'text',
                'title'    => __('Privacy Policy Accept text', 'wordpress-gdpr'),
                'subtitle' => __('Text for users to agree on privacy policy.', 'wordpress-gdpr'),
                'default'  => __('I’ve read and accept the privacy policy.', 'wordpress-gdpr'),
                'required' => array('privacyPolicyAccept','equals','1'),
            ),
        )
    ));


    Redux::setSection($opt_name, array(
        'title'      => __('Privacy Policy Update', 'wordpress-gdpr'),
        'id'         => 'privacy-policy-update',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'privacyPolicyUpdateEnable',
                'type'     => 'switch',
                'title'    => __('Enable Privacy Policy Update', 'wordpress-gdpr'),
                'subtitle' => __('Enable Privacy Policy Update form.', 'wordpress-gdpr'),
                'default'  => '1',
            ),
            array(
                'id'       => 'privacyPolicyUpdateEmail',
                'type'     => 'text',
                'title'    => __('Privacy Policy Update from Email', 'wordpress-gdpr'),
                'subtitle' => __('The FROM Email used for Privacy Policy Updates.', 'wordpress-gdpr'),
                'default'  => $default_email,
                'required' => array('privacyPolicyUpdateEnable','equals','1'),
            ),
            array(
                'id'       => 'privacyPolicyUpdateSubject',
                'type'     => 'text',
                'title'    => __('Privacy Policy Update Email Subject', 'wordpress-gdpr'),
                'subtitle' => __('Subject of the Email.', 'wordpress-gdpr'),
                'default'  => $site_name . ' - ' . __('GDPR - Privacy Policy Updated', 'wordpress-gdpr'),
                'required' => array('privacyPolicyUpdateEnable','equals','1'),
            ),
            array(
                'id'       => 'privacyPolicyUpdateText',
                'type'     => 'editor',
                'title'    => __('Privacy Policy Update Email Text', 'wordpress-gdpr'),
                'subtitle' => __('Text of the Email regarding a Privacy Policy Update.', 'wordpress-gdpr'),
                'default'  => __('Dear %s,
                                <br>
                                We have updated our privacy policy. Please read more here: INSERT YOUR LINK.', 'wordpress-gdpr'),
                'required' => array('privacyPolicyUpdateEnable','equals','1'),
            ),
            array(
                'id'   => 'privacyPolicyUpdateTextSend',
                'type' => 'info',
                'desc' => '<div style="text-align:center;">
                    <a href="' . get_admin_url() . 'admin.php?page=wordpress_gdpr_options_options&wordpress_gdpr[send-privacy-policy-update]=true" class="button button-success">' . __('Send Privacy Update Email', 'wordpress-gdpr') . '</a>
                    </div>',
                'required' => array('dataBreachEnable','equals','1'),
            ),
        )
    ));

    Redux::setSection($opt_name, array(
        'title'      => __('Pseudonymization', 'wordpress-gdpr'),
        'id'         => 'pseudonymization',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'pseudonymizationEnable',
                'type'     => 'info',
                'title'    => __('Pseudonymization', 'wordpress-gdpr'),
                'desc' => __('Pseudonymization is not mandatory by GDPR. It might be a good improvement, but also can risk in data loss. <a href="https://plugins.db-dzine.com/wordpress-gdpr/documentation/faq/pseudonymization/" target="_blank">Please read more here</a>.', 'wordpress-gdpr'),
            ),
        )
    ));


    Redux::setSection($opt_name, array(
        'title'      => __('Request Data Archive', 'wordpress-gdpr'),
        'id'         => 'requestData',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'requestDataEnable',
                'type'     => 'switch',
                'title'    => __('Enable Request Data', 'wordpress-gdpr'),
                'subtitle' => __('Enable Request Data form.', 'wordpress-gdpr'),
                'default'  => '1',
            ),
            array(
                'id'       => 'requestDataPage',
                'type'     => 'select',
                'title'    => __('Request Data Archive Page', 'wordpress-gdpr'),
                'subtitle' => __('Shortcode: [wordpress_gdpr_request_data]', 'wordpress-gdpr'),
                // 'options'  => $wordpress_gdpr_pages,
                'data'     => 'pages',
                'ajax'     => true,
                'required' => array('requestDataEnable','equals','1'),
            ),
            array(
                'id'       => 'requestDataLoggedInButton',
                'type'     => 'checkbox',
                'title'    => __('Show Export Data Button for Logged in', 'wordpress-gdpr'),
                'subtitle' => __('This will show logged in users a button where they can export data theirselves.', 'wordpress-gdpr'),
                'default'  => '0',
                'required' => array('requestDataEnable','equals','1'),
            ),
            array(
                'id'       => 'requestDataAsTable',
                'type'     => 'checkbox',
                'title'    => __('Export Data as HTML Table', 'wordpress-gdpr'),
                'subtitle' => __('Instead of JSON data will be exported as html table.', 'wordpress-gdpr'),
                'default'  => '1',
            ),
            array(
                'id'       => 'requestDataDisableUserExistsCheck',
                'type'     => 'checkbox',
                'title'    => __('Disable User Exists Check', 'wordpress-gdpr'),
                'subtitle' => __('This allows all users to create requests for manually processing.', 'wordpress-gdpr'),
                'default'  => '0',
                'required' => array(
                    array('requestDataEnable','equals','1'),
                    array('useWPCoreFunctions','equals','0')
                ),
            ),
            array(
                'id'       => 'requestDataSubject',
                'type'     => 'text',
                'title'    => __('Request Data Export Email Subject', 'wordpress-gdpr'),
                'subtitle' => __('Subject of the Email notification.', 'wordpress-gdpr'),
                'default'  => $site_name . ' - ' . __('GDPR - Your User Data is Ready', 'wordpress-gdpr'),
                'required' => array('requestDataEnable','equals','1'),
            ),
            array(
                'id'       => 'requestDataText',
                'type'     => 'editor',
                'title'    => __('Request Data Export Email Text', 'wordpress-gdpr'),
                'subtitle' => __('Text of the Email data export.', 'wordpress-gdpr'),
                'default'  => __('Dear %s, <br><br>attached you find an export of your user data.', 'wordpress-gdpr'),
                'required' => array('requestDataEnable','equals','1'),
            ),
        )
    ));


    Redux::setSection($opt_name, array(
        'title'      => __('Services', 'wordpress-gdpr'),
        'id'         => 'services',
        'desc'      => __('!! DEPRECATED !! The Services you find below are deprecated in our plugin settings and they should be moved into our new Services section (see menu GDPR > Services). However you can enable an old Service here and click on migrate Services to migrate them automatically.', 'wordpress-gdpr'),
        'subsection' => true,
        'fields'     => array(
            array(
                'id'   => 'migrateServices',
                'type' => 'info',
                'desc' => '<div style="text-align:center;">' . __('This will migrate all Service like Google Analytics etc to our new services section.', 'wordpress-gdpr') . '<br>
                    <a href="' . get_admin_url() . 'admin.php?page=wordpress_gdpr_options_options&wordpress_gdpr[migrate-services]=true" class="button button-success">' . __('Migrate Services', 'wordpress-gdpr') . '</a>
                    </div>',
            ),
            array(
                'id'       => 'customAllowedCookies',
                'type'     => 'text',
                'title'    => __('Custom Allowed Cookies', 'wordpress-gdpr'),
                'subtitle' => __('Put your technically required cookies here - separated by COMMA (,).', 'wordpress-gdpr'),
                'default'  => 'wordpress_test_cookie, wordpress_logged_in_, wordpress_sec',
            ),
            array(
                'id'       => 'integrationsCloudflare',
                'type'     => 'checkbox',
                'title'    => __('Enable Cloudlfare', 'wordpress-gdpr'),
                'subtitle' => __('If you use Cloudlfare enable this. <a href="https://support.cloudflare.com/hc/en-us/articles/200170156-What-does-the-Cloudflare-cfduid-cookie-do-">See here about __cfduid cookie</a>.', 'wordpress-gdpr'),
                'default'  => '0',
            ),
            array(
                'id'       => 'integrationsGoogleAnalytics',
                'type'     => 'checkbox',
                'title'    => __('Enable Google Analytics', 'wordpress-gdpr'),
                'subtitle' => __('Enable Analytics Loading only if allowed.', 'wordpress-gdpr'),
                'default'  => '0',
            ),
            array(
                'id'       => 'integrationsGoogleAnalyticsCode',
                'type'     => 'ace_editor',
                'mode'     => 'javascript',
                'title'    => __('Analytics Code', 'wordpress-gdpr'),
                'default'  => "
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src='https://www.googletagmanager.com/gtag/js?id=UA-XXXXXX-XX'></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'UA-XXXXXX-XX', { 'anonymize_ip': true });
</script>",
                'required' => array('integrationsGoogleAnalytics','equals','1'),
            ),
            array(
                'id'       => 'integrationsGoogleAdwords',
                'type'     => 'checkbox',
                'title'    => __('Enable Google Adwords', 'wordpress-gdpr'),
                'subtitle' => __('Enable Adwords Loading only if allowed.', 'wordpress-gdpr'),
                'default'  => '0',
            ),
            array(
                'id'       => 'integrationsGoogleAdwordsCode',
                'type'     => 'ace_editor',
                'mode'     => 'javascript',
                'title'    => __('Adwords Code', 'wordpress-gdpr'),
                'default'  => '',
                'required' => array('integrationsGoogleAdwords','equals','1'),
            ),
            array(
                'id'       => 'integrationsGoogleTagManager',
                'type'     => 'checkbox',
                'title'    => __('Enable Google Tag Manager', 'wordpress-gdpr'),
                'subtitle' => __('Enable Tag Manager Loading only if allowed.', 'wordpress-gdpr'),
                'default'  => '0',
            ),
            array(
                'id'       => 'integrationsGoogleTagManagerCode',
                'type'     => 'ace_editor',
                'mode'     => 'javascript',
                'title'    => __('Tag Manager Head Code', 'wordpress-gdpr'),
                'default'  => '',
                'required' => array('integrationsGoogleTagManager','equals','1'),
            ),
            array(
                'id'       => 'integrationsGoogleTagManagerCodeBody',
                'type'     => 'ace_editor',
                'mode'     => 'javascript',
                'title'    => __('Tag Manager Body Code', 'wordpress-gdpr'),
                'default'  => '',
                'required' => array('integrationsGoogleTagManager','equals','1'),
            ),
            array(
                'id'       => 'integrationsHotJar',
                'type'     => 'checkbox',
                'title'    => __('Enable Hot Jar', 'wordpress-gdpr'),
                'subtitle' => __('Enable Hot Jar Loading only if allowed.', 'wordpress-gdpr'),
                'default'  => '0',
            ),
            array(
                'id'       => 'integrationsHotJarCode',
                'type'     => 'ace_editor',
                'mode'     => 'javascript',
                'title'    => __('Hot Jar Code', 'wordpress-gdpr'),
                'default'  => '',
                'required' => array('integrationsHotJar','equals','1'),
            ),
            array(
                'id'       => 'integrationsFacebook',
                'type'     => 'checkbox',
                'title'    => __('Enable Facebook', 'wordpress-gdpr'),
                'subtitle' => __('Enable Facebook pixel loading only if allowed.', 'wordpress-gdpr'),
                'default'  => '0',
            ),
            array(
                'id'       => 'integrationsFacebookCode',
                'type'     => 'ace_editor',
                'mode'     => 'javascript',
                'title'    => __('Facebook Code', 'wordpress-gdpr'),
                'default'  => '',
                'required' => array('integrationsFacebook','equals','1'),
            ),
            array(
                'id'       => 'integrationsPiwik',
                'type'     => 'checkbox',
                'title'    => __('Enable Piwik', 'wordpress-gdpr'),
                'subtitle' => __('Enable Piwik Loading only if allowed.', 'wordpress-gdpr'),
                'default'  => '0',
            ),
            array(
                'id'       => 'integrationsPiwikCode',
                'type'     => 'ace_editor',
                'mode'     => 'javascript',
                'title'    => __('Piwik Code', 'wordpress-gdpr'),
                'default'  => '',
                'required' => array('integrationsPiwik','equals','1'),
            ),
            array(
                'id'       => 'integrationsAdsense',
                'type'     => 'checkbox',
                'title'    => __('Enable Adsense', 'wordpress-gdpr'),
                'subtitle' => __('Enable Adsense Loading only if allowed.', 'wordpress-gdpr'),
                'default'  => '0',
            ),
            array(
                'id'       => 'integrationsAdsenseCode',
                'type'     => 'ace_editor',
                'mode'     => 'javascript',
                'title'    => __('Adsense Code', 'wordpress-gdpr'),
                'default'  => '',
                'required' => array('integrationsAdsense','equals','1'),
            ),
            array(
                'id'       => 'integrationsCustom',
                'type'     => 'checkbox',
                'title'    => __('Enable Custom', 'wordpress-gdpr'),
                'subtitle' => __('Use this for custom JS Code to be executed if allowed. Use Loco Translate to adjust frontend text.', 'wordpress-gdpr'),
                'default'  => '0',
            ),
            array(
                'id'       => 'integrationsCustomCode',
                'type'     => 'ace_editor',
                'mode'     => 'javascript',
                'title'    => __('Custom Code', 'wordpress-gdpr'),
                'default'  => '',
                'required' => array('integrationsCustom','equals','1'),
            ),
            array(
                'id'       => 'integrationsCustoms',
                'type'        => 'slides',
                'title'       => __('Custom Integrations', 'wordpress-gdpr'),
                'subtitle'    => __('Add custom Integrations here.', 'wordpress-gdpr'),
                'placeholder' => array(
                    'title'           => __('Name', 'wordpress-gdpr'),
                    'description'     => __('Code (script tag)', 'wordpress-gdpr'),
                    'url'             => __('Reason', 'wordpress-gdpr'),
                ),
            ),
        )
    ));

    Redux::setSection($opt_name, array(
        'title'      => __('Terms & Conditions', 'wordpress-gdpr'),
        'id'         => 'termsConditions',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'termsConditionsEnable',
                'type'     => 'switch',
                'title'    => __('Enable Terms & Conditions', 'wordpress-gdpr'),
                'subtitle' => __('Enable Terms & Conditions form.', 'wordpress-gdpr'),
                'default'  => '1',
            ),
            array(
                'id'       => 'termsConditionsPage',
                'type'     => 'select',
                'title'    => __('Terms & Conditions Page', 'wordpress-gdpr'),
                'subtitle' => __('Place your Terms & Conditions Page here:', 'wordpress-gdpr'),
                // 'options'  => $wordpress_gdpr_pages,
                'data'     => 'pages',
                'ajax'     => true,
                'required' => array('termsConditionsEnable','equals','1'),
            ),
            array(
                'id'       => 'termsConditionsAccept',
                'type'     => 'checkbox',
                'title'    => __('Show Accept Checkbox', 'wordpress-gdpr'),
                'subtitle' => __('Place the following Shortcode at the bottom of your Terms & Conditions: [wordpress_gdpr_terms_conditions_accept]', 'wordpress-gdpr'),
                'default'  => '0',
            ),
            array(
                'id'       => 'termsConditionsAcceptText',
                'type'     => 'text',
                'title'    => __('Terms & Conditions Accept text', 'wordpress-gdpr'),
                'subtitle' => __('Text for users to agree on Terms & Conditions.', 'wordpress-gdpr'),
                'default'  => __('I’ve read and accept the Terms & Conditions.', 'wordpress-gdpr'),
                'required' => array('termsConditionsAccept','equals','1'),
            ),
        )
    ));

    Redux::setSection($opt_name, array(
        'title'      => __('Unsubscribe', 'wordpress-gdpr'),
        'id'         => 'unsubscribe',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'unsubscribeEnable',
                'type'     => 'switch',
                'title'    => __('Enable Unsubscribe', 'wordpress-gdpr'),
                'subtitle' => __('Enable Unsubscribe', 'wordpress-gdpr'),
                'default'  => '1',
            ),
            array(
                'id'       => 'unsubscribePage',
                'type'     => 'select',
                'title'    => __('Unsubscribe Page', 'wordpress-gdpr'),
                'subtitle' => __('Add your unsubscribe Form here! For <a target="_blank" href="https://kb.mailchimp.com/lists/signup-forms/find-the-unsubscribe-link-for-your-list">Mailchimp see here how to setup a Mailchimp Unsuscribe form</a>. This can be embedded via iFrame.', 'wordpress-gdpr'),
                // 'options'  => $wordpress_gdpr_pages,
                'data'     => 'pages',
                'ajax'     => true,
                'required' => array('unsubscribeEnable','equals','1'),
            ),         
        )
    ));

    Redux::setSection($opt_name, array(
        'title'      => __('Expert', 'wordpress-gdpr'),
        'desc'       => __('Expert Settings.', 'wordpress-gdpr'),
        'id'         => 'advanced',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'geoIP',
                'type'     => 'switch',
                'title'    => __('Use GEO IP to show Popup only for EU', 'wordpress-gdpr'),
                'subtitle' => __('Use with caution - users surfing with a proxy might be wrong targeted.', 'wordpress-gdpr'),
                'default'  => '0',
            ),
            array(
                'id'       => 'useWPCoreFunctions',
                'type'     => 'switch',
                'title'    => __('Use WP Core Functions (req. WP 4.9.6 or Higher!)', 'wordpress-gdpr'),
                'subtitle'    => __('Only check this option, if you are on WP 4.9.6 or later. We will then use the WP Core functions for Data Export & Erasing. Our built-in GDPR Requests Menu will be removed.', 'wordpress-gdpr'),
                'default'  => '0',
            ),
            array(
                'id'       => 'useCookieWhitelist',
                'type'     => 'switch',
                'title'    => __('Use Cookie Whitelist', 'wordpress-gdpr'),
                'subtitle'    => __('If enabled our plugin will remove all cookies, that are not allowed by a certain service. If disabled all cookies will only be removed on decline.', 'wordpress-gdpr'),
                'default'  => '0',
            ),
            array(
                'id'       => 'customCSS',
                'type'     => 'ace_editor',
                'mode'     => 'css',
                'title'    => __('Custom CSS', 'wordpress-gdpr'),
                'subtitle' => __('Add some stylesheet if you want.', 'wordpress-gdpr'),
            ),  
        )
    ));