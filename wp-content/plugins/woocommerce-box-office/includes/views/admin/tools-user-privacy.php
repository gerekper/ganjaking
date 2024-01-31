<?php
/**
 * Template file for user privacy preference updating tool.
 *
 * @package woocommerce-box-office
 */

$is_scheduled = get_option( 'wc-box-office-update-user-privacy-preference', false ); ?>

<form action="" method="POST">
	<input type="hidden" name="post_type" value="event_ticket">
	<input type="hidden" name="page" value="ticket_tools">
	<input type="hidden" name="tab" value="user-privacy">

	<?php if ( $is_scheduled ) : ?>
		<div class="notice notice-info">
			<p>
				<?php
					printf(
						/* translators: %s - Plugin name */
						__( '<strong>%s</strong>: User privacy preference update is in progress...', 'woocommerce-box-office' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						esc_html__( 'WooCommerce Box Office', 'woocommerce-box-office' )
					);
				?>
			</p>
		</div>
	<?php endif; ?>

	<p>
		<strong><?php esc_html_e( 'User privacy preference' ); ?></strong><br />
		<span class="description">
			<?php esc_html_e( 'Select an option to bulk-update user privacy preference for all tickets.', 'woocommerce-box-office' ); ?>
		</span>
	</p>
	<p>
		<label>
			<input type="radio" name="user-privacy-preference" value="opted-out">
			<?php esc_html_e( 'Exclude all ticket holders from being displayed in the public list of attendees.', 'woocommerce-box-office' ); ?>
		</label>
		<br />
		<label>
			<input type="radio" name="user-privacy-preference" value="opted-in">
			<?php esc_html_e( 'Include all ticket holders in the public list of attendees.', 'woocommerce-box-office' ); ?>
		</label>
	</p>

	<p class="buttons">
		<input <?php echo $is_scheduled ? 'disabled' : ''; ?> type="submit" value="<?php esc_attr_e( 'Update', 'woocommerce-box-office' ); ?>" class="button-primary">
	</p>
</form>
