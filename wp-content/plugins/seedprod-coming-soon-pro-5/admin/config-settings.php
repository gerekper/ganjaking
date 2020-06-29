<?php
/**
 * Config
 *
 * @package WordPress
 * @subpackage seed_cspv5
 * @since 0.1.0
 */

/**
 * Config Settings
 */
function seed_cspv5_get_options(){

    /**
     * Create new menus
     */

    $seed_cspv5_options[ ] = array(
        "type" => "menu",
        "menu_type" => "add_options_page",
        "page_name" => __( "Coming Soon", 'seedprod-coming-soon-pro' ),
        "menu_slug" => "seed_cspv5",
        "layout" => "2-col"
    );

    /**
     * Settings Tab
     */
    $seed_cspv5_options[ ] = array(
        "type" => "tab",
        "id" => "seed_cspv5_setting",
        "label" => __( "Coming Soon & Maintenance Page", 'seedprod-coming-soon-pro' ),
        "icon" => 'fa fa-clock-o',
    );

    $seed_cspv5_options[ ] = array(
        "type" => "setting",
        "id" => "seed_cspv5_settings_content",
    );

    $seed_cspv5_options[ ] = array(
        "type" => "section",
        "id" => "seed_cspv5_section_general",
        "label" => __( "General Settings", 'seedprod-coming-soon-pro' ),
        "icon" => 'fa fa-cogs',
    );

    $seed_cspv5_options[ ] = array(
        "type" => "custom_status",
        "id" => "status",
        "label" => __( "Status", 'seedprod-coming-soon-pro' ),
        "option_values" => array(
            '0' => __( 'Disabled', 'seedprod-coming-soon-pro' ),
            '1' => __( 'Enable Coming Soon Mode', 'seedprod-coming-soon-pro' ),
            '2' => __( 'Enable Maintenance Mode', 'seedprod-coming-soon-pro' ),
            '3' => __( 'Enable Redirect Mode', 'seedprod-coming-soon-pro' )
        ),
        "desc" => __( "<span class='highlight'>When you are logged in you'll see your normal website. Logged out visitors will see the Coming Soon or Maintenance page.</span><br><strong>Coming Soon Mode</strong> will be available to search engines if your site is not private in WordPress.<br><strong>Maintenance Mode</strong> will notify search engines that the site is unavailable. <br><strong>Redirect Mode</strong> will redirect traffic to the designated url.", 'seedprod-coming-soon-pro' ),
        "default_value" => "0"
    );
    
    $seed_cspv5_options[ ] = array(
        "type" => "custom_editpage",
        "id" => "edit_page",
        "label" => __( "", 'seedprod-coming-soon-pro' ),

    );




   
    // Scripts
    $seed_cspv5_options[ ] = array(
        "type" => "section",
        "id" => "seed_cspv5_section_scripts",
        "label" => __( "Advanced Settings", 'seedprod-coming-soon-pro' ),
        "icon" => 'fa fa-code',
    );



    $seed_cspv5_options[ ] = array(
        "type" => "checkbox",
        "id" => "disable_default_excluded_urls",
        "label" => __( "Disable Default Excluded URLs", 'seedprod-coming-soon-pro' ),
        "desc" => __("By default we exclude urls with the terms: login, admin, dashboard and account to prevent lockouts. Check to disable.", 'seedprod-coming-soon-pro'),
        "option_values" => array(
             '1' => __( 'Disable', 'seedprod-coming-soon-pro' ),
        ),
        "default" => "1",
    );

    $seed_cspv5_options[ ] = array(
        "type" => "radio",
        "id" => "include_exclude_options",
        "label" => __( "Include/Exclude URLs", 'seedprod-coming-soon-pro' ),
        "desc" => __("By default the Coming Soon/Maintenance page is shown on every page. Use the <strong>'Show on the Home Page Only'</strong> option to only show on the home page. Alternatively Include or Exclude URLs.", 'seedprod-coming-soon-pro'),
        "option_values" => array(
             '0' => __( 'Show on Coming Soon/Maintenance Page on the Entire Site', 'seedprod-coming-soon-pro' ),
             '1' => __( 'Show on the Home Page Only', 'seedprod-coming-soon-pro' ),
             '2' => __( 'Include URLs', 'seedprod-coming-soon-pro' ),
             '3' => __( 'Exclude URLs', 'seedprod-coming-soon-pro' ),
        ),
    );


    $seed_cspv5_options[ ] = array(
        'id'        => 'include_url_pattern',
        'type'      => 'textarea',
        'label'     => __( "Include URLs", 'seedprod' ),
        'desc'  => __( 'Include certain urls to display the Coming Soon or Maintenance Page. One per line. You may also enter a page or post id. <br>Example: https://www.example.com/about/ <br>To exclude wildcard urls use this pattern: https://www.example.com/about/* This will include any url that starts with https://www.example.com/about/ ', 'seedprod' ),
        'class' => 'large-text'
    );

    $seed_cspv5_options[ ] = array(
        'id'        => 'exclude_url_pattern',
        'type'      => 'textarea',
        'label'     => __( "Exclude URLs", 'seedprod' ),
        'desc'  => __( 'Exclude certain urls from displaying the Coming Soon or Maintenance Page. One per line. You may also enter a page or post id.<br>Example: https://www.example.com/about/ <br>To exclude wildcard urls use this pattern: https://www.example.com/about/* This will exclude any url that starts with https://www.example.com/about/', 'seedprod' ),
        'class' => 'large-text'
    );

    // Scripts
    $seed_cspv5_options[ ] = array(
        "type" => "section",
        "id" => "seed_cspv5_section_access",
        "label" => __( "Access Controls", 'seedprod-coming-soon-pro' ),
        "icon" => 'fa fa-lock',
    );
    $seed_cspv5_options[ ] = array(
        'id'        => 'client_view_url',
        'type'      => 'custom_clientview',
        'label'     => __( "Bypass URL", 'seedprod' ),
    );
    
    $seed_cspv5_options[ ] = array(
        'id'        => 'bypass_expires',
        'type'      => 'select',
        'label'     => __( "Bypass URL Expires", 'seedprod' ),
        'desc'  => __( 'Default is 2 days. ', 'seedprod' ),
        'option_values' => array(
                            '' => "Default",
                            '1' => "1 Hour",
                            '2' => "2 Hours",
                            '3' => "3 Hours",
                            '4' => "4 Hours",
                            '5' => "5 Hours",
                            '6' => "6 Hours",
                            '7' => "7 Hours",
                            '8' => "8 Hours",
                            '9' => "9 Hours",
                            '10' => "10 Hours",
                            '11' => "11 Hours",
                            '12' => "12 Hours",
                            '13' => "13 Hours",
                            '14' => "14 Hours",
                            '15' => "15 Hours",
                            '16' => "16 Hours",
                            '17' => "17 Hours",
                            '18' => "18 Hours",
                            '19' => "19 Hours",
                            '20' => "20 Hours",
                            '21' => "21 Hours",
                            '21' => "22 Hours",
                            '23' => "23 Hours",
                            '24' => "1 Day",
                            '48' => "2 Days",
                            '72' => "3 Days",
                            '96' => "4 Days",
                            '120' => "5 Days",
                            '144' => "6 Days",
                            '168' => "7 Days",
                            '192' => "8 Days",
                            '216' => "9 Days",
                            '240' => "10 Days",
                            '264' => "11 Days",
                            '288' => "12 Days",
                            '312' => "13 Days",
                            '336' => "14 Days",
                            '360' => "15 Days",
                            '384' => "16 Days",
                            '408' => "17 Days",
                            '432' => "18 Days",
                            '456' => "19 Days",
                            '480' => "20 Days",
                            '504' => "21 Days",
                            '528' => "22 Days",
                            '552' => "23 Days",
                            '576' => "24 Days",
                            '600' => "25 Days",
                            '624' => "26 Days",
                            '648' => "27 Days",
                            '672' => "28 Days",
                            '696' => "29 Days",
                            '720' => "30 Days",
                            '8760' => "1 Year",
                            '87600' => "10 Year",
                            ),
    );


    $seed_cspv5_options[ ] = array(
        "type" => "checkbox",
        "id" => "alt_bypass",
        "label" => __( "Use Cookies for Bypass", 'seedprod-coming-soon-pro' ),
        "desc" => __("Use cookies instead of creating a WordPress user for the bypass. Note: this may not work on sites that are cached. <a href='https://support.seedprod.com/article/39-how-the-bypass-url-works' target='_blank'>Learn More</a>", 'seedprod-coming-soon-pro'),
        "option_values" => array(
             '1' => __( 'Enable', 'seedprod-coming-soon-pro' ),
        ),
        "default" => "1",
    );
    
    $seed_cspv5_options[ ] = array(
        'id'        => 'ip_access',
        'type'      => 'textarea',
        'label'     => __( "Access by IP", 'seedprod' ),
        'desc'  => __( "All visitors from certain IP's to bypass the Coming Soon page. Put each IP on it's own line. Your current IP is: ", 'seedprod' ). seed_cspv5_get_ip(),
    );
    
    
    $seed_cspv5_options[ ] = array(
        'id'        => 'include_roles',
        'type'      => 'multiselect',
        'option_values' => array('anyone' => "Anyone Logged In") + seed_cspv5_get_roles(),
        'label'     => __( "Access by Role", 'seedprod' ),
        'desc'  => __( 'By default anyone logged in will see the regular site and not the coming soon page. To override this select Roles that will be given access to see the regular site.', 'seedprod' ),
    );
    




    
    
    /**
     * Pages Tab
     */
    if(seed_cspv5_cu('lp')){
    $seed_cspv5_options[ ] = array(
        "type" => "tab",
        "id" => "seed_cspv5_tab_pages",
        "label" => __( "Landing Pages", 'seedprod-coming-soon-pro' ),
        "icon" => 'fa fa-file-o'
    );
    }
    
    /**
     * Subscribers Tab
     */
    
    $seed_cspv5_options[ ] = array(
        "type" => "tab",
        "id" => "seed_cspv5_tab_subscribers",
        "label" => __( "Subscribers", 'seedprod-coming-soon-pro' ),
        "icon" => 'fa fa-users',
    );


    return $seed_cspv5_options;

}
