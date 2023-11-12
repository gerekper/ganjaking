<?php
    add_action( 'wp_ajax_pafe_campaign_fields', 'pafe_campaign_fields' );
	add_action( 'wp_ajax_nopriv_pafe_campaign_fields', 'pafe_campaign_fields' );

	function pafe_campaign_fields() {
		$url = $_POST['campaign_url'];
        $campaign_key = $_POST['campaign_key'];
        $list_id = $_POST['list_id'];
        if($url == 'false' && $campaign_key == 'false'){
            $url = get_option('piotnet-addons-for-elementor-pro-activecampaign-api-url');
            $campaign_key = get_option('piotnet-addons-for-elementor-pro-activecampaign-api-key');
        }
        $params = array(
            'api_key'      =>  $campaign_key,
            'api_action'   => 'list_field_view',
            'api_output'   => 'serialize',
            'ids'           => 'all',
        );

        $query = "";
        foreach( $params as $key => $value ) $query .= urlencode($key) . '=' . urlencode($value) . '&';
        $query = rtrim($query, '& ');

        $url = rtrim($url, '/ ');

        if ( !function_exists('curl_init') ) die('CURL not supported. (introduced in PHP 4.0.2)');

        if ( $params['api_output'] == 'json' && !function_exists('json_decode') ) {
            die('JSON not supported. (introduced in PHP 5.2.0)');
        }

        $api = $url . '/admin/api.php?' . $query;

        $request = curl_init($api);
        curl_setopt($request, CURLOPT_HEADER, 0);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);

        $response = (string)curl_exec($request); 

        curl_close($request);

        if ( !$response ) {
            die('Nothing was returned. Do you have a connection to Email Marketing server?');
        }
        $result = unserialize($response);
                foreach($result as $key => $value){
            if(!empty($value['tag']) && $value['tag'] != 's' && $value['tag'] != 'S'){
                if($value['type'] == 'checkbox' || $value['type'] == 'listbox '){
                    echo '<input type="text" value="'.$value['tag'].'@multiple" readonly/><br><br>';
                }else{
                    echo '<input type="text" value="'.$value['tag'].'" readonly/><br><br>';
                }
            }
        }
	wp_die(); 
}