<div class="updb-widget-style">
<div class="updb-view-activity">
	<div class="updb-basic-info">
		<?php
		global $userpro;
		if ($userpro->is_user_logged_user($user_id)) { ?>
			<?php _e('Your Activities','userpro-dashboard'); ?>
			<?php } else { ?>
			<?php _e(userpro_profile_data('display_name', $user_id)."'s Activities",'userpro-dashboard'); ?>
			<?php } ?>	
	</div>
	<?php
		global $userpro, $userpro_social;
		$args['activity_user'] = $user_id;
		$args['activity_all'] = 1;
		$args['i'] = $i;
		$template = 'activity';
		$args['activity_per_page'] = userpro_sc_get_option( 'activity_per_page' );
		if (userpro_sc_get_option('activity_open_to_all') == 1) {
					$activity = $userpro_social->activity(0, 0, $args['activity_per_page'], $args['activity_user'] );
					if (locate_template('userpro/' . $template . '.php') != '') {
						include get_stylesheet_directory() . '/userpro/'. $template . '.php';
					} else {
						include userpro_sc_path . "templates/$template.php";
					}
				} else {
					
					if (userpro_is_logged_in()){
						$activity = $userpro_social->activity(0, 0, $args['activity_per_page'], $args['activity_user'] );
						if (locate_template('userpro/' . $template . '.php') != '') {
							include get_stylesheet_directory() . '/userpro/'. $template . '.php';
						} else {
							include userpro_sc_path . "templates/$template.php";
						}
					} else {
					
						/* attempt to view profile so force redirect to same page */
						$args['force_redirect_uri'] = 1;
						$template = 'login';$args['template'] = 'login';
						if (locate_template('userpro/' . $template . '.php') != '') { 
							include get_stylesheet_directory() . '/userpro/'. $template . '.php';
						} else {
							include userpro_path . "templates/login.php";
						}
						
					}
					
				}
		//echo do_shortcode("[userpro template=activity activity_all=1]");
	?>
</div>
</div>
