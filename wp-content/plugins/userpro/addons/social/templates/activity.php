<div class="userpro userpro-<?php echo $args['i']; ?> userpro-<?php echo $args['layout']; ?>" <?php userpro_args_to_data( $args ); ?>>

	<a href="#" class="userpro-close-popup"><?php _e('Close','userpro'); ?></a>
	
	<div class="userpro-head">
		<div class="userpro-left"><?php echo $args["{$template}_heading"]; ?></div>
		<?php if (isset($args["{$template}_side"])) { ?>
		<div class="userpro-right">
			<a href="#" class="userpro-sc-refresh userpro-button secondary"><?php echo __('Refresh','userpro'); ?></a>
			<img src="<?php echo $userpro->skin_url(); ?>loading-dots.gif" alt="" class="userpro-sc-loader userpro-sc-refresh-loader" />
		</div>
		<?php } ?>
		<div class="userpro-clear"></div>
	</div>
	
	<?php
	// action hook after user header
	if (!isset($user_id)) $user_id = 0;
	$hook_args = array_merge($args, array('user_id' => $user_id));
	//do_action('userpro_after_profile_head', $hook_args);
	?>
	
	<div class="userpro-body userpro-body-nopad">
	
		<?php if ($activity): ?>
		
		<?php  foreach($activity as $timestamp=>$status) : ?>
		
		<div class="userpro-sc">
		
			<?php 
				$content = str_replace('{timestamp}', $userpro->time_elapsed( $status['timestamp'] ), $status['status']);
				echo $content; 
			?>
						
			<div class="userpro-sc-btn">
				<?php echo $userpro_social->follow_text($status['user_id']); ?>
			</div>
		
		</div>
		
		<?php endforeach; ?>
		
		<div class="userpro-sc-load" <?php if ($args['activity_all'] == 0) : echo 'data-user_id="'.$user_id.'"'; endif; ?>>
			<a href="#" class="userpro-button secondary" data-activity_user="<?php echo $args['activity_user']; ?>" data-activity_per_page="<?php echo $args['activity_per_page']; ?>"><?php _e('View more activity','userpro'); ?></a>
			<img src="<?php echo $userpro->skin_url(); ?>loading-dots.gif" alt="" class="userpro-sc-loader" />
		</div>
		
		<?php // If no activity to display // */ ?>
		
		<?php else : ?>
		
		<div class="userpro-sc userpro-sc-noborder">
		<?php if ($args['activity_all'] == 0) { 
			_e('There is no recent activity from the users you follow yet.','userpro');
			} else {
			_e('There is no public activity to display yet.','userpro');
			}?>
		</div>
		
		<?php endif; ?>
	
	</div>

</div>
