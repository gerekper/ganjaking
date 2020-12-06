<?php
/**
 * Install addon.
 *
 * @since 1.0.0
 */
function seedprod_pro_install_addon()
{

    // Run a security check.
    check_ajax_referer('seedprod_pro_install_addon', 'nonce');

    // Check for permissions.
    if (! current_user_can('install_plugins')) {
        wp_send_json_error();
    }

 

    // Install the addon.
    if (isset($_POST['plugin'])) {
        $download_url = $_POST['plugin'];

        global $hook_suffix;
    
        // Set the current screen to avoid undefined notices.
        set_current_screen();
    
        // Prepare variables.
        $method = '';
        $url    = add_query_arg(
                array(
                    'page' => 'seedprod_pro'
                ),
                admin_url('admin.php')
            );
        $url = esc_url($url);
    
        // Start output bufferring to catch the filesystem form if credentials are needed.
        ob_start();
        if (false === ($creds = request_filesystem_credentials($url, $method, false, false, null))) {
            $form = ob_get_clean();
            echo json_encode(array( 'form' => $form ));
            wp_die();
        }
    
        // If we are not authenticated, make it happen now.
        if (! WP_Filesystem($creds)) {
            ob_start();
            request_filesystem_credentials($url, $method, true, false, null);
            $form = ob_get_clean();
            echo json_encode(array( 'form' => $form ));
            wp_die();
        }
    
        // We do not need any extra credentials if we have gotten this far, so let's install the plugin.
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        global $wp_version;
        if (version_compare($wp_version,'5.3.0') >= 0) {
            require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/includes/skin53.php';
        }else{
            require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/includes/skin.php';
        }
        
            
        // Create the plugin upgrader with our custom skin.
        $installer = new Plugin_Upgrader($skin = new SeedProd_Skin());
        $installer->install($download_url);
    
        // Flush the cache and return the newly installed plugin basename.
        wp_cache_flush();
        if ($installer->plugin_info()) {
            $plugin_basename = $installer->plugin_info();
            echo json_encode(array( 'plugin' => $plugin_basename ));
            wp_die();
        }
    }
    
    // Send back a response.
    echo json_encode(true);
    wp_die();
}


/**
 * Deactivate addon.
 *
 * @since 1.0.0
 */
function seedprod_pro_deactivate_addon()
{

    // Run a security check.
    check_ajax_referer('seedprod_pro_deactivate_addon', 'nonce');

    // Check for permissions.
    if (! current_user_can('activate_plugins')) {
        wp_send_json_error();
    }

    $type = 'addon';
    if (! empty($_POST['type'])) {
        $type = sanitize_key($_POST['type']);
    }

    if (isset($_POST['plugin'])) {
        deactivate_plugins($_POST['plugin']);

        if ('plugin' === $type) {
            wp_send_json_success(esc_html__('Plugin deactivated.', 'seedprod-pro'));
        } else {
            wp_send_json_success(esc_html__('Addon deactivated.', 'seedprod-pro'));
        }
    }

    wp_send_json_error(esc_html__('Could not deactivate the addon. Please deactivate from the Plugins page.', 'seedprod-pro'));
}


/**
 * Activate addon.
 *
 * @since 1.0.0
 */
function seedprod_pro_activate_addon()
{

    // Run a security check.
    check_ajax_referer('seedprod_pro_activate_addon', 'nonce');

    // Check for permissions.
    if (! current_user_can('activate_plugins')) {
        wp_send_json_error();
    }

    if (isset($_POST['plugin'])) {
        $type = 'addon';
        if (! empty($_POST['type'])) {
            $type = sanitize_key($_POST['type']);
        }

        $activate = activate_plugins($_POST['plugin']);

        if (! is_wp_error($activate)) {
            if ('plugin' === $type) {
                wp_send_json_success(esc_html__('Plugin activated.', 'seedprod-pro'));
            } else {
                wp_send_json_success(esc_html__('Addon activated.', 'seedprod-pro'));
            }
        }
    }

    wp_send_json_error(esc_html__('Could not activate addon. Please activate from the Plugins page.', 'seedprod-pro'));
}

function seedprod_pro_get_plugins_list()
{
    check_ajax_referer('seedprod_pro_get_plugins_list', 'nonce');

    $am_plugins = array(
        'google-analytics-for-wordpress/googleanalytics.php' => 'monsterinsights' ,
        'google-analytics-premium/googleanalytics-premium.php' => 'monsterinsights-pro' ,
        'optinmonster/optin-monster-wp-api.php' => 'optinmonster',
        'wp-mail-smtp/wp_mail_smtp.php'  => 'wpmailsmtp' ,
        'wp-mail-smtp-pro/wp_mail_smtp.php'  => 'wpmailsmtp-pro' ,
        'wpforms-lite/wpforms.php'  => 'wpforms' ,
        'wpforms/wpforms.php'  => 'wpforms-pro' ,
        'rafflepress/rafflepress.php'  => 'rafflepress' ,
        'rafflepress-pro/rafflepress-pro.php'  => 'rafflepress-pro' ,
        'trustpulse-api/trustpulse.php'  => 'trustpulse' ,
        'google-analytics-dashboard-for-wp/gadwp.php' => 'exactmetrics',
        'exactmetrics-premium/exactmetrics-premium.php' => 'exactmetrics-pro',
        'all-in-one-seo-pack/all_in_one_seo_pack.php' => 'all-in-one' ,
        'all-in-one-seo-pack-pro/all_in_one_seo_pack.php' => 'all-in-one-pro' ,
        'seo-by-rank-math/rank-math.php' => 'rank-math',
        'wordpress-seo/wp-seo.php'  => 'yoast' ,
        'autodescription/autodescription.php'  => 'seo-framework' ,
    );
    $all_plugins = get_plugins();

    $response = array();

    foreach ($am_plugins as $slug => $label) {
        if (array_key_exists($slug, $all_plugins)) {
            if (is_plugin_active($slug)) {
                $response[$label] = array('label' => __('Active', 'seedprod-pro'), 'status' => 1) ;
            } else {
                $response[$label] = array('label' => __('Inactive', 'seedprod-pro'), 'status' => 2);
            }
        } else {
            $response[$label]= array('label' =>  __('Not Installed', 'seedprod-pro'), 'status' => 0);
        }
    }


    wp_send_json($response);
}

function seedprod_pro_get_plugins_array()
{

    $am_plugins = array(
        'google-analytics-for-wordpress/googleanalytics.php' => 'monsterinsights' ,
        'google-analytics-premium/googleanalytics-premium.php' => 'monsterinsights-pro' ,
        'optinmonster/optin-monster-wp-api.php' => 'optinmonster',
        'wp-mail-smtp/wp_mail_smtp.php'  => 'wpmailsmtp' ,
        'wp-mail-smtp-pro/wp_mail_smtp.php'  => 'wpmailsmtp-pro' ,
        'wpforms-lite/wpforms.php'  => 'wpforms' ,
        'wpforms/wpforms.php'  => 'wpforms-pro' ,
        'rafflepress/rafflepress.php'  => 'rafflepress' ,
        'rafflepress-pro/rafflepress-pro.php'  => 'rafflepress-pro' ,
        'trustpulse-api/trustpulse.php'  => 'trustpulse' ,
        'google-analytics-dashboard-for-wp/gadwp.php' => 'exactmetrics',
        'exactmetrics-premium/exactmetrics-premium.php' => 'exactmetrics-pro',
        'all-in-one-seo-pack/all_in_one_seo_pack.php' => 'all-in-one' ,
        'all-in-one-seo-pack-pro/all_in_one_seo_pack.php' => 'all-in-one-pro' ,
        'seo-by-rank-math/rank-math.php' => 'rank-math',
        'wordpress-seo/wp-seo.php'  => 'yoast' ,
        'autodescription/autodescription.php'  => 'seo-framework' ,
    );
    $all_plugins = get_plugins();

    $response = array();

    foreach ($am_plugins as $slug => $label) {
        if (array_key_exists($slug, $all_plugins)) {
            if (is_plugin_active($slug)) {
                $response[$label] = array('label' => __('Active', 'seedprod-pro'), 'status' => 1) ;
            } else {
                $response[$label] = array('label' => __('Inactive', 'seedprod-pro'), 'status' => 2);
            }
        } else {
            $response[$label]= array('label' =>  __('Not Installed', 'seedprod-pro'), 'status' => 0);
        }
    }


    return $response;
}

function seedprod_pro_get_form_plugins_list()
{

    $am_plugins = array(
        'wpforms/wpforms.php' => 'wpforms' ,
        'wpforms-lite/wpforms.php' => 'wpforms-lite',
    );
    $all_plugins = get_plugins();

    $response = array();

    foreach ($am_plugins as $slug => $label) {
        if (array_key_exists($slug, $all_plugins)) {
            if (is_plugin_active($slug)) {
                $response[$label] = 1; // Active
            } else {
                $response[$label] = 2; // InActive
            }
        } else {
            $response[$label]= 0; // Not installed
        }
    }


    return $response;
}

function seedprod_pro_get_giveaway_plugins_list()
{

    $am_plugins = array(
        'rafflepress-pro/rafflepress-pro.php' => 'rafflepress-pro' ,
        'rafflepress/rafflepress.php' => 'rafflepress',
    );
    $all_plugins = get_plugins();

    $response = array();

    foreach ($am_plugins as $slug => $label) {
        if (array_key_exists($slug, $all_plugins)) {
            if (is_plugin_active($slug)) {
                $response[$label] = 1; // Active
            } else {
                $response[$label] = 2; // InActive
            }
        } else {
            $response[$label]= 0; // Not installed
        }
    }


    return $response;
}


function seedprod_pro_get_seo_plugins_list()
{

    $am_plugins = array(
        'all-in-one-seo-pack/all_in_one_seo_pack.php' => 'all-in-one' ,
        'seo-by-rank-math/rank-math.php' => 'rank-math',
        'wordpress-seo/wp-seo.php'  => 'yoast' ,
        'autodescription/autodescription.php'  => 'seo-framework' ,
    );
    $all_plugins = get_plugins();

    $response = array();

    foreach ($am_plugins as $slug => $label) {
        if (array_key_exists($slug, $all_plugins)) {
            if (is_plugin_active($slug)) {
                $response[$label] = 1; // Active
            } else {
                $response[$label] = 2; // InActive
            }
        } else {
            $response[$label]= 0; // Not installed
        }
    }


    return $response;
}

function seedprod_pro_get_analytics_plugins_list()
{

    $am_plugins = array(
        'google-analytics-for-wordpress/googleanalytics.php' => 'monster-insights' ,
        'google-analytics-dashboard-for-wp/gadwp.php' => 'exactmetrics',
    );
    $all_plugins = get_plugins();

    $response = array();

    foreach ($am_plugins as $slug => $label) {
        if (array_key_exists($slug, $all_plugins)) {
            if (is_plugin_active($slug)) {
                $response[$label] = 1; // Active
            } else {
                $response[$label] = 2; // InActive
            }
        } else {
            $response[$label]= 0; // Not installed
        }
    }


    return $response;
}

function seedprod_pro_get_plugins_install_url($slug)
{
    $action = 'install-plugin';
    $url = wp_nonce_url(
        add_query_arg(
            array(
                'action' => $action,
                'plugin' => $slug
            ),
            admin_url( 'update.php' )
        ),
        $action.'_'.$slug
    );

    return $url;

}

function seedprod_pro_get_plugins_activate_url($slug)
{
    $url =   wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . urlencode( $slug ), 'activate-plugin_' . $slug );
    return $url;

}