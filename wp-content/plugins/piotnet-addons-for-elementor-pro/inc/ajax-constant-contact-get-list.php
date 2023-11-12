<?php
    require_once(__DIR__.'/helper/functions.php');
    add_action( 'wp_ajax_pafe_constant_contact_get_list', 'pafe_constant_contact_get_list' );
    add_action( 'wp_ajax_nopriv_pafe_constant_contact_get_list', 'pafe_constant_contact_get_list');
    
    function pafe_constant_contact_get_list(){
        $access_token = get_option('piotnet-constant-contact-access-token');
        $constant_time_get_token = get_option('piotnet-constant-contact-time-get-token');
        if(time() > intval($constant_time_get_token + 7000)){
            $helper = new PAFE_Helper();
            $constant_contact_key = get_option('piotnet-addons-for-elementor-pro-constant-contact-client-id');
            $constant_contact_secret = get_option('piotnet-addons-for-elementor-pro-constant-contact-app-secret-id');
            $constant_contact_refresh_token = get_option('piotnet-constant-contact-refresh-token');
            $access_token = $helper->pafe_constant_contact_refresh_token($constant_contact_key, $constant_contact_secret, $constant_contact_refresh_token);
        }
        $html = '';
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.cc.email/v3/contact_lists',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'accept: application/json',
            'authorization: Bearer '.$access_token,
            'cache-control: no-cache',
            'content-type: application/json'
        ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response)->lists;
        if(!empty($response)){
            foreach($response as $list){
                $html .= '<div class="piotnet-constant-contact-list-item"><label>'.$list->name.'</label><div><input type="text" value="'.$list->list_id.'" readonly></div></div>';
            }
        }
        echo $html;
        wp_die();
    }