<?php

    $path = preg_replace('/wp-content(?!.*wp-content).*/','',__DIR__);
   
    include($path.'wp-load.php');
	
	$redirect_url = @get_bloginfo('home')."/wp-admin/admin.php?page=wc-settings&tab=integration";
	
	if(isset($_REQUEST['code']) && !empty($_REQUEST['code'])){
		    $help_scout_settings = get_option('woocommerce_help-scout_settings');

		//echo "<b>Code updated:-</b>";
		//echo "</br>----------------------------------------</br>";
		//echo $_REQUEST['code'];
		//echo "</br>----------------------------------------</br>";
		update_option('wc_helpscout_code',$_REQUEST['code']);
		
		// define params for constants to use for api post
		define( 'wc_helpscout_client_app_key', $help_scout_settings['app_key'] );
		define( 'wc_helpscout_client_api_secret', $help_scout_settings['app_secret'] );
		define( 'wc_helpscout_access_code', get_option('wc_helpscout_code') );
		define( 'wc_helpscout_token_uri','https://api.helpscout.net/v2/oauth2/token' );
	   
		if(get_option('wc_helpscout_code')){			
			
			// build up the params for generating token 
			$params = array(
				'client_id'				=>	wc_helpscout_client_app_key,
				'client_secret'			=>	wc_helpscout_client_api_secret,
				'grant_type'			=>	'authorization_code',
				'code'					=>	wc_helpscout_access_code,
			);
			 
			$params = http_build_query($params);
			
			// generate token post through api
			$response  = wp_remote_post( wc_helpscout_token_uri , array(
				'body' => $params                
			)); 
			
			if($response['response']['code']=="200"){				
				$tokenData = json_decode($response['body']);
				
				// update token related data in option table
				update_option('helpscout_access_token',$tokenData->access_token);
				update_option('helpscout_access_refresh_token',$tokenData->refresh_token);
				update_option('helpscout_access_token_type',$tokenData->token_type);
				update_option('helpscout_expires_in',$tokenData->expires_in);	?>
				<script>
					window.location = "<?php echo $redirect_url;?>"; 
				</script>	<?php	
			}else{
				echo "</br>----------------------------------------</br>";
				echo "<b>Invalid URL for HelpScout authorization.</b>";
				echo "</br>----------------------------------------</br>";
			}			
		} 
	}else{
		
		// define params for constants to use for api post
		define( 'wc_helpscout_client_app_key', $help_scout_settings['app_key'] );
		define( 'wc_helpscout_client_api_secret', $help_scout_settings['app_secret'] );
		define( 'wc_helpscout_access_code', get_option('wc_helpscout_code') );
		define( 'wc_helpscout_token_uri','https://api.helpscout.net/v2/oauth2/token' );
	   
		if(get_option('helpscout_access_refresh_token')){			
			
			// build up the params for regenerating token 
			$params = array(
				'client_id'				=>	wc_helpscout_client_api_id,
				'client_secret'			=>	wc_helpscout_client_api_secret,
				'grant_type'			=>	'refresh_token',
				'refresh_token'			=>	wc_helpscout_access_token,
			); 
			$params = http_build_query($params);
				
			// generate token post through api
			$response = wp_remote_post( wc_helpscout_token_uri , array(
				'body' => $params                
			)); 
					
			if($response['response']['code']=="200"){				
				$tokenData = json_decode($response['body']);
				
				// update token related data in option table
				update_option('helpscout_access_token',$tokenData->access_token);
				update_option('helpscout_access_refresh_token',$tokenData->refresh_token);
				update_option('helpscout_access_token_type',$tokenData->token_type);
				update_option('helpscout_expires_in',$tokenData->expires_in); ?>

				<script>
					window.location = "<?php echo $redirect_url;?>"; 
				</script>

			<?php }else{
				echo "</br>----------------------------------------</br>";
				echo "<b>Invalid URL for HelpScout authorization.</b>";
				echo "</br>----------------------------------------</br>";
			}
		}
	}

?>



