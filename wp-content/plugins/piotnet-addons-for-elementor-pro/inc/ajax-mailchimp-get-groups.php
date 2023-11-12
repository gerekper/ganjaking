<?php
require_once (__DIR__.'/helper/functions.php');
add_action( 'wp_ajax_pafe_mailchimp_get_groups', 'pafe_mailchimp_get_groups' );
add_action( 'wp_ajax_nopriv_pafe_mailchimp_get_groups', 'pafe_mailchimp_get_groups' );

function pafe_mailchimp_get_groups(){
    $api_key = $_REQUEST['api'];
    $list_id = $_REQUEST['list_id'];
    $helper = new PAFE_Helper();
    if($api_key == 'false'){
        $api_key = get_option('piotnet-addons-for-elementor-pro-mailchimp-api-key');
    }
    if($api_key != 'false' && !empty($list_id)){
        $groups = array();
        $url = 'https://' . substr($api_key,strpos($api_key,'-')+1) . '.api.mailchimp.com/3.0/lists/'.$list_id.'/interest-categories/';
        $result = json_decode( $helper->mailchimp_curl_get_connect( $url, 'GET', $api_key, $data) );
        $html = '<div class="mailchimp-group-title">Groups</div>';
        foreach($result->categories as $cat){
            $categorys = pafe_get_groups_category($api_key, $list_id, $cat->id, $cat->title);
            foreach($categorys as $key => $val){
                $html .= '<div class="piotnet-mailchimp-api__inner"><label>'.$key.'</label><div><input type="text" value="'.$val.'" readonly></div></div>';
            }
        }
        echo $html;
        wp_die();
    }else{
        echo "Please check API key and List ID not empty.";
        wp_die();
    }
}
function pafe_get_groups_category($api_key, $list_id, $group_id, $title){
    $data = array(
        'count' => 100,
    );
    $helper = new PAFE_Helper();
    $category = array();
    $ids = '';
    $url = 'https://' . substr($api_key,strpos($api_key,'-')+1) . '.api.mailchimp.com/3.0/lists/'.$list_id.'/interest-categories/'.$group_id.'/interests/';
    $result = json_decode( $helper->mailchimp_curl_get_connect( $url, 'GET', $api_key, $data) );
    foreach($result->interests as $item){
        $category[$title.'-'.$item->name] = $item->id;
    }
    return $category;
}