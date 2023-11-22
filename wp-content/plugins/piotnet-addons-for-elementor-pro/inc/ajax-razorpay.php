<?php
add_action( 'wp_ajax_pafe_razorpay_get_plan', 'pafe_razorpay_get_plan' );
add_action( 'wp_ajax_nopriv_pafe_razorpay_get_plan', 'pafe_razorpay_get_plan' );

function pafe_razorpay_get_plan(){
    $key = get_option( 'piotnet-addons-for-elementor-pro-razorpay-api-key' );
    $secret = get_option('piotnet-addons-for-elementor-pro-razorpay-secret-key');
    $plan_url = 'https://api.razorpay.com/v1/plans';
    $args = [
        'method' => 'GET',
        'headers' => [
            'content-type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode( $key . ':' . $secret ),
        ],
    ];
    $response = wp_remote_get( $plan_url, $args );
    if ( !is_wp_error( $response ) && !empty($response['body']) ) {
        $body = json_decode( $response['body'], true );
        $plans = isset($body['items']) ? $body['items'] : [];
        $plan_html = '';
        if(!empty($plans)){
            $plan_html .= '<div class="pafe-razorpay-plans">';
            foreach ($plans as $plan) {
                $plan_id = $plan['id'];
                $plan_name = $plan['item']['name'];
                $period = isset($plan['period']) ? '( ' . $plan['period'] . ' )' : '';
                $plan_html .= '<div style="display:flex;align-items: center; margin-bottom: 5px;"><label style="width:90%;">'.$plan_name.' '.$period.'</label><input type="text" value="'.$plan_id.'" readonly/></div>';
            }
            $plan_html .= '</div>';
        }
        echo $plan_html;
    } else {
        echo '<div style="color:red;">An error occurred, please check again.</div>';
    }
    wp_die();
}