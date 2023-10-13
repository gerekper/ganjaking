<?php
/**
 * Admin View: Settings
 *
 * @author YITH
 * @package YITH\GiftCards\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="yith-plugin-fw yit-admin-panel-container" id="yith-wcwl-emails-wrapper">
	<div id="yith-wcwl-table-emails">
		<div class="content-table">
			<?php foreach ( $emails_table as $email_key => $email ) : ?>
				<?php $url = YITH_WCWL_Admin()->build_single_email_settings_url( $email_key ); ?>
				<div class="yith-wcwl-row toggle-row-settings">
					<span class="yith-wcwl-column email">
						<?php echo esc_html( $email['title'] ); ?>
						<?php echo wc_help_tip( $email['description'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
					<span class="yith-wcwl-column status">
						<?php
							$email_status = array(
								'id'      => 'yith-wcwl-email-status',
								'type'    => 'onoff',
								'default' => 'yes',
								'value'   => $email['enable'],
								'data'    => array(
									'email_key' => $email_key,
								),
							);

							if ( 'estimate_mail' !== $email_key && 'yith_wcwl_promotion_mail' !== $email_key ) {
								yith_plugin_fw_get_field( $email_status, true );
							}
							?>
					</span>

					<span class="yith-wcwl-column action">
						<?php
						yith_plugin_fw_get_component(
							array(
								'title'  => __( 'Edit', 'yith-woocommerce-gift-cards' ),
								'type'   => 'action-button',
								'action' => 'edit',
								'icon'   => 'edit',
								'url'    => esc_url( $url ),
								'data'   => array(
									'target' => $email_key,
								),
								'class'  => 'toggle-settings',
							)
						);
						?>
					</span>
					<div class="email-settings" id="<?php echo esc_attr( $email_key ); ?>">
						<?php do_action( 'yith_wcwl_print_email_settings', $email_key ); ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
