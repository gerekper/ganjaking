<?php
/**
 * Add payment method form form
 *
 * @version     3.4.0
 */

defined( 'ABSPATH' ) || exit;

$porto_woo_version = porto_get_woo_version_number();
?>

<?php if ( version_compare( $porto_woo_version, '2.6', '<' ) ) : ?>

<div class="featured-box align-left">
	<div class="box-content">
<?php endif; ?>

<?php

$available_gateways = WC()->payment_gateways->get_available_payment_gateways();

if ( $available_gateways ) :
	?>
	<form id="add_payment_method" method="post">
		<div id="payment" class="woocommerce-Payment">
			<ul class="woocommerce-PaymentMethods payment_methods methods">
				<?php
					// Chosen Method.
				if ( count( $available_gateways ) ) {
					current( $available_gateways )->set_current();
				}

				foreach ( $available_gateways as $gateway ) {
					?>
						<li class="woocommerce-PaymentMethod woocommerce-PaymentMethod--<?php echo esc_attr( $gateway->id ); ?> payment_method_<?php echo esc_attr( $gateway->id ); ?>">
							<input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> />
							<label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>"><?php echo wp_kses_post( $gateway->get_title() ); ?> <?php echo wp_kses_post( $gateway->get_icon() ); ?></label>
						<?php
						if ( $gateway->has_fields() || $gateway->get_description() ) {
							echo '<div class="woocommerce-PaymentBox woocommerce-PaymentBox--' . $gateway->id . ' payment_box payment_method_' . $gateway->id . '" style="display: none;">';
							$gateway->payment_fields();
							echo '</div>';
						}
						?>
						</li>
					<?php
				}
				?>
			</ul>

			<div class="form-row clearfix">
				<?php wp_nonce_field( 'woocommerce-add-payment-method', 'woocommerce-add-payment-method-nonce' ); ?>
				<button type="submit" class="woocommerce-Button woocommerce-Button--alt button btn-lg pt-right alt" id="place_order" value="<?php esc_attr_e( 'Add Payment Method', 'woocommerce' ); ?>"><?php esc_html_e( 'Add Payment Method', 'woocommerce' ); ?></button>
				<input type="hidden" name="woocommerce_add_payment_method" id="woocommerce_add_payment_method" value="1" />
			</div>
		</div>
	</form>
<?php else : ?>
	<p><?php esc_html_e( 'Sorry, it seems that there are no payment methods which support adding a new payment method. Please contact us if you require assistance or wish to make alternate arrangements.', 'porto' ); ?></p>
<?php endif; ?>

<?php if ( version_compare( $porto_woo_version, '2.6', '<' ) ) : ?>
	</div>
</div>
<?php endif; ?>
