<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<h3><?php _e('Affiliate Settings', 'affiliate-royale', 'easy-affiliate'); ?></h3>
<?php
if($wafp_options->show_address_fields and $is_affiliate)
{
?>
  <table class="form-table">
    <tr>
      <th><label for="<?php echo WafpUser::$address_one_str; ?>"><?php _e('Address 1', 'affiliate-royale', 'easy-affiliate'); ?></label></th>
      <td><input type="text" name="<?php echo WafpUser::$address_one_str; ?>" id="<?php echo WafpUser::$address_one_str; ?>" class="regular-text" value="<?php echo $address_one; ?>" /></td>
    </tr>
    <tr>
      <th><label for="<?php echo WafpUser::$address_two_str; ?>"><?php _e('Address 2', 'affiliate-royale', 'easy-affiliate'); ?></label></th>
      <td><input type="text" name="<?php echo WafpUser::$address_two_str; ?>" id="<?php echo WafpUser::$address_two_str; ?>" class="regular-text" value="<?php echo $address_two; ?>" /></td>
    </tr>
    <tr>
      <th><label for="<?php echo WafpUser::$city_str; ?>"><?php _e('City', 'affiliate-royale', 'easy-affiliate'); ?></label></th>
      <td><input type="text" name="<?php echo WafpUser::$city_str; ?>" id="<?php echo WafpUser::$city_str; ?>" class="regular-text" value="<?php echo $city; ?>" /></td>
    </tr>
    <tr>
      <th><label for="<?php echo WafpUser::$state_str; ?>"><?php _e('State', 'affiliate-royale', 'easy-affiliate'); ?></label></th>
      <td><input type="text" name="<?php echo WafpUser::$state_str; ?>" id="<?php echo WafpUser::$state_str; ?>" class="regular-text" value="<?php echo $state; ?>" /></td>
    </tr>
    <tr>
      <th><label for="<?php echo WafpUser::$zip_str; ?>"><?php _e('Zip', 'affiliate-royale', 'easy-affiliate'); ?></label></th>
      <td><input type="text" name="<?php echo WafpUser::$zip_str; ?>" id="<?php echo WafpUser::$zip_str; ?>" class="regular-text" value="<?php echo $zip; ?>" /></td>
    </tr>
    <tr>
      <th><label for="<?php echo WafpUser::$country_str; ?>"><?php _e('Country', 'affiliate-royale', 'easy-affiliate'); ?></label></th>
      <td><input type="text" name="<?php echo WafpUser::$country_str; ?>" id="<?php echo WafpUser::$country_str; ?>" class="regular-text" value="<?php echo $country; ?>" /></td>
    </tr>
  </table>
  <?php
}
  ?>
<?php
  if ($wafp_options->show_tax_id_fields && $is_affiliate) {
?>
  <table class="form-table">
    <tr>
      <th><label for="<?php echo WafpUser::$tax_id_us_str; ?>"><?php _e('SSN / Tax ID', 'affiliate-royale', 'easy-affiliate'); ?></label></th>
      <td><input type="text" name="<?php echo WafpUser::$tax_id_us_str; ?>" id="<?php echo WafpUser::$tax_id_us_str; ?>" class="regular-text" value="<?php echo $tax_id_us; ?>" /></td>
    </tr>
    <tr>
      <th><label for="<?php echo WafpUser::$tax_id_int_str; ?>"><?php _e('Int\'l Tax ID', 'affiliate-royale', 'easy-affiliate'); ?></label></th>
      <td><input type="text" name="<?php echo WafpUser::$tax_id_int_str; ?>" id="<?php echo WafpUser::$tax_id_int_str; ?>" class="regular-text" value="<?php echo $tax_id_int; ?>" /></td>
    </tr>
  </table>
<?php
  }
?>
<table class="form-table">
  <tr>
    <th><?php _e('Affiliate Referrer', 'affiliate-royale', 'easy-affiliate'); ?> <span class="description"><?php _e('(login name)', 'affiliate-royale', 'easy-affiliate'); ?></span></th>
    <td><input type="text" name="<?php echo WafpUser::$referrer_str ?>" id="<?php echo WafpUser::$referrer_str ?>" class="regular-text" value="<?php echo $affiliate ? $affiliate->get_field('user_login') : ''; ?>" /></td>
  </tr>
  <tr>
    <th><label for="<?php echo WafpUser::$is_affiliate_str; ?>"><?php _e('User is an Affiliate', 'affiliate-royale', 'easy-affiliate'); ?></label></th>
    <td><input type="checkbox" name="<?php echo WafpUser::$is_affiliate_str; ?>" id="<?php echo WafpUser::$is_affiliate_str; ?>"<?php checked($is_affiliate); ?> />&nbsp;<?php _e('Is this user an Affiliate?', 'affiliate-royale', 'easy-affiliate'); ?></td>
  </tr>
  <tr>
    <th><label for="<?php echo WafpUser::$is_blocked_str; ?>"><?php _e('User is Blocked', 'affiliate-royale', 'easy-affiliate'); ?></label></th>
    <td>
      <input type="checkbox" name="<?php echo WafpUser::$is_blocked_str; ?>" id="<?php echo WafpUser::$is_blocked_str; ?>"<?php checked($is_blocked); ?> />
      <?php _e('Is this user blocked?', 'affiliate-royale', 'easy-affiliate'); ?>
      <div id="wafp-is-blocked-textarea" class="wafp-hidden">
        <h4><?php _e('Affiliate Blocked Message:', 'affiliate-royale', 'easy-affiliate'); ?></h4>
        <?php wp_editor( $blocked_message, WafpUser::$blocked_message_str ); ?>
      </div>
    </td>
  </tr>
  <tr>
    <th><label for="wafp_override_enabled"><?php _e('Commission Override', 'affiliate-royale', 'easy-affiliate'); ?></label></th>
    <td><input type="checkbox" name="wafp_override_enabled" id="wafp_override_enabled"<?php checked($commission_override_enabled); ?> />&nbsp;<?php _e('Enable Commission Override for this User.', 'affiliate-royale', 'easy-affiliate'); ?></td>
  </tr>
  <tr>
    <td colspan="2">
      <?php WafpAppHelper::display_affiliate_commissions($user->get_id()); ?>
      <div id="wafp_override_pane" class="wafp-options-pane wafp-hidden">
        <div class="commission_type">
          <p><strong><?php _e('Commission Type:', 'affiliate-royale', 'easy-affiliate'); ?></strong></p>
          <select name="wafp_commission_type" id="wafp_commission_type">
            <option value="percentage"<?php selected('percentage',$commission_type); ?>><?php _e("Percentages", 'affiliate-royale', 'easy-affiliate'); ?></option>
            <option value="fixed"<?php selected('fixed',$commission_type); ?>><?php _e("Fixed Amounts", 'affiliate-royale', 'easy-affiliate'); ?></option>
          </select>
        </div>
        <br/>
        <p><strong><?php _e('Commission Levels:', 'affiliate-royale', 'easy-affiliate'); ?></strong></p>
        <ol id="wafp_commissions" data-currency="<?php echo $wafp_options->currency_symbol; ?>"></ol>
        <a href="#" id="wafp_add_user_commission_level" class="button" ><?php _e('add level', 'affiliate-royale', 'easy-affiliate'); ?></a>
        <a href="#" id="wafp_remove_user_commission_level" class="button wafp-hidden"><?php _e('remove level', 'affiliate-royale', 'easy-affiliate'); ?></a>
        <textarea name="wafp_commissions_json" id="wafp_commissions_json" class="wafp-hidden"><?php echo json_encode($commissions); ?></textarea>
        <br/>
        <br/>
        <p><strong><?php _e('Recurring Commissions:', 'affiliate-royale', 'easy-affiliate'); ?></strong></p>
        <p>
          <input type="checkbox" name="wafp_recurring" id="wafp_recurring" <?php checked($recurring); ?> />
          <label for="wafp_recurring"><?php _e('Pay Recurring Commissions', 'affiliate-royale', 'easy-affiliate'); ?></label>
          <?php /* MeprAppHelper::info_tooltip('wafp-recurring',
                                            __('Pay Recurring Commissions', 'affiliate-royale'),
                                            __('If checked, commissions will be paid on the first and all recurring transactions within a subscription. If unchecked, commissions will only be paid on the first transaction of a subscription.','affiliate-royale')); */ ?>
        </p>
      </div>
    </td>
  </tr>
</table>
<table class="form-table">
  <tr><td><a class="button wafp-resend-welcome-email" href="javascript:" user-id="<?php echo $user->get_id(); ?>" wafp-nonce="<?php echo wp_create_nonce('wafp-resend-welcome-email'); ?>"><?php _e('Resend Affiliate Program Welcome Email', 'affiliate-royale', 'easy-affiliate'); ?></a>&nbsp;&nbsp;<img src="<?php echo admin_url('images/loading.gif'); ?>" alt="<?php _e('Loading...', 'affiliate-royale', 'easy-affiliate'); ?>" class="wafp-resend-welcome-email-loader" />&nbsp;&nbsp;<span class="wafp-resend-welcome-email-message">&nbsp;</span></td></tr>
</table>
