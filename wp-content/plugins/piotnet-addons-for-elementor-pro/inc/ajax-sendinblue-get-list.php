<?php
require_once (__DIR__.'/helper/functions.php');
add_action( 'wp_ajax_pafe_sendinblue_get_list', 'pafe_sendinblue_get_list' );
add_action( 'wp_ajax_nopriv_pafe_sendinblue_get_list', 'pafe_sendinblue_get_list' );

function pafe_sendinblue_get_list(){
    $api_key = $_REQUEST['apiKey'];
    $helper = new PAFE_Helper();
    if($api_key == 'false'){
        $api_key = get_option('piotnet-addons-for-elementor-pro-sendinblue-api-key');
    }
    if($api_key){
        $lists = json_decode($helper->sendinblue_get_list($api_key))->lists;
        echo '<h3 class="pafe-sendinblue-title">Lists:</h3>';
        foreach($lists as $key => $val){
            echo '<div class="pafe-sendinblue-item"><label>'.$val->name.'</label><div class="pafe-sendinblue-item-id"><input type="text" value="'.$val->id.'" readonly></div></div>';
        }
    }
    wp_die();
}