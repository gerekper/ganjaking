<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div class="wafp-options-pane wafp-integration-option wafp-authorize-config">
  <p>
    <label for="<?php echo $wafp_options->arb_post_process_str; ?>">
      <input type="checkbox" name="<?php echo $wafp_options->arb_post_process_str; ?>" id="<?php echo $wafp_options->arb_post_process_str; ?>"<?php echo (($wafp_options->arb_post_process)?' checked="checked"':''); ?> onclick="jQuery('#<?php echo $wafp_options->arb_post_process_str ?>-option').toggle()"/>&nbsp;
      <?php _e('Process silent posts with cron','affiliate-royale', 'easy-affiliate'); ?>
    </label>
  </p>
  <p id="<?php echo $wafp_options->arb_post_process_str ?>-option"<?php echo (($wafp_options->arb_post_process)?'':' style="display:none;"'); ?>>
    Now add this cron job to your crontab (Note the path to php may vary from system to system)<br>
    <pre>*/15 * * * * /usr/bin/php <?php echo WAFP_URL; ?>/process_responses.php</pre>
  </p>

  <p>
    <label for="<?php echo $wafp_options->arb_debug_str; ?>">
      <input type="checkbox" name="<?php echo $wafp_options->arb_debug_str; ?>" id="<?php echo $wafp_options->arb_debug_str; ?>"<?php echo (($wafp_options->arb_debug)?' checked="checked"':''); ?> onclick="jQuery('#<?php echo $wafp_options->arb_debug_str ?>-option').toggle()"/>&nbsp;
      <?php _e('Debug mode','affiliate-royale', 'easy-affiliate'); ?></label>
    </p>
  <p id="<?php echo $wafp_options->arb_debug_str ?>-option"<?php echo (($wafp_options->arb_debug)?'':' style="display:none;"'); ?>>
    Test Silent Post for subscription:
    <select id="test-silent-post-subid">
      <option value="">Select</option>
      <?php foreach (WafpSubscription::get_all() as $wafp_subscription_id): ?>
        <option value="<?php echo $wafp_subscription_id ?>"><?php echo $wafp_subscription_id ?></option>
      <?php endforeach; ?>
    </select>
    <a href="<?php echo site_url('/index.php?plugin=wafp&controller=authorize&action=test_silent_post&subid=') ?>" id="test-silent-post" class="button">Send</a>
  </p>
</div>
