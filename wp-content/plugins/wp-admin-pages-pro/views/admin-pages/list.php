<div class="wrap">
  <h1>
    <?php _e('Admin Pages', 'wu-apc'); ?>

    <?php if (WP_Ultimo_APC()->is_network_active()) : ?>
    <a href="<?php echo network_admin_url('admin.php?page=' . WU_Admin_Pages_Standalone_Dependencies()->edit_menu_slug); ?>" class="page-title-action"><?php _e('Add new Admin Page', 'wu-apc'); ?></a>
    <?php else : ?>
    <a href="<?php echo admin_url('admin.php?page=' . WU_Admin_Pages_Standalone_Dependencies()->edit_menu_slug); ?>" class="page-title-action"><?php _e('Add new Admin Page', 'wu-apc'); ?></a>
    <?php endif; ?>

  </h1>

    <?php if (isset($_GET['deleted']) && current_user_can('edit_posts')) : ?> 
    <div id="message" class="updated notice notice-success is-dismissible below-h2">
      <p><?php printf(__('%s Admin Page(s) were successfully deleted!', 'wu-apc'), esc_html($_GET['deleted'])); ?></p>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['duplicated']) && current_user_can('edit_posts')) : ?>
    <div id="message" class="updated notice notice-success is-dismissible below-h2">
      <p><?php printf(__('%s Admin Page(s) were successfully duplicated!', 'wu-apc'), esc_html($_GET['duplicated'])); ?></p>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['activated']) && current_user_can('edit_posts')) : ?>
    <div id="message" class="updated notice notice-success is-dismissible below-h2">
      <p><?php printf(__('%s Admin Page(s) were successfully activated!', 'wu-apc'), esc_html($_GET['activated'])); ?></p>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['deactivated']) && current_user_can('edit_posts')) : ?>
    <div id="message" class="updated notice notice-success is-dismissible below-h2">
      <p><?php printf(__('%s Admin Page(s) were successfully deactivated!', 'wu-apc'), esc_html($_GET['deactivated'])); ?></p>
    </div>
    <?php endif; ?>
  
  <!--
  <p class="desc">
    <?php _e('Explanation', 'wu-apc'); ?>
  </p>
  -->

  <div id="poststuff">
      <div id="post-body" class="">
          <div id="post-body-content">
              <div class="meta-box-sortables ui-sortable">
                  <form method="post">
						<?php
						$table->prepare_items();
						$table->display();
						?>
                  </form>
              </div>
          </div>
      </div>
      <br class="clear">
  </div>
</div>
