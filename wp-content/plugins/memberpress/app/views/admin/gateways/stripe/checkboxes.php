<?php

$classes = '';

if ( ! isset( $_GET['display-keys'] ) && ! isset( $_COOKIE['mepr_stripe_display_keys'] ) && ! defined( 'MEPR_DISABLE_STRIPE_CONNECT' ) ) {
  $classes = 'class="mepr-hidden"';
}

?>

<table class="form-table">
  <tbody>
    <tr valign="top">
      <th scope="row"><label for="<?php echo $test_mode_str; ?>"><?php _e('Test Mode', 'memberpress'); ?></label></th>
      <td><input class="mepr-stripe-testmode" data-integration="<?php echo $id; ?>" type="checkbox" name="<?php echo $test_mode_str; ?>"<?php echo checked($test_mode); ?> <?php disabled((defined('MEMBERPRESS_STRIPE_TESTING') && MEMBERPRESS_STRIPE_TESTING == true));?> /></td>
      </td>
    </tr>
    <tr valign="top" <?php echo MeprStripeGateway::is_stripe_connect( $id ) || empty( $live_public_key ) ? 'style="display:none;"' : ''; ?>>
      <th scope="row"><label for="<?php echo $force_ssl_str; ?>"><?php _e('Force SSL', 'memberpress'); ?></label></th>
      <td><input type="checkbox" name="<?php echo $force_ssl_str; ?>"<?php echo checked($force_ssl); ?> /></td>
    </tr>
    <tr valign="top" <?php echo MeprStripeGateway::is_stripe_connect( $id ) || empty( $live_public_key ) ? 'style="display:none;"' : ''; ?>>
      <th scope="row"><label for="<?php echo $debug_str; ?>"><?php _e('Send Debug Emails', 'memberpress'); ?></label></th>
      <td><input type="checkbox" name="<?php echo $debug_str; ?>"<?php echo checked($debug); ?> /></td>
    </tr>
    <tr valign="top" <?php echo $classes; ?>>
      <th scope="row"><label><?php _e('Stripe Webhook URL:', 'memberpress'); ?></label></th>
      <td>
        <?php MeprAppHelper::clipboard_input($whk_url); ?>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $stripe_checkout_enabled_str; ?>"><?php _e('Enable Stripe Checkout', 'memberpress'); ?></label>
        <?php MeprAppHelper::info_tooltip('mepr-stripe-enable-stripe-checkout',
           __('Enable Stripe Checkout', 'memberpress'),
           __('Stripe Checkout is a prebuilt, hosted payment page optimized for conversion. Whether you offer one-time purchases or subscriptions, use Checkout to easily and securely accept payments online.', 'memberpress'));
        ?>
      </th>
      <td>
        <input type="checkbox" class="mepr-toggle-checkbox" data-box="mepr_stripe_checkout_<?php echo $id; ?>_box" name="<?php echo $stripe_checkout_enabled_str; ?>" <?php checked($stripe_checkout_enabled); ?> />
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo $churn_buster_enabled_str; ?>"><?php _e('Enable Churn Buster', 'memberpress'); ?></label>
        <?php MeprAppHelper::info_tooltip('mepr-stripe-enable-churn-buster',
           __('Enable Churn Buster', 'memberpress'),
           __('Churn Buster is a 3rd party service that allows you to automatically respond to failed payments in Stripe with email campaigns, card update pages, and real-time insights to reduce churn by up to 50%.', 'memberpress'));
        ?>
      </th>
      <td>
        <input type="checkbox" class="mepr-toggle-checkbox" data-box="mepr_stripe_churn_buster_<?php echo $id; ?>_box" name="<?php echo $churn_buster_enabled_str; ?>" <?php checked($churn_buster_enabled); ?> />
      </td>
    </tr>
  </tbody>
</table>
<div id="mepr_stripe_churn_buster_<?php echo $id; ?>_box" class="mepr-sub-box mepr_stripe_churn_buster_<?php echo $id; ?>_box">
  <div class="mepr-arrow mepr-gray mepr-up mepr-sub-box-arrow"> </div>
  <h3 class="mepr-page-heading"><?php _e('Recover failed payments with Churn Buster', 'memberpress'); ?></h3>
  <table class="form-table">
    <tbody>
      <?php /** We'll show this legacy UI if there's already a UUID in place for backwards compatibility **/ ?>
      <?php if(!empty($churn_buster_uuid)): ?>
        <tr valign="top">
          <th scope="row">
            <label for="<?php echo $churn_buster_uuid_str; ?>"><?php _e('Churn Buster Account ID', 'memberpress'); ?></label>
            <?php MeprAppHelper::info_tooltip('mepr-stripe-churn-buster-uuid',
              __('Churn Buster Account ID', 'memberpress'),
              __('This is the account id that is linked to your Churn Buster account. If you don\'t have a Churn Buster account yet, click the \'Create a Churn Buster Account\' link below.', 'memberpress')); ?>
          </th>
          <td>
            <input type="text" name="<?php echo $churn_buster_uuid_str; ?>" class="regular-text" value="<?php echo $churn_buster_uuid; ?>" />
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label><?php _e('Update Billing URL:', 'memberpress'); ?></label>
            <?php MeprAppHelper::info_tooltip('mepr-stripe-churn-buster-update-card-url',
              __('Update Billing URL', 'memberpress'),
              __('You\'ll give this URL to Churn Buster to use in dunning emails so that your customers can update their billing information.', 'memberpress')); ?>
          </th>
          <td>
            <?php MeprAppHelper::clipboard_input($update_billing_url); ?>
          </td>
        </tr>
      <?php endif; ?>
      <tr valign="top">
        <th scope="row" colspan="2">
           <div><a href="https://memberpress.com/cb"><?php _e('Use this link to start with a free trial month >>', 'memberpress'); ?></a></div>
        </th>
      </tr>
    </tbody>
  </table>
</div>
