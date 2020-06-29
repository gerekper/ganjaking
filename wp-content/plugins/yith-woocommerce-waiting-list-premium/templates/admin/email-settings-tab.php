<?php
/**
 * Admin View: Settings
 */

if ( ! defined( 'YITH_WCWTL' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="wrap woocommerce">
	<table class="wc_emails widefat" cellspacing="0">
		<thead>
		<tr>
			<th class="wc-email-settings-table-status"></th>
			<th class="wc-email-settings-table-name"><?php esc_html_e( 'Email', 'yith-woocommerce-waiting-list' ); ?></th>
			<th class="wc-email-settings-table-recipient"><?php esc_html_e( 'Recipient(s)', 'yith-woocommerce-waiting-list' ); ?></th>
			<th class="wc-email-settings-table-action"></th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach ( $emails_table as $email_key => $email ) :
			$url = YITH_WCWTL_Admin_Premium()->build_single_email_settings_url( $email_key );
			?>
			<tr>
				<td class="wc-email-settings-table-status">
					<?php if ( $email['enable'] ) : ?>
						<span class="status-enabled tips"
							data-tip="<?php esc_attr_e( 'Enabled', 'yith-woocommerce-waiting-list' ); ?>">
                            <?php esc_html_e( 'Yes', 'yith-woocommerce-waiting-list' ); ?>
                        </span>
					<?php else : ?>
						<span class="status-disabled tips"
							data-tip="<?php esc_attr_e( 'Disabled', 'yith-woocommerce-waiting-list' ); ?>">-</span>
					<?php endif; ?>
				</td>
				<td class="wc-email-settings-table-name">
					<a href="<?php echo esc_url( $url ) ?>"><?php echo esc_html( $email['title'] ); ?></a>
					<?php echo wc_help_tip( $email['description'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</td>
				<td class="wc-email-settings-table-recipient">
					<?php esc_html_e( $email['recipient'] ); ?>
				</td>
				<td class="wc-email-settings-table-action">
					<a class="button alignright" href="<?php echo esc_url( $url ); ?>">
						<?php esc_html_e( 'Manage', 'yith-woocommerce-waiting-list' ); ?>
					</a>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>