<?php global $userpro, $userpro_badges; ?>

<form action="" method="post">

<h3><?php _e('Edit/Delete User Badges','userpro'); ?></h3>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="badge_user"><?php _e('Select a user','userpro'); ?></label></th>
		<td>
			<select name="badge_user" id="badge_user" class="chosen-select" style="width:300px" data-placeholder="">
				<option value=""><?php _e('Select a user...','userpro'); ?></option>
				<?php
				$users=userpro_badges_admin_users(true);
				foreach($users as $user) {
				?>
				<option value="<?php echo $user->ID; ?>" <?php userpro_admin_post_value('badge_user', $user->ID, $_POST); ?>><?php echo userpro_profile_data('display_name', $user->ID); if ($user->user_email) echo ' ('. $user->user_email . ')'; ?></option>
				<?php } ?>
			</select>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="remove_badge_from_all_users"><?php _e('Select a badge to remove from all current users','userpro'); ?></label></th>
		<td>
			<select name="remove_badge_from_all_users" id="remove_badge_from_all_users" class="chosen-select" style="width:300px" data-placeholder="">
				<option value=""><?php _e('Select a badge...','userpro'); ?></option>
				<?php
				global $userpro;
				$created_badges = get_option('_userpro_badges');
				$default_badge = get_option( 'userpro_defaultbadge' );
				if(!empty($default_badge) && empty($achievement)){
					$created_badges = array('defaultbadge'=>array(get_option( 'userpro_defaultbadge' )));
				}
				else if(!empty($default_badge)){
					$created_badges = array_merge($created_badges,array('defaultbadge'=>array(get_option( 'userpro_defaultbadge' ))));
				}
				if(is_array($created_badges)){
					$created_badges_array_keys = array_keys($created_badges);
					$created_badges_array_keys_count = count($created_badges_array_keys);
					for($i=0;$i<$created_badges_array_keys_count;$i++){
						$created_badge = $created_badges[$created_badges_array_keys[$i]];
						$created_badge_count = count($created_badge);
						$j=0;
					    foreach($created_badge as $badges) {
							$keys = array_keys($created_badge);
					?>
					<option value="<?php echo $badges['badge_url'];?>"><?php echo ucfirst($badges['badge_title'])?></option>
					
					<?php } 
					}
				}
				?>
				
			</select>
		</td>
	</tr>
	<tr valign="top">
	<td>
	<input type="button" value="Remove badge from all users" class="button button-primary" id="delete_badge" name="delete_badge" style="display:none;"/>
	</td>
	<td>
	<div id="userpro-delete-badge-loading" style="display:none">
		<img src="<?php echo $userpro->skin_url(); ?>loading.gif" alt="" class="userpro-loading" style="display:block !important"/>
	</div>
	</td>
	</tr>
	</table>
<p class="submit">
	<input type="submit" name="find-user-badges" id="find-user-badges" class="button button-primary" value="<?php _e('Find User Badges','userpro'); ?>"  />
</p>

</form>

<?php
if (isset($_POST['badge_user']) && $userpro->user_exists($_POST['badge_user']) ) {

	$user_id = $_POST['badge_user'];
	$badges = get_user_meta($user_id, '_userpro_badges', true);
	if (isset($badges) && is_array($badges) && !empty($badges)){
		echo '<h3>'.sprintf(__('%s\'s Given Badges','userpro'), userpro_profile_data('display_name', $user_id) ).'</h3>';

		foreach($badges as $k => $arr) {

		    if(empty($arr)){
		        unset($badges[$k]);
		        continue;
            }
			?>
			
			<div class="userpro-user-badge">
				<img src="<?php echo $arr['badge_url']; ?>" alt="" title="<?php echo $arr['badge_title']; ?>" /> <?php echo $arr['badge_title']; ?>
				<a href="#" class="button userpro-delete-badge" data-user="<?php echo $user_id; ?>" data-url="<?php echo $arr['badge_url']; ?>"><?php _e('Delete Badge','userpro'); ?></a>
			</div>
			
			<?php
		}
	} else {
		delete_user_meta($user_id,'_userpro_badges');
		echo '<p>'.__('This user does not have any manually assigned badges.','userpro').'</p>';
	}

}
?>
