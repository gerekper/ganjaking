<?php
add_action( 'wp_ajax_pafe_mailpoet_get_custom_fields', 'pafe_mailpoet_get_custom_fields' );
add_action( 'wp_ajax_nopriv_pafe_mailpoet_get_custom_fields', 'pafe_mailpoet_get_custom_fields' );
function pafe_mailpoet_get_custom_fields(){
    if (class_exists(\MailPoet\API\API::class)) {
        $mailpoet_api = \MailPoet\API\API::MP('v1');
        $fields = $mailpoet_api->getSubscriberFields();
        $html = '';
        foreach($fields as $field){
            $html .= '<div class="piotnet-mailpoet-custom-field__inner"><label>'.$field['name'].'</label><div class="piotnet-mailpoet-custom-field__id"><input type="text" value="'.$field['id'].'" readonly></div></div>';
        }
        echo $html;
        wp_die();
    }else{
        echo "You have not installed the Mailpoet plugin";
        wp_die();
    }
}