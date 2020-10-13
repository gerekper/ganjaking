<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<table class="settings-table">
  <tr>
    <td class="settings-table-nav">
      <ul class="sidebar-nav">
        <li><a data-id="general"><?php _e('General', 'memberpress-courses'); ?></a></li>
      </ul>
    </td>
    <td class="settings-table-pages">
      <div class="page" id="general">
        <div class="page-title"><?php _e('General Options', 'memberpress-courses'); ?></div>
        <?php \do_action('mpcs_admin_general_options', $options); ?>
      </div>
    </td>
  </tr>
</table>