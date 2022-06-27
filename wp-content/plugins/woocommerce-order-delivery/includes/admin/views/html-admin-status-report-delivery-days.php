<?php
/**
 * Admin View: Delivery Days Status Report.
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
			<th colspan="6" data-export-label="Delivery Days">
				<h2><?php esc_html_e( 'Delivery Days', 'woocommerce-order-delivery' ); ?><?php echo wc_help_tip( esc_html__( 'This section shows the delivery days defined with WooCommerce Order Delivery.', 'woocommerce-order-delivery' ) ); ?></h2>
			</th>
		</tr>
		<tr>
			<td><strong><?php echo esc_html_x( 'Day', 'delivery days: table column', 'woocommerce-order-delivery' ); ?></strong></td>
			<td class="help"></td>
			<td><strong><?php echo esc_html_x( 'Enabled', 'delivery days: table column', 'woocommerce-order-delivery' ); ?></strong></td>
			<td><strong><?php echo esc_html_x( 'Nº of orders', 'delivery days: table column', 'woocommerce-order-delivery' ); ?></strong></td>
			<td><strong><?php echo esc_html_x( 'Shipping methods', 'delivery days: table column', 'woocommerce-order-delivery' ); ?></strong></td>
			<td><strong><?php echo esc_html_x( 'Time frames', 'delivery days: table column', 'woocommerce-order-delivery' ); ?></strong></td>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ( $data['settings']['delivery_days'] as $delivery_day ) :
			$fee_amount      = ( $delivery_day->get_fee_amount() ? $delivery_day->get_fee_amount() : '' );
			$has_time_frames = $delivery_day->has_time_frames();

			$extra_info = array(
				'Nº of orders: ' . $delivery_day->get_number_of_orders(),
				'Shipping methods: ' . ( $delivery_day->get_shipping_methods_option() ? $delivery_day->get_shipping_methods_option() : 'all' ),
				'Time frames: ' . wc_bool_to_string( $has_time_frames ),
			);

			if ( $has_time_frames ) {
				$time_frames = $delivery_day->get_time_frames();
				foreach ( $time_frames as $time_frame ) {
					$tf_fee       = $time_frame->get_fee_amount();
					$extra_info[] = 'Time frame title: ' . $time_frame->get_title();
					if ( $tf_fee ) {
						$extra_info[] = 'Fee tax amount: ' . $tf_fee;
						$extra_info[] = 'Fee tax status: ' . ( $time_frame->get_fee_tax_status() ? $time_frame->get_fee_tax_status() : 'none' );
					}
				}
			} else {
				if ( $fee_amount ) {
					$extra_info[] = 'Fee tax amount: ' . $fee_amount;
					$extra_info[] = 'Fee tax status: ' . ( $delivery_day->get_fee_tax_status() ? $delivery_day->get_fee_tax_status() : 'none' );
				}
			}

			echo '<tr>';
			echo '<td>' . esc_html( $data['days'][ $delivery_day->get_weekday() ] ) . '</td>';
			echo '<td></td>';
			echo '<td>';
			WC_OD_Admin_System_Status::output_bool_html( $delivery_day->is_enabled() );
			echo '<span style="display: none;">, ' . esc_html( join( ', ', $extra_info ) ) . '</span></td>';
			echo '<td>' . esc_html( $delivery_day->get_number_of_orders() ) . '</td>';
			echo '<td>' . ( $delivery_day->get_shipping_methods_option() ? esc_html( $delivery_day->get_shipping_methods_option() ) : 'all' ) . '</td>';
			echo '<td>';
			WC_OD_Admin_System_Status::output_bool_html( $delivery_day->has_time_frames() );
			echo '<span style="display: none;">' . esc_html( join( ', ', $extra_info ) ) . '</span></td>';
			echo '</tr>';
		endforeach;
		?>
	</tbody>
</table>
<?php
