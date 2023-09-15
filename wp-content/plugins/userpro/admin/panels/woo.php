<form method="post" action="">

<p class="upadmin-highlight"><?php _e('Click on the button below to automatically add WooCommerce profile fields to UserPro fields and make users manage their WooCommerce profile data from one place, their UserPro profile!','userpro'); ?></p>

<p class="submit">

	<?php if ( get_option('userpro_update_woosync') == 1) { ?>

    <input type="hidden" name="user_pro_nonce" value="<?= wp_create_nonce("user_pro_nonce"); ?>";>
	<input type="submit" name="woosync" id="woosync" class="up-admin-btn approve small" value="<?php _e('Rebuild WooCommerce Sync','userpro'); ?>"  />
	<input type="submit" name="woosync_del" id="woosync_del" class="up-admin-btn remove small" value="<?php _e('Remove WooCommerce Sync','userpro'); ?>"  />
	
	<?php } else { ?>

    <input type="hidden" name="user_pro_nonce" value="<?= wp_create_nonce("user_pro_nonce"); ?>";>
	<input type="submit" name="woosync" id="woosync" class="up-admin-btn up-admin-btn--dark-blue small" value="<?php _e('Start WooCommerce Sync','userpro'); ?>"  />
	
	<?php } ?>
	
</p>

</form>