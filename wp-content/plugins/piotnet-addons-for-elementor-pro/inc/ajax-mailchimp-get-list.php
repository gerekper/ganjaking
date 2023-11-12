<?php
require_once (__DIR__.'/helper/functions.php');
add_action( 'wp_ajax_pafe_mailchimp_select_list', 'pafe_mailchimp_select_list' );
add_action( 'wp_ajax_nopriv_pafe_mailchimp_select_list', 'pafe_mailchimp_select_list' );

function pafe_mailchimp_select_list(){
    $api_key = $_REQUEST['api'];
    $helper = new PAFE_Helper();
    if($api_key == 'false'){
        $api_key = get_option('piotnet-addons-for-elementor-pro-mailchimp-api-key');
    }
    if($api_key != false){
        $data = array(
            'fields' => 'lists',
            'count' => 100,
        );
        $url = 'https://' . substr($api_key,strpos($api_key,'-')+1) . '.api.mailchimp.com/3.0/lists/';
        $result = json_decode( $helper->mailchimp_curl_get_connect( $url, 'GET', $api_key, $data) );
        if( !empty($result->lists) ) {
            foreach($result->lists as $list){
                echo '<div class="pafe-mailchimp-list__item"><label>'.$list->name.' ('.$list->stats->member_count.')</label><div class="pafe-mailchimp-list__item-value"><input type="text" value="'.$list->id.'" readonly></div></div>';
            }
            wp_die();
        } elseif ( is_int( $result->status ) ) {
            echo '<strong>' . $result->title . ':</strong> ' . $result->detail;
        }
    }else{
        echo "Please enter the API key.";
        wp_die();
    }
}