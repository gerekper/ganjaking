<?php
require_once (__DIR__.'/helper/functions.php');
add_action( 'wp_ajax_pafe_mailchimp_merge_fields', 'pafe_mailchimp_merge_fields' );
add_action( 'wp_ajax_nopriv_pafe_mailchimp_merge_fields', 'pafe_mailchimp_merge_fields' );

function pafe_mailchimp_merge_fields(){
    $helper = new PAFE_Helper();
    $api_key = $_REQUEST['api'];
    $list_id = $_REQUEST['list_id'];
    if($api_key == 'false'){
        $api_key = get_option('piotnet-addons-for-elementor-pro-mailchimp-api-key');
    }
    if($api_key != 'false' && !empty($list_id)){
        $data = array(
            'count' => 100,
        );
        $url = 'https://' . substr($api_key,strpos($api_key,'-')+1) . '.api.mailchimp.com/3.0/lists/'.$list_id.'/merge-fields/';
        $result = json_decode( $helper->mailchimp_curl_get_connect( $url, 'GET', $api_key, $data) );
        if( !empty($result) ) {
            $html = '<div class="mailchimp-fields-title">All fields</div>';
            $html .= '<div class="data-mailchimp-merge-field__inner"><label>Email</label><div><input type="text" value="email_address" readonly></div></div>';
            foreach( $result->merge_fields as $fields ){
                if($fields->type == 'radio'){
                    $html .= '<div class="data-mailchimp-field-radio">';
                    $html .= '<div class="data-mailchimp-merge-field__inner"><label>'.$fields->name.'</label><div><input type="text" value="'.$fields->tag.'" readonly></div></div><div class="data-mailchimp-field-radio__items" style="margin-left:10px;">';
                    foreach($fields->options->choices as $option){
                        $html .= '<div class="data-mailchimp-merge-field__inner"><label style="font-style: italic;font-size:12px;">'.$option.'</label><div><input type="text" value="'.$option.'" readonly></div></div>';
                    }
                    $html .= '</div></div>';
                }else{
                    $html .= '<div class="data-mailchimp-merge-field__inner"><label>'.$fields->name.'</label><div><input type="text" value="'.$fields->tag.'" readonly></div></div>';
                }
            }
            $html .= '<div class="data-mailchimp-merge-field__inner"><label>Tags</label><div><input type="text" value="tags" readonly></div></div>';
            echo $html;
            wp_die();
        } elseif ( is_int( $result->status ) ) {
            echo '<strong>' . $result->title . ':</strong> ' . $result->detail;
            wp_die();
        }
    }else{
        echo "Please check API key and List ID not empty.";
        wp_die();
    }
}