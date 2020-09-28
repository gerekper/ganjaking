<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<tr>
<td colspan="2">

<h3><?php _e('Corporate Accounts', 'memberpress-corporate'); ?></h3>

  <?php
  foreach($subscriptions as $sub):

    $obj_type = MPCA_Corporate_Account::get_obj_type($sub);

    // Hide sub accounts
    if(MPCA_Corporate_Account::is_obj_sub_account( $sub->id, $obj_type )) continue;

    $i = uniqid();
    $product = $sub->product();
    ?>
<table class="form-table">
  <input type="hidden" name="mpca[<?php echo $i ?>][obj_type]" value="<?php echo $obj_type ?>" />
  <input type="hidden" name="mpca[<?php echo $i ?>][obj_id]" value="<?php echo $sub->id ?>" />

  <tr>
    <th><?php _e('Corporate Account?', 'memberpress-corporate'); ?></th>
    <td>
      <label>
        <input type="checkbox" id="" class="mepr-toggle-checkbox" data-box="mepr_corporate_options_box_<?php echo $i; ?>" name="mpca[<?php echo $i ?>][is_corporate]"
        <?php checked($sub->is_corporate_account); ?> /> <?php echo $helper->subscription_header_html($sub); ?>
      </label>
    </td>
  </tr>
</table>

<div id="" class="mepr-sub-box-white mepr_corporate_options_box_<?php echo $i; ?>">
  <div class="mepr-arrow mepr-white mepr-up mepr-sub-box-arrow"></div>
  <table class="form-table">
    <tr id="mpca-sub-account-limit-row">
      <th id='myheading'><?php _e('Max Sub Accounts', 'memberpress-corporate'); ?></th>
      <td>
        <input
          id="mpca-num-sub-account"
          type="number"
          name="mpca[<?php echo $i; ?>][num_sub_accounts]"
          min="0"
          value=<?php echo $sub->num_sub_accounts ?>
        />
      </td>
    </tr>
    <?php
      if($sub->is_corporate_account) {
        ?>
          <tr>
            <th><?php _e('Sub Account Usage', 'memberpress-corporate'); ?></th>
            <td>
              <?php
                printf(
                  __('%1$s of %2$s Sub Accounts Used', 'memberpress-corporate'),
                  $sub->corporate_account->num_sub_accounts_used(),
                  $sub->corporate_account->num_sub_accounts
                );
              ?>
            </td>
          </tr>
          <tr>
            <th><?php _e('Actions', 'memberpress-corporate'); ?></th>
            <td>
              <a href="<?php echo $sub->corporate_account->sub_account_management_url(); ?>" class="button"><?php _e('Manage Sub Accounts', 'memberpress-corporate');?></a>
              <a href="<?php echo $sub->corporate_account->import_url(); ?>" class="button"><?php _e('Import Sub Accounts', 'memberpress-corporate');?></a>
              <a href="<?php echo $sub->corporate_account->export_url(); ?>" class="button"><?php _e('Export Sub Accounts', 'memberpress-corporate');?></a>
            </td>
          </tr>
        <?php
      }
    ?>
  </table>
</div>

<?php endforeach; ?>

</td>
</tr>
