<?php
  if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

  $wafp_options = WafpOptions::fetch();
  $paypal_on    = $wafp_options->payment_type == 'paypal';
?>
<div id="wafp-admin-affiliate-panel" class="wrap">
<?php WafpAppHelper::plugin_title(__('Pay Affiliates','affiliate-royale', 'easy-affiliate')); ?>
<form action="" method="post">
<input type="hidden" name="wafp-update-payments" value="Y" />
<input type="hidden" name="wafp-period" value="<?php echo $period; ?>" />
<p><?php _e('Select the period you want to view', 'affiliate-royale', 'easy-affiliate'); ?>:<br/><?php WafpReportsHelper::periods_dropdown('wafp-report-period', $period, 'javascript:wafp_view_admin_affiliate_page( \'admin_affiliate_payments\', this.value, 1, \'\', true);'); ?>&nbsp;&nbsp;<img src="<?php echo admin_url('images/loading.gif'); ?>" alt="<?php _e('Loading...', 'affiliate-royale', 'easy-affiliate'); ?>" style="display: none;" class="wafp-stats-loader" /></p>
<table class="widefat post fixed wafp-table wafp-owed-payments-table" cellspacing="0">
<thead>
  <tr>
    <th class="manage-column wafp-pay-affiliate-col"><?php _e('Affiliate', 'affiliate-royale', 'easy-affiliate'); ?></th>
    <th class="manage-column wafp-pay-name-col"><?php _e('Name', 'affiliate-royale', 'easy-affiliate'); ?></th>
    <th class="manage-column wafp-status-col"><?php _e('Status', 'affiliate-royale', 'easy-affiliate'); ?></th>
    <?php if( $paypal_on ): ?>
      <th class="manage-column wafp-pay-paypal-col"><?php _e('PayPal Email', 'affiliate-royale', 'easy-affiliate'); ?></th>
    <?php endif; ?>
    <th class="manage-column wafp-pay-commissions-col"><?php _e('Commissions', 'affiliate-royale', 'easy-affiliate'); ?></th>
    <th class="manage-column wafp-pay-corrections-col"><?php _e('Corrections', 'affiliate-royale', 'easy-affiliate'); ?></th>
    <th class="manage-column wafp-pay-payment-col"><?php _e('To Payout', 'affiliate-royale', 'easy-affiliate'); ?></th>
    <th class="manage-column wafp-pay-paid-col"><?php _e('Paid', 'affiliate-royale', 'easy-affiliate'); ?></th>
  </tr>
</thead>
<tbody>
<?php
  if(empty($totals)):
   ?>
   <tr>
     <td colspan="7"><?php _e('No Payments are due for this period.', 'affiliate-royale', 'easy-affiliate'); ?></td>
   </tr>
   <?php
  else:
    $row_index = 0;
    foreach($totals as $key => $total):
      $row = $results[$key];

      $paypal_email = get_user_meta($row->aff_id, 'wafp_paypal_email', true);
      $first_name   = get_user_meta($row->aff_id, 'first_name', true);
      $last_name    = get_user_meta($row->aff_id, 'last_name', true);
      $address_one  = get_user_meta($row->aff_id, 'wafp_user_address_one', true);
      $address_two  = get_user_meta($row->aff_id, 'wafp_user_address_two', true);
      $city         = get_user_meta($row->aff_id, 'wafp_user_city', true);
      $state        = get_user_meta($row->aff_id, 'wafp_user_state', true);
      $zip          = get_user_meta($row->aff_id, 'wafp_user_zip', true);
      $country      = get_user_meta($row->aff_id, 'wafp_user_country', true);
      $is_blocked   = get_user_meta($row->aff_id, 'wafp_is_blocked', true);
      $is_affiliate = get_user_meta($row->aff_id, 'wafp_is_affiliate', true);

      if((float)$row->correction_amount > 0.00)
        $correction = "<span style=\"color: red\">(" . WafpAppHelper::format_currency( (float)$row->correction_amount) . ")</span>";
      else
        $correction = WafpAppHelper::format_currency( (float)$row->correction_amount);

      $alternate = ( $row_index++ % 2 ? '' : 'alternate' );

      $error = ( !$is_affiliate or $is_blocked or
                 ( $paypal_on and empty($paypal_email) ) or
                 ( $wafp_options->minimum > (float)$total ) ) ? 'error' : '';

      if( !$is_affiliate ) {
        $status = '<strong>'. __('Not Affiliate', 'affiliate-royale', 'easy-affiliate') . '</strong>';
      }
      else if( $is_blocked ) {
        $status = '<strong>' . __('Blocked', 'affiliate-royale', 'easy-affiliate') . '</strong>';
      }
      else if( $paypal_on and empty($paypal_email) ) {
        $status = '<strong>' . __('No PayPal Email', 'affiliate-royale', 'easy-affiliate') . '</strong>';
      }
      else if( $wafp_options->minimum > (float)$total ) {
        $status = '<strong>' . __('Below Minimum', 'affiliate-royale', 'easy-affiliate') . '</strong>';
      }
      else {
        $status = __('Eligible', 'affiliate-royale', 'easy-affiliate');
      }

    $aff = new WafpUser();
    $aff->load_user_data_by_login( $row->aff_login );

    $profile_url = admin_url("user-edit.php?user_id=".$aff->get_id());
    $clicks_url  = admin_url("admin.php?page=affiliate-royale-clicks&search=".$row->aff_login);
    $txns_url    = admin_url("admin.php?page=affiliate-royale-transactions&search=".$row->aff_login);

    ?>
  <tr class="<?php echo "{$alternate} {$error}"; ?>">
    <td><a href="<?php echo $profile_url; ?>"><strong><?php echo $row->aff_login; ?></strong></a><div class="wafp-row-actions"><a href="<?php echo $clicks_url; ?>"><?php _e('Clicks', 'affiliate-royale', 'easy-affiliate'); ?></a> | <a href="<?php echo $txns_url; ?>"><?php _e('Sales', 'affiliate-royale', 'easy-affiliate'); ?></a></div></td>
    <td><?php echo "{$first_name} {$last_name}"; ?></td>
    <td><?php echo $status; ?></td>
    <?php if( $paypal_on ): ?>
      <td><?php echo ( !$paypal_email or empty($paypal_email) )?__("none", 'affiliate-royale', 'easy-affiliate'):$paypal_email; ?></td>
    <?php endif; ?>
    <td><?php echo WafpAppHelper::format_currency( (float)$row->commission_amount - (float)$row->payment_amount ); ?></td>
    <td><?php echo $correction; ?></td>
    <td><?php echo WafpAppHelper::format_currency( (float)($total)); ?></td>
    <td><input type="hidden" name="wafp-payment-amount[<?php echo $row->aff_id; ?>]" value="<?php echo WafpUtils::format_float( (float)($total)); ?>" /><input type="checkbox" name="wafp-payment-paid[<?php echo $row->aff_id; ?>]" /></td>
  </tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
<p class="wafp-trans-submit-wrap">
<input type="submit" class="wafp-trans-submit button-primary" value="<?php _e('Mark Checked Commissions as Paid', 'affiliate-royale', 'easy-affiliate'); ?>" name="submit" />
</p>
</form>
<?php
if(isset($prev_page))
{
  ?>
<span style="float: right;"><a href="javascript:wafp_view_admin_affiliate_page('admin_affiliate_payments',<?php echo $period; ?>,<?php echo $prev_page; ?>);"><?php _e('Previous Payments', 'affiliate-royale', 'easy-affiliate'); ?></a>&nbsp;&raquo;</span>
  <?php
}

if(isset($next_page))
{
  ?>
<span>&laquo;&nbsp;<a href="javascript:wafp_view_admin_affiliate_page('admin_affiliate_payments',<?php echo $period; ?>,<?php echo $next_page; ?>);"><?php _e('Next Payments', 'affiliate-royale', 'easy-affiliate'); ?></a></span>
  <?php
}
?>
</div>
