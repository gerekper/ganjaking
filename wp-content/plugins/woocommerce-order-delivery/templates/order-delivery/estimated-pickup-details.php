<?php
/**
 * Estimated pickup details.
 *
 * @package WC_OD/Templates
 * @version 2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var string $pickup_date  Pickup date.
 * @var array  $pickup_range Pickup range.
 */
?>
<p>
	<?php
	/* translators: %s: pickup date */
	printf( wp_kses_post( __( 'Your order will be ready to pick up on %s.', 'woocommerce-order-delivery' ) ), "<strong>$pickup_date</strong>" ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
</p>

<p>
	<?php
	if ( 0 < $pickup_range['max'] ) :
		printf(
			wp_kses_post(
			/* translators: %s: number of days */
				_n(
					'You have %s business day from this date to pick up your order.',
					'You have up to %s business days from this date to pick up your order.',
					( $pickup_range['max'] ),
					'woocommerce-order-delivery'
				)
			),
			'<strong>' . $pickup_range['max'] . '</strong>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	endif;
	?>
</p>
