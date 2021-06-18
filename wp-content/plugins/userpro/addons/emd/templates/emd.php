<!-- filters -->
<input type="button" id="showFilters" class="filter-button" style="display:none;" value="Show filters">
<?php
$emd_state = (int) $userpro_emd->yes_show_filters( $args );
if ($emd_state == 1) { ?>
<div class="emd-filters emd-filters-responsive">

	<form action="" method="get">
	
	<div class="emd-head"><?php _e('Customize your Search','userpro'); ?></div>
	
	<?php $userpro_emd->show_filters( $args ); ?>
	<input type="hidden" name="page_id" value="<?php echo get_the_ID();?>">
	<div class="emd-foot"><input type="submit" name="emd-search" id="emd-search" value="<?php _e('Filter Search','userpro'); ?>" class="userpro-button" /></div>
	
	</form>

</div>
<?php } ?>

<!-- users -->

<div class="emd-main emd-main-<?php echo $emd_state; ?>">

    <?php if ( $userpro->memberlist_in_search_mode($args) ) { ?>
    
	<?php $arr = $userpro_emd->users( $args );
	if (isset($arr['users']) && !empty($arr['users']) ) {
	?>
	
	<?php if (isset($arr['paginate']) && $args['emd_paginate'] && $args['emd_paginate_top'] == 1) { ?><div class="userpro-paginate top"><?php echo $arr['paginate']; ?></div><?php } ?>
		
	<div class="emd-list" data-layoutmode="<?php echo $args['emd_layout']; ?>">
	
		<?php
		foreach($arr['users'] as $user) { $user_id = $user->ID; ?>
		
		<div class="emd-user">
			
			<div class="emd-user-img">
				<a href="<?php echo $userpro->permalink( $user_id ); ?>"><img src="<?php echo $userpro->profile_photo_url($user_id); ?>" alt="profile-pic"></a>
			</div>
			
			<div class="emd-user-info">
				<div class="emd-user-left"><a href="<?php echo $userpro->permalink( $user_id ); ?>" title="<?php _e('View Full Profile','userpro'); ?>"><?php echo userpro_profile_data('display_name', $user_id); ?></a></div>
				<div class="emd-user-right"><?php echo userpro_show_badges( $user_id, true ); ?></div>
				<div class="userpro-clear"></div>
				<?php do_action('userpro_after_profile_img' , $user_id); ?>
			</div>
			
			<?php if ($args['emd_bio']) { ?>
			<div class="emd-user-bio"><?php echo $userpro->shortbio( $user_id ); ?></div>
			<?php } ?>

			<?php if ($args['emd_fields']) { $array = explode(',',$args['emd_fields']); ?>
			<div class="emd-user-columns">
			<?php foreach($array as $field) { ?>
			<?php $userpro_emd->print_field( $field, $user_id ); ?>
			<?php } ?>
			</div>
			<?php } ?>
			
			<?php if ($args['emd_social'] == 1) { ?>
			<div class="emd-user-icons">
				<?php echo $userpro->show_social_bar( $args, $user_id, 'userpro-centered-icons' ); ?>
				<div class="userpro-clear"></div>
			</div>
			<?php } ?>
			
			<div class="userpro-clear"></div>
			
		</div>
		
		<?php } ?>
		
	</div><div class="userpro-clear"></div>
		
	<?php } else { // no results ?>
		
		<div class="emd-list-empty">
			<?php _e('No users are matching your search criteria.','userpro'); ?>
		</div>
		
	<?php } ?>
	
	<?php if (isset($arr['paginate']) && $args['emd_paginate'] ) { ?><div class="userpro-paginate bottom"><?php echo $arr['paginate']; ?></div><?php } ?>
	
	<?php } // initial results off/on ?>
</div>

<div class="userpro-clear"></div>

<script type="text/javascript">

jQuery(document).ready(function(){
	jQuery('#showFilters').click(function(){
			if(jQuery('.emd-filters').hasClass('active')){
				jQuery('.emd-filters').removeClass('active');
			}
			else
			{
				jQuery('.emd-filters').addClass('active');
			}
				
		})
})
</script>

