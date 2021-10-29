<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div class="esaf-page-title"><?php _e('International Settings', 'affiliate-royale', 'easy-affiliate'); ?></div>
<table class="form-table">
  <tbody>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $wafp_options->currency_code_str; ?>"><?php _e('Currency Code', 'easy-affiliate', 'affiliate-royale'); ?></label>
      </th>
      <td>
        <?php WafpOptionsHelper::payment_currency_code_dropdown($wafp_options->currency_code_str, $wafp_options->currency_code); ?>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $wafp_options->currency_symbol_str; ?>"><?php _e('Currency symbol', 'easy-affiliate', 'affiliate-royale'); ?></label>
      </th>
      <td>
        <?php WafpOptionsHelper::payment_currencies_dropdown($wafp_options->currency_symbol_str, $wafp_options->currency_symbol); ?>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $wafp_options->number_format_str; ?>"><?php _e('Currency Format', 'easy-affiliate', 'affiliate-royale'); ?></label>
      </th>
      <td>
        <?php WafpOptionsHelper::payment_format_dropdown($wafp_options->number_format_str, $wafp_options->number_format); ?>
      </td>
    </tr>
  </tbody>
</table>
