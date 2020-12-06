<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<div id="wafp-admin-affiliate-panel" class="wrap">
<?php WafpAppHelper::plugin_title(__('Affiliate Commission Summary','affiliate-royale', 'easy-affiliate')); ?>
<table class="widefat post fixed wafp-table" cellspacing="0">
<thead>
  <tr>
    <th class="manage-column wafp-pay-affiliate-col"><?php _e('Affiliate', 'affiliate-royale', 'easy-affiliate'); ?></th>
    <th class="manage-column wafp-pay-name-col"><?php _e('Name', 'affiliate-royale', 'easy-affiliate'); ?></th>
    <th class="manage-column wafp-pay-paypal-col"><?php _e('PayPal Email', 'affiliate-royale', 'easy-affiliate'); ?></th>
    <th class="manage-column wafp-pay-paid-col"><?php _e('Paid', 'affiliate-royale', 'easy-affiliate'); ?></th>
  </tr>
</thead>
<tbody>
<?php
  foreach( $_POST['wafp-payment-paid'] as $affiliate_id => $value )
  {
    $affiliate    = new WafpUser($affiliate_id);
    $amount       = $_POST['wafp-payment-amount'][$affiliate_id];
    $paypal_email = $affiliate->get_paypal_email();
  ?>
<tr>
  <td><?php echo $affiliate->get_field('user_login'); ?></td>
  <td><?php echo $affiliate->get_first_name() . " " . $affiliate->get_last_name(); ?></td>
  <td><?php echo ( !$paypal_email or empty($paypal_email) )?__("none", 'affiliate-royale', 'easy-affiliate'):$paypal_email; ?></td>
  <td><?php echo WafpAppHelper::format_currency( (float)$amount ); ?></td>
</tr>
<?php } ?>
</tbody>
</table>
<p class="wafp-trans-submit-wrap">
  <a href="<?php echo WAFP_SCRIPT_URL; ?>&controller=payments&action=paypal_bulk_file&id=<?php echo $payment_ids; ?>" class="button-primary" style="float: right;"><?php _e('Download PayPal Mass Payment File', 'affiliate-royale', 'easy-affiliate'); ?></a>
</p>
<?php
if(isset($prev_page))
{
  ?>
<span style="float: right;"><a href="javascript:wafp_view_admin_affiliate_page('admin_affiliate_payments',<?php echo $period; ?>,<?php echo $prev_page; ?>);"><?php _e('Newer Clicks', 'affiliate-royale', 'easy-affiliate'); ?></a>&nbsp;&raquo;</span>
  <?php
}

if(isset($next_page))
{
  ?>
<span>&laquo;&nbsp;<a href="javascript:wafp_view_admin_affiliate_page('admin_affiliate_payments',<?php echo $period; ?>,<?php echo $next_page; ?>);"><?php _e('Older Clicks', 'affiliate-royale', 'easy-affiliate'); ?></a></span>
  <?php
}
?>
</div>
