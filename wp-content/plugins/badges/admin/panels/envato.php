<?php global $userpro, $userpro_badges; ?>

<?php

	$users = get_users(array(
		'meta_key'     => '_envato_verified',
		'meta_value'   => 1,
		'meta_compare' => '=',
	));
	
	if (!empty($users)){ ?>

	<h3><?php echo sprintf(__('%s Customers verified their purchase','userpro'), count($users)); ?></h3>
	<div class="upadmin-panel">
	
	<?php
	foreach($users as $user) {
		$user_id = $user->ID; 
		
		$buycodes[] = userpro_profile_data('envato_purchase_code', $user_id);		?>
		
		<div class="upadmin-pending-verify">
			<div class="upadmin-pending-img"><a href="<?php echo $userpro->permalink($user_id); ?>" target="_blank"><?php echo get_avatar( $user_id, 64 ); ?></a></div>
			<div><a href="<?php echo $userpro->permalink($user_id); ?>" target="_blank"><?php echo userpro_profile_data('display_name', $user_id); ?></a></div>
			<div><span><?php echo $user->user_email; ?></span></div>
			<div>
				<a href="<?php echo $userpro->permalink($user_id); ?>" class="button button-primary"><?php _e('View Profile','userpro'); ?></a>
			</div>
		</div>

	<?php
		}
	}
	?>
	
	</div>