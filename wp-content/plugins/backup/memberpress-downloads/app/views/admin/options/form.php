<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
  <form name="mpdl-options-form" id="mpdl-options" method="post" action="<?php echo admin_url('/admin.php?page=memberpress-downloads-options'); ?>">
    <?php wp_nonce_field('update-options'); ?>
    <table class="settings-table">
      <tr>
        <td class="settings-table-nav">
          <ul class="sidebar-nav">
            <li><a data-id="general"><?php _e('General', 'memberpress-downloads'); ?></a></li>
          </ul>
        </td>
        <td class="settings-table-pages">
          <div class="page" id="general">
            <div class="page-title"><?php _e('General Options', 'memberpress-downloads'); ?></div>
            <?php do_action('mpdl_admin_general_options'); ?>
          </div>
        </td>
      </tr>
    </table>
  </form>
</div>
