<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<div id="wafp-admin-affiliate-panel">
<p><?php _e('Select the period you want to view', 'affiliate-royale', 'easy-affiliate'); ?>:<br/><?php WafpReportsHelper::periods_dropdown('wafp-report-period', $period, 'javascript:wafp_view_admin_affiliate_page( \'admin_affiliate_top\', this.value, ' . $page . ', \'\', true);'); ?>&nbsp;&nbsp;<img src="<?php echo admin_url('images/loading.gif'); ?>" alt="<?php _e('Loading...', 'affiliate-royale', 'easy-affiliate'); ?>" style="display: none;" class="wafp-stats-loader" /></p>
<table class="widefat post fixed wafp-table" cellspacing="0">
<thead>
  <tr>
    <th class="manage-column wafp-affiliate-column"><?php _e('Affiliate', 'affiliate-royale', 'easy-affiliate'); ?></th>
    <th class="manage-column wafp-clicks-column"><?php _e('Clicks', 'affiliate-royale', 'easy-affiliate'); ?></th>
    <th class="manage-column wafp-transaction-column"><?php _e('Transactions', 'affiliate-royale', 'easy-affiliate'); ?></th>
    <th class="manage-column wafp-sales-column"><?php _e('Sales', 'affiliate-royale', 'easy-affiliate'); ?></th>
    <th class="manage-column wafp-refunds-column"><?php _e('Refunds', 'affiliate-royale', 'easy-affiliate'); ?></th>
    <th class="manage-column wafp-total-column"><?php _e('Total', 'affiliate-royale', 'easy-affiliate'); ?></th>
  </tr>
</thead>
<tbody>
<?php
  $row_index = 0;
  foreach($top_affiliates as $row)
  {
    $total_amount = (float)$row->sales_amount - (float)$row->refund_amount;
    $alternate = ( $row_index++ % 2 ? '' : 'alternate' );
  ?>
<tr class="<?php echo $alternate; ?>">
  <td><?php echo $row->aff_login; ?></td>
  <td><?php echo $row->click_count; ?></td>
  <td><?php echo $row->transaction_count; ?></td>
  <td><?php echo WafpAppHelper::format_currency( (float)$row->sales_amount ); ?></td>
  <td><?php echo WafpAppHelper::format_currency( (float)$row->refund_amount ); ?></td>
  <td><?php echo WafpAppHelper::format_currency( (float)$total_amount ); ?></td>
</tr>
<?php } ?>
</tbody>
</table>
<?php
if($prev_page)
{
  ?>
<span style="float: right;"><a href="javascript:wafp_view_admin_affiliate_page('admin_affiliate_clicks',<?php echo $period; ?>,<?php echo $prev_page; ?>);"><?php _e('Next Page', 'affiliate-royale', 'easy-affiliate'); ?></a>&nbsp;&raquo;</span>
  <?php
}

if($next_page)
{
  ?>
<span>&laquo;&nbsp;<a href="javascript:wafp_view_admin_affiliate_page('admin_affiliate_clicks',<?php echo $period; ?>,<?php echo $next_page; ?>);"><?php _e('Prev Page', 'affiliate-royale', 'easy-affiliate'); ?></a></span>
  <?php
}
?>
</div>
