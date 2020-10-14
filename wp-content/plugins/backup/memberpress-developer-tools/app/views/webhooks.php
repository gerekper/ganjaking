<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<?php $admin = MpdtCtrlFactory::fetch('admin'); ?>
<div class="mepr-page-title"><?php _e('Webhooks', 'memberpress-developer-tools'); ?></div>
<p><?php _e('Webhooks can send JSON push notices to specific URLs via POST when specific events occur in MemberPress. You can configure your webhooks here:', 'memberpress-developer-tools'); ?></p>

<form action="" method="post" id="mpdt_ops_form">
  <?php
    if($webhooks !== false && !empty($webhooks)) {
      foreach($webhooks as $count => $webhook) {
        $admin->webhook_row($count, $webhook);
      }
    }
    else {
      $admin->webhook_row(0);
    }
  ?>

  <div>
    <a href="" class="mpdt_add_row" title="<?php _e('Add Webhook URL', 'memberpress-developer-tools'); ?>"><i class="mp-icon mp-icon-plus-circled mp-24"></i></a>
  </div>

  <div class="mpdt_spacer"></div>

  <input type="submit" class="button button-primary" value="<?php _e('Save Webhooks', 'memberpress-developer-tools'); ?>" />
</form>

