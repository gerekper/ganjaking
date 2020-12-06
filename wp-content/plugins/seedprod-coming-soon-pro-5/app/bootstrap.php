<?php


/**
 * Enqueue Styles and Scripts
 */
function seedprod_pro_admin_enqueue_scripts($hook_suffix)
{
    // global admin style
    wp_enqueue_style(
        'seedprod-global-admin',
        SEEDPROD_PRO_PLUGIN_URL . 'public/css/global-admin.css',
        false,
        SEEDPROD_PRO_VERSION
    );

    $is_localhost = seedprod_pro_is_localhost();

    // Load our admin styles and scripts only on our pages
    if (strpos($hook_suffix, 'seedprod_pro') !==  false) {
        // remove conflicting scripts
        wp_dequeue_script('googlesitekit_admin');

        $vue_app_folder = 'pro';
        if (strpos($hook_suffix, 'seedprod_pro_builder') !==  false || strpos($hook_suffix, 'seedprod_pro_template') !==  false) {
            if ($is_localhost) {
                

                wp_register_script(
                    'seedprod_vue_builder_app',
                    'http://localhost:8083/index.js',
                    array(),
                    SEEDPROD_PRO_VERSION,
                    true
                );
                wp_enqueue_script('seedprod_vue_builder_app');
            
            } else {
                wp_register_script(
                    'seedprod_vue_builder_app_1',
                    SEEDPROD_PRO_PLUGIN_URL . 'public/'.$vue_app_folder.'/vue-backend/js/index.js',
                    array(),
                    SEEDPROD_PRO_VERSION,
                    true
                );
                wp_register_script(
                    'seedprod_vue_builder_app_2',
                    SEEDPROD_PRO_PLUGIN_URL . 'public/'.$vue_app_folder.'/vue-backend/js/chunk-vendors.js',
                    array(),
                    SEEDPROD_PRO_VERSION,
                    true
                );
                wp_register_script(
                    'seedprod_vue_builder_app_3',
                    SEEDPROD_PRO_PLUGIN_URL . 'public/'.$vue_app_folder.'/vue-backend/js/chunk-common.js',
                    array(),
                    SEEDPROD_PRO_VERSION,
                    true
                );
                wp_enqueue_script('seedprod_vue_builder_app_1');
                wp_enqueue_script('seedprod_vue_builder_app_2');
                wp_enqueue_script('seedprod_vue_builder_app_3');
                wp_enqueue_style('seedprod_vue_builder_app_css_1', SEEDPROD_PRO_PLUGIN_URL . 'public/'.$vue_app_folder.'/vue-backend/css/chunk-vendors.css', false, SEEDPROD_PRO_VERSION);
            }
        } else {
            if ($is_localhost) {
                
                wp_register_script(
                    'seedprod_vue_admin_app',
                    'http://localhost:8083/admin.js',
                    array(),
                    SEEDPROD_PRO_VERSION,
                    true
                );
                wp_enqueue_script('seedprod_vue_admin_app');
            
            } else {
                wp_register_script(
                    'seedprod_vue_admin_app_1',
                    SEEDPROD_PRO_PLUGIN_URL . 'public/'.$vue_app_folder.'/vue-backend/js/admin.js',
                    array(),
                    SEEDPROD_PRO_VERSION,
                    true
                );
                wp_register_script(
                    'seedprod_vue_admin_app_2',
                    SEEDPROD_PRO_PLUGIN_URL . 'public/'.$vue_app_folder.'/vue-backend/js/chunk-vendors.js',
                    array(),
                    SEEDPROD_PRO_VERSION,
                    true
                );
                wp_register_script(
                    'seedprod_vue_admin_app_3',
                    SEEDPROD_PRO_PLUGIN_URL . 'public/'.$vue_app_folder.'/vue-backend/js/chunk-common.js',
                    array(),
                    SEEDPROD_PRO_VERSION,
                    true
                );
                wp_enqueue_script('seedprod_vue_admin_app_1');
                wp_enqueue_script('seedprod_vue_admin_app_2');
                wp_enqueue_script('seedprod_vue_admin_app_3');
                wp_enqueue_style(
                    'seedprod_vue_admin_app_css_1',
                    SEEDPROD_PRO_PLUGIN_URL . 'public/'.$vue_app_folder.'/vue-backend/css/chunk-vendors.css',
                    false,
                    SEEDPROD_PRO_VERSION
                );
                // wp_enqueue_style(
                //     'seedprod_vue_admin_app_css_2',
                //     SEEDPROD_PRO_PLUGIN_URL . 'public/'.$vue_app_folder.'/vue-backend/css/admin.css',
                //     false,
                //     SEEDPROD_PRO_VERSION
                // );
            }
        }

        if (strpos($hook_suffix, 'seedprod_pro_builder') !==  false) {
            wp_enqueue_style(
                'seedprod-css',
                SEEDPROD_PRO_PLUGIN_URL . 'public/css/admin-style.min.css',
                false,
                SEEDPROD_PRO_VERSION
            );
            wp_enqueue_style(
                'seedprod-builder-css',
                SEEDPROD_PRO_PLUGIN_URL . 'public/css/tailwind-builder.min.css',
                false,
                SEEDPROD_PRO_VERSION
            );

            // Load WPForms CSS assets.
            if (function_exists('wpforms')) {
                add_filter( 'wpforms_global_assets', '__return_true' );
                wpforms()->frontend->assets_css();
            }
        }

        if (strpos($hook_suffix, 'seedprod_pro_template') !==  false) {
            wp_enqueue_style(
                'seedprod-css',
                SEEDPROD_PRO_PLUGIN_URL . 'public/css/admin-style.min.css',
                false,
                SEEDPROD_PRO_VERSION
            );
            wp_enqueue_style(
                'seedprod-builder-css',
                SEEDPROD_PRO_PLUGIN_URL . 'public/css/tailwind-builder.min.css',
                false,
                SEEDPROD_PRO_VERSION
            );
        }


        if (strpos($hook_suffix, 'seedprod_pro_builder') ===  false) {
            wp_enqueue_style(
                'seedprod-css',
                SEEDPROD_PRO_PLUGIN_URL . 'public/css/tailwind-admin.min.css',
                false,
                SEEDPROD_PRO_VERSION
            );
        }

        wp_enqueue_style('seedprod-google-fonts', 'https://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,700italic,400,600,700&display=swap', false);

        wp_enqueue_style(
            'seedprod-fontawesome',
            SEEDPROD_PRO_PLUGIN_URL . 'public/fontawesome/css/all.min.css',
            false,
            SEEDPROD_PRO_VERSION
        );

        wp_register_script(
            'seedprod-iframeresizer',
            SEEDPROD_PRO_PLUGIN_URL . 'public/js/iframeResizer.min.js',
            array(),
            SEEDPROD_PRO_VERSION,
            false
        );
        wp_enqueue_script('seedprod-iframeresizer');

        wp_enqueue_media();
        wp_enqueue_script('wp-tinymce');
        wp_enqueue_editor();
    }
}
add_action('admin_enqueue_scripts', 'seedprod_pro_admin_enqueue_scripts');



function seedprod_pro_wp_enqueue_styles()
{
    // wp_register_style(
    //     'seedprod-style',
    //     SEEDPROD_PRO_PLUGIN_URL . 'public/css/seedprod-style.min.css',
    //     false,
    //     SEEDPROD_PRO_VERSION
    //     );
    //wp_enqueue_style('seedprod-style');

    $is_user_logged_in = is_user_logged_in();
    if ($is_user_logged_in) {
        wp_enqueue_style(
            'seedprod-global-admin',
            SEEDPROD_PRO_PLUGIN_URL . 'public/css/global-admin.css',
            false,
            SEEDPROD_PRO_VERSION
        );
    }

    wp_register_style(
        'seedprod-fontawesome',
        SEEDPROD_PRO_PLUGIN_URL . 'public/fontawesome/css/all.min.css',
        false,
        SEEDPROD_PRO_VERSION
    );

    //wp_enqueue_style('seedprod-fontawesome');
}
add_action('init', 'seedprod_pro_wp_enqueue_styles');


/**
 * Display settings link on plugin page
 */
add_filter('plugin_action_links', 'seedprod_pro_plugin_action_links', 10, 2);

function seedprod_pro_plugin_action_links($links, $file)
{
    $plugin_file = SEEDPROD_PRO_SLUG;

    if ($file == $plugin_file) {
        $settings_link = '<a href="admin.php?page=seedprod_pro">Setup</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
}

/**
 * Remove other plugin's style from our page so they don't conflict
 */

add_action('admin_enqueue_scripts', 'seedprod_pro_deregister_backend_styles', PHP_INT_MAX);

function seedprod_pro_deregister_backend_styles()
{
    // remove scripts registered by the theme so they don't screw up our page's style
    if (isset($_GET['page']) && strpos($_GET['page'], 'seedprod_pro_builder') !==  false) {
        wp_dequeue_style( 'dashicons',9999 );
        $seedprod_builder_debug = get_option('seedprod_builder_debug');
        if (empty($seedprod_builder_debug)) {
        global $wp_styles;
        // list of styles to keep else remove
        $keep_styles = "media-views|editor-buttons|imgareaselect|buttons|wp-auth-check|wpforms-full|thickbox|wp-mediaelement";
        $s = explode("|", $keep_styles);

        $wpforms_url = plugins_url('wpforms');

        foreach ($wp_styles->queue as $handle) {
            //echo '<br> '.$handle;
            if (!in_array($handle, $s)) {
                if (strpos($handle, 'seedprod') ===  false) {
                    wp_dequeue_style($handle);
                    wp_deregister_style($handle);
                    //echo '<br>removed '.$handle;
                }
            }
        }

        // foreach ($wp_styles->registered as $handle => $asset) {
        //     //echo '<br> '.$handle;
        //     if (!in_array($handle, $s)) {
        //         if (strpos($handle, 'seedprod') === false && strpos($asset->src, $wpforms_url) === false) {
        //             wp_dequeue_style($handle);
        //             wp_deregister_style($handle);
        //             echo '<br>removed '.$handle;
        //         }
        //     }
        // }





        // remove scripts

            $s = 'admin-bar|common|utils|wp-auth-check|media-upload|jquery|media-editor|media-audiovideo|mce-view|image-edit|wp-tinymce|editor|quicktags|wplink|jquery-ui-autocomplete|thickbox|svg-painter|jquery-ui-core|jquery-ui-mouse|jquery-ui-accordion|jquery-ui-datepicker|jquery-ui-dialog|jquery-ui-slider|jquery-ui-sortable|jquery-ui-droppable|jquery-ui-tabs|jquery-ui-widget|wp-mediaelement';
            $d = explode("|", urldecode($s));

            global $wp_scripts;
            foreach ($wp_scripts->queue as $handle) :
       //echo '<br>removed '.$handle;

        if (!empty($d)) {
            if (!in_array($handle, $d)) {
                if (strpos($handle, 'seedprod') ===  false) {
                    wp_dequeue_script($handle);
                    wp_deregister_script($handle);
                    //echo '<br>removed '.$handle;
                }
            }
        }
            endforeach;
        }
    }
}


add_filter('admin_body_class', 'seedprod_pro_add_admin_body_classes');
function seedprod_pro_add_admin_body_classes($classes)
{
    if (!empty($_GET['page']) && strpos($_GET['page'], 'seedprod_pro') !==  false) {
        $classes .= ' seedprod-body seedprod-pro';
    }
    if (!empty($_GET['page']) && (strpos($_GET['page'], 'seedprod_pro_builder') !==  false)) {
        $classes .= ' seedprod-builder seedprod-pro';
    }
    return $classes;
}


// Review Request
add_action('admin_footer_text', 'seedprod_pro_admin_footer');

function seedprod_pro_admin_footer($text)
{
    global $current_screen;

    if (!empty($current_screen->id) && strpos($current_screen->id, 'seedprod') !== false && SEEDPROD_PRO_BUILD == 'lite') {
        $url  = 'https://wordpress.org/support/plugin/coming-soon/reviews/?filter=5#new-post';
        $text = sprintf(__('Please rate <strong>SeedProd</strong> <a href="%s" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on <a href="%s" target="_blank">WordPress.org</a> to help us spread the word. Thank you from the SeedProd team!', 'seedprod-pro'), $url, $url);
    }
    return $text;
}



// Add or Upgrade DB
add_action('admin_init', 'seedprod_pro_upgrade', 0);


/**
 * Upgrade setting pages. This allows you to run an upgrade script when the version changes.
 *
 */
function seedprod_pro_upgrade()
{

    // try to update license key
    $old_key = get_option('seed_cspv5_license_key');
    $new_key = get_option('seedprod_api_key');
    if(!empty($old_key) && empty($new_key)){
        update_option('seedprod_api_key',$old_key);
        $r = seedprod_pro_save_api_key($old_key);
    }

    // get current version
    $seedprod_current_version = get_option('seedprod_version');
    $upgrade_complete = false;
    if (empty($seedprod_current_version)) {
        $seedprod_current_version = 0;
    }

    //if ($seedprod_current_version === 0) {
    if (version_compare($seedprod_current_version, SEEDPROD_PRO_VERSION) === -1 || !empty($_GET['seedprod_force_db_setup'])) {
        // Upgrade db if new version
        seedprod_pro_database_setup();
        seedprod_pro_domain_mapping_db_setup();
        $upgrade_complete = true;
    }


    if ($upgrade_complete) {
        update_option('seedprod_version', SEEDPROD_PRO_VERSION);
    }
    //}
}

/**
 * Create Database to Store Emails
 */
function seedprod_pro_database_setup()
{
    global $wpdb;
    $tablename = $wpdb->prefix . 'csp3_subscribers';

    $sql = "CREATE TABLE `$tablename` (
            id int(11) unsigned NOT NULL AUTO_INCREMENT,
            page_id int(11) NOT NULL,
            page_uuid varchar(255) DEFAULT NULL,
            email varchar(255) DEFAULT NULL,
            fname varchar(255) DEFAULT NULL,
            lname varchar(255) DEFAULT NULL,
            ref_url varchar(255) DEFAULT NULL,
            clicks int(11) NOT NULL DEFAULT '0',
            conversions int(11) NOT NULL DEFAULT '0',
            referrer int(11) NOT NULL DEFAULT '0',
            confirmed int(11) NOT NULL DEFAULT '0',
            optin_confirm int(11) NOT NULL DEFAULT '0',
            ip varchar(255) DEFAULT NULL,
            meta text DEFAULT NULL,
            created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY csp3_subscribers_page_uuid_idx (page_uuid)
        );";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    dbDelta($sql);
}

/**
 * Create Domain Mapping Database
 */
function seedprod_pro_domain_mapping_db_setup()
{
    global $wpdb;
    $tablename = $wpdb->prefix . 'sp_domain_mapping';

    $sql = "CREATE TABLE `$tablename` (
            id int(11) unsigned NOT NULL AUTO_INCREMENT,
            domain varchar(255) DEFAULT NULL,
            path varchar(255) DEFAULT NULL,
            mapped_page_id int(11) NOT NULL,
            force_https boolean DEFAULT false,
            PRIMARY KEY  (id)
        );";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    dbDelta($sql);
}


function seedprod_pro_change_footer_version($str) {
    if (!empty($_GET['page']) && strpos($_GET['page'], 'seedprod_pro') !==  false) {
        return $str.' - SeedProd '.SEEDPROD_PRO_VERSION;
    }
}
add_filter( 'update_footer', 'seedprod_pro_change_footer_version', 9999 );
