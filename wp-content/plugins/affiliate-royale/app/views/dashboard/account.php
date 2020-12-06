<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<?php
if(isset($wafp_process_profile) and $wafp_process_profile == "Y")
  echo '<div id="wafp-account-saved">'.__('Your account was successfully saved!', 'affiliate-royale', 'easy-affiliate').'</div>';
?>
<h4><?php _e('Affiliate Profile', 'affiliate-royale', 'easy-affiliate'); ?>:</h4>
<form action="" method="post">
<input type="hidden" name="wafp_process_profile" value="Y" />
<table class="wafp-frontend-table">
  <tr>
    <td class="wafp-frontend-label-col"><?php _e('First Name', 'affiliate-royale', 'easy-affiliate'); ?>:&nbsp;</td>
    <td><input type="text" class="wafp-frontend-text-input" name="wafp_dashboard_first_name" value="<?php echo $wafp_dashboard_first_name; ?>" /></td>
  </tr>
  <tr>
    <td><?php _e('Last Name', 'affiliate-royale', 'easy-affiliate'); ?>:&nbsp;</td>
    <td><input type="text" class="wafp-frontend-text-input" name="wafp_dashboard_last_name" value="<?php echo $wafp_dashboard_last_name; ?>" /></td>
  </tr>

  <?php if($wafp_options->payment_type == 'paypal'): ?>
  <tr>
    <td><?php _e('PayPal Email', 'affiliate-royale', 'easy-affiliate'); ?>:&nbsp;</td>
    <td><input type="text" class="wafp-frontend-text-input" name="wafp_dashboard_paypal" value="<?php echo $wafp_dashboard_paypal; ?>" /></td>
  </tr>
  <?php endif; ?>

  <?php if($wafp_options->show_address_fields): ?>
  <tr>
    <td><?php _e('Address Line 1', 'affiliate-royale', 'easy-affiliate'); ?>:&nbsp;</td>
    <td><input type="text" class="wafp-frontend-text-input" name="wafp_dashboard_address_one" value="<?php echo $wafp_dashboard_address_one; ?>" /></td>
  </tr>
  <tr>
    <td><?php _e('Address Line 2', 'affiliate-royale', 'easy-affiliate'); ?>:&nbsp;</td>
    <td><input type="text" class="wafp-frontend-text-input" name="wafp_dashboard_address_two" value="<?php echo $wafp_dashboard_address_two; ?>" /></td>
  </tr>
  <tr>
    <td><?php _e('City', 'affiliate-royale', 'easy-affiliate'); ?>:&nbsp;</td>
    <td><input type="text" class="wafp-frontend-text-input" name="wafp_dashboard_city" value="<?php echo $wafp_dashboard_city; ?>" /></td>
  </tr>
  <tr>
    <td><?php _e('State/Province', 'affiliate-royale', 'easy-affiliate'); ?>:&nbsp;</td>
    <td><input type="text" class="wafp-frontend-text-input" name="wafp_dashboard_state" value="<?php echo $wafp_dashboard_state; ?>" /></td>
  </tr>
  <tr>
    <td><?php _e('Zip/Postal Code', 'affiliate-royale', 'easy-affiliate'); ?>:&nbsp;</td>
    <td><input type="text" class="wafp-frontend-text-input" name="wafp_dashboard_zip" value="<?php echo $wafp_dashboard_zip; ?>" /></td>
  </tr>
  <tr>
    <td><?php _e('Country', 'affiliate-royale', 'easy-affiliate'); ?>:&nbsp;</td>
    <td><input type="text" class="wafp-frontend-text-input" name="wafp_dashboard_country" value="<?php echo $wafp_dashboard_country; ?>" /></td>
  </tr>
  <?php endif; ?>

  <?php if($wafp_options->show_tax_id_fields): ?>
  <tr>
    <td><?php _e('SSN / Tax ID', 'affiliate-royale', 'easy-affiliate'); ?>:&nbsp;</td>
    <td><input type="text" class="wafp-frontend-text-input" name="wafp_dashboard_tax_id_us" value="<?php echo $wafp_dashboard_tax_id_us; ?>" />&nbsp;<em><?php _e('US Residents', 'affiliate-royale', 'easy-affiliate'); ?></em></td>
  </tr>
  <tr>
    <td><?php _e('Intern\'l Tax ID', 'affiliate-royale', 'easy-affiliate'); ?>:&nbsp;</td>
    <td><input type="text" class="wafp-frontend-text-input" name="wafp_dashboard_tax_id_int" value="<?php echo $wafp_dashboard_tax_id_int; ?>" />&nbsp;<em><?php _e('Non-US Residents', 'affiliate-royale', 'easy-affiliate'); ?></em></td>
  </tr>
  <?php endif; ?>

  <?php do_action('wafp-dashboard-account-fields'); ?>

</table>
<input type="submit" value="<?php _e('Save Profile', 'affiliate-royale', 'easy-affiliate'); ?>" name="submit" />
</form>
