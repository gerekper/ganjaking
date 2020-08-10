<?php

    /**
     * ReduxFramework Barebones Sample Config File
     * For full documentation, please visit: http://docs.reduxframework.com/
     */

    if ( ! class_exists( 'Redux' ) ) {
        return;
    }

    // This is your option name where all the Redux data is stored.
    $opt_name = "wiopt";

    /**
     * ---> SET ARGUMENTS
     * All the possible arguments for Redux.
     * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
     * */

    $args = array(
        // TYPICAL -> Change these values as you need/desire
        'opt_name'             => $opt_name,
        // This is where your data is stored in the database and also becomes your global variable name.
        'display_name'         => __( 'WooInstant Settings', 'wooinstant' ),
        // Name that appears at the top of your panel
        //'display_version'      => __( '1.0.0', 'wooinstant' ),
        // Version that appears at the top of your panel
        'menu_type'            => 'menu',
        //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
        'allow_sub_menu'       => true,
        // Show the sections below the admin menu item or not
        'menu_title'           => __( 'WooInstant', 'wooinstant' ),
        'page_title'           => __( 'WooInstant - WooCommerce Instant / One Page Checkout Settings', 'wooinstant' ),
        // You will need to generate a Google API key to use this feature.
        // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
        'google_api_key'       => '',
        // Set it you want google fonts to update weekly. A google_api_key value is required.
        'google_update_weekly' => false,
        // Must be defined to add google fonts to the typography module
        'async_typography'     => true,
        // Use a asynchronous font on the front end or font string
        //'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
        'admin_bar'            => false,
        // Show the panel pages on the admin bar
        'admin_bar_icon'       => 'dashicons-cart',
        // Choose an icon for the admin bar menu
        'admin_bar_priority'   => 50,
        // Choose an priority for the admin bar menu
        'global_variable'      => '',
        // Set a different name for your global variable other than the opt_name
        'dev_mode'             => false,
        // Show the time the page took to load, etc
        'update_notice'        => false,
        // If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
        'customizer'           => false,
        // Enable basic customizer support
        //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
        'disable_save_warn' => true,                    // Disable the save warning when a user changes a field

        // OPTIONAL -> Give you extra features
        'page_priority'        => null,
        // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
        'page_parent'          => 'themes.php',
        // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
        'page_permissions'     => 'manage_options',
        // Permissions needed to access the options panel.
        'menu_icon'            => 'dashicons-cart',
        // Specify a custom URL to an icon
        'last_tab'             => '',
        // Force your panel to always open to a specific tab (by id)
        'page_icon'            => 'icon-themes',
        // Icon displayed in the admin panel next to your menu_title
        'page_slug'            => '_woinstant',
        // Page slug used to denote the panel
        'save_defaults'        => true,
        // On load save the defaults to DB before user clicks save or not
        'default_show'         => false,
        // If true, shows the default value next to each field that is not the default value.
        'default_mark'         => '',
        // What to print by the field's title if the value shown is default. Suggested: *
        'show_import_export'   => true,
        // Shows the Import/Export panel when not used as a field.

        // CAREFUL -> These options are for advanced use only
        'transient_time'       => 60 * MINUTE_IN_SECONDS,
        'output'               => true,
        // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
        'output_tag'           => true,
        // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
        'footer_credit'     => ' ',
        // Disable the footer credit of Redux. Please leave if you can help it.

        // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
        'database'             => '',
        // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!

        'use_cdn'              => true,
        // If you prefer not to use the CDN for Select2, Ace Editor, and others, you may download the Redux Vendor Support plugin yourself and run locally or embed it in your code.

        //'compiler'             => true,

        // HINTS
        'hints'                => array(
            'icon'          => 'el el-question-sign',
            'icon_position' => 'right',
            'icon_color'    => 'lightgray',
            'icon_size'     => 'normal',
            'tip_style'     => array(
                'color'   => 'light',
                'shadow'  => true,
                'rounded' => false,
                'style'   => '',
            ),
            'tip_position'  => array(
                'my' => 'top left',
                'at' => 'bottom right',
            ),
            'tip_effect'    => array(
                'show' => array(
                    'effect'   => 'slide',
                    'duration' => '500',
                    'event'    => 'mouseover',
                ),
                'hide' => array(
                    'effect'   => 'slide',
                    'duration' => '500',
                    'event'    => 'click mouseleave',
                ),
            ),
        )
    );

    Redux::setArgs( $opt_name, $args );

    /*
     * ---> END ARGUMENTS
     */


    /*
     *
     * ---> START SECTIONS
     *
     */

    // -> START Basic Fields
    Redux::setSection( $opt_name, array(
        'title'  => __( 'General', 'wooinstant' ),
        'desc'   => __( 'General Options', 'wooinstant' ),
        'icon'   => 'el el-cogs',
        'fields' => array(
            array(
                'id'       => 'wi-active',
                'type'     => 'switch',
                'title'    => __( 'Active WooInstant', 'wooinstant' ),
                'default'   => 1,
            ),
            array(
                'id'       => 'wi-show-oncart',
                'type'     => 'button_set',
                'title'    => __( 'Show On Cart Page', 'wooinstant' ),
                'options' => array(
                    '1' => __( 'Yes', 'wooinstant' ),
                    '0' => __( 'No', 'wooinstant' ),
                 ),
                'default'   => '0',
            ),
            array(
                'id'       => 'wi-show-oncheckout',
                'type'     => 'button_set',
                'title'    => __( 'Show On Checkout Page', 'wooinstant' ),
                'options' => array(
                    '1' => __( 'Yes', 'wooinstant' ),
                    '0' => __( 'No', 'wooinstant' ),
                 ),
                'default'   => '0',
            ),
            array(
                'id'       => 'wi-disable-quickview',
                'type'     => 'button_set',
                'title'    => __( 'Disable Quick View', 'wooinstant' ),
                'subtitle' => __( 'You can disable it if you already have quick view function in your theme', 'wooinstant' ),
                'options' => array(
                    '1' => __( 'Yes', 'wooinstant' ),
                    '0' => __( 'No', 'wooinstant' ),
                 ),
                'default'   => '0',
            ),
            array(
                'id'       => 'wi-disable-ajax-add-cart',
                'type'     => 'button_set',
                'title'    => __( 'Disable Ajax Add to Cart', 'wooinstant' ),
                'subtitle' => __( 'You can disable it if you already have ajax add to cart function in your theme', 'wooinstant' ),
                'options' => array(
                    '1' => __( 'Yes', 'wooinstant' ),
                    '0' => __( 'No', 'wooinstant' ),
                 ),
                'default'   => '0',
            ),
            array(
                'id'       => 'wi-window-type',
                'type'     => 'button_set',
                'title'    => __('Drawer Window Type', 'wooinstant'),
                'subtitle' => __( 'Choose how the window will looks like', 'wooinstant' ),
                'options' => array(
                    '0' => __( 'Multi Step', 'wooinstant' ),
                    '1' => __( 'Single Step', 'wooinstant' ),
                 ),
                'default' => '0'
            ),
            array(
                'id'       => 'wi-active-window',
                'type'     => 'button_set',
                'title'    => __('Choose Active Window', 'wooinstant'),
                'subtitle' => __( 'Actived window will open first when wooinstant open', 'wooinstant' ),
                'options' => array(
                    '0' => __( 'Cart', 'wooinstant' ),
                    '1' => __( 'Checkout', 'wooinstant' ),
                 ),
                'default' => '0',
                'required' => array('wi-window-type','equals','0'),
            ),
            array(
                'id'       => 'wi-hide-close-btn',
                'type'     => 'button_set',
                'title'    => __( 'Hide Close Button?', 'wooinstant' ),
                'options' => array(
                    '1' => __( 'Yes', 'wooinstant' ),
                    '0' => __( 'No', 'wooinstant' ),
                 ),
                'default'   => '0',
            ),
        )
    ) );
    Redux::setSection( $opt_name, array(
        'title'  => __( 'Design', 'wooinstant' ),
        'desc'   => __( 'Design Options', 'wooinstant' ),
        'icon'   => 'el el-magic',
        'fields' => array(
            array(
                'id'       => 'wi-drawer-direction',
                'type'     => 'button_set',
                'title'    => __('Choose Cart Drawer Direction', 'wooinstant'),
                'options' => array(
                    '0' => __( 'Right to Left', 'wooinstant' ),
                    '1' => __( 'Left to Right', 'wooinstant' ),
                    //'2' => __( 'Top to Bottom', 'wooinstant' ),
                    //'3' => __( 'Bottom to Top', 'wooinstant' ),
                 ),
                'default' => '0'
            ),
            array(
                'id'       => 'wi-container-bg',
                'type'     => 'color',
                'title'    => __( 'Panel Background', 'wooinstant' ),
                'subtitle' => __( 'WooInstant Cart Panel Background Color', 'wooinstant' ),
                'default'  => '#f5f5f5',
                'transparent'  => false,
            ),
            array(
                'id'       => 'wi-header-bg',
                'type'     => 'color',
                'title'    => __( 'Cart Toggler Background', 'wooinstant' ),
                'subtitle' => __( 'WooInstant Cart Panel Toggler Background Color', 'wooinstant' ),
                'default'  => '#f5f5f5',
                'transparent'  => false,
            ),
            array(
                'id'       => 'wi-icon-choice',
                'type'     => 'button_set',
                'title'    => __('Choose Icon or Custom Image', 'wooinstant'),
                'options' => array(
                    '1' => __( 'Default Icon', 'wooinstant' ),
                    '2' => __( 'Custom Image', 'wooinstant' ),
                 ),
                'default' => '1'
            ),
            array(
                'id'       => 'wi-cart-image',
                'type'     => 'media',
                'title'    => __( 'Cart icon', 'wooinstant' ),
                'subtitle' => __( 'Upload your cart icon. Recommended size of an icon is 30x30px', 'wooinstant' ),
                'placeholder'   => __('Upload image/icon', 'wooinstant' ),
                'required' => array('wi-icon-choice','equals','2')
            ),
            array(
                'id'       => 'wi-header-text-color',
                'type'     => 'color',
                'title'    => __( 'Cart Toggler Color', 'wooinstant' ),
                'subtitle' => __( 'WooInstant Cart Panel Toggler Text/Icon Color', 'wooinstant' ),
                'default'  => '#272727',
                'transparent'  => false,
                'required' => array('button-set-single','equals','1')
            ),
            array(
                'id'       => 'wi-header-text-hovcolor',
                'type'     => 'color',
                'title'    => __( 'Cart Toggler Hover Color', 'wooinstant' ),
                'desc'     => __( 'Default is: Theme Default', 'wooinstant' ),
                'subtitle' => __( 'WooInstant Cart Panel Toggler Text/Icon Hover Color', 'wooinstant' ),
                'default'  => '',
                'transparent'  => false,
                'required' => array('button-set-single','equals','1')
            ),
            array(
                'id'       => 'wi-quickview-bg',
                'type'     => 'color',
                'title'    => __( 'Quick View Background', 'wooinstant' ),
                'subtitle' => __( 'WooInstant Quick View Panel Background Color', 'wooinstant' ),
                'default'  => '#f5f5f5',
                'transparent'  => false,
            ),
            array(
                'id'       => 'wi-zindex',
                'type'     => 'slider',
                'title'    => __( 'Panel Z-index', 'wooinstant' ),
                'subtitle' => __( 'Control WooInstant z-index from this option. More about <a target="_blank" href="https://css-tricks.com/almanac/properties/z/z-index/">z-index</a>', 'wooinstant' ),
                'default' => 999,
                'min' => 999,
                'step' => 5,
                'max' => 99999,
            ),
            array(
                'id'       => 'wi-custom-css',
                'type'     => 'ace_editor',
                'title'    => __( 'Custom CSS', 'wooinstant' ),
                'subtitle' => __( 'If you want to make extra CSS then you can do it from here', 'wooinstant' ),
                'mode'   => 'css',
                'theme'    => 'monokai',
                'default'  => ".wi-container .cart_totals {\n    width: 100%!important;\n}"
            ),
        )
    ) );


    /*
     * <--- END SECTIONS
     */
