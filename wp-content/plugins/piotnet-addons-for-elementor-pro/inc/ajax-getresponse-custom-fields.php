<?php
    add_action( 'wp_ajax_pafe_getresponse_custom_fields', 'pafe_getresponse_custom_fields' );
    add_action( 'wp_ajax_nopriv_pafe_getresponse_custom_fields', 'pafe_getresponse_custom_fields' );

    function pafe_getresponse_custom_fields(){
        $api = $_REQUEST['api'];
        if($api == 'false'){
            $api = get_option('piotnet-addons-for-elementor-pro-getresponse-api-key');
        }
        $url_custom_fields = "https://api.getresponse.com/v3/custom-fields/";
        $ch = curl_init($url_custom_fields);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',
            'X-Auth-Token: api-key '.$api,
        ));
        $result =  json_decode(curl_exec($ch));
        if(!empty($result)){
            $html = '<br><div class="pafe-getresponse-custom-fields__inner"><label>email</label><div class="pafe-getresponse-custom-fields__inner-item"><input type="text" value="email" readonly/></div></div><div class="pafe-getresponse-custom-fields__inner"><label>name</label><div class="pafe-getresponse-custom-fields__inner-item"><input type="text" value="name" readonly/></div></div>';
            foreach($result as $item){
                $html .= '<div class="pafe-getresponse-custom-fields__inner"><label>'.$item->name.'('.$item->fieldType.')</label><div class="pafe-getresponse-custom-fields__inner-item"><input type="text" value="'.$item->customFieldId.'" readonly/></div></div>';
            }
            echo $html;
        }
        wp_die();
    }