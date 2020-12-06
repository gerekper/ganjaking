<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div class="wafp-memberpress-meta-box">
  <input type="checkbox" name="wafp_enable_commission_group" id="wafp_enable_commission_group" <?php checked($commission_groups_enabled); ?> />
  <label for="wafp_enable_commission_group"><?php _e('Enable Commission Group', 'affiliate-royale', 'easy-affiliate'); ?></label>
  <?php /* MeprAppHelper::info_tooltip('wafp-product-enable-commission-group',
                                    __('Enable Commission Group for this Product', 'affiliate-royale'),
                                    __('If enabled, purchasers of this product will be added to your Affiliate Royale affiliate program and will be enrolled in a special commission group set here.<br/><br/>Commissions for these affiliates will be calculated based on this commission structure.', 'affiliate-royale')); */ ?>
  <div id="wafp_commission_group" class="mepr-options-pane mepr_hidden">
    <div class="commission_type">
      <p><strong><?php _e('Commission Type:', 'affiliate-royale', 'easy-affiliate'); ?></strong></p>
      <select name="wafp_commission_type" id="wafp_commission_type">
        <option value="percentage"<?php selected('percentage',$commission_type); ?>><?php _e("Percentages", 'affiliate-royale', 'easy-affiliate'); ?></option>
        <option value="fixed"<?php selected('fixed',$commission_type); ?>><?php _e("Fixed Amounts", 'affiliate-royale', 'easy-affiliate'); ?></option>
      </select>
    </div>
    <p><strong><?php _e('Commission Levels:', 'affiliate-royale', 'easy-affiliate'); ?></strong></p>
    <ol id="wafp_commissions" data-currency="<?php echo $mepr_options->currency_symbol; //Should probably use Wafp currency symbol here instead ? ?>"></ol>
    <a href="#" id="wafp_add_product_commission_level" class="button" ><?php _e('add level', 'affiliate-royale', 'easy-affiliate'); ?></a>
    <a href="#" id="wafp_remove_product_commission_level" class="button mepr_hidden"><?php _e('remove level', 'affiliate-royale', 'easy-affiliate'); ?></a>
    <textarea name="wafp_commissions_json" id="wafp_commissions_json" class="mepr_hidden"><?php echo json_encode($commissions); ?></textarea>

    <p><strong><?php _e('Recurring Commissions:', 'affiliate-royale', 'easy-affiliate'); ?></strong></p>
    <p>
      <input type="checkbox" name="wafp_recurring" id="wafp_recurring" <?php checked($recurring); ?> />
      <label for="wafp_recurring"><?php _e('Pay Recurring Commissions', 'affiliate-royale', 'easy-affiliate'); ?></label>
      <?php /* MeprAppHelper::info_tooltip('wafp-recurring',
                                        __('Pay Recurring Commissions', 'affiliate-royale'),
                                        __('If checked, commissions will be paid on the first and all recurring transactions within a subscription. If unchecked, commissions will only be paid on the first transaction of a subscription.','affiliate-royale')); */ ?>
    </p>
  </div>
</div>
