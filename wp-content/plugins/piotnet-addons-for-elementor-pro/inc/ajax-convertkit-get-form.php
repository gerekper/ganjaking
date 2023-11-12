<?php
add_action( 'wp_ajax_pafe_convertkit_get_form', 'pafe_convertkit_get_form' );
add_action( 'wp_ajax_nopriv_pafe_convertkit_get_form', 'pafe_convertkit_get_form' );

function pafe_convertkit_get_form(){
    $api_key = $_REQUEST['api_key'];
    if($api_key == 'false'){
        $api_key = get_option('piotnet-addons-for-elementor-pro-convertkit-api-key');
    }
    if($api_key != false){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.convertkit.com/v3/forms?api_key='.$api_key,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $forms = json_decode($response)->forms;
        foreach($forms as $index => $item){
            echo '<div class="pafe-convertkit-form-item"><label>'.$item->name.'</label><div><input type="text" value="'.$item->id.'" readonly></div></div>';
        }

    }else{
        echo "Please enter the API key.";
    }
    wp_die();
}