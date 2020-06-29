<?php
/**
 * Dropdown and button for various actions for archives
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wcwl_actions">
	<select name="wcwl_action_<?php echo $product_id; ?>" class="wcwl_action">
		<option disabled selected value="0"><?php _e( 'Actions', 'woocommerce-waitlist' ); ?></option>
		<option value="wcwl_return_to_waitlist"><?php _e( 'Add to waitlist', 'woocommerce-waitlist' ); ?></option>
		<option value="wcwl_remove_archive"><?php _e( 'Permanently delete', 'woocommerce-waitlist' ); ?></option>
		<option value="wcwl_email_custom"><?php _e( 'Send custom email', 'woocommerce-waitlist' ); ?></option>
		<option value="wcwl_export"><?php _e( 'Export emails', 'woocommerce-waitlist' ); ?></option>
	</select>
	<button type="button" class="button wcwl_action" data-nonce="<?php echo wp_create_nonce( 'wcwl-action-nonce' ); ?>">
		<?php _e( 'Go', 'woocommerce-waitlist' ); ?>
	</button>
</div>