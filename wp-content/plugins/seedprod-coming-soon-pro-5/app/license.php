<?php


/**
 * Welcome Page On Activation
 */
add_action('admin_init', 'seedprod_pro_welcome_screen_do_activation_redirect');

function seedprod_pro_welcome_screen_do_activation_redirect()
{
    // Check PHP Version
    if (version_compare(phpversion(), '5.3.3', '<=')) {
        wp_die(__("The minimum required version of PHP to run this plugin is PHP Version 5.3.3<br>Please contact your hosting company and ask them to upgrade this site's php verison.", 'seedprod-pro'), __("Upgrade PHP", 'seedprod-pro'), 200);
    }

    // Bail if no activation redirect
    if (! get_transient('_seedprod_welcome_screen_activation_redirect')) {
        return;
    }

    // Delete the redirect transient
    delete_transient('_seedprod_welcome_screen_activation_redirect');

    // Bail if activating from network, or bulk
    if (is_network_admin() || isset($_GET['activate-multi'])) {
        return;
    }

    // Redirect to our page
    wp_safe_redirect(add_query_arg(array( 'page' => 'seedprod_pro' ), admin_url('admin.php')).'#/welcome');
}



/**
 * Save API Key
 */
function seedprod_pro_save_api_key($api_key = null)
{
    if (check_ajax_referer('seedprod_nonce', '_wpnonce', false) || ! empty($api_key)) {
        if (empty($api_key)) {
            $api_key =  $_POST['api_key'];
        }

        if (defined('SEEDPROD_LOCAL_JS')) {
            $slug = 'seedprod-coming-soon-pro-5/seedprod-coming-soon-pro-5.php';
        }else{
            $slug = SEEDPROD_PRO_SLUG;
        }

        $token = get_option('seedprod_token');
        if(empty($token)){
            add_option('seedprod_token', wp_generate_uuid4());
        }

        // Validate the api key
        $data = array(
            'action'     => 'info',
            'license_key'  => $api_key,
            'token'  => get_option('seedprod_token'),
            'wp_version' => get_bloginfo('version'),
            'domain'     => home_url(),
            'installed_version'  => SEEDPROD_PRO_VERSION,
            'slug'  => $slug,
        );

        if (empty($data['license_key'])) {
            $response = array(
                'status'=> 'false',
                'msg'=> __('License Key is Required.','')
            );
            wp_send_json($response);
            exit;
        }

        $headers = array();

        // Build the headers of the request.
        $headers = wp_parse_args(
            $headers,
            array(
                'Accept'   => 'application/json',
            )
        );

        $url = SEEDPROD_PRO_API_URL.'update';
        $response = wp_remote_post($url, array('body' => $data,'headers'=> $headers));

        $status_code = wp_remote_retrieve_response_code($response);

        if (is_wp_error($response)) {
            $response = array(
                'status'=> 'false',
                'ip'=> seedprod_pro_get_ip(),
                'msg'=> $response->get_error_message(),
            );
            wp_send_json($response);
        }

        if ($status_code != 200) {
            $response = array(
                'status'=> 'false',
                'ip'=> seedprod_pro_get_ip(),
                'msg'=> $response['response']['message'],
            );
            wp_send_json($response);
        }

        
        $body = wp_remote_retrieve_body($response);

        if (!empty($body)) {
            $body = json_decode($body);
        }
    

        if (!empty($body->valid) && $body->valid === true) {
            // Store API key
            update_option('seedprod_user_id', $body->user_id);
            update_option('seedprod_api_token', $body->api_token);
            update_option('seedprod_api_key', $data['license_key']);
            update_option('seedprod_api_message', $body->message);
            update_option('seedprod_license_name', $body->license_name);
            update_option('seedprod_a', true);
            update_option('seedprod_per', $body->per);
            $response = array(
                'status'=> 'true',
                'license_name'=>sprintf( __("You currently have the <strong>%s</strong> license.", 'seedprod-pro' ),$body->license_name),
                'msg'=>$body->message,
                'body'=> $body,
            );
        } elseif(isset($body->valid) && $body->valid === false) {
            $api_msg =  __('Invalid License Key.', 'seedprod-pro');
            if ($body->message != 'Unauthenticated.') {
                $api_msg = $body->message;
            }
            update_option('seedprod_license_name', '');
            update_option('seedprod_api_token', '');
            update_option('seedprod_api_key', '');
            update_option('seedprod_api_message', $api_msg);
            update_option('seedprod_a', false);
            update_option('seedprod_per', '');
            $response = array(
                'status'=> 'false',
                'license_name'=>'',
                'msg'=> $api_msg,
                'body'=> $body,
            );
        }

        // Send Response
        if (!empty($_POST['api_key'])) {
            wp_send_json($response);
            exit;
        } else {
            return $response;
        }
    }
}


function seedprod_pro_force_license_recheck($api_key)
{
    if (!empty($_GET['token']) && !empty($_GET['license-key']) && !empty($_GET['license-key']) && $_GET['action'] == 'recheck-license') {
        $token = get_option('seedprod_token');
        $api_key = get_option('seedprod_api_key');
        $r = false;
        if ($_GET['token'] == $token && $_GET['license-key'] == $api_key) {
            $r = seedprod_pro_save_api_key(sanitize_text_field($_GET['license-key']));
        }
        wp_send_json($r);
    }
}


function seedprod_pro_deactivate_api_key($api_key = null)
{
    if (check_ajax_referer('seedprod_pro_deactivate_api_key', '_wpnonce', false) || empty($api_key)) {
        if (empty($api_key)) {
            $api_key =  $_POST['api_key'];
        }

        // Validate the api key
        $data = array(
            'api_token' => get_option('seedprod_api_token'),
            'api_key'  => $api_key,
            'token'  => get_option('seedprod_token'),
        );

        if (empty($data['api_key'])) {
            $response = array(
                'status'=> 'false',
                'msg'=> __('License Key is Required.','seedprod-pro')
            );
            wp_send_json($response);
            exit;
        }

        $headers = array();

        // Build the headers of the request.
        $headers = wp_parse_args(
            $headers,
            array(
                'Accept'   => 'application/json',
            )
        );

        $url = SEEDPROD_PRO_API_URL.'plugin-deactivate';
        $response = wp_remote_post($url, array('body' => $data,'headers'=> $headers));

        if (is_wp_error($response)) {
            $response = array(
                'status'=> 'false',
                'msg'=> $response->get_error_message()
            );
            wp_send_json($response);
        } else {
            if ($response['body'] == 'true') {
                update_option('seedprod_api_key', '');
                update_option('seedprod_license_name', '');
                update_option('seedprod_per', '');
                update_option('seedprod_api_token', '');
                update_option('seedprod_a',false);
                $response = array(
                    'status'=> 'true',
                    'msg'=> 'Deactivated'
                );
            } else {
                $response = array(
                'status'=> 'false',
                'msg'=>  __('There was an issue deactivating this license. Please log into the members area to deactivate.' , 'seedprod-pro'),
            );
            }
            wp_send_json($response);
        }
    }
}


    /**
     *  Deactivate License
     */
    function seedprod_pro_deactivate_license(){
        $token = get_option('seedprod_token');
        // $seedprod_api_key = '';
        // //   if(defined('SEED_CSP_API_KEY')){
        // //       $seedprod_api_key = SEED_CSP_API_KEY;
        // //   }
        //   if(empty($seedprod_api_key)){
        //       $seedprod_api_key = get_option('seedprod_license_key');
        //   }

        if((isset($_REQUEST['seedprod_token']) && $_REQUEST['seedprod_token'] == $token) && (isset($_REQUEST['seedprod_action']) && $_REQUEST['seedprod_action'] == 'deactivate')) {
                    $seedprod_per = '';
                    // if(!empty($_REQUEST['seedprod_per'])){
                    //     $seedprod_per = $_REQUEST['seedprod_per'];
                    // }

                    $seedprod_api_nag='Site Deactivated';
                    if(!empty($_REQUEST['seedprod_api_nag'])){
                        $seedprod_api_nag = $_REQUEST['seedprod_api_nag'];
                    }

                    update_option('seedprod_api_nag', $seedprod_api_nag);
                    update_option('seedprod_api_message',$seedprod_api_nag);
                    update_option('seedprod_api_key', '');
                    update_option('seedprod_license_name', '');
                    update_option('seedprod_per', '');
                    update_option('seedprod_api_token', '');
                    update_option('seedprod_a',false);

                    echo 'true';

            exit();
        }
    }


