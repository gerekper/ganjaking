<?php

global $current_user;
$DBR_admin = get_user_by('login', 'admin');
$DBR_admin_exists = 1;
if (!isset($DBR_admin->user_login ) || $DBR_admin->user_level < 10 ){
	$DBR_admin_exists = 0;
}

?>

<div class="DBR-box-warning">
	<span style="color:red"><strong><?php _e('WARNING:','advanced-wp-reset'); ?></strong></span>
	<br/>
	<?php _e('The reset makes a fresh installation of your database. Therefore, ANY data in your database will be lost.','advanced-wp-reset'); ?>
	<br/>
	<?php _e('Please do not use this option if you want to keep your posts and pages.','advanced-wp-reset'); ?>
</div>

<div class="DBR-box-info">
	<ul>
		<li><?php _e('The reset does not delete or modify any of your plugins/themes files or server files.','advanced-wp-reset'); ?></li>
		<li><?php _e('All your plugins will be deactivated except this one. You should activate them manually after the reset.','advanced-wp-reset'); ?></li>
		<li>
		<?php 
		if($DBR_admin_exists){
			printf(__('The plugin has detected that the <b>%s</b> user exists. It will be recreated with its current password.','advanced-wp-reset'), "admin");
		}else{
			printf(__('The <b>%s</b> user does not exist. The user <b>%s</b> will be recreated with its current password with user level 10.','advanced-wp-reset'), "admin", esc_html($current_user->user_login));
		}?>
		</li>
		<li><?php _e('After the reset, you will be redirected to the admin login page.','advanced-wp-reset'); ?></li>
	</ul>
</div>

<h3 style="padding-top: 10px"><?php _e('Reset database','advanced-wp-reset'); ?></h3>

<p>
	<?php 
	printf(__('Please type "<b>%s</b>" in the confirmation field below to confirm the reset and then click the reset button.','advanced-wp-reset'), "reset" );
	?>
</p>

<form id="DBR_form" action="" method="post">
	<input id="DBR_reset_comfirmation" type="text" name="DBR_reset_comfirmation" value="" style="width:150px"/>
	<p class="submit">
		<input id="DBR_reset_button" name="DBR_reset_button" type="submit" class="button-primary DBR-button-reset" value="<?php _e("Reset database","advanced-wp-reset"); ?>"/>
	</p>
</form>
