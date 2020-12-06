<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<div class="esaf-page-title"><?php _e('Affiliate Settings', 'affiliate-royale', 'easy-affiliate'); ?></div>
<table class="form-table">
  <tbody>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $wafp_options->payment_type_str; ?>"><?php _e('Payment Method', 'affiliate-royale', 'easy-affiliate'); ?></label>
        <?php
          WafpAppHelper::info_tooltip(
            'esaf-options-payment-method',
            __('Pay Affiliates With?', 'pretty-link', 'easy-affiliate', 'affiliate-royale'),
            __('What method will you use to pay your affiliates?', 'affiliate-royale', 'easy-affiliate')
          );
        ?>
      </th>
      <td>
        <?php WafpOptionsHelper::payment_types_dropdown( $wafp_options->payment_type_str, $wafp_options->payment_type ); ?>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $wafp_options->show_address_fields_str; ?>"><?php _e('Show Address Fields', 'affiliate-royale', 'easy-affiliate'); ?></label>
        <?php
          WafpAppHelper::info_tooltip(
            'esaf-options-address-info',
            __('Collect Address Info?', 'pretty-link', 'easy-affiliate', 'affiliate-royale'),
            __('Collect address information from your affiliates?', 'affiliate-royale', 'easy-affiliate')
          );
        ?>
      </th>
      <td>
        <input type="checkbox" name="<?php echo $wafp_options->show_address_fields_str; ?>" id="<?php echo $wafp_options->show_address_fields_str; ?>" <?php checked($wafp_options->show_address_fields); ?> />
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $wafp_options->show_tax_id_fields_str; ?>"><?php _e('Show Tax ID Field', 'affiliate-royale', 'easy-affiliate'); ?></label>
        <?php
          WafpAppHelper::info_tooltip(
            'esaf-options-tax-id',
            __('Collect Tax ID?', 'affiliate-royale', 'easy-affiliate'),
            __('Collect Tax ID #\'s from your affiliates?', 'affiliate-royale', 'easy-affiliate')
          );
        ?>
      </th>
      <td>
        <input type="checkbox" name="<?php echo $wafp_options->show_tax_id_fields_str; ?>" id="<?php echo $wafp_options->show_tax_id_fields_str; ?>"<?php checked($wafp_options->show_tax_id_fields); ?>/>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $wafp_options->force_account_info_str; ?>"><?php _e('Force Required Fields', 'affiliate-royale', 'easy-affiliate'); ?></label>
        <?php
          WafpAppHelper::info_tooltip(
            'esaf-options-force-required',
            __('Force Required', 'affiliate-royale', 'easy-affiliate'),
            __('Force required fields to be completed before promoting?', 'affiliate-royale', 'easy-affiliate')
          );
        ?>
      </th>
      <td>
        <input type="checkbox" name="<?php echo $wafp_options->force_account_info_str; ?>" id="<?php echo $wafp_options->force_account_info_str; ?>"<?php checked($wafp_options->force_account_info); ?> />
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $wafp_options->make_new_users_affiliates_str; ?>"><?php _e('Auto-Add Users', 'affiliate-royale', 'easy-affiliate'); ?></label>
        <?php
          WafpAppHelper::info_tooltip(
            'esaf-options-auto-add-users',
            __('Auto-Add Users as Affiliates', 'affiliate-royale', 'easy-affiliate'),
            __('Automatically make each new user an Affiliate?', 'affiliate-royale', 'easy-affiliate')
          );
        ?>
      </th>
      <td>
        <input type="checkbox" name="<?php echo $wafp_options->make_new_users_affiliates_str; ?>" id="<?php echo $wafp_options->make_new_users_affiliates_str; ?>"<?php checked($wafp_options->make_new_users_affiliates); ?> />
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $wafp_options->affiliate_agreement_enabled_str; ?>"><?php _e('Show Affiliate Agreement', 'affiliate-royale', 'easy-affiliate'); ?></label>
        <?php
          WafpAppHelper::info_tooltip(
            'esaf-options-affiliate-agreement',
            __('Show Affiliate Agreement', 'affiliate-royale', 'easy-affiliate'),
            __('Enable The Affiliate Signup Agreement', 'affiliate-royale', 'easy-affiliate')
          );
        ?>
      </th>
      <td>
        <input type="checkbox" name="<?php echo $wafp_options->affiliate_agreement_enabled_str; ?>" id="<?php echo $wafp_options->affiliate_agreement_enabled_str; ?>" class="esaf-toggle-checkbox" data-box="esaf-options-affiliate-agreement-box" <?php checked($wafp_options->affiliate_agreement_enabled); ?> />
      </td>
    </tr>
  </tbody>
</table>
<div class="esaf-sub-box esaf-options-affiliate-agreement-box">
  <div class="esaf-arrow esaf-gray esaf-up esaf-sub-box-arrow"> </div>
  <table class="form-table">
    <tbody>
      <tr valign="top">
        <th scope="row">
          <label for="<?php echo $wafp_options->affiliate_agreement_text_str; ?>"><?php _e('Affiliate Agreement', 'easy-affiliate', 'affiliate-royale'); ?></label>
        </th>
        <td>
          <textarea name="<?php echo $wafp_options->affiliate_agreement_text_str; ?>" id="<?php echo $wafp_options->affiliate_agreement_text_str; ?>" class="large-text" style="min-height:150px;"><?php echo $wafp_options->affiliate_agreement_text; ?></textarea>
        </td>
      </tr>
    </tbody>
  </table>
</div>
<table class="form-table">
  <tbody>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $wafp_options->expire_after_days_str; ?>"><?php _e('Expire Cookie', 'affiliate-royale', 'easy-affiliate'); ?></label>
        <?php
          WafpAppHelper::info_tooltip(
            'esaf-options-expire-cookie',
            __('Expire Cookie', 'affiliate-royale', 'easy-affiliate'),
            __('The length of time that you\'ll allow before the affiliate cookie expires', 'affiliate-royale', 'easy-affiliate')
          );
        ?>
      </th>
      <td>
        <input class="form-field" id="<?php echo $wafp_options->expire_after_days_str; ?>" name="<?php echo $wafp_options->expire_after_days_str; ?>" value="<?php echo $wafp_options->expire_after_days; ?>" size="6" />&nbsp;<?php _e('Days', 'affiliate-royale', 'easy-affiliate'); ?>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $wafp_options->pretty_affiliate_links_str; ?>"><?php _e('Pretty Affiliate Links', 'affiliate-royale', 'easy-affiliate'); ?></label>
        <?php
          WafpAppHelper::info_tooltip(
            'esaf-options-pretty-affiliate-links',
            __('Affiliate Link Settings', 'affiliate-royale', 'easy-affiliate'),
            __('Enable Pretty Affiliate Links', 'affiliate-royale', 'easy-affiliate')
          );
        ?>
      </th>
      <td>
        <input type="checkbox" name="<?php echo $wafp_options->pretty_affiliate_links_str; ?>" id="<?php echo $wafp_options->pretty_affiliate_links_str; ?>"<?php checked($wafp_options->pretty_affiliate_links); ?> />
      </td>
    </tr>
  </tbody>
</table>

