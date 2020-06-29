<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<?php if($mepr_options->show_address_fields && $mepr_options->require_address_fields): ?>
  <input type="hidden" name="card-address-1" value="<?php echo (isset($user)) ? get_user_meta($user->ID, 'mepr-address-one', true) : ''; ?>" />
  <input type="hidden" name="card-address-2" value="<?php echo (isset($user)) ? get_user_meta($user->ID, 'mepr-address-two', true) : ''; ?>" />
  <input type="hidden" name="card-city" value="<?php echo (isset($user)) ? get_user_meta($user->ID, 'mepr-address-city', true) : ''; ?>" />
  <input type="hidden" name="card-state" value="<?php echo (isset($user)) ? get_user_meta($user->ID, 'mepr-address-state', true) : ''; ?>" />
  <input type="hidden" name="card-zip" value="<?php echo (isset($user)) ? get_user_meta($user->ID, 'mepr-address-zip', true) : ''; ?>" />
  <input type="hidden" name="card-country" value="<?php echo (isset($user)) ? get_user_meta($user->ID, 'mepr-address-country', true) : ''; ?>" />
<?php endif; ?>
