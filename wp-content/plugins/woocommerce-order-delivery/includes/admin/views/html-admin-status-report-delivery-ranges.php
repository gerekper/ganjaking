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
			<th colspan="4" data-export-label="Delivery Ranges">
				<h2><?php esc_html_e( 'Delivery Ranges', 'woocommerce-order-delivery' ); ?><?php echo wc_help_tip( esc_html__( 'This section shows the delivery ranges defined with WooCommerce Order Delivery.', 'woocommerce-order-delivery' ) ); ?></h2>
			</th>
		</tr>
		<tr>
			<td><strong><?php echo esc_html_x( 'Title', 'delivery ranges: table column', 'woocommerce-order-delivery' ); ?></strong></td>
			<td class="help"></td>
			<td><strong><?php echo esc_html_x( 'Range', 'delivery ranges: table column', 'woocommerce-order-delivery' ); ?></strong></td>
			<td><strong><?php echo esc_html_x( 'Shipping methods', 'delivery ranges: table column', 'woocommerce-order-delivery' ); ?></strong></td>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ( $data['delivery_ranges'] as $delivery_range ) :
			$extra_info = array(
				'Shipping methods: ' . ( $delivery_range->get_id() > 0 ? esc_html( $delivery_range->get_shipping_methods_option() ) : '-' ),
			);

			echo '<tr>';
			echo '<td>' . esc_html( $delivery_range->get_title() ) . '</td>';
			echo '<td></td>';
			echo '<td>' . esc_html( $delivery_range->get_from() ) . ' - ' . esc_html( $delivery_range->get_to() ) . '<span style="display: none;">, ' . esc_html( join( ', ', $extra_info ) ) . '</span></td>';
			echo '<td>' . ( $delivery_range->get_id() > 0 ? esc_html( $delivery_range->get_shipping_methods_option() ) : '-' ) . '</td>';
			echo '<span style="display: none;">, ' . esc_html( join( ', ', $extra_info ) ) . '</span></td>';
			echo '</tr>';
		endforeach;
		?>
	</tbody>
</table>
<?php
