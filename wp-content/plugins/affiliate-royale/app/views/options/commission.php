<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div class="esaf-page-title"><?php _e('Commission Settings', 'affiliate-royale', 'easy-affiliate'); ?></div>
<table class="form-table">
  <tbody>
    <tr valign="top">
      <th scope="row">
        <label for="wafp_commission_type"><?php _e('Commission Type', 'affiliate-royale', 'easy-affiliate') ?></label>
        <?php WafpAppHelper::info_tooltip(
          'esaf-options-commission-type',
          __('Affiliate Commission Type', 'pretty-link', 'easy-affiliate', 'affiliate-royale'),
          __('Base commissions on fixed amounts or on percentages of sales.', 'affiliate-royale', 'easy-affiliate')
        );
        ?>
      </th>
      <td>
        <select name="<?php echo $wafp_options->commission_type_str; ?>" id="wafp_commission_type">
          <option value="percentage"<?php selected('percentage',$wafp_options->commission_type); ?>><?php _e("Percentages", 'affiliate-royale', 'easy-affiliate'); ?></option>
          <option value="fixed"<?php selected('fixed',$wafp_options->commission_type); ?>><?php _e("Fixed Amounts", 'affiliate-royale', 'easy-affiliate'); ?></option>
        </select>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="wafp_commission_levels"><?php _e('Commission Levels', 'affiliate-royale', 'easy-affiliate') ?></label>
        <?php WafpAppHelper::info_tooltip(
          'esaf-options-commission-levels',
          __('Affiliate Commission', 'pretty-link', 'easy-affiliate', 'affiliate-royale'),
          __('Configure what percentage or fixed amount you want to pay your affiliates per sale.', 'affiliate-royale', 'easy-affiliate')
        );
        ?>
      </th>
      <td>
        <ul id="wafp_commission_levels">
          <?php foreach( $wafp_options->commission as $index => $commish ) {
            $level = $index + 1;
            ?>
            <li><?php printf(__('Level %d:', 'affiliate-royale', 'easy-affiliate'),$level); ?> <span class="wafp_commission_currency_symbol"><?php echo $wafp_options->currency_symbol; ?></span><input id="<?php echo $wafp_options->commission_str; ?>_<?php echo $level; ?>" class="form-field" size="6" value="<?php echo WafpUtils::format_float($commish); ?>" name="<?php echo $wafp_options->commission_str; ?>[]"><span class="wafp_commission_percentage_symbol">%</span></li>
          <?php } ?>
        </ul>

        <a href="javascript:" id="wafp_add_commission_level" class="button"><?php _e('add level', 'affiliate-royale', 'easy-affiliate'); ?></a><span id="wafp_remove_commission_level" class="wafp-hidden">&nbsp;<a href="javascript:" class="button"><?php _e('remove level', 'affiliate-royale', 'easy-affiliate'); ?></a></span>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $wafp_options->recurring_str; ?>">
          <?php
            _e('Recurring Commissions','affiliate-royale', 'easy-affiliate');
            WafpAppHelper::info_tooltip(
              'esaf-options-recurring-commissions',
              __('Recurring Commissions', 'pretty-link', 'easy-affiliate', 'affiliate-royale'),
              __('Pay commissions on recurring transactions. If checked, Easy Affiliate will record commisssions on recurring rebill transactions -- otherwise it will only record commissions on the first transaction of a subscription.', 'affiliate-royale', 'easy-affiliate')
            );
          ?>
        </label>
      </th>
      <td>
        <input type="checkbox" name="<?php echo $wafp_options->recurring_str; ?>" id="<?php echo $wafp_options->recurring_str; ?>" <?php checked($wafp_options->recurring); ?> />
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $wafp_options->minimum_str; ?>"><?php _e('Require Minimum Payout','affiliate-royale', 'easy-affiliate'); ?></label>
        <?php
          WafpAppHelper::info_tooltip(
            'esaf-options-minimum-payout',
            __('Require Minimum Payout', 'pretty-link', 'easy-affiliate', 'affiliate-royale'),
            __('If this box is checked, an affiliate won\'t be elligible to get commissions until they\'ve acquired a minimum amount of commissions', 'affiliate-royale', 'easy-affiliate')
          );
        ?>
      </th>
      <td>
        <input type="checkbox" name="<?php echo $wafp_options->minimum_str; ?>-checkbox" id="<?php echo $wafp_options->minimum_str; ?>-checkbox" class="esaf-toggle-checkbox" data-box="esaf-options-minimum-payout-box" <?php checked(($wafp_options->minimum > 0.00)); ?> />
      </td>
    </tr>
  </tbody>
</table>

<div class="esaf-sub-box esaf-options-minimum-payout-box">
  <div class="esaf-arrow esaf-gray esaf-up esaf-sub-box-arrow"> </div>
  <table class="form-table">
    <tbody>
      <tr valign="top">
        <th scope="row">
          <label for="<?php echo $wafp_options->minimum_str; ?>"><?php _e('Minimum Payout', 'easy-affiliate', 'affiliate-royale'); ?></label>
        </th>
        <td>
          <input class="form-field regular-text" type="text" id="<?php echo $wafp_options->minimum_str; ?>" name="<?php echo $wafp_options->minimum_str; ?>" value="<?php echo $wafp_options->minimum; ?>" />
        </td>
      </tr>
    </tbody>
  </table>
</div>

