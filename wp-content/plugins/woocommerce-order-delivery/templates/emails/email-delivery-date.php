<?php
/**
 * Email delivery details
 *
 * @package WC_OD/Templates/Emails
 * @version 1.5.5
 */

defined( 'ABSPATH' ) || exit;

/**
 * Global variables.
 *
 * @global string $delivery_date
 * @global array $delivery_time_frame
 */
?>
<h2 style="margin-top:40px;"><?php echo esc_html( $title ); ?></h2>

<div style="margin-bottom:40px;">
	<?php do_action( 'wc_od_email_before_delivery_details', $args ); ?>

	<p>
	<?php
		/* translators: %s: delivery date */
		printf( wp_kses_post( __( 'We will try our best to deliver your order on %s.', 'woocommerce-order-delivery' ) ), "<strong>{$delivery_date}</strong>" ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
	</p>

	<?php if ( ! empty( $delivery_time_frame ) ) : ?>
		<p>
		<?php
			/* translators: %s: delivery time frame */
			printf( wp_kses_post( __( 'Time frame: %s', 'woocommerce-order-delivery' ) ), '<strong>' . wc_od_time_frame_to_string( $delivery_time_frame ) . '</strong>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
		</p>
	<?php endif; ?>

	<?php do_action( 'wc_od_email_after_delivery_details', $args ); ?>
</div>
