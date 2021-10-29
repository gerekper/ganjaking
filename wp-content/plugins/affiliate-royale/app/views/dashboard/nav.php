<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<?php
WafpDashboardHelper::nav();

//Let's make sure a default link has been set in the options before displaying this.
if(!empty($default_link))
  $default_affiliate_url = $default_link->display_url($affiliate_id);
else {
  $affiliate = new WafpUser($affiliate_id);
  $default_affiliate_url = $affiliate->default_affiliate_url();
}
?>
  <div id="wafp_main_affiliate_link">
    <div class="wafp_main_affiliate_link_label"><?php _e('My Affiliate link:', 'affiliate-royale', 'easy-affiliate'); ?></div>
    <input type="text" style="display: inline-block;" onfocus="this.select();" onclick="this.select();" readonly="true" value="<?php echo htmlentities($default_affiliate_url); ?>" />
    <span class="wafp-clipboard"><i class="ar-icon-clipboard ar-list-icon icon-clipboardjs" data-clipboard-text="<?php echo $default_affiliate_url; ?>"></i></span>
  </div>
