<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div class="wafp-options-pane wafp-integration-option wafp-wishlist-option">
  <p><strong><?php _e('Here are the steps you\'ll need to follow to integrate Affiliate Royale with Wishlist Member when its using PayPal to process payments', 'affiliate-royale', 'easy-affiliate'); ?></strong></p>
  <h3><?php _e('To create your payment button:', 'affiliate-royale', 'easy-affiliate'); ?></h3>
  <ol>
    <li><?php _e('Log Into Your PayPal Account', 'affiliate-royale', 'easy-affiliate'); ?></li>
    <li><?php _e('Go to "Merchant Services" -> "Create Buttons"', 'affiliate-royale', 'easy-affiliate'); ?></li>
    <li><?php _e('Create either a "Buy Now" or "Subscribe" Button', 'affiliate-royale', 'easy-affiliate'); ?></li>
    <li><?php _e('Make Sure You Uncheck "Save Button at PayPal" in Step 2', 'affiliate-royale', 'easy-affiliate'); ?></li>
    <li><?php _e('Make Sure "Add advanced variables" is checked in Step 3 and add the following text into the "Advanced Variables" text area underneath your custom variables for Wishlist Member:', 'affiliate-royale', 'easy-affiliate'); ?><br/>
      <pre><strong>custom=[wafp_custom_args]</strong></pre>
    </li>
    <li><?php _e('Click "Create Button"', 'affiliate-royale', 'easy-affiliate'); ?></li>
    <li><?php _e('Click "Remove code protection"', 'affiliate-royale', 'easy-affiliate'); ?></li>
    <li><?php _e('Now you can copy your button\'s code and paste it somewhere on this site. Note: the button must reside on this site in a page or post in order for the affiliate tracking to work properly.', 'affiliate-royale', 'easy-affiliate'); ?></li>
    <li><?php _e('Lastly you\'ll need to edit the HTML you copied onto your page and replace <b>&lt;input type="hidden" name="custom" value="[wafp_custom_args]"&gt;</b> with just <b>[wafp_custom_args]</b>. This is required to get around a change made to shortcode processing in WordPress 4.2.3 and later.', 'affiliate-royale', 'easy-affiliate'); ?></li>
  </ol>
  <h3><?php _e('(Optional) Setup Affiliate Royale to automatically record refunds and process recurring payments:', 'affiliate-royale', 'easy-affiliate'); ?></h3>
  <ol>
    <li><?php _e('Go to "My Account" -> "Profile" -> "Instant Payment Notification Preferences" in PayPal', 'affiliate-royale', 'easy-affiliate'); ?></li>
    <li><?php _e('Make sure IPN is enabled', 'affiliate-royale', 'easy-affiliate'); ?></li>
    <li><?php _e('Paste the following URL into the Notification URL text field and hit save:', 'affiliate-royale', 'easy-affiliate'); ?><br/>
      <pre><strong><?php echo WAFP_SCRIPT_URL . "&controller=paypal&action=ipn"; ?></strong></pre>
    </li>
  </ol>
</div>
