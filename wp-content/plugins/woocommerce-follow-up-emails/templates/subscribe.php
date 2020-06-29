<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div id="fue_subscribe_container">
	<?php if ( isset( $_GET['fue_subscribed'] ) ): // phpcs:ignore WordPress.Security.NonceVerification ?>
	<div class="fue-success">
		<p><?php echo esc_html( $success_message ); ?></p>
	</div>
	<?php else: ?>
	<form id="fue_subscribe_form" method="post">
		<?php if ( !empty( $_REQUEST['error'] ) ): // phpcs:ignore WordPress.Security.NonceVerification ?>
			<div class="fue-error">
				<p><?php echo wp_kses_post( wp_unslash( $_REQUEST['error'] ) ); // phpcs:ignore WordPress.Security.NonceVerification ?></p>
			</div>
		<?php endif; ?>

		<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
		<label for="fue_first_name"><?php echo esc_html( $label_first_name ); ?></label>
		<input type="text" name="fue_first_name" id="fue_first_name" value="" />
		</p>

		<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-last">
		<label for="fue_last_name"><?php echo esc_html( $label_last_name ); ?></label>
		<input type="text" name="fue_last_name" id="fue_last_name" value="" />
		</p>

		<div class="clear"></div>

		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="fue_subscriber_email"><?php echo esc_html( $label_email ); ?></label>
		<input type="email" name="fue_subscriber_email" id="fue_subscriber_email" placeholder="<?php echo esc_attr( $placeholder_email ); ?>" value="" />
		</p>

		<input type="hidden" name="fue_action" value="subscribe" />
		<input type="hidden" name="fue_email_list" value="<?php echo esc_attr( $list ); ?>" />
		<?php wp_nonce_field( 'fue_subscribe' ); ?>
		<input type="submit" id="fue_subscriber_submit" class="button button-submit" value="<?php echo esc_attr( $submit_text ); ?>" />
	</form>
	<?php endif; ?>
</div>
