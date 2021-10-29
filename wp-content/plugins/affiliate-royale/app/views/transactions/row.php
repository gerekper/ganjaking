<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

if(!empty($records)) {
  $row_index = 0;
  foreach($records as $rec) {
    $alternate = ( $row_index++ % 2 ? '' : 'alternate' );

    //Open the line
    ?>
    <tr id="record_<?php echo $rec->id; ?>" class="<?php echo $alternate; ?>">
    <?php
    foreach( $columns as $column_name => $column_display_name ) {
      //Style attributes for each col
      $class = "class='$column_name column-$column_name'";
      $style = "";
      if( in_array( $column_name, $hidden ) ) $style = ' style="display:none;"';
      $attributes = $class . $style;

      $editlink = admin_url( 'user-edit.php?user_id=' . (int)$rec->affiliate_id );

      //Display the cell
      switch( $column_name ) {
        case 'col_created_at':
          ?>
          <td><?php echo $rec->created_at; ?></td>
          <?php
          break;
        case 'col_user_login':
          ?>
          <td><a href="<?php echo $editlink; ?>"><?php echo $rec->user_login; ?></a></td>
          <?php
          break;
        case 'col_item_name':
          ?>
          <td><?php echo $rec->item_name; ?></td>
          <?php
          break;
        case 'col_trans_num':
          ?>
          <td><?php echo apply_filters('wafp-invoice-num',$rec->trans_num); ?></td>
          <?php
          break;
        case 'col_sale_amount':
          ?>
          <td><?php echo WafpAppHelper::format_currency( (float)$rec->sale_amount); ?></td>
          <?php
          break;
        case 'col_refund_amount':
          ?>
          <td><?php echo WafpAppHelper::format_currency( (float)$rec->refund_amount ); ?></td>
          <?php
          break;
        case 'col_total_amount':
          ?>
          <td><?php echo WafpAppHelper::format_currency( (float)$rec->total_amount ); ?></td>
          <?php
          break;
        case 'col_referring_page':
          ?>
          <td><?php echo empty($rec->referring_page)?'':"<a href=\"{$rec->referring_page}\" target=\"_blank\">{$rec->referring_page}</a>"; ?></td>
          <?php
          break;
        case 'col_commission_amount':
          ?>
          <td><?php echo WafpAppHelper::format_currency( (float)$rec->commission_amount ); ?></td>
          <?php
          break;
        case 'col_actions':
          ?>
          <td>
            <a href="#" class="wafp-toggle-link" data-id="commissions_<?php echo $rec->id; ?>"><?php _e('Info','affiliate-royale', 'easy-affiliate'); ?></a>&nbsp;|&nbsp;<a href="<?php echo admin_url("admin.php?page=affiliate-royale-transactions&action=edit&id={$rec->id}"); ?>"><?php _e('Edit','affiliate-royale', 'easy-affiliate'); ?></a>&nbsp;|&nbsp;<a href="#" class="wafp_del_txn" data-id="<?php echo $rec->id; ?>" title="<?php _e('Delete this Transaction.', 'affiliate-royale', 'easy-affiliate'); ?>"><?php _e('Delete','affiliate-royale', 'easy-affiliate'); ?></a>
          </td>
          <?php
          break;
      }
    }

    ?>
    </tr>
    <tr id="commissions_<?php echo $rec->id; ?>" class="wafp-hidden">
      <td colspan="<?php echo count($columns); ?>">
        <div class="wafp-commissions-table">
          <p><b><?php _e('Commission Payouts', 'affiliate-royale', 'easy-affiliate'); ?></b></p>
          <table class="wp-list-table widefat fixed wp_list_wafp_transactions">
            <thead>
              <tr>
                <th scope="col" class="manage-column"><?php _e('Level','affiliate-royale', 'easy-affiliate'); ?></th>
                <th scope="col" class="manage-column"><?php _e('Affiliate','affiliate-royale', 'easy-affiliate'); ?></th>
                <th scope="col" class="manage-column"><?php _e('Commissions','affiliate-royale', 'easy-affiliate'); ?></th>
                <th scope="col" class="manage-column"><?php _e('Commission Amount','affiliate-royale', 'easy-affiliate'); ?></th>
                <th scope="col" class="manage-column"><?php _e('Correction Amount','affiliate-royale', 'easy-affiliate'); ?></th>
              </tr>
            </thead>
            <tbody>
            <?php

            $commissions = WafpCommission::get_all_by_transaction_id( $rec->id, 'commission_level' );
            foreach($commissions as $commish):
              $aff = new WafpUser($commish->affiliate_id);
              ?>
              <tr>
                <td scope="col" class="manage-column"><?php echo $commish->commission_level + 1; ?></td>
                <td scope="col" class="manage-column"><a href="<?php echo admin_url("user-edit.php?user_id={$commish->affiliate_id}", 'relative'); ?>"><?php echo $aff->get_full_name() . ' (' . $aff->get_field('user_login') . ')'; ?></a></td>
                <td scope="col" class="manage-column"><?php echo ( $commish->commission_type == 'fixed' ? WafpAppHelper::format_currency($commish->commission_percentage) : WafpUtils::format_float($commish->commission_percentage) . '%' ); ?></td>
                <td scope="col" class="manage-column"><?php echo WafpAppHelper::format_currency($commish->commission_amount); ?></td>
                <td scope="col" class="manage-column"><?php echo WafpAppHelper::format_currency($commish->correction_amount); ?></td>
              </tr>
              <?php
            endforeach;
            ?>
            </tbody>
          </table>
          <br/>
        </div>
      </td>
    </tr>
    <?php
  }
}
