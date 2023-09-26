<?php
/**
 * Delivery details for PDF Invoices & Packing slips.
 *
 * @package WC_OD/Templates
 * @version 2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var string $date            The selected date by the customer with the ISO 8601 format.
 * @var array  $time_frame      An array with the time frame data.
 * @var bool   $is_local_pickup Whether the order shipping method is a local pickup.
 */

if ( $is_local_pickup ) :
	$label = __( 'Pickup date:', 'woocommerce-order-delivery' );
else :
	$label = __( 'Delivery date:', 'woocommerce-order-delivery' );
endif;
?>

<tr class="wc-od-pdf-details-date">
	<th><?php echo esc_html( $label ); ?></th>
	<td><?php echo esc_html( wc_od_localize_date( $date ) ); ?></td>
</tr>

<?php if ( $time_frame ) : ?>
	<tr class="wc-od-pdf-details-time-frame">
		<th><?php esc_html_e( 'Time frame:', 'woocommerce-order-delivery' ); ?></th>
		<td><?php echo esc_html( wc_od_time_frame_to_string( $time_frame ) ); ?></td>
	</tr>
	<?php
endif;
