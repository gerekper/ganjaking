<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<p><?php _e('Only affiliates can access this page. If you would like to become an affiliate, please click the button below:', 'affiliate-royale', 'easy-affiliate'); ?></p>
<?php if($wafp_options->affiliate_agreement_enabled): ?>
  <p><?php printf(__('By clicking the button below, you agree to the %1$sAffiliate Sign-up Agreement%2$s.', 'affiliate-royale', 'easy-affiliate'), '<a href="#" id="wafp_agreement_agree">', '</a>'); ?></p>
  <div id="wafp_signup_agreement_text" style="display:none;">
    <textarea readonly style="width:100%;height:200px;"><?php echo $wafp_options->affiliate_agreement_text; ?></textarea>
  </div>
  <p></p>
<?php endif; ?>
<form name="become_affiliate_form" action="" method="post">
<input type="submit" name="become_affiliate_submit" value="<?php _e('Become an Affiliate', 'affiliate-royale', 'easy-affiliate'); ?>" />
</form>
