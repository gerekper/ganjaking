<?php
require_once (__DIR__.'/helper/functions.php');
add_action( 'wp_ajax_pafe_sendinblue_get_attributes', 'pafe_sendinblue_get_attributes' );
add_action( 'wp_ajax_nopriv_pafe_sendinblue_get_attributes', 'pafe_sendinblue_get_attributes' );

function pafe_sendinblue_get_attributes(){
    $api_key = $_REQUEST['apiKey'];
    $helper = new PAFE_Helper();
    if($api_key == 'false'){
        $api_key = get_option('piotnet-addons-for-elementor-pro-sendinblue-api-key');
    }
    if($api_key){
        $attributes = json_decode($helper->sendinblue_get_attribute($api_key))->attributes;
        echo '<h3 class="pafe-sendinblue-title" style="margin-bottom: 5px;">Attributes:</h3>';
        echo '<div class="pafe-sendinblue-item"><input type="text" value="email" readonly></div>';
        foreach($attributes as $key => $val){
            echo '<div class="pafe-sendinblue-item"><input type="text" value="'.$val->name.'" readonly></div>';
        }
    }
    wp_die();
}