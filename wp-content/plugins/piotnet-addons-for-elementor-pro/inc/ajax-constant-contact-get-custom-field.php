<?php
    require_once(__DIR__.'/helper/functions.php');
    add_action( 'wp_ajax_pafe_constant_contact_get_custom_field', 'pafe_constant_contact_get_custom_field' );
    add_action( 'wp_ajax_nopriv_pafe_constant_contact_get_custom_field', 'pafe_constant_contact_get_custom_field');
    
    function pafe_constant_contact_get_custom_field(){
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
        CURLOPT_URL => 'https://api.cc.email/v3/contact_custom_fields',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Authorization: Bearer '. $access_token,
            'Content-Type: application/json'
        ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($response)->custom_fields;
        $html .= '<div class="piotnet-constant-contact-item" style="margin-top:10px"><label>Email</label><div><input type="text" value="email_address" readonly></div></div>';
        $html .= '<div class="piotnet-constant-contact-item"><label>First Name</label><div><input type="text" value="first_name" readonly></div></div>';
        $html .= '<div class="piotnet-constant-contact-item"><label>Last Name</label><div><input type="text" value="last_name" readonly></div></div>';
        $html .= '<div class="piotnet-constant-contact-item"><label>Phone</label><div><input type="text" value="phone_number" readonly></div></div>';
        $html .= '<div class="piotnet-constant-contact-item"><label>Job Title</label><div><input type="text" value="job_title" readonly></div></div>';
        $html .= '<div class="piotnet-constant-contact-item"><label>Company Name</label><div><input type="text" value="company_name" readonly></div></div>';
        $html .= '<div class="piotnet-constant-contact-item"><label>Birthday Month</label><div><input type="text" value="birthday_month" readonly></div></div>';
        $html .= '<div class="piotnet-constant-contact-item"><label>Birthday Day</label><div><input type="text" value="birthday_day" readonly></div></div>';
        $html .= '<div class="piotnet-constant-contact-item"><label>Anniversary</label><div><input type="text" value="anniversary" readonly></div></div>';
        $html .= '<div class="piotnet-constant-contact-item"><label>Taggings</label><div><input type="text" value="taggings" readonly></div></div>';
        if(!empty($result)){
            foreach($result as $tag){
                $html .= '<div class="piotnet-constant-contact-item"><label>'.$tag->label.'</label><div><input type="text" value="'.$tag->custom_field_id.'" readonly></div></div>';
            }
        }
        echo $html;
        wp_die();
    }