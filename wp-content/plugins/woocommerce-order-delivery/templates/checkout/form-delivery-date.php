<?php
/**
 * Checkout delivery date form.
 *
 * @package WC_OD/Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var WC_Checkout $checkout
 * @var string      $title
 * @var string      $delivery_option
 * @var string      $checkout_text
 * @var array       $delivery_range
 */
?>
<div id="wc-od">
	<h3><?php echo esc_html( $title ); ?></h3>

	<?php do_action( 'wc_od_checkout_before_delivery_details', $args ); ?>

	<?php if ( 'calendar' === $delivery_option ) : ?>

		<?php
		if ( ! empty( $checkout_text ) ) :
			echo '<p class="wc-od-checkout-text">' . wp_kses_post( $checkout_text ) . '</p>';
		endif;

		$fields = $checkout->get_checkout_fields( 'delivery' );

		foreach ( $fields as $key => $field ) :
			woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
		endforeach;
		?>

	<?php elseif ( isset( $shipping_date ) ) : ?>

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

	<?php endif; ?>

	<?php do_action( 'wc_od_checkout_after_delivery_details', $args ); ?>
</div>
