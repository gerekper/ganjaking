<?php 
    $activated_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
    if( in_array('userpro-memberlist-layouts/class-userpro-memberlists-setup.php', $activated_plugins)){
        $member_array= array();
        $member_array['search'] = $search;
        $member_array['memberlist_default_search'] = $memberlist_default_search;
        $member_array['searchuser'] = $_GET['searchuser'];
        $member_array['GET'] = $_GET;
        $member_array['users'] = $users;
        $member_array['memberlist_paginate'] = $memberlist_paginate;
        $member_array['memberlist_paginate_top'] = $memberlist_paginate_top;
        $member_array['memberlist_paginate_bottom'] = $memberlist_paginate_bottom;
        $member_array['memberlist_v2_bio'] = $memberlist_v2_bio;
        $member_array['memberlist_v2_pic_size'] = $memberlist_v2_pic_size;
        $args = array_merge($args,$member_array);
        do_action('before_default_layout',$args);
    }
    else {
?>
<div class="userpro userpro-users userpro-users-v2 userpro-<?php echo $i; ?> userpro-<?php echo $layout; ?>" <?php userpro_args_to_data( $args ); ?>>
	
	<div class="userpro-body userpro-body-nopad">
	
		<?php if ($search){ ?>
		<div class="userpro-search">
			<form class="userpro-search-form" action="" method="get">
				
				<?php if ($memberlist_default_search) { ?><input type="text" name="searchuser" id="searchuser" value="<?php if(isset($_GET['searchuser'])) echo $_GET['searchuser']; ?>" placeholder="<?php _e('Search for a user...','userpro'); ?>" /><?php } ?>
				
				<?php do_action('userpro_modify_search_filters', $args); ?>
				
				<input type="hidden" name="page_id" value="<?php echo get_the_ID();?>">
				
				<button type="submit" class="userpro-icon-search userpro-tip" title="<?php _e('Search','userpro'); ?>"></button>
				
				<button type="button" class="userpro-icon-remove userpro-clear-search userpro-tip" title="<?php _e('Clear your Search','userpro'); ?>"></button>
							
			</form>
		</div>
		<?php
		if (isset($users['total']) && !empty($users['total']) && $userpro->memberlist_in_search_mode($args) ){
			echo '<div class="userpro-search-results">'.$userpro->found_members( $users['total'] ).'</div>';
		}
		?>
		<?php } 
		
		if(userpro_get_option('alphabetical_pagination') == 1 ){?>
		<div class="alphabetical-pagination">
		<?php
			$alphabets = range('A','Z');
			foreach ($alphabets as $k => $v){?>
				<span class="alpha-pagination-list <?php if(isset($_GET['userpa']) && $_GET['userpa'] == $v ) {echo 'current-alphabet';}?>"><a class="alpha-pagination-link" href="?userpa=<?php echo $v;?>"><?php echo $v;?></a></span>
			<?php }?>		
		</div>
		
		<?php }?>
		<?php if ( $userpro->memberlist_in_search_mode($args) ) { ?>

		<?php if ( $memberlist_paginate == 1 && $memberlist_paginate_top == 1 && isset($users['paginate'])) { ?><div class="userpro-paginate top"><?php echo $users['paginate']; ?></div><?php } ?>
	
		<?php if (isset($users['users']) && !empty($users['users'])){ ?>
		<?php foreach($users['users'] as $user) : $user_id = $user->ID; ?>
		
		<div class="userpro-awsm">
		
			<div class="userpro-awsm-pic">
			<?php 
			global  $userpro_social;
			?>
				<?php if ( userpro_get_option('lightbox') && userpro_get_option('profile_lightbox') ) { ?>
				<a href="<?php echo $userpro->profile_photo_url($user_id); ?>" class="lightview" data-lightview-caption="<?php echo $userpro->profile_photo_title( $user_id ); ?>" title="<?php echo userpro_profile_data('display_name', $user_id); ?>">
                    <img src="<?php echo $userpro->profile_photo_url($user_id); ?>" alt="profile-pic">
                <?php } else { ?>
				<a href="<?php echo $userpro->permalink($user_id); ?>"><img src="<?php echo $userpro->profile_photo_url($user_id); ?>" alt="profile-pic"></a>
				<?php } ?>
			</div>
			<?php if ($memberlist_v2_showname) { ?>
			<div class="userpro-awsm-name"><a href="<?php echo $userpro->permalink($user_id); ?>" class="<?php userpro_user_via_popup($args); ?> userpro-transition" data-up_username="<?php echo $userpro->id_to_member($user_id); ?>"><?php echo userpro_profile_data('display_name', $user_id); ?></a><?php if ($memberlist_v2_showbadges) { echo userpro_show_badges( $user_id ); } ?></div>
			<?php } ?>
			
			<?php do_action('userpro_after_profile_img' , $user_id); ?>
			<?php if ($memberlist_v2_fields) { ?>
			<div class="userpro-awsm-meta"><?php echo $userpro->meta_fields( $args['memberlist_v2_fields'], $user_id ); ?></div>
			<?php } ?>
			
			<?php if ($memberlist_v2_bio) { ?>
			<div class="userpro-awsm-bio"><?php echo $userpro->shortbio($user_id, $length=100, $fallback = __('The user did not enter a description yet.','userpro') ); ?></div>
			<?php } ?>
			
			<?php if ($memberlist_v2_showsocial) { ?>
			<div class="userpro-awsm-social"><?php echo $userpro->show_social_bar( $args, $user_id, 'userpro-centered-icons' ); ?></div>
			<?php } ?>
			
			<div class="userpro-awsm-link"><a href="<?php echo $userpro->permalink($user_id); ?>" class="userpro-flat-btn userpro-transition"><?php _e('View Profile','userpro'); ?></a><?php if( $args['memberlist_show_follow']) echo $userpro_social->follow_text($user_id);?>
<?php 

if(is_user_logged_in() && userpro_get_option('enable_connect')=='y')
{ ?>


<?php 
		$current_user = wp_get_current_user();
		 $current_user_id=$current_user->ID;
	$userrequest = get_user_meta($user_id,'_userpro_users_request', true);
		$accepted = get_user_meta($current_user_id, '_userpro_connected_userlist', true);
	
	
		
	if(isset($userrequest[$current_user_id]) && $userrequest[$current_user_id])
	{ ?>
		

	<div title="<?php _e('Pending Request','userpro'); ?>" class="userpro_connection_pending userpro_title_connect userpro-centered-icons"> <i class="userpro-icon-connection"></i> </div>
	<?php } 

	elseif(isset($accepted[$user_id]) && $accepted[$user_id])
	{ ?>
<div title="<?php _e('Connected','userpro'); ?>" class="userpro_connection_accepted userpro_title_connect userpro-centered-icons"> <i class="userpro-icon-connection"></i> </div>
	
	<?php } 
	elseif($current_user_id !=$user_id) 
	{?>
	
<div class="userpro_connection userpro_title_connect userpro-centered-icons" title="<?php _e('Send Connect Request','userpro'); ?>" onclick="userpro_connect_user(<?php echo $user_id;?>,'<?php echo userpro_profile_data('display_name', $user_id); ?>');"><i class="userpro-icon-connection"></i>  </div>


	<?php } 
}?>
</div>
			
		</div>
		
		<?php endforeach; ?>
		
		<?php } else { ?>
		<div class="userpro-search-noresults"><?php _e('No users match your search. Please try again.','userpro'); ?></div>
		<?php } ?>

		<?php if ($memberlist_paginate == 1 && $memberlist_paginate_bottom == 1 && isset($users['paginate'])) { ?><div class="userpro-paginate bottom"><?php echo $users['paginate']; ?></div><?php } ?>
		
		<?php } // initial results off/on ?>
	
	</div>

</div>
<?php } ?>
