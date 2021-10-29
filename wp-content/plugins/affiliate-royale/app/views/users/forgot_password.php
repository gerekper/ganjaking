<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<h3><?php _e('Request a Password Reset', 'affiliate-royale', 'easy-affiliate'); ?></h3>
<form name="wafp_forgot_password_form" id="wafp_forgot_password_form" action="" method="post">
  <p>
    <label><?php _e('Enter Your Username or Email Address', 'affiliate-royale', 'easy-affiliate'); ?><br/>
    <input type="text" name="wafp_user_or_email" id="wafp_user_or_email" class="input" value="" tabindex="600" style="width: auto; min-width: 250px; font-size: 12px; padding: 4px;" /></label>
  </p>
  <p class="submit">
    <input type="submit" name="wp-submit" id="wp-submit" class="button-primary wafp-share-button" value="<?php _e('Request Password Reset', 'affiliate-royale', 'easy-affiliate'); ?>" tabindex="610" />
    <input type="hidden" name="wafp_process_forgot_password_form" value="true" />
  </p>
</form>