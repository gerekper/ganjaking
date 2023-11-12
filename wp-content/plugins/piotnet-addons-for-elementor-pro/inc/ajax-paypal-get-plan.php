<?php
require_once (__DIR__.'/helper/functions.php');
add_action( 'wp_ajax_pafe_paypal_get_plan', 'pafe_paypal_get_plan' );
add_action( 'wp_ajax_nopriv_pafe_paypal_get_plan', 'pafe_paypal_get_plan' );

function pafe_paypal_get_plan(){
    $sand_box = $_REQUEST['sandbox'];
    $paypal_url = $sand_box == 'yes' ? 'https://api-m.sandbox.paypal.com/' : 'https://api-m.paypal.com/';
    $client_id = get_option('piotnet-addons-for-elementor-pro-paypal-client-id');
    $client_secret = get_option('piotnet-addons-for-elementor-pro-paypal-client-secret');
    if($sand_box == 'yes'){
        $token = get_option('piotnet-addons-for-elementor-pro-paypal-sandbox-token');
        $token_expires = get_option('piotnet-addons-for-elementor-pro-paypal-sandbox-expires');
    }else{
        $token = get_option('piotnet-addons-for-elementor-pro-paypal-token');
        $token_expires = get_option('piotnet-addons-for-elementor-pro-paypal-expires');
    }
    if(empty($token) || empty($token_expires) || intval($token_expires) < time()){
        $helper = new PAFE_Helper();
        $token = $helper->pafe_paypal_get_token($client_id, $client_secret, $paypal_url);
        if($sand_box == 'yes'){
            update_option('piotnet-addons-for-elementor-pro-paypal-sandbox-token', $token);
            update_option('piotnet-addons-for-elementor-pro-paypal-sandbox-expires', time()+30000);
        }else{
            update_option('piotnet-addons-for-elementor-pro-paypal-token', $token);
            update_option('piotnet-addons-for-elementor-pro-paypal-expires', time()+30000);
        }
    }
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => $paypal_url.'v1/billing/plans',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Authorization: Bearer '.$token
    ),
    ));
    $response = json_decode(curl_exec($curl))->plans;
    curl_close($curl);
    if(!empty($response)){
        $html = '';
        foreach($response as $plan){
            $html .= '<div class="pafe-paypal-plan-item"><label>'.$plan->name.'</label><div><input type="text" value="'.$plan->id.'" readonly></div></div>';
        }
        echo $html;
    }else{
        echo "No plans have been created yet";
    }
    wp_die();
}