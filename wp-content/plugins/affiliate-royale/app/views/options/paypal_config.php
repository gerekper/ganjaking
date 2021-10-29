<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div class="wafp-options-pane wafp-integration-option wafp-paypal-config">
  <p><strong><?php _e('Valid PayPal Email Addresses (recommended)', 'affiliate-royale', 'easy-affiliate') ?></strong></p>
  <input type="text" style="width: 100%;" class="form-field" id="<?php echo $wafp_options->paypal_emails_str; ?>" name="<?php echo $wafp_options->paypal_emails_str ?>" value="<?php echo $wafp_options->paypal_emails; ?>" /><br/>
  <div class="description"><?php _e("This is a list of valid paypal email addresses that IPN requests can be recieved from. If this is left blank then any valid IPN request will be recorded as a transaction. It is recommended that you enter all the paypal email addresses (comma separated) that could be used to send IPN requests to your affiliate commission tracker to prevent fraud.", 'affiliate-royale', 'easy-affiliate') ?></div>
  <p><label for="<?php echo $wafp_options->paypal_sandbox_str; ?>"><input type="checkbox" name="<?php echo $wafp_options->paypal_sandbox_str; ?>" id="<?php echo $wafp_options->paypal_sandbox_str; ?>"<?php echo (($wafp_options->paypal_sandbox)?' checked="checked"':''); ?>/>&nbsp;<?php _e('Use PayPal Sandbox','affiliate-royale', 'easy-affiliate'); ?></label></p>
  <p><strong><?php _e('Recieve IPN Requests from hosts other than PayPal:', 'affiliate-royale', 'easy-affiliate') ?></strong></p>
  <input type="text" style="width: 100%;" class="form-field" id="<?php echo $wafp_options->paypal_src_str; ?>" name="<?php echo $wafp_options->paypal_src_str ?>" value="<?php echo $wafp_options->paypal_src; ?>" />
  <span class="description"><?php _e('Please enter the IP Addresses, separated by commas, of hosts authorized for Affiliate Royale to receive IPN requests from. If this is left blank then only IPN requests coming directly from PayPal will be recorded.', 'affiliate-royale', 'easy-affiliate'); ?></span><br/>
  <p><strong><?php _e('Forward IPN Requests to additional URLs:', 'affiliate-royale', 'easy-affiliate') ?></strong></p>
  <textarea style="width: 100%; min-height: 150px;" class="form-field" id="<?php echo $wafp_options->paypal_dst_str; ?>" name="<?php echo $wafp_options->paypal_dst_str ?>"><?php echo $wafp_options->paypal_dst; ?></textarea><br/>
  <span class="description"><?php _e('Please enter URLs to forward IPN requests to. Each URL should be on its own line. If this is left blank then IPN requests will not be forwarded.', 'affiliate-royale', 'easy-affiliate'); ?></span>
</div>
