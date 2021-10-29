<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<tr valign="top">
  <th scope="row"><label for="wafp-affiliate-referrer"><?php _e('Affiliate*:', 'affiliate-royale', 'easy-affiliate'); ?></label></th>
  <td>
    <?php wp_nonce_field( 'affiliate-royale-trans' ); ?>
    <input type="text" name="referrer" id="wafp-affiliate-referrer" class="regular-text" value="<?php echo $referrer; ?>" autocomplete="off">
    <p class="description"><?php _e('The affiliate who referred this transaction.', 'affiliate-royale', 'easy-affiliate'); ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="customer-name"><?php _e('Customer Name (optional):', 'affiliate-royale', 'easy-affiliate'); ?></label></th>
  <td>
    <input type="text" name="customer_name" id="customer-name" class="regular-text" value="<?php echo $customer_name; ?>" autocomplete="off">
    <p class="description"><?php _e('The Customer\'s Full Name. Not required.', 'affiliate-royale', 'easy-affiliate'); ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="customer-email"><?php _e('Customer Email (optional):', 'affiliate-royale', 'easy-affiliate'); ?></label></th>
  <td>
    <input type="text" name="customer_email" id="customer-email" class="regular-text" value="<?php echo $customer_email; ?>" autocomplete="off">
    <p class="description"><?php _e('The Customer\'s Email Address. Not required.', 'affiliate-royale', 'easy-affiliate'); ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="item_name"><?php _e('Product*:', 'affiliate-royale', 'easy-affiliate'); ?></label></th>
  <td>
    <input type="text" name="item_name" id="item_name" value="<?php echo $txn->item_name; ?>" class="regular-text" />
    <p class="description"><?php _e('The product that was purchased', 'affiliate-royale', 'easy-affiliate'); ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="trans_num"><?php _e('Unique Order ID*:', 'affiliate-royale', 'easy-affiliate'); ?></label></th>
  <td>
    <input type="text" name="trans_num" id="trans_num" value="<?php echo $txn->trans_num; ?>" class="regular-text" />
    <p class="description"><?php _e('The unique order id of this transaction.', 'affiliate-royale', 'easy-affiliate'); ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="sale_amount"><?php _e('Amount*:', 'affiliate-royale', 'easy-affiliate'); ?></label></th>
  <td>
    <span><?php echo $wafp_options->currency_symbol; ?></span>
    <input type="text" name="sale_amount" id="sale_amount" value="<?php echo WafpUtils::format_float($txn->sale_amount); ?>" class="regular-text" style="width:95px !important;"/>
    <p class="description"><?php _e('The sale amount of this transaction', 'affiliate-royale', 'easy-affiliate'); ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="refund_amount"><?php _e('Refund Amount*:', 'affiliate-royale', 'easy-affiliate'); ?></label></th>
  <td>
    <span><?php echo $wafp_options->currency_symbol; ?></span>
    <input type="text" name="refund_amount" id="refund_amount" value="<?php echo WafpUtils::format_float($txn->refund_amount); ?>" class="regular-text" style="width:95px !important;"/>
    <p class="description"><?php _e('The refund amount of this transaction', 'affiliate-royale', 'easy-affiliate'); ?></p>
  </td>
</tr>

