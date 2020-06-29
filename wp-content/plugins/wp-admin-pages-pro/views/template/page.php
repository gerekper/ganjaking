<div class="<?php echo $admin_page->menu_type !== 'widget' ? 'wrap wu-apc-wrap' : 'wu-apc-widget'; ?> <?php echo !$admin_page->add_margin ? 'wu-apc-wrap-no-margin' : ''; ?> <?php echo $admin_page->display_title ? 'wu-apc-wrap-has-title' : 'wu-apc-wrap-has-no-title'; ?>">

	<?php if ($admin_page->display_title) : ?>
    <h1 class="wp-heading-inline"><?php echo $admin_page->title; ?></h1>
    <hr class="wp-header-end">
	<?php else : ?>
    <h1 class="wu-apc-invisible-title" style="height: 0;"></h1>
	<?php endif; ?>
  
	<?php if ($admin_page->content_type == 'html') : ?>
  
    <div id="wu-apc-html-content">
		<?php $admin_page->display_html_content(); ?>
    </div>

	<?php elseif ($admin_page->content_type == 'external_link' && $admin_page->external_link_open_new_tab) : ?>
    
    <div id="wu-iframe-content" style="position:relative;width:100%;"  >

      <iframe src="<?php echo $admin_page->external_link_url; ?>"  style="width:100%; height:100vh; border:none; margin:0; padding:0; overflow:hidden;">
        Your browser doesn't support iframes
      </iframe>

    </div>

	<?php elseif ($admin_page->content_type == 'normal') : ?>
    
    <div id="wu-apc-content">
		<?php echo apply_filters('the_content', wu_apc_process_page_content($admin_page->content)); ?>
    </div>

	<?php endif; ?>

	<?php
	/**
	 * Display custom editor contents.
	 *
	 * @since 1.0.1
	 * @param WU_Admin_Page The admin page object
	 * @return void
	 */
	do_action('wu_admin_pages_display_content', $admin_page);
	?>

  <br class="clear">

	<?php if (current_user_can(WU_Admin_Pages()->user_can_manage) && WU_Admin_Pages()->should_display_admin_menu()) : ?>

    <div class="wu-row wu-apc-absolute-row-button">

      <span class="wu-apc-edit-options">
      
        <?php if (WP_Ultimo_APC()->is_network_active()) : ?> 
        <span class="description">
			  <?php echo _e('You are seeing this because you are a super admin.', 'wu-apc'); ?>
        </span>
        <?php endif; ?>

        <?php if (WP_Ultimo_APC()->is_network_active()) : ?>
        <a href="<?php echo network_admin_url('admin.php?page=' . WU_Admin_Pages_Standalone_Dependencies()->edit_menu_slug . "&admin_page_id={$admin_page->id}"); ?>" class="button button-primary" target="_blank">
        <?php else : ?>
        <a href="<?php echo admin_url('admin.php?page=' . WU_Admin_Pages_Standalone_Dependencies()->edit_menu_slug . "&admin_page_id={$admin_page->id}"); ?>" class="button button-primary" target="_blank">
        <?php endif; ?>
          <?php echo _e('Edit on Admin Page Creator', 'wu-apc'); ?> <span class="dashicons dashicons-external"></span>
        </a>
      
      </span>

      <span class="wu-apc-icon dashicons dashicons-admin-generic"></span>
    
    </div>

	<?php endif; ?>

</div>
