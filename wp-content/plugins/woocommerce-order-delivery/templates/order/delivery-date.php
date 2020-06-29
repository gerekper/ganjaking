<?php
/**
 * Order delivery details
 *
 * @package WC_OD/Templates
 * @since   1.5.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Global variables.
 *
 * @global string $shipping_date
 * @global string $delivery_date
 * @global array  $delivery_time_frame
 * @global array  $delivery_range
 */
?>
<div id="wc-od">
	<header>
		<h2><?php echo esc_html( $title ); ?></h2>
	</header>

	<?php do_action( 'wc_od_order_before_delivery_details', $args ); ?>

	<?php if ( isset( $delivery_date ) ) : ?>

		<p>
			<?php
			/* translators: %s: delivery date */
			printf( wp_kses_post( __( 'We will try our best to deliver your order on %s.', 'woocommerce-order-delivery' ) ), "<strong>{$delivery_date}</strong>" ); // WPCS: XSS ok.
			?>
		</p>

		<?php if ( ! empty( $delivery_time_frame ) ) : ?>
			<p>
				<?php
				/* translators: %s: delivery time frame */
				printf( wp_kses_post( __( 'Time frame: %s', 'woocommerce-order-delivery' ) ), '<strong>' . wc_od_time_frame_to_string( $delivery_time_frame ) . '</strong>' ); // WPCS: XSS ok.
				?>
			</p>
		<?php endif; ?>

	<?php elseif ( isset( $shipping_date ) ) : ?>

		<p>
			<?php
			/* translators: %s: shipping date */
			printf( wp_kses_post( __( 'We estimate that your order will be shipped on %s.', 'woocommerce-order-delivery' ) ), "<strong>{$shipping_date}</strong>" ); // WPCS: XSS ok.
			?>
		</p>

		<p>
			<?php
			printf(
				wp_kses_post(
					/* translators: %s: minimum delivery days */
					_n(
						'The delivery will take approximately %s working day from the shipping date.',
						'The delivery will take approximately %s working days from the shipping date.',
						( $delivery_range['min'] === $delivery_range['max'] && 1 === $delivery_range['min'] ? 1 : $delivery_range['max'] ),
						'woocommerce-order-delivery'
					)
				),
				'<strong>' . wc_od_format_delivery_range( $delivery_range ) . '</strong>'
			); // WPCS: XSS ok.
			?>
		</p>

	<?php endif; ?>

	<?php do_action( 'wc_od_order_after_delivery_details', $args ); ?>
</div>
