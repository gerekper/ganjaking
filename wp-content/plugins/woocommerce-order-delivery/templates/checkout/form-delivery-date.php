<?php
/**
 * Checkout delivery date form
 *
 * @package WC_OD/Templates
 * @since   1.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Variables.
 *
 * @var WC_Checkout $checkout
 * @var string      $delivery_option
 * @var array       $delivery_range
 */
?>
<div id="wc-od">
	<h3><?php echo esc_html( $title ); ?></h3>

	<?php do_action( 'wc_od_checkout_before_delivery_details', $args ); ?>

	<?php if ( 'calendar' === $delivery_option ) : ?>

		<p><?php echo wp_kses_post( __( 'We will try our best to deliver your order on the specified date.', 'woocommerce-order-delivery' ) ); ?></p>

		<?php
		$fields = $checkout->get_checkout_fields( 'delivery' );

		foreach ( $fields as $key => $field ) :
			woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
		endforeach;
		?>

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
					/* translators: %s: number of days */
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

	<?php do_action( 'wc_od_checkout_after_delivery_details', $args ); ?>
</div>
