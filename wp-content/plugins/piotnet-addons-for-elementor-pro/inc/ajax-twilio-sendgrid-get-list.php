<?php
add_action( 'wp_ajax_pafe_twilio_sendgrid_get_list', 'pafe_twilio_sendgrid_get_list' );
add_action( 'wp_ajax_nopriv_pafe_twilio_sendgrid_get_list', 'pafe_twilio_sendgrid_get_list' );

function pafe_twilio_sendgrid_get_list(){
	$api_key = $_REQUEST['api'];
	if (!empty($api_key)) {
		$api_key = 'authorization: Bearer ' . $api_key;
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api.sendgrid.com/v3/marketing/lists?page_size=100',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_POSTFIELDS =>'{}',
			CURLOPT_SSL_VERIFYPEER =>false,
			CURLOPT_HTTPHEADER => array(
				$api_key,
				'content-type: application/json'
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);

		$response = json_decode($response);
		$result = $response->result;
		foreach ($result as $value) {
			$name = $value->name;
			$id = $value->id;
			echo '<div class="pafe-twilio-sendgrid-list__item" style="padding-top:5px;"><label> <strong>'.$name.'</strong> ('.$value->contact_count.')</label><div class="pafe-twilio-sendgrid-list__item-value" style="padding-bottom:3px;"><input type="text" value="'.$id.'" readonly></div></div>';
		}
		wp_die();
	} else {
		echo "Please enter the API key.";
		wp_die();
	}
}
