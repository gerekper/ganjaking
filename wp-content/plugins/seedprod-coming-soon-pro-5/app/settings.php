<?php

/**
 * Save Settings
 */
function seedprod_pro_save_settings()
{
    if (check_ajax_referer('seedprod_nonce')) {
        if (!empty($_POST['settings'])) {
            $settings = stripslashes_deep($_POST['settings']);

            // publish page if not published when active
            $s = json_decode($settings );
            $update = array();
            $update['post_status'] = 'publish';
            
            if($s->enable_coming_soon_mode === true){
                $csp_id = get_option('seedprod_coming_soon_page_id'); 
                $update['ID'] = $csp_id;
            }
            if($s->enable_maintenance_mode === true){
                $mm_id = get_option('seedprod_maintenance_mode_page_id'); 
                $update['ID'] = $mm_id;
            }
            if($s->enable_404_mode === true){
                $p404_id = get_option('seedprod_404_page_id'); 
                $update['ID'] = $p404_id;
            }
            wp_update_post($update);

            update_option('seedprod_settings', $settings);

            $response = array(
            'status'=> 'true',
            'msg'=> __('Settings Updated', 'seedprod-pro')
        );
        } else {
            $response = array(
                'status'=> 'false',
                'msg'=> __('Error Updating Settings', 'seedprod-pro')
            );
        }

        // Send Response
        wp_send_json($response);
        exit;
    }
}