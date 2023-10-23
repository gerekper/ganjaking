<?php

	/* Custom login redirects */
	add_filter('userpro_login_redirect', 'userpro_rd_custom_login_redirection', 10);
	function userpro_rd_custom_login_redirection($arg){
		global $userpro_redirection;
		$user_id = get_current_user_id();
		$user = get_userdata($user_id);
		//added for userpro as per global rule redirection.-Niranjan
		$rules = get_option('userpro_redirects_login');
		if (is_array($rules) ) {
			$rules = array_reverse($rules);
			foreach($rules as $k => $rule){
				/* Check user */
				if ($rule['user'] != '' || $rule['user'] == 'all'){ 
					if ($user_id == $rule['user'] || $rule['user'] == 'all') { 
						$arg = $userpro_redirection->map_url( $rule['url'], $user );
						return $arg;
					}
				}
				
				/* Check user */
				if ($rule['field'] != '' && userpro_profile_data($rule['field'], $user_id) != '' ){ 
					$arg = $userpro_redirection->map_url( $rule['url'], $user );
					return $arg;
				}
			
				/* Check role */
				if ($rule['role'] != ''){
					$user_roles = $user->roles;
					if(is_array($user_roles)){
					$user_role = array_shift($user_roles);
					if ($user_role == $rule['role']) {
						$arg = $userpro_redirection->map_url( $rule['url'], $user );
						return $arg;
					}
				  }
				}
		
			}
		}
		
		return $arg;

	}
	
	/* Custom register redirects */
	add_filter('userpro_register_redirect', 'userpro_rd_custom_register_redirection', 10);
	function userpro_rd_custom_register_redirection($arg){
		$user_id = get_current_user_id();
		$user = get_userdata($user_id);
		global $userpro_redirection;
		$rules = get_option('userpro_redirects_register');
		if (is_array($rules) ) {
			$rules = array_reverse($rules);
			foreach($rules as $k => $rule){
			
				/* Check user */
				if ($rule['user'] != '' || $rule['user'] == 'all'){
					if ($user_id == $rule['user'] || $rule['user'] == 'all') {
						$arg = $userpro_redirection->map_url( $rule['url'], $user );
						return $arg;
					}
				}
				
				/* Check user */
				if ($rule['field'] != ''){
					$test = userpro_profile_data( $rule['field'] , $user_id);
					if ($test == $rule['field_value'] ) {
						$arg = $userpro_redirection->map_url( $rule['url'], $user );
						return $arg;
					}
				}
			
				/* Check role */
				if ($rule['role'] != ''){
					$user_roles = $user->roles;
					$user_role = array_shift($user_roles);
					if ($user_role == $rule['role']) {
						$arg = $userpro_redirection->map_url( $rule['url'], $user );
						return $arg;
					}
				}
		
			}
		}
		
		return $arg;

	}
