<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<div class="wrap">
<h2 id="wafp_title" style="margin: 10px 0px 0px 0px; padding: 0px 0px 0px 56px; height: 48px; background: url(<?php echo WAFP_URL . "/images/affiliate-royale-48.png"; ?>) no-repeat"><?php _e('Affiliate Program: Integration', 'affiliate-royale', 'easy-affiliate'); ?></h2>
<br/>
<h4><?php _e('Tracking Pixel', 'affiliate-royale', 'easy-affiliate'); ?></h4>
<div class="wafp-options-pane">
  <input type="text" value="<?php echo htmlentities( $wafp_options->transaction_tracking_code() ); ?>" onfocus="this.select()" onclick="this.select()" readonly="true" style="width: 100%"/>
  <span class="description"><?php _e('Copy this code and paste it onto your product\'s website. You should also add code to dynamically set the "amount", "payment_id" and "affiliate_id" (affiliate_id only needs to be set if your payment system pushes this across as a post back url) variables.', 'affiliate-royale', 'easy-affiliate'); ?></span>
</div>
