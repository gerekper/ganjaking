<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div class="mepr-page-title"><?php _e('Webhook Events', 'memberpress-developer-tools'); ?></div>
<p><?php _e('Here are the Events sent to URLs configured with Webhooks. Select an Event for more information, to have the ability to send test POST requests, and see an example of the JSON response you can expect.', 'memberpress-developer-tools'); ?></p>

<h3><?php _e('Select an Event:', 'memberpress-developer-tools'); ?></h3>
<?php $whk = MpdtCtrlFactory::fetch('webhooks'); ?>

<div class="mpdt_select_wrap mpdt_events_dropdown_wrap">
  <select id="mpdt_events_dropdown" class="mpdt_select">
    <option value="-1">-- <?php _e('Select an Event', 'memberpress-developer-tools'); ?> --</option>
    <?php foreach($whk->events as $slug => $info): ?>
      <?php if('all'==$slug) { continue; } ?>
      <option value="<?php echo $slug; ?>"><?php echo $info->label; ?></option>
    <?php endforeach; ?>
  </select>
  <span class="mpdt_rolling">
    <?php echo file_get_contents(MPDT_IMAGES_PATH . '/rolling.svg'); ?>
  </span>
</div>

<div>&nbsp;</div>

<div id="mpdt_event_display" class="mepr-sub-box" style="display: none;">
  <div class="mepr-arrow mepr-gray mepr-up mepr-sub-box-arrow"> </div>

  <h3 class="mepr-page-heading"><?php _e('Description', 'memberpress-developer-tools'); ?></h3>
  <div class="mepr_event_description"></div>

  <div>&nbsp;</div>

  <h3 class="mepr-page-heading"><?php _e('Test', 'memberpress-developer-tools'); ?></h3>
  <p><?php _e('Click this button to send a POST request of this event to all webhooks you currently have setup.', 'memberpress-developer-tools'); ?></p>
  <div class="mpdt_test_webhook">
    <button class="button button-primary"><?php _e('Send Test', 'memberpress-developer-tools'); ?></button>
    <span class="mpdt_rolling"><?php echo file_get_contents(MPDT_IMAGES_PATH . '/rolling.svg'); ?></span>
    <span class="mpdt_test_webhook_message"></span>
    <span class="mpdt_test_webhook_error"></span>
  </div>

  <div>&nbsp;</div>

  <h3 class="mepr-page-heading"><?php _e('JSON Response', 'memberpress-developer-tools'); ?></h3>
  <p><?php _e('MemberPress will send JSON like this in the POST body to your Webhook when this event triggers.', 'memberpress-developer-tools'); ?></p>
  <div id="mpdt_event_json"><pre class="mpdt_code_display"><code class="json"></code></pre></div>

</div>

