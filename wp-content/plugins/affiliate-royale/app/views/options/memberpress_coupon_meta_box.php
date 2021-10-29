<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div class="wafp-mepr-coupon-meta-box">
  <input type="checkbox" name="mepr-associate-affiliate-enable" id="mepr-associate-affiliate-enable" style="vertical-align:bottom;" <?php checked($enabled); ?> />
  <label for="mepr-associate-affiliate-enable"><?php _e('Associate an Affiliate', 'affiliate-royale', 'easy-affiliate'); ?></label>

  <div id="mepr-affiliate-search" class="wafp-hidden">
    <label for="mepr-associate-affiliate-username"><?php _e('Affiliate', 'affiliate-royale', 'easy-affiliate'); ?></label><br/>
    <input type="text" name="mepr-associate-affiliate-username" id="mepr-associate-affiliate-username" class="mepr_suggest_user" value="<?php echo $affiliate_login; ?>" />
  </div>
</div>
