<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div class="wafp-options-pane wafp-integration-option wafp-paypal-option">
  <p><strong><?php _e('Here are the steps you\'ll need to follow to integrate Affiliate Royale with PayPal', 'affiliate-royale', 'easy-affiliate'); ?>:</strong></p>
  <h3><?php _e('To create your payment button:', 'affiliate-royale', 'easy-affiliate'); ?></h3>
  <ol>
    <li><?php _e('Log Into Your PayPal Account', 'affiliate-royale', 'easy-affiliate'); ?></li>
    <li><?php _e('Go to "Merchant Services" -> "Create Buttons"', 'affiliate-royale', 'easy-affiliate'); ?></li>
    <li><?php _e('Create either a "Buy Now" or "Subscribe" Button', 'affiliate-royale', 'easy-affiliate'); ?></li>
    <li><?php _e('Make Sure You Uncheck "Save Button at PayPal" in Step 2', 'affiliate-royale', 'easy-affiliate'); ?></li>
    <li><?php _e('Make Sure "Add advanced variables" is checked in Step 3 and add the following text into the "Advanced Variables" text area:', 'affiliate-royale', 'easy-affiliate'); ?><br/>
<pre>
notify_url=[wafp_ipn]
custom=[wafp_custom_args]</pre>
    </li>
    <li><?php _e('Click "Create Button"', 'affiliate-royale', 'easy-affiliate'); ?></li>
    <li><?php _e('Click "Remove code protection"', 'affiliate-royale', 'easy-affiliate'); ?></li>
    <li><?php _e('Now you can copy your button\'s code and paste it somewhere on this site. Note: the button must reside on this site in a page or post in order for the affiliate tracking to work properly.', 'affiliate-royale', 'easy-affiliate'); ?></li>
    <li><?php _e('Lastly you\'ll need to edit the HTML you copied onto your page and replace <pre>&lt;input type="hidden" name="custom" value="[wafp_custom_args]"&gt;</pre> with just <pre>[wafp_custom_args]</pre> and <pre>&lt;input type="hidden" name="notify_url" value="[wafp_ipn]"&gt;</pre> with just <pre>[wafp_ipn]</pre>. This is required to get around a change made to shortcode processing in WordPress 4.2.3 and later.', 'affiliate-royale', 'easy-affiliate'); ?></li>
  </ol>
  <h3><?php _e('(Optional) Setup Affiliate Royale to automatically record refunds and process recurring payments:', 'affiliate-royale', 'easy-affiliate'); ?></h3>
  <ol>
    <li><?php _e('Go to "My Account" -> "Profile" -> "Instant Payment Notification Preferences" in PayPal', 'affiliate-royale', 'easy-affiliate'); ?></li>
    <li><?php _e('Make sure IPN is enabled', 'affiliate-royale', 'easy-affiliate'); ?></li>
    <li><?php _e('Paste the following URL into the Notification URL text field and hit save:', 'affiliate-royale', 'easy-affiliate'); ?><br/>
      <pre><?php echo WAFP_SCRIPT_URL . "&controller=paypal&action=ipn"; ?></pre>
    </li>
  </ol>
</div>
