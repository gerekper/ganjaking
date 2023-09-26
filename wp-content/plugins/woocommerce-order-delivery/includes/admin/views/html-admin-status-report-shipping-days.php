<?php
/**
 * Admin View: Shipping Days Status Report.
 *
 * @package WC_OD/Admin/Views
 * @since   2.0.0
 */

/**
 * Template vars.
 *
 * @var array $data The template data.
 */
?>
<table class="wc_status_table widefat" cellspacing="0">
	<thead>
		<tr>
			<th colspan="4" data-export-label="Shipping days">
				<h2><?php esc_html_e( 'Shipping days', 'woocommerce-order-delivery' ); ?><?php echo wc_help_tip( esc_html__( 'This section shows the shipping days defined with WooCommerce Order Delivery.', 'woocommerce-order-delivery' ) ); ?></h2>
			</th>
		</tr>
		<tr>
			<td><strong><?php echo esc_html__( 'Day', 'woocommerce-order-delivery' ); ?></strong></td>
			<td class="help"></td>
			<td><strong><?php echo esc_html__( 'Enabled', 'woocommerce' ); // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch ?></strong></td>
			<td><strong><?php echo esc_html__( 'Cut-off time', 'woocommerce-order-delivery' ); ?></strong></td>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ( $data['shipping_days'] as $index => $shipping_day ) :
			$extra_info = array(
				'Cut-off time: ' . ( ! empty( $shipping_day['time'] ) ? esc_html( $shipping_day['time'] ) : '-' ),
			);

			echo '<tr>';
			echo '<td>' . esc_html( $data['days'][ $index ] ) . '</td>';
			echo '<td></td>';
			echo '<td>';
			WC_OD_Admin_System_Status::output_bool_html( wc_string_to_bool( $shipping_day['enabled'] ) );
			echo '<span style="display: none;">, ' . esc_html( join( ', ', $extra_info ) ) . '</span></td>';
			echo '<td>' . ( ! empty( $shipping_day['time'] ) ? esc_html( $shipping_day['time'] ) : '-' ) . '</td>';
			echo '</tr>';
		endforeach;
		?>
	</tbody>
</table>
<?php
