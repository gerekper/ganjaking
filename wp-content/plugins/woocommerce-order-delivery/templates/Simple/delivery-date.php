<?php
/**
 * Delivery details for PDF Invoices & Packing slips.
 *
 * @package WC_OD/Templates
 * @version 1.9.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Global variables.
 *
 * @global string $document_type
 * @global string $delivery_date
 * @global array  $delivery_time_frame
 */
?>

<tr class="delivery-date">
	<th><?php esc_html_e( 'Delivery Date:', 'woocommerce-order-delivery' ); ?></th>
	<td><?php echo esc_html( wc_od_localize_date( $delivery_date ) ); ?></td>
</tr>

<?php if ( $delivery_time_frame ) : ?>
	<tr class="delivery-time-frame">
		<th><?php esc_html_e( 'Time Frame:', 'woocommerce-order-delivery' ); ?></th>
		<td><?php echo esc_html( wc_od_time_frame_to_string( $delivery_time_frame ) ); ?></td>
	</tr>
	<?php
endif;
