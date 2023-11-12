<?php

class PAFE_Helper{
	protected $B = 0;
	protected $I = 0;
	protected $U = 0;
	protected $HREF = '';

	public function mailchimp_curl_get_connect( $url, $request_type, $api_key, $data = array() ) {
		if( $request_type == 'GET' )
			$url .= '?' . http_build_query($data);
	 
		$mch = curl_init();
		$headers = array(
			'Content-Type: application/json',
			'Authorization: Basic '.base64_encode( 'user:'. $api_key )
		);
		curl_setopt($mch, CURLOPT_URL, $url );
		curl_setopt($mch, CURLOPT_HTTPHEADER, $headers);
		//curl_setopt($mch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
		curl_setopt($mch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($mch, CURLOPT_CUSTOMREQUEST, $request_type);
		curl_setopt($mch, CURLOPT_TIMEOUT, 10);
		curl_setopt($mch, CURLOPT_SSL_VERIFYPEER, false);
		if( $request_type != 'GET' ) {
			curl_setopt($mch, CURLOPT_POST, true);
			curl_setopt($mch, CURLOPT_POSTFIELDS, json_encode($data) );
		}
		return curl_exec($mch);
	}
	public function mailchimp_curl_put_member($url, $api_key, $data){
		$ch = curl_init($url);
		$header = array(
			'Content-Type: application/json',
			'Authorization: Basic '.base64_encode( 'user:'. $api_key )
		);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	
		$result   = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return $httpCode;
	}
	public function zohocrm_post_record($data, $url, $token){
		$data = json_encode($data);
		$data = '{"data":['.$data.']}';
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => $data,
		CURLOPT_HTTPHEADER => array(
				"Authorization: Zoho-oauthtoken ".$token."",
				"Content-Type: application/x-www-form-urlencoded",
			),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		return $response;
	}
	public function zohocrm_get_record($url, $token){
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"Authorization: Zoho-oauthtoken ".$token."",
				"Content-Type: application/x-www-form-urlencoded",
			),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		return $response;
	}
	public function zoho_refresh_token(){
		$zoho_client_id = get_option('piotnet-addons-for-elementor-pro-zoho-client-id');
		$zoho_client_secret = get_option('piotnet-addons-for-elementor-pro-zoho-client-secret');
		$zoho_domain_refresh_token = get_option('piotnet-addons-for-elementor-pro-zoho-domain');
		$refresh_token = get_option('zoho_refresh_token');
	
		$url_refresh_token = 'https://'.$zoho_domain_refresh_token.'/oauth/v2/token?refresh_token='.$refresh_token.'&client_id='.$zoho_client_id.'&client_secret='.$zoho_client_secret.'&grant_type=refresh_token';       
		$zoho_access_token = wp_remote_post($url_refresh_token);
		if(empty($zoho_access_token->error)){
			$zoho_access_token = json_decode($zoho_access_token['body']);
			update_option('zoho_access_token', $zoho_access_token->access_token);
			update_option('zoho_api_domain', $zoho_access_token->api_domain);
		}else{
			echo $zoho_access_token->error;
		}
	}
	public function activecampaign_add_contact($api, $data){
		$activecampaign_request = curl_init($api);
		curl_setopt($activecampaign_request, CURLOPT_HEADER, 0);
		curl_setopt($activecampaign_request, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($activecampaign_request, CURLOPT_POSTFIELDS, $data);
		curl_setopt($activecampaign_request, CURLOPT_FOLLOWLOCATION, true);

		$activecampaign_response = (string)curl_exec($activecampaign_request);

		curl_close($activecampaign_request);
		return unserialize($activecampaign_response);
	}
	public function activecampaign_edit_contact($api, $data){
		$request = curl_init($api);
		curl_setopt($request, CURLOPT_HEADER, 0);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($request, CURLOPT_POSTFIELDS, $data);
		curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);
		$response = (string)curl_exec($request);
		curl_close($request);
		return unserialize($response);
	}
	public function sendinblue_get_list($api_key){
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://api.brevo.com/v3/contacts/lists?limit=50&offset=0&sort=desc',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'GET',
		CURLOPT_HTTPHEADER => array(
			'Accept: application/json',
			'api-key: '.$api_key.'',
		),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		return $response;
	}
	public function sendinblue_get_attribute($api_key){
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://api.brevo.com/v3/contacts/attributes',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'GET',
		CURLOPT_HTTPHEADER => array(
			'Accept: application/json',
			'api-key: '.$api_key.'',
		),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		return $response;
	}
	public function sendinblue_create_contact($api_key, $data){
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://api.brevo.com/v3/contacts',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS =>$data,
		CURLOPT_HTTPHEADER => array(
			'Accept: application/json',
			'Content-Type: application/json',
			'api-key: '.$api_key.'',
		),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		return $response;
	}
	public function pafe_convertkit_add_subscriber($data, $form_id){
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api.convertkit.com/v3/forms/'.$form_id.'/subscribe',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json; charset=utf-8',
			),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		return $response;
	}
	public function pafe_constant_contact_create_contact($token, $data){
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://api.cc.email/v3/contacts',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => $data,
		CURLOPT_HTTPHEADER => array(
			'accept: application/json',
			'authorization: Bearer '.$token,
			'cache-control: no-cache',
			'content-type: application/json'
		),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return $response;

	}
	public function pafe_constant_contact_refresh_token($key, $secret, $refresh_token){
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://authz.constantcontact.com/oauth2/default/v1/token?refresh_token='.$refresh_token.'&grant_type=refresh_token',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/x-www-form-urlencoded',
			'Authorization: Basic '. base64_encode($key.':'.$secret),
		),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		$response = json_decode($response);
		if(!empty($response)){
			update_option('piotnet-constant-contact-access-token', $response->access_token);
			update_option('piotnet-constant-contact-refresh-token', $response->refresh_token);
			update_option('piotnet-constant-contact-time-get-token', time());
		}
		return $response->access_token ? $response->access_token : false;
	}

    public function pafe_paypal_get_token($client_id, $client_secret, $paypal_url){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $paypal_url.'v1/oauth2/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Accept-Language: en_US',
                'Authorization: Basic '.base64_encode($client_id . ':' . $client_secret),
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));
        $response = json_decode(curl_exec($curl))->access_token;
        curl_close($curl);
        return $response;
    }
	public function pafe_twilio_whatsapp_send_msg($data, $url, $header)
    {
        $whatsapp_body = http_build_query($data);

        $whatsapp_curl = curl_init();

        curl_setopt_array($whatsapp_curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $whatsapp_body,
            CURLOPT_HTTPHEADER => array(
                "Authorization:  Basic " . $header . "",
                "Content-Type: application/x-www-form-urlencoded",
            ),
        )
        );
        $whatsapp_response = curl_exec($whatsapp_curl);
        curl_close($whatsapp_curl);
        return $whatsapp_response;
    }

}
