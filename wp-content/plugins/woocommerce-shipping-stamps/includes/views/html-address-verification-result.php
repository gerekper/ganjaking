<?php
/**
 * View template to display a verfication result.
 *
 * @package WC_Stamps_Integration/View
 */

?>

<?php if ( ! empty( $result['matched'] ) ) : ?>

	<p><?php esc_html_e( 'Stamps.com matched the following address:', 'woocommerce-shipping-stamps' ); ?></p>
	<address>
		<?php
		// Escaping in array_map below.
		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo implode( '<br/>', array_filter( array_map( 'esc_html', $result['address'] ) ) );
		?>
	</address>
	<p>
		<button type="submit" class="button button-primary stamps-action" data-stamps_action="accept_address"><?php esc_html_e( 'Accept', 'woocommerce-shipping-stamps' ); ?></button>
		<button type="submit" class="button stamps-action" data-stamps_action="override_address"><?php esc_html_e( 'Continue without changes', 'woocommerce-shipping-stamps' ); ?></button>
	</p>

<?php elseif ( ! empty( $result['matched_zip'] ) ) : ?>

		<p><?php esc_html_e( 'Stamps.com could not find an exact match for the shipping address.', 'woocommerce-shipping-stamps' ); ?></p>
		<p><button type="submit" class="button stamps-action" data-stamps_action="override_address"><?php esc_html_e( 'Continue anyway', 'woocommerce-shipping-stamps' ); ?></button>

<?php else : ?>

		<p><?php esc_html_e( 'Invalid shipping address - a label cannot be generated. Please correct the shipping address manually.', 'woocommerce-shipping-stamps' ); ?></p>

<?php endif; ?>
