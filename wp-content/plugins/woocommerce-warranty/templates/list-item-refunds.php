<?php
/**
 * The template for displaying warranty options.
 *
 * @package WooCommerce_Warranty\Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<?php if ( 'refund' === $request['request_type'] ) : ?>
	<div id="warranty-refund-modal-<?php echo esc_attr( $request['ID'] ); ?>" style="display:none;">
		<table class="form-table">
			<tr>
				<th><span class="label"><?php esc_html_e( 'Amount refunded:', 'wc_warranty' ); ?></span></th>
				<td><span class="value"><?php echo wc_price( $refunded ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span></td>
			</tr>
			<tr>
				<th><span class="label"><?php esc_html_e( 'Item cost:', 'wc_warranty' ); ?></span></th>
				<td><span class="value"><?php echo wc_price( $item_amount ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span></td>
			</tr>
			<tr>
				<th><span class="label"><?php esc_html_e( 'Refund amount:', 'wc_warranty' ); ?></span></th>
				<td>
					<?php echo esc_html( get_woocommerce_currency_symbol() ); ?>
					<input type="text" class="input-short amount" value="<?php echo esc_attr( $available ); ?>" size="5" />
				</td>
			</tr>
		</table>

		<p class="submit alignright">
			<input
				type="button"
				class="warranty-process-refund button-primary"
				value="<?php esc_attr_e( 'Process Refund', 'wc_warranty' ); ?>"
				data-id="<?php echo esc_attr( $request['ID'] ); ?>"
				data-security="<?php echo esc_attr( $update_nonce ); ?>"
				/>
		</p>
	</div>
	<?php elseif ( 'coupon' === $request['request_type'] ) : ?>
	<div id="warranty-coupon-modal-<?php echo esc_attr( $request['ID'] ); ?>" style="display:none;">
		<table class="form-table">
			<tr>
				<th><span class="label"><?php esc_html_e( 'Amount refunded:', 'wc_warranty' ); ?></span></th>
				<td><span class="value"><?php echo wc_price( $refunded ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span></td>
			</tr>
			<tr>
				<th><span class="label"><?php esc_html_e( 'Item cost:', 'wc_warranty' ); ?></span></th>
				<td><span class="value"><?php echo wc_price( $item_amount ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span></td>
			</tr>
			<tr>
				<th><span class="label"><?php esc_html_e( 'Coupon amount:', 'wc_warranty' ); ?></span></th>
				<td>
					<?php echo esc_html( get_woocommerce_currency_symbol() ); ?>
					<input type="text" class="input-short amount" value="<?php echo esc_attr( $available ); ?>" size="5" />
				</td>
			</tr>
		</table>

		<p class="submit alignright">
			<input
				type="button"
				class="warranty-process-coupon button-primary"
				value="<?php esc_html_e( 'Send Coupon', 'wc_warranty' ); ?>"
				data-id="<?php echo esc_attr( $request['ID'] ); ?>"
				data-security="<?php echo esc_attr( wp_create_nonce( 'warranty_send_coupon' ) ); ?>"
				/>
		</p>
	</div>
<?php endif; ?>
<?php
