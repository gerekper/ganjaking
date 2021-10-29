<?php
/**
 * Admin View: Setting Status Report.
 *
 * @package WC_Newsletter_Subscription/Admin/Views
 * @since   3.3.3
 */

/**
 * Template vars.
 *
 * @var WC_Newsletter_Subscription_Provider|null $provider The provider instance.
 * @var array                                    $data     The template data.
 */
?>
<table class="wc_status_table widefat" cellspacing="0">
	<thead>
		<tr>
			<th colspan="5" data-export-label="Newsletter Subscription">
				<h2><?php esc_html_e( 'Newsletter Subscription', 'woocommerce-subscribe-to-newsletter' ); ?><?php echo wc_help_tip( esc_html__( 'This section shows information about Newsletter Subscription.', 'woocommerce-subscribe-to-newsletter' ) ); ?></h2>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td data-export-label="Provider"><?php esc_html_e( 'Provider', 'woocommerce-subscribe-to-newsletter' ); ?>:</td>
			<td class="help"></td>
			<td><?php echo esc_html( $provider ? $provider->get_name() : '-' ); ?></td>
		</tr>
		<?php if ( $provider && $provider->is_enabled() ) : ?>
			<tr>
				<td data-export-label="Provider List"><?php echo esc_html_x( 'Provider List', 'setting title', 'woocommerce-subscribe-to-newsletter' ); ?>:</td>
				<td class="help"></td>
				<td><?php WC_Newsletter_Subscription_Admin_System_Status::output_bool_html( ! empty( $data['provider_list'] ) ); ?></td>
			</tr>
			<tr>
				<td data-export-label="Subscribe checkbox label"><?php echo esc_html_x( 'Subscribe checkbox label', 'setting-title', 'woocommerce-subscribe-to-newsletter' ); ?>:</td>
				<td class="help"></td>
				<td><?php echo esc_html( ! empty( $data['subscribe_checkbox_label'] ) ? $data['subscribe_checkbox_label'] : '-' ); ?></td>
			</tr>
			<tr>
				<td data-export-label="Default checkbox status"><?php echo esc_html_x( 'Default checkbox status', 'setting title', 'woocommerce-subscribe-to-newsletter' ); ?>:</td>
				<td class="help"></td>
				<td><?php echo esc_html( $data['default_checkbox_status'] ); ?></td>
			</tr>
			<tr>
				<td data-export-label="Checkout location"><?php echo esc_html_x( 'Checkout location', 'setting title', 'woocommerce-subscribe-to-newsletter' ); ?>:</td>
				<td class="help"></td>
				<td><?php echo esc_html( $data['checkout_location'] ); ?></td>
			</tr>
			<tr>
				<td data-export-label="Subscribe on order statuses"><?php echo esc_html_x( 'Subscribe on order statuses', 'setting title', 'woocommerce-subscribe-to-newsletter' ); ?>:</td>
				<td class="help"></td>
				<td><?php echo esc_html( ! empty( $data['subscribe_on_order_statuses'] ) ? join( ' | ', $data['subscribe_on_order_statuses'] ) : '-' ); ?></td>
			</tr>
			<?php if ( isset( $data['mailchimp_double_opt_in'] ) ) : ?>
				<tr>
					<td data-export-label="Enabled Double Opt-in"><?php esc_html_e( 'Enabled Double Opt-in', 'woocommerce-subscribe-to-newsletter' ); ?>:</td>
					<td class="help"></td>
					<td>
						<?php WC_Newsletter_Subscription_Admin_System_Status::output_bool_html( $data['mailchimp_double_opt_in'] ); ?>
					</td>
				</tr>
			<?php endif; ?>
		<?php endif; ?>
	</tbody>
</table>
<?php
