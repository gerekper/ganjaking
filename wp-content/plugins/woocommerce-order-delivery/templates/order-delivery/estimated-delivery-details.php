<?php
/**
 * Estimated delivery details.
 *
 * @package WC_OD/Templates
 * @version 2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var string $shipping_date  Shipping date.
 * @var array  $delivery_range Delivery range.
 */
?>
<p>
	<?php
	/* translators: %s: shipping date */
	printf( wp_kses_post( __( 'We estimate that your order will be shipped on %s.', 'woocommerce-order-delivery' ) ), "<strong>{$shipping_date}</strong>" ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
</p>

<p>
	<?php
	if ( 0 < $delivery_range['max'] ) :
		printf(
			wp_kses_post(
			/* translators: %s: number of days */
				_n(
					'The delivery will take approximately %s working day from the shipping date.',
					'The delivery will take approximately %s working days from the shipping date.',
					( $delivery_range['min'] === $delivery_range['max'] && 1 === $delivery_range['min'] ? 1 : $delivery_range['max'] ),
					'woocommerce-order-delivery'
				)
			),
			'<strong>' . wc_od_format_delivery_range( $delivery_range ) . '</strong>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	endif;
	?>
</p>
