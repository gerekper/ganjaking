<?php

	/* Add Remember me checkbox */
	add_action('userpro_before_form_submit', 'userpro_add_remember_me', 9);
	function userpro_add_remember_me($args){
		global $userpro;
		if ($args['template'] == 'login' && isset($args['rememberme']) && $args['rememberme'] == 'true' ) {
		
			?>
			
			<div class="userpro-column">
				<div class="userpro-field userpro-field-compact">
					<div class="userpro-input">
					
						<div class='userpro-checkbox-wrap'>
							<label class='userpro-checkbox hide-field'>
								<span></span>
								<input type='checkbox' name='rememberme-<?php echo $args['unique_id']; ?>' id='rememberme-<?php echo $args['unique_id']; ?>' value="true" /><?php _e('Remember me','userpro'); ?>
							</label>
						</div>
						
					</div>
				</div>
			</div><div class="userpro-clear"></div>
			
			<?php
		
		}
	}

	/* Hidden fields in forms */
	add_action('userpro_before_fields', 'userpro_form_role');
	function userpro_form_role($args){
		if (isset($args['form_role']) && $args['form_role'] != '') {
		?>
		<input type="hidden" name="form_role-<?php echo $args['unique_id']; ?>" id="form_role-<?php echo $args['unique_id']; ?>" value="<?php echo $args['form_role']; ?>" />
		<?php
		}
	}
	
	/* Apply rules to custom field values before they are display
		On user profiles 
		e.g. videos, links, etc.
	*/
	add_filter('userpro_before_value_is_displayed', 'userpro_before_value_is_displayed', 9999, 4);
	function userpro_before_value_is_displayed($value, $key, $array, $user_id){
		
		/* Images to lightbox */
		if ( $array['type'] == 'picture' ) {
			$source = userpro_profile_data($key, $user_id);
			$title = sprintf(__('%s\'s uploaded photo','userpro'), userpro_profile_data('display_name', $user_id));
			$caption = $array['label'];
			return '<a href="'.$source.'" class="lightview" data-lightview-title="'.$title.'" data-lightview-caption="'.$caption.'"><span></span>'.$value.'</a>';
		}
		
		/* Pre-value: Vimeo, YouTube */
		if (strpos($value, "vimeo.com") !== false || strpos($value, "youtube.com") !== false ) {
			if ( substr( $value, 0, 7 ) === "http://" || substr( $value, 0, 8 ) === "https://"  ) {
				global $wp_embed;
				$post_embed = $wp_embed->run_shortcode('[embed height="200"]'.$value.'[/embed]');
				return $post_embed;
			}
		}
		
		/* Pre-value: Description */
		if ($key == 'description'){
			$value = wpautop($value);
			return $value;
		}
		
		/* Pre-value Country */
		if ($key == 'country' && userpro_get_option('show_flag_in_profile') ) {
			$flag_name = str_replace(' ','-',$value);
			$flag_name = iconv("utf-8", "ascii//TRANSLIT//IGNORE", $flag_name);
            $value = __( $value, 'userpro' );
			$value = '<img src="'.userpro_url.'img/flags/'.strtolower($flag_name).'.png" alt="" title="'.$value.'" class="userpro-flag-normal" />'.$value;
			return $value;
		}
		
		/* Pre-value Links (URL vs Emails) */
		if(filter_var($value, FILTER_VALIDATE_URL)){
			$value = $value . '<a href="'.$value.'" target="_blank"><i class="userpro-icon-external-link userpro-meta-value"></i></a>';
			return $value;
		} elseif (is_email($value)) {
			$value = $value . '<a href="mailto:'.$value.'"><i class="userpro-icon-envelope userpro-meta-value"></i></a>';
			return $value;
		}
		
		return $value;
	}

	/* Maybe unverify display name changes for verified accounts */
	add_filter('userpro_field_filter','userpro_warn_verified_user', 9999, 2);
	function userpro_warn_verified_user($key, $user_id){
		global $userpro;
		$res = '';
		
		// add custom notice to display name
		if ($user_id > 0 && $key == 'display_name') {
			if (!userpro_is_admin($user_id) && userpro_get_option('unverify_on_namechange') && $userpro->get_verified_status($user_id) == 1  && !current_user_can('manage_options') ) {
				$res .= '<div class="userpro-notice">'.sprintf(__('<strong>Warning!</strong> Your account is %s verified. If you change your display name, <em>you will lose your verification status.</em>','userpro'), userpro_get_badge('verified')).'</div>';
			}
		}
		
		return $res;
	}
	
	/* action hooks before profile is updated */
	add_action('userpro_pre_profile_update', 'userpro_unverify_verified_account', 9999, 2);
	function userpro_unverify_verified_account($form, $user_id){
		global $userpro;
		
		// validate display name change
		if (!userpro_is_admin($user_id) && userpro_get_option('unverify_on_namechange') && $userpro->get_verified_status($user_id) == 1 && !current_user_can('manage_options') ) {
			if (isset($form['display_name'])){
				$old_displayname = userpro_profile_data('display_name', $user_id);
				$new_displayname = $form['display_name'];
				if ($new_displayname != $old_displayname){
					$userpro->unverify($user_id);
				}
			}	
		}	
	}
	
	/* filter hooks before profile is updated */
	add_filter('userpro_pre_profile_update_filters', 'userpro_prevent_duplicate_display_names', 9999, 2);
	function userpro_prevent_duplicate_display_names($form, $user_id){
		global $userpro;
		
		// validate display name
		if (isset($form['display_name'])){
			$form['display_name'] = $userpro->remove_denied_chars($form['display_name'], 'display_name');
			if ($userpro->display_name_exists( $form['display_name'] )){
				$user = get_userdata($user_id);
				$form['display_name'] = $user->user_login;
			}
		}
		
		return $form;
	}