<?php

/* Add custom styles */

add_action('wp_head','userpro_add_custom_styles', 99999);
function userpro_add_custom_styles() {
	if (userpro_get_option('userpro_css')) {
		print '<style type="text/css">'.userpro_get_option('userpro_css').'</style>';
	}
	if(is_rtl()){
		echo '<script type="text/javascript">';
		?>
			jQuery(function(){
				jQuery('select').attr('class' , jQuery('select').attr('class')+' chosen-rtl');
				jQuery('.chosen-container-single').attr('class' , 'chosen-container chosen-container-single chosen-rtl');
			});
		<?php
		echo '</script>';
			}
}

	/* Verify an Envato purchase */
	add_action('userpro_profile_update', 'userpro_verify_envato_purchase', 10, 2);
	function userpro_verify_envato_purchase($form, $user_id){
		global $userpro;
		if (isset($form['envato_purchase_code'])){
			$code = $form['envato_purchase_code'];
			if ($userpro->verify_purchase($code)) {
				$userpro->do_envato($user_id);
			} else {
				$userpro->undo_envato($user_id);
			}
		}
	}

	/* Enqueue Scripts */
	add_action('wp_enqueue_scripts', 'userpro_enqueue_scripts');
	function userpro_enqueue_scripts(){
		$connected = @fsockopen("www.google.com", 80);
		 if($connected){
		if ( userpro_get_option('googlefont') && !userpro_get_option('customfont') ) {
			if (is_ssl()){
				$fonts_url = 'https://fonts.googleapis.com/css?family='.userpro_get_option('googlefont').':400,400italic,700,700italic,300italic,300';
			} else {
				$fonts_url = 'http://fonts.googleapis.com/css?family='.userpro_get_option('googlefont').':400,400italic,700,700italic,300italic,300';
			}
			wp_register_style('userpro_google_font', $fonts_url);
			wp_enqueue_style('userpro_google_font');
		 }
		 }
		 else {
		 	$font = str_replace(' ','-',strtolower(userpro_get_option('googlefont')));
		 	$fonts_url = userpro_url .'css/google-fonts/'.$font.'.css';
		 	wp_register_style('custom_font',$fonts_url);
		 	wp_enqueue_style('custom_font');
		 }
		 global $post;
		 if( !empty( $post ) && !has_shortcode( $post->post_content,'userpro' ) ){
		 	if ( !empty( $post ) && strpos( $post->post_content,'[userpro' ) !== false ){
		 		global $userpro;
		 		$userpro->up_enqueue_scripts_styles();
		 	}
		 }
        $skin = userpro_get_option('skin');
        if (class_exists('userpro_sk_api') && is_dir( userpro_sk_path . 'skins/'.$skin ) ) {
            wp_register_style('userpro_skin_min', userpro_sk_url . 'skins/'.$skin.'/style.css');
            wp_enqueue_style('userpro_skin_min');
        } else {
            wp_register_style('userpro_skin_min', userpro_url . 'skins/'.$skin.'/style.css');
            wp_enqueue_style('userpro_skin_min');
        }
        if (locate_template('userpro/skins/'.$skin.'/style.css') ) {
            wp_register_style('userpro_skin_custom', get_stylesheet_directory_uri() . '/userpro/skins/'.$skin.'/style.css' );
            wp_enqueue_style('userpro_skin_custom');
        }
		/* wp_enqueue_script */
		 wp_enqueue_script('jquery');
		 wp_enqueue_script('jquery-ui-datepicker');
		 wp_enqueue_style('up_fontawesome',userpro_url.'css/up-fontawesome.css');
		 wp_enqueue_script('up-custom-script',userpro_url . 'scripts/up-custom-script.js','','',true);
		 wp_localize_script( 'up-custom-script', 'up_values', array('up_url'=>userpro_url));
		 add_filter( 'style_loader_src', 'up_remove_wp_ver_css_js', 9999 );
		 add_filter( 'script_loader_src','up_remove_wp_ver_css_js', 9999 );
	}

	function up_remove_wp_ver_css_js( $src ){
		 if ( strpos( $src, 'ver=' ) )
        	$src = remove_query_arg( 'ver', $src );
    	return $src;
	}
	/* Remove bar except for admins */
	add_action('init', 'userpro_remove_admin_bar');
	function userpro_remove_admin_bar() {
		global $userpro;
		if (!current_user_can('manage_options') && !is_admin()) {

			if (userpro_get_option('hide_admin_bar')) {

				if ( userpro_get_option('allow_dashboard_for_these_roles') && userpro_is_logged_in() && $userpro->user_role_in_array( get_current_user_id(), explode(',',userpro_get_option('allow_dashboard_for_these_roles') ) ) ) {
                    show_admin_bar(true);
				} else {

                    show_admin_bar(false);

				}
			}
		}
	}

	/* Hook into WP normal login if panic key is used */
	add_action('login_form','userpro_panic_key');
	function userpro_panic_key(){
		if ( isset($_REQUEST['userpro_panic_key']) && userpro_get_option('userpro_panic_key') && $_REQUEST['userpro_panic_key'] == userpro_get_option('userpro_panic_key') ) {
	?>
		<input type="hidden" value="<?php echo userpro_get_option('userpro_panic_key'); ?>" id="userpro_panic_key" name="userpro_panic_key"></label>
	<?php
		}
	}


add_action('user_register', 'add_usermeta_userpro', 10, 1 );

function add_usermeta_userpro( $user_id ) {
	$user_info = get_userdata($user_id);
	update_user_meta($user_id,"display_name",$user_info->display_name );
	
	$timestamp = current_time('timestamp');
	$meta_value = get_user_meta( $user_id, 'up-timeline-actions', true );
	$timeline_actions = empty($meta_value)?array():$meta_value;
	$timeline_actions[] = array( 'action'=>'registered', 'timestamp'=>$timestamp );
	update_user_meta( $user_id, 'up-timeline-actions', $timeline_actions );

}





	/* Setup redirections */
	add_action('init','userpro_redirects');
	function userpro_redirects(){
		global $pagenow;

		// redirect dashboard
		if ('index.php' == $pagenow && is_admin()) {
			if (userpro_is_logged_in() && userpro_allow_dashboard_redirect() ){
				wp_safe_redirect( userpro_dashboard_redirect_uri() );
				exit();
			}
		}

		// redirect dashboard profile
		if( 'profile.php' == $pagenow ) {
			if (userpro_is_logged_in() && userpro_allow_profile_redirect() ){
				wp_safe_redirect( userpro_profile_redirect_uri() );
				exit();
			}
		}

		// redirect login
		if ('wp-login.php' == $pagenow && !isset($_REQUEST['action']) ) {
			if ( !userpro_is_logged_in() && isset($_REQUEST['userpro_panic_key']) && userpro_get_option('userpro_panic_key') && $_REQUEST['userpro_panic_key'] == userpro_get_option('userpro_panic_key') ) {
				return true;
			}
			if (userpro_allow_login_redirect() ){
				if (isset($_GET['redirect_to'])){
					$url = add_query_arg('redirect_to', urlencode( esc_url($_GET['redirect_to']) ), esc_url(userpro_login_redirect_uri()) );
				} else {
					$url = userpro_login_redirect_uri();
				}
				wp_safe_redirect($url);
				exit();
			}
		}

		// redirect lostpassword
		if ('wp-login.php' == $pagenow && isset($_REQUEST['action']) && $_REQUEST['action'] == 'lostpassword') {
			if (userpro_allow_login_redirect() ){
				wp_safe_redirect( userpro_login_redirect_uri() );
				exit();
			}
		}

		// redirect register
		if ('wp-login.php' == $pagenow && isset($_REQUEST['action']) && $_REQUEST['action'] == 'register') {
			if (userpro_allow_register_redirect() ){
				wp_safe_redirect( userpro_register_redirect_uri() );
				exit();
			}
		}

	}

	/**
	Clear cache on some actions
	**/

	add_action ('userpro_after_account_verified', "userpro_cache_clear");
	add_action ('userpro_after_account_unverified', "userpro_cache_clear");
	add_action('userpro_after_profile_updated_fb', 'userpro_cache_clear');
	add_action('userpro_after_profile_updated','userpro_cache_clear');
	add_action ('user_register', "userpro_cache_clear");
	add_action ('delete_user', "userpro_cache_clear");
	function userpro_cache_clear(){
		global $userpro;
		$userpro->clear_cache();
	}

	add_action('userpro_after_new_registration', "userpro_cache_clear_frontend");
	function userpro_cache_clear_frontend($user_id){
		global $userpro;
		$userpro->clear_cache();
	}

	add_action( 'profile_update', 'userpro_profile_updated', 10, 2 );
	function userpro_profile_updated( $user_id, $old_user_data ) {
		global $userpro;
		$current_user=wp_get_current_user();
		if ( !empty($user_id) ) {

			$current_user_data = WP_User::get_data_by( 'id', $user_id );
			$display_name = $current_user_data->display_name;
			update_user_meta($user_id, 'display_name', $display_name);
		}
		$userpro->clear_cache();
	}

	add_action('edit_user_profile_update', 'userpro_edit_user_profile_update');
	function userpro_edit_user_profile_update($user_id) {
		global $userpro;
		$userpro->clear_cache();
	}

	add_action('personal_options_update', 'userpro_personal_options_update');
	function userpro_personal_options_update($user_id) {
		global $userpro;
		$userpro->clear_cache();
	}

function userpro_replace_profile_title($title , $id=null) {
 	global $current_user;
	$current_user= wp_get_current_user();

 if($id != null) {

		$post = get_post($id);
		$other_username = '';
		$other_username = get_query_var('up_username');


	 	if($post->post_name == userpro_get_option('slug') && !empty($other_username) ) {
			if($other_username != ''){
				//$title = $other_username.__("'s Profile",'userpro');
				$title = sprintf(__("%s's Profile",'userpro'), $other_username);
			}
			else {
	 			//$title = $current_user->display_name.__("'s Profile",'userpro');
				$title = sprintf(__("%s's Profile",'userpro'), $current_user->display_name);
	 		}
	 	}
	 }
 	return $title;
 }

function userpro_replace_wp_title($title , $sep = ' | ') {
	global $post;
	global $current_user;
	$current_user=wp_get_current_user();
	if(is_user_logged_in()) {

	//$post = get_post($id);
	$other_username = '';
	$other_username = get_query_var('up_username');
	if(isset($post) && $post->post_name == userpro_get_option('slug') && !is_admin()) {
		global $wp_filter;
		if($other_username != ''){
			//$title = $other_username.__("'s Profile",'userpro');
			$title = sprintf(__("%s's Profile",'userpro'), $other_username);
			$site_description = get_bloginfo( 'description', 'display' );
			$title .= "$sep $site_description";
		}else {
			//$title = $current_user->display_name.__("'s Profile",'userpro');
			$title = sprintf(__("%s's Profile",'userpro'), $current_user->display_name);
			$site_description = get_bloginfo( 'description', 'display' );
			$title .= "$sep $site_description";
		}
	}
	}
	return $title;
}

function userpro_avoid_conflict(){
	add_action('pre_get_document_title' , 'userpro_replace_wp_title' ,999 );
}
add_action('init' ,  'userpro_avoid_conflict');

function userpro_replace_profile_name() {
	if(is_user_logged_in())
	add_action('the_title' , 'userpro_replace_profile_title' , 10 ,2);
}
	add_action('the_post' , 'userpro_replace_profile_name');
 /**
 * My Profile Is Changed To UserName END
 */
	add_action('wp_logout' , 'userpro_remove_online_badge');
	function userpro_remove_online_badge() {
		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;
		$online = get_transient('userpro_users_online');
		if (isset($online) && is_array($online) && isset($online[$user_id]) ){
			unset($online[$user_id]);
			set_transient('userpro_users_online', $online , (30*60));
		}
	}
