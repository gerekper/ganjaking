<?php

$classes = '';

if ( ! isset( $_GET['display-keys'] ) && ! isset( $_COOKIE['mepr_stripe_display_keys'] ) && ! defined( 'MEPR_DISABLE_STRIPE_CONNECT' ) ) {
  $classes = 'class="mepr-hidden"';
}

?>

<div <?php echo $classes; ?>>
  <table id="mepr-stripe-test-keys-<?php echo $id; ?>" class="form-table mepr-stripe-test-keys mepr-hidden">
    <tbody>
      <tr valign="top">
        <th scope="row"><label for="<?php echo $test_public_key_str; ?>"><?php _e('Test Publishable Key*:', 'memberpress'); ?></label></th>
        <td><input type="text" class="mepr-auto-trim" name="<?php echo $test_public_key_str; ?>" value="<?php echo $test_public_key; ?>" /></td>
      </tr>
      <tr valign="top">
        <th scope="row"><label for="<?php echo $test_secret_key_str; ?>"><?php _e('Test Secret Key*:', 'memberpress'); ?></label></th>
        <td><input type="text" class="mepr-auto-trim" name="<?php echo $test_secret_key_str; ?>" value="<?php echo $test_secret_key; ?>" /></td>
      </tr>
    </tbody>
  </table>
  <table id="mepr-stripe-live-keys-<?php echo $id; ?>" class="form-table mepr-stripe-live-keys mepr-hidden">
    <tbody>
      <tr valign="top">
        <th scope="row"><label for="<?php echo $live_public_key_str; ?>"><?php _e('Live Publishable Key*:', 'memberpress'); ?></label></th>
        <td><input type="text" class="mepr-auto-trim" name="<?php echo $live_public_key_str; ?>" value="<?php echo $live_public_key; ?>" /></td>
      </tr>
      <tr valign="top">
        <th scope="row"><label for="<?php echo $live_secret_key_str; ?>"><?php _e('Live Secret Key*:', 'memberpress'); ?></label></th>
        <td><input type="text" class="mepr-auto-trim" name="<?php echo $live_secret_key_str; ?>" value="<?php echo $live_secret_key; ?>" /></td>
      </tr>
    </tbody>
  </table>
  <input class="mepr-stripe-connect-status" type="hidden" name="<?php echo $connect_status_string; ?>" value="<?php esc_attr_e( $connect_status, 'memberpress' ); ?>" />
  <input class="mepr-stripe-service-account-id" type="hidden" name="<?php echo $service_account_id_string; ?>" value="<?php esc_attr_e( $service_account_id, 'memberpress' ); ?>" />
  <input class="mepr-stripe-service-account-name" type="hidden" name="<?php echo $service_account_name_string; ?>" value="<?php esc_attr_e( $service_account_name, 'memberpress' ); ?>" />
</div>
