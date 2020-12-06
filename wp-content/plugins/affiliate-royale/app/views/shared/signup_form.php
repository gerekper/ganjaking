<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<form name="wafp_registerform" id="wafp_registerform" action="" method="post">
<input type="hidden" id="wafp-process-form" name="wafp-process-form" value="Y" />
<table class="affroy_signup_form">
  <tr id="wafp_signup_first_name_row">
    <td><label for="user_first_name"><?php _e('First Name', 'affiliate-royale', 'easy-affiliate'); ?>:&nbsp;</td>
    <td><input type="text" name="user_first_name" id="user_first_name" class="input wafp_signup_input" value="<?php echo (isset($user_first_name)?$user_first_name:''); ?>" tabindex="1000" /></td>
  </tr>
  <tr id="wafp_signup_last_name_row">
    <td><label for="user_last_name"><?php _e('Last Name', 'affiliate-royale', 'easy-affiliate'); ?>:&nbsp;</td>
    <td><input type="text" name="user_last_name" id="user_last_name" class="input wafp_signup_input" value="<?php echo (isset($user_last_name)?$user_last_name:''); ?>" tabindex="2000" /></td>
  </tr>
  <tr id="wafp_signup_username_row">
    <td><label><?php _e('Choose a Username', 'affiliate-royale', 'easy-affiliate'); ?>*:&nbsp;</label></td>
    <td><input type="text" name="user_login" id="user_login" class="input wafp_signup_input" value="<?php echo (isset($user_login)?$user_login:''); ?>" tabindex="3000" /></td>
  </tr>
  <tr id="wafp_signup_email_row">
    <td><label><?php _e('E-mail', 'affiliate-royale', 'easy-affiliate'); ?>*:&nbsp;</label></td>
    <td><input type="text" name="user_email" id="user_email" class="input wafp_signup_input" value="<?php echo (isset($user_email)?$user_email:''); ?>" tabindex="4000" /></td>
  </tr>
<?php
  if($wafp_options->show_address_fields) {
?>
  <tr id="wafp_signup_address_one_row">
    <td><label><?php _e('Address Line 1', 'affiliate-royale', 'easy-affiliate'); ?>*:&nbsp;</label></td>
    <td><input type="text" name="<?php echo WafpUser::$address_one_str; ?>" id="<?php echo WafpUser::$address_one_str; ?>" class="input wafp_signup_input" value="<?php echo (isset($_POST[WafpUser::$address_one_str])?$_POST[WafpUser::$address_one_str]:''); ?>" tabindex="5000" /></td>
  </tr>
  <tr id="wafp_signup_address_two_row">
    <td><label><?php _e('Address Line 2', 'affiliate-royale', 'easy-affiliate'); ?>:&nbsp;</label></td>
    <td><input type="text" name="<?php echo WafpUser::$address_two_str; ?>" id="<?php echo WafpUser::$address_two_str; ?>" class="input wafp_signup_input" value="<?php echo (isset($_POST[WafpUser::$address_two_str])?$_POST[WafpUser::$address_two_str]:''); ?>" tabindex="6000" /></td>
  </tr>
  <tr id="wafp_signup_address_city_row">
    <td><label><?php _e('City', 'affiliate-royale', 'easy-affiliate'); ?>*:&nbsp;</label></td>
    <td><input type="text" name="<?php echo WafpUser::$city_str; ?>" id="<?php echo WafpUser::$city_str; ?>" class="input wafp_signup_input" value="<?php echo (isset($_POST[WafpUser::$city_str])?$_POST[WafpUser::$city_str]:''); ?>" tabindex="7000" /></td>
  </tr>
  <tr id="wafp_signup_address_state_row">
    <td><label><?php _e('State/Province', 'affiliate-royale', 'easy-affiliate'); ?>*:&nbsp;</label></td>
    <td><input type="text" name="<?php echo WafpUser::$state_str; ?>" id="<?php echo WafpUser::$state_str; ?>" class="input wafp_signup_input" value="<?php echo (isset($_POST[WafpUser::$state_str])?$_POST[WafpUser::$state_str]:''); ?>" tabindex="8000" /></td>
  </tr>
  <tr id="wafp_signup_address_zip_row">
    <td><label><?php _e('Zip/Postal Code', 'affiliate-royale', 'easy-affiliate'); ?>*:&nbsp;</label></td>
    <td><input type="text" name="<?php echo WafpUser::$zip_str; ?>" id="<?php echo WafpUser::$zip_str; ?>" class="input wafp_signup_input" value="<?php echo (isset($_POST[WafpUser::$zip_str])?$_POST[WafpUser::$zip_str]:''); ?>" tabindex="9000" /></td>
  </tr>
  <tr id="wafp_signup_address_country_row">
    <td><label><?php _e('Country', 'affiliate-royale', 'easy-affiliate'); ?>*:&nbsp;</label></td>
    <td><input type="text" name="<?php echo WafpUser::$country_str; ?>" id="<?php echo WafpUser::$country_str; ?>" class="input wafp_signup_input" value="<?php echo (isset($_POST[WafpUser::$country_str])?$_POST[WafpUser::$country_str]:''); ?>" tabindex="9500" /></td>
  </tr>
<?php
  }
  if($wafp_options->show_tax_id_fields) {
?>
  <tr id="wafp_signup_ssn_row">
    <td><label><?php _e('SSN / Tax ID', 'affiliate-royale', 'easy-affiliate'); ?>:&nbsp;</label></td>
    <td><input type="text" name="<?php echo WafpUser::$tax_id_us_str; ?>" id="<?php echo WafpUser::$tax_id_us_str; ?>" class="input wafp_signup_input" value="<?php echo (isset($_POST[WafpUser::$tax_id_us_str])?$_POST[WafpUser::$tax_id_us_str]:''); ?>" tabindex="10000" />&nbsp;<em><?php _e('US Residents (###-##-#### or ##-#######)', 'affiliate-royale', 'easy-affiliate'); ?></em></td>
  </tr>
  <tr id="wafp_signup_intlid_row">
    <td><label><?php _e('International Tax ID', 'affiliate-royale', 'easy-affiliate'); ?>:&nbsp;</label></td>
    <td><input type="text" name="<?php echo WafpUser::$tax_id_int_str; ?>" id="<?php echo WafpUser::$tax_id_int_str; ?>" class="input wafp_signup_input" value="<?php echo (isset($_POST[WafpUser::$tax_id_int_str])?$_POST[WafpUser::$tax_id_int_str]:''); ?>" tabindex="11000" />&nbsp;<em><?php _e('Non-US Residents', 'affiliate-royale', 'easy-affiliate'); ?></em></td>
  </tr>
<?php
  }
  if($wafp_options->payment_type == 'paypal') {
?>
  <tr id="wafp_signup_paypal_email_row">
    <td><label><?php _e('PayPal E-mail', 'affiliate-royale', 'easy-affiliate'); ?>*:&nbsp;</label></td>
    <td><input type="text" name="<?php echo WafpUser::$paypal_email_str; ?>" id="<?php echo WafpUser::$paypal_email_str; ?>" class="input wafp_signup_input" value="<?php echo (isset($_POST[WafpUser::$paypal_email_str])?$_POST[WafpUser::$paypal_email_str]:''); ?>" tabindex="12000" /></td>
  </tr>
<?php
  }
?>
  <?php do_action('wafp-inner-user-signup-fields'); ?>
  <tr id="wafp_signup_password_one_row">
    <td><label><?php _e('Create a Password', 'affiliate-royale', 'easy-affiliate'); ?>:&nbsp;</label></td>
    <td><input type="password" name="wafp_user_password" id="wafp_user_password" class="input wafp_signup_input" tabindex="13000"/></td>
  </tr>
  <tr id="wafp_signup_password_two_row">
    <td><label><?php _e('Password Confirmation', 'affiliate-royale', 'easy-affiliate'); ?>:&nbsp;</label></td>
    <td><input type="password" name="wafp_user_password_confirm" id="wafp_user_password_confirm" class="input wafp_signup_input" tabindex="13000"/></td>
  </tr>

<?php
  if($wafp_options->affiliate_agreement_enabled)
  {
?>
  <tr id="wafp_signup_signup_agreement_row">
    <td colspan="2">
      <input type="checkbox" name="wafp_user_signup_agreement" id="wafp_user_signup_agreement" style="width:auto;" /><?php printf(__('I agree to the %1$sAffiliate Sign-up Agreement%2$s.', 'affiliate-royale', 'easy-affiliate'), '<a href="#" id="wafp_agreement_agree">', '</a>'); ?><br/>
      <div id="wafp_signup_agreement_text" style="display:none;">
        <textarea readonly style="width:100%;height:200px;"><?php echo $wafp_options->affiliate_agreement_text; ?></textarea>
      </div>
    </td>
  </tr>
<?php
  }
?>
<!-- Extra signup fields show here -->
<?php do_action('wafp-user-signup-fields'); ?>
</table>

  <br class="clear" />
  <input type="text" name="wafp_honeypot" id="wafp_honeypot" class="input wafp_honeypot wafp-hidden" style="display: none !important; margin 0; padding 0;"/>
  <p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="wafp-share-button" value="<?php _e('Sign Up', 'affiliate-royale', 'easy-affiliate'); ?>" tabindex="13000" /></p>
</form>
