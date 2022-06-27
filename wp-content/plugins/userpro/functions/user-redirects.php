<?php


function userpro_user_redirect($username , $redirect = null, $action = null){


	#if action isset [ login ]
	if(isset($action)){
		try{
			$url = userpro_default_form_redirects($action, $redirect);

			return $url;
		}
		catch(Exception $e){

			up_error('Redirection error :', $e->getMessage());

		}



	}
}

function userpro_default_form_redirects($action, $redirect = null){

	global $userpro;

	if($action == 'login'){
//			Do redirection actions for login.

						if (!empty($redirect['force_redirect_uri'])) {
							$output['redirect_uri'] = 'refresh';

						} else {

							if (current_user_can('manage_options') && userpro_get_option('show_admin_after_login')) {
								$output['redirect_uri'] = admin_url();
							} else {

								if (!empty($redirect['redirect_uri'])) {
									$output['redirect_uri'] = esc_url($redirect['redirect_uri']);

								} else {
									if (userpro_get_option('after_login') == 'no_redirect') {
										$output['redirect_uri'] = 'refresh';
									}
									if (userpro_get_option('after_login') == 'profile') {
										$output['redirect_uri'] = $userpro->permalink();
									}
								}
							}
//							/* hook the redirect URI */
							$output['redirect_uri'] = apply_filters('userpro_login_redirect', $output['redirect_uri']);
						}
						/* super redirection */
						if (!empty($redirect['global_redirect'])) {
							$output['redirect_uri'] = wp_validate_redirect($redirect['global_redirect']);
						}
			return $output['redirect_uri'];

	}elseif($action == 'social'){
		#Redirection for social logins
		userpro_social_logins_redirect();

	}
	else{
		throw new Exception('Please check userpro_default_form_redirects function.');
	}
}

//Redirects for social logins
function userpro_social_logins_redirect(){
	global $userpro;

	$url = '';

	$url = apply_filters('userpro_login_redirect', $url);

	if(empty($url))
	$url = $userpro->permalink();

	wp_safe_redirect( $url );

	exit;

}