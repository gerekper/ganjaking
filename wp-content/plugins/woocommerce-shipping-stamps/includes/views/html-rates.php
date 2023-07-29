<?php
/**
 * View template to display the rates in meta box from AJAX response.
 *
 * @package WC_Stamps_Integration/View
 */
?>

<p><?php esc_html_e( 'Choose a rate to generate a shipping label:', 'woocommerce-shipping-stamps' ); ?></p>

<table class="widefat wc-stamps-rates">
	<?php
	foreach ( $rates as $rate ) :
		$rate_object = wp_json_encode( $rate->rate_object );
		$rate_object = function_exists( 'wc_esc_json' ) ? wc_esc_json( $rate_object ) : _wp_specialchars( $rate_object, ENT_QUOTES, 'UTF-8', true );
		?>
		<tr>
			<td>
				<input type="radio" id="<?php echo esc_attr( sanitize_title( $rate->service . '-' . $rate->package ) ); ?>" name="stamps_rate" value="<?php echo esc_attr( $rate_object ); ?>" />
			</td>
			<th><label for="<?php echo esc_attr( sanitize_title( $rate->service . '-' . $rate->package ) ); ?>"><?php echo esc_html( $rate->name . ' (' . $rate->package . ')' ); ?></label></th>
			<td>
				<?php
				// Skipping escaping here since we are using wc_price() which escapes the value.
				// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
				echo wc_price( $rate->cost );
				?>
			</td>
		</tr>
		<tr class="addons" style="display:none;">
			<td></td>
			<td colspan="2">
				<?php
				if ( isset( $rate->rate_object->AddOns ) && isset( $rate->rate_object->AddOns->AddOnV7 ) ) {
					WC_Stamps_Order::addons_html( $rate );
				}
				?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>
<p>
	<?php if ( $this->needs_customs_step( $order ) ) : ?>
		<button type="submit" class="button button-primary stamps-action" data-stamps_action="customs"><?php esc_html_e( 'Enter customs information', 'woocommerce-shipping-stamps' ); ?></button>
	<?php else : ?>
		<button type="submit" class="button button-primary stamps-action" data-stamps_action="request_label"><?php esc_html_e( 'Request label', 'woocommerce-shipping-stamps' ); ?></button>
	<?php endif; ?>
	<button type="submit" class="button stamps-action" data-stamps_action="define_package"><?php esc_html_e( 'Back', 'woocommerce-shipping-stamps' ); ?></button>
</p>
