<?php

	/* Enqueue Scripts */
	add_action('wp_enqueue_scripts', 'userpro_sc_enqueue_scripts', 99);
	function userpro_sc_enqueue_scripts(){
	
		wp_register_script('userpro_sc', userpro_sc_url . 'scripts/userpro-social.js');

        wp_localize_script( 'userpro_sc', 'up_social_ajax ', array(
            'ajaxurl'    => admin_url( 'admin-ajax.php' ),
            'security' => wp_create_nonce( 'userpro_followAction' ),
            'data'       => array(
               'is_user_logged_in' => is_user_logged_in(),
            )));
		wp_enqueue_script('userpro_sc');
		wp_enqueue_script('userpro_encrypt_js', userpro_url . 'scripts/userpro.encrypt.js', '' , '' ,FALSE);

	}
	
	/* Hook after name in user list compact */
	add_action('userpro_after_name_user_list', 'userpro_sc_show_follow', 99);
	function userpro_sc_show_follow($user_id){
		global $userpro, $userpro_social;
		
		if (!userpro_get_option('modstate_social') ) return false;

		if ( userpro_is_logged_in() && !$userpro->is_user_logged_user($user_id) ) {
			echo '<div class="userpro-sc-flw">'.$userpro_social->follow_text($user_id).'</div>';
		}
	
	}
        add_action('init', 'userpro_clear_members_cache', 99);
        
        function userpro_clear_members_cache(){
            global $userpro;
            $up_time = userpro_get_option('up_delete_cache_interval');
            if( !empty($up_time) ){
                $up_time_sec = $up_time*24*60*60;
                if(false === get_transient( 'members_cache_delete' ) ){
                    $userpro->clear_cache();
                    set_transient('members_cache_delete', 'members_cache_delete', $up_time_sec);
                }
            }
        }
