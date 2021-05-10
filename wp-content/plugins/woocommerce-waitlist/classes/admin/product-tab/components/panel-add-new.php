<?php
/**
 * Elements to add a new user on the waitlist tab
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wcwl_add_user_wrap">
	<button type="button" class="button wcwl_add">
		<?php _e( 'Add new user', 'woocommerce-waitlist' ); ?>
	</button>
	<div class="wcwl_add_user_content">
		<input type="email" placeholder="<?php _e( 'Email address', 'woocommerce-waitlist' ); ?>" class="wcwl_email" name="wcwl_email_<?php echo $product_id; ?>"/>
		<button type="button" class="button wcwl_email_add_user" data-nonce="<?php echo wp_create_nonce( 'wcwl-add-user-nonce' ); ?>">
			<?php _e( 'Add', 'woocommerce-waitlist' ); ?>
		</button>
		<button type="button" class="button wcwl_back">
			X
		</button>
			<span class="wcwl_new_account_text">
				<?php _e( 'New users will be registered and emailed a "New Account" email', 'woocommerce-waitlist' ); ?>
			</span>
	</div>
</div>