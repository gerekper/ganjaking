<?php
	/* allow dashboard redirect */
	function userpro_allow_dashboard_redirect(){
		global $userpro;
		
		if ( userpro_get_option('allow_dashboard_for_these_roles') && userpro_is_logged_in() && $userpro->user_role_in_array( get_current_user_id(), explode(',',userpro_get_option('allow_dashboard_for_these_roles') ) ) )
			return false;
			
		if (!current_user_can('manage_options') && userpro_get_option('dashboard_redirect_users') )
			return true;
		return false;
	}
	
	/* allow profile redirect */
	function userpro_allow_profile_redirect(){
		global $userpro;
		
		if ( userpro_get_option('allow_dashboard_for_these_roles') && userpro_is_logged_in() && $userpro->user_role_in_array( get_current_user_id(), explode(',',userpro_get_option('allow_dashboard_for_these_roles') ) ) )
			return false;
			
		if (!current_user_can('manage_options') && userpro_get_option('profile_redirect_users') )
			return true;
		return false;
	}
	
	/* allow login redirect */
	function userpro_allow_login_redirect(){
		if ( userpro_get_option('login_redirect_users') )
			return true;
		return false;
	}
	
	/* allow register redirect */
	function userpro_allow_register_redirect(){
		if ( userpro_get_option('register_redirect_users') )
			return true;
		return false;
	}
	
	/* dashboard redirect url */
	function userpro_dashboard_redirect_uri(){
		global $userpro;
		$possible = userpro_get_option('dashboard_redirect_users');
		if ($possible == 1)
			return $userpro->permalink();
		if ($possible == 2)
			return userpro_get_option('dashboard_redirect_users_url');
	}
	
	/* profile redirect url */
	function userpro_profile_redirect_uri(){
		global $userpro;
		$possible = userpro_get_option('profile_redirect_users');
		if ($possible == 1)
			return $userpro->permalink(0, 'edit');
		if ($possible == 2)
			return userpro_get_option('profile_redirect_users_url');
	}
	
	/* login redirect url */
	function userpro_login_redirect_uri(){
		global $userpro;
		$possible = userpro_get_option('login_redirect_users');
		if ($possible == 1){
			$pages = get_option('userpro_pages');
			if (!$userpro->page_exists($pages['login'])){
				userpro_set_option('login_redirect_users', 0);
				return admin_url();
			} else {
				return $userpro->permalink(0, 'login');
			}
		}
		if ($possible == 2)
			return userpro_get_option('login_redirect_users_url');
	}
	
	/* register redirect url */
	function userpro_register_redirect_uri(){
		
		global $userpro;
		$possible = userpro_get_option('register_redirect_users');
		if ($possible == 1)
			return $userpro->permalink(0, 'register');
		if ($possible == 2)
				return userpro_get_option('register_redirect_users_url');
		
	}

	/* runs link thru any special filter */
	function userpro_link_filter($value, $key) {
		// auto email
		if (is_email($value)) {
			return 'mailto:'.$value;
		}
		
		// auto twitter
		if ($key == 'twitter' && !strstr($value, 'http') ) {
			$v = 'http://';
			if (!strstr($value, 'twitter')){
				$v .= 'twitter.com/';
			}
			$v .= $value;
			return $v;
		}
		
		// auto facebook
		if ($key == 'facebook' && !strstr($value, 'http') ) {
			$v = 'http://';
			if (!strstr($value, 'facebook')){
				$v .= 'facebook.com/';
			}
			$v .= $value;
			return $v;
		}
		
		// auto instagram
		if ($key == 'instagram' && !strstr($value, 'http') ) {
			$v = 'http://';
			if (!strstr($value, 'instagram')){ 
				$v .= 'instagram.com/'; 
			} 
			$v .= $value;
			return $v; 
		}
		
		// auto google+
		if ($key == 'google_plus' && !strstr($value, 'http') ) {
			$v = 'http://';
			if (!strstr($value, 'plus.google.com')){
				$v .= 'plus.google.com/';
			}
			$v .= $value;
			return $v;
		}
		
		// auto phone number
		if ($key == 'phone_number'){
			return 'tel:'.$value;
		}
		
		return $value;
	}


	function up_get_template_part($part , $dir = null, $vars = null){

	    $path = '';
        if($dir){
            $path = userpro_path . $dir . '/' . $part . '.php';
        }else{
            $path = userpro_path . 'profile-layouts/'.$part.'.php';
        }

        /**
         * Include vars to template.
         */
        if(isset($vars)){
            extract($vars);
        }
        include_once $path;
    }
    function up_set_user_id($user_id)
    {
        unset($GLOBALS['up_user']);
        $GLOBALS['up_user'] = new UP_User($user_id);
    }
