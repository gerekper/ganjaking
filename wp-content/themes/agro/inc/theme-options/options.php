<?php

    /**
     * ReduxFramework Sample Config File
     * For full documentation, please visit: http://docs.reduxframework.com/
     */

    if (! class_exists('Redux')) {
        return;
    }

    // This is your option name where all the Redux data is stored.
    $agro_pre = "agro";

    // If Redux is running as a plugin, this will remove the demo notice and links
    add_action('init', 'agro_remove_demo');
    /**
     * ---> SET ARGUMENTS
     * All the possible arguments for Redux.
     * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
     * */

    $agro_theme = wp_get_theme(); // For use with some settings. Not necessary.

    $agro_options_args = array(
        // TYPICAL -> Change these values as you need/desire
        'agro_pre' => $agro_pre,
        // This is where your data is stored in the database and also becomes your global variable name.
        'display_name' => $agro_theme->get('Name'),
        // Name that appears at the top of your panel
        'display_version' => $agro_theme->get('Version'),
        // Version that appears at the top of your panel
        'menu_type' => 'submenu',
        //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
        'allow_sub_menu' =>false,
        // Show the sections below the admin menu item or not
        'menu_title' => esc_html__('Theme Options', 'agro'),
        'page_title' => esc_html__('Theme Options', 'agro'),
        // You will need to generate a Google API key to use this feature.
        // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
        'google_api_key' =>'',
        // Set it you want google fonts to update weekly. A google_api_key value is required.
        'google_update_weekly' => false,
        // Must be defined to add google fonts to the typography module
        'async_typography' => false,
        // Use a asynchronous font on the front end or font string
        //'disable_google_fonts_link' => true,
        'admin_bar' => false,
        // Show the panel pages on the admin bar
        'admin_bar_icon' =>'dashicons-admin-generic',
        // Choose an icon for the admin bar menu
        'admin_bar_priority' => 50,
        // Choose an priority for the admin bar menu
        'global_variable' => 'agro',
        // Set a different name for your global variable other than the agro_pre
        'dev_mode' => false,
        //'forced_dev_mode_off' => true,
        // Show the time the page took to load, etc
        'update_notice' => false,
        // If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
        'customizer' => true,
        // Enable basic customizer support
        //'open_expanded' => true,        // Allow you to start the panel in an expanded way initially.
        //'disable_save_warn' => true,        // Disable the save warning when a user changes a field

        // OPTIONAL -> Give you extra features
        'page_priority' => 99,
        // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
        'page_parent' => apply_filters( 'ninetheme_parent_slug', 'themes.php' ),
        // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
        'page_permissions' => 'manage_options',
        // Permissions needed to access the options panel.
        'menu_icon' => '',
        // Specify a custom URL to an icon
        'last_tab' => '',
        // Force your panel to always open to a specific tab (by id)
        'page_icon' => 'icon-themes',
        // Icon displayed in the admin panel next to your menu_title
        'page_slug' => '',
        // Page slug used to denote the panel, will be based off page title then menu title then agro_pre if not provided
        'save_defaults' => true,
        // On load save the defaults to DB before user clicks save or not
        'default_show' => false,
        // If true, shows the default value next to each field that is not the default value.
        'default_mark' => '',
        // What to print by the field's title if the value shown is default. Suggested: *
        'show_import_export' => true,
        // Shows the Import/Export panel when not used as a field.

        // CAREFUL -> These options are for advanced use only
        'transient_time' =>60 * MINUTE_IN_SECONDS,
        'output' => true,
        // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
        'output_tag' => true,
        // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
        // 'footer_credit' => '',                   // Disable the footer credit of Redux. Please leave if you can help it.

        // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
        'database' => '',
        // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
        'use_cdn' => true,
        // If you prefer not to use the CDN for Select2, Ace Editor, and others, you may download the Redux Vendor Support plugin yourself and run locally or embed it in your code.

        // HINTS
        'hints' => array(
            'icon' => 'el el-question-sign',
            'icon_position' => 'right',
            'icon_color' => 'lightgray',
            'icon_size' => 'normal',
            'tip_style' => array(
                'color' => 'dark',
                'shadow' => true,
                'rounded' => false,
                'style' => ''
            ),
            'tip_position' => array(
                'my' => 'top left',
                'at' => 'bottom right'
            ),
            'tip_effect' => array(
                'show' => array(
                    'effect' => 'slide',
                    'duration' => '500',
                    'event' => 'mouseover'
                ),
                'hide' => array(
                    'effect' => 'slide',
                    'duration' => '500',
                    'event' => 'click mouseleave'
                )
            )
        )
    );

    // ADMIN BAR LINKS -> Setup custom links in the admin bar menu as external items.
    $agro_options_args['admin_bar_links'][] = array(
        'id' => 'ninetheme-agro-docs',
        'href' => 'http://demo-ninetheme.com/agro/doc.html',
        'title' => esc_html__('agro Documentation', 'agro'),
    );
    $agro_options_args['admin_bar_links'][] = array(
        'id' => 'ninetheme-support',
        'href' => 'https://9theme.ticksy.com/',
        'title' => esc_html__('Support', 'agro'),
    );
    $agro_options_args['admin_bar_links'][] = array(
        'id' => 'ninetheme-portfolio',
        'href' => 'https://themeforest.net/user/ninetheme/portfolio',
        'title' => esc_html__('NineTheme Portfolio', 'agro'),
    );

    // Add content after the form.
    $agro_options_args['footer_text'] = esc_html__('If you need help please open a ticket on our support center.', 'agro');

    Redux::setArgs($agro_pre, $agro_options_args);

    /* END ARGUMENTS */

    /* START SECTIONS */


    /*************************************************
    ## MAIN SETTING SECTION
    *************************************************/
    Redux::setSection($agro_pre, array(
        'title' => esc_html__('Main Setting', 'agro'),
        'id' => 'basic',
        'desc' => esc_html__('These are main settings for general theme!', 'agro'),
        'icon' => 'el el-cog'
    ));
    //BREADCRUMBS SETTINGS SUBSECTION
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Theme Main Design', 'agro'),
    'id' => 'themedesignsection',
    'icon' => 'el el-brush',
    'subsection' => true,
    'fields' => array(
        array(
            'title' => esc_html__('Theme Main Color', 'agro'),
            'subtitle' => esc_html__('Change main color.', 'agro'),
            'id' =>'theme_main_color',
            'type' => 'color',
            'default' => '',
            )
        )

    ));
    //BREADCRUMBS SETTINGS SUBSECTION
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Breadcrumbs', 'agro'),
    'id' => 'themebreadcrumbssubsection',
    'icon' => 'el el-idea-alt',
    'subsection' => true,
    'fields' => array(
        array(
            'title' => esc_html__('Breadcrumbs', 'agro'),
            'subtitle' => esc_html__('If enabled, adds breadcrumbs navigation to bottom of page title.', 'agro'),
            'id' =>'breadcrumbs_onoff',
            'type' => 'switch',
            'default' => false
        ),
        array(
            'title' => esc_html__('Breadcrumbs Title', 'agro'),
            'desc' => esc_html__('This option is for breadcrumbs first title.', 'agro'),
            'id' =>'bred_title',
            'type' => 'text',
        ),
        array(
            'title' => esc_html__('Breadcrumbs Typography', 'agro'),
            'id' => 'breadcrumbs_typo',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( '.nt-breadcrumbs, .nt-breadcrumbs a' ),
            'default' =>array(
                'color' =>'',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            ),
            'required' => array( 'breadcrumbs_onoff', '=', '1' )
        )
    )));
    //PRELOADER SETTINGS SUBSECTION
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Preloader', 'agro'),
    'id' => 'themepreloadersubsection',
    'icon' => 'el el-brush',
    'subsection' => true,
    'fields' => array(
        array(
            'title' => esc_html__('Preloader', 'agro'),
            'subtitle' => esc_html__('If enabled, adds preloader.', 'agro'),
            'id' =>'pre_onoff',
            'type' => 'switch',
            'default' => true
        ),
        array(
            'title' => esc_html__('Preloader Type', 'agro'),
            'subtitle' => esc_html__('Select your site preloader type.', 'agro'),
            'id' => 'pre_type',
            'type' => 'select',
            'customizer'=> true,
            'options' => array(
                'default' => 'Default',
                '01' => 'Type 1',
                '02' => 'Type 2',
                '03' => 'Type 3',
                '04' => 'Type 4',
                '05' => 'Type 5',
                '06' => 'Type 6',
                '07' => 'Type 7',
                '08' => 'Type 8',
                '09' => 'Type 9',
                '10' => 'Type 10',
                '11' => 'Type 11',
                '12' => 'Type 12'
            ),
            'default' => 'default',
            'required' => array( 'pre_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Preloader Background Color', 'agro'),
            'subtitle' => esc_html__('Add preloader background color.', 'agro'),
            'id' =>'pre_bg',
            'type' => 'color',
            'default' => '',
            'required' => array(
                array( 'pre_onoff', '=', '1' ),
                array( 'pre_type', '!=', 'default' ),
                array( 'pre_type', '!=', '' )
            )
        ),
        array(
            'title' => esc_html__('Preloader Spin Color', 'agro'),
            'subtitle' => esc_html__('Add preloader spin color.', 'agro'),
            'id' =>'pre_spin',
            'type' => 'color',
            'default' => '',
            'required' => array(
                array( 'pre_onoff', '=', '1' ),
                array( 'pre_type', '!=', 'default' ),
                array( 'pre_type', '!=', '' )
            )
        )
    )));
    //MAIN THEME TYPOGRAPHY SUBSECTION
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Typograhy General', 'agro'),
    'id' => 'themetypographysection',
    'icon' => 'el el-fontsize',
    'subsection' => true,
    'fields' => array(
        array(
            'title' =>esc_html__('H1 Headings', 'agro'),
            'subtitle' => esc_html__("Choose Size and Style for h1", 'agro'),
            'id' => 'font_h1',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( 'h1' ),
            'default' => array(
                'color' =>'',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            )
        ),
        array(
            'title' =>esc_html__('H2 Headings', 'agro'),
            'subtitle' => esc_html__("Choose Size and Style for h2", 'agro'),
            'id' => 'font_h2',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( 'h2' ),
            'default' => array(
                'color' =>'',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            )
        ),
        array(
            'title' =>esc_html__('H3 Headings', 'agro'),
            'subtitle' => esc_html__("Choose Size and Style for h3", 'agro'),
            'id' => 'font_h3',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( 'h3' ),
            'default' => array(
                'color' =>'',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            )
        ),
        array(
            'title' =>esc_html__('H4 Headings', 'agro'),
            'subtitle' => esc_html__("Choose Size and Style for h4", 'agro'),
            'id' => 'font_h4',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( 'h4' ),
            'default' => array(
                'color' =>'',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            )
        ),
        array(
            'title' =>esc_html__('H5 Headings', 'agro'),
            'subtitle' => esc_html__("Choose Size and Style for h5", 'agro'),
            'id' => 'font_h5',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( 'h5' ),
            'default' => array(
                'color' =>'',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            )
        ),
        array(
            'title' =>esc_html__('H6 Headings', 'agro'),
            'subtitle' => esc_html__("Choose Size and Style for h6", 'agro'),
            'id' => 'font_h6',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( 'h6' ),
            'units' =>'px',
            'default' => array(
                'color' =>'',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            )
        ),
        array(
            'title' =>esc_html__('Paragraph', 'agro'),
            'subtitle' => esc_html__("Choose Size and Style for paragraph", 'agro'),
            'id' => 'font_p',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( 'p' ),
            'default' => array(
                'font-family' =>'',
                'color' =>"",
                'font-style' =>'',
                'font-size' =>'',
                'line-height' =>''
            ),
        ),
        array(
            'title' =>esc_html__('Body a (link)', 'agro'),
            'subtitle' => esc_html__("Choose Size and Style for link ( a )", 'agro'),
            'id' => 'font_a',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( 'body a' ),
            'default' => array(
                'font-family' =>'',
                'color' =>"",
                'font-style' =>'',
                'font-size' =>'',
                'line-height' =>''
            ),
        ),
        array(
            'id' =>'info_body_font',
            'type' => 'info',
            'customizer' => false,
            'desc' => esc_html__('Body Font Options', 'agro'),
        ),
        array(
            'title' =>esc_html__('Body General', 'agro'),
            'subtitle' => esc_html__("Choose Size and Style for Body General text", 'agro'),
            'id' => 'font_body',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( 'body' ),
            'default' => array(
                'font-family' =>'',
                'color' =>"",
                'font-style' =>'',
                'font-size' =>'',
                'line-height' =>''
            )
        )
    )));
    //BACKTOTOP BUTTON SUBSECTION
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Backtotop Button', 'agro'),
    'id' => 'backtotop',
    'icon' => 'el el-brush',
    'subsection' => true,
    'fields' => array(
        array(
            'title' => esc_html__('Back-to-top', 'agro'),
            'subtitle' => esc_html__('Switch On-off', 'agro'),
            'desc' => esc_html__('If enabled, adds preloader.', 'agro'),
            'id' => 'backtotop_onoff',
            'type' => 'switch',
            'default' => true
        ),
        array(
            'title' => esc_html__('Back-to-top Background', 'agro'),
            'id' =>'backtotop_bg',
            'type' => 'color',
            'mode' => 'background',
            'output' => array( '#btn-to-top' ),
            'default' =>  '#fcd641',
            'required' => array( 'backtotop_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Back-to-top Arrow Color', 'agro'),
            'id' =>'backtotop_icon',
            'type' => 'color',
            'mode' => 'border-bottom-color',
            'output' => array( '#btn-to-top:before' ),
            'default' =>  '',
            'required' => array( 'backtotop_onoff', '=', '1' )
        )
    )));
    // THEME PAGINATION SUBSECTION
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Pagination', 'agro'),
    'desc' => esc_html__('These are main settings for general theme!', 'agro'),
    'id' => 'pagination',
    'subsection' => true,
    'icon' => 'el el-link',
    'fields' => array(
        array(
            'title' => esc_html__('Pagination', 'agro'),
            'subtitle' => esc_html__('Switch On-off', 'agro'),
            'desc' => esc_html__('If enabled, adds pagination.', 'agro'),
            'id' => 'pag_onoff',
            'type' => 'switch',
            'default' => true
        ),
        array(
            'title' => esc_html__('Pagination Type', 'agro'),
            'subtitle' => esc_html__('Select type.', 'agro'),
            'id' => 'pag_type',
            'type' => 'select',
            'customizer'=> true,
            'options' => array(
                'default' => 'Default',
                'outline' => 'Outline'
            ),
            'default' => 'outline',
            'required' => array( 'pag_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Pagination size', 'agro'),
            'subtitle' => esc_html__('Select size.', 'agro'),
            'id' => 'pag_size',
            'type' => 'select',
            'customizer'=> true,
            'options' => array(
                'small' => 'small',
                'medium' => 'medium',
                'large' => 'large'
            ),
            'default' => 'large',
            'required' => array( 'pag_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Pagination group', 'agro'),
            'subtitle' => esc_html__('Select group.', 'agro'),
            'id' => 'pag_group',
            'type' => 'select',
            'customizer'=> true,
            'options' => array(
                'yes' => 'yes',
                'no' => 'no'
            ),
            'default' => 'no',
            'required' => array( 'pag_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Pagination corner', 'agro'),
            'subtitle' => esc_html__('Select corner type.', 'agro'),
            'id' => 'pag_corner',
            'type' => 'select',
            'customizer'=> true,
            'options' => array(
                'square' => 'square',
                'rounded' => 'rounded',
                'circle' => 'circle',
            ),
            'default' => 'circle',
            'required' => array( 'pag_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Pagination align', 'agro'),
            'subtitle' => esc_html__('Select align.', 'agro'),
            'id' => 'pag_align',
            'type' => 'select',
            'customizer'=> true,
            'options' => array(
                'left' => 'left',
                'right' => 'right',
                'center' => 'center',
            ),
            'default' => 'left',
            'required' => array( 'pag_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Pagination default/outline color', 'agro'),
            'id' =>'pag_clr',
            'type' => 'color',
            'mode' => 'color',
            'required' => array( 'pag_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Active and Hover pagination color', 'agro'),
            'id' =>'pag_hvrclr',
            'type' => 'color',
            'mode' => 'color',
            'required' => array( 'pag_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Pagination number color', 'agro'),
            'id' =>'pag_nclr',
            'type' => 'color',
            'mode' => 'color',
            'required' => array( 'pag_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Active and Hover pagination number color', 'agro'),
            'id' =>'pag_hvrnclr',
            'type' => 'color',
            'mode' => 'color',
            'required' => array( 'pag_onoff', '=', '1' )
        )
    )));

    /*************************************************
    ## LOGO SECTION
    *************************************************/
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Logo', 'agro'),
    'desc' => esc_html__('These are main settings for general theme!', 'agro'),
    'id' => 'logosection',
    'icon' => 'el el-star-empty',
    ));
    // logo
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Logo', 'agro'),
    'id' => 'logosubsection',
    'subsection' => true,
    'icon' => 'el el-brush',
    'fields' => array(
        array(
            'title' => esc_html__('Logo Switch', 'agro'),
            'subtitle' => esc_html__('You can select logo on or off.', 'agro'),
            'id' => 'logo_onoff',
            'type' => 'switch',
            'default' => true
        ),
        array(
            'title' => esc_html__('Logo Type', 'agro'),
            'subtitle' => esc_html__('Select your logo type.', 'agro'),
            'id' => 'logo_type',
            'type' => 'select',
            'customizer'=> true,
            'options' => array(
                'img' => 'Image Logo',
                'sitename' => 'Site Name',
                'customtext'=> 'Custom Text'
            ),
            'default' => 'sitename',
            'required' => array( 'logo_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Custom Text for Logo', 'agro'),
            'desc' => esc_html__('Text entered here will be used as logo', 'agro'),
            'id' =>'text_logo',
            'type' => 'text',
            'required' => array(
                array( 'logo_onoff', '=', '1' ),
                array( 'logo_type', '=', 'customtext' )
            )
        ),
        array(
            'title' => esc_html__('Sitename or Custom Text Logo Font', 'agro'),
            'desc' => esc_html__("Choose size and style your sitename, if you don't use an image logo.", 'agro'),
            'id' =>'logo_style',
            'type' => 'typography',
            'font-family' => true,
            'google' => true,
            'font-backup' => false,
            'font-style' => true,
            'subsets' => true,
            'font-size' => true,
            'line-height' => true,
            'text-align' => false,
            'customizer' => true,
            'color' => true,
            'preview' => true,
            'output' => array('header #nt-logo a.site-logo'),
            'default' => array(
                'font-family' =>'',
                'color' =>"",
                'font-style' =>'',
                'font-size' =>'',
                'line-height' =>''
            ),
            'required' => array(
                array( 'logo_onoff', '=', '1' ),
                array( 'logo_type', '=', 'customtext' ),
                array( 'logo_type', '=', 'sitename' )
            )
        ),
        array(
            'title' => esc_html__('Text logo color ( for sticky menu)', 'agro'),
            'subtitle' => esc_html__('Set your own color for sticky menu text logo.', 'agro'),
            'id' =>'nav_icon',
            'type' => 'color',
            'mode' => 'color',
            'output' => array( 'header.topbar-fixed.fixed #nt-logo a.site-logo' ),
            'required' => array(
                array( 'logo_onoff', '=', '1' ),
                array( 'logo_type', '=', 'customtext' ),
                array( 'logo_type', '=', 'sitename' )
            )
        ),
        array(
            'title' => esc_html__('Logo Image', 'agro'),
            'subtitle' => esc_html__('Upload your Logo. If left blank theme will use site default logo.', 'agro'),
            'id' => 'img_logo',
            'type' => 'media',
            'url' => true,
            'customizer'=> true,
            'required' => array(
                array( 'logo_onoff', '=', '1' ),
                array( 'logo_type', '=', 'img' ),
                array( 'logo_type', '!=', '' )
            )
        ),
        array(
            'title' => esc_html__('Logo Dimensions', 'agro'),
            'subtitle' => esc_html__('Set your logo width and height.', 'agro'),
            'id' => 'img_logo_dimensions',
            'type' => 'dimensions',
            'output' => array(
                'width' => '.top-bar__logo img',
                'height' => '.top-bar__logo img',
            ),
            'units' => array('em','px','%'),
            'required' => array(
                array( 'logo_onoff', '=', '1' ),
                array( 'logo_type', '=', 'img' ),
                array( 'logo_type', '!=', '' )
            )
        ),
        array(
            'title' => esc_html__('Logo 2 ( for sticky menu)', 'agro'),
            'subtitle' => esc_html__('Upload your Logo. If left blank theme will use site default logo.', 'agro'),
            'id' => 'img_logo2',
            'type' => 'media',
            'url' => true,
            'customizer'=> true,
            'required' => array(
                array( 'logo_onoff', '=', '1' ),
                array( 'logo_type', '=', 'img' ),
                array( 'logo_type', '!=', '' )
            )
        ),
        array(
            'title' => esc_html__('Mobile logo', 'agro'),
            'subtitle' => esc_html__('Upload your Logo. If left blank theme will use site default logo.', 'agro'),
            'id' => 'img_mobile_logo',
            'type' => 'media',
            'url' => true,
            'customizer'=> true,
            'required' => array(
                array( 'logo_onoff', '=', '1' ),
                array( 'logo_type', '=', 'img' ),
                array( 'logo_type', '!=', '' )
            )
        ),
        array(
            'title' => esc_html__('Mobile Sticky logo', 'agro'),
            'subtitle' => esc_html__('Upload your Logo. If left blank theme will use site default logo.', 'agro'),
            'id' => 'img_smobile_logo',
            'type' => 'media',
            'url' => true,
            'customizer'=> true,
            'required' => array(
                array( 'logo_onoff', '=', '1' ),
                array( 'logo_type', '=', 'img' ),
                array( 'logo_type', '!=', '' )
            )
        ),
        array(
            'title' => esc_html__('Logo 2 Dimensions', 'agro'),
            'subtitle' => esc_html__('Set your logo width and height.', 'agro'),
            'id' => 'img_logo2_dimensions',
            'type' => 'dimensions',
            'output' => array(
                'width' => 'header.topbar-fixed.fixed .has-sticky-logo .sticky-logo',
                'height' => 'header.topbar-fixed.fixed .has-sticky-logo .sticky-logo',
            ),
            'units' => array('em','px','%'),
            'default' => array(
                'Width' => '',
                'Height' => ''
            ),
            'required' => array(
                array( 'logo_onoff', '=', '1' ),
                array( 'logo_type', '=', 'img' ),
                array( 'logo_type', '!=', '' )
            )
        ),
        array(
            'title' => esc_html__('Mobile Menu Logo Dimensions', 'agro'),
            'subtitle' => esc_html__('Set your logo width and height for mobile menu.', 'agro'),
            'id' => 'mob_logo_dimensions',
            'type' => 'dimensions',
            'units' => array('em','px','%'),
            'default' => array(
                'Width' => '',
                'Height' => ''
            ),
            'required' => array(
                array( 'logo_onoff', '=', '1' ),
                array( 'logo_type', '=', 'img' ),
                array( 'logo_type', '!=', '' )
            )
        )
    )));
    /*************************************************
    ## HEADER ( NAV - TOPBAR -RIGHT BUTTON )
    *************************************************/
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Header', 'agro'),
    'id' => 'headersection',
    'icon' => 'el el-brush',
    'fields' => array(
        array(
            'title' => esc_html__('Header Display', 'agro'),
            'subtitle' => esc_html__('You can enable or disable the site header.', 'agro'),
            'id' =>'header_onoff',
            'type' => 'switch',
            'default' => 1,
            'on' =>'On',
            'off' => 'Off',
        ),
        array(
            'title' => esc_html__('Header Style', 'agro'),
            'subtitle' => esc_html__('Select the site header style.', 'agro'),
            'id' => 'header_style',
            'type' => 'select',
            'customizer'=> true,
            'options' => array(
                '1' => 'Style 1',
                '2' => 'Style 2',
                '3' => 'Style 3',
            ),
            'default' => '1',
            'required' => array( 'header_onoff', '=', '1' )
        ),

    )));
    /*************************************************
    ## TOPBAR SECTION
    *************************************************/
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Header Topbar', 'agro'),
    'id' => 'topbarsubsection',
    'subsection' => true,
    'icon' => 'el el-brush',
    'fields' => array(
        array(
            'title' => esc_html__('Topbar Display', 'agro'),
            'subtitle' => esc_html__('You can select topbar section on or off.', 'agro'),
            'id' => 'topbar_onoff',
            'type' => 'switch',
            'default' => true,
            'required' => array(
                array( 'header_onoff', '=', '1' ),
                array( 'header_style', '=', '2' )
            ),
        ),
        array(
            'title' => esc_html__('Address', 'agro'),
            'desc' => esc_html__('Enter topbar your address.', 'agro'),
            'id' =>'topbar_address',
            'type' => 'textarea',
            'default' => '523 Sylvan Ave, 5th Floor Mountain View, CA 940 USA',
            'required' => array(
                array( 'header_onoff', '=', '1' ),
                array( 'header_style', '=', '2' ),
                array( 'topbar_onoff', '=', '1' )
            ),
        ),
        array(
            'title' => esc_html__('Phone Number Area', 'agro'),
            'subtitle' => esc_html__('HTML allowed', 'agro'),
            'id' => 'topbar_phone',
            'type' => 'textarea',
            'validate' => 'html',
            'default' => '<a href="#">+1 (234) 56789</a>,  <a href="#">+1 987 654 3210</a>',
            'required' => array(
                array( 'header_onoff', '=', '1' ),
                array( 'header_style', '=', '2' ),
                array( 'topbar_onoff', '=', '1' )
            ),
        ),
        array(
            'title' => esc_html__('Mail Address Area', 'agro'),
            'subtitle' => esc_html__('HTML allowed', 'agro'),
            'id' => 'topbar_mail',
            'type' => 'textarea',
            'validate' => 'html',
            'default' => '<a href="mailto:support@agrocompany.com">support@agrocompany.com</a>',
            'required' => array(
                array( 'header_onoff', '=', '1' ),
                array( 'header_style', '=', '2' ),
                array( 'topbar_onoff', '=', '1' )
            ),
        ),
        array(
            'title' => esc_html__('Create Your Custom Social Icons List', 'agro'),
            'subtitle' => esc_html__('HTML allowed', 'agro'),
            'id' => 'topbar_socials',
            'type' => 'textarea',
            'validate' => 'html',
            'default' => '<a class="fontello-twitter" href="#"></a>
            <a class="fontello-facebook" href="#"></a>
            <a class="fontello-linkedin-squared" href="#"></a>',
            'required' => array(
                array( 'header_onoff', '=', '1' ),
                array( 'header_style', '=', '2' ),
                array( 'topbar_onoff', '=', '1' )
            ),
        ),
        array(
            'title' => esc_html__('Social Icons Color', 'agro'),
            'subtitle' => esc_html__('Set your own color for for the topbar social media.', 'agro'),
            'id' =>'nav_icon',
            'type' => 'color',
            'mode' => 'color',
            'output' => array( '.top-bar__contacts .social-btns a' ),
            'required' => array(
                array( 'header_onoff', '=', '1' ),
                array( 'header_style', '=', '2' ),
                array( 'topbar_onoff', '=', '1' )
            ),
        ),
        array(
            'title' => esc_html__('Hover Social Icons Color', 'agro'),
            'subtitle' => esc_html__('Set your own hover color for the topbar social media.', 'agro'),
            'id' =>'nav_icon_hvr',
            'type' => 'color',
            'mode' => 'color',
            'output' => array( '.top-bar__contacts .social-btns a:hover' ),
            'required' => array(
                array( 'header_onoff', '=', '1' ),
                array( 'header_style', '=', '2' ),
                array( 'topbar_onoff', '=', '1' )
            ),
        ),
        //information on-off
        array(
            'id' =>'info_navtopbar0',
            'type' => 'info',
            'style' => 'success',
            'title' => esc_html__('Info!', 'agro'),
            'icon' => 'el el-info-circle',
            'customizer'=> false,
            'desc' => sprintf(esc_html__('%s is only compatible with the header style 2', 'agro'), '<b>The header topbar</b>'),
            'required' => array( 'header_style', '!=', '2' ),
        )
    )));
    /*************************************************
    ## Header Search Button
    *************************************************/
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Header Search', 'agro'),
    'id' => 'headersearchpopup',
    'subsection' => true,
    'icon' => 'el el-brush',
    'fields' => array(
        array(
            'title' => esc_html__('Search Display', 'agro'),
            'subtitle' => esc_html__('You can select search section on or off.', 'agro'),
            'id' => 'search_header_popup_display',
            'type' => 'switch',
            'default' => false,
        ),
        array(
            'title' => esc_html__('Search Background', 'agro'),
            'id' =>'s_bg',
            'type' => 'color_rgba',
            'mode' => 'background',
            'default' => array(
                'color' => '',
                'alpha' => ''
            ),
            'output' => array( '.header_search ' ),
        ),
    )));
    //HEADER MENU
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Header Menu', 'agro'),
    'id' => 'headernavsubsection',
    'subsection' => true,
    'icon' => 'el el-align-justify',
    'fields' => array(
        array(
            'title' => esc_html__('Menu Background', 'agro'),
            'id' =>'nav_bg',
            'type' => 'color_rgba',
            'mode' => 'background',
            'default' => array(
                'color' => '',
                'alpha' => ''
            ),
            'output' => array( 'header.top-bar' ),
            'required' => array( 'header_onoff', '=', '1' ),
        ),
        array(
            'title' => esc_html__('Menu Padding', 'agro'),
            'id' => 'nav_pad',
            'type' => 'spacing',
            'mode' => 'padding',
            'all' => false,
            'units' => array( 'em', 'px', '%' ),
            'units_extended' => 'true',
            'output' => array( 'header.top-bar' ),
            'default' => array(
                'margin-top' => '',
                'margin-right' => '',
                'margin-bottom' => '',
                'margin-left' => ''
            )
        ),
        array(
            'title' =>esc_html__('Primary Menu Font', 'agro'),
            'subtitle' => esc_html__("Choose Size and Style for primary menu", 'agro'),
            'id' => 'nav_a_typo',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( '.top-bar .navigation li a:not(.custom-btn), .top-bar__navigation .submenu a, .top-bar__navigation .submenu a' ),
            'default' => array(
            'color' =>'',
            'font-style' => '',
            'font-family' => '',
            'google' => true,
            'font-size' => '',
            'line-height' => ''
            ),
            'required' => array( 'header_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Hover Menu Item Color', 'agro'),
            'subtitle' => esc_html__('Set your own hover color for the navigation menu item.', 'agro'),
            'id' =>'nav_hvr_a',
            'type' => 'color',
            'output' => array( '.navigation li a:not(.custom-btn):hover, .top-bar__navigation .submenu a:hover' ),
            'required' => array( 'header_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Hover Menu Item Bottom Line Color', 'agro'),
            'subtitle' => esc_html__('Set your own hover color for the navigation menu item before after line.', 'agro'),
            'id' =>'nav_hvr_a_bfraftr',
            'type' => 'color',
            'mode' => 'background',
            'output' => array( '.navigation li.active > a:not(.custom-btn):after, .navigation li:hover > a:not(.custom-btn):after' ),
            'required' => array( 'header_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Dropdown Menu Background', 'agro'),
            'id' =>'nav_dropdown_bg',
            'type' => 'color_rgba',
            'mode' => 'background',
            'default' => array(
                'color' => '',
                'alpha' => ''
            ),
            'required' => array( 'header_onoff', '=', '1' ),
        ),
        array(
            'title' =>esc_html__('Dropdown Menu Font', 'agro'),
            'subtitle' => esc_html__("Choose Size and Style for dropdown submenu menu", 'agro'),
            'id' => 'nav_dropdown_typo',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( '#top-bar.top-bar .top-bar__navigation .submenu a' ),
            'default' => array(
            'color' =>'',
            'font-style' => '',
            'font-family' => '',
            'google' => true,
            'font-size' => '',
            'line-height' => ''
            ),
            'required' => array( 'header_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Background Image for Toggle Mobile Menu', 'agro'),
            'id' =>'mobile_nav_bg',
            'type' => 'background',
            'preview' => true,
            'preview_media' => true,
            'default' => '',
            'output' => array( 'header.top-bar.is-expanded .top-bar__bg' ),
            'required' => array( 'header_onoff', '=', '1' ),
        ),
        array(
            'title' => esc_html__('Mobile Menu Background Image Overlay Color', 'agro'),
            'subtitle' => esc_html__("Please create opaque color for your mobile menu background image", 'agro'),
            'id' =>'nav_mob_bg_overlay',
            'type' => 'color_rgba',
            'mode' => 'background',
            'default' => array(
                'color' => '',
                'alpha' => ''
            ),
            'required' => array( 'header_onoff', '=', '1' ),
        ),
        array(
            'title' => esc_html__('Mobile Menu Hamburger Button Background Color', 'agro'),
            'subtitle' => esc_html__("Please create opaque color for your mobile menu hamburger buton background color", 'agro'),
            'id' =>'nav_mob_hbg_overlay',
            'type' => 'color_rgba',
            'mode' => 'background',
            'default' => array(
                'color' => '',
                'alpha' => ''
            ),
            'required' => array( 'header_onoff', '=', '1' ),
        ),
        array(
            'title' => esc_html__('Mobile Menu Hamburger Button Color', 'agro'),
            'subtitle' => esc_html__("Please create opaque color for your mobile menu hamburger buton color", 'agro'),
            'id' =>'nav_mob_hcbg_overlay',
            'type' => 'color_rgba',
            'mode' => 'background',
            'default' => array(
                'color' => '',
                'alpha' => ''
            ),
            'required' => array( 'header_onoff', '=', '1' ),
        ),
    )));
    //HEADER MENU
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Header Sticky Menu', 'agro'),
    'id' => 'headerstickynavsubsection',
    'subsection' => true,
    'icon' => 'el el-align-justify',
    'fields' => array(
        array(
            'title' => esc_html__('Sticky Menu Display', 'agro'),
            'subtitle' => esc_html__('With this option, you can enable the sticky menu feature.', 'agro'),
            'id' => 'sticky_topbar_onoff',
            'type' => 'switch',
            'default' => false,
            'required' => array('header_onoff', '=', '1' ),
        ),
        array(
            'title' => esc_html__('Sticky Menu Background', 'agro'),
            'id' =>'sticky_nav_bg',
            'type' => 'color_rgba',
            'mode' => 'background',
            'default' => array(
                'color' => '',
                'alpha' => ''
            ),
            'output' => array( 'header.top-bar.topbar-fixed.fixed' ),
            'required' => array(
                array( 'header_onoff', '=', '1' ),
                array( 'sticky_topbar_onoff', '=', '1' ),
            ),
        ),
        array(
            'title' => esc_html__('Sticky Menu Padding', 'agro'),
            'id' => 'snav_pad',
            'type' => 'spacing',
            'mode' => 'padding',
            'all' => false,
            'units' => array( 'em', 'px', '%' ),
            'units_extended' => 'true',
            'output' => array( 'header.top-bar.topbar-fixed.fixed' ),
            'default' => array(
                'margin-top' => '',
                'margin-right' => '',
                'margin-bottom' => '',
                'margin-left' => ''
            )
        ),
        array(
            'title' =>esc_html__('Sticky Menu Font', 'agro'),
            'subtitle' => esc_html__("Choose Size and Style for primary menu", 'agro'),
            'id' => 'sticky_nav_a_typo',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( 'header.top-bar.topbar-fixed.fixed .navigation li a:not(.custom-btn)' ),
            'default' => array(
            'color' =>'',
            'font-style' => '',
            'font-family' => '',
            'google' => true,
            'font-size' => '',
            'line-height' => ''
            ),
            'required' => array(
                array( 'header_onoff', '=', '1' ),
                array( 'sticky_topbar_onoff', '=', '1' )
            )
        ),
        array(
            'title' => esc_html__('Sticky Menu Hover Item Color', 'agro'),
            'subtitle' => esc_html__('Set your own hover color for the navigation menu item.', 'agro'),
            'id' =>'sticky_nav_hvr_a',
            'type' => 'color',
            'output' => array( 'header.top-bar.topbar-fixed.fixed .navigation li:hover a:not(.custom-btn)' ),
            'required' => array(
                array( 'header_onoff', '=', '1' ),
                array( 'sticky_topbar_onoff', '=', '1' )
            )
        ),
        array(
            'title' => esc_html__('Sticky Menu Hover Item Bottom Line Color', 'agro'),
            'subtitle' => esc_html__('Set your own hover color for the navigation menu item before after line.', 'agro'),
            'id' =>'nav_hvr_a_bfraftr',
            'type' => 'color',
            'mode' => 'background',
            'output' => array( 'header.top-bar.topbar-fixed.fixed .navigation li.active > a:not(.custom-btn):after, header.top-bar.topbar-fixed.fixed .navigation li:hover > a:not(.custom-btn):after' ),
            'required' => array(
                array( 'header_onoff', '=', '1' ),
                array( 'sticky_topbar_onoff', '=', '1' )
            )
        ),

    )));
    //HEADER RIGHT BUTTON
    Redux::setSection($agro_pre, array(
        'title' => esc_html__('Header Menu Right Button', 'agro'),
        'id' => 'headernavbtnsubsection',
        'subsection' => true,
        'icon' => 'el el-brush',
        'fields' => array(
            array(
                'title' => esc_html__('Header Right Buttons Display', 'agro'),
                'subtitle' => esc_html__('You can enable or disable the site header right section buttons with switch option.', 'agro'),
                'id' => 'nav_btn_onoff',
                'type' => 'switch',
                'default' => 1,
                'on' => 'On',
                'off' => 'Off',
                'required' => array( 'header_onoff', '=', '1' ),
            ),
            array(
                'title' => esc_html__('Button Title', 'agro'),
                'subtitle' => esc_html__('Add button title.', 'agro'),
                'id' => 'nav_btn_title',
                'type' => 'text',
                'default' => 'Get in Touch',
                'validate' => 'html_custom',
                'allowed_html' => array(
                    'i' => array(
                        'class' => array(),
                        'style' => array()
                    ),
                    'span' => array(
                        'class' => array(),
                        'style' => array()
                    )
                ),
                'required' => array(
                    array( 'header_onoff', '=', '1' ),
                    array( 'nav_btn_onoff', '=', '1' )
                ),
            ),
            array(
                'title' => esc_html__('Button URL', 'agro'),
                'subtitle' => esc_html__('Add button URL/Link.', 'agro'),
                'id' => 'nav_btn_url',
                'type' => 'text',
                'default' => '',
                'required' => array(
                    array( 'header_onoff', '=', '1' ),
                    array( 'nav_btn_onoff', '=', '1' )
                ),
            ),
            array(
                'title' => esc_html__('Button Target', 'agro'),
                'subtitle' => esc_html__('Select the header right button target type.', 'agro'),
                'id' => 'nav_btn_target',
                'type' => 'select',
                'customizer'=> true,
                'options' => array(
                    '_blank' => esc_html__('Open in a new window', 'agro'),
                    '_self' => esc_html__('Open in the same frame', 'agro'),
                    '_parent' => esc_html__('Open in the parent frameset', 'agro'),
                    '_top' => esc_html__('Open in the full body of the window', 'agro'),
                ),
                'default' => '_blank',
                'required' => array(
                    array( 'header_onoff', '=', '1' ),
                    array( 'nav_btn_onoff', '=', '1' )
                ),
            ),
            array(
                'title' => esc_html__('Button Background Color', 'agro'),
                'id' => 'nav_btn_bg',
                'type' => 'color_rgba',
                'mode' => 'background',
                'output'   => array( '.top-bar__navigation li.li-btn .custom-btn' ),
                'default' => array(
                    'color' => '',
                    'alpha' => 1
                ),
                'required' => array(
                    array( 'header_onoff', '=', '1' ),
                    array( 'nav_btn_onoff', '=', '1' )
                ),
            ),
            array(
                'title' => esc_html__('Hover Background Color', 'agro'),
                'id' => 'nav_btn_hvrbg',
                'type' => 'color_rgba',
                'mode' => 'background',
                'output'   => array( '.top-bar__navigation li.li-btn .custom-btn:hover' ),
                'default' => array(
                    'color' => '',
                    'alpha' => 1
                ),
                'required' => array(
                    array( 'header_onoff', '=', '1' ),
                    array( 'nav_btn_onoff', '=', '1' )
                ),
            ),
            array(
                'title' => esc_html__('Button Title Color', 'agro'),
                'id' => 'nav_btn_clr',
                'type' => 'color',
                'mode' => 'color',
                'output'   => array( '.top-bar__navigation li.li-btn .custom-btn' ),
                'required' => array(
                    array( 'header_onoff', '=', '1' ),
                    array( 'nav_btn_onoff', '=', '1' )
                ),
            ),
            array(
                'title' => esc_html__('Hover Title Color', 'agro'),
                'id' => 'nav_btn_hvrclr',
                'type' => 'color',
                'mode' => 'color',
                'output'   => array( '.top-bar__navigation li.li-btn .custom-btn:hover' ),
                'required' => array(
                    array( 'header_onoff', '=', '1' ),
                    array( 'nav_btn_onoff', '=', '1' )
                ),
            ),
            //information on-off
            array(
                'id'		=>'info_nav_btn0',
                'type' 		=> 'info',
                'style' 	=> 'success',
                'title' 	=> esc_html__('Success!', 'agro'),
                'icon'  	=> 'el el-info-circle',
                'customizer'=> false,
                'desc' 		=> sprintf(esc_html__('%s is disabled on the site header. Please activate to view options.', 'agro'), '<b>Header right button</b>'),
                'required' => array( 'nav_allbtn_onoff', '=', '0' ),
            ),
    )));
    /*************************************************
    ## SIDEBARS SECTION
    *************************************************/
    Redux::setSection($agro_pre, array(
        'title' => esc_html__('Sidebars', 'agro'),
        'id' => 'sidebarssection',
        'customizer_width' => '400px',
        'icon' => 'el el-website',
    ));
    // SIDEBAR LAYOUT SUBSECTION
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Sidebars Layout', 'agro'),
    'desc' => esc_html__('You can change the below default layout type.', 'agro'),
    'id' => 'sidebarslayoutsection',
    'subsection' => true,
    'icon' => 'el el-cogs',
    'fields' => array(
        array(
            'title' => esc_html__('Blog Page Layout', 'agro'),
            'subtitle' => esc_html__('Choose the blog index page layout.', 'agro'),
            'id' =>'index_layout',
            'type' => 'image_select',
            'options' => array(
                'left-sidebar' => array(
                    'alt' => 'Left Sidebar',
                    'img' => get_template_directory_uri() . '/inc/theme-options/img/2cl.png'
                ),
                'full-width' => array(
                    'alt' => 'Full Width',
                    'img' => get_template_directory_uri() . '/inc/theme-options/img/1col.png'
                ),
                'right-sidebar' => array(
                    'alt' => 'Right Sidebar',
                    'img' => get_template_directory_uri() . '/inc/theme-options/img/2cr.png'
                )
            ),
            'default' => 'right-sidebar'
        ),
        array(
            'title' => esc_html__('Single Page Layout', 'agro'),
            'subtitle' => esc_html__('Choose the single post page layout.', 'agro'),
            'id' =>'single_layout',
            'type' => 'image_select',
            'options' => array(
                'left-sidebar' => array(
                    'alt' => 'Left Sidebar',
                    'img' => get_template_directory_uri() . '/inc/theme-options/img/2cl.png'
                ),
                'full-width' => array(
                    'alt' => 'Full Width',
                    'img' => get_template_directory_uri() . '/inc/theme-options/img/1col.png'
                ),
                'right-sidebar' => array(
                    'alt' => 'Right Sidebar',
                    'img' => get_template_directory_uri() . '/inc/theme-options/img/2cr.png'
                )
            ),
            'default' => 'right-sidebar'
        ),
        array(
            'title' => esc_html__('Search Page Layout', 'agro'),
            'subtitle' => esc_html__('Choose the search page layout.', 'agro'),
            'id' =>'search_layout',
            'type' => 'image_select',
            'options' => array(
                'left-sidebar' => array(
                    'alt' => 'Left Sidebar',
                    'img' => get_template_directory_uri() . '/inc/theme-options/img/2cl.png'
                ),
                'full-width' => array(
                    'alt' => 'Full Width',
                    'img' => get_template_directory_uri() . '/inc/theme-options/img/1col.png'
                ),
                'right-sidebar' => array(
                    'alt' => 'Right Sidebar',
                    'img' => get_template_directory_uri() . '/inc/theme-options/img/2cr.png'
                )
            ),
            'default' => 'full-width'
        ),
        array(
            'title' => esc_html__('Archive Page Layout', 'agro'),
            'subtitle' => esc_html__('Choose the archive page layout.', 'agro'),
            'id' =>'archive_layout',
            'type' => 'image_select',
            'options' => array(
                'left-sidebar' => array(
                    'alt' => 'Left Sidebar',
                    'img' => get_template_directory_uri() . '/inc/theme-options/img/2cl.png'
                ),
                'full-width' => array(
                    'alt' => 'Full Width',
                    'img' => get_template_directory_uri() . '/inc/theme-options/img/1col.png'
                ),
                'right-sidebar' => array(
                    'alt' => 'Right Sidebar',
                    'img' => get_template_directory_uri() . '/inc/theme-options/img/2cr.png'
                )
            ),
            'default' => 'full-width'
        ),
        array(
            'title' => esc_html__('404 Page Layout', 'agro'),
            'subtitle' => esc_html__('Choose the 404 page layout.', 'agro'),
            'id' =>'error_layout',
            'type' => 'image_select',
            'options' => array(
                'left-sidebar' => array(
                    'alt' => 'Left Sidebar',
                    'img' => get_template_directory_uri() . '/inc/theme-options/img/2cl.png'
                ),
                'full-width' => array(
                    'alt' => 'Full Width',
                    'img' => get_template_directory_uri() . '/inc/theme-options/img/1col.png'
                ),
                'right-sidebar' => array(
                    'alt' => 'Right Sidebar',
                    'img' => get_template_directory_uri() . '/inc/theme-options/img/2cr.png'
                )
            ),
            'default' => 'full-width'
        )
    )));
    // SIDEBAR COLORS SUBSECTION
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Sidebar Customize', 'agro'),
    'desc' => esc_html__('These are main settings for general theme!', 'agro'),
    'id' => 'sidebarsgenaralsection',
    'subsection' => true,
    'icon' => 'el el-brush',
    'fields' => array(
        array(
            'title' => esc_html__('Sidebar Background', 'agro'),
            'id' =>'sdbr_bg',
            'type' => 'color',
            'mode' => 'background',
            'output' => array( '.nt-sidebar .nt-sidebar-inner' )
        ),
        array(
            'id' =>'sdbr_brd',
            'type' => 'border',
            'title' => esc_html__('Sidebar Border', 'agro'),
            'output' => array( '.nt-sidebar .nt-sidebar-inner' ),
            'all' => false
        ),
        array(
            'title' => esc_html__('Sidebar Padding', 'agro'),
            'id' => 'sdbr_pad',
            'type' => 'spacing',
            'mode' => 'padding',
            'all' => false,
            'units' => array( 'em', 'px', '%' ),
            'units_extended' => 'true',
            'output' => array( '.nt-sidebar .nt-sidebar-inner' ),
            'default' => array(
                'margin-top' => '',
                'margin-right' => '',
                'margin-bottom' => '',
                'margin-left' => ''
            )
        ),
        array(
            'title' => esc_html__('Sidebar Margin', 'agro'),
            'id' => 'sdbr_mar',
            'type' => 'spacing',
            'mode' => 'margin',
            'all' => false,
            'units' => array( 'em', 'px', '%' ),
            'units_extended' => 'true',
            'output' => array( '.nt-sidebar .nt-sidebar-inner' ),
            'default' => array(
                'margin-top' => '',
                'margin-right' => '',
                'margin-bottom' => '',
                'margin-left' => ''
            )
        )
    )));
    // SIDEBAR WIDGET COLORS SUBSECTION
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Widget Customize', 'agro'),
    'desc' => esc_html__('These are main settings for general theme!', 'agro'),
    'id' => 'sidebarwidgetsection',
    'subsection' => true,
    'icon' => 'el el-brush',
    'fields' => array(
        array(
            'title' => esc_html__('Sidebar Widgets Background Color', 'agro'),
            'id' =>'sdbr_w_bg',
            'type' => 'color',
            'mode' => 'background',
            'output' => array( '.nt-sidebar .nt-sidebar-inner-widget' )
        ),
        array(
            'title' => esc_html__('Widgets Border', 'agro'),
            'id' =>'sdbr_w_brd',
            'type' => 'border',
            'output' => array( '.nt-sidebar .nt-sidebar-inner-widget' ),
            'all' => false
        ),
        array(
            'title' => esc_html__('Widget Title Color', 'agro'),
            'desc' => esc_html__('Set your own colors for the widgets.', 'agro'),
            'id' =>'sdbr_wt',
            'type' => 'color',
            'output' => array( '.nt-sidebar .nt-sidebar-inner-widget h4' )
        ),
        array(
            'title' => esc_html__('Widget Text Color', 'agro'),
            'desc' => esc_html__('Set your own colors for the widgets.', 'agro'),
            'id' =>'sdbr_wp',
            'type' => 'color',
            'output' => array( '.nt-sidebar .nt-sidebar-inner-widget' )
        ),
        array(
            'title' => esc_html__('Widget Link Color', 'agro'),
            'desc' => esc_html__('Set your own colors for the widgets.', 'agro'),
            'id' =>'sdbr_a',
            'type' => 'color',
            'output' => array( '.nt-sidebar .nt-sidebar-inner-widget a' )
        ),
        array(
            'title' => esc_html__('Widget Hover Link Color', 'agro'),
            'desc' => esc_html__('Set your own hover colors for the widgets.', 'agro'),
            'id' =>'sdbr_hvr_a',
            'type' => 'color',
            'output' => array( '.nt-sidebar .nt-sidebar-inner-widget a:hover' )
        ),
        array(
            'title' => esc_html__('Widget Padding', 'agro'),
            'id' => 'sdbr_w_pad',
            'type' => 'spacing',
            'mode' => 'padding',
            'all' => false,
            'units' => array( 'em', 'px', '%' ),
            'units_extended' => 'true',
            'output' => array( '.nt-sidebar .nt-sidebar-inner-widget' ),
            'default' => array(
                'margin-top' => '',
                'margin-right' => '',
                'margin-bottom' => '',
                'margin-left' => ''
            )
        ),
        array(
            'title' => esc_html__('Widget Margin', 'agro'),
            'id' => 'sdbr_w_mar',
            'type' => 'spacing',
            'mode' => 'margin',
            'all' => false,
            'units' => array( 'em', 'px', '%' ),
            'units_extended' => 'true',
            'output' => array( '.nt-sidebar .nt-sidebar-inner-widget' ),
            'default' => array(
                'margin-top' => '',
                'margin-right' => '',
                'margin-bottom' => '',
                'margin-left' => ''
            )
        )
    )));

    /*************************************************
    ## BLOG PAGE SECTION
    *************************************************/
    Redux::setSection($agro_pre, array(
        'title' => esc_html__('Blog Page', 'agro'),
        'id' => 'blogsection',
        'icon' => 'el el-home',
    ));
    // BLOG HERO SUBSECTION
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Blog Hero', 'agro'),
    'desc' => esc_html__('These are blog index page hero text settings!', 'agro'),
    'id' => 'blogherosubsection',
    'subsection'=> true,
    'icon' => 'el el-brush',
    'fields' => array(
        array(
            'title' => esc_html__('Blog Hero Display', 'agro'),
            'subtitle' => esc_html__('You can enable or disable the site blog index page hero section with switch option.', 'agro'),
            'id' =>'blog_hero_onoff',
            'type' => 'switch',
            'default' => 1,
            'on' =>'On',
            'off' => 'Off'
        ),
        array(
            'title' => esc_html__('Blog Hero Alignment', 'agro'),
            'subtitle' => esc_html__('Select blog page hero text alignment.', 'agro'),
            'id' => 'blog_hero_alignment',
            'type' => 'select',
            'customizer'=> true,
            'options' => array(
                '' => esc_html__('Select alignment', 'agro'),
                'text-right' => esc_html__('right', 'agro'),
                'text-center' => esc_html__('center', 'agro'),
                'text-left' => esc_html__('left', 'agro'),
            ),
            'default' => 'text-left',
            'required' => array( 'blog_hero_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Blog Hero Background', 'agro'),
            'id' =>'blog_hero_bg',
            'type' => 'background',
            'preview' => true,
            'preview_media' => true,
            'default' => '',
            'output' => array( '#nt-index .hero-container' ),
            'required' => array( 'blog_hero_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Blog Hero Overlay Color', 'agro'),
            'id' =>'blog_hero_overlay',
            'type' => 'color_rgba',
            'mode' => 'background',
            'default' => array(
                'color' => '',
                'alpha' => 0.5
            ),
            'output' => array( '#nt-index .hero-container.hero-overlay:before' ),
            'required' => array( 'blog_hero_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Blog Hero Padding', 'agro'),
            'subtitle' => esc_html__('You can set the top spacing of the site blog page hero section.', 'agro'),
            'id' =>'blog_hero_pad',
            'type' => 'spacing',
            'output' => array('#nt-index .hero-container'),
            'mode' => 'padding',
            'units' => array('em', 'px'),
            'units_extended' => 'false',
            'default' => array(
                'padding-top' => '',
                'padding-right' => '',
                'padding-bottom' => '',
                'padding-left' => '',
                'units' => 'px',
            ),
            'required' => array( 'blog_hero_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Blog Title', 'agro'),
            'subtitle' => esc_html__('Add your blog index page title here.', 'agro'),
            'id' =>'blog_title',
            'type' => 'text',
            'default' => 'BLOG',
            'required' => array( 'blog_hero_onoff', '=', '1' )
        ),
        array(
            'title' =>esc_html__('Blog Title Typography', 'agro'),
            'id' => 'blog_title_typo',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( '#nt-index .nt-hero-title' ),
            'default' => array(
                'color' =>'',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            ),
            'required' => array( 'blog_hero_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Blog Slogan', 'agro'),
            'subtitle' => esc_html__('Add your blog index page slogan here.', 'agro'),
            'id' =>'blog_slogan',
            'type' => 'textarea',
            'default' => 'Our',
            'required' => array( 'blog_hero_onoff', '=', '1' )
        ),
        array(
            'title' =>esc_html__('Blog Slogan Typography', 'agro'),
            'id' => 'blog_slogan_typo',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( '#nt-index .nt-hero-subtitle' ),
            'default' => array(
                'color' =>'',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            ),
            'required' => array( 'blog_hero_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Blog Description', 'agro'),
            'subtitle' => esc_html__('Add your blog index page description here.', 'agro'),
            'id' =>'blog_desc',
            'type' => 'textarea',
            'default' => '',
            'required' => array( 'blog_hero_onoff', '=', '1' ),
        ),
        array(
            'title' =>esc_html__('Blog Description Typography', 'agro'),
            'id' => 'blog_desc_typo',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( '#nt-index .nt-hero-description' ),
            'default' => array(
                'color' =>'',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            ),
            'required' => array( 'blog_hero_onoff', '=', '1' )
        )
    )));
    // BLOG LAYOUT AND POST COLUMN STYLE
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Blog Content', 'agro'),
    'id' => 'blogcontentsubsection',
    'subsection' => true,
    'icon' => 'el el-brush',
    'fields' => array(
        array(
            'title' => esc_html__('Blog Container Type', 'agro'),
            'subtitle' => esc_html__('Select blog page container type.', 'agro'),
            'id' => 'blog_container_type',
            'type' => 'select',
            'customizer'=> true,
            'options' => array(
                '' => esc_html__('Default ( Container )', 'agro'),
                '-fluid' => esc_html__('Fluid', 'agro'),
                '-off' => esc_html__('Full-width (no-paddings)', 'agro'),
            ),
            'default' => ''
        ),
        array(
            'title' => esc_html__('Blog Index Type', 'agro'),
            'subtitle' => esc_html__('Select blog index content type.', 'agro'),
            'id' => 'blog_index_type',
            'type' => 'select',
            'customizer'=> true,
            'options' => array(
                'grid' => esc_html__('Grid', 'agro'),
                'masonry' => esc_html__('Masonry', 'agro'),
            ),
            'default' => 'grid',
        ),
        array(
            'title' => esc_html__('Column Width', 'agro'),
            'subtitle' => esc_html__('Select post column width for grid or masonry type.', 'agro'),
            'id' => 'post_column',
            'type' => 'select',
            'customizer'=> true,
            'options' => array(
                'col-md-12' => esc_html__('1 Column', 'agro'),
                'col-md-6' => esc_html__('2 Column', 'agro'),
                'col-md-4' => esc_html__('3 Column', 'agro'),
                'col-md-3' => esc_html__('4 Column', 'agro'),
            ),
            'default' => 'col-md-12'
        ),
        array(
            'title' => esc_html__('Responsive Content Padding Options', 'agro'),
            'id' =>'blog_content_pad_divide',
            'type' => 'divide',
        ),
        array(
            'title' => esc_html__('Blog Content Padding', 'agro'),
            'subtitle' => esc_html__('You can set the top spacing of the site blog page content warapper.', 'agro'),
            'id' =>'blog_content_pad',
            'type' => 'spacing',
            'output' => array('#nt-index .nt-theme-inner-container'),
            'mode' => 'padding',
            'units' => array('em', 'px'),
            'units_extended' => 'false',
            'default' => array(
                'padding-top' => '',
                'padding-right' => '',
                'padding-bottom' => '',
                'padding-left' => '',
                'units' => ''
            ),
        ),
        array(
            'title' => esc_html__('Responsive Content Padding Options', 'agro'),
            'id' =>'blog_content_res_pad_section',
            'type' => 'section',
            'indent' => true
        ),
        array(
            'title' => esc_html__('Content Padding 992px ( Responsive )', 'agro'),
            'subtitle' => esc_html__('You can set the top spacing of the site blog page content warapper for max-device-width 992px.', 'agro'),
            'id' =>'blog_content_992_pad',
            'type' => 'spacing',
            'mode' => 'padding',
            'units' => array('em', 'px'),
            'units_extended' => 'false',
            'default' => array(
                'padding-top' => '',
                'padding-right' => '',
                'padding-bottom' => '',
                'padding-left' => '',
                'units' => ''
            ),
        ),
        array(
            'title' => esc_html__('Content Padding 768px ( Responsive )', 'agro'),
            'subtitle' => esc_html__('You can set the top spacing of the site blog page content warapper for max-device-width 768px.', 'agro'),
            'id' =>'blog_content_768_pad',
            'type' => 'spacing',
            'mode' => 'padding',
            'units' => array('em', 'px'),
            'units_extended' => 'false',
            'default' => array(
                'padding-top' => '',
                'padding-right' => '',
                'padding-bottom' => '',
                'padding-left' => '',
                'units' => ''
            ),
        ),
    )));
    // BLOG LAYOUT AND POST COLUMN STYLE
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Blog Post Style', 'agro'),
    'id' => 'blogpoststylesubsection',
    'subsection' => true,
    'icon' => 'el el-brush',
    'fields' => array(
        array(
            'title' => esc_html__('Genaral Options', 'agro'),
            'id' =>'post_general_section',
            'type' => 'section',
            'indent' => true
        ),
        array(
            'title' => esc_html__('Post Box Skin Type', 'agro'),
            'subtitle' => esc_html__('Select blog index post skin type.', 'agro'),
            'id' => 'post_skin_type',
            'type' => 'select',
            'customizer'=> true,
            'options' => array(
                '1' => esc_html__('Type 1 ( Default )', 'agro'),
                '2' => esc_html__('Type 2', 'agro'),
                '3' => esc_html__('Type 3', 'agro'),
            ),
            'default' => '1',
        ),
        array(
            'title' => esc_html__('Post Box Border', 'agro'),
            'id' =>'blog_post_box_brd',
            'type' => 'border',
            'customizer'=> true,
            'all' => false,
            'default'  => array(
                'border-color'  => '',
                'border-style'  => '',
                'border-top' => '',
                'border-right'  => '',
                'border-bottom' => '',
                'border-left'   => ''
            ),
            'output' => array('.nt-blog-item .nt-blog-item-inner'),
        ),
        array(
            'title' => esc_html__('Post Box Background Color', 'agro'),
            'id' =>'blog_post_box_bg',
            'customizer'=> true,
            'type' => 'color',
            'mode' => 'background-color',
            'output' => array( '.nt-blog-item .nt-blog-item-inner' )
        ),
        array(
            'title' => esc_html__('Post Content Border Top', 'agro'),
            'id' =>'blog_post_info_top_brd',
            'type' => 'border',
            'customizer'=> true,
            'all' => false,
            'right' => false,
            'bottom' => false,
            'left' => false,
            'top' => true,
            'style' => true,
            'color' => true,
            'output' => array('.nt-blog-item .nt-blog-info')
        ),
        array(
            'title' => esc_html__('Genaral Hover Link Color', 'agro'),
            'id' =>'blog_post_link_hvrclr',
            'type' => 'color',
            'customizer'=> true,
            'default' => ''
        ),
        array(
            'title' => esc_html__('Post Image and Text Alignment', 'agro'),
            'subtitle' => esc_html__('Select post content alignment.', 'agro'),
            'id' => 'post_alignment',
            'type' => 'select',
            'customizer'=> true,
            'options' => array(
                '' => esc_html__('Select alignment', 'agro'),
                'text-right' => esc_html__('right', 'agro'),
                'text-center' => esc_html__('center', 'agro'),
                'text-left' => esc_html__('left', 'agro'),
            ),
            'default' => 'text-left'
        ),
        array(
            'title' => esc_html__('Category Options', 'agro'),
            'id' =>'post_category_section',
            'type' => 'section',
            'indent' => true
        ),
        array(
            'title' => esc_html__('Category Display', 'agro'),
            'subtitle' => esc_html__('You can enable or disable the site blog index page post category with this option.', 'agro'),
            'id' =>'post_category_onoff',
            'type' => 'switch',
            'default' => 1,
            'customizer'=> true,
            'on' =>'On',
            'off' => 'Off'
        ),
        array(
            'title' => esc_html__('Category Background Color', 'agro'),
            'subtitle' => esc_html__('You can use this option for post skin type 2.', 'agro'),
            'id' =>'blog_post_cat_bg',
            'type' => 'color_rgba',
            'mode' => 'background',
            'customizer'=> true,
            'default' => array(),
            'options' => array(
                'show_input' => true,
                'show_initial' => true,
                'show_alpha' => true,
                'show_palette' => true,
                'show_palette_only' => false,
                'show_selection_palette' => true,
                'max_palette_size' => 10,
                'allow_empty' => true,
                'clickout_fires_change' => false,
                'show_buttons' => true,
                'use_extended_classes' => true,
                'palette' => null
            ),
            'output' => array( '.nt-blog-item .nt-blog-info-category a' ),
            'required' => array(
                array( 'post_skin_type', '=', '2' ),
                array( 'post_category_onoff', '=', '1' ),
            )
        ),
        array(
            'title' => esc_html__('Category Color', 'agro'),
            'id' =>'blog_post_cat_clr',
            'type' => 'color',
            'customizer'=> true,
            'output' => array( '.nt-blog-item .nt-blog-info-category a, .nt-blog-item.posts .__item .__category a' ),
            'required' => array( 'post_category_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Post Title Options', 'agro'),
            'id' =>'post_title_section',
            'type' => 'section',
            'indent' => true
        ),
        array(
            'title' => esc_html__('Title Display', 'agro'),
            'subtitle' => esc_html__('You can enable or disable the site blog index page post title with this option.', 'agro'),
            'id' =>'post_title_onoff',
            'type' => 'switch',
            'customizer'=> true,
            'default' => 1,
            'on' =>'On',
            'off' => 'Off'
        ),
        array(
            'title' =>esc_html__('Title Typography', 'agro'),
            'id' => 'blog_post_title_typo',
            'type' => 'typography',
            'customizer'=> true,
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'default' => array(
                'color' =>'',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            ),
            'output' => array( '.nt-blog-item .nt-blog-info-title a, .posts--style-2 .__item--preview .__title' ),
            'required' => array( 'post_title_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Meta Options ( Author-Commnets-Date )', 'agro'),
            'id' =>'post_meta_section',
            'type' => 'section',
            'indent' => true
        ),
        array(
            'title' => esc_html__('All Meta Display', 'agro'),
            'subtitle' => esc_html__('You can enable or disable the site blog index page post meta with this option.', 'agro'),
            'id' =>'post_meta_onoff',
            'type' => 'switch',
            'customizer'=> true,
            'default' => 1,
            'on' =>'On',
            'off' => 'Off'
        ),
        array(
            'title' => esc_html__('Author Display', 'agro'),
            'subtitle' => esc_html__('You can enable or disable the site blog index page post author with switch option.', 'agro'),
            'id' =>'post_author_onoff',
            'type' => 'switch',
            'default' => 1,
            'customizer'=> true,
            'on' =>'On',
            'off' => 'Off',
            'required' => array( 'post_meta_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Date Display', 'agro'),
            'subtitle' => esc_html__('You can enable or disable the site blog index page post date with this option.', 'agro'),
            'id' =>'post_date_onoff',
            'type' => 'switch',
            'default' => 1,
            'customizer'=> true,
            'on' =>'On',
            'off' => 'Off',
            'required' => array( 'post_meta_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Comments Display', 'agro'),
            'subtitle' => esc_html__('You can enable or disable the site blog index page post comments with this option.', 'agro'),
            'id' =>'post_comments_onoff',
            'type' => 'switch',
            'default' => 1,
            'customizer'=> true,
            'on' =>'On',
            'off' => 'Off',
            'required' => array( 'post_meta_onoff', '=', '1' )
        ),
        array(
            'title' =>esc_html__('Meta Typography', 'agro'),
            'id' => 'blog_post_meta_typo',
            'type' => 'typography',
            'customizer'=> true,
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( '.nt-blog-item .nt-blog-info-meta-link' ),
            'default' => array(
                'color' =>'',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            ),
            'required' => array( 'post_meta_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Meta Icon Color', 'agro'),
            'id' =>'blog_post_meta_icon_clr',
            'type' => 'color',
            'customizer'=> true,
            'output' => array( '.nt-blog-item .nt-blog-info-meta-item i' ),
            'required' => array( 'post_meta_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Meta Icon Background Color', 'agro'),
            'id' =>'blog_post_meta_icon_bg',
            'type' => 'color',
            'mode' => 'background-color',
            'customizer'=> true,
            'output' => array( '.nt-blog-item .nt-blog-info-meta-item i' ),
            'required' => array( 'post_meta_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Date Color  ( Type 2-3 )', 'agro'),
            'id' =>'blog_post_date_clr',
            'type' => 'color',
            'customizer'=> true,
            'output' => array( '.nt-blog-item.posts .__item--preview .__date-post' ),
            'required' => array(
                array( 'post_skin_type', '!=', '1' ),
                array( 'post_meta_onoff', '=', '1' ),
                array( 'post_date_onoff', '=', '1' )
             )
        ),
        array(
            'title' => esc_html__('Date Background Color ( Type 2-3 )', 'agro'),
            'id' =>'blog_post_date_bg',
            'type' => 'color',
            'mode' => 'background-color',
            'customizer'=> true,
            'output' => array( '.nt-blog-item.posts .__item--preview .__date-post' ),
            'required' => array(
                array( 'post_skin_type', '!=', '1' ),
                array( 'post_meta_onoff', '=', '1' ),
                array( 'post_date_onoff', '=', '1' )
             )
        ),
        array(
            'title' => esc_html__('Excerpt Options', 'agro'),
            'id' =>'post_excerpt_section',
            'type' => 'section',
            'indent' => true
        ),
        array(
            'title' => esc_html__('Excerpt Display', 'agro'),
            'subtitle' => esc_html__('You can enable or disable the site blog index page post meta with this option.', 'agro'),
            'id' =>'post_excerpt_onoff',
            'type' => 'switch',
            'default' => 1,
            'customizer'=> true,
            'on' =>'On',
            'off' => 'Off'
        ),
        array(
            'title' => esc_html__('Post Excerpt Size', 'agro'),
            'subtitle' => esc_html__('You can control blog post excerpt size with this option.', 'agro'),
            'id' => 'excerptsz',
            'type' => 'slider',
            'customizer'=> true,
            'default' =>80,
            'min' => 0,
            'step' => 1,
            'max' => 500,
            'display_value' => 'text',
            'required' => array( 'post_excerpt_onoff', '=', '1' )
        ),
        array(
            'title' =>esc_html__('Excerpt Typography', 'agro'),
            'id' => 'blog_post_excerpt_typo',
            'type' => 'typography',
            'customizer'=> true,
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( '.nt-blog-item .nt-blog-info-excerpt, .nt-blog-item .__content p' ),
            'default' => array(
                'color' =>'',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            ),
            'required' => array( 'post_excerpt_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Button Options', 'agro'),
            'id' =>'post_button_section',
            'type' => 'section',
            'indent' => true
        ),
        array(
            'title' => esc_html__('Read More Button Display', 'agro'),
            'subtitle' => esc_html__('You can enable or disable the read more button with this option.', 'agro'),
            'id' =>'post_button_onoff',
            'type' => 'switch',
            'default' => 1,
            'customizer'=> true,
            'on' =>'On',
            'off' => 'Off'
        ),
        array(
            'title' => esc_html__('Blog Post Read More Button Title', 'agro'),
            'subtitle' => esc_html__('You can change post button title.', 'agro'),
            'id' =>'post_button_title',
            'type' => 'text',
            'default' => 'Read more',
            'customizer'=> true,
            'required' => array( 'post_button_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Button Title Color', 'agro'),
            'id' =>'blog_post_btn_clr',
            'type' => 'link_color',
            'active' => false,
            'customizer'=> true,
            'default'  => array(
                'regular' => '',
                'hover' => '',
            ),
            'required' => array( 'post_button_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Button Background Color', 'agro'),
            'id' =>'blog_post_btn_bg',
            'type' => 'link_color',
            'active' => false,
            'customizer'=> true,
            'default'  => array(
                'regular' => '',
                'hover' => ''
            ),
            'required' => array( 'post_button_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Button Border Color', 'agro'),
            'id' =>'blog_post_btn_brd',
            'type' => 'link_color',
            'active' => false,
            'customizer'=> true,
            'default'  => array(
                'regular' => '',
                'hover' => ''
            ),
            'required' => array( 'post_button_onoff', '=', '1' ),
        ),
        array(
            'title' => esc_html__('Button Border Radius', 'agro'),
            'id' =>'blog_post_btn_brdrad',
            'customizer'=> true,
            'type' => 'spinner',
            'default' => '30',
            'min' => '0',
            'step' => '1',
            'max' => '100',
            'required' => array( 'post_button_onoff', '=', '1' )
        ),
    )));
    // BLOG LAYOUT AND POST COLUMN STYLE
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Blog After Content', 'agro'),
    'id' => 'blogaftercontentsubsection',
    'subsection' => true,
    'icon' => 'el el-brush',
    'fields' => array(
        array(
            'title' => esc_html__('After Content Template?', 'agro'),
            'subtitle' => esc_html__('You can use your custom template with this option.', 'agro'),
            'id' =>'blog_after_content_display',
            'type' => 'switch',
            'default' => 0,
            'customizer'=> true,
            'on' =>'On',
            'off' => 'Off'
        ),
        array(
            'title' => esc_html__('Select Template', 'agro'),
            'subtitle' => esc_html__('Select your template if you want to use a template saved from WPBakery Page Builder settings after blog content.', 'agro'),
            'id' => 'blog_after_content_saved_templates',
            'type' => 'select',
            'customizer' => true,
            'options' => class_exists('Agro_Saved_Templates') ? Agro_Saved_Templates::get_vc_templates() : array('no-template' => esc_html__('No exists templates', 'agro')),
            'required' => array( 'blog_after_content_display', '=', '1' )
        )
    )));
    /*************************************************
    ## SINGLE PAGE SECTION
    *************************************************/
    Redux::setSection($agro_pre, array(
        'title' => esc_html__('Single Page', 'agro'),
        'id' => 'singlesection',
        'icon' => 'el el-home-alt'
    ));
    // SINGLE HERO SUBSECTION
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Single Hero', 'agro'),
    'desc' => esc_html__('These are single page hero section settings!', 'agro'),
    'id' => 'singleherosubsection',
    'subsection' => true,
    'icon' => 'el el-brush',
    'fields' => array(
        array(
            'title' => esc_html__('Single Hero Display', 'agro'),
            'subtitle' => esc_html__('You can enable or disable the site single page hero section with switch option.', 'agro'),
            'id' =>'single_hero_onoff',
            'type' => 'switch',
            'default' => 1,
            'on' =>'On',
            'off' => 'Off'
        ),
        array(
            'title' => esc_html__('Single Hero Alignment', 'agro'),
            'subtitle' => esc_html__('Select single page hero text alignment.', 'agro'),
            'id' => 'single_hero_alignment',
            'type' => 'select',
            'customizer'=> true,
            'options' => array(
                '' => esc_html__('Select alignment', 'agro'),
                'text-right' => esc_html__('right', 'agro'),
                'text-center' => esc_html__('center', 'agro'),
                'text-left' => esc_html__('left', 'agro')
            ),
            'default' => 'text-left',
            'required' => array( 'single_hero_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Single Hero Background', 'agro'),
            'id' =>'single_hero_bg',
            'type' => 'background',
            'output' => array( '#nt-single .hero-container' ),
            'required' => array( 'single_hero_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Single Hero Overlay Color', 'agro'),
            'id' =>'single_hero_overlay',
            'type' => 'color_rgba',
            'mode' => 'background',
            'default' => array(
                'alpha' => 0.5
            ),
            'output' => array( '#nt-single .hero-container.hero-overlay:before' ),
            'required' => array( 'single_hero_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Single Hero Padding', 'agro'),
            'subtitle' => esc_html__('You can set the top spacing of the site single page hero section.', 'agro'),
            'id' =>'single_hero_pad',
            'type' => 'spacing',
            'output' => array('#nt-index .hero-container'),
            'mode' => 'padding',
            'units' => array('em', 'px'),
            'units_extended' => 'false',
            'default' => array(
                'padding-top' => '',
                'padding-right' => '',
                'padding-bottom' => '',
                'padding-left' => '',
                'units' => 'px',
            ),
            'required' => array( 'single_hero_onoff', '=', '1' )
        ),
        array(
            'title' =>esc_html__('Single Post Title Typography', 'agro'),
            'id' => 'single_title_typo',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( '#nt-single .nt-hero-title' ),
            'units' =>'px',
            'default' => array(
                'color' =>'',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            ),
            'required' => array( 'single_hero_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Single Slogan', 'agro'),
            'subtitle' => esc_html__('Add your single page slogan here.', 'agro'),
            'id' =>'single_slogan',
            'type' => 'textarea',
            'default' => '',
            'required' => array( 'single_hero_onoff', '=', '1' )
        ),
        array(
            'title' =>esc_html__('Single Slogan Typography', 'agro'),
            'id' => 'single_slogan_typo',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( '#nt-single .nt-hero-subtitle' ),
            'default' => array(
                'color' =>'',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            ),
            'required' => array( 'single_hero_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Single Description', 'agro'),
            'subtitle' => esc_html__('Add your single page description here.', 'agro'),
            'id' =>'single_desc',
            'type' => 'textarea',
            'default' => '',
            'required' => array( 'single_hero_onoff', '=', '1' )
        ),
        array(
            'title' =>esc_html__('Single Description Typography', 'agro'),
            'id' => 'single_desc_typo',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( '#nt-single .nt-hero-description' ),
            'default' => array(
                'color' =>'',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            ),
            'required' => array( 'single_hero_onoff', '=', '1' )
        )
    )));
    // SINGLE CONTENT SUBSECTION
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Single Content', 'agro'),
    'id' => 'singlecontentsubsection',
    'subsection' => true,
    'icon' => 'el el-brush',
    'fields' => array(
        array(
            'title' => esc_html__('Single Container Type', 'agro'),
            'subtitle' => esc_html__('Select single page container type.', 'agro'),
            'id' => 'single_container_type',
            'type' => 'select',
            'customizer'=> true,
            'options' => array(
                '' => esc_html__('Default ( Container )', 'agro'),
                '-fluid' => esc_html__('Fluid', 'agro'),
                '-off' => esc_html__('Full-width (no-paddings)', 'agro'),
            ),
            'default' => '',
        ),
        array(
            'title' => esc_html__('Single Post Tags Display', 'agro'),
            'subtitle' => esc_html__('You can enable or disable the site single page post meta tags with switch option.', 'agro'),
            'id' =>'single_postmeta_tags_onoff',
            'type' => 'switch',
            'default' => 1,
            'on' =>'On',
            'off' => 'Off'
        ),
        array(
            'title' => esc_html__('Single Post Authorbox', 'agro'),
            'subtitle' => esc_html__('You can enable or disable the site single page post authorbox with switch option.', 'agro'),
            'id' =>'single_post_author_box_onoff',
            'type' => 'switch',
            'default' => 0,
            'on' =>'On',
            'off' => 'Off'
        ),
        array(
            'title' => esc_html__('Single Post Pagination Display', 'agro'),
            'subtitle' => esc_html__('You can enable or disable the site single page post next and prev pagination with switch option.', 'agro'),
            'id' =>'single_navigation_onoff',
            'type' => 'switch',
            'default' => 1,
            'on' =>'On',
            'off' => 'Off'
        ),
        array(
            'title' => esc_html__('Single Related Post Display', 'agro'),
            'subtitle' => esc_html__('You can enable or disable the site single page related post with switch option.', 'agro'),
            'id' =>'single_related_onoff',
            'type' => 'switch',
            'default' => 0,
            'on' =>'On',
            'off' => 'Off'
        ),
        array(
            'title' => esc_html__('Single Related Post Count', 'agro'),
            'subtitle' => esc_html__('You can control related post count with this option.', 'agro'),
            'id' => 'related_perpage',
            'type' => 'slider',
            'default' =>3,
            'min' => 1,
            'step' => 1,
            'max' => 24,
            'display_value' => 'text',
            'required' => array( 'single_related_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Related Section Title', 'agro'),
            'subtitle' => esc_html__('Add your single page related post section title here.', 'agro'),
            'id' =>'related_title',
            'type' => 'text',
            'default' => 'You May Also Like',
            'required' => array( 'single_related_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Single Content Padding', 'agro'),
            'subtitle' => esc_html__('You can set the top spacing of the site single page content.', 'agro'),
            'id' =>'single_content_pad',
            'type' => 'spacing',
            'output' => array('#nt-single .nt-theme-inner-container'),
            'mode' => 'padding',
            'units' => array('em', 'px'),
            'units_extended' => 'false',
            'default' => array(
                'padding-top' => '',
                'padding-right' => '',
                'padding-bottom' => '',
                'padding-left' => '',
                'units' => 'px'
            )
        ),
        array(
            'title' => esc_html__('Responsive Content Padding Options', 'agro'),
            'id' =>'single_content_res_pad_section',
            'type' => 'section',
            'indent' => true
        ),
        array(
            'title' => esc_html__('Content Padding 992px ( Responsive )', 'agro'),
            'subtitle' => esc_html__('You can set the top spacing of the site blog page content warapper for max-device-width 992px.', 'agro'),
            'id' =>'single_content_992_pad',
            'type' => 'spacing',
            'mode' => 'padding',
            'units' => array('em', 'px'),
            'units_extended' => 'false',
            'default' => array(
                'padding-top' => '',
                'padding-right' => '',
                'padding-bottom' => '',
                'padding-left' => '',
                'units' => ''
            ),
        ),
        array(
            'title' => esc_html__('Content Padding 768px ( Responsive )', 'agro'),
            'subtitle' => esc_html__('You can set the top spacing of the site blog page content warapper for max-device-width 768px.', 'agro'),
            'id' =>'single_content_768_pad',
            'type' => 'spacing',
            'mode' => 'padding',
            'units' => array('em', 'px'),
            'units_extended' => 'false',
            'default' => array(
                'padding-top' => '',
                'padding-right' => '',
                'padding-bottom' => '',
                'padding-left' => '',
                'units' => ''
            ),
        ),
    )));

    //ARCHIVE PAGE SECTION
    Redux::setSection($agro_pre, array(
    'title'	=> esc_html__('Archive Page', 'agro'),
    'desc'	=> esc_html__('These are archive page settings!', 'agro'),
    'id'	=> 'archivesection',
    'icon'	=> 'el el-folder-open',
    'fields'=> array(
        array(
            'title' => esc_html__('Archive Hero Display', 'agro'),
            'subtitle' => esc_html__('You can enable or disable the site archive page hero section with switch option.', 'agro'),
            'id' =>'archive_hero_onoff',
            'type' => 'switch',
            'default' => 1,
            'on' =>'On',
            'off' => 'Off',
        ),
        array(
            'title' => esc_html__('Archive Hero Alignment', 'agro'),
            'subtitle' => esc_html__('Select archive page hero text alignment.', 'agro'),
            'id' => 'archive_hero_alignment',
            'type' => 'select',
            'customizer'=> true,
            'options' => array(
                '' => esc_html__('Select alignment', 'agro'),
                'text-right' => esc_html__('right', 'agro'),
                'text-center' => esc_html__('center', 'agro'),
                'text-left' => esc_html__('left', 'agro'),
            ),
            'default' => 'text-left',
            'required' => array( 'archive_hero_onoff', '=', '1' ),
        ),
        array(
            'title' => esc_html__('Archive Hero Background', 'agro'),
            'id' =>'archive_hero_bg',
            'type' => 'background',
            'output' => array( '#nt-archive .hero-container' ),
            'required' => array( 'archive_hero_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Archive Hero Overlay Color', 'agro'),
            'id' =>'archive_hero_overlay',
            'type' => 'color_rgba',
            'mode' => 'background',
            'default' => array(
                'alpha' => 0.5
            ),
            'output' => array( '#nt-archive .hero-container.hero-overlay:before' ),
            'required' => array( 'archive_hero_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Archive Hero Padding', 'agro'),
            'subtitle' => esc_html__('You can set the top spacing of the site archive page hero section.', 'agro'),
            'id' =>'archive_hero_pad',
            'type' => 'spacing',
            'output' => array('#nt-archive .hero-container'),
            'mode' => 'padding',
            'units' => array('em', 'px'),
            'units_extended' => 'false',
            'default' => array(
                'padding-top' => '',
                'padding-right' => '',
                'padding-bottom' => '',
                'padding-left' => '',
                'units' => 'px',
            ),
            'required' => array( 'archive_hero_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Custom Archive Title', 'agro'),
            'subtitle' => esc_html__('Add your custom archive page title here.', 'agro'),
            'id' =>'archive_title',
            'type' => 'text',
            'default' => 'ARCHIVE',
            'required' => array( 'archive_hero_onoff', '=', '1' )
        ),
        array(
            'title' =>esc_html__('Archive Title Typography', 'agro'),
            'id' => 'archive_title_typo',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( '#nt-archive .nt-hero-title' ),
            'default' => array(
                'color' =>'',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            ),
            'required' => array( 'archive_hero_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Archive Slogan', 'agro'),
            'subtitle' => esc_html__('Add your archive page slogan here.', 'agro'),
            'id' =>'archive_slogan',
            'type' => 'textarea',
            'default' => '',
            'required' => array( 'archive_hero_onoff', '=', '1' ),
        ),
        array(
            'title' => esc_html__('Archive Slogan Typography', 'agro'),
            'id' => 'archive_slogan_typo',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( '#nt-archive .nt-hero-subtitle' ),
            'default' => array(
                'color' =>'',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            ),
            'required' => array( 'archive_hero_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Archive Description', 'agro'),
            'subtitle' => esc_html__('Add your archive page description here.', 'agro'),
            'id' =>'archive_desc',
            'type' => 'textarea',
            'default' => '',
            'required' => array( 'archive_hero_onoff', '=', '1' )
        ),
        array(
            'title' =>esc_html__('Archive Description Typography', 'agro'),
            'id' => 'archive_desc_typo',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( '#nt-archive .nt-hero-description' ),
            'default' => array(
                'color' =>'',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            ),
            'required' => array( 'archive_hero_onoff', '=', '1' )
        ),
        array(
            'title' => esc_html__('Archive Content Padding', 'agro'),
            'subtitle' => esc_html__('You can set the top spacing of the site archive page content.', 'agro'),
            'id' =>'archive_content_pad',
            'type' => 'spacing',
            'output' => array('#nt-archive .nt-theme-inner-container'),
            'mode' => 'padding',
            'units' => array('em', 'px'),
            'units_extended' => 'false',
            'default' => array(
                'padding-top' => '',
                'padding-right' => '',
                'padding-bottom' => '',
                'padding-left' => '',
                'units' => 'px',
            )
        )
    )));

    //404 PAGE SECTION
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('404 Page', 'agro'),
    'id' => 'errorsection',
    'icon' => 'el el-error',
    'fields' => array(
        array(
            'title' => esc_html__('404 Page Type', 'agro'),
            'subtitle' => esc_html__('Select 404 page different type from list.', 'agro'),
            'id' => 'error_page_type',
            'type' => 'select',
            'customizer' => true,
            'options' => array(
                'default' => esc_html__('Default', 'agro'),
                'custom-page' => esc_html__('Custom page from pages', 'agro'),
            ),
            'default' => 'default',
        ),
        array(
            'title' => esc_html__('Custom 404 Page Type', 'agro'),
            'subtitle' => esc_html__('Select custom 404 page type from page list.', 'agro'),
            'id' => 'error_select_page_type',
            'type' => 'select',
            'multi' => false,
            'data' => 'page',
            'required' => array( 'error_page_type', '=', 'custom-page' ),
        )
    )));
    //404 PAGE SECTION
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('404 Page Hero', 'agro'),
    'id' => 'errorherosubsection',
    'desc' => esc_html__('These are main settings for general theme!', 'agro'),
    'subsection' => true,
    'icon' => 'el el-brush',
    'fields' => array(
        array(
            'title' => esc_html__('404 Hero Display', 'agro'),
            'subtitle' => esc_html__('You can enable or disable the site 404 page hero section with switch option.', 'agro'),
            'id' =>'error_hero_onoff',
            'type' => 'switch',
            'default' => 1,
            'on' =>'On',
            'off' => 'Off',
            'required' => array( 'error_page_type', '=', 'default' ),
        ),
        array(
            'title' => esc_html__('404 Hero Alignment', 'agro'),
            'subtitle' => esc_html__('Select 404 page hero text alignment.', 'agro'),
            'id' => 'error_hero_alignment',
            'type' => 'select',
            'customizer'=> true,
            'options' => array(
                '' => esc_html__('Select alignment', 'agro'),
                'text-right' => esc_html__('right', 'agro'),
                'text-center' => esc_html__('center', 'agro'),
                'text-left' => esc_html__('left', 'agro'),
            ),
            'default' => 'text-left',
            'required' => array(
                array( 'error_page_type', '=', 'default' ),
                array( 'error_hero_onoff', '=', '1' )
            )
        ),
        array(
            'title' => esc_html__('404 Hero Background', 'agro'),
            'id' =>'error_hero_bg',
            'type' => 'background',
            'output' => array( '#nt-404 .hero-container' ),
            'required' => array(
                array( 'error_page_type', '=', 'default' ),
                array( 'error_hero_onoff', '=', '1' )
            )
        ),
        array(
            'title' => esc_html__('404 Hero Overlay Color', 'agro'),
            'id' =>'error_hero_overlay',
            'type' => 'color_rgba',
            'mode' => 'background',
            'default' => array(
            'alpha' => 0.5
            ),
            'output' => array( '#nt-404 .hero-container.hero-overlay:before' ),
            'required' => array(
                array( 'error_page_type', '=', 'default' ),
                array( 'error_hero_onoff', '=', '1' )
            )
        ),
        array(
            'title' => esc_html__('404 Page Hero Padding', 'agro'),
            'subtitle' => esc_html__('You can set the top spacing of the site error page hero section.', 'agro'),
            'id' =>'error_hero_pad',
            'type' => 'spacing',
            'output' => array('#nt-404 .hero-container'),
            'mode' => 'padding',
            'units' => array('em', 'px'),
            'units_extended' => 'false',
            'default' => array(
                'padding-top' => '',
                'padding-right' => '',
                'padding-bottom' => '',
                'padding-left' => '',
                'units' => 'px',
            ),
            'required' => array(
                array( 'error_page_type', '=', 'default' ),
                array( 'error_hero_onoff', '=', '1' )
            )
        ),
        array(
            'title' => esc_html__('404 Title', 'agro'),
            'subtitle' => esc_html__('Add your 404 page title here.', 'agro'),
            'id' =>'error_title',
            'type' => 'text',
            'default' => '404 - Not Found',
            'required' => array(
                array( 'error_page_type', '=', 'default' ),
                array( 'error_hero_onoff', '=', '1' )
            )
        ),
        array(
            'title' =>esc_html__('404 Title Typography', 'agro'),
            'id' => 'error_title_typo',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( '#nt-404 .nt-hero-title' ),
            'default' => array(
                'color' =>'',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            ),
            'required' => array(
                array( 'error_page_type', '=', 'default' ),
                array( 'error_hero_onoff', '=', '1' )
            )
        ),
        array(
            'title' => esc_html__('404 Slogan', 'agro'),
            'subtitle' => esc_html__('Add your 404 page slogan here.', 'agro'),
            'id' =>'error_slogan',
            'type' => 'textarea',
            'default' => 'Page',
            'required' => array(
                array( 'error_page_type', '=', 'default' ),
                array( 'error_hero_onoff', '=', '1' )
            )
        ),
        array(
            'title' =>esc_html__('404 Slogan Typography', 'agro'),
            'id' => 'error_slogan_typo',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( '#nt-404 .nt-hero-subtitle' ),
            'default' => array(
                'color' =>'',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            ),
            'required' => array( 'error_hero_onoff', '=', '1' ),
        ),
        array(
            'title' => esc_html__('404 Description', 'agro'),
            'subtitle' => esc_html__('Add your 404 page description here.', 'agro'),
            'id' =>'error_desc',
            'type' => 'textarea',
            'default' => '',
            'required' => array(
                array( 'error_page_type', '=', 'default' ),
                array( 'error_hero_onoff', '=', '1' )
            )
        ),
        array(
            'title' =>esc_html__('404 Description Typography', 'agro'),
            'id' => 'error_desc_typo',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( '#nt-404 .nt-hero-description' ),
            'default' => array(
                'color' =>'',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            ),
            'required' => array(
                array( 'error_page_type', '=', 'default' ),
                array( 'error_hero_onoff', '=', '1' )
            )
        ),
        //information
        array(
            'id' =>'info_error_hero',
            'type' => 'info',
            'style' => 'success',
            'title' => esc_html__('Info!', 'agro'),
            'icon' => 'el el-info-circle',
            'customizer' => false,
            'desc' => sprintf(esc_html__('These options is not compatible with %s.', 'agro'), '<b>404 Page Type : Custom</b>'),
            'required' => array( 'error_page_type', '==', 'custom-page' )
        )
    )));
    //404 PAGE SECTION
    Redux::setSection($agro_pre, array(
        'title' => esc_html__('404 Content', 'agro'),
        'id' => 'errorcontentsubsection',
        'subsection' => true,
        'icon' => 'el el-brush',
        'fields' => array(
            array(
                'title' => esc_html__('Content Type', 'agro'),
                'subtitle' => esc_html__('Select 404 page different type from list.', 'agro'),
                'id' => 'error_page_content_type',
                'type' => 'select',
                'customizer' => true,
                'options' => array(
                    'default' => esc_html__('Default', 'agro'),
                    'custom' => esc_html__('Custom Content', 'agro'),
                ),
                'default' => 'default',
                'required' => array( 'error_page_type', '=', 'default' )
            ),
            array(
                'title' => esc_html__('Create your custom 404 content', 'agro'),
                'subtitle' => esc_html__('HTML allowed (wp_kses)', 'agro'),
                'id' => 'error_page_custom_content',
                'type' => 'textarea',
                'validate' => 'html',
                'default' => '',
                'required' => array(
                    array( 'error_page_type', '=', 'default' ),
                    array( 'error_page_content_type', '=', 'custom' )
                )
            ),
            array(
                'title' => esc_html__('Content Image Display', 'agro'),
                'subtitle' => esc_html__('You can enable or disable the site 404 page content image with switch option.', 'agro'),
                'id' => 'error_content_image_visibility',
                'type' => 'switch',
                'default' => 1,
                'on' => 'On',
                'off' => 'Off',
                'required' => array(
                    array( 'error_page_type', '=', 'default' ),
                    array( 'error_page_content_type', '=', 'default' )
                )
            ),
            array(
                'title' => esc_html__('Content Image', 'agro'),
                'id' => 'error_content_image',
                'type' => 'media',
                'url' => true,
                'required' => array(
                    array( 'error_page_type', '=', 'default' ),
                    array( 'error_page_content_type', '=', 'default' ),
                    array( 'error_content_image_visibility', '=', '1' )
                )
            ),
            array(
                'title' => esc_html__('Content Title Display', 'agro'),
                'subtitle' => esc_html__('You can enable or disable the site 404 page content search form with switch option.', 'agro'),
                'id' => 'error_content_title_visibility',
                'type' => 'switch',
                'default' => 1,
                'on' => 'On',
                'off' => 'Off',
                'required' => array(
                    array( 'error_page_type', '=', 'default' ),
                    array( 'error_page_content_type', '=', 'default' )
                )
            ),
            array(
                'title' => esc_html__('Content Title', 'agro'),
                'subtitle' => esc_html__('Add your 404 page content title here.HTML allowed', 'agro'),
                'id' => 'error_content_title',
                'type' => 'textarea',
                'validate' => 'html',
                'default' => '',
                'required' => array(
                    array( 'error_page_type', '=', 'default' ),
                    array( 'error_page_content_type', '=', 'default' ),
                    array( 'error_content_title_visibility', '=', '1' )
                )
            ),
            array(
                'title' => esc_html__('Title Typography', 'agro'),
                'id' => 'error_content_title_typo',
                'type' => 'typography',
                'font-backup' => false,
                'letter-spacing' => true,
                'text-transform' => true,
                'all_styles' => true,
                'output' => array( '#nt-404 .error-content-title' ),
                'default' => array(
                    'color' => '',
                    'font-style' => '',
                    'font-family' => '',
                    'google' => true,
                    'font-size' => '',
                    'line-height' => ''
                ),
                'required' => array(
                    array( 'error_page_type', '=', 'default' ),
                    array( 'error_page_content_type', '=', 'default' ),
                    array( 'error_content_title_visibility', '=', '1' )
                )
            ),
            array(
                'title' => esc_html__('Content Description Display', 'agro'),
                'subtitle' => esc_html__('You can enable or disable the site 404 page content search form with switch option.', 'agro'),
                'id' => 'error_content_desc_visibility',
                'type' => 'switch',
                'default' => 1,
                'on' => 'On',
                'off' => 'Off',
                'required' => array(
                    array( 'error_page_type', '=', 'default' ),
                    array( 'error_page_content_type', '=', 'default' )
                )
            ),
            array(
                'title' => esc_html__('Content Description', 'agro'),
                'subtitle' => esc_html__('Add your 404 page content description here.HTML allowed', 'agro'),
                'id' => 'error_content_desc',
                'type' => 'textarea',
                'validate' => 'html',
                'default' => '',
                'required' => array(
                    array( 'error_page_type', '=', 'default' ),
                    array( 'error_page_content_type', '=', 'default' ),
                    array( 'error_content_desc_visibility', '=', '1' )
                )
            ),
            array(
                'title' =>esc_html__('Description Typography', 'agro'),
                'id' => 'error_page_content_desc_typo',
                'type' => 'typography',
                'font-backup' => false,
                'letter-spacing' => true,
                'text-transform' => true,
                'all_styles' => true,
                'output' => array( '#nt-404 .error-content-desc' ),
                'default' => array(
                    'color' =>'',
                    'font-style' => '',
                    'font-family' => '',
                    'google' => true,
                    'font-size' => '',
                    'line-height' => ''
                ),
                'required' => array(
                    array( 'error_page_type', '=', 'default' ),
                    array( 'error_page_content_type', '=', 'default' ),
                    array( 'error_content_desc_visibility', '=', '1' )
                )
            ),
            array(
                'title' => esc_html__('Description Margin', 'agro'),
                'subtitle' => esc_html__('You can set the spacing of the site error page content description.', 'agro'),
                'id' =>'error_content_pad',
                'type' => 'spacing',
                'output' => array('#nt-404 .error-content-desc'),
                'mode' => 'margin',
                'units' => array('em', 'px'),
                'units_extended' => 'false',
                'default' => array(
                    'margin-top' => '',
                    'margin-right' => '',
                    'margin-bottom' => '',
                    'margin-left' => '',
                    'units' => 'px'
                ),
                'required' => array(
                    array( 'error_page_type', '=', 'default' ),
                    array( 'error_page_content_type', '=', 'default' ),
                    array( 'error_content_desc_visibility', '=', '1' )
                )
            ),
            array(
                'title' => esc_html__('404 Content Search Form Display', 'agro'),
                'subtitle' => esc_html__('You can enable or disable the site 404 page content search form with switch option.', 'agro'),
                'id' => 'error_content_search_visibility',
                'type' => 'switch',
                'default' => 1,
                'on' => 'On',
                'off' => 'Off',
                'required' => array(
                    array( 'error_page_type', '=', 'default' ),
                    array( 'error_page_content_type', '=', 'default' )
                )
            ),
            array(
                'title' => esc_html__('404 Page Content Padding', 'agro'),
                'subtitle' => esc_html__('You can set the top spacing of the site error page content.', 'agro'),
                'id' =>'error_content_pad',
                'type' => 'spacing',
                'output' => array('#nt-404 .nt-theme-inner-container'),
                'mode' => 'padding',
                'units' => array('em', 'px'),
                'units_extended' => 'false',
                'default' => array(
                    'padding-top' => '',
                    'padding-right' => '',
                    'padding-bottom' => '',
                    'padding-left' => '',
                    'units' => 'px'
                ),
                'required' => array( 'error_page_type', '=', 'default' ),
            ),
            array(
                'title' => esc_html__('Content Background', 'agro'),
                'id' => 'error_content_bg',
                'type' => 'background',
                'output' => array( '#nt-404 .nt-theme-inner-container' ),
                'required' => array( 'error_page_type', '=', 'default' ),

            ),
            //information
            array(
                'id' =>'info_error_content',
                'type' => 'info',
                'style' => 'success',
                'title' => esc_html__('Info!', 'agro'),
                'icon' => 'el el-info-circle',
                'customizer' => false,
                'desc' => sprintf(esc_html__('These options is not compatible with %s.', 'agro'), '<b>404 Page Type : Custom</b>'),
                'required' => array( 'error_page_type', '!=', 'default' )
            )
    )));
    //SEARCH PAGE SECTION
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Search Page', 'agro'),
    'id' => 'searchsection',
    'desc' => esc_html__('These are main settings for general theme!', 'agro'),
    'icon' => 'el el-search',
    'fields' => array(
        array(
            'title' => esc_html__('Search Hero Display', 'agro'),
            'subtitle' => esc_html__('You can enable or disable the site search page hero section with switch option.', 'agro'),
            'id' =>'search_hero_onoff',
            'type' => 'switch',
            'default' => 1,
            'on' =>'On',
            'off' => 'Off',
        ),
        array(
            'title' => esc_html__('Search Hero Alignment', 'agro'),
            'subtitle' => esc_html__('Select search page hero text alignment.', 'agro'),
            'id' => 'search_hero_alignment',
            'type' => 'select',
            'customizer'=> true,
            'options' => array(
                '' => esc_html__('Select alignment', 'agro'),
                'text-right' => esc_html__('right', 'agro'),
                'text-center' => esc_html__('center', 'agro'),
                'text-left' => esc_html__('left', 'agro')
            ),
            'default' => 'text-left',
            'required' => array( 'search_hero_onoff', '=', '1' ),
        ),
        array(
            'title' => esc_html__('Search Hero Background', 'agro'),
            'id' =>'search_hero_bg',
            'type' => 'background',
            'output' => array( '#nt-search .hero-container' ),
            'required' => array( 'search_hero_onoff', '=', '1' ),
        ),
        array(
            'title' => esc_html__('Search Hero Overlay Color', 'agro'),
            'id' =>'search_hero_overlay',
            'type' => 'color_rgba',
            'mode' => 'background',
            'default' => array(
            'alpha' => 0.5
            ),
            'output' => array( '#nt-search .hero-container.hero-overlay:before' ),
            'required' => array( 'search_hero_onoff', '=', '1' ),
        ),
        array(
            'title' => esc_html__('Search Page Hero Padding', 'agro'),
            'subtitle' => esc_html__('You can set the top spacing of the site search page hero section.', 'agro'),
            'id' =>'search_hero_pad',
            'type' => 'spacing',
            'output' => array('#nt-search .hero-container'),
            'mode' => 'padding',
            'units' => array('em', 'px'),
            'units_extended' => 'false',
            'default' => array(
                'padding-top' => '',
                'padding-right' => '',
                'padding-bottom' => '',
                'padding-left' => '',
                'units' => 'px',
            ),
            'required' => array( 'search_hero_onoff', '=', '1' ),
        ),
        array(
            'title' => esc_html__('Search Title', 'agro'),
            'subtitle' => esc_html__('Add your search page title here.', 'agro'),
            'id' =>'search_title',
            'type' => 'text',
            'default' => '',
            'required' => array( 'search_hero_onoff', '=', '1' ),
        ),
        array(
            'title' =>esc_html__('Search Title Typography', 'agro'),
            'id' => 'search_title_typo',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( '#nt-search .nt-hero-title' ),
            'default' => array(
                'color' =>'',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            ),
            'required' => array( 'search_hero_onoff', '=', '1' ),
        ),
        array(
            'title' => esc_html__('Search Slogan', 'agro'),
            'subtitle' => esc_html__('Add your search page slogan here.', 'agro'),
            'id' =>'search_slogan',
            'type' => 'textarea',
            'default' => 'Search Completed',
            'required' => array( 'search_hero_onoff', '=', '1' ),
        ),
        array(
            'title' =>esc_html__('Search Slogan Typography', 'agro'),
            'id' => 'search_slogan_typo',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( '#nt-search .nt-hero-subtitle' ),
            'default' => array(
                'color' =>'',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            ),
            'required' => array( 'search_hero_onoff', '=', '1' ),
        ),
        array(
            'title' => esc_html__('Search Description', 'agro'),
            'subtitle' => esc_html__('Add your search page description here.', 'agro'),
            'id' =>'search_desc',
            'type' => 'textarea',
            'required' => array( 'search_hero_onoff', '=', '1' ),
        ),
        array(
            'title' =>esc_html__('Search Description Typography', 'agro'),
            'id' => 'search_desc_typo',
            'type' => 'typography',
            'font-backup' => false,
            'letter-spacing'=> true,
            'all_styles' => true,
            'output' => array( '#nt-search .nt-hero-description' ),
            'default' => array(
                'color' =>'',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            ),
            'required' => array( 'search_hero_onoff', '=', '1' ),
        ),
        array(
            'title' => esc_html__('Search Page Content Padding', 'agro'),
            'subtitle' => esc_html__('You can set the top spacing of the site search page content.', 'agro'),
            'id' =>'search_content_pad',
            'type' => 'spacing',
            'output' => array('#nt-search .nt-theme-inner-container'),
            'mode' => 'padding',
            'units' => array('em', 'px'),
            'units_extended' => 'false',
            'default' => array(
                'padding-top' => '',
                'padding-right' => '',
                'padding-bottom' => '',
                'padding-left' => '',
                'units' => 'px',
            ),
        ),
    )));
    //FOOTER SECTION
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Footer Contact Form 7', 'agro'),
    'desc' => esc_html__('These are main settings for general theme!', 'agro'),
    'id' => 'footerformsection',
    'icon' => 'el el-pencil',
    'fields' => array(
        array(
            'title' => esc_html__('Contact Form 7 Display', 'agro'),
            'subtitle' => esc_html__('You can enable or disable the contact form on the site with switch option.', 'agro'),
            'id' => 'footer_form_onoff',
            'type' => 'switch',
            'default' => 1,
            'on' => 'On',
            'off' =>'Off'
        ),
        array(
            'title' => esc_html__('Contact Form 7 Shortcode', 'agro'),
            'subtitle' => esc_html__('Add your contact form 7 shortcode here.', 'agro'),
            'id' =>'footer_form_shortcode',
            'type' => 'text',
            'required' => array( 'footer_form_onoff', '=', '1' ),
        ),
        array(
            'title' => esc_html__('Contact Form Section Heading', 'agro'),
            'subtitle' => esc_html__('Add your section heading here.', 'agro'),
            'id' =>'footer_form_heading',
            'type' => 'textarea',
            'default' => 'Get <span>in touch</span>',
            'required' => array( 'footer_form_onoff', '=', '1' ),
        ),
        array(
            'title' => esc_html__('Contact Form Section Description', 'agro'),
            'subtitle' => esc_html__('Add your section description here.', 'agro'),
            'id' =>'footer_form_desc',
            'type' => 'textarea',
            'default' => 'Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable.',
            'required' => array( 'footer_form_onoff', '=', '1' ),
        ),
        array(
            'title' => esc_html__('Form Section Background', 'agro'),
            'id' => 'form_bottom_bg',
            'type' => 'background',
            'output' => array( '.contact-form-area.section--dark-bg' ),
            'required' => array( 'footer_form_onoff', '=', '1' ),
        ),
        array(
            'title' => esc_html__('Form Section Background Image Overlay', 'agro'),
            'id' => 'form_bottom_bg_overlay',
            'type' => 'color_rgba',
            'mode' => 'background-color',
            'output' => array( '.contact-form-area.section--dark-bg.has-overlay-color:before' ),
            'required' => array( 'footer_form_onoff', '=', '1' ),
        ),

    )));
    //FOOTER SECTION
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Google Map', 'agro'),
    'desc' => esc_html__('These are main settings for general theme!', 'agro'),
    'id' => 'footermapsection',
    'icon' => 'el el-globe',
    'fields' => array(
        array(
            'title' => esc_html__('Google Map Display', 'agro'),
            'subtitle' => esc_html__('You can enable or disable the custom map on the site with switch option.', 'agro'),
            'id' => 'footer_map_onoff',
            'type' => 'switch',
            'default' => 1,
            'on' => 'On',
            'off' =>'Off'
        ),
        array(
            'title' => esc_html__('Google Map Apikey', 'agro'),
            'subtitle' => esc_html__('Add your map apikey here.', 'agro'),
            'desc' => esc_html__('You can find your API key on google maps page.', 'agro'),
            'id' =>'footer_map_apikey',
            'type' => 'text',
            'required' => array( 'footer_map_onoff', '=', '1' ),
        ),
        array(
            'title' => esc_html__('Coordinate Longitude', 'agro'),
            'desc' => esc_html__('Example for demo map: 44.958309', 'agro'),
            'id' =>'footer_map_longitude',
            'type' => 'text',
            'required' => array( 'footer_map_onoff', '=', '1' ),
        ),
        array(
            'title' => esc_html__('Coordinate Latitude', 'agro'),
            'desc' => esc_html__('Example for demo map: 34.109925', 'agro'),
            'id' =>'footer_map_latitude',
            'type' => 'text',
            'required' => array( 'footer_map_onoff', '=', '1' ),
        ),
        array(
            'title' => esc_html__('Map Marker Image', 'agro'),
            'id' =>'footer_map_marker',
            'type' => 'media',
            'url' => true,
            'required' => array( 'footer_map_onoff', '=', '1' ),
        ),
        array(
            'title' => esc_html__('Map Min Height', 'agro'),
            'id' => 'footer_map_minheight',
            'type' => 'slider',
            "default" => 255,
            "min" => 255,
            "step" => 1,
            "max" => 1000,
            'display_value' => 'label',
            'required' => array( 'footer_map_onoff', '=', '1' ),
        ),

    )));
    //FOOTER SECTION
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Footer', 'agro'),
    'desc' => esc_html__('These are main settings for general theme!', 'agro'),
    'id' => 'footersection',
    'icon' => 'el el-photo',
    'fields' => array(
        array(
            'title' => esc_html__('Footer Section Display', 'agro'),
            'subtitle' => esc_html__('You can enable or disable the site footer section on the site with switch option.', 'agro'),
            'id' => 'footer_onoff',
            'type' => 'switch',
            'default' => 1,
            'on' => 'On',
            'off' =>'Off'
        ),
        array(
            'title' => esc_html__('Use Custom Footer Template?', 'agro'),
            'subtitle' => esc_html__('Turn on this option if you want to use a template saved from WPBakery Page Builder settings for Footer.', 'agro'),
            'id' =>'use_custom_footer_template',
            'type' => 'switch',
            'default' => 0,
            'customizer'=> true,
            'on' =>'On',
            'off' => 'Off',
            'required' => array('footer_onoff', '=', '1')
        ),
        array(
            'title' => esc_html__('Select Template', 'agro'),
            'subtitle' => esc_html__('Select your saved template for Footer.', 'agro'),
            'id' => 'custom_footer_template',
            'type' => 'select',
            'customizer' => true,
            'options' => class_exists('Agro_Saved_Templates') ? Agro_Saved_Templates::get_vc_templates() : array('no-template' => esc_html__('No exists templates', 'agro')),
            'required' => array(
                array( 'footer_onoff', '=', '1' ),
                array( 'use_custom_footer_template', '=', '1' )
            )
        ),
        array(
            'title' => esc_html__('Footer Background', 'agro'),
            'id' => 'fw_bg',
            'type' => 'background',
            'output' => array( '#footer' ),
            'required' => array(
                array( 'footer_onoff', '=', '1' ),
                array( 'use_custom_footer_template', '=', '0' )
            )
        ),
        array(
            'title' => esc_html__('Footer Padding', 'agro'),
            'subtitle' => esc_html__('You can set the top spacing of the widget area.', 'agro'),
            'id' => 'fw_pad',
            'type' => 'spacing',
            'output' => array('.footer'),
            'mode' => 'padding',
            'units' => array('em', 'px'),
            'units_extended' => 'false',
            'default' => array(
            'padding-top' => '',
            'padding-right' => '',
            'padding-bottom' => '',
            'padding-left' => '',
            'units' => 'px',
            ),
            'required' => array(
                array( 'footer_onoff', '=', '1' ),
                array( 'use_custom_footer_template', '=', '0' )
            )
        )
    )));

    //WIDGET SETTINGS SUBSECTION
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Widget Area Customize', 'agro'),
    'id' => 'widgetsettingssection',
    'subsection' => true,
    'icon' => 'el el-cogs',
    'fields' => array(
        array(
            'title' => esc_html__('Create Custom Widgets Area', 'agro'),
            'subtitle' => sprintf('%s <a href="%s" target="_blank"><strong><i>%s</i></strong></a>',
            esc_html__('Enter your custom column for custom widget.Note: This option is based on the', 'agro'),
            esc_url('https://getbootstrap.com/docs/4.0/layout/grid/#grid-options'),
            esc_html__('BOOTSTRAP GRID SYSTEM.', 'agro')),
            'id' => 'custom_widgets',
            'type' => 'multi_text',
            'add_text' => esc_html__('Add Column', 'agro'),
            'default'=> array( 'col-12 col-sm-6 col-md-4','col-12 col-sm-6 col-md-4', 'col-12 col-sm-6 col-md-4' ),
            'show_empty'=> true,
            'customizer' => true,
            'required' => array(
                array( 'footer_onoff', '=', '1' ),
                array( 'use_custom_footer_template', '=', '0' )
            )
        ),
        array(
            'title' => esc_html__('Widget Title Color', 'agro'),
            'desc' => esc_html__('Set your own color for the widgets title.', 'agro'),
            'id' =>'fw_wt',
            'type' => 'color',
            'output' => array( '.nt-footer-widget .nt-sidebar-inner-widget-title' ),
            'required' => array(
                array( 'footer_onoff', '=', '1' ),
                array( 'use_custom_footer_template', '=', '0' )
            )
        ),
        array(
            'title' => esc_html__('Widget Link Color', 'agro'),
            'desc' => esc_html__('Set your own color for the widgets link.', 'agro'),
            'id' =>'fw_wa',
            'type' => 'color',
            'output' => array( '.nt-footer-widget a' ),
            'required' => array(
                array( 'footer_onoff', '=', '1' ),
                array( 'use_custom_footer_template', '=', '0' )
            )
        ),
        array(
            'title' => esc_html__('Widget Hover Link Color', 'agro'),
            'desc' => esc_html__('Set your own hover colors for the widgets link.', 'agro'),
            'id' =>'fw_hvra',
            'type' => 'color',
            'output' => array( '.nt-footer-widget a:hover' ),
            'required' => array(
                array( 'footer_onoff', '=', '1' ),
                array( 'use_custom_footer_template', '=', '0' )
            )
        ),
        array(
            'title' => esc_html__('Widget Text Color', 'agro'),
            'desc' => esc_html__('Set your own colors for the widgets.', 'agro'),
            'id' =>'fw_wp',
            'type' => 'color',
            'output' => array( '.nt-footer-widget' ),
            'required' => array(
                array( 'footer_onoff', '=', '1' ),
                array( 'use_custom_footer_template', '=', '0' )
            )
        )
    )));
    Redux::setSection($agro_pre, array(
    'title' => esc_html__('Footer Copyright', 'agro'),
    'desc' => esc_html__('These are main settings for general theme!', 'agro'),
    'id' => 'footercopyrightsubsection',
    'icon' => 'el el-photo',
    'subsection' => true,
    'fields' => array(
        array(
            'title' => esc_html__('Copyright Text', 'agro'),
            'subtitle' => esc_html__('HTML allowed', 'agro'),
            'desc' => esc_html__('Enter your site copyright text here.', 'agro'),
            'id' => 'footer_copyright',
            'type' => 'textarea',
            'validate' => 'html',
            'default' => '2019 Agro. All rights reserved. Created by NineTheme.',
            'required' => array(
                array( 'footer_onoff', '=', '1' ),
                array( 'use_custom_footer_template', '=', '0' )
            )
        ),
        array(
            'title' => esc_html__('Copyright Text Color', 'agro'),
            'desc' => esc_html__('Set your own color for the site copyright text.', 'agro'),
            'id' =>'footer_copyright_clr',
            'type' => 'color',
            'output' => array( '.nt-footer-copyright, .nt-footer-copyright .__copy, .nt-footer-copyright .__copy *' ),
            'required' => array(
                array( 'footer_onoff', '=', '1' ),
                array( 'use_custom_footer_template', '=', '0' )
            )
        ),
        array(
            'title' => esc_html__('Copyright Link Color', 'agro'),
            'desc' => esc_html__('Set your own color for the site copyright link.', 'agro'),
            'id' =>'footer_copyright_aclr',
            'type' => 'color',
            'output' => array( '.nt-footer-copyright .__copy a, #footer .__dev' ),
            'required' => array(
                array( 'footer_onoff', '=', '1' ),
                array( 'use_custom_footer_template', '=', '0' )
            )
        ),
        array(
            'title' => esc_html__('Copyright Hover Link Color', 'agro'),
            'desc' => esc_html__('Set your own hover color for the site copyright link.', 'agro'),
            'id' =>'footer_copyright_ahvrclr',
            'type' => 'color',
            'output' => array( '.nt-footer-copyright .__copy a:hover, #footer .__dev:hover' ),
            'required' => array(
                array( 'footer_onoff', '=', '1' ),
                array( 'use_custom_footer_template', '=', '0' )
            )
        ),
        array(
            'title' => esc_html__('Footer Menu Display', 'agro'),
            'subtitle' => esc_html__('You can enable or disable the bottom footer navigation.', 'agro'),
            'id' => 'footer_nav_onoff',
            'type' => 'switch',
            'default' => 0,
            'on' => 'On',
            'off' =>'Off',
            'required' => array(
                array( 'footer_onoff', '=', '1' ),
                array( 'use_custom_footer_template', '=', '0' )
            )
        ),
        array(
            'title' => esc_html__('Footer Menu Item Color', 'agro'),
            'desc' => esc_html__('Set your own color for the site footer menu item.', 'agro'),
            'id' =>'footer_nav_clr',
            'type' => 'color',
            'output' => array( '.nt-footer-nav ul li a, .footer--style-1 #footer__navigation a' ),
            'required' => array(
                array('footer_onoff', '=', '1'),
                array('footer_nav_onoff', '=', '1'),
                array( 'use_custom_footer_template', '=', '0' )
            )
        ),
        array(
            'title' => esc_html__('Hover Menu Item Color', 'agro'),
            'desc' => esc_html__('Set your own hover color for the site footer menu item.', 'agro'),
            'id' =>'footer_nav_hvrclr',
            'type' => 'color',
            'output' => array( '.nt-footer-nav ul li a:hover, .footer--style-1 #footer__navigation a:hover' ),
            'required' => array(
                array('footer_onoff', '=', '1'),
                array('footer_nav_onoff', '=', '1'),
                array( 'use_custom_footer_template', '=', '0' )
            )
        ),
        array(
            'title' => esc_html__('Hover Menu Item Line Color', 'agro'),
            'desc' => esc_html__('Set your own hover color for the site footer menu item.', 'agro'),
            'id' =>'footer_nav_lineclr',
            'type' => 'color',
            'mode' => 'background-color',
            'output' => array( '.nt-footer-nav ul li a:after, .footer--style-1 #footer__navigation a:after' ),
            'required' => array(
                array('footer_onoff', '=', '1'),
                array('footer_nav_onoff', '=', '1'),
                array( 'use_custom_footer_template', '=', '0' )
            )
        )
    )));
    Redux::setSection($agro_pre, array(
    'id' => 'custom_css_js_settings',
    'title' => esc_html__('Custom CSS & JS', 'agro'),
    'desc' => esc_html__( 'Please place your own CSS and javascript codes here (instead of placing them directly into theme codes) to keep them from removing after theme updates.', 'agro' ),
    'icon' => 'el el-css',
    'fields' => array(
        array(
            'id'       => 'editor_css',
            'type'     => 'ace_editor',
            'title' => __( 'CSS Code', 'agro' ),
            'subtitle' => __( 'These CSS codes will will be placed in the <head> tag.', 'agro' ),
            'mode'     => 'css',
            'theme' => 'monokai',
            'default'  => "#element-id { background:#ddd; }",
            'options'  => array('minLines'=> 20)
        ),
        array(
            'id'       => 'editor_js',
            'type'     => 'ace_editor',
            'title' => __( 'JS Code', 'agro' ),
            'subtitle' => __( 'These javascript codes will be placed before the < /body> tag in footer.', 'agro' ),
            'mode'     => 'javascript',
            'theme' => 'monokai',
            'compiler'  => true,
            'default'  => "$( 'some-element' ).remove();",
            'options'  => array('minLines'=> 20)
        ),
    )));

    Redux::setSection($agro_pre, array(
    'id' => 'inportexport_settings',
    'title' => esc_html__('Import / Export', 'agro'),
    'desc' => esc_html__('Import and Export your Theme Options from text or URL.', 'agro'),
    'icon' => 'el el-cog',
    'fields' => array(
        array(
            'id' => 'opt_import_export',
            'type' =>'import_export',
            'title' => '',
            'customizer' => false,
            'subtitle' => '',
            'full_width' => true,
        )
    )));
    Redux::setSection($agro_pre, array(
    'id' => 'nt_support_settings',
    'title' => esc_html__('Support', 'agro'),
    'icon' => 'el el-idea',
    'fields' => array(
        array(
            'id' => 'doc',
            'type' => 'raw',
            'markdown' => true,
            'class' => 'theme_support',
            'content' => '<div class="support-section">
            <h5>'.esc_html__('WE RECOMMEND YOU READ IT BEFORE YOU START', 'agro').'</h5>
            <h2><i class="el el-website"></i> '.esc_html__('DOCUMENTATION', 'agro').'</h2>
            <a target="_blank" class="button" href="https://ninetheme.com/support/">'.esc_html__('READ MORE', 'agro').'</a>
            </div>'
        ),
        array(
            'id' => 'support',
            'type' => 'raw',
            'markdown' => true,
            'class' => 'theme_support',
            'content' => '<div class="support-section">
            <h5>'.esc_html__('DO YOU NEED HELP?', 'agro').'</h5>
            <h2><i class="el el-adult"></i> '.esc_html__('SUPPORT CENTER', 'agro').'</h2>
            <a target="_blank" class="button" href="https://ninetheme.com/support/">'.esc_html__('GET SUPPORT', 'agro').'</a>
            </div>'
        ),
        array(
            'id' => 'portfolio',
            'type' => 'raw',
            'markdown' => true,
            'class' => 'theme_support',
            'content' => '<div class="support-section">
            <h5>'.esc_html__('SEE MORE THE NINETHEME WORDPRESS THEMES', 'agro').'</h5>
            <h2><i class="el el-picture"></i> '.esc_html__('NINETHEME PORTFOLIO', 'agro').'</h2>
            <a target="_blank" class="button" href="https://ninetheme.com/wordpress-themes/">'.esc_html__('SEE MORE', 'agro').'</a>
            </div>'
        ),
        array(
            'id' => 'like',
            'type' => 'raw',
            'markdown' => true,
            'class' => 'theme_support',
            'content' => '<div class="support-section">
            <h5>'.esc_html__('WOULD YOU LIKE TO REWARD OUR EFFORT?', 'agro').'</h5>
            <h2><i class="el el-thumbs-up"></i> '.esc_html__('PLEASE RATE US!', 'agro').'</h2>
            <a target="_blank" class="button" href="https://themeforest.net/downloads/">'.esc_html__('GET STARS', 'agro').'</a>
            </div>'
        )
    )));
    /*
     * <--- END SECTIONS
     */
     add_filter('redux/options/' .$agro_pre. '/compiler', 'compiler_actionn', 10, 3);


     function compiler_actionn($options, $css, $changed_values) {
         global $wp_filesystem;

         $filename = get_template_directory() . '/js/custom-editor.js';


         $css = array();
         if (array_key_exists('editor_js', $options)) {

             foreach ($options as $key => $val) {
                 if (isset($key) && $key == 'editor_js') {
                     array_push($css, $val);
                 }
             }
         }
$data ='(function($) {

"use strict";'.PHP_EOL
.implode('',$css).'
}(jQuery));';

         if( empty( $wp_filesystem ) ) {
             require_once( ABSPATH .'/wp-admin/includes/file.php' );
             WP_Filesystem();
         }

         if( $wp_filesystem ) {
             $wp_filesystem->put_contents(
                 $filename,
                 $data,
                 FS_CHMOD_FILE // predefined mode settings for WP files
             );
         }
     }

     /** Action hook examples **/
    function agro_remove_demo()
    {

        // Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
        if (class_exists('ReduxFrameworkPlugin')) {
            remove_action('admin_notices', array( ReduxFrameworkPlugin::get_instance(), 'admin_notices' ));
        }

    }
